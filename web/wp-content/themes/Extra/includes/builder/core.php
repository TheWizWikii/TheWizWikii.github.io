<?php
/**
 * Builder's core functionality.
 *
 * @package Builder
 * @since 1.0
 */

/**
 * Sanitizes the post content before saving it.
 *
 * This function is used as a filter callback for the 'pre_post_content' filter hook.
 * It removes the 'data-et-multi-view' attribute from the post content, unless the current user has the 'unfiltered_html' capability.
 *
 * @since 4.25.1
 *
 * @param string $post_content The post content to be sanitized.
 * @return string The sanitized post content.
 */
function et_builder_sanitize_post_content_before_saving( $post_content ) {
	if ( current_user_can( 'unfiltered_html' ) ) {
		return $post_content;
	}

	return str_ireplace( 'data-et-multi-view', '', $post_content );
}
add_filter( 'pre_post_content', 'et_builder_sanitize_post_content_before_saving' );

/**
 * Render a builder layout to string.
 *
 * @since 4.0.8
 *
 * @param string $content the content.
 *
 * @return string
 */
function et_builder_render_layout( $content ) {
	/**
	 * Filters layout content when it's being rendered.
	 *
	 * @since 4.0.8
	 *
	 * @param string $content
	 */
	return apply_filters( 'et_builder_render_layout', $content );
}

// Add all core filters that are applied to the_content() without do_blocks().
add_filter( 'et_builder_render_layout', 'capital_P_dangit', 11 );
add_filter( 'et_builder_render_layout', 'wptexturize' );
add_filter( 'et_builder_render_layout', 'convert_smilies', 20 );
add_filter( 'et_builder_render_layout', 'wpautop' );
add_filter( 'et_builder_render_layout', 'shortcode_unautop' );
add_filter( 'et_builder_render_layout', 'prepend_attachment' );
add_filter( 'et_builder_render_layout', 'do_shortcode', 11 ); // AFTER wpautop().

// Temporarily remove wp_filter_content_tags() from the_content, then call it again by
// running et_builder_filter_content_image_tags() after do_shortcode() to fill any
// missing height and width attributes on the image. Those attributes are required
// to add loading "lazy" attribute on the image. In this case, we set the order as
// 12 because TB runs do_shortcode() on order 11.
remove_filter( 'the_content', 'wp_filter_content_tags' );
add_filter( 'the_content', 'et_builder_filter_content_image_tags', 12 );
add_filter( 'et_builder_render_layout', 'et_builder_filter_content_image_tags', 12 );

if ( ! function_exists( 'et_builder_filter_content_image_tags' ) ) {
	/**
	 * Whether filter image tags on post content with different functions.
	 *
	 * @since 4.5.2
	 *
	 * @param string $content The HTML content to be filtered.
	 *
	 * @return string Converted content with images modified.
	 */
	function et_builder_filter_content_image_tags( $content ) {
		if ( function_exists( 'wp_filter_content_tags' ) ) {
			// Function wp_filter_content_tags() is introduced on WP 5.5 forward.
			$content = wp_filter_content_tags( $content );
		} else {
			// Function wp_make_content_images_responsive() is used by WP 5.4 below.
			$content = wp_make_content_images_responsive( $content );
		}

		return $content;
	}
}

if ( ! function_exists( 'et_builder_add_filters' ) ) :
	/**
	 * Add common filters depending on what builder is being used.
	 * These hooks are not used in DBP as it has its own implementations for them.
	 *
	 * @return void
	 */
	function et_builder_add_filters() {
		if ( et_is_builder_plugin_active() ) {
			return;
		}

		add_filter( 'et_builder_bfb_enabled', 'et_builder_filter_bfb_enabled' );
		add_filter( 'et_builder_is_fresh_install', 'et_builder_filter_is_fresh_install' );
		add_action( 'et_builder_toggle_bfb', 'et_builder_action_toggle_bfb' );
	}
endif;
add_action( 'init', 'et_builder_add_filters' );

if ( ! function_exists( 'et_builder_should_load_framework' ) ) :
	/**
	 * Determine whether Divi Builder codebase should be loaded.
	 *
	 * @return bool
	 */
	function et_builder_should_load_framework() {
		global $pagenow;
		// reason: Since we are accessing $_GET only for the comparision, nonce verification is not required.
		// phpcs:disable WordPress.Security.NonceVerification

		static $should_load = null;

		if ( null !== $should_load ) {
			return $should_load;
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			$should_load = true;
			return $should_load;
		}

		if ( ET_Core_Portability::doing_import() ) {
			$should_load = true;
			return $should_load;
		}

		$is_admin              = is_admin();
		$required_admin_pages  = array( 'edit.php', 'post.php', 'post-new.php', 'admin.php', 'customize.php', 'edit-tags.php', 'admin-ajax.php', 'export.php', 'options-permalink.php', 'themes.php', 'revision.php', 'widgets.php', 'site-editor.php' ); // list of admin pages where we need to load builder files.
		$specific_filter_pages = array( 'edit.php', 'post.php', 'post-new.php', 'admin.php', 'edit-tags.php' ); // list of admin pages where we need more specific filtering.
		$post_id               = (int) et_()->array_get( $_GET, 'post', 0 );

		$bfb_settings = get_option( 'et_bfb_settings' );
		$is_bfb       = et_pb_is_allowed( 'use_visual_builder' ) && isset( $bfb_settings['enable_bfb'] ) && 'on' === $bfb_settings['enable_bfb'];
		$is_bfb_used  = 'on' === get_post_meta( $post_id, '_et_pb_use_builder', true ) && $is_bfb;

		$is_edit_library_page  = in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ), true ) && ( ( isset( $_GET['post_type'] ) && 'et_pb_layout' === $_GET['post_type'] ) || ( $post_id && 'et_pb_layout' === get_post_type( $post_id ) ) );
		$is_extra_builder      = $post_id && 'layout' === get_post_type( $post_id );
		$is_edit_page_not_bfb  = in_array( $pagenow, array( 'post-new.php', 'post.php' ), true ) && ! $is_bfb_used;
		$is_role_editor_page   = 'admin.php' === $pagenow && isset( $_GET['page'] ) && apply_filters( 'et_divi_role_editor_page', 'et_divi_role_editor' ) === $_GET['page'];
		$is_theme_builder_page = 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'et_theme_builder' === $_GET['page'];

		// phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled -- `$_GET['import']` variable does not contain the 'WordPress' string.
		$is_import_page               = 'admin.php' === $pagenow && isset( $_GET['import'] ) && 'wordpress' === $_GET['import']; // Page Builder files should be loaded on import page as well to register the et_pb_layout post type properly.
		$is_wpml_page                 = 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'sitepress-multilingual-cms/menu/languages.php' === $_GET['page']; // Page Builder files should be loaded on WPML clone page as well to register the custom taxonomies properly.
		$is_edit_layout_category_page = 'edit-tags.php' === $pagenow && isset( $_GET['taxonomy'] ) && in_array( $_GET['taxonomy'], array( 'layout_category', 'layout_tag', 'layout_pack' ), true );

		if ( ! $is_admin || ( $is_admin && in_array( $pagenow, $required_admin_pages, true ) && ( ! in_array( $pagenow, $specific_filter_pages, true ) || $is_edit_library_page || $is_role_editor_page || $is_edit_layout_category_page || $is_import_page || $is_wpml_page || $is_edit_page_not_bfb || $is_extra_builder || $is_theme_builder_page ) ) ) {
			$should_load = true;
		} else {
			$should_load = false;
		}

		/**
		 * Filters whether or not the Divi Builder codebase should be loaded for the current request.
		 *
		 * @since 3.0.99
		 *
		 * @param bool $should_load
		 */
		$should_load = apply_filters( 'et_builder_should_load_framework', $should_load );

		return $should_load;
		// phpcs:enable
	}
endif;

if ( ! function_exists( 'et_builder_load_library' ) ) :
	/**
	 * Load Divi Library and Divi Cloud.
	 *
	 * @return void
	 */
	function et_builder_load_library() {
		// Initialize the Divi Library.
		require_once ET_BUILDER_DIR . 'feature/Library.php';
		require_once ET_BUILDER_DIR . 'feature/SplitLibrary.php';

		// Initialize Divi Cloud and AI.
		if ( defined( 'ET_BUILDER_PLUGIN_ACTIVE' ) ) {
			require_once ET_BUILDER_PLUGIN_DIR . '/cloud/cloud-app.php';
			require_once ET_BUILDER_PLUGIN_DIR . '/ai-app/ai-app.php';
		} else {
			require_once get_template_directory() . '/cloud/cloud-app.php';
			require_once get_template_directory() . '/ai-app/ai-app.php';
		}
	}
endif;

if ( et_builder_should_load_framework() ) {
	et_builder_load_library();
}

if ( ! function_exists( 'et_builder_maybe_enable_inline_styles' ) ) :
	/**
	 * Enable builder setting to output css styles inline in the footer.
	 */
	function et_builder_maybe_enable_inline_styles() {
		et_update_option( 'static_css_custom_css_safety_check_done', true );

		if ( ! wp_get_custom_css() ) {
			return;
		}

		// This site has Custom CSS that existed prior to v3.0.54 which could contain syntax
		// errors that the user is unaware of. Such errors would cause problems in a unified
		// static CSS file so let's enable inline styles for the builder's design styles.
		et_update_option( 'et_pb_css_in_footer', 'on' );
	}
endif;

if ( defined( 'ET_CORE_UPDATED' ) && ! et_get_option( 'static_css_custom_css_safety_check_done', false ) ) {
	et_builder_maybe_enable_inline_styles();
}

/**
 * AJAX Callback: Generate video thumbnail from video url.
 */
function et_pb_video_get_oembed_thumbnail() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) { // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- wp_verify_nonce() function does sanitation.
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$video_url = isset( $_POST['et_video_url'] ) ? esc_url( sanitize_text_field( $_POST['et_video_url'] ) ) : '';
	if ( false !== wp_oembed_get( $video_url ) ) {
		// Get image thumbnail.
		add_filter( 'oembed_dataparse', 'et_pb_video_oembed_data_parse', 10, 3 );
		// Save thumbnail.
		$image_src = wp_oembed_get( $video_url );

		// If the image src is empty try making a remote call with domain referer in case it's domain-restricted vimeo video.
		// Ref: https://developer.vimeo.com/api/oembed/videos#embedding-videos-with-domain-privacy .
		$is_vimeo_url = false !== strpos( $video_url, 'vimeo.com' );

		if ( $is_vimeo_url && ! $image_src ) {
			$vimeo_url      = add_query_arg( 'url', $video_url, 'https://vimeo.com/api/oembed.json' );
			$vimeo_response = wp_remote_get(
				$vimeo_url,
				array(
					'headers' => array(
						'Referer' => get_site_url(),
					),
				)
			);

			if ( $vimeo_response ) {
				$vimeo_response_body = wp_remote_retrieve_body( $vimeo_response );

				if ( $vimeo_response_body ) {
					$vimeo_response_body = (array) json_decode( $vimeo_response_body );

					if ( ! empty( $vimeo_response_body['thumbnail_url'] ) ) {
						$image_src = $vimeo_response_body['thumbnail_url'];
					}
				}
			}
		}

		// Set back to normal.
		remove_filter( 'oembed_dataparse', 'et_pb_video_oembed_data_parse', 10, 3 );

		if ( '' === $image_src ) {
			die( -1 );
		}
		echo esc_url( $image_src );
	} else {
		die( -1 );
	}
	die();
}
add_action( 'wp_ajax_et_pb_video_get_oembed_thumbnail', 'et_pb_video_get_oembed_thumbnail' );

if ( ! function_exists( 'et_pb_video_oembed_data_parse' ) ) :
	/**
	 * Remove protocol https or http from the video thumbnail url.
	 *
	 * @param string $return The returned oEmbed HTML.
	 * @param object $data   A data object result from an oEmbed provider.
	 * @param string $url    The URL of the content to be embedded.
	 */
	function et_pb_video_oembed_data_parse( $return, $data, $url ) {
		if ( isset( $data->thumbnail_url ) ) {
			return esc_url( str_replace( array( 'https://', 'http://' ), '//', $data->thumbnail_url ), array( 'http' ) );
		} else {
			return false;
		}
	}
endif;

/**
 * AJAX Callback: Create widget area from wp dashbaoard Widgets screen.
 */
function et_pb_add_widget_area() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	$et_pb_widgets = get_theme_mod( 'et_pb_widgets' );

	$number = $et_pb_widgets ? intval( $et_pb_widgets['number'] ) + 1 : 1;

	$et_widget_area_name                                      = isset( $_POST['et_widget_area_name'] ) ? sanitize_text_field( $_POST['et_widget_area_name'] ) : '';
	$et_pb_widgets['areas'][ 'et_pb_widget_area_' . $number ] = $et_widget_area_name;
	$et_pb_widgets['number']                                  = $number;

	set_theme_mod( 'et_pb_widgets', $et_pb_widgets );

	et_pb_force_regenerate_templates();

	printf(
	// translators: %1$s: widget area name.
		et_get_safe_localization( __( '<strong>%1$s</strong> widget area has been created. You can create more areas, once you finish update the page to see all the areas.', 'et_builder' ) ),
		esc_html( $et_widget_area_name )
	);

	die();
}
add_action( 'wp_ajax_et_pb_add_widget_area', 'et_pb_add_widget_area' );

/**
 * AJAX Callback: Remove widget area from wp dashbaoard Widgets screen.
 */
function et_pb_remove_widget_area() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	$et_pb_widgets = get_theme_mod( 'et_pb_widgets' );

	$et_widget_area_name = isset( $_POST['et_widget_area_name'] ) ? sanitize_text_field( $_POST['et_widget_area_name'] ) : '';
	unset( $et_pb_widgets['areas'][ $et_widget_area_name ] );

	set_theme_mod( 'et_pb_widgets', $et_pb_widgets );

	et_pb_force_regenerate_templates();

	die( esc_html( $et_widget_area_name ) );
}
add_action( 'wp_ajax_et_pb_remove_widget_area', 'et_pb_remove_widget_area' );

/**
 * AJAX Callback: Check if current user has permission to lock/unlock content.
 */
function et_pb_current_user_can_lock() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$permission = et_pb_is_allowed( 'lock_module' );
	$permission = wp_json_encode( (bool) $permission );

	die( et_core_esc_previously( $permission ) );
}
add_action( 'wp_ajax_et_pb_current_user_can_lock', 'et_pb_current_user_can_lock' );

/**
 * Get the supported post types by default.
 *
 * @since 3.10
 *
 * @return string[]
 */
function et_builder_get_default_post_types() {
	/**
	 * Filter default post types that are powered by the builder.
	 *
	 * @since 4.0
	 *
	 * @param string[]
	 */
	return apply_filters(
		'et_builder_default_post_types',
		array(
			// WordPress.
			'page',
			'post',

			// Divi/Extra/DBP.
			'project',
			'et_pb_layout',
		)
	);
}

/**
 * Get the supported third party post types.
 *
 * @since 3.10
 *
 * @return string[]
 */
function et_builder_get_third_party_post_types() {
	$third_party_post_types = array();

	// WooCommerce (https://wordpress.org/plugins/woocommerce/).
	if ( class_exists( 'WooCommerce' ) ) {
		$third_party_post_types[] = 'product';
	}

	// The Events Calendar (https://wordpress.org/plugins/the-events-calendar/).
	if ( class_exists( 'Tribe__Events__Main' ) ) {
		$third_party_post_types[] = 'tribe_events';
	}

	// Popup Maker (https://wordpress.org/plugins/popup-maker/).
	if ( class_exists( 'Popup_Maker' ) ) {
		$third_party_post_types[] = 'popup';
	}

	// All-in-One Event Calendar (https://wordpress.org/plugins/all-in-one-event-calendar/).
	if ( function_exists( 'ai1ec_initiate_constants' ) ) {
		$third_party_post_types[] = 'ai1ec_event';
	}

	// Events Manager (https://wordpress.org/plugins/events-manager/).
	if ( function_exists( 'em_plugins_loaded' ) ) {
		$third_party_post_types[] = 'event';
		$third_party_post_types[] = 'location';
	}

	// Portfolio Post Type (https://wordpress.org/plugins/portfolio-post-type/).
	if ( function_exists( 'portfolio_post_type_init' ) ) {
		$third_party_post_types[] = 'portfolio';
	}

	// LifterLMS (https://wordpress.org/plugins/lifterlms/).
	if ( class_exists( 'LifterLMS' ) ) {
		$third_party_post_types[] = 'course';
	}

	// LearnDash (https://www.learndash.com/wordpress-course-plugin-features/).
	if ( class_exists( 'Semper_Fi_Module' ) ) {
		$third_party_post_types[] = 'sfwd-courses';
		$third_party_post_types[] = 'sfwd-lessons';
	}

	/**
	 * Array of third-party registered post types that should have support enabled by default.
	 *
	 * @since 3.10
	 *
	 * @param string[]
	 */
	return apply_filters( 'et_builder_third_party_post_types', $third_party_post_types );
}

/**
 * Look for builder's registered third party post type that isn't publicly queryable.
 *
 * @since 3.19.9
 *
 * @return array
 */
function et_builder_get_third_party_unqueryable_post_types() {
	// Save the value in static variable so if post type's publicly_queryable is modified due to current
	// request is BFB request, this function still return correct value.
	static $unqueryable_post_types = array();

	if ( empty( $unqueryable_post_types ) ) {
		// Get third party's unqueryable post types only as default post types have been handled properly.
		$valid_third_party_post_types = array_diff(
			et_builder_get_builder_post_types(),
			et_builder_get_default_post_types()
		);

		$unqueryable_post_types = array_intersect(
			$valid_third_party_post_types,
			get_post_types( array( 'publicly_queryable' => false ) )
		);
	}

	return apply_filters( 'et_builder_get_third_party_unqueryable_post_types', $unqueryable_post_types );
}

/**
 * Get the list of registered Post Types options.
 *
 * @since 3.18
 * @since 4.0.7 Added the $require_editor parameter.
 *
 * @param boolean|callable $usort Comparision callback.
 * @param boolean          $require_editor Optional. Whether to retrieve only post type that has editor support.
 *
 * @return array
 */
function et_get_registered_post_type_options( $usort = false, $require_editor = true ) {
	$require_editor_key = $require_editor ? '1' : '0';
	$key                = "et_get_registered_post_type_options:{$require_editor_key}";

	if ( ET_Core_Cache::has( $key ) ) {
		return ET_Core_Cache::get( $key );
	}

	$blocklist = et_builder_get_blocklisted_post_types();
	$allowlist = et_builder_get_third_party_post_types();

	// Extra and Library layouts shouldn't appear in Theme Options as configurable post types.
	/**
	 * Get array of post types to prevent from appearing as options for builder usage.
	 *
	 * @since 4.0
	 *
	 * @param string[] $blocklist Post types to blocklist.
	 */
	$blocklist      = array_merge(
		$blocklist,
		array(
			'et_pb_layout',
			'layout',
		)
	);
	$blocklist      = apply_filters( 'et_builder_post_type_options_blocklist', $blocklist );
	$raw_post_types = get_post_types(
		array(
			'show_ui' => true,
		),
		'objects'
	);
	$post_types     = array();

	foreach ( $raw_post_types as $post_type ) {
		$is_allowlisted  = in_array( $post_type->name, $allowlist, true );
		$is_blocklisted  = in_array( $post_type->name, $blocklist, true );
		$supports_editor = $require_editor ? post_type_supports( $post_type->name, 'editor' ) : true;
		$is_public       = et_builder_is_post_type_public( $post_type->name );

		if ( ! $is_allowlisted && ( $is_blocklisted || ! $supports_editor || ! $is_public ) ) {
			continue;
		}

		$post_types[] = $post_type;
	}

	if ( $usort && is_callable( $usort ) ) {
		usort( $post_types, $usort );
	}

	$post_type_options = array_combine(
		wp_list_pluck( $post_types, 'name' ),
		wp_list_pluck( $post_types, 'label' )
	);

	// did_action() actually checks if the action has started, not ended so we
	// need to check that we are not currently doing the action as well.
	if ( did_action( 'init' ) && ! doing_action( 'init' ) ) {
		// Only cache the value after init is done when we are sure all
		// plugins have registered their post types.
		ET_Core_Cache::add( $key, $post_type_options );
	}

	return $post_type_options;
}

/**
 * Clear post type options cache whenever a custom post type is registered.
 *
 * @since 3.21.2
 *
 * @return void
 */
function et_clear_registered_post_type_options_cache() {
	ET_Core_Cache::delete( 'et_get_registered_post_type_options' );
}

add_action( 'registered_post_type', 'et_clear_registered_post_type_options_cache' );

/**
 * Get the list of unsupported Post Types.
 *
 * @since 4.5.1
 *
 * @return array
 */
function et_builder_get_blocklisted_post_types() {
	$post_type_blocklist = array(
		// LearnDash.
		'sfwd-essays',

		// bbPress.
		'forum',
		'topic',
		'reply',
	);

	return apply_filters( 'et_builder_post_type_blocklist', $post_type_blocklist );
}

/**
 * Get the list of unsupported Post Types.
 *
 * @deprecated ?? No longer used by internal code; use `et_builder_get_blocklisted_post_types` instead.
 *
 * @since 3.10
 * @since 4.5.1 Aliased to `et_builder_get_blocklisted_post_types`.
 *
 * @return array
 */
function et_builder_get_blacklisted_post_types() {
	return et_builder_get_blocklisted_post_types();
}

/**
 * Check whether the supplied post type is a custom post type as far as the builder is concerned.
 *
 * @since 3.10
 *
 * @param string $post_type the post type to be checked.
 *
 * @return boolean
 */
function et_builder_is_post_type_custom( $post_type ) {
	return $post_type && ( ! in_array( $post_type, et_builder_get_default_post_types(), true ) || et_theme_builder_is_layout_post_type( $post_type ) );
}

/**
 * Check whether the supplied post is of a custom post type as far as the builder is concerned.
 * If no post id is supplied, checks whether the current page is the singular view of a custom post type.
 *
 * @since 3.10
 *
 * @param integer $post_id post id.
 *
 * @return boolean
 */
function et_builder_post_is_of_custom_post_type( $post_id = 0 ) {
	$post_types = et_builder_get_default_post_types();

	if ( 0 === $post_id ) {
		return is_singular() && ! in_array( get_post_type( get_the_ID() ), $post_types, true );
	}

	return et_builder_is_post_type_custom( get_post_type( $post_id ) );
}

/**
 * Check whether the current request is for a custom post type archive.
 *
 * @since 4.0.5
 *
 * @return boolean
 */
function et_builder_is_custom_post_type_archive() {
	// Use get_the_ID() explicitly so we decide based on the first post of an archive page.
	return is_archive() && et_builder_post_is_of_custom_post_type( get_the_ID() );
}

/**
 * Get an array of post types the Divi Builder is enabled on.
 *
 * @since 3.10
 *
 * @return string[]
 */
function et_builder_get_enabled_builder_post_types() {
	$default = array_merge(
		et_builder_get_default_post_types(),
		et_builder_get_third_party_post_types()
	);

	/**
	 * Filter the array of enabled post type options.
	 * Allows Divi/Extra/DBP to only supply their option value in order to reduce code duplication.
	 *
	 * Schema:
	 *     array(
	 *         'post_type_name' => <'on' or 'off'>,
	 *         // ...
	 *     )
	 *
	 * @since 3.10
	 *
	 * @param string[] $options
	 *
	 * @return string[]
	 */
	$options = apply_filters( 'et_builder_enabled_builder_post_type_options', array() );

	// Ensure $options value type is array.
	$options = is_array( $options ) ? $options : array();

	foreach ( $default as $post_type ) {
		if ( ! isset( $options[ $post_type ] ) ) {
			$options[ $post_type ] = 'on';
		}
	}

	$filtered = array();

	foreach ( $options as $post_type => $state ) {
		if ( 'on' === $state && array_key_exists( $post_type, et_get_registered_post_type_options() ) && ! in_array( $post_type, et_builder_get_blocklisted_post_types(), true ) ) {
			$filtered[] = $post_type;
		}
	}

	return $filtered;
}

/**
 * Return an array of post types which have the builder enabled.
 *
 * @return mixed|void
 */
function et_builder_get_builder_post_types() {
	/**
	 * Array of post types which have the builder enabled.
	 *
	 * @since 3.10
	 *
	 * @param string[]
	 */
	return apply_filters( 'et_builder_post_types', et_builder_get_enabled_builder_post_types() );
}

/**
 * Return an array of post types which have the frontend builder enabled.
 *
 * @return mixed|void
 */
function et_builder_get_fb_post_types() {
	/**
	 * Array of post types which have the frontend builder enabled.
	 *
	 * @since 3.10
	 *
	 * @param string[]
	 */
	return apply_filters( 'et_fb_post_types', et_builder_get_enabled_builder_post_types() );
}

/**
 * Check whether the specified post can have the builder enabled.
 *
 * @since 3.10
 *
 * @param integer $post_id the post id to be checked.
 *
 * @return boolean
 */
function et_builder_enabled_for_post( $post_id ) {
	if ( et_pb_is_pagebuilder_used( $post_id ) ) {
		return true;
	}

	return et_builder_enabled_for_post_type( get_post_type( $post_id ) );
}

/**
 * Check whether the specified post type can have the builder enabled.
 *
 * @since 3.10
 *
 * @param string $post_type the registered post type to be checked.
 *
 * @return boolean
 */
function et_builder_enabled_for_post_type( $post_type ) {
	return in_array( $post_type, et_builder_get_builder_post_types(), true );
}

/**
 * Check whether the specified post can have the FB enabled.
 *
 * @since 3.10
 *
 * @param integer $post_id the post id to be checked for the FB enable.
 *
 * @return boolean
 */
function et_builder_fb_enabled_for_post( $post_id ) {
	$post_type            = get_post_type( $post_id );
	$enabled              = false;
	$pto                  = get_post_type_object( $post_type );
	$is_default_post_type = in_array( $post_type, et_builder_get_default_post_types(), true );
	$is_public_post_type  = et_builder_is_post_type_public( $post_type );

	if ( $pto && ( $is_default_post_type || $is_public_post_type ) ) {
		$enabled = et_builder_enabled_for_post( $post_id );
	}

	/**
	 * Filter whether the FB is enabled for a given post.
	 *
	 * @since 3.10
	 *
	 * @param boolean $enabled
	 * @param integer $post_id
	 */
	$enabled = apply_filters( 'et_builder_fb_enabled_for_post', $enabled, $post_id );

	return $enabled;
}

/**
 * Check whether the specified post type is public.
 *
 * @since 3.10
 *
 * @param string $post_type the post type to be checked.
 *
 * @return boolean
 */
function et_builder_is_post_type_public( $post_type ) {
	$pto = get_post_type_object( $post_type );

	// Note: the page post type is not publicly_queryable but we should treat it as such.
	return ( $pto && ( $pto->publicly_queryable || 'page' === $pto->name ) );
}

/**
 * Check whether the styles for the current request should be wrapped.
 * We wrap styles on non-native custom post types and custom post archives.
 *
 * @since 4.10.6
 *
 * @return boolean
 */
function et_builder_should_wrap_styles() {
	static $should_wrap = null;

	if ( null === $should_wrap ) {
		$post_id = get_the_ID();

		// Warp on custom post type archives and on non-native custom post types when the builder is used.
		$should_wrap = et_builder_is_custom_post_type_archive() || ( et_builder_post_is_of_custom_post_type( $post_id ) && et_pb_is_pagebuilder_used( $post_id ) );
	}

	return $should_wrap;
}

/**
 * Determine whether post is of post_type layout or not.
 *
 * @param integer $post_id the post id to be checked.
 *
 * @return bool
 */
function et_is_extra_library_layout( $post_id ) {
	return 'layout' === get_post_type( $post_id );
}

/**
 * Check whether the specified capability allowed for the user.
 *
 * @param array|string $capabilities   capabilities names.
 * @param string       $role         - The user role. If empty the role of the current user is used.
 *
 * @return bool
 */
function et_pb_is_allowed( $capabilities, $role = '' ) {
	$saved_capabilities = et_pb_get_role_settings();
	$test_current_user  = '' === $role;
	$role               = $test_current_user ? et_pb_get_current_user_role() : $role;

	if ( ! $role ) {
		return false;
	}

	// Disable certain capabilities for non-allowlisted roles by default.
	$dangerous       = array( 'theme_builder', 'read_dynamic_content_custom_fields' );
	$roles_allowlist = array( 'administrator', 'et_support_elevated', 'et_support' );

	foreach ( (array) $capabilities as $capability ) {
		$is_dangerous         = in_array( $capability, $dangerous, true );
		$role_not_allowlisted = ! in_array( $role, $roles_allowlist, true );

		if ( $test_current_user && $is_dangerous && is_multisite() && is_super_admin() ) {
			// Super admins always have access to dangerous capabilities and that cannot be
			// changed in the role editor.
			return true;
		}

		if ( ! empty( $saved_capabilities[ $role ][ $capability ] ) ) {
			return 'on' === $saved_capabilities[ $role ][ $capability ];
		}

		if ( $is_dangerous && $role_not_allowlisted ) {
			// Allowlisted roles have access to dangerous capabilities by default,
			// but that can be changed in the role editor.
			return false;
		}
	}

	return true;
}

/**
 * Performs a check against ET capabilities before passing on to {@see et_core_security_check()}.
 *
 * @since 4.0
 *
 * @param string $et_capability ET Capability name.
 * @param string $wp_capability WP Capability name.
 * @param string $nonce_action Name of the nonce action to check.
 * @param string $nonce_key The key to use to lookup nonce value.
 * @param string $nonce_location Where the nonce is stored (_POST|_GET|_REQUEST).
 * @param bool   $die  Whether or not to `die()` on failure.
 *
 * @return bool
 */
function et_builder_security_check( $et_capability, $wp_capability = 'manage_options', $nonce_action = '', $nonce_key = '', $nonce_location = '_POST', $die = true ) {
	if ( ! et_pb_is_allowed( $et_capability ) ) {
		if ( $die ) {
			et_core_die();
		}
		return false;
	}

	return et_core_security_check( $wp_capability, $nonce_action, $nonce_key, $nonce_location, $die );
}

/**
 * Gets the array of role settings
 *
 * @return array
 */
function et_pb_get_role_settings() {
	global $et_pb_role_settings;

	// if we don't have saved global variable, then get the value from WPDB.
	$et_pb_role_settings = isset( $et_pb_role_settings ) ? $et_pb_role_settings : get_option( 'et_pb_role_settings', array() );

	return $et_pb_role_settings;
}

/**
 * Determines the current user role.
 *
 * @return string
 */
function et_pb_get_current_user_role() {
	$current_user = wp_get_current_user();
	$user_roles   = $current_user->roles;

	// retrieve the role from array if exists or determine it using custom mechanism
	// $user_roles array may start not from 0 index. Use reset() to retrieve the first value from array regardless its index.
	$role = ! empty( $user_roles ) ? reset( $user_roles ) : et_pb_determine_current_user_role();

	return $role;
}

/**
 * Generate the list of all roles ( with editing permissions ) registered in current WP.
 *
 * @return array
 */
function et_pb_get_all_roles_list() {
	// get all roles registered in current WP.
	if ( ! function_exists( 'get_editable_roles' ) ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';
	}

	$all_roles           = get_editable_roles();
	$builder_roles_array = array();

	if ( ! empty( $all_roles ) ) {
		foreach ( $all_roles as $role => $role_data ) {
			// add roles with edit_posts capability into $builder_roles_array (but not Support).
			if (
				! empty( $role_data['capabilities']['edit_posts'] )
				&&
				1 === (int) $role_data['capabilities']['edit_posts']
				&&
				! in_array( $role_data['name'], array( 'ET Support', 'ET Support - Elevated' ), true )
			) {
				$builder_roles_array[ $role ] = $role_data['name'];
			}
		}
	}

	// fill the builder roles array with default roles if it's empty.
	if ( empty( $builder_roles_array ) ) {
		$builder_roles_array = array(
			'administrator' => esc_html__( 'Administrator', 'et_builder' ),
			'editor'        => esc_html__( 'Editor', 'et_builder' ),
			'author'        => esc_html__( 'Author', 'et_builder' ),
			'contributor'   => esc_html__( 'Contributor', 'et_builder' ),
		);
	}

	return $builder_roles_array;
}

/**
 * Determine the current user role by checking every single registered role via current_user_can().
 *
 * @return string
 */
