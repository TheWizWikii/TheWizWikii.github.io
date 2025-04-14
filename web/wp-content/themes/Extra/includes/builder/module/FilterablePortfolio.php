<?php

class ET_Builder_Module_Filterable_Portfolio extends ET_Builder_Module_Type_PostBased {
	function init() {
		$this->name       = esc_html__( 'Filterable Portfolio', 'et_builder' );
		$this->plural     = esc_html__( 'Filterable Portfolios', 'et_builder' );
		$this->slug       = 'et_pb_filterable_portfolio';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%.et_pb_filterable_portfolio';

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
					'text'    => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
					'image'   => et_builder_i18n( 'Image' ),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'title'      => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'         => "{$this->main_css_element} h2, {$this->main_css_element} .et_pb_module_header",
						'limited_main' => "{$this->main_css_element} h2, {$this->main_css_element} h2 a, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h1.et_pb_module_header a, {$this->main_css_element} h3.et_pb_module_header, {$this->main_css_element} h3.et_pb_module_header a, {$this->main_css_element} h4.et_pb_module_header, {$this->main_css_element} h4.et_pb_module_header a, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header a, {$this->main_css_element} h6.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header a",
						'hover'        => "{$this->main_css_element} h2:hover, {$this->main_css_element} h2:hover a, {$this->main_css_element} h1.et_pb_module_header:hover, {$this->main_css_element} h1.et_pb_module_header:hover a, {$this->main_css_element} h3.et_pb_module_header:hover, {$this->main_css_element} h3.et_pb_module_header:hover a, {$this->main_css_element} h4.et_pb_module_header:hover, {$this->main_css_element} h4.et_pb_module_header:hover a, {$this->main_css_element} h5.et_pb_module_header:hover, {$this->main_css_element} h5.et_pb_module_header:hover a, {$this->main_css_element} h6.et_pb_module_header:hover, {$this->main_css_element} h6.et_pb_module_header:hover a",
						'important'    => 'all',
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'filter'     => array(
					'label'           => esc_html__( 'Filter Criteria', 'et_builder' ),
					'hide_text_align' => true,
					'css'             => array(
						'main'         => "{$this->main_css_element} .et_pb_portfolio_filter",
						'limited_main' => "{$this->main_css_element} .et_pb_portfolio_filter, {$this->main_css_element} .et_pb_portfolio_filter a",
						'hover'        => "{$this->main_css_element} .et_pb_portfolio_filter:hover, {$this->main_css_element} .et_pb_portfolio_filter:hover a",
						'color_hover'  => "{$this->main_css_element} .et_pb_portfolio_filter:hover a",
						'color'        => "{$this->main_css_element} .et_pb_portfolio_filter a",
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
					'label'      => esc_html__( 'Pagination', 'et_builder' ),
					'css'        => array(
						'main'       => "{$this->main_css_element} .et_pb_portofolio_pagination a",
						'text_align' => "{$this->main_css_element} .et_pb_portofolio_pagination ul",
						'hover'      => "{$this->main_css_element} .et_pb_portofolio_pagination a:hover",
					),
					'text_align' => array(
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
							'border_radii'  => "{$this->main_css_element} .et_pb_portfolio_item",
							'border_styles' => "{$this->main_css_element} .et_pb_portfolio_item",
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
				'default' => array(),
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
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'max_width'      => array(
				'css' => array(
					'module_alignment' => '%%order_class%%.et_pb_filterable_portfolio.et_pb_module',
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
			'portfolio_filters'           => array(
				'label'                    => esc_html__( 'Portfolio Filters', 'et_builder' ),
				'selector'                 => '.et_pb_filterable_portfolio .et_pb_portfolio_filters',
				'no_space_before_selector' => true,
			),
			'active_portfolio_filter'     => array(
				'label'                    => esc_html__( 'Active Portfolio Filter', 'et_builder' ),
				'selector'                 => '.et_pb_filterable_portfolio .et_pb_portfolio_filters li a.active',
				'no_space_before_selector' => true,
			),
			'portfolio_image'             => array(
				'label'    => esc_html__( 'Portfolio Image', 'et_builder' ),
				'selector' => '.et_portfolio_image',
			),
			'overlay'                     => array(
				'label'    => et_builder_i18n( 'Overlay' ),
				'selector' => '.et_overlay',
			),
			'overlay_icon'                => array(
				'label'    => esc_html__( 'Overlay Icon', 'et_builder' ),
				'selector' => '.et_overlay:before',
			),
			'portfolio_title'             => array(
				'label'    => esc_html__( 'Portfolio Title', 'et_builder' ),
				'selector' => '.et_pb_portfolio_item h2',
			),
			'portfolio_post_meta'         => array(
				'label'    => esc_html__( 'Portfolio Post Meta', 'et_builder' ),
				'selector' => '.et_pb_portfolio_item .post-meta',
			),
			'portfolio_pagination'        => array(
				'label'    => esc_html__( 'Portfolio Pagination', 'et_builder' ),
				'selector' => '.et_pb_portofolio_pagination',
			),
			'portfolio_pagination_active' => array(
				'label'    => esc_html__( 'Pagination Active Page', 'et_builder' ),
				'selector' => '.et_pb_portofolio_pagination a.active',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'AZheY1hVcJc',
				'name' => esc_html__( 'An introduction to the Filterable Portfolio module', 'et_builder' ),
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
				'default_on_front' => 'on',
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
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Select the categories that you would like to include in the feed.', 'et_builder' ),
				'computed_affects' => array(
					'__project_terms',
					'__projects',
				),
				'taxonomy_name'    => 'project_category',
				'toggle_slug'      => 'main_content',
			),
			'show_title'          => array(
				'label'            => esc_html__( 'Show Title', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Turn project titles on or off.', 'et_builder' ),
				'default_on_front' => 'on',
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
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Turn the category links on or off.', 'et_builder' ),
				'default_on_front' => 'on',
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
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Enable or disable pagination for this feed.', 'et_builder' ),
				'default_on_front' => 'on',
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
				'sticky'          => true,
			),
			'hover_overlay_color' => array(
				'label'           => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'sticky'          => true,
			),
			'hover_icon'          => array(
				'label'           => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'type'            => 'select_icon',
				'option_category' => 'configuration',
				'class'           => array( 'et-pb-font-icon' ),
				'depends_show_if' => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'sticky'          => true,
			),
			'__project_terms'     => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Filterable_Portfolio', 'get_portfolio_terms' ),
				'computed_depends_on' => array(
					'include_categories',
				),
			),
			'__projects'          => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Filterable_Portfolio', 'get_portfolio_item' ),
				'computed_depends_on' => array(
					'show_pagination',
					'posts_number',
					'include_categories',
					'fullwidth',
				),
			),
		);

		return $fields;
	}

	static function get_portfolio_item( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		global $et_fb_processing_shortcode_object, $post;

		$global_processing_original_value = $et_fb_processing_shortcode_object;

		$defaults = array(
			'show_pagination'    => 'on',
			'include_categories' => '',
			'fullwidth'          => 'on',
			'nopaging'           => true,
		);

		$query_args = array();

		$args = wp_parse_args( $args, $defaults );

		$include_categories = self::filter_invalid_term_ids( explode( ',', $args['include_categories'] ), 'project_category' );

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

		$default_query_args = array(
			'post_type'      => 'project',
			'post_status'    => array( 'publish', 'private' ),
			'perm'           => 'readable',
			'posts_per_page' => - 1,
		);

		$query_args = wp_parse_args( $query_args, $default_query_args );

		// Get portfolio query
		$query = new WP_Query( $query_args );

		// Format portfolio output, and add supplementary data
		$width     = 'on' === $args['fullwidth'] ? 1080 : 400;
		$width     = (int) apply_filters( 'et_pb_portfolio_image_width', $width );
		$height    = 'on' === $args['fullwidth'] ? 9999 : 284;
		$height    = (int) apply_filters( 'et_pb_portfolio_image_height', $height );
		$classtext = 'on' === $args['fullwidth'] ? 'et_pb_post_main_image' : '';
		$titletext = get_the_title();

		// Loop portfolio item and add supplementary data
		if ( $query->have_posts() ) {
			$post_index = 0;
			while ( $query->have_posts() ) {
				$query->the_post();
				ET_Post_Stack::replace( $post );

				$categories = array();

				$category_classes = array( 'et_pb_portfolio_item' );

				if ( 'on' !== $args['fullwidth'] ) {
					$category_classes[] = 'et_pb_grid_item';
				}

				$categories_object = get_the_terms( get_the_ID(), 'project_category' );
				if ( ! empty( $categories_object ) ) {
					foreach ( $categories_object as $category ) {
						// Update category classes which will be used for post_class
						$category_classes[] = 'project_category_' . urldecode( $category->slug );

						// Push category data
						$categories[] = array(
							'id'        => $category->term_id,
							'slug'      => $category->slug,
							'label'     => $category->name,
							'permalink' => get_term_link( $category ),
						);
					}
				}

				// need to disable processing to make sure get_thumbnail() doesn't generate errors
				$et_fb_processing_shortcode_object = false;

				// Capture the ALT text defined in WP Media Library
				$alttext = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );

				// Get thumbnail
				$thumbnail = get_thumbnail( $width, $height, $classtext, $alttext, $titletext, false, 'Blogimage' );

				$et_fb_processing_shortcode_object = $global_processing_original_value;

				// Append value to query post
				$query->posts[ $post_index ]->post_permalink  = get_permalink();
				$query->posts[ $post_index ]->post_thumbnail  = print_thumbnail( $thumbnail['thumb'], $thumbnail['use_timthumb'], $titletext, $width, $height, '', false, true );
				$query->posts[ $post_index ]->post_categories = $categories;
				$query->posts[ $post_index ]->post_class_name = array_merge( get_post_class( '', get_the_ID() ), $category_classes );

				// Append category classes
				$category_classes = implode( ' ', $category_classes );

				$post_index++;
				ET_Post_Stack::pop();
			}
			ET_Post_Stack::reset();
		} elseif ( self::is_processing_computed_prop() ) {
			// This is for the VB
			$query = array( 'posts' => self::get_no_results_template() );
		}

		return $query;
	}

	static function get_portfolio_terms( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$portfolio = self::get_portfolio_item( $args, $conditional_tags, $current_page );

		$terms = array();

		if ( ! empty( $portfolio->posts ) ) {
			foreach ( $portfolio->posts as $post ) {
				if ( ! empty( $post->post_categories ) ) {
					foreach ( $post->post_categories as $category ) {
						$terms[ $category['slug'] ] = $category;
					}
				}
			}
		}

		return $terms;
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
		$hover_icon         = $this->props['hover_icon'];
		$hover_icon_sticky  = $sticky->get_value( 'hover_icon', $this->props );
		$header_level       = $this->props['title_level'];

		$this->generate_styles(
			array(
				'hover'          => false,
				'base_attr_name' => 'zoom_icon_color',
				'selector'       => '%%order_class%% .et_overlay:before',
				'css_property'   => 'color',
				'render_slug'    => $render_slug,
				'important'      => true,
				'type'           => 'color',
			)
		);

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

		$projects = self::get_portfolio_item(
			array(
				'show_pagination'    => $show_pagination,
				'posts_number'       => $posts_number,
				'include_categories' => $include_categories,
				'fullwidth'          => $fullwidth,
			)
		);

		$categories_included = array();

		$portfolio_order = self::_get_index( array( self::INDEX_MODULE_ORDER, $render_slug ) );
		$items_count     = 0;

		$overlay_output = 'on' === $fullwidth ? '' : ET_Builder_Module_Helper_Overlay::render(
			array(
				'icon'        => $hover_icon,
				'icon_sticky' => $hover_icon_sticky,
			)
		);

		if ( 'on' !== $fullwidth ) {
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
		if ( $projects->post_count > 0 ) {
			while ( $projects->have_posts() ) {
				$projects->the_post();
				ET_Post_Stack::replace( $post );

				$category_classes = array();
				$categories       = get_the_terms( get_the_ID(), 'project_category' );
				if ( $categories ) {
					foreach ( $categories as $category ) {
						$category_classes[]    = 'project_category_' . urldecode( $category->slug );
						$categories_included[] = $category->term_id;
					}
				}

				$category_classes = implode( ' ', $category_classes );

				$item_class = sprintf( 'et_pb_filterable_portfolio_item_%1$s_%2$s', $portfolio_order, $items_count );
				$items_count++;

				$main_post_class = sprintf(
					'et_pb_portfolio_item%1$s %2$s %3$s',
					( 'on' !== $fullwidth ? ' et_pb_grid_item' : '' ),
					$category_classes,
					$item_class
				);

				?>
				<div id="post-<?php the_ID(); ?>" <?php post_class( $main_post_class ); ?>>
				<?php
					$thumb = '';

					$width = 'on' === $fullwidth ? 1080 : 400;
					$width = (int) apply_filters( 'et_pb_portfolio_image_width', $width );

					$height    = 'on' === $fullwidth ? 9999 : 284;
					$height    = (int) apply_filters( 'et_pb_portfolio_image_height', $height );
					$classtext = 'on' === $fullwidth ? 'et_pb_post_main_image' : '';
					$titletext = get_the_title();
					$permalink = get_permalink();
					$post_meta = get_the_term_list( get_the_ID(), 'project_category', '', ', ' );
					$alttext   = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
					$thumbnail = get_thumbnail( $width, $height, $classtext, $alttext, $titletext, false, 'Blogimage' );
					$thumb     = $thumbnail['thumb'];

				if ( '' !== $thumb ) :
					?>
						<a href="<?php echo esc_url( $permalink ); ?>">
							<span class="et_portfolio_image">
								<?php print_thumbnail( $thumb, $thumbnail['use_timthumb'], $titletext, $width, $height ); ?>
								<?php
								if ( 'on' !== $fullwidth ) {
									echo et_core_esc_previously( $overlay_output ); }
								?>
							</span>
						</a>
					<?php
					endif;

					$multi_view->render_element(
						array(
							'tag'        => et_pb_process_header_level( $header_level, 'h2' ),
							'content'    => sprintf( '<a href="%1$s">%2$s</a>', esc_url( $permalink ), et_core_intentionally_unescaped( $titletext, 'html' ) ),
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

					$multi_view->render_element(
						array(
							'tag'        => 'p',
							'content'    => et_core_esc_wp( $post_meta ),
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
				?>
				</div>
				<?php
				ET_Post_Stack::pop();
			}
			ET_Post_Stack::reset();
		}

		if ( ! $posts = ob_get_clean() ) {
			$posts            = self::get_no_results_template();
			$category_filters = '';
		} else {
			$categories_included = explode( ',', $include_categories );
			$terms_args          = array(
				'include' => $categories_included,
				'orderby' => 'name',
				'order'   => 'ASC',
			);
			$terms               = get_terms( 'project_category', $terms_args );

			$category_filters  = '<ul class="clearfix">';
			$category_filters .= sprintf(
				'<li class="et_pb_portfolio_filter et_pb_portfolio_filter_all"><a href="#" class="active" data-category-slug="all">%1$s</a></li>',
				esc_html__( 'All', 'et_builder' )
			);
			foreach ( $terms as $term ) {
				$category_filters .= sprintf(
					'<li class="et_pb_portfolio_filter"><a href="#" data-category-slug="%1$s">%2$s</a></li>',
					esc_attr( urldecode( $term->slug ) ),
					esc_html( $term->name )
				);
			}
			$category_filters .= '</ul>';
		}

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		if ( isset( $this->advanced_fields['image']['css'] ) ) {
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
				'et_pb_portfolio',
				$this->get_text_orientation_classname(),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		if ( 'on' === $fullwidth ) {
			$this->add_classname( 'et_pb_filterable_portfolio_fullwidth' );
		} else {
			$this->add_classname(
				array(
					'et_pb_filterable_portfolio_grid',
					'clearfix',
				)
			);
		}

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$pagination_classes_multi_view_attr = $multi_view->render_attrs(
			array(
				'classes' => array(
					'clearfix'      => array(
						'show_pagination' => 'on',
					),
					'no_pagination' => array(
						'show_pagination' => 'off',
					),
				),
			)
		);

		$pagination_multi_view = $multi_view->render_element(
			array(
				'tag'        => 'div',
				'attrs'      => array(
					'class' => 'et_pb_portofolio_pagination',
				),
				'visibility' => array(
					'show_pagination' => 'on',
				),
				'required'   => array(
					'show_pagination' => 'on',
				),
			)
		);

		$output = sprintf(
			'<div%4$s class="%1$s" data-posts-number="%5$d"%8$s%11$s>
				%10$s
				%9$s
				%13$s
				%14$s
				<div class="et_pb_portfolio_filters clearfix">%2$s</div>

				<div class="et_pb_portfolio_items_wrapper %6$s"%12$s>
					<div class="et_pb_portfolio_items">%3$s</div>
				</div>
				%7$s
			</div>',
			$this->module_classname( $render_slug ),
			$category_filters,
			$posts,
			$this->module_id(),
			esc_attr( $posts_number ), // #5
			( 'on' === $multi_view->get_value( 'show_pagination' ) ? 'clearfix' : 'no_pagination' ),
			$pagination_multi_view,
			is_rtl() ? ' data-rtl="true"' : '',
			$video_background,
			$parallax_image_background, // #10
			et_core_esc_previously( $data_background_layout ),
			$pagination_classes_multi_view_attr,
			et_core_esc_previously( $this->background_pattern() ), // #13
			et_core_esc_previously( $this->background_mask() ) // #14
		);

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Filterable_Portfolio();
}
