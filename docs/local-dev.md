# Local Development Setup

## Local machine facts (confirmed 2026-07-02)

- OS: macOS (Darwin 21.6.0)
- Git: installed (2.37.1)
- Node/npm: installed (node v21.0.0, npm 10.9.8)
- WP-CLI: **not installed locally**
- Docker: **not installed locally**

## Recommendation: LocalWP

Given Docker is not currently installed, **LocalWP** is the best fit:

- Native macOS app, no Docker dependency (rules out DDEV and Docker Compose without an extra install + approval first, per safety rule 11).
- One-click WordPress + MySQL + PHP environment, easy to match production's PHP 8.2 / MariaDB-compatible MySQL version.
- Handles WooCommerce and Elementor without any special configuration — it's just WordPress underneath.
- Has a built-in "Live Link" feature for quick shareable preview URLs, useful for showing UI work without needing staging for every small iteration.
- Easiest option for iterating on the corporate page's HTML/CSS/JS quickly, then promoting proven changes to staging.

**Alternative — DDEV**: better if the project later grows to need a fully scripted, reproducible, CLI-driven environment (e.g. onboarding more developers, CI integration). Requires Docker Desktop first (new install, needs approval). Worth revisiting if the project scales beyond one corporate page.

**WordPress Playground**: only suitable for very quick, throwaway checks of isolated PHP/template logic — it can't run the full plugin stack (WooCommerce + Elementor + Wordfence + AutomateWoo etc.) reliably and has no persistent state across the plugins this site depends on. Not recommended as the primary environment.

Installing LocalWP is itself a new local tool install — per safety rule 11, this requires the user's approval before running the installer, even though it's local-only and low-risk.

## Workflow (once LocalWP is approved and installed)

1. **Clone this repo**: `git clone <remote-url-once-it-exists> treat-trunk-corporate` (no remote exists yet — repo is currently local-only, per the user's note).
2. **Create a new LocalWP site** matching production's PHP version (8.2) and WordPress version (6.9.4).
3. **Import a sanitized database** (from the Phase 5 sanitized export, never the raw production dump) via LocalWP's built-in database import, or `wp db import <file>.sql` inside LocalWP's shell.
4. **Configure the local environment**: LocalWP manages its own `wp-config.php` — do not copy production's `wp-config.php` into it or into this repo (safety rule 7). Only DB credentials LocalWP generates itself are used locally.
5. **Install WordPress core + WooCommerce + Elementor/Elementor Pro**: LocalWP installs WP automatically; WooCommerce and Elementor are then installed from the plugin repo / licensed Elementor Pro package to match production versions (WooCommerce 10.9.1, Elementor 3.35.4, Elementor Pro 3.27.5) — these are third-party plugins, not vendored into this Git repo (per the repo structure decision), so they're installed fresh locally each time rather than copied.
6. **Copy in custom code from this repo**: symlink or copy `theme/` and `plugins/` (this repo's custom code, once Phase 4 has pulled it from production) into LocalWP's `wp-content/themes` / `wp-content/plugins`, then activate the `hello-elementor` theme and the custom plugins (`treattrunk-welcome-box`, `subscription-reporting`, `woo-update-manager`) to match production's active set.
7. **`npm install`** inside whichever new code directory is created for the corporate page work (`corporate-ui/`, once its implementation method is decided in Phase 10) — no build tooling exists yet since no frontend rebuild has started.
8. **`npm run dev`** for local iteration with hot reload (if a Vite-based approach is chosen); **`npm run build`** to produce the production-ready assets before pushing to staging.
9. **Test the corporate page locally** at LocalWP's local URL (e.g. `http://treattrunk-corporate.local/corporate-orders/`), comparing against the production page inventoried in `docs/frontend-techniques.md`.
10. **Deploy to staging**: once satisfied locally, push the code to the `feature/corporate-page-redesign` branch and copy/deploy it to the staging server (exact deploy mechanism to be defined once staging exists — see `docs/staging-plan.md`).

## Avoiding real payments/emails locally

- LocalWP sites have no outbound mail configured by default — real emails will not send unless something is explicitly configured to relay them. Leave it that way.
- Any payment gateway testing locally must use Stripe/WooCommerce Payments **test/sandbox keys only** — never production keys, and production keys should never be present in a local `wp-config.php` in the first place since it's LocalWP-managed, not copied from production.
