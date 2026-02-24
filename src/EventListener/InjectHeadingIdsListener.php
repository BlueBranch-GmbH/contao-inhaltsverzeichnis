<?php

declare(strict_types=1);

namespace Bluebranch\Inhaltsverzeichnis\EventListener;

use Bluebranch\Inhaltsverzeichnis\Service\SlugGenerator;
use Bluebranch\Inhaltsverzeichnis\Service\TocListRenderer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class InjectHeadingIdsListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => '__invoke'];
    }

    /**
     * Pass 1: injects id attributes into <h1>–<h6> tags that have none, collecting them.
     * Pass 2: replaces data-toc-config placeholders (page mode) with built TOC HTML.
     */
    public function __invoke(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response    = $event->getResponse();
        $contentType = $response->headers->get('Content-Type', '');

        if (!str_contains($contentType, 'text/html')) {
            return;
        }

        $html = $response->getContent();

        if ($html === false || $html === '') {
            return;
        }

        $seen     = [];
        $headings = []; // collected for page-mode TOC: [['level', 'text', 'slug'], ...]

        // Pass 1: inject id attributes, build $headings list
        $html = preg_replace_callback(
            '/<(h[1-6])((?:\s[^>]*)?)>(.*?)<\/h[1-6]>/is',
            static function (array $matches) use (&$seen, &$headings): string {
                [, $tag, $attrs, $inner] = $matches;

                // Skip tags that already carry an id attribute
                if (preg_match('/\bid\s*=/i', $attrs)) {
                    return $matches[0];
                }

                $text     = strip_tags($inner);
                $baseSlug = SlugGenerator::generate($text);

                if ($baseSlug === '') {
                    return $matches[0];
                }

                $level = (int) ltrim($tag, 'h');
                $slug  = SlugGenerator::deduplicate($baseSlug, $seen);

                $headings[] = ['level' => $level, 'text' => trim($text), 'slug' => $slug];

                return "<{$tag} id=\"" . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') . "\"{$attrs}>{$inner}</{$tag}>";
            },
            $html
        );

        if ($html === null) {
            return;
        }

        // Pass 2: replace page-mode TOC placeholders
        if (str_contains($html, 'data-toc-config')) {
            $html = preg_replace_callback(
                "~<div[^>]*data-toc-config='([^']+)'[^>]*></div>~i",
                static function (array $matches) use ($headings): string {
                    $config = json_decode($matches[1], true);

                    if (!is_array($config)) {
                        trigger_error(
                            'InjectHeadingIdsListener: invalid JSON in data-toc-config placeholder: ' . $matches[1],
                            E_USER_WARNING
                        );

                        return '';
                    }

                    $startLevel = (int) ($config['s'] ?? 2);
                    $endLevel   = (int) ($config['e'] ?? 4);
                    $listType   = in_array($config['l'] ?? '', ['ol', 'ul'], true) ? $config['l'] : 'ol';

                    $filtered = array_values(array_filter(
                        $headings,
                        static fn (array $h): bool => $h['level'] >= $startLevel && $h['level'] <= $endLevel
                    ));

                    if (empty($filtered)) {
                        return '';
                    }

                    return '<nav class="inhaltsverzeichnis">' . "\n    "
                        . TocListRenderer::render($filtered, $listType)
                        . "\n</nav>";
                },
                $html
            );

            if ($html === null) {
                return;
            }
        }

        $response->setContent($html);
    }
}
