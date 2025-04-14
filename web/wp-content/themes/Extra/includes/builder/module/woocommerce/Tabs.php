<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Tabs class
 *
 * The ET_Builder_Module_Woocommerce_Tabs Class is responsible for rendering the
 * Tabs markup using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ET_Builder_Module_Tabs' ) ) {
	require_once ET_BUILDER_DIR_RESOLVED_PATH . '/module/Tabs.php';
}

/**
 * Class representing WooCommerce Add to cart component.
 *
 * @since 3.29
 */
class ET_Builder_Module_Woocommerce_Tabs extends ET_Builder_Module_Tabs {
	/**
	 * Holds WooCommerce Tabs data rendered by self::get_tabs().
	 *
	 * @var array
	 */
	public $rendered_tabs_data = [];

	/**
	 * Define WooCommerce Tabs property.
	 */
	public function init() {
		// Inherit tabs module property.
		parent::init();

		// Define WooCommerce Tabs module property; overwriting inherited property.
		$this->name        = esc_html__( 'Woo Product Tabs', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Product Tabs', 'et_builder' );
		$this->slug        = 'et_pb_wc_tabs';
		$this->folder_name = 'et_pb_woo_modules';

		/*
		 * Set property for holding rendering data so the data rendering via
		 * ET_Builder_Module_Woocommerce_Tabs::get_tabs() is only need to be done once.
		 */
		$this->rendered_tabs_data = array();

		// Remove module item.
		$this->child_slug      = null;
		$this->child_item_text = null;

		// Set WooCommerce Tabs specific toggle / options group.
		$this->settings_modal_toggles['general']['toggles']['main_content'] = array(
			'title'    => et_builder_i18n( 'Content' ),
			'priority' => 10,
		);

		$this->advanced_fields['fonts']['tab']['font_size']   = array(
			'default' => '14px',
		);
		$this->advanced_fields['fonts']['tab']['line_height'] = array(
			'default' => '1.7em',
		);

		$this->help_videos = array(
			array(
				'id'   => '7X03vBPYJ1o',
				'name' => esc_html__( 'Divi WooCommerce Modules', 'et_builder' ),
			),
		);
	}

	/**
	 * Get product all possible tabs data
	 *
	 * @since 3.29
	 * @since 4.4.2   Fix to include Custom tabs.
	 *
	 * @global WP_Post    $post    WordPress Post.
	 * @global WC_Product $product WooCommerce Product.
	 *
	 * @return array
	 */
	public function get_product_tabs() {
		static $tabs = null;

		if ( ! is_null( $tabs ) ) {
			return $tabs;
		}

		global $post, $product;

		// Save existing $post and $product global.
		$original_post    = $post;
		$original_product = $product;

		$post_id = 'product' === $this->get_post_type()
			? ET_Builder_Element::get_current_post_id()
			: ET_Builder_Module_Helper_Woocommerce_Modules::get_product_id( 'latest' );

		// Overwriting global $post is necessary as WooCommerce relies on it.
		$post    = get_post( $post_id );
		$product = wc_get_product( $post_id );

		/*
		 * Get relevant product tabs data. Product tabs hooks use global based conditional
		 * for adding / removing product tabs data via filter hoook callback, hence the
		 * need to overwrite the global for determining product tabs data
		 */
		$tabs = is_object( $product )
			? apply_filters( 'woocommerce_product_tabs', array() )
			: ET_Builder_Module_Helper_Woocommerce_Modules::get_default_product_tabs();

		// Reset $post and $product global.
		$post    = $original_post;
		$product = $original_product;

		/*
		 * Always return all possible tabs
		 */
		return $tabs;
	}

	/**
	 * Get product tabs options; product data formatted for checkbox control's options
	 *
	 * @since 3.29
	 *
	 * @return array
	 */
	public function get_tab_options() {
		$tabs    = $this->get_product_tabs();
		$options = array();

		foreach ( $tabs as $name => $tab ) {
			if ( ! isset( $tab['title'] ) ) {
				continue;
			}

			$options[ $name ] = array(
				'value' => $name,
				'label' => 'reviews' === $name ? esc_html__( 'Reviews', 'et_builder' ) :
					esc_html( $tab['title'] ),
			);
		}

		return $options;
	}

	/**
	 * Get product tabs default based on product tabs options
	 *
	 * @since 3.29
	 *
	 * @return string
	 */
	public function get_tab_defaults() {
		return implode( '|', array_keys( $this->get_product_tabs() ) );
	}

	/**
	 * Define Woo Tabs fields
	 *
	 * @since 3.29
	 *
	 * @return array
	 */
	public function get_fields() {
		$fields = array_merge(
			parent::get_fields(),
			array(
				'product'      => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
					'product',
					array(
						'default'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_product_default(),
						'computed_affects' => array(
							'__tabs',
							'include_tabs',
						),
					)
				),
				'include_tabs' => array(
					'label'               => esc_html__( 'Include Tabs', 'et_builder' ),
					'type'                => 'checkboxes_advanced_woocommerce',
					'option_category'     => 'configuration',
					'default'             =>
						ET_Builder_Module_Helper_Woocommerce_Modules::get_woo_default_tabs(),
					'description'         => esc_html__( 'Here you can select the tabs that you would like to display.', 'et_builder' ),
					'toggle_slug'         => 'main_content',
					'mobile_options'      => true,
					'hover'               => 'tabs',
					'computed_depends_on' => array(
						'product',
					),
				),
				'__tabs'       => array(
					'type'                => 'computed',
					'computed_callback'   => array(
						'ET_Builder_Module_Woocommerce_Tabs',
						'get_tabs',
					),
					'computed_depends_on' => array(
						'product',
					),
					'computed_minimum'    => array(
						'product',
					),
				),
			)
		);

		return $fields;
	}

