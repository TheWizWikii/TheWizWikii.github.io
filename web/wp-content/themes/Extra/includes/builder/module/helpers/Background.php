<?php
/**
 * Background helper Class.
 *
 * @package Divi
 * @subpackage Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}


/**
 * Background helper methods.
 *
 * Intended for module that needs to extend module background mechanism with few modification
 * (eg. post slider which needs to apply module background on individual slide that has featured
 * image).
 *
 * @since 4.3.3
 * @since 4.6.0 Add sticky style support
 * @since 4.15.0 Add pattern/mask style support.
 *
 * Class ET_Builder_Module_Helper_Background
 */
class ET_Builder_Module_Helper_Background {
	/**
	 * Returns instance of the class.
	 *
	 * @return ET_Builder_Module_Helper_Background
	 */
	public static function instance() {
		static $instance;

		return $instance ? $instance : $instance = new self();
	}

	/**
	 * Get prop name alias. Some background settings (eg. button's gradient background enable) might
	 * use slightly different prop name to store background config;
	 *
	 * @since 4.6.0
	 *
	 * @param array  $aliases   Aliases.
	 * @param string $prop_name Prop name.
	 *
	 * @return string
	 */
	public function get_prop_name_alias( $aliases = array(), $prop_name = '' ) {
		// If no aliases given, simply return the prop name because it has no alias.
		if ( empty( $aliases ) ) {
			return $prop_name;
		}

		return isset( $aliases[ $prop_name ] ) ? $aliases[ $prop_name ] : $prop_name;
	}

	/**
	 * Get gradient properties for hover mode
	 *
	 * @since 4.3.3
	 * @since 4.6.0 add capability to look for sticky style's gradient
	 * @since 4.16.0   Uses the `_stops` field introduced in the Gradient Builder update.
	 *
	 * @param string $mode                        Mode name.
	 * @param array  $props                       Module's props.
	 * @param string $base_prop_name              Background base prop name.
	 * @param array  $gradient_properties_desktop {
	 *     @type string $mode
	 *     @type string $stops
	 *     @type string $type
	 *     @type string $direction
	 *     @type string $radial_direction
	 *     @type string $color_start
	 *     @type string $color_end
	 *     @type string $start_position
	 *     @type string $end_position
	 * }
	 *
	 * @return array|false
	 */
	public function get_gradient_mode_properties( $mode, $props, $base_prop_name, $gradient_properties_desktop = array() ) {
		$helper = et_builder_get_helper( $mode );

		if ( ! $mode ) {
			return false;
		}

		// Desktop value as default.
		$gradient_repeat_desktop           = et_pb_responsive_options()->get_any_value( $props, "{$base_prop_name}_color_repeat_image", '', true );
		$gradient_type_desktop             = et_()->array_get( $gradient_properties_desktop, 'type', '' );
		$gradient_direction_desktop        = et_()->array_get( $gradient_properties_desktop, 'direction', '' );
		$gradient_radial_direction_desktop = et_()->array_get( $gradient_properties_desktop, 'radial_direction', '' );
		$gradient_stops_desktop            = et_()->array_get( $gradient_properties_desktop, 'stops', '' );
		$gradient_unit_desktop             = et_()->array_get( $gradient_properties_desktop, 'unit', '' );
		$gradient_overlays_image_desktop   = et_pb_responsive_options()->get_any_value( $props, "{$base_prop_name}_color_gradient_overlays_image", '', true );

		$gradient_color_start_desktop    = et_()->array_get( $gradient_properties_desktop, 'color_start', '' );
		$gradient_color_end_desktop      = et_()->array_get( $gradient_properties_desktop, 'color_end', '' );
		$gradient_start_position_desktop = et_()->array_get( $gradient_properties_desktop, 'start_position', '' );
		$gradient_end_position_desktop   = et_()->array_get( $gradient_properties_desktop, 'end_position', '' );

		// Mode value.
		$gradient_repeat_mode           = $helper->get_raw_value( "{$base_prop_name}_color_repeat_image", $props, $gradient_repeat_desktop );
		$gradient_type_mode             = $helper->get_raw_value( "{$base_prop_name}_color_gradient_type", $props, $gradient_type_desktop );
		$gradient_direction_mode        = $helper->get_raw_value( "{$base_prop_name}_color_gradient_direction", $props, $gradient_direction_desktop );
		$gradient_direction_radial_mode = $helper->get_raw_value( "{$base_prop_name}_color_gradient_direction_radial", $props, $gradient_radial_direction_desktop );
		$gradient_stops_mode            = $helper->get_raw_value( "{$base_prop_name}_color_gradient_stops", $props, $gradient_stops_desktop );
		$gradient_unit_mode             = $helper->get_raw_value( "{$base_prop_name}_color_gradient_unit", $props, $gradient_unit_desktop );
		$gradient_overlays_image_mode   = $helper->get_raw_value( "{$base_prop_name}_color_gradient_overlays_image", $props, $gradient_overlays_image_desktop );

		$gradient_start_mode          = $helper->get_raw_value( "{$base_prop_name}_color_gradient_start", $props, $gradient_color_start_desktop );
		$gradient_end_mode            = $helper->get_raw_value( "{$base_prop_name}_color_gradient_end", $props, $gradient_color_end_desktop );
		$gradient_start_position_mode = $helper->get_raw_value( "{$base_prop_name}_color_gradient_start_position", $props, $gradient_start_position_desktop );
		$gradient_end_position_mode   = $helper->get_raw_value( "{$base_prop_name}_color_gradient_end_position", $props, $gradient_end_position_desktop );

		$color_start_value = '' !== $gradient_start_mode ? $gradient_start_mode : $gradient_color_start_desktop;
		$color_end_value   = '' !== $gradient_end_mode ? $gradient_end_mode : $gradient_color_end_desktop;

		return array(
			'repeat'           => '' !== $gradient_repeat_mode ? $gradient_repeat_mode : $gradient_repeat_desktop,
			'type'             => '' !== $gradient_type_mode ? $gradient_type_mode : $gradient_type_desktop,
			'direction'        => '' !== $gradient_direction_mode ? $gradient_direction_mode : $gradient_direction_desktop,
			'radial_direction' => '' !== $gradient_direction_radial_mode ? $gradient_direction_radial_mode : $gradient_radial_direction_desktop,
			'stops'            => '' !== $gradient_stops_mode ? $gradient_stops_mode : $gradient_stops_desktop,
			'color_start'      => '' !== $gradient_start_mode ? $gradient_start_mode : $gradient_color_start_desktop,
			'color_end'        => '' !== $gradient_end_mode ? $gradient_end_mode : $gradient_color_end_desktop,
			'start_position'   => '' !== $gradient_start_position_mode ? $gradient_start_position_mode : $gradient_start_position_desktop,
			'end_position'     => '' !== $gradient_end_position_mode ? $gradient_end_position_mode : $gradient_end_position_desktop,
			'unit'             => '' !== $gradient_unit_mode ? $gradient_unit_mode : $gradient_stops_desktop,
		);
	}

	/**
	 * Get gradient properties based on given props
	 *
	 * @since 4.3.3
	 * @since 4.16.0   Uses the `_stops` field introduced in the Gradient Builder update.
	 *
	 * @param array  $props          Module's props.
	 * @param string $base_prop_name Background base prop name.
	 * @param string $suffix         Background base prop name's suffix.
	 *
	 * @return array
	 */
	public function get_gradient_properties( $props, $base_prop_name, $suffix ) {
		return array(
			'repeat'           => et_pb_responsive_options()->get_any_value( $props, "{$base_prop_name}_color_gradient_repeat{$suffix}", '', true ),
			'type'             => et_pb_responsive_options()->get_any_value( $props, "{$base_prop_name}_color_gradient_type{$suffix}", '', true ),
			'direction'        => et_pb_responsive_options()->get_any_value( $props, "{$base_prop_name}_color_gradient_direction{$suffix}", '', true ),
			'radial_direction' => et_pb_responsive_options()->get_any_value( $props, "{$base_prop_name}_color_gradient_direction_radial{$suffix}", '', true ),
			'stops'            => et_pb_responsive_options()->get_any_value( $props, "{$base_prop_name}_color_gradient_stops{$suffix}", '', true ),
			'unit'             => et_pb_responsive_options()->get_any_value( $props, "{$base_prop_name}_color_gradient_unit{$suffix}", '', true ),
			'color_start'      => et_pb_responsive_options()->get_any_value( $props, "{$base_prop_name}_color_gradient_start{$suffix}", '', true ),
			'color_end'        => et_pb_responsive_options()->get_any_value( $props, "{$base_prop_name}_color_gradient_end{$suffix}", '', true ),
			'start_position'   => et_pb_responsive_options()->get_any_value( $props, "{$base_prop_name}_color_gradient_start_position{$suffix}", '', true ),
			'end_position'     => et_pb_responsive_options()->get_any_value( $props, "{$base_prop_name}_color_gradient_end_position{$suffix}", '', true ),
		);
	}

	/**
	 * Get background gradient style based on properties given
	 *
	 * @since 4.3.3
	 * @since 4.16.0   Uses the `_stops` field introduced in the Gradient Builder update.
	 *
	 * @param array $args {
	 *     @type string $repeat           Whether the gradient stops repeat.
	 *     @type string $type             Linear or radial gradient.
	 *     @type string $direction        The gradient line's angle of direction.
	 *     @type string $radial_direction The position of the gradient.
	 *     @type string $stops            Brace-delimited list of color stops.
	 *     @type string $color_start      Deprecated.
	 *     @type string $color_end        Deprecated.
	 *     @type string $start_position   Deprecated.
	 *     @type string $end_position     Deprecated.
	 * }
	 *
	 * @return string
	 */
	public function get_gradient_style( $args ) {
		$default_gradient = array(
			'repeat'           => ET_Global_Settings::get_value( 'all_background_gradient_repeat' ),
			'type'             => ET_Global_Settings::get_value( 'all_background_gradient_type' ),
			'direction'        => ET_Global_Settings::get_value( 'all_background_gradient_direction' ),
			'radial_direction' => ET_Global_Settings::get_value( 'all_background_gradient_direction_radial' ),
			'stops'            => ET_Global_Settings::get_value( 'all_background_gradient_stops' ),
		);

		$defaults = apply_filters( 'et_pb_default_gradient', $default_gradient );
		$args     = wp_parse_args( array_filter( $args ), $defaults );
		$stops    = str_replace( '|', ', ', $args['stops'] );

		$stops = $this->get_color_value( $stops );

		switch ( $args['type'] ) {
			case 'conic':
				$type      = 'conic';
				$direction = "from {$args['direction']} at {$args['radial_direction']}";
				break;
			case 'elliptical':
				$type      = 'radial';
				$direction = "ellipse at {$args['radial_direction']}";
				break;
			case 'radial':
			case 'circular':
				$type      = 'radial';
				$direction = "circle at {$args['radial_direction']}";
				break;
			case 'linear':
			default:
				$type      = 'linear';
				$direction = $args['direction'];
		}

		// Apply gradient repeat (if set).
		if ( 'on' === $args['repeat'] ) {
			$type = "repeating-{$type}";
		}

		return esc_html(
			"{$type}-gradient( {$direction}, {$stops} )"
		);
	}

