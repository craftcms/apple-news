<?php

/**
 * @file
 * Example: GET Section
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

// Fetches information about a single section.
$response = $PublisherAPI->Get('/sections/{section_id}',
  [
    'section_id' => '[SECTION_ID]'
  ]
);
