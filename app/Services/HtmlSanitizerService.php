<?php

namespace App\Services;

class HtmlSanitizerService
{
    /**
     * Allowed HTML tags for rich text content (property descriptions).
     */
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'em', 'u', 'h2', 'h3', 'ul', 'ol', 'li', 'a',
    ];

    /**
     * Allowed attributes for HTML tags.
     */
    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'title', 'target'],
    ];

    /**
     * Sanitize HTML content for property descriptions.
     * Allows only safe HTML tags and removes potentially dangerous content.
     *
     * @param string|null $html
     * @return string|null
     */
    public function sanitizeRichText(?string $html): ?string
    {
        if (empty($html)) {
            return $html;
        }

        // Remove script tags and their content
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);

        // Remove style tags and their content
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);

        // Remove iframe tags
        $html = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is', '', $html);

        // Remove object and embed tags
        $html = preg_replace('/<(object|embed)\b[^>]*>(.*?)<\/\1>/is', '', $html);

        // Remove event handlers (onclick, onload, etc.)
        $html = preg_replace('/\s*on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);

        // Remove javascript: protocol from links
        $html = preg_replace('/href\s*=\s*["\']javascript:[^"\']*["\']/i', 'href="#"', $html);

        // Strip tags except allowed ones
        $allowedTagsString = '<' . implode('><', self::ALLOWED_TAGS) . '>';
        $html = strip_tags($html, $allowedTagsString);

        // Additional sanitization for attributes
        $html = $this->sanitizeAttributes($html);

        return $html;
    }

    /**
     * Sanitize HTML attributes to remove dangerous ones.
     *
     * @param string $html
     * @return string
     */
    private function sanitizeAttributes(string $html): string
    {
        // This is a basic implementation. For production, consider using a library like HTML Purifier
        foreach (self::ALLOWED_ATTRIBUTES as $tag => $allowedAttrs) {
            // Find all instances of the tag
            $pattern = '/<' . $tag . '\s+([^>]+)>/i';
            $html = preg_replace_callback($pattern, function ($matches) use ($tag, $allowedAttrs) {
                $attributes = $matches[1];
                $sanitizedAttrs = [];

                // Parse attributes
                preg_match_all('/(\w+)\s*=\s*["\']([^"\']*)["\']/', $attributes, $attrMatches, PREG_SET_ORDER);

                foreach ($attrMatches as $attrMatch) {
                    $attrName = strtolower($attrMatch[1]);
                    $attrValue = $attrMatch[2];

                    // Only keep allowed attributes
                    if (in_array($attrName, $allowedAttrs)) {
                        // Additional sanitization for href
                        if ($attrName === 'href') {
                            // Only allow http, https, and relative URLs
                            if (preg_match('/^(https?:\/\/|\/)/i', $attrValue)) {
                                $sanitizedAttrs[] = $attrName . '="' . htmlspecialchars($attrValue, ENT_QUOTES, 'UTF-8') . '"';
                            }
                        } else {
                            $sanitizedAttrs[] = $attrName . '="' . htmlspecialchars($attrValue, ENT_QUOTES, 'UTF-8') . '"';
                        }
                    }
                }

                return '<' . $tag . ($sanitizedAttrs ? ' ' . implode(' ', $sanitizedAttrs) : '') . '>';
            }, $html);
        }

        return $html;
    }

    /**
     * Strip all HTML tags from content.
     *
     * @param string|null $content
     * @return string|null
     */
    public function stripAllTags(?string $content): ?string
    {
        if (empty($content)) {
            return $content;
        }

        // Remove script tags and their content first
        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $content);
        
        // Remove style tags and their content
        $content = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $content);

        return strip_tags($content);
    }

    /**
     * Sanitize plain text input (removes HTML and trims).
     *
     * @param string|null $text
     * @return string|null
     */
    public function sanitizePlainText(?string $text): ?string
    {
        if (empty($text)) {
            return $text;
        }

        return trim(strip_tags($text));
    }
}
