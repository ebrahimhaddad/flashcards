# Architecture Decision Records, Flashcards Project

## Background

This project started as a simple review tool for vocabulary from German course books: words, part of speech, article (for nouns), plural form, synonym/antonym, Persian translation, and an example sentence, entered into a database by hand.

As classmates and other learners began using it, some offered to help with data entry. A free admin panel was adapted to support this, with submitted words routed through a temporary staging table for review before publication. Later, teachers from German courses got involved as editors, reviewing, commenting on, and approving volunteer submissions.

The project was built and maintained by a single developer alongside other commitments. Many features were designed with real intent (gamification, volunteer profiles, admin messaging) but not fully completed; some administrative work that was meant to be automated has instead been done by hand. This log documents the decisions as they actually happened, including what's fully built, partially built, or still aspirational, so a later pass can pick up threads without re-deriving the original reasoning.

---

## ADR-001: Full card set sent to the client for offline resilience

**Status:** Accepted

**Context:** The target audience is largely Iranian learners, where internet connectivity is often unreliable or restricted. A design requiring a live connection for every card would make the app frustrating or unusable for exactly the audience it's meant to serve.

**Decision:** When a user selects a book/lesson, the entire card set for that selection is embedded into the page in one response (via `json_encode()` into an inline `<script>` block), rather than fetched card-by-card. Once loaded, reviewing cards requires no further network requests except for audio playback.

**Consequences:** Users can review a full lesson offline after the initial load. The trade-off is that all card content (including "hidden until revealed" translations) is present in the page source immediately, acceptable because the app is freeware with no access control tied to individual cards, and the reveal interaction is a study aid, not a security boundary. This decision will need revisiting if a gated pro tier is introduced later, since it currently offers no way to withhold part of a fetched lesson.

---

## ADR-002: Self-hosted pronunciation audio instead of a live TTS API

**Status:** Accepted

**Context:** Pronunciation audio was originally generated via Google Translate's TTS output. Depending on a live third-party API for every playback would reintroduce the same connectivity problem ADR-001 was written to avoid, and would add an external dependency (rate limits, availability, potential cost) for a freeware project.

**Decision:** Generate pronunciation audio once per word and store the audio files on the project's own server (hosted in Iran), rather than calling a TTS API at playback time.

**Consequences:** Playback works reliably without depending on an external API being reachable at the moment of use. Adding a new word requires a one-time audio-generation step rather than being fully automatic; if the word list grows significantly or the audio generation step is ever revisited, this is worth re-evaluating.

---

## ADR-003: Staging-table pipeline for volunteer-submitted content

**Status:** Partially implemented

**Context:** Once volunteers (initially classmates/learners, later teachers acting as editors) began contributing new vocabulary, submissions couldn't go directly into the live word table, they needed review before publication.

**Decision:** New submissions are written to a temporary table (`woerter_tmp`) rather than the live table (`woerter_txt`). An editor reviews entries there, can comment on a submission and refer it back to the volunteer, edit it directly, or approve and move it into `woerter_txt` for publication. Supporting list views exist (or are intended) for waiting, edited, and published states.

**Consequences:** Live vocabulary data stays curated even with open contribution. This workflow is not fully built out, several of the supporting admin views and controls have been handled manually rather than through finished tooling. Treat this as the target design to build toward, not a fully working system today. Concretely: cards can currently only be selected one-by-one or all-at-once for batch actions (no partial multi-select), and editing is limited to plain textareas with no structured/rich editing tools.

---

## ADR-004: Volunteer/editor roles with profiles, gamification, and admin messaging

**Status:** Partially implemented

**Context:** To sustain volunteer motivation and give contributors visibility into their own activity, the plan included personal profile pages for each contributor.

**Decision:** Each user has a profile showing their submission activity and a simple medal/badge system, currently awarded for reaching a certain number of points rather than for accuracy. Communication is split into two parts: notifications and internal messages, with the intent of also sending automated messages (e.g. birthdays or other special dates) through the internal messaging channel.

**Consequences:** This is a meaningful piece of what would make the volunteer pipeline self-sustaining, but is only partially built. Users cannot yet change their own password or profile picture. Medal awarding is point-based only for now (accuracy-based scoring is a planned refinement, not implemented). The automated/special-date messaging is intended but not yet built, messages from the administrator are currently manual. Worth prioritizing if volunteer numbers grow beyond what manual tracking can handle.

---

## ADR-005: Manual administration in place of full automation (solo-maintainer constraint)

**Status:** Accepted (documented constraint, not a target design)

**Context:** The project has been built and maintained by one developer. Several administrative workflows described above (ADR-003, ADR-004) were designed with automation in mind but, given limited time, have partly relied on manual work instead, checking submissions by hand, following up with volunteers directly, tracking activity outside the app.

**Decision:** Accept manual administration as a stopgap for incomplete tooling rather than blocking volunteer contributions on finishing every planned feature first. Prioritize security and correctness of what's live (the public-facing review flow, and the parts of the editor flow that are built) over completing every planned admin feature immediately.

**Consequences:** The project could keep growing with real contributors despite incomplete tooling, at the cost of ongoing manual effort. This is the primary motivation for treating the admin panel modernization as future work rather than something to rush, see the security-refactor entries below, which deliberately focused on the parts of the system already live and public-facing first.

---

## ADR-006: Separate public and private repositories

**Status:** Rejected (superseded - see below)

**Context:** The public-facing review flow and the (incomplete, not fully reviewed for security) admin/volunteer panel shared one local folder. The admin panel isn't ready for public visibility. A two-repo split (public `flashcards` + separate private admin repo) was initially proposed on that basis.

