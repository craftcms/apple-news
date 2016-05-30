<?php

/**
 * @file
 * An Apple News Document Margin.
 */

namespace ChapterThree\AppleNewsAPI\Document;

/**
 * An Apple News Document Margin.
 */
class Margin extends Base {

  protected $top;
  protected $bottom;

  /**
   * Implements __construct().
   *
   * @param mixed $target_anchor_position
   *   TargetAnchorPosition.
   */
  public function __construct($top = NULL, $bottom = NULL) {
    if ($top !== NULL) {
      $this->setTop($top);
    }
    if ($bottom !== NULL) {
      $this->setBottom($bottom);
    }
  }

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'top',
      'bottom',
    ));
  }

  /**
   * Getter for top.
   */
  public function getTop() {
    return $this->top;
  }

  /**
   * Setter for top.
   *
   * @param string $top
   *   Top.
   *
   * @return $this
   */
  public function setTop($top) {
    if (!$this->isSupportedUnit($top)) {
      $this->triggerError("Value \"${top}\" does not use a supported unit.");
    }
    else {
      $this->top = $top;
    }
    return $this;
  }

  /**
   * Getter for bottom.
   */
  public function getBottom() {
    return $this->bottom;
  }

  /**
   * Setter for bottom.
   *
   * @param mixed $bottom
   *   Bottom.
   *
   * @return $this
   */
  public function setBottom($bottom) {
    if (!$this->isSupportedUnit($bottom)) {
      $this->triggerError("Value \"${bottom}\" does not use a supported unit.");
    }
    else {
      $this->bottom = $bottom;
    }
    return $this;
  }

}
