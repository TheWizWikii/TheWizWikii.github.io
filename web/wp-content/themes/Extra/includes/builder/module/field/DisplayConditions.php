<?php
/**
 * Display Conditions logics lies below, Buckle up!
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Load traits, No autoloader :sad_pepe:
 */
require_once __DIR__ . '/display-conditions/LoggedInStatus.php';
require_once __DIR__ . '/display-conditions/UserRole.php';
require_once __DIR__ . '/display-conditions/DateTime.php';
require_once __DIR__ . '/display-conditions/PostType.php';
require_once __DIR__ . '/display-conditions/Author.php';
require_once __DIR__ . '/display-conditions/Categories.php';
require_once __DIR__ . '/display-conditions/Tags.php';
require_once __DIR__ . '/display-conditions/DateArchive.php';
require_once __DIR__ . '/display-conditions/ProductPurchase.php';
require_once __DIR__ . '/display-conditions/CartContents.php';
require_once __DIR__ . '/display-conditions/SearchResults.php';
require_once __DIR__ . '/display-conditions/OperatingSystem.php';
require_once __DIR__ . '/display-conditions/Browser.php';
require_once __DIR__ . '/display-conditions/PageVisit.php';
require_once __DIR__ . '/display-conditions/DynamicPosts.php';
require_once __DIR__ . '/display-conditions/Cookie.php';
require_once __DIR__ . '/display-conditions/CategoryPage.php';
require_once __DIR__ . '/display-conditions/TagPage.php';
require_once __DIR__ . '/display-conditions/NumberOfViews.php';
require_once __DIR__ . '/display-conditions/CustomField.php';
require_once __DIR__ . '/display-conditions/UrlParameter.php';
require_once __DIR__ . '/display-conditions/ProductStock.php';

/**
 * Import class dependencies
 */
use ET_Builder_Module_Field_Base;

/**
 * Display Conditions class.
 *
 * @since 4.11.0
 */
class ET_Builder_Module_Field_DisplayConditions extends ET_Builder_Module_Field_Base {

	/**
	 * Import traits dependencies.
	 * Keep the code clean and the logic separated, don't be ET_Builder_Element.
	 */
	use LoggedInStatusCondition;
	use UserRoleCondition;
	use DateTimeCondition;
	use PostTypeCondition;
	use AuthorCondition;
	use CategoriesCondition;
	use TagsCondition;
	use DateArchiveCondition;
	use ProductPurchaseCondition;
	use CartContentsCondition;
	use SearchResultsCondition;
	use OperatingSystemCondition;
	use BrowserCondition;
	use PageVisitCondition;
	use DynamicPostsCondition;
	use CookieCondition;
	use CategoryPageCondition;
	use TagPageCondition;
	use NumberOfViewsCondition;
	use CustomFieldCondition;
	use UrlParameterCondition;
	use ProductStockCondition;

	/**
	 * Custom current date.
	 * Useful for testing purposes where we don't want to depend on server's timestamp.
	 *
	 * @since 4.11.0
	 *
	 * @var string
	 */
	protected $_custom_current_date = '';

	/**
	 * Retrieves fields for Display Conditions.
	 * Used in `ET_Builder_ELement` to set Display Conditions fields in Divi Builder.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $args   Associative array for settings.
	 *
	 * @return array $fields Option settings.
	 */
	public function get_fields( array $args = array() ) {

		$defaults = [
			'prefix'         => '',
			'tab_slug'       => 'custom_css',
			'toggle_slug'    => 'conditions',
			'mobile_options' => false,
			'default'        => '',
		];

		$settings = array_merge( $defaults, $args );

		return array_merge(
			$this->get_field( $settings )
		);

	}

	/**
	 * Retrieves field for Display Conditions.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $args    Associative array for settings.
	 *
	 * @return array $options Option settings.
	 */
	public function get_field( $args ) {
		static $i18n;

		// Cache translations.
		if ( ! $i18n ) {
			$i18n = [
				'Display Conditions' => esc_html__( 'Display Conditions', 'et_builder' ),
				'description'        => et_get_safe_localization( sprintf( __( 'Choose when to display this element based on a set of conditions. Multiple conditions can be added. Date & Time condition is based on your timezone settings in your <a href="%1$s" target="_blank" title="WordPress General Settings">WordPress General Settings</a>', 'et_builder' ), esc_url( admin_url( 'options-general.php' ) ) ) ),
			];
		}

		$settings = array(
			'label'          => $i18n['Display Conditions'],
			'type'           => 'display_conditions',
			'mobile_options' => $args['mobile_options'],
			'default'        => $args['default'],
			'tab_slug'       => $args['tab_slug'],
			'toggle_slug'    => $args['toggle_slug'],
			'description'    => $i18n['description'],
		);

		$options = array( 'display_conditions' => $settings );

		return $options;
	}

