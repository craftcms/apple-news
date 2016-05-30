<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Styles\Fills\Gradients\GradientFill.
 */
use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document\Styles\Fills\Fill;
use ChapterThree\AppleNewsAPI\Document\Styles\Fills\Gradients;
use ChapterThree\AppleNewsAPI\Document\Styles\Fills\Gradients\GradientFill;
use ChapterThree\AppleNewsAPI\Document\Styles\Fills\Gradients\ColorStop;

/**
 * A test class for GradientFill.
 */
class GradientFillClass extends GradientFill {

  /**
   * {@inheritdoc}
   */
  public function __construct($type, array $colorStop) {
    parent::__construct($type, $colorStop);
  }

}

/**
 * Tests for the Fill class.
 */
class GradientFillTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new GradientFillClass('linear_gradient', [new ColorStop('#FF0000'), new ColorStop('#000000')]);

    $json = '{"type":"linear_gradient","colorStops":[{"color":"#FF0000"},{"color":"#000000"}]}';

    $this->assertJsonStringEqualsJsonString($json, $obj->json());

    $json = '{"type":"linear_gradient","colorStops":[{"color":"#FF0000"},{"color":"#000000"}],"attachment":"fixed"}';
    $obj->setAttachment('fixed');

    $this->assertJsonStringEqualsJsonString($json, $obj->json());

  }

}
