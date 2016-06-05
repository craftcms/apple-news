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
		craft()->on('entries.beforeDeleteEntry', [$this, 'handleEntryDelete']);

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

		// Make sure it's not a revision
		if ($entry instanceof EntryVersionModel || $entry instanceof EntryDraftModel) {
			return;
		}

		// Queue it up to be posted to Apple News
		$this->getService()->queueArticle($entry);
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

		$isVersion = ($entry instanceof EntryVersionModel);
		$isDraft = ($entry instanceof EntryDraftModel);

		// Get any existing records for these channels.
		$infos = $this->getService()->getArticleInfo($entry, array_keys($channels));

		$html = '<div class="pane lightpane meta" id="apple-news-pane">' .
			'<h4 class="heading">'.Craft::t('Apple News Channels').'</h4>' .
			'<div class="spinner hidden"></div>';

		foreach ($channels as $channelId => $channel) {
			$state = isset($infos[$channelId]) ? $infos[$channelId]['state'] : null;
			switch ($state) {
				case 'QUEUED':
					$statusColor = 'grey';
					$statusMessage = Craft::t('The article is in the queue to be published.');
					break;
				case 'QUEUED_UPDATE':
					$statusColor = 'grey';
					$statusMessage = Craft::t('A previous version of the article has been published, and an update is currently in the queue to be published.');
					break;
				case 'PROCESSING':
					$statusColor = 'orange';
					$statusMessage = Craft::t('The article has been published and is going through processing.');
					break;
				case 'PROCESSING_UPDATE':
					$statusColor = 'orange';
					$statusMessage = Craft::t('A previous version of the article is visible in the News app, and an update is currently in processing.');
					break;
				case 'LIVE':
					$statusColor = 'green';
					$statusMessage = Craft::t('The article has been published, finished processing, and is visible in the News app.');
					break;
				case 'FAILED_PROCESSING':
					$statusColor = 'red';
					$statusMessage = Craft::t('The article failed during processing and is not visible in the News app.');
					break;
				case 'FAILED_PROCESSING_UPDATE':
					$statusColor = 'red';
					$statusMessage = Craft::t('A previous version of the article is visible in the News app, but an update failed during processing.');
					break;
				case 'TAKEN_DOWN':
					$statusColor = null;
					$statusMessage = Craft::t('The article was previously visible in the News app, but was taken down.');
					break;
				default:
					$statusColor = null;
					$statusMessage = Craft::t('The article has not been published yet.');
			}

			$html .= '<div class="data" data-channel-id="'.$channelId.'">' .
				'<h5 class="heading">' .
					"<div class=\"status {$statusColor}\" title=\"{$statusMessage}\"></div>" .
					$this->getService()->getChannelName($channelId) .
				'</h5>' .
				'<div class="value"><a class="btn menubtn" data-icon="settings" title="'.Craft::t('Actions').'"></a>' .
				'<div class="menu">' .
				'<ul>';

			if (in_array($state, ['QUEUED_UPDATE', 'PROCESSING', 'PROCESSING_UPDATE', 'LIVE'])) {
				$shareUrl = $infos[$channelId]['shareUrl'];
				$html .= '<li><a data-action="copy-share-url" data-url="'.$shareUrl.'">'.Craft::t('Copy share URL').'</a></li>';
			}

			if (!in_array($state, ['QUEUED', 'QUEUED_UPDATE']) && !$isVersion && !$isDraft && $channel->canPublish($entry)) {
				$html .= '<li><a data-action="post-article">'.Craft::t('Post to Apple News').'</a></li>';
			} else {
				// TODO: preview support that ignores canPublish()
				//$html .= '<li><a data-action="post-preview">'.Craft::t('Post preview to Apple News').'</a></li>';
			}

			$downloadUrlParams = [
				'entryId' => $entry->id,
				'locale' => $entry->locale,
				'channelId' => $channelId,
			];

			if ($isVersion) {
				$downloadUrlParams['versionId'] = $entry->versionId;
			} else if ($isDraft) {
				$downloadUrlParams['draftId'] = $entry->draftId;
			}

			$downloadUrl = UrlHelper::getActionUrl('appleNews/downloadArticle', $downloadUrlParams);

			$html .= '<li><a href="'.$downloadUrl.'" target="_blank">'.Craft::t('Download for News Preview').'</a></li>' .
				'</ul>' .
				'</div>' .
				'</div>' .
				'</div>';
		};

		$html .= '</div>';

		craft()->templates->includeCssResource('appleNews/css/edit-entry.css');
		craft()->templates->includeJsResource('appleNews/js/ArticlePane.js');

		$infosJs = JsonHelper::encode($infos);
		$versionIdJs = $isVersion ? $entry->versionId : 'null';
		$draftIdJs = $isDraft ? $entry->draftId : 'null';

		$js = <<<EOT
Garnish.\$doc.ready(function() {
	new Craft.AppleNews.ArticlePane(
		{$entry->id},
		'{$entry->locale}',
		{$versionIdJs},
		{$draftIdJs},
		{$infosJs});
});
EOT;
		craft()->templates->includeJs($js);

		return $html;
	}

	/**
	 * Adds new bulk actions to the Entries index page.
	 *
	 * @param string $source The currently selected source
	 *
	 * @return array The bulk actions
	 */
	public function addEntryActions($source)
	{
		$actions = [];

		// Post Articles action
		$canPostArticles = false;
		$userSessionService = craft()->userSession;

		if ($userSessionService->isAdmin()) {
			$canPostArticles = true;
		} else if (preg_match('/^section:(\d+)$/', $source, $matches)) {
			if ($userSessionService->checkPermission('publishEntries:'.$matches[1])) {
				$canPostArticles = true;
			}
		}

		if ($canPostArticles) {
			$actions[] = 'AppleNews_PostArticles';
		}

		return $actions;
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
