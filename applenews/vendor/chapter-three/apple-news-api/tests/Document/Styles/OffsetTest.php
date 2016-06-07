<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Styles\OffsetTest.
 */

use ChapterThree\AppleNewsAPI\Document\Styles\Offset;

/**
 * Tests for the OffsetTest class.
 */
class OffsetTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $json = '{"x":50,"y":-50}';

    $obj = new Offset(50, -50);
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

    $obj->setX('asdf');
    $this->assertJsonStringEqualsJsonString($json, $obj->json());
    $obj->setY('asdf');
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

  }

}
