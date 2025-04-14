<?php
/**
 * Date Archive condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

use DateTimeImmutable;

/**
 * Date Archive Condition Trait.
 */
trait DateArchiveCondition {

	/**
	 * Processes "Date Archive" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_date_archive_condition( $condition_settings ) {
		if ( ! is_date() ) {
			return false;
		}

		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule = isset( $condition_settings['dateArchiveDisplay'] ) ? $condition_settings['dateArchiveDisplay'] : 'isAfter';
		$display_rule        = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$date                = isset( $condition_settings['dateArchive'] ) ? $condition_settings['dateArchive'] : '';

		$year         = get_query_var( 'year' );
		$monthnum     = get_query_var( 'monthnum' ) === 0 ? 1 : get_query_var( 'monthnum' );
		$day          = get_query_var( 'day' ) === 0 ? 1 : get_query_var( 'day' );
		$archive_date = sprintf( '%s-%s-%s', $year, $monthnum, $day );

		$target_date         = new DateTimeImmutable( $date, wp_timezone() );
		$current_arhive_date = new DateTimeImmutable( $archive_date, wp_timezone() );

		switch ( $display_rule ) {
			case 'isAfter':
				return ( $current_arhive_date > $target_date );

			case 'isBefore':
				return ( $current_arhive_date < $target_date );

			default:
				return ( $current_arhive_date > $target_date );
		}
	}

}
