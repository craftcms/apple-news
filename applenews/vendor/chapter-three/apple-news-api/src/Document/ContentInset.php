<?php

/**
 * @file
 * An Apple News Document ContentInset.
 */

namespace ChapterThree\AppleNewsAPI\Document;

/**
 * An Apple News Document ContentInset.
 */
class ContentInset extends Base {

  protected $top;
  protected $right;
  protected $bottom;
  protected $left;

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'top',
      'right',
      'bottom',
      'left',
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
   * @param bool $top
   *   Top.
   *
   * @return $this
   */
  public function setTop($top) {
    $this->top = $top;
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
   * @param bool $right
   *   Right.
   *
   * @return $this
   */
  public function setRight($right) {
    $this->right = $right;
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
   * @param bool $bottom
   *   Bottom.
   *
   * @return $this
   */
  public function setBottom($bottom) {
    $this->bottom = $bottom;
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
   * @param bool $left
   *   Left.
   *
   * @return $this
   */
  public function setLeft($left) {
    $this->left = $left;
    return $this;
  }

}
