<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Styles\ShadowStyle.
 */

use ChapterThree\AppleNewsAPI\Document\Styles\Offset;
use ChapterThree\AppleNewsAPI\Document\Styles\ShadowStyle;

/**
 * Tests for the ShadowStyleTest class.
 */
class ShadowStyleTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new ShadowStyle('#000000', 50);

    $json = '{"color":"#000000","radius":50}';
    $this->assertEquals($json, $obj->json());

    // Test Validation.
    @$obj->setColor('000000');
    $this->assertEquals($json, $obj->json());
    @$obj->setColor('blue');
    $this->assertEquals($json, $obj->json());

    @$obj->setRadius(1000);
    $this->assertEquals($json, $obj->json());
    @$obj->setRadius('asdf');
    $this->assertEquals($json, $obj->json());

    @$obj->setOpacity(1000);
    $this->assertEquals($json, $obj->json());
    @$obj->setOpacity('asdf');
    $this->assertEquals($json, $obj->json());

    // Optional properties.
    $json = '{"color":"#FFC800","radius":50,"opacity":50,"offset":{"x":50,"y":50}}';

    @$obj->setColor('#FFC800');
    @$obj->setRadius(50);
    @$obj->setOpacity(50);
    @$obj->setOffset(new Offset(50, 50));
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

  }

}
