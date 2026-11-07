# Upgrading to Apple News 5.0 (Craft 6)

Apple News 5.0 is a native Craft 6 plugin. This guide lists what changed from 4.x and what to update in your project.

## Requirements

- Craft CMS 6.0 or later
- PHP 8.5 or later
- The `craftcms/yii2-adapter` package is not needed.

## Upgrade steps

1. Upgrade your project to Craft 6 first.
2. Update the plugin: `composer require craftcms/apple-news:^5.0`.
3. Move `config/apple-news.php` to `config/craft/apple-news.php`.
4. Update your channel and article classes as described below.
5. Make sure your project runs a queue worker (`php artisan queue:work`), since articles are published by queue jobs.

Your existing `applenews_articles` table and its records are kept.

## Breaking changes

### Classes and namespaces

| Before | After |
| --- | --- |
| `craft\applenews\Plugin` | `CraftCms\AppleNews\Plugin` |
| `craft\applenews\Api` | `CraftCms\AppleNews\Api` |
| `craft\applenews\Article` | `CraftCms\AppleNews\Article` |
| `craft\applenews\ArticleInterface` | `CraftCms\AppleNews\ArticleInterface` |
| `craft\applenews\ArticleManager` | `CraftCms\AppleNews\ArticleManager` |
| `craft\applenews\BaseChannel` | `CraftCms\AppleNews\BaseChannel` |
| `craft\applenews\ChannelInterface` | `CraftCms\AppleNews\ChannelInterface` |
| `craft\applenews\ChannelManager` | `CraftCms\AppleNews\ChannelManager` |
| `craft\applenews\Helper` | `CraftCms\AppleNews\Helper` |
| `craft\applenews\Settings` | `CraftCms\AppleNews\Settings` |
| `craft\applenews\controllers\ArticleController` | `CraftCms\AppleNews\Http\Controllers\ArticleController` |
| `craft\applenews\elementactions\PublishArticles` | `CraftCms\AppleNews\ElementActions\PublishArticles` |
| `craft\applenews\jobs\PublishArticle` | `CraftCms\AppleNews\Jobs\PublishArticleJob` |
| `craft\applenews\records\Article` | `CraftCms\AppleNews\Models\Article` (Eloquent) |
| `craft\applenews\utilities\AppleNewsInfo` | `CraftCms\AppleNews\Utilities\AppleNewsInfo` |
| `craft\applenews\assets\AppleNewsAsset` | Removed. The editor panel is registered from `resources/js/cp.js`. |

### Channel and article classes

Channel and article classes now receive Craft 6 classes. Update their imports:

```php
// Before
use craft\applenews\Article;
use craft\applenews\BaseChannel;
use craft\applenews\Helper;
use craft\elements\Asset;
use craft\elements\Entry;
use craft\helpers\DateTimeHelper;

// After
use CraftCms\AppleNews\Article;
use CraftCms\AppleNews\BaseChannel;
use CraftCms\AppleNews\Helper;
use CraftCms\Cms\Asset\Elements\Asset;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Support\DateTimeHelper;
```

- `Article` no longer extends `yii\base\BaseObject`. Its constructor still takes `(?Entry $entry, array $config = [])`, sets the config properties, then calls `init()`, so subclasses that override `init()` keep working.
- Channel configs are still arrays with a `class` key; the other keys set the channel’s public properties.
- `Helper::stripHtml()`, `html2Markdown()` and `html2Components()` take a string or any HTML field value (such as a CKEditor field’s), in place of `craft\htmlfield\HtmlFieldData`.
- `Helper::markdown2Components()` parses Markdown with Craft’s `original` flavor in place of `yii\helpers\Markdown`.
- Replace `Craft::$app->formatter` with `app(\CraftCms\Cms\Translation\Formatter::class)`, and `craft\helpers\App::env()` with `env()`.

### Services

The plugin no longer has `api`, `articleManager` or `channelManager` components. Resolve the services from the container:

```php
// Before
Plugin::getInstance()->articleManager->queue($entry);

// After
app(\CraftCms\AppleNews\ArticleManager::class)->queue($entry);
```

`ArticleManager::$generator` is now the `generator()` method. Methods that threw `yii\base\InvalidConfigException` for an invalid channel config now throw `UnexpectedValueException`.

### Apple News API client

`Api` now sends requests with Laravel’s HTTP client. The `chapter-three/apple-news-api` and `php-curl-class/php-curl-class` packages are no longer installed, so require them yourself if your code uses them.

- When Apple News returns an error, `Api` methods throw `Illuminate\Http\Client\RequestException`, whose response holds Apple’s error codes. Before, the library raised a PHP notice, which Craft turned into an `ErrorException`.
- All public `Api` methods return `stdClass`. `deleteArticle()` returns an empty object.
- If you extend `Api`, its protected methods changed: `get($channelId, $path, $query)`, `post($channelId, $path, $data)`, and `request($channelId)`, which returns the signed `PendingRequest`. Paths no longer take `{placeholder}` arguments. `api()` was removed.
- To fake the API in tests, use `Http::fake(['news-api.apple.com/*' => ...])`.

### Article states

The `ArticleManager::STATE_*` constants were removed. Use the `CraftCms\AppleNews\Enums\ArticleState` enum instead, which has the same values plus `DUPLICATE`:

```php
// Before
if ($record->state === ArticleManager::STATE_LIVE) {

// After
use CraftCms\AppleNews\Enums\ArticleState;

if ($record->state === ArticleState::Live) {
```

`Models\Article::$state` is cast to `ArticleState`. `ArticleManager::getArticleInfo()` still returns each state as a string, and now also returns `isQueued`, `isProcessing` and `isShareable` flags.

### Routes and action URLs

The `apple-news/article/download`, `apple-news/article/get-info` and `apple-news/article/publish` action URLs keep working. They now require a logged-in user, and authorize with Craft’s entry policies: `download` and `get-info` need permission to view the entry, and `publish` needs permission to save it.

### Permissions

The **Publish to Apple News** bulk action requires the “Save entries” permission (`saveEntries:<uid>`) for the selected section.

### Database and migrations

The 2.x upgrade migration (`m190917_173453_v2_upgrade`) was removed. Upgrade from Apple News 4.x.
