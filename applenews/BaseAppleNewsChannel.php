<?php

use Craft\EntryModel;

/**
 * Class BaseAppleNewsChannel
 */
abstract class BaseAppleNewsChannel implements IAppleNewsChannel
{
	// Properties
	// =========================================================================

	/**
	 * @var string The channel ID
	 */
	protected $channelId;

	/**
	 * @var string The channel API key ID
	 */
	protected $apiKeyId;

	/**
	 * @var string The channel API shared secret
	 */
	protected $apiSecret;

	// Public Methods
	// =========================================================================

	/**
	 * @inheritdoc
	 */
	public function getChannelId()
	{
		return $this->channelId;
	}

	/**
	 * @inheritdoc
	 */
	public function getApiKeyId()
	{
		return $this->apiKeyId;
	}

	/**
	 * @inheritdoc
	 */
	public function getApiSecret()
	{
		return $this->apiSecret;
	}

	/**
	 * @inheritdoc
	 */
	public function matchEntry(EntryModel $entry)
	{
		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function canPublish(EntryModel $entry)
	{
		if ($entry->getStatus() != EntryModel::LIVE) {
			return false;
		}

		return true;
	}

	/**
	 * @inheritdoc
	 */
	public function createArticle(EntryModel $entry)
	{
		throw new Exception('createArticle not implemented');
	}
}
