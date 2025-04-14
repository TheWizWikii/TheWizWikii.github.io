<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Title class
 *
 * The ET_Builder_Module_Woocommerce_Title Class is responsible for rendering the
 * Title markup using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Title component.
 */
class ET_Builder_Module_Woocommerce_Title extends ET_Builder_Module {
	/**
	 * Initialize.
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Product Title', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Product Title', 'et_builder' );
		$this->slug        = 'et_pb_wc_title';
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
					'header' => array(
						'title'    => esc_html__( 'Title Text', 'et_builder' ),
						'priority' => 49,
					),
					'width'  => array(
						'title'    => et_builder_i18n( 'Sizing' ),
						'priority' => 65,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'header' => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main' => '%%order_class%% h1, %%order_class%% h2, %%order_class%% h3, %%order_class%% h4, %%order_class%% h5, %%order_class%% h6',
					),
					'header_level' => array(
						'default' => 'h1',
					),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'header',
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
				'use_background_layout' => true,
				'use_text_orientation'  => false,
				'css'                   => array(
					'text_shadow' => '%%order_class%% .et_pb_wc_title',
				),
				'options'               => array(
					'background_layout' => array(
						'default_on_front' => 'light',
						'hover'            => 'tabs',
					),
				),
				'toggle_slug'           => 'header',
			),
			'text_shadow'    => array(
				// Don't add text-shadow fields since they already are via font-options.
				'default' => false,
			),
			'button'         => false,
		);

		$this->custom_css_fields = array(
			'title_text' => array(
				'label'    => esc_html__( 'Title Text', 'et_builder' ),
				'selector' => '%%order_class%% h1, %%order_class%% h2, %%order_class%% h3, %%order_class%% h4, %%order_class%% h5, %%order_class%% h6',
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
						'__title',
					),
				)
			),
			'product_filter' => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product_filter',
				array(
					'computed_affects' => array(
						'__title',
					),
				)
			),
			'__title'        => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Title',
					'get_title',
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
	 * Gets the Title.
	 *
	 * @param array $args Additional arguments.
	 *
	 * @return string
	 */
	public static function get_title( $args = array() ) {
		if ( et_builder_tb_enabled() ) {
			return esc_html( 'Product Name', 'et_builder' );
		}

		$defaults = array(
			'product' => 'current',
		);
		$args     = wp_parse_args( $args, $defaults );
		$title    = et_builder_wc_render_module_template(
			'the_title',
			$args,
			array( 'post', 'product' )
		);

		return $title;
	}

	/**
	 * Gets the WooCommerce Product Title markup.
	 *
	 * @return string
	 */
	protected function get_title_markup() {
		$header_level  = $this->props['header_level'];
		$product_title = self::get_title( $this->props );

		return sprintf(
			'
		<%1$s>%2$s</%1$s>',
			et_pb_process_header_level( $header_level, 'h1' ),
			et_core_esc_previously( $product_title )
		);
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

		$output = self::get_title_markup();

		// Render empty string if no output is generated to avoid unwanted vertical space.
		if ( '' === $output ) {
			return '';
		}

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Title();
