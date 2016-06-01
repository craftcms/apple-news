<?php
namespace Craft;

use ChapterThree\AppleNewsAPI\PublisherAPI;

/**
 * Class AppleNews_ApiService
 */
class AppleNews_ApiService extends BaseApplicationComponent
{
	// Properties
	// =========================================================================

	protected $apis;

	// Public Methods
	// =========================================================================

	/**
	 * Returns information about a channel.
	 *
	 * @param string $channelId
	 *
	 * @return \stdClass
	 */
	public function readChannel($channelId)
	{
		return $this->get($channelId, '/channels/{channel_id}', ['channel_id' => $channelId]);
	}

	/**
	 * Returns information about a channel’s sections.
	 *
	 * @param string $channelId
	 *
	 * @return \stdClass[]
	 */
	public function listSections($channelId)
	{
		return $this->get($channelId, '/channels/{channel_id}/sections', ['channel_id' => $channelId]);
	}

	/**
	 * Returns information about a section.
	 *
	 * @param string $channelId
	 * @param string $sectionId
	 *
	 * @return \stdClass
	 */
	public function readSection($channelId, $sectionId)
	{
		return $this->get($channelId, '/sections/{section_id}', ['section_id' => $sectionId]);
	}

	/**
	 * Returns information about an article.
	 *
	 * @param string $channelId
	 * @param string $articleId
	 *
	 * @return \stdClass
	 */
	public function readArticle($channelId, $articleId)
	{
		return $this->get($channelId, '/articles/{article_id}', ['article_id' => $articleId]);
	}

	/**
	 * Searches for articles in a channel.
	 *
	 * @param string $channelId
	 * @param array $params
	 *
	 * @return \stdClass[]
	 */
	public function searchArticles($channelId, $params = [])
	{
		return $this->get($channelId, '/channels/{channel_id}/articles', ['channel_id' => $channelId], $params);
	}

	/**
	 * Creates a new article.
	 *
	 * @param string $channelId
	 * @param array  $data
	 *
	 * @return \stdClass
	 */
	public function createArticle($channelId, $data)
	{
		return $this->post($channelId, '/channels/{channel_id}/articles', ['channel_id' => $channelId], $data);
	}

	/**
	 * Updates an article.
	 *
	 * @param string $channelId
	 * @param string $articleId
	 * @param array  $data
	 *
	 * @return \stdClass
	 */
	public function updateArticle($channelId, $articleId, $data)
	{
		return $this->post($channelId, '/articles/{article_id}', ['article_id' => $articleId], $data);
	}

	/**
	 * Deletes an article.
	 *
	 * @param string $channelId
	 * @param string $articleId
	 *
	 * @return \stdClass
	 */
	public function deleteArticle($channelId, $articleId)
	{
		return $this->delete($channelId, '/articles/{article_id}', ['article_id' => $articleId]);
	}

	// Protected Methods
	// =========================================================================

	/**
	 * Sends a GET request to the Apple News API.
	 *
	 * @param string $channelId
	 * @param string $path
	 * @param array  $pathArgs
	 * @param array  $data
	 *
	 * @return mixed
	 */
	protected function get($channelId, $path, $pathArgs = [], $data = [])
	{
		$api = $this->getApi($channelId);
		$response = $api->get($path, $pathArgs, $data);

		return $response;
	}

	/**
	 * Sends a POST request to the Apple News API.
	 *
	 * @param string $channelId
	 * @param string $path
	 * @param array  $pathArgs
	 * @param array  $data
	 *
	 * @return mixed
	 */
	protected function post($channelId, $path, $pathArgs = [], $data = [])
	{
		$api = $this->getApi($channelId);
		$response = $api->post($path, $pathArgs, $data);

		return $response;
	}

	/**
	 * Sends a DELETE request to the Apple News API.
	 *
	 * @param string $channelId
	 * @param string $path
	 * @param array  $pathArgs
	 * @param array  $data
	 *
	 * @return mixed
	 */
	protected function delete($channelId, $path, $pathArgs = [], $data = [])
	{
		$api = $this->getApi($channelId);
		$response = $api->delete($path, $pathArgs, $data);

		return $response;
	}

	/**
	 * Returns a publisher API configured for a given channel ID.
	 *
	 * @param string $channelId
	 *
	 * @return PublisherAPI
	 */
	protected function getApi($channelId)
	{
		if (!isset($this->apis[$channelId])) {
			$channel = $this->getService()->getChannelById($channelId);
			$this->apis[$channelId] = new PublisherAPI($channel->getApiKeyId(), $channel->getApiSecret(), 'https://news-api.apple.com');
		}

		return $this->apis[$channelId];
	}

	/**
	 * Returns the AppleNewsService instance
	 *
	 * @return AppleNewsService
	 */
	protected function getService()
	{
		return craft()->appleNews;
	}
}
