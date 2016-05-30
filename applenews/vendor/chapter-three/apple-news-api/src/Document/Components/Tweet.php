<?php

/**
 * @file
 * An Apple News Document Tweet.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Tweet.
 */
class Tweet extends Component {

  protected $URL;

  /**
   * Implements __construct().
   *
   * @param string $url
   *   Role.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($url, $identifier = NULL) {
    parent::__construct('tweet', $identifier);
    $this->setUrl($url);
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
   * @param mixed $value
   *   Url.
   *
   * @return $this
   */
  public function setUrl($value) {
    $this->URL = $value;
    return $this;
  }

}
