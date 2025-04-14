<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Overlay helper methods.
 *
 * Class ET_Builder_Module_Helper_Overlay
 */
class ET_Builder_Module_Helper_Overlay {
	/**
	 * Get an overlay html tag's attributes.
	 *
	 * @since 3.29
	 *
	 * @param array $args
	 *
	 * @return array
	 */
	public static function get_attributes( $args ) {
		$attributes = array();

		if ( ! empty( $args['icon'] ) ) {
			$attributes['data-icon'] = et_pb_extended_process_font_icon( $args['icon'] );
		}

		if ( ! empty( $args['icon_tablet'] ) ) {
			$attributes['data-icon-tablet'] = et_pb_extended_process_font_icon( $args['icon_tablet'] );
		}

		if ( ! empty( $args['icon_phone'] ) ) {
			$attributes['data-icon-phone'] = et_pb_extended_process_font_icon( $args['icon_phone'] );
		}

		if ( ! empty( $args['icon_sticky'] ) ) {
			$attributes['data-icon-sticky'] = et_pb_extended_process_font_icon( $args['icon_sticky'] );
		}

		return $attributes;
	}

	/**
	 * Render an overlay html tag's attributes.
	 *
	 * @since 3.29
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public static function render_attributes( $args ) {
		$attributes = self::get_attributes( $args );
		$html       = array();

		foreach ( $attributes as $attribute => $value ) {
			$html[] = sprintf(
				'%1$s="%2$s"',
				et_core_intentionally_unescaped( $attribute, 'fixed_string' ),
				et_core_esc_previously( $value )
			);
		}

		return implode( ' ', $html );
	}

	/**
	 * Render an overlay html tag.
	 *
	 * @since 3.29
	 *
	 * @param array $args
	 *
	 * @return string
	 */
	public static function render( $args ) {
		$attributes = et_core_esc_previously( self::render_attributes( $args ) );
		$classes    = array( 'et_overlay' );

		if ( ! empty( $args['icon'] ) ) {
			$classes[] = 'et_pb_inline_icon';
		}

		if ( ! empty( $args['icon_tablet'] ) ) {
			$classes[] = 'et_pb_inline_icon_tablet';
		}

		if ( ! empty( $args['icon_phone'] ) ) {
			$classes[] = 'et_pb_inline_icon_phone';
		}

		if ( ! empty( $args['icon_sticky'] ) ) {
			$classes[] = 'et_pb_inline_icon_sticky';
		}

		return sprintf(
			'<span class="%1$s"%2$s></span>',
			et_core_intentionally_unescaped( implode( ' ', $classes ), 'fixed_string' ),
			( '' !== $attributes ? ' ' . $attributes : '' )
		);
	}
}
