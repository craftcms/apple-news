<?php

namespace craft\applenews;

use ChapterThree\AppleNewsAPI\PublisherAPI;
use stdClass;
use yii\base\Component;

/**
 * API
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
class Api extends Component
{
    /**
     * Returns information about a channel.
     *
     * @param string $channelId
     * @return stdClass
     */
    public function channel($channelId): stdClass
    {
        return $this->get($channelId, '/channels/{channel_id}', [
            'channel_id' => $channelId,
        ]);
    }

    /**
     * Returns information about a channel’s sections.
     *
     * @param string $channelId
     * @return stdClass
     */
    public function sections(string $channelId): stdClass
    {
        return $this->get($channelId, '/channels/{channel_id}/sections', [
            'channel_id' => $channelId,
        ]);
    }

    /**
     * Returns information about a section.
     *
     * @param string $channelId
     * @param string $sectionId
     * @return stdClass
     */
    public function section(string $channelId, string $sectionId): stdClass
    {
        return $this->get($channelId, '/sections/{section_id}', [
            'section_id' => $sectionId,
        ]);
    }

    /**
     * Returns information about an article.
     *
     * @param string $channelId
     * @param string $articleId
     * @return stdClass
     */
    public function article(string $channelId, string $articleId): stdClass
    {
        return $this->get($channelId, '/articles/{article_id}', ['article_id' => $articleId]);
    }

    /**
     * Searches for articles in a channel.
     *
     * @param string $channelId
     * @param array $params
     * @return stdClass[]
     */
    public function search(string $channelId, array $params = []): array
    {
        return $this->get($channelId, '/channels/{channel_id}/articles', [
            'channel_id' => $channelId,
        ], $params);
    }

    /**
     * Creates a new article.
     *
     * @param string $channelId
     * @param array $data
     * @return stdClass
     */
    public function createArticle(string $channelId, array $data): stdClass
    {
        return $this->post($channelId, '/channels/{channel_id}/articles', [
            'channel_id' => $channelId,
        ], $data);
    }

    /**
     * Updates an article.
     *
     * @param string $channelId
     * @param string $articleId
     * @param array $data
     * @return stdClass
     */
    public function updateArticle(string $channelId, string $articleId, array $data): stdClass
    {
        return $this->post($channelId, '/articles/{article_id}', [
            'article_id' => $articleId,
        ], $data);
    }

    /**
     * Deletes an article.
     *
     * @param string $channelId
     * @param string $articleId
     * @return stdClass
     */
    public function deleteArticle(string $channelId, string $articleId): stdClass
    {
        return $this->delete($channelId, '/articles/{article_id}', [
            'article_id' => $articleId,
        ]);
    }

    /**
     * Sends a GET request to the Apple News API.
     *
     * @param string $channelId
     * @param string $path
     * @param array $pathArgs
     * @param array $data
     * @return mixed
     */
    protected function get(string $channelId, string $path, array $pathArgs = [], array $data = [])
    {
        return $this->api($channelId)->get($path, $pathArgs, $data);
    }

    /**
     * Sends a POST request to the Apple News API.
     *
     * @param string $channelId
     * @param string $path
     * @param array $pathArgs
     * @param array $data
     * @return mixed
     */
    protected function post(string $channelId, string $path, array $pathArgs = [], array $data = [])
    {
        return $this->api($channelId)->post($path, $pathArgs, $data);
    }

    /**
     * Sends a DELETE request to the Apple News API.
     *
     * @param string $channelId
     * @param string $path
     * @param array $pathArgs
     * @param array $data
     * @return mixed
     */
    protected function delete(string $channelId, string $path, array $pathArgs = [], array $data = [])
    {
        return $this->api($channelId)->delete($path, $pathArgs, $data);
    }

    /**
     * Returns a publisher API configured for a given channel ID.
     *
     * @param string $channelId
     * @return PublisherAPI
     */
    protected function api(string $channelId): PublisherAPI
    {
        $channel = Plugin::getInstance()->channelManager->getChannelById($channelId);
        return new PublisherAPI($channel->getApiKeyId(), $channel->getApiSecret(), 'https://news-api.apple.com');
    }
}
