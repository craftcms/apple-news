<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Tests;

use CraftCms\AppleNews\Tests\Fixtures\TestChannel;
use CraftCms\Cms\Plugin\Testing\PluginTestCase;
use Illuminate\Database\Eloquent\Factories\Factory;
use Override;

abstract class TestCase extends PluginTestCase
{
    public const string CHANNEL_ID = '11111111-1111-1111-1111-111111111111';

    public const string API_KEY_ID = 'test-key';

    public const string API_SECRET = 'c2VjcmV0LWtleS1mb3ItdGVzdHM=';

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        // Use Craft’s model factories, as core’s tests do
        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'CraftCms\\Cms\\Database\\Factories\\'.class_basename($modelName).'Factory',
        );
    }

    #[Override]
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('craft.apple-news', [
            'channels' => [
                [
                    'class' => TestChannel::class,
                    'channelId' => self::CHANNEL_ID,
                    'apiKeyId' => self::API_KEY_ID,
                    'apiSecret' => self::API_SECRET,
                    'sectionHandle' => 'news',
                ],
            ],
            'autoPublishOnSave' => false,
        ]);
    }
}
