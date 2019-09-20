<?php

namespace craft\applenews;

use craft\elements\Entry;

/**
 * Base Channel class
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
abstract class BaseChannel implements ChannelInterface
{
    /**
     * @var string The channel ID
     */
    public $channelId;

    /**
     * @var string The channel API key ID
     */
    public $apiKeyId;

    /**
     * @var string The channel API shared secret
     */
    public $apiSecret;

    /**
     * @inheritdoc
     */
    public function getChannelId(): string
    {
        return $this->channelId;
    }

    /**
     * @inheritdoc
     */
    public function getApiKeyId(): string
    {
        return $this->apiKeyId;
    }

    /**
     * @inheritdoc
     */
    public function getApiSecret(): string
    {
        return $this->apiSecret;
    }

    /**
     * @inheritdoc
     */
    public function matchEntry(Entry $entry): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function canPublish(Entry $entry): bool
    {
        return $entry->getStatus() === Entry::STATUS_LIVE;
    }
}
