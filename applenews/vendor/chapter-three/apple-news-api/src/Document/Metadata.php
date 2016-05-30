<?php

/**
 * @file
 * An Apple News Document.
 */

namespace ChapterThree\AppleNewsAPI\Document;

/**
 * An Apple News Document.
 */
class Metadata extends Base {

  protected $datePublished;
  protected $dateCreated;
  protected $dateModified;
  protected $authors;
  protected $generatorName;
  protected $generatorVersion;
  protected $generatorIdentifier;
  protected $canonicalURL;
  protected $thumbnailURL;
  protected $keywords;
  protected $excerpt;
  protected $campaignData;
  protected $transparentToolbar;

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'datePublished',
      'dateCreated',
      'dateModified',
      'authors',
      'generatorName',
      'generatorVersion',
      'generatorIdentifier',
      'canonicalURL',
      'thumbnailURL',
      'keywords',
      'excerpt',
      'campaignData',
      'transparentToolbar',
    ));
  }

  /**
   * Getter for datePublished.
   */
  public function getDatePublished() {
    return $this->datePublished;
  }

  /**
   * Setter for datePublished.
   *
   * @param string $value
   *   DatePublished.
   *
   * @return $this
   */
  public function setDatePublished($value) {
    $this->datePublished = $value;
    return $this;
  }

  /**
   * Getter for dateCreated.
   */
  public function getDateCreated() {
    return $this->dateCreated;
  }

  /**
   * Setter for dateCreated.
   *
   * @param string $value
   *   DateCreated.
   *
   * @return $this
   */
  public function setDateCreated($value) {
    $this->dateCreated = $value;
    return $this;
  }

  /**
   * Getter for dateModified.
   */
  public function getDateModified() {
    return $this->dateModified;
  }

  /**
   * Setter for dateModified.
   *
   * @param string $value
   *   DateModified.
   *
   * @return $this
   */
  public function setDateModified($value) {
    $this->dateModified = $value;
    return $this;
  }

  /**
   * Getter for authors.
   */
  public function getAuthors() {
    return $this->authors;
  }

  /**
   * Setter for authors.
   *
   * @param string $value
   *   Author.
   *
   * @return $this
   */
  public function addAuthor($value) {
    $this->authors[] = $value;
    return $this;
  }

  /**
   * Getter for generatorName.
   */
  public function getGeneratorName() {
    return $this->generatorName;
  }

  /**
   * Setter for generatorName.
   *
   * @param string $value
   *   GeneratorName.
   *
   * @return $this
   */
  public function setGeneratorName($value) {
    $this->generatorName = $value;
    return $this;
  }

  /**
   * Getter for generatorVersion.
   */
  public function getGeneratorVersion() {
    return $this->generatorVersion;
  }

  /**
   * Setter for generatorVersion.
   *
   * @param string $value
   *   GeneratorVersion.
   *
   * @return $this
   */
  public function setGeneratorVersion($value) {
    $this->generatorVersion = $value;
    return $this;
  }

  /**
   * Getter for generatorIdentifier.
   */
  public function getGeneratorIdentifier() {
    return $this->generatorIdentifier;
  }

  /**
   * Setter for generatorIdentifier.
   *
   * @param string $value
   *   GeneratorIdentifier.
   *
   * @return $this
   */
  public function setGeneratorIdentifier($value) {
    $this->generatorIdentifier = $value;
    return $this;
  }

  /**
   * Getter for canonicalURL.
   */
  public function getCanonicalURL() {
    return $this->canonicalURL;
  }

  /**
   * Setter for canonicalURL.
   *
   * @param string $value
   *   CanonicalURL.
   *
   * @return $this
   */
  public function setCanonicalURL($value) {
    $this->canonicalURL = $value;
    return $this;
  }

  /**
   * Getter for thumbnailURL.
   */
  public function getThumbnailURL() {
    return $this->thumbnailURL;
  }

  /**
   * Setter for thumbnailURL.
   *
   * @param string $value
   *   ThumbnailURL.
   *
   * @return $this
   */
  public function setThumbnailURL($value) {
    $this->thumbnailURL = $value;
    return $this;
  }

  /**
   * Getter for keywords.
   */
  public function getKeywords() {
    return $this->keywords;
  }

  /**
   * Setter for keywords.
   *
   * @param string $value
   *   Keyword.
   *
   * @return $this
   */
  public function addKeyword($value) {
    if ($this->validateKeywords($value)) {
      $this->keywords[] = $value;
    }
    return $this;
  }

  /**
   * Getter for excerpt.
   */
  public function getExcerpt() {
    return $this->excerpt;
  }

  /**
   * Setter for excerpt.
   *
   * @param string $value
   *   Excerpt.
   *
   * @return $this
   */
  public function setExcerpt($value) {
    $this->excerpt = $value;
    return $this;
  }

  /**
   * Getter for campaignData.
   */
  public function getCampaignData() {
    return $this->campaignData;
  }

  /**
   * Setter for campaignData.
   *
   * @param array $value
   *   CampaignData.
   *
   * @return $this
   */
  public function setCampaignData(array $value) {
    $this->campaignData = (object) $value;
    return $this;
  }

  /**
   * Getter for transparentToolbar.
   */
  public function getTransparentToolbar() {
    return $this->transparentToolbar;
  }

  /**
   * Setter for transparentToolbar.
   *
   * @param bool $value
   *   TransparentToolbar.
   *
   * @return $this
   */
  public function setTransparentToolbar($value) {
    $this->transparentToolbar = (bool) $value;
    return $this;
  }

  /**
   * Validates the keywords attribute.
   */
  protected function validateKeywords($value) {
    if (count($this->keywords) > 49) {
      $this->triggerError('number of keywords limited to 50.');
      return FALSE;
    }
    return TRUE;
  }

}
