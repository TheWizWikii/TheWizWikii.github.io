<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Price class
 *
 * The ET_Builder_Module_Woocommerce_Price Class is responsible for rendering the
 * Price markup using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Price component.
 */
class ET_Builder_Module_Woocommerce_Price extends ET_Builder_Module {
	/**
	 * Initialize.
	 */
	public function init() {
		$this->name   = esc_html__( 'Woo Product Price', 'et_builder' );
		$this->plural = esc_html__( 'Woo Product Price', 'et_builder' );

		// Use `et_pb_wc_{module}` for all WooCommerce modules.
		$this->slug        = 'et_pb_wc_price';
		$this->vb_support  = 'on';
		$this->folder_name = 'et_pb_woo_modules';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Content' ),
				),
			),
			'advanced' => array(
				'toggles' => array(

					/*
					 * Manually define `text` to avoid `Text Text` toggle defined by advanced
					 * field font which automatically append ` Text` by default.
					 */
					'text' => array(
						'title'    => esc_html__( 'Price Text', 'et_builder' ),
						'priority' => 45,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'body'              => array(
					'label'           => esc_html__( 'Price', 'et_builder' ),
					'css'             => array(
						'main'      => '%%order_class%% .price',
						'important' => array( 'size' ),
					),
					'line_height'     => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.7' ) ) . 'em',
					),
					'font_size'       => array(
						'default' => '26px',
					),
					'hide_text_align' => true,
				),
				'before_sale_price' => array(
					'label'           => esc_html__( 'Sale Old Price', 'et_builder' ),
					'css'             => array(
						'main' => '%%order_class%% .price del',
					),
					'line_height'     => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.7' ) ) . 'em',
					),
					'font_size'       => array(
						'default' => '26px',
					),
					'hide_text_align' => true,
				),
				'sale_price'        => array(
					'label'           => esc_html__( 'Sale New Price', 'et_builder' ),
					'css'             => array(
						'main'      => '%%order_class%% .price ins',
						'important' => array( 'font' ),
					),
					'line_height'     => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.7' ) ) . 'em',
					),
					'font'            => array(
						'default' => '|700|||||||',
					),
					'font_size'       => array(
						'default' => '26px',
					),
					'hide_text_align' => true,
				),
			),
			'background'     => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'margin_padding' => array(
				'css' => array(

					/*
					 * Module has default margin-bottom which adapts to column it currently in
					 * thus, stronger selector needed for module's custom styling to overwrite it.
					 */
					'margin'    => '.et_pb_row .et_pb_column div%%order_class%%',
					'important' => 'all',
				),
			),
			'text'           => array(
				'css'         => array(
					'text_shadow' => '%%order_class%%',
				),
				'options'     => array(
					'background_layout' => array(
						'default' => 'light',
						'hover'   => 'tabs',
					),
				),

				// Assign to main font advanced field (body), keeping things simplified
				'toggle_slug' => 'body',
			),
			'text_shadow'    => array(
				// Don't add text-shadow fields since they already are via font-options
				'default' => false,
			),
			'button'         => false,
		);

		$this->custom_css_fields = array(
			'text'              => array(
				'label'    => esc_html__( 'Price', 'et_builder' ),
				'selector' => '.price',
			),
			'before_sale_price' => array(
				'label'    => esc_html__( 'Sale Old Price', 'et_builder' ),
				'selector' => '.price del',
			),
			'sale_price'        => array(
				'label'    => esc_html__( 'Sale New Price', 'et_builder' ),
				'selector' => '.price ins',
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
						'__price',
					),
				)
			),
			'product_filter' => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product_filter',
				array(
					'computed_affects' => array(
						'__price',
					),
				)
			),
			'__price'        => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Price',
					'get_price',
				),
				'computed_depends_on' => array(
					'product',
					'product_filter',
				),
				'computed_minumum'    => array(
					'product',
				),
			),
		);

		return $fields;
	}

	/**
	 * Get price data
	 *
	 * @since 3.29
	 *
	 * @param array $args             Arguments from Computed Prop AJAX call.
	 * @param array $conditional_tags Conditional Tags.
	 * @param array $current_page     Current page args.
	 *
	 * @return string
	 */
	public static function get_price( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		return et_builder_wc_render_module_template( 'woocommerce_template_single_price', $args );
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
		$this->add_classname( $this->get_text_orientation_classname() );

		$output = self::get_price( $this->props );

		// Render empty string if no output or no price value are generated
		// to avoid unwanted vertical space.
		if ( '' === trim( $output ) || '<p class="price"></p>' === trim( $output ) ) {
			return '';
		}

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Price();
