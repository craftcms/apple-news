<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI.
 */

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\content\LargeFileContent;

/**
 * A test class for PushAPI.
 */
class PublisherAPITest extends \PHPUnit_Framework_TestCase {

  /** @var (const) CRLF */
  const EOL = "\r\n";

  /** @var (string) API Key ID */
  private static $api_key = '';

  /** @var (string) API Key Secret */
  private static $api_key_secret = '';

  /** @var (string) API Endpoint full URL */
  private static $endpoint = '';

  /** @var (string) Endpoint method to test */
  private static $endpoint_method = '';

  /** @var (string) Endpoint path to test */
  private static $endpoint_path = '';

  /** @var (object) PushAPI class object */
  private $PushAPI;

  /** @var (const) Contents of the test GIF file */
  const BASE64_1X1_GIF = 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

  /** @var (string) File path generated via vfsStream. */
  private $fileroot;

  /** @var (array) Array of files to upload via multipart data. */
  private $files = [];

  /**
   * Check PushAPI credentials.
   */
  private function checkPushAPICredentials() {
    if (empty(static::$api_key) && empty(static::$api_key_secret) && empty(static::$endpoint)) {
      return false;
    }
    else {
      return true;
    }
  }

  /**
   * Run before all tests.
   */
  public static function setUpBeforeClass() {
    global $argv, $argc;

    // Get variables from the unit test command string.
    static::$api_key = isset($argv[6]) ? $argv[6] : '';
    static::$api_key_secret = isset($argv[7]) ? $argv[7] : '';
    static::$endpoint = isset($argv[8]) ? $argv[8] : '';
    static::$endpoint_method = isset($argv[9]) ? strtolower($argv[9]) : '';
    static::$endpoint_path = isset($argv[10]) ? $argv[10] : '';

    fwrite(STDOUT, "\nPushAPI Unit Tests:\n\n");
    // Make sure user provides credentials to test PublisherAPI endpoints.
    if (empty(static::$api_key) && empty(static::$api_key_secret) && empty(static::$endpoint)) {
      fwrite(STDOUT, "Please specify PublisherAPI credentials. See documentation for more details about PublisherAPI unit tests.\n");
      fwrite(STDOUT, "When no credentials specified, only PublisherAPI helper methods will be tested.\n\n");
    }

  }

  /**
   *  Create objects against which we will test.
   */
  protected function setUp() {

    // Set up PublisherAPI object.
    $this->PublisherAPI = new \ChapterThree\AppleNewsAPI\PublisherAPI(
      self::$api_key,
      self::$api_key_secret,
      self::$endpoint
    );

    // Set up virtual file system.
    $this->fileroot = vfsStream::setup();

    // Generate file in vfs.
    $file = vfsStream::newFile('image.gif')
      ->withContent(base64_decode(static::BASE64_1X1_GIF))
      ->at($this->fileroot);

    // Add file path to files.
    $this->files[] = $file->url();

  }

  /**
   * Test PublisherAPI::get().
   *
   * Usage:
   *   ./vendor/bin/phpunit -v --colors=auto --bootstrap vendor/autoload.php tests/PushAPITest.php
   *     [API_KEY_ID] [API_SECRET_KEY] [ENDPOINT_URL] get /channels/{channel_id}
   */
  public function testGet() {

    if (static::$endpoint_method == 'get' && $this->checkPushAPICredentials()) {

      $response = $this->PublisherAPI->get(static::$endpoint_path);
      if (isset($response->errors)) {
        $this->assertTrue(false);
      }
      else {
        fwrite(STDOUT, "Successfully passed GET method test.\n");
        $this->assertTrue(true);
      }

    }

  }

  /**
   * Test PublisherAPI::delete().
   *
   * Usage:
   *   ./vendor/bin/phpunit -v --colors=auto --bootstrap vendor/autoload.php tests/PushAPITest.php
   *     [API_KEY_ID] [API_SECRET_KEY] [ENDPOINT_URL] delete /articles/{article_id}
   */
  public function testDelete() {

    if (static::$endpoint_method == 'delete' && $this->checkPushAPICredentials()) {

      $response = $this->PublisherAPI->delete(static::$endpoint_path);
      if (isset($response->errors)) {
        $this->assertTrue(false);
      }
      else {
        fwrite(STDOUT, "Successfully passed DELETE method test.\n");
        $this->assertTrue(true);
      }

    }

  }


