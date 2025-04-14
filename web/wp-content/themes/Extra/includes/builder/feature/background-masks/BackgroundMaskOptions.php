<?php
/**
 * Background Mask Options
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Background_Mask_Options
 *
 * @since 4.15.0
 */
class ET_Builder_Background_Mask_Options {
	/**
	 * Class instance object.
	 *
	 * @var ET_Builder_Background_Mask_Options
	 */
	private static $_instance;

	/**
	 * Mask Settings.
	 *
	 * @var array
	 */
	private static $_settings = null;

	/**
	 * Get instance of ET_Builder_Background_Mask_Options.
	 *
	 * @return ET_Builder_Background_Mask_Options
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new ET_Builder_Background_Mask_Options();
		}

		return self::$_instance;
	}

	/**
	 * Get SVG Settings for a Mask Style.
	 *
	 * @param string $name Style Name.
	 *
	 * @return array
	 */
	public function get_style( $name ) {
		// Fetch style when settings already processed.
		if ( isset( self::$_settings['styles'][ $name ] ) ) {
			return self::$_settings['styles'][ $name ];
		}

		// Fetch settings for the mask style.
		$instance = ET_Builder_Background_Mask_Style_Factory::get( $name );

		if ( ! empty( $instance ) ) {
			return $instance->settings();
		}

		return array();
	}

	/**
	 * Returns SVG url for Mask style.
	 *
	 * @param string $name Style Name.
	 * @param string $color Color value.
	 * @param string $type SVG type, valid options: landscape | portrait | square | thumbnail.
	 * @param bool   $rotated Rotated or not.
	 * @param bool   $inverted Inverted or not.
	 * @param string $size Size value.
	 *
	 * @return string
	 */
	public function get_svg( $name, $color, $type, $rotated, $inverted, $size ) {
		if ( strpos( $color, 'gcid-' ) === 0 ) {
			$global_color_info = et_builder_get_global_color_info( $color );

			$color = $global_color_info['color'];
		}

		$is_stretch = 'stretch' === $size || '' === $size;

		$content  = $this->get_svg_content( $name, $type, $rotated, $inverted );
		$view_box = $this->get_view_box( $name, $type );
		$props    = et_()->get_svg_attrs(
			array(
				'fill'                => esc_attr( $color ),
				'viewBox'             => esc_attr( $view_box ),
				'preserveAspectRatio' => $is_stretch ? 'none' : 'xMinYMin slice',
			)
		);

		// Encode the SVG, so we can use it as data for background-image.
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- base64_encode() used for browser support.
		$svg = base64_encode( "<svg {$props}>{$content}</svg>" );

		return sprintf( 'url( data:image/svg+xml;base64,%s )', $svg );
	}

	/**
	 * Get SVG content for a Mask Style.
	 *
	 * @param string $name Style Name.
	 * @param string $type Valid options: landscape | portrait | square | thumbnail.
	 * @param bool   $rotated Default false, set true to get rotated version.
	 * @param bool   $inverted Default false, set true to get inverted version.
	 *
	 * @return string
	 */
	public function get_svg_content( $name, $type, $rotated = false, $inverted = false ) {
		$settings = $this->get_style( $name );

		// Return SVG Content for Thumbnail.
		// Note: Thumbnail value decided as following:
		// 1. Return Thumbnail from the Mask Style settings
		// 2. If not defined, return landscape value from default SVG group.
		if ( 'thumbnail' === $type ) {
			return isset( $settings['svgContent']['thumbnail'] )
				? $settings['svgContent']['thumbnail']
				: $this->get_svg_content( $name, 'landscape' );
		}

		// Return SVG Content for Style.
		$svg_group = $rotated ? 'rotated' : 'default';
		$svg_group = $inverted ? "{$svg_group}-inverted" : $svg_group;

		return isset( $settings['svgContent'][ $svg_group ][ $type ] )
			? $settings['svgContent'][ $svg_group ][ $type ]
			: '';
	}

	/**
	 * Get viewBox for a Mask Style.
	 *
	 * @param string $name Style name.
	 * @param string $type viewBox type, valid options: landscape | portrait | square | thumbnail.
	 *
	 * @return string
	 */
	public function get_view_box( $name, $type ) {
		$view_box_settings = $this->view_box_settings();
		$style_settings    = $this->get_style( $name );

		// Note: viewBox value decided as following:
		// 1. Return viewBox from the Mask Style settings
		// 2. If not defined, return viewBox from default settings.
		$view_box_default = isset( $view_box_settings[ $type ] )
			? $view_box_settings[ $type ]
			: '';

		return isset( $style_settings['viewBox'][ $type ] )
			? $style_settings['viewBox'][ $type ]
			: $view_box_default;
	}

	/**
	 * Mask SVG Settings.
	 *
	 * @return array
	 */
	public function settings() {
		if ( null === self::$_settings ) {
			// Look at builder/feature/background-masks/mask directory.
			self::$_settings = array(
				'styles'  => glob( ET_BUILDER_DIR . 'feature/background-masks/mask/*.php' ),
				'viewBox' => $this->view_box_settings(),
			);

			// Default mask style.
			$default = self::get_default_style_name();
			$style   = array(
				$default => self::get_style( $default ),
			);

			$files = array();

			foreach ( self::$_settings['styles'] as $file ) {
				// Extract name from file (e.g corner-lake).
				$name = basename( $file, '.php' );

				// Fetch settings for the style.
				$style_settings = $default !== $name ? self::get_style( $name ) : array();

				// Include the style only when valid settings are found.
				if ( ! empty( $style_settings ) ) {
					$files[ $name ] = $style_settings;
				}
			}

			// Sort by priority.
			et_()->uasort( $files, array( 'ET_Builder_Element', 'compare_by_priority' ) );

			self::$_settings['styles'] = array_merge( $style, $files );

			// Cleanup.
			$default = null;
			$files   = null;
			$style   = null;
		}

		return self::$_settings;
	}

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

	/**
	 * Get default mask style.
	 *
	 * @return string Default Style Name.
	 */
	public function get_default_style_name() {
		return 'layer-blob';
	}
}
