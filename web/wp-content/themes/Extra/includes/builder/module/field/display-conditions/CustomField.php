<?php
/**
 * Custom Field's logic swiftly crafted.
 *
 * @since 4.11.0
 *
 * @package     Divi
 * @sub-package Builder
 */

namespace Module\Field\DisplayConditions;

/**
 * Custom Field Condition Trait
 */
trait CustomFieldCondition {

	/**
	 * Evaluates "Custom Field" condition.
	 *
	 * @since 4.14.3
	 *
	 * @param  array $condition_settings The Condition settings containing:
	 *                                   'selectConditionalMetaField' => base64_data.
	 *
	 * @return boolean Returns `true` if the condition evaluation is true, `false` otherwise.
	 */
	protected function _process_custom_field_condition( $condition_settings ) {
		// Only check for Posts.
		if ( ! is_singular() ) {
			return false;
		}

		// Only check if queried object id is valid.
		$queried_object_id = get_queried_object_id();
		if ( ! \WP_Post::get_instance( $queried_object_id ) ) {
			return false;
		}

		// Decodes the base64 data and runs validations.
		$meta_field_settings_base64 = isset( $condition_settings['selectConditionalMetaField'] ) ? $condition_settings['selectConditionalMetaField'] : '';
		$meta_field_settings_json   = base64_decode( $meta_field_settings_base64 ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode  -- The returned data is an array and necessary validation checks are performed.
		$meta_field_settings        = false !== $meta_field_settings_json ? json_decode( $meta_field_settings_json, true ) : [];

		// Gets meta fields settings.
		$display_rule         = isset( $meta_field_settings['displayRule'] ) ? $meta_field_settings['displayRule'] : 'is';
		$selected_field_name  = isset( $meta_field_settings['selectedFieldName'] ) ? $meta_field_settings['selectedFieldName'] : 'manualCustomFieldName';
		$custom_field_name    = isset( $meta_field_settings['customFieldName'] ) ? $meta_field_settings['customFieldName'] : '';
		$selected_field_value = isset( $meta_field_settings['selectedFieldValue'] ) ? $meta_field_settings['selectedFieldValue'] : 'manualCustomFieldValue';
		$custom_field_value   = isset( $meta_field_settings['customFieldValue'] ) ? $meta_field_settings['customFieldValue'] : '';

		$field_name             = 'manualCustomFieldName' === $selected_field_name ? $custom_field_name : $selected_field_name;
		$has_custom_field_value = 'manualCustomFieldValue' === $selected_field_value ? true : false;

		// Checks whether the specified custom fields actually exist.
		$has_field_name_metadata           = metadata_exists( 'post', $queried_object_id, $field_name );
		$has_selected_field_value_metadata = $has_custom_field_value ? true : metadata_exists( 'post', $queried_object_id, $selected_field_value );

		// Bailout if specified custom fields don't exist.
		if ( ! $has_field_name_metadata || ! $has_selected_field_value_metadata ) {
			return false;
		}

		$field_name_meta  = (string) get_post_meta( $queried_object_id, $field_name, true );
		$field_value_meta = $has_custom_field_value ? (string) $custom_field_value : (string) get_post_meta( $queried_object_id, $selected_field_value, true );

		// The PHP 7.4 and below will throw warning if we pass empty string on the 2nd arg
		// of `strpos`. We have to avoid this issue and make sure the `Contains` condition
		// threat $field_value_meta empty value as invalid condition (no match).
		$contains = false;
		if ( ! empty( $field_value_meta ) ) {
			$contains = strpos( $field_name_meta, $field_value_meta );
		}

		$output = [
			'is'             => $field_name_meta === $field_value_meta,
			'isNot'          => $field_name_meta !== $field_value_meta,
			'contains'       => false !== $contains,
			'doesNotContain' => false === $contains,
			'isAnyValue'     => strlen( $field_name_meta ) > 0,
			'hasNoValue'     => strlen( $field_name_meta ) === 0,
			'isGreaterThan'  => (float) $field_name_meta > (float) $field_value_meta,
			'isLessThan'     => (float) $field_name_meta < (float) $field_value_meta,
		];

		return isset( $output[ $display_rule ] ) ? $output[ $display_rule ] : false;
	}

}
