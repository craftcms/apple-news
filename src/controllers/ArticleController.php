<?php

namespace craft\applenews\controllers;

use Craft;
use craft\applenews\Plugin;
use craft\elements\Entry;
use craft\helpers\FileHelper;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use craft\web\Controller;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use ZipArchive;

/**
 * Article controller
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
class ArticleController extends Controller
{
    /**
     * Downloads a bundle for Apple News Preview.
     *
     * @return Response
     * @throws BadRequestHttpException
     */
    public function actionDownload(): Response
    {
        $entry = $this->entry(true);
        $channelId = Craft::$app->getRequest()->getRequiredParam('channelId');
        $channel = Plugin::getInstance()->channelManager->getChannelById($channelId);

        if (!$channel->matchEntry($entry)) {
            throw new BadRequestHttpException('This channel does not want anything to do with this entry.');
        }

        $article = $channel->createArticle($entry);

        // Prep the zip staging folder
        $zipDir = Craft::$app->getPath()->getTempPath() . '/apple-news-articles';
        FileHelper::createDirectory($zipDir);
        $zipPath = "{$zipDir}/{$entry->slug}-" . StringHelper::UUID() . '.zip';

        // Create the zip
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            throw new Exception('Cannot create zip at ' . $zipPath);
        }

        // Add article.json
        $zip->addFromString('article.json', Json::encode($article->getContent(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        // Add the article files
        foreach ($article->getFiles() as $uri => $path) {
            // Preserve the directory structure within the templates folder
            $zip->addFile($path, $uri);
        }

        // Close and send the zip
        $zip->close();
        $response = Craft::$app->getResponse()->sendFile($zipPath, "{$entry->slug}.zip");
        FileHelper::unlink($zipPath);
        return $response;
    }

    /**
     * Returns the latest info about an entry's articles
     *
     * @return Response
     */
    public function actionGetInfo(): Response
    {
        $entry = $this->entry();
        $channelId = Craft::$app->getRequest()->getParam('channelId');

        return $this->asJson([
            'infos' => $this->getArticleInfo($entry, $channelId, true),
        ]);
    }

    /**
     * Publishes an entry to Apple News.
     *
     * @return Response
     */
    public function actionPublish(): Response
    {
        $entry = $this->entry();
        $channelId = Craft::$app->getRequest()->getParam('channelId');
        Plugin::getInstance()->articleManager->queue($entry, $channelId);

        return $this->asJson([
            'success' => true,
            'infos' => $this->getArticleInfo($entry, $channelId),
        ]);
    }

    /**
     * @param bool $acceptRevision
     * @return Entry
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    protected function entry(bool $acceptRevision = false): Entry
    {
        $request = Craft::$app->getRequest();
        $entryId = $request->getRequiredParam('entryId');
        $siteId = $request->getRequiredParam('siteId');
        $draftId = $acceptRevision ? $request->getParam('draftId') : null;
        $revisionId = $acceptRevision ? $request->getParam('revisionId') : null;

        $query = Entry::find()
            ->siteId($siteId)
            ->anyStatus();

        if ($draftId) {
            $query->draftId($draftId);
        } else if ($revisionId) {
            $query->revisionId($revisionId);
        } else {
            $query->id($entryId);
        }

        /** @var Entry $entry */
        $entry = $query->one();

        /** @phpstan-ignore-next-line */
        if (!$entry) {
            throw new BadRequestHttpException();
        }

        // Make sure the user is allowed to edit entries in this section
        $this->requirePermission("editEntries:{$entry->section->uid}");

        return $entry;
    }

    /**
     * @param Entry $entry
     * @param string|string[]|null $channelId
     * @param bool $refresh
     * @return array
     */
    protected function getArticleInfo(Entry $entry, $channelId, bool $refresh = false): array
    {
        $infos = Plugin::getInstance()->articleManager->getArticleInfo($entry, $channelId, $refresh);

        // Add canPublish keys
        $channelManager = Plugin::getInstance()->channelManager;
        foreach ($infos as $channelId => $channelInfo) {
            $channel = $channelManager->getChannelById($channelId);
            $infos[$channelId]['canPublish'] = $channel->canPublish($entry);
        }

        return $infos;
    }
}
