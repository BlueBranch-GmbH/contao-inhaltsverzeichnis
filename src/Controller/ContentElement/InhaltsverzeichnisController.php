<?php

declare(strict_types=1);

namespace Bluebranch\Inhaltsverzeichnis\Controller\ContentElement;

use Bluebranch\Inhaltsverzeichnis\Service\TableOfContentsBuilder;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement("inhaltsverzeichnis", category="includes")
 */
class InhaltsverzeichnisController extends AbstractContentElementController
{
    public function __construct(private readonly TableOfContentsBuilder $builder)
    {
    }

    protected function getResponse(object $template, ContentModel $model, Request $request): Response
    {
        if (!isset($GLOBALS['objPage'])) {
            return new Response('');
        }

        $startLevel = (int) ($model->toc_headline_start ?: 2);
        $endLevel   = (int) ($model->toc_headline_end ?: 4);
        $listType   = in_array($model->toc_list_type, ['ol', 'ul'], true) ? $model->toc_list_type : 'ol';

        $headlineData = StringUtil::deserialize($model->headline, true);
        $headlineText = $headlineData['value'] ?? '';
        $headlineUnit = $headlineData['unit'] ?? 'h1';

        $cssIdData = StringUtil::deserialize($model->cssID, true);
        $htmlId    = $cssIdData[0] ?? '';
        $cssClass  = $cssIdData[1] ?? '';

        // Page mode: render placeholder, InjectHeadingIdsListener fills it after full rendering
        if ($model->toc_source === 'page') {
            $config = ['s' => $startLevel, 'e' => $endLevel, 'l' => $listType];

            if ($htmlId !== '') {
                $config['id'] = $htmlId;
            }

            if ($cssClass !== '') {
                $config['c'] = $cssClass;
            }

            $json = json_encode($config, JSON_HEX_APOS | JSON_HEX_TAG | JSON_HEX_AMP);

            if ($json === false) {
                return new Response('');
            }

            $output = '';

            if ($headlineText !== '') {
                $output .= '<' . $headlineUnit . '>' . htmlspecialchars($headlineText, ENT_QUOTES, 'UTF-8') . '</' . $headlineUnit . ">\n";
            }

            $output .= "<div data-toc-config='" . $json . "' class='inhaltsverzeichnis-placeholder'></div>";

            return new Response($output);
        }

        // Articles mode: DB queries with column filter (existing behaviour)
        $pageId  = (int) $GLOBALS['objPage']->id;
        $columns = StringUtil::deserialize($model->toc_columns, true);

        if (empty($columns)) {
            $columns = ['main'];
        }

        $tocHtml = $this->builder->build($pageId, $startLevel, $endLevel, $listType, $columns);

        if (method_exists($template, 'set')) {
            $template->set('tocHtml', $tocHtml);
            $template->set('isEmpty', $tocHtml === '');
            $template->set('headline', $headlineText);
            $template->set('headlineUnit', $headlineUnit);
            $template->set('htmlId', $htmlId);
            $template->set('cssClass', $cssClass);

            return $template->getResponse();
        }

        // Contao 4.x: apply customTpl explicitly (framework may not handle it automatically)
        if ($model->customTpl && method_exists($template, 'setName')) {
            $template->setName($model->customTpl);
        }

        $template->tocHtml      = $tocHtml;
        $template->isEmpty      = $tocHtml === '';
        $template->headline     = $headlineText;
        $template->headlineUnit = $headlineUnit;
        $template->htmlId       = $htmlId;
        $template->cssClass     = $cssClass;

        return new Response($template->parse());
    }
}
