<?php

class ET_Builder_Module_Contact_Form extends ET_Builder_Module_Type_WithSpamProtection {

	function init() {
		parent::init();

		$this->name            = esc_html__( 'Contact Form', 'et_builder' );
		$this->plural          = esc_html__( 'Contact Forms', 'et_builder' );
		$this->slug            = 'et_pb_contact_form';
		$this->vb_support      = 'on';
		$this->child_slug      = 'et_pb_contact_field';
		$this->child_item_text = esc_html__( 'Field', 'et_builder' );
		$this->_use_unique_id  = true;

		$this->main_css_element = '%%order_class%%.et_pb_contact_form_container';

		$this->settings_modal_toggles = array(
			'general' => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'email'        => esc_html__( 'Email', 'et_builder' ),
					'elements'     => et_builder_i18n( 'Elements' ),
					'redirect'     => esc_html__( 'Redirect', 'et_builder' ),
					'spam'         => esc_html__( 'Spam Protection', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'        => array(
				'default' => array(
					'css'          => array(
						'main'      => array(
							'border_radii'  => sprintf( '%1$s .input, %1$s .input[type="checkbox"] + label i, %1$s .input[type="radio"] + label i', $this->main_css_element ),
							'border_styles' => sprintf( '%1$s .input, %1$s .input[type="checkbox"] + label i, %1$s .input[type="radio"] + label i', $this->main_css_element ),
						),
						'important' => 'plugin_only',
					),
					'label_prefix' => esc_html__( 'Inputs', 'et_builder' ),
				),
			),
			'fonts'          => array(
				'title'   => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main' => "{$this->main_css_element} h1, {$this->main_css_element} h2.et_pb_contact_main_title, {$this->main_css_element} h3.et_pb_contact_main_title, {$this->main_css_element} h4.et_pb_contact_main_title, {$this->main_css_element} h5.et_pb_contact_main_title, {$this->main_css_element} h6.et_pb_contact_main_title",
					),
					'header_level' => array(
						'default' => 'h1',
					),
				),
				'captcha' => array(
					'label'           => esc_html__( 'Captcha', 'et_builder' ),
					'css'             => array(
						'main' => "{$this->main_css_element} .et_pb_contact_right p",
					),
					'hide_text_align' => true,
					'line_height'     => array(
						'default' => '1.7em',
					),
				),
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main' => implode(
							', ',
							array(
								'%%order_class%% .et_pb_contact_field input',
								'%%order_class%% .et_pb_contact_field select',
								'%%order_class%% .et_pb_contact_field textarea',
								'%%order_class%% .et_pb_contact_field .et_pb_contact_field_options_list label > i',
								'%%order_class%% input.et_pb_contact_captcha',
							)
						),
					),
				),
			),
			'button'         => array(
				'button' => array(
					'label'          => et_builder_i18n( 'Button' ),
					'css'            => array(
						'main'         => "{$this->main_css_element}.et_pb_module .et_pb_button",
						'limited_main' => "{$this->main_css_element}.et_pb_module .et_pb_button",
						'important'    => 'plugin_only',
					),
					'no_rel_attr'    => true,
					'box_shadow'     => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_contact_submit',
						),
					),
					'margin_padding' => array(
						'css' => array(
							'important' => 'all',
						),
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'max_width'      => array(
				'css' => array(
					'module_alignment' => '%%order_class%%.et_pb_contact_form_container.et_pb_module',
				),
			),
			'text'           => array(
				'css' => array(
					'text_orientation' => '%%order_class%% input, %%order_class%% textarea, %%order_class%% label',
					'text_shadow'      => '%%order_class%%, %%order_class%% input, %%order_class%% textarea, %%order_class%% label, %%order_class%% select',
				),
			),
			'form_field'     => array(
				'form_field' => array(
					'label'          => esc_html__( 'Fields', 'et_builder' ),
					'css'            => array(
						'main'                         => '%%order_class%% .input',
						'background_color'             => '%%order_class%% .input, %%order_class%% .input[type="checkbox"] + label i, %%order_class%% .input[type="radio"] + label i',
						'background_color_hover'       => '%%order_class%% .input:hover, %%order_class%% .input[type="checkbox"]:hover + label i, %%order_class%% .input[type="radio"]:hover + label i',
						'focus_background_color'       => '%%order_class%% .input:focus, %%order_class%% .input[type="checkbox"]:active + label i, %%order_class%% .input[type="radio"]:active + label i',
						'focus_background_color_hover' => '%%order_class%% .input:focus:hover, %%order_class%% .input[type="checkbox"]:active:hover + label i, %%order_class%% .input[type="radio"]:active:hover + label i',
						'placeholder_focus'            => '%%order_class%% p .input:focus::-webkit-input-placeholder, %%order_class%% p .input:focus::-moz-placeholder, %%order_class%% p .input:focus:-ms-input-placeholder, %%order_class%% p textarea:focus::-webkit-input-placeholder, %%order_class%% p textarea:focus::-moz-placeholder, %%order_class%% p textarea:focus:-ms-input-placeholder',
						'padding'                      => '%%order_class%% .et_pb_contact_field .input',
						'margin'                       => '%%order_class%% .et_pb_contact_field',
						'form_text_color'              => '%%order_class%% .input, %%order_class%% .input[type="checkbox"] + label, %%order_class%% .input[type="radio"] + label, %%order_class%% .input[type="checkbox"]:checked + label i:before',
						'form_text_color_hover'        => '%%order_class%% .input:hover, %%order_class%% .input[type="checkbox"]:hover + label, %%order_class%% .input[type="radio"]:hover + label, %%order_class%% .input[type="checkbox"]:checked:hover + label i:before',
						'focus_text_color'             => '%%order_class%% .input:focus, %%order_class%% .input[type="checkbox"]:active + label, %%order_class%% .input[type="radio"]:active + label, %%order_class%% .input[type="checkbox"]:checked:active + label i:before',
						'focus_text_color_hover'       => '%%order_class%% .input:focus:hover, %%order_class%% .input[type="checkbox"]:active:hover + label, %%order_class%% .input[type="radio"]:active:hover + label, %%order_class%% .input[type="checkbox"]:checked:active:hover + label i:before',
					),
					'box_shadow'     => false,
					'border_styles'  => false,
					'font_field'     => array(
						'css' => array(
							'main'  => implode(
								', ',
								array(
									"{$this->main_css_element} .input",
									"{$this->main_css_element} .input::placeholder",
									"{$this->main_css_element} .input::-webkit-input-placeholder",
									"{$this->main_css_element} .input::-moz-placeholder",
									"{$this->main_css_element} .input:-ms-input-placeholder",
									"{$this->main_css_element} .input[type=checkbox] + label",
									"{$this->main_css_element} .input[type=radio] + label",
								)
							),
							'hover' => array(
								"{$this->main_css_element} .input:hover",
								"{$this->main_css_element} .input:hover::placeholder",
								"{$this->main_css_element} .input:hover::-webkit-input-placeholder",
								"{$this->main_css_element} .input:hover::-moz-placeholder",
								"{$this->main_css_element} .input:hover:-ms-input-placeholder",
								"{$this->main_css_element} .input[type=checkbox]:hover + label",
								"{$this->main_css_element} .input[type=radio]:hover + label",
							),
						),
					),
					'margin_padding' => array(
						'css' => array(
							'main'    => '%%order_class%% .input',
							'padding' => '%%order_class%% .et_pb_contact_field .input',
							'margin'  => '%%order_class%% .et_pb_contact_field',
						),
					),
				),
			),
		);

		$this->custom_css_fields = array(
			'contact_title'  => array(
				'label'    => esc_html__( 'Contact Title', 'et_builder' ),
				'selector' => '.et_pb_contact_main_title',
			),
			'contact_button' => array(
				'label'                    => esc_html__( 'Contact Button', 'et_builder' ),
				'selector'                 => '.et_pb_contact_form_container .et_contact_bottom_container .et_pb_contact_submit.et_pb_button',
				'no_space_before_selector' => true,
			),
			'contact_fields' => array(
				'label'    => esc_html__( 'Form Fields', 'et_builder' ),
				'selector' => 'input',
			),
			'text_field'     => array(
				'label'    => esc_html__( 'Message Field', 'et_builder' ),
				'selector' => 'textarea.et_pb_contact_message',
			),
			'captcha_field'  => array(
				'label'    => esc_html__( 'Captcha Field', 'et_builder' ),
				'selector' => 'input.et_pb_contact_captcha',
			),
			'captcha_label'  => array(
				'label'    => esc_html__( 'Captcha Text', 'et_builder' ),
				'selector' => '.et_pb_contact_right p',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'y3NSTE6BSfo',
				'name' => esc_html__( 'An introduction to the Contact Form module', 'et_builder' ),
			),
		);
	}

	/**
	 * Get form map containing essential info (form number, field id/type/required) based on
	 * et_pb_contact_field's shortcode layout
	 *
	 * @since 3.26.5
	 *
	 * @param string $content_shortcode
	 * @param int    $contact_form_number
	 * @param array  $hidden_form_fields
	 *
	 * @return mixed[] {
	 *     Form Map
	 *
	 *     @type int      $form_number Contact form number.
	 *     @type string[] $fields      {
	 *         Form Field
	 *
	 *         @type string $field_type    Field type
	 *         @type string $field_id      Field id
	 *         @type string $required_mark Required field status. Accepts 'on', 'off'.
	 *     }
	 * }
	 */
	function get_form_map( $content_shortcode = '', $contact_form_number = 0, $hidden_form_fields = array() ) {
		$pattern = get_shortcode_regex( array( 'et_pb_contact_field' ) );
		$map     = array(
			'form_number' => (int) $contact_form_number,
			'fields'      => array(),
		);

		preg_match_all( "/$pattern/", $content_shortcode, $contact_fields, PREG_SET_ORDER );

		foreach ( $contact_fields as $contact_field ) {
			$contact_field_attrs = shortcode_parse_atts( $contact_field[3] );
			$field_id            = strtolower( self::$_->array_get( $contact_field_attrs, 'field_id' ) );
			$conditional_logic   = self::$_->array_get( $contact_field_attrs, 'conditional_logic', 'off' );

			// Only allow to disable fields for which conditional logic has been enabled
			if ( 'on' === $conditional_logic && in_array( $field_id, $hidden_form_fields ) ) {
				continue;
			}

			$map['fields'][] = array(
				'field_type'    => self::$_->array_get( $contact_field_attrs, 'field_type', 'input' ),
				'field_id'      => $field_id,
				'required_mark' => self::$_->array_get( $contact_field_attrs, 'required_mark', 'on' ),
			);
		}

		return $map;
	}

	function get_fields() {
		return array_merge(
			self::_get_spam_provider_fields(),
			array(
				'captcha'            => array(
					'label'            => esc_html__( 'Use Basic Captcha', 'et_builder' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'on'  => et_builder_i18n( 'Yes' ),
						'off' => et_builder_i18n( 'No' ),
					),
					'toggle_slug'      => 'spam',
					'description'      => esc_html__( 'Turn the captcha on or off using this option.', 'et_builder' ),
					'default_on_front' => 'on',
					'show_if'          => array(
						'use_spam_service' => 'off',
					),
				),
				'email'              => array(
					'label'           => esc_html__( 'Email Address', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'basic_option',
					'description'     => et_get_safe_localization(
						sprintf(
							__( 'Input the email address where messages should be sent.<br /><br /> Note: email delivery and spam prevention are complex processes. We recommend using a delivery service such as <a href="%1$s">Mandrill</a>, <a href="%2$s">SendGrid</a>, or other similar service to ensure the deliverability of messages that are submitted through this form', 'et_builder' ),
							'http://mandrill.com/',
							'https://sendgrid.com/'
						)
					),
					'toggle_slug'     => 'email',
				),
				'title'              => array(
					'label'           => et_builder_i18n( 'Title' ),
					'type'            => 'text',
					'option_category' => 'basic_option',
					'description'     => esc_html__( 'Define a title for your contact form.', 'et_builder' ),
					'toggle_slug'     => 'main_content',
					'dynamic_content' => 'text',
					'mobile_options'  => true,
					'hover'           => 'tabs',
				),
				'custom_message'     => array(
					'label'           => esc_html__( 'Message Pattern', 'et_builder' ),
					'type'            => 'textarea',
					'option_category' => 'configuration',
					'description'     => et_get_safe_localization( __( 'Here you can define the custom pattern for the email Message. Fields should be included in following format - <strong>%%field_id%%</strong>. For example if you want to include the field with id = <strong>phone</strong> and field with id = <strong>message</strong>, then you can use the following pattern: <strong>My message is %%message%% and phone number is %%phone%%</strong>. Leave blank for default.', 'et_builder' ) ),
					'toggle_slug'     => 'email',
				),
				'use_redirect'       => array(
					'label'            => esc_html__( 'Enable Redirect URL', 'et_builder' ),
					'type'             => 'yes_no_button',
					'option_category'  => 'configuration',
					'options'          => array(
						'off' => et_builder_i18n( 'No' ),
						'on'  => et_builder_i18n( 'Yes' ),
					),
					'affects'          => array(
						'redirect_url',
					),
					'toggle_slug'      => 'redirect',
					'description'      => esc_html__( 'Redirect users after successful form submission.', 'et_builder' ),
					'default_on_front' => 'off',
				),
				'redirect_url'       => array(
					'label'           => esc_html__( 'Redirect URL', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'depends_show_if' => 'on',
					'toggle_slug'     => 'redirect',
					'description'     => esc_html__( 'Type the Redirect URL', 'et_builder' ),
				),
				'success_message'    => array(
					'label'           => esc_html__( 'Success Message', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'description'     => esc_html__( 'Type the message you want to display after successful form submission. Leave blank for default', 'et_builder' ),
					'toggle_slug'     => 'main_content',
					'dynamic_content' => 'text',
				),
				'submit_button_text' => array(
					'label'           => esc_html__( 'Submit Button', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'basic_option',
					'description'     => esc_html__( 'Define the text of the form submit button.', 'et_builder' ),
					'toggle_slug'     => 'main_content',
					'dynamic_content' => 'text',
					'mobile_options'  => true,
					'hover'           => 'tabs',
				),
			)
		);
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['form_field_background_color'] = array(
			'background-color' => implode(
				', ',
				array(
					'%%order_class%% .input',
					'%%order_class%% .input[type="checkbox"]+label i',
					'%%order_class%% .input[type="radio"]+label i',
				)
			),
		);

		return $fields;
	}

	function predefined_child_modules() {
		$output = sprintf(
			'[et_pb_contact_field field_title="%1$s" field_type="input" field_id="Name" required_mark="on" fullwidth_field="off" /][et_pb_contact_field field_title="%2$s" field_type="email" field_id="Email" required_mark="on" fullwidth_field="off" /][et_pb_contact_field field_title="%3$s" field_type="text" field_id="Message" required_mark="on" fullwidth_field="on" /]',
			esc_attr__( 'Name', 'et_builder' ),
			esc_attr__( 'Email Address', 'et_builder' ),
			esc_attr__( 'Message', 'et_builder' )
		);

		return $output;
	}

	/**
	 * Renders the module output.
	 *
	 * @param  array  $attrs       List of attributes.
	 * @param  string $content     Content being processed.
	 * @param  string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string
	 */
	public function render( $attrs, $content, $render_slug ) {
		parent::render( $attrs, $content, $render_slug );

		global $et_pb_half_width_counter, $et_pb_contact_form_num;

		$et_pb_half_width_counter = 0;

		$multi_view = et_pb_multi_view_options( $this );
		$multi_view->set_default_value( 'submit_button_text', __( 'Submit', 'et_builder' ) );

		$captcha               = $this->props['captcha'];
		$email                 = $this->props['email'];
		$title                 = $multi_view->render_element(
			array(
				'tag'     => et_pb_process_header_level( $this->props['title_level'], 'h1' ),
				'content' => '{{title}}',
				'attrs'   => array(
					'class' => 'et_pb_contact_main_title',
				),
			)
		);
		$form_field_text_color = $this->props['form_field_text_color'];
		$button_custom         = $this->props['custom_button'];
		$custom_message        = $this->props['custom_message'];
		$use_redirect          = $this->props['use_redirect'];
		$redirect_url          = $this->props['redirect_url'];
		$success_message       = $this->_esc_attr( 'success_message' );
		$header_level          = $this->props['title_level'];
		$use_spam_service      = $this->prop( 'use_spam_service', 'off' );

		$field_text_color_hover        = $this->get_hover_value( 'form_field_text_color' );
		$field_text_color_values       = et_pb_responsive_options()->get_property_values( $this->props, 'form_field_text_color' );
		$field_focus_text_color_hover  = $this->get_hover_value( 'form_field_focus_text_color' );
		$field_focus_text_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'form_field_focus_text_color' );

		$custom_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'button_icon' );
		$custom_icon        = isset( $custom_icon_values['desktop'] ) ? $custom_icon_values['desktop'] : '';
		$custom_icon_tablet = isset( $custom_icon_values['tablet'] ) ? $custom_icon_values['tablet'] : '';
		$custom_icon_phone  = isset( $custom_icon_values['phone'] ) ? $custom_icon_values['phone'] : '';

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Form Field Text Color - Radio Checked.
		$field_text_color_important = et_builder_has_limitation( 'force_use_global_important' ) ? ' !important' : '';
		et_pb_responsive_options()->generate_responsive_css( $field_text_color_values, '%%order_class%% .input[type="radio"]:checked + label i:before', 'background-color', $render_slug, $field_text_color_important, 'color' );

		if ( et_builder_is_hover_enabled( 'form_field_text_color', $this->props ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .input[type="radio"]:checked:hover + label i:before',
					'declaration' => sprintf(
						'background-color: %1$s%2$s;',
						esc_html( $field_text_color_hover ),
						$field_text_color_important
					),
				)
			);
		}

		// Form Field Text Color on Focus - Radio Checked.
		et_pb_responsive_options()->generate_responsive_css( $field_focus_text_color_values, '%%order_class%% .input[type="radio"]:checked:active + label i:before', 'background-color', $render_slug, $field_text_color_important, 'color' );

		if ( et_builder_is_hover_enabled( 'form_field_focus_text_color', $this->props ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .input[type="radio"]:checked:active:hover + label i:before',
					'declaration' => sprintf(
						'background-color: %1$s%2$s;',
						esc_html( $field_focus_text_color_hover ),
						$field_text_color_important
					),
				)
			);
		}

		$success_message = '' !== $success_message ? $success_message : esc_html__( 'Thanks for contacting us', 'et_builder' );

		$et_pb_contact_form_num = $this->render_count();
		$hidden_form_fields_key = "et_pb_contact_email_hidden_fields_{$et_pb_contact_form_num}";
		$hidden_form_fields     = self::$_->array_get( $_POST, $hidden_form_fields_key, array() );
		$shortcode_content      = $content;

		if ( ! empty( $hidden_form_fields ) ) {
			$hidden_form_fields = str_replace( '\\', '', $hidden_form_fields );
			$hidden_form_fields = json_decode( $hidden_form_fields );
		}

		$content = $this->content;

		$et_error_message        = '';
		$et_contact_error        = false;
		$current_form_fields     = isset( $_POST[ 'et_pb_contact_email_fields_' . $et_pb_contact_form_num ] ) ? $_POST[ 'et_pb_contact_email_fields_' . $et_pb_contact_form_num ] : '';
		$contact_email           = '';
		$processed_fields_values = array();

		$nonce_result = isset( $_POST[ '_wpnonce-et-pb-contact-form-submitted-' . $et_pb_contact_form_num ] ) && wp_verify_nonce( $_POST[ '_wpnonce-et-pb-contact-form-submitted-' . $et_pb_contact_form_num ], 'et-pb-contact-form-submit' ) ? true : false;

		// check that the form was submitted and et_pb_contact_et_number field is empty to protect from spam
		if ( $nonce_result && isset( $_POST[ 'et_pb_contactform_submit_' . $et_pb_contact_form_num ] ) && empty( $_POST[ 'et_pb_contact_et_number_' . $et_pb_contact_form_num ] ) ) {
			if ( '' !== $current_form_fields ) {
				$fields_data_json  = str_replace( '\\', '', $current_form_fields );
				$fields_data_array = json_decode( $fields_data_json, true );
				$fields_data_array = null === $fields_data_array ? [] : $fields_data_array;

				// check whether captcha field is not empty.
				if ( 'on' === $captcha && 'off' === $use_spam_service && ( ! isset( $_POST[ 'et_pb_contact_captcha_' . $et_pb_contact_form_num ] ) || empty( $_POST[ 'et_pb_contact_captcha_' . $et_pb_contact_form_num ] ) ) ) {
					$et_error_message .= sprintf( '<p class="et_pb_contact_error_text">%1$s</p>', esc_html__( 'Make sure you entered the captcha.', 'et_builder' ) );
					$et_contact_error  = true;

				} elseif ( 'on' === $use_spam_service && $this->is_spam_submission() ) {
					$et_error_message .= sprintf( '<p class="et_pb_contact_error_text">%1$s</p>', esc_html__( 'You must be a human to submit this form.', 'et_builder' ) );
					$et_contact_error  = true;
				}

				// check all fields on current form and generate error message if needed
				// Generate form map of submitted form.
				$submitted_form_map = array(
					'form_number' => $et_pb_contact_form_num,
					'fields'      => array(),
				);

				foreach ( $fields_data_array as $index => $value ) {
					if ( ! isset( $value['field_id'], $value['field_label'], $value['field_type'], $value['original_id'], $value['required_mark'] ) ) {
						continue;
					}

					if ( 'et_pb_contact_et_number_' . $et_pb_contact_form_num === $value['field_id'] ) {
						continue;
					}

					// Populate form map's fields.
					$submitted_form_map['fields'][] = array(
						'field_type'    => self::$_->array_get( $value, 'field_type', 'input' ),
						'field_id'      => self::$_->array_get( $value, 'original_id' ),
						'required_mark' => 'required' === self::$_->array_get( $value, 'required_mark', 'required' ) ? 'on' : 'off',
					);

					// Check all the required fields, generate error message if required field is empty.
					// Use `sanitize_textarea_field` for message field content to preserve newlines.
					$sanitize_callback = isset( $value['original_id'] ) && 'text' === $value['field_type'] ? 'sanitize_textarea_field' : 'sanitize_text_field';

					// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- The $sanitize_callback will sanitize the field value.
					$field_value = isset( $_POST[ $value['field_id'] ] ) ? trim( call_user_func( $sanitize_callback, $_POST[ $value['field_id'] ] ) ) : '';

					if ( 'required' === $value['required_mark'] && empty( $field_value ) && ! is_numeric( $field_value ) ) {
						$et_error_message .= sprintf( '<p class="et_pb_contact_error_text">%1$s</p>', esc_html__( 'Make sure you fill in all required fields.', 'et_builder' ) );
						$et_contact_error  = true;
						continue;
					}

					// additional check for email field.
					if ( 'email' === $value['field_type'] && ! empty( $field_value ) ) {
						$contact_email = isset( $_POST[ $value['field_id'] ] ) ? sanitize_email( $_POST[ $value['field_id'] ] ) : '';

						if ( 'required' === $value['required_mark'] && ( empty( $contact_email ) || ! is_email( $contact_email ) ) ) {
							$et_error_message .= sprintf( '<p class="et_pb_contact_error_text">%1$s</p>', esc_html__( 'Invalid Email.', 'et_builder' ) );
							$et_contact_error  = true;
						}
					}

					// prepare the array of processed field values in convenient format.
					if ( false === $et_contact_error ) {
						$processed_fields_values[ $value['original_id'] ]['value'] = $field_value;
						$processed_fields_values[ $value['original_id'] ]['label'] = $value['field_label'];
					}
				}

				// Check form's integrity by comparing fields structure (used for required fields check, etc)
				// stored in the shortcode against submitted value generated using JS on the front end
				// to prevent data being altered by modifying form markup.
				$form_map = $this->get_form_map( $shortcode_content, $et_pb_contact_form_num, $hidden_form_fields );

				// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.serialize_serialize -- doing equality check between two serialized arrays
				if ( serialize( $submitted_form_map ) !== serialize( $form_map ) ) {
					$et_error_message .= sprintf( '<p class="et_pb_contact_error_text">%1$s</p>', esc_html__( 'Invalid submission. Please refresh the page and try again.', 'et_builder' ) );
					$et_contact_error  = true;
				}

			} else {
				$et_error_message .= sprintf( '<p class="et_pb_contact_error_text">%1$s</p>', esc_html__( 'Make sure you fill in all required fields.', 'et_builder' ) );
				$et_contact_error  = true;
			}
		} else {
			if ( false === $nonce_result && isset( $_POST[ 'et_pb_contactform_submit_' . $et_pb_contact_form_num ] ) && empty( $_POST[ 'et_pb_contact_et_number_' . $et_pb_contact_form_num ] ) ) {
				$et_error_message .= sprintf( '<p class="et_pb_contact_error_text">%1$s</p>', esc_html__( 'Please refresh the page and try again.', 'et_builder' ) );
			}
			$et_contact_error = true;
		}

		// generate digits for captcha
		$et_pb_first_digit  = rand( 1, 15 );
		$et_pb_second_digit = rand( 1, 15 );

		if ( ! $et_contact_error && $nonce_result ) {
			$et_email_to = '' !== $email
				? $email
				: get_site_option( 'admin_email' );

			$et_site_name = strval( get_option( 'blogname' ) );

			$contact_name = isset( $processed_fields_values['name'] ) ? stripslashes( sanitize_text_field( $processed_fields_values['name']['value'] ) ) : '';

			if ( '' !== $custom_message ) {
				// decode html entites to make sure HTML from the message pattern is rendered properly
				$message_pattern = et_builder_convert_line_breaks( html_entity_decode( $custom_message ), "\r\n" );

				// insert the data from contact form into the message pattern
				foreach ( $processed_fields_values as $key => $value ) {
					// strip all tags from each field. Don't strip tags from the entire message to allow using HTML in the pattern.
					$message_pattern = str_ireplace( "%%{$key}%%", wp_strip_all_tags( $value['value'] ), $message_pattern );
				}

				if ( is_array( $hidden_form_fields ) ) {
					foreach ( $hidden_form_fields as $hidden_field_label ) {
						$message_pattern = str_ireplace( "%%{$hidden_field_label}%%", '', $message_pattern );
					}
				}
			} else {
				// use default message pattern if custom pattern is not defined
				$message_pattern = isset( $processed_fields_values['message']['value'] ) ? $processed_fields_values['message']['value'] : '';

				// Add all custom fields into the message body by default
				foreach ( $processed_fields_values as $key => $value ) {
					if ( ! in_array( $key, array( 'message', 'name', 'email' ) ) ) {
						$message_pattern .= "\r\n";
						$message_pattern .= sprintf(
							'%1$s: %2$s',
							'' !== $value['label'] ? $value['label'] : $key,
							$value['value']
						);
					}
				}

				// strip all tags from the message content
				$message_pattern = wp_strip_all_tags( $message_pattern );
			}

			$http_host = str_replace( 'www.', '', $_SERVER['HTTP_HOST'] );

			$headers[] = "From: \"{$contact_name}\" <mail@{$http_host}>";

			// Set `Reply-To` email header based on contact_name and contact_email values
			if ( ! empty( $contact_email ) ) {
				$contact_name = ! empty( $contact_name ) ? $contact_name : $contact_email;
				$headers[]    = "Reply-To: \"{$contact_name}\" <{$contact_email}>";
			}

			add_filter( 'et_get_safe_localization', 'et_allow_ampersand' );

			// don't strip tags at this point to properly send the HTML from pattern. All the unwanted HTML stripped at this point.
			$email_message = trim( stripslashes( $message_pattern ) );

			wp_mail(
				apply_filters( 'et_contact_page_email_to', $et_email_to ),
				et_get_safe_localization(
					sprintf(
						__( 'New Message From %1$s%2$s', 'et_builder' ),
						sanitize_text_field( html_entity_decode( $et_site_name, ENT_QUOTES, 'UTF-8' ) ),
						( '' !== $title ? sprintf( _x( ' - %s', 'contact form title separator', 'et_builder' ), $title ) : '' )
					)
				),
				! empty( $email_message ) ? $email_message : ' ',
				apply_filters( 'et_contact_page_headers', $headers, $contact_name, $contact_email )
			);

			remove_filter( 'et_get_safe_localization', 'et_allow_ampersand' );

			$et_error_message = sprintf( '<p>%1$s</p>', et_core_esc_previously( $success_message ) );
		}

		// Contact form should always have the ID. Use saved ID or generate automatically.
		$module_id = '' !== $this->module_id( false ) ? $this->module_id( false ) : 'et_pb_contact_form_' . $et_pb_contact_form_num;
		$unique_id = self::$_->array_get( $this->props, '_unique_id' );

		if ( $nonce_result ) {
			// Additional info to be passed on the `et_pb_contact_form_submit` hook.
			$contact_form_info = array(
				'contact_form_id'        => $module_id,
				'contact_form_number'    => $et_pb_contact_form_num,
				'contact_form_unique_id' => $unique_id,
				'module_slug'            => $render_slug,
				'post_id'                => $this->get_the_ID(),
			);

			/**
			 * Fires after contact form is submitted.
			 *
			 * Use $et_contact_error variable to check whether there is an error on the form
			 * entry submit process or not.
			 *
			 * @since 4.13.1
			 *
			 * @param array $processed_fields_values Processed fields values.
			 * @param array $et_contact_error        Whether there is an error on the form
			 *                                       entry submit process or not.
			 * @param array $contact_form_info       Additional contact form info.
			 */
			do_action( 'et_pb_contact_form_submit', $processed_fields_values, $et_contact_error, $contact_form_info );
		}

		$form        = '';
		$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$et_pb_captcha = sprintf(
			'
			<div class="et_pb_contact_right">
				<p class="clearfix">
					<span class="et_pb_contact_captcha_question">%1$s</span> = <input type="text" size="2" class="input et_pb_contact_captcha" data-first_digit="%3$s" data-second_digit="%4$s" value="" name="et_pb_contact_captcha_%2$s" data-required_mark="required" autocomplete="off">
				</p>
			</div>',
			sprintf( '%1$s + %2$s', esc_html( $et_pb_first_digit ), esc_html( $et_pb_second_digit ) ),
			esc_attr( $et_pb_contact_form_num ),
			esc_attr( $et_pb_first_digit ),
			esc_attr( $et_pb_second_digit )
		);

		if ( '' === trim( $content ) ) {
			$content = do_shortcode( $this->predefined_child_modules() );
		}

		if ( $et_contact_error ) {
			$multi_view_data_attr = $multi_view->render_attrs(
				array(
					'content' => '{{submit_button_text}}',
				)
			);

			$form = sprintf(
				'
				<div class="et_pb_contact">
					<form class="et_pb_contact_form clearfix" method="post" action="%1$s">
						%7$s
						<input type="hidden" value="et_contact_proccess" name="et_pb_contactform_submit_%6$s"/>
						<div class="et_contact_bottom_container">
							%2$s
							<button type="submit" name="et_builder_submit_button" class="et_pb_contact_submit et_pb_button"%5$s%8$s%9$s%10$s>%3$s</button>
						</div>
						%4$s
					</form>
				</div>',
				esc_url( $current_url ),
				( 'on' === $captcha && 'off' === $use_spam_service ? $et_pb_captcha : '' ),
				esc_html( $multi_view->get_value( 'submit_button_text' ) ),
				wp_nonce_field( 'et-pb-contact-form-submit', '_wpnonce-et-pb-contact-form-submitted-' . $et_pb_contact_form_num, true, false ),
				'' !== $custom_icon && 'on' === $button_custom ? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $custom_icon ) )
				) : '', // #5
				esc_attr( $et_pb_contact_form_num ),
				$content,
				'' !== $custom_icon_tablet && 'on' === $button_custom ? sprintf( ' data-icon-tablet="%1$s"', esc_attr( et_pb_process_font_icon( $custom_icon_tablet ) ) ) : '',
				'' !== $custom_icon_phone && 'on' === $button_custom ? sprintf( ' data-icon-phone="%1$s"', esc_attr( et_pb_process_font_icon( $custom_icon_phone ) ) ) : '',
				$multi_view_data_attr // #10
			);
		}

		// Module classnames
		$this->add_classname(
			array(
				'et_pb_contact_form_container',
				'clearfix',
				$this->get_text_orientation_classname(),
			)
		);

		// Remove automatically added classname
		$this->remove_classname( $render_slug );

		$output = sprintf(
			'
			<div id="%4$s" class="%5$s" data-form_unique_num="%6$s" data-form_unique_id="%10$s"%7$s>
				%9$s
				%8$s
				%11$s
				%12$s
				%1$s
				<div class="et-pb-contact-message">%2$s</div>
				%3$s
			</div>
			',
			$title,
			$et_error_message,
			$form,
			esc_attr( $module_id ),
			$this->module_classname( $render_slug ), // #5
			esc_attr( $et_pb_contact_form_num ),
			'on' === $use_redirect && '' !== $redirect_url ? sprintf( ' data-redirect_url="%1$s"', esc_attr( $redirect_url ) ) : '',
			$video_background,
			$parallax_image_background,
			esc_attr( $unique_id ), // #10
			et_core_esc_previously( $this->background_pattern() ), // #11
			et_core_esc_previously( $this->background_mask() ) // #12
		);

		return $output;
	}

	/**
	 * Filter multi view value.
	 *
	 * @since 3.27.1
	 *
	 * @see ET_Builder_Module_Helper_MultiViewOptions::filter_value
	 *
	 * @param mixed                                     $raw_value Props raw value.
	 * @param array                                     $args {
	 *                                         Context data.
	 *
	 *     @type string $context      Context param: content, attrs, visibility, classes.
	 *     @type string $name         Module options props name.
	 *     @type string $mode         Current data mode: desktop, hover, tablet, phone.
	 *     @type string $attr_key     Attribute key for attrs context data. Example: src, class, etc.
	 *     @type string $attr_sub_key Attribute sub key that availabe when passing attrs value as array such as styes. Example: padding-top, margin-botton, etc.
	 * }
	 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Multiview object instance.
	 *
	 * @return mixed
	 */
	public function multi_view_filter_value( $raw_value, $args, $multi_view ) {
		$name = isset( $args['name'] ) ? $args['name'] : '';
		$mode = isset( $args['mode'] ) ? $args['mode'] : '';

		$fields_need_escape = array(
			'title',
		);

		if ( $raw_value && in_array( $name, $fields_need_escape, true ) ) {
			return $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'none', $raw_value );
		} elseif ( 'submit_button_text' === $name ) {
			if ( '' === trim( $raw_value ) ) {
				$raw_value = __( 'Submit', 'et_builder' );
			}

			return esc_html( $raw_value );
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Contact_Form();
}
