<?php

namespace craft\applenews;

use Craft;
use craft\applenews\assets\AppleNewsAsset;
use craft\applenews\elementactions\PublishArticles;
use craft\applenews\utilities\AppleNewsInfo;
use craft\base\Element;
use craft\base\Model;
use craft\elements\Entry;
use craft\events\DefineHtmlEvent;
use craft\events\ModelEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterElementActionsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\helpers\ElementHelper;
use craft\helpers\Json;
use craft\helpers\UrlHelper;
use craft\services\Utilities;
use craft\web\UrlManager;
use craft\web\View;
use yii\base\Event;
use yii\base\InvalidConfigException;

/**
 * Apple News plugin.
 *
 * @property ArticleManager $articleManager
 * @property Api $api
 * @property ChannelManager $channelManager
 * @property Settings $settings
 * @method Settings getSettings()
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
class Plugin extends \craft\base\Plugin
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->set('api', Api::class);
        $this->set('articleManager', ArticleManager::class);
        $this->set('channelManager', ChannelManager::class);

        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, [$this, 'registerUrlRules']);

        if ($this->getSettings()->autoPublishOnSave) {
            Event::on(Entry::class, Element::EVENT_AFTER_SAVE, function(ModelEvent $e) {
                /** @var Entry $entry */
                $entry = $e->sender;
                if (!ElementHelper::isDraftOrRevision($entry)) {
                    $this->articleManager->queue($entry);
                }
            });
        }

        Event::on(Entry::class, Element::EVENT_BEFORE_DELETE, function(ModelEvent $e) {
            /** @var Entry $entry */
            $entry = $e->sender;
            $this->articleManager->delete($entry);
        });

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            Event::on(Entry::class, Entry::EVENT_DEFINE_SIDEBAR_HTML, [$this, 'addEditEntryPagePane']);

            Event::on(Entry::class, Element::EVENT_REGISTER_ACTIONS, function(RegisterElementActionsEvent $e) {
                $userSession = Craft::$app->getUser();
                if (
                    $userSession->getIsAdmin() ||
                    (
                        preg_match('/^section:(.+)/', $e->source, $matches) &&
                        $userSession->checkPermission("publishEntries:$matches[1]")
                    )
                ) {
                    $e->actions[] = PublishArticles::class;
                }
            });

            Event::on(Utilities::class, Utilities::EVENT_REGISTER_UTILITY_TYPES, function(RegisterComponentTypesEvent $e) {
                $e->types[] = AppleNewsInfo::class;
            });
        }
    }

    /**
     * Registers the site URL rules.
     *
     * @param RegisterUrlRulesEvent $event
     */
    public function registerUrlRules(RegisterUrlRulesEvent $event): void
    {
        $event->rules['apple-news'] = 'apple-news/settings';
    }

    /**
     * @param DefineHtmlEvent $event
     * @throws InvalidConfigException
     */
    public function addEditEntryPagePane(DefineHtmlEvent $event): void
    {
        /** @var Entry $entry */
        $entry = $event->sender;

        if ($entry->getIsUnpublishedDraft()) {
            return;
        }

        $channelManager = $this->channelManager;

        // Find any channels that match this entry
        /** @var ChannelInterface[] $channels */
        $channels = [];
        foreach ($channelManager->getChannels() as $channel) {
            if ($channel->matchEntry($entry)) {
                $channels[$channel->getChannelId()] = $channel;
            }
        }

        if (!$channels) {
            return;
        }

        $isDraft = $entry->getIsDraft();
        $isRevision = $entry->getIsRevision();

        // Get any existing records for these channels.
        $infos = $this->articleManager->getArticleInfo($entry, array_keys($channels));

        $html = '<fieldset>' .
            '<legend class="h6">' . Craft::t('apple-news', 'Apple News Channels') . '</legend>' .
            '<div class="meta" id="apple-news-pane">' .
            '<div class="spinner hidden"></div>';

        foreach ($channels as $channelId => $channel) {
            $state = isset($infos[$channelId]) ? $infos[$channelId]['state'] : null;
            switch ($state) {
                case ArticleManager::STATE_QUEUED:
                    $statusColor = 'grey';
                    $statusMessage = Craft::t('apple-news', 'The article is in the queue to be published.');
                    break;
                case ArticleManager::STATE_QUEUED_UPDATE:
                    $statusColor = 'grey';
                    $statusMessage = Craft::t('apple-news', 'A previous version of the article has been published, and an update is currently in the queue to be published.');
                    break;
                case ArticleManager::STATE_PROCESSING:
                    $statusColor = 'orange';
                    $statusMessage = Craft::t('apple-news', 'The article has been published and is going through processing.');
                    break;
                case ArticleManager::STATE_PROCESSING_UPDATE:
                    $statusColor = 'orange';
                    $statusMessage = Craft::t('apple-news', 'A previous version of the article is visible in the News app, and an update is currently in processing.');
                    break;
                case ArticleManager::STATE_LIVE:
                    $statusColor = 'green';
                    $statusMessage = Craft::t('apple-news', 'The article has been published, finished processing, and is visible in the News app.');
                    break;
                case ArticleManager::STATE_FAILED_PROCESSING:
                    $statusColor = 'red';
                    $statusMessage = Craft::t('apple-news', 'The article failed during processing and is not visible in the News app.');
                    break;
                case ArticleManager::STATE_FAILED_PROCESSING_UPDATE:
                    $statusColor = 'red';
                    $statusMessage = Craft::t('apple-news', 'A previous version of the article is visible in the News app, but an update failed during processing.');
                    break;
                case ArticleManager::STATE_TAKEN_DOWN:
                    $statusColor = null;
                    $statusMessage = Craft::t('apple-news', 'The article was previously visible in the News app, but was taken down.');
                    break;
                default:
                    $statusColor = null;
                    $statusMessage = Craft::t('apple-news', 'The article has not been published yet.');
            }

            $html .= '<div class="data" data-channel-id="' . $channelId . '">' .
                '<h5 class="heading">' .
                "<div class=\"status $statusColor\" title=\"$statusMessage\"></div>" .
                $channelManager->getChannelName($channelId) .
                '</h5>' .
                '<div class="value"><a class="btn menubtn" data-icon="settings" title="' . Craft::t('apple-news', 'Actions') . '"></a>' .
                '<div class="menu">' .
                '<ul>';

            if (in_array($state, [
                ArticleManager::STATE_QUEUED_UPDATE,
                ArticleManager::STATE_PROCESSING,
                ArticleManager::STATE_PROCESSING_UPDATE,
                ArticleManager::STATE_LIVE,
            ])) {
                $shareUrl = $infos[$channelId]['shareUrl'];
                $html .= '<li><a data-action="copy-share-url" data-url="' . $shareUrl . '">' . Craft::t('apple-news', 'Copy share URL') . '</a></li>';
            }

            if (!$isRevision && !$isDraft && !in_array($state, [
                    ArticleManager::STATE_QUEUED,
                    ArticleManager::STATE_QUEUED_UPDATE,
                ]) && $channel->canPublish($entry)
            ) {
                $html .= '<li><a data-action="publish-article">' . Craft::t('apple-news', 'Publish to Apple News') . '</a></li>';
            }

            $downloadUrlParams = [
                'entryId' => $entry->id,
                'siteId' => $entry->siteId,
                'channelId' => $channelId,
            ];

            if ($isDraft) {
                $downloadUrlParams['draftId'] = $entry->draftId;
            } elseif ($isRevision) {
                $downloadUrlParams['revisionId'] = $entry->revisionId;
            }

            $downloadUrl = UrlHelper::actionUrl('apple-news/article/download', $downloadUrlParams);

            $html .= '<li><a href="' . $downloadUrl . '" target="_blank">' . Craft::t('apple-news', 'Download for News Preview') . '</a></li>' .
                '</ul>' .
                '</div>' .
                '</div>' .
                '</div>';
        }

        $html .= '</div></fieldset>';

        $view = Craft::$app->getView();
        $view->registerAssetBundle(AppleNewsAsset::class);

        $infosJs = Json::encode($infos);
        $draftIdJs = Json::encode($entry->draftId);
        $revisionIdJs = Json::encode($entry->revisionId);

        $js = <<<EOT
new Craft.AppleNews.ArticlePane(
    $entry->id,
    $entry->siteId,
    $draftIdJs,
    $revisionIdJs,
    $infosJs
);
EOT;
        $view->registerJs($js, View::POS_READY);

        $event->html .= $html;
    }

    /**
     * @return Model|null
     */
    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }
}
