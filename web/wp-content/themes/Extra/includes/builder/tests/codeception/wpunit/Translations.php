<?php

namespace ET\Builder\Codeception\WPUnit;

/**
 * Records how many time gettext is called for the same string.
 *
 * @since 4.4.9
 */
class Translations {
	protected static $stats = array();

	public static function gettext( $translation, $text, $domain ) {
		if ( isset( self::$stats[ $domain ][ $text ] ) ) {
			self::$stats[ $domain ][ $text ] += 1;
		} else {
			self::$stats[ $domain ][ $text ] = 1;
		}
		return $translation;
	}

	public static function gettext_with_context( $translation, $text, $context, $domain ) {
		return self::gettext( $translation, $text, $domain );
	}

	public static function stats() {
		return self::$stats;
	}

	public static function add_filters() {
		// Add gettext filters.
		add_filter( 'gettext', __CLASS__ . '::gettext', 99, 3 );
		add_filter( 'gettext_with_context', __CLASS__ . '::gettext_with_context', 99, 4 );
	}
}

Translations::add_filters();
