<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

// Include dependency for ResponsiveOptions.
if ( ! function_exists( 'et_pb_responsive_options' ) ) {
	require_once 'ResponsiveOptions.php';
}

// Include dependency for HoverOptions.
if ( ! function_exists( 'et_pb_hover_options' ) ) {
	require_once 'HoverOptions.php';
}

/**
 * Background layout helper methods.
 *
 * @since 4.0.7
 *
 * Class ET_Builder_Module_Helper_BackgroundLayout
 */
class ET_Builder_Module_Helper_BackgroundLayout {

	public static function instance() {
		static $instance;

		return $instance ? $instance : $instance = new self();
	}

	/**
	 * Get background layout class names.
	 *
	 * @since 4.0.7
	 *
	 * @param  array   $attrs
	 * @param  boolean $is_skip_desktop Not all modules need to print desktop background layout.
	 * @param  boolean $is_text_color   Not all modules need text color layout class name.
	 *
	 * @return array
	 */
	public function get_background_layout_class( $attrs, $is_skip_desktop = false, $is_text_color = false ) {
		// Background layout values.
		$background_layouts       = et_pb_responsive_options()->get_property_values( $attrs, 'background_layout' );
		$background_layout        = et_()->array_get( $background_layouts, 'desktop', '' );
		$background_layout_tablet = et_()->array_get( $background_layouts, 'tablet', '' );
		$background_layout_phone  = et_()->array_get( $background_layouts, 'phone', '' );
		$background_layout_hover  = et_pb_hover_options()->get_value( 'background_layout', $attrs, 'light' );

		// Background layout class names.
		$background_layout_class_names = ! $is_skip_desktop ? array( "et_pb_bg_layout_{$background_layout}" ) : array();

		if ( ! empty( $background_layout_tablet ) ) {
			$background_layout_class_names[] = "et_pb_bg_layout_{$background_layout_tablet}_tablet";
		}

		if ( ! empty( $background_layout_phone ) ) {
			$background_layout_class_names[] = "et_pb_bg_layout_{$background_layout_phone}_phone";
		}

		// Text color class names.
		if ( $is_text_color ) {
			if ( 'light' === $background_layout ) {
				$background_layout_class_names[] = 'et_pb_text_color_dark';
			}

			if ( 'light' === $background_layout_tablet ) {
				$background_layout_class_names[] = 'et_pb_text_color_dark_tablet';
			}

			if ( 'light' === $background_layout_phone ) {
				$background_layout_class_names[] = 'et_pb_text_color_dark_phone';
			}
		}

		return $background_layout_class_names;
	}

	/**
	 * Get background layout data attributes.
	 *
	 * @since 4.0.7
	 *
	 * @param array $attrs
	 *
	 * @return string
	 */
	public function get_background_layout_attrs( $attrs ) {
		// Background layout data attributes is only needed by hover or sticky effect.
		if ( ! et_pb_hover_options()->is_enabled( 'background_layout', $attrs ) && ! et_pb_sticky_options()->is_enabled( 'background_layout', $attrs ) ) {
			return '';
		}

		// Background layout values.
		$background_layouts       = et_pb_responsive_options()->get_property_values( $attrs, 'background_layout' );
		$background_layout        = et_()->array_get( $background_layouts, 'desktop', '' );
		$background_layout_tablet = et_()->array_get( $background_layouts, 'tablet', '' );
		$background_layout_phone  = et_()->array_get( $background_layouts, 'phone', '' );
		$background_layout_hover  = et_pb_hover_options()->get_value( 'background_layout', $attrs, '' );
		$background_layout_sticky = et_pb_sticky_options()->get_value( 'background_layout', $attrs, '' );

		$data_background_layout = sprintf(
			' data-background-layout="%1$s"',
			esc_attr( $background_layout )
		);

		if ( ! empty( $background_layout_hover ) ) {
			$data_background_layout .= sprintf(
				' data-background-layout-hover="%1$s"',
				esc_attr( $background_layout_hover )
			);
		}

		if ( ! empty( $background_layout_sticky ) ) {
			$data_background_layout .= sprintf(
				' data-background-layout-sticky="%1$s"',
				esc_attr( $background_layout_sticky )
			);
		}

		if ( ! empty( $background_layout_tablet ) ) {
			$data_background_layout .= sprintf(
				' data-background-layout-tablet="%1$s"',
				esc_attr( $background_layout_tablet )
			);
		}

		if ( ! empty( $background_layout_phone ) ) {
			$data_background_layout .= sprintf(
				' data-background-layout-phone="%1$s"',
				esc_attr( $background_layout_phone )
			);
		}

		return $data_background_layout;
	}
}
