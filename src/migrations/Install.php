<?php

namespace craft\applenews\migrations;

use craft\db\Migration;
use craft\db\Table;

/**
 * Install migration
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%applenews_articles}}', [
            'id' => $this->primaryKey(),
            'entryId' => $this->integer()->notNull(),
            'channelId' => $this->char(36)->notNull(),
            'articleId' => $this->char(36),
            'revisionId' => $this->char(24),
            'isSponsored' => $this->boolean()->notNull()->defaultValue(false),
            'isPreview' => $this->boolean()->notNull()->defaultValue(false),
            'state' => $this->string()->notNull(),
            'shareUrl' => $this->string(),
            'response' => $this->text(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->createIndex(null, '{{%applenews_articles}}', ['entryId', 'channelId'], true);
        $this->createIndex(null, '{{%applenews_articles}}', ['entryId', 'channelId', 'state']);
        $this->addForeignKey(null, '{{%applenews_articles}}', ['entryId'], Table::ENTRIES, ['id'], 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTableIfExists('{{%applenews_articles}}');
    }
}
