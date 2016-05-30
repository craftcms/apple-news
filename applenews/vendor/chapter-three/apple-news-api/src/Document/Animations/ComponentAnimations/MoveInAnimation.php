<?php

/**
 * @file
 * An Apple News Document MoveInAnimation.
 */

namespace ChapterThree\AppleNewsAPI\Document\Animations\ComponentAnimations;

/**
 * An Apple News Document MoveInAnimation.
 */
class MoveInAnimation extends ComponentAnimation {

  protected $preferredStartingPosition;

  /**
   * Implements __construct().
   */
  public function __construct() {
    return parent::__construct('move_in');
  }

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'preferredStartingPosition',
    ));
  }

  /**
   * Getter for preferredStartingPosition.
   */
  public function getPreferredStartingPosition() {
    return $this->preferredStartingPosition;
  }

  /**
   * Setter for preferredStartingPosition.
   *
   * @param mixed $value
   *   preferredStartingPosition.
   *
   * @return $this
   */
  public function setPreferredStartingPosition($value) {
    if ($this->validatePreferredStartingPosition($value)) {
      $this->preferredStartingPosition = $value;
    }
    return $this;
  }

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {
    $valid = !isset($this->preferredStartingPosition) ||
      $this->validatePreferredStartingPosition($this->preferredStartingPosition);
    if (!$valid) {
      return NULL;
    }
    return parent::jsonSerialize();
  }

  /**
   * Validates the preferredStartingPosition attribute.
   */
  protected function validatePreferredStartingPosition($value) {
    if (!in_array($value, array('left', 'right'))) {
      $this->triggerError('preferredStartingPosition not one of "left" or "right"');
      return FALSE;
    }
    return TRUE;
  }

}
