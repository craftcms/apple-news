<?php

namespace craft\applenews\elementactions;

use Craft;
use craft\applenews\Plugin;
use craft\base\ElementAction;
use craft\elements\db\ElementQueryInterface;
use craft\elements\Entry;

/**
 * Publish Articles element action
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 *
 * @property-read string $triggerLabel
 */
class PublishArticles extends ElementAction
{
    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('apple-news', 'Publish to Apple News');
    }

    /**
     * @inheritdoc
     */
    public function performAction(ElementQueryInterface $query): bool
    {
        $articleManager = Plugin::getInstance()->articleManager;
        foreach ($query->all() as $entry) {
            /** @var Entry $entry */
            $articleManager->queue($entry);
        }
        return true;
    }
}
