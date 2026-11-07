<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\ElementActions;

use CraftCms\AppleNews\ArticleManager;
use CraftCms\Cms\Element\Actions\ElementAction;
use CraftCms\Cms\Element\Queries\Contracts\ElementQueryInterface;
use CraftCms\Cms\Entry\Elements\Entry;
use Override;

use function CraftCms\Cms\t;

/** @since 2.0 */
class PublishArticles extends ElementAction
{
    #[Override]
    public static function displayName(): string
    {
        return t('Publish to Apple News', category: 'apple-news');
    }

    #[Override]
    public function performAction(ElementQueryInterface $query): bool
    {
        $articleManager = app(ArticleManager::class);

        foreach ($query->all() as $entry) {
            /** @var Entry $entry */
            $articleManager->queue($entry);
        }

        return true;
    }
}
