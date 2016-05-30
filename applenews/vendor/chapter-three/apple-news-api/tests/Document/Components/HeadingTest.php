<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Components\Heading.
 */

use ChapterThree\AppleNewsAPI\Document\Components\Heading;

/**
 * Tests for the Heading class.
 */
class HeadingTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new Heading('some header text.');

    $expected = '{"role":"heading","text":"some header text."}';
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    // Validate.
    @$obj->setRole('asdf');
    $this->assertEquals(FALSE, $obj->json());

  }

}
