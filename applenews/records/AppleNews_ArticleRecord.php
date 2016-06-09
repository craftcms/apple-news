<?php
namespace Craft;

/**
 * Class AppleNews_ArticleRecord
 *
 * @license https://github.com/pixelandtonic/AppleNews/blob/master/LICENSE
 */
class AppleNews_ArticleRecord extends BaseRecord
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
        return 'applenews_articles';
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
            ['columns' => ['entryId', 'channelId'], 'unique' => true],
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
            'channelId' => [
                AttributeType::String,
                'required' => true,
                'length' => 36
            ],
            'articleId' => [
                AttributeType::String,
                'required' => true,
                'length' => 36
            ],
            'revisionId' => [
                AttributeType::String,
                'required' => true,
                'length' => 24
            ],
            'isSponsored' => [AttributeType::Bool],
            'isPreview' => [AttributeType::Bool],
            'state' => [AttributeType::String],
            'shareUrl' => [AttributeType::Url],
            'response' => [AttributeType::Mixed],
        ];
    }
}
