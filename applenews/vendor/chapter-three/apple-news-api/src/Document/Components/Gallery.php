<?php

/**
 * @file
 * An Apple News Document Gallery.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document\GalleryItem;

/**
 * An Apple News Document Gallery.
 */
class Gallery extends Component {

  protected $items;

  /**
   * Implements __construct().
   *
   * @param array|\ChapterThree\AppleNewsAPI\Document\GalleryItem $items
   *   GalleryItem items.
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct(array $items, $identifier = NULL) {
    parent::__construct('gallery', $identifier);
    $this->setItems($items);
  }

  /**
   * Getter for items.
   */
  public function getItems() {
    return $this->items;
  }

  /**
   * Setter for items.
   *
   * @param array $value
   *   Items.
   *
   * @return $this
   */
  public function setItems(array $value) {
    if (isset($value[0]) &&
        is_object($value[0]) &&
        !$value[0] instanceof GalleryItem
    ) {
      $this->triggerError('Object not of type GalleryItem');
    }
    else {
      $this->items = $value;
    }
    return $this;
  }

}
