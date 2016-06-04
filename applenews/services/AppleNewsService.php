<?php
namespace Craft;

use ChapterThree\AppleNewsAPI\PublisherAPI;

/**
 * Class AppleNewsService
 */
class AppleNewsService extends BaseApplicationComponent
{
	// Properties
	// =========================================================================

	/**
	 * @var IAppleNewsChannel[] The channels
	 */
	private $_channels;

	/**
	 * @var array Generator metadata properties
	 */
	private $_generatorMetadata;

	// Public Methods
	// =========================================================================

	/**
	 * Instantiates the application component
	 */
	public function init()
	{
		// Autoload the Composer packages
		require craft()->path->getPluginsPath().'applenews/vendor/autoload.php';

		// Import base classes
		Craft::import('plugins.applenews.IAppleNewsArticle');
		Craft::import('plugins.applenews.AppleNewsArticle');
		Craft::import('plugins.applenews.IAppleNewsChannel');
		Craft::import('plugins.applenews.BaseAppleNewsChannel');

		// Set the applenewschannels alias
		defined('APPLE_NEWS_CHANELS_PATH') || define('APPLE_NEWS_CHANELS_PATH', CRAFT_BASE_PATH.'applenewschannels/');
		Craft::setPathOfAlias('applenewschannels', APPLE_NEWS_CHANELS_PATH);
	}

	/**
	 * Returns all the channels.
	 *
	 * @return IAppleNewsChannel[]
	 * @throws Exception if any of the channels don't implement IAppleNewsChannel.
	 */
	public function getChannels()
	{
		if (!isset($this->_channels))
		{
			$this->_channels = [];
			$channelConfigs = craft()->config->get('channels', 'applenews');

			foreach ($channelConfigs as $config)
			{
				$channel = Craft::createComponent($config);

				if (!($channel instanceof IAppleNewsChannel)) {
					throw new Exception('All Apple News channels must implement the IAppleNewsChannel interface');
				}

				$this->_channels[$channel->getChannelId()] = $channel;
			}
		}

		return $this->_channels;
	}

	/**
	 * Returns a channel by its ID.
	 *
	 * @param string $channelId The channel ID
	 *
	 * @return IAppleNewsChannel
	 * @throws Exception if no channel exists with that ID
	 */
	public function getChannelById($channelId)
	{
		$channels = $this->getChannels();

		if (isset($channels[$channelId])) {
			return $channels[$channelId];
		}

		throw new Exception('No channel exists with the ID '.$channelId);
	}

	/**
	 * Returns a channel’s name by its ID.
	 *
	 * @param string $channelId The channel ID
	 *
	 * @return string The channel name
	 * @throws Exception if no channel exists with that ID
	 */
	public function getChannelName($channelId)
	{
		$cacheKey = 'appleNews:channelName:'.$channelId;
		$name = craft()->cache->get($cacheKey);

		if ($name === false) {
			$info = $this->getApiService()->readChannel($channelId);
			$name = $info->data->name;
			craft()->cache->set($cacheKey, $name, 0);
		}

		return $name;
	}

