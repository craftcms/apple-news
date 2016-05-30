<?php

/**
 * @file
 * An Apple News Document Title Component.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Title Component.
 */
class Title extends Text {

  /**
   * Implements __construct().
   *
   * @param mixed $text
   *   Text.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($text, $identifier = NULL) {
    return parent::__construct('title', $text, $identifier);
  }

}
