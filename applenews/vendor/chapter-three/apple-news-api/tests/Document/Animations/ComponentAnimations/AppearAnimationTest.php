<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Animations\ComponentAnimations\AppearAnimation.
 */

use ChapterThree\AppleNewsAPI\Document\Animations\ComponentAnimations\AppearAnimation;

/**
 * Tests for the AppearAnimation class.
 */
class AppearAnimationTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $expected = '{"type":"appear"}';

    $obj = new AppearAnimation();
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
