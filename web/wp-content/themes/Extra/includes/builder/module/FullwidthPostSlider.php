<?php

require_once 'helpers/Slider.php';

class ET_Builder_Module_Fullwidth_Post_Slider extends ET_Builder_Module_Type_PostBased {
	function init() {
		$this->name       = esc_html__( 'Fullwidth Post Slider', 'et_builder' );
		$this->plural     = esc_html__( 'Fullwidth Post Sliders', 'et_builder' );
		$this->slug       = 'et_pb_fullwidth_post_slider';
		$this->vb_support = 'on';
		$this->fullwidth  = true;

		// Save processed background so it can be modified & reapplied on another element
		$this->save_processed_background = true;

		// need to use global settings from the fullwidth slider module
		$this->global_settings_slug = 'et_pb_fullwidth_slider';

		$this->main_css_element = '%%order_class%%.et_pb_slider';

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content'   => et_builder_i18n( 'Content' ),
					'elements'       => et_builder_i18n( 'Elements' ),
					'featured_image' => esc_html__( 'Featured Image', 'et_builder' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'layout'     => et_builder_i18n( 'Layout' ),
					'overlay'    => et_builder_i18n( 'Overlay' ),
					'navigation' => esc_html__( 'Navigation', 'et_builder' ),
					'image'      => et_builder_i18n( 'Image' ),
					'text'       => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
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
				'header' => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} .et_pb_slide_description .et_pb_slide_title, {$this->main_css_element} .et_pb_slide_description .et_pb_slide_title a",
						'important' => array( 'size', 'font-size' ),
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'body'   => array(
					'label'          => et_builder_i18n( 'Body' ),
					'css'            => array(
						'line_height' => "{$this->main_css_element}",
						'main'        => "{$this->main_css_element} .et_pb_slide_content, {$this->main_css_element} .et_pb_slide_content div",
						'important'   => 'all',
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
						'css'               => array(
							'link'           => "{$this->main_css_element} .et_pb_slide_content a",
							'ul'             => "{$this->main_css_element} .et_pb_slide_content ul li",
							'ul_item_indent' => "{$this->main_css_element} .et_pb_slide_content ul",
							'ol'             => "{$this->main_css_element} .et_pb_slide_content ol li",
							'ol_item_indent' => "{$this->main_css_element} .et_pb_slide_content ol",
							'quote'          => "{$this->main_css_element} .et_pb_slide_content blockquote",
						),
					),
				),
				'meta'   => array(
					'label'          => esc_html__( 'Meta', 'et_builder' ),
					'css'            => array(
						'main'         => "{$this->main_css_element} .et_pb_slide_content .post-meta, {$this->main_css_element} .et_pb_slide_content .post-meta a",
						'limited_main' => "{$this->main_css_element} .et_pb_slide_content .post-meta, {$this->main_css_element} .et_pb_slide_content .post-meta a, {$this->main_css_element} .et_pb_slide_content .post-meta span",
						'important'    => 'all',
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'font_size'      => array(
						'default' => '16px',
					),
					'letter_spacing' => array(
						'default' => '0',
					),
				),
			),
			'button'          => array(
				'button' => array(
					'label'          => et_builder_i18n( 'Button' ),
					'css'            => array(
						'main'         => "{$this->main_css_element} .et_pb_more_button.et_pb_button",
						'limited_main' => "{$this->main_css_element} .et_pb_more_button.et_pb_button",
						'alignment'    => "{$this->main_css_element} .et_pb_button_wrapper",
					),
					'use_alignment'  => true,
					'box_shadow'     => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button',
						),
					),
					'margin_padding' => array(
						'css' => array(
							'important' => 'all',
						),
					),
				),
			),
			'box_shadow'      => array(
				'default' => array(
					'css' => array(
						'overlay' => 'inset',
					),
				),
				'image'   => array(
					'label'             => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'image',
					'css'               => array(
						'main' => '%%order_class%% .et_pb_slide_image img',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'background'      => array(
				'css'     => array(
					'main' => '%%order_class%%, %%order_class%%.et_pb_bg_layout_dark, %%order_class%%.et_pb_bg_layout_light',
				),
				'options' => array(
					'background_color'    => array(
						'default' => et_builder_accent_color(),
					),
					'background_position' => array(
						'default' => 'center',
					),
					'parallax_method'     => array(
						'default' => 'off',
					),
				),
			),
			'margin_padding'  => array(
				'css' => array(
					'main'      => '%%order_class%%',
					'padding'   => '%%order_class%% .et_pb_slide_description, .et_pb_slider_fullwidth_off%%order_class%% .et_pb_slide_description',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'text'            => array(
				'use_background_layout' => true,
				'css'                   => array(
					'main'             => implode(
						', ',
						array(
							'%%order_class%% .et_pb_slide .et_pb_slide_description .et_pb_slide_title',
							'%%order_class%% .et_pb_slide .et_pb_slide_description .et_pb_slide_title a',
							'%%order_class%% .et_pb_slide .et_pb_slide_description .et_pb_slide_content',
							'%%order_class%% .et_pb_slide .et_pb_slide_description .et_pb_slide_content .post-meta',
							'%%order_class%% .et_pb_slide .et_pb_slide_description .et_pb_slide_content .post-meta a',
							'%%order_class%% .et_pb_slide .et_pb_slide_description .et_pb_slide_content .et_pb_button',
						)
					),
					'text_orientation' => '%%order_class%% .et_pb_slide .et_pb_slide_description',
					'text_shadow'      => '%%order_class%% .et_pb_slide .et_pb_slide_description',
				),
				'options'               => array(
					'text_orientation'  => array(
						'default' => 'center',
					),
					'background_layout' => array(
						'default' => 'dark',
						'hover'   => 'tabs',
					),
				),
			),
			'borders'         => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii' => '%%order_class%%, %%order_class%% .et_pb_slide, %%order_class%% .et_pb_slide_overlay_container',
						),
					),
				),
				'image'   => array(
					'css'             => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .et_pb_slide .et_pb_slide_image img',
							'border_styles' => '%%order_class%% .et_pb_slide .et_pb_slide_image img',
						),
					),
					'label_prefix'    => et_builder_i18n( 'Image' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'depends_show_if' => 'off',
					'defaults'        => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
				),
			),
			'filters'         => array(
				'child_filters_target' => array(
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'           => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_slide_image',
				),
			),
			'height'          => array(
				'css' => array(
					'main' => '%%order_class%%, %%order_class%% .et_pb_slide',
				),
			),
			'max_width'       => array(
				'extra' => array(
					'content' => array(
						'use_module_alignment' => false,
						'css'                  => array(
							'main' => '%%order_class%% .et_pb_slide > .et_pb_container',
						),
						'options'              => array(
							'width'     => array(
								'label'   => esc_html__( 'Content Width', 'et_builder' ),
								'default' => '80%',
							),
							'max_width' => array(
								'label'   => esc_html__( 'Content Max Width', 'et_builder' ),
								'default' => '1080px',
							),
						),
					),
				),
			),
			'position_fields' => array(
				'default' => 'relative',
			),
		);

		$this->custom_css_fields = array(
			'slide_description'       => array(
				'label'    => esc_html__( 'Slide Description', 'et_builder' ),
				'selector' => '.et_pb_slide_description',
			),
			'slide_title'             => array(
				'label'    => esc_html__( 'Slide Title', 'et_builder' ),
				'selector' => '.et_pb_slide_description .et_pb_slide_title',
			),
			'slide_meta'              => array(
				'label'    => esc_html__( 'Slide Meta', 'et_builder' ),
				'selector' => '.et_pb_slide_description .post-meta',
			),
			'slide_button'            => array(
				'label'                    => esc_html__( 'Slide Button', 'et_builder' ),
				'selector'                 => '.et_pb_slider a.et_pb_more_button.et_pb_button',
				'no_space_before_selector' => true,
			),
			'slide_controllers'       => array(
				'label'    => esc_html__( 'Slide Controllers', 'et_builder' ),
				'selector' => '.et-pb-controllers',
			),
			'slide_active_controller' => array(
				'label'    => esc_html__( 'Slide Active Controller', 'et_builder' ),
				'selector' => '.et-pb-controllers .et-pb-active-control',
			),
			'slide_image'             => array(
				'label'    => esc_html__( 'Slide Image', 'et_builder' ),
				'selector' => '.et_pb_slide_image',
			),
			'slide_arrows'            => array(
				'label'    => esc_html__( 'Slide Arrows', 'et_builder' ),
				'selector' => '.et-pb-slider-arrows a',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'rDaVUZjDaGQ',
				'name' => esc_html__( 'An introduction to the Fullwidth Post Slider module', 'et_builder' ),
			),
		);
	}

	static function get_blog_posts( $args = array(), $conditional_tags = array(), $current_page = array(), $is_ajax_request = true ) {
		global $wp_query, $paged, $post;

		$defaults = array(
			'use_current_loop'   => 'off',
			'posts_number'       => '',
			'include_categories' => '',
			'orderby'            => '',
			'content_source'     => '',
			'use_manual_excerpt' => '',
			'excerpt_length'     => '',
			'offset_number'      => '',
		);

		$args = wp_parse_args( $args, $defaults );

		// Include query args that we don't control.
		$query_args = array_merge(
			array_diff_key( $args, $defaults ),
			array(
				'posts_per_page' => (int) $args['posts_number'],
				'post_status'    => array( 'publish', 'private' ),
				'perm'           => 'readable',
			)
		);

		if ( 'on' === $args['use_current_loop'] ) {
			// Reset loop-affecting values to their defaults to simulate the current loop.
			$reset_keys = array( 'include_categories', 'orderby' );

			foreach ( $reset_keys as $key ) {
				$args[ $key ] = $defaults[ $key ];
			}
		}

		if ( '' !== $args['include_categories'] ) {
			$query_args['cat'] = $args['include_categories'];
		}

		// WP_Query doesn't return sticky posts when it performed via Ajax.
		// This happens because `is_home` is false in this case, but on FE it's true if no category set for the query.
		// Set `is_home` = true to emulate the FE behavior with sticky posts in VB.
		if ( empty( $query_args['cat'] ) ) {
			add_action(
				'pre_get_posts',
				function( $query ) {
					if ( true === $query->get( 'et_is_home' ) ) {
						$query->is_home = true;
					}
				}
			);

			$query_args['et_is_home'] = true;
		}

		if ( 'date_desc' !== $args['orderby'] ) {
			switch ( $args['orderby'] ) {
				case 'date_asc':
					$query_args['orderby'] = 'date';
					$query_args['order']   = 'ASC';
					break;
				case 'title_asc':
					$query_args['orderby'] = 'title';
					$query_args['order']   = 'ASC';
					break;
				case 'title_desc':
					$query_args['orderby'] = 'title';
					$query_args['order']   = 'DESC';
					break;
				case 'rand':
					$query_args['orderby'] = 'rand';
					break;
			}
		}

		if ( '' !== $args['offset_number'] && ! empty( $args['offset_number'] ) ) {
			/**
			 * Offset + pagination don't play well. Manual offset calculation required
			 *
			 * @see: https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
			 */
			if ( $paged > 1 ) {
				$query_args['offset'] = ( ( $paged - 1 ) * intval( $args['posts_number'] ) ) + intval( $args['offset_number'] );
			} else {
				$query_args['offset'] = intval( $args['offset_number'] );
			}
		}

		$query = new WP_Query( $query_args );
		// Keep page's $wp_query global
		$wp_query_page = $wp_query;

		// Turn page's $wp_query into this module's query
		$wp_query = $query; //phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited

		if ( $query->have_posts() ) {
			$post_index = 0;
			while ( $query->have_posts() ) {
				$query->the_post();
				ET_Post_Stack::replace( $post );

				$post_author_id = $query->posts[ $post_index ]->post_author;

				$categories = array();

				$categories_object = get_the_terms( get_the_ID(), 'category' );

				if ( ! empty( $categories_object ) ) {
					foreach ( $categories_object as $category ) {
						$categories[] = array(
							'id'        => $category->term_id,
							'label'     => $category->name,
							'permalink' => get_term_link( $category ),
						);
					}
				}

				$has_post_thumbnail = has_post_thumbnail();

				$query->posts[ $post_index ]->has_post_thumbnail  = $has_post_thumbnail;
				$query->posts[ $post_index ]->post_thumbnail      = $has_post_thumbnail ? get_the_post_thumbnail() : '';
				$query->posts[ $post_index ]->post_featured_image = $has_post_thumbnail ? esc_url( wp_get_attachment_url( get_post_thumbnail_id() ) ) : '';
				$query->posts[ $post_index ]->post_permalink      = get_the_permalink();
				$query->posts[ $post_index ]->post_author_url     = get_author_posts_url( $post_author_id );
				$query->posts[ $post_index ]->post_author_name    = get_the_author_meta( 'display_name', $post_author_id );
				$query->posts[ $post_index ]->post_date_readable  = get_the_date();
				$query->posts[ $post_index ]->categories          = $categories;
				$query->posts[ $post_index ]->post_comment_popup  = et_core_maybe_convert_to_utf_8( sprintf( esc_html( _nx( '%s Comment', '%s Comments', get_comments_number(), 'number of comments', 'et_builder' ) ), number_format_i18n( get_comments_number() ) ) );

				$post_content = et_strip_shortcodes( get_the_content(), true );

				global $et_fb_processing_shortcode_object, $et_pb_rendering_column_content;

				$global_processing_original_value = $et_fb_processing_shortcode_object;

				// reset the fb processing flag
				$et_fb_processing_shortcode_object = false;
				// set the flag to indicate that we're processing internal content
				$et_pb_rendering_column_content = true;

				if ( $is_ajax_request ) {
					// reset all the attributes required to properly generate the internal styles
					ET_Builder_Element::clean_internal_modules_styles();
				}

				if ( 'both' === $args['content_source'] ) {
					global $more;

					// Page builder doesn't support more tag, so display the_content() in case of post made with page builder.
					if ( et_pb_is_pagebuilder_used( get_the_ID() ) || has_block( 'divi/layout', get_the_ID() ) ) {

						// do_shortcode for Divi Plugin instead of applying `the_content` filter to avoid conflicts with 3rd party themes.
						$builder_post_content = et_is_builder_plugin_active() ? do_shortcode( $post_content ) : apply_filters( 'the_content', $post_content );

						// Overwrite default content, in case the content is protected.
						$query->posts[ $post_index ]->post_content = $builder_post_content;
					} else {
						$more = null; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited

						// Overwrite default content, in case the content is protected.
						$query->posts[ $post_index ]->post_content = et_is_builder_plugin_active() ? do_shortcode( get_the_content( '' ) ) : apply_filters( 'the_content', get_the_content( '' ) );
					}

					if ( has_excerpt() && 'off' !== $args['use_manual_excerpt'] ) {
						$query->posts[ $post_index ]->post_excerpt = et_is_builder_plugin_active() ? do_shortcode( et_strip_shortcodes( get_the_excerpt(), true ) ) : apply_filters( 'the_content', et_strip_shortcodes( get_the_excerpt(), true ) );
					} else {
						$query->posts[ $post_index ]->post_excerpt = strip_shortcodes( truncate_post( intval( $args['excerpt_length'] ), false, '', true ) );
					}
				} elseif ( 'on' === $args['content_source'] ) {
					global $more;

					// page builder doesn't support more tag, so display the_content() in case of post made with page builder.
					if ( et_pb_is_pagebuilder_used( get_the_ID() || has_block( 'divi/layout', get_the_ID() ) ) ) {

						// do_shortcode for Divi Plugin instead of applying `the_content` filter to avoid conflicts with 3rd party themes.
						$builder_post_content = et_is_builder_plugin_active() ? do_shortcode( $post_content ) : apply_filters( 'the_content', $post_content );

						// Overwrite default content, in case the content is protected.
						$query->posts[ $post_index ]->post_content = $builder_post_content;
					} else {
						$more = null; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited

						// Overwrite default content, in case the content is protected.
						$query->posts[ $post_index ]->post_content = et_is_builder_plugin_active() ? do_shortcode( get_the_content( '' ) ) : apply_filters( 'the_content', get_the_content( '' ) );
					}
				} else {
					if ( has_excerpt() && 'off' !== $args['use_manual_excerpt'] ) {
						$query->posts[ $post_index ]->post_content = et_is_builder_plugin_active() ? do_shortcode( et_strip_shortcodes( get_the_excerpt(), true ) ) : apply_filters( 'the_content', et_strip_shortcodes( get_the_excerpt(), true ) );
					} else {
						$query->posts[ $post_index ]->post_content = strip_shortcodes( truncate_post( intval( $args['excerpt_length'] ), false, '', true ) );
					}
				}

				$et_fb_processing_shortcode_object = $global_processing_original_value;

				if ( $is_ajax_request ) {
					// retrieve the styles for the modules inside Blog content
					$internal_style = ET_Builder_Element::get_style( true );

					// reset all the attributes after we retrieved styles
					ET_Builder_Element::clean_internal_modules_styles( false );

					$query->posts[ $post_index ]->internal_styles = $internal_style;
				}

				$et_pb_rendering_column_content = false;

				ET_Post_Stack::pop();
				$post_index++;
			} // end while
			ET_Post_Stack::reset();
		} elseif ( self::is_processing_computed_prop() ) {
			// This is for the VB
			$query  = '<div class="et_pb_row et_pb_no_results">';
			$query .= self::get_no_results_template();
			$query .= '</div>';

			$query = array( 'posts' => $query );
		}

		// Reset $wp_query to its origin
		$wp_query = $wp_query_page; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited

		return $query;
	}

	function get_fields() {
		$fields = array(
			'use_current_loop'        => array(
				'label'            => esc_html__( 'Posts For Current Page', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'description'      => esc_html__( 'Display posts for the current page. Useful on archive and index pages.', 'et_builder' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'main_content',
				'default'          => 'off',
				'show_if'          => array(
					'function.isTBLayout' => 'on',
				),
			),
			'posts_number'            => array(
				'label'            => esc_html__( 'Post Count', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'Choose how many posts you would like to display in the slider.', 'et_builder' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'main_content',
			),
			'include_categories'      => array(
				'label'            => esc_html__( 'Included Categories', 'et_builder' ),
				'type'             => 'categories',
				'meta_categories'  => array(
					'all'     => esc_html__( 'All Categories', 'et_builder' ),
					'current' => esc_html__( 'Current Category', 'et_builder' ),
				),
				'option_category'  => 'basic_option',
				'renderer_options' => array(
					'use_terms' => false,
				),
				'description'      => esc_html__( 'Choose which categories you would like to include in the slider.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__posts',
				),
				'show_if'          => array(
					'use_current_loop' => 'off',
				),
			),
			'orderby'                 => array(
				'label'            => esc_html__( 'Order', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'date_desc'  => esc_html__( 'Date: new to old', 'et_builder' ),
					'date_asc'   => esc_html__( 'Date: old to new', 'et_builder' ),
					'title_asc'  => esc_html__( 'Title: a-z', 'et_builder' ),
					'title_desc' => esc_html__( 'Title: z-a', 'et_builder' ),
					'rand'       => esc_html__( 'Random', 'et_builder' ),
				),
				'default'          => 'date_desc',
				'description'      => esc_html__( 'Here you can adjust the order in which posts are displayed.', 'et_builder' ),
				'computed_affects' => array(
					'__posts',
				),
				'show_if'          => array(
					'use_current_loop' => 'off',
				),
				'toggle_slug'      => 'main_content',
			),
			'show_arrows'             => array(
				'label'            => esc_html__( 'Show Arrows', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'This setting will turn on and off the navigation arrows.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_pagination'         => array(
				'label'            => esc_html__( 'Show Controls', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'This setting will turn on and off the circle buttons at the bottom of the slider.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_more_button'        => array(
				'label'            => esc_html__( 'Show Read More Button', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'affects'          => array(
					'more_text',
				),
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'This setting will turn on and off the read more button.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'more_text'               => array(
				'label'            => et_builder_i18n( 'Button' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'default_on_front' => esc_html__( 'Read More', 'et_builder' ),
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'main_content',
				'dynamic_content'  => 'text',
				'description'      => esc_html__( 'Define the text which will be displayed on "Read More" button. Leave blank for default ( Read More )', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'content_source'          => array(
				'label'            => esc_html__( 'Content Display', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'Show Excerpt', 'et_builder' ),
					'on'  => esc_html__( 'Show Content', 'et_builder' ),
				),
				'default'          => 'off',
				'affects'          => array(
					'use_manual_excerpt',
					'excerpt_length',
				),
				'description'      => esc_html__( 'Showing the full content will not truncate your posts in the slider. Showing the excerpt will only display excerpt text.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__posts',
				),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'use_manual_excerpt'      => array(
				'label'            => esc_html__( 'Use Post Excerpts', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default'          => 'on',
				'depends_show_if'  => 'off',
				'description'      => esc_html__( 'Disable this option if you want to ignore manually defined excerpts and always generate it automatically.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__posts',
				),
			),
			'excerpt_length'          => array(
				'label'            => esc_html__( 'Excerpt Length', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'default_on_front' => '270',
				'depends_show_if'  => 'off',
				'description'      => esc_html__( 'Define the length of automatically generated excerpts. Leave blank for default ( 270 ) ', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__posts',
				),
			),
			'show_meta'               => array(
				'label'            => esc_html__( 'Show Post Meta', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					// Uses cached uppercase translation but keeps the lowercase not change definition content.
					'on'  => strtolower( et_builder_i18n( 'Yes' ) ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_fron'  => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'This setting will turn on and off the meta section.', 'et_builder' ),
				'default_on_front' => 'on',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_image'              => array(
				'label'            => esc_html__( 'Show Featured Image', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					// Uses cached uppercase translation but keeps the lowercase not change definition content.
					'on'  => strtolower( et_builder_i18n( 'Yes' ) ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'affects'          => array(
					'image_placement',
				),
				'toggle_slug'      => 'featured_image',
				'description'      => esc_html__( 'This setting will turn on and off the featured image in the slider.', 'et_builder' ),

				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'image_placement'         => array(
				'label'            => esc_html__( 'Featured Image Placement', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'background' => et_builder_i18n( 'Background' ),
					'left'       => et_builder_i18n( 'Left' ),
					'right'      => et_builder_i18n( 'Right' ),
					'top'        => et_builder_i18n( 'Top' ),
					'bottom'     => et_builder_i18n( 'Bottom' ),
				),
				'default_on_front' => 'background',
				'depends_show_if'  => 'on',
				'toggle_slug'      => 'featured_image',
				'description'      => esc_html__( 'Select how you would like to display the featured image in slides', 'et_builder' ),
			),
			'use_bg_overlay'          => array(
				'label'            => esc_html__( 'Use Background Overlay', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'affects'          => array(
					'bg_overlay_color',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'description'      => esc_html__( 'When enabled, a custom overlay color will be added above your background image and behind your slider content.', 'et_builder' ),
			),
			'bg_overlay_color'        => array(
				'label'           => esc_html__( 'Background Overlay Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'Use the color picker to choose a color for the background overlay.', 'et_builder' ),
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'use_text_overlay'        => array(
				'label'           => esc_html__( 'Use Text Overlay', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => et_builder_i18n( 'No' ),
					// Uses cached uppercase translation but keeps the lowercase not change definition content.
					'on'  => strtolower( et_builder_i18n( 'Yes' ) ),
				),
				'affects'         => array(
					'text_overlay_color',
					'text_border_radius',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'When enabled, a background color is added behind the slider text to make it more readable atop background images.', 'et_builder' ),
			),
			'text_overlay_color'      => array(
				'label'           => esc_html__( 'Text Overlay Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'Use the color picker to choose a color for the text overlay.', 'et_builder' ),
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'show_content_on_mobile'  => array(
				'label'            => esc_html__( 'Show Content On Mobile', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'tab_slug'         => 'custom_css',
				'toggle_slug'      => 'visibility',
			),
			'show_cta_on_mobile'      => array(
				'label'            => esc_html__( 'Show CTA On Mobile', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'tab_slug'         => 'custom_css',
				'toggle_slug'      => 'visibility',
			),
			'show_image_video_mobile' => array(
				'label'            => esc_html__( 'Show Image On Mobile', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default_on_front' => 'off',
				'tab_slug'         => 'custom_css',
				'toggle_slug'      => 'visibility',
			),
			'text_border_radius'      => array(
				'label'            => esc_html__( 'Text Overlay Border Radius', 'et_builder' ),
				'description'      => esc_html__( 'Increasing the border radius will increase the roundness of the overlay corners. Setting this value to 0 will result in squared corners.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'default'          => '3',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default_unit'     => 'px',
				'default_on_front' => '',
				'range_settings'   => array(
					'min'  => '0',
					'max'  => '100',
					'step' => '1',
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'mobile_options'   => true,
				'sticky'           => true,
			),
			'arrows_custom_color'     => array(
				'label'          => esc_html__( 'Arrow Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the slider arrows that are used to navigate through each slide.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'navigation',
				'mobile_options' => true,
				'sticky'         => true,
				'hover'          => 'tabs',
			),
			'dot_nav_custom_color'    => array(
				'label'          => esc_html__( 'Dot Navigation Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the dot navigation that appears at the bottom of the slider to designate which slide is active.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'navigation',
				'mobile_options' => true,
				'sticky'         => true,
				'hover'          => 'tabs',
			),
			'__posts'                 => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Fullwidth_Post_Slider', 'get_blog_posts' ),
				'computed_depends_on' => array(
					'use_current_loop',
					'posts_number',
					'include_categories',
					'orderby',
					'content_source',
					'use_manual_excerpt',
					'excerpt_length',
					'offset_number',
				),
			),
			'offset_number'           => array(
				'label'            => esc_html__( 'Post Offset Number', 'et_builder' ),
				'description'      => esc_html__( 'Choose how many posts you would like to offset by', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__posts',
				),
				'default'          => 0,
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields                      = parent::get_transition_fields_css_props();
		$fields['background_layout'] = array(
			'background-color' => '%%order_class%% .et_pb_slide_overlay_container, %%order_class%% .et_pb_text_overlay_wrapper',
			'color'            => self::$_->array_get( $this->advanced_fields, 'text.css.main', '%%order_class%%' ),
		);

		$fields['bg_overlay_color']     = array( 'background-color' => '%%order_class%% .et_pb_slide .et_pb_slide_overlay_container' );
		$fields['text_overlay_color']   = array( 'background-color' => '%%order_class%% .et_pb_slide .et_pb_text_overlay_wrapper' );
		$fields['text_border_radius']   = array( 'border-radius' => '%%order_class%%.et_pb_slider_with_text_overlay .et_pb_text_overlay_wrapper' );
		$fields['dot_nav_custom_color'] = array( 'background-color' => et_pb_slider_options()->get_dots_selector() );
		$fields['arrows_custom_color']  = array( 'all' => et_pb_slider_options()->get_arrows_selector() );

		return $fields;
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

		/**
		 * Cached $wp_filter so it can be restored at the end of the callback.
		 * This is needed because this callback uses the_content filter / calls a function
		 * which uses the_content filter. WordPress doesn't support nested filter
		 */
		global $wp_filter;
		$wp_filter_cache         = $wp_filter;
		$multi_view              = et_pb_multi_view_options( $this );
		$use_current_loop        = isset( $this->props['use_current_loop'] ) ? $this->props['use_current_loop'] : 'off';
		$show_arrows             = $this->props['show_arrows'];
		$show_pagination         = $this->props['show_pagination'];
		$parallax                = $this->props['parallax'];
		$parallax_method         = $this->props['parallax_method'];
		$auto                    = $this->props['auto'];
		$auto_speed              = $this->props['auto_speed'];
		$auto_ignore_hover       = $this->props['auto_ignore_hover'];
		$body_font_size          = $this->props['body_font_size'];
		$show_content_on_mobile  = $this->props['show_content_on_mobile'];
		$show_cta_on_mobile      = $this->props['show_cta_on_mobile'];
		$show_image_video_mobile = $this->props['show_image_video_mobile'];
		$background_position     = $this->props['background_position'];
		$background_size         = $this->props['background_size'];
		$posts_number            = $this->props['posts_number'];
		$include_categories      = $this->props['include_categories'];
		$more_text               = $this->props['more_text'];
		$background_color        = $this->props['background_color'];
		$show_image              = $this->props['show_image'];
		$image_placement         = $this->props['image_placement'];
		$background_image        = $this->props['background_image'];
		$background_repeat       = $this->props['background_repeat'];
		$background_blend        = $this->props['background_blend'];
		$use_bg_overlay          = $this->props['use_bg_overlay'];
		$use_text_overlay        = $this->props['use_text_overlay'];
		$orderby                 = $this->props['orderby'];
		$button_custom           = $this->props['custom_button'];
		$button_rel              = $this->props['button_rel'];
		$use_manual_excerpt      = $this->props['use_manual_excerpt'];
		$excerpt_length          = $this->props['excerpt_length'];
		$header_level            = $this->props['header_level'];
		$offset_number           = $this->props['offset_number'];

		$use_gradient_options    = $this->props['use_background_color_gradient'];
		$gradient_overlays_image = $this->props['background_color_gradient_overlays_image'];

		$background_options        = et_pb_background_options();
		$gradient_properties       = $background_options->get_gradient_properties( $this->props, 'background', '' );
		$background_gradient_style = $background_options->get_gradient_style( $gradient_properties );
		$is_gradient_on            = false;

		if ( 'on' === $use_gradient_options && 'on' === $gradient_overlays_image && 'on' === $parallax ) {
			$is_gradient_on = '' !== $background_gradient_style;
		}

		$custom_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'button_icon' );
		$custom_icon        = isset( $custom_icon_values['desktop'] ) ? $custom_icon_values['desktop'] : '';
		$custom_icon_tablet = isset( $custom_icon_values['tablet'] ) ? $custom_icon_values['tablet'] : '';
		$custom_icon_phone  = isset( $custom_icon_values['phone'] ) ? $custom_icon_values['phone'] : '';

		$post_index = 0;

		$hide_on_mobile_class = self::HIDE_ON_MOBILE;

		$is_text_overlay_applied = 'on' === $use_text_overlay;

		if ( 'on' === $use_bg_overlay ) {
			// Background Overlay color.
			$this->generate_styles(
				array(
					'hover'          => false,
					'base_attr_name' => 'bg_overlay_color',
					'selector'       => '%%order_class%% .et_pb_slide .et_pb_slide_overlay_container',
					'css_property'   => 'background-color',
					'render_slug'    => $render_slug,
					'type'           => 'color',
				)
			);
		}

		if ( $is_text_overlay_applied ) {
			// Text Overlay color.
			$this->generate_styles(
				array(
					'hover'          => false,
					'base_attr_name' => 'text_overlay_color',
					'selector'       => '%%order_class%% .et_pb_slide .et_pb_text_overlay_wrapper',
					'css_property'   => 'background-color',
					'render_slug'    => $render_slug,
					'type'           => 'color',
				)
			);
		}

		// Text Border Radius.
		$this->generate_styles(
			array(
				'hover'          => false,
				'base_attr_name' => 'text_border_radius',
				'selector'       => '%%order_class%%.et_pb_slider_with_text_overlay .et_pb_text_overlay_wrapper',
				'css_property'   => 'border-radius',
				'render_slug'    => $render_slug,
				'type'           => 'range',
			)
		);

		// Arrow Custom Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'arrows_custom_color',
				'selector'                        => et_pb_slider_options()->get_arrows_selector(),
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Dot Navigation Custom Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'dot_nav_custom_color',
				'selector'                        => et_pb_slider_options()->get_dots_selector(),
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'background-color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$content_source_both = count( $multi_view->get_values( 'content_source', true ) ) > 1;
		$is_show_image       = $multi_view->has_value( 'show_image', 'on' );
		$is_show_meta        = $multi_view->has_value( 'show_meta', 'on' );

		$multi_view_attrs_button = $multi_view->render_attrs(
			array(
				'content'    => '{{more_text}}',
				'visibility' => array(
					'more_text'        => '__not_empty',
					'show_more_button' => 'on',
				),
			)
		);

		$multi_view_attrs_show_meta = $multi_view->render_attrs(
			array(
				'visibility' => array(
					'show_meta' => 'on',
				),
			)
		);

		$multi_view_attrs_show_image = $multi_view->render_attrs(
			array(
				'visibility' => array(
					'show_image' => 'on',
				),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );

		ob_start();

		$show_no_results_template = true;

		$args = array(
			'posts_number'       => $posts_number,
			'include_categories' => implode( ',', self::filter_include_categories( $include_categories ) ),
			'orderby'            => $orderby,
			'content_source'     => $content_source_both ? 'both' : $multi_view->get_value( 'content_source' ),
			'use_manual_excerpt' => $use_manual_excerpt,
			'excerpt_length'     => $excerpt_length,
			'offset_number'      => $offset_number,
		);

		if ( 'on' === $use_current_loop ) {
			if ( ! have_posts() || is_singular() ) {
				// Force an empty result set in order to avoid loops over the current post.
				$args                     = array( 'post__in' => array( 0 ) );
				$show_no_results_template = false;
			} else {
				// Only allow certain args when `Posts For Current Page` is set.
				global $wp_query;
				$original = $wp_query->query_vars;
				$allowed  = array(
					'posts_number',
					'content_source',
					'use_manual_excerpt',
					'excerpt_length',
					'offset_number',
				);
				$custom   = array_intersect_key( $args, array_flip( $allowed ) );
				$args     = array_merge( $original, $custom );
			}
		}

		$query = self::get_blog_posts( $args, array(), array(), false );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				ET_Post_Stack::replace( $post );

				$has_post_thumbnail = $query->posts[ $post_index ]->has_post_thumbnail;

				$multi_view->set_custom_prop( 'content_source_both', $content_source_both );
				if ( $content_source_both ) {
					$multi_view->set_custom_prop( 'post_excerpt', $query->posts[ $post_index ]->post_excerpt );
				}
				$multi_view->set_custom_prop( 'post_content', $query->posts[ $post_index ]->post_content );
				$multi_view->set_custom_prop( 'has_post_thumbnail', $query->posts[ $post_index ]->has_post_thumbnail );
				$multi_view->set_custom_prop( 'post_featured_image', $query->posts[ $post_index ]->post_featured_image );
				$multi_view->set_custom_prop( 'background_image_featured', $multi_view->get_values( 'show_image', false ) );
				$multi_view->set_custom_prop( 'background_image_parallax', $multi_view->get_values( 'show_image', false ) );

				$multi_view_attrs_wrapper = $multi_view->render_attrs(
					array(
						'styles'  => array(
							'background-image' => '{{background_image_featured}}',
						),
						'classes' => array(
							'et_pb_slide_with_image'    => array(
								'show_image'         => 'on',
								'has_post_thumbnail' => true,
								'image_placement'    => array( 'left', 'right' ),
							),
							'et_pb_slide_with_no_image' => array(
								'show_image'         => 'on',
								'has_post_thumbnail' => false,
							),
						),
					)
				);

				$multi_view_attrs_parallax_bg = 'on' === $parallax ? $multi_view->render_attrs(
					array(
						'styles' => array(
							'background-image' => '{{background_image_parallax}}',
						),
					)
				) : '';

				$slide_class  = 'off' !== $show_image && in_array( $image_placement, array( 'left', 'right' ), true ) && $has_post_thumbnail ? ' et_pb_slide_with_image et_pb_media_alignment_center' : '';
				$slide_class .= 'off' !== $show_image && ! $has_post_thumbnail ? ' et_pb_slide_with_no_image' : '';
				$slide_class .= ' ' . implode( ' ', $background_layout_class_names );

				// Reapply module's gradient background on slide item with featured image
				$slide_post_id = $query->posts[ $post_index ]->ID;
				$slide_class  .= " et_pb_post_slide-{$slide_post_id}";

				ET_Builder_Module_Helper_Slider::reapply_module_background_on_slide(
					array(
						'slide_post_id'       => $slide_post_id,
						'post_featured_image' => $query->posts[ $post_index ]->post_featured_image,
						'render_slug'         => $render_slug,
						'props'               => $this->props,
					)
				);

				$should_apply_bg_image = $is_show_image && 'background' === $image_placement;
				$inline_background     = $should_apply_bg_image && $query->posts[ $post_index ]->post_featured_image ? 'style="background-image: url(' . esc_url( $query->posts[ $post_index ]->post_featured_image ) . ');"' : '';
				?>
			<div class="et_pb_slide<?php echo esc_attr( $slide_class ); ?>" <?php echo et_core_esc_previously( $multi_view_attrs_wrapper ); ?> <?php echo et_core_esc_previously( $inline_background ); ?>>
				<?php if ( 'on' === $parallax && $should_apply_bg_image ) { ?>
					<div class="et_parallax_bg_wrap">
						<div class="et_parallax_bg
						<?php
						if ( 'off' === $parallax_method ) {
							echo ' et_pb_parallax_css'; }
						?>
						" style="background-image: url(<?php echo esc_url( $query->posts[ $post_index ]->post_featured_image ); ?>);"<?php echo et_core_esc_previously( $multi_view_attrs_parallax_bg ); ?>></div>
						<?php
						if ( $is_gradient_on ) {
							printf(
								'<span class="et_parallax_gradient" style="%1$s%2$s"></span>',
								sprintf(
									'background-image: %1$s;',
									esc_html( $background_gradient_style )
								),
								( '' !== $background_blend && 'normal' !== $background_blend ) ? sprintf(
									'mix-blend-mode: %1$s;',
									esc_html( $background_blend )
								) : ''
							);
						}
						?>
					</div>
				<?php } ?>
				<?php if ( 'on' === $use_bg_overlay ) { ?>
					<div class="et_pb_slide_overlay_container"></div>
				<?php } ?>
				<div class="et_pb_container clearfix">
					<div class="et_pb_slider_container_inner">
						<?php if ( $is_show_image && $has_post_thumbnail && ! in_array( $image_placement, array( 'background', 'bottom' ) ) ) { ?>
							<div class="et_pb_slide_image"<?php echo et_core_esc_previously( $multi_view_attrs_show_image ); ?>>
								<?php the_post_thumbnail(); ?>
							</div>
						<?php } ?>
						<div class="et_pb_slide_description">
							<?php
							if ( $is_text_overlay_applied ) :
								?>
								<div class="et_pb_text_overlay_wrapper"><?php endif; ?>
								<<?php echo et_pb_process_header_level( $header_level, 'h2' ); ?> class="et_pb_slide_title"><a href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></<?php echo et_pb_process_header_level( $header_level, 'h2' ); ?>>
								<div class="et_pb_slide_content
								<?php
								if ( 'on' !== $show_content_on_mobile ) {
									echo esc_attr( $hide_on_mobile_class ); }
								?>
								">
									<?php
									if ( $is_show_meta ) {
										printf(
											'<p class="post-meta"%5$s>%1$s | %2$s | %3$s | %4$s</p>',
											et_get_safe_localization( sprintf( __( 'by %s', 'et_builder' ), '<span class="author vcard">' . et_pb_get_the_author_posts_link() . '</span>' ) ),
											et_get_safe_localization( sprintf( __( '%s', 'et_builder' ), '<span class="published">' . esc_html( get_the_date() ) . '</span>' ) ),
											get_the_category_list( ', ' ),
											esc_html( sprintf( _nx( '%s Comment', '%s Comments', get_comments_number(), 'number of comments', 'et_builder' ), number_format_i18n( get_comments_number() ) ) ),
											$multi_view_attrs_show_meta
										);
									}

									$multi_view->render_element(
										array(
											'tag'     => 'div',
											'content' => '{{content_source}}',
										),
										true
									);
									?>
								</div>
							<?php
							if ( $is_text_overlay_applied ) :
								?>
								</div><?php endif; ?>
							<?php
								// render button
								$button_classname = array( 'et_pb_more_button' );

							if ( 'on' !== $show_cta_on_mobile ) {
								$button_classname[] = $hide_on_mobile_class;
							}

								echo et_core_esc_previously(
									$this->render_button(
										array(
											'button_classname' => $button_classname,
											'button_custom' => $button_custom,
											'button_rel'  => $button_rel,
											'button_text' => $more_text,
											'button_url'  => get_permalink(),
											'custom_icon' => $custom_icon,
											'custom_icon_tablet' => $custom_icon_tablet,
											'custom_icon_phone' => $custom_icon_phone,
											'display_button' => $multi_view->has_value( 'show_more_button', 'on' ) && $multi_view->has_value( 'more_text' ),
											'multi_view_data' => $multi_view->render_attrs(
												array(
													'content' => '{{more_text}}',
													'visibility' => array(
														'more_text' => '__not_empty',
														'show_more_button' => 'on',
													),
												)
											),
										)
									)
								);
							?>
						</div>
						<?php if ( $is_show_image && $has_post_thumbnail && 'bottom' === $image_placement ) { ?>
							<div class="et_pb_slide_image"<?php echo et_core_esc_previously( $multi_view_attrs_show_image ); ?>>
								<?php the_post_thumbnail(); ?>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
				<?php
				$post_index++;
				ET_Post_Stack::pop();
			} // end while
			ET_Post_Stack::reset();
		} // end if

		$content = ob_get_clean();
		if ( ! $content && $show_no_results_template ) {
			$content  = '<div class="et_pb_row et_pb_no_results">';
			$content .= self::get_no_results_template();
			$content .= '</div>';
		}

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		// Module classnames
		$this->add_classname(
			array(
				'et_pb_slider',
				'et_pb_post_slider',
				"et_pb_post_slider_image_{$image_placement}",
			)
		);

		if ( 'on' !== $show_arrows ) {
			$this->add_classname( 'et_pb_slider_no_arrows' );
		}

		if ( 'on' !== $show_pagination ) {
			$this->add_classname( 'et_pb_slider_no_pagination' );
		}

		if ( 'on' === $parallax ) {
			$this->add_classname( 'et_pb_slider_parallax' );
		}

		if ( 'on' === $auto ) {
			$this->add_classname(
				array(
					'et_slider_auto',
					"et_slider_speed_{$auto_speed}",
				)
			);
		}

		if ( 'on' === $auto_ignore_hover ) {
			$this->add_classname( 'et_slider_auto_ignore_hover' );
		}

		if ( 'on' === $show_image_video_mobile ) {
			$this->add_classname( 'et_pb_slider_show_image' );
		}

		if ( 'on' === $use_bg_overlay ) {
			$this->add_classname( 'et_pb_slider_with_overlay' );
		}

		if ( 'on' === $use_text_overlay ) {
			$this->add_classname( 'et_pb_slider_with_text_overlay' );
		}

		$muti_view_data_attr = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_slider_no_arrows'             => array(
						'show_arrows' => 'off',
					),
					'et_pb_slider_no_pagination'         => array(
						'show_pagination' => 'off',
					),
					'et_pb_post_slider_image_background' => array(
						'image_placement' => 'background',
					),
					'et_pb_post_slider_image_left'       => array(
						'image_placement' => 'left',
					),
					'et_pb_post_slider_image_right'      => array(
						'image_placement' => 'right',
					),
					'et_pb_post_slider_image_top'        => array(
						'image_placement' => 'top',
					),
					'et_pb_post_slider_image_bottom'     => array(
						'image_placement' => 'bottom',
					),
				),
			)
		);

		$output = sprintf(
			'<div%3$s class="%1$s"%7$s%8$s>
				%5$s
				%4$s
				%9$s
				%10$s
				<div class="et_pb_slides">
					%2$s
				</div>
				%6$s
			</div>
			',
			$this->module_classname( $render_slug ),
			$content,
			$this->module_id(),
			$video_background,
			$parallax_image_background, // #5
			$this->inner_shadow_back_compatibility( $render_slug ),
			et_core_esc_previously( $data_background_layout ),
			$muti_view_data_attr,
			et_core_esc_previously( $this->background_pattern() ), // #9
			et_core_esc_previously( $this->background_mask() ) // #10
		);

		// Restore $wp_filter
		$wp_filter = $wp_filter_cache; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		unset( $wp_filter_cache );

		return $output;
	}

	private function inner_shadow_back_compatibility( $functions_name ) {
		$utils = ET_Core_Data_Utils::instance();
		$atts  = $this->props;
		$style = '';

		if (
			version_compare( $utils->array_get( $atts, '_builder_version', '3.0.93' ), '3.0.99', 'lt' )
		) {
			$class = self::get_module_order_class( $functions_name );
			$style = sprintf(
				'<style>%1$s</style>',
				sprintf(
					'.%1$s.et_pb_slider .et_pb_slide {'
					. '-webkit-box-shadow: none; '
					. '-moz-box-shadow: none; '
					. 'box-shadow: none; '
					. '}',
					esc_attr( $class )
				)
			);

			if ( 'off' !== $utils->array_get( $atts, 'show_inner_shadow' ) ) {
				$style .= sprintf(
					'<style>%1$s</style>',
					sprintf(
						'.%1$s > .box-shadow-overlay { '
						. '-webkit-box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1); '
						. '-moz-box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1); '
						. 'box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.1); '
						. '}',
						esc_attr( $class )
					)
				);
			}
		}

		return $style;
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
		$name = et_()->array_get( $args, 'name', '' );

		if ( 'background_image_featured' === $name ) {
			$image_placement = et_()->array_get( $this->props, 'image_placement', '' );
			$parallax        = et_()->array_get( $this->props, 'parallax', '' );

			if ( 'on' !== $raw_value || 'background' !== $image_placement || 'on' === $parallax || ! $multi_view->get_value( 'has_post_thumbnail' ) ) {
				return 'none';
			}

			return sprintf( 'url(%1$s)', $multi_view->get_value( 'post_featured_image' ) );
		} elseif ( 'background_image_parallax' === $name ) {
			$image_placement = et_()->array_get( $this->props, 'image_placement', '' );
			$parallax        = et_()->array_get( $this->props, 'parallax', '' );

			if ( 'on' !== $raw_value || 'background' !== $image_placement || 'on' !== $parallax || ! $multi_view->get_value( 'has_post_thumbnail' ) ) {
				return 'none';
			}

			return sprintf( 'url(%1$s)', $multi_view->get_value( 'post_featured_image' ) );
		} elseif ( 'content_source' === $name ) {
			if ( 'on' !== $raw_value && $multi_view->get_value( 'content_source_both' ) ) {
				return $multi_view->get_value( 'post_excerpt' );
			}

			return $multi_view->get_value( 'post_content' );
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Fullwidth_Post_Slider();
}
