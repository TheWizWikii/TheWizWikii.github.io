<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Responsive options helper methods.
 *
 * @since 3.23 Add more helper functions. Originally, this class is introduced on Options Harmony v2.
 * @since 3.22
 *
 * Class ET_Builder_Module_Helper_ResponsiveOptions
 */
class ET_Builder_Module_Helper_ResponsiveOptions {

	const DESKTOP = 'desktop';
	const TABLET  = 'tablet';
	const PHONE   = 'phone';

	public static function instance() {
		static $instance;

		return $instance ? $instance : $instance = new self();
	}

	private function __construct() {
		// Now call me if you can
	}

	/**
	 * Get value from an array based on key. However, we can force to return default value if key
	 * doesn't exist or value is empty.
	 *
	 * @since 3.23
	 *
	 * @param  array  $list             Array of values.
	 * @param  string $key              Target key.
	 * @param  mixed  $default          Default value, return if the target doesn't exist.
	 * @param  mixed  $default_on_empty Force to return default if value is empty.
	 * @return mixed                    Value.
	 */
	private function get( $list, $key, $default = null, $default_on_empty = false ) {
		$value = isset( $list[ $key ] ) ? $list[ $key ] : $default;

		// Return default if we need non empty value to be used.
		if ( $default_on_empty && empty( $value ) ) {
			$value = $default;
		}

		return $value;
	}

	/**
	 * Check if responsive settings is enabled or not on the option.
	 *
	 * Mostly used by FE.
	 *
	 * @since 3.23
	 *
	 * @param  array  $attrs All module attributes.
	 * @param  string $name  Option name.
	 * @return boolean        Responsive settings status.
	 */
	public function is_responsive_enabled( $attrs, $name ) {
		$last_edited = $this->get( $attrs, "{$name}_last_edited", '' );
		return $this->get_responsive_status( $last_edited );
	}

	/**
	 * Check if responsive settings are enabled on one of the options list.
	 *
	 * @since 3.23
	 *
	 * @param  array $attrs All module attributes.
	 * @param  array $list  Options list.
	 * @return boolean      Responsive styles status.
	 */
	public function is_any_responsive_enabled( $attrs, $list ) {
		// Ensure list is not empty and valid array.
		if ( empty( $list ) || ! is_array( $list ) ) {
			return false;
		}

		// Check the responsive status one by one.
		$is_responsive_enabled = false;
		foreach ( $list as $name ) {
			if ( $this->is_responsive_enabled( $attrs, $name ) ) {
				// Break early if current field enabled responsive is found.
				$is_responsive_enabled = true;
				break;
			}
		}

		return $is_responsive_enabled;
	}

	/**
	 * Get responsive status based one last edited value.
	 *
	 * Parsed *_last_edited value and determine wheter the passed string means it has responsive value
	 * or not. *_last_edited holds two values (responsive status and last opened tabs) in the following
	 * format: status|last_opened_tab.
	 *
	 * Copy of et_pb_get_responsive_status() with a little modified and to organize the code.
	 *
	 * @param  string $last_edited Last edited field value.
	 * @return bool                Responsive field status.
	 */
	public function get_responsive_status( $last_edited ) {
		if ( empty( $last_edited ) || ! is_string( $last_edited ) ) {
			return false;
		}

		$parsed_last_edited = explode( '|', $last_edited );

		return isset( $parsed_last_edited[0] ) ? 'on' === $parsed_last_edited[0] : false;
	}

	/**
	 * Generate video background markup.
	 *
	 * When background support responsive settings, the default callback will be replaced with
	 * get_video_background() function to retrieve all video values for desktop, hover, tablet,
	 * and phone.
	 *
	 * @since 3.23
	 *
	 * @param  array $args             Background values.
	 * @param  array $conditional_tags Conditional tags.
	 * @param  array $current_page     Current page info.
	 * @return mixed                    Mixed background content generated as video markup.
	 */
	public static function get_video_background( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$base_name   = isset( $args['computed_variables'] ) && isset( $args['computed_variables']['base_name'] ) ? $args['computed_variables']['base_name'] : 'background';
		$attr_prefix = "{$base_name}_";

		// Build custom args.
		$default_args = array(
			"{$attr_prefix}video_mp4"    => isset( $args[ "{$attr_prefix}video_mp4" ] ) ? $args[ "{$attr_prefix}video_mp4" ] : '',
			"{$attr_prefix}video_webm"   => isset( $args[ "{$attr_prefix}video_webm" ] ) ? $args[ "{$attr_prefix}video_webm" ] : '',
			"{$attr_prefix}video_width"  => isset( $args[ "{$attr_prefix}video_width" ] ) ? $args[ "{$attr_prefix}video_width" ] : '',
			"{$attr_prefix}video_height" => isset( $args[ "{$attr_prefix}video_height" ] ) ? $args[ "{$attr_prefix}video_height" ] : '',
			'computed_variables'         => array(
				'base_name' => $base_name,
			),
		);

		$hover_args = array(
			"{$attr_prefix}video_mp4__hover"    => isset( $args[ "{$attr_prefix}video_mp4__hover" ] ) ? $args[ "{$attr_prefix}video_mp4__hover" ] : '',
			"{$attr_prefix}video_webm__hover"   => isset( $args[ "{$attr_prefix}video_webm__hover" ] ) ? $args[ "{$attr_prefix}video_webm__hover" ] : '',
			"{$attr_prefix}video_width__hover"  => isset( $args[ "{$attr_prefix}video_width__hover" ] ) ? $args[ "{$attr_prefix}video_width__hover" ] : '',
			"{$attr_prefix}video_height__hover" => isset( $args[ "{$attr_prefix}video_height__hover" ] ) ? $args[ "{$attr_prefix}video_height__hover" ] : '',
			'computed_variables'                => array(
				'base_name' => $base_name,
				'device'    => '_hover',
			),
		);

		$tablet_args = array(
			"{$attr_prefix}video_mp4_tablet"    => isset( $args[ "{$attr_prefix}video_mp4_tablet" ] ) ? $args[ "{$attr_prefix}video_mp4_tablet" ] : '',
			"{$attr_prefix}video_webm_tablet"   => isset( $args[ "{$attr_prefix}video_webm_tablet" ] ) ? $args[ "{$attr_prefix}video_webm_tablet" ] : '',
			"{$attr_prefix}video_width_tablet"  => isset( $args[ "{$attr_prefix}video_width_tablet" ] ) ? $args[ "{$attr_prefix}video_width_tablet" ] : '',
			"{$attr_prefix}video_height_tablet" => isset( $args[ "{$attr_prefix}video_height_tablet" ] ) ? $args[ "{$attr_prefix}video_height_tablet" ] : '',
			'computed_variables'                => array(
				'base_name' => $base_name,
				'device'    => 'tablet',
			),
		);

		$phone_args = array(
			"{$attr_prefix}video_mp4_phone"    => isset( $args[ "{$attr_prefix}video_mp4_phone" ] ) ? $args[ "{$attr_prefix}video_mp4_phone" ] : '',
			"{$attr_prefix}video_webm_phone"   => isset( $args[ "{$attr_prefix}video_webm_phone" ] ) ? $args[ "{$attr_prefix}video_webm_phone" ] : '',
			"{$attr_prefix}video_width_phone"  => isset( $args[ "{$attr_prefix}video_width_phone" ] ) ? $args[ "{$attr_prefix}video_width_phone" ] : '',
			"{$attr_prefix}video_height_phone" => isset( $args[ "{$attr_prefix}video_height_phone" ] ) ? $args[ "{$attr_prefix}video_height_phone" ] : '',
			'computed_variables'               => array(
				'base_name' => $base_name,
				'device'    => 'phone',
			),
		);

		$video_backgrounds = array();

		// Get video background markup.
		$background_video = ET_Builder_Element::get_video_background( $default_args );
		if ( $background_video ) {
			$video_backgrounds['desktop'] = $background_video;
		}

		$background_video_hover = ET_Builder_Element::get_video_background( $hover_args );
		if ( $background_video_hover ) {
			$video_backgrounds['hover'] = $background_video_hover;
		}

		$background_video_tablet = ET_Builder_Element::get_video_background( $tablet_args );
		if ( $background_video_tablet ) {
			$video_backgrounds['tablet'] = $background_video_tablet;
		}

		$background_video_phone = ET_Builder_Element::get_video_background( $phone_args );
		if ( $background_video_phone ) {
			$video_backgrounds['phone'] = $background_video_phone;
		}

		return $video_backgrounds;
	}

