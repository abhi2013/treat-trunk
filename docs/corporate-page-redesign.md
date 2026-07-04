# Corporate Page Redesign Strategy

Source page: `https://treattrunk.co.uk/corporate-orders/` (full audit in `docs/frontend-techniques.md`)

## Current page analysis

- **URL**: `/corporate-orders/` (to be preserved exactly — no redirect needed, no SEO equity lost)
- **Title**: `Corporate Orders | Treat Trunk | Office Staff Gifts`
- **Meta description**: "Treat your staff whilst also keeping them healthy & sharp. Our healthy snack boxes are great for staff working remotely, Christmas, summer parties in the office or at home."
- **H1**: none currently — a defect to fix, not a baseline to preserve
- **Page goal**: convert B2B/office buyers into a corporate-gifting lead (via ActiveCampaign form) or newsletter signup
- **Primary CTA (implicit today)**: "Simply Contact Us Below" → scroll to `#corporate-form` (ActiveCampaign form #1)
- **Secondary CTA**: newsletter popup, same ActiveCampaign form
- **Trust signals present**: a testimonial slider ("What our corporate clients say...") — content not yet extracted, needs pulling in implementation
- **Forms**: one ActiveCampaign shortcode form, used both inline and in a popup — must be preserved and re-verified functional
- **Section structure today**: hero → offer/benefits (8 bullets) → testimonials → CTA copy → lead form → newsletter CTA → product cross-sell → shared footer nav
- **Mobile/perf/accessibility concerns**: see `frontend-techniques.md` — missing H1, heavy script count (76 tags, site-wide plugin load not page-specific), CTA buttons that open popups via `href="#elementor-action..."` (potential keyboard/screen-reader friction)
- **Schema**: none detected in the quick audit — worth adding basic `Organization`/`Service` structured data as an improvement, not currently present to preserve

## Competitor benchmarking — not yet done

No competitor URLs have been provided. This section is a placeholder; ask the
user for corporate/B2B-gifting competitor pages if benchmarking is wanted
before finalizing copy/visual direction. The structure and recommendations
below are based on general landing-page best practice (clear single H1, one
primary CTA repeated, benefit-led copy, social proof near the CTA, mobile-first
layout) rather than direct competitor comparison.

## Recommended page structure (section-by-section)

1. **Hero** — one clear `h1` (e.g. a tightened version of "Yummy Healthy Snacks For Your Staff, Team or Clients"), one-line subhead, single primary CTA button scrolling to the lead form. Keep the existing preloaded hero image approach (WP Rocket already optimizes it).
2. **Offer/benefits** — condense the current 8 bullets into a scannable grid (e.g. 4–6 icon + short-label cards: tailored boxes, no minimum order, bespoke branding, dietary options, flexible budget, bulk or individual delivery). Same substance as today, tighter presentation.
3. **Social proof** — keep the testimonial carousel (or convert to a simple 2–3 card static layout if the testimonial count is small — check actual slide count during implementation), placed right before or after the benefits to build trust ahead of the ask.
4. **Primary CTA / lead form** — the `#corporate-form` section, keep the ActiveCampaign shortcode embed exactly as-is functionally, just restyle the surrounding section.
5. **Secondary conversion path** — newsletter signup, can stay as the existing popup (same ActiveCampaign form #1) rather than being rebuilt as a new component.
6. **Cross-sell** — "Our Products" section, unchanged in function.
7. **Footer** — untouched (shared, theme-level).

## Copy direction

- Lead with the outcome ("healthier, happier teams") before the mechanism (snack boxes).
- Keep the existing concrete differentiators (no minimum order, bespoke branded packaging, dietary/gluten-free options, budget flexibility, independent-brand sourcing) — these are real, specific claims, not generic filler; don't dilute them.
- One primary CTA verb repeated consistently (e.g. "Get a Quote" / "Talk to Us"), rather than mixing "Contact Us Below" / "Sign up" / popup triggers with different language for effectively the same ActiveCampaign form.

## Visual direction

- Establish clear visual hierarchy: one dominant H1, consistent heading scale, generous spacing (the current page reads as a flat stack of equally-weighted sections).
- Keep working within the existing brand's Elementor global kit colors/typography where reasonable, so the page doesn't look disconnected from the rest of the site — pull actual color/typography values from the Elementor kit (`elementor-kit-14303`) during implementation rather than guessing.

## CTA strategy

- Single consistent primary CTA (the ActiveCampaign form), reinforced 2–3 times down the page (hero, after benefits, before footer) rather than introducing new competing actions.
- Secondary/lower-commitment CTA (newsletter) kept but visually subordinate to the primary one.

## SEO improvements

- Add exactly one `h1`.
- Keep the existing Yoast title/meta description as a baseline; tighten if it improves clarity, but don't regress on the specific "corporate/office snack gifting" keyword focus already present.
- Add basic `Organization`/`Service`-type structured data if feasible within the chosen implementation method (nice-to-have, not blocking).

## Accessibility improvements

- Real `<h1>`, proper heading hierarchy (h2/h3 for subsections).
- Ensure the popup-triggering CTA is reachable and operable via keyboard, with appropriate ARIA attributes (Elementor Pro's popup JS should already handle much of this — verify rather than assume once rebuilt).
- Icon-list benefits as a real `<ul>/<li>` structure with decorative icons marked `aria-hidden`.

## Performance improvements

- Keep working with WP Rocket + compressx rather than around them (don't introduce a second, competing asset pipeline).
- Avoid adding new heavy JS frameworks/libraries for what is fundamentally a static, content-led landing page — see implementation method recommendation below.

## Implementation method — recommendation: **Option C (server-rendered WordPress template + CSS, no React)**

Reasoning:
- The page is content-led and SEO-focused (a B2B lead-gen landing page), not app-like or highly interactive — nothing here needs client-side state, routing, or component reactivity.
- The only genuinely interactive piece (the ActiveCampaign form + popup) is already a third-party embed via shortcode — it doesn't benefit from being wrapped in React, and doing so would add risk (a new integration point) for zero gain.
- The site already carries a heavy JS payload (76 script tags) from its existing plugin stack. Introducing a React/Vite bundle for one page adds more JS weight in the opposite direction from the performance goals identified above.
- A custom PHP page template in the theme (or a small custom plugin providing the template, to avoid further hand-editing the already-risky `hello-elementor` theme directly — see `elementor-removal-plan.md`) keeps the page server-rendered, fast, easy to test locally without a build step, and easy to diff/review in Git.
- This keeps local testing and staging deployment simple: no Node build pipeline required for this specific page (Node/npm are still available locally if a future page genuinely needs interactivity).

**Not recommended**: Option B/D (React) — no interactive requirement justifies the added complexity, bundle weight, or new dependency surface for this specific page. Revisit only if a future page has a genuine need (e.g. a live configurator/calculator).

## Implementation plan (high level — detailed steps in Phase 10-12 execution, staging only)

1. Create branch `feature/corporate-page-redesign`.
2. Build a new PHP page template (likely in a small custom plugin under `corporate-ui/`, or a template file added to the theme — decide based on the `functions1.php`/`functions2.php` investigation outcome, to avoid compounding the existing no-child-theme risk) with semantic HTML + modern CSS, mobile-first.
3. Preserve the ActiveCampaign shortcode integration exactly (`[activecampaign form=1 css=1]`) and the newsletter popup.
4. Assign the new template to the existing `corporate-orders` page on **staging only**, keeping the current Elementor version intact and switchable back (e.g. by not deleting the existing Elementor data, just changing `_wp_page_template` on staging).
5. Verify against the testing checklist (`docs/testing-checklist.md`) before considering it done, and before any discussion of promoting to production.

## Final design (2026-07-04): built by the user in Claude Design

User designed the new page in Claude Design (project "Treat Trunk Corporate Page Redesign", file `Corporate Orders.dc.html`) and shared it for review. Full content reviewed via the design-system MCP (`get_file`). Verdict: strong direction, adopted as the visual/structural basis — single clear `h1` (fixes the missing-H1 defect), a 4-path product grid, a cost-comparison section, a WeWork-specific offer section, real (verified) testimonials, FAQ accordion, and a quote/enquiry section.

**Issues found during review, now resolved or tracked:**

1. **Pricing mismatches vs real WooCommerce products** — the mockup's prices didn't match actual product prices (verified via WP-CLI against production, read-only):
   - Letterbox unit price is **£15.99** (not £10.95 as implied)
   - Treat Trunk Monthly Subscription is **£39.99** (mockup said £36.99)
   - One-Off Treat Trunk is **£28.99** (mockup said £24.99 — a different product, Treat Trunk Subscription, is actually £24.99; likely a mix-up in the mockup)
   - Copy must be corrected to real prices during implementation, or the referenced discount/bundle logic must actually exist (see below).
2. **"Remote Team Boxes" card had no real backing product** — confirmed no existing product matches its implied £11.99/person/month price point, and the card doesn't link to a product page at all (goes to the quote form). **Decision: kept as quote/manual for now** (see Multi-address orders below) — no new product needed for this specific card as currently designed.
3. **WeWork free-box offer** — needs the user's confirmation that this is a real, currently-running promotion before publishing (flagged, not yet confirmed as of this writing).
4. **Enquiry form is a UI mock only** — the design's `handleSubmit` just sets local component state; it isn't wired to ActiveCampaign or anything real. Implementation must wire the real fields to the actual lead-capture mechanism (the site's existing `[activecampaign form=1 css=1]` integration, styled to match the new design) rather than leaving it as a non-functional mock.
5. **Competitor pricing citations** ("£87.50 for 50 snacks", "£54.99 for 50 snacks", footnoted "checked July 2026") — need the user to confirm these are genuinely sourced figures before publishing, since comparative pricing claims carry real accuracy/legal weight.

## Bulk pricing architecture (decided 2026-07-04)

Goal: self-serve bulk ordering for corporate buyers, with volume discounts, with zero "email us to get pricing" friction.

**Bulk-to-one-address discount (the common case: N letterbox boxes, one delivery address)**:
- Mechanism: **automatic quantity-based cart discount via custom code** (not a new plugin, not separate "20-pack"/"50-pack" products). A small pricing rule (via WooCommerce's cart-price-calculation hooks) checks the quantity of the Letterbox product in the cart and applies a percentage discount automatically. This will live in a new custom plugin under `site-core/` in this repo — fully reviewable, no third-party dependency, tested on staging before touching production.
- Rationale for custom code over a dynamic-pricing plugin: no new install/approval needed, and this is a simple enough rule that a plugin's admin UI isn't worth the added dependency surface — the user explicitly chose this option over a plugin-based alternative.
- Tiers (based on real Letterbox price of £15.99/unit) — **confirmed by user 2026-07-04** (original proposal of 20%/30% would not leave margin):
  - 1–19 units: full price, £15.99/box
  - 20+ units: **15% off → £13.59/box** (20 boxes = £271.80)
  - 50+ units: **20% off → £12.79/box** (50 boxes = £639.50)
- **No discount code needed and none will be built** — the discount applies automatically based on cart quantity. "One address" isn't something that needs separate verification: a single WooCommerce order only ever ships to one address by default, so the condition is inherently satisfied by normal checkout behavior.

**Multi-address orders (e.g. 50 boxes to 50 different individual home addresses — the "Remote Team Boxes" concept)**:
- **Decision: stays quote/manual for now**, same as today — customer submits details (e.g. a spreadsheet of addresses) via the enquiry form, fulfilled manually by the team.
- Not pursuing self-serve multi-address ordering at this time — that would require a multi-address shipping plugin (new install, needs approval) plus real development/testing effort, and would delay the corporate page launch. Revisit as a future enhancement if demand justifies it.
- Implication for the page: the "Remote Team Boxes" card keeps linking to the quote form (`#quote`), not a product page.

Pricing tiers are now confirmed — nothing blocking implementation on the pricing side.
