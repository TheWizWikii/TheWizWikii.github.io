<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Cart_Totals class
 *
 * The ET_Builder_Module_Woocommerce_Cart_Totals Class is responsible for rendering the
 * Cart Totals using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since 4.14.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Checkout order component.
 */
class ET_Builder_Module_Woocommerce_Cart_Totals extends ET_Builder_Module {

	/**
	 * Initialize.
	 *
	 * @since 4.14.0 Fixed PHP Warnings {@link https://github.com/elegantthemes/Divi/issues/22104}
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Cart Totals', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Cart Totals', 'et_builder' );
		$this->slug        = 'et_pb_wc_cart_totals';
		$this->vb_support  = 'on';
		$this->folder_name = 'et_pb_woo_modules';

		$this->main_css_element       = '%%order_class%%.et_pb_wc_cart_totals';
		$this->settings_modal_toggles = array(
			'advanced' => array(
				'toggles' => array(
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
						'priority'          => 53,
					),
					'title'        => array(
						'title'    => esc_html__( 'Title Text', 'et_builder' ),
						'priority' => 51,
					),
					'column_label' => array(
						'title'    => esc_html__( 'Column Label', 'et_builder' ),
						'priority' => 52,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'        => array(
				'title'        => array(
					'label'       => esc_html__( 'Title', 'et_builder' ),
					'css'         => array(
						'main' => '%%order_class%% h2',
					),
					'font_size'   => array(
						'default' => '26px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'column_label' => array(
					'label'       => esc_html__( 'Column Label', 'et_builder' ),
					'css'         => array(
						'main' => implode(
							',',
							[
								'%%order_class%% table.shop_table tbody th',
								'%%order_class%% table.shop_table_responsive tbody td:before',
							]
						),
					),
					'font'        => array(
						'default' => '|700|||||||',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
					'toggle_slug' => 'column_label',
				),
				'body'         => array(
					'label'       => esc_html__( 'Body', 'et_builder' ),
					'css'         => array(
						'main'        => implode(
							',',
							array(
								'%%order_class%% .woocommerce-Price-amount',
								'%%order_class%% .woocommerce-shipping-totals label',
								'%%order_class%% .woocommerce-shipping-totals .woocommerce-shipping-destination',
								'%%order_class%% table.shop_table a',
								'%%order_class%% table.shop_table tr.shipping td',
							)
						),
						'line_height' => '%%order_class%% table.shop_table td',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'body',
					'sub_toggle'  => 'p',
				),
				'link'         => array(
					'label'       => esc_html__( 'Link', 'et_builder' ),
					'css'         => array(
						'main'        => '%%order_class%% table.shop_table a',
						'line_height' => '%%order_class%% table.shop_table td',
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
			'link_options' => false,
			'button'       => array(
				'button' => array(
					'label'          => esc_html__( 'Button', 'et_builder' ),
					'css'            => array(
						'main' => implode(
							',',
							array(
								'%%order_class%% a.checkout-button',
								'%%order_class%% button.button',
							)
						),
					),
					'use_alignment'  => false,
					'box_shadow'     => array(
						'css' => array(
							'main' => implode(
								',',
								array(
									'%%order_class%% a.checkout-button',
									'%%order_class%% button.button',
								)
							),
						),
					),
					'margin_padding' => array(
						'css'            => array(
							'main'      => implode(
								',',
								array(
									'%%order_class%% a.checkout-button.button',
									'%%order_class%% button.button',
								)
							),
							'important' => array( 'custom_padding' ),
						),
						'custom_padding' => array(
							'default' => '0.3em|1em|0.3em|1em|false|false',
						),
					),
				),
			),
			'form_field'   => array(
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
							'label_prefix'    => 'Table',
							'css'             => array(
								'main' => array(
									'border_styles' => '%%order_class%% table.shop_table',
									'border_radii'  => '%%order_class%% table.shop_table',
								),
							),
							'defaults'        => array(
								'border_radii'  => 'on|5px|5px|5px|5px',
								'border_styles' => array(
									'width' => '1px',
									'style' => 'solid',
									'color' => '#eeeeee',
								),
							),
							'depends_on'      => array(
								'collapse_table_gutters_borders',
							),
							'depends_show_if' => 'off',
						),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => '%%order_class%% table.shop_table',
						),
					),
					'toggle_priority'        => 55,
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
						'use_padding' => false,
						'use_margin'  => false,
					),
					'text_color'             => false,
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'border_styles'          => array(
						'table_row' => array(
							'label_prefix'    => 'Table Row',
							'css'             => array(
								'main' => array(
									'border_styles' => '%%order_class%% table.shop_table tbody th, %%order_class%% table.shop_table td',
									'border_radii'  => '%%order_class%% table.shop_table tr td',
								),
							),
							'defaults'        => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '1px',
								),
							),
							'depends_on'      => array(
								'collapse_table_gutters_borders',
							),
							'depends_show_if' => 'on',
							'use_radius'      => false,
						),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => '%%order_class%% table.shop_table tr',
						),
					),
					'toggle_priority'        => 60,
				),
				'table_cell' => array(
					'label'                  => esc_html__( 'Table Cell', 'et_builder' ),
					'css'                    => array(
						'main'                   => '%%order_class%% table.shop_table tr th, %%order_class%% table.shop_table tr td',
						'important'              => [ 'background_color' ],
						'background_color_hover' => implode(
							',',
							[
								'%%order_class%% table.shop_table tr th:hover',
								'%%order_class%% table.shop_table tr td:hover',
							]
						),
					),
					'background_color'       => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s table cell.', 'et_builder' ),
					),
					'font_field'             => false,
					'margin_padding'         => array(
						'css'        => array(
							'main' => implode(
								',',
								array(
									'%%order_class%% table.shop_table tr th',
									'%%order_class%% table.shop_table tr td',
									'%%order_class%% table.cart tr td',
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
							'label_prefix'      => 'Table Cell',
							'css'               => array(
								'main' => array(
									'border_styles' => '%%order_class%% table.shop_table tr th,%%order_class%% table.shop_table tr td',
									'border_radii'  => '%%order_class%% table.shop_table tr th, %%order_class%% table.shop_table tr td',
								),
							),
							'defaults'          => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '0px',
									'style' => 'solid',
								),
								'composite'     => array(
									'border_top' => array(
										'border_width_top' => '1px',
									),
								),
							),
							'use_focus_borders' => false,
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
					'toggle_priority'        => 65,
				),
				'form_field' => array(
					'label'            => esc_html__( 'Fields', 'et_builder' ),
					'css'              => array(
						'main'                   => implode(
							',',
							array(
								'%%order_class%% .select2-container--default .select2-selection--single',
								'%%order_class%% form .form-row input.input-text',
								'%%order_class%% form .form-row textarea',
							)
						),
						'focus_background_color' => implode(
							',',
							array(
								'%%order_class%% .select2-selection--single:focus',
								'%%order_class%% form .form-row input.input-text:focus',
								'%%order_class%% form .form-row textarea:focus',
							)
						),
						'focus_text_color'       => implode(
							',',
							array(
								'%%order_class%% .select2-selection--single:focus .select2-selection__rendered',
								'%%order_class%% form .form-row input.input-text:focus',
								'%%order_class%% form .form-row textarea:focus',
							)
						),
						'form_text_color'        => implode(
							',',
							array(
								'%%order_class%% .select2-container--default .select2-selection--single .select2-selection__rendered',
								'%%order_class%% form .form-row input.input-text',
								'%%order_class%% form .form-row textarea',
							)
						),
					),
					'background_color' => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s fields.', 'et_builder' ),
					),
					'box_shadow'       => array(
						'css' => array(
							'main' => implode(
								',',
								array(
									'%%order_class%% .select2-container--default .select2-selection--single',
									'%%order_class%% form .form-row input.input-text',
									'%%order_class%% form .form-row textarea',
								)
							),
						),
					),
					'border_styles'    => array(
						'form_field'       => array(
							'label_prefix' => esc_html__( 'Fields', 'et_builder' ),
							'css'          => array(
								'main' => array(
									'border_styles' => implode(
										',',
										array(
											'%%order_class%% .select2-container--default .select2-selection--single',
											'%%order_class%% form .form-row input.input-text',
											'%%order_class%% form .form-row textarea',
										)
									),
									'border_radii'  => implode(
										',',
										array(
											'%%order_class%% .select2-container--default .select2-selection--single',
											'%%order_class%% form .form-row input.input-text',
											'%%order_class%% form .form-row textarea',
										)
									),
								),
							),
							'defaults'     => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '0px',
									'style' => 'solid',
								),
							),
						),
						'form_field_focus' => array(
							'label_prefix' => esc_html__( 'Fields Focus', 'et_builder' ),
							'css'          => array(
								'main' => array(
									'border_styles' => implode(
										',',
										array(
											'%%order_class%% .select2-container--default.select2-container--open .select2-selection--single',
											'%%order_class%% form .form-row input.input-text:focus',
											'%%order_class%% form .form-row textarea:focus',
										)
									),
									'border_radii'  => implode(
										',',
										array(
											'%%order_class%% .select2-container--default.select2-container--open .select2-selection--single',
											'%%order_class%% form .form-row input.input-text:focus',
											'%%order_class%% form .form-row textarea:focus',
										)
									),
								),
							),
							'defaults'     => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '0px',
									'style' => 'solid',
								),
							),
						),
					),
					'font_field'       => array(
						'css'         => array(
							'main'      => implode(
								', ',
								array(
									'%%order_class%% .select2-container--default .select2-selection--single .select2-selection__rendered',
									'%%order_class%% form .form-row input.input-text',
									'%%order_class%% form .form-row textarea',
								)
							),
							'important' => array( 'size', 'font' ),
						),
						'font_size'   => array(
							'default' => '14px',
						),
						'line_height' => array(
							'default' => '1.7em',
						),
					),
					'margin_padding'   => array(
						'css' => array(
							'main'    => implode(
								', ',
								array(
									'%%order_class%% .select2-container--default .select2-selection--single .select2-selection__rendered',
									'%%order_class%% form .form-row input.input-text',
									'%%order_class%% form .form-row textarea',
								)
							),
							'padding' => implode(
								', ',
								array(
									'%%order_class%% .select2-container--default .select2-selection--single',
									'%%order_class%% form .form-row input.input-text',
									'%%order_class%% form .form-row textarea',
								)
							),
							'margin'  => implode(
								', ',
								array(
									'%%order_class%% .select2-container--default .select2-selection--single',
									'%%order_class%% form .form-row input.input-text',
									'%%order_class%% form .form-row textarea',
								)
							),
						),
					),
					'width'            => array(),
					'toggle_priority'  => 70,
				),
			),
		);

		$this->custom_css_fields = array(
			'title_text'   => array(
				'label'    => esc_html__( 'Title Text', 'et_builder' ),
				'selector' => '%%order_class%% h2',
			),
			'column_label' => array(
				'label'    => esc_html__( 'Column Label', 'et_builder' ),
				'selector' => '%%order_class%% table.shop_table tbody th',
			),
			'body'         => array(
				'label'    => esc_html__( 'Body', 'et_builder' ),
				'selector' => '%%order_class%% tr.cart-subtotal .woocommerce-Price-amount, %%order_class%% tr.cart-discount .woocommerce-Price-amount, tr.woocommerce-shipping-totals label',
			),
			'link'         => array(
				'label'    => esc_html__( 'Link', 'et_builder' ),
				'selector' => '%%order_class%% table.shop_table a',
			),
			'button'       => array(
				'label'    => esc_html__( 'Checkout Button', 'et_builder' ),
				'selector' => '%%order_class%% a.checkout-button',
			),
			'table'        => array(
				'label'    => esc_html__( 'Cart Totals Table', 'et_builder' ),
				'selector' => '%%order_class%% table.shop_table',
			),
			'table_row'    => array(
				'label'    => esc_html__( 'Cart Totals Table Row', 'et_builder' ),
				'selector' => '%%order_class%% table.shop_table tr',
			),
			'table_cell'   => array(
				'label'    => esc_html__( 'Cart Totals Table Cell', 'et_builder' ),
				'selector' => '%%order_class%% table.shop_table tr th, %%order_class%% table.shop_table tr td',
			),
			'form_field'   => array(
				'label'    => esc_html__( 'Fields', 'et_builder' ),
				'selector' => '%%order_class%% .quantity input.qty',
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
			'__cart_totals'                  => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Cart_Totals',
					'get_cart_totals',
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
			'placeholder_color'              => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'placeholder_color'
			),
		);

		return $fields;
	}

	/**
	 * Sets the Button's data-* attrs for the Icons to render.
	 *
	 * These attributes are set on the outer wrapper & will be set on the Button elements using JS.
	 *
	 * @param array $outer_wrapper_attrs Outer wrapper attributes.
	 *
	 * @return array
	 */
	public function add_custom_icon_attrs( $outer_wrapper_attrs ) {
		$checkout_btn_attrs = ET_Builder_Module_Helper_Woocommerce_Modules::add_custom_icon_attrs( $this->props );

		$update_btn_attrs = ET_Builder_Module_Helper_Woocommerce_Modules::add_custom_icon_attrs( $this->props );

		if ( ! is_array( $checkout_btn_attrs ) || empty( $checkout_btn_attrs )
			&& ! is_array( $update_btn_attrs ) || empty( $update_btn_attrs ) ) {
			return $outer_wrapper_attrs;
		}

		$attrs = array_merge( $outer_wrapper_attrs, $checkout_btn_attrs );
		$attrs = array_merge( $attrs, $update_btn_attrs );

		return $attrs;
	}

