# Harrison Muhoro — Portfolio

A production portfolio for a software engineer based in Nyeri, Kenya. Built as a
lightweight, dependency-free PHP application with a hand-rolled design system,
dark/light theming, and a recruiter-focused information architecture (hero →
case studies → stack → currently building → certifications → about → contact).

The design goal is a polished **product-engineer** aesthetic (inspired by Linear,
Vercel, Stripe, Raycast) rather than a flashy developer template.

---

## Tech stack

- **PHP** (8.0+) — no framework, no Composer dependencies
- **Vanilla CSS** — custom design-token system, no build step
- **Vanilla JS** — `IntersectionObserver` reveals, theme toggle, mobile nav
- **Apache** (`.htaccess`) — clean-URL rewriting, security headers, caching
- Served locally via **WAMP**

There is no bundler, package manager, or transpile step. Files are served as-is.

---

## Requirements

- PHP 8.0 or newer with the **GD** extension (used to generate social/icon assets)
- Apache with **mod_rewrite** enabled (for clean URLs)
- WAMP/XAMPP/MAMP or any PHP-capable web server

---

## Running locally (WAMP)

1. Place the project in your web root, e.g. `D:\wamp\www\harrison-portfolio`.
2. Ensure Apache's **mod_rewrite** module is enabled.
3. Start WAMP and visit:

   ```
   http://localhost/harrison-portfolio/
   ```

The base path is auto-detected from the script location, so the site works from
a subdirectory (`/harrison-portfolio`) or from a domain root without config
changes.

### Quick static check

```bash
php -l index.php          # lint the router
php -l pages/home-redesign.php
```

---

## Project structure

```
harrison-portfolio/
├── index.php                 # Front controller / router + session + security setup
├── .htaccess                 # Clean URLs, security headers, caching, compression
├── sitemap.xml.php           # Dynamic sitemap
│
├── config/
│   ├── config.php            # ALL constants: identity, contact, SEO, social links, CV path
│   └── security_headers.php  # CSP and security response headers
│
├── includes/
│   ├── header.php            # <head>: SEO, Open Graph, JSON-LD, fonts, theme boot script
│   ├── navigation.php        # Sticky navbar + mobile drawer + theme toggle
│   └── footer.php            # Footer + closing scripts
│
├── pages/
│   ├── home-redesign.php     # ★ Live homepage (routed at "/")
│   ├── about.php
│   ├── projects.php          # Full project archive
│   ├── services.php
│   ├── contact.php           # Contact form (CSRF-protected)
│   ├── 404.php
│   └── index.php             # Safety redirect (NOT used by router)
│
├── backend/
│   └── contact_handler.php   # Processes contact form submissions
│
└── assets/
    ├── css/                  # global.css (design tokens) + one file per page/component
    ├── js/                   # global.js, navigation.js, + per-page scripts
    ├── images/               # harrison2.jpg, og-image.jpg, apple-touch-icon.png
    ├── icons/                # favicon.svg
    └── docs/                 # harrison-muhoro-cv.pdf (see note below)
```

---

## Routing

`index.php` at the project root is the single entry point. It maps paths to page
files:

| Path        | File                        |
|-------------|-----------------------------|
| `/`         | `pages/home-redesign.php`   |
| `/about`    | `pages/about.php`           |
| `/services` | `pages/services.php`        |
| `/projects` | `pages/projects.php`        |
| `/contact`  | `pages/contact.php`         |
| *(other)*   | `pages/404.php` (HTTP 404)  |

> **Note:** `pages/home-redesign.php` is the live homepage. A `pages/index.php`
> exists only as a redirect fallback and is never hit by the router.

---

## Configuration

All site-wide values live in **`config/config.php`** — no hardcoded strings in
pages. Common things you'll edit:

- **Identity** — `OWNER_NAME`, `PRIMARY_TITLE`, `SECONDARY_TITLE`, `LOCATION`
- **Contact** — `EMAIL_ADDRESS`, `PHONE_PRIMARY`, `WHATSAPP_NUMBER`
- **Social** — `SOCIAL_LINKS` (github, linkedin, whatsapp, email)
- **SEO** — `SITE_DESCRIPTION`, `SEO_KEYWORDS`, `OG_IMAGE`
- **CV** — `CV_PATH`

Environment (`development` vs `production`) is auto-detected from the host, which
toggles error display.

---

## Design system

Design tokens are defined in **`assets/css/global.css`** and consumed everywhere.

**Color palette**

| Token         | Dark (default) | Purpose        |
|---------------|----------------|----------------|
| `--background`| `#0A1628`      | Page background|
| `--surface`   | `#1E3A5F`      | Cards, panels  |
| `--primary`   | `#2D6BE4`      | Primary accent |
| `--secondary` | `#00A86B`      | Secondary accent|
| `--text`      | `#F0F4FC`      | Primary text   |

**Typography** — Space Grotesk (headings), Inter (body), JetBrains Mono (code),
loaded from Google Fonts.

**Spacing** — 8-point grid via `--space-*` tokens. Radii, shadows, and
transitions are tokenized too.

### Theming

Dark/light mode with:
- System-preference detection + `localStorage` persistence
- A boot script in `header.php` that sets the theme **before paint** (no flash)
- A toggle in the navbar; light values live under `[data-theme='light']`

---

## Accessibility & performance

- Semantic HTML, skip link, ARIA labels, visible `:focus-visible` states
- `prefers-reduced-motion` disables reveal animations and transitions
- WCAG-AA-oriented contrast on the token palette
- SEO: per-page meta, Open Graph, Twitter cards, JSON-LD `Person` schema, sitemap
- No JS framework; scripts are deferred; fonts use `display=swap`

---

## Regenerating brand assets (optional)

`assets/images/og-image.jpg` (1200×630 social card) and `apple-touch-icon.png`
(180×180) were generated with PHP's **GD** extension from the real photo and the
brand palette. If you change the photo, name, or colors and want to regenerate
them, recreate a short GD script using those values — GD must be enabled in PHP.

---

## Replace the placeholder CV

`assets/docs/harrison-muhoro-cv.pdf` is an **auto-generated placeholder** built
only from public site data (contact, skills, selected projects) — it contains no
fabricated employment history. Replace it with your real résumé using the **same
filename** so the "Download CV" button serves your document. The path is set by
`CV_PATH` in `config/config.php`.

---

## Editable content notes

- **Certifications** (homepage `#certifications`) currently use placeholder
  credentials — edit the `$certifications` array in `pages/home-redesign.php`.
- **Currently Building** (homepage `#building`) — edit the `$currently_building`
  array in the same file.
- **Case studies / projects** — arrays at the top of `pages/home-redesign.php`
  and `pages/projects.php`.

---

## License

© Harrison Muhoro. All rights reserved.
