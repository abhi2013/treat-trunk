# Backup Plan — treattrunk.co.uk

Production has **no active backup plugin** (see `docs/site-inventory.md`), so
backups are our responsibility until/unless the user turns one on. Two
independent layers, used together:

1. **Lightsail snapshot** — whole-instance, disk-level, fastest full restore, done from the AWS Lightsail console (manual, not automated by this workflow).
2. **Local file + sanitized DB backups** — targeted, versionable-adjacent, used to seed staging and local dev and to diff custom code over time.

Nothing in this document has been executed yet. File copies require approval (Phase 4); database export requires separate, explicit approval (Phase 5).

## 1. Lightsail snapshot (manual, console-based)

1. AWS Console → Lightsail → Instances → select the production instance.
2. "Snapshots" tab → "Create snapshot".
3. Name it with a date, e.g. `treattrunk-prod-2026-07-02`.
4. Snapshots are billed for storage — periodically prune old ones from the console.

**Restore from snapshot**: Lightsail console → Snapshots → select snapshot → "Create new instance". This creates a **new** instance from the snapshot; it does not overwrite the original. To actually roll production back, you'd need to either re-point the static IP to the new instance or manually restore files/DB from it — this is a deliberate, human-driven decision, never something to script silently.

## 2. Local file backups

Directory structure (created under this repo root, but excluded from Git via `.gitignore`):

```
backups/
  production/
    2026-07-02-2200/
      themes/
      plugins/
      database/
      reports/
```

Each timestamped folder is one backup run. `reports/` holds a plain-text manifest (what was copied, source paths, file counts/sizes, checksums) so a backup's completeness can be verified without re-connecting to the server.

### What gets copied (once approved)

- `wp-content/themes/hello-elementor` (the active, hand-edited theme)
- `wp-content/themes/my-custom-theme` (inactive but tiny — cheap to keep)
- `wp-content/plugins/treattrunk-welcome-box`
- `wp-content/plugins/subscription-reporting`
- `wp-content/plugins/woo-update-manager`
- `wp-content/mu-plugins/health-check-troubleshooting-mode.php`

### What does NOT get copied without separate explicit approval

- `wp-content/uploads/` (2.4GB — media library; large, mostly not custom code, may contain customer-uploaded content)
- `wp-content/cache/` (270MB — regenerable wp-rocket cache, no value in backing up)
- Any database dump
- `wp-config.php`, `.env`, any `.pem`/`.key` files, credentials of any kind
- Third-party plugin folders (WooCommerce, Elementor, Wordfence, etc.) — these are documented by name+version in the inventory instead of copied, since they're reinstallable from the plugin repo/vendor

### Example commands (dry-run shown; nothing runs until approved)

```bash
# Dry run first — rsync --dry-run, no files transferred
rsync -avzn --delete \
  -e "ssh -i ~/.ssh/treattrunk3.pem" \
  bitnami@13.43.130.225:/opt/bitnami/wordpress/wp-content/themes/hello-elementor/ \
  ./backups/production/<timestamp>/themes/hello-elementor/

# Real run (only after dry-run output is reviewed and approved)
rsync -avz \
  -e "ssh -i ~/.ssh/treattrunk3.pem" \
  bitnami@13.43.130.225:/opt/bitnami/wordpress/wp-content/themes/hello-elementor/ \
  ./backups/production/<timestamp>/themes/hello-elementor/
```

Same pattern for the custom plugins, swapping the source path.

## 3. Database export (separate approval gate — see Phase 5 plan)

Not run yet. When approved:

```bash
sudo wp db export --path=/opt/bitnami/wordpress --allow-root \
  /home/bitnami/backups/db-<timestamp>.sql
scp -i ~/.ssh/treattrunk3.pem \
  bitnami@13.43.130.225:/home/bitnami/backups/db-<timestamp>.sql \
  ./backups/production/<timestamp>/database/
```

The dump contains real customer/order data (live Stripe/WooCommerce Payments orders, subscriptions). It must be **sanitized before it's usable anywhere except a locked-down restore**, e.g.:

- Replace customer emails/names/addresses with fake data (WP-CLI has no built-in anonymizer; a small one-off script or a tool like `wp-cli-anonymize` — **would need approval to install** — or manual `wp db query` `UPDATE` statements scoped only to `wp_users`/`wp_postmeta` order fields, run against the **copy**, never production).
- Strip or replace API keys/tokens stored in `wp_options` (Stripe/WooCommerce Payments keys, SMTP credentials, ActiveCampaign/Pinterest/etc. API keys).
- Never commit the raw or sanitized dump to Git — store only in the ignored `backups/` folder.

## 4. Restore procedures

- **Restore files**: reverse the rsync direction, copying from `backups/production/<timestamp>/themes/...` back to the server path — always with `--dry-run` reviewed first, always to **staging**, never directly to production without explicit approval.
- **Restore database**: `wp db import <file>.sql` against a target site's DB — staging or local only, never production, and only after confirming which environment is being imported into (`wp option get siteurl` before AND after).
- **Restore from Lightsail snapshot**: see section 1 — creates a new instance, does not silently overwrite anything.

## 5. Verifying backup integrity

- After any file copy: `find <backup-dir> -type f | wc -l` and compare against the same count run on the server (`ssh treat-trunk 'find <path> -type f | wc -l'`).
- Record file counts and a top-level `du -sh` in the `reports/` manifest for each backup run.
- For DB dumps: confirm the file is non-empty, contains `CREATE TABLE` statements for the expected core tables (`wp_options`, `wp_posts`, `wp_users`), and that `wp db import` against a scratch/local DB succeeds without error before trusting the dump.

## Never committed to Git (enforced via `.gitignore`)

`wp-config.php`, `.env`/`*.env`, `*.pem`/`*.key`, `*.sql`/`*.sql.gz`, `*.zip`/`*.tar`/`*.tar.gz`, `node_modules/`, `vendor/`, `uploads/`, `cache/`, `backup*/`, `backups/`, `ai1wm-backups/`, `updraft/`, `wp-content/cache/`, `wp-content/uploads/`, `.DS_Store`.
