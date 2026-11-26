# Phase 2.1 — Personal Entity Authority Baseline Audit

**Date:** 26 November 2026 · **Scope:** audit only — no content, code, GitHub or LinkedIn changes made.
**Subject:** Harrison Muhoro — approved identity: *Software Developer & Technology Consultant*; expertise: Full-Stack Development, IT Support & Networking, Enterprise Software Development; Nyeri, Kenya.

---

## 1. Executive summary

The three public identity surfaces (website, GitHub profile + README, LinkedIn) are **broadly consistent on facts** — same name, location, portfolio URL, email, project portfolio, and the "2 years" IT-support experience claim. However, they **disagree on professional positioning**: the website leads with "Software Engineer", the GitHub README leads with "Full-Stack Developer · Network Administration · IT Support", and the approved Phase 2 identity is "Software Developer & Technology Consultant". None of the three currently expresses the approved three-pillar positioning. LinkedIn could not be inspected (public fetch blocked). Several claims remain owner-verification items carried over from Phase 0/1 (certifications, experience duration basis).

## 2. Sources inspected

| Source | Result |
|--------|--------|
| `https://www.harrisonmuhoro.me/` + all page sources in repo (`main` @ `3668ef6`) | ✅ inspected (code + Phase 1 live verification) |
| `https://github.com/harrisonmuhoro` (public profile) | ✅ fetched — bio field is emoji-only ("💭👨‍💻👨‍💻⌨️"), location "Nyeri,Kenya", link `harrisonmuhoro.me`, 2 followers |
| `https://github.com/harrisonmuhoro/harrisonmuhoro` (profile README repo) | ✅ fetched — full README captured; repo metadata: **no description, no website, no topics** |
| `https://www.linkedin.com/in/zion-harrison` | ❌ **inaccessible** — HTTP 999 (LinkedIn auth wall). No profile data was invented. |

## 3. Current identity inventory

**Website** (`config/config.php`, pages):
- Name: Harrison Muhoro · `PRIMARY_TITLE` = **"Software Engineer"** · `SECONDARY_TITLE` = "Enterprise Systems & Full-Stack Engineer"
- Person JSON-LD: `jobTitle: "Software Engineer"`, `sameAs` = GitHub + LinkedIn only, `@id …/#person`
- Homepage hero: "Software engineer · Nyeri, Kenya"; page titles mix "Software Engineer" and "Software Developer"
- IT Support page: "2 years experience" badge + "Two years of hands-on experience" hero copy (EN + SW)
- Certifications: Responsive Web Design (freeCodeCamp, 2023), Back End Development and APIs (freeCodeCamp, 2024), AZ-900 "In progress"
- No education/degree claims anywhere on the site ✅

**GitHub profile:**
- Headline in README: "Full-Stack Developer · Network Administration · IT Support · Kenya"
- "2 years of practical experience" — scoped specifically to networking/IT support
- Featured projects: Afya HMS, Taara HMS, InduTrack KE, Scott Pigs Farm, TuaVois (+ Vuka, Loan Mgmt, Portfolio listed)
- Stack badges: PHP, Laravel, Node.js, React, TypeScript, JS, HTML5, CSS3, Tailwind, MySQL, PostgreSQL, Redis, Docker
- Links: Portfolio `harrisonmuhoro.me` ✅, GitHub ✅, Email `zionh191@gmail.com` ✅ — **no LinkedIn link in README**
- Profile README repo metadata empty (no description/topics/website)

**LinkedIn:** unknown — auth-walled.

## 4. Cross-platform consistency matrix

| Field | Website | GitHub | LinkedIn | Status |
|-------|---------|--------|----------|--------|
| Professional name | Harrison Muhoro | Harrison Muhoro | — | ✅ consistent |
| Primary title | "Software Engineer" (+ "Software Developer" on About/contact titles) | "Full-Stack Developer · Network Administration · IT Support" | — | ⚠️ **three different framings; none matches approved "Software Developer & Technology Consultant"** |
| Expertise areas | Web dev, systems, IT support (services page) | Full-stack, networking, IT support (README pillars) | — | ⚠️ conceptually aligned, terminology differs |
| Location | Nyeri, Kenya | "Nyeri,Kenya" (missing space — cosmetic) | — | ✅ consistent |
| Biography | About page bio (developer, practical systems) | README "About Me" (engineer, real-world operations) | — | ✅ consistent tone/facts |
| Portfolio link | canonical domain | `harrisonmuhoro.me` ✅ | — | ✅ |
| GitHub link | `sameAs` + footer | n/a | — | ✅ |
| LinkedIn link | `sameAs` + footer | **absent from README** | — | ⚠️ missing on GitHub |
| Email | `zionh191@gmail.com` | same ✅ | — | ✅ |
| Experience claim | "2 years" IT-support badge/hero | "2 years" networking/IT support | — | ⚠️ consistent number, scope wording differs (see §5) |
| Certifications | 2× freeCodeCamp + AZ-900 in-progress | none listed | — | ⚠️ unverified externally |
| Projects | Afya HMS, Taara?, InduTrack KE, etc. | Same named projects featured | — | ✅ consistent |

## 5. Specific inconsistencies

