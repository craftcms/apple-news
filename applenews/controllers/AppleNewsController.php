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
	public function actionDownloadPreview()
	{
		$entryId = craft()->request->getRequiredParam('entryId');
		$localeId = craft()->request->getRequiredParam('locale');
		$entry = craft()->entries->getEntryById($entryId, $localeId);

		if (!$entry) {
			throw new HttpException(404);
		}


	}
}
