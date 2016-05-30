<?php

/**
 * @file
 * An Apple News Document BackgroundMotion.
 */

namespace ChapterThree\AppleNewsAPI\Document\Behaviors;

/**
 * An Apple News Document BackgroundMotion.
 *
 * @property $type
 */
class BackgroundMotion extends Behavior {

  /**
   * Implements __construct().
   */
  public function __construct() {
    return parent::__construct('background_motion');
  }

}
