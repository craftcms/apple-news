<?php

use Craft\AppleNewsArticle;
use Craft\BaseAppleNewsChannel;
use Craft\AssetFileModel;
use Craft\EntryModel;
use Craft\AppleNewsHelper;
use Craft\DateTimeHelper;
use Craft\IAppleNewsArticle;
use Craft\MatrixBlockModel;
use Craft\RichTextData;

/**
 * Class MyNewsChannel
 */
class MyNewsChannel extends BaseAppleNewsChannel
{
    // Public Methods
    // =========================================================================

    /**
     * Determines whether a given entry should be included in the News channel.
     *
     * @param EntryModel $entry The entry
     *
     * @return bool Whether the entry should be included in the News channel
     */
    public function matchEntry(EntryModel $entry)
    {
        if ($entry->locale != 'en') {
            return false;
        }

        if ($entry->getSection()->handle != 'news') {
            return false;
        }

        if ($entry->getType()->handle != 'article') {
            return false;
        }

        return true;
    }

    /**
     * Creates an {@link Craft\IAppleNewsArticle} for the given entry
     *
     * @param EntryModel $entry The entry
     *
     * @return IAppleNewsArticle The article that represents the entry
     */
    public function createArticle(EntryModel $entry)
    {
        $article = new AppleNewsArticle();

        // Build the components
        // ---------------------------------------------------------------------

        $components = [];

        /** @var RichTextData $shortDescription */
        $shortDescription = $entry->shortDescription;

        /** @var AssetFileModel|null $featuredImage */
        $featuredImage = $entry->featuredImage->first();

        $byline = $entry->getAuthor()->getName().($entry->postDate ? ' | '.$entry->postDate->format('F j, Y') : '');

        if ($featuredImage) {
            $featuredImageUrl = $article->addFile($featuredImage);

            $components[] = [
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
                                'URL' => $featuredImageUrl,
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
                                                'text' => $entry->title,
                                                'textStyle' => 'titleStyle',
                                            ],
                                            [
                                                'role' => 'byline',
                                                'textStyle' => 'bylineStyle',
                                                'layout' => 'bylineLayout',
                                                'text' => $byline,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        } else {
            $components[] = [
                'role' => 'section',
                'layout' => [
                    'columnStart' => 0,
                    'columnSpan' => 12,
                    'ignoreDocumentMargin' => true,
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
                                'text' => $entry->title,
                                'textStyle' => 'titleStyle',
                            ],
                            [
                                'role' => 'byline',
                                'textStyle' => 'bylineStyle',
                                'layout' => 'bylineLayout',
                                'text' => $byline,
                            ],
                        ],
                    ],
                ],
            ];
        }

        // Body components
        $firstBody = true;

        foreach ($entry->articleBody as $i => $block) {
            /** @var MatrixBlockModel $block */
            switch ($block->getType()->handle) {
                case 'heading': {
                    $components[] = [
                        'role' => 'heading',
                        'layout' => 'headingLayout',
                        'textStyle' => 'headingStyle',
                        'text' => $block->heading,
                    ];
                    break;
                }
                case 'text': {
                    // Is this the first body block?
                    if ($firstBody) {
                        $textStyle = 'dropcapBodyStyle';
                        $firstBody = false;
                    } else {
                        $textStyle = 'bodyStyle';
                    }
                    /** @var RichTextData $text */
                    $text = $block->text;
                    $components[] = [
                        'role' => 'body',
                        'layout' => 'bodyLayout',
                        'textStyle' => $textStyle,
                        'text' => AppleNewsHelper::html2Markdown($text),
                        'format' => 'markdown',
                    ];
                    break;
                }
                case 'pullQuote': {
                    $components[] = [
                        'role' => 'container',
                        'layout' => 'pullquoteContainer',
                        'components' => [
                            [
                                'role' => 'pullquote',
                                'textStyle' => 'pullquoteStyle',
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
                        'layout' => 'pullquoteContainer',
                        'components' => [
                            [
                                'role' => 'pullquote',
                                'textStyle' => 'pullquoteStyle',
                                'layout' => 'pullquoteLayout',
                                'text' => '“'.$block->quote.'”',
                                'animation' => ['type' => 'fade_in'],
                            ],
                            [
                                'role' => 'pullquote',
                                'textStyle' => 'pullquoteAuthor',
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
                        $imageUrl = $article->addFile($image);
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
                                'layout' => 'captionContainer',
                                'style' => 'captionContainerStyle',
                                'components' => [
                                    [
                                        'role' => 'caption',
                                        'textStyle' => 'photoCaption',
                                        'layout' => 'captionTitle',
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
                            'URL' => $article->addFile($image),
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

        // Subscribe component
        $components[] = [
            'role' => 'container',
            'layout' => 'subscribeContainer',
            'style' => 'subscribeContainerStyle',
            'components' => [
                [
                    'role' => 'body',
                    'textStyle' => 'subscribeText',
                    'layout' => 'subscribeLayout',
                    'format' => 'markdown',
                    'text' => '[**Subscribe** to this newsletter for up to the minute news.](https://craftcms.com)',
                ],
            ],
        ];

        // Set the content/metadata on the article
        // ---------------------------------------------------------------------

        $article->setContent([
            'version' => '1.1',
            'identifier' => $entry->id,
            'title' => $entry->title,
            'language' => AppleNewsHelper::formatLanguage($entry->locale),
            'layout' => [
                'columns' => 12,
                'width' => 1024,
                'margin' => 65,
                'gutter' => 20,
            ],
            //'subtitle' => 'Non occidere quae cumque vi ventia',
            'metadata' => [
                'authors' => [
                    $entry->getAuthor()->getName()
                ],
                'canonicalURL' => $entry->getUrl(),
                'dateCreated' => DateTimeHelper::toIso8601($entry->dateCreated),
                'dateModified' => DateTimeHelper::toIso8601($entry->dateUpdated),
                'datePublished' => ($entry->postDate ? DateTimeHelper::toIso8601($entry->postDate) : null),
                'excerpt' => AppleNewsHelper::stripHtml($shortDescription),
                'keywords' => AppleNewsHelper::createKeywords($entry, ['shortDescription']),
                'thumbnailURL' => isset($featuredImageUrl) ? $featuredImageUrl : null,
            ],
            'components' => $components,
            'componentLayouts' => $this->getComponentLayouts(),
            'componentStyles' => $this->getComponentStyles(),
            'componentTextStyles' => $this->getComponentTextStyles(),
            'textStyles' => $this->getTextStyles(),
        ]);

        $article->addMetadata('isPreview', false);

        return $article;
    }

    // Protected Methods
    // =========================================================================

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
            'pullquoteContainer' => [
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
            'captionContainer' => [
                'ignoreDocumentMargin' => true,
                'columnSpan' => 12,
                'contentInset' => ['top' => false, 'bottom' => false],
                'margin' => ['bottom' => 50],
            ],
            'captionTitle' => [
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
            'subscribeContainer' => [
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
        return [
            'titleStyle' => [
                'fontName' => 'AvenirNext-Bold',
                'textColor' => '#FFF',
                'fontSize' => 75,
                'lineHeight' => 70,
                'textAlignment' => 'center',
            ],
            'bylineStyle' => [
                'fontName' => 'AppleSDGothicNeo-Regular',
                'textColor' => '#FFF',
                'fontSize' => 13,
                'textAlignment' => 'center',
            ],
            'bodyStyle' => [
                'fontName' => 'AvenirNext-Medium',
                'textColor' => '#4A4A4A',
                'fontSize' => 18,
                'lineHeight' => 28,
                'hyphenation' => false,
                'linkStyle' => ['textColor' => '#0072AD', 'underline' => true],
            ],
            'dropcapBodyStyle' => [
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
            'pullquoteStyle' => [
                'fontName' => 'AvenirNext-Bold',
                'textColor' => '#2A2A2A',
                'fontSize' => 65,
                'lineHeight' => 72,
                'textAlignment' => 'center',
            ],
            'pullquoteAuthor' => [
                'fontName' => 'AppleSDGothicNeo-Regular',
                'textColor' => '#676767',
                'fontSize' => 13,
                'textAlignment' => 'center',
            ],
            'photoCaption' => [
                'fontName' => 'AvenirNext-DemiBold',
                'textColor' => '#2A2A2A',
                'textAlignment' => 'center',
                'fontSize' => 18,
            ],
            'headingStyle' => [
                'fontName' => 'AvenirNext-Bold',
                'textColor' => '#2A2A2A',
                'fontSize' => 36,
                'lineHeight' => 42,
            ],
            'subscribeText' => [
                'fontName' => 'AvenirNext-Medium',
                'textColor' => '#2A2A2A',
                'textAlignment' => 'center',
                'fontSize' => 18,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getTextStyles()
    {
        return [
            'photoCaptionThin' => [
                'fontName' => 'AppleSDGothicNeo-Regular',
                'textColor' => '#676767',
                'fontSize' => 13,
            ],
        ];
    }
}
