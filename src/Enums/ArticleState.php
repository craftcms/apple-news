<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Enums;

use function CraftCms\Cms\t;

/**
 * The state of an entry’s article in an Apple News channel.
 *
 * The plugin sets the queued states while a publish job is pending. Apple News reports the others.
 *
 * @see https://developer.apple.com/documentation/applenewsapi/article
 * @since 5.0.0
 */
enum ArticleState: string
{
    case Queued = 'QUEUED';
    case QueuedUpdate = 'QUEUED_UPDATE';
    case Processing = 'PROCESSING';
    case ProcessingUpdate = 'PROCESSING_UPDATE';
    case Live = 'LIVE';
    case FailedProcessing = 'FAILED_PROCESSING';
    case FailedProcessingUpdate = 'FAILED_PROCESSING_UPDATE';
    case TakenDown = 'TAKEN_DOWN';
    case Duplicate = 'DUPLICATE';

    /**
     * Whether a publish job is pending for the article.
     */
    public function isQueued(): bool
    {
        return $this === self::Queued || $this === self::QueuedUpdate;
    }

    /**
     * Whether Apple News is processing the article, so its state will change without the plugin doing anything.
     */
    public function isProcessing(): bool
    {
        return $this === self::Processing || $this === self::ProcessingUpdate;
    }

    /**
     * Whether a version of the article is, or is about to be, visible in the News app.
     */
    public function isShareable(): bool
    {
        return $this === self::QueuedUpdate || $this->isProcessing() || $this === self::Live;
    }

    /**
     * Describes an article’s state for the entry editor’s Apple News panel.
     *
     * @param  self|null  $state  The article’s state, or `null` if the entry hasn’t been published to the channel
     * @return array{label: string, description: string, color: string} The status badge’s label, a sentence describing the state, and the badge color
     */
    public static function status(?self $state): array
    {
        [$label, $description, $color] = match ($state) {
            self::Queued => ['Queued', 'The article is in the queue to be published.', 'gray'],
            self::QueuedUpdate => ['Update queued', 'A previous version of the article has been published, and an update is currently in the queue to be published.', 'gray'],
            self::Processing => ['Processing', 'The article has been published and is going through processing.', 'orange'],
            self::ProcessingUpdate => ['Update processing', 'A previous version of the article is visible in the News app, and an update is currently in processing.', 'orange'],
            self::Live => ['Live', 'The article has been published, finished processing, and is visible in the News app.', 'green'],
            self::FailedProcessing => ['Failed', 'The article failed during processing and is not visible in the News app.', 'red'],
            self::FailedProcessingUpdate => ['Update failed', 'A previous version of the article is visible in the News app, but an update failed during processing.', 'red'],
            self::Duplicate => ['Duplicate', 'The article is a duplicate of another article and is not visible in the News app.', 'gray'],
            self::TakenDown => ['Taken down', 'The article was previously visible in the News app, but was taken down.', 'gray'],
            null => ['Not published', 'The article has not been published yet.', 'gray'],
        };

        return [
            'label' => t($label, category: 'apple-news'),
            'description' => t($description, category: 'apple-news'),
            'color' => $color,
        ];
    }
}
