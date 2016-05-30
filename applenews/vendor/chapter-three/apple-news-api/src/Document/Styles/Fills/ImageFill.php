<?php

/**
 * @file
 * An Apple News Document ImageFill.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles\Fills;

/**
 * An Apple News Document ImageFill.
 */
class ImageFill extends Fill {

  protected $URL;
  protected $fillMode;
  protected $verticalAlignment;
  protected $horizontalAlignment;

  /**
   * Implements __construct().
   *
   * @param string $url
   *   URL.
   */
  public function __construct($url) {
    parent::__construct('image');
    $this->setUrl($url);
  }

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'fillMode',
      'verticalAlignment',
      'horizontalAlignment',
    ));
  }

  /**
   * Getter for url.
   */
  public function getUrl() {
    return $this->URL;
  }

  /**
   * Setter for url.
   *
   * @param string $value
   *   Url.
   *
   * @return $this
   */
  public function setUrl($value) {
    $this->URL = $value;
    return $this;
  }

  /**
   * Getter for fillMode.
   */
  public function getFillMode() {
    return $this->fillMode;
  }

  /**
   * Setter for fillMode.
   *
   * @param string $value
   *   FillMode.
   *
   * @return $this
   */
  public function setFillMode($value) {
    if ($this->validateFillMode($value)) {
      $this->fillMode = $value;
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
   * Getter for horizontalAlignment.
   */
  public function getHorizontalAlignment() {
    return $this->horizontalAlignment;
  }

  /**
   * Setter for horizontalAlignment.
   *
   * @param string $value
   *   HorizontalAlignment.
   *
   * @return $this
   */
  public function setHorizontalAlignment($value) {
    if ($this->validateHorizontalAlignment($value)) {
      $this->horizontalAlignment = $value;
    }
    return $this;
  }

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {
    $valid = (!isset($this->fillMode) ||
        $this->validateFillMode($this->fillMode)) &&
      (!isset($this->verticalAlignment) ||
        $this->validateVerticalAlignment($this->verticalAlignment)) &&
      (!isset($this->horizontalAlignment) ||
        $this->validateHorizontalAlignment($this->horizontalAlignment));
    if (!$valid) {
      return NULL;
    }
    return parent::jsonSerialize();
  }

  /**
   * Validates the fillMode attribute.
   */
  protected function validateFillMode($value) {
    if (!in_array($value, array(
        'fit',
        'cover',
      ))
    ) {
      $this->triggerError('fillMode is not valid');
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validates the verticalAlignment attribute.
   */
  protected function validateVerticalAlignment($value) {
    if (!in_array($value, array(
        'top',
        'center',
        'bottom',
      ))
    ) {
      $this->triggerError('verticalAlignment is not valid');
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validates the horizontalAlignment attribute.
   */
  protected function validateHorizontalAlignment($value) {
    if (!in_array($value, array(
        'left',
        'center',
        'right',
      ))
    ) {
      $this->triggerError('horizontalAlignment is not valid');
      return FALSE;
    }
    return TRUE;
  }

}
