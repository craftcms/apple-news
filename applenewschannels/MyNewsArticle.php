<?php

use Craft\AppleNewsArticle;
use Craft\BaseAppleNewsChannel;
use Craft\AssetFileModel;
use Craft\AppleNewsHelper;
use Craft\DateTimeHelper;
use Craft\IAppleNewsArticle;
use Craft\MatrixBlockModel;
use Craft\RichTextData;

/**
 * Class MyNewsArticle
 */
class MyNewsArticle extends AppleNewsArticle
{
    // Properties
    // =========================================================================

    /**
     * @var string|null The featured image URL, if any
     */
    protected $featuredImageUrl;

    /**
     * @var string The article byline
     */
    protected $byline;

    // Public Methods
    // =========================================================================

    /**
     * Initializes the article
     */
    public function init()
    {
        // Assign some commonly-used properties
        // ---------------------------------------------------------------------

        /** @var AssetFileModel|null $featuredImage */
        $featuredImage = $this->entry->featuredImage->first();
        $this->featuredImageUrl = $featuredImage ? $this->addFile($featuredImage) : null;

        $this->byline = $this->entry->getAuthor()->getName().($this->entry->postDate ? ' | '.$this->entry->postDate->format('F j, Y') : '');

        // Article content
        // ---------------------------------------------------------------------

        $this->setContent([
            'version' => '1.1',
            'identifier' => $this->entry->id,
            'title' => $this->entry->title,
            //'subtitle' => 'Non occidere quae cumque vi ventia',
            'language' => AppleNewsHelper::formatLanguage($this->entry->locale),
            'layout' => [
                'columns' => 12,
                'width' => 1024,
                'margin' => 65,
                'gutter' => 20,
            ],
            'metadata' => [
                'authors' => [
                    $this->entry->getAuthor()->getName()
                ],
                'canonicalURL' => $this->entry->getUrl(),
                'dateCreated' => DateTimeHelper::toIso8601($this->entry->dateCreated),
                'dateModified' => DateTimeHelper::toIso8601($this->entry->dateUpdated),
                'datePublished' => ($this->entry->postDate ? DateTimeHelper::toIso8601($this->entry->postDate) : null),
                'excerpt' => AppleNewsHelper::stripHtml($this->entry->shortDescription),
                'keywords' => AppleNewsHelper::createKeywords($this->entry, ['shortDescription']),
                'thumbnailURL' => isset($featuredImageUrl) ? $featuredImageUrl : null,
            ],
            'components' => $this->getComponents(),
            'componentLayouts' => $this->getComponentLayouts(),
            'componentStyles' => $this->getComponentStyles(),
            'componentTextStyles' => $this->getComponentTextStyles(),
        ]);

        // Request metadata
        // ---------------------------------------------------------------------

        $this->addMetadata('isPreview', false);
    }

    // Protected Methods
    // =========================================================================

