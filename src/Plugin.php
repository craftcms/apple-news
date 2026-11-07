<?php

declare(strict_types=1);

namespace CraftCms\AppleNews;

use CraftCms\AppleNews\Listeners\AddArticlePane;
use CraftCms\AppleNews\Listeners\AddPublishAction;
use CraftCms\AppleNews\Listeners\DeleteArticles;
use CraftCms\AppleNews\Listeners\QueueSavedEntry;
use CraftCms\AppleNews\Utilities\AppleNewsInfo;
use CraftCms\Cms\Element\Events\ElementActionsResolving;
use CraftCms\Cms\Element\Events\ElementDeleting;
use CraftCms\Cms\Element\Events\ElementEditorPayloadResolving;
use CraftCms\Cms\Element\Events\ElementSaved;
use CraftCms\Cms\Plugin\Plugin as BasePlugin;
use CraftCms\Cms\Plugin\PluginSettings;
use CraftCms\Cms\Utility\Utility;
use Override;

/**
 * @method Settings getSettings()
 *
 * @since 2.0
 */
class Plugin extends BasePlugin
{
    public string $minVersionRequired = '4.0.0';

    /** @var array<class-string, class-string|class-string[]> */
    protected array $events = [
        ElementSaved::class => QueueSavedEntry::class,
        ElementDeleting::class => DeleteArticles::class,
        ElementActionsResolving::class => AddPublishAction::class,
        ElementEditorPayloadResolving::class => AddArticlePane::class,
    ];

    /** @var array<class-string<Utility>> */
    protected array $utilities = [
        AppleNewsInfo::class,
    ];

    /**
     * The Apple News Channels panel in the entry editor.
     */
    protected array $vite = [
        'resources/js/cp.js',
    ];

    public function boot(): void
    {
        $this->app->singleton(Settings::class, fn() => $this->getSettings());
    }

    #[Override]
    protected static function createSettings(): ?PluginSettings
    {
        return new Settings();
    }
}
