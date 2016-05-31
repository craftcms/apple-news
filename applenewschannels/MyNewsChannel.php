<?php

use Craft\AppleNewsArticle;
use Craft\BaseAppleNewsChannel;
use Craft\AssetFileModel;
use Craft\EntryModel;
use Craft\AppleNewsHelper;
use Craft\DateTimeHelper;
use Craft\RichTextData;

/**
 * Class MyNewsChannel
 */
class MyNewsChannel extends BaseAppleNewsChannel
{
    public function matchEntry(EntryModel $entry)
    {
        if ($entry->locale != 'en') {
            return false;
        }

        if ($entry->getSection()->handle != 'news') {
            return false;
        }

        return true;
    }

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

        if ($featuredImage) {
            $featuredImageUrl = $article->addFile($featuredImage);
        }

        // Title
        $components[] = [
            'role' => 'title',
            'layout' => 'titleLayout',
            'text' => $entry->title,
            'textStyle' => 'titleStyle',
        ];

        // Intro
        $components[] = [
            'role' => 'intro',
            'layout' => 'introLayout',
            'text' => $shortDescription->getParsedContent(),
            'textStyle' => 'introStyle',
        ];

        // Header image
        if (isset($featuredImageUrl)) {
            $components[] = [
                'role' => 'header',
                'layout' => 'headerImageLayout',
                'style' => [
                    'fill' => [
                        'type' => 'image',
                        'URL' => $featuredImageUrl,
                        'fillMode' => 'cover',
                        'verticalAlignment' => 'center',
                    ],
                ],
            ];
        }

        // Author
        $components[] = [
            'role' => 'author',
            'layout' => 'authorLayout',
            'text' => 'By '.$entry->getAuthor()->getName(),
            'textStyle' => 'authorStyle',
        ];

        // Body components
        foreach ($entry->articleBody as $block) {
            switch ($block->getType()->handle) {
                case 'heading': {
                    $components[] = [
                        'role' => 'heading1',
                        'layout' => 'heading1Layout',
                        'text' => $block->heading,
                        'textStyle' => 'heading1Style',
                    ];
                    break;
                }
                case 'text': {
                    /** @var RichTextData $text */
                    $text = $block->text;
                    $components[] = [
                        'role' => 'body',
                        'layout' => 'bodyLayout',
                        'text' => $text->getParsedContent(),
                        'textStyle' => 'bodyStyle',
                    ];
                    break;
                }
                case 'pullQuote': {
                    $components[] = [
                        'role' => 'pullquote',
                        'layout' => 'pullquoteLayout',
                        'text' => $block->pullQuote,
                        'textStyle' => 'pullquoteStyle',
                    ];
                    break;
                }
                case 'image': {
                    /** @var AssetFileModel|null $image */
                    $image = $block->image->first();
                    if ($image) {
                        /** @var RichTextData $caption */
                        $caption = $article->caption;
                        $captionText = $caption->getParsedContent();
                        $imageUrl = $article->addFile($image);
                        $components[] = [
                            'role' => 'photo',
                            'layout' => 'photoLayout',
                            'url' => $imageUrl,
                            'caption' => $captionText,
                        ];
                        if ($captionText) {
                            $components[] = [
                                'role' => 'caption',
                                'layout' => 'captionLayout',
                                'text' => $captionText,
                                'textStyle' => 'captionStyle',
                            ];
                        }
                    }
                    break;
                }
            }
        }

        // Set the content/metadata on the article
        // ---------------------------------------------------------------------

        $article->setContent([
            'version' => '1.0',
            'identifier' => $entry->id,
            'title' => $entry->title,
            'language' => AppleNewsHelper::formatLanguage($entry->locale),
            'layout' => [
                'columns' => 7,
                'width'   => 1024,
                'margin'  => 70,
                'gutter'  => 40,
            ],
            //'subtitle' => 'Non occidere quae cumque vi ventia',
            'metadata' => [
                'authors' => [
                    $entry->getAuthor()->getName()
                ],
                'canonicalURL' => $entry->getUrl(),
                'dateCreated' => DateTimeHelper::toIso8601($entry->dateCreated),
                'dateModified' => DateTimeHelper::toIso8601($entry->dateUpdated),
                'datePublished' => DateTimeHelper::toIso8601($entry->postDate),
                'excerpt' => $shortDescription->getParsedContent(),
                'keywords' => AppleNewsHelper::createKeywords($entry, ['shortDescription']),
                'thumbnailURL' => isset($featuredImageUrl) ? $featuredImageUrl : null,
            ],
            'documentStyle' => [
                'backgroundColor' => '#f6f6f6',
            ],
            'components' => $components,
            'componentTextStyles' => $this->getComponentTextStyles(),
            'componentLayouts' => $this->getComponentLayouts(),
        ]);
        $article->addMetadata('isPreview', false);

