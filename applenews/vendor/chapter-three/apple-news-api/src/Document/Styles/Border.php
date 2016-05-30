<?php

/**
 * @file
 * An Apple News Document Border.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles;

use ChapterThree\AppleNewsAPI\Document\Base;

/**
 * An Apple News Document Border.
 *
 * @property $all
 * @property $top
 * @property $bottom
 * @property $left
 * @property $right
 */
class Border extends Base {

  protected $all;
  protected $top;
  protected $bottom;
  protected $left;
  protected $right;

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'all',
      'top',
      'bottom',
      'left',
      'right',
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
   * @param bool $value
   *   Top.
   *
   * @return $this
   */
  public function setTop($value = true) {
    $this->top = $value;
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
   * @param bool $value
   *   Bottom.
   *
   * @return $this
   */
  public function setBottom($value = true) {
    $this->bottom = $value;
    return $this;
  }

  /**
   * Getter for left.
   */
  public function getLeft() {
    return $this->left;
  }

  /**
   * Setter for left.
   *
   * @param bool $value
   *   Left.
   *
   * @return $this
   */
  public function setLeft($value = true) {
    $this->left = $value;
    return $this;
  }

  /**
   * Getter for right.
   */
  public function getRight() {
    return $this->right;
  }

  /**
   * Setter for right.
   *
   * @param bool $value
   *   Right.
   *
   * @return $this
   */
  public function setRight($value = true) {
    $this->right = $value;
    return $this;
  }

  /**
   * Getter for all.
   */
  public function getAll() {
    return $this->all;
  }

  /**
   * Setter for all.
   *
   * @param \ChapterThree\AppleNewsAPI\Document\Styles\StrokeStyle $value
   *   All.
   *
   * @return $this
   */
  public function setAll(StrokeStyle $value) {
    $this->all = $value;
    return $this;
  }

}