function et_pb_determine_current_user_role() {
	$all_roles = et_pb_get_all_roles_list();

	// go through all the registered roles and return the one current user have.
	foreach ( $all_roles as $role => $role_data ) {
		if ( current_user_can( $role ) ) {
			return $role;
		}
	}
}

/**
 * Retrieve similar post types for the given post type.
 *
 * @param string $post_type The post type for which retrieve similar post types.
 *
 * @return array
 */
function et_pb_show_all_layouts_built_for_post_type( $post_type ) {
	$similar_post_types = array(
		'post',
		'page',
		'project',
	);

	if ( in_array( $post_type, $similar_post_types, true ) ) {
		return $similar_post_types;
	}

	return $post_type;
}
add_filter( 'et_pb_show_all_layouts_built_for_post_type', 'et_pb_show_all_layouts_built_for_post_type' );

/**
 * AJAX Callback :: Load layouts.
 */
function et_pb_show_all_layouts() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	printf(
		'
		<label for="et_pb_load_layout_replace">
			<input name="et_pb_load_layout_replace" type="checkbox" id="et_pb_load_layout_replace" %2$s/>
			<span>%1$s</span>
		</label>',
		esc_html__( 'Replace the existing content with loaded layout', 'et_builder' ),
		checked( get_theme_mod( 'et_pb_replace_content', 'on' ), 'on', false )
	);

	$post_type    = ! empty( $_POST['et_layouts_built_for_post_type'] ) ? sanitize_text_field( $_POST['et_layouts_built_for_post_type'] ) : 'post';
	$layouts_type = ! empty( $_POST['et_load_layouts_type'] ) ? sanitize_text_field( $_POST['et_load_layouts_type'] ) : 'predefined';

	$predefined_operator = 'predefined' === $layouts_type ? 'EXISTS' : 'NOT EXISTS';

	$post_type = apply_filters( 'et_pb_show_all_layouts_built_for_post_type', $post_type, $layouts_type );

	$query_args = array(
		'meta_query'       => array(
			'relation' => 'AND',
			array(
				'key'     => '_et_pb_predefined_layout',
				'value'   => 'on',
				'compare' => $predefined_operator,
			),
			array(
				'key'     => '_et_pb_built_for_post_type',
				'value'   => $post_type,
				'compare' => 'IN',
			),
			array(
				'key'     => '_et_pb_layout_applicability',
				'value'   => 'product_tour',
				'compare' => 'NOT EXISTS',
			),
		),
		'tax_query'        => array(
			array(
				'taxonomy' => 'layout_type',
				'field'    => 'slug',
				'terms'    => array( 'section', 'row', 'module', 'fullwidth_section', 'specialty_section', 'fullwidth_module' ),
				'operator' => 'NOT IN',
			),
		),
		'post_type'        => ET_BUILDER_LAYOUT_POST_TYPE,
		'posts_per_page'   => '-1',
		'suppress_filters' => 'predefined' === $layouts_type,
	);

	$query = new WP_Query( $query_args );

	if ( $query->have_posts() ) :

		echo '<ul class="et-pb-all-modules et-pb-load-layouts">';

		while ( $query->have_posts() ) :
			$query->the_post();

			$button_html = 'predefined' !== $layouts_type ?
				sprintf(
					'<a href="#" class="button et_pb_layout_button_delete">%1$s</a>',
					esc_html__( 'Delete', 'et_builder' )
				)
				: '';

			printf(
				'<li class="et_pb_text" data-layout_id="%2$s">%1$s<span class="et_pb_layout_buttons"><a href="#" class="button-primary et_pb_layout_button_load">%3$s</a>%4$s</span></li>',
				esc_html( get_the_title() ),
				esc_attr( get_the_ID() ),
				esc_html__( 'Load', 'et_builder' ),
				et_core_esc_previously( $button_html )
			);

		endwhile;

		echo '</ul>';
	endif;

	wp_reset_postdata();

	die();
}
add_action( 'wp_ajax_et_pb_show_all_layouts', 'et_pb_show_all_layouts' );

/**
 * AJAX Callback :: Retrieves saved builder layouts.
 */
function et_pb_get_saved_templates() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$layout_type     = ! empty( $_POST['et_layout_type'] ) ? sanitize_text_field( $_POST['et_layout_type'] ) : 'layout';
	$module_width    = ! empty( $_POST['et_module_width'] ) && 'module' === $layout_type ? sanitize_text_field( $_POST['et_module_width'] ) : '';
	$is_global       = ! empty( $_POST['et_is_global'] ) ? sanitize_text_field( $_POST['et_is_global'] ) : 'false';
	$specialty_query = ! empty( $_POST['et_specialty_columns'] ) && 'row' === $layout_type ? sanitize_text_field( $_POST['et_specialty_columns'] ) : '0';
	$post_type       = ! empty( $_POST['et_post_type'] ) ? sanitize_text_field( $_POST['et_post_type'] ) : 'post';

	$templates_data = et_pb_retrieve_templates( $layout_type, $module_width, $is_global, $specialty_query, $post_type );

	if ( empty( $templates_data ) ) {
		$templates_data = array( 'error' => esc_html__( 'You have not saved any items to your Divi Library yet. Once an item has been saved to your library, it will appear here for easy use.', 'et_builder' ) );
	}

	$json_templates = wp_json_encode( $templates_data );

	die( et_core_esc_previously( $json_templates ) );
}
add_action( 'wp_ajax_et_pb_get_saved_templates', 'et_pb_get_saved_templates' );

/**
 * Retrieves saved builder layouts.
 *
 * @since 2.0
 *
 * @param string $layout_type     Accepts 'section', 'row', 'module', 'fullwidth_section',
 *                                'specialty_section', 'fullwidth_module'.
 * @param string $module_width    Accepts 'regular', 'fullwidth'.
 * @param string $is_global       Filter layouts based on their scope. Accepts 'global' to include
 *                                only global layouts, 'false' to include only non-global layouts,
 *                                or 'all' to include both global and non-global layouts.
 * @param string $specialty_query Limit results to layouts of type 'row' that can be put inside
 *                                specialty sections. Accepts '3' to include only 3-column rows,
 *                                '2' for 2-column rows, or '0' to disable the specialty query. Default '0'.
 * @param string $post_type       Limit results to layouts built for this post type.
 * @param string $deprecated      Deprecated.
 * @param array  $boundaries      {
 *
 *     Return a subset of the total results.
 *
 *     @type int $offset Start from this point in the results. Default `0`.
 *     @type int $limit  Maximum number of results to return. Default `-1`.
 * }
 *
 * @return array[] $layouts {
 *
 *     @type mixed[] {
 *
 *         Layout Data
 *
 *         @type int      $ID               The layout's post id.
 *         @type string   $title            The layout's title/name.
 *         @type string   $shortcode        The layout's shortcode content.
 *         @type string   $is_global        The layout's scope. Accepts 'global', 'non_global'.
 *         @type string   $layout_type      The layout's type. See {@see self::$layout_type} param for accepted values.
 *         @type string   $applicability    The layout's applicability.
 *         @type string   $layouts_type     Deprecated. Will always be 'library'.
 *         @type string   $module_type      For layouts of type 'module', the module type/slug (eg. et_pb_blog).
 *         @type string[] $categories       This layout's assigned categories (slugs).
 *         @type string   $row_layout       For layout's of type 'row', the row layout type (eg. 4_4).
 *         @type mixed[]  $unsynced_options For global layouts, the layout's unsynced settings.
 *     }
 *     ...
 * }
 */
function et_pb_retrieve_templates( $layout_type = 'layout', $module_width = '', $is_global = 'false', $specialty_query = '0', $post_type = 'post', $deprecated = '', $boundaries = array() ) {
	$templates_data         = array();
	$suppress_filters       = false;
	$extra_layout_post_type = 'layout';
	$module_icons           = ET_Builder_Element::get_module_icons();
	$utils                  = ET_Core_Data_Utils::instance();
	$similar_post_types     = array_keys( ET_Builder_Settings::get_registered_post_type_options() );

	// All default and 3rd party post types considered similar and share the same library items, so retrieve all items for any post type from the list.
	$post_type = in_array( $post_type, $similar_post_types, true ) ? $similar_post_types : $post_type;

	// need specific query for the layouts.
	if ( 'layout' === $layout_type ) {

		if ( 'all' === $post_type ) {
			$meta_query = array(
				'relation' => 'AND',
				array(
					'key'     => '_et_pb_built_for_post_type',
					'value'   => $extra_layout_post_type,
					'compare' => 'NOT IN',
				),
			);
		} else {
			$meta_query = array(
				'relation' => 'AND',
				array(
					'key'     => '_et_pb_built_for_post_type',
					'value'   => $post_type,
					'compare' => 'IN',
				),
			);
		}

		$tax_query        = array(
			array(
				'taxonomy' => 'layout_type',
				'field'    => 'slug',
				'terms'    => array( 'section', 'row', 'module', 'fullwidth_section', 'specialty_section', 'fullwidth_module' ),
				'operator' => 'NOT IN',
			),
		);
		$suppress_filters = 'predefined' === $layout_type;
	} else {
		$additional_condition = '' !== $module_width ?
			array(
				'taxonomy' => 'module_width',
				'field'    => 'slug',
				'terms'    => $module_width,
			) : '';

		$meta_query = array();

		if ( '0' !== $specialty_query ) {
			$columns_val  = '3' === $specialty_query ? array( '4_4', '1_2,1_2', '1_3,1_3,1_3' ) : array( '4_4', '1_2,1_2' );
			$meta_query[] = array(
				'key'     => '_et_pb_row_layout',
				'value'   => $columns_val,
				'compare' => 'IN',
			);
		}

		$post_type    = apply_filters( 'et_pb_show_all_layouts_built_for_post_type', $post_type, $layout_type );
		$meta_query[] = array(
			'key'     => '_et_pb_built_for_post_type',
			'value'   => $post_type,
			'compare' => 'IN',
		);

		$tax_query = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'layout_type',
				'field'    => 'slug',
				'terms'    => $layout_type,
			),
			$additional_condition,
		);

		if ( 'all' !== $is_global ) {
			$global_operator = 'global' === $is_global ? 'IN' : 'NOT IN';
			$tax_query[]     = array(
				'taxonomy' => 'scope',
				'field'    => 'slug',
				'terms'    => array( 'global' ),
				'operator' => $global_operator,
			);
		}
	}

	$start_from = 0;
	$limit_to   = '-1';

	if ( ! empty( $boundaries ) ) {
		$start_from = $boundaries[0];
		$limit_to   = $boundaries[1];
	}

	/**
	 * Filter suppress_filters argument.
	 *
	 * @since 4.4.5
	 *
	 * @param boolean $suppress_filters
	 */
	$suppress_filters = wp_validate_boolean( apply_filters( 'et_pb_show_all_layouts_suppress_filters', $suppress_filters ) );

	$query = new WP_Query(
		array(
			'tax_query'        => $tax_query,
			'post_type'        => ET_BUILDER_LAYOUT_POST_TYPE,
			'posts_per_page'   => $limit_to,
			'meta_query'       => $meta_query,
			'offset'           => $start_from,
			'suppress_filters' => $suppress_filters,
		)
	);

	if ( ! empty( $query->posts ) ) {
		// Call the_post() to properly configure post data. Make sure to call the_post() and
		// wp_reset_postdata() only if the posts result exist to avoid unexpected issues.
		$query->the_post();

		wp_reset_postdata();

		foreach ( $query->posts as $single_post ) {

			if ( 'module' === $layout_type ) {
				$module_type = get_post_meta( $single_post->ID, '_et_pb_module_type', true );
			} else {
				$module_type = '';
			}

			// add only modules allowed for current user.
			if ( '' === $module_type || et_pb_is_allowed( $module_type ) ) {
				$categories                = wp_get_post_terms( $single_post->ID, 'layout_category' );
				$scope                     = wp_get_post_terms( $single_post->ID, 'scope' );
				$global_scope              = isset( $scope[0] ) ? $scope[0]->slug : 'non_global';
				$categories_processed      = array();
				$row_layout                = '';
				$this_layout_type          = '';
				$this_layout_applicability = '';

				if ( ! empty( $categories ) ) {
					foreach ( $categories as $category_data ) {
						$categories_processed[] = esc_html( $category_data->slug );
					}
				}

				if ( 'row' === $layout_type ) {
					$row_layout = get_post_meta( $single_post->ID, '_et_pb_row_layout', true );
				}

				if ( 'layout' === $layout_type ) {
					$this_layout_type          = 'on' === get_post_meta( $single_post->ID, '_et_pb_predefined_layout', true ) ? 'predefined' : 'library';
					$this_layout_applicability = get_post_meta( $single_post->ID, '_et_pb_layout_applicability', true );
				}

				// get unsynced global options for module.
				if ( 'module' === $layout_type && 'false' !== $is_global ) {
					$unsynced_options = get_post_meta( $single_post->ID, '_et_pb_excluded_global_options' );
				}

				$templates_datum = array(
					'ID'               => (int) $single_post->ID,
					'title'            => esc_html( $single_post->post_title ),
					'shortcode'        => et_core_intentionally_unescaped( $single_post->post_content, 'html' ),
					'is_global'        => esc_html( $global_scope ),
					'layout_type'      => esc_html( $layout_type ),
					'applicability'    => esc_html( $this_layout_applicability ),
					'layouts_type'     => esc_html( $this_layout_type ),
					'module_type'      => esc_html( $module_type ),
					'categories'       => et_core_esc_previously( $categories_processed ),
					'row_layout'       => esc_html( $row_layout ),
					'unsynced_options' => ! empty( $unsynced_options ) ? $utils->esc_array( json_decode( $unsynced_options[0], true ), 'sanitize_text_field' ) : array(),
				);

				if ( $module_type ) {

					// Append icon if there's any.
					$template_icon = $utils->array_get( $module_icons, "{$module_type}.icon", false );
					if ( $template_icon ) {
						$templates_datum['icon'] = $template_icon;
					}

					// Append svg icon if there's any.
					$template_icon_svg = $utils->array_get( $module_icons, "{$module_type}.icon_svg", false );
					if ( $template_icon_svg ) {
						$templates_datum['icon_svg'] = $template_icon_svg;
					}
				}

				$templates_data[] = $templates_datum;
			}
		}
	}

	return $templates_data;
}

/**
 * AJAX Callback :: Add template meta.
 */
function et_pb_add_template_meta() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	$post_id = ! empty( $_POST['et_post_id'] ) ? sanitize_text_field( $_POST['et_post_id'] ) : '';

	if ( empty( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		die( -1 );
	}

	$value        = ! empty( $_POST['et_meta_value'] ) ? sanitize_text_field( $_POST['et_meta_value'] ) : '';
	$custom_field = ! empty( $_POST['et_custom_field'] ) ? sanitize_text_field( $_POST['et_custom_field'] ) : '';

	$allowlisted_meta_keys = array(
		'_et_pb_row_layout',
		'_et_pb_module_type',
	);

	if ( in_array( $custom_field, $allowlisted_meta_keys, true ) ) {
		update_post_meta( $post_id, $custom_field, $value );
	}
}
add_action( 'wp_ajax_et_pb_add_template_meta', 'et_pb_add_template_meta' );

if ( ! function_exists( 'et_pb_add_new_layout' ) ) {
	/**
	 * AJAX Callback :: Save layout to database.
	 */
	function et_pb_add_new_layout() {
		if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
			die( -1 );
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			die( -1 );
		}

		// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- et_layout_options is json object, and this function sanitize object values at the time of using it.
		$fields_data = isset( $_POST['et_layout_options'] ) ? trim( $_POST['et_layout_options'] ) : '';

		if ( empty( $fields_data ) ) {
			die();
		}

		$fields_data_json     = str_replace( '\\', '', $fields_data );
		$fields_data_array    = json_decode( $fields_data_json, true );
		$processed_data_array = array();

		// prepare array with fields data in convenient format.
		if ( ! empty( $fields_data_array ) ) {
			foreach ( $fields_data_array as $index => $field_data ) {
				$processed_data_array[ $field_data['field_id'] ] = $field_data['field_val'];
			}
		}

		$processed_data_array = apply_filters( 'et_pb_new_layout_data_from_form', $processed_data_array, $fields_data_array );

		if ( empty( $processed_data_array ) ) {
			die();
		}

		$layout_location  = et_()->array_get_sanitized( $processed_data_array, 'et_pb_template_cloud', 'local' );
		$layout_type      = et_()->array_get_sanitized( $processed_data_array, 'new_template_type', 'layout' );
		$layout_is_global = 'global' === et_()->array_get( $processed_data_array, 'et_pb_template_global', 'not_global' );
		if ( 'layout' === $layout_type ) {
			// Layouts of type 'layout' are not allowed to be global.
			$layout_is_global = false;
		}

		$args = array(
			'layout_type'          => $layout_type,
			'layout_selected_cats' => ! empty( $processed_data_array['selected_cats'] ) ? sanitize_text_field( $processed_data_array['selected_cats'] ) : '',
			'layout_selected_tags' => ! empty( $processed_data_array['selected_tags'] ) ? sanitize_text_field( $processed_data_array['selected_tags'] ) : '',
			'built_for_post_type'  => ! empty( $processed_data_array['et_builder_layout_built_for_post_type'] ) ? sanitize_text_field( $processed_data_array['et_builder_layout_built_for_post_type'] ) : 'page',
			'layout_new_cat'       => ! empty( $processed_data_array['et_pb_new_cat_name'] ) ? sanitize_text_field( $processed_data_array['et_pb_new_cat_name'] ) : '',
			'layout_new_tag'       => ! empty( $processed_data_array['et_pb_new_tag_name'] ) ? sanitize_text_field( $processed_data_array['et_pb_new_tag_name'] ) : '',
			'columns_layout'       => ! empty( $processed_data_array['et_columns_layout'] ) ? sanitize_text_field( $processed_data_array['et_columns_layout'] ) : '0',
			'module_type'          => ! empty( $processed_data_array['et_module_type'] ) ? sanitize_text_field( $processed_data_array['et_module_type'] ) : 'et_pb_unknown',
			'layout_scope'         => $layout_is_global ? 'global' : 'not_global',
			'layout_location'      => $layout_location,
			'module_width'         => 'regular',
			'layout_content'       => ! empty( $processed_data_array['template_shortcode'] ) ? $processed_data_array['template_shortcode'] : '',
			'layout_name'          => ! empty( $processed_data_array['et_pb_new_template_name'] ) ? sanitize_text_field( $processed_data_array['et_pb_new_template_name'] ) : '',
		);

		// construct the initial shortcode for new layout.
		switch ( $args['layout_type'] ) {
			case 'row':
				$args['layout_content'] = '[et_pb_row template_type="row"][/et_pb_row]';
				break;
			case 'section':
				$args['layout_content'] = '[et_pb_section template_type="section"][et_pb_row][/et_pb_row][/et_pb_section]';
				break;
			case 'module':
				$args['layout_content'] = '[et_pb_module_placeholder selected_tabs="all"]';
				break;
			case 'fullwidth_module':
				$args['layout_content'] = '[et_pb_fullwidth_module_placeholder selected_tabs="all"]';
				$args['module_width']   = 'fullwidth';
				$args['layout_type']    = 'module';
				break;
			case 'fullwidth_section':
				$args['layout_content'] = '[et_pb_section template_type="section" fullwidth="on"][/et_pb_section]';
				$args['layout_type']    = 'section';
				break;
			case 'specialty_section':
				$args['layout_content'] = '[et_pb_section template_type="section" specialty="on" skip_module="true" specialty_placeholder="true"][/et_pb_section]';
				$args['layout_type']    = 'section';
				break;
		}

		$new_layout_meta = et_pb_submit_layout( apply_filters( 'et_pb_new_layout_args', $args ) );
		die( et_core_esc_previously( $new_layout_meta ) );
	}
}
add_action( 'wp_ajax_et_pb_add_new_layout', 'et_pb_add_new_layout' );

if ( ! function_exists( 'et_pb_submit_layout' ) ) :
	/**
	 * Handles saving layouts to the database for the builder. Essentially just a wrapper for
	 * {@see et_pb_create_layout()} that processes the data from the builder before passing it on.
	 *
	 * @since 1.0
	 *
	 * @param array $args {
	 *     Layout Data.
	 *
	 *     @type string $layout_type          Accepts 'layout', 'section', 'row', 'module'.
	 *     @type string $layout_selected_cats Categories to which the layout should be added. This should
	 *                                        be one or more IDs separated by pipe symbols. Example: '1|2|3'.
	 *     @type string $built_for_post_type  The post type for which the layout was built.
	 *     @type string $layout_new_cat       Name of a new category to which the layout should be added.
	 *     @type string $columns_layout       When 'layout_type' is 'row', the row's columns structure. Example: '1_4'.
	 *     @type string $module_type          When 'layout_type' is 'module', the module type. Example: 'et_pb_blurb'.
	 *     @type string $layout_scope         Optional. The layout's scope. Accepts: 'global', 'not_global'.
	 *     @type string $module_width         When 'layout_type' is 'module', the module's width. Accepts: 'regular', 'fullwidth'.
	 *     @type string $layout_content       The layout's content (unprocessed shortcodes).
	 *     @type string $layout_name          The layout's name.
	 * }
	 *
	 * @return string $layout_data The 'post_id' and 'edit_link' for the saved layout (JSON encoded).
	 */
	function et_pb_submit_layout( $args ) {
		/**
		 * Filters the layout data passed to {@see et_pb_submit_layout()}.
		 *
		 * @since 3.0.99
		 *
		 * @param string[] $args See {@see et_pb_submit_layout()} for array structure definition.
		 */
		$args = apply_filters( 'et_pb_submit_layout_args', $args );

		if ( empty( $args ) ) {
			return '';
		}

		$layout_cats_processed = array();
		$layout_tags_processed = array();

		if ( '' !== $args['layout_selected_cats'] ) {
			$layout_cats_array     = explode( ',', $args['layout_selected_cats'] );
			$layout_cats_processed = array_map( 'intval', $layout_cats_array );
		}

		if ( '' !== $args['layout_selected_tags'] ) {
			$layout_tags_array     = explode( ',', $args['layout_selected_tags'] );
			$layout_tags_processed = array_map( 'intval', $layout_tags_array );
		}

		$meta = array();

		if ( 'row' === $args['layout_type'] && '0' !== $args['columns_layout'] ) {
			$meta = array_merge( $meta, array( '_et_pb_row_layout' => $args['columns_layout'] ) );
		}

		if ( 'module' === $args['layout_type'] ) {
			$meta = array_merge( $meta, array( '_et_pb_module_type' => $args['module_type'] ) );

			// save unsynced options for global modules. Always empty for new modules.
			if ( 'global' === $args['layout_scope'] ) {
				$meta = array_merge( $meta, array( '_et_pb_excluded_global_options' => wp_json_encode( array() ) ) );
			}
		}

		// et_layouts_built_for_post_type.
		$meta = array_merge( $meta, array( '_et_pb_built_for_post_type' => $args['built_for_post_type'] ) );

		$tax_input = array(
			'scope'           => $args['layout_scope'],
			'layout_type'     => $args['layout_type'],
			'module_width'    => $args['module_width'],
			'layout_category' => $layout_cats_processed,
			'layout_tag'      => $layout_tags_processed,
			'layout_location' => et_()->array_get_sanitized( $args, 'layout_location', 'local' ),
		);

		$new_layout_id            = et_pb_create_layout( $args['layout_name'], $args['layout_content'], $meta, $tax_input, $args['layout_new_cat'], $args['layout_new_tag'] );
		$new_post_data['post_id'] = (int) $new_layout_id;

		$new_post_data['edit_link'] = esc_url_raw( get_edit_post_link( $new_layout_id ) );
		$json_post_data             = wp_json_encode( $new_post_data );

		return $json_post_data;
	}
endif;

if ( ! function_exists( 'et_pb_create_layout' ) ) :
	/**
	 * Create new layout.
	 *
	 * @param string $name The layout name.
	 * @param string $content The layout content.
	 * @param array  $meta The layout meta.
	 * @param array  $tax_input  Array of taxonomy terms keyed by their taxonomy name.
	 * @param string $new_category The layout category.
	 */
	function et_pb_create_layout( $name, $content, $meta = array(), $tax_input = array(), $new_category = '', $new_tag = '', $post_status = 'publish' ) {
		$layout = array(
			'post_title'   => sanitize_text_field( $name ),
			'post_content' => $content,
			'post_status'  => $post_status,
			'post_type'    => ET_BUILDER_LAYOUT_POST_TYPE,
		);

		$layout_id = wp_insert_post( $layout );

		if ( ! empty( $meta ) ) {
			foreach ( $meta as $meta_key => $meta_value ) {
				add_post_meta( $layout_id, $meta_key, sanitize_text_field( $meta_value ) );
			}
		}

		if ( '' !== $new_category ) {
			// Multiple categories could be provided.
			$category_names = explode( ',', $new_category );

			foreach ( $category_names as $term_name ) {
				$new_term = wp_insert_term( $term_name, 'layout_category' );

				if ( ! is_wp_error( $new_term ) && isset( $new_term['term_id'] ) ) {
					$tax_input['layout_category'][] = (int) $new_term['term_id'];
				}
			}
		}

		if ( '' !== $new_tag ) {
			// Multiple tags could be provided.
			$tag_names = explode( ',', $new_tag );

			foreach ( $tag_names as $term_name ) {
				$new_term = wp_insert_term( $term_name, 'layout_tag' );

				if ( ! is_wp_error( $new_term ) && isset( $new_term['term_id'] ) ) {
					$tax_input['layout_tag'][] = (int) $new_term['term_id'];
				}
			}
		}

		if ( ! empty( $tax_input ) ) {
			foreach ( $tax_input as $taxonomy => $terms ) {
				wp_set_post_terms( $layout_id, $terms, $taxonomy );
			}

			return $layout_id;
		}
	}
endif;

/**
 * AJAX Callback :: Save layout to the database for the builder.
 */
function et_pb_save_layout() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	if ( empty( $_POST['et_layout_name'] ) ) {
		die();
	}

	$args = array(
		'layout_type'          => isset( $_POST['et_layout_type'] ) ? sanitize_text_field( $_POST['et_layout_type'] ) : 'layout',
		'layout_selected_cats' => isset( $_POST['et_layout_cats'] ) ? sanitize_text_field( $_POST['et_layout_cats'] ) : '',
		'built_for_post_type'  => isset( $_POST['et_post_type'] ) ? sanitize_text_field( $_POST['et_post_type'] ) : 'page',
		'layout_new_cat'       => isset( $_POST['et_layout_new_cat'] ) ? sanitize_text_field( $_POST['et_layout_new_cat'] ) : '',
		'layout_new_tag'       => isset( $_POST['et_layout_new_tag'] ) ? sanitize_text_field( $_POST['et_layout_new_tag'] ) : '',
		'columns_layout'       => isset( $_POST['et_columns_layout'] ) ? sanitize_text_field( $_POST['et_columns_layout'] ) : '0',
		'module_type'          => isset( $_POST['et_module_type'] ) ? sanitize_text_field( $_POST['et_module_type'] ) : 'et_pb_unknown',
		'layout_scope'         => isset( $_POST['et_layout_scope'] ) ? sanitize_text_field( $_POST['et_layout_scope'] ) : 'not_global',
		'module_width'         => isset( $_POST['et_module_width'] ) ? sanitize_text_field( $_POST['et_module_width'] ) : 'regular',
		'layout_content'       => isset( $_POST['et_layout_content'] ) ? $_POST['et_layout_content'] : '', // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- wp_insert_post function does sanitization.
		'layout_name'          => isset( $_POST['et_layout_name'] ) ? sanitize_text_field( $_POST['et_layout_name'] ) : '',
	);

	$new_layout_meta = et_pb_submit_layout( $args );
	die( et_core_esc_previously( $new_layout_meta ) );
}
add_action( 'wp_ajax_et_pb_save_layout', 'et_pb_save_layout' );

/**
 * AJAX Callback :: Get layouts.
 */
function et_pb_get_global_module() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$global_shortcode = array();

	$utils = ET_Core_Data_Utils::instance();

	$post_id = isset( $_POST['et_global_id'] ) ? (int) $_POST['et_global_id'] : '';

	if ( empty( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		die( -1 );
	}

	$global_autop = isset( $_POST['et_global_autop'] ) ? sanitize_text_field( $_POST['et_global_autop'] ) : 'apply';

	if ( ! empty( $post_id ) ) {
		$query = new WP_Query(
			array(
				'p'         => $post_id,
				'post_type' => ET_BUILDER_LAYOUT_POST_TYPE,
			)
		);

		if ( ! empty( $query->post ) ) {
			// Call the_post() to properly configure post data. Make sure to call the_post() and
			// wp_reset_postdata() only if the posts result exist to avoid unexpected issues.
			$query->the_post();

			wp_reset_postdata();

			if ( 'skip' === $global_autop ) {
				$post_content = $query->post->post_content;
			} else {
				$post_content = $query->post->post_content;
				// do prep.
				$post_content = et_pb_prep_code_module_for_wpautop( $post_content );

				// wpautop does its "thing".
				$post_content = wpautop( $post_content );

				// undo prep.
				$post_content = et_pb_unprep_code_module_for_wpautop( $post_content );
			}

			$global_shortcode['shortcode'] = et_core_intentionally_unescaped( $post_content, 'html' );
			$excluded_global_options       = get_post_meta( $post_id, '_et_pb_excluded_global_options' );
			$selective_sync_status         = empty( $excluded_global_options ) ? '' : 'updated';

			$global_shortcode['sync_status'] = et_core_intentionally_unescaped( $selective_sync_status, 'fixed_string' );
			// excluded_global_options is an array with single value which is json string, so just `sanitize_text_field`, because `esc_html` converts quotes and breaks the json string.
			$global_shortcode['excluded_options'] = $utils->esc_array( $excluded_global_options, 'sanitize_text_field' );
		}
	}

	if ( empty( $global_shortcode ) ) {
		$global_shortcode['error'] = 'nothing';
	}

	$json_post_data = wp_json_encode( $global_shortcode );

	die( et_core_esc_previously( $json_post_data ) );
}
add_action( 'wp_ajax_et_pb_get_global_module', 'et_pb_get_global_module' );

/**
 * AJAX Callback :: Update layouts.
 */
function et_pb_update_layout() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_id     = isset( $_POST['et_template_post_id'] ) ? absint( $_POST['et_template_post_id'] ) : '';
	$new_content = isset( $_POST['et_layout_content'] ) ? $_POST['et_layout_content'] : ''; // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- wp_insert_post function does sanitization.
	$layout_type = isset( $_POST['et_layout_type'] ) ? sanitize_text_field( $_POST['et_layout_type'] ) : '';

	if ( empty( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		die( -1 );
	}

	$update = array(
		'ID'           => $post_id,
		'post_content' => $new_content,
	);

	$result = wp_update_post( $update );

	if ( ! $result || is_wp_error( $result ) ) {
		wp_send_json_error();
	}

	ET_Core_PageResource::remove_static_resources( 'all', 'all' );

	if ( 'module' === $layout_type && isset( $_POST['et_unsynced_options'] ) ) {
		$unsynced_options = stripslashes( sanitize_text_field( $_POST['et_unsynced_options'] ) );

		update_post_meta( $post_id, '_et_pb_excluded_global_options', $unsynced_options );
	}

	die();
}
add_action( 'wp_ajax_et_pb_update_layout', 'et_pb_update_layout' );

/**
 * AJAX Callback :: Get/load layout.
 */
function et_pb_load_layout() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$layout_id = ! empty( $_POST['et_layout_id'] ) ? (int) $_POST['et_layout_id'] : 0;

	if ( empty( $layout_id ) || ! current_user_can( 'edit_post', $layout_id ) ) {
		die( -1 );
	}

	// sanitize via allowlisting.
	$replace_content = isset( $_POST['et_replace_content'] ) && 'on' === $_POST['et_replace_content'] ? 'on' : 'off';

	set_theme_mod( 'et_pb_replace_content', $replace_content );

	$layout = get_post( $layout_id );

	if ( $layout ) {
		echo et_core_esc_previously( $layout->post_content );
	}

	die();
}
add_action( 'wp_ajax_et_pb_load_layout', 'et_pb_load_layout' );

/**
 * AJAX Callback :: Delete layout.
 */
function et_pb_delete_layout() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	$layout_id = ! empty( $_POST['et_layout_id'] ) ? (int) $_POST['et_layout_id'] : '';

	if ( empty( $layout_id ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'delete_post', $layout_id ) ) {
		die( -1 );
	}

	wp_delete_post( $layout_id );

	die();
}
add_action( 'wp_ajax_et_pb_delete_layout', 'et_pb_delete_layout' );