	/**
	 * Returns the field original name by removing the `_tablet` or `_phone` suffix if it exists.
	 *
	 * Only remove tablet/phone string of the last setting name. Doesn't work for other format.
	 *
	 * @since 3.23
	 *
	 * @param string $name Setting name.
	 * @return string      Base setting name.
	 */
	public function get_field_base_name( $name ) {
		// Do not use rtim as it removes by character not by string. So, cases like `key_tablets`
		// will be reduced to `key`.
		$regex   = '/(.*)(_tablet|_phone)$/';
		$replace = '${1}';
		return preg_replace( $regex, $replace, $name );
	}

	/**
	 * Returns the field responsive name by adding the `_tablet` or `_phone` suffix if it exists.
	 *
	 * @since 3.27.4
	 *
	 * @param  string $name   Setting name.
	 * @param  string $device Device name.
	 * @return string         Field setting name.
	 */
	public function get_field_name( $name, $device = 'desktop' ) {
		// Field name should not be empty.
		if ( empty( $name ) ) {
			return $name;
		}

		// Ensure device is not empty.
		$device = '' === $device ? 'desktop' : $device;

		// Get device name.
		return 'desktop' !== $device ? "{$name}_{$device}" : $name;
	}

	/**
	 * Returns the device name by removing the `name` prefix. If the result is one of tablet or phone,
	 * return it. But, if it's empty, return desktop.
	 *
	 * @since 3.23
	 *
	 * @param string $name Setting name.
	 * @return string      Device name.
	 */
	public function get_device_name( $name ) {
		// Do not use rtim as it removes by character not by string. So, cases like `key_tablets`
		// will be reduced to `key`.
		$regex   = '/(.*)(tablet|phone)$/';
		$replace = '${2}';
		$result  = preg_replace( $regex, $replace, $name );
		return in_array( $result, array( 'tablet', 'phone' ) ) ? $result : 'desktop';
	}

	/**
	 * Get responsive value based on field base name and device.
	 *
	 * NOTE: Function get_single_value() is different with get_any_value(). It will return only
	 *       current field value without checking the previous device value.
	 *
	 * For example: We have Title Text Font Size -> desktop 30px, tablet 10px, phone 10px. Fetch
	 *              the value for phone, it will return pure 10px even the value is same with tablet.
	 *              We have Subtitle Text Font Size -> desktop 20px, tablet 15px, phone ''. Fetch
	 *              the value for phone, it will return pure '' or default even the value is empty.
	 *
	 * To get tablet or phone value:
	 * 1. You can pass only field base name and device name as the 4th argument. The parameters
	 *    structure it's made like that to make it similar with other get* method we already have.
	 *    For example: get_single_value( $this->props, 'title_text_font_size', '', 'tablet' ).
	 *
	 * 2. Or you can pass the actual field name with device. If the field name is already contains
	 *    _tablet and _phone, don't pass device parameter because it will be added as suffix.
	 *    For example: get_single_value( $this->props, 'title_text_font_size_tablet', '' ).
	 *
	 * @since 3.23
	 *
	 * @param  array  $attrs         All module attributes.
	 * @param  string $name          Option name.
	 * @param  array  $default_value Default value.
	 * @param  string $device        Current device name.
	 * @return mixed                 Current option value based on active device.
	 */
	public function get_single_value( $attrs, $name = '', $default_value = '', $device = 'desktop' ) {
		// Ensure $device is not empty.
		$device = '' === $device ? 'desktop' : $device;

		// Ensure always use device as suffix if device is not desktop or empty.
		if ( 'desktop' !== $device ) {
			$base_name = $this->get_field_base_name( $name );
			$name      = "{$base_name}_{$device}";
		}

		return $this->get( $attrs, $name, $default_value, true );
	}

	/**
	 * Get current active device value from attributes.
	 *
	 * NOTE: Function get_any_value() is different with get_value(). It also compare the value
	 *       with the previous device value to avoid duplication. Or you can also force to return
	 *       either current or previous default value if needed.
	 *
	 * For example: We have Title Text Font Size -> desktop 30px, tablet 30px, phone 10px. When
	 *              we fetch the value for tablet, it will return pure empty string ('') because
	 *              tablet value is equal with desktop value.
	 *
	 *              We have Title Text Font Size -> desktop 30px, tablet '', phone ''. When
	 *              we fetch the value for phone and force it to return any value, it will
	 *              return 30px because phone and tablet value is empty and the function will
	 *              look up to tablet or even desktop value.
	 *
	 * To get tablet or phone value:
	 * 1. You can pass only field base name and device name as the 4th argument. The parameters
	 *    structure it's made like that to make it similar with other get* method we already have.
	 *    For example: get_any_value( $this->props, 'title_text_font_size', '', 'tablet' ).
	 *
	 * 2. Or you can pass the actual field name with device. If the field name is already contains
	 *    _tablet and _phone, don't pass device parameter because it will be added as suffix.
	 *    For example: get_any_value( $this->props, 'title_text_font_size_tablet', '' ).
	 *
	 * 3. You can also force to return any value by passing true on the 5th argument. In some cases
	 *    we need this to fill missing tablet/phone value with desktop value.
	 *
	 * @since 3.23
	 *
	 * @param  array  $attrs         All module attributes.
	 * @param  string $name          Option name.
	 * @param  array  $default_value Default value.
	 * @param  bool   $force_return  Force to return any value.
	 * @param  string $device        Current device name.
	 * @return mixed                 Current option value based on active device.
	 */
	public function get_any_value( $attrs, $name = '', $default_value = '', $force_return = false, $device = 'desktop' ) {
		// Ensure $device is not empty.
		$device = '' === $device ? 'desktop' : $device;

		// Ensure always use device as suffix if device is not desktop/empty.
		if ( 'desktop' !== $device ) {
			$base_name = $this->get_field_base_name( $name );
			$name      = "{$base_name}_{$device}";
		}

		// Get current value.
		$current_value = $this->get( $attrs, $name, '' );

		// Get previous value to be compared.
		$prev_value = $this->get_default_value( $attrs, $name, $default_value );

		// Force to return any values given.
		if ( $force_return ) {
			return ! empty( $current_value ) ? $current_value : $prev_value;
		}

		// Ensure current value is different with the previous device or default.
		if ( $current_value === $prev_value ) {
			return '';
		}

		return $current_value;
	}

