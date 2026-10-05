# Happy Addons — WPML Support Status & Guide

Sources: `wpml/` (33 integration files), `classes/wpml-manager.php` (widget registration map), `classes/widgets-manager.php`, `classes/extensions-manager.php`.

---

## 1. Widgets WITHOUT WPML support (21 of 73 free widgets)

### A. Need integration — they contain user-entered strings (0)

**All done.** The former candidates were resolved as follows:

- `whatsapp-button` → now supported (8 fields registered: phone, messages, texts)
- `image-cycle` → now supported (3 title fields + repeater link class `wpml/image-cycle.php`)
- `svg-draw` → now supported (Link field registered; `ha_custom_svg` excluded — raw SVG markup must not be edited by translators)
- `cf7` → runtime `wpml_object_id` translation of `form_id` in render (see section 4, Case 4)
- `post-tab` → runtime `wpml_object_id` translation of the selected taxonomy term IDs in render — tabs and posts now resolve to the current language (see section 4, Case 4)
- `lordicon` → now supported (`icon_cdn` registered, allows per-language icon JSONs; `custom_target` is a CSS selector and `icon_json` goes through WPML Media Translation — both excluded)
- `calendly` → now supported (`calendly_username` registered, allows per-language booking pages)

### B. No integration needed — form widgets (7)

Form contents live in, and are translated by, the form plugins themselves. Widgets only hold a form ID:
`cf7`, `wpform`, `ninjaform`, `calderaform`, `weform`, `gravityforms`, `fluent-form`

`cf7` additionally translates `form_id` at render time via `wpml_object_id` (widgets/cf7/widget.php:566). The other six could adopt the same pattern (see section 4, Case 4).

### C. No integration needed — dynamic-content widgets (13)

Render dynamic content (post/site data) that WPML already translates at the content level. No static widget strings:
`page-title`, `post-title`, `post-content`, `post-excerpt`, `site-logo`, `site-title`, `site-tagline`, `author-meta`, `archive-title`, `post-comments`, `post-featured-image`, `navigation-menu` (menu is translated via WPML menu sync), `post-tab` (renders translated posts; term IDs translated at render — see Case 4)

### D. No integration needed — media-only widget (1)

`threesixty-rotation` (360° Rotation — images only, no strings)

> **Real backlog = 0 widgets.**

---

## 2. Features (extensions) — WPML support: NONE

WPML's Elementor API (`wpml_elementor_widgets_to_translate`) only matches **widgets**, so none of the 21 free features can be registered through it. Settings that features attach to sections/containers/widgets (Advanced tab) are never sent to the Translation Editor.

### Features containing user-entered strings (currently NOT translatable, 8)

| Feature key | Title | String control location |
|---|---|---|
| `advanced-tooltip` | Happy Tooltip | `extensions/advanced-tooltip.php:99` (TEXTAREA tooltip text) |
| `wrapper-link` | Wrapper Link | `extensions/wrapper-link.php:39` (URL) |
| `grid-layer` | Grid Layer | `extensions/grid-layer.php:170` |
| `column-extended` | Column Order & Extension | `extensions/column-extended.php:30` |
| `custom-mouse-cursor` | Happy Mouse Cursor | `extensions/custom-mouse-cursor.php:339` |
| `appearing-image-animation` | Appearing Image Animation | `extensions/appearing-image-animation.php:495` |
| `heading-text-animation` | Text Animation | `extensions/heading-text-animation.php:410,769` |
| `scroll-to-top` | Scroll To Top | `extensions/scroll-to-top-kit-settings.php:384` |

### Features with no strings — nothing to translate (13)

`background-overlay`, `foreground-overlay`, `floating-effects`, `css-transform`, `equal-height`, `shape-divider`, `text-stroke`, `reading-progress-bar`, `custom-js`, `background-parallax`, `liquid-glass`, `container-hover-text-color`, `scroll-flow`

### Extensions map (`get_local_extensions_map`) — styling only, nothing to translate (5)

`background-hover-effect`, `foreground-overlay`, `button-fixed-size`, `widget-background-overlay`, `text-stroke`

---

## 3. Widgets WITH WPML support (52, for reference)

`age-gate`, `archive-posts`, `bar-chart`, `calendly`, `card`, `carousel`, `comparison-table`, `content-switcher`, `creative-button`, `data-table`, `dual-button`, `events-calendar`, `flip-box`, `fun-factor`, `gradient-heading`, `horizontal-timeline`, `icon-box`, `image-accordion`, `image-compare`, `image-cycle`, `image-grid`, `image-hover-effect`, `image-stack-group`, `infobox`, `justified-gallery`, `lightbox`, `link-hover`, `liquid-hover-image`, `logo-grid`, `lordicon`, `mailchimp`, `member`, `news-ticker`, `number`, `pdf-view`, `photo-stack`, `post-info`, `post-list`, `post-navigation`, `pricing-table`, `review`, `skills`, `slider`, `social-icons`, `social-share`, `step-flow`, `svg-draw`, `taxonomy-list`, `testimonial`, `text-scroll`, `twitter-feed`, `whatsapp-button`

---

## 4. How to ADD WPML support to a widget

### How the current system works