	/**
	 * Get individual background image style
	 *
	 * @since 4.3.3
	 * @since 4.15.0 deprecated
	 *
	 * @deprecated Use {@see get_attr_value} instead.
	 *
	 * @param string $attr                 Background attribute name.
	 * @param string $base_prop_name       Base background prop name.
	 * @param string $suffix               Attribute name suffix.
	 * @param array  $props                Module props.
	 * @param array  $fields_definition    Module's fields definition.
	 * @param bool   $is_prev_image_active Whether previous background image is active or not.
	 *
	 * @return string
	 */
	public function get_image_style( $attr, $base_prop_name, $suffix = '', $props = array(), $fields_definition = array(), $is_prev_image_active = true ) {
		return $this->get_attr_value( $attr, $base_prop_name, $suffix = '', $props, $fields_definition, $is_prev_image_active );
	}

	/**
	 * Get individual default value.
	 *
	 * @since 4.15.0
	 *
	 * @param string $attr                  Background attribute name.
	 * @param string $base_prop_name        Base background prop name.
	 * @param array  $fields_definition     Module's fields definition.
	 * @param bool   $is_prev_device_active Whether previous background image is active or not.
	 *
	 * @return string
	 */
	public function get_attr_default( $attr, $base_prop_name, $fields_definition = array(), $is_prev_device_active = true ) {
		$default = isset( $fields_definition[ "{$base_prop_name}_{$attr}" ]['default'] )
			? $fields_definition[ "{$base_prop_name}_{$attr}" ]['default']
			: '';

		return $is_prev_device_active ? $default : '';
	}

	/**
	 * Get individual background attribute value.
	 *
	 * @since 4.15.0
	 *
	 * @param string $attr                 Background attribute name.
	 * @param string $base_prop_name       Base background prop name.
	 * @param string $suffix               Attribute name suffix.
	 * @param array  $props                Module props.
	 * @param array  $fields_definition    Module's fields definition.
	 * @param bool   $is_prev_device_active  Whether is active in previous device or not.
	 *
	 * @return string
	 */
	public function get_attr_value( $attr, $base_prop_name, $suffix = '', $props = array(), $fields_definition = array(), $is_prev_device_active = true ) {
		$default = $this->get_attr_default( $attr, $base_prop_name, $fields_definition, $is_prev_device_active );

		return et_pb_responsive_options()->get_any_value( $props, "{$base_prop_name}_{$attr}{$suffix}", $default, ! $is_prev_device_active );
	}

	/**
	 * Return CSS for Transform State.
	 *
	 * @since 4.15.0
	 *
	 * @param string $values Transform states.
	 * @param string $state  Query state, valid options: horizontal | vertical | rotate | invert.
	 *
	 * @return string
	 */
	public function get_transform_state( $values, $state ) {
		$flip = '' !== $values ? explode( '|', $values ) : array();

		switch ( $state ) {
			case 'horizontal':
				$result = in_array( 'flip_horizontal', $flip, true );
				break;
			case 'invert':
				$result = in_array( 'invert', $flip, true );
				break;
			case 'rotate':
				$result = in_array( 'rotate_90_degree', $flip, true );
				break;
			case 'vertical':
				$result = in_array( 'flip_vertical', $flip, true );
				break;
			default:
				$result = false;
		}

		return $result;
	}

	/**
	 * Return CSS for Transform State.
	 *
	 * We use `scale` here because CSS Transform's `rotateX`/`rotateY` trigger a 10+
	 * year old Safari bug that hides rotated background images (including SVGs).
	 *
	 * @see https://bugs.webkit.org/show_bug.cgi?id=61824
	 *
	 * @since 4.15.0
	 *
	 * @param bool $horizontal Horizontal state.
	 * @param bool $vertical   Vertical state.
	 *
	 * @return string
	 */
	public function get_transform_css( $horizontal, $vertical ) {
		$flip_h = $horizontal ? '-1' : '1';
		$flip_v = $vertical ? '-1' : '1';

		return "scale($flip_h, $flip_v)";
	}

	/**
	 * Helper function to return CSS for BackgroundPosition.
	 *
	 * @since 4.15.0
	 *
	 * @param string $position Position.
	 * @param string $horizontal_offset Horizontal Offset.
	 * @param string $vertical_offset Vertical Offset.
	 * @param string $position_default Default Position.
	 *
	 * @return array
	 */
	public function get_background_position_css( $position, $horizontal_offset, $vertical_offset, $position_default = '' ) {
		$position_value          = empty( $position ) ? $position_default : $position;
		$horizontal_offset_value = et_builder_process_range_value( $horizontal_offset );
		$vertical_offset_value   = et_builder_process_range_value( $vertical_offset );
		$position_array          = explode( '_', $position_value );
		$output                  = array();

		// Vertical Offset.
		if ( isset( $position_array[0] ) ) {
			switch ( $position_array[0] ) {
				case 'top':
					// Top doesn't need suffix when value is 0.
					$output['position-y'] = 0 === (int) $vertical_offset
						? $position_array[0]
						: "{$position_array[0]} {$vertical_offset_value}";
					break;
				case 'bottom':
					$output['position-y'] = "{$position_array[0]} {$vertical_offset_value}";
					break;
				case 'center':
				default:
					$output['position-y'] = 'center';
			}
		}

		// Horizontal Offset.
		if ( isset( $position_array[1] ) ) {
			switch ( $position_array[1] ) {
				case 'left':
					// Left doesn't need suffix when value is 0.
					$output['position-x'] = 0 === (int) $horizontal_offset
						? $position_array[1]
						: "{$position_array[1]} {$horizontal_offset_value}";
					break;
				case 'right':
					$output['position-x'] = "{$position_array[1]} {$horizontal_offset_value}";
					break;
				case 'center':
				default:
					$output['position-x'] = 'center';
			}
		} else {
			// When $position_array[1] is absence.
			$output['position-x'] = 'center';
		}

		// Prepare output for the CSS value.
		$output['position'] = 'center' === $output['position-x'] && 'center' === $output['position-y']
			? 'center'
			: "{$output['position-x']} {$output['position-y']}";

		return $output;
	}

	/**
	 * Helper function to return CSS for BackgroundSize.
	 *
	 * @since 4.15.0
	 *
	 * @param string $size Size.
	 * @param string $width Width.
	 * @param string $height Vertical Offset.
	 * @param string $size_default Default Size.
	 * @param string $type Type, use to handle special case.
	 *
	 * @return array
	 */
	public function get_background_size_css( $size, $width, $height, $size_default = '', $type = '' ) {
		$size_value = '' === $size ? $size_default : $size;
		$output     = array();

		switch ( $size_value ) {
			case 'custom':
				$is_width_auto  = 'auto' === $width || '' === $width || 0 === (int) $width;
				$is_height_auto = 'auto' === $height || '' === $height || 0 === (int) $height;
				$width_value    = $is_width_auto ? 'auto' : et_builder_process_range_value( $width );
				$height_value   = $is_height_auto ? 'auto' : et_builder_process_range_value( $height );

				if ( $is_width_auto && $is_height_auto ) {
					$output['size'] = 'initial';
				} else {
					$output['size'] = "{$width_value} {$height_value}";
				}
				break;
			case 'stretch':
				// For mask, increase 2px to resolve sub-pixel rendering issue in Chrome/Safari.
				$output['size'] = 'mask' === $type
					? 'calc(100% + 2px) calc(100% + 2px)'
					: '100% 100%';
				break;
			case 'cover':
			case 'contain':
			case 'initial':
				$output['size'] = $size_value;
				break;
			default:
				$output['size'] = '';
		}

		return $output;
	}

