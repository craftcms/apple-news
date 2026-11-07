<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Models;

use CraftCms\AppleNews\Enums\ArticleState;
use CraftCms\Cms\Shared\BaseModel;
use CraftCms\Cms\Shared\Concerns\HasUid;

/**
 * An entry’s article in one Apple News channel.
 *
 * @property int $id
 * @property int $entryId
 * @property string $channelId
 * @property string|null $articleId
 * @property string|null $revisionId
 * @property bool $isSponsored
 * @property bool $isPreview
 * @property ArticleState $state
 * @property string|null $shareUrl
 * @property string|null $response
 *
 * @since 2.0
 */
class Article extends BaseModel
{
    use HasUid;

    protected $table = 'applenews_articles';

    protected $casts = [
        'entryId' => 'integer',
        'isSponsored' => 'boolean',
        'isPreview' => 'boolean',
        'state' => ArticleState::class,
    ];
}
