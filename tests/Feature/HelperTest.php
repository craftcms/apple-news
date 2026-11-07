<?php

declare(strict_types=1);

use CraftCms\AppleNews\Helper;

it('removes HTML comments, including ones that span lines', function () {
    expect(Helper::html2Markdown("<p>a<!-- note -->b<!--\nmulti\nline\n-->c</p>"))->toBe('abc')
        ->and(Helper::html2Components("<p>a<!-- note -->b</p><!--\n<h1>Hidden</h1>\n-->"))->toBe([
            ['role' => 'body', 'text' => 'ab', 'format' => 'markdown'],
        ]);
});

it('turns HTML into components', function () {
    expect(Helper::html2Components('<h2>Title</h2><p>One</p><p>Two <strong>bold</strong></p><hr><blockquote>Quote</blockquote>', [
        'heading' => ['textStyle' => 'heading'],
    ]))->toBe([
        ['role' => 'heading2', 'text' => 'Title', 'format' => 'markdown', 'textStyle' => 'heading'],
        ['role' => 'body', 'text' => "One\n\nTwo **bold**", 'format' => 'markdown'],
        ['role' => 'divider'],
        ['role' => 'quote', 'text' => 'Quote', 'format' => 'markdown'],
    ]);
});

it('formats language codes for Apple News', function (string $language, string $expected) {
    expect(Helper::formatLanguage($language))->toBe($expected);
})->with([
    ['en', 'en'],
    ['en-us', 'en_US'],
    ['pt_BR', 'pt_BR'],
]);
