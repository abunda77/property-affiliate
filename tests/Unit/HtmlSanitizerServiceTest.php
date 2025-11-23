<?php

namespace Tests\Unit;

use App\Services\HtmlSanitizerService;
use Tests\TestCase;

class HtmlSanitizerServiceTest extends TestCase
{
    private HtmlSanitizerService $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sanitizer = new HtmlSanitizerService();
    }

    /** @test */
    public function it_removes_script_tags()
    {
        $html = '<p>Safe content</p><script>alert("xss")</script><p>More content</p>';
        $result = $this->sanitizer->sanitizeRichText($html);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringNotContainsString('alert', $result);
        $this->assertStringContainsString('<p>Safe content</p>', $result);
    }

    /** @test */
    public function it_removes_style_tags()
    {
        $html = '<p>Content</p><style>body { display: none; }</style>';
        $result = $this->sanitizer->sanitizeRichText($html);

        $this->assertStringNotContainsString('<style>', $result);
        $this->assertStringNotContainsString('display: none', $result);
    }

    /** @test */
    public function it_removes_iframe_tags()
    {
        $html = '<p>Content</p><iframe src="http://evil.com"></iframe>';
        $result = $this->sanitizer->sanitizeRichText($html);

        $this->assertStringNotContainsString('<iframe>', $result);
        $this->assertStringNotContainsString('evil.com', $result);
    }

    /** @test */
    public function it_removes_event_handlers()
    {
        $html = '<p onclick="alert(\'xss\')">Click me</p>';
        $result = $this->sanitizer->sanitizeRichText($html);

        $this->assertStringNotContainsString('onclick', $result);
        $this->assertStringNotContainsString('alert', $result);
    }

    /** @test */
    public function it_removes_javascript_protocol()
    {
        $html = '<a href="javascript:alert(\'xss\')">Link</a>';
        $result = $this->sanitizer->sanitizeRichText($html);

        $this->assertStringNotContainsString('javascript:', $result);
    }

    /** @test */
    public function it_allows_safe_html_tags()
    {
        $html = '<p>Paragraph</p><strong>Bold</strong><em>Italic</em><ul><li>Item</li></ul>';
        $result = $this->sanitizer->sanitizeRichText($html);

        $this->assertStringContainsString('<p>Paragraph</p>', $result);
        $this->assertStringContainsString('<strong>Bold</strong>', $result);
        $this->assertStringContainsString('<em>Italic</em>', $result);
        $this->assertStringContainsString('<ul>', $result);
        $this->assertStringContainsString('<li>Item</li>', $result);
    }

    /** @test */
    public function it_removes_disallowed_tags()
    {
        $html = '<p>Safe</p><div>Div content</div><span>Span content</span>';
        $result = $this->sanitizer->sanitizeRichText($html);

        $this->assertStringContainsString('<p>Safe</p>', $result);
        $this->assertStringNotContainsString('<div>', $result);
        $this->assertStringNotContainsString('<span>', $result);
        // Content should still be there, just without tags
        $this->assertStringContainsString('Div content', $result);
        $this->assertStringContainsString('Span content', $result);
    }

    /** @test */
    public function it_allows_safe_link_attributes()
    {
        $html = '<a href="https://example.com" title="Example">Link</a>';
        $result = $this->sanitizer->sanitizeRichText($html);

        $this->assertStringContainsString('href=', $result);
        $this->assertStringContainsString('example.com', $result);
    }

    /** @test */
    public function it_strips_all_tags_from_plain_text()
    {
        $text = '<p>Hello</p><script>alert("xss")</script><b>World</b>';
        $result = $this->sanitizer->stripAllTags($text);

        $this->assertEquals('HelloWorld', $result);
        $this->assertStringNotContainsString('<', $result);
        $this->assertStringNotContainsString('>', $result);
    }

    /** @test */
    public function it_sanitizes_plain_text_and_trims()
    {
        $text = '  <b>Hello</b> World  ';
        $result = $this->sanitizer->sanitizePlainText($text);

        $this->assertEquals('Hello World', $result);
        $this->assertStringNotContainsString('<b>', $result);
    }

    /** @test */
    public function it_handles_null_values()
    {
        $this->assertNull($this->sanitizer->sanitizeRichText(null));
        $this->assertNull($this->sanitizer->stripAllTags(null));
        $this->assertNull($this->sanitizer->sanitizePlainText(null));
    }

    /** @test */
    public function it_handles_empty_strings()
    {
        $this->assertEquals('', $this->sanitizer->sanitizeRichText(''));
        $this->assertEquals('', $this->sanitizer->stripAllTags(''));
        $this->assertEquals('', $this->sanitizer->sanitizePlainText(''));
    }
}
