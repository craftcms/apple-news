<?php

/**
 * @file
 * An Apple News Document Logo.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Logo.
 */
class Logo extends Image {

  /**
   * Implements __construct().
   *
   * @param string $url
   *   Image URL.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($url, $identifier = NULL) {
    parent::__construct('logo', $url, $identifier);
  }

}
