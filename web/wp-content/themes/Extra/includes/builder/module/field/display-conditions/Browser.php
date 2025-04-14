<?php
/**
 * Browser condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Browser Condition Trait.
 */
trait BrowserCondition {

	/**
	 * Processes "Operating System" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_browser_condition( $condition_settings ) {
		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule = isset( $condition_settings['browserDisplay'] ) ? $condition_settings['browserDisplay'] : 'is';
		$display_rule        = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$browsers_raw        = isset( $condition_settings['browsers'] ) ? $condition_settings['browsers'] : '';
		$browsers            = explode( '|', $browsers_raw );
		// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput  -- User Agent is not stored or displayed therefore XSS safe.
		$useragent              = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$is_old_edge            = preg_match( '/edge\//i', $useragent );
		$is_checking_for_chrome = array_search( 'chrome', $browsers, true ) !== false;
		$current_browser        = $this->_get_browser( $useragent );

		// Exception: When checking "Chrome" condition we should treat New Edge as Chrome.
		if ( 'edge' === $current_browser && ! $is_old_edge && $is_checking_for_chrome ) {
			$current_browser = 'chrome';
		}

		$should_display = array_intersect( $browsers, (array) $current_browser ) ? true : false;
		return ( 'is' === $display_rule ) ? $should_display : ! $should_display;
	}

	/**
	 * Returns the Browser name based on user agent.
	 *
	 * @since 4.11.0
	 *
	 * @param  string $useragent The useragent of the berowser.
	 *
	 * @return string Detected browser.
	 */
	protected function _get_browser( $useragent ) {
		$browser       = 'unknown';
		$browser_array = array(
			'/safari/i'           => 'safari',
			'/chrome|CriOS/i'     => 'chrome',
			'/firefox|FxiOS/i'    => 'firefox',
			'/msie|Trident/i'     => 'ie',
			'/edg/i'              => 'edge',
			'/opr|Opera|Presto/i' => 'opera',
			'/maxthon/i'          => 'maxthon',
			'/ucbrowser/i'        => 'ucbrowser',
		);

		foreach ( $browser_array as $regex => $value ) {
			if ( preg_match( $regex, $useragent ) ) {
				$browser = $value;
			}
		}
		return $browser;
	}

}