	/**
	 * Swaps Cart Totals template.
	 *
	 * By default WooCommerce displays Shipping calculator only for eligibleCart items.
	 * However, Shipping Calculator must be shown in VB. Hence we swap the template.
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
				'cart/cart-totals.php',
			),
			true
		);

		if ( $is_template_override ) {
			return trailingslashit( ET_BUILDER_DIR ) . 'feature/woocommerce/templates/' . $template_name;
		}

		return $template;
	}

	/**
	 * Show dummy subtotal.
	 *
	 * @param string $value Value.
	 *
	 * @return string
	 */
	public static function show_dummy_subtotal( $value ) {
		if ( ! function_exists( 'wc_price' ) ) {
			return $value;
		}

		return wc_price( '187.00' );
	}

	/**
	 * Show dummy total.
	 *
	 * @param string $value Value.
	 *
	 * @return string
	 */
	public static function show_dummy_total( $value ) {
		if ( ! function_exists( 'wc_price' ) ) {
			return $value;
		}

		return sprintf( '<strong>%s</strong>', wc_price( '187.00' ) );
	}

	/**
	 * Displays message before shipping calculator in VB and in TB.
	 */
	public static function display_message_before_shipping_calculator() {
		$message = apply_filters(
			'woocommerce_shipping_may_be_available_html',
			__( 'Enter your address to view shipping options.', 'woocommerce' )
		);
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped in the previous line.
		echo $message;
	}