	/**
	 * Get tabs nav output
	 *
	 * @since 3.29
	 *
	 * @return string
	 */
	public function get_tabs_nav() {
		$nav   = '';
		$index = 0;

		// get_tabs_content() method is called earlier so get_tabs_nav() can reuse tabs data.
		if ( ! empty( $this->rendered_tabs_data ) ) {
			foreach ( $this->rendered_tabs_data as $name => $tab ) {
				$index ++;

				$nav .= sprintf(
					'<li class="%3$s%1$s"><a href="#tab-%4$s">%2$s</a></li>',
					( 1 === $index ? ' et_pb_tab_active' : '' ),
					esc_html( $tab['title'] ),
					sprintf( '%1$s_tab', esc_attr( $name ) ),
					esc_attr( $name )
				);
			}
		}

		return $nav;
	}

	/**
	 * Get tabs content output
	 *
	 * @since 4.4.1 Fix [embed][/embed] shortcodes not working in tab content
	 * @since 3.29
	 *
	 * @return string
	 */
	public function get_tabs_content() {
		// Get tabs data.
		$this->rendered_tabs_data = self::get_tabs(
			array(
				'product'      => $this->props['product'],
				'include_tabs' => $this->props['include_tabs'],
			)
		);

		// Add tabs module classname.
		$this->add_classname( 'et_pb_tabs' );

		// Render tabs content output.
		$index   = 0;
		$content = '';

		foreach ( $this->rendered_tabs_data as $name => $tab ) {
			$index ++;

			$content .= sprintf(
				'<div class="et_pb_tab clearfix%2$s">
					<div class="et_pb_tab_content">
						%1$s
					</div>
				</div>',
				$tab['content'],
				1 === $index ? ' et_pb_active_content' : ''
			);
		}

		return $content;
	}

	/**
	 * Load comments template.
	 *
	 * @param string $template template to load.
	 * @return string
	 */
	public static function comments_template_loader( $template ) {
		if ( ! et_builder_tb_enabled() ) {
			return $template;
		}

		$check_dirs = array(
			trailingslashit( get_stylesheet_directory() ) . WC()->template_path(),
			trailingslashit( get_template_directory() ) . WC()->template_path(),
			trailingslashit( get_stylesheet_directory() ),
			trailingslashit( get_template_directory() ),
			trailingslashit( WC()->plugin_path() ) . 'templates/',
		);

		if ( WC_TEMPLATE_DEBUG_MODE ) {
			$check_dirs = array( array_pop( $check_dirs ) );
		}

		foreach ( $check_dirs as $dir ) {
			if ( file_exists( trailingslashit( $dir ) . 'single-product-reviews.php' ) ) {
				return trailingslashit( $dir ) . 'single-product-reviews.php';
			}
		}
	}

