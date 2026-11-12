<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Http\Requests;

/**
 * Validates a request to queue an entry to be published to Apple News.
 *
 * @since 5.0.0
 */
class PublishArticleRequest extends ArticleRequest
{
    protected string $ability = 'save';
}
