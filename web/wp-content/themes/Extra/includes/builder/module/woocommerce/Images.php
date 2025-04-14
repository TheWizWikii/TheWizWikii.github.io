<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Images class
 *
 * The ET_Builder_Module_Woocommerce_Images Class is responsible for rendering the
 * Image markup using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Images component.
 */
class ET_Builder_Module_Woocommerce_Images extends ET_Builder_Module {
	/**
	 * Initialize.
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Product Images', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Product Images', 'et_builder' );
		$this->slug        = 'et_pb_wc_images';
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
					'image' => et_builder_i18n( 'Image' ),
					'sale_badge' => esc_html__( 'Sale Badge Text', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'        => array(
				'default'    => array(),
				'image'      => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%% div.images ol.flex-control-thumbs.flex-control-nav li, %%order_class%% .flex-viewport, %%order_class%% .woocommerce-product-gallery--without-images .woocommerce-product-gallery__wrapper, %%order_class%% .woocommerce-product-gallery > div:not(.flex-viewport) .woocommerce-product-gallery__image, %%order_class%% .woocommerce-product-gallery > .woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image, %%order_class%% .woocommerce-product-gallery .woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image',
							'border_styles' => '%%order_class%% div.images ol.flex-control-thumbs.flex-control-nav li, %%order_class%% .flex-viewport, %%order_class%% .woocommerce-product-gallery--without-images .woocommerce-product-gallery__wrapper, %%order_class%% .woocommerce-product-gallery > div:not(.flex-viewport) .woocommerce-product-gallery__image, %%order_class%% .woocommerce-product-gallery > .woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image',
						),
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
				'default'    => array(),
				'image'      => array(
					'label'           => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category' => 'layout',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'css'             => array(
						'main'      => '%%order_class%% div.images ol.flex-control-thumbs.flex-control-nav li, %%order_class%% .flex-viewport, %%order_class%% .woocommerce-product-gallery--without-images .woocommerce-product-gallery__wrapper, %%order_class%% .woocommerce-product-gallery > div:not(.flex-viewport) .woocommerce-product-gallery__image, %%order_class%% .woocommerce-product-gallery > .woocommerce-product-gallery__wrapper .woocommerce-product-gallery__image',
						'overlay'   => 'inset',
						'important' => true,
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
			'background'     => array(),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'text_shadow'    => array(),
			'text'           => false,
			'fonts'          => array(
				'sale_badge' => array(
					'label'           => esc_html__( 'Sale Badge', 'et_builder' ),
					'css'             => array(
						'main'      => '%%order_class%% .et_pb_module_inner span.onsale',
						'important' => 'all',
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
						'__images',
					),
				)
			),
			'product_filter'       => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product_filter',
				array(
					'computed_affects' => array(
						'__images',
					),
				)
			),
			'show_product_image'   => array(
				'label'            => esc_html__( 'Show Featured Image', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Here you can choose whether product image should be displayed or not.', 'et_builder' ),
				'computed_affects' => array(
					'__images',
				),
			),
			'show_product_gallery' => array(
				'label'            => esc_html__( 'Show Gallery Images', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Here you can choose whether product gallery should be displayed or not.', 'et_builder' ),
				'computed_affects' => array(
					'__images',
				),
			),
			'show_sale_badge'      => array(
				'label'            => esc_html__( 'Show Sale Badge', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Here you can choose whether Sale Badge should be displayed or not.', 'et_builder' ),
				'computed_affects' => array(
					'__images',
				),
			),
			'sale_badge_color'     => array(
				'label'          => esc_html__( 'Sale Badge Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the sales bade that appears on products that are on sale.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'sale_badge',
				'hover'          => 'tabs',
				'sticky'         => true,
				'mobile_options' => true,
			),
			'__images'             => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Images',
					'get_images',
				),
				'computed_depends_on' => array(
					'product',
					'product_filter',
					'show_product_image',
					'show_product_gallery',
					'show_sale_badge',
				),
				'computed_minimum'    => array(
					'product',
				),
			),
			'force_fullwidth'      => array(
				'label'           => esc_html__( 'Force Fullwidth', 'et_builder' ),
				'description'     => esc_html__( "When enabled, this will force your image to extend 100% of the width of the column it's in.", 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default'         => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'image',
				'affects'         => array(
					'max_width',
					'width',
				),
			),
		);

		return $fields;
	}

	/**
	 * Get images output
	 *
	 * @since 4.12.0 Set $defaults['overwrite'] to array( 'product', 'post' )
	 * @since 3.29
	 *
	 * @param array $args Additional args.
	 *
	 * @return string
	 */
	public static function get_images( $args = array() ) {
		/*
		 * YITH Badge Management plugin executes only when
		 * did_action( 'woocommerce_product_thumbnails' ) returns FALSE.
		 *
		 * The above wouldn't be the case when multiple Woo Images modules
		 * are placed on the same page.
		 * Hence the workaround is to reset the 'woocommerce_product_thumbnails' action.
		 *
		 * @link https://github.com/elegantthemes/Divi/issues/18530
		 */
		global $wp_actions;

		$tag   = 'woocommerce_product_thumbnails';
		$reset = false;
		$value = 0;

		if ( isset( $wp_actions[ $tag ] ) ) {
			$value = $wp_actions[ $tag ];
			$reset = true;
			unset( $wp_actions[ $tag ] );
		}

		$defaults = array(
			'product'              => 'current',
			'show_product_image'   => 'on',
			'show_product_gallery' => 'on',
			'show_sale_badge'      => 'on',
		);
		$args     = wp_parse_args( $args, $defaults );

		$images = et_builder_wc_render_module_template(
			'woocommerce_show_product_images',
			$args,
			array( 'product', 'post' )
		);

		/*
		 * Reset changes made for YITH Badge Management plugin.
		 *
		 * That way we won't bleed creating new issues.
		 *
		 * @link https://github.com/elegantthemes/Divi/issues/18530
		 */
		if ( $reset && ! isset( $wp_actions[ $tag ] ) ) {
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Fix compatibility.
			$wp_actions[ $tag ] = $value;
		}

		return $images;
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

		$sale_badge_color_hover  = $this->get_hover_value( 'sale_badge_color' );
		$sale_badge_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'sale_badge_color' );
		$force_fullwidth         = et_()->array_get( $this->props, 'force_fullwidth', 'off' );
		$show_product_image      = et_()->array_get( $this->props, 'show_product_image', 'off' );

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

		// Image force fullwidth.
		if ( 'on' === $force_fullwidth ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .woocommerce-product-gallery__image img',
					'declaration' => 'width: 100%;',
				)
			);
		}

		// Toggle featured image.
		if ( 'off' === $show_product_image ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% .woocommerce-product-gallery__image--placeholder img[src*="woocommerce-placeholder"]',
					'declaration' => 'visibility: hidden;',
				)
			);
		}

		$output = self::get_images( $this->props );

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Images();
