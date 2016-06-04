<?php
namespace Craft;

/**
 * Class AppleNewsController
 */
class AppleNewsController extends BaseController
{
	// Public Methods
	// =========================================================================

	/**
	 * Downloads a bundle for Apple News Preview.
	 */
	public function actionDownloadArticle()
	{
		$entry = $this->getEntry(true);
		$channelId = craft()->request->getRequiredParam('channelId');
		$channel = $this->getService()->getChannelById($channelId);

		if (!$channel->matchEntry($entry)){
			throw new Exception('This channel does not want anything to do with this entry.');
		}

		$article = $channel->createArticle($entry);

		// Prep the zip staging folder
		$zipDir = craft()->path->getTempPath().StringHelper::UUID();
		$zipContentDir = $zipDir.'/'.$entry->slug;
		IOHelper::createFolder($zipDir);
		IOHelper::createFolder($zipContentDir);

		// Create article.json
		$json = JsonHelper::encode($article->getContent());
		IOHelper::writeToFile($zipContentDir.'/article.json', $json);

		// Copy the files
		$files = $article->getFiles();
		if ($files) {
			foreach ($files as $uri => $path) {
				IOHelper::copyFile($path, $zipContentDir.'/'.$uri);
			}
		}

		$zipFile = $zipDir.'.zip';
		IOHelper::createFile($zipFile);

		Zip::add($zipFile, $zipDir, $zipDir);
		craft()->request->sendFile($zipFile, IOHelper::getFileContents($zipFile), ['filename' => $entry->slug.'.zip', 'forceDownload' => true], false);
		IOHelper::deleteFolder($zipDir);
		IOHelper::deleteFile($zipFile);
	}

	/**
	 * Returns the latest info about an entry's articles
	 */
	public function actionGetArticleInfo()
	{
		$entry = $this->getEntry();
		$channelId = craft()->request->getParam('channelId');

		$this->returnJson([
			'infos' => $this->getArticleInfo($entry, $channelId, true),
		]);
	}

	/**
	 * Posts an article to Apple News.
	 */
	public function actionPostArticle()
	{
		$entry = $this->getEntry();
		$channelId = craft()->request->getParam('channelId');
		$service = $this->getService();

		$service->postArticle($entry, $channelId);

		$this->returnJson([
			'success' => true,
			'infos' => $this->getArticleInfo($entry, $channelId),
		]);
	}

	// Protected Methods
	// =========================================================================

	/**
	 * @param bool $acceptRevision
	 *
	 * @return EntryModel
	 * @throws HttpException
	 */
	protected function getEntry($acceptRevision = false)
	{
		$entryId = craft()->request->getRequiredParam('entryId');
		$localeId = craft()->request->getRequiredParam('locale');

		if ($acceptRevision) {
			$versionId = craft()->request->getParam('versionId');
			$draftId   = craft()->request->getParam('draftId');
		} else {
			$versionId = $draftId = null;
		}

		if ($versionId) {
			$entry = craft()->entryRevisions->getVersionById($versionId);
		} elseif ($draftId) {
			$entry = craft()->entryRevisions->getDraftById($draftId);
		} else {
			$entry = craft()->entries->getEntryById($entryId, $localeId);
		}

		if (!$entry) {
			throw new HttpException(404);
		}

		// Make sure the user is allowed to edit entries in this section
		craft()->userSession->requirePermission('editEntries:'.$entry->sectionId);

		return $entry;
	}

	/**
	 * @param EntryModel $entry
	 * @param string     $channelId
	 * @param bool       $refresh
	 *
	 * @return \array[]
	 * @throws Exception
	 */
	protected function getArticleInfo(EntryModel $entry, $channelId, $refresh = false)
	{
		$infos = $this->getService()->getArticleInfo($entry, $channelId, true);

		// Add canPublish keys
		foreach ($infos as $channelId => $channelInfo) {
			$channel = $this->getService()->getChannelById($channelId);
			$infos[$channelId]['canPublish'] = $channel->canPublish($entry);
		}

		return $infos;
	}

	/**
	 * @return AppleNewsService
	 */
	protected function getService()
	{
		return craft()->appleNews;
	}
}
