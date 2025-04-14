<?php
/**
 * Migrate Background Gradient Start/End to new Multi-stops format.
 *
 * This migration will take four existing settings and combine them into one
 * new, unified setting:
 *
 * OLD:
 * - background_color_gradient_start: #rrggbb
 * - background_color_gradient_start_position: xx%
 * - background_color_gradient_end: rgba(rr,gg,bb,aa)
 * - background_color_gradient_end_position: xx%
 *
 * NEW:
 * - background_gradient_stops: #rrggbb xx%|rgba(rr,gg,bb,aa) xx%
 *
 * This new format is not limited to only having defined points for gradient
 * start and end. In this way, we can enable unlimited gradient stops in our
 * gradient background settings.
 *
 * @package    Divi
 * @subpackage Builder/Migration
 * @since 4.16.0
 */

/**
 * Background Gradient Stops migrations class.
 */
class ET_Builder_Module_Settings_Migration_BackgroundGradientStops extends ET_Builder_Module_Settings_Migration {


	/**
	 * The Divi release where this migration was introduced.
	 *
	 * @var string
	 *
	 * @since 4.16.0
	 */
	public $version = '4.16';

	/**
	 * This migration should add the defined field(s).
	 *
	 * This migration adds a new, combined field for background gradient stops. Older modules do not
	 * have this field, so it needs to be added.
	 *
	 * @var bool
	 *
	 * @since 4.16.1
	 */
	public $add_missing_fields = true;

	/**
	 * Array of modules to inspect for settings to migrate.
	 *
	 * Pass attribute and it will return selected modules only. Default return all affected modules.
	 *
	 * @param string $attr Attribute name.
	 *
	 * @return array Collection of module types.
	 *
	 * @since 4.16.0
	 */
	public function get_modules( $attr = '' ) {
		$all_module_slugs = ET_Builder_Element::get_all_module_slugs();
		$modules          = array();

		$module_bg           = array(
			// Structure Elements.
			'et_pb_column_inner',
			'et_pb_column_specialty',
			'et_pb_column',
			'et_pb_section_fullwidth',
			'et_pb_section_specialty',
			'et_pb_section',
			'et_pb_row_inner',
			'et_pb_row',
			// Divi Content Modules.
			'et_pb_accordion_item',
			'et_pb_accordion',
			'et_pb_audio',
			'et_pb_blog',
			'et_pb_blurb',
			'et_pb_circle_counter',
			'et_pb_code',
			'et_pb_comments',
			'et_pb_contact_field',
			'et_pb_contact_form',
			'et_pb_countdown_timer',
			'et_pb_counter',
			'et_pb_counters',
			'et_pb_cta',
			'et_pb_divider',
			'et_pb_filterable_portfolio',
			'et_pb_gallery',
			'et_pb_icon',
			'et_pb_image',
			'et_pb_login',
			'et_pb_map',
			'et_pb_menu',
			'et_pb_number_counter',
			'et_pb_portfolio',
			'et_pb_post_content',
			'et_pb_post_nav',
			'et_pb_post_slider',
			'et_pb_post_title',
			'et_pb_pricing_table',
			'et_pb_pricing_tables',
			'et_pb_search',
			'et_pb_shop',
			'et_pb_sidebar',
			'et_pb_signup_custom_field',
			'et_pb_signup',
			'et_pb_slide_fullwidth',
			'et_pb_slide',
			'et_pb_slider',
			'et_pb_social_media_follow',
			'et_pb_tab',
			'et_pb_tabs',
			'et_pb_team_member',
			'et_pb_testimonial',
			'et_pb_text',
			'et_pb_toggle',
			'et_pb_video_slider',
			'et_pb_video',
			'et_pb_fullwidth_code',
			'et_pb_fullwidth_header',
			'et_pb_fullwidth_image',
			'et_pb_fullwidth_map',
			'et_pb_fullwidth_menu',
			'et_pb_fullwidth_portfolio',
			'et_pb_fullwidth_post_content',
			'et_pb_fullwidth_post_slider',
			'et_pb_fullwidth_post_title',
			'et_pb_fullwidth_slider',
			// WooCommerce Modules.
			'et_pb_wc_add_to_cart',
			'et_pb_wc_additional_info',
			'et_pb_wc_breadcrumb',
			'et_pb_wc_cart_notice',
			'et_pb_wc_description',
			'et_pb_wc_gallery',
			'et_pb_wc_images',
			'et_pb_wc_meta',
			'et_pb_wc_price',
			'et_pb_wc_rating',
			'et_pb_wc_related_products',
			'et_pb_wc_reviews',
			'et_pb_wc_stock',
			'et_pb_wc_tabs',
			'et_pb_wc_title',
			'et_pb_wc_upsells',
		);
		$button_bg           = array(
			// Divi Content Modules.
			'et_pb_button',
			'et_pb_comments',
			'et_pb_contact_form',
			'et_pb_cta',
			'et_pb_login',
			'et_pb_post_slider',
			'et_pb_pricing_table',
			'et_pb_pricing_tables',
			'et_pb_signup',
			'et_pb_slide_fullwidth',
			'et_pb_slide',
			'et_pb_slider',
			'et_pb_social_media_follow',
			'et_pb_fullwidth_slider',
			// WooCommerce Modules.
			'et_pb_wc_add_to_cart',
			'et_pb_wc_cart_notice',
			'et_pb_wc_reviews',
		);
		$fw_header_button_bg = array(
			// Divi Content Modules.
			'et_pb_fullwidth_header',
		);

		// Any modules not caught by the preceding lists (generally this will be 3rd-party modules).
		$module_catchall = array_diff( $all_module_slugs, $module_bg, $button_bg, $fw_header_button_bg );

		// Background.
		if ( in_array( $attr, array( '', 'module_bg' ), true ) ) {
			$modules = array_merge( $modules, $module_bg, $module_catchall );
		}

		// Button BG.
		if ( in_array( $attr, array( '', 'button_bg' ), true ) ) {
			$modules = array_merge( $modules, $button_bg, $module_catchall );
		}

		// Fullwidth Header Button One/Two BG.
		if ( in_array( $attr, array( '', 'fw_header_button_bg' ), true ) ) {
			$modules = array_merge( $modules, $fw_header_button_bg, $module_catchall );
		}

		// In the event that no match was made, pass *only* the filtered modules.
		if ( empty( $modules ) ) {
			$modules = $module_catchall;
		}

		return $modules;
	}

