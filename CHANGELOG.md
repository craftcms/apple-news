# Release Notes for Apple News for Craft CMS

## Unreleased

### Fixed
- Fixed a bug where translation messages weren’t getting registered for JavaScript properly.

## 2.0.1 - 2021-04-21

### Changed
- `article.json` files downloaded from the Control Panel now contain pretty-formatted JSON.

### Fixed
- Fixed a PHP error that could occur when deleting an entry that didn’t have an Apple News article ID yet. ([#11](https://github.com/craftcms/apple-news/issues/11))

## 2.0.0 - 2019-09-20

### Added
- Added Craft 3 compatibility. See [Upgrading from Craft 2](https://github.com/craftcms/apple-news/blob/master/README.md#upgrading-from-craft-2) for upgrade instructions.

## 1.0.2 - 2017-12-18

### Changed
- The “Publishing articles to Apple News” task now logs a message before posting articles, making it easier to track down problem entries.
- Updated chapter-three/apple-news-api to 0.3.9.

### Fixed
- Fixed a bug where `AppleNewsHelper::html2Components()` could set the wrong default `role` property on components where a role wasn’t explicitly set.

## 1.0.1 - 2016-06-15

### Fixed
- Fixed a bug where `AppleNewsHelper::formatLanguage()` was not placing an underscore between the language and region codes.

## 1.0.0 - 2016-06-14

Initial release.
