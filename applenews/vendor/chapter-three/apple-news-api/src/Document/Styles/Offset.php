<?php

/**
 * @file
 * An Apple News component offset.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles;

use ChapterThree\AppleNewsAPI\Document\Base;

/**
 * An Apple News Offset component.
 */
class Offset extends Base {

  /**
   * X coordinate
   *
   * @var float
   */
  protected $x;

  /**
   * Y coordinate
   *
   * @var float
   */
  protected $y;

  /**
   * Implements __construct().
   *
   * @param int $x
   *   X offset.
   *
   * @param int $y
   *   Y offset.
   */
  public function __construct($x, $y) {
    $this->setX($x);
    $this->setY($y);
  }

  /**
   * Gets x
   *
   * @return float
   */
  public function getX() {
    return $this->x;
  }

  /**
   * Sets x
   *
   * @param float $x
   */
  public function setX($x) {
    if ($this->validateOffset($x)) {
      $this->x = round($x, 1);
    }
  }

  /**
   * Gets y
   *
   * @return float
   */
  public function getY() {
    return $this->y;
  }

  /**
   * Sets y
   *
   * @param float $y
   */
  public function setY($y) {
    if ($this->validateOffset($y)) {
      $this->y = round($y, 1);
    }
  }

  /**
   * Validates the offset.
   *
   * @param mixed $value
   *
   * @return bool
   */
  protected function validateOffset($value) {
    return is_numeric($value) && $value >= -50 && $value <= 50;
  }
}
