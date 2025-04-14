<?php
/**
 * URL Parameter Condition logic swiftly crafted.
 *
 * @since 4.14.3
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * URL Parameter Condition Trait.
 */
trait UrlParameterCondition {

	/**
	 * Processes "URL Parameter" condition.
	 *
	 * @since 4.14.3
	 *
	 * @param  array $condition_settings The Condition settings containing:
	 *                                   'displayRule' => string,
	 *                                   'selectUrlParameter' => string,
	 *                                   'urlParameterName' => string,
	 *                                   'urlParameterValue' => string.
	 *
	 * @return boolean Returns `true` if the condition evaluation is true, `false` otherwise.
	 */
	protected function _process_url_parameter_condition( $condition_settings ) {
		$display_rule         = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : 'equals';
		$select_url_parameter = isset( $condition_settings['selectUrlParameter'] ) ? $condition_settings['selectUrlParameter'] : 'specificUrlParameter';
		$url_parameter_name   = isset( $condition_settings['urlParameterName'] ) ? (string) $condition_settings['urlParameterName'] : '';
		$url_parameter_value  = isset( $condition_settings['urlParameterValue'] ) ? (string) $condition_settings['urlParameterValue'] : '';

		$get_url_parameter    = isset( $_GET[ $url_parameter_name ] ) ? sanitize_text_field( $_GET[ $url_parameter_name ] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No need to use nonce as there is no form processing.
		$is_url_parameter_set = isset( $_GET[ $url_parameter_name ] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No need to use nonce as there is no form processing.

		if ( 'anyUrlParameter' === $select_url_parameter ) {
			$parameter_values = $this->_get_all_parameter_values();
			$output           = [
				'equals'         => count( $parameter_values ) > 0 && array_intersect( $parameter_values, (array) $url_parameter_value ),
				'exist'          => count( $parameter_values ) > 0,
				'doesNotExist'   => count( $parameter_values ) === 0,
				'doesNotEqual'   => count( $parameter_values ) > 0 && ! array_intersect( $parameter_values, (array) $url_parameter_value ),
				'contains'       => count( $parameter_values ) > 0 && $this->_array_contains_string( $parameter_values, $url_parameter_value ),
				'doesNotContain' => count( $parameter_values ) > 0 && ! $this->_array_contains_string( $parameter_values, $url_parameter_value ),
			];
		} else {
			$output = [
				'equals'         => $is_url_parameter_set && $get_url_parameter === $url_parameter_value,
				'exist'          => $is_url_parameter_set,
				'doesNotExist'   => ! $is_url_parameter_set,
				'doesNotEqual'   => $is_url_parameter_set && $get_url_parameter !== $url_parameter_value,
				'contains'       => $is_url_parameter_set && strpos( $get_url_parameter, $url_parameter_value ) !== false,
				'doesNotContain' => $is_url_parameter_set && strpos( $get_url_parameter, $url_parameter_value ) === false,
			];
		}

		return isset( $output[ $display_rule ] ) ? $output[ $display_rule ] : false;
	}

	/**
	 * Returns all parameter values.
	 *
	 * @since 4.14.3
	 *
	 * @return array
	 */
	protected function _get_all_parameter_values() {
		return array_map(
			function( $value ) {
				return $value;
			},
			$_GET // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No need to use nonce.
		);
	}

	/**
	 * Checks if `$haystack` items contain `$needle` in their values.
	 *
	 * @since 4.14.3
	 *
	 * @param array  $haystack The array to search in.
	 * @param string $needle   The string needle to search for.
	 *
	 * @return boolean
	 */
	protected function _array_contains_string( $haystack, $needle ) {
		$filtered_array = array_filter(
			$haystack,
			function( $value ) use ( $needle ) {
				return strpos( $value, $needle ) !== false;
			}
		);
		return count( $filtered_array ) > 0 ? true : false;
	}

}
