<?php if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access forbidden.' );
}

/**
 * Transition Options helper methods
 *
 * Class ET_Builder_Module_Transition_Options
 */
class ET_Builder_Module_Helper_Transition_Options {

	private static $instance;

	public static function get() {
		if ( empty( self::$instance ) ) {
			self::$instance = new ET_Builder_Module_Helper_Transition_Options();
		}

		return self::$instance;
	}

	/**
	 * Return transition value.
	 *
	 * @since 3.23 Add $device param to support responsive settings.
	 *
	 * @param string $key
	 * @param array  $list
	 * @param string $default
	 * @param string $device
	 *
	 * @return void
	 */
	private function get_value( $key, $list, $default = null, $device = 'desktop' ) {
		$value = (string) ET_Core_Data_Utils::instance()->array_get( $list, $key );

		if ( 'desktop' !== $device ) {
			$responsive = ET_Builder_Module_Helper_ResponsiveOptions::instance();
			$is_enabled = $responsive->is_responsive_enabled( $list, $key );
			$value      = $is_enabled ? $responsive->get_any_value( $list, "{$key}_{$device}", $value, true ) : $value;
		}

		return '' === $value ? $default : $value;
	}

	/**
	 * Returns the module transition duration,
	 * In case the setting is empty, a default value is returned
	 *
	 * @since 3.23 Add $device param to support responsive settings.
	 *
	 * @param array  $props
	 * @param string $device
	 *
	 * @return string
	 */
	public function get_duration( $props, $device = 'desktop' ) {
		return $this->get_value( 'hover_transition_duration', $props, '300ms', $device );
	}

	/**
	 * Returns the module transition speed curve,
	 * In case the setting is empty, a default value is returned
	 *
	 * @since 3.23 Add $device param to support responsive settings.
	 *
	 * @param array  $props
	 * @param string $device
	 *
	 * @return string
	 */
	public function get_easing( $props, $device = 'desktop' ) {
		return $this->get_value( 'hover_transition_speed_curve', $props, 'ease', $device );
	}

	/**
	 * Returns the module transition transition delay,
	 * In case the setting is empty, a default value is returned
	 *
	 * @since 3.23 Add $device param to support responsive settings.
	 *
	 * @param array  $props
	 * @param string $device
	 *
	 * @return string
	 */
	public function get_delay( $props, $device = 'desktop' ) {
		return $this->get_value( 'hover_transition_delay', $props, '0ms', $device );
	}

	/**
	 * Return transition styles.
	 *
	 * @since 3.23 Add $device param to support responsive settings.
	 *
	 * @param string $property
	 * @param array  $props
	 * @param string $device
	 *
	 * @return string
	 */
	public function get_style( $property, $props, $device = 'desktop' ) {
		$duration = $this->get_duration( $props, $device = 'desktop' );
		$easing   = $this->get_easing( $props, $device = 'desktop' );
		$delay    = $this->get_delay( $props, $device = 'desktop' );

		return "{$property} {$duration} {$easing} {$delay}";
	}
}

