<?php

/**
 * @file
 * An Apple News Document TextStyle.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles;

use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document\Styles\TextStrokeStyle;

/**
 * An Apple News Document TextStyle.
 */
class TextStyle extends Base {

  protected $fontName;
  protected $fontSize;
  protected $textColor;
  protected $textTransform;
  protected $underline;
  protected $strikethrough;
  protected $backgroundColor;
  protected $verticalAlignment;
  protected $tracking;

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'fontName',
      'fontSize',
      'textColor',
      'textTransform',
      'underline',
      'strikethrough',
      'backgroundColor',
      'verticalAlignment',
      'tracking',
    ));
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
    $this->fontName = (string) $value;
    return $this;
  }

  /**
   * Getter for fontSize.
   */
  public function getFontSize() {
    return $this->fontSize;
  }

  /**
   * Setter for fontSize.
   *
   * @param int $value
   *   FontSize.
   *
   * @return $this
   */
  public function setFontSize($value) {
    $this->fontSize = $value;
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
   * Getter for textTransform.
   */
  public function getTextTransform() {
    return $this->textTransform;
  }

  /**
   * Setter for textTransform.
   *
   * @param string $value
   *   TextTransform.
   *
   * @return $this
   */
  public function setTextTransform($value) {
    if ($this->validateTextTransform($value)) {
      $this->textTransform = $value;
    }
    return $this;
  }

  /**
   * Getter for underline.
   */
  public function getUnderline() {
    return $this->underline;
  }

  /**
   * Setter for underline.
   *
   * @param bool|\ChapterThree\AppleNewsAPI\Document\Styles\TextStrokeStyle $value
   *   Underline.
   *
   * @return $this
   */
  public function setUnderline($value) {
    if (is_object($value) && !$value instanceof TextStrokeStyle) {
      $this->triggerError('Object not of type TextStrokeStyle');
    }
    else {
      $this->underline = $value;
    }
    return $this;
  }

  /**
   * Getter for strikethrough.
   */
  public function getStrikethrough() {
    return $this->strikethrough;
  }

  /**
   * Setter for strikethrough.
   *
   * @param bool|\ChapterThree\AppleNewsAPI\Document\Styles\TextStrokeStyle $value
   *   Strikethrough.
   *
   * @return $this
   */
  public function setStrikethrough($value) {
    if (is_object($value) && !$value instanceof TextStrokeStyle) {
      $this->triggerError('Object not of type TextStrokeStyle');
    }
    else {
      $this->strikethrough = $value;
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
   * Getter for verticalAlignment.
   */
  public function getVerticalAlignment() {
    return $this->verticalAlignment;
  }

  /**
   * Setter for verticalAlignment.
   *
   * @param string $value
   *   VerticalAlignment.
   *
   * @return $this
   */
  public function setVerticalAlignment($value) {
    if ($this->validateVerticalAlignment($value)) {
      $this->verticalAlignment = $value;
    }
    return $this;
  }

  /**
   * Getter for tracking.
   */
  public function getTracking() {
    return $this->tracking;
  }

  /**
   * Setter for tracking.
   *
   * @param float $value
   *   Tracking.
   *
   * @return $this
   */
  public function setTracking($value) {
    $this->tracking = $value;
    return $this;
  }

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {
    $valid = (!isset($this->textColor) ||
        $this->validateTextColor($this->textColor)) &&
      (!isset($this->textTransform) ||
        $this->validateTextTransform($this->textTransform)) &&
      (!isset($this->backgroundColor) ||
        $this->validateBackgroundColor($this->backgroundColor)) &&
      (!isset($this->verticalAlignment) ||
        $this->validateVerticalAlignment($this->verticalAlignment));
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
   * Validates the textTransform attribute.
   */
  protected function validateTextTransform($value) {
    if (!in_array($value, array(
        'uppercase',
        'lowercase',
        'capitalize',
        'none',
      ))
    ) {
      $this->triggerError('textTransform is not valid');
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

  /**
   * Validates the verticalAlignment attribute.
   */
  protected function validateVerticalAlignment($value) {
    if (!in_array($value, array(
        'superscript',
        'subscript',
        'baseline',
      ))
    ) {
      $this->triggerError('verticalAlignment is not valid');
      return FALSE;
    }
    return TRUE;
  }

}
