<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Stock class
 *
 * The ET_Builder_Module_Woocommerce_Stock Class is responsible for rendering the
 * Stock markup using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Stock component.
 */
class ET_Builder_Module_Woocommerce_Stock extends ET_Builder_Module {
	/**
	 * Initialize.
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Product Stock', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Product Stock', 'et_builder' );
		$this->slug        = 'et_pb_wc_stock';
		$this->vb_support  = 'on';
		$this->folder_name = 'et_pb_woo_modules';

		$this->settings_modal_toggles = array(
			'general' => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Content' ),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'instock'     => array(
					'label'       => esc_html__( 'In Stock', 'et_builder' ),
					'css'         => array(
						'main'        => '%%order_class%% .et_pb_module_inner .stock.in-stock',
						'plugin_main' => '%%order_class%% .in-stock, %%order_class%% .in-stock a, %%order_class%% .in-stock span',
					),
					'font_size'   => array(
						'default' => '13px',
					),
					'line_height' => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.7' ) ) . 'em',
					),
				),
				'outofstock'  => array(
					'label'       => esc_html__( 'Out Of Stock', 'et_builder' ),
					'css'         => array(
						'main'        => '%%order_class%% .et_pb_module_inner .stock.out-of-stock',
						'plugin_main' => '%%order_class%% .out-of-stock, %%order_class%% .out-of-stock a, %%order_class%% .out-of-stock span',
					),
					'font_size'   => array(
						'default' => '13px',
					),
					'line_height' => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.7' ) ) . 'em',
					),
				),
				'onbackorder' => array(
					'label'       => esc_html__( 'On Backorder', 'et_builder' ),
					'css'         => array(
						'main'        => '%%order_class%% .available-on-backorder',
						'plugin_main' => '%%order_class%% .available-on-backorder, %%order_class%% .available-on-backorder a, %%order_class%% .available-on-backorder span',
					),
					'font_size'   => array(
						'default' => '13px',
					),
					'line_height' => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.7' ) ) . 'em',
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
			'text'           => array(),
			'text_shadow'    => array(
				// Don't add text-shadow fields since they already are via font-options.
				'default' => false,
			),
			'button'         => false,
		);

		$this->custom_css_fields = array(
			'instock_text'    => array(
				'label'    => esc_html__( 'In Stock Text', 'et_builder' ),
				'selector' => '.in-stock',
			),
			'outofstock_text' => array(
				'label'    => esc_html__( 'Out of Stock Text', 'et_builder' ),
				'selector' => '.out-of-stock',
			),
			'backorder_text'  => array(
				'label'    => esc_html__( 'On Backorder Text', 'et_builder' ),
				'selector' => '.available-on-backorder',
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
			'product'        => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product',
				array(
					'default'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_product_default(),
					'computed_affects' => array(
						'__stock',
					),
				)
			),
			'product_filter' => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product_filter',
				array(
					'computed_affects' => array(
						'__stock',
					),
				)
			),
			'__stock'        => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Stock',
					'get_stock',
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
	 * Get stock output
	 *
	 * @since 3.29
	 *
	 * @param array $args Additional arguments.
	 *
	 * @return string
	 */
	public static function get_stock( $args = array() ) {
		$defaults = array(
			'product' => 'current',
		);
		$args     = wp_parse_args( $args, $defaults );
		$rating   = et_builder_wc_render_module_template(
			'wc_get_stock_html',
			$args
		);

		return $rating;
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

		$output = self::get_stock( $this->props );

		// Render empty string if no output is generated to avoid unwanted vertical space.
		if ( '' === $output ) {
			return '';
		}

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Stock();
