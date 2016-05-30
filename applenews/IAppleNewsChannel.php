<?php

use Craft\EntryModel;

/**
 * Interface IAppleNewsChannel
 */
interface IAppleNewsChannel
{
	// Public Methods
	// =========================================================================

	/**
	 * @return string The channel ID
	 */
	public function getChannelId();

	/**
	 * @return string The channel API key ID
	 */
	public function getApiKeyId();

	/**
	 * @return string The channel API shared secret
	 */
	public function getApiSecret();

	/**
	 * Determines whether a given entry should be included in the News channel.
	 *
	 * @param EntryModel $entry The entry
	 *
	 * @return bool Whether the entry should be included in the News channel
	 */
	public function matchEntry(EntryModel $entry);

	/**
	 * Determines whether a given entry should be published to Apple News in its current state.
	 *
	 * @param EntryModel $entry The entry
	 *
	 * @return bool Whether the entry should be published to Apple News
	 */
	public function canPublish(EntryModel $entry);

	/**
	 * Creates an {@link IAppleNewsArticle} for the given entry
	 *
	 * @param EntryModel $entry The entry
	 *
	 * @return IAppleNewsArticle The article that represents the entry
	 */
	public function createArticle(EntryModel $entry);
}
