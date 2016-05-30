<?php

/**
 * @file
 * An Apple News Document ParallaxScaleHeader.
 */

namespace ChapterThree\AppleNewsAPI\Document\Animations\Scenes;

use ChapterThree\AppleNewsAPI\Document\Base;

/**
 * An Apple News Document ParallaxScaleHeader.
 *
 * @property $type
 */
class ParallaxScaleHeader extends Scene {

  /**
   * Implements __construct().
   */
  public function __construct() {
    parent::__construct('parallax_scale');
  }

}
