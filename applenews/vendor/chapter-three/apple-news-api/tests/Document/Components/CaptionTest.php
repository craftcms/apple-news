<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Components\Caption.
 */

use ChapterThree\AppleNewsAPI\Document\Components\Caption;

/**
 * Tests for the Caption class.
 */
class CaptionTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new Caption('some caption text.');

    // Optional properties.
    $expected = '{"role":"caption","text":"some caption text."}';
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
