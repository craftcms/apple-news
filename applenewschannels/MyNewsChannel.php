<?php

use Craft\AppleNewsArticle;
use Craft\BaseAppleNewsChannel;
use Craft\Craft;
use Craft\EntryModel;
use Craft\AppleNewsHelper;
use Craft\IAppleNewsArticle;

/**
 * Class MyNewsChannel
 */
class MyNewsChannel extends BaseAppleNewsChannel
{
    // Public Methods
    // =========================================================================

    /**
     * Determines whether a given entry should be included in the News channel.
     *
     * @param EntryModel $entry The entry
     *
     * @return bool Whether the entry should be included in the News channel
     */
    public function matchEntry(EntryModel $entry)
    {
        if ($entry->locale != 'en') {
            return false;
        }

        if ($entry->getSection()->handle != 'news') {
            return false;
        }

        if ($entry->getType()->handle != 'article') {
            return false;
        }

        return true;
    }

    /**
     * Determines whether a given entry should be published to Apple News in its current state.
     *
     * @param EntryModel $entry The entry
     *
     * @return bool Whether the entry should be published to Apple News
     */
    public function canPublish(EntryModel $entry)
    {
        if ($entry->getStatus() != EntryModel::LIVE) {
            return false;
        }

        return true;
    }

    /**
     * Creates an {@link Craft\IAppleNewsArticle} for the given entry
     *
     * @param EntryModel $entry The entry
     *
     * @return IAppleNewsArticle The article that represents the entry
     */
    public function createArticle(EntryModel $entry)
    {
        Yii::import('applenewschannels.MyNewsArticle');
        $article = new MyNewsArticle($entry);

        return $article;
    }
}
