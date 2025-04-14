<?php
/**
 * Operating System Condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Operating System Condition Trait.
 */
trait OperatingSystemCondition {

	/**
	 * Processes "Operating System" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_operating_system_condition( $condition_settings ) {
		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule   = isset( $condition_settings['operatingSystemDisplay'] ) ? $condition_settings['operatingSystemDisplay'] : 'is';
		$display_rule          = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$operating_systems_raw = isset( $condition_settings['operatingSystems'] ) ? $condition_settings['operatingSystems'] : '';
		$operating_systems     = explode( '|', $operating_systems_raw );
		$current_os            = $this->_get_os();

		$should_display = array_intersect( $operating_systems, (array) $current_os ) ? true : false;
		return ( 'is' === $display_rule ) ? $should_display : ! $should_display;
	}


	/**
	 * Returns the Operating System name based on user agent.
	 *
	 * @since 4.11.0
	 *
	 * @return string
	 */
	protected function _get_os() {
		$os_platform = 'unknown';
		// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput  -- User Agent is not stored or displayed therefore XSS safe.
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$os_array   = array(
			'/windows nt/i'         => 'windows',
			'/macintosh|mac os x/i' => 'macos',
			'/linux/i'              => 'linux',
			'/android/i'            => 'android',
			'/iphone/i'             => 'iphone',
			'/ipad/i'               => 'ipad',
			'/ipod/i'               => 'ipod',
			'/appletv/i'            => 'appletv',
			'/playstation/i'        => 'playstation',
			'/xbox/i'               => 'xbox',
			'/nintendo/i'           => 'nintendo',
			'/webos|hpwOS/i'        => 'webos',
		);

		foreach ( $os_array as $regex => $value ) {
			if ( preg_match( $regex, $user_agent ) ) {
				$os_platform = $value;
			}
		}
		return $os_platform;
	}

}
