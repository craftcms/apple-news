<?php

declare(strict_types=1);

namespace CraftCms\AppleNews;

use CraftCms\AppleNews\Enums\ArticleState;
use CraftCms\AppleNews\Jobs\PublishArticleJob;
use CraftCms\AppleNews\Models\Article as ArticleRecord;
use CraftCms\Cms\Cms;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Support\Arr;
use CraftCms\Cms\Support\Json;
use Illuminate\Container\Attributes\Singleton;
use RuntimeException;
use stdClass;

/** @since 2.0 */
#[Singleton]
class Articles
{
    public function __construct(
        private readonly Channels $channels,
        private readonly Api $api,
    ) {}

    /**
     * Returns the generator metadata that is included in each article’s content.
     *
     * @return array<string, string>
     */
    public function generator(): array
    {
        $plugin = app(Plugin::class);

        return [
            'generatorIdentifier' => 'CraftCMS',
            'generatorName' => 'Craft CMS',
            'generatorVersion' => Cms::version()." ($plugin->name $plugin->version)",
        ];
    }

    /**
     * Returns all known info about an entry's articles on Apple News.
     *
     * @param  Entry  $entry  The entry
     * @param  string|string[]|null  $channelId  The channel ID(s) to limit the query to
     * @param  bool  $refresh  Whether the info should be refreshed for articles that are processing
     * @return array<string, array<string, mixed>> The info, indexed by channel ID
     */
    public function info(Entry $entry, array|string|null $channelId = null, bool $refresh = false): array
    {
        $records = ArticleRecord::query()->forEntry($entry->id)->inChannels($channelId)->get();
        $infos = [];

        foreach ($records as $record) {
            // Refresh first?
            if ($refresh && $record->state->isProcessing()) {
                $response = $this->api->article($record->channelId, $record->articleId);
                if (isset($response->data)) {
                    $this->updateArticleRecord($record, $response);
                }
            }

            $infos[$record->channelId] = [
                'articleId' => $record->articleId,
                'revisionId' => $record->revisionId,
                'isSponsored' => $record->isSponsored,
                'isPreview' => $record->isPreview,
                'state' => $record->state->value,
                'shareUrl' => $record->shareUrl,
                'isQueued' => $record->state->isQueued(),
                'isProcessing' => $record->state->isProcessing(),
                'isShareable' => $record->state->isShareable(),
            ];
        }

        return $infos;
    }

    /**
     * Returns whether any Apple News channels can publish the given entry.
     *
     * @param  Entry  $entry  The entry to check
     * @return bool Whether the entry can be published to any channels
     */
    public function canPublish(Entry $entry): bool
    {
        return $this->publishableChannels($entry) !== [];
    }

    /**
     * Queues an entry up to be published on Apple News.
     *
     * Channels that don’t exist, don’t match the entry, or can’t publish it are skipped.
     *
     * @param  Entry  $entry  The entry to be published
     * @param  string|string[]|null  $channelIds  The Apple News channel ID(s) that the entry should be published to
     */
    public function queue(Entry $entry, array|string|null $channelIds = null): void
    {
        $channelIds = array_keys($this->publishableChannels($entry, $channelIds));

        if (empty($channelIds)) {
            return;
        }

        $articles = ArticleRecord::query()->forEntry($entry->id)->inChannels($channelIds)->get()->keyBy('channelId');

        foreach ($channelIds as $channelId) {
            $article = $articles[$channelId] ?? new ArticleRecord([
                'entryId' => $entry->id,
                'channelId' => $channelId,
            ]);
            $article->state = $article->articleId ? ArticleState::QueuedUpdate : ArticleState::Queued;
            $article->save();

            dispatch(new PublishArticleJob($entry->id, $entry->siteId, $channelId));
        }
    }

    /**
     * Publishes an article to Apple News.
     *
     * If the entry can’t be published to a channel it was queued for anymore, the article goes back to the state it
     * had before it was queued. If publishing fails, the exception is thrown and the article stays queued, so
     * {@see PublishArticleJob} can retry it.
     *
     * @param  Entry  $entry  The entry to be published
     * @param  string|string[]|null  $channelIds  The Apple News channel ID(s) to publish the entry to
     */
    public function publish(Entry $entry, array|string|null $channelIds = null): void
    {
        $channels = $this->publishableChannels($entry, $channelIds);

        $articleRecords = ArticleRecord::query()->forEntry($entry->id)->inChannels($channelIds)->get()->keyBy('channelId');

        foreach ($articleRecords as $channelId => $articleRecord) {
            if (! isset($channels[$channelId]) && $articleRecord->state->isQueued()) {
                $this->unqueueRecord($articleRecord);
            }
        }

        foreach ($channels as $channelId => $channel) {
            $articleRecord = $articleRecords[$channelId] ?? null;

            $response = $this->sendArticle($entry, $channel, $articleRecord);

            // Save a record of the article
            if ($articleRecord === null) {
                $articleRecord = new ArticleRecord([
                    'entryId' => $entry->id,
                    'channelId' => $channelId,
                    'articleId' => $response->data->id,
                ]);
            } elseif (! $articleRecord->articleId) {
                $articleRecord->articleId = $response->data->id;
            }
            $this->updateArticleRecord($articleRecord, $response);
        }
    }