	/**
	 * Handle hooks.
	 *
	 * @param array $conditional_tags Conditional tags from AJAX callback.
	 */
	public static function maybe_handle_hooks( $conditional_tags ) {
		$is_tb              = et_()->array_get( $conditional_tags, 'is_tb', false );
		$is_use_placeholder = $is_tb || is_et_pb_preview();

		if ( et_fb_is_computed_callback_ajax() || $is_use_placeholder ) {
			$class = 'ET_Builder_Module_Woocommerce_Cart_Totals';

			add_filter(
				'woocommerce_cart_subtotal',
				// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- Call to a function.
				array( $class, 'show_dummy_subtotal' )
			);

			add_filter(
				'woocommerce_cart_totals_order_total_html',
				// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- Call to a function.
				array( $class, 'show_dummy_total' )
			);

			add_action(
				'woocommerce_before_shipping_calculator',
				[
					$class,
					'display_message_before_shipping_calculator',
				]
			);

			if ( is_et_pb_preview() || WC()->cart->is_empty() ) {
				add_filter(
					'wc_get_template',
					[
						$class,
						'swap_template',
					],
					10,
					5
				);
			}
		}
	}

	/**
	 * Reset hooks.
	 *
	 * @param array $conditional_tags Conditional tags from AJAX callback.
	 */
	public static function maybe_reset_hooks( $conditional_tags ) {
		$is_tb              = et_()->array_get( $conditional_tags, 'is_tb', false );
		$is_use_placeholder = $is_tb || is_et_pb_preview();

		if ( et_fb_is_computed_callback_ajax() || $is_use_placeholder ) {
			$class = 'ET_Builder_Module_Woocommerce_Cart_Totals';

			remove_filter(
				'woocommerce_cart_totals_order_total_html',
				// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- Call to a function.
				array( $class, 'show_dummy_subtotal' )
			);

			remove_filter(
				'woocommerce_cart_totals_order_total_html',
				// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- Call to a function.
				array( $class, 'show_dummy_total' )
			);

			remove_action(
				'woocommerce_before_shipping_calculator',
				[
					$class,
					'display_message_before_shipping_calculator',
				]
			);

			if ( is_et_pb_preview() || WC()->cart->is_empty() ) {
				remove_filter(
					'wc_get_template',
					[
						$class,
						'swap_template',
					]
				);
			}
		}
	}

