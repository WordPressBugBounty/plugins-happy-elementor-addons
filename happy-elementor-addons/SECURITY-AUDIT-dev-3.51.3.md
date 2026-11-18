# Security Audit: Happy Elementor Addons (dev branch, v3.51.3)

**Scope and method.** Ten review passes using `gk` (GitKraken CLI) for history and branch analysis, static code reading, and `jev` for triage. Nothing was executed against a live site and no source file was changed (this report is the only file added). Every finding comes from reading code and should be reproduced before you rely on it.

**This is not a proof the plugin is clean.** I asked jev whether it is safe to call this list complete. It answered 0.04. See section 7 for what was not covered.

**Correction to my earlier reports.** I said the Clone handler checks a nonce. It does not. See finding 4.

## 1. The vulnerability class being fixed on dev

Found with `gk log`, `gk show` and `gk diff`.

**Output escaping / stored XSS:**

| Commit | Fix |
|---|---|
| `54314876` | Creative Button: icon passed through `esc_attr` |
| `f9a63e82` | Reading Progress Bar: `esc_js` in inline script, switcher values whitelisted |
| `1544432a`, `0c083399` | Lordicon, Icon Box, Info Box: new `traits/lordicon-sanitizer.php` |
| Earlier | Age Gate, Page/Post Title, Image Compare, Tooltip, Gradient Heading, Photo Stack, Calendly and others |

**Missing authorization / role checks:**

| Commit | Fix |
|---|---|
| `153d6f5d` | Setup Wizard REST routes: `true` or `edit_others_posts` changed to `manage_options`, with key whitelists |
| `624578de` | Team Member widget: rendered any post ID. Now requires publish status and post type `elementor_library` |
| `0ab91d5d` | Mailchimp: API key read server-side |
| Earlier | Happy Clone and Template Conditions hardening |

## 2. Final findings (ranked)

Severity is my own judgment. Jev's score (0 to 4) and confidence are shown where it rated the item. Several jev scores were low-confidence, and I note where I disagree.

### Tier 1: fix before release

#### 1. SVG Draw checks the viewer's capability, not the author's (admin XSS)
- `widgets/svg-draw/widget.php:735`
- `if ( current_user_can( 'unfiltered_html' ) ) { echo $settings['ha_custom_svg']; }` tests whoever is viewing. A Contributor saves a malicious SVG, and an Admin or Editor who views or previews the page runs the script in their session. This is a route to admin takeover.
- `extensions/custom-js.php` handles the same situation correctly by checking the post's last editor.
- Fix: always `wp_kses` the SVG with an SVG-aware allow-list, or check the content author's capability.
- Jev: 3.06 (confidence 0.57).

#### 2. Mailchimp subscribe AJAX trusts client list, post and widget IDs
- `classes/ajax-handler.php:301` (registered `nopriv` at `base.php:93-94`), `widgets/mailchimp/mailchimp-api.php:28-47`
- Guarded only by a public nonce. `list_id`, `post_id` and `widget_id` come from `$_POST`, and `list_id` is never compared with the audience configured in the widget. Any visitor can subscribe any address to any audience in the site's Mailchimp account. Settings of any `post_id`, including drafts and private posts, are loaded. `list_id` goes into the API URL path unvalidated.
- Fix: take the list ID from saved widget settings only, require the post to be published, validate the ID format, rate-limit.
- Jev: 3.59 (0.68). I would call it high rather than critical.

#### 3. Twitter Feed AJAX: unauthenticated URL parameter injection and unbounded permanent options
- `classes/ajax-handler.php:12-66` (registered `nopriv` at `base.php:87-88`)
- `$settings['user_name']` from the request is concatenated unescaped into `https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=...`, so parameters can be injected into a request made with the site's Twitter credentials.
- The result is stored with `set_transient( $transient_key, $twitter_data, 0 )`. A transient with no expiry becomes an autoloaded option, and the key includes the client-supplied `user_name` and `id`. A visitor who knows a widget ID (it is on the page) can create unlimited permanent autoloaded rows, which can bloat every page load.
- `$_POST['widget_id']` is read before `isset` is checked.
- Fix: do not accept query settings from the client (store them server-side per widget), `rawurlencode` the name, set an expiry, cap the entries.
- Jev: 3.63 (0.68).

#### 4. Happy Clone never verifies its nonce (CSRF)
- `classes/clone-handler.php:60-130` (hooked on `admin_action_ha_duplicate_thing`, `base.php:196`)
- `duplicate_thing()` reads `$_GET['_wpnonce']` into `$nonce` but nothing in the function or file calls `wp_verify_nonce` or `check_admin_referer`. `get_url()` builds a nonce URL, but it is never checked on receipt.
- A link a logged-in Editor or Admin is tricked into opening clones any post they can edit into a draft owned by them. The copy includes taxonomies, meta and `post_password`.
- Fix: `check_admin_referer( self::ACTION )` at the top.
- Jev: 3.5 (0.59).

