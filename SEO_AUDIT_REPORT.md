# SEO Audit Report — Harrison Muhoro Portfolio (Phase 0)

**Repository:** `harrisonmuhoro/my-portfolio` · **Branch:** `main` · **Audited commit:** `77500c2` (+ working-tree state at audit time)
**Method:** static inspection of the repository plus live HTTP checks against `https://www.harrisonmuhoro.me` (all production observations below were actually executed via `curl`; nothing is assumed).

> **Phase 1 status update:** findings **F1, F2, F4, F5, F8, F10 are resolved** in code (see `docs/SEO_PHASE_1_IMPLEMENTATION.md`; automated assertions in `tests/run_tests.php`). **Phase 1B verification** (`docs/SEO_PHASE_1B_VERIFICATION.md`) hardened the suite to 94 assertions — DOMDocument sitemap validation, language-session isolation, OG/Twitter parity, cross-page Person consistency — with no application-code defects found. **F3** (edge 307 redirect) remains a hosting-config issue; **F6** (unconditional session), **F7** (credential verification) and **F9** (duplicate robots.txt sources) remain open.

---

## 1. Executive summary

The site has a solid technical SEO foundation: per-request canonicals on the production host, Open Graph/Twitter parity, XML sitemap with image extension, robots.txt, JSON-LD, HSTS/CSP/security headers, and a clean 301 canonical-host redirect in application code.

The audit confirmed **two high-severity defects**: five public pages share an identical `<title>` and meta description (only `/it-support` and the 404 page override the defaults), and the Person JSON-LD `sameAs` array contains contact channels (`mailto:`, `wa.me`) rather than only public identity profiles, with no stable `@id`. Production verification also confirmed the reported **HTTP 307 apex→www redirect is real but is issued by the hosting edge, not the application** (PHP sends 301; the edge intercepts first). The Kiswahili variant of `/it-support` canonicalizes to the English URL with no `<link rel="alternate" hreflang>` annotations, so the Swahili version is unlikely to be indexed independently.

No application code was modified for this audit.

---

## 2. Findings

