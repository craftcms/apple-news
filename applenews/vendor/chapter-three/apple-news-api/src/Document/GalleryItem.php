<?php

/**
 * @file
 * An Apple News Document GalleryItem.
 */

namespace ChapterThree\AppleNewsAPI\Document;

/**
 * An Apple News Document GalleryItem.
 */
class GalleryItem extends Base {

  protected $URL;

  protected $caption;
  protected $accessibilityCaption;
  protected $explicitContent;

  /**
   * Implements __construct().
   *
   * @param string $url
   *   URL (bundle:// format).
   */
  public function __construct($url) {
    $this->setUrl($url);
  }

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'caption',
      'accessibilityCaption',
      'explicitContent',
    ));
  }

  /**
   * Getter for URL.
   */
  public function getUrl() {
    return $this->URL;
  }

  /**
   * Setter for URL.
   *
   * @param string $URL
   *   URL.
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
  public function setExplicitContent($value) {
    $this->explicitContent = $value;
    return $this;
  }

}
