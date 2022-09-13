<?php

namespace craft\applenews;

use Craft;
use craft\applenews\jobs\PublishArticle;
use craft\applenews\records\Article as ArticleRecord;
use craft\elements\Entry;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use stdClass;
use yii\base\Component;
use yii\db\ActiveQuery;

/**
 * Article manager
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
class ArticleManager extends Component
{
    const STATE_PROCESSING = 'PROCESSING';
    const STATE_PROCESSING_UPDATE = 'PROCESSING_UPDATE';
    const STATE_QUEUED = 'QUEUED';
    const STATE_QUEUED_UPDATE = 'QUEUED_UPDATE';
    const STATE_LIVE = 'LIVE';
    const STATE_FAILED_PROCESSING = 'FAILED_PROCESSING';
    const STATE_FAILED_PROCESSING_UPDATE = 'FAILED_PROCESSING_UPDATE';
    const STATE_TAKEN_DOWN = 'TAKEN_DOWN';

    /**
     * @var array The generator metadata
     */
    public $generator;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $plugin = Plugin::getInstance();
        $this->generator = [
            'generatorIdentifier' => 'CraftCMS',
            'generatorName' => 'Craft CMS',
            'generatorVersion' => Craft::$app->getVersion() . " ({$plugin->name} {$plugin->version})"
        ];
    }

    /**
     * Returns all known info about an entry's articles on Apple News.
     *
     * @param Entry $entry The entry
     * @param string|string[]|null $channelId The channel ID(s) to limit the query to
     * @param bool $refresh Whether the info should be refreshed for articles that are processing
     * @return array[] The info, indexed by channel ID
     */
    public function getArticleInfo(Entry $entry, $channelId = null, bool $refresh = false)
    {
        $query = ArticleRecord::find()
            ->where(['entryId' => $entry->id]);

        if ($channelId !== null) {
            $query->andWhere(['channelId' => $channelId]);
        }

        /** @var ArticleRecord[] $records */
        $records = $query->all();
        $infos = [];
        $api = Plugin::getInstance()->api;

        foreach ($records as $record) {
            // Refresh first?
            if ($refresh && in_array($record->state, [self::STATE_PROCESSING, self::STATE_PROCESSING_UPDATE], true)) {
                $response = $api->article($record->channelId, $record->articleId);
                if (isset($response->data)) {
                    $this->updateArticleRecord($record, $response);
                }
            }

            $infos[$record->channelId] = [
                'articleId' => $record->articleId,
                'revisionId' => $record->revisionId,
                'isSponsored' => (bool)$record->isSponsored,
                'isPreview' => (bool)$record->isPreview,
                'state' => $record->state,
                'shareUrl' => $record->shareUrl,
            ];
        }

        return $infos;
    }

    /**
     * Returns whether any Apple News channels can publish the given entry.
     *
     * @param Entry $entry The entry to check
     * @return bool Whether the entry can be published to any channels
     */
    public function canPublishEntry(Entry $entry): bool
    {
        // See if any channels will have it
        foreach (Plugin::getInstance()->channelManager->getChannels() as $channel) {
            if ($channel->matchEntry($entry) && $channel->canPublish($entry)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Queues an entry up to be published on Apple News
     *
     * @param Entry $entry The entry to be published
     * @param string|string[]|null $channelIds The Apple News channel ID(s) that the entry should be published to
     */
    public function queue(Entry $entry, $channelIds = null)
    {
        if ($channelIds === null) {
            // Queue all of them
            $channelIds = [];
            foreach (Plugin::getInstance()->channelManager->getChannels() as $channel) {
                if ($channel->matchEntry($entry) && $channel->canPublish($entry)) {
                    $channelIds[] = $channel->getChannelId();
                }
            }
        } else if (!is_array($channelIds)) {
            $channelIds = [$channelIds];
        }

        if (empty($channelIds)) {
            return;
        }

        $queue = Craft::$app->getQueue();

        /** @var ArticleRecord $articles */
        $articles = ArticleRecord::find()
            ->where([
                'entryId' => $entry->id,
                'channelId' => $channelIds,
            ])
            ->indexBy('channelId')
            ->all();

        foreach ($channelIds as $channelId) {
            $article = $articles[$channelId] ?? new ArticleRecord([
                    'entryId' => $entry->id,
                    'channelId' => $channelId,
                ]);
            $article->state = $article->articleId ? self::STATE_QUEUED_UPDATE : self::STATE_QUEUED;
            $article->save();

            $queue->push(new PublishArticle([
                'entryId' => $entry->id,
                'siteId' => $entry->siteId,
                'channelId' => $channelId,
            ]));
        }
    }

    /**
     * Publishes an article to Apple News.
     *
     * @param Entry $entry The entry to be published
     * @param string|string[]|null $channelIds The Apple News channel ID(s) to publish the entry to
     */
    public function publish(Entry $entry, $channelIds = null)
    {
        if (is_string($channelIds)) {
            $channelIds = [$channelIds];
        }

        /** @var ChannelInterface[] $channels */
        $channels = ArrayHelper::where(Plugin::getInstance()->channelManager->getChannels(),
            function(ChannelInterface $channel) use ($entry, $channelIds) {
                return (
                    ($channelIds === null || in_array($channel->getChannelId(), $channelIds)) &&
                    $channel->matchEntry($entry) &&
                    $channel->canPublish($entry)
                );
            });

        if (empty($channels)) {
            return;
        }

        /** @var ArticleRecord[] $articleRecords */
        $articleRecords = $this->createArticleQuery($entry->id, $channelIds)->all();

        foreach ($channels as $channel) {
            $channelId = $channel->getChannelId();
            $articleRecord = $articleRecords[$channelId] ?? null;

            $article = $channel->createArticle($entry);
            $content = $article->getContent();
            $metadata = $article->getMetadata() ?: [];

            // Include the generator metadata in the content
            $content['metadata'] = array_merge(
                $content['metadata'] ?? [],
                $this->generator
            );

            // Include the latest revision ID if we have one
            if ($articleRecord !== null) {
                $metadata['revision'] = $articleRecord->revisionId;
            }

            // Prepare the data and send the request
            $data = [
                'files' => $article->getFiles(),
                'metadata' => !empty($metadata) ? Json::encode(['data' => $metadata]) : null,
                'json' => Json::encode($content)
            ];

            // Publish the article
            $api = Plugin::getInstance()->api;
            if ($articleRecord !== null && $articleRecord->articleId) {
                $response = $api->updateArticle($channelId, $articleRecord->articleId, $data);
            } else {
                $response = $api->createArticle($channelId, $data);
            }

            if (isset($response->data)) {
                // Save a record of the article
                if ($articleRecord === null) {
                    $articleRecord = new ArticleRecord([
                        'entryId' => $entry->id,
                        'channelId' => $channelId,
                        'articleId' => $response->data->id,
                    ]);
                } else if (!$articleRecord->articleId) {
                    $articleRecord->articleId = $response->data->id;
                }
                $this->updateArticleRecord($articleRecord, $response);
            }
        }
    }

    /**
     * Deletes an article on Apple News.
     *
     * @param Entry $entry
     * @param string|string[]|null $channelIds The Apple News channel ID(s) to delete the entry from
     */
    public function delete(Entry $entry, $channelIds = null)
    {
        /** @var ArticleRecord[] $records */
        $records = $this->createArticleQuery($entry->id, $channelIds)
            ->andWhere(['not', ['articleId' => null]])
            ->all();
        $api = Plugin::getInstance()->api;

        foreach ($records as $record) {
            $api->deleteArticle($record->channelId, $record->articleId);
            $record->delete();
        }
    }

    /**
     * Creates an article record query for the given entry ID and channel IDs.
     *
     * @param int $entryId
     * @param string[]|null $channelIds
     * @return ActiveQuery
     */
    protected function createArticleQuery(int $entryId, $channelIds = null): ActiveQuery
    {
        $query = ArticleRecord::find()
            ->where(['entryId' => $entryId])
            ->indexBy('channelId');

        if ($channelIds !== null) {
            $query->andWhere(['channelId' => $channelIds]);
        }

        return $query;
    }

    /**
     * Updates a given Article record with the data in an Apple News API response.
     *
     * @param ArticleRecord $record
     * @param stdClass $response
     */
    protected function updateArticleRecord(ArticleRecord $record, stdClass $response)
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
