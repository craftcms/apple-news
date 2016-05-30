<?php

use Craft\AssetFileModel;
use Craft\IOHelper;

/**
 * Class AppleNewsArticle
 */
class AppleNewsArticle implements IAppleNewsArticle
{
	// Properties
	// =========================================================================

	/**
	 * @var array The files that should be included in the article (uri => path)
	 */
	protected $files;

	/**
	 * @var array The metadata that should be included with the request
	 */
	protected $metadata;

	/**
	 * @var array The article content, described in Apple News Format
	 */
	protected $content;

	// Public Methods
	// =========================================================================

	/**
	 * @inheritdoc
	 */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * @param array $files The files that should be included in the article (uri => path)
	 */
	public function setFiles($files)
	{
		$this->files = $files;
	}

	/**
	 * Adds a new file to the article and returns its URL.
	 *
	 * @param AssetFileModel|string $file An {@link AssetFileModel} or a path to a file
	 *
	 * @return string The URL that the article should use to reference the file
	 */
	public function addFile($file)
	{
		if ($file instanceof AssetFileModel) {
			// Get the local path to the file (and copy it from its remote source if need be)
			$file = $file->getTransformSource();
		}

		// Get a unique filename for the article
		$filename = IOHelper::getFileName($file);
		if (isset($this->files[$filename])) {
			$basename = IOHelper::getFileName($file, false);
			$ext = IOHelper::getExtension($file);
			$i = 0;
			do {
				$filename = $basename.'_'.++$i.'.'.$ext;
			} while (isset($this->files[$filename]));
		}

		$this->files[$filename] = $file;

		return 'bundle://'.$filename;
	}

	/**
	 * @inheritdoc
	 */
	public function getMetadata()
	{
		return $this->metadata;
	}

	/**
	 * @param array $metadata The metadata that should be included with the request
	 */
	public function setMetadata($metadata)
	{
		$this->metadata = $metadata;
	}

	/**
	 * Adds a new metadata property to the article.
	 *
	 * @param string $name  The metadata property name. Can be a dot-delimited path for defining nested array properties (e.g. `links.sections`)
	 * @param mixed  $value The metadata property value
	 */
	public function addMetadata($name, $value)
	{
		if (!isset($this->metadata)) {
			$this->metadata = array();
		}

		$arr = &$this->metadata;

		if (strpos($name, '.') !== false) {
			$path = explode('.', $name);
			$name = array_pop($path);

			foreach ($path as $step) {
				if (!isset($arr[$step])) {
					$arr[$step] = array();
				}
				$arr = &$arr[$step];
			}
		}

		$arr[$name] = $value;
	}

	/**
	 * @inheritdoc
	 */
	public function getContent()
	{
		return $this->content;
	}

	/**
	 * @param array $content The article content, described in Apple News Format
	 */
	public function setContent($content)
	{
		$this->content = $content;
	}
}