	/**
	 * Get property's values for requested device.
	 *
	 * This function is added to summarize how we fetch desktop/hover/tablet/phone value. This
	 * function still uses get_any_value to get current device values.
	 *
	 * @since 3.23
	 *
	 * @param array   $attrs         List of all attributes and values.
	 * @param string  $name          Property name.
	 * @param mixed   $default_value Default value.
	 * @param string  $device        Device name.
	 * @param boolean $force_return  Force to return any values found.
	 *
	 * @return array Pair of devices and the values.
	 */
	public function get_property_value( $attrs, $name, $default_value = '', $device = 'desktop', $force_return = false ) {
		// Default values.
		$default_value = esc_attr( $default_value );

		// Ensure $device is not empty.
		$device = '' === $device ? 'desktop' : $device;

		// Ensure attrs (values list) and name (property name) are not empty.
		if ( empty( $attrs ) || '' === $name ) {
			return $default_value;
		}

		$is_enabled = 'desktop' !== $device ? $this->is_responsive_enabled( $attrs, $name ) : true;
		$suffix     = 'desktop' !== $device ? "_{$device}" : '';
		$value      = $is_enabled ? $this->get_any_value( $attrs, "{$name}{$suffix}", $default_value, $force_return ) : $default_value;

		return esc_attr( $value );
	}

	/**
	 * Get all properties values for all devices.
	 *
	 * This function is added to summarize how we fetch desktop/hover, tablet, and phone values. This
	 * function still use get_any_value to get current device values.
	 *
	 * @since 3.23
	 *
	 * @param array   $attrs         List of all attributes and values.
	 * @param string  $name          Property name.
	 * @param mixed   $default_value Default value.
	 * @param boolean $force_return  Force to return any values found.
	 *
	 * @return array Pair of devices and the values.
	 */
	public function get_property_values( $attrs, $name, $default_value = '', $force_return = false ) {
		// Default values.
		$default_value = esc_attr( $default_value );
		$values        = array(
			'desktop' => $default_value,
			'tablet'  => $default_value,
			'phone'   => $default_value,
		);

		// Ensure attrs (values list) and name (property name) are not empty.
		if ( empty( $attrs ) || '' === $name ) {
			return $values;
		}

		$is_responsive = $this->is_responsive_enabled( $attrs, $name );

		// Get values for each devices.
		$values['desktop'] = esc_html( $this->get_any_value( $attrs, $name, $default_value, $force_return ) );
		if ( $is_responsive ) {
			$values['tablet'] = esc_html( $this->get_any_value( $attrs, "{$name}_tablet", $default_value, $force_return ) );
			$values['phone']  = esc_html( $this->get_any_value( $attrs, "{$name}_phone", $default_value, $force_return ) );
		}

		return $values;
	}

	/**
	 * Get property value after checking whether it uses responsive or not
	 *
	 * If responsive is used, automatically return array of all devices value.
	 * If responsive is not used, return string of desktop value
	 *
	 * @since 4.6.0
	 *
	 * @param array   $attrs         List of all attributes and values.
	 * @param string  $name          Property name.
	 * @param mixed   $default_value Default value.
	 * @param boolean $force_return  Force to return any values found.
	 *
	 * @return string|array String if not responsive, Pair of devices and the values if responsive.
	 */
	public function get_checked_property_value( $attrs, $name, $default_value = '', $force_return = false ) {
		$is_responsive = $this->is_responsive_enabled( $attrs, $name );

		return $is_responsive ?
			$this->get_property_values( $attrs, $name, $default_value, $force_return ) :
			$this->get_property_value( $attrs, $name, $default_value, 'desktop', $force_return );
	}

	/**
	 * Get composite property's value for requested device.
	 *
	 * This function is added to summarize how we fetch desktop/hover/tablet/phone value. This
	 * function still uses get_any_value to get current device values.
	 *
	 * @since 3.27.4
	 *
	 * @param array   $attrs          List of all attributes and values.
	 * @param string  $composite_name Composite property name.
	 * @param string  $name           Property name.
	 * @param mixed   $default_value  Default value.
	 * @param string  $device         Device name.
	 * @param boolean $force_return   Force to return any values found.
	 *
	 * @return array Pair of devices and the values.
	 */
	public function get_composite_property_value( $attrs, $composite_name, $name, $default_value = '', $device = 'desktop', $force_return = false ) {
		// Default values.
		$default_value = esc_attr( $default_value );

		// Ensure $device is not empty.
		$device = '' === $device ? 'desktop' : $device;

		// Ensure attrs, composite name (parent property name), name (property name) are not empty.
		if ( empty( $attrs ) || '' === $composite_name || '' === $name ) {
			return $default_value;
		}

		$is_enabled = 'desktop' !== $device ? $this->is_responsive_enabled( $attrs, $composite_name ) : true;
		$suffix     = 'desktop' !== $device ? "_{$device}" : '';
		$value      = $is_enabled ? $this->get_any_value( $attrs, "{$name}{$suffix}", $default_value, $force_return ) : $default_value;

		return esc_attr( $value );
	}

	/**
	 * Get all composite properties values for all devices.
	 *
	 * This function is added to summarize how we fetch desktop/hover, tablet, and phone values. This
	 * function still use get_any_value to get current device values.
	 *
	 * @since 3.27.4
	 *
	 * @param array   $attrs          List of all attributes and values.
	 * @param string  $composite_name Composite property name.
	 * @param string  $name           Property name.
	 * @param mixed   $default_value  Default value.
	 * @param boolean $force_return   Force to return any values found.
	 *
	 * @return array Pair of devices and the values.
	 */
	public function get_composite_property_values( $attrs, $composite_name, $name, $default_value = '', $force_return = false ) {
		// Default values.
		$default_value = esc_attr( $default_value );
		$values        = array(
			'desktop' => $default_value,
			'tablet'  => $default_value,
			'phone'   => $default_value,
		);

		// Ensure attrs, composite name (parent property name), name (property name) are not empty.
		if ( empty( $attrs ) || '' === $composite_name || '' === $name ) {
			return $values;
		}

		$is_responsive = $this->is_responsive_enabled( $attrs, $composite_name );

		// Get values for each devices.
		$values['desktop'] = esc_attr( $this->get_any_value( $attrs, $name, $default_value, $force_return ) );
		if ( $is_responsive ) {
			$values['tablet'] = esc_attr( $this->get_any_value( $attrs, "{$name}_tablet", $default_value, $force_return ) );
			$values['phone']  = esc_attr( $this->get_any_value( $attrs, "{$name}_phone", $default_value, $force_return ) );
		}

		return $values;
	}