	/**
	 * Gets Cart totals markup.
	 *
	 * @param array $args             List of attributes.
	 * @param array $conditional_tags Conditional tags from AJAX callback.
	 *
	 * @return string
	 */
	public static function get_cart_totals( $args = array(), $conditional_tags = array() ) {
		if ( ! function_exists( 'woocommerce_cart_totals' ) ) {
			return '';
		}

		// Show nothing when the Cart is empty in FE.
		if ( ! et_fb_is_computed_callback_ajax() && ! is_et_pb_preview() && ( is_null( WC()->cart ) || WC()->cart->is_empty() ) ) {
			return '';
		}

		self::maybe_handle_hooks( $conditional_tags );

		ob_start();
		if ( ! et_fb_is_enabled() && ! is_et_pb_preview() && ! is_null( WC()->cart ) ) {
			wc_maybe_define_constant( 'WOOCOMMERCE_CART', true );
			WC()->cart->calculate_totals();
		}
		woocommerce_cart_totals();
		$markup = ob_get_clean();

		self::maybe_reset_hooks( $conditional_tags );

		// Fallback.
		if ( ! is_string( $markup ) ) {
			$markup = '';
		}

		return $markup;
	}

	/**
	 * Gets the dropdown arrow style rule sets.
	 *
	 * @param string $value         Shortcode attribute value.
	 * @param string $property      CSS property.
	 * @param string $prop_position CSS Property position.
	 *
	 * @return string|void
	 */
	public function get_dropdown_arrow_style( $value, $property = 'margin', $prop_position = 'top' ) {
		$values = explode( '|', $value );

		if ( empty( $values ) ) {
			return '';
		}

		$values    = array_map( 'trim', $values );
		$positions = array(
			'top',
			'right',
			'bottom',
			'left',
		);

		foreach ( $positions as $i => $position ) {
			if ( $position !== $prop_position ) {
				continue;
			}

			if ( ! isset( $values[ $i ] ) || '' === $values[ $i ] ) {
				continue;
			}

			$processed_value = et_builder_process_range_value( $values[ $i ], $property );

			return $processed_value;
		}
	}

