<?php

namespace craft\applenews;

use craft\elements\Asset;

/**
 * Article interface
 *
 * See <https://developer.apple.com/documentation/apple_news/apple_news_api#//apple_ref/doc/uid/TP40015409-CH14-SW1> for
 * info about the Apple News API.
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
interface ArticleInterface
{
    /**
     * Returns the article content, described in Apple News Format.
     *
     * This will become the `article.json` part of the publish request.
     *
     * @return array The article content
     */
    public function getContent(): array;

    /**
     * Returns the files that are included in the article.
     *
     * This should be set to an array where the keys are the file URIs within the publish request
     * (everything after `bundle://`) and the values are either strings (local path to the file)
     * or [[Asset]] objects.
     *
     * @return string[]|Asset[] The files that are included in the article
     */
    public function getFiles(): array;

    /**
     * Returns metadata about the article.
     *
     * This will become the `metadata` part of the publish request.
     *
     * @return array Metadata about the article
     */
    public function getMetadata(): array;
}
