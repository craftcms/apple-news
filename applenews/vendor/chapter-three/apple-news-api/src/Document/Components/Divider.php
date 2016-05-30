<?php

/**
 * @file
 * An Apple News Document Divider.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document\Styles\StrokeStyle;

/**
 * An Apple News Document Divider.
 */
class Divider extends Component {

  protected $stroke;

  /**
   * Implements __construct().
   *
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($identifier = NULL) {
    parent::__construct('divider', $identifier);
  }

  /**
   * {@inheritdoc}
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'stroke',
    ));
  }

  /**
   * Getter for Stroke.
   */
  public function getStroke() {
    return $this->stroke;
  }

  /**
   * Setter for Stroke.
   *
   * @param \ChapterThree\AppleNewsAPI\Document\Styles\StrokeStyle $stroke
   *   StrokeStyle.
   *
   * @return $this
   */
  public function setStroke(StrokeStyle $stroke) {
    $this->stroke = $stroke;
    return $this;
  }

}
