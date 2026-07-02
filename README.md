# treattrunk-corporate

Working repo for modernizing selected pages on the treattrunk.co.uk
WordPress + WooCommerce + Elementor site, hosted on AWS Lightsail.

This repo does **not** contain the full production site. It contains:

- `docs/` — inventory, backup, staging, local-dev, and redesign planning docs
- `theme/` — custom theme code pulled from production (child theme / hand-edited theme code), not third-party themes
- `plugins/` — custom, first-party plugin code pulled from production, not third-party plugins
- `site-core/` — any new shared/core custom plugin code created during this project
- `corporate-ui/` — new corporate page redesign code

Third-party themes and plugins (WooCommerce, Elementor, Wordfence, etc.) are
**documented by name and version** in `docs/site-inventory.md`, not vendored
into this repo.

## Safety rules

See the project's standing safety rules (production is never modified without
explicit per-action approval; WooCommerce checkout/cart/payments/orders are
never touched without explicit approval; no secrets, DB dumps, uploads, or
credentials are ever committed — enforced via `.gitignore`).

## Start here

- `docs/site-inventory.md` — what's actually on production today
- `docs/backup-plan.md` — how backups work for this project
- `docs/staging-plan.md` — staging environment plan (in progress)
- `docs/local-dev.md` — local development setup (in progress)