| ID | Severity | Finding | Evidence | File | Recommended action | Verification |
|----|----------|---------|----------|------|--------------------|--------------|
| F1 | **High** ✅ **RESOLVED (Phase 1)** | Five public pages emit identical title and description | `header.php:9-10` defaults apply when pages don't override; only `it-support.php:7-9` and `404.php:3-4` set `$page_title`/`$page_description`. `/`, `/about`, `/services`, `/projects`, `/contact` all inherit `Harrison Muhoro — Software Engineer` + `SITE_DESCRIPTION` | `includes/header.php:9-10`; `pages/home-redesign.php:2`, `about.php:3`, `services.php:3`, `projects.php:4`, `contact.php:2` | Give each page a unique title/description (suggested set in §3) | Re-fetch each route, diff `<title>`/`description` |
| F2 | **High** ✅ **RESOLVED (Phase 1)** | `sameAs` includes non-profile contact channels; no stable `@id` | `header.php:109` emits `array_values(SOCIAL_LINKS)`; `config.php:85-91` includes `whatsapp => wa.me/...` and `email => mailto:...` — contact endpoints, not identity profiles | `includes/header.php:94-112`; `config/config.php:85-91` | Restrict `sameAs` to verified public profiles (GitHub, LinkedIn, Facebook); add `"@id": "https://www.harrisonmuhoro.me/#person"` | Validate JSON-LD (Rich Results Test / schema.org validator) |
| F3 | **High** | Apex→www redirect observed as HTTP 307 in production | Live: `https://harrisonmuhoro.me/about` → `307 → https://www.harrisonmuhoro.me/about`. App code sends **301** (`index.php:18`), so the 307 is issued by the hosting edge before PHP runs | `index.php:15-20`; production curl | Configure the edge/host to emit 301/308 for apex→www, or accept 307 (weaker equity consolidation) | `curl -sI https://harrisonmuhoro.me/` |
| F4 | **Medium** ✅ **RESOLVED (Phase 1)** | Kiswahili `/it-support` shares the English canonical; no real hreflang | Canonical strips query strings (`header.php:20-25`); verified live: `?lang=sw` → `<link rel="canonical" href="https://www.harrisonmuhoro.me/it-support">`. `hreflang` appears only on switcher anchors (`it-support.php:53,55`), not `<link rel="alternate">` | `includes/header.php:20-25,44`; `pages/it-support.php:53-55` | Pick a strategy (§6): either canonicalize both to EN and keep SW as UX-only, or implement self-referencing canonicals + `x-default` hreflang | Re-fetch both `?lang` variants, inspect canonical + alternate links |
| F5 | **Medium** ✅ **RESOLVED (Phase 1)** | No `ProfilePage` structured data on `/about` | No `ProfilePage`/`profile` markup found; page has name H1, photo (`about.php:50-53`), bio sections — qualifies for the type | `pages/about.php` | Add `ProfilePage` JSON-LD referencing the Person `@id` once F2 lands | JSON-LD validation |
| F6 | **Medium** | Session cookie + `no-store` on every public page | `index.php:26-37` calls `session_start()` unconditionally → live homepage returns `set-cookie: PHPSESSID=...` and `cache-control: no-store, no-cache, must-revalidate` (the no-store header is **not** emitted by app code — grep finds none; it comes from the hosting layer). Public pages are therefore uncacheable at the edge | `index.php:26-37`; live `curl -sI https://www.harrisonmuhoro.me/` | Start sessions lazily (only pages/handlers needing CSRF); review host cache headers for anonymous GETs | `curl -sI` before/after; check `Set-Cookie` absence on `/`, `/about` |
| F7 | **Medium** | Certification claims unverified | `home-redesign.php:50-53` lists two completed freeCodeCamp certs (2023, 2024) and `AZ-900 — In progress`. No verification URLs emitted | `pages/home-redesign.php:50-53,166-` | Owner confirms credentials; add issuer verification links or mark appropriately | Owner confirmation + live page check |
| F8 | **Low** ✅ **RESOLVED (Phase 1)** | Sitemap image entries use `.jpg` while pages/OG use `.webp` | `sitemap.xml.php:39,52` reference `og-image.jpg`/`harrison2.jpg`; pages use `.webp` (`about.php:51`, `home-redesign.php:74`); `OG_IMAGE` config is `.webp` (`config.php:113`). **All four files exist** in `assets/images/` — links are not broken, but the variants are inconsistent | `sitemap.xml.php:37-56`; `assets/images/` | Pick one canonical variant per asset (WebP preferred) and align sitemap + OG | File listing + fetch each URL |
| F9 | **Low** | Duplicate `robots.txt` sources | Dynamic `robots.txt.php` (routed at `/robots.txt`) and static `robots.txt` both exist with equivalent content; which one Apache serves depends on resolution order. Neither disallows `/storage/` | `robots.txt.php`; `robots.txt`; `index.php:63` | Keep one source of truth; consider `Disallow: /storage/` | `curl https://www.harrisonmuhoro.me/robots.txt` and compare |
| F10 | **Low** ✅ **RESOLVED (Phase 1)** | Duplicate Person entities per page; `Service.provider` is a second Person | `header.php` emits Person JSON-LD on every route; `it-support.php:299-303` nests another `Person` with no `@id` — search engines see unrelated Person nodes | `includes/header.php:94-112`; `pages/it-support.php:293-312` | Resolve together with F2 via shared `@id` | JSON-LD inspection per route |

---

## 3. Metadata audit — per-page detail

`header.php:9-10` defaults: title `Harrison Muhoro — Software Engineer`; description `SITE_DESCRIPTION` (`config.php:61`). Canonical = `SITE_URL` + path with query stripped (`header.php:20-25`); `og:url`/`twitter:url` reuse it.

