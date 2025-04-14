<?php
/**
 * ET_Builder_Module_Settings_Migration_BackgroundGradientOverlaysImage.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.15.0
 */

/**
 * Migrate Background Color Gradient Overlays Image option to 'off' based on condition.
 *
 * This migration will check three existing settings, and update as following:
 *
 * OLD:
 * - use_background_color_gradient: on
 * - background_color_gradient_overlays_image: on
 * - parallax: on
 *
 * NEW:
 * - background_color_gradient_overlays_image: off
 *
 * @package Divi
 * @subpackage Builder/Migration
 * @since 4.15.0
 */

/**
 * Background Gradient Overlays Image migrations class.
 */
class ET_Builder_Module_Settings_Migration_BackgroundGradientOverlaysImage extends ET_Builder_Module_Settings_Migration {

	/**
	 * The Divi release where this migration was introduced.
	 *
	 * @var string
	 *
	 * @since 4.15.0
	 */
	public $version = '4.15';

	/**
	 * Array of modules to inspect for settings to migrate.
	 *
	 * Pass attribute and it will return selected modules only. Default return all affected modules.
	 *
	 * @param string $attr Attribute name.
	 *
	 * @return array
	 *
	 * @since 4.15.0
	 */
	public function get_modules( $attr = '' ) {
		$modules = array();

		// Background.
		if ( in_array( $attr, array( '', 'module_bg' ), true ) ) {
			// Structure Elements.
			$modules[] = 'et_pb_column_inner';
			$modules[] = 'et_pb_column_specialty';
			$modules[] = 'et_pb_column';
			$modules[] = 'et_pb_section_fullwidth';
			$modules[] = 'et_pb_section_specialty';
			$modules[] = 'et_pb_section';
			$modules[] = 'et_pb_row_inner';
			$modules[] = 'et_pb_row';
			// Divi Content Modules.
			$modules[] = 'et_pb_accordion_item';
			$modules[] = 'et_pb_accordion';
			$modules[] = 'et_pb_audio';
			$modules[] = 'et_pb_blog';
			$modules[] = 'et_pb_blurb';
			$modules[] = 'et_pb_circle_counter';
			$modules[] = 'et_pb_code';
			$modules[] = 'et_pb_comments';
			$modules[] = 'et_pb_contact_field';
			$modules[] = 'et_pb_contact_form';
			$modules[] = 'et_pb_countdown_timer';
			$modules[] = 'et_pb_counter';
			$modules[] = 'et_pb_counters';
			$modules[] = 'et_pb_cta';
			$modules[] = 'et_pb_divider';
			$modules[] = 'et_pb_filterable_portfolio';
			$modules[] = 'et_pb_gallery';
			$modules[] = 'et_pb_image';
			$modules[] = 'et_pb_login';
			$modules[] = 'et_pb_map';
			$modules[] = 'et_pb_menu';
			$modules[] = 'et_pb_number_counter';
			$modules[] = 'et_pb_portfolio';
			$modules[] = 'et_pb_post_content';
			$modules[] = 'et_pb_post_nav';
			$modules[] = 'et_pb_post_slider';
			$modules[] = 'et_pb_post_title';
			$modules[] = 'et_pb_pricing_table';
			$modules[] = 'et_pb_pricing_tables';
			$modules[] = 'et_pb_search';
			$modules[] = 'et_pb_shop';
			$modules[] = 'et_pb_sidebar';
			$modules[] = 'et_pb_signup_custom_field';
			$modules[] = 'et_pb_signup';
			$modules[] = 'et_pb_slide_fullwidth';
			$modules[] = 'et_pb_slide';
			$modules[] = 'et_pb_slider';
			$modules[] = 'et_pb_social_media_follow';
			$modules[] = 'et_pb_tab';
			$modules[] = 'et_pb_tabs';
			$modules[] = 'et_pb_team_member';
			$modules[] = 'et_pb_testimonial';
			$modules[] = 'et_pb_text';
			$modules[] = 'et_pb_toggle';
			$modules[] = 'et_pb_video_slider';
			$modules[] = 'et_pb_video';
			$modules[] = 'et_pb_fullwidth_code';
			$modules[] = 'et_pb_fullwidth_header';
			$modules[] = 'et_pb_fullwidth_image';
			$modules[] = 'et_pb_fullwidth_map';
			$modules[] = 'et_pb_fullwidth_menu';
			$modules[] = 'et_pb_fullwidth_portfolio';
			$modules[] = 'et_pb_fullwidth_post_content';
			$modules[] = 'et_pb_fullwidth_post_slider';
			$modules[] = 'et_pb_fullwidth_post_title';
			$modules[] = 'et_pb_fullwidth_slider';
			// WooCommerce Modules.
			$modules[] = 'et_pb_wc_add_to_cart';
			$modules[] = 'et_pb_wc_additional_info';
			$modules[] = 'et_pb_wc_breadcrumb';
			$modules[] = 'et_pb_wc_cart_notice';
			$modules[] = 'et_pb_wc_description';
			$modules[] = 'et_pb_wc_gallery';
			$modules[] = 'et_pb_wc_images';
			$modules[] = 'et_pb_wc_meta';
			$modules[] = 'et_pb_wc_price';
			$modules[] = 'et_pb_wc_rating';
			$modules[] = 'et_pb_wc_related_products';
			$modules[] = 'et_pb_wc_reviews';
			$modules[] = 'et_pb_wc_stock';
			$modules[] = 'et_pb_wc_tabs';
			$modules[] = 'et_pb_wc_title';
			$modules[] = 'et_pb_wc_upsells';
		}

		return $modules;
	}

