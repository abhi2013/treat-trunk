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

## PageSpeed Insights fixes — done 2026-07-04

Shipped to staging first, then production, in this order:

1. **CompressX `exclude_png` setting** (DB option `compressx_general_settings`, not file-based — no repo artifact) was `1` on both environments, meaning no PNG on the site (the majority of images — icons, illustrations, logo) ever got a WebP/AVIF `<picture>` rewrite, even though many WebP files already existed on disk. Flipped to `0` on staging then production, followed by a one-off batch reprocessing of the images that genuinely hadn't been converted yet (0 failures on either environment).
2. **`site-core/site-core.php`**: added conditional loading for the Total Recipe Generator plugin's CSS (was loading site-wide including pages with no recipe content — the single biggest render-blocking resource on the homepage), async-deferred a handful of non-essential stylesheets (cookie consent banner, ActiveCampaign popup form, Pinterest save button, wc-partial-shipment widget, Mailjet form widget), and removed WordPress core's MediaElement (audio/video player) CSS from pages with no actual media content.
   - Deliberately **excluded** the same async-defer treatment for Elementor's `e-transitions` stylesheet — it defines the opacity/transform states for Elementor's hover-animation effects (fade-in/zoom/etc), so deferring it risked leaving an animated above-the-fold element invisible/mistransformed until load. The lab-test evidence for this was inconclusive (single-run PageSpeed Insights scores on this server vary ±14 points with zero code changes, and repeat test calls in quick succession return a cached report, not fresh runs — confirmed via identical `fetchTime` values), so this was a code-level judgment call, not something PSI numbers could actually prove either way. Left excluded given the small size of the win (~189ms) relative to the residual doubt.
3. Net effect on production (single lab-test run, mobile): PageSpeed Performance score 71 → 89 immediately after step 1 + the initial batch of step 2. Real-world field data (CrUX, 28-day rolling) was already rated **FAST** across FCP/LCP/CLS before any of this work — these changes target the synthetic lab-test/Core Web Vitals score, not a real user-facing slowness problem.

**Known unresolved, not blocking**: WP Rocket's "Remove Unused CSS" / critical-CSS feature has been failing authentication against WP Rocket's own SaaS build service since at least 2026-06-03 (`400: We could not authenticate your request...`), confirmed still broken as of 2026-07-04 despite the site's license/registration checking out correctly on wp-rocket.me. Open support ticket with WP Rocket; the remaining ~2.3s of render-blocking CSS (core WooCommerce/Elementor/Google Fonts stylesheets, deliberately left untouched here as too risky to hand-defer without real critical CSS) is gated on that getting fixed.

## Accessibility fixes ("agentic browsing" / link-name audit) — done 2026-07-04

Lighthouse's `link-name` audit ("Links must have discernible text") was failing on 8 links: Elementor Gallery items, Elementor Social Icons widget links, and 3 "our-snacks" links wrapping sticker images with empty `alt=""`. Fixed via `site-core/site-core.php`:

- A footer script labels gallery-item links from Elementor's own `data-elementor-lightbox-title` data, and social-icon links from a domain→label lookup against their `href`.
- Real alt text set in the media library for the 3 sticker images (IDs 7142/7144/7146 — "Fun"/"Nutritious"/"Surprises") — correct regardless of the point below, but doesn't fully resolve the audit on its own: Elementor's live front-end render of these specific image-widget instances doesn't reliably reflect the current `_wp_attachment_image_alt` value (root cause not isolated — ruled out page cache, CompressX's own metadata, and any persistent object cache). The same footer script also labels these 3 links directly by the wrapped image's class as a working fallback.
- **Two real bugs found and fixed along the way, not just PSI-score issues**: (1) the footer script originally waited for `DOMContentLoaded`, but WP Rocket's "delay JS execution" was rewriting it to only run after a real user interaction (scroll/click/etc) — by which point `DOMContentLoaded` had already long since fired, so the listener would never have fired for *any* real visitor either, not just Lighthouse. Fixed by excluding the script from delay via the `rocket_delay_js_exclusions` filter and removing the event-listener wrapper (the script runs unconditionally in the footer, after all markup it targets already exists). (2) The sticker-link fallback initially checked `link.textContent.trim()` to avoid mislabeling links that already have real text — but these links contain a `<noscript><img></noscript>` fallback, and with JS enabled `textContent` includes that noscript's raw markup as literal text, which caused the check to wrongly skip the very links it was meant to fix.
- Verified via a real PSI API run (not just markup inspection): `link-name` audit went from failing (score 0, 8 broken links) to passing (score 1), Accessibility category 89 → 92.

## Rollback

- **Corporate page specifically**: reset `_wp_page_template` back to its prior value (`default`) — the original Elementor-built page reappears, since nothing was deleted (see `docs/elementor-removal-plan.md`).
- **Any other file change**: restore from the local file backup taken before the change (`docs/backup-plan.md`), or from the pre-change Lightsail snapshot for a full-instance rollback.
- **Git**: every deployed change corresponds to a commit on `feature/corporate-page-redesign` (later merged to `main`), so the exact diff of any deploy is always known and revertible in the repo, independent of the server-side rollback.
