<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Utilities;

use CraftCms\AppleNews\Api;
use CraftCms\AppleNews\Channels;
use CraftCms\Cms\Utility\Utility;
use CraftCms\Cms\View\TemplateMode;
use Override;

use function CraftCms\Cms\t;
use function CraftCms\Cms\template;

/**
 * Lists each channel’s sections, with their IDs.
 *
 * @since 2.0
 */
class AppleNewsInfo extends Utility
{
    #[Override]
    public static function id(): string
    {
        return 'apple-news-info';
    }

    #[Override]
    public static function displayName(): string
    {
        return t('Apple News Info', category: 'apple-news');
    }

    #[Override]
    public static function icon(): ?string
    {
        return dirname(__DIR__, 2).'/resources/icon-mask.svg';
    }

    #[Override]
    public static function contentHtml(): string
    {
        $channels = app(Channels::class);
        $api = app(Api::class);

        // Load the channel info
        $channelNames = [];
        $sections = [];

        foreach ($channels->all() as $channelId => $channel) {
            $channelNames[$channelId] = $channels->name($channelId);
            $sections[$channelId] = $api->sections($channelId)->data;
        }

        return template('apple-news/_info', [
            'channels' => $channels->all(),
            'channelNames' => $channelNames,
            'sections' => $sections,
        ], TemplateMode::Cp);
    }
}
