<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Anchor.
 */

use ChapterThree\AppleNewsAPI\Document\Anchor;

/**
 * Tests for the Anchor class.
 */
class AnchorTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    // Required properties.
    $expected = '{"targetAnchorPosition":"bottom"}';

    $obj = new Anchor('bottom');
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    // Test validation.
    @$obj->setTargetAnchorPosition('asdf');
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());
    @$obj->setRangeStart(5);
    $this->assertEquals(FALSE, $obj->json());

    // Optional properties.
    $expected = '{"targetAnchorPosition":"bottom","originAnchorPosition":"top","targetComponentIdentifier":"02767FCB-3901-4340-B403-96CDEAF76EE8","rangeStart":5,"rangeLength":20}';

    $obj->setOriginAnchorPosition('top')
      ->setTargetComponentIdentifier('02767FCB-3901-4340-B403-96CDEAF76EE8')
      ->setRangeLength(20);
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
