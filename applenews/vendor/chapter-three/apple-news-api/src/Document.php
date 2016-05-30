<?php

/**
 * @file
 * An Apple News Document.
 */

namespace ChapterThree\AppleNewsAPI;

use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document\Components\Component;
use ChapterThree\AppleNewsAPI\Document\Components\ComponentNested;
use ChapterThree\AppleNewsAPI\Document\Metadata;
use ChapterThree\AppleNewsAPI\Document\Styles\ComponentTextStyle;
use ChapterThree\AppleNewsAPI\Document\Styles\DocumentStyle;
use ChapterThree\AppleNewsAPI\Document\Styles\TextStyle;
use ChapterThree\AppleNewsAPI\Document\Styles\ComponentStyle;
use ChapterThree\AppleNewsAPI\Document\Layouts\Layout;
use ChapterThree\AppleNewsAPI\Document\Layouts\ComponentLayout;

/**
 * An Apple News Document.
 */
class Document extends Base {

  protected $version = '0.10.13';
  protected $identifier;
  protected $title;
  protected $language;
  protected $layout;
  protected $components;
  protected $componentTextStyles;

  protected $subtitle;
  protected $metadata;
  protected $documentStyle;
  protected $textStyles;
  protected $componentStyles;
  protected $componentLayouts;

  private $baseIdentifier = 1;

  /**
   * Implements __construct().
   *
   * @param mixed $identifier
   *   Identifier.
   * @param mixed $title
   *   Title.
   * @param mixed $language
   *   Language.
   * @param \ChapterThree\AppleNewsAPI\Document\Layouts\Layout $layout
   *   Layout.
   */
  public function __construct($identifier, $title, $language, Layout $layout) {
    $this->setIdentifier($identifier);
    $this->setTitle($title);
    $this->setLanguage($language);
    $this->setLayout($layout);
    $this->addComponentTextStyle('default', new ComponentTextStyle());
  }

  /**
   * Define optional properties.
   */
  protected function optional() {
    return array_merge(parent::optional(), array(
      'subtitle',
      'metadata',
      'documentStyle',
      'textStyles',
      'componentStyles',
      'componentLayouts',
    ));
  }

  /**
   * Getter for version.
   */
  public function getVersion() {
    return $this->version;
  }

  /**
   * Getter for identifier.
   */
  public function getIdentifier() {
    return $this->identifier;
  }

  /**
   * Setter for identifier.
   *
   * @param mixed $identifier
   *   Identifier.
   *
   * @return $this
   */
  public function setIdentifier($identifier) {
    $this->identifier = (string) $identifier;
    return $this;
  }

  /**
   * Getter for title.
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * Setter for title.
   *
   * @param mixed $title
   *   Title.
   *
   * @return $this
   */
  public function setTitle($title) {
    $this->title = (string) $title;
    return $this;
  }

  /**
   * Getter for language.
   */
  public function getLanguage() {
    return $this->language;
  }

  /**
   * Setter for language.
   *
   * @param mixed $language
   *   Language.
   *
   * @return $this
   */
  public function setLanguage($language) {
    $this->language = (string) $language;
    return $this;
  }

  /**
   * Getter for layout.
   *
   * @return \ChapterThree\AppleNewsAPI\Document\Layouts\Layout
   */
  public function getLayout() {
    return $this->layout;
  }

  /**
   * Setter for layout.
   *
   * @param \ChapterThree\AppleNewsAPI\Document\Layouts\Layout $layout
   *   Layout.
   *
   * @return $this
   */
  public function setLayout(Layout $layout) {
    $this->layout = $layout;
    return $this;
  }

  /**
   * Getter for child components.
   *
   * @return array
   *   List of \ChapterThree\AppleNewsAPI\Document\Components\Component.
   */
  public function getComponents() {
    return $this->components;
  }

  /**
   * Gets nested components as a flattened list.
   *
   * @return array
   *   List of \ChapterThree\AppleNewsAPI\Document\Components\Component.
   */
  public function getComponentsFlattened() {
    $components = [];
    foreach ($this->getComponents() as $component) {
      $components[] = $component;
      if ($component instanceof ComponentNested) {
        /** @var \ChapterThree\AppleNewsAPI\Document\Components\ComponentNested $component */
        $descendants = $component->getComponentsFlattened();
        array_merge($components, $descendants);
      }
    }
    return $components;
  }

