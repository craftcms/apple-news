<?php

/**
 * @file
 * AppleNewsAPI PublisherAPI library.
 */

namespace ChapterThree\AppleNewsAPI;

/**
 * AppleNewsAPI PublisherAPI
 *
 * The PublisherAPI is a PHP library that allows you to publish content to Apple News.
 * You can also retrieve, update and delete articles you've already published,
 * and get basic information about your channel and sections.
 *
 * @package    ChapterThree\AppleNewsAPI\PublisherAPI
 * @subpackage ChapterThree\AppleNewsAPI\PublisherAPI\Base
 */
class PublisherAPI extends PublisherAPI\Curl {

  /** @var (const) PublisherAPI version */
  const VERSION = '0.3.8';

}
