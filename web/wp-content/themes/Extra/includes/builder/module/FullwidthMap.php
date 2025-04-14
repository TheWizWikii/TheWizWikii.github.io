<?php

class ET_Builder_Module_Fullwidth_Map extends ET_Builder_Module {
	function init() {
		$this->name                   = esc_html__( 'Fullwidth Map', 'et_builder' );
		$this->plural                 = esc_html__( 'Fullwidth Maps', 'et_builder' );
		$this->slug                   = 'et_pb_fullwidth_map';
		$this->vb_support             = 'on';
		$this->fullwidth              = true;
		$this->child_slug             = 'et_pb_map_pin';
		$this->child_item_text        = esc_html__( 'Pin', 'et_builder' );
		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'map' => esc_html__( 'Map', 'et_builder' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'controls'      => esc_html__( 'Controls', 'et_builder' ),
					'child_filters' => array(
						'title'    => esc_html__( 'Map', 'et_builder' ),
						'priority' => 51,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'overlay' => 'inset',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'filters'        => array(
				'css'                  => array(
					'main' => '%%order_class%%',
				),
				'child_filters_target' => array(
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'child_filters',
					'label'       => esc_html__( 'Map', 'et_builder' ),
				),
			),
			'child_filters'  => array(
				'css' => array(
					'main' => '%%order_class%% .gm-style>div>div>div>div>div>img',
				),
			),
			'height'         => array(
				'css'     => array(
					'main' => '%%order_class%% > .et_pb_map',
				),
				'options' => array(
					'height' => array(
						'default'        => '440px',
						'default_tablet' => '350px',
						'default_phone'  => '200px',
					),
				),
			),
			'fonts'          => false,
			'text'           => false,
			'button'         => false,
		);

		$this->help_videos = array(
			array(
				'id'   => 'JtTSSI6wlU0',
				'name' => esc_html__( 'An introduction to the Fullwidth Map module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'google_maps_script_notice' => array(
				'type'        => 'warning',
				'value'       => et_pb_enqueue_google_maps_script(),
				'display_if'  => false,
				'message'     => esc_html__(
					sprintf(
						'The Google Maps API Script is currently disabled in the <a href="%s" target="_blank">Theme Options</a>. This module will not function properly without the Google Maps API.',
						admin_url( 'admin.php?page=et_divi_options' )
					),
					'et_builder'
				),
				'toggle_slug' => 'map',
			),
			'google_api_key'            => array(
				'label'                  => esc_html__( 'Google API Key', 'et_builder' ),
				'type'                   => 'text',
				'option_category'        => 'basic_option',
				'attributes'             => 'readonly',
				'additional_button'      => sprintf(
					' <a href="%2$s" target="_blank" class="et_pb_update_google_key button" data-empty_text="%3$s">%1$s</a>',
					esc_html__( 'Change API Key', 'et_builder' ),
					esc_url( et_pb_get_options_page_link() ),
					esc_attr__( 'Add Your API Key', 'et_builder' )
				),
				'additional_button_type' => 'change_google_api_key',
				'class'                  => array( 'et_pb_google_api_key', 'et-pb-helper-field' ),
				'description'            => et_get_safe_localization( sprintf( __( 'The Maps module uses the Google Maps API and requires a valid Google API Key to function. Before using the map module, please make sure you have added your API key inside the Divi Theme Options panel. Learn more about how to create your Google API Key <a href="%1$s" target="_blank">here</a>.', 'et_builder' ), esc_url( 'https://www.elegantthemes.com/documentation/divi/map/#gmaps-api-key' ) ) ),
				'toggle_slug'            => 'map',
			),
			'address'                   => array(
				'label'             => esc_html__( 'Map Center Address', 'et_builder' ),
				'type'              => 'text',
				'option_category'   => 'basic_option',
				'additional_button' => sprintf(
					' <a href="#" class="et_pb_find_address button">%1$s</a>',
					esc_html__( 'Find', 'et_builder' )
				),
				'class'             => array( 'et_pb_address' ),
				'description'       => esc_html__( 'Enter an address for the map center point, and the address will be geocoded and displayed on the map below.', 'et_builder' ),
				'toggle_slug'       => 'map',
			),
			'zoom_level'                => array(
				'type'    => 'hidden',
				'class'   => array( 'et_pb_zoom_level' ),
				'default' => '18',
			),
			'address_lat'               => array(
				'type'  => 'hidden',
				'class' => array( 'et_pb_address_lat' ),
			),
			'address_lng'               => array(
				'type'  => 'hidden',
				'class' => array( 'et_pb_address_lng' ),
			),
			'map_center_map'            => array(
				'type'                  => 'center_map',
				'use_container_wrapper' => false,
				'option_category'       => 'basic_option',
				'toggle_slug'           => 'map',
			),
			'mouse_wheel'               => array(
				'label'            => esc_html__( 'Mouse Wheel Zoom', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'controls',
				'description'      => esc_html__( 'Here you can choose whether the zoom level will be controlled by mouse wheel or not.', 'et_builder' ),
				'default_on_front' => 'on',
			),
			'mobile_dragging'           => array(
				'label'            => esc_html__( 'Draggable On Mobile', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'controls',
				'description'      => esc_html__( 'Here you can choose whether or not the map will be draggable on mobile devices.', 'et_builder' ),
				'default_on_front' => 'on',
			),
			'use_grayscale_filter'      => array(
				'label'            => esc_html__( 'Use Grayscale Filter', 'et_builder' ),
				'description'      => esc_html__( 'Adjusting the grayscale filter will allow you to change the color saturation of the map.', 'et_builder' ),
				'type'             => 'hidden',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'affects'          => array(
					'grayscale_filter_amount',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'child_filters',
				'default_on_front' => 'off',
			),
			'grayscale_filter_amount'   => array(
				'label'            => esc_html__( 'Grayscale Filter Amount (%)', 'et_builder' ),
				'description'      => esc_html__( 'Adjusting the grayscale filter will allow you to change the color saturation of the map.', 'et_builder' ),
				'type'             => 'hidden',
				'default_on_front' => '0',
				'option_category'  => 'configuration',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'child_filters',
				'depends_show_if'  => 'on',
				'validate_unit'    => false,
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
		$address_lat             = $this->props['address_lat'];
		$address_lng             = $this->props['address_lng'];
		$zoom_level              = $this->props['zoom_level'];
		$mouse_wheel             = $this->props['mouse_wheel'];
		$mobile_dragging         = $this->props['mobile_dragging'];
		$use_grayscale_filter    = $this->props['use_grayscale_filter'];
		$grayscale_filter_amount = $this->props['grayscale_filter_amount'];

		if ( et_pb_enqueue_google_maps_script() ) {
			wp_enqueue_script( 'google-maps-api' );
		}

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$all_pins_content = $this->content;

		$grayscale_filter_data = '';
		if ( 'on' === $use_grayscale_filter && '' !== $grayscale_filter_amount ) {
			$grayscale_filter_data = sprintf( ' data-grayscale="%1$s"', esc_attr( $grayscale_filter_amount ) );
		}

		// Map Tiles: Add CSS Filters and Mix Blend Mode rules (if set)
		if ( array_key_exists( 'child_filters', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['child_filters'] ) ) {
			$this->add_classname(
				$this->generate_css_filters(
					$render_slug,
					'child_',
					self::$data_utils->array_get( $this->advanced_fields['child_filters']['css'], 'main', '%%order_class%%' )
				)
			);
		}

		// Module classnames
		$this->add_classname(
			array(
				'et_pb_map_container',
			)
		);

		// Remove automatically added classname
		$this->remove_classname( $render_slug );

		$output = sprintf(
			'<div%5$s class="%6$s"%11$s>
				%10$s
				%9$s
				%12$s
				%13$s
				<div class="et_pb_map" data-center-lat="%1$s" data-center-lng="%2$s" data-zoom="%3$d" data-mouse-wheel="%7$s" data-mobile-dragging="%8$s"></div>
				%4$s
			</div>',
			esc_attr( et_()->to_css_decimal( $address_lat ) ),
			esc_attr( et_()->to_css_decimal( $address_lng ) ),
			esc_attr( $zoom_level ),
			$all_pins_content,
			$this->module_id(),
			$this->module_classname( $render_slug ),
			esc_attr( $mouse_wheel ),
			esc_attr( $mobile_dragging ),
			$video_background,
			$parallax_image_background,
			$grayscale_filter_data,
			et_core_esc_previously( $this->background_pattern() ), // #12
			et_core_esc_previously( $this->background_mask() ) // #13
		);

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Fullwidth_Map();
}
