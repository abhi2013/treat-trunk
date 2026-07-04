# Staging Plan — treattrunk.co.uk

## Current status: staging instance created (2026-07-03/04)

User created `treattrunk-staging` from a production snapshot via the Lightsail console. Details:

- Public IP: `35.177.198.77` (static, attached 2026-07-04 — was `35.178.179.3` before; the old dynamic IP was released back to AWS's pool and is now fully unreachable, so update any reference to the old IP)
- SSH: alias `treat-trunk-staging` in `~/.ssh/config` (same user `bitnami`, same key `~/.ssh/treattrunk3.pem` — the key was already trusted because it's baked into the snapshot's disk image); the `Hostname` was updated to the new static IP after it was attached
- Confirmed genuinely separate from production: different internal hostname (`ip-172-26-11-51` vs production's `ip-172-26-4-189`)
- DNS: `staging.treattrunk.co.uk` already has an A record (not set up as part of this plan — pre-existing/set up independently), but as of 2026-07-04 it resolves to **both** the new correct IP and the old dead one (`dig +short staging.treattrunk.co.uk` returns both). **Action needed**: remove the stale `35.178.179.3` A record so only `35.177.198.77` remains — otherwise DNS round-robin will intermittently send visitors to a dead address.

### Critical post-creation safety pass (completed 2026-07-04)

A snapshot-cloned instance boots with **production's live database and live credentials still in it**. Before any further use, this was verified and fixed:

- **WP-Cron**: was NOT disabled by default — any stray HTTP hit to the instance could trigger it, and 31 overdue Action Scheduler jobs (WooCommerce Subscriptions-related) were queued. Fixed: added `define('DISABLE_WP_CRON', true);` to staging's `wp-config.php`.
- **Payment gateways**: Stripe was `enabled=yes, testmode=no` with a live secret key present. WooCommerce Payments was `enabled=yes`. **Gotcha**: WooCommerce Payments' sub-methods (Afterpay/Clearpay, Klarna, Apple Pay, Google Pay) are stored as **separate gateway objects with their own settings**, under option names `woocommerce_{gateway_id}_settings` (not the bare gateway ID) — disabling the parent `woocommerce_payments` gateway does NOT disable these; each had to be disabled individually. All payment gateways are now confirmed `enabled=no` (verified via WooCommerce's own runtime `WC()->payment_gateways()->payment_gateways()` list, not just raw options).
- **Still open**: `wp-mail-smtp` likely still holds production's real SMTP credentials — transactional emails could still send for real if manually triggered in wp-admin. Not yet fixed (see step 9 below).

Given this experience: **any future staging rebuild from a fresh snapshot must repeat this exact safety pass before any interactive testing** — it is not a one-time setup step, it's required after every snapshot-based (re)creation.

### URL migration to staging domain (completed 2026-07-04)

Ran `wp search-replace 'https://treattrunk.co.uk' 'https://staging.treattrunk.co.uk' --precise --skip-columns=guid` on staging — 14,369 replacements across `wp_options`, `wp_postmeta`, `wp_posts`, `wp_comments`, `wp_usermeta`, `wp_users`, and WP Rocket's own cache/URL tables.

Two gotchas hit immediately after, both now fixed:

1. **`siteurl` DB row was stored as `http://treattrunk.co.uk` (no `s`)** — the `https://` search string didn't match it, so it was skipped. Fixed with a direct `wp option update siteurl`.
2. **`wp-config.php` had `WP_SITEURL`/`WP_HOME` hardcoded** to `https://treattrunk.co.uk/` (a Bitnami pattern for reliability) — these constants **override the DB entirely**, which is why the site kept reporting/redirecting to the production domain (visiting the staging IP directly was silently redirecting to production) even after the DB search-replace succeeded. Fixed by editing both constants in `wp-config.php` to `https://staging.treattrunk.co.uk/`.

**Lesson for next time**: on a Bitnami WordPress instance, always check `wp-config.php` for hardcoded `WP_SITEURL`/`WP_HOME` constants *before or immediately after* running `search-replace` — the DB change alone is not sufficient and the mismatch symptom (redirects to the old domain) is easy to misread as a DNS/caching problem instead.

### PHP-FPM OPcache masking the wp-config.php fix (found and fixed 2026-07-04)

After editing `wp-config.php`'s `WP_SITEURL`/`WP_HOME` constants (above), the site **still redirected to production** when tested over real HTTP (`curl` showed a genuine server-side `301`, header `x-redirect-by: WordPress`, `location: https://treattrunk.co.uk/`) — even though WP-CLI and the raw DB both already showed the correct staging values. Root cause: **PHP-FPM's OPcache had the old compiled `wp-config.php` cached in memory** and doesn't automatically notice on-disk edits. Fixed with `sudo /opt/bitnami/ctlscript.sh restart php-fpm`. Confirmed fixed via `curl` — `HTTP/2 200`, all URLs in the response (REST API discovery link, shortlink) correctly showing `staging.treattrunk.co.uk`.

**Lesson for next time**: whenever `wp-config.php` (or any PHP file affecting global config) is edited directly on a Bitnami instance, restart `php-fpm` afterward — don't assume the on-disk edit takes effect immediately. Verifying via WP-CLI alone isn't sufficient to catch this, since WP-CLI runs as its own PHP process and may not exhibit the same stale-cache symptom as the long-running PHP-FPM pool serving real HTTP requests; verify via an actual `curl`/HTTP request too.

**How to preview staging right now**: DNS already resolves `staging.treattrunk.co.uk` (once the stale record above is removed), so no `/etc/hosts` override should be needed. Expect a browser SSL certificate warning (the Let's Encrypt cert on that instance was issued for `treattrunk.co.uk`, not the staging subdomain, since no cert has been issued for staging yet) — safe to click through for internal testing only; not to be treated as a real trust boundary.

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