**Decision:** After reviewing the application as a whole, this split was not approved. The project will instead be kept as a single repository covering both the public flow and the admin panel, rather than maintaining two repos with shared conventions (`config.php` structure, JWT helpers) that would otherwise need to be kept in sync manually.

**Consequences:** Repository visibility (public vs. private) becomes a decision about the single repo as a whole, rather than something handled by splitting code across two repos. If parts of the codebase aren't ready for public visibility, that's addressed by keeping the whole repo private until it is, not by partitioning files.

---

## ADR-007: Move editor identity from URL token to JWT in an HttpOnly cookie

**Status:** Accepted

**Context:** Editor access was originally granted via a `?token=...` URL parameter, checked once, then passed to the next page via a hidden form field, which any client could tamper with to claim editor status regardless of having a valid token. The token in the URL also leaked into browser history and logs.

**Decision:** Introduce a real login page (`login.php`) authenticating against `email`/`passcode`. On success, issue a JWT (built manually, to understand the mechanics directly rather than depend on a library) with editor identity and claims, stored in an `HttpOnly`, `Secure`, `SameSite=Strict` cookie. A single `current_editor()` helper resolves identity on every page, defaulting to `'user'` (anonymous) when there's no valid cookie, keeping `cards.php`/`practice.php` fully public by design.

**Alternatives considered:** JWT in `localStorage`, avoids CSRF exposure entirely, but is readable by any script on the page, so a single XSS hole would allow direct, reusable credential theft. Rejected in favor of `HttpOnly`, which blocks that read path outright.

**Consequences:** Editor status can no longer be forged client-side. Using a cookie reintroduces CSRF exposure, addressed in ADR-009.

---

## ADR-008: Systematic audit of every query for injection risk

Status: Accepted

Context: Rather than fixing SQL injection opportunistically wherever it happened to be noticed, the refactor treated it as a category of risk worth checking for deliberately, file by file, across the codebase, since a single missed query undermines the value of fixing all the others.

Decision: Every file touched during the refactor (cards.php, practice.php, api_comment.php) was checked specifically for raw string interpolation of request parameters into SQL, not just reviewed for whatever issue prompted the pass. Each was converted to parameterized queries, mysqli prepared statements where the codebase already uses mysqli, PDO prepared statements where PDO is used (ADR-011 explains why both exist).

Consequences: Injection risk closed across every file reviewed so far. This audit hasn't yet covered the rest of the application (e.g. espanol.php, index1.php, and the volunteer/admin panel), extending the same systematic check to those is tracked in the planned work below, and any new query added going forward should be written parameterized from the start rather than relying on a future audit to catch it.

---

## ADR-009: CSRF protection via double-submit cookie

**Status:** Accepted

**Context:** With editor identity now in a cookie (ADR-007), a forged cross-site request would carry a valid `auth_token` automatically. The only current state-changing action is comment submission (`api_comment.php`).

**Decision:** Use the double-submit cookie pattern, a second, non-`HttpOnly` `csrf_token` cookie, whose value is embedded in forms and verified server-side with a timing-safe comparison. This keeps the JWT-based identity flow (`current_editor()`, the public review flow) free of server-side session state.

**Consequences:** Closes the forgery gap opened by moving the public-facing editor auth into a cookie. Note this applies specifically to the JWT/cookie-based flow covered by ADR-007, the volunteer/admin panel login is a separate flow and does use a PHP session, so the identity model is not uniformly cookie/JWT-based across the whole application. Reconciling the two (or documenting why they differ long-term) is worth a future decision once the admin panel gets its own security pass.

---

## ADR-010: Transparent password hash migration from unsalted SHA-256 to bcrypt

**Status:** Accepted

**Context:** Existing `users.passcode` values were unsalted `sha256(password)`, fast to brute-force, vulnerable to rainbow tables, identical hashes for identical passwords across users.

**Decision:** Detect hash format by prefix at login. Verify legacy hashes the old way; on a successful legacy-format login, transparently re-hash with `password_hash()` (bcrypt) and overwrite the stored value. No forced mass reset.

**Consequences:** Gradual, low-disruption migration tied to actual logins. Dormant accounts stay on the legacy hash until they next log in, acceptable for the current small editor pool.

---

## ADR-011: Single source of DB credentials, dual mysqli + PDO connections

**Status:** Accepted

**Context:** `api_comment.php` previously created its own PDO connection with credentials duplicated from the mysqli connection used elsewhere, including one instance that was accidentally committed to git history.

**Decision:** `config.php` defines credentials once as constants and opens both a mysqli connection (`$db`, used by `cards.php`/`practice.php`) and a PDO connection (`$pdo`, used by `api_comment.php`/`login.php`).

**Consequences:** One place to update credentials. Two DB APIs coexist, which is acceptable short-term; a future ADR should decide whether to consolidate on one.

---

## ADR-012: Output escaping standard

**Status:** Accepted

**Context:** Several values (book/lesson names, cover image paths) were rendered into HTML and into `<script>`-embedded JSON without escaping, creating reflected and potential stored XSS paths.

**Decision:** All dynamic values rendered into HTML use `htmlspecialchars()`. Data embedded into `<script>` blocks via `json_encode()` uses `JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP` to prevent script-tag breakout or attribute injection from stored data.

**Consequences:** Closes the reviewed XSS paths. Any new file rendering user- or DB-sourced data into HTML/JS must follow the same convention.

---

_Planned/upcoming entries: completing the volunteer submission review workflow (ADR-003) and profile/gamification system (ADR-004); tiered free/pro content access; consolidating on a single DB API; automated tests around JWT and auth logic; a learner-facing user panel; a Leitner-system (spaced repetition) review mode; a full pass over every query in the codebase to confirm parameterized queries are used consistently throughout._
