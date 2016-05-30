<?php

/**
 * @file
 * An Apple News Document TextStrokeStyle.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles;

use ChapterThree\AppleNewsAPI\Document\Base;

/**
 * An Apple News Document TextStrokeStyle.
 */
class TextStrokeStyle extends Base {

  protected $color;

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'color',
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

}
