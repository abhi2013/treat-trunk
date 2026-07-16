#!/usr/bin/env bash
# Publishes the "Healthy Office Snack Ideas for 2026" post - written
# 2026-07-16 to revive the blog (dormant since May 2025, see the SEO/GEO/AEO
# audit) and target the same "office snacks" search intent Snackfully's
# active blog is already competing for. Run once per environment via
# WP-CLI, from the WordPress root.
#
# Usage: bash scripts/publish-blog-post-office-snacks.sh
#
# Not idempotent for post creation - re-running creates a second post. Only
# the meta description update at the bottom is safe to re-run.

set -euo pipefail

WP="sudo wp --allow-root"
CONTENT_FILE="$(dirname "$0")/../content/blog-posts/healthy-office-snack-ideas-2026.html"

POST_ID=$($WP post create "$CONTENT_FILE" \
  --post_title="Healthy Office Snack Ideas for 2026: What Actually Keeps a Team Fuelled" \
  --post_name="healthy-office-snack-ideas-2026" \
  --post_type=post \
  --post_status=publish \
  --post_author=6609 \
  --porcelain)

$WP post meta update "$POST_ID" _yoast_wpseo_metadesc \
  "Real healthy office snack ideas for 2026: what to stock, what to skip, and how to keep a team fuelled without the 3pm sugar crash."

$WP post meta update "$POST_ID" _yoast_wpseo_focuskw "healthy office snacks"

echo "Published as post ID $POST_ID"
