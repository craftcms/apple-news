<?php

/**
 * @file
 * Example: GET Channel
 */

require '../../src/PublisherAPI.php';

use \ChapterThree\AppleNewsAPI;

$api_key_id = "";
$api_key_secret = "";
$endpoint = "https://endpoint_url";

$PublisherAPI = new PublisherAPI(
  $api_key_id,
  $api_key_secret,
  $endpoint
);

// Fetches information about a channel.
$response = $PublisherAPI->Get('/channels/{channel_id}',
  [
    'channel_id' => '[CHANNEL_ID]'
  ]
);
