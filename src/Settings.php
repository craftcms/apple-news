<?php

declare(strict_types=1);

namespace CraftCms\AppleNews;

use CraftCms\Cms\Plugin\PluginSettings;

/**
 * Settings, set from `config/craft/apple-news.php`.
 *
 * @since 2.0
 */
class Settings extends PluginSettings
{
    /**
     * @var array<array<string, mixed>|class-string<ChannelInterface>> Channel configs: a class name, or an array with a `class` key and the channel's properties
     */
    public array $channels = [];

    /**
     * @var bool Whether entries should be published to Apple News whenever they are saved
     */
    public bool $autoPublishOnSave = true;

    /**
     * @var int Seconds to wait for the Apple News API to respond
     */
    public int $httpClientTimeout = 30;
}
