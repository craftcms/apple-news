<?php

namespace craft\applenews;

use craft\elements\Entry;

/**
 * Channel interface
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
interface ChannelInterface
{
    /**
     * @return string The channel ID
     */
    public function getChannelId(): string;

    /**
     * @return string The channel API key ID
     */
    public function getApiKeyId(): string;

    /**
     * @return string The channel API shared secret
     */
    public function getApiSecret(): string;

    /**
     * Determines whether a given entry should be included in the News channel.
     *
     * @param Entry $entry The entry
     * @return bool Whether the entry should be included in the News channel
     */
    public function matchEntry(Entry $entry): bool;

    /**
     * Determines whether a given entry should be published to Apple News in its current state.
     *
     * @param Entry $entry The entry
     * @return bool Whether the entry should be published to Apple News
     */
    public function canPublish(Entry $entry): bool;

    /**
     * Creates an article for the given entry
     *
     * @param Entry $entry The entry
     * @return ArticleInterface The article that represents the entry
     */
    public function createArticle(Entry $entry): ArticleInterface;
}