  /**
   * Test PublisherAPI::post().
   *
   * Usage:
   *   ./vendor/bin/phpunit -v --colors=auto --bootstrap vendor/autoload.php tests/PushAPITest.php
   *     [API_KEY_ID] [API_SECRET_KEY] [ENDPOINT_URL] post /channels/{channel_id}/articles
   */
  public function testPost() {

    if (static::$endpoint_method == 'post' && $this->checkPushAPICredentials()) {

      // Add test article.json file.
      $this->files[] = __DIR__ . '/PublisherAPI/article.json';

      $response = $this->PublisherAPI->post(static::$endpoint_path, [],
        [
          'files' => $this->files,
          'json'  => '',
        ]
      );

      if (isset($response->errors)) {
        $this->assertTrue(false);
      }
      else {
        fwrite(STDOUT, "Successfully passed POST method test.\n");
        $this->assertTrue(true);
      }

    }

  }

  /**
   * Test PublisherAPI::getFileInformation().
   */
  public function testGetFileInformation() {

    fwrite(STDOUT, "Tested GetFileInformation().\n");

    $reflection = new \ReflectionClass('\ChapterThree\AppleNewsAPI\PublisherAPI');
    $method = $reflection->getMethod('getFileInformation');
    $method->setAccessible(true);

    // Process each file and generate multipart form data.
    foreach ($this->files as $path) {
      // Load file information.
      $file = $method->invokeArgs($this->PublisherAPI, [$path]);
      $expected =
  	    [
  	      'name'      => 'image',
  	      'filename'  => 'image.gif',
  	      'extension' => 'gif',
  	      'mimetype'  => 'image/gif',
  	      'contents'  => base64_decode(static::BASE64_1X1_GIF),
  	      'size'      => strlen(base64_decode(static::BASE64_1X1_GIF))
  	    ];
  	    // Check file information
      $this->assertEquals(0, count(array_diff($file, $expected)));
    }

  }

  /**
   * Test PublisherAPI::getFileInformation().
   * Test PublisherAPI::multipartPart().
   * Test PublisherAPI::multipartFinalize().
   */
  public function testMultipartPart() {

    fwrite(STDOUT, "Tested multipart generator methods.\n");

    $reflection = new \ReflectionClass('\ChapterThree\AppleNewsAPI\PublisherAPI\Curl');

    // Access protected method getFileInformation().
    $getFileInformation = $reflection->getMethod('getFileInformation');
    $getFileInformation->setAccessible(true);

    // Access protected method multipartPart().
    $multipartPart = $reflection->getMethod('multipartPart');
    $multipartPart->setAccessible(true);

    // Access protected method multipartFinalize().
    $multipartFinalize = $reflection->getMethod('multipartFinalize');
    $multipartFinalize->setAccessible(true);

    // Get private property.
    $getBoundary = $reflection->getProperty('boundary');
    $getBoundary->setAccessible(true);
    $boundary = $getBoundary->getValue($this->PublisherAPI);

    // Multiparts
    $multiparts = [];

    // Process each file and generate multipart form data.
    foreach ($this->files as $path) {
      // Load file information.
      $file = $getFileInformation->invokeArgs($this->PublisherAPI, [$path]);
      $multiparts[] = $multipartPart->invokeArgs(
      	$this->PublisherAPI,
      	[
          [
            'filename'   => $file['filename'],
            'name'       => $file['name'],
            'size'       => $file['size']
          ],
          $file['mimetype'],
          $file['contents']
        ]
      );
    }

    // Generate finalized version of the multipart data.
    $contents = $multipartFinalize->invokeArgs($this->PublisherAPI, [$multiparts]);
    // Get rid of first boundary.
    $multipart1 = '--' . $boundary . static::EOL .  preg_replace('/^.+\n/', '', $contents);

    // Load test file.
    $file = $getFileInformation->invokeArgs($this->PublisherAPI, [$this->files[0]]);

    // Expected multipart content.
    $multipart2 = '--' . $boundary . static::EOL;
    $multipart2 .= 'Content-Type: image/gif'. static::EOL;
    $multipart2 .= 'Content-Disposition: form-data; filename=image.gif; name=image; size=42' . static::EOL;
    $multipart2 .= static::EOL . $file['contents'] . static::EOL;
    $multipart2 .= '--' . $boundary . '--' . static::EOL;

    // Test Multipart data headers and content.
    $this->assertEquals($multipart1, $multipart2);

  }

}
