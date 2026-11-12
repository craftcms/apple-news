<?php

declare(strict_types=1);

namespace CraftCms\AppleNews;

use CraftCms\Cms\Cms;
use CraftCms\Cms\Support\Config;
use CraftCms\Cms\Support\Typecast;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use UnexpectedValueException;

/** @since 2.0 */
#[Singleton]
class Channels
{
    /**
     * @var ChannelInterface[]|null The channels, indexed by channel ID
     */
    private ?array $channels = null;

    public function __construct(
        private readonly Settings $settings,
    ) {}

    /**
     * Returns all the channels.
     *
     * @return ChannelInterface[]
     *
     * @throws UnexpectedValueException if a channel config is invalid
     */
    public function all(): array
    {
        if ($this->channels !== null) {
            return $this->channels;
        }

        $this->channels = [];

        foreach ($this->settings->channels as $config) {
            $channel = $this->createChannel($config);
            $this->channels[$channel->getChannelId()] = $channel;
        }

        return $this->channels;
    }

    /**
     * Returns a channel by its ID.
     *
     * @param  string  $channelId  The channel ID
     *
     * @throws InvalidArgumentException if no channel exists with that ID
     */
    public function get(string $channelId): ChannelInterface
    {
        $channels = $this->all();
        if (! isset($channels[$channelId])) {
            throw new InvalidArgumentException('No channel exists with the ID '.$channelId);
        }

        return $channels[$channelId];
    }

    /**
     * Returns a channel’s name by its ID.
     *
     * @param  string  $channelId  The channel ID
     * @return string The channel name
     *
     * @throws InvalidArgumentException if no channel exists with that ID
     */
    public function name(string $channelId): string
    {
        return Cache::remember(
            "apple-news:channel-name:$channelId",
            Config::durationInSeconds(Cms::config()->cacheDuration) ?: null,
            fn () => app(Api::class)->channel($channelId)->data->name,
        );
    }

    /**
     * @param  array<string, mixed>|class-string<ChannelInterface>  $config
     */
    private function createChannel(array|string $config): ChannelInterface
    {
        if (is_string($config)) {
            $config = ['class' => $config];
        }

        $class = $config['class'] ?? null;
        unset($config['class']);

        if (! is_string($class) || ! is_subclass_of($class, ChannelInterface::class)) {
            throw new UnexpectedValueException('Channels must implement '.ChannelInterface::class.'.');
        }

        /** @var ChannelInterface */
        return Typecast::configure(app()->make($class), $config);
    }
}