	/**
	 * Get fields that are affected by this migration.
	 *
	 * @return array
	 *
	 * @since 4.15.0
	 */
	public function get_fields() {
		return array(
			'background_color_gradient_overlays_image' => array(
				'affected_fields' => array(
					'background_color_gradient_overlays_image' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_overlays_image_tablet' => array(
				'affected_fields' => array(
					'background_color_gradient_overlays_image_tablet' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_overlays_image_phone' => array(
				'affected_fields' => array(
					'background_color_gradient_overlays_image_phone' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_overlays_image__hover' => array(
				'affected_fields' => array(
					'background_color_gradient_overlays_image__hover' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_overlays_image__sticky' => array(
				'affected_fields' => array(
					'background_color_gradient_overlays_image__sticky' => $this->get_modules( 'module_bg' ),
				),
			),
		);
	}

	/**
	 * Migrate.
	 *
	 * @param string        $to_field_name This migration's target field.
	 * @param string|array  $affected_field_value Affected field reference value.
	 * @param string|number $module_slug Current module type.
	 * @param string        $to_field_value Migration target's current value.
	 * @param string        $affected_field_name Affected field attribute name.
	 * @param array         $module_attrs Current module's full attributes.
	 * @param string        $module_content Current module's content.
	 * @param string|number $module_address Current module's address.
	 *
	 * @return string
	 *
	 * @since 4.15.0
	 */
	public function migrate(
		$to_field_name,
		$affected_field_value,
		$module_slug,
		$to_field_value,
		$affected_field_name,
		$module_attrs,
		$module_content,
		$module_address
	) {
		// Early exit if affected field ("Use Gradient" or "Parallax") is not "on".
		if ( 'on' !== $affected_field_value ) {
			return $affected_field_value;
		}

		$default_settings = array(
			'use_gradient'   => 'off',
			'overlays_image' => ET_Global_Settings::get_value( 'all_background_gradient_overlays_image' ),
			'parallax'       => 'off',
		);

		$old_values = $default_settings;

		// Collect the old settings.
		switch ( $affected_field_name ) {
			// Desktop View.
			case 'background_color_gradient_overlays_image':
				if ( ! isset( $module_attrs[ $affected_field_name ] ) ) {
					return $affected_field_value;
				} else {
					$old_values['use_gradient']   = $this->_get_attr_value( 'use_background_color_gradient', $module_attrs );
					$old_values['overlays_image'] = $this->_get_attr_value( 'background_color_gradient_overlays_image', $module_attrs );
					$old_values['parallax']       = $this->_get_attr_value( 'parallax', $module_attrs );
				}
				break;

			// Tablet View.
			case 'background_color_gradient_overlays_image_tablet':
				if ( ! isset( $module_attrs[ $affected_field_name ] ) ) {
					return $affected_field_value;
				} else {
					$old_values['use_gradient']   = $this->_get_attr_value( 'use_background_color_gradient_tablet', $module_attrs );
					$old_values['overlays_image'] = $this->_get_attr_value( 'background_color_gradient_overlays_image_tablet', $module_attrs );
					$old_values['parallax']       = $this->_get_attr_value( 'parallax_tablet', $module_attrs );
				}
				break;

			// Phone View.
			case 'background_color_gradient_overlays_image_phone':
				if ( ! isset( $module_attrs[ $affected_field_name ] ) ) {
					return $affected_field_value;
				} else {
					$old_values['use_gradient']   = $this->_get_attr_value( 'use_background_color_gradient_phone', $module_attrs );
					$old_values['overlays_image'] = $this->_get_attr_value( 'background_color_gradient_overlays_image_phone', $module_attrs );
					$old_values['parallax']       = $this->_get_attr_value( 'parallax_phone', $module_attrs );
				}
				break;

			// Hover Mode.
			case 'background_color_gradient_overlays_image__hover':
				if ( ! isset( $module_attrs[ $affected_field_name ] ) ) {
					return $affected_field_value;
				} else {
					$old_values['use_gradient']   = $this->_get_attr_value( 'use_background_color_gradient__hover', $module_attrs );
					$old_values['overlays_image'] = $this->_get_attr_value( 'background_color_gradient_overlays_image__hover', $module_attrs );
					$old_values['parallax']       = $this->_get_attr_value( 'parallax__hover', $module_attrs );
				}
				break;

			// Sticky Mode.
			case 'background_color_gradient_overlays_image__sticky':
				if ( ! isset( $module_attrs[ $affected_field_name ] ) ) {
					return $affected_field_value;
				} else {
					$old_values['use_gradient']   = $this->_get_attr_value( 'use_background_color_gradient__sticky', $module_attrs );
					$old_values['overlays_image'] = $this->_get_attr_value( 'background_color_gradient_overlays_image__sticky', $module_attrs );
					$old_values['parallax']       = $this->_get_attr_value( 'parallax__sticky', $module_attrs );
				}
				break;

			default:
				return $affected_field_value;
		}

		// If overlays_image aren't defined, pull in global default settings.
		if ( empty( $old_values['use_gradient'] ) ) {
			$old_values['use_gradient'] = $default_settings['use_gradient'];
		}

		if ( empty( $old_values['overlays_image'] ) ) {
			$old_values['overlays_image'] = $default_settings['overlays_image'];
		}

		if ( empty( $old_values['parallax'] ) ) {
			$old_values['parallax'] = $default_settings['parallax'];
		}

		// New value for overlays_image.
		return 'on' === $old_values['use_gradient'] && 'on' === $old_values['overlays_image'] && 'on' === $old_values['parallax']
			? 'off' : $affected_field_value;
	}

	/**
	 * Get attributes value by field_name.
	 *
	 * @param string $field_name Field name.
	 * @param array  $module_attrs Module's full attributes.
	 *
	 * @return string
	 */
	private function _get_attr_value( $field_name, $module_attrs ) {
		if ( array_key_exists( $field_name, $module_attrs ) ) {
			return $module_attrs[ $field_name ];
		}

		return '';
	}
}

return new ET_Builder_Module_Settings_Migration_BackgroundGradientOverlaysImage();
