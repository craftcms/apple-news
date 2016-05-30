<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Animations\ComponentAnimations\MoveInAnimation.
 */

use ChapterThree\AppleNewsAPI\Document\Animations\ComponentAnimations\MoveInAnimation;

/**
 * Tests for the MoveInAnimation class.
 */
class MoveInAnimationTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $expected = '{"type":"move_in"}';

    $obj = new MoveInAnimation();
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    // Test validation.
    @$obj->setPreferredStartingPosition('random');
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
