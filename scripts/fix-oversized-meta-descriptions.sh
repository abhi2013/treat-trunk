#!/usr/bin/env bash
# Fixes 4 Yoast meta descriptions found during the 2026-07-16 SEO/GEO/AEO
# audit. Run once per environment via WP-CLI, from the WordPress root (see
# docs/site-inventory.md for the path/SSH alias for each environment).
#
# Usage: sudo wp-cli-context bash scripts/fix-oversized-meta-descriptions.sh
# (i.e. run this ON the server, in the WordPress root, as the user that can
# run `wp --allow-root`)
#
# Idempotent - safe to re-run, just overwrites the same 4 values again.

set -euo pipefail

WP="sudo wp --allow-root"

# Pages 37/7013/42001: trims descriptions that were running 400-470
# characters (2.5-3x the ~155-char limit Google displays before
# truncating).

$WP post meta update 37 _yoast_wpseo_metadesc \
  "The story behind Treat Trunk: how Sally's own sugar-addiction journey became a UK healthy snack box, now delivering guilt-free treats nationwide."

$WP post meta update 7013 _yoast_wpseo_metadesc \
  "Give the gift of health: vegan-friendly snack boxes for birthdays, new mums, corporate gifting or any occasion. One-off or subscription options."

$WP post meta update 42001 _yoast_wpseo_metadesc \
  "Treat Trunk in the media: news, features and press coverage of our healthy, vegan-friendly snack box subscriptions and corporate snacking service."

# Page 80 (/our-mission-values/): the old description claimed "Subscribers
# of our snack boxes provide Share the meal Charity with a donation" - not
# a real feature (confirmed by the user 2026-07-16), and it never appeared
# anywhere in the page's actual visible content, only duplicated across
# metadata (meta description, og:description, the Yoast schema graph's
# description field, which all derive from this one postmeta value).
# Replaced with an accurate description of what's really on the page.

$WP post meta update 80 _yoast_wpseo_metadesc \
  "Treat Trunk's mission and values: healthier alternatives for everyone, ethical brand partnerships, and support for small independent UK snack businesses."

echo "Done. Verify with: wp post meta get 80 _yoast_wpseo_metadesc"
