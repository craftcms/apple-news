<?php

/**
 * @file
 * Example: GET Sections
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

// Fetches a list of all sections for a channel.
$response = $PublisherAPI->Get('/channels/{channel_id}/sections',
  [
    'channel_id' =>'[CHANNEL_ID]'
  ]
);
