<?php
/**
 * Handle Dynamic Assets
 *
 * @package Builder
 */

/**
 * Gets the assets directory.
 *
 * @param bool $url check if url.
 *
 * @return string
 * @since 4.10.0
 */
function et_get_dynamic_assets_path( $url = false ) {
	$is_builder_active = et_is_builder_plugin_active();

	$template_address = $url ? get_template_directory_uri() : get_template_directory();

	if ( $is_builder_active ) {
		$template_address = $url ? ET_BUILDER_PLUGIN_URI : ET_BUILDER_PLUGIN_DIR;
	}

	return apply_filters( 'et_dynamic_assets_prefix', $template_address . '/includes/builder/feature/dynamic-assets/assets' );
}

/**
 * Checks if current post/page is built-in.
 *
 * @return bool
 * @since 4.10.0
 */
function et_is_cpt() {
	static $is_cpt = null;

	if ( null === $is_cpt ) {
		global $post;

		$custom_post_types = get_post_types( array( '_builtin' => false ) );
		$custom_types      = array();
		$is_cpt            = false;

		if ( ! empty( $custom_post_types ) ) {
			$custom_types = array_keys( $custom_post_types );
		}

		$post_type = get_post_type( $post );

		if ( in_array( $post_type, $custom_types, true ) && is_singular() && 'project' !== $post_type ) {
			$is_cpt = true;
		}
	}

	return $is_cpt;
}

/**
 * Extracts gutter width values from post/page content.
 *
 * @param array $matches matched gutters.
 *
 * @return array
 * @since 4.10.0
 */
function et_get_content_gutter_widths( $matches ) {
	$gutters = array();

	foreach ( $matches as $match ) {
		preg_match_all( '/"([^"]+)"/', $match, $matches );
		$gutters = array_merge( $gutters, $matches[1] );
	}

	// Convert strings to integers.
	$gutters = array_map( 'intval', $gutters );

	return $gutters;
}

/**
 * Check if any widgets are currently active.
 *
 * @return bool
 * @since 4.10.0
 */
