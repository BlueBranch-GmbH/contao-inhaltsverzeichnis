<?php

declare(strict_types=1);

namespace Bluebranch\Inhaltsverzeichnis\Service;

class SlugGenerator
{
    /**
     * Generates a URL-safe slug from a heading text.
     * The same input always produces the same slug (before deduplication).
     */
    public static function generate(string $text): string
    {
        // Strip HTML tags (safety: text may contain inline markup)
        $text = strip_tags($text);

        // Decode HTML entities
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Lowercase
        $text = mb_strtolower($text, 'UTF-8');

        // Replace German umlauts
        $text = str_replace(
            ['ä', 'ö', 'ü', 'ß'],
            ['ae', 'oe', 'ue', 'ss'],
            $text
        );

        // Replace spaces and underscores with hyphens
        $text = preg_replace('/[\s_]+/', '-', $text);

        // Remove any character that is not a-z, 0-9 or hyphen
        $text = preg_replace('/[^a-z0-9\-]/', '', $text);

        // Collapse multiple hyphens
        $text = preg_replace('/-{2,}/', '-', $text);

        // Trim hyphens from start and end
        return trim($text, '-');
    }

    /**
     * Deduplicates slugs within a set.
     * Pass $seen by reference across multiple calls (e.g. across all articles on a page).
     *
     * @param array<string,int> $seen  Tracks how many times each base slug was seen
     */
    public static function deduplicate(string $slug, array &$seen): string
    {
        if (!isset($seen[$slug])) {
            $seen[$slug] = 1;
            return $slug;
        }

        $seen[$slug]++;
        return $slug . '-' . $seen[$slug];
    }
}
