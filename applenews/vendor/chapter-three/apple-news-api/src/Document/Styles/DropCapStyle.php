<?php

/**
 * @file
 * An Apple News Document DropCapStyle.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles;

use ChapterThree\AppleNewsAPI\Document\Base;

/**
 * An Apple News Document DropCapStyle.
 */
class DropCapStyle extends Base {

  protected $numberOfLines;
  protected $numberOfCharacters;
  protected $fontName;
  protected $textColor;
  protected $backgroundColor;
  protected $padding;

  /**
   * Implements __construct().
   *
   * @param int $number_of_lines
   *   Text.
   */
  public function __construct($number_of_lines) {
    $this->setNumberOfLines($number_of_lines);
  }

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'numberOfCharacters',
      'fontName',
      'textColor',
      'backgroundColor',
      'padding',
    ));
  }

  /**
   * Getter for numberOfLines.
   */
  public function getNumberOfLines() {
    return $this->numberOfLines;
  }

  /**
   * Setter for numberOfLines.
   *
   * @param int $value
   *   NumberOfLines.
   *
   * @return $this
   */
  public function setNumberOfLines($value) {
    $this->numberOfLines = $value;
    return $this;
  }

  /**
   * Getter for numberOfCharacters.
   */
  public function getNumberOfCharacters() {
    return $this->numberOfCharacters;
  }

  /**
   * Setter for numberOfCharacters.
   *
   * @param int $value
   *   NumberOfCharacters.
   *
   * @return $this
   */
  public function setNumberOfCharacters($value) {
    $this->numberOfCharacters = $value;
    return $this;
  }

  /**
   * Getter for fontName.
   */
  public function getFontName() {
    return $this->fontName;
  }

  /**
   * Setter for fontName.
   *
   * @param string $value
   *   FontName.
   *
   * @return $this
   */
  public function setFontName($value) {
    $this->fontName = $value;
    return $this;
  }

  /**
   * Getter for textColor.
   */
  public function getTextColor() {
    return $this->textColor;
  }

  /**
   * Setter for textColor.
   *
   * @param string $value
   *   TextColor.
   *
   * @return $this
   */
  public function setTextColor($value) {
    if ($this->validateTextColor($value)) {
      $this->textColor = $value;
    }
    return $this;
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
   * Getter for padding.
   */
  public function getPadding() {
    return $this->padding;
  }

  /**
   * Setter for padding.
   *
   * @param int $value
   *   Padding.
   *
   * @return $this
   */
  public function setPadding($value) {
    $this->padding = $value;
    return $this;
  }

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {
    $valid = (!isset($this->textColor) ||
        $this->validateTextColor($this->textColor)) &&
      (!isset($this->backgroundColor) ||
        $this->validateBackgroundColor($this->backgroundColor));
    if (!$valid) {
      return NULL;
    }
    return parent::jsonSerialize();
  }

  /**
   * Validates the textColor attribute.
   */
  protected function validateTextColor($value) {
    if (!$this->isHexColor($value)) {
      $this->triggerError('textColor is not valid');
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validates the backgroundColor attribute.
   */
  protected function validateBackgroundColor($value) {
    if (!$this->isHexColor($value)) {
      $this->triggerError('backgroundColor is not valid');
      return FALSE;
    }
    return TRUE;
  }

}