function et_pb_are_widgets_used() {
	global $wp_registered_sidebars;

	$sidebars = get_option( 'sidebars_widgets' );

	foreach ( $wp_registered_sidebars as $sidebar_key => $sidebar_options ) {
		if ( ! empty( $sidebars[ $sidebar_key ] ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Check if a specific value is "on" on the page.
 *
 * @param array $values matched values.
 *
 * @return bool
 * @since 4.10.0
 */
function et_check_if_particular_value_is_on( $values ) {
	foreach ( $values as $match ) {
		preg_match_all( '/"([^"]+)"/', $match, $matches );
		if ( in_array( 'on', $matches[1], true ) ) {
			return true;
		};
	}

	return false;
}

/**
 * Get if a non-default preset value.
 *
 * @param array $values Matched values.
 *
 * @return array
 * @since 4.10.0
 */
function et_get_non_default_preset_ids( $values ) {
	$result = array();

	foreach ( $values as $match ) {

		preg_match_all( '/"([^"]+)"/', $match, $matches );

		if ( ! in_array( 'default', $matches[1], true ) ) {
			$result = array_merge( $result, $matches[1] );
		}
	}

	return $result;
}

/**
 * Check to see if this is a front end request applicable to Dynamic Assets.
 *
 * @since 4.10.0
 *
 * @return bool
 */
function et_is_dynamic_front_end_request() {
	static $is_dynamic_front_end_request = null;

	if ( null === $is_dynamic_front_end_request ) {
		if (
			// Disable for WordPress admin requests.
			! is_admin()
			// Disable for non-front-end requests.
			&& ! wp_doing_ajax()
			&& ! wp_doing_cron()
			&& ! wp_is_json_request()
			&& ! ( defined( 'REST_REQUEST' ) && REST_REQUEST )
			&& ! ( defined( 'WP_CLI' ) && WP_CLI )
			&& ! ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST )
			&& ! is_trackback()
			&& ! is_feed()
			&& ! get_query_var( 'sitemap' )
			// Disable when in preview modes.
			&& ! is_customize_preview()
			&& ! is_et_pb_preview()
			&& ! ET_GB_Block_Layout::is_layout_block_preview()
			&& ! is_preview()
			// Disable when using the visual builder.
			&& ! et_fb_is_enabled()
			// Disable on paginated index pages when blog style mode is enabled and when using the Divi Builder plugin.
			&& ! ( is_paged() && ( 'on' === et_get_option( 'divi_blog_style', 'off' ) || et_is_builder_plugin_active() ) )
		) {
			$is_dynamic_front_end_request = true;
		}
	}

	return $is_dynamic_front_end_request;
}

/**
 * Check to see if this is a front end request.
 *
 * @since 4.10.0
 *
 * @return bool
 */
function et_is_front_end_request() {
	static $et_is_front_end_request = null;

	if ( null === $et_is_front_end_request ) {
		if (
			// Disable for WordPress admin requests.
			! is_admin()
			&& ! wp_doing_ajax()
			&& ! wp_doing_cron()
		) {
			$et_is_front_end_request = true;
		}
	}

	return $et_is_front_end_request;
}

/**
 * Check if the current request should generate Dynamic Assets.
 * We only generate dynamic assets on the front end and when cache dir is writable.
 *
 * @since 4.10.0
 *
 * @return bool
 */
function et_should_generate_dynamic_assets() {
	static $should_generate_assets = null;

	if ( null === $should_generate_assets ) {
		if (
			// Cache directory must be writable.
			et_core_cache_dir()->can_write
			// Request must be an applicable front-end request.
			&& et_is_dynamic_front_end_request()
		) {
			$should_generate_assets = true;
		}
	}

	/**
	 * Filters whether to generate dynamic assets.
	 *
	 * @since 4.10.6
	 *
	 * @param bool $should_generate_assets
	 */
	return apply_filters( 'et_should_generate_dynamic_assets', (bool) $should_generate_assets );
}

/**
 * Check if Dynamic CSS is enabled.
 *
 * @return bool
 * @since 4.10.0
 */
function et_use_dynamic_css() {
	global $shortname;
	static $et_use_dynamic_css = null;

	if ( null === $et_use_dynamic_css ) {
		if ( et_is_builder_plugin_active() ) {
			$options     = get_option( 'et_pb_builder_options', array() );
			$dynamic_css = isset( $options['performance_main_dynamic_css'] ) ? $options['performance_main_dynamic_css'] : 'on';
		} else {
			$dynamic_css = et_get_option( $shortname . '_dynamic_css', 'on' );
		}

		if ( 'on' === $dynamic_css && et_should_generate_dynamic_assets() ) {
			$et_use_dynamic_css = true;
		}
	}

	/**
	 * Filters whether to use dynamic CSS.
	 *
	 * @since 4.10.6
	 *
	 * @param bool $et_use_dynamic_css
	 */
	return apply_filters( 'et_use_dynamic_css', (bool) $et_use_dynamic_css );
}

/**
 * Check if Dynamic Icons are enabled.
 *
 * @since 4.10.0
 */
function et_use_dynamic_icons() {
	global $shortname;
	$child_theme_active = is_child_theme();

	if ( et_is_builder_plugin_active() ) {
		$options       = get_option( 'et_pb_builder_options', array() );
		$dynamic_icons = isset( $options['performance_main_dynamic_icons'] ) ? $options['performance_main_dynamic_icons'] : et_dynamic_icons_default_value();
	} else {
		$dynamic_icons = et_get_option( $child_theme_active ? $shortname . '_dynamic_icons_child_theme' : $shortname . '_dynamic_icons', et_dynamic_icons_default_value() );
	}

	return $dynamic_icons;
}

/**
 * Check if JavaScript On Demand is enabled.
 *
 * @return bool
 * @since 4.10.0
 */
function et_disable_js_on_demand() {
	global $shortname;
	static $et_disable_js_on_demand = null;

	if ( null === $et_disable_js_on_demand ) {
		if ( et_is_builder_plugin_active() ) {
			$options              = get_option( 'et_pb_builder_options', array() );
			$dynamic_js_libraries = isset( $options['performance_main_dynamic_js_libraries'] ) ? $options['performance_main_dynamic_js_libraries'] : 'on';
		} else {
			$dynamic_js_libraries = et_get_option( $shortname . '_dynamic_js_libraries', 'on' );
		}

		if (
			// Disable when theme option not enabled.
			'on' !== $dynamic_js_libraries
			// Disable when not an applicable front-end request.
			|| ! et_is_dynamic_front_end_request()
		) {
			$et_disable_js_on_demand = true;
		}
	}

	/**
	 * Filters whether to disable JS on demand.
	 *
	 * @since 4.10.6
	 *
	 * @param bool $et_disable_js_on_demand
	 */
	return apply_filters( 'et_disable_js_on_demand', (bool) $et_disable_js_on_demand );
}

/**
 * Disable dynamic icons if TP modules are present.
 *
 * @since 4.10.0
 */
function et_dynamic_icons_default_value() {
	$tp_extensions      = DiviExtensions::get( 'all' );
	$child_theme_active = is_child_theme();

	if ( ! empty( $tp_extensions ) || ( $child_theme_active && ! et_is_builder_plugin_active() ) ) {
		return 'off';
	}

	return 'on';
}

/**
 * Get all active block widgets.
 *
 * This method will collect all active block widgets first. Later on, the result will be
 * cached to improve the performance.
 *
 * @since 4.10.5
 *
 * @return array List of active block widgets.
 */
function et_get_active_block_widgets() {
	global $wp_version;
	static $active_block_widgets = null;

	$wp_major_version = substr( $wp_version, 0, 3 );

	// Bail early if were pre WP 5.8, when block widgets were introduced.
	if ( version_compare( $wp_major_version, '5.8', '<' ) ) {
		return array();
	}

	global $wp_widget_factory;

	$active_block_widgets = array();
	$block_instance       = $wp_widget_factory->get_widget_object( 'block' );
	$block_settings       = $block_instance->get_settings();

	// Bail early if there is no active block widgets.
	if ( empty( $block_settings ) ) {
		return $active_block_widgets;
	}

	// Collect all active blocks.
	foreach ( $block_settings as $block_number => $block_setting ) {
		$block_content = et_()->array_get( $block_setting, 'content' );
		$block_parsed  = parse_blocks( $block_content );
		$block_name    = et_()->array_get( $block_parsed, array( '0', 'blockName' ) );

		// Save and cache there result.
		if ( ! in_array( $block_name, $active_block_widgets, true ) ) {
			array_push( $active_block_widgets, $block_name );
		}
	}

	return $active_block_widgets;
}

/**
 * Check whether current block widget is active or not.
 *
 * @since 4.10.5
 *
 * @param string $block_widget_name Block widget name.
 *
 * @return boolean Whether current block widget is active or not.
 */
function et_is_active_block_widget( $block_widget_name ) {
	return in_array( $block_widget_name, et_get_active_block_widgets(), true );
}

/**
 * Check whether Extra Home layout is being used.
 *
 * @since 4.17.5
 *
 * @return boolean whether Extra Home layout is being used.
 */
function et_is_extra_layout_used_as_front() {
	return function_exists( 'et_extra_show_home_layout' ) && et_extra_show_home_layout() && is_front_page();
}

/**
 * Check whether Extra Home layout is being used.
 *
 * @since 4.17.5
 *
 * @return boolean whether Extra Home layout is being used.
 */
function et_is_extra_layout_used_as_home() {
	return function_exists( 'et_extra_show_home_layout' ) && et_extra_show_home_layout() && is_home();
}

/**
 * Get Extra Home layout ID.
 *
 * @since 4.17.5
 *
 * @return int|null
 */
function et_get_extra_home_layout_id() {
	if ( function_exists( 'extra_get_home_layout_id' ) ) {
		return extra_get_home_layout_id();
	}
	return null;
}

/**
 *  Get Extra Taxonomy layout ID.
 *
 * @since 4.17.5
 *
 * @return int|null
 */
function et_get_extra_tax_layout_id() {
	if ( function_exists( 'extra_get_tax_layout_id' ) ) {
		return extra_get_tax_layout_id();
	}
	return null;
}

/**
 * Get embeded media from post content.
 *
 * @since 4.20.1
 *
 * @param int $content Post Content.
 *
 * @return boolean false on failure, true on success.
 */
function et_is_media_embedded_in_content( $content ) {
	if ( ! $content ) {
		return false;
	}

	// regex match for youtube and vimeo urls in $content.
	$pattern = '~https?://(?:www\.)?(?:youtube\.com/watch\?v=|youtu\.be/|vimeo\.com/)([^\s]+)~i';
	preg_match_all( $pattern, $content, $matches );

	if ( empty( $matches[0] ) ) {
		return false;
	}

	return true;
}
