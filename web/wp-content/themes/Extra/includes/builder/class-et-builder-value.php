<?php
/**
 * Represent a simple value or a dynamic one.
 * Used for module attributes and content.
 *
 * @package Divi
 * @subpackage Builder
 * @since 3.17.2
 */

/**
 * Class ET_Builder_Value.
 */
class ET_Builder_Value {
	/**
	 * Flag whether the value is static or dynamic.
	 *
	 * @since 3.17.2
	 *
	 * @var bool
	 */
	protected $dynamic = false;

	/**
	 * Value content. Represents the dynamic content type when dynamic.
	 *
	 * @since 3.17.2
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * Array of dynamic content settings.
	 *
	 * @since 3.17.2
	 *
	 * @var mixed[]
	 */
	protected $settings = array();

	/**
	 * ET_Builder_Value constructor.
	 *
	 * @since 3.17.2
	 *
	 * @param boolean $dynamic Whether content is dynamic.
	 * @param string  $content Value content.
	 * @param array   $settings Dynamic content settings.
	 */
	public function __construct( $dynamic, $content, $settings = array() ) {
		$this->dynamic  = $dynamic;
		$this->content  = $content;
		$this->settings = $settings;
	}

	/**
	 * Check if the value is dynamic or not.
	 *
	 * @since 3.17.2
	 *
	 * @return bool
	 */
	public function is_dynamic() {
		return $this->dynamic;
	}

	/**
	 * Retrieve the value content.
	 *
	 * @since 4.4.4
	 *
	 * @return string
	 */
	public function get_content() {
		return $this->content;
	}

	/**
	 * Get the resolved content.
	 *
	 * @since 3.17.2
	 *
	 * @param integer $post_id Post id.
	 *
	 * @return string
	 */
	public function resolve( $post_id ) {
		if ( ! $this->dynamic ) {
			return $this->content;
		}

		return et_builder_resolve_dynamic_content( $this->content, $this->settings, $post_id, 'display' );
	}

	/**
	 * Get the static content or a serialized representation of the dynamic one.
	 *
	 * @since 3.17.2
	 *
	 * @return string
	 */
	public function serialize() {
		if ( ! $this->dynamic ) {
			return $this->content;
		}

		return et_builder_serialize_dynamic_content( $this->dynamic, $this->content, $this->settings );
	}

	/**
	 * Get settings value.
	 *
	 * @since 4.23.2
	 *
	 * @param string $setting_name Setting name.
	 *
	 * @return mixed
	 */
	public function get_settings( $setting_name ) {
		if ( ! isset( $this->settings[ $setting_name ] ) ) {
			return null;
		}

		return $this->settings[ $setting_name ];
	}

	/**
	 * Set settings value.
	 *
	 * @since 4.23.2
	 *
	 * @param string $setting_name Setting name.
	 * @param mixed  $setting_value Setting value.
	 *
	 * @return void
	 */
	public function set_settings( $setting_name, $setting_value ) {
		if ( is_null( $setting_value ) ) {
			return;
		}

		$this->settings[ $setting_name ] = $setting_value;
	}
}