    /**
     * @return array
     */
    protected function getComponents()
    {
        $components = [];

        // Header
        // ---------------------------------------------------------------------

        if ($this->featuredImageUrl) {
            $components[] = $this->getHeaderWithFeaturedImage();
        } else {
            $components[] = $this->getPlainTextHeader();
        }

        // Body
        // ---------------------------------------------------------------------

        $firstBody = true;

        foreach ($this->entry->articleBody as $block) {
            /** @var MatrixBlockModel $block */
            switch ($block->getType()->handle) {
                case 'heading': {
                    $components[] = [
                        'role' => 'heading',
                        'layout' => 'headingLayout',
                        'textStyle' => 'headingTextStyle',
                        'text' => $block->heading,
                    ];
                    break;
                }
                case 'text': {
                    // Is this the first body block?
                    if ($firstBody) {
                        $textStyle = 'dropcapBodyTextStyle';
                        $firstBody = false;
                    } else {
                        $textStyle = 'bodyTextStyle';
                    }
                    $textComponents = AppleNewsHelper::html2Components($block->text, [
                        'heading' => [
                            'role' => 'heading',
                            'layout' => 'headingLayout',
                            'textStyle' => 'headingTextStyle',
                        ],
                        'body' => [
                            'role' => 'body',
                            'layout' => 'bodyLayout',
                            'textStyle' => $textStyle,
                        ],
                    ]);
                    $components = array_merge($components, $textComponents);
                    break;
                }
                case 'pullQuote': {
                    $components[] = [
                        'role' => 'container',
                        'layout' => 'pullquoteContainerLayout',
                        'components' => [
                            [
                                'role' => 'pullquote',
                                'textStyle' => 'pullquoteTextStyle',
                                'layout' => 'pullquoteLayout',
                                'text' => '“'.$block->pullQuote.'”',
                                'animation' => ['type' => 'fade_in'],
                            ],
                        ],
                    ];
                    break;
                }
                case 'quote': {
                    $components[] = [
                        'role' => 'container',
                        'layout' => 'pullquoteContainerLayout',
                        'components' => [
                            [
                                'role' => 'pullquote',
                                'textStyle' => 'pullquoteTextStyle',
                                'layout' => 'pullquoteLayout',
                                'text' => '“'.$block->quote.'”',
                                'animation' => ['type' => 'fade_in'],
                            ],
                            [
                                'role' => 'pullquote',
                                'textStyle' => 'pullquoteTextAuthor',
                                'layout' => 'pullquoteAuthorLayout',
                                'text' => $block->attribution,
                            ],
                        ],
                    ];
                    break;
                }
                case 'image': {
                    /** @var AssetFileModel|null $image */
                    $image = $block->image->first();
                    if ($image) {
                        $imageUrl = $this->addFile($image);
                        /** @var RichTextData $caption */
                        $caption = $block->caption;
                        $layout = $caption ? 'photoWithCaptionLayout' : 'photoLayout';
                        $photoComponents = [
                            [
                                'role' => 'photo',
                                'URL' => $imageUrl,
                                'layout' => $layout,
                                'caption' => AppleNewsHelper::stripHtml($caption),
                            ],
                        ];
                        if ($caption) {
                            $photoComponents[] = [
                                'role' => 'container',
                                'layout' => 'captionContainerLayout',
                                'style' => 'captionContainerStyle',
                                'components' => [
                                    [
                                        'role' => 'caption',
                                        'textStyle' => 'photoTextCaption',
                                        'layout' => 'captionTitleLayout',
                                        'format' => 'markdown',
                                        'text' => AppleNewsHelper::html2Markdown($caption),
                                    ],
                                ],
                            ];
                        }
                        $components[] = [
                            'role' => 'container',
                            'layout' => ['ignoreDocumentMargin' => true,],
                            'components' => $photoComponents,
                        ];
                    }
                    break;
                }
                case 'gallery': {
                    $items = [];
                    foreach ($block->images as $image) {
                        /** @var AssetFileModel $image */
                        /** @var RichTextData $imageDescription */
                        $imageDescription = $image->shortDescription;
                        $items[] = [
                            'URL' => $this->addFile($image),
                            'caption' => AppleNewsHelper::stripHtml($imageDescription),
                        ];
                    }
                    $components[] = [
                        'role' => 'gallery',
                        'layout' => 'galleryLayout',
                        'items' => $items,
                    ];
                    break;
                }
            }
        }

        // Subscribe
        // ---------------------------------------------------------------------

        $components[] = [
            'role' => 'container',
            'layout' => 'subscribeContainerLayout',
            'style' => 'subscribeContainerStyle',
            'components' => [
                [
                    'role' => 'body',
                    'textStyle' => 'subscribeTextText',
                    'layout' => 'subscribeLayout',
                    'format' => 'markdown',
                    'text' => '[**Subscribe** to this newsletter for up to the minute news.](https://craftcms.com)',
                ],
            ],
        ];

        return $components;
    }

