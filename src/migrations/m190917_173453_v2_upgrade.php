<?php

namespace craft\applenews\migrations;

use craft\db\Migration;

/**
 * m190917_173453_v2_upgrade migration.
 */
class m190917_173453_v2_upgrade extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Drop the queue table
        $this->dropTableIfExists('{{%applenews_articlequeue}}');

        // Update the articles table (no need to worry about Postgres here)
        $this->alterColumn('{{%applenews_articles}}', 'entryId', $this->integer()->notNull());
        $this->alterColumn('{{%applenews_articles}}', 'articleId', $this->integer());
        $this->alterColumn('{{%applenews_articles}}', 'revisionId', $this->integer());
        $this->alterColumn('{{%applenews_articles}}', 'state', $this->string()->notNull());
        $this->createIndex(null, '{{%applenews_articles}}', ['entryId', 'channelId', 'state']);
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190917_173453_v2_upgrade cannot be reverted.\n";
        return false;
    }
}
