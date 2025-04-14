<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Additional_Info class
 *
 * The ET_Builder_Module_Woocommerce_Additional_Info Class is responsible for rendering the
 * Additional markup using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Additional Info component.
 */
class ET_Builder_Module_Woocommerce_Additional_Info extends ET_Builder_Module {
	/**
	 * Initialize.
	 *
	 * @since 4.0.6 Implemented Attribute Row, Title and Body Custom CSS fields.
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Product Information', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Product Information', 'et_builder' );
		$this->slug        = 'et_pb_wc_additional_info';
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
					'text'       => array(
						'title'             => et_builder_i18n( 'Text' ),
						'priority'          => 45,
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
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
					),
					'header'     => array(
						'title' => esc_html__( 'Title Text', 'et_builder' ),
					),
					'table'      => array(
						'title'    => esc_html__( 'Table', 'et_builder' ),
						'priority' => 70,
					),
					'table_row'  => array(
						'title'    => esc_html__( 'Table Row', 'et_builder' ),
						'priority' => 75,
					),
					'table_cell' => array(
						'title'    => esc_html__( 'Table Cell', 'et_builder' ),
						'priority' => 80,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'body'      => array(
					'label'           => et_builder_i18n( 'Text' ),
					'css'             => array(
						'main'      => '%%order_class%% td',
						'important' => array( 'line-height' ),
					),
					'font_size'       => array(
						'default' => '14px',
					),
					'line_height'     => array(
						'default' => '1.5em',
					),
					'toggle_slug'     => 'text',
					'sub_toggle'      => 'p',
					'font'            => array(
						'default' => '||on||||||',
					),
					'hide_text_align' => true,
				),
				'link'      => array(
					'label'           => et_builder_i18n( 'Link' ),
					'css'             => array(
						'main' => '%%order_class%% a',
					),
					'font_size'       => array(
						'default' => '14px',
					),
					'line_height'     => array(
						'default' => '1.5em',
					),
					'toggle_slug'     => 'text',
					'sub_toggle'      => 'a',
					'hide_text_align' => true,
				),
				'header'    => array(
					'label'       => et_builder_i18n( 'Title' ),
					'css'         => array(
						'main' => '%%order_class%% h2',
					),
					'font_size'   => array(
						'default' => '26px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
				),
				'attribute' => array(
					'label'       => esc_html__( 'Attribute', 'et_builder' ),
					'css'         => array(
						'main'       => '%%order_class%% th',
						'important'  => 'all',
						'text_align' => '%%order_class%% th, %%order_class%% td',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.5em',
					),
					'font'        => array(
						'default' => '|700|||||||',
					),
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
				'css'                   => array(
					'text_orientation' => '%%order_class%%',
				),
				'use_background_layout' => true,
				'sub_toggle'            => 'p',
				'options'               => array(
					'text_orientation'  => array(
						'default' => 'left',
					),
					'background_layout' => array(
						'default' => 'light',
						'hover'   => 'tabs',
					),
				),
			),
			'text_shadow'    => array(
				// Don't add text-shadow fields since they already are via font-options.
				'default' => false,
			),
			'button'         => false,
			'form_field'     => array(
				'table'      => array(
					'label'                  => esc_html__( 'Table', 'et_builder' ),
					'css'                    => array(
						'main' => '%%order_class%% table.shop_attributes',
					),
					'font_field'             => false,
					'margin_padding'         => array(
						'css'         => array(
							'main' => '%%order_class%% table.shop_attributes',
						),
						'use_padding' => false,
					),
					'text_color'             => false,
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'border_styles'          => array(
						'table'      => array(
							'label_prefix'      => 'Table',
							'css'               => array(
								'main' => array(
									'border_styles' => '%%order_class%% table.shop_attributes',
									'border_radii'  => '%%order_class%% table.shop_attributes',
								),
							),
							'use_focus_borders' => false,
							'defaults'          => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '0px',
									'style' => 'dotted',
								),
								'composite'     => array(
									'border_top' => array(
										'border_width_top' => '1px',
									),
								),
							),
						),
						'box_shadow' => array(
							'css' => array(
								'main' => '%%order_class%% table.shop_attributes',
							),
						),
					),
					'table_row'              => array(
						'label'                  => esc_html__( 'Table Row', 'et_builder' ),
						'css'                    => array(
							'main' => '%%order_class%% table.shop_attributes tr',
						),
						'font_field'             => false,
						'margin_padding'         => array(
							'css'        => array(
								'main' => '%%order_class%% table.shop_attributes tr th, %%order_class%% table.shop_attributes tr td',
							),
							'use_margin' => false,
						),
						'text_color'             => false,
						'focus_background_color' => false,
						'focus_text_color'       => false,
						'border_styles'          => array(
							'table_row' => array(
								'label_prefix'      => 'Table Row',
								'css'               => array(
									'main'      => array(
										// Accepts only string and not array. Hence using `implode`.
										'border_radii'  => implode(
											', ',
											array(
												'%%order_class%% table.shop_attributes th',
												'%%order_class%% table.shop_attributes td',
											)
										),
										'border_styles' => implode(
											', ',
											array(
												'%%order_class%% table.shop_attributes th',
												'%%order_class%% table.shop_attributes td',
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
										'style' => 'dotted',
									),
								),
							),
						),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => '%%order_class%% table.shop_attributes tr',
						),
					),
				),
				'table_row'  => array(
					'label'                  => esc_html__( 'Table Row', 'et_builder' ),
					'css'                    => array(
						'main' => '%%order_class%% table.shop_attributes tr',
					),
					'font_field'             => false,
					'margin_padding'         => array(
						'css'        => array(
							'main' => '%%order_class%% table.shop_attributes tr th, %%order_class%% table.shop_attributes tr td',
						),
						'use_margin' => false,
					),
					'text_color'             => false,
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'border_styles'          => array(
						'table_row' => array(
							'label_prefix'      => 'Table Row',
							'css'               => array(
								'main' => array(
									// Accepts only string and not array. Hence using `implode`.
									'border_radii'  => implode(
										', ',
										array(
											'%%order_class%% table.shop_attributes th',
											'%%order_class%% table.shop_attributes td',
										)
									),
									'border_styles' => implode(
										', ',
										array(
											'%%order_class%% table.shop_attributes th',
											'%%order_class%% table.shop_attributes td',
										)
									),
								),
							),
							'use_focus_borders' => false,
							'defaults'          => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '1px',
									'style' => 'dotted',
								),
							),
						),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => '%%order_class%% table.shop_attributes tr',
						),
					),
				),
				'table_cell' => array(
					'label'                  => esc_html__( 'Table Cell', 'et_builder' ),
					'css'                    => array(
						'main' => '%%order_class%% table.shop_attributes tr th, %%order_class%% table.shop_attributes tr td',
					),
					'font_field'             => false,
					'margin_padding'         => array(
						'css'        => array(
							'main' => implode(
								', ',
								array(
									'%%order_class%% table.shop_attributes tr th',
									'%%order_class%% table.shop_attributes tr td',
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
									'border_styles' => '%%order_class%% table.shop_attributes tr th,%%order_class%% table.shop_attributes tr td',
									'border_radii'  => '%%order_class%% table.shop_attributes tr th, %%order_class%% table.shop_attributes tr td',
								),
							),
							'use_focus_borders' => false,
							'defaults'          => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '1px',
									'style' => 'dotted',
								),
								'composite'     => array(
									'border_top' => array(
										'border_width_top' => '1px',
									),
								),
							),
						),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => '%%order_class%% table.shop_attributes tr th, %%order_class%% table.shop_attributes td',
						),
					),
				),
			),
		);

		$this->custom_css_fields = array(
			'title_text'      => array(
				'label'    => esc_html__( 'Title Text', 'et_builder' ),
				'selector' => 'h2',
			),
			'content_area'    => array(
				'label'    => esc_html__( 'Content Area', 'et_builder' ),
				'selector' => '.shop_attributes',
			),
			'attribute_row'   => array(
				'label'    => esc_html__( 'Attribute Row', 'et_builder' ),
				'selector' => '.shop_attributes .woocommerce-product-attributes-item',
			),
			'attribute_title' => array(
				'label'    => esc_html__( 'Attribute Title', 'et_builder' ),
				'selector' => '.shop_attributes .woocommerce-product-attributes-item__label',
			),
			'attribute_text'  => array(
				'label'    => esc_html__( 'Attribute Body', 'et_builder' ),
				'selector' => '.shop_attributes .woocommerce-product-attributes-item__value',
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
			'product'           => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product',
				array(
					'default'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_product_default(),
					'computed_affects' => array(
						'__additional_info',
					),
				)
			),
			'product_filter'    => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product_filter',
				array(
					'computed_affects' => array(
						'__additional_info',
					),
				)
			),
			'show_title'        => array(
				'label'            => esc_html__( 'Show Title', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Here you can choose to display the title.', 'et_builder' ),
				'computed_affects' => array(
					'__additional_info',
				),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'__additional_info' => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Additional_Info',
					'get_additional_info',
				),
				'computed_depends_on' => array(
					'product',
					'product_filter',
					'show_title',
				),
				'computed_minimum'    => array(
					'product',
					'show_title',
				),
			),
		);

		return $fields;
	}

	/**
	 * Get additional information
	 *
	 * @param array $args Additional arguments.
	 *
	 * @return string
	 */
	public static function get_additional_info( $args = array() ) {
		$defaults      = array(
			'show_title' => 'on',
		);
		$args          = wp_parse_args( $args, $defaults );
		$display_title = 'on' === $args['show_title'];

		/*
		 * WooCommerce's default additional information template conditionally display heading
		 * markup based on filterable value which can be plugged and unplugged here.
		 */
		if ( ! $display_title ) {
			add_filter( 'woocommerce_product_additional_information_heading', '__return_false' );
		}

		$additional_info = et_builder_wc_render_module_template(
			'woocommerce_product_additional_information_tab',
			$args
		);

		if ( ! $display_title ) {
			remove_filter( 'woocommerce_product_additional_information_heading', '__return_false' );
		}

		return $additional_info;
	}

