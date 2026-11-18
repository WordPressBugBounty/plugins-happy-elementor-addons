# Stale branches report: Happy Elementor Addons (free) and Pro

Generated 2026-11-12. Read-only analysis; no branch was changed, deleted or checked out.

**Definition.** Stale = last commit before 2026-05-12 (six months). `master` and `dev` excluded.
Compared against `origin/master`.

| Repo | Stale | Fully merged into master | With commits not in master |
|---|---|---|---|
| happy-elementor-addons (free) | 65 | 38 | 27 |
| happy-elementor-addons-pro | 59 | 37 | 22 |

## Column meanings
- **Unique**: commits reachable from no other branch or tag.
- **Not in master**: commits whose changes are not already in master (`git cherry`, patch comparison).
  A branch can have 0 unique commits and still have commits missing from master, because another branch holds them.
- **Jev safe-delete**: jev `noul` probability that deleting the branch loses nothing meaningful.

## 1. Fully merged (nothing unique, safe to delete)

### free
ai-widget/whatsapp-sonnet, before-remove-2.8-suppor, bg-parallax, buddy-boss-issues, build, compatible-with-3.8, control-updater, custom-js, data-table-issues, element-cache, fade-carousel, finder-backup, gsap-correction, horizontal-scroll-gsap, huge-icons-ondemand-assets, icon-update, lightbox, liquid-hover, liquid-hover-image, loop-post, mouse-cursor, new-autoloader-2, optimized-markup, post-acf, post-archive, post-tab, pre-release, reading-progress-bar, remove-2.8-support, replace-waypointjs, sass-fix, sayem, svg-draw, svg-draw-2, template-condition-v2, text-color-scroll, wpml, wpml-2

### pro
adv-accordion-enhancement, advanced_accordion_fix, advanced-comparison-table, adv-pricing-table, adv-tab-mobile-device, autoloader, before-remove-2.8-support, build, element-cache, featured-post, feature-issues, horizontal-scroll-gsap, loop-post, loop-tab, mega-menu-flashes-issues, metro-grid, metro-grid-old, metro-grid-title-tips, multi-layer-parallax, multi-scroll, new-autoloader, new-autoloader-2, old-live-copy, optimized-markup, post-featured-from-dev, post-query-id, pre-release, remove-2.8-support, replace-waypointjs, sass-fix, sayem, scroll-sequence, scroll-tabs, super-btn, version-2.19.0, wpml, wpml-2

Caveat: "merged" is by commit ancestry. Squash-merged branches would show under section 2, not here.

## 2. Branches with commits not in master

### free (happy-elementor-addons)

| Branch | Last commit | Unique | Not in master | Jev safe-delete | Notes |
|---|---|---|---|---|---|
| custom-mouse-cursor | 2024-12-01 | 46 | 53 | 0.17 | about 3,000-line cursor feature |
| phpcs-new | 2025-01-21 | 46 | 171 | 0.20 | repo-wide phpcbf run |
| ok-branch-for-documentOptionsMenu-and-utilitiesMenu | 2025-02-26 | 2 | 20 | 0.13 | |
| documentation | 2025-02-05 | 12 | 12 | 0.34 | architecture and release docs |
| scroll-magic | 2024-09-02 | 12 | 12 | 0.20 | |
| search-form | 2023-03-06 | 7 | 7 | 0.18 | |
| chartjs-update | 2025-06-19 | 4 | 4 | 0.28 | |
| chartjs-1 | 2025-06-24 | 1 | 1 | 0.30 | |
| feature-issues | 2024-12-05 | 4 | 4 | 0.25 | |
| web-pack | 2021-09-28 | 4 | 4 | 0.29 | |
| promotion-fix | 2024-02-28 | 3 | 2 | 0.19 | |
| version-3.7.0 | 2022-08-21 | 3 | 6 | 0.24 | |
| wc-builder | 2025-02-25 | 1 | 18 | 0.17 | |
| advanced-comparison-table | 2024-03-11 | 1 | 1 | 0.28 | |
| full-page-scroll | 2025-03-25 | 1 | 1 | 0.27 | |
| inside-elementor-tab-image-compare-fix | 2026-03-10 | 2 | 2 | 0.25 | |
| nasted | 2026-04-27 | 1 | 1 | 0.27 | |
| nested-widget-test | 2025-08-13 | 1 | 1 | 0.34 | |
| fix/scss-fix | 2026-02-25 | 1 | 1 | 0.25 | |
| fix-sass-error | 2025-08-18 | 1 | 1 | 0.26 | |
| cookie-jar | 2023-03-09 | 1 | 1 | 0.28 | |
| display-condition-enh | 2025-02-23 | 2 | 2 | 0.35 | 1-line deletion |
| new-autoloader | 2025-05-06 | 1 | 1 | 0.39 | |
| autoloader | 2022-03-13 | 0 | 5 | 0.19 | commits held by autoloader-2 |
| autoloader-2 | 2022-07-25 | 0 | 5 | 0.24 | commits held by autoloader |
| phpcs | 2025-01-06 | 0 | 128 | 0.24 | commits held by phpcs-new |
| wc-builder-backup | 2024-12-08 | 0 | 14 | 0.30 | commits held by other branches |

