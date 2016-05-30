<?php

/**
 * @file
 * An Apple News Document Portrait.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Portrait.
 */
class Portrait extends ScalableImage {

  /**
   * Implements __construct().
   *
   * @param string $url
   *   Role.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($url, $identifier = NULL) {
    parent::__construct('portrait', $url, $identifier);
  }

}
