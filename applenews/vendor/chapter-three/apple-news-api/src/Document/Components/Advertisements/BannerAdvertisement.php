<?php

/**
 * @file
 * An Apple News Document BannerAdvertisement.
 */

namespace ChapterThree\AppleNewsAPI\Document\Components\Advertisements;

use ChapterThree\AppleNewsAPI\Document\Components\Component;

/**
 * An Apple News Document BannerAdvertisement.
 */
class BannerAdvertisement extends Component {

  protected $bannerType;

  /**
   * Implements __construct().
   *
   * @param mixed $identifier
   *   Identifier.
   */
  public function __construct($identifier = NULL) {
    parent::__construct('banner_advertisement', $identifier);
  }

  /**
   * {@inheritdoc}
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'bannerType',
    ));
  }

  /**
   * Getter for bannerType.
   */
  public function getBannerType() {
    return $this->bannerType;
  }

  /**
   * Setter for bannerType.
   *
   * @param mixed $value
   *   bannerType.
   *
   * @return $this
   */
  public function setBannerType($value = 'any') {
    if ($this->validateBannerType($value)) {
      $this->bannerType = $value;
    }
    return $this;
  }

  /**
   * Validates the bannerType attribute.
   */
  protected function validateBannerType($value) {
    if (!in_array($value, ['any', 'standard', 'double_height', 'large'])) {
      $this->triggerError('bannerType not one of "any", "standard", "double_height" or "large".');
      return FALSE;
    }
    return TRUE;
  }

}
