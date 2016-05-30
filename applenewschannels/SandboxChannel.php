<?php

use Craft\AssetFileModel;
use Craft\EntryModel;
use Craft\AppleNewsHelper;
use Craft\DateTimeHelper;
use Craft\RichTextData;

/**
 * Class SandboxChannel
 */
class SandboxChannel extends BaseAppleNewsChannel
{
	protected $channelId = 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx';
	protected $apiKeyId  = 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx';
	protected $apiSecret = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

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

		$components = array();

		/** @var RichTextData $shortDescription */
		$shortDescription = $entry->shortDescription;

		/** @var AssetFileModel|null $featuredImage */
		$featuredImage = $entry->featuredImage->first();

		if ($featuredImage) {
			$featuredImageUrl = $article->addFile($featuredImage);
		}

		// Title
		$components[] = array(
			'role' => 'title',
			'layout' => 'titleLayout',
			'text' => $entry->title,
			'textStyle' => 'titleStyle',
		);

		// Intro
		$components[] = array(
			'role' => 'intro',
			'layout' => 'introLayout',
			'text' => $shortDescription->getParsedContent(),
			'textStyle' => 'introStyle',
		);

		// Header image
		if (isset($featuredImageUrl)) {
			$components[] = array(
				'role' => 'header',
				'layout' => 'headerImageLayout',
				'style' => array(
					'fill' => array (
						'type' => 'image',
						'URL' => $featuredImageUrl,
						'fillMode' => 'cover',
						'verticalAlignment' => 'center',
					),
				),
			);
		}

		// Author
		$components[] = array(
			'role' => 'author',
			'layout' => 'authorLayout',
			'text' => 'By '.$entry->getAuthor()->getName(),
			'textStyle' => 'authorStyle',
		);