    /**
     * Component tree:
     *
     * - Section (sets parallax effect)
     *     - Header (image fill)
     *         - Container (linear gradient fill)
     *             - Container (anchored to bottom)
     *                 - Title
     *                 - Byline
     *
     * @return array
     */
    protected function getHeaderWithFeaturedImage()
    {
        return [
            'role' => 'section',
            'layout' => [
                'columnStart' => 0,
                'columnSpan' => 12,
                'ignoreDocumentMargin' => true,
            ],
            'scene' => [
                'type' => 'parallax_scale'
            ],
            'components' => [
                [
                    'role' => 'header',
                    'layout' => 'headerLayout',
                    'style' => [
                        'fill' => [
                            'type' => 'image',
                            'URL' => $this->featuredImageUrl,
                            'fillMode' => 'cover',
                            'verticalAlignment' => 'top',
                        ],
                    ],
                    'components' => [
                        [
                            'role' => 'container',
                            'anchor' => [
                                'targetAnchorPosition' => 'bottom',
                                'originAnchorPosition' => 'bottom',
                            ],
                            'style' => [
                                'fill' => [
                                    'type' => 'linear_gradient',
                                    'colorStops' => [
                                        ['color' => '#00000000', 'location' => 50],
                                        ['color' => '#00000080'],
                                    ],
                                ],
                            ],
                            'layout' => [
                                'ignoreDocumentMargin' => true,
                                'minimumHeight' => '70vh',
                            ],
                            'components' => [
                                [
                                    'role' => 'container',
                                    'anchor' => [
                                        'targetAnchorPosition' => 'bottom',
                                        'originAnchorPosition' => 'bottom',
                                    ],
                                    'components' => [
                                        [
                                            'role' => 'title',
                                            'layout' => 'titleLayout',
                                            'text' => $this->entry->title,
                                            'textStyle' => 'titleTextStyle',
                                        ],
                                        [
                                            'role' => 'byline',
                                            'textStyle' => 'bylineTextStyle',
                                            'layout' => 'bylineLayout',
                                            'text' => $this->byline,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Component tree:
     *
     * - Section
     *     - Title
     *     - Byline
     *
     * @return array
     */
    protected function getPlainTextHeader()
    {
        return [
            'role' => 'section',
            'layout' => [
                'columnStart' => 0,
                'columnSpan' => 12,
            ],
            'components' => [
                [
                    'role' => 'title',
                    'layout' => 'titleLayout',
                    'text' => $this->entry->title,
                    'textStyle' => 'titleTextStyle',
                ],
                [
                    'role' => 'byline',
                    'textStyle' => 'bylineTextStyle',
                    'layout' => 'bylineLayout',
                    'text' => $this->byline,
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getComponentLayouts()
    {
        return [
            'headerLayout' => [
                'ignoreDocumentMargin' => true,
                'margin' => ['bottom' => 40],
                'minimumHeight' => '70vh',
            ],
            'titleLayout' => [
                'columnStart' => 1,
                'columnSpan' => 10,
                'margin' => ['bottom' => 0],
            ],
            'bylineLayout' => [
                'margin' => ['bottom' => 30],
            ],
            'bodyLayout' => [
                'columnStart' => 1,
                'columnSpan' => 10,
                'margin' => ['bottom' => 35],
            ],
            'pullquoteContainerLayout' => [
                'ignoreDocumentMargin' => true,
                'columnSpan' => 12,
                'contentInset' => ['top' => false, 'bottom' => false],
                'margin' => ['bottom' => 40, 'top' => 0],
            ],
            'pullquoteLayout' => [
                'margin' => ['top' => 0, 'bottom' => 0],
                'columnStart' => 0,
                'columnSpan' => 12,
            ],
            'pullquoteAuthorLayout' => [
                'margin' => ['top' => 0, 'bottom' => 0],
            ],
            'photoLayout' => [
                'columnSpan' => 12,
                'ignoreDocumentMargin' => true,
                'margin' => ['top' => 20, 'bottom' => 20],
            ],
            'photoWithCaptionLayout' => [
                'columnSpan' => 12,
                'ignoreDocumentMargin' => true,
                'margin' => ['top' => 20],
            ],
            'captionContainerLayout' => [
                'ignoreDocumentMargin' => true,
                'columnSpan' => 12,
                'contentInset' => ['top' => false, 'bottom' => false],
                'margin' => ['bottom' => 50],
            ],
            'captionTitleLayout' => [
                'margin' => ['top' => 15, 'bottom' => 15],
            ],
            'headingLayout' => [
                'columnStart' => 1,
                'columnSpan' => 10,
                'margin' => ['bottom' => 10],
            ],
            'galleryLayout' => [
                'margin' => ['top' => 10, 'bottom' => 40],
            ],
            'subscribeContainerLayout' => [
                'ignoreDocumentMargin' => true,
                'columnSpan' => 12,
                'contentInset' => true,
            ],
            'subscribeLayout' => [
                'margin' => ['top' => 15, 'bottom' => 15],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getComponentStyles()
    {
        return [
            'captionContainerStyle' => ['backgroundColor' => '#EEEEEE'],
            'subscribeContainerStyle' => ['backgroundColor' => '#EEEEEE'],
        ];
    }

    /**
     * @return array
     */
    protected function getComponentTextStyles()
    {
        if ($this->featuredImageUrl) {
            $titleTextColor = '#FFF';
        } else {
            $titleTextColor = '#2A2A2A';
        }

        return [
            'titleTextStyle' => [
                'fontName' => 'AvenirNext-Bold',
                'textColor' => $titleTextColor,
                'fontSize' => 75,
                'lineHeight' => 70,
                'textAlignment' => 'center',
            ],
            'bylineTextStyle' => [
                'fontName' => 'AppleSDGothicNeo-Regular',
                'textColor' => $titleTextColor,
                'fontSize' => 13,
                'textAlignment' => 'center',
            ],
            'bodyTextStyle' => [
                'fontName' => 'AvenirNext-Medium',
                'textColor' => '#4A4A4A',
                'fontSize' => 18,
                'lineHeight' => 28,
                'hyphenation' => false,
                'linkStyle' => ['textColor' => '#0072AD', 'underline' => true],
            ],
            'dropcapBodyTextStyle' => [
                'fontName' => 'AvenirNext-Medium',
                'textColor' => '#4A4A4A',
                'fontSize' => 18,
                'lineHeight' => 28,
                'hyphenation' => false,
                'linkStyle' => ['textColor' => '#0072AD', 'underline' => true],
                'dropCapStyle' => [
                    'numberOfLines' => 3,
                    'numberOfCharacters' => 1,
                    'padding' => 0,
                    'fontName' => 'AvenirNext-DemiBold',
                    'textColor' => '#2A2A2A',
                ],
            ],
            'pullquoteTextStyle' => [
                'fontName' => 'AvenirNext-Bold',
                'textColor' => '#2A2A2A',
                'fontSize' => 65,
                'lineHeight' => 72,
                'textAlignment' => 'center',
            ],
            'pullquoteTextAuthor' => [
                'fontName' => 'AppleSDGothicNeo-Regular',
                'textColor' => '#676767',
                'fontSize' => 13,
                'textAlignment' => 'center',
            ],
            'photoTextCaption' => [
                'fontName' => 'AvenirNext-DemiBold',
                'textColor' => '#2A2A2A',
                'textAlignment' => 'center',
                'fontSize' => 18,
            ],
            'headingTextStyle' => [
                'fontName' => 'AvenirNext-Bold',
                'textColor' => '#2A2A2A',
                'fontSize' => 36,
                'lineHeight' => 42,
            ],
            'subscribeTextText' => [
                'fontName' => 'AvenirNext-Medium',
                'textColor' => '#2A2A2A',
                'textAlignment' => 'center',
                'fontSize' => 18,
            ],
        ];
    }
}
