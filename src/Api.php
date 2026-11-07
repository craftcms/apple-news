<?php

declare(strict_types=1);

namespace CraftCms\AppleNews;

use CraftCms\Cms\Asset\Elements\Asset;
use Illuminate\Container\Attributes\Singleton;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\RequestInterface;
use stdClass;
use UnexpectedValueException;

/**
 * Sends requests to the Apple News API.
 *
 * Every method throws {@see RequestException} when Apple News returns an error. Its response holds Apple’s error
 * codes.
 *
 * @see https://developer.apple.com/documentation/applenewsapi
 * @since 2.0
 */
#[Singleton]
class Api
{
    public const string BASE_URL = 'https://news-api.apple.com';

    /**
     * The file types Apple News accepts in an article bundle, besides JSON.
     */
    private const array FILE_MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'application/octet-stream'];

    public function __construct(
        private readonly Channels $channels,
        private readonly Settings $settings,
    ) {}

    /**
     * Returns information about a channel.
     */
    public function channel(string $channelId): stdClass
    {
        return $this->get($channelId, "/channels/$channelId");
    }

    /**
     * Returns information about a channel’s sections.
     */
    public function sections(string $channelId): stdClass
    {
        return $this->get($channelId, "/channels/$channelId/sections");
    }

    /**
     * Returns information about a section.
     */
    public function section(string $channelId, string $sectionId): stdClass
    {
        return $this->get($channelId, "/sections/$sectionId");
    }

    /**
     * Returns information about an article.
     */
    public function article(string $channelId, string $articleId): stdClass
    {
        return $this->get($channelId, "/articles/$articleId");
    }

    /**
     * Searches for articles in a channel.
     *
     * @param  array<string, mixed>  $params  Query parameters
     */
    public function search(string $channelId, array $params = []): stdClass
    {
        return $this->get($channelId, "/channels/$channelId/articles", $params);
    }

    /**
     * Creates a new article.
     *
     * @param  array{json: string, metadata?: string|null, files?: array<string, string|Asset>}  $data
     */
    public function createArticle(string $channelId, array $data): stdClass
    {
        return $this->post($channelId, "/channels/$channelId/articles", $data);
    }

    /**
     * Updates an article.
     *
     * @param  array{json: string, metadata?: string|null, files?: array<string, string|Asset>}  $data
     */
    public function updateArticle(string $channelId, string $articleId, array $data): stdClass
    {
        return $this->post($channelId, "/articles/$articleId", $data);
    }

    /**
     * Deletes an article.
     */
    public function deleteArticle(string $channelId, string $articleId): stdClass
    {
        return $this->decode($this->request($channelId)->delete("/articles/$articleId"));
    }

    /**
     * Sends a GET request.
     *
     * @param  array<string, mixed>  $query
     */
    protected function get(string $channelId, string $path, array $query = []): stdClass
    {
        return $this->decode($this->request($channelId)->get($path, $query));
    }

    /**
     * Sends an article as a multipart POST request: its Apple News Format JSON, its metadata, and its files.
     *
     * @param  array{json: string, metadata?: string|null, files?: array<string, string|Asset>}  $data
     */
    protected function post(string $channelId, string $path, array $data): stdClass
    {
        $request = $this->request($channelId)->asMultipart();

        $this->attach($request, 'article', $data['json'], 'application/json', 'article.json');

        if (! empty($data['metadata'])) {
            $this->attach($request, 'metadata', $data['metadata'], 'application/json');
        }

        foreach ($data['files'] ?? [] as $uri => $file) {
            $filePath = $file instanceof Asset ? $file->getCopyOfFile() : $file;
            $this->attach($request, pathinfo($uri, PATHINFO_FILENAME), file_get_contents($filePath), $this->fileMimeType($filePath), basename($uri));
        }

        return $this->decode($request->post($path));
    }

    /**
     * Returns a request for a channel, signed with its API key.
     */
    protected function request(string $channelId): PendingRequest
    {
        $channel = $this->channels->get($channelId);

        return Http::baseUrl(self::BASE_URL)
            ->acceptJson()
            ->timeout($this->settings->httpClientTimeout)
            ->withRequestMiddleware(fn (RequestInterface $request) => $this->sign($request, $channel))
            ->throw();
    }

    /**
     * Adds an `Authorization` header with the request’s HHMAC signature.
     *
     * The signature covers the method, full URL and date, plus the content type and body of POST requests.
     *
     * @see https://developer.apple.com/documentation/applenews/about-the-apple-news-security-model
     */
    protected function sign(RequestInterface $request, ChannelInterface $channel): RequestInterface
    {
        $date = gmdate('Y-m-d\TH:i:s\Z');
        $canonical = $request->getMethod().$request->getUri().$date;

        if ($request->getMethod() === 'POST') {
            $canonical .= $request->getHeaderLine('Content-Type').$request->getBody();
            $request->getBody()->rewind();
        }

        $signature = base64_encode(hash_hmac('sha256', $canonical, base64_decode($channel->getApiSecret()), true));

        return $request->withHeader(
            'Authorization',
            sprintf('HHMAC; key=%s; signature=%s; date=%s', $channel->getApiKeyId(), $signature, $date),
        );
    }

    private function attach(PendingRequest $request, string $name, string $contents, string $mimeType, ?string $filename = null): void
    {
        $disposition = "form-data; name=$name".($filename !== null ? "; filename=$filename" : '').'; size='.strlen($contents);

        $request->attach($name, $contents, $filename, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => $disposition,
        ]);
    }

    private function fileMimeType(string $path): string
    {
        if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) === 'json') {
            return 'application/json';
        }

        $mimeType = mime_content_type($path) ?: 'application/octet-stream';

        if ($mimeType === 'text/plain') {
            return 'application/octet-stream';
        }

        if (! in_array($mimeType, self::FILE_MIME_TYPES, true)) {
            throw new UnexpectedValueException("Apple News doesn’t accept $mimeType files ($path).");
        }

        return $mimeType;
    }

    private function decode(Response $response): stdClass
    {
        return $response->object() ?? new stdClass;
    }
}
