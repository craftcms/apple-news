<?php

/**
 * @file
 * An Apple News Document Layout.
 */

namespace ChapterThree\AppleNewsAPI\Document\Layouts;

use ChapterThree\AppleNewsAPI\Document\Base;

/**
 * An Apple News Document Layout.
 */
class Layout extends Base {

  protected $columns;
  protected $width;

  protected $margin;
  protected $gutter;

  /**
   * Implements __construct().
   *
   * @param int $columns
   *   Columns.
   * @param int $width
   *   Width.
   */
  public function __construct($columns, $width) {
    $this->setColumns($columns);
    $this->setWidth($width);
  }

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'margin',
      'gutter',
    ));
  }

  /**
   * Getter for columns.
   */
  public function getColumns() {
    return $this->columns;
  }

  /**
   * Setter for columns.
   *
   * @param int $value
   *   Columns.
   *
   * @return $this
   */
  public function setColumns($value) {
    $this->columns = $value;
    return $this;
  }

  /**
   * Getter for width.
   */
  public function getWidth() {
    return $this->width;
  }

  /**
   * Setter for columns.
   *
   * @param int $value
   *   Width.
   *
   * @return $this
   */
  public function setWidth($value) {
    $this->width = $value;
    return $this;
  }

  /**
   * Getter for margin.
   */
  public function getMargin() {
    return $this->margin;
  }

  /**
   * Setter for margin.
   *
   * @param int $value
   *   Margin.
   *
   * @return $this
   */
  public function setMargin($value) {
    $this->margin = $value;
    return $this;
  }

  /**
   * Getter for gutter.
   */
  public function getGutter() {
    return $this->gutter;
  }

  /**
   * Setter for gutter.
   *
   * @param int $value
   *   Gutter.
   *
   * @return $this
   */
  public function setGutter($value) {
    $this->gutter = $value;
    return $this;
  }

}
