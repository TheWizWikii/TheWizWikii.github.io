<?php

class ET_Builder_Module_Login extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Login', 'et_builder' );
		$this->plural     = esc_html__( 'Logins', 'et_builder' );
		$this->slug       = 'et_pb_login';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%.et_pb_login';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'redirect'     => esc_html__( 'Redirect', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text' => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'header' => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} h2, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h3.et_pb_module_header, {$this->main_css_element} h4.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header",
						'important' => 'all',
					),
					'header_level' => array(
						'default' => 'h2',
					),
				),
				'body'   => array(
					'label'          => et_builder_i18n( 'Body' ),
					'css'            => array(
						'line_height' => "{$this->main_css_element} p",
						'font'        => "{$this->main_css_element}, {$this->main_css_element} .et_pb_newsletter_description_content, {$this->main_css_element} p, {$this->main_css_element} span",
						'text_shadow' => "{$this->main_css_element}, {$this->main_css_element} .et_pb_newsletter_description_content, {$this->main_css_element} p, {$this->main_css_element} span",
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
						'css'               => array(
							'main' => "{$this->main_css_element}",
						),
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'button'         => array(
				'button' => array(
					'label'          => et_builder_i18n( 'Button' ),
					'css'            => array(
						'main'         => "{$this->main_css_element} .et_pb_newsletter_button.et_pb_button",
						'limited_main' => "{$this->main_css_element} .et_pb_newsletter_button.et_pb_button",
					),
					'no_rel_attr'    => true,
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
			'background'     => array(
				'has_background_color_toggle' => true,
				'use_background_color'        => true,
				'options'                     => array(
					'background_color'     => array(
						'depends_show_if' => 'on',
						'default'         => et_builder_accent_color(),
					),
					'use_background_color' => array(
						'default' => 'on',
					),
				),
			),
			'text'           => array(
				'use_background_layout' => true,
				'options'               => array(
					'text_orientation'  => array(
						'default' => 'left',
					),
					'background_layout' => array(
						'default' => 'dark',
						'hover'   => 'tabs',
					),
				),
				'css'                   => array(
					'main' => implode(
						', ',
						array(
							'%%order_class%% .et_pb_module_header',
							'%%order_class%% .et_pb_newsletter_description_content',
							'%%order_class%% .et_pb_forgot_password a',
						)
					),
				),
			),
			'form_field'     => array(
				'form_field' => array(
					'label'         => esc_html__( 'Fields', 'et_builder' ),
					'css'           => array(
						'main'              => '%%order_class%% input[type="password"], %%order_class%% input[type="text"], %%order_class%% textarea, %%order_class%% .input',
						'hover'             => '%%order_class%% input[type="text"]:hover, %%order_class%% textarea:hover, %%order_class%% .input:hover',
						'focus'             => '%%order_class%% .et_pb_newsletter_form p input:focus',
						'focus_hover'       => '%%order_class%% .et_pb_newsletter_form p input:focus:hover',
						'placeholder_focus' => '%%order_class%% .et_pb_newsletter_form p input:focus::-webkit-input-placeholder, %%order_class%% .et_pb_newsletter_form p input:focus::-moz-placeholder, %%order_class%% .et_pb_newsletter_form p input:focus:-ms-input-placeholder',
						'padding'           => '%%order_class%% .et_pb_newsletter_form .input',
						'margin'            => '%%order_class%% .et_pb_newsletter_form .et_pb_contact_form_field',
						'important'         => array( 'padding', 'margin' ),
					),
					'box_shadow'    => array(
						'name' => 'fields',
						'css'  => array(
							'main' => '%%order_class%% .et_pb_newsletter_form .input',
						),
					),
					'border_styles' => array(
						'form_field'       => array(
							'name'         => 'fields',
							'css'          => array(
								'main' => array(
									'border_radii'  => '%%order_class%% .et_pb_newsletter_form p input',
									'border_styles' => '%%order_class%% .et_pb_newsletter_form p input',
								),
							),
							'label_prefix' => esc_html__( 'Fields', 'et_builder' ),
						),
						'form_field_focus' => array(
							'name'         => 'fields_focus',
							'css'          => array(
								'main' => array(
									'border_radii'  => '%%order_class%% .et_pb_newsletter_form p input:focus',
									'border_styles' => '%%order_class%% .et_pb_newsletter_form p input:focus',
								),
							),
							'label_prefix' => esc_html__( 'Fields Focus', 'et_builder' ),
						),
					),
					'font_field'    => array(
						'css' => array(
							'main'        => '%%order_class%% .et_pb_newsletter_form .input',
							'hover'       => '%%order_class%% .et_pb_newsletter_form .input:hover',
							'text_shadow' => "{$this->main_css_element} input",
							'important'   => 'plugin_only',
						),
					),
				),
			),
		);

		$this->custom_css_fields = array(
			'newsletter_title'       => array(
				'label'    => esc_html__( 'Login Title', 'et_builder' ),
				'selector' => "{$this->main_css_element} h2, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h3.et_pb_module_header, {$this->main_css_element} h4.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header",
			),
			'newsletter_description' => array(
				'label'    => esc_html__( 'Login Description', 'et_builder' ),
				'selector' => '.et_pb_newsletter_description',
			),
			'newsletter_form'        => array(
				'label'    => esc_html__( 'Login Form', 'et_builder' ),
				'selector' => '.et_pb_newsletter_form',
			),
			'newsletter_fields'      => array(
				'label'    => esc_html__( 'Login Fields', 'et_builder' ),
				'selector' => '.et_pb_newsletter_form input',
			),
			'newsletter_button'      => array(
				'label'                    => esc_html__( 'Login Button', 'et_builder' ),
				'selector'                 => '.et_pb_login .et_pb_login_form .et_pb_newsletter_button.et_pb_button',
				'no_space_before_selector' => true,
			),
		);

		$this->help_videos = array(
			array(
				'id'   => '6ZEw-Izfjg8',
				'name' => esc_html__( 'An introduction to the Login module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title'                 => array(
				'label'           => et_builder_i18n( 'Title' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Choose a title of your login box.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'current_page_redirect' => array(
				'label'            => esc_html__( 'Redirect To The Current Page', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default_on_front' => 'off',
				'toggle_slug'      => 'redirect',
				'description'      => esc_html__( 'Here you can choose whether the user should be redirected back to the current page after logging in.', 'et_builder' ),
			),
			'content'               => array(
				'label'           => et_builder_i18n( 'Body' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		return parent::get_transition_fields_css_props();
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
		$multi_view             = et_pb_multi_view_options( $this );
		$module_id              = $this->props['module_id'];
		$title                  = $multi_view->render_element(
			array(
				'tag'     => et_pb_process_header_level( $this->props['header_level'], 'h2' ),
				'content' => '{{title}}',
				'attrs'   => array(
					'class' => 'et_pb_module_header',
				),
			)
		);
		$background_color       = $this->props['background_color'];
		$use_background_color   = $this->props['use_background_color'];
		$current_page_redirect  = $this->props['current_page_redirect'];
		$button_custom          = $this->props['custom_button'];
		$header_level           = $this->props['header_level'];
		$content                = $this->content;
		$use_focus_border_color = $this->props['use_focus_border_color'];

		$custom_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'button_icon' );
		$custom_icon        = isset( $custom_icon_values['desktop'] ) ? $custom_icon_values['desktop'] : '';
		$custom_icon_tablet = isset( $custom_icon_values['tablet'] ) ? $custom_icon_values['tablet'] : '';
		$custom_icon_phone  = isset( $custom_icon_values['phone'] ) ? $custom_icon_values['phone'] : '';

		$redirect_url = 'on' === $current_page_redirect
			? ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
			: '';

		if ( is_user_logged_in() && ! is_customize_preview() && ! is_et_pb_preview() ) {
			$current_user = wp_get_current_user();

			$content .= sprintf(
				'<br/>%1$s <a href="%2$s">%3$s</a>',
				sprintf( esc_html__( 'Logged in as %1$s', 'et_builder' ), esc_html( $current_user->display_name ) ),
				esc_url( wp_logout_url( $redirect_url ) ),
				esc_html__( 'Log out', 'et_builder' )
			);
		}

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$form = '';

		if ( ! is_user_logged_in() || is_customize_preview() || is_et_pb_preview() ) {
			$username = esc_html__( 'Username', 'et_builder' );
			$password = esc_html__( 'Password', 'et_builder' );

			$form = sprintf(
				'
				<div class="et_pb_newsletter_form et_pb_login_form">
					<form action="%7$s" method="post">
						<p class="et_pb_contact_form_field">
							<label class="et_pb_contact_form_label" for="user_login_%11$s" style="display: none;">%3$s</label>
							<input id="user_login_%11$s" placeholder="%4$s" class="input" type="text" value="" name="log" />
						</p>
						<p class="et_pb_contact_form_field">
							<label class="et_pb_contact_form_label" for="user_pass_%11$s" style="display: none;">%5$s</label>
							<input id="user_pass_%11$s" placeholder="%6$s" class="input" type="password" value="" name="pwd" />
						</p>
						<p class="et_pb_forgot_password"><a href="%2$s">%1$s</a></p>
						<p>
							<button type="submit" name="et_builder_submit_button" class="et_pb_newsletter_button et_pb_button"%10$s%12$s%13$s>%8$s</button>
							%9$s
						</p>
					</form>
				</div>',
				esc_html__( 'Forgot your password?', 'et_builder' ),
				esc_url( wp_lostpassword_url() ),
				esc_html( $username ),
				esc_attr( $username ),
				esc_html( $password ), // #5
				esc_attr( $password ),
				esc_url( site_url( 'wp-login.php', 'login_post' ) ),
				esc_html__( 'Login', 'et_builder' ),
				( 'on' === $current_page_redirect
					? sprintf( '<input type="hidden" name="redirect_to" value="%1$s" />', esc_url( $redirect_url ) )
					: ''
				),
				'' !== $custom_icon && 'on' === $button_custom ? sprintf(
					' data-icon="%1$s"',
					esc_attr( et_pb_process_font_icon( $custom_icon ) )
				) : '', // #10
				// Prevent an accidental "duplicate ID" error if there's more than one instance of this module
				( '' !== $module_id ? esc_attr( $module_id ) : uniqid() ),
				'' !== $custom_icon_tablet && 'on' === $button_custom ? sprintf( ' data-icon-tablet="%1$s"', esc_attr( et_pb_process_font_icon( $custom_icon_tablet ) ) ) : '',
				'' !== $custom_icon_phone && 'on' === $button_custom ? sprintf( ' data-icon-phone="%1$s"', esc_attr( et_pb_process_font_icon( $custom_icon_phone ) ) ) : ''
			);
		}

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		// Module classnames
		$this->add_classname(
			array(
				'et_pb_newsletter',
				'clearfix',
				$this->get_text_orientation_classname(),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		if ( is_customize_preview() || is_et_pb_preview() ) {
			$this->add_classname( 'et_pb_in_customizer' );
		}

		if ( 'on' === $use_focus_border_color ) {
			$this->add_classname( 'et_pb_with_focus_border' );
		}

		if ( 'on' !== $use_background_color ) {
			$this->add_classname( 'et_pb_no_bg' );
		}

		if ( ! $multi_view->has_value( 'title' ) ) {
			$this->add_classname( 'et_pb_newsletter_description_no_title' );
		}

		if ( ! $multi_view->has_value( 'content' ) ) {
			$this->add_classname( 'et_pb_newsletter_description_no_content' );
		}

		$content = $multi_view->render_element(
			array(
				'tag'      => 'div',
				'content'  => '{{content}}',
				'required' => false,
				'attrs'    => array(
					'class' => 'et_pb_newsletter_description_content',
				),
			)
		);

		$content_wrapper = $multi_view->render_element(
			array(
				'tag'     => 'div',
				'content' => "{$title}{$content}",
				'attrs'   => array(
					'class' => 'et_pb_newsletter_description',
				),
				'classes' => array(
					'et_multi_view_hidden' => array(
						'title'   => '__empty',
						'content' => '__empty',
					),
				),
			)
		);

		$wrapper_multi_view_classes = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_newsletter_description_no_title' => array(
						'title' => '__empty',
					),
					'et_pb_newsletter_description_no_content' => array(
						'content' => '__empty',
					),
				),
			)
		);

		$output = sprintf(
			'<div%4$s class="%2$s"%7$s%8$s>
				%6$s
				%5$s
				%9$s
				%10$s
				%3$s
				%1$s
			</div>',
			$form,
			$this->module_classname( $render_slug ),
			et_core_esc_previously( $content_wrapper ),
			$this->module_id(),
			$video_background, // #5
			$parallax_image_background,
			et_core_esc_previously( $data_background_layout ),
			$wrapper_multi_view_classes,
			et_core_esc_previously( $this->background_pattern() ), // #9
			et_core_esc_previously( $this->background_mask() ) // #10
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

		if ( is_user_logged_in() && ! is_customize_preview() && ! is_et_pb_preview() && 'content' === $name ) {
			$current_user = wp_get_current_user();
			$redirect_url = 'on' === $this->props['current_page_redirect']
				? ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
				: '';

			$raw_value .= sprintf(
				'%4$s%1$s <a href="%2$s">%3$s</a>',
				sprintf( esc_html__( 'Logged in as %1$s', 'et_builder' ), esc_html( $current_user->display_name ) ),
				esc_url( wp_logout_url( esc_url( $redirect_url ) ) ),
				esc_html__( 'Log out', 'et_builder' ),
				'' === $raw_value && ! $multi_view->has_value( 'title' ) ? '' : '<br/>'
			);
		}

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
	new ET_Builder_Module_Login();
}
