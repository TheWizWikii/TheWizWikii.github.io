<?php

/**
 * Redirect admin post to FB builder if set.
 *
 * @since 3.0.0
 *
 * @param string $location Parameter passed by the 'redirect_post_location' filter.
 * @return string $_POST['et-fb-builder-redirect'] if set, $location otherwise.
 */
function et_fb_redirect_post_location( $location ) {
	// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
	if ( is_admin() && isset( $_POST['et-fb-builder-redirect'] ) ) {
		return $_POST['et-fb-builder-redirect'];
	}

	return $location;
	// phpcs:enable
}
add_filter( 'redirect_post_location', 'et_fb_redirect_post_location' );

/**
 * @internal NOTE: Don't use this from outside builder code! {@see et_core_is_fb_enabled()}.
 *
 * @deprecated Use et_core_is_fb_enabled() instead.
 *
 * @return bool
 */
function et_fb_enabled() {
	if ( defined( 'ET_FB_ENABLED' ) ) {
		return ET_FB_ENABLED;
	}

	// et_fb parameter supported by FB only, so check !is_admin() to avoid false loading of BFB
	if ( empty( $_GET['et_fb'] ) && ! is_admin() ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		return false;
	}

	if ( is_customize_preview() ) {
		return false;
	}

	if ( ! is_admin() && ! is_single() && ! is_page() && ! et_builder_used_in_wc_shop() && ! isset( $_GET['is_new_page'] ) && ( ! et_fb_is_theme_builder_used_on_page() || ! et_pb_is_allowed( 'theme_builder' ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- used inside isset()
		return false;
	}

	if ( ! et_fb_is_user_can_edit() ) {
		return false;
	}

	if ( ! et_pb_is_allowed( 'use_visual_builder' ) ) {
		return false;
	}

	// if we have made it here, determine if this is legit BFB page
	if ( is_admin() ) {
		if ( ! et_builder_should_load_framework() ) {
			return false;
		}

		if ( ! et_builder_bfb_enabled() ) {
			return false;
		}

		// no need to check posttypes here because it's checked in more appropriate place - et_pb_admin_scripts_styles()
	}

	return true;
}

function et_fb_is_user_can_edit() {

	$_ = ET_Core_Data_Utils::instance();

	$post_id = et_core_page_resource_get_the_ID();

	// If this function is called very early, global $post might not be defined yet.
	$post_id = $post_id ? $post_id : $_->array_get( $_GET, 'post', 0 );

	if ( is_page() ) {
		if ( ! current_user_can( 'edit_pages' ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_others_pages' ) && ! current_user_can( 'edit_page', $post_id ) ) {
			return false;
		}

		if ( ( ! current_user_can( 'publish_pages' ) || ! current_user_can( 'edit_published_pages' ) ) && 'publish' === get_post_status( $post_id ) ) {
			return false;
		}

		if ( ( ! current_user_can( 'edit_private_pages' ) || ! current_user_can( 'read_private_pages' ) ) && 'private' === get_post_status( $post_id ) ) {
			return false;
		}
	} else {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_others_posts' ) && ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		if ( ( ! current_user_can( 'publish_posts' ) || ! current_user_can( 'edit_published_posts' ) ) && 'publish' === get_post_status( $post_id ) ) {
			return false;
		}

		if ( ( ! current_user_can( 'edit_private_posts' ) || ! current_user_can( 'read_private_posts' ) ) && 'private' === get_post_status( $post_id ) ) {
			return false;
		}
	}

	return true;
}

define( 'ET_FB_ENABLED', et_core_is_fb_enabled() );

// Set default value if the constant hasn't been defined
if ( ! defined( 'ET_BUILDER_LOAD_ON_AJAX' ) ) {
	define( 'ET_BUILDER_LOAD_ON_AJAX', false );
}

// Always load helpers files to prevent errors when 3rd party modules are autosaved.
define( 'ET_FB_URI', ET_BUILDER_URI . '/frontend-builder' );
define( 'ET_FB_ASSETS_URI', ET_FB_URI . '/assets' );

require_once ET_BUILDER_DIR . 'frontend-builder/helpers.php';

// Stop here if the front end builder isn't enabled.
if ( ! ET_FB_ENABLED && ! ET_BUILDER_LOAD_ON_AJAX ) {
	return;
}

require_once ET_BUILDER_DIR . 'frontend-builder/view.php';
require_once ET_BUILDER_DIR . 'frontend-builder/assets.php';
require_once ET_BUILDER_DIR . 'frontend-builder/rtl.php';

do_action( 'et_fb_framework_loaded' );
et_fb_fix_plugin_conflicts();
