<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Behaviors\Parallax.
 */

use ChapterThree\AppleNewsAPI\Document\Behaviors\Parallax;

/**
 * Tests for the Parallax class.
 */
class ParallaxTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $expected = '{"type":"parallax"}';

    $obj = new Parallax();
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