	/**
	 * Checks all $display_conditions and returns a final boolean output.
	 *
	 * @since 4.11.0
	 *
	 * @param  array   $display_conditions Associative array containing conditions.
	 * @param  boolean $only_return_status Whether to return all conditions full status (useful in VB tooltips).
	 *
	 * @return boolean Conditions final result.
	 */
	public function is_displayable( $display_conditions, $only_return_status = false ) {
		// Bail out and just display the module if $display_conditions is not array.
		if ( ! is_array( $display_conditions ) ) {
			return true;
		}

		// Holds current condition evaluation.
		$should_display = true;
		$status         = [];

		// Reverses condition list, We start from the bottom of the list.
		$display_conditions = array_reverse( $display_conditions );

		// Holds all the conditions that have been processed, except the ones detected as conflicted.
		$processed_conditions = array();

		foreach ( $display_conditions as $arr_key => $condition ) {
			$condition_id            = isset( $condition['id'] ) ? $condition['id'] : '';
			$condition_name          = isset( $condition['condition'] ) ? $condition['condition'] : '';
			$condition_settings      = isset( $condition['conditionSettings'] ) ? $condition['conditionSettings'] : [];
			$operator                = isset( $condition['operator'] ) ? $condition['operator'] : 'OR';
			$is_enable_condition_set = isset( $condition_settings['enableCondition'] ) ? true : false;
			$is_disabled             = $is_enable_condition_set && 'off' === $condition_settings['enableCondition'] ? true : false;

			// Skip if condition is disabled.
			if ( $is_disabled ) {
				$status[] = [
					'id'            => $condition_id,
					'is_conflicted' => false,
				];
				continue;
			}

			$is_conflict_detected = $this->_is_condition_conflicted( $condition, $processed_conditions, $operator );

			$status[] = [
				'id'            => $condition_id,
				'is_conflicted' => $is_conflict_detected,
			];

			if ( $is_conflict_detected ) {
				continue;
			} else {
				$should_display         = $this->is_condition_true( $condition_id, $condition_name, $condition_settings );
				$processed_conditions[] = $condition;
			}

			// If operator is set to "OR/ANY" break as soon as one condition is true - returning a final true.
			// If operator is set to "AND/ALL" break as soon as one condition is false - returning a final false.
			if ( 'OR' === $operator && $should_display && ! $only_return_status ) {
				break;
			} elseif ( 'AND' === $operator && ! $should_display && ! $only_return_status ) {
				break;
			}
		}

		return ( $only_return_status ) ? $status : $should_display;
	}