	/**
	 * Get background UI option's style based on given props and prop name
	 *
	 * @since 4.3.3
	 * @since 4.6.0 Add sticky style support.
	 * @since 4.15.0 Add pattern/mask style support.
	 *
	 * @todo Further simplify this method; Break it down into more encapsulated methods
	 *
	 * @param array $args {
	 *     @type string $base_prop_name
	 *     @type array  $props
	 *     @type string $important
	 *     @type array  $fields_Definition
	 *     @type string $selector
	 *     @type string $selector_hover
	 *     @type string $selector_sticky
	 *     @type number $priority
	 *     @type string $function_name
	 *     @type bool   $has_background_color_toggle
	 *     @type bool   $use_background_color
	 *     @type bool   $use_background_color_gradient
	 *     @type bool   $use_background_image
	 *     @type bool   $use_background_video
	 *     @type bool   $use_background_pattern
	 *     @type bool   $use_background_mask
	 *     @type bool   $use_background_color_reset
	 *     @type bool   $use_background_image_parallax
	 * }
	 */
	public function get_background_style( $args = array() ) {
		// Default settings.
		$defaults = array(
			'base_prop_name'                => 'background',
			'props'                         => array(),
			'important'                     => '',
			'fields_definition'             => array(),
			'selector'                      => '',
			'selector_hover'                => '',
			'selector_sticky'               => '',
			'selector_pattern'              => '',
			'selector_mask'                 => '',
			'priority'                      => '',
			'function_name'                 => '',
			'has_background_color_toggle'   => false,
			'use_background_color'          => true,
			'use_background_color_gradient' => true,
			'use_background_image'          => true,
			'use_background_video'          => true,
			'use_background_color_reset'    => true,
			'use_background_pattern'        => true,
			'use_background_mask'           => true,
			'use_background_image_parallax' => true,
			'prop_name_aliases'             => array(),
		);

		// Parse arguments.
		$args = wp_parse_args( $args, $defaults );

		// Break argument into variables.
		$base_prop_name    = $args['base_prop_name'];
		$props             = $args['props'];
		$important         = $args['important'];
		$fields_definition = $args['fields_definition'];
		$selector          = $args['selector'];
		$selector_pattern  = $args['selector_pattern'];
		$selector_mask     = $args['selector_mask'];
		$priority          = $args['priority'];
		$function_name     = $args['function_name'];

		// Possible values for use_background_* variables are true, false, or 'fields_only'.
		$has_color_toggle_options = $args['has_background_color_toggle'];
		$use_gradient_options     = $args['use_background_color_gradient'];
		$use_image_options        = $args['use_background_image'];
		$use_color_options        = $args['use_background_color'];
		$use_color_reset_options  = $args['use_background_color_reset'];
		$use_pattern_options      = $args['use_background_pattern'];
		$use_mask_options         = $args['use_background_mask'];

		// Prop name aliases. Some background element uses different prop name (eg. button background).
		$prop_name_aliases = $args['prop_name_aliases'];

		// Save processed background. These will be compared with the smaller device background
		// processed value to avoid rendering the same styles.
		$processed_color                 = '';
		$gradient_properties_desktop     = array();
		$gradient_overlays_image_desktop = 'off';

		// Store background images status because the process is extensive.
		$image_status                   = $this->get_default_mode_status();
		$processed_image_size_style     = array( 'size' => $this->get_attr_default( 'size', $base_prop_name, $fields_definition ) );
		$processed_image                = '';
		$processed_image_position_style = '';
		$processed_image_blend_style    = '';

		// Background pattern.
		$pattern_base_prop_name            = "{$base_prop_name}_pattern";
		$pattern_status                    = $this->get_default_mode_status();
		$processed_pattern_transform_style = '';
		$processed_pattern_svg             = '';
		$processed_pattern_size_style      = array( 'size' => $this->get_attr_default( 'size', $pattern_base_prop_name, $fields_definition ) );
		$processed_pattern_position_style  = '';

		// Background mask.
		$mask_base_prop_name            = "{$base_prop_name}_mask";
		$mask_status                    = $this->get_default_mode_status();
		$processed_mask_transform_style = '';
		$processed_mask_svg             = '';
		$processed_mask_size_style      = array( 'size' => $this->get_attr_default( 'size', $mask_base_prop_name, $fields_definition ) );
		$processed_mask_position_style  = '';

		// Helper.
		$feature_manager = ET_Builder_Module_Features::instance();
		$responsive      = et_pb_responsive_options();

		/**
		 * Module Component.
		 *
		 * @var ET_Builder_Element $module
		 */
		$module = isset( $args['module'] ) ? $args['module'] : ET_Builder_Element::get_module( $function_name );

		// Bail, there is nothing to process.
		// Happens when et_pb_posts module don't find a $post to process.
		if ( is_string( $module ) || ! ( $module && $module instanceof ET_Builder_Element ) ) {
			return;
		}

		// We got the module, get unprocessed attrs.
		$attrs_unprocessed = $module->get_attrs_unprocessed();

		// Get dynamic attributes.
		$dynamic_attributes = $this->_get_enabled_dynamic_attributes( $props );

		// Parsed prop name, in case it has aliases.
		$base_prop_name_parsed = $this->get_prop_name_alias( $prop_name_aliases, $base_prop_name );

		// Background Desktop, Tablet, and Phone.
		foreach ( $responsive->get_modes() as $device ) {
			$is_desktop = 'desktop' === $device;
			$suffix     = ! $is_desktop ? "_{$device}" : '';
			$style      = '';

			$style_pattern = '';
			$style_mask    = '';

			// Conditionals.
			$has_gradient           = false;
			$has_image              = false;
			$has_gradient_and_image = false;
			$is_gradient_disabled   = false;
			$is_image_disabled      = false;

			$is_enabled = $feature_manager->get(
			// Is background responsive enabled for $device.
			// keys: bared, baret, barep.
				'bare' . $device[0],
				function() use ( $responsive, $base_prop_name_parsed, $props ) {
					return $responsive->is_responsive_enabled( $props, $base_prop_name_parsed );
				}
			);

			// Ensure responsive settings is enabled on mobile.
			if ( ! $is_desktop && ! $is_enabled ) {
				continue;
			}

			// Styles output.
			$image_style             = '';
			$color_style             = '';
			$images                  = array();
			$gradient_overlays_image = 'off';

			$image_size_style        = '';
			$image_position_style    = '';
			$pattern_transform_style = '';
			$pattern_style_svg       = '';
			$pattern_size_style      = '';
			$pattern_position_style  = '';
			$mask_transform_style    = '';
			$mask_style_svg          = '';
			$mask_size_style         = '';
			$mask_position_style     = '';

			// A. Background Gradient.
			if ( $use_gradient_options && 'fields_only' !== $use_gradient_options ) {
				$use_gradient = $responsive->get_inheritance_background_value(
					$props,
					$this->get_prop_name_alias( $prop_name_aliases, "use_{$base_prop_name}_color_gradient" ),
					$device,
					$base_prop_name,
					$fields_definition
				);

				// 1. Ensure gradient color is active.
				if ( 'on' === $use_gradient ) {
					$gradient_overlays_image = $responsive->get_any_value( $props, "{$base_prop_name}_color_gradient_overlays_image{$suffix}", '', true );
					$gradient_properties     = $this->get_gradient_properties( $props, $base_prop_name, $suffix );

					// Will be used as default of Gradient hover.
					if ( $is_desktop ) {
						$gradient_properties_desktop     = $gradient_properties;
						$gradient_overlays_image_desktop = $gradient_overlays_image;
					}

					// Save background gradient into background images list.
					$background_gradient = $this->get_gradient_style( $gradient_properties );
					$images[]            = $background_gradient;

					// Flag to inform Background Color if current module has Gradient.
					$has_gradient = true;
				} elseif ( 'off' === $use_gradient ) {
					$is_gradient_disabled = true;
				}
			}

			// B. Background Image.
			if ( $use_image_options && 'fields_only' !== $use_image_options ) {
				$image    = $responsive->get_inheritance_background_value( $props, "{$base_prop_name}_image", $device, $base_prop_name, $fields_definition );
				$parallax = $responsive->get_any_value( $props, "parallax{$suffix}", 'off' );

				// Determine whether force inline styles.
				if ( ! ET_Builder_Element::$forced_inline_styles ) {
					$background_image_field = $responsive->get_field_name( "{$base_prop_name}_image", $device );
					$raw                    = isset( $attrs_unprocessed[ $background_image_field ] ) ? $attrs_unprocessed[ $background_image_field ] : '';
					if ( $this->_is_dynamic_value( $background_image_field, $raw, $dynamic_attributes ) ) {
						ET_Builder_Element::$forced_inline_styles = true;
					}
				}

				// Featured image as background is in higher priority.
				if ( $module->featured_image_background ) {
					$featured_image         = isset( $props['featured_image'] ) ? $props['featured_image'] : '';
					$featured_placement     = isset( $props['featured_placement'] ) ? $props['featured_placement'] : '';
					$featured_image_src_obj = wp_get_attachment_image_src( get_post_thumbnail_id( self::_get_main_post_id() ), 'full' );
					$featured_image_src     = isset( $featured_image_src_obj[0] ) ? $featured_image_src_obj[0] : '';

					if ( 'on' === $featured_image && 'background' === $featured_placement && '' !== $featured_image_src ) {
						$image = $featured_image_src;
					}
				}

				// Background image and parallax status.
				$is_image_active         = '' !== $image && 'on' !== $parallax;
				$image_status[ $device ] = $is_image_active;

				// 1. Ensure image exists and parallax is off.
				if ( $is_image_active ) {
					// Flag to inform Background Color if current module has Image.
					$has_image = true;

					// Check previous Background image status. Needed to get the correct value.
					$is_prev_image_active = true;

					if ( ! $is_desktop ) {
						$is_prev_image_active = 'tablet' === $device ?
							$image_status['desktop'] :
							$image_status['tablet'];
					}

					// Size.
					$image_size_default   = $this->get_attr_default( 'size', $base_prop_name, $fields_definition );
					$image_size_inherit   = $this->get_attr_value( 'size', $base_prop_name, $suffix, $props, $fields_definition, false );
					$image_width_inherit  = $this->get_attr_value( 'image_width', $base_prop_name, $suffix, $props, $fields_definition, false );
					$image_height_inherit = $this->get_attr_value( 'image_height', $base_prop_name, $suffix, $props, $fields_definition, false );

					if ( '' !== $image_size_inherit || $image_width_inherit || $image_height_inherit ) {
						// Get Size CSS.
						$image_size_style = $this->get_background_size_css( $image_size_inherit, $image_width_inherit, $image_height_inherit, $image_size_default, 'image' );

						// Set image background size styles only it's different compared to the larger device.
						if ( $processed_image_size_style !== $image_size_style ) {
							if ( isset( $image_size_style['size'] ) && $image_size_style['size'] ) {
								$style .= sprintf(
									'background-size: %1$s; ',
									esc_html( $image_size_style['size'] )
								);
							}
						}
					}

					// Check if image size has 'stretch' value or not.
					$is_image_size_stretch = 'stretch' === $this->get_attr_value( 'size', $base_prop_name, $suffix, $props, $fields_definition, false );

					// Repeat.
					$image_repeat = $this->get_attr_value( 'repeat', $base_prop_name, $suffix, $props, $fields_definition, $is_prev_image_active );

					// Print image repeat when image size is not 'stretch'.
					if ( ! $is_image_size_stretch && '' !== $image_repeat ) {
						$style .= sprintf( 'background-repeat: %1$s; ', esc_html( $image_repeat ) );
					}

					// Position.
					$image_position_default          = $this->get_attr_default( "{$base_prop_name}_position{$suffix}", $base_prop_name, $fields_definition );
					$image_position_inherit          = $this->get_attr_value( 'position', $base_prop_name, $suffix, $props, $fields_definition, $is_prev_image_active );
					$image_horizontal_offset_inherit = $this->get_attr_value( 'horizontal_offset', $base_prop_name, $suffix, $props, $fields_definition, $is_prev_image_active );
					$image_vertical_offset_inherit   = $this->get_attr_value( 'vertical_offset', $base_prop_name, $suffix, $props, $fields_definition, $is_prev_image_active );

					// Check if image repeat has 'space' value or not.
					$is_image_repeat_space = 'space' === $this->get_attr_value( 'repeat', $base_prop_name, $suffix, $props, $fields_definition, false );

					// Print image repeat origin/offset when image size is not 'stretch', and
					// image repeat is not 'space'.
					if (
						! $is_image_size_stretch
						&& ! $is_image_repeat_space
						&& ( '' !== $image_position_inherit || $image_horizontal_offset_inherit || $image_vertical_offset_inherit )
					) {
						// Get Position CSS.
						$image_position_style = $this->get_background_position_css( $image_position_inherit, $image_horizontal_offset_inherit, $image_vertical_offset_inherit, $image_position_default );

						// Set background image position styles only it's different compared to the larger device.
						if ( $processed_image_position_style !== $image_position_style ) {
							$style .= sprintf(
								'background-position: %1$s; ',
								esc_html( $image_position_style['position'] )
							);
						}
					}

					// Blend.
					$image_blend_inherit = $this->get_attr_value( 'blend', $base_prop_name, $suffix, $props, $fields_definition, $is_prev_image_active );
					$image_blend_default = $this->get_attr_default( 'blend', $base_prop_name, $fields_definition );

					if ( '' !== $image_blend_inherit ) {
						// Don't print the same image blend style.
						if ( $processed_image_blend_style !== $image_blend_inherit ) {
							$style .= sprintf( 'background-blend-mode: %1$s; ', esc_html( $image_blend_inherit ) );
						}

						// Reset - If background has image and gradient, force background-color: initial.
						if ( $has_gradient && 'fields_only' !== $use_color_reset_options && $image_blend_inherit !== $image_blend_default ) {
							$has_gradient_and_image = true;
							$color_style            = 'initial';

							$style .= sprintf( 'background-color: initial%1$s; ', esc_html( $important ) );
						}

						$processed_image_blend_style = $image_blend_inherit;
					}

					// Only append background image when the image is exist.
					$images[] = sprintf( 'url(%1$s)', esc_html( $image ) );
				} elseif ( '' === $image ) {
					// Reset - If background image is disabled, ensure we reset prev background blend mode.
					if ( '' !== $processed_image_blend_style ) {
						$style                      .= 'background-blend-mode: normal; ';
						$processed_image_blend_style = '';
					}

					$is_image_disabled = true;
				}
			}

			if ( ! empty( $images ) ) {
				// Check if Parallax and Gradient Overlays are on.
				$gradient_overlays_image = $responsive->get_any_value( $props, "{$base_prop_name}_color_gradient_overlays_image{$suffix}", 'off', true );
				$parallax                = $responsive->get_any_value( $props, "parallax{$suffix}", 'off', true );

				// The browsers stack the images in the opposite order to what you'd expect.
				if ( 'on' !== $gradient_overlays_image ) {
					$images = array_reverse( $images );
				}

				if ( $use_gradient_options && 'on' === $gradient_overlays_image && 'on' === $parallax ) {
					// Set background image to initial when Parallax and Gradient Overlays are on.
					$image_style = 'initial';
				} else {
					// Set background image styles only it's different compared to the larger device.
					$image_style = join( ', ', $images );
				}

				if ( $processed_image !== $image_style ) {
					$style .= sprintf(
						'background-image: %1$s%2$s;',
						esc_html( $image_style ),
						$important
					);
				}
			} elseif ( ! $is_desktop && $is_gradient_disabled && $is_image_disabled ) {
				// Reset - If background image and gradient are disabled, reset current background image.
				$image_style = 'initial';

				$style .= sprintf(
					'background-image: %1$s%2$s;',
					esc_html( $image_style ),
					$important
				);
			}

			// Save processed background images.
			$processed_image = $image_style;

			// Save processed styles.
			$processed_image_size_style     = $image_size_style;
			$processed_image_position_style = $image_position_style;

			// C. Background Color.
			if ( $use_color_options && 'fields_only' !== $use_color_options ) {

				$use_color_value = $responsive->get_any_value( $props, "use_{$base_prop_name}_color{$suffix}", 'on', true );

				if ( ! $has_gradient_and_image && 'off' !== $use_color_value ) {
					$color       = $responsive->get_inheritance_background_value( $props, "{$base_prop_name}_color", $device, $base_prop_name, $fields_definition );
					$color       = ! $is_desktop && '' === $color ? 'initial' : $color;
					$color_style = $color;

					if ( '' !== $color && $processed_color !== $color ) {
						$style .= sprintf(
							'background-color: %1$s%2$s; ',
							esc_html( $this->get_color_value( $color ) ),
							esc_html( $important )
						);
					}
				} elseif ( $has_color_toggle_options && 'off' === $use_color_value && ! $is_desktop ) {
					// Reset - If current module has background color toggle, it's off, and current mode
					// it's not desktop, we should reset the background color.
					$style .= sprintf(
						'background-color: initial %1$s; ',
						esc_html( $important )
					);
				}
			}

			// Save processed background color.
			$processed_color = $color_style;

			// E. Background Pattern.
			if ( $use_pattern_options && 'fields_only' !== $use_pattern_options ) {
				$pattern_style_name = $responsive->get_inheritance_background_value( $props, "{$pattern_base_prop_name}_style", $device, $base_prop_name, $fields_definition );

				$is_pattern_active         = '' !== $pattern_style_name;
				$pattern_status[ $device ] = $is_pattern_active;

				if ( $is_pattern_active ) {
					// Check previous pattern status. Needed to get the correct value.
					$is_prev_pattern_active = true;
					if ( ! $is_desktop ) {
						$is_prev_pattern_active = 'tablet' === $device ? $pattern_status['desktop'] : $pattern_status['tablet'];
					}

					// Pattern Transform.
					$pattern_transform_inherit = $responsive->get_any_value( $props, "{$pattern_base_prop_name}_transform{$suffix}", '', true );

					$pattern_is_horizontal   = $this->get_transform_state( $pattern_transform_inherit, 'horizontal' );
					$pattern_is_vertical     = $this->get_transform_state( $pattern_transform_inherit, 'vertical' );
					$pattern_is_rotated      = $this->get_transform_state( $pattern_transform_inherit, 'rotate' );
					$pattern_is_inverted     = $this->get_transform_state( $pattern_transform_inherit, 'invert' );
					$pattern_transform_style = $this->get_transform_css( $pattern_is_horizontal, $pattern_is_vertical );

					$is_pattern_transform_style_different = $processed_pattern_transform_style !== $pattern_transform_style;

					if ( $is_pattern_transform_style_different && ( $pattern_is_horizontal || $pattern_is_vertical ) ) {
						$style_pattern .= sprintf(
							'transform: %1$s; ',
							esc_html( $pattern_transform_style )
						);
					} elseif ( $is_pattern_transform_style_different && ! $is_desktop ) {
						$style_pattern .= sprintf(
							'transform: %1$s; ',
							esc_html( $pattern_transform_style )
						);
					}

					// Pattern Image.
					$pattern_color     = $responsive->get_any_value( $props, "{$pattern_base_prop_name}_color{$suffix}", '', true );
					$pattern_style_svg = et_pb_background_pattern_options()->get_svg( $pattern_style_name, $pattern_color, 'default', $pattern_is_rotated, $pattern_is_inverted );

					// Set pattern style when it's different compared to the larger device.
					if ( $processed_pattern_svg !== $pattern_style_svg ) {
						$style_pattern .= sprintf(
							'background-image: %1$s; ',
							esc_html( $pattern_style_svg )
						);
					}

					// Pattern Size.
					$pattern_size_default   = $this->get_attr_default( 'size', $pattern_base_prop_name, $fields_definition );
					$pattern_size_inherit   = $this->get_attr_value( 'size', $pattern_base_prop_name, $suffix, $props, $fields_definition, $is_prev_pattern_active );
					$pattern_width_inherit  = $this->get_attr_value( 'width', $pattern_base_prop_name, $suffix, $props, $fields_definition, $is_prev_pattern_active );
					$pattern_height_inherit = $this->get_attr_value( 'height', $pattern_base_prop_name, $suffix, $props, $fields_definition, $is_prev_pattern_active );

					if ( '' !== $pattern_size_inherit || $pattern_width_inherit || $pattern_height_inherit ) {
						// Get Size CSS.
						$pattern_size_style = $this->get_background_size_css( $pattern_size_inherit, $pattern_width_inherit, $pattern_height_inherit, $pattern_size_default );

						// Set pattern background size styles only it's different compared to the larger device.
						if ( $processed_pattern_size_style !== $pattern_size_style ) {
							if ( isset( $pattern_size_style['size'] ) && $pattern_size_style['size'] ) {
								$style_pattern .= sprintf(
									'background-size: %1$s; ',
									esc_html( $pattern_size_style['size'] )
								);
							}
						}
					}

					// Check if pattern size has 'stretch' value or not.
					$is_pattern_size_stretch = 'stretch' === $this->get_attr_value( 'size', $pattern_base_prop_name, $suffix, $props, $fields_definition, false );

					// Pattern Repeat.
					$pattern_repeat_inherit = $this->get_attr_value( 'repeat', $pattern_base_prop_name, $suffix, $props, $fields_definition, $is_prev_pattern_active );

					// Print pattern repeat when pattern size is not 'stretch'.
					if ( ! $is_pattern_size_stretch && '' !== $pattern_repeat_inherit ) {
						$style_pattern .= sprintf(
							'background-repeat: %1$s; ',
							esc_html( $pattern_repeat_inherit )
						);
					}

					// Pattern Repeat Origin.
					$pattern_position_default          = $this->get_attr_default( 'repeat_origin', $pattern_base_prop_name, $fields_definition );
					$pattern_position_inherit          = $this->get_attr_value( 'repeat_origin', $pattern_base_prop_name, $suffix, $props, $fields_definition, $is_prev_pattern_active );
					$pattern_horizontal_offset_inherit = $this->get_attr_value( 'horizontal_offset', $pattern_base_prop_name, $suffix, $props, $fields_definition, $is_prev_pattern_active );
					$pattern_vertical_offset_inherit   = $this->get_attr_value( 'vertical_offset', $pattern_base_prop_name, $suffix, $props, $fields_definition, $is_prev_pattern_active );

					// Check if pattern repeat has 'space' value or not.
					$is_pattern_repeat_space = 'space' === $this->get_attr_value( 'repeat', $pattern_base_prop_name, $suffix, $props, $fields_definition, false );

					// Print pattern repeat origin/offset when pattern size is not 'stretch', and
					// pattern repeat is not 'space'.
					if (
						! $is_pattern_size_stretch
						&& ! $is_pattern_repeat_space
						&& ( '' !== $pattern_position_inherit || $pattern_horizontal_offset_inherit || $pattern_vertical_offset_inherit )
					) {
						// Get Position CSS.
						$pattern_position_style = $this->get_background_position_css( $pattern_position_inherit, $pattern_horizontal_offset_inherit, $pattern_vertical_offset_inherit, $pattern_position_default );

						// Set background pattern position styles only it's different compared to the larger device.
						if ( $processed_pattern_position_style !== $pattern_position_style ) {
							$style_pattern .= sprintf(
								'background-position: %1$s; ',
								esc_html( $pattern_position_style['position'] )
							);
						}
					}

					// Pattern Blend Mode.
					$pattern_blend_inherit = $this->get_attr_value( 'blend_mode', $pattern_base_prop_name, $suffix, $props, $fields_definition, $is_prev_pattern_active );

					if ( '' !== $pattern_blend_inherit ) {
						$style_pattern .= sprintf(
							'mix-blend-mode: %1$s; ',
							esc_html( $pattern_blend_inherit )
						);
					}
				} elseif ( ! $is_desktop && $processed_pattern_svg !== $pattern_style_svg ) {
					// Reset pattern image.
					$pattern_style_svg = 'initial';
					$style_pattern    .= sprintf(
						'background-image: %1$s; ',
						esc_html( $pattern_style_svg )
					);
				}
			}

			// Save processed styles.
			$processed_pattern_transform_style = $pattern_transform_style;
			$processed_pattern_svg             = $pattern_style_svg;
			$processed_pattern_size_style      = $pattern_size_style;
			$processed_pattern_position_style  = $pattern_position_style;

			// F. Background Mask.
			if ( $use_mask_options && 'fields_only' !== $use_mask_options ) {
				$mask_style_name = $responsive->get_inheritance_background_value( $props, "{$mask_base_prop_name}_style", $device, $base_prop_name, $fields_definition );

				$is_mask_active         = '' !== $mask_style_name;
				$mask_status[ $device ] = $is_mask_active;

				if ( $is_mask_active ) {
					// Check previous mask status. Needed to get the correct value.
					$is_prev_mask_active = true;
					if ( ! $is_desktop ) {
						$is_prev_mask_active = 'tablet' === $device ? $mask_status['desktop'] : $mask_status['tablet'];
					}

					// Mask Transform.
					$mask_transform_inherit = $responsive->get_any_value( $props, "{$mask_base_prop_name}_transform{$suffix}", '', true );

					$mask_is_horizontal   = $this->get_transform_state( $mask_transform_inherit, 'horizontal' );
					$mask_is_vertical     = $this->get_transform_state( $mask_transform_inherit, 'vertical' );
					$mask_is_rotated      = $this->get_transform_state( $mask_transform_inherit, 'rotate' );
					$mask_is_inverted     = $this->get_transform_state( $mask_transform_inherit, 'invert' );
					$mask_transform_style = $this->get_transform_css( $mask_is_horizontal, $mask_is_vertical );

					$is_mask_transform_style_different = $processed_mask_transform_style !== $mask_transform_style;

					if ( $is_mask_transform_style_different && ( $mask_is_horizontal || $mask_is_vertical ) ) {
						$style_mask .= sprintf(
							'transform: %1$s; ',
							esc_html( $mask_transform_style )
						);
					} elseif ( $is_mask_transform_style_different && ! $is_desktop ) {
						$style_mask .= sprintf(
							'transform: %1$s; ',
							esc_html( $mask_transform_style )
						);
					}

					// Mask Size.
					$mask_size_default   = $this->get_attr_default( 'size', $mask_base_prop_name, $fields_definition );
					$mask_size_inherit   = $this->get_attr_value( 'size', $mask_base_prop_name, $suffix, $props, $fields_definition, $is_prev_mask_active );
					$mask_width_inherit  = $this->get_attr_value( 'width', $mask_base_prop_name, $suffix, $props, $fields_definition, $is_prev_mask_active );
					$mask_height_inherit = $this->get_attr_value( 'height', $mask_base_prop_name, $suffix, $props, $fields_definition, $is_prev_mask_active );

					if ( '' !== $mask_size_inherit || ( $mask_width_inherit || $mask_height_inherit ) ) {
						// Get Size CSS.
						$mask_size_style = $this->get_background_size_css( $mask_size_inherit, $mask_width_inherit, $mask_height_inherit, $mask_size_default, 'mask' );

						// Set mask background size styles only it's different compared to the larger device.
						if ( $processed_mask_size_style !== $mask_size_style ) {
							if ( isset( $mask_size_style['size'] ) && $mask_size_style['size'] ) {
								$style_mask .= sprintf(
									'background-size: %1$s; ',
									esc_html( $mask_size_style['size'] )
								);
							}
						}
					}

					// Check if mask size has 'stretch' value or not.
					$is_mask_size_stretch = 'stretch' === $mask_size_inherit;

					// Set background mask style only it's different compared to the larger device.
					$mask_color     = $responsive->get_any_value( $props, "{$mask_base_prop_name}_color{$suffix}", '', true );
					$mask_ratio     = $responsive->get_any_value( $props, "{$mask_base_prop_name}_aspect_ratio{$suffix}", '', true );
					$mask_style_svg = et_pb_background_mask_options()->get_svg( $mask_style_name, $mask_color, $mask_ratio, $mask_is_rotated, $mask_is_inverted, $mask_size_inherit );

					if ( $processed_mask_svg !== $mask_style_svg ) {
						$style_mask .= sprintf(
							'background-image: %1$s; ',
							esc_html( $mask_style_svg )
						);
					}

					// Mask Position.
					$mask_position_default          = $responsive->get_any_value( $props, "{$mask_base_prop_name}_position{$suffix}", '', true );
					$mask_position_inherit          = $this->get_attr_value( 'position', $mask_base_prop_name, $suffix, $props, $fields_definition, $is_prev_mask_active );
					$mask_horizontal_offset_inherit = $this->get_attr_value( 'horizontal_offset', $mask_base_prop_name, $suffix, $props, $fields_definition, $is_prev_mask_active );
					$mask_vertical_offset_inherit   = $this->get_attr_value( 'vertical_offset', $mask_base_prop_name, $suffix, $props, $fields_definition, $is_prev_mask_active );

					if ( ! $is_mask_size_stretch && ( '' !== $mask_position_inherit || $mask_horizontal_offset_inherit || $mask_vertical_offset_inherit ) ) {
						// Get Position CSS.
						$mask_position_style = $this->get_background_position_css( $mask_position_inherit, $mask_horizontal_offset_inherit, $mask_vertical_offset_inherit, $mask_position_default );

						// Set background mask position styles only it's different compared to the larger device.
						if ( $processed_mask_position_style !== $mask_position_style ) {
							$style_mask .= sprintf(
								'background-position: %1$s; ',
								esc_html( $mask_position_style['position'] )
							);
						}
					}

					// Mask Blend Mode.
					$mask_blend_inherit = $this->get_attr_value( 'blend_mode', $mask_base_prop_name, $suffix, $props, $fields_definition, $is_prev_mask_active );

					if ( '' !== $mask_blend_inherit ) {
						$style_mask .= sprintf(
							'mix-blend-mode: %1$s; ',
							esc_html( $mask_blend_inherit )
						);
					}
				} elseif ( ! $is_desktop && $processed_mask_svg !== $mask_style_svg ) {
					// Reset mask image.
					$mask_style_svg = 'initial';
					$style_mask    .= sprintf(
						'background-image: %1$s; ',
						esc_html( $mask_style_svg )
					);
				}
			}

			// Save processed styles.
			$processed_mask_transform_style = $mask_transform_style;
			$processed_mask_svg             = $mask_style_svg;
			$processed_mask_size_style      = $mask_size_style;
			$processed_mask_position_style  = $mask_position_style;

			// Add media query parameter.
			$background_args = array();
			if ( ! $is_desktop ) {
				$current_media_query            = 'tablet' === $device ? 'max_width_980' : 'max_width_767';
				$background_args['media_query'] = ET_Builder_Element::get_media_query( $current_media_query );
			}

			// Render background styles.
			if ( '' !== $style ) {
				$el_style = array(
					'selector'    => $selector,
					'declaration' => rtrim( $style ),
					'priority'    => $module->get_style_priority(),
				);
				ET_Builder_Element::set_style( $function_name, wp_parse_args( $background_args, $el_style ) );
			}

			// Render pattern styles.
			if ( '' !== $style_pattern ) {
				$el_pattern_style = array(
					'selector'    => $selector_pattern,
					'declaration' => rtrim( $style_pattern ),
					'priority'    => $module->get_style_priority(),
				);
				ET_Builder_Element::set_style( $function_name, wp_parse_args( $background_args, $el_pattern_style ) );
			}

			// Render mask styles.
			if ( '' !== $style_mask ) {
				$el_mask_style = array(
					'selector'    => $selector_mask,
					'declaration' => rtrim( $style_mask ),
					'priority'    => $module->get_style_priority(),
				);
				ET_Builder_Element::set_style( $function_name, wp_parse_args( $background_args, $el_mask_style ) );
			}
		}

		// Background Modes (Hover & Sticky).
		$modes = array( 'hover', 'sticky' );

		foreach ( $modes as $mode ) {
			// Get helper.
			$helper = et_builder_get_helper( $mode );

			// Bail if no helper.
			if ( ! $helper ) {
				continue;
			}

			// Get selector.
			$selector_mode = $args[ "selector_{$mode}" ];

			$selector_pattern_mode = '';
			$selector_mask_mode    = '';

			// If no fixed selector defined, prepend / append default selector.
			if ( '' === $selector_mode ) {
				if ( 'hover' === $mode ) {
					$selector_mode = $helper->add_hover_to_selectors( $selector );
				} elseif ( 'sticky' === $mode ) {
					$is_sticky_module = $helper->is_sticky_module( $props );
					$selector_mode    = $helper->add_sticky_to_order_class( $selector, $is_sticky_module );
				}
			}

			$base_prop_name_mode_parsed = $this->get_prop_name_alias( $prop_name_aliases, $base_prop_name );
			$is_enabled_mode            = $helper->is_enabled( $base_prop_name_mode_parsed, $props );
			$background_image_field     = "{$base_prop_name}_image";

			if ( 'hover' === $mode ) {
				$selector_pattern_mode  = $helper->add_hover_to_order_class( $selector_pattern );
				$selector_mask_mode     = $helper->add_hover_to_order_class( $selector_mask );
				$background_image_field = $helper->get_hover_field( $background_image_field );
			} elseif ( 'sticky' === $mode ) {
				$is_sticky_module       = $helper->is_sticky_module( $props );
				$selector_pattern_mode  = $helper->add_sticky_to_order_class( $selector_pattern, $is_sticky_module );
				$selector_mask_mode     = $helper->add_sticky_to_order_class( $selector_mask, $is_sticky_module );
				$background_image_field = $helper->get_sticky_field( $background_image_field );
			}

			// Check if mode is enabled.
			if ( $is_enabled_mode ) {
				$images_mode = array();
				$style_mode  = '';

				$style_pattern_mode = '';
				$style_mask_mode    = '';

				$has_gradient_mode           = false;
				$has_image_mode              = false;
				$has_gradient_and_image_mode = false;
				$is_gradient_mode_disabled   = false;
				$is_image_mode_disabled      = false;

				$gradient_overlays_image_mode = 'off';

				// Background Gradient Mode (Hover / Sticky).
				// This part is little bit different compared to responsive implementation. In
				// this case, mode is enabled on the background field, not on the each of those
				// fields. So, built in function get_value() doesn't work in this case.
				// Temporarily, we need to fetch the the value from get_raw_value().
				if ( $use_gradient_options && 'fields_only' !== $use_gradient_options ) {
					$use_gradient_mode = $responsive->get_inheritance_background_value(
						$props,
						$this->get_prop_name_alias( $prop_name_aliases, "use_{$base_prop_name}_color_gradient" ),
						$mode,
						$base_prop_name,
						$fields_definition
					);

					// 1. Ensure gradient color is active and values are not null.
					if ( 'on' === $use_gradient_mode ) {
						// Flag to inform BG Color if current module has Gradient.
						$has_gradient_mode    = true;
						$gradient_values_mode = $this->get_gradient_mode_properties(
							$mode,
							$props,
							$base_prop_name,
							$gradient_properties_desktop
						);

						$gradient_mode = $this->get_gradient_style( $gradient_values_mode );
						$images_mode[] = $gradient_mode;

						$gradient_overlays_image_desktop = $responsive->get_any_value(
							$props,
							"{$base_prop_name}_color_gradient_overlays_image",
							'',
							true
						);
						$gradient_overlays_image_mode    = $helper->get_raw_value(
							"{$base_prop_name}_color_gradient_overlays_image",
							$props,
							$gradient_overlays_image_desktop
						);
					} elseif ( 'off' === $use_gradient_mode ) {
						$is_gradient_mode_disabled = true;
					}
				}

				// Background Image Mode (Hover / Sticky).
				// This part is little bit different compared to responsive implementation. In
				// this case, mode is enabled on the background field, not on the each of those
				// fields. So, built in function get_value() doesn't work in this case.
				// Temporarily, we need to fetch the the value from get_raw_value().
				if ( $use_image_options && 'fields_only' !== $use_image_options ) {
					$image_mode    = $responsive->get_inheritance_background_value(
						$props,
						"{$base_prop_name}_image",
						$mode,
						$base_prop_name,
						$fields_definition
					);
					$parallax_mode = $helper->get_raw_value( 'parallax', $props );

					// Determine whether force inline styles.
					if ( ! ET_Builder_Element::$forced_inline_styles ) {
						$raw = isset( $attrs_unprocessed[ $background_image_field ] ) ? $attrs_unprocessed[ $background_image_field ] : '';
						if ( $this->_is_dynamic_value( $background_image_field, $raw, $dynamic_attributes ) ) {
							ET_Builder_Element::$forced_inline_styles = true;
						}
					}

					// Featured image as background is in higher priority.
					if ( $module->featured_image_background ) {
						$featured_image         = isset( $props['featured_image'] ) ? $props['featured_image'] : '';
						$featured_placement     = isset( $props['featured_placement'] ) ? $props['featured_placement'] : '';
						$featured_image_src_obj = wp_get_attachment_image_src( get_post_thumbnail_id( self::_get_main_post_id() ), 'full' );
						$featured_image_src     = isset( $featured_image_src_obj[0] ) ? $featured_image_src_obj[0] : '';

						if ( 'on' === $featured_image && 'background' === $featured_placement && '' !== $featured_image_src ) {
							$image_mode = $featured_image_src;
						}
					}

					if ( '' !== $image_mode && null !== $image_mode && 'on' !== $parallax_mode ) {
						// Flag to inform BG Color if current module has Image.
						$has_image_mode = true;

						// Size.
						$image_size_default = $this->get_attr_default( 'size', $base_prop_name, $fields_definition );
						$image_size_mode    = $helper->get_raw_value( "{$base_prop_name}_size", $props );
						$image_size_desktop = isset( $props[ "{$base_prop_name}_size" ] ) ? $props[ "{$base_prop_name}_size" ] : '';

						if ( empty( $image_size_mode ) && ! empty( $image_size_desktop ) ) {
							$image_size_mode = $image_size_desktop;
						}

						$is_same_image_size = $image_size_mode === $image_size_desktop;

						if ( ! empty( $image_size_mode ) && ! $is_same_image_size ) {
							$image_width_desktop  = isset( $props[ "{$base_prop_name}_image_width" ] ) ? $props[ "{$base_prop_name}_image_width" ] : '';
							$image_width_mode     = $helper->get_raw_value( "{$base_prop_name}_image_width", $props, $image_width_desktop );
							$image_height_desktop = isset( $props[ "{$base_prop_name}_image_height" ] ) ? $props[ "{$base_prop_name}_image_height" ] : '';
							$image_height_mode    = $helper->get_raw_value( "{$base_prop_name}_image_height", $props, $image_height_desktop );

							// Get Size CSS.
							$image_size_style_desktop = $this->get_background_size_css( $image_size_desktop, $image_width_desktop, $image_height_desktop, $image_size_default, 'image' );
							$image_size_style_mode    = $this->get_background_size_css( $image_size_mode, $image_width_mode, $image_height_mode, $image_size_default, 'image' );

							$is_same_image_size_style = $image_size_style_mode === $image_size_style_desktop;

							if ( ! empty( $image_size_style_mode ) && ! $is_same_image_size_style ) {
								if ( isset( $image_size_style_mode['size'] ) && $image_size_style_mode['size'] ) {
									$style_mode .= sprintf(
										'background-size: %1$s; ',
										esc_html( $image_size_style_mode['size'] )
									);
								}
							}
						}

						// Check if image size has 'stretch' value or not.
						$is_image_size_stretch_mode = 'stretch' === $image_size_mode;

						// Repeat.
						$image_repeat_mode    = $helper->get_raw_value( "{$base_prop_name}_repeat", $props );
						$image_repeat_desktop = isset( $props[ "{$base_prop_name}_repeat" ] ) ? $props[ "{$base_prop_name}_repeat" ] : '';
						$is_same_image_repeat = $image_repeat_mode === $image_repeat_desktop;

						if ( empty( $image_repeat_mode ) && ! empty( $image_repeat_desktop ) ) {
							$image_repeat_mode = $image_repeat_desktop;
						}

						// Don't print the same image repeat.
						// Don't print image repeat when image size is 'stretch'.
						if ( ! empty( $image_repeat_mode ) && ! $is_same_image_repeat && ! $is_image_size_stretch_mode ) {
							$style_mode .= sprintf(
								'background-repeat: %1$s; ',
								esc_html( $image_repeat_mode )
							);
						}

						// Position.
						$image_position_mode    = $helper->get_raw_value( "{$base_prop_name}_position", $props );
						$image_position_desktop = isset( $props[ "{$base_prop_name}_position" ] ) ? $props[ "{$base_prop_name}_position" ] : '';

						if ( empty( $image_position_mode ) && ! empty( $image_position_desktop ) ) {
							$image_position_mode = $image_position_desktop;
						}

						// Check if pattern repeat has 'space' value or not.
						$is_image_repeat_space_mode = 'space' === $image_repeat_mode;

						// Print image position/offset when pattern size is not 'stretch', and
						// image repeat is not 'space'.
						if ( ! empty( $image_position_mode ) && ! $is_image_repeat_space_mode && ! $is_image_size_stretch_mode ) {
							$horizontal_offset_desktop = isset( $props[ "{$base_prop_name}_horizontal_offset" ] ) ? $props[ "{$base_prop_name}_horizontal_offset" ] : '';
							$horizontal_offset_mode    = $helper->get_raw_value( "{$base_prop_name}_horizontal_offset", $props, $horizontal_offset_desktop );
							$vertical_offset_desktop   = isset( $props[ "{$base_prop_name}_vertical_offset" ] ) ? $props[ "{$base_prop_name}_vertical_offset" ] : '';
							$vertical_offset_mode      = $helper->get_raw_value( "{$base_prop_name}_vertical_offset", $props, $vertical_offset_desktop );

							// Get Position CSS.
							$image_position_style_desktop = $this->get_background_position_css( $image_position_desktop, $horizontal_offset_desktop, $vertical_offset_desktop );
							$image_position_style_mode    = $this->get_background_position_css( $image_position_mode, $horizontal_offset_mode, $vertical_offset_mode );

							$is_same_image_position_style = $image_position_style_mode === $image_position_style_desktop;

							if ( ! empty( $image_position_style_mode ) && ! $is_same_image_position_style ) {
								$style_mode .= sprintf(
									'background-position: %1$s; ',
									esc_html( $image_position_style_mode['position'] )
								);
							}
						}

						// Blend.
						$image_blend_mode    = $helper->get_raw_value( "{$base_prop_name}_blend", $props );
						$image_blend_default = $this->get_attr_default( 'blend', $base_prop_name, $fields_definition );
						$image_blend_desktop = isset( $props[ "{$base_prop_name}_blend" ] ) ? $props[ "{$base_prop_name}_blend" ] : '';
						$is_same_image_blend = $image_blend_mode === $image_blend_desktop;

						if ( empty( $image_blend_mode ) && ! empty( $image_blend_desktop ) ) {
							$image_blend_mode = $image_blend_desktop;
						}

						if ( ! empty( $image_blend_mode ) ) {
							// Don't print the same background blend.
							if ( ! $is_same_image_blend ) {
								$style_mode .= sprintf(
									'background-blend-mode: %1$s; ',
									esc_html( $image_blend_mode )
								);
							}

							// Force background-color: initial.
							if ( $has_gradient_mode && $has_image_mode && $image_blend_mode !== $image_blend_default ) {
								$has_gradient_and_image_mode = true;
								$style_mode                 .= sprintf( 'background-color: initial%1$s; ', esc_html( $important ) );
							}
						}

						// Only append background image when the image is exist.
						$images_mode[] = sprintf( 'url(%1$s)', esc_html( $image_mode ) );
					} elseif ( '' === $image_mode ) {
						$is_image_mode_disabled = true;
					}
				}

				if ( ! empty( $images_mode ) ) {
					// Check if Parallax and Gradient Overlays are on.
					$gradient_overlays_image_desktop = $responsive->get_any_value( $props, "{$base_prop_name}_color_gradient_overlays_image", 'off', true );
					$gradient_overlays_image_mode    = $helper->get_raw_value( "{$base_prop_name}_color_gradient_overlays_image", $props, $gradient_overlays_image_desktop );
					$parallax_desktop                = $responsive->get_any_value( $props, 'parallax', 'off', true );
					$parallax_mode                   = $helper->get_raw_value( 'parallax', $props, $parallax_desktop );

					// The browsers stack the images in the opposite order to what you'd expect.
					if ( 'on' !== $gradient_overlays_image_mode ) {
						$images_mode = array_reverse( $images_mode );
					}

					if ( $use_gradient_options && 'on' === $gradient_overlays_image_mode && 'on' === $parallax_mode ) {
						// Set background image to initial when Parallax and Gradient Overlays are on.
						$images_mode = 'initial';
					} else {
						$images_mode = join( ', ', $images_mode );
					}

					$style_mode .= sprintf(
						'background-image: %1$s%2$s;',
						esc_html( $images_mode ),
						$important
					);
				} elseif ( $is_gradient_mode_disabled && $is_image_mode_disabled ) {
					$style_mode .= sprintf(
						'background-image: initial %1$s;',
						$important
					);
				}

				// Background Color Mode (Hover / Sticky).
				if ( $use_color_options && 'fields_only' !== $use_color_options ) {
					$use_color_mode_default = isset( $props[ "use_{$base_prop_name}_color" ] ) ? $props[ "use_{$base_prop_name}_color" ] : 'on';
					$use_color_mode_value   = $helper->get_raw_value( "use_{$base_prop_name}_color", $props );
					$use_color_mode_value   = ! empty( $use_color_mode_value )
						? $use_color_mode_value
						: $use_color_mode_default;

					if ( ! $has_gradient_and_image_mode && 'off' !== $use_color_mode_value ) {
						$color_mode = $responsive->get_inheritance_background_value(
							$props,
							"{$base_prop_name}_color",
							$mode,
							$base_prop_name,
							$fields_definition
						);

						$color_mode = '' !== $color_mode ? $color_mode : 'transparent';

						if ( '' !== $color_mode ) {
							$style_mode .= sprintf(
								'background-color: %1$s%2$s; ',
								esc_html( $this->get_color_value( $color_mode ) ),
								esc_html( $important )
							);
						}
					} elseif ( $has_color_toggle_options && 'off' === $use_color_mode_value ) {
						// Reset - If current module has background color toggle, it's off, and current mode
						// it's not desktop, we should reset the background color.
						$style .= sprintf(
							'background-color: initial %1$s; ',
							esc_html( $important )
						);
					}
				}

				// Background Pattern Mode (Hover / Sticky).
				// This part is little bit different compared to responsive implementation. In
				// this case, mode is enabled on the background field, not on the each of those
				// fields. So, built in function get_value() doesn't work in this case.
				// Temporarily, we need to fetch the the value from get_raw_value().
				if ( $use_pattern_options && 'fields_only' !== $use_pattern_options ) {
					$pattern_style_name_mode = $responsive->get_inheritance_background_value(
						$props,
						"{$pattern_base_prop_name}_style",
						$mode,
						$base_prop_name,
						$fields_definition
					);

					if ( '' !== $pattern_style_name_mode ) {
						// Pattern Transform.
						$pattern_transform_mode    = $helper->get_raw_value( "{$pattern_base_prop_name}_transform", $props );
						$pattern_transform_desktop = isset( $props[ "{$pattern_base_prop_name}_transform" ] ) ? $props[ "{$pattern_base_prop_name}_transform" ] : '';

						if ( empty( $pattern_transform_mode ) && ! empty( $pattern_transform_desktop ) ) {
							$pattern_transform_mode = $pattern_transform_desktop;
						}

						$pattern_is_horizontal_mode = $this->get_transform_state( $pattern_transform_mode, 'horizontal' );
						$pattern_is_vertical_mode   = $this->get_transform_state( $pattern_transform_mode, 'vertical' );
						$pattern_is_rotated_mode    = $this->get_transform_state( $pattern_transform_mode, 'rotate' );
						$pattern_is_inverted_mode   = $this->get_transform_state( $pattern_transform_mode, 'invert' );

						if ( ! empty( $pattern_transform_mode ) && ( $pattern_is_horizontal_mode || $pattern_is_vertical_mode ) ) {
							$style_pattern_mode .= sprintf(
								'transform: %1$s; ',
								esc_html( $this->get_transform_css( $pattern_is_horizontal_mode, $pattern_is_vertical_mode ) )
							);
						};

						// Set background pattern style only it's different compared to the larger device.
						$pattern_color_mode    = $helper->get_raw_value( "{$pattern_base_prop_name}_color", $props );
						$pattern_color_desktop = isset( $props[ "{$pattern_base_prop_name}_color" ] ) ? $props[ "{$pattern_base_prop_name}_color" ] : '';

						if ( empty( $pattern_color_mode ) && ! empty( $pattern_color_desktop ) ) {
							$pattern_color_mode = $pattern_color_desktop;
						}

						$pattern_style_svg_mode = et_pb_background_pattern_options()->get_svg( $pattern_style_name_mode, $pattern_color_mode, 'default', $pattern_is_rotated_mode, $pattern_is_inverted_mode );

						$style_pattern_mode .= sprintf(
							'background-image: %1$s; ',
							esc_html( $pattern_style_svg_mode )
						);

						// Pattern Size.
						$pattern_size_mode    = $helper->get_raw_value( "{$pattern_base_prop_name}_size", $props );
						$pattern_size_desktop = isset( $props[ "{$pattern_base_prop_name}_size" ] ) ? $props[ "{$pattern_base_prop_name}_size" ] : '';

						if ( empty( $pattern_size_mode ) && ! empty( $pattern_size_desktop ) ) {
							$pattern_size_mode = $pattern_size_desktop;
						}

						if ( ! empty( $pattern_size_mode ) ) {
							$pattern_width_desktop  = isset( $props[ "{$pattern_base_prop_name}_width" ] ) ? $props[ "{$pattern_base_prop_name}_width" ] : '';
							$pattern_width_mode     = $helper->get_raw_value( "{$pattern_base_prop_name}_width", $props, $pattern_width_desktop );
							$pattern_height_desktop = isset( $props[ "{$pattern_base_prop_name}_height" ] ) ? $props[ "{$pattern_base_prop_name}_height" ] : '';
							$pattern_height_mode    = $helper->get_raw_value( "{$pattern_base_prop_name}_height", $props, $pattern_height_desktop );

							// Get Size CSS.
							$pattern_size_style_desktop = $this->get_background_size_css( $pattern_size_desktop, $pattern_width_desktop, $pattern_height_desktop );
							$pattern_size_style_mode    = $this->get_background_size_css( $pattern_size_mode, $pattern_width_mode, $pattern_height_mode );

							$is_same_pattern_size_style = $pattern_size_style_mode === $pattern_size_style_desktop;

							if ( ! empty( $pattern_size_style_mode ) && ! $is_same_pattern_size_style ) {
								if ( isset( $pattern_size_style_mode['size'] ) && $pattern_size_style_mode['size'] ) {
									$style_pattern_mode .= sprintf(
										'background-size: %1$s; ',
										esc_html( $pattern_size_style_mode['size'] )
									);
								}
							}
						}

						// Check if pattern size has 'stretch' value or not.
						$is_pattern_size_stretch_mode = 'stretch' === $pattern_size_mode;

						// Pattern Repeat.
						$pattern_repeat_mode    = $helper->get_raw_value( "{$pattern_base_prop_name}_repeat", $props );
						$pattern_repeat_desktop = isset( $props[ "{$pattern_base_prop_name}_repeat" ] ) ? $props[ "{$pattern_base_prop_name}_repeat" ] : '';

						if ( empty( $pattern_repeat_mode ) && ! empty( $pattern_repeat_desktop ) ) {
							$pattern_repeat_mode = $pattern_repeat_desktop;
						}

						$is_same_pattern_repeat_style = $pattern_repeat_mode === $pattern_repeat_desktop;

						// Don't print the same pattern repeat.
						// Don't print pattern repeat when pattern size is 'stretch'.
						if ( ! empty( $pattern_repeat_mode ) && ! $is_same_pattern_repeat_style && ! $is_pattern_size_stretch_mode ) {
							$style_pattern_mode .= sprintf(
								'background-repeat: %1$s; ',
								esc_html( $pattern_repeat_mode )
							);
						}

						// Pattern repeat origin.
						$pattern_position_mode    = $helper->get_raw_value( "{$pattern_base_prop_name}_repeat_origin", $props );
						$pattern_position_desktop = isset( $props[ "{$pattern_base_prop_name}_repeat_origin" ] ) ? $props[ "{$pattern_base_prop_name}_repeat_origin" ] : '';

						if ( empty( $pattern_position_mode ) && ! empty( $pattern_position_desktop ) ) {
							$pattern_position_mode = $pattern_position_desktop;
						}

						// Check if pattern repeat has 'space' value or not.
						$is_pattern_repeat_space_mode = 'space' === $pattern_repeat_mode;

						// Print pattern repeat origin/offset when pattern size is not 'stretch',
						// and pattern repeat is not 'space'.
						if ( ! empty( $pattern_position_mode ) && ! $is_pattern_repeat_space_mode && ! $is_pattern_size_stretch_mode ) {
							$pattern_horizontal_offset_desktop = isset( $props[ "{$pattern_base_prop_name}_horizontal_offset" ] ) ? $props[ "{$pattern_base_prop_name}_horizontal_offset" ] : '';
							$pattern_horizontal_offset_mode    = $helper->get_raw_value( "{$pattern_base_prop_name}_horizontal_offset", $props, $pattern_horizontal_offset_desktop );
							$pattern_vertical_offset_desktop   = isset( $props[ "{$pattern_base_prop_name}_vertical_offset" ] ) ? $props[ "{$pattern_base_prop_name}_vertical_offset" ] : '';
							$pattern_vertical_offset_mode      = $helper->get_raw_value( "{$pattern_base_prop_name}_vertical_offset", $props, $pattern_vertical_offset_desktop );

							// Get Position CSS.
							$pattern_position_style_desktop = $this->get_background_position_css( $pattern_position_desktop, $pattern_horizontal_offset_desktop, $pattern_vertical_offset_desktop );
							$pattern_position_style_mode    = $this->get_background_position_css( $pattern_position_mode, $pattern_horizontal_offset_mode, $pattern_vertical_offset_mode );

							$is_same_pattern_position_style = $pattern_position_style_mode === $pattern_position_style_desktop;

							if ( ! empty( $pattern_position_style_mode ) && ! $is_same_pattern_position_style ) {
								$style_pattern_mode .= sprintf(
									'background-position: %1$s; ',
									esc_html( $pattern_position_style_mode['position'] )
								);
							}
						}

						// Pattern Blend Mode.
						$pattern_blend_mode    = $helper->get_raw_value( "{$pattern_base_prop_name}_blend_mode", $props );
						$pattern_blend_desktop = isset( $props[ "{$pattern_base_prop_name}_blend_mode" ] ) ? $props[ "{$pattern_base_prop_name}_blend_mode" ] : '';

						if ( empty( $pattern_blend_mode ) && ! empty( $pattern_blend_desktop ) ) {
							$pattern_blend_mode = $pattern_blend_desktop;
						}

						$is_same_pattern_blend_style = $pattern_blend_mode === $pattern_blend_desktop;

						// Don't print the same pattern blend mode.
						if ( ! empty( $pattern_blend_mode ) && ! $is_same_pattern_blend_style ) {
							$style_pattern_mode .= sprintf(
								'mix-blend-mode: %1$s; ',
								esc_html( $pattern_blend_mode )
							);
						}
					}
				}

				// Background Mask Mode (Hover / Sticky).
				// This part is little bit different compared to responsive implementation. In
				// this case, mode is enabled on the background field, not on the each of those
				// fields. So, built in function get_value() doesn't work in this case.
				// Temporarily, we need to fetch the the value from get_raw_value().
				if ( $use_mask_options && 'fields_only' !== $use_mask_options ) {
					$mask_style_name_mode = $responsive->get_inheritance_background_value(
						$props,
						"{$base_prop_name}_mask_style",
						$mode,
						$base_prop_name,
						$fields_definition
					);

					if ( '' !== $mask_style_name_mode ) {
						// Mask Transform.
						$mask_transform_mode    = $helper->get_raw_value( "{$mask_base_prop_name}_transform", $props );
						$mask_transform_desktop = isset( $props[ "{$mask_base_prop_name}_transform" ] ) ? $props[ "{$mask_base_prop_name}_transform" ] : '';

						if ( empty( $mask_transform_mode ) && ! empty( $mask_transform_desktop ) ) {
							$mask_transform_mode = $mask_transform_desktop;
						}

						$mask_is_horizontal_mode = $this->get_transform_state( $mask_transform_mode, 'horizontal' );
						$mask_is_vertical_mode   = $this->get_transform_state( $mask_transform_mode, 'vertical' );
						$mask_is_rotated_mode    = $this->get_transform_state( $mask_transform_mode, 'rotate' );
						$mask_is_inverted_mode   = $this->get_transform_state( $mask_transform_mode, 'invert' );

						if ( ! empty( $mask_transform_mode ) && ( $mask_is_horizontal_mode || $mask_is_vertical_mode ) ) {
							$style_mask_mode .= sprintf(
								'transform: %1$s; ',
								esc_html( $this->get_transform_css( $mask_is_horizontal_mode, $mask_is_vertical_mode ) )
							);
						};

						// Mask Size.
						$mask_size_default = $this->get_attr_default( 'size', $mask_base_prop_name, $fields_definition );
						$mask_size_mode    = $helper->get_raw_value( "{$mask_base_prop_name}_size", $props );
						$mask_size_desktop = isset( $props[ "{$mask_base_prop_name}_size" ] ) ? $props[ "{$mask_base_prop_name}_size" ] : '';

						if ( empty( $mask_size_mode ) && ! empty( $mask_size_desktop ) ) {
							$mask_size_mode = $mask_size_desktop;
						}

						if ( ! empty( $mask_size_mode ) ) {
							$mask_width_desktop  = isset( $props[ "{$mask_base_prop_name}_width" ] ) ? $props[ "{$mask_base_prop_name}_width" ] : '';
							$mask_width_mode     = $helper->get_raw_value( "{$mask_base_prop_name}_width", $props, $mask_width_desktop );
							$mask_height_desktop = isset( $props[ "{$mask_base_prop_name}_height" ] ) ? $props[ "{$mask_base_prop_name}_height" ] : '';
							$mask_height_mode    = $helper->get_raw_value( "{$mask_base_prop_name}_height", $props, $mask_height_desktop );

							// Get Size CSS.
							$mask_size_style_desktop = $this->get_background_size_css( $mask_size_desktop, $mask_width_desktop, $mask_height_desktop, $mask_size_default, 'mask' );
							$mask_size_style_mode    = $this->get_background_size_css( $mask_size_mode, $mask_width_mode, $mask_height_mode, $mask_size_default, 'mask' );

							$is_same_mask_size_style = $mask_size_style_mode === $mask_size_style_desktop;

							if ( ! empty( $mask_size_style_mode ) && ! $is_same_mask_size_style ) {
								if ( isset( $mask_size_style_mode['size'] ) && $mask_size_style_mode['size'] ) {
									$style_mask_mode .= sprintf(
										'background-size: %1$s; ',
										esc_html( $mask_size_style_mode['size'] )
									);
								}
							}
						}

						// Set background mask style only it's different compared to the larger device.
						$mask_color_mode    = $helper->get_raw_value( "{$mask_base_prop_name}_color", $props );
						$mask_color_desktop = isset( $props[ "{$mask_base_prop_name}_color" ] ) ? $props[ "{$mask_base_prop_name}_color" ] : '';

						if ( empty( $mask_color_mode ) && ! empty( $mask_color_desktop ) ) {
							$mask_color_mode = $mask_color_desktop;
						}

						$mask_ratio_mode    = $helper->get_raw_value( "{$mask_base_prop_name}_aspect_ratio", $props );
						$mask_ratio_desktop = isset( $props[ "{$mask_base_prop_name}_aspect_ratio" ] ) ? $props[ "{$mask_base_prop_name}_aspect_ratio" ] : '';

						if ( empty( $mask_ratio_mode ) && ! empty( $mask_ratio_desktop ) ) {
							$mask_ratio_mode = $mask_ratio_desktop;
						}

						$mask_style_svg_mode = et_pb_background_mask_options()->get_svg( $mask_style_name_mode, $mask_color_mode, $mask_ratio_mode, $mask_is_rotated_mode, $mask_is_inverted_mode, $mask_size_mode );

						$style_mask_mode .= sprintf(
							'background-image: %1$s; ',
							esc_html( $mask_style_svg_mode )
						);

						// Mask Position.
						$mask_position_mode    = $helper->get_raw_value( "{$mask_base_prop_name}_position", $props );
						$mask_position_desktop = isset( $props[ "{$mask_base_prop_name}_position" ] ) ? $props[ "{$mask_base_prop_name}_position" ] : '';

						if ( empty( $mask_position_mode ) && ! empty( $mask_position_desktop ) ) {
							$mask_position_mode = $mask_position_desktop;
						}

						if ( ! empty( $mask_position_mode ) && ! empty( $mask_size_mode ) && 'stretch' !== $mask_size_mode ) {
							$mask_horizontal_offset_desktop = isset( $props[ "{$mask_base_prop_name}_horizontal_offset" ] ) ? $props[ "{$mask_base_prop_name}_horizontal_offset" ] : '';
							$mask_horizontal_offset_mode    = $helper->get_raw_value( "{$mask_base_prop_name}_horizontal_offset", $props, $mask_horizontal_offset_desktop );
							$mask_vertical_offset_desktop   = isset( $props[ "{$mask_base_prop_name}_vertical_offset" ] ) ? $props[ "{$mask_base_prop_name}_vertical_offset" ] : '';
							$mask_vertical_offset_mode      = $helper->get_raw_value( "{$mask_base_prop_name}_vertical_offset", $props, $mask_vertical_offset_desktop );

							// Get Position CSS.
							$mask_position_style_desktop = $this->get_background_position_css( $mask_position_desktop, $mask_horizontal_offset_desktop, $mask_vertical_offset_desktop );
							$mask_position_style_mode    = $this->get_background_position_css( $mask_position_mode, $mask_horizontal_offset_mode, $mask_vertical_offset_mode );

							$is_same_mask_position_style = $mask_position_style_mode === $mask_position_style_desktop;

							if ( ! empty( $mask_position_style_mode ) && ! $is_same_mask_position_style ) {
								$style_mask_mode .= sprintf(
									'background-position: %1$s; ',
									esc_html( $mask_position_style_mode['position'] )
								);
							}
						}

						// Mask Blend Mode.
						$mask_blend_mode    = $helper->get_raw_value( "{$mask_base_prop_name}_blend_mode", $props );
						$mask_blend_desktop = isset( $props[ "{$mask_base_prop_name}_blend_mode" ] ) ? $props[ "{$mask_base_prop_name}_blend_mode" ] : '';

						if ( empty( $mask_blend_mode ) && ! empty( $mask_blend_desktop ) ) {
							$mask_blend_mode = $mask_blend_desktop;
						}

						$is_same_mask_blend_style = $mask_blend_mode === $mask_blend_desktop;

						// Don't print the same mask blend mode.
						if ( ! empty( $mask_blend_mode ) && ! $is_same_mask_blend_style ) {
							$style_mask_mode .= sprintf(
								'mix-blend-mode: %1$s; ',
								esc_html( $mask_blend_mode )
							);
						}
					}
				}

				// Render background mode styles.
				if ( '' !== $style_mode ) {
					$el_style = array(
						'selector'    => $selector_mode,
						'declaration' => rtrim( $style_mode ),
						'priority'    => $module->get_style_priority(),
					);
					ET_Builder_Element::set_style( $function_name, $el_style );
				}

				// Render pattern mode styles.
				if ( '' !== $style_pattern_mode ) {
					$el_pattern_style = array(
						'selector'    => $selector_pattern_mode,
						'declaration' => rtrim( $style_pattern_mode ),
						'priority'    => $module->get_style_priority(),
					);
					ET_Builder_Element::set_style( $function_name, $el_pattern_style );
				}

				// Render mask mode styles.
				if ( '' !== $style_mask_mode ) {
					$el_mask_style = array(
						'selector'    => $selector_mask_mode,
						'declaration' => rtrim( $style_mask_mode ),
						'priority'    => $module->get_style_priority(),
					);
					ET_Builder_Element::set_style( $function_name, $el_mask_style );
				}
			}
		}

		// Cleanup.
		$args              = null;
		$attrs_unprocessed = null;
		$feature_manager   = null;
		$module            = null;
		$props             = null;
		$responsive        = null;
	}

