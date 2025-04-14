<?php
/**
 * Fires before the builder's structure element classes are loaded.
 *
 * @package Divi
 * @subpackage Builder
 * @since 4.4.9
 */

do_action( 'et_builder_structure_elements_load' );

/**
 * Section Element class
 *
 * @since 3.0.16
 */
class ET_Builder_Section extends ET_Builder_Structure_Element {
	/**
	 * Sets up the element's properties.
	 */
	public function init() {
		$this->name       = esc_html__( 'Section', 'et_builder' );
		$this->plural     = esc_html__( 'Sections', 'et_builder' );
		$this->slug       = 'et_pb_section';
		$this->vb_support = 'on';

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'background' => array(
						'title'       => et_builder_i18n( 'Background' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => array( 'name' => esc_html__( 'Column 1', 'et_builder' ) ),
							'column_2' => array( 'name' => esc_html__( 'Column 2', 'et_builder' ) ),
							'column_3' => array( 'name' => esc_html__( 'Column 3', 'et_builder' ) ),
						),
						'priority'    => 80,
					),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'layout'         => et_builder_i18n( 'Layout' ),
					'width'          => array(
						'title'    => et_builder_i18n( 'Sizing' ),
						'priority' => 65,
					),
					'margin_padding' => array(
						'title'       => esc_html__( 'Spacing', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => array( 'name' => esc_html__( 'Column 1', 'et_builder' ) ),
							'column_2' => array( 'name' => esc_html__( 'Column 2', 'et_builder' ) ),
							'column_3' => array( 'name' => esc_html__( 'Column 3', 'et_builder' ) ),
						),
						'priority'    => 70,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'classes'    => array(
						'title'       => esc_html__( 'CSS ID & Classes', 'et_builder' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => array( 'name' => esc_html__( 'Column 1', 'et_builder' ) ),
							'column_2' => array( 'name' => esc_html__( 'Column 2', 'et_builder' ) ),
							'column_3' => array( 'name' => esc_html__( 'Column 3', 'et_builder' ) ),
						),
					),
					'custom_css' => array(
						'title'       => et_builder_i18n( 'Custom CSS' ),
						'sub_toggles' => array(
							'main'     => '',
							'column_1' => array( 'name' => esc_html__( 'Column 1', 'et_builder' ) ),
							'column_2' => array( 'name' => esc_html__( 'Column 2', 'et_builder' ) ),
							'column_3' => array( 'name' => esc_html__( 'Column 3', 'et_builder' ) ),
						),
					),
				),
			),
		);

		$this->advanced_fields = array(
			'background'      => array(
				'use_background_color'          => 'fields_only',
				'use_background_image'          => true,
				'use_background_color_gradient' => true,
				'use_background_video'          => true,
				'use_background_color_reset'    => 'fields_only',
				'use_background_pattern'        => true,
				'use_background_mask'           => true,
				'css'                           => array(
					'important' => 'all',
					'main'      => 'div.et_pb_section%%order_class%%',
				),
				'options'                       => array(
					'background_color'   => array(
						'default' => '',
						'hover'   => 'tabs',
					),
					'allow_player_pause' => array(
						'default_on_front' => 'off',
					),
					'background_video_pause_outside_viewport' => array(
						'default_on_front' => 'on',
					),
					'parallax'           => array(
						'default_on_front' => 'off',
					),
					'parallax_method'    => array(
						'default_on_front' => 'on',
					),
				),
			),
			'max_width'       => array(
				'css'     => array(
					'module_alignment' => '%%order_class%%',
				),
				'options' => array(
					'module_alignment' => array(
						'label' => esc_html__( 'Section Alignment', 'et_builder' ),
					),
				),
				'extra'   => array(
					'inner' => array(
						'css'     => array(
							'main' => array( '%%order_class%% > .et_pb_row', '%%order_class%%.et_section_specialty > .et_pb_row' ),
						),
						'options' => array(
							'width'            => array(
								'label'           => esc_html__( 'Inner Width', 'et_builder' ),
								'depends_show_if' => 'on',
								'specialty_only'  => 'yes',
							),
							'max_width'        => array(
								'label'           => esc_html__( 'Inner Max Width', 'et_builder' ),
								'depends_show_if' => 'on',
								'specialty_only'  => 'yes',
							),
							'module_alignment' => array(
								'label'           => esc_html__( 'Row Alignment', 'et_builder' ),
								'depends_show_if' => 'on',
								'specialty_only'  => 'yes',
							),
						),
					),
				),
			),
			'fonts'           => false,
			'text'            => false,
			'button'          => false,
			'z_index'         => array(
				'default' => 'auto',
			),
			'position_fields' => array(
				'default' => 'relative',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => '3kmJ_mMVB1w',
				'name' => esc_html__( 'An introduction to Sections', 'et_builder' ),
			),
		);
	}

	/**
	 * Get the settings fields data for this element.
	 *
	 * @return array|array[]
	 */
	public function get_fields() {
		$fields = array(
			'inner_shadow'               => array(
				'label'            => esc_html__( 'Show Inner Shadow', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default'          => 'off',
				'description'      => esc_html__( 'Here you can select whether or not your section has an inner shadow. This can look great when you have colored backgrounds or background images.', 'et_builder' ),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
				'default_on_front' => 'off',
			),
			'make_equal'                 => array(
				'label'           => esc_html__( 'Equalize Column Heights', 'et_builder' ),
				'description'     => esc_html__( 'Equalizing column heights will force all columns to assume the height of the tallest column in the row. All columns will have the same height, keeping their appearance uniform.', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default'         => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'specialty_only'  => 'yes',
			),
			'use_custom_gutter'          => array(
				'label'           => esc_html__( 'Use Custom Gutter Width', 'et_builder' ),
				'description'     => esc_html__( 'Enable this option to define custom gutter width for this section.', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default'         => 'off',
				'affects'         => array(
					'gutter_width',
				),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
				'specialty_only'  => 'yes',
			),
			'gutter_width'               => array(
				'label'            => esc_html__( 'Gutter Width', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'range_settings'   => array(
					'min'       => 1,
					'max'       => 4,
					'step'      => 1,
					'min_limit' => 1,
					'max_limit' => 4,
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'width',
				'specialty_only'   => 'yes',
				'validate_unit'    => false,
				'fixed_range'      => true,
				'default_on_front' => (string) et_get_option( 'gutter_width', '3' ),
				'hover'            => 'tabs',
				'description'      => esc_html__( 'Gutter width controls the space between each column in a row. Lowering the gutter width will cause modules to become closer together.', 'et_builder' ),
			),
			'columns_background'         => array(
				'type'            => 'column_settings_background',
				'option_category' => 'configuration',
				'toggle_slug'     => 'background',
				'specialty_only'  => 'yes',
				'priority'        => 99,
			),
			'columns_padding'            => array(
				'type'            => 'column_settings_padding',
				'option_category' => 'configuration',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'margin_padding',
				'specialty_only'  => 'yes',
				'priority'        => 99,
			),
			'fullwidth'                  => array(
				'type'             => 'hidden',
				'default_on_front' => 'off',
			),
			'specialty'                  => array(
				'type'             => 'skip',
				'default_on_front' => 'off',
				'affects'          => array( 'inner_width', 'inner_max_width' ),
			),
			'columns_css'                => array(
				'type'            => 'column_settings_css',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'custom_css',
				'priority'        => 20,
			),
			'columns_css_fields'         => array(
				'type'            => 'column_settings_css_fields',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'priority'        => 20,
			),
			'custom_padding_last_edited' => array(
				'type'           => 'skip',
				'tab_slug'       => 'advanced',
				'specialty_only' => 'yes',
			),
			'__video_background'         => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Helper_ResponsiveOptions', 'get_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4',
					'background_video_webm',
					'background_video_width',
					'background_video_height',
				),
				'computed_minimum'    => array(
					'background_video_mp4',
					'background_video_webm',
				),
			),
			'prev_background_color'      => array(
				'type' => 'skip',
			),
			'next_background_color'      => array(
				'type' => 'skip',
			),
		);

		$column_fields = $this->get_column_fields(
			3,
			array(
				'parallax'                                 => array(
					'default_on_front' => 'off',
				),
				'parallax_method'                          => array(
					'default_on_front' => 'on',
				),
				'background_color'                         => array(),
				'bg_img'                                   => array(),
				'background_size'                          => array(),
				'background_image_width'                   => array(),
				'background_image_height'                  => array(),
				'background_repeat'                        => array(),
				'background_position'                      => array(),
				'background_horizontal_offset'             => array(),
				'background_vertical_offset'               => array(),
				'background_blend'                         => array(),
				'use_background_color_gradient'            => array(),
				'background_color_gradient_repeat'         => array(),
				'background_color_gradient_type'           => array(),
				'background_color_gradient_direction'      => array(),
				'background_color_gradient_direction_radial' => array(),
				'background_color_gradient_stops'          => array(),
				'background_color_gradient_unit'           => array(),
				'background_color_gradient_start'          => array(),
				'background_color_gradient_end'            => array(),
				'background_color_gradient_start_position' => array(),
				'background_color_gradient_end_position'   => array(),
				'background_color_gradient_overlays_image' => array(),
				'background_pattern_style'                 => array(),
				'background_enable_pattern_style'          => array(),
				'background_pattern_color'                 => array(),
				'background_pattern_transform'             => array(),
				'background_pattern_size'                  => array(),
				'background_pattern_width'                 => array(),
				'background_pattern_height'                => array(),
				'background_pattern_repeat_origin'         => array(),
				'background_pattern_horizontal_offset'     => array(),
				'background_pattern_vertical_offset'       => array(),
				'background_pattern_repeat'                => array(),
				'background_pattern_blend_mode'            => array(),
				'background_mask_style'                    => array(),
				'background_mask_color'                    => array(),
				'background_mask_transform'                => array(),
				'background_mask_aspect_ratio'             => array(),
				'background_mask_size'                     => array(),
				'background_mask_width'                    => array(),
				'background_mask_height'                   => array(),
				'background_mask_position'                 => array(),
				'background_mask_horizontal_offset'        => array(),
				'background_mask_vertical_offset'          => array(),
				'background_mask_blend_mode'               => array(),
				'background_enable_mask_style'             => array(),
				'background_video_mp4'                     => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'background_video_webm'                    => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'background_video_width'                   => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'background_video_height'                  => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'allow_player_pause'                       => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'background_video_pause_outside_viewport'  => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'__video_background'                       => array(
					'type'                => 'computed',
					'computed_callback'   => array(
						'ET_Builder_Column',
						'get_column_video_background',
					),
					'computed_depends_on' => array(
						'background_video_mp4',
						'background_video_webm',
						'background_video_width',
						'background_video_height',
					),
					'computed_minimum'    => array(
						'background_video_mp4',
						'background_video_webm',
					),
				),
				'padding_top'                              => array( 'tab_slug' => 'advanced' ),
				'padding_right'                            => array( 'tab_slug' => 'advanced' ),
				'padding_bottom'                           => array( 'tab_slug' => 'advanced' ),
				'padding_left'                             => array( 'tab_slug' => 'advanced' ),
				'padding_top_bottom_link'                  => array( 'tab_slug' => 'advanced' ),
				'padding_left_right_link'                  => array( 'tab_slug' => 'advanced' ),
				'padding_%column_index%_tablet'            => array(
					'has_custom_index_location' => true,
					'tab_slug'                  => 'advanced',
				),
				'padding_%column_index%_phone'             => array(
					'has_custom_index_location' => true,
					'tab_slug'                  => 'advanced',
				),
				'padding_%column_index%_last_edited'       => array(
					'has_custom_index_location' => true,
					'tab_slug'                  => 'advanced',
				),
				'module_id'                                => array( 'tab_slug' => 'custom_css' ),
				'module_class'                             => array( 'tab_slug' => 'custom_css' ),
				'custom_css_before'                        => array( 'tab_slug' => 'custom_css' ),
				'custom_css_main'                          => array( 'tab_slug' => 'custom_css' ),
				'custom_css_after'                         => array( 'tab_slug' => 'custom_css' ),
			)
		);

		return array_merge( $fields, $column_fields );
	}

	/**
	 * Get CSS fields transition of section.
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		// Section Dividers Height.
		foreach ( array( 'top', 'bottom' ) as $placement ) {
			// Inside sprintf, the double %% prints a literal '%' character.
			$selector                                = sprintf(
				'%%%%order_class%%%%.section_has_divider.et_pb_%1$s_divider .et_pb_%1$s_inside_divider',
				$placement
			);
			$fields[ "{$placement}_divider_height" ] = array(
				'height'          => $selector,
				'background-size' => $selector,
			);
		}

		return $fields;
	}

	/**
	 * Check if current background is transparent background or not.
	 *
	 * @param string $background_color Background color.
	 *
	 * @return boolean Transparent color status.
	 * @since 3.24.1
	 */
	public function is_transparent_background( $background_color = '' ) {
		$page_setting_section_background = et_builder_settings_get( 'et_pb_section_background_color', get_the_ID() );
		$is_empty_background_color       = '' === $background_color && '' === $page_setting_section_background;
		$is_layout_block                 = ET_GB_Block_Layout::is_layout_block() || ET_GB_Block_Layout::is_layout_block_preview();

		return 'rgba(255,255,255,0)' === $background_color
			|| ( $is_layout_block && $is_empty_background_color )
			|| ( et_is_builder_plugin_active() && $is_empty_background_color );
	}

	/**
	 * Check if current background should have initial background color.
	 *
	 * @since 3.24.1
	 *
	 * @param string $mode Preview mode.
	 *
	 * @return boolean
	 */
	public function is_initial_background_color( $mode = 'desktop' ) {
		// Ensure $mode parameter not empty.
		$mode          = '' === $mode ? 'desktop' : $mode;
		$device_suffix = 'desktop' !== $mode && 'hover' !== $mode ? "_{$mode}" : '';

		$parallax           = 'hover' === $mode ? et_pb_hover_options()->get_raw_value( 'parallax', $this->props ) : et_pb_responsive_options()->get_any_value( $this->props, "parallax{$device_suffix}", '', true );
		$background_blend   = 'hover' === $mode ? et_pb_hover_options()->get_raw_value( 'background_blend', $this->props ) : et_pb_responsive_options()->get_any_value( $this->props, "background_blend{$device_suffix}", '', true );
		$use_gradient_value = et_pb_responsive_options()->get_inheritance_background_value( $this->props, 'use_background_color_gradient', $mode );
		$background_image   = et_pb_responsive_options()->get_inheritance_background_value( $this->props, 'background_image', $mode );
		$bg_blend_default   = self::$_->array_get( $this->fields_unprocessed, 'background_blend.default', '' );

		$is_gradient_active = 'on' === $use_gradient_value;
		$is_image_active    = '' !== $background_image && 'on' !== $parallax;
		$is_image_blend     = '' !== $background_blend && $background_blend !== $bg_blend_default;

		return $is_gradient_active && $is_image_active && $is_image_blend;
	}

	/**
	 * Generate parallax image background markup.
	 *
	 * @since 3.24.1
	 * @since 4.15.0 Added $props property.
	 *
	 * @param string $base_name Background base name.
	 * @param array  $props Props (optional).
	 *
	 * @return string
	 */
	public function get_parallax_image_background( $base_name = 'background', $props = array() ) {
		$props         = empty( $props ) ? $this->props : $props;
		$attr_prefix   = "{$base_name}_";
		$custom_prefix = 'background' === $base_name ? '' : "{$base_name}_";

		$parallax_processed  = array();
		$parallax_background = '';
		$hover_suffix        = et_pb_hover_options()->get_suffix();
		$sticky_suffix       = et_pb_sticky_options()->get_suffix();
		$preview_modes       = array( $hover_suffix, $sticky_suffix, '_phone', '_tablet', '' );

		// Featured Image as Background.
		$featured_image     = '';
		$featured_placement = '';
		$featured_image_src = '';
		if ( $this->featured_image_background ) {
			$featured_image         = self::$_->array_get( $props, 'featured_image', '' );
			$featured_placement     = self::$_->array_get( $props, 'featured_placement', '' );
			$featured_image_src_obj = wp_get_attachment_image_src( get_post_thumbnail_id( self::_get_main_post_id() ), 'full' );
			$featured_image_src     = isset( $featured_image_src_obj[0] ) ? $featured_image_src_obj[0] : '';
		}

		// Parallax Gradient.
		$background_options = et_pb_background_options();
		$is_gradient_on     = false;

		foreach ( $preview_modes as $suffix ) {
			$is_hover  = $hover_suffix === $suffix;
			$is_sticky = $sticky_suffix === $suffix;

			// A. Bail early if hover or responsive settings disabled on mobile/hover.
			if ( '' !== $suffix ) {
				// Ensure responsive settings is enabled on mobile.
				if ( ! $is_hover && ! $is_sticky && ! et_pb_responsive_options()->is_responsive_enabled( $props, $base_name ) ) {
					continue;
				}

				// Ensure hover settings is enabled.
				if ( $is_hover && ! et_pb_hover_options()->is_enabled( $base_name, $props ) ) {
					continue;
				}

				// Ensure sticky setting is enabled.
				if ( $is_sticky && ! et_pb_sticky_options()->is_enabled( $base_name, $props ) ) {
					continue;
				}
			}

			// Prepare preview mode.
			$mode = '' !== $suffix ? str_replace( '_', '', $suffix ) : 'desktop';
			$mode = $is_hover ? 'hover' : $mode;
			$mode = $is_sticky ? 'sticky' : $mode;

			// B.1. Get inherited background value.
			$use_gradient_options      = et_pb_responsive_options()->get_inheritance_background_value( $props, "use_{$base_name}_color_gradient", $mode, $base_name, $this->fields_unprocessed );
			$background_image          = et_pb_responsive_options()->get_inheritance_background_value( $props, "{$attr_prefix}image", $mode, $base_name, $this->fields_unprocessed );
			$parallax                  = et_pb_responsive_options()->get_any_value( $props, "parallax{$suffix}", '', true );
			$parallax_method           = et_pb_responsive_options()->get_any_value( $props, "parallax_method{$suffix}", '', true );
			$gradient_overlays_image   = et_pb_responsive_options()->get_any_value( $props, "{$base_name}_color_gradient_overlays_image{$suffix}", '', true );
			$background_gradient_blend = et_pb_responsive_options()->get_any_value( $props, "{$base_name}_blend{$suffix}", 'normal', true );

			// B.2. Set default value for parallax and parallax method on hover when they are empty.
			if ( $is_hover || $is_sticky ) {
				$parallax                  = et_pb_hover_options()->get_raw_value( "{$custom_prefix}parallax", $props, $parallax );
				$parallax_method           = et_pb_hover_options()->get_raw_value( "{$custom_prefix}parallax_method", $props, $parallax_method );
				$gradient_overlays_image   = et_pb_hover_options()->get_raw_value( "{$base_name}_color_gradient_overlays_image", $props, $gradient_overlays_image );
				$background_gradient_blend = et_pb_hover_options()->get_raw_value( "{$base_name}_blend", $props, $background_gradient_blend );

				$gradient_properties_desktop = $background_options->get_gradient_properties( $props, $base_name, '' );
				$gradient_properties         = $background_options->get_gradient_mode_properties(
					$mode,
					$props,
					$base_name,
					$gradient_properties_desktop
				);
				$background_gradient_style   = $background_options->get_gradient_style( $gradient_properties );
			} else {
				$gradient_properties       = $background_options->get_gradient_properties( $props, $base_name, $suffix );
				$background_gradient_style = $background_options->get_gradient_style( $gradient_properties );
			}

			if ( 'on' === $use_gradient_options && 'on' === $gradient_overlays_image && 'on' === $parallax ) {
				$is_gradient_on = '' !== $background_gradient_style;
			}

			// B.3. Override background image with featured image if needed.
			if ( 'on' === $featured_image && 'background' === $featured_placement && '' !== $featured_image_src ) {
				$background_image = $featured_image_src;
			}

			// C.1. Parallax BG Class to inform if other modes exist.
			$parallax_classname          = array();
			$parallax_gradient_classname = array();
			if ( ( '_tablet' === $suffix || '' === $suffix ) && in_array( '_phone', $parallax_processed, true ) ) {
				$parallax_classname[]          = 'et_parallax_bg_phone_exist';
				$parallax_gradient_classname[] = 'et_parallax_gradient_phone_exist';
			}

			if ( '' === $suffix && in_array( '_tablet', $parallax_processed, true ) ) {
				$parallax_classname[]          = 'et_parallax_bg_tablet_exist';
				$parallax_gradient_classname[] = 'et_parallax_gradient_tablet_exist';
			}

			if ( in_array( $hover_suffix, $parallax_processed, true ) ) {
				$parallax_classname[]          = 'et_parallax_bg_hover_exist';
				$parallax_gradient_classname[] = 'et_parallax_gradient_hover_exist';
			}

			if ( in_array( $sticky_suffix, $parallax_processed, true ) ) {
				$parallax_classname[]          = 'et_parallax_bg_sticky_exist';
				$parallax_gradient_classname[] = 'et_parallax_gradient_sticky_exist';
			}

			// C.2. Set up parallax class and wrapper.
			if ( '' !== $background_image && 'on' === $parallax ) {
				$parallax_classname[]          = 'et_parallax_bg';
				$parallax_gradient_classname[] = 'et_parallax_gradient';

				if ( 'off' === $parallax_method ) {
					$parallax_classname[]          = 'et_pb_parallax_css';
					$parallax_gradient_classname[] = 'et_pb_parallax_css';

					if ( isset( $props['inner_shadow'] ) && 'off' !== $props['inner_shadow'] ) {
						$parallax_classname[] = 'et_pb_inner_shadow';
					}
				}

				// Parallax BG Class with suffix.
				if ( '' !== $suffix ) {
					$parallax_classname[]          = "et_parallax_bg{$suffix}";
					$parallax_gradient_classname[] = "et_parallax_gradient{$suffix}";
				}

				$background_gradient_image = sprintf(
					'background-image: %1$s;',
					esc_attr( $background_gradient_style )
				);
				$background_gradient_blend = 'normal' !== $background_gradient_blend
					? sprintf(
						'mix-blend-mode: %1$s;',
						esc_attr( $background_gradient_blend )
					)
					: '';
				$parallax_gradient         = sprintf(
					'<span
						class="%1$s"
						style="%2$s%3$s"
					></span>',
					esc_attr( implode( ' ', $parallax_gradient_classname ) ), // #1
					et_core_esc_previously( $background_gradient_image ), // #2
					et_core_esc_previously( $background_gradient_blend ) // #3
				);

				$parallax_background .= sprintf(
					'<span class="et_parallax_bg_wrap"><span
						class="%1$s"
						style="background-image: url(%2$s);"
					></span>%3$s</span>',
					esc_attr( implode( ' ', $parallax_classname ) ),
					esc_url( $background_image ),
					$is_gradient_on ? et_core_esc_previously( $parallax_gradient ) : ''
				);

				// Cleanup.
				$background_gradient_image = null;
				$background_gradient_blend = null;
				$parallax_gradient         = null;

				// set `.et_parallax_bg_wrap` border-radius.
				et_set_parallax_bg_wrap_border_radius( $props, $this->slug, '%%order_class%%' );
			}

			// C.3. Hover parallax class.
			if ( '' !== $background_image && $is_hover ) {
				$this->add_classname( 'et_pb_section_parallax_hover' );
			}

			// C.4. Sticky parallax class.
			if ( '' !== $background_image && $is_sticky ) {
				$this->add_classname( 'et_pb_section_parallax_sticky' );
			}

			array_push( $parallax_processed, $suffix );
		}

		// Added classname for module wrapper.
		if ( '' !== $parallax_background ) {
			$this->add_classname( 'et_pb_section_parallax' );
		}

		return $parallax_background;
	}

	/**
	 * Generates the structure module's HTML output based on {@see ET_Builder_Section::$props}.
	 *
	 * @param array  $atts       List of unprocessed attributes.
	 * @param mixed  $content     Content being processed.
	 * @param string $function_name Slug of module that is used for rendering output.
	 *
	 * @return string The module's HTML output.
	 */
	public function render( $atts, $content, $function_name ) {
		$multi_view                                   = et_pb_multi_view_options( $this );
		$hover                                        = et_pb_hover_options();
		$sticky                                       = et_pb_sticky_options();
		$background_video_mp4                         = $this->props['background_video_mp4'];
		$background_video_webm                        = $this->props['background_video_webm'];
		$inner_shadow                                 = $this->props['inner_shadow'];
		$parallax                                     = $this->props['parallax'];
		$parallax_method                              = $this->props['parallax_method'];
		$fullwidth                                    = $this->props['fullwidth'];
		$specialty                                    = $this->props['specialty'];
		$background_color_1                           = isset( $this->props['background_color_1'] ) ? $this->props['background_color_1'] : '';
		$background_color_2                           = isset( $this->props['background_color_2'] ) ? $this->props['background_color_2'] : '';
		$background_color_3                           = isset( $this->props['background_color_3'] ) ? $this->props['background_color_3'] : '';
		$bg_img_1                                     = isset( $this->props['bg_img_1'] ) ? $this->props['bg_img_1'] : '';
		$bg_img_2                                     = isset( $this->props['bg_img_2'] ) ? $this->props['bg_img_2'] : '';
		$bg_img_3                                     = isset( $this->props['bg_img_3'] ) ? $this->props['bg_img_3'] : '';
		$background_size_1                            = isset( $this->props['background_size_1'] ) ? $this->props['background_size_1'] : '';
		$background_size_2                            = isset( $this->props['background_size_2'] ) ? $this->props['background_size_2'] : '';
		$background_size_3                            = isset( $this->props['background_size_3'] ) ? $this->props['background_size_3'] : '';
		$background_image_width_1                     = isset( $this->props['background_image_width_1'] ) ? $this->props['background_image_width_1'] : '';
		$background_image_width_2                     = isset( $this->props['background_image_width_2'] ) ? $this->props['background_image_width_2'] : '';
		$background_image_width_3                     = isset( $this->props['background_image_width_3'] ) ? $this->props['background_image_width_3'] : '';
		$background_image_height_1                    = isset( $this->props['background_image_height_1'] ) ? $this->props['background_image_height_1'] : '';
		$background_image_height_2                    = isset( $this->props['background_image_height_2'] ) ? $this->props['background_image_height_2'] : '';
		$background_image_height_3                    = isset( $this->props['background_image_height_3'] ) ? $this->props['background_image_height_3'] : '';
		$background_position_1                        = isset( $this->props['background_position_1'] ) ? $this->props['background_position_1'] : '';
		$background_position_2                        = isset( $this->props['background_position_2'] ) ? $this->props['background_position_2'] : '';
		$background_position_3                        = isset( $this->props['background_position_3'] ) ? $this->props['background_position_3'] : '';
		$background_horizontal_offset_1               = isset( $this->props['background_horizontal_offset_1'] ) ? $this->props['background_horizontal_offset_1'] : '';
		$background_horizontal_offset_2               = isset( $this->props['background_horizontal_offset_2'] ) ? $this->props['background_horizontal_offset_2'] : '';
		$background_horizontal_offset_3               = isset( $this->props['background_horizontal_offset_3'] ) ? $this->props['background_horizontal_offset_3'] : '';
		$background_vertical_offset_1                 = isset( $this->props['background_vertical_offset_1'] ) ? $this->props['background_vertical_offset_1'] : '';
		$background_vertical_offset_2                 = isset( $this->props['background_vertical_offset_2'] ) ? $this->props['background_vertical_offset_2'] : '';
		$background_vertical_offset_3                 = isset( $this->props['background_vertical_offset_3'] ) ? $this->props['background_vertical_offset_3'] : '';
		$background_repeat_1                          = isset( $this->props['background_repeat_1'] ) ? $this->props['background_repeat_1'] : '';
		$background_repeat_2                          = isset( $this->props['background_repeat_2'] ) ? $this->props['background_repeat_2'] : '';
		$background_repeat_3                          = isset( $this->props['background_repeat_3'] ) ? $this->props['background_repeat_3'] : '';
		$background_blend_1                           = isset( $this->props['background_blend_1'] ) ? $this->props['background_blend_1'] : '';
		$background_blend_2                           = isset( $this->props['background_blend_2'] ) ? $this->props['background_blend_2'] : '';
		$background_blend_3                           = isset( $this->props['background_blend_3'] ) ? $this->props['background_blend_3'] : '';
		$parallax_1                                   = isset( $this->props['parallax_1'] ) ? $this->props['parallax_1'] : '';
		$parallax_2                                   = isset( $this->props['parallax_2'] ) ? $this->props['parallax_2'] : '';
		$parallax_3                                   = isset( $this->props['parallax_3'] ) ? $this->props['parallax_3'] : '';
		$parallax_method_1                            = isset( $this->props['parallax_method_1'] ) ? $this->props['parallax_method_1'] : '';
		$parallax_method_2                            = isset( $this->props['parallax_method_2'] ) ? $this->props['parallax_method_2'] : '';
		$parallax_method_3                            = isset( $this->props['parallax_method_3'] ) ? $this->props['parallax_method_3'] : '';
		$padding_top_1                                = isset( $this->props['padding_top_1'] ) ? $this->props['padding_top_1'] : '';
		$padding_top_2                                = isset( $this->props['padding_top_2'] ) ? $this->props['padding_top_2'] : '';
		$padding_top_3                                = isset( $this->props['padding_top_3'] ) ? $this->props['padding_top_3'] : '';
		$padding_right_1                              = isset( $this->props['padding_right_1'] ) ? $this->props['padding_right_1'] : '';
		$padding_right_2                              = isset( $this->props['padding_right_2'] ) ? $this->props['padding_right_2'] : '';
		$padding_right_3                              = isset( $this->props['padding_right_3'] ) ? $this->props['padding_right_3'] : '';
		$padding_left_1                               = isset( $this->props['padding_left_1'] ) ? $this->props['padding_left_1'] : '';
		$padding_left_2                               = isset( $this->props['padding_left_2'] ) ? $this->props['padding_left_2'] : '';
		$padding_left_3                               = isset( $this->props['padding_left_3'] ) ? $this->props['padding_left_3'] : '';
		$padding_bottom_1                             = isset( $this->props['padding_bottom_1'] ) ? $this->props['padding_bottom_1'] : '';
		$padding_bottom_2                             = isset( $this->props['padding_bottom_2'] ) ? $this->props['padding_bottom_2'] : '';
		$padding_bottom_3                             = isset( $this->props['padding_bottom_3'] ) ? $this->props['padding_bottom_3'] : '';
		$padding_1_tablet                             = isset( $this->props['padding_1_tablet'] ) ? $this->props['padding_1_tablet'] : '';
		$padding_2_tablet                             = isset( $this->props['padding_2_tablet'] ) ? $this->props['padding_2_tablet'] : '';
		$padding_3_tablet                             = isset( $this->props['padding_3_tablet'] ) ? $this->props['padding_3_tablet'] : '';
		$padding_1_phone                              = isset( $this->props['padding_1_phone'] ) ? $this->props['padding_1_phone'] : '';
		$padding_2_phone                              = isset( $this->props['padding_2_phone'] ) ? $this->props['padding_2_phone'] : '';
		$padding_3_phone                              = isset( $this->props['padding_3_phone'] ) ? $this->props['padding_3_phone'] : '';
		$padding_1_last_edited                        = isset( $this->props['padding_1_last_edited'] ) ? $this->props['padding_1_last_edited'] : '';
		$padding_2_last_edited                        = isset( $this->props['padding_2_last_edited'] ) ? $this->props['padding_2_last_edited'] : '';
		$padding_3_last_edited                        = isset( $this->props['padding_3_last_edited'] ) ? $this->props['padding_3_last_edited'] : '';
		$gutter_width                                 = $this->props['gutter_width'];
		$gutter_width_hover                           = $this->get_hover_value( 'gutter_width' );
		$make_equal                                   = $this->props['make_equal'];
		$global_module                                = $this->props['global_module'];
		$use_custom_gutter                            = $this->props['use_custom_gutter'];
		$module_id_1                                  = isset( $this->props['module_id_1'] ) ? $this->props['module_id_1'] : '';
		$module_id_2                                  = isset( $this->props['module_id_2'] ) ? $this->props['module_id_2'] : '';
		$module_id_3                                  = isset( $this->props['module_id_3'] ) ? $this->props['module_id_3'] : '';
		$module_class_1                               = isset( $this->props['module_class_1'] ) ? $this->props['module_class_1'] : '';
		$module_class_2                               = isset( $this->props['module_class_2'] ) ? $this->props['module_class_2'] : '';
		$module_class_3                               = isset( $this->props['module_class_3'] ) ? $this->props['module_class_3'] : '';
		$custom_css_before_1                          = isset( $this->props['custom_css_before_1'] ) ? $this->props['custom_css_before_1'] : '';
		$custom_css_before_2                          = isset( $this->props['custom_css_before_2'] ) ? $this->props['custom_css_before_2'] : '';
		$custom_css_before_3                          = isset( $this->props['custom_css_before_3'] ) ? $this->props['custom_css_before_3'] : '';
		$custom_css_main_1                            = isset( $this->props['custom_css_main_1'] ) ? $this->props['custom_css_main_1'] : '';
		$custom_css_main_2                            = isset( $this->props['custom_css_main_2'] ) ? $this->props['custom_css_main_2'] : '';
		$custom_css_main_3                            = isset( $this->props['custom_css_main_3'] ) ? $this->props['custom_css_main_3'] : '';
		$custom_css_after_1                           = isset( $this->props['custom_css_after_1'] ) ? $this->props['custom_css_after_1'] : '';
		$custom_css_after_2                           = isset( $this->props['custom_css_after_2'] ) ? $this->props['custom_css_after_2'] : '';
		$custom_css_after_3                           = isset( $this->props['custom_css_after_3'] ) ? $this->props['custom_css_after_3'] : '';
		$custom_css_before_1_hover                    = $this->get_hover_value( 'custom_css_before_1' );
		$custom_css_before_2_hover                    = $this->get_hover_value( 'custom_css_before_2' );
		$custom_css_before_3_hover                    = $this->get_hover_value( 'custom_css_before_3' );
		$custom_css_main_1_hover                      = $this->get_hover_value( 'custom_css_main_1' );
		$custom_css_main_2_hover                      = $this->get_hover_value( 'custom_css_main_2' );
		$custom_css_main_3_hover                      = $this->get_hover_value( 'custom_css_main_3' );
		$custom_css_after_1_hover                     = $this->get_hover_value( 'custom_css_after_1' );
		$custom_css_after_2_hover                     = $this->get_hover_value( 'custom_css_after_2' );
		$custom_css_after_3_hover                     = $this->get_hover_value( 'custom_css_after_3' );
		$custom_css_before_1_sticky                   = $sticky->get_value( 'custom_css_before_1', $this->props, '' );
		$custom_css_before_2_sticky                   = $sticky->get_value( 'custom_css_before_2', $this->props, '' );
		$custom_css_before_3_sticky                   = $sticky->get_value( 'custom_css_before_3', $this->props, '' );
		$custom_css_main_1_sticky                     = $sticky->get_value( 'custom_css_main_1', $this->props, '' );
		$custom_css_main_2_sticky                     = $sticky->get_value( 'custom_css_main_2', $this->props, '' );
		$custom_css_main_3_sticky                     = $sticky->get_value( 'custom_css_main_3', $this->props, '' );
		$custom_css_after_1_sticky                    = $sticky->get_value( 'custom_css_after_1', $this->props, '' );
		$custom_css_after_2_sticky                    = $sticky->get_value( 'custom_css_after_2', $this->props, '' );
		$custom_css_after_3_sticky                    = $sticky->get_value( 'custom_css_after_3', $this->props, '' );
		$use_background_color_gradient_1              = isset( $this->props['use_background_color_gradient_1'] ) ? $this->props['use_background_color_gradient_1'] : '';
		$use_background_color_gradient_2              = isset( $this->props['use_background_color_gradient_2'] ) ? $this->props['use_background_color_gradient_2'] : '';
		$use_background_color_gradient_3              = isset( $this->props['use_background_color_gradient_3'] ) ? $this->props['use_background_color_gradient_3'] : '';
		$background_color_gradient_repeat_1           = isset( $this->props['background_color_gradient_repeat_1'] ) ? $this->props['background_color_gradient_repeat_1'] : '';
		$background_color_gradient_repeat_2           = isset( $this->props['background_color_gradient_repeat_2'] ) ? $this->props['background_color_gradient_repeat_2'] : '';
		$background_color_gradient_repeat_3           = isset( $this->props['background_color_gradient_repeat_3'] ) ? $this->props['background_color_gradient_repeat_3'] : '';
		$background_color_gradient_type_1             = isset( $this->props['background_color_gradient_type_1'] ) ? $this->props['background_color_gradient_type_1'] : '';
		$background_color_gradient_type_2             = isset( $this->props['background_color_gradient_type_2'] ) ? $this->props['background_color_gradient_type_2'] : '';
		$background_color_gradient_type_3             = isset( $this->props['background_color_gradient_type_3'] ) ? $this->props['background_color_gradient_type_3'] : '';
		$background_color_gradient_direction_1        = isset( $this->props['background_color_gradient_direction_1'] ) ? $this->props['background_color_gradient_direction_1'] : '';
		$background_color_gradient_direction_2        = isset( $this->props['background_color_gradient_direction_2'] ) ? $this->props['background_color_gradient_direction_2'] : '';
		$background_color_gradient_direction_3        = isset( $this->props['background_color_gradient_direction_3'] ) ? $this->props['background_color_gradient_direction_3'] : '';
		$background_color_gradient_direction_radial_1 = isset( $this->props['background_color_gradient_direction_radial_1'] ) ? $this->props['background_color_gradient_direction_radial_1'] : '';
		$background_color_gradient_direction_radial_2 = isset( $this->props['background_color_gradient_direction_radial_2'] ) ? $this->props['background_color_gradient_direction_radial_2'] : '';
		$background_color_gradient_direction_radial_3 = isset( $this->props['background_color_gradient_direction_radial_3'] ) ? $this->props['background_color_gradient_direction_radial_3'] : '';
		$background_color_gradient_stops_1            = isset( $this->props['background_color_gradient_stops_1'] ) ? $this->props['background_color_gradient_stops_1'] : '';
		$background_color_gradient_stops_2            = isset( $this->props['background_color_gradient_stops_2'] ) ? $this->props['background_color_gradient_stops_2'] : '';
		$background_color_gradient_stops_3            = isset( $this->props['background_color_gradient_stops_3'] ) ? $this->props['background_color_gradient_stops_3'] : '';
		$background_color_gradient_unit_1             = isset( $this->props['background_color_gradient_unit_1'] ) ? $this->props['background_color_gradient_unit_1'] : '';
		$background_color_gradient_unit_2             = isset( $this->props['background_color_gradient_unit_2'] ) ? $this->props['background_color_gradient_unit_2'] : '';
		$background_color_gradient_unit_3             = isset( $this->props['background_color_gradient_unit_3'] ) ? $this->props['background_color_gradient_unit_3'] : '';
		$background_color_gradient_overlays_image_1   = isset( $this->props['background_color_gradient_overlays_image_1'] ) ? $this->props['background_color_gradient_overlays_image_1'] : '';
		$background_color_gradient_overlays_image_2   = isset( $this->props['background_color_gradient_overlays_image_2'] ) ? $this->props['background_color_gradient_overlays_image_2'] : '';
		$background_color_gradient_overlays_image_3   = isset( $this->props['background_color_gradient_overlays_image_3'] ) ? $this->props['background_color_gradient_overlays_image_3'] : '';
		$background_color_gradient_start_1            = isset( $this->props['background_color_gradient_start_1'] ) ? $this->props['background_color_gradient_start_1'] : '';
		$background_color_gradient_start_2            = isset( $this->props['background_color_gradient_start_2'] ) ? $this->props['background_color_gradient_start_2'] : '';
		$background_color_gradient_start_3            = isset( $this->props['background_color_gradient_start_3'] ) ? $this->props['background_color_gradient_start_3'] : '';
		$background_color_gradient_end_1              = isset( $this->props['background_color_gradient_end_1'] ) ? $this->props['background_color_gradient_end_1'] : '';
		$background_color_gradient_end_2              = isset( $this->props['background_color_gradient_end_2'] ) ? $this->props['background_color_gradient_end_2'] : '';
		$background_color_gradient_end_3              = isset( $this->props['background_color_gradient_end_3'] ) ? $this->props['background_color_gradient_end_3'] : '';
		$background_color_gradient_start_position_1   = isset( $this->props['background_color_gradient_start_position_1'] ) ? $this->props['background_color_gradient_start_position_1'] : '';
		$background_color_gradient_start_position_2   = isset( $this->props['background_color_gradient_start_position_2'] ) ? $this->props['background_color_gradient_start_position_2'] : '';
		$background_color_gradient_start_position_3   = isset( $this->props['background_color_gradient_start_position_3'] ) ? $this->props['background_color_gradient_start_position_3'] : '';
		$background_color_gradient_end_position_1     = isset( $this->props['background_color_gradient_end_position_1'] ) ? $this->props['background_color_gradient_end_position_1'] : '';
		$background_color_gradient_end_position_2     = isset( $this->props['background_color_gradient_end_position_2'] ) ? $this->props['background_color_gradient_end_position_2'] : '';
		$background_color_gradient_end_position_3     = isset( $this->props['background_color_gradient_end_position_3'] ) ? $this->props['background_color_gradient_end_position_3'] : '';
		$background_enable_pattern_style_1            = isset( $this->props['background_enable_pattern_style_1'] ) ? $this->props['background_enable_pattern_style_1'] : '';
		$background_enable_pattern_style_2            = isset( $this->props['background_enable_pattern_style_2'] ) ? $this->props['background_enable_pattern_style_2'] : '';
		$background_enable_pattern_style_3            = isset( $this->props['background_enable_pattern_style_3'] ) ? $this->props['background_enable_pattern_style_3'] : '';
		$background_pattern_style_1                   = isset( $this->props['background_pattern_style_1'] ) ? $this->props['background_pattern_style_1'] : '';
		$background_pattern_style_2                   = isset( $this->props['background_pattern_style_2'] ) ? $this->props['background_pattern_style_2'] : '';
		$background_pattern_style_3                   = isset( $this->props['background_pattern_style_3'] ) ? $this->props['background_pattern_style_3'] : '';
		$background_pattern_color_1                   = isset( $this->props['background_pattern_color_1'] ) ? $this->props['background_pattern_color_1'] : '';
		$background_pattern_color_2                   = isset( $this->props['background_pattern_color_2'] ) ? $this->props['background_pattern_color_2'] : '';
		$background_pattern_color_3                   = isset( $this->props['background_pattern_color_3'] ) ? $this->props['background_pattern_color_3'] : '';
		$background_pattern_transform_1               = isset( $this->props['background_pattern_transform_1'] ) ? $this->props['background_pattern_transform_1'] : '';
		$background_pattern_transform_2               = isset( $this->props['background_pattern_transform_2'] ) ? $this->props['background_pattern_transform_2'] : '';
		$background_pattern_transform_3               = isset( $this->props['background_pattern_transform_3'] ) ? $this->props['background_pattern_transform_3'] : '';
		$background_pattern_size_1                    = isset( $this->props['background_pattern_size_1'] ) ? $this->props['background_pattern_size_1'] : '';
		$background_pattern_size_2                    = isset( $this->props['background_pattern_size_2'] ) ? $this->props['background_pattern_size_2'] : '';
		$background_pattern_size_3                    = isset( $this->props['background_pattern_size_3'] ) ? $this->props['background_pattern_size_3'] : '';
		$background_pattern_width_1                   = isset( $this->props['background_pattern_width_1'] ) ? $this->props['background_pattern_width_1'] : '';
		$background_pattern_width_2                   = isset( $this->props['background_pattern_width_2'] ) ? $this->props['background_pattern_width_2'] : '';
		$background_pattern_width_3                   = isset( $this->props['background_pattern_width_3'] ) ? $this->props['background_pattern_width_3'] : '';
		$background_pattern_height_1                  = isset( $this->props['background_pattern_height_1'] ) ? $this->props['background_pattern_height_1'] : '';
		$background_pattern_height_2                  = isset( $this->props['background_pattern_height_2'] ) ? $this->props['background_pattern_height_2'] : '';
		$background_pattern_height_3                  = isset( $this->props['background_pattern_height_3'] ) ? $this->props['background_pattern_height_3'] : '';
		$background_pattern_repeat_origin_1           = isset( $this->props['background_pattern_repeat_origin_1'] ) ? $this->props['background_pattern_repeat_origin_1'] : '';
		$background_pattern_repeat_origin_2           = isset( $this->props['background_pattern_repeat_origin_2'] ) ? $this->props['background_pattern_repeat_origin_2'] : '';
		$background_pattern_repeat_origin_3           = isset( $this->props['background_pattern_repeat_origin_3'] ) ? $this->props['background_pattern_repeat_origin_3'] : '';
		$background_pattern_horizontal_offset_1       = isset( $this->props['background_pattern_horizontal_offset_1'] ) ? $this->props['background_pattern_horizontal_offset_1'] : '';
		$background_pattern_horizontal_offset_2       = isset( $this->props['background_pattern_horizontal_offset_2'] ) ? $this->props['background_pattern_horizontal_offset_2'] : '';
		$background_pattern_horizontal_offset_3       = isset( $this->props['background_pattern_horizontal_offset_3'] ) ? $this->props['background_pattern_horizontal_offset_3'] : '';
		$background_pattern_vertical_offset_1         = isset( $this->props['background_pattern_vertical_offset_1'] ) ? $this->props['background_pattern_vertical_offset_1'] : '';
		$background_pattern_vertical_offset_2         = isset( $this->props['background_pattern_vertical_offset_2'] ) ? $this->props['background_pattern_vertical_offset_2'] : '';
		$background_pattern_vertical_offset_3         = isset( $this->props['background_pattern_vertical_offset_3'] ) ? $this->props['background_pattern_vertical_offset_3'] : '';
		$background_pattern_repeat_1                  = isset( $this->props['background_pattern_repeat_1'] ) ? $this->props['background_pattern_repeat_1'] : '';
		$background_pattern_repeat_2                  = isset( $this->props['background_pattern_repeat_2'] ) ? $this->props['background_pattern_repeat_2'] : '';
		$background_pattern_repeat_3                  = isset( $this->props['background_pattern_repeat_3'] ) ? $this->props['background_pattern_repeat_3'] : '';
		$background_pattern_blend_mode_1              = isset( $this->props['background_pattern_blend_mode_1'] ) ? $this->props['background_pattern_blend_mode_1'] : '';
		$background_pattern_blend_mode_2              = isset( $this->props['background_pattern_blend_mode_2'] ) ? $this->props['background_pattern_blend_mode_2'] : '';
		$background_pattern_blend_mode_3              = isset( $this->props['background_pattern_blend_mode_3'] ) ? $this->props['background_pattern_blend_mode_3'] : '';
		$background_enable_mask_style_1               = isset( $this->props['background_enable_mask_style_1'] ) ? $this->props['background_enable_mask_style_1'] : '';
		$background_enable_mask_style_2               = isset( $this->props['background_enable_mask_style_2'] ) ? $this->props['background_enable_mask_style_2'] : '';
		$background_enable_mask_style_3               = isset( $this->props['background_enable_mask_style_3'] ) ? $this->props['background_enable_mask_style_3'] : '';
		$background_mask_style_1                      = isset( $this->props['background_mask_style_1'] ) ? $this->props['background_mask_style_1'] : '';
		$background_mask_style_2                      = isset( $this->props['background_mask_style_2'] ) ? $this->props['background_mask_style_2'] : '';
		$background_mask_style_3                      = isset( $this->props['background_mask_style_3'] ) ? $this->props['background_mask_style_3'] : '';
		$background_mask_color_1                      = isset( $this->props['background_mask_color_1'] ) ? $this->props['background_mask_color_1'] : '';
		$background_mask_color_2                      = isset( $this->props['background_mask_color_2'] ) ? $this->props['background_mask_color_2'] : '';
		$background_mask_color_3                      = isset( $this->props['background_mask_color_3'] ) ? $this->props['background_mask_color_3'] : '';
		$background_mask_transform_1                  = isset( $this->props['background_mask_transform_1'] ) ? $this->props['background_mask_transform_1'] : '';
		$background_mask_transform_2                  = isset( $this->props['background_mask_transform_2'] ) ? $this->props['background_mask_transform_2'] : '';
		$background_mask_transform_3                  = isset( $this->props['background_mask_transform_3'] ) ? $this->props['background_mask_transform_3'] : '';
		$background_mask_aspect_ratio_1               = isset( $this->props['background_mask_aspect_ratio_1'] ) ? $this->props['background_mask_aspect_ratio_1'] : '';
		$background_mask_aspect_ratio_2               = isset( $this->props['background_mask_aspect_ratio_2'] ) ? $this->props['background_mask_aspect_ratio_2'] : '';
		$background_mask_aspect_ratio_3               = isset( $this->props['background_mask_aspect_ratio_3'] ) ? $this->props['background_mask_aspect_ratio_3'] : '';
		$background_mask_size_1                       = isset( $this->props['background_mask_size_1'] ) ? $this->props['background_mask_size_1'] : '';
		$background_mask_size_2                       = isset( $this->props['background_mask_size_2'] ) ? $this->props['background_mask_size_2'] : '';
		$background_mask_size_3                       = isset( $this->props['background_mask_size_3'] ) ? $this->props['background_mask_size_3'] : '';
		$background_mask_width_1                      = isset( $this->props['background_mask_width_1'] ) ? $this->props['background_mask_width_1'] : '';
		$background_mask_width_2                      = isset( $this->props['background_mask_width_2'] ) ? $this->props['background_mask_width_2'] : '';
		$background_mask_width_3                      = isset( $this->props['background_mask_width_3'] ) ? $this->props['background_mask_width_3'] : '';
		$background_mask_height_1                     = isset( $this->props['background_mask_height_1'] ) ? $this->props['background_mask_height_1'] : '';
		$background_mask_height_2                     = isset( $this->props['background_mask_height_2'] ) ? $this->props['background_mask_height_2'] : '';
		$background_mask_height_3                     = isset( $this->props['background_mask_height_3'] ) ? $this->props['background_mask_height_3'] : '';
		$background_mask_position_1                   = isset( $this->props['background_mask_position_1'] ) ? $this->props['background_mask_position_1'] : '';
		$background_mask_position_2                   = isset( $this->props['background_mask_position_2'] ) ? $this->props['background_mask_position_2'] : '';
		$background_mask_position_3                   = isset( $this->props['background_mask_position_3'] ) ? $this->props['background_mask_position_3'] : '';
		$background_mask_horizontal_offset_1          = isset( $this->props['background_mask_horizontal_offset_1'] ) ? $this->props['background_mask_horizontal_offset_1'] : '';
		$background_mask_horizontal_offset_2          = isset( $this->props['background_mask_horizontal_offset_2'] ) ? $this->props['background_mask_horizontal_offset_2'] : '';
		$background_mask_horizontal_offset_3          = isset( $this->props['background_mask_horizontal_offset_3'] ) ? $this->props['background_mask_horizontal_offset_3'] : '';
		$background_mask_vertical_offset_1            = isset( $this->props['background_mask_vertical_offset_1'] ) ? $this->props['background_mask_vertical_offset_1'] : '';
		$background_mask_vertical_offset_2            = isset( $this->props['background_mask_vertical_offset_2'] ) ? $this->props['background_mask_vertical_offset_2'] : '';
		$background_mask_vertical_offset_3            = isset( $this->props['background_mask_vertical_offset_3'] ) ? $this->props['background_mask_vertical_offset_3'] : '';
		$background_mask_blend_mode_1                 = isset( $this->props['background_mask_blend_mode_1'] ) ? $this->props['background_mask_blend_mode_1'] : '';
		$background_mask_blend_mode_2                 = isset( $this->props['background_mask_blend_mode_2'] ) ? $this->props['background_mask_blend_mode_2'] : '';
		$background_mask_blend_mode_3                 = isset( $this->props['background_mask_blend_mode_3'] ) ? $this->props['background_mask_blend_mode_3'] : '';
		$background_video_mp4_1                       = isset( $this->props['background_video_mp4_1'] ) ? $this->props['background_video_mp4_1'] : '';
		$background_video_mp4_2                       = isset( $this->props['background_video_mp4_2'] ) ? $this->props['background_video_mp4_2'] : '';
		$background_video_mp4_3                       = isset( $this->props['background_video_mp4_3'] ) ? $this->props['background_video_mp4_3'] : '';
		$background_video_webm_1                      = isset( $this->props['background_video_webm_1'] ) ? $this->props['background_video_webm_1'] : '';
		$background_video_webm_2                      = isset( $this->props['background_video_webm_2'] ) ? $this->props['background_video_webm_2'] : '';
		$background_video_webm_3                      = isset( $this->props['background_video_webm_3'] ) ? $this->props['background_video_webm_3'] : '';
		$background_video_width_1                     = isset( $this->props['background_video_width_1'] ) ? $this->props['background_video_width_1'] : '';
		$background_video_width_2                     = isset( $this->props['background_video_width_2'] ) ? $this->props['background_video_width_2'] : '';
		$background_video_width_3                     = isset( $this->props['background_video_width_3'] ) ? $this->props['background_video_width_3'] : '';
		$background_video_height_1                    = isset( $this->props['background_video_height_1'] ) ? $this->props['background_video_height_1'] : '';
		$background_video_height_2                    = isset( $this->props['background_video_height_2'] ) ? $this->props['background_video_height_2'] : '';
		$background_video_height_3                    = isset( $this->props['background_video_height_3'] ) ? $this->props['background_video_height_3'] : '';
		$allow_player_pause_1                         = isset( $this->props['allow_player_pause_1'] ) ? $this->props['allow_player_pause_1'] : '';
		$allow_player_pause_2                         = isset( $this->props['allow_player_pause_2'] ) ? $this->props['allow_player_pause_2'] : '';
		$allow_player_pause_3                         = isset( $this->props['allow_player_pause_3'] ) ? $this->props['allow_player_pause_3'] : '';
		$background_video_pause_outside_viewport_1    = isset( $this->props['background_video_pause_outside_viewport_1'] ) ? $this->props['background_video_pause_outside_viewport_1'] : '';
		$background_video_pause_outside_viewport_2    = isset( $this->props['background_video_pause_outside_viewport_2'] ) ? $this->props['background_video_pause_outside_viewport_2'] : '';
		$background_video_pause_outside_viewport_3    = isset( $this->props['background_video_pause_outside_viewport_3'] ) ? $this->props['background_video_pause_outside_viewport_3'] : '';
		$prev_background_color                        = $this->props['prev_background_color'];
		$next_background_color                        = $this->props['next_background_color'];

		$is_background_responsive = et_pb_responsive_options()->is_responsive_enabled( $this->props, 'background' );

		global $et_pb_rendering_specialty_section;

		// Check Background Image.
		$background_image = $this->props['background_image'];
		if ( '' === $background_image && $is_background_responsive ) {
			$background_image_tablet = et_pb_responsive_options()->get_inheritance_background_value( $this->props, 'background_image', 'tablet' );
			$background_image_phone  = et_pb_responsive_options()->get_inheritance_background_value( $this->props, 'background_image', 'phone' );
			$background_image        = '' !== $background_image_tablet ? $background_image_tablet : $background_image_phone;
		}

		// Background Color.
		$background_color        = et_pb_responsive_options()->get_inheritance_background_value( $this->props, 'background_color', 'desktop' );
		$background_color_tablet = '';
		$background_color_phone  = '';

		$processed_background_color        = $this->is_initial_background_color() ? 'inherit' : $background_color;
		$processed_background_color_tablet = '';
		$processed_background_color_phone  = '';

		if ( $is_background_responsive ) {
			$background_color_tablet = et_pb_responsive_options()->get_inheritance_background_value( $this->props, 'background_color', 'tablet' );
			$background_color_phone  = et_pb_responsive_options()->get_inheritance_background_value( $this->props, 'background_color', 'phone' );

			$processed_background_color_tablet = $this->is_initial_background_color( 'tablet' ) ? 'inherit' : $background_color_tablet;
			$processed_background_color_phone  = $this->is_initial_background_color( 'phone' ) ? 'inherit' : $background_color_phone;
		}

		if ( '' !== $global_module ) {
			$global_content = et_pb_load_global_module( $global_module, '', $prev_background_color, $next_background_color );

			if ( '' !== $global_content ) {
				return do_shortcode( et_pb_fix_shortcodes( wpautop( $global_content ) ) );
			}
		}

		$gutter_class      = '';
		$gutter_hover_data = '';

		if ( 'on' === $specialty ) {
			global $et_pb_all_column_settings, $et_pb_rendering_column_content, $et_pb_rendering_column_content_row;

			$et_pb_all_column_settings_backup = $et_pb_all_column_settings;

			$et_pb_all_column_settings = ! isset( $et_pb_all_column_settings ) ? array() : $et_pb_all_column_settings;

			if ( 'on' === $make_equal ) {
				$this->add_classname( 'et_pb_equal_columns' );
			}

			if ( 'on' === $use_custom_gutter && '' !== $gutter_width ) {
				$gutter_width  = '0' === $gutter_width ? '1' : $gutter_width; // set the gutter to 1 if 0 entered by user.
				$gutter_class .= ' et_pb_gutters' . $gutter_width;

				if ( et_builder_is_hover_enabled( 'gutter_width', $this->props ) && ! empty( $gutter_width_hover ) && $gutter_width !== $gutter_width_hover ) {
					$gutter_class .= ' et_pb_gutter_hover';

					$gutter_hover_data = sprintf(
						' data-original_gutter="%1$s" data-hover_gutter="%2$s"',
						esc_attr( $gutter_width ),
						esc_attr( $gutter_width_hover )
					);
				}
			}

			// Column hover backgrounds.
			$column_hover_backgrounds  = array();
			$column_sticky_backgrounds = array();

			for ( $i = 0; $i <= 3; $i ++ ) {
				$column_hover_backgrounds = array_merge(
					$column_hover_backgrounds,
					array(
						"column_{$i}_color_hover"         => $hover->get_value( "background_color_{$i}", $this->props, false ),
						"column_{$i}_color_hover_enabled" => $hover->is_enabled( "background_color_{$i}", $this->props ),
					)
				);

				$column_sticky_backgrounds = array_merge(
					$column_sticky_backgrounds,
					array(
						"column_{$i}_color_sticky"         => $sticky->get_value( "background_color_{$i}", $this->props, false ),
						"column_{$i}_color_sticky_enabled" => $sticky->is_enabled( "background_color_{$i}", $this->props ),
					)
				);
			}

			$et_pb_column_backgrounds = array(
				array(
					'color'                   => $background_color_1,
					'color_hover'             => $column_hover_backgrounds['column_1_color_hover'],
					'color_hover_enabled'     => $column_hover_backgrounds['column_1_color_hover_enabled'],
					'color_sticky'            => $column_sticky_backgrounds['column_1_color_sticky'],
					'color_sticky_enabled'    => $column_sticky_backgrounds['column_1_color_sticky_enabled'],
					'image'                   => $bg_img_1,
					'image_size'              => $background_size_1,
					'image_width'             => $background_image_width_1,
					'image_height'            => $background_image_height_1,
					'image_position'          => $background_position_1,
					'image_horizontal_offset' => $background_horizontal_offset_1,
					'image_vertical_offset'   => $background_vertical_offset_1,
					'image_repeat'            => $background_repeat_1,
					'image_blend'             => $background_blend_1,
				),
				array(
					'color'                   => $background_color_2,
					'color_hover'             => $column_hover_backgrounds['column_2_color_hover'],
					'color_hover_enabled'     => $column_hover_backgrounds['column_2_color_hover_enabled'],
					'color_sticky'            => $column_sticky_backgrounds['column_2_color_sticky'],
					'color_sticky_enabled'    => $column_sticky_backgrounds['column_2_color_sticky_enabled'],
					'image'                   => $bg_img_2,
					'image_size'              => $background_size_2,
					'image_width'             => $background_image_width_2,
					'image_height'            => $background_image_height_2,
					'image_position'          => $background_position_2,
					'image_horizontal_offset' => $background_horizontal_offset_2,
					'image_vertical_offset'   => $background_vertical_offset_2,
					'image_repeat'            => $background_repeat_2,
					'image_blend'             => $background_blend_2,
				),
				array(
					'color'                   => $background_color_3,
					'color_hover'             => $column_hover_backgrounds['column_3_color_hover'],
					'color_hover_enabled'     => $column_hover_backgrounds['column_3_color_hover_enabled'],
					'color_sticky'            => $column_sticky_backgrounds['column_3_color_sticky'],
					'color_sticky_enabled'    => $column_sticky_backgrounds['column_3_color_sticky_enabled'],
					'image'                   => $bg_img_3,
					'image_size'              => $background_size_3,
					'image_width'             => $background_image_width_3,
					'image_height'            => $background_image_height_3,
					'image_position'          => $background_position_3,
					'image_horizontal_offset' => $background_horizontal_offset_3,
					'image_vertical_offset'   => $background_vertical_offset_3,
					'image_repeat'            => $background_repeat_3,
					'image_blend'             => $background_blend_3,
				),
			);

			$et_pb_column_backgrounds_gradient = array(
				array(
					'active'           => $use_background_color_gradient_1,
					'repeat'           => $background_color_gradient_repeat_1,
					'type'             => $background_color_gradient_type_1,
					'direction'        => $background_color_gradient_direction_1,
					'radial_direction' => $background_color_gradient_direction_radial_1,
					'stops'            => $background_color_gradient_stops_1,
					'unit'             => $background_color_gradient_unit_1,
					'overlays_image'   => $background_color_gradient_overlays_image_1,
					'color_start'      => $background_color_gradient_start_1,
					'color_end'        => $background_color_gradient_end_1,
					'start_position'   => $background_color_gradient_start_position_1,
					'end_position'     => $background_color_gradient_end_position_1,
				),
				array(
					'active'           => $use_background_color_gradient_2,
					'repeat'           => $background_color_gradient_repeat_2,
					'type'             => $background_color_gradient_type_2,
					'direction'        => $background_color_gradient_direction_2,
					'radial_direction' => $background_color_gradient_direction_radial_2,
					'stops'            => $background_color_gradient_stops_2,
					'unit'             => $background_color_gradient_unit_2,
					'overlays_image'   => $background_color_gradient_overlays_image_2,
					'color_start'      => $background_color_gradient_start_2,
					'color_end'        => $background_color_gradient_end_2,
					'start_position'   => $background_color_gradient_start_position_2,
					'end_position'     => $background_color_gradient_end_position_2,
				),
				array(
					'active'           => $use_background_color_gradient_3,
					'repeat'           => $background_color_gradient_repeat_3,
					'type'             => $background_color_gradient_type_3,
					'direction'        => $background_color_gradient_direction_3,
					'radial_direction' => $background_color_gradient_direction_radial_3,
					'stops'            => $background_color_gradient_stops_3,
					'unit'             => $background_color_gradient_unit_3,
					'overlays_image'   => $background_color_gradient_overlays_image_3,
					'color_start'      => $background_color_gradient_start_3,
					'color_end'        => $background_color_gradient_end_3,
					'start_position'   => $background_color_gradient_start_position_3,
					'end_position'     => $background_color_gradient_end_position_3,
				),
			);

			$et_pb_column_backgrounds_pattern = array(
				array(
					'enable_pattern_style'      => $background_enable_pattern_style_1,
					'pattern_style'             => $background_pattern_style_1,
					'pattern_color'             => $background_pattern_color_1,
					'pattern_transform'         => $background_pattern_transform_1,
					'pattern_size'              => $background_pattern_size_1,
					'pattern_width'             => $background_pattern_width_1,
					'pattern_height'            => $background_pattern_height_1,
					'pattern_repeat'            => $background_pattern_repeat_1,
					'pattern_repeat_origin'     => $background_pattern_repeat_origin_1,
					'pattern_horizontal_offset' => $background_pattern_horizontal_offset_1,
					'pattern_vertical_offset'   => $background_pattern_vertical_offset_1,
					'pattern_blend_mode'        => $background_pattern_blend_mode_1,
				),
				array(
					'enable_pattern_style'      => $background_enable_pattern_style_2,
					'pattern_style'             => $background_pattern_style_2,
					'pattern_color'             => $background_pattern_color_2,
					'pattern_transform'         => $background_pattern_transform_2,
					'pattern_size'              => $background_pattern_size_2,
					'pattern_width'             => $background_pattern_width_2,
					'pattern_height'            => $background_pattern_height_2,
					'pattern_repeat'            => $background_pattern_repeat_2,
					'pattern_repeat_origin'     => $background_pattern_repeat_origin_2,
					'pattern_horizontal_offset' => $background_pattern_horizontal_offset_2,
					'pattern_vertical_offset'   => $background_pattern_vertical_offset_2,
					'pattern_blend_mode'        => $background_pattern_blend_mode_2,
				),
				array(
					'enable_pattern_style'      => $background_enable_pattern_style_3,
					'pattern_style'             => $background_pattern_style_3,
					'pattern_color'             => $background_pattern_color_3,
					'pattern_transform'         => $background_pattern_transform_3,
					'pattern_size'              => $background_pattern_size_3,
					'pattern_width'             => $background_pattern_width_3,
					'pattern_height'            => $background_pattern_height_3,
					'pattern_repeat'            => $background_pattern_repeat_3,
					'pattern_repeat_origin'     => $background_pattern_repeat_origin_3,
					'pattern_horizontal_offset' => $background_pattern_horizontal_offset_3,
					'pattern_vertical_offset'   => $background_pattern_vertical_offset_3,
					'pattern_blend_mode'        => $background_pattern_blend_mode_3,
				),
			);

			$et_pb_column_backgrounds_mask = array(
				array(
					'enable_mask_style'      => $background_enable_mask_style_1,
					'mask_style'             => $background_mask_style_1,
					'mask_color'             => $background_mask_color_1,
					'mask_transform'         => $background_mask_transform_1,
					'mask_aspect_ratio'      => $background_mask_aspect_ratio_1,
					'mask_size'              => $background_mask_size_1,
					'mask_width'             => $background_mask_width_1,
					'mask_height'            => $background_mask_height_1,
					'mask_position'          => $background_mask_position_1,
					'mask_horizontal_offset' => $background_mask_horizontal_offset_1,
					'mask_vertical_offset'   => $background_mask_vertical_offset_1,
					'mask_blend_mode'        => $background_mask_blend_mode_1,
				),
				array(
					'enable_mask_style'      => $background_enable_mask_style_2,
					'mask_style'             => $background_mask_style_2,
					'mask_color'             => $background_mask_color_2,
					'mask_transform'         => $background_mask_transform_2,
					'mask_aspect_ratio'      => $background_mask_aspect_ratio_2,
					'mask_size'              => $background_mask_size_2,
					'mask_width'             => $background_mask_width_2,
					'mask_height'            => $background_mask_height_2,
					'mask_position'          => $background_mask_position_2,
					'mask_horizontal_offset' => $background_mask_horizontal_offset_2,
					'mask_vertical_offset'   => $background_mask_vertical_offset_2,
					'mask_blend_mode'        => $background_mask_blend_mode_2,
				),
				array(
					'enable_mask_style'      => $background_enable_mask_style_3,
					'mask_style'             => $background_mask_style_3,
					'mask_color'             => $background_mask_color_3,
					'mask_transform'         => $background_mask_transform_3,
					'mask_aspect_ratio'      => $background_mask_aspect_ratio_3,
					'mask_size'              => $background_mask_size_3,
					'mask_width'             => $background_mask_width_3,
					'mask_height'            => $background_mask_height_3,
					'mask_position'          => $background_mask_position_3,
					'mask_horizontal_offset' => $background_mask_horizontal_offset_3,
					'mask_vertical_offset'   => $background_mask_vertical_offset_3,
					'mask_blend_mode'        => $background_mask_blend_mode_3,
				),
			);

			$et_pb_column_backgrounds_video = array(
				array(
					'background_video_mp4'         => $background_video_mp4_1,
					'background_video_webm'        => $background_video_webm_1,
					'background_video_width'       => $background_video_width_1,
					'background_video_height'      => $background_video_height_1,
					'background_video_allow_pause' => $allow_player_pause_1,
					'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_1,
				),
				array(
					'background_video_mp4'         => $background_video_mp4_2,
					'background_video_webm'        => $background_video_webm_2,
					'background_video_width'       => $background_video_width_2,
					'background_video_height'      => $background_video_height_2,
					'background_video_allow_pause' => $allow_player_pause_2,
					'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_2,
				),
				array(
					'background_video_mp4'         => $background_video_mp4_3,
					'background_video_webm'        => $background_video_webm_3,
					'background_video_width'       => $background_video_width_3,
					'background_video_height'      => $background_video_height_3,
					'background_video_allow_pause' => $allow_player_pause_3,
					'background_video_pause_outside_viewport' => $background_video_pause_outside_viewport_3,
				),
			);

			// Column hover paddings.
			$column_hover_paddings  = array();
			$column_sticky_paddings = array();

			for ( $i = 0; $i <= 3; $i++ ) {
				$column_hover_paddings = array_merge(
					$column_hover_paddings,
					array(
						"column_{$i}_padding_hover_enabled" => $hover->is_enabled( "padding_{$i}", $this->props ),
						"column_{$i}_padding_top"    => $hover->get_compose_value( "padding_top_{$i}", "padding_{$i}", $this->props ),
						"column_{$i}_padding_right"  => $hover->get_compose_value( "padding_right_{$i}", "padding_{$i}", $this->props ),
						"column_{$i}_padding_bottom" => $hover->get_compose_value( "padding_bottom_{$i}", "padding_{$i}", $this->props ),
						"column_{$i}_padding_left"   => $hover->get_compose_value( "padding_left_{$i}", "padding_{$i}", $this->props ),
					)
				);

				$sticky_padding_values  = explode( '|', $sticky->get_value( "padding_{$i}", $this->props, '|||' ) );
				$column_sticky_paddings = array_merge(
					$column_sticky_paddings,
					array(
						"column_{$i}_padding_sticky_enabled" => $sticky->is_enabled( "padding_{$i}", $this->props ),
						"column_{$i}_padding_top"    => $sticky_padding_values[0],
						"column_{$i}_padding_right"  => $sticky_padding_values[1],
						"column_{$i}_padding_bottom" => $sticky_padding_values[2],
						"column_{$i}_padding_left"   => $sticky_padding_values[3],
					)
				);
			}

			$et_pb_column_paddings = array(
				array(
					'padding-top'            => $padding_top_1,
					'padding-right'          => $padding_right_1,
					'padding-bottom'         => $padding_bottom_1,
					'padding-left'           => $padding_left_1,
					'padding-hover-enabled'  => $column_hover_paddings['column_1_padding_hover_enabled'],
					'padding-top-hover'      => $column_hover_paddings['column_1_padding_top'],
					'padding-right-hover'    => $column_hover_paddings['column_1_padding_right'],
					'padding-bottom-hover'   => $column_hover_paddings['column_1_padding_bottom'],
					'padding-left-hover'     => $column_hover_paddings['column_1_padding_left'],
					'padding-sticky-enabled' => $column_sticky_paddings['column_1_padding_sticky_enabled'],
					'padding-top-sticky'     => $column_sticky_paddings['column_1_padding_top'],
					'padding-right-sticky'   => $column_sticky_paddings['column_1_padding_right'],
					'padding-bottom-sticky'  => $column_sticky_paddings['column_1_padding_bottom'],
					'padding-left-sticky'    => $column_sticky_paddings['column_1_padding_left'],
				),
				array(
					'padding-top'            => $padding_top_2,
					'padding-right'          => $padding_right_2,
					'padding-bottom'         => $padding_bottom_2,
					'padding-left'           => $padding_left_2,
					'padding-hover-enabled'  => $column_hover_paddings['column_2_padding_hover_enabled'],
					'padding-top-hover'      => $column_hover_paddings['column_2_padding_top'],
					'padding-right-hover'    => $column_hover_paddings['column_2_padding_right'],
					'padding-bottom-hover'   => $column_hover_paddings['column_2_padding_bottom'],
					'padding-left-hover'     => $column_hover_paddings['column_2_padding_left'],
					'padding-sticky-enabled' => $column_sticky_paddings['column_2_padding_sticky_enabled'],
					'padding-top-sticky'     => $column_sticky_paddings['column_2_padding_top'],
					'padding-right-sticky'   => $column_sticky_paddings['column_2_padding_right'],
					'padding-bottom-sticky'  => $column_sticky_paddings['column_2_padding_bottom'],
					'padding-left-sticky'    => $column_sticky_paddings['column_2_padding_left'],
				),
				array(
					'padding-top'            => $padding_top_3,
					'padding-right'          => $padding_right_3,
					'padding-bottom'         => $padding_bottom_3,
					'padding-left'           => $padding_left_3,
					'padding-hover-enabled'  => $column_hover_paddings['column_3_padding_hover_enabled'],
					'padding-top-hover'      => $column_hover_paddings['column_3_padding_top'],
					'padding-right-hover'    => $column_hover_paddings['column_3_padding_right'],
					'padding-bottom-hover'   => $column_hover_paddings['column_3_padding_bottom'],
					'padding-left-hover'     => $column_hover_paddings['column_3_padding_left'],
					'padding-sticky-enabled' => $column_sticky_paddings['column_3_padding_sticky_enabled'],
					'padding-top-sticky'     => $column_sticky_paddings['column_3_padding_top'],
					'padding-right-sticky'   => $column_sticky_paddings['column_3_padding_right'],
					'padding-bottom-sticky'  => $column_sticky_paddings['column_3_padding_bottom'],
					'padding-left-sticky'    => $column_sticky_paddings['column_3_padding_left'],
				),
			);

			$et_pb_column_paddings_mobile = array(
				array(
					'tablet'      => explode( '|', $padding_1_tablet ),
					'phone'       => explode( '|', $padding_1_phone ),
					'last_edited' => $padding_1_last_edited,
				),
				array(
					'tablet'      => explode( '|', $padding_2_tablet ),
					'phone'       => explode( '|', $padding_2_phone ),
					'last_edited' => $padding_2_last_edited,
				),
				array(
					'tablet'      => explode( '|', $padding_3_tablet ),
					'phone'       => explode( '|', $padding_3_phone ),
					'last_edited' => $padding_3_last_edited,
				),
			);

			$et_pb_column_parallax = array(
				array( $parallax_1, $parallax_method_1 ),
				array( $parallax_2, $parallax_method_2 ),
				array( $parallax_3, $parallax_method_3 ),
			);

			$et_pb_column_css = array(
				'css_class'                => array( $module_class_1, $module_class_2, $module_class_3 ),
				'css_id'                   => array( $module_id_1, $module_id_2, $module_id_3 ),
				'custom_css_before'        => array( $custom_css_before_1, $custom_css_before_2, $custom_css_before_3 ),
				'custom_css_main'          => array( $custom_css_main_1, $custom_css_main_2, $custom_css_main_3 ),
				'custom_css_after'         => array( $custom_css_after_1, $custom_css_after_2, $custom_css_after_3 ),
				'custom_css_before_hover'  => array( $custom_css_before_1_hover, $custom_css_before_2_hover, $custom_css_before_3_hover ),
				'custom_css_main_hover'    => array( $custom_css_main_1_hover, $custom_css_main_2_hover, $custom_css_main_3_hover ),
				'custom_css_after_hover'   => array( $custom_css_after_1_hover, $custom_css_after_2_hover, $custom_css_after_3_hover ),
				'custom_css_before_sticky' => array( $custom_css_before_1_sticky, $custom_css_before_2_sticky, $custom_css_before_3_sticky ),
				'custom_css_main_sticky'   => array( $custom_css_main_1_sticky, $custom_css_main_2_sticky, $custom_css_main_3_sticky ),
				'custom_css_after_sticky'  => array( $custom_css_after_1_sticky, $custom_css_after_2_sticky, $custom_css_after_3_sticky ),
			);

			$internal_columns_settings_array = array(
				'keep_column_padding_mobile'        => 'on',
				'et_pb_column_backgrounds'          => $et_pb_column_backgrounds,
				'et_pb_column_backgrounds_gradient' => $et_pb_column_backgrounds_gradient,
				'et_pb_column_backgrounds_pattern'  => $et_pb_column_backgrounds_pattern,
				'et_pb_column_backgrounds_mask'     => $et_pb_column_backgrounds_mask,
				'et_pb_column_backgrounds_video'    => $et_pb_column_backgrounds_video,
				'et_pb_column_parallax'             => $et_pb_column_parallax,
				'et_pb_columns_counter'             => 0,
				'et_pb_column_paddings'             => $et_pb_column_paddings,
				'et_pb_column_paddings_mobile'      => $et_pb_column_paddings_mobile,
				'et_pb_column_css'                  => $et_pb_column_css,
			);

			$current_row_position = $et_pb_rendering_column_content ? 'internal_row' : 'regular_row';

			$et_pb_all_column_settings[ $current_row_position ] = $internal_columns_settings_array;

			$et_pb_rendering_specialty_section = true;

			if ( $et_pb_rendering_column_content ) {
				$et_pb_rendering_column_content_row = true;
			}
		} else {
			$et_pb_rendering_specialty_section = false;
		}

		$background_video = $this->video_background();
		$parallax_image   = $this->get_parallax_image_background();

		// Background Color.
		$background_color_values = array(
			'desktop' => 'rgba(255,255,255,0)' !== $processed_background_color ? esc_html( $processed_background_color ) : '',
			'tablet'  => 'rgba(255,255,255,0)' !== $processed_background_color_tablet ? esc_html( $processed_background_color_tablet ) : '',
			'phone'   => 'rgba(255,255,255,0)' !== $processed_background_color_phone ? esc_html( $processed_background_color_phone ) : '',
		);
		et_pb_responsive_options()->generate_responsive_css( $background_color_values, '%%order_class%%.et_pb_section', 'background-color', $function_name, ' !important;', 'color' );

		// Background hover styles.
		$hover_background_color    = $hover->get_value( 'background_color', $this->props, $background_color );
		$hover_background_selector = '%%order_class%%.et_pb_section:hover';

		if ( $hover_background_color !== $background_color ) {
			$el_style = array(
				'selector'    => $hover_background_selector,
				'declaration' => sprintf(
					'background-color:%s !important;',
					esc_attr( $hover_background_color )
				),
			);
			ET_Builder_Element::set_style( $function_name, $el_style );
		}

		// Background sticky styles.
		$sticky_background_color = $sticky->get_value( 'background_color', $this->props, $background_color );
		if ( $sticky_background_color !== $background_color ) {
			$el_style = array(
				'selector'    => $sticky->add_sticky_to_order_class(
					'%%order_class%%.et_pb_section',
					$this->is_sticky_module
				),
				'declaration' => sprintf(
					'background-color:%s !important;',
					esc_attr( $sticky_background_color )
				),
			);
			ET_Builder_Element::set_style( $function_name, $el_style );

			// Add hover style in sticky state.
			if ( $hover_background_color !== $background_color ) {
				$el_style = array(
					'selector'    => $sticky->add_sticky_to_order_class(
						$hover_background_selector,
						$this->is_sticky_module
					),
					'declaration' => sprintf(
						'background-color:%s !important;',
						esc_attr( $hover_background_color )
					),
				);
				ET_Builder_Element::set_style( $function_name, $el_style );
			}
		}

		// Transparent is default for Builder Plugin, but not for theme.
		$is_transparent_background        = $this->is_transparent_background( $background_color );
		$is_transparent_background_tablet = $this->is_transparent_background( $background_color_tablet );
		$is_transparent_background_phone  = $this->is_transparent_background( $background_color_phone );
		$is_background_color              = ( '' !== $background_color && ! $is_transparent_background ) || ( '' !== $background_color_tablet && ! $is_transparent_background_tablet ) || ( '' !== $background_color_phone && ! $is_transparent_background_phone );

		if ( ! empty( $background_video ) || $is_background_color || '' !== $background_image ) {
			$this->add_classname( 'et_pb_with_background' );
		}

		// Background UI.
		if ( 'on' === $parallax ) {
			$this->add_classname( 'et_pb_section_parallax' );
		}

		// CSS Filters.
		$this->add_classname( $this->generate_css_filters( $function_name ) );

		if ( 'on' === $inner_shadow && ! ( '' !== $background_image && 'on' === $parallax && 'off' === $parallax_method ) ) {
			$this->add_classname( 'et_pb_inner_shadow' );
		}

		if ( 'on' === $fullwidth ) {
			$this->add_classname( 'et_pb_fullwidth_section' );
		}

		if ( 'on' === $specialty ) {
			$this->add_classname( 'et_section_specialty' );
		} else {
			$this->add_classname( 'et_section_regular' );
		}

		if ( $is_transparent_background || $is_transparent_background_tablet || $is_transparent_background_phone ) {
			$this->add_classname( 'et_section_transparent' );
		}

		// Setup for SVG.
		$bottom  = '';
		$top     = '';
		$divider = ET_Builder_Module_Fields_Factory::get( 'Divider' );
		// pass section number for background color usage.
		$divider->count = $this->render_count();

		// Check if animation is enabled.
		$animation_enabled = $this->_features_manager->get(
			'section_divider_enabled',
			function() {
				return $this->is_section_divider_enabled();
			}
		);

		if ( $animation_enabled ) {
			// Divider Placement.
			foreach ( array( 'bottom', 'top' ) as $placement ) {
				// Divider Responsive.
				foreach ( array( 'desktop', 'tablet', 'phone' ) as $device ) {
					// Ensure responsive settings for style is active on tablet and phone.
					$is_desktop          = 'desktop' === $device;
					$is_responsive_style = et_pb_responsive_options()->is_responsive_enabled( $this->props, "{$placement}_divider_style" );

					// Get all responsive values if it's exist and not empty.
					$values = array();
					if ( ! $is_desktop ) {
						$values = et_pb_responsive_options()->get_any_responsive_values(
							$this->props,
							array(
								"{$placement}_divider_color"  => '',
								"{$placement}_divider_height" => '',
								"{$placement}_divider_repeat" => '',
								"{$placement}_divider_flip"   => '',
								"{$placement}_divider_arrangement" => '',
							),
							false,
							$device
						);
					}

					// Get Divider Style.
					$divider_style = $is_desktop || ! empty( $values ) ? et_pb_responsive_options()->get_any_value( $this->props, "{$placement}_divider_style" ) : '';

					if ( ! $is_desktop && $is_responsive_style ) {
						$divider_style = et_pb_responsive_options()->get_any_value( $this->props, "{$placement}_divider_style", '', true, $device );
					}

					// Check if style is not default.
					if ( '' !== $divider_style ) {
						// get an svg for using in ::before.
						$breakpoint = ! $is_desktop ? $device : '';
						$divider->process_svg( $placement, $this->props, $breakpoint, $values );

						// Get the placeholder for the bottom/top.
						if ( 'bottom' === $placement && '' === $bottom ) {
							$bottom = $divider->get_svg( 'bottom' );
						} elseif ( 'top' === $placement && '' === $top ) {
							$top = $divider->get_svg( 'top' );
						}

						// add a corresponding class.
						$this->add_classname( $divider->classes );
					}
				}
			}
		}

		// Position Options.
		if ( $multi_view->has_value( 'positioning', 'absolute' ) ) {
			$this->add_classname( 'et_pb_section--absolute' );
		}

		if ( $multi_view->has_value( 'positioning', 'fixed' ) ) {
			$this->add_classname( 'et_pb_section--fixed' );
		}

		$muti_view_attributes = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_section--absolute' => array(
						'positioning' => 'absolute',
					),
					'et_pb_section--fixed'    => array(
						'positioning' => 'fixed',
					),
				),
			)
		);

		// Remove automatically added classnames.
		$this->remove_classname( 'et_pb_module' );

		if ( self::contains( $content, array( 'et_pb_menu', 'et_pb_fullwidth_menu' ) ) ) {
			$this->add_classname( 'et_pb_section--with-menu' );
		}

		// Save module classes into variable BEFORE processing the content with `do_shortcode()`
		// Otherwise order classes messed up with internal sections if exist.
		$module_classes = $this->module_classname( $function_name );

		$output = sprintf(
			'<div%4$s class="%3$s"%8$s %11$s>
				%9$s
				%7$s
				%2$s
				%12$s
				%13$s
				%5$s
				%1$s
				%6$s
				%10$s
			</div>',
			do_shortcode( et_pb_fix_shortcodes( $content ) ), // 1
			$background_video, // 2
			$module_classes, // 3
			$this->module_id(), // 4
			( 'on' === $specialty ?
				sprintf( '<div class="et_pb_row%1$s"%2$s>', $gutter_class, et_core_esc_previously( $gutter_hover_data ) )
				: '' ), // 5
			( 'on' === $specialty ? '</div>' : '' ), // 6
			$parallax_image, // 7
			$this->get_module_data_attributes(), // 8
			et_core_esc_previously( $top ), // 9
			et_core_esc_previously( $bottom ), // 10,
			et_core_esc_previously( $muti_view_attributes ), // 11
			et_core_esc_previously( $this->background_pattern() ), // 12
			et_core_esc_previously( $this->background_mask() ) // 13
		);

		if ( 'on' === $specialty ) {
			// reset the global column settings to make sure they are not affected by internal content.
			$et_pb_all_column_settings = $et_pb_all_column_settings_backup;

			if ( $et_pb_rendering_column_content_row ) {
				$et_pb_rendering_column_content_row = false;
			}
		}

		return $output;

	}

	/**
	 * Process box shadow CSS styles of section.
	 *
	 * @since 4.10.0
	 */
	public function is_section_divider_enabled() {
		// Divider Responsive.
		foreach ( array( 'desktop', 'tablet', 'phone' ) as $device ) {
			// Get Divider Style.
			$divider_style_enabled = et_pb_responsive_options()->get_any_value( $this->props, 'top_divider_style', false )
			|| et_pb_responsive_options()->get_any_value( $this->props, 'bottom_divider_style', false )
			|| et_pb_responsive_options()->get_any_value( $this->props, 'top_divider_style', false, true, $device )
			|| et_pb_responsive_options()->get_any_value( $this->props, 'bottom_divider_style', false, true, $device );

			return $divider_style_enabled;
		}
		return false;
	}

	/**
	 * Process box shadow CSS styles of section.
	 *
	 * @param string $function_name Module slug.
	 */
	public function process_box_shadow( $function_name ) {
		/**
		 * Instance of BoxShadow module field. @var ET_Builder_Module_Field_BoxShadow $box_shadow
		 */
		$box_shadow  = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );
		$style       = $box_shadow->get_value( $this->props );
		$hover_style = $box_shadow->get_value( $this->props, array( 'hover' => true ) );

		if ( ! empty( $style ) && 'none' !== $style && false === strpos( $style, 'inset' ) ) {
			// Make section z-index higher if it has outer box shadow #4762.
			$el_style = array(
				'selector'    => '%%order_class%%',
				'declaration' => 'z-index: 10;',
			);
			self::set_style( $function_name, $el_style );
		}

		if ( ! empty( $hover_style ) && 'none' !== $hover_style && false === strpos( $hover_style, 'inset' ) ) {
			// Make section z-index higher if it has outer box shadow #4762.
			$el_style = array(
				'selector'    => '%%order_class%%:hover',
				'declaration' => 'z-index: 10;',
			);
			self::set_style( $function_name, $el_style );
		}

		parent::process_box_shadow( $function_name );
	}

	/**
	 * Box shadow back compatibility.
	 *
	 * @param string $function_name Module slug.
	 *
	 * @return string
	 */
	private function _keep_box_shadow_compatibility( $function_name ) {
		/**
		 * Instance of box shadow field factory. @var ET_Builder_Module_Field_BoxShadow $box_shadow
		 */
		$box_shadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );
		$utils      = ET_Core_Data_Utils::instance();
		$atts       = $this->props;
		$style      = $box_shadow->get_value( $atts );

		if (
			! empty( $style )
			&&
			! is_admin()
			&&
			version_compare( $utils->array_get( $atts, '_builder_version', '3.0.93' ), '3.0.94', 'lt' )
			&&
			! $box_shadow->is_inset( $box_shadow->get_value( $atts ) )
		) {
			$class = '.' . self::get_module_order_class( $function_name );

			return sprintf(
				'<style type="text/css">%1$s</style>',
				sprintf( '%1$s { z-index: 11; %2$s }', esc_html( $class ), esc_html( $style ) )
			);
		}

		return '';
	}
}
new ET_Builder_Section();

