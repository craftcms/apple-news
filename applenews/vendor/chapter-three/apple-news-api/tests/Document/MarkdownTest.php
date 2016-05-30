<?php

/**
 * @file
 * Tests for ChapterThree\AppleNewsAPI\Document\Markdown.
 */

use ChapterThree\AppleNewsAPI\Document\Markdown;

/**
 * Tests for the Markdown class.
 */
class MarkdownTest extends PHPUnit_Framework_TestCase {

  /**
   * Paragraphs.
   */
  public function testParagraphs() {
    $html = <<<'EOD'
<p>some paragraph content</p>
<p>some paragraph content with <i>italic</i> and <em>emphasized</em> text.</p>
<p>some paragraph content with <b>bold</b> and <strong>strong</strong> text.</p>
EOD;
    $expected = <<<'EOD'
some paragraph content

some paragraph content with *italic* and *emphasized* text.

some paragraph content with **bold** and **strong** text.
EOD;
    $markdown = new Markdown();
    $markdown = $markdown->convert($html);
    $this->assertEquals(trim($expected), trim($markdown),
      'Convert HTML with paragraphs and inline elements to Markdown.');
  }

  /**
   * Headers.
   */
  public function testHeaders() {
    $html = <<<'EOD'
<h1>header 1</h1>
<p>some paragraph content</p>
<h2>header 2</h2>
<h3>header 3</h3>
<p>some paragraph content</p>
<h4>header 4</h4>
<h5>header 5</h5>
<h6>header 6</h6>
<p>some paragraph content</p>
EOD;
    $expected = <<<'EOD'
# header 1

some paragraph content

## header 2

### header 3

some paragraph content

#### header 4

##### header 5

###### header 6

some paragraph content
EOD;
    $markdown = new Markdown();
    $markdown = $markdown->convert($html);
    $this->assertEquals(trim($expected), trim($markdown),
      'Convert HTML with headers to Markdown.');
  }

