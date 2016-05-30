<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\ContentInset.
 */

use ChapterThree\AppleNewsAPI\Document\ContentInset;

/**
 * Tests for the ContentInset class.
 */
class ContentInsetTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new ContentInset();

    $json = '{}';
    $this->assertEquals($json, $obj->json());

    // Optional properties.
    $expected = '{"top":true,"right":true,"bottom":true,"left":true}';

    $obj->setTop(TRUE);
    $obj->setRight(TRUE);
    $obj->setBottom(TRUE);
    $obj->setLeft(TRUE);
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
