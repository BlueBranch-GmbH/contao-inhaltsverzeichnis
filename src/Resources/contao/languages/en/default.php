<?php

$GLOBALS['TL_LANG']['CTE']['inhaltsverzeichnis']    = ['Table of Contents', 'Generates a hierarchical table of contents from the page\'s headline elements.'];
$GLOBALS['TL_LANG']['FMD']['inhaltsverzeichnis']    = ['Table of Contents', 'Generates a hierarchical table of contents from the page\'s headline elements.'];

$GLOBALS['TL_LANG']['tl_content']['inhaltsverzeichnis_legend'] = 'Table of Contents settings';
$GLOBALS['TL_LANG']['tl_content']['toc_headline_start'] = ['Start level', 'Highest priority heading level to include (e.g. 2 for h2).'];
$GLOBALS['TL_LANG']['tl_content']['toc_headline_end']   = ['End level', 'Lowest priority heading level to include (e.g. 4 for h4).'];
$GLOBALS['TL_LANG']['tl_content']['toc_list_type']      = ['List type', 'Ordered (ol) or unordered (ul) list.'];
$GLOBALS['TL_LANG']['tl_content']['toc_columns']        = ['Columns', 'Articles in these layout columns will be searched for headings. Only effective in "Article content" mode.'];
$GLOBALS['TL_LANG']['tl_content']['toc_source']         = ['Sources', 'Defines where headings for the table of contents are collected from.'];
$GLOBALS['TL_LANG']['tl_content']['toc_source_options'] = [
    'articles' => 'Article content (with column filter)',
    'page'     => 'Entire page (news, events, all modules)',
];

$GLOBALS['TL_LANG']['tl_content']['toc_headline_levels'] = [
    '1' => 'h1', '2' => 'h2', '3' => 'h3',
    '4' => 'h4', '5' => 'h5', '6' => 'h6',
];
$GLOBALS['TL_LANG']['tl_content']['toc_list_types'] = [
    'ol' => 'Ordered list (ol)',
    'ul' => 'Unordered list (ul)',
];
$GLOBALS['TL_LANG']['tl_content']['toc_column_names'] = [
    'main'   => 'Main column (main)',
    'left'   => 'Left column (left)',
    'right'  => 'Right column (right)',
    'header' => 'Header (header)',
    'footer' => 'Footer (footer)',
];

$GLOBALS['TL_LANG']['tl_content']['toc_error_range'] = 'The end level must not be smaller than the start level.';
