<?php

/**
 * @file
 * An Apple News Document Anchor.
 */

namespace ChapterThree\AppleNewsAPI\Document;

/**
 * An Apple News Document Anchor.
 */
class Anchor extends Base {

  protected $targetAnchorPosition;
  protected $originAnchorPosition;
  protected $targetComponentIdentifier;
  protected $rangeStart;
  protected $rangeLength;

  /**
   * Implements __construct().
   *
   * @param mixed $target_anchor_position
   *   TargetAnchorPosition.
   */
  public function __construct($target_anchor_position) {
    $this->setTargetAnchorPosition($target_anchor_position);
  }

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'originAnchorPosition',
      'targetComponentIdentifier',
      'rangeStart',
      'rangeLength',
    ));
  }

  /**
   * Getter for targetAnchorPosition.
   */
  public function getTargetAnchorPosition() {
    return $this->targetAnchorPosition;
  }

  /**
   * Setter for targetAnchorPosition.
   *
   * @param mixed $value
   *   TargetAnchorPosition.
   *
   * @return $this
   */
  public function setTargetAnchorPosition($value) {
    if ($this->validateTargetAnchorPosition($value)) {
      $this->targetAnchorPosition = (string) $value;
    }
    return $this;
  }

  /**
   * Getter for originAnchorPosition.
   */
  public function getOriginAnchorPosition() {
    return $this->originAnchorPosition;
  }

  /**
   * Setter for originAnchorPosition.
   *
   * @param mixed $value
   *   OriginAnchorPosition.
   *
   * @return $this
   */
  public function setOriginAnchorPosition($value) {
    $this->originAnchorPosition = (string) $value;
    return $this;
  }

  /**
   * Getter for targetComponentIdentifier.
   */
  public function getTargetComponentIdentifier() {
    return $this->targetComponentIdentifier;
  }

  /**
   * Setter for targetComponentIdentifier.
   *
   * @param mixed $value
   *   TargetComponentIdentifier.
   *
   * @return $this
   */
  public function setTargetComponentIdentifier($value) {
    $this->targetComponentIdentifier = (string) $value;
    return $this;
  }

  /**
   * Getter for rangeStart.
   */
  public function getRangeStart() {
    return $this->rangeStart;
  }

  /**
   * Setter for rangeStart.
   *
   * @param int $value
   *   RangeStart.
   *
   * @return $this
   */
  public function setRangeStart($value) {
    $this->rangeStart = $value;
    return $this;
  }

  /**
   * Getter for rangeLength.
   */
  public function getRangeLength() {
    return $this->rangeLength;
  }

  /**
   * Setter for rangeLength.
   *
   * @param int $value
   *   RangeLength.
   *
   * @return $this
   */
  public function setRangeLength($value) {
    $this->rangeLength = $value;
    return $this;
  }

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {
    if (isset($this->rangeStart) && !isset($this->rangeLength)) {
      $msg = "If rangeStart is specified, rangeLength is required.";
      $this->triggerError($msg);
      return NULL;
    }
    $valid = (!isset($this->targetAnchorPosition) ||
        $this->validateTargetAnchorPosition($this->targetAnchorPosition));
    if (!$valid) {
      return NULL;
    }
    return parent::jsonSerialize();
  }

  /**
   * Validates the targetAnchorPosition attribute.
   */
  protected function validateTargetAnchorPosition($value) {
    if (!in_array($value, array(
        'top',
        'center',
        'bottom',
      ))
    ) {
      $this->triggerError('targetAnchorPosition is not valid');
      return FALSE;
    }
    return TRUE;
  }

}
