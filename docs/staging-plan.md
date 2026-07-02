# Staging Plan — treattrunk.co.uk

## Current status: confirmed — staging does not exist yet (2026-07-02)

User confirmed no staging instance exists. Proceeding with the creation steps below. This is **console/instructions-only**: AWS CLI is installed locally but not authenticated, and per the standing safety rules it won't be used to create resources without separate explicit approval anyway — these are manual steps for the user to run in the Lightsail console.

Nothing in this document has been executed. Creating a new Lightsail instance, attaching a static IP, or changing DNS are all irreversible-ish, billed, or externally-visible actions — every step below requires explicit approval before being run, and AWS CLI will not be used to create resources without that approval (per the standing safety rules).

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
