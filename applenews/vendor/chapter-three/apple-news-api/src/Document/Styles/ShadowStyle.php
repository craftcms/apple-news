<?php

/**
 * @file
 * An Apple News Document Shadow style.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles;

use ChapterThree\AppleNewsAPI\Document\Base;

/**
 * An Apple News Document ShadowStyle.
 */
class ShadowStyle extends Base {

  protected $color;
  protected $radius;
  protected $opacity;
  protected $offset;

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'opacity',
      'offset',
    ));
  }

  /**
   * Implements __construct().
   *
   * @param string $color
   *   Color HEX.
   *
   * @param int $radius
   *   Radius.
   */
  public function __construct($color, $radius) {
    $this->setColor($color);
    $this->setRadius($radius);
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
   *   Color.
   *
   * @return $this
   */
  public function setColor($value) {
    if ($this->validateColor($value)) {
      $this->color = $value;
    }
    return $this;
  }

  /**
   * Getter for radius.
   */
  public function getRadius() {
    return $this->radius;
  }

  /**
   * Setter for radius.
   *
   * @param int $value
   *   Width.
   *
   * @return $this
   */
  public function setRadius($value) {
    if ($this->validateRadius($value)) {
      $this->radius = $value;
    }
    return $this;
  }

  /**
   * Getter for opacity.
   */
  public function getOpacity() {
    return $this->opacity;
  }

  /**
   * Setter for opacity.
   *
   * @param string $value
   *   Opacity.
   *
   * @return $this
   */
  public function setOpacity($value) {
    if ($this->validateOpacity($value)) {
      $this->opacity = $value;
    }
    return $this;
  }

  /**
   * Getter for Offset.
   *
   * @return Offset
   */
  public function getOffset() {
    return $this->offset;
  }

  /**
   * Setter for Offset
   *
   * @param Offset $offset
   */
  public function setOffset(Offset $offset) {
    $this->offset = $offset;
  }

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {
    $valid = (!isset($this->color) ||
      $this->validateColor($this->color));
    if (!$valid) {
      return NULL;
    }
    return parent::jsonSerialize();
  }

  /**
   * Validates the color attribute.
   */
  protected function validateColor($value) {
    if (!$this->isHexColor($value)) {
      $this->triggerError('color is not valid');
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validates the opacity attribute.
   */
  protected function validateOpacity($value) {
    if (!is_numeric($value) || $value < 0 || $value > 100) {
      $this->triggerError('opacity is not valid');
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validates the radius attribute.
   */
  protected function validateRadius($value) {
    if (!is_numeric($value) || $value < 0 || $value > 100) {
      $this->triggerError('radius is not valid');
      return FALSE;
    }
    return TRUE;
  }

}
