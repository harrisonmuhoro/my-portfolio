# Phase 1D — Final Regression Test Corrections

**Repository:** `harrisonmuhoro/my-portfolio` · **Branch:** `main`
**Baseline:** `a17a313` · **Scope:** test-quality corrections only — no application code changed.

---

## 1. Defects found

| # | Defect | Evidence | Fix |
|---|--------|----------|-----|
| F1 | Sitemap test silently skipped `<url>` nodes lacking `<loc>` — entries were only recorded `if ($loc)` | `tests/run_tests.php` sitemap block | Extracted validation into `seo_validate_sitemap()` which iterates **every** `//sm:url` node and requires exactly one `<loc>` and one `<lastmod>` per node; missing/duplicate elements are errors. Added a regression case: a synthetic sitemap with a `<url>` missing `<loc>` is asserted to be rejected. |
| F2 | Cookie test only checked `array_key_exists('secure')` — never verified the flag's value per scheme | `tests/run_tests.php` session section | `page_probe.php` now accepts a spec `https` flag driving `$_SERVER['HTTPS']`. Both `/contact` and `/it-support` are probed under HTTPS (`secure === true`) and HTTP (`secure === false`); HttpOnly, SameSite=Lax, empty domain, `path=/`, lifetime=0 asserted on both. |

## 2. Files changed

- `tests/run_tests.php` — `seo_validate_sitemap()` function; sitemap assertions now consume its result; synthetic bad-sitemap regression test; HTTPS/HTTP cookie assertions.
- `tests/page_probe.php` — `$_SERVER['HTTPS']` populated from spec (`https` key).

No application code, SEO strategy, identity data or certificate claims were modified.

## 3. Test results

`php tests/run_tests.php` → **110 passed, 0 failed, 1 skipped**
(skip: symlink regression cases — `symlink()` unavailable on Windows; they execute on Linux CI)

All prior coverage preserved: i18n dictionary, rate limiter + failure policy, mailer, both form handlers (CSRF, honeypot, validation, rate limit, provider failure), full CSRF rotation lifecycle in persisted sessions, SEO metadata/JSON-LD/hreflang/sitemap, session gating per route, robots.txt.

`php -l` clean on both modified files.

## 4. Remaining production-only tasks

- ~~Apex→www redirect~~ — **RESOLVED.** Owner-provided live PowerShell verification (26 November 2026) confirms the hosting layer now returns **HTTP 308 Permanent Redirect**: `https://harrisonmuhoro.me/` → `https://www.harrisonmuhoro.me/` (308), `https://www.harrisonmuhoro.me/` → 200, with paths (`/about`, `/services`) and query strings (`/contact?source=test`) preserved. Earlier Phase 1C observation of a 307 was point-in-time and is superseded.
- Certification claims — owner verification pending (unchanged by design).
- Search Console resubmission/monitoring post-deploy.
