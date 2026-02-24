# Inhaltsverzeichnis für Contao (DE)

Generiere automatisch ein hierarchisches Inhaltsverzeichnis aus den Überschriften deiner Contao-Seite –
als Content-Element oder Frontend-Modul, mit Anker-Links zu jeder Überschrift.

## Installation

```bash
composer require bluebranch/inhaltsverzeichnis
php vendor/bin/contao-console contao:migrate
```

Danach steht das Element unter **Inhaltselemente → Einschlüsse → Inhaltsverzeichnis** sowie als
Frontend-Modul zur Verfügung.

## Verwendung

1. Inhaltsverzeichnis-Element in einen Artikel einfügen (oder als Frontend-Modul ins Layout einbinden)
2. Quellen-Modus wählen (siehe unten)
3. Start- und Endebene der Überschriften festlegen (Standard: h2–h4)
4. Listentyp wählen: geordnete (`ol`) oder ungeordnete (`ul`) Liste
5. Speichern – fertig

Das Element rendert ein `<nav class="inhaltsverzeichnis">` mit verschachtelten Links zu allen
gefundenen Überschriften. Alle Überschriften auf der Seite erhalten dabei automatisch `id`-Attribute
als Anker.

## Quellen-Modi

### Artikelinhalte (Standard)

Durchsucht die Überschriften-Inhaltselemente (`tl_content`) der aktuellen Seite per Datenbankabfrage.
Über einen Spaltenfilter lässt sich einschränken, welche Layout-Spalten berücksichtigt werden
(Hauptspalte, linke Spalte, rechte Spalte, Kopf- und Fußbereich).

Dieser Modus ist ideal für klassische Seiten, auf denen der gesamte Inhalt aus Contao-Artikeln besteht.

### Gesamte Seite

Erfasst alle Überschriften der vollständig gerenderten Seite – einschließlich Überschriften aus
News-Artikeln, Event-Details, Element-Gruppen und beliebigen anderen Frontend-Modulen.

Der Modus funktioniert, indem nach dem Rendern der gesamten Seite ein `kernel.response`-Listener
alle `<h1>`–`<h6>` im HTML auswertet, IDs injiziert und den Platzhalter des Elements durch den
fertigen TOC ersetzt.

> Hinweis: Der Spaltenfilter ist in diesem Modus nicht wirksam, da alle Überschriften der Seite
> unabhängig von ihrer Layout-Spalte erfasst werden.

## Einstellungen im Backend

| Einstellung | Beschreibung |
|---|---|
| Quellen | `Artikelinhalte` (DB-Abfrage) oder `Gesamte Seite` (Response-Scan) |
| Startebene | Kleinste Überschriftenebene im TOC (z. B. 2 für h2) |
| Endebene | Größte Überschriftenebene im TOC (z. B. 4 für h4) |
| Listentyp | Geordnete (`ol`) oder ungeordnete (`ul`) Liste |
| Spalten | Welche Layout-Spalten durchsucht werden (nur im Modus „Artikelinhalte") |

## Automatische ID-Injection

Unabhängig vom gewählten Modus injiziert die Erweiterung automatisch `id`-Attribute in alle
`<h1>`–`<h6>` auf der Seite, die noch kein `id`-Attribut besitzen. Die IDs werden als URL-taugliche
Slugs aus dem Überschriftentext generiert und bei Duplikaten automatisch nummeriert
(z. B. `abschnitt`, `abschnitt-2`, `abschnitt-3`).

So funktionieren Anker-Links auch dann, wenn das Inhaltsverzeichnis-Element gar nicht auf der Seite
eingebunden ist.

## Vorteile

- Kein manuelles Setzen von Anker-IDs notwendig
- Unterstützt alle Contao-Seitentypen: News, Events, Element-Gruppen, eigene Module
- Als Content-Element und als Frontend-Modul nutzbar
- Konfigurierbar nach Überschriftenebene, Listentyp und Layout-Spalte
- Kompatibel mit Contao 4.13 und Contao 5.x

---

# Table of Contents for Contao (EN)

Automatically generate a hierarchical table of contents from the headings on your Contao page –
as a content element or frontend module, with anchor links to every heading.

## Installation

```bash
composer require bluebranch/inhaltsverzeichnis
php vendor/bin/contao-console contao:migrate
```

The element is then available under **Content elements → Includes → Inhaltsverzeichnis** and as a
frontend module.

## Usage

1. Insert the table of contents element into an article (or add it as a frontend module to a layout)
2. Choose a source mode (see below)
3. Set the start and end level for headings (default: h2–h4)
4. Choose a list type: ordered (`ol`) or unordered (`ul`)
5. Save – done

The element renders a `<nav class="inhaltsverzeichnis">` with nested links to all found headings.
All headings on the page automatically receive `id` attributes as anchors.

## Source Modes

### Article content (default)

Queries the heading content elements (`tl_content`) of the current page from the database.
A column filter lets you restrict which layout columns are included
(main column, left column, right column, header and footer).

This mode is ideal for classic pages where all content comes from Contao articles.

### Entire page

Captures all headings from the fully rendered page – including headings from news articles,
event details, element groups, and any other frontend modules.

This works by running a `kernel.response` listener after the entire page has been rendered.
The listener scans all `<h1>`–`<h6>` elements in the HTML, injects IDs, and replaces the element's
placeholder with the finished TOC.

> Note: The column filter has no effect in this mode, as all headings on the page are captured
> regardless of their layout column.

## Backend Settings

| Setting | Description |
|---|---|
| Sources | `Article content` (DB query) or `Entire page` (response scan) |
| Start level | Lowest heading level included in the TOC (e.g. 2 for h2) |
| End level | Highest heading level included in the TOC (e.g. 4 for h4) |
| List type | Ordered (`ol`) or unordered (`ul`) list |
| Columns | Which layout columns are searched (only in "Article content" mode) |

## Automatic ID injection

Regardless of the chosen mode, the extension automatically injects `id` attributes into all
`<h1>`–`<h6>` elements on the page that do not already have one. IDs are generated as URL-safe
slugs from the heading text and are numbered automatically when duplicates occur
(e.g. `section`, `section-2`, `section-3`).

This means anchor links work even when the table of contents element is not present on the page.

## Advantages

- No manual anchor IDs required
- Supports all Contao page types: news, events, element groups, custom modules
- Available as both a content element and a frontend module
- Configurable by heading level, list type, and layout column
- Compatible with Contao 4.13 and Contao 5.x

---

## Vielen Dank

Danke für die Nutzung der Inhaltsverzeichnis-Erweiterung.

Das Team von [www.bluebranch.de](https://www.bluebranch.de/)

## Changes

### 1.1.x – 2026-02-24

- Add source mode "Entire page" (`toc_source = 'page'`) for news, events, element groups and all frontend modules
- Add automatic `id` injection for all headings via `kernel.response` listener

### 1.0.x – 2024

- Initial release
- Content element and frontend module for generating a hierarchical table of contents
- Configurable heading levels (start/end), list type (ol/ul) and layout column filter
- Automatic slug generation with deduplication
