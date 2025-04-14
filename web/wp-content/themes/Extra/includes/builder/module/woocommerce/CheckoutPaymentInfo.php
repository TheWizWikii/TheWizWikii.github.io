<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Checkout_Payment_Info class
 *
 * The ET_Builder_Module_Woocommerce_Checkout_Payment_Info Class is responsible for rendering the
 * Checkout payment info using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since 4.14.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Checkout Payment component.
 */
class ET_Builder_Module_Woocommerce_Checkout_Payment_Info extends ET_Builder_Module {
	/**
	 * Initialize.
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Checkout Payment', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Checkout Payment', 'et_builder' );
		$this->slug        = 'et_pb_wc_checkout_payment_info';
		$this->vb_support  = 'on';
		$this->folder_name = 'et_pb_woo_modules';

		$this->main_css_element = implode(
			',',
			array(
				'%%order_class%% .woocommerce-checkout #payment',
				'%%order_class%% .woocommerce-order',
			)
		);

		$this->settings_modal_toggles = array(
			'advanced' => array(
				'toggles' => array(
					'body'                  => array(
						'title'             => esc_html__( 'Body Text', 'et_builder' ),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => array(
							'p' => array(
								'name' => 'P',
								'icon' => 'text-left',
							),
							'a' => array(
								'name' => 'A',
								'icon' => 'text-link',
							),
						),
						'priority'          => 52,
					),
					'radio_button'          => array(
						'title'    => esc_html__( 'Radio Buttons', 'et_builder' ),
						'priority' => 65,
					),
					'selected_radio_button' => array(
						'title'    => esc_html__( 'Selected Radio Button', 'et_builder' ),
						'priority' => 70,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'body' => array(
					'label'       => et_builder_i18n( 'Body' ),
					'css'         => array(
						'main'      => implode(
							',',
							array(
								'%%order_class%% .woocommerce-privacy-policy-text',
								'%%order_class%% .woocommerce-privacy-policy-text a',
								'%%order_class%% .wc_payment_method a',

								// Order confirmation Page elements.
								'%%order_class%% .woocommerce-order p',
								'%%order_class%% .woocommerce-order .woocommerce-order-overview',
							)
						),
						'important' => array( 'size', 'line-height' ),
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.7em',
					),
					'toggle_slug' => 'body',
					'sub_toggle'  => 'p',
				),
				'link' => array(
					'label'       => et_builder_i18n( 'Link' ),
					'css'         => array(
						'main'      => implode(
							',',
							array(
								'%%order_class%% .woocommerce-privacy-policy-text a',
								'%%order_class%% .wc_payment_method a',
							)
						),
						// CPT style uses `!important` so outputting important is inevitable.
						'important' => 'all',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.7em',
					),
					'toggle_slug' => 'body',
					'sub_toggle'  => 'a',
				),
			),
			'link_options'   => false,
			'button'         => array(
				'button' => array(
					'label'           => esc_html__( 'Button', 'et_builder' ),
					'css'             => array(
						'main' => '%%order_class%% #payment #place_order',
					),
					'use_alignment'   => false,
					'border_width'    => array(
						'default' => '2px',
					),
					'box_shadow'      => array(
						'css' => array(
							'main' => '%%order_class%% #payment #place_order',
						),
					),
					'margin_padding'  => array(
						'css' => array(
							'important' => 'all',
						),
					),
					'toggle_priority' => 80,
				),
			),
			'form_field'     => array(
				'radio_button'          => array(
					'label'                  => esc_html__( 'Radio Button', 'et_builder' ),
					'css'                    => array(
						'main'        => '%%order_class%% #payment .wc_payment_method',
						'text_shadow' => '%%order_class%% #payment .wc_payment_method label',
					),
					'background_color'       => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s radio buttons.', 'et_builder' ),
					),
					'text_color'             => array(
						'description' => esc_html__( 'Pick a color to be used for the text written next to radio button.', 'et_builder' ),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => '%%order_class%% #payment .wc_payment_method',
						),
					),
					'border_styles'          => array(
						'radio_button' => array(
							'label_prefix' => esc_html__( 'Radio Button', 'et_builder' ),
							'css'          => array(
								'main' => array(
									'border_styles' => '%%order_class%% #payment .wc_payment_method',
									'border_radii'  => '%%order_class%% #payment .wc_payment_method',
								),
							),
							'defaults'     => array(
								'border_radii' => 'off|0px|0px|0px|0px',
								'border_style' => array(
									'width' => '0px',
									'style' => 'none',
								),
							),
						),
					),
					'font_field'             => array(
						'css'         => array(
							'main'       => '%%order_class%% #payment .wc_payment_method label',
							'focus'      => '%%order_class%% #payment .input-radio:focus',
							'text_align' => '%%order_class%% #payment ul.payment_methods li',
						),
						'font_size'   => array(
							'default' => '14px',
						),
						'line_height' => array(
							'default' => '1.4em',
						),
					),
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'margin_padding'         => array(
						'css' => array(
							// Different from main css selector for added specificity.
							'margin'  => '%%order_class%% #payment ul.payment_methods li',
							'padding' => '%%order_class%% #payment ul.payment_methods li',
						),
					),
					'width'                  => array(),
				),
				'selected_radio_button' => array(
					'label'                  => esc_html__( 'Selected Radio Button', 'et_builder' ),
					'css'                    => array(
						'main'        => '%%order_class%% #payment .wc_payment_method.et_pb_checked',
						'text_shadow' => '%%order_class%% #payment .wc_payment_method.et_pb_checked label',
					),
					'background_color'       => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s selected radio button.', 'et_builder' ),
					),
					'text_color'             => array(
						'description' => esc_html__( 'Pick a color to be used for the text written next to selected radio button.', 'et_builder' ),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => '%%order_class%% #payment .wc_payment_method.et_pb_checked',
						),
					),
					'border_styles'          => array(
						'selected_radio_button' => array(
							'label_prefix' => esc_html__( 'Selected Radio Button', 'et_builder' ),
							'css'          => array(
								'main' => array(
									'border_styles' => '%%order_class%% #payment .wc_payment_method.et_pb_checked',
									'border_radii'  => '%%order_class%% #payment .wc_payment_method.et_pb_checked',
								),
							),
							'defaults'     => array(
								'border_radii' => 'off|0px|0px|0px|0px',
								'border_style' => array(
									'width' => '0px',
									'style' => 'none',
								),
							),
						),
					),
					'font_field'             => array(
						'css'         => array(
							'main'       => '%%order_class%% #payment .wc_payment_method.et_pb_checked label',
							'focus'      => '%%order_class%% #payment .wc_payment_method.et_pb_checked .input-radio:focus',
							'text_align' => '%%order_class%% #payment ul.payment_methods li.et_pb_checked',
						),
						'font_size'   => array(
							'default' => '14px',
						),
						'line_height' => array(
							'default' => '1.4em',
						),
					),
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'margin_padding'         => array(
						'css' => array(
							// Different from main css selector for added specificity.
							'margin'  => '%%order_class%% #payment ul.payment_methods li.et_pb_checked',
							'padding' => '%%order_class%% #payment ul.payment_methods li.et_pb_checked',
						),
					),
					'width'                  => array(),
				),
				'tooltip'               => array(
					'label'                  => esc_html__( 'Tooltip', 'et_builder' ),
					'css'                    => array(
						'main' => '%%order_class%% #payment div.payment_box',
					),
					'background_color'       => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s tooltip.', 'et_builder' ),
					),
					'font_field'             => array(
						'css'             => array(
							'main' => '%%order_class%% .wc_payment_method p',
						),
						'font_size'       => array(
							'default'        => '',
							'allowed_values' => et_builder_get_acceptable_css_string_values( 'width' ),
							'allow_empty'    => true,
						),
						'line_height'     => array(
							'default' => '1.5em',
						),
						'hide_text_color' => false,
					),
					'margin_padding'         => array(
						'css' => array(
							'main' => '%%order_class%% #payment div.payment_box',
						),
					),
					'text_color'             => false,
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'border_styles'          => array(
						'tooltip' => array(
							'label_prefix' => 'Tooltip',
							'css'          => array(
								'main' => array(
									'border_styles' => '%%order_class%% #payment div.payment_box',
									'border_radii'  => '%%order_class%% #payment div.payment_box',
								),
							),
							'defaults'     => array(
								'border_radii' => 'on|2px|2px|2px|2px',
							),
						),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => '%%order_class%% #payment div.payment_box',
						),
					),
					'toggle_priority'        => 70,
				),
				'form_notice'           => array(
					'label'                  => esc_html__( 'Form Notice', 'et_builder' ),
					'css'                    => array(
						'main' => '%%order_class%% #payment ul.payment_methods li.woocommerce-info',
					),
					'background_color'       => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s notice.', 'et_builder' ),
					),
					'font_field'             => array(
						'css'             => array(
							'main'      => '%%order_class%% #payment ul.payment_methods li.woocommerce-notice',
							'important' => array( 'size', 'text-shadow' ),
						),
						'font_size'       => array(
							'default' => '18px',
						),
						'line_height'     => array(
							'default' => '1.7em',
						),
						'hide_text_color' => false,
					),
					'margin_padding'         => array(
						'css'            => array(
							'main'      => '%%order_class%% #payment ul.payment_methods li.woocommerce-info',
							'important' => array( 'custom_padding' ),
						),
						'custom_padding' => array(
							'default' => '15px|15px|15px|15px|false|false',
						),
					),
					'text_color'             => false,
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'border_styles'          => array(
						'form_notice' => array(
							'label_prefix'      => esc_html__( 'Notice', 'et_builder' ),
							'css'               => array(
								'main'      => array(
									'border_styles' => '%%order_class%% #payment ul.payment_methods li.woocommerce-info',
									'border_radii'  => '%%order_class%% #payment ul.payment_methods li.woocommerce-info',
								),
								'important' => true,
							),
							'defaults'          => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '0px',
									'style' => 'solid',
								),
							),
							'use_focus_borders' => false,
						),
					),
					'box_shadow'             => array(
						'css' => array(
							'main'      => '%%order_class%% #payment ul.payment_methods li.woocommerce-info',
							'important' => true,
						),
					),
					'toggle_priority'        => 75,
				),
			),
			'background'     => array(
				'css'     => array(
					// Backgrounds need to be applied to module wrapper.
					'main' => '%%order_class%%.et_pb_wc_checkout_payment_info',
				),
				'options' => array(
					'background_color' => array(
						'default' => '#ebe9eb',
					),
				),
			),
			'borders'        => array(
				'default' => array(
					'css'      => array(
						'main' => implode(
							',',
							array(
								'%%order_class%% .woocommerce-checkout #payment',
								'%%order_class%% .woocommerce-order',
							)
						),
					),
					'defaults' => array(
						'border_radii'  => 'on|5px|5px|5px|5px',
						'border_styles' => array(
							'width' => '0px',
							'style' => 'solid',
							'color' => '#eee',
						),
					),
				),
			),
			'margin_padding' => array(
				'css'            => array(
					'main' => implode(
						',',
						array(
							'%%order_class%% .woocommerce-checkout #payment',
							'%%order_class%% .woocommerce-order',
						)
					),
				),
				'custom_padding' => array(
					'default' => '1em|1em|1em|1em|false|false',
				),
			),
		);

		$this->custom_css_fields = array(
			'fields'      => array(
				'label'    => esc_html__( 'Fields', 'et_builder' ),
				'selector' => '%%order_class%% #payment .input-radio',
			),
			'body'        => array(
				'label'    => esc_html__( 'Body', 'et_builder' ),
				'selector' => '%%order_class%% .woocommerce-privacy-policy-text',
			),
			'body_anchor' => array(
				'label'    => esc_html__( 'Body Link', 'et_builder' ),
				'selector' => '%%order_class%% .woocommerce-privacy-policy-text a',
			),
			'button'      => array(
				'label'    => esc_html__( 'Button', 'et_builder' ),
				'selector' => '%%order_class%% #payment #place_order',
			),
			'tooltip'     => array(
				'label'    => esc_html__( 'Tooltip', 'et_builder' ),
				'selector' => '%%order_class%% #payment div.payment_box',
			),
			'form_notice' => array(
				'label'    => esc_html__( 'Form Notice', 'et_builder' ),
				'selector' => '%%order_class%% #payment ul.payment_methods li.woocommerce-info',
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
			'__checkout_payment_info' => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Checkout_Payment_Info',
					// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- This is a function call.
					'get_checkout_payment_info'
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
	 * Gets the Button classname.
	 *
	 * @used-by ET_Builder_Module_Helper_Woocommerce_Modules::add_custom_button_icons()
	 *
	 * @return string
	 */
	public function get_button_classname() {
		return 'button';
	}

	/**
	 * Swaps login form template.
	 *
	 * By default WooCommerce displays these only when logged-out.
	 * However these templates must be shown in VB when logged-in. Hence we use these templates.
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
				'checkout/payment.php',
				'checkout/payment-method.php',
			),
			true
		);

		if ( $is_template_override ) {
			return trailingslashit( ET_BUILDER_DIR ) . 'feature/woocommerce/templates/' . $template_name;
		}

		return $template;
	}

	/**
	 * Reset hooks.
	 */
	public static function maybe_reset_hooks() {
		ET_Builder_Module_Helper_Woocommerce_Modules::attach_wc_checkout_coupon_form();
		ET_Builder_Module_Helper_Woocommerce_Modules::attach_wc_checkout_login_form();
		ET_Builder_Module_Helper_Woocommerce_Modules::attach_wc_checkout_billing();
		ET_Builder_Module_Helper_Woocommerce_Modules::attach_wc_checkout_shipping();
		ET_Builder_Module_Helper_Woocommerce_Modules::attach_wc_checkout_order_review();
	}

	/**
	 * Handle hooks.
	 */
	public static function maybe_handle_hooks() {
		ET_Builder_Module_Helper_Woocommerce_Modules::detach_wc_checkout_coupon_form();
		ET_Builder_Module_Helper_Woocommerce_Modules::detach_wc_checkout_login_form();
		ET_Builder_Module_Helper_Woocommerce_Modules::detach_wc_checkout_billing();
		ET_Builder_Module_Helper_Woocommerce_Modules::detach_wc_checkout_shipping();
		ET_Builder_Module_Helper_Woocommerce_Modules::detach_wc_checkout_order_review();
	}

	/**
	 * Gets the Checkout Payment info markup.
	 *
	 * @param array $args             Additional arguments.
	 * @param array $conditional_tags Array of conditional tags.
	 *
	 * @return string
	 */
	public static function get_checkout_payment_info( $args = array(), $conditional_tags = array() ) {
		if ( ! class_exists( 'WC_Shortcode_Checkout' )
			|| ! method_exists( 'WC_Shortcode_Checkout', 'output' ) ) {
			return '';
		}

		$is_tb = et_()->array_get( $conditional_tags, 'is_tb', false );

		self::maybe_handle_hooks();

		$is_cart_empty = function_exists( 'WC' ) && isset( WC()->cart ) && WC()->cart->is_empty();
		$is_pb_mode    = et_fb_is_computed_callback_ajax() || is_et_pb_preview();
		$class         = 'ET_Builder_Module_Helper_Woocommerce_Modules';

		// Set dummy cart contents to output Billing when no product is in cart.
		if ( ( $is_cart_empty && $is_pb_mode ) || is_et_pb_preview() ) {
			add_filter(
				'woocommerce_get_cart_contents',
				array( $class, 'set_dummy_cart_contents' )
			);
		}

		if ( et_fb_is_computed_callback_ajax() || $is_tb || is_et_pb_preview() ) {
			/*
			 * Show Login form in VB.
			 *
			 * The swapped login form will display irrespective of the user logged-in status.
			 *
			 * Previously swapped template (FE) will only display the form when
			 * a user is not logged-in. Hence we use a different template in VB.
			 */
			add_filter(
				'wc_get_template',
				[
					'ET_Builder_Module_Woocommerce_Checkout_Payment_Info',
					'swap_template',
				],
				10,
				5
			);
		}

		ob_start();
		if ( is_et_pb_preview() ) {
			printf(
				'<div className="et_pb_wc_inactive__message">%s</div>',
				esc_html__( 'Woo Checkout Payment module can be used on a page and cannot be previewd.', 'et_builder' )
			);
		} else {
			WC_Shortcode_Checkout::output( array() );
		}
		$markup = ob_get_clean();

		if ( et_fb_is_computed_callback_ajax() || $is_tb || is_et_pb_preview() ) {
			remove_filter(
				'wc_get_template',
				[
					'ET_Builder_Module_Woocommerce_Checkout_Payment_Info',
					'swap_template',
				],
				10,
				5
			);
		}

		if ( ( $is_cart_empty && $is_pb_mode ) || is_et_pb_preview() ) {
			remove_filter(
				'woocommerce_get_cart_contents',
				array( $class, 'set_dummy_cart_contents' )
			);
		}

		self::maybe_reset_hooks();

		// Fallback.
		if ( ! is_string( $markup ) ) {
			$markup = '';
		}

		return $markup;
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
		ET_Builder_Module_Helper_Woocommerce_Modules::process_custom_button_icons( $render_slug, $this );
		// Module classname.
		$this->add_classname( $this->get_text_orientation_classname() );

		if ( $this->_module_has_background() ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .woocommerce-checkout #payment, %%order_class%% .woocommerce-order',
					'declaration' => 'background: transparent !important;',
				)
			);
		}

		$output = self::get_checkout_payment_info( $attrs );

		return $this->_render_module_wrapper( $output, $render_slug );
	}

	/**
	 * Checks if module has background.
	 *
	 * @since 4.15.0
	 *
	 * @return bool
	 */
	protected function _module_has_background() {
		$has_background_color      = ! empty( $this->props['background_color'] );
		$has_background_gradient   = isset( $this->props['use_background_color_gradient'] ) && 'on' === $this->props['use_background_color_gradient'];
		$has_background_image      = ! empty( $this->props['background_image'] );
		$has_background_video_mp4  = ! empty( $this->props['background_video_mp4'] );
		$has_background_video_webm = ! empty( $this->props['background_video_webm'] );
		$has_background_pattern    = isset( $this->props['background_enable_pattern_style'] ) && 'on' === $this->props['background_enable_pattern_style'] && ! empty( $this->props['background_pattern_style'] );
		$has_background_mask       = isset( $this->props['background_enable_pattern_style'] ) && 'on' === $this->props['background_enable_mask_style'] && ! empty( $this->props['background_mask_style'] );

		return $has_background_color
			|| $has_background_gradient
			|| $has_background_image
			|| $has_background_video_mp4
			|| $has_background_video_webm
			|| $has_background_pattern
			|| $has_background_mask;
	}
}

new ET_Builder_Module_Woocommerce_Checkout_Payment_Info();
