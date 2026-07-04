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

## Deploying to production — done 2026-07-05

The corporate page redesign is **live on production** as of 2026-07-05. Actual sequence used, each step separately approved (the auto-mode permission layer specifically required its own explicit yes for plugin activation and for creating the new live product, beyond the general go-ahead for the overall plan):

1. Copied `site-core/site-core.php` and the `corporate-ui` plugin (template + CSS) to production's `wp-content/plugins/`, lint-checked, left inactive first.
2. Activated both plugins (explicit approval) — verified the bulk-discount hook on a real cart simulation (20 units → £275.00) before touching anything customer-facing.
3. Created the "Treat Trunk Corporate Snack Box" product on production (ID 54610, explicit approval) — same content/price/image as staging's version.
4. Updated Yoast SEO title/meta on the production `corporate-orders` page (ID 36634).
5. Switched `_wp_page_template` on production's page 36634 to the new template — this was the actual go-live moment.
6. Cleared the one stale WP Rocket cache directory for `corporate-orders` specifically (not a full site cache flush) so the new template served immediately.
7. Smoke-tested: live page HTML (correct H1/title, no PHP errors), homepage/shop/letterbox product all still 200, bulk-pricing note visible on the live product page, zero "staging" references leaked into production output.

**Deliberately excluded from production** (staging-only safety nets, not needed/wanted on a live site):
- `site-core/mu-staging-email-block.php` (production needs real email)
- Any of the WP-Cron/payment-gateway/indexing changes made on staging to handle the "clone of live DB" scenario — none of that applies to production, which was never touched in those respects.

## Rollback

- **Corporate page specifically**: reset `_wp_page_template` back to its prior value (`default`) — the original Elementor-built page reappears, since nothing was deleted (see `docs/elementor-removal-plan.md`).
- **Any other file change**: restore from the local file backup taken before the change (`docs/backup-plan.md`), or from the pre-change Lightsail snapshot for a full-instance rollback.
- **Git**: every deployed change corresponds to a commit on `feature/corporate-page-redesign` (later merged to `main`), so the exact diff of any deploy is always known and revertible in the repo, independent of the server-side rollback.
