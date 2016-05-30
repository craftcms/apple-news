<?php

/**
 * @file
 * An Apple News Document GradientFill.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles\Fills\Gradients;

use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document\Styles\Fills;

/**
 * An Apple News Document GradientFill.
 */
abstract class GradientFill extends Fills\Fill {

  protected $colorStops;

  /**
   * Implements __construct().
   *
   * @param string $type
   *   The type of gradient; e.g., linear_gradient.
   * @param array|\ChapterThree\AppleNewsAPI\Document\Styles\Fills\Gradients\ColorStop $color_stops
   *   URL.
   */
  public function __construct($type, array $color_stops) {
    parent::__construct($type);
    $this->setColorStops($color_stops);
  }

  /**
   * Getter for url.
   */
  public function getColorStops() {
    return $this->colorStops;
  }

  /**
   * Setter for url.
   *
   * @param array|\ChapterThree\AppleNewsAPI\Document\Styles\Fills\Gradients\ColorStop $items
   *   An array of color stops. Each stop sets a color and percentage.
   *
   * @return $this
   */
  public function setColorStops(array $items) {
    if (isset($items[0]) &&
        is_object($items[0]) &&
        !$items[0] instanceof Fills\Gradients\ColorStop
    ) {
      $this->triggerError('Object not of type Gradients\ColorStop');
    }
    else {
      $this->colorStops = $items;
    }
    return $this;
  }

}
