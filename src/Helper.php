<?php

namespace craft\applenews;

use craft\base\Field;
use craft\base\FieldInterface;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use craft\helpers\Search;
use craft\redactor\FieldData;
use DOMDocument;
use DOMNode;
use InvalidArgumentException;
use League\HTMLToMarkdown\HtmlConverter;
use yii\helpers\Markdown;

/**
 * Apple News helper
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
abstract class Helper
{
    /**
     * @var HtmlConverter|null $_htmlConverter
     * @see htmlConverter()
     */
    private static ?HtmlConverter $_htmlConverter = null;

    /**
     * Formats a language ID into the format required by the Apple News API (e.g. "en" or "en_US").
     *
     * @param string $language The language ID
     * @return string The formatted language ID
     */
    public static function formatLanguage(string $language): string
    {
        $parts = preg_split('/[\-_]/', $language);
        return strtolower($parts[0]) . (!empty($parts[1]) ? '_' . strtoupper($parts[1]) : '');
    }

    /**
     * Creates a list of keywords for an article.
     *
     * @param Entry $entry The entry
     * @param string[] $fieldHandles The field handles that the keywords should be extracted from
     * @return string[] List of keywords for the article
     * @throws InvalidFieldException
     */
    public static function createKeywords(Entry $entry, array $fieldHandles): array
    {
        $keywords = [];

        // Find the fields
        /** @var FieldInterface[] $fields */
        $fields = [];
        foreach ($entry->getFieldLayout()->getCustomFields() as $field) {
            /** @var Field $field */
            $fieldHandle = $field->handle;
            if (in_array($fieldHandle, $fieldHandles, true)) {
                $fields[$fieldHandle] = $field;
            }
        }

        // Add the keywords in the order defined by $fieldHandles
        foreach ($fieldHandles as $fieldHandle) {
            if (isset($fields[$fieldHandle])) {
                $field = $fields[$fieldHandle];
                $fieldKeywords = Search::normalizeKeywords($field->getSearchKeywords($entry->getFieldValue($fieldHandle), $entry));
                $keywords = array_merge($keywords, array_filter(preg_split('/[\s\n\r]/', $fieldKeywords)));

                // Out of room?
                if (count($keywords) >= 50) {
                    array_splice($keywords, 50);
                    break;
                }
            }
        }

        return $keywords;
    }

    /**
     * Strips HTML tags from a given string, returning just the text. Block-level tags will be joined by two newlines.
     *
     * @param string|FieldData $html HTML-formatted text, or a FieldData object
     * @return string Text without HTML tags
     */
    public static function stripHtml(FieldData|string $html): string
    {
        if ($html instanceof FieldData) {
            $html = $html->getParsedContent();
        }

        // Replace block-level tags with newlines
        $blockTags = 'h1|h2|h3|h4|h5|h6|p|ul|ol|li|div';
        $html = preg_replace("/<\\/(?:$blockTags)>/i", "\n\n", $html);

        // Remove <script> tags, including their contents
        $html = preg_replace('/<script.*>.*<\/script>/isU', '', $html);

        // Remove all remaining tags
        $html = preg_replace('/<\/?\w+.*>/sU', '', $html);

        // Trim unwanted whitespace
        $html = preg_replace('/^[ \t]|[ \t]$/m', '', $html);
        $html = trim($html, "\n\r");
        $html = preg_replace('/[\n\r]{3,}/', "\n\n", $html);

        return $html;
    }

    /**
     * Converts HTML-formatted text into Markdown, stripped of any tags that aren’t supported by Apple News Format.
     *
     * @param string|FieldData $html HTML-formatted text, or a FieldData object
     * @return string Markdown-formatted text
     */
    public static function html2Markdown(FieldData|string $html): string
    {
        if ($html instanceof FieldData) {
            $html = $html->getParsedContent();
        }

        // Trim unwanted whitespace within block tags
        $blockTags = 'h1|h2|h3|h4|h5|h6|p|ul|ol|li|div';
        $html = preg_replace("/(<(?:$blockTags).*?>)\s*/is", "$1", $html);
        $html = trim($html, chr(0xC2) . chr(0xA0));

        // Remove comments
        $html = preg_replace('<!--.*?-->', '', $html);
        return static::htmlConverter()->convert($html);
    }

    /**
     * Converts HTML-formatted text into component definitions.
     *
     * The function will replace the following HTML tags with components:
     *
     * - <blockquote> => role=quote with `quote` properties
     * - <code> => role=body with either `code` or `body` properties
     * - <hr> => role=divider with `divider` properties
     * - <h1-6> => role=heading1-6 with either `heading1-6` or `heading` properties
     * - everything else => role=body with `body` properties
     *
     * @param string|FieldData $html HTML-formatted text, or a FieldData object
     * @param callable|array $properties An array defining the component properties that should be applied to each component type,
     *                                   or a function that returns the full component definition, given the type and Markdown text.
     *
     * @return array Component definitions
     * @todo Add support for images + captions and videos
     */
    public static function html2Components(FieldData|string $html, callable|array $properties = []): array
    {
        if ($html instanceof FieldData) {
            $html = $html->getParsedContent();
        }

        // Remove comments
        $html = preg_replace('<!--.*?-->', '', $html);

        // Create a DOMDocument object for the HTML
        // (from HtmlConverter::convert)
        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">' . $html);
        $document->encoding = 'UTF-8';
        libxml_clear_errors();
        if (!($body = $document->getElementsByTagName('body')->item(0))) {
            throw new InvalidArgumentException('Invalid HTML was provided');
        }

        // Create an array of all the top-level elements
        $componentInfos = [];
        $lastWasBody = false;

        foreach ($body->childNodes as $node) {
            /** @var DOMNode $node */

            // <pre><code> => <code>
            if ($node->nodeName === 'pre' && $node->childNodes->length == 1 && $node->firstChild->nodeName === 'code') {
                $node = $node->firstChild;
            }

            switch ($node->nodeName) {
                case 'blockquote':
                {
                    $role = 'quote';
                    $types = ['quote'];
                    $isBody = false;
                    $captureText = true;
                    $captureOuterHtml = false;
                    break;
                }
                case 'code':
                {
                    $role = 'body';
                    $types = ['code', 'body'];
                    $isBody = false;
                    $captureText = true;
                    $captureOuterHtml = false;
                    break;
                }

                case 'h1':
                case 'h2':
                case 'h3':
                case 'h4':
                case 'h5':
                case 'h6':
                {
                    $level = substr($node->nodeName, 1);
                    $role = 'heading' . $level;
                    $types = ['heading' . $level, 'heading'];
                    $isBody = false;
                    $captureText = true;
                    $captureOuterHtml = false;
                    break;
                }

                case 'hr':
                {
                    $role = 'divider';
                    $types = ['divider'];
                    $isBody = false;
                    $captureText = false;
                    $captureOuterHtml = false;
                    break;
                }
                default:
                {
                    // div, p, ol, ul, #text, etc.
                    $role = 'body';
                    $types = ['body'];
                    $isBody = true;
                    $captureText = true;
                    $captureOuterHtml = true;
                }
            }

            // Do we care about the text value?
            if ($captureText) {
                // Is the node type important?
                if ($captureOuterHtml) {
                    $nodeHtml = $document->saveHTML($node);
                } else {
                    $nodeHtml = '';
                    foreach ($node->childNodes as $childNode) {
                        $nodeHtml .= $document->saveHTML($childNode);
                    }
                }
                $nodeHtml = trim($nodeHtml, "\n\r ");
                if (!$nodeHtml) {
                    continue;
                }
                $text = static::html2Markdown($nodeHtml);
            } else {
                $text = null;
            }

            // If this is a body component and the last one was as well, append the text to the last one
            if ($isBody && $lastWasBody) {
                $lastComponentIndex = count($componentInfos) - 1;
                $componentInfos[$lastComponentIndex]['text'] .= "\n\n" . $text;
            } else {
                $componentInfos[] = [
                    'types' => $types,
                    'role' => $role,
                    'text' => $text,
                ];
                $lastWasBody = $isBody;
            }
        }

        $components = [];

        foreach ($componentInfos as $componentInfo) {
            $component = null;
            if (is_callable($properties)) {
                // See if the function cares about any of these types
                foreach ($componentInfo['types'] as $type) {
                    $component = $properties($type, $componentInfo['text']);
                    if ($component) {
                        break;
                    }
                }
            }
            if (!$component) {
                $component = [
                    'role' => $componentInfo['role'],
                ];

                if ($componentInfo['text']) {
                    $component['text'] = $componentInfo['text'];
                    $component['format'] = 'markdown';
                }

                if (is_array($properties)) {
                    // Merge in the first matching property key, if any
                    foreach ($componentInfo['types'] as $type) {
                        if (isset($properties[$type])) {
                            $component = array_merge($component, $properties[$type]);
                            break;
                        }
                    }
                }
            }

            $components[] = $component;
        }

        return $components;
    }

    /**
     * Converts Markdown-formatted text into component definitions.
     *
     * @param string $text Markdown-formatted text
     * @param callable|array $properties An array defining the component properties that should be applied to each component type,
     *                                        or a function that returns the full component definition, given the type and Markdown text.
     * @return array Component definitions
     */
    public static function markdown2Components(string $text, callable|array $properties = []): array
    {
        // Convert Markdown to HTML and run through html2Components()
        $html = Markdown::process($text);
        return static::html2Components($html, $properties);
    }

    /**
     * Returns an instance of HtmlConverter.
     *
     * @return HtmlConverter
     */
    protected static function htmlConverter(): HtmlConverter
    {
        if (!empty(self::$_htmlConverter)) {
            return self::$_htmlConverter;
        }

        return self::$_htmlConverter = new HtmlConverter([
            'strip_tags' => true,
        ]);
    }
}
