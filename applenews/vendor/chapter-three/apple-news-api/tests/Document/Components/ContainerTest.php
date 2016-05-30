<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Components\Container.
 */

use ChapterThree\AppleNewsAPI\Document;
use ChapterThree\AppleNewsAPI\Document\Components\Container;
use ChapterThree\AppleNewsAPI\Document\Components\Body;

/**
 * Tests for the Container class.
 */
class ContainerTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new Container();

    $expected = '{"role":"container"}';
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    $expected = '{"role":"container","components":[{"role":"body","text":"some body text."}]}';
    $obj->addComponent(new Body('some body text.'));
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
