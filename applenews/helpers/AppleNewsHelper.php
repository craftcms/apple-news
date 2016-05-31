<?php
namespace Craft;

/**
 * Class AppleNewsHelper
 */
abstract class AppleNewsHelper
{
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
}
