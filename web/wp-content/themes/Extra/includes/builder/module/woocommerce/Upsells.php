<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Upsells class
 *
 * The ET_Builder_Module_Woocommerce_Upsells Class is responsible for rendering the
 * Upsells markup using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Upsells component.
 */
class ET_Builder_Module_Woocommerce_Upsells extends ET_Builder_Module {
	/**
	 * Holds Prop values across static methods.
	 *
	 * @var array
	 */
	public static $static_props;

	/**
	 * Number of products to be offset.
	 *
	 * @var int Default 0.
	 */
	public static $offset = 0;

	/**
	 * Initialize.
	 */
	public function init() {
		$this->name   = esc_html__( 'Woo Product Upsell', 'et_builder' );
		$this->plural = esc_html__( 'Woo Product Upsell', 'et_builder' );

		// Use `et_pb_wc_{module}` for all WooCommerce modules.
		$this->slug        = 'et_pb_wc_upsells';
		$this->vb_support  = 'on';
		$this->folder_name = 'et_pb_woo_modules';

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Content' ),
					'elements'     => et_builder_i18n( 'Elements' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'overlay'    => et_builder_i18n( 'Overlay' ),
					'image'      => et_builder_i18n( 'Image' ),
					// Avoid Text suffix by manually defining the `star` toggle slug.
					'star'       => esc_html__( 'Star Rating', 'et_builder' ),
					'sale_badge' => esc_html__( 'Sale Badge Text', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'title'         => array(
					'label'       => et_builder_i18n( 'Title' ),
					'css'         => array(
						'main'      => '%%order_class%% section.products > h1, %%order_class%% section.products > h2, %%order_class%% section.products > h3, %%order_class%% section.products > h4, %%order_class%% section.products > h5, %%order_class%% section.products > h6',
						'important' => 'all',
					),
					'font_size'   => array(
						'default' => '26px',
					),
					'line_height' => array(
						'default' => '1',
					),
				),
				'rating'        => array(
					'label'            => esc_html__( 'Star Rating', 'et_builder' ),
					'css'              => array(
						'main'                 => '%%order_class%% ul.products li.product .star-rating',
						'color'                => '%%order_class%% li.product .star-rating > span:before',
						'letter_spacing_hover' => '%%order_class%% ul.products li.product:hover .star-rating',
					),
					'font_size'        => array(
						'default' => '14px',
						'label'   => esc_html__( 'Star Rating Size', 'et_builder' ),
					),
					'hide_font'        => true,
					'hide_line_height' => true,
					'hide_text_shadow' => true,
					'text_align'       => array(
						'label' => esc_html__( 'Star Rating Alignment', 'et_builder' ),
					),
					'text_color'       => array(
						'label' => esc_html__( 'Star Rating Color', 'et_builder' ),
					),
					'toggle_slug'      => 'star',
				),
				'product_title' => array(
					'label'       => esc_html__( 'Product Title', 'et_builder' ),
					'css'         => array(
						'main'      => "{$this->main_css_element} ul.products li.product h3, {$this->main_css_element} ul.products li.product h1, {$this->main_css_element} ul.products li.product h2, {$this->main_css_element} ul.products li.product h4, {$this->main_css_element} ul.products li.product h5, {$this->main_css_element} ul.products li.product h6",
						'important' => 'all',
					),
					'font_size'   => array(
						'default' => '1em',
					),
					'line_height' => array(
						'default' => '1',
					),
				),
				'price'         => array(
					'label'       => esc_html__( 'Price', 'et_builder' ),
					'css'         => array(
						'main' => "{$this->main_css_element} ul.products li.product .price, {$this->main_css_element} ul.products li.product .price .amount",
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
						'default'        => floatval( et_get_option( 'body_font_height', '1.7' ) ) . 'em',
					),
				),
				'sale_badge'    => array(
					'label'           => esc_html__( 'Sale Badge', 'et_builder' ),
					'css'             => array(
						'main'      => "{$this->main_css_element} ul.products li.product .onsale",
						'important' => array( 'line-height', 'font', 'text-shadow' ),
					),
					'hide_text_align' => true,
					'line_height'     => array(
						'default' => '1.7em',
					),
					'font_size'       => array(
						'default' => '20px',
					),
					'letter_spacing'  => array(
						'default' => '0px',
					),
				),
				'sale_price'    => array(
					'label'           => esc_html__( 'Sale Price', 'et_builder' ),
					'css'             => array(
						'main' => "{$this->main_css_element} ul.products li.product .price ins .amount",
					),
					'hide_text_align' => true,
					'font'            => array(
						'default' => '|700|||||||',
					),
					'font_size'       => array(
						'default' => '14px',
					),
					'line_height'     => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
						'default'        => '1.7em',
					),
				),
			),
			'borders'        => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => '%%order_class%%.et_pb_wc_upsells .product',
							'border_styles' => '%%order_class%%.et_pb_wc_upsells .product',
						),
					),
				),
				'image'   => array(
					'css'          => array(
						'main'      => array(
							'border_radii'  => '%%order_class%%.et_pb_module .et_shop_image',
							'border_styles' => '%%order_class%%.et_pb_module .et_shop_image',
						),
						'important' => 'all',
					),
					'label_prefix' => et_builder_i18n( 'Image' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
				),
				'sale_badge' => array(
					'css'          => array(
						'main'      => array(
							'border_radii'  => '%%order_class%% span.onsale',
							'border_styles' => '%%order_class%% span.onsale',
						),
						'important' => true,
					),
					'defaults'     => array(
						'border_radii' => 'on|3px|3px|3px|3px',
					),
					'label_prefix' => esc_html__( 'Sale Badge', 'et_builder' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'sale_badge',
				),
			),
			'box_shadow'     => array(
				'default'    => array(
					'css' => array(
						'main' => '%%order_class%% .product',
					),
				),
				'image'      => array(
					'label'             => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'image',
					'css'               => array(
						'main'    => '%%order_class%% .et_shop_image',
						'overlay' => 'inset',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
				'sale_badge' => array(
					'label'           => esc_html__( 'Sale Badge Box Shadow', 'et_builder' ),
					'option_category' => 'layout',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'sale_badge',
					'css'             => array(
						'main'      => '%%order_class%% span.onsale',
						'overlay'   => 'inset',
						'important' => true,
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main'      => '%%order_class%%',
					// Needed to overwrite last module margin-bottom styling.
					'important' => array( 'custom_margin' ),
				),
			),
			'text'           => array(
				'css' => array(
					'text_shadow' => implode(
						', ',
						array(
							// Title.
							"{$this->main_css_element} ul.products h3",
							"{$this->main_css_element} ul.products  h1",
							"{$this->main_css_element} ul.products  h2",
							"{$this->main_css_element} ul.products  h4",
							"{$this->main_css_element} ul.products  h5",
							"{$this->main_css_element} ul.products  h6",
							// Price.
							"{$this->main_css_element} ul.products .price",
							"{$this->main_css_element} ul.products .price .amount",

						)
					),
				),
			),
			'filters'        => array(
				'child_filters_target' => array(
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'          => array(
				'css' => array(
					'main' => '%%order_class%% .et_shop_image',
				),
			),
			'button'         => false,
			'form_field'     => array(
				'sale_badge' => array(
					'label'                  => esc_html__( 'Sale Badge', 'et_builder' ),
					'background_color'       => false,
					'text_color'             => false,
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'font_field'             => false,
					'margin_padding'         => array(
						'css'            => array(
							'main'      => '%%order_class%% ul.products li.product span.onsale',
							'important' => array( 'custom_margin', 'custom_padding' ),
						),
						'custom_margin'  => array(
							'default' => '0px|0px|0px|0px|false|false',
						),
						'custom_padding' => array(
							'default' => '6px|18px|6px|18px|false|false',
						),
						'toggle_slug'    => 'sale_badge',
					),
					'border_styles'          => false,
					'box_shadow'             => false,
				),
			),
		);

		$this->custom_css_fields = array(
			'product'   => array(
				'label'    => esc_html__( 'Product', 'et_builder' ),
				'selector' => 'li.product',
			),
			'onsale'    => array(
				'label'    => esc_html__( 'Onsale', 'et_builder' ),
				'selector' => 'li.product .onsale',
			),
			'image'     => array(
				'label'    => et_builder_i18n( 'Image' ),
				'selector' => '.et_shop_image',
			),
			'overlay'   => array(
				'label'    => et_builder_i18n( 'Overlay' ),
				'selector' => '.et_overlay',
			),
			'title'     => array(
				'label'    => et_builder_i18n( 'Title' ),
				'selector' => ET_Builder_Module_Helper_Woocommerce_Modules::get_title_selector(),
			),
			'rating'    => array(
				'label'    => esc_html__( 'Star Rating', 'et_builder' ),
				'selector' => '.star-rating',
			),
			'price'     => array(
				'label'    => esc_html__( 'Price', 'et_builder' ),
				'selector' => 'li.product .price',
			),
			'price_old' => array(
				'label'    => esc_html__( 'Old Price', 'et_builder' ),
				'selector' => 'li.product .price del span.amount',
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
			'product'             => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product',
				array(
					'default'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_product_default(),
					'computed_affects' => array(
						'__upsells',
					),
				)
			),
			'product_filter'      => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product_filter',
				array(
					'computed_affects' => array(
						'__upsells',
					),
				)
			),
			'posts_number'        => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'posts_number',
				array(
					'default'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_columns_posts_default(),
					'computed_affects' => array(
						'__upsells',
					),
				)
			),
			'columns_number'      => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'columns_number',
				array(
					'default'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_columns_posts_default(),
					'computed_affects' => array(
						'__upsells',
					),
				)
			),
			'orderby'             => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'orderby',
				array(
					'options'          => array(
						'default'    => esc_html__( 'Random Order', 'et_builder' ),
						'menu_order' => esc_html__( 'Sort by Menu Order', 'et_builder' ),
						'popularity' => esc_html__( 'Sort By Popularity', 'et_builder' ),
						'date'       => esc_html__( 'Sort By Date: Oldest To Newest', 'et_builder' ),
						'date-desc'  => esc_html__( 'Sort By Date: Newest To Oldest', 'et_builder' ),
						'price'      => esc_html__( 'Sort By Price: Low To High', 'et_builder' ),
						'price-desc' => esc_html__( 'Sort By Price: High To Low', 'et_builder' ),
					),
					'computed_affects' => array(
						'__upsells',
					),
				)
			),
			'sale_badge_color'    => array(
				'label'          => esc_html__( 'Sale Badge Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the sales bade that appears on products that are on sale.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'sale_badge',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'icon_hover_color'    => array(
				'label'          => esc_html__( 'Overlay Icon Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the icon that appears when hovering over a product.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'overlay',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'hover_overlay_color' => array(
				'label'          => esc_html__( 'Overlay Background Color', 'et_builder' ),
				'description'    => esc_html__( 'Here you can define a custom color for the overlay', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'overlay',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'hover_icon'          => array(
				'label'           => esc_html__( 'Overlay Icon', 'et_builder' ),
				'description'     => esc_html__( 'Here you can define a custom icon for the overlay', 'et_builder' ),
				'type'            => 'select_icon',
				'option_category' => 'configuration',
				'class'           => array( 'et-pb-font-icon' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'show_name'           => array(
				'label'            => esc_html__( 'Show Name', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Turn name on or off.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_image'          => array(
				'label'            => esc_html__( 'Show Image', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Turn image on or off.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_price'          => array(
				'label'            => esc_html__( 'Show Price', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Turn price on or off.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_rating'         => array(
				'label'            => esc_html__( 'Show Rating', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Turn rating on or off.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_sale_badge'     => array(
				'label'            => esc_html__( 'Show Sale Badge', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Turn sale badge on or off.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'offset_number'       => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'offset_number',
				array(
					'computed_affects' => array(
						'__upsells',
					),
				)
			),
			'__upsells'           => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Upsells',
					'get_upsells',
				),
				'computed_depends_on' => array(
					'product',
					'product_filter',
					'posts_number',
					'columns_number',
					'orderby',
					'offset_number',
				),
				'computed_minimum'    => array(
					'product',
				),
			),
		);

		return $fields;
	}

	/**
	 * Appends offset to the WP_Query that retrieves Products.
	 *
	 * @since 4.14.0
	 *
	 * @param array $query_args Query args.
	 *
	 * @return array
	 */
	public static function append_offset( $query_args ) {
		if ( ! is_array( $query_args ) ) {
			return $query_args;
		}

		$query_args['offset'] = self::$offset;

		return $query_args;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['rating_letter_spacing'] = array(
			'width'          => '%%order_class%% .star-rating',
			'letter-spacing' => '%%order_class%% .star-rating',
		);

		return $fields;
	}

	/**
	 * Gets the Upsells Products.
	 *
	 * Used as a callback to the __upsells computed prop.
	 *
	 * @param array $args             Arguments from Computed Prop AJAX call.
	 * @param array $conditional_tags Conditional Tags.
	 * @param array $current_page     Current page args.
	 *
	 * @return string
	 */
	public static function get_upsells( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		self::$static_props = $args;
		$offset_number      = et_()->array_get( $args, 'offset_number', 0 );

		// Force set product's class to ET_Theme_Builder_Woocommerce_Product_Variable_Placeholder
		// in TB so related product can outputs visible content based on pre-filled value in TB
		if ( 'true' === et_()->array_get( $conditional_tags, 'is_tb', false ) || is_et_pb_preview() ) {
			// Set upsells id; adjust it with module's arguments. This is specifically needed if
			// the module fetched the value via computed callback due to some fields no longer uses
			// default value
			ET_Theme_Builder_Woocommerce_Product_Variable_Placeholder::set_tb_upsells_ids(
				array(
					'limit' => et_()->array_get( $args, 'posts_number', 4 ),
				)
			);

			add_filter( 'woocommerce_product_class', 'et_theme_builder_wc_product_class' );
		}

		$is_offset_valid = absint( $offset_number ) > 0;
		if ( $is_offset_valid ) {
			self::$offset = $offset_number;

			add_filter(
				'woocommerce_shortcode_products_query',
				array( 'ET_Builder_Module_Woocommerce_Upsells', 'append_offset' )
			);
		}

		add_filter(
			'woocommerce_upsell_display_args',
			array(
				'ET_Builder_Module_Woocommerce_Upsells',
				'set_upsell_display_args',
			)
		);

		if ( isset( $args['orderby'] ) ) {
			$orderby = $args['orderby'];

			if ( in_array( $orderby, array( 'price', 'date' ), true ) ) {
				/*
				 * For the list of all allowed Orderby values, refer
				 *
				 * @see wc_products_array_orderby
				 */
				$args['order'] = 'asc';
			}
		}

		$output = et_builder_wc_render_module_template( 'woocommerce_upsell_display', $args );

		remove_filter(
			'woocommerce_upsell_display_args',
			array( 'ET_Builder_Module_Woocommerce_Upsells', 'set_upsell_display_args' )
		);

		if ( $is_offset_valid ) {
			remove_filter(
				'woocommerce_shortcode_products_query',
				array( 'ET_Builder_Module_Woocommerce_Upsells', 'append_offset' )
			);

			self::$offset = 0;
		}

		return $output;
	}

	/**
	 * Returns the User selected Posts per page, columns and Order by values to WooCommerce.
	 *
	 * @param array $args Documented at
	 *                    {@see woocommerce_upsell_display()}.
	 *
	 * @return array
	 */
	public static function set_upsell_display_args( $args ) {
		$selected_args = self::get_selected_upsell_display_args();

		return wp_parse_args( $selected_args, $args );
	}

	/**
	 * Gets the User set Posts per page, columns and Order by values.
	 *
	 * The static variable used in this method is set by
	 *
	 * @see ET_Builder_Module_Woocommerce_Upsells::get_upsells()
	 *
	 * @return array
	 */
	public static function get_selected_upsell_display_args() {
		$selected_args                   = array();
		$selected_args['posts_per_page'] = et_()->array_get(
			self::$static_props,
			'posts_number',
			''
		);
		$selected_args['columns']        = et_()->array_get(
			self::$static_props,
			'columns_number',
			''
		);
		$selected_args['orderby']        = et_()->array_get(
			self::$static_props,
			'orderby',
			''
		);

		// Set default values when parameters are empty.
		$default = ET_Builder_Module_Helper_Woocommerce_Modules::get_columns_posts_default_value();
		if ( empty( $selected_args['posts_per_page'] ) ) {
			$selected_args['posts_per_page'] = $default;
		}
		if ( empty( $selected_args['columns'] ) ) {
			$selected_args['columns'] = $default;
		}

		$selected_args = array_filter( $selected_args, 'strlen' );

		return $selected_args;
	}

	/**
	 * Adds Multi view attributes to the Outer wrapper.
	 *
	 * Since we do not have control over the WooCommerce Related Products markup, we inject Multi
	 * view attributes on to the Outer wrapper.
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
					'et_pb_wc_upsells_no_name'       => array(
						'show_name' => 'off',
					),
					'et_pb_wc_upsells_no_image'      => array(
						'show_image' => 'off',
					),
					'et_pb_wc_upsells_no_price'      => array(
						'show_price' => 'off',
					),
					'et_pb_wc_upsells_no_rating'     => array(
						'show_rating' => 'off',
					),
					'et_pb_wc_upsells_no_sale_badge' => array(
						'show_sale_badge' => 'off',
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
		ET_Builder_Module_Helper_Woocommerce_Modules::add_star_rating_style(
			$render_slug,
			$this->props,
			'%%order_class%% ul.products li.product .star-rating',
			'%%order_class%% ul.products li.product:hover .star-rating'
		);

		// Sale Badge Color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'sale_badge_color',
				'selector'       => '%%order_class%% span.onsale',
				'css_property'   => 'background-color',
				'important'      => true,
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		// Icon Hover Color.
		$this->generate_styles(
			array(
				'hover'          => false,
				'base_attr_name' => 'icon_hover_color',
				'selector'       => '%%order_class%% .et_overlay:before',
				'css_property'   => 'color',
				'important'      => true,
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		// Hover Overlay Color.
		$this->generate_styles(
			array(
				'hover'          => false,
				'base_attr_name' => 'hover_overlay_color',
				'selector'       => '%%order_class%% .et_overlay',
				'css_property'   => array( 'background-color', 'border-color' ),
				'important'      => true,
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		// Extended Icon Styles.
		$this->generate_styles(
			array(
				'utility_arg'    => 'icon_font_family',
				'render_slug'    => $render_slug,
				'base_attr_name' => 'hover_icon',
				'important'      => true,
				'selector'       => '%%order_class%% .et_overlay:before',
				'processor'      => array(
					'ET_Builder_Module_Helper_Style_Processor',
					'process_extended_icon',
				),
			)
		);

		// Images: Add CSS Filters and Mix Blend Mode rules (if set).
		if ( array_key_exists( 'image', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['image'] ) ) {
			$this->add_classname(
				$this->generate_css_filters(
					$render_slug,
					'child_',
					self::$data_utils->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' )
				)
			);
		}

		$this->add_classname( $this->get_text_orientation_classname() );

		$is_shop                        = function_exists( 'is_shop' ) && is_shop();
		$is_wc_loop_prop_get_set_exists = function_exists( 'wc_get_loop_prop' ) && function_exists( 'wc_set_loop_prop' );
		$is_product_category            = function_exists( 'is_product_category' ) && is_product_category();

		if ( $is_shop ) {
			$display_type = ET_Builder_Module_Helper_Woocommerce_Modules::set_display_type_to_render_only_products( 'woocommerce_shop_page_display' );
		} elseif ( is_product_category() ) {
			$display_type = ET_Builder_Module_Helper_Woocommerce_Modules::set_display_type_to_render_only_products( 'woocommerce_category_archive_display' );
		}

		// Required to handle Customizer preview pane.
		// Refer: https://github.com/elegantthemes/Divi/issues/17998#issuecomment-565955422
		if ( $is_wc_loop_prop_get_set_exists && is_customize_preview() ) {
			$is_filtered = wc_get_loop_prop( 'is_filtered' );
			wc_set_loop_prop( 'is_filtered', true );
		}

		$output = self::get_upsells( $this->props );

		// Required to handle Customizer preview pane.
		// Refer: https://github.com/elegantthemes/Divi/issues/17998#issuecomment-565955422
		if ( $is_wc_loop_prop_get_set_exists && is_customize_preview() && isset( $is_filtered ) ) {
			wc_set_loop_prop( 'is_filtered', $is_filtered );
		}

		if ( $is_shop && isset( $display_type ) ) {
			ET_Builder_Module_Helper_Woocommerce_Modules::reset_display_type( 'woocommerce_shop_page_display', $display_type );
		} elseif ( $is_product_category && isset( $display_type ) ) {
			ET_Builder_Module_Helper_Woocommerce_Modules::reset_display_type( 'woocommerce_category_archive_display', $display_type );
		}

		// Render empty string if no output is generated to avoid unwanted vertical space.
		if ( '' === $output ) {
			return '';
		}

		add_filter(
			"et_builder_module_{$render_slug}_outer_wrapper_attrs",
			array(
				'ET_Builder_Module_Helper_Woocommerce_Modules',
				'output_data_icon_attrs',
			),
			10,
			2
		);

		add_filter(
			"et_builder_module_{$render_slug}_outer_wrapper_attrs",
			array(
				$this,
				'add_multi_view_attrs',
			)
		);

		$output = $this->_render_module_wrapper( $output, $render_slug );

		remove_filter(
			"et_builder_module_{$render_slug}_outer_wrapper_attrs",
			array(
				'ET_Builder_Module_Helper_Woocommerce_Modules',
				'output_data_icon_attrs',
			),
			10
		);

		return $output;
	}
}

new ET_Builder_Module_Woocommerce_Upsells();
