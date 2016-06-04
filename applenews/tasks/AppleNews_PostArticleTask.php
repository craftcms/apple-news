<?php
namespace Craft;

/**
 * Class AppleNews_PostArticleTask
 */
class AppleNews_PostArticleTask extends BaseTask
{
	// Properties
	// =========================================================================

	/**
	 * @var int[] The IDs of the entries that will be posted to Apple News
	 */
	private $_entryIds;

	// Public Methods
	// =========================================================================

	/**
	 * @inheritDoc ITask::getDescription()
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return Craft::t('Posting to Apple News');
	}

	/**
	 * @inheritDoc ITask::getTotalSteps()
	 *
	 * @return int
	 */
	public function getTotalSteps()
	{
		// Normalize the entryId setting
		$this->_entryIds = $this->getSettings()->entryId;

		if (!is_array($this->_entryIds)) {
			$this->_entryIds = [$this->_entryIds];
		}

		$this->_entryIds = array_values(array_unique($this->_entryIds, SORT_NUMERIC));

		return count($this->_entryIds);
	}

	/**
	 * @inheritDoc ITask::runStep()
	 *
	 * @param int $step
	 *
	 * @return bool
	 */
	public function runStep($step)
	{
		$entry = craft()->entries->getEntryById($this->_entryIds[$step], $this->getSettings()->locale);

		if ($entry) {
			$this->getService()->postArticle($entry);
		}

		return true;
	}

	// Protected Methods
	// =========================================================================

	/**
	 * @inheritDoc BaseSavableComponentType::defineSettings()
	 *
	 * @return array
	 */
	protected function defineSettings()
	{
		return array(
			'entryId' => AttributeType::Mixed,
			'locale' => AttributeType::Locale,
		);
	}

	/**
	 * @return AppleNewsService
	 */
	protected function getService()
	{
		return craft()->appleNews;
	}
}
