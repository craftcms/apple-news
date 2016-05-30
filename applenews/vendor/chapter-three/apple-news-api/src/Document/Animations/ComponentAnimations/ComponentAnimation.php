<?php

/**
 * @file
 * An Apple News Document ComponentAnimation.
 */

namespace ChapterThree\AppleNewsAPI\Document\Animations\ComponentAnimations;

use ChapterThree\AppleNewsAPI\Document\Base;

/**
 * An Apple News Document ComponentAnimation.
 *
 * @property $type
 * @property $userControllable
 */
abstract class ComponentAnimation extends Base {

  protected $type;

  protected $userControllable;

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
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'userControllable',
    ));
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