/**
 * Class used to process row element(module).
 *
 * @since 4.6.2
 */
class ET_Builder_Row extends ET_Builder_Structure_Element {
	/**
	 * Sets up the element's properties.
	 */
	public function init() {
		$this->name            = esc_html__( 'Row', 'et_builder' );
		$this->plural          = esc_html__( 'Rows', 'et_builder' );
		$this->slug            = 'et_pb_row';
		$this->vb_support      = 'on';
		$this->child_slug      = 'et_pb_column';
		$this->child_item_text = esc_html__( 'Column', 'et_builder' );

		$this->advanced_fields = array(
			'background'      => array(
				'use_background_color'          => true,
				'use_background_image'          => true,
				'use_background_color_gradient' => true,
				'use_background_video'          => true,
				'use_background_pattern'        => true,
				'use_background_mask'           => true,
				'options'                       => array(
					'background_color'   => array(
						'default' => '',
						'hover'   => 'tabs',
					),
					'allow_player_pause' => array(
						'default_on_front' => 'off',
					),
					'parallax'           => array(
						'default_on_front' => 'off',
					),
					'parallax_method'    => array(
						'default_on_front' => 'on',
					),
				),
			),
			'max_width'       => array(
				'css'             => array(
					'module_alignment' => '%%order_class%%.et_pb_row',
				),
				'options'         => array(
					'width'            => array(
						'default' => '80%',
					),
					'max_width'        => array(
						'default'        => '1080px',
						'range_settings' => array(
							'min'  => 0,
							'max'  => 2560,
							'step' => 1,
						),
					),
					'module_alignment' => array(
						'label'          => esc_html__( 'Row Alignment', 'et_builder' ),
						'mobile_options' => true,
						'description'    => esc_html__( 'Rows can be aligned to the left, right or center. By default, rows are centered within their parent section.', 'et_builder' ),
						'default_sticky' => 'center',
					),
				),
				'toggle_slug'     => 'width',
				'toggle_title'    => esc_html__( 'Alignment', 'et_builder' ),
				'toggle_priority' => 50,
			),
			'margin_padding'  => array(
				'css' => array(
					'main'      => '%%order_class%%.et_pb_row',
					'important' => 'all',
				),
			),
			'fonts'           => false,
			'text'            => false,
			'button'          => false,
			'position_fields' => array(
				'default' => 'relative',
			),
			'z_index'         => array(
				'important' => true,
			),
		);

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'column_structure' => array(
						'title'       => esc_html__( 'Column Structure', 'et_builder' ),
						'priority'    => 1,
						'always_open' => true,
					),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'width' => array(
						'title'    => et_builder_i18n( 'Sizing' ),
						'priority' => 65,
					),
				),
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'R9ds7bEaHE8',
				'name' => esc_html__( 'An introduction to Rows', 'et_builder' ),
			),
		);
	}

	/**
	 * Get the settings fields data for this element.
	 *
	 * @return array|array[]
	 */
	public function get_fields() {
		$fields = array(
			'column_structure'           => array(
				'label'       => esc_html__( 'Column Structure', 'et_builder' ),
				'description' => esc_html__( 'Here you can choose the Column Structure for this Row.', 'et_builder' ),
				'type'        => 'column-structure',
				'default'     => '4_4',
				'options'     => array(
					'4_4'                     => et_pb_get_column_svg( '4_4' ),
					'1_2,1_2'                 => et_pb_get_column_svg( '1_2,1_2' ),
					'1_3,1_3,1_3'             => et_pb_get_column_svg( '1_3,1_3,1_3' ),
					'1_4,1_4,1_4,1_4'         => et_pb_get_column_svg( '1_4,1_4,1_4,1_4' ),
					'1_4,1_4,1_4,1_4'         => et_pb_get_column_svg( '1_4,1_4,1_4,1_4' ),
					'1_5,1_5,1_5,1_5,1_5'     => et_pb_get_column_svg( '1_5,1_5,1_5,1_5,1_5' ),
					'1_6,1_6,1_6,1_6,1_6,1_6' => et_pb_get_column_svg( '1_6,1_6,1_6,1_6,1_6,1_6' ),
					'2_5,3_5'                 => et_pb_get_column_svg( '2_5,3_5' ),
					'3_5,2_5'                 => et_pb_get_column_svg( '3_5,2_5' ),
					'1_3,2_3'                 => et_pb_get_column_svg( '1_3,2_3' ),
					'2_3,1_3'                 => et_pb_get_column_svg( '2_3,1_3' ),
					'1_4,3_4'                 => et_pb_get_column_svg( '1_4,3_4' ),
					'3_4,1_4'                 => et_pb_get_column_svg( '3_4,1_4' ),
					'1_4,1_2,1_4'             => et_pb_get_column_svg( '1_4,1_2,1_4' ),
					'1_5,3_5,1_5'             => et_pb_get_column_svg( '1_5,3_5,1_5' ),
					'1_4,1_4,1_2'             => et_pb_get_column_svg( '1_4,1_4,1_2' ),
					'1_2,1_4,1_4'             => et_pb_get_column_svg( '1_2,1_4,1_4' ),
					'1_5,1_5,3_5'             => et_pb_get_column_svg( '1_5,1_5,3_5' ),
					'3_5,1_5,1_5'             => et_pb_get_column_svg( '3_5,1_5,1_5' ),
					'1_6,1_6,1_6,1_2'         => et_pb_get_column_svg( '1_6,1_6,1_6,1_2' ),
					'1_2,1_6,1_6,1_6'         => et_pb_get_column_svg( '1_2,1_6,1_6,1_6' ),
				),
				'toggle_slug' => 'column_structure',
			),
			'use_custom_gutter'          => array(
				'label'           => esc_html__( 'Use Custom Gutter Width', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default'         => 'off',
				'affects'         => array(
					'gutter_width',
				),
				'description'     => esc_html__( 'Enable this option to define custom gutter width for this row.', 'et_builder' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
			),
			'gutter_width'               => array(
				'label'            => esc_html__( 'Gutter Width', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'range_settings'   => array(
					'min'       => 1,
					'max'       => 4,
					'step'      => 1,
					'min_limit' => 1,
					'max_limit' => 4,
				),
				'depends_show_if'  => 'on',
				'description'      => esc_html__( 'Adjust the spacing between each column in this row.', 'et_builder' ),
				'validate_unit'    => false,
				'fixed_range'      => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'width',
				'default_on_front' => (string) et_get_option( 'gutter_width', '3' ),
				'hover'            => 'tabs',
			),
			'padding_mobile'             => array(
				'label'            => esc_html__( 'Keep Custom Padding on Mobile', 'et_builder' ),
				'type'             => 'skip', // Remaining attribute for backward compatibility.
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'margin_padding',
				'default_on_front' => '',
			),
			'make_equal'                 => array(
				'label'           => esc_html__( 'Equalize Column Heights', 'et_builder' ),
				'description'     => esc_html__( 'Equalizing column heights will force all columns to assume the height of the tallest column in the row. All columns will have the same height, keeping their appearance uniform.', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default'         => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
			),
			'column_padding_mobile'      => array(
				'label'            => esc_html__( 'Keep Column Padding on Mobile', 'et_builder' ),
				'tab_slug'         => 'advanced',
				'type'             => 'skip', // Remaining attribute for backward compatibility.
				'default_on_front' => '',
			),
			'custom_padding_last_edited' => array(
				'type'     => 'skip',
				'tab_slug' => 'advanced',
			),
			'__video_background'         => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Helper_ResponsiveOptions', 'get_video_background' ),
				'computed_depends_on' => array(
					'background_video_mp4',
					'background_video_webm',
					'background_video_width',
					'background_video_height',
				),
				'computed_minimum'    => array(
					'background_video_mp4',
					'background_video_webm',
				),
			),
		);

		$column_fields = $this->get_column_fields(
			6,
			array(
				'background_color'                         => array(),
				'bg_img'                                   => array(),
				'padding_top_bottom_link'                  => array(),
				'padding_left_right_link'                  => array(),
				'parallax'                                 => array(
					'default_on_front' => 'off',
				),
				'parallax_method'                          => array(
					'default_on_front' => 'on',
				),
				'background_size'                          => array(),
				'background_image_width'                   => array(),
				'background_image_height'                  => array(),
				'background_position'                      => array(),
				'background_horizontal_offset'             => array(),
				'background_vertical_offset'               => array(),
				'background_repeat'                        => array(),
				'background_blend'                         => array(),
				'use_background_color_gradient'            => array(),
				'background_color_gradient_repeat'         => array(),
				'background_color_gradient_type'           => array(),
				'background_color_gradient_direction'      => array(),
				'background_color_gradient_direction_radial' => array(),
				'background_color_gradient_stops'          => array(),
				'background_color_gradient_unit'           => array(),
				'background_color_gradient_overlays_image' => array(),
				'background_color_gradient_start'          => array(),
				'background_color_gradient_end'            => array(),
				'background_color_gradient_start_position' => array(),
				'background_color_gradient_end_position'   => array(),
				'background_color_gradient_overlays_image' => array(),
				'background_enable_pattern_style'          => array(),
				'background_pattern_style'                 => array(),
				'background_pattern_color'                 => array(),
				'background_pattern_transform'             => array(),
				'background_pattern_size'                  => array(),
				'background_pattern_width'                 => array(),
				'background_pattern_height'                => array(),
				'background_pattern_repeat'                => array(),
				'background_pattern_repeat_origin'         => array(),
				'background_pattern_horizontal_offset'     => array(),
				'background_pattern_vertical_offset'       => array(),
				'background_pattern_blend_mode'            => array(),
				'background_enable_mask_style'             => array(),
				'background_mask_style'                    => array(),
				'background_mask_color'                    => array(),
				'background_mask_transform'                => array(),
				'background_mask_aspect_ratio'             => array(),
				'background_mask_size'                     => array(),
				'background_mask_width'                    => array(),
				'background_mask_height'                   => array(),
				'background_mask_position'                 => array(),
				'background_mask_horizontal_offset'        => array(),
				'background_mask_vertical_offset'          => array(),
				'background_mask_blend_mode'               => array(),
				'background_video_mp4'                     => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'background_video_webm'                    => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'background_video_width'                   => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'background_video_height'                  => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'allow_player_pause'                       => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'background_video_pause_outside_viewport'  => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'__video_background'                       => array(
					'type'                => 'computed',
					'computed_callback'   => array(
						'ET_Builder_Column',
						'get_column_video_background',
					),
					'computed_depends_on' => array(
						'background_video_mp4',
						'background_video_webm',
						'background_video_width',
						'background_video_height',
					),
					'computed_minimum'    => array(
						'background_video_mp4',
						'background_video_webm',
					),
				),
				'padding_top'                              => array( 'tab_slug' => 'advanced' ),
				'padding_right'                            => array( 'tab_slug' => 'advanced' ),
				'padding_bottom'                           => array( 'tab_slug' => 'advanced' ),
				'padding_left'                             => array( 'tab_slug' => 'advanced' ),
				'padding_top_bottom_link'                  => array( 'tab_slug' => 'advanced' ),
				'padding_left_right_link'                  => array( 'tab_slug' => 'advanced' ),
				'padding_%column_index%_tablet'            => array(
					'has_custom_index_location' => true,
					'tab_slug'                  => 'advanced',
				),
				'padding_%column_index%_phone'             => array(
					'has_custom_index_location' => true,
					'tab_slug'                  => 'advanced',
				),
				'padding_%column_index%_last_edited'       => array(
					'has_custom_index_location' => true,
					'tab_slug'                  => 'advanced',
				),
				'module_id'                                => array( 'tab_slug' => 'custom_css' ),
				'module_class'                             => array( 'tab_slug' => 'custom_css' ),
				'custom_css_before'                        => array( 'tab_slug' => 'custom_css' ),
				'custom_css_main'                          => array( 'tab_slug' => 'custom_css' ),
				'custom_css_after'                         => array( 'tab_slug' => 'custom_css' ),
			)
		);

		return array_merge( $fields, $column_fields );
	}

	/**
	 * Get CSS fields transition of row.
	 *
	 * @since 3.23 Add form field options group and background image on the fields list.
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		for ( $i = 1; $i <= 6; $i ++ ) {
			$selector                          = "%%order_class%% > .et_pb_column:nth-child({$i})";
			$fields[ "background_color_{$i}" ] = array( 'background-color' => $selector );
			$fields[ "padding_{$i}" ]          = array( 'padding' => $selector );
		}

		return $fields;
	}

	/**
	 * Generates the structure module's HTML output based on {@see ET_Builder_Row::$props}.
	 *
	 * @param array  $atts       List of unprocessed attributes.
	 * @param mixed  $content     Content being processed.
	 * @param string $function_name Slug of module that is used for rendering output.
	 *
	 * @return string The module's HTML output.
	 */
	public function render( $atts, $content, $function_name ) {
		$custom_padding             = $this->props['custom_padding'];
		$custom_padding_tablet      = $this->props['custom_padding_tablet'];
		$custom_padding_phone       = $this->props['custom_padding_phone'];
		$custom_padding_last_edited = $this->props['custom_padding_last_edited'];
		$column_padding_mobile      = $this->props['column_padding_mobile'];
		$make_equal                 = $this->props['make_equal'];
		$padding_mobile             = $this->props['padding_mobile'];
		$gutter_width               = $this->props['gutter_width'];
		$gutter_width_hover         = $this->get_hover_value( 'gutter_width' );
		$global_module              = $this->props['global_module'];
		$use_custom_gutter          = $this->props['use_custom_gutter'];

		$hover = et_pb_hover_options();

		global $et_pb_all_column_settings, $et_pb_rendering_column_content, $et_pb_rendering_column_content_row;

		$et_pb_all_column_settings = ! isset( $et_pb_all_column_settings ) ? array() : $et_pb_all_column_settings;

		$et_pb_all_column_settings_backup = $et_pb_all_column_settings;

		$keep_column_padding_mobile = $column_padding_mobile;

		if ( '' !== $global_module ) {
			$global_content = et_pb_load_global_module( $global_module, $function_name );

			if ( '' !== $global_content ) {
				return do_shortcode( et_pb_fix_shortcodes( wpautop( $global_content ) ) );
			}
		}

		$custom_padding_responsive_active = et_pb_get_responsive_status( $custom_padding_last_edited );

		$padding_mobile_values = $custom_padding_responsive_active ? array(
			'tablet' => explode( '|', $custom_padding_tablet ),
			'phone'  => explode( '|', $custom_padding_phone ),
		) : array(
			'tablet' => false,
			'phone'  => false,
		);

		$internal_columns_settings_array = array(
			'keep_column_padding_mobile' => $keep_column_padding_mobile,
			'et_pb_columns_counter'      => 0,
		);

		$current_row_position = $et_pb_rendering_column_content ? 'internal_row' : 'regular_row';

		$et_pb_all_column_settings[ $current_row_position ] = $internal_columns_settings_array;

		if ( $et_pb_rendering_column_content ) {
			$et_pb_rendering_column_content_row = true;
		}

		if ( 'on' === $make_equal ) {
			$this->add_classname( 'et_pb_equal_columns' );
		}

		$gutter_hover_data = '';

		if ( 'on' === $use_custom_gutter && '' !== $gutter_width ) {
			$gutter_width = '0' === $gutter_width ? '1' : $gutter_width; // set the gutter width to 1 if 0 entered by user.
			$this->add_classname( 'et_pb_gutters' . $gutter_width );

			if ( et_builder_is_hover_enabled( 'gutter_width', $this->props ) && ! empty( $gutter_width_hover ) && $gutter_width !== $gutter_width_hover ) {
				$this->add_classname( 'et_pb_gutter_hover' );

				$gutter_hover_data = sprintf(
					' data-original_gutter="%1$s" data-hover_gutter="%2$s"',
					esc_attr( $gutter_width ),
					esc_attr( $gutter_width_hover )
				);
			}
		}

		$padding_values = explode( '|', $custom_padding );

		if ( ! empty( $padding_values ) ) {
			// old version of Rows support only top and bottom padding, so we need to handle it along with the full padding in the recent version.
			if ( 2 === count( $padding_values ) ) {
				$padding_settings = array(
					'top'    => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'bottom' => isset( $padding_values[1] ) ? $padding_values[1] : '',
				);
			} else {
				$padding_settings = array(
					'top'    => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'right'  => isset( $padding_values[1] ) ? $padding_values[1] : '',
					'bottom' => isset( $padding_values[2] ) ? $padding_values[2] : '',
					'left'   => isset( $padding_values[3] ) ? $padding_values[3] : '',
				);
			}

			foreach ( $padding_settings as $padding_side => $value ) {
				if ( '' !== $value ) {
					$element_style = array(
						'selector'    => '%%order_class%%.et_pb_row',
						'declaration' => sprintf(
							'padding-%1$s: %2$s;',
							esc_html( $padding_side ),
							esc_html( $value )
						),
					);

					// Backward compatibility. Keep Padding on Mobile is deprecated in favour of responsive inputs mechanism for custom padding
					// To ensure that it is compatibility with previous version of Divi, this option is now only used as last resort if no
					// responsive padding value is found,  and padding_mobile value is saved (which is set to off by default).
					if ( in_array( $padding_mobile, array( 'on', 'off' ), true ) && 'on' !== $padding_mobile && ! $custom_padding_responsive_active ) {
						$element_style['media_query'] = ET_Builder_Element::get_media_query( 'min_width_981' );
					}

					ET_Builder_Element::set_style( $function_name, $element_style );
				}
			}
		}

		if ( ! empty( $padding_mobile_values['tablet'] ) || ! empty( $padding_values['phone'] ) ) {
			$padding_mobile_values_processed = array();

			foreach ( array( 'tablet', 'phone' ) as $device ) {
				if ( empty( $padding_mobile_values[ $device ] ) ) {
					continue;
				}

				$padding_mobile_values_processed[ $device ] = array(
					'padding-top'    => isset( $padding_mobile_values[ $device ][0] ) ? $padding_mobile_values[ $device ][0] : '',
					'padding-right'  => isset( $padding_mobile_values[ $device ][1] ) ? $padding_mobile_values[ $device ][1] : '',
					'padding-bottom' => isset( $padding_mobile_values[ $device ][2] ) ? $padding_mobile_values[ $device ][2] : '',
					'padding-left'   => isset( $padding_mobile_values[ $device ][3] ) ? $padding_mobile_values[ $device ][3] : '',
				);
			}

			if ( ! empty( $padding_mobile_values_processed ) ) {
				et_pb_responsive_options()->generate_responsive_css( $padding_mobile_values_processed, '%%order_class%%.et_pb_row', '', $function_name, ' !important; ' );
			}
		}

		$parallax_image   = $this->get_parallax_image_background();
		$background_video = $this->video_background();

		// CSS Filters.
		$this->add_classname( $this->generate_css_filters( $function_name ) );

		// Remove automatically added classnames.
		$this->remove_classname( 'et_pb_module' );

		if ( self::contains( $content, array( 'et_pb_menu', 'et_pb_fullwidth_menu' ) ) ) {
			$this->add_classname( 'et_pb_row--with-menu' );
		}

		// Save module classes into variable BEFORE processing the content with `do_shortcode()`
		// Otherwise order classes messed up with internal rows if exist.
		$module_classes = $this->module_classname( $function_name );

		// Inner content shortcode parsing has to be done after all classname addition/removal.
		$inner_content               = do_shortcode( et_pb_fix_shortcodes( $content ) );
		$content_dependent_classname = '' === trim( $inner_content ) ? ' et_pb_row_empty' : '';

		// reset the global column settings to make sure they are not affected by internal content
		// This has to be done after inner content's shortcode being parsed.
		$et_pb_all_column_settings = $et_pb_all_column_settings_backup;

		// Reset row's column content flag.
		if ( $et_pb_rendering_column_content_row ) {
			$et_pb_rendering_column_content_row = false;
		}

		$output = sprintf(
			'<div%4$s class="%2$s%7$s"%8$s>
				%1$s
				%6$s
				%5$s
				%9$s
				%10$s
			</div>',
			$inner_content,
			$module_classes,
			esc_html( $function_name ),
			$this->module_id(),
			$background_video,
			$parallax_image,
			$content_dependent_classname,
			et_core_esc_previously( $gutter_hover_data ),
			et_core_esc_previously( $this->background_pattern() ), // 9
			et_core_esc_previously( $this->background_mask() ) // 10
		);

		return $output;
	}
}
new ET_Builder_Row();

/**
 * Class used to process inner row element(module).
 *
 * @since 4.6.2
 */
class ET_Builder_Row_Inner extends ET_Builder_Structure_Element {
	/**
	 * Sets up the element's properties.
	 */
	public function init() {
		$this->name            = esc_html__( 'Row', 'et_builder' );
		$this->plural          = esc_html__( 'Rows', 'et_builder' );
		$this->slug            = 'et_pb_row_inner';
		$this->vb_support      = 'on';
		$this->child_slug      = 'et_pb_column_inner';
		$this->child_item_text = esc_html__( 'Column', 'et_builder' );

		$this->advanced_fields = array(
			'background'      => array(
				'use_background_color'          => true,
				'use_background_image'          => true,
				'use_background_color_gradient' => true,
				'use_background_video'          => true,
				'use_background_pattern'        => true,
				'use_background_mask'           => true,
			),
			'margin_padding'  => array(
				'css' => array(
					'main'      => '%%order_class%%.et_pb_row_inner',
					'important' => 'all',
				),
			),
			'max_width'       => array(
				'css'     => array(
					'main' => '%%order_class%%.et_pb_row_inner',
				),
				'options' => array(
					'module_alignment' => array(
						'label'          => esc_html__( 'Row Alignment', 'et_builder' ),
						'description'    => esc_html__( 'Rows can be aligned to the left, right or center. By default, rows are centered within their parent section.', 'et_builder' ),
						'default_sticky' => 'center',
					),
				),
			),
			'fonts'           => false,
			'text'            => false,
			'button'          => false,
			'position_fields' => array(
				'default' => 'relative',
			),
		);

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'column_structure' => array(
						'title'       => esc_html__( 'Column Structure', 'et_builder' ),
						'priority'    => 1,
						'always_open' => true,
					),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'width' => array(
						'title'    => et_builder_i18n( 'Sizing' ),
						'priority' => 65,
					),
				),
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'R9ds7bEaHE8',
				'name' => esc_html__( 'An introduction to Rows', 'et_builder' ),
			),
		);
	}

	/**
	 * Get the settings fields data for this element.
	 *
	 * @return array|array[]
	 */
	public function get_fields() {
		$fields = array(
			'column_structure'           => array(
				'label'       => esc_html__( 'Column Structure', 'et_builder' ),
				'description' => esc_html__( 'Here you can choose the Column Structure for this Row.', 'et_builder' ),
				'type'        => 'column-structure',
				'default'     => '4_4',
				'options'     => array(
					'4_4'             => et_pb_get_column_svg( '4_4' ),
					'1_2,1_2'         => et_pb_get_column_svg( '1_2,1_2' ),
					'1_3,1_3,1_3'     => et_pb_get_column_svg( '1_3,1_3,1_3' ),
					'1_4,1_4,1_4,1_4' => et_pb_get_column_svg( '1_4,1_4,1_4,1_4' ),
				),
				'toggle_slug' => 'column_structure',
			),
			'padding_mobile'             => array(
				'label'       => esc_html__( 'Keep Custom Padding on Mobile', 'et_builder' ),
				'type'        => 'skip', // Remaining attribute for backward compatibility.
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'margin_padding',
			),
			'use_custom_gutter'          => array(
				'label'           => esc_html__( 'Use Custom Gutter Width', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default'         => 'off',
				'affects'         => array(
					'gutter_width',
				),
				'description'     => esc_html__( 'Enable this option to define custom gutter width for this row.', 'et_builder' ),
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
			),
			'gutter_width'               => array(
				'label'            => esc_html__( 'Gutter Width', 'et_builder' ),
				'type'             => 'range',
				'option_category'  => 'layout',
				'range_settings'   => array(
					'min'       => 1,
					'max'       => 4,
					'step'      => 1,
					'min_limit' => 1,
					'max_limit' => 4,
				),
				'depends_show_if'  => 'on',
				'description'      => esc_html__( 'Adjust the spacing between each column in this row.', 'et_builder' ),
				'validate_unit'    => false,
				'fixed_range'      => true,
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'width',
				'default_on_front' => (string) et_get_option( 'gutter_width', '3' ),
				'hover'            => 'tabs',
			),
			'make_equal'                 => array(
				'label'           => esc_html__( 'Equalize Column Heights', 'et_builder' ),
				'description'     => esc_html__( 'Equalizing column heights will force all columns to assume the height of the tallest column in the row. All columns will have the same height, keeping their appearance uniform.', 'et_builder' ),
				'type'            => 'yes_no_button',
				'option_category' => 'layout',
				'options'         => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default'         => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'width',
			),
			'column_padding_mobile'      => array(
				'label'    => esc_html__( 'Keep Column Padding on Mobile', 'et_builder' ),
				'tab_slug' => 'advanced',
				'type'     => 'skip', // Remaining attribute for backward compatibility.
			),
			'custom_padding_last_edited' => array(
				'type'     => 'skip',
				'tab_slug' => 'advanced',
			),
		);

		$column_fields = $this->get_column_fields(
			4,
			array(
				'background_color'                         => array(),
				'bg_img'                                   => array(),
				'padding_top_bottom_link'                  => array(),
				'padding_left_right_link'                  => array(),
				'parallax'                                 => array(
					'default_on_front' => 'off',
				),
				'parallax_method'                          => array(
					'default_on_front' => 'on',
				),
				'background_size'                          => array(),
				'background_image_width'                   => array(),
				'background_image_height'                  => array(),
				'background_position'                      => array(),
				'background_horizontal_offset'             => array(),
				'background_vertical_offset'               => array(),
				'background_repeat'                        => array(),
				'background_blend'                         => array(),
				'use_background_color_gradient'            => array(),
				'background_color_gradient_repeat'         => array(),
				'background_color_gradient_type'           => array(),
				'background_color_gradient_direction'      => array(),
				'background_color_gradient_direction_radial' => array(),
				'background_color_gradient_stops'          => array(),
				'background_color_gradient_unit'           => array(),
				'background_color_gradient_overlays_image' => array(),
				'background_color_gradient_start'          => array(),
				'background_color_gradient_end'            => array(),
				'background_color_gradient_start_position' => array(),
				'background_color_gradient_end_position'   => array(),
				'background_color_gradient_overlays_image' => array(),
				'background_enable_pattern_style'          => array(),
				'background_pattern_style'                 => array(),
				'background_pattern_color'                 => array(),
				'background_pattern_transform'             => array(),
				'background_pattern_size'                  => array(),
				'background_pattern_width'                 => array(),
				'background_pattern_height'                => array(),
				'background_pattern_repeat'                => array(),
				'background_pattern_repeat_origin'         => array(),
				'background_pattern_horizontal_offset'     => array(),
				'background_pattern_vertical_offset'       => array(),
				'background_pattern_blend_mode'            => array(),
				'background_enable_mask_style'             => array(),
				'background_mask_style'                    => array(),
				'background_mask_color'                    => array(),
				'background_mask_transform'                => array(),
				'background_mask_aspect_ratio'             => array(),
				'background_mask_size'                     => array(),
				'background_mask_width'                    => array(),
				'background_mask_height'                   => array(),
				'background_mask_position'                 => array(),
				'background_mask_horizontal_offset'        => array(),
				'background_mask_vertical_offset'          => array(),
				'background_mask_blend_mode'               => array(),
				'background_video_mp4'                     => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'background_video_webm'                    => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'background_video_width'                   => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'background_video_height'                  => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'allow_player_pause'                       => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'background_video_pause_outside_viewport'  => array(
					'computed_affects' => array(
						'__video_background',
					),
				),
				'__video_background'                       => array(
					'type'                => 'computed',
					'computed_callback'   => array(
						'ET_Builder_Column',
						'get_column_video_background',
					),
					'computed_depends_on' => array(
						'background_video_mp4',
						'background_video_webm',
						'background_video_width',
						'background_video_height',
					),
					'computed_minimum'    => array(
						'background_video_mp4',
						'background_video_webm',
					),
				),
				'padding_top'                              => array( 'tab_slug' => 'advanced' ),
				'padding_right'                            => array( 'tab_slug' => 'advanced' ),
				'padding_bottom'                           => array( 'tab_slug' => 'advanced' ),
				'padding_left'                             => array( 'tab_slug' => 'advanced' ),
				'padding_top_bottom_link'                  => array( 'tab_slug' => 'advanced' ),
				'padding_left_right_link'                  => array( 'tab_slug' => 'advanced' ),
				'padding_%column_index%_tablet'            => array(
					'has_custom_index_location' => true,
					'tab_slug'                  => 'advanced',
				),
				'padding_%column_index%_phone'             => array(
					'has_custom_index_location' => true,
					'tab_slug'                  => 'advanced',
				),
				'padding_%column_index%_last_edited'       => array(
					'has_custom_index_location' => true,
					'tab_slug'                  => 'advanced',
				),
				'module_id'                                => array( 'tab_slug' => 'custom_css' ),
				'module_class'                             => array( 'tab_slug' => 'custom_css' ),
				'custom_css_before'                        => array( 'tab_slug' => 'custom_css' ),
				'custom_css_main'                          => array( 'tab_slug' => 'custom_css' ),
				'custom_css_after'                         => array( 'tab_slug' => 'custom_css' ),
			)
		);

		return array_merge( $fields, $column_fields );
	}

	/**
	 * Get CSS fields transition of inner row.
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		for ( $i = 1; $i <= 6; $i ++ ) {
			$selector                          = "%%order_class%% > .et_pb_column:nth-child({$i})";
			$fields[ "background_color_{$i}" ] = array( 'background-color' => $selector );
			$fields[ "padding_{$i}" ]          = array( 'padding' => $selector );
		}

		return $fields;
	}

	/**
	 * Generates the structure module's HTML output based on {@see ET_Builder_Row_Inner::$props}.
	 *
	 * @param array  $atts       List of unprocessed attributes.
	 * @param mixed  $content     Content being processed.
	 * @param string $function_name Slug of module that is used for rendering output.
	 *
	 * @return string The module's HTML output.
	 */
	public function render( $atts, $content, $function_name ) {
		$gutter_width               = $this->props['gutter_width'];
		$gutter_width_hover         = $this->get_hover_value( 'gutter_width' );
		$make_equal                 = $this->props['make_equal'];
		$custom_padding             = $this->props['custom_padding'];
		$padding_mobile             = $this->props['padding_mobile'];
		$custom_padding_tablet      = $this->props['custom_padding_tablet'];
		$custom_padding_phone       = $this->props['custom_padding_phone'];
		$custom_padding_last_edited = $this->props['custom_padding_last_edited'];
		$column_padding_mobile      = $this->props['column_padding_mobile'];
		$global_module              = $this->props['global_module'];
		$use_custom_gutter          = $this->props['use_custom_gutter'];

		$hover = et_pb_hover_options();

		global $et_pb_all_column_settings_inner, $et_pb_rendering_column_content, $et_pb_rendering_column_content_row;

		$et_pb_all_column_settings_inner = ! isset( $et_pb_all_column_settings_inner ) ? array() : $et_pb_all_column_settings_inner;

		$et_pb_all_column_settings_backup = $et_pb_all_column_settings_inner;

		$keep_column_padding_mobile = $column_padding_mobile;

		if ( '' !== $global_module ) {
			$global_content = et_pb_load_global_module( $global_module, $function_name );

			if ( '' !== $global_content ) {
				return do_shortcode( et_pb_fix_shortcodes( wpautop( $global_content ) ) );
			}
		}

		$custom_padding_responsive_active = et_pb_get_responsive_status( $custom_padding_last_edited );

		$padding_mobile_values = $custom_padding_responsive_active ? array(
			'tablet' => explode( '|', $custom_padding_tablet ),
			'phone'  => explode( '|', $custom_padding_phone ),
		) : array(
			'tablet' => false,
			'phone'  => false,
		);

		$et_pb_columns_inner_counter = 0;

		$padding_values = explode( '|', $custom_padding );

		if ( ! empty( $padding_values ) ) {
			// old version of Rows support only top and bottom padding, so we need to handle it along with the full padding in the recent version.
			if ( 2 === count( $padding_values ) ) {
				$padding_settings = array(
					'top'    => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'bottom' => isset( $padding_values[1] ) ? $padding_values[1] : '',
				);
			} else {
				$padding_settings = array(
					'top'    => isset( $padding_values[0] ) ? $padding_values[0] : '',
					'right'  => isset( $padding_values[1] ) ? $padding_values[1] : '',
					'bottom' => isset( $padding_values[2] ) ? $padding_values[2] : '',
					'left'   => isset( $padding_values[3] ) ? $padding_values[3] : '',
				);
			}

			foreach ( $padding_settings as $padding_side => $value ) {
				if ( '' !== $value ) {
					$element_style = array(
						'selector'    => '.et_pb_column %%order_class%%',
						'declaration' => sprintf(
							'padding-%1$s: %2$s;',
							esc_html( $padding_side ),
							esc_html( $value )
						),
					);

					// Backward compatibility. Keep Padding on Mobile is deprecated in favour of responsive inputs mechanism for custom padding
					// To ensure that it is compatibility with previous version of Divi, this option is now only used as last resort if no
					// responsive padding value is found,  and padding_mobile value is saved (which is set to off by default).
					if ( in_array( $padding_mobile, array( 'on', 'off' ), true ) && 'on' !== $padding_mobile && ! $custom_padding_responsive_active ) {
						$element_style['media_query'] = ET_Builder_Element::get_media_query( 'min_width_981' );
					}

					ET_Builder_Element::set_style( $function_name, $element_style );
				}
			}
		}

		if ( ! empty( $padding_mobile_values['tablet'] ) || ! empty( $padding_values['phone'] ) ) {
			$padding_mobile_values_processed = array();

			foreach ( array( 'tablet', 'phone' ) as $device ) {
				if ( empty( $padding_mobile_values[ $device ] ) ) {
					continue;
				}

				$padding_mobile_values_processed[ $device ] = array(
					'padding-top'    => isset( $padding_mobile_values[ $device ][0] ) ? $padding_mobile_values[ $device ][0] : '',
					'padding-right'  => isset( $padding_mobile_values[ $device ][1] ) ? $padding_mobile_values[ $device ][1] : '',
					'padding-bottom' => isset( $padding_mobile_values[ $device ][2] ) ? $padding_mobile_values[ $device ][2] : '',
					'padding-left'   => isset( $padding_mobile_values[ $device ][3] ) ? $padding_mobile_values[ $device ][3] : '',
				);
			}

			if ( ! empty( $padding_mobile_values_processed ) ) {
				et_pb_responsive_options()->generate_responsive_css( $padding_mobile_values_processed, '.et_pb_column %%order_class%%', '', $function_name, ' !important; ' );
			}
		}

		$internal_columns_settings_array = array(
			'keep_column_padding_mobile'  => $keep_column_padding_mobile,
			'et_pb_columns_inner_counter' => $et_pb_columns_inner_counter,
			'et_pb_columns_counter'       => 0,
		);

		$current_row_position = $et_pb_rendering_column_content ? 'internal_row' : 'regular_row';

		$et_pb_all_column_settings_inner[ $current_row_position ] = $internal_columns_settings_array;

		if ( 'on' === $make_equal ) {
			$this->add_classname( 'et_pb_equal_columns' );
		}

		$gutter_hover_data = '';

		if ( 'on' === $use_custom_gutter && '' !== $gutter_width ) {
			$gutter_width = '0' === $gutter_width ? '1' : $gutter_width; // set the gutter to 1 if 0 entered by user.
			$this->add_classname( 'et_pb_gutters' . $gutter_width );

			if ( et_builder_is_hover_enabled( 'gutter_width', $this->props ) && ! empty( $gutter_width_hover ) && $gutter_width !== $gutter_width_hover ) {
				$this->add_classname( 'et_pb_gutter_hover' );

				$gutter_hover_data = sprintf(
					' data-original_gutter="%1$s" data-hover_gutter="%2$s"',
					esc_attr( $gutter_width ),
					esc_attr( $gutter_width_hover )
				);
			}
		}

		$parallax_image   = $this->get_parallax_image_background();
		$background_video = $this->video_background();

		// CSS Filters.
		$this->add_classname( $this->generate_css_filters( $function_name ) );

		// Remove automatically added classnames.
		$this->remove_classname( 'et_pb_module' );

		if ( self::contains( $content, array( 'et_pb_menu', 'et_pb_fullwidth_menu' ) ) ) {
			$this->add_classname( 'et_pb_row--with-menu' );
		}

		// Save module classes into variable BEFORE processing the content with `do_shortcode()`
		// Otherwise order classes messed up with internal rows if exist.
		$module_classes = $this->module_classname( $function_name );

		// Inner content shortcode parsing has to be done after all classname addition/removal.
		$inner_content               = do_shortcode( et_pb_fix_shortcodes( $content ) );
		$content_dependent_classname = '' === trim( $inner_content ) ? ' et_pb_row_empty' : '';

		// reset the global column settings to make sure they are not affected by internal content.
		$et_pb_all_column_settings_inner = $et_pb_all_column_settings_backup;

		$output = sprintf(
			'<div%4$s class="%2$s%7$s"%8$s>
				%1$s
				%5$s
				%6$s
				%9$s
				%10$s
			</div>',
			$inner_content,
			$module_classes,
			esc_html( $function_name ),
			$this->module_id(),
			$parallax_image,
			$background_video,
			$content_dependent_classname,
			et_core_esc_previously( $gutter_hover_data ),
			et_core_esc_previously( $this->background_pattern() ), // 9
			et_core_esc_previously( $this->background_mask() ) // 10
		);

		return $output;
	}
}
new ET_Builder_Row_Inner();

