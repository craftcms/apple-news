<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Behaviors\Springy.
 */

use ChapterThree\AppleNewsAPI\Document\Behaviors\Springy;

/**
 * Tests for the Springy class.
 */
class SpringyTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $expected = '{"type":"springy"}';

    $obj = new Springy();
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
