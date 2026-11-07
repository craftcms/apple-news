<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Tests\Fixtures;

use CraftCms\AppleNews\ArticleInterface;
use CraftCms\AppleNews\BaseChannel;
use CraftCms\Cms\Entry\Elements\Entry;

class TestChannel extends BaseChannel
{
    public ?string $sectionHandle = null;

    public function matchEntry(Entry $entry): bool
    {
        return $this->sectionHandle === null || $entry->getSection()?->handle === $this->sectionHandle;
    }

    public function createArticle(Entry $entry): ArticleInterface
    {
        return new TestArticle($entry);
    }
}
