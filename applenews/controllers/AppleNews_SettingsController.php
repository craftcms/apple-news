<?php
namespace Craft;

/**
 * Class AppleNews_SettingsController
 *
 * @license https://github.com/pixelandtonic/AppleNews/blob/master/LICENSE
 */
class AppleNews_SettingsController extends BaseController
{
    // Public Methods
    // =========================================================================

    /**
     * Initializes the controller
     */
    public function init()
    {
        // All settings actions requrie an admin account
        $this->requireAdmin();
    }

    /**
     * Settnigs index
     */
    public function actionIndex()
    {
        // Load the channel info
        $channels = $this->getService()->getChannels();
        $channelNames = [];
        $sections = [];
        $api = $this->getApiService();

        foreach ($channels as $channelId => $channel) {
            $channelNames[$channelId] = $this->getService()->getChannelName($channelId);
            $response = $api->listSections($channelId);
            $sections[$channelId] = $response->data;
        }

        $this->renderTemplate('applenews/_index', [
            'channels' => $channels,
            'channelNames' => $channelNames,
            'sections' => $sections,
        ]);
    }

    /**
     * Returns the latest info about an entry's articles
     */
    public function actionGetArticleInfo()
    {
        $foo = 'foo';
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

        $service->queueArticle($entry, $channelId);

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
            $draftId = craft()->request->getParam('draftId');
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

    /**
     * @return AppleNews_ApiService
     */
    protected function getApiService()
    {
        return craft()->appleNews_api;
    }
}
