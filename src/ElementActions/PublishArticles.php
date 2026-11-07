<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\ElementActions;

use CraftCms\AppleNews\Articles;
use CraftCms\Cms\Element\Actions\ElementAction;
use CraftCms\Cms\Element\Queries\Contracts\ElementQueryInterface;
use CraftCms\Cms\Entry\Elements\Entry;
use Illuminate\Support\Facades\Gate;
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
        $articles = app(Articles::class);
        $queued = 0;

        foreach ($query->cursor() as $entry) {
            /** @var Entry $entry */
            if (Gate::check('save', $entry)) {
                $articles->queue($entry);
                $queued++;
            }
        }

        if ($queued === 0) {
            $this->setMessage(t('You don’t have permission to publish the selected entries.', category: 'apple-news'));

            return false;
        }

        return true;
    }
}