/**
 * Enables zlib compression if needed/supported.
 */
function et_builder_enable_zlib_compression() {
	// If compression is already enabled, do nothing.
	if ( 1 === intval( ini_get( 'zlib.output_compression' ) ) ) {
		return;
	}

	// We need to be sure no content has been pushed yet before enabling compression
	// to avoid decoding errors. To do so, we flush buffer and then check header_sent.
	while ( ob_get_level() ) {
		ob_end_flush();
	}

	if ( headers_sent() ) {
		// Something has been sent already, could be PHP notices or other plugin output.
		return;
	}

	// We use ob_gzhandler because less prone to errors with WP.
	if ( function_exists( 'ob_gzhandler' ) ) {
		// phpcs:ignore WordPress.PHP.IniSet -- Faster compression, requires less cpu/memory.
		ini_set( 'zlib.output_compression_level', 1 );

		ob_start( 'ob_gzhandler' );
	}
}

/**
 * AJAX Callback :: Get backbone templates.
 */
function et_pb_get_backbone_templates() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_type  = isset( $_POST['et_post_type'] ) ? sanitize_text_field( $_POST['et_post_type'] ) : '';
	$start_from = isset( $_POST['et_templates_start_from'] ) ? sanitize_text_field( $_POST['et_templates_start_from'] ) : 0;
	$amount     = ET_BUILDER_AJAX_TEMPLATES_AMOUNT;

	// Enable zlib compression.
	et_builder_enable_zlib_compression();
	// get the portion of templates.
	$result = wp_json_encode( ET_Builder_Element::output_templates( $post_type, $start_from, $amount ) );

	die( et_core_intentionally_unescaped( $result, 'html' ) );
}
add_action( 'wp_ajax_et_pb_get_backbone_templates', 'et_pb_get_backbone_templates' );

/**
 * Determine if a post is built by a certain builder.
 *
 * @param int    $post_id          The post_id to check.
 * @param string $built_by_builder The builder to check if the post is built by. Allowed values: fb, bb.
 *
 * @return bool
 */
function et_builder_is_builder_built( $post_id, $built_by_builder ) {
	$_post = get_post( $post_id );

	// a autosave could be passed as $post_id, and an autosave will not have post_meta and then et_pb_is_pagebuilder_used() will always return false.
	$parent_post = wp_is_post_autosave( $post_id ) ? get_post( $_post->post_parent ) : $_post;

	if ( ! $post_id || ! $_post || ! is_object( $_post ) || ! et_pb_is_pagebuilder_used( $parent_post->ID ) ) {
		return false;
	}

	// ensure this is an allowed builder post_type.
	if ( ! in_array( $parent_post->post_type, et_builder_get_builder_post_types(), true ) ) {
		return false;
	}

	// allowlist the builder slug.
	$built_by_builder = in_array( $built_by_builder, array( 'fb', 'bb' ), true ) ? $built_by_builder : '';

	// the built by slug prepended to the first section automatically, in this format: fb_built="1".
	$pattern = '/^\[et_pb_section ' . $built_by_builder . '_built="1"/s';

	return preg_match( $pattern, $_post->post_content );
}

/**
 * Determine et-editor-available-post-$post_id cookie is set or not.
 *
 * @return bool
 */
function et_is_builder_available_cookie_set() {
	static $builder_available = null;

	if ( null !== $builder_available ) {
		return $builder_available;
	}

	foreach ( (array) $_COOKIE as $cookie => $value ) {
		if ( 0 === strpos( $cookie, 'et-editor-available-post-' ) ) {
			$builder_available = true;

			return $builder_available;
		}
	}

	$builder_available = false;

	return $builder_available;
}

/**
 * Return heartbeat internal value.
 *
 * @return mixed|void
 */
function et_builder_heartbeat_interval() {
	return apply_filters( 'et_builder_heartbeat_interval', 30 );
}

/**
 * Ensure hearbeat interval set properly.
 *
 * @param array  $response The Heartbeat response.
 * @param string $screen_id The screen id.
 *
 * @return mixed
 */
function et_builder_ensure_heartbeat_interval( $response, $screen_id ) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return $response;
	}

	if ( ! isset( $response['heartbeat_interval'] ) ) {
		return $response;
	}

	if ( et_builder_heartbeat_interval() === $response['heartbeat_interval'] ) {
		return $response;
	}

	if ( ! et_is_builder_available_cookie_set() ) {
		return $response;
	}

	$response['heartbeat_interval'] = et_builder_heartbeat_interval();

	return $response;
}
add_filter( 'heartbeat_send', 'et_builder_ensure_heartbeat_interval', 100, 2 );

/**
 * Sync during WP heartbeat to have BB check if changes are made outside of itself, and to re-init with changed content if changes occurred.
 *
 * @param array $response The Heartbeat response.
 *
 * @return mixed
 */
function et_pb_heartbeat_post_modified( $response ) {
	et_core_nonce_verified_previously();

	if ( ! current_user_can( 'edit_posts' ) ) {
		return $response;
	}

	if ( empty( $_POST['data'] ) ) {
		return $response;
	}

	// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- sanitize_text_fields function does sanitization.
	$heartbeat_data    = et_()->sanitize_text_fields( $_POST['data'] );
	$has_focus         = isset( $_POST['has_focus'] ) && 'true' === $_POST['has_focus'] ? true : false;
	$heartbeat_data_et = ! empty( $heartbeat_data['et'] ) ? $heartbeat_data['et'] : false;

	if ( ! empty( $heartbeat_data_et ) ) {
		$post_id = ! empty( $heartbeat_data_et['post_id'] ) ? absint( $heartbeat_data_et['post_id'] ) : '';

		if ( empty( $heartbeat_data_et['post_id'] ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return $response;
		}

		$last_post_modified = sanitize_text_field( $heartbeat_data_et['last_post_modified'] );
		$built_by           = sanitize_text_field( $heartbeat_data_et['built_by'] );
		$force_check        = isset( $heartbeat_data_et['force_check'] ) && 'true' === $heartbeat_data_et['force_check'] ? true : false;
		$force_autosave     = isset( $heartbeat_data_et['force_autosave'] ) && 'true' === $heartbeat_data_et['force_autosave'] ? true : false;
		$current_user_id    = get_current_user_id();

		$_post = get_post( $post_id );

		if ( ! $post_id || ! $_post || ! is_object( $_post ) ) {
			return $response;
		}

		// minimum sucessful response.
		$response['et'] = array(
			'received'       => true,
			'force_check'    => $force_check,
			'force_autosave' => $force_autosave,
		);

		// the editor in focus is not going to be receiving an update from the other editor
		// so we can return early.
		if ( $has_focus && ! $force_check ) {
			$response['et']['action'] = 'No actions since this editor has focus'; // dev use.
			return $response;
		}

		if ( $force_autosave ) {
			$response['et']['action'] = 'No actions since this is a force autosave request'; // dev use.
			return $response;
		}

		// from here down we know that the following logic applies to the editor
		// currently *not* in focus, i.e. the one eligable for a potential sync update.

		// sync builder settings.
		$builder_settings_autosave = get_post_meta( $post_id, "_et_builder_settings_autosave_{$current_user_id}", true );
		if ( ! empty( $builder_settings_autosave ) ) {
			$response['et']['builder_settings_autosave'] = $builder_settings_autosave;
		}

		$post_content  = $_post->post_content;
		$post_modified = $_post->post_modified;

		$autosave = wp_get_post_autosave( $post_id, $current_user_id );

		$post_post_modified                   = gmdate( 'U', strtotime( $post_modified ) );
		$response['et']['post_post_modified'] = $_post->post_modified;

		if ( ! empty( $autosave ) ) {
			$response['et']['autosave_exists']        = true;
			$autosave_post_modified                   = gmdate( 'U', strtotime( $autosave->post_modified ) );
			$response['et']['autosave_post_modified'] = $autosave->post_modified;
		} else {
			$response['et']['autosave_exists'] = false;
		}

		if ( ! empty( $autosave ) && $autosave_post_modified > $post_post_modified ) {
			$response['et']['used_autosave'] = true;
			$post_id                         = $autosave->ID;
			$post_content                    = $autosave->post_content;
			$post_modified                   = $autosave->post_modified;
		} else {
			$response['et']['used_autosave'] = false;
		}

		$response['et']['post_id']            = $post_id;
		$response['et']['last_post_modified'] = $last_post_modified;
		$response['et']['post_modified']      = $post_modified;

		// security short circuit.
		$_post = get_post( $post_id );

		// $post_id could be an autosave.
		$parent_post = wp_is_post_autosave( $post_id ) ? get_post( $_post->post_parent ) : $_post;

		if ( ! et_pb_is_pagebuilder_used( $parent_post->ID ) || ! in_array( $parent_post->post_type, et_builder_get_builder_post_types(), true ) ) {
			return $response;
		}
		// end security short circuit.

		if ( $last_post_modified !== $post_modified ) {

			// check if the newly modified was made by opposite builder,
			// and if so, send it back in the response.
			if ( 'bb' === $built_by ) {
				// backend builder in use and in focus.

				$response['et']['is_built_by_fb'] = et_builder_is_builder_built( $post_id, 'fb' );
				// check if latest post_content is built by fb.
				if ( et_builder_is_builder_built( $post_id, 'fb' ) ) {
					if ( et_builder_bfb_enabled() ) {
						$post_content_obj                   = et_fb_process_shortcode( $post_content );
						$response['et']['post_content_obj'] = $post_content_obj;
					} else {
						$response['et']['post_content'] = $post_content;
					}
					$response['et']['action'] = 'current editor is bb, updated to content that was built by fb'; // dev use.
				} else {
					$response['et']['action'] = 'current editor is bb, content wasnt updated by fb'; // dev use.
				}
			} else {
				// frontend builder in use and in focus.

				$response['et']['is_built_by_bb'] = et_builder_is_builder_built( $post_id, 'bb' );
				// check if latest post_content is built by bb.
				if ( et_builder_is_builder_built( $post_id, et_builder_bfb_enabled() ? 'fb' : 'bb' ) ) {
					$post_content_obj = et_fb_process_shortcode( $post_content );

					$response['et']['post_content_obj'] = $post_content_obj;
					$response['et']['action']           = 'current editor is fb, updated to content that was built by bb'; // dev use.
				} else {
					$response['et']['action'] = 'current editor is fb, content wasnt updated by bb'; // dev use.
				}
			}

			$global_presets_manager           = ET_Builder_Global_Presets_Settings::instance();
			$response['et']['global_presets'] = $global_presets_manager->get_global_presets();
		} else {
			$response['et']['post_not_modified'] = true;
			$response['et']['action']            = 'post content not modified externally'; // dev use.
		}
	}

	return $response;
}
add_filter( 'heartbeat_send', 'et_pb_heartbeat_post_modified' );

/**
 * Save a post submitted via ETBuilder Heartbeat.
 *
 * Adapted from WordPress
 *
 * @copyright 2016 by the WordPress contributors.
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * This program incorporates work covered by the following copyright and
 * permission notices:
 *
 * b2 is (c) 2001, 2002 Michel Valdrighi - m@tidakada.com - http://tidakada.com
 *
 * b2 is released under the GPL
 *
 * WordPress - Web publishing software
 *
 * Copyright 2003-2010 by the contributors
 *
 * WordPress is released under the GPL
 *
 * @param array $post_data Associative array of the submitted post data.
 * @return mixed The value 0 or WP_Error on failure. The saved post ID on success.
 *               The ID can be the draft post_id or the autosave revision post_id.
 */
function et_fb_autosave( $post_data ) {
	if ( ! defined( 'DOING_AUTOSAVE' ) ) {
		define( 'DOING_AUTOSAVE', true );
	}

	$post_id              = (int) $post_data['post_id'];
	$post_data['ID']      = $post_id;
	$post_data['post_ID'] = $post_id;

	if ( false === wp_verify_nonce( $post_data['et_fb_autosave_nonce'], 'et_fb_autosave_nonce' ) ) {
		return new WP_Error( 'invalid_nonce', __( 'Error while saving.', 'et_builder' ) );
	}

	$_post           = get_post( $post_id );
	$current_user_id = get_current_user_id();

	if ( ! et_fb_current_user_can_save( $post_id ) ) {
		return new WP_Error( 'edit_posts', __( 'Sorry, you are not allowed to edit this item.', 'et_builder' ) );
	}

	// NOTE, no stripslashes() needed first as it's already been done on the POST'ed $post_data prior.
	$shortcode_data = json_decode( $post_data['content'], true );

	$options              = array(
		'post_type' => sanitize_text_field( $post_data['post_type'] ),
	);
	$post_data['content'] = et_fb_process_to_shortcode( $shortcode_data, $options );

	if ( 'auto-draft' === $_post->post_status ) {
		$post_data['post_status'] = 'draft';
	}

	if ( ! wp_check_post_lock( $_post->ID ) && get_current_user_id() === $_post->post_author && ( 'auto-draft' === $_post->post_status || 'draft' === $_post->post_status ) ) {
		// Drafts and auto-drafts are just overwritten by autosave for the same user if the post is not locked.
		return edit_post( wp_slash( $post_data ) );
	} else {
		// Non drafts or other users drafts are not overwritten. The autosave is stored in a special post revision for each user.
		return wp_create_post_autosave( wp_slash( $post_data ) );
	}
}

/**
 * Autosave builder settings.
 *
 * @param integer $post_id post id.
 * @param array   $builder_settings builder settings.
 *
 * @return bool|int
 */
function et_pb_autosave_builder_settings( $post_id, $builder_settings ) {
	$current_user_id = get_current_user_id();
	// Builder settings autosave.
	if ( ! empty( $builder_settings ) ) {

		// Data is coming from `wp_ajax_heartbeat` which ran `wp_unslash` on it,
		// `update_post_meta` will do the same, resulting in legit slashes being removed
		// from page settings.
		// The solution is to add those slashes back before updating metas.
		$builder_settings = wp_slash( $builder_settings );

		// Pseudo activate AB Testing for VB draft/builder-sync interface.
		if ( isset( $builder_settings['et_pb_use_ab_testing'] ) ) {
			// Save autosave/draft AB Testing status.
			update_post_meta(
				$post_id,
				'_et_pb_use_ab_testing_draft',
				sanitize_text_field( $builder_settings['et_pb_use_ab_testing'] )
			);

			// Format AB Testing data, since BB has UI and actual input IDs. FB uses BB's UI ID.
			$builder_settings['et_pb_enable_ab_testing'] = $builder_settings['et_pb_use_ab_testing'];

			// Unset BB's actual input data.
			unset( $builder_settings['et_pb_use_ab_testing'] );
		}

		// Pseudo save AB Testing subjects for VB draft/builder-sync interface.
		if ( isset( $builder_settings['et_pb_ab_subjects'] ) ) {
			// Save autosave/draft subjects.
			update_post_meta(
				$post_id,
				'_et_pb_ab_subjects_draft',
				sanitize_text_field( et_prevent_duplicate_item( $builder_settings['et_pb_ab_subjects'], ',' ) )
			);

			// Format subjects data into array.
			$builder_settings['et_pb_ab_subjects'] = array_unique( explode( ',', $builder_settings['et_pb_ab_subjects'] ) );
		}

		$et_builder_settings_autosave_data = get_post_meta( $post_id, "_et_builder_settings_autosave_{$current_user_id}", true );

		// Merge incoming post meta changes with saved ones to avoid missing post meta changes that
		// has been synced but hasn't been delivered to VB. Let VB drops autosave once it has been
		// used / inserted into the layout.
		if ( is_array( $et_builder_settings_autosave_data ) && is_array( $builder_settings ) ) {
			$et_builder_settings_autosave_data = wp_parse_args(
				$builder_settings,
				$et_builder_settings_autosave_data
			);
		} else {
			$et_builder_settings_autosave_data = $builder_settings;
		}

		return update_post_meta(
			$post_id,
			"_et_builder_settings_autosave_{$current_user_id}",
			$et_builder_settings_autosave_data
		);
	}
}

/**
 * Autosave with heartbeat.
 *
 * Adapted from WordPress.
 *
 * @copyright 2016 by the WordPress contributors.
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * This program incorporates work covered by the following copyright and
 * permission notices:
 *
 * b2 is (c) 2001, 2002 Michel Valdrighi - m@tidakada.com - http://tidakada.com
 *
 * b2 is released under the GPL
 *
 * WordPress - Web publishing software
 *
 * Copyright 2003-2010 by the contributors
 *
 * WordPress is released under the GPL
 *
 * @param array $response The Heartbeat response.
 * @param array $data     The $_POST data sent.
 *
 * @return array The Heartbeat response.
 */
function et_fb_heartbeat_autosave( $response, $data ) {
	et_core_nonce_verified_previously();

	if ( ! current_user_can( 'edit_posts' ) ) {
		return $response;
	}

	if ( ! empty( $data['et_fb_autosave'] ) ) {
		$post_id = ! empty( $data['et_fb_autosave']['post_id'] ) ? absint( $data['et_fb_autosave']['post_id'] ) : '';

		if ( empty( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return $response;
		}

		$has_focus      = ! empty( $_POST['has_focus'] ) && 'true' === $_POST['has_focus'];
		$force_autosave = ! empty( $data['et'] ) && ! empty( $data['et']['force_autosave'] ) && 'true' === $data['et']['force_autosave'];

		$editor_1                         = 'fb' === $data['et']['built_by'] ? 'fb' : 'bb';
		$editor_2                         = 'fb' === $editor_1 ? 'bb' : 'fb';
		$editor_1_editing_cookie          = isset( $_COOKIE[ 'et-editing-post-' . $post_id . '-' . $editor_1 ] ) ? sanitize_text_field( $_COOKIE[ 'et-editing-post-' . $post_id . '-' . $editor_1 ] ) : false;
		$editor_2_editor_available_cookie = isset( $_COOKIE[ 'et-editor-available-post-' . $post_id . '-' . $editor_2 ] ) ? sanitize_text_field( $_COOKIE[ 'et-editor-available-post-' . $post_id . '-' . $editor_2 ] ) : false;
		$editor_1_autosavable             = ! empty( $editor_1_editing_cookie ) && empty( $editor_2_editor_available_cookie );

		if ( ! $has_focus && ! $force_autosave && ! $editor_1_autosavable ) {
			$response['et_fb_autosave'] = array(
				'success' => false,
				'message' => __( 'Not saved, editor out of focus', 'et_builder' ),
			);
			return $response;
		}

		$saved = et_fb_autosave( $data['et_fb_autosave'] );

		if ( ! is_wp_error( $saved ) && ! empty( $data['et_fb_autosave']['builder_settings'] ) ) {
			$builder_settings_autosaved                  = et_pb_autosave_builder_settings( $post_id, $data['et_fb_autosave']['builder_settings'] );
			$response['et_pb_autosave_builder_settings'] = array(
				'success' => $builder_settings_autosaved,
				'message' => __( 'Builder settings synced', 'et_builder' ),
			);
		}

		if ( is_wp_error( $saved ) ) {
			$response['et_fb_autosave'] = array(
				'success' => false,
				'message' => $saved->get_error_message(),
			);
		} elseif ( empty( $saved ) ) {
			$response['et_fb_autosave'] = array(
				'success' => false,
				'message' => __( 'Error while saving.', 'et_builder' ),
			);
		} else {
			/* translators: draft saved date format, see https://secure.php.net/date */
			$draft_saved_date_format = __( 'g:i:s a', 'et_builder' );
			/* translators: %s: date and time */
			$response['et_fb_autosave'] = array(
				'success' => true,
				// translators: %s date.
				'message' => sprintf( __( 'Draft saved at %s.', 'et_builder' ), date_i18n( $draft_saved_date_format ) ),
			);
		}
	}

	return $response;
}
add_filter( 'heartbeat_received', 'et_fb_heartbeat_autosave', 499, 2 );

/**
 * Builder settings autosave.
 *
 * @param array $response The Heartbeat response.
 * @param array $data The $_POST data sent.
 *
 * @return mixed
 */
function et_bb_heartbeat_autosave( $response, $data ) {
	et_core_nonce_verified_previously();

	if ( ! current_user_can( 'edit_posts' ) ) {
		return $response;
	}

	if ( ! empty( $data['wp_autosave'] ) ) {
		$has_focus      = ! empty( $_POST['has_focus'] ) && 'true' === $_POST['has_focus'];
		$force_autosave = ! empty( $data['et'] ) && ! empty( $data['et']['force_autosave'] ) && 'true' === $data['et']['force_autosave'];

		if ( ! $has_focus && ! $force_autosave ) {
			$response['wp_autosave'] = array(
				'success' => true,
				'message' => __( 'Not saved, editor out of focus', 'et_builder' ),
			);
			remove_filter( 'heartbeat_received', 'heartbeat_autosave', 500, 2 );
			remove_filter( 'heartbeat_received', 'et_bb_heartbeat_builder_settings_autosave', 500, 2 );
		} elseif ( $force_autosave ) {
			$response['wp_autosave_check'] = array(
				'success' => true,
				'message' => 'saved, because force_autosave ',
			);
		}
	}
	return $response;
}
add_filter( 'heartbeat_received', 'et_bb_heartbeat_autosave', 498, 2 );

/**
 * Builder settings sync.
 *
 * @param array $response The Heartbeat response.
 * @param array $data The $_POST data sent.
 *
 * @return mixed
 */
function et_bb_heartbeat_builder_settings_autosave( $response, $data ) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return $response;
	}

	if ( ! empty( $data['wp_autosave'] ) ) {
		$post_id = ! empty( $data['wp_autosave']['post_id'] ) ? absint( $data['wp_autosave']['post_id'] ) : '';

		if ( empty( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return $response;
		}

		if ( ! empty( $data['wp_autosave']['builder_settings'] ) ) {
			$builder_settings_autosaved                  = et_pb_autosave_builder_settings( $post_id, $data['wp_autosave']['builder_settings'] );
			$response['et_pb_autosave_builder_settings'] = array(
				'success' => $builder_settings_autosaved,
				'message' => __( 'Builder settings synced', 'et_builder' ),
			);
		}
	}

	return $response;
}
add_filter( 'heartbeat_received', 'et_bb_heartbeat_builder_settings_autosave', 500, 2 );

/**
 * Refresh nonces after user logs back in to an expired session.
 *
 * @param array   $response The Heartbeat response.
 * @param array   $data The $_POST data sent.
 * @param integer $screen_id The screen id.
 *
 * @return mixed
 */
function et_fb_wp_refresh_nonces( $response, $data, $screen_id ) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return $response;
	}

	if ( ! isset( $data['et']['built_by'] ) || 'fb' !== $data['et']['built_by'] ) {
		return $response;
	}

	$response['et'] = array(
		'exportUrl'       => et_fb_get_portability_export_url(),
		'nonces'          => et_fb_get_nonces(),
		'heartbeat_nonce' => wp_create_nonce( 'heartbeat-nonce' ),
	);

	return $response;
}
add_filter( 'wp_refresh_nonces', 'et_fb_wp_refresh_nonces', 10, 3 );

/**
 * Return portability export url.
 *
 * @return string
 */
function et_fb_get_portability_export_url() {
	$admin_url = is_ssl() ? admin_url() : admin_url( '', 'http' );
	$args      = array(
		'et_core_portability' => true,
		'context'             => 'et_builder',
		'name'                => 'temp_name',
		'nonce'               => wp_create_nonce( 'et_core_portability_nonce' ),
	);
	return add_query_arg( $args, $admin_url );
}

/**
 * Return FB nonces.
 *
 * @return array
 */
function et_fb_get_nonces() {
	$nonces    = apply_filters( 'et_fb_nonces', array() );
	$fb_nonces = array(
		'moduleContactFormSubmit'         => wp_create_nonce( 'et-pb-contact-form-submit' ),
		'et_admin_load'                   => wp_create_nonce( 'et_admin_load_nonce' ),
		'computedProperty'                => wp_create_nonce( 'et_pb_process_computed_property_nonce' ),
		'renderShortcode'                 => wp_create_nonce( 'et_pb_render_shortcode_nonce' ),
		'updateAssets'                    => wp_create_nonce( 'et_fb_update_helper_assets_nonce' ),
		'loadAssets'                      => wp_create_nonce( 'et_fb_load_helper_assets_nonce' ),
		'renderSave'                      => wp_create_nonce( 'et_fb_save_nonce' ),
		'convertToShortcode'              => wp_create_nonce( 'et_fb_convert_to_shortcode_nonce' ),
		'dropAutosave'                    => wp_create_nonce( 'et_fb_drop_autosave_nonce' ),
		'prepareShortcode'                => wp_create_nonce( 'et_fb_prepare_shortcode_nonce' ),
		'processImportedData'             => wp_create_nonce( 'et_fb_process_imported_data_nonce' ),
		'retrieveLibraryModules'          => wp_create_nonce( 'et_fb_retrieve_library_modules_nonce' ),
		'saveLibraryModules'              => wp_create_nonce( 'et_fb_save_library_modules_nonce' ),
		'splitLibraryItem'                => wp_create_nonce( 'et_builder_split_library_item' ),
		'clearTempPresets'                => wp_create_nonce( 'et_fb_clear_temp_presets_nonce' ),
		'saveCloudItemContent'            => wp_create_nonce( 'et_fb_save_cloud_item_nonce' ),
		'removeLibraryModules'            => wp_create_nonce( 'et_fb_remove_library_modules_nonce' ),
		'preview'                         => wp_create_nonce( 'et_pb_preview_nonce' ),
		'autosave'                        => wp_create_nonce( 'et_fb_autosave_nonce' ),
		'moduleEmailOptinFetchLists'      => wp_create_nonce( 'et_builder_email_fetch_lists_nonce' ),
		'moduleEmailOptinAddAccount'      => wp_create_nonce( 'et_builder_email_add_account_nonce' ),
		'moduleEmailOptinRemoveAccount'   => wp_create_nonce( 'et_builder_email_remove_account_nonce' ),
		'uploadFontNonce'                 => wp_create_nonce( 'et_fb_upload_font_nonce' ),
		'abTestingReport'                 => wp_create_nonce( 'ab_testing_builder_nonce' ),
		'libraryLayoutsData'              => wp_create_nonce( 'et_builder_library_get_layouts_data' ),
		'libraryGetLayout'                => wp_create_nonce( 'et_builder_library_get_layout' ),
		'libraryUpdateLayout'             => wp_create_nonce( 'et_builder_library_update_layout' ),
		'libraryConvertLayout'            => wp_create_nonce( 'et_builder_library_convert_layout' ),
		'libraryUpdateTerms'              => wp_create_nonce( 'et_builder_library_update_terms' ),
		'libraryUpdateLocation'           => wp_create_nonce( 'et_builder_library_toggle_item_location' ),
		'libraryUpdateAccount'            => wp_create_nonce( 'et_builder_library_update_account' ),
		'libraryGetCloudToken'            => wp_create_nonce( 'et_builder_library_get_cloud_token' ),
		'fetchAttachments'                => wp_create_nonce( 'et_fb_fetch_attachments' ),
		'droploaderProcess'               => wp_create_nonce( 'et_builder_droploader_process_nonce' ),
		'resolvePostContent'              => wp_create_nonce( 'et_fb_resolve_post_content' ),
		'searchProducts'                  => wp_create_nonce( 'et_builder_search_products' ),
		'searchPosts'                     => wp_create_nonce( 'et_builder_search_posts' ),
		'getPostsList'                    => wp_create_nonce( 'et_fb_get_posts_list' ),
		'sendErrorReport'                 => wp_create_nonce( 'et_fb_send_error_report' ),
		'saveGlobalPresetsHistory'        => wp_create_nonce( 'et_builder_save_global_presets_history' ),
		'retrieveGlobalPresetsHistory'    => wp_create_nonce( 'et_builder_retrieve_global_presets_history' ),
		'migrateModuleCustomizerPhaseTwo' => wp_create_nonce( 'et_builder_migrate_module_customizer_phase_two' ),
		'getWoocommerceTabs'              => wp_create_nonce( 'et_builder_get_woocommerce_tabs' ),
		'getPostTypes'                    => wp_create_nonce( 'et_builder_ajax_get_post_types' ),
		'getAuthors'                      => wp_create_nonce( 'et_builder_ajax_get_authors' ),
		'getUserRoles'                    => wp_create_nonce( 'et_builder_ajax_get_user_roles' ),
		'getCategories'                   => wp_create_nonce( 'et_builder_ajax_get_categories' ),
		'getTags'                         => wp_create_nonce( 'et_builder_ajax_get_tags' ),
		'searchProducts'                  => wp_create_nonce( 'et_builder_ajax_search_products' ),
		'getDisplayConditionsStatus'      => wp_create_nonce( 'et_builder_ajax_get_display_conditions_status' ),
		'getPostMetaFields'               => wp_create_nonce( 'et_builder_ajax_get_post_meta_fields' ),
		'globalColorsSave'                => wp_create_nonce( 'et_builder_global_colors_save' ),
		'globalColorsGet'                 => wp_create_nonce( 'et_builder_global_colors_get' ),
		'defaultColorsUpdate'             => wp_create_nonce( 'et_builder_default_colors_update' ),
		'saveDomainToken'                 => wp_create_nonce( 'et_builder_ajax_save_domain_token' ),
		'beforeAfterComponents'           => wp_create_nonce( 'et_fb_fetch_before_after_components_nonce' ),
		'aiLayoutSaveDefaults'            => wp_create_nonce( 'et_ai_layout_save_defaults' ),
		'saveCustomizerFonts'             => wp_create_nonce( 'et_pb_save_customizer_fonts_nonce' ),
	);

	return array_merge( $nonces, $fb_nonces );
}

if ( ! function_exists( 'et_builder_is_product_tour_enabled' ) ) :
	/**
	 * Determine the product tour enabled or not.
	 */
	function et_builder_is_product_tour_enabled() {
		static $product_tour_enabled = null;

		if ( null !== $product_tour_enabled ) {
			return $product_tour_enabled;
		}

		if ( ! ( function_exists( 'et_fb_is_enabled' ) && et_fb_is_enabled() ) ) {
			// Do not update `$product_tour_enabled` at this point since we can run et_builder_is_product_tour_enabled() check later
			// when et_fb_is_enabled() will be available.
			return false;
		}

		/**
		 * Filters the on/off status of the product tour for the current user.
		 *
		 * @since 3.0.64
		 *
		 * @param string $product_tour_status_override Accepts 'on', 'off'.
		 */
		$product_tour_status_override = apply_filters( 'et_builder_product_tour_status_override', false );

		if ( false !== $product_tour_status_override ) {
			$product_tour_enabled = 'on' === $product_tour_status_override;
		} else {
			$user_id                    = (int) get_current_user_id();
			$product_tour_settings      = et_get_option( 'product_tour_status', array() );
			$product_tour_status_global = 'on' === et_get_option( 'et_pb_product_tour_global', 'on' );
			$product_tour_enabled       = $product_tour_status_global && ( ! isset( $product_tour_settings[ $user_id ] ) || 'on' === $product_tour_settings[ $user_id ] );
		}

		return $product_tour_enabled;
	}
endif;

/**
 * Get module backbone template.
 */
function et_pb_get_backbone_template() {
	if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$module_slugs = isset( $_POST['et_modules_slugs'] ) ? json_decode( str_replace( '\\', '', sanitize_text_field( $_POST['et_modules_slugs'] ) ) ) : '';
	$post_type    = isset( $_POST['et_post_type'] ) ? sanitize_text_field( $_POST['et_post_type'] ) : '';

	// Enable zlib compression.
	et_builder_enable_zlib_compression();
	// get the portion of templates for specified slugs.
	$result = wp_json_encode( ET_Builder_Element::get_modules_templates( $post_type, $module_slugs->missing_modules_array ) );

	die( et_core_esc_previously( $result ) );
}
add_action( 'wp_ajax_et_pb_get_backbone_template', 'et_pb_get_backbone_template' );


