<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Styles\StrokeStyle.
 */

use ChapterThree\AppleNewsAPI\Document;
use ChapterThree\AppleNewsAPI\Document\Layouts\Layout;
use ChapterThree\AppleNewsAPI\Document\Styles\InlineTextStyle;
use ChapterThree\AppleNewsAPI\Document\Styles\TextStyle;

/**
 * Tests for the TextStrokeStyle class.
 */
class InlineTextStyleTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new InlineTextStyle(0, 1, new TextStyle());

    $expected = '{"rangeStart":0,"rangeLength":1,"textStyle":{}}';
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    $expected = '{"rangeStart":1,"rangeLength":10,"textStyle":{}}';
    $obj->setRangeLength(10);
    $obj->setRangeStart(1);
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    // Test assigning document level objects.
    $document = new Document('1', 'title', 'en-us', new Layout(2, 512));

    $expected = '{"rangeStart":1,"rangeLength":10,"textStyle":"key"}';
    $style = new TextStyle();
    $document->addTextStyle('key', $style);
    $obj->setTextStyle('key', $document);
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());
    @$obj->setTextStyle('invalid key', $document);
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
