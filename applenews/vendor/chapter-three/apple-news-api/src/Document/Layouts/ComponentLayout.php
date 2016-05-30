<?php

/**
 * @file
 * An Apple News Document ComponentLayout.
 */

namespace ChapterThree\AppleNewsAPI\Document\Layouts;

use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document\Margin;
use ChapterThree\AppleNewsAPI\Document\ContentInset;

/**
 * An Apple News Document ComponentLayout.
 */
class ComponentLayout extends Base {

  protected $columnStart;
  protected $columnSpan;
  protected $margin;
  protected $contentInset;
  protected $ignoreDocumentMargin;
  protected $ignoreDocumentGutter;
  protected $minimumHeight;
  protected $maximumContentWidth;
  protected $horizontalContentAlignment;

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'columnStart',
      'columnSpan',
      'margin',
      'contentInset',
      'ignoreDocumentMargin',
      'ignoreDocumentGutter',
      'minimumHeight',
      'maximumContentWidth',
      'horizontalContentAlignment'
    ));
  }

  /**
   * Getter for columnStart.
   */
  public function getColumnStart() {
    return $this->columnStart;
  }

  /**
   * Setter for columnStart.
   *
   * @param int $value
   *   ColumnStart.
   *
   * @return $this
   */
  public function setColumnStart($value) {
    $this->columnStart = $value;
    return $this;
  }

  /**
   * Getter for columnSpan.
   */
  public function getColumnSpan() {
    return $this->columnSpan;
  }

  /**
   * Setter for columnSpan.
   *
   * @param int $value
   *   ColumnSpan.
   *
   * @return $this
   */
  public function setColumnSpan($value) {
    $this->columnSpan = $value;
    return $this;
  }

  /**
   * Getter for margin.
   */
  public function getMargin() {
    return $this->margin;
  }

  /**
   * Setter for margin.
   *
   * @param int|\ChapterThree\AppleNewsAPI\Document\Margin $value
   *   Margin.
   *
   * @return $this
   */
  public function setMargin($value) {
    if (is_object($value) && !$value instanceof Margin) {
      $this->triggerError('Object not of type Margin');
    }
    else {
      $this->margin = $value;
    }
    return $this;
  }

  /**
   * Getter for contentInset.
   */
  public function getContentInset() {
    return $this->contentInset;
  }

  /**
   * Setter for contentInset.
   *
   * @param bool|\ChapterThree\AppleNewsAPI\Document\ContentInset $value
   *   ContentInset.
   *
   * @return $this
   */
  public function setContentInset($value) {
    if (is_object($value) && !$value instanceof ContentInset) {
      $this->triggerError('Object not of type ContentInset');
    }
    else {
      $this->contentInset = $value;
    }
    return $this;
  }

  /**
   * Getter for ignoreDocumentMargin.
   */
  public function getIgnoreDocumentMargin() {
    return $this->ignoreDocumentMargin;
  }

  /**
   * Setter for ignoreDocumentMargin.
   *
   * @param float $value
   *   IgnoreDocumentMargin.
   *
   * @return $this
   */
  public function setIgnoreDocumentMargin($value = TRUE) {
    if ($this->validateIgnoreDocumentMargin($value)) {
      $this->ignoreDocumentMargin = $value;
    }
    return $this;
  }

  /**
   * Getter for ignoreDocumentGutter.
   */
  public function getIgnoreDocumentGutter() {
    return $this->ignoreDocumentGutter;
  }

  /**
   * Setter for ignoreDocumentGutter.
   *
   * @param float $value
   *   IgnoreDocumentGutter.
   *
   * @return $this
   */
  public function setIgnoreDocumentGutter($value = TRUE) {
    if ($this->validateIgnoreDocumentGutter($value)) {
      $this->ignoreDocumentGutter = $value;
    }
    return $this;
  }

  /**
   * Getter for minimumHeight.
   */
  public function getMinimumHeight() {
    return $this->minimumHeight;
  }

  /**
   * Setter for minimumHeight.
   *
   * @param int|string $value
   *   MinimumHeight.
   *
   * @return $this
   */
  public function setMinimumHeight($value) {
    if ($this->validateMinimumHeight($value)) {
      $this->minimumHeight = $value;
    }
    return $this;
  }

  /**
   * Getter for maximumContentWidth.
   */
  public function getMaximumContentWidth() {
    return $this->maximumContentWidth;
  }

  /**
   * Setter for maximumContentWidth.
   *
   * @param int|string $value
   *   maximumContentWidth.
   *
   * @return $this
   */
  public function setMaximumContentWidth($value) {
    if ($this->validateMaximumContentWidth($value)) {
      $this->maximumContentWidth = $value;
    }
    return $this;
  }

  /**
   * Getter for horizontalContentAlignment.
   */
  public function getHorizontalContentAlignment() {
    return $this->horizontalContentAlignment;
  }

  /**
   * Setter for horizontalContentAlignment.
   *
   * @param string $value
   *   horizontalContentAlignment.
   *
   * @return $this
   */
  public function setHorizontalContentAlignment($value = 'center') {
    if ($this->validateHorizontalContentAlignment($value)) {
      $this->horizontalContentAlignment = $value;
    }
    return $this;
  }

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {
    $valid = (!isset($this->ignoreDocumentMargin) ||
        $this->validateIgnoreDocumentMargin($this->ignoreDocumentMargin)) &&
      (!isset($this->ignoreDocumentGutter) ||
        $this->validateIgnoreDocumentGutter($this->ignoreDocumentGutter)) &&
      (!isset($this->minimumHeight) ||
        $this->validateMinimumHeight($this->minimumHeight));
    if (!$valid) {
      return NULL;
    }
    return parent::jsonSerialize();
  }

  /**
   * Validates the ignoreDocumentMargin attribute.
   */
  protected function validateIgnoreDocumentMargin($value) {
    if (!is_bool($value) &&
        !in_array($value, ['none', 'left', 'right', 'both'])
    ) {
      $this->triggerError('ignoreDocumentMargin is not valid');
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validates the ignoreDocumentGutter attribute.
   */
  protected function validateIgnoreDocumentGutter($value) {
    if (!is_bool($value) &&
        !in_array($value, ['none', 'left', 'right', 'both'])
    ) {
      $this->triggerError('ignoreDocumentGutter is not valid');
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validates the minimumHeight attribute.
   */
  protected function validateMinimumHeight($value) {
    if (!is_int($value) &&
        !$this->isSupportedUnit($value)
    ) {
      $this->triggerError('minimumHeight is not valid');
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validates the maximumContentWidth attribute.
   */
  protected function validateMaximumContentWidth($value) {
    if (!is_int($value) &&
        !$this->isSupportedUnit($value)
    ) {
      $this->triggerError('maximumContentWidth is not valid');
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Validates the horizontalContentAlignment attribute.
   */
  protected function validateHorizontalContentAlignment($value) {
    if (!in_array($value, ['center', 'left', 'right'])) {
      $this->triggerError('horizontalContentAlignment is not valid');
      return FALSE;
    }
    return TRUE;
  }

}
