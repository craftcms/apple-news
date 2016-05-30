<?php

/**
 * @file
 * An Apple News Document Motion.
 */

namespace ChapterThree\AppleNewsAPI\Document\Behaviors;

/**
 * An Apple News Document Motion.
 *
 * @property $type
 */
class Motion extends Behavior {

  /**
   * Implements __construct().
   */
  public function __construct() {
    return parent::__construct('motion');
  }

}
