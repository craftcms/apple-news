<?php

/**
 * @file
 * An Apple News Document MediumRectangleAdvertisement.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components\Advertisements;

use ChapterThree\AppleNewsAPI\Document\Components\Component;

/**
 * An Apple News Document MediumRectangleAdvertisement.
 */
class MediumRectangleAdvertisement extends Component {

  /**
   * Implements __construct().
   *
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($identifier = NULL) {
    parent::__construct('medium_rectangle_advertisement', $identifier);
  }

}