#### 5. Content Switcher renders any published post (same flaw as the Team Member fix)
- `widgets/content-switcher/widget.php:1258-1301` (nine call sites)
- Checks only `get_post_status() == 'publish'`, with no `elementor_library` post-type check. A Contributor+ with editor access can render any published post or page by ID.
- Fix: share one helper (for example `ha_is_renderable_template( $id )`) with the Member widget and use it everywhere.
- Jev: 2.38 (confidence only 0.28, so weak). Its yes/no on "genuine instance" was 0.93.

### Tier 2: Contributor+ stored XSS (mechanical escaping fixes)

#### 6. Creative Button text unescaped
- `traits/creative-button-markup.php`, five render methods: `$btn_txt = $settings['button_text']` into a heredoc. Only the icon was fixed. Fix: `esc_html()`.

#### 7. Age Gate and Archive Posts raw echo
- `widgets/age-gate/widget.php:2040` (`warning_message`), `widgets/archive-posts/widget.php:1176` (`nothing_found_message`), both TEXTAREA settings. Fix: `wp_kses_post()` or `ha_kses_intermediate()`.

#### 8. Post Navigation labels unescaped
- `widgets/post-navigation/widget.php:579-580`, used at 617 and 625: `prev_label` and `next_label` concatenated into HTML passed to `previous_post_link` / `next_post_link`. Fix: `esc_html()`.

#### 9. Social Icons custom separator unescaped
- `widgets/social-icons/widget.php:902-903, 959`: `custom_separator` (TEXT) is output with `echo "<span ...>" . $separators . "</span>"`. The icon title on the line above is escaped, the separator is not. Fix: `esc_html()`.

Jev scored items 6 to 9 as a group at 2.52 (confidence 0.31, weak). Its yes/no on "genuine stored XSS" was 0.84 to 0.85.

### Tier 3: needs verification, or lower impact

#### 10. Elementor editor templates print settings unescaped
- `widgets/card/widget.php:1217-1230` (`badge_text`, `title`, `description`), `widgets/review/widget.php:820-848` (`review`, `title`, `job_title`), `widgets/gradient-heading/widget.php:284` (`title`)
- The editor-side `content_template()` prints these with triple braces, which render raw. A malicious value saved by a Contributor would run when an Admin opens the page in Elementor. Elementor may filter some of this on save, so reproduce before relying on it.
- Fix: use the double-brace form, or the same kses used on the frontend.
- Jev: 3.06 (0.62).

#### 11. `post_tab` AJAX trusts client query settings (unauthenticated)
- `classes/ajax-handler.php:209`, registered `nopriv` at `base.php:90-91`
- Public nonce only. `post_type`, `taxonomy` and `item_limit` come from the request, so a visitor can enumerate published posts of any post type with any limit. `get_the_date()` output is also unescaped.
- Fix: store the settings server-side, whitelist post types, cap the limit.
- Jev: 1.6 (0.47).

#### 12. Event Calendar JSON inside an inline script
- `widgets/event-calendar/widget.php:1814`: `json_encode( $data )` without `JSON_HEX_TAG | JSON_HEX_AMP`. A `</script>` in a value could close the tag. Titles are `esc_html`'d, so check which fields reach it. Fix: `wp_json_encode( $data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT )`.

#### 13. Attention Seeker AJAX has no capability check
- `classes/attention-seeker.php:46`: nonce only. `$seeker[0]['end_date']` is read without an existence check. Fix: add `manage_options` and a check.

#### 14. Admin notice handlers change options from GET with no nonce or capability
- `classes/review.php:67-90`, `classes/notice.php:83-91`, hooked on `admin_init` (`base.php:178,183`): `?spare_me=1`, `?remind_me=1`, `?ha_rated=1`, `?ha_not_interested=1`. Any logged-in user who can load `wp-admin` can set them, and a CSRF link can do it to an admin. Low impact (notice state), same missing-authorization pattern as the REST fix.

#### 15. Template library AJAX switches to any post
- `classes/library-manager.php:52-90`: after an `edit_posts` check, `switch_to_post( editor_post_id )` runs for any existing post, with no `edit_post` check. Fix: `current_user_can( 'edit_post', $id )`.

#### 16. SVG Draw builds a file path from a setting
- `widgets/svg-draw/widget.php:835-858`: the icon `library` value goes into `ELEMENTOR_ASSETS_PATH . 'lib/font-awesome/json/' . $name . '.json'` and is read with `file_get_contents`. There is no whitelist, so traversal limited to `.json` files is possible. `$data['icons'][$icon_name]` is read without an existence check.

#### 17. Template type endpoint has no capability or nonce check
- `classes/condition-manager.php:299`: `ha_get_template_type()` has its validation commented out. Any logged-in user can read `_ha_library_type` for any post. Low (information disclosure).

