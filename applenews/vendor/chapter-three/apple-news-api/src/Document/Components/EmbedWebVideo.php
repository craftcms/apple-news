<?php

/**
 * @file
 * An Apple News Document EmbedWebVideo.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document EmbedWebVideo.
 */
class EmbedWebVideo extends Component {

  protected $URL;

  protected $aspectRatio;
  protected $caption;
  protected $accessibilityCaption;
  protected $explicitContent;

  /**
   * Implements __construct().
   *
   * @param string $url
   *   Role.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($url, $identifier = NULL) {
    parent::__construct('embedwebvideo', $identifier);
    $this->setUrl($url);
  }

  /**
   * {@inheritdoc}
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'caption',
      'aspectRatio',
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
   * Getter for aspectRatio.
   */
  public function getAspectRatio() {
    return $this->aspectRatio;
  }

  /**
   * Setter for aspectRatio.
   *
   * @param bool $value
   *   ExplicitContent.
   *
   * @return $this
   */
  public function setAspectRatio($value) {
    $this->aspectRatio = $value;
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
  public function setExplicitContent($value = false) {
    $this->explicitContent = $value;
    return $this;
  }

}
