<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Components\Tweet.
 */

use ChapterThree\AppleNewsAPI\Document\Components\VinePost;

/**
 * Tests for the VinePost class.
 */
class VinePostTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new VinePost('https://vine.co/v/Ml16lZVTTxe');

    // Optional properties.
    $expected = '{"role":"vine_post","URL":"https://vine.co/v/Ml16lZVTTxe"}';
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
