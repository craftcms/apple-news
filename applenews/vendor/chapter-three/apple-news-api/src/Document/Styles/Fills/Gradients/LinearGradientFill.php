<?php

/**
 * @file
 * An Apple News Document LinearGradientFill.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles\Fills\Gradients;

use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document\Styles\Fills;

/**
 * An Apple News Document LinearGradientFill.
 */
class LinearGradientFill extends GradientFill {

  protected $angle;

  /**
   * Implements __construct().
   *
   * @param array|\ChapterThree\AppleNewsAPI\Document\Styles\Fills\Gradients\ColorStop $colorStops
   *   URL.
   */
  public function __construct(array $colorStops) {
    parent::__construct('linear_gradient', $colorStops);
  }

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'angle'
    ));
  }

  /**
   * Getter for angle.
   */
  public function getAngle() {
    return $this->angle;
  }

  /**
   * Setter for angle.
   *
   * @param float $value
   *   The angle of the gradient fill, in degrees Use the angle to set
   *   the direction of the gradient. For example, a value of 180 defines
   *   a gradient that changes color from top to bottom. An angle of 90 
   *   defines a gradient that changes color from left to right.
   *
   *   If angle is omitted, an angle of 180 (top to bottom) is used.
   *
   * @return $this
   */
  public function setAngle($value) {
    $this->angle = $value;
    return $this;
  }

}
