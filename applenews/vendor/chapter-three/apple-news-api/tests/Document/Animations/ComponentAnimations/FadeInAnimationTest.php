<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Animations\ComponentAnimations\FadeInAnimation.
 */

use ChapterThree\AppleNewsAPI\Document\Animations\ComponentAnimations\FadeInAnimation;

/**
 * Tests for the FadeInAnimation class.
 */
class FadeInAnimationTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $expected = '{"type":"fade_in"}';

    $obj = new FadeInAnimation();
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    // Test validation.
    @$obj->setInitialAlpha('random');
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());
    @$obj->setInitialAlpha(1.2);
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
