<?php

class ET_Builder_Module_Sidebar extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Sidebar', 'et_builder' );
		$this->plural     = esc_html__( 'Sidebars', 'et_builder' );
		$this->slug       = 'et_pb_sidebar';
		$this->vb_support = 'on';

		$this->main_css_element = '%%order_class%%.et_pb_widget_area';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Content' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => et_builder_i18n( 'Layout' ),
					'text'   => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
				),
			),
		);

		$this->advanced_fields   = array(
			'fonts'          => array(
				'header' => array(
					'label' => et_builder_i18n( 'Title' ),
					'css'   => array(
						'main' => "{$this->main_css_element} h3:first-of-type, {$this->main_css_element} h4:first-of-type, {$this->main_css_element} h5:first-of-type, {$this->main_css_element} h6:first-of-type, {$this->main_css_element} h2:first-of-type, {$this->main_css_element} h1:first-of-type, {$this->main_css_element} .widget-title, {$this->main_css_element} .widgettitle",
					),
				),
				'body'   => array(
					'label' => et_builder_i18n( 'Body' ),
					'css'   => array(
						'main'        => "{$this->main_css_element}, {$this->main_css_element} li, {$this->main_css_element} li:before, {$this->main_css_element} a",
						'line_height' => "{$this->main_css_element} p",
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'main'      => '%%order_class%%',
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
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
					'main' => '%%order_class%%, %%order_class%% .widgettitle',
				),
			),
			'button'         => false,
		);
		$this->custom_css_fields = array(
			'widget' => array(
				'label'    => esc_html__( 'Widget', 'et_builder' ),
				'selector' => '.et_pb_widget',
			),
			'title'  => array(
				'label'    => et_builder_i18n( 'Title' ),
				'selector' => 'h4.widgettitle',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => '468VROeyKq4',
				'name' => esc_html__( 'An introduction to the Sidebar module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'orientation' => array(
				'label'            => esc_html__( 'Alignment', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'left'  => et_builder_i18n( 'Left' ),
					'right' => et_builder_i18n( 'Right' ),
				),
				'default_on_front' => 'left',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
				'description'      => esc_html__( 'Choose which side of the page your sidebar will be on. This setting controls text orientation and border position.', 'et_builder' ),
			),
			'area'        => array(
				'label'            => esc_html__( 'Widget Area', 'et_builder' ),
				'type'             => 'select_sidebar',
				'option_category'  => 'basic_option',
				'description'      => esc_html__( 'Select a widget-area that you would like to display. You can create new widget areas within the Appearances > Widgets tab.', 'et_builder' ),
				'toggle_slug'      => 'main_content',
				'computed_affects' => array(
					'__sidebars',
				),
			),
			'show_border' => array(
				'label'            => esc_html__( 'Show Border Separator', 'et_builder' ),
				'description'      => esc_html__( 'Disabling the border separator will remove the solid line that appears to the left or right of sidebar widgets.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
			),
			'__sidebars'  => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Sidebar', 'get_sidebar' ),
				'computed_depends_on' => array(
					'area',
				),
			),
		);
		return $fields;
	}

	static function get_default_area() {
		global $wp_registered_sidebars;

		if ( ! empty( $wp_registered_sidebars ) ) {
			// Pluck sidebar ids
			$sidebar_ids = wp_list_pluck( $wp_registered_sidebars, 'id' );

			// Return first sidebar id
			return array_shift( $sidebar_ids );
		}

		return '';
	}

	/**
	 * Get sidebar data for sidebar module
	 *
	 * @param string comma separated gallery ID
	 * @param string on|off to determine grid / slider layout
	 * @param array  passed current page params
	 *
	 * @return string JSON encoded array of attachments data
	 */
	static function get_sidebar( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$defaults = array(
			'area' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		// Get any available widget areas so it isn't empty
		if ( '' === $args['area'] ) {
			$args['area'] = self::get_default_area();
		}

		// Outputs sidebar
		$widgets = '';

		ob_start();

		if ( is_active_sidebar( $args['area'] ) ) {
			dynamic_sidebar( $args['area'] );
		}

		$widgets = ob_get_contents();

		ob_end_clean();

		return $widgets;
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
		$orientation = $this->props['orientation'];
		$area        = '' === $this->props['area'] ? self::get_default_area() : $this->props['area'];
		$show_border = $this->props['show_border'];

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$widgets = '';

		ob_start();

		if ( is_active_sidebar( $area ) ) {
			dynamic_sidebar( $area );
		}

		$widgets = ob_get_contents();

		ob_end_clean();

		// Module classnames
		$this->add_classname(
			array(
				'et_pb_widget_area',
				'clearfix',
				"et_pb_widget_area_{$orientation}",
				$this->get_text_orientation_classname(),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		if ( 'on' !== $show_border ) {
			$this->add_classname( 'et_pb_sidebar_no_border' );
		}

		// Remove automatically added classnames
		$this->remove_classname(
			array(
				$render_slug,
			)
		);

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$output = sprintf(
			'<div%3$s class="%2$s"%6$s>
				%5$s
				%4$s
				%7$s
				%8$s
				%1$s
			</div>',
			$widgets,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			$video_background,
			$parallax_image_background, // #5
			et_core_esc_previously( $data_background_layout ),
			et_core_esc_previously( $this->background_pattern() ), // #7
			et_core_esc_previously( $this->background_mask() ) // #8
		);

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Sidebar();
}