	/**
	 * Return default status for device modes.
	 *
	 * @since 4.15.0
	 *
	 * @return array
	 */
	public function get_default_mode_status() {
		return array(
			'desktop' => false,
			'tablet'  => false,
			'phone'   => false,
		);
	}

	/**
	 * Returns real color value by the global color ID, if any.
	 *
	 * @param string $color Raw Color Value.
	 *
	 * @since 4.16.0 Refactored to perform a substring find/replace (for compound settings like in Gradient Builder).
	 *
	 * @return string
	 */
	public function get_color_value( $color ) {
		if ( false === strpos( $color, 'gcid-' ) ) {
			return $color;
		}

		$global_colors = et_builder_get_all_global_colors( true );

		// If there are no matching Global Colors, return null.
		if ( ! is_array( $global_colors ) ) {
			return null;
		}

		foreach ( $global_colors as $gcid => $details ) {
			if ( false !== strpos( $color, $gcid ) ) {
				// Match substring (needed for attrs like gradient stops).
				$color = str_replace( $gcid, $details['color'], $color );
			}
		}

		return $color;
	}

	/**
	 * Get array of attributes which have dynamic content enabled.
	 *
	 * @param mixed[] $attrs Module attributes.
	 *
	 * @see ET_Builder_Element::_get_enabled_dynamic_attributes()
	 *
	 * @since 4.15.0
	 *
	 * @return string[]
	 */
	protected function _get_enabled_dynamic_attributes( $attrs ) {
		$enabled_dynamic_attributes = isset( $attrs['_dynamic_attributes'] ) ? $attrs['_dynamic_attributes'] : '';
		$enabled_dynamic_attributes = array_filter( explode( ',', $enabled_dynamic_attributes ) );

		return $enabled_dynamic_attributes;
	}

