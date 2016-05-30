<?php

/**
 * @file
 * An Apple News Document ScaleFadeAnimation.
 */

namespace ChapterThree\AppleNewsAPI\Document\Animations\ComponentAnimations;

/**
 * An Apple News Document ScaleFadeAnimation.
 */
class ScaleFadeAnimation extends ComponentAnimation {

  protected $initialAlpha;
  protected $initialScale;

  /**
   * Implements __construct().
   */
  public function __construct() {
    return parent::__construct('scale_fade');
  }

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'initialAlpha',
      'initialScale',
    ));
  }

  /**
   * Getter for initialAlpha.
   */
  public function getInitialAlpha() {
    return $this->initialAlpha;
  }

  /**
   * Setter for initialAlpha.
   *
   * @param float|int $value
   *   initialAlpha.
   *
   * @return $this
   */
  public function setInitialAlpha($value) {
    if ($this->validateInitialAlpha($value)) {
      $this->initialAlpha = $value;
    }
    return $this;
  }

  /**
   * Getter for initialScale.
   */
  public function getInitialScale() {
    return $this->initialScale;
  }

  /**
   * Setter for initialScale.
   *
   * @param float $value
   *   initialScale.
   *
   * @return $this
   */
  public function setInitialScale($value) {
    if ($this->validateInitialScale($value)) {
      $this->initialScale = $value;
    }
    return $this;
  }

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {
    $valid =
      (!isset($this->initialAlpha) ||
        $this->validateInitialAlpha($this->initialAlpha)) &&
      (!isset($this->initialScale) ||
        $this->validateInitialScale($this->initialScale));
    if (!$valid) {
      return NULL;
    }
    return parent::jsonSerialize();
  }

  /**
   * Validates the initialAlpha attribute.
   */
  protected function validateInitialAlpha($value) {
    if (!$this->isUnitInterval($value)) {
      $this->triggerError('initialAlpha is not a Unit Interval');
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validates the initialScale attribute.
   */
  protected function validateInitialScale($value) {
    if (!$this->isUnitInterval($value)) {
      $this->triggerError('initialScale is not a Unit Interval');
      return FALSE;
    }
    return TRUE;
  }

}
