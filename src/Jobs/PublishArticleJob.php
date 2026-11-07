<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Jobs;

use CraftCms\AppleNews\ArticleManager;
use CraftCms\AppleNews\Models\Article;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Queue\Job;
use CraftCms\Cms\Support\Facades\I18N;
use Override;

/**
 * Publishes an entry to one Apple News channel.
 *
 * @since 5.0.0
 */
class PublishArticleJob extends Job
{
    public function __construct(
        public readonly int $entryId,
        public readonly int $siteId,
        public readonly string $channelId,
    ) {
        parent::__construct();
    }

    public function handle(ArticleManager $articleManager): void
    {
        // Make sure the article is still queued
        $queued = Article::query()
            ->where('entryId', $this->entryId)
            ->where('channelId', $this->channelId)
            ->whereIn('state', [ArticleManager::STATE_QUEUED, ArticleManager::STATE_QUEUED_UPDATE])
            ->exists();

        if (!$queued) {
            return;
        }

        /** @var Entry|null $entry */
        $entry = Entry::find()
            ->id($this->entryId)
            ->siteId($this->siteId)
            ->status(null)
            ->one();

        if (!$entry) {
            return;
        }

        $this->setProgress(0, $entry->title);
        $articleManager->publish($entry, $this->channelId);
    }

    #[Override]
    protected function defaultDescription(): string
    {
        return I18N::prep('Publishing to Apple News', [], 'apple-news');
    }
}
