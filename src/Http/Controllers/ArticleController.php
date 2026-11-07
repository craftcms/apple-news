<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Http\Controllers;

use CraftCms\AppleNews\ArticleManager;
use CraftCms\AppleNews\ChannelManager;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Support\Facades\Path;
use CraftCms\Cms\Support\File;
use CraftCms\Cms\Support\Json;
use CraftCms\Cms\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

/** @since 2.0 */
class ArticleController
{
    public function __construct(
        private readonly ArticleManager $articleManager,
        private readonly ChannelManager $channelManager,
    ) {
    }

    /**
     * Downloads a bundle for Apple News Preview.
     */
    public function download(Request $request): BinaryFileResponse
    {
        $entry = $this->entry($request, 'view', acceptRevision: true);
        $request->validate(['channelId' => ['required', ...$this->channelRules()]]);
        $channel = $this->channelManager->getChannelById($request->input('channelId'));

        abort_unless($channel->matchEntry($entry), 400, 'This channel does not want anything to do with this entry.');

        $article = $channel->createArticle($entry);

        // Prep the zip staging folder
        $zipDir = Path::temp('apple-news-articles');
        File::ensureDirectoryExists($zipDir);
        $zipPath = "$zipDir/$entry->slug-" . Str::uuid()->toString() . '.zip';

        // Create the zip
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            throw new RuntimeException('Cannot create zip at ' . $zipPath);
        }

        // Add article.json
        $zip->addFromString('article.json', Json::encode($article->getContent(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

        // Add the article files
        foreach ($article->getFiles() as $uri => $path) {
            $zip->addFile($path, $uri);
        }

        $zip->close();

        return response()->download($zipPath, "$entry->slug.zip")->deleteFileAfterSend();
    }

    /**
     * Returns the latest info about an entry's articles.
     */
    public function getInfo(Request $request): JsonResponse
    {
        $entry = $this->entry($request, 'view');
        $request->validate(['channelId' => ['nullable', ...$this->channelRules()]]);

        return response()->json([
            'infos' => $this->getArticleInfo($entry, $request->input('channelId'), true),
        ]);
    }

    /**
     * Queues an entry to be published to Apple News.
     */
    public function publish(Request $request): JsonResponse
    {
        $entry = $this->entry($request, 'save');
        $request->validate(['channelId' => ['nullable', ...$this->channelRules()]]);
        $channelId = $request->input('channelId');
        $this->articleManager->queue($entry, $channelId);

        return response()->json([
            'success' => true,
            'infos' => $this->getArticleInfo($entry, $channelId),
        ]);
    }

    private function entry(Request $request, string $ability, bool $acceptRevision = false): Entry
    {
        $request->validate([
            'entryId' => ['required', 'integer'],
            'siteId' => ['required', 'integer'],
            'draftId' => ['nullable', 'integer'],
            'revisionId' => ['nullable', 'integer'],
        ]);

        $draftId = $acceptRevision ? $request->integer('draftId') : 0;
        $revisionId = $acceptRevision ? $request->integer('revisionId') : 0;

        $query = Entry::find()
            ->siteId($request->integer('siteId'))
            ->status(null);

        if ($draftId) {
            $query->draftId($draftId);
        } elseif ($revisionId) {
            $query->revisionId($revisionId);
        } else {
            $query->id($request->integer('entryId'));
        }

        /** @var Entry|null $entry */
        $entry = $query->one();

        abort_unless($entry !== null, 400);

        Gate::authorize($ability, $entry);

        return $entry;
    }

    /**
     * Validation rules for a `channelId` param, which must be one of the configured channels.
     *
     * @return array<mixed>
     */
    private function channelRules(): array
    {
        return ['string', Rule::in(array_keys($this->channelManager->getChannels()))];
    }

    /**
     * @param  string|string[]|null  $channelId
     * @return array<string, array<string, mixed>>
     */
    private function getArticleInfo(Entry $entry, array|string|null $channelId, bool $refresh = false): array
    {
        $infos = $this->articleManager->getArticleInfo($entry, $channelId, $refresh);

        // Add canPublish keys
        $channels = $this->channelManager->getChannels();

        foreach ($infos as $id => $channelInfo) {
            $infos[$id]['canPublish'] = isset($channels[$id]) && $channels[$id]->canPublish($entry);
        }

        return $infos;
    }
}
