<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Components\Component.
 */

use ChapterThree\AppleNewsAPI\Document\Components\Component;
use ChapterThree\AppleNewsAPI\Document\Layouts\Layout;
use ChapterThree\AppleNewsAPI\Document\Layouts\ComponentLayout;
use ChapterThree\AppleNewsAPI\Document;

/**
 * A test class for Component.
 */
class ComponentTestClass extends Component {

  /**
   * {@inheritdoc}
   */
  public function __construct($identifier = NULL) {
    return parent::__construct('role', $identifier);
  }

}

/**
 * Tests for the Component class.
 */
class ComponentTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new ComponentTestClass();

    $expected = '{"role":"role"}';
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

    // Test assigning document level objects.
    $expected = '{"role":"role","layout":"key"}';
    $layout = new ComponentLayout();
    $document = new Document('1', 'title', 'en-us', new Layout(2, 512));
    $document->addComponentLayout('key', $layout);
    $obj->setLayout('key', $document);
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());
    @$obj->setLayout('invalid key', $document);
    $this->assertJsonStringEqualsJsonString($expected, $obj->json());

  }

}
