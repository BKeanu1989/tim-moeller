# Website-Dokumentation

## Astro-Projekt · Redakteursleitfaden

**Version 1.0 · Oktober 2026**

*Dieses Dokument richtet sich an Redakteure und Administratoren der Website. Es beschreibt, wie Inhalte gepflegt, Übersetzungen verwaltet und Medien hochgeladen werden – ohne Programmierkenntnisse.*

---

## Inhaltsverzeichnis

1. [Überblick über die Projektstruktur](#1-%C3%BCberblick-%C3%BCber-die-projektstruktur)
2. [Beiträge & Inhalte verwalten](#2-beitr%C3%A4ge--inhalte-verwalten)
3. [Internationalisierung (Mehrsprachigkeit)](#3-internationalisierung-mehrsprachigkeit)
4. [Bildmaterial & Medien](#4-bildmaterial--medien)
5. [Deployment via FTP (GitHub → Provider)](#5-deployment-via-ftp-github--provider)
6. [Häufige Fehler & Lösungen](#6-h%C3%A4ufige-fehler--l%C3%B6sungen)

---

## 1 Überblick über die Projektstruktur

Das Projekt basiert auf **Astro**, einem modernen Static-Site-Generator. Alle Inhalte liegen als einfache Textdateien (MDX) vor und können ohne Programmierkenntnisse bearbeitet werden.

| Verzeichnis | Beschreibung |
| --- | --- |
| `src/content/` | Alle redaktionellen Inhalte (Beiträge, Seiten) |
| `src/content/publications/` | Veröffentlichungen, gegliedert nach Sprache (`de` / `en`) |
| `src/content/authors/` | Autorenprofile |
| `src/content/career/` | Karriere-Inhalte |
| `src/assets/` | Optimierte Bilder & Medien (vom Build-Prozess verarbeitet) |
| `src/i18n/` | Übersetzungsdateien (`ui.ts`, `utils.ts`) |
| `src/pages/` | Seitenvorlagen (nicht regelmäßig bearbeiten) |
| `src/layouts/` | Layout-Dateien (kein Redakteurszugriff nötig) |

> 💡 **Hinweis:** Dateien in `src/pages/` und `src/layouts/` sind technische Vorlagen. Diese bitte nur nach Absprache mit dem Entwickler ändern.

---

## 2 Beiträge & Inhalte verwalten

Alle Beiträge befinden sich im Verzeichnis `src/content/publications/` und sind nach Sprache unterteilt:

- `src/content/publications/de/` → deutsche Beiträge
- `src/content/publications/en/` → englische Beiträge

Jeder Beitrag ist eine einzelne `.mdx`-Datei. MDX ist normales Markdown – für das Schreiben genügt grundlegendes Markdown-Wissen.

### 2.1 Neuen Beitrag anlegen

1. Öffnen Sie das Verzeichnis der gewünschten Sprache auf GitHub (z. B. `src/content/publications/de/`).
2. Klicken Sie auf **„Add file"** → **„Create new file"**.
3. Benennen Sie die Datei nach dem Schema:

   ```
   mein-thema-stichwort.mdx
   ```
4. Fügen Sie am Anfang der Datei den **Frontmatter** ein (Metadaten zwischen den drei Strichen):

   ```yaml
   ---
   title: "Titel des Beitrags"
   description: "Kurze Beschreibung für SEO und Vorschau"
   pubDate: 2026-10-05
   author: "Vorname Nachname"
   image: "/images/mein-bild.jpg"
   ---
   ```
5. Schreiben Sie darunter den Beitragstext in Markdown.
6. Klicken Sie auf **„Commit changes"** → kurze Beschreibung eingeben → **„Commit directly to main"**.

> 💡 **Hinweis:** Die Website wird automatisch neu gebaut, sobald die Änderung auf GitHub gespeichert ist. Je nach Provider-Einstellung dauert das 1–5 Minuten.

### 2.2 Beitrag bearbeiten

1. Navigieren Sie auf GitHub zur entsprechenden `.mdx`-Datei.
2. Klicken Sie auf das **Stift-Symbol (✏️)** oben rechts in der Dateiansicht.
3. Nehmen Sie Ihre Änderungen vor.
4. Klicken Sie unten auf **„Commit changes"**.

### 2.3 Beitrag löschen

1. Öffnen Sie die Datei auf GitHub.
2. Klicken Sie auf das **Drei-Punkte-Menü (···)** → **„Delete file"**.
3. Bestätigen Sie mit **„Commit changes"**.

> 💡 **Hinweis:** Gelöschte Dateien können über die GitHub-History jederzeit wiederhergestellt werden. Gehen Sie dafür zu „History" der Datei und wählen Sie eine frühere Version.

---

## 3 Internationalisierung (Mehrsprachigkeit)

Das Projekt unterstützt mehrere Sprachen. Die Konfiguration der Übersetzungen befindet sich in:

| Datei | Beschreibung |
| --- | --- |
| `src/i18n/ui.ts` | Alle UI-Texte (Menüs, Buttons, Labels) in allen Sprachen |
| `src/i18n/utils.ts` | Hilfsfunktionen für die Sprachauswahl (technisch, nicht bearbeiten) |
| `src/content/[lang]/config.ts` | Sprachspezifische Inhaltskonfiguration |

### 3.1 Bestehende Übersetzung anpassen

Öffnen Sie `src/i18n/ui.ts` auf GitHub. Die Datei ist wie folgt aufgebaut:

```typescript
export const ui = {
  de: {
    'nav.home': 'Startseite',
    'nav.about': 'Über mich',
    'nav.publications': 'Veröffentlichungen',
    // ...
  },
  en: {
    'nav.home': 'Home',
    'nav.about': 'About me',
    'nav.publications': 'Publications',
    // ...
  },
};
```

Um einen Text zu ändern, suchen Sie den entsprechenden Schlüssel (z. B. `nav.about`) und passen Sie den Wert in Anführungszeichen an. Achten Sie darauf, die Anführungszeichen und Kommas nicht zu verändern.

### 3.2 Neue Sprache hinzufügen

Für eine neue Sprache sind folgende Schritte notwendig – **bitte nach Absprache mit dem Entwickler vornehmen:**

- In `src/i18n/ui.ts` einen neuen Sprachblock hinzufügen (z. B. `fr: { ... }`).
- In `src/pages/` das Verzeichnis `_[lang]/` entsprechend ergänzen.
- In `src/content/publications/` einen neuen Sprachordner anlegen (z. B. `fr/`).

> 💡 **Hinweis:** Der Sprachcode muss dem ISO-639-1-Standard entsprechen (`de`, `en`, `fr`, `es` …). Der Entwickler richtet die Routing-Logik initial ein.

### 3.3 Sprachspezifische Beiträge

Für jeden Beitrag sollte idealerweise eine Version pro Sprache existieren – mit identischem Dateinamen in den jeweiligen Sprachordnern:

- `src/content/publications/de/real-time-ecg-analysis.mdx`
- `src/content/publications/en/real-time-ecg-analysis.mdx`

Existiert für eine Sprache keine Übersetzung, wird der Beitrag in der jeweiligen Sprachversion nicht angezeigt.

---

## 4 Bildmaterial & Medien

### 4.1 Wo Bilder abgelegt werden

| Verzeichnis | Verwendung |
| --- | --- |
| `src/assets/` | Bilder, die Astro automatisch optimiert (empfohlen für Artikelbilder & Layouts) |
| `public/images/` | Bilder, die direkt ohne Verarbeitung ausgeliefert werden (z. B. Logos, Favicons) |

**Empfehlung:** Legen Sie Artikelbilder grundsätzlich in `src/assets/images/` ab. Astro optimiert sie automatisch (WebP, korrekte Größen), was die Ladezeit der Website verbessert.

### 4.2 Bild auf GitHub hochladen

1. Navigieren Sie auf GitHub zu `src/assets/images/` (oder `public/images/` je nach Bedarf).
2. Klicken Sie auf **„Add file"** → **„Upload files"**.
3. Ziehen Sie die Bilddatei ins Fenster oder wählen Sie sie aus.
4. Klicken Sie auf **„Commit changes"**.

### 4.3 Namenskonventionen für Bilder

Verwenden Sie ausschließlich **Kleinbuchstaben, Zahlen und Bindestriche**. Keine Leerzeichen, Umlaute oder Sonderzeichen:

| Beispiel | Status |
| --- | --- |
| `ecg-studie-2026.jpg` | ✅ Korrekt |
| `portrait-dr-mueller.png` | ✅ Korrekt |
| `EKG Studie 2026.jpg` | ❌ Leerzeichen → Fehler im Build |
| `portrait-Dr.Müller.png` | ❌ Sonderzeichen → Fehler im Build |

### 4.4 Bild in einem Beitrag verwenden

Referenzieren Sie das Bild im Frontmatter des Beitrags:

```yaml
---
image: "/images/ecg-studie-2026.jpg"
---
```

Oder direkt im Beitragstext mit Markdown-Syntax:

```markdown
![Beschreibung des Bildes](/images/ecg-studie-2026.jpg)
```

---

## 5 Deployment via FTP (GitHub → Provider)

### 5.1 Workflow-Übersicht

Der Deployment-Prozess läuft **vollautomatisch** über GitHub Actions ab. Sobald eine Änderung auf dem `main`-Branch gespeichert wird, geschieht Folgendes automatisch:

| Schritt | Beschreibung |
| --- | --- |
| 1 | GitHub erkennt die Änderung im Repository |
| 2 | GitHub Actions startet den Build-Prozess (Astro baut die statischen HTML-Dateien) |
| 3 | Die fertigen Dateien (`dist/`) werden via FTP auf den Server übertragen |
| 4 | Die Website ist aktualisiert (i. d. R. nach 2–5 Minuten sichtbar) |

### 5.2 Was Sie als Redakteur tun müssen

> 💡 **Hinweis:** Als Redakteur müssen Sie sich um das Deployment nicht kümmern. Es reicht, die Änderung auf GitHub zu speichern ("Commit") – alles weitere läuft automatisch oder macht der Entwickler.

### 5.3 Status des Deployments prüfen

1. Öffnen Sie das Repository auf GitHub.
2. Klicken Sie auf den Tab **„Actions"** (oben in der Navigation).
3. Der aktuellste Eintrag zeigt den Status:
   - ✅ Grüner Haken → Build erfolgreich, Website ist aktualisiert
   - 🟡 Gelber Kreis → Build läuft noch
   - ❌ Rotes X → Fehler aufgetreten (Entwickler informieren)



---

## 6 Häufige Fehler & Lösungen

| Problem | Lösung |
| --- | --- |
| Build schlägt fehl (rotes X) | Frontmatter prüfen: Sind alle Felder korrekt? Keine fehlenden Anführungszeichen oder Doppelpunkte? |
| Bild wird nicht angezeigt | Dateiname prüfen (keine Leerzeichen/Umlaute). Pfad im Frontmatter korrekt? |
| Neue Sprache erscheint nicht | Wurde der Sprachcode in `ui.ts` und im `pages`-Verzeichnis eingetragen? |
| Beitrag erscheint nur in einer Sprache | Für jede Sprache muss eine separate `.mdx`-Datei im jeweiligen Sprachordner vorhanden sein. |
| Änderung nach 10 Min. nicht sichtbar | Actions-Tab auf GitHub prüfen; ggf. Browser-Cache leeren (`Strg+Shift+R`) |

---

*Bei technischen Fragen wenden Sie sich bitte an Ihren Entwickler.*