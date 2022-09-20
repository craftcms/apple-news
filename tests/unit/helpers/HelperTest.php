<?php
/**
 * @link https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license https://craftcms.github.io/license/
 */

namespace applenewstests\unit\helpers;

use Codeception\Test\Unit;
use Craft;
use craft\applenews\Helper;
use UnitTester;

/**
 * HelperTest
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since 3.0
 */
class HelperTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    /**
     * @param $expected
     * @param $language
     * @return void
     * @dataProvider languageDataProvider
     */
    public function testFormatLanguage($expected, $language): void
    {
        $this->tester->assertSame($expected, Helper::formatLanguage($language));
    }

    /**
     * @param $expected
     * @param $html
     * @return void
     * @dataProvider htmlDataProvider
     */
    public function testStripHtml($expected, $html): void
    {
        $this->tester->assertSame($expected, preg_replace('/^ +/m', '', Helper::stripHtml($html)));
    }

    /**
     * @return array
     */
    public function htmlDataProvider(): array
    {
        return [
            [
'


HTML Boilerplate



The Most Important Thing',
'<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>HTML Boilerplate</title>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <script src="index.js"></script>
    <h1>The Most Important Thing</h1>
  </body>
</html>']
        ];
    }

    /**
     * @return array
     */
    public function languageDataProvider(): array
    {
        return [
            ['en_US', 'en-us'],
            ['en_US', 'en-US'],
            ['en_US', 'en_us'],
            ['en_US', 'en_US'],
            ['en', 'en']
        ];
    }
}