	/**
	 * Returns whether any channels can publish the given entry.
	 *
	 * @param EntryModel $entry
	 *
	 * @return bool Whether the entry can be published to any channels
	 */
	public function canPostArticle(EntryModel $entry)
	{
		// See if any channels will have it
		foreach ($this->getChannels() as $channel) {
			if ($channel->matchEntry($entry) && $channel->canPublish($entry)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Posts an article to Apple News.
	 *
	 * @param EntryModel $entry
	 *
	 * @return bool Whether the entry was posted to Apple News successfully
	 */
	public function postArticle(EntryModel $entry)
	{
		/** @var IAppleNewsChannel[] $channels */
		$channels = [];

		foreach ($this->getChannels() as $channel) {
			if ($channel->matchEntry($entry) && $channel->canPublish($entry)) {
				$channels[] = $channel;
			}
		}

		if (!$channels) {
			return false;
		}

		$articleRecords = $this->getArticleRecordsForEntry($entry);

		foreach ($channels as $channel) {
			$channelId = $channel->getChannelId();
			$articleExists = isset($articleRecords[$channelId]);

			$article = $channel->createArticle($entry);
			$content = $article->getContent();
			$metadata = $article->getMetadata() ?: [];

			// Include the generator metadata in the content
			$content['metadata'] = array_merge(
				isset($content['metadata']) ? $content['metadata'] : [],
				$this->getGeneratorMetadata());

			// Include the latest revision ID if we have one
			if ($articleExists) {
				$revisionId = $articleRecords[$channelId]->revisionId;
				$metadata['revision'] = $revisionId;
			}

			// Prepare the data and send the request
			$data = [
				'files' => $article->getFiles(),
				'metadata' => $metadata ? JsonHelper::encode(['data' => $metadata]) : null,
				'json' => JsonHelper::encode($content)
			];

			// Publish the article
			if ($articleExists) {
				$articleId = $articleRecords[$channelId]->articleId;
				$response = $this->getApiService()->updateArticle($channelId, $articleId, $data);
			} else {
				$response = $this->getApiService()->createArticle($channelId, $data);
			}

			if (isset($response->data)) {
				// Save a record of the article
				if ($articleExists) {
					$record = $articleRecords[$channelId];
				} else {
					$record = new AppleNews_ArticleRecord();
					$record->entryId = $entry->id;
					$record->channelId = $channelId;
					$record->articleId = $response->data->id;
				}
				$record->shareUrl = $response->data->shareUrl;
				$record->revisionId = $response->data->revision;
				$record->response = JsonHelper::encode($response);
				$record->save();
			}

			if ($articleExists) {
				// Forget about this record since we've dealt with it, so it doesn't get deleted
				unset($articleRecords[$channelId]);
			}
		}

		// If there are any records left over, delete them
		$this->deleteArticlesFromRecords($articleRecords);

		return true;
	}

	/**
	 * Deletes an article in a channel.
	 *
	 * @param EntryModel $entry
	 *
	 * @return void
	 */
	public function deleteArticle(EntryModel $entry)
	{
		$articleRecords = $this->getArticleRecordsForEntry($entry);
		$this->deleteArticlesFromRecords($articleRecords);
	}

	// Protected Methods
	// =========================================================================

	/**
	 * Returns the API service.
	 *
	 * @return AppleNews_ApiService
	 */
	protected function getApiService()
	{
		return craft()->appleNews_api;
	}

	/**
	 * Returns article records for a given entry ID, indexed by the channel ID.
	 *
	 * @param EntryModel $entry
	 *
	 * @return AppleNews_ArticleRecord[]
	 */
	protected function getArticleRecordsForEntry(EntryModel $entry)
	{
		$records = AppleNews_ArticleRecord::model()->findAllByAttributes([
			'entryId' => $entry->id
		]);

		// Index by channel ID
		$recordsByChannelId = [];

		foreach ($records as $record) {
			$recordsByChannelId[$record->channelId] = $record;
		}

		return $recordsByChannelId;
	}

	/**
	 * Deletes articles on Apple News based on the given records.
	 *
	 * @param AppleNews_ArticleRecord[] $records The article records
	 *
	 * @return void
	 */
	protected function deleteArticlesFromRecords($records)
	{
		$apiService = $this->getApiService();
		foreach ($records as $channelId => $record) {
			$apiService->deleteArticle($channelId, $record->articleId);
			$record->delete();
		}
	}

	/**
	 * @return array Generator metadata properties
	 */
	protected function getGeneratorMetadata()
	{
		if (!isset($this->_generatorMetadata)) {
			$this->_generatorMetadata = [
				'generatorIdentifier' => 'AppleNewsForCraftCMS',
				'generatorName' => 'Apple News for Craft CMS',
				'generatorVersion' => craft()->plugins->getPlugin('applenews')->getVersion(),
			];
		}
		return $this->_generatorMetadata;
	}
}
