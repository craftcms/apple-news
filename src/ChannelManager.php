<?php

namespace craft\applenews;

use Craft;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;

/**
 * Channel manager
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 *
 * @property-read ChannelInterface[] $channels
 */
class ChannelManager extends Component
{
    /**
     * @var ChannelInterface[] The channels
     */
    private array $_channels;

    /**
     * Returns all the channels.
     *
     * @return ChannelInterface[]
     * @throws InvalidConfigException
     */
    public function getChannels(): array
    {
        if ($this->_channels !== null) {
            return $this->_channels;
        }

        $this->_channels = [];
        $channelConfigs = Plugin::getInstance()->getSettings()->channels;

        foreach ($channelConfigs as $config) {
            $channel = Craft::createObject($config);

            if (!$channel instanceof ChannelInterface) {
                throw new InvalidConfigException('Channels must implement ' . ChannelInterface::class . '.');
            }

            $this->_channels[$channel->getChannelId()] = $channel;
        }

        return $this->_channels;
    }

    /**
     * Returns a channel by its ID.
     *
     * @param string $channelId The channel ID
     * @return ChannelInterface
     * @throws InvalidConfigException
     * @throws InvalidArgumentException if no channel exists with that ID
     */
    public function getChannelById(string $channelId): ChannelInterface
    {
        $channels = $this->getChannels();
        if (!isset($channels[$channelId])) {
            throw new InvalidArgumentException('No channel exists with the ID ' . $channelId);
        }
        return $channels[$channelId];
    }

    /**
     * Returns a channel’s name by its ID.
     *
     * @param string $channelId The channel ID
     * @return string The channel name
     * @throws InvalidArgumentException if no channel exists with that ID
     */
    public function getChannelName(string $channelId): string
    {
        return Craft::$app->cache->getOrSet("apple-news:channel-name:$channelId", function() use ($channelId) {
            return Plugin::getInstance()->api->channel($channelId)->data->name;
        });
    }
}