	/**
	 * Get multiple attributes value from current active device.
	 *
	 * Basically, this function is combination of:
	 * - Get any value of attribute
	 * - Check attribute responsive status for tablet/phone
	 * - Only send non empty attributes, except you force to return any given value
	 * - Doing all of the process above for more than one fields
	 *
	 * @since 3.23
	 *
	 * @param  array  $attrs        All module attributes.
	 * @param  string $list         List of options name. Name should be field base name.
	 * @param  bool   $force_return Force to return any value.
	 * @param  string $device       Current device name.
	 * @return array                All option values.
	 */
	public function get_any_responsive_values( $attrs, $list, $force_return = false, $device = 'desktop' ) {
		// Ensure list is not empty and valid array.
		if ( empty( $list ) || ! is_array( $list ) ) {
			return array();
		}

		// Ensure device is not empty.
		$device = '' === $device ? 'desktop' : $device;

		// Fetch each attribute and store it in $values.
		$values = array();
		foreach ( $list as $field_key => $field_value ) {
			// Check responsive status if current device is tablet or phone.
			if ( 'desktop' !== $device && ! $this->is_responsive_enabled( $attrs, $field_key ) ) {
				continue;
			}

			// Get value.
			$value = $this->get_any_value( $attrs, $field_key, $field_value, $force_return, $device );

			// No need to save the value if it's empty and we don't force to return any value.
			if ( ! $force_return && empty( $value ) ) {
				continue;
			}

			$values[ $field_key ] = $value;
		}

		return $values;
	}

	/**
	 * Get default value of active device. Mechanism:
	 *
	 * - Desktop => Return default value.
	 * - Tablet  => Return desktop value or default value.
	 * - Phone   => Return tablet value or desktop value or default value.
	 *
	 * @since 3.23
	 *
	 * @param  array  $attrs         All module attributes.
	 * @param  string $name          Option name.
	 * @param  array  $default_value All module advanced defaults.
	 * @return mixed                 Previous option value based on active device.
	 */
	public function get_default_value( $attrs, $name = '', $default_value = '' ) {
		// Get option base name.
		$base_name = $this->get_field_base_name( $name );
		$device    = $this->get_device_name( $name );

		// Get default value and return it for Desktop.
		if ( 'desktop' === $device ) {
			return $default_value;
		}

		// Get tablet value and return it for Tablet.
		$desktop_value = $this->get( $attrs, "{$base_name}", $default_value, true );
		if ( 'tablet' === $device ) {
			return $desktop_value;
		}

		// Get phone value and return it for Phone.
		$tablet_value = $this->get( $attrs, "{$base_name}_tablet", $desktop_value, true );
		if ( 'phone' === $device ) {
			return $tablet_value;
		}

		return $default_value;
	}

	/**
	 * Returns responsive modes list from largest to narrow
	 *
	 * @return string[]
	 */
	public function get_modes() {
		return array( self::DESKTOP, self::TABLET, self::PHONE );
	}

	/**
	 * Returns next wider mode then provided
	 *
	 * @param $mode
	 *
	 * @return null|string
	 */
	public function get_wider_mode( $mode ) {
		$modes = $this->get_modes();
		$key   = array_search( $this->validate_mode( $mode ), $modes );

		return false != $key ? et_()->array_get( $modes, '[' . ( -- $key ) . ']', null ) : null;
	}

	/**
	 * Returns next narrower mode then provided
	 *
	 * @param $mode
	 *
	 * @return null|string
	 */
	public function get_narrower_mode( $mode ) {
		$modes = $this->get_modes();
		$key   = array_search( $this->validate_mode( $mode ), $modes );

		return false !== $key && isset( $modes[ $key + 1 ] ) ? $modes[ $key + 1 ] : null;
	}

	/**
	 * Return default responsive mode
	 *
	 * @return string
	 */
	public function get_default_mode() {
		return self::DESKTOP;
	}

	/**
	 * Returns setting field name by responsive mode
	 *
	 * @param $setting
	 * @param $mode
	 *
	 * @return string
	 */
	public function get_field( $setting, $mode ) {
		return $setting . $this->mode_field( (string) $this->validate_mode( $mode ) );
	}

	/**
	 * Returns setting field name of the last edited mode
	 *
	 * @param string $setting
	 *
	 * @return string
	 */
	public function get_last_edited_field( $setting ) {
		return "{$setting}_last_edited";
	}

	/**
	 * Checks if setting responsive mode is enabled
	 *
	 * @param $setting
	 * @param $props
	 *
	 * @return bool
	 */
	public function is_enabled( $setting, $props ) {
		$value = et_builder_module_prop( $this->get_last_edited_field( $this->get_field_base_name( $setting ) ), $props, '' );

		return et_pb_get_responsive_status( $value );
	}

	/**
	 * Returns the props value by mode
	 * If no mode provided, the default mode is used
	 *
	 * @param $setting
	 * @param $props
	 * @param null    $mode
	 * @param string  $default
	 *
	 * @return mixed
	 */
	public function get_value( $setting, $props, $mode = null, $default = '' ) {
		$mode = $this->get_mode_or_default( $mode );

		if ( $this->get_default_mode() != $mode && ! $this->is_enabled( $setting, $props ) ) {
			return $default;
		}

		return et_builder_module_prop( $this->get_field( $setting, $mode ), $props, $default );
	}

	/**
	 * Is the implementation of get_value specifically for desktop mode
	 *
	 * Note: since the desktop mode is the default mode,
	 * this method would similar to get_value without providing mode,
	 * but can be used for a more explicit representation
	 *
	 * @param string $setting
	 * @param array  $props
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get_desktop_value( $setting, $props, $default = null ) {
		return $this->get_value( $setting, $props, self::DESKTOP, $default );
	}

	/**
	 * Is the implementation of get_value specifically for tablet mode
	 *
	 * @param string $setting
	 * @param array  $props
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get_tablet_value( $setting, $props, $default = null ) {
		return $this->get_value( $setting, $props, self::TABLET, $default );
	}

	/**
	 * Is the implementation of get_value specifically for phone mode
	 *
	 * @param string $setting
	 * @param array  $props
	 * @param mixed  $default
	 *
	 * @return mixed
	 */
	public function get_phone_value( $setting, $props, $default = null ) {
		return $this->get_value( $setting, $props, self::PHONE, $default );
	}

	/**
	 * Returns the last edited responsive mode of the provided setting
	 * If not valid value is provided, default mode is returned
	 *
	 * @param $setting
	 * @param $props
	 *
	 * @return string
	 */
	public function get_last_edited( $setting, $props ) {
		$value = et_builder_module_prop( $this->get_last_edited_field( $setting ), $props, '' );
		$mode  = et_()->array_get( explode( '|', $value ), '[1]' );

		return $this->validate_mode( $mode ) ? $mode : $this->get_default_mode();
	}

