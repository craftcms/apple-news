<?php

declare(strict_types=1);

namespace CraftCms\AppleNews;

use CraftCms\AppleNews\Jobs\PublishArticleJob;
use CraftCms\AppleNews\Models\Article as ArticleRecord;
use CraftCms\Cms\Cms;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Support\Json;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use stdClass;

/** @since 2.0 */
#[Singleton]
class ArticleManager
{
    public const string STATE_PROCESSING = 'PROCESSING';

    public const string STATE_PROCESSING_UPDATE = 'PROCESSING_UPDATE';

    public const string STATE_QUEUED = 'QUEUED';

    public const string STATE_QUEUED_UPDATE = 'QUEUED_UPDATE';

    public const string STATE_LIVE = 'LIVE';

    public const string STATE_FAILED_PROCESSING = 'FAILED_PROCESSING';

    public const string STATE_FAILED_PROCESSING_UPDATE = 'FAILED_PROCESSING_UPDATE';

    public const string STATE_TAKEN_DOWN = 'TAKEN_DOWN';

    public function __construct(
        private readonly ChannelManager $channelManager,
        private readonly Api $api,
    ) {
    }

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
            'generatorVersion' => Cms::version() . " ($plugin->name $plugin->version)",
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
    public function getArticleInfo(Entry $entry, array|string|null $channelId = null, bool $refresh = false): array
    {
        $records = $this->createArticleQuery($entry->id, $channelId)->get();
        $infos = [];

        foreach ($records as $record) {
            // Refresh first?
            if ($refresh && in_array($record->state, [self::STATE_PROCESSING, self::STATE_PROCESSING_UPDATE], true)) {
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
                'state' => $record->state,
                'shareUrl' => $record->shareUrl,
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
    public function canPublishEntry(Entry $entry): bool
    {
        foreach ($this->channelManager->getChannels() as $channel) {
            if ($channel->matchEntry($entry) && $channel->canPublish($entry)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Queues an entry up to be published on Apple News
     *
     * @param  Entry  $entry  The entry to be published
     * @param  string|string[]|null  $channelIds  The Apple News channel ID(s) that the entry should be published to
     */
    public function queue(Entry $entry, array|string|null $channelIds = null): void
    {
        if ($channelIds === null) {
            // Queue all of them
            $channelIds = [];
            foreach ($this->channelManager->getChannels() as $channel) {
                if ($channel->matchEntry($entry) && $channel->canPublish($entry)) {
                    $channelIds[] = $channel->getChannelId();
                }
            }
        } else {
            $channelIds = Arr::wrap($channelIds);
        }

        if (empty($channelIds)) {
            return;
        }

        $articles = $this->createArticleQuery($entry->id, $channelIds)->get()->keyBy('channelId');

        foreach ($channelIds as $channelId) {
            $article = $articles[$channelId] ?? new ArticleRecord([
                'entryId' => $entry->id,
                'channelId' => $channelId,
            ]);
            $article->state = $article->articleId ? self::STATE_QUEUED_UPDATE : self::STATE_QUEUED;
            $article->save();

            dispatch(new PublishArticleJob($entry->id, $entry->siteId, $channelId));
        }
    }

    /**
     * Publishes an article to Apple News.
     *
     * @param  Entry  $entry  The entry to be published
     * @param  string|string[]|null  $channelIds  The Apple News channel ID(s) to publish the entry to
     */
    public function publish(Entry $entry, array|string|null $channelIds = null): void
    {
        if ($channelIds !== null) {
            $channelIds = Arr::wrap($channelIds);
        }

        $channels = array_filter(
            $this->channelManager->getChannels(),
            fn(ChannelInterface $channel) => (
                ($channelIds === null || in_array($channel->getChannelId(), $channelIds, true)) &&
                $channel->matchEntry($entry) &&
                $channel->canPublish($entry)
            ),
        );

        if (empty($channels)) {
            return;
        }

        $articleRecords = $this->createArticleQuery($entry->id, $channelIds)->get()->keyBy('channelId');

        foreach ($channels as $channel) {
            $channelId = $channel->getChannelId();
            $articleRecord = $articleRecords[$channelId] ?? null;

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
                'metadata' => !empty($metadata) ? Json::encode(['data' => $metadata]) : null,
                'json' => Json::encode($content),
            ];

            // Publish the article
            if ($articleRecord !== null && $articleRecord->articleId) {
                $response = $this->api->updateArticle($channelId, $articleRecord->articleId, $data);
            } else {
                $response = $this->api->createArticle($channelId, $data);
            }

            if (isset($response->data)) {
                // Save a record of the article
                if ($articleRecord === null) {
                    $articleRecord = new ArticleRecord([
                        'entryId' => $entry->id,
                        'channelId' => $channelId,
                        'articleId' => $response->data->id,
                    ]);
                } elseif (!$articleRecord->articleId) {
                    $articleRecord->articleId = $response->data->id;
                }
                $this->updateArticleRecord($articleRecord, $response);
            }
        }
    }

    /**
     * Deletes an article on Apple News.
     *
     * @param  string|string[]|null  $channelIds  The Apple News channel ID(s) to delete the entry from
     */
    public function delete(Entry $entry, array|string|null $channelIds = null): void
    {
        $records = $this->createArticleQuery($entry->id, $channelIds)
            ->where('articleId', '!=', null)
            ->get();

        foreach ($records as $record) {
            $this->api->deleteArticle($record->channelId, $record->articleId);
            $record->delete();
        }
    }

    /**
     * Creates an article record query for the given entry ID and channel IDs.
     *
     * @param  string|string[]|null  $channelIds
     * @return Builder<ArticleRecord>
     */
    protected function createArticleQuery(int $entryId, array|string|null $channelIds = null): Builder
    {
        $query = ArticleRecord::query()->where('entryId', $entryId);

        if ($channelIds !== null) {
            $query->whereIn('channelId', Arr::wrap($channelIds));
        }

        return $query;
    }

    /**
     * Updates a given Article record with the data in an Apple News API response.
     */
    protected function updateArticleRecord(ArticleRecord $record, stdClass $response): void
    {
        $record->revisionId = $response->data->revision;
        $record->isSponsored = $response->data->isSponsored;
        $record->isPreview = $response->data->isPreview;
        $record->state = $response->data->state;
        $record->shareUrl = $response->data->shareUrl;
        $record->response = Json::encode($response);
        $record->save();
    }
}
