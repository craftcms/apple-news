<?php

namespace craft\applenews\records;

use craft\db\ActiveRecord;

/**
 * Article active record
 *
 * @param int $id
 * @property int $entryId
 * @property string $channelId
 * @property string $articleId
 * @property string $revisionId
 * @property bool $isSponsored
 * @property bool $isPreview
 * @property string $state
 * @property string $shareUrl
 * @property string $response
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
class Article extends ActiveRecord
{
    /**
     * @inherit
     */
    public static function tableName(): string
    {
        return '{{%applenews_articles}}';
    }
}