	/**
	 * Get tabs data
	 *
	 * @since 4.0.9 Avoid fetching Tabs content using `the_content` when editing TB layout.
	 *
	 * @param array $args Additional args.
	 *
	 * @return array
	 */
	public static function get_tabs( $args = array() ) {
		global $product, $post, $wp_query;

		/*
		 * Visual builder fetches all tabs data and filter the included tab on the app to save
		 * app to server request for faster user experience. Frontend passes `includes_tab` to
		 * this method so it only process required tabs
		 */
		$defaults = array(
			'product' => 'current',
		);
		$args     = wp_parse_args( $args, $defaults );
		$tabs     = array();

		// Get actual product id based on given `product` attribute.
		$product_id = ET_Builder_Module_Helper_Woocommerce_Modules::get_product_id( $args['product'] );

		// Determine whether current tabs data needs global variable overwrite or not.
		$overwrite_global = et_builder_wc_need_overwrite_global( $args['product'] );

		// Check if TB is used
		$is_tb = et_builder_tb_enabled();

		$is_use_placeholder = $is_tb || is_et_pb_preview();

		if ( $is_use_placeholder ) {
			et_theme_builder_wc_set_global_objects();
		} elseif ( $overwrite_global ) {
			// Save current global variable for later reset.
			$original_product  = $product;
			$original_post     = $post;
			$original_wp_query = $wp_query;

			// Overwrite global variable.
			$post     = get_post( $product_id );
			$product  = wc_get_product( $product_id );
			$wp_query = new WP_Query( array( 'p' => $product_id ) );
		}

		if ( ! is_a( $post, 'WP_Post' ) ) {
			return $tabs;
		}

		// Get product tabs.
		$all_tabs    = apply_filters( 'woocommerce_product_tabs', array() );
		$active_tabs = isset( $args['include_tabs'] ) ? explode( '|', $args['include_tabs'] ) : false;

		// Get product tabs data.
		foreach ( $all_tabs as $name => $tab ) {
			// Skip if current tab is not included, based on `include_tabs` attribute value.
			if ( $active_tabs && ! in_array( $name, $active_tabs, true ) ) {
				continue;
			}

			if ( 'description' === $name ) {
				if ( ! $is_use_placeholder && ! et_pb_is_pagebuilder_used( $product_id ) ) {
					// If selected product doesn't use builder, retrieve post content.
					if ( et_theme_builder_overrides_layout( ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE ) ) {
						$tab_content = apply_filters( 'et_builder_wc_description', $post->post_content );
					} else {
						$tab_content = $post->post_content;
					}
				} else {
					/*
					 * Description can't use built in callback data because it gets `the_content`
					 * which might cause infinite loop; get Divi's long description from
					 * post meta instead.
					 */
					if ( $is_use_placeholder ) {
						$placeholders = et_theme_builder_wc_placeholders();

						$tab_content = $placeholders['description'];
					} else {
						$tab_content = get_post_meta( $product_id, ET_BUILDER_WC_PRODUCT_LONG_DESC_META_KEY, true );

						// Cannot use `the_content` filter since it adds content wrapper.
						// Content wrapper added at
						// `includes/builder/core.php`::et_builder_add_builder_content_wrapper()
						// This filter is documented at
						// includes/builder/feature/woocommerce-modules.php
						$tab_content = apply_filters( 'et_builder_wc_description', $tab_content );
					}
				}
			} else {
				// Skip if the 'callback' key does not exist.
				if ( ! isset( $tab['callback'] ) ) {
					continue;
				}

				// Get tab value based on defined product tab's callback attribute.
				ob_start();
				// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found
				call_user_func( $tab['callback'], $name, $tab );
				$tab_content = ob_get_clean();
			}

			// Populate product tab data.
			$tabs[ $name ] = array(
				'name'    => $name,
				'title'   => $tab['title'],
				'content' => $tab_content,
			);
		}

		// Reset overwritten global variable.
		if ( $is_use_placeholder ) {
			et_theme_builder_wc_reset_global_objects();
		} elseif ( $overwrite_global ) {
			$product  = $original_product;
			$post     = $original_post;
			$wp_query = $original_wp_query;
		}

		return $tabs;
	}

	/**
	 * Gets Multi view attributes to the Outer wrapper.
	 *
	 * Since we do not have control over the WooCommerce Breadcrumb markup, we inject Multi view
	 * attributes on to the Outer wrapper.
	 *
	 * @used-by ET_Builder_Module_Tabs::render()
	 *
	 * @return string
	 */
	public function get_multi_view_attrs() {
		$multi_view = et_pb_multi_view_options( $this );

		$multi_view_attrs = $multi_view->render_attrs(
			array(
				'attrs'  => array(
					'data-include_tabs' => '{{include_tabs}}',
				),
				'target' => '%%order_class%%',
			)
		);

		return $multi_view_attrs;
	}
}

new ET_Builder_Module_Woocommerce_Tabs();
