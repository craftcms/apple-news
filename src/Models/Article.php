<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Models;

use CraftCms\AppleNews\Enums\ArticleState;
use CraftCms\Cms\Shared\BaseModel;
use CraftCms\Cms\Shared\Concerns\HasUid;
use CraftCms\Cms\Support\Arr;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

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
 * @method static Builder<self> forEntry(int $entryId)
 * @method static Builder<self> inChannels(string|string[]|null $channelIds)
 *
 * @since 2.0
 */
class Article extends BaseModel
{
    use HasUid;

    protected $table = 'applenews_articles';

    protected function casts(): array
    {
        return [
            'entryId' => 'integer',
            'isSponsored' => 'boolean',
            'isPreview' => 'boolean',
            'state' => ArticleState::class,
        ];
    }

    /**
     * Limits the query to an entry’s articles.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    #[Scope]
    protected function forEntry(Builder $query, int $entryId): Builder
    {
        return $query->where('entryId', $entryId);
    }

    /**
     * Limits the query to articles in the given channels. `null` means all channels.
     *
     * @param  Builder<self>  $query
     * @param  string|string[]|null  $channelIds
     * @return Builder<self>
     */
    #[Scope]
    protected function inChannels(Builder $query, array|string|null $channelIds): Builder
    {
        return $channelIds === null ? $query : $query->whereIn('channelId', Arr::wrap($channelIds));
    }
}
