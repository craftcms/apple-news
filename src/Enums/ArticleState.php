<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Enums;

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
}
