<?php

class ET_Builder_Module_Comments extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Comments', 'et_builder' );
		$this->plural     = esc_html__( 'Comments', 'et_builder' );
		$this->slug       = 'et_pb_comments';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements' => et_builder_i18n( 'Elements' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'image' => array(
						'title'    => et_builder_i18n( 'Image' ),
						'priority' => 30,
					),
					'text'  => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'box_shadow'     => array(
				'default' => array(),
				'image'   => array(
					'label'           => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category' => 'layout',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'css'             => array(
						'main' => "{$this->main_css_element} .commentlist img.avatar",
					),
				),
			),
			'borders'        => array(
				'default' => array(
					'css' => array(
						'main'      => array(
							'border_radii'  => "{$this->main_css_element}",
							'border_styles' => "{$this->main_css_element}",
						),
						'important' => 'all',
					),
				),
				'image'   => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => '%%order_class%%.et_pb_comments_module .commentlist li img.avatar',
							'border_styles' => '%%order_class%%.et_pb_comments_module .commentlist li img.avatar',
						),
					),
					'label_prefix' => et_builder_i18n( 'Image' ),
					'tab_slug'     => 'advanced',
					'toggle_slug'  => 'image',
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'fonts'          => array(
				'header' => array(
					'label'        => esc_html__( 'Comment Count', 'et_builder' ),
					'css'          => array(
						'main' => "{$this->main_css_element} h1.page_title, {$this->main_css_element} h2.page_title, {$this->main_css_element} h3.page_title, {$this->main_css_element} h4.page_title, {$this->main_css_element} h5.page_title, {$this->main_css_element} h6.page_title",
					),
					'header_level' => array(
						'default' => 'h1',
					),
				),
				'title'  => array(
					'label'          => esc_html__( 'Form Title', 'et_builder' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .comment-reply-title",
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'font_size'      => array(
						'default' => '22px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'header_level'   => array(
						'default' => 'h3',
					),
				),
				'meta'   => array(
					'label'          => esc_html__( 'Meta', 'et_builder' ),
					'css'            => array(
						'main'       => "{$this->main_css_element} .comment_postinfo span",
						'important'  => 'all',
						'text_align' => "{$this->main_css_element} .comment_postinfo",
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'font_size'      => array(
						'default' => '14px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
				'body'   => array(
					'label'          => esc_html__( 'Comment', 'et_builder' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .comment-content p",
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'font_size'      => array(
						'default' => '14px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
			),
			'button'         => array(
				'button' => array(
					'label'          => et_builder_i18n( 'Button' ),
					'css'            => array(
						'main'         => "{$this->main_css_element}.et_pb_comments_module .et_pb_button",
						'limited_main' => "{$this->main_css_element}.et_pb_comments_module .et_pb_button",
						'alignment'    => "{$this->main_css_element} .form-submit",
					),
					'no_rel_attr'    => true,
					'use_alignment'  => true,
					'box_shadow'     => array(
						'css' => array(
							'main' => "{$this->main_css_element} .et_pb_button",
						),
					),
					'margin_padding' => array(
						'css' => array(
							'important' => 'all',
						),
					),
				),
			),
			'text'           => array(
				'use_background_layout' => true,
				'css'                   => array(
					'main'        => '%%order_class%% p, %%order_class%% .comment_postinfo *, %%order_class%% .page_title, %%order_class%% .comment-reply-title',
					'text_shadow' => '%%order_class%% p, %%order_class%% .comment_postinfo, %%order_class%% .page_title, %%order_class%% .comment-reply-title',
				),
				'options'               => array(
					'background_layout' => array(
						'default_on_front' => 'light',
						'hover'            => 'tabs',
					),
				),
			),
			'form_field'     => array(
				'form_field' => array(
					'label'         => esc_html__( 'Fields', 'et_builder' ),
					'css'           => array(
						'main'              => "{$this->main_css_element} #commentform textarea, {$this->main_css_element} #commentform input[type='text'], {$this->main_css_element} #commentform input[type='email'], {$this->main_css_element} #commentform input[type='url']",
						'hover'             => "{$this->main_css_element} #commentform textarea:hover, {$this->main_css_element} #commentform input[type='text']:hover, {$this->main_css_element} #commentform input[type='email']:hover, {$this->main_css_element} #commentform input[type='url']:hover",
						'focus'             => "{$this->main_css_element} #commentform textarea:focus, {$this->main_css_element} #commentform input[type='text']:focus, {$this->main_css_element} #commentform input[type='email']:focus, {$this->main_css_element} #commentform input[type='url']:focus",
						'focus_hover'       => "{$this->main_css_element} #commentform textarea:focus:hover, {$this->main_css_element} #commentform input[type='text']:focus:hover, {$this->main_css_element} #commentform input[type='email']:focus:hover, {$this->main_css_element} #commentform input[type='url']:focus:hover",
						'placeholder'       => "{$this->main_css_element} #commentform textarea::-webkit-input-placeholder, {$this->main_css_element} #commentform textarea::-moz-placeholder, {$this->main_css_element} #commentform textarea:-ms-input-placeholder, {$this->main_css_element} #commentform input::-webkit-input-placeholder, {$this->main_css_element} #commentform input::-moz-placeholder, {$this->main_css_element} #commentform input:-ms-input-placeholder",
						'placeholder_focus' => "{$this->main_css_element} #commentform textarea:focus::-webkit-input-placeholder, {$this->main_css_element} #commentform textarea:focus::-moz-placeholder, {$this->main_css_element} #commentform textarea:focus:-ms-input-placeholder, {$this->main_css_element} #commentform input:focus::-webkit-input-placeholder, {$this->main_css_element} #commentform input:focus::-moz-placeholder, {$this->main_css_element} #commentform input:focus:-ms-input-placeholder",
						'margin'            => "{$this->main_css_element} #commentform .comment-form-comment, {$this->main_css_element} #commentform .comment-form-author, {$this->main_css_element} #commentform .comment-form-email, {$this->main_css_element} #commentform .comment-form-url",
					),
					'box_shadow'    => array(
						'name' => 'fields',
						'css'  => array(
							'main' => "{$this->main_css_element} #commentform textarea, {$this->main_css_element} #commentform input[type='text'], {$this->main_css_element} #commentform input[type='email'], {$this->main_css_element} #commentform input[type='url']",
						),
					),
					'border_styles' => array(
						'form_field'       => array(
							'name'         => 'fields',
							'css'          => array(
								'main'      => array(
									'border_radii'  => "{$this->main_css_element} #commentform textarea, {$this->main_css_element} #commentform input[type='text'], {$this->main_css_element} #commentform input[type='email'], {$this->main_css_element} #commentform input[type='url']",
									'border_styles' => "{$this->main_css_element} #commentform textarea, {$this->main_css_element} #commentform input[type='text'], {$this->main_css_element} #commentform input[type='email'], {$this->main_css_element} #commentform input[type='url']",
								),
								'important' => 'all',
							),
							'label_prefix' => esc_html__( 'Fields', 'et_builder' ),
						),
						'form_field_focus' => array(
							'name'         => 'fields_focus',
							'css'          => array(
								'main' => array(
									'border_radii'  => "{$this->main_css_element} #commentform textarea:focus, {$this->main_css_element} #commentform input[type='text']:focus, {$this->main_css_element} #commentform input[type='email']:focus, {$this->main_css_element} #commentform input[type='url']:focus",
									'border_styles' => "{$this->main_css_element} #commentform textarea:focus, {$this->main_css_element} #commentform input[type='text']:focus, {$this->main_css_element} #commentform input[type='email']:focus, {$this->main_css_element} #commentform input[type='url']:focus",
								),
							),
							'label_prefix' => esc_html__( 'Fields Focus', 'et_builder' ),
						),
					),
					'font_field'    => array(
						'css'            => array(
							'main'      => "{$this->main_css_element} #commentform textarea, {$this->main_css_element} #commentform input[type='text'], {$this->main_css_element} #commentform input[type='email'], {$this->main_css_element} #commentform input[type='url'], {$this->main_css_element} #commentform label",
							'important' => 'all',
						),
						'line_height'    => array(
							'default' => '1em',
						),
						'font_size'      => array(
							'default' => '18px',
						),
						'letter_spacing' => array(
							'default' => '0px',
						),
					),
				),
			),
			'filters'        => array(
				'child_filters_target' => array(
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'          => array(
				'css' => array(
					'main' => "{$this->main_css_element} .commentlist img.avatar",
				),
			),
		);

		$this->custom_css_fields = array(
			'main_header'     => array(
				'label'    => esc_html__( 'Comments Count', 'et_builder' ),
				'selector' => 'h1#comments',
			),
			'comment_body'    => array(
				'label'    => esc_html__( 'Comment Body', 'et_builder' ),
				'selector' => '.comment-body',
			),
			'comment_meta'    => array(
				'label'    => esc_html__( 'Comment Meta', 'et_builder' ),
				'selector' => '.comment_postinfo',
			),
			'comment_content' => array(
				'label'    => esc_html__( 'Comment Content', 'et_builder' ),
				'selector' => '.comment_area .comment-content',
			),
			'comment_avatar'  => array(
				'label'    => esc_html__( 'Comment Avatar', 'et_builder' ),
				'selector' => '.comment_avatar',
			),
			'reply_button'    => array(
				'label'    => esc_html__( 'Reply Button', 'et_builder' ),
				'selector' => '.comment-reply-link.et_pb_button',
			),
			'new_title'       => array(
				'label'    => esc_html__( 'New Comment Title', 'et_builder' ),
				'selector' => 'h3#reply-title',
			),
			'message_field'   => array(
				'label'    => esc_html__( 'Message Field', 'et_builder' ),
				'selector' => '.comment-form-comment textarea#comment',
			),
			'name_field'      => array(
				'label'    => esc_html__( 'Name Field', 'et_builder' ),
				'selector' => '.comment-form-author input',
			),
			'email_field'     => array(
				'label'    => esc_html__( 'Email Field', 'et_builder' ),
				'selector' => '.comment-form-email input',
			),
			'website_field'   => array(
				'label'    => esc_html__( 'Website Field', 'et_builder' ),
				'selector' => '.comment-form-url input',
			),
			'submit_button'   => array(
				'label'    => esc_html__( 'Submit Button', 'et_builder' ),
				'selector' => '.form-submit .et_pb_button#et_pb_submit',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'k6vskmOxM4U',
				'name' => esc_html__( 'An introduction to the Comments module', 'et_builder' ),
			),
		);
	}

	function get_fields() {

		$fields = array(
			'show_avatar' => array(
				'label'            => esc_html__( 'Show Author Avatar', 'et_builder' ),
				'description'      => esc_html__( 'Disabling the author avatar will remove the profile picture from the module.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'toggle_slug'      => 'elements',
				'default_on_front' => 'on',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_reply'  => array(
				'label'            => esc_html__( 'Show Reply Button', 'et_builder' ),
				'description'      => esc_html__( 'Disabling the reply button will prevent visitors from creating threaded comments.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'toggle_slug'      => 'elements',
				'default_on_front' => 'on',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_count'  => array(
				'label'            => esc_html__( 'Show Comment Count', 'et_builder' ),
				'description'      => esc_html__( 'Disabling the comment count will remove the number of comments from the top of the module.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'toggle_slug'      => 'elements',
				'default_on_front' => 'on',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_meta'   => array(
				'label'            => esc_html__( 'Show Meta', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => esc_html__( 'Yes', 'et_builder' ),
					'off' => esc_html__( 'No', 'et_builder' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'description'      => esc_html__( 'Turn meta on or off.', 'et_builder' ),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
		);

		return $fields;
	}

	/**
	 * Get comments markup for comments module
	 *
	 * @since 4.0.9 Add custom form title heading level.
	 *
	 * @param {string} $header_level
	 * @param {string} $form_title_level
	 *
	 * @return string of comment section markup
	 */
	static function get_comments( $header_level, $form_title_level ) {
		global $et_pb_comments_print, $et_comments_header_level, $et_comments_form_title_level;

		// Globally flag that comment module is being printed
		$et_pb_comments_print = true;

		// set custom header level for comments form
		$et_comments_header_level     = $header_level;
		$et_comments_form_title_level = $form_title_level;

		// remove filters to make sure comments module rendered correctly if the below filters were applied earlier.
		remove_filter( 'get_comments_number', '__return_zero' );
		remove_filter( 'comments_open', '__return_false' );
		remove_filter( 'comments_array', '__return_empty_array' );

		// Custom action before calling comments_template.
		do_action( 'et_fb_before_comments_template' );

		ob_start();
		comments_template( '', true );
		$comments_content = ob_get_contents();
		ob_end_clean();

		// Custom action after calling comments_template.
		do_action( 'et_fb_after_comments_template' );

		// Globally flag that comment module has been printed
		$et_pb_comments_print     = false;
		$et_comments_header_level = '';

		return $comments_content;
	}

	/**
	 * Action and filter hooks that are called before comment content rendering. These are
	 * abstracted into method so module which extends comment module can modify these
	 *
	 * @since 3.29
	 */
	function before_comments_content() {
		// Modify the comments request to make sure it's unique.
		// Otherwise WP generates SQL error and doesn't allow multiple comments sections on single page
		add_action( 'pre_get_comments', array( $this, 'et_pb_modify_comments_request' ), 1 );

		// include custom comments_template to display the comment section with Divi style
		add_filter( 'comments_template', array( $this, 'et_pb_comments_template' ) );

		// Modify submit button to be advanced button style ready
		add_filter( 'comment_form_submit_button', array( $this, 'et_pb_comments_submit_button' ) );
	}

	/**
	 * Comment content rendering. These are abstracted into method so module which extends comment
	 * module can modify these
	 *
	 * @since 3.29
	 * @since 4.0.9 Add form title heading level.
	 */
	function get_comments_content() {
		$header_level               = et_()->array_get( $this->props, 'header_level' );
		$form_title_level           = et_()->array_get( $this->props, 'title_level' );
		$header_level_processed     = et_pb_process_header_level( $header_level, 'h1' );
		$form_title_level_processed = et_pb_process_header_level( $form_title_level, 'h3' );

		return self::get_comments( $header_level_processed, $form_title_level_processed );
	}

	/**
	 * Action and filter hooks that are called after comment content rendering. These are
	 * abstracted into method so module which extends comment module can modify these
	 */
	function after_comments_content() {
		// remove all the actions and filters to not break the default comments section from theme
		remove_filter( 'comments_template', array( $this, 'et_pb_comments_template' ) );
		remove_action( 'pre_get_comments', array( $this, 'et_pb_modify_comments_request' ), 1 );
	}

	function et_pb_comments_template() {
		return realpath( dirname( __FILE__ ) . '/..' ) . '/comments_template.php';
	}

	function et_pb_comments_submit_button( $submit_button ) {
		return sprintf(
			'<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
			esc_attr( 'submit' ),
			esc_attr( 'et_pb_submit' ),
			esc_attr( 'submit' ),
			esc_html__( 'Submit Comment', 'et_builder' )
		);
	}

	function et_pb_modify_comments_request( $params ) {
		// modify the request parameters the way it doesn't change the result just to make request with unique parameters
		$params->query_vars['type__not_in'] = 'et_pb_comments_random_type_' . $this->et_pb_unique_comments_module_class;
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
		$multi_view                = et_pb_multi_view_options( $this );
		$button_custom             = $this->props['custom_button'];
		$show_avatar               = $this->props['show_avatar'];
		$show_reply                = $this->props['show_reply'];
		$show_count                = $this->props['show_count'];
		$show_meta                 = $this->props['show_meta'];
		$show_rating               = et_()->array_get( $this->props, 'show_rating', '' );
		$header_level              = $this->props['header_level'];
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$custom_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'button_icon' );
		$custom_icon        = isset( $custom_icon_values['desktop'] ) ? $custom_icon_values['desktop'] : '';
		$custom_icon_tablet = isset( $custom_icon_values['tablet'] ) ? $custom_icon_values['tablet'] : '';
		$custom_icon_phone  = isset( $custom_icon_values['phone'] ) ? $custom_icon_values['phone'] : '';

		$this->et_pb_unique_comments_module_class = ET_Builder_Element::get_module_order_class( $render_slug ); // use this variable to make the comments request unique for each module instance

		// Action & filter hooks before comment content rendering
		$this->before_comments_content();

		// Comment content rendering
		$comments_content = $this->get_comments_content();

		// Action & filter hooks after comment content rendering
		$this->after_comments_content();

		// Image - CSS Filters.
		if ( et_()->array_get( $this->advanced_fields, 'image.css', false ) ) {
			$this->add_classname(
				$this->generate_css_filters(
					$this->slug,
					'child_',
					et_()->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' )
				)
			);
		}

		$comments_custom_icon        = 'on' === $button_custom ? $custom_icon : '';
		$comments_custom_icon_tablet = 'on' === $button_custom ? $custom_icon_tablet : '';
		$comments_custom_icon_phone  = 'on' === $button_custom ? $custom_icon_phone : '';

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		// Module classname
		$this->add_classname(
			array(
				'et_pb_comments_module',
				$this->get_text_orientation_classname(),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		if ( 'off' === $show_avatar ) {
			$this->add_classname( 'et_pb_no_avatar' );
		}

		if ( 'off' === $show_reply ) {
			$this->add_classname( 'et_pb_no_reply_button' );
		}

		if ( 'off' === $show_count ) {
			$this->add_classname( 'et_pb_no_comments_count' );
		}

		if ( 'off' === $show_meta ) {
			$this->add_classname( 'et_pb_no_comments_meta' );
		}

		if ( 'off' === $show_rating ) {
			$this->add_classname( 'et_pb_no_comments_rating' );
		}

		// Removed automatically added classname
		$this->remove_classname( $render_slug );

		$multi_view_data_attr = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_no_avatar'          => array(
						'show_avatar' => 'off',
					),
					'et_pb_no_reply_button'    => array(
						'show_reply' => 'off',
					),
					'et_pb_no_comments_count'  => array(
						'show_count' => 'off',
					),
					'et_pb_no_comments_meta'   => array(
						'show_meta' => 'off',
					),
					/* WooCommerce Reviews Module uses the following class. */
					'et_pb_no_comments_rating' => array(
						'show_rating' => 'off',
					),
				),
			)
		);

		$output = sprintf(
			'<div%3$s class="%2$s"%4$s%7$s%8$s%9$s%10$s>
				%5$s
				%11$s
				%12$s
				%6$s
				%1$s
			</div>',
			$comments_content,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			'' !== $comments_custom_icon ? sprintf( ' data-icon="%1$s"', esc_attr( et_pb_process_font_icon( $comments_custom_icon ) ) ) : '',
			$video_background, // #5
			$parallax_image_background,
			et_core_esc_previously( $data_background_layout ),
			'' !== $comments_custom_icon_tablet ? sprintf( ' data-icon-tablet="%1$s"', esc_attr( et_pb_process_font_icon( $comments_custom_icon_tablet ) ) ) : '',
			'' !== $comments_custom_icon_phone ? sprintf( ' data-icon-phone="%1$s"', esc_attr( et_pb_process_font_icon( $comments_custom_icon_phone ) ) ) : '',
			$multi_view_data_attr, // #10
			et_core_esc_previously( $this->background_pattern() ), // #11
			et_core_esc_previously( $this->background_mask() ) // #12
		);

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Comments();
}

if ( et_is_woocommerce_plugin_active() && defined( 'ET_BUILDER_DIR' ) ) {
	// Use separate files for better organization.
	require_once ET_BUILDER_DIR . 'module/woocommerce/Reviews.php';
}
