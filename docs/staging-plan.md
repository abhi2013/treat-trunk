# Staging Plan — treattrunk.co.uk

## Current status: staging instance created (2026-07-03/04)

User created `treattrunk-staging` from a production snapshot via the Lightsail console. Details:

- Public IP: `35.178.179.3`
- SSH: alias `treat-trunk-staging` added to `~/.ssh/config` (same user `bitnami`, same key `~/.ssh/treattrunk3.pem` — the key was already trusted because it's baked into the snapshot's disk image)
- Confirmed genuinely separate from production: different internal hostname (`ip-172-26-11-51` vs production's `ip-172-26-4-189`)
- DNS (`staging.treattrunk.co.uk`) not yet pointed at it — still pending, see Open Questions
- Static IP not yet attached (currently using whatever IP Lightsail assigned on creation)

### Critical post-creation safety pass (completed 2026-07-04)

A snapshot-cloned instance boots with **production's live database and live credentials still in it**. Before any further use, this was verified and fixed:

- **WP-Cron**: was NOT disabled by default — any stray HTTP hit to the instance could trigger it, and 31 overdue Action Scheduler jobs (WooCommerce Subscriptions-related) were queued. Fixed: added `define('DISABLE_WP_CRON', true);` to staging's `wp-config.php`.
- **Payment gateways**: Stripe was `enabled=yes, testmode=no` with a live secret key present. WooCommerce Payments was `enabled=yes`. **Gotcha**: WooCommerce Payments' sub-methods (Afterpay/Clearpay, Klarna, Apple Pay, Google Pay) are stored as **separate gateway objects with their own settings**, under option names `woocommerce_{gateway_id}_settings` (not the bare gateway ID) — disabling the parent `woocommerce_payments` gateway does NOT disable these; each had to be disabled individually. All payment gateways are now confirmed `enabled=no` (verified via WooCommerce's own runtime `WC()->payment_gateways()->payment_gateways()` list, not just raw options).
- **Still open**: `wp-mail-smtp` likely still holds production's real SMTP credentials — transactional emails could still send for real if manually triggered in wp-admin. Not yet fixed (see step 9 below).

Given this experience: **any future staging rebuild from a fresh snapshot must repeat this exact safety pass before any interactive testing** — it is not a one-time setup step, it's required after every snapshot-based (re)creation.

## If staging does not exist: creation steps (Lightsail console, manual)

1. **Snapshot production first** (see `docs/backup-plan.md` §1) so staging is created from a known-good, current copy — not from scratch.
2. AWS Console → Lightsail → Snapshots → select the production snapshot → **"Create new instance"**.
3. Give the new instance a clear name, e.g. `treattrunk-staging`.
4. Choose the same or a smaller instance plan (staging doesn't need production-grade capacity).
5. **Static IP**: Lightsail console → Networking → "Create static IP" → attach it to the new staging instance. This gives staging a stable IP even if the instance is stopped/started.
6. **DNS**: point a subdomain (e.g. `staging.treattrunk.co.uk`) to the staging static IP via an `A` record in whatever DNS provider manages `treattrunk.co.uk`. This is a live DNS change — confirm the exact subdomain name with the user before creating the record.
7. **Block search engine indexing** on staging:
   - WordPress admin → Settings → Reading → check "Discourage search engines from indexing this site" (`wp option update blog_public 0`, staging only, never production).
   - Additionally add a staging-only `X-Robots-Tag: noindex` header or `robots.txt` disallow-all at the web server level, since some crawlers ignore the WP setting.
8. **Password-protect staging** (optional but recommended for a site handling real-looking WooCommerce data): either Apache Basic Auth on the vhost, or a security plugin's "coming soon"/maintenance mode restricted to logged-in/allow-listed IPs.
9. **Stop real transactional emails**: install/configure a mail-catching approach so staging never sends real customer emails — e.g. reconfigure `wp-mail-smtp` (already active) to point at a safe testing inbox, or use a plugin like "Disable Emails" (new plugin — needs approval), or block outbound SMTP at the instance's security group level.
10. **Payment gateways to test mode**: in WooCommerce → Settings → Payments, switch Stripe (`woocommerce-gateway-stripe`) and WooCommerce Payments to their sandbox/test-mode credentials on staging only. Never change production's payment settings as part of this.
11. **Verify staging works**: load the staging domain, confirm `wp option get siteurl`/`home` match the staging domain (not production), confirm a test WooCommerce checkout completes in test mode without a real charge, confirm no email actually lands in a real inbox.

## If staging already exists

Once the user provides connection details:

1. Verify SSH connection (read-only) the same way production was verified.
2. Confirm the WordPress path via the same `wp-config.php` existence checks used on production.
3. **Verify it is not actually production** — compare `wp option get siteurl` against the known production URL (`https://treattrunk.co.uk`) and compare the instance's public IP against `13.43.130.225`. This check must pass before any write action ever runs against "staging."
4. Confirm indexing is blocked (`wp option get blog_public` should be `0`).
5. Confirm payment gateways are in test mode and email sending is safely diverted, per steps 9–10 above.

## Database sync to staging

Covered in detail in the Phase 5 DB export/sanitization plan (separate doc/approval). Summary: production DB is exported, sanitized (customer PII and API keys/secrets stripped or replaced), imported into staging, then `wp search-replace` is run **on staging only** to rewrite the production domain to the staging domain.

## Open questions for the user

- Does staging already exist? (see above — genuinely unknown from this machine)
- Desired staging subdomain, if it needs to be created
- Who manages DNS for `treattrunk.co.uk` (needed before any DNS step)
- Preference: instructions-only for staging creation, or should commands be run once each step is approved
