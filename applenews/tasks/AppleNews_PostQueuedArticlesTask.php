<?php
namespace Craft;

/**
 * Class AppleNews_PostQueuedArticlesTask
 */
class AppleNews_PostQueuedArticlesTask extends BaseTask
{
	// Properties
	// =========================================================================

	/**
	 * @var array[] Info needed for each step
	 */
	private $_stepInfo;

	// Public Methods
	// =========================================================================

	/**
	 * @inheritDoc ITask::getDescription()
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return Craft::t('Posting articles to Apple News');
	}

	/**
	 * @inheritDoc ITask::getTotalSteps()
	 *
	 * @return int
	 */
	public function getTotalSteps()
	{
		$limit = $this->getSettings()->limit;
		$db = craft()->db;

		// Get the rows
		$rows = $db->createCommand()
			->select('id, entryId, locale, channelId')
			->from('applenews_articlequeue')
			->order('id asc')
			->limit($limit)
			->queryAll();

		// If there are any more, create a follow-up task.
		if ($limit) {
			$total = $db->createCommand()
				->from('applenews_articlequeue')
				->count('id');
			if ($total > $limit) {
				$this->getService()->createPostQueuedArticlesTask();
			}
		}

		$this->_stepInfo = [];

		foreach ($rows as $row) {
			$key = $row['entryId'].':'.$row['locale'];
			if (!isset($this->_stepInfo[$key])) {
				$this->_stepInfo[$key] = [
					'entryId' => $row['entryId'],
					'locale' => $row['locale'],
					'channelIds' => [],
				];
			}
			$this->_stepInfo[$key]['channelIds'][] = $row['channelId'];
		}

		return count($this->_stepInfo);
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
		$info = array_shift($this->_stepInfo);
		$entry = craft()->entries->getEntryById($info['entryId'], $info['locale']);

		if ($entry) {
			$this->getService()->postArticle($entry, $info['channelIds']);
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
			'limit' => [AttributeType::Number, 'default' => 50],
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
