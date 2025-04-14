<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Plugin compatibility for WPML Multilingual CMS
 *
 * @since 3.0.64
 *
 * @link https://wpml.org
 */
class ET_Builder_Plugin_Compat_WPML_Multilingual_CMS extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor
	 */
	function __construct() {
		$this->plugin_id = 'sitepress-multilingual-cms/sitepress.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress
	 *
	 * Latest plugin version: 3.7.1
	 *
	 * @return void
	 */
	function init_hooks() {
		// Bail if there's no version found
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'maybe_enqueue_admin_scripts' ) );

		// Override the configuration
		add_action( 'wpml_config_array', array( $this, 'override_wpml_configuration' ) );
		add_filter(
			'et_pb_module_shortcode_attributes',
			array( $this, '_filter_traslate_shop_module_categories_ids' ),
			10,
			5
		);
		// Override the language code used in the AJAX request that checks if
		// cached definitions/helpers needs to be updated.
		add_filter( 'et_fb_current_page_params', array( $this, 'override_current_page_params' ) );

		// Override suppress_filters argument when accessing library layouts,
		add_filter( 'et_pb_show_all_layouts_suppress_filters', '__return_true' );

		// Handle Divi Library Layout translation process.
		add_action( 'wp_ajax_et_builder_wpml_translate_layout', array( $this, 'translate_layout' ) );
		add_filter( 'wp_insert_post_empty_content', array( $this, 'maybe_allow_save_empty_content' ), 10, 2 );
	}

	/**
	 * @param array $config
	 *
	 * @return array
	 */
	function override_wpml_configuration( $config ) {

		if ( ! empty( $config['wpml-config']['custom-fields']['custom-field'] ) ) {

			$missing_fields = array(
				array(
					'value' => '_et_pb_built_for_post_type',
					'attr'  => array(
						'action' => 'copy',
					),
				),
			);

			$seen   = array();
			$fields = $config['wpml-config']['custom-fields']['custom-field'];

			foreach ( $fields as $field ) {
				$seen[ $field['value'] ] = true;
			}

			foreach ( $missing_fields as $field ) {
				if ( empty( $seen[ $field['value'] ] ) ) {
					// The missing field is really missing, let's add it
					$fields[] = $field;
				}
			}

			$config['wpml-config']['custom-fields']['custom-field'] = $fields;

		}

		if ( ! empty( $config['wpml-config']['taxonomies']['taxonomy'] ) ) {

			$taxonomy_replacements = array(
				'scope'           => array(
					'translate' => 0,
				),
				'layout_type'     => array(
					'translate' => 0,
				),
				'module_width'    => array(
					'translate' => 0,
				),
				'layout_category' => array(
					'translate' => 1,
				),
			);

			$fixed_taxonomies = array();
			$taxonomies       = $config['wpml-config']['taxonomies']['taxonomy'];

			foreach ( $taxonomies as $taxonomy ) {
				if ( ! empty( $taxonomy_replacements[ $taxonomy['value'] ] ) ) {
					// Replace attributes
					$taxonomy['attr'] = $taxonomy_replacements[ $taxonomy['value'] ];
				}
				$fixed_taxonomies[] = $taxonomy;
			}

			$config['wpml-config']['taxonomies']['taxonomy'] = $fixed_taxonomies;

		}

		return $config;
	}

	/**
	 * Convert selected categories ids to translated ones.
	 *
	 * @internal
	 *
	 * @param array  $shortcode_atts
	 * @param array  $atts
	 * @param string $slug
	 * @param string $address
	 *
	 * @return array
	 **/
	public function _filter_traslate_shop_module_categories_ids( $shortcode_atts, $atts, $slug, $address ) {
		if (
			! is_admin() && $slug === 'et_pb_shop'
			&&
			! empty( $shortcode_atts['type'] )
			&&
			$shortcode_atts['type'] === 'product_category'
			&&
			! empty( $shortcode_atts['include_categories'] )
		) {
			$cats_array = explode( ',', $shortcode_atts['include_categories'] );
			$new_ids    = array();

			foreach ( $cats_array as $cat_id ) {
				$translated_cat_id = apply_filters( 'wpml_object_id', $cat_id, 'product_cat' );
				$new_ids[]         = ! empty( $translated_cat_id ) ? $translated_cat_id : $cat_id;
			}

			$shortcode_atts['include_categories'] = implode( ',', $new_ids );
		}

		return $shortcode_atts;
	}

	/**
	 * Override the language code used in the AJAX request that checks if
	 * cached definitions/helpers needs to be updated.
	 *
	 * @param array $params
	 *
	 * @return array
	 */
	public function override_current_page_params( $params ) {
		$langCode = apply_filters( 'wpml_current_language', false );

		if ( $langCode ) {
			$params['langCode'] = $langCode;
		}

		return $params;
	}

	/**
	 * Enqueues admin related scripts and styles for WPML compatiblity.
	 *
	 * @since 4.5.7
	 */
	public function maybe_enqueue_admin_scripts() {
		global $typenow;

		if ( 'et_pb_layout' === $typenow ) {
			wp_enqueue_script( 'et-builder-wpml-compat-scripts', ET_BUILDER_URI . '/plugin-compat/scripts/sitepress-multilingual-cms.js', array( 'jquery', 'lodash' ), ET_BUILDER_VERSION, true );

			wp_localize_script(
				'et-builder-wpml-compat-scripts',
				'et_builder_wpml_compat_options',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonces'  => array(
						'et_builder_wpml_translate_layout' => wp_create_nonce( 'et_builder_wpml_translate_layout' ),
					),
				)
			);
		}
	}

	/**
	 * Translate library layout on the background.
	 *
	 * @since 4.5.7
	 */
	public function translate_layout() {
		et_builder_security_check( 'divi_library', 'edit_posts', 'et_builder_wpml_translate_layout', 'nonce' );

		// phpcs:disable WordPress.Security.NonceVerification -- Already verified by et_builder_security_check
		$translation_trid    = (int) sanitize_text_field( et_()->array_get( $_POST, 'translation_trid' ) );
		$translation_lang_id = sanitize_text_field( et_()->array_get( $_POST, 'translation_lang_id' ) );
		// phpcs:enable

		if ( empty( $translation_trid ) || empty( $translation_lang_id ) ) {
			wp_send_json_error(
				array(
					'message' => 'Incorrect translation group or language ID.',
				)
			);
		}

		$element_type        = apply_filters( 'wpml_element_type', ET_BUILDER_LAYOUT_POST_TYPE );
		$translation_details = apply_filters( 'wpml_get_element_translations', null, $translation_trid, $element_type );
		$original_layout_id  = '';

		// Find original layout ID.
		if ( ! empty( $translation_details ) ) {
			foreach ( $translation_details as $translation_detail ) {
				$translation_original   = isset( $translation_detail->original ) ? $translation_detail->original : '';
				$translation_element_id = isset( $translation_detail->element_id ) ? $translation_detail->element_id : '';

				if ( '1' === $translation_original ) {
					$original_layout_id = $translation_element_id;
					break;
				}
			}
		}

		if ( empty( $original_layout_id ) ) {
			wp_send_json_error(
				array(
					'message' => 'No translation found.',
				)
			);
		}

		// Meta.
		$meta_values = array();
		$meta_keys   = array(
			'_et_pb_row_layout',
			'_et_pb_module_type',
			'_et_pb_excluded_global_options',
			'_et_pb_built_for_post_type',
		);

		foreach ( $meta_keys as $meta_key ) {
			$meta_value = get_post_meta( $original_layout_id, $meta_key, true );

			if ( ! empty( $meta_value ) ) {
				$meta_values[ $meta_key ] = $meta_value;
			}
		}

		// Taxonomy.
		$tax_values = array();
		$tax_args   = array( 'fields' => 'names' );
		$tax_keys   = array(
			'scope',
			'layout_type',
			'module_width',
		);

		foreach ( $tax_keys as $tax_key ) {
			$terms = get_the_terms( $original_layout_id, $tax_key );

			if ( is_wp_error( $terms ) ) {
				continue;
			}

			// Only expect the last term to be saved here.
			foreach ( $terms as $term ) {
				$tax_values[ $tax_key ] = $term->slug;
			}
		}

		// 1. Create new layout based on meta and taxonomy of original layout.
		$translation_layout_id = et_pb_create_layout( '', '', $meta_values, $tax_values );

		if ( is_wp_error( $translation_layout_id ) ) {
			wp_send_json_error(
				array(
					'message' => 'Failed to create translation layout.',
				)
			);
		}

		// Translation Details.
		$original_language_args = array(
			'element_id'   => $original_layout_id,
			'element_type' => ET_BUILDER_LAYOUT_POST_TYPE,
		);
		$original_language_info = apply_filters( 'wpml_element_language_details', null, $original_language_args );

		$translation_language_args = array(
			'element_id'           => $translation_layout_id,
			'element_type'         => $element_type,
			'trid'                 => ! empty( $original_language_info->trid ) ? $original_language_info->trid : null,
			'language_code'        => $translation_lang_id,
			'source_language_code' => ! empty( $original_language_info->language_code ) ? $original_language_info->language_code : null,
		);

		// 2. Set the new layout as translation of the original layout.
		do_action( 'wpml_set_element_language_details', $translation_language_args );

		wp_send_json_success(
			array(
				'original_layout_id'    => $original_layout_id,
				'translation_layout_id' => $translation_layout_id,
				'edit_layout_link'      => esc_url_raw( get_edit_post_link( $translation_layout_id ) ),
			)
		);
	}

	/**
	 * Allow library layout with empty title and content to be inserted as new post for
	 * translation purpose.
	 *
	 * @since 4.5.7
	 *
	 * @param bool  $maybe_empty Original status.
	 * @param array $postarr     Array of post data.
	 */
	public function maybe_allow_save_empty_content( $maybe_empty, $postarr ) {
		$post_status = et_()->array_get( $postarr, 'post_status' );
		$post_type   = et_()->array_get( $postarr, 'post_type' );

		if ( $maybe_empty && ET_BUILDER_LAYOUT_POST_TYPE === $post_type && 'publish' === $post_status ) {
			return false;
		}

		return $maybe_empty;
	}
}

new ET_Builder_Plugin_Compat_WPML_Multilingual_CMS();
