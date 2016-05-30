<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\ComponentLayout.
 */

use ChapterThree\AppleNewsAPI\Document\Layouts\ComponentLayout;
use ChapterThree\AppleNewsAPI\Document\ContentInset;
use ChapterThree\AppleNewsAPI\Document\Margin;

/**
 * Tests for the ComponentLayout class.
 */
class ComponentLayoutTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new ComponentLayout();

    $expected = '{}';
    $this->assertEquals($expected, $obj->json());

    // Test validation.
    @$obj->setMargin(new \stdClass());
    $this->assertEquals($expected, $obj->json());
    @$obj->setContentInset(new \stdClass());
    $this->assertEquals($expected, $obj->json());
    @$obj->setIgnoreDocumentMargin('asdf');
    $this->assertEquals($expected, $obj->json());
    @$obj->setIgnoreDocumentGutter('asdf');
    $this->assertEquals($expected, $obj->json());
    @$obj->setMinimumHeight('1asdf');
    $this->assertEquals($expected, $obj->json());

  }

}
