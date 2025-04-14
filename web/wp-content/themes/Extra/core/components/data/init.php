<?php

if ( ! function_exists( 'et_core_data_init' ) ):
function et_core_data_init() {}
endif;


if ( ! function_exists( 'et_' ) ):
function et_() {
	global $et_;

	if ( ! $et_ ) {
		$et_ = ET_Core_Data_Utils::instance();
	}

	return $et_;
}
endif;


if ( ! function_exists( 'et_html_attr' ) ):
/**
 * Generates a properly escaped attribute string.
 *
 * @param string $name         The attribute name.
 * @param string $value        The attribute value.
 * @param bool   $space_before Whether or not the result should start with a space. Default is `true`.
 *
 * @return string
 */
function et_html_attr( $name, $value, $space_before = true ) {
	$result = ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';

	return $space_before ? $result : trim( $result );
}
endif;

if ( ! function_exists( 'et_html_attrs' ) ):
/**
 * Generate properly escaped attributes string
 *
 * @since 3.10
 *
 * @param array $attributes Array of attributes
 *
 * @return string
 */
function et_html_attrs( $attributes = array() ) {
	$output = '';

	foreach ( $attributes as $name => $value ) {
		$parsed_value = is_array( $value ) ? implode( ' ', $value ) : $value;

		$output .= et_html_attr( $name, $parsed_value );
	}

	return $output;
}
endif;


if ( ! function_exists( 'et_sanitized_previously' ) ):
/**
 * Semantical previously sanitized acknowledgement
 *
 * @deprecated {@see et_core_sanitized_previously()}
 *
 * @since 3.17.3 Deprecated
 *
 * @param mixed $value The value being passed-through
 *
 * @return mixed
 */
function et_sanitized_previously( $value ) {
	et_debug( "You're Doing It Wrong! Attempted to call " . __FUNCTION__ . "(), use et_core_sanitized_previously() instead." );
	return $value;
}
endif;

if ( ! function_exists( 'et_core_sanitized_previously' ) ):
/**
 * Semantical previously sanitized acknowledgement
 *
 * @since 3.17.3
 *
 * @param mixed $value The value being passed-through
 *
 * @return mixed
 */
function et_core_sanitized_previously( $value ) {
	return $value;
}
endif;

if ( ! function_exists( 'et_core_esc_attr' ) ):
/**
 * Escape attribute value
 *
 * @since 3.27.1?
 *
 * @param string $attr_key Element attribute key.
 * @param (string|array) $attr_value Element attribute value.
 *
 * @return (string|array|WP_Error)
 */