### pro (happy-elementor-addons-pro)

| Branch | Last commit | Unique | Not in master | Jev safe-delete | Notes |
|---|---|---|---|---|---|
| sazzad-wc-builder | 2024-12-01 | 19 | 83 | 0.18 | |
| full-scroll | 2025-06-02 | 17 | 16 | 0.19 | |
| background-parallax | 2022-11-17 | 13 | 13 | 0.20 | |
| fullscreen-parallax | 2026-03-10 | 12 | 12 | 0.20 | |
| multiscroll | 2021-08-11 | 12 | 11 | 0.21 | |
| g-review | 2022-02-22 | 11 | 10 | 0.22 | |
| x-copy | 2023-10-18 | 9 | 9 | 0.23 | |
| post-grid | 2026-02-02 | 8 | 7 | 0.16 | |
| bg-parallax | 2025-08-06 | 5 | 4 | 0.18 | |
| wc-builder | 2025-02-25 | 4 | 15 | 0.17 | |
| phpcs | 2024-04-23 | 4 | 3 | 0.28 | |
| checkout-widgets | 2024-12-03 | 2 | 2 | 0.18 | |
| scrolling-image | 2025-08-25 | 2 | 2 | 0.22 | |
| search-form | 2023-02-20 | 1 | 1 | 0.28 | |
| sticky-video | 2022-09-01 | 1 | 1 | 0.27 | |
| for-ai-bug-fix | 2026-04-16 | 1 | 1 | 0.30 | |
| title-tips | 2024-11-18 | 1 | 1 | 0.38 | |
| display-condition-enh | 2025-02-19 | 1 | 1 | 0.32 | commit message "kjhkj" |
| preset-backup | 2022-07-18 | 1 | 1 | 0.46 | 7 lines |
| lazy-query-update | 2024-11-20 | 0 | 48 | 0.21 | commits held by other branches |
| rony-wc-builder-widget | 2024-11-28 | 0 | 70 | 0.19 | commits held by other branches |
| wc-builder-backup | 2024-12-08 | 0 | 12 | 0.27 | commits held by other branches |

## 3. How to read the jev numbers
Jev gave "worth keeping" 0.53 to 0.92 for every branch with unmerged work, and safe-delete never exceeded 0.46.
It does not separate real features from junk, so do not use it to decide deletions on its own.
Likely low value on inspection of the commit summaries: `display-condition-enh` (both repos), `preset-backup`,
`title-tips`, `cookie-jar`, and the phpcs and backup branches whose commits live elsewhere.

## 4. Caveats
- `lazy-query-update` and the `wc-builder` branches rewrite the AJAX handlers flagged in `SECURITY-AUDIT-pro-atomic-v4-wf.md`.
  Review them before merging or restoring any of them.
- Open pull requests on the remote were not checked.
- Dates are the newest commit across local and remote refs of the same branch name.
