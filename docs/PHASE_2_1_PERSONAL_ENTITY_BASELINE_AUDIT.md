# Phase 2.1 — Personal Entity Authority Baseline Audit

**Date:** 26 November 2026 (corrected same day after owner evidence) · **Scope:** audit only — no content, code, GitHub or LinkedIn changes made during the audit itself.
**Subject:** Harrison Muhoro — approved identity: *Software Developer & Technology Consultant*; expertise: Full-Stack Development, IT Support & Networking, Enterprise Software Development; Nyeri, Kenya.

> **Correction notice (26 November 2026):** This document was corrected after the owner supplied a LinkedIn profile screenshot and confirmed identity facts. The original audit recorded `linkedin.com/in/zion-harrison` as the profile URL and stated LinkedIn was inaccessible — both are superseded. The confirmed LinkedIn URL is `https://www.linkedin.com/in/harrison-muhoro/`. Corrections are marked **[CORRECTED]**; original findings are preserved where still historically relevant.

---

## 1. Executive summary

**[CORRECTED]** The three public identity surfaces (website, GitHub profile + README, LinkedIn) are **broadly consistent on facts** — same name, location (Nyeri, Kenya), portfolio URL, email, project portfolio — but **disagreed on professional positioning** at audit time: the website led with "Software Engineer", the GitHub README led with "Full-Stack Developer · Network Administration · IT Support", and LinkedIn's headline (per owner screenshot) reads "Web Developer | HTML, CSS, JavaScript, PHP | Builds clean, responsive website." None matched the approved "Software Developer & Technology Consultant" three-pillar positioning. The owner has since confirmed the approved identity, the corrected LinkedIn URL, and that the experience claim is **two years of combined practical experience across software development and IT Support & Networking** (not two years per area). Certification claims (2× freeCodeCamp + AZ-900 in progress) remain owner-verification items carried over from Phase 0/1.

## 2. Sources inspected

| Source | Result |
|--------|--------|
| `https://www.harrisonmuhoro.me/` + all page sources in repo (`main` @ `3668ef6`) | ✅ inspected (code + Phase 1 live verification) |
| `https://github.com/harrisonmuhoro` (public profile) | ✅ fetched — bio field is emoji-only ("💭👨‍💻👨‍💻⌨️"), location "Nyeri,Kenya", link `harrisonmuhoro.me`, 2 followers |
| `https://github.com/harrisonmuhoro/harrisonmuhoro` (profile README repo) | ✅ fetched — full README captured; repo metadata: **no description, no website, no topics** |
| LinkedIn profile | ✅ **reviewed via owner-provided screenshot, 26 November 2026** — **[CORRECTED]** (original audit recorded HTTP 999 inaccessibility for the wrong URL `zion-harrison`). Screenshot evidence: name Harrison Muhoro; headline "Web Developer \| HTML, CSS, JavaScript, PHP \| Builds clean, responsive website."; location Nyeri, Kenya; "Open to Work" indicator visible; custom URL slug `harrison-muhoro`. **The screenshot does not expose the About, Experience, Featured, Skills or full contact sections — those were NOT audited and must not be assumed.** |

## 3. Current identity inventory

*Note: website and GitHub rows below describe the state **at audit time** (pre-implementation). See `PHASE_2_1_IMPLEMENTATION_VERIFICATION.md` for post-change state.*

**Website** (`config/config.php`, pages) — *at audit time*:
- Name: Harrison Muhoro · `PRIMARY_TITLE` = **"Software Engineer"** · `SECONDARY_TITLE` = "Enterprise Systems & Full-Stack Engineer"
- Person JSON-LD: `jobTitle: "Software Engineer"`, `sameAs` = GitHub + LinkedIn only, `@id …/#person`
- Homepage hero: "Software engineer · Nyeri, Kenya"; page titles mixed "Software Engineer" and "Software Developer"
- IT Support page: "2 years experience" badge + "Two years of hands-on experience" hero copy (EN + SW)
- Certifications: Responsive Web Design (freeCodeCamp, 2023), Back End Development and APIs (freeCodeCamp, 2024), AZ-900 "In progress"
- No education/degree claims anywhere on the site ✅

**GitHub profile** — *at audit time*:
- Headline in README: "Full-Stack Developer · Network Administration · IT Support · Kenya"
- "2 years of practical experience" — scoped specifically to networking/IT support
- Featured projects: Afya HMS, Taara HMS, InduTrack KE, Scott Pigs Farm, TuaVois (+ Vuka, Loan Mgmt, Portfolio listed)
- Stack badges: PHP, Laravel, Node.js, React, TypeScript, JS, HTML5, CSS3, Tailwind, MySQL, PostgreSQL, Redis, Docker
- Links: Portfolio `harrisonmuhoro.me` ✅, GitHub ✅, Email `zionh191@gmail.com` ✅ — **no LinkedIn link in README at audit time**
- Profile README repo metadata empty (no description/topics/website)