function et_core_esc_attr( $attr_key, $attr_value ) {
	// Skip validation for landscape image default value.
	$image_landscape = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTA4MCIgaGVpZ2h0PSI1NDAiIHZpZXdCb3g9IjAgMCAxMDgwIDU0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICAgIDxnIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPHBhdGggZmlsbD0iI0VCRUJFQiIgZD0iTTAgMGgxMDgwdjU0MEgweiIvPgogICAgICAgIDxwYXRoIGQ9Ik00NDUuNjQ5IDU0MGgtOTguOTk1TDE0NC42NDkgMzM3Ljk5NSAwIDQ4Mi42NDR2LTk4Ljk5NWwxMTYuMzY1LTExNi4zNjVjMTUuNjItMTUuNjIgNDAuOTQ3LTE1LjYyIDU2LjU2OCAwTDQ0NS42NSA1NDB6IiBmaWxsLW9wYWNpdHk9Ii4xIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KICAgICAgICA8Y2lyY2xlIGZpbGwtb3BhY2l0eT0iLjA1IiBmaWxsPSIjMDAwIiBjeD0iMzMxIiBjeT0iMTQ4IiByPSI3MCIvPgogICAgICAgIDxwYXRoIGQ9Ik0xMDgwIDM3OXYxMTMuMTM3TDcyOC4xNjIgMTQwLjMgMzI4LjQ2MiA1NDBIMjE1LjMyNEw2OTkuODc4IDU1LjQ0NmMxNS42Mi0xNS42MiA0MC45NDgtMTUuNjIgNTYuNTY4IDBMMTA4MCAzNzl6IiBmaWxsLW9wYWNpdHk9Ii4yIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KICAgIDwvZz4KPC9zdmc+Cg==';
	if ( $attr_value === $image_landscape ) {
		return $attr_value;
	}

	// Skip validation for portrait image default value.
	$image_portrait = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAwIiBoZWlnaHQ9IjUwMCIgdmlld0JveD0iMCAwIDUwMCA1MDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiAgICA8ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPgogICAgICAgIDxwYXRoIGZpbGw9IiNFQkVCRUIiIGQ9Ik0wIDBoNTAwdjUwMEgweiIvPgogICAgICAgIDxyZWN0IGZpbGwtb3BhY2l0eT0iLjEiIGZpbGw9IiMwMDAiIHg9IjY4IiB5PSIzMDUiIHdpZHRoPSIzNjQiIGhlaWdodD0iNTY4IiByeD0iMTgyIi8+CiAgICAgICAgPGNpcmNsZSBmaWxsLW9wYWNpdHk9Ii4xIiBmaWxsPSIjMDAwIiBjeD0iMjQ5IiBjeT0iMTcyIiByPSIxMDAiLz4KICAgIDwvZz4KPC9zdmc+Cg==';
	if ( $attr_value === $image_portrait ) {
		return $attr_value;
	}

	$attr_key = strtolower( $attr_key );

	$allowed_attrs_default = array(
		// Filter style.
		// @see https://developer.wordpress.org/reference/functions/safecss_filter_attr/
		'style'       => 'safecss_filter_attr',

		// We just pick some of the HTML attributes considered safe to use.
		'data-*'      => 'esc_attr',
		'align'       => 'esc_attr',
		'alt'         => 'esc_attr',
		'autofocus'   => 'esc_attr',
		'autoplay'    => 'esc_attr',
		'class'       => 'esc_attr',
		'cols'        => 'esc_attr',
		'controls'    => 'esc_attr',
		'disabled'    => 'esc_attr',
		'height'      => 'esc_attr',
		'id'          => 'esc_attr',
		'max'         => 'esc_attr',
		'min'         => 'esc_attr',
		'multiple'    => 'esc_attr',
		'name'        => 'esc_attr',
		'placeholder' => 'esc_attr',
		'required'    => 'esc_attr',
		'rows'        => 'esc_attr',
		'size'        => 'esc_attr',
		'sizes'       => 'esc_attr',
		'srcset'      => 'esc_attr',
		'step'        => 'esc_attr',
		'tabindex'    => 'esc_attr',
		'target'      => 'esc_attr',
		'title'       => 'esc_attr',
		'type'        => 'esc_attr',
		'value'       => 'esc_attr',
		'width'       => 'esc_attr',

		// We just pick some of the HTML attributes containing a URL.
		// @see https://developer.wordpress.org/reference/functions/wp_kses_uri_attributes/
		'action'      => 'esc_url',
		'background'  => 'esc_url',
		'formaction'  => 'esc_url',
		'href'        => 'esc_url',
		'icon'        => 'esc_url',
		'poster'      => 'esc_url',
		'src'         => 'esc_url',
		'usemap'      => 'esc_url',
	);

	/**
	 * Filters allowed attributes
	 *
	 * @since 3.27.1?
	 *
	 * @param array  $allowed_attrs_default Key/value paired array, the key used as the attribute identifier, 
	 *                                      the value used as the callback to escape the value.
	 * @param string $attr_key              Element attribute key.
	 * @param (string|array) $attr_value    Element attribute value.
	 *
	 * @return array
	 */
	$allowed_attrs = apply_filters( 'et_core_esc_attr', $allowed_attrs_default, $attr_key, $attr_value );

	// Get attribute key callback.
	$callback = isset( $allowed_attrs[ $attr_key ] ) && 'data-*' !== $attr_key ? $allowed_attrs[ $attr_key ] : false;

	// Get data attribute key callback.
	if ( ! $callback && 'data-*' !== $attr_key && 0 === strpos( $attr_key, 'data-' ) && isset( $allowed_attrs['data-*'] ) ) {
		$callback = $allowed_attrs['data-*'];
	}

	if ( ! $callback || ! is_callable( $callback ) ) {
		return new WP_Error( 'invalid_attr_key', __( 'Invalid attribute key', 'et_builder' ) );
	}

	if ( is_array( $attr_value ) ) {
		return array_map( $callback, $attr_value );
	}

	// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
	return call_user_func( $callback, $attr_value );
}
endif;

if ( ! function_exists( 'et_core_sanitize_element_tag' ) ):
/**
 * Sanitize element tag
 *
 * @since 3.27.1?
 *
 * @param string $tag Element tag.
 *
 * @return (string|WP_Error)
 */
function et_core_sanitize_element_tag( $tag ) {
	$tag      = strtolower( $tag );
	$headings = array(
		'h1',
		'h2',
		'h3',
		'h4',
		'h5',
		'h6',
	);

	// Bail early for heading tags.
	if ( in_array( $tag, $headings, true ) ) {
		return $tag;
	}

	$tag = preg_replace( '/[^a-z]/', '', $tag );

	$disallowed_tags = array(
		'applet',
		'body',
		'canvas',
		'command',
		'content',
		'element',
		'embed',
		'frame',
		'frameset',
		'head',
		'html',
		'iframe',
		'noembed',
		'noframes',
		'noscript',
		'object',
		'param',
		'script',
		'shadow',
		'slot',
		'style',
		'template',
		'title',
		'xml',
	);

	if ( empty( $tag ) || in_array( $tag, $disallowed_tags, true ) ) {
		return new WP_Error( 'invalid_element_tag', __( 'Invalid tag element', 'et_builder' ) );
	}

	return $tag;
}
endif;

