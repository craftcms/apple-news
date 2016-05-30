<?php

/**
 * @file
 * An Apple News Document VideoFill.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles\Fills;

/**
 * An Apple News Document VideoFill.
 */
class VideoFill extends Fill {

  protected $URL;
  protected $stillURL;
  protected $fillMode;
  protected $verticalAlignment;
  protected $horizontalAlignment;

  /**
   * Implements __construct().
   *
   * @param string $url
   *   URL.
   * @param string $stillURL
   *   URL.
   */
  public function __construct($url, $stillURL) {
    parent::__construct('video');
    $this->setUrl($url);
    $this->setStillURL($stillURL);
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
   * Getter for stillURL.
   */
  public function getStillURL() {
    return $this->stillURL;
  }

  /**
   * Setter for stillURL.
   *
   * @param string $value
   *   Url.
   *
   * @return $this
   */
  public function setStillURL($value) {
    $this->stillURL = $value;
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
  public function setFillMode($value = 'cover') {
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
  public function setVerticalAlignment($value = 'center') {
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
  public function setHorizontalAlignment($value = 'center') {
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
