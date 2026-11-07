<?php

declare(strict_types=1);

namespace CraftCms\AppleNews\Http\Controllers;

use CraftCms\AppleNews\Articles;
use CraftCms\AppleNews\Channels;
use CraftCms\AppleNews\Http\Requests\ArticleInfoRequest;
use CraftCms\AppleNews\Http\Requests\DownloadArticleRequest;
use CraftCms\AppleNews\Http\Requests\PublishArticleRequest;
use CraftCms\Cms\Entry\Elements\Entry;
use CraftCms\Cms\Support\Facades\Path;
use CraftCms\Cms\Support\File;
use CraftCms\Cms\Support\Json;
use CraftCms\Cms\Support\Str;
use Illuminate\Http\JsonResponse;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

/** @since 2.0 */
class ArticleController
{
    public function __construct(
        private readonly Articles $articles,
        private readonly Channels $channels,
    ) {}

    /**
     * Downloads a bundle for Apple News Preview.
     */
    public function download(DownloadArticleRequest $request): BinaryFileResponse
    {
        $entry = $request->entry();
        $channel = $request->channel();

        abort_unless($channel->matchEntry($entry), 400, 'This channel does not want anything to do with this entry.');

        $article = $channel->createArticle($entry);

        // Prep the zip staging folder
        $zipDir = Path::temp('apple-news-articles');
        File::ensureDirectoryExists($zipDir);
        $zipPath = "$zipDir/$entry->slug-".Str::uuid()->toString().'.zip';

        // Create the zip
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            throw new RuntimeException('Cannot create zip at '.$zipPath);
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
    public function getInfo(ArticleInfoRequest $request): JsonResponse
    {
        return response()->json([
            'infos' => $this->articleInfo($request->entry(), $request->validated('channelId'), true),
        ]);
    }

    /**
     * Queues an entry to be published to Apple News.
     */
    public function publish(PublishArticleRequest $request): JsonResponse
    {
        $entry = $request->entry();
        $channelId = $request->validated('channelId');
        $this->articles->queue($entry, $channelId);

        return response()->json([
            'success' => true,
            'infos' => $this->articleInfo($entry, $channelId),
        ]);
    }

    /**
     * @param  string|string[]|null  $channelId
     * @return array<string, array<string, mixed>>
     */
    private function articleInfo(Entry $entry, array|string|null $channelId, bool $refresh = false): array
    {
        $infos = $this->articles->info($entry, $channelId, $refresh);

        // Add canPublish keys
        $channels = $this->channels->all();

        foreach ($infos as $id => $channelInfo) {
            $infos[$id]['canPublish'] = isset($channels[$id]) && $channels[$id]->canPublish($entry);
        }

        return $infos;
    }
}
