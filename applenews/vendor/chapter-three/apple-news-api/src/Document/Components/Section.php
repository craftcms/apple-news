<?php

/**
 * @file
 * An Apple News Document Section.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document\Animations\Scenes\Scene;

/**
 * An Apple News Document Section.
 */
class Section extends ComponentNested {

  protected $scene;

  /**
   * Implements __construct().
   *
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($identifier = NULL, $role = NULL) {
    return parent::__construct('section', $identifier);
  }

  /**
   * {@inheritdoc}
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'scene',
    ));
  }

  /**
   * Getter for scene.
   */
  public function getScene() {
    return $this->scene;
  }

  /**
   * Setter for scene.
   *
   * @param \ChapterThree\AppleNewsAPI\Document\Animations\Scenes\Scene $scene
   *   Scene.
   *
   * @return $this
   */
  public function setScene(Scene $scene) {
    $this->scene = $scene;
    return $this;
  }

}
