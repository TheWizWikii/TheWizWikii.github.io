<?php

class ET_Builder_Module_Posts_Navigation extends ET_Builder_Module {
	function init() {
		$this->name             = esc_html__( 'Post Navigation', 'et_builder' );
		$this->plural           = esc_html__( 'Post Navigations', 'et_builder' );
		$this->slug             = 'et_pb_post_nav';
		$this->vb_support       = 'on';
		$this->main_css_element = '.et_pb_posts_nav%%order_class%%';

		$this->defaults = array();

		$this->settings_modal_toggles = array(
			'general' => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'categories'   => esc_html__( 'Categories', 'et_builder' ),
					'navigation'   => esc_html__( 'Navigation', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'title' => array(
					'label'           => esc_html__( 'Links', 'et_builder' ),
					'css'             => array(
						'main' => "{$this->main_css_element} span a, {$this->main_css_element} span a span",
					),
					'line_height'     => array(
						'default' => '1em',
					),
					'font_size'       => array(
						'default' => '14px',
					),
					'letter_spacing'  => array(
						'default' => '0px',
					),
					'hide_text_align' => true,
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main' => "{$this->main_css_element} span.nav-previous a, {$this->main_css_element} span.nav-next a",
				),
			),
			'background'     => array(
				'css' => array(
					'main' => "{$this->main_css_element} a",
				),
			),
			'borders'        => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} span.nav-previous a, {$this->main_css_element} span.nav-next a",
							'border_styles' => "{$this->main_css_element} span.nav-previous a, {$this->main_css_element} span.nav-next a",
						),
					),
				),
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main'      => '%%order_class%% .nav-previous a, %%order_class%% .nav-next a',
						'overlay'   => 'inset',
						'important' => true,
					),
				),
			),
			'text'           => false,
			'button'         => false,
			'link_options'   => false,
		);

		$this->custom_css_fields = array(
			'links'           => array(
				'label'    => esc_html__( 'Links', 'et_builder' ),
				'selector' => 'span a',
			),
			'prev_link'       => array(
				'label'    => esc_html__( 'Previous Link', 'et_builder' ),
				'selector' => 'span.nav-previous a',
			),
			'prev_link_arrow' => array(
				'label'    => esc_html__( 'Previous Link Arrow', 'et_builder' ),
				'selector' => 'span.nav-previous .meta-nav',
			),
			'next_link'       => array(
				'label'    => esc_html__( 'Next Link', 'et_builder' ),
				'selector' => 'span.nav-next a',
			),
			'next_link_arrow' => array(
				'label'    => esc_html__( 'Next Link Arrow', 'et_builder' ),
				'selector' => 'span.nav-next .meta-nav',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'q7SrK2sh7_o',
				'name' => esc_html__( 'An introduction to the Post Navigation module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'in_same_term'       => array(
				'label'            => esc_html__( 'Navigate Within Current Category', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'affects'          => array(
					'taxonomy_name',
				),
				'description'      => esc_html__( 'Here you can define whether previous and next posts must be within the same taxonomy term as the current post', 'et_builder' ),
				'toggle_slug'      => 'categories',
				'computed_affects' => array(
					'__posts_navigation',
				),
			),
			'taxonomy_name'      => array(
				'label'            => esc_html__( 'Custom Taxonomy Name', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'depends_show_if'  => 'on',
				'description'      => esc_html__( 'Leave blank if you\'re using this module on a Project or Post. Otherwise type the taxonomy name to make the \'In the Same Category\' option work correctly', 'et_builder' ),
				'toggle_slug'      => 'categories',
				'computed_affects' => array(
					'__posts_navigation',
				),
			),
			'show_prev'          => array(
				'label'            => esc_html__( 'Show Previous Post Link', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'affects'          => array(
					'prev_text',
				),
				'toggle_slug'      => 'navigation',
				'description'      => esc_html__( 'Turn this on to show the previous post link', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_next'          => array(
				'label'            => esc_html__( 'Show Next Post Link', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'affects'          => array(
					'next_text',
				),
				'toggle_slug'      => 'navigation',
				'description'      => esc_html__( 'Turn this on to show the next post link', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'prev_text'          => array(
				'label'            => esc_html__( 'Previous Link', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'depends_show_if'  => 'on',
				'computed_affects' => array(
					'__posts_navigation',
				),
				'description'      => et_get_safe_localization( __( 'Define custom text for the previous link. You can use the <strong>%title</strong> variable to include the post title. Leave blank for default.', 'et_builder' ) ),
				'toggle_slug'      => 'main_content',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'next_text'          => array(
				'label'            => esc_html__( 'Next Link', 'et_builder' ),
				'type'             => 'text',
				'option_category'  => 'configuration',
				'depends_show_if'  => 'on',
				'computed_affects' => array(
					'__posts_navigation',
				),
				'description'      => et_get_safe_localization( __( 'Define custom text for the next link. You can use the <strong>%title</strong> variable to include the post title. Leave blank for default.', 'et_builder' ) ),
				'toggle_slug'      => 'main_content',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'__posts_navigation' => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Posts_Navigation', 'get_posts_navigation' ),
				'computed_depends_on' => array(
					'in_same_term',
					'taxonomy_name',
					'prev_text',
					'next_text',
				),
			),
		);
		return $fields;
	}

	/**
	 * Get prev and next post link data for frontend builder's post navigation module component
	 *
	 * @param int    post ID
	 * @param bool   show posts which uses same link only or not
	 * @param string excluded terms name
	 * @param string taxonomy name for in_same_terms
	 *
	 * @return string JSON encoded array of post's next and prev link
	 */
	static function get_posts_navigation( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		global $post;

		$defaults = array(
			'in_same_term'  => 'off',
			'taxonomy_name' => 'category',
			'prev_text'     => '%title',
			'next_text'     => '%title',
		);

		$args = wp_parse_args( $args, $defaults );

		// taxonomy name overwrite if in_same_term option is set to off and no taxonomy name defined
		if ( '' === $args['taxonomy_name'] || 'on' !== $args['in_same_term'] ) {
			$is_singular_project   = isset( $conditional_tags['is_singular_project'] ) ? $conditional_tags['is_singular_project'] === 'true' : is_singular( 'project' );
			$args['taxonomy_name'] = $is_singular_project ? 'project_category' : 'category';
		}

		$in_same_term = ! $args['in_same_term'] || 'off' === $args['in_same_term'] ? false : true;

		et_core_nonce_verified_previously();
		if ( ! isset( $post ) && defined( 'DOING_AJAX' ) && DOING_AJAX && ! empty( $_POST['et_post_id'] ) ) {
			$post_id = sanitize_text_field( $_POST['et_post_id'] );
		} elseif ( isset( $current_page['id'] ) ) {
			// Overwrite global $post value in this scope
			$post_id = intval( $current_page['id'] );
		} elseif ( is_object( $post ) && isset( $post->ID ) ) {
			$post_id = $post->ID;
		} else {
			return array(
				'next' => '',
				'prev' => '',
			);
		}

		// Set current post as global $post
		$post = get_post( $post_id ); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited

		// Get next post.
		if ( is_et_theme_builder_template_preview() ) {
			$next_post = (object) array(
				'post_title' => esc_html__( 'Next Post', 'et_builder' ),
				'post_date'  => current_time( 'mysql', false ),
				'ID'         => 0,
			);
		} else {
			$next_post = get_next_post( $in_same_term, '', $args['taxonomy_name'] );
		}

		$next = new stdClass();

		if ( ! empty( $next_post ) ) {

			$next_title = isset( $next_post->post_title ) ? esc_html( $next_post->post_title ) : esc_html__( 'Next Post' );

			$next_date      = mysql2date( get_option( 'date_format' ), $next_post->post_date );
			$next_permalink = isset( $next_post->ID ) ? esc_url( get_the_permalink( $next_post->ID ) ) : '';

			$next_processed_title = '' === $args['next_text'] ? '%title' : $args['next_text'];

			// process WordPress' wildcards
			$next_processed_title = str_replace( '%title', $next_title, $next_processed_title );
			$next_processed_title = str_replace( '%date', $next_date, $next_processed_title );
			$next_processed_title = str_replace( '%link', $next_permalink, $next_processed_title );

			$next->title     = $next_processed_title;
			$next->id        = isset( $next_post->ID ) ? intval( $next_post->ID ) : '';
			$next->permalink = $next_permalink;
		}

		// Get prev post.
		if ( is_et_theme_builder_template_preview() ) {
			$prev_post = (object) array(
				'post_title' => esc_html__( 'Previous Post', 'et_builder' ),
				'post_date'  => current_time( 'mysql', false ),
				'ID'         => 0,
			);
		} else {
			$prev_post = get_previous_post( $in_same_term, '', $args['taxonomy_name'] );
		}

		$prev = new stdClass();

		if ( ! empty( $prev_post ) ) {

			$prev_title = isset( $prev_post->post_title ) ? esc_html( $prev_post->post_title ) : esc_html__( 'Previous Post' );

			$prev_date = mysql2date( get_option( 'date_format' ), $prev_post->post_date );

			$prev_permalink = isset( $prev_post->ID ) ? esc_url( get_the_permalink( $prev_post->ID ) ) : '';

			$prev_processed_title = '' === $args['prev_text'] ? '%title' : $args['prev_text'];

			// process WordPress' wildcards
			$prev_processed_title = str_replace( '%title', $prev_title, $prev_processed_title );
			$prev_processed_title = str_replace( '%date', $prev_date, $prev_processed_title );
			$prev_processed_title = str_replace( '%link', $prev_permalink, $prev_processed_title );

			$prev->title     = $prev_processed_title;
			$prev->id        = isset( $prev_post->ID ) ? intval( $prev_post->ID ) : '';
			$prev->permalink = $prev_permalink;
		}

		// Formatting returned value
		$posts_navigation = array(
			'next' => $next,
			'prev' => $prev,
		);

		return $posts_navigation;
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
		$multi_view    = et_pb_multi_view_options( $this );
		$in_same_term  = $this->props['in_same_term'];
		$taxonomy_name = $this->props['taxonomy_name'];
		$show_prev     = $this->props['show_prev'];
		$show_next     = $this->props['show_next'];
		$prev_text     = $this->props['prev_text'];
		$next_text     = $this->props['next_text'];

		// do not output anything if both prev and next links are disabled
		if ( ! $multi_view->has_value( 'show_prev', 'on' ) && ! $multi_view->has_value( 'show_next', 'on' ) ) {
			return;
		}

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$posts_navigation = self::get_posts_navigation(
			array(
				'in_same_term'  => $in_same_term,
				'taxonomy_name' => $taxonomy_name,
				'prev_text'     => $prev_text,
				'next_text'     => $next_text,
			)
		);

		ob_start();

		$background_classname = array();

		if ( '' !== $video_background ) {
			$background_classname[] = 'et_pb_section_video';
			$background_classname[] = 'et_pb_preload';

		}

		if ( '' !== $parallax_image_background ) {
			$background_classname[] = 'et_pb_section_parallax';
		}

		$background_class_attr = empty( $background_classname ) ? '' : sprintf( ' class="%s"', esc_attr( implode( ' ', $background_classname ) ) );

		if ( $multi_view->has_value( 'show_prev', 'on' ) && ! empty( $posts_navigation['prev']->permalink ) ) {
			?>
				<span class="nav-previous"
				<?php
				$multi_view->render_attrs(
					array(
						'visibility' => array(
							'show_prev' => 'on',
						),
					),
					true
				);
				?>
					>
					<a href="<?php echo esc_url( $posts_navigation['prev']->permalink ); ?>" rel="prev"<?php echo et_core_esc_previously( $background_class_attr ); ?>>
						<?php
							echo et_core_esc_previously( $parallax_image_background );
							echo et_core_esc_previously( $video_background );
							echo et_core_esc_previously( $this->background_pattern() );
							echo et_core_esc_previously( $this->background_mask() );
						?>
						<span class="meta-nav">&larr; </span><span class="nav-label"<?php $multi_view->render_attrs( array( 'content' => '{{prev_text}}' ), true ); ?>><?php echo esc_html( $posts_navigation['prev']->title ); ?></span>
					</a>
				</span>
			<?php
		}

		if ( $multi_view->has_value( 'show_next', 'on' ) && ! empty( $posts_navigation['next']->permalink ) ) {
			?>
				<span class="nav-next"
				<?php
				$multi_view->render_attrs(
					array(
						'visibility' => array(
							'show_next' => 'on',
						),
					),
					true
				);
				?>
					>
					<a href="<?php echo esc_url( $posts_navigation['next']->permalink ); ?>" rel="next"<?php echo et_core_esc_previously( $background_class_attr ); ?>>
						<?php
							echo et_core_esc_previously( $parallax_image_background );
							echo et_core_esc_previously( $video_background );
							echo et_core_esc_previously( $this->background_pattern() );
							echo et_core_esc_previously( $this->background_mask() );
						?>
						<span class="nav-label"<?php $multi_view->render_attrs( array( 'content' => '{{next_text}}' ), true ); ?>><?php echo esc_html( $posts_navigation['next']->title ); ?></span><span class="meta-nav"> &rarr;</span>
					</a>
				</span>
			<?php
		}

		$page_links = ob_get_contents();

		ob_end_clean();

		// Module classname
		$this->add_classname(
			array(
				'et_pb_posts_nav',
				'nav-single',
			)
		);

		// Remove automatically added module classname
		$this->remove_classname(
			array(
				$render_slug,
				'et_pb_section_video',
				'et_pb_preload',
				'et_pb_section_parallax',
			)
		);

		$output = sprintf(
			'<div class="%2$s"%1$s>
				%3$s
			</div>',
			$this->module_id(),
			$this->module_classname( $render_slug ),
			$page_links
		);

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Posts_Navigation();
}
