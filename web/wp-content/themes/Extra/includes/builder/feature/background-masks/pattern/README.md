# Adding New Pattern Style
To add new Pattern style in the Divi Builder follow the Actions Items.

## Action Items
- [ ] Copy Pattern Template (see bellow).
- [ ] Replace `NAME`, all the `ET_Builder_Pattern_NAME` in the template (3 places).
- [ ] Replace `TITLE` in the template (2 places).
- [ ] Replace `PRIORITY` in the template, lower number will make it show-up early in Pattern Style Dropdown list in the VB.
- [ ] Save in a new file, e.g: `some-name.php`, in this folder, add/commit to the repository.

**Tip**:
- For `NAME`, if it's multiple words like `Diagonal Lines`, use `_` to join, e.g `Diagonal_Lines`.
- For `filename`, if it's multiple words like `Diagonal Lines`, use `-` to join and make it lower case, e.g `diagonal-lines.php`.
- Once new `filename.php` placed in this folder, the new pattern would automatically appear in the VB (just refresh).
- default', 'default-inverted', 'thumbnail' should only contain all tags inside the `<svg></svg>` file, e.g:

```
'thumbnail'            => '<path d="M28,28H56V56H28ZM0,0H28V28H0Z"/>',
```

<hr>

### Pattern Template:

```
<?php
/**
 * Background Pattern Style - TITLE.
 *
 * @package Divi
 * @sub-package Builder
 * @since ??
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Pattern_NAME
 *
 * @since ??
 */
class ET_Builder_Pattern_NAME extends ET_Builder_Background_Pattern_Style_Base {
	/**
	 * Configuration.
	 *
	 * @return array
	 */
	public function settings() {
		return array(
			'label'      => esc_html__( 'TITLE', 'et-builder' ),
			'svgContent' => array(
				'default'          => '',
				'default-inverted' => '',
				'rotated'          => '',
				'rotated-inverted' => '',
				'thumbnail'        => '',
			),
			'width'      => '11px',
			'height'     => '11px',
			// Replace following PRIORITY with number (1-9) and uncomment to make it on top 9 list.
			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- temporary comment.
			// 'priority'   => PRIORITY,
		);
	}
}

return new ET_Builder_Pattern_NAME();

```

<hr>

**Last Updated**: Mar 10, 2022.
