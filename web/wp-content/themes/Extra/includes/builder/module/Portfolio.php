<?php

class ET_Builder_Module_Portfolio extends ET_Builder_Module_Type_PostBased {
	function init() {
		$this->name       = esc_html__( 'Portfolio', 'et_builder' );
		$this->plural     = esc_html__( 'Portfolios', 'et_builder' );
		$this->slug       = 'et_pb_portfolio';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%% .et_pb_portfolio_item';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Content' ),
					'elements'     => et_builder_i18n( 'Elements' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout'  => et_builder_i18n( 'Layout' ),
					'overlay' => et_builder_i18n( 'Overlay' ),
					'image'   => array(
						'title' => et_builder_i18n( 'Image' ),
					),
					'text'    => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'title'      => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} h2, {$this->main_css_element} h2 a, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h1.et_pb_module_header a, {$this->main_css_element} h3.et_pb_module_header, {$this->main_css_element} h3.et_pb_module_header a, {$this->main_css_element} h4.et_pb_module_header, {$this->main_css_element} h4.et_pb_module_header a, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header a, {$this->main_css_element} h6.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header a",
						'important' => 'all',
						'hover'     => "{$this->main_css_element} h2:hover, {$this->main_css_element} h2:hover a, {$this->main_css_element} h1.et_pb_module_header:hover, {$this->main_css_element} h1.et_pb_module_header:hover a, {$this->main_css_element} h3.et_pb_module_header:hover, {$this->main_css_element} h3.et_pb_module_header:hover a, {$this->main_css_element} h4.et_pb_module_header:hover, {$this->main_css_element} h4.et_pb_module_header:hover a, {$this->main_css_element} h5.et_pb_module_header:hover, {$this->main_css_element} h5.et_pb_module_header:hover a, {$this->main_css_element} h6.et_pb_module_header:hover, {$this->main_css_element} h6.et_pb_module_header:hover a",
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'caption'    => array(
					'label' => esc_html__( 'Meta', 'et_builder' ),
					'css'   => array(
						'main'  => "{$this->main_css_element} .post-meta, {$this->main_css_element} .post-meta a",
						'hover' => "{$this->main_css_element} .post-meta a:hover",
					),
				),
				'pagination' => array(
					'label'           => esc_html__( 'Pagination', 'et_builder' ),
					'css'             => array(
						'main'       => function_exists( 'wp_pagenavi' ) ? '%%order_class%% .wp-pagenavi a, %%order_class%% .wp-pagenavi span' : '%%order_class%% .pagination a',
						'important'  => function_exists( 'wp_pagenavi' ) ? 'all' : array(),
						'text_align' => '%%order_class%% .wp-pagenavi',
						'hover'      => function_exists( 'wp_pagenavi' ) ? '%%order_class%% .wp-pagenavi a:hover, %%order_class%% .wp-pagenavi span:hover' : '%%order_class%% .pagination a:hover',
					),
					'hide_text_align' => ! function_exists( 'wp_pagenavi' ),
					'text_align'      => array(
						'options' => et_builder_get_text_orientation_options( array( 'justified' ), array() ),
					),
				),
			),
			'background'     => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'borders'        => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => $this->main_css_element,
							'border_styles' => $this->main_css_element,
						),
					),
				),
				'image'   => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_portfolio_image",
							'border_styles' => "{$this->main_css_element} .et_portfolio_image",
						),
					),
					'label_prefix' => et_builder_i18n( 'Image' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
				),
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'overlay' => false,
					),
				),
				'image'   => array(
					'label'             => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'image',
					'css'               => array(
						'main'    => '%%order_class%% .et_pb_portfolio_item .et_portfolio_image',
						'overlay' => 'inset',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main'      => '%%order_class%%',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'text'           => array(
				'use_background_layout' => true,
				'options'               => array(
					'background_layout' => array(
						'default' => 'light',
						'hover'   => 'tabs',
					),
				),
				'css'                   => array(
					'main' => '%%order_class%% .et_pb_module_header, %%order_class%% .post-meta',
				),
			),
			'filters'        => array(
				'css'                  => array(
					'main' => '%%order_class%%',
				),
				'child_filters_target' => array(
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'          => array(
				'css' => array(
					'main' => '%%order_class%% .et_portfolio_image',
				),
			),
			'scroll_effects' => array(
				'grid_support' => 'yes',
			),
			'button'         => false,
		);

		$this->custom_css_fields = array(
			'portfolio_image'     => array(
				'label'    => esc_html__( 'Portfolio Image', 'et_builder' ),
				'selector' => '.et_portfolio_image',
			),
			'overlay'             => array(
				'label'    => et_builder_i18n( 'Overlay' ),
				'selector' => '.et_overlay',
			),
			'overlay_icon'        => array(
				'label'    => esc_html__( 'Overlay Icon', 'et_builder' ),
				'selector' => '.et_overlay:before',
			),
			'portfolio_title'     => array(
				'label'    => esc_html__( 'Portfolio Title', 'et_builder' ),
				'selector' => '.et_pb_portfolio_item h2',
			),
			'portfolio_post_meta' => array(
				'label'    => esc_html__( 'Portfolio Post Meta', 'et_builder' ),
				'selector' => '.et_pb_portfolio_item .post-meta',
			),
			'pagination'          => array(
				'label'    => esc_html__( 'Portfolio Pagination', 'et_builder' ),
				'selector' => function_exists( 'wp_pagenavi' ) ? '%%order_class%% .wp-pagenavi a, %%order_class%% .wp-pagenavi span' : '%%order_class%% .pagination a',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => '6NpHdiLciDU',
				'name' => esc_html__( 'An introduction to the Portfolio module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'fullwidth'           => array(
				'label'            => et_builder_i18n( 'Layout' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'on'  => esc_html__( 'Fullwidth', 'et_builder' ),
					'off' => esc_html__( 'Grid', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'affects'          => array(
					'hover_icon',
					'zoom_icon_color',
					'hover_overlay_color',
				),
				'description'      => esc_html__( 'Choose your desired portfolio layout style.', 'et_builder' ),
				'computed_affects' => array(
					'__projects',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
			),
			'posts_number'        => array(
				'default'          => 10,
				'label'            => esc_html__( 'Post Count', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'Define the number of projects that should be displayed per page.', 'et_builder' ),
				'computed_affects' => array(
					'__projects',
				),
				'toggle_slug'      => 'main_content',
			),
			'include_categories'  => array(
				'label'            => esc_html__( 'Included Categories', 'et_builder' ),
				'type'             => 'categories',
				'meta_categories'  => array(
					'all'     => esc_html__( 'All Categories', 'et_builder' ),
					'current' => esc_html__( 'Current Category', 'et_builder' ),
				),
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Select the categories that you would like to include in the feed.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__projects',
				),
				'taxonomy_name'    => 'project_category',
			),
			'show_title'          => array(
				'label'            => esc_html__( 'Show Title', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Turn project titles on or off.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_categories'     => array(
				'label'            => esc_html__( 'Show Categories', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Turn the category links on or off.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_pagination'     => array(
				'label'            => esc_html__( 'Show Pagination', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Enable or disable pagination for this feed.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'zoom_icon_color'     => array(
				'label'           => esc_html__( 'Zoom Icon Color', 'et_builder' ),
				'description'     => esc_html__( 'Here you can define a custom color for the zoom icon.', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'hover_overlay_color' => array(
				'label'           => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'description'     => esc_html__( 'Here you can define a custom color for the overlay', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'hover_icon'          => array(
				'label'           => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'description'     => esc_html__( 'Here you can define a custom icon for the overlay', 'et_builder' ),
				'type'            => 'select_icon',
				'option_category' => 'configuration',
				'class'           => array( 'et-pb-font-icon' ),
				'depends_show_if' => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'__projects'          => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Portfolio', 'get_portfolio_item' ),
				'computed_depends_on' => array(
					'posts_number',
					'include_categories',
					'fullwidth',
					'__page',
				),
			),
			'__page'              => array(
				'type'              => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Portfolio', 'get_portfolio_item' ),
				'computed_affects'  => array(
					'__projects',
				),
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['max_width'] = array( 'max-width' => '%%order_class%%, %%order_class%% .et_pb_portfolio_item' );
		$fields['width']     = array( 'width' => '%%order_class%%, %%order_class%% .et_pb_portfolio_item' );

		return $fields;
	}

	/**
	 * Get portfolio objects for portfolio module
	 *
	 * @param array $args             arguments that affect et_pb_portfolio query
	 * @param array $conditional_tags conditional tag for update process
	 * @param array $current_page     current page params
	 *
	 * @return mixed portfolio item data
	 */
	static function get_portfolio_item( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		global $et_fb_processing_shortcode_object, $post, $paged, $__et_portfolio_module_paged;

		$global_processing_original_value = $et_fb_processing_shortcode_object;

		$defaults = array(
			'posts_number'       => 10,
			'include_categories' => '',
			'fullwidth'          => 'on',
		);

		$args = wp_parse_args( $args, $defaults );

		// Native conditional tag only works on page load. Data update needs $conditional_tags data
		$is_front_page = et_fb_conditional_tag( 'is_front_page', $conditional_tags );
		$is_search     = et_fb_conditional_tag( 'is_search', $conditional_tags );

		// Prepare query arguments
		$query_args = array(
			'posts_per_page' => (int) $args['posts_number'],
			'post_type'      => 'project',
			'post_status'    => array( 'publish', 'private' ),
			'perm'           => 'readable',
		);

		// Conditionally get paged data
		if ( defined( 'DOING_AJAX' ) && isset( $current_page['paged'] ) ) {
			$et_paged = intval( $current_page['paged'] );
		} else {
			$et_paged = $is_front_page ? get_query_var( 'page' ) : get_query_var( 'paged' );
		}

		if ( $__et_portfolio_module_paged > 1 ) {
			$et_paged      = $__et_portfolio_module_paged;
			$paged         = $__et_portfolio_module_paged; //phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited -- Override with ajax pagination.
			$args['paged'] = $__et_portfolio_module_paged;
		}

		if ( $is_front_page ) {
			$paged = $et_paged; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		}

		// support pagination in VB
		if ( isset( $args['__page'] ) ) {
			$et_paged = $args['__page'];
		}

		if ( ! is_search() ) {
			$query_args['paged'] = $et_paged;
		}

		// Passed categories parameter
		$include_categories = self::filter_include_categories( $args['include_categories'], 0, 'project_category' );

		if ( ! empty( $include_categories ) ) {
			$query_args['tax_query'] = array(
				array(
					'taxonomy' => 'project_category',
					'field'    => 'id',
					'terms'    => $include_categories,
					'operator' => 'IN',
				),
			);
		}

		// Get portfolio query
		$query = new WP_Query( $query_args );

		// Format portfolio output, and add supplementary data
		$width     = 'on' === $args['fullwidth'] ? 1080 : 400;
		$width     = (int) apply_filters( 'et_pb_portfolio_image_width', $width );
		$height    = 'on' === $args['fullwidth'] ? 9999 : 284;
		$height    = (int) apply_filters( 'et_pb_portfolio_image_height', $height );
		$classtext = 'on' === $args['fullwidth'] ? 'et_pb_post_main_image' : '';
		$titletext = get_the_title();

		// Loop portfolio item data and add supplementary data
		if ( $query->have_posts() ) {
			$post_index = 0;
			while ( $query->have_posts() ) {
				$query->the_post();
				ET_Post_Stack::replace( $post );

				$categories = array();

				$categories_object = get_the_terms( get_the_ID(), 'project_category' );

				if ( ! empty( $categories_object ) ) {
					foreach ( $categories_object as $category ) {
						$categories[] = array(
							'id'        => $category->term_id,
							'label'     => $category->name,
							'permalink' => get_term_link( $category ),
						);
					}
				}

				// need to disable processnig to make sure get_thumbnail() doesn't generate errors
				$et_fb_processing_shortcode_object = false;

				// Capture the ALT text defined in WP Media Library
				$alttext = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );

				// Get thumbnail
				$thumbnail = get_thumbnail( $width, $height, $classtext, $alttext, $titletext, false, 'Blogimage' );

				$et_fb_processing_shortcode_object = $global_processing_original_value;

				// Append value to query post
				$query->posts[ $post_index ]->post_permalink  = get_permalink();
				$query->posts[ $post_index ]->featured_image  = isset( $thumbnail['fullpath'] ) ? $thumbnail['fullpath'] : null;
				$query->posts[ $post_index ]->post_thumbnail  = print_thumbnail( $thumbnail['thumb'], $thumbnail['use_timthumb'], $titletext, $width, $height, '', false, true );
				$query->posts[ $post_index ]->post_categories = $categories;
				$query->posts[ $post_index ]->post_class_name = get_post_class( '', get_the_ID() );

				$post_index++;
				ET_Post_Stack::pop();
			}
			ET_Post_Stack::reset();

			$query->posts_next = array(
				'label' => esc_html__( '&laquo; Older Entries', 'et_builder' ),
				'url'   => self::get_next_link( $et_paged, $query->max_num_pages ),
			);

			$query->posts_prev = array(
				'label' => esc_html__( 'Next Entries &raquo;', 'et_builder' ),
				'url'   => self::get_previous_link( $et_paged ),
			);

			// Added wp_pagenavi support
			$query->wp_pagenavi = function_exists( 'wp_pagenavi' ) ? wp_pagenavi(
				array(
					'query' => $query,
					'echo'  => false,
				)
			) : false;
		} elseif ( self::is_processing_computed_prop() ) {
			// This is for the VB
			$query = array( 'posts' => self::get_no_results_template() );
		}

		return $query;
	}

	/**
	 * Get the next link
	 *
	 * @param int $paged Current page.
	 * @param int $max Max number of pages.
	 *
	 * @return string|null
	 */
	private static function get_next_link( $paged, $max ) {
		if ( ! $paged ) {
			$paged = 1;
		}

		$next_page = (int) $paged + 1;

		return $next_page <= $max ? get_pagenum_link( $next_page ) : null;
	}

	/**
	 * Get the previous link
	 *
	 * @param int $paged Current page.
	 *
	 * @return string|null
	 */
	private static function get_previous_link( $paged ) {
		$previous_page = (int) $paged - 1;

		return $previous_page >= 1 ? get_pagenum_link( $previous_page ) : null;
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
		global $post;
		$sticky             = et_pb_sticky_options();
		$multi_view         = et_pb_multi_view_options( $this );
		$fullwidth          = $this->props['fullwidth'];
		$posts_number       = $this->props['posts_number'];
		$include_categories = $this->props['include_categories'];
		$show_title         = $this->props['show_title'];
		$show_categories    = $this->props['show_categories'];
		$show_pagination    = $this->props['show_pagination'];
		$header_level       = $this->props['title_level'];

		global $paged;

		$processed_header_level = et_pb_process_header_level( $header_level, 'h2' );

		// Set inline style

		// Zoom Icon color.
		$this->generate_styles(
			array(
				'hover'          => false,
				'base_attr_name' => 'zoom_icon_color',
				'selector'       => '%%order_class%% .et_overlay:before',
				'css_property'   => 'color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
				'important'      => true,
			)
		);

		// Hover Overlay color.
		$this->generate_styles(
			array(
				'hover'          => false,
				'base_attr_name' => 'hover_overlay_color',
				'selector'       => '%%order_class%% .et_overlay',
				'css_property'   => array( 'background-color', 'border-color' ),
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		$container_is_closed = false;

		// Get loop data
		$portfolio = self::get_portfolio_item(
			array(
				'posts_number'       => $posts_number,
				'include_categories' => $include_categories,
				'fullwidth'          => $fullwidth,
			)
		);

		// setup overlay
		$overlay = '';
		if ( 'on' !== $fullwidth ) {
			$hover_icon        = $this->props['hover_icon'];
			$hover_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'hover_icon' );
			$hover_icon_tablet = isset( $hover_icon_values['tablet'] ) ? $hover_icon_values['tablet'] : '';
			$hover_icon_phone  = isset( $hover_icon_values['phone'] ) ? $hover_icon_values['phone'] : '';
			$hover_icon_sticky = $sticky->get_value( 'hover_icon', $this->props );

			$overlay = 'on' === $fullwidth ? '' : ET_Builder_Module_Helper_Overlay::render(
				array(
					'icon'        => $hover_icon,
					'icon_tablet' => $hover_icon_tablet,
					'icon_phone'  => $hover_icon_phone,
					'icon_sticky' => $hover_icon_sticky,
				)
			);

			// Overlay Icon Styles.
			$this->generate_styles(
				array(
					'hover'          => false,
					'utility_arg'    => 'icon_font_family',
					'render_slug'    => $render_slug,
					'base_attr_name' => 'hover_icon',
					'important'      => true,
					'selector'       => '%%order_class%% .et_overlay:before',
					'processor'      => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_extended_icon',
					),
				)
			);
		}

		ob_start();

		$portfolio_order = self::_get_index( array( self::INDEX_MODULE_ORDER, $render_slug ) );
		$items_count     = 0;

		if ( $portfolio->have_posts() ) {
			while ( $portfolio->have_posts() ) {
				$portfolio->the_post();
				ET_Post_Stack::replace( $post );

				// Get $post data of current loop
				global $post;

				array_push( $post->post_class_name, 'et_pb_portfolio_item' );

				$item_class = sprintf( 'et_pb_portfolio_item_%1$s_%2$s', $portfolio_order, $items_count );

				array_push( $post->post_class_name, $item_class );

				$items_count++;

				if ( 'on' !== $fullwidth ) {
					array_push( $post->post_class_name, 'et_pb_grid_item' );
				}

				?>
				<div id="post-<?php echo esc_attr( $post->ID ); ?>" class="<?php echo esc_attr( join( ' ', $post->post_class_name ) ); ?>">

					<?php if ( '' !== $post->post_thumbnail ) { ?>
					<a href="<?php echo esc_url( $post->post_permalink ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>">
						<?php if ( 'on' === $fullwidth ) { ?>
							<span class="et_portfolio_image">
							<?php
								$this->render_image(
									$post->post_thumbnail,
									array(
										'alt'    => get_the_title(),
										'width'  => '1080',
										'height' => '9999',
									)
								);
							?>
							</span>
						<?php } else { ?>
							<span class="et_portfolio_image">
								<?php
								$image_attrs = array(
									'alt'    => get_the_title(),
									'width'  => '400',
									'height' => '284',
								);

								if ( ! empty( $post->featured_image ) ) {
									$image_attrs['srcset'] = $post->featured_image . ' 479w, ' . $post->post_thumbnail . ' 480w';
									$image_attrs['sizes']  = '(max-width:479px) 479px, 100vw';
								}

								$this->render_image( $post->post_thumbnail, $image_attrs );
								?>
								<?php echo et_core_esc_previously( $overlay ); ?>
							</span>
						<?php } ?>
					</a>
					<?php } ?>

					<?php
					$multi_view->render_element(
						array(
							'tag'        => $processed_header_level,
							'content'    => sprintf( '<a href="%1$s" title="%2$s">%3$s</a>', esc_url( $post->post_permalink ), esc_attr( get_the_title() ), esc_html( get_the_title() ) ),
							'attrs'      => array(
								'class' => 'et_pb_module_header',
							),
							'visibility' => array(
								'show_title' => 'on',
							),
							'required'   => array(
								'show_title' => 'on',
							),
						),
						true
					);

					if ( $multi_view->has_value( 'show_categories', 'on' ) && ! empty( $post->post_categories ) ) :
						$categories_links = '';
						$category_index   = 0;

						foreach ( $post->post_categories as $category ) {
							$category_index++;
							$separator         = $category_index < count( $post->post_categories ) ? ', ' : '';
							$categories_links .= '<a href="' . esc_url( $category['permalink'] ) . '" title="' . esc_attr( $category['label'] ) . '">' . esc_html( $category['label'] ) . '</a>' . et_core_intentionally_unescaped( $separator, 'fixed_string' );
						}

						$multi_view->render_element(
							array(
								'tag'        => 'p',
								'content'    => $categories_links,
								'attrs'      => array(
									'class' => 'post-meta',
								),
								'visibility' => array(
									'show_categories' => 'on',
								),
								'required'   => array(
									'show_categories' => 'on',
								),
							),
							true
						);
					endif;
					?>

				</div>
				<?php
				ET_Post_Stack::pop();
			}
			ET_Post_Stack::reset();

			if ( $multi_view->has_value( 'show_pagination', 'on' ) && ! is_search() ) {
				if ( function_exists( 'wp_pagenavi' ) ) {
					$pagination = $multi_view->render_element(
						array(
							'tag'        => 'div',
							'content'    => wp_pagenavi(
								array(
									'query' => $portfolio,
									'echo'  => false,
								)
							),
							'visibility' => array(
								'show_pagination' => 'on',
							),
							'required'   => array(
								'show_pagination' => 'on',
							),
						)
					);
				} else {
					$next_posts_link_html = $prev_posts_link_html = '';

					if ( ! empty( $portfolio->posts_next['url'] ) ) {
						$next_posts_link_html = sprintf(
							'<div class="alignleft">
								<a href="%1$s">%2$s</a>
							</div>',
							add_query_arg( 'et_portfolio', '', esc_url( $portfolio->posts_next['url'] ) ),
							esc_html( $portfolio->posts_next['label'] )
						);
					}

					if ( ! empty( $portfolio->posts_prev['url'] ) ) {
						$prev_posts_link_html = sprintf(
							'<div class="alignright">
								<a href="%1$s">%2$s</a>
							</div>',
							add_query_arg( 'et_portfolio', '', esc_url( $portfolio->posts_prev['url'] ) ),
							esc_html( $portfolio->posts_prev['label'] )
						);
					}

					$pagination = sprintf(
						'<div class="pagination clearfix"%3$s>
							%1$s
							%2$s
						</div>',
						$next_posts_link_html,
						$prev_posts_link_html,
						$multi_view->render_attrs(
							array(
								'visibility' => array(
									'show_pagination' => 'on',
								),
								'required'   => array(
									'show_pagination' => 'on',
								),
							)
						)
					);
				}
			}
		}

		if ( ! $posts = ob_get_clean() ) {
			$posts = self::get_no_results_template();
		}

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$fullwidth = 'on' === $fullwidth;

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		if ( array_key_exists( 'image', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['image'] ) ) {
			$this->add_classname(
				$this->generate_css_filters(
					$render_slug,
					'child_',
					self::$data_utils->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' )
				)
			);
		}

		// Module classnames
		$this->add_classname(
			array(
				$this->get_text_orientation_classname(),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		if ( ! $fullwidth ) {
			$this->add_classname(
				array(
					'et_pb_portfolio_grid',
					'clearfix',
				)
			);

			$this->remove_classname( $render_slug );
		}

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$output = sprintf(
			'<div%4$s class="%1$s"%10$s>
				<div class="et_pb_ajax_pagination_container">
					%6$s
					%5$s
					%11$s
					%12$s
					%7$s
						%2$s
					%8$s
					%9$s
				</div>
			%3$s',
			$this->module_classname( $render_slug ),
			$posts,
			( ! $container_is_closed ? '</div>' : '' ),
			$this->module_id(),
			$video_background, // #5
			$parallax_image_background,
			$fullwidth ? '' : '<div class="et_pb_portfolio_grid_items">',
			$fullwidth ? '' : '</div>',
			isset( $pagination ) ? $pagination : '',
			et_core_esc_previously( $data_background_layout ), // #10
			et_core_esc_previously( $this->background_pattern() ), // #11
			et_core_esc_previously( $this->background_mask() ) // #12
		);

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Portfolio();
}
