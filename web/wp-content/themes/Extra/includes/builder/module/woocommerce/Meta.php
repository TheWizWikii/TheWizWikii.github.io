<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Meta class
 *
 * The ET_Builder_Module_Woocommerce_Meta Class is responsible for rendering the
 * Meta markup using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Meta component.
 */
class ET_Builder_Module_Woocommerce_Meta extends ET_Builder_Module {
	/**
	 * Initialize.
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Product Meta', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Product Meta', 'et_builder' );
		$this->slug        = 'et_pb_wc_meta';
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
					'layout' => et_builder_i18n( 'Layout' ),
					'body'   => array(
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
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'body' => array(
					'label'       => esc_html__( 'Meta', 'et_builder' ),
					'css'         => array(
						'main' => '%%order_class%% .product_meta, %%order_class%% .product_meta a',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1.7em',
					),
					'sub_toggle'  => 'p',
					'toggle_slug' => 'body',
				),
				'link' => array(
					'label'           => et_builder_i18n( 'Link' ),
					'css'             => array(
						'main' => '%%order_class%% div.product_meta a',
					),
					'font_size'       => array(
						'default' => '14px',
					),
					'line_height'     => array(
						'default' => '1.7em',
					),
					'toggle_slug'     => 'body',
					'sub_toggle'      => 'a',
					'hide_text_align' => true,
				),
			),
			'background'     => array(
				'settings' => array(
					'color' => 'alpha',
				),
				'css'      => array(
					'main' => '%%order_class%% .product_meta',
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main'      => '%%order_class%% .product_meta',
					'important' => 'all',
				),
			),
			'text'           => array(
				'use_text_orientation' => false,
			),
			'text_shadow'    => array(
				// Don't add text-shadow fields since they already are via font-options.
				'default' => false,
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main' => '%%order_class%% .product_meta',
					),
				),
			),
			'button'         => false,

			'borders'        => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .product_meta',
							'border_styles' => '%%order_class%% .product_meta',
						),
					),
					'defaults' => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#dadada',
							'style' => 'solid',
						),
						'composite'     => array(
							'border_top' => array(
								'border_width_top' => '1px',
								'border_color_top' => '#dadada',
							),
						),
					),
				),
			),
			'height'         => array(
				'css' => array(
					'main' => '%%order_class%% .product_meta',
				),
			),
		);

		$this->custom_css_fields = array(
			'meta_text' => array(
				'label'    => esc_html__( 'Meta Text', 'et_builder' ),
				'selector' => '.product_meta, .product_meta a',
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
			'product'         => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product',
				array(
					'default'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_product_default(),
					'computed_affects' => array(
						'__meta',
					),
				)
			),
			'product_filter'  => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product_filter',
				array(
					'computed_affects' => array(
						'__meta',
					),
				)
			),
			'separator'       => array(
				'label'           => esc_html__( 'Separator', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can set the separator.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'default'         => '/',
				'show_if'         => array(
					'meta_layout' => 'inline',
				),
			),
			'show_sku'        => array(
				'label'            => esc_html__( 'Show SKU', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Here you can choose whether the SKU should be added.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_categories' => array(
				'label'            => esc_html__( 'Show Categories', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Here you can choose whether the Categories should be added.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_tags'       => array(
				'label'            => esc_html__( 'Show Tags', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Here you can choose whether the Tags should be added.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'meta_layout'     => array(
				'label'            => esc_html__( 'Meta Layout', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'inline'  => esc_html__( 'Inline', 'et_builder' ),
					'stacked' => esc_html__( 'Stacked', 'et_builder' ),
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
				'description'      => esc_html__( 'Here you can choose how to position the product meta.', 'et_builder' ),
				'default_on_front' => 'inline',
				'affects'          => array(
					'separator',
				),
			),
			'__meta'          => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Meta',
					'get_meta',
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
	 * Get meta output
	 *
	 * @since 3.29
	 *
	 * @param array $args Additional arguments.
	 *
	 * @return string
	 */
	public static function get_meta( $args = array() ) {
		$defaults = array(
			'product' => 'current',
		);
		$args     = wp_parse_args( $args, $defaults );
		$meta     = et_builder_wc_render_module_template(
			'woocommerce_template_single_meta',
			$args
		);

		return $meta;
	}

	/**
	 * Adds Multi view attributes to the Outer wrapper.
	 *
	 * Since we do not have control over the WooCommerce Breadcrumb markup, we inject Multi view
	 * attributes on to the Outer wrapper.
	 *
	 * @param array                              $outer_wrapper_attrs
	 * @param ET_Builder_Module_Woocommerce_Meta $this_class
	 *
	 * @return array
	 */
	public function add_multi_view_attrs( $outer_wrapper_attrs, $this_class ) {
		$multi_view = et_pb_multi_view_options( $this_class );

		$multi_view_attrs = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_wc_no_sku'        => array(
						'show_sku' => 'off',
					),
					'et_pb_wc_no_categories' => array(
						'show_categories' => 'off',
					),
					'et_pb_wc_no_tags'       => array(
						'show_tags' => 'off',
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
		$multi_view = et_pb_multi_view_options( $this );
		ET_Builder_Module_Helper_Woocommerce_Modules::process_background_layout_data( $render_slug, $this );

		$this->add_classname( $this->get_text_orientation_classname() );

		// Module classnames.
		if ( 'on' !== $multi_view->get_value( 'show_sku' ) ) {
			$this->add_classname( 'et_pb_wc_no_sku' );
		}

		if ( 'on' !== $multi_view->get_value( 'show_categories' ) ) {
			$this->add_classname( 'et_pb_wc_no_categories' );
		}

		if ( 'on' !== $multi_view->get_value( 'show_tags' ) ) {
			$this->add_classname( 'et_pb_wc_no_tags' );
		}

		$this->add_classname( esc_attr( 'et_pb_wc_meta_layout_' . $this->props['meta_layout'] ) );

		/*
		 * Append separator via css pseudo selector so meta module can reuse default WooCommerce
		 * template (default WooCommerce meta template has no separator option)
		 */
		ET_Builder_Element::set_style(
			$render_slug,
			array(
				'selector'    => array(
					'%%order_class%%:not(.et_pb_wc_no_categories).et_pb_wc_meta_layout_inline .sku_wrapper:after',
					'%%order_class%%:not(.et_pb_wc_no_tags).et_pb_wc_meta_layout_inline .sku_wrapper:after',
					'%%order_class%%:not(.et_pb_wc_no_tags).et_pb_wc_meta_layout_inline .posted_in:after',
				),
				'declaration' => 'content: " ' . esc_html(
					ET_Builder_Module_Helper_Woocommerce_Modules::escape_special_chars(
						$this->props['separator']
					)
				) . ' "',
			)
		);

		add_filter( "et_builder_module_{$render_slug}_outer_wrapper_attrs", array( $this, 'add_multi_view_attrs' ), 10, 2 );

		$output = self::get_meta( $this->props );

		// Render empty string if no output is generated to avoid unwanted vertical space.
		if ( '' === $output ) {
			return '';
		}

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Meta();
