<?php

require_once 'helpers/Overlay.php';

class ET_Builder_Module_Blog extends ET_Builder_Module_Type_PostBased {
	/**
	 * Track if the module is currently rendering to prevent unnecessary rendering and recursion.
	 *
	 * @var bool
	 */
	protected static $rendering = false;

	function init() {
		$this->name             = esc_html__( 'Blog', 'et_builder' );
		$this->plural           = esc_html__( 'Blogs', 'et_builder' );
		$this->slug             = 'et_pb_blog';
		$this->vb_support       = 'on';
		$this->main_css_element = '%%order_class%% .et_pb_post';

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
						'title'    => et_builder_i18n( 'Image' ),
						'priority' => 49,
					),
					'text'    => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 51,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'header'     => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'         => "{$this->main_css_element} .entry-title, %%order_class%% .not-found-title",
						'font'         => "{$this->main_css_element} .entry-title a, %%order_class%% .not-found-title",
						'color'        => "{$this->main_css_element} .entry-title a, %%order_class%% .not-found-title",
						'limited_main' => "{$this->main_css_element} .entry-title, {$this->main_css_element} .entry-title a, %%order_class%% .not-found-title",
						'hover'        => "{$this->main_css_element}:hover .entry-title, {$this->main_css_element}:hover .entry-title:hover a, %%order_class%% .not-found-title",
						'color_hover'  => "{$this->main_css_element}:hover .entry-title a, %%order_class%%:hover .not-found-title",
						'important'    => 'all',
					),
					'header_level' => array(
						'default'          => 'h2',
						'computed_affects' => array(
							'__posts',
						),
					),
				),
				'body'       => array(
					'label'          => et_builder_i18n( 'Body' ),
					'css'            => array(
						'main'         => "{$this->main_css_element} .post-content, %%order_class%%.et_pb_bg_layout_light .et_pb_post .post-content p, %%order_class%%.et_pb_bg_layout_dark .et_pb_post .post-content p",
						'color'        => "{$this->main_css_element}, {$this->main_css_element} .post-content *",
						'line_height'  => "{$this->main_css_element} p",
						'limited_main' => "{$this->main_css_element}, %%order_class%%.et_pb_bg_layout_light .et_pb_post .post-content p, %%order_class%%.et_pb_bg_layout_dark .et_pb_post .post-content p, %%order_class%%.et_pb_bg_layout_light .et_pb_post a.more-link, %%order_class%%.et_pb_bg_layout_dark .et_pb_post a.more-link",
						'hover'        => "{$this->main_css_element}:hover .post-content, %%order_class%%.et_pb_bg_layout_light:hover .et_pb_post .post-content p, %%order_class%%.et_pb_bg_layout_dark:hover .et_pb_post .post-content p",
						'color_hover'  => "{$this->main_css_element}:hover, {$this->main_css_element}:hover .post-content *",
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
						'css'               => array(
							'link'           => "{$this->main_css_element} .post-content a, %%order_class%%.et_pb_bg_layout_light .et_pb_post .post-content a, %%order_class%%.et_pb_bg_layout_dark .et_pb_post .post-content a",
							'ul'             => "{$this->main_css_element} .post-content ul li, %%order_class%%.et_pb_bg_layout_light .et_pb_post .post-content ul li, %%order_class%%.et_pb_bg_layout_dark .et_pb_post .post-content ul li",
							'ul_item_indent' => "{$this->main_css_element} .post-content ul, %%order_class%%.et_pb_bg_layout_light .et_pb_post .post-content ul, %%order_class%%.et_pb_bg_layout_dark .et_pb_post .post-content ul",
							'ol'             => "{$this->main_css_element} .post-content ol li, %%order_class%%.et_pb_bg_layout_light .et_pb_post .post-content ol li, %%order_class%%.et_pb_bg_layout_dark .et_pb_post .post-content ol li",
							'ol_item_indent' => "{$this->main_css_element} .post-content ol, %%order_class%%.et_pb_bg_layout_light .et_pb_post .post-content ol, %%order_class%%.et_pb_bg_layout_dark .et_pb_post .post-content ol",
							'quote'          => "{$this->main_css_element} .post-content blockquote, %%order_class%%.et_pb_bg_layout_light .et_pb_post .post-content blockquote, %%order_class%%.et_pb_bg_layout_dark .et_pb_post .post-content blockquote",
						),
					),
				),
				'meta'       => array(
					'label' => esc_html__( 'Meta', 'et_builder' ),
					'css'   => array(
						'main'         => "{$this->main_css_element} .post-meta, {$this->main_css_element} .post-meta a",
						'limited_main' => "{$this->main_css_element} .post-meta, {$this->main_css_element} .post-meta a, {$this->main_css_element} .post-meta span",
						'hover'        => "{$this->main_css_element}:hover .post-meta, {$this->main_css_element}:hover .post-meta a, {$this->main_css_element}:hover .post-meta span",
					),
				),
				'read_more'  => array(
					'label'           => esc_html__( 'Read More', 'et_builder' ),
					'css'             => array(
						'main'  => "{$this->main_css_element} div.post-content a.more-link",
						'hover' => "{$this->main_css_element} div.post-content a.more-link:hover",
					),
					'hide_text_align' => true,
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
				'css' => array(
					'main' => '%%order_class%%',
				),
			),
			'borders'        => array(
				'default'   => array(
					'css'             => array(
						'main' => array(
							'border_radii'        => '%%order_class%% .et_pb_blog_grid .et_pb_post',
							'border_styles'       => '%%order_class%% .et_pb_blog_grid .et_pb_post',
							'border_styles_hover' => '%%order_class%% .et_pb_blog_grid .et_pb_post:hover',
						),
					),
					'depends_on'      => array( 'fullwidth' ),
					'depends_show_if' => 'off',
					'defaults'        => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '1px',
							'color' => '#d8d8d8',
							'style' => 'solid',
						),
					),
					'label_prefix'    => esc_html__( 'Grid Layout', 'et_builder' ),
				),
				'fullwidth' => array(
					'css'             => array(
						'main' => array(
							'border_radii'  => '%%order_class%%:not(.et_pb_blog_grid_wrapper) .et_pb_post',
							'border_styles' => '%%order_class%%:not(.et_pb_blog_grid_wrapper) .et_pb_post',
						),
					),
					'depends_on'      => array( 'fullwidth' ),
					'depends_show_if' => 'on',
					'defaults'        => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#333333',
							'style' => 'solid',
						),
					),
				),
				'image'     => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%% .et_pb_post .entry-featured-image-url, %%order_class%% .et_pb_post .et_pb_slides, %%order_class%% .et_pb_post .et_pb_video_overlay',
							'border_styles' => '%%order_class%% .et_pb_post .entry-featured-image-url, %%order_class%% .et_pb_post .et_pb_slides, %%order_class%% .et_pb_post .et_pb_video_overlay',
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
						'main'    => '%%order_class%% .et_pb_post .entry-featured-image-url, %%order_class%% .et_pb_post img, %%order_class%% .et_pb_post .et_pb_slides, %%order_class%% .et_pb_post .et_pb_video_overlay',
						'overlay' => 'inset',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'height'         => array(
				'css' => array(
					'main' => '%%order_class%%',
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main'      => '%%order_class%%',
					'important' => array( 'custom_margin' ),
				),
			),
			'text'           => array(
				'use_background_layout' => true,
				'css'                   => array(
					'text_shadow' => '%%order_class%%',
				),
				'options'               => array(
					'background_layout' => array(
						'depends_show_if'  => 'on',
						'default_on_front' => 'light',
						'hover'            => 'tabs',
					),
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
					'main' => implode(
						', ',
						array(
							'%%order_class%% img',
							'%%order_class%% .et_pb_slides',
							'%%order_class%% .et_pb_video_overlay',
						)
					),
				),
			),
			'scroll_effects' => array(
				'grid_support' => 'yes',
			),
			'button'         => false,
		);

		$this->custom_css_fields = array(
			'title'          => array(
				'label'    => et_builder_i18n( 'Title' ),
				'selector' => '.entry-title',
			),
			'content'        => array(
				'label'    => et_builder_i18n( 'Body' ),
				'selector' => '.post-content',
			),
			'post_meta'      => array(
				'label'    => esc_html__( 'Post Meta', 'et_builder' ),
				'selector' => '.post-meta',
			),
			'pagenavi'       => array(
				'label'    => esc_html__( 'Pagenavi', 'et_builder' ),
				'selector' => '.wp_pagenavi',
			),
			'featured_image' => array(
				'label'    => esc_html__( 'Featured Image', 'et_builder' ),
				'selector' => '.entry-featured-image-url img',
			),
			'read_more'      => array(
				'label'    => esc_html__( 'Read More Button', 'et_builder' ),
				'selector' => 'a.more-link',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'PRaWaGI75wc',
				'name' => esc_html__( 'An introduction to the Blog module', 'et_builder' ),
			),
			array(
				'id'   => 'jETCzKVv6P0',
				'name' => esc_html__( 'How To Use Divi Blog Post Formats', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'fullwidth'                     => array(
				'label'            => et_builder_i18n( 'Layout' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'on'  => esc_html__( 'Fullwidth', 'et_builder' ),
					'off' => esc_html__( 'Grid', 'et_builder' ),
				),
				'affects'          => array(
					'background_layout',
					'masonry_tile_background_color',
					'border_radii_fullwidth',
					'border_styles_fullwidth',
					'border_radii',
					'border_styles',
				),
				'description'      => esc_html__( 'Toggle between the various blog layout types.', 'et_builder' ),
				'computed_affects' => array(
					'__posts',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
				'default_on_front' => 'on',
			),
			'use_current_loop'              => array(
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
			'post_type'                     => array(
				'label'            => esc_html__( 'Post Type', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => et_get_registered_post_type_options( false, false ),
				'description'      => esc_html__( 'Choose posts of which post type you would like to display.', 'et_builder' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'main_content',
				'default'          => 'post',
				'show_if'          => array(
					'use_current_loop' => 'off',
				),
			),
			'posts_number'                  => array(
				'label'            => esc_html__( 'Post Count', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'Choose how much posts you would like to display per page.', 'et_builder' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'main_content',
				'default'          => 10,
			),
			'include_categories'            => array(
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
				'description'      => esc_html__( 'Choose which categories you would like to include in the feed.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__posts',
				),
				'show_if'          => array(
					'use_current_loop' => 'off',
					'post_type'        => 'post',
				),
			),
			'meta_date'                     => array(
				'label'            => esc_html__( 'Date Format', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'If you would like to adjust the date format, input the appropriate PHP date format here.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__posts',
				),
				'default'          => 'M j, Y',
			),
			'show_thumbnail'                => array(
				'label'            => esc_html__( 'Show Featured Image', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'description'      => esc_html__( 'This will turn thumbnails on and off.', 'et_builder' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
				'default_on_front' => 'on',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_content'                  => array(
				'label'            => esc_html__( 'Content Length', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'Show Excerpt', 'et_builder' ),
					'on'  => esc_html__( 'Show Content', 'et_builder' ),
				),
				'affects'          => array(
					'show_more',
					'show_excerpt',
					'use_manual_excerpt',
					'excerpt_length',
				),
				'description'      => esc_html__( 'Showing the full content will not truncate your posts on the index page. Showing the excerpt will only display your excerpt text.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__posts',
				),
				'default_on_front' => 'off',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'use_manual_excerpt'            => array(
				'label'            => esc_html__( 'Use Post Excerpts', 'et_builder' ),
				'description'      => esc_html__( 'Disable this option if you want to ignore manually defined excerpts and always generate it automatically.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default'          => 'on',
				'computed_affects' => array(
					'__posts',
				),
				'depends_show_if'  => 'off',
				'toggle_slug'      => 'main_content',
				'option_category'  => 'configuration',
			),
			'excerpt_length'                => array(
				'label'            => esc_html__( 'Excerpt Length', 'et_builder' ),
				'description'      => esc_html__( 'Define the length of automatically generated excerpts. Leave blank for default ( 270 ) ', 'et_builder' ),
				'type'             => 'text',
				'default'          => '270',
				'computed_affects' => array(
					'__posts',
				),
				'depends_show_if'  => 'off',
				'toggle_slug'      => 'main_content',
				'option_category'  => 'configuration',
			),
			'show_more'                     => array(
				'label'            => esc_html__( 'Show Read More Button', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'depends_show_if'  => 'off',
				'description'      => esc_html__( 'Here you can define whether to show "read more" link after the excerpts or not.', 'et_builder' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
				'default_on_front' => 'off',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_author'                   => array(
				'label'            => esc_html__( 'Show Author', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'description'      => esc_html__( 'Turn on or off the author link.', 'et_builder' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
				'default_on_front' => 'on',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_date'                     => array(
				'label'            => esc_html__( 'Show Date', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'description'      => esc_html__( 'Turn the date on or off.', 'et_builder' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
				'default_on_front' => 'on',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_categories'               => array(
				'label'            => esc_html__( 'Show Categories', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'description'      => esc_html__( 'Turn the category links on or off.', 'et_builder' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
				'default_on_front' => 'on',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_comments'                 => array(
				'label'            => esc_html__( 'Show Comment Count', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'description'      => esc_html__( 'Turn comment count on and off.', 'et_builder' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
				'default_on_front' => 'off',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_excerpt'                  => array(
				'label'            => esc_html__( 'Show Excerpt', 'et_builder' ),
				'description'      => esc_html__( 'Turn excerpt on and off.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'computed_affects' => array(
					'__posts',
				),
				'depends_show_if'  => 'off',
				'toggle_slug'      => 'elements',
				'option_category'  => 'configuration',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_pagination'               => array(
				'label'            => esc_html__( 'Show Pagination', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'description'      => esc_html__( 'Turn pagination on and off.', 'et_builder' ),
				'computed_affects' => array(
					'__posts',
				),
				'toggle_slug'      => 'elements',
				'default_on_front' => 'on',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'offset_number'                 => array(
				'label'            => esc_html__( 'Post Offset Number', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'description'      => esc_html__( 'Choose how many posts you would like to skip. These posts will not be shown in the feed.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__posts',
				),
				'default'          => 0,
			),
			'use_overlay'                   => array(
				'label'            => esc_html__( 'Featured Image Overlay', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'off' => et_builder_i18n( 'Off' ),
					'on'  => et_builder_i18n( 'On' ),
				),
				'affects'          => array(
					'overlay_icon_color',
					'hover_overlay_color',
					'hover_icon',
				),
				'description'      => esc_html__( 'If enabled, an overlay color and icon will be displayed when a visitors hovers over the featured image of a post.', 'et_builder' ),
				'computed_affects' => array(
					'__posts',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'default_on_front' => 'off',
			),
			'overlay_icon_color'            => array(
				'label'           => esc_html__( 'Overlay Icon Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'Here you can define a custom color for the overlay icon', 'et_builder' ),
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'hover_overlay_color'           => array(
				'label'           => esc_html__( 'Overlay Background Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'Here you can define a custom color for the overlay', 'et_builder' ),
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'hover_icon'                    => array(
				'label'            => esc_html__( 'Overlay Icon', 'et_builder' ),
				'type'             => 'select_icon',
				'option_category'  => 'configuration',
				'class'            => array( 'et-pb-font-icon' ),
				'depends_show_if'  => 'on',
				'description'      => esc_html__( 'Here you can define a custom icon for the overlay', 'et_builder' ),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'computed_affects' => array(
					'__posts',
				),
				'mobile_options'   => true,
				'sticky'           => true,
			),
			'masonry_tile_background_color' => array(
				'label'           => esc_html__( 'Grid Tile Background Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'toggle_slug'     => 'background',
				'depends_show_if' => 'off',
				'depends_on'      => array(
					'fullwidth',
				),
				'hover'           => 'tabs',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'__posts'                       => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Blog', 'get_blog_posts' ),
				'computed_depends_on' => array(
					'use_current_loop',
					'post_type',
					'fullwidth',
					'posts_number',
					'include_categories',
					'meta_date',
					'show_thumbnail',
					'show_content',
					'show_more',
					'show_author',
					'show_date',
					'show_categories',
					'show_comments',
					'show_excerpt',
					'use_manual_excerpt',
					'excerpt_length',
					'show_pagination',
					'offset_number',
					'use_overlay',
					'hover_icon',
					'hover_icon_tablet',
					'hover_icon_phone',
					'header_level',
					'__page',
				),
			),
			'__page'                        => array(
				'type'              => 'computed',
				'computed_callback' => array( 'ET_Builder_Module_Blog', 'get_blog_posts' ),
				'computed_affects'  => array(
					'__posts',
				),
			),
		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['background_layout']             = array(
			'color' => implode(
				', ',
				array(
					'%%order_class%% .entry-title',
					'%%order_class%% .post-meta',
					'%%order_class%% .post-content',
				)
			),
		);
		$fields['border_radii']                  = array( 'border-radius' => self::$_->array_get( $this->advanced_fields, 'borders.default.css.main.border_radii' ) );
		$fields['border_styles']                 = array( 'border' => self::$_->array_get( $this->advanced_fields, 'borders.default.css.main.border_styles' ) );
		$fields['border_radii_fullwidth']        = array( 'border-radius' => self::$_->array_get( $this->advanced_fields, 'borders.fullwidth.css.main.border_radii' ) );
		$fields['border_styles_fullwidth']       = array( 'border' => self::$_->array_get( $this->advanced_fields, 'borders.fullwidth.css.main.border_styles' ) );
		$fields['max_width']                     = array( 'max-width' => '%%order_class%%' );
		$fields['width']                         = array( 'width' => '%%order_class%%' );
		$fields['overlay_icon_color']            = array( 'background-color' => '%%order_class%% .et_overlay:before' );
		$fields['hover_overlay_color']           = array( 'background-color' => '%%order_class%% .et_overlay' );
		$fields['masonry_tile_background_color'] = array( 'background-color' => '%%order_class%% .et_pb_blog_grid .et_pb_post' );

		return $fields;
	}

	/**
	 * Get blog posts for blog module
	 *
	 * @param array   arguments that is being used by et_pb_blog
	 * @return string blog post markup
	 */
	static function get_blog_posts( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		global $paged, $post, $wp_query, $et_fb_processing_shortcode_object, $et_pb_rendering_column_content;

		if ( self::$rendering ) {
			// We are trying to render a Blog module while a Blog module is already being rendered
			// which means we have most probably hit an infinite recursion. While not necessarily
			// the case, rendering a post which renders a Blog module which renders a post
			// which renders a Blog module is not a sensible use-case.
			return '';
		}

		$global_processing_original_value = $et_fb_processing_shortcode_object;

		// Default params are combination of attributes that is used by et_pb_blog and
		// conditional tags that need to be simulated (due to AJAX nature) by passing args
		$defaults = array(
			'use_current_loop'              => 'off',
			'post_type'                     => '',
			'fullwidth'                     => '',
			'posts_number'                  => '',
			'include_categories'            => '',
			'meta_date'                     => '',
			'show_thumbnail'                => '',
			'show_content'                  => '',
			'show_author'                   => '',
			'show_date'                     => '',
			'show_categories'               => '',
			'show_comments'                 => '',
			'show_excerpt'                  => '',
			'use_manual_excerpt'            => '',
			'excerpt_length'                => '',
			'show_pagination'               => '',
			'background_layout'             => '',
			'show_more'                     => '',
			'offset_number'                 => '',
			'masonry_tile_background_color' => '',
			'overlay_icon_color'            => '',
			'hover_overlay_color'           => '',
			'hover_icon'                    => '',
			'hover_icon_tablet'             => '',
			'hover_icon_phone'              => '',
			'use_overlay'                   => '',
			'header_level'                  => 'h2',
		);

		// WordPress' native conditional tag is only available during page load. It'll fail during component update because
		// et_pb_process_computed_property() is loaded in admin-ajax.php. Thus, use WordPress' conditional tags on page load and
		// rely to passed $conditional_tags for AJAX call
		$is_front_page               = et_fb_conditional_tag( 'is_front_page', $conditional_tags );
		$is_single                   = et_fb_conditional_tag( 'is_single', $conditional_tags );
		$et_is_builder_plugin_active = et_fb_conditional_tag( 'et_is_builder_plugin_active', $conditional_tags );
		$post_id                     = isset( $current_page['id'] ) ? (int) $current_page['id'] : 0;

		$container_is_closed = false;

		// remove all filters from WP audio shortcode to make sure current theme doesn't add any elements into audio module
		remove_all_filters( 'wp_audio_shortcode_library' );
		remove_all_filters( 'wp_audio_shortcode' );
		remove_all_filters( 'wp_audio_shortcode_class' );

		$args = wp_parse_args( $args, $defaults );

		if ( 'on' === $args['use_current_loop'] ) {
			// Reset loop-affecting values to their defaults to simulate the current loop.
			$reset_keys = array( 'post_type', 'include_categories' );

			foreach ( $reset_keys as $key ) {
				$args[ $key ] = $defaults[ $key ];
			}
		}

		$processed_header_level = et_pb_process_header_level( $args['header_level'], 'h2' );
		$processed_header_level = esc_html( $processed_header_level );

		$overlay_output = '';

		if ( 'on' === $args['use_overlay'] ) {
			$overlay_output = ET_Builder_Module_Helper_Overlay::render(
				array(
					'icon'        => $args['hover_icon'],
					'icon_tablet' => $args['hover_icon_tablet'],
					'icon_phone'  => $args['hover_icon_phone'],
				)
			);
		}

		$overlay_class = 'on' === $args['use_overlay'] ? ' et_pb_has_overlay' : '';

		$query_args = array(
			'posts_per_page' => intval( $args['posts_number'] ),
			'post_status'    => array( 'publish', 'private', 'inherit' ),
			'perm'           => 'readable',
			'post_type'      => $args['post_type'],
		);

		if ( defined( 'DOING_AJAX' ) && isset( $current_page['paged'] ) ) {
			$paged = intval( $current_page['paged'] ); //phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		} else {
			$paged = $is_front_page ? get_query_var( 'page' ) : get_query_var( 'paged' ); //phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		}

		// support pagination in VB
		if ( isset( $args['__page'] ) ) {
			$paged = $args['__page']; //phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		}

		$query_args['cat'] = implode( ',', self::filter_include_categories( $args['include_categories'], $post_id ) );

		$query_args['paged'] = $paged;

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

		if ( $is_single ) {
			$main_query_post = ET_Post_Stack::get_main_post();

			if ( null !== $main_query_post ) {
				$query_args['post__not_in'][] = $main_query_post->ID;
			}
		}

		// Get query
		$query = new WP_Query( $query_args );

		/**
		 * Filters Blog module's main query.
		 *
		 * @since 4.7.0
		 * @since 4.11.0 Pass modified module attributes.
		 *
		 * @param WP_Query $query
		 * @param array    $args  Modified module attributes.
		 */
		$query = apply_filters( 'et_builder_blog_query', $query, $args ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- We intend to override $wp_query for blog module.

		// Keep page's $wp_query global
		$wp_query_page = $wp_query;

		// Turn page's $wp_query into this module's query
		$wp_query = $query; //phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited

		$wp_query->et_pb_blog_query = true;

		self::$rendering = true;

		// Manually set the max_num_pages to make the `next_posts_link` work
		if ( '' !== $args['offset_number'] && ! empty( $args['offset_number'] ) ) {
			$wp_query->found_posts   = max( 0, $wp_query->found_posts - intval( $args['offset_number'] ) );
			$posts_number            = intval( $args['posts_number'] );
			$wp_query->max_num_pages = $posts_number > 1 ? ceil( $wp_query->found_posts / $posts_number ) : 1;
		}

		ob_start();

		if ( $query->have_posts() ) {
			if ( 'on' !== $args['fullwidth'] ) {
				echo '<div class="et_pb_salvattore_content" data-columns>';
			}

			while ( $query->have_posts() ) {
				$query->the_post();
				ET_Post_Stack::replace( $post );
				global $et_fb_processing_shortcode_object;

				$global_processing_original_value = $et_fb_processing_shortcode_object;

				// reset the fb processing flag
				$et_fb_processing_shortcode_object = false;

				$thumb          = '';
				$width          = 'on' === $args['fullwidth'] ? 1080 : 400;
				$width          = (int) apply_filters( 'et_pb_blog_image_width', $width );
				$height         = 'on' === $args['fullwidth'] ? 675 : 250;
				$height         = (int) apply_filters( 'et_pb_blog_image_height', $height );
				$classtext      = 'on' === $args['fullwidth'] ? 'et_pb_post_main_image' : '';
				$titletext      = get_the_title();
				$alttext        = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
				$thumbnail      = get_thumbnail( $width, $height, $classtext, $alttext, $titletext, false, 'Blogimage' );
				$thumb          = $thumbnail['thumb'];
				$no_thumb_class = '' === $thumb || 'off' === $args['show_thumbnail'] ? ' et_pb_no_thumb' : '';
				$excerpt_length = '' !== $args['excerpt_length'] ? intval( $args['excerpt_length'] ) : 270;

				$post_format = et_pb_post_format();
				if ( in_array( $post_format, array( 'video', 'gallery' ) ) ) {
					$no_thumb_class = '';
				}

				// Print output
				?>
					<article id="" <?php post_class( 'et_pb_post clearfix' . $no_thumb_class . $overlay_class ); ?>>
						<?php
							et_divi_post_format_content();

						if ( ! in_array( $post_format, array( 'link', 'audio', 'quote' ) ) ) {
							if ( 'video' === $post_format && false !== ( $first_video = et_get_first_video() ) ) :
								$video_overlay = has_post_thumbnail() ? sprintf(
									'<div class="et_pb_video_overlay" style="background-image: url(%1$s); background-size: cover;">
											<div class="et_pb_video_overlay_hover">
												<a href="#" class="et_pb_video_play"></a>
											</div>
										</div>',
									et_core_esc_previously( $thumb )
								) : '';

								printf(
									'<div class="et_main_video_container">
											%1$s
											%2$s
										</div>',
									et_core_esc_previously( $video_overlay ),
									et_core_esc_previously( $first_video )
								);
							elseif ( 'gallery' === $post_format ) :
								et_pb_gallery_images( 'slider' );
								elseif ( '' !== $thumb && 'on' === $args['show_thumbnail'] ) :
									if ( 'on' !== $args['fullwidth'] ) {
										echo '<div class="et_pb_image_container">';
									}
									?>
										<?php if ( get_permalink() ) { ?>
											<a href="<?php the_permalink(); ?>" class="entry-featured-image-url">
										<?php } ?>
											<?php print_thumbnail( $thumb, $thumbnail['use_timthumb'], $titletext, $width, $height ); ?>
											<?php
											if ( 'on' === $args['use_overlay'] ) {
												echo et_core_esc_previously( $overlay_output );
											}
											?>
										<?php if ( get_permalink() ) { ?>
											</a>
										<?php } ?>
									<?php
									if ( 'on' !== $args['fullwidth'] ) {
										echo '</div>';
									}
								endif;
						}
						?>

						<?php if ( 'off' === $args['fullwidth'] || ! in_array( $post_format, array( 'link', 'audio', 'quote' ) ) ) { ?>
							<?php if ( ! in_array( $post_format, array( 'link', 'audio' ) ) ) { ?>
								<<?php echo et_core_esc_previously( $processed_header_level ); ?> class="entry-title">
									<?php if ( get_permalink() ) { ?>
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									<?php } else { ?>
										<?php the_title(); ?>
									<?php } ?>
								</<?php echo et_core_esc_previously( $processed_header_level ); ?>>
							<?php } ?>

							<?php
							if ( 'on' === $args['show_author'] || 'on' === $args['show_date'] || 'on' === $args['show_categories'] || 'on' === $args['show_comments'] ) {
								$author = 'on' === $args['show_author']
									? et_get_safe_localization( sprintf( __( 'by %s', 'et_builder' ), '<span class="author vcard">' . et_pb_get_the_author_posts_link() . '</span>' ) )
									: '';

								$author_separator = 'on' === $args['show_author'] && 'on' === $args['show_date']
									? ' | '
									: '';

								// phpcs:disable WordPress.WP.I18n.NoEmptyStrings -- intentionally used.
								$date = 'on' === $args['show_date']
									? et_get_safe_localization( sprintf( __( '%s', 'et_builder' ), '<span class="published">' . esc_html( get_the_date( str_replace( '\\\\', '\\', $args['meta_date'] ) ) ) . '</span>' ) )
									: '';
								// phpcs:enable

								$date_separator = ( ( 'on' === $args['show_author'] || 'on' === $args['show_date'] ) && 'on' === $args['show_categories'] )
									? ' | '
									: '';

								$categories = 'on' === $args['show_categories']
									? et_builder_get_the_term_list( ', ' )
									: '';

								$categories_separator = ( ( 'on' === $args['show_author'] || 'on' === $args['show_date'] || 'on' === $args['show_categories'] ) && 'on' === $args['show_comments'] )
									? ' | '
									: '';

								$comments = 'on' === $args['show_comments']
									? et_core_maybe_convert_to_utf_8( sprintf( esc_html( _nx( '%s Comment', '%s Comments', get_comments_number(), 'number of comments', 'et_builder' ) ), number_format_i18n( get_comments_number() ) ) )
									: '';

								printf(
									'<p class="post-meta">%1$s %2$s %3$s %4$s %5$s %6$s %7$s</p>',
									et_core_esc_previously( $author ),
									et_core_intentionally_unescaped( $author_separator, 'fixed_string' ),
									et_core_esc_previously( $date ),
									et_core_intentionally_unescaped( $date_separator, 'fixed_string' ),
									et_core_esc_wp( $categories ),
									et_core_intentionally_unescaped( $categories_separator, 'fixed_string' ),
									et_core_esc_previously( $comments )
								);
							}

								$post_content = et_strip_shortcodes( et_delete_post_first_video( get_the_content() ), true );

								// reset the fb processing flag
								$et_fb_processing_shortcode_object = false;
								// set the flag to indicate that we're processing internal content
								$et_pb_rendering_column_content = true;
								// reset all the attributes required to properly generate the internal styles
								ET_Builder_Element::clean_internal_modules_styles();

								echo '<div class="post-content">';

							if ( 'on' === $args['show_content'] ) {
								global $more;

								// page builder doesn't support more tag, so display the_content() in case of post made with page builder
								if ( et_pb_is_pagebuilder_used( get_the_ID() ) ) {
									$more = 1; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited

									echo et_core_intentionally_unescaped( apply_filters( 'the_content', $post_content ), 'html' );

								} else {
									$more = null; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
									echo et_core_intentionally_unescaped( apply_filters( 'the_content', et_delete_post_first_video( get_the_content( esc_html__( 'read more...', 'et_builder' ) ) ) ), 'html' );
								}
							} elseif ( 'on' === $args['show_excerpt'] ) {
								if ( has_excerpt() && 'off' !== $args['use_manual_excerpt'] ) {
									the_excerpt();
								} else {
									if ( '' !== $post_content ) {
										// set the $et_fb_processing_shortcode_object to false, to retrieve the content inside truncate_post() correctly
										$et_fb_processing_shortcode_object = false;
										echo et_core_intentionally_unescaped( wpautop( et_delete_post_first_video( strip_shortcodes( truncate_post( $excerpt_length, false, '', true ) ) ) ), 'html' );
										// reset the $et_fb_processing_shortcode_object to its original value
										$et_fb_processing_shortcode_object = $global_processing_original_value;
									} else {
										echo '';
									}
								}
							}

								$et_fb_processing_shortcode_object = $global_processing_original_value;
								// retrieve the styles for the modules inside Blog content
								$internal_style = ET_Builder_Element::get_style( true );
								// reset all the attributes after we retrieved styles
								ET_Builder_Element::clean_internal_modules_styles( false );
								$et_pb_rendering_column_content = false;
								// append styles to the blog content
							if ( $internal_style ) {
								printf(
									'<style type="text/css" class="et_fb_blog_inner_content_styles">
											%1$s
										</style>',
									et_core_esc_previously( $internal_style )
								);
							}

							if ( 'on' !== $args['show_content'] ) {
								$more = 'on' === $args['show_more'] ? sprintf( ' <a href="%1$s" class="more-link" >%2$s</a>', esc_url( get_permalink() ), esc_html__( 'read more', 'et_builder' ) ) : ''; //phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
								echo et_core_esc_previously( $more );
							}

								echo '</div>';
							?>
						<?php } // 'off' === $fullwidth || ! in_array( $post_format, array( 'link', 'audio', 'quote', 'gallery' ?>
					</article>
				<?php

				$et_fb_processing_shortcode_object = $global_processing_original_value;
				ET_Post_Stack::pop();
			} // endwhile
			ET_Post_Stack::reset();

			if ( 'on' !== $args['fullwidth'] ) {
				echo '</div>';
			}

			if ( 'on' === $args['show_pagination'] ) {

				$container_is_closed = true;

				if ( function_exists( 'wp_pagenavi' ) ) {
					wp_pagenavi(
						array(
							'query' => $query,
						)
					);
				} else {
					if ( $et_is_builder_plugin_active ) {
						include ET_BUILDER_PLUGIN_DIR . 'includes/navigation.php';
					} else {
						get_template_part( 'includes/navigation', 'index' );
					}
				}
			}
		}

		unset( $wp_query->et_pb_blog_query );

		// Reset $wp_query to its origin
		$wp_query = $wp_query_page; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited

		if ( ! $posts = ob_get_clean() ) {
			$posts = self::get_no_results_template( et_core_esc_previously( $processed_header_level ) );
		}

		self::$rendering = false;

		return $posts;
	}

	/**
	 * Render pagination element
	 *
	 * @since 3.27.1
	 *
	 * @param bool $echo Wether to print the output or return it.
	 *
	 * @return (void|string)
	 */
	function render_pagination( $echo = true ) {
		if ( ! $echo ) {
			ob_start();
		}

		add_filter( 'get_pagenum_link', array( 'ET_Builder_Module_Blog', 'filter_pagination_url' ) );

		if ( function_exists( 'wp_pagenavi' ) ) {
			wp_pagenavi();
		} else {
			if ( et_is_builder_plugin_active() ) {
				include ET_BUILDER_PLUGIN_DIR . 'includes/navigation.php';
			} else {
				get_template_part( 'includes/navigation', 'index' );
			}
		}

		remove_filter( 'get_pagenum_link', array( 'ET_Builder_Module_Blog', 'filter_pagination_url' ) );

		if ( ! $echo ) {
			$output = ob_get_contents();
			ob_end_clean();

			return $output;
		}
	}

	/**
	 * Filter the pagination url to add a flag so it can be filtered to avoid pagination clashes with the main query.
	 *
	 * @since 4.0
	 *
	 * @param string  $result
	 * @param integer $pagenum
	 *
	 * @return string
	 */
	public static function filter_pagination_url( $result ) {
		return add_query_arg( 'et_blog', '', $result );
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
		global $post, $paged, $wp_query, $wp_the_query, $wp_filter, $__et_blog_module_paged;

		if ( self::$rendering ) {
			// We are trying to render a Blog module while a Blog module is already being rendered
			// which means we have most probably hit an infinite recursion. While not necessarily
			// the case, rendering a post which renders a Blog module which renders a post
			// which renders a Blog module is not a sensible use-case.
			return '';
		}

		// Keep a reference to the real main query to restore from later.
		$main_query = $wp_the_query;

		// Stored current global post as variable so global $post variable can be restored
		// to its original state when et_pb_blog shortcode ends to avoid incorrect global $post
		// being used on the page (i.e. blog + shop module in backend builder)
		$post_cache = $post;

		/**
		 * Cached $wp_filter so it can be restored at the end of the callback.
		 * This is needed because this callback uses the_content filter / calls a function
		 * which uses the_content filter. WordPress doesn't support nested filter
		 */
		$wp_filter_cache = $wp_filter;

		// Helpers.
		$sticky = et_pb_sticky_options();

		$multi_view         = et_pb_multi_view_options( $this );
		$use_current_loop   = isset( $this->props['use_current_loop'] ) ? $this->props['use_current_loop'] : 'off';
		$post_type          = isset( $this->props['post_type'] ) ? $this->props['post_type'] : 'post';
		$fullwidth          = $this->props['fullwidth'];
		$posts_number       = $this->props['posts_number'];
		$include_categories = $this->props['include_categories'];
		$meta_date          = $this->props['meta_date'];
		$show_thumbnail     = $this->props['show_thumbnail'];
		$show_content       = $this->props['show_content'];
		$show_author        = $this->props['show_author'];
		$show_date          = $this->props['show_date'];
		$show_categories    = $this->props['show_categories'];
		$show_comments      = $this->props['show_comments'];
		$show_excerpt       = $this->props['show_excerpt'];
		$use_manual_excerpt = $this->props['use_manual_excerpt'];
		$excerpt_length     = $this->props['excerpt_length'];
		$show_pagination    = $this->props['show_pagination'];
		$show_more          = $this->props['show_more'];
		$offset_number      = $this->props['offset_number'];
		$use_overlay        = $this->props['use_overlay'];
		$header_level       = $this->props['header_level'];

		$background_layout               = $this->props['background_layout'];
		$background_layout_hover         = et_pb_hover_options()->get_value( 'background_layout', $this->props, 'light' );
		$background_layout_hover_enabled = et_pb_hover_options()->is_enabled( 'background_layout', $this->props );
		$background_layout_values        = et_pb_responsive_options()->get_property_values( $this->props, 'background_layout' );
		$background_layout_tablet        = isset( $background_layout_values['tablet'] ) ? $background_layout_values['tablet'] : '';
		$background_layout_phone         = isset( $background_layout_values['phone'] ) ? $background_layout_values['phone'] : '';

		$hover_icon        = $this->props['hover_icon'];
		$hover_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'hover_icon' );
		$hover_icon_tablet = isset( $hover_icon_values['tablet'] ) ? $hover_icon_values['tablet'] : '';
		$hover_icon_phone  = isset( $hover_icon_values['phone'] ) ? $hover_icon_values['phone'] : '';
		$hover_icon_sticky = $sticky->get_value( 'hover_icon', $this->props );

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$container_is_closed = false;

		$processed_header_level = et_pb_process_header_level( $header_level, 'h2' );

		// some themes do not include these styles/scripts so we need to enqueue them in this module to support audio post format
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );

		// remove all filters from WP audio shortcode to make sure current theme doesn't add any elements into audio module
		remove_all_filters( 'wp_audio_shortcode_library' );
		remove_all_filters( 'wp_audio_shortcode' );
		remove_all_filters( 'wp_audio_shortcode_class' );

		// Masonry Tile Background color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'masonry_tile_background_color',
				'selector'       => '%%order_class%% .et_pb_blog_grid .et_pb_post',
				'css_property'   => 'background-color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		// Overlay Icon Color.
		$this->generate_styles(
			array(
				'hover'          => false,
				'base_attr_name' => 'overlay_icon_color',
				'selector'       => '%%order_class%% .et_overlay:before',
				'css_property'   => 'color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		// Hover Overlay Color.
		$this->generate_styles(
			array(
				'hover'          => false,
				'base_attr_name' => 'hover_overlay_color',
				'selector'       => '%%order_class%% .et_overlay',
				'css_property'   => 'background-color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		$overlay_output = '';

		if ( 'on' === $use_overlay ) {
			$overlay_output = ET_Builder_Module_Helper_Overlay::render(
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

		$overlay_class = 'on' === $use_overlay ? ' et_pb_has_overlay' : '';

		if ( 'on' !== $fullwidth ) {
			wp_enqueue_script( 'salvattore' );

			$background_layout        = 'light';
			$background_layout_tablet = ! empty( $background_layout_tablet ) ? 'light' : '';
			$background_layout_phone  = ! empty( $background_layout_phone ) ? 'light' : '';
		}

		$args = array(
			'posts_per_page' => (int) $posts_number,
			'post_status'    => array( 'publish', 'private', 'inherit' ),
			'perm'           => 'readable',
			'post_type'      => $post_type,
		);

		$et_paged = is_front_page() ? get_query_var( 'page' ) : get_query_var( 'paged' );

		if ( $__et_blog_module_paged > 1 ) {
			$et_paged      = $__et_blog_module_paged;
			$paged         = $__et_blog_module_paged; //phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
			$args['paged'] = $__et_blog_module_paged;
		}

		if ( is_front_page() ) {
			$paged = $et_paged; //phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		}

		$args['cat'] = implode( ',', self::filter_include_categories( $include_categories ) );

		$args['paged'] = $et_paged;

		if ( '' !== $offset_number && ! empty( $offset_number ) ) {
			/**
			 * Offset + pagination don't play well. Manual offset calculation required
			 *
			 * @see: https://codex.wordpress.org/Making_Custom_Queries_using_Offset_and_Pagination
			 */
			if ( $paged > 1 ) {
				$args['offset'] = ( ( $et_paged - 1 ) * intval( $posts_number ) ) + intval( $offset_number );
			} else {
				$args['offset'] = intval( $offset_number );
			}
		}

		$main_query_post = ET_Post_Stack::get_main_post();

		if ( $main_query_post && is_singular( $main_query_post->post_type ) && ! isset( $args['post__not_in'] ) ) {
			$args['post__not_in'] = array( $main_query_post->ID );
		}

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

		self::$rendering = true;

		$post_meta_remove_keys = array(
			'show_author',
			'show_date',
			'show_categories',
			'show_comments',
		);

		$post_meta_removes = array(
			'desktop' => array(
				'none' => 'none',
			),
			'tablet'  => array(
				'none' => 'none',
			),
			'phone'   => array(
				'none' => 'none',
			),
			'hover'   => array(
				'none' => 'none',
			),
		);

		foreach ( $post_meta_removes as $mode => $post_meta_remove ) {
			foreach ( $post_meta_remove_keys as $post_meta_remove_key ) {
				if ( $multi_view->has_value( $post_meta_remove_key, 'on', $mode, true ) ) {
					continue;
				}

				$post_meta_remove[ $post_meta_remove_key ] = $post_meta_remove_key;
			}

			$post_meta_removes[ $mode ] = implode( ',', $post_meta_remove );
		}

		$multi_view->set_custom_prop( 'post_meta_removes', $post_meta_removes );
		$multi_view->set_custom_prop( 'post_content', $multi_view->get_values( 'show_content' ) );

		$show_thumbnail = $multi_view->has_value( 'show_thumbnail', 'on' );

		ob_start();

		// Stash properties that will not be the same after wp_reset_query().
		$wp_query_props = array(
			'current_post' => $wp_query->current_post,
			'in_the_loop'  => $wp_query->in_the_loop,
		);

		$show_no_results_template = true;

		if ( 'off' === $use_current_loop ) {
			query_posts( $args );
		} elseif ( is_singular() ) {
			// Force an empty result set in order to avoid loops over the current post.
			query_posts( array( 'post__in' => array( 0 ) ) );
			$show_no_results_template = false;
		} else {
			// Only allow certain args when `Posts For Current Page` is set.
			$original = $wp_query->query_vars;
			$custom   = array_intersect_key( $args, array_flip( array( 'posts_per_page', 'offset', 'paged' ) ) );

			// Trick WP into reporting this query as the main query so third party filters
			// that check for is_main_query() are applied.
			$wp_the_query = $wp_query = new WP_Query( array_merge( $original, $custom ) );
		}

		/**
		 * Filters Blog module's main query.
		 *
		 * @since 4.7.0
		 * @since 4.11.0 Pass modified module attributes.
		 *
		 * @param WP_Query $wp_query
		 * @param array    $attrs    Modified module attributes.
		 */
		$wp_query = apply_filters( 'et_builder_blog_query', $wp_query, $attrs ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- We intend to override $wp_query for blog module.

		// Manually set the max_num_pages to make the `next_posts_link` work
		if ( '' !== $offset_number && ! empty( $offset_number ) ) {
			global $wp_query;
			$wp_query->found_posts   = max( 0, $wp_query->found_posts - intval( $offset_number ) );
			$posts_number            = intval( $posts_number );
			$wp_query->max_num_pages = $posts_number > 1 ? ceil( $wp_query->found_posts / $posts_number ) : 1;
		}

		$blog_order                 = self::_get_index( array( self::INDEX_MODULE_ORDER, $render_slug ) );
		$items_count                = 0;
		$wp_query->et_pb_blog_query = true;

		if ( have_posts() ) {
			if ( 'off' === $fullwidth ) {
				$attribute = et_core_is_fb_enabled() ? 'data-et-vb-columns' : 'data-columns';
				echo '<div class="et_pb_salvattore_content" ' . et_core_intentionally_unescaped( $attribute, 'fixed_string' ) . '>';
			}

			while ( have_posts() ) {
				the_post();
				ET_Post_Stack::replace( $post );

				$post_format = et_pb_post_format();

				$thumb = '';

				$width = 'on' === $fullwidth ? 1080 : 400;
				$width = (int) apply_filters( 'et_pb_blog_image_width', $width );

				$height    = 'on' === $fullwidth ? 675 : 250;
				$height    = (int) apply_filters( 'et_pb_blog_image_height', $height );
				$classtext = 'on' === $fullwidth ? 'et_pb_post_main_image' : '';
				$titletext = get_the_title();
				$alttext   = get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true );
				$thumbnail = get_thumbnail( $width, $height, $classtext, $alttext, $titletext, false, 'Blogimage' );
				$thumb     = $thumbnail['thumb'];

				$no_thumb_class = '' === $thumb || ! $show_thumbnail ? ' et_pb_no_thumb' : '';

				if ( in_array( $post_format, array( 'video', 'gallery' ) ) ) {
					$no_thumb_class = '';
				}

				$item_class = sprintf( ' et_pb_blog_item_%1$s_%2$s', $blog_order, $items_count );
				$items_count++;
				?>

			<article id="post-<?php the_ID(); ?>" <?php post_class( 'et_pb_post clearfix' . $no_thumb_class . $overlay_class . $item_class ); ?>>

				<?php
				et_divi_post_format_content();

				if ( ! in_array( $post_format, array( 'link', 'audio', 'quote' ) ) || post_password_required( $post ) ) {
					if ( 'video' === $post_format && false !== ( $first_video = et_get_first_video() ) ) :
						$video_overlay = has_post_thumbnail() ? sprintf(
							'<div class="et_pb_video_overlay" style="background-image: url(%1$s); background-size: cover;">
								<div class="et_pb_video_overlay_hover">
									<a href="#" class="et_pb_video_play"></a>
								</div>
							</div>',
							$thumb
						) : '';

						printf(
							'<div class="et_main_video_container">
								%1$s
								%2$s
							</div>',
							et_core_esc_previously( $video_overlay ),
							et_core_esc_previously( $first_video )
						);
					elseif ( 'gallery' === $post_format ) :
						et_pb_gallery_images( 'slider' );
					elseif ( '' !== $thumb && $show_thumbnail ) :
						if ( 'on' !== $fullwidth ) {
							echo '<div class="et_pb_image_container">';
						}

						$thumbnail_output = print_thumbnail( $thumb, $thumbnail['use_timthumb'], $titletext, $width, $height, '', false );

						if ( 'on' === $use_overlay ) {
							$thumbnail_output .= et_core_esc_previously( $overlay_output );
						}

						if ( $thumbnail_output ) {
							$multi_view->render_element(
								array(
									'tag'            => 'a',
									'content'        => $thumbnail_output,
									'attrs'          => array(
										'href'  => get_the_permalink(),
										'class' => 'entry-featured-image-url',
									),
									'visibility'     => array(
										'show_thumbnail' => 'on',
									),
									'required'       => array(
										'show_thumbnail' => 'on',
									),
									'hover_selector' => '%%order_class%% .et_pb_post',
								),
								true
							);
						}

						if ( 'on' !== $fullwidth ) {
							echo '</div>';
						}
					endif;
				}
				?>

				<?php if ( 'off' === $fullwidth || ! in_array( $post_format, array( 'link', 'audio', 'quote' ) ) || post_password_required( $post ) ) { ?>
					<?php if ( ! in_array( $post_format, array( 'link', 'audio' ) ) || post_password_required( $post ) ) { ?>
					<<?php echo et_core_intentionally_unescaped( $processed_header_level, 'fixed_string' ); ?> class="entry-title">
						<?php if ( get_permalink() ) { ?>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						<?php } else { ?>
							<?php the_title(); ?>
						<?php } ?>
					</<?php echo et_core_intentionally_unescaped( $processed_header_level, 'fixed_string' ); ?>>
				<?php } ?>

					<?php
					if ( 'on' === $show_author || 'on' === $show_date || 'on' === $show_categories || 'on' === $show_comments ) {
						$multi_view->render_element(
							array(
								'tag'            => 'p',
								'content'        => '{{post_meta_removes}}',
								'attrs'          => array(
									'class' => 'post-meta',
								),
								'hover_selector' => '%%order_class%% .et_pb_post',
							),
							true
						);
					}

					echo '<div class="post-content">';

					$multi_view->render_element(
						array(
							'tag'            => 'div',
							'content'        => '{{post_content}}',
							'attrs'          => array(
								'class' => 'post-content-inner',
							),
							'visibility'     => array(
								'show_excerpt' => 'on',
							),
							'classes'        => array(
								'et_pb_blog_show_content' => array(
									'show_content' => 'on',
								),
							),
							'hover_selector' => '%%order_class%% .et_pb_post',
						),
						true
					);

					$more = $multi_view->render_element(
						array(
							'tag'            => 'a',
							'content'        => esc_html__( 'read more', 'et_builder' ),
							'attrs'          => array(
								'class' => 'more-link',
								'href'  => esc_url( get_permalink() ),
							),
							'visibility'     => array(
								'show_content' => 'off',
								'show_more'    => 'on',
							),
							'required'       => array(
								'show_content' => 'off',
								'show_more'    => 'on',
							),
							'hover_selector' => '%%order_class%% .et_pb_post',
						)
					);

					echo et_core_esc_previously( $more );

					echo '</div>';
					?>
			<?php } // 'off' === $fullwidth || ! in_array( $post_format, array( 'link', 'audio', 'quote', 'gallery' ?>

			</article>
				<?php
				ET_Post_Stack::pop();
			} // endwhile
			ET_Post_Stack::reset();

			if ( 'off' === $fullwidth ) {
				echo '</div>';
			}

			if ( $multi_view->has_value( 'show_pagination', 'on' ) ) {
				$multi_view->render_element(
					array(
						'tag'            => 'div',
						'content'        => $this->render_pagination( false ),
						'visibility'     => array(
							'show_pagination' => 'on',
						),
						'hover_selector' => '%%order_class%% .et_pb_post',
					),
					true
				);

				echo '</div>';

				$container_is_closed = true;
			}
		} elseif ( $show_no_results_template ) {
			echo self::get_no_results_template( et_core_intentionally_unescaped( $processed_header_level, 'fixed_string' ) );
		}

		unset( $wp_query->et_pb_blog_query );

		$wp_the_query = $wp_query = $main_query;
		wp_reset_query();
		ET_Post_Stack::reset();

		// Restore stashed properties.
		foreach ( $wp_query_props as $prop => $value ) {
			$wp_query->{$prop} = $value;
		}

		$posts = ob_get_contents();

		ob_end_clean();
		self::$rendering = false;

		// Remove automatically added classnames
		$this->remove_classname(
			array(
				$render_slug,
			)
		);

		// Background layout data attributes.
		$background_layout_props = array_merge(
			$this->props,
			array(
				'background_layout'        => $background_layout,
				'background_layout_tablet' => $background_layout_tablet,
				'background_layout_phone'  => $background_layout_phone,
			)
		);
		$data_background_layout  = et_pb_background_layout_options()->get_background_layout_attrs( $background_layout_props );

		if ( 'on' !== $fullwidth ) {
			// Module classname
			$this->add_classname(
				array(
					'et_pb_blog_grid_wrapper',
					"et_pb_bg_layout_{$background_layout}",
				)
			);

			// Remove auto-added classname for module wrapper because on grid mode these classnames
			// are placed one level below module wrapper
			$this->remove_classname(
				array(
					'et_pb_section_video',
					'et_pb_preload',
					'et_pb_section_parallax',
				)
			);

			// Inner module wrapper classname
			$inner_wrap_classname = array(
				'et_pb_blog_grid',
				'clearfix',
				$this->get_text_orientation_classname(),
			);

			// Background layout class names.
			$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $background_layout_props, false, true );
			array_merge( $inner_wrap_classname, $background_layout_class_names );

			if ( '' !== $video_background ) {
				$inner_wrap_classname[] = 'et_pb_section_video';
				$inner_wrap_classname[] = 'et_pb_preload';
			}

			if ( '' !== $parallax_image_background ) {
				$inner_wrap_classname[] = 'et_pb_section_parallax';
			}

			$output = sprintf(
				'<div%4$s class="%5$s"%9$s>
					<div class="%1$s">
					%7$s
					%6$s
					%10$s
					%11$s
					<div class="et_pb_ajax_pagination_container">
						%2$s
					</div>
					%3$s %8$s
				</div>',
				esc_attr( implode( ' ', $inner_wrap_classname ) ),
				$posts,
				( ! $container_is_closed ? '</div>' : '' ),
				$this->module_id(),
				$this->module_classname( $render_slug ), // #5
				$video_background,
				$parallax_image_background,
				$this->drop_shadow_back_compatibility( $render_slug ),
				et_core_esc_previously( $data_background_layout ),
				et_core_esc_previously( $this->background_pattern() ), // #10
				et_core_esc_previously( $this->background_mask() ) // #11
			);
		} else {
			// Module classname
			$this->add_classname(
				array(
					'et_pb_posts',
					"et_pb_bg_layout_{$background_layout}",
					$this->get_text_orientation_classname(),
				)
			);

			if ( ! empty( $background_layout_tablet ) ) {
				$this->add_classname( "et_pb_bg_layout_{$background_layout_tablet}_tablet" );
			}

			if ( ! empty( $background_layout_phone ) ) {
				$this->add_classname( "et_pb_bg_layout_{$background_layout_phone}_phone" );
			}

			$output = sprintf(
				'<div%4$s class="%1$s"%8$s>
				%6$s
				%5$s
				%9$s
				%10$s
				<div class="et_pb_ajax_pagination_container">
					%2$s
				</div>
				%3$s %7$s',
				$this->module_classname( $render_slug ),
				$posts,
				( ! $container_is_closed ? '</div>' : '' ),
				$this->module_id(),
				$video_background, // #5
				$parallax_image_background,
				$this->drop_shadow_back_compatibility( $render_slug ),
				et_core_esc_previously( $data_background_layout ),
				et_core_esc_previously( $this->background_pattern() ), // #9
				et_core_esc_previously( $this->background_mask() ) // #10
			);
		}

		// Restore $wp_filter
		$wp_filter = $wp_filter_cache; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
		unset( $wp_filter_cache );

		// Restore global $post into its original state when et_pb_blog shortcode ends to avoid
		// the rest of the page uses incorrect global $post variable
		$post = $post_cache; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited

		return $output;
	}

	public function process_box_shadow( $function_name ) {
		if ( isset( $this->props['fullwidth'] ) && $this->props['fullwidth'] === 'off' ) {
			// Only override 'default' box shadow because we also defined
			// box shadow settings for the image.
			$this->advanced_fields['box_shadow']['default'] = array(
				'css' => array(
					'main'    => '%%order_class%% article.et_pb_post',
					'hover'   => '%%order_class%% article.et_pb_post:hover',
					'overlay' => 'inset',
				),
			);
		}

		parent::process_box_shadow( $function_name );
	}

	/**
	 * Since the styling file is not updated until the author updates the page/post,
	 * we should keep the drop shadow visible.
	 *
	 * @param string $functions_name
	 *
	 * @return string
	 */
	private function drop_shadow_back_compatibility( $functions_name ) {
		$utils = ET_Core_Data_Utils::instance();
		$atts  = $this->props;

		if (
			version_compare( $utils->array_get( $atts, '_builder_version', '3.0.93' ), '3.0.94', 'lt' )
			&&
			'on' !== $utils->array_get( $atts, 'fullwidth' )
			&&
			'on' === $utils->array_get( $atts, 'use_dropshadow' )
		) {
			$class = self::get_module_order_class( $functions_name );

			return sprintf(
				'<style>%1$s</style>',
				sprintf( '.%1$s  article.et_pb_post { box-shadow: 0 1px 5px rgba(0,0,0,.1) }', esc_html( $class ) )
			);
		}

		return '';
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
		$name    = isset( $args['name'] ) ? $args['name'] : '';
		$mode    = isset( $args['mode'] ) ? $args['mode'] : '';
		$context = isset( $args['context'] ) ? $args['context'] : '';

		if ( 'post_content' === $name && 'content' === $context ) {
			global $et_pb_rendering_column_content;

			$post_content = et_strip_shortcodes( et_delete_post_first_video( get_the_content() ), true );

			$et_pb_rendering_column_content = true;

			if ( 'on' === $raw_value ) {
				global $more;

				if ( et_pb_is_pagebuilder_used( get_the_ID() ) ) {
					$more      = 1; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
					$raw_value = et_core_intentionally_unescaped( apply_filters( 'the_content', $post_content ), 'html' );
				} else {
					$more      = null; // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited
					$raw_value = et_core_intentionally_unescaped( apply_filters( 'the_content', et_delete_post_first_video( get_the_content( esc_html__( 'read more...', 'et_builder' ) ) ) ), 'html' );
				}
			} else {
				$use_manual_excerpt = isset( $this->props['use_manual_excerpt'] ) ? $this->props['use_manual_excerpt'] : 'off';
				$excerpt_length     = isset( $this->props['excerpt_length'] ) ? $this->props['excerpt_length'] : 270;

				if ( has_excerpt() && 'off' !== $use_manual_excerpt ) {
					/**
					 * Filters the displayed post excerpt.
					 *
					 * @since 3.29
					 *
					 * @param string $post_excerpt The post excerpt.
					 */
					$raw_value = apply_filters( 'the_excerpt', get_the_excerpt() );
				} else {
					$raw_value = et_core_intentionally_unescaped( wpautop( et_delete_post_first_video( strip_shortcodes( truncate_post( $excerpt_length, false, '', true ) ) ) ), 'html' );
				}
			}

			$et_pb_rendering_column_content = false;
		} elseif ( 'show_content' === $name && 'visibility' === $context ) {
			$raw_value = $multi_view->has_value( $name, 'on', $mode, true ) ? 'on' : $raw_value;
		} elseif ( 'post_meta_removes' === $name && 'content' === $context ) {
			$post_meta_remove_keys = array(
				'show_author'     => true,
				'show_date'       => true,
				'show_categories' => true,
				'show_comments'   => true,
			);

			$post_meta_removes = explode( ',', $raw_value );

			if ( $post_meta_removes ) {
				foreach ( $post_meta_removes as $post_meta_remove ) {
					unset( $post_meta_remove_keys[ $post_meta_remove ] );
				}
			}

			$post_meta_datas = array();

			if ( isset( $post_meta_remove_keys['show_author'] ) ) {
				$post_meta_datas[] = et_get_safe_localization( sprintf( __( 'by %s', 'et_builder' ), '<span class="author vcard">' . et_pb_get_the_author_posts_link() . '</span>' ) );
			}

			if ( isset( $post_meta_remove_keys['show_date'] ) ) {
				$post_meta_datas[] = et_get_safe_localization( sprintf( __( '%s', 'et_builder' ), '<span class="published">' . esc_html( get_the_date( $this->props['meta_date'] ) ) . '</span>' ) );
			}

			if ( isset( $post_meta_remove_keys['show_categories'] ) ) {
				$post_meta_datas[] = et_builder_get_the_term_list( ', ' );
			}

			if ( isset( $post_meta_remove_keys['show_comments'] ) ) {
				$post_meta_datas[] = sprintf( esc_html( _nx( '%s Comment', '%s Comments', get_comments_number(), 'number of comments', 'et_builder' ) ), number_format_i18n( get_comments_number() ) );
			}

			$raw_value = implode( ' | ', $post_meta_datas );
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Blog();
}
