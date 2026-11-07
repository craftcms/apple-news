<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Listeners;

use CraftCms\AppleNews\Articles;
use CraftCms\AppleNews\Settings;
use CraftCms\Cms\Element\ElementHelper;
use CraftCms\Cms\Element\Events\ElementSaved;
use CraftCms\Cms\Entry\Elements\Entry;

/**
 * Queues saved entries to be published, when `autoPublishOnSave` is enabled.
 *
 * @since 5.0.0
 */
class QueueSavedEntry
{
    public function __construct(
        private readonly Settings $settings,
        private readonly Articles $articles,
    ) {}

    public function handle(ElementSaved $event): void
    {
        if (
            ! $this->settings->autoPublishOnSave ||
            ! $event->element instanceof Entry ||
            ElementHelper::isDraftOrRevision($event->element)
        ) {
            return;
        }

        $this->articles->queue($event->element);
    }
}