1. `base.php:131` hooks `wpml_elementor_widgets_to_translate` → `WPML_Manager::add_widgets_to_translate()`.
2. `classes/wpml-manager.php` holds the translatable-fields map per widget (keyed by widget key, auto-prefixed `ha-`).
3. The autoloader (`base.php:337`) loads `Happy_Addons\Elementor\Wpml\<Class>` from `wpml/<class-key>.php` (underscores → dashes, lowercase). No manual `include` needed.
4. On `wpml_translation_job_saved` (`base.php:132`), the HappyAddons widgets-usage cache is copied to the translated post and assets are regenerated.

### Case 1 — Simple fields (no repeater): edit only `classes/wpml-manager.php`

Add an entry in `$widgets_map` inside `add_widgets_to_translate()`:

```php
'whatsapp-button' => [
    'fields' => [
        [
            'field'       => 'button_text',           // exact control name in the widget
            'type'        => __( 'WhatsApp Button: Text', 'happy-elementor-addons' ),
            'editor_type' => 'LINE',                   // LINE | AREA | VISUAL
        ],
        'chat_link' => [                               // URL fields: key must differ, field is 'url'
            'field'       => 'url',
            'type'        => __( 'WhatsApp Button: Chat Link', 'happy-elementor-addons' ),
            'editor_type' => 'LINK',
        ],
    ],
],
```

- `field` must match the control name in `widgets/<key>/widget.php` exactly.
- Editor types: `LINE` (text), `AREA` (textarea), `VISUAL` (wysiwyg), `LINK` (url control, uses nested `'field' => 'url'`).
- Do NOT register technical values (CSS selectors, query IDs, raw SVG/code fields) — translators editing them breaks widgets.

### Case 2 — Repeater fields: create a class in `wpml/` + register it

**Step 1.** Create `wpml/image-cycle.php` (file name = widget key):

```php
<?php
/**
 * Image Cycle integration
 */
namespace Happy_Addons\Elementor\Wpml;

defined( 'ABSPATH' ) || die();

class Image_Cycle extends \WPML_Elementor_Module_With_Items {

	/**
	 * @return string
	 */
	public function get_items_field() {
		return 'ha_ic_images'; // repeater control name in the widget
	}

	/**
	 * @return array
	 */
	public function get_fields() {
		return [ 'ha_ic_link' => ['url'] ]; // translatable sub-fields of each item
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_title( $field ) {
		switch ( $field ) {
			case 'url':
				return __( 'Image Cycle: Image Link', 'happy-elementor-addons' );
			default:
				return '';
		}
	}

	/**
	 * @param string $field
	 *
	 * @return string
	 */
	protected function get_editor_type( $field ) {
		switch ( $field ) {
			case 'url':
				return 'LINK';
			default:
				return '';
		}
	}
}
```

**Step 2.** Register it in `classes/wpml-manager.php`:

```php
'image-cycle' => [
    'fields' => [],
    'integration-class' => [
        'Happy_Addons\Elementor\Wpml\Image_Cycle',
    ]
],
```

Notes:
- One class **per repeater**. A widget with several repeaters lists several classes (see `comparison-table`, `post-list`, `taxonomy-list`).
- Class name → file name via autoloader: `Image_Cycle` → `wpml/image-cycle.php`.
- For **nested repeaters** (a repeater inside a repeater), extend the local base class `Happy_Addons\Elementor\Classes\WPML_Module_With_Items` (see `wpml/image-accordion.php`) instead of `\WPML_Elementor_Module_With_Items`.
- Media sub-fields are excluded — attachments are translated by WPML Media Translation.

### Case 3 — Features (element-level settings)

The standard mechanism does **not** work — `wpml_elementor_widgets_to_translate` only translates widget settings. Options:

1. Register the strings with WPML String Translation (`icl_register_string` / `wpml_register_single_string`) when the feature saves, and wrap render output with `apply_filters( 'wpml_translate_single_string', ... )` (`extensions/advanced-tooltip.php` is the best first candidate).
2. Or use the `WPML_Elementor_Module_With_Items` approach only where the string happens to live inside a widget's own repeater.

### Case 4 — Widgets holding a post/object/term ID (forms, templates, lists)

When a widget stores an ID (form ID, saved template ID, taxonomy term list) instead of text, do NOT register it in the Translation Editor — resolve the translated object at render time:

```php
// Single object (form, template)
$form_id = apply_filters( 'wpml_object_id', $settings['form_id'], 'wpcf7_contact_form', true );

// Multiple term IDs
$terms_ids = array_map(
    static function ( $term_id ) use ( $taxonomy ) {
        return apply_filters( 'wpml_object_id', $term_id, $taxonomy, true );
    },
    (array) $terms_ids
);
$terms_ids = array_values( array_filter( $terms_ids ) );
```

The third argument `true` returns the original ID when no translation exists; when WPML is inactive the filter returns the original value unchanged, so the code is safe without WPML.

Existing examples: `widgets/cf7/widget.php:566` (CF7 form), `widgets/post-tab/widget.php` (taxonomy term IDs), `widgets/member/widget.php:1789` (saved template), `classes/theme-builder.php:891`.

### Checklist after adding support

1. Widget key entry in `wpml-manager.php` matches the widget directory name (`ha-<key>`).
2. Every `field` name matches the real control name in `widgets/<key>/widget.php`.
3. File name in `wpml/` matches the class name (dashes, lowercase).
4. Test: open the page in the secondary language → Translation Editor → the new fields appear and translations persist after save.
5. Verify assets regenerate on the translated post (`WPML_Manager::on_translation_job_saved` handles the cache copy).
