<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Base.
 */

use ChapterThree\AppleNewsAPI\Document\Base;

/**
 * A test class for Base.
 */
class BaseTestAttributesClass extends Base {

  protected $required;
  protected $optional;

  /**
   * {@inheritdoc}
   */
  public function optional() {
    return array('optional');
  }

  /**
   * Setter for required.
   */
  public function setRequired($value) {
    $this->required = $value;
    return $this;
  }

}

/**
 * A test class for Base.
 */
class BaseTestValidationClass extends Base {

  /**
   * Expose for testing.
   */
  public static function isSupportedUnit($value) {
    return parent::isSupportedUnit($value);
  }

  /**
   * Expose for testing.
   */
  public static function isUnitInterval($value) {
    return parent::isUnitInterval($value);
  }

  /**
   * Expose for testing.
   */
  public static function isHexColor($value) {
    return parent::isHexColor($value);
  }

}

/**
 * Tests for the Base class.
 */
class BaseTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new BaseTestAttributesClass();

    // Missing required.
    $this->assertEquals(FALSE, @$obj->json());

    $json = '{"required":"asdf"}';
    $obj->setRequired('asdf');
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

    $obj = new BaseTestValidationClass();

    // PHP "arrays" by default get converted to JSON arrays.
    $json = '{}';
    $this->assertEquals($json, $obj->json());

    foreach (array(
        // Out of range.
        2, 1.1, -1, -0.1,
      ) as $value
    ) {
      $this->assertEquals(FALSE,
        @BaseTestValidationClass::isUnitInterval($value));
    }
    foreach (array(
        0, 1, 0.1,
      ) as $value
    ) {
      $this->assertEquals(TRUE,
        BaseTestValidationClass::isUnitInterval($value));
    }

    foreach (array(
        1.1, 'asdf',
      ) as $value
    ) {
      $this->assertEquals(FALSE,
        @BaseTestValidationClass::isSupportedUnit($value));
    }
    foreach (array(
        // Integers.
        1, 10, 515,
        // With unit.
        '1vh', '10vw', '515vmin', '1vmax', '1gut', '1cw', '1pt',
      ) as $value
    ) {
      $this->assertEquals(TRUE,
        BaseTestValidationClass::isSupportedUnit($value));
    }

    foreach (array(
        // Wrong number or digits.
        '#00', '#0000000000', '#',
        // Missing "#".
        '000', '000000', '00000000',
        // Not uppercase.
        '#fff', '#ffffff', '#ffffffff',
        // Digit out of range.
        '#00G', '#G00000', '#00G00000',
        // Not hex color.
        'blue', 'black',
      ) as $value
    ) {
      $this->assertEquals(FALSE,
        @BaseTestValidationClass::isHexColor($value));
    }
    foreach (array(
        '#000', '#FFF', '#3C6',
        '#000000', '#FFFFFF', '#3C69F0',
        '#00000000', '#FFFFFFFF', '#3C69F0E1',
      ) as $value
    ) {
      $this->assertEquals(TRUE,
        BaseTestValidationClass::isHexColor($value));
    }

  }

}
