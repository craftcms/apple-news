<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Listeners;

use CraftCms\AppleNews\ArticleManager;
use CraftCms\Cms\Element\Events\ElementDeleting;
use CraftCms\Cms\Entry\Elements\Entry;

/**
 * Deletes an entry’s articles from Apple News when the entry is deleted.
 *
 * @since 5.0.0
 */
class DeleteArticles
{
    public function __construct(
        private readonly ArticleManager $articleManager,
    ) {}

    public function handle(ElementDeleting $event): void
    {
        if ($event->element instanceof Entry) {
            $this->articleManager->delete($event->element);
        }
    }
}
