---
target: homepage + article surfaces
total_score: 23
max_score: 40
na_heuristics: 
p0_count: 2
p1_count: 2
timestamp: 2026-09-28T14-20-14Z
slug: wacanan-wilsb-me
---
## Wacanan critique — homepage + article surfaces (2026-09-28)

Method: dual-agent (A: CritiqueDesignReview · B: CritiqueDetectorEvidence)

### Design Health Score — 23/40 (needs work)

| # | Heuristic | Score | Key Issue |
|---|-----------|-------|-----------|
| 1 | Visibility of System Status | 2 | Follow/bookmark/bell fake interactivity, no loading states |
| 2 | Match System / Real World | 2 | "Reading List", "Topics for you", "follow" promise nonexistent features |
| 3 | User Control and Freedom | 2 | Signed-in "Start writing" loops to /register; no editor link in UI |
| 4 | Consistency and Standards | 2 | Two search behaviors; "All" active state differs; 3 primary-button styles; "reads" vs "stories" |
| 5 | Error Prevention | 4 | Register form validation craft solid (minor: 8-char hint unenforced) |
| 6 | Recognition Rather Than Recall | 3 | No reading time; "10 reads" unitless; TOC is truncated fragments |
| 7 | Flexibility and Efficiency | 3 | No skip link; single-category filter; mobile TOC wall |
| 8 | Aesthetic and Minimalist Design | 2 | Featured article renders 3x; sidebar repeats header nav |
| 9 | Error Recovery | 2 | Good empty states; thin recovery beyond them; no help link |
| 10 | Help and Documentation | 1 | No FAQ/help; no password-reset route — lockout permanent |

### Design Specificity Verdict — interchangeable, with an authored seed
Competent 2024 Tailwind publication look (system-ui, emerald accent, rounded-2xl cards). Swap wordmark + accent and it serves any blog. Authored parts worth keeping: editorial card rhythm (eyebrow → title → excerpt → avatar/date/likes), serif/sans split on show page, one-accent discipline, honest empty-state copy. Identity lives in corpus (night markets, Kota Tua), not interface.

### Deterministic scan — 0 true positives on target surfaces
detect.mjs exit 2, 3 warnings total, all outside target surfaces: bounce-easing in welcome.blade.php inlined Tailwind fallback CSS (false positive); gray-on-color hover-variant mispair in admin index (false positive); empty-src hidden preview img in admin form (borderline). home/layouts/articles/auth templates clean. Live: zero `http://` occurrences, assets all `https://`, lang="en", viewport present, single h1.

### Overall Impression
Article read page is the real product and the best surface. Homepage sags under repetition; register flow breaks its own promise. Biggest opportunity: connect "Start writing" to the editor for signed-in users.

### What's Working
1. Article page typographic spine (serif title, 68ch, 1.8 rhythm, dark cover hero).
2. Register validation craft (per-field errors, aria wiring, old() retention).
3. Dependency-free design layer (one accent, no font payload, reduced-motion on reveals, clipboard fallback).

### Priority Issues
1. [P0] "Start writing" dead-ends signed-in users at /register; no admin.articles.create link in public UI. Fix: @auth conditional → editor for authed. Command: $impeccable clarify.
2. [P0] No password reset; forgotten password = permanent lockout. Fix: add reset routes + login link. Command: $impeccable harden.
3. [P1] Dead bookmark/bell (href="#") + fake Follow toggle (no persist, stale aria-label). Fix: delete until real. Command: $impeccable distill.
4. [P1] Homepage repeats featured article 3x; sidebar duplicates header nav. Fix: exclude shown IDs from Reading List; drop sidebar topics. Command: $impeccable layout.
5. [P2] Fabricated TOC (truncated paragraph openers) + pull quote duplicates paragraph 1 mid-word. Fix: headings-backed TOC or drop; author quote field. Command: $impeccable distill.

### Persona Red Flags
- Mobile 3-minute browser: no header search <768px, featured burns 3 slots, no reading time to triage — bounces thinking archive is one deep.
- Aspiring writer via hero CTA: registers ("Make your first mark"), lands home saying "Start writing" again, no editor door — writes the bad review.
- Returning signed-in reader: bookmark/bell go nowhere, "stories you follow" never materializes — distrust transfers to the good reading surface.
- SR/reduced-motion: canvas rAF ignores prefers-reduced-motion; no skip link; Follow has no aria-pressed.

### Minor Observations
Avatar initials collide with name ("ASArya Salim"); header search drops category; "10 reads" = min(total,10) counting articles not reads; 8-char hint unenforced; pagination unstyled default; tab tagline differs from page headline; like error low-contrast on dark.

### Questions to Consider
- Publication or writing platform — which is the homepage built for?
- Day 30, corpus fully read: what does "Reading List" become?
- Why no author pages when writers are the uncopyable asset?
- Quote the writer chose, or no quote?
- If Follow ships fake six more months, what does that teach visitors about the like counter?
