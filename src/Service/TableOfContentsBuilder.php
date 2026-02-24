<?php

declare(strict_types=1);

namespace Bluebranch\Inhaltsverzeichnis\Service;

use Contao\Database;
use Contao\StringUtil;

class TableOfContentsBuilder
{
    /**
     * Builds and returns the rendered HTML list for the given configuration.
     *
     * @param int    $pageId     Current page ID (from $GLOBALS['objPage']->id)
     * @param int    $startLevel Inclusive start level (1–6)
     * @param int    $endLevel   Inclusive end level (1–6)
     * @param string $listType   'ol' or 'ul'
     * @param array  $columns    Layout columns to search, e.g. ['main', 'left']
     */
    public function build(int $pageId, int $startLevel, int $endLevel, string $listType, array $columns): string
    {
        $merged = array_merge(
            $this->fetchHeadlineItems($pageId, $startLevel, $endLevel, $columns),
            $this->fetchTextItems($pageId, $startLevel, $endLevel, $columns)
        );

        if (empty($merged)) {
            return '';
        }

        usort($merged, static function (array $a, array $b): int {
            if ($a['article_sorting'] !== $b['article_sorting']) {
                return $a['article_sorting'] <=> $b['article_sorting'];
            }
            if ($a['ce_sorting'] !== $b['ce_sorting']) {
                return $a['ce_sorting'] <=> $b['ce_sorting'];
            }
            return $a['offset'] <=> $b['offset'];
        });

        $seen     = [];
        $headings = [];

        foreach ($merged as $item) {
            $baseSlug = SlugGenerator::generate($item['text']);

            if ($baseSlug === '') {
                continue;
            }

            $headings[] = [
                'level' => $item['level'],
                'text'  => $item['text'],
                'slug'  => SlugGenerator::deduplicate($baseSlug, $seen),
            ];
        }

        if (empty($headings)) {
            return '';
        }

        return TocListRenderer::render($headings, $listType);
    }

    /**
     * Queries tl_content for headline elements on the given page.
     * Returns raw items with sort keys; slug deduplication happens in build().
     */
    private function fetchHeadlineItems(int $pageId, int $startLevel, int $endLevel, array $columns): array
    {
        if (empty($columns)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($columns), '?'));

        $sql = "
            SELECT c.headline, a.sorting AS article_sorting, c.sorting AS ce_sorting
            FROM tl_content c
            INNER JOIN tl_article a ON a.id = c.pid
            WHERE a.pid = ?
              AND c.type = 'headline'
              AND c.invisible = ''
              AND a.inColumn IN ({$placeholders})
            ORDER BY a.sorting ASC, c.sorting ASC
        ";

        $params = array_merge([$pageId], $columns);
        $result = Database::getInstance()->prepare($sql)->execute(...$params);
        $items  = [];

        while ($result->next()) {
            $data  = StringUtil::deserialize($result->headline, true);
            $text  = $data['value'] ?? '';
            $unit  = $data['unit'] ?? 'h2';
            $level = (int) ltrim($unit, 'h');

            if ($level < $startLevel || $level > $endLevel || $text === '') {
                continue;
            }

            $items[] = [
                'level'           => $level,
                'text'            => $text,
                'article_sorting' => (int) $result->article_sorting,
                'ce_sorting'      => (int) $result->ce_sorting,
                'offset'          => 0,
            ];
        }

        return $items;
    }

    /**
     * Queries tl_content for text elements on the given page and extracts inline headings.
     * Returns raw items with sort keys; slug deduplication happens in build().
     */
    private function fetchTextItems(int $pageId, int $startLevel, int $endLevel, array $columns): array
    {
        if (empty($columns)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($columns), '?'));

        $sql = "
            SELECT c.text, a.sorting AS article_sorting, c.sorting AS ce_sorting
            FROM tl_content c
            INNER JOIN tl_article a ON a.id = c.pid
            WHERE a.pid = ?
              AND c.type = 'text'
              AND c.invisible = ''
              AND a.inColumn IN ({$placeholders})
            ORDER BY a.sorting ASC, c.sorting ASC
        ";

        $params = array_merge([$pageId], $columns);
        $result = Database::getInstance()->prepare($sql)->execute(...$params);
        $items  = [];

        while ($result->next()) {
            $html = $result->text ?? '';

            if ($html === '') {
                continue;
            }

            preg_match_all('/<h([1-6])[^>]*>(.*?)<\/h[1-6]>/is', $html, $matches, PREG_SET_ORDER);

            // $offset is the 0-based heading index within this element — used as DOM sort key.
            foreach ($matches as $offset => $match) {
                $level = (int) $match[1];
                $text  = strip_tags($match[2]);

                if ($level < $startLevel || $level > $endLevel || trim($text) === '') {
                    continue;
                }

                $items[] = [
                    'level'           => $level,
                    'text'            => trim($text),
                    'article_sorting' => (int) $result->article_sorting,
                    'ce_sorting'      => (int) $result->ce_sorting,
                    'offset'          => $offset,
                ];
            }
        }

        return $items;
    }
}
