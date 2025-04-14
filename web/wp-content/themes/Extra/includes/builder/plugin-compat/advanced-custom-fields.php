<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for the Advanced Custom Fields plugin.
 *
 * @since 3.17.2
 *
 * @link https://www.advancedcustomfields.com/
 */
class ET_Builder_Plugin_Compat_Advanced_Custom_Fields extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 3.17.2
	 */
	public function __construct() {
		$this->plugin_id = $this->_get_plugin_id();
		$this->init_hooks();
	}

	/**
	 * Get the currently activated ACF plugin id as the FREE and PRO versions are separate plugins.
	 *
	 * @since 3.18
	 *
	 * @return string
	 */
	protected function _get_plugin_id() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$pro  = 'advanced-custom-fields-pro/acf.php';
		$free = 'advanced-custom-fields/acf.php';

		return is_plugin_active( $pro ) ? $pro : $free;
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 3.17.2
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'et_builder_dynamic_content_meta_value', array( $this, 'maybe_filter_dynamic_content_meta_value' ), 10, 3 );
		add_filter( 'et_builder_custom_dynamic_content_fields', array( $this, 'maybe_filter_dynamic_content_fields' ), 10, 3 );
	}

	/**
	 * Format ACF meta values accordingly.
	 *
	 * @since 3.17.2
	 *
	 * @param string  $meta_value
	 * @param string  $meta_key
	 * @param integer $post_id
	 *
	 * @return string
	 */
	public function maybe_filter_dynamic_content_meta_value( $meta_value, $meta_key, $post_id ) {
		global $wp_query;

		$post_type  = get_post_type( $post_id );
		$identifier = $post_id;

		if ( et_theme_builder_is_layout_post_type( $post_type ) ) {
			return $this->format_placeholder_value( $meta_key, $post_id );
		}

		$is_blog_query = isset( $wp_query->et_pb_blog_query ) && $wp_query->et_pb_blog_query;

		if ( ! $is_blog_query && ( is_category() || is_tag() || is_tax() ) ) {
			$term       = get_queried_object();
			$identifier = "{$term->taxonomy}_{$term->term_id}";
		} elseif ( is_author() ) {
			$user       = get_queried_object();
			$identifier = "user_{$user->ID}";
		}

		$acf_value = get_field( $meta_key, $identifier );

		if ( false === $acf_value ) {
			return $meta_value;
		}

		$acf_field = get_field_object( $meta_key, $post_id, array( 'load_value' => false ) );
		$acf_value = $this->format_field_value( $acf_value, $acf_field );

		if ( is_array( $acf_value ) || is_object( $acf_value ) ) {
			// Avoid exposing unformatted values.
			$acf_value = '';
		}

		return (string) $acf_value;
	}

	/**
	 * Format ACF dynamic content field.
	 *
	 * @since 3.17.2
	 *
	 * @param array[] $custom_fields
	 * @param int     $post_id
	 * @param mixed[] $raw_custom_fields
	 *
	 * @return array[] modified $custom_fields
	 */
	public function maybe_filter_dynamic_content_fields( $custom_fields, $post_id, $raw_custom_fields ) {
		if ( ! $post_id || et_theme_builder_is_layout_post_type( get_post_type( $post_id ) ) ) {
			$post_id = 0;
		}

		return $this->maybe_filter_dynamic_content_fields_from_groups( $custom_fields, $post_id, $raw_custom_fields );
	}

	/**
	 * Format ACF dynamic content fields for TB layouts.
	 *
	 * @since 4.0.9
	 *
	 * @param array[] $custom_fields
	 * @param int     $post_id
	 * @param mixed[] $raw_custom_fields
	 *
	 * @return array[] modified $custom_fields
	 */
	public function maybe_filter_dynamic_content_fields_from_groups( $custom_fields, $post_id, $raw_custom_fields ) {
		$groups = 0 !== $post_id ? acf_get_field_groups( array( 'post_id' => $post_id ) ) : acf_get_field_groups();

		foreach ( $groups as $group ) {
			$fields = $this->expand_fields( acf_get_fields( $group['ID'] ) );

			foreach ( $fields as $field ) {
				if ( 'group' === $field['type'] ) {
					// Remove all group fields as ACF stores empty values for them.
					unset( $custom_fields[ "custom_meta_{$field['name']}" ] );
					continue;
				}

				$settings = array(
					'label'    => esc_html( $field['label'] ),
					'type'     => 'any',
					'fields'   => array(
						'before' => array(
							'label'   => et_builder_i18n( 'Before' ),
							'type'    => 'text',
							'default' => '',
							'show_on' => 'text',
						),
						'after'  => array(
							'label'   => et_builder_i18n( 'After' ),
							'type'    => 'text',
							'default' => '',
							'show_on' => 'text',
						),
					),
					'meta_key' => $field['name'],
					'custom'   => true,
					'group'    => "ACF: {$group['title']}",
				);

				if ( current_user_can( 'unfiltered_html' ) ) {
					$settings['fields']['enable_html'] = array(
						'label'   => esc_html__( 'Enable raw HTML', 'et_builder' ),
						'type'    => 'yes_no_button',
						'options' => array(
							'on'  => et_builder_i18n( 'Yes' ),
							'off' => et_builder_i18n( 'No' ),
						),
						// Set enable_html default to `on` for taxonomy fields so builder
						// automatically renders taxonomy list properly as unescaped HTML.
						'default' => 'taxonomy' === $field['type'] ? 'on' : 'off',
						'show_on' => 'text',
					);
				}

				$custom_fields[ "custom_meta_{$field['name']}" ] = $settings;
			}
		}

		return $custom_fields;
	}

	/**
	 * Expand ACF fields into their subfields in the order they are specified, if any.
	 *
	 * @since 4.0.9
	 *
	 * @param array[] $fields
	 * @param string  $name_prefix
	 * @param string  $label_prefix
	 *
	 * @return array[]
	 */
	public function expand_fields( $fields, $name_prefix = '', $label_prefix = '' ) {
		$expanded = array();

		foreach ( $fields as $field ) {
			$expanded[] = array(
				array_merge(
					$field,
					array(
						'name'  => $name_prefix . $field['name'],
						'label' => $label_prefix . $field['label'],
					)
				),
			);

			if ( 'group' === $field['type'] ) {
				$expanded[] = $this->expand_fields(
					$field['sub_fields'],
					$name_prefix . $field['name'] . '_',
					$label_prefix . $field['label'] . ': '
				);
			}
		}

		if ( empty( $expanded ) ) {
			return array();
		}

		// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
		return call_user_func_array( 'array_merge', $expanded );
	}

	/**
	 * Format a field value based on the field type.
	 *
	 * @param mixed $value
	 * @param array $field
	 *
	 * @return mixed
	 */
	protected function format_field_value( $value, $field ) {
		if ( ! is_array( $field ) || empty( $field['type'] ) ) {
			return $value;
		}

		switch ( $field['type'] ) {
			case 'image':
				$format = isset( $field['return_format'] ) ? $field['return_format'] : 'url';
				switch ( $format ) {
					case 'array':
						$value = esc_url( wp_get_attachment_url( intval( $value['id'] ) ) );
						break;
					case 'id':
						$value = esc_url( wp_get_attachment_url( intval( $value ) ) );
						break;
				}
				break;

			case 'select':
			case 'checkbox':
				$value        = is_array( $value ) ? $value : array( $value );
				$value_labels = array();

				foreach ( $value as $value_key ) {
					$choice_label = isset( $field['choices'][ $value_key ] ) ? $field['choices'][ $value_key ] : '';
					if ( ! empty( $choice_label ) ) {
						$value_labels[] = $choice_label;
					}
				}

				$value = implode( ', ', $value_labels );
				break;

			case 'true_false':
				$value = et_builder_i18n( $value ? 'Yes' : 'No' );
				break;

			case 'taxonomy':
				// If taxonomy configuration exist, get HTML output of given value (ids).
				if ( isset( $field['taxonomy'] ) ) {
					$terms     = get_terms(
						array(
							'taxonomy' => $field['taxonomy'],
							'include'  => $value,
						)
					);
					$link      = 'on';
					$separator = ', ';

					if ( is_array( $terms ) ) {
						$value = et_builder_list_terms( $terms, $link, $separator );
					}
				}
				break;

			default:
				// Handle multiple values for which a more appropriate formatting method is not available.
				if ( isset( $field['multiple'] ) && $field['multiple'] ) {
					$value = implode( ', ', $value );
				}
				break;
		}

		// Value escaping left to the user to decide since some fields hold rich content.
		$value = et_core_esc_previously( $value );

		return $value;
	}

	/**
	 * Format a placeholder value based on the field type.
	 *
	 * @param string  $meta_key
	 * @param integer $post_id
	 *
	 * @return mixed
	 */
	protected function format_placeholder_value( $meta_key, $post_id ) {
		if ( function_exists( 'acf_get_field' ) ) {
			$field = acf_get_field( $meta_key );
		} else {
			$field = get_field_object( $meta_key, false, array( 'load_value' => false ) );
		}

		if ( ! is_array( $field ) || empty( $field['type'] ) ) {
			return esc_html__( 'Your ACF Field Value Will Display Here', 'et_builder' );
		}

		$value = esc_html(
			sprintf(
				// Translators: %1$s: ACF Field name
				__( 'Your "%1$s" ACF Field Value Will Display Here', 'et_builder' ),
				$field['label']
			)
		);

		switch ( $field['type'] ) {
			case 'image':
				$value = ET_BUILDER_PLACEHOLDER_LANDSCAPE_IMAGE_DATA;
				break;

			case 'taxonomy':
				$value = esc_html(
					implode(
						', ',
						array(
							__( 'Category 1', 'et_builder' ),
							__( 'Category 2', 'et_builder' ),
							__( 'Category 3', 'et_builder' ),
						)
					)
				);
				break;
		}

		return $value;
	}
}

new ET_Builder_Plugin_Compat_Advanced_Custom_Fields();
