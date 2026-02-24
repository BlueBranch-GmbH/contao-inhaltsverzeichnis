<?php

declare(strict_types=1);

namespace Bluebranch\Inhaltsverzeichnis\Service;

class TocListRenderer
{
    /**
     * Builds a nested ol/ul HTML string from a flat list of headings.
     *
     * @param non-empty-array<array{level: int, text: string, slug: string}> $headings Must not be empty.
     * @param string $listType 'ol' or 'ul'
     */
    public static function render(array $headings, string $listType): string
    {
        $html  = '';
        $stack = [];

        foreach ($headings as $heading) {
            $level = $heading['level'];
            $link  = sprintf(
                '<a href="#%s">%s</a>',
                htmlspecialchars($heading['slug'], ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($heading['text'], ENT_QUOTES, 'UTF-8')
            );

            if (empty($stack)) {
                $stack[] = ['level' => $level, 'html' => "<li>{$link}"];
            } elseif ($level > end($stack)['level']) {
                $stack[] = ['level' => $level, 'html' => "<{$listType}><li>{$link}"];
            } elseif ($level === end($stack)['level']) {
                $top = array_pop($stack);
                $stack[] = ['level' => $level, 'html' => $top['html'] . "</li>\n<li>{$link}"];
            } else {
                while (count($stack) > 1 && end($stack)['level'] > $level) {
                    $top    = array_pop($stack);
                    $parent = array_pop($stack);
                    $stack[] = [
                        'level' => $parent['level'],
                        'html'  => $parent['html'] . $top['html'] . "</li>\n</{$listType}>",
                    ];
                }

                if (end($stack)['level'] === $level) {
                    $top = array_pop($stack);
                    $stack[] = ['level' => $level, 'html' => $top['html'] . "</li>\n<li>{$link}"];
                } else {
                    $top = array_pop($stack);
                    $stack[] = ['level' => $top['level'], 'html' => $top['html'] . "</li>\n<li>{$link}"];
                }
            }
        }

        while (!empty($stack)) {
            $top = array_pop($stack);
            if (empty($stack)) {
                $html = $top['html'] . '</li>';
            } else {
                $parent = array_pop($stack);
                $stack[] = [
                    'level' => $parent['level'],
                    'html'  => $parent['html'] . $top['html'] . "</li>\n</{$listType}>",
                ];
            }
        }

        return "<{$listType}>\n{$html}\n</{$listType}>";
    }
}