		// Body components
		foreach ($entry->articleBody as $block) {
			switch ($block->getType()->handle) {
				case 'heading': {
					$components[] = array(
						'role' => 'heading1',
						'layout' => 'heading1Layout',
						'text' => $block->heading,
						'textStyle' => 'heading1Style',
					);
					break;
				}
				case 'text': {
					/** @var RichTextData $text */
					$text = $block->text;
					$components[] = array(
						'role' => 'body',
						'layout' => 'bodyLayout',
						'text' => $text->getParsedContent(),
						'textStyle' => 'bodyStyle',
					);
					break;
				}
				case 'pullQuote': {
					$components[] = array(
						'role' => 'pullquote',
						'layout' => 'pullquoteLayout',
						'text' => $block->pullQuote,
						'textStyle' => 'pullquoteStyle',
					);
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
						$components[] = array(
							'role' => 'photo',
							'layout' => 'photoLayout',
							'url' => $imageUrl,
							'caption' => $captionText,
						);
						if ($captionText) {
							$components[] = array(
								'role' => 'caption',
								'layout' => 'captionLayout',
								'text' => $captionText,
								'textStyle' => 'captionStyle',
							);
						}
					}
					break;
				}
			}
		}

		// Set the content/metadata on the article
		// ---------------------------------------------------------------------

		$article->setContent(array(
			'version' => '1.0',
			'identifier' => $entry->id,
			'title' => $entry->title,
			'language' => AppleNewsHelper::formatLanguage($entry->locale),
			'layout' => array(
				'columns' => 7,
				'width'   => 1024,
				'margin'  => 70,
				'gutter'  => 40,
			),
			//'subtitle' => 'Non occidere quae cumque vi ventia',
			'metadata' => array(
				'authors' => array(
					$entry->getAuthor()->getName()
				),
				'canonicalURL' => $entry->getUrl(),
				'dateCreated' => DateTimeHelper::toIso8601($entry->dateCreated),
				'dateModified' => DateTimeHelper::toIso8601($entry->dateUpdated),
				'datePublished' => DateTimeHelper::toIso8601($entry->postDate),
				'excerpt' => $shortDescription->getParsedContent(),
				'keywords' => AppleNewsHelper::createKeywords($entry, array('shortDescription')),
				'thumbnailURL' => isset($featuredImageUrl) ? $featuredImageUrl : null,
			),
			'documentStyle' => array(
				'backgroundColor' => '#f6f6f6',
			),
			'components' => $components,
			'componentTextStyles' => $this->getComponentTextStyles(),
			'componentLayouts' => $this->getComponentLayouts(),
		));
		$article->addMetadata('isPreview', false);

		return $article;
	}

	protected function getComponentTextStyles()
	{
		return array(
			'default-title' => array(
				'fontName'      => 'HelveticaNeue-Thin',
				'fontSize'      => 36,
				'textColor'     => '#2F2F2F',
				'textAlignment' => 'center',
				'lineHeight'    => 44,
			),
			'default-subtitle' => array(
				'fontName'      => 'HelveticaNeue-Thin',
				'fontSize'      => 20,
				'textColor'     => '#2F2F2F',
				'textAlignment' => 'center',
				'lineHeight'    => 24,
			),
			'titleStyle' => array(
				'textAlignment' => 'left',
				'fontName'      => 'HelveticaNeue-Bold',
				'fontSize'      => 64,
				'lineHeight'    => 74,
				'textColor'     => '#000',
			),
			'introStyle' => array(
				'textAlignment' => 'left',
				'fontName'      => 'HelveticaNeue-Medium',
				'fontSize'      => 24,
				'textColor'     => '#000',
			),
			'authorStyle' => array(
				'textAlignment' => 'left',
				'fontName'      => 'HelveticaNeue-Bold',
				'fontSize'      => 16,
				'textColor'     => '#000',
			),
			'bodyStyle' => array(
				'textAlignment' => 'left',
				'fontName'      => 'Georgia',
				'fontSize'      => 18,
				'lineHeight'    => 26,
				'textColor'     => '#000',
			),
			'captionStyle' => array(
				'textAlignment' => 'left',
				'fontName'      => 'HelveticaNeue-Medium',
				'fontSize'      => 12,
				'lineHeight'    => 17,
				'textColor'     => '#000',
			),
			'heading1Style' => array(
				'textAlignment' => 'left',
				'fontName'      => 'HelveticaNeue-Medium',
				'fontSize'      => 28,
				'lineHeight'    => 41,
				'textColor'     => '#000',
			),
			'pullquoteStyle' => array(
				'textAlignment' => 'left',
				'fontName'      => 'HelveticaNeue-Bold',
				'fontSize'      => 28,
				'lineHeight'    => 41,
				'textColor'     => '#000',
			),
		);
	}

	protected function getComponentLayouts()
	{
		return array(
			'headerImageLayout' => array(
				'columnStart'          => 0,
				'columnSpan'           => 7,
				'ignoreDocumentMargin' => true,
				'minimumHeight'        => '42vh',
			),
			'titleLayout' => array(
				'columnStart' => 0,
				'columnSpan'  => 7,
				'margin'      => array('top' => 30, 'bottom' => 10),
			),
			'introLayout' => array(
				'columnStart' => 0,
				'columnSpan'  => 7,
				'margin'      => array('top' => 15, 'bottom' => 15),
			),
			'authorLayout' => array(
				'columnStart' => 0,
				'columnSpan'  => 7,
				'margin'      => array('top' => 15, 'bottom' => 15),
			),
			'bodyLayout' => array(
				'columnStart' => 0,
				'columnSpan'  => 5,
				'margin'      => array('top' => 15, 'bottom' => 15),
			),
			'captionLayout' => array(
				'columnStart' => 5,
				'columnSpan'  => 2,
				'margin'      => array('top' => 15, 'bottom' => 30),
			),
			'heading1Layout' => array(
				'columnStart' => 0,
				'columnSpan'  => 5,
				'margin'      => array('top' => 15),
			),
			'pullquoteLayout' => array(
				'columnStart' => 0,
				'columnSpan'  => 7,
				'margin'      => array('top' => 15, 'bottom' => 15),
			),
			'photoLayout' => array(
				'columnStart' => 0,
				'columnSpan'  => 7,
				'margin'      => array('top' => 15, 'bottom' => 15),
			),
		);
	}
}
