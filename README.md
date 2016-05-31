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
