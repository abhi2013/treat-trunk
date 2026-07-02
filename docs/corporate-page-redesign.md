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
