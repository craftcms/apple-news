<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Jobs;

use CraftCms\AppleNews\ArticleManager;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Queue\Job;
use CraftCms\Cms\Support\Facades\I18N;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Http\Client\RequestException;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Override;
use Throwable;

/**
 * Publishes an entry to one Apple News channel.
 *
 * Only one job waits in the queue per entry and channel, and only one runs at a time, so two workers can’t create
 * the same article twice.
 *
 * Connection errors, server errors and throttling are retried. Other errors from Apple News fail the job right away,
 * because they would fail the same way again. Once the job has failed, the article goes back to its previous state.
 *
 * @since 5.0.0
 */
class PublishArticleJob extends Job implements ShouldBeUniqueUntilProcessing
{
    /**
     * Attempts, so a job that waits for another one to finish isn’t failed. Exceptions are limited by $maxExceptions.
     */
    public int $tries = 10;

    public int $maxExceptions = 3;

    /**
     * Seconds to wait before retrying after an exception.
     *
     * @var int[]
     */
    public array $backoff = [10, 30];

    /**
     * Seconds before a waiting job’s uniqueness lock expires, in case the job is removed from the queue without
     * running. Overlapping runs are still prevented after that by {@see middleware()}.
     */
    public int $uniqueFor = 300;

    public function __construct(
        public readonly int $entryId,
        public readonly int $siteId,
        public readonly string $channelId,
    ) {
        parent::__construct();
    }

    public function uniqueId(): string
    {
        return "$this->entryId:$this->channelId";
    }

    /**
     * @return array<object>
     */
    #[Override]
    public function middleware(): array
    {
        return [
            ...parent::middleware(),
            (new WithoutOverlapping($this->uniqueId()))
                ->releaseAfter(10)
                ->expireAfter($this->timeout),
        ];
    }

    public function handle(ArticleManager $articleManager): void
    {
        /** @var Entry|null $entry */
        $entry = Entry::find()
            ->id($this->entryId)
            ->siteId($this->siteId)
            ->status(null)
            ->one();

        if (! $entry) {
            return;
        }

        $this->setProgress(0, $entry->title);

        try {
            $articleManager->publish($entry, $this->channelId);
        } catch (RequestException $e) {
            if ($e->response->clientError() && $e->response->status() !== 429) {
                $this->fail($e);

                return;
            }

            throw $e;
        }
    }

    public function failed(?Throwable $exception): void
    {
        app(ArticleManager::class)->unqueue($this->entryId, $this->channelId);
    }

    #[Override]
    protected function defaultDescription(): string
    {
        return I18N::prep('Publishing to Apple News', [], 'apple-news');
    }
}
