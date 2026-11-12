<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Listeners;

use CraftCms\AppleNews\ElementActions\PublishArticles;
use CraftCms\Cms\Element\Events\ElementActionsResolving;
use CraftCms\Cms\Entry\Elements\Entry;

use function CraftCms\Cms\currentUser;

/**
 * Adds the “Publish to Apple News” bulk action to entry indexes, for people who can save entries in the section.
 * The action itself checks each entry’s `save` policy.
 *
 * @since 5.0.0
 */
class AddPublishAction
{
    public function handle(ElementActionsResolving $event): void
    {
        if ($event->elementType !== Entry::class) {
            return;
        }

        $user = currentUser();

        if (
            $user !== null &&
            preg_match('/^section:(.+)/', $event->source, $matches) &&
            $user->can("saveEntries:$matches[1]")
        ) {
            $event->actions[] = PublishArticles::class;
        }
    }
}
