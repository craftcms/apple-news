<?php
namespace Craft;

/**
 * Class AppleNews_PostArticlesElementAction
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
		return Craft::t('Post to Apple News');
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
		// Create a new PostArticle task
		$entryIds = $criteria->ids();
		if (count($entryIds) == 1) {
			$entry = $criteria->first();
			$desc = Craft::t('Posting “{title}” to Apple News', ['title' => $entry->title]);
		} else {
			$desc = Craft::t('Posting {total} entries to Apple News', ['total' => count($entryIds)]);
		}

		craft()->tasks->createTask('AppleNews_PostArticle', $desc, [
			'entryId' => $entryIds,
			'locale' => $criteria->locale,
		]);

		return true;
	}
}