	/**
	 * Adds custom width styles.
	 *
	 * @param string $render_slug    Module slug.
	 * @param array  $attrs          Module props.
	 * @param string $selector       The selector.
	 * @param string $hover_selector Hover selector.
	 * @param string $prop           Shortcode property used in computing styles.
	 */
	public function add_dropdown_arrow_style( $render_slug, $attrs, $selector, $hover_selector, $prop ) {
		if ( 'form_field_custom_margin' !== $prop ) {
			return;
		}

		// 01. Get custom margin values. Note that the values are '|' (pipe) separated.
		$form_field_custom_margin        = et_pb_responsive_options()->get_single_value(
			$attrs,
			$prop
		);
		$form_field_custom_margin_tablet = et_pb_responsive_options()->get_single_value(
			$attrs,
			$prop,
			'',
			'tablet'
		);
		$form_field_custom_margin_phone  = et_pb_responsive_options()->get_single_value(
			$attrs,
			$prop,
			'',
			'phone'
		);

		/*
		 * 02. Since Select 2 (library that WooCommmerce uses) styles have
		 * margin top and margin left values set, Divi Builder has to accommodate those.
		 */
		$form_field_custom_margin_right        = $this->get_dropdown_arrow_style(
			$form_field_custom_margin,
			'margin',
			'right'
		);
		$form_field_custom_margin_tablet_right = $this->get_dropdown_arrow_style(
			$form_field_custom_margin_tablet,
			'margin',
			'left'
		);
		$form_field_custom_margin_phone_right  = $this->get_dropdown_arrow_style(
			$form_field_custom_margin_phone,
			'margin',
			'left'
		);

		/*
		 * 03. Use Responsive Options to declare CSS and NOT generate.
		 *
		 * i.e USE et_pb_responsive_options()->declare_responsive_css()
		 * and NOT et_pb_responsive_options()->generate_responsive_css()
		 *
		 * When generate is used the custom computation cannot be done.
		 */
		$form_field_custom_margin_right        = empty( $form_field_custom_margin_right )
			? '0px'
			: $form_field_custom_margin_right;
		$form_field_custom_margin_tablet_right = empty( $form_field_custom_margin_tablet_right )
			? '0px'
			: $form_field_custom_margin_tablet_right;
		$form_field_custom_margin_phone_right  = empty( $form_field_custom_margin_phone_right )
			? '0px'
			: $form_field_custom_margin_phone_right;

		$form_field_custom_margin_last_edited       = $this->props[ "{$prop}_last_edited" ];
		$form_field_custom_margin_responsive_active = et_pb_get_responsive_status( $form_field_custom_margin_last_edited );

		// 04. Prep values for desktop, tablet, and phone.
		$form_field_custom_margin_right_values = array(
			'desktop' => sprintf(
				// No Space between minus and 1$s to avoid `Invalid property value` error.
				'margin-left: calc( -%1$s - 4px );',
				esc_html( $form_field_custom_margin_right )
			),
			'tablet'  => $form_field_custom_margin_responsive_active
				? sprintf(
					// No Space between minus and 1$s to avoid `Invalid property value` error.
					'margin-left: calc( -%1$s - 4px );',
					esc_html( $form_field_custom_margin_tablet_right )
				)
				: '',
			'phone'   => $form_field_custom_margin_responsive_active
				? sprintf(
					// No Space between minus and 1$s to avoid `Invalid property value` error.
					'margin-left: calc( -%1$s - 4px );',
					esc_html( $form_field_custom_margin_phone_right )
				)
				: '',
		);

		// 05. Generate style for desktop, tablet, and phone.
		et_pb_responsive_options()->declare_responsive_css(
			$form_field_custom_margin_right_values,
			$selector,
			$render_slug
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['placeholder_color'] = array(
			'color' => array(
				'%%order_class%% form .form-row input.input-text::placeholder',
				'%%order_class%% form .form-row input.input-text::-webkit-input-placeholder',
				'%%order_class%% form .form-row input.input-text::-moz-placeholder',
				'%%order_class%% form .form-row input.input-text:-ms-input-placeholder',
				'%%order_class%% form .form-row textarea::placeholder',
				'%%order_class%% form .form-row textarea::-webkit-input-placeholder',
				'%%order_class%% form .form-row textarea::-moz-placeholder',
				'%%order_class%% form .form-row textarea:-ms-input-placeholder',
			),
		);

		return $fields;
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
		$this->add_classname( 'woocommerce-cart' );
		$this->add_classname( $this->get_text_orientation_classname() );

		if ( ! is_et_pb_preview() && function_exists( 'WC' ) && isset( WC()->cart ) && WC()->cart->is_empty() ) {
			$this->add_classname( 'et_pb_wc_cart_empty' );
		}

		// Set `data-*` attrs for Button Icons to work.
		add_filter(
			"et_builder_module_{$render_slug}_outer_wrapper_attrs",
			array( $this, 'add_custom_icon_attrs' )
		);

		$this->add_classname( 'et_pb_woo_custom_button_icon' );

		$output = self::get_cart_totals();

		/*
		 * Add custom margin to the dropdown arrow.
		 */
		$this->add_dropdown_arrow_style(
			$render_slug,
			$this->props,
			'%%order_class%% .select2-container--default .select2-selection--single .select2-selection__arrow b',
			'',
			'form_field_custom_margin'
		);

		// Placeholder Color.
		$placeholder_selectors = array(
			'%%order_class%% form .form-row input.input-text::placeholder',
			'%%order_class%% form .form-row input.input-text::-webkit-input-placeholder',
			'%%order_class%% form .form-row input.input-text::-moz-placeholder',
			'%%order_class%% form .form-row input.input-text:-ms-input-placeholder',
			'%%order_class%% form .form-row textarea::placeholder',
			'%%order_class%% form .form-row textarea::-webkit-input-placeholder',
			'%%order_class%% form .form-row textarea::-moz-placeholder',
			'%%order_class%% form .form-row textarea:-ms-input-placeholder',
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

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Cart_Totals();