if ( ! function_exists( 'et_builder_email_add_account' ) ) :
	/**
	 * Ajax handler for the Email Opt-in Module's "Add Account" action.
	 */
	function et_builder_email_add_account() {
		et_core_security_check( 'manage_options', 'et_builder_email_add_account_nonce' );

		$provider_slug = isset( $_POST['et_provider'] ) ? sanitize_text_field( $_POST['et_provider'] ) : '';
		$name_key      = "et_{$provider_slug}_account_name";
		$account_name  = isset( $_POST[ $name_key ] ) ? sanitize_text_field( stripslashes( $_POST[ $name_key ] ) ) : ''; // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- sanitize_text_field() function does sanitation.

		if ( isset( $_POST['module_class'] ) && in_array( $_POST['module_class'], array( 'Signup', 'Contact_Form' ), true ) ) {
			$module_class = sanitize_text_field( $_POST['module_class'] );
		} else {
			$module_class = 'Signup';
		}

		$is_bb           = isset( $_POST['et_bb'] );
		$is_spam_account = isset( $_POST['is_spam_account'] );

		if ( empty( $provider_slug ) || empty( $account_name ) ) {
			et_core_die();
		}

		unset( $_POST[ $name_key ] );

		$fields = et_builder_email_get_fields_from_post_data( $provider_slug, $is_spam_account );

		if ( false === $fields ) {
			et_core_die();
		}

		if ( $is_spam_account ) {
			$result = et_core_api_spam_add_account( $provider_slug, $account_name, $fields );

		} else {
			$result = et_core_api_email_fetch_lists( $provider_slug, $account_name, $fields );
		}

		$_ = ET_Core_Data_Utils::instance();

		// Get data in builder format.
		$list_data = et_builder_email_get_lists_field_data( $provider_slug, $is_bb, $module_class );

		if ( 'success' === $result ) {
			$result = array(
				'error'                    => false,
				'accounts_list'            => $_->array_get( $list_data, 'accounts_list', $list_data ),
				'custom_fields'            => $_->array_get( $list_data, 'custom_fields', array() ),
				'predefined_custom_fields' => ET_Core_API_Email_Providers::instance()->custom_fields_data(),
			);
		} elseif ( is_array( $result ) && isset( $result['redirect_url'] ) ) {
			$result = array(
				'error'                    => false,
				'redirect_url'             => $result['redirect_url'],
				'accounts_list'            => $_->array_get( $list_data, 'accounts_list', $list_data ),
				'custom_fields'            => $_->array_get( $list_data, 'custom_fields', array() ),
				'predefined_custom_fields' => ET_Core_API_Email_Providers::instance()->custom_fields_data(),
			);
		} else {
			$result = array(
				'error'                    => true,
				'message'                  => esc_html__( 'Error: ', 'et_builder' ) . esc_html( $result ),
				'accounts_list'            => $_->array_get( $list_data, 'accounts_list', $list_data ),
				'custom_fields'            => $_->array_get( $list_data, 'custom_fields', array() ),
				'predefined_custom_fields' => ET_Core_API_Email_Providers::instance()->custom_fields_data(),
			);
		}

		die( wp_json_encode( $result ) );
		//phpcs:enable
	}
	add_action( 'wp_ajax_et_builder_email_add_account', 'et_builder_email_add_account' );
endif;

if ( ! function_exists( 'et_builder_finish_oauth2_authorization' ) ) :
	/**
	 * Process the Email Optin OAuth2 authorization from builder / VB.
	 * This will be loaded in a popup window.
	 *
	 * @since 4.9.0
	 *
	 * @throws Exception If the Provider or Account not exists.
	 *
	 * @return void
	 */
	function et_builder_finish_oauth2_authorization() {
		if ( ! isset( $_GET['et-core-api-email-auth'] ) || empty( esc_attr( $_GET['et-core-api-email-auth'] ) ) ) { // // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Recommended -- logic for nonce checks are following
			return;
		}

		if ( ! isset( $_GET['state'] ) || 0 !== strpos( $_GET['state'], 'ET_Core' ) ) { // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.NonceVerification.Recommended -- logic for nonce checks are following
			return;
		}

		$authenticated = false;
		try {
			et_core_nonce_verified_previously();

			list( $_, $name, $account, $nonce ) = explode( '|', sanitize_text_field( rawurldecode( $_GET['state'] ) ) ); // @phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- Sanitized with sanitize_text_field

			if ( ! $name || ! $account || ! $nonce ) {
				return;
			}

			$_GET['nonce'] = $nonce;

			et_core_security_check( 'manage_options', 'et_core_api_service_oauth2', 'nonce', '_GET' );

			$providers = ET_Core_API_Email_Providers::instance();
			if ( ! $providers->account_exists( $name, $account ) ) {
				throw new Exception( __( 'Account not exists.', 'et_builder' ) );
			}

			$provider = $providers->get( $name, $account, 'builder' );
			if ( ! $provider ) {
				throw new Exception( __( 'Email provider not exists.', 'et_builder' ) );
			}

			$authenticated = $provider->is_authenticated();
			if ( ! $authenticated ) {
				$authenticated = $provider->authenticate();
			}
		} catch ( Exception $err ) {
			$authenticated = false;
		}
		?>
		<script type="text/javascript">
			// Send a message to the window opener to close this window and process further.
			if (window.opener) {
				window.opener.postMessage({ authenticated: <?php echo $authenticated ? 1 : 0; ?> }, window.opener.location);
			}
		</script>
		<?php
		exit();
	}
	add_action( 'init', 'et_builder_finish_oauth2_authorization' );
endif;

if ( ! function_exists( 'et_builder_email_get_fields_from_post_data' ) ) :
	/**
	 * Retrieve the email fields by the provider.
	 *
	 * @param string  $provider_slug the email provider slug.
	 * @param boolean $is_spam_account Whether it is spam proctection provider or not.
	 *
	 * @return array
	 */
	function et_builder_email_get_fields_from_post_data( $provider_slug, $is_spam_account = false ) {
		et_core_security_check( 'manage_options', 'et_builder_email_add_account_nonce' );

		if ( $is_spam_account ) {
			$fields = ET_Core_API_Spam_Providers::instance()->account_fields( $provider_slug );
		} else {
			$fields = ET_Core_API_Email_Providers::instance()->account_fields( $provider_slug );
		}

		$result   = array();
		$protocol = is_ssl() ? 'https' : 'http';

		if ( ! $fields ) {
			// If there are no fields to check then the check passes.
			return $fields;
		}

		foreach ( $fields as $field_name => $field_info ) {
			$key = "et_{$provider_slug}_{$field_name}";

			if ( empty( $_POST[ $key ] ) ) {
				$required = true;

				if ( isset( $field_info['required'] ) ) {
					// Field can be required only when https or http.
					$required = $field_info['required'] === $protocol;
				}

				if ( $required && ! isset( $field_info['not_required'] ) ) {
					return false;
				}
			}

			$result[ $field_name ] = sanitize_text_field( $_POST[ $key ] );
		}

		return $result;
	}
endif;


if ( ! function_exists( 'et_builder_email_get_lists_field_data' ) ) :
	/**
	 * Get email list data in a builder's options field format.
	 *
	 * @param string $provider_slug The email provider slug.
	 * @param bool   $is_bb Whether is it backbend builder.
	 * @param string $module_class The ET_Builder_Module_ class suffix.
	 *
	 * @return array|string The data in the BB's format if `$is_bb` is `true`, the FB's format otherwise.
	 */
	function et_builder_email_get_lists_field_data( $provider_slug, $is_bb = false, $module_class = 'Signup' ) {
		$module     = 'ET_Builder_Module_' . $module_class;
		$module     = new $module();
		$fields     = $module->get_fields();
		$field_name = $provider_slug . '_list';
		$field      = $fields[ $field_name ];

		if ( $is_bb ) {
			$field['only_options'] = true;
			$field['name']         = $field_name;
			$field_data            = $module->render_field( $field );
		} else {
			$field_data = array(
				'accounts_list' => $field['options'],
			);

			if ( 'Signup' === $module_class ) {
				$signup_field                = new ET_Builder_Module_Signup_Item();
				$field_data['custom_fields'] = $signup_field->get_fields();
			}
		}
		// phpcs:enable
		et_pb_force_regenerate_templates();
		et_fb_delete_builder_assets();

		return $field_data;
	}
endif;


if ( ! function_exists( 'et_builder_email_get_lists' ) ) :
	/**
	 * Ajax handler for the Email Opt-in Module's "Fetch Lists" action.
	 */
	function et_builder_email_get_lists() {
		et_core_security_check( 'manage_options', 'et_builder_email_fetch_lists_nonce' );
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- BB is common abbreviation.
		$provider_slug = isset( $_POST['et_provider'] ) ? sanitize_text_field( $_POST['et_provider'] ) : '';
		$account_name  = isset( $_POST['et_account'] ) ? sanitize_text_field( stripslashes( $_POST['et_account'] ) ) : ''; // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- sanitize_text_field() function does sanitation.
		$is_bb         = isset( $_POST['et_bb'] );

		if ( empty( $provider_slug ) || empty( $account_name ) ) {
			et_core_die();
		}

		// Make sure email component group is loaded.
		new ET_Core_API_Email_Providers();

		$_ = ET_Core_Data_Utils::instance();

		// Fetch lists from provider.
		$message = et_core_api_email_fetch_lists( $provider_slug, $account_name );

		// Get data in builder format.
		$list_data = et_builder_email_get_lists_field_data( $provider_slug, $is_bb );

		$result = array(
			'error'                    => false,
			'accounts_list'            => $_->array_get( $list_data, 'accounts_list', $list_data ),
			'custom_fields'            => $_->array_get( $list_data, 'custom_fields', array() ),
			'predefined_custom_fields' => ET_Core_API_Email_Providers::instance()->custom_fields_data(),
		);

		if ( 'success' !== $message ) {
			$result['error']   = true;
			$result['message'] = esc_html__( 'Error: ', 'et_core' ) . esc_html( $message );
		}

		die( wp_json_encode( $result ) );
		// phpcs:enable
	}
	add_action( 'wp_ajax_et_builder_email_get_lists', 'et_builder_email_get_lists' );
endif;


if ( ! function_exists( 'et_builder_email_maybe_migrate_accounts' ) ) :
	/**
	 * Migrate email provider credential.
	 */
	function et_builder_email_maybe_migrate_accounts() {
		$divi_migrated_key    = 'divi_email_provider_credentials_migrated';
		$builder_migrated_key = 'email_provider_credentials_migrated';

		$builder_options  = (array) get_option( 'et_pb_builder_options' );
		$builder_migrated = isset( $builder_options[ $builder_migrated_key ] );
		$divi_migrated    = false !== et_get_option( $divi_migrated_key, false );

		$data_utils = ET_Core_Data_Utils::instance();
		$migrations = array(
			'builder' => $builder_migrated,
			'divi'    => $divi_migrated,
		);
		$providers  = new ET_Core_API_Email_Providers(); // Ensure the email component group is loaded.

		if ( $data_utils->all( $migrations, true ) ) {
			// We've already migrated accounts data.
			return;
		}

		foreach ( $migrations as $product => $completed ) {
			if ( 'builder' === $product ) {
				$account_name      = 'Divi Builder Plugin';
				$mailchimp_api_key = isset( $builder_options['newsletter_main_mailchimp_key'] ) ? $builder_options['newsletter_main_mailchimp_key'] : '';

				$consumer_key    = isset( $builder_options['aweber_consumer_key'] ) ? $builder_options['aweber_consumer_key'] : '';
				$consumer_secret = isset( $builder_options['aweber_consumer_secret'] ) ? $builder_options['aweber_consumer_secret'] : '';
				$access_key      = isset( $builder_options['aweber_access_key'] ) ? $builder_options['aweber_access_key'] : '';
				$access_secret   = isset( $builder_options['aweber_access_secret'] ) ? $builder_options['aweber_access_secret'] : '';
			} elseif ( 'divi' === $product ) {
				$account_name      = 'Divi Builder';
				$mailchimp_api_key = et_get_option( 'divi_mailchimp_api_key' );

				$consumer_key    = et_get_option( 'divi_aweber_consumer_key' );
				$consumer_secret = et_get_option( 'divi_aweber_consumer_secret' );
				$access_key      = et_get_option( 'divi_aweber_access_key' );
				$access_secret   = et_get_option( 'divi_aweber_access_secret' );
			} else {
				continue; // Satisfy code linter.
			}

			$aweber_key_parts = array( $consumer_key, $consumer_secret, $access_key, $access_secret );

			if ( $data_utils->all( $aweber_key_parts ) ) {
				// Typically AWeber tokens have five parts. We don't have the last part (the verifier token) because
				// we didn't save it at the time it was originally input by the user. Thus, we add an additional separator
				// (|) so that the token passes the processing performed by ET_Core_API_Email_Aweber::_parse_ID().
				$aweber_api_key = implode( '|', array( $consumer_key, $consumer_secret, $access_key, $access_secret, '|' ) );
			}

			if ( ! empty( $mailchimp_api_key ) ) {
				et_core_api_email_fetch_lists( 'MailChimp', "{$account_name} MailChimp", $mailchimp_api_key );
			}

			if ( ! empty( $aweber_api_key ) ) {
				$aweber = $providers->get( 'Aweber', "{$account_name} Aweber", 'builder' );

				$aweber->data['api_key']         = $aweber_api_key;
				$aweber->data['consumer_key']    = $consumer_key;
				$aweber->data['consumer_secret'] = $consumer_secret;
				$aweber->data['access_key']      = $access_key;
				$aweber->data['access_secret']   = $access_secret;
				$aweber->data['is_authorized']   = true;

				$aweber->save_data();
				$aweber->fetch_subscriber_lists();
			}
		}

		// Make sure the BB updates its cached templates.
		et_pb_force_regenerate_templates();

		$builder_options[ $builder_migrated_key ] = true;

		update_option( 'et_pb_builder_options', $builder_options );
		et_update_option( $divi_migrated_key, true );
	}
endif;


if ( ! function_exists( 'et_builder_email_remove_account' ) ) :
	/**
	 * Ajax handler for the Email Opt-in Module's "Remove Account" action.
	 */
	function et_builder_email_remove_account() {
		// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- BB is common abbreviation.
		et_core_security_check( 'manage_options', 'et_builder_email_remove_account_nonce' );

		$provider_slug = isset( $_POST['et_provider'] ) ? sanitize_text_field( $_POST['et_provider'] ) : '';
		$account_name  = isset( $_POST['et_account'] ) ? sanitize_text_field( stripslashes( $_POST['et_account'] ) ) : ''; // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- sanitize_text_field() function does sanitation.

		if ( isset( $_POST['module_class'] ) && in_array( $_POST['module_class'], array( 'Signup', 'Contact_Form' ), true ) ) {
			$module_class = sanitize_text_field( $_POST['module_class'] );
		} else {
			$module_class = 'Signup';
		}

		$is_bb           = isset( $_POST['et_bb'] );
		$is_spam_account = isset( $_POST['is_spam_account'] );

		if ( empty( $provider_slug ) || empty( $account_name ) ) {
			et_core_die();
		}

		if ( $is_spam_account ) {
			et_core_api_spam_remove_account( $provider_slug, $account_name );

		} else {
			et_core_api_email_remove_account( $provider_slug, $account_name );
		}

		$_ = ET_Core_Data_Utils::instance();

		// Get data in builder format.
		$list_data = et_builder_email_get_lists_field_data( $provider_slug, $is_bb, $module_class );

		$result = array(
			'error'                    => false,
			'accounts_list'            => $_->array_get( $list_data, 'accounts_list', $list_data ),
			'custom_fields'            => $_->array_get( $list_data, 'custom_fields', array() ),
			'predefined_custom_fields' => ET_Core_API_Email_Providers::instance()->custom_fields_data(),
		);

		die( wp_json_encode( $result ) );
		// phpcs:enable
	}
	add_action( 'wp_ajax_et_builder_email_remove_account', 'et_builder_email_remove_account' );
endif;


if ( ! function_exists( 'et_pb_submit_subscribe_form' ) ) :
	/**
	 * Ajax handler for Email Opt-in Module form submissions.
	 */
	function et_pb_submit_subscribe_form() {
		et_core_security_check( '', 'et_frontend_nonce' );

		$providers = ET_Core_API_Email_Providers::instance();
		$utils     = ET_Core_Data_Utils::instance();

		$checksum         = $utils->array_get_sanitized( $_POST, 'et_checksum' );
		$use_spam_service = get_option( 'et_pb_signup_' . $checksum );

		$provider_slug = $utils->array_get_sanitized( $_POST, 'et_provider' );
		$account_name  = stripslashes( $utils->array_get_sanitized( $_POST, 'et_account' ) );
		$custom_fields = $utils->array_get( $_POST, 'et_custom_fields', array() );
		$provider      = $providers->get( $provider_slug, $account_name, 'builder' );

		if ( ! $provider || ! $use_spam_service ) {
			et_core_die( esc_html__( 'Configuration Error: Invalid data.', 'et_builder' ) );
		}

		$args = array(
			'list_id'       => $utils->array_get_sanitized( $_POST, 'et_list_id' ),
			'email'         => $utils->array_get_sanitized( $_POST, 'et_email' ),
			'name'          => $utils->array_get_sanitized( $_POST, 'et_firstname' ),
			'last_name'     => $utils->array_get_sanitized( $_POST, 'et_lastname' ),
			'ip_address'    => $utils->array_get_sanitized( $_POST, 'et_ip_address' ),
			'custom_fields' => $utils->sanitize_text_fields( $custom_fields ),
		);

		if ( ! is_email( $args['email'] ) ) {
			et_core_die( esc_html__( 'Please input a valid email address.', 'et_builder' ) );
		}

		if ( '' === (string) $args['list_id'] ) {
			et_core_die( esc_html__( 'Configuration Error: No list has been selected for this form.', 'et_builder' ) );
		}

		$signup = ET_Builder_Element::get_module( 'et_pb_signup' );

		if ( $signup && 'on' === $use_spam_service && $signup->is_spam_submission() ) {
			et_core_die( esc_html__( 'You must be a human to submit this form.', 'et_builder' ) );
		}

		et_builder_email_maybe_migrate_accounts();

		$result = $provider->subscribe( $args );

		if ( 'success' === $result ) {
			$result = array( 'success' => true );
		} else {
			$message = esc_html__( 'Subscription Error: ', 'et_builder' );
			$result  = array( 'error' => $message . $result );
		}

		die( wp_json_encode( $result ) );
	}
	add_action( 'wp_ajax_et_pb_submit_subscribe_form', 'et_pb_submit_subscribe_form' );
	add_action( 'wp_ajax_nopriv_et_pb_submit_subscribe_form', 'et_pb_submit_subscribe_form' );
endif;


if ( ! function_exists( 'et_builder_has_limitation' ) ) :
	/**
	 * Whether or not the builder currently has a certain limitation.
	 *
	 * @since 3.18
	 *
	 * @param string $limit The builder limitation key/slug.
	 *
	 * @return bool
	 */
	function et_builder_has_limitation( $limit ) {
		if ( 'use_wrapped_styles' === $limit && et_builder_is_post_type_custom( get_post_type() ) ) {
			return true;
		}

		if ( ! et_builder_is_limited_mode() ) {
			return false;
		}

		$limited_builder        = et_get_limited_builder();
		$limited_builder_limits = et_get_limited_builder_defaults();

		switch ( $limited_builder ) {
			case 'divi-builder-plugin':
				$limited_builder_limits = array_merge(
					$limited_builder_limits,
					array(
						'use_wrapped_styles',
						'force_use_global_important',
						'use_limited_main',
						'use_additional_limiting_styles',
						'forced_icon_color_default',
						'register_fittext_script',
					)
				);
				break;
		}

		return in_array( $limit, $limited_builder_limits, true );
	}
endif;

/**
 * Get the defaults for limited builder limitations.
 *
 * @since 3.18
 *
 * @return string[]
 */
function et_get_limited_builder_defaults() {
	return apply_filters(
		'et_builder_get_limited_builder_defaults',
		array(
			'force_enqueue_theme_style',
		)
	);
}


/**
 * Get the slug name of the current limited builder.
 *
 * @since 3.18
 *
 * @return string The slug name of the current limited builder.
 */
function et_get_limited_builder() {
	// phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
	$get = $_GET;
	$bfb = '1' === et_()->array_get( $get, 'et_bfb', '0' );
	$dbp = et_is_builder_plugin_active();

	if ( $dbp ) {
		return 'divi-builder-plugin';
	}

	if ( $bfb ) {
		return 'bfb';
	}

	return '';
}


if ( ! function_exists( 'et_builder_is_limited_mode' ) ) :
	/**
	 * Is Builder in limited mode?
	 *
	 * @since 3.18
	 *
	 * @return bool  True - if the builder is in limited mode.
	 */
	function et_builder_is_limited_mode() {
		$get = $_GET; // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.

		return '' !== et_get_limited_builder();
	}
endif;

if ( ! function_exists( 'et_is_builder_plugin_active' ) ) :
	/**
	 * Is Builder plugin active?
	 *
	 * @since 3.18
	 * @deprecated ??
	 *
	 * @return bool  True - if the plugin is active.
	 */
	function et_is_builder_plugin_active() {
		return (bool) defined( 'ET_BUILDER_PLUGIN_ACTIVE' );
	}
endif;

if ( ! function_exists( 'et_is_shortcodes_plugin_active' ) ) :
	/**
	 * Is ET Shortcodes plugin active?
	 *
	 * @return bool  True - if the plugin is active
	 */
	function et_is_shortcodes_plugin_active() {
		return (bool) defined( 'ET_SHORTCODES_PLUGIN_VERSION' );
	}
endif;

/**
 * Saves the Role Settings into WP database
 *
 * @return void
 */
function et_pb_save_role_settings() {
	if ( ! isset( $_POST['et_pb_save_roles_nonce'] ) || ! wp_verify_nonce( $_POST['et_pb_save_roles_nonce'], 'et_roles_nonce' ) ) { // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- wp_verify_nonce() function does sanitation.
		die( -1 );
	}

	if ( ! current_user_can( 'manage_options' ) ) {
		die( -1 );
	}

	// handle received data and convert json string to array.
	$data_json         = isset( $_POST['et_pb_options_all'] ) ? str_replace( '\\', '', sanitize_text_field( $_POST['et_pb_options_all'] ) ) : '';
	$data              = json_decode( $data_json, true );
	$processed_options = array();

	// convert settings string for each role into array and save it into et_pb_role_settings option.
	if ( ! empty( $data ) ) {
		$role_capabilities = array();
		foreach ( $data as $role => $settings ) {
			parse_str( $data[ $role ], $role_capabilities );
			foreach ( $role_capabilities as $capability => $value ) {
				$processed_options[ $role ][ $capability ] = $value;
			}
		}
	}

	update_option( 'et_pb_role_settings', $processed_options );
	// set the flag to reload backbone templates and make sure all the role settings applied correctly right away.
	et_update_option( 'et_pb_clear_templates_cache', true );

	// Delete cached definitions / helpers.
	et_fb_delete_builder_assets();

	$response = array(
		'success' => true,
	);

	wp_send_json( $response );
}
add_action( 'wp_ajax_et_pb_save_role_settings', 'et_pb_save_role_settings' );

/**
 * Filter callback to determine what shortcodes tags are to be removed.
 * Stips all non-builder shortcodes.
 *
 * @see strip_shortcodes_tagnames
 *
 * @param array $tags_to_remove An array of tags to be removed during strip_shortcodes() call.
 *
 * @return array An array of tags to be removed during strip_shortcodes() call.
 */
function et_pb_strip_non_builder_shortcodes_tagnames( $tags_to_remove ) {
	// Initial allowlist.
	$valid_tags = ET_Builder_Element::get_module_slugs_by_post_type();

	/**
	 * What shortcode tags are valid (and safe) builder shortcodes,
	 * all other shortcodes will be stripped.
	 *
	 * @param array $valid_tags Array of valid shortcode tags.
	 */
	$valid_tags = apply_filters( 'et_pb_valid_builder_shortcodes', $valid_tags );

	// Generate a blocklist, by subtracting the allowlist from all registered shortcodes.
	$tags_to_remove = array_diff( $tags_to_remove, $valid_tags );

	return $tags_to_remove;
}


if ( ! function_exists( 'et_is_yoast_seo_plugin_active' ) ) :
	/**
	 * Is Yoast SEO plugin active?
	 *
	 * @return bool  True - if the plugin is active.
	 */
	function et_is_yoast_seo_plugin_active() {
		return class_exists( 'WPSEO_Options' );
	}
endif;

/**
 * Remove all non-builder shortcodes from builder built post content.
 *
 * @param string $content Builder built post content.
 *
 * @return string Sanitized builder built post content.
 */
function et_pb_enforce_builder_shortcode( $content ) {
	add_filter( 'strip_shortcodes_tagnames', 'et_pb_strip_non_builder_shortcodes_tagnames' );

	$content = strip_shortcodes( $content );

	remove_filter( 'strip_shortcodes_tagnames', 'et_pb_strip_non_builder_shortcodes_tagnames' );

	// this will parse the shortcode to an array, then run it back through some sanity check and sanitization and reform into a shortcode again.
	$content = et_pb_sanitize_shortcode( $content, true );

	return $content;
}

if ( ! function_exists( 'et_pb_register_posttypes' ) ) :
	/**
	 * Register 'project' post type, 'project_category' and 'project_tag' taxonomies.
	 */
	function et_pb_register_posttypes() {
		$labels = array(
			'name'               => esc_html__( 'Projects', 'et_builder' ),
			'singular_name'      => esc_html__( 'Project', 'et_builder' ),
			'add_new'            => esc_html__( 'Add New', 'et_builder' ),
			'add_new_item'       => esc_html__( 'Add New Project', 'et_builder' ),
			'edit_item'          => esc_html__( 'Edit Project', 'et_builder' ),
			'new_item'           => esc_html__( 'New Project', 'et_builder' ),
			'all_items'          => esc_html__( 'All Projects', 'et_builder' ),
			'view_item'          => esc_html__( 'View Project', 'et_builder' ),
			'search_items'       => esc_html__( 'Search Projects', 'et_builder' ),
			'not_found'          => esc_html__( 'Nothing found', 'et_builder' ),
			'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'et_builder' ),
			'parent_item_colon'  => '',
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'can_export'         => true,
			'show_in_nav_menus'  => true,
			'query_var'          => true,
			'has_archive'        => true,
			'rewrite'            => apply_filters(
				'et_project_posttype_rewrite_args',
				array(
					'feeds'      => true,
					'slug'       => 'project',
					'with_front' => false,
				)
			),
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'menu_position'      => null,
			'show_in_rest'       => true,
			'supports'           => array( 'title', 'author', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions', 'custom-fields' ),
		);

		register_post_type( 'project', apply_filters( 'et_project_posttype_args', $args ) );

		$labels = array(
			'name'              => esc_html__( 'Project Categories', 'et_builder' ),
			'singular_name'     => esc_html__( 'Project Category', 'et_builder' ),
			'search_items'      => esc_html__( 'Search Categories', 'et_builder' ),
			'all_items'         => esc_html__( 'All Categories', 'et_builder' ),
			'parent_item'       => esc_html__( 'Parent Category', 'et_builder' ),
			'parent_item_colon' => esc_html__( 'Parent Category:', 'et_builder' ),
			'edit_item'         => esc_html__( 'Edit Category', 'et_builder' ),
			'update_item'       => esc_html__( 'Update Category', 'et_builder' ),
			'add_new_item'      => esc_html__( 'Add New Category', 'et_builder' ),
			'new_item_name'     => esc_html__( 'New Category Name', 'et_builder' ),
			'menu_name'         => esc_html__( 'Categories', 'et_builder' ),
			'not_found'         => esc_html__( "You currently don't have any project categories.", 'et_builder' ),
		);

		$project_category_args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
		);
		register_taxonomy( 'project_category', array( 'project' ), $project_category_args );

		$labels = array(
			'name'              => esc_html__( 'Project Tags', 'et_builder' ),
			'singular_name'     => esc_html__( 'Project Tag', 'et_builder' ),
			'search_items'      => esc_html__( 'Search Tags', 'et_builder' ),
			'all_items'         => esc_html__( 'All Tags', 'et_builder' ),
			'parent_item'       => esc_html__( 'Parent Tag', 'et_builder' ),
			'parent_item_colon' => esc_html__( 'Parent Tag:', 'et_builder' ),
			'edit_item'         => esc_html__( 'Edit Tag', 'et_builder' ),
			'update_item'       => esc_html__( 'Update Tag', 'et_builder' ),
			'add_new_item'      => esc_html__( 'Add New Tag', 'et_builder' ),
			'new_item_name'     => esc_html__( 'New Tag Name', 'et_builder' ),
			'menu_name'         => esc_html__( 'Tags', 'et_builder' ),
		);

		$project_tag_args = array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'show_in_rest'      => true,
		);
		register_taxonomy( 'project_tag', array( 'project' ), $project_tag_args );
	}
endif;

/**
 * Determine whether to load backbone template or not.
 *
 * @return bool
 */
function et_admin_backbone_templates_being_loaded() {
	if ( ! et_core_security_check_passed( '', 'et_admin_load_nonce' ) ) {
		return false;
	}

	if ( ! is_admin() ) {
		return false;
	}

	if ( ! wp_doing_ajax() ) {
		return false;
	}

	if ( ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
		return false;
	}

	if ( ! isset( $_POST['action'] ) || 'et_pb_get_backbone_templates' !== $_POST['action'] ) {
		return false;
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		return false;
	}

	return true;
}

if ( ! function_exists( 'et_pb_attempt_memory_limit_increase' ) ) :
	/**
	 * AJAX Callback :: Increase memory limit from failure notice .
	 */
	function et_pb_attempt_memory_limit_increase() {
		if ( ! isset( $_POST['et_admin_load_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_admin_load_nonce'] ), 'et_admin_load_nonce' ) ) {
			die( -1 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			die( -1 );
		}

		if ( et_increase_memory_limit() ) {
			et_update_option( 'set_memory_limit', '1' );

			die(
				wp_json_encode(
					array(
						'success' => true,
					)
				)
			);
		} else {
			die(
				wp_json_encode(
					array(
						'error' => true,
					)
				)
			);
		}

		die();
	}
endif;

add_action( 'wp_ajax_et_pb_increase_memory_limit', 'et_pb_attempt_memory_limit_increase' );

if ( ! function_exists( 'et_reset_memory_limit_increase' ) ) :
	/**
	 * AJAX Callback :: Reset memory limit increase when automatic memory limit increase get disabled.
	 */
	function et_reset_memory_limit_increase() {
		if ( ! isset( $_POST['et_builder_reset_memory_limit_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_builder_reset_memory_limit_nonce'] ), 'et_builder_reset_memory_limit_nonce' ) ) {
			die( -1 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			die( -1 );
		}

		if ( et_get_option( 'set_memory_limit' ) ) {
			et_delete_option( 'set_memory_limit' );
		}

		die( 'success' );
	}
endif;

add_action( 'wp_ajax_et_reset_memory_limit_increase', 'et_reset_memory_limit_increase' );

if ( ! function_exists( 'et_builder_get_core_modal_template' ) ) :
	/**
	 * Return core modal template.
	 */
	function et_builder_get_core_modal_template() {
		return '<div class="et-core-modal-overlay et-core-active">
			<div class="et-core-modal">
				<div class="et-core-modal-header">
					<h3 class="et-core-modal-title">%1$s</h3>
					<a href="#" class="et-core-modal-close" data-et-core-modal="close"></a>
				</div>
				<div class="et-core-modal-content">
					<p>%2$s</p>
				</div>
				%3$s
			</div>
		</div>';
	}
endif;

if ( ! function_exists( 'et_builder_get_core_modal_buttons_template' ) ) :
	/**
	 * Return core modal buttons template.
	 */
	function et_builder_get_core_modal_buttons_template() {
		return '<div class="et_pb_prompt_buttons">
					<br>
					<span class="spinner"></span>
					%1$s
				</div>
			</div>';
	}
endif;

