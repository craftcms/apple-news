<?php

/**
 * @file
 * An Apple News Document StrokeStyle.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles;

use ChapterThree\AppleNewsAPI\Document\Base;

/**
 * An Apple News Document StrokeStyle.
 */
class StrokeStyle extends Base {

  protected $color;
  protected $width;
  protected $style;

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'color',
      'width',
      'style',
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
   * Getter for width.
   */
  public function getWidth() {
    return $this->width;
  }

  /**
   * Setter for width.
   *
   * @param string|int $value
   *   Width.
   *
   * @return $this
   */
  public function setWidth($value) {
    if ($this->validateWidth($value)) {
      $this->width = $value;
    }
    return $this;
  }

  /**
   * Getter for style.
   */
  public function getStyle() {
    return $this->style;
  }

  /**
   * Setter for style.
   *
   * @param string $value
   *   Style.
   *
   * @return $this
   */
  public function setStyle($value) {
    if ($this->validateStyle($value)) {
      $this->style = $value;
    }
    return $this;
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
   * Validates the width attribute.
   */
  protected function validateWidth($value) {
    if (!$this->isSupportedUnit($value)) {
      $this->triggerError('width is not valid');
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validates the style attribute.
   */
  protected function validateStyle($value) {
    if (!in_array($value, array(
        'solid',
        'dashed',
        'dotted',
      ))
    ) {
      $this->triggerError('style is not valid');
      return FALSE;
    }
    return TRUE;
  }

}
