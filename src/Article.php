<?php

namespace craft\applenews;

use craft\elements\Asset;
use craft\elements\Entry;
use craft\helpers\FileHelper;
use yii\base\BaseObject;

/**
 * Article
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 2.0
 */
class Article extends BaseObject implements ArticleInterface
{
    /**
     * @var Entry The associated entry
     */
    protected $entry;

    /**
     * @var array The files that should be included in the article (uri => path)
     */
    protected $files = [];

    /**
     * @var array The metadata that should be included with the request
     */
    protected $metadata = [];

    /**
     * @var array The article content, described in Apple News Format
     */
    protected $content;

    /**
     * Constructor
     *
     * @param Entry|null $entry
     * @param array $config
     */
    public function __construct(Entry $entry = null, array $config = [])
    {
        $this->entry = $entry;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array $files The files that should be included in the article (uri => path)
     */
    public function setFiles(array $files)
    {
        $this->files = $files;
    }

    /**
     * Adds a new file to the article and returns its URL.
     *
     * @param Asset|string $file An asset or a path to a file
     * @return string The URL that the article should use to reference the file
     */
    public function addFile($file)
    {
        if ($file instanceof Asset) {
            // Get the local path to the file (and copy it from its remote source if need be)
            $file = $file->getImageTransformSourcePath();
        }

        // Get a unique filename for the article
        $basename = FileHelper::sanitizeFilename(str_replace('@', '-', pathinfo($file, PATHINFO_FILENAME)), [
            'asciiOnly' => true,
            'separator' => '-',
        ]);
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $filename = "{$basename}.{$ext}";
        $i = 0;
        while (isset($this->files[$filename])) {
            $filename = "{$basename}_" . ++$i . ".{$ext}";
        }

        $this->files[$filename] = $file;
        return 'bundle://' . $filename;
    }

    /**
     * @inheritdoc
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata The metadata that should be included with the request
     */
    public function setMetadata(array $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Adds a new metadata property to the article.
     *
     * @param string $name The metadata property name. Can be a dot-delimited path for defining nested array properties (e.g. `links.sections`)
     * @param mixed $value The metadata property value
     */
    public function addMetadata(string $name, $value)
    {
        $arr = &$this->metadata;

        if (strpos($name, '.') !== false) {
            $path = explode('.', $name);
            $name = array_pop($path);

            foreach ($path as $step) {
                if (!isset($arr[$step])) {
                    $arr[$step] = [];
                }
                $arr = &$arr[$step];
            }
        }

        $arr[$name] = $value;
    }

    /**
     * @inheritdoc
     */
    public function getContent(): array
    {
        return $this->content;
    }

    /**
     * @param array $content The article content, described in Apple News Format
     */
    public function setContent(array $content)
    {
        $this->content = $content;
    }
}
