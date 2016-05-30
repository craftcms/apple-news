<?php

/**
 * @file
 * An Apple News Document Figure.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Figure.
 */
class Figure extends ScalableImage {

  /**
   * Implements __construct().
   *
   * @param string $url
   *   Role.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($url, $identifier = NULL) {
    parent::__construct('figure', $url, $identifier);
  }

}
