<?php
/**
 * Date Time condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

use DateTimeImmutable;

/**
 * Date Time Condition Trait.
 */
trait DateTimeCondition {

	/**
	 * Processes "Date & Time" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_date_time_condition( $condition_settings ) {
		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule            = isset( $condition_settings['dateTimeDisplay'] ) ? $condition_settings['dateTimeDisplay'] : 'isAfter';
		$display_rule                   = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$date                           = isset( $condition_settings['date'] ) ? $condition_settings['date'] : '';
		$time                           = isset( $condition_settings['time'] ) ? $condition_settings['time'] : '';
		$all_day                        = isset( $condition_settings['allDay'] ) ? $condition_settings['allDay'] : '';
		$from_time                      = isset( $condition_settings['fromTime'] ) ? $condition_settings['fromTime'] : '';
		$until_time                     = isset( $condition_settings['untilTime'] ) ? $condition_settings['untilTime'] : '';
		$weekdays                       = isset( $condition_settings['weekdays'] ) ? array_filter( explode( '|', $condition_settings['weekdays'] ) ) : array();
		$repeat_frequency               = isset( $condition_settings['repeatFrequency'] ) ? $condition_settings['repeatFrequency'] : '';
		$repeat_frequency_specific_days = isset( $condition_settings['repeatFrequencySpecificDays'] ) ? $condition_settings['repeatFrequencySpecificDays'] : '';

		$date_from_time  = $date . ' ' . $from_time;
		$date_until_time = $date . ' ' . $until_time;
		$date_time       = $date . ' ' . $time;

		$target_date           = new DateTimeImmutable( $date, wp_timezone() );
		$target_datetime       = new DateTimeImmutable( $date_time, wp_timezone() );
		$target_from_datetime  = new DateTimeImmutable( $date_from_time, wp_timezone() );
		$target_until_datetime = new DateTimeImmutable( $date_until_time, wp_timezone() );

		$current_datetime       = ! empty( $this->_custom_current_date ) ? $this->_custom_current_date : current_datetime();
		$current_datetime_from  = $current_datetime->modify( $from_time );
		$current_datetime_until = $current_datetime->modify( $until_time );

		switch ( $display_rule ) {
			case 'isAfter':
				return ( $current_datetime > $target_datetime );

			case 'isBefore':
				return ( $current_datetime < $target_datetime );

			case 'isOnSpecificDate':
				$has_reached_target_datetime = $current_datetime >= $target_date;
				$has_time_until_tomorrow     = $current_datetime < $target_date->modify( 'tomorrow' );
				if ( 'off' === $all_day ) {
					$has_reached_target_datetime = $current_datetime >= $target_from_datetime;
					$has_time_until_tomorrow     = $current_datetime < $target_until_datetime;
				}
				$is_on_specific_date = ( $has_reached_target_datetime && $has_time_until_tomorrow );
				$is_repeated         = $this->_is_datetime_condition_repeated( $condition_settings, $is_on_specific_date, $current_datetime, $target_datetime );
				return ( $is_on_specific_date || $is_repeated );

			case 'isNotOnSpecificDate':
				$has_reached_target_datetime = $current_datetime >= $target_date;
				$has_time_until_tomorrow     = $current_datetime < $target_date->modify( 'tomorrow' );
				if ( 'off' === $all_day ) {
					$has_reached_target_datetime = $current_datetime >= $target_from_datetime;
					$has_time_until_tomorrow     = $current_datetime < $target_until_datetime;
				}
				return ! ( $has_reached_target_datetime && $has_time_until_tomorrow );

			case 'isOnSpecificDays':
				$current_day                 = strtolower( $current_datetime->format( 'l' ) );
				$is_on_selected_day          = array_intersect( (array) $current_day, $weekdays ) ? true : false;
				$has_reached_target_datetime = true;
				$has_time_until_tomorrow     = true;
				if ( 'off' === $all_day ) {
					$has_reached_target_datetime = $current_datetime >= $current_datetime_from;
					$has_time_until_tomorrow     = $current_datetime < $current_datetime_until;
				}
				$is_repeated         = $this->_is_datetime_condition_repeated( $condition_settings, $is_on_selected_day, $current_datetime, $target_datetime );
				$is_on_specific_days = $is_on_selected_day && $has_reached_target_datetime && $has_time_until_tomorrow;
				return ( 'weekly' === $repeat_frequency_specific_days ) ? $is_on_specific_days : $is_repeated;

			case 'isFirstDayOfMonth':
				$is_first_day_of_month       = $current_datetime->format( 'd' ) === '01';
				$has_reached_target_datetime = true;
				$has_time_until_tomorrow     = true;
				if ( 'off' === $all_day ) {
					$has_reached_target_datetime = $current_datetime >= $current_datetime_from;
					$has_time_until_tomorrow     = $current_datetime < $current_datetime_until;
				}
				return ( $is_first_day_of_month && $has_reached_target_datetime && $has_time_until_tomorrow );

			case 'isLastDayOfMonth':
				$last_day_of_month           = new DateTimeImmutable( 'last day of this month', wp_timezone() );
				$is_last_day_of_month        = $current_datetime->format( 'd' ) === $last_day_of_month->format( 'd' );
				$has_reached_target_datetime = true;
				$has_time_until_tomorrow     = true;
				if ( 'off' === $all_day ) {
					$has_reached_target_datetime = $current_datetime >= $current_datetime_from;
					$has_time_until_tomorrow     = $current_datetime < $current_datetime_until;
				}
				return ( $is_last_day_of_month && $has_reached_target_datetime && $has_time_until_tomorrow );

			default:
				return ( $current_datetime >= $target_datetime );
		}
	}

	/**
	 * Checks whether a condition should be repeated or not.
	 *
	 * @since 4.11.0
	 *
	 * @param array             $condition_settings  Contains all settings of the condition.
	 * @param boolean           $is_on_specific_date Specifies if "Is On Specific Date" condition has already
	 *                                               reached that specific date or not.
	 *                                               Useful to avoid repetition checking if condition is already
	 *                                               true and also for "Every Other" repeat frequency.
	 * @param DateTimeImmutable $current_datetime    The current date and time to use.
	 * @param DateTimeImmutable $target_datetime     To detect Monthly/Annually repetition and "After Number of times".
	 *
	 * @return boolean          Condition repetition result.
	 */
	protected function _is_datetime_condition_repeated( $condition_settings, $is_on_specific_date, $current_datetime, $target_datetime ) {
		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule            = isset( $condition_settings['dateTimeDisplay'] ) ? $condition_settings['dateTimeDisplay'] : 'isAfter';
		$display_rule                   = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$repeat                         = isset( $condition_settings['repeat'] ) ? $condition_settings['repeat'] : '';
		$repeat_frequency               = isset( $condition_settings['repeatFrequency'] ) ? $condition_settings['repeatFrequency'] : '';
		$repeat_frequency_specific_days = isset( $condition_settings['repeatFrequencySpecificDays'] ) ? $condition_settings['repeatFrequencySpecificDays'] : '';
		$repeat_end                     = isset( $condition_settings['repeatEnd'] ) ? $condition_settings['repeatEnd'] : '';
		$repeat_until                   = isset( $condition_settings['repeatUntilDate'] ) ? $condition_settings['repeatUntilDate'] : '';
		$repeat_times                   = isset( $condition_settings['repeatTimes'] ) ? $condition_settings['repeatTimes'] : '';
		$all_day                        = isset( $condition_settings['allDay'] ) ? $condition_settings['allDay'] : '';
		$from_time                      = isset( $condition_settings['fromTime'] ) ? $condition_settings['fromTime'] : '';
		$until_time                     = isset( $condition_settings['untilTime'] ) ? $condition_settings['untilTime'] : '';
		$is_repeated                    = false;
		$is_on_specific_days            = 'isOnSpecificDays' === $display_rule;

		if ( $is_on_specific_days || ( 'on' === $repeat && ! $is_on_specific_date ) ) {
			if ( $is_on_specific_days ) {
				$is_day_repeated = $this->_is_day_repeated( $repeat_frequency_specific_days, $is_on_specific_date, $current_datetime, $target_datetime );
			} else {
				$is_day_repeated = $this->_is_day_repeated( $repeat_frequency, $is_on_specific_date, $current_datetime, $target_datetime );
			}
			$is_repeat_valid = false;

			switch ( $repeat_end ) {
				case 'untilDate':
					$is_repeat_valid = $current_datetime <= new DateTimeImmutable( $repeat_until, wp_timezone() );
					break;

				case 'afterNumberOfTimes':
					$target_date_after_number_of_times = $target_datetime->modify( '+' . $repeat_times . ' month' );
					if ( 'annually' === $repeat_frequency ) {
						$target_date_after_number_of_times = $target_datetime->modify( '+' . $repeat_times . ' year' );
					}
					if ( 'off' === $all_day ) {
						$target_date_after_number_of_times = $target_date_after_number_of_times->modify( $until_time );
					}
					$is_repeat_valid = $current_datetime <= $target_date_after_number_of_times;
					break;

				case 'never':
					$is_repeat_valid = true;
					break;

				default:
					$is_repeat_valid = true;
					break;
			}

			// We assume "All Day" switch is "On".
			$has_reached_from_time  = $is_day_repeated;
			$has_reached_until_time = $current_datetime < $current_datetime->modify( 'tomorrow' );

			// Calculate from time/until time if "All Day" switch is "Off".
			if ( 'off' === $all_day ) {
				$has_reached_from_time  = $current_datetime >= $current_datetime->modify( $from_time );
				$has_reached_until_time = $current_datetime < $current_datetime->modify( $until_time );
			}

			$is_repeated = $is_day_repeated && $has_reached_from_time && $has_reached_until_time && $is_repeat_valid;
		}

		return $is_repeated;
	}

