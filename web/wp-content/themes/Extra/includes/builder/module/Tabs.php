<?php

class ET_Builder_Module_Tabs extends ET_Builder_Module {
	function init() {
		$this->name             = esc_html__( 'Tabs', 'et_builder' );
		$this->plural           = esc_html__( 'Tabs', 'et_builder' );
		$this->slug             = 'et_pb_tabs';
		$this->vb_support       = 'on';
		$this->child_slug       = 'et_pb_tab';
		$this->child_item_text  = esc_html__( 'Tab', 'et_builder' );
		$this->main_css_element = '%%order_class%%.et_pb_tabs';

		$this->advanced_fields = array(
			'borders'        => array(
				'default' => array(
					'css'      => array(
						'main' => array(
							'border_radii'  => $this->main_css_element,
							'border_styles' => $this->main_css_element,
						),
					),
					'defaults' => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '1px',
							'color' => '#d9d9d9',
							'style' => 'solid',
						),
					),
				),
			),
			'fonts'          => array(
				'body' => array(
					'label'          => et_builder_i18n( 'Body' ),
					'css'            => array(
						'main'         => "{$this->main_css_element} .et_pb_all_tabs .et_pb_tab",
						'limited_main' => "{$this->main_css_element} .et_pb_all_tabs .et_pb_tab, {$this->main_css_element} .et_pb_all_tabs .et_pb_tab p",
						'line_height'  => "{$this->main_css_element} .et_pb_tab p",
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
					),
				),
				'tab'  => array(
					'label'            => esc_html__( 'Tab', 'et_builder' ),
					'css'              => array(
						'main'        => "{$this->main_css_element} .et_pb_tabs_controls li, {$this->main_css_element} .et_pb_tabs_controls li a",
						'color'       => "{$this->main_css_element} .et_pb_tabs_controls li a",
						'hover'       => "{$this->main_css_element} .et_pb_tabs_controls li:hover, {$this->main_css_element} .et_pb_tabs_controls li:hover a",
						'color_hover' => "{$this->main_css_element} .et_pb_tabs_controls li:hover a",
					),
					'hide_text_align'  => true,
					'options_priority' => array(
						'tab_text_color' => 9,
					),
				),
			),
			'background'     => array(
				'css'      => array(
					'main'    => "{$this->main_css_element} .et_pb_all_tabs",
					'mask'    => "{$this->main_css_element} > .et_pb_background_mask",
					'pattern' => "{$this->main_css_element} > .et_pb_background_pattern",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'margin_padding' => array(
				'css' => array(
					'padding'   => '%%order_class%% .et_pb_tab',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'text'           => false,
			'button'         => false,
		);

		$this->custom_css_fields = array(
			'tabs_controls' => array(
				'label'    => esc_html__( 'Tabs Controls', 'et_builder' ),
				'selector' => '.et_pb_tabs_controls',
			),
			'tab'           => array(
				'label'    => esc_html__( 'Tab', 'et_builder' ),
				'selector' => '.et_pb_tabs_controls li',
			),
			'active_tab'    => array(
				'label'    => esc_html__( 'Active Tab', 'et_builder' ),
				'selector' => '.et_pb_tabs_controls li.et_pb_tab_active',
			),
			'tabs_content'  => array(
				'label'    => esc_html__( 'Tabs Content', 'et_builder' ),
				'selector' => '.et_pb_tab',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'xk2Ite-oFhg',
				'name' => esc_html__( 'An introduction to the Tabs module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'active_tab_background_color'   => array(
				'label'          => esc_html__( 'Active Tab Background Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to be used for active tab backgrounds. You can assign a unique color to active tabs to differentiate them from inactive tabs.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'tab',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'inactive_tab_background_color' => array(
				'label'          => esc_html__( 'Inactive Tab Background Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to be used for inactive tab backgrounds. You can assign a unique color to inactive tabs to differentiate them from active tabs.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'tab',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'active_tab_text_color'         => array(
				'label'          => esc_html__( 'Active Tab Text Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for tab text within active tabs. You can assign a unique color to active tabs to differentiate them from inactive tabs.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'tab',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['inactive_tab_background_color'] = array( 'background-color' => '%%order_class%% .et_pb_tabs_controls li' );
		$fields['active_tab_background_color']   = array( 'background-color' => '%%order_class%% .et_pb_tabs_controls li' );
		$fields['active_tab_text_color']         = array( 'color' => '%%order_class%% .et_pb_tabs_controls li a' );

		return $fields;
	}

	/**
	 * Outputs tabs module nav markup
	 * The nav output is abstracted into method so tabs module can be extended
	 *
	 * @since 3.29
	 *
	 * @return string
	 */
	public function get_tabs_nav() {
		global $et_pb_tab_titles;
		global $et_pb_tab_classes;

		$tabs = '';

		$i = 0;
		if ( ! empty( $et_pb_tab_titles ) ) {
			foreach ( $et_pb_tab_titles as $tab_title ) {
				++$i;
				$tabs .= sprintf(
					'<li class="%3$s%1$s">%2$s</li>',
					( 1 === $i ? ' et_pb_tab_active' : '' ),
					et_pb_multi_view_options( $this )->render_element(
						array(
							'tag'          => 'a',
							'content'      => '{{tab_title}}',
							'attrs'        => array(
								'href' => '#',
							),
							'custom_props' => array(
								'tab_title' => $tab_title,
							),
						)
					),
					esc_attr( ltrim( $et_pb_tab_classes[ $i - 1 ] ) )
				);
			}
		}

		return $tabs;
	}

	/**
	 * Outputs tabs content markup
	 * The tabs content is abstracted into method so tabs module can be extended
	 *
	 * @since 3.29
	 *
	 * @return string
	 */
	public function get_tabs_content() {
		return $this->content;
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
		$all_tabs_content = $this->get_tabs_content();

		global $et_pb_tab_titles;
		global $et_pb_tab_classes;

		// Inactive Tab Background Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'inactive_tab_background_color',
				'selector'                        => '%%order_class%% .et_pb_tabs_controls li',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'background-color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Active Tab Background Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'active_tab_background_color',
				'selector'                        => '%%order_class%% .et_pb_tabs_controls li.et_pb_tab_active',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'background-color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Active Text Color
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'active_tab_text_color',
				'selector'                        => '%%order_class%%.et_pb_tabs .et_pb_tabs_controls li.et_pb_tab_active a',
				'hover_selector'                  => '%%order_class%% .et_pb_tabs_controls li.et_pb_tab_active:hover a',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'important'                       => true,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		$tabs = $this->get_tabs_nav();

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$et_pb_tab_titles = $et_pb_tab_classes = array();

		// Module classnames
		$this->add_classname(
			array(
				$this->get_text_orientation_classname(),
			)
		);

		$output = sprintf(
			'<div%3$s class="%4$s" %7$s>
				%6$s
				%5$s
				%8$s
				%9$s
				<ul class="et_pb_tabs_controls clearfix">
					%1$s
				</ul>
				<div class="et_pb_all_tabs">
					%2$s
				</div>
			</div>',
			$tabs,
			$all_tabs_content,
			$this->module_id(),
			$this->module_classname( $render_slug ),
			$video_background,
			$parallax_image_background,
			/* 7$s */ 'et_pb_wc_tabs' === $render_slug ? $this->get_multi_view_attrs() : '',
			et_core_esc_previously( $this->background_pattern() ), // #8
			et_core_esc_previously( $this->background_mask() ) // #9
		);

		return $output;
	}

	public function process_box_shadow( $function_name ) {
		$boxShadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );
		$style     = $boxShadow->get_value( $this->props );

		if ( $boxShadow->is_inset( $style ) ) {
			$this->advanced_fields['box_shadow'] = array(
				'default' => array(
					'css' => array(
						'main' => '%%order_class%% .et-pb-active-slide',
					),
				),
			);

		}

		parent::process_box_shadow( $function_name );
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Tabs();
}
