<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Rating class
 *
 * The ET_Builder_Module_Woocommerce_Rating Class is responsible for rendering the
 * Rating markup using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Rating component.
 */
class ET_Builder_Module_Woocommerce_Rating extends ET_Builder_Module {
	/**
	 * Initialize.
	 *
	 * @since 3.29.2 Added custom margin default.
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Product Rating', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Product Rating', 'et_builder' );
		$this->slug        = 'et_pb_wc_rating';
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
					// Avoid Text suffix by manually defining the `text` toggle slug.
					'text'   => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 45,
					),
					// Avoid Text suffix by manually defining the `star` toggle slug.
					'star'   => esc_html__( 'Star Rating', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'rating' => array(
					'label'            => esc_html__( 'Star Rating', 'et_builder' ),
					'css'              => array(
						'main'       => '%%order_class%% .woocommerce-product-rating .star-rating',
						'color'      => '%%order_class%% .woocommerce-product-rating .star-rating > span:before',
						'text_align' => '%%order_class%% .woocommerce-product-rating',
					),
					'font_size'        => array(
						'default' => '14px',
					),
					'hide_font'        => true,
					'hide_line_height' => true,
					'hide_text_shadow' => true,
					'text_align'       => array(
						'label' => esc_html__( 'Star Rating Alignment', 'et_builder' ),
					),
					'font_size'        => array(
						'label' => esc_html__( 'Star Rating Size', 'et_builder' ),
					),
					'text_color'       => array(
						'label' => esc_html__( 'Star Rating Color', 'et_builder' ),
					),
					'toggle_slug'      => 'star',
				),
				'body'   => array(
					'label'           => et_builder_i18n( 'Text' ),
					'css'             => array(
						'main' => '%%order_class%% a.woocommerce-review-link',
					),
					'font_size'       => array(
						'default' => '14px',
					),
					'line_height'     => array(
						'default' => '1.7em',
					),
					'hide_text_align' => true,

					/*
					 * Manually assign `text` toggle to avoid `Text Text` toggle defined by advanced
					 * field font which automatically append ` Text` by default.
					 */
					'toggle_slug'     => 'text',
				),
			),
			'background'     => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'margin_padding' => array(
				'css'           => array(
					'important' => 'all',
				),
				'custom_margin' => array(
					'default' => '0em|0em|1.618em|0em|false|false',
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
			'text'             => array(
				'label'    => et_builder_i18n( 'Text' ),
				'selector' => 'a.woocommerce-review-link',
			),
			'star_rating_text' => array(
				'label'    => esc_html__( 'Star Rating', 'et_builder' ),
				'selector' => '.woocommerce-product-rating .star-rating',
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
						'__rating',
					),
				)
			),
			'product_filter'    => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product_filter',
				array(
					'computed_affects' => array(
						'__rating',
					),
				)
			),
			'show_rating'       => array(
				'label'            => esc_html__( 'Show Star Rating', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Here you can choose whether the star rating should be added.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_reviews_link' => array(
				'label'            => esc_html__( 'Show Customer Reviews Count', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Here you can choose whether the custom reviews link should be added.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'layout'            => array(
				'label'            => esc_html__( 'Rating Layout', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'inline'  => esc_html__( 'Inline', 'et_builder' ),
					'stacked' => esc_html__( 'Stacked', 'et_builder' ),
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
				'description'      => esc_html__( 'Here you can choose where to place the reviews link.', 'et_builder' ),
				'default_on_front' => 'inline',
			),
			'__rating'          => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Rating',
					'get_rating',
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
	 * Get rating output
	 *
	 * @param array $args Additional arguments.
	 *
	 * @return string
	 */
	public static function get_rating( $args = array() ) {
		$defaults = array(
			'product' => 'current',
		);
		$args     = wp_parse_args( $args, $defaults );

		if ( 'current' !== $args['product'] ) {
			// Enable comments via filter to render the reviews link.
			add_filter( 'comments_open', '__return_true' );
		}

		$rating = et_builder_wc_render_module_template(
			'woocommerce_template_single_rating',
			$args,
			array( 'product', 'wp_query' )
		);

		if ( 'current' !== $args['product'] ) {
			// Remove filter after module is rendered.
			remove_filter( 'comments_open', '__return_true' );
		}

		return $rating;
	}

	/**
	 * Adds Multi view attributes to the Outer wrapper.
	 *
	 * Since we do not have control over the WooCommerce Rating markup, we inject Multi view
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
					'et_pb_wc_rating_no_rating'  => array(
						'show_rating' => 'off',
					),
					'et_pb_wc_rating_no_reviews' => array(
						'show_reviews_link' => 'off',
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
			'%%order_class%% .star-rating',
			'%%order_class%%:hover .star-rating',
			array( 'rating_letter_spacing' )
		);

		$this->add_classname( $this->get_text_orientation_classname() );

		// Add classes to hide disabled elements.
		if ( 'on' !== $this->props['show_rating'] ) {
			$this->add_classname( 'et_pb_wc_rating_no_rating' );
		}

		if ( 'on' !== $this->props['show_reviews_link'] ) {
			$this->add_classname( 'et_pb_wc_rating_no_reviews' );
		}

		if ( ! empty( $this->props['layout'] ) ) {
			$this->add_classname( "et_pb_wc_rating_layout_{$this->props['layout']}" );
		}

		add_filter( "et_builder_module_{$render_slug}_outer_wrapper_attrs", array( $this, 'add_multi_view_attrs' ) );

		$output = self::get_rating( $this->props );

		// Render empty string if no output is generated to avoid unwanted vertical space.
		if ( '' === $output ) {
			return '';
		}

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Rating();
