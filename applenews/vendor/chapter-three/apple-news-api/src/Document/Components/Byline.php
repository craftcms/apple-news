<?php

/**
 * @file
 * An Apple News Document Byline.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Byline Component.
 */
class Byline extends Text {

  /**
   * Implements __construct().
   *
   * @param mixed $text
   *   Text.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($text, $identifier = NULL) {
    return parent::__construct('byline', $text, $identifier);
  }

}
