<?php

/**
 * @file
 * An Apple News Document Springy.
 */

namespace ChapterThree\AppleNewsAPI\Document\Behaviors;

/**
 * An Apple News Document Springy.
 *
 * @property $type
 */
class Springy extends Behavior {

  /**
   * Implements __construct().
   */
  public function __construct() {
    return parent::__construct('springy');
  }

}