  /**
   * Headers.
   */
  public function testLinks() {
    $html = <<<'EOD'
<p>some paragraph content with <a href="http://apple.com">links</a>.</p>
<h2>header 2 with <a href="http://apple.com">link</a></h2>
<p>some paragraph content with <em><a href="http://apple.com">emphasized links</a></em> or <a href="http://apple.com"><em>emphasized</em> links</a> text.</p>
<p>some paragraph content with <strong><a href="http://apple.com">strong links</a></strong> or <a href="http://apple.com"><strong>strong</strong> links</a> text.</p>
EOD;
    $expected = <<<'EOD'
some paragraph content with [links](http://apple.com).

## header 2 with [link](http://apple.com)

some paragraph content with *[emphasized links](http://apple.com)* or [*emphasized* links](http://apple.com) text.

some paragraph content with **[strong links](http://apple.com)** or [**strong** links](http://apple.com) text.
EOD;
    $markdown = new Markdown();
    $markdown = $markdown->convert($html);
    $this->assertEquals(trim($expected), trim($markdown),
      'Convert HTML with link elements to Markdown.');
  }

  /**
   * Horizontal rules.
   */
  public function testHorizontalRules() {
    $html = <<<'EOD'
<p>some paragraph content.</p><hr/>
<p>some paragraph content.</p>
<hr/>
<p>some paragraph content.</p>
<hr/><p>some paragraph content.</p>
<p>some paragraph content.</p>
<p>some paragraph <hr/>content.
<p>some paragraph content.</p>
EOD;
    $expected = <<<'EOD'
some paragraph content.

***

some paragraph content.

***

some paragraph content.

***

some paragraph content.

some paragraph content.

some paragraph

***

content.

some paragraph content.
EOD;
    $markdown = new Markdown();
    $markdown = $markdown->convert($html);
    $this->assertEquals(trim($expected), trim($markdown),
      'Convert HTML with hr elements to Markdown.');
  }

  /**
   * Horizontal rules.
   */
  public function testLists() {
    $html = <<<'EOD'
<p>a ul</p>
<ul>
<li>item 1</li>
<li><a href="http://apple.com">item</a> 2</li>
<li>item 3</li>
</ul>
<p>an ol</p>
<ol>
<li>item 1</li>
<li>item 2</li>
<li>item 3</li>
</ol>
<p>dl to ul</p>
<dl>
<dt>definition term 1</dt>
<dd>definition description 1</dd>
<dt>definition term 2.1</dt>
<dt>definition term 2.2</dt>
<dd>definition description 2</dd>
</dl>
EOD;
    $expected = <<<'EOD'
a ul

 - item 1
 - [item](http://apple.com) 2
 - item 3

an ol

 1. item 1
 1. item 2
 1. item 3

dl to ul

 - definition term 1<br/>definition description 1
 - definition term 2.1<br/>definition term 2.2<br/>definition description 2
EOD;
    $markdown = new Markdown();
    $markdown = $markdown->convert($html);
    $this->assertEquals(trim($expected), trim($markdown),
      'Convert HTML with list elements to Markdown.');
  }

  /**
   * Ignored tags.
   */
  public function testIgnored() {
    $html = <<<'EOD'
<p>some paragraph content</p>
<p>some paragraph content with <span>ignored</span> inline tags.</p>
<p>some paragraph content with <span>ignored inline tags.</span></p>
<p><span>some paragraph content with ignored</span> inline tags.</p>
<p><span>some paragraph content with ignored inline tags.</span></p>
<noscript>some content inside an ignored block tag.</noscript>
<p>some paragraph content</p>
EOD;
    $expected = <<<'EOD'
some paragraph content

some paragraph content with ignored inline tags.

some paragraph content with ignored inline tags.

some paragraph content with ignored inline tags.

some paragraph content with ignored inline tags.

some paragraph content
EOD;
    $markdown = new Markdown();
    $markdown = $markdown->convert($html);
    $this->assertEquals(trim($expected), trim($markdown),
      'Convert HTML with ignored elements to Markdown.');
  }

  /**
   * Ignored tags.
   */
  public function testBlock() {
    $html = <<<'EOD'
<p>some paragraph content</p>
<div>
  <p>some paragraph content</p>
  <p>some paragraph content</p>
  <div>
    <p>some paragraph content</p>
    <p>some paragraph content</p>
  </div>
  <p>
    <div><p>some paragraph content</p></div>
    <div>some paragraph content</div>
  <p>
</div>
<p>
  <div><p>some paragraph content</p></div>
  <div>some paragraph content</div>
<p>
EOD;
    $expected = <<<'EOD'
some paragraph content

some paragraph content

some paragraph content

some paragraph content

some paragraph content

some paragraph content

some paragraph content

some paragraph content

some paragraph content
EOD;
    $markdown = new Markdown();
    $markdown = $markdown->convert($html);
    $this->assertEquals(trim($expected), trim($markdown),
      'Convert HTML with block elements to Markdown.');
  }

  /**
   * Ignored tags.
   */
  public function testInline() {
    $html = <<<'EOD'
<p>some paragraph content</p>
<p>some paragraph content with <em>emphasized <strong>and strong</strong></em> text.</p>
<p>some paragraph content with <span><em>nested emphasized</em></span> text.</p>
<p>some paragraph content with <em><span>nested emphasized</span></em> text.</p>
EOD;
    $expected = <<<'EOD'
some paragraph content

some paragraph content with *emphasized **and strong*** text.

some paragraph content with *nested emphasized* text.

some paragraph content with *nested emphasized* text.
EOD;
    $markdown = new Markdown();
    $markdown = $markdown->convert($html);
    $this->assertEquals(trim($expected), trim($markdown),
      'Convert HTML with block elements to Markdown.');
  }

  /**
   * Ignored tags.
   */
  public function testSpecialChars() {
    $html = <<<'EOD'
<p>some paragraph content with special characters \ ` * _ {} [] () # + - !</p>
<p>here is an exclamation point!<a href="http://apple.com">followed by a link</a> (not an image)</p>
EOD;
    $expected = <<<'EOD'
some paragraph content with special characters \\ \` \* \_ \{\} \[\] \(\) \# \+ \- \!

here is an exclamation point\![followed by a link](http://apple.com) \(not an image\)
EOD;
    $markdown = new Markdown();
    $markdown = $markdown->convert($html);
    $this->assertEquals(trim($expected), trim($markdown),
      'Convert HTML with special characters to Markdown.');
  }

  /**
   * HTML Entities.
   */
  public function testHtmlEntities() {
    $html = <<<'EOD'
<p>&lt;img src="<a href="http://example.com/img.png">img.png</a>" /&gt;</p>
&nbsp;
<p>&nbsp;</p>
<p>x</p>
EOD;
    $expected = <<<'EOD'
<img src="[img.png](http://example.com/img.png)" />

x
EOD;
    $markdown = new Markdown();
    $markdown = $markdown->convert($html);
    $this->assertEquals(trim($expected), trim($markdown),
      'Convert HTML with entities to Markdown.');
  }

  /**
   * Whitelisted elements.
   */
  public function testWhitelist() {
    $html = <<<'EOD'
<p>some paragraph content with an <img src="http://example.com/image.png"> inline image.</p>
<p>some paragraph content with a <span>span.</span></p>
<span>a span.</span>
<p>some paragraph <span>content <strong>with</strong></span> a span.</p>
<p><span>some paragraph content with a span.</span></p>
EOD;
    $expected = <<<'EOD'
some paragraph content with an <img src="http://example.com/image.png"/> inline image.

some paragraph content with a <span>span.</span>

<span>a span.</span>

some paragraph <span>content **with**</span> a span.

<span>some paragraph content with a span.</span>
EOD;
    $markdown = new Markdown(['img', 'span']);
    $markdown = $markdown->convert($html);
    $this->assertEquals(trim($expected), trim($markdown),
      'Whitelist elements.');
  }

}
