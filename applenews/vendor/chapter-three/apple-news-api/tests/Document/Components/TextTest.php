<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Components\Text.
 */

use ChapterThree\AppleNewsAPI\Document;
use ChapterThree\AppleNewsAPI\Document\Components\Text;
use ChapterThree\AppleNewsAPI\Document\Layouts\Layout;
use ChapterThree\AppleNewsAPI\Document\Styles\ComponentTextStyle;
use ChapterThree\AppleNewsAPI\Document\Styles\InlineTextStyle;
use ChapterThree\AppleNewsAPI\Document\Styles\TextStyle;

/**
 * A test class for Text.
 */
class TextTestClass extends Text {

  /**
   * {@inheritdoc}
   */
  public function __construct($text, $identifier = NULL) {
    return parent::__construct('role', $text, $identifier);
  }

}

/**
 * Tests for the Text class.
 */
class TextTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new TextTestClass('some text');

    $expected = '{"role":"role","text":"some text"}';
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    $expected = '{"role":"role","text":"some other text"}';
    $obj->setText('some other text');
    $this->assertEquals('some other text', $obj->getText());
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    $expected = '{"role":"role","text":"some other text","format":"markdown"}';
    $obj->setFormat('markdown');
    $this->assertEquals('markdown', $obj->getFormat());
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    // Test assigning document level objects.
    $document = new Document('1', 'title', 'en-us', new Layout(2, 512));

    $expected = '{"role":"role","text":"some other text","format":"markdown","textStyle":"key"}';
    $style = new ComponentTextStyle();
    $document->addComponentTextStyle('key', $style);
    $obj->setTextStyle('key', $document);
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());
    @$obj->setTextStyle('invalid key', $document);
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    $expected = '{"role":"role","text":"some other text","format":"markdown","textStyle":"key","inlineTextStyles":[{"rangeStart":0,"rangeLength":1,"textStyle":{}}]}';
    $style = new InlineTextStyle(0, 1, new TextStyle());
    $obj->addInlineTextStyles($style);
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
