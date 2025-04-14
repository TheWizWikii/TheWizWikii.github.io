<?php
/**
 * User Role Condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * User Role Condition Trait.
 */
trait UserRoleCondition {

	/**
	 * Processes "User Role" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_user_role_condition( $condition_settings ) {
		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule = isset( $condition_settings['userRoleDisplay'] ) ? $condition_settings['userRoleDisplay'] : 'is';
		$display_rule        = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$roles_raw           = isset( $condition_settings['userRoles'] ) ? $condition_settings['userRoles'] : [];
		$ids_raw             = isset( $condition_settings['userIds'] ) ? $condition_settings['userIds'] : '';
		$roles               = array_map(
			function( $item ) {
				return $item['value'];
			},
			$roles_raw
		);
		$ids                 = isset( $ids_raw ) ? array_map( 'trim', array_filter( explode( ',', $ids_raw ) ) ) : array();
		$user                = wp_get_current_user();

		$should_display_based_on_roles = array_intersect( $roles, (array) $user->roles ) ? true : false;
		$should_display_based_on_ids   = array_intersect( $ids, (array) $user->ID ) ? true : false;
		$should_display                = ( $should_display_based_on_roles || $should_display_based_on_ids );

		return ( 'is' === $display_rule ) ? $should_display : ! $should_display;
	}

	/**
	 * Checks user role for possible conflicts.
	 *
	 * @since 4.11.0
	 *
	 * @param string $current_value                  Currently processing condition's conflicting value.
	 * @param string $prev_value                     Previously processed condition's conflicting value.
	 * @param array  $conflicting_value              Array of values containing the conflicting values as defined in $this->conflicts.
	 * @param string $current_display_rule           Currently processing condition's display rule Ex. is,isNot...
	 * @param string $prev_display_rule              Previously processed condition's display rule Ex. is,isNot...
	 * @param array  $conflicting_display_rule_vals  Array of values containing the conflicting display rules as defined in $this->conflicts.
	 *
	 * @return boolean Conflict evaluation result.
	 */
	protected function _is_user_role_conflicted( $current_value, $prev_value, $conflicting_value, $current_display_rule, $prev_display_rule, $conflicting_display_rule_vals ) {
		$current_value                      = explode( '|', $current_value );
		$prev_value                         = explode( '|', $prev_value );
		$is_current_value_conflicted        = ! empty( array_intersect( $current_value, $conflicting_value ) );
		$is_prev_value_conflicted           = ! empty( array_intersect( $prev_value, $conflicting_value ) );
		$is_current_display_rule_conflicted = in_array( $current_display_rule, $conflicting_display_rule_vals, true );
		$is_prev_display_rule_conflicted    = in_array( $prev_display_rule, $conflicting_display_rule_vals, true );
		if ( $is_current_value_conflicted && $is_prev_value_conflicted && $is_current_display_rule_conflicted && $is_prev_display_rule_conflicted ) {
			return true;
		}
		return false;
	}

}
