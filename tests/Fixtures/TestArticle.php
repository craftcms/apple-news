<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Tests\Fixtures;

use CraftCms\AppleNews\Article;
use CraftCms\AppleNews\Helper;

class TestArticle extends Article
{
    public function init(): void
    {
        $this->setContent([
            'version' => '1.1',
            'identifier' => (string) $this->entry->id,
            'title' => $this->entry->title,
            'language' => Helper::formatLanguage($this->entry->getSite()->language),
            'components' => Helper::markdown2Components("# Hello\n\nSome *text*."),
        ]);
        $this->addMetadata('links.sections', ['https://news-api.apple.com/sections/abc']);
        $this->addFile(__DIR__ . '/photo.png');
    }
}
