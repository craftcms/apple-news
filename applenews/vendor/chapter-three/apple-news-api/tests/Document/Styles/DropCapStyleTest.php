<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Styles\DropCapStyle.
 */

use ChapterThree\AppleNewsAPI\Document\Styles\DropCapStyle;

/**
 * Tests for the DropCapStyle class.
 */
class DropCapStyleTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new DropCapStyle(3);
    $json = '{"numberOfLines":3}';
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

    // Test Validation.
    @$obj->setTextColor('000000');
    $this->assertEquals($json, $obj->json());
    @$obj->setTextColor('#00000');
    $this->assertEquals($json, $obj->json());
    @$obj->setTextColor('blue');
    $this->assertEquals($json, $obj->json());
    @$obj->setBackgroundColor('000000');
    $this->assertEquals($json, $obj->json());
    @$obj->setBackgroundColor('#00000');
    $this->assertEquals($json, $obj->json());
    @$obj->setBackgroundColor('blue');
    $this->assertEquals($json, $obj->json());

    // Optional properties.
    $json = '{"numberOfLines":3,"numberOfCharacters":2,"fontName":"HelveticaNeue","textColor":"#FFF","backgroundColor":"#000000","padding":5}';

    $obj->setNumberOfCharacters(2)
      ->setFontName('HelveticaNeue')
      ->setTextColor('#FFF')
      ->setBackgroundColor('#000000')
      ->setPadding(5);
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

  }

}
