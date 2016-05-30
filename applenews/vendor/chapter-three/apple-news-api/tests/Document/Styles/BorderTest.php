<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Styles\Border.
 */

use ChapterThree\AppleNewsAPI\Document\Styles\Border;
use ChapterThree\AppleNewsAPI\Document\Styles\StrokeStyle;

/**
 * Tests for the ComponentStyle class.
 */
class BorderTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new Border();

    $json = '{}';
    $this->assertEquals($json, $obj->json());

    // Optional properties.
    $json = '{"all":{}}';

    $obj->setAll(new StrokeStyle());
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

    $json = '{"all":{},"top":true,"bottom":false,"left":true,"right":false}';

    $obj->setTop(TRUE)
      ->setBottom(FALSE)
      ->setLeft(TRUE)
      ->setRight(FALSE);
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

  }

}
