<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Listeners;

use CraftCms\AppleNews\Articles;
use CraftCms\AppleNews\Channels;
use CraftCms\Cms\Element\Events\ElementEditorPayloadResolving;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Support\Arr;
use CraftCms\Cms\Support\Url;

/**
 * Gives the entry editor what its Apple News Channels panel shows: the channels that match the entry and the state
 * of the entry’s article in each.
 *
 * The panel itself is registered from `resources/js/cp.js`.
 *
 * @since 5.0.0
 */
class AddArticlePane
{
    public function __construct(
        private readonly Channels $channels,
        private readonly Articles $articles,
    ) {}

    public function handle(ElementEditorPayloadResolving $event): void
    {
        $entry = $event->element;

        if (! $entry instanceof Entry || ! $entry->id || $entry->getIsUnpublishedDraft()) {
            return;
        }

        $channels = array_filter(
            $this->channels->all(),
            fn ($channel) => $channel->matchEntry($entry),
        );

        if (! $channels) {
            return;
        }

        $infos = $this->articles->info($entry, array_keys($channels));
        $isDraftOrRevision = $entry->getIsDraft() || $entry->getIsRevision();
        $downloadParams = [
            'entryId' => $entry->getCanonicalId(),
            'siteId' => $entry->siteId,
            'draftId' => $entry->getIsDraft() ? $entry->draftId : null,
            'revisionId' => $entry->getIsRevision() ? $entry->revisionId : null,
        ];

        $event->data['appleNews'] = [
            'entryId' => $entry->getCanonicalId(),
            'siteId' => $entry->siteId,
            'isDraftOrRevision' => $isDraftOrRevision,
            'channels' => array_values(array_map(fn ($channel) => [
                'id' => $channel->getChannelId(),
                'name' => $this->channels->name($channel->getChannelId()),
                'state' => null,
                'shareUrl' => null,
                'isQueued' => false,
                'isProcessing' => false,
                'isShareable' => false,
                ...Arr::only($infos[$channel->getChannelId()] ?? [], ['state', 'shareUrl', 'isQueued', 'isProcessing', 'isShareable']),
                'canPublish' => ! $isDraftOrRevision && $channel->canPublish($entry),
                'downloadUrl' => Url::actionUrl('apple-news/article/download', array_filter([
                    ...$downloadParams,
                    'channelId' => $channel->getChannelId(),
                ], fn ($value) => $value !== null)),
            ], $channels)),
        ];
    }
}
