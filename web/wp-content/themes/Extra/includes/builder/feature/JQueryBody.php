<?php
/**
 * Move JQuery from head to body.
 *
 * @package Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class to move JQuery from head to body.
 */
class ET_Builder_JQuery_Body {
	/**
	 * Cache.
	 *
	 * @var array.
	 */
	protected $_has_jquery_dep = [];

	/**
	 * Should move jquery.
	 *
	 * @var bool|null.
	 */
	protected static $_should_move_jquery = null;

	/**
	 * Instance of `ET_Builder_JQuery_Body`.
	 *
	 * @var ET_Builder_JQuery_Body
	 */
	private static $_instance;

	/**
	 * ET_Builder_JQuery_Body constructor.
	 */
	public function __construct() {
		add_action( 'wp_print_scripts', [ $this, 'wp_print_scripts' ], 99 );

		add_action( 'wp_head', [ $this, 'add_jquery_sub' ], 1 );
		add_action( 'wp_enqueue_scripts', [ $this, 'remove_jquery_sub' ] );
	}

	/**
	 * Get post content from Theme Builder templates.
	 * Combine it with the post content from the current post and integration code.
	 *
	 * @return string
	 * @since 4.10.0
	 */
	public function get_all_content() {
		static $all_content;

		// Cache the value.
		if ( ! empty( $all_content ) ) {
			return $all_content;
		}

		global $post, $shortname;

		$all_content = empty( $post ) ? '' : $post->post_content;
		$tb_layouts  = et_theme_builder_get_template_layouts();

		// Add TB templates content.
		if ( ! empty( $tb_layouts ) ) {
			$post_types = [
				ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE,
				ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE,
				ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE,
			];

			foreach ( $post_types as $post_type ) {
				$layout = $tb_layouts[ $post_type ];
				if ( $layout['override'] && ! empty( $layout['enabled'] ) ) {
					$template     = get_post( intval( $layout['id'] ) );
					$all_content .= $template->post_content;
				}
			}
		}

		$integrations = [
			'head',
			'body',
			'single_top',
			'single_bottom',
		];

		// Add Integration code.
		foreach ( $integrations as $integration ) {
			$all_content .= et_get_option( $shortname . '_integration_' . $integration );
		}

		return $all_content;
	}

	/**
	 * Recursively check if a script (or its deps) depends on JQuery.
	 *
	 * @since 4.10.0
	 * @param string $script Script Handle.
	 *
	 * @return bool
	 */
	public function has_jquery_dep( $script ) {
		global $wp_scripts;

		$registered = $wp_scripts->registered;
		$handles    = [ $script ];
		$stack      = [];
		$result     = false;

		while ( false === $result && $handles ) {
			foreach ( $handles as $handle ) {
				if ( ! empty( $this->_has_jquery_dep[ $handle ] ) ) {
					$result = true;
					break;
				}

				if ( isset( $registered[ $handle ] ) ) {
					$deps = $registered[ $handle ]->deps;
					if ( $deps ) {
						if ( in_array( 'jquery-core', $deps, true ) ) {
							$result = true;
							break;
						}
						array_push( $stack, $deps );
					}
				}
			}

			$handles = array_pop( $stack );
		}

		$this->_has_jquery_dep[ $script ] = $result;
		return $result;
	}

	/**
	 * Get script deps.
	 *
	 * @since 4.10.0
	 * @param string $script Script Handle.
	 *
	 * @return array
	 */
	public function get_deps( $script ) {
		global $wp_scripts;

		$registered = $wp_scripts->registered;
		$handles    = is_array( $script ) ? $script : [ $script ];
		$all_deps   = $handles;
		$stack      = [];
		$done       = [];

		while ( $handles ) {
			foreach ( $handles as $handle ) {
				if ( ! isset( $done[ $handle ] ) && isset( $registered[ $handle ] ) ) {
					$deps = $registered[ $handle ]->deps;
					if ( $deps ) {
						$all_deps = array_merge( $all_deps, $deps );
						array_push( $stack, $deps );
					}
					$done[ $handle ] = true;
				}
			}
			$handles = array_pop( $stack );
		}

		return array_unique( $all_deps );
	}

	/**
	 * Check if a script is currently enqueued in HEAD.
	 *
	 * @since 4.10.0
	 * @param string $handle Script Handle.
	 *
	 * @return bool
	 */
	public function in_head( $handle ) {
		global $wp_scripts;

		if ( empty( $wp_scripts->registered[ $handle ] ) ) {
			return false;
		}

		$script = $wp_scripts->registered[ $handle ];

		if ( isset( $script->args ) && 1 === $script->args ) {
			return false;
		}

		if ( isset( $script->extra['group'] ) && 1 === $script->extra['group'] ) {
			return false;
		}

		return true;
	}

	/**
	 * Check whether some content includes jQuery or not.
	 *
	 * @since 4.10.0
	 * @param string $content Content.
	 *
	 * @return bool
	 */
	public static function has_jquery_content( $content ) {
		$has_jquery_regex = '/\(jQuery|jQuery\.|jQuery\)/';
		return 1 === preg_match( $has_jquery_regex, $content );
	}

