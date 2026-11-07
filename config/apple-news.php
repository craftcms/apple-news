<?php

/**
 * Apple News settings.
 *
 * Publish this file to `config/craft/apple-news.php` with
 * `php artisan vendor:publish --tag=apple-news`.
 */

return [
    // The Apple News channels to publish to. Each one is a channel class name, or an array with a
    // `class` key and values for the class’s public properties.
    'channels' => [
        // [
        //     'class' => applenewschannels\MyNewsChannel::class,
        //     'channelId' => env('NEWS_CHANNEL_ID'),
        //     'apiKeyId' => env('NEWS_API_KEY'),
        //     'apiSecret' => env('NEWS_API_SECRET'),
        // ],
    ],

    // Whether entries should be published to Apple News whenever they are saved
    'autoPublishOnSave' => true,

    // Seconds to wait for the Apple News API to respond
    'httpClientTimeout' => 30,
];