#### 18. Unescaped user-controlled date formats
- `widgets/post-list/widget.php:1372`, `widgets/post-tab/widget.php:1085`: `get_the_date()` output is echoed unescaped. If the format comes from a setting, backslash-escaped characters in the format can emit HTML. Low, verify the source of the format.

#### 19. Robustness issues reachable from public requests
- `inc/functions.php:736`: `ha_sanitize_array_recursively()` runs `foreach` on any input, so the unauthenticated `post_tab` and Twitter handlers can pass it a string (warnings). `classes/theme-builder.php:379`: an invalid `post_type` makes `get_post_type_object()` return null and `->cap` then fatals (needs a valid nonce).

### Merge hazard (not a code bug on dev)

#### 20. Stale branches carry pre-fix code
- Examples: `full-page-scroll` (2025-03), `wc-builder` (2025-02), `custom-mouse-cursor` (2024-12), `version-3.7.0` (2022-08), `phpcs` and `phpcs-new` (2025-01, 134 and 180 commits). They render templates by ID with no checks, use unsanitized `$_REQUEST` in the condition handlers and similar. Merging any of them into `dev` would reintroduce fixed vulnerabilities. Diff before merging.

## 3. Areas checked and found fine

Pricing Table, Fun Factor, Number, PDF View, WhatsApp status text, Post Excerpt, Link Hover, Lightbox, Button renderer trait, Event Calendar popup strings and description (`ha_kses_intermediate` on all three data sources), Post Info (`wp_kses` with `a href` only), Taxonomy List, `admin-bar`, `dashboard` save (nonce and `manage_options`), Setup Wizard routes, `condition-manager` (nonce plus per-template `edit_post`), `select2` (nonce and `edit_posts`), `custom-js` (author capability), assets cache (integer post ID, plugin-owned CSS files), form-plugin widgets (shortcode IDs, class sanitised), extension script contexts in Custom Mouse Cursor (booleans only).

## 4. Lower-priority observations
- `classes/select2-handler.php`: any `edit_posts` user can query arbitrary post types and taxonomies. Published posts only.
- `heading-text-animation.js` re-parses element HTML via `innerHTML` after render. The source is server-escaped, so low.
- `extensions/scroll-to-top.php` preview mode builds HTML from kit settings unescaped (admin-set values only).
- Several widgets output a tag name from a variable. The ones checked are fixed values, but prefer `ha_escape_tags()` as a rule.

## 5. History checks (gk)
- `dev` is 41 commits ahead of `master`, and `master` has nothing `dev` lacks.
- `pre-release` / `release-new` have four commits dev lacks: `1ffa6631`, `e4acfa51`, `e6e126c1`, `1ebeb749`. None are security fixes by message.
- 40 remote branches are unmerged into `dev`. I scanned added lines of the main ones for risky patterns; the hits were the stale-branch hazard in finding 20.

## 6. Jev results
Jev is advisory. Numbers are as returned.

| Question | Result |
|---|---|
| Release dev as-is? | `fix_first` 0.73 (confidence 0.6) |
| Is this list complete? | 0.04 (no) |
| Which fix first, original order | Clone CSRF (D), 0.72, confidence 0.65 |
| Which fix first, reversed order, 5 options | SVG Draw (A), 0.99 |

The first-fix ranking flipped with option order, so by jev's own stability rule there is no reliable winner. The reversed run also had a different option set, which may have changed the outcome. My ordering puts SVG Draw first because it reaches admin accounts, then the unauthenticated endpoints and the Clone CSRF.

Earlier rounds (kept for the record): the content-switcher, creative button, age-gate / archive and post_tab findings each scored 0.83 to 0.93 as genuine in yes/no questions. Post-navigation, the notice handlers, the library AJAX and the SVG path scored 0.81 to 0.83. Release as-is: `fix_first` at 0.96 in the first round.

Jev's group severity of 3.47 for items 12 to 15 looks inflated to me, because the group mixes a low-impact hardening item with real ones.

## 7. Not covered or not verified
- Nothing was run. Elementor may sanitize some values before they reach the render code.
- I read the sinks reachable by search patterns and the code around them, not every line of the 73 widgets.
- The bundled third-party code (`assets/vendor`, `vendor/`, `dist/`, the Lordicon script) was not audited.
- Pro and WPML integration paths, translation files and the `wpml/` directory were only skimmed.
- The unmerged branches were scanned for risky added lines only, not reviewed.

## 8. Suggested order of work
1. Finding 1 (SVG Draw), the admin-reaching case.
2. Findings 4, 3, 2 (Clone nonce, Twitter AJAX, Mailchimp subscribe).
3. Finding 5 with a shared template-ID helper, then reuse it in the Member widget.
4. Findings 6 to 9: escape the output.
5. Finding 10: verify, then fix.
6. Findings 11 to 19: hardening.
7. Before merging any branch listed in finding 20, diff it against `dev`.
8. Add a changelog entry for each fix, in the existing `changelog.txt` format.
