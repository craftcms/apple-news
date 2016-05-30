<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Layouts\Layout.
 */

use ChapterThree\AppleNewsAPI\Document\Layouts\Layout;

/**
 * Tests for the Layout class.
 */
class LayoutTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new Layout(7, 1024);

    $json = '{"columns":7,"width":1024}';
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

  }

}
