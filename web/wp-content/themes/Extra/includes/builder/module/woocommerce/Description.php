<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Description class
 *
 * The ET_Builder_Module_Woocommerce_Description Class is responsible for rendering the
 * Description markup using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Description component.
 */
class ET_Builder_Module_Woocommerce_Description extends ET_Builder_Module {
	/**
	 * Initialize.
	 *
	 * @since 4.0.6 Updated `toggle_slug` to avoid empty Tabs in Text OG.
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Product Description', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Product Description', 'et_builder' );
		$this->slug        = 'et_pb_wc_description';
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
					'body'   => array(
						'title'             => et_builder_i18n( 'Text' ),
						'priority'          => 45,
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
						'sub_toggles'       => array(
							'p'     => array(
								'name' => 'P',
								'icon' => 'text-left',
							),
							'a'     => array(
								'name' => 'A',
								'icon' => 'text-link',
							),
							'ul'    => array(
								'name' => 'UL',
								'icon' => 'list',
							),
							'ol'    => array(
								'name' => 'OL',
								'icon' => 'numbered-list',
							),
							'quote' => array(
								'name' => 'QUOTE',
								'icon' => 'text-quote',
							),
						),
					),
					'header' => array(
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
					'width'  => array(
						'title'    => et_builder_i18n( 'Sizing' ),
						'priority' => 65,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'body'     => array(
					'label'           => et_builder_i18n( 'Text' ),
					'css'             => array(
						'line_height' => '%%order_class%% p',
						'color'       => '%%order_class%%.et_pb_wc_description',
					),
					'line_height'     => array(
						'default' => floatval( et_get_option( 'body_font_height', '1.7' ) ) . 'em',
					),
					'font_size'       => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'toggle_slug'     => 'body',
					'sub_toggle'      => 'p',
					'hide_text_align' => true,
				),
				'link'     => array(
					'label'           => et_builder_i18n( 'Link' ),
					'css'             => array(
						'main'  => '%%order_class%% a',
						'color' => '%%order_class%%.et_pb_wc_description a',
					),
					'line_height'     => array(
						'default' => '1em',
					),
					'font_size'       => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'toggle_slug'     => 'body',
					'sub_toggle'      => 'a',
					'hide_text_align' => true,
				),
				'ul'       => array(
					'label'       => esc_html__( 'Unordered List', 'et_builder' ),
					'css'         => array(
						'main'        => '%%order_class%% ul',
						'color'       => '%%order_class%%.et_pb_wc_description ul',
						'line_height' => '%%order_class%% ul li',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'toggle_slug' => 'body',
					'sub_toggle'  => 'ul',
				),
				'ol'       => array(
					'label'       => esc_html__( 'Ordered List', 'et_builder' ),
					'css'         => array(
						'main'        => '%%order_class%% ol',
						'color'       => '%%order_class%%.et_pb_wc_description ol',
						'line_height' => '%%order_class%% ol li',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'toggle_slug' => 'body',
					'sub_toggle'  => 'ol',
				),
				'quote'    => array(
					'label'       => esc_html__( 'Blockquote', 'et_builder' ),
					'css'         => array(
						'main'  => '%%order_class%% blockquote',
						'color' => '%%order_class%%.et_pb_wc_description blockquote',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'toggle_slug' => 'body',
					'sub_toggle'  => 'quote',
				),
				'header'   => array(
					'label'       => esc_html__( 'Heading', 'et_builder' ),
					'css'         => array(
						'main' => '%%order_class%% h1',
					),
					'font_size'   => array(
						'default' => absint( et_get_option( 'body_header_size', '30' ) ) . 'px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h1',
				),
				'header_2' => array(
					'label'       => esc_html__( 'Heading 2', 'et_builder' ),
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
					'sub_toggle'  => 'h2',
				),
				'header_3' => array(
					'label'       => esc_html__( 'Heading 3', 'et_builder' ),
					'css'         => array(
						'main' => '%%order_class%% h3',
					),
					'font_size'   => array(
						'default' => '22px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h3',
				),
				'header_4' => array(
					'label'       => esc_html__( 'Heading 4', 'et_builder' ),
					'css'         => array(
						'main' => '%%order_class%% h4',
					),
					'font_size'   => array(
						'default' => '18px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h4',
				),
				'header_5' => array(
					'label'       => esc_html__( 'Heading 5', 'et_builder' ),
					'css'         => array(
						'main' => '%%order_class%% h5',
					),
					'font_size'   => array(
						'default' => '16px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h5',
				),
				'header_6' => array(
					'label'       => esc_html__( 'Heading 6', 'et_builder' ),
					'css'         => array(
						'main' => '%%order_class%% h6',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '1em',
					),
					'toggle_slug' => 'header',
					'sub_toggle'  => 'h6',
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
				'sub_toggle'            => 'p',
				'toggle_slug'           => 'body',
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
			'product'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product',
				array(
					'default'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_product_default(),
					'computed_affects' => array(
						'__description',
					),
				)
			),
			'product_filter'   => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product_filter',
				array(
					'computed_affects' => array(
						'__description',
					),
				)
			),
			'description_type' => array(
				'label'            => esc_html__( 'Description Type', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'description'       => esc_html__( 'Description', 'et_builder' ),
					'short_description' => esc_html__( 'Short Description', 'et_builder' ),
				),
				'toggle_slug'      => 'main_content',
				'description'      => esc_html__( 'Here you can choose between Description and short description to display.', 'et_builder' ),
				'default_on_front' => 'short_description',
				'mobile_options'   => true,
				'hover'            => 'tabs',
				'computed_affects' => array(
					'__description',
				),
			),
			'__description'    => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Description',
					'get_description',
				),
				'computed_depends_on' => array(
					'product',
					'product_filter',
					'description_type',
				),
				'computed_minimum'    => array(
					'product',
				),
			),
		);

		return $fields;
	}

	/**
	 * Gets the Description
	 *
	 * @since 3.29
	 *
	 * @param array $args Additional arguments.
	 *
	 * @return string
	 */
	public static function get_description( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$defaults = array(
			'product'          => 'current',
			'description_type' => 'short_description',
		);
		$args     = wp_parse_args( $args, $defaults );

		// Theme builder's description placeholder; short-circuit is cleaner and more efficient than
		// global object element being modified. NOTE: $conditional_tags element value is string
		if ( et_builder_tb_enabled() || 'true' === et_()->array_get( $conditional_tags, 'is_tb', false ) || is_et_pb_preview() ) {
			$placeholders = et_theme_builder_wc_placeholders();

			$description = 'short_description' === $args['description_type'] ?
				$placeholders['short_description'] :
				$placeholders['description'];

			// Description comes from Post Content or Excerpt or Custom Field which is processed by WP and should be properly escaped during save.
			return et_core_intentionally_unescaped( $description, 'html' );
		}

		$post_id = ET_Builder_Module_Helper_Woocommerce_Modules::get_product_id( $args['product'] );
		$post    = get_post( $post_id );

		if ( ! ( $post instanceof WP_Post ) ) {
			return '';
		}

		if ( 'description' === $args['description_type'] ) {
			// If builder is not used on given post, display post content.
			if ( ! et_pb_is_pagebuilder_used( $post_id ) ) {
				/** This filter is documented in wp-includes/post-template.php */
				$description = apply_filters( 'the_content', $post->post_content );
			} else {
				$description = get_post_meta( $post->ID, ET_BUILDER_WC_PRODUCT_LONG_DESC_META_KEY, true );

				// Cannot use `the_content` filter since it adds content wrapper.
				// Content wrapper added at
				// `includes/builder/core.php`::et_builder_add_builder_content_wrapper()
				// This filter is documented at
				// includes/builder/feature/woocommerce-modules.php
				$description = apply_filters( 'et_builder_wc_description', $description );
			}
		} else {
			$description = apply_filters( 'woocommerce_short_description', $post->post_excerpt );
		}

		// Description comes from Post Content or Excerpt or Custom Field which is processed by WP and should be properly escaped during save.
		return et_core_intentionally_unescaped( $description, 'html' );
	}

