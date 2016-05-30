<?php

/**
 * @file
 * An Apple News Document Fill.
 */

namespace ChapterThree\AppleNewsAPI\Document\Styles\Fills;

use ChapterThree\AppleNewsAPI\Document\Base;

/**
 * An Apple News Document Fill.
 */
abstract class Fill extends Base {

  protected $type;
  protected $attachment;

  /**
   * Implements __construct().
   *
   * @param mixed $type
   *   Text.
   */
  public function __construct($type) {
    $this->setType($type);
  }

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'attachment',
    ));
  }

  /**
   * Getter for type.
   */
  public function getType() {
    return (string) $this->type;
  }

  /**
   * Setter for type.
   *
   * @param bool $value
   *   Type.
   *
   * @return $this
   */
  public function setType($value) {
    $this->type = $value;
    return $this;
  }

  /**
   * Getter for attachment.
   */
  public function getAttachment() {
    return $this->attachment;
  }

  /**
   * Setter for attachment.
   *
   * @param bool $value
   *   Attachment.
   *
   * @return $this
   */
  public function setAttachment($value) {
    $this->attachment = $value;
    return $this;
  }

}