if ( ! function_exists( 'et_builder_get_cache_notification_modal' ) ) :
	/**
	 * Return Builder Cache Warning modal template.
	 */
	function et_builder_get_cache_notification_modal() {
		$cache_plugin_message = '';

		$cache_plugin = et_pb_detect_cache_plugins();
		if ( false !== $cache_plugin ) {
			$cache_plugin_message = sprintf(
				// translators: %1$s: cache plugin name.
				esc_html__( 'You are using the %1$s plugin. We recommend clearing the plugin cache after updating your theme.', 'et_builder' ),
				esc_html( $cache_plugin['name'] )
			);

			$cache_plugin_message = '<p>' . $cache_plugin_message . '</p>';

			if ( ! empty( $cache_plugin['page'] ) ) {
				$cache_plugin_message .= sprintf(
					'<a href="%1$s" class="et_builder_modal_action_button" target="_blank">%2$s</a>',
					esc_url( admin_url( $cache_plugin['page'] ) ),
					esc_html__( 'Clear Plugin Cache', 'et_builder' )
				);
			}
		}

		$browser_cache_message = '<p>' . esc_html__( 'Builder files may also be cached in your browser. Please clear your browser cache.', 'et_builder' ) . '</p>';

		$browser_cache_message .= sprintf(
			'<a href="http://www.refreshyourcache.com/en/home/" class="et_builder_modal_action_button" target="_blank">%1$s</a>',
			esc_html__( 'Clear Browser Cache', 'et_builder' )
		);

		$output = sprintf(
			'<div class="et_pb_modal_overlay et_modal_on_top et_pb_failure_notification_modal et_pb_new_template_modal">
			<div class="et_pb_prompt_modal">
				<h2>%1$s</h2>

				<div class="et_pb_prompt_modal_inside">
					<p>%2$s</p>
					%4$s
					%5$s
					<p>%6$s</p>
				</div>

				<a href="#"" class="et_pb_prompt_dont_proceed et-pb-modal-close"></a>

				<div class="et_pb_prompt_buttons">
					<br>
					<span class="spinner"></span>
					<a href="#" class="et_pb_reload_builder button-primary et_pb_prompt_proceed">%3$s</a>
				</div>
			</div>
		</div>',
			esc_html__( 'Builder Cache Warning', 'et_builder' ),
			esc_html__( 'The Divi Builder has been updated, however your browser is loading an old cached version of the builder. Loading old files can cause the builder to malfunction.', 'et_builder' ),
			esc_html__( 'Reload The Builder', 'et_builder' ),
			et_core_esc_previously( $cache_plugin_message ),
			et_core_esc_previously( $browser_cache_message ),
			esc_html__( 'If you have cleared your plugin cache and browser cache, but still get this warning, then your files may be cached at the DNS or Server level. Contact your host or CDN for assistance.', 'et_builder' )
		);

		return $output;
	}
endif;

if ( ! function_exists( 'et_builder_get_failure_notification_modal' ) ) :
	/**
	 * Return Divi Builder Timeout failure notice modal.
	 */
	function et_builder_get_failure_notification_modal() {
		$warnings = et_builder_get_warnings();

		if ( false === $warnings ) {
			return '';
		}

		$messages = '';
		$i        = 1;

		foreach ( $warnings as $warning ) {
			$messages .= sprintf(
				'<p><strong>%1$s. </strong>%2$s</p>',
				esc_html( $i ),
				et_core_esc_previously( $warning )
			);

			$i++;
		}

		$output = sprintf(
			'<div class="et-core-modal-overlay et-builder-timeout et-core-active">
			<div class="et-core-modal">
				<div class="et-core-modal-header">
					<h3 class="et-core-modal-title">%1$s</h3>
					<a href="#" class="et-core-modal-close" data-et-core-modal="close"></a>
				</div>

				<div class="et-core-modal-content">
					<p><strong>%4$s</strong></p>

					%2$s
				</div>

				<div class="et_pb_prompt_buttons">
					<br>
					<span class="spinner"></span>
					<a href="#" class="et-core-modal-action">%3$s</a>
				</div>
			</div>
		</div>',
			esc_html__( 'Divi Builder Timeout', 'et_builder' ),
			et_core_esc_previously( $messages ),
			esc_html__( 'Reload The Builder', 'et_builder' ),
			esc_html__( 'Oops, it looks like the Divi Builder failed to load. Performing the following actions may help solve the problem.', 'et_builder' )
		);

		return $output;
	}
endif;

if ( ! function_exists( 'et_builder_get_no_builder_notification_modal' ) ) :
	/**
	 * Incompatible Post Type modal template.
	 */
	function et_builder_get_no_builder_notification_modal() {
		$output = sprintf(
			'<div class="et-core-modal-overlay et-builder-timeout et-core-active">
			<div class="et-core-modal">
				<div class="et-core-modal-header">
					<h3 class="et-core-modal-title">%1$s</h3>
					<a href="#" class="et-core-modal-close" data-et-core-modal="close"></a>
				</div>

				<div class="et-core-modal-content">
					<p><strong>%2$s</strong></p>
				</div>
			</div>
		</div>',
			esc_html__( 'Incompatible Post Type', 'et_builder' ),
			esc_html__( 'This post does not show the standard WordPress content area. Unfortunately, that means the Divi Builder cannot be used on this post.', 'et_builder' )
		);

		return $output;
	}
endif;

if ( ! function_exists( 'et_builder_get_no_browser_notification_modal' ) ) :
	/**
	 * Browser Is Not Supported modal template.
	 */
	function et_builder_get_no_browser_notification_modal() {
		$output = sprintf(
			'<div class="et-core-modal-overlay et-builder-timeout et-core-active">
			<div class="et-core-modal">
				<div class="et-core-modal-header">
					<h3 class="et-core-modal-title">%1$s</h3>
					<a href="#" class="et-core-modal-close" data-et-core-modal="close"></a>
				</div>

				<div class="et-core-modal-content">
					<p><strong>%2$s</strong></p>
				</div>
			</div>
		</div>',
			esc_html__( 'Your Browser Is Not Supported', 'et_builder' ),
			esc_html__( 'The Divi Builder does not support the browser you are using. Your browser is no longer being developed, so it is time to switch to something new! The Divi Builder works best in the most recent versions of Chrome, Firefox, Safari, Opera and Edge.', 'et_builder' )
		);

		return $output;
	}
endif;

if ( ! function_exists( 'et_builder_get_exit_notification_modal' ) ) :
	/**
	 * Have Unsaved Changes modal template.
	 */
	function et_builder_get_exit_notification_modal() {
		$output = sprintf(
			'<div class="et-core-modal-overlay et-core-modal-two-buttons et-builder-exit-modal et-core-active">
			<div class="et-core-modal">
				<div class="et-core-modal-header">
					<h3 class="et-core-modal-title">%1$s</h3>
					<a href="#" class="et-core-modal-close" data-et-core-modal="close"></a>
				</div>

				<div class="et-core-modal-content">
					<p>%2$s</p>
				</div>

				<div class="et_pb_prompt_buttons">
					<br>
					<span class="spinner"></span>
					<a href="#" class="et-core-modal-action et-core-modal-action-secondary">%3$s</a>
					<a href="#" class="et-core-modal-action">%4$s</a>
				</div>
			</div>
		</div>',
			esc_html__( 'You Have Unsaved Changes', 'et_builder' ),
			et_get_safe_localization( __( 'Your page contains changes that have not been saved. If you close the builder without saving, these changes will be lost. If you would like to leave the builder and save all changes, please select <strong>Save & Exit</strong>. If you would like to discard all recent changes, choose <strong>Discard & Exit</strong>.', 'et_builder' ) ),
			esc_html__( 'Discard & Exit', 'et_builder' ),
			esc_html__( 'Save & Exit', 'et_builder' )
		);

		return $output;
	}
endif;

if ( ! function_exists( 'et_builder_get_browser_autosave_notification_modal' ) ) :
	/**
	 * Return Browser Backup Exists modal template.
	 */
	function et_builder_get_browser_autosave_notification_modal() {
		$output = sprintf(
			'<div class="et-core-modal-overlay et-core-modal-two-buttons et-builder-autosave-modal et-core-active">
			<div class="et-core-modal">
				<div class="et-core-modal-header">
					<h3 class="et-core-modal-title">%1$s</h3>
					<a href="#" class="et-core-modal-close" data-et-core-modal="close"></a>
				</div>
				<div class="et-core-modal-content">
					<p>%2$s</p>
				</div>
				<div class="et_pb_prompt_buttons">
					<br>
					<span class="spinner"></span>
					<a href="#" class="et-core-modal-action et-core-modal-action-dont-restore et-core-modal-action-secondary">%3$s</a>
					<a href="#" class="et-core-modal-action et-core-modal-action-restore">%4$s</a>
				</div>
			</div>
		</div>',
			esc_html__( 'A Browser Backup Exists', 'et_builder' ),
			et_get_safe_localization( __( 'A browser backup exists for this post that is newer than  the version you are currently viewing. This backup was captured during your previous editing session, but you never saved it. Would you like to restore this backup and continue editing where you left off?', 'et_builder' ) ),
			esc_html__( "Don't Restore", 'et_builder' ), // left button.
			esc_html__( 'Restore', 'et_builder' ) // right button.
		);
		return $output;
	}
endif;

if ( ! function_exists( 'et_builder_get_server_autosave_notification_modal' ) ) :
	/**
	 * Return Autosave Exists modal template.
	 */
	function et_builder_get_server_autosave_notification_modal() {
		$output = sprintf(
			'<div class="et-core-modal-overlay et-core-modal-two-buttons et-builder-autosave-modal et-core-active">
			<div class="et-core-modal">
				<div class="et-core-modal-header">
					<h3 class="et-core-modal-title">%1$s</h3>
					<a href="#" class="et-core-modal-close" data-et-core-modal="close"></a>
				</div>
				<div class="et-core-modal-content">
					<p>%2$s</p>
				</div>
				<div class="et_pb_prompt_buttons">
					<br>
					<span class="spinner"></span>
					<a href="#" class="et-core-modal-action et-core-modal-action-dont-restore et-core-modal-action-secondary">%3$s</a>
					<a href="#" class="et-core-modal-action et-core-modal-action-restore">%4$s</a>
				</div>
			</div>
		</div>',
			esc_html__( 'An Autosave Exists', 'et_builder' ),
			et_get_safe_localization( __( 'A recent autosave exists for this post that is newer than the version you are currently viewing. This autosave was captured during your previous editing session, but you never saved it. Would you like to restore this autosave and continue editing where you left off?', 'et_builder' ) ),
			esc_html__( "Don't Restore", 'et_builder' ), // left button.
			esc_html__( 'Restore', 'et_builder' ) // right button.
		);
		return $output;
	}
endif;

if ( ! function_exists( 'et_builder_get_unsaved_notification_texts' ) ) :
	/**
	 *  Return Save Has Failed notification template.
	 */
	function et_builder_get_unsaved_notification_texts() {
		$text = sprintf(
			'<p>%1$s</p><p>%2$s</p><p>%3$s</p>',
			et_get_safe_localization( __( 'An error has occurred while saving your page. Various problems can cause a save to fail, such as a lack of server resources, firewall blockages, plugin conflicts or server misconfiguration. You can try saving again by clicking Try Again, or you can download a backup of your unsaved page by clicking Download Backup. Backups can be restored using the portability system while next editing your page.', 'et_builder' ) ),
			et_get_safe_localization( __( 'Contacting your host and asking them to increase the following PHP variables may help: memory_limit, max_execution_time, upload_max_filesize, post_max_size, max_input_time, max_input_vars. In addition, auditing your firewall error log (such as ModSecurity) may reveal false positives that are preventing saves from completing.', 'et_builder' ) ),
			et_get_safe_localization( __( 'Lastly, it is recommended that you temporarily disable all WordPress plugins and browser extensions and try to save again to determine if something is causing a conflict.', 'et_builder' ) )
		);

		return array(
			'header'  => esc_html__( 'Your Save Has Failed', 'et_builder' ),
			'text'    => $text,
			'buttons' => array(
				'secondary' => sprintf( '<a href="#" class="et-core-modal-action et-core-modal-action-secondary">%1$s</a>', esc_html__( 'Try Again', 'et_builder' ) ),
				'primary'   => sprintf( '<a href="#" class="et-core-modal-action et-core-modal-action-primary">%1$s</a>', esc_html__( 'Download Backup', 'et_builder' ) ),
			),
			'classes' => 'et-builder-unsaved-modal',
		);
	}
endif;

if ( ! function_exists( 'et_builder_get_global_presets_save_failure_texts' ) ) :
	/**
	 * Return Global Presets Save Failed notification modal template.
	 */
	function et_builder_get_global_presets_save_failure_texts() {
		$text = sprintf(
			'<p>%1$s</p>
		<a class="et-builder-global-presets-save-failure-download" style="display: none"></a>',
			et_get_safe_localization( __( 'An error has occurred while saving the Global Presets settings. Various problems can cause a save to fail, such as a lack of server resources, firewall blockages or plugin conflicts or server misconfiguration. You can try saving again by clicking Try Again, or you can download a backup of your unsaved defaults by clicking Download Backup. A backup can be helpful when contacting our Support Team.', 'et_builder' ) )
		);

		return array(
			'header'  => esc_html__( 'Save of Global Presets Has Failed', 'et_builder' ),
			'text'    => $text,
			'buttons' => array(
				'secondary' => sprintf( '<a href="#" class="et-core-modal-action et-core-modal-action-secondary">%1$s</a>', esc_html__( 'Try Again', 'et_builder' ) ),
				'primary'   => sprintf( '<a href="#" class="et-core-modal-action et-core-modal-action-primary">%1$s</a>', esc_html__( 'Download Backup', 'et_builder' ) ),
			),
			'classes' => 'et-builder-global-presets-save-failure-modal',
		);
	}
endif;

if ( ! function_exists( 'et_builder_get_global_presets_save_forbidden_texts' ) ) :
	/**
	 * Return Global presets save failure text.
	 */
	function et_builder_get_global_presets_save_forbidden_texts() {
		$text = sprintf(
			'<p>%1$s</p>',
			et_get_safe_localization( __( 'You do not have sufficient permissions to edit Divi Presets.', 'et_builder' ) )
		);

		return array(
			'header'  => esc_html__( 'Save of Global Presets Has Failed', 'et_builder' ),
			'text'    => $text,
			'buttons' => array(
				'primary' => sprintf( '<a href="#" class="et-core-modal-action et-core-modal-action-primary">%1$s</a>', esc_html__( 'Ok', 'et_builder' ) ),
			),
			'classes' => 'et-builder-global-presets-save-forbidden-modal',
		);
	}
endif;

if ( ! function_exists( 'et_builder_get_global_presets_load_failure_texts' ) ) :
	/**
	 * Return Global Presets Load Failed notification modal template.
	 */
	function et_builder_get_global_presets_load_failure_texts() {
		$text = sprintf(
			'<p>%1$s</p>',
			et_get_safe_localization( __( 'An error has occurred while loading the Global History States. Various problems can cause a save to fail, such as a lack of server resources, firewall blockages or plugin conflicts or server misconfiguration. You can try loading again by clicking Try Again.', 'et_builder' ) )
		);

		return array(
			'header'  => esc_html__( 'Load of Global Presets Has Failed', 'et_builder' ),
			'text'    => $text,
			'buttons' => array(
				'primary' => sprintf( '<a href="#" class="et-core-modal-action et-core-modal-action-primary">%1$s</a>', esc_html__( 'Try Again', 'et_builder' ) ),
			),
			'classes' => 'et-builder-global-presets-load-failure-modal',
		);
	}
endif;

if ( ! function_exists( 'et_builder_page_creation_modal' ) ) :
	/**
	 * Return Page Creation Card modal template.
	 */
	function et_builder_page_creation_modal() {
		return '<div class="et-pb-page-creation-card <%= option.className %>" data-action="<%= id %>">
			<div class="et-pb-page-creation-content">
				<img src="<%= option.images_uri %>/<%= option.imgSrc %>" data-src="<%= option.images_uri %>/<%= option.imgSrc %>" data-hover="<%= option.images_uri %>/<%= option.imgHover %>" alt="<%= option.titleText %>" />
				<div class="et-pb-page-creation-text">
					<h3><%= option.titleText %></h3>
					<p><%= option.descriptionText %></p>
				</div>
			</div>
			<a href="#" class="et-pb-page-creation-link"><%= option.buttonText %></a>
		</div>';
	}
endif;


if ( ! function_exists( 'et_builder_get_disabled_link_modal' ) ) :
	/**
	 * Return Link Disabled notification modal template.
	 */
	function et_builder_disabled_link_modal() {
		$output = sprintf(
			'<div class="et_pb_modal_overlay link-disabled">
			<div class="et_pb_prompt_modal">
				<h3>%1$s</h3>
				<p>%2$s</p>
				<div class="et_pb_prompt_buttons">
					<a href="#" class="et_pb_prompt_proceed">%3$s</a>
				</div>
			</div>
		</div>',
			esc_html__( 'Link Disabled', 'et_builder' ),
			esc_html__( 'During preview, link to different page is disabled', 'et_builder' ),
			esc_html__( 'Close', 'et_builder' )
		);

		return $output;
	}
endif;

if ( ! function_exists( 'et_builder_get_warnings' ) ) :
	/**
	 * Return
	 */
	function et_builder_get_warnings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$warnings = array();

		// WP_DEBUG check.
		if ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) {
			$warnings[] = sprintf(
				'%1$s. <a href="https://codex.wordpress.org/Debugging_in_WordPress" class="et_builder_modal_action_button" target="_blank">%2$s</a>',
				esc_html__( 'You have WP_DEBUG enabled. Please disable this setting in wp-config.php', 'et_builder' ),
				esc_html__( 'Disable Debug Mode', 'et_builder' )
			);
		}

		// Plugins check.
		$third_party_plugins_active = false;

		$excluded_plugins = array(
			'wordpress-importer/wordpress-importer.php',
			'divi-builder/divi-builder.php',
			'elegant-themes-updater/elegant-themes-updater.php',
			'et-security-patcher/et-security-patcher.php',
		);

		$active_plugins = get_option( 'active_plugins' );

		if ( is_array( $active_plugins ) && ! empty( $active_plugins ) ) {
			foreach ( $active_plugins as $plugin ) {
				if ( in_array( $plugin, $excluded_plugins, true ) ) {
					continue;
				}

				$third_party_plugins_active = true;

				break;
			}
		}

		if ( $third_party_plugins_active ) {
			$warnings[] = sprintf(
				'%1$s <a href="%3$s" class="et_builder_modal_action_button" target="_blank">%2$s</a>',
				esc_html__( 'You are using third party plugins. Try disabling each plugin to see if one is causing a conflict.', 'et_builder' ),
				esc_html__( 'Manage Your Plugins', 'et_builder' ),
				esc_url( admin_url( 'plugins.php' ) )
			);
		}

		// WordPress update check.
		require_once ABSPATH . 'wp-admin/includes/update.php';

		$updates = get_core_updates();

		if ( isset( $updates[0]->response ) && 'latest' !== $updates[0]->response ) {
			$warnings[] = sprintf(
				'%1$s <a href="%3$s" class="et_builder_modal_action_button" target="_blank">%2$s</a>',
				esc_html__( 'You are using an outdated version of WordPress. Please upgrade.', 'et_builder' ),
				esc_html__( 'Upgrade WordPress', 'et_builder' ),
				esc_url( admin_url( 'update-core.php' ) )
			);
		}

		global $et_current_memory_limit; // Memory check.

		if ( ! empty( $et_current_memory_limit ) && intval( $et_current_memory_limit ) < 128 ) {
			$class = ' et_builder_increase_memory';

			$warnings[] = sprintf(
				'%1$s. <a href="http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" class="et_builder_modal_action_button%3$s" target="_blank">%2$s</a>',
				esc_html__( 'Please increase your PHP Memory Limit. You can return the value to default via the Divi Theme Options in the future', 'et_builder' ),
				esc_html__( 'Increase Your Memory Limit Now', 'et_builder' ),
				esc_attr( $class )
			);
		}

		// Version check.
		$et_update_themes = get_site_transient( 'et_update_themes' );

		if ( is_object( $et_update_themes ) && isset( $et_update_themes->response ) ) {
			$theme_info = wp_get_theme();

			if ( is_child_theme() ) {
				$theme_info = wp_get_theme( $theme_info->parent_theme );
			}

			$name    = $theme_info->get( 'Name' );
			$version = $theme_info->get( 'Version' );

			if ( isset( $et_update_themes->response[ $name ] ) && isset( $et_update_themes->response[ $name ]['new_version'] ) && version_compare( $version, $et_update_themes->response[ $name ]['new_version'], '<' ) ) {
				$warnings[] = sprintf(
					'%1$s <a href="%3$s" class="et_builder_modal_action_button" target="_blank">%2$s</a>',
					sprintf(
					// translators: %1$s theme version.
						esc_html__( 'You are using an outdated version of the theme. The latest version is %1$s', 'et_builder' ),
						esc_html( $et_update_themes->response[ $name ]['new_version'] )
					),
					esc_html__( 'Upgrade', 'et_builder' ),
					esc_url( admin_url( 'themes.php' ) )
				);
			}
		}

		if ( empty( $warnings ) ) {
			return false;
		}

		return $warnings;
	}
endif;

if ( ! function_exists( 'et_increase_memory_limit' ) ) :
	/**
	 * Increase the memory limit.
	 */
	function et_increase_memory_limit() {
		if ( ! is_admin() ) {
			return false;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		// proceed only if current memory limit < 256.
		if ( et_core_get_memory_limit() >= 256 ) {
			return true;
		}

		$result = wp_raise_memory_limit();

		return ! empty( $result );
	}
endif;

if ( ! function_exists( 'et_maybe_increase_memory_limit' ) ) :
	/**
	 * Try to increase the php memory limit.
	 */
	function et_maybe_increase_memory_limit() {
		global $pagenow;

		if ( ! is_admin() ) {
			return;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		if ( empty( $pagenow ) ) {
			return;
		}

		// increase memory limit on Edit Post page only.
		if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		/**
		 * Check if a user clicked "Increase Memory Limit" button
		 * in the "Failure Notification" modal window.
		 */
		if ( ! et_should_memory_limit_increase() ) {
			return;
		}

		et_increase_memory_limit();
	}
endif;
add_action( 'admin_init', 'et_maybe_increase_memory_limit' );

if ( ! function_exists( 'et_should_memory_limit_increase' ) ) :
	/**
	 * Force php memory limit increase.
	 */
	function et_should_memory_limit_increase() {
		$memory_limit = et_get_option( 'set_memory_limit' );
		if ( '1' === $memory_limit ) {
			return true;
		}

		return false;
	}
endif;

if ( ! function_exists( 'et_reset_memory_limit_increase_setting' ) ) :
	/**
	 * Return Memory Limit Increase template.
	 */
	function et_reset_memory_limit_increase_setting() {
		wp_enqueue_script( 'et-builder-reset-memory-limit-increase', ET_BUILDER_URI . '/scripts/reset_memory_limit_increase_setting.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
		wp_localize_script(
			'et-builder-reset-memory-limit-increase',
			'et_reset_memory_limit_increase',
			array(
				'et_builder_reset_memory_limit_nonce' => wp_create_nonce( 'et_builder_reset_memory_limit_nonce' ),
			)
		);

		printf(
			'<button class="et_disable_memory_limit_increase button button-primary button-large">%1$s</button>',
			esc_html__( 'Disable Memory Limit Increase' )
		);
	}
endif;

if ( ! function_exists( 'et_pb_detect_cache_plugins' ) ) :
	/**
	 * Detect the activated cache plugins and return the link to plugin options and return its page link or false.
	 *
	 * @return string or bool
	 */
	function et_pb_detect_cache_plugins() {
		// Cache Plugins.
		if ( function_exists( 'edd_w3edge_w3tc_activate_license' ) ) {
			return array(
				'name' => 'W3 Total Cache',
				'page' => 'admin.php?page=w3tc_pgcache',
			);
		}

		if ( function_exists( 'wpsupercache_activate' ) ) {
			return array(
				'name' => 'WP Super Cache',
				'page' => 'options-general.php?page=wpsupercache',
			);
		}

		if ( class_exists( 'HyperCache' ) ) {
			return array(
				'name' => 'Hyper Cache',
				'page' => 'options-general.php?page=hyper-cache%2Foptions.php',
			);
		}

		if ( class_exists( '\zencache\plugin' ) ) {
			return array(
				'name' => 'ZenCache',
				'page' => 'admin.php?page=zencache',
			);
		}

		if ( class_exists( 'WpFastestCache' ) ) {
			return array(
				'name' => 'WP Fastest Cache',
				'page' => 'admin.php?page=WpFastestCacheOptions',
			);
		}

		if ( '1' === get_option( 'wordfenceActivated' ) ) {
			// Wordfence removed their support of Falcon cache in v6.2.8, so we'll
			// just check against their `cacheType` setting (if it exists).
			if ( class_exists( 'wfConfig' ) && 'falcon' === wfConfig::get( 'cacheType' ) ) {
				return array(
					'name' => 'Wordfence',
					'page' => 'admin.php?page=WordfenceSitePerf',
				);
			}
		}

		if ( function_exists( 'cachify_autoload' ) ) {
			return array(
				'name' => 'Cachify',
				'page' => 'options-general.php?page=cachify',
			);
		}

		if ( class_exists( 'FlexiCache' ) ) {
			return array(
				'name' => 'FlexiCache',
				'page' => 'options-general.php?page=flexicache',
			);
		}

		if ( function_exists( 'rocket_init' ) ) {
			return array(
				'name' => 'WP Rocket',
				'page' => 'options-general.php?page=wprocket',
			);
		}

		if ( function_exists( 'cloudflare_init' ) ) {
			return array(
				'name' => 'CloudFlare',
				'page' => 'options-general.php?page=cloudflare',
			);
		}

		if ( class_exists( 'Hummingbird\\WP_Hummingbird' ) ) {
			return array(
				'name' => 'Hummingbird',
				'page' => 'admin.php?page=wphb',
			);
		}

		if ( class_exists( 'comet_cache' ) ) {
			return array(
				'name' => 'Comet Cache',
				'page' => 'admin.php?page=comet_cache',
			);
		}

		if ( class_exists( 'Cache_Enabler' ) ) {
			return array(
				'name' => 'Cache Enabler',
				'page' => 'options-general.php?page=cache-enabler',
			);
		}

		// Hosting Provider Caching.
		if ( class_exists( 'batcache' ) ) {
			// Doesn't have clear cache button on WP Admin area.
			return array(
				'name' => 'Pressable Cache',
				'page' => '',
			);
		}

		if ( class_exists( 'WpeCommon' ) ) {
			return array(
				'name' => 'WP Engine Cache',
				'page' => 'admin.php?page=wpengine-common',
			);
		}

		if ( class_exists( 'Endurance_Page_Cache' ) ) {
			// The purge cache button exists on MU plugins page.
			return array(
				'name' => 'Endurance Page Cache',
				'page' => 'plugins.php?plugin_status=mustuse',
			);
		}

		if ( function_exists( 'pantheon_wp_clear_edge_all' ) ) {
			// Doesn't have clear cache button on WP Admin area.
			return array(
				'name' => 'Pantheon Advanced Page Cache',
				'page' => '',
			);
		}

		if ( function_exists( 'sg_cachepress_purge_cache' ) ) {
			return array(
				'name' => 'SG Optimizer',
				'page' => 'admin.php?page=sg-cachepress',
			);
		}

		if ( class_exists( 'Breeze_Admin' ) ) {
			return array(
				'name' => 'Breeze',
				'page' => 'options-general.php?page=breeze',
			);
		}

		if ( class_exists( '\Kinsta\Cache' ) ) {
			return array(
				'name' => 'Kinsta Cache',
				'page' => 'admin.php?page=kinsta-tools',
			);
		}

		if ( class_exists( '\WPaaS\Cache' ) ) {
			return array(
				'name' => 'GoDaddy Cache',
				'page' => '',
			);
		}

		// Complimentary Performance Plugins.
		if ( class_exists( 'autoptimizeCache' ) ) {
			return array(
				'name' => 'Autoptimize',
				'page' => 'options-general.php?page=autoptimize',
			);
		}

		if ( class_exists( 'WP_Optimize' ) ) {
			return array(
				'name' => 'WP-Optimize',
				'page' => 'admin.php?page=wpo_settings',
			);
		}

		return false;
	}
endif;

/**
 * Clear templates cache and delete cached definitions to force regenerate templates.
 */
function et_pb_force_regenerate_templates() {
	// add option to indicate that templates cache should be updated in case of term added/removed/updated.
	et_update_option( 'et_pb_clear_templates_cache', true );

	// Delete cached definitions / helpers.
	et_fb_delete_builder_assets();
}

add_action( 'created_term', 'et_pb_force_regenerate_templates' );
add_action( 'edited_term', 'et_pb_force_regenerate_templates' );
add_action( 'delete_term', 'et_pb_force_regenerate_templates' );

// @Todo we should remove this hook after BB is retired
// purge BB microtemplates cache after Theme Customizer changes.
add_action( 'customize_save_after', 'et_pb_force_regenerate_templates' );

/**
 * Return current ab module id.
 *
 * @param integer $test_id Test id.
 * @param bool    $subject_index Subject index.
 *
 * @return int|mixed
 */
function et_pb_ab_get_current_ab_module_id( $test_id, $subject_index = false ) {
	$all_subjects_raw = get_post_meta( $test_id, '_et_pb_ab_subjects', true );
	$all_subjects     = false !== $all_subjects_raw ? explode( ',', $all_subjects_raw ) : array();

	if ( false === $subject_index ) {
		$saved_next_subject    = get_post_meta( $test_id, '_et_pb_ab_next_subject', true );
		$current_subject_index = false !== $saved_next_subject ? (int) $saved_next_subject : 0;
	} else {
		$current_subject_index = $subject_index;
	}

	if ( empty( $all_subjects ) ) {
		return 0;
	}

	if ( ! isset( $all_subjects[ $current_subject_index ] ) ) {
		return $all_subjects[0];
	}

	return $all_subjects[ $current_subject_index ];
}

/**
 * Increment current subject index value on post meta.
 *
 * @param int $test_id test id.
 */
function et_pb_ab_increment_current_ab_module_id( $test_id ) {
	global $wpdb;

	// Get subjects and current subject index.
	$all_subjects_raw      = get_post_meta( $test_id, '_et_pb_ab_subjects', true );
	$all_subjects          = false !== $all_subjects_raw ? explode( ',', $all_subjects_raw ) : array();
	$saved_next_subject    = get_post_meta( $test_id, '_et_pb_ab_next_subject', true );
	$current_subject_index = false !== $saved_next_subject ? (int) $saved_next_subject : 0;

	if ( empty( $all_subjects ) ) {
		return;
	}

	// increment the index of next subject, set to 0 if it's a last subject in the list.
	$next_subject_index = ( count( $all_subjects ) - 1 ) < ( $current_subject_index + 1 ) ? 0 : $current_subject_index + 1;

	update_post_meta( $test_id, '_et_pb_ab_next_subject', $next_subject_index );
}

/**
 * Add the record into AB Testing log table.
 *
 * @param array $stats_data_array State record data.
 *
 * @return void
 */
function et_pb_add_stats_record( $stats_data_array ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'et_divi_ab_testing_stats';

	$record_date = current_time( 'mysql' );

	// sanitize and set vars.
	$test_id     = intval( $stats_data_array['test_id'] );
	$subject_id  = intval( $stats_data_array['subject_id'] );
	$record_type = sanitize_text_field( $stats_data_array['record_type'] );
	$record_date = sanitize_text_field( $record_date );

	// Check visitor cookie and do not proceed if event already logged for current visitor.
	if ( et_pb_ab_get_visitor_cookie( $test_id, $record_date ) ) {
		return;
	}

	$wpdb->insert(
		$table_name,
		array(
			'record_date' => $record_date,
			'test_id'     => $test_id,
			'subject_id'  => $subject_id,
			'event'       => $record_type,
		),
		array(
			'%s', // record_date.
			'%d', // test_id.
			'%d', // subject_id.
			'%s', // event.
		)
	);
}

/**
 * Set AB Testing formatted cookie.
 *
 * @since 4.4.3 Set cookie path, so the cookie will be available on overall site.
 *
 * @param int    $post_id post ID.
 * @param string $record_type record type.
 * @param mixed  $value cookie value.
 *
 * @return bool|mixed
 */
function et_pb_ab_set_visitor_cookie( $post_id, $record_type, $value = true ) {
	$unique_test_id = get_post_meta( $post_id, '_et_pb_ab_testing_id', true );
	$cookie_name    = sanitize_text_field( "et_pb_ab_{$record_type}_{$post_id}{$unique_test_id}" );

	return setcookie( $cookie_name, $value, 0, SITECOOKIEPATH );
}

/**
 * Get AB Testing formatted cookie.
 *
 * @param int    $post_id post ID.
 * @param string $record_type record type.
 *
 * @return bool|mixed
 */
function et_pb_ab_get_visitor_cookie( $post_id, $record_type ) {
	$unique_test_id = get_post_meta( $post_id, '_et_pb_ab_testing_id', true );
	$cookie_name    = "et_pb_ab_{$record_type}_{$post_id}{$unique_test_id}";

	return isset( $_COOKIE[ $cookie_name ] ) ? sanitize_text_field( $_COOKIE[ $cookie_name ] ) : false;
}

/**
 * Get subjects of particular post / AB Testing.
 *
 * @param int    $post_id post id.
 * @param string $type array|string type of output.
 * @param mixed  $prefix string|bool  prefix that should be prepended.
 * @param bool   $is_cron_task Whether subjects is autosave/draft or not.
 *
 * @return array
 */
function et_pb_ab_get_subjects( $post_id, $type = 'array', $prefix = false, $is_cron_task = false ) {
	$subjects_data = get_post_meta( $post_id, '_et_pb_ab_subjects', true );
	$fb_enabled    = et_fb_is_enabled();

	// Get autosave/draft subjects if post hasn't been published.
	if ( ! $is_cron_task && ! $subjects_data && $fb_enabled && 'publish' !== get_post_status() ) {
		$subjects_data = get_post_meta( $post_id, '_et_pb_ab_subjects_draft', true );
	}

	// If user wants string.
	if ( 'string' === $type ) {
		return $subjects_data;
	}

	// Convert into array.
	$subjects = explode( ',', $subjects_data );

	if ( ! empty( $subjects ) && $prefix ) {

		$prefixed_subjects = array();

		// Loop subject, add prefix.
		foreach ( $subjects as $subject ) {
			$prefixed_subjects[] = $prefix . (string) $subject;
		}

		return $prefixed_subjects;
	}

	return $subjects;
}

/**
 * Unhashed hashed subject id.
 *
 * @param int    $post_id post ID.
 * @param string $hashed_subject_id hashed subject id.
 *
 * @return string subject ID
 */
function et_pb_ab_unhashed_subject_id( $post_id, $hashed_subject_id ) {
	if ( ! $post_id || ! $hashed_subject_id ) {
		return false;
	}

	$ab_subjects = et_pb_ab_get_subjects( $post_id );
	$ab_hash_key = defined( 'NONCE_SALT' ) ? NONCE_SALT : 'default-divi-hash-key';
	$subject_id  = false;

	// Compare subjects against hashed subject id found on cookie to verify whether cookie value is valid or not.
	foreach ( $ab_subjects as $ab_subject ) {
		// Valid subject_id is found.
		if ( hash_hmac( 'md5', $ab_subject, $ab_hash_key ) === $hashed_subject_id ) {
			$subject_id = $ab_subject;

			// no need to continue.
			break;
		}
	}

	// If no valid subject found, get the first one.
	if ( ! $subject_id && isset( $ab_subjects[0] ) ) {
		$subject_id = $ab_subjects[0];
	}

	return $subject_id;
}

/**
 * AJAX Callback :: AB Testing :: Get subject id.
 */
function et_pb_ab_get_subject_id() {
	if ( ! isset( $_POST['et_frontend_nonce'] ) || ! wp_verify_nonce( $_POST['et_frontend_nonce'], 'et_frontend_nonce' ) ) {  // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- wp_verify_nonce() function does sanitation.
		die( -1 );
	}

	$test_id              = isset( $_POST['et_pb_ab_test_id'] ) ? intval( $_POST['et_pb_ab_test_id'] ) : 0;
	$hashed_subject_id    = et_pb_ab_get_visitor_cookie( $test_id, 'view_page' );
	$current_ab_module_id = et_pb_ab_unhashed_subject_id( $test_id, $hashed_subject_id );

	// retrieve the cached subjects HTML.
	$subjects_cache = get_post_meta( $test_id, 'et_pb_subjects_cache', true );

	$result = array(
		'id'      => $current_ab_module_id,
		'content' => isset( $subjects_cache[ $current_ab_module_id ] ) ? $subjects_cache[ $current_ab_module_id ] : '',
	);

	die( wp_json_encode( $result ) );
}
add_action( 'wp_ajax_et_pb_ab_get_subject_id', 'et_pb_ab_get_subject_id' );
add_action( 'wp_ajax_nopriv_et_pb_ab_get_subject_id', 'et_pb_ab_get_subject_id' );

/**
 * Register Builder Portability.
 *
 * @since 2.7.0
 */
function et_pb_register_builder_portabilities() {
	global $shortname;

	// Don't overwrite global.
	$_shortname = empty( $shortname ) ? 'divi' : $shortname;

	// get all the roles that can edit theme options.
	$applicability_roles = et_core_get_roles_by_capabilities( [ 'edit_theme_options' ] );

	// Make sure the Portability is loaded.
	et_core_load_component( 'portability' );

	if ( current_user_can( 'edit_theme_options' ) ) {
		// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		// Register the Roles Editor portability.
		$pb_roles = array(
			'title'         => esc_html__( 'Import & Export Roles', 'et_builder' ),
			'name'          => esc_html__( 'Divi Role Editor Settings', 'et_builder' ),
			'type'          => 'options',
			'target'        => 'et_pb_role_settings',
			'view'          => ( isset( $_GET['page'] ) && "et_{$_shortname}_role_editor" === $_GET['page'] ),
			'applicability' => $applicability_roles,
		);
		et_core_portability_register( 'et_pb_roles', $pb_roles );
		// phpcs:enable
	}

	if ( current_user_can( 'edit_posts' ) ) {
		// Register the Builder individual layouts portability.
		$args = array(
			'title' => esc_html__( 'Import & Export Layouts', 'et_builder' ),
			'name'  => esc_html__( 'Divi Builder Layout', 'et_builder' ),
			'type'  => 'post',
			'view'  => ( function_exists( 'et_builder_should_load_framework' ) && et_builder_should_load_framework() ),
		);
		et_core_portability_register( 'et_builder', $args );

		// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		// Register the Builder Layouts Post Type portability.
		$layouts = array(
			'title'  => esc_html__( 'Import & Export Layouts', 'et_builder' ),
			'name'   => esc_html__( 'Divi Builder Layouts', 'et_builder' ),
			'type'   => 'post_type',
			'target' => ET_BUILDER_LAYOUT_POST_TYPE,
			'view'   => ( isset( $_GET['post_type'] ) && ET_BUILDER_LAYOUT_POST_TYPE === $_GET['post_type'] ),
		);
		et_core_portability_register( 'et_builder_layouts', $layouts );
		// phpcs:enable
	}
}
add_action( 'admin_init', 'et_pb_register_builder_portabilities' );

/**
 * Modify the portability export WP query.
 *
 * @since To define
 *
 * @param WP_Query $query portability query.
 *
 * @return string New query.
 */
function et_pb_modify_portability_export_wp_query( $query ) {
	// Exclude predefined layout from export.
	return array_merge(
		$query,
		array(
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key'     => '_et_pb_predefined_layout',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key'     => '_et_pb_predefined_layout',
					'value'   => 'on',
					'compare' => 'NOT LIKE',
				),
			),
		)
	);
}
add_filter( 'et_core_portability_export_wp_query_et_builder_layouts', 'et_pb_modify_portability_export_wp_query' );

/**
 * Check whether current page is pagebuilder preview page.
 *
 * @return bool
 */
function is_et_pb_preview() {
	global $wp_query;
	// phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
	return ( 'true' === $wp_query->get( 'et_pb_preview' ) && isset( $_GET['et_pb_preview_nonce'] ) );
}

if ( ! function_exists( 'et_pb_is_pagebuilder_used' ) ) :
	/**
	 * Determine whether page builder is used or not on the post/page.
	 *
	 * @param integer $page_id The post id to be checked.
	 *
	 * @return bool
	 */
	function et_pb_is_pagebuilder_used( $page_id = 0 ) {
		if ( 0 === $page_id ) {
			$page_id = et_core_page_resource_get_the_ID();
		}

		return (
			'on' === get_post_meta( $page_id, '_et_pb_use_builder', true ) ||
			// Divi layout post type always use the builder.
			'et_pb_layout' === get_post_type( $page_id ) ||
			// Extra Category post type always use the builder.
			'layout' === get_post_type( $page_id )
		);
	}
endif;

if ( ! function_exists( 'et_fb_is_enabled' ) ) :
	/**
	 * Determine fb enabled status of a post / page.
	 *
	 * @internal NOTE: Don't use this from outside builder code! {@see et_core_is_fb_enabled()}.
	 *
	 * @param bool|integer $post_id The post ID to determine fb enabled status of a post / page.
	 *
	 * @return bool
	 */
	function et_fb_is_enabled( $post_id = false ) {
		// Cache results since the function could end up being called thousands of times.
		static $cache = array();

		if ( ! $post_id ) {
			global $post;

			$post_id = isset( $post->ID ) ? $post->ID : false;
		}

		$check = apply_filters( 'et_fb_is_enabled', null, $post_id );

		if ( null !== $check ) {
			return $check;
		}

		// phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		if ( empty( $_GET['et_fb'] ) ) {
			return false;
		}

		if ( isset( $cache[ $post_id ] ) ) {
			return $cache[ $post_id ];
		}

		$cache[ $post_id ] = false;

		if ( is_admin() ) {
			return false;
		}

		if ( is_customize_preview() ) {
			return false;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		if ( ! et_pb_is_pagebuilder_used( $post_id ) && ! et_fb_is_theme_builder_used_on_page() ) {
			return false;
		}

		if ( is_singular() && ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		if ( ! is_singular() && ! et_pb_is_allowed( 'theme_builder' ) ) {
			return false;
		}

		if ( ! et_pb_is_allowed( 'use_visual_builder' ) ) {
			return false;
		}

		$cache[ $post_id ] = true;

		return true;
	}
endif;

if ( ! function_exists( 'et_fb_is_enabled_on_any_template' ) ) :
	/**
	 * Determine fb enabled status of a post / page or any theme builder layout used in the page.
	 *
	 * @internal NOTE: Don't use this from outside builder code! {@see et_core_is_fb_enabled()}.
	 *
	 * @return bool
	 */
	function et_fb_is_enabled_on_any_template() {
		$theme_builder_layouts = et_theme_builder_get_template_layouts();

		// Unset main template from Theme Builder layouts to avoid PHP Notices.
		if ( isset( $theme_builder_layouts['et_template'] ) ) {
			unset( $theme_builder_layouts['et_template'] );
		}

		// Check if Builder is enabled on any Theme Builder Layout used.
		foreach ( $theme_builder_layouts as $key => $theme_builder_layout ) {
			if ( $theme_builder_layout['enabled'] && $theme_builder_layout['override'] ) {
				if ( et_fb_is_enabled( $theme_builder_layout['id'] ) ) {
					return true;
				}
			}
		}

		return false;
	}
endif;

if ( ! function_exists( 'et_fb_is_theme_builder_used_on_page' ) ) :
	/**
	 * Check if Theme Builder is Used on the page.
	 *
	 * @return bool
	 */
	function et_fb_is_theme_builder_used_on_page() {
		$theme_builder_layouts = et_theme_builder_get_template_layouts();

		// Unset main template from Theme Builder layouts to avoid PHP Notices.
		if ( isset( $theme_builder_layouts['et_template'] ) ) {
			unset( $theme_builder_layouts['et_template'] );
		}

		// If any template is used and enabled return true.
		foreach ( $theme_builder_layouts as $theme_builder_layout ) {
			if ( $theme_builder_layout['enabled'] && $theme_builder_layout['override'] ) {
				return true;
			}
		}

		return false;
	}
endif;

if ( ! function_exists( 'et_fb_is_builder_ajax' ) ) :
	/**
	 * Returns whether current request is a builder AJAX call.
	 *
	 * @return bool
	 */
	function et_fb_is_builder_ajax() {
		// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		if ( ! wp_doing_ajax() || empty( $_REQUEST['action'] ) ) {
			return false;
		}

		return in_array(
			$_REQUEST['action'],
			array(
				'et_fb_update_builder_assets',
				'et_fb_retrieve_builder_data',
			),
			true
		);
		// phpcs:enable
	}
endif;

if ( ! function_exists( 'et_fb_is_computed_callback_ajax' ) ) :
	/**
	 * Returns whether current request is computed callback AJAX call
	 *
	 * @return bool
	 */
	function et_fb_is_computed_callback_ajax() {
		// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		if ( ! wp_doing_ajax() || empty( $_REQUEST['action'] ) ) {
			return false;
		}

		return 'et_pb_process_computed_property' === $_REQUEST['action'];
		// phpcs:enable
	}
