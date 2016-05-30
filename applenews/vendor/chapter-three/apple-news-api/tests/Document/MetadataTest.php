<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Metadata.
 */

use ChapterThree\AppleNewsAPI\Document\Metadata;

/**
 * Tests for the Metadata class.
 */
class MetadataTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new Metadata();

    $json = '{}';
    $this->assertEquals($json, $obj->json());

    // Test validation.
    for ($i = 0; $i < 50; $i++) {
      $obj->addKeyword('a');
    }
    @$obj->addKeyword('a');
    $this->assertEquals(50, count($obj->getKeywords()),
      'Max 50 keywords.');

  }

}
