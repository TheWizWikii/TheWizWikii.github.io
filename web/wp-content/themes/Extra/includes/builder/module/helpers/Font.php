<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

// Include dependency for ResponsiveOptions.
if ( ! function_exists( 'et_pb_responsive_options' ) ) {
	require_once 'ResponsiveOptions.php';
}

/**
 * Font helper methods.
 *
 * @since 4.0
 *
 * Class ET_Builder_Module_Helper_Font
 */
class ET_Builder_Module_Helper_Font {

	public static function instance() {
		static $instance;

		return $instance ? $instance : $instance = new self();
	}

	/**
	 * Check if current font is Default or not.
	 *
	 * @since 4.0
	 *
	 * @param array  $attrs
	 * @param string $name
	 * @param string $device
	 *
	 * @return boolean
	 */
	public function is_font_default( $attrs, $name, $device = 'desktop' ) {
		return 'Default' === $this->get_font_value( $attrs, $name, $device );
	}

	/**
	 * Check if current font is empty or not.
	 *
	 * @since 4.0
	 *
	 * @param array  $attrs
	 * @param string $name
	 * @param string $device
	 *
	 * @return boolean
	 */
	public function is_font_empty( $attrs, $name, $device = 'desktop' ) {
		return '' === $this->get_font_value( $attrs, $name, $device );
	}

	/**
	 * Get font value based on device.
	 *
	 * @since 4.0
	 *
	 * @param  array  $attrs
	 * @param  string $name
	 * @param  string $device
	 *
	 * @return string
	 */
	public function get_font_value( $attrs, $name, $device = 'desktop' ) {
		$value        = et_pb_responsive_options()->get_property_value( $attrs, $name, '', $device, true );
		$value_pieces = ! empty( $value ) && is_string( $value ) ? explode( '|', $value ) : array();
		return et_()->array_get( $value_pieces, 0, '' );
	}

	/**
	 * Get custom breakpoint by font value.
	 *
	 * There is a case where tablet and phone use Default font. Default font means the element will
	 * use the original or font defined on Theme Customizer. It's different with empty string which
	 * means the font will be inherited from the larger device. So, when current device use non
	 * default font, we should check smaller device uses default font or not. If the smaller device
	 * use default font, we have to render current font inclusidely  on current device, something
	 * likes desktop_only, tablet_only, or desktop_tablet_only.
	 *
	 * @since 4.0
	 *
	 * @param  array  $attrs
	 * @param  string $name
	 * @param  string $device
	 * @param  string $default_breakpoint
	 *
	 * @return string
	 */
	public function get_breakpoint_by_font_value( $attrs, $name, $device = 'desktop', $default_breakpoint = '' ) {
		// Bail early if current $device value is default or empty.
		if ( $this->is_font_default( $attrs, $name, $device ) || $this->is_font_empty( $attrs, $name, $device ) ) {
			return $default_breakpoint;
		}

		// Phone - There is no smaller $device than phone, no need to check.
		if ( 'phone' === $device ) {
			return $default_breakpoint;
		}

		$is_phone_default  = $this->is_font_default( $attrs, $name, 'phone' );
		$is_tablet_default = $this->is_font_default( $attrs, $name, 'tablet' );

		// Tablet.
		if ( 'tablet' === $device ) {
			// Return breakpoint for tablet only if phone uses default, otherwise return default.
			return $is_phone_default ? et_pb_responsive_options()->get_breakpoint_by_device( 'tablet_only' ) : $default_breakpoint;
		}

		// Desktop.
		if ( $is_tablet_default ) {
			// Return breakpoint for desktop only if tablet uses default.
			return et_pb_responsive_options()->get_breakpoint_by_device( 'desktop_only' );
		} elseif ( $is_phone_default ) {
			// Return breakpoint for desktop & only if tablet uses default.
			return et_pb_responsive_options()->get_breakpoint_by_device( 'desktop_tablet_only' );
		}

		return $default_breakpoint;
	}

	/**
	 * Get font selector based on settings.
	 *
	 * @since 4.0
	 *
	 * @param  array  $option_settings
	 * @param  string $main_css_element
	 *
	 * @return string
	 */
	public function get_font_selector( $option_settings, $main_css_element ) {
		// Get main CSS selector.
		$main_selector         = et_()->array_get( $option_settings, 'css.main', $main_css_element );
		$limited_main_selector = et_()->array_get( $option_settings, 'css.limited_main', '' );
		$font_selector         = et_()->array_get( $option_settings, 'css.font', '' );

		// Use different selector for plugin if defined.
		if ( et_builder_has_limitation( 'use_limited_main' ) && ! empty( $limited_main_selector ) ) {
			$main_selector = $limited_main_selector;
		}

		// Use font selector if it's specified
		if ( ! empty( $font_selector ) ) {
			$main_selector = $font_selector;
		}

		// Join all the main selectors if it's an array.
		if ( is_array( $main_selector ) ) {
			$main_selector = implode( ', ', $main_selector );
		}

		return $main_selector;
	}
}
