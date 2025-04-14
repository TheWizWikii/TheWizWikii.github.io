<?php
/**
 * Number of Views condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Number of Views Condition Trait.
 */
trait NumberOfViewsCondition {

	/**
	 * Processes "Number of Views" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_id       Condition ID.
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_number_of_views_condition( $condition_id, $condition_settings ) {

		if ( ! isset( $_COOKIE['divi_module_views'] ) ) {
			return true;
		}

		// Get condition's settings.
		$number_of_views  = isset( $condition_settings['numberOfViews'] ) ? $condition_settings['numberOfViews'] : '0';
		$cookie_array     = [];
		$visit_count      = 0;
		$current_datetime = current_datetime();
		$cookie_array     = json_decode( base64_decode( $_COOKIE['divi_module_views'] ), true ); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput, WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode  -- Cookie is not stored or displayed therefore XSS safe, The returned data is an array and necessary validation checks are performed.

		if ( ! is_array( $cookie_array ) ) {
			return true;
		}

		// Logic evaluation.
		$col                        = array_column( $cookie_array, 'id' );
		$is_condition_set_in_cookie = array_search( $condition_id, $col, true ) !== false;

		if ( ! $is_condition_set_in_cookie ) {
			// Display module if condition is not set in Cookie yet.
			return true;
		}

		$is_reset_after_duration_on = 'on' === $condition_settings['resetAfterDuration'] ? true : false;

		if ( $is_reset_after_duration_on ) {
			$first_visit_timestamp  = $cookie_array[ $condition_id ]['first_visit_timestamp'];
			$display_again_after    = $condition_settings['displayAgainAfter'] . ' ' . $condition_settings['displayAgainAfterUnit'];
			$first_visit_datetime   = $current_datetime->setTimestamp( $first_visit_timestamp );
			$display_again_datetime = $first_visit_datetime->modify( $display_again_after );
			if ( $current_datetime > $display_again_datetime ) {
				return true;
			}
		}

		$visit_count = $cookie_array[ $condition_id ]['visit_count'];

		if ( (int) $visit_count >= (int) $number_of_views ) {
			$is_displayable = false;
		} else {
			$is_displayable = true;
		}

		// Evaluation output.
		return $is_displayable;

	}

}