| Route | Page file | Own title? | Own description? | Effective title (all non-overriding pages identical) |
|-------|-----------|-----------|-------------------|-------------------------------------------------------|
| `/` | `pages/home-redesign.php` | No | No | `Harrison Muhoro — Software Engineer` |
| `/about` | `pages/about.php` | No | No | same default |
| `/services` | `pages/services.php` | No | No | same default |
| `/projects` | `pages/projects.php` | No | No | same default |
| `/contact` | `pages/contact.php` | No | No | same default |
| `/it-support` | `pages/it-support.php:7-9` | Yes (`t('meta_title')`) | Yes (`t('meta_desc')`) | EN: `IT Support & Networking Services in Nyeri, Kenya \| Harrison Muhoro`; SW variant verified live |
| `/sitemap.xml`, `/robots.txt` | dynamic endpoints | n/a | n/a | non-HTML |
| 404 | `pages/404.php:3-4` | Yes | Yes | `404 — Page Not Found \| Harrison Muhoro` |

**Verified live:** `https://www.harrisonmuhoro.me/it-support?lang=sw` returns `<title>Hudama za Msaada wa IT na Mtandao Nyeri, Kenya | Harrison Muhoro</title>` — i18n metadata works; the defect is duplication on the five default pages.

Suggested unique metadata (for Phase 1, not applied):

| Page | Title | Description basis |
|------|-------|-------------------|
| `/` | Harrison Muhoro – Software Engineer in Kenya | existing `SITE_DESCRIPTION` is acceptable here |
| `/about` | About Harrison Muhoro – Software Developer | bio/experience summary |
| `/projects` | Software Development Projects – Harrison Muhoro | portfolio summary |
| `/services` | Web Development Services – Harrison Muhoro | service list |
| `/contact` | Contact Harrison Muhoro – Kenya | contact intent |
| `/it-support` | already unique | already unique |

---

## 4. Structured data audit

Current Person node (`header.php:94-112`), emitted on **every** route:

```json
{ "@context": "https://schema.org", "@type": "Person",
  "name": "Harrison Muhoro", "jobTitle": "Software Engineer",
  "description": "...", "url": "https://www.harrisonmuhoro.me",
  "email": "zionh191@gmail.com", "telephone": "+254 726 300 091",
  "address": {"@type":"PostalAddress","addressLocality":"Nyeri","addressCountry":"KE"},
  "sameAs": [github, linkedin, facebook, wa.me link, mailto: link],
  "knowsAbout": [...] }
```

- **`sameAs` defect:** `array_values(SOCIAL_LINKS)` (`config.php:85-91`) yields 5 URLs; `whatsapp` (`https://wa.me/254726300091?text=...`) and `email` (`mailto:...`) are contact channels, not profiles that assert identity. Schema.org guidance: `sameAs` should reference pages about the same person/entity.
- **No `@id`:** the Person node has no stable identifier, so it cannot be referenced from other entities (e.g., `Service.provider`, a future `ProfilePage`).
- **Duplicate entities:** the identical Person block is emitted site-wide; `/it-support` adds a `Service` node whose `provider` is a *second*, disconnected Person (`it-support.php:299-303`).
- **`ProfilePage`:** absent everywhere. `/about` contains a name H1, portrait (`about.php:50-53` uses `harrison2.webp`), bio, tools and current-work sections — it qualifies for `ProfilePage` markup once the Person `@id` exists.

---

## 5. Sitemap & image verification

`sitemap.xml.php` emits 6 URLs (`/`, `/about`, `/projects`, `/services`, `/it-support`, `/contact`) on `SITE_URL` → production `https://www.harrisonmuhoro.me` ✓. `lastmod` = page file `mtime` (changes on any edit — acceptable but coarse). `robots.txt` references the sitemap ✓.

**Image assets — verified on disk:**

