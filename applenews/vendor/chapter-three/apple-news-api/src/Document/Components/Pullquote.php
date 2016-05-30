<?php

/**
 * @file
 * An Apple News Document Pullquote.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Pullquote Component.
 */
class Pullquote extends Text {

  /**
   * Implements __construct().
   *
   * @param mixed $text
   *   Text.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($text, $identifier = NULL) {
    return parent::__construct('pullquote', $text, $identifier);
  }

}
