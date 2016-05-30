<?php

/**
 * @file
 * An Apple News Document Body.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Body Component.
 */
class Body extends Text {

  /**
   * Implements __construct().
   *
   * @param mixed $text
   *   Text.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($text, $identifier = NULL) {
    return parent::__construct('body', $text, $identifier);
  }

}
