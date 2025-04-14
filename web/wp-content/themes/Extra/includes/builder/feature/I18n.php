<?php
/**
 * Cached common translation.
 *
 * @package Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Commonly used translations.
 */
class ET_Builder_I18n {

	/**
	 * Retrieve a commonly used translation.
	 *
	 * @since 4.4.9
	 *
	 * @param string $key Translation key.
	 *
	 * @return string
	 */
	public static function get( $key ) {
		// phpcs:disable PSR2.ControlStructures.SwitchDeclaration.SpaceBeforeColonCASE
		// phpcs:disable PSR2.ControlStructures.SwitchDeclaration.BodyOnNextLineCASE
		switch ( $key ) {
			// To avoid breaking tests:
			// 1. Do not remove `i18-list-begin` / `i18-list-end` tags.
			// 2. One translation per line.
			// 3. `et_builder` Text Domain only.
			// 4. No comments / empty lines.
			// 5. Keep the list ordered, if can't do with your IDE, switch to Emacs.
			// i18-list-begin.
			case 'Actual Size'                : return esc_html__( 'Actual Size', 'et_builder' );
			case 'Admin Label'                : return esc_html__( 'Admin Label', 'et_builder' );
			case 'Advanced'                   : return esc_html__( 'Advanced', 'et_builder' );
			case 'After'                      : return esc_html__( 'After', 'et_builder' );
			case 'Background'                 : return esc_html__( 'Background', 'et_builder' );
			case 'Before'                     : return esc_html__( 'Before', 'et_builder' );
			case 'Blur'                       : return esc_html__( 'Blur', 'et_builder' );
			case 'Body'                       : return esc_html__( 'Body', 'et_builder' );
			case 'Bottom Center'              : return esc_html__( 'Bottom Center', 'et_builder' );
			case 'Bottom Left'                : return esc_html__( 'Bottom Left', 'et_builder' );
			case 'Bottom Right'               : return esc_html__( 'Bottom Right', 'et_builder' );
			case 'Bottom'                     : return esc_html__( 'Bottom', 'et_builder' );
			case 'Button'                     : return esc_html__( 'Button', 'et_builder' );
			case 'CSS'                        : return esc_html__( 'CSS', 'et_builder' );
			case 'Cancel'                     : return esc_html__( 'Cancel', 'et_builder' );
			case 'Center Center'              : return esc_html__( 'Center Center', 'et_builder' );
			case 'Center Left'                : return esc_html__( 'Center Left', 'et_builder' );
			case 'Center Right'               : return esc_html__( 'Center Right', 'et_builder' );
			case 'Center'                     : return esc_html__( 'Center', 'et_builder' );
			case 'Centimeters (cm)'           : return esc_html__( 'Centimeters (cm)', 'et_builder' );
			case 'Circle'                     : return esc_html__( 'Circle', 'et_builder' );
			case 'Circular'                   : return esc_html__( 'Circular', 'et_builder' );
			case 'Color Burn'                 : return esc_html__( 'Color Burn', 'et_builder' );
			case 'Color Dodge'                : return esc_html__( 'Color Dodge', 'et_builder' );
			case 'Color'                      : return esc_html__( 'Color', 'et_builder' );
			case 'Conical'                    : return esc_html__( 'Conical', 'et_builder' );
			case 'Content'                    : return esc_html__( 'Content', 'et_builder' );
			case 'Cover'                      : return esc_html__( 'Cover', 'et_builder' );
			case 'Custom CSS'                 : return esc_html__( 'Custom CSS', 'et_builder' );
			case 'Custom Size'                : return esc_html__( 'Custom Size', 'et_builder' );
			case 'Dark'                       : return esc_html__( 'Dark', 'et_builder' );
			case 'Darken'                     : return esc_html__( 'Darken', 'et_builder' );
			case 'Default'                    : return esc_html__( 'Default', 'et_builder' );
			case 'Degrees (deg)'              : return esc_html__( 'Degrees (deg)', 'et_builder' );
			case 'Design'                     : return esc_html__( 'Design', 'et_builder' );
			case 'Desktop'                    : return esc_html__( 'Desktop', 'et_builder' );
			case 'Difference'                 : return esc_html__( 'Difference', 'et_builder' );
			case 'Disc'                       : return esc_html__( 'Disc', 'et_builder' );
			case 'Down'                       : return esc_html__( 'Down', 'et_builder' );
			case 'Ease'                       : return esc_html__( 'Ease', 'et_builder' );
			case 'Ease-In'                    : return esc_html__( 'Ease-In', 'et_builder' );
			case 'Ease-In-Out'                : return esc_html__( 'Ease-In-Out', 'et_builder' );
			case 'Ease-Out'                   : return esc_html__( 'Ease-Out', 'et_builder' );
			case 'Elements'                   : return esc_html__( 'Elements', 'et_builder' );
			case 'Elliptical'                 : return esc_html__( 'Elliptical', 'et_builder' );
			case 'Exclusion'                  : return esc_html__( 'Exclusion', 'et_builder' );
			case 'Expand'                     : return esc_html__( 'Expand', 'et_builder' );
			case 'Fade'                       : return esc_html__( 'Fade', 'et_builder' );
			case 'Fit'                        : return esc_html__( 'Fit', 'et_builder' );
			case 'Flip'                       : return esc_html__( 'Flip', 'et_builder' );
			case 'Font size (em)'             : return esc_html__( 'Font size (em)', 'et_builder' );
			case 'Hard Light'                 : return esc_html__( 'Hard Light', 'et_builder' );
			case 'Heading'                    : return esc_html__( 'Heading', 'et_builder' );
			case 'Hue'                        : return esc_html__( 'Hue', 'et_builder' );
			case 'Image'                      : return esc_html__( 'Image', 'et_builder' );
			case 'Inches (in)'                : return esc_html__( 'Inches (in)', 'et_builder' );
			case 'Inside'                     : return esc_html__( 'Inside', 'et_builder' );
			case 'Layout'                     : return esc_html__( 'Layout', 'et_builder' );
			case 'Left'                       : return esc_html__( 'Left', 'et_builder' );
			case 'Light'                      : return esc_html__( 'Light', 'et_builder' );
			case 'Lighten'                    : return esc_html__( 'Lighten', 'et_builder' );
			case 'Linear'                     : return esc_html__( 'Linear', 'et_builder' );
			case 'Link'                       : return esc_html__( 'Link', 'et_builder' );
			case 'Luminosity'                 : return esc_html__( 'Luminosity', 'et_builder' );
			case 'Main Element'               : return esc_html__( 'Main Element', 'et_builder' );
			case 'Millimeters (mm)'           : return esc_html__( 'Millimeters (mm)', 'et_builder' );
			case 'Multiply'                   : return esc_html__( 'Multiply', 'et_builder' );
			case 'No Repeat'                  : return esc_html__( 'No Repeat', 'et_builder' );
			case 'No'                         : return esc_html__( 'No', 'et_builder' );
			case 'None'                       : return esc_html__( 'None', 'et_builder' );
			case 'Normal'                     : return esc_html__( 'Normal', 'et_builder' );
			case 'Off'                        : return esc_html__( 'Off', 'et_builder' );
			case 'On'                         : return esc_html__( 'On', 'et_builder' );
			case 'Outside'                    : return esc_html__( 'Outside', 'et_builder' );
			case 'Overlay'                    : return esc_html__( 'Overlay', 'et_builder' );
			case 'Percent (%)'                : return esc_html__( 'Percent (%)', 'et_builder' );
			case 'Phone'                      : return esc_html__( 'Phone', 'et_builder' );
			case 'Picas (pc)'                 : return esc_html__( 'Picas (pc)', 'et_builder' );
			case 'Pixels (px)'                : return esc_html__( 'Pixels (px)', 'et_builder' );
			case 'Points (pt)'                : return esc_html__( 'Points (pt)', 'et_builder' );
			case 'Position'                   : return esc_html__( 'Position', 'et_builder' );
			case 'Radial'                     : return esc_html__( 'Radial', 'et_builder' );
			case 'Repeat X (horizontal)'      : return esc_html__( 'Repeat X (horizontal)', 'et_builder' );
			case 'Repeat Y (vertical)'        : return esc_html__( 'Repeat Y (vertical)', 'et_builder' );
			case 'Repeat and Stretch'         : return esc_html__( 'Repeat and Stretch', 'et_builder' );
			case 'Repeat with space between'  : return esc_html__( 'Repeat with space between', 'et_builder' );
			case 'Repeat'                     : return esc_html__( 'Repeat', 'et_builder' );
			case 'Right'                      : return esc_html__( 'Right', 'et_builder' );
			case 'Root-level font size (rem)' : return esc_html__( 'Root-level font size (rem)', 'et_builder' );
			case 'Saturation'                 : return esc_html__( 'Saturation', 'et_builder' );
			case 'Screen'                     : return esc_html__( 'Screen', 'et_builder' );
			case 'Sizing'                     : return esc_html__( 'Sizing', 'et_builder' );
			case 'Slide'                      : return esc_html__( 'Slide', 'et_builder' );
			case 'Soft Light'                 : return esc_html__( 'Soft Light', 'et_builder' );
			case 'Square'                     : return esc_html__( 'Square', 'et_builder' );
			case 'Stretch to Fill'            : return esc_html__( 'Stretch to Fill', 'et_builder' );
			case 'Tablet'                     : return esc_html__( 'Tablet', 'et_builder' );
			case 'Text'                       : return esc_html__( 'Text', 'et_builder' );
			case 'Title'                      : return esc_html__( 'Title', 'et_builder' );
			case 'Top Center'                 : return esc_html__( 'Top Center', 'et_builder' );
			case 'Top Left'                   : return esc_html__( 'Top Left', 'et_builder' );
			case 'Top Right'                  : return esc_html__( 'Top Right', 'et_builder' );
			case 'Top'                        : return esc_html__( 'Top', 'et_builder' );
			case 'Up'                         : return esc_html__( 'Up', 'et_builder' );
			case 'Upload an image'            : return esc_attr__( 'Upload an image', 'et_builder' );
			case 'Viewport Height (vh)'       : return esc_html__( 'Viewport Height (vh)', 'et_builder' );
			case 'Viewport Maximum (vmax)'    : return esc_html__( 'Viewport Maximum (vmax)', 'et_builder' );
			case 'Viewport Minimum (vmin)'    : return esc_html__( 'Viewport Minimum (vmin)', 'et_builder' );
			case 'Viewport Width (vw)'        : return esc_html__( 'Viewport Width (vw)', 'et_builder' );
			case 'Visibility'                 : return esc_attr__( 'Visibility', 'et_builder' );
			case 'X-height (ex)'              : return esc_html__( 'X-height (ex)', 'et_builder' );
			case 'Yes'                        : return esc_html__( 'Yes', 'et_builder' );
			case 'Zero-width (ch)'            : return esc_html__( 'Zero-width (ch)', 'et_builder' );
			// i18-list-end.
		}
		// phpcs:enable

		return $key;
	}
}
