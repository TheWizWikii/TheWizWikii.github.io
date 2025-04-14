<?php

/**
 * Class ET_Builder_Module_Helper_Slider
 */
class ET_Builder_Module_Helper_Slider {

	/**
	 * Returns slider arrows CSS selector
	 *
	 * @since 3.25.3
	 *
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function get_arrows_selector( $prefix = '%%order_class%%' ) {
		return implode(
			',',
			array(
				"$prefix .et-pb-slider-arrows .et-pb-arrow-prev",
				"$prefix .et-pb-slider-arrows .et-pb-arrow-next",
			)
		);
	}

	/**
	 * Returns slider dots CSS selector
	 *
	 * @since 3.25.3
	 *
	 * @param string $prefix
	 *
	 * @return string
	 */
	public function get_dots_selector( $prefix = '%%order_class%%' ) {
		return "$prefix .et-pb-controllers a, $prefix .et-pb-controllers .et-pb-active-control";
	}

	/**
	 * Reapply (fullwidth) post slider's module background on slide item which has featured image
	 *
	 * @since 4.3.3
	 *
	 * @param array $args {
	 *     @type int         $slide_post_id
	 *     @type string|bool $post_featured_image
	 *     @type string      $render_slug
	 *     @type array       $props
	 * }
	 */
	public static function reapply_module_background_on_slide( $args = array() ) {
		$defaults = array(
			'slide_post_id'       => 0,
			'post_featured_image' => false,
			'render_slug'         => '',
			'props'               => array(),
		);

		// Parse argument
		$args = wp_parse_args( $args, $defaults );

		// Create slide class
		$slide_id_class = "et_pb_post_slide-{$args['slide_post_id']}";

		// Reapply background color (affecting blend mode), gradient (can be placed on top of image
		// creating overlay-effect), and images (gradient is actually image) if:
		// 1. Featured image exist on current slide
		// 2. Featured image is shown (responsive)
		// 3. Featured image placement is placed on background
		// 4. Parallax (responsive) is off

		// 1. Exit if featured image doesn't exist on current slide
		if ( ! $args['post_featured_image'] ) {
			return;
		}

		$props = $args['props'];

		// 2. Exit if featured image is not shown
		$is_show_image_responsive = et_pb_responsive_options()->is_responsive_enabled( $props, 'show_image' );
		$is_featured_image_shown  = $is_show_image_responsive ?
			in_array( 'on', et_pb_responsive_options()->get_property_values( $props, 'show_image' ) ) :
			'on' === et_()->array_get( $props, 'show_image' );

		if ( ! $is_featured_image_shown ) {
			return;
		}

		// 3. Exit if feature image is not placed in background
		if ( 'background' !== et_()->array_get( $props, 'image_placement' ) ) {
			return;
		}

		// 4. Exit if parallax is activated
		$is_parallax_responsive = et_pb_responsive_options()->is_responsive_enabled( $props, 'parallax' );
		$is_parallax_active     = $is_parallax_responsive ?
			in_array( 'on', et_pb_responsive_options()->get_property_values( $props, 'parallax' ) ) :
			'on' === et_()->array_get( $props, 'parallax' );

		if ( $is_parallax_active ) {
			return;
		}

		// Process background
		$props['background_image']        = $args['post_featured_image'];
		$props['background_enable_image'] = 'on';

		// Background responsive is generally set via background_last_edited instead of each background
		// type's *_last_edited; when background's responsive active and no background image is set,
		// background-image property will be set to `initial` and featured image on current image got
		// removed on current breakpoint. Thus,  Set background image responsive attribute on current
		// background_image attribute to keep it visible
		if ( et_pb_responsive_options()->is_responsive_enabled( $props, 'background' ) ) {
			$props['background_image_last_edited'] = '';
			$props['background_image_tablet']      = $args['post_featured_image'];
			$props['background_image_phone']       = $args['post_featured_image'];

		}

		if ( et_builder_is_hover_enabled( 'background', $props ) ) {
			$props['background_image__hover'] = $args['post_featured_image'];
		}

		et_pb_background_options()->get_background_style(
			array(
				'props'          => $props,
				'selector'       => "%%order_class%% .{$slide_id_class}",
				'selector_hover' => "%%order_class%%:hover .{$slide_id_class}",
				'function_name'  => $args['render_slug'],
			)
		);

		return;
	}
}

