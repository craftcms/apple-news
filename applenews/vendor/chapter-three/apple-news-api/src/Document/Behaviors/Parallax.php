<?php

/**
 * @file
 * An Apple News Document Parallax.
 */

namespace ChapterThree\AppleNewsAPI\Document\Behaviors;

/**
 * An Apple News Document Parallax.
 *
 * @property $type
 */
class Parallax extends Behavior {

  protected $factor;

  /**
   * Implements __construct().
   */
  public function __construct() {
    return parent::__construct('parallax');
  }

  /**
   * {@inheritdoc}
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'factor',
    ));
  }

  /**
   * Getter for factor.
   */
  public function getFactor() {
    return $this->factor;
  }

  /**
   * Setter for factor.
   *
   * @param float $value
   *   Factor.
   *
   * @return $this
   */
  public function setFactor($value) {
    $this->factor = $value;
    return $this;
  }

}
