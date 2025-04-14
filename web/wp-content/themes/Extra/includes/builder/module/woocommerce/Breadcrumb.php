<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Breadcrumb class
 *
 * The ET_Builder_Module_Woocommerce_Breadcrumb Class is responsible for rendering the
 * Breadcrumb markup using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Breadcrumb component.
 */
class ET_Builder_Module_Woocommerce_Breadcrumb extends ET_Builder_Module {
	/**
	 * Home URL.
	 *
	 * @var string
	 */
	public static $home_url;

	/**
	 * Initialize.
	 */
	public function init() {
		$this->name             = esc_html__( 'Woo Breadcrumbs', 'et_builder' );
		$this->plural           = esc_html__( 'Woo Breadcrumbs', 'et_builder' );
		$this->slug             = 'et_pb_wc_breadcrumb';
		$this->vb_support       = 'on';
		$this->folder_name      = 'et_pb_woo_modules';
		$this->main_css_element = '%%order_class%% .woocommerce-breadcrumb';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => array(
						'title' => et_builder_i18n( 'Content' ),
					),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => array(
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
					'label'           => et_builder_i18n( 'Text' ),
					'css'             => array(
						'main'       => '%%order_class%%, %%order_class%% .et_pb_module_inner, %%order_class%% .woocommerce-breadcrumb, %%order_class%% .woocommerce-breadcrumb a',
						'text_align' => '%%order_class%%',
					),
					'font_size'       => array(
						'default' => '13px',
					),
					'line_height'     => array(
						'default' => '1.7em',
					),
					'toggle_slug'     => 'text',
					'sub_toggle'      => 'p',
					'hide_text_align' => true,
				),
				'link' => array(
					'label'           => et_builder_i18n( 'Link' ),
					'css'             => array(
						'main' => '%%order_class%%.et_pb_wc_breadcrumb a, %%order_class%%.et_pb_wc_breadcrumb .woocommerce-breadcrumb a',
					),
					'font_size'       => array(
						'default' => '14px',
					),
					'line_height'     => array(
						'default' => '1.7em',
					),
					'toggle_slug'     => 'text',
					'sub_toggle'      => 'a',
					'hide_text_align' => true,
				),
			),
			'background'     => array(
				'css'      => array(
					// Backgrounds need to be applied to module wrapper.
					'main' => '%%order_class%%.et_pb_wc_breadcrumb',
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'margin_padding' => array(
				'css' => array(
					'margin'    => '%%order_class%% .woocommerce-breadcrumb',
					'important' => 'all',
				),
			),
			'text'           => array(
				'use_background_layout' => false,
				'sub_toggle'            => 'p',
				'options'               => array(
					'text_orientation'  => array(
						'default' => 'left',
					),
					'background_layout' => array(
						'default' => 'light',
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
	 * Includes any Module specific fields.
	 *
	 * Fields from Parent module that may be not needed are also removed.
	 *
	 * @since 4.0 Removed Hover options from Breadcrumb URL.
	 *
	 * @return array Parent's fields w/ module specific fields.
	 */
	public function get_fields() {
		// Content Toggle.
		$fields = array(
			'product'              => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product',
				array(
					'default'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_product_default(),
					'computed_affects' => array(
						'__breadcrumb',
					),
				)
			),
			'product_filter'       => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product_filter',
				array(
					'computed_affects' => array(
						'__breadcrumb',
					),
				)
			),
			'breadcrumb_home_text' => array(
				'label'           => esc_html__( 'Home Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can create the breadcrumb text for the Home page.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'default'         => __( 'Home', 'et_builder' ),
				'mobile_options'  => true,
				'hover'           => 'tabs',
				'dynamic_content' => 'text',
			),
			'breadcrumb_home_url'  => array(
				'label'           => esc_html__( 'Home Link', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can create the link for the Home page.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'default'         => get_home_url(),
				'mobile_options'  => true,
				'dynamic_content' => 'url',
			),
			'breadcrumb_separator' => array(
				'label'           => esc_html__( 'Separator', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can set the Breadcrumb separator.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'default'         => ' / ',
				'dynamic_content' => 'text',
			),
			'__breadcrumb'         => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Breadcrumb',
					'get_breadcrumb',
				),
				'computed_depends_on' => array(
					'product',
					'product_filter',
				),
			),
		);

		return $fields;
	}

	/**
	 * Get breadcrumb
	 *
	 * @since 3.29
	 *
	 * @param array $args Additional arguments.
	 *
	 * @return string
	 */
	public static function get_breadcrumb( $args = array() ) {
		global $post, $product, $wp_query;

		$defaults = array(
			'product'              => 'current',
			'breadcrumb_home_text' => __( 'Home', 'et_builder' ),
			'breadcrumb_home_url'  => get_home_url(),
			'breadcrumb_separator' => '/',
		);

		$args = wp_parse_args( $args, $defaults );

		$args['breadcrumb_separator'] = esc_html( $args['breadcrumb_separator'] );

		/*
		 * Replace update-able string in visual builder with text placeholder which can be
		 * easily replaced by builder for quick interaction on field change
		 *
		 * The `et_fb_is_resolve_post_content_callback_ajax()` check is added to enable
		 * Product Breadcrumb dynamic content field.
		 *
		 * Breadcrumb customization is not required when resolving dynamic content field.
		 * Hence we exclude customizations if the AJAX request is to resolve dynamic content fields.
		 */
		$main_query_post_id = ET_Post_Stack::get_main_post_id();
		$layout_post_id     = ET_Builder_Element::get_layout_id();
		$is_fb              = et_core_is_fb_enabled() && $main_query_post_id === $layout_post_id;

		if ( ! et_fb_is_resolve_post_content_callback_ajax() && ( $is_fb || et_fb_is_builder_ajax() || et_fb_is_computed_callback_ajax() || is_et_pb_preview() ) ) {
			$args = wp_parse_args(
				array(
					'breadcrumb_home_text' => '%HOME_TEXT%',
					'breadcrumb_home_url'  => '%HOME_URL%',
					'breadcrumb_separator' => '%SEPARATOR%',
				),
				$args
			);
		}

		// Update home URL which is rendered inside breadcrumb function and pluggable via filter.
		self::$home_url = $args['breadcrumb_home_url'];
		add_filter(
			'woocommerce_breadcrumb_home_url',
			array( 'ET_Builder_Module_Woocommerce_Breadcrumb', 'modify_home_url' )
		);

		$breadcrumb = et_builder_wc_render_module_template(
			'woocommerce_breadcrumb',
			$args,
			array(
				'product',
				'post',
				'wp_query',
			)
		);

		// Reset home URL.
		self::$home_url = get_home_url();
		remove_filter(
			'woocommerce_breadcrumb_home_url',
			array( 'ET_Builder_Module_Woocommerce_Breadcrumb', 'modify_home_url' )
		);

		return $breadcrumb;
	}

	/**
	 * Modify home url
	 *
	 * @since 3.29
	 *
	 * @return string
	 */
	public static function modify_home_url() {
		return self::$home_url;
	}

	/**
	 * Adds Multi view attributes to the Inner wrapper.
	 *
	 * Since we do not have control over the WooCommerce Breadcrumb markup, we inject Multi view
	 * attributes on to the Inner wrapper.
	 *
	 * Inner wrapper is selected to inject the Multi view attributes because, there is already
	 * a lot going on w/ the Outer wrapper.
	 *
	 * @param array $inner_wrapper_attrs
	 *
	 * @return array
	 */
	public function add_multi_view_attrs( $inner_wrapper_attrs ) {
		$multi_view = et_pb_multi_view_options( $this );

		/*
		 * Breadcrumb separator cannot have Multi-view options as it is not enclosed in a HTML tag.
		 * Element being enclose in a tag is essential for the Multi-view options to work.
		 */
		$multi_view_attrs = $multi_view->render_attrs(
			array(
				'content' => '{{breadcrumb_home_text}}',
				'attrs'   => array(
					'href'                      => '{{breadcrumb_home_url}}',
					'data-breadcrumb-separator' => '{{breadcrumb_separator}}',
				),
				'target'  => '%%order_class%% .woocommerce-breadcrumb a:first-child',
			),
			false,
			null,
			true
		);

		if ( $multi_view_attrs && is_array( $multi_view_attrs ) ) {
			$inner_wrapper_attrs = array_merge( $inner_wrapper_attrs, $multi_view_attrs );
		}

		return $inner_wrapper_attrs;
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

		$output = self::get_breadcrumb( $this->props );

		// Render empty string if no output is generated to avoid unwanted vertical space.
		if ( '' === $output ) {
			return '';
		}

		add_filter( "et_builder_module_{$render_slug}_inner_wrapper_attrs", array( $this, 'add_multi_view_attrs' ) );

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Breadcrumb();
