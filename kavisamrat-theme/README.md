# Kavisamrat — WordPress Theme

Digital archive & author website for **Viswanadha Sathyanarayana** ("Kavi Samrat"),
built on **PODS Framework** + **SiteOrigin Page Builder**, styled to your
"Kavisamrat — Modern Heritage Brand Guidelines" design system, with
sethring.com-inspired scroll/hover motion powered by GSAP.

---

## 1. Requirements

- WordPress 6.x
- **Pods Framework** plugin (required — https://wordpress.org/plugins/pods/)
- **SiteOrigin Page Builder** plugin (required)
- **SiteOrigin Widgets Bundle** (optional, only needed if you also want SiteOrigin's
  own widgets alongside the two custom ones this theme adds)

The admin dashboard will show a notice if either required plugin is missing/inactive.

## 2. Install

1. Zip the `kavisamrat-theme` folder (if not already zipped) and upload via
   **Appearance → Themes → Add New → Upload Theme**, or drop the folder into
   `/wp-content/themes/`.
2. Activate the theme.
3. Activate **Pods** and **SiteOrigin Page Builder** if you haven't already.
4. Reload `wp-admin` once — this triggers `inc/register-book-pod.php`, which
   programmatically creates the **Books** CPT and every custom field described
   in the spec (Book Title, Thumbnail, Images, Highlights, Description,
   Where to Buy, Media Links, Characters, Publication Year, Genre).
   You should immediately see a new **Books** item in the left-hand admin menu.

   *(This code-based registration is the authoritative path. `/pods-export/book-pod-export.json`
   is provided as an alternative if you'd rather import through Pods Admin →
   Import/Export Tools — but Pods' export schema can vary slightly by version,
   so the code-based registration is what's tested and guaranteed to work.)*

5. Go to **Settings → Reading** and set a static page as your homepage (or just
   let `front-page.php` render — it auto-detects whether that page has a saved
   SiteOrigin layout and falls back to a fully-built default layout if not).

## 3. Add your first book

**Books → Add New**. Fill in:
- Title (native WP title) + **Book Title** (Pods field, shown on cards/H1)
- **Book Thumbnail** — vertical cover image
- **Book Description** — full rich-text synopsis
- **Book Highlights** — click "+" to add as many quote rows as you like
- **Where to Buy** — vendor name + URL, repeat per retailer
- **Media Links** — paste a YouTube URL, or a full `<iframe>` embed code, one per row
- **Book Characters** (optional) — name + description, repeat per character
- **Publication Year** — drives chronological ordering on the archive & home page
- **Genre** — drives the archive's filter bar

Publish. It immediately appears on `/books/` (archive) and at its own URL,
fully styled — no Page Builder work required.

## 4. Customize layouts visually (optional)

Every template (`front-page.php`, `single-book.php`) checks for a saved
SiteOrigin Page Builder layout (`panels_data` post meta) and renders that
instead of the PHP default the moment one exists. So:

- To hand-build the Home Page: edit the front page with **Page Builder**,
  and drag in **"Kavisamrat: Book Grid"** and **"Kavisamrat: Book Highlights
  Carousel"** from the widget panel — both pull live Pods data.
- To hand-build a specific book's page: edit that book with **Page Builder**
  (the CPT already supports it).
- `siteorigin-layouts/*.json` contain best-effort starter layouts you can try
  importing via Page Builder's Import/Export screen. SiteOrigin's export
  format can shift between versions, so if an import doesn't take cleanly,
  just build the same rows live in the editor — it's quick with the two
  custom widgets doing the heavy lifting.

## 5. What's inside

```
kavisamrat-theme/
├── style.css                        WP theme header (required file)
├── functions.php                    theme setup, enqueues, plugin checks
├── header.php / footer.php
├── front-page.php                   Home page (Hero, Featured Books, Bio, Media)
├── archive-book.php                 Books archive (chronological grid + filters)
├── single-book.php                  Single book (hero, highlights, desc, chars, media, gallery)
├── inc/
│   ├── register-book-pod.php        authoritative Pods CPT + field registration
│   ├── helpers.php                  safe field getters + render helpers
│   └── widgets/
│       ├── class-so-book-grid-widget.php
│       └── class-so-book-highlights-widget.php
├── template-parts/
│   ├── book-card.php
│   └── widgets/book-grid.php
├── assets/
│   ├── css/kavisamrat-design-system.css   full design system (colors/type/components)
│   ├── css/book.css                       book-specific supplemental styles
│   └── js/kavisamrat-animations.js        GSAP scroll-reveal, tilt, carousel
├── pods-export/book-pod-export.json       alternative Pods import file
└── siteorigin-layouts/*.json              starter Page Builder layouts
```

## 6. Design tokens (from your brand guidelines)

| Token | Hex | Use |
|---|---|---|
| Ekavira Ivory | `#F9F6F0` | Page background |
| Dharmarao Espresso | `#2B1D14` | Text, inverse sections, footer |
| Subbannapeta Stone | `#E8E3D9` | Alt section background |
| Veyi Padagalu Brass | `#C89B3C` | Primary buttons, accents |
| Kinnerasani Emerald | `#2A623D` | Secondary buttons, stat cards |
| Kamalam Rose | `#C85875` | Highlight buttons |
| Sankha Silver | `#8C8DA8` | Logo accent |

Headings: **Tiro Telugu** (serif). Body: **Noto Sans Telugu**. All corners are
sharp (0px radius) — this is a deliberate part of the system, not an oversight.

## 7. Motion

`assets/js/kavisamrat-animations.js` loads GSAP + ScrollTrigger from cdnjs and:
- staggers grid/card entrances on scroll,
- fades/slides the hero in on load,
- powers the swipeable Book Highlights carousel,
- adds a subtle 3D tilt to book covers on hover.

Everything respects `prefers-reduced-motion` and degrades to an
IntersectionObserver-only reveal if GSAP fails to load (e.g. blocked CDN).
