<?php
/**
 * Background Pattern Options
 *
 * @package Divi
 * @sub-package Builder
 * @since 4.15.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Class ET_Builder_Background_Pattern_Options
 *
 * @since 4.15.0
 */
class ET_Builder_Background_Pattern_Options {
	/**
	 * Class instance object.
	 *
	 * @var ET_Builder_Background_Pattern_Options
	 */
	private static $_instance;

	/**
	 * Pattern Settings.
	 *
	 * @var array
	 */
	private static $_settings = null;

	/**
	 * Get instance of ET_Builder_Background_Pattern_Options.
	 *
	 * @return ET_Builder_Background_Pattern_Options
	 */
	public static function get() {
		if ( empty( self::$_instance ) ) {
			self::$_instance = new ET_Builder_Background_Pattern_Options();
		}

		return self::$_instance;
	}

	/**
	 * Get SVG Settings for a Pattern Style.
	 *
	 * @param string $name Style name.
	 *
	 * @return array
	 */
	public function get_style( $name ) {
		// Fetch style when settings already processed.
		if ( isset( self::$_settings['styles'][ $name ] ) ) {
			return self::$_settings['styles'][ $name ];
		}

		// Fetch settings for the pattern style.
		$instance = ET_Builder_Background_Pattern_Style_Factory::get( $name );

		if ( ! empty( $instance ) ) {
			return $instance->settings();
		}

		return array();
	}

	/**
	 * Returns SVG content for a Pattern style.
	 *
	 * @param string $name Style Name.
	 * @param string $color Color value.
	 * @param string $type SVG Type.
	 * @param bool   $rotated Default false, set true to get rotated version.
	 * @param bool   $inverted Default false, set true to get inverted version.
	 *
	 * @return string
	 */
	public function get_svg( $name, $color, $type, $rotated = false, $inverted = false ) {
		if ( strpos( $color, 'gcid-' ) === 0 ) {
			$global_color_info = et_builder_get_global_color_info( $color );

			$color = $global_color_info['color'];
		}

		$content = $this->get_svg_content( $name, $type, $rotated, $inverted );
		$props   = et_()->get_svg_attrs(
			array(
				'fill'                => esc_attr( $color ),
				'height'              => esc_attr( $this->get_value( $name, 'height', $rotated ) ),
				'width'               => esc_attr( $this->get_value( $name, 'width', $rotated ) ),
				'viewBox'             => esc_attr( $this->get_value( $name, 'viewBox', $rotated ) ),
				'preserveAspectRatio' => 'none',
			)
		);

		$svg = "<svg {$props}>{$content}</svg>";

		// Encode the SVG so we can use it as data for background-image.
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- base64_encode() used for browser support.
		$svg = base64_encode( $svg );

		return sprintf( 'url( data:image/svg+xml;base64,%s )', $svg );
	}

	/**
	 * Get SVG content for a Pattern Style.
	 *
	 * @param string $name Pattern style name.
	 * @param string $type Valid options: default | thumbnail.
	 * @param bool   $rotated Default false, set true to get rotated version.
	 * @param bool   $inverted Default false, set true to get inverted version.
	 *
	 * @return string
	 */
	public function get_svg_content( $name, $type, $rotated = false, $inverted = false ) {
		$settings = $this->get_style( $name );

		// Return SVG Content for Thumbnail.
		if ( 'thumbnail' === $type ) {
			return isset( $settings['svgContent']['thumbnail'] )
				? $settings['svgContent']['thumbnail']
				: '';
		}

		// Return SVG Content for Style.
		$svg_type = $rotated ? 'rotated' : $type;
		$svg_type = $inverted ? "{$svg_type}-inverted" : $svg_type;

		return isset( $settings['svgContent'][ $svg_type ] )
			? $settings['svgContent'][ $svg_type ]
			: '';
	}

	/**
	 * Get Width/Height/viewBox for a Pattern Style.
	 *
	 * @param string $name    Style name.
	 * @param string $type    Value Style.
	 * @param bool   $rotated Default false, set true to get rotated version.
	 *
	 * @return string
	 */
	public function get_value( $name, $type, $rotated = false ) {
		$settings = $this->get_style( $name );
		$width    = isset( $settings['width'] ) ? $settings['width'] : '';
		$height   = isset( $settings['height'] ) ? $settings['height'] : '';

		switch ( true ) {
			case 'width' === $type:
				// When rotated, we need to swap the width/height.
				return $rotated ? $height : $width;
			case 'height' === $type:
				// When rotated, we need to swap the width/height.
				return $rotated ? $width : $height;
			case 'viewBox' === $type:
				// The viewBox format is '[x] [y] [width] [height]'.
				// When rotated, we need to swap the width/height.
				return $rotated
					? '0 0 ' . (int) $height . ' ' . (int) $width
					: '0 0 ' . (int) $width . ' ' . (int) $height;
			default:
				return '';
		}
	}

	/**
	 * Get value for thumbnail settings.
	 *
	 * @param string $key Attr key.
	 *
	 * @return string
	 */
	public function get_thumbnail_value( $key ) {
		$thumbnail = $this->thumbnail_settings();

		return isset( $thumbnail[ $key ] )
			? $thumbnail[ $key ]
			: '';
	}

	/**
	 * Pattern SVG Settings.
	 *
	 * @return array
	 */
	public function settings() {
		if ( null === self::$_settings ) {
			// Look at builder/feature/background-masks/pattern directory.
			self::$_settings = array(
				'styles'    => glob( ET_BUILDER_DIR . 'feature/background-masks/pattern/*.php' ),
				'thumbnail' => $this->thumbnail_settings(),
			);

			// Default pattern style.
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
	 * Default thumbnail settings for Pattern.
	 *
	 * @return string[]
	 */
	public function thumbnail_settings() {
		return array(
			'height' => '60px',
			'width'  => '80px',
		);
	}

	/**
	 * Get default pattern style.
	 *
	 * @return string Default Style Name.
	 */
	public function get_default_style_name() {
		return 'polka-dots';
	}
}
