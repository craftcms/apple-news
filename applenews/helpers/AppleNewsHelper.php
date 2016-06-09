<?php
namespace Craft;

use League\HTMLToMarkdown\HtmlConverter;

/**
 * Class AppleNewsHelper
 *
 * @license https://github.com/pixelandtonic/AppleNews/blob/master/LICENSE
 */
abstract class AppleNewsHelper
{
    // Properties
    // =========================================================================

    private static $_htmlConverter;

    // Public Methods
    // =========================================================================

    /**
     * Formats a language ID into the format required by the Apple News API (e.g. "en" or "en_US").
     *
     * @param string $language The language ID
     *
     * @return string The formatted language ID
     */
    public static function formatLanguage($language)
    {
        $parts = explode('_', $language);

        return strtolower($parts[0]).(!empty($parts[1]) ? strtoupper($parts[1]) : '');
    }

    /**
     * Creates a list of keywords for an article.
     *
     * @param EntryModel $entry        The entry
     * @param string[]   $fieldHandles The field handles that the keywords should be extracted from
     *
     * @return string[] List of keywords for the article
     */
    public static function createKeywords(EntryModel $entry, $fieldHandles)
    {
        $keywords = [];

        // Find the fields
        /** @var FieldModel[] $fields */
        $fields = [];
        foreach ($entry->getFieldLayout()->getFields() as $fieldLayoutField) {
            $field = $fieldLayoutField->getField();
            $fieldHandle = $field->handle;
            if (in_array($fieldHandle, $fieldHandles)) {
                $fields[$fieldHandle] = $field;
            }
        }

        // Add the keywords in the order defined by $fieldHandles
        foreach ($fieldHandles as $fieldHandle) {
            if (isset($fields[$fieldHandle])) {
                $fieldType = $fields[$fieldHandle]->getFieldType();
                if ($fieldType) {
                    $fieldType->element = $entry;
                    $fieldKeywords = StringHelper::normalizeKeywords($fieldType->getSearchKeywords($entry->getFieldValue($fieldHandle)));
                    $keywords = array_merge($keywords, array_filter(preg_split('/[\s\n\r]/', $fieldKeywords)));

                    // Out of room?
                    if (count($keywords) >= 50) {
                        array_splice($keywords, 50);
                        break;
                    }
                }
            }
        }

        return $keywords;
    }

    /**
     * Strips HTML tags from a given string, returning just the text. Block-level tags will be joined by two newlines.
     *
     * @param string|RichTextData $html HTML-formatted text, or a RichTextData object
     *
     * @return string Text without HTML tags
     */
    public static function stripHtml($html)
    {
        if ($html instanceof RichTextData) {
            $html = $html->getParsedContent();
        }

        // Replace block-level tags with newlines
        $blockTags = 'h1|h2|h3|h4|h5|h6|p|ul|ol|li|div';
        $html = preg_replace("/<\\/(?:{$blockTags})>/i", "\n\n", $html);

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
     * @param string|RichTextData $html HTML-formatted text, or a RichTextData object
     *
     * @return string Markdown-formatted text
     */
    public static function html2Markdown($html)
    {
        if ($html instanceof RichTextData) {
            $html = $html->getParsedContent();
        }

        // Trim unwanted whitespace within within block tags
        $blockTags = 'h1|h2|h3|h4|h5|h6|p|ul|ol|li|div';
        $html = preg_replace("/(<(?:{$blockTags}).*?>)\s*/is", "$1", $html);

        $md = static::getHtmlConverter()->convert($html);

        return $md;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Returns an instance of HtmlConverter.
     *
     * @return HtmlConverter
     */
    protected static function getHtmlConverter()
    {
        if (!isset(self::$_htmlConverter)) {
            self::$_htmlConverter = new HtmlConverter([
                'strip_tags' => true
            ]);
        }

        return self::$_htmlConverter;
    }
}