	/**
	 * Get breakpoint minimum widths
	 *
	 * @since 4.3.2
	 *
	 * @return array
	 */
	public function get_breakpoint_min_widths() {
		return array(
			'desktop' => 980,
			'tablet'  => 768,
			'phone'   => 0,
		);
	}

	/**
	 * Get breakpoint based on device name.
	 *
	 * @since 4.0
	 *
	 * @param  string $device
	 *
	 * @return string
	 */
	public function get_breakpoint_by_device( $device = 'desktop' ) {
		switch ( $device ) {
			case 'desktop_only':
				return 'min_width_981';
			case 'tablet':
				return 'max_width_980';
			case 'tablet_only':
				return '768_980';
			case 'desktop_tablet_only':
				return 'min_width_768';
			case 'phone':
				return 'max_width_767';
			default:
				return 'general';
		}
	}

	/**
	 * @param $mode
	 *
	 * @return bool|string
	 */
	protected function validate_mode( $mode ) {
		return in_array( strtolower( $mode ), $this->get_modes() ) ? strtolower( $mode ) : false;
	}

	protected function get_mode_or_default( $mode ) {
		return $this->validate_mode( $mode ) ? strtolower( $mode ) : $this->get_default_mode();
	}

	/**
	 * Returns mode suffix
	 * The default mode suffix is empty
	 *
	 * @param $mode
	 *
	 * @return string
	 */
	protected function mode_field( $mode ) {
		switch ( $mode ) {
			case $this->get_default_mode():
				return '';
			default:
				return "_$mode";
		}
	}

	/**
	 * Generates the css code for responsive options.
	 *
	 * Uses array of values for each device as input parameter and css_selector with property to
	 * apply the css.
	 *
	 * Copy of et_pb_generate_responsive_css() with some modifications to improve.
	 *
	 * @since 3.23
	 *
	 * @param  array  $values_array   All device values.
	 * @param  mixed  $css_selector   CSS selector.
	 * @param  string $css_property   CSS property.
	 * @param  string $function_name  Module slug.
	 * @param  string $additional_css Additional CSS.
	 * @param  string $type           Value type to determine need filter or not. Previously, it only
	 *                                accept value from range control and run a function to process
	 *                                range value.
	 * @param  string $priority       CSS style declaration priority.
	 */
	public function generate_responsive_css( $values_array, $css_selector, $css_property, $function_name, $additional_css = '', $type = 'range', $priority = '' ) {
		if ( empty( $values_array ) ) {
			return;
		}

		foreach ( $values_array as $device => $current_value ) {
			if ( empty( $current_value ) ) {
				continue;
			}

			// 1. Selector.
			// There are some cases where selector is an object contains specific selector for
			// each devices.
			$selector = $css_selector;
			if ( is_array( $css_selector ) ) {
				$selector = ! empty( $css_selector[ $device ] ) ? $css_selector[ $device ] : '';
			}

			if ( empty( $selector ) ) {
				continue;
			}

			// 2. Declare CSS style.
			// There are some cases before we can declare the CSS style:
			// 1. The value is an array contains pair of properties and values.
			// 2. The value is single string but we have multiple properties exist.
			// 3. The value is single string with only one property.
			$declaration = '';

			// Value can be provided as a string or array in following format:
			// array(
			// 'property_1' => 'value_1', 'property_2' => 'value_2', ... ,
			// 'property_n' => 'value_n'
			// )
			if ( is_array( $current_value ) ) {
				foreach ( $current_value as $this_property => $this_value ) {
					if ( empty( $this_property ) || '' === $this_value ) {
						continue;
					}

					// Get valid value. Previously, it only works for range control value and run
					// et_builder_process_range_value function directly. Keep it as it is now for
					// backward compatibility.
					$valid_value = $this_value;
					if ( 'range' === $type ) {
						$valid_value = et_builder_process_range_value( $this_value, $this_property );
					}

					$declaration .= sprintf(
						'%1$s: %2$s%3$s',
						$this_property,
						esc_html( $valid_value ),
						'' !== $additional_css ? $additional_css : ';'
					);
				}
			} elseif ( is_array( $css_property ) ) {
				// Get valid value. Previously, it only works for range control value and run
				// et_builder_process_range_value function directly.
				$valid_value = $current_value;

				foreach ( $css_property as $this_property ) {
					if ( empty( $this_property ) ) {
						continue;
					}

					if ( 'range' === $type ) {
						$valid_value = et_builder_process_range_value( $current_value, $this_property );
					}

					$declaration .= sprintf(
						'%1$s: %2$s%3$s',
						$this_property,
						esc_html( $valid_value ),
						'' !== $additional_css ? $additional_css : ';'
					);
				}
			} elseif ( ! empty( $css_property ) ) {
				// Get valid value. Previously, it only works for range control value and run
				// et_builder_process_range_value function directly.
				$valid_value = $current_value;
				if ( 'range' === $type ) {
					$valid_value = et_builder_process_range_value( $current_value, $css_property );
				}

				$declaration = sprintf(
					'%1$s: %2$s%3$s',
					$css_property,
					esc_html( $valid_value ),
					'' !== $additional_css ? $additional_css : ';'
				);
			}

			if ( '' === $declaration ) {
				continue;
			}

			$style = array(
				'selector'    => $selector,
				'declaration' => $declaration,
			);

			if ( 'desktop_only' === $device ) {
				$style['media_query'] = ET_Builder_Element::get_media_query( 'min_width_981' );
			} elseif ( 'desktop' !== $device ) {
				$current_media_query  = 'tablet' === $device ? 'max_width_980' : 'max_width_767';
				$style['media_query'] = ET_Builder_Element::get_media_query( $current_media_query );
			}

			// Priority.
			if ( '' !== $priority ) {
				$style['priority'] = $priority;
			}

			ET_Builder_Element::set_style( $function_name, $style );
		}
	}

	/**
	 * Generates the CSS code for responsive options based on existing declaration.
	 *
	 * Similar with generate_responsive_css(), but it's only used to declare the CSS and selector
	 * without set styles on ET_Builder_Element.
	 *
	 * @since 3.23
	 *
	 * @param array  $values_array  All device values.
	 * @param mixed  $css_selector  CSS selector.
	 * @param string $function_name Module slug.
	 * @param string $priority      CSS style declaration priority.
	 */
	public function declare_responsive_css( $values_array, $css_selector, $function_name, $priority = '' ) {
		if ( empty( $values_array ) ) {
			return;
		}

		foreach ( $values_array as $device => $declaration ) {
			if ( empty( $declaration ) ) {
				continue;
			}

			$style = array(
				'selector'    => $css_selector,
				'declaration' => $declaration,
			);

			// Media query.
			if ( 'desktop_only' === $device ) {
				$style['media_query'] = ET_Builder_Element::get_media_query( 'min_width_981' );
			} elseif ( 'desktop' !== $device ) {
				$current_media_query  = 'tablet' === $device ? 'max_width_980' : 'max_width_767';
				$style['media_query'] = ET_Builder_Element::get_media_query( $current_media_query );
			}

			// Priority.
			if ( '' !== $priority ) {
				$style['priority'] = $priority;
			}

			ET_Builder_Element::set_style( $function_name, $style );
		}
	}

