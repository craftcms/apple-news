<?php

/**
 * @file
 * An Apple News Document Component.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document;
use ChapterThree\AppleNewsAPI\Document\Anchor;
use ChapterThree\AppleNewsAPI\Document\Animations\ComponentAnimations\ComponentAnimation;
use ChapterThree\AppleNewsAPI\Document\Behaviors\Behavior;

/**
 * An Apple News Document Component.
 */
abstract class Component extends Base {

  protected $role;

  protected $identifier;
  protected $layout;
  protected $style;
  protected $anchor;
  protected $animation;
  protected $behavior;

  /**
   * Implements __construct().
   *
   * @param mixed $role
   *   Role.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($role, $identifier = NULL) {
    $this->setRole($role);
    $this->setIdentifier($identifier);
  }

  /**
   * {@inheritdoc}
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'identifier',
      'layout',
      'style',
      'anchor',
      'animation',
      'behavior',
    ));
  }

  /**
   * Getter for role.
   */
  public function getRole() {
    return $this->role;
  }

  /**
   * Setter for role.
   *
   * Concrete classes are expected to set this explicitly.
   *
   * @param mixed $role
   *   Role.
   *
   * @return $this
   */
  protected function setRole($role) {
    $this->role = (string) $role;
    return $this;
  }

  /**
   * Getter for identifier.
   */
  public function getIdentifier() {
    return $this->identifier;
  }

  /**
   * Setter for identifier.
   *
   * @param mixed $identifier
   *   Identifier.
   *
   * @return $this
   */
  public function setIdentifier($identifier) {
    $this->identifier = (string) $identifier;
    return $this;
  }

  /**
   * Getter for layout.
   *
   * @return \ChapterThree\AppleNewsAPI\Document\Layouts\ComponentLayout|string
   *   Layout object or string reference
   */
  public function getLayout() {
    return $this->layout;
  }

  /**
   * Setter for layout.
   *
   * @param \ChapterThree\AppleNewsAPI\Document\Layouts\ComponentLayout|string $layout
   *   Either a ComponentLayout object, or a string reference to one defined in
   *   $document.
   * @param \ChapterThree\AppleNewsAPI\Document|NULL $document
   *   If required by first parameter.
   *
   * @return $this
   */
  public function setLayout($layout, Document $document = NULL) {
    $class = 'ChapterThree\AppleNewsAPI\Document\Layouts\ComponentLayout';
    if (is_string($layout)) {
      // Check that layout exists.
      if ($document &&
          empty($document->getComponentLayouts()[$layout])
      ) {
        $this->triggerError("No component layout \"${layout}\" found.");
        return $this;
      }
    }
    elseif (!$layout instanceof $class) {
      $this->triggerError("Layout not of class ${class}.");
      return $this;
    }
    $this->layout = $layout;
    return $this;
  }

  /**
   * Getter for style.
   */
  public function getStyle() {
    return $this->style;
  }

  /**
   * Setter for style.
   *
   * @param \ChapterThree\AppleNewsAPI\Document\Styles\ComponentStyle|string $style
   *   Either a ComponentStyle object, or a string reference to one defined in
   *   $document.
   * @param \ChapterThree\AppleNewsAPI\Document|NULL $document
   *   If required by first parameter.
   *
   * @return $this
   */
  public function setStyle($style, Document $document = NULL) {
    $class = 'ChapterThree\AppleNewsAPI\Document\Styles\ComponentStyle';
    if (is_string($style)) {
      // Check that style exists.
      if ($document &&
          empty($document->getComponentLayouts()[$style])
      ) {
        $this->triggerError("No component style \"${style}\" found.");
        return $this;
      }
    }
    elseif (!$style instanceof $class) {
      $this->triggerError("Style not of class ${class}.");
      return $this;
    }
    $this->style = $style;
    return $this;
  }

  /**
   * Getter for anchor.
   */
  public function getAnchor() {
    return $this->anchor;
  }

  /**
   * Setter for anchor.
   *
   * @param \ChapterThree\AppleNewsAPI\Document\Anchor $anchor
   *   Anchor.
   *
   * @return $this
   */
  public function setAnchor(Anchor $anchor) {
    $this->anchor = $anchor;
    return $this;
  }

  /**
   * Getter for animation.
   */
  public function getAnimation() {
    return $this->animation;
  }

  /**
   * Setter for animation.
   *
   * @param \ChapterThree\AppleNewsAPI\Document\Animations\ComponentAnimations\ComponentAnimation $animation
   *   Animation.
   *
   * @return $this
   */
  public function setAnimation(ComponentAnimation $animation) {
    $this->animation = $animation;
    return $this;
  }

  /**
   * Getter for behavior.
   */
  public function getBehavior() {
    return $this->behavior;
  }

  /**
   * Setter for behavior.
   *
   * @param \ChapterThree\AppleNewsAPI\Document\Behaviors\Behavior $behavior
   *   Behavior.
   *
   * @return $this
   */
  public function setBehavior(Behavior $behavior) {
    $this->behavior = $behavior;
    return $this;
  }

}
