# Release Notes for Apple News for Craft CMS

## Unreleased

- Apple News now requires Craft CMS 6.0 or later and PHP 8.5 or later, and runs natively on Craft 6 without the `craftcms/yii2-adapter` package.
- The plugin’s classes moved to the `CraftCms\AppleNews` namespace. See [UPGRADE.md](UPGRADE.md).
- The Apple News Channels pane is now a details panel in the entry editor.
- The “Publish to Apple News” bulk action requires the “Save entries” permission for the section.
- Plugin settings are now read from `config/craft/apple-news.php`.
- Article publishing now uses Laravel queue jobs (`CraftCms\AppleNews\Jobs\PublishArticleJob`).
- Added `CraftCms\AppleNews\Enums\ArticleState`, which replaces the `ArticleManager::STATE_*` constants.
- Fixed a bug where an article stayed queued if publishing it failed, or if the entry could no longer be published by the time the queue job ran. The article now goes back to its previous state.
- Fixed a bug where articles that Apple News marked as duplicates were shown as not published yet.
- Fixed a bug where the “Publish to Apple News” bulk action published entries that the user wasn’t allowed to save, such as other authors’ entries.
- Fixed a bug where an unknown channel ID sent to the `apple-news/article/*` actions caused a server error, and `publish` queued the entry for it anyway. The actions now return a validation error.
- Fixed a bug where `ArticleManager::queue()` queued entries for the given channels even if the channel didn’t match the entry or couldn’t publish it.
- Fixed a bug where HTML comments weren’t removed from HTML converted to Markdown or article components.

## 4.1.0 - 2026-03-27

- Fixed a bug where remote assets in an Apple News article might not download.
- Fixed a SQL error that could occur during installation.

## 4.0.0 - 2024-03-21

- Apple News now requires Craft CMS 5.0.0-beta.1 or later.