<?php

/**
 * @file
 * An Apple News Document Quote.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Quote Component.
 */
class Quote extends Text {

  /**
   * Implements __construct().
   *
   * @param mixed $text
   *   Text.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($text, $identifier = NULL) {
    return parent::__construct('quote', $text, $identifier);
  }

}
