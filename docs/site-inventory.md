# Site Inventory — treattrunk.co.uk (Production)

Generated from read-only discovery via `ssh treat-trunk` on 2026-07-02. No files
were edited and nothing was installed to produce this document.

## Hosting

- AWS Lightsail instance, region eu-west-2 (London)
- Public IP: `13.43.130.225`
- Internal hostname: `ip-172-26-4-189`
- SSH: user `bitnami`, key `~/.ssh/treattrunk3.pem`, alias `treat-trunk` in local `~/.ssh/config`
- Stack: Bitnami WordPress (Apache + PHP + MariaDB)
- `sudo` works passwordless for the `bitnami` user

## Server software

- PHP: 8.2.19 (CLI, NTS, OPcache enabled)
- MariaDB: 11.3.2
- WP-CLI: installed at `/opt/bitnami/wp-cli/bin/wp`, must be run with `sudo ... --allow-root`
- WordPress: 6.9.4

## WordPress path

- **Live root**: `/opt/bitnami/wordpress` — confirmed via `wp option get siteurl` → `https://treattrunk.co.uk`
- `/home/bitnami/stack/wordpress` is the **same directory** (identical inode `149907`), just a different path into the same install
- `/bitnami/wordpress` is a **different, unrelated directory** (inode `524293`) and is **not a valid WordPress install** (`wp core` commands fail there) — do not use this path for anything
- DB name: `bitnami_wordpress`

## Theme

