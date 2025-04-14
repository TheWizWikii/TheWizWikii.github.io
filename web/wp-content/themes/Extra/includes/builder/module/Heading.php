<?php
/**
 * Heading Module Definition.
 *
 * @since 4.22.0
 *
 * @package Divi Builder
 */

/**
 * Class ET_Builder_Module_Heading
 */
class ET_Builder_Module_Heading extends ET_Builder_Module {
	/**
	 * Init the module.
	 *
	 * @inherit
	 *
	 * @return void
	 */
	public function init() {
		$this->name             = esc_html__( 'Heading', 'et_builder' );
		$this->plural           = esc_html__( 'Headings', 'et_builder' );
		$this->slug             = 'et_pb_heading';
		$this->vb_support       = 'on';

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'et_builder' ),
						'priority' => 95,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'           => array(
				'title' => array(
					'label'          => et_builder_i18n( 'Heading' ),
					'css'            => array(
						'main' => [
							'%%order_class%% .et_pb_heading_container h1',
							'%%order_class%% .et_pb_heading_container h2',
							'%%order_class%% .et_pb_heading_container h3',
							'%%order_class%% .et_pb_heading_container h4',
							'%%order_class%% .et_pb_heading_container h5',
							'%%order_class%% .et_pb_heading_container h6',
						],
					),
					'font_size'      => array(
						'default' => '30px',
					),
					'line_height'    => array(
						'default' => '1em',
					),
					'letter_spacing' => array(
						'default' => '0px',
					),
					'header_level'   => array(
						'default' => 'h1',
						'label'   => esc_html__( 'Heading Level', 'et_builder' ),
					),
				),
			),
			'background'      => array(
				'options' => array(
					'parallax_method' => array(
						'default' => 'off',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'max_width'       => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'text'            => array(
				'use_text_orientation'  => false,
				'use_background_layout' => false,
				'css'                   => array(
					'main' => [
						'%%order_class%% h1',
						'%%order_class%% h2',
						'%%order_class%% h3',
						'%%order_class%% h4',
						'%%order_class%% h5',
						'%%order_class%% h6',
					],
				),
			),
			'box_shadow'      => array(
				'default' => array(),
			),
			'position_fields' => array(
				'default' => 'relative',
			),
			'link_options'    => false,
			'filters'         => false,
		);

		$this->custom_css_fields = array(
			'main_element'      => [
				'label'    => et_builder_i18n( 'Main Element' ),
				'selector' => implode(
					',',
					[
						'%%order_class%% h1',
						'%%order_class%% h2',
						'%%order_class%% h3',
						'%%order_class%% h4',
						'%%order_class%% h5',
						'%%order_class%% h6',
					]
				),
			],
			'heading_container' => array(
				'label'    => esc_html__( 'Heading Container', 'et_builder' ),
				'selector' => '.et_pb_heading_container',
			),
		);
	}

	/**
	 * Fields definition.
	 *
	 * @return array
	 */
	public function get_fields() {
		$fields = array(
			'title' => array(
				'label'           => et_builder_i18n( 'Heading' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Enter your page title here.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
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
		$multi_view = et_pb_multi_view_options( $this );
		// Allowing full html for backwards compatibility.
		$title            = $this->_esc_attr( 'title', 'full' );
		$header_level     = $this->props['title_level'];
		$video_background = $this->video_background();

		$title = $multi_view->render_element(
			array(
				'tag'     => et_pb_process_header_level( $header_level, 'h1' ),
				'content' => '{{title}}',
				'attrs'   => array(
					'class' => 'et_pb_module_heading',
				),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$content = $multi_view->render_element(
			array(
				'tag'     => 'div',
				'content' => $title,
				'attrs'   => array(
					'class' => 'et_pb_heading_container',
				),
			)
		);

		$output = sprintf(
			'<div%3$s class="%2$s"%6$s>
				%5$s
				%4$s
				%7$s
				%8$s
				%1$s
			</div>',
			/* 01 */ $content,
			/* 02 */ $this->module_classname( $render_slug ),
			/* 03 */ $this->module_id(),
			/* 04 */ $video_background,
			/* 05 */ $parallax_image_background,
			/* 06 */ et_core_esc_previously( $data_background_layout ),
			/* 07 */ et_core_esc_previously( $this->background_pattern() ),
			/* 08 */ et_core_esc_previously( $this->background_mask() )
		);

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Heading();
}