	/**
	 * Generate additional responsive fields such as _tablet, _phone, and _last_edited.
	 *
	 * @since 3.23
	 *
	 * @param  string $field_name  Base field name.
	 * @param  string $toggle_slug Toggle slug name.
	 * @param  string $tab_slug    Tab slug name.
	 * @param  array  $field       Field data in array.
	 * @return array               Responsive fields set.
	 */
	public function generate_responsive_fields( $field_name, $toggle_slug, $tab_slug, $field = array() ) {
		$responsive_options = array();

		// Add fields with responsive suffix for each devices.
		$responsive_options[ "{$field_name}_tablet" ]      = array(
			'type'        => 'skip',
			'tab_slug'    => $tab_slug,
			'toggle_slug' => $toggle_slug,
		);
		$responsive_options[ "{$field_name}_phone" ]       = array(
			'type'        => 'skip',
			'tab_slug'    => $tab_slug,
			'toggle_slug' => $toggle_slug,
		);
		$responsive_options[ "{$field_name}_last_edited" ] = array(
			'type'        => 'skip',
			'tab_slug'    => $tab_slug,
			'toggle_slug' => $toggle_slug,
		);

		// Add computed effect field on mobile and hover fields.
		if ( ! empty( $field['computed_affects'] ) && isset( $field['affects_mobile'] ) && true === $field['affects_mobile'] ) {
			$responsive_options[ "{$field_name}_tablet" ]['computed_affects'] = $field['computed_affects'];
			$responsive_options[ "{$field_name}_phone" ]['computed_affects']  = $field['computed_affects'];

			$responsive_options[ "{$field_name}__hover" ] = array(
				'type'             => 'skip',
				'tab_slug'         => $tab_slug,
				'toggle_slug'      => $toggle_slug,
				'computed_affects' => $field['computed_affects'],
			);
		}

		// Set default on tablet and phone if needed.
		if ( ! empty( $field['default_on_mobile'] ) ) {
			$default        = ! empty( $field['default'] ) ? $field['default'] : '';
			$default_mobile = ! empty( $field['default_on_mobile'] ) ? $field['default_on_mobile'] : $default;
			$default_tablet = ! empty( $field['default_on_tablet'] ) ? $field['default_on_tablet'] : $default_mobile;
			$default_phone  = ! empty( $field['default_on_phone'] ) ? $field['default_on_phone'] : $default_mobile;

			$responsive_options[ "{$field_name}_tablet" ]['default'] = $default_tablet;
			$responsive_options[ "{$field_name}_phone" ]['default']  = $default_phone;
		}

		// Set default on hover if needed.
		if ( ! empty( $field['default_on_hover'] ) ) {
			$default       = ! empty( $field['default'] ) ? $field['default'] : '';
			$default_hover = ! empty( $field['default_on_hover'] ) ? $field['default_on_hover'] : $default_mobile;

			$responsive_options[ "{$field_name}__hover" ]['default'] = $default_hover;
		}

		return $responsive_options;
	}