        return $article;
    }

    protected function getComponentTextStyles()
    {
        return [
            'default-title' => [
                'fontName'      => 'HelveticaNeue-Thin',
                'fontSize'      => 36,
                'textColor'     => '#2F2F2F',
                'textAlignment' => 'center',
                'lineHeight'    => 44,
            ],
            'default-subtitle' => [
                'fontName'      => 'HelveticaNeue-Thin',
                'fontSize'      => 20,
                'textColor'     => '#2F2F2F',
                'textAlignment' => 'center',
                'lineHeight'    => 24,
            ],
            'titleStyle' => [
                'textAlignment' => 'left',
                'fontName'      => 'HelveticaNeue-Bold',
                'fontSize'      => 64,
                'lineHeight'    => 74,
                'textColor'     => '#000',
            ],
            'introStyle' => [
                'textAlignment' => 'left',
                'fontName'      => 'HelveticaNeue-Medium',
                'fontSize'      => 24,
                'textColor'     => '#000',
            ],
            'authorStyle' => [
                'textAlignment' => 'left',
                'fontName'      => 'HelveticaNeue-Bold',
                'fontSize'      => 16,
                'textColor'     => '#000',
            ],
            'bodyStyle' => [
                'textAlignment' => 'left',
                'fontName'      => 'Georgia',
                'fontSize'      => 18,
                'lineHeight'    => 26,
                'textColor'     => '#000',
            ],
            'captionStyle' => [
                'textAlignment' => 'left',
                'fontName'      => 'HelveticaNeue-Medium',
                'fontSize'      => 12,
                'lineHeight'    => 17,
                'textColor'     => '#000',
            ],
            'heading1Style' => [
                'textAlignment' => 'left',
                'fontName'      => 'HelveticaNeue-Medium',
                'fontSize'      => 28,
                'lineHeight'    => 41,
                'textColor'     => '#000',
            ],
            'pullquoteStyle' => [
                'textAlignment' => 'left',
                'fontName'      => 'HelveticaNeue-Bold',
                'fontSize'      => 28,
                'lineHeight'    => 41,
                'textColor'     => '#000',
            ],
        ];
    }

    protected function getComponentLayouts()
    {
        return [
            'headerImageLayout' => [
                'columnStart'          => 0,
                'columnSpan'           => 7,
                'ignoreDocumentMargin' => true,
                'minimumHeight'        => '42vh',
            ],
            'titleLayout' => [
                'columnStart' => 0,
                'columnSpan'  => 7,
                'margin'      => ['top' => 30, 'bottom' => 10],
            ],
            'introLayout' => [
                'columnStart' => 0,
                'columnSpan'  => 7,
                'margin'      => ['top' => 15, 'bottom' => 15],
            ],
            'authorLayout' => [
                'columnStart' => 0,
                'columnSpan'  => 7,
                'margin'      => ['top' => 15, 'bottom' => 15],
            ],
            'bodyLayout' => [
                'columnStart' => 0,
                'columnSpan'  => 5,
                'margin'      => ['top' => 15, 'bottom' => 15],
            ],
            'captionLayout' => [
                'columnStart' => 5,
                'columnSpan'  => 2,
                'margin'      => ['top' => 15, 'bottom' => 30],
            ],
            'heading1Layout' => [
                'columnStart' => 0,
                'columnSpan'  => 5,
                'margin'      => ['top' => 15],
            ],
            'pullquoteLayout' => [
                'columnStart' => 0,
                'columnSpan'  => 7,
                'margin'      => ['top' => 15, 'bottom' => 15],
            ],
            'photoLayout' => [
                'columnStart' => 0,
                'columnSpan'  => 7,
                'margin'      => ['top' => 15, 'bottom' => 15],
            ],
        ];
    }
}
