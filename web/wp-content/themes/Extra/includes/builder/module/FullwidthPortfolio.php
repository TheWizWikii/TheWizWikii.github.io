<?php

class ET_Builder_Module_Fullwidth_Portfolio extends ET_Builder_Module_Type_PostBased {
	function init() {
		$this->name       = esc_html__( 'Fullwidth Portfolio', 'et_builder' );
		$this->plural     = esc_html__( 'Fullwidth Portfolios', 'et_builder' );
		$this->slug       = 'et_pb_fullwidth_portfolio';
		$this->vb_support = 'on';
		$this->fullwidth  = true;

		// need to use global settings from the slider module
		$this->global_settings_slug = 'et_pb_portfolio';

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Content' ),
					'elements'     => et_builder_i18n( 'Elements' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'layout'   => et_builder_i18n( 'Layout' ),
					'overlay'  => et_builder_i18n( 'Overlay' ),
					'rotation' => esc_html__( 'Rotation', 'et_builder' ),
					'text'     => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
					'image'    => et_builder_i18n( 'Image' ),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'           => array(
				'portfolio_header' => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} .et_pb_portfolio_title",
						'important' => 'all',
					),
					'header_level' => array(
						'default' => 'h2',
					),
					'font_size'    => array(
						'default' => '26px',
					),
					'line_height'  => array(
						'default' => '1em',
					),
				),
				'title'            => array(
					'label'        => esc_html__( 'Portfolio Item Title', 'et_builder' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} h3, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h2.et_pb_module_header, {$this->main_css_element} h4.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header",
						'important' => 'all',
					),
					'header_level' => array(
						'default' => 'h3',
					),
				),
				'caption'          => array(
					'label' => esc_html__( 'Meta', 'et_builder' ),
					'css'   => array(
						'main'       => "{$this->main_css_element} .post-meta, {$this->main_css_element} .post-meta a",
						'text_align' => "{$this->main_css_element} .et_pb_portfolio_image p.post-meta",
					),
				),
			),
			'background'      => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'borders'         => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element}",
							'border_styles' => "{$this->main_css_element}",
						),
					),
				),
				'image'   => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_pb_portfolio_image",
							'border_styles' => "{$this->main_css_element} .et_pb_portfolio_image",
						),
					),
					'label_prefix' => et_builder_i18n( 'Image' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
				),
			),
			'box_shadow'      => array(
				'default' => array(),
				'image'   => array(
					'label'             => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'image',
					'css'               => array(
						'main'    => '%%order_class%% .et_pb_portfolio_image',
						'overlay' => 'inset',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'text'            => array(
				'use_background_layout' => true,
				'css'                   => array(
					'text_orientation' => '%%order_class%% h2, %%order_class%% .et_pb_portfolio_image h3, %%order_class%% .et_pb_portfolio_image p, %%order_class%% .et_pb_portfolio_title, %%order_class%% .et_pb_portfolio_image .et_pb_module_header',
				),
				'options'               => array(
					'text_orientation'  => array(
						'default_on_front' => 'center',
					),
					'background_layout' => array(
						'default_on_front' => 'light',
						'hover'            => 'tabs',
					),
				),
			),
			'filters'         => array(
				'css'                  => array(
					'main' => '%%order_class%%',
				),
				'child_filters_target' => array(
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'           => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_portfolio_image',
				),
			),
			'scroll_effects'  => array(
				'grid_support' => 'yes',
			),
			'button'          => false,
			'position_fields' => array(
				'default' => 'relative',
			),
		);

		$this->custom_css_fields = array(
			'portfolio_title'      => array(
				'label'    => esc_html__( 'Portfolio Title', 'et_builder' ),
				'selector' => '> h2',
			),
			'portfolio_item'       => array(
				'label'    => esc_html__( 'Portfolio Item', 'et_builder' ),
				'selector' => '.et_pb_portfolio_item',
			),
			'portfolio_overlay'    => array(
				'label'    => esc_html__( 'Item Overlay', 'et_builder' ),
				'selector' => 'span.et_overlay',
			),
			'portfolio_item_title' => array(
				'label'    => esc_html__( 'Item Title', 'et_builder' ),
				'selector' => '.meta h3',
			),
			'portfolio_meta'       => array(
				'label'    => esc_html__( 'Meta', 'et_builder' ),
				'selector' => '.meta p',
			),
			'portfolio_arrows'     => array(
				'label'    => esc_html__( 'Navigation Arrows', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'Mug6LhcJQ5M',
				'name' => esc_html__( 'An introduction to the Fullwidth Portfolio module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title'               => array(
				'label'           => esc_html__( 'Portfolio Title', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Title displayed above the portfolio.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'fullwidth'           => array(
				'label'            => et_builder_i18n( 'Layout' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'on'  => esc_html__( 'Carousel', 'et_builder' ),
					'off' => esc_html__( 'Grid', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'affects'          => array(
					'auto',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
				'description'      => esc_html__( 'Choose your desired portfolio layout style.', 'et_builder' ),
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
				'computed_affects' => array(
					'__projects',
				),
				'taxonomy_name'    => 'project_category',
				'toggle_slug'      => 'main_content',
			),
			'posts_number'        => array(
				'label'            => esc_html__( 'Post Count', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'Control how many projects are displayed. Leave blank or use 0 to not limit the amount.', 'et_builder' ),
				'computed_affects' => array(
					'__projects',
				),
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
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Turn project titles on or off.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_date'           => array(
				'label'            => esc_html__( 'Show Date', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Turn the date display on or off.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'zoom_icon_color'     => array(
				'label'          => esc_html__( 'Overlay Icon Color', 'et_builder' ),
				'description'    => esc_html__( 'Here you can define a custom color for the zoom icon.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'overlay',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'hover_overlay_color' => array(
				'label'          => esc_html__( 'Overlay Background Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the icon that appears when hovering over a portfolio item.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'overlay',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'hover_icon'          => array(
				'label'           => esc_html__( 'Overlay Icon', 'et_builder' ),
				'description'     => esc_html__( 'Select an icon to appear when hovering over a portfolio item.', 'et_builder' ),
				'type'            => 'select_icon',
				'option_category' => 'configuration',
				'class'           => array( 'et-pb-font-icon' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'__projects'          => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Fullwidth_Portfolio', 'get_portfolio_item' ),
				'computed_depends_on' => array(
					'posts_number',
					'include_categories',
				),
			),
		);

		return $fields;
	}

	/**
	 * Get portfolio objects for portfolio module
	 *
	 * @param array  arguments that affect et_pb_portfolio query
	 * @param array  passed conditional tag for update process
	 * @param array  passed current page params
	 * @return array portfolio item data
	 */
	static function get_portfolio_item( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		global $post;

		$defaults = array(
			'posts_number'       => '',
			'include_categories' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		$query_args = array(
			'post_type'   => 'project',
			'post_status' => array( 'publish', 'private' ),
			'perm'        => 'readable',
		);

		if ( is_numeric( $args['posts_number'] ) && $args['posts_number'] > 0 ) {
			$query_args['posts_per_page'] = $args['posts_number'];
		} else {
			$query_args['nopaging'] = true;
		}

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

		// Format portfolio output, add supplementary data
		$width  = (int) apply_filters( 'et_pb_portfolio_image_width', 510 );
		$height = (int) apply_filters( 'et_pb_portfolio_image_height', 382 );

		if ( $query->post_count > 0 ) {
			$post_index = 0;
			while ( $query->have_posts() ) {
				$query->the_post();
				ET_Post_Stack::replace( $post );

				// Get thumbnail
				$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), array( $width, $height ) );

				if ( isset( $thumbnail[2] ) && isset( $thumbnail[1] ) ) {
					$orientation = ( $thumbnail[2] > $thumbnail[1] ) ? 'portrait' : 'landscape';
				} else {
					$orientation = false;
				}

				// Append value to query post
				$query->posts[ $post_index ]->post_permalink             = get_permalink();
				$query->posts[ $post_index ]->post_thumbnail             = isset( $thumbnail[0] ) ? $thumbnail[0] : false;
				$query->posts[ $post_index ]->post_thumbnail_orientation = $orientation;
				$query->posts[ $post_index ]->post_date_readable         = get_the_date();
				$query->posts[ $post_index ]->post_class_name            = get_post_class( 'et_pb_portfolio_item et_pb_grid_item ' );

				$post_index++;
				ET_Post_Stack::pop();
			}
			ET_Post_Stack::reset();
		} elseif ( self::is_processing_computed_prop() ) {
			// This is for the VB
			$posts  = '<div class="et_pb_row et_pb_no_results">';
			$posts .= self::get_no_results_template();
			$posts .= '</div>';
			$query  = array( 'posts' => $posts );
		}

		return $query;
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
		$title              = $multi_view->render_element(
			array(
				'tag'     => et_pb_process_header_level( $this->props['portfolio_header_level'], 'h2' ),
				'content' => '{{title}}',
				'attrs'   => array(
					'class' => 'et_pb_portfolio_title',
				),
			)
		);
		$fullwidth          = $this->props['fullwidth'];
		$include_categories = $this->props['include_categories'];
		$posts_number       = $this->props['posts_number'];
		$show_title         = $this->props['show_title'];
		$show_date          = $this->props['show_date'];
		$auto               = $this->props['auto'];
		$auto_speed         = $this->props['auto_speed'];
		$hover_icon         = $this->props['hover_icon'];
		$header_level       = $this->props['title_level'];

		$zoom_and_hover_selector = '.et_pb_fullwidth_portfolio%%order_class%% .et_pb_portfolio_image';

		// Zoom Icon color.
		$this->generate_styles(
			array(
				'hover'          => false,
				'base_attr_name' => 'zoom_icon_color',
				'selector'       => "{$zoom_and_hover_selector} .et_overlay:before",
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
				'selector'       => "{$zoom_and_hover_selector} .et_overlay",
				'css_property'   => array( 'background-color', 'border-color' ),
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		// Overlay.
		$hover_icon        = $this->props['hover_icon'];
		$hover_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'hover_icon' );
		$hover_icon_tablet = isset( $hover_icon_values['tablet'] ) ? $hover_icon_values['tablet'] : '';
		$hover_icon_phone  = isset( $hover_icon_values['phone'] ) ? $hover_icon_values['phone'] : '';
		$hover_icon_sticky = $sticky->get_value( 'hover_icon', $this->props );

		$overlay = ET_Builder_Module_Helper_Overlay::render(
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
				'selector'       => "{$zoom_and_hover_selector} .et_overlay:before",
				'processor'      => array(
					'ET_Builder_Module_Helper_Style_Processor',
					'process_extended_icon',
				),
			)
		);

		$projects = self::get_portfolio_item(
			array(
				'posts_number'       => $posts_number,
				'include_categories' => $include_categories,
			)
		);

		$portfolio_order = self::_get_index( array( self::INDEX_MODULE_ORDER, $render_slug ) );
		$items_count     = 0;

		ob_start();
		if ( $projects->post_count > 0 ) {
			while ( $projects->have_posts() ) {
				$projects->the_post();
				ET_Post_Stack::replace( $post );

				$item_class = sprintf( 'et_pb_fullwidth_portfolio_item_%1$s_%2$s', $portfolio_order, $items_count );
				$items_count++;
				?>
				<div id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_portfolio_item et_pb_grid_item ' . $item_class ); ?>>
				<?php
					$thumb = '';

					$width = 510;
					$width = (int) apply_filters( 'et_pb_portfolio_image_width', $width );

					$height = 382;
					$height = (int) apply_filters( 'et_pb_portfolio_image_height', $height );

					list($thumb_src, $thumb_width, $thumb_height) = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), array( $width, $height ) );

					$orientation = ( $thumb_height > $thumb_width ) ? 'portrait' : 'landscape';

				if ( '' !== $thumb_src ) :
					?>
						<div class="et_pb_portfolio_image <?php echo esc_attr( $orientation ); ?>">
							<?php
							$image_attrs = array(
								'alt' => get_the_title(),
							);

							$full_src = get_the_post_thumbnail_url();

							if ( $full_src ) {
								$image_attrs['srcset'] = $full_src . ' 479w, ' . $thumb_src . ' 480w';
								$image_attrs['sizes']  = '(max-width:479px) 479px, 100vw';
							}

							$image_attachment_class = et_pb_media_options()->get_image_attachment_class( $this->props, '', get_post_thumbnail_id() );

							if ( ! empty( $image_attachment_class ) ) {
								$image_attrs['class'] = esc_attr( $image_attachment_class );
							}

							$this->render_image( $thumb_src, $image_attrs );
							?>
							<div class="meta">
							<a href="<?php esc_url( the_permalink() ); ?>">
							<?php
								echo et_core_esc_previously( $overlay );

								$multi_view->render_element(
									array(
										'tag'        => et_pb_process_header_level( $header_level, 'h3' ),
										'content'    => get_the_title(),
										'attrs'      => array(
											'class' => 'et_pb_module_header',
										),
										'visibility' => array(
											'show_title' => 'on',
										),
									),
									true
								);

								$multi_view->render_element(
									array(
										'tag'        => 'p',
										'content'    => get_the_date(),
										'attrs'      => array(
											'class' => 'post-meta',
										),
										'visibility' => array(
											'show_date' => 'on',
										),
									),
									true
								);
							?>
								</a>
							</div>
						</div>
				<?php endif; ?>
				</div>
				<?php
				ET_Post_Stack::pop();
			}
			ET_Post_Stack::reset();
		}

		if ( ! $posts = ob_get_clean() ) {
			$posts  = '<div class="et_pb_row et_pb_no_results">';
			$posts .= self::get_no_results_template();
			$posts .= '</div>';
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

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		// Module classnames
		if ( 'on' === $fullwidth ) {
			$this->add_classname( 'et_pb_fullwidth_portfolio_carousel' );
		} else {
			$this->add_classname(
				array(
					'et_pb_fullwidth_portfolio_grid',
					'clearfix',
				)
			);
		}

		$output = sprintf(
			'<div%3$s class="%1$s" data-auto-rotate="%4$s" data-auto-rotate-speed="%5$s"%9$s>
				%8$s
				%7$s
				%6$s
				%10$s
				%11$s
				<div class="et_pb_portfolio_items clearfix" data-portfolio-columns="">
					%2$s
				</div>
			</div>',
			$this->module_classname( $render_slug ),
			$posts,
			$this->module_id(),
			( '' !== $auto && in_array( $auto, array( 'on', 'off' ) ) ? esc_attr( $auto ) : 'off' ),
			( '' !== $auto_speed && is_numeric( $auto_speed ) ? esc_attr( $auto_speed ) : '7000' ), // #5
			$title,
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $this->background_pattern() ), // #10
			et_core_esc_previously( $this->background_mask() ) // #11
		);

		return $output;
	}

	/**
	 * Filter multi view value.
	 *
	 * @since 3.27.1
	 *
	 * @see ET_Builder_Module_Helper_MultiViewOptions::filter_value
	 *
	 * @param mixed                                     $raw_value Props raw value.
	 * @param array                                     $args {
	 *                                         Context data.
	 *
	 *     @type string $context      Context param: content, attrs, visibility, classes.
	 *     @type string $name         Module options props name.
	 *     @type string $mode         Current data mode: desktop, hover, tablet, phone.
	 *     @type string $attr_key     Attribute key for attrs context data. Example: src, class, etc.
	 *     @type string $attr_sub_key Attribute sub key that availabe when passing attrs value as array such as styes. Example: padding-top, margin-botton, etc.
	 * }
	 * @param ET_Builder_Module_Helper_MultiViewOptions $multi_view Multiview object instance.
	 *
	 * @return mixed
	 */
	public function multi_view_filter_value( $raw_value, $args, $multi_view ) {
		$name = isset( $args['name'] ) ? $args['name'] : '';
		$mode = isset( $args['mode'] ) ? $args['mode'] : '';

		$fields_need_escape = array(
			'title',
		);

		if ( $raw_value && in_array( $name, $fields_need_escape, true ) ) {
			return $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'none', $raw_value );
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Fullwidth_Portfolio();
}
