# Adding New Mask Style
To add new Mask style in the Divi Builder follow the Actions Items.

## Action Items
- [ ] Copy Mask Template (see bellow).
- [ ] Replace `NAME`, all the `ET_Builder_Mask_NAME` in the template (3 places).
- [ ] Replace `TITLE` in the template (2 places).
- [ ] Replace `PRIORITY` in the template, lower number will make it show-up early in Mask Style Dropdown list in the VB.
- [ ] Save in a new file, e.g: `some-name.php`, in this folder, add/commit to the repository.

**Tip**:
- For `NAME`, if it's multiple words like `Diagonal Lines`, use `_` to join, e.g `Diagonal_Lines`.
- For `filename`, if it's multiple words like `Diagonal Lines`, use `-` to join and make it lower case, e.g `diagonal-lines.php`.
- Once new `filename.php` placed in this folder, the new mask would automatically appear in the VB (just refresh).
- `landscape`, `portrait` and `square` should only contain all tags inside the `<svg></svg>` file, e.g:

```
'landscape'            => '<path d="M28,28H56V56H28ZM0,0H28V28H0Z"/>',
```

<hr>

### Regular Mask Template:

```
<?php
/**
 * Background Mask Style - TITLE.
 *
 * @package Divi
 * @sub-package Builder
 * @since ??
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Mask_NAME
 *
 * @since ??
 */
class ET_Builder_Mask_NAME extends ET_Builder_Background_Mask_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'TITLE', 'et-builder' ),
			'svgContent' => array(
				'default'          => array(
					'landscape' => '',
					'portrait'  => '',
					'square'    => '',
				),
				'default-inverted' => array(
					'landscape' => '',
					'portrait'  => '',
					'square'    => '',
				),
				'rotated'          => array(
					'landscape' => '',
					'portrait'  => '',
					'square'    => '',
				),
				'rotated-inverted' => array(
					'landscape' => '',
					'portrait'  => '',
					'square'    => '',
				),
			),
			// Replace following PRIORITY with number (1-9) and uncomment to make it on top 9 list.
			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- temporary comment.
			// 'priority'   => PRIORITY,
		);
	}
}

return new ET_Builder_Mask_NAME();
```

<hr>

### Extended Mask Template:

We're using following default `viewBox` settings for all masks ([Code Ref](https://github.com/elegantthemes/submodule-builder/blob/a54a40832c4abc5777b1f3fad52ad2cabde6f97f/module/settings/BackgroundMaskOptions.php#L195-L202)).

```
	/**
	 * Default viewBox settings for Mask.
	 *
	 * @return string[]
	 */
	public function view_box_settings() {
		return array(
			'landscape' => '0 0 1920 1440',
			'portrait'  => '0 0 1920 2560',
			'square'    => '0 0 1920 1920',
			'thumbnail' => '0 0 1920 1440',
		);
	}
```

Also, we're using svgContent of `square` to show as `thumbnail` to display in Dropdown Style list in the VB.

In case a mask need any custom value for viewBox and/or custom thumbnail, can be done like following:

```
<?php
/**
 * Background Mask Style - TITLE.
 *
 * @package Divi
 * @sub-package Builder
 * @since ??
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Mask_NAME
 *
 * @since ??
 */
class ET_Builder_Mask_NAME extends ET_Builder_Background_Mask_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'TITLE', 'et-builder' ),
			'svgContent' => array(
				'default'          => array(
					'landscape' => '',
					'portrait'  => '',
					'square'    => '',
				),
				'default-inverted' => array(
					'landscape' => '',
					'portrait'  => '',
					'square'    => '',
				),
				'rotated'          => array(
					'landscape' => '',
					'portrait'  => '',
					'square'    => '',
				),
				'rotated-inverted' => array(
					'landscape' => '',
					'portrait'  => '',
					'square'    => '',
				),
				// Following is optional, uncomment it if don't want to reuse landscape value.
				// 'thumbnail'        => '',
			),
			// Replace following PRIORITY with number (1-9) and uncomment to make it on top 9 list.
			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- temporary comment.
			// 'priority'   => PRIORITY,
			// Following is optional, remove any number of them if you want to reuse global settings.
			'viewBox'    => array(
				'landscape' => '0 0 1920 1440',
				'portrait'  => '0 0 1920 2560',
				'square'    => '0 0 1920 1920',
				'thumbnail' => '0 0 1920 1440',
			),
		);
	}
}

return new ET_Builder_Mask_NAME();
```

The Code works as following:
- Look for `viewBox` value from mask file, if not exists, global settings are used.
- Look for `thumbnail` value from `svgContent` array from mask file, if not exists, `square` value is used.

<hr>

**Last Updated**: Feb 10, 2022.
