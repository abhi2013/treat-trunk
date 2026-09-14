#!/bin/bash
# SEO weekly actions 2026-09-14. Run ON the prod server. DRY_RUN=1 = build + diff only, no writes.
set -euo pipefail
W="sudo /opt/bitnami/wp-cli/bin/wp --allow-root --path=/opt/bitnami/wordpress"
DRY=${DRY_RUN:-1}
B=/home/bitnami/backups/2026-09-14-seo; T=/tmp/seo-0914
mkdir -p "$B" "$T"

# 1. backups (always, cheap)
for id in 41958 40571 37444 41103; do $W post get $id --field=post_content > "$B/$id-content.before.html"; done
for id in 54675 40571 41103; do
  echo "title=$($W post meta get $id _yoast_wpseo_title 2>/dev/null || true)" > "$B/$id-yoast.before.txt"
  echo "desc=$($W post meta get $id _yoast_wpseo_metadesc 2>/dev/null || true)" >> "$B/$id-yoast.before.txt"
done
echo "backups in $B:"; ls -la "$B"

# 2. build patched content + show diffs
for id in 41958 40571 37444 41103; do
  python3 "$T/patch.py" "$B/$id-content.before.html" "$T/snip-$id.html" "$T/$id-content.after.html" "$id"
  diff -u "$B/$id-content.before.html" "$T/$id-content.after.html" | head -60 || true
done

if [ "$DRY" = "1" ]; then echo "DRY RUN - nothing written"; exit 0; fi

# 3. write content (wp_update_post -> WP Rocket purges these posts itself)
for id in 41958 40571 37444 41103; do $W post update $id "$T/$id-content.after.html"; done

# 4. Yoast titles / descriptions (postmeta; does NOT purge cache)
$W post meta update 54675 _yoast_wpseo_title    "Best Snack Box Subscription UK 2026: 6 Boxes Compared | Treat Trunk"
$W post meta update 54675 _yoast_wpseo_metadesc "6 UK snack box subscriptions compared: Treat Trunk, Graze, LifeBox, Nutribox, SnackVerse and TokyoTreat. Price, snacks per box and who each suits, from £15.99."
$W post meta update 40571 _yoast_wpseo_title    "5 Best Snack Boxes for Your Commute + Snack Box Subscription | Treat Trunk"
$W post meta update 40571 _yoast_wpseo_metadesc "The 5 best snack boxes to carry on your commute, from bamboo to multi-compartment, plus a healthy snack box subscription to fill them from £15.99 a month."
$W post meta update 41103 _yoast_wpseo_title    "5 Reasons to Send Corporate Christmas Gifts to Staff in 2026 | Treat Trunk"

# 5. purge WP Rocket page cache for the 5 URLs (meta updates leave it stale)
C=/opt/bitnami/wordpress/wp-content/cache/wp-rocket/treattrunk.co.uk
for slug in best-snack-subscription-boxes-uk 10-nutritionist-approved-healthy-snack-ideas-for-menopause 5-of-the-best-snack-boxes-to-take-on-your-morning-commute the-best-corporate-wellbeing-gifts-to-support-your-employees-healthwellness 5-benefits-to-sending-the-perfect-corporate-christmas-gift-to-your-employees; do
  sudo find "$C/$slug" -type f -delete 2>/dev/null || true
done
echo DEPLOYED
