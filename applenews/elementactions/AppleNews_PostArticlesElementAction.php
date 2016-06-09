<?php
namespace Craft;

/**
 * Class AppleNews_PostArticlesElementAction
 *
 * @license https://github.com/pixelandtonic/AppleNews/blob/master/LICENSE
 */
class AppleNews_PostArticlesElementAction extends BaseElementAction
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritDoc IComponentType::getName()
     *
     * @return string
     */
    public function getName()
    {
        return Craft::t('Publish to Apple News');
    }

    /**
     * @inheritDoc IElementAction::performAction()
     *
     * @param ElementCriteriaModel $criteria
     *
     * @return bool
     */
    public function performAction(ElementCriteriaModel $criteria)
    {
        /** @var AppleNewsService $service */
        $service = craft()->appleNews;

        // Queue them up
        foreach ($criteria->find() as $entry) {
            /** @var EntryModel $entry */
            $service->queueArticle($entry);
        }

        return true;
    }
}