1. **Primary positioning (highest impact).** Approved identity is "Software Developer & Technology Consultant" with three equal pillars. Website says "Software Engineer"; GitHub says "Full-Stack Developer · Network Administration · IT Support"; neither mentions "Technology Consultant" or the three-pillar structure. Search engines and knowledge panels reconcile entities by consistent naming — divergent titles dilute the entity signal.
2. **Title terminology drift within the website itself.** `PRIMARY_TITLE` = "Software Engineer" while page titles/about/contact say "Software Developer". Internal inconsistency predates Phase 2 and persists in JSON-LD `jobTitle`.
3. **"2 years" claim scope.** GitHub README scopes it to networking/IT support explicitly; the website badge "2 years experience" sits in a general badges row and the hero says "Two years of hands-on experience" without scoping. If the 2 years covers only IT support, the site slightly overstates; if it covers all work, GitHub understates. Needs owner clarification.
4. **LinkedIn absent from GitHub README** despite being an approved `sameAs` identity profile — a missed entity cross-link.
5. **GitHub profile bio field is emoji-only** ("💭👨‍💻👨‍💻⌨️") and location has a missing space ("Nyeri,Kenya") — weak first impression for entity recognition, though README carries the substance.
6. **Profile README repo has no description/topics/website** — GitHub's own SEO/entity metadata is empty.
7. **Stack drift (minor):** website About lists MongoDB + Microsoft Azure; GitHub README lists Redis + Tailwind + Vite which the site omits. Neither is "wrong" — but canonical skill lists should be reconciled.

## 6. Claims verification register

| Claim | Where | Classification |
|-------|-------|----------------|
| "2 years" IT/networking experience | Site badge+hero, GitHub README | **Needs owner confirmation** — duration basis and scope |
| freeCodeCamp Responsive Web Design 2023 | Site certs | **Needs owner confirmation** — no verification URL published |
| freeCodeCamp Back End Development and APIs 2024 | Site certs | **Needs owner confirmation** — same |
| AZ-900 "In progress" | Site certs | Correctly labelled; confirm still accurate |
| Project ownership (Afya HMS, Taara HMS, InduTrack KE, Scott Pigs, TuaVois) | Site + GitHub | **Verified as self-consistent** across both surfaces; external verification N/A (own work) |
| Skills lists (PHP/Laravel/React/MySQL/etc.) | Both | Self-consistent; MongoDB/Azure vs Redis/Tailwind drift noted |
| "Software Engineer" vs "Software Developer" vs approved "Software Developer & Technology Consultant" | Site/GitHub | **Inconsistent** — owner decision required on canonical title |
| Education/degree | — | None claimed anywhere ✅ nothing unsupported |
| Clients/employers | — | None claimed ✅ |

## 7. Recommended corrections (prioritized)

1. **Owner decision:** adopt "Software Developer & Technology Consultant" (or confirm current "Software Engineer") as the canonical primary title; propagate consistently to `PRIMARY_TITLE`, page titles, hero eyebrow, Person `jobTitle`, GitHub README headline, and LinkedIn headline.
2. **Add LinkedIn URL to GitHub README** "Let's Connect" section (approved profile: `linkedin.com/in/zion-harrison`).
3. **Fill GitHub profile README repo metadata** — description, website (`https://www.harrisonmuhoro.me`), topics (e.g. `portfolio`, `php`, `laravel`, `react`, `it-support`).
4. **Improve GitHub profile bio field** — replace emoji-only bio with a one-line professional summary matching the canonical title.
5. **Clarify "2 years" scope** on the website (e.g. "2 years in IT support & networking") or correct the duration.
6. **Publish certificate verification links** (freeCodeCamp public profile URLs) once owner confirms — strengthens E-E-A-T and entity trust.
7. **Reconcile skill lists** — pick one canonical set (suggest union minus genuinely unused tools).
8. Minor: fix "Nyeri,Kenya" spacing on GitHub profile.

## 8. Required owner decisions

- Canonical primary title (adopt approved "Software Developer & Technology Consultant" vs keep "Software Engineer").
- Whether "Technology Consultant" positioning is accurate/offered (it implies advisory services — confirm before publishing).
- Basis and scope of the "2 years" claim.
- Certificate verification URLs (freeCodeCamp profile link).
- LinkedIn headline/current title (inaccessible to audit — owner must supply).
- Whether GitHub README should be updated to mirror the three expertise pillars.

## 9. Proposed implementation sequence (for a later phase — not started)

1. Owner decisions (§8) → 2. Website title/positioning alignment (`config.php` + affected strings, JSON-LD auto-follows) → 3. GitHub README + profile field + repo metadata → 4. LinkedIn headline alignment (owner manual) → 5. Certificate verification links → 6. Re-run SEO test suite + add entity-consistency assertions.

## 10. Limitations

- LinkedIn profile **not inspected** (HTTP 999 auth wall) — all LinkedIn fields are owner-supplied knowledge only.
- GitHub repo code/descriptions beyond the profile README were not exhaustively audited (9 public repos exist; featured ones captured via README).
- No claim is made about Google indexing, Knowledge Panel eligibility, or Search Console state.
- "Verified" above means consistent across the two accessible surfaces, not externally credential-verified.
