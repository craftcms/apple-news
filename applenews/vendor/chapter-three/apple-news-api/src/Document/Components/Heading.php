<?php

/**
 * @file
 * An Apple News Document Heading.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Heading Component.
 */
class Heading extends Text {

  /**
   * Implements __construct().
   *
   * @param mixed $text
   *   Text.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($text, $identifier = NULL) {
    return parent::__construct('heading', $text, $identifier);
  }

  /**
   * Setter for role.
   *
   * @param string $value
   *   One of "heading" or "heading1" through "heading6", defaults to "heading".
   *
   * @return $this
   */
  public function setRole($value) {
    $this->validateRole($value);
    return parent::setRole($value);
  }

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {
    if (!$this->validateRole($this->role)) {
      return NULL;
    }
    return parent::jsonSerialize();
  }

  /**
   * Validates the role attribute.
   */
  protected function validateRole($value) {
    if (!preg_match('/^heading[1-6]?$/', $value)) {
      $this->triggerError('Heading not one of "heading" or "heading1" through "heading6"');
      return FALSE;
    }
    return TRUE;
  }

}
