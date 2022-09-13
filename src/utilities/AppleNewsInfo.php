<?php

namespace craft\applenews\utilities;

use Craft;
use craft\applenews\Plugin;
use craft\base\Utility;

abstract class AppleNewsInfo extends Utility
{
    /**
     * @inheritdoc
     */
    public static function id(): string
    {
        return 'apple-news-info';
    }

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('apple-news', 'Apple News Info');
    }

    /**
     * @inheritdoc
     */
    public static function iconPath(): ?string
    {
        return dirname(__DIR__) . '/icon-mask.svg';
    }

    /**
     * @inheritdoc
     */
    public static function contentHtml(): string
    {
        $channelManager = Plugin::getInstance()->channelManager;
        $api = Plugin::getInstance()->api;

        // Load the channel info
        $channels = $channelManager->getChannels();
        $channelNames = [];
        $sections = [];

        foreach ($channels as $channelId => $channel) {
            $channelNames[$channelId] = $channelManager->getChannelName($channelId);
            $response = $api->sections($channelId);
            $sections[$channelId] = $response->data;
        }

        return Craft::$app->getView()->renderTemplate('apple-news/_info', [
            'channels' => $channels,
            'channelNames' => $channelNames,
            'sections' => $sections,
        ]);
    }
}
