<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Components\Tweet.
 */

use ChapterThree\AppleNewsAPI\Document\Components\FacebookPost;

/**
 * Tests for the FacebookPost class.
 */
class FacebookPostTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new FacebookPost('https://www.facebook.com/applemusic/posts/1372231696125877');

    // Optional properties.
    $expected = '{"role":"facebook_post","URL":"https://www.facebook.com/applemusic/posts/1372231696125877"}';
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