/**
 * Class used to process column element(module).
 *
 * @since 4.6.2
 */
class ET_Builder_Column extends ET_Builder_Structure_Element {

	/**
	 * Sets up the element's properties.
	 */
	public function init() {
		$this->name                        = esc_html__( 'Column', 'et_builder' );
		$this->plural                      = esc_html__( 'Columns', 'et_builder' );
		$this->slug                        = 'et_pb_column';
		$this->additional_shortcode_slugs  = array( 'et_pb_column_inner' );
		$this->child_title_var             = 'admin_label';
		$this->advanced_setting_title_text = esc_html__( 'Column', 'et_builder' );
		$this->vb_support                  = 'on';
		$this->type                        = 'child';

		$this->advanced_fields = array(
			'max_width'       => false,
			'fonts'           => false,
			'text'            => false,
			'margin_padding'  => array(
				'use_margin' => false,
			),
			'z_index'         => array(
				'default'   => '2',
				'important' => true,
			),
			'position_fields' => array(
				'default' => 'relative',
			),
		);

		$this->settings_modal_toggles = array(
			'general' => array(
				'toggles' => array(
					'admin_label' => array(
						'title'    => et_builder_i18n( 'Admin Label' ),
						'priority' => 99,
					),
				),
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'R9ds7bEaHE8',
				'name' => esc_html__( 'An introduction to the Column module', 'et_builder' ),
			),
		);
	}

