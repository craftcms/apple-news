<?php

declare(strict_types=1);

use CraftCms\AppleNews\ElementActions\PublishArticles;
use CraftCms\AppleNews\Models\Article;
use CraftCms\AppleNews\Tests\TestCase;
use CraftCms\Cms\Edition;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Support\Facades\Elements;
use CraftCms\Cms\User\Elements\User;
use CraftCms\Cms\User\Models\User as UserModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

beforeEach(function () {
    Edition::set(Edition::Pro);
    Queue::fake();
    fakeAppleNews();
    $this->entry = newsEntry(['slug' => 'apple-news-story']);
    $this->params = [
        'entryId' => $this->entry->id,
        'siteId' => $this->entry->siteId,
        'channelId' => TestCase::CHANNEL_ID,
    ];
});

/**
 * @param  string[]  $permissions  Permissions for the `news` section
 */
function editor(array $permissions): User
{
    $uid = Entry::find()->section('news')->one()->getSection()->uid;

    return UserModel::factory()
        ->withPermissions(['accessCp', ...array_map(fn ($p) => "$p:$uid", $permissions)])
        ->createElement();
}

it('requires a login', function (string $method, string $action) {
    $this->{$method}("/actions/apple-news/article/$action?".http_build_query($this->params))->assertUnauthorized();
})->with([
    ['getJson', 'download'],
    ['postJson', 'get-info'],
    ['postJson', 'publish'],
]);

it('requires permission to save the entry to publish it', function () {
    actingAs(editor(['viewEntries', 'viewPeerEntries']));

    postJson('/actions/apple-news/article/get-info', $this->params)->assertOk();
    postJson('/actions/apple-news/article/publish', $this->params)->assertForbidden();

    expect(Article::query()->count())->toBe(0);
});

it('queues the entry and returns its article info', function () {
    actingAs(User::find()->admin()->one());

    postJson('/admin/actions/apple-news/article/publish', $this->params)
        ->assertOk()
        ->assertJsonPath('infos.'.TestCase::CHANNEL_ID.'.state', 'QUEUED')
        ->assertJsonPath('infos.'.TestCase::CHANNEL_ID.'.isQueued', true)
        ->assertJsonPath('infos.'.TestCase::CHANNEL_ID.'.canPublish', true);
});

it('rejects unknown channels', function (string $method, string $action) {
    actingAs(User::find()->admin()->one());

    $this->{$method}("/actions/apple-news/article/$action?".http_build_query([...$this->params, 'channelId' => 'nope']))
        ->assertUnprocessable()
        ->assertJsonValidationErrors('channelId');

    expect(Article::query()->count())->toBe(0);
    Queue::assertNothingPushed();
})->with([
    ['getJson', 'download'],
    ['postJson', 'get-info'],
    ['postJson', 'publish'],
]);

it('downloads the article bundle for News Preview', function () {
    actingAs(User::find()->admin()->one());

    $response = get('/actions/apple-news/article/download?'.http_build_query($this->params))
        ->assertOk()
        ->assertDownload('apple-news-story.zip');

    $zip = new ZipArchive;
    $zip->open($response->baseResponse->getFile()->getPathname());

    expect($zip->getFromName('photo.png'))->toBe(file_get_contents(dirname(__DIR__).'/Fixtures/photo.png'))
        ->and(json_decode($zip->getFromName('article.json'), true))->toMatchArray(['title' => 'Apple News story']);
    Http::assertNothingSent();
});

describe('bulk action', function () {
    it('is offered to people who can save entries in the section', function (array $permissions, bool $offered) {
        actingAs(editor($permissions));
        $source = 'section:'.$this->entry->getSection()->uid;

        expect(in_array(PublishArticles::class, Entry::actions($source), true))->toBe($offered);
    })->with([
        'view only' => [['viewEntries'], false],
        'save' => [['viewEntries', 'saveEntries'], true],
    ]);

    it('only queues entries the user can save', function () {
        $ownEntry = newsEntry();
        actingAs($editor = editor(['viewEntries', 'viewPeerEntries', 'saveEntries']));
        $ownEntry->setAuthorIds([$editor->id]);
        Elements::saveElement($ownEntry);
        Queue::fake();

        $action = new PublishArticles;
        $result = $action->performAction(Entry::find()->id([$this->entry->id, $ownEntry->id]));

        expect($result)->toBeTrue()
            ->and(Article::query()->pluck('entryId')->all())->toBe([$ownEntry->id]);
    });

    it('reports when the user can’t save any of the entries', function () {
        actingAs(editor(['viewEntries', 'viewPeerEntries', 'saveEntries']));

        $action = new PublishArticles;

        expect($action->performAction(Entry::find()->id($this->entry->id)))->toBeFalse()
            ->and($action->getMessage())->toBe('You don’t have permission to publish the selected entries.')
            ->and(Article::query()->count())->toBe(0);
    });
});