	/**
	 * Adds Multi view attributes to the Outer wrapper.
	 *
	 * Since we do not have control over the WooCommerce Additional Info markup, we inject Multi
	 * view attributes on to the Outer wrapper.
	 *
	 * @param array                                         $outer_wrapper_attrs
	 * @param ET_Builder_Module_Woocommerce_Additional_Info $this_class
	 *
	 * @return array
	 */
	public function add_multi_view_attrs( $outer_wrapper_attrs, $this_class ) {
		$multi_view = et_pb_multi_view_options( $this_class );

		$multi_view_attrs = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_hide_title' => array(
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
		ET_Builder_Module_Helper_Woocommerce_Modules::process_background_layout_data( $render_slug, $this );

		$this->add_classname( $this->get_text_orientation_classname() );

		add_filter( "et_builder_module_{$render_slug}_outer_wrapper_attrs", array( $this, 'add_multi_view_attrs' ), 10, 2 );

		$table_row_bg_color = et_()->array_get( $this->props, 'table_row_background_color', '' );

		if ( ! empty( $table_row_bg_color ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => implode(
						',',
						array(
							'%%order_class%% table.shop_attributes tr:nth-child(even) th',
							'%%order_class%% table.shop_attributes tr:nth-child(even) td',
						)
					),
					'declaration' => 'background: inherit',
				)
			);
		}

		$output = self::get_additional_info( $this->props );

		// Render empty string if no output is generated to avoid unwanted vertical space.
		if ( '' === $output ) {
			return '';
		}

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Additional_Info();
