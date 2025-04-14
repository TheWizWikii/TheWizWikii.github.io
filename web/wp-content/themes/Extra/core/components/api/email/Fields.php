<?php


class ET_Core_API_Email_Fields {

	/**
	 * @var ET_Core_Data_Utils
	 */
	protected static $_;

	protected static $_any_custom_field_type_support;
	protected static $_predefined_custom_field_support;

	/**
	 * @var ET_Core_API_Email_Providers
	 */
	protected static $_providers;

	public static $owner;

	protected static function _custom_field_definitions() {
		$readonly_dependency   = 'builder' === self::$owner ? 'parentModule:provider' : 'email_provider';
		$custom_fields_support = array_keys( self::$_providers->names_by_slug( 'all', 'custom_fields' ) );
		$custom_fields_data    = self::$_providers->custom_fields_data();
		$fields                = array();

		if ( 'bloom' === self::$owner ) {
			$fields = array(
				'use_custom_fields' => array(
					'label'       => esc_html__( 'Use Custom Fields', 'et_core' ),
					'type'        => 'yes_no_button',
					'options'     => array(
						'on'  => esc_html__( 'Yes', 'et_core' ),
						'off' => esc_html__( 'No', 'et_core' ),
					),
					'default'     => 'off',
					'show_if'     => array(
						'email_provider' => $custom_fields_support,
					),
					'allow_dynamic' => array_keys( self::$_providers->names_by_slug( 'all', 'dynamic_custom_fields' ) ),
					'description'   => esc_html__( 'Enable this option to use custom fields in your opt-in form.', 'et_core' ),
				),
				'custom_fields'     => array(
					'type'    => 'sortable_list',
					'label'   => '',
					'default' => '[]',
				),
				'editing_field'     => array(
					'type'    => 'skip',
					'default' => 'off',
				),
			);
		}

		foreach ( $custom_fields_data as $provider => $accounts ) {
			foreach ( $accounts as $account => $lists ) {
				$account_name = str_replace( ' ', '', strtolower( $account ) );
				$key          = "predefined_field_{$provider}_{$account_name}";

				if ( isset( $lists['custom_fields'] ) ) {
					// Custom fields are per account
					$fields[ $key ] = self::_predefined_custom_field_select( $lists['custom_fields'], $provider, $account );
					continue;
				}

				foreach ( $lists as $list_id => $custom_fields ) {
					// Custom fields are per list
					$key            = "predefined_field_{$provider}_{$account_name}_{$list_id}";
					$fields[ $key ] = self::_predefined_custom_field_select( $custom_fields, $provider, $account, $list_id );
				}
			}
		}

		$labels = array(
			'link_url'      => esc_html__( 'Link URL', 'et_core' ),
			'link_text'     => esc_html__( 'Link Text', 'et_core' ),
			'link_cancel'   => esc_html__( 'Discard Changes', 'et_core' ),
			'link_save'     => esc_html__( 'Save Changes', 'et_core' ),
			'link_settings' => esc_html__( 'Option Link', 'et_core' ),
		);

		$fields = array_merge( $fields, array(
			'predefined_field' => array(
				'type'    => 'skip',
				'default' => 'none',
			),
			'field_id'         => array(
				'label'       => esc_html__( 'ID', 'et_core' ),
				'type'        => 'text',
				'default'     => '', // <--- Intentional
				'description' => esc_html__( 'Define a unique ID for this field. You should use only English characters without special characters or spaces.', 'et_core' ),
				'toggle_slug' => 'main_content',
				'readonly_if' => array(
					$readonly_dependency => self::$_predefined_custom_field_support,
				),
			),
			'field_title'      => array(
				'label'           => esc_html__( 'Name', 'et_core' ),
				'type'            => 'text',
				'description'     => esc_html__( 'Set the label that will appear above this field in the opt-in form.', 'et_core' ),
				'toggle_slug'     => 'main_content',
				'readonly_if'     => array(
					$readonly_dependency => self::$_predefined_custom_field_support,
				),
				'readonly_if_not' => array(
					$readonly_dependency => array( 'getresponse', 'sendinblue', 'constant_contact', 'fluentcrm' ),
				),
			),
			'field_type'       => array(
				'label'           => esc_html__( 'Type', 'et_core' ),
				'type'            => 'select',
				'default'         => 'none',
				'option_category' => 'basic_option',
				'options'         => array(
					'none'     => esc_html__( 'Choose a field type...', 'et_core' ),
					'input'    => esc_html__( 'Input Field', 'et_core' ),
					'email'    => esc_html__( 'Email Field', 'et_core' ),
					'text'     => esc_html__( 'Textarea', 'et_core' ),
					'checkbox' => esc_html__( 'Checkboxes', 'et_core' ),
					'radio'    => esc_html__( 'Radio Buttons', 'et_core' ),
					'select'   => esc_html__( 'Select Dropdown', 'et_core' ),
				),
				'description'     => esc_html__( 'Choose the type of field', 'et_core' ),
				'toggle_slug'     => 'field_options',
				'readonly_if'     => array(
					$readonly_dependency => self::$_predefined_custom_field_support,
				),
				'readonly_if_not' => array(
					$readonly_dependency => self::$_any_custom_field_type_support,
				),
			),
			'checkbox_checked' => array(
				'label'           => esc_html__( 'Checked By Default', 'et_core' ),
				'description'     => esc_html__( 'If enabled, the check mark will be automatically selected for the visitor. They can still deselect it.', 'et_core' ),
				'type'            => 'hidden',
				'option_category' => 'layout',
				'default'         => 'off',
				'toggle_slug'     => 'field_options',
				'show_if'         => array(
					'field_type' => 'checkbox',
				),
			),
			'checkbox_options' => array(
				'label'           => esc_html__( 'Options', 'et_core' ),
				'type'            => 'sortable_list',
				'checkbox'        => true,
				'option_category' => 'basic_option',
				'toggle_slug'     => 'field_options',
				'readonly_if'     => array(
					$readonly_dependency => self::$_predefined_custom_field_support,
				),
				'readonly_if_not' => array(
					$readonly_dependency => self::$_any_custom_field_type_support,
				),
				'show_if'         => array(
					'field_type' => 'checkbox',
				),
				'right_actions'   => 'move|link|copy|delete',
				'right_actions_readonly' => 'move|link',
				'labels'          => $labels,
			),
			'radio_options'    => array(
				'label'           => esc_html__( 'Options', 'et_core' ),
				'type'            => 'sortable_list',
				'radio'           => true,
				'option_category' => 'basic_option',
				'toggle_slug'     => 'field_options',
				'readonly_if'     => array(
					$readonly_dependency => self::$_predefined_custom_field_support,
				),
				'readonly_if_not' => array(
					$readonly_dependency => self::$_any_custom_field_type_support,
				),
				'show_if'         => array(
					'field_type' => 'radio',
				),
				'right_actions'   => 'move|link|copy|delete',
				'right_actions_readonly' => 'move|link',
				'labels'          => $labels,
			),
			'select_options'   => array(
				'label'           => esc_html__( 'Options', 'et_core' ),
				'type'            => 'sortable_list',
				'option_category' => 'basic_option',
				'toggle_slug'     => 'field_options',
				'readonly_if'     => array(
					$readonly_dependency => self::$_predefined_custom_field_support,
				),
				'readonly_if_not' => array(
					$readonly_dependency => self::$_any_custom_field_type_support,
				),
				'show_if'         => array(
					'field_type' => 'select',
				),
				'right_actions'   => 'move|copy|delete',
				'right_actions_readonly' => 'move',
			),
			'min_length'       => array(
				'label'           => esc_html__( 'Minimum Length', 'et_core' ),
				'description'     => esc_html__( 'Leave at 0 to remove restriction', 'et_core' ),
				'type'            => 'range',
				'default'         => '0',
				'unitless'        => true,
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '255',
					'step' => '1',
				),
				'option_category' => 'basic_option',
				'toggle_slug'     => 'field_options',
				'show_if'         => array(
					'field_type' => 'input',
				),
			),
			'max_length'       => array(
				'label'           => esc_html__( 'Maximum Length', 'et_core' ),
				'description'     => esc_html__( 'Leave at 0 to remove restriction', 'et_core' ),
				'type'            => 'range',
				'default'         => '0',
				'unitless'        => true,
				'range_settings'  => array(
					'min'  => '0',
					'max'  => '255',
					'step' => '1',
				),
				'option_category' => 'basic_option',
				'toggle_slug'     => 'field_options',
				'show_if'         => array(
					'field_type' => 'input',
				),
			),
			'allowed_symbols'  => array(
				'label'           => esc_html__( 'Allowed Symbols', 'et_core' ),
				'description'     => esc_html__( 'You can validate certain types of information by disallowing certain symbols. Symbols added here will prevent the form from being submitted if added by the user.', 'et_core' ),
				'type'            => 'select',
				'default'         => 'all',
				'options'         => array(
					'all'          => esc_html__( 'All', 'et_core' ),
					'letters'      => esc_html__( 'Letters Only (A-Z)', 'et_core' ),
					'numbers'      => esc_html__( 'Numbers Only (0-9)', 'et_core' ),
					'alphanumeric' => esc_html__( 'Alphanumeric Only (A-Z, 0-9)', 'et_core' ),
				),
				'option_category' => 'basic_option',
				'toggle_slug'     => 'field_options',
				'show_if'         => array(
					'field_type' => 'input',
				),
			),
			'required_mark'    => array(
				'label'           => esc_html__( 'Required Field', 'et_core' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'default'         => 'on',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_core' ),
					'off' => esc_html__( 'No', 'et_core' ),
				),
				'description'     => esc_html__( 'Define whether the field should be required or optional', 'et_core' ),
				'toggle_slug'     => 'field_options',
				'show_if_not'     => array(
					'email_provider' => 'getresponse',
				),
			),
			'hidden'           => array(
				'label'           => esc_html__( 'Hidden Field', 'et_core' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'default'         => 'off',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_core' ),
					'off' => esc_html__( 'No', 'et_core' ),
				),
				'description'     => esc_html__( 'Define whether or not the field should be visible.', 'et_core' ),
				'toggle_slug'     => 'field_options',
				'readonly_if'     => array(
					$readonly_dependency => self::$_predefined_custom_field_support,
				),
				'readonly_if_not' => array(
					$readonly_dependency => self::$_any_custom_field_type_support,
				),
			),
			'fullwidth_field'  => array(
				'label'           => esc_html__( 'Make Fullwidth', 'et_core' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'on'  => esc_html__( 'Yes', 'et_core' ),
					'off' => esc_html__( 'No', 'et_core' ),
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
				'description'     => esc_html__( 'If enabled, the field will take 100% of the width of the form, otherwise it will take 50%', 'et_core' ),
				'default'         => array( 'parent:layout', array(
					'left_right' => 'on',
					'right_left' => 'on',
					'top_bottom' => 'off',
					'bottom_top' => 'off',
				)),
			),
		) );

		if ( 'builder' === self::$owner ) {
			$fields = array_merge( $fields, array(
				'field_background_color'     => array(
					'label'        => esc_html__( 'Field Background Color', 'et_core' ),
					'description'  => esc_html__( "Pick a color to fill the module's input fields.", 'et_core' ),
					'type'         => 'color-alpha',
					'custom_color' => true,
					'toggle_slug'  => 'form_field',
					'tab_slug'     => 'advanced',
				),
				'conditional_logic'          => array(
					'label'           => esc_html__( 'Enable', 'et_core' ),
					'type'            => 'yes_no_button',
					'option_category' => 'layout',
					'default'         => 'off',
					'options'         => array(
						'on'  => esc_html__( 'Yes', 'et_core' ),
						'off' => esc_html__( 'No', 'et_core' ),
					),
					'description'     => et_get_safe_localization( __( "Enabling conditional logic makes this field only visible when any or all of the rules below are fulfilled<br><strong>Note:</strong> Only fields with an unique and non-empty field ID can be used", 'et_core' ) ),
					'toggle_slug'     => 'conditional_logic',
				),
				'conditional_logic_relation' => array(
					'label'           => esc_html__( 'Relation', 'et_core' ),
					'type'            => 'yes_no_button',
					'option_category' => 'layout',
					'options'         => array(
						'on'  => esc_html__( 'All', 'et_core' ),
						'off' => esc_html__( 'Any', 'et_core' ),
					),
					'default'         => 'off',
					'button_options'  => array(
						'button_type' => 'equal',
					),
					'description'     => esc_html__( 'Choose whether any or all of the rules should be fulfilled', 'et_core' ),
					'toggle_slug'     => 'conditional_logic',
					'show_if'         => array(
						'conditional_logic' => 'on',
					),
				),
				'conditional_logic_rules'    => array(
					'label'           => esc_html__( 'Rules', 'et_core' ),
					'description'     => esc_html__( 'Conditional logic rules can be used to hide and display different input fields conditionally based on how the visitor has interacted with different inputs.', 'et_core' ),
					'type'            => 'conditional_logic',
					'option_category' => 'layout',
					'depends_show_if' => 'on',
					'toggle_slug'     => 'conditional_logic',
					'show_if'         => array(
						'conditional_logic' => 'on',
					),
				),
			) );
		}

		if ( 'bloom' === self::$owner ) {
			foreach ( $fields as $field_slug => &$field ) {
				if ( 'use_custom_fields' !== $field_slug ) {
					self::$_->array_set( $field, 'show_if.use_custom_fields', 'on' );
				}
			}
		}

		return $fields;
	}

