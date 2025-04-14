<?php
/**
 * Logged In Status Condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Logged In Status Condition Trait.
 */
trait LoggedInStatusCondition {

	/**
	 * Processes "Logged In Status" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_logged_in_status_condition( $condition_settings ) {
		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule = isset( $condition_settings['loggedInStatus'] ) ? $condition_settings['loggedInStatus'] : 'loggedIn';
		$display_rule        = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$should_display      = ( is_user_logged_in() ) ? true : false;

		return ( 'loggedIn' === $display_rule ) ? $should_display : ! $should_display;
	}

}
