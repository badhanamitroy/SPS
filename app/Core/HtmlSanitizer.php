<?php

namespace App\Core;

/**
 * Strict allowlist-based HTML sanitizer for rich text content.
 * 
 * Permitted tags:
 * p, br, h2, h3, h4, strong, em, u, blockquote, ul, ol, li, a[href,title], img[src,alt],
 * figure, figcaption, code, pre, hr.
 * 
 * Disallowed / stripped:
 * script, style, iframe, all on* event handler attributes, and dangerous URI schemes (javascript:, data:, vbscript:).
 */
class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'br', 'h2', 'h3', 'h4', 'strong', 'em', 'u',
        'blockquote', 'ul', 'ol', 'li', 'a', 'img',
        'figure', 'figcaption', 'code', 'pre', 'hr'
    ];

    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'title'],
        'img' => ['src', 'alt'],
    ];

    /**
     * Clean untrusted HTML string against allowlist.
     */
    public static function clean(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        if (!str_contains($html, '<')) {
            return trim($html);
        }

        $prevInternalErrors = libxml_use_internal_errors(true);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        // Wrap with UTF-8 meta header and container to preserve Bengali/Unicode scripts accurately
        $wrapped = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body><div id="__sps_sanitizer_root__">' . $html . '</div></body></html>';
        
        $dom->loadHTML($wrapped, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        libxml_use_internal_errors($prevInternalErrors);

        $root = $dom->getElementById('__sps_sanitizer_root__');
        if (!$root) {
            return strip_tags($html);
        }

        self::sanitizeNode($root, $dom);

        $output = '';
        foreach ($root->childNodes as $child) {
            $output .= $dom->saveHTML($child);
        }

        return trim($output);
    }

    private static function sanitizeNode(\DOMNode $node, \DOMDocument $dom): void
    {
        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child instanceof \DOMElement) {
                $tagName = strtolower($child->tagName);

                // Blacklisted executable tags: discard tag and all children completely
                if (in_array($tagName, ['script', 'style', 'iframe', 'object', 'embed', 'applet', 'meta', 'link', 'svg'], true)) {
                    $node->removeChild($child);
                    continue;
                }

                // If element is not in allowlist: unwrap element (preserve safe text and children)
                if (!in_array($tagName, self::ALLOWED_TAGS, true)) {
                    self::sanitizeNode($child, $dom);
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                    continue;
                }

                // Element is allowed: sanitize its attributes
                $allowedAttrs = self::ALLOWED_ATTRIBUTES[$tagName] ?? [];
                $attributesToRemove = [];

                if ($child->hasAttributes()) {
                    foreach ($child->attributes as $attr) {
                        $attrName = strtolower($attr->name);
                        $attrValue = trim($attr->value);

                        // Strip any on* event handler or unwhitelisted attribute
                        if (str_starts_with($attrName, 'on') || !in_array($attrName, $allowedAttrs, true)) {
                            $attributesToRemove[] = $attr->name;
                            continue;
                        }

                        // Validate URL attributes against dangerous protocols (javascript:, data:, etc.)
                        if ($attrName === 'href' || $attrName === 'src') {
                            if (self::isDangerousUrl($attrValue)) {
                                $attributesToRemove[] = $attr->name;
                            }
                        }
                    }

                    foreach ($attributesToRemove as $attrName) {
                        $child->removeAttribute($attrName);
                    }
                }

                // Recurse into child nodes
                self::sanitizeNode($child, $dom);
            }
        }
    }

    /**
     * Detect javascript:, data:, vbscript: or other active content URL schemes.
     */
    private static function isDangerousUrl(string $url): bool
    {
        $cleaned = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        // Remove whitespace and invisible control characters
        $cleaned = preg_replace('/[\x00-\x20\x7f]/', '', $cleaned);

        if (preg_match('/^(javascript|data|vbscript|file):/i', $cleaned)) {
            return true;
        }

        return false;
    }
}
