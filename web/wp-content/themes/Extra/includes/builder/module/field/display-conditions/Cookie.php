<?php
/**
 * Cookie condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Cookie Condition Trait.
 */
trait CookieCondition {

	/**
	 * Processes "Cookie" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_cookie_condition( $condition_settings ) {
		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule    = isset( $condition_settings['cookieDisplay'] ) ? $condition_settings['cookieDisplay'] : 'cookieExists';
		$display_rule           = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$cookie_name            = isset( $condition_settings['cookieName'] ) ? $condition_settings['cookieName'] : '';
		$cookie_value           = isset( $condition_settings['cookieValue'] ) ? $condition_settings['cookieValue'] : '';
		$is_cookie_set          = ( isset( $_COOKIE[ $cookie_name ] ) ) ? true : false;
		$is_cookie_value_equals = ( isset( $_COOKIE[ $cookie_name ] ) ) ? $cookie_value === $_COOKIE[ $cookie_name ] : false;

		switch ( $display_rule ) {
			case 'cookieExists':
				return $is_cookie_set;

			case 'cookieDoesNotExist':
				return ! $is_cookie_set;

			case 'cookieValueEquals':
				return $is_cookie_value_equals;

			case 'cookieValueDoesNotEqual':
				return ! $is_cookie_value_equals;

			default:
				return false;
		}
	}

}
