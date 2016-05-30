<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Components\Illustrator.
 */

use ChapterThree\AppleNewsAPI\Document\Components\Illustrator;

/**
 * Tests for the Illustrator class.
 */
class IllustratorTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new Illustrator('some illustrator text.');

    // Optional properties.
    $expected = '{"role":"illustrator","text":"some illustrator text."}';
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
