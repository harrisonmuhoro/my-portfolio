# Phase 2.1 — Personal Entity Identity Implementation Specification

**Date:** 26 November 2026 · **Status:** Approved by owner · **Scope:** website, GitHub profile README, LinkedIn (manual), cross-platform verification.

This document records the owner-approved identity specification that governed the Phase 2.1 implementation, the manual LinkedIn plan, and the boundaries of what was changed vs. what remains owner-managed.

---

## A. Approved professional identity (authoritative)

| Field | Approved value |
|-------|----------------|
| Professional name | Harrison Muhoro |
| Primary professional title | **Software Developer & Technology Consultant** |
| Expertise areas (three, equal weight) | 1. Full-Stack Development · 2. IT Support & Networking · 3. Enterprise Software Development |
| Professional location | Nyeri, Kenya |
| Experience | **Two years of combined practical experience** across software development and IT Support & Networking — never stated as two years per area |
| Consulting positioning | Offered primarily alongside software development and IT services — not a standalone advisory-only business |
| Certifications | freeCodeCamp Responsive Web Design (2023) · freeCodeCamp Back End Development and APIs (2024) · Microsoft AZ-900 **In progress** |
| Website | https://www.harrisonmuhoro.me/ |
| GitHub | https://github.com/harrisonmuhoro |
| LinkedIn | https://www.linkedin.com/in/harrison-muhoro-6476b33a8/ — **supersedes `zion-harrison` everywhere** |

**Technology listing rule (owner-confirmed):** all technologies the owner has worked with may be listed, subject to evidence in project repositories and profile content. Actual experience must be distinguished from proficiency and current learning — no technology may be represented as expert-level or currently active without evidence.

## B. Website implementation (completed — see verification doc)

- `PRIMARY_TITLE` → `'Software Developer & Technology Consultant'`; `SECONDARY_TITLE` → the three expertise areas.
- `SITE_DESCRIPTION`, `SEO_KEYWORDS` → title-aligned, three-pillar wording.
- `SOCIAL_LINKS['linkedin']` → canonical confirmed URL (single source — no hardcoded LinkedIn URLs elsewhere).
- Homepage hero eyebrow/subheadline, About title/bio/alt, Contact page title/description/LinkedIn display, footer tagline → approved positioning.
- Person JSON-LD `jobTitle` follows `PRIMARY_TITLE`; `sameAs` = GitHub + confirmed LinkedIn only (email/WhatsApp excluded).
- Experience wording → combined-experience phrasing in EN and SW i18n strings.
- AZ-900 remains labelled "In progress" — unchanged.

## C. GitHub implementation

**Repository `harrisonmuhoro/harrisonmuhoro` (profile README):** aligned — commit `eb00a79` (plus owner-side rewrite commits `d6e95ac`…`4d6f430` that landed concurrently and were rebased onto). README now carries the approved title, the three pillars, the confirmed LinkedIn badge/link, combined-experience wording, consulting-alongside positioning, and "AZ-900 in progress / not yet certified".

**Owner-managed fields still pending (cannot be changed via git):**
- Profile bio field (currently emoji-only) → replace with one-line professional summary.
- Profile location formatting → "Nyeri, Kenya".
- Profile README **repo** description, website field, and topics.

## D. LinkedIn manual implementation plan (owner-managed — NOT changed by agent)

LinkedIn blocks automated access (HTTP 999). The following is the recommended manual checklist for owner approval and application:

### Headline (recommended)
```
Software Developer & Technology Consultant | Full-Stack Development | IT Support & Networking | Enterprise Software Development
```
(Current screenshot headline "Web Developer | HTML, CSS, JavaScript, PHP | Builds clean, responsive website." does not reflect the approved identity.)

### About section (recommended content)
- Introduce Harrison Muhoro as a Software Developer & Technology Consultant based in Nyeri, Kenya.
- Give equal prominence to the three expertise areas: Full-Stack Development, IT Support & Networking, Enterprise Software Development.
- State two years of **combined** practical experience across software development and IT support & networking.
- Explain technology consulting is offered alongside implementation and technical services.
- List only technologies/projects supported by evidence; distinguish experience from proficiency.
- Link the portfolio (harrisonmuhoro.me) and GitHub.
- Do not invent employers, clients, qualifications or achievements.

### Featured section (recommended)
- Official portfolio (https://www.harrisonmuhoro.me/)
- One or two substantial project case studies (e.g. Afya HMS, Taara HMS, InduTrack KE)
- Relevant GitHub repositories
- Genuine technical articles if available

### Experience & Skills
- Review existing owner-provided entries before editing.
- Do not create fictional employment history or claim personal projects were paid client engagements.
- Align headline/title fields with the approved primary title.

## E. Cross-platform verification (post-implementation)

Pending owner completion of LinkedIn + GitHub profile fields. Website-side verification is automated in `tests/run_tests.php` (see verification doc). Final manual checklist for the owner after LinkedIn edits:

- [ ] LinkedIn headline matches approved title/pillars
- [ ] LinkedIn About uses combined-experience wording
- [ ] LinkedIn links to portfolio + GitHub
- [ ] GitHub profile bio field updated (non-emoji)
- [ ] GitHub location reads "Nyeri, Kenya"
- [ ] Profile README repo description/website/topics filled
- [ ] No surface still references `zion-harrison`

## F. Constraints honoured

- No fabricated credentials, employers, clients, project results or engagement claims.
- No artificial backlinks or invented profiles.
- Certificate statuses unchanged (AZ-900 remains "In progress").
- Canonical domain unchanged; EN/SW i18n preserved; all Phase 1 SEO/security work preserved.
- No claims of Google indexing, Knowledge Panel or ranking improvements.
- LinkedIn was not modified directly.
