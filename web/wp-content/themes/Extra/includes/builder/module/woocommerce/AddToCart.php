<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Add_To_Cart class
 *
 * The ET_Builder_Module_Woocommerce_Add_To_Cart Class is responsible for rendering the
 * Add To Cart markup using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Add to cart component.
 */
class ET_Builder_Module_Woocommerce_Add_To_Cart extends ET_Builder_Module {
	/**
	 * Initialize.
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Product Add To Cart', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Product Add To Cart', 'et_builder' );
		$this->slug        = 'et_pb_wc_add_to_cart';
		$this->vb_support  = 'on';
		$this->folder_name = 'et_pb_woo_modules';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Content' ),
					'elements'     => et_builder_i18n( 'Elements' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'field_label' => array(
						'title' => esc_html__( 'Field Labels', 'et_builder' ),
					),
					'header'      => array(
						'title'             => esc_html__( 'Heading Text', 'et_builder' ),
						'priority'          => 49,
						'tabbed_subtoggles' => true,
						'sub_toggles'       => array(
							'h1' => array(
								'name' => 'H1',
								'icon' => 'text-h1',
							),
							'h2' => array(
								'name' => 'H2',
								'icon' => 'text-h2',
							),
							'h3' => array(
								'name' => 'H3',
								'icon' => 'text-h3',
							),
							'h4' => array(
								'name' => 'H4',
								'icon' => 'text-h4',
							),
							'h5' => array(
								'name' => 'H5',
								'icon' => 'text-h5',
							),
							'h6' => array(
								'name' => 'H6',
								'icon' => 'text-h6',
							),
						),
					),
					'width'       => array(
						'title'    => et_builder_i18n( 'Sizing' ),
						'priority' => 80,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'field_label' => array(
					'label'       => esc_html__( 'Field Labels', 'et_builder' ),
					'css'         => array(
						'main' => implode(
							',',
							array(
								'%%order_class%% label',
							)
						),
					),
					'font'        => array(
						'default' => '|700|||||||',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'toggle_slug' => 'field_label',
				),
			),
			'background'     => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'text'           => array(
				'use_background_layout' => false,
				'css'                   => array(
					'main'        => '%%order_class%% td.label',
					'text_shadow' => '%%order_class%% td.label',
				),
				'options'               => array(
					'text_orientation' => array(
						'default' => 'left',
					),
				),
			),
			'button'         => array(
				'button' => array(
					'label'          => et_builder_i18n( 'Button' ),
					'css'            => array(
						'main'         => '%%order_class%% .button',
						'limited_main' => '%%order_class%% .button',
						'alignment'    => '%%order_class%% .et_pb_module_inner > form',
						// Setting to TRUE since it only checks for the value's existence.
						'important'    => 'all',
					),

					/*
					 * Button inside add to cart module is rendered from WooCommerce's default
					 * template which makes its positioning isn't flexible. Thus button alignment
					 * is removed.
					 */
					'use_alignment'  => false,
					'box_shadow'     => array(
						'css' => array(
							'main'      => '%%order_class%% .button',
							'important' => true,
						),
					),
					'use_icon'       => false,
					'margin_padding' => array(
						'css' => array(
							'important' => 'all',
						),
					),
				),
			),
			'form_field'     => array(
				'fields'         => array(
					'label'           => esc_html__( 'Fields', 'et_builder' ),
					'toggle_priority' => 67,
					'css'             => array(
						'main'                   => '%%order_class%% input, %%order_class%% .quantity input.qty',
						'background_color'       => '%%order_class%% input, %%order_class%% .quantity input.qty',
						'background_color_hover' => '%%order_class%% input:hover, %%order_class%% .quantity input.qty:hover',
						'focus_background_color' => '%%order_class%% input:focus, %%order_class%% select:focus, %%order_class%% .quantity input.qty:focus',
						'form_text_color'        => '%%order_class%% input, %%order_class%% select, %%order_class%% .quantity input.qty',
						'form_text_color_hover'  => '%%order_class%% input[type="text"]:hover, %%order_class%% select:hover, %%order_class%% .quantity input.qty:hover',
						'focus_text_color'       => '%%order_class%% input:focus, %%order_class%% .quantity input.qty:focus',
						'placeholder_focus'      => '%%order_class%% input:focus::-webkit-input-placeholder, %%order_class%% input:focus::-moz-placeholder, %%order_class%% input:focus:-ms-input-placeholder, %%order_class%% textarea:focus::-webkit-input-placeholder, %%order_class%% textarea:focus::-moz-placeholder, %%order_class%% textarea:focus:-ms-input-placeholder',
						'padding'                => '%%order_class%% input',
						'margin'                 => '%%order_class%%',
						'important'              => array(
							'background_color',
							'background_color_hover',
							'focus_background_color',
							'form_text_color',
							'form_text_color_hover',
							'text_color',
							'focus_text_color',
							'padding',
							'margin',
						),
					),
					'box_shadow'      => array(
						'name'              => 'fields',
						'css'               => array(
							'main' => '%%order_class%% input',
						),
						'default_on_fronts' => array(
							'color'    => '',
							'position' => '',
						),
					),
					'border_styles'   => array(
						'fields'       => array(
							'name'         => 'fields',
							'css'          => array(
								'main'      => array(
									'border_radii'  => '%%order_class%% input, %%order_class%% .quantity input.qty',
									'border_styles' => '%%order_class%% input, %%order_class%% .quantity input.qty',
									'defaults'      => array(
										'border_radii'  => 'on|3px|3px|3px|3px',
										'border_styles' => array(
											'width' => '0px',
											'style' => 'none',
										),
									),
								),
								'important' => 'all',
							),
							'label_prefix' => esc_html__( 'Fields', 'et_builder' ),
						),
						'fields_focus' => array(
							'name'         => 'fields_focus',
							'css'          => array(
								'main'      => array(
									'border_radii'  => '%%order_class%% input:focus, %%order_class%% .quantity input.qty:focus',
									'border_styles' => '%%order_class%% input:focus, %%order_class%% .quantity input.qty:focus',
								),
								'important' => 'all',
							),
							'label_prefix' => esc_html__( 'Fields Focus', 'et_builder' ),
						),
					),
					'font_field'      => array(
						'css'         => array(
							'main'      => array(
								'%%order_class%% input, %%order_class%% .quantity input.qty',
							),
							'hover'     => array(
								'%%order_class%% input:hover',
								'%%order_class%% input:hover::-webkit-input-placeholder',
								'%%order_class%% input:hover::-moz-placeholder',
								'%%order_class%% input:hover:-ms-input-placeholder',
							),
							'important' => 'all',
						),
						'font_size'   => array(
							'default' => '20px',
						),
						'line_height' => array(
							'default' => '1em',
						),
					),
					'margin_padding'  => array(
						'css' => array(
							'main'      => '%%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations tr',
							'important' => array( 'custom_padding' ),
						),
					),
				),
				'dropdown_menus' => array(
					'label'           => esc_html__( 'Dropdown Menus', 'et_builder' ),
					'toggle_priority' => 67,
					'css'             => array(
						'main'                   => '%%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations td select',
						'background_color'       => '%%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations td select',
						'background_color_hover' => '%%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations td select:hover',
						'focus_background_color' => '%%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations td select:focus',
						'form_text_color'        => '%%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations td select',
						'form_text_color_hover'  => '%%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations td select + label:hover, %%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations td select:hover',
						'focus_text_color'       => '%%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations td select option:focus, %%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations td select + label',
						'placeholder_focus'      => '%%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations td select:focus, %%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations td select + label:focus',
						'margin_padding'         => array(
							'css' => array(
								'main'      => '%%order_class%% select',
								'important' => array( 'all' ),
							),
						),
						'important'              => array(
							'text_color',
							'form_text_color',
							'margin_padding',
						),
					),
					'margin_padding'  => array(
						'use_padding' => false,
					),
					'box_shadow'      => array(
						'name' => 'dropdown_menus',
						'css'  => array(
							'main' => '%%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations td select',
						),
					),
					'border_styles'   => array(
						'dropdown_menus' => array(
							'name'         => 'dropdown_menus',
							'css'          => array(
								'main'      => array(
									'border_styles' => '%%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations td select',
									'border_radii'  => '%%order_class%%.et_pb_module .et_pb_module_inner form.cart .variations td select',
								),
								'important' => 'all',
							),
							'label_prefix' => esc_html__( 'Dropdown Menus', 'et_builder' ),
							'use_radius'   => false,
						),
					),
					'font_field'      => array(
						'css'              => array(
							'main'      => array(
								'%%order_class%% select',
							),
							'hover'     => array(
								'%%order_class%% select:hover',
							),
							'important' => 'all',
						),
						'font_size'        => array(
							'default' => '12px',
						),
						'hide_line_height' => true,
						'hide_text_align'  => true,
					),
				),
			),
		);

		$this->custom_css_fields = array(
			'fields'         => array(
				'label'    => esc_html__( 'Fields', 'et_builder' ),
				'selector' => 'input',
			),
			'dropdown_menus' => array(
				'label'    => esc_html__( 'Dropdown Menus', 'et_builder' ),
				'selector' => 'select',
			),
			'buttons'        => array(
				'label'    => esc_html__( 'Buttons', 'et_builder' ),
				'selector' => '.button',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => '7X03vBPYJ1o',
				'name' => esc_html__( 'Divi WooCommerce Modules', 'et_builder' ),
			),
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_fields() {
		$fields = array(
			'product'              => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product',
				array(
					'default'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_product_default(),
					'computed_affects' => array(
						'__add_to_cart',
					),
				)
			),
			'product_filter'       => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product_filter',
				array(
					'computed_affects' => array(
						'__add_to_cart',
					),
				)
			),
			'show_quantity'        => array(
				'label'           => esc_html__( 'Show Quantity Field', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'default'         => 'on',
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Here you can choose whether the quantity field should be added before the Add to Cart button.', 'et_builder' ),
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'show_stock'           => array(
				'label'           => esc_html__( 'Show Stock', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'default'         => 'on',
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Here you can choose whether the stock (displayed when product inventory is managed) should be visible or not', 'et_builder' ),
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'field_label_position' => array(
				'label'           => esc_html__( 'Fields Label Position', 'et_builder' ),
				'description'     => esc_html__( 'Set the position of the field labels.', 'et_builder' ),
				'type'            => 'select',
				'option_category' => 'configuration',
				'options'         => array(
					'inline'  => __( 'Inline', 'et_builder' ),
					'stacked' => __( 'Stacked', 'et_builder' ),
				),
				'default'         => 'default',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'field_label',
				'mobile_options'  => true,
				'priority'        => 15,
			),
			'__add_to_cart'        => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Add_To_Cart',
					'get_add_to_cart',
				),
				'computed_depends_on' => array(
					'product',
					'product_filter',
				),
				'computed_minimum'    => array(
					'product',
				),
			),
		);

		return $fields;
	}

	/**
	 * Replaces the Add to Cart form's action.
	 *
	 * @since 4.14.0
	 *
	 * @param string $permalink Permalink.
	 *
	 * @return string
	 */
	public static function replace_add_to_cart_form_action( $permalink ) {
		$the_id = et_core_page_resource_get_the_ID();
		if ( 0 === absint( et_core_page_resource_get_the_ID() ) ) {
			return $permalink;
		}

		$link = get_permalink( $the_id );

		// Validate if Post exists.
		return $link ? $link : $permalink;
	}

	/**
	 * Get add to cart markup as string
	 *
	 * @since 4.14.0 Update Add to Cart Form action {@see https://github.com/elegantthemes/Divi/issues/16682}
	 *
	 * @since 4.4.0 Fixed compatibility w/ WooCommerce Product Add-ons
	 * @see   https://github.com/elegantthemes/Divi/issues/19116
	 *
	 * @param array $args             Arguments from Computed Prop AJAX call.
	 * @param array $conditional_tags Conditional Tags.
	 *
	 * @return string
	 */
	public static function get_add_to_cart( $args = array(), $conditional_tags = array() ) {
		$is_tb            = 'true' === et_()->array_get( $conditional_tags, 'is_tb', 'false' );
		$is_bfb           = 'true' === et_()->array_get( $conditional_tags, 'is_bfb', 'false' );
		$is_bfb_activated = 'true' === et_()->array_get( $conditional_tags, 'is_bfb_activated', 'false' );
		$is_builder       = $is_tb || $is_bfb || $is_bfb_activated || is_et_pb_preview();

		if ( ! $is_builder ) {
			add_filter(
				'woocommerce_add_to_cart_form_action',
				array(
					'ET_Builder_Module_Woocommerce_Add_To_Cart',
					// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- This is a function call.
					'replace_add_to_cart_form_action'
				)
			);
		}

		$output = et_builder_wc_render_module_template(
			'woocommerce_template_single_add_to_cart',
			$args,
			array( 'product', 'post' )
		);

		if ( ! $is_builder ) {
			remove_filter(
				'woocommerce_add_to_cart_form_action',
				array(
					'ET_Builder_Module_Woocommerce_Add_To_Cart',
					// phpcs:ignore WordPress.Arrays.CommaAfterArrayItem.NoComma -- This is a function call.
					'replace_add_to_cart_form_action'
				)
			);
		}

		return $output;
	}

	/**
	 * Gets the Button classname.
	 *
	 * @used-by ET_Builder_Module_Helper_Woocommerce_Modules::add_custom_button_icons()
	 *
	 * @return string
	 */
	public function get_button_classname() {
		return 'single_add_to_cart_button';
	}

	/**
	 * Adds Multi view attributes to the Outer wrapper.
	 *
	 * Since we do not have control over the WooCommerce Breadcrumb markup, we inject Multi view
	 * attributes on to the Outer wrapper.
	 *
	 * @param array $outer_wrapper_attrs
	 *
	 * @return array
	 */
	public function add_multi_view_attrs( $outer_wrapper_attrs ) {
		$multi_view = et_pb_multi_view_options( $this );

		$multi_view_attrs = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_hide_input_quantity'           => array(
						'show_quantity' => 'off',
					),
					'et_pb_hide_stock'                    => array(
						'show_stock' => 'off',
					),
					'et_pb_fields_label_position_inline'  => array(
						'field_label_position' => 'inline',
					),
					'et_pb_fields_label_position_stacked' => array(
						'field_label_position' => 'stacked',
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
	 * Calculates any required additional CSS.
	 *
	 * Dropdown menu's Bottom & Left margin affects the Dropdown arrow placement.
	 * This is handled using additional CSS.
	 *
	 * @param array  $attrs
	 * @param string $render_slug
	 *
	 * @since 4.3.4
	 */
	public function add_additional_css( $attrs, $render_slug ) {
		if ( ! is_array( $attrs ) || empty( $attrs ) ) {
			return;
		}

		$prop = 'dropdown_menus_custom_margin';

		$values           = et_pb_responsive_options()->get_property_values( $attrs, $prop );
		$hover_value      = et_pb_hover_options()->get_value( $prop, $attrs, '' );
		$processed_values = array();

		foreach ( $values as $device => $value ) {
			if ( empty( $value ) ) {
				continue;
			}

			$processed_values[ $device ] = $this->calculate_dropdown_arrow_margin( $value );
		}

		// Generate style for desktop, tablet, and phone.
		et_pb_responsive_options()->declare_responsive_css(
			$processed_values,
			'%%order_class%% form.cart .variations td.value span:after',
			$render_slug
		);
	}

	/**
	 * Calculates Dropdown's arrow margin values.
	 *
	 * The Dropdown's arrow margin values depend on the actual
	 * Dropdown margin values.
	 *
	 * @since 4.3.4
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public function calculate_dropdown_arrow_margin( $value ) {
		$dropdown_margin        = explode( '|', $value );
		$dropdown_bottom_margin = empty( $dropdown_margin[2] ) ? '0px' : $dropdown_margin[2];
		$dropdown_left_margin   = empty( $dropdown_margin[3] ) ? '0px' : $dropdown_margin[3];

		$declarations = array(
			sprintf( 'margin-top: calc( 3px - %s )', $dropdown_bottom_margin ),
			sprintf( 'right: calc( 10px - %s )', $dropdown_left_margin ),
		);

		// The last declaration wouldn't have the `;`. So appending manually.
		return implode( ';', $declarations ) . ';';
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
		$multi_view             = et_pb_multi_view_options( $this );
		$use_focus_border_color = $this->props['use_focus_border_color'];

		// Module classnames.
		if ( 'on' !== $multi_view->get_value( 'show_quantity' ) ) {
			$this->add_classname( 'et_pb_hide_input_quantity' );
		}

		if ( 'on' !== $multi_view->get_value( 'show_stock' ) ) {
			$this->add_classname( 'et_pb_hide_stock' );
		}

		if ( 'on' === $use_focus_border_color ) {
			$this->add_classname( 'et_pb_with_focus_border' );
		}

		$fields_label_position = et_()->array_get( $this->props, 'field_label_position', 'inline' );
		$this->add_classname( "et_pb_fields_label_position_{$fields_label_position}" );

		ET_Builder_Module_Helper_Woocommerce_Modules::process_background_layout_data( $render_slug, $this );
		ET_Builder_Module_Helper_Woocommerce_Modules::process_custom_button_icons( $render_slug, $this );

		$this->add_classname( $this->get_text_orientation_classname() );

		$this->add_additional_css( $this->props, $render_slug );

		add_filter( "et_builder_module_{$render_slug}_outer_wrapper_attrs", array( $this, 'add_multi_view_attrs' ) );

		$output = self::get_add_to_cart( $this->props );

		// Render empty string if no output is generated to avoid unwanted vertical space.
		if ( '' === $output ) {
			return '';
		}

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Add_To_Cart();
