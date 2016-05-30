<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document.
 */

use ChapterThree\AppleNewsAPI\Document;
use ChapterThree\AppleNewsAPI\Document\Components\Body;
use ChapterThree\AppleNewsAPI\Document\Layouts\Layout;
use ChapterThree\AppleNewsAPI\Document\Styles\ComponentTextStyle;

/**
 * Tests using Document class.
 */
class DocumentTest extends PHPUnit_Framework_TestCase {

  /**
   * Test missing required properties when casting to json string.
   */
  public function testJsonRequired() {

    // @todo When an for instance ComponentTextStyle adds a new DropCapStyle()
    // without the required numberOfLines property, no error is thrown in
    // Base::__toString().
  }

  /**
   * Setting properties and outputting json.
   *
   * @depends testJsonRequired
   */
  public function testSetters() {

    $obj = new Document(1, 'title', 'en', new Layout(7, 1024));
    $obj->addComponent(new Body('body text'))
      ->addComponentTextStyle('default', new ComponentTextStyle());

    $expected = '{"version":"' . $obj->getVersion() . '","identifier":"1","title":"title","language":"en","layout":{"columns":7,"width":1024},"components":[{"text":"body text","role": "body"}],"componentTextStyles":{"default":{}}}';

    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    // Optional properties.
    $expected = '{"version":"' . $obj->getVersion() . '","identifier":"1","title":"title","subtitle":"subtitle","language":"en","layout":{"columns":7,"width":1024},"components":[{"text":"body text","role": "body"}],"componentTextStyles":{"default":{}}}';

    $obj->setSubtitle('subtitle');
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    // Test validation.
    $obj = new Document(1, 'title', 'en', new Layout(7, 1024));
    $this->assertEquals(FALSE, $obj->json());

  }

}
