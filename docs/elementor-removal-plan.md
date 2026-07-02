# Elementor Removal Plan — Corporate Page Only

Scope: this plan covers **only** `https://treattrunk.co.uk/corporate-orders/`.
No other Elementor page on the site is touched. Elementor and Elementor Pro
remain installed/active site-wide — this is not a site-wide Elementor
migration.

## Why this page, why now

The corporate page is the one the user wants modernized first. It's a good
candidate to de-Elementor because (per `frontend-techniques.md`): it uses only
stock Elementor/WooCommerce widgets (nothing exotic), its one real dynamic
piece (the ActiveCampaign form) is a shortcode embed that works identically
outside Elementor, and it's content-led rather than interactive — a strong
fit for a plain server-rendered PHP template (see `corporate-page-redesign.md`
implementation method decision).

## What "removal" actually means here

Not deleting the Elementor content — **switching which template renders the
page**, while keeping the Elementor data intact and reversible:

1. The existing Elementor design stays stored on the `corporate-orders` post (`_elementor_data`, `_elementor_edit_mode`, etc.) — untouched.
2. A new custom page template is created and assigned to the page (`_wp_page_template` meta) on **staging only**.
3. When a custom page template is active, Elementor does not render its builder output for that page (Elementor respects `_wp_page_template` — assigning a real template file effectively bypasses the Elementor Canvas/builder render for that page). This is a standard, reversible technique — no plugin uninstall, no data loss.
4. **Rollback**: reset `_wp_page_template` back to `default` (or whatever Elementor's canvas template is) and the original Elementor-built page reappears exactly as it was, since nothing was deleted.

## What must be preserved exactly

- The `[activecampaign form=1 css=1]` shortcode embed (both inline and in the "Newsletter Sign Up Pop Up", post ID 43205) — the popup itself lives as a separate `elementor_library` post and is unaffected by changing the corporate page's own template, but must be re-tested to confirm it still triggers correctly from the new template's CTA markup.
- URL slug `corporate-orders` — unchanged, since we're changing the template, not the post/slug.
- Yoast SEO title/meta description — carried into the new template's `<head>` output (or left to Yoast's own hooks, which work independently of Elementor).

## What is explicitly out of scope

- Any other Elementor-built page on the site.
- The global Elementor Kit (`elementor-kit-14303`), header/footer, nav menu — these stay Elementor-rendered and shared site-wide.
- Elementor/Elementor Pro plugin updates — not part of this effort (see the version-gap risk noted in `site-inventory.md`).
- Any WooCommerce checkout/cart/account template — those Elementor/theme overrides are untouched (safety rule 4).

## Risk notes

- Because the active theme (`hello-elementor`) has no child theme, the new page template file needs a home that doesn't add to the existing risk. Preference: a small custom plugin (under `corporate-ui/` in this repo) registering the template via `theme_page_templates`/`template_include`, rather than dropping a new file directly into the hand-edited `hello-elementor` theme.
- The `functions1.php`/`functions2.php` question (see `site-inventory.md`) is resolved — confirmed dead code, not a factor in this decision.
- All of this happens on **staging first**; nothing here is applied to production without a separate, explicit approval after staging verification (`docs/testing-checklist.md`).
