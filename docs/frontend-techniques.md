# Frontend Techniques — Corporate Page

Page: `https://treattrunk.co.uk/corporate-orders/` (post ID `36634`, WordPress
page, slug `corporate-orders`)

Analysis is based on read-only inspection: the public HTML response (`curl`,
no login) and page/meta data via WP-CLI (read-only) on 2026-07-02. No changes
were made to the live page.

## Current implementation

- **Elementor** page builder, edit mode `builder`, page-level Elementor data
  ~10KB. `_elementor_version` recorded on the page is `3.6.5`, well behind the
  currently installed Elementor core (`3.35.4`) — the page hasn't been
  re-saved/touched in Elementor for a while.
- Theme: `hello-elementor` (the hand-edited active theme — see `site-inventory.md`), page template `default`.
- Uses the site's global Elementor Kit (`elementor-kit-14303`), so it inherits global colors/typography/spacing from Elementor's Site Settings, not page-specific overrides.

## Elementor widgets in use on this page

`heading`, `text-editor`, `icon-list`, `icon`, `image`, `button`, `spacer`, `slides` (a slider/carousel), `shortcode`, `nav-menu`, `theme-site-logo`, `woocommerce-menu-cart`.

No custom/third-party Elementor widgets beyond stock Elementor + Elementor Pro + WooCommerce's own widgets (nav menu, site logo, mini cart) — this is a good sign for portability; nothing exotic to replicate.

## Content structure (from headings found, top to bottom)

1. Hero: **"Yummy Healthy Snacks For Your Staff, Team or Clients"** (H2-styled via Elementor heading widget — see accessibility note below)
2. **"What our corporate healthy snack gift boxes offer..."** — followed by an icon-list of 8 benefit bullets (tailoring, bulk/individual delivery, no minimum order, bespoke branded packaging, budget flexibility, wellbeing/vegan-as-standard snacks, dietary requirements/gluten-free, support for independent snack brands)
3. **"What our corporate clients say..."** — testimonial section (likely the `slides` widget carousel)
4. **"For Corporate Discounts - Simply Contact Us Below"** — CTA section
5. Lead-capture form area, anchored at `#corporate-form`
6. **"Sign up to our newsletter and receive a 10% discount code off your first box!"** — secondary newsletter CTA
7. **"Our Products" / "Discover"** — product cross-sell section
8. Footer nav (shared site-wide): FAQ, Delivery & Returns, Blog, Refer a Friend, Contact Us, About Us, Our Mission & Values, Our Snacks

## Forms / lead capture — preserve exactly

- Three CTA buttons point to `#corporate-form` (in-page anchor scroll)
- One CTA button opens an **Elementor popup** (`popup id 43205`) — content not yet inspected, needs a look during implementation
- One CTA button links directly to an **external ActiveCampaign-hosted form**: `https://treattrunk.activehosted.com/f/1`
- The `activecampaign-for-woocommerce` and `activecampaign-subscription-forms` plugins are active site-wide (see inventory) — the on-page form is very likely an ActiveCampaign embed/shortcode, not a native WordPress form plugin (no WPForms/CF7 markup found specific to this page's own content)
- **This is a live marketing lead-gen integration** — must be preserved and re-tested working (not just visually present) in any redesign, on staging first

## Shortcodes

At least one `elementor-widget-shortcode` block is used — likely rendering the ActiveCampaign form or a WooCommerce-related shortcode. Exact shortcode content not yet pulled (requires reading the Elementor JSON `_elementor_data`, deferred to implementation phase to avoid over-fetching page internals during the audit pass).

## Custom CSS / JS

- No page-specific custom CSS/JS found on this page beyond the theme-wide `custom.css`/`custom.js` in `hello-elementor` (see inventory) and Elementor's own generated CSS.
- Heavy reliance on **WP Rocket** for asset optimization: images are served as AVIF via `compressx`, critical hero image is preloaded (`rel="preload" ... fetchpriority="high"`), most other images/assets are lazy-loaded via WP Rocket's own JS (`data-lazy-src` / `data-rocket-src` attributes) rather than native `loading="lazy"`.

## Theme/header/footer dependency

- Header/footer/nav menu and WooCommerce mini-cart are all theme-level Elementor widgets shared site-wide — a redesign of this one page does not need to touch header/footer, and must not accidentally override them.
- Body classes confirm: `page-template-default`, `wp-theme-hello-elementor`, `elementor-default`, `elementor-kit-14303`, `woocommerce-no-js` (WooCommerce loaded even though this isn't a shop page — mini-cart widget in header).

## SEO — current state

- Title tag: `Corporate Orders | Treat Trunk | Office Staff Gifts`
- Yoast meta description: "Treat your staff whilst also keeping them healthy & sharp. Our healthy snack boxes are great for staff working remotely, Christmas, summer parties in the office or at home."
- Canonical URL present and correct.
- Viewport meta present (mobile-friendly base).
- **No `<h1>` element found anywhere on the page.** This is a real SEO/accessibility gap — Elementor heading widgets on this page are apparently not using an `h1` tag (likely all set to `h2`/`div` in the widget's HTML tag setting). Should be fixed in the redesign: exactly one clear `h1`.

## Performance concerns

- 76 `<script>` tags on a single page load — driven by the overall plugin stack (Elementor, Elementor Pro, WooCommerce, Wordfence, Jetpack, Popup Builder, Trustpilot, Cookie Law Info, Google Site Kit/GTM), not specific to this page's content. A React/JS-heavy rebuild would add to this rather than help; a server-rendered, low-JS page is the better direction for this specific page (see `corporate-page-redesign.md`).
- Full HTML response is 220KB before assets — on the heavier side for a single landing/lead-gen page.
- WP Rocket + compressx are already doing real optimization work (AVIF images, preloaded hero, deferred/lazy assets) — any redesign should keep working within that pipeline rather than bypassing it.

## Accessibility concerns

- Missing `<h1>` (see above) — a screen reader / SEO crawler currently has no single clear page heading.
- Elementor icon-list bullets and button links should be checked for proper semantic markup (`<ul>/<li>`, real `<a href>` vs `href="#"` JS-triggered popups) once implementation begins — the popup-triggering CTA (`href="#elementor-action..."`) is a common accessibility weak point in Elementor sites (relies on JS, no real destination, may not be keyboard/screen-reader friendly without ARIA handling from Elementor Pro's popup script).

## What should be preserved

- Exact URL (`/corporate-orders/`)
- Yoast SEO title/meta description (or an improved equivalent, not a regression)
- The ActiveCampaign lead-capture form integration and the external AC link
- The Elementor popup content (needs inspection before removal)
- All footer/header/nav — untouched, they're shared site-wide
- The core message and offer content (bulk/tailored corporate snack box gifting) — copy can be tightened but the substance (no minimum order, bespoke branding, dietary options, budget flexibility) is the actual value proposition and should carry through

## What can be replaced

- The Elementor-authored markup/CSS itself (widgets, generated inline styles) — candidate for a clean semantic HTML/PHP template or lightweight component structure (see `corporate-page-redesign.md` for the implementation-method decision)
- Missing `h1` — fix as part of any rebuild
- Visual polish/hierarchy — current page reads as a fairly plain Elementor stack (heading → text → icon-list → slider → CTA → form → newsletter → product grid) with room for a stronger hero, clearer visual hierarchy, and stronger trust signals