	/**
	 * Checks whether a day is repeated or not.
	 *
	 * @since 4.11.0
	 *
	 * @param string            $repeat_frequency    Frequency of repeat Ex. monthly, annually, everyOther...
	 * @param boolean           $is_on_specific_date Useful for "Every Other" repeat frequency.
	 * @param DateTimeImmutable $current_datetime    The current date and time to use.
	 * @param DateTimeImmutable $target_datetime     Checks monthly/annually repetition against this Date and Time.
	 *
	 * @return boolean          Day repetition result.
	 */
	protected function _is_day_repeated( $repeat_frequency, $is_on_specific_date, $current_datetime, $target_datetime ) {
		switch ( $repeat_frequency ) {
			case 'monthly':
				return ( $current_datetime->format( 'd' ) === $target_datetime->format( 'd' ) );

			case 'annually':
				return ( $current_datetime->format( 'm d' ) === $target_datetime->format( 'm d' ) );

			case 'everyOther':
				return ! $is_on_specific_date;

			case 'firstInstanceOfMonth':
				return ( $current_datetime->format( 'Y-m-d' ) === $current_datetime->modify( 'first ' . $current_datetime->format( 'l' ) . ' of this month' )->format( 'Y-m-d' ) );

			case 'lastInstanceOfMonth':
				return ( $current_datetime->format( 'Y-m-d' ) === $current_datetime->modify( 'last ' . $current_datetime->format( 'l' ) . ' of this month' )->format( 'Y-m-d' ) );

			default:
				return false;
		}
	}

	/**
	 * Checks date and time for possible conflicts.
	 *
	 * @since 4.11.0
	 *
	 * @param string $current_display_rule           Value of currently processing condition's display rule Ex. is,isNot...
	 * @param string $prev_display_rule              Value of previously processed condition's display rule Ex. is,isNot...
	 * @param array  $conflicting_display_rule_vals  Array of values containing the conflicting display rules as defined in $this->conflicts.
	 *
	 * @return boolean Conflict evaluation result.
	 */
	protected function _is_date_time_conflicted( $current_display_rule, $prev_display_rule, $conflicting_display_rule_vals ) {
		$is_current_display_rule_conflicted = $this->_in_array_conflict( $current_display_rule, $conflicting_display_rule_vals );
		$is_prev_display_rule_conflicted    = $this->_in_array_conflict( $prev_display_rule, $conflicting_display_rule_vals );
		$is_from_same_group                 = $is_current_display_rule_conflicted['index'] === $is_prev_display_rule_conflicted['index'];
		if ( $is_current_display_rule_conflicted['value'] && $is_prev_display_rule_conflicted['value'] && ! $is_from_same_group ) {
			return true;
		}
		return false;
	}

}