	/**
	 * Check if an attribute value is dynamic or not.
	 *
	 * @param string $attribute Attribute name.
	 * @param string $value Attribute value.
	 * @param array  $enabled_dynamic_attributes Attributes which have dynamic content enabled.
	 *
	 * @see ET_Builder_Element::_is_dynamic_value()
	 *
	 * @since 4.15.0
	 *
	 * @return bool
	 */
	protected function _is_dynamic_value( $attribute, $value, $enabled_dynamic_attributes ) {
		if ( ! in_array( $attribute, $enabled_dynamic_attributes, true ) ) {
			return false;
		}

		return et_builder_parse_dynamic_content( $value )->is_dynamic();
	}

	/**
	 * Get whether third party post interference should be respected.
	 * Current use case is for plugins like Toolset that render a
	 * loop within a layout which renders another layout for
	 * each post - in this case we must NOT override the
	 * current post so the loop works as expected.
	 *
	 * @see ET_Builder_Element::_should_respect_post_interference()
	 *
	 * @since 4.15.0
	 *
	 * @return boolean
	 */
	protected static function _should_respect_post_interference() {
		$post = ET_Post_Stack::get();

		return null !== $post && get_the_ID() !== $post->ID;
	}

	/**
	 * Retrieve the main query post id.
	 * Accounts for third party interference with the current post.
	 *
	 * @see ET_Builder_Element::_get_main_post_id()
	 *
	 * @since 4.15.0
	 *
	 * @return integer|boolean
	 */
	protected static function _get_main_post_id() {
		if ( self::_should_respect_post_interference() ) {
			return get_the_ID();
		}

		return ET_Post_Stack::get_main_post_id();
	}
}
