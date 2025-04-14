<?php
/**
 * Search Results Condition logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Search Results Condition Trait.
 */
trait SearchResultsCondition {

	/**
	 * Processes "Search Results" condition.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	protected function _process_search_results_condition( $condition_settings ) {
		// Only check for Search.
		if ( ! is_search() ) {
			return false;
		}

		// Checks for additional display rule for compatibility with Conditional Display older versions which didn't use `displayRule` key.
		$legacy_display_rule         = isset( $condition_settings['searchResultsDisplay'] ) ? $condition_settings['searchResultsDisplay'] : 'specificSearchQueries';
		$display_rule                = isset( $condition_settings['displayRule'] ) ? $condition_settings['displayRule'] : $legacy_display_rule;
		$specific_search_queries_raw = isset( $condition_settings['specificSearchQueries'] ) ? $condition_settings['specificSearchQueries'] : '';
		$excluded_search_queries_raw = isset( $condition_settings['excludedSearchQueries'] ) ? $condition_settings['excludedSearchQueries'] : '';
		$specific_search_queries     = explode( ',', $specific_search_queries_raw );
		$excluded_search_queries     = explode( ',', $excluded_search_queries_raw );

		switch ( $display_rule ) {
			case 'specificSearchQueries':
				return $this->_is_specific_search_query( $specific_search_queries );

			case 'excludedSearchQueries':
				return ! $this->_is_specific_search_query( $excluded_search_queries );

			default:
				return false;
		}
	}

	/**
	 * "is specirfic serach query" Condition logic.
	 *
	 * @param array $specific_search_queries Array of search queries.
	 * @return boolean Indicating whether "is specirfic serach query" Condition is true or false.
	 */
	protected function _is_specific_search_query( $specific_search_queries ) {
		$is_specific_search_query = false;
		foreach ( $specific_search_queries as $search_query ) {
			$is_specific_search_query = get_search_query() === $search_query;
			if ( $is_specific_search_query ) {
				break;
			}
		}
		return $is_specific_search_query;
	}

}
