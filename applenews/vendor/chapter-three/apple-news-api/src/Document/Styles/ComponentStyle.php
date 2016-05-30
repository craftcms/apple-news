<?php

/**
 * @file
 * An Apple News Document ComponentStyle.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles;

use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document\Styles\Fills\Fill;

/**
 * An Apple News Document ComponentStyle.
 */
class ComponentStyle extends Base {

  protected $backgroundColor;
  protected $fill;
  protected $opacity;
  protected $border;

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'backgroundColor',
      'fill',
      'opacity',
      'border',
    ));
  }

  /**
   * Getter for backgroundColor.
   */
  public function getBackgroundColor() {
    return $this->backgroundColor;
  }

  /**
   * Setter for backgroundColor.
   *
   * @param string $value
   *   BackgroundColor.
   *
   * @return $this
   */
  public function setBackgroundColor($value = 'transparent') {
    if ($this->validateBackgroundColor($value)) {
      $this->backgroundColor = $value;
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
   * @param float $value
   *   Opacity.
   *
   * @return $this
   */
  public function setOpacity($value = 1) {
    if ($this->validateOpacity($value)) {
      $this->opacity = $value;
    }
    return $this;
  }

  /**
   * Getter for fill.
   */
  public function getFill() {
    return $this->fill;
  }

  /**
   * Setter for fill.
   *
   * @param \ChapterThree\AppleNewsAPI\Document\Styles\Fills\Fill $value
   *   Fill.
   *
   * @return $this
   */
  public function setFill(Fill $value) {
    $this->fill = $value;
    return $this;
  }

  /**
   * Getter for border.
   */
  public function getBorder() {
    return $this->border;
  }

  /**
   * Setter for border.
   *
   * @param \ChapterThree\AppleNewsAPI\Document\Styles\Border $value
   *   Border.
   *
   * @return $this
   */
  public function setBorder(Border $value) {
    $this->border = $value;
    return $this;
  }

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {
    $valid = (!isset($this->backgroundColor) ||
        $this->validateBackgroundColor($this->backgroundColor)) &&
      (!isset($this->opacity) ||
        $this->validateOpacity($this->opacity));
    if (!$valid) {
      return NULL;
    }
    return parent::jsonSerialize();
  }

  /**
   * Validates the backgroundColor attribute.
   */
  protected function validateBackgroundColor($value) {
    if (!$this->isHexColor($value)) {
      $this->triggerError('backgroundColor is not valid');
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validates the opacity attribute.
   */
  protected function validateOpacity($value) {
    if (!$this->isUnitInterval($value)) {
      $this->triggerError('opacity is not valid');
      return FALSE;
    }
    return TRUE;
  }

}
