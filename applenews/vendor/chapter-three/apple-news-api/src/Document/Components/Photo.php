<?php

/**
 * @file
 * An Apple News Document Photo.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Photo.
 */
class Photo extends ScalableImage {

  /**
   * Implements __construct().
   *
   * @param string $url
   *   Role.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($url, $identifier = NULL) {
    parent::__construct('photo', $url, $identifier);
  }

}
