# Phase 1 — Personal Identity SEO & Structured Data Implementation

**Repository:** `harrisonmuhoro/my-portfolio` · **Branch:** `main`
**Scope:** Phase 1 of the approved SEO programme — canonical Person entity, unique page metadata, ProfilePage schema, multilingual canonicals/hreflang, sitemap alignment, automated SEO tests.

---

## 1. Changes implemented

| Area | Change |
|------|--------|
| Person entity | One canonical Person node with stable `@id`, emitted site-wide from `includes/seo.php` |
| `sameAs` | Restricted to approved identity profiles only (GitHub, LinkedIn); email/WhatsApp excluded |
| Page metadata | Unique `<title>` + meta description on all six public routes |
| `/about` | `ProfilePage` JSON-LD whose `mainEntity` references `PERSON_ID` |
| `/it-support` | Language-specific canonicals + reciprocal `hreflang` (`en`, `sw`, `x-default`) |
| `Service.provider` | Now `{"@id": PERSON_ID}` instead of an inline duplicate Person |
| Sitemap | Both language URLs listed; image `loc`s switched to the `.webp` variants the site serves |
| Tests | `tests/page_probe.php` harness + 20 SEO assertions in `tests/run_tests.php` |

## 2. Files modified

- `config/config.php` — added `PERSON_ID`, `PERSON_IMAGE`, `PERSON_SAME_AS` constants (built from `SITE_URL` and approved `SOCIAL_LINKS` entries)
- `includes/seo.php` — **new**: `seo_person_schema()` (canonical Person array) + `seo_jsonld()` (HEX-flagged JSON output)
- `includes/header.php` — requires `seo.php`, emits Person via `seo_jsonld()`, language-aware canonical/hreflang block
- `pages/home-redesign.php`, `about.php`, `services.php`, `projects.php`, `contact.php` — page-specific `$page_title`/`$page_description`
- `pages/about.php` — ProfilePage JSON-LD
- `pages/it-support.php` — `Service.provider` → `@id` reference
- `sitemap.xml.php` — `?lang=en`/`?lang=sw` entries, `.webp` image locations
- `tests/page_probe.php` — **new**: subprocess renderer for real pages (mirrors `handler_probe.php` spec pattern)
- `tests/run_tests.php` — SEO section (20 assertions)

## 3. Person entity architecture

`includes/seo.php` is the single source of truth. `seo_person_schema()` returns the full Person array; `seo_jsonld()` prints it with `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT` so apostrophes, Kiswahili text and `</script>` sequences cannot break markup. `header.php` emits it once per page; other schema types reference it by `@id` rather than re-declaring fields. Identity updates (name, image, profiles) now happen in `config.php`/`seo.php` only.

## 4. ProfilePage implementation

`/about` emits a second JSON-LD block: `ProfilePage` with `url = SITE_URL . '/about'`, `name`, and `mainEntity => {'@id': PERSON_ID}`. The Person itself is not duplicated — it is referenced by identifier. No Knowledge Panel outcome is implied.

## 5. Metadata changes

Every public route now sets `$page_title`/`$page_description` before `header.php`, which falls back to site defaults only when unset. OG/Twitter tags automatically inherit the per-page values (same variables). Verified rendered titles include e.g. `About Harrison Muhoro – Software Developer in Kenya` and `Contact Harrison Muhoro – Software Developer in Kenya`.

## 6. Language indexing implementation

On pages where `IT_I18N_ACTIVE` is defined (it-support), `header.php` builds `$hreflang_urls` for `en`, `sw` and `x-default` pointing at `SITE_URL + path + ?lang=xx`, sets `$page_canonical` to the active language's URL, and prints three `<link rel="alternate" hreflang>` tags. Both variants are listed in the sitemap, so neither canonical is a duplicate. `html lang`/`og:locale` were already language-aware and remain so.

## 7. Sitemap changes

- Two `/it-support` entries (`?lang=en`, `?lang=sw`) replace the single ambiguous entry — no bare duplicate.
- Image locations updated to `og-image.webp` and `harrison2.webp`, matching what pages/OG actually serve (F8 consistency).
- `lastmod` remains page-file `mtime` (coarse but real modification dates).

## 8. Test results

`php tests/run_tests.php` → **83 passed, 0 failed** (63 pre-existing + 20 new SEO assertions). New coverage:

- Unique non-empty `<title>` and meta description across all 7 rendered variants (6 routes + SW it-support)
- Canonical correctness per route; language-specific canonicals for `?lang=en`/`?lang=sw`
- Reciprocal `hreflang` (`en`, `sw`, `x-default`) present on the Kiswahili page
- Distinct EN/SW titles
- Exactly one Person with `@id = https://www.harrisonmuhoro.me/#person`; `sameAs` equals exactly `[github, linkedin]`; no `mailto:`/`wa.me`
- ProfilePage `mainEntity.@id` matches `PERSON_ID`; `url` ends `/about`
- IT Support `Service.provider.@id` matches; still exactly one Person node site-wide
- All emitted JSON-LD blocks parse as valid JSON
- Sitemap contains both language URLs, no bare `/it-support` duplicate, and every `image:loc` maps to an existing file

`php -l` clean on all modified files. CSRF/session/rate-limit/mail tests all still pass.

## 9. Known limitations

- The edge/CDN may still issue **307** for apex→www before PHP runs (F3) — application sends 301; fixing the edge is a host-config task, not code.
- `session_start()` remains unconditional (F6) — lazy session startup is a separate phase.
- Certification claims preserved verbatim (owner decision); external verification still pending (F7).
- Two `robots.txt` sources still exist (F9) — unchanged this phase.
- `lastmod` uses file `mtime`, which updates on any edit including non-content changes.
- `hreflang`/`canonical` URLs use `?lang=` query form — stable and crawlable as implemented; path-based locales would be a larger routing change deferred by design.

## 10. Required production verification

After deploy, verify with `curl`:

1. `https://www.harrisonmuhoro.me/` — unique title, Person JSON-LD with `@id`, `sameAs` = GitHub + LinkedIn only.
2. `/about` — ProfilePage block present, `mainEntity.@id` matches.
3. `/it-support?lang=en` and `?lang=sw` — self-referencing canonicals, three reciprocal `hreflang` links, translated `<title>`.
4. `/sitemap.xml` — both language URLs, `.webp` image locs return 200.
5. Google Rich Results Test on `/` and `/about` for Person/ProfilePage validity.
6. Search Console: resubmit sitemap; monitor which canonical is selected for `/it-support` variants.
