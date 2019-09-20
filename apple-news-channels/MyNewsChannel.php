<?php

namespace applenewschannels;

use craft\applenews\ArticleInterface;
use craft\applenews\BaseChannel;
use craft\elements\Entry;

/**
 * Class MyNewsChannel
 */
class MyNewsChannel extends BaseChannel
{
    /**
     * Determines whether a given entry should be included in the News channel.
     *
     * @param Entry $entry The entry
     * @return bool Whether the entry should be included in the News channel
     */
    public function matchEntry(Entry $entry): bool
    {
        if ($entry->site->handle !== 'default') {
            return false;
        }

        if ($entry->section->handle !== 'news') {
            return false;
        }

        if ($entry->type->handle !== 'article') {
            return false;
        }

        return true;
    }

    /**
     * Determines whether a given entry should be published to Apple News in its current state.
     *
     * @param Entry $entry The entry
     * @return bool Whether the entry should be published to Apple News
     */
    public function canPublish(Entry $entry): bool
    {
        return $entry->status === Entry::STATUS_LIVE;
    }

    /**
     * Creates an article for the given entry
     *
     * @param Entry $entry The entry
     * @return ArticleInterface The article that represents the entry
     */
    public function createArticle(Entry $entry): ArticleInterface
    {
        return new MyNewsArticle($entry);
    }
}
