<?php


if ( ! function_exists( '_sanitize_text_fields' ) ):
/**
 * Internal helper function to sanitize a string from user input or from the db
 *
 * @since 4.7.0
 * @access private
 *
 * @param string $str String to sanitize.
 * @param bool $keep_newlines optional Whether to keep newlines. Default: false.
 * @return string Sanitized string.
 */
function _sanitize_text_fields( $str, $keep_newlines = false ) {
	$filtered = wp_check_invalid_utf8( $str );

	if ( strpos( $filtered, '<' ) !== false ) {
		$filtered = wp_pre_kses_less_than( $filtered );
		// This will strip extra whitespace for us.
		$filtered = wp_strip_all_tags( $filtered, false );

		// Use html entities in a special case to make sure no later
		// newline stripping stage could lead to a functional tag
		$filtered = str_replace( "<\n", "&lt;\n", $filtered );
	}

	if ( ! $keep_newlines ) {
		$filtered = preg_replace( '/[\r\n\t ]+/', ' ', $filtered );
	}

	$filtered = trim( $filtered );
	$found    = false;

	while ( preg_match( '/%[a-f0-9]{2}/i', $filtered, $match ) ) {
		$filtered = str_replace( $match[0], '', $filtered );
		$found    = true;
	}

	if ( $found ) {
		// Strip out the whitespace that may now exist after removing the octets.
		$filtered = trim( preg_replace( '/ +/', ' ', $filtered ) );
	}

	return $filtered;
}
endif;


if ( ! function_exists( 'get_site' ) ):
/**
 * Retrieves site data given a site ID or site object.
 *
 * Site data will be cached and returned after being passed through a filter.
 * If the provided site is empty, the current site global will be used.
 *
 * @since 4.6.0
 *
 * @param WP_Site|int|null $site Optional. Site to retrieve. Default is the current site.
 * @return WP_Site|null The site object or null if not found.
 */
function get_site( $site = null ) {
	if ( empty( $site ) ) {
		$site = get_current_blog_id();
	}

	if ( $site instanceof WP_Site ) {
		$_site = $site;
	} elseif ( is_object( $site ) ) {
		$_site = new WP_Site( $site );
	} else {
		$_site = WP_Site::get_instance( $site );
	}

	if ( ! $_site ) {
		return null;
	}

	/**
	 * Fires after a site is retrieved.
	 *
	 * @since 4.6.0
	 *
	 * @param WP_Site $_site Site data.
	 */
	$_site = apply_filters( 'get_site', $_site );

	return $_site;
}
endif;


if ( ! function_exists( 'sanitize_textarea_field' ) ):
/**
 * Sanitizes a multiline string from user input or from the database.
 *
 * The function is like sanitize_text_field(), but preserves
 * new lines (\n) and other whitespace, which are legitimate
 * input in textarea elements.
 *
 * @see sanitize_text_field()
 *
 * @since 4.7.0
 *
 * @param string $str String to sanitize.
 * @return string Sanitized string.
 */
function sanitize_textarea_field( $str ) {
	$filtered = _sanitize_text_fields( $str, true );

	/**
	 * Filters a sanitized textarea field string.
	 *
	 * @since 4.7.0
	 *
	 * @param string $filtered The sanitized string.
	 * @param string $str      The string prior to being sanitized.
	 */
	return apply_filters( 'sanitize_textarea_field', $filtered, $str );
}
endif;


if ( ! function_exists( 'wp_doing_ajax' ) ):
function wp_doing_ajax() {
	/**
	 * Filters whether the current request is an Ajax request.
	 *
	 * @since 4.7.0
	 *
	 * @param bool $wp_doing_ajax Whether the current request is an Ajax request.
	 */
	return apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX );
}
endif;


if ( ! function_exists( 'wp_doing_cron' ) ):
function wp_doing_cron() {
	/**
	 * Filters whether the current request is a WordPress cron request.
	 *
	 * @since 4.8.0
	 *
	 * @param bool $wp_doing_cron Whether the current request is a WordPress cron request.
	 */
	return apply_filters( 'wp_doing_cron', defined( 'DOING_CRON' ) && DOING_CRON );
}
endif;

if ( ! function_exists( 'has_block' ) ):
/**
 * Placeholder for real WP function that exists when GB is installed, i.e. WP >= 5.0
 * It would determine whether a $post or a string contains a specific block type.
 *
 * @see has_block()
 *
 * @since 4.2
 *
 * @return bool forced false result.
 */
function has_block() {
	return false;
}
endif;

if ( ! function_exists( 'wp_get_default_update_php_url' ) ) :
/**
 * Gets the default URL to learn more about updating the PHP version the site is running on.
 *
 * Do not use this function to retrieve this URL. Instead, use {@see wp_get_update_php_url()} when relying on the URL.
 * This function does not allow modifying the returned URL, and is only used to compare the actually used URL with the
 * default one.
 *
 * @since 5.1.0
 * @access private
 *
 * @return string Default URL to learn more about updating PHP.
 */
