<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for the EventOn plugin.
 *
 * @since 3.10
 *
 * @link http://www.myeventon.com/
 */
class ET_Builder_Plugin_Compat_Eventon extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Event post type.
	 *
	 * @var string
	 */
	protected $event_post_type = 'ajde_events';

	/**
	 * Constructor.
	 *
	 * @since 3.10
	 */
	public function __construct() {
		$this->plugin_id = 'eventon/eventon.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 3.10
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_filter( 'et_builder_post_type_blocklist', array( $this, 'maybe_filter_post_type_blocklist' ) );
		add_filter( 'et_builder_third_party_post_types', array( $this, 'maybe_filter_third_party_post_types' ) );
		add_filter( 'et_builder_post_types', array( $this, 'maybe_filter_builder_post_types' ) );
		add_filter( 'et_fb_post_types', array( $this, 'maybe_filter_builder_post_types' ) );
		add_filter( 'et_builder_fb_enabled_for_post', array( $this, 'maybe_filter_fb_enabled_for_post' ), 10, 2 );
	}

	/**
	 * Get whether the EventOn content filter is set to WordPress' default one.
	 *
	 * @since 3.10
	 *
	 * @return boolean
	 */
	public function uses_default_filter() {
		$options = get_option( 'evcal_options_evcal_1' );
		return ! empty( $options['evo_content_filter'] ) && $options['evo_content_filter'] === 'def';
	}

	/**
	 * Maybe filter the post type blocklist if the post type is not supported.
	 *
	 * @since 3.10
	 *
	 * @param string[] $post_types
	 *
	 * @return string[]
	 */
	public function maybe_filter_post_type_blocklist( $post_types ) {
		if ( ! $this->uses_default_filter() ) {
			$post_types[] = $this->event_post_type;
		}

		return $post_types;
	}

	/**
	 * Maybe filter the supported post type allowlist if the post type is supported.
	 *
	 * @since 3.10
	 *
	 * @param string[] $post_types
	 *
	 * @return string[]
	 */
	public function maybe_filter_third_party_post_types( $post_types ) {
		if ( $this->uses_default_filter() ) {
			$post_types[] = $this->event_post_type;
		}

		return $post_types;
	}

	/**
	 * Maybe filter the enabled post type list if the post type has been enabled but the content
	 * filter has been changed back to the unsupported one.
	 *
	 * @since 3.10
	 *
	 * @param string[] $post_types
	 *
	 * @return string[]
	 */
	public function maybe_filter_builder_post_types( $post_types ) {
		if ( ! $this->uses_default_filter() ) {
			$index = array_search( $this->event_post_type, $post_types );
			array_splice( $post_types, $index, 1 );
		}

		return $post_types;
	}

	/**
	 * Maybe disable the FB for a given post if the builder was enabled but the
	 * content filter was switched after that.
	 *
	 * @since 3.10
	 *
	 * @param boolean $enabled
	 * @param integer $post_id
	 *
	 * @return boolean
	 */
	public function maybe_filter_fb_enabled_for_post( $enabled, $post_id ) {
		$post_type = get_post_type( $post_id );

		if ( $post_type === $this->event_post_type && ! $this->uses_default_filter() ) {
			$enabled = false;
		}

		return $enabled;
	}
}

new ET_Builder_Plugin_Compat_Eventon();
