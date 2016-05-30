<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Animations\ComponentAnimations\ScaleFadeAnimation.
 */

use ChapterThree\AppleNewsAPI\Document\Animations\ComponentAnimations\ScaleFadeAnimation;

/**
 * Tests for the ScaleFadeAnimation class.
 */
class ScaleFadeAnimationTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $expected = '{"type":"scale_fade"}';

    $obj = new ScaleFadeAnimation();
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    // Test validation.
    @$obj->setInitialAlpha('random');
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());
    @$obj->setInitialAlpha(1.2);
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());
    @$obj->setInitialScale('random');
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());
    @$obj->setInitialScale(1.2);
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