    /**
     * Deletes an article on Apple News.
     *
     * @param  string|string[]|null  $channelIds  The Apple News channel ID(s) to delete the entry from
     */
    public function delete(Entry $entry, array|string|null $channelIds = null): void
    {
        foreach (ArticleRecord::query()->forEntry($entry->id)->inChannels($channelIds)->get() as $record) {
            // Queued articles may not be on Apple News yet
            if ($record->articleId) {
                $this->api->deleteArticle($record->channelId, $record->articleId);
            }
            $record->delete();
        }
    }

    /**
     * Sends an entry’s article to Apple News, creating it or updating the existing one.
     *
     * @throws RuntimeException if Apple News doesn’t return the article
     */
    protected function sendArticle(Entry $entry, ChannelInterface $channel, ?ArticleRecord $articleRecord): stdClass
    {
        $channelId = $channel->getChannelId();

        $article = $channel->createArticle($entry);
        $content = $article->getContent();
        $metadata = $article->getMetadata() ?: [];

        // Include the generator metadata in the content
        $content['metadata'] = array_merge(
            $content['metadata'] ?? [],
            $this->generator(),
        );

        // Include the latest revision ID if we have one
        if ($articleRecord !== null) {
            $metadata['revision'] = $articleRecord->revisionId;
        }

        // Prepare the data and send the request
        $data = [
            'files' => $article->getFiles(),
            'metadata' => ! empty($metadata) ? Json::encode(['data' => $metadata]) : null,
            'json' => Json::encode($content),
        ];

        // Publish the article
        if ($articleRecord !== null && $articleRecord->articleId) {
            $response = $this->api->updateArticle($channelId, $articleRecord->articleId, $data);
        } else {
            $response = $this->api->createArticle($channelId, $data);
        }

        if (! isset($response->data)) {
            throw new RuntimeException("Apple News didn’t return the article for entry $entry->id in channel $channelId.");
        }

        return $response;
    }

    /**
     * Returns the channels that the entry can be published to, limited to the given channel IDs.
     *
     * @param  string|string[]|null  $channelIds
     * @return array<string, ChannelInterface> The channels, indexed by channel ID
     */
    protected function publishableChannels(Entry $entry, array|string|null $channelIds = null): array
    {
        $channelIds = $channelIds !== null ? Arr::wrap($channelIds) : null;

        return array_filter(
            $this->channels->all(),
            fn (ChannelInterface $channel) => (
                ($channelIds === null || in_array($channel->getChannelId(), $channelIds, true)) &&
                $channel->matchEntry($entry) &&
                $channel->canPublish($entry)
            ),
        );
    }

    /**
     * Updates a given Article record with the data in an Apple News API response.
     */
    protected function updateArticleRecord(ArticleRecord $record, stdClass $response): void
    {
        $record->revisionId = $response->data->revision;
        $record->isSponsored = $response->data->isSponsored;
        $record->isPreview = $response->data->isPreview;
        $record->state = ArticleState::from($response->data->state);
        $record->shareUrl = $response->data->shareUrl;
        $record->response = Json::encode($response);
        $record->save();
    }

    /**
     * Returns an entry’s queued article in a channel to the state Apple News last reported for it, or removes it if it
     * never reached Apple News. Articles that aren’t queued are left alone.
     */
    public function unqueue(int $entryId, string $channelId): void
    {
        $record = ArticleRecord::query()->forEntry($entryId)->inChannels($channelId)->first();

        if ($record?->state->isQueued()) {
            $this->unqueueRecord($record);
        }
    }

    protected function unqueueRecord(ArticleRecord $record): void
    {
        $previousState = ArticleState::tryFrom(Json::decode($record->response)['data']['state'] ?? '');

        if ($previousState === null) {
            $record->delete();

            return;
        }

        $record->state = $previousState;
        $record->save();
    }
}
