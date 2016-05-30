# AppleNewsAPI

`AppleNewsAPI\PublisherAPI` is a PHP library that allows you to publish content to Apple News. You can also retrieve and delete articles you’ve already published, and get basic information about your channel and sections.

`AppleNewsAPI\Document` is a PHP library that helps construct documents in the Apple News native JSON format.

## Installation

To install, simply:

@todo composer instructions

```shell
git clone git@github.com:chapter-three/AppleNewsAPI.git
cd AppleNewsAPI
curl -sS https://getcomposer.org/installer | php
./composer.phar install
```

### Unit Tests

```shell
./vendor/bin/phpunit -v --colors=auto --bootstrap vendor/autoload.php tests
```

To test PublisherAPI GET/POST/DELETE methods use the following pattern:

```shell
./vendor/bin/phpunit -v --colors=auto --bootstrap vendor/autoload.php 
tests/PublisherAPITest.php [API_KEY] [API_SECRET] [ENDPOINT_URL] [METHOD] [ENDPOINT_PATH]
```

## PublisherAPI class Quick Start and Examples

```php
$api_key_id = "";
$api_key_secret = "";
$endpoint = "https://endpoint_url";

$PublisherAPI = new ChapterThree\AppleNewsAPI\PublisherAPI(
  $api_key_id,
  $api_key_secret,
  $endpoint
);
```

##### GET Channel

```php
// Fetches information about a channel.
$response = $PublisherAPI->get('/channels/{channel_id}',
  [
    'channel_id' => CHANNEL_ID
  ]
);
```

##### GET Sections

```php
// Fetches a list of all sections for a channel.
$response = $PublisherAPI->get('/channels/{channel_id}/sections',
  [
    'channel_id' => CHANNEL_ID
  ]
);
```

##### GET Section

```php
// Fetches information about a single section.
$response = $PublisherAPI->get('/sections/{section_id}',
  [
    'section_id' => SECTION_ID
  ]
);
```

##### GET Article

```php
// Fetches an article.
$response = $PublisherAPI->get('/articles/{article_id}',
  [
    'article_id' => ARTICLE_ID
  ]
);
```

##### POST Article

```php
// Publishes a new article to a channel.
// $response contains an article ID and revision ID.
$response = $PublisherAPI->post('/channels/{channel_id}/articles',
  [
    'channel_id' => CHANNEL_ID
  ],
  [
    // List of files to POST
    'files' => [], // optional. A list of article assets [uri => path]
    // JSON metadata string
    'metadata' => $metadata, // required
    'json' => '', // required. Apple News Native formatted JSON string.
  ]
);
```

##### UPDATE Article

```php
// Metadata information `revision` is required.
$metadata = json_encode([
  'data' => [
    'revision' => REVISION_ID
  ]
]);
// Updates an existing article.
// See $response variable to get a new revision ID.
$response = $PublisherAPI->post('/articles/{article_id}',
  [
    'article_id' => ARTICLE_ID
  ],
  [
    // List of files to POST
    'files' => [], // optional. A list of article assets [uri => path]
    // JSON metadata string
    'metadata' => $metadata, // required
    // Apple News Native formatted JSON string. See examples.
    'json' => '', // required.
  ]
);
```

##### DELETE Article

```php
// Deletes an article.
$response = $PublisherAPI->delete('/articles/{article_id}',
  [
    'article_id' => ARTICLE_ID
  ]
);
```

## Document class Quick Start and Examples

@todo