endif;

if ( ! function_exists( 'et_fb_is_before_after_components_callback_ajax' ) ) :
	/**
	 * Returns whether current request is before & after components callback AJAX call.
	 *
	 * @since 4.14.5
	 *
	 * @return bool
	 */
	function et_fb_is_before_after_components_callback_ajax() {
		// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		$action = ! empty( $_REQUEST['action'] ) ? sanitize_text_field( $_REQUEST['action'] ) : '';

		return wp_doing_ajax() && 'et_fb_fetch_before_after_components' === $action;
		// phpcs:enable
	}
endif;

if ( ! function_exists( 'et_fb_is_resolve_post_content_callback_ajax' ) ) :
	/**
	 * Returns whether current request is resolve post content callback AJAX call
	 *
	 * @return bool
	 */
	function et_fb_is_resolve_post_content_callback_ajax() {
		// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		if ( ! wp_doing_ajax() || empty( $_REQUEST['action'] ) ) {
			return false;
		}

		return 'et_builder_resolve_post_content' === $_REQUEST['action'];
		// phpcs:enable
	}
endif;


if ( ! function_exists( 'et_fb_auto_activate_builder' ) ) :
	/**
	 * FB :: enable page with no BB activated to directly use visual builder by clicking use visual builder link on WP admin bar.
	 */
	function et_fb_auto_activate_builder() {
		$post_id = get_the_ID();

		if (
			! is_admin() &&
			$post_id &&
			current_user_can( 'edit_post', $post_id ) &&
			isset( $_GET['et_fb_activation_nonce'] ) &&
			wp_verify_nonce( sanitize_text_field( $_GET['et_fb_activation_nonce'] ), 'et_fb_activation_nonce_' . get_the_ID() )
		) {
			$set_content  = et_builder_set_content_activation( $post_id );
			$post_url     = get_permalink( $post_id );
			$redirect_url = $set_content ? et_fb_get_vb_url( $post_url ) : $post_url;

			wp_safe_redirect( $redirect_url );
			exit();
		}
	}
endif;
add_action( 'template_redirect', 'et_fb_auto_activate_builder' );

/**
 * Enable the VB for a post.
 *
 * @since 4.0
 *
 * @param integer $post_id The post id for which to enable VB.
 * @param bool    $show_page_creation Whether to show page creation card.
 *
 * @return bool Success.
 */
function et_builder_enable_for_post( $post_id, $show_page_creation = true ) {
	$_post = get_post( $post_id );

	if ( ! $post_id || ! $_post || ! is_object( $_post ) ) {
		return false;
	}

	$activate_builder = update_post_meta( $post_id, '_et_pb_use_builder', 'on' );

	if ( false === $activate_builder ) {
		return false;
	}

	update_post_meta( $post_id, '_et_pb_show_page_creation', $show_page_creation ? 'on' : 'off' );

	return true;
}

/**
 * Set builder content at the time of enabling visual builder.
 *
 * @param bool|integer $post_id The post id on which visual builder get enable.
 *
 * @return bool
 */
function et_builder_set_content_activation( $post_id = false ) {
	$_post = get_post( $post_id );

	if ( ! $post_id || ! $_post || ! is_object( $_post ) ) {
		return false;
	}

	$activate_builder = et_builder_enable_for_post( $post_id );

	if ( false === $activate_builder ) {
		return false;
	}

	// If content already has a section (not as divi/layout block attribute), it means builder is
	// active and activation has to be skipped to avoid nested and unwanted builder structure.
	if ( ! has_block( 'divi/layout', $post_id ) && has_shortcode( $_post->post_content, 'et_pb_section' ) ) {
		return true;
	}

	// `update_post_meta()`'s saved value is run through `stripslashes()` which makes encoded
	// shortcode on layout block's `layoutContent` attributes looses its slashes when being saved.
	// To fix this, If saved content has layout block, add one more slash using `wp_slash()`.
	// NOTE:`$new_old_content` parameter is meant to be used as `update_post_meta()` parameter only
	// {@see https://codex.wordpress.org/Function_Reference/update_post_meta#Character_Escaping}
	$content_has_layout_block = has_block( 'divi/layout', $_post->post_content );
	$new_old_content          = $_post->post_content;

	// Save old content.
	$saved_old_content = get_post_meta( $post_id, '_et_pb_old_content', true );
	$save_old_content  = update_post_meta( $post_id, '_et_pb_old_content', $new_old_content );

	/**
	 * Filters the flag that sets default Content during Builder activation.
	 *
	 * @since 3.29
	 *
	 * @param bool    $is_skip_content_activation TRUE skips the content activation.
	 * @param WP_Post $_post                      The Post.
	 *
	 * @used-by et_builder_wc_init()
	 */
	if ( apply_filters( 'et_builder_skip_content_activation', false, $_post ) ) {
		return true;
	}

	if ( false === $save_old_content && $saved_old_content !== $_post->post_content && '' !== $_post->post_content ) {
		return false;
	}

	$text_module = '' !== $_post->post_content ? '[et_pb_text admin_label="Text"]' . $_post->post_content . '[/et_pb_text]' : '';

	if ( has_block( 'divi/layout', $post_id ) ) {
		$updated_content = et_builder_convert_block_to_shortcode( $_post->post_content );
	} else {
		// Re-format content.
		$updated_content = '[et_pb_section admin_label="section"]
			[et_pb_row admin_label="row"]
				[et_pb_column type="4_4"]' . $text_module . '[/et_pb_column]
			[/et_pb_row]
		[/et_pb_section]';
	}

	// Update post_content.
	$_post->post_content = $updated_content;

	// Update post.
	$update_post = wp_update_post( $_post );

	if ( 0 < $update_post ) {
		setup_postdata( $_post );
	}

	return 0 < $update_post;
}

if ( ! function_exists( 'et_builder_get_font_family' ) ) :
	/**
	 * Load font family.
	 *
	 * @param string $font_name Font name slug.
	 * @param bool   $use_important Whether to use !important in font-family css property.
	 *
	 * @return string
	 */
	function et_builder_get_font_family( $font_name, $use_important = false ) {
		$is_global_font        = in_array( $font_name, array( '--et_global_heading_font', '--et_global_body_font' ), true );
		$font_name             = $is_global_font ? '--et_global_heading_font' === $font_name ? et_get_option( 'heading_font', '' ) : et_get_option( 'body_font', '' ) : $font_name;
		$user_fonts            = et_builder_get_custom_fonts();
		$fonts                 = isset( $user_fonts[ $font_name ] ) ? $user_fonts : et_builder_get_fonts();
		$removed_fonts_mapping = et_builder_old_fonts_mapping();

		$font_style  = '';
		$font_weight = '';

		$font_name_ms = isset( $fonts[ $font_name ] ) && isset( $fonts[ $font_name ]['add_ms_version'] ) ? "'{$font_name} MS', " : '';

		if ( isset( $removed_fonts_mapping[ $font_name ] ) && isset( $removed_fonts_mapping[ $font_name ]['parent_font'] ) ) {
			$font_style = $removed_fonts_mapping[ $font_name ]['styles'];
			$font_name  = $removed_fonts_mapping[ $font_name ]['parent_font'];
		}

		if ( '' !== $font_style ) {
			$is_global_font_weigth = in_array( $font_style, array( '--et_global_heading_font_weight', '--et_global_body_font_weight' ), true );
			$font_weight_value     = $is_global_font_weigth ? '--et_global_heading_font_weight' === $font_style ? et_get_option( 'heading_font_weight', '' ) : et_get_option( 'body_font_weight', '' ) : $font_style;
			$font_weight           = sprintf( ' font-weight: %1$s;', esc_html( $font_style ) );
		}

		$style = sprintf(
			'font-family: \'%1$s\', %5$s%2$s%3$s;%4$s',
			esc_html( $font_name ),
			isset( $fonts[ $font_name ] ) ? et_builder_get_websafe_font_stack( $fonts[ $font_name ]['type'] ) : 'sans-serif',
			( $use_important ? ' !important' : '' ),
			$font_weight,
			$font_name_ms
		);

		return $style;
	}
endif;

if ( ! function_exists( 'et_builder_get_fonts' ) ) :
	/**
	 * Return websafe and google font list.
	 *
	 * @param array $settings {
	 *  Font settings.
	 *  @type string $prepend_standard_fonts Whether to prepend or append websafe fonts in returned list.
	 * }
	 *
	 * @return array
	 */
	function et_builder_get_fonts( $settings = array() ) {
		// Only return websafe fonts if google fonts disabled.
		if ( ! et_core_use_google_fonts() ) {
			return et_builder_get_websafe_fonts();
		}

		$defaults = array(
			'prepend_standard_fonts' => true,
		);

		$settings = wp_parse_args( $settings, $defaults );

		$fonts = $settings['prepend_standard_fonts']
			? array_merge( et_builder_get_websafe_fonts(), et_builder_get_google_fonts() )
			: array_merge( et_builder_get_google_fonts(), et_builder_get_websafe_fonts() );

		ksort( $fonts );

		return $fonts;
	}
endif;

if ( ! function_exists( 'et_builder_get_websafe_font_stack' ) ) :
	/**
	 * Return websafe font stack.
	 *
	 * @param string $type the font stack type.
	 *
	 * @return string
	 */
	function et_builder_get_websafe_font_stack( $type = 'sans-serif' ) {
		$font_stack = $type;

		switch ( $type ) {
			case 'sans-serif':
				$font_stack = 'Helvetica, Arial, Lucida, sans-serif';
				break;
			case 'serif':
				$font_stack = 'Georgia, "Times New Roman", serif';
				break;
			case 'cursive':
				$font_stack = 'cursive';
				break;
		}

		return $font_stack;
	}
endif;

if ( ! function_exists( 'et_builder_get_websafe_fonts' ) ) :
	/**
	 * Return websafe fonts list.
	 */
	function et_builder_get_websafe_fonts() {
		return et_core_get_websafe_fonts();
	}
endif;

if ( ! function_exists( 'et_builder_get_font_weight_list' ) ) :
	/**
	 * Return font weight list.
	 */
	function et_builder_get_font_weight_list() {
		$default_font_weights_list = array(
			'100' => esc_html__( 'Thin', 'et_builder' ),
			'200' => esc_html__( 'Ultra Light', 'et_builder' ),
			'300' => et_builder_i18n( 'Light' ),
			'400' => esc_html__( 'Regular', 'et_builder' ),
			'500' => esc_html__( 'Medium', 'et_builder' ),
			'600' => esc_html__( 'Semi Bold', 'et_builder' ),
			'700' => esc_html__( 'Bold', 'et_builder' ),
			'800' => esc_html__( 'Ultra Bold', 'et_builder' ),
			'900' => esc_html__( 'Heavy', 'et_builder' ),
		);

		return apply_filters( 'et_builder_all_font_weights', $default_font_weights_list );
	}
endif;

/**
 * Retrieve list of uploaded user fonts stored in `et_uploaded_fonts` option.
 *
 * @since 3.0
 *
 * @return array fonts list
 */
if ( ! function_exists( 'et_builder_get_custom_fonts' ) ) :
	/**
	 * Return user uploaded custom fonts.
	 *
	 * @return array
	 */
	function et_builder_get_custom_fonts() {
		$all_custom_fonts = get_option( 'et_uploaded_fonts', array() );

		// Convert any falsey value to empty array to avoid PHP errors.
		if ( ! is_array( $all_custom_fonts ) ) {
			$all_custom_fonts = array();
		}

		return (array) apply_filters( 'et_builder_custom_fonts', $all_custom_fonts );
	}
endif;

/**
 * Return old(removed) fonts mapping.
 *
 * @return array
 */
function et_builder_old_fonts_mapping() {
	return array(
		'Raleway Light'         => array(
			'parent_font' => 'Raleway',
			'styles'      => '300',
		),
		'Roboto Light'          => array(
			'parent_font' => 'Roboto',
			'styles'      => '100',
		),
		'Source Sans Pro Light' => array(
			'parent_font' => 'Source Sans Pro',
			'styles'      => '300',
		),
		'Lato Light'            => array(
			'parent_font' => 'Lato',
			'styles'      => '300',
		),
		'Open Sans Light'       => array(
			'parent_font' => 'Open Sans',
			'styles'      => '300',
		),
	);
}

if ( ! function_exists( 'et_builder_google_fonts_sync' ) ) :
	/**
	 * Sync Google Fonts. Clear font cache every 24 hours.
	 */
	function et_builder_google_fonts_sync() {
		$google_api_key = et_pb_get_google_api_key();

		// Bail early if 'fonts_cache_status' transient is not expired.
		if ( false !== get_transient( 'fonts_cache_status' ) ) {
			return;
		}

		// Bail early if Google API Key is empty or Google Fonts is disabled.
		if ( '' === $google_api_key || ! et_core_use_google_fonts() ) {
			return;
		}

		// Set 'fonts_cache_status' transient to true, marking the font cache update attempt to avoid making the request more than once a day in case of an error.
		set_transient( 'fonts_cache_status', true, 24 * HOUR_IN_SECONDS );

		$google_fonts_api_url  = sprintf( 'https://www.googleapis.com/webfonts/v1/webfonts?key=%1$s', $google_api_key );
		$google_fonts_response = wp_remote_get( esc_url_raw( $google_fonts_api_url ) );

		// Check if the response is an array and we have a valid 200 response, otherwise log an error.
		if ( is_array( $google_fonts_response ) && 200 === $google_fonts_response['response']['code'] ) {
			$google_fonts_json = wp_remote_retrieve_body( $google_fonts_response );
			$google_fonts_json = et_core_parse_google_fonts_json( $google_fonts_json );

			if ( ! empty( $google_fonts_json ) ) {
				// Save Google Fonts Data, if it's not empty.
				update_option( 'et_google_fonts_cache', $google_fonts_json );
			}
		} else {
			et_debug( 'An unkown error has occured while trying to retrieve the fonts from the Google Fonts API. Please ensure your Google API Key is valid and active.' );

			return;
		}
	}
endif;

if ( ! function_exists( 'et_builder_get_google_fonts' ) ) :
	/**
	 * Return google fonts.
	 */
	function et_builder_get_google_fonts() {
		// Google Fonts disabled.
		if ( ! et_core_use_google_fonts() ) {
			return array();
		}

		et_builder_google_fonts_sync();

		$google_fonts_cache = get_option( 'et_google_fonts_cache', array() );
		$google_fonts_cache = is_array( $google_fonts_cache ) ? $google_fonts_cache : et_core_parse_google_fonts_json( $google_fonts_cache );

		if ( ! empty( $google_fonts_cache ) ) {
			// Use cache if it's not empty.
			return apply_filters( 'et_builder_google_fonts', $google_fonts_cache );
		}

		// use hardcoded google fonts as fallback if no cache exists.
		return apply_filters( 'et_builder_google_fonts', et_core_get_saved_google_fonts() );
	}
endif;

/**
 * Use correct conditional tag for compute callback. Compute callback can use actual conditional tag
 * on page load. Compute callback relies on passed conditional tag params for update due to the
 * ajax-admin.php nature.
 *
 * @param string $name conditional tag name.
 * @param array  $conditional_tags all conditional tags params.
 * @return bool  conditional tag value.
 */
function et_fb_conditional_tag( $name, $conditional_tags ) {

	if ( defined( 'DOING_AJAX' ) && isset( $conditional_tags[ $name ] ) ) {
		return 'true' === $conditional_tags[ $name ] ? true : false;
	}

	return is_callable( $name ) ? $name() : false;
}

/**
 * Retrieves the content of saved modules and process the shortcode into array.
 */
function et_fb_get_saved_templates() {
	if ( ! isset( $_POST['et_fb_retrieve_library_modules_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_fb_retrieve_library_modules_nonce'] ), 'et_fb_retrieve_library_modules_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$layout_type     = ! empty( $_POST['et_layout_type'] ) ? sanitize_text_field( $_POST['et_layout_type'] ) : 'layout';
	$module_width    = ! empty( $_POST['et_module_width'] ) && 'module' === $layout_type ? sanitize_text_field( $_POST['et_module_width'] ) : '';
	$is_global       = ! empty( $_POST['et_is_global'] ) ? sanitize_text_field( $_POST['et_is_global'] ) : 'all';
	$specialty_query = ! empty( $_POST['et_specialty_columns'] ) && 'row' === $layout_type ? sanitize_text_field( $_POST['et_specialty_columns'] ) : '0';
	$post_type       = ! empty( $_POST['et_post_type'] ) ? sanitize_text_field( $_POST['et_post_type'] ) : 'post';
	$start_from      = ! empty( $_POST['et_templates_start_page'] ) ? sanitize_text_field( $_POST['et_templates_start_page'] ) : 0;

	if ( et_theme_builder_is_layout_post_type( $post_type ) ) {
		// Treat TB layouts as normal posts when fetching layouts from the library.
		$post_type = 'post';
	}

	if ( 'all' === $is_global ) {
		$templates_data_regular = et_pb_retrieve_templates( $layout_type, $module_width, 'not_global', $specialty_query, $post_type, '', array( $start_from, 25 ) );
		$templates_data_global  = et_pb_retrieve_templates( $layout_type, $module_width, 'global', $specialty_query, $post_type, '', array( $start_from, 25 ) );
		$templates_data         = array_merge( $templates_data_regular, $templates_data_global );
	} else {
		$templates_data = et_pb_retrieve_templates( $layout_type, $module_width, $is_global, $specialty_query, $post_type, array( $start_from, 50 ) );
	}

	$templates_data_processed = $templates_data;
	$next_page                = 'none';

	if ( 0 !== $start_from && empty( $templates_data ) ) {
		$templates_data_processed = array();
	} else {
		if ( empty( $templates_data ) ) {
			$templates_data_processed = array( 'error' => esc_html__( 'You have not saved any items to your Divi Library yet. Once an item has been saved to your library, it will appear here for easy use.', 'et_builder' ) );
		} else {
			foreach ( $templates_data as $index => $data ) {
				$templates_data_processed[ $index ]['shortcode'] = et_fb_process_shortcode( $data['shortcode'] );

				if ( 'global' === $templates_data_processed[ $index ]['is_global'] && 'module' === $templates_data_processed[ $index ]['layout_type'] && is_array( $templates_data_processed[ $index ]['shortcode'] ) ) {
					$templates_data_processed[ $index ]['shortcode'][0]['unsyncedGlobalSettings'] = $templates_data_processed[ $index ]['unsynced_options'];

					if ( empty( $templates_data_processed[ $index ]['unsynced_options'] ) && isset( $templates_data_processed[ $index ]['shortcode'][0]['attrs']['saved_tabs'] ) && 'all' !== $templates_data_processed[ $index ]['shortcode'][0]['attrs']['saved_tabs'] ) {
						$templates_data_processed[ $index ]['shortcode'][0]['unsyncedGlobalSettings'] = et_pb_get_unsynced_legacy_options( $post_type, $templates_data_processed[ $index ]['shortcode'][0] );
					}
				}
			}
			$next_page = 'all' === $is_global ? $start_from + 25 : $start_from + 50;
		}
	}

	$json_templates = wp_json_encode(
		array(
			'templates_data' => $templates_data_processed,
			'next_page'      => $next_page,
		)
	);

	die( et_core_esc_previously( $json_templates ) );
}
add_action( 'wp_ajax_et_fb_get_saved_templates', 'et_fb_get_saved_templates' );

/**
 * Retrieves posts list that builder enabled.
 */
function et_fb_get_posts_list() {
	et_core_security_check( 'edit_posts', 'et_fb_get_posts_list' );

	$post_types = et_get_registered_post_type_options();
	$post_type  = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : false;

	if ( empty( $post_type ) || ! isset( $post_types[ $post_type ] ) ) {
		wp_send_json_error();
	}

	$posts_list = array();

	$query = new ET_Core_Post_Query( $post_type );

	$posts = $query->run(
		array(
			'post_status' => array( 'draft', 'publish', 'pending' ),
		)
	);

	$_utils = ET_Core_Data_Utils::instance();

	$posts = $_utils->array_sort_by( is_array( $posts ) ? $posts : array( $posts ), 'post_title' );

	if ( empty( $posts ) ) {
		wp_send_json_error();
	}

	foreach ( $posts as $post ) {
		// Check if page builder is activated.
		if ( ! et_pb_is_pagebuilder_used( $post->ID ) ) {
			continue;
		}

		// Only include posts that the user is allowed to edit.
		if ( ! current_user_can( 'edit_post', $post->ID ) ) {
			continue;
		}

		// Skip for post has no title.
		if ( empty( $post->post_title ) ) {
			continue;
		}

		$posts_list[ $post->ID ] = array(
			'id'    => $post->ID,
			'title' => $post->post_title,
			'link'  => array(
				'vb'  => et_fb_get_vb_url( get_permalink( $post->ID ) ),
				'bfb' => add_query_arg(
					array(
						'post'           => $post->ID,
						'action'         => 'edit',
						'classic-editor' => '1',
					),
					admin_url( 'post.php' )
				),
			),
		);
	}

	wp_send_json_success( $posts_list );
}
add_action( 'wp_ajax_et_fb_get_posts_list', 'et_fb_get_posts_list' );

/**
 * Return supported font formats.
 *
 * @return mixed|void
 */
function et_pb_get_supported_font_formats() {
	return apply_filters( 'et_pb_supported_font_formats', array( 'ttf', 'otf' ) );
}

/***
 * AJAX Callback :: Process uploaded custom font.
 */
function et_pb_process_custom_font() {
	et_core_security_check( 'upload_files', 'et_fb_upload_font_nonce' );

	// action "add" or "remove".
	$action = ! empty( $_POST['et_pb_font_action'] ) ? sanitize_text_field( $_POST['et_pb_font_action'] ) : 'save';

	if ( 'add' === $action ) {
		$supported_font_files           = et_pb_get_supported_font_formats();
		$custom_font_name               = ! empty( $_POST['et_pb_font_name'] ) ? sanitize_text_field( $_POST['et_pb_font_name'] ) : '';
		$custom_font_settings           = ! empty( $_POST['et_pb_font_settings'] ) ? sanitize_text_field( $_POST['et_pb_font_settings'] ) : '';
		$custom_font_settings_processed = '' === $custom_font_settings ? array() : json_decode( str_replace( '\\', '', $custom_font_settings ), true );
		$fonts_array                    = array();

		foreach ( $supported_font_files as $format ) {
			if ( isset( $_FILES[ 'et_pb_font_file_' . $format ] ) ) {
				// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- This is file input.
				$fonts_array[ $format ] = $_FILES[ 'et_pb_font_file_' . $format ];
			}
		}

		die( wp_json_encode( et_pb_add_font( $fonts_array, $custom_font_name, $custom_font_settings_processed ) ) );
	} elseif ( 'remove' === $action ) {
		$font_slug = ! empty( $_POST['et_pb_font_name'] ) ? sanitize_text_field( $_POST['et_pb_font_name'] ) : '';
		die( wp_json_encode( et_pb_remove_font( $font_slug ) ) );
	}
}

add_action( 'wp_ajax_et_pb_process_custom_font', 'et_pb_process_custom_font' );

/**
 * Drag and Droploader :: Process Media
 */
if ( ! function_exists( 'et_builder_droploader_process' ) ) :
	/**
	 * Save droploaded images to WP Media Library.
	 */
	function et_builder_droploader_process() {
		et_core_security_check( 'upload_files', 'et_builder_droploader_process_nonce' );

		$post_id = ! empty( $_POST['post_id'] ) ? (int) $_POST['post_id'] : '';

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			die( -1 );
		}

		et_core_security_check( 'edit_posts', 'et_builder_droploader_process_nonce' );

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$attachment_id = media_handle_upload( 'file', (int) $_POST['post_id'] );

		if ( is_wp_error( $attachment_id ) ) {
			wp_send_json_error( $attachment_id->get_error_message() );
		}

		wp_send_json_success( $attachment_id );
	}
