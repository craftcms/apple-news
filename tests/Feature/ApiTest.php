<?php

declare(strict_types=1);

use CraftCms\AppleNews\Api;
use CraftCms\AppleNews\Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * Checks a request’s HHMAC signature the way Apple documents it, independently of {@see Api::sign()}.
 */
function hasValidSignature(Request $request): bool
{
    $psr = $request->toPsrRequest();

    if (! preg_match('/^HHMAC; key=([^;]+); signature=([^;]+); date=(\d{4}-\d\d-\d\dT\d\d:\d\d:\d\dZ)$/', $psr->getHeaderLine('Authorization'), $m)) {
        return false;
    }

    $canonical = $psr->getMethod().$psr->getUri().$m[3];
    if ($psr->getMethod() === 'POST') {
        $canonical .= $psr->getHeaderLine('Content-Type').$psr->getBody();
    }

    $expected = base64_encode(hash_hmac('sha256', $canonical, base64_decode(TestCase::API_SECRET), true));

    return $m[1] === TestCase::API_KEY_ID && hash_equals($expected, $m[2]);
}

/**
 * @return array<string, array{disposition: string, type: string, content: string}>
 */
function multipartParts(Request $request): array
{
    $psr = $request->toPsrRequest();
    preg_match('/boundary=(.+)$/', $psr->getHeaderLine('Content-Type'), $boundary);
    $parts = [];

    foreach (array_slice(explode("--$boundary[1]", (string) $psr->getBody()), 1, -1) as $part) {
        [$headers, $content] = explode("\r\n\r\n", ltrim($part, "\r\n"), 2);
        preg_match('/^Content-Disposition: (.+)$/mi', $headers, $disposition);
        preg_match('/^Content-Type: (.+)$/mi', $headers, $type);
        preg_match('/name=([^;]+)/', $disposition[1], $name);
        $parts[$name[1]] = [
            'disposition' => trim($disposition[1]),
            'type' => trim($type[1]),
            'content' => substr($content, 0, -2),
        ];
    }

    return $parts;
}

it('signs GET requests, including the query string', function () {
    fakeAppleNews();

    app(Api::class)->search(TestCase::CHANNEL_ID, ['pageSize' => 5]);

    Http::assertSent(fn (Request $request) => $request->method() === 'GET'
        && $request->url() === 'https://news-api.apple.com/channels/'.TestCase::CHANNEL_ID.'/articles?pageSize=5'
        && hasValidSignature($request));
});

it('sends an article as signed multipart parts', function () {
    fakeAppleNews();

    $response = app(Api::class)->updateArticle(TestCase::CHANNEL_ID, 'article-1', [
        'json' => '{"title":"Hi"}',
        'metadata' => '{"data":{"revision":"rev1"}}',
        'files' => ['image.png' => dirname(__DIR__).'/Fixtures/photo.png'],
    ]);

    expect($response->data->state)->toBe('PROCESSING');

    Http::assertSent(fn (Request $request) => $request->method() === 'POST'
        && $request->url() === 'https://news-api.apple.com/articles/article-1'
        && hasValidSignature($request));

    $parts = multipartParts(Http::recorded()->first()[0]);

    expect($parts)->toHaveKeys(['article', 'metadata', 'image'])
        ->and($parts['article'])->toMatchArray([
            'disposition' => 'form-data; name=article; filename=article.json; size=14',
            'type' => 'application/json',
            'content' => '{"title":"Hi"}',
        ])
        ->and($parts['metadata'])->toMatchArray([
            'disposition' => 'form-data; name=metadata; size=28',
            'type' => 'application/json',
            'content' => '{"data":{"revision":"rev1"}}',
        ])
        ->and($parts['image']['disposition'])->toBe('form-data; name=image; filename=image.png; size=94')
        ->and($parts['image']['type'])->toBe('image/png')
        ->and($parts['image']['content'])->toBe(file_get_contents(dirname(__DIR__).'/Fixtures/photo.png'));
});

it('throws Apple’s errors', function () {
    fakeAppleNews(fn () => Http::response(['errors' => [['code' => 'INVALID_DOCUMENT']]], 400));

    expect(fn () => app(Api::class)->createArticle(TestCase::CHANNEL_ID, ['json' => '{}']))
        ->toThrow(fn (RequestException $e) => expect($e->response->json('errors.0.code'))->toBe('INVALID_DOCUMENT'));
});

it('rejects files Apple News doesn’t accept', function () {
    fakeAppleNews();

    expect(fn () => app(Api::class)->createArticle(TestCase::CHANNEL_ID, [
        'json' => '{}',
        'files' => ['script.php' => __FILE__],
    ]))->toThrow(UnexpectedValueException::class);

    Http::assertNothingSent();
});
