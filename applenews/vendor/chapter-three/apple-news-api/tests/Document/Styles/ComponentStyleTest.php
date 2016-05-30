<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Styles\ComponentStyle.
 */

use ChapterThree\AppleNewsAPI\Document\Styles\Border;
use ChapterThree\AppleNewsAPI\Document\Styles\ComponentStyle;
use ChapterThree\AppleNewsAPI\Document\Styles\Fills\ImageFill;

/**
 * Tests for the ComponentStyle class.
 */
class ComponentStyleTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new ComponentStyle();

    $json = '{}';
    $this->assertEquals($json, $obj->json());

    // Optional properties.
    $json = '{"fill":{"type":"image","URL":"bundle://header-image.png"},"border":{}}';
    $obj->setFill(new ImageFill('bundle://header-image.png'))
      ->setBorder(new Border());
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

    $json = '{"backgroundColor":"#FFFFFF","fill":{"type":"image","URL":"bundle://header-image.png"},"opacity":1,"border":{}}';
    $obj->setBackgroundColor('#FFFFFF')
      ->setOpacity(1);
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

  }

}
