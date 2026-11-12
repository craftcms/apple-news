<?php

declare(strict_types=1);

use CraftCms\AppleNews\Tests\TestCase;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Entry\Models\Entry as EntryModel;
use CraftCms\Cms\Section\Models\Section;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

uses(TestCase::class)->in('Feature');

/**
 * Creates a live entry in the `news` section, which the test channel matches.
 *
 * @param  array<string, mixed>  $attributes
 */
function newsEntry(array $attributes = [], string $section = 'news'): Entry
{
    $sectionModel = Section::query()->where('handle', $section)->first()
        ?? Section::factory()->create(['handle' => $section, 'name' => ucfirst($section)]);

    return EntryModel::factory()->createElement([
        'sectionId' => $sectionModel->id,
        'title' => 'Apple News story',
        ...$attributes,
    ]);
}

/**
 * The `data` part of an Apple News article response.
 *
 * @return array<string, mixed>
 */
function appleArticle(string $state = 'PROCESSING', string $revision = 'rev1', string $id = 'article-1'): array
{
    return [
        'id' => $id,
        'revision' => $revision,
        'isSponsored' => false,
        'isPreview' => false,
        'state' => $state,
        'shareUrl' => 'https://apple.news/Aabc',
    ];
}

/**
 * Fakes the Apple News API. Article POSTs get `$post`’s response, or a new article in PROCESSING.
 */
function fakeAppleNews(?Closure $post = null): void
{
    Http::fake(['news-api.apple.com/*' => function (Request $request) use ($post) {
        $path = parse_url($request->url(), PHP_URL_PATH);

        return match ($request->method()) {
            'POST' => $post ? $post($request) : Http::response(['data' => appleArticle()], 201),
            'DELETE' => Http::response(null, 204),
            default => match (true) {
                str_ends_with($path, '/sections') => Http::response(['data' => [['id' => 's1', 'name' => 'Main']]]),
                str_starts_with($path, '/channels/') => Http::response(['data' => ['name' => 'Test channel']]),
                default => Http::response(['data' => appleArticle('LIVE', 'rev2', basename($path))]),
            },
        };
    }]);
}
