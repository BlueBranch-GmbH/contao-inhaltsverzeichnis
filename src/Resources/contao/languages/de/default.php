<?php

// Content-Element / Modul Label (Backend-Auswahl)
$GLOBALS['TL_LANG']['CTE']['inhaltsverzeichnis']    = ['Inhaltsverzeichnis', 'Generiert ein hierarchisches Inhaltsverzeichnis aus den Überschriften der Seite.'];
$GLOBALS['TL_LANG']['FMD']['inhaltsverzeichnis']    = ['Inhaltsverzeichnis', 'Generiert ein hierarchisches Inhaltsverzeichnis aus den Überschriften der Seite.'];

// Felder
$GLOBALS['TL_LANG']['tl_content']['inhaltsverzeichnis_legend'] = 'Inhaltsverzeichnis-Einstellungen';
$GLOBALS['TL_LANG']['tl_content']['toc_headline_start'] = ['Startebene', 'Kleinste Überschriften-Ebene, die aufgenommen wird (z. B. 2 für h2).'];
$GLOBALS['TL_LANG']['tl_content']['toc_headline_end']   = ['Endebene', 'Größte Überschriften-Ebene, die aufgenommen wird (z. B. 4 für h4).'];
$GLOBALS['TL_LANG']['tl_content']['toc_list_type']      = ['Listentyp', 'Geordnete (ol) oder ungeordnete (ul) Liste.'];
$GLOBALS['TL_LANG']['tl_content']['toc_columns']        = ['Spalten', 'Artikel in diesen Layout-Spalten werden nach Überschriften durchsucht. Nur im Modus „Artikelinhalte" wirksam.'];
$GLOBALS['TL_LANG']['tl_content']['toc_source']         = ['Quellen', 'Legt fest, woher Überschriften für das Inhaltsverzeichnis stammen.'];
$GLOBALS['TL_LANG']['tl_content']['toc_source_options'] = [
    'articles' => 'Artikelinhalte (mit Spaltenfilter)',
    'page'     => 'Gesamte Seite (News, Events, alle Module)',
];

// Referenz-Labels
$GLOBALS['TL_LANG']['tl_content']['toc_headline_levels'] = [
    '1' => 'h1', '2' => 'h2', '3' => 'h3',
    '4' => 'h4', '5' => 'h5', '6' => 'h6',
];
$GLOBALS['TL_LANG']['tl_content']['toc_list_types'] = [
    'ol' => 'Geordnete Liste (ol)',
    'ul' => 'Ungeordnete Liste (ul)',
];
$GLOBALS['TL_LANG']['tl_content']['toc_column_names'] = [
    'main'   => 'Hauptspalte (main)',
    'left'   => 'Linke Spalte (left)',
    'right'  => 'Rechte Spalte (right)',
    'header' => 'Kopfbereich (header)',
    'footer' => 'Fußbereich (footer)',
];

// Fehlermeldungen
$GLOBALS['TL_LANG']['tl_content']['toc_error_range'] = 'Die Endebene darf nicht kleiner sein als die Startebene.';