	/**
	 * Adds Multi view attributes to the Outer wrapper.
	 *
	 * Since we do not have control over the WooCommerce Breadcrumb markup, we inject Multi view
	 * attributes on to the Outer wrapper.
	 *
	 * @param array                                     $outer_wrapper_attrs
	 * @param ET_Builder_Module_Woocommerce_Description $this_class
	 *
	 * @return array
	 */
	public function add_multi_view_attrs( $outer_wrapper_attrs, $this_class ) {
		$multi_view = et_pb_multi_view_options( $this_class );

		$contexts = array(
			'content' => '{{description_type}}',
			'target'  => '%%order_class%% .et_pb_module_inner',
		);

		$multi_view_attrs = $multi_view->render_attrs( $contexts, false, null, true );

		if ( $multi_view_attrs && is_array( $multi_view_attrs ) ) {
			$outer_wrapper_attrs = array_merge( $outer_wrapper_attrs, $multi_view_attrs );
		}

		return $outer_wrapper_attrs;
	}

	/**
	 * Filter multi view value.
	 *
	 * @see   ET_Builder_Module_Helper_MultiViewOptions::filter_value
	 *
	 * @param mixed                                     $raw_value    Props raw value.
	 * @param array                                     $args         {
	 *                                                                Context data.
	 *
	 * @type string                                     $context      Context param: content,
	 *       attrs, visibility, classes.
	 * @type string                                     $name         Module options props name.
	 * @type string                                     $mode         Current data mode: desktop,
	 *       hover, tablet, phone.
	 * @type string                                     $attr_key     Attribute key for attrs
	 *       context data. Example: src, class, etc.
	 * @type string                                     $attr_sub_key Attribute sub key that
	 *       availabe when passing attrs value as array such as styes. Example: padding-top,
	 *       margin-botton, etc.
	 * }
	 *
	 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view   Multiview object instance.
	 *
	 * @return mixed
	 */
	public function multi_view_filter_value( $raw_value, $args, $multi_view ) {
		if ( empty( $multi_view->get_module_props() ) ) {
			return $raw_value;
		}

		$maybe_product = et_()->array_get( $multi_view->get_module_props(), 'product', '0' );
		$product       = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $maybe_product );

		if ( ! $product ) {
			return $raw_value;
		}

		$name = et_()->array_get( $args, 'name', '' );
		$mode = et_()->array_get( $args, 'mode', '' );
		$post = get_post( $product->get_id() );

		// Validating $post validates $post_id. No separate $post_id validation is required.
		if ( 'description_type' !== $name || ! $post ) {
			return $raw_value;
		}

		if ( 'description' === $multi_view->get_inherit_value( $name, $mode ) ) {
			if ( ! et_pb_is_pagebuilder_used( $product->get_id() ) ) {
				$raw_value = $post->post_content;
			} else {
				$raw_value = get_post_meta( $post->ID, ET_BUILDER_WC_PRODUCT_LONG_DESC_META_KEY, true );
			}
		} else {
			$raw_value = $post->post_excerpt;
		}

		return $raw_value;
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

		$output = self::get_description( $this->props );

		// Render empty string if no output is generated to avoid unwanted vertical space.
		if ( '' === $output ) {
			return '';
		}

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Description();
