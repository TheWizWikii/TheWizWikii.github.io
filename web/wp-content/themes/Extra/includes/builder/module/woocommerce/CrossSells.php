<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Cross_Sells class
 *
 * The ET_Builder_Module_Woocommerce_Cross_Sells Class is responsible for rendering the
 * Cross sells using the WooCommerce template.
 *
 * @since 4.14.0
 * @package Divi\Builder
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Cart Upsells component.
 *
 * @since 4.14.0
 */
class ET_Builder_Module_Woocommerce_Cross_Sells extends ET_Builder_Module {

	/**
	 * Initialize.
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Cross Sells', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Cross Sells', 'et_builder' );
		$this->slug        = 'et_pb_wc_cross_sells';
		$this->vb_support  = 'on';
		$this->folder_name = 'et_pb_woo_modules';

		$this->main_css_element = '%%order_class%%.et_pb_wc_cross_sells';

		$this->advanced_fields = array(
			'fonts' => array(
				'title' => array(
					'label'       => et_builder_i18n( 'Title' ),
					'css'         => array(
						'main'      => implode(
							',',
							[
								"{$this->main_css_element} ul.products li.product h3",
								"{$this->main_css_element} ul.products li.product h1",
								"{$this->main_css_element} ul.products li.product h2",
								"{$this->main_css_element} ul.products li.product h4",
								"{$this->main_css_element} ul.products li.product h5",
								"{$this->main_css_element} ul.products li.product h6",
							]
						),
						'hover'     => implode(
							',',
							[
								"{$this->main_css_element} .woocommerce ul.products li.product h3:hover",
								"{$this->main_css_element} .woocommerce ul.products li.product h1:hover",
								"{$this->main_css_element} .woocommerce ul.products li.product h2:hover",
								"{$this->main_css_element} .woocommerce ul.products li.product h4:hover",
								"{$this->main_css_element} .woocommerce ul.products li.product h5:hover",
								"{$this->main_css_element} .woocommerce ul.products li.product h6:hover",
							]
						),
						'important' => 'plugin_only',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1em',
					),
				),
				'price' => array(
					'label'       => esc_html__( 'Price', 'et_builder' ),
					'css'         => array(
						'main' => implode(
							',',
							[
								"{$this->main_css_element} ul.products li.product .price",
								"{$this->main_css_element} ul.products li.product .price .amount",
							]
						),
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
						'default'        => '26px',
					),
				),
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
			'__cross_sells' => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Cross_Sells',
					'get_cross_sells',
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
		);

		return $fields;
	}

	/**
	 * Swaps Cross-sells template.
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
				'cart/cross-sells.php',
			),
			true
		);

		if ( $is_template_override ) {
			return trailingslashit( ET_BUILDER_DIR ) . 'feature/woocommerce/templates/' . $template_name;
		}

		return $template;
	}

	/**
	 * Gets Cross sells markup.
	 *
	 * @param array $args             Props.
	 * @param array $conditional_tags Conditional tags set during computed property AJAX call.
	 *
	 * @return string
	 */
	public static function get_cross_sells( $args = array(), $conditional_tags = array() ) {
		if ( is_checkout() ) {
			return;
		}

		if ( ! function_exists( 'woocommerce_cross_sell_display' ) ) {
			return;
		}

		$is_tb = et_()->array_get( $conditional_tags, 'is_tb', false );

		$output = '';

		if ( ( $is_tb || et_fb_is_computed_callback_ajax() || is_et_pb_preview() ) ) {
			$cross_sell_items = array(
				1001 => array(
					'name'  => esc_html__( 'Product 4', 'et_builder' ),
					'price' => '12.00',
				),
				1002 => array(
					'name'  => esc_html__( 'Product 5', 'et_builder' ),
					'price' => '75.00',
				),
				1003 => array(
					'name'  => esc_html__( 'Product 6', 'et_builder' ),
					'price' => '38.00',
				),
			);

			$cross_sell_products = [];

			foreach ( $cross_sell_items as $id => $details ) {
				$product = new ET_Builder_Woocommerce_Product_Simple_Placeholder();
				$product->set_name( $details['name'] );
				$product->set_id( $id );
				$product->set_price( $details['price'] );

				$cross_sell_products[] = $product;
			}

			wc_set_loop_prop( 'name', 'cross-sells' );
			wc_set_loop_prop( 'columns', apply_filters( 'woocommerce_cross_sells_columns', 2 ) );

			$orderby     = apply_filters( 'woocommerce_cross_sells_orderby', 'rand' );
			$order       = apply_filters( 'woocommerce_cross_sells_order', 'desc' );
			$cross_sells = wc_products_array_orderby( $cross_sell_products, $orderby, $order );
			$limit       = apply_filters( 'woocommerce_cross_sells_total', 0 );
			$cross_sells = $limit > 0 ? array_slice( $cross_sells, 0, $limit ) : $cross_sells;

			// Runs only on Builder mode.
			add_filter(
				'wc_get_template',
				[
					'ET_Builder_Module_Woocommerce_Cross_Sells',
					'swap_template',
				],
				10,
				5
			);

			ob_start();
			wc_get_template(
				'cart/cross-sells.php',
				array(
					'cross_sells'    => $cross_sells,

					// Not used now, but used in previous version of up-sells.php.
					'posts_per_page' => $limit,
					'orderby'        => $orderby,
					'columns'        => 2,
				)
			);
			$output = ob_get_clean();

			remove_filter(
				'wc_get_template',
				[
					'ET_Builder_Module_Woocommerce_Cross_Sells',
					'swap_template',
				],
				10,
				5
			);
		} else {
			if ( ! is_null( WC()->cart ) ) {
				ob_start();
				woocommerce_cross_sell_display( 0 );
				$output = ob_get_clean();
			}
		}

		return $output;
	}

	/**
	 * Renders the module output.
	 *
	 * @param array  $attrs       List of attributes.
	 * @param string $content     Content being processed.
	 * @param string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string
	 */
	public function render( $attrs, $content, $render_slug ) {
		// Module classnames.
		$this->add_classname(
			array(
				$this->get_text_orientation_classname(),
			)
		);

		$output = self::get_cross_sells();

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Cross_Sells();
