# Phase 1B — SEO Implementation Verification & Regression Hardening

**Repository:** `harrisonmuhoro/my-portfolio` · **Branch:** `main`
**Baseline:** `4aaad39` (Phase 1 implementation) · **Method:** static review + rendered-page probes via `tests/page_probe.php` + DOMDocument XML parsing. Local test results only — production state is **not** established by this document.

---

## 1. Findings

| # | Area | Result |
|---|------|--------|
| 1 | Sitemap XML validity | **Defect in test, not in output.** Phase 1 tests checked substrings only. Sitemap itself is well-formed; replaced string checks with DOMDocument/DOMXPath validation (namespaces, uniqueness, host, lastmod format, image existence). |
| 2 | Language persistence | **Verified correct.** `it_lang()` (`includes/i18n.php:25-40`) writes a validated `?lang=` into the session *before* reading it, so an explicit URL parameter always wins over stale session state. Invalid codes are ignored and never stored. Six probe scenarios now asserted (see §3). |
| 3 | About identity consistency | **Consistent.** Person `description` = `SITE_DESCRIPTION` ("software engineer in Kenya … healthcare, operations, and business teams"); About bio describes a "software developer from Kenya" building solutions for "businesses and organizations … healthcare and hospitality … business operations". Same positioning, no contradiction. |
| 4 | Job title accuracy | `PRIMARY_TITLE = 'Software Engineer'` is rendered on the homepage, `/about` (`about.php:27`) and inside Person JSON-LD. LinkedIn headline consistency **cannot be verified from the repository** — owner confirmation required (see §6). |
| 5 | Production indexing | **Pending — out of scope for code.** Local/CI green does not prove deployment or crawling. Commands in §5. |
| — | OG/Twitter parity | **Test defect found and fixed.** Phase 1B's new parity assertions initially failed because the test regex expected single spaces between attributes while `header.php` emits aligned multi-space attributes. The emitted HTML was correct; regexes updated to `\s+`. |

## 2. Fixes made

- `tests/run_tests.php` — sitemap assertions rewritten to use `DOMDocument`/`DOMXPath` (well-formedness, `sitemap` + `image` namespaces, canonical host, duplicate `<loc>` detection, `YYYY-MM-DD` lastmod, per-image file existence).
- `tests/run_tests.php` — 6 language-isolation probes added (explicit `?lang` vs seeded session, fresh sessions, persistence, invalid codes).
- `tests/run_tests.php` — OG/Twitter parity assertion across all 7 rendered variants (title, description, URL on `og:*` and `twitter:*`).
- `tests/run_tests.php` — Person `@id`/name consistency asserted across every page that emits the node.
- No application code changed — all defects were in test coverage, not product behavior.

## 3. Language isolation — verified behavior

| Scenario | Expected | Result |
|----------|----------|--------|
| session=`sw`, request `?lang=en` | English | ✅ `lang="en-KE"`, EN title, canonical `?lang=en` |
| session=`en`, request `?lang=sw` | Kiswahili | ✅ `lang="sw-KE"`, SW title, canonical `?lang=sw` |
| no session, `?lang=en` / `?lang=sw` | requested language | ✅ |
| session=`sw`, no `?lang` | Kiswahili persists | ✅ |
| `?lang=fr` (invalid) + session | session fallback | ✅ invalid code ignored |

`html lang`, `<title>`, meta description, canonical and hreflang annotations all track the resolved language.

## 4. Test results

`php tests/run_tests.php` → **94 passed, 0 failed, 1 skipped** (symlink cases — `symlink()` unavailable on Windows; they execute on Linux CI).

SEO section grew from 20 → 34 assertions. All pre-existing CSRF, rate-limit, mailer and handler tests unchanged and passing.

## 5. Production verification commands

Run these against `https://www.harrisonmuhoro.me` after deploy — **these verify production, which this document does not**:

```bash
# Page status codes
for p in / /about /services /projects /contact "/it-support?lang=en" "/it-support?lang=sw" /sitemap.xml /robots.txt; do
  printf '%-28s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' "https://www.harrisonmuhoro.me$p")"
done

# Canonical host redirects (expect final 200 on https://www...)
curl -sI http://harrisonmuhoro.me/ | head -1     # expect 301/308
curl -sI https://harrisonmuhoro.me/ | head -1    # edge-issued 307 known issue (F3)
curl -sI https://www.harrisonmuhoro.me/about/ | head -1  # app 301 → /about

# Structured data spot checks
curl -s https://www.harrisonmuhoro.me/ | grep -o '"@id":"[^"]*"'
curl -s https://www.harrisonmuhoro.me/about | grep -o 'ProfilePage'
curl -s "https://www.harrisonmuhoro.me/it-support?lang=sw" | grep -oE 'hreflang="[^"]+"|rel="canonical"[^>]*'

# Sitemap + robots
curl -s https://www.harrisonmuhoro.me/sitemap.xml | head -5
curl -s https://www.harrisonmuhoro.me/robots.txt
```

JSON-LD validity: paste page source blocks into https://validator.schema.org/ or Google Rich Results Test.

## 6. Outstanding risks / owner actions

- **F3** apex→www redirect — *superseded:* owner-verified HTTP **308** on 26 Nov 2026 (see `SEO_PHASE_1D_VERIFICATION.md` §4). Was edge-issued 307 at the time of this audit; app code sends 301.
- **F7** certification claims preserved verbatim per owner decision; external verification pending.
- **LinkedIn headline** should be checked by the owner to confirm it supports "Software Engineer" positioning (`linkedin.com/in/zion-harrison`).
- Production deployment/crawl state cannot be confirmed from the repository — verify per §5 and Search Console.

### Follow-up (post-1B): F6 + F9 resolved

- **F6 lazy sessions:** `index.php` now starts a session only for `/contact` and `/it-support` (the routes that need CSRF/language state). All session starts go through `app_session_start()` in `config.php`, which applies the hardened cookie params (HttpOnly, SameSite=Lax, Secure-on-HTTPS) — this also fixes a latent gap where `backend/*.php` handlers and `i18n.php` previously called plain `session_start()` with default cookie params. Router-mode probes assert `session_status() === PHP_SESSION_NONE` on `/`, `/about`, `/services`, `/projects`, `/sitemap.xml`, `/robots.txt` and 404s. **Production check after deploy:** `curl -sI https://www.harrisonmuhoro.me/ | grep -i "set-cookie"` should return nothing.
- **F9 robots.txt:** the static `robots.txt` was deleted; `/robots.txt` now routes to `robots.txt.php` (same mechanism as `/sitemap.xml`, already proven in production). Single source of truth, `SITE_URL`-aware sitemap reference. Tests assert content and that no static file shadows the route.
