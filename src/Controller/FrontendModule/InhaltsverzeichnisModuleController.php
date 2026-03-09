<?php

declare(strict_types=1);

namespace Bluebranch\Inhaltsverzeichnis\Controller\FrontendModule;

use Bluebranch\Inhaltsverzeichnis\Service\TableOfContentsBuilder;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\ModuleModel;
use Contao\StringUtil;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule("inhaltsverzeichnis", category="miscellaneous")
 */
class InhaltsverzeichnisModuleController extends AbstractFrontendModuleController
{
    public function __construct(private readonly TableOfContentsBuilder $builder)
    {
    }

    protected function getResponse(object $template, ModuleModel $model, Request $request): Response
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

        if ($model->toc_source === 'page') {
            // Page mode: placeholder div inside nav; InjectHeadingIdsListener replaces
            // the entire <nav>...<div>...</div>...</nav> block after full page rendering.
            // Routing through the template ensures customTpl, headline and cssID work.
            $json = json_encode(
                ['s' => $startLevel, 'e' => $endLevel, 'l' => $listType],
                JSON_HEX_APOS | JSON_HEX_TAG | JSON_HEX_AMP
            );

            if ($json === false) {
                return new Response('');
            }

            $tocHtml = "<div data-toc-config='" . $json . "' class='inhaltsverzeichnis-placeholder'></div>";
        } else {
            // Articles mode: DB queries with column filter
            $pageId  = (int) $GLOBALS['objPage']->id;
            $columns = StringUtil::deserialize($model->toc_columns, true);

            if (empty($columns)) {
                $columns = ['main'];
            }

            $tocHtml = $this->builder->build($pageId, $startLevel, $endLevel, $listType, $columns);
        }

        $isEmpty = $model->toc_source !== 'page' && $tocHtml === '';

        if (method_exists($template, 'set')) {
            $template->set('tocHtml', $tocHtml);
            $template->set('isEmpty', $isEmpty);
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
        $template->isEmpty      = $isEmpty;
        $template->headline     = $headlineText;
        $template->headlineUnit = $headlineUnit;
        $template->htmlId       = $htmlId;
        $template->cssClass     = $cssClass;

        return new Response($template->parse());
    }
}
