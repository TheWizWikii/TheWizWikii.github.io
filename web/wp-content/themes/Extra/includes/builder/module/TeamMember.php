<?php

class ET_Builder_Module_Team_Member extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Person', 'et_builder' );
		$this->plural     = esc_html__( 'Persons', 'et_builder' );
		$this->slug       = 'et_pb_team_member';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%.et_pb_team_member';

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'image'        => et_builder_i18n( 'Image' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'icon'  => esc_html__( 'Icon', 'et_builder' ),
					'image' => et_builder_i18n( 'Image' ),
					'text'  => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Image Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'header'   => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'      => "{$this->main_css_element} h4, {$this->main_css_element} h1.et_pb_module_header, {$this->main_css_element} h2.et_pb_module_header, {$this->main_css_element} h3.et_pb_module_header, {$this->main_css_element} h5.et_pb_module_header, {$this->main_css_element} h6.et_pb_module_header",
						'important' => 'plugin_only',
					),
					'header_level' => array(
						'default' => 'h4',
					),
				),
				'body'     => array(
					'label'          => et_builder_i18n( 'Body' ),
					'css'            => array(
						'main' => "{$this->main_css_element}",
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
					),
				),
				'position' => array(
					'label'          => et_builder_i18n( 'Position' ),
					'css'            => array(
						'main' => "{$this->main_css_element} .et_pb_member_position",
					),
					'line_height'    => array(
						'default' => '1.7em',
					),
					'font_size'      => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
				),
			),
			'background'     => array(
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'borders'        => array(
				'default' => array(),
				'image'   => array(
					'css'          => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_pb_team_member_image",
							'border_styles' => "{$this->main_css_element} .et_pb_team_member_image",
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
						'main'         => '%%order_class%% .et_pb_team_member_image',
						'custom_style' => true,
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'max_width'      => array(
				'css' => array(
					'module_alignment' => '%%order_class%%.et_pb_team_member.et_pb_module',
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
					'main' => implode(
						', ',
						array(
							'%%order_class%% .et_pb_module_header',
							'%%order_class%% .et_pb_member_position',
							'%%order_class%% .et_pb_team_member_description p',
						)
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
					'main' => '%%order_class%% .et_pb_team_member_image',
				),
			),
			'button'         => false,
		);

		$this->custom_css_fields = array(
			'member_image'        => array(
				'label'    => esc_html__( 'Member Image', 'et_builder' ),
				'selector' => '.et_pb_team_member_image',
			),
			'member_description'  => array(
				'label'    => esc_html__( 'Member Description', 'et_builder' ),
				'selector' => '.et_pb_team_member_description',
			),
			'title'               => array(
				'label'    => et_builder_i18n( 'Title' ),
				'selector' => '.et_pb_team_member_description h4',
			),
			'member_position'     => array(
				'label'    => esc_html__( 'Member Position', 'et_builder' ),
				'selector' => '.et_pb_member_position',
			),
			'member_social_links' => array(
				'label'    => esc_html__( 'Member Social Links', 'et_builder' ),
				'selector' => '.et_pb_member_social_links',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'rrKmaQ0n7Hw',
				'name' => esc_html__( 'An introduction to the Person module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'name'               => array(
				'label'           => esc_html__( 'Name', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the name of the person', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'position'           => array(
				'label'           => et_builder_i18n( 'Position' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( "Input the person's position.", 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'image_url'          => array(
				'label'              => et_builder_i18n( 'Image' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'image',
				'dynamic_content'    => 'image',
				'mobile_options'     => true,
				'hover'              => 'tabs',
			),
			'facebook_url'       => array(
				'label'           => esc_html__( 'Facebook Profile Url', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input Facebook Profile Url.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'url',
			),
			'twitter_url'        => array(
				'label'           => esc_html__( 'X Profile Url', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input X Profile Url', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'url',
			),
			'linkedin_url'       => array(
				'label'           => esc_html__( 'LinkedIn Profile Url', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input LinkedIn Profile Url', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'url',
			),
			'content'            => array(
				'label'           => et_builder_i18n( 'Body' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Input the main text content for your module here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'icon_color'         => array(
				'label'          => esc_html__( 'Icon Color', 'et_builder' ),
				'description'    => esc_html__( 'Here you can define a custom color for the icon.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'use_icon_font_size' => array(
				'label'            => esc_html__( 'Use Icon Font Size', 'et_builder' ),
				'description'      => esc_html__( 'If you would like to control the size of the icon, you must first enable this option.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default_on_front' => 'off',
				'affects'          => array(
					'icon_font_size',
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
				'option_category'  => 'font_option',
			),
			'icon_font_size'     => array(
				'label'            => esc_html__( 'Icon Font Size', 'et_builder' ),
				'description'      => esc_html__( 'Control the size of the icon by increasing or decreasing the font size.', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'font_option',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'icon',
				'allowed_units'    => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'default'          => '16px',
				'default_unit'     => 'px',
				'default_on_front' => '',
				'range_settings'   => array(
					'min'  => '1',
					'max'  => '120',
					'step' => '1',
				),
				'mobile_options'   => true,
				'depends_show_if'  => 'on',
				'responsive'       => true,
				'sticky'           => true,
				'hover'            => 'tabs',
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['icon_color']     = array( 'color' => '%%order_class%% .et_pb_member_social_links a' );
		$fields['icon_font_size'] = array( 'font-size' => '%%order_class%% .et_pb_member_social_links a' );

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
		$multi_view         = et_pb_multi_view_options( $this );
		$name               = $multi_view->render_element(
			array(
				'tag'     => et_pb_process_header_level( $this->props['header_level'], 'h4' ),
				'content' => '{{name}}',
				'attrs'   => array(
					'class' => 'et_pb_module_header',
				),
			)
		);
		$position           = $multi_view->render_element(
			array(
				'tag'     => 'p',
				'content' => '{{position}}',
				'attrs'   => array(
					'class' => 'et_pb_member_position',
				),
			)
		);
		$image_url          = $this->props['image_url'];
		$animation          = $this->props['animation'];
		$facebook_url       = $this->props['facebook_url'];
		$twitter_url        = $this->props['twitter_url'];
		$linkedin_url       = $this->props['linkedin_url'];
		$use_icon_font_size = $this->props['use_icon_font_size'];

		$image = $social_links = '';

		// Icon Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'icon_color',
				'selector'                        => '%%order_class%% .et_pb_member_social_links a',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'important'                       => true,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		if ( '' !== $facebook_url ) {
			$social_links .= sprintf(
				'<li><a href="%1$s" class="et_pb_font_icon et_pb_facebook_icon"><span>%2$s</span></a></li>',
				esc_url( $facebook_url ),
				esc_html__( 'Facebook', 'et_builder' )
			);
		}

		if ( '' !== $twitter_url ) {
			$social_links .= sprintf(
				'<li><a href="%1$s" class="et_pb_font_icon et_pb_twitter_icon"><span>%2$s</span></a></li>',
				esc_url( $twitter_url ),
				esc_html__( 'X', 'et_builder' )
			);
		}

		if ( '' !== $linkedin_url ) {
			$social_links .= sprintf(
				'<li><a href="%1$s" class="et_pb_font_icon et_pb_linkedin_icon"><span>%2$s</span></a></li>',
				esc_url( $linkedin_url ),
				esc_html__( 'LinkedIn', 'et_builder' )
			);
		}

		if ( '' !== $social_links ) {
			$social_links = sprintf( '<ul class="et_pb_member_social_links">%1$s</ul>', $social_links );
		}

		// Icon Size.
		if ( 'off' !== $use_icon_font_size ) {
			$this->generate_styles(
				array(
					'base_attr_name'                  => 'icon_font_size',
					'selector'                        => '%%order_class%% .et_pb_member_social_links .et_pb_font_icon',
					'hover_pseudo_selector_location'  => 'suffix',
					'sticky_pseudo_selector_location' => 'prefix',
					'css_property'                    => 'font-size',
					'render_slug'                     => $render_slug,
					'type'                            => 'range',
				)
			);
		}

		// Added for backward compatibility
		if ( empty( $animation ) ) {
			$animation = 'top';
		}

		if ( $multi_view->has_value( 'image_url' ) ) {
			$team_member_image_classes = array(
				'et_pb_team_member_image',
				'et-waypoint',
				'et_pb_animation_' . $animation,
			);

			// Images: Add CSS Filters and Mix Blend Mode rules (if set)
			if ( array_key_exists( 'image', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['image'] ) ) {
				$generate_css_filters = $this->generate_css_filters(
					$render_slug,
					'child_',
					self::$data_utils->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' )
				);

				if ( $generate_css_filters ) {
					$team_member_image_classes[] = $generate_css_filters;
				}
			}

			$image_attrs = array(
				'src' => '{{image_url}}',
				'alt' => '{{name}}',
			);

			$image_attachment_class = et_pb_media_options()->get_image_attachment_class( $this->props, 'image_url' );

			if ( ! empty( $image_attachment_class ) ) {
				$image_attrs['class'] = esc_attr( $image_attachment_class );
			}

			$image = $multi_view->render_element(
				array(
					'tag'     => 'div',
					'content' => $multi_view->render_element(
						array(
							'tag'   => 'img',
							'attrs' => $image_attrs,
						)
					),
					'attrs'   => array(
						'class' => implode( ' ', $team_member_image_classes ),
					),
					'classes' => array(
						'et-svg' => array(
							'image_url' => true,
						),
					),
				)
			);
		}

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Module classnames
		$this->add_classname(
			array(
				'clearfix',
				$this->get_text_orientation_classname(),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		if ( '' === $image ) {
			$this->add_classname( 'et_pb_team_member_no_image' );
		}

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$content = $multi_view->render_element(
			array(
				'tag'     => 'div',
				'content' => '{{content}}',
			)
		);

		$muti_view_data_attr = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_team_member_no_image' => array(
						'image' => '__empty',
					),
				),
			)
		);

		$output = sprintf(
			'<div%3$s class="%4$s"%10$s%11$s>
				%9$s
				%8$s
				%12$s
				%13$s
				%2$s
				<div class="et_pb_team_member_description">
					%5$s
					%6$s
					%1$s
					%7$s
				</div>
			</div>',
			$content,
			et_core_esc_previously( $image ),
			$this->module_id(),
			$this->module_classname( $render_slug ),
			et_core_esc_previously( $name ), // #5
			et_core_esc_previously( $position ),
			$social_links,
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $data_background_layout ), // #10
			et_core_esc_previously( $muti_view_data_attr ),
			et_core_esc_previously( $this->background_pattern() ), // #12
			et_core_esc_previously( $this->background_mask() ) // #13
		);

		return $output;
	}

	/**
	 * Check if image has svg extension
	 *
	 * @param string $image_url Image URL.
	 *
	 * @return bool
	 */
	public function is_svg( $image_url ) {
		if ( ! $image_url ) {
			return false;
		}

		$image_pathinfo = pathinfo( $image_url );

		return isset( $image_pathinfo['extension'] ) ? 'svg' === $image_pathinfo['extension'] : false;
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
		$name    = et_()->array_get( $args, 'name', '' );
		$mode    = et_()->array_get( $args, 'mode', '' );
		$context = et_()->array_get( $args, 'context', '' );

		$fields_need_escape = array(
			'name',
			'position',
		);

		if ( $raw_value && in_array( $name, $fields_need_escape, true ) ) {
			return $this->_esc_attr( $multi_view->get_name_by_mode( $name, $mode ), 'none', $raw_value );
		}

		if ( 'image_url' === $name && 'classes' === $context ) {
			$raw_value = $raw_value ? $this->is_svg( $raw_value ) : false;
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Team_Member();
}
