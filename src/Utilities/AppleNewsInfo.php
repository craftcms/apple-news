<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Utilities;

use CraftCms\AppleNews\Api;
use CraftCms\AppleNews\Channels;
use CraftCms\Cms\Support\Html;
use CraftCms\Cms\Utility\Utility;
use Override;

use function CraftCms\Cms\t;

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

        return Html::tag('AppleNewsInfo', attributes: [
            ':channels' => array_map(fn (string $channelId) => [
                'id' => $channelId,
                'name' => $channels->name($channelId),
                'sections' => array_map(fn (object $section) => [
                    'id' => $section->id,
                    'name' => $section->name,
                    'isDefault' => (bool) ($section->isDefault ?? false),
                    'link' => $section->links->self ?? null,
                ], $api->sections($channelId)->data ?? []),
            ], array_keys($channels->all())),
        ]);
    }
}
