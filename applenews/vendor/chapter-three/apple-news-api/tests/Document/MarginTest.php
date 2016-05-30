<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Margin.
 */

use ChapterThree\AppleNewsAPI\Document\Margin;

/**
 * Tests for the Margin class.
 */
class MarginTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new Margin();

    $json = '{}';
    $this->assertEquals($json, $obj->json());

    // Test validation.
    @$obj->setTop('67rndm');
    @$obj->setBottom('57rndm');
    $this->assertEquals($json, $obj->json());

    // Optional properties.
    $expected = '{"top":"10vh","bottom":15}';

    $obj->setTop('10vh');
    $obj->setBottom(15);
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
