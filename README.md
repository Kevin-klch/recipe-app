# 🍽️ Meine Rezepte

Eine selbst gehostete Rezeptverwaltung auf Basis von Laravel und SQLite.

Die Anwendung ermöglicht das Anlegen, Bearbeiten, Löschen und Durchsuchen von Rezepten inklusive Bildern, Zutaten und zusätzlichen Informationen wie Zubereitungszeit, Schwierigkeit oder Ernährungsart.

## Features

### Rezepte verwalten

* Rezept erstellen
* Rezept bearbeiten
* Rezept löschen
* Rezeptdetails anzeigen
* Automatische Sortierung nach neuesten Rezepten

### Rezeptinformationen

* Titel
* Beschreibung
* Zubereitung
* Notizen
* Dauer in Minuten
* Aufwand / Schwierigkeit
* Preisschätzung
* Portionen
* Kalorien (optional)
* Ernährungsart

  * Normal
  * Vegetarisch
  * Vegan
* Kategorie

  * Frühstück
  * Mittagessen
  * Abendessen
  * Snack
  * Dessert
  * Dip
  * Getränk
  * Sonstiges

### Zutaten

* Beliebig viele Zutaten pro Rezept
* Menge
* Einheit
* Zutatenname
* Dynamisches Hinzufügen weiterer Zutatenfelder

### Bilder

* Foto pro Rezept
* Automatischer Platzhalter bei fehlendem Bild
* Bildanzeige in Listenansicht und Detailansicht

### Suche

* Volltextsuche über Titel und Beschreibung

## Verwendete Technologien

* PHP 8+
* Laravel
* SQLite
* Blade Templates
* CSS (Dark Mode Design)

## Installation

### Repository klonen

```bash
git clone <repository-url>
cd recipe-app
```

### Abhängigkeiten installieren

```bash
composer install
```

### Environment-Datei erstellen

```bash
cp .env.example .env
```

### Application Key generieren

```bash
php artisan key:generate
```

### SQLite Datenbank anlegen

```bash
touch database/database.sqlite
```

In der `.env` Datei:

```env
DB_CONNECTION=sqlite
```

### Migrationen ausführen

```bash
php artisan migrate
```

### Storage-Link erstellen

```bash
php artisan storage:link
```

### Lokalen Server starten

```bash
php artisan serve
```

Anschließend erreichbar unter:

```text
http://127.0.0.1:8000
```

## Projektstruktur

```text
app/
├── Http/
│   └── Controllers/
├── Models/
│   ├── Recipe.php
│   └── Ingredient.php

database/
├── migrations/
└── database.sqlite

public/
├── css/
└── storage/

resources/
└── views/
    ├── home.blade.php
    └── recipes/
```

## Geplante Features

* Favoriten
* Tags
* Kategorien filtern
* Erweiterte Suche
* Nährwertverwaltung
* Rezeptbewertung
* Import von Rezepten über URL
* Druckansicht
* PDF Export
* Responsive Mobile Navigation
* Deployment auf Raspberry Pi

## Lizenz

Dieses Projekt dient aktuell als privates Hobbyprojekt.
