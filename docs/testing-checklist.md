# Testing Checklist — Corporate Page Redesign (Staging)

Run all of this on **staging**, never production. Status as of 2026-07-05 —
checked items were verified via direct HTTP/WP-CLI testing against
`https://staging.treattrunk.co.uk`, not just read from code.

## Corporate page itself

- [ ] Desktop layout renders correctly (Chrome/Safari/Firefox) — verified via HTTP only, not a real browser; recommend a manual look
- [ ] Tablet breakpoint renders correctly — CSS breakpoints added (`corporate-ui/assets/corporate-orders.css`), not manually verified in a real device/emulator yet
- [ ] Mobile breakpoint renders correctly — same as above, CSS is in place but not manually verified in a real viewport yet
- [x] Single, correct `<h1>` present — confirmed: "Corporate snack boxes your team will actually fight over"
- [x] Yoast title/meta description render correctly in page source — confirmed, updated to target "corporate snack box"/"office snacks"
- [ ] Hero image loads via WP Rocket preload/AVIF pipeline — currently a placeholder image (see known gaps below), not the real intended photo
- [x] Testimonial/social-proof section displays correctly — confirmed, and cross-checked the quotes against the real page's Elementor data (genuine, not fabricated)
- [x] Benefit bullets/cards display correctly — confirmed via HTTP

## Forms / CTAs — highest-risk area, test thoroughly

- [x] Primary CTA scrolls to / reveals the lead form correctly — anchor links confirmed present and correctly targeted
- [x] ActiveCampaign form (`[activecampaign form=1 css=1]`) actually renders — confirmed: real AC embed script loads, not a blank shortcode
- [x] Submitting the form does not risk a real lead — investigated: the real AC form only has `email`/`phone` fields (checked the actual embed script), so it's the same integration as production either way; no new risk introduced by the redesign
- [ ] ~~Newsletter popup opens correctly~~ — **gap found**: the original page's separate newsletter popup ("10% off first box") was not carried over into the new template at all. Not yet fixed — flagged for the user.
- [x] All CTA buttons point to the intended destination — **bug found and fixed**: 3 product links were hardcoded to `treattrunk.co.uk` (production), silently sending staging visitors to production. Fixed via `home_url()`.

## Site-wide, not just the one page

- [x] Header renders and functions — confirmed (`wp-theme-hello-elementor` body class present, real nav/mini-cart intact)
- [x] Footer renders and functions
- [x] Global navigation unaffected
- [x] Other Elementor-built pages still render normally — spot-checked shop, homepage, our-story: all HTTP 200

## WooCommerce — do not skip, even though this page isn't a WooCommerce page

- [x] Shop page still renders
- [x] Product page still renders
- [x] Add to cart still works — verified via real cart session, including the bulk-discount hook firing correctly (20 units → £275.00, 50 units → £650.00)
- [x] Cart page still works
- [x] Checkout completes successfully **in test/sandbox payment mode only** — **finding**: the site's Stripe "test" keys are actually mislabeled live keys (`pk_live_`/`sk_live_`, and suspiciously short at 32 chars — not genuine Stripe key format), so no real Stripe test-mode checkout is possible until real test keys are obtained from the Stripe dashboard. Verified checkout completion instead using WooCommerce's built-in, zero-risk Bank Transfer (BACS) method — real order created (#54610, correct £15.99 total), then deleted.
- [x] Confirm no real charge occurred — BACS involves no payment processor at all, zero risk by construction

## Technical checks

- [x] No PHP warnings/notices/fatal errors in page output (checked after every deploy this session)
- [x] No broken links — checked all internal hrefs; found and fixed the 3 hardcoded-production-domain links above
- [ ] Basic performance check (Lighthouse/WebPageTest) — not done, needs a real browser tool
- [ ] Basic accessibility check (axe/Lighthouse) — not done with real tooling; the missing-H1 defect is fixed and the FAQ/comparison table use semantic markup, but no formal audit run

## Environment safety checks

- [x] No production secrets/keys visible in frontend JS or page source — checked, none found
- [x] Staging confirmed non-indexable — `blog_public=0` **and** a hard `X-Robots-Tag: noindex, nofollow, noarchive` header at the Apache level (added because Yoast appeared to override the WP-core behavior on its own)
- [x] Confirmed test checkout does not trigger a real transactional email — **finding**: it did try to. Enabling a payment gateway fired a real WooCommerce admin notification via `wp_mail()`, proving staging's SMTP config is still live (as flagged much earlier and never closed out). Fixed with a `pre_wp_mail` short-circuit in a staging-only mu-plugin (`site-core/mu-staging-email-block.php`) — verified with a direct test send after deploying it. **This mu-plugin must never reach production.**

## Known gaps / follow-ups (not blocking, but not done)

1. Newsletter popup ("10% off first box") not carried over from the original page.
2. Hero and "Why us" images are still placeholders (real photos from the Claude Design project were too large to fetch programmatically) — need the user to upload them to the media library.
3. No real browser/device testing done — everything above was verified via HTTP requests and WP-CLI, which catches functional/backend issues but not visual rendering, real touch interaction, or actual cross-browser quirks.
4. No Stripe test-mode checkout possible until genuine test API keys are obtained (current "test" keys are mislabeled live keys).
