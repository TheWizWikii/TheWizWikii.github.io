<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Cart_Products class
 *
 * The ET_Builder_Module_Woocommerce_Cart_Products Class is responsible for rendering the
 * Cart Products info using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since 4.14.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Cart Products component.
 */
class ET_Builder_Module_Woocommerce_Cart_Products extends ET_Builder_Module {

	/**
	 * Initialize.
	 *
	 * @since 4.14.0 Fixed PHP Warnings {@link https://github.com/elegantthemes/Divi/issues/22104}
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Cart Products', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Cart Products', 'et_builder' );
		$this->slug        = 'et_pb_wc_cart_products';
		$this->vb_support  = 'on';
		$this->folder_name = 'et_pb_woo_modules';

		$this->main_css_element       = '%%order_class%%.et_pb_wc_cart_products';
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements' => esc_html__( 'Elements', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'table_header' => array(
						'title'    => esc_html__( 'Table Header', 'et_builder' ),
						'priority' => 50,
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
						'priority'          => 55,
					),
					'table_row'    => array(
						'title'    => esc_html__( 'Table Row', 'et_builder' ),
						'priority' => 60,
					),
					'table_cell'   => array(
						'title'    => esc_html__( 'Table Cell', 'et_builder' ),
						'priority' => 65,
					),
					'icon'         => array(
						'title'    => esc_html__( 'Remove Icon', 'et_builder' ),
						'priority' => 70,
					),
					'image'        => array(
						'title'    => esc_html__( 'Image', 'et_builder' ),
						'priority' => 75,
					),
					'form_field'   => array(
						'title'    => esc_html__( 'Fields', 'et_builder' ),
						'priority' => 80,
					),
					'width'        => array(
						'title'    => esc_html__( 'Sizing', 'et_builder' ),
						'priority' => 85,
					),
					'layout'       => array(
						'title'    => esc_html__( 'Layout', 'et_builder' ),
						'priority' => 45,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'body'         => array(
					'label'       => esc_html__( 'Body', 'et_builder' ),
					'css'         => array(
						'main'        => array(
							'%%order_class%% tbody td',
							'%%order_class%% ul.products li.product .woocommerce-loop-product__title',
							'%%order_class%% ul.products li.product .price',
						),
						'color'       => implode(
							', ',
							array(
								'%%order_class%% tbody td a',
								'%%order_class%% tbody td',
								'%%order_class%% ul.products h1',
								'%%order_class%% ul.products h2',
								'%%order_class%% ul.products h3',
								'%%order_class%% ul.products h4',
								'%%order_class%% ul.products h5',
								'%%order_class%% ul.products h6',
								'%%order_class%% ul.products li.product .price',
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
						'main'        => '%%order_class%% td.product-name a',
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
				'table_header' => array(
					'label'       => esc_html__( 'Table Header', 'et_builder' ),
					'css'         => array(
						'main' => implode(
							',',
							array(
								'%%order_class%% table.cart th',
								'%%order_class%%.et_pb_row_layout_vertical table.shop_table_responsive tr td::before',
								'%%order_class%%.et_pb_row_layout_default table.shop_table_responsive tr td::before',
							)
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
					'toggle_slug' => 'table_header',
				),
			),
			'background'     => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'borders'        => array(
				'default' => array(),
				'image'   => array(
					'css'                 => array(
						'main' => array(
							'border_radii'  => '%%order_class%% table.cart img',
							'border_styles' => '%%order_class%% table.cart img',
						),
					),
					'label_prefix'        => esc_html__( 'Image', 'et_builder' ),
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'image',
					'depends_show_if_not' => 'vertical',
					'depends_on'          => array(
						'row_layout',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'text'           => array(
				'use_background_layout' => false,
				'use_text_orientation'  => true,
				'css'                   => array(
					'text_shadow' => '%%order_class%%',
				),
				'options'               => array(
					'background_layout' => array(
						'default_on_front' => 'light',
						'hover'            => 'tabs',
					),
				),
			),
			'text_shadow'    => array(
				// Add Text Shadow to Text OG.
				'default' => array(),
			),
			'link_options'   => false,
			'filters'        => array(
				'css'                  => array(
					'main' => '%%order_class%%',
				),
				'child_filters_target' => array(
					'tab_slug'            => 'advanced',
					'toggle_slug'         => 'image',
					'depends_show_if_not' => 'vertical',
					'depends_on'          => array(
						'row_layout',
					),
				),
			),
			'image'          => array(
				'css' => array(
					'main' => '%%order_class%% table.cart img',
				),
			),
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
									'style' => 'solid',
									'color' => '#eeeeee',
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
					'toggle_priority'        => 55,
				),
				'table_row'  => array(
					'label'                  => esc_html__( 'Table Row', 'et_builder' ),
					'css'                    => array(
						'main'                   => '%%order_class%% table.shop_table tr',
						'background_color'       => '%%order_class%% table.shop_table tr',
						'background_color_hover' => '%%order_class%% table.shop_table tr:hover',
					),
					'background_color'       => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s table row.', 'et_builder' ),
					),
					'font_field'             => false,
					'margin_padding'         => array(
						'css'         => array(
							'main'      => array(
								'%%order_class%% th',
								'%%order_class%% td',
							),
							'important' => array( 'custom_padding' ),
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
								'main' => array(
									'border_styles' => '%%order_class%% table.shop_table tr',
								),
							),
							'defaults'          => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '0px',
									'style' => 'none',
								),
							),
							'use_focus_borders' => false,
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
					'label'                        => esc_html__( 'Table Cell', 'et_builder' ),
					// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned -- Seems to be a false positive.
					'css'                          => array(
						'main'                               => '%%order_class%% table.shop_table tr td',
						'background_color'                   => '%%order_class%% table.shop_table tr td',
						'alternating_background_color'       => '%%order_class%% table.shop_table_responsive tr:nth-child(2n) td',
						'alternating_background_color_hover' => '%%order_class%% table.shop_table_responsive tr:nth-child(2n):hover td',
						'background_color_hover'             => '%%order_class%% table.shop_table tr td:hover',
						'important'                          => array(
							'background_color',
							'alternating_background_color',
						),
					),
					// phpcs:enable
					'background_color'             => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s table cell.', 'et_builder' ),
					),
					'font_field'                   => false,
					'margin_padding'               => array(
						'css'        => array(
							'main'      => array(
								'%%order_class%% th',
								'%%order_class%% td',
							),
							'important' => array( 'custom_padding' ),
						),
						'use_margin' => false,
					),
					'text_color'                   => false,
					'alternating_background_color' => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s table alternating cell.', 'et_builder' ),
					),
					'focus_background_color'       => false,
					'focus_text_color'             => false,
					'border_styles'                => array(
						'table_cell' => array(
							'label_prefix'      => 'Table Cell',
							'css'               => array(
								'main' => array(
									'border_styles' => '%%order_class%% table.shop_table tr td, %%order_class%% table.shop_table th',
									'border_radii'  => '%%order_class%% table.shop_table tr td, %%order_class%% table.shop_table th',
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
										'border_style_top' => 'solid',
										'border_color_top' => 'rgba(0,0,0,.1)',
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
					'box_shadow'                   => array(
						'css' => array(
							'main' => '%%order_class%% table.shop_table td',
						),
					),
				),
				'icon'       => array(
					'label'                  => esc_html__( 'Remove Icon', 'et_builder' ),
					'css'                    => array(
						'main'                   => '%%order_class%% a.remove',
						'form_text_color'        => '%%order_class%% a.remove',
						'background_color'       => '%%order_class%% a.remove',
						'background_color_hover' => '%%order_class%% a.remove:hover',
						'important'              => array( 'form_text_color' ),
					),
					'background_color'       => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s remove icon.', 'et_builder' ),
					),
					'text_color'             => array(
						'description' => esc_html__( 'Pick a color to be used for the remove icon.', 'et_builder' ),
					),
					'placeholder'            => false,
					'font_field'             => array(
						'hide_font'           => true,
						'hide_text_align'     => true,
						'hide_letter_spacing' => true,
						'hide_line_height'    => true,
						'hide_text_shadow'    => true,
						'css'                 => array(
							'main' => '%%order_class%% a.remove',
						),
						'font_size'           => array(
							'default' => '1.5em',
						),
					),
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'box_shadow'             => false,
					'border_styles'          => false,
					'margin_padding'         => false,
				),
				'form_field' => array(
					'label'            => esc_html__( 'Field', 'et_builder' ),
					'css'              => array(
						'main'                         => '%%order_class%% .quantity input.qty',
						'background_color'             => '%%order_class%% .quantity input.qty, %%order_class%% table.cart td.actions .coupon .input-text',
						'background_color_hover'       => '%%order_class%% form .input-text:hover, %%order_class%% table.cart td.actions .coupon .input-text:hover',
						'focus_background_color'       => '%%order_class%% .quantity input.qty:focus, %%order_class%% table.cart td.actions .coupon .input-text:focus',
						'focus_background_color_hover' => '%%order_class%% .quantity input.qty:focus:hover, %%order_class%% table.cart td.actions .coupon .input-text:focus:hover',
						'placeholder_focus'            => '%%order_class%% .input-text:focus::-webkit-input-placeholder, %%order_class%% .input-text:focus::-moz-placeholder, %%order_class%% p .input-text:focus:-ms-input-placeholder',
						'form_text_color'              => '%%order_class%% .quantity input.qty, %%order_class%% table.cart td.actions .coupon  .input-text',
						'form_text_color_hover'        => '%%order_class%% .quantity input.qty:hover, %%order_class%% table.cart td.actions .coupon  .input-text:hover',
						'focus_text_color'             => '%%order_class%% .quantity input.qty:focus, %%order_class%% table.cart td.actions .coupon .input-text:focus',
						'focus_text_color_hover'       => '%%order_class%% .quantity input.qty:focus:hover, %%order_class%% table.cart td.actions .coupon .input-text:focus:hover',
						'important'                    => array(
							'background_color',
							'form_text_color',
							'focus_background_color',
						),
					),
					'background_color' => array(
						'description' => esc_html__( 'Pick a color to fill the module\'s fields.', 'et_builder' ),
					),
					'box_shadow'       => array(
						'css' => array(
							'main'      => implode(
								',',
								array(
									'%%order_class%% .quantity input.qty',
									'%%order_class%% table.cart td.actions .coupon .input-text',
								)
							),
							'important' => true,
						),
					),
					'border_styles'    => array(
						'form_field'       => array(
							'label_prefix' => esc_html__( 'Field', 'et_builder' ),
							'css'          => array(
								'main'      => array(
									'border_styles' => implode(
										',',
										array(
											'%%order_class%% .quantity input.qty',
											'%%order_class%% table.cart td.actions .coupon .input-text',
										)
									),
									'border_radii'  => implode(
										',',
										array(
											'%%order_class%% .quantity input.qty',
											'%%order_class%% table.cart td.actions .coupon .input-text',
										)
									),
								),
								'important' => true,
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
							'label_prefix' => esc_html__( 'Field Focus', 'et_builder' ),
							'css'          => array(
								'main'      => array(
									'border_styles' => implode(
										',',
										array(
											'%%order_class%% .quantity input.qty:focus',
											'%%order_class%% table.cart td.actions .coupon .input-text:focus',
										)
									),
									'border_radii'  => implode(
										',',
										array(
											'%%order_class%% .quantity input.qty:focus',
											'%%order_class%% table.cart td.actions .coupon .input-text:focus',
										)
									),
								),
								'important' => true,
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
									'%%order_class%% .quantity input.qty',
									'%%order_class%% .quantity input.qty::placeholder',
									'%%order_class%% .quantity input.qty::-webkit-input-placeholder',
									'%%order_class%% .quantity input.qty::-moz-placeholder',
									'%%order_class%% .quantity input.qty:-ms-input-placeholder',
									'%%order_class%% table.cart td.actions .coupon .input-text',
									'%%order_class%% table.cart td.actions .coupon .input-text::placeholder',
									'%%order_class%% table.cart td.actions .coupon .input-text::-webkit-input-placeholder',
									'%%order_class%% table.cart td.actions .coupon .input-text::-moz-placeholder',
									'%%order_class%% table.cart td.actions .coupon .input-text:-ms-input-placeholder',
								)
							),
							'hover'     => implode(
								', ',
								array(
									'%%order_class%% .quantity input.qty:hover',
									'%%order_class%% .quantity input.qty:hover::placeholder',
									'%%order_class%% .quantity input.qty:hover::-webkit-input-placeholder',
									'%%order_class%% .quantity input.qty:hover::-moz-placeholder',
									'%%order_class%% .quantity input.qty:hover:-ms-input-placeholder',
									'%%order_class%% table.cart td.actions .coupon .input-text:hover',
									'%%order_class%% table.cart td.actions .coupon .input-text:hover::placeholder',
									'%%order_class%% table.cart td.actions .coupon .input-text:hover::-webkit-input-placeholder',
									'%%order_class%% table.cart td.actions .coupon .input-text:hover::-moz-placeholder',
									'%%order_class%% table.cart td.actions .coupon .input-text:hover:-ms-input-placeholder',
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
					'margin_padding'   => array(
						'css' => array(
							'main'      => '%%order_class%% form .input-text, %%order_class%% table.cart td.actions .coupon .input-text',
							'padding'   => '%%order_class%% form .qty.input-text, %%order_class%% table.cart td.actions .coupon .input-text',
							'margin'    => '%%order_class%%.woocommerce-cart table.cart input, %%order_class%% table.cart td.actions .coupon .input-text',
							'important' => array( 'custom_padding' ),
						),
					),
					'width'            => array(),
				),
			),
			'height'         => array(
				'use_min_height' => false,
				'use_height'     => false,
				'use_max_height' => false,
			),
			'button'         => array(
				'button'          => array(
					'label'           => esc_html__( 'Button', 'et_builder' ),
					'css'             => array(
						'main' => implode(
							',',
							array(
								'%%order_class%% table.cart button[name="apply_coupon"]',
								'%%order_class%% table.cart button[name="update_cart"]:not([disabled])',
							)
						),
					),
					'text_size'       => array(
						'default' => '20px',
					),
					'use_alignment'   => false,
					'box_shadow'      => array(
						'css' => array(
							'main' => implode(
								',',
								array(
									'%%order_class%% table.cart button[name="apply_coupon"]',
									'%%order_class%% table.cart button[name="update_cart"]:not([disabled])',
								)
							),
						),
					),
					'margin_padding'  => array(
						'css' => array(
							'main'      => implode(
								',',
								array(
									'%%order_class%% table.cart button[name="apply_coupon"]',
									'%%order_class%% table.cart button[name="update_cart"]:not([disabled])',
								)
							),
							'important' => 'all',
						),
						// Removed the margin padding defaults to NOT affect Extra theme.
					),
					'no_rel_attr'     => false,
					'toggle_priority' => 80,
				),
				'disabled_button' => array(
					'label'           => esc_html__( 'Disabled Button', 'et_builder' ),
					'css'             => array(
						'main' => '%%order_class%% table.cart button[name="update_cart"]:disabled',
					),
					'text_size'       => array(
						'default' => '20px',
					),
					'use_alignment'   => false,
					'box_shadow'      => array(
						'css' => array(
							'main' => '%%order_class%% table.cart button[name="update_cart"]:disabled',
						),
					),
					'margin_padding'  => array(
						'css' => array(
							'main'      => '%%order_class%% table.cart button[name="update_cart"]:disabled',
							'important' => 'all',
						),
						// Removed the margin padding defaults to NOT affect Extra theme.
					),
					'no_rel_attr'     => false,
					'toggle_priority' => 81,
				),
			),
			'max_width'      => array(
				'css' => array(
					'main'             => $this->main_css_element,
					'module_alignment' => '%%order_class%%.et_pb_wc_cart_products.et_pb_module',
				),
			),
		);

		$this->custom_css_fields = array(
			'body'             => array(
				'label'    => esc_html__( 'Body', 'et_builder' ),
				'selector' => '%%order_class%% td.product-price, %%order_class%% td.product-subtotal, %%order_class%% td.product-name dl.variation dd',
			),
			'link'             => array(
				'label'    => esc_html__( 'Link', 'et_builder' ),
				'selector' => '%%order_class%% td.product-name a',
			),
			'column_label'     => array(
				'label'    => esc_html__( 'Column Label', 'et_builder' ),
				'selector' => '%%order_class%% table.cart th',
			),
			'table'            => array(
				'label'    => esc_html__( 'Table', 'et_builder' ),
				'selector' => '%%order_class%% table.cart',
			),
			'table_header_row' => array(
				'label'    => esc_html__( 'Table Header Row', 'et_builder' ),
				'selector' => '%%order_class%% table.cart th',
			),
			'table_rows'       => array(
				'label'    => esc_html__( 'Table Rows', 'et_builder' ),
				'selector' => '%%order_class%% table.cart tr',
			),
			'product_image'    => array(
				'label'    => esc_html__( 'Product Image', 'et_builder' ),
				'selector' => '%%order_class%% table.cart img',
			),
			'product_name'     => array(
				'label'    => esc_html__( 'Product Name', 'et_builder' ),
				'selector' => '%%order_class%% td.product-name a',
			),
			'variation_label'  => array(
				'label'    => esc_html__( 'Variation Label', 'et_builder' ),
				'selector' => '%%order_class%% .product-name dl.variation',
			),
			'remove_icon'      => array(
				'label'    => esc_html__( 'Remove Icon', 'et_builder' ),
				'selector' => '%%order_class%% a.remove',
			),
			'input_fields'     => array(
				'label'    => esc_html__( 'Input Fields', 'et_builder' ),
				'selector' => '%%order_class%% .quantity input.qty, %%order_class%% table.cart td.actions .coupon  .input-text',
			),
			'button'           => array(
				'label'    => esc_html__( 'Button', 'et_builder' ),
				'selector' => '%%order_class%% table.cart button[name="apply_coupon"]',
			),
			'disabled_button'  => array(
				'label'    => esc_html__( 'Disabled Button', 'et_builder' ),
				'selector' => '%%order_class%% table.cart button[name="update_cart"]:disabled',
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
			'__cart_products'                => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Cart_Products',
					'get_cart_products',
				),
				'computed_depends_on' => array(

					/*
					 * `depends_on` arg is required. Otherwise AJAX will return null.
					 * @see et_pb_process_computed_property().
					 * `product` will not be processed since there is no definition in
					 * @see ET_Builder_Module_Woocommerce_Checkout_Order::get_fields()
					 */
					'product',
					'show_update_cart_button',
				),
			),
			'row_layout'                     => array(
				'label'           => esc_html__( 'Product Row Layout', 'et_builder' ),
				'description'     => esc_html__( 'Set the row layout.', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'default'    => __( 'Default', 'et_builder' ),
					'horizontal' => __( 'Horizontal', 'et_builder' ),
					'vertical'   => __( 'Vertical', 'et_builder' ),
				),
				'default'         => 'default',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'layout',
				'mobile_options'  => true,
				'affects'         => array(
					'image',
					'border_radii_image',
					'border_styles_image',
					'table_header_background_color',
				),
			),
			'image_max_width'                => array(
				'label'            => esc_html__(
					'Image Width',
					'et_builder'
				),
				'description'      => esc_html__(
					'Adjust the width of the image within the table.',
					'et_builder'
				),
				'type'             => 'range',
				'option_category'  => 'layout',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'width',
				'mobile_options'   => true,
				'validate_unit'    => true,
				'depends_show_if'  => 'off',
				'allowed_units'    => array(
					'%',
					'em',
					'rem',
					'px',
					'cm',
					'mm',
					'in',
					'pt',
					'pc',
					'ex',
					'vh',
					'vw',
				),
				'default'          => '32px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'allow_empty'      => true,
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'responsive'       => true,
				'sticky'           => false,
			),
			'collapse_table_gutters_borders' => ET_Builder_Module_Helper_Woocommerce_Modules::get_field( 'collapse_table_gutters_borders' ),
			'vertical_gutter_width'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_field( 'vertical_gutter_width' ),
			'horizontal_gutter_width'        => ET_Builder_Module_Helper_Woocommerce_Modules::get_field( 'horizontal_gutter_width' ),
			'placeholder_color'              => ET_Builder_Module_Helper_Woocommerce_Modules::get_field( 'placeholder_color' ),
			'show_product_image'             => array(
				'label'           => esc_html__( 'Show Product Image', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'description'     => esc_html__( 'Here you can choose whether or not to display the Product image.', 'et_builder' ),
				'toggle_slug'     => 'elements',
				'tab_slug'        => 'general',
				'default'         => 'on',
				'mobile_options'  => true,
				'show_if_not'     => array(
					'row_layout' => 'vertical',
				),
			),
			'show_coupon_code'               => array(
				'label'           => esc_html__( 'Show Coupon Code', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'description'     => esc_html__( 'Here you can choose whether or not to display the Coupon code.', 'et_builder' ),
				'toggle_slug'     => 'elements',
				'tab_slug'        => 'general',
				'default'         => 'on',
				'mobile_options'  => true,
			),
			'show_update_cart_button'        => array(
				'label'           => esc_html__( 'Show Update Cart Button', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'description'     => esc_html__( 'Here you can choose whether or not to display the Update cart button.', 'et_builder' ),
				'toggle_slug'     => 'elements',
				'tab_slug'        => 'general',
				'default'         => 'on',
				'mobile_options'  => true,
			),
			'show_remove_item_icon'          => array(
				'label'           => esc_html__( 'Show Remove Item Icon', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'description'     => esc_html__( 'Here you can choose whether or not to display the Remove Item Icon.', 'et_builder' ),
				'toggle_slug'     => 'elements',
				'tab_slug'        => 'general',
				'default'         => 'on',
				'mobile_options'  => true,
			),
			'table_header_background_color'  => array(
				'label'               => esc_html__( 'Table Header Background Color', 'et_builder' ),
				'description'         => esc_html__( 'Pick a color to fill the module\'s table header.', 'et_builder' ),
				'type'                => 'color-alpha',
				'option_category'     => 'field',
				'custom_color'        => true,
				'tab_slug'            => 'advanced',
				'toggle_slug'         => 'table_header',
				'hover'               => 'tabs',
				'mobile_options'      => true,
				'sticky'              => true,
				'depends_show_if_not' => 'vertical',
				'depends_on'          => array(
					'row_layout',
				),
			),
		);

		return $fields;
	}

	/**
	 * Display mocked variation attribute in VB.
	 *
	 * @param array $cart_item Cart Item.
	 */
	public static function display_variation_attribute( $cart_item ) {
		$product_id = $cart_item['product_id'];

		switch ( $product_id ) {
			case 1000:
				$item_data = array(
					array(
						'key'     => 'Size',
						'display' => 'Large',
					),
				);
				break;
			case 1001:
				$item_data = array(
					array(
						'key'     => 'Color',
						'display' => 'Black',
					),
				);
				break;
			default:
				return;
		}

		wc_get_template( 'cart/cart-item-data.php', array( 'item_data' => $item_data ) );
	}

	/**
	 * Sets dummy permalink.
	 *
	 * @return string
	 */
	public static function set_dummy_permalink() {
		return '#';
	}

	/**
	 * Sets dummy cart contents to be dipslayed in VB.
	 *
	 * @param array $cart_contents Cart Contents.
	 *
	 * @return array
	 */
	public static function set_dummy_cart_contents( $cart_contents ) {
		if ( ! is_array( $cart_contents ) ) {
			return $cart_contents;
		}

		$cart_contents = array();
		$fake_products = array(
			999  => array(
				'name'     => esc_html__( 'Product 1', 'et_builder' ),
				'price'    => '12.00',
				'quantity' => 3,
			),
			1000 => array(
				'name'     => esc_html__( 'Product 2', 'et_builder' ),
				'price'    => '75.00',
				'quantity' => 1,
			),
			1001 => array(
				'name'     => esc_html__( 'Product 3', 'et_builder' ),
				'price'    => '38.00',
				'quantity' => 2,
			),
		);

		foreach ( $fake_products as $id => $details ) {
			$product = new ET_Builder_Woocommerce_Product_Simple_Placeholder();
			$product->set_name( $details['name'] );
			$product->set_id( $id );
			$product->set_price( $details['price'] );

			$cart_item_key = WC()->cart->generate_cart_id( $product->get_id() );

			$cart_contents[ $cart_item_key ] = array(
				'key'          => $cart_item_key,
				'product_id'   => $product->get_id(),
				'variation_id' => 0,
				'variation'    => array(),
				'quantity'     => $details['quantity'],
				'data'         => $product,
				'data_hash'    => wc_get_cart_item_data_hash( $product ),
			);
		}

		return $cart_contents;
	}

	/**
	 * Set readonly attribute when Show Update Cart btn is set to OFF.
	 *
	 * @param array $input_args Args for the input.
	 *
	 * @return mixed
	 */
	public static function set_quantity_input_readonly( $input_args ) {
		$input_args['readonly'] = 'readonly';

		return $input_args;
	}

	/**
	 * Handle hooks.
	 *
	 * @param array $conditional_tags Array of conditional tags.
	 */
	public static function maybe_handle_hooks( $conditional_tags ) {
		remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
		remove_action( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 10 );

		$class = 'ET_Builder_Module_Woocommerce_Cart_Products';
		$is_tb = et_()->array_get( $conditional_tags, 'is_tb', false );

		// Runs on both VB and FE.
		add_filter(
			'wc_get_template',
			array(
				$class,
				'swap_quantity_input_template',
			),
			10,
			5
		);

		if ( et_fb_is_computed_callback_ajax() || $is_tb || is_et_pb_preview() ) {
			// Runs only on Builder mode.
			add_filter(
				'wc_get_template',
				array(
					$class,
					'swap_template',
				),
				10,
				5
			);
		}
	}

	/**
	 * Reset hooks.
	 *
	 * @param array $conditional_tags Array of conditional tags.
	 */
	public static function maybe_reset_hooks( $conditional_tags ) {
		add_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals', 10 );
		add_action( 'woocommerce_before_cart', 'woocommerce_output_all_notices', 10 );

		$is_tb = et_()->array_get( $conditional_tags, 'is_tb', false );
		$class = 'ET_Builder_Module_Woocommerce_Cart_Products';

		remove_filter(
			'wc_get_template',
			array(
				$class,
				'swap_quantity_input_template',
			)
		);

		if ( et_fb_is_computed_callback_ajax() || $is_tb || is_et_pb_preview() ) {
			remove_filter(
				'wc_get_template',
				array(
					$class,
					'swap_template',
				)
			);
		}
	}

	/**
	 * Swaps Quantity input template.
	 *
	 * @param string $template      Template.
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments.
	 * @param string $template_path Template path.
	 * @param string $default_path  Default path.
	 *
	 * @return string
	 */
	public static function swap_quantity_input_template( $template, $template_name, $args, $template_path, $default_path ) {
		$is_template_override = in_array(
			$template_name,
			array(
				'global/quantity-input.php',
			),
			true
		);

		if ( $is_template_override ) {
			return trailingslashit( ET_BUILDER_DIR ) . 'feature/woocommerce/templates/' . $template_name;
		}

		return $template;
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
				'cart/cart.php',
			),
			true
		);

		if ( $is_template_override ) {
			return trailingslashit( ET_BUILDER_DIR ) . 'feature/woocommerce/templates/' . $template_name;
		}

		return $template;
	}

	/**
	 * Gets Cart Products markup.
	 *
	 * @param array $args Props.
	 * @param array $conditional_tags Conditional tags set during computed property AJAX call.
	 *
	 * @return string
	 */
	public static function get_cart_products( $args = array(), $conditional_tags = array() ) {
		if ( ! class_exists( 'WC_Shortcode_Cart' ) ||
			! method_exists( 'WC_Shortcode_Cart', 'output' ) ) {
			return '';
		}

		$is_tb              = et_()->array_get( $conditional_tags, 'is_tb', false );
		$is_use_placeholder = $is_tb || is_et_pb_preview();

		// Stop mocking Cart Products when Cart isn't empty.
		$reset_filters = false;

		self::maybe_handle_hooks( $conditional_tags );

		if ( ( $is_use_placeholder || et_fb_is_computed_callback_ajax() ) && WC()->cart->is_empty() ) {
			add_filter(
				'woocommerce_get_cart_contents',
				array(
					'ET_Builder_Module_Woocommerce_Cart_Products',
					// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- This is a function call.
					'set_dummy_cart_contents'
				)
			);

			add_filter(
				'woocommerce_cart_item_permalink',
				array(
					'ET_Builder_Module_Woocommerce_Cart_Products',
					// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- This is a function call.
					'set_dummy_permalink'
				)
			);

			add_action(
				'woocommerce_after_cart_item_name',
				array(
					'ET_Builder_Module_Woocommerce_Cart_Products',
					// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- This is a function call.
					'display_variation_attribute'
				)
			);

			$reset_filters = true;
		}

		$show_update_cart_button = isset( $args['show_update_cart_button'] )
			? $args['show_update_cart_button']
			: 'on';

		if ( 'off' === $show_update_cart_button ) {
			add_filter(
				'woocommerce_quantity_input_args',
				array(
					'ET_Builder_Module_Woocommerce_Cart_Products',
					'set_quantity_input_readonly',
				)
			);
		}

		ob_start();
		if ( isset( WC()->cart ) && ! WC()->cart->is_empty() ) {
			wc_get_template( 'cart/cart.php' );
		}
		$markup = ob_get_clean();

		if ( 'off' === $show_update_cart_button ) {
			remove_filter(
				'woocommerce_quantity_input_args',
				array(
					'ET_Builder_Module_Woocommerce_Cart_Products',
					'set_quantity_input_readonly',
				)
			);
		}

		if ( ( $is_use_placeholder || et_fb_is_computed_callback_ajax() ) && $reset_filters ) {
			remove_filter(
				'woocommerce_get_cart_contents',
				array(
					'ET_Builder_Module_Woocommerce_Cart_Products',
					'set_dummy_cart_contents',
				)
			);
			remove_filter(
				'woocommerce_cart_item_permalink',
				array(
					'ET_Builder_Module_Woocommerce_Cart_Products',
					'set_dummy_permalink',
				)
			);
			remove_action(
				'woocommerce_after_cart_item_name',
				array(
					'ET_Builder_Module_Woocommerce_Cart_Products',
					'display_variation_attribute',
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
	 * @param array $outer_wrapper_attrs Outer wrapper attributes.
	 *
	 * @return array
	 */
	public function add_multi_view_attrs( $outer_wrapper_attrs ) {
		$multi_view = et_pb_multi_view_options( $this );

		$multi_view_attrs = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_row_layout_default'       => array(
						'row_layout' => 'default',
					),
					'et_pb_row_layout_horizontal'    => array(
						'row_layout' => 'horizontal',
					),
					'et_pb_row_layout_vertical'      => array(
						'row_layout' => 'vertical',
					),
					'et_pb_wc_no_product_image'      => array(
						'show_product_image' => 'off',
					),
					'et_pb_wc_no_coupon_code'        => array(
						'show_coupon_code' => 'off',
					),
					'et_pb_wc_no_update_cart_button' => array(
						'show_update_cart_button' => 'off',
					),
					'et_pb_wc_no_remove_item_icon'   => array(
						'show_remove_item_icon' => 'off',
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
	 * Gets the required HTML data attributes based on the button name and slug.
	 *
	 * @param string $btn_name Button name.
	 * @param string $btn_slug Button slug.
	 *
	 * @return array
	 */
	public function get_button_icon_attrs( $btn_name, $btn_slug = 'button' ) {
		$attrs = array();

		// Get Icon values based on Device.
		$custom_icon_values = et_pb_responsive_options()->get_property_values( $this->props, "{$btn_slug}_icon", '', true );

		$custom_icon        = et_()->array_get( $custom_icon_values, 'desktop', '' );
		$custom_icon_tablet = et_()->array_get( $custom_icon_values, 'tablet', $custom_icon );
		$custom_icon_phone  = et_()->array_get( $custom_icon_values, 'phone', $custom_icon );

		// Store the device based values in array to filter out empty values using array_filter().
		$icon_values = array(
			"data-{$btn_name}-icon"        => $custom_icon,
			"data-{$btn_name}-icon-tablet" => $custom_icon_tablet,
			"data-{$btn_name}-icon-phone"  => $custom_icon_phone,
		);

		// Verify if Custom icon value exists at least in one of Desktop / Tablet / Phone.
		$has_custom_icon = count( array_filter( $icon_values ) ) > 0;

		if ( ! $has_custom_icon ) {
			return $attrs;
		}

		// The following data-* will be used in JS manipulation.
		$attrs['data-button-names'] = esc_attr( $btn_name ); // phpcs:ignore WordPress.Arrays.ArrayKeySpacingRestrictions.NoSpacesAroundArrayKeys -- The key is not a variable.
		$attrs['data-button-class'] = et_core_intentionally_unescaped( 'et_pb_custom_button_icon et_pb_button', 'fixed_string' );

		// Get the icon data-* attributes based on values set in DB.
		foreach ( $icon_values as $attr_name => $attr_value ) {
			if ( empty( $attr_value ) ) {
				continue;
			}

			$attrs[ $attr_name ] = esc_attr( et_pb_process_font_icon( $attr_value ) );
		}

		return $attrs;
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
		if ( ! is_array( $this->props ) || empty( $this->props ) ) {
			return $outer_wrapper_attrs;
		}

		// Use short variable name for readability.
		$attrs = $outer_wrapper_attrs;

		$apply_coupon_btn_attrs = $this->get_button_icon_attrs( 'apply_coupon', 'button' );
		$update_cart_btn_attrs  = $this->get_button_icon_attrs( 'update_cart', 'disabled_button' );

		$names = array();
		if ( isset( $apply_coupon_btn_attrs['data-button-names'] ) ) {
			$names[] = $apply_coupon_btn_attrs['data-button-names'];
		}

		if ( isset( $update_cart_btn_attrs['data-button-names'] ) ) {
			$names[] = $update_cart_btn_attrs['data-button-names'];
		}

		$attrs = array_merge( $attrs, $apply_coupon_btn_attrs, $update_cart_btn_attrs );
		unset( $attrs['data-button-names'] );

		if ( ! empty( $names ) ) {
			$attrs['data-button-names'] = implode( ' ', $names );
		}

		return $attrs;
	}

	/**
	 * Gets the actual css rule-set (i.e. a selector & a declaration block).
	 *
	 * @param string $value Shortcode attribute value.
	 *
	 * @return mixed
	 */
	public function get_width_style( $value ) {
		$value          = et_builder_process_range_value( $value );
		$property_value = 'calc( ' . $value . ' * 10 )';

		return sprintf( 'width: %1$s;', esc_html( $property_value ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['placeholder_color'] = array(
			'color' => array(
				'%%order_class%% .quantity input.qty::placeholder',
				'%%order_class%% .quantity input.qty::-webkit-input-placeholder',
				'%%order_class%% .quantity input.qty::-moz-placeholder',
				'%%order_class%% .quantity input.qty:-ms-input-placeholder',
				'%%order_class%% table.cart td.actions .coupon .input-text::placeholder',
				'%%order_class%% table.cart td.actions .coupon .input-text::-webkit-input-placeholder',
				'%%order_class%% table.cart td.actions .coupon .input-text::-moz-placeholder',
				'%%order_class%% table.cart td.actions .coupon .input-text:-ms-input-placeholder',
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
		$image_max_width             = $this->props['image_max_width'];
		$image_max_width_tablet      = $this->props['image_max_width_tablet'];
		$image_max_width_phone       = $this->props['image_max_width_phone'];
		$image_max_width_last_edited = $this->props['image_max_width_last_edited'];

		$this->add_classname( 'woocommerce-cart' );
		$this->add_classname( 'woocommerce' );
		$this->add_classname( 'et_pb_woo_custom_button_icon' );
		$this->add_classname( $this->get_text_orientation_classname() );

		if ( function_exists( 'WC' ) && isset( WC()->cart ) && WC()->cart->is_empty() ) {
			$this->add_classname( 'et_pb_wc_cart_empty' );
		}

		$row_layout = et_()->array_get( $this->props, 'row_layout', false );
		if ( false !== $row_layout ) {
			$this->add_classname( "et_pb_row_layout_{$row_layout}" );
		}

		$show_product_image = et_()->array_get( $this->props, 'show_product_image', 'on' );
		if ( 'off' === $show_product_image ) {
			$this->add_classname( 'et_pb_wc_no_product_image' );
		}

		$show_coupon_code = et_()->array_get( $this->props, 'show_coupon_code', 'on' );
		if ( 'off' === $show_coupon_code ) {
			$this->add_classname( 'et_pb_wc_no_coupon_code' );
		}

		$show_update_cart_button = et_()->array_get( $this->props, 'show_update_cart_button', 'on' );
		if ( 'off' === $show_update_cart_button ) {
			$this->add_classname( 'et_pb_wc_no_update_cart_button' );
		}

		$show_remove_item_icon = et_()->array_get( $this->props, 'show_remove_item_icon', 'on' );
		if ( 'off' === $show_remove_item_icon ) {
			$this->add_classname( 'et_pb_wc_no_remove_item_icon' );
		}

		// Images: Add CSS Filters and Mix Blend Mode rules (if set).
		if ( array_key_exists( 'image', $this->advanced_fields )
			&& array_key_exists( 'css', $this->advanced_fields['image'] ) ) {
			$this->add_classname(
				$this->generate_css_filters(
					$render_slug,
					'child_',
					self::$data_utils->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' )
				)
			);
		}

		// Set `data-*` attrs for Button Icons to work.
		// Handles both `Apply Coupon` and `Update Cart` buttons.
		add_filter(
			"et_builder_module_{$render_slug}_outer_wrapper_attrs",
			// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- Call to a function.
			array( $this, 'add_custom_icon_attrs' )
		);

		add_filter(
			"et_builder_module_{$render_slug}_outer_wrapper_attrs",
			// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- Call to a function.
			array( $this, 'add_multi_view_attrs' )
		);

		$image_max_width_responsive_active = et_pb_get_responsive_status( $image_max_width_last_edited );

		$image_max_width_values = array(
			'desktop' => $image_max_width,
			'tablet'  => $image_max_width_responsive_active ? $image_max_width_tablet : '',
			'phone'   => $image_max_width_responsive_active ? $image_max_width_phone : '',
		);

		$image_max_width_selector = '%%order_class%% table.cart img';
		$image_max_width_property = 'width';

		et_pb_responsive_options()->generate_responsive_css( $image_max_width_values, $image_max_width_selector, $image_max_width_property, $render_slug );

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

		// Table Header Background Color.
		$table_header_selectors = array(
			'%%order_class%% table.cart th',
		);

		$this->generate_styles(
			array(
				'base_attr_name'                  => 'table_header_background_color',
				'selector'                        => join( ', ', $table_header_selectors ),
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'background-color',
				'important'                       => false,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		$output = self::get_cart_products( $attrs );

		// Placeholder Color.
		$placeholder_selectors = array(
			'%%order_class%% .quantity input.qty::placeholder',
			'%%order_class%% .quantity input.qty::-webkit-input-placeholder',
			'%%order_class%% .quantity input.qty::-moz-placeholder',
			'%%order_class%% .quantity input.qty:-ms-input-placeholder',
			'%%order_class%% table.cart td.actions .coupon .input-text::placeholder',
			'%%order_class%% table.cart td.actions .coupon .input-text::-webkit-input-placeholder',
			'%%order_class%% table.cart td.actions .coupon .input-text::-moz-placeholder',
			'%%order_class%% table.cart td.actions .coupon .input-text:-ms-input-placeholder',
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

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Cart_Products();
