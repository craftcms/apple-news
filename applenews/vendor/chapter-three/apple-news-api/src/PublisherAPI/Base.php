<?php

/**
 * @file
 * PublisherAPI Base abstract class.
 */

namespace ChapterThree\AppleNewsAPI\PublisherAPI;

/**
 * PublisherAPI Abstract class
 * 
 * @package    ChapterThree\AppleNewsAPI\PublisherAPI\Base
 */
abstract class Base {

  /** @var (string) PublisherAPI API Key ID. */
  public $api_key_id = '';

  /** @var (string) PublisherAPI Secret Key. */
  public $api_key_secret = '';

  /** @var (string) PublisherAPI Endpoint base URL. */
  public $endpoint = '';

  /** @var (object) HTTP client class. */
  public $client;

  /** @var (string) Endpoint path. */
  public $path = '';

  /** @var (string) HTTP Method (GET/DELETE/POST). */
  public $method = '';

  /** @var (array) Endpoint path variables to replace. */
  public $path_args = [];

  /** @var (datetime) ISO 8601 datetime. */
  public $datetime;

  /** @var (array) Valid values for resource part Content-Type. */
  protected $valid_mimes = [
    'image/jpeg',
    'image/png',
    'image/gif',
    'application/octet-stream'
  ];

  /**
   * Initialize variables needed in the communication with the API.
   *
   * @param (string) $key API Key.
   * @param (string) $secret API Secret Key.
   * @param (string) $endpoint API endpoint URL.
   */
  public function __construct($key, $secret, $endpoint) {
    // Set API required variables.
    $this->api_key_id = $key;
    $this->api_key_secret = $secret;
    $this->endpoint = $endpoint;
    // ISO 8601 date and time format.
    $this->datetime = gmdate(\DateTime::ISO8601);
    // Initialize HTTP client.
    $this->setHTTPClient();
  }

  /**
   * Generate HMAC cryptographic hash.
   *
   * @param (string) $string Message to be hashed.
   * @param (string) $api_key_secret Shared secret key used for generating the HMAC.
   *
   * @return (string) Authorization token used in the HTTP headers.
   */
  final protected function hhmac($string, $api_key_secret) {
    $key = base64_decode($api_key_secret);
    $hashed = hash_hmac('sha256', $string, $key, true);
    $encoded = base64_encode($hashed);
    $signature = rtrim($encoded, "\n");
    return strval($signature);
  }

  /**
   * Create canonical version of the request as a byte-wise concatenation.
   *
   * @param (string) $string String to concatenate (see POST method).
   *
   * @return (string) HMAC cryptographic hash
   */
  final protected function auth($string = '') {
    $canonical = strtoupper($this->method) . $this->path() . strval($this->datetime) . $string;
    $signature = $this->hhmac($canonical, $this->api_key_secret);
    return sprintf('HHMAC; key=%s; signature=%s; date=%s',
      $this->api_key_id, $signature,
      $this->datetime
    );
  }

  /**
   * Setup HTTP client to make requests.
   */
  public function setHTTPClient() {
    // Example: $this->client = new \Curl\Curl;
    $this->triggerError('No HTTP Client found', E_USER_ERROR);
  }

  /**
   * Generate HTTP request URL.
   *
   * @return (string) URL to create request.
   */
  protected function path() {
    $params = [];
    // Take arguments and pass them to the path by replacing {argument} tokens.
    foreach ($this->path_args as $argument => $value) {
      $params["{{$argument}}"] = $value;
    }
    $path = str_replace(array_keys($params), array_values($params), $this->path);
    return $this->endpoint . $path;
  }

  /**
   * Initialize variables needed to make a request.
   *
   * @param (string) $method Request method (POST/GET/DELETE).
   * @param (string) $path Path to API endpoint.
   * @param (array) $path_args Endpoint path arguments to replace tokens in the path.
   * @param (array) $data Data to pass to the endpoint.
   *
   * @see PublisherAPI::post().
   */
  protected function initVars($method, $path, Array $path_args, Array $data) {
    $this->method = $method;
    $this->path = $path;
    $this->path_args = $path_args;
  }

