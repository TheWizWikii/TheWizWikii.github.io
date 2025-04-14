<?php

class ET_Builder_Module_Signup_Item extends ET_Builder_Module {

	public $child_title_var  = 'field_title';
	public $bb_support       = false;
	public $main_css_element = '.et_pb_newsletter_form %%order_class%%';
	public $no_render        = true;
	public $slug             = 'et_pb_signup_custom_field';
	public $type             = 'child';
	public $vb_support       = 'on';

	public function init() {
		$this->name                        = esc_html__( 'Custom Field', 'et_builder' );
		$this->plural                      = esc_html__( 'Custom Fields', 'et_builder' );
		$this->advanced_setting_title_text = $this->name;
		$this->settings_text               = esc_html__( 'Custom Field Settings', 'et_builder' );
	}

	public function get_advanced_fields_config() {
		return array(
			'background'     => array(
				'css' => array(
					'main' => '%%order_class%%',
				),
			),
			'borders'        => array(
				'default' => array(
					'css'          => array(
						'main'      => array(
							'border_radii'  => sprintf( '%1$s .input, %1$s .input[type="checkbox"] + label i, %1$s .input[type="radio"] + label i', '.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%%' ),
							'border_styles' => sprintf( '%1$s .input, %1$s .input[type="checkbox"] + label i, %1$s .input[type="radio"] + label i', '.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%%' ),
						),
						'important' => 'plugin_only',
					),
					'label_prefix' => esc_html__( 'Input', 'et_builder' ),
					'defaults'     => array(
						'border_radii'  => 'on|3px|3px|3px|3px',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
					'fields_after' => array(
						'use_focus_border_color' => array(
							'label'            => esc_html__( 'Use Focus Borders', 'et_builder' ),
							'description'      => esc_html__( 'Enabling this option will add borders to input fields when focused.', 'et_builder' ),
							'type'             => 'yes_no_button',
							'option_category'  => 'color_option',
							'options'          => array(
								'off' => et_builder_i18n( 'No' ),
								'on'  => et_builder_i18n( 'Yes' ),
							),
							'affects'          => array(
								'border_radii_focus',
								'border_styles_focus',
							),
							'tab_slug'         => 'advanced',
							'toggle_slug'      => 'border',
							'default_on_front' => 'off',
						),
					),
				),
				'focus'   => array(
					'label_prefix'    => esc_html__( 'Input Focus', 'et_builder' ),
					'css'             => array(
						'main'      => array(
							'border_radii'  => sprintf( '%1$s .input:focus, %1$s .input[type="checkbox"]:focus + label i, %1$s .input[type="radio"]:focus + label i', '.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%%' ),
							'border_styles' => sprintf( '%1$s .input:focus, %1$s .input[type="checkbox"]:focus + label i, %1$s .input[type="radio"]:focus + label i', '.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%%' ),
						),
						'important' => 'plugin_only',
					),
					'option_category' => 'border',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'border',
					'depends_on'      => array( 'use_focus_border_color' ),
					'depends_show_if' => 'on',
					'defaults'        => array(
						'border_radii'  => 'on|3px|3px|3px|3px',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
				),
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main'      => implode(
							', ',
							array(
								'%%order_class%% input',
								'%%order_class%% select',
								'%%order_class%% textarea',
								'%%order_class%% .et_pb_contact_field_options_list label > i',
							)
						),
						'important' => true,
					),
				),
			),
			'filters'        => array(
				'css' => array(
					'main' => array(
						'%%order_class%% input',
						'%%order_class%% textarea',
						'%%order_class%% label',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main'      => '.et_pb_newsletter_form p%%order_class%%',
					'padding'   => '.et_pb_newsletter_form p%%order_class%%.et_pb_newsletter_field.et_pb_signup_custom_field',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'text'           => array(
				'css' => array(
					'text_orientation' => '%%order_class%% input, %%order_class%% textarea, %%order_class%% label',
				),
			),
			'text_shadow'    => array(
				// Don't add text-shadow fields since they already are via font-options
				'default' => false,
			),
			'form_field'     => array(
				'form_field' => array(
					'label'          => esc_html__( 'Field', 'et_builder' ),
					'css'            => array(
						'main'                   => '.et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% .input',
						'background_color'       => '.et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% input[type="text"], .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% textarea, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% select, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% .input[type="checkbox"] + label i, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% .input[type="radio"] + label i',
						'background_color_hover' => '.et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% input[type="text"]:hover, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% textarea:hover, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% select:hover, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% .input[type="checkbox"] + label:hover i, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% .input[type="radio"] + label:hover i',
						'focus_background_color' => '.et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% input.input:focus, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% textarea:focus, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% select:focus',
						'form_text_color'        => '.et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% input[type="text"], .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% textarea, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% select, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% .input[type="checkbox"] + label i:before',
						'form_text_color_hover'  => '.et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% input[type="text"]:hover, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% textarea:hover, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% select:hover, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% .input[type="checkbox"] + label:hover i:before',
						'focus_text_color'       => '.et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% .input:focus',
						'placeholder_focus'      => '.et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% .input:focus::-webkit-input-placeholder, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% .input:focus::-moz-placeholder, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% .input:focus:-ms-input-placeholder, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% textarea:focus::-webkit-input-placeholder, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% textarea:focus::-moz-placeholder, .et_pb_newsletter_form .et_pb_newsletter_fields %%order_class%% textarea:focus:-ms-input-placeholder',
						'important'              => array( 'form_text_color', 'padding', 'margin' ),
					),
					'margin_padding' => false,
					'box_shadow'     => false,
					'border_styles'  => false,
					'font_field'     => array(
						'css' => array(
							'main'      => array(
								'%%order_class%%.et_pb_contact_field .et_pb_contact_field_options_title',
								'.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%% .input',
								'.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%% .input::-webkit-input-placeholder',
								'.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%% .input::-moz-placeholder',
								'.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%% .input:-ms-input-placeholder',
								'.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%% .input[type=checkbox] + label',
								'.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%% .input[type=radio] + label',
							),
							'hover'     => array(
								'%%order_class%%.et_pb_contact_field .et_pb_contact_field_options_title:hover',
								'.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%% .input:hover',
								'.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%% .input:hover::-webkit-input-placeholder',
								'.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%% .input:hover::-moz-placeholder',
								'.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%% .input:hover:-ms-input-placeholder',
								'.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%% .input[type=checkbox] + label:hover',
								'.et_pb_newsletter_form .et_pb_newsletter_fields p%%order_class%% .input[type=radio] + label:hover',
							),
							'important' => 'plugin_only',
						),
					),
				),
			),
			'sticky'         => false,
		);
	}

	public function get_fields() {
		$fields = ET_Core_API_Email_Fields::get_definitions( 'builder' );

		// Remove field background color because we already have one that support responsive settings
		// on Form Field element.
		if ( isset( $fields['field_background_color'] ) ) {
			unset( $fields['field_background_color'] );
		}

		return $fields;
	}

	public function get_settings_modal_toggles() {
		return array(
			'general'  => array(
				'toggles' => array(
					'main_content'      => esc_html__( 'Field', 'et_builder' ),
					'field_options'     => esc_html__( 'Field Options', 'et_builder' ),
					'conditional_logic' => esc_html__( 'Conditional Logic', 'et_builder' ),
					'background'        => et_builder_i18n( 'Background' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => et_builder_i18n( 'Layout' ),
				),
			),
		);
	}
}

new ET_Builder_Module_Signup_Item();
