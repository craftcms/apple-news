<?php

/**
 * @file
 * An Apple News Document Audio.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Audio.
 */
abstract class Audio extends Component {

  protected $URL;

  protected $caption;
  protected $imageURL;
  protected $accessibilityCaption;
  protected $explicitContent;

  /**
   * Implements __construct().
   *
   * @param mixed $role
   *   Role.
   * @param mixed $url
   *   Text.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($role, $url, $identifier = NULL) {
    parent::__construct($role, $identifier);
    $this->setUrl($url);
  }

  /**
   * {@inheritdoc}
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'caption',
      'imageURL',
      'accessibilityCaption',
      'explicitContent',
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
   * @param mixed $url
   *   Url.
   *
   * @return $this
   */
  public function setUrl($url) {
    $this->URL = $url;
    return $this;
  }

  /**
   * Getter for caption.
   */
  public function getCaption() {
    return $this->caption;
  }

  /**
   * Setter for caption.
   *
   * @param string $value
   *   Caption.
   *
   * @return $this
   */
  public function setCaption($value) {
    $this->caption = (string) $value;
    return $this;
  }

  /**
   * Getter for imageURL.
   */
  public function getImageURL() {
    return $this->imageURL;
  }

  /**
   * Setter for imageURL.
   *
   * @param string $value
   *   ImageURL.
   *
   * @return $this
   */
  public function setImageURL($value) {
    $this->imageURL = (string) $value;
    return $this;
  }

  /**
   * Getter for accessibilityCaption.
   */
  public function getAccessibilityCaption() {
    return $this->accessibilityCaption;
  }

  /**
   * Setter for accessibilityCaption.
   *
   * @param string $value
   *   AccessibilityCaption.
   *
   * @return $this
   */
  public function setAccessibilityCaption($value) {
    $this->accessibilityCaption = (string) $value;
    return $this;
  }

  /**
   * Getter for explicitContent.
   */
  public function getExplicitContent() {
    return $this->explicitContent;
  }

  /**
   * Setter for explicitContent.
   *
   * @param bool $value
   *   ExplicitContent.
   *
   * @return $this
   */
  public function setExplicitContent($value = false) {
    $this->explicitContent = $value;
    return $this;
  }

}