	protected static function _predefined_custom_field_select( $custom_fields, $provider, $account_name, $list_id = '' ) {
		$is_builder = 'builder' === self::$owner;
		$is_bloom   = 'bloom' === self::$owner;
		$dependency = $is_builder ? 'parentModule:provider' : 'email_provider';
		$show_if    = array(
			$dependency => $provider,
		);

		if ( $is_bloom ) {
			$show_if['account_name'] = $account_name;
		}

		if ( $list_id && $is_builder ) {
			$dependency             = "parentModule:{$provider}_list";
			$show_if[ $dependency ] = "{$account_name}|{$list_id}";
		} else if ( $list_id && $is_bloom ) {
			$show_if['email_list'] = (string) $list_id;
		}

		$options = array( 'none' => esc_html__( 'Choose a field...', 'et_core' ) );

		foreach ( $custom_fields as $field ) {
			$field_id             = isset( $field['group_id'] ) ? $field['group_id'] : $field['field_id'];
			$options[ $field_id ] = $field['name'];
		}

		return array(
			'label'       => esc_html__( 'Field', 'et_core' ),
			'type'        => 'select',
			'options'     => $options,
			'order'       => array_keys( $options ),
			'show_if'     => $show_if,
			'toggle_slug' => 'main_content',
			'default'     => 'none',
			'description' => esc_html__( 'Choose a custom field. Custom fields must be defined in your email provider account.', 'et_core' ),
		);
	}

	/**
	 * Get field definitions
	 *
	 * @since 3.10
	 *
	 * @param string $type Accepts 'custom_field'
	 * @param string $for Accepts 'builder', 'bloom'
	 *
	 * @return array
	 */
	public static function get_definitions( $for, $type = 'custom_field' ) {
		self::$owner = $for;
		$fields      = array();

		if ( 'custom_field' === $type ) {
			$fields = self::_custom_field_definitions();
		}

		return $fields;
	}

	public static function initialize() {
		self::$_          = ET_Core_Data_Utils::instance();
		self::$_providers = ET_Core_API_Email_Providers::instance();

		self::$_predefined_custom_field_support = array_keys( self::$_providers->names_by_slug( 'all', 'predefined_custom_fields' ) );
		self::$_any_custom_field_type_support   = self::$_providers->names_by_slug( 'all', 'any_custom_field_type' );
	}
}


ET_Core_API_Email_Fields::initialize();
