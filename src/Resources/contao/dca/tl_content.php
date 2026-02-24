<?php

declare(strict_types=1);

// Palette für das Content-Element
$GLOBALS['TL_DCA']['tl_content']['palettes']['inhaltsverzeichnis'] =
    '{type_legend},type,headline;'
    . '{inhaltsverzeichnis_legend},toc_source,toc_headline_start,toc_headline_end,toc_list_type,toc_columns;'
    . '{invisible_legend:hide},invisible,start,stop';

// Felder
$GLOBALS['TL_DCA']['tl_content']['fields']['toc_source'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['toc_source'],
    'inputType' => 'select',
    'options'   => ['articles', 'page'],
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['toc_source_options'],
    'default'   => 'articles',
    'eval'      => ['mandatory' => true, 'tl_class' => 'w50 clr'],
    'sql'       => ['type' => 'string', 'length' => 10, 'default' => 'articles'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['toc_headline_start'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['toc_headline_start'],
    'inputType' => 'select',
    'options'   => ['1', '2', '3', '4', '5', '6'],
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['toc_headline_levels'],
    'default'   => '2',
    'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
    'sql'       => ['type' => 'string', 'length' => 1, 'default' => '2'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['toc_headline_end'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_content']['toc_headline_end'],
    'inputType'     => 'select',
    'options'       => ['1', '2', '3', '4', '5', '6'],
    'reference'     => &$GLOBALS['TL_LANG']['tl_content']['toc_headline_levels'],
    'default'       => '4',
    'eval'          => ['mandatory' => true, 'tl_class' => 'w50'],
    'save_callback' => [['tl_content_inhaltsverzeichnis', 'validateHeadlineRange']],
    'sql'           => ['type' => 'string', 'length' => 1, 'default' => '4'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['toc_list_type'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['toc_list_type'],
    'inputType' => 'select',
    'options'   => ['ol', 'ul'],
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['toc_list_types'],
    'default'   => 'ol',
    'eval'      => ['mandatory' => true, 'tl_class' => 'w50'],
    'sql'       => ['type' => 'string', 'length' => 2, 'default' => 'ol'],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['toc_columns'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['toc_columns'],
    'inputType' => 'checkbox',
    'options'   => ['main', 'left', 'right', 'header', 'footer'],
    'reference' => &$GLOBALS['TL_LANG']['tl_content']['toc_column_names'],
    'default'   => ['main'],
    'eval'      => ['mandatory' => true, 'multiple' => true, 'tl_class' => 'w50 clr'],
    'sql'       => ['type' => 'blob', 'notnull' => false],
];

// Callback-Klasse für save_callback (klassisches Contao-Muster)
class tl_content_inhaltsverzeichnis
{
    public function validateHeadlineRange(string $value, \Contao\DataContainer $dc): string
    {
        $start = (int) $dc->activeRecord->toc_headline_start;
        $end   = (int) $value;

        if ($end < $start) {
            throw new \RuntimeException(
                $GLOBALS['TL_LANG']['tl_content']['toc_error_range']
            );
        }

        return $value;
    }
}
