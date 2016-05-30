<?php

/**
 * @file
 * An Apple News Document ComponentTextStyle.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles;

/**
 * An Apple News Document ComponentTextStyle.
 */
class ComponentTextStyle extends TextStyle {

  protected $textAlignment;
  protected $lineHeight;
  protected $dropCapStyle;
  protected $hyphenation;
  protected $linkStyle;

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'textAlignment',
      'lineHeight',
      'dropCapStyle',
      'hyphenation',
      'linkStyle',
    ));
  }

  /**
   * Getter for textAlignment.
   */
  public function getTextAlignment() {
    return $this->textAlignment;
  }

  /**
   * Setter for textAlignment.
   *
   * @param string $value
   *   TextAlignment.
   *
   * @return $this
   */
  public function setTextAlignment($value) {
    if ($this->validateTextAlignment($value)) {
      $this->textAlignment = $value;
    }
    return $this;
  }

  /**
   * Getter for lineHeight.
   */
  public function getLineHeight() {
    return $this->lineHeight;
  }

  /**
   * Setter for lineHeight.
   *
   * @param int $value
   *   LineHeight.
   *
   * @return $this
   */
  public function setLineHeight($value) {
    $this->lineHeight = $value;
    return $this;
  }

  /**
   * Getter for dropCapStyle.
   */
  public function getDropCapStyle() {
    return $this->dropCapStyle;
  }

  /**
   * Setter for dropCapStyle.
   *
   * @param \ChapterThree\AppleNewsAPI\Document\Styles\DropCapStyle $value
   *   DropCapStyle.
   *
   * @return $this
   */
  public function setDropCapStyle(DropCapStyle $value) {
    $this->dropCapStyle = $value;
    return $this;
  }

  /**
   * Getter for linkStyle.
   */
  public function getLinkStyle() {
    return $this->linkStyle;
  }

  /**
   * Setter for linkStyle.
   *
   * @param \ChapterThree\AppleNewsAPI\Document\Styles\TextStyle $value
   *   LinkStyle.
   *
   * @return $this
   */
  public function setLinkStyle(TextStyle $value) {
    $this->linkStyle = $value;
    return $this;
  }

  /**
   * Getter for hyphenation.
   */
  public function getHyphenation() {
    return $this->hyphenation;
  }

  /**
   * Setter for hyphenation.
   *
   * @param bool $value
   *   Hyphenation.
   *
   * @return $this
   */
  public function setHyphenation($value) {
    $this->hyphenation = $value;
    return $this;
  }

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {
    $valid = (!isset($this->textAlignment) ||
        $this->validateTextAlignment($this->textAlignment));
    if (!$valid) {
      return NULL;
    }
    return parent::jsonSerialize();
  }

  /**
   * Validates the textAlignment attribute.
   */
  protected function validateTextAlignment($value) {
    if (!in_array($value, array(
        'left',
        'center',
        'right',
        'justified',
        'none',
      ))
    ) {
      $this->triggerError('textAlignment is not valid');
      return FALSE;
    }
    return TRUE;
  }

}
