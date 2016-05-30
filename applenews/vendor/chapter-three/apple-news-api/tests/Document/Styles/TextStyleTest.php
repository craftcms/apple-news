<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\TextStyle.
 */

use ChapterThree\AppleNewsAPI\Document\Styles\TextStyle;
use ChapterThree\AppleNewsAPI\Document\Styles\TextStrokeStyle;

/**
 * Tests for the ComponentLayout class.
 */
class TextStyleTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new TextStyle();

    $json = '{}';
    $this->assertEquals($json, $obj->json());

    // Test Validation.
    @$obj->setTextColor('000000');
    $this->assertEquals($json, $obj->json());
    @$obj->setTextColor('#00000');
    $this->assertEquals($json, $obj->json());
    @$obj->setTextColor('blue');
    $this->assertEquals($json, $obj->json());

    @$obj->setTextTransform('asdf');
    $this->assertEquals($json, $obj->json());

    @$obj->setBackgroundColor('000000');
    $this->assertEquals($json, $obj->json());
    @$obj->setBackgroundColor('#00000');
    $this->assertEquals($json, $obj->json());
    @$obj->setBackgroundColor('blue');
    $this->assertEquals($json, $obj->json());

    @$obj->setVerticalAlignment('asdf');
    $this->assertEquals($json, $obj->json());

    // Optional properties.
    $json = '{"underline":{},"strikethrough":{}}';

    $obj->setUnderline(new TextStrokeStyle());
    $obj->setStrikethrough(new TextStrokeStyle());
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

    // Reset object.
    $obj = new TextStyle();

    $json = '{"fontName":"GillSans-Bold","fontSize":12,"textColor":"#333333","textTransform":"capitalize","backgroundColor":"#FF00007F","verticalAlignment":"baseline","tracking":0.5}';

    $obj->setFontName('GillSans-Bold');
    $obj->setTextColor('#333333');
    $obj->setTextTransform('capitalize');
    $obj->setBackgroundColor('#FF00007F');
    $obj->setVerticalAlignment('baseline');
    $obj->setFontSize(12);
    $obj->setTracking(0.5);
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

  }

}
