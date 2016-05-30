<?php

/**
 * @file
 * Base class for AppleNewsAPI\Document classes.
 */

namespace ChapterThree\AppleNewsAPI\Document;

/**
 * Base class for AppleNewsAPI\Document classes.
 */
abstract class Base implements \JsonSerializable {

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {
    // Define empty attribute values.
    // @see http://php.net/manual/en/language.types.boolean.php#language.types.boolean.casting
    $present = function($value) {
      if ($value === NULL) {
        return FALSE;
      }
      if (is_string($value)) {
        return $value !== '';
      }
      if (is_array($value)) {
        return (bool) $value;
      }
      return TRUE;
    };

    // Protected attributes are not outputted via parent::__toString().
    $out = get_object_vars($this);

    // Required attributes.
    $names = array_diff(array_keys($out), $this->optional());
    foreach ($names as $name) {
      if (!isset($out[$name]) || !$present($out[$name])) {
        $this->triggerError("Missing required attribute ${name}.");
        return NULL;
      }
    }

    // Unset optional attributes.
    $names = array_intersect($this->optional(), array_keys($out));
    foreach ($names as $name) {
      if (!$present($out[$name])) {
        unset($out[$name]);
      }
    }

    // Return empty object, not array.
    if (empty($out)) {
      return new \stdClass();
    }

    return $out;
  }

  /**
   * Implements __toString().
   */
  public function __toString() {
    return json_encode($this, JSON_UNESCAPED_SLASHES);
  }

  /**
   * Generates json representation.
   *
   * @return bool|string
   *   JSON string, or FALSE on error.
   */
  public function json() {
    $out = (string) $this;
    return $out == 'null' ? FALSE : $out;
  }

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array();
  }

  /**
   * Helper function to determine if a value is suffixed by a supported unit.
   *
   * @param mixed $value
   *   Value.
   *
   * @return bool
   *   Result.
   */
  protected static function isSupportedUnit($value) {
    $units = array(
      'vh',
      'vw',
      'vmin',
      'vmax',
      'gut',
      'cw',
      'pt',
    );
    $re = '/^[1-9][0-9]*(' . implode('|', $units) . ')?$/';
    return preg_match($re, $value);
  }

  /**
   * Helper function to determine if a value is a unit interval.
   *
   * A unit interval is the closed interval [0,1], that is, the set of all real
   * numbers that are greater than or equal to 0 and less than or equal to 1.
   *
   * @param float|int $value
   *   Value.
   *
   * @return bool
   *   Result.
   */
  protected static function isUnitInterval($value) {
    if (!is_int($value) && !is_float($value)) {
      return FALSE;
    }
    return 0 <= $value && $value <= 1;
  }

  /**
   * Helper to validate color hex code.
   *
   * Valid codes are hexadecimal numbers of length 3, 6 or 8 (with opacity),
   * prefixed with "#".
   *
   * @param string $value
   *   Value.
   *
   * @return bool
   *   Result.
   */
  protected static function isHexColor($value) {
    return preg_match('/^#[0-9A-F]+$/', $value) &&
      in_array(strlen($value), array(4, 7, 9));
  }

  /**
   * Error handler.
   *
   * @param string $message
   *   Message.
   * @param int $message_type
   *   Matching E_USER_ERROR|E_USER_WARNING|E_USER_NOTICE|E_USER_DEPRECATED.
   */
  public function triggerError($message, $message_type = E_USER_NOTICE) {
    $trace = debug_backtrace();
    trigger_error($message . ' in ' . $trace[0]['file'] . ' on line ' .
      $trace[0]['line'], $message_type);
  }

}
