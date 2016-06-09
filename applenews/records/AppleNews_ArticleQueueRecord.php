<?php
namespace Craft;

/**
 * Class AppleNews_ArticleQueueRecord
 *
 * @license https://github.com/pixelandtonic/AppleNews/blob/master/LICENSE
 */
class AppleNews_ArticleQueueRecord extends BaseRecord
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc BaseRecord::getTableName()
     *
     * @return string
     */
    public function getTableName()
    {
        return 'applenews_articlequeue';
    }

    /**
     * @inheritDoc BaseRecord::defineRelations()
     *
     * @return array
     */
    public function defineRelations()
    {
        return [
            'entry' => [
                static::BELONGS_TO,
                'EntryRecord',
                'onDelete' => static::CASCADE
            ],
        ];
    }

    /**
     * @inheritDoc BaseRecord::defineIndexes()
     *
     * @return array
     */
    public function defineIndexes()
    {
        return [
            ['columns' => ['entryId', 'locale', 'channelId'], 'unique' => true],
        ];
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritDoc BaseRecord::defineAttributes()
     *
     * @return array
     */
    protected function defineAttributes()
    {
        return [
            'locale' => [AttributeType::Locale, 'required' => true],
            'channelId' => [
                AttributeType::String,
                'required' => true,
                'length' => 36
            ]
        ];
    }
}
