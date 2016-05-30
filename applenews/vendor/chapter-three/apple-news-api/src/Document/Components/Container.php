<?php

/**
 * @file
 * An Apple News Document Container.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components;

/**
 * An Apple News Document Container.
 */
class Container extends ComponentNested {

  /**
   * Implements __construct().
   *
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($identifier = NULL, $role = NULL) {
    return parent::__construct('container', $identifier);
  }

}