**LinkedIn** — *per owner screenshot, 26 November 2026* **[CORRECTED]**:
- Name: Harrison Muhoro · Location: Nyeri, Kenya · "Open to Work" visible
- Headline: "Web Developer | HTML, CSS, JavaScript, PHP | Builds clean, responsive website." — does **not** reflect the approved title or three pillars
- Custom URL: `harrison-muhoro` — **supersedes `zion-harrison` everywhere**
- About / Experience / Featured / Skills / full contact details: **not visible in screenshot — not audited**

## 4. Cross-platform consistency matrix

| Field | Website | GitHub | LinkedIn | Status |
|-------|---------|--------|----------|--------|
| Professional name | Harrison Muhoro | Harrison Muhoro | Harrison Muhoro (screenshot) | ✅ consistent |
| Primary title | "Software Engineer" (+ "Software Developer" drift) | "Full-Stack Developer · Network Administration · IT Support" | "Web Developer \| HTML, CSS, JavaScript, PHP…" (screenshot) | ⚠️ **three different framings; none matched approved title at audit time** |
| Expertise areas | Web dev, systems, IT support | Full-stack, networking, IT support | — (not in screenshot) | ⚠️ conceptually aligned, terminology differed |
| Location | Nyeri, Kenya | "Nyeri,Kenya" (missing space — cosmetic) | Nyeri, Kenya (screenshot) | ✅ consistent |
| Biography | About page bio | README "About Me" | — (not in screenshot) | ✅ consistent tone/facts (website/GitHub) |
| Portfolio link | canonical domain | `harrisonmuhoro.me` ✅ | — (not in screenshot) | ✅ |
| GitHub link | `sameAs` + footer | n/a | — (not in screenshot) | ✅ |
| LinkedIn link | `sameAs` + footer (**old `zion-harrison` URL at audit time**) | absent from README at audit time | self | ⚠️ **old URL published on website — corrected in Phase 2.1 implementation** |
| Email | `zionh191@gmail.com` | same ✅ | — (not in screenshot) | ✅ |
| Experience claim | "2 years" badge/hero | "2 years" networking/IT support | — (not in screenshot) | ✅ **[CORRECTED]** owner confirms: two years of *combined* practical experience across software development and IT Support & Networking — not per-area |
| Certifications | 2× freeCodeCamp + AZ-900 in-progress | AZ-900 noted as in-progress in later README revision | — (not in screenshot) | ⚠️ unverified externally; **AZ-900 confirmed still "In progress" by owner** |
| Projects | Afya HMS, Taara, InduTrack KE, etc. | Same named projects featured | — (not in screenshot) | ✅ consistent |

## 5. Specific inconsistencies

1. **Primary positioning (highest impact).** Approved identity is "Software Developer & Technology Consultant" with three equal pillars. At audit time: website "Software Engineer", GitHub "Full-Stack Developer · Network Administration · IT Support", LinkedIn "Web Developer | HTML, CSS, JavaScript, PHP…" — four divergent framings once the approved title is counted. Divergent titles dilute entity signals for search engines and knowledge panels.
2. **Title terminology drift within the website itself.** `PRIMARY_TITLE` = "Software Engineer" while page titles/about/contact said "Software Developer". Internal inconsistency predates Phase 2 and persisted in JSON-LD `jobTitle`.
3. **"2 years" claim scope.** **[CORRECTED — resolved by owner]** Owner confirms the claim is two years of *combined* practical experience across software development and IT Support & Networking — it must not be stated as two years in each area separately. Implementation uses combined-experience wording (EN + SW).
4. **LinkedIn absent from GitHub README** at audit time — a missed entity cross-link. **[RESOLVED]** LinkedIn badge/link added to README (commit `eb00a79` in `harrisonmuhoro/harrisonmuhoro`).
5. **GitHub profile bio field is emoji-only** ("💭👨‍💻👨‍💻⌨️") — weak first impression for entity recognition. **Owner-managed field; manual update required** (cannot be changed via repository).
6. **Profile README repo has no description/topics/website** — GitHub's own SEO/entity metadata is empty. **Owner-managed repo settings; manual update required.**
7. **Stack drift (minor):** website About listed MongoDB + Microsoft Azure; GitHub README listed Redis + Tailwind + Vite. **[PARTIALLY RESOLVED]** — owner rule recorded: all technologies the owner has worked with may be listed, but actual experience must be distinguished from proficiency and current learning; no technology is to be represented as expert-level or currently active without evidence.
8. **Outdated LinkedIn URL published on the website** — `config.php`, `config.example.php`, `pages/contact.php`, `tests/run_tests.php` and JSON-LD `sameAs` all carried `linkedin.com/in/zion-harrison`. **[RESOLVED in Phase 2.1 implementation]** — canonical URL is now `https://www.linkedin.com/in/harrison-muhoro/`.

## 6. Claims verification register

