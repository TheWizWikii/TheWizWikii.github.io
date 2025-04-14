<?php

class ET_Builder_Module_Search extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Search', 'et_builder' );
		$this->plural     = esc_html__( 'Searches', 'et_builder' );
		$this->slug       = 'et_pb_search';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'elements'     => et_builder_i18n( 'Elements' ),
					'exceptions'   => esc_html__( 'Exceptions', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'text'  => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
					'width' => array(
						'title'    => et_builder_i18n( 'Sizing' ),
						'priority' => 65,
					),
				),
			),
		);
		$this->advanced_fields        = array(
			'fonts'          => array(
				'button' => array(
					'label'           => et_builder_i18n( 'Button' ),
					'css'             => array(
						'main'      => "{$this->main_css_element} input.et_pb_searchsubmit",
						'important' => array( 'line-height', 'text-shadow' ),
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
				'css'            => array(
					'padding'   => "{$this->main_css_element} input.et_pb_s",
					'important' => 'all',
				),
				'custom_padding' => array(
					'default' => '0.715em|0.715em|0.715em|0.715em|false|false',
				),
			),
			'background'     => array(
				'css' => array(
					'main' => "{$this->main_css_element} .et_pb_searchform",
				),
			),
			'borders'        => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element}.et_pb_search, {$this->main_css_element} input.et_pb_s",
							'border_styles' => "{$this->main_css_element}.et_pb_search",
						),
					),
					'defaults' => array(
						'border_radii'  => 'on|3px|3px|3px|3px',
						'border_styles' => array(
							'width' => '1px',
							'color' => '#dddddd',
							'style' => 'solid',
						),
					),
				),
			),
			'text'           => array(
				'use_background_layout' => true,
				'css'                   => array(
					'main'        => "{$this->main_css_element} input.et_pb_searchsubmit, {$this->main_css_element} input.et_pb_s",
					'text_shadow' => "{$this->main_css_element} input.et_pb_searchsubmit, {$this->main_css_element} input.et_pb_s",
				),
				'text_orientation'      => array(
					'exclude_options' => array( 'justified' ),
				),
				'options'               => array(
					'text_orientation'  => array(
						'default' => 'left',
					),
					'background_layout' => array(
						'default' => 'light',
						'hover'   => 'tabs',
					),
				),
			),
			'button'         => false,
			'link_options'   => false,
			'form_field'     => array(
				'form_field' => array(
					'label'          => esc_html__( 'Field', 'et_builder' ),
					'css'            => array(
						'main'        => '%%order_class%% form input.et_pb_s',
						'hover'       => '%%order_class%% form input.et_pb_s:hover',
						'focus'       => '%%order_class%% form input.et_pb_s:focus',
						'focus_hover' => '%%order_class%% form input.et_pb_s:focus:hover',
					),
					'placeholder'    => false,
					'margin_padding' => false,
					'box_shadow'     => false,
					'border_styles'  => false,
					'font_field'     => array(
						'css'            => array(
							'main'        => implode(
								', ',
								array(
									'%%order_class%% form input.et_pb_s',
									'%%order_class%% form input.et_pb_s::placeholder',
									'%%order_class%% form input.et_pb_s::-webkit-input-placeholder',
									'%%order_class%% form input.et_pb_s::-ms-input-placeholder',
									'%%order_class%% form input.et_pb_s::-moz-placeholder',
								)
							),
							'placeholder' => true,
							'important'   => array( 'line-height', 'text-shadow' ),
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
			),
			'overflow'       => array(
				'default' => 'hidden',
			),
		);

		$this->custom_css_fields = array(
			'input_field' => array(
				'label'    => esc_html__( 'Input Field', 'et_builder' ),
				'selector' => 'input.et_pb_s',
			),
			'button'      => array(
				'label'    => et_builder_i18n( 'Button' ),
				'selector' => 'input.et_pb_searchsubmit',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'HNmb20Mdvno',
				'name' => esc_html__( 'An introduction to the Search module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'exclude_pages'      => array(
				'label'           => esc_html__( 'Exclude Pages', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'description'     => esc_html__( 'Turning this on will exclude Pages from search results', 'et_builder' ),
				'toggle_slug'     => 'exceptions',
			),
			'exclude_posts'      => array(
				'label'           => esc_html__( 'Exclude Posts', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default'         => 'off',
				'affects'         => array(
					'include_categories',
				),
				'description'     => esc_html__( 'Turning this on will exclude Posts from search results', 'et_builder' ),
				'toggle_slug'     => 'exceptions',
			),
			'include_categories' => array(
				'label'            => esc_html__( 'Exclude Categories', 'et_builder' ),
				'type'             => 'categories',
				'option_category'  => 'basic_option',
				'renderer_options' => array(
					'use_terms' => false,
				),
				'depends_show_if'  => 'off',
				'description'      => esc_html__( 'Choose which categories you would like to exclude from the search results.', 'et_builder' ),
				'toggle_slug'      => 'exceptions',
			),
			'show_button'        => array(
				'label'           => esc_html__( 'Show Button', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'configuration',
				'options'         => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default'         => 'on',
				'toggle_slug'     => 'elements',
				'description'     => esc_html__( 'Turn this on to show the Search button', 'et_builder' ),
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'placeholder'        => array(
				'label'           => esc_html__( 'Input Placeholder', 'et_builder' ),
				'type'            => 'text',
				'description'     => esc_html__( 'Type the text you want to use as placeholder for the search field.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'button_color'       => array(
				'label'          => esc_html__( 'Button and Border Color', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'button',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'placeholder_color'  => array(
				'label'          => esc_html__( 'Placeholder Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to be used for the placeholder written inside input fields.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'form_field',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['button_color'] = array(
			'background-color' => '%%order_class%% input.et_pb_searchsubmit',
			'border-color'     => array(
				'%%order_class%% input.et_pb_searchsubmit',
				'%%order_class%% input.et_pb_s',
			),
		);

		$fields['placeholder_color'] = array(
			'color' => array(
				'%%order_class%% form input.et_pb_s::placeholder',
				'%%order_class%% form input.et_pb_s::-webkit-input-placeholder',
				'%%order_class%% form input.et_pb_s::-ms-input-placeholder',
				'%%order_class%% form input.et_pb_s::-moz-placeholder',
			),
		);

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
		$multi_view                = et_pb_multi_view_options( $this );
		$exclude_categories        = $this->props['include_categories'];
		$exclude_posts             = $this->props['exclude_posts'];
		$exclude_pages             = $this->props['exclude_pages'];
		$show_button               = $this->props['show_button'];
		$placeholder               = $multi_view->render_element(
			array(
				'tag'   => 'input',
				'attrs' => array(
					'type'        => 'text',
					'name'        => 's',
					'class'       => 'et_pb_s',
					'placeholder' => '{{placeholder}}',
				),
			)
		);
		$input_line_height         = $this->props['form_field_line_height'];
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$this->content = et_builder_replace_code_content_entities( $this->content );

		// Button Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'button_color',
				'selector'                        => '%%order_class%% input.et_pb_searchsubmit',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => array( 'background-color', 'border-color' ),
				'important'                       => true,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		$this->generate_styles(
			array(
				'base_attr_name'                  => 'button_color',
				'selector'                        => '%%order_class%% input.et_pb_s',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'border-color',
				'important'                       => true,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Placeholder Color.
		$placeholder_selectors = array(
			'%%order_class%% form input.et_pb_s::-webkit-input-placeholder',
			'%%order_class%% form input.et_pb_s::-moz-placeholder',
			'%%order_class%% form input.et_pb_s:-ms-input-placeholder',
		);

		$this->generate_styles(
			array(
				'base_attr_name'                  => 'placeholder_color',
				'selector'                        => join( ', ', $placeholder_selectors ),
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'important'                       => true,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		if ( '' !== $input_line_height ) {
			$el_style = array(
				'selector'    => '%%order_class%% input.et_pb_s',
				'declaration' => 'height: auto; min-height: 0;',
			);
			ET_Builder_Element::set_style( $render_slug, $el_style );
		}

		// Module classnames
		$this->add_classname(
			array(
				$this->get_text_orientation_classname( true ),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		if ( 'on' !== $show_button ) {
			$this->add_classname( 'et_pb_hide_search_button' );
		}

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$multi_view_show_button_data_attr = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_hide_search_button' => array(
						'show_button' => 'off',
					),
				),
			)
		);

		$output = sprintf(
			'<div%3$s class="%2$s"%12$s%13$s>
				%11$s
				%10$s
				%14$s
				%15$s
				<form role="search" method="get" class="et_pb_searchform" action="%1$s">
					<div>
						<label class="screen-reader-text" for="s">%8$s</label>
						%7$s
						<input type="hidden" name="et_pb_searchform_submit" value="et_search_proccess" />
						%4$s
						%5$s
						%6$s
						<input type="submit" value="%9$s" class="et_pb_searchsubmit">
					</div>
				</form>
			</div>',
			esc_url( home_url( '/' ) ),
			$this->module_classname( $render_slug ),
			$this->module_id(),
			'' !== $exclude_categories ? sprintf( '<input type="hidden" name="et_pb_search_cat" value="%1$s" />', esc_attr( $exclude_categories ) ) : '',
			'on' !== $exclude_posts ? '<input type="hidden" name="et_pb_include_posts" value="yes" />' : '', // #5
			'on' !== $exclude_pages ? '<input type="hidden" name="et_pb_include_pages" value="yes" />' : '',
			$placeholder,
			esc_html__( 'Search for:', 'et_builder' ),
			esc_attr__( 'Search', 'et_builder' ),
			$video_background, // #10
			$parallax_image_background,
			et_core_esc_previously( $data_background_layout ),
			$multi_view_show_button_data_attr,
			et_core_esc_previously( $this->background_pattern() ), // #14
			et_core_esc_previously( $this->background_mask() ) // #15
		);

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Search();
}
