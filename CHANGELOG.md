# Release Notes for Apple News for Craft CMS

## Unreleased

- Added support for Craft CMS 6, when the `craftcms/yii2-adapter` package is installed.
- Fixed a bug where entries with published Apple News articles couldn’t be deleted.
- Fixed PHP 8.4 deprecation warnings.
- Fixed a bug where only admins could use the Apple News Channels pane and the “Publish to Apple News” bulk action. Publishing now requires permission to save the entry.

## 4.1.0 - 2026-03-27

- Fixed a bug where remote assets in an Apple News article might not download.
- Fixed a SQL error that could occur during installation.

## 4.0.0 - 2024-03-21

- Apple News now requires Craft CMS 5.0.0-beta.1 or later.