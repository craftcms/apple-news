<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Styles\Fills\VideoFill.
 */
use ChapterThree\AppleNewsAPI\Document\Base;
use ChapterThree\AppleNewsAPI\Document\Styles\Fills;
use ChapterThree\AppleNewsAPI\Document\Styles\Fills\VideoFill;

/**
 * Tests for the Fill class.
 */
class VideoFillTest extends PHPUnit_Framework_TestCase {

  /**
   * Setting properties and outputting json.
   */
  public function testSetters() {

    $obj = new VideoFill('https://live-streaming.apple.com/hls/2014/fded0-1077dae/main.m3u8', 'bundle://video-still.jpg');

    $json = '{"URL":"https://live-streaming.apple.com/hls/2014/fded0-1077dae/main.m3u8","type":"video","stillURL":"bundle://video-still.jpg"}';

    $this->assertJsonStringEqualsJsonString($json, $obj->json());

    // Optional properties.
    $json = '{"URL":"https://live-streaming.apple.com/hls/2014/fded0-1077dae/main.m3u8","type":"video","stillURL":"bundle://video-still.jpg","fillMode":"fit","verticalAlignment":"top","horizontalAlignment":"center","attachment":"fixed"}';
    $obj->setAttachment('fixed')
      ->setFillMode('fit')
      ->setVerticalAlignment('top')
      ->setHorizontalAlignment('center');
    $this->assertJsonStringEqualsJsonString($json, $obj->json());

  }

}