	/**
	 * Get main background value based on enabled status of current field. It's used to selectively
	 * get the correct color, gradient status, image, and video. It's introduced along with new
	 * enable fields to decide should we remove or inherit the value from larger device.
	 *
	 * @since 3.24.1
	 *
	 * @param array  $attrs           All module attributes.
	 * @param string $base_setting    Setting need to be checked.
	 * @param string $preview_mode    Current preview mode.
	 * @param string $background_base Background base name (background, button_bg, etc.)
	 * @param array  $fields          All module fields definition.
	 * @param string $value           Active value.
	 * @param string $default_value   Active default value.
	 *
	 * @return string New value.
	 */
	public function get_inheritance_background_value( $attrs, $base_setting, $preview_mode, $background_base = 'background', $fields = array(), $value = '', $default_value = '' ) {
		// Default new value is same with the generated or active one.
		$new_value = $value;

		$enable_fields = array(
			"{$background_base}_color"              => "{$background_base}_enable_color",
			'use_background_color_gradient'         => 'use_background_color_gradient',
			"{$background_base}_use_color_gradient" => "{$background_base}_use_color_gradient",
			"{$background_base}_image"              => "{$background_base}_enable_image",
			"video_{$background_base}_values"       => "video_{$background_base}_values",
			"{$background_base}_pattern_style"      => "{$background_base}_enable_pattern_style",
			"{$background_base}_mask_style"         => "{$background_base}_enable_mask_style",
		);

		// Empty string is slug for desktop.
		$map_slugs = array(
			'desktop' => array( '' ),
			'hover'   => array( '__hover', '' ),
			'sticky'  => array( '__sticky', '' ),
			'tablet'  => array( '_tablet', '' ),
			'phone'   => array( '_phone', '_tablet', '' ),
		);

		// Start checking if current field is enabled or disabled.
		$base_enable_field_name = ! empty( $enable_fields[ $base_setting ] ) ? $enable_fields[ $base_setting ] : '';

		// Bail early if setting name is different.
		if ( '' === $base_enable_field_name || ! isset( $map_slugs[ $preview_mode ] ) ) {
			return $new_value;
		}

		$new_value = '';

		$origin_mp4_enabled  = '';
		$origin_mp4_data     = array();
		$origin_webm_enabled = '';
		$origin_webm_data    = array();

		foreach ( $map_slugs[ $preview_mode ] as $slug ) {

			// BG Color, BG Image, BG Pattern, BG Mask.
			if (
				in_array(
					$base_setting,
					array(
						"{$background_base}_color",
						"{$background_base}_image",
						"{$background_base}_pattern_style",
						"{$background_base}_mask_style",
					),
					true
				)
			) {
				$base_type      = str_replace( "{$background_base}_", '', $base_setting );
				$enable_default = ! empty( $fields[ "{$background_base}_enable_{$base_type}{$slug}" ] ) && ! empty( $fields[ "{$background_base}_enable_{$base_type}{$slug}" ]['default'] ) ? $fields[ "{$background_base}_enable_{$base_type}{$slug}" ]['default'] : '';
				$enable_value   = ! empty( $attrs[ "{$background_base}_enable_{$base_type}{$slug}" ] ) ? $attrs[ "{$background_base}_enable_{$base_type}{$slug}" ] : $enable_default;
				$setting_value  = ! empty( $attrs[ "{$background_base}_{$base_type}{$slug}" ] ) ? $attrs[ "{$background_base}_{$base_type}{$slug}" ] : '';
				$is_tab_enabled = 'off' !== $enable_value;

				if ( '' !== $setting_value && $is_tab_enabled ) {
					$new_value = $setting_value;
					break;
				} elseif ( ! $is_tab_enabled ) {
					$new_value = '';
					break;
				}

				// BG Gradient.
			} elseif ( in_array( $base_setting, array( 'use_background_color_gradient', "{$background_base}_use_color_gradient" ), true ) ) {

				$new_value = 'off';

				$field_map = array(
					'use_background_color_gradient' => array(
						'value' => "use_background_color_gradient{$slug}",
						'start' => "{$background_base}_color_gradient_start{$slug}",
						'end'   => "{$background_base}_color_gradient_end{$slug}",
						'stops' => "{$background_base}_color_gradient_stops{$slug}",
						'unit'  => "{$background_base}_color_gradient_unit{$slug}",
					),
					"{$background_base}_use_color_gradient" => array(
						'value' => "{$background_base}_use_color_gradient{$slug}",
						'start' => "{$background_base}_color_gradient_start{$slug}",
						'end'   => "{$background_base}_color_gradient_end{$slug}",
						'stops' => "{$background_base}_color_gradient_stops{$slug}",
						'unit'  => "{$background_base}_color_gradient_unit{$slug}",
					),
				);

				$field_value = '';
				$field_start = '';
				$field_end   = '';
				$field_stops = '';

				if ( ! empty( $field_map[ $base_setting ] ) ) {
					$field_value = ! empty( $field_map[ $base_setting ]['value'] ) ? $field_map[ $base_setting ]['value'] : '';
					$field_start = ! empty( $field_map[ $base_setting ]['start'] ) ? $field_map[ $base_setting ]['start'] : '';
					$field_end   = ! empty( $field_map[ $base_setting ]['end'] ) ? $field_map[ $base_setting ]['end'] : '';
					$field_stops = ! empty( $field_map[ $base_setting ]['stops'] ) ? $field_map[ $base_setting ]['stops'] : '';
				}

				// Set value from attrs, otherwise, assign default value, for desktop/tablet/phone.
				$use_gradient_value   = $this->get_any_value( $attrs, $field_value, 'off', true );
				$gradient_start_value = $this->get_any_value( $attrs, $field_start, '', true );
				$gradient_end_value   = $this->get_any_value( $attrs, $field_end, '', true );
				$gradient_stops_value = $this->get_any_value( $attrs, $field_stops, '', true );

				// Set value from attrs, otherwise, assign value from desktop.
				if ( in_array( $slug, array( '__hover', '__sticky' ), true ) ) {
					$use_gradient_value   = ! empty( $attrs[ $field_value ] ) ? $attrs[ $field_value ] : $use_gradient_value;
					$gradient_start_value = ! empty( $attrs[ $field_start ] ) ? $attrs[ $field_start ] : $gradient_start_value;
					$gradient_end_value   = ! empty( $attrs[ $field_end ] ) ? $attrs[ $field_end ] : $gradient_end_value;
					$gradient_stops_value = ! empty( $attrs[ $field_stops ] ) ? $attrs[ $field_stops ] : $gradient_stops_value;
				}

				$is_gradient_enabled = 'off' !== $use_gradient_value;

				if ( ( '' !== $gradient_stops_value || ( '' !== $gradient_start_value || '' !== $gradient_end_value ) ) && $is_gradient_enabled ) {
					$new_value = 'on';
					break;
				} elseif ( ! $is_gradient_enabled ) {
					$new_value = 'off';
					break;
				}

				// BG Video.
			} elseif ( "video_{$background_base}_values" === $base_setting ) {
				$base_slug    = preg_replace( '/[_]+/', '', $slug );
				$current_mode = '' !== $base_slug ? $base_slug : 'desktop';

				// Video markup.
				$video_background = et_()->array_get( $attrs, "{$base_setting}.{$current_mode}", '' );

				// MP4.
				$enable_mp4_default = et_()->array_get( $fields, "{$background_base}_enable_video_mp4{$slug}", '' );
				$enable_mp4_value   = $this->get_any_value( $attrs, "{$background_base}_enable_video_mp4{$slug}", $enable_mp4_default, true );
				$is_mp4_enabled     = 'off' !== $enable_mp4_value;

				$video_mp4_value = et_pb_responsive_options()->get_any_value( $attrs, "{$background_base}_video_mp4" );
				if ( 'hover' === $current_mode ) {
					$video_mp4_hover_value = et_()->array_get( $attrs, "{$background_base}_video_mp4__hover", '' );
					$video_mp4_value       = '' !== $video_mp4_hover_value ? $video_mp4_hover_value : $video_mp4_value;
				} else {
					$video_mp4_value = et_pb_responsive_options()->get_any_value( $attrs, "{$background_base}_video_mp4{$slug}", '', true );
				}

				// Check MP4 enabled and data status.
				if ( '' === $origin_mp4_enabled ) {
					if ( '' !== $video_mp4_value && $is_mp4_enabled ) {
						$origin_mp4_enabled = 'enabled';
						$origin_mp4_data    = array(
							'mode'             => $current_mode,
							'video_value'      => $video_mp4_value,
							'video_background' => $video_background,
							'display'          => ! empty( $video_background ) ? 'self' : 'inherit',
						);
					} elseif ( false === $is_mp4_enabled ) {
						$origin_mp4_enabled = 'disabled';
						$origin_mp4_data    = array();
					}
				} elseif ( 'enabled' === $origin_mp4_enabled ) {
					if ( isset( $origin_mp4_data['video_background'] ) && empty( $origin_mp4_data['video_background'] ) ) {
						$origin_mp4_data['video_background'] = $video_background;
					}
				}

				// Webm.
				$enable_webm_default = et_()->array_get( $fields, "{$background_base}_enable_video_webm{$slug}", '' );
				$enable_webm_value   = $this->get_any_value( $attrs, "{$background_base}_enable_video_webm{$slug}", $enable_webm_default, true );
				$is_webm_enabled     = 'off' !== $enable_webm_value;

				$video_webm_value = et_pb_responsive_options()->get_any_value( $attrs, "{$background_base}_video_webm" );
				if ( 'hover' === $current_mode ) {
					$video_webm_hover_value = et_()->array_get( $attrs, "{$background_base}_video_webm__hover", '' );
					$video_webm_value       = '' !== $video_webm_hover_value ? $video_webm_hover_value : $enable_webm_value;
				} else {
					$video_webm_value = et_pb_responsive_options()->get_any_value( $attrs, "{$background_base}_video_webm{$slug}", '', true );
				}

				// Check Webm enabled and data status.
				if ( '' === $origin_webm_enabled ) {
					if ( '' !== $video_webm_value && $is_webm_enabled ) {
						$origin_webm_enabled = 'enabled';
						$origin_webm_data    = array(
							'mode'             => $current_mode,
							'video_value'      => $video_webm_value,
							'video_background' => $video_background,
							'display'          => ! empty( $video_background ) ? 'self' : 'inherit',
						);
					} elseif ( ! $is_webm_enabled ) {
						$origin_webm_enabled = 'disabled';
						$origin_webm_data    = array();
					}
				} elseif ( 'enabled' === $origin_webm_enabled ) {
					if ( isset( $origin_webm_data['video_background'] ) && empty( $origin_webm_data['video_background'] ) ) {
						$origin_webm_data['video_background'] = $video_background;
					}
				}

				// Continue if current mode is not desktop.
				if ( '' !== $slug ) {
					continue;
				}

				// Decide to display the video or not.
				if ( 'disabled' === $origin_mp4_enabled && 'disabled' === $origin_webm_enabled ) {
					$new_value = array(
						'display' => 'hide',
						'video'   => '',
					);
				} else {
					// MP4 display and video status.
					$mp4_enabled_display  = et_()->array_get( $origin_mp4_data, 'display', '' );
					$mp4_enabled_mode     = et_()->array_get( $origin_mp4_data, 'mode', '' );
					$mp4_video_background = '';
					if ( $preview_mode === $mp4_enabled_mode ) {
						$mp4_video_background = et_()->array_get( $origin_mp4_data, 'video_background', '' );
					}

					// Webm display and video status.
					$webm_enabled_display  = et_()->array_get( $origin_webm_data, 'display', '' );
					$webm_enabled_mode     = et_()->array_get( $origin_webm_data, 'mode', '' );
					$webm_video_background = '';
					if ( $preview_mode === $webm_enabled_mode ) {
						$webm_video_background = et_()->array_get( $origin_webm_data, 'video_background', '' );
					}

					// Set display current video or not.
					$new_video_display = 'hide';
					if ( '' !== $mp4_enabled_display ) {
						$new_video_display = $mp4_enabled_display;
					} elseif ( '' !== $webm_enabled_display ) {
						$new_video_display = $webm_enabled_display;
					}

					// Set video markup.
					$new_video_background = '';
					if ( '' !== $mp4_video_background ) {
						$new_video_background = $mp4_video_background;
					} elseif ( '' !== $webm_video_background ) {
						$new_video_background = $webm_video_background;
					}

					$new_value = array(
						'display' => $new_video_display,
						'video'   => $new_video_background,
					);
				}
			}
		}

		return $new_value;
	}

