<?php

namespace craft\applenews\assets;

use craft\web\AssetBundle;
use craft\web\View;

class AppleNewsAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = __DIR__ . '/dist';

    /**
     * @inheritdoc
     */
    public $css = [
        'css/edit-entry.css',
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'js/ArticlePane.js',
    ];

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);

        if ($view instanceof View) {
            $view->registerTranslations('app', [
                'A previous version of the article has been published, and an update is currently in the queue to be published.',
                'A previous version of the article is visible in the News app, and an update is currently in processing.',
                'A previous version of the article is visible in the News app, but an update failed during processing.',
                'Copy share URL',
                'Download for News Preview',
                'Publish to Apple News',
                'The article failed during processing and is not visible in the News app.',
                'The article has been published and is going through processing.',
                'The article has been published, finished processing, and is visible in the News app.',
                'The article has not been published yet.',
                'The article is in the queue to be published.',
                'The article was previously visible in the News app, but was taken down.',
                '{ctrl}C to copy.',
            ]);
        }
    }
}