	/**
	 * Checks a single condition and returns a boolean result.
	 *
	 * @since 4.11.0
	 *
	 * @param  string $condition_id           Condition ID.
	 * @param  string $condition_name         Condition name.
	 * @param  array  $condition_settings Containing all settings of the condition.
	 *
	 * @return boolean Condition output.
	 */
	public function is_condition_true( $condition_id, $condition_name, $condition_settings ) {
		switch ( $condition_name ) {
			case 'loggedInStatus':
				return $this->_process_logged_in_status_condition( $condition_settings );

			case 'userRole':
				return $this->_process_user_role_condition( $condition_settings );

			case 'dateTime':
				return $this->_process_date_time_condition( $condition_settings );

			case 'postType':
				return $this->_process_post_type_condition( $condition_settings );

			case 'author':
				return $this->_process_author_condition( $condition_settings );

			case 'categories':
				return $this->_process_categories_condition( $condition_settings );

			case 'categoryPage':
				return $this->_process_category_page_condition( $condition_settings );

			case 'tags':
				return $this->_process_tags_condition( $condition_settings );

			case 'tagPage':
				return $this->_process_tag_page_condition( $condition_settings );

			case 'dateArchive':
				return $this->_process_date_archive_condition( $condition_settings );

			case 'productPurchase':
				return $this->_process_product_purchase_condition( $condition_settings );

			case 'cartContents':
				return $this->_process_cart_contents_condition( $condition_settings );

			case 'searchResults':
				return $this->_process_search_results_condition( $condition_settings );

			case 'operatingSystem':
				return $this->_process_operating_system_condition( $condition_settings );

			case 'browser':
				return $this->_process_browser_condition( $condition_settings );

			case 'pageVisit':
				return $this->_process_page_visit_condition( $condition_settings );

			case 'postVisit':
				return $this->_process_page_visit_condition( $condition_settings );

			case 'cookie':
				return $this->_process_cookie_condition( $condition_settings );

			case 'numberOfViews':
				return $this->_process_number_of_views_condition( $condition_id, $condition_settings );

			case 'customField':
				return $this->_process_custom_field_condition( $condition_settings );

			case 'urlParameter':
				return $this->_process_url_parameter_condition( $condition_settings );

			case 'productStock':
				return $this->_process_product_stock_condition( $condition_settings );

			default:
				if ( isset( $condition_settings['dynamicPosts'] ) ) {
					return $this->_process_dynamic_posts_condition( $condition_settings );
				}
				return true;
		}
	}

	/**
	 * Checks the $condition against $processed_conditions to determine if the $condition is considered a conflict or not.
	 *
	 * When operator 'OR/Any' is selected and we have more than one condition of the same type the priority
	 * is with the latest condition (located lower in the list).
	 *
	 * When operator 'AND/All' is selected no condition is considered a conflict.
	 *
	 * @since 4.11.0
	 *
	 * @param  array $condition            Containing all settings of the condition.
	 * @param  array $processed_conditions Containing all settings of previously processed conditions.
	 * @param  array $operator             Selected operator for the Display Conditions, Options: 'OR' or 'AND'.
	 *
	 * @return boolean Condition output.
	 */
	protected function _is_condition_conflicted( $condition, $processed_conditions, $operator ) {

		if ( 'AND' === $operator ) {
			return false;
		}

		$is_conflicted = false;

		// Check condition against all previously processed conditions.
		foreach ( $processed_conditions as $processed_condition ) {
			// Only check same condition types against each other, Ex. UserRole against UserRole.
			if ( $condition['condition'] !== $processed_condition['condition'] ) {
				continue;
			}

			// Exception! "Date Time" Condition can have multiple positive conditions.
			$is_datetime                           = 'dateTime' === $condition['condition'];
			$is_prev_cond_datetime_and_negative    = $is_datetime && 'isNotOnSpecificDate' === $processed_condition['conditionSettings']['dateTimeDisplay'];
			$is_current_cond_datetime_and_negative = $is_datetime && 'isNotOnSpecificDate' === $condition['conditionSettings']['dateTimeDisplay'];
			if ( $is_prev_cond_datetime_and_negative || $is_current_cond_datetime_and_negative ) {
				$is_conflicted = true;
				break;
			} elseif ( $is_datetime ) {
				$is_conflicted = false;
				break;
			}

			// Exception! "Custom Field" Condition can have multiple conditions.
			$is_custom_field = 'customField' === $condition['condition'];
			if ( $is_custom_field ) {
				$is_conflicted = false;
				break;
			}

			// Exception! "URL Parameter" Condition can have multiple conditions.
			$is_url_parameter = 'urlParameter' === $condition['condition'];
			if ( $is_url_parameter ) {
				$is_conflicted = false;
				break;
			}

			/**
			 * When operator is set to "OR/ANY" and we have more than one condition, all other conditions
			 * will be set as conflicted, giving the priority to the latest condition in the list.
			 */
			if ( count( $processed_conditions ) > 0 ) {
				$is_conflicted = true;
				break;
			}
		}

		return $is_conflicted;

	}

	/**
	 * Overrides current date with specified date.
	 * Useful for testing purposes where we don't want to depend on server's timestamp.
	 *
	 * @since 4.11.0
	 *
	 * @param DateTimeImmutable $date The datetime which will overrides current datetime.
	 *
	 * @return void
	 */
	public function override_current_date( $date ) {
		$this->_custom_current_date = $date;
	}

}

return new ET_Builder_Module_Field_DisplayConditions();
