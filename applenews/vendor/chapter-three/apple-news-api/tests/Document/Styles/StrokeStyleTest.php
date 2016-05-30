<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Styles\StrokeStyle.
 */

use ChapterThree\AppleNewsAPI\Document\Styles\StrokeStyle;

/**
 * Tests for the TextStrokeStyle class.
 */
class StrokeStyleTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new StrokeStyle();

    $json = '{}';
    $this->assertEquals($json, $obj->json());

    // Test Validation.
    @$obj->setColor('000000');
    $this->assertEquals($json, $obj->json());
    @$obj->setColor('#00000');
    $this->assertEquals($json, $obj->json());
    @$obj->setColor('blue');
    $this->assertEquals($json, $obj->json());

    @$obj->setStyle('asdf');
    $this->assertEquals($json, $obj->json());

    @$obj->setWidth('72rndm');
    $this->assertEquals($json, $obj->json());

    // Optional properties.
    $json = '{"color":"#FFC800","width":1,"style":"dashed"}';

    $obj->setColor('#FFC800');
    $obj->setWidth(1);
    $obj->setStyle('dashed');
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

  }

}
