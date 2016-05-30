<?php

/**
 * @file
 * An Apple News Document Scene.
 */

namespace ChapterThree\AppleNewsAPI\Document\Animations\Scenes;

use ChapterThree\AppleNewsAPI\Document\Base;

/**
 * An Apple News Document Scene.
 *
 * @property $type
 */
abstract class Scene extends Base {

  protected $type;

  /**
   * Implements __construct().
   *
   * @param bool $type
   *   Type.
   */
  public function __construct($type) {
    $this->setType($type);
  }

  /**
   * Getter for type.
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Setter for type.
   *
   * Concrete classes are expected to set this explicitly.
   *
   * @param bool $value
   *   Type.
   *
   * @return $this
   */
  protected function setType($value) {
    $this->type = $value;
    return $this;
  }

}
