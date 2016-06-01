<?php
namespace Craft;

class AppleNewsPlugin extends BasePlugin
{
	// Public Methods
	// =========================================================================

	/**
	 * @return mixed
	 */
	public function getName()
	{
		return Craft::t('Apple News');
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return '1.0';
	}

	public function getSchemaVersion()
	{
		return '1.0.0';
	}

	/**
	 * @return string
	 */
	public function getDeveloper()
	{
		return 'Pixel & Tonic';
	}

	/**
	 * @return string
	 */
	public function getDeveloperUrl()
	{
		return 'http://pixelandtonic.com';
	}

	/**
	 * @return string
	 */
	public function getPluginUrl()
	{
		return 'https://github.com/pixelandtonic/AppleNews';
	}

	/**
	 * @return string
	 */
	public function getDocumentationUrl()
	{
		return $this->getPluginUrl().'/blob/master/README.md';
	}

	/**
	 * @return string
	 */
	public function getReleaseFeedUrl()
	{
		return 'https://raw.githubusercontent.com/pixelandtonic/AppleNews/master/releases.json';
	}

	/**
	 * @return void
	 */
	public function init()
	{
		craft()->on('entries.saveEntry', [$this, 'handleEntrySave']);
		//craft()->on('entries.deleteEntry', [$this, 'handleEntryDelete']);

		craft()->templates->hook('cp.entries.edit.right-pane', [$this, 'addEditEntryPagePane']);
	}

	/**
	 * @param Event $event
	 *
	 * @return void
	 */
	public function handleEntrySave(Event $event)
	{
		/** @var EntryModel $entry */
		$entry = $event->params['entry'];

		$this->getService()->postArticle($entry);
	}

	/**
	 * @param Event $event
	 *
	 * @return void
	 */
	public function handleEntryDelete(Event $event)
	{
		/** @var EntryModel $entry */
		$entry = $event->params['entry'];

		$this->getService()->deleteArticle($entry);
	}

	/**
	 * @param array &$context
	 *
	 * @return string
	 */
	public function addEditEntryPagePane(&$context)
	{
		/** @var EntryModel $entry */
		$entry = $context['entry'];

		if (!$entry->id) {
			return '';
		}

		// Find any channels that match this entry
		/** @var IAppleNewsChannel[] $channels */
		$channels = [];
		foreach ($this->getService()->getChannels() as $channel) {
			if ($channel->matchEntry($entry)) {
				$channels[$channel->getChannelId()] = $channel;
			}
		}

		if (!$channels) {
			return '';
		}

		// Get any existing records for these channels.
		$records = AppleNews_ArticleRecord::model()->findAllByAttributes([
			'entryId' => $entry->id,
			'channelId' => array_keys($channels),
		]);
		$indexedRecords = [];
		foreach ($records as $record) {
			$indexedRecords[$record->channelId] = $record;
		}

		$html = '<div class="pane lightpane meta" id="apple-news-pane">' .
			'<h4 class="heading">'.Craft::t('Apple News Channels').'</h4>';

		foreach ($channels as $channelId => $channel) {
			$html .= '<div class="data" data-channel-id="'.$channelId.'">' .
				'<h5 class="heading">'.$this->getService()->getChannelName($channelId).'</h5>' .
				'<div class="value"><a class="btn menubtn" data-icon="settings" title="'.Craft::t('Actions').'"></a>' .
				'<div class="menu">' .
				'<ul>';

			if (isset($indexedRecords[$channelId])) {
				$shareUrl = $indexedRecords[$channelId]->shareUrl;
				$html .= '<li><a data-action="copy-share-url" data-url="'.$shareUrl.'">'.Craft::t('Copy share URL').'</a></li>';
			}

			$downloadUrl = UrlHelper::getActionUrl('appleNews/downloadArticle', [
				'entryId' => $entry->id,
				'locale' => $entry->locale,
				'channelId' => $channelId,
			]);

			$html .= '<li><a href="'.$downloadUrl.'" target="_blank">'.Craft::t('Download for News Preview').'</a></li>' .
				'</ul>' .
				'</div>' .
				'</div>' .
				'</div>';
		};

		$html .= '</div>';

		craft()->templates->includeCssResource('appleNews/css/edit-entry.css');
		craft()->templates->includeJsResource('appleNews/js/edit-entry.js');

		return $html;
	}

	// Protected Methods
	// =========================================================================

	/**
	 * @return AppleNewsService
	 */
	protected function getService()
	{
		return craft()->appleNews;
	}
}
