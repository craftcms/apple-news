<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Styles\TextStrokeStyle.
 */

use ChapterThree\AppleNewsAPI\Document\Styles\TextStrokeStyle;

/**
 * Tests for the TextStrokeStyle class.
 */
class TextStrokeStyleTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new TextStrokeStyle();

    $json = '{}';
    $this->assertEquals($json, $obj->json());

    // Test Validation.
    @$obj->setColor('000000');
    $this->assertEquals($json, $obj->json());
    @$obj->setColor('#00000');
    $this->assertEquals($json, $obj->json());
    @$obj->setColor('blue');
    $this->assertEquals($json, $obj->json());

    // Optional properties.
    $json = '{"color":"#FFC800"}';

    $obj->setColor('#FFC800');
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

  }

}
