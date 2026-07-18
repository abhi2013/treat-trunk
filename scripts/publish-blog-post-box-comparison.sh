#!/usr/bin/env bash
# Publishes the "Best UK Snack Subscription Boxes in 2026, Compared" post -
# written 2026-07-18 to contest the comparison-intent keyword space
# ("best snack subscription box UK") that SnackVerse's single blog post
# currently targets uncontested (see the 2026-07-18 competitor audit).
# Run once per environment via WP-CLI, from the WordPress root.
#
# Usage: bash scripts/publish-blog-post-box-comparison.sh
#
# Not idempotent for post creation - re-running creates a second post. Only
# the meta updates at the bottom are safe to re-run.

set -euo pipefail

WP="sudo wp --allow-root"
CONTENT_FILE="$(dirname "$0")/../content/blog-posts/best-snack-subscription-boxes-uk.html"

POST_ID=$($WP post create "$CONTENT_FILE" \
  --post_title="The Best UK Snack Subscription Boxes in 2026, Compared" \
  --post_name="best-snack-subscription-boxes-uk" \
  --post_type=post \
  --post_status=publish \
  --post_author=6609 \
  --porcelain)

$WP post meta update "$POST_ID" _yoast_wpseo_metadesc \
  "An honest comparison of the best UK snack subscription boxes in 2026: healthy, world, gluten-free and portion-controlled options, and how to pick the right one."

$WP post meta update "$POST_ID" _yoast_wpseo_focuskw "best snack subscription box UK"

echo "Published as post ID $POST_ID"
