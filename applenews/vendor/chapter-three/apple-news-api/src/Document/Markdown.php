<?php

/**
 * @file
 * Handling of Apple News Document markdown.
 */

namespace ChapterThree\AppleNewsAPI\Document;

/**
 * Handling of Apple News Document markdown.
 */
class Markdown {

  // Markdown special characters.
  const ESCAPED_CHARACTERS = '\`*_{}[]()#+-!';

  // Markdown block delimiter.
  const BLOCK_DELIMITER = "\n\n";

  /**
   * Escape Markdown special characters.
   *
   * @param string $string
   *   A string without any Markdown.
   *
   * @return mixed
   *   $string with any special characters escaped.
   */
  public static function escape($string) {
    $escaped_chars = str_split(self::ESCAPED_CHARACTERS);
    $escaped_chars_replace = array_map(function($char) {
      return '\\' . $char;
    }, $escaped_chars);
    $string = str_replace($escaped_chars, $escaped_chars_replace, $string);
    // Ignored whitespace.
    return preg_replace('/\s+/u', ' ', $string);
  }

  /**
   * @var \DOMDocument
   */
  public $dom;
  
  /**
   * @var array
   */
  public $white_list;

  /**
   * Implements __construct().
   * 
   * @param array $white_list
   *   An array of inline-type element names to preserve in situ.
   */
  public function __construct($white_list = []) {
    $this->white_list = $white_list;
  }

  /**
   * Convert HTML to Apple News Markdown.
   *
   * @param string $html
   *   HTML to convert. Value is not validated, it is caller's responsibility
   *   to validate.
   *
   * @return string|NULL
   *   Markdown representation of the HTML, or NULL if failed.
   */
  public function convert($html) {
    if (preg_match('/^\s*$/u', $html)) {
      return '';
    }
    $html = '<html><head><meta http-equiv="content-type" content="text/html; charset=UTF-8"></head>' . $html . '</body></html>';
    $this->dom = new \DOMDocument();
    if (!$this->dom->loadHTML($html)) {
      return NULL;
    }
    $xp = new \DOMXPath($this->dom);
    return implode(self::BLOCK_DELIMITER, $this->getBlocks(
      $xp->query('/html/body')->item(0)->childNodes
    ));
  }

  /**
   * Converts a \DOMNodeList into a series of Markdown blocks.
   *
   * @param \DOMNodeList|array $nodes
   *   DOM nodes.
   *
   * @return array
   *   Array of string Markdown blocks.
   */
  protected function getBlocks($nodes) {
    // Each completed block.
    $blocks = array();
    // Container for any top-level inline elements.
    $block = '';
    // Add a completed block to $blocks.
    $add_block = function($string = NULL) use(&$blocks, &$block) {
      if (preg_match('/\S/u', $block)) {
        $blocks[] = trim($block, " \t");
      }
      if (preg_match('/\S/u', $string)) {
        $blocks[] = rtrim($string, " \t");
      }
      $block = '';
    };
    /** @var \DOMNode $node */
    foreach ($nodes as $node) {
      switch ($node->nodeType) {

        case XML_ELEMENT_NODE:
          /** @var \DOMElement $node */
          switch ($node->nodeName) {

            // Explicitly handle these elements.

            case 'p':
            case 'pre':
              $add_block($this->getBlock($node));
              break;

            case 'h1':
              $add_block('# ' . $this->getBlock($node));
              break;

            case 'h2':
              $add_block('## ' . $this->getBlock($node));
              break;

            case 'h3':
              $add_block('### ' . $this->getBlock($node));
              break;

            case 'h4':
              $add_block('#### ' . $this->getBlock($node));
              break;

            case 'h5':
              $add_block('##### ' . $this->getBlock($node));
              break;

            case 'h6':
              $add_block('###### ' . $this->getBlock($node));
              break;

            case 'hr':
              $add_block('***');
              break;

            case 'ul':
            case 'ol':
            case 'dl':
              $add_block($this->getBlockList($node));
              break;

            // Other block-level elements.
            case 'article':
            case 'aside':
            case 'blockquote':
            case 'dd':
            case 'div':
            case 'fieldset':
            case 'figcaption':
            case 'figure':
            case 'footer':
            case 'form':
            case 'header':
            case 'hgroup':
            case 'main':
            case 'nav':
            case 'output':
            case 'section':
              // Recursively handle any descendant elements we care about.
              foreach ($this->getBlocks($node->childNodes) as $b) {
                $add_block($b);
              }
              break;

            // Unsupported.
            case 'canvas':
            case 'noscript':
            case 'script':
            case 'table':
            case 'tfoot':
            case 'video':
              break;

            // Treat as inline element.
            default:
              // Append series of inline elements together, as if they were
              // inside a block-level element.
              $block .= $this->getBlock($node);
              break;

          }
          break;

        case XML_TEXT_NODE:
          /** @var \DOMText $node */
          $block .= self::escape($node->textContent);
          break;

      }
    }

    $add_block();
    return $blocks;
  }