	public function create( $additional_options ) {
		// Add hover field indication
		$additional_options['hover_enabled'] = array(
			'type'    => 'skip',
			'default' => 0,
		);

		// Generate responsive fields for additional options (Design).
		// There are 4 types where the mobile_options exist on the options.
		// 1. Exist on the option definition.
		// 2. Exist on the computed field type, just like point 1 but we threat it differently
		// because there are some properties need to be updated and added.
		// 3. Exist on the background-field.
		// 4. Exist on the composite field.
		foreach ( $additional_options as $field_name => $field ) {
			$is_mobile_options = isset( $field['mobile_options'] ) && $field['mobile_options'];
			$is_hover          = isset( $field['hover'] ) && 'tabs' === $field['hover'];
			$field_type        = isset( $field['type'] ) ? $field['type'] : '';
			$field_context     = isset( $field['context'] ) ? $field['context'] : '';
			$field_last_edited = isset( $field['last_edited'] ) ? $field['last_edited'] : '';

			// Mobile options property maybe exist on the field.
			if ( $is_mobile_options ) {
				// Get tab and toggle slugs value.
				$tab_slug    = isset( $field['tab_slug'] ) ? $field['tab_slug'] : '';
				$toggle_slug = isset( $field['toggle_slug'] ) ? $field['toggle_slug'] : '';

				// 2. Mobile options property for computed fields.
				if ( 'computed' === $field_type ) {
					// Computed depends on. Add suffix after depends on info.
					if ( ! empty( $field['computed_depends_on'] ) ) {
						$computed_depends_on = $field['computed_depends_on'];
						foreach ( $computed_depends_on as $depends_value ) {
							if ( $is_hover ) {
								array_push( $field['computed_depends_on'], "{$depends_value}_tablet", "{$depends_value}_phone", "{$depends_value}__hover" );
							} else {
								array_push( $field['computed_depends_on'], "{$depends_value}_tablet", "{$depends_value}_phone" );
							}
						}
					}

					// Computed minimum. Add suffix after minimum info.
					if ( ! empty( $field['computed_minimum'] ) ) {
						$computed_minimum = $field['computed_minimum'];
						foreach ( $computed_minimum as $minimum_value ) {
							if ( $is_hover ) {
								array_push( $field['computed_minimum'], "{$minimum_value}_tablet", "{$minimum_value}_phone", "{$minimum_value}__hover" );
							} else {
								array_push( $field['computed_minimum'], "{$minimum_value}_tablet", "{$minimum_value}_phone" );
							}
						}
					}

					$additional_options[ "{$field_name}" ] = $field;

					continue;
				}

				// 3. Mobile options property maybe exist under background field.
				if ( 'background-field' === $field_type ) {
					// Just in case current field is background-field and the mobile_options
					// attributes are located in the fields. Ensure background fields is exist.
					if ( ! empty( $field['background_fields'] ) ) {
						// Fetch the fields and check for mobile_options.
						foreach ( $field['background_fields'] as $background_name => $background_field ) {
							if ( isset( $background_field['mobile_options'] ) && $background_field['mobile_options'] ) {
								// Get tab and toggle slugs value.
								$tab_slug    = isset( $background_field['tab_slug'] ) ? $background_field['tab_slug'] : '';
								$toggle_slug = isset( $background_field['toggle_slug'] ) ? $background_field['toggle_slug'] : '';

								// Add fields with responsive suffix for each devices.
								$additional_options = array_merge(
									$additional_options,
									et_pb_responsive_options()->generate_responsive_fields( $background_name, $toggle_slug, $tab_slug )
								);
							}
						}
					}

					continue;
				}

				// 1. Mobile options property added directly on options definition. Add fields
				// with responsive suffix for each devices.
				$additional_options = array_merge(
					$additional_options,
					et_pb_responsive_options()->generate_responsive_fields( $field_name, $toggle_slug, $tab_slug, $field )
				);

				// Additional last edited field just in case we need more last edited field.
				if ( ! empty( $field_last_edited ) ) {
					$additional_options[ "{$field_last_edited}_last_edited" ] = array(
						'type'        => 'skip',
						'tab_slug'    => $tab_slug,
						'toggle_slug' => $toggle_slug,
					);
				}

				continue;
			}

			// 4. Mobile options property maybe exist under composite field.
			if ( 'composite' === $field_type ) {
				// Just in case current field is composite and the mobile_options attributes
				// are located in the controls. Ensure composite structure is exist.
				$composite_structure = isset( $field['composite_structure'] ) ? $field['composite_structure'] : array();
				if ( empty( $composite_structure ) ) {
					continue;
				}

				foreach ( $composite_structure as $composite_field ) {
					// Ensure composite field controls is exist and not empty.
					$composite_field_controls = isset( $composite_field['controls'] ) ? $composite_field['controls'] : array();
					if ( empty( $composite_field_controls ) ) {
						continue;
					}

					// Fetch the controls and check for mobile_options.
					foreach ( $composite_field_controls as $control_name => $control ) {
						if ( isset( $control['mobile_options'] ) && $control['mobile_options'] ) {
							// Get tab and toggle slugs value.
							$tab_slug    = isset( $control['tab_slug'] ) ? $control['tab_slug'] : '';
							$toggle_slug = isset( $control['toggle_slug'] ) ? $control['toggle_slug'] : '';

							// Add fields with responsive suffix for each devices.
							$additional_options = array_merge(
								$additional_options,
								et_pb_responsive_options()->generate_responsive_fields( $control_name, $toggle_slug, $tab_slug )
							);
						}
					}
				}
			}
		}
		return $additional_options;
	}

}


