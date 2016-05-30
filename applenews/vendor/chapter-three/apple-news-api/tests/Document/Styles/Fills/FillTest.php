<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Components\Text.
 */

use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document\Styles\Fills\Fill;

/**
 * A test class for Fill.
 */
class FillTestClass extends Fill {

  /**
   * {@inheritdoc}
   */
  public function __construct($type) {
    parent::__construct($type);
  }

}

/**
 * Tests for the Fill class.
 */
class FillTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new FillTestClass('video');

    $json = '{"type":"video"}';

    $this->assertJsonStringEqualsJsonString($json, $obj->json());

    // Optional properties.
    $json = '{"type":"video","attachment":"fixed"}';
    $obj->setAttachment('fixed');
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

  }

}
