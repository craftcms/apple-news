<?php

/**
 * @file
 * An Apple News Document Music.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Music.
 */
class Music extends Audio {

  /**
   * Implements __construct().
   *
   * @param mixed $url
   *   URL.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($url, $identifier = NULL) {
    parent::__construct('music', $url, $identifier);
  }

}