	/**
	 * Get the settings fields data for this element.
	 *
	 * @return array|array[]
	 */
	public function get_fields() {
		$fields = array(
			'type'                        => array(
				'default_on_front' => '4_4',
				'type'             => 'skip',
			),
			'specialty_columns'           => array(
				'type' => 'skip',
			),
			'saved_specialty_column_type' => array(
				'type' => 'skip',
			),
			'module_id'                   => array(
				'label'           => esc_html__( 'CSS ID', 'et_builder' ),
				'description'     => esc_html__( "Assign a unique CSS ID to the element which can be used to assign custom CSS styles from within your child theme or from within Divi's custom CSS inputs.", 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'module_class'                => array(
				'label'           => esc_html__( 'CSS Class', 'et_builder' ),
				'description'     => esc_html__( "Assign any number of CSS Classes to the element, separated by spaces, which can be used to assign custom CSS styles from within your child theme or from within Divi's custom CSS inputs.", 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'classes',
				'option_class'    => 'et_pb_custom_css_regular',
			),
			'admin_label'                 => array(
				'label'           => et_builder_i18n( 'Admin Label' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'This will change the label of the module in the builder for easy identification.', 'et_builder' ),
				'toggle_slug'     => 'admin_label',
			),
		);

		return $fields;
	}

	/**
	 * Generates the structure module's HTML output based on {@see ET_Builder_Column::$props}.
	 *
	 * @param array  $atts       List of unprocessed attributes.
	 * @param mixed  $content     Content being processed.
	 * @param string $function_name Slug of module that is used for rendering output.
	 *
	 * @return string The module's HTML output.
	 */
	public function render( $atts, $content, $function_name ) {
		$type                        = $this->props['type'];
		$specialty_columns           = $this->props['specialty_columns'];
		$saved_specialty_column_type = $this->props['saved_specialty_column_type'];
		$custom_css_class            = '';

		global $et_pb_all_column_settings,
			$et_pb_all_column_settings_inner,
			$et_specialty_column_type,
			$et_pb_rendering_column_content,
			$et_pb_rendering_column_content_row,
			$et_pb_rendering_specialty_section,
			$et_pb_column_completion;

		$is_specialty_column          = 'et_pb_column_inner' !== $function_name && $et_pb_rendering_specialty_section;
		$gobal_column_settings_holder = 'et_pb_column_inner' === $function_name ? $et_pb_all_column_settings_inner : $et_pb_all_column_settings;
		$current_row_position         = $et_pb_rendering_column_content_row ? 'internal_row' : 'regular_row';
		$array_index                  = self::$_->array_get( $gobal_column_settings_holder, "{$current_row_position}.et_pb_columns_counter", 0 );
		$keep_column_padding_mobile   = self::$_->array_get( $gobal_column_settings_holder, "{$current_row_position}.keep_column_padding_mobile", 'on' );
		$sticky                       = et_pb_sticky_options();
		$background_options           = et_pb_background_options();

		if ( $is_specialty_column ) {
			$et_specialty_column_type = $type;
			$backgrounds_array        = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_backgrounds", array() );
			$background_gradient      = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_backgrounds_gradient.[{$array_index}]", '' );
			$pattern_array            = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_backgrounds_pattern.[{$array_index}]", array() );
			$mask_array               = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_backgrounds_mask.[{$array_index}]", array() );
			$background_video         = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_backgrounds_video.[{$array_index}]", '' );
			$paddings_array           = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_paddings", array() );
			$paddings_mobile_array    = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_paddings_mobile", array() );
			$column_css_array         = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_css", array() );
			$column_parallax          = self::$_->array_get( $et_pb_all_column_settings, "{$current_row_position}.et_pb_column_parallax", '' );

			$background_color                   = isset( $backgrounds_array[ $array_index ]['color'] ) ? $backgrounds_array[ $array_index ]['color'] : '';
			$background_img                     = isset( $backgrounds_array[ $array_index ]['image'] ) ? $backgrounds_array[ $array_index ]['image'] : '';
			$background_size                    = isset( $backgrounds_array[ $array_index ]['image_size'] ) ? $backgrounds_array[ $array_index ]['image_size'] : '';
			$background_image_width             = isset( $backgrounds_array[ $array_index ]['image_width'] ) ? $backgrounds_array[ $array_index ]['image_width'] : '';
			$background_image_height            = isset( $backgrounds_array[ $array_index ]['image_height'] ) ? $backgrounds_array[ $array_index ]['image_height'] : '';
			$background_position                = isset( $backgrounds_array[ $array_index ]['image_position'] ) ? $backgrounds_array[ $array_index ]['image_position'] : '';
			$background_horizontal_offset       = isset( $backgrounds_array[ $array_index ]['image_horizontal_offset'] ) ? $backgrounds_array[ $array_index ]['image_horizontal_offset'] : '';
			$background_vertical_offset         = isset( $backgrounds_array[ $array_index ]['image_vertical_offset'] ) ? $backgrounds_array[ $array_index ]['image_vertical_offset'] : '';
			$background_repeat                  = isset( $backgrounds_array[ $array_index ]['image_repeat'] ) ? $backgrounds_array[ $array_index ]['image_repeat'] : '';
			$background_blend                   = isset( $backgrounds_array[ $array_index ]['image_blend'] ) ? $backgrounds_array[ $array_index ]['image_blend'] : '';
			$background_gradient_overlays_image = isset( $background_gradient['overlays_image'] ) ? $background_gradient['overlays_image'] : '';
			$background_color_hover             = isset( $backgrounds_array[ $array_index ] ) ? self::$_->array_get( $backgrounds_array[ $array_index ], 'color_hover' ) : '';
			$background_color_hover_enabled     = isset( $backgrounds_array[ $array_index ] ) ? self::$_->array_get( $backgrounds_array[ $array_index ], 'color_hover_enabled' ) : '';
			$background_color_sticky            = isset( $backgrounds_array[ $array_index ] ) ? self::$_->array_get( $backgrounds_array[ $array_index ], 'color_sticky' ) : '';
			$background_color_sticky_enabled    = isset( $backgrounds_array[ $array_index ] ) ? self::$_->array_get( $backgrounds_array[ $array_index ], 'color_sticky_enabled' ) : '';

			// Pattern.
			$enable_pattern_style_default      = $background_options->get_attr_default( 'style', 'background_enable_pattern', $this->fields_unprocessed );
			$pattern_style_name_default        = $background_options->get_attr_default( 'style', 'background_pattern', $this->fields_unprocessed );
			$pattern_color_default             = $background_options->get_attr_default( 'color', 'background_pattern', $this->fields_unprocessed );
			$pattern_transform_default         = $background_options->get_attr_default( 'transform', 'background_pattern', $this->fields_unprocessed );
			$pattern_size_default              = $background_options->get_attr_default( 'size', 'background_pattern', $this->fields_unprocessed );
			$pattern_width_default             = $background_options->get_attr_default( 'width', 'background_pattern', $this->fields_unprocessed );
			$pattern_height_default            = $background_options->get_attr_default( 'height', 'background_pattern', $this->fields_unprocessed );
			$pattern_repeat_default            = $background_options->get_attr_default( 'repeat', 'background_pattern', $this->fields_unprocessed );
			$pattern_repeat_origin_default     = $background_options->get_attr_default( 'repeat_origin', 'background_pattern', $this->fields_unprocessed );
			$pattern_horizontal_offset_default = $background_options->get_attr_default( 'horizontal_offset', 'background_pattern', $this->fields_unprocessed );
			$pattern_vertical_offset_default   = $background_options->get_attr_default( 'vertical_offset', 'background_pattern', $this->fields_unprocessed );
			$pattern_blend_mode_default        = $background_options->get_attr_default( 'blend_mode', 'background_pattern', $this->fields_unprocessed );

			$enable_pattern_style      = ! empty( $pattern_array['enable_pattern_style'] ) ? $pattern_array['enable_pattern_style'] : $enable_pattern_style_default;
			$pattern_style_name        = ! empty( $pattern_array['pattern_style'] ) ? $pattern_array['pattern_style'] : $pattern_style_name_default;
			$pattern_color             = ! empty( $pattern_array['pattern_color'] ) ? $pattern_array['pattern_color'] : $pattern_color_default;
			$pattern_transform         = ! empty( $pattern_array['pattern_transform'] ) ? $pattern_array['pattern_transform'] : $pattern_transform_default;
			$pattern_size              = ! empty( $pattern_array['pattern_size'] ) ? $pattern_array['pattern_size'] : $pattern_size_default;
			$pattern_width             = ! empty( $pattern_array['pattern_width'] ) ? $pattern_array['pattern_width'] : $pattern_width_default;
			$pattern_height            = ! empty( $pattern_array['pattern_height'] ) ? $pattern_array['pattern_height'] : $pattern_height_default;
			$pattern_repeat            = ! empty( $pattern_array['pattern_repeat'] ) ? $pattern_array['pattern_repeat'] : $pattern_repeat_default;
			$pattern_repeat_origin     = ! empty( $pattern_array['pattern_repeat_origin'] ) ? $pattern_array['pattern_repeat_origin'] : $pattern_repeat_origin_default;
			$pattern_horizontal_offset = ! empty( $pattern_array['pattern_horizontal_offset'] ) ? $pattern_array['pattern_horizontal_offset'] : $pattern_horizontal_offset_default;
			$pattern_vertical_offset   = ! empty( $pattern_array['pattern_vertical_offset'] ) ? $pattern_array['pattern_vertical_offset'] : $pattern_vertical_offset_default;
			$pattern_blend_mode        = ! empty( $pattern_array['pattern_blend_mode'] ) ? $pattern_array['pattern_blend_mode'] : $pattern_blend_mode_default;

			// Mask.
			$enable_mask_style_default      = $background_options->get_attr_default( 'style', 'background_enable_mask', $this->fields_unprocessed );
			$mask_style_name_default        = $background_options->get_attr_default( 'style', 'background_mask', $this->fields_unprocessed );
			$mask_color_default             = $background_options->get_attr_default( 'color', 'background_mask', $this->fields_unprocessed );
			$mask_transform_default         = $background_options->get_attr_default( 'transform', 'background_mask', $this->fields_unprocessed );
			$mask_ratio_default             = $background_options->get_attr_default( 'aspect_ratio', 'background_mask', $this->fields_unprocessed );
			$mask_size_default              = $background_options->get_attr_default( 'size', 'background_mask', $this->fields_unprocessed );
			$mask_width_default             = $background_options->get_attr_default( 'width', 'background_mask', $this->fields_unprocessed );
			$mask_height_default            = $background_options->get_attr_default( 'height', 'background_mask', $this->fields_unprocessed );
			$mask_position_default          = $background_options->get_attr_default( 'position', 'background_mask', $this->fields_unprocessed );
			$mask_horizontal_offset_default = $background_options->get_attr_default( 'horizontal_offset', 'background_mask', $this->fields_unprocessed );
			$mask_vertical_offset_default   = $background_options->get_attr_default( 'vertical_offset', 'background_mask', $this->fields_unprocessed );
			$mask_blend_mode_default        = $background_options->get_attr_default( 'blend_mode', 'background_mask', $this->fields_unprocessed );

			$enable_mask_style      = ! empty( $mask_array['enable_mask_style'] ) ? $mask_array['enable_mask_style'] : $enable_mask_style_default;
			$mask_style_name        = ! empty( $mask_array['mask_style'] ) ? $mask_array['mask_style'] : $mask_style_name_default;
			$mask_color             = ! empty( $mask_array['mask_color'] ) ? $mask_array['mask_color'] : $mask_color_default;
			$mask_transform         = ! empty( $mask_array['mask_transform'] ) ? $mask_array['mask_transform'] : $mask_transform_default;
			$mask_aspect_ratio      = ! empty( $mask_array['mask_aspect_ratio'] ) ? $mask_array['mask_aspect_ratio'] : $mask_ratio_default;
			$mask_size              = ! empty( $mask_array['mask_size'] ) ? $mask_array['mask_size'] : $mask_size_default;
			$mask_width             = ! empty( $mask_array['mask_width'] ) ? $mask_array['mask_width'] : $mask_width_default;
			$mask_height            = ! empty( $mask_array['mask_height'] ) ? $mask_array['mask_height'] : $mask_height_default;
			$mask_position          = ! empty( $mask_array['mask_position'] ) ? $mask_array['mask_position'] : $mask_position_default;
			$mask_horizontal_offset = ! empty( $mask_array['mask_horizontal_offset'] ) ? $mask_array['mask_horizontal_offset'] : $mask_horizontal_offset_default;
			$mask_vertical_offset   = ! empty( $mask_array['mask_vertical_offset'] ) ? $mask_array['mask_vertical_offset'] : $mask_vertical_offset_default;
			$mask_blend_mode        = ! empty( $mask_array['mask_blend_mode'] ) ? $mask_array['mask_blend_mode'] : $mask_blend_mode_default;

			// Others.
			$padding_values            = isset( $paddings_array[ $array_index ] ) ? $paddings_array[ $array_index ] : array();
			$padding_mobile_values     = isset( $paddings_mobile_array[ $array_index ] ) ? $paddings_mobile_array[ $array_index ] : array();
			$padding_last_edited       = isset( $padding_mobile_values['last_edited'] ) ? $padding_mobile_values['last_edited'] : 'off|desktop';
			$padding_responsive_active = et_pb_get_responsive_status( $padding_last_edited );
			$is_parallax               = isset( $column_parallax[ $array_index ][0] ) && 'on' === $column_parallax[ $array_index ][0] ? true : false;
			$parallax_method           = isset( $column_parallax[ $array_index ][0] ) && 'on' === $column_parallax[ $array_index ][0] ? $column_parallax[ $array_index ][1] : '';
			$custom_css_class          = isset( $column_css_array['css_class'][ $array_index ] ) ? ' ' . $column_css_array['css_class'][ $array_index ] : '';
			$custom_css_id             = isset( $column_css_array['css_id'][ $array_index ] ) ? $column_css_array['css_id'][ $array_index ] : '';
			$custom_css_before         = isset( $column_css_array['custom_css_before'][ $array_index ] ) ? $column_css_array['custom_css_before'][ $array_index ] : '';
			$custom_css_main           = isset( $column_css_array['custom_css_main'][ $array_index ] ) ? $column_css_array['custom_css_main'][ $array_index ] : '';
			$custom_css_after          = isset( $column_css_array['custom_css_after'][ $array_index ] ) ? $column_css_array['custom_css_after'][ $array_index ] : '';
			$custom_css_before_hover   = self::$_->array_get( $column_css_array, "custom_css_before_hover.[$array_index]", '' );
			$custom_css_main_hover     = self::$_->array_get( $column_css_array, "custom_css_main_hover.[$array_index]", '' );
			$custom_css_after_hover    = self::$_->array_get( $column_css_array, "custom_css_after_hover.[$array_index]", '' );
			$custom_css_before_sticky  = self::$_->array_get( $column_css_array, "custom_css_before_sticky.[$array_index]", '' );
			$custom_css_main_sticky    = self::$_->array_get( $column_css_array, "custom_css_main_sticky.[$array_index]", '' );
			$custom_css_after_sticky   = self::$_->array_get( $column_css_array, "custom_css_after_sticky.[$array_index]", '' );
		} else {
			$custom_css_id   = self::$_->array_get( $this->props, 'module_id', '' );
			$parallax_method = self::$_->array_get( $this->props, 'parallax_method', '' );
		}

		// et_pb_columns_counter should be updated for all columns to calculate the last column correctly.
		if ( isset( $gobal_column_settings_holder[ $current_row_position ] ) ) {
			if ( 'et_pb_column_inner' === $function_name ) {
				$et_pb_all_column_settings_inner[ $current_row_position ]['et_pb_columns_counter']++;
			} else {
				$et_pb_all_column_settings[ $current_row_position ]['et_pb_columns_counter']++;
			}
		}

		// Get column type value in array.
		$column_type = explode( '_', $type );

		// Just in case for some reason column shortcode has no `type` attribute and causes unexpected $column_type values.
		if ( isset( $column_type[0] ) && isset( $column_type[1] ) ) {
			// Get column progress.
			$column_progress = intval( $column_type[0] ) / intval( $column_type[1] );

			if ( 0 === $array_index ) {
				$et_pb_column_completion = $column_progress;
			} else {
				$et_pb_column_completion = $et_pb_column_completion + $column_progress;
			}
		}

		// Last column is when sum of column type value equals to 1. Compare value as a string, comparing integers returns inconsistent results.
		$is_last_column = '1' === strval( $et_pb_column_completion );

		// Still need to manually output this for Specialty columns.
		if ( $is_specialty_column ) {
			$background_images = array();
			$is_gradient_on    = false;

			if ( '' !== $background_gradient && 'on' === $background_gradient['active'] ) {
				$has_background_gradient = true;

				$default_gradient = apply_filters(
					'et_pb_default_gradient',
					array(
						'repeat'           => ET_Global_Settings::get_value( 'all_background_gradient_repeat' ),
						'type'             => ET_Global_Settings::get_value( 'all_background_gradient_type' ),
						'direction'        => ET_Global_Settings::get_value( 'all_background_gradient_direction' ),
						'radial_direction' => ET_Global_Settings::get_value( 'all_background_gradient_direction_radial' ),
						'stops'            => ET_Global_Settings::get_value( 'all_background_gradient_stops' ),
						'unit'             => ET_Global_Settings::get_value( 'all_background_gradient_unit' ),
					)
				);

				$background_gradient = wp_parse_args( array_filter( $background_gradient ), $default_gradient );

				$gradient_stops = str_replace( '|', ', ', $background_gradient['stops'] );

				switch ( $background_gradient['type'] ) {
					case 'conic':
						$gradient_type      = 'conic';
						$gradient_direction = "from {$background_gradient['direction']} at {$background_gradient['radial_direction']}";
						break;
					case 'elliptical':
						$gradient_type      = 'radial';
						$gradient_direction = "ellipse at {$background_gradient['radial_direction']}";
						break;
					case 'radial':
					case 'circular':
						$gradient_type      = 'radial';
						$gradient_direction = "circle at {$background_gradient['radial_direction']}";
						break;
					case 'linear':
					default:
						$gradient_type      = 'linear';
						$gradient_direction = $background_gradient['direction'];
				}

				// Apply gradient repeat (if set).
				if ( 'on' === $background_gradient['repeat'] ) {
					$gradient_type = 'repeating-' . $gradient_type;
				}

				$background_images[] = "{$gradient_type}-gradient( {$gradient_direction}, {$gradient_stops} )";
			}

			if ( '' !== $background_img && 'on' !== $parallax_method ) {
				$has_background_image = true;

				$background_images[] = sprintf(
					'url(%s)',
					esc_attr( $background_img )
				);

				if ( '' !== $background_size ) {
					$image_size_style = et_pb_background_options()->get_background_size_css( $background_size, $background_image_width, $background_image_height );

					if ( isset( $image_size_style['size'] ) && $image_size_style['size'] ) {
						$el_style = array(
							'selector'    => '%%order_class%%',
							'declaration' => sprintf(
								'background-size:%s;',
								esc_attr( $image_size_style['size'] )
							),
						);

						ET_Builder_Element::set_style( $function_name, $el_style );
					}
				}

				// Check if image size has 'stretch' value or not.
				$is_image_size_stretch = 'stretch' === $background_size;

				// Image Repeat.
				if ( '' !== $background_repeat && ! $is_image_size_stretch ) {
					$el_style = array(
						'selector'    => '%%order_class%%',
						'declaration' => sprintf(
							'background-repeat:%s;',
							esc_attr( $background_repeat )
						),
					);
					ET_Builder_Element::set_style( $function_name, $el_style );
				}

				// Check if image repeat has 'space' value or not.
				$is_image_repeat_space = 'space' === $background_repeat;

				// Image Position.
				if (
					! $is_image_size_stretch
					&& ! $is_image_repeat_space
					&& ( '' !== $background_position || $background_horizontal_offset || $background_vertical_offset )
				) {
					$image_position_style = et_pb_background_options()->get_background_position_css( $background_position, $background_horizontal_offset, $background_vertical_offset );
					$el_style_position    = array(
						'selector'    => '%%order_class%%',
						'declaration' => sprintf(
							'background-position:%1$s; ',
							esc_attr( $image_position_style['position'] )
						),
					);
					ET_Builder_Element::set_style( $function_name, $el_style_position );
				}

				if ( '' !== $background_blend ) {
					$el_style = array(
						'selector'    => '%%order_class%%',
						'declaration' => sprintf(
							'background-blend-mode:%s;',
							esc_attr( $background_blend )
						),
					);
					ET_Builder_Element::set_style( $function_name, $el_style );
				}
			}

			if ( ! empty( $background_images ) ) {
				if ( 'on' !== $background_gradient_overlays_image ) {
					// The browsers stack the images in the opposite order to what you'd expect.
					$background_images = array_reverse( $background_images );
				}

				// Check if Parallax and Gradient Overlays are on.
				if ( 'on' === $background_gradient['active'] && 'on' === $background_gradient_overlays_image && $is_parallax ) {
					// Set background image to initial when Parallax and Gradient Overlays are on.
					$image_style = 'initial';

					$is_gradient_on = true;
				} else {
					// Set background image styles only it's different compared to the larger device.
					$image_style = implode( ', ', $background_images );
				}

				$background_images_declaration = sprintf(
					'background-image: %1$s;',
					esc_html( $image_style )
				);

				$el_style = array(
					'selector'    => '%%order_class%%',
					'declaration' => esc_attr( $background_images_declaration ),
				);
				ET_Builder_Element::set_style( $function_name, $el_style );
			}

			if ( '' !== $background_color && 'rgba(0,0,0,0)' !== $background_color && ! isset( $has_background_gradient, $has_background_image ) ) {
				$el_style = array(
					'selector'    => '%%order_class%%',
					'declaration' => sprintf(
						'background-color:%s;',
						esc_attr( $background_color )
					),
				);
				ET_Builder_Element::set_style( $function_name, $el_style );

				if ( $background_color_hover && $background_color_hover_enabled ) {
					$el_style = array(
						'selector'    => '%%order_class%%:hover',
						'declaration' => sprintf(
							'background-color:%s;',
							esc_attr( $background_color )
						),
					);
					ET_Builder_Element::set_style( $function_name, $el_style );
				}

				// Sticky.
				if ( $background_color_sticky && $background_color_sticky_enabled ) {
					$el_style = array(
						'selector'    => $sticky->add_sticky_to_order_class(
							'%%order_class%%',
							$this->is_sticky_module
						),
						'declaration' => sprintf(
							'background-color:%s;',
							esc_attr( $background_color_sticky )
						),
					);
					ET_Builder_Element::set_style( $function_name, $el_style );

					// Add hover styles in sticky state.
					if ( $background_color_hover && $background_color_hover_enabled ) {
						$el_style = array(
							'selector'    => $sticky->add_sticky_to_order_class(
								'%%order_class%%:hover',
								$this->is_sticky_module
							),
							'declaration' => sprintf(
								'background-color:%s;',
								esc_attr( $background_color_hover )
							),
						);
						ET_Builder_Element::set_style( $function_name, $el_style );
					}
				}
			} elseif ( isset( $has_background_gradient, $has_background_image ) ) {
				// Force background-color: initial.
				$el_style = array(
					'selector'    => '%%order_class%%',
					'declaration' => 'background-color: initial;',
				);
				ET_Builder_Element::set_style( $function_name, $el_style );
			}

			// Pattern Style.
			$pattern_selector  = $this->add_suffix_to_selectors( ' > .et_pb_background_pattern', '%%order_class%%' );
			$is_pattern_active = 'off' !== $enable_pattern_style && '' !== $pattern_style_name;

			if ( $is_pattern_active ) {
				$style_pattern = '';

				// Pattern Transform.
				$pattern_is_horizontal = $background_options->get_transform_state( $pattern_transform, 'horizontal' );
				$pattern_is_vertical   = $background_options->get_transform_state( $pattern_transform, 'vertical' );
				$pattern_is_rotated    = $background_options->get_transform_state( $pattern_transform, 'rotate' );
				$pattern_is_inverted   = $background_options->get_transform_state( $pattern_transform, 'invert' );

				if ( '' !== $pattern_transform && ( $pattern_is_horizontal || $pattern_is_vertical ) ) {
					$style_pattern .= sprintf(
						'transform: %s;',
						esc_attr( esc_html( $background_options->get_transform_css( $pattern_is_horizontal, $pattern_is_vertical ) ) )
					);
				};

				// Pattern Image.
				$pattern_style_svg = et_pb_background_pattern_options()->get_svg( $pattern_style_name, $pattern_color, 'default', $pattern_is_rotated, $pattern_is_inverted );

				$style_pattern .= sprintf(
					'background-image: %s;',
					esc_html( $pattern_style_svg )
				);

				// Pattern Size.
				if ( '' !== $pattern_size && $pattern_size !== $pattern_size_default ) {
					$pattern_size_style = $background_options->get_background_size_css( $pattern_size, $pattern_width, $pattern_height );

					if ( isset( $pattern_size_style['size'] ) && $pattern_size_style['size'] ) {
						$style_pattern .= sprintf(
							'background-size: %1$s; ',
							esc_html( $pattern_size_style['size'] )
						);
					}
				}

				// Check if pattern size has 'stretch' value or not.
				$is_pattern_size_stretch = 'stretch' === $pattern_size;

				// Pattern Repeat.
				if ( '' !== $pattern_repeat && $pattern_repeat !== $pattern_repeat_default && ! $is_pattern_size_stretch ) {
					$style_pattern .= sprintf(
						'background-repeat: %1$s; ',
						esc_html( $pattern_repeat )
					);
				}

				// Check if pattern repeat has 'space' value or not.
				$is_pattern_repeat_space  = 'space' === $pattern_repeat;
				$has_pattern_repeat_value = '' !== $pattern_repeat_origin && $pattern_repeat_origin !== $pattern_repeat_origin_default;

				// Pattern Repeat Origin.
				if (
					! $is_pattern_size_stretch
					&& ! $is_pattern_repeat_space
					&& ( $has_pattern_repeat_value || $pattern_horizontal_offset || $pattern_vertical_offset )
				) {
					$pattern_position_style = $background_options->get_background_position_css( $pattern_repeat_origin, $pattern_horizontal_offset, $pattern_vertical_offset );
					$style_pattern         .= sprintf(
						'background-position: %1$s; ',
						esc_html( $pattern_position_style['position'] )
					);
				}

				// Pattern Blend Mode.
				if ( '' !== $pattern_blend_mode && $pattern_blend_mode !== $pattern_blend_mode_default ) {
					$style_pattern .= sprintf(
						'mix-blend-mode: %1$s; ',
						esc_html( $pattern_blend_mode )
					);
				}

				// Output Pattern Style.
				if ( '' !== $style_pattern ) {
					$el_pattern_style = array(
						'selector'    => $pattern_selector,
						'declaration' => rtrim( $style_pattern ),
					);
					ET_Builder_Element::set_style( $function_name, $el_pattern_style );
				}
			}

			// Mask Style.
			$mask_selector  = $this->add_suffix_to_selectors( ' > .et_pb_background_mask', '%%order_class%%' );
			$is_mask_active = 'off' !== $enable_mask_style && '' !== $mask_style_name;

			if ( $is_mask_active ) {
				$style_mask = '';

				// Mask Transform.
				$mask_is_horizontal = $background_options->get_transform_state( $mask_transform, 'horizontal' );
				$mask_is_vertical   = $background_options->get_transform_state( $mask_transform, 'vertical' );
				$mask_is_rotated    = $background_options->get_transform_state( $mask_transform, 'rotate' );
				$mask_is_inverted   = $background_options->get_transform_state( $mask_transform, 'invert' );

				if ( '' !== $mask_transform && ( $mask_is_horizontal || $mask_is_vertical ) ) {
					$style_mask .= sprintf(
						'transform: %s;',
						esc_attr( esc_html( $background_options->get_transform_css( $mask_is_horizontal, $mask_is_vertical ) ) )
					);
				};

				// Mask Size.
				if ( '' !== $mask_size && $mask_size !== $mask_size_default ) {
					$mask_size_style = $background_options->get_background_size_css( $mask_size, $mask_width, $mask_height, $mask_size_default, 'mask' );

					if ( isset( $mask_size_style['size'] ) && $mask_size_style['size'] ) {
						$style_mask .= sprintf(
							'background-size: %1$s; ',
							esc_html( $mask_size_style['size'] )
						);
					}
				}

				// Mask Image.
				$mask_style_svg = et_pb_background_mask_options()->get_svg( $mask_style_name, $mask_color, $mask_aspect_ratio, $mask_is_rotated, $mask_is_inverted, $mask_size );

				$style_mask .= sprintf(
					'background-image: %s;',
					esc_html( $mask_style_svg )
				);

				// Check if mask size has 'stretch' value or not.
				$is_mask_size_stretch    = 'stretch' === $mask_size;
				$has_mask_position_value = '' !== $mask_position && $mask_position !== $mask_position_default;

				// Mask Position.
				if ( ! $is_mask_size_stretch && ( $has_mask_position_value || $mask_horizontal_offset || $mask_vertical_offset ) ) {
					$mask_position_style = $background_options->get_background_position_css( $mask_position, $mask_horizontal_offset, $mask_vertical_offset );
					$style_mask         .= sprintf(
						'background-position: %1$s; ',
						esc_html( $mask_position_style['position'] )
					);
				}

				// Mask Blend Mode.
				if ( '' !== $mask_blend_mode && $mask_blend_mode !== $mask_blend_mode_default ) {
					$style_mask .= sprintf(
						'mix-blend-mode: %1$s; ',
						esc_html( $mask_blend_mode )
					);
				}

				// Output Mask Style.
				if ( '' !== $style_mask ) {
					$el_mask_style = array(
						'selector'    => $mask_selector,
						'declaration' => rtrim( $style_mask ),
					);
					ET_Builder_Element::set_style( $function_name, $el_mask_style );
				}
			}

			if ( ! empty( $padding_values ) ) {
				$padding_hover_enabled   = self::$_->array_get( $padding_values, 'padding-hover-enabled', false );
				$padding_sticky_enabled  = self::$_->array_get( $padding_values, 'padding-sticky-enabled', false );
				$sticky_padding_selector = $sticky->add_sticky_to_selectors( '%%order_class%%', $this->is_sticky_module );

				unset( $padding_values['padding-hover-enabled'], $padding_values['padding-sticky-enabled'] );

				foreach ( $padding_values as $position => $value ) {
					if ( in_array( $position, array( 'padding-top', 'padding-right', 'padding-bottom', 'padding-left' ), true ) && ! empty( $value ) ) {
						$element_style = array(
							'selector'    => '%%order_class%%',
							'declaration' => sprintf(
								'%1$s:%2$s;',
								esc_html( $position ),
								esc_html( et_builder_process_range_value( $value ) )
							),
						);

						// Backward compatibility. Keep Padding on Mobile is deprecated in favour of responsive inputs mechanism for custom padding
						// To ensure that it is compatibility with previous version of Divi, this option is now only used as last resort if no
						// responsive padding value is found,  and padding_mobile value is saved (which is set to off by default).
						if ( in_array( $keep_column_padding_mobile, array( 'on', 'off' ), true ) && 'on' !== $keep_column_padding_mobile && ! $padding_responsive_active ) {
							$element_style['media_query'] = ET_Builder_Element::get_media_query( 'min_width_981' );
						}

						ET_Builder_Element::set_style( $function_name, $element_style );
					}

					// Add padding hover styles.
					if ( $padding_hover_enabled
						&& null !== self::$_->array_get( $padding_values, "{$position}-hover" )
						&& '' !== self::$_->array_get( $padding_values, "{$position}-hover" )
					) {
						$hover_value = $padding_values[ "{$position}-hover" ];

						$element_style = array(
							'selector'    => '%%order_class%%:hover',
							'declaration' => sprintf(
								'%1$s:%2$s;',
								esc_html( $position ),
								esc_html( et_builder_process_range_value( $hover_value ) )
							),
						);

						ET_Builder_Element::set_style( $function_name, $element_style );
					}

					// Sticky.
					$sticky_padding_value = self::$_->array_get( $padding_values, "{$position}-sticky", '' );
					if ( $padding_sticky_enabled && ! empty( $sticky_padding_value ) ) {
						$element_style = array(
							'selector'    => $sticky_padding_selector,
							'declaration' => sprintf(
								'%1$s:%2$s;',
								esc_html( $position ),
								esc_html( et_builder_process_range_value( $sticky_padding_value ) )
							),
						);

						ET_Builder_Element::set_style( $function_name, $element_style );
					}
				}
			}

			if ( $padding_responsive_active && ( ! empty( $padding_mobile_values['tablet'] ) || ! empty( $padding_values['phone'] ) ) ) {
				$padding_mobile_values_processed = array();

				foreach ( array( 'tablet', 'phone' ) as $device ) {
					if ( empty( $padding_mobile_values[ $device ] ) ) {
						continue;
					}

					$padding_mobile_values_processed[ $device ] = array(
						'padding-top'    => isset( $padding_mobile_values[ $device ][0] ) ? $padding_mobile_values[ $device ][0] : '',
						'padding-right'  => isset( $padding_mobile_values[ $device ][1] ) ? $padding_mobile_values[ $device ][1] : '',
						'padding-bottom' => isset( $padding_mobile_values[ $device ][2] ) ? $padding_mobile_values[ $device ][2] : '',
						'padding-left'   => isset( $padding_mobile_values[ $device ][3] ) ? $padding_mobile_values[ $device ][3] : '',
					);
				}

				if ( ! empty( $padding_mobile_values_processed ) ) {
					$padding_mobile_selector = 'et_pb_column_inner' !== $function_name ? '.et_pb_row > .et_pb_column%%order_class%%' : '.et_pb_row_inner > .et_pb_column%%order_class%%';
					et_pb_responsive_options()->generate_responsive_css( $padding_mobile_values_processed, $padding_mobile_selector, '', $function_name );
				}
			}
			if ( '' !== $custom_css_before ) {
				$el_style = array(
					'selector'    => '%%order_class%%:before',
					'declaration' => trim( $custom_css_before ),
				);
				ET_Builder_Element::set_style( $function_name, $el_style );
			}

			if ( '' !== $custom_css_main ) {
				$el_style = array(
					'selector'    => '%%order_class%%',
					'declaration' => trim( $custom_css_main ),
				);
				ET_Builder_Element::set_style( $function_name, $el_style );
			}

			if ( '' !== $custom_css_after ) {
				$el_style = array(
					'selector'    => '%%order_class%%:after',
					'declaration' => trim( $custom_css_after ),
				);
				ET_Builder_Element::set_style( $function_name, $el_style );
			}

			if ( '' !== $custom_css_before_hover ) {
				$el_style = array(
					'selector'    => '%%order_class%%:hover:before',
					'declaration' => trim( $custom_css_before_hover ),
				);
				ET_Builder_Element::set_style( $function_name, $el_style );
			}

			if ( '' !== $custom_css_main_hover ) {
				$el_style = array(
					'selector'    => '%%order_class%%:hover',
					'declaration' => trim( $custom_css_main_hover ),
				);
				ET_Builder_Element::set_style( $function_name, $el_style );
			}

			if ( '' !== $custom_css_after_hover ) {
				$el_style = array(
					'selector'    => '%%order_class%%:hover:after',
					'declaration' => trim( $custom_css_after_hover ),
				);
				ET_Builder_Element::set_style( $function_name, $el_style );
			}

			// Sticky.
			if ( ! empty( $custom_css_before_sticky ) ) {
				$el_style = array(
					'selector'    => $sticky->add_sticky_to_order_class(
						'%%order_class%%:before',
						$this->is_sticky_module
					),
					'declaration' => trim( $custom_css_before_sticky ),
				);
				ET_Builder_Element::set_style( $function_name, $el_style );

				// Add hover styles in sticky state, in case some hover styles are overridden with sticky styles.
				if ( ! empty( $custom_css_before_hover ) ) {
					$el_style = array(
						'selector'    => $sticky->add_sticky_to_order_class(
							'%%order_class%%:hover:before',
							$this->is_sticky_module
						),
						'declaration' => trim( $custom_css_before_hover ),
					);
					ET_Builder_Element::set_style( $function_name, $el_style );
				}
			}

			if ( ! empty( $custom_css_main_sticky ) ) {
				$el_style = array(
					'selector'    => $sticky->add_sticky_to_order_class(
						'%%order_class%%',
						$this->is_sticky_module
					),
					'declaration' => trim( $custom_css_main_sticky ),
				);
				ET_Builder_Element::set_style( $function_name, $el_style );

				// Add hover styles in sticky state, in case some hover styles are overridden with sticky styles.
				if ( ! empty( $custom_css_main_hover ) ) {
					$el_style = array(
						'selector'    => $sticky->add_sticky_to_order_class(
							'%%order_class%%:hover',
							$this->is_sticky_module
						),
						'declaration' => trim( $custom_css_main_hover ),
					);
					ET_Builder_Element::set_style( $function_name, $el_style );
				}
			}

			if ( ! empty( $custom_css_after_sticky ) ) {
				$el_style = array(
					'selector'    => $sticky->add_sticky_to_order_class(
						'%%order_class%%:after',
						$this->is_sticky_module
					),
					'declaration' => trim( $custom_css_after_sticky ),
				);
				ET_Builder_Element::set_style( $function_name, $el_style );

				// Add hover styles in sticky state, in case some hover styles are overridden with sticky styles.
				if ( ! empty( $custom_css_after_hover ) ) {
					$el_style = array(
						'selector'    => $sticky->add_sticky_to_order_class(
							'%%order_class%%:hover:after',
							$this->is_sticky_module
						),
						'declaration' => trim( $custom_css_after_hover ),
					);
					ET_Builder_Element::set_style( $function_name, $el_style );
				}
			}
		}

		if ( 'et_pb_column_inner' === $function_name ) {
			if ( '1_1' === $type ) {
				$type = '4_4';
			}

			$et_specialty_column_type = '' !== $saved_specialty_column_type ? $saved_specialty_column_type : $et_specialty_column_type;

			switch ( $et_specialty_column_type ) {
				case '1_2':
					if ( '1_2' === $type ) {
						$type = '1_4';
					}
					if ( '1_3' === $type ) {
						$type = '1_6';
					}

					break;
				case '2_3':
					if ( '1_3' === $type ) {
						$type = '2_9';
					}

					if ( '1_2' === $type ) {
						$type = '1_3';
					}

					if ( '1_4' === $type ) {
						$type = '1_6';
					}

					break;
				case '3_4':
					if ( '1_2' === $type ) {
						$type = '3_8';
					} elseif ( '1_3' === $type ) {
						$type = '1_4';
					}

					break;
			}
		}

		$video_background = '';
		$parallax_image   = '';

		// Column background video.
		if ( $is_specialty_column ) {
			$video_background = trim( $this->video_background( $background_video ) );
			if ( '' !== $background_img && '' !== $parallax_method ) {
				$background_gradient_style = $is_gradient_on
					? sprintf(
						'background-image: %1$s;',
						esc_html( $background_images[0] )
					) : '';
				$background_gradient_blend = '' !== $background_blend && 'normal' !== $background_blend
					? sprintf(
						'mix-blend-mode: %1$s;',
						esc_html( $background_blend )
					) : '';
				$parallax_gradient         = sprintf(
					'<div
						class="et_parallax_gradient%1$s"
						style="%2$s%3$s"
					></div>',
					( 'off' === $parallax_method ? ' et_pb_parallax_css' : '' ),
					$background_gradient_style,
					$background_gradient_blend
				);

				$parallax_image = sprintf(
					'<div class="et_parallax_bg_wrap">
						<div class="et_parallax_bg%2$s" style="background-image: url(%1$s);"></div>
						%3$s
					</div>',
					esc_attr( $background_img ),
					( 'off' === $parallax_method ? ' et_pb_parallax_css' : '' ),
					$is_gradient_on ? et_core_esc_previously( $parallax_gradient ) : ''
				);
			}

			if ( '' !== $parallax_method ) {
				$this->add_classname( 'et_pb_section_parallax' );
			}
		} else {
			$video_background = trim( $this->video_background() );
			$parallax_image   = $this->get_parallax_image_background();
		}

		// Remove automatically added classname.
		$this->remove_classname( 'et_pb_module' );

		$this->add_classname( 'et_pb_column_' . $type, 1 );

		if ( '' !== $custom_css_class ) {
			$this->add_classname( $custom_css_class );
		}

		if ( $is_specialty_column && '' !== $specialty_columns ) {
			$this->add_classname( 'et_pb_specialty_column' );
		}

		// CSS Filters.
		$this->add_classname( $this->generate_css_filters( $function_name ) );

		if ( '' !== $video_background ) {
			$this->add_classname(
				array(
					'et_pb_section_video',
					'et_pb_preload',
				)
			);
		}

		if ( $is_last_column ) {
			$this->add_classname( 'et-last-child' );
		}

		if ( self::contains( $content, array( 'et_pb_menu', 'et_pb_fullwidth_menu' ) ) ) {
			$this->add_classname( 'et_pb_column--with-menu' );
		}

		// Module classname in column has to be contained in variable BEFORE content is being parsed
		// as shortcode because column and column inner use the same ET_Builder_Column's render
		// classname doesn't work in nested situation because each called module doesn't have its own class init.
		$module_classname = $this->module_classname( $function_name );

		// Inner content shortcode parsing has to be done after all classname addition/removal.
		$inner_content = do_shortcode( et_pb_fix_shortcodes( $content ) );

		// Inner content dependant class in column shouldn't use add_classname/remove_classname method.
		$content_dependent_classname = '' === trim( $inner_content ) ? ' et_pb_column_empty' : '';

		if ( $is_specialty_column ) {
			$pattern_args = array(
				'background_enable_pattern_style' => $enable_pattern_style,
				'background_pattern_style'        => $pattern_style_name,
			);
			$mask_args    = array(
				'background_enable_mask_style' => $enable_mask_style,
				'background_mask_style'        => $mask_style_name,
				'background_mask_size'         => $mask_size,
			);

			$output = sprintf(
				'<div class="%1$s%6$s"%4$s>
				%3$s
				%5$s
				%7$s
				%8$s
				%2$s
			</div>',
				$module_classname,
				$inner_content,
				$parallax_image,
				'' !== $custom_css_id ? sprintf( ' id="%1$s"', esc_attr( $custom_css_id ) ) : '', // 5
				$video_background,
				$content_dependent_classname,
				et_core_esc_previously( $this->background_pattern( $pattern_args ) ), // 7
				et_core_esc_previously( $this->background_mask( $mask_args ) ) // 8
			);
		} else {
			$output = sprintf(
				'<div class="%1$s%6$s"%4$s>
				%3$s
				%5$s
				%7$s
				%8$s
				%2$s
			</div>',
				$module_classname,
				$inner_content,
				$parallax_image,
				'' !== $custom_css_id ? sprintf( ' id="%1$s"', esc_attr( $custom_css_id ) ) : '', // 5
				$video_background,
				$content_dependent_classname,
				et_core_esc_previously( $this->background_pattern() ), // 7
				et_core_esc_previously( $this->background_mask() ) // 8
			);
		}

		return $output;

	}

}
new ET_Builder_Column();

/**
 * Fires after the builder's structure element classes are loaded.
 *
 * @since 4.4.9
 */
do_action( 'et_builder_structure_elements_loaded' );