  /**
   * Set HTTP headers.
   *
   * @param (array) $headers Associative array [header field name => value].
   */
  abstract protected function setHeaders(Array $headers = []);

  /**
   * Remove specified header names from HTTP request.
   *
   * @param (array) $headers Associative array [header1, header2, ..., headerN].
   */
  abstract protected function unsetHeaders(Array $headers = []);

  /**
   * Create HTTP request.
   *
   * @param (array|string) $data Raw content of the request or associative array to pass to endpoints.
   *
   * @return (object) HTTP Response object.
   */
  abstract protected function request($data);

  /**
   * Preprocess HTTP response.
   *
   * @param (object) $response Structured object.
   *
   * @return (object) HTTP Response object.
   */
  protected function response($response) {
    // Process responsed data.
  }

  /**
   * Callback for successful HTTP response.
   *
   * @param (object) $response HTTP Response object.
   */
  protected function onSuccessfulResponse($response) {
    // Perform some operations on success response.
  }

  /**
   * Callback for error HTTP response.
   *
   * @param (int) $error_code HTTP status code.
   * @param (string) $error_message HTTP status message.
   * @param (object) $response Structured object.
   */
  protected function onErrorResponse($error_code, $error_message, $response) {
    $message = print_r(
      [
        'code'      => $error_code,
        'message'   => $error_message,
        'response'  => $response
      ],
      true
    );
    $this->triggerError($message);
  }

  /**
   * Create GET request to a specified endpoint.
   *
   * @param (string) $path Path to API endpoint.
   * @param (array) $path_args Endpoint path arguments to replace tokens in the path.
   * @param (array) $data Raw content of the request or associative array to pass to endpoints.
   *
   * @return object Preprocessed structured object.
   */
  public function get($path, Array $path_args, Array $data) {
    $this->initVars(__FUNCTION__, $path, $path_args, $data);
  }

  /**
   * Create POST request to a specified endpoint.
   *
   * @param (string) $path Path to API endpoint.
   * @param (array) $path_args Endpoint path arguments to replace tokens in the path.
   * @param (array) $data Raw content of the request or associative array to pass to endpoints.
   *
   * @return object Preprocessed structured object.
   */
  public function post($path, Array $path_args, Array $data) {
    $this->initVars(__FUNCTION__, $path, $path_args, $data);
  }

  /**
   * Create DELETE request to a specified endpoint.
   *
   * @param (string) $path Path to API endpoint.
   * @param (array) $path_args Endpoint path arguments to replace tokens in the path.
   * @param (array) $data Raw content of the request or associative array to pass to endpoints.
   *
   * @return object Preprocessed structured object and returns 204 No Content on success, with no response body.
   */
  public function delete($path, Array $path_args, Array $data) {
    $this->initVars(__FUNCTION__, $path, $path_args, $data);
  }

  /**
   * Implements __get().
   *
   * @param (mixed) $name Property name.
   */
  public function __get($name) {
    return $this->$name;
  }

  /**
   * Implements __set().
   *
   * @param (mixed) $name Property name.
   * @param (mixed) $value Property value. 
   */
  public function __set($name, $value) {
    $this->triggerError('Undefined property via __set(): ' . $name);
    return NULL;
  }

  /**
   * Implements __isset().
   *
   * @param (mixed) $name Property name.
   */
  public function __isset($name) {
    return isset($this->$name);
  }

  /**
   * Implements __unset().
   *
   * @param (mixed) $name Property name.
   */
  public function __unset($name) {
    unset($this->$name);
  }

  /**
   * Error handler.
   *
   * @param (string) $message Error message to display.
   * @param (const) $message_type Predefined Constants
   *
   * @see http://php.net/manual/en/errorfunc.constants.php
   */
  public function triggerError($message, $message_type = E_USER_NOTICE) {
    $debug_backtrace = debug_backtrace();
    $trace = $debug_backtrace[0];
    trigger_error($message . ' in ' . $trace['file'] . ' on line ' .
      $trace['line'], $message_type);
  }

}