| Claim | Where | Classification |
|-------|-------|----------------|
| "2 years" experience | Site badge+hero, GitHub README | ✅ **Owner-confirmed:** two years of *combined* practical experience across software development and IT Support & Networking |
| freeCodeCamp Responsive Web Design 2023 | Site certs | **Needs owner confirmation** — no verification URL published |
| freeCodeCamp Back End Development and APIs 2024 | Site certs | **Needs owner confirmation** — same |
| AZ-900 status | Site certs, GitHub README | ✅ **Owner-confirmed "In progress"** — preserved verbatim; no completion date or credential ID claimed |
| Project ownership (Afya HMS, Taara HMS, InduTrack KE, Scott Pigs, TuaVois, Vuka, Loan Mgmt, LifePilot) | Site + GitHub | **Verified as self-consistent** across surfaces; external verification N/A (own work) |
| Skills/technology lists | Both | **Owner rule:** all technologies worked with may be listed; distinguish experience from proficiency and current learning — no expert-level claims without evidence |
| "Software Engineer" vs "Software Developer" vs approved title | Site/GitHub/LinkedIn | ✅ **Resolved:** owner approved "Software Developer & Technology Consultant" as canonical primary title |
| "Technology Consultant" positioning | Approved title | ✅ **Owner-confirmed:** consulting is offered primarily alongside software development and IT services — not as an independent advisory-only business |
| Education/degree | — | None claimed anywhere ✅ nothing unsupported |
| Clients/employers | — | None claimed ✅ |

## 7. Recommended corrections (prioritized)

1. ~~**Owner decision:** canonical primary title~~ — **DONE:** "Software Developer & Technology Consultant" approved and implemented.
2. ~~**Add LinkedIn URL to GitHub README**~~ — **DONE:** badge + link added (`eb00a79`); correct URL `harrison-muhoro`.
3. **Fill GitHub profile README repo metadata** — description, website (`https://www.harrisonmuhoro.me`), topics. *Owner-managed — pending.*
4. **Improve GitHub profile bio field** — replace emoji-only bio with a one-line professional summary. *Owner-managed — pending.*
5. ~~**Clarify "2 years" scope**~~ — **DONE:** combined-experience wording implemented (EN + SW) per owner confirmation.
6. **Publish certificate verification links** (freeCodeCamp public profile URLs) once owner confirms — strengthens E-E-A-T and entity trust. *Owner task pending.*
7. **Reconcile skill lists** — apply owner rule: union of evidenced technologies, experience ≠ proficiency ≠ current learning.
8. Minor: fix "Nyeri,Kenya" spacing on GitHub profile. *Owner-managed — pending.*
9. **LinkedIn headline/About/Featured/Skills** — align to approved title and three pillars. *Owner-managed — manual checklist in `PHASE_2_1_IDENTITY_IMPLEMENTATION_SPEC.md`.*

## 8. Owner decisions — status

| Decision | Status |
|----------|--------|
| Canonical primary title | ✅ Decided: "Software Developer & Technology Consultant" |
| "Technology Consultant" positioning | ✅ Decided: approved in title; consulting offered alongside implementation services, not standalone advisory |
| Basis/scope of "2 years" | ✅ Decided: two years combined across software development + IT Support & Networking |
| Correct LinkedIn URL | ✅ Decided: `https://www.linkedin.com/in/harrison-muhoro/` (supersedes `zion-harrison`) |
| AZ-900 status | ✅ Confirmed: In progress |
| Technology listing rule | ✅ Confirmed: all worked-with technologies may be listed; distinguish experience from proficiency/current learning |
| Certificate verification URLs | ⏳ Pending — owner to supply freeCodeCamp verification links |
| LinkedIn headline/About content | ⏳ Pending — owner manual update (screenshot shows outdated "Web Developer" headline) |
| GitHub profile bio/location/repo metadata | ⏳ Pending — owner-managed fields |

## 9. Implementation sequence — status

1. ~~Owner decisions (§8)~~ → ✅ done 2. ~~Website title/positioning alignment~~ → ✅ done (see `PHASE_2_1_IMPLEMENTATION_VERIFICATION.md`) 3. ~~GitHub README alignment~~ → ✅ done (`eb00a79`); repo metadata pending (owner) 4. LinkedIn alignment → ⏳ owner manual (plan in `PHASE_2_1_IDENTITY_IMPLEMENTATION_SPEC.md`) 5. Certificate verification links → ⏳ owner 6. ~~Re-run SEO test suite + entity-consistency assertions~~ → ✅ done.

## 10. Limitations

- LinkedIn was reviewed **only via an owner-provided screenshot (26 November 2026)**. The screenshot exposes name, headline, location, Open-to-Work status and the custom URL slug — it does **not** expose About, Experience, Featured, Skills or contact sections. Those remain unaudited; the manual LinkedIn checklist must be owner-verified.
- GitHub repo code/descriptions beyond the profile README were not exhaustively audited.
- No claim is made about Google indexing, Knowledge Panel eligibility, or Search Console state.
- "Verified" means consistent across accessible surfaces or owner-confirmed — not externally credential-verified.
