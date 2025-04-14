<?php
/**
 * Migration process to handle all the changes done in Options Harmony v2's QF.
 *
 * @since 3.23
 */
class ET_Builder_Module_Settings_Migration_OptionsHarmony2 extends ET_Builder_Module_Settings_Migration {

	/**
	 * Migration Version
	 *
	 * @since 3.23
	 * @todo Should be replaced with the correct release version.
	 *
	 * @var string
	 */
	public $version = '3.23';

	/**
	 * Get all fields need to be migrated.
	 *
	 * Contains array with:
	 * - key as new field
	 * - value consists affected fields as old field and module location
	 *
	 * @since 3.23
	 *
	 * @return array New and old fields need to be migrated.
	 */
	public function get_fields() {
		return array(
			// Form Field BG Color.
			'form_field_background_color'                 => array(
				'affected_fields' => array(
					'form_background_color'  => $this->get_modules( 'form_bg_color' ),
					'field_background_color' => $this->get_modules( 'field_bg_color' ),
				),
			),
			'form_field_background_color__hover_enabled'  => array(
				'affected_fields' => array(
					'form_background_color__hover_enabled' => $this->get_modules( 'contact_form' ),
					'field_background_color__hover_enabled' => $this->get_modules( 'contact_form_field' ),
				),
			),
			'form_field_background_color__hover'          => array(
				'affected_fields' => array(
					'form_background_color__hover'  => $this->get_modules( 'contact_form' ),
					'field_background_color__hover' => $this->get_modules( 'contact_form_field' ),
				),
			),
			// Form Field Focus BG Color.
			'form_field_focus_background_color'           => array(
				'affected_fields' => array(
					'focus_background_color' => $this->get_modules( 'focus_bg_color' ),
				),
			),
			'form_field_focus_background_color__hover_enabled' => array(
				'affected_fields' => array(
					'focus_background_color__hover_enabled' => $this->get_modules( 'login' ),
				),
			),
			'form_field_focus_background_color__hover'    => array(
				'affected_fields' => array(
					'focus_background_color__hover' => $this->get_modules( 'login' ),
				),
			),
			// Form Field Focus Text Color.
			'form_field_focus_text_color'                 => array(
				'affected_fields' => array(
					'focus_text_color' => $this->get_modules( 'focus_bg_color' ),
				),
			),
			'form_field_focus_text_color__hover_enabled'  => array(
				'affected_fields' => array(
					'focus_text_color__hover_enabled' => $this->get_modules( 'login' ),
				),
			),
			'form_field_focus_text_color__hover'          => array(
				'affected_fields' => array(
					'focus_text_color__hover' => $this->get_modules( 'login' ),
				),
			),
			// Form Field Font.
			'form_field_text_color'                       => array(
				'affected_fields' => array(
					'input_text_color' => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_color__hover_enabled'        => array(
				'affected_fields' => array(
					'input_text_color__hover_enabled' => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_color__hover'                => array(
				'affected_fields' => array(
					'input_text_color__hover' => $this->get_modules( 'search' ),
				),
			),
			'form_field_font'                             => array(
				'affected_fields' => array(
					'input_font' => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_align'                       => array(
				'affected_fields' => array(
					'input_text_align' => $this->get_modules( 'search' ),
				),
			),
			'form_field_font_size'                        => array(
				'affected_fields' => array(
					'input_font_size' => $this->get_modules( 'search' ),
				),
			),
			'form_field_font_size_last_edited'            => array(
				'affected_fields' => array(
					'input_font_size_last_edited' => $this->get_modules( 'search' ),
				),
			),
			'form_field_font_size_tablet'                 => array(
				'affected_fields' => array(
					'input_font_size_tablet' => $this->get_modules( 'search' ),
				),
			),
			'form_field_font_size_phone'                  => array(
				'affected_fields' => array(
					'input_font_size_phone' => $this->get_modules( 'search' ),
				),
			),
			'form_field_font_size__hover_enabled'         => array(
				'affected_fields' => array(
					'input_font_size__hover_enabled' => $this->get_modules( 'search' ),
				),
			),
			'form_field_font_size__hover'                 => array(
				'affected_fields' => array(
					'input_font_size__hover' => $this->get_modules( 'search' ),
				),
			),
			'form_field_letter_spacing'                   => array(
				'affected_fields' => array(
					'input_letter_spacing' => $this->get_modules( 'search' ),
				),
			),
			'form_field_letter_spacing_last_edited'       => array(
				'affected_fields' => array(
					'input_letter_spacing_last_edited' => $this->get_modules( 'search' ),
				),
			),
			'form_field_letter_spacing_tablet'            => array(
				'affected_fields' => array(
					'input_letter_spacing_tablet' => $this->get_modules( 'search' ),
				),
			),
			'form_field_letter_spacing_phone'             => array(
				'affected_fields' => array(
					'input_letter_spacing_phone' => $this->get_modules( 'search' ),
				),
			),
			'form_field_letter_spacing__hover_enabled'    => array(
				'affected_fields' => array(
					'input_letter_spacing__hover_enabled' => $this->get_modules( 'search' ),
				),
			),
			'form_field_letter_spacing__hover'            => array(
				'affected_fields' => array(
					'input_letter_spacing__hover' => $this->get_modules( 'search' ),
				),
			),
			'form_field_line_height'                      => array(
				'affected_fields' => array(
					'input_line_height' => $this->get_modules( 'search' ),
				),
			),
			'form_field_line_height_last_edited'          => array(
				'affected_fields' => array(
					'input_line_height_last_edited' => $this->get_modules( 'search' ),
				),
			),
			'form_field_line_height_tablet'               => array(
				'affected_fields' => array(
					'input_line_height_tablet' => $this->get_modules( 'search' ),
				),
			),
			'form_field_line_height_phone'                => array(
				'affected_fields' => array(
					'input_line_height_phone' => $this->get_modules( 'search' ),
				),
			),
			'form_field_line_height__hover_enabled'       => array(
				'affected_fields' => array(
					'input_line_height__hover_enabled' => $this->get_modules( 'search' ),
				),
			),
			'form_field_line_height__hover'               => array(
				'affected_fields' => array(
					'input_line_height__hover' => $this->get_modules( 'search' ),
				),
			),
			// Form Field Text Shadow.
			'form_field_text_shadow_horizontal_length'    => array(
				'affected_fields' => array(
					'fields_text_shadow_horizontal_length' => $this->get_modules( 'text_shadow' ),
					'input_text_shadow_horizontal_length'  => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_shadow_horizontal_length__hover_enabled' => array(
				'affected_fields' => array(
					'fields_text_shadow_horizontal_length__hover_enabled' => $this->get_modules( 'text_shadow' ),
					'input_text_shadow_horizontal_length__hover_enabled'  => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_shadow_horizontal_length__hover' => array(
				'affected_fields' => array(
					'fields_text_shadow_horizontal_length__hover' => $this->get_modules( 'text_shadow' ),
					'input_text_shadow_horizontal_length__hover'  => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_shadow_vertical_length'      => array(
				'affected_fields' => array(
					'fields_text_shadow_vertical_length' => $this->get_modules( 'text_shadow' ),
					'input_text_shadow_vertical_length'  => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_shadow_vertical_length__hover_enabled' => array(
				'affected_fields' => array(
					'fields_text_shadow_vertical_length__hover_enabled' => $this->get_modules( 'text_shadow' ),
					'input_text_shadow_vertical_length__hover_enabled'  => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_shadow_vertical_length__hover' => array(
				'affected_fields' => array(
					'fields_text_shadow_vertical_length__hover' => $this->get_modules( 'text_shadow' ),
					'input_text_shadow_vertical_length__hover'  => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_shadow_blur_strength'        => array(
				'affected_fields' => array(
					'fields_text_shadow_blur_strength' => $this->get_modules( 'text_shadow' ),
					'input_text_shadow_blur_strength'  => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_shadow_blur_strength__hover_enabled' => array(
				'affected_fields' => array(
					'fields_text_shadow_blur_strength__hover_enabled' => $this->get_modules( 'text_shadow' ),
					'input_text_shadow_blur_strength__hover_enabled'  => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_shadow_blur_strength__hover' => array(
				'affected_fields' => array(
					'fields_text_shadow_blur_strength__hover' => $this->get_modules( 'text_shadow' ),
					'input_text_shadow_blur_strength__hover'  => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_shadow_color'                => array(
				'affected_fields' => array(
					'fields_text_shadow_color' => $this->get_modules( 'text_shadow' ),
					'input_text_shadow_color'  => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_shadow_color__hover_enabled' => array(
				'affected_fields' => array(
					'fields_text_shadow_color__hover_enabled' => $this->get_modules( 'text_shadow' ),
					'input_text_shadow_color__hover_enabled'  => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_shadow_color__hover'         => array(
				'affected_fields' => array(
					'fields_text_shadow_color__hover' => $this->get_modules( 'text_shadow' ),
					'input_text_shadow_color__hover'  => $this->get_modules( 'search' ),
				),
			),
			'form_field_text_shadow_style'                => array(
				'affected_fields' => array(
					'fields_text_shadow_style' => $this->get_modules( 'text_shadow' ),
					'input_text_shadow_style'  => $this->get_modules( 'search' ),
				),
			),
			// Image.
			'align_last_edited'                           => array(
				'affected_fields' => array(
					'always_center_on_mobile' => $this->get_modules( 'image' ),
				),
			),
			'align_tablet'                                => array(
				'affected_fields' => array(
					'always_center_on_mobile' => $this->get_modules( 'image' ),
				),
			),
			// Price Excluded Color.
			'excluded_text_color'                         => array(
				'affected_fields' => array(
					'pricing_item_excluded_color' => $this->get_modules( 'pricing_table' ),
				),
			),
			'excluded_text_color__hover_enabled'          => array(
				'affected_fields' => array(
					'pricing_item_excluded_color__hover_enabled' => $this->get_modules( 'pricing_table' ),
				),
			),
			'excluded_text_color__hover'                  => array(
				'affected_fields' => array(
					'pricing_item_excluded_color__hover' => $this->get_modules( 'pricing_table' ),
				),
			),
			// Price Excluded Color.
			'body_text_align'                             => array(
				'affected_fields' => array(
					'center_list_items' => $this->get_modules( 'pricing_tables' ),
				),
			),
		);
	}

	/**
	 * Get all modules affected.
	 *
	 * Pass attribute and it will return selected modules only. Default return all affected modules.
	 *
	 * @since 3.23
	 *
	 * @param  string $attr Attribute name.
	 *
	 * @return [type]       [description]
	 */
	public function get_modules( $attr = '' ) {
		$modules = array();

		// Comments.
		if ( in_array( $attr, array( '', 'comments' ) ) ) {
			$modules[] = 'et_pb_comments';
		}

		// Contact Form.
		if ( in_array( $attr, array( '', 'contact_form' ) ) ) {
			$modules[] = 'et_pb_contact_form';
		}

		// Contact Form Field.
		if ( in_array( $attr, array( '', 'contact_form_field' ) ) ) {
			$modules[] = 'et_pb_contact_field';
		}

		// Email Optin.
		if ( in_array( $attr, array( '', 'email_optin' ) ) ) {
			$modules[] = 'et_pb_signup';
		}

		// Email Optin Custom Field.
		if ( in_array( $attr, array( '', 'email_optin_field' ) ) ) {
			$modules[] = 'et_pb_signup_custom_field';
		}

		// Image.
		if ( in_array( $attr, array( '', 'image' ) ) ) {
			$modules[] = 'et_pb_image';
		}

		// Login.
		if ( in_array( $attr, array( '', 'login' ) ) ) {
			$modules[] = 'et_pb_login';
		}

		// Search.
		if ( in_array( $attr, array( '', 'search' ) ) ) {
			$modules[] = 'et_pb_search';
		}

		// Pricing Table.
		if ( in_array( $attr, array( '', 'pricing_tables' ) ) ) {
			$modules[] = 'et_pb_pricing_tables';
		}

		if ( in_array( $attr, array( '', 'pricing_table' ) ) ) {
			$modules[] = 'et_pb_pricing_table';
		}

		// Form BG Color options group.
		if ( 'form_bg_color' === $attr ) {
			$modules[] = 'et_pb_comments';
			$modules[] = 'et_pb_contact_form';
		}

		// Form BG Color options group.
		if ( 'field_bg_color' === $attr ) {
			$modules[] = 'et_pb_signup_custom_field';
			$modules[] = 'et_pb_contact_field';
		}

		// Form BG Color options group.
		if ( in_array( $attr, array( 'focus_bg_color', 'text_shadow' ) ) ) {
			$modules[] = 'et_pb_signup';
			$modules[] = 'et_pb_login';
		}

		return $modules;
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {

		// Migrate Email Optin fields text shadow. There is a conflict on Fields & Email Optin. There
		// are two different Text Shadow settings. The first one under Fields settings, the second one
		// under Field Text. In the process, the second one has higher priority than the first one. And
		// it has the same prefix with current form field. So, we need to check if saved value (2nd) is
		// exist and not empty, return it and no need to migrate.
		if ( 'et_pb_signup' === $module_slug ) {
			$text_shadow_property_name = $this->get_form_field_text_shadow( $field_name );
			if ( '' !== $text_shadow_property_name ) {
				// If form_field_text_shadow_style value is not 'none', we need to return current saved
				// value or 2nd text shadow settings value (Field Text).
				$form_field_text_shadow_style = isset( $attrs['form_field_text_shadow_style'] ) ? $attrs['form_field_text_shadow_style'] : '';
				if ( '' !== $form_field_text_shadow_style && 'none' !== $form_field_text_shadow_style ) {
					return $saved_value;
				}

				return $current_value;
			}
		}

		// Migrate Always Center Image on Mobile field as Image Alignment. If current value is
		// 'off', there is nothing we need to do here. But, if it's 'on', we need to set value
		// for align_tablet and align_last_edited.
		if ( 'et_pb_image' === $module_slug ) {
			if ( 'align_last_edited' === $field_name && 'off' !== $current_value ) {
				// If always_center_on_mobile is on (the current value is ''), we need to set last
				// edited value as 'on|desktop'.
				return 'on|desktop';
			} elseif ( 'align_tablet' === $field_name && 'off' !== $current_value ) {
				// If always_center_on_mobile is on (the current value is ''), we need to set align
				// tablet value as center.
				return 'center';
			}

			return $saved_value;
		}

		// Migrate Center List Items as Body Tetx Alignment. If current value is 'off', there is
		// nothing we need to do here. But, if it's 'on', we need to set value for body_text_align
		// as 'center'.
		if ( 'et_pb_pricing_tables' === $module_slug ) {
			if ( 'body_text_align' === $field_name && 'on' === $current_value && empty( $saved_value ) ) {
				// If center_list_items is on (the current value is 'on'), we need to set body text
				// align value as 'center'. However, only do this if current $saved_value is empty
				// because body_text_align has higher priority than center_list_items.
				return 'center';
			}

			return $saved_value;
		}

		// Don't migrate empty value.
		if ( ! empty( $current_value ) ) {
			return $current_value;
		}

		return $saved_value;
	}

	/**
	 * Text shadow properties need to be migrated.
	 *
	 * It's only used to check if current text shadow property is the correct field to migrate.
	 *
	 * @since 3.23
	 *
	 * @param  string $field_name New field name.
	 * @return string             Old field name.
	 */
	public function get_form_field_text_shadow( $field_name = '' ) {
		$text_shadow_properties = array(
			'form_field_text_shadow_style'                => 'fields_text_shadow_style',
			'form_field_text_shadow_horizontal_length'    => 'fields_text_shadow_horizontal_length',
			'form_field_text_shadow_horizontal_length__hover_enabled' => 'fields_text_shadow_horizontal_length__hover_enabled',
			'form_field_text_shadow_horizontal_length__hover' => 'fields_text_shadow_horizontal_length__hover',
			'form_field_text_shadow_vertical_length'      => 'fields_text_shadow_vertical_length',
			'form_field_text_shadow_vertical_length__hover_enabled' => 'fields_text_shadow_vertical_length__hover_enabled',
			'form_field_text_shadow_vertical_length__hover' => 'fields_text_shadow_vertical_length__hover',
			'form_field_text_shadow_blur_strength'        => 'fields_text_shadow_blur_strength',
			'form_field_text_shadow_blur_strength__hover' => 'fields_text_shadow_blur_strength__hover',
			'form_field_text_shadow_blur_strength__hover_enabled' => 'fields_text_shadow_blur_strength__hover_enabled',
			'form_field_text_shadow_color'                => 'fields_text_shadow_color',
			'form_field_text_shadow_color__hover_enabled' => 'fields_text_shadow_color__hover_enabled',
			'form_field_text_shadow_color__hover'         => 'fields_text_shadow_color__hover',
		);

		return isset( $text_shadow_properties[ $field_name ] ) ? $text_shadow_properties[ $field_name ] : '';
	}
}

return new ET_Builder_Module_Settings_Migration_OptionsHarmony2();