  /**
   * Setter for components.
   *
   * @param Component $component
   *   Component.
   *
   * @return $this
   */
  public function addComponent(Component $component) {
    $this->components[] = $component;
    return $this;
  }

  /**
   * Getter for componentTextStyles.
   */
  public function getComponentTextStyles() {
    return $this->componentTextStyles;
  }

  /**
   * Setter for componentTextStyles.
   *
   * @param mixed $name
   *   Name.
   * @param ComponentTextStyle $component_text_style
   *   ComponentTextStyle.
   *
   * @return $this
   */
  public function addComponentTextStyle($name, ComponentTextStyle $component_text_style) {
    $this->componentTextStyles[(string) $name] = $component_text_style;
    return $this;
  }

  /**
   * Getter for subtitle.
   */
  public function getSubtitle() {
    return $this->subtitle;
  }

  /**
   * Setter for subtitle.
   *
   * @param mixed $subtitle
   *   Subtitle.
   *
   * @return $this
   */
  public function setSubtitle($subtitle) {
    $this->subtitle = (string) $subtitle;
    return $this;
  }

  /**
   * Getter for metadata.
   */
  public function getMetadata() {
    return $this->metadata;
  }

  /**
   * Setter for metadata.
   *
   * @param Metadata $metadata
   *   Metadata.
   *
   * @return $this
   */
  public function setMetadata(Metadata $metadata) {
    $this->metadata = $metadata;
    return $this;
  }

  /**
   * Getter for documentStyle.
   */
  public function getDocumentStyle() {
    return $this->documentStyle;
  }

  /**
   * Setter for documentStyle.
   *
   * @param DocumentStyle $document_style
   *   DocumentStyle.
   *
   * @return $this
   */
  public function setDocumentStyle(DocumentStyle $document_style) {
    $this->documentStyle = $document_style;
    return $this;
  }

  /**
   * Getter for textStyles.
   */
  public function getTextStyles() {
    return $this->textStyles;
  }

  /**
   * Setter for textStyles.
   *
   * @param mixed $name
   *   Name.
   * @param \ChapterThree\AppleNewsAPI\Document\Styles\TextStyle $text_style
   *   TextStyle.
   *
   * @return $this
   */
  public function addTextStyle($name, TextStyle $text_style) {
    $this->textStyles[(string) $name] = $text_style;
    return $this;
  }

  /**
   * Getter for componentStyles.
   */
  public function getComponentStyles() {
    return $this->componentStyles;
  }

  /**
   * Setter for componentStyles.
   *
   * @param mixed $name
   *   Name.
   * @param \ChapterThree\AppleNewsAPI\Document\Styles\ComponentStyle $component_style
   *   ComponentStyle.
   *
   * @return $this
   */
  public function addComponentStyle($name, ComponentStyle $component_style) {
    $this->componentStyles[(string) $name] = $component_style;
    return $this;
  }

  /**
   * Getter for componentLayouts.
   */
  public function getComponentLayouts() {
    return $this->componentLayouts;
  }

  /**
   * Setter for componentLayouts.
   *
   * @param mixed $name
   *   Name.
   * @param \ChapterThree\AppleNewsAPI\Document\Layouts\ComponentLayout $component_layout
   *   ComponentLayout.
   *
   * @return $this
   */
  public function addComponentLayout($name, ComponentLayout $component_layout) {
    $this->componentLayouts[(string) $name] = $component_layout;
    return $this;
  }

  /**
   * Implements JsonSerializable::jsonSerialize().
   */
  public function jsonSerialize() {

    if (!isset($this->componentTextStyles['default'])) {
      $msg = "Document must have at least a \"default\" ComponentTextStyle.";
      $this->triggerError($msg);
      return NULL;
    }

    return parent::jsonSerialize();
  }

  /**
   * Generates a unique identifier in this document.
   */
  public function generateIdentifier() {
    return '_' . $this->baseIdentifier++;
  }

}
