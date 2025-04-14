<?php
/**
 * Compatibility code that deals with 3P Services which are not integrated via
 * WP Plugins (eg. code added via integration / Code Module) because not hosted locally.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.10.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Disable JQuery Body feature when certain 3P form services are used.
// phpcs:disable PEAR.Functions.FunctionCallSignature.MultipleArguments -- Anonymous functions.
// phpcs:disable PEAR.Functions.FunctionCallSignature.ContentAfterOpenBracket -- Anonymous functions.
// phpcs:disable PEAR.Functions.FunctionCallSignature.CloseBracketLine -- Anonymous functions.
add_filter( 'et_builder_enable_jquery_body', function( $enabled, $content = '' ) {
	if ( empty( $content ) ) {
		return $enabled;
	}

	$services = [
		'et_builder_disable_jquery_body',
		'slick.js',
		'webforms/bbox-min.js',
		'www.cognitoforms.com',
		'mailchimp.com',
		'mindbodyonline.com/javascripts',
		'static.smartrecruiters.com/job-widget/',
		'default.salsalabs.org/api/widget/',
	];

	$services = array_filter( $services, 'preg_quote' );
	$pattern  = '#(' . implode( '|', $services ) . ')#';

	// Disable when the service is found.
	return 1 === preg_match( $pattern, $content ) ? false : $enabled;
}, 10, 2);
// phpcs:enable
