<?php

class ET_Builder_Module_Fullwidth_Code extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Fullwidth Code', 'et_builder' );
		$this->plural          = esc_html__( 'Fullwidth Codes', 'et_builder' );
		$this->slug            = 'et_pb_fullwidth_code';
		$this->vb_support      = 'on';
		$this->fullwidth       = true;
		$this->use_raw_content = true;

		$this->settings_modal_toggles = array(
			'general' => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
				),
			),
		);

		$this->advanced_fields = array(
			'text_shadow'     => array(
				// Don't add text-shadow fields since they already are via font-options
				'default' => false,
			),
			'fonts'           => false,
			'button'          => false,
			'position_fields' => array(
				'default' => 'relative',
			),
			'z_index'         => array(
				'default' => '9',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'dTY6-Cbr00A',
				'name' => esc_html__( 'An introduction to the Fullwidth Code module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'raw_content' => array(
				'label'           => esc_html__( 'Code', 'et_builder' ),
				'type'            => 'codemirror',
				'mode'            => 'html',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can create the content that will be used within the module.', 'et_builder' ),
				'is_fb_content'   => true,
				'toggle_slug'     => 'main_content',
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
		$multi_view                = et_pb_multi_view_options( $this );
		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$this->add_classname( $this->get_text_orientation_classname() );

		$raw_content = $multi_view->render_element(
			array(
				'tag'     => 'div',
				'content' => '{{raw_content}}',
				'attrs'   => array(
					'class' => 'et_pb_code_inner',
				),
			)
		);

		$output = sprintf(
			'<div%2$s class="%3$s">
				%5$s
				%4$s
				%6$s
				%7$s
				%1$s
			</div>',
			$raw_content,
			$this->module_id(),
			$this->module_classname( $render_slug ),
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $this->background_pattern() ), // #6
			et_core_esc_previously( $this->background_mask() ) // #7
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
	 * @param mixed $raw_value Props raw value.
	 * @param array $args {
	 *     Context data.
	 *
	 *     @type string $context      Context param: content, attrs, visibility, classes.
	 *     @type string $name         Module options props name.
	 *     @type string $mode         Current data mode: desktop, hover, tablet, phone.
	 *     @type string $attr_key     Attribute key for attrs context data. Example: src, class, etc.
	 *     @type string $attr_sub_key Attribute sub key that availabe when passing attrs value as array such as styes. Example: padding-top, margin-botton, etc.
	 * }
	 *
	 * @return mixed
	 */
	public function multi_view_filter_value( $raw_value, $args ) {
		$name = isset( $args['name'] ) ? $args['name'] : '';
		$mode = isset( $args['mode'] ) ? $args['mode'] : 'desktop';

		if ( $raw_value && 'raw_content' === $name ) {
			if ( 'desktop' !== $mode ) {
				$raw_value = et_builder_convert_line_breaks( et_builder_replace_code_content_entities( $raw_value ) );
			}

			$raw_value = $this->fix_wptexturized_scripts( $raw_value );
		}

		return $raw_value;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Fullwidth_Code();
}