| File | Exists | Referenced by |
|------|--------|---------------|
| `assets/images/og-image.jpg` | ✅ | sitemap `/` image entry |
| `assets/images/og-image.webp` | ✅ | `OG_IMAGE` config, `og:image` meta |
| `assets/images/harrison2.jpg` | ✅ | sitemap `/about` image entry |
| `assets/images/harrison2.webp` | ✅ | `about.php:51`, `home-redesign.php:74` |

**Conclusion for Finding D:** no broken image URLs — both variants exist. Remaining issue is consistency only (F8): sitemap advertises `.jpg` while the site serves `.webp`.

---

## 6. Redirect & canonical-host verification (live)

| Request | Observed | Issued by |
|---------|----------|-----------|
| `http://harrisonmuhoro.me` | `308 → https://harrisonmuhoro.me/` | edge/host |
| `https://harrisonmuhoro.me` | `307 → https://www.harrisonmuhoro.me/` | **edge/host (before PHP)** |
| `https://harrisonmuhoro.me/about` | `307 → https://www.harrisonmuhoro.me/about` | edge/host |
| `http://www.harrisonmuhoro.me` | `308 → https://www.harrisonmuhoro.me/` | edge/host |
| `https://www.harrisonmuhoro.me/about/` | `301 → https://www.harrisonmuhoro.me/about` | **PHP (`index.php:18`)** |
| `https://www.harrisonmuhoro.me/` | `200` | app |

The earlier "307" observation is confirmed and explained: the apex→www hop is terminated by the hosting layer with **307 Temporary Redirect**, while the app's own canonicalization (trailing-slash, host) correctly uses **301**. Final destination is consistently HTTPS + `www` ✓. A 307 still consolidates users but passes less link equity than 301 — worth fixing at the edge if the host allows.

---

## 7. Language indexing (`/it-support` EN/SW)

- Mechanism: `?lang=en|sw`, strict allowlist, session-persisted (`includes/i18n.php`); `IT_I18N_ACTIVE` gates `html lang` (`sw-KE`/`en-KE`), `og:locale`, and translated nav/footer — verified live.
- Canonicals: identical for both languages (query stripped) → both declare `https://www.harrisonmuhoro.me/it-support`.
- Hreflang: only `hreflang` attributes on the two switcher anchors; **no `<link rel="alternate" hreflang>`** in `<head>`.
- Consequence: Google sees one canonical URL; the Kiswahili version is a UX variant, not an indexable locale page.

**Recommended strategy (not implemented):**
- *Option A (minimal):* declare `/it-support` English-canonical, keep SW as a session preference — acceptable if SW search demand is low.
- *Option B (proper i18n SEO):* give each language a distinct URL (`/it-support` + `/it-support/sw` or keep `?lang=sw`), emit self-referencing canonicals per language, `<link rel="alternate" hreflang="en|sw|x-default">`, and keep `html lang`/`og:locale` in sync (already done). Option B is the technically sound choice if Kiswahili queries matter.

---

## 8. Credentials & identity — owner verification required

| Claim | Location | Status |
|-------|----------|--------|
| Responsive Web Design — freeCodeCamp, 2023 | `home-redesign.php:51` | **Unverified** — no credential URL emitted; confirm certificate exists and is claimable |
| Back End Development and APIs — freeCodeCamp, 2024 | `home-redesign.php:52` | **Unverified** — same |
| Microsoft Azure Fundamentals (AZ-900) — "In progress" | `home-redesign.php:53` | Correctly labelled in-progress; confirm still accurate |
| Social profiles (GitHub/LinkedIn/Facebook) | `config.php:86-88` | URLs exist in markup; ownership/equivalence not verifiable from repo — confirm they are the intended public identities before using in `sameAs` |

Do not add verification links or claims until the owner confirms each credential.

---

## 9. Performance & HTTP headers

