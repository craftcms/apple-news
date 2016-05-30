<?php

/**
 * @file
 * An Apple News Document Illustrator.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Illustrator Component.
 */
class Illustrator extends Text {

  /**
   * Implements __construct().
   *
   * @param mixed $text
   *   Text.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($text, $identifier = NULL) {
    return parent::__construct('illustrator', $text, $identifier);
  }

}