  /**
   * Converts a DOM element into a single Markdown block.
   *
   * Handles inline elements.
   *
   * @param \DOMElement $element
   *   The Node to transform.
   *
   * @return string
   *   A single Markdown block string.
   */
  protected function getBlock(\DOMElement $element) {
    $block = '';
    /** @var \DOMNode $node */
    foreach ($element->childNodes as $node) {
      switch ($node->nodeType) {

        case XML_ELEMENT_NODE:
          /** @var \DOMElement $node */
          switch ($node->nodeName) {

            // Explicitly handle these elements.

            case 'i':
            case 'em':
              $block .= '*' . $this->getBlock($node) . '*';
              break;

            case 'b':
            case 'strong':
              $block .= '**' . $this->getBlock($node) . '**';
              break;

            case 'a':
              if ($node->hasAttribute('href')) {
                $block .= '[' . $this->getBlock($node) . ']' .
                  '(' . $node->getAttribute('href') . ')';
              }
              break;

            // Other inline elements.
            default:
              // Recursively handle any descendant elements we care about.
              $block .= $this->getBlock($node);
              break;

          }
          break;

        case XML_TEXT_NODE:
          /** @var \DOMText $node */
          $block .= self::escape($node->textContent);
          break;

      }
    }

    // Handle white-listed elements.
    if (in_array($element->nodeName, $this->white_list)) {
      $tag = $element->cloneNode();
      if (!empty($block)) {
        $tag->appendChild(new \DOMText($block));
      }
      $block = $this->dom->saveXML($tag);
    }

    return $block;
  }

  /**
   * Converts a list-type \DOMElement into a single Markdown block.
   *
   * Note Apple markdown subset does not support nested lists.
   *
   * @param \DOMElement $element
   *   One of ul, ol, dl.
   *
   * @return string
   *   A single Markdown block string.
   */
  protected function getBlockList(\DOMElement $element) {
    $lines = array();
    $prefix = $element->nodeName == 'ol' ? ' 1. ' : ' - ';
    // Markdown does not support definition lists, convert to ul.
    if ($element->nodeName == 'dl') {
      $newline = TRUE;
      /** @var \DOMElement $child */
      foreach ($element->childNodes as $child) {
        switch ($child->nodeName) {

          case 'dt':
            if ($newline) {
              $lines[] = $prefix . $this->getBlock($child);
            }
            else {
              $lines[count($lines) - 1] .= '<br/>' . $this->getBlock($child);
            }
            $newline = FALSE;
            break;

          case 'dd':
            $lines[count($lines) - 1] .= '<br/>' . $this->getBlock($child);
            $newline = TRUE;
            break;

        }
      }
    }
    else {
      /** @var \DOMElement $child */
      foreach ($element->childNodes as $child) {
        switch ($child->nodeName) {

          case 'li':
            $lines[] = $prefix . $this->getBlock($child);
            break;

        }
      }
    }
    return implode("\n", $lines);
  }

}
