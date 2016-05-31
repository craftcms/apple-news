<?php
namespace Craft;

/**
 * Class AppleNewsController
 */
class AppleNewsController extends BaseController
{
	// Public Methods
	// =========================================================================

	public function actionTest()
	{
		/** @var AppleNewsService $service */
		$service = craft()->appleNews;
		/** @var AppleNews_ApiService $apiService */
		$apiService = craft()->appleNews_api;
		$channels = $service->getChannels();

		foreach ($channels as $channel) {
			$response = $apiService->readChannel($channel->getChannelId());
			Craft::dd($response);
		}
	}

	/**
	 * Downloads a bundle for Apple News Preview.
	 */
	public function actionDownloadArticle()
	{
		$entryId = craft()->request->getRequiredParam('entryId');
		$localeId = craft()->request->getRequiredParam('locale');
		$channelId = craft()->request->getRequiredParam('channelId');
		$entry = craft()->entries->getEntryById($entryId, $localeId);

		if (!$entry) {
			throw new HttpException(404);
		}

		// Make sure the user is allowed to edit entries in this section
		craft()->userSession->requirePermission('editEntries:'.$entry->sectionId);
		
		$channel = $this->getService()->getChannelById($channelId);
		
		if (!$channel->matchEntry($entry)){
			throw new Exception('This channel does not want anything to do with this entry.');
		}
		
		$article = $channel->createArticle($entry);

		// Create the zip file
		$zipDir = craft()->path->getTempPath().StringHelper::UUID();
		IOHelper::createFolder($zipDir);
		IOHelper::writeToFile($zipDir.'/article.json', JsonHelper::encode($article->getContent()));

		$zipFile = $zipDir.'.zip';
		IOHelper::createFile($zipFile);

		Zip::add($zipFile, $zipDir, $zipDir);
		craft()->request->sendFile($zipFile, '', array('filename' => 'Article.zip', 'forceDownload' => true), false);
		IOHelper::deleteFolder($zipDir);
		IOHelper::deleteFile($zipFile);
	}

	/**
	 * @return AppleNewsService
	 */
	protected function getService()
	{
		return craft()->appleNews;
	}
}
