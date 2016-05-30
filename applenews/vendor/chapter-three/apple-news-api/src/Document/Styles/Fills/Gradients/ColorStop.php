<?php

/**
 * @file
 * An Apple News Document ColorStop.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles\Fills\Gradients;

use ChapterThree\AppleNewsAPI\Document\Base;

/**
 * An Apple News Document GradientFill.
 */
class ColorStop extends Base {

  protected $color;
  protected $location;

  /**
   * Implements __construct().
   *
   * @param string $color
   */
  public function __construct($color) {
    $this->setColor($color);
  }

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'location',
    ));
  }

  /**
   * Getter for color.
   */
  public function getColor() {
    return $this->color;
  }

  /**
   * Setter for color.
   *
   * @param string $value
   *   The color of this color stop, defined as a 3- to 8-character RGBA
   *   hexadecimal string; e.g., #000 for black or #FF00007F for red with
   *   an alpha (opacity) of 50%.
   *
   * @return $this
   */
  public function setColor($value) {
    $this->color = $value;
    return $this;
  }

  /**
   * Getter for location.
   */
  public function getLocation() {
    return $this->location;
  }

  /**
   * Setter for location.
   *
   * @param float $value
   *   An optional location of the color stop within the gradient, as a
   *   percentage of the gradient size. If location is omitted, the 
   *   length of the stop is calculated by first subtracting color stops
   *   with specified locations from the full length, then equally distributing
   *   the remaining length.
   *
   * @return $this
   */
  public function setLocation($value) {
    $this->location = $value;
    return $this;
  }

}