endif;
add_action( 'wp_ajax_et_builder_droploader_process', 'et_builder_droploader_process' );

/**
 * Add allowed mime types and file extensions for font files.
 *
 * @return array
 */
function et_pb_filter_upload_mimes_custom_fonts() {
	return array(
		'otf'   => 'application/x-font-opentype',
		'ttf'   => 'application/x-font-ttf',
		'woff'  => 'application/font-woff',
		'woff2' => 'application/font-woff2',
		'eot'   => 'application/vnd.ms-fontobject',
	);
}

/**
 * Save the font-file.
 *
 * @param array  $font_files font files.
 * @param string $font_name font name.
 * @param array  $font_settings font settings.
 *
 * @return array
 */
function et_pb_add_font( $font_files, $font_name, $font_settings ) {
	if ( ! isset( $font_files ) || empty( $font_files ) ) {
		return array( 'error' => esc_html__( 'No Font File Provided', 'et_builder' ) );
	}

	// remove all special characters from the font name.
	$font_name = preg_replace( '/[^A-Za-z0-9\s\_-]/', '', $font_name );

	if ( '' === $font_name ) {
		return array( 'error' => esc_html__( 'Font Name Cannot be Empty and Cannot Contain Special Characters', 'et_builder' ) );
	}

	$google_fonts     = et_builder_get_google_fonts();
	$all_custom_fonts = get_option( 'et_uploaded_fonts', array() );

	// Don't allow to add fonts with the names which already used by User Fonts or Google Fonts.
	if ( isset( $all_custom_fonts[ $font_name ] ) || isset( $google_fonts[ $font_name ] ) ) {
		return array( 'error' => esc_html__( 'Font With This Name Already Exists. Please Use a Different Name', 'et_builder' ) );
	}

	// set the upload Directory for builder font files.
	add_filter( 'upload_dir', 'et_pb_set_fonts_upload_dir' );

	// Set the upload_mimes filter before uploading font file.
	add_filter( 'upload_mimes', 'et_pb_filter_upload_mimes_custom_fonts' );

	$uploaded_files_error = '';
	$uploaded_files       = array(
		'font_file' => array(),
		'font_url'  => array(),
	);

	foreach ( $font_files as $ext => $font_file ) {
		// Try to upload font file.
		// phpcs:ignore ET.Sniffs.DangerousFunctions.ET_handle_upload -- test_type is enabled and proper type and extension checking are implemented.
		$upload = wp_handle_upload(
			$font_file,
			array(
				'test_size' => false,
				'test_form' => false,
				'mimes'     => et_pb_filter_upload_mimes_custom_fonts(),
			)
		);

		// try with different MIME types if uploading .otf file and error occurs.
		if ( 'otf' === $ext && ! empty( $upload['error'] ) ) {
			foreach ( array( 'application/x-font-ttf', 'application/vnd.ms-opentype' ) as $mime_type ) {
				if ( ! empty( $upload['error'] ) ) {
					// phpcs:ignore ET.Sniffs.DangerousFunctions.ET_handle_upload -- test_type is enabled and proper type and extension checking are implemented.
					$upload = wp_handle_upload(
						$font_file,
						array(
							'test_size' => false,
							'test_form' => false,
							'mimes'     => array(
								'otf' => $mime_type,
							),
						)
					);
				}
			}
		}

		if ( ! empty( $upload['error'] ) ) {
			$uploaded_files_error = $upload['error'];
		} else {
			$uploaded_files['font_file'][ $ext ] = esc_url( $upload['file'] );
			$uploaded_files['font_url'][ $ext ]  = esc_url( $upload['url'] );
		}
	}

	// Reset the upload Directory after uploading font file.
	remove_filter( 'upload_dir', 'et_pb_set_fonts_upload_dir' );

	// Reset the upload_mimes filter after uploading font file.
	remove_filter( 'upload_mimes', 'et_pb_filter_upload_mimes_custom_fonts' );

	// return error if no files were uploaded.
	if ( empty( $uploaded_files['font_file'] ) && '' !== $uploaded_files_error ) {
		return array( 'error' => $uploaded_files_error );
	}

	// organize uploaded files.
	$all_custom_fonts[ $font_name ] = array(
		'font_file' => $uploaded_files['font_file'],
		'font_url'  => $uploaded_files['font_url'],
	);

	if ( ! empty( $font_settings ) ) {
		$all_custom_fonts[ $font_name ]['styles'] = ! isset( $font_settings['font_weights'] ) || 'all' === $font_settings['font_weights'] ? '100,200,300,400,500,600,700,800,900' : $font_settings['font_weights'];
		$all_custom_fonts[ $font_name ]['type']   = isset( $font_settings['generic_family'] ) ? $font_settings['generic_family'] : 'serif';
	}

	update_option( 'et_uploaded_fonts', $all_custom_fonts );
	// Need to update cached assets because custom fonts are included in static helpers.
	et_fb_delete_builder_assets();

	return array(
		'error'         => array(),
		'success'       => true,
		'uploaded_font' => $font_name,
		'updated_fonts' => $all_custom_fonts,
	);
}

/**
 * Remove custom font.
 *
 * @param string $font_name Font name to remove.
 *
 * @return array
 */
function et_pb_remove_font( $font_name ) {
	if ( '' === $font_name ) {
		return array( 'error' => esc_html__( 'Font Name Cannot be Empty', 'et_builder' ) );
	}

	$all_custom_fonts = get_option( 'et_uploaded_fonts', array() );

	if ( ! isset( $all_custom_fonts[ $font_name ] ) ) {
		return array( 'error' => esc_html__( 'Font Does not Exist', 'et_builder' ) );
	}

	// remove all uploaded font files if array.
	if ( is_array( $all_custom_fonts[ $font_name ]['font_file'] ) ) {
		foreach ( $all_custom_fonts[ $font_name ]['font_file'] as $ext => $font_file ) {
			et_pb_safe_unlink_font_file( $font_file );
		}
	} else {
		$font_file = $all_custom_fonts[ $font_name ]['font_file'];
		et_pb_safe_unlink_font_file( $font_file );
	}

	unset( $all_custom_fonts[ $font_name ] );

	update_option( 'et_uploaded_fonts', $all_custom_fonts );
	// Need to update cached assets because custom fonts are included in static helpers.
	et_fb_delete_builder_assets();

	return array(
		'error'         => array(),
		'success'       => true,
		'updated_fonts' => $all_custom_fonts,
	);
}

/**
 * Delete a font file.
 *
 * @param string $font_file font file path.
 *
 * @return bool
 */
function et_pb_safe_unlink_font_file( $font_file ) {
	$data_utils = ET_Core_Data_Utils::instance();

	// get the extensions from our list of allowed font ext/mimes.
	$valid_font_exts = array_keys( et_pb_filter_upload_mimes_custom_fonts() );

	// set the upload Directory for builder font files, so we can retrieve the proper font upload dir info.
	add_filter( 'upload_dir', 'et_pb_set_fonts_upload_dir' );

	$wp_upload_dir_array = wp_get_upload_dir();

	// get the absolute path to the et fonts upload dir.
	$et_fonts_dir = $wp_upload_dir_array['path'];

	// reset the upload Directory after getting the upload dir.
	remove_filter( 'upload_dir', 'et_pb_set_fonts_upload_dir' );

	// expand all symbolic links and resolve references to /./, /../ and extra / characters in the input path and return the canonicalized absolute pathname.
	$file_realpath = realpath( $font_file );

	// get information about the path.
	$file_pathinfo = pathinfo( $font_file );

	// Build the full file path based on the parsed pathinfo pieces.
	$file_pathinfo_filename = $file_pathinfo['dirname'] . '/' . $file_pathinfo['basename'];

	// make sure the realpath matches the parsed pathinfo file path, so there is no funny business.
	if ( $data_utils->normalize_path( $file_realpath ) !== $data_utils->normalize_path( $file_pathinfo_filename ) ) {
		return false;
	}

	// make sure the font file to be deleted is an actual font file extension (not an arbitrarty PHP file somehow for example).
	if ( ! in_array( $file_pathinfo['extension'], $valid_font_exts, true ) ) {
		return false;
	}

	// the proper upload dir for fonts.
	$proper_font_file_path = $et_fonts_dir . '/' . $file_pathinfo['basename'];

	// make sure the file is located in the proper fonts upload dir.
	if ( $data_utils->normalize_path( $file_realpath ) !== $data_utils->normalize_path( $proper_font_file_path ) ) {
		return false;
	}

	// now that all checks have passed, the file can be safely deleted.
	return unlink( $file_realpath );
}

/**
 * Set fonts upload dir.
 *
 * @param array $directory directory path.
 *
 * @return mixed
 */
function et_pb_set_fonts_upload_dir( $directory ) {
	$directory['path']   = $directory['basedir'] . '/et-fonts';
	$directory['url']    = $directory['baseurl'] . '/et-fonts';
	$directory['subdir'] = '/et-fonts';

	return $directory;
}

/**
 * Return unsynced global settings,
 *
 * @param string $post_type Post type.
 * @param array  $shortcode_data Shortcode data.
 *
 * @return array
 */
function et_pb_get_unsynced_legacy_options( $post_type, $shortcode_data ) {
	if ( ! isset( $shortcode_data['attrs']['saved_tabs'] ) && 'all' === $shortcode_data['attrs']['saved_tabs'] ) {
		return array();
	}

	// get all options.
	$general_fields  = ET_Builder_Element::get_general_fields( $post_type, 'all', $shortcode_data['type'] );
	$advanced_fields = ET_Builder_Element::get_advanced_fields( $post_type, 'all', $shortcode_data['type'] );
	$css_fields      = ET_Builder_Element::get_custom_css_fields( $post_type, 'all', $shortcode_data['type'] );
	$saved_fields    = array_keys( $shortcode_data['attrs'] );

	// content fields should never be included into unsynced options. We use different key for the content options.
	$saved_fields[] = 'content';
	$saved_fields[] = 'raw_content';

	$all_fields = array_merge( array_keys( $general_fields ), array_keys( $advanced_fields ), array_keys( $css_fields ) );

	// compare all options with saved options to get array of unsynced ones.
	$unsynced_options = array_diff( $all_fields, $saved_fields );

	if ( false === strpos( $shortcode_data['attrs']['saved_tabs'], 'general' ) ) {
		$unsynced_options[] = 'et_pb_content_field';
	}

	return $unsynced_options;
}

/**
 * Prepare the ssl link for FB.
 *
 * @param string $link The link to be be prepared for ssl.
 *
 * @return string|string[]
 */
function et_fb_prepare_ssl_link( $link ) {
	// replace http:// with https:// if FORCE_SSL_ADMIN option enabled.
	if ( defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ) {
		return str_replace( 'http://', 'https://', $link );
	}

	return $link;
}

if ( ! function_exists( 'et_fb_get_builder_url' ) ) :
	/**
	 * Create a VB/BFB url.
	 *
	 * @param string $url Post url.
	 * @param string $builder 'vb' or 'bfb'.
	 * @param bool   $is_new_page Whether the page is new or not.
	 * @param bool   $custom_page_id page id.
	 *
	 * @return string.
	 */
	function et_fb_get_builder_url( $url = false, $builder = 'vb', $is_new_page = false, $custom_page_id = false ) {
		$args = array(
			'et_fb'     => '1',
			'et_bfb'    => 'bfb' === $builder ? '1' : false,
			'PageSpeed' => 'off',
		);

		if ( 'bfb' === $builder && $is_new_page ) {
			global $post;

			$duplicate_options  = get_user_meta( get_current_user_id(), 'pll_duplicate_content', true );
			$duplicate_content  = ! empty( $duplicate_options ) && ! empty( $duplicate_options[ $post->post_type ] );
			$duplicate_fallback = (int) get_option( 'page_for_posts' ) === (int) $custom_page_id ? (int) $custom_page_id : 'empty';

			// phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
			$from_post_id = isset( $_GET['from_post'] ) ? (int) sanitize_text_field( $_GET['from_post'] ) : false;

			$args['from_post']   = $duplicate_content && $from_post_id ? $from_post_id : $duplicate_fallback;
			$args['is_new_page'] = '1';

			if ( $custom_page_id ) {
				$args['custom_page_id'] = $custom_page_id;
			}
		}

		// Additional info need to be appended via query strings if current request is used to get
		// BFB URL and the given page's custom post type has its publicly_queryable setting is set
		// to false. These additional information is be used to deterimined whether the BFB page
		// request needs to modify its global $query and rewrite_rule configuration so correct BFB
		// page can be rendered for valid user.
		if ( 'bfb' === $builder && ! $url ) {
			$post_id   = get_the_ID();
			$post_type = get_post_type();

			// 'page' and 'et_pb_layout' are not queryable so post type needs to be checked against
			// third party post types first to avoid false positive for these default post types.
			$is_third_party_post_type = in_array( $post_type, et_builder_get_third_party_post_types(), true );
			$is_unqueryable           = $is_third_party_post_type && in_array(
				$post_type,
				get_post_types( array( 'publicly_queryable' => false ) ),
				true
			);

			// These post id & post type query strings should only be added if current post type
			// has false publicly_queryable setting.
			if ( $post_id && $post_type && is_user_logged_in() && $is_unqueryable ) {
				$args['et_post_id']         = $post_id;
				$args['et_post_id_nonce']   = wp_create_nonce( 'et_post_id_' . $post_id );
				$args['et_post_type']       = $post_type;
				$args['et_post_type_nonce'] = wp_create_nonce( 'et_post_type_' . $post_type );
			}
		}

		return add_query_arg( $args, et_fb_prepare_ssl_link( $url ? $url : get_the_permalink() ) );
	}
endif;

if ( ! function_exists( 'et_fb_get_vb_url' ) ) :
	/**
	 * Create a VB url.
	 *
	 * @param string $url Post url.
	 * @return string.
	 */
	function et_fb_get_vb_url( $url = false ) {
		return et_fb_get_builder_url( $url );
	}
endif;

if ( ! function_exists( 'et_fb_get_bfb_url' ) ) :
	/**
	 * Create a BFB url.
	 *
	 * @param string $url Post url.
	 * @param bool   $is_new_page Whether the page is new or not.
	 * @param bool   $custom_page_id page id.
	 *
	 * @return string.
	 */
	function et_fb_get_bfb_url( $url = false, $is_new_page = false, $custom_page_id = false ) {
		return et_fb_get_builder_url( $url, 'bfb', $is_new_page, $custom_page_id );
	}
endif;

if ( ! function_exists( 'et_builder_options' ) ) :
	/**
	 * Filterable options for backend and visual builder. Designed to be filtered
	 * by theme/plugin since builder is shared accross Divi, Extra, and Divi Builder.
	 *
	 * @return array builder options values
	 */
	function et_builder_options() {
		return apply_filters(
			'et_builder_options',
			array(
				'all_buttons_icon' => 'yes', // Default appearance of button icon.
			)
		);
	}
endif;

if ( ! function_exists( 'et_builder_option' ) ) :
	/**
	 * Get specific builder option (fetched from et_builder_options()).
	 *
	 * @param string $name option name.
	 * @return mixed builder option value
	 */
	function et_builder_option( $name ) {
		$options = et_builder_options();

		$option = isset( $options[ $name ] ) ? $options[ $name ] : false;

		return apply_filters( "et_builder_option_{$name}", $option );
	}
endif;

/**
 * Pass thru semantical previously escaped acknowledgement
 *
 * @deprecated {@see et_core_esc_previously()}
 *
 * @since 3.17.1 Deprecated
 *
 * @param string $passthru value being passed through.
 * @return string
 */
function et_esc_previously( $passthru ) {
	et_debug( "You're Doing It Wrong! Attempted to call " . __FUNCTION__ . '(), use et_core_esc_previously() instead.' );
	return $passthru;
}

/**
 * Pass thru semantical escaped by WordPress core acknowledgement
 *
 * @deprecated {@see et_core_esc_wp()}
 *
 * @since 3.17.1 Deprecated
 *
 * @param string $passthru value being passed through.
 *
 * @return string
 */
function et_esc_wp( $passthru ) {
	et_debug( "You're Doing It Wrong! Attempted to call " . __FUNCTION__ . '(), use et_core_esc_wp() instead.' );
	return $passthru;
}

/**
 * Pass thru semantical intentionally unescaped acknowledgement.
 *
 * @deprecated {@see et_core_intentionally_unescaped()}
 *
 * @since 3.17.1 Deprecated
 *
 * @param string $passthru value being passed through.
 * @param string $excuse excuse the value is allowed to be unescaped.
 * @return string
 */
function et_intentionally_unescaped( $passthru, $excuse ) {
	et_debug( "You're Doing It Wrong! Attempted to call " . __FUNCTION__ . '(), use et_core_intentionally_unescaped() instead.' );

	// Add valid excuses as they arise.
	$valid_excuses = array(
		'cap_based_sanitized',
		'fixed_string',
		'react_jsx',
		'underscore_template',
	);

	if ( ! in_array( $excuse, $valid_excuses, true ) ) {
		et_debug( "You're Doing It Wrong! This is not a valid excuse to not escape the passed value." );
	}

	return $passthru;
}

/**
 * Sanitize value depending on user capability.
 *
 * @deprecated {@see et_core_sanitize_value_by_cap()}
 *
 * @since 3.17.1 Deprecated
 *
 *  @param string   $passthru value being passed through.
 *  @param callable $sanitize_function santization function.
 *  @param string   $cap WP capability name.
 *
 * @return string value being passed through.
 */
function et_sanitize_value_by_cap( $passthru, $sanitize_function = 'et_sanitize_html_input_text', $cap = 'unfiltered_html' ) {
	et_debug( "You're Doing It Wrong! Attempted to call " . __FUNCTION__ . '(), use et_core_sanitize_value_by_cap() instead.' );

	if ( ! current_user_can( $cap ) ) {
		$passthru = $sanitize_function( $passthru );
	}

	return $passthru;
}

/**
 * Pass thru semantical intentionally unsanitized acknowledgement.
 *
 * @deprecated {@see et_core_intentinally_unsanitized()}
 *
 * @since 3.17.1 Deprecated
 *
 * @param string $passthru value being passed through.
 * @param string $excuse excuse the value is allowed to be unsanitized.
 * @return string
 */
function et_intentionally_unsanitized( $passthru, $excuse ) {
	et_debug( "You're Doing It Wrong! Attempted to call " . __FUNCTION__ . '(), use et_core_intentionally_unsanitized() instead.' );

	// Add valid excuses as they arise.
	$valid_excuses = array();

	if ( ! in_array( $excuse, $valid_excuses, true ) ) {
		et_debug( "You're Doing It Wrong! This is not a valid excuse to not sanitize the passed value." );
	}

	return $passthru;
}

/**
 * Prevent delimiter-separated string from having duplicate item.
 *
 * @param string $string_list delimiter-separated string.
 * @param string $delimiter delimiter.
 * @return string filtered delimiter-separated string.
 */
function et_prevent_duplicate_item( $string_list, $delimiter ) {
	$list = explode( $delimiter, $string_list );

	return implode( $delimiter, array_unique( $list ) );
}

/**
 * Determining whether unminified scripts should be loaded or not.
 *
 * @since 4.6.2 Removes static $should_load to ensure it's filtered with latest value.
 *
 * @return bool
 *
 * @deprecated ??
 */
function et_load_unminified_scripts() {
	$is_script_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

	return apply_filters( 'et_load_unminified_scripts', $is_script_debug );
}

/**
 * Determining whether unminified styles should be loaded or not
 *
 * @since 4.6.2 Removes static $should_load to ensure it's filtered with latest value.
 *
 * @deprecated ??
 */
function et_load_unminified_styles() {
	$is_script_debug = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG;

	return apply_filters( 'et_load_unminified_styles', $is_script_debug );
}

/**
 * Enable / Disable classic editor based on saved option in Theme Options page.
 * Only applies to versions of WordPress that have the Gutenberg editor.
 *
 * @since 3.18
 *
 * @param bool $enable Whether to enable or disable.
 *
 * @return bool
 */
function et_builder_enable_classic_editor( $enable ) {
	if ( 'on' === et_get_option( 'et_enable_classic_editor', 'off' ) ) {
		return true;
	}

	return $enable;
}
if ( version_compare( $GLOBALS['wp_version'], '5.0-beta', '>=' ) ) {
	add_filter( 'et_builder_enable_classic_editor', 'et_builder_enable_classic_editor' );
}

/**
 * Check whether the BFB is enabled.
 *
 * @since 3.18
 *
 * @return bool
 */
function et_builder_bfb_enabled() {
	return apply_filters( 'et_builder_bfb_enabled', false );
}

/**
 * Check whether BFB is activated for this site or not.
 *
 * @since 3.28
 *
 * @return bool
 */
function et_builder_bfb_activated() {
	$bfb_settings = get_option( 'et_bfb_settings' );
	$enabled      = isset( $bfb_settings['enable_bfb'] ) && 'on' === $bfb_settings['enable_bfb'];
	return $enabled;
}

/**
 * Check whether the VB is loaded through TB.
 *
 * @since 4.0
 *
 * @return bool
 */
function et_builder_tb_enabled() {
	// Layout Block uses abstracted visual builder on modal originally introduced in TB. However,
	// TB needs different Divi capability, hence adjust it for Layout Block Builder.
	$is_layout_block    = ET_GB_Block_Layout::is_layout_block_preview();
	$builder_capability = $is_layout_block ? 'use_visual_builder' : 'theme_builder';

	// phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
	return et_core_is_fb_enabled() && et_pb_is_allowed( $builder_capability ) && isset( $_GET['et_tb'] ) && '1' === $_GET['et_tb'];
}

/**
 * Check if the current screen is the Theme Builder administration screen.
 *
 * @since 4.0
 *
 * @return bool
 */
function et_builder_is_tb_admin_screen() {
	global $pagenow;

	// phpcs:ignore WordPress.Security.NonceVerification -- this is generic read-only, bool returning helper function, and not a state changing action that is susceptible to CSRF attack.
	return is_admin() && 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'et_theme_builder' === $_GET['page'];
}

/**
 * Check if the current screen is the Divi Onboarding administration screen.
 *
 * @since ??
 *
 * @return bool
 */
function et_builder_is_et_onboarding_page() {
	// phpcs:ignore WordPress.Security.NonceVerification -- this is generic read-only, bool returning helper function, and not a state changing action that is susceptible to CSRF attack.
	return is_admin() && isset( $_GET['page'] ) && 'et_onboarding' === $_GET['page'];
}

if ( ! function_exists( 'et_builder_filter_bfb_enabled' ) ) :
	/**
	 * Theme implementation for BFB enabled check.
	 *
	 * @since 3.18
	 *
	 * @return bool
	 */
	function et_builder_filter_bfb_enabled() {
		global $pagenow;

		$enabled = et_builder_bfb_activated();

		// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		if ( is_admin() && ! in_array( $pagenow, array( 'post.php', 'post-new.php', 'admin-ajax.php' ), true ) ) {
			$enabled = false;
		} elseif ( ! is_admin() && ! isset( $_GET['et_bfb'] ) ) {
			$enabled = false;
		} elseif ( ! et_pb_is_allowed( 'use_visual_builder' ) ) {
			$enabled = false;
		}
		// phpcs:enable

		return $enabled;
	}
endif;

if ( ! function_exists( 'et_builder_is_fresh_install' ) ) :
	/**
	 * Get whether the builder is freshly installed.
	 *
	 * @since 3.18
	 *
	 * @return bool
	 */
	function et_builder_is_fresh_install() {
		return apply_filters( 'et_builder_is_fresh_install', false );
	}
endif;

if ( ! function_exists( 'et_builder_filter_is_fresh_install' ) ) :
	/**
	 * Theme implementation for fresh install check.
	 *
	 * @since 3.18
	 *
	 * @return bool
	 */
	function et_builder_filter_is_fresh_install() {
		global $shortname;

		return false === et_get_option( $shortname . '_logo' );
	}
endif;

/**
 * Determine whether current request is AJAX request for loading BB data
 *
 * @since 3.28
 *
 * @todo remove & replace this function with `et_builder_is_loading_data()` once PR #6325 is merged
 *
 * @return bool
 */
function et_builder_is_loading_bb_data() {
	// phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
	return isset( $_POST['action'] ) && in_array( $_POST['action'], array( 'et_pb_get_backbone_templates', 'et_pb_get_backbone_template' ), true );
}

/**
 * Determine whether current request is classic builder (BB) edit page.
 *
 * @since 3.28
 *
 * @return bool
 */
function et_builder_is_bb_page() {
	// BB Is definitely on backend.
	if ( ! is_admin() ) {
		return false;
	}

	// BB page is on either post new or edit post page in backend.
	global $pagenow;

	if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
		return false;
	}

	// If BFB is activated, this is definitely not BB page.
	if ( et_builder_bfb_enabled() ) {
		return false;
	}

	// Check if current post type has builder activated.
	// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
	if ( 'post-new.php' === $pagenow ) {
		$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : 'post';
	} else {
		$post_id   = isset( $_GET['post'] ) ? sanitize_text_field( $_GET['post'] ) : false;
		$post_type = get_post_type( $post_id );
	}
	// phpcs:enable

	return et_builder_enabled_for_post_type( $post_type );
}

if ( ! function_exists( 'et_builder_toggle_bfb' ) ) :
	/**
	 * Toggle BFB.
	 *
	 * @since 3.18
	 *
	 * @param bool $enable Whether to enable or disable bfb.
	 *
	 * @return void
	 */
	function et_builder_toggle_bfb( $enable ) {
		do_action( 'et_builder_toggle_bfb', $enable );
	}
endif;

if ( ! function_exists( 'et_builder_action_toggle_bfb' ) ) :
	/**
	 * Theme implementation for BFB toggle.
	 *
	 * @since 3.18
	 *
	 * @param bool $enable Whether to enable or disable BFB.
	 *
	 * @return void
	 */
	function et_builder_action_toggle_bfb( $enable ) {
		$bfb_value = $enable ? 'on' : 'off';

		et_update_option( '', $bfb_value, true, 'et_bfb_settings', 'enable_bfb' );
	}
endif;

if ( ! function_exists( 'et_builder_show_bfb_welcome_modal' ) ) :
	/**
	 * Show the BFB welcome modal.
	 *
	 * @since 3.18
	 *
	 * @return void
	 */
	function et_builder_show_bfb_welcome_modal() {
		global $pagenow;

		// Cancel if BFB is not enabled yet.
		if ( ! et_builder_bfb_enabled() ) {
			return;
		}

		// Cancel if current request is not editing screen.
		if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		// Cancel if current edit screen use Gutenberg. `use_block_editor_for_post_type()` was added
		// after v5.0 so check for its existance first in case current WP version is below 5.0.
		if ( function_exists( 'use_block_editor_for_post_type' ) && use_block_editor_for_post_type( get_post_type() ) ) {
			return;
		}

		// Cancel if current edit screen doesn't use builder.
		if ( ! et_pb_is_pagebuilder_used() ) {
			return;
		}

		// Cancel if assigned transient doesn't exist.
		if ( ! get_transient( 'et_builder_show_bfb_welcome_modal' ) ) {
			return;
		}

		// Clear Builder assets cache to avoid double reloading of BFB after theme update.
		et_fb_delete_builder_assets();

		// Clear Builder assets cache to avoid double reloading of BFB after theme update.
		et_fb_delete_builder_assets();

		delete_transient( 'et_builder_show_bfb_welcome_modal' );
		?>
		<div class="et-core-modal-overlay et-builder-bfb-welcome-modal" style="z-index: 9999999999;">
			<div class="et-core-modal">
				<div class="et-core-modal-header">
					<h3 class="et-core-modal-title"><?php esc_html_e( 'Welcome To The New Builder', 'et_builder' ); ?></h3>
				</div>

				<div class="et-core-modal-content">
					<p><?php esc_html_e( 'You are now using the latest Divi Builder experience! This new version of the builder comes with many interface enhancements that were previously only available in the Visual Builder. It also improves performance and takes advantage of the latest builder technology. You can still switch back to the Classic Builder in your Divi Theme Options, however, we recommend familiarizing yourself with the new version and everything it has to offer.', 'et_builder' ); ?></p>
					<p><a href="https://www.elegantthemes.com/blog/theme-releases/introducing-the-new-divi-builder-experience" target="_blank"><?php esc_html_e( 'Learn more about the new experience here.', 'et_builder' ); ?></a></p>
				</div>

				<div class="et_pb_prompt_buttons">
					<a href="#" class="et-core-modal-action" data-et-core-modal="close"><?php esc_html_e( 'Start Building', 'et_builder' ); ?></a>
				</div>
			</div>
		</div> );
		<script>
			jQuery(function() {
				etCore.modalOpen(jQuery('.et-builder-bfb-welcome-modal').first());
			});
		</script>
		<?php
	}
endif;
add_action( 'admin_footer', 'et_builder_show_bfb_welcome_modal' );

