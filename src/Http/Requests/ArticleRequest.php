<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Http\Requests;

use CraftCms\AppleNews\ChannelInterface;
use CraftCms\AppleNews\Channels;
use CraftCms\Cms\Entry\Elements\Entry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use LogicException;
use Override;

/**
 * A request about an entry’s articles: validates the entry and channel params, loads the entry, and checks that the
 * user may perform {@see $ability} on it.
 *
 * @since 5.0.0
 */
abstract class ArticleRequest extends FormRequest
{
    /**
     * The entry policy ability the user needs.
     */
    protected string $ability = 'view';

    /**
     * Whether `draftId` and `revisionId` params can point at a draft or revision of the entry.
     */
    protected bool $acceptsDrafts = false;

    /**
     * Whether the `channelId` param is required.
     */
    protected bool $requiresChannel = false;

    private ?Entry $entry = null;

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'entryId' => ['required', 'integer'],
            'siteId' => ['required', 'integer'],
            'draftId' => ['nullable', 'integer'],
            'revisionId' => ['nullable', 'integer'],
            'channelId' => [
                $this->requiresChannel ? 'required' : 'nullable',
                'string',
                Rule::in(array_keys(app(Channels::class)->all())),
            ],
        ];
    }

    /**
     * Returns the entry, or its draft or revision.
     */
    public function entry(): Entry
    {
        return $this->entry ?? throw new LogicException('The request hasn’t been validated yet.');
    }

    /**
     * Returns the requested channel, if any.
     */
    public function channel(): ?ChannelInterface
    {
        $channelId = $this->validated('channelId');

        return $channelId !== null ? app(Channels::class)->get($channelId) : null;
    }

    #[Override]
    protected function passedValidation(): void
    {
        $query = Entry::find()
            ->siteId($this->integer('siteId'))
            ->status(null);

        if ($this->acceptsDrafts && $this->integer('draftId')) {
            $query->draftId($this->integer('draftId'));
        } elseif ($this->acceptsDrafts && $this->integer('revisionId')) {
            $query->revisionId($this->integer('revisionId'));
        } else {
            $query->id($this->integer('entryId'));
        }

        /** @var Entry|null $entry */
        $entry = $query->one();

        abort_unless($entry !== null, 400, 'No entry exists with the given ID.');

        Gate::authorize($this->ability, $entry);

        $this->entry = $entry;
    }
}