function wp_get_default_update_php_url() {
	return _x( 'https://wordpress.org/support/update-php/', 'localized PHP upgrade information page' );
}
endif;

if ( ! function_exists( 'wp_get_update_php_url' ) ) :
/**
 * Gets the URL to learn more about updating the PHP version the site is running on.
 *
 * This URL can be overridden by specifying an environment variable `WP_UPDATE_PHP_URL` or by using the
 * {@see 'wp_update_php_url'} filter. Providing an empty string is not allowed and will result in the
 * default URL being used. Furthermore the page the URL links to should preferably be localized in the
 * site language.
 *
 * @since 5.1.0
 *
 * @return string URL to learn more about updating PHP.
 */
function wp_get_update_php_url() {
	$default_url = wp_get_default_update_php_url();

	$update_url = $default_url;
	if ( false !== getenv( 'WP_UPDATE_PHP_URL' ) ) {
		$update_url = getenv( 'WP_UPDATE_PHP_URL' );
	}

	/**
	 * Filters the URL to learn more about updating the PHP version the site is running on.
	 *
	 * Providing an empty string is not allowed and will result in the default URL being used. Furthermore
	 * the page the URL links to should preferably be localized in the site language.
	 *
	 * @since 5.1.0
	 *
	 * @param string $update_url URL to learn more about updating PHP.
	 */
	$update_url = apply_filters( 'wp_update_php_url', $update_url );

	if ( empty( $update_url ) ) {
		$update_url = $default_url;
	}

	return $update_url;
}
endif;

if ( ! function_exists( 'wp_get_update_php_annotation' ) ) :
/**
 * Returns the default annotation for the web hosting altering the "Update PHP" page URL.
 *
 * This function is to be used after {@see wp_get_update_php_url()} to return a consistent
 * annotation if the web host has altered the default "Update PHP" page URL.
 *
 * @since 5.2.0
 *
 * @return string Update PHP page annotation. An empty string if no custom URLs are provided.
 */
function wp_get_update_php_annotation() {
	$update_url  = wp_get_update_php_url();
	$default_url = wp_get_default_update_php_url();

	if ( $update_url === $default_url ) {
		return '';
	}

	$annotation = sprintf(
		/* translators: %s: Default Update PHP page URL. */
		__( 'This resource is provided by your web host, and is specific to your site. For more information, <a href="%s" target="_blank">see the official WordPress documentation</a>.' ),
		esc_url( $default_url )
	);

	return $annotation;
}
endif;

if ( ! function_exists( 'wp_update_php_annotation' ) ) :
/**
 * Prints the default annotation for the web host altering the "Update PHP" page URL.
 *
 * This function is to be used after {@see wp_get_update_php_url()} to display a consistent
 * annotation if the web host has altered the default "Update PHP" page URL.
 *
 * @since 5.1.0
 * @since 5.2.0 Added the `$before` and `$after` parameters.
 *
 * @param string $before Markup to output before the annotation. Default `<p class="description">`.
 * @param string $after  Markup to output after the annotation. Default `</p>`.
 */
function wp_update_php_annotation( $before = '<p class="description">', $after = '</p>' ) {
	$annotation = wp_get_update_php_annotation();

	if ( $annotation ) {
		echo et_core_intentionally_unescaped( $before . $annotation . $after, 'html' );
	}
}
endif;

if ( ! function_exists( 'is_wp_version_compatible' ) ) :
/**
 * Checks compatibility with the current WordPress version.
 *
 * @since 5.2.0
 *
 * @param string $required Minimum required WordPress version.
 * @return bool True if required version is compatible or empty, false if not.
 */
function is_wp_version_compatible( $required ) {
	return empty( $required ) || version_compare( get_bloginfo( 'version' ), $required, '>=' );
}
endif;

if ( ! function_exists( 'is_php_version_compatible' ) ) :
/**
 * Checks compatibility with the current PHP version.
 *
 * @since 5.2.0
 *
 * @param string $required Minimum required PHP version.
 * @return bool True if required version is compatible or empty, false if not.
 */
function is_php_version_compatible( $required ) {
	return empty( $required ) || version_compare( phpversion(), $required, '>=' );
}
endif;

if ( ! function_exists( 'wp_body_open' ) ) :
	/**
	 * Fire the wp_body_open action.
	 *
	 * See {@see 'wp_body_open'}.
	 *
	 * @since 5.2.0
	 */
	function wp_body_open() {
		/**
		 * Triggered after the opening body tag.
		 *
		 * @since 5.2.0
		 */
		do_action( 'wp_body_open' );
	}
endif;