/**
 * Pass thru semantical previously escaped acknowledgement
 *
 * @since 3.17.3
 *
 * @param string value being passed through
 * @return string
 */
if ( ! function_exists( 'et_core_esc_previously' ) ) :
function et_core_esc_previously( $passthru ) {
	return $passthru;
}
endif;

/**
 * Pass thru function used to pacfify phpcs sniff.
 * Used when the nonce is checked elsewhere.
 *
 * @since 3.17.3
 *
 * @return void
 */
if ( ! function_exists( 'et_core_nonce_verified_previously' ) ) :
function et_core_nonce_verified_previously() {
	// :)
}
endif;

/**
 * Pass thru semantical escaped by WordPress core acknowledgement
 *
 * @since 3.17.3
 *
 * @param string value being passed through
 * @return string
 */
if ( ! function_exists( 'et_core_esc_wp' ) ) :
function et_core_esc_wp( $passthru ) {
	return $passthru;
}
endif;

/**
 * Pass thru semantical intentionally unescaped acknowledgement
 *
 * @since 3.17.3
 *
 * @param string value being passed through
 * @param string excuse the value is allowed to be unescaped
 * @return string
 */
if ( ! function_exists( 'et_core_intentionally_unescaped' ) ) :
function et_core_intentionally_unescaped( $passthru, $excuse ) {
	// Add valid excuses as they arise
	$valid_excuses = array(
		'cap_based_sanitized',
		'fixed_string',
		'react_jsx',
		'html',
		'underscore_template',
	);

	if ( ! in_array( $excuse, $valid_excuses ) ) {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'This is not a valid excuse to not escape the passed value.', 'et_core' ), esc_html( et_get_theme_version() ) );
	}

	return $passthru;
}
endif;

/**
 * Sanitize value depending on user capability
 *
 * @since 3.17.3
 *
 * @return string value being passed through
 */
if ( ! function_exists( 'et_core_sanitize_value_by_cap' ) ) :
function et_core_sanitize_value_by_cap( $passthru, $sanitize_function = 'et_sanitize_html_input_text', $cap = 'unfiltered_html' ) {
	if ( ! current_user_can( $cap ) ) {
		$passthru = $sanitize_function( $passthru );
	}

	return $passthru;
}
endif;

/**
 * Pass thru semantical intentionally unsanitized acknowledgement
 *
 * @since 3.17.3
 *
 * @param string value being passed through
 * @param string excuse the value is allowed to be unsanitized
 * @return string
 */
if ( ! function_exists( 'et_core_intentionally_unsanitized' ) ) :
function et_core_intentionally_unsanitized( $passthru, $excuse ) {
	// Add valid excuses as they arise
	$valid_excuses = array();

	if ( ! in_array( $excuse, $valid_excuses ) ) {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'This is not a valid excuse to not sanitize the passed value.', 'et_core' ), esc_html( et_get_theme_version() ) );
	}

	return $passthru;
}
endif;

/**
 * Fixes unclosed HTML tags
 *
 * @since 3.18.4
 *
 * @param string $content source HTML
 *
 * @return string
 */
if ( ! function_exists( 'et_core_fix_unclosed_html_tags' ) ):
function et_core_fix_unclosed_html_tags( $content ) {
	// Exit if source has no HTML tags or we miss what we need to fix them anyway.
	if ( false === strpos( $content, '<' ) || ! class_exists( 'DOMDocument' ) ) {
		return $content;
	}

	$scripts = false;

	if ( false !== strpos( $content, '<script' ) ) {
		// Replace scripts with placeholders so we don't mess with HTML included in JS strings.
		$scripts = new ET_Core_Data_ScriptReplacer();
		$content = preg_replace_callback( '|<script.*?>[\s\S]+?</script>|', array( $scripts, 'replace' ), $content );
	}

	$doc = new DOMDocument();
	@$doc->loadHTML( sprintf(
		'<html><head>%s</head><body>%s</body></html>',
		// Use WP charset
		sprintf( '<meta http-equiv="content-type" content="text/html; charset=%s" />', get_bloginfo( 'charset' ) ),
		$content
	) );

	if ( preg_match( '|<body>([\s\S]+)</body>|', $doc->saveHTML(), $matches ) ) {
		// Extract the fixed content.
		$content = $matches[1];
	}

	if ( $scripts ) {
		// Replace placeholders with scripts.
		$content = strtr( $content, $scripts->map() );
	}

	return $content;
}
endif;


/**
 * Converts string to UTF-8 if mb_convert_encoding function exists
 *
 * @since 3.19.17
 *
 * @param string $string source string
 *
 * @return string
 */
if ( ! function_exists( 'et_core_maybe_convert_to_utf_8' ) ):
function et_core_maybe_convert_to_utf_8( $string ) {
	if ( function_exists( 'mb_convert_encoding' ) ) {
		return mb_convert_encoding( $string, 'UTF-8' );
	}

	return $string;
}
endif;