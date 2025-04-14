<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Checkout_Order_Details class
 *
 * The ET_Builder_Module_Woocommerce_Checkout_Order_Details Class is responsible for rendering the
 * Checkout order details section using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since 4.14.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing Woo Checkout Details component.
 */
class ET_Builder_Module_Woocommerce_Checkout_Order_Details extends ET_Builder_Module {
	/**
	 * Initialize.
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Checkout Details', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Checkout Details', 'et_builder' );
		$this->slug        = 'et_pb_wc_checkout_order_details';
		$this->vb_support  = 'on';
		$this->folder_name = 'et_pb_woo_modules';

		$this->settings_modal_toggles = array(
			'advanced' => array(
				'toggles' => array(
					'title'        => array(
						'title'    => esc_html__( 'Title Text', 'et_builder' ),
						'priority' => 55,
					),
					'column_label' => array(
						'title'    => esc_html__( 'Column Label', 'et_builder' ),
						'priority' => 60,
					),
					'body'         => array(
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
						'priority'          => 65,
					),
					'table'        => array(
						'title'    => esc_html__( 'Table', 'et_builder' ),
						'priority' => 70,
					),
					'table_row'    => array(
						'title'    => esc_html__( 'Table Row', 'et_builder' ),
						'priority' => 75,
					),
					'table_cell'   => array(
						'title'    => esc_html__( 'Table Cell', 'et_builder' ),
						'priority' => 80,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'title'        => array(
					'label'       => esc_html__( 'Title', 'et_builder' ),
					'css'         => array(
						'main' => '%%order_class%% #order_review_heading',
					),
					'font_size'   => array(
						'default' => '22px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'column_label' => array(
					'label'       => esc_html__( 'Column Label', 'et_builder' ),
					'css'         => array(
						'main' => '%%order_class%% table.shop_table thead th',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
				),
				'body'         => array(
					'label'       => esc_html__( 'Body', 'et_builder' ),
					'css'         => array(

						// Accepts only string and not array. Hence using `implode`.
						'main'        => implode(
							', ',
							array(
								'%%order_class%% td',
								'%%order_class%% tfoot th',
							)
						),

						// Accepts only string and not array. Hence using `implode`.
						'line_height' => implode(
							', ',
							array(
								'%%order_class%% table.shop_table th',
								'%%order_class%% table.shop_table td',
							)
						),
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
					'toggle_slug' => 'body',
					'sub_toggle'  => 'p',
				),
				'link'         => array(
					'label'       => esc_html__( 'Link', 'et_builder' ),
					'css'         => array(
						'main'        => '%%order_class%% td a',
						'line_height' => '%%order_class%% td a',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
					'toggle_slug' => 'body',
					'sub_toggle'  => 'a',
				),
			),
			'text'           => array(
				'css' => array(
					'text_orientation' => '%%order_class%% h3, table.shop_table th, table.shop_table tr td',
					// Refer ET_Builder_Module_Field_TextShadow::update_styles for selector
					// definition.
					'text_shadow'      => '%%order_class%% h3, table.shop_table th, table.shop_table tr td',
				),
			),
			'link_options'   => false,
			'form_field'     => array(
				'table'      => array(
					'label'                  => esc_html__( 'Table', 'et_builder' ),
					'css'                    => array(
						'main' => '%%order_class%% table.shop_table',
					),
					'background_color'       => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s table.', 'et_builder' ),
					),
					'font_field'             => false,
					'margin_padding'         => array(
						'css'             => array(
							'main'      => '%%order_class%% table.shop_table',
							'important' => array( 'custom_margin' ),
						),
						'depends_on'      => array(
							'collapse_table_gutters_borders',
						),
						'depends_show_if' => 'off',
					),
					'text_color'             => false,
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'border_styles'          => array(
						'table' => array(
							'label_prefix'      => esc_html__( 'Table', 'et_builder' ),
							'css'               => array(
								'main' => array(
									'border_styles' => '%%order_class%% table.shop_table',
									'border_radii'  => '%%order_class%% table.shop_table',
								),
							),
							'use_focus_borders' => false,
							'defaults'          => array(
								'border_radii'  => 'on|5px|5px|5px|5px',
								'border_styles' => array(
									'width' => '1px',
								),
							),
							'depends_on'        => array(
								'collapse_table_gutters_borders',
							),
							'depends_show_if'   => 'off',
						),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => '%%order_class%% table.shop_table',
						),
					),
				),
				'table_row'  => array(
					'label'                  => esc_html__( 'Table Row', 'et_builder' ),
					'css'                    => array(
						'main' => '%%order_class%% table.shop_table tr',
					),
					'background_color'       => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s table row.', 'et_builder' ),
					),
					'font_field'             => false,
					'margin_padding'         => array(
						'css'         => array(
							'main' => '%%order_class%% table.shop_table tr th, %%order_class%% table.shop_table tr td',
						),
						'use_margin'  => false,
						'use_padding' => false,
					),
					'text_color'             => false,
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'border_styles'          => array(
						'table_row' => array(
							'label_prefix'      => esc_html__( 'Table Row', 'et_builder' ),
							'css'               => array(
								'main'      => array(

									// Accepts only string and not array. Hence using `implode`.
									'border_radii'  => implode(
										', ',
										array(
											'%%order_class%% table.shop_table th',
											'%%order_class%% table.shop_table td',
										)
									),
									'border_styles' => implode(
										', ',
										array(
											'%%order_class%% table.shop_table th',
											'%%order_class%% table.shop_table td',
										)
									),
								),
								'important' => true,
							),
							'use_focus_borders' => false,
							'defaults'          => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '1px',
								),
							),
							'depends_on'        => array(
								'collapse_table_gutters_borders',
							),
							'depends_show_if'   => 'on',
							'use_radius'        => false,
						),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => '%%order_class%% table.shop_table tr',
						),
					),
				),
				'table_cell' => array(
					'label'                  => esc_html__( 'Table Cell', 'et_builder' ),
					'css'                    => array(
						'main' => '%%order_class%% table.shop_table tr th, %%order_class%% table.shop_table tr td',
					),
					'background_color'       => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s table cell.', 'et_builder' ),
					),
					'font_field'             => false,
					'margin_padding'         => array(
						'css'        => array(
							'main' => implode(
								', ',
								array(
									'%%order_class%% table.shop_table tr th',
									'%%order_class%% table.shop_table tr td',
								)
							),
						),
						'use_margin' => false,
					),
					'text_color'             => false,
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'border_styles'          => array(
						'table_cell' => array(
							'label_prefix'      => esc_html__( 'Table Cell', 'et_builder' ),
							'css'               => array(
								'main'      => array(
									'border_styles' => '%%order_class%% table.shop_table tr th,%%order_class%% table.shop_table tr td',
									'border_radii'  => '%%order_class%% table.shop_table tr th, %%order_class%% table.shop_table tr td',
								),
								'important' => array( 'border-color' ),
							),
							'use_focus_borders' => false,
							'defaults'          => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '0px',
									'style' => 'solid',
								),
								'composite'     => array(
									'border_top' => array(
										'border_width_top' => '1px',
										'border_style_top' => 'solid',
										'border_color_top' => '#eeeeee',
									),
								),
							),
							'depends_on'        => array(
								'collapse_table_gutters_borders',
							),
							'depends_show_if'   => 'off',
						),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => '%%order_class%% table.shop_table tr th, %%order_class%% table.shop_table td',
						),
					),
				),
			),

			// Use !important in Spacing OG — Margin values.
			'margin_padding' => array(
				'css' => array(
					'important' => array( 'custom_margin' ),
				),
			),
		);

		$this->custom_css_fields = array(
			'title_text' => array(
				'label'    => esc_html__( 'Title Text', 'et_builder' ),
				'selector' => '%%order_class%% h1, %%order_class%% h2, %%order_class%% h3, %%order_class%% h4, %%order_class%% h5, %%order_class%% h6',
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
			'__checkout_order_details'       => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Checkout_Order_Details',
					// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- This is a function call.
					'get_checkout_order_details'
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
			'collapse_table_gutters_borders' => ET_Builder_Module_Helper_Woocommerce_Modules::get_field( 'collapse_table_gutters_borders' ),
			'vertical_gutter_width'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_field( 'vertical_gutter_width' ),
			'horizontal_gutter_width'        => ET_Builder_Module_Helper_Woocommerce_Modules::get_field( 'horizontal_gutter_width' ),
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
	public static function swap_template_fe( $template, $template_name, $args, $template_path, $default_path ) {
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
				'checkout/review-order.php',
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
	 *
	 * @param array $conditional_tags Array of conditional tags.
	 */
	public static function maybe_reset_hooks( $conditional_tags ) {
		$is_tb = et_()->array_get( $conditional_tags, 'is_tb', false );

		ET_Builder_Module_Helper_Woocommerce_Modules::attach_wc_checkout_coupon_form();
		ET_Builder_Module_Helper_Woocommerce_Modules::attach_wc_checkout_login_form();
		ET_Builder_Module_Helper_Woocommerce_Modules::attach_wc_checkout_billing();
		ET_Builder_Module_Helper_Woocommerce_Modules::attach_wc_checkout_shipping();
		ET_Builder_Module_Helper_Woocommerce_Modules::attach_wc_checkout_payment();

		if ( et_fb_is_computed_callback_ajax() || $is_tb ) {
			remove_filter(
				'wc_get_template',
				[
					'ET_Builder_Module_Woocommerce_Checkout_Order_Details',
					'swap_template',
				],
				10,
				5
			);
		}

		if ( ! et_fb_is_computed_callback_ajax() && ! $is_tb ) {
			remove_filter(
				'wc_get_template',
				[
					'ET_Builder_Module_Woocommerce_Checkout_Order_Details',
					'swap_template_fe',
				],
				10,
				5
			);
		}
	}

	/**
	 * Handle hooks.
	 *
	 * @param array $conditional_tags Array of conditional tags.
	 */
	public static function maybe_handle_hooks( $conditional_tags ) {
		$is_tb = et_()->array_get( $conditional_tags, 'is_tb', false );

		ET_Builder_Module_Helper_Woocommerce_Modules::detach_wc_checkout_coupon_form();
		ET_Builder_Module_Helper_Woocommerce_Modules::detach_wc_checkout_login_form();
		ET_Builder_Module_Helper_Woocommerce_Modules::detach_wc_checkout_billing();
		ET_Builder_Module_Helper_Woocommerce_Modules::detach_wc_checkout_shipping();
		ET_Builder_Module_Helper_Woocommerce_Modules::detach_wc_checkout_payment();

		if ( et_fb_is_computed_callback_ajax() || $is_tb ) {
			add_filter(
				'wc_get_template',
				[
					'ET_Builder_Module_Woocommerce_Checkout_Order_Details',
					'swap_template',
				],
				10,
				5
			);
		}

		if ( ! et_fb_is_computed_callback_ajax() && ! $is_tb ) {
			add_filter(
				'wc_get_template',
				[
					'ET_Builder_Module_Woocommerce_Checkout_Order_Details',
					'swap_template_fe',
				],
				10,
				5
			);
		}
	}

	/**
	 * Gets the Checkout Order Details markup.
	 *
	 * @param array $args Array of `depends_on` key/value pairs.
	 * @param array $conditional_tags Array of conditional tags.
	 *
	 * @return string
	 */
	public static function get_checkout_order_details( $args = array(), $conditional_tags = array() ) {
		if ( ! class_exists( 'WC_Shortcode_Checkout' )
			|| ! method_exists( 'WC_Shortcode_Checkout', 'output' ) ) {
			return '';
		}

		self::maybe_handle_hooks( $conditional_tags );

		$is_cart_empty = function_exists( 'WC' ) && isset( WC()->cart ) && WC()->cart->is_empty();
		$is_pb_mode    = et_fb_is_computed_callback_ajax() || is_et_pb_preview();
		$class         = 'ET_Builder_Module_Helper_Woocommerce_Modules';

		// Set dummy cart contents to output Billing when no product is in cart.
		if ( ( $is_cart_empty && $is_pb_mode ) || is_et_pb_preview() ) {
			add_filter(
				'woocommerce_get_cart_contents',
				// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- This is a function call.
				array( $class, 'set_dummy_cart_contents' )
			);
		}

		ob_start();

		WC_Shortcode_Checkout::output( array() );

		$markup = ob_get_clean();

		if ( ( $is_cart_empty && $is_pb_mode ) || is_et_pb_preview() ) {
			remove_filter(
				'woocommerce_get_cart_contents',
				// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- This is a function call.
				array( $class, 'set_dummy_cart_contents' )
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

		$this->add_classname( $this->get_text_orientation_classname() );

		$output = self::get_checkout_order_details();

		$collapse_table_gutters_borders_style_values = array();
		$table_border_spacing_style_values           = array();
		foreach ( et_pb_responsive_options()->get_modes() as $device ) {
			$collapse_table_gutters_borders = et_pb_responsive_options()->get_property_value(
				$attrs,
				'collapse_table_gutters_borders',
				'off',
				$device,
				true
			);

			$horizontal_gutter_width_value = et_pb_responsive_options()->get_property_value(
				$attrs,
				'horizontal_gutter_width',
				'0px',
				$device,
				true
			);
			$vertical_gutter_width_value   = et_pb_responsive_options()->get_property_value(
				$attrs,
				'vertical_gutter_width',
				'0px',
				$device,
				true
			);

			if ( 'on' === $collapse_table_gutters_borders ) {
				$collapse_table_gutters_borders_style_values[ $device ] = array(
					'border-collapse' => 'collapse',
				);

				$table_border_spacing_style_values[ $device ] = array(
					'border-spacing' => '0 0',
				);
			} else {
				$collapse_table_gutters_borders_style_values[ $device ] = array(
					'border-collapse' => 'separate',
				);

				$table_border_spacing_style_values[ $device ] = array(
					'border-spacing' => sprintf(
						'%s %s',
						$horizontal_gutter_width_value,
						$vertical_gutter_width_value
					),
				);
			}
		}

		et_pb_responsive_options()->generate_responsive_css(
			$collapse_table_gutters_borders_style_values,
			'%%order_class%% table.shop_table',
			'border-collapse',
			$render_slug,
			'',
			'border-collapse' /* Can be anything other than `range`. */
		);

		et_pb_responsive_options()->generate_responsive_css(
			$table_border_spacing_style_values,
			'%%order_class%% table.shop_table',
			'border-spacing',
			$render_slug,
			'',
			'border-spacing' /* Can be anything other than `range`. */
		);

		if ( isset( WC()->cart )
			&& ! is_null( WC()->cart && method_exists( WC()->cart, 'check_cart_items' ) )
			&& ! is_et_pb_preview() ) {
			$return = WC()->cart->check_cart_items();

			if ( wc_notice_count( 'error' ) > 0 ) {
				$this->add_classname( 'et_pb_hide_module' );
			}
		}

		global $wp;
		if ( ! empty( $wp->query_vars['order-pay'] ) ) {
			$this->add_classname( 'et_pb_wc_order_pay' );
		}

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Checkout_Order_Details();
