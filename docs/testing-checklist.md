# Testing Checklist — Corporate Page Redesign (Staging)

Run all of this on **staging**, never production. Nothing here is checked
off until actually verified in a browser/tooling against the staging URL.

## Corporate page itself

- [ ] Desktop layout renders correctly (Chrome/Safari/Firefox)
- [ ] Tablet breakpoint renders correctly
- [ ] Mobile breakpoint renders correctly (this page's audience likely includes mobile HR/office-manager buyers — don't treat mobile as secondary)
- [ ] Single, correct `<h1>` present
- [ ] Yoast title/meta description render correctly in page source
- [ ] Hero image loads (check it's still using the WP Rocket preload/AVIF pipeline, not a regression to unoptimized images)
- [ ] Testimonial/social-proof section displays correctly
- [ ] Benefit bullets/cards display correctly

## Forms / CTAs — highest-risk area, test thoroughly

- [ ] Primary CTA scrolls to / reveals the lead form correctly
- [ ] ActiveCampaign form (`[activecampaign form=1 css=1]`) actually renders (not just a blank shortcode)
- [ ] Submitting the form on staging does not create a real lead in production ActiveCampaign (confirm with the user what staging's AC account/list config should be, or accept staging submissions are test data — clarify before real testing)
- [ ] Newsletter popup opens correctly (keyboard-accessible: can it be opened/closed without a mouse?)
- [ ] All CTA buttons point to the intended destination (no leftover `href="#"` dead links)

## Site-wide, not just the one page

- [ ] Header renders and functions (nav links, mini-cart icon)
- [ ] Footer renders and functions (all footer nav links from the page list still resolve)
- [ ] Global navigation unaffected
- [ ] Other Elementor-built pages still render normally (spot-check 2–3 unrelated pages, e.g. homepage, About Us) — confirms the template-swap technique didn't affect anything beyond the one page

## WooCommerce — do not skip, even though this page isn't a WooCommerce page

- [ ] Shop page still renders
- [ ] Product page still renders
- [ ] Add to cart still works
- [ ] Cart page still works
- [ ] Checkout completes successfully **in test/sandbox payment mode only**
- [ ] Confirm no real charge occurred (check the test gateway's dashboard, not a real card statement)

## Technical checks

- [ ] Browser console: no new JS errors introduced by the redesign
- [ ] No broken links (internal or the ActiveCampaign external link)
- [ ] Basic performance check (e.g. Lighthouse or WebPageTest against the staging URL) compared against the current production page's numbers as a baseline
- [ ] Basic accessibility check (e.g. axe DevTools or Lighthouse accessibility score) — confirm the missing-H1 issue is actually fixed and no new issues introduced

## Environment safety checks

- [ ] No production secrets/keys visible in any new frontend JS or page source (view-source check)
- [ ] Staging confirmed non-indexable (`wp option get blog_public` is `0`, and/or `robots.txt`/`X-Robots-Tag` blocks crawling)
- [ ] Confirm test form submissions / test checkout did not trigger a real transactional email to a real customer inbox