if ( ! function_exists( 'et_builder_prepare_bfb' ) ) :
	/**
	 * Maybe queue BFB opt-in modal.
	 *
	 * @since 3.18
	 *
	 * @return void
	 */
	function et_builder_prepare_bfb() {
		$bfb_settings = get_option( 'et_bfb_settings' );
		$enabled      = isset( $bfb_settings['enable_bfb'] ) && 'on' === $bfb_settings['enable_bfb'];
		$toggled      = isset( $bfb_settings['toggle_bfb'] ) && 'on' === $bfb_settings['toggle_bfb'];

		if ( $enabled || $toggled ) {
			return;
		}

		// Enable BFB for all users.
		et_builder_toggle_bfb( true );

		// set the flag to not force toggle BFB more than once.
		et_update_option( '', 'on', true, 'et_bfb_settings', 'toggle_bfb' );

		set_transient( 'et_builder_show_bfb_welcome_modal', true, 0 );
	}
endif;
add_action( 'after_switch_theme', 'et_builder_prepare_bfb' );
add_action( 'upgrader_process_complete', 'et_builder_prepare_bfb' );
add_action( 'activated_plugin', 'et_builder_prepare_bfb', 10, 0 );
add_action( 'deactivated_plugin', 'et_builder_prepare_bfb', 10, 0 );

/**
 * Add the divi builder body class.
 *
 * @param array $classes body classes.
 *
 * @return array
 */
function et_builder_add_body_class( $classes ) {
	$classes[] = 'et-db';

	return $classes;
}
add_filter( 'body_class', 'et_builder_add_body_class' );

/**
 * Add builder inner content wrapper classes.
 *
 * @since 3.10
 *
 * @param array $classes css classes list.
 *
 * @return array
 */
function et_builder_add_builder_inner_content_class( $classes ) {
	$page_custom_gutter = get_post_meta( get_the_ID(), '_et_pb_gutter_width', true );
	$valid_gutter_width = array( '1', '2', '3', '4' );
	$gutter_width       = in_array( $page_custom_gutter, $valid_gutter_width, true ) ? $page_custom_gutter : '3';
	$classes[]          = "et_pb_gutters{$gutter_width}";

	return $classes;
}
add_filter( 'et_builder_inner_content_class', 'et_builder_add_builder_inner_content_class' );

/**
 * Get the opening wrappers for builder-powered content.
 *
 * @since 4.0
 *
 * @return string
 */
function et_builder_get_builder_content_opening_wrapper() {
	$outer_class   = apply_filters( 'et_builder_outer_content_class', array( 'et-boc' ) );
	$outer_classes = implode( ' ', $outer_class );
	$outer_id      = apply_filters( 'et_builder_outer_content_id', 'et-boc' );

	$is_dbp                  = et_is_builder_plugin_active();
	$dbp_compat_wrapper_open = $is_dbp ? '<div id="et_builder_outer_content" class="et_builder_outer_content">' : '';

	return sprintf(
		'<div id="%1$s" class="%2$s">
			%3$s
		',
		esc_attr( $outer_id ),
		esc_attr( $outer_classes ),
		et_core_intentionally_unescaped( $dbp_compat_wrapper_open, 'fixed_string' )
	);
}

/**
 * Get the opening wrappers for individual builder-powered layouts.
 *
 * @since 4.0
 *
 * @return string
 */
function et_builder_get_layout_opening_wrapper( $post_type = '' ) {
	$post_type    = ! empty( $post_type ) ? $post_type : get_post_type();
	$layout_class = array( 'et-l' );
	$el           = 'div';
	$layout_id    = '';

	switch ( $post_type ) {
		case ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE:
			$el             = 'header';
			$layout_class[] = 'et-l--header';
			break;

		case ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE:
			$layout_class[] = 'et-l--body';
			break;

		case ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE:
			$el             = 'footer';
			$layout_class[] = 'et-l--footer';
			break;

		default:
			$layout_class[] = 'et-l--post';
			break;
	}

	$layout_id      = apply_filters( 'et_builder_layout_id', $layout_id, $post_type );
	$layout_id      = ! empty( $layout_id ) ? sprintf( 'id="%s" ', esc_attr( $layout_id ) ) : '';
	$layout_class   = apply_filters( 'et_builder_layout_class', $layout_class );
	$layout_classes = implode( ' ', $layout_class );
	$inner_class    = apply_filters( 'et_builder_inner_content_class', array( 'et_builder_inner_content' ) );
	$inner_classes  = implode( ' ', $inner_class );

	return sprintf(
		'<%3$s %4$sclass="%1$s">
			<div class="%2$s">
		',
		esc_attr( $layout_classes ),
		esc_attr( $inner_classes ),
		esc_attr( $el ),
		et_core_esc_previously( $layout_id )
	);
}

/**
 * Get the closing wrappers for individual builder-powered layouts.
 *
 * @since 4.0
 *
 * @return string
 */
function et_builder_get_layout_closing_wrapper( $post_type = '' ) {
	$post_type = ! empty( $post_type ) ? $post_type : get_post_type();
	$el        = 'div';

	switch ( $post_type ) {
		case ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE:
			$el = 'header';
			break;

		case ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE:
			$el = 'footer';
			break;
	}

	return sprintf(
		'
		</div>
	</%1$s>
	',
		esc_attr( $el )
	);
}

/**
 * Get the closing wrappers for builder-powered content.
 *
 * @since 4.0
 *
 * @return string
 */
function et_builder_get_builder_content_closing_wrapper() {
	$is_dbp                   = et_is_builder_plugin_active();
	$dbp_compat_wrapper_close = $is_dbp ? '</div>' : '';

	return sprintf(
		'
			%1$s
		</div>
		',
		et_core_intentionally_unescaped( $dbp_compat_wrapper_close, 'fixed_string' )
	);
}

/**
 * Wrap post builder content.
 *
 * @since 3.10
 *
 * @param string $content The post content.
 *
 * @return string
 */
function et_builder_add_builder_content_wrapper( $content ) {
	// phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
	$is_bfb_new_page  = isset( $_GET['is_new_page'] ) && '1' === $_GET['is_new_page'];
	$has_layout_block = has_block( 'divi/layout', get_the_ID() );

	if ( ! et_pb_is_pagebuilder_used( get_the_ID() ) && ! is_et_pb_preview() && ! $is_bfb_new_page && ! $has_layout_block ) {
		return $content;
	}

	// Divi builder layout should only be used in singular template unless we are rendering
	// a theme builder layout as they can appear on any page.
	if ( ! is_singular() && ! $is_bfb_new_page && ! et_theme_builder_is_layout_post_type( get_post_type( get_the_ID() ) ) ) {
		return $content;
	}

	$content = et_builder_get_layout_opening_wrapper() . $content . et_builder_get_layout_closing_wrapper();

	/**
	 * Filter whether to add the outer builder content wrapper or not.
	 *
	 * @since 4.0
	 *
	 * @param bool $wrap
	 */
	$post_id                   = is_singular() ? get_the_ID() : 0;
	$is_custom_post_type       = et_builder_post_is_of_custom_post_type( $post_id ) && ! ET_Builder_Element::is_theme_builder_layout();
	$should_content_be_wrapped = $is_custom_post_type || et_is_builder_plugin_active() || et_core_is_fb_enabled();
	$wrap                      = apply_filters( 'et_builder_add_outer_content_wrap', $should_content_be_wrapped );

	if ( $wrap ) {
		$content = et_builder_get_builder_content_opening_wrapper() . $content . et_builder_get_builder_content_closing_wrapper();
	}

	return $content;
}
add_filter( 'the_content', 'et_builder_add_builder_content_wrapper' );
add_filter( 'et_builder_render_layout', 'et_builder_add_builder_content_wrapper' );

/**
 * Wraps a copy of a css selector and then returns both selectors.
 * Wrapping a copy of a selector instead of the original is necessary for selectors
 * that target elements both inside AND outside the wrapper element.
 *
 * @since 3.10
 * @since 4.6.6 New $inside_selectors parameter to extend default inside selector.
 *
 * @param string  $selector         CSS selector to wrap.
 * @param string  $suffix           Selector partial to add to the wrapped selector after the wrapper (a space will be added first).
 * @param boolean $clone            Duplicate the selector, wrap the duplicate, and then return both selectors. Default `true`.
 * @param mixed   $inside_selectors Additional inside builder element selectors.
 *
 * @return string
 */
function et_builder_maybe_wrap_css_selector( $selector, $suffix = '', $clone = true, $inside_selectors = '' ) {
	static $should_wrap_selectors = array();

	$post_id = ET_Builder_Element::get_theme_builder_layout_id();

	if ( ! isset( $should_wrap_selectors[ $post_id ] ) ) {
		$is_builder_used                   = et_pb_is_pagebuilder_used( $post_id ) || has_block( 'divi/layout', get_the_ID() );
		$should_wrap_selectors[ $post_id ] = et_is_builder_plugin_active() || et_builder_is_custom_post_type_archive() || ( $is_builder_used && ( et_builder_post_is_of_custom_post_type( $post_id ) || et_theme_builder_is_layout_post_type( get_post_type( $post_id ) ) ) );
	}

	if ( is_bool( $suffix ) ) {
		$clone  = $suffix;
		$suffix = '';
	}

	if ( ! $should_wrap_selectors[ $post_id ] ) {
		return trim( "{$selector} {$suffix}" );
	}

	$wrapper = ET_BUILDER_CSS_PREFIX;
	$result  = '';

	if ( $clone ) {
		$result .= $suffix ? "{$selector} {$suffix}, " : "{$selector}, ";
	}

	// By default, only selector that starts with `.et_pb` or `.et_fb` is considered as
	// inside builder element. $inside_selectors param allow us to extend it and it would
	// be useful for 3rd party extensions that use Divi Module Elements on their modules.
	//
	// Default inside builder element pattern as the first alternative, matches:
	// - \.et[_-]      : Start with .et- or .et_
	// - (?:pb|fb)[_-] : Followed by one of pb-, pb_, fb-, fb_.
	$inside_selector_pattern = '\.et[_-](?:pb|fb)[_-]';

	if ( ! empty( $inside_selectors ) ) {
		if ( is_array( $inside_selectors ) ) {
			$inside_selectors = implode( '|', $inside_selectors );
		}

		$inside_selector_pattern .= "|{$inside_selectors}";
	}

	// Elements selector pattern.
	// - (html[^ ]*)?        : 1st group (html). Match html followed by non empty string
	// - (body[^ ]*)?        : 2nd group (body). Match body followed by non empty string
	// - (.*?)               : 3rd group (outside). Match any character.
	// - ([^ ]*(?:inside).+) : 4th group (inside). Match one of inside builder element alternatives from $inside_selector_pattern.
	// - (?: *)              : Non capturing group. Match any space character.
	$elements_selector_pattern = '/^(html[^ ]*)?(?: *)(body[^ ]*)?(?: *)(.*?)(?: *)([^ ]*(?:' . $inside_selector_pattern . ').+)/';

	if ( $suffix ) {
		// $suffix param allows caller to split selector into two parts (1. outside builder and 2. inside builder)
		// so that it can be wrapped properly. It was implemented before the regex solution below.
		if ( preg_match( '/et_fb_preview|et_fb_desktop_mode/', $selector ) ) {
			// Selector targets html element using a custom class.
			$result .= "{$selector} {$wrapper} {$suffix}";
		} else {
			// Selector targets body element either directly or using a custom class.
			$result .= "{$selector}{$wrapper} {$suffix}";
		}
	} elseif ( preg_match( $elements_selector_pattern, $selector, $matches ) ) {
		// The selector includes elements outside builder content so we can't just prepend the wrapper to it.
		list( $_, $html, $body, $outside_builder, $inside_builder ) = $matches;

		$parts   = array_filter(
			array(
				$html,
				// Intentionally glued together to produce "body.et-db", for example.
				$body . ET_BUILDER_CSS_WRAPPER_PREFIX,
				$outside_builder,
				ET_BUILDER_CSS_LAYOUT_PREFIX,
				$inside_builder,
			)
		);
		$result .= implode( ' ', $parts );

	} else {
		$result .= "{$wrapper} {$selector}";
	}

	return trim( $result );
}

/**
 * Wrapper for {@see et_builder_maybe_wrap_css_selector()} to support multiple selectors
 * at once (eg. selector1, selector2, selector3)
 *
 * @since 3.10
 * @since 4.6.6 New $inside_selectors parameter to extend default inside selector.
 *
 * @param string $selector         CSS selectors to wrap.
 * @param bool   $clone            {@see et_builder_maybe_wrap_css_selector()}.
 * @param mixed  $inside_selectors Additional inside builder element selectora.
 *
 * @return string
 */
function et_builder_maybe_wrap_css_selectors( $selector, $clone = true, $inside_selectors = '' ) {
	static $should_wrap_selectors = array();

	$post_id      = ET_Builder_Element::get_theme_builder_layout_id();
	$wrap_post_id = $post_id;

	if ( ! isset( $should_wrap_selectors[ $post_id ] ) ) {
		if ( et_theme_builder_is_layout_post_type( get_post_type( $post_id ) ) ) {
			$main_post_id = ET_Post_Stack::get_main_post_id();

			if ( $main_post_id ) {
				$wrap_post_id = $main_post_id;
			}
		}

		// GB editor + layout block is considered using builder.
		$is_builder_used                   = et_pb_is_pagebuilder_used( $wrap_post_id ) || has_block( 'divi/layout', get_the_ID() );
		$should_wrap_selectors[ $post_id ] = et_is_builder_plugin_active() || et_builder_is_custom_post_type_archive() || ( $is_builder_used && et_builder_post_is_of_custom_post_type( $wrap_post_id ) );
	}

	if ( ! $should_wrap_selectors[ $post_id ] ) {
		return $selector;
	}

	$selectors = explode( ',', $selector );
	$result    = array();

	foreach ( $selectors as $css_selector ) {
		$result[] = et_builder_maybe_wrap_css_selector( $css_selector, '', $clone, $inside_selectors );
	}

	return implode( ',', $result );
}

/**
 * Unprepend code module content.
 *
 * @param string $content Code module content.
 *
 * @return string|string[]|null
 */
function _et_pb_code_module_unprep_content( $content ) {
	// before we swap out the placeholders,
	// remove all the <p> tags and \n that wpautop added!
	$content = preg_replace( '/\n/smi', '', $content );
	$content = preg_replace( '/<p>/smi', '', $content );
	$content = preg_replace( '/<\/p>/smi', '', $content );

	$content = str_replace( '<!–- [et_pb_br_holder] -–>', '<br />', $content );

	// convert the <pee tags back to <p
	// see et_pb_prep_code_module_for_wpautop().
	$content = str_replace( '<pee', '<p', $content );
	$content = str_replace( '</pee>', '</p> ', $content );

	return $content;
}

/**
 * A preg_replace_callback callback.
 *
 * @param array $matches matched elements.
 *
 * @return string|string[]
 */
function _et_pb_code_module_unprep_content_regex_cb( $matches ) {
	$prepped_content = $matches[1];

	$prepped_content = _et_pb_code_module_unprep_content( $prepped_content );

	return str_replace( $matches[1], $prepped_content, $matches[0] );
}

/**
 * Undo prepared code module content.
 *
 * @param string $content Content from which to remove prepended.
 *
 * @return string|string[]|null
 */
function et_pb_unprep_code_module_for_wpautop( $content ) {
	$content = preg_replace_callback( '/\[et_pb_code.*?\](.*)\[\/et_pb_code\]/mis', '_et_pb_code_module_unprep_content_regex_cb', $content );
	$content = preg_replace_callback( '/\[et_pb_fullwidth_code.*?\](.*)\[\/et_pb_fullwidth_code\]/mis', '_et_pb_code_module_unprep_content_regex_cb', $content );

	return $content;
}

/**
 * Prepare code modules for wpautop.
 *
 * @param string $content Content to be prep.
 *
 * @return string|string[]|null
 */
function _et_pb_code_module_prep_content( $content ) {
	// convert <br /> tags into placeholder so wpautop will leave them alone.
	$content = preg_replace( '|<br[\s]?[\/]?>|', '<!–- [et_pb_br_holder] -–>', $content );

	// convert <p> tag to <pee> tag, so wpautop will leave them alone,
	// *and* so that we can clearly spot the <p> tags that wpautop adds
	// so we can quickly remove them.
	$content = preg_replace( '|<p |', '<pee ', $content );
	$content = preg_replace( '|<p>|', '<pee>', $content );
	$content = preg_replace( '|<\/p>|', '</pee>', $content );

	return $content;
}

/**
 * The Callback preg_replace_callback used while preparing code module for autop.
 *
 * @param array $matches array of matched elements.
 *
 * @return string|string[]
 */
function _et_pb_code_module_prep_content_regex_cb( $matches ) {
	$prepped_content = $matches[1];

	$prepped_content = _et_pb_code_module_prep_content( $prepped_content );

	return str_replace( $matches[1], $prepped_content, $matches[0] );
}

/**
 * Prepare code module for autop.
 *
 * @param string $content the content.
 *
 * @return string|string[]|null
 */
function et_pb_prep_code_module_for_wpautop( $content ) {
	$content = preg_replace_callback( '/\[et_pb_code(?:(?![^\]]*\/\])[^\]]*)\](.*?)\[\/et_pb_code\]/mis', '_et_pb_code_module_prep_content_regex_cb', $content );
	$content = preg_replace_callback( '/\[et_pb_fullwidth_code(?:(?![^\]]*\/\])[^\]]*)\](.*?)\[\/et_pb_fullwidth_code\]/mis', '_et_pb_code_module_prep_content_regex_cb', $content );

	return $content;
}

/**
 * Determine whether dynamic asset exists or not.
 *
 * @param string      $prefix Asset prefix.
 * @param string|bool $post_type Asset post type.
 *
 * @return bool
 */
function et_fb_dynamic_asset_exists( $prefix, $post_type = false ) {
	// Get post type if it isn't being defined.
	if ( ! $post_type ) {
		if ( wp_doing_ajax() ) {
			// phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
			$post_type = isset( $_REQUEST['et_post_type'] ) ? sanitize_text_field( $_REQUEST['et_post_type'] ) : 'post';
			$post_type = sanitize_text_field( $post_type );
		} else {
			global $post;

			$post_type = isset( $post->post_type ) ? $post->post_type : 'post';
		}
	}
	$post_type = apply_filters( 'et_builder_cache_post_type', $post_type, $prefix );

	$prefix = esc_attr( $prefix );
	$cache  = sprintf( '%s/%s', ET_Core_PageResource::get_cache_directory(), get_locale() );
	$files  = glob( sprintf( '%s/%s-%s-*.js', $cache, $prefix, $post_type ) );

	return is_array( $files ) && count( $files ) > 0;
}

if ( ! function_exists( 'et_fb_delete_builder_assets' ) ) :
	/**
	 * Delete builder cache.
	 */
	function et_fb_delete_builder_assets() {
		$cache = ET_Core_PageResource::get_cache_directory();

		// Old cache location, make sure we clean that one too.
		$old_files = glob( sprintf( '%s/*.js', $cache ) );
		$old_files = is_array( $old_files ) ? $old_files : array();
		// New, per language location.
		$new_files = glob( sprintf( '%s/*/*.js', $cache ) );
		$new_files = is_array( $new_files ) ? $new_files : array();

		// Modules cache.
		$modules_files = glob( sprintf( '%s/*/*.data', $cache ) );
		$modules_files = is_array( $modules_files ) ? $modules_files : array();

		foreach ( array_merge( $old_files, $new_files, $modules_files ) as $file ) {
			@unlink( $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- unlink may fail with the permissions denied error.
		}

		// Images data cache.
		$image_cache_keys = array(
			'image_srcset_sizes',
			'image_responsive_metadata',
			'attachment_id_by_url',
			'attachment_size_by_url',
		);

		foreach ( $image_cache_keys as $image_cache_key ) {
			$cache_file_name = ET_Core_Cache_File::get_cache_file_name( $image_cache_key );

			if ( file_exists( $cache_file_name ) ) {
				@unlink( $cache_file_name ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- unlink may fail with the permissions denied error.
			}
		}

		/**
		 * Clear AJAX cache.
		 *
		 * @since 4.0.10
		 */
		do_action( 'et_builder_ajax_cache_clear' );
	}

	// Since Google data is included in static helpers, we have to delete assets
	// whenever the option is updated to avoid Builder reloads.
	add_action( 'update_option_et_google_api_settings', 'et_fb_delete_builder_assets' );
endif;

if ( ! function_exists( 'et_fb_enqueue_open_sans' ) ) :
	/**
	 * Load Open Sans font.
	 *
	 * @deprecated See {@see et_builder_enqueue_open_sans()}
	 */
	function et_fb_enqueue_open_sans() {
		et_builder_enqueue_open_sans();
	}
endif;

/**
 * Wrapper for et_core_portability_link() which does ET capability checks as well.
 *
 * @since 3.26
 *
 * @param string       $context The context used to register the portability.
 * @param string|array $attributes Query string or array of attributes. Default empty.
 *
 * @return string
 */
function et_builder_portability_link( $context, $attributes = array() ) {
	global $shortname;

	$product = (string) $shortname;

	$context_caps = array(
		'et_builder'         => 'et_builder_portability',
		'et_builder_layouts' => 'et_builder_layouts_portability',
		"et_{$product}_mods" => "et_{$product}_mods_portability",
		'et_pb_roles'        => 'et_pb_roles_portability',
		'epanel'             => 'epanel_portability',
	);

	$cap = et_()->array_get( $context_caps, $context, '' );

	if ( ! empty( $cap ) && ! et_pb_is_allowed( $cap ) ) {
		return '';
	}

	return et_core_portability_link( $context, $attributes );
}

/**
 * Get the list of all public post types.
 *
 * @since 3.26.7
 *
 * @return WP_Post_Type[]
 */
function et_builder_get_public_post_types() {
	$cache_key = 'et_builder_get_public_post_types';

	if ( ! et_core_cache_has( $cache_key ) ) {
		$blocklist      = array_merge(
			array(
				'et_pb_layout',
				ET_THEME_BUILDER_TEMPLATE_POST_TYPE,
			),
			et_theme_builder_get_layout_post_types()
		);
		$all_post_types = get_post_types( array(), 'objects' );
		$post_types     = array();

		foreach ( $all_post_types as $post_type ) {
			if ( ! in_array( $post_type->name, $blocklist, true ) && et_builder_is_post_type_public( $post_type->name ) ) {
				$post_types[ $post_type->name ] = $post_type;
			}
		}

		et_core_cache_add( $cache_key, $post_types );
	}

	/**
	 * Filter array of public post types.
	 *
	 * @since 3.26.7
	 *
	 * @param WP_Post_Type[]
	 */
	return apply_filters( 'et_builder_get_public_post_types', et_core_cache_get( $cache_key ) );
}

/**
 * Clear public post type cache whenever a custom post type is registered.
 *
 * @since 3.26.7
 *
 * @return void
 */
function et_builder_clear_get_public_post_types_cache() {
	et_core_cache_delete( 'et_builder_get_public_post_types' );
}
add_action( 'registered_post_type', 'et_builder_clear_get_public_post_types_cache' );

if ( ! function_exists( 'et_filter_intermediate_image_sizes_advanced' ) ) :
	/**
	 * Filters the image sizes to calculate responsive image height.
	 *
	 * @param array $sizes    An associative array of image sizes.
	 * @param array $metadata An associative array of image metadata: width, height, file.
	 *
	 * @return array
	 */
	function et_filter_intermediate_image_sizes_advanced( $sizes, $metadata = array() ) {
		// Bail early when the attachment metadata is empty.
		if ( ! $metadata ) {
			return $sizes;
		}

		foreach ( array_keys( $sizes ) as $size_key ) {
			if ( strpos( $size_key, 'et-pb-image--responsive--' ) !== 0 ) {
				continue;
			}

			$breakpoint      = str_replace( 'et-pb-image--responsive--', '', $size_key );
			$responsive_size = et_image_get_responsive_size( $metadata['width'], $metadata['height'], $breakpoint );

			if ( $responsive_size && isset( $responsive_size['width'] ) && isset( $responsive_size['height'] ) ) {
				$sizes[ $size_key ]['width']  = $responsive_size['width'];
				$sizes[ $size_key ]['height'] = $responsive_size['height'];
			} else {
				unset( $sizes[ $size_key ] );
			}
		}

		return $sizes;
	}
endif;
add_filter( 'intermediate_image_sizes_advanced', 'et_filter_intermediate_image_sizes_advanced', 10, 2 );

if ( ! function_exists( 'et_action_sync_attachment_data_cache' ) ) :
	/**
	 * Sync image data cache
	 *
	 * @since 3.29.3
	 *
	 * @param int   $attachment_id Attachment ID.
	 * @param array $metadata      Image metadata.
	 *
	 * @return void
	 */
	function et_action_sync_attachment_data_cache( $attachment_id, $metadata = null ) {
		if ( ! $attachment_id ) {
			return;
		}

		$url_full = wp_get_attachment_url( $attachment_id );

		if ( ! $url_full ) {
			return;
		}

		// Normalize image URL to remove the HTTP/S protocol.
		$normalized_url_full = et_attachment_normalize_url( $url_full );

		if ( ! $normalized_url_full ) {
			return;
		}

		$normalized_urls = array(
			$normalized_url_full => $normalized_url_full,
		);

		if ( is_null( $metadata ) ) {
			$metadata = wp_get_attachment_metadata( $attachment_id );
		}

		if ( ! empty( $metadata ) ) {
			foreach ( $metadata['sizes'] as $image_size ) {
				$normalized_url = str_replace( basename( $normalized_url_full ), $image_size['file'], $normalized_url_full );

				if ( ! isset( $normalized_urls[ $normalized_url ] ) ) {
					$normalized_urls[ $normalized_url ] = $normalized_url;
				}
			}
		}

		$cache_keys = array(
			'attachment_id_by_url',
			'attachment_size_by_url',
			'image_responsive_metadata',
			'image_srcset_sizes',
		);

		foreach ( $cache_keys as $cache_key ) {
			$cache = ET_Core_Cache_File::get( $cache_key );

			// Skip if the cache data is empty.
			if ( ! $cache ) {
				continue;
			}

			foreach ( $normalized_urls as $normalized_url ) {
				unset( $cache[ $normalized_url ] );
			}

			ET_Core_Cache_File::set( $cache_key, $cache );
		}
	}
endif;
add_action( 'delete_attachment', 'et_action_sync_attachment_data_cache' );

if ( ! function_exists( 'et_filter_wp_generate_attachment_metadata' ) ) :
	/**
	 * Sync the cached srcset data when attachment meta data generated/updated.
	 *
	 * @since 3.27.1
	 *
	 * @param array $metadata      An array of attachment meta data.
	 * @param int   $attachment_id Current attachment ID.
	 *
	 * @return array
	 */
	function et_filter_wp_generate_attachment_metadata( $metadata, $attachment_id = 0 ) {
		if ( $attachment_id ) {
			et_action_sync_attachment_data_cache( $attachment_id, $metadata );
		}

		return $metadata;
	}
endif;
add_filter( 'wp_generate_attachment_metadata', 'et_filter_wp_generate_attachment_metadata', 10, 2 );

/**
 * Filter the main query paged arg to avoid pagination clashes with the Blog module pagination.
 *
 * @since 4.0
 *
 * @param WP_Query $query Query object.
 *
 * @return void
 */
function et_builder_filter_main_query_paged_for_blog_module( $query ) {
	/**
	 * Utility which holds the current page number for the Blog module.
	 * Necessary to avoid clashes with the main query pagination.
	 *
	 * @var integer
	 */
	global $__et_blog_module_paged, $__et_portfolio_module_paged;

	// phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
	if ( ( isset( $_GET['et_blog'] ) || isset( $_GET['et_portfolio'] ) ) && $query->is_main_query() ) {
		$__et_blog_module_paged      = $query->get( 'paged' );
		$__et_portfolio_module_paged = $query->get( 'paged' );
		$query->set( 'paged', 0 );
	}
}
add_filter( 'pre_get_posts', 'et_builder_filter_main_query_paged_for_blog_module' );

if ( ! function_exists( 'et_maybe_enable_embed_shortcode' ) ) :
	/**
	 * Maybe enable [embed] shortcode at the content.
	 *
	 * @since 4.4.9
	 *
	 * @param string  $content Content to search for shortcodes.
	 * @param boolean $is_content Whether the passed content is the content.
	 *
	 * @return string
	 */
	function et_maybe_enable_embed_shortcode( $content, $is_content ) {
		if ( $is_content && has_shortcode( $content, 'embed' ) ) {
			global $wp_embed;
			$content = $wp_embed->run_shortcode( $content );
		}

		return $content;
	}
endif;

/**
 * Calculate value which has unit on it.
 *
 * Might need to group this style rendering related utils function if there are more of them
 *
 * @used-by ET_Builder_Module_Helper_Overlay::process_icon_font_size()
 *
 * @param string         $value      base value which has unit.
 * @param int|float      $multiplier multiplier (literally).
 * @param bool|int|float $min_value  minimum $value to do calculation. set to false to skip.
 *
 * @return string
 */
function et_builder_multiply_value_has_unit( $value, $multiplier, $min_value = false ) {
	$number           = (float) $value;
	$unit             = str_replace( $number, '', $value );
	$should_calculate = false === $min_value || $min_value < $number;
	$product          = $should_calculate ? $number * (float) $multiplier : $min_value;

	return (string) $product . $unit;
}

/**
 * Register custom sidebars.
 *
 * @since 4.4.8 Moved from builder/functions.php, so it can be loaded on wp_ajax_save_widget().
 */
function et_builder_widgets_init() {
	$et_pb_widgets = get_theme_mod( 'et_pb_widgets' );
	$widget_areas  = et_()->array_get( $et_pb_widgets, 'areas', array() );
	if ( ! empty( $widget_areas ) ) {
		foreach ( $widget_areas as $id => $name ) {
			register_sidebar(
				array(
					'name'          => sanitize_text_field( $name ),
					'id'            => sanitize_text_field( $id ),
					'before_widget' => '<div id="%1$s" class="et_pb_widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h4 class="widgettitle">',
					'after_title'   => '</h4>',
				)
			);
		}
	}

	// Disable built-in's recent comments widget link styling because ET Themes don't need it.
	if ( ! et_is_builder_plugin_active() ) {
		add_filter( 'show_recent_comments_widget_style', '__return_false' );
	}
}

// Call the widgets init at 'init' hook if Divi Builder plugin active because plugin
// loads the Divi builder at 'init' hook and 'widgets_init' is too early.
if ( et_is_builder_plugin_active() ) {
	add_action( 'init', 'et_builder_widgets_init', 20 );
} else {
	add_action( 'widgets_init', 'et_builder_widgets_init' );
}

/**
 * For Dev Use
 */
function et_light_debug_backtrace() {
	$debug_backtrace       = debug_backtrace();
	$keys                  = [ 'file', 'line', 'function', 'class' ];
	$light_debug_backtrace = [];

	foreach ( $debug_backtrace as $key => $value ) {
		foreach ( $keys as $_key ) {
			if ( isset( $value[ $_key ] ) ) {
				$light_debug_backtrace[ $key ][ $_key ] = $value[ $_key ];
			}
		}
	}

	return array_slice( $light_debug_backtrace, 1 );
}

/**
 * Get all global colors.
 *
 * @since 4.9.0
 *
 * @return array
 */
function et_builder_get_all_global_colors( $include_customizer = false ) {
	if ( $include_customizer ) {
		$primary_color   = et_get_option( 'accent_color', '#2ea3f2' );
		$secondary_color = et_get_option( 'secondary_accent_color', '#2ea3f2' );
		$heading_color   = et_get_option( 'header_color', '#666666' );
		$body_color      = et_get_option( 'font_color', '#666666' );

		$saved_global_colors = et_get_option( 'et_global_colors' );

		if ( empty( $saved_global_colors ) ) {
			$saved_global_colors = [];
		}

		// Remove customizer global colors if exist for any reason.
		// For example if user imported global colors on old version of Divi which doesn't support customizer colors.
		$excluded_keys = [
			'gcid-primary-color',
			'gcid-secondary-color',
			'gcid-heading-color',
			'gcid-body-color',
		];

		foreach ( $excluded_keys as $excluded_key ) {
			unset( $saved_global_colors[ $excluded_key ] );
		}

		return array_merge(
			[
				'gcid-primary-color'   => [
					'color'  => $primary_color,
					'active' => 'yes',
				],
				'gcid-secondary-color' => [
					'color'  => $secondary_color,
					'active' => 'yes',
				],
				'gcid-heading-color'   => [
					'color'  => $heading_color,
					'active' => 'yes',
				],
				'gcid-body-color'      => [
					'color'  => $body_color,
					'active' => 'yes',
				],
			],
			$saved_global_colors
		);
	}

	return et_get_option( 'et_global_colors' );
}
