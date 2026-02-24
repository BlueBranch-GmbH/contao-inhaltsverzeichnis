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

        // Page mode: render placeholder, InjectHeadingIdsListener fills it after full rendering
        if ($model->toc_source === 'page') {
            $json = json_encode(
                ['s' => $startLevel, 'e' => $endLevel, 'l' => $listType],
                JSON_HEX_APOS | JSON_HEX_TAG | JSON_HEX_AMP
            );

            if ($json === false) {
                return new Response('');
            }

            return new Response("<div data-toc-config='" . $json . "' class='inhaltsverzeichnis-placeholder'></div>");
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

            return $template->getResponse();
        }

        $template->tocHtml = $tocHtml;
        $template->isEmpty = $tocHtml === '';

        return new Response($template->parse());
    }
}
