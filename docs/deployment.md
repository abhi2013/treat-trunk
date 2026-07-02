# Deployment

## Flow

`local dev (LocalWP)` → `feature/corporate-page-redesign` branch, committed to this repo → `staging` (Lightsail) → **explicit approval** → `production` (Lightsail)

Production is never deployed to directly from this repo without a specific,
explicit approval for that deploy — this repo having code ready is not
itself authorization to push it live (per the standing safety rules).

## Deploying to staging (once staging exists — see `docs/staging-plan.md`)

Mechanism to be finalized once staging's connection details are known, but
the intended approach:

1. Work happens on `feature/corporate-page-redesign`.
2. New/changed files (the custom plugin or template under `corporate-ui/`, plus any theme file changes) are copied to staging via `rsync`/`scp` over SSH — same pattern as the read-only-safe copies used for backups, just in the opposite direction and targeted at staging, never production.
3. On staging, the new page template is assigned to the `corporate-orders` page (see `docs/elementor-removal-plan.md`) — a low-risk, reversible change since the underlying Elementor data isn't deleted.
4. Full run through `docs/testing-checklist.md` on staging before considering the work "done."

## Deploying to production (future, requires explicit approval)

Not planned as part of the initial corporate-page work described in this
repo's docs so far — the user's brief was staging-first. If/when production
deployment is requested:

1. Confirm staging testing is fully green (`docs/testing-checklist.md`).
2. Take a fresh Lightsail snapshot of production immediately before any change (`docs/backup-plan.md` §1).
3. Show the exact file(s) to be changed and the exact copy command, and get explicit approval before running it — same pattern as every other production-write action in this project.
4. Apply the same template-swap technique used on staging (reversible: switching `_wp_page_template` back undoes it).
5. Immediately verify the live page and re-run the relevant subset of the testing checklist against production.

## Rollback

- **Corporate page specifically**: reset `_wp_page_template` back to its prior value (`default`) — the original Elementor-built page reappears, since nothing was deleted (see `docs/elementor-removal-plan.md`).
- **Any other file change**: restore from the local file backup taken before the change (`docs/backup-plan.md`), or from the pre-change Lightsail snapshot for a full-instance rollback.
- **Git**: every deployed change corresponds to a commit on `feature/corporate-page-redesign` (later merged to `main`), so the exact diff of any deploy is always known and revertible in the repo, independent of the server-side rollback.
