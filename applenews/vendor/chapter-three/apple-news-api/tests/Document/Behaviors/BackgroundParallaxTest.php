<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Behaviors\BackgroundParallax.
 */

use ChapterThree\AppleNewsAPI\Document\Behaviors\BackgroundParallax;

/**
 * Tests for the BackgroundParallax class.
 */
class BackgroundParallaxTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $expected = '{"type":"background_parallax"}';

    $obj = new BackgroundParallax();
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
