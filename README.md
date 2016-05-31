# Apple News for Craft CMS

This plugin provides an Apple News integration for Craft CMS, making it possible to publish your content to iPhone and iPad owners around the world.


## Requirements

Apple News for Craft CMS requires Craft CMS 2 and PHP 5.4 or later.


## Before You Begin

You will need at least one active Apple News channel. Visit the [iCloud News Publisher](https://www.icloud.com/#newspublisher) website to create one. Note that new channels must go through a quick approval process before they can be used.


## Installation

To install Apple News for Craft CMS, follow these steps:

1.  Upload the `applenews` folder to your `craft/plugins` folder.
2.  Go to Settings > Plugins from your Craft control panel and install the Apple News plugin.


## Setup

Each Apple News channel needs a corresponding PHP class implementing the [IAppleNewsChannel](https://github.com/pixelandtonic/AppleNews/blob/master/applenews/IAppleNewsChannel.php) interface. These classes tell the plugin everything it needs to know to start posting articles to Apple News. You can place these classes within `craft/applenewschannels`. (Note that you should **not** give them a `Craft` namespace like you would for plugin classes.)

In addition to creating the Channel classes, you will also need to tell the plugin where to find them. You do that by creating a new file within craft/config/ called `applenews.php`. Give it the following contents:

```php
<?php

return [
    'channels' => [
        'applenewschannels.MyNewsChannel',
    ],
];
```

An example Channel class is proided at [applenewschannels/MyNewsChannel.php](https://github.com/pixelandtonic/AppleNews/blob/master/applenewschannels/MyNewsChannel.php), which will more or less work with the “News” section within the [Happy Lager demo site](https://github.com/pixelandtonic/HappyLager).


## Usage

Once your Channel classes are set up and included properly, you should be able to see a new “Apple News Channels” pane within your Edit Entry pages, for entries that have at least one Channel whose `matchEntry()` method returns `true`. Each channel will display an action menu beside it with the following options:

- **Copy share URL** – If the entry has been published to Apple News, this will present a prompt where you can copy the article’s share URL. If the URL is opened on an iOS device, it will launch the News app and bring you to the article.
- **Download for News Preview** – This will download the entry’s article.json (and supplimental files), which can be loaded into the [News Preview](https://developer.apple.com/news-preview/) app, to see exactly how your article will look on various iOS devices once published.

No specific user action is required to publish entries to Apple News. Each time an entry is saved, the plugin will determine if it should push the article to Apple News, depending on the Channel classes’ `matchEntry()` and `canPublish()` responses.


## Caveats

Please be aware of the following caveats:

- At this time there is no way to schedule an entry to be pushed to Apple News in the future, nor does Apple News support articles with publish dates set to the future. So if you save an entry with a Post Date set in the future, you will have to manually re-save the entry later on for it to actually get pushed to Apple News.
