<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Components\Author.
 */

use ChapterThree\AppleNewsAPI\Document\Components\Author;

/**
 * Tests for the Author class.
 */
class AuthorTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new Author('some author text.');

    // Optional properties.
    $expected = '{"role":"author","text":"some author text."}';
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
