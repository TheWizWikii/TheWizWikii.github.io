<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Checkout_Additional_Info class
 *
 * The ET_Builder_Module_Woocommerce_Checkout_Additional_Info Class is responsible for rendering the
 * Checkout Additional Info section using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since 4.14.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Checkout Additional Info component.
 */
final class ET_Builder_Module_Woocommerce_Checkout_Additional_Info extends ET_Builder_Module {

	/**
	 * Initialize.
	 *
	 * @since 4.14.0 Fixed PHP Warnings {@link https://github.com/elegantthemes/Divi/issues/22104}
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Checkout Information', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Checkout Information', 'et_builder' );
		$this->slug        = 'et_pb_wc_checkout_additional_info';
		$this->vb_support  = 'on';
		$this->folder_name = 'et_pb_woo_modules';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements' => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'title'       => array(
						'title'    => esc_html__( 'Title Text', 'et_builder' ),
						'priority' => 49,
					),
					'field_label' => array(
						'title'    => esc_html__( 'Field Labels', 'et_builder' ),
						'priority' => 60,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'        => array(
				'title'       => array(
					'label'       => esc_html__( 'Title', 'et_builder' ),
					'css'         => array(
						'main' => '%%order_class%% h3',
					),
					'font_size'   => array(
						'default' => '22px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'title',
				),
				'field_label' => array(
					'label'       => esc_html__( 'Field Label', 'et_builder' ),
					'css'         => array(
						'main' => '%%order_class%% form .form-row label',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '2em',
					),
					'toggle_slug' => 'field_label',
				),
			),
			'text'         => array(
				'use_background_layout' => false,
				'use_text_orientation'  => true,
			),
			'button'       => false,
			'link_options' => false,
			'form_field'   => array(
				'form_field' => array(
					'label'           => esc_html__( 'Fields', 'et_builder' ),
					'css'             => array(
						'main'             => '%%order_class%% form .form-row .input-text',
						'focus_text_color' => implode(
							',',
							[
								'.woocommerce %%order_class%% form .form-row .input-text:focus',
								'.woocommerce-page %%order_class%% form .form-row .input-text:focus',
							]
						),
					),
					'box_shadow'      => false,
					'border_styles'   => array(
						'form_field'       => array(
							'label_prefix' => esc_html__( 'Fields', 'et_builder' ),
							'css'          => array(
								'main'     => array(
									'border_styles' => '%%order_class%% .et_pb_module_inner form .form-row textarea',
									'border_radii'  => '%%order_class%% .et_pb_module_inner form .form-row textarea',
								),
								'defaults' => array(
									'border_radii'  => 'on|0px|0px|0px|0px',
									'border_styles' => array(
										'width' => '0px',
										'style' => 'solid',
									),
								),
							),
						),
						'form_field_focus' => array(
							'label_prefix' => esc_html__( 'Fields Focus', 'et_builder' ),
							'css'          => array(
								'main'     => array(
									'border_styles' => '%%order_class%% form .form-row textarea.input-text:focus',
									'border_radii'  => '%%order_class%% form .form-row textarea.input-text:focus',
								),
								'defaults' => array(
									'border_radii'  => 'on|0px|0px|0px|0px',
									'border_styles' => array(
										'width' => '0px',
										'style' => 'solid',
									),
								),
							),
						),
					),
					'font_field'      => array(
						'css'         => array(
							'main'      => implode(
								', ',
								array(
									'%%order_class%% form .form-row .input-text',
									'%%order_class%% form .form-row .input-text::placeholder',
									'%%order_class%% form .form-row .input-text::-webkit-input-placeholder',
									'%%order_class%% form .form-row .input-text::-moz-placeholder',
									'%%order_class%% form .form-row .input-text:-ms-input-placeholder',
								)
							),
							'hover'     => implode(
								', ',
								array(
									'%%order_class%% form .input-text',
									'%%order_class%% form .input-text:hover::placeholder',
									'%%order_class%% form .input-text:hover::-webkit-input-placeholder',
									'%%order_class%% form .input-text:hover::-moz-placeholder',
									'%%order_class%% form .input-text:hover:-ms-input-placeholder',
								)
							),
							// Required to override default WooCommerce styles.
							'important' => array( 'line-height', 'font', 'size' ),
						),
						'font_size'   => array(
							'default' => '14px',
						),
						'line_height' => array(
							'default' => '1.7em',
						),
					),
					'margin_padding'  => array(
						'css'            => array(
							'main'    => '%%order_class%% form .form-row textarea',
							'padding' => '%%order_class%% form .form-row textarea.input-text',
						),
						'custom_padding' => array(
							'default' => '15px|15px|15px|15px|false|false',
						),
					),
					'width'           => array(),
					'toggle_priority' => 65,
				),
			),
		);

		$this->custom_css_fields = array(
			'title_text'         => array(
				'label'    => esc_html__( 'Title Text', 'et_builder' ),
				'selector' => '%%order_class%% h3',
			),
			'field_label'        => array(
				'label'    => esc_html__( 'Field Label', 'et_builder' ),
				'selector' => '%%order_class%% form .form-row label',
			),
			'fields'             => array(
				'label'    => esc_html__( 'Fields', 'et_builder' ),
				'selector' => '%%order_class%% form .input-text',
			),
			'fields_placeholder' => array(
				'label'    => esc_html__( 'Fields Placeholder', 'et_builder' ),
				'selector' => implode(
					', ',
					array(
						'%%order_class%% form .form-row .input-text::placeholder',
						'%%order_class%% form .form-row .input-text::-webkit-input-placeholder',
						'%%order_class%% form .form-row .input-text::-moz-placeholder',
						'%%order_class%% form .form-row .input-text:-ms-input-placeholder',
					)
				),
			),
		);

		$this->help_videos = array(
			array(
				'id'   => esc_html( '7X03vBPYJ1o' ),
				'name' => esc_html__( 'Divi WooCommerce Modules', 'et_builder' ),
			),
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_fields() {
		$fields = array(
			'show_title'                 => array(
				'label'            => esc_html__( 'Show Title', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Turn title on or off.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'placeholder_color'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'placeholder_color'
			),
			'__checkout_additional_info' => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Checkout_Additional_Info',
					// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- Call to a function.
					'get_additional_info'
				),
				'computed_depends_on' => array(

					/*
					 * `depends_on` arg is required. Otherwise AJAX will return null.
					 * @see et_pb_process_computed_property().
					 * `product` will not be processed since there is no definition in
					 * @see ET_Builder_Module_Woocommerce_Checkout_Order::get_fields()
					 */
					'product',
				),
			),
		);

		return $fields;
	}

	/**
	 * Reset hooks.
	 */
	public static function maybe_reset_hooks( $conditional_tags ) {
		$is_tb = et_()->array_get( $conditional_tags, 'is_tb', false );

		ET_Builder_Module_Helper_Woocommerce_Modules::attach_wc_checkout_coupon_form();
		ET_Builder_Module_Helper_Woocommerce_Modules::attach_wc_checkout_login_form();
		ET_Builder_Module_Helper_Woocommerce_Modules::attach_wc_checkout_order_review();
		ET_Builder_Module_Helper_Woocommerce_Modules::attach_wc_checkout_payment();

		if ( ! et_fb_is_computed_callback_ajax() && ! $is_tb ) {
			remove_filter(
				'wc_get_template',
				[
					'ET_Builder_Module_Woocommerce_Checkout_Additional_Info',
					'swap_template',
				],
				10,
				5
			);
		}
	}

	/**
	 * Handle hooks.
	 */
	public static function maybe_handle_hooks( $conditional_tags ) {
		$is_tb = et_()->array_get( $conditional_tags, 'is_tb', false );

		ET_Builder_Module_Helper_Woocommerce_Modules::detach_wc_checkout_coupon_form();
		ET_Builder_Module_Helper_Woocommerce_Modules::detach_wc_checkout_login_form();
		ET_Builder_Module_Helper_Woocommerce_Modules::detach_wc_checkout_order_review();
		ET_Builder_Module_Helper_Woocommerce_Modules::detach_wc_checkout_payment();

		if ( ! et_fb_is_computed_callback_ajax() && ! $is_tb ) {
			add_filter(
				'wc_get_template',
				[
					'ET_Builder_Module_Woocommerce_Checkout_Additional_Info',
					'swap_template',
				],
				10,
				5
			);
		}
	}

	/**
	 * Invoke WooCommerce hooks.
	 *
	 * When Dynamic framework is enabled, some of WooCommerce's actions/filters
	 * won't be invoked because of lazy loading.
	 *
	 * Since WooCommerce's hooks are required before render() they are
	 * invoked using the `et_builder_module_lazy_shortcodes_registered` hook.
	 *
	 * @see et_builder_wc_init()
	 * @see ET_Builder_Module_Shortcode_Manager::register_all_shortcodes()
	 */
	public static function maybe_invoke_woocommerce_hooks() {
		/*
		 * The respective remove_filter is used at
		 *
		 * @see ET_Builder_Module_Woocommerce_Checkout_Additional_Info::get_additional_info()
		 */
		add_filter(
			'woocommerce_checkout_fields',
			array(
				'ET_Builder_Module_Woocommerce_Checkout_Additional_Info',
				// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- Call to a function.
				'modify_order_comments_rows'
			)
		);
	}

	/**
	 * Increases the Checkout Information Textarea `rows` attribute.
	 *
	 * @param array $fields Array of checkout fields.
	 *
	 * @return array
	 */
	public static function modify_order_comments_rows( $fields ) {
		if ( ! is_array( $fields ) ) {
			return $fields;
		}

		if ( ! isset( $fields['order'] ) || ! isset( $fields['order']['order_comments'] ) ) {
			return $fields;
		}

		$fields['order']['order_comments']['custom_attributes']['rows'] = 4;

		return $fields;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['placeholder_color'] = array(
			'color' => array(
				'%%order_class%% form .form-row textarea.input-text::placeholder',
				'%%order_class%% form .form-row textarea.input-text::-webkit-input-placeholder',
				'%%order_class%% form .form-row textarea.input-text::-moz-placeholder',
				'%%order_class%% form .form-row textarea.input-text:-ms-input-placeholder',
			),
		);

		return $fields;
	}

	/**
	 * Swaps Checkout Order Details template.
	 *
	 * Coupon Remove Link must be shown in VB. Hence we swap the template.
	 *
	 * @param string $template      Template.
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments.
	 * @param string $template_path Template path.
	 * @param string $default_path  Default path.
	 *
	 * @return string
	 */
	public static function swap_template( $template, $template_name, $args, $template_path, $default_path ) {
		$is_template_override = in_array(
			$template_name,
			array(
				'checkout/form-checkout.php',
			),
			true
		);

		if ( $is_template_override ) {
			return trailingslashit( ET_BUILDER_DIR ) . 'feature/woocommerce/templates/' . $template_name;
		}

		return $template;
	}

	/**
	 * Gets the Checkout Additional Info markup.
	 *
	 * @param array $args Settings used to render the module's output.
	 *                    Refer ET_Builder_Element::props.
	 *
	 * @return string
	 */
	public static function get_additional_info( $args = array(), $conditional_tags = array() ) {
		self::maybe_handle_hooks( $conditional_tags );

		$is_cart_empty = function_exists( 'WC' ) && isset( WC()->cart ) && WC()->cart->is_empty();

		// Is Page Builder mode?.
		$is_pb_mode = et_fb_is_computed_callback_ajax() || is_et_pb_preview();

		// Set dummy cart contents to output Additional Information when no product is in cart.
		if ( ( $is_cart_empty && $is_pb_mode ) || is_et_pb_preview() ) {
			add_filter(
				'woocommerce_get_cart_contents',
				array(
					'ET_Builder_Module_Helper_Woocommerce_Modules',
					// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- Call to a function.
					'set_dummy_cart_contents'
				)
			);
		}

		// Show Checkout Additional Info module title.
		add_filter( 'woocommerce_cart_needs_shipping', '__return_false' );

		add_filter(
			'woocommerce_checkout_fields',
			array(
				'ET_Builder_Module_Woocommerce_Checkout_Additional_Info',
				// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- Call to a function.
				'modify_order_comments_rows'
			)
		);

		ob_start();

		WC_Shortcode_Checkout::output( array() );

		$markup = ob_get_clean();

		remove_filter(
			'woocommerce_checkout_fields',
			array(
				'ET_Builder_Module_Woocommerce_Checkout_Additional_Info',
				// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- Call to a function.
				'modify_order_comments_rows'
			)
		);

		// Reset showing Checkout Additional Info module title.
		remove_filter( 'woocommerce_cart_needs_shipping', '__return_false' );

		if ( ( $is_cart_empty && $is_pb_mode ) || is_et_pb_preview() ) {
			remove_filter(
				'woocommerce_get_cart_contents',
				array(
					'ET_Builder_Module_Helper_Woocommerce_Modules',
					// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- Call to a function.
					'set_dummy_cart_contents'
				)
			);
		}

		self::maybe_reset_hooks( $conditional_tags );

		// Fallback.
		if ( ! is_string( $markup ) ) {
			$markup = '';
		}

		return $markup;
	}

	/**
	 * Adds Multi view attributes to the Outer wrapper.
	 *
	 * Since we do not have control over the WooCommerce Additional Info markup,
	 * we inject Multi view attributes on to the Outer wrapper.
	 *
	 * @since 4.14.0
	 *
	 * @param array $outer_wrapper_attrs Outer wrapper attributes.
	 *
	 * @return array
	 */
	public function add_multi_view_attrs( $outer_wrapper_attrs ) {
		$multi_view = et_pb_multi_view_options( $this );

		$multi_view_attrs = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_wc_no_title' => array(
						'show_title' => 'off',
					),
				),
			),
			false,
			null,
			true
		);

		if ( $multi_view_attrs && is_array( $multi_view_attrs ) ) {
			$outer_wrapper_attrs = array_merge( $outer_wrapper_attrs, $multi_view_attrs );
		}

		return $outer_wrapper_attrs;
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
		if ( function_exists( 'is_order_received_page' )
			&& is_order_received_page() ) {
			return '';
		}

		// Add classes to hide disabled elements.
		if ( 'off' === $this->prop( 'show_title', 'on' ) ) {
			$this->add_classname( 'et_pb_wc_no_title' );
		}

		$this->add_classname( $this->get_text_orientation_classname() );

		add_filter( "et_builder_module_{$render_slug}_outer_wrapper_attrs", array( $this, 'add_multi_view_attrs' ) );

		// Placeholder Color.
		$placeholder_selectors = array(
			'%%order_class%% form .form-row textarea.input-text::placeholder',
			'%%order_class%% form .form-row textarea.input-text::-webkit-input-placeholder',
			'%%order_class%% form .form-row textarea.input-text::-moz-placeholder',
			'%%order_class%% form .form-row textarea.input-text:-ms-input-placeholder',
		);

		$this->generate_styles(
			array(
				'base_attr_name'                  => 'placeholder_color',
				'selector'                        => join( ', ', $placeholder_selectors ),
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'important'                       => false,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		global $wp;
		if ( ! empty( $wp->query_vars['order-pay'] ) ) {
			$this->add_classname( 'et_pb_wc_order_pay' );
		}

		if ( isset( WC()->cart )
			&& ! is_null( WC()->cart && method_exists( WC()->cart, 'check_cart_items' ) )
			&& ! is_et_pb_preview() ) {
			$return = WC()->cart->check_cart_items();

			if ( wc_notice_count( 'error' ) > 0 ) {
				$this->add_classname( 'et_pb_hide_module' );
			}
		}

		$output = self::get_additional_info( $this->props );

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Checkout_Additional_Info();
