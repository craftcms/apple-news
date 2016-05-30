<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Styles\Fills\Gradients\ColorStop.
 */
use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document\Styles\Fills\Gradients\ColorStop;

/**
 * Tests for the Fill class.
 */
class ColorStopTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new ColorStop('#FF0000');

    $json = '{"color":"#FF0000"}';

    $this->assertJsonStringEqualsJsonString($json, $obj->json());

    $json = '{"color":"#FF0000","location":25}';

    $obj->setLocation(25);

    $this->assertJsonStringEqualsJsonString($json, $obj->json());

  }

}