**Confirmed in code:**
- Security headers (HSTS preload, CSP, X-Frame-Options, nosniff, Referrer-Policy, Permissions-Policy, COOP/CORP) sent on all responses — `config/security_headers.php`; **verified live** on the homepage.
- `session_start()` runs for every route (`index.php:26-37`) → `Set-Cookie: PHPSESSID` on all pages (verified live).
- `sitemap.xml`/`robots.txt` send public cache headers (`sitemap.xml.php:87`, `robots.txt.php`).

**Observed at runtime (production):**
- Homepage returns `cache-control: no-store, no-cache, must-revalidate` — **not emitted by application code** (no `Cache-Control`/`no-store` string exists in `config/`, `includes/`, `index.php`); attributed to the hosting/edge layer. Combined with the unconditional session cookie, anonymous page views are uncacheable — impacts TTFB under crawl load.

**Recommended:** lazy session start (only where CSRF is needed) + host-level cache rule for anonymous GETs. Verify after change with `curl -sI`.

---

## 10. Testing

Existing `tests/run_tests.php` covered i18n dictionary, rate limiter, mailer, and both form handlers — **no SEO assertions existed at audit time**. **Update (Phase 1):** the matrix below is now partially implemented — `tests/page_probe.php` renders real pages in subprocesses and 20 SEO assertions run in CI (unique titles/descriptions, canonicals, hreflang reciprocity, Person `@id`/`sameAs`, ProfilePage, Service `provider` @id, JSON-LD validity, sitemap URL/image checks). Redirect, robots.txt and lazy-session items remain unimplemented. Recommended matrix for remaining work:

| Test | Method |
|------|--------|
| Unique `<title>` per route | probe each page, extract title, assert distinct |
| Unique meta description per route | same |
| Canonical = `SITE_URL` + path, query stripped | per-route fetch |
| JSON-LD parses + required Person fields + valid `sameAs` hosts | `json_decode` + URL allowlist |
| `@id` present and referenced consistently | string/JSON assertions |
| Sitemap XML well-formed + all `loc` return 200 + image `loc` files exist | DOM parse + HEAD requests |
| robots.txt 200 + references sitemap | fetch |
| Redirect: non-www→www 301/308, trailing-slash 301, http→https | curl matrix (edge-dependent — document expected vs observed) |
| `?lang=sw` → `lang="sw-KE"`, distinct canonical per chosen strategy | fetch both variants |
| No `Set-Cookie` on anonymous public GETs (after lazy-session fix) | header inspection |

None of these were executed as a suite — they are recommendations. Live curl checks in §6/§9 **were** executed.

---

## 11. Production verification gaps

- Edge/CDN redirect codes (307 apex→www) — app code can't control; needs host config review.
- `no-store` cache header origin — hosting layer, not app code.
- Whether Google has already indexed the duplicated titles / which canonical won historically — requires Search Console.
- Actual certificate credentials — owner-only knowledge.
- Whether `config.local.php`/env on the host matches repo assumptions (`BASE_PATH`, `SITE_URL` is hardcoded to the prod domain for non-local hosts — `config.php:64-65`).

---

## 12. Prioritized remediation roadmap

1. **F1** — unique titles/descriptions for `/`, `/about`, `/services`, `/projects`, `/contact`.
2. **F2 + F5 + F10** — Person `@id`, clean `sameAs`, `ProfilePage` on `/about`, wire `Service.provider` to the `@id`.
3. **F6** — lazy session start; verify cache headers end-to-end.
4. **F4** — decide EN/SW indexing strategy; implement canonicals + hreflang accordingly.
5. **F3** — edge redirect to permanent status (host config).
6. **F7** — owner verifies credentials; add verification links or correct claims.
7. **F8/F9** — align image variants; single robots source; consider `/storage/` disallow.
8. Add the §10 test matrix to CI.

---

## 13. Limitations

Read-only audit; no code changed. Production observations are point-in-time curl results. Search-index state, certificate validity, and social-profile ownership cannot be established from the repository — flagged for owner verification rather than asserted.
