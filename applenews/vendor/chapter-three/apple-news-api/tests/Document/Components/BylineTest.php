<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Components\Byline.
 */

use ChapterThree\AppleNewsAPI\Document\Components\Byline;

/**
 * Tests for the Byline class.
 */
class BylineTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new Byline('some byline text.');

    // Optional properties.
    $expected = '{"role":"byline","text":"some byline text."}';
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
