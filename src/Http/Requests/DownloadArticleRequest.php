<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Http\Requests;

/**
 * Validates a request to download an entry’s article bundle for News Preview.
 *
 * @since 5.0.0
 */
class DownloadArticleRequest extends ArticleRequest
{
    protected bool $acceptsDrafts = true;

    protected bool $requiresChannel = true;
}
