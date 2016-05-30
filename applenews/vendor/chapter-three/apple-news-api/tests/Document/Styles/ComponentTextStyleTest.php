<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\ComponentTextStyle.
 */

use ChapterThree\AppleNewsAPI\Document\Styles\ComponentTextStyle;
use ChapterThree\AppleNewsAPI\Document\Styles\DropCapStyle;
use ChapterThree\AppleNewsAPI\Document\Styles\TextStyle;

/**
 * Tests for the ComponentLayout class.
 */
class ComponentTextStyleTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new ComponentTextStyle();

    $json = '{}';
    $this->assertEquals($json, $obj->json());

    // Optional properties.
    $json = '{"dropCapStyle":{"numberOfLines":3},"linkStyle":{}}';

    $obj->setDropCapStyle(new DropCapStyle(3))
      ->setLinkStyle(new TextStyle());
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

    $json = '{"dropCapStyle":{"numberOfLines":3},"linkStyle":{},"textAlignment":"right","lineHeight":14,"hyphenation":true}';

    $obj->setTextAlignment('right')
      ->setLineHeight(14)
      ->setHyphenation(TRUE);
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

  }

}
