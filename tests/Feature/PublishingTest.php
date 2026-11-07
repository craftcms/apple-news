<?php

declare(strict_types=1);

use CraftCms\AppleNews\ArticleManager;
use CraftCms\AppleNews\Enums\ArticleState;
use CraftCms\AppleNews\Jobs\PublishArticleJob;
use CraftCms\AppleNews\Models\Article;
use CraftCms\AppleNews\Settings;
use CraftCms\AppleNews\Tests\TestCase;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Support\Facades\Elements;
use CraftCms\Cms\Support\Json;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

function article(Entry $entry): ?Article
{
    return Article::query()->forEntry($entry->id)->first();
}

/**
 * Saves a record of an article that Apple News has published.
 */
function publishedArticle(Entry $entry, string $state = 'LIVE'): Article
{
    $data = appleArticle($state, 'rev2');

    return Article::create([
        'entryId' => $entry->id,
        'channelId' => TestCase::CHANNEL_ID,
        'articleId' => $data['id'],
        'revisionId' => $data['revision'],
        'state' => ArticleState::from($state),
        'shareUrl' => $data['shareUrl'],
        'response' => Json::encode(['data' => $data]),
    ]);
}

function postedArticles(): array
{
    return Http::recorded(fn(Request $request) => $request->method() === 'POST')
        ->map(fn($pair) => parse_url($pair[0]->url(), PHP_URL_PATH))
        ->values()
        ->all();
}

it('creates the article, then updates it with the latest revision', function() {
    fakeAppleNews();
    $entry = newsEntry();
    $manager = app(ArticleManager::class);

    $manager->queue($entry);

    expect(article($entry))
        ->state->toBe(ArticleState::Processing)
        ->articleId->toBe('article-1')
        ->revisionId->toBe('rev1');

    // Refreshing a processing article picks up Apple’s current state
    expect($manager->getArticleInfo($entry, refresh: true)[TestCase::CHANNEL_ID])->toMatchArray([
        'state' => 'LIVE',
        'revisionId' => 'rev2',
        'isQueued' => false,
        'isProcessing' => false,
        'isShareable' => true,
    ]);

    $manager->queue($entry);

    expect(postedArticles())->toBe(['/channels/' . TestCase::CHANNEL_ID . '/articles', '/articles/article-1']);
    Http::assertSent(fn(Request $request) => $request->method() === 'POST'
        && str_contains((string) $request->toPsrRequest()->getBody(), '"revision":"rev2"'));
});

it('marks articles as queued until their job runs', function() {
    Queue::fake();
    $entry = newsEntry();
    publishedArticle($entry);
    $newEntry = newsEntry();

    app(ArticleManager::class)->queue($entry);
    app(ArticleManager::class)->queue($newEntry);

    expect(article($entry)->state)->toBe(ArticleState::QueuedUpdate)
        ->and(article($newEntry)->state)->toBe(ArticleState::Queued)
        ->and(app(ArticleManager::class)->getArticleInfo($entry)[TestCase::CHANNEL_ID])
        ->toMatchArray(['isQueued' => true, 'isShareable' => true]);
});

it('only queues one job per article', function() {
    Queue::fake();
    $entry = newsEntry();

    app(ArticleManager::class)->queue($entry);
    app(ArticleManager::class)->queue($entry);

    Queue::assertPushed(PublishArticleJob::class, 1);
});

it('publishes the entry’s current content even if its article isn’t queued anymore', function() {
    fakeAppleNews();
    $entry = newsEntry();
    publishedArticle($entry);

    // e.g. a job for an earlier save has already replaced the queued state
    (new PublishArticleJob($entry->id, $entry->siteId, TestCase::CHANNEL_ID))->handle(app(ArticleManager::class));

    expect(postedArticles())->toBe(['/articles/article-1']);
});

