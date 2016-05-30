<?php

/**
 * @file
 * An Apple News Document InlineTextStyle.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles;

use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document;

/**
 * An Apple News Document InlineTextStyle.
 */
class InlineTextStyle extends Base {

  protected $rangeStart;
  protected $rangeLength;
  protected $textStyle;

  /**
   * Implements __construct().
   *
   * @param int $range_start
   *   RangeStart.
   * @param int $range_length
   *   RangeLength.
   * @param string|\ChapterThree\AppleNewsAPI\Document\Styles\TextStyle $text_style
   *   Either a TextStyle object, or a string reference to one defined
   *   in $document.
   */
  public function __construct($range_start, $range_length, $text_style) {
    $this->setRangeStart($range_start);
    $this->setRangeLength($range_length);
    $this->setTextStyle($text_style);
  }

  /**
   * Getter for rangeStart.
   */
  public function getRangeStart() {
    return $this->rangeStart;
  }

  /**
   * Setter for rangeStart.
   *
   * @param int $value
   *   RangeStart.
   *
   * @return $this
   */
  public function setRangeStart($value) {
    $this->rangeStart = $value;
    return $this;
  }

  /**
   * Getter for rangeLength.
   */
  public function getRangeLength() {
    return $this->rangeLength;
  }

  /**
   * Setter for rangeLength.
   *
   * @param int $value
   *   RangeLength.
   *
   * @return $this
   */
  public function setRangeLength($value) {
    $this->rangeLength = $value;
    return $this;
  }

  /**
   * Getter for textStyle.
   */
  public function getTextStyle() {
    return $this->textStyle;
  }

  /**
   * Setter for textStyle.
   *
   * @param string|\ChapterThree\AppleNewsAPI\Document\Styles\TextStyle $value
   *   Either a TextStyle object, or a string reference to one defined
   *   in $document.
   * @param \ChapterThree\AppleNewsAPI\Document|NULL $document
   *   If required by first parameter.
   *
   * @return $this
   */
  public function setTextStyle($value, Document $document = NULL) {
    $class = 'ChapterThree\AppleNewsAPI\Document\Styles\TextStyle';
    if (is_string($value)) {
      // Check that value exists.
      if ($document &&
          empty($document->getTextStyles()[$value])
      ) {
        $this->triggerError("No TextStyle \"${value}\" found.");
        return $this;
      }
    }
    elseif (!$value instanceof $class) {
      $this->triggerError("Style not of class ${class}.");
      return $this;
    }
    $this->textStyle = $value;
    return $this;
  }

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {

    if (isset($this->rangeStart) && !isset($this->rangeLength)) {
      $msg = "If rangeStart is specified, rangeLength is required.";
      $this->triggerError($msg);
      return NULL;
    }

    return parent::jsonSerialize();
  }

}
