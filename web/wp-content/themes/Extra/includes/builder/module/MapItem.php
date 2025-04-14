<?php

class ET_Builder_Module_Map_Item extends ET_Builder_Module {
	function init() {
		$this->name            = esc_html__( 'Pin', 'et_builder' );
		$this->plural          = esc_html__( 'Pins', 'et_builder' );
		$this->slug            = 'et_pb_map_pin';
		$this->vb_support      = 'on';
		$this->type            = 'child';
		$this->child_title_var = 'title';
		$this->custom_css_tab  = false;

		$this->advanced_setting_title_text = esc_html__( 'New Pin', 'et_builder' );
		$this->settings_text               = esc_html__( 'Pin Settings', 'et_builder' );

		$this->settings_modal_toggles = array(
			'general' => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
					'map'          => esc_html__( 'Map', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = false;
	}

	function get_fields() {
		$fields = array(
			'title'           => array(
				'label'           => et_builder_i18n( 'Title' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'The title will be used within the tab button for this tab.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'pin_address'     => array(
				'label'             => esc_html__( 'Map Pin Address', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'class'             => array( 'et_pb_pin_address' ),
				'description'       => esc_html__( 'Enter an address for this map pin, and the address will be geocoded and displayed on the map below.', 'et_builder' ),
				'additional_button' => sprintf(
					'<a href="#" class="et_pb_find_address button">%1$s</a>',
					esc_html__( 'Find', 'et_builder' )
				),
				'toggle_slug'       => 'map',
			),
			'zoom_level'      => array(
				'type'             => 'hidden',
				'class'            => array( 'et_pb_zoom_level' ),
				'default'          => '18',
				'default_on_front' => '',
				'option_category'  => 'basic_option',
			),
			'pin_address_lat' => array(
				'type'            => 'hidden',
				'class'           => array( 'et_pb_pin_address_lat' ),
				'option_category' => 'basic_option',
			),
			'pin_address_lng' => array(
				'type'            => 'hidden',
				'class'           => array( 'et_pb_pin_address_lng' ),
				'option_category' => 'basic_option',
			),
			'map_center_map'  => array(
				'type'                  => 'center_map',
				'option_category'       => 'basic_option',
				'use_container_wrapper' => false,
				'toggle_slug'           => 'map',
			),
			'content'         => array(
				'label'           => et_builder_i18n( 'Body' ),
				'type'            => 'tiny_mce',
				'option_category' => 'basic_option',
				'description'     => esc_html__( 'Here you can define the content that will be placed within the infobox for the pin.', 'et_builder' ),
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
		global $et_pb_tab_titles;

		$multi_view      = et_pb_multi_view_options( $this );
		$title           = $multi_view->render_element(
			array(
				'tag'     => 'h3',
				'content' => '{{title}}',
				'styles'  => array(
					'margin-top' => '10px',
				),
			)
		);
		$pin_address_lat = $this->props['pin_address_lat'];
		$pin_address_lng = $this->props['pin_address_lng'];

		$replace_htmlentities = array(
			'&#8221;' => '',
			'&#8243;' => '',
		);

		if ( ! empty( $pin_address_lat ) ) {
			$pin_address_lat = strtr( $pin_address_lat, $replace_htmlentities );
		}
		if ( ! empty( $pin_address_lng ) ) {
			$pin_address_lng = strtr( $pin_address_lng, $replace_htmlentities );
		}

		$content = $multi_view->render_element(
			array(
				'tag'           => 'div',
				'content'       => '{{content}}',
				'attrs'         => array(
					'class' => 'infowindow',
				),
				'required_some' => array( 'title', 'content' ),
			)
		);

		$title_multi_view_data_attr = $multi_view->render_attrs(
			array(
				'attrs' => array(
					'data-title' => '{{title}}',
				),
			)
		);

		$output = sprintf(
			'<div class="et_pb_map_pin" data-lat="%1$s" data-lng="%2$s" data-title="%5$s"%6$s>
				%3$s
				%4$s
			</div>',
			esc_attr( et_()->to_css_decimal( $pin_address_lat ) ),
			esc_attr( et_()->to_css_decimal( $pin_address_lng ) ),
			et_core_esc_previously( $title ),
			et_core_esc_previously( $content ),
			esc_attr( $multi_view->get_value( 'title' ) ),
			$title_multi_view_data_attr
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
	new ET_Builder_Module_Map_Item();
}
