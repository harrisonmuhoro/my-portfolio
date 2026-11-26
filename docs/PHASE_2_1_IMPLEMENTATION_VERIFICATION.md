# Phase 2.1 — Identity Implementation Verification

**Date:** 26 November 2026 · **Scope:** evidence record for the approved identity rollout across website + GitHub profile README. LinkedIn and remaining GitHub profile fields are owner-managed (see `PHASE_2_1_IDENTITY_IMPLEMENTATION_SPEC.md` §D/E).

---

## 1. What was changed

### Website (`harrisonmuhoro/my-portfolio`)

| File | Change |
|------|--------|
| `config/config.php` | `PRIMARY_TITLE` → `Software Developer & Technology Consultant`; `SECONDARY_TITLE` → three expertise areas; `SITE_DESCRIPTION` + `SEO_KEYWORDS` title-aligned; `SOCIAL_LINKS['linkedin']` → `https://www.linkedin.com/in/harrison-muhoro/` |
| `config/config.example.php` | Same LinkedIn URL + title constants (template parity) |
| `pages/home-redesign.php` | page_title, hero eyebrow, subheadline, image alt → approved positioning |
| `pages/about.php` | title, bio line, image alt → approved positioning; `about-page__title` now renders `PRIMARY_TITLE` |
| `pages/contact.php` | page title/description + LinkedIn display text → confirmed profile |
| `includes/footer.php` | footer tagline → approved positioning |
| `includes/i18n.php` | EN + SW `hero_sub`/`hero_title` → combined-experience wording ("Two years of combined hands-on experience…" / "Miaka miwili ya pamoja…") |
| `tests/run_tests.php` | LinkedIn `sameAs` expectation updated to confirmed URL; ~11 new assertions: `jobTitle` consistency, no stale `zion-harrison` URL on any public page, combined-experience EN/SW checks, AZ-900 "In progress", contact-page LinkedIn destination |
| `docs/PHASE_2_1_PERSONAL_ENTITY_BASELINE_AUDIT.md` | Corrected per owner evidence (LinkedIn screenshot, confirmed URL, owner decisions) |

**LinkedIn URL — final canonical value:** `https://www.linkedin.com/in/harrison-muhoro/`. This owner-confirmed URL supersedes both the original `linkedin.com/in/zion-harrison` and the intermediate slugged variant `…/in/harrison-muhoro-6476b33a8/` (which appeared transiently during implementation and in the owner-supplied screenshot). All links resolve through `SOCIAL_LINKS['linkedin']` — single source, no hardcoded duplicates. Tests assert the exact URL (sameAs membership, every rendered `href`, contact-page anchor) and reject both superseded variants on all public pages.

**Mail recipient precedence (corrected):** `MAIL_TO` = `MAIL_TO` env → `CONTACT_EMAIL` env → `zionmuhoro@gmail.com` (approved default). Previously `config.php` ignored the `MAIL_TO` env var and `config.example.php` ignored the `CONTACT_EMAIL` fallback — runtime behavior now matches documentation. `config/config.local.php` is a **local-only, gitignored** WAMP override (it exists on the dev machine, not in the repository — a 404 when fetched from GitHub is expected); its `MAIL_TO` line was updated locally to the same getenv chain so env precedence also works in local development. `MAIL_FROM` and Resend settings unchanged.

### GitHub profile README (`harrisonmuhoro/harrisonmuhoro`)

Commit `eb00a79` — "Clarify combined two-year experience and consulting positioning". Concurrent owner-side rewrite commits (`d6e95ac`, `6e78734`, `4d6f430`, stats commits) were rebased onto; final README carries:

- Title: **Software Developer & Technology Consultant**
- Pillars line: Full-Stack Development · Enterprise Software Development · IT Support & Networking
- Location: Nyeri, Kenya
- LinkedIn badge + link → confirmed URL
- Combined two-year experience wording (EN)
- Consulting-alongside-implementation positioning
- AZ-900: "Currently studying toward the certification. Not yet certified."
- Portfolio + GitHub + email links retained; genuine project descriptions preserved

## 2. Test evidence

```
php tests/run_tests.php
→ 125 passed, 0 failed, 1 skipped (symlink cases — run on Linux CI)
php -l config/config.php config/config.example.php → clean
```

New/updated Phase 2.1 assertions (all PASS):
- `Person jobTitle is the approved primary title`
- `homepage presents the approved primary title`
- `about page presents the approved primary title`
- `contact page links the confirmed LinkedIn profile`
- `no public page publishes the outdated LinkedIn URL`
- `English IT Support states combined (not per-area) experience`
- `Kiswahili IT Support conveys the same combined-experience meaning`
- `AZ-900 remains labelled In progress on the homepage`
- `Person sameAs contains only the approved identity profiles` / `excludes email and WhatsApp` (existing, still passing)

## 3. Not changed / still owner-managed

- **LinkedIn profile** — headline still reads "Web Developer | HTML, CSS, JavaScript, PHP…" per owner screenshot; manual checklist in spec §D awaits owner application. No agent access exists (HTTP 999).
- **GitHub profile bio field** — still emoji-only; owner-managed.
- **GitHub profile location formatting** — owner-managed.
- **Profile README repo metadata** (description/topics/website) — owner-managed repo settings.
- **Certificate verification URLs** — owner to supply freeCodeCamp links.

## 4. Outstanding risks / limitations

- Phase 2.1 is **not fully closed**: website + README are aligned and tested, but LinkedIn and GitHub profile fields await owner action. Do not claim cross-platform consistency until the §E checklist is completed.
- LinkedIn About/Experience/Featured/Skills were never audited (screenshot limitation) — owner must review for consistency when editing.
- No claims made about indexing, Knowledge Panel or rankings.