it('doesn’t queue entries for channels that don’t match them', function() {
    Queue::fake();
    $entry = newsEntry(section: 'pages');

    app(ArticleManager::class)->queue($entry, TestCase::CHANNEL_ID);

    expect(article($entry))->toBeNull();
    Queue::assertNothingPushed();
});

it('restores the previous state when Apple rejects an update', function() {
    fakeAppleNews(fn() => Http::response(['errors' => [['code' => 'INVALID_DOCUMENT']]], 400));
    $entry = newsEntry();
    publishedArticle($entry);

    app(ArticleManager::class)->queue($entry);

    expect(article($entry))->state->toBe(ArticleState::Live)->revisionId->toBe('rev2')
        ->and(postedArticles())->toHaveCount(1);
});

it('removes the record when a new article is rejected', function() {
    fakeAppleNews(fn() => Http::response(['errors' => [['code' => 'INVALID_DOCUMENT']]], 400));
    $entry = newsEntry();

    app(ArticleManager::class)->queue($entry);

    expect(article($entry))->toBeNull();
});

it('fails the job right away when Apple rejects the article', function() {
    fakeAppleNews(fn() => Http::response(['errors' => [['code' => 'INVALID_DOCUMENT']]], 400));
    $entry = newsEntry();
    $job = (new PublishArticleJob($entry->id, $entry->siteId, TestCase::CHANNEL_ID))->withFakeQueueInteractions();

    $job->handle(app(ArticleManager::class));

    $job->assertFailed();
});

it('leaves the article queued for a retry after a server error', function(int $status) {
    fakeAppleNews(fn() => Http::response(null, $status));
    Queue::fake();
    $entry = newsEntry();
    publishedArticle($entry);
    app(ArticleManager::class)->queue($entry);
    $job = (new PublishArticleJob($entry->id, $entry->siteId, TestCase::CHANNEL_ID))->withFakeQueueInteractions();

    expect(fn() => $job->handle(app(ArticleManager::class)))->toThrow(RequestException::class);

    $job->assertNotFailed();
    expect(article($entry)->state)->toBe(ArticleState::QueuedUpdate);

    // After the last attempt
    $job->failed(null);
    expect(article($entry)->state)->toBe(ArticleState::Live);
})->with([503, 429]);

it('unqueues the article if the entry can’t be published anymore when the job runs', function() {
    Queue::fake();
    $entry = newsEntry();
    publishedArticle($entry);
    $newEntry = newsEntry();
    app(ArticleManager::class)->queue($entry);
    app(ArticleManager::class)->queue($newEntry);

    fakeAppleNews();
    foreach ([$entry, $newEntry] as $e) {
        $e->enabled = false;
        Elements::saveElement($e);
        (new PublishArticleJob($e->id, $e->siteId, TestCase::CHANNEL_ID))->handle(app(ArticleManager::class));
    }

    expect(article($entry)->state)->toBe(ArticleState::Live)
        ->and(article($newEntry))->toBeNull();
    Http::assertNothingSent();
});

it('publishes entries when they’re saved, if autoPublishOnSave is enabled', function(bool $autoPublish) {
    fakeAppleNews();
    app(Settings::class)->autoPublishOnSave = $autoPublish;
    $entry = newsEntry();

    $entry->title = 'Updated';
    Elements::saveElement($entry);

    expect(postedArticles())->toHaveCount($autoPublish ? 1 : 0);
})->with([true, false]);

it('deletes articles from Apple News when the entry is deleted', function() {
    Queue::fake();
    fakeAppleNews();
    $entry = newsEntry();
    publishedArticle($entry);
    $queuedEntry = newsEntry();
    app(ArticleManager::class)->queue($queuedEntry);

    Elements::deleteElement($entry, hardDelete: true);
    Elements::deleteElement($queuedEntry, hardDelete: true);

    Http::assertSentCount(1);
    Http::assertSent(fn(Request $request) => $request->method() === 'DELETE'
        && $request->url() === 'https://news-api.apple.com/articles/article-1');
    expect(Article::query()->count())->toBe(0);
});