	/**
	 * Get fields that are affected by this migration.
	 *
	 * We need to write gradient stops into the new attribute even if they're
	 * built using default values for gradient start/end settings, so we want to
	 * find any case where a background gradient is used (regardless of whether
	 * or not a custom color or position has been defined). As long as some form
	 * of `use[_background]_color_gradient` is saved to the module, we want to
	 * load it and migrate its data for the new `_gradient_stops` field.
	 *
	 * @return array Collection of affected attributes.
	 *
	 * @since 4.16.0
	 */
	public function get_fields() {
		// Gradient Stops fields.
		$gradient_stops_fields = array(
			// Core fields.
			'background_color_gradient_stops'            => array(
				'affected_fields' => array(
					'background_color_gradient_stops' => $this->get_modules( 'module_bg' ),
				),
			),
			'button_bg_color_gradient_stops'             => array(
				'affected_fields' => array(
					'button_bg_color_gradient_stops' => $this->get_modules( 'button_bg' ),
				),
			),
			'button_one_bg_color_gradient_stops'         => array(
				'affected_fields' => array(
					'button_one_bg_color_gradient_stops' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'button_two_bg_color_gradient_stops'         => array(
				'affected_fields' => array(
					'button_two_bg_color_gradient_stops' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'background_color_gradient_stops_1'          => array(
				'affected_fields' => array(
					'background_color_gradient_stops_1' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_stops_2'          => array(
				'affected_fields' => array(
					'background_color_gradient_stops_2' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_stops_3'          => array(
				'affected_fields' => array(
					'background_color_gradient_stops_3' => $this->get_modules( 'module_bg' ),
				),
			),

			// Tablet View.
			'background_color_gradient_stops_tablet'     => array(
				'affected_fields' => array(
					'background_color_gradient_stops_tablet' => $this->get_modules( 'module_bg' ),
				),
			),
			'button_bg_color_gradient_stops_tablet'      => array(
				'affected_fields' => array(
					'button_bg_color_gradient_stops_tablet' => $this->get_modules( 'button_bg' ),
				),
			),
			'button_one_bg_color_gradient_stops_tablet'  => array(
				'affected_fields' => array(
					'button_one_bg_color_gradient_stops_tablet' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'button_two_bg_color_gradient_stops_tablet'  => array(
				'affected_fields' => array(
					'button_two_bg_color_gradient_stops_tablet' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'background_color_gradient_stops_tablet_1'   => array(
				'affected_fields' => array(
					'background_color_gradient_stops_tablet_1' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_stops_tablet_2'   => array(
				'affected_fields' => array(
					'background_color_gradient_stops_tablet_2' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_stops_tablet_3'   => array(
				'affected_fields' => array(
					'background_color_gradient_stops_tablet_3' => $this->get_modules( 'module_bg' ),
				),
			),

			// Phone View.
			'background_color_gradient_stops_phone'      => array(
				'affected_fields' => array(
					'background_color_gradient_stops_phone' => $this->get_modules( 'module_bg' ),
				),
			),
			'button_bg_color_gradient_stops_phone'       => array(
				'affected_fields' => array(
					'button_bg_color_gradient_stops_phone' => $this->get_modules( 'button_bg' ),
				),
			),
			'button_one_bg_color_gradient_stops_phone'   => array(
				'affected_fields' => array(
					'button_one_bg_color_gradient_stops_phone' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'button_two_bg_color_gradient_stops_phone'   => array(
				'affected_fields' => array(
					'button_two_bg_color_gradient_stops_phone' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'background_color_gradient_stops_phone_1'    => array(
				'affected_fields' => array(
					'background_color_gradient_stops_phone_1' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_stops_phone_2'    => array(
				'affected_fields' => array(
					'background_color_gradient_stops_phone_2' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_stops_phone_3'    => array(
				'affected_fields' => array(
					'background_color_gradient_stops_phone_3' => $this->get_modules( 'module_bg' ),
				),
			),

			// Hover Mode.
			'background_color_gradient_stops__hover'     => array(
				'affected_fields' => array(
					'background_color_gradient_stops__hover' => $this->get_modules( 'module_bg' ),
				),
			),
			'button_bg_color_gradient_stops__hover'      => array(
				'affected_fields' => array(
					'button_bg_color_gradient_stops__hover' => $this->get_modules( 'button_bg' ),
				),
			),
			'button_one_bg_color_gradient_stops__hover'  => array(
				'affected_fields' => array(
					'button_one_bg_color_gradient_stops__hover' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'button_two_bg_color_gradient_stops__hover'  => array(
				'affected_fields' => array(
					'button_two_bg_color_gradient_stops__hover' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'background_color_gradient_stops__hover_1'   => array(
				'affected_fields' => array(
					'background_color_gradient_stops__hover_1' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_stops__hover_2'   => array(
				'affected_fields' => array(
					'background_color_gradient_stops__hover_2' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_stops__hover_3'   => array(
				'affected_fields' => array(
					'background_color_gradient_stops__hover_3' => $this->get_modules( 'module_bg' ),
				),
			),

			// Sticky Mode.
			'background_color_gradient_stops__sticky'    => array(
				'affected_fields' => array(
					'background_color_gradient_stops__sticky' => $this->get_modules( 'module_bg' ),
				),
			),
			'button_bg_color_gradient_stops__sticky'     => array(
				'affected_fields' => array(
					'button_bg_color_gradient_stops__sticky' => $this->get_modules( 'button_bg' ),
				),
			),
			'button_one_bg_color_gradient_stops__sticky' => array(
				'affected_fields' => array(
					'button_one_bg_color_gradient_stops__sticky' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'button_two_bg_color_gradient_stops__sticky' => array(
				'affected_fields' => array(
					'button_two_bg_color_gradient_stops__sticky' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'background_color_gradient_stops__sticky_1'  => array(
				'affected_fields' => array(
					'background_color_gradient_stops__sticky_1' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_stops__sticky_2'  => array(
				'affected_fields' => array(
					'background_color_gradient_stops__sticky_2' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_stops__sticky_3'  => array(
				'affected_fields' => array(
					'background_color_gradient_stops__sticky_3' => $this->get_modules( 'module_bg' ),
				),
			),
		);

		// Gradient Type fields.
		$gradient_type_fields = array(
			// Core fields.
			'background_color_gradient_type'            => array(
				'affected_fields' => array(
					'background_color_gradient_type' => $this->get_modules( 'module_bg' ),
				),
			),
			'button_bg_color_gradient_type'             => array(
				'affected_fields' => array(
					'button_bg_color_gradient_type' => $this->get_modules( 'button_bg' ),
				),
			),
			'button_one_bg_color_gradient_type'         => array(
				'affected_fields' => array(
					'button_one_bg_color_gradient_type' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'button_two_bg_color_gradient_type'         => array(
				'affected_fields' => array(
					'button_two_bg_color_gradient_type' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'background_color_gradient_type_1'          => array(
				'affected_fields' => array(
					'background_color_gradient_type_1' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_type_2'          => array(
				'affected_fields' => array(
					'background_color_gradient_type_2' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_type_3'          => array(
				'affected_fields' => array(
					'background_color_gradient_type_3' => $this->get_modules( 'module_bg' ),
				),
			),

			// Tablet View.
			'background_color_gradient_type_tablet'     => array(
				'affected_fields' => array(
					'background_color_gradient_type_tablet' => $this->get_modules( 'module_bg' ),
				),
			),
			'button_bg_color_gradient_type_tablet'      => array(
				'affected_fields' => array(
					'button_bg_color_gradient_type_tablet' => $this->get_modules( 'button_bg' ),
				),
			),
			'button_one_bg_color_gradient_type_tablet'  => array(
				'affected_fields' => array(
					'button_one_bg_color_gradient_type_tablet' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'button_two_bg_color_gradient_type_tablet'  => array(
				'affected_fields' => array(
					'button_two_bg_color_gradient_type_tablet' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'background_color_gradient_type_tablet_1'   => array(
				'affected_fields' => array(
					'background_color_gradient_type_tablet_1' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_type_tablet_2'   => array(
				'affected_fields' => array(
					'background_color_gradient_type_tablet_2' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_type_tablet_3'   => array(
				'affected_fields' => array(
					'background_color_gradient_type_tablet_3' => $this->get_modules( 'module_bg' ),
				),
			),

			// Phone View.
			'background_color_gradient_type_phone'      => array(
				'affected_fields' => array(
					'background_color_gradient_type_phone' => $this->get_modules( 'module_bg' ),
				),
			),
			'button_bg_color_gradient_type_phone'       => array(
				'affected_fields' => array(
					'button_bg_color_gradient_type_phone' => $this->get_modules( 'button_bg' ),
				),
			),
			'button_one_bg_color_gradient_type_phone'   => array(
				'affected_fields' => array(
					'button_one_bg_color_gradient_type_phone' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'button_two_bg_color_gradient_type_phone'   => array(
				'affected_fields' => array(
					'button_two_bg_color_gradient_type_phone' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'background_color_gradient_type_phone_1'    => array(
				'affected_fields' => array(
					'background_color_gradient_type_phone_1' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_type_phone_2'    => array(
				'affected_fields' => array(
					'background_color_gradient_type_phone_2' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_type_phone_3'    => array(
				'affected_fields' => array(
					'background_color_gradient_type_phone_3' => $this->get_modules( 'module_bg' ),
				),
			),

			// Hover Mode.
			'background_color_gradient_type__hover'     => array(
				'affected_fields' => array(
					'background_color_gradient_type__hover' => $this->get_modules( 'module_bg' ),
				),
			),
			'button_bg_color_gradient_type__hover'      => array(
				'affected_fields' => array(
					'button_bg_color_gradient_type__hover' => $this->get_modules( 'button_bg' ),
				),
			),
			'button_one_bg_color_gradient_type__hover'  => array(
				'affected_fields' => array(
					'button_one_bg_color_gradient_type__hover' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'button_two_bg_color_gradient_type__hover'  => array(
				'affected_fields' => array(
					'button_two_bg_color_gradient_type__hover' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'background_color_gradient_type__hover_1'   => array(
				'affected_fields' => array(
					'background_color_gradient_type__hover_1' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_type__hover_2'   => array(
				'affected_fields' => array(
					'background_color_gradient_type__hover_2' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_type__hover_3'   => array(
				'affected_fields' => array(
					'background_color_gradient_type__hover_3' => $this->get_modules( 'module_bg' ),
				),
			),

			// Sticky Mode.
			'background_color_gradient_type__sticky'    => array(
				'affected_fields' => array(
					'background_color_gradient_type__sticky' => $this->get_modules( 'module_bg' ),
				),
			),
			'button_bg_color_gradient_type__sticky'     => array(
				'affected_fields' => array(
					'button_bg_color_gradient_type__sticky' => $this->get_modules( 'button_bg' ),
				),
			),
			'button_one_bg_color_gradient_type__sticky' => array(
				'affected_fields' => array(
					'button_one_bg_color_gradient_type__sticky' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'button_two_bg_color_gradient_type__sticky' => array(
				'affected_fields' => array(
					'button_two_bg_color_gradient_type__sticky' => $this->get_modules( 'fw_header_button_bg' ),
				),
			),
			'background_color_gradient_type__sticky_1'  => array(
				'affected_fields' => array(
					'background_color_gradient_type__sticky_1' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_type__sticky_2'  => array(
				'affected_fields' => array(
					'background_color_gradient_type__sticky_2' => $this->get_modules( 'module_bg' ),
				),
			),
			'background_color_gradient_type__sticky_3'  => array(
				'affected_fields' => array(
					'background_color_gradient_type__sticky_3' => $this->get_modules( 'module_bg' ),
				),
			),
		);

		return array_merge( $gradient_stops_fields, $gradient_type_fields );
	}

	/**
	 * The various names of gradient *_type fields.
	 *
	 * @return array Collection of affected attributes.
	 *
	 * @since 4.16.0
	 */
	public function gradientTypeFields() {
		return array(
			// Core fields.
			'background_color_gradient_type',
			'button_bg_color_gradient_type',
			'button_one_bg_color_gradient_type',
			'button_two_bg_color_gradient_type',
			'background_color_gradient_type_1',
			'background_color_gradient_type_2',
			'background_color_gradient_type_3',

			// Tablet View.
			'background_color_gradient_type_tablet',
			'button_bg_color_gradient_type_tablet',
			'button_one_bg_color_gradient_type_tablet',
			'button_two_bg_color_gradient_type_tablet',
			'background_color_gradient_type_tablet_1',
			'background_color_gradient_type_tablet_2',
			'background_color_gradient_type_tablet_3',

			// Phone View.
			'background_color_gradient_type_phone',
			'button_bg_color_gradient_type_phone',
			'button_one_bg_color_gradient_type_phone',
			'button_two_bg_color_gradient_type_phone',
			'background_color_gradient_type_phone_1',
			'background_color_gradient_type_phone_2',
			'background_color_gradient_type_phone_3',

			// Hover Mode.
			'background_color_gradient_type__hover',
			'button_bg_color_gradient_type__hover',
			'button_one_bg_color_gradient_type__hover',
			'button_two_bg_color_gradient_type__hover',
			'background_color_gradient_type__hover_1',
			'background_color_gradient_type__hover_2',
			'background_color_gradient_type__hover_3',

			// Sticky Mode.
			'background_color_gradient_type__sticky',
			'button_bg_color_gradient_type__sticky',
			'button_one_bg_color_gradient_type__sticky',
			'button_two_bg_color_gradient_type__sticky',
			'background_color_gradient_type__sticky_1',
			'background_color_gradient_type__sticky_2',
			'background_color_gradient_type__sticky_3',
		);
	}

	/**
	 * Check if array key exists and has non-empty value.
	 *
	 * @param string $key   Name of an attribute being searched.
	 * @param array  $array Module attributes.
	 *
	 * @return bool Whether a matching attribute exists with a non-empty value.
	 *
	 * @since 4.16.0
	 */
	public function existsAndIsNotEmpty( $key, $array ) {
		if ( ! array_key_exists( $key, $array ) ) {
			return false;
		}

		return ! empty( $array[ $key ] );
	}

	/**
	 * Gradient Type: Replace any "radial" gradient types with "elliptical".
	 *
	 * "Gradient Type" has been updated.
	 *
	 * Previously, the options were:
	 * - linear [`linear-gradient(...)`]
	 * - radial [`radial-gradient(...)`] (defaults to 'ellipse at' unless circle is defined)
	 *
	 * The options now are:
	 * - linear [`linear-gradient(...)`]
	 * - elliptical [`radial-gradient( ellipse at...)`]
	 * - circular [`radial-gradient( circle at...)`]
	 * - conic [`conic-gradient(...)`]
	 *
	 * This means that the old "radial" type needs to be updated to "ellipse" wherever it appears.
	 *
	 * @param string $current_value The current value (which may need replacing).
	 *
	 * @return string Value to assign to the migrated field.
	 *
	 * @since 4.16.0
	 */
	public function migrateGradientType( $current_value ) {
		switch ( $current_value ) {
			case 'radial':
				return 'circular';
			default:
				return $current_value;
		}
	}

	/**
	 * Gradient Stops: Formatting information for the old start/end values to be migrated.
	 *
	 * `background_color_gradient_start_position`:
	 * `background_color_gradient_end_position`:
	 *   string - percentage value (0%-100%); could also be a fraction of
	 *            a percent (ex. '25.68%'). We will be rounding these to the
	 *            nearest whole percent during migration, so:
	 *              pre-migration '25.48%' = post-migration '25%'
	 *              pre-migration '25.68%' = post-migration '26%'
	 *
	 * `background_color_gradient_start`:
	 * `background_color_gradient_end`:
	 *   string - CSS color code. Should be one of:
	 *              hex (ex. #ff00aa, #f0a)
	 *              rgba (ex. rgba(255,0,170,0.54)
	 *            Our color picker converts rgb(...) to hex and hsla(...)
	 *            to rgba, so they don't appear naturally. Customized layout
	 *            exports could have modified values, though, so we should
	 *            be prepared to handle them.
	 *
	 * @param string $field_name Name of the field.
	 * @param string $current_value Current value.
	 * @param array  $attrs The attrs.
	 *
	 * @return string Value to assign to the migrated field.
	 *
	 * @since 4.16.0 Added.
	 * @since 4.17.1 Check inheritance. If all values are inherited, unset the attribute.
	 */
	public function migrateGradientStops( $field_name, $current_value, $attrs ) {

		// Grab system defaults to insert where needed (due to empty values).
		$default_settings = array(
			'start_color'    => ET_Global_Settings::get_value( 'all_background_gradient_start' ),
			'start_position' => ET_Global_Settings::get_value( 'all_background_gradient_start_position' ),
			'end_color'      => ET_Global_Settings::get_value( 'all_background_gradient_end' ),
			'end_position'   => ET_Global_Settings::get_value( 'all_background_gradient_end_position' ),
		);

		// This array will be populated with values from the old fields.
		$old_values = array(
			'start_color'    => '',
			'start_position' => '',
			'end_color'      => '',
			'end_position'   => '',
		);

		// This is used to avoid polluting with unnecessary settings.
		$is_inherited = array(
			'start_color'    => false,
			'start_position' => false,
			'end_color'      => false,
			'end_position'   => false,
		);

		// Collect the old gradient settings.
		switch ( $field_name ) {
			// Core fields.
			case 'background_color_gradient_stops':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start'];
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position'];
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end'];
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position'];
				}
				break;
			case 'button_bg_color_gradient_stops':
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_bg_color_gradient_start'];
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_bg_color_gradient_start_position'];
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_bg_color_gradient_end'];
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_bg_color_gradient_end_position'];
				}
				break;
			case 'button_one_bg_color_gradient_stops':
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_one_bg_color_gradient_start'];
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_one_bg_color_gradient_start_position'];
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_one_bg_color_gradient_end'];
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_one_bg_color_gradient_end_position'];
				}
				break;
			case 'button_two_bg_color_gradient_stops':
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_two_bg_color_gradient_start'];
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_two_bg_color_gradient_start_position'];
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_two_bg_color_gradient_end'];
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_two_bg_color_gradient_end_position'];
				}
				break;
			case 'background_color_gradient_stops_1':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_1', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start_1'];
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_1', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position_1'];
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_1', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end_1'];
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_1', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position_1'];
				}
				break;
			case 'background_color_gradient_stops_2':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_2', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start_2'];
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_2', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position_2'];
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_2', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end_2'];
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_2', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position_2'];
				}
				break;
			case 'background_color_gradient_stops_3':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_3', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start_3'];
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_3', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position_3'];
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_3', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end_3'];
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_3', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position_3'];
				}
				break;

			// Tablet View.
			// If tablet view settings are not found, check for desktop view settings and inherit (if found).
			case 'background_color_gradient_stops_tablet':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_tablet', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_tablet', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_tablet', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_tablet', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'button_bg_color_gradient_stops_tablet':
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start_tablet', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_bg_color_gradient_start_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_bg_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start_position_tablet', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_bg_color_gradient_start_position_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_bg_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end_tablet', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_bg_color_gradient_end_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_bg_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end_position_tablet', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_bg_color_gradient_end_position_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_bg_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'button_one_bg_color_gradient_stops_tablet':
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start_tablet', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_one_bg_color_gradient_start_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_one_bg_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start_position_tablet', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_one_bg_color_gradient_start_position_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_one_bg_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end_tablet', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_one_bg_color_gradient_end_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_one_bg_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end_position_tablet', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_one_bg_color_gradient_end_position_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_one_bg_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'button_two_bg_color_gradient_stops_tablet':
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start_tablet', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_two_bg_color_gradient_start_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_two_bg_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start_position_tablet', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_two_bg_color_gradient_start_position_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_two_bg_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end_tablet', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_two_bg_color_gradient_end_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_two_bg_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end_position_tablet', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_two_bg_color_gradient_end_position_tablet'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_two_bg_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'background_color_gradient_stops_tablet_1':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_tablet_1', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start_tablet_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_1', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_1'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_tablet_1', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position_tablet_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_1', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_1'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_tablet_1', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end_tablet_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_1', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_1'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_tablet_1', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position_tablet_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_1', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_1'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'background_color_gradient_stops_tablet_2':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_tablet_2', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start_tablet_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_2', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_2'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_tablet_2', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position_tablet_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_2', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_2'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_tablet_2', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end_tablet_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_2', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_2'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_tablet_2', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position_tablet_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_2', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_2'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'background_color_gradient_stops_tablet_3':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_tablet_3', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start_tablet_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_3', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_3'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_tablet_3', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position_tablet_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_3', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_3'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_tablet_3', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end_tablet_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_3', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_3'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_tablet_3', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position_tablet_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_3', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_3'];
					$is_inherited['end_position'] = true;
				}
				break;

			// Phone View.
			// If phone view settings are not found, check for table view settings and inherit (if found).
			// If tablet view settings are not found, check for desktop view settings and inherit (if found).
			case 'background_color_gradient_stops_phone':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_phone', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_tablet', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_tablet'];
					$is_inherited['start_color'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_phone', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_tablet', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_tablet'];
					$is_inherited['start_position'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_phone', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_tablet', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_tablet'];
					$is_inherited['end_color'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_phone', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_tablet', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_tablet'];
					$is_inherited['end_position'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'button_bg_color_gradient_stops_phone':
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start_phone', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_bg_color_gradient_start_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start_tablet', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_bg_color_gradient_start_tablet'];
					$is_inherited['start_color'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_bg_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start_position_phone', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_bg_color_gradient_start_position_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start_position_tablet', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_bg_color_gradient_start_position_tablet'];
					$is_inherited['start_position'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_bg_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end_phone', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_bg_color_gradient_end_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end_tablet', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_bg_color_gradient_end_tablet'];
					$is_inherited['end_color'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_bg_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end_position_phone', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_bg_color_gradient_end_position_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end_position_tablet', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_bg_color_gradient_end_position_tablet'];
					$is_inherited['end_position'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_bg_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'button_one_bg_color_gradient_stops_phone':
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start_phone', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_one_bg_color_gradient_start_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start_tablet', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_one_bg_color_gradient_start_tablet'];
					$is_inherited['start_color'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_one_bg_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start_position_phone', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_one_bg_color_gradient_start_position_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start_position_tablet', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_one_bg_color_gradient_start_position_tablet'];
					$is_inherited['start_position'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_one_bg_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end_phone', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_one_bg_color_gradient_end_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end_tablet', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_one_bg_color_gradient_end_tablet'];
					$is_inherited['end_color'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_one_bg_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end_position_phone', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_one_bg_color_gradient_end_position_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end_position_tablet', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_one_bg_color_gradient_end_position_tablet'];
					$is_inherited['end_position'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_one_bg_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'button_two_bg_color_gradient_stops_phone':
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start_phone', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_two_bg_color_gradient_start_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start_tablet', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_two_bg_color_gradient_start_tablet'];
					$is_inherited['start_color'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_two_bg_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start_position_phone', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_two_bg_color_gradient_start_position_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start_position_tablet', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_two_bg_color_gradient_start_position_tablet'];
					$is_inherited['start_position'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_two_bg_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end_phone', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_two_bg_color_gradient_end_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end_tablet', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_two_bg_color_gradient_end_tablet'];
					$is_inherited['end_color'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_two_bg_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end_position_phone', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_two_bg_color_gradient_end_position_phone'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end_position_tablet', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_two_bg_color_gradient_end_position_tablet'];
					$is_inherited['end_position'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_two_bg_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'background_color_gradient_stops_phone_1':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_phone_1', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start_phone_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_tablet_1', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_tablet_1'];
					$is_inherited['start_color'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_1', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_1'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_phone_1', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position_phone_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_tablet_1', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_tablet_1'];
					$is_inherited['start_position'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_1', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_1'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_phone_1', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end_phone_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_tablet_1', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_tablet_1'];
					$is_inherited['end_color'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_1', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_1'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_phone_1', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position_phone_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_tablet_1', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_tablet_1'];
					$is_inherited['end_position'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_1', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_1'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'background_color_gradient_stops_phone_2':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_phone_2', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start_phone_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_tablet_2', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_tablet_2'];
					$is_inherited['start_color'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_2', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_2'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_phone_2', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position_phone_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_tablet_2', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_tablet_2'];
					$is_inherited['start_position'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_2', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_2'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_phone_2', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end_phone_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_tablet_2', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_tablet_2'];
					$is_inherited['end_color'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_2', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_2'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_phone_2', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position_phone_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_tablet_2', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_tablet_2'];
					$is_inherited['end_position'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_2', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_2'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'background_color_gradient_stops_phone_3':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_phone_3', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start_phone_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_tablet_3', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_tablet_3'];
					$is_inherited['start_color'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_3', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_3'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_phone_3', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position_phone_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_tablet_3', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_tablet_3'];
					$is_inherited['start_position'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_3', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_3'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_phone_3', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end_phone_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_tablet_3', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_tablet_3'];
					$is_inherited['end_color'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_3', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_3'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_phone_3', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position_phone_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_tablet_3', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_tablet_3'];
					$is_inherited['end_position'] = true;
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_3', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_3'];
					$is_inherited['end_position'] = true;
				}
				break;

			// Hover Mode.
			// If hover view settings are not found, check for desktop view settings and inherit (if found).
			case 'background_color_gradient_stops__hover':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start__hover', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position__hover', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end__hover', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position__hover', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'button_bg_color_gradient_stops__hover':
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start__hover', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_bg_color_gradient_start__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_bg_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start_position__hover', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_bg_color_gradient_start_position__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_bg_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end__hover', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_bg_color_gradient_end__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_bg_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end_position__hover', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_bg_color_gradient_end_position__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_bg_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'button_one_bg_color_gradient_stops__hover':
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start__hover', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_one_bg_color_gradient_start__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_one_bg_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start_position__hover', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_one_bg_color_gradient_start_position__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_one_bg_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end__hover', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_one_bg_color_gradient_end__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_one_bg_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end_position__hover', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_one_bg_color_gradient_end_position__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_one_bg_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'button_two_bg_color_gradient_stops__hover':
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start__hover', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_two_bg_color_gradient_start__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_two_bg_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start_position__hover', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_two_bg_color_gradient_start_position__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_two_bg_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end__hover', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_two_bg_color_gradient_end__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_two_bg_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end_position__hover', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_two_bg_color_gradient_end_position__hover'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_two_bg_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'background_color_gradient_stops__hover_1':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start__hover_1', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start__hover_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_1', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_1'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position__hover_1', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position__hover_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_1', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_1'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end__hover_1', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end__hover_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_1', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_1'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position__hover_1', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position__hover_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_1', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_1'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'background_color_gradient_stops__hover_2':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start__hover_2', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start__hover_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_2', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_2'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position__hover_2', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position__hover_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_2', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_2'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end__hover_2', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end__hover_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_2', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_2'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position__hover_2', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position__hover_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_2', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_2'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'background_color_gradient_stops__hover_3':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start__hover_3', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start__hover_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_3', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_3'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position__hover_3', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position__hover_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_3', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_3'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end__hover_3', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end__hover_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_3', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_3'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position__hover_3', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position__hover_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_3', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_3'];
					$is_inherited['end_position'] = true;
				}
				break;

			// Sticky Mode.
			// If sticky view settings are not found, check for desktop view settings and inherit (if found).
			case 'background_color_gradient_stops__sticky':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start__sticky', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position__sticky', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end__sticky', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position__sticky', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'button_bg_color_gradient_stops__sticky':
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start__sticky', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_bg_color_gradient_start__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_bg_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start_position__sticky', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_bg_color_gradient_start_position__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_bg_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end__sticky', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_bg_color_gradient_end__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_bg_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end_position__sticky', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_bg_color_gradient_end_position__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'button_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_bg_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'button_one_bg_color_gradient_stops__sticky':
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start__sticky', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_one_bg_color_gradient_start__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_one_bg_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start_position__sticky', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_one_bg_color_gradient_start_position__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_one_bg_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end__sticky', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_one_bg_color_gradient_end__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_one_bg_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end_position__sticky', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_one_bg_color_gradient_end_position__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'button_one_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_one_bg_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'button_two_bg_color_gradient_stops__sticky':
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start__sticky', $attrs ) ) {
					$old_values['start_color'] = $attrs['button_two_bg_color_gradient_start__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start', $attrs ) ) {
					$old_values['start_color']   = $attrs['button_two_bg_color_gradient_start'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start_position__sticky', $attrs ) ) {
					$old_values['start_position'] = $attrs['button_two_bg_color_gradient_start_position__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_start_position', $attrs ) ) {
					$old_values['start_position']   = $attrs['button_two_bg_color_gradient_start_position'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end__sticky', $attrs ) ) {
					$old_values['end_color'] = $attrs['button_two_bg_color_gradient_end__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end', $attrs ) ) {
					$old_values['end_color']   = $attrs['button_two_bg_color_gradient_end'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end_position__sticky', $attrs ) ) {
					$old_values['end_position'] = $attrs['button_two_bg_color_gradient_end_position__sticky'];
				} elseif ( self::existsAndIsNotEmpty( 'button_two_bg_color_gradient_end_position', $attrs ) ) {
					$old_values['end_position']   = $attrs['button_two_bg_color_gradient_end_position'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'background_color_gradient_stops__sticky_1':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start__sticky_1', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start__sticky_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_1', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_1'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position__sticky_1', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position__sticky_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_1', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_1'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end__sticky_1', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end__sticky_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_1', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_1'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position__sticky_1', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position__sticky_1'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_1', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_1'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'background_color_gradient_stops__sticky_2':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start__sticky_2', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start__sticky_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_2', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_2'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position__sticky_2', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position__sticky_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_2', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_2'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end__sticky_2', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end__sticky_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_2', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_2'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position__sticky_2', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position__sticky_2'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_2', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_2'];
					$is_inherited['end_position'] = true;
				}
				break;
			case 'background_color_gradient_stops__sticky_3':
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start__sticky_3', $attrs ) ) {
					$old_values['start_color'] = $attrs['background_color_gradient_start__sticky_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_3', $attrs ) ) {
					$old_values['start_color']   = $attrs['background_color_gradient_start_3'];
					$is_inherited['start_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position__sticky_3', $attrs ) ) {
					$old_values['start_position'] = $attrs['background_color_gradient_start_position__sticky_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_start_position_3', $attrs ) ) {
					$old_values['start_position']   = $attrs['background_color_gradient_start_position_3'];
					$is_inherited['start_position'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end__sticky_3', $attrs ) ) {
					$old_values['end_color'] = $attrs['background_color_gradient_end__sticky_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_3', $attrs ) ) {
					$old_values['end_color']   = $attrs['background_color_gradient_end_3'];
					$is_inherited['end_color'] = true;
				}
				if ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position__sticky_3', $attrs ) ) {
					$old_values['end_position'] = $attrs['background_color_gradient_end_position__sticky_3'];
				} elseif ( self::existsAndIsNotEmpty( 'background_color_gradient_end_position_3', $attrs ) ) {
					$old_values['end_position']   = $attrs['background_color_gradient_end_position_3'];
					$is_inherited['end_position'] = true;
				}
				break;

			default:
				// Bail, nothing to process.
				return $current_value;
		}

		// If we didn't find *any* old values, then bail--there's nothing to process here.
		if ( empty( implode( '', $old_values ) ) ) {
			return $current_value;
		}

		// If *all* settings have been inherited, then return `null` to unset this attribute (don't pollute).
		if ( $is_inherited['start_color'] && $is_inherited['start_position'] && $is_inherited['end_color'] && $is_inherited['end_position'] ) {
			return null;
		}

		// If colors or positions aren't defined, use the system default settings.
		if ( empty( $old_values['start_color'] ) ) {
			$old_values['start_color'] = $default_settings['start_color'];
		}
		if ( empty( $old_values['start_position'] ) ) {
			$old_values['start_position'] = $default_settings['start_position'];
		}
		if ( empty( $old_values['end_color'] ) ) {
			$old_values['end_color'] = $default_settings['end_color'];
		}
		if ( empty( $old_values['end_position'] ) ) {
			$old_values['end_position'] = $default_settings['end_position'];
		}

		// Strip percent signs and round to nearest int for our calculations.
		$pos_start      = round( floatval( $old_values['start_position'] ) );
		$pos_start_unit = trim( $old_values['start_position'], ',. 0..9' );
		$pos_end        = round( floatval( $old_values['end_position'] ) );
		$pos_end_unit   = trim( $old_values['end_position'], ',. 0..9' );

		// Our sliders use percent values, but pixel values might be manually set.
		$pos_units_match = ( $pos_start_unit === $pos_end_unit );

		// If (and ONLY if) both values use the same unit of measurement,
		// adjust the end position value to be no smaller than the start.
		if ( $pos_units_match && $pos_end < $pos_start ) {
			$pos_end = $pos_start;
		}

		// Prepare to receive the new gradient settings.
		$new_values = array(
			'start' => $old_values['start_color'] . ' ' . $pos_start . $pos_start_unit,
			'end'   => $old_values['end_color'] . ' ' . $pos_end . $pos_end_unit,
		);

		// Compile and return the migrated value for the Gradient Stops attribute.
		return implode( '|', $new_values );
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
	 * @since 4.16.0
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
		if ( in_array( $affected_field_name, self::gradientTypeFields(), true ) ) {
			// Migrate the gradient type.
			$to_field_value = self::migrateGradientType( $affected_field_value );
		} else {
			// Migrate gradient stops.
			$to_field_value = self::migrateGradientStops( $affected_field_name, $affected_field_value, $module_attrs );
		}

		return $to_field_value;
	}
}

return new ET_Builder_Module_Settings_Migration_BackgroundGradientStops();
