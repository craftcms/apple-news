<?php

/**
 * @file
 * An Apple News Document Border.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles;

use ChapterThree\AppleNewsAPI\Document\Base;

/**
 * An Apple News Document Style.
 */
class DocumentStyle extends Base {

  protected $backgroundColor;

  /**
   * Implements __construct().
   *
   * @param int $background_color
   *   Text.
   */
  public function __construct($background_color) {
    $this->setBackgroundColor($background_color);
  }

  /**
   * Getter for backgroundColor.
   */
  public function getBackgroundColor() {
    return $this->backgroundColor;
  }

  /**
   * Setter for backgroundColor.
   *
   * @param string $value
   *   BackgroundColor.
   *
   * @return $this
   */
  public function setBackgroundColor($value) {
    if ($this->validateBackgroundColor($value)) {
      $this->backgroundColor = $value;
    }
    return $this;
  }

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {
    $valid = (!isset($this->backgroundColor) ||
        $this->validateBackgroundColor($this->backgroundColor));
    if (!$valid) {
      return NULL;
    }
    return parent::jsonSerialize();
  }

  /**
   * Validates the backgroundColor attribute.
   */
  protected function validateBackgroundColor($value) {
    if (!$this->isUnitInterval($value)) {
      $this->triggerError('backgroundColor is not valid');
      return FALSE;
    }
    return TRUE;
  }

}