	/**
	 * Move jQuery / migrate / 3P scripts in BODY.
	 *
	 * @since 4.10.0
	 *
	 * @return void
	 */
	public function wp_print_scripts() {
		global $shortname;

		if ( $this->should_move_jquery() ) {
			global $wp_scripts;
			$queue = $this->get_deps( $wp_scripts->queue );
			$head  = in_array( 'jquery-migrate', $queue, true ) ? [ 'jquery-migrate' ] : [];
			$mode  = 'on' === et_get_option( $shortname . '_enable_jquery_body_super' ) ? 'super' : 'safe';

			// Find all head scripts that depends on JQuery.
			foreach ( $queue as $handle ) {
				if ( $this->in_head( $handle ) && $this->has_jquery_dep( $handle ) ) {
					if ( 'safe' === $mode && 'jquery' !== $handle && 'jquery-migrate' !== $handle ) {
						// Bail out when a script requiring jQuery is found in head.
						return;
					}
					$head[] = $handle;
				}
			}

			$registered = $wp_scripts->registered;

			// Disable the feature when finding a script which does not depend on jQuery
			// but still uses it inside its inlined content (before/after).
			foreach ( $queue as $handle ) {
				if ( ! $this->has_jquery_dep( $handle ) ) {
					$script = $registered[ $handle ];
					$data   = '';

					if ( isset( $script->extra['data'] ) ) {
						$data .= $script->extra['data'];
					}
					if ( isset( $script->extra['before'] ) ) {
						$data .= join( '', $script->extra['before'] );
					}
					if ( isset( $script->extra['after'] ) ) {
						$data .= join( '', $script->extra['after'] );
					}

					if ( ! empty( $data ) && self::has_jquery_content( $data ) ) {
						return;
					}
				}
			}

			// Disable the feature when jQuery is found in TB/Post Content.
			if ( self::has_jquery_content( $this->get_all_content() ) ) {
				return;
			}

			if ( ! empty( $head ) ) {
				foreach ( $this->get_deps( $head ) as $handle ) {
					$wp_scripts->add_data( $handle, 'group', 1 );
				}
			}
		}
	}

	/**
	 * Get the class instance.
	 *
	 * @since 4.10.0
	 *
	 * @return ET_Builder_JQuery_Body
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Fake jQuery in head when jQuery body option is true.
	 */
	public function add_jquery_sub() {
		global $shortname;

		$jquery_compatibility_enabled = 'on' === et_get_option( $shortname . '_enable_jquery_compatibility', 'on' ) ? true : false;

		if ( $this->should_move_jquery() && $jquery_compatibility_enabled ) {
			echo '<script type="text/javascript">
			let jqueryParams=[],jQuery=function(r){return jqueryParams=[...jqueryParams,r],jQuery},$=function(r){return jqueryParams=[...jqueryParams,r],$};window.jQuery=jQuery,window.$=jQuery;let customHeadScripts=!1;jQuery.fn=jQuery.prototype={},$.fn=jQuery.prototype={},jQuery.noConflict=function(r){if(window.jQuery)return jQuery=window.jQuery,$=window.jQuery,customHeadScripts=!0,jQuery.noConflict},jQuery.ready=function(r){jqueryParams=[...jqueryParams,r]},$.ready=function(r){jqueryParams=[...jqueryParams,r]},jQuery.load=function(r){jqueryParams=[...jqueryParams,r]},$.load=function(r){jqueryParams=[...jqueryParams,r]},jQuery.fn.ready=function(r){jqueryParams=[...jqueryParams,r]},$.fn.ready=function(r){jqueryParams=[...jqueryParams,r]};</script>';
		}
	}

	/**
	 * Disable the Fake jQuery in head when jQuery body option is true.
	 */
	public function remove_jquery_sub() {
		global $shortname;

		$jquery_compatibility_enabled = 'on' === et_get_option( $shortname . '_enable_jquery_compatibility', 'on' ) ? true : false;

		if ( $this->should_move_jquery() && $jquery_compatibility_enabled ) {
			$script = 'jqueryParams.length&&$.each(jqueryParams,function(e,r){if("function"==typeof r){var n=String(r);n.replace("$","jQuery");var a=new Function("return "+n)();$(document).ready(a)}});';

			wp_add_inline_script( 'jquery', $script, 'after' );
		}
	}

	/**
	 * Check if jQuery should be moved to the body.
	 */
	public function should_move_jquery() {
		global $shortname;

		if ( null === self::$_should_move_jquery ) {
			/**
			 * Filters whether JQuery Body feature is enabled or not.
			 *
			 * @since 4.10.5
			 *
			 * @param bool $enabled JQuery Body enabled value.
			 * @param string $content TB/Post Content.
			 */
			if ( false === apply_filters( 'et_builder_enable_jquery_body', true, $this->get_all_content() ) ) {
				// Bail out if disabled by filter.
				self::$_should_move_jquery = false;
				return false;
			}
			self::$_should_move_jquery = ! ( is_admin() || wp_doing_ajax() || et_is_builder_plugin_active() || is_customize_preview() || is_et_pb_preview() || 'wp-login.php' === $GLOBALS['pagenow'] || et_fb_is_enabled() ) && 'on' === et_get_option( $shortname . '_enable_jquery_body', 'on' );
		}

		return self::$_should_move_jquery;
	}
}

ET_Builder_JQuery_Body::instance();
