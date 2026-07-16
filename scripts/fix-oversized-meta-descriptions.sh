#!/usr/bin/env bash
# Trims 3 Yoast meta descriptions that were running 400-470 characters
# (2.5-3x the ~155-char limit Google displays before truncating), found
# during the 2026-07-16 SEO/GEO/AEO audit. Run once per environment via
# WP-CLI, from the WordPress root (see docs/site-inventory.md for the
# path/SSH alias for each environment).
#
# Usage: sudo wp-cli-context bash scripts/fix-oversized-meta-descriptions.sh
# (i.e. run this ON the server, in the WordPress root, as the user that can
# run `wp --allow-root`)
#
# Idempotent - safe to re-run, just overwrites the same 3 values again.

set -euo pipefail

WP="sudo wp --allow-root"

$WP post meta update 37 _yoast_wpseo_metadesc \
  "The story behind Treat Trunk: how Sally's own sugar-addiction journey became a UK healthy snack box, now delivering guilt-free treats nationwide."

$WP post meta update 7013 _yoast_wpseo_metadesc \
  "Give the gift of health: vegan-friendly snack boxes for birthdays, new mums, corporate gifting or any occasion. One-off or subscription options."

$WP post meta update 42001 _yoast_wpseo_metadesc \
  "Treat Trunk in the media: news, features and press coverage of our healthy, vegan-friendly snack box subscriptions and corporate snacking service."

echo "Done. Verify with: wp post meta get 37 _yoast_wpseo_metadesc"