- **Active theme: `hello-elementor`** (Elementor's "Hello" base theme), reported version `9.9.9` (higher than what WP.org lists — likely a heavily customized/forked copy, not stock)
- **No child theme exists** (`hello-elementor-child` not present)
- The active theme has been **directly hand-edited**, not extended via a child theme:
  - `functions.php` (972 lines / ~31KB) — only include found inside it is `includes/admin-functions.php`; does **not** appear to `require` the two files below
  - `functions1.php` (478 lines) and `functions2.php` (532 lines) exist alongside `functions.php` (972 lines) — **confirmed dead code**: not `require`d/`include`d anywhere in the theme's PHP files (grepped after copying locally). Diffing shows they're similar to each other (91 lines apart) but substantially different from the current `functions.php` (~500+ lines apart each) — almost certainly older snapshots left in place rather than deleted. Safe to ignore; not a blocker for editing `functions.php`.
  - `custom.css` (17KB), `custom.js` (~1KB) — theme-level custom styling/scripting
  - `automatewoo/` — custom AutomateWoo integration folder
  - `tpl-testing-ground.php` — looks like a scratch/testing template file
  - `woocommerce/` template override directory (see below)
- Pattern match for hardcoded secrets (`api_key`, `secret`, `password =`, `stripe_sk`, `smtp_pass`, `consumer_secret`, `private_key`) across `functions.php`, `functions1.php`, `functions2.php` came back **clean** — no obvious hardcoded credentials found
- `my-custom-theme` exists but is **inactive** and nearly empty (just `index.php` 492 bytes + `style.css` 2KB) — looks like an unfinished/abandoned theme stub, not currently relevant
- Several stock themes present but inactive (twentytwenty/twentytwentyone/two/three/four/five) — normal Bitnami/WP defaults, low priority

## WooCommerce template overrides (inside `hello-elementor/woocommerce/`)

`cart/`, `checkout/`, `content-single-product.php`, `emails/`, `global/`, `loop/`, `myaccount/`, `single-product/`

**This is safety-critical**: the site has custom checkout/cart templates already. Per the standing safety rules, these must not be touched without explicit approval, and any future theme work must preserve them exactly.

## WooCommerce

- Version: **10.9.1** (active, no update pending)
- **Live payment gateways active**: `woocommerce-gateway-stripe` (10.4.0), `woocommerce-payments` (10.4.0) — confirms this is a live, order-taking store
- `woocommerce-subscriptions` (5.3.1) + a subscriptions cancel-confirmation plugin — recurring billing is in use
- `wc-partial-shipment`, `flexible-checkout-fields`, `coupon-by-roles-for-woocommerce`, `hide-shipping-method-for-woocommerce`, `woocommerce-extra-product-options-pro`, `woocommerce-order-export`, `woocommerce-legacy-rest-api` (inactive)
- `activecampaign-for-woocommerce`, `pinterest-for-woocommerce`, `automatewoo` + `automatewoo-referrals` (inactive) — marketing integrations
- `affiliates-manager` — affiliate program

## Elementor

- `elementor` (core): **3.35.4** active — update available to 4.1.4 (large version gap, worth investigating before any bump given the custom theme depends on it)
- `elementor-pro`: **3.27.5** active — update available to 4.1.2
- Given the version gap between installed and latest (3.x → 4.x major line), treat any Elementor core/pro update as a planned, tested activity — not routine maintenance

## Custom / first-party-looking plugins

- `treattrunk-welcome-box` (1.0.0) — custom, active
- `subscription-reporting` (0.1.0) — custom, active, looks like an internal reporting tool
- `woo-update-manager` (1.0.3) — custom, active
- `wp-test-email` (1.1.9) — active, likely a dev/testing utility left active on production (worth a low-priority follow-up, not touching it now)
- mu-plugin: `health-check-troubleshooting-mode.php` (52KB) — WordPress core's own Health Check troubleshooting mode, left in mu-plugins

## Other active plugins of note

- **Security**: `wordfence` (8.2.2, active)
- **Caching/performance**: `wp-rocket` (3.18.2, active), `compressx` (image compression, active)
- **SEO**: `wordpress-seo` / Yoast (26.9, active)
- **Forms**: `contact-form-7` + `flamingo`, `wpforms-lite`
- **Email delivery**: `wp-mail-smtp` (active) — relevant for staging email-safety later
- **Cookie/consent**: `cookie-law-info`
- **Trust signals**: `trustpilot-reviews`
- **Analytics**: `google-site-kit`, `jetpack` + `jetpack-protect`
- **Popups**: `popup-builder`
- **Recipe/content**: `total-recipe-generator-el`

## Backups (on production)

**No active backup plugin found.** `duplicator`, `duplicator-pro`, and `wp-migrate-2-aws` are all present but **inactive**. This is a gap — see `docs/backup-plan.md` for how we'll cover this from our side (Lightsail snapshots + our own file/DB backup process) without relying on any of these plugins.

## Disk usage (informational)

- `wp-content/uploads`: 2.4GB
- `wp-content/cache`: 270MB (wp-rocket cache — never needs backing up or committing)

## Risky dependencies / things to be careful about

1. **No child theme** — all custom theme code lives directly in `hello-elementor`. A theme update would be destructive to custom code. Do not update this theme without a full diff/backup first, and do not assume future Elementor "Hello" updates are safe to auto-apply.
2. ~~`functions1.php` / `functions2.php` status is unclear~~ — **Resolved**: confirmed dead code (see above), not a blocker.
3. **Checkout/cart/account WooCommerce template overrides already exist** in the theme — any page-template work must not disturb these files.
4. **Live payment gateways + active subscriptions** — this store takes real recurring payments. Reinforces safety rule 4 (never touch checkout/cart/payments/orders without explicit approval) and means staging must force gateways to test mode before any testing.
5. **Elementor core is several minor versions behind Elementor Pro's available update line** — don't bundle an Elementor update into unrelated work.
6. **No active backup plugin on production** — our own backup process (Phase 2) is the only safety net until/unless the user wants one enabled.

## Recommended modernization order

1. Local backup of theme + custom plugin code (Phase 2/4) — no risk, no production impact.
2. Stand up staging from a Lightsail snapshot (Phase 6) so all further testing happens off production.
3. Resolve the `functions1.php`/`functions2.php` question on staging (read the files, trace references) before any refactor.
4. Establish local dev (Phase 7) so UI work on the corporate page can be iterated on quickly before ever touching staging.
5. Redesign the one corporate page on staging first (Phases 8–12), explicitly avoiding the WooCommerce-touching files listed above.
6. Only after the corporate page workflow is proven out, consider a longer-term plan for moving the rest of the customizations into a proper child theme / custom plugin structure (separate future effort, not in scope now).

## Still needed from the user

- Corporate page URL (not yet identified — needed to start Phase 8/9)
- Confirmation on whether email sending should be disabled on staging (assumed yes, since WooCommerce Subscriptions is active and would otherwise send real renewal/order emails from a staging copy)
- Whether a staging instance already exists (not yet checked — no separate staging SSH alias found locally)
