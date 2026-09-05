# Ebrahim's Flash Cards

A vocabulary review app built for learners of German course books, with a companion (in-progress) admin/volunteer panel for community-driven content contribution.

Live at: https://t.me/ebrahimsflashcards

---

## What this is

The project started as a simple tool to review vocabulary, word, part of speech, article (for nouns), plural form, synonym/antonym, Persian translation, and an example sentence, from German course books. As classmates and other learners started using it, some offered to help expand the word list, which grew into a moderated volunteer-contribution workflow with teacher-editors reviewing submissions before publication.

It's built and maintained solo, alongside other work, and several parts (the volunteer panel, gamification, messaging) are intentionally incomplete, see [`ADR.md`](./ADR.md) for the full history of what's built, what's partial, and why specific design choices were made.

## Features

**Public, no login required:**

- Choose a book and lesson, review flashcards with word/translation/example
- Toggle verb-only filtering and shuffle order
- Self-hosted pronunciation audio (click to play)
- Full lesson data loads once, so review works with an unreliable connection, only audio playback needs a live connection

**Editor login required:**

- Review cards in an editable view and leave comments on specific words
- Review volunteer-submitted words staged in a temporary table before they're published

**Volunteer contribution:**

- Submit new vocabulary for review
- Personal profile with activity history and a simple points-based medal system
- Notifications and internal messages from the administrator

## Tech stack

- **Backend:** PHP, both `mysqli` and PDO (see ADR-011 for why both exist)
- **Database:** MySQL
- **Auth:** Custom-built JWT (manually implemented, not a library) in an HttpOnly cookie for editor login on the public-facing flow; PHP sessions for the volunteer/admin panel login (these two flows are intentionally not yet unified, see ADR-009)
- **Frontend:** Server-rendered PHP, vanilla JS, Bootstrap

## Security

This project went through a dedicated security-hardening pass. Highlights:

- All queries parameterized (mysqli/PDO prepared statements), no raw SQL string interpolation
- Output escaping (`htmlspecialchars`, JSON-safe encoding flags) against reflected/stored XSS
- Editor identity verified server-side via signed JWT, never trusted from client input
- CSRF protection via the double-submit cookie pattern on state-changing requests
- Passwords migrated transparently from unsalted SHA-256 to bcrypt on login
- Centralized DB credentials, no secrets committed to the repo

Full reasoning and context for each of these lives in [`ADR.md`](./ADR.md).

## Getting started

1. Clone the repo
2. Copy `config.example.php` to `config.php` and fill in your own database credentials and a freshly generated `JWT_SECRET` (a long random string, never reuse the example value)
3. Import the schema (`flashcards.sql` or your own dump) into MySQL
4. Point your PHP-capable web server at the project root

`config.php` is not included in the repo, copy `config.example.php` and fill in your own values.

## Project status & roadmap

This is an actively evolving personal project, not a finished product. See [`ADR.md`](./ADR.md) for:

- What's fully working vs. partially built vs. planned
- Why specific architectural decisions were made (and one that was reconsidered and reversed)
- What's next: completing the volunteer review workflow, a learner-facing user panel, a Leitner-system (spaced repetition) review mode, tiered free/pro content access, and extending the security audit to the remaining unreviewed files

## Related

- Companion German course book: https://abeling.ir/book
- Telegram: https://t.me/ebrahimsflashcards

## Author

Ebrahim Haddad, backend developer.
