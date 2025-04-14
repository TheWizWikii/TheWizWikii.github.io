<?php

// Quick exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Elegant Themes Support Center adds a new page to the WP Admin menu.
 *
 * System Status
 * Here we note system settings that could potentially cause problems. An extended view (displaying all settings we
 * check, not just those with problematic results) can be toggled, with an option to copy this report to the
 * clipboard so it can be pasted in a support ticket.
 *
 * Elegant Themes Support
 * If Remote Access is enabled in this section, Elegant Themes Support will be granted limited access to the user's site
 * (@see ET_Core_SupportCenter::support_user_maybe_create_user()). When this is activated, a second toggle appears
 * for the user that will allow them to enable "Full Admin Privileges" which has no restrictions (only certain ET
 * Support staff will be able to request that the user enables this). Full Admin Privileges can be disabled at any
 * time, but are automatically disabled whenever the Remote Access is disabled (manually or by timeout). Time
 * remaining until Remote Access is automatically deactivated is indicated alongside the toggle. A link for
 * initiating a chat https://www.elegantthemes.com/members-area/help/ is also available in this section.
 *
 * Divi Documentation & Help
 * This section contains common help videos, articles, and a link to full documentation. This is not meant to be a
 * full service documentation center; it's mainly a launch off point.
 *
 * Divi Safe Mode
 * A quick and easy way for users and support to quickly disable plugins and scripts to see if Divi is the cause of
 * an issue. This call to action disables active plugins, custom css, child themes, scripts in the Integrations tab,
 * static css, and combination/minification of CSS and JS. When enabling this, the user will be presented with a
 * list of plugins that will be affected (disabled). Sitewide (not including the Visual Builder), there will be a
 * floating indicator in the upper right or left corner of the website that will indicate that Safe Mode is enabled
 * and will contain a link that takes you to the Support Page to disabled it.
 *
 * Logs
 * If WP_DEBUG_LOG is enabled, WordPress related errors will be archived in a log file. We load the most recent entries
 * of this log file for convienient viewing here, with a link to download the full log, as well as an option to copy
 * the log to the clipboard so it can be pasted in a support ticket.
 *
 * @package ET\Core\SupportCenter
 * @author  Elegant Themes <http://www.elegantthemes.com>
 * @license GNU General Public License v2 <http://www.gnu.org/licenses/gpl-2.0.html>
 *
 * @since 3.24.1 Renamed from `ET_Support_Center` to `ET_Core_SupportCenter`.
 * @since 3.20
 */
class ET_Core_SupportCenter {
	/**
	 * Catch whether the ET_DEBUG flag is set.
	 *
	 * @since 3.20
	 *
	 * @type string
	 */
	protected $DEBUG_ET_SUPPORT_CENTER = false;

	/**
	 * Identifier for the parent theme or plugin activating the Support Center.
	 *
	 * @since 3.20
	 *
	 * @type string
	 */
	protected $parent = '';

	/**
	 * "Nice name" for the parent theme or plugin activating the Support Center.
	 *
	 * @since 3.20
	 *
	 * @type string
	 */
	protected $parent_nicename = '';

	/**
	 * Whether the Support Center was activated through a `plugin` or a `theme`.
	 *
	 * @since 3.20
	 *
	 * @type string
	 */
	protected $child_of = '';

	/**
	 * Identifier for the parent theme or plugin activating the Support Center.
	 *
	 * @since 3.20
	 *
	 * @type string
	 */
	protected $local_path;

	/**
	 * Support User options
	 *
	 * @since 3.20
	 *
	 * @type array
	 */
	protected $support_user_options;

	/**
	 * Support User account name
	 *
	 * @since 3.20
	 *
	 * @type string
	 */
	protected $support_user_account_name = 'elegant_themes_support';

	/**
	 * Support options name in the database
	 *
	 * @since 3.20
	 *
	 * @type string
	 */
	protected $support_user_options_name = 'et_support_options';

	/**
	 * Name of the cron job we use to auto-delete the Support User account
	 *
	 * @since 3.20
	 *
	 * @type string
	 */
	protected $support_user_cron_name = 'et_cron_delete_support_account';

	/**
	 * Expiration time to auto-delete the Support User account via cron
	 *
	 * @since 3.20
	 *
	 * @type string
	 */
	protected $support_user_expiration_time = '+4 days';

	/**
	 * Provide nicename equivalents for boolean values
	 *
	 * @since 3.23
	 *
	 * @type array
	 */
	protected $boolean_label = array( 'False', 'True' );

	/**
	 * Collection of plugins that we will NOT disable when Safe Mode is activated.
	 *
	 * @since 3.20
	 *
	 * @type array
	 */
	protected $safe_mode_plugins_allowlist = array(
		'etdev/etdev.php', // ET Development Workspace
		'bloom/bloom.php', // ET Bloom Plugin
		'monarch/monarch.php', // ET Monarch Plugin
		'divi-builder/divi-builder.php', // ET Divi Builder Plugin
		'ari-adminer/ari-adminer.php', // ARI Adminer
		'query-monitor/query-monitor.php', // Query Monitor
		'woocommerce/woocommerce.php', // WooCommerce
		'really-simple-ssl/rlrsssl-really-simple-ssl.php', // Really Simple SSL
	);

	/**
	 * Capabilities that should be granted to the Administrator role on activation.
	 *
	 * @since 3.28
	 *
	 * @type array
	 */
	protected $support_center_administrator_caps = array(
		'et_support_center',
		'et_support_center_system',
		'et_support_center_remote_access',
		'et_support_center_documentation',
		'et_support_center_safe_mode',
		'et_support_center_logs',
	);

	/**
	 * Collection of Cards that has button to dismiss the Card.
	 *
	 * @since 4.4.7
	 *
	 * @type array
	 */
	protected $card_with_dismiss_button = array(
		'et_hosting_card',
	);

	/**
	 * Core functionality of the class
	 *
	 * @since 4.4.7 Added WP AJAX action for dismissible button for a card
	 * @since 3.20
	 *
	 * @param string $parent Identifier for the parent theme or plugin activating the Support Center.
	 */
	public function __construct( $parent = '' ) {
		// Verbose logging: only log if `wp-config.php` has defined `ET_DEBUG='support_center'`
		$this->DEBUG_ET_SUPPORT_CENTER = defined( 'ET_DEBUG' ) && 'support_center' === ET_DEBUG;

		// Set the identifier for the parent theme or plugin activating the Support Center.
		$this->parent = $parent;

		// Get `et_support_options` settings & set $this->support_user_options
		$this->support_user_get_options();

		// Set the plugins allowlist for Safe Mode
		$this->set_safe_mode_plugins_allowlist();
	}

	/**
	 * WordPress action & filter setup
	 *
	 * @since 3.20
	 */
	public function init() {
		update_option( 'et_support_center_installed', 'true' );

		// Establish which theme or plugin has loaded the Support Center
		$this->set_parent_properties();

		// When initialized, deactivate conflicting plugins
		$this->deactivate_conflicting_plugins();

		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );

		// SC scripts are only used in FE for the "Turn Off Divi Safe Mode" floating button.
		if ( et_core_is_safe_mode_active() ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );
		}

		// Get Site ID with Elegant Themes API & token (needed in advance for Remote Access).
		add_action( 'admin_init', array( $this, 'maybe_set_site_id' ) );

		// Add extra User Role capabilities needed for Remote Access to work with 3rd party software
		add_filter( 'add_et_support_standard_capabilities', array( $this, 'support_user_extra_caps_standard' ), 10, 1 );
		add_filter( 'add_et_support_elevated_capabilities', array( $this, 'support_user_extra_caps_elevated' ), 10, 1 );

		// Make sure that our Support Account's roles are set up
		add_filter( 'add_et_builder_role_options', array( $this, 'support_user_add_role_options' ), 10, 1 );

		// On Multisite installs, grant `unfiltered_html` capabilities to the Support User
		add_filter( 'map_meta_cap', array( $this, 'support_user_map_meta_cap' ), 1, 3 );

		// Add CSS class name(s) to the Support Center page's body tag
		add_filter( 'admin_body_class', array( $this, 'add_admin_body_class_name' ) );

		// Add a link to the Support Center in the admin menu
		add_filter( 'admin_menu', array( $this, 'add_admin_menu_item' ) );

		// When Safe Mode is enabled, add floating frontend indicator
		add_action( 'admin_footer', array( $this, 'maybe_add_safe_mode_indicator' ) );
		add_action( 'wp_footer', array( $this, 'maybe_add_safe_mode_indicator' ) );

		// Add User capabilities to the Administrator Role
		add_action( 'admin_init', array( $this, 'support_center_capabilities_setup' ) );

		if ( 'plugin' === $this->child_of ) {
			// Delete our Support User settings on deactivation
			register_deactivation_hook( __FILE__, array( $this, 'support_user_delete_account' ) );
			register_deactivation_hook( __FILE__, array( $this, 'unlist_support_center' ) );
			register_deactivation_hook( __FILE__, array( $this, 'support_center_capabilities_teardown' ) );
		}
		if ( 'theme' === $this->child_of ) {
			// Delete our Support User settings on deactivation
			add_action( 'switch_theme', array( $this, 'maybe_deactivate_on_theme_switch' ) );
		}

		// Automatically delete our Support User when the time runs out
		add_action( $this->support_user_cron_name, array( $this, 'support_user_cron_maybe_delete_account' ) );
		add_action( 'init', array( $this, 'support_user_maybe_delete_expired_account' ) );
		add_action( 'admin_init', array( $this, 'support_user_maybe_delete_expired_account' ) );

		// Remove KSES filters for ET Support User
		add_action( 'admin_init', array( $this, 'support_user_kses_remove_filters' ) );

		// Update Support User settings via AJAX
		add_action( 'wp_ajax_et_support_user_update', array( $this, 'support_user_update_via_ajax' ) );

		// Toggle Safe Mode via AJAX
		add_action( 'wp_ajax_et_safe_mode_update', array( $this, 'safe_mode_update_via_ajax' ) );

		// Safe Mode: Block restricted actions when Safe Mode active
		add_action( 'admin_footer', array( $this, 'render_safe_mode_block_restricted' ) );

		// Safe Mode: Temporarily disable Custom CSS
		add_action( 'init', array( $this, 'maybe_disable_custom_css' ) );

		// Safe Mode: Remove "Additional CSS" from WP Head action hook
		if ( et_core_is_safe_mode_active() ) {
			remove_action( 'wp_head', 'wp_custom_css_cb', 101 );
		}

		// Support Center Card: Handle Dismiss Button
		add_action( 'wp_ajax_et_dismiss_support_center_card', array( $this, 'dismiss_support_center_card_via_ajax' ) );
	}

	/**
	 * Add User capabilities to the Administrator Role (if it exists) on first run
	 *
	 * @since 3.29.2 Added check to verify the Administrator Role exists before attempting to run `add_cap()`.
	 * @since 3.28
	 */
	public function support_center_capabilities_setup() {
		$support_capabilities = get_option( 'et_support_center_setup_done' );
		$administrator_role   = get_role( 'administrator' );

		if ( $administrator_role && ! $support_capabilities ) {
			foreach ( $this->support_center_administrator_caps as $cap ) {
				$administrator_role->add_cap( $cap );
			}
			update_option( 'et_support_center_setup_done', 'processed' );
		}
	}

	/**
	 * Remove User capabilities from the Administrator Role when product with Support Center is removed
	 *
	 * @since 3.29.2 Added check to verify the Administrator Role exists before attempting to run `remove_cap()`.
	 * @since 3.28
	 */
	public function support_center_capabilities_teardown() {
		$support_capabilities = get_option( 'et_support_center_setup_done' );
		$administrator_role   = get_role( 'administrator' );

		if ( $administrator_role && $support_capabilities ) {
			foreach ( $this->support_center_administrator_caps as $cap ) {
				$administrator_role->remove_cap( $cap );
			}
			delete_option( 'et_support_center_setup_done' );
		}
	}

	/**
	 * Set variables that change depending on whether a theme or a plugin activated the Support Center
	 *
	 * @since 3.20
	 */
	public function set_parent_properties() {
		$core_path = _et_core_normalize_path( trailingslashit( dirname( __FILE__ ) ) );
		$theme_dir = _et_core_normalize_path( trailingslashit( realpath( get_template_directory() ) ) );

		if ( 0 === strpos( $core_path, $theme_dir ) ) {
			$this->child_of   = 'theme';
			$this->local_path = trailingslashit( get_template_directory_uri() . '/core/' );
		} else {
			$this->child_of   = 'plugin';
			$this->local_path = plugins_url( '/', dirname( __FILE__ ) );
		}

		$this->parent_nicename = $this->get_parent_nicename( $this->parent );
	}

	/**
	 * Get the "Nice Name" for the parent theme/plugin
	 *
	 * @param $parent
	 *
	 * @return bool|string
	 */
	public function get_parent_nicename( $parent ) {
		switch ( $parent ) {
			case 'bloom_plugin':
				return 'Bloom';
				break;
			case 'monarch_plugin':
				return 'Monarch';
				break;
			case 'extra_theme':
				return 'Extra';
				break;
			case 'divi_theme':
				return 'Divi';
				break;
			case 'divi_builder_plugin':
				return 'Divi Builder';
				break;
			default:
				return false;
		}
	}

	/**
	 * Prevent any possible conflicts with the Elegant Themes Support plugin
	 *
	 * @since 3.20
	 */
	public function deactivate_conflicting_plugins() {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		// Load WP user management functions
		if ( is_multisite() ) {
			require_once( ABSPATH . 'wp-admin/includes/ms.php' );
		} else {
			require_once( ABSPATH . 'wp-admin/includes/user.php' );
		}

		// Verify that WP user management functions are available
		$can_delete_user = false;
		if ( is_multisite() && function_exists( 'wpmu_delete_user' ) ) {
			$can_delete_user = true;
		}
		if ( ! is_multisite() && function_exists( 'wp_delete_user' ) ) {
			$can_delete_user = true;
		}

		if ( $can_delete_user ) {
			deactivate_plugins( '/elegant-themes-support/elegant-themes-support.php' );
		} else {
			et_error( 'Support Center: Unable to deactivate the ET Support Plugin.' );
		}
	}

	/**
	 * @param string $capability
	 *
	 * @return bool
	 */
	protected function current_user_can( $capability = '' ) {
		if ( function_exists( 'et_is_builder_plugin_active' ) ) {
			return et_pb_is_allowed( $capability );
		}

		return current_user_can( $capability );
	}

	/**
	 * Add Safe Mode Autoloader Must-Use Plugin
	 *
	 * @since 3.20
	 */
	public function maybe_add_mu_autoloader() {
		$file_name = '/SupportCenterMUAutoloader.php';
		$file_path = dirname( __FILE__ );

		// Exit if the `mu-plugins` directory doesn't exist & we're unable to create it
		if ( ! wp_mkdir_p( WPMU_PLUGIN_DIR ) ) {
			et_error( 'Support Center Safe Mode: mu-plugin folder not found.' );

			return;
		}

		$pathname_to   = WPMU_PLUGIN_DIR . $file_name;
		$pathname_from = $file_path . $file_name;

		// Exit if we can't find the mu-plugins autoloader
		if ( ! file_exists( $pathname_from ) ) {
			et_error( 'Support Center Safe Mode: mu-plugin autoloader not found.' );

			return;
		}

		// Try to create a new subdirectory for our mu-plugins; if it fails, log an error message
		$pathname_plugins_from = dirname( __FILE__ ) . '/mu-plugins';
		$pathname_plugins_to   = WPMU_PLUGIN_DIR . '/et-safe-mode';
		if ( ! wp_mkdir_p( $pathname_plugins_to ) ) {
			et_error( 'Support Center Safe Mode: mu-plugins subfolder not found.' );

			return;
		}

		// Try to copy the mu-plugins; if any fail, log an error message
		if ( $mu_plugins = glob( dirname( __FILE__ ) . '/mu-plugins/*.php' ) ) {
			foreach ( $mu_plugins as $plugin ) {
				$new_file_path = str_replace( $pathname_plugins_from, $pathname_plugins_to, $plugin );

				// Skip if this particular mu-plugin hasn't changed
				if ( file_exists( $new_file_path ) && md5_file( $new_file_path ) === md5_file( $plugin ) ) {
					continue;
				}

				$copy_file = @copy( $plugin, $new_file_path );

				if ( ! $this->DEBUG_ET_SUPPORT_CENTER ) {
					continue;
				}

				if ( $copy_file ) {
					et_error( 'Support Center Safe Mode: mu-plugin [' . $plugin . '] installed.' );
				} else {
					et_error( 'Support Center Safe Mode: mu-plugin [' . $plugin . '] failed installation. ' );
				}
			}
		}

		// Finally, try to copy the autoloader file; if it fails, log an error message

		// Skip if the mu-plugins autoloader hasn't changed
		if ( file_exists( $pathname_to ) && md5_file( $pathname_to ) === md5_file( $pathname_from ) ) {
			return;
		}

		$copy_file = @copy( $pathname_from, $pathname_to );

		if ( $this->DEBUG_ET_SUPPORT_CENTER ) {
			if ( $copy_file ) {
				et_error( 'Support Center Safe Mode: mu-plugin installed.' );
			} else {
				et_error( 'Support Center Safe Mode: mu-plugin failed installation. ' );
			}
		}
	}

	public function maybe_remove_mu_autoloader() {
		@unlink( WPMU_PLUGIN_DIR . '/SupportCenterMUAutoloader.php' );
		@unlink( WPMU_PLUGIN_DIR . '/et-safe-mode/SupportCenterSafeModeDisablePlugins.php' );
		et_()->remove_empty_directories( WPMU_PLUGIN_DIR . '/et-safe-mode' );
	}

	/**
	 * Update the Site ID data via Elegant Themes API
	 *
	 * @since ?.?  Early exit if no Site ID, but also no API credentials to use for a request.
	 * @since 3.20
	 *
	 * @return void
	 */
	public function maybe_set_site_id() {
		// Early exit if the user doesn't have Support Center access.
		if ( ! $this->current_user_can( 'et_support_center' ) ) {
			return;
		}

		$site_id = get_option( 'et_support_site_id' );

		// If we already have a saved Site ID for support, then we don't need to request a new ID.
		if ( ! empty( $site_id ) ) {
			return;
		}

		// If there are no saved API credentials, then we can't use the API to request a Site ID for support.
		if ( ! $this->get_et_license() ) {
			return;
		}

		$site_id = '';

		$send_to_api = array(
			'action' => 'get_site_id',
		);

		$settings = array(
			'timeout' => 30,
			'body'    => $send_to_api,
		);

		$request = wp_remote_post( 'https://www.elegantthemes.com/api/token.php', $settings );

		if ( ! is_wp_error( $request ) && 200 == wp_remote_retrieve_response_code( $request ) ) {
			$response = unserialize( wp_remote_retrieve_body( $request ) );

			if ( ! empty( $response['site_id'] ) ) {
				$site_id = esc_attr( $response['site_id'] );
			}
		}

		update_option( 'et_support_site_id', $site_id );
	}

	/**
	 * Safe Mode temporarily deactivates all plugins *except* those in the allowlist option set here
	 *
	 * @since 3.20
	 *
	 * @return void
	 */
	public function set_safe_mode_plugins_allowlist() {
		update_option( 'et_safe_mode_plugins_allowlist', $this->safe_mode_plugins_allowlist );
	}

	/**
	 * Add Support Center menu item (but only if it's enabled for current user)
	 *
	 * When initialized we were given an identifier for the plugin or theme doing the initializing. We're going to use
	 * that identifier here to insert the Support Center menu item in the correct location within the WP Admin Menu.
	 *
	 * @since 3.28 Expanded sub-menu links with support for additional ET products.
	 * @since 3.20
	 */
	public function add_admin_menu_item() {
		// Early exit if the user doesn't have Support Center access
		if ( ! $this->current_user_can( 'et_support_center' ) ) {
			return;
		}

		$menu_title       = esc_html__( 'Support Center', 'et-core' );
		$menu_slug        = null;
		$parent_menu_slug = null;

		// By default, only user with `manage_options` capability which is "administrator"
		// can see Support Center menu and access the page.
		$capability = 'manage_options';

		// Define parent and child menu slugs
		switch ( $this->parent ) {
			case 'bloom_plugin':
				$menu_slug        = 'et_support_center_bloom';
				$parent_menu_slug = 'et_bloom_options';
				break;
			case 'monarch_plugin':
				$menu_title       = esc_html__( 'Monarch Support Center', 'et-core' );
				$menu_slug        = 'et_support_center_monarch';
				$parent_menu_slug = 'tools.php';
				break;
			case 'extra_theme':
				$menu_slug        = 'et_support_center_extra';
				$parent_menu_slug = 'et_extra_options';
				break;
			case 'divi_theme':
			case 'divi_builder_plugin':
				// In the Roles Editor, other user roles may have access to the Support Center.
				// But, most likely they don't have `manage_options` capability. So, if current
				// user can't `manage_options`, we will set the submenu capability into the
				// `edit_theme_options`, so other roles with `edit_theme_options` capability
				// can access the Support Center.
				if ( ! current_user_can( 'manage_options' ) ) {
					$capability = 'edit_theme_options';
				}

				// However, that's not enough. We still need to check whether current user with
				// `manage_options` or `edit_theme_options` is allowed to access Support Center.
				if ( ! et_pb_is_allowed( 'support_center' ) ) {
					return;
				}

				$menu_slug        = 'et_support_center_divi';
				$parent_menu_slug = 'et_divi_options';
		}

		// If there's no menu slug, then this product doesn't have Support Center enabled
		if ( ! $menu_slug ) {
			return;
		}

		// Build the link
		add_submenu_page(
			$parent_menu_slug,
			$menu_title,
			$menu_title,
			$capability,
			$menu_slug,
			array( $this, 'add_support_center' )
		);
	}

	/**
	 * Add class name to Support Center page
	 *
	 * @since 3.20
	 *
	 * @param string $admin_classes Current class names for the body tag.
	 *
	 * @return string
	 */
	public function add_admin_body_class_name( $admin_classes = '' ) {
		$classes   = explode( ' ', $admin_classes );
		$classes[] = 'et-admin-page';

		if ( et_core_is_safe_mode_active() ) {
			$classes[] = 'et-safe-mode-active';
		}

		return implode( ' ', $classes );
	}

	/**
	 * Support Center admin page JS
	 *
	 * @since 3.20
	 *
	 * @param $hook string Unique identifier for WP admin page.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts_styles( $hook ) {
		et_core_register_admin_assets();

		wp_enqueue_style( 'et-core-admin' );
		wp_enqueue_script( 'et-core-admin' );

		// Load only on `_et_support_center` pages.
		if ( strpos( $hook, '_et_support_center' ) ) {
			// Core Admin CSS
			wp_enqueue_style( 'et-core',
				$this->local_path . 'admin/css/core.css',
				array(),
				ET_CORE_VERSION
			);

			// ePanel CSS
			wp_enqueue_style( 'et-wp-admin',
				$this->local_path . 'admin/css/wp-admin.css',
				array(),
				ET_CORE_VERSION
			);

			// Support Center CSS
			wp_enqueue_style( 'et-support-center',
				$this->local_path . 'admin/css/support-center.css',
				array(),
				ET_CORE_VERSION
			);

			// Support Center uses ePanel controls, so include the necessary scripts
			if ( function_exists( 'et_core_enqueue_js_admin' ) ) {
				et_core_enqueue_js_admin();
			}
		}
	}

	/**
	 * Support Center frontend CSS/JS
	 *
	 * @since 3.20
	 *
	 * @param $hook string Unique identifier for WP admin page.
	 *
	 * @return void
	 */
	public function enqueue_scripts_styles( $hook ) {
		// We only need to add this for authenticated users on the frontend
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Support Center JS
		wp_enqueue_script( 'et-support-center',
			$this->local_path . 'admin/js/support-center.js',
			array( 'jquery', 'underscore' ),
			ET_CORE_VERSION,
			true
		);

		$support_center_nonce = wp_create_nonce( 'support_center' );

		$etSupportCenterSettings = array(
			'ajaxLoaderImg'    => esc_url( $this->local_path . 'admin/images/ajax-loader.gif' ),
			'ajaxURL'          => admin_url( 'admin-ajax.php' ),
			'siteURL'          => get_site_url(),
			'supportCenterURL' => get_admin_url( null, 'admin.php?page=et_support_center#et_card_safe_mode' ),
			'nonce'            => $support_center_nonce,
		);

		wp_localize_script( 'et-support-center', 'etSupportCenter', $etSupportCenterSettings );
	}

	/**
	 * Divi Support Center :: Card
	 *
	 * Take an array of attributes and build a WP Card block for display on the Divi Support Center page.
	 *
	 * @since 4.4.7 Added optional dismissible button
	 * @since 3.20
	 *
	 * @param array $attrs
	 *
	 * @return string
	 */
	protected function add_support_center_card( $attrs = array( 'title' => '', 'content' => '' ) ) {

		$card_classes = array(
			'card',
		);

		if ( array_key_exists( 'additional_classes', $attrs ) ) {
			$card_classes = array_merge( $card_classes, $attrs['additional_classes'] );
		}

		$dismiss_button = '';

		if ( array_key_exists( 'dismiss_button', $attrs ) ) {
			// Update card class to indicate the presence of the dismiss button
			$card_classes = array_merge( $card_classes, array( 'has-dismiss-button' ) );

			// Prepare Class for the Dismiss button
			$dismiss_button_classes = array( 'et-dismiss-button' );

			if ( array_key_exists( 'additional_classes', $attrs['dismiss_button'] ) ) {
				$dismiss_button_classes = array_merge( $dismiss_button_classes, $attrs['dismiss_button']['additional_classes'] );
			}

			// Whether to display tooltip for the dismiss button
			$dismiss_button_has_tooltip = array_key_exists( 'tooltip', $attrs['dismiss_button'] );

			// HTML Template for the dismiss button
			$dismiss_button = PHP_EOL . "\t" . sprintf(
					'<button class="%2$s" data-key="%3$s" data-product="%4$s" %5$s type="button" ><span class="et-dismiss-button-label">%1$s</span></button>',
					esc_html__( 'Dismiss', 'et-core' ),
					esc_attr( implode( ' ', $dismiss_button_classes ) ),
					esc_attr( $attrs['dismiss_button']['card_key'] ),
					esc_attr( $this->parent ),
					$dismiss_button_has_tooltip ? 'data-tippy-content="' . esc_attr( $attrs['dismiss_button']['tooltip'] ) . '"' : ''
				);
		}

		$card = PHP_EOL . '<div class="' . esc_attr( implode( ' ', $card_classes ) ) . '">' .
				PHP_EOL . "\t" . '<h2>' . esc_html( $attrs['title'] ) . '</h2>' .
				PHP_EOL . "\t" . '<div class="main">' . et_core_intentionally_unescaped( $attrs['content'], 'html' ) . '</div>' .
				et_core_esc_previously( $dismiss_button ) .
				PHP_EOL . '</div>';

		return $card;
	}

	/**
	 * Divi Support Center :: Dismiss a Card via Ajax
	 *
	 * @since 4.4.7
	 */
	public function dismiss_support_center_card_via_ajax() {

		et_core_security_check( 'manage_options', 'support_center', 'nonce' );

		$response = array();

		// Check the ET product that dismissing the card
		$et_product = sanitize_key( $_POST['product'] );

		// Confirm that this is a allowlisted product
		$allowlisted_product = $this->is_allowlisted_product( $et_product );

		if ( ! $allowlisted_product ) {
			// Send a failure code and exit the function
			header( "HTTP/1.0 403 Forbidden" );
			print 'Bad or malformed ET product name.';
			wp_die();
		}

		// Check the Card key against Cards that has a dismiss button
		$card_key = sanitize_key( $_POST['card_key'] );

		if ( ! in_array( $card_key, $this->card_with_dismiss_button, true ) ) {
			// Send a failure code and exit the function
			header( "HTTP/1.0 403 Forbidden" );
			print 'Card does not exists.';
			wp_die();
		}

		// Update option(s)
		update_option( "{$card_key}_dismissed", true );

		// For Divi Hosting Card, update the status via ET API
		if ( $card_key === 'et_hosting_card' ) {
			$settings    = $this->get_et_api_request_settings( 'disable_hosting_card' );
			$et_username = et_()->array_get( $settings, 'body.username', '' );
			$et_api_key  = et_()->array_get( $settings, 'body.api_key', '' );

			// Exit if ET Username and/or ET API Key is not found
			if ( $et_username === '' || $et_api_key === '' ) {
				return;
			}

			et_maybe_update_hosting_card_status();
		}

		$response['message'] = sprintf(
			esc_html__( 'Card (%1$s) has been dismissed successfully.', 'et-core' ),
			$card_key
		);

		// `echo` data to return
		if ( isset( $response ) ) {
			wp_send_json_success( $response );
		}

		// `die` when we're done
		wp_die();
	}

	/**
	 * Prepare the "Divi Documentation & Help" video player block
	 *
	 * @since 3.28 Added support for Bloom, Monarch, and Divi Builer plugins.
	 * @since 3.20
	 *
	 * @param bool $formatted Return either a formatted HTML block (true) or an array (false)
	 *
	 * @return array|string
	 */
	protected function get_documentation_video_player( $formatted = true ) {

		/**
		 * Define the videos list
		 */
		switch ( $this->parent ) {
			case 'extra_theme':
				$documentation_videos = array(
					array(
						'name'       => esc_attr__( 'A Basic Overview Of Extra', 'et-core' ),
						'youtube_id' => 'JDSg9eq4LIc',
					),
					array(
						'name'       => esc_attr__( 'Using Premade Layout Packs', 'et-core' ),
						'youtube_id' => '9eqXcrLcnoc',
					),
					array(
						'name'       => esc_attr__( 'Creating Category Layouts', 'et-core' ),
						'youtube_id' => '30SVxnjdnxcE',
					),
				);
				break;
			case 'divi_theme':
			case 'divi_builder_plugin':
				$documentation_videos = array(
					array(
						'name'       => esc_attr__( 'Getting Started With The Divi Builder', 'et-core' ),
						'youtube_id' => 'T-Oe01_J62c',
					),
					array(
						'name'       => esc_attr__( 'Using Premade Layout Packs', 'et-core' ),
						'youtube_id' => '9eqXcrLcnoc',
					),
					array(
						'name'       => esc_attr__( 'The Divi Library', 'et-core' ),
						'youtube_id' => 'boNZZ0MYU0E',
					),
				);
				break;
			case 'bloom_plugin':
				$documentation_videos = array(
					array(
						'name'       => esc_attr__( 'A Basic Overview Of The Bloom Plugin', 'et-core' ),
						'youtube_id' => 'E4nfXFjuRRI',
					),
					array(
						'name'       => esc_attr__( 'How To Update The Bloom Plugin', 'et-core' ),
						'youtube_id' => '-IIdkRLskuA',
					),
					array(
						'name'       => esc_attr__( 'How To Add Mailing List Accounts', 'et-core' ),
						'youtube_id' => 'nEdWkHIgQwY',
					),
				);
				break;
			case 'monarch_plugin':
				$documentation_videos = array(
					array(
						'name'       => esc_attr__( 'A Complete Overviw Of Monarch', 'et-core' ),
						'youtube_id' => 'RlMUEVkbMrs',
					),
					array(
						'name'       => esc_attr__( 'Adding Social Networks', 'et-core' ),
						'youtube_id' => 'ZabKCiKQJLM',
					),
					array(
						'name'       => esc_attr__( 'Configuring Social Follower APIs', 'et-core' ),
						'youtube_id' => 'vmE8uFhbzos',
					),
				);
				break;
			default:
				$documentation_videos = array();
		}

		// If we just want the array (not a formatted HTML block), return that now
		if ( false === $formatted ) {
			return $documentation_videos;
		}

		$videos_list_html = '';
		$playlist         = array();

		foreach ( $documentation_videos as $key => $video ) {
			$extra = '';
			if ( 0 === $key ) {
				$extra = ' class="active"';
			}
			$videos_list_html .= sprintf( '<li %1$s data-ytid="%2$s">%3$s%4$s</li>',
				$extra,
				esc_attr( $video['youtube_id'] ),
				'<span class="dashicons dashicons-arrow-right"></span>',
				et_core_intentionally_unescaped( $video['name'], 'fixed_string' )
			);
			$playlist[]       = et_core_intentionally_unescaped( $video['youtube_id'], 'fixed_string' );
		}

		$html = sprintf( '<div class="et_docs_videos">'
						 . '<div class="wrapper"><div id="et_documentation_player" data-playlist="%1$s"></div></div>'
						 . '<ul class="et_documentation_videos_list">%2$s</ul>'
						 . '</div>',
			esc_attr( implode( ',', $playlist ) ),
			$videos_list_html
		);

		return $html;
	}

	/**
	 * Prepare the "Divi Documentation & Help" articles list
	 *
	 * @since 3.28 Added support for Bloom, Monarch, and Divi Builer plugins.
	 * @since 3.20
	 *
	 * @param bool $formatted Return either a formatted HTML block (true) or an array (false)
	 *
	 * @return array|string
	 */
	protected function get_documentation_articles_list( $formatted = true ) {

		$articles_list_html = '';


		switch ( $this->parent ) {
			case 'extra_theme':
				$articles = array(
					array(
						'title' => esc_attr__( 'Getting Started With Extra', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/extra/overview-extra/',
					),
					array(
						'title' => esc_attr__( 'Setting Up The Extra Theme Options', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/extra/theme-options-extra/',
					),
					array(
						'title' => esc_attr__( 'The Extra Category Builder', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/extra/category-builder/',
					),
					array(
						'title' => esc_attr__( 'Getting Started With The Divi Builder', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/visual-builder/',
					),
					array(
						'title' => esc_attr__( 'How To Update The Extra Theme', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/update-divi/',
					),
					array(
						'title' => esc_attr__( 'An Overview Of All Divi Modules', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/modules/',
					),
					array(
						'title' => esc_attr__( 'Getting Started With Layout Packs', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/premade-layouts/',
					),
					array(
						'title' => esc_attr__( 'Customizing Your Header And Navigation', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/extra/theme-customizer/',
					),
				);
				break;
			case 'divi_theme':
				$articles = array(
					array(
						'title' => esc_attr__( 'Getting Started With The Divi Builder', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/visual-builder/',
					),
					array(
						'title' => esc_attr__( 'How To Update The Divi Theme', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/update-divi/',
					),
					array(
						'title' => esc_attr__( 'An Overview Of All Divi Modules', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/modules/',
					),
					array(
						'title' => esc_attr__( 'Using The Divi Library', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/divi-library/',
					),
					array(
						'title' => esc_attr__( 'Setting Up The Divi Theme Options', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/theme-options/',
					),
					array(
						'title' => esc_attr__( 'Getting Started With Layout Packs', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/premade-layouts/',
					),
					array(
						'title' => esc_attr__( 'Customizing Your Header And Navigation', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/customizer-header/',
					),
					array(
						'title' => esc_attr__( 'Divi For Developers', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/developers/',
					),
				);
				break;
			case 'divi_builder_plugin':
				$articles = array(
					array(
						'title' => esc_attr__( 'Getting Started With The Divi Builder', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/visual-builder/',
					),
					array(
						'title' => esc_attr__( 'How To Update The Divi Builder', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi-builder/update-divi-builder/',
					),
					array(
						'title' => esc_attr__( 'An Overview Of All Divi Modules', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/modules/',
					),
					array(
						'title' => esc_attr__( 'Using The Divi Library', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/divi-library/',
					),
					array(
						'title' => esc_attr__( 'Selling Products With Divi And WooCommerce', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/ecommerce-divi/',
					),
					array(
						'title' => esc_attr__( 'Getting Started With Layout Packs', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/premade-layouts/',
					),
					array(
						'title' => esc_attr__( 'Importing And Exporting Divi Layouts', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/divi/library-import/',
					),
					array(
						'title' => esc_attr__( 'Divi For Developers', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/developers/',
					),
				);
				break;
			case 'bloom_plugin':
				$articles = array(
					array(
						'title' => esc_attr__( 'A Basic Overview Of The Bloom Plugin', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/bloom/overview/',
					),
					array(
						'title' => esc_attr__( 'How To Update Your Bloom Plugin', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/bloom/update/',
					),
					array(
						'title' => esc_attr__( 'Adding Email Accounts In Bloom', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/bloom/accounts/',
					),
					array(
						'title' => esc_attr__( 'Customizing Your Opt-in Designs', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/bloom/design/',
					),
					array(
						'title' => esc_attr__( 'The Different Bloom Opt-in Types', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/bloom/optin-types/',
					),
					array(
						'title' => esc_attr__( 'Using The Bloom Display Settings', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/bloom/display/',
					),
					array(
						'title' => esc_attr__( 'How To Use Triggers In Bloom', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/bloom/triggers/',
					),
					array(
						'title' => esc_attr__( 'Adding Custom Fields To Bloom Opt-in Forms', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/bloom/optin/adding-custom-fields-to-bloom-optin-forms/',
					),
				);
				break;
			case 'monarch_plugin':
				$articles = array(
					array(
						'title' => esc_attr__( 'A Complete Overview Of The Monarch Plugin', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/monarch/overview-monarch/',
					),
					array(
						'title' => esc_attr__( 'How To Update Your Monarch WordPress Plugin', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/monarch/update-monarch/',
					),
					array(
						'title' => esc_attr__( 'Adding and Managing Social Networks', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/monarch/networks/',
					),
					array(
						'title' => esc_attr__( 'Configuring Social Network APIs', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/monarch/api/',
					),
					array(
						'title' => esc_attr__( 'Customizing The Monarch Design', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/monarch/design-monarch/',
					),
					array(
						'title' => esc_attr__( 'Viewing Your Social Stats', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/monarch/stats/',
					),
					array(
						'title' => esc_attr__( 'Using The Floating Sidebar', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/monarch/sidebar/',
					),
					array(
						'title' => esc_attr__( 'Using Popup & Flyin Triggers', 'et-core' ),
						'url'   => 'https://www.elegantthemes.com/documentation/monarch/triggers-monarch/',
					),
				);
				break;
			default:
				$articles = array();
		}

		// If we just want the array (not a formatted HTML block), return that now
		if ( false === $formatted ) {
			return $articles;
		}

		foreach ( $articles as $key => $article ) {
			$articles_list_html .= sprintf(
				'<li class="et-support-center-article"><a href="%1$s" target="_blank">%2$s</a></li>',
				esc_url( $article['url'] ),
				et_core_intentionally_unescaped( $article['title'], 'fixed_string' )
			);
		}

		$html = sprintf(
			'<div class="et_docs_articles"><ul class="et_documentation_articles_list">%1$s</ul></div>',
			$articles_list_html
		);

		return $html;
	}

	/**
	 * Look for Elegant Themes Support Account
	 *
	 * @since 3.20
	 *
	 * @return WP_User|false WP_User object on success, false on failure.
	 */
	public function get_et_support_user() {
		return get_user_by( 'slug', $this->support_user_account_name );
	}

	/**
	 * Look for saved Elegant Themes Username & API Key
	 *
	 * @since 3.20
	 *
	 * @return array|false license credentials on success, false on failure.
	 */
	public function get_et_license() {

		/** @var array License credentials [username|api_key] */
		if ( ! $et_license = get_site_option( 'et_automatic_updates_options' ) ) {
			$et_license = get_option( 'et_automatic_updates_options', array() );
		}

		if ( ! et_()->array_get( $et_license, 'username' ) ) {
			return false;
		}

		if ( ! et_()->array_get( $et_license, 'api_key' ) ) {
			return false;
		}

		return $et_license;
	}

	/**
	 * Try to load the WP debug log. If found, return the last [$lines_to_return] lines of the file and the filesize.
	 *
	 * @since 3.20
	 *
	 * @param int $lines_to_return Number of lines to read and return from the end of the wp_debug.log file.
	 *
	 * @return array
	 */
	protected function get_wp_debug_log( $lines_to_return = 10 ) {
		$log = array(
			'entries' => '',
			'size'    => 0,
		);

		// Early exit: internal PHP function `file_get_contents()` appears to be on lockdown
		if ( ! function_exists( 'file_get_contents' ) ) {
			$log['error'] = esc_attr__( 'Divi Support Center :: WordPress debug log cannot be read.', 'et-core' );

			if ( defined( 'ET_DEBUG' ) ) {
				et_error( $log['error'] );
			}

			return $log;
		}

		// Early exit: WP_DEBUG_LOG isn't defined in wp-config.php (or it's defined, but it's empty)
		if ( ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) {
			$log['error'] = esc_attr__( 'Divi Support Center :: WordPress debug.log is not configured.', 'et-core' );

			if ( defined( 'ET_DEBUG' ) ) {
				et_error( $log['error'] );
			}

			return $log;
		}

		/**
		 * WordPress 5.1 introduces the option to define a custom path for the WP_DEBUG_LOG file.
		 *
		 * @see wp_debug_mode()
		 *
		 * @since 3.20
		 */
		if ( in_array( strtolower( (string) WP_DEBUG_LOG ), array( 'true', '1' ), true ) ) {
			$wp_debug_log_path = realpath( WP_CONTENT_DIR . '/debug.log' );
		} else if ( is_string( WP_DEBUG_LOG ) ) {
			$wp_debug_log_path = realpath( WP_DEBUG_LOG );
		}

		// Early exit: `debug.log` doesn't exist or otherwise can't be read
		if ( ! isset( $wp_debug_log_path ) || ! file_exists( $wp_debug_log_path ) || ! is_readable( $wp_debug_log_path ) ) {
			$log['error'] = esc_attr__( 'Divi Support Center :: WordPress debug log cannot be found.', 'et-core' );

			if ( defined( 'ET_DEBUG' ) ) {
				et_error( $log['error'] );
			}

			return $log;
		}

		/**
		 * At this point, we know:
		 * (1) `$wp_debug_log_path` is set,
		 * (2) it points to a valid location, and
		 * (3) what it points to is readable.
		 *
		 * Before we continue, we'll ensure `$wp_debug_log_path` does not point to a directory.
		 */

		// Early exit: debug log definition points to a directory, not a file.
		if ( is_dir( $wp_debug_log_path ) ) {
			$log['error'] = esc_attr__(
				'Divi Support Center :: WordPress debug log setting points to a directory, but should point to a file.',
				'et-core'
			);

			if ( defined( 'ET_DEBUG' ) ) {
				et_error( $log['error'] );
			}

			return $log;
		}

		// Load the debug.log file
		$file = new SplFileObject( $wp_debug_log_path );

		// Get the filesize of debug.log
		$log['size'] = $this->get_size_in_shorthand( 0 + $file->getSize() );

		// If $lines_to_return is a positive integer, fetch the last [$lines_to_return] lines of the log file
		$lines_to_return = (int) $lines_to_return;
		if ( $lines_to_return > 0 ) {
			$file->seek( PHP_INT_MAX );
			$total_lines = $file->key();
			// If the file is smaller than the number of lines requested, return the entire file.
			$reader         = new LimitIterator( $file, max( 0, $total_lines - $lines_to_return ) );
			$log['entries'] = '';
			foreach ( $reader as $line ) {
				$log['entries'] .= $line;
			}
		}
		// Unload the SplFileObject
		$file = null;

		return $log;
	}

	/**
	 * When a predefined system setting is passed to this function, it will return the observed value.
	 *
	 * @since 3.20
	 *
	 * @param bool $formatted Whether to return a formatted report or just the data array
	 * @param string $format Return the report as either a `div` or `plain` text (if $formatted = true)
	 *
	 * @return array|string
	 */
	protected function system_diagnostics_generate_report( $formatted = true, $format = 'plain' ) {
		/** @var array Collection of system settings to run diagnostic checks on. */
		global $wp_version;

		global $shortname;

		$divi_builder_plugin_active = et_is_builder_plugin_active();

		if ( $divi_builder_plugin_active ) {
			$options = get_option( 'et_pb_builder_options', array() );
		}

		if ( 'divi' === $shortname ) {
			$performance_options_url = get_admin_url() . 'admin.php?page=et_divi_options#general-2';
			$builder_options_url     = get_admin_url() . 'admin.php?page=et_divi_options#builder-2';
		} elseif ( 'extra' === $shortname ) {
			$performance_options_url = get_admin_url() . 'admin.php?page=et_extra_options#general-2';
			$builder_options_url     = get_admin_url() . 'admin.php?page=et_extra_options#builder-2';
		} else {
			$performance_options_url = get_admin_url() . 'admin.php?page=et_divi_options#tab_et_dashboard_tab_content_performance_main';
			$builder_options_url     = get_admin_url() . 'admin.php?page=et_divi_options#tab_et_dashboard_tab_content_advanced_main';
		}

		$system_diagnostics_settings = array(
			array(
				'name'           => esc_attr__( 'Writable wp-content Directory', 'et-core' ),
				'environment'    => 'server',
				'type'           => 'truthy_falsy',
				'pass_minus_one' => null,
				'pass_zero'      => null,
				'minimum'        => null,
				'recommended'    => true,
				'actual'         => wp_is_writable( WP_CONTENT_DIR ),
				'help_text'      => et_core_intentionally_unescaped( __( 'We recommend that the wp-content directory on your server be writable by WordPress in order to ensure the full functionality of Divi Builder themes and plugins.', 'et-core' ), 'html' ),
				'learn_more'     => 'https://wordpress.org/support/article/changing-file-permissions/',
			),
			array(
				'name'           => esc_attr__( 'Writable et-cache Directory', 'et-core' ),
				'environment'    => 'server',
				'type'           => 'truthy_falsy',
				'pass_minus_one' => null,
				'pass_zero'      => null,
				'minimum'        => null,
				'recommended'    => true,
				'actual'         => wp_is_writable( WP_CONTENT_DIR . '/et-cache' ),
				'help_text'      => et_core_intentionally_unescaped( __( 'We recommend that the et-cache directory on your server be writable by WordPress in order to ensure the full functionality of Divi Builder themes and plugins.', 'et-core' ), 'html' ),
				'learn_more'     => 'https://wordpress.org/support/article/changing-file-permissions/',
			),
			array(
				'name'           => esc_attr__( 'PHP: Version', 'et-core' ),
				'environment'    => 'server',
				'type'           => 'version',
				'pass_minus_one' => false,
				'pass_zero'      => false,
				'minimum'        => null,
				'recommended'    => '7.4 or higher',
				'actual'         => (float) phpversion(),
				'help_text'      => et_core_intentionally_unescaped( __( 'We recommend using the latest stable version of PHP. This will not only ensure compatibility with Divi, but it will also greatly speed up your website leading to less memory and CPU related issues.', 'et-core' ), 'html' ),
				'learn_more'     => 'http://php.net/releases/',
			),
			array(
				'name'           => esc_attr__( 'WordPress Version', 'et-core' ),
				'environment'    => 'server',
				'type'           => 'version',
				'pass_minus_one' => false,
				'pass_zero'      => false,
				'minimum'        => null,
				'recommended'    => '5.3 or higher',
				'actual'         => $wp_version,
				'help_text'      => et_core_intentionally_unescaped( __( 'We recommend using the latest stable version of WordPress. This will not only ensure compatibility with Divi, but it will also greatly speed up your website leading to less memory and CPU related issues.', 'et-core' ), 'html' ),
				'learn_more'     => 'https://wordpress.org/download/releases/',
			),
			array(
				'name'           => esc_attr__( 'PHP: memory_limit', 'et-core' ),
				'environment'    => 'server',
				'type'           => 'size',
				'pass_minus_one' => true,
				'pass_zero'      => false,
				'minimum'        => null,
				'recommended'    => '128M',
				'actual'         => ini_get( 'memory_limit' ),
				'help_text'      => et_get_safe_localization( sprintf( __( 'By default, memory limits set by your host or by WordPress may be too low. This will lead to applications crashing as PHP reaches the artificial limit. You can adjust your memory limit within your <a href="%1$s" target="_blank">php.ini file</a>, or by contacting your host for assistance. You may also need to define a memory limited in <a href="%2$s" target=_blank">wp-config.php</a>.', 'et-core' ), 'http://php.net/manual/en/ini.core.php#ini.memory-limit', 'https://codex.wordpress.org/Editing_wp-config.php' ) ),
				'learn_more'     => 'http://php.net/manual/en/ini.core.php#ini.memory-limit',
			),
			array(
				'name'           => esc_attr__( 'PHP: post_max_size', 'et-core' ),
				'environment'    => 'server',
				'type'           => 'size',
				'pass_minus_one' => false,
				'pass_zero'      => true,
				'minimum'        => null,
				'recommended'    => '64M',
				'actual'         => ini_get( 'post_max_size' ),
				'help_text'      => et_get_safe_localization( sprintf( __( 'Post Max Size limits how large a page or file can be on your website. If your page is larger than the limit set in PHP, it will fail to load. Post sizes can become quite large when using the Divi Builder, so it is important to increase this limit. It also affects file size upload/download, which can prevent large layouts from being imported into the builder. You can adjust your max post size within your <a href="%1$s" target="_blank">php.ini file</a>, or by contacting your host for assistance.', 'et_core' ), 'http://php.net/manual/en/ini.core.php#ini.post-max-size' ) ),
				'learn_more'     => 'http://php.net/manual/en/ini.core.php#ini.post-max-size',
			),
			array(
				'name'           => esc_attr__( 'PHP: max_execution_time', 'et-core' ),
				'environment'    => 'server',
				'type'           => 'seconds',
				'pass_minus_one' => false,
				'pass_zero'      => true,
				'minimum'        => null,
				'recommended'    => '120',
				'actual'         => ini_get( 'max_execution_time' ),
				'help_text'      => et_get_safe_localization( sprintf( __( 'Max Execution Time affects how long a page is allowed to load before it times out. If the limit is too low, you may not be able to import large layouts and files into the builder. You can adjust your max execution time within your <a href="%1$s">php.ini file</a>, or by contacting your host for assistance.', 'et-core' ), 'http://php.net/manual/en/info.configuration.php#ini.max-execution-time' ) ),
				'learn_more'     => 'http://php.net/manual/en/info.configuration.php#ini.max-execution-time',
			),
			array(
				'name'           => esc_attr__( 'PHP: upload_max_filesize', 'et-core' ),
				'environment'    => 'server',
				'type'           => 'size',
				'pass_minus_one' => false,
				'pass_zero'      => true,
				'minimum'        => null,
				'recommended'    => '64M',
				'actual'         => ini_get( 'upload_max_filesize' ),
				'help_text'      => et_get_safe_localization( sprintf( __( 'Upload Max File Size determines that maximum file size that you are allowed to upload to your server. If the limit is too low, you may not be able to import large collections of layouts into the Divi Library. You can adjust your max file size within your <a href="%1$s" target="_blank">php.ini file</a>, or by contacting your host for assistance.', 'et-core' ), 'http://php.net/manual/en/ini.core.php#ini.upload-max-filesize' ) ),
				'learn_more'     => 'http://php.net/manual/en/ini.core.php#ini.upload-max-filesize',
			),
			array(
				'name'           => esc_attr__( 'PHP: max_input_time', 'et-core' ),
				'environment'    => 'server',
				'type'           => 'seconds',
				'pass_minus_one' => true,
				'pass_zero'      => true,
				'minimum'        => null,
				'recommended'    => '60',
				'actual'         => ini_get( 'max_input_time' ),
				'help_text'      => et_get_safe_localization( sprintf( __( 'This sets the maximum time in seconds a script is allowed to parse input data. If the limit is too low, the Divi Builder may time out before it is allowed to load. You can adjust your max input time within your <a href="%1$s" target="_blank">php.ini file</a>, or by contacting your host for assistance.', 'et-core' ), 'http://php.net/manual/en/info.configuration.php#ini.max-input-time' ) ),
				'learn_more'     => 'http://php.net/manual/en/info.configuration.php#ini.max-input-time',
			),
			array(
				'name'           => esc_attr__( 'PHP: max_input_vars', 'et-core' ),
				'environment'    => 'server',
				'type'           => 'size',
				'pass_minus_one' => false,
				'pass_zero'      => false,
				'minimum'        => null,
				'recommended'    => '1000',
				'actual'         => ini_get( 'max_input_vars' ),
				'help_text'      => et_get_safe_localization( sprintf( __( 'This setting affects how many input variables may be accepted. If the limit is too low, it may prevent the Divi Builder from loading. You can adjust your max input variables within your <a href="%1$s" target="_blank">php.ini file</a>, or by contacting your host for assistance.', 'et-core' ), 'http://php.net/manual/en/info.configuration.php#ini.max-input-vars' ) ),
				'learn_more'     => 'http://php.net/manual/en/info.configuration.php#ini.max-input-vars',
			),
			array(
				'name'           => esc_attr__( 'PHP: display_errors', 'et-core' ),
				'environment'    => 'server',
				'type'           => 'truthy_falsy',
				'pass_minus_one' => null,
				'pass_zero'      => null,
				'pass_exact'     => null,
				'minimum'        => null,
				'recommended'    => '0',
				'actual'         => ! ini_get( 'display_errors' ) ? '0' : ini_get( 'display_errors' ),
				'help_text'      => et_get_safe_localization( sprintf( __( 'This setting determines whether or not errors should be printed as part of the page output. This is a feature to support your site\'s development and should never be used on production sites. You can edit this setting within your <a href="%1$s" target="_blank">php.ini file</a>, or by contacting your host for assistance.', 'et-core' ), 'http://php.net/manual/en/errorfunc.configuration.php#ini.display-errors' ) ),
				'learn_more'     => 'http://php.net/manual/en/errorfunc.configuration.php#ini.display-errors',
			),
			array(
				'name'           => esc_attr__( 'Dynamic CSS', 'et-core' ),
				'environment'    => 'performance',
				'type'           => 'truthy_falsy',
				'pass_minus_one' => null,
				'pass_zero'      => null,
				'pass_exact'     => null,
				'minimum'        => null,
				'recommended'    => 'on',
				'actual'         => $divi_builder_plugin_active ? ( isset( $options['performance_main_dynamic_css'] ) ? $options['performance_main_dynamic_css'] : 'on' ) : et_get_option( $shortname . '_dynamic_css', 'on' ),
				'help_text'      => et_get_safe_localization( sprintf( __( 'This is a very important performance setting that should be turned on. Dynamic CSS greatly reduces your website\'s CSS size, speeds up page load times and improves Google PageSpeed scores. You can turn this setting on in the <a href="%1$s" target="_blank">Theme Options</a>.', 'et-core' ), $performance_options_url ) ),
				'learn_more'     => $performance_options_url,
			),
			array(
				'name'           => esc_attr__( 'Dynamic Framework', 'et-core' ),
				'environment'    => 'performance',
				'type'           => 'truthy_falsy',
				'pass_minus_one' => null,
				'pass_zero'      => null,
				'pass_exact'     => null,
				'minimum'        => null,
				'recommended'    => 'on',
				'actual'         => $divi_builder_plugin_active ? ( isset( $options['performance_main_dynamic_module_framework'] ) ? $options['performance_main_dynamic_module_framework'] : 'on' ) : et_get_option( $shortname . '_dynamic_module_framework', 'on' ),
				'help_text'      => et_get_safe_localization( sprintf( __( 'This is a very important performance setting that should be turned on. The Dynamic Framework removes bloat from the back-end. This greatly reduces CPU and Memory usage and improves website speed. You can turn this setting on in the <a href="%1$s" target="_blank">Theme Options</a>.', 'et-core' ), $performance_options_url ) ),
				'learn_more'     => $performance_options_url,
			),
			array(
				'name'           => esc_attr__( 'Dynamic JavaScript', 'et-core' ),
				'environment'    => 'performance',
				'type'           => 'truthy_falsy',
				'pass_minus_one' => null,
				'pass_zero'      => null,
				'pass_exact'     => null,
				'minimum'        => null,
				'recommended'    => 'on',
				'actual'         => $divi_builder_plugin_active ? ( isset( $options['performance_main_dynamic_css'] ) ? $options['performance_main_dynamic_css'] : 'on' ) : et_get_option( $shortname . '_dynamic_js_libraries', 'on' ),
				'help_text'      => et_get_safe_localization( sprintf( __( 'This is a very important performance setting that should be turned on. Dynamic JavaScript removes unused scripts and improves website speed by loading JavaScript files only when they are needed. You can turn this setting on in the <a href="%1$s" target="_blank">Theme Options</a>.', 'et-core' ), $performance_options_url ) ),
				'learn_more'     => $performance_options_url,
			),
			array(
				'name'           => esc_attr__( 'Critical CSS', 'et-core' ),
				'environment'    => 'performance',
				'type'           => 'truthy_falsy',
				'pass_minus_one' => null,
				'pass_zero'      => null,
				'pass_exact'     => null,
				'minimum'        => null,
				'recommended'    => 'on',
				'actual'         => $divi_builder_plugin_active ? ( isset( $options['performance_main_critical_css'] ) ? $options['performance_main_dynamic_css'] : 'on' ) : et_get_option( $shortname . '_critical_css', 'on' ),
				'help_text'      => et_get_safe_localization( sprintf( __( 'This is a very important performance setting that should be turned on. Critical CSS greatly improves website loading speeds by deferring "below the fold" styles and removing render blocking requests for critical styles. You can turn this setting on in the <a href="%1$s" target="_blank">Theme Options</a>.', 'et-core' ), $performance_options_url ) ),
				'learn_more'     => $performance_options_url,
			),
			array(
				'name'           => esc_attr__( 'Static CSS', 'et-core' ),
				'environment'    => 'performance',
				'type'           => 'truthy_falsy',
				'pass_minus_one' => null,
				'pass_zero'      => null,
				'pass_exact'     => null,
				'minimum'        => null,
				'recommended'    => 'on',
				'actual'         => et_get_option( 'et_pb_static_css_file', 'on' ),
				'help_text'      => et_get_safe_localization( sprintf( __( 'This is a very important performance setting that should be turned on, even if you are using a caching plugin. Static CSS caches the builder CSS for each page so that it doesn\'t need to be processed on every page load. Even if you are using a caching plugin, this setting should still be turned on so that dynamic pages benefit. You can turn this setting on in the <a href="%1$s" target="_blank">Theme Options</a>.', 'et-core' ), $builder_options_url ) ),
				'learn_more'     => $builder_options_url,
			),
		);

		/** @var string Formatted report. */
		$report = '';

		// pass/fail Should be one of pass|minimal|fail|unknown. Defaults to 'unknown'.
		foreach ( $system_diagnostics_settings as $i => $scan ) {
			/**
			 * 'pass_fail': four-step process to set its value:
			 * - begin with `unknown` state;
			 * - if recommended value exists, change to `fail`;
			 * - if minimum value exists, compare against it & change to `minimal` if it passes;
			 * - compare against recommended value & change to `pass` if it passes.
			 */
			$system_diagnostics_settings[ $i ]['pass_fail'] = 'unknown';
			if ( ! is_null( $scan['recommended'] ) ) {
				$system_diagnostics_settings[ $i ]['pass_fail'] = 'fail';
			}

			if ( ! is_null( $scan['minimum'] ) && $this->value_is_at_least( $scan['minimum'], $scan['actual'], $scan['type'] ) ) {
				$system_diagnostics_settings[ $i ]['pass_fail'] = 'minimal';
			}

			if ( empty( $scan['pass_exact'] ) && ! is_null( $scan['recommended'] ) && $this->value_is_at_least( $scan['recommended'], $scan['actual'], $scan['type'] ) ) {
				$system_diagnostics_settings[ $i ]['pass_fail'] = 'pass';
			}

			if ( $scan['pass_minus_one'] && -1 === (int) $scan['actual'] ) {
				$system_diagnostics_settings[ $i ]['pass_fail'] = 'pass';
			}

			if ( $scan['pass_zero'] && 0 === (int) $scan['actual'] ) {
				$system_diagnostics_settings[ $i ]['pass_fail'] = 'pass';
			}

			if ( ! empty( $scan['pass_exact'] ) && $scan['recommended'] === $scan['actual'] ) {
				$system_diagnostics_settings[ $i ]['pass_fail'] = 'pass';
			}

			/**
			 * Build messaging for minimum required values
			 */
			$message_minimum = '';
			if ( ! is_null( $scan['minimum'] ) && 'fail' === $system_diagnostics_settings[ $i ]['pass_fail'] ) {
				$message_minimum = sprintf(
					esc_html__( 'This fails to meet our minimum required value of %1$s. ', 'et-core' ),
					esc_html( is_bool( $scan['minimum'] ) ? $this->boolean_label[ $scan['minimum'] ] : $scan['minimum'] )
				);
			}
			if ( ! is_null( $scan['minimum'] ) && 'minimal' === $system_diagnostics_settings[ $i ]['pass_fail'] ) {
				$message_minimum = sprintf(
					esc_html__( 'This meets our minimum required value of %1$s. ', 'et-core' ),
					esc_html( is_bool( $scan['minimum'] ) ? $this->boolean_label[ $scan['minimum'] ] : $scan['minimum'] )
				);
			}

			/**
			 * Build description messaging for results & recommendation
			 */
			$learn_more_link = '';
			if ( ! is_null( $scan['learn_more'] ) ) {
				$learn_more_link = sprintf( ' <a href="%1$s" target="_blank">%2$s</a>',
					esc_url( $scan['learn_more'] ),
					esc_html__( 'server' === $scan['environment'] ? 'Learn More.' : 'Enable Option.', 'et-core' )
				);
			}

			switch ( $system_diagnostics_settings[ $i ]['pass_fail'] ) {
				case 'pass':
					$system_diagnostics_settings[ $i ]['description'] = sprintf(
						'- %1$s %2$s',
						sprintf(
							esc_html__( 'performance' === $scan['environment'] ? 'Perfect! We recommend enabling this option.' : 'Congratulations! This meets or exceeds our recommendation of %1$s.', 'et-core' ),
							esc_html( is_bool( $scan['recommended'] ) ? $this->boolean_label[ $scan['recommended'] ] : $scan['recommended'] )
						),
						et_core_intentionally_unescaped( $learn_more_link, 'html' )
					);
					break;
				case 'minimal':
				case 'fail':
					$system_diagnostics_settings[ $i ]['description'] = sprintf(
						'- %1$s%2$s %3$s',
						esc_html( $message_minimum ),
						sprintf(
							esc_html__( 'performance' === $scan['environment'] ? 'Enable for optimal performance.' : 'We recommend %1$s for the best experience.', 'et-core' ),
							esc_html( is_bool( $scan['recommended'] ) ? $this->boolean_label[ $scan['recommended'] ] : $scan['recommended'] )
						),
						et_core_intentionally_unescaped( $learn_more_link, 'html' )
					);
					break;
				case 'unknown':
				default:
					$system_diagnostics_settings[ $i ]['description'] = sprintf(
						esc_html__( '- We are unable to determine your setting. %1$s', 'et-core' ),
						et_core_intentionally_unescaped( $learn_more_link, 'html' )
					);
			}
		}

		// If we just want the array (not a formatted HTML block), return that now
		if ( false === $formatted ) {
			return $system_diagnostics_settings;
		}

		foreach ( $system_diagnostics_settings as $item ) {
			// Add reported setting to plaintext report:
			if ( 'plain' === $format ) {
				switch ( $item['pass_fail'] ) {
					case 'pass':
						$status = '  ';
						break;
					case 'minimal':
						$status = '~ ';
						break;
					case 'fail':
						$status = "! ";
						break;
					case 'unknown':
					default:
						$status = '? ';
				}

				$report .= $status . $item['name'] . PHP_EOL
						   . '  ' . $item['actual'] . PHP_EOL . PHP_EOL;
			}

			// Add reported setting to table:
			if ( 'div' === $format ) {
				$help_text = '';
				if ( ! is_null( $item['help_text'] ) ) {
					$help_text = $item['help_text'];
				}

				$report .= sprintf( '<div class="et-epanel-box et_system_status_row et_system_status_%1$s">
				<div class="et-box-title setting">
				    <h3>%2$s</h3>
				    <div class="et-box-descr"><p>%3$s</p></div>
				</div>
				<div class="et-box-content results">
				    <span class="actual">%4$s</span>
				    <span class="description">%5$s</span>
				</div>
				<span class="et-box-description"></span>
				</div>',
					esc_attr( $item['pass_fail'] ),
					esc_html( $item['name'] ),
					et_core_intentionally_unescaped( $help_text, 'html' ),
					esc_html( is_bool( $item['actual'] ) ? $this->boolean_label[ $item['actual'] ] : $item['actual'] ),
					et_core_intentionally_unescaped( $item['description'], 'html' )
				);
			}

		}

		// Prepend title and timestamp
		if ( 'plain' === $format ) {
			$report = '## ' . esc_html__( 'System Status', 'et-core' ) . ' ##' . PHP_EOL
					  . ':: ' . date( 'Y-m-d @ H:i:s e' ) . PHP_EOL . PHP_EOL
					  . $report;
		}
		if ( 'div' === $format ) {
			$report = sprintf( '<div class="%3$s-report">%1$s</div><p class="%3$s-congratulations">%2$s</p>',
				$report,
				esc_html__( 'Congratulations, all system checks have passed. Your hosting configuration is compatible with Divi.', 'et-core' ),
				'et-system-status'
			);
		}

		return $report;
	}

	/**
	 * Convert size string with "shorthand byte" notation to raw byte value for comparisons.
	 *
	 * @since 3.20
	 *
	 * @param string $size
	 *
	 * @return int size in bytes
	 */
	protected function get_size_in_bytes( $size = '' ) {
		// Capture the denomination and convert to uppercase, then do math to it
		switch ( strtoupper( substr( $size, -1 ) ) ) {
			// Terabytes
			case 'T':
				return (int) $size * 1099511627776;
			// Gigabytes
			case 'G':
				return (int) $size * 1073741824;
			// Megabytes
			case 'M':
				return (int) $size * 1048576;
			// Kilobytes
			case 'K':
				return (int) $size * 1024;
			default:
				return (int) $size;
		}
	}

	/**
	 * Convert size string with "shorthand byte" notation to raw byte value for comparisons.
	 *
	 * @since 3.20
	 *
	 * @param int $bytes
	 * @param int $precision
	 *
	 * @return string size in "shorthand byte" notation
	 */
	protected function get_size_in_shorthand( $bytes = 0, $precision = 2 ) {
		$units = array( ' bytes', 'KB', 'MB', 'GB', 'TB' );
		$i     = 0;

		while ( $bytes > 1024 ) {
			$bytes /= 1024;
			$i++;
		}

		return round( $bytes, $precision ) . $units[ $i ];
	}

	/**
	 * Size comparisons between two values using a variety of calculation methods.
	 *
	 * @since 3.20.2
	 *
	 * @param string|int|float $a Our value to compare against
	 * @param string|int|float $b Server value being compared
	 * @param string $type Comparison type
	 *
	 * @return bool Whether the second value is equal to or greater than the first
	 */
	protected function value_is_at_least( $a, $b, $type = 'size' ) {
		switch ( $type ) {
			case 'truthy_falsy':
				return $this->value_is_falsy( $a ) === $this->value_is_falsy( $b );
			case 'version':
				return (float) $a <= (float) $b;
			case 'seconds':
				return (int) $a <= (int) $b;
			case 'size':
			default:
				return $this->get_size_in_bytes( $a ) <= $this->get_size_in_bytes( $b );
		}
	}

	/**
	 * Check value against a collection of "falsy" values
	 *
	 * @since 3.23
	 *
	 * @param string|int|float $a Value to compare against
	 *
	 * @return bool Whether the second value is equal to or greater than the first
	 */
	protected function value_is_falsy( $a ) {
		// Accept falsy strings regardless of case (e.g. 'off', 'Off', 'OFF', 'oFf')
		if ( is_string( $a ) ) {
			$a = strtolower( $a );
		}

		return in_array( $a, array( false, 'false', 0, '0', 'off' ), true );
	}

	/**
	 * SUPPORT CENTER :: REMOTE ACCESS
	 */

	/**
	 * Add Support Center options to the Role Editor screen
	 *
	 * @see ET_Core_SupportCenter::current_user_can()
	 *
	 * @since 3.20
	 *
	 * @param $all_role_options
	 *
	 * @return array
	 */
	public function support_user_add_role_options( $all_role_options ) {
		// get all the roles that can edit theme options.
		$applicability_roles = et_core_get_roles_by_capabilities( [ 'edit_theme_options' ] );

		$all_role_options['support_center'] = array(
			'section_title' => esc_attr__( 'Support Center', 'et-core' ),
			'applicability' => $applicability_roles,
			'options'       => array(
				'et_support_center'               => array(
					'name' => esc_attr__( 'Divi Support Center Page', 'et-core' ),
				),
				'et_support_center_system'        => array(
					'name' => esc_attr__( 'System Status', 'et-core' ),
				),
				'et_support_center_remote_access' => array(
					'name' => esc_attr__( 'Remote Access', 'et-core' ),
				),
				'et_support_center_documentation' => array(
					'name' => esc_attr__( 'Divi Documentation &amp; Help', 'et-core' ),
				),
				'et_support_center_safe_mode'     => array(
					'name' => esc_attr__( 'Safe Mode', 'et-core' ),
				),
				'et_support_center_logs'          => array(
					'name' => esc_attr__( 'Logs', 'et-core' ),
				),
			),
		);

		return $all_role_options;
	}

	/**
	 * Add third party capabilities to Remote Access roles
	 *
	 * @return array Capabilities to add to the Remote Access user roles.
	 */
	public function support_user_extra_caps_standard( $extra_capabilities = array() ) {
		// The Events Calendar (if active on the site)
		if ( class_exists( 'Tribe__Events__Main' ) ) {
			$the_events_calendar = array(
				// Events
				'edit_tribe_event'                  => 1,
				'read_tribe_event'                  => 1,
				'delete_tribe_event'                => 1,
				'delete_tribe_events'               => 1,
				'edit_tribe_events'                 => 1,
				'edit_others_tribe_events'          => 1,
				'delete_others_tribe_events'        => 1,
				'publish_tribe_events'              => 1,
				'edit_published_tribe_events'       => 1,
				'delete_published_tribe_events'     => 1,
				'delete_private_tribe_events'       => 1,
				'edit_private_tribe_events'         => 1,
				'read_private_tribe_events'         => 1,
				// Venues
				'edit_tribe_venue'                  => 1,
				'read_tribe_venue'                  => 1,
				'delete_tribe_venue'                => 1,
				'delete_tribe_venues'               => 1,
				'edit_tribe_venues'                 => 1,
				'edit_others_tribe_venues'          => 1,
				'delete_others_tribe_venues'        => 1,
				'publish_tribe_venues'              => 1,
				'edit_published_tribe_venues'       => 1,
				'delete_published_tribe_venues'     => 1,
				'delete_private_tribe_venues'       => 1,
				'edit_private_tribe_venues'         => 1,
				'read_private_tribe_venues'         => 1,
				// Organizers
				'edit_tribe_organizer'              => 1,
				'read_tribe_organizer'              => 1,
				'delete_tribe_organizer'            => 1,
				'delete_tribe_organizers'           => 1,
				'edit_tribe_organizers'             => 1,
				'edit_others_tribe_organizers'      => 1,
				'delete_others_tribe_organizers'    => 1,
				'publish_tribe_organizers'          => 1,
				'edit_published_tribe_organizers'   => 1,
				'delete_published_tribe_organizers' => 1,
				'delete_private_tribe_organizers'   => 1,
				'edit_private_tribe_organizers'     => 1,
				'read_private_tribe_organizers'     => 1,
			);

			$extra_capabilities = array_merge( $extra_capabilities, $the_events_calendar );
		}

		return $extra_capabilities;
	}

	/**
	 * Add third party capabilities to the *Elevated* Remote Access role only
	 *
	 * @return array Capabilities to add to the Elevated Remote Access user role.
	 */
	public function support_user_extra_caps_elevated() {
		$extra_capabilities = array();

		return $extra_capabilities;
	}

	/**
	 * Create the Divi Support user (if it doesn't already exist)
	 *
	 * @since 3.20
	 *
	 * @return void|WP_Error
	 */
	public function support_user_maybe_create_user() {
		if ( username_exists( $this->support_user_account_name ) ) {
			return;
		}

		// Define user roles that will be used to control ET Support User permissions
		$this->support_user_create_roles();

		$token = $this->support_user_generate_token();

		$password = $this->support_user_generate_password( $token );

		if ( is_wp_error( $password ) ) {
			return $password;
		}

		$user_id = wp_insert_user( array(
			'user_login'   => $this->support_user_account_name,
			'user_pass'    => $password,
			'first_name'   => 'Elegant Themes',
			'last_name'    => 'Support',
			'display_name' => 'Elegant Themes Support',
			'role'         => 'et_support',
		) );

		if ( is_wp_error( $user_id ) ) {
			return $user_id;
		}

		$account_settings = array(
			'date_created' => time(),
			'token'        => $token,
		);

		update_option( $this->support_user_options_name, $account_settings );

		// update options variable
		$this->support_user_get_options();

		$this->support_user_init_cron_delete_account();
	}

	/**
	 * Define both Standard and Elevated roles for the Divi Support user
	 *
	 * @since 3.22 Added filters to extend the list of capabilities for the ET Support User
	 * @since 3.20
	 */
	public function support_user_create_roles() {
		// Make sure old versions of these roles do not exist
		$this->support_user_remove_roles();

		// Divi Support :: Standard
		$standard_capabilities = array(
			'assign_product_terms'               => true,
			'delete_pages'                       => true,
			'delete_posts'                       => true,
			'delete_private_pages'               => true,
			'delete_private_posts'               => true,
			'delete_private_products'            => true,
			'delete_product'                     => true,
			'delete_product_terms'               => true,
			'delete_products'                    => true,
			'delete_published_pages'             => true,
			'delete_published_posts'             => true,
			'delete_published_products'          => true,
			'edit_dashboard'                     => true,
			'edit_files'                         => true,
			'edit_others_pages'                  => true,
			'edit_others_posts'                  => true,
			'edit_others_products'               => true,
			'edit_pages'                         => true,
			'edit_posts'                         => true,
			'edit_private_pages'                 => true,
			'edit_private_posts'                 => true,
			'edit_private_products'              => true,
			'edit_product'                       => true,
			'edit_product_terms'                 => true,
			'edit_products'                      => true,
			'edit_published_pages'               => true,
			'edit_published_posts'               => true,
			'edit_published_products'            => true,
			'edit_theme_options'                 => true,
			'list_users'                         => true,
			'manage_categories'                  => true,
			'manage_links'                       => true,
			'manage_options'                     => true,
			'manage_product_terms'               => true,
			'moderate_comments'                  => true,
			'publish_pages'                      => true,
			'publish_posts'                      => true,
			'publish_products'                   => true,
			'read'                               => true,
			'read_private_pages'                 => true,
			'read_private_posts'                 => true,
			'read_private_products'              => true,
			'read_product'                       => true,
			'unfiltered_html'                    => true,
			'upload_files'                       => true,
			// Divi
			'ab_testing'                         => true,
			'add_library'                        => true,
			'disable_module'                     => true,
			'divi_builder_control'               => true,
			'divi_ai'                            => true,
			'divi_library'                       => true,
			'edit_borders'                       => true,
			'edit_buttons'                       => true,
			'edit_colors'                        => true,
			'edit_configuration'                 => true,
			'edit_content'                       => true,
			'edit_global_library'                => true,
			'edit_layout'                        => true,
			'export'                             => true,
			'lock_module'                        => true,
			'page_options'                       => true,
			'portability'                        => true,
			'read_dynamic_content_custom_fields' => true,
			'save_library'                       => true,
			'use_visual_builder'                 => true,
			'theme_builder'                      => true,
			// WooCommerce Capabilities
			'manage_woocommerce'                 => true,
		);

		// Divi Support :: Elevated
		$elevated_capabilities = array_merge( $standard_capabilities, array(
			'activate_plugins' => true,
			'delete_plugins'   => true,
			'delete_themes'    => true,
			'edit_plugins'     => true,
			'edit_themes'      => true,
			'install_plugins'  => true,
			'install_themes'   => true,
			'switch_themes'    => true,
			'update_plugins'   => true,
			'update_themes'    => true,
		) );

		// Filters to allow other code to extend the list of capabilities
		$additional_standard = apply_filters( 'add_et_support_standard_capabilities', array() );
		$additional_elevated = apply_filters( 'add_et_support_elevated_capabilities', array() );

		// Apply filter capabilities to our definitions
		$standard_capabilities = array_merge( $additional_standard, $standard_capabilities );
		// Just like Elevated gets all of Standard's capabilities, it also inherits Standard's filter caps
		$elevated_capabilities = array_merge( $additional_standard, $additional_elevated, $elevated_capabilities );

		// Create the standard ET Support role
		add_role( 'et_support', 'ET Support', $standard_capabilities );
		$et_support_role = get_role( 'et_support' );
		foreach ( $standard_capabilities as $cap ) {
			$et_support_role->add_cap( $cap );
		}
		// Create the elevated ET Support role
		add_role( 'et_support_elevated', 'ET Support - Elevated', $elevated_capabilities );
		$et_support_elevated_role = get_role( 'et_support_elevated' );
		foreach ( $elevated_capabilities as $cap ) {
			$et_support_elevated_role->add_cap( $cap );
		}
	}

	/**
	 * Remove our Standard and Elevated Support roles
	 *
	 * @since 3.20
	 */
	public function support_user_remove_roles() {
		// Divi Support :: Standard
		remove_role( 'et_support' );

		// Divi Support :: Elevated
		remove_role( 'et_support_elevated' );
	}

	/**
	 * Set the ET Support User's role
	 *
	 * @since 3.20
	 *
	 * @param string $role
	 */
	public function support_user_set_role( $role = '' ) {
		// Get the Divi Support User object
		$support_user = new WP_User( $this->support_user_account_name );

		// Set the new Role
		switch ( $role ) {
			case 'et_support':
				$support_user->set_role( 'et_support' );
				break;
			case 'et_support_elevated':
				$support_user->set_role( 'et_support_elevated' );
				break;
			case '':
			default:
				$support_user->set_role( '' );
		}
	}

	/**
	 * Ensure the `unfiltered_html` capability is added to the ET Support roles in Multisite
	 *
	 * @since 3.22
	 *
	 * @param array $caps An array of capabilities.
	 * @param string $cap The capability being requested.
	 * @param int $user_id The current user's ID.
	 *
	 * @return array Modified array of user capabilities.
	 */
	function support_user_map_meta_cap( $caps, $cap, $user_id ) {

		if ( ! $this->is_support_user( $user_id ) ) {
			return $caps;
		}

		// This user is in an ET Support user role, so add the capability
		if ( 'unfiltered_html' === $cap ) {
			$caps = array( 'unfiltered_html' );
		}

		return $caps;
	}

	/**
	 * Remove KSES filters on ET Support User's content
	 *
	 * @since 3.22
	 */
	function support_user_kses_remove_filters() {
		if ( $this->is_support_user() ) {
			kses_remove_filters();
		}
	}

	/**
	 * Clear "Delete Account" cron hook
	 *
	 * @since 3.20
	 *
	 * @return void
	 */
	public function support_user_clear_delete_cron() {
		wp_clear_scheduled_hook( $this->support_user_cron_name );
	}

	/**
	 * Delete the support account if it's expired or the expiration date is not set
	 *
	 * @since 3.20
	 *
	 * @return void
	 */
	public function support_user_cron_maybe_delete_account() {
		if ( ! username_exists( $this->support_user_account_name ) ) {
			return;
		}

		if ( isset( $this->support_user_options['date_created'] ) ) {
			$this->support_user_maybe_delete_expired_account();
		} else {
			// if the expiration date isn't set, delete the account anyway
			$this->support_user_delete_account();
		}
	}

	/**
	 * Schedule account removal check
	 *
	 * @since 3.20
	 *
	 * @return void
	 */
	public function support_user_init_cron_delete_account() {
		$this->support_user_clear_delete_cron();

		wp_schedule_event( time(), 'hourly', $this->support_user_cron_name );
	}

	/**
	 * Get plugin options
	 *
	 * @since 3.20
	 *
	 * @return void
	 */
	public function support_user_get_options() {
		$this->support_user_options = get_option( $this->support_user_options_name );
	}

	/**
	 * Generate random token
	 *
	 * @since 3.20
	 *
	 * @param integer $length Token Length
	 * @param bool $include_symbols Whether to include special characters (or just stick to alphanumeric)
	 *
	 * @return string  $token           Generated token
	 */
	public function support_user_generate_token( $length = 17, $include_symbols = true ) {
		$alphanum = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$symbols  = '!@$^*()-=+';
		$token    = substr( str_shuffle( $include_symbols ? $alphanum . $symbols : $alphanum ), 0, $length );

		return $token;
	}

	/**
	 * Generate password from token
	 *
	 * @since 3.20
	 *
	 * @param string $token Token
	 *
	 * @return string|WP_Error Generated password if successful, WP Error object otherwise
	 */
	public function support_user_generate_password( $token ) {
		global $wp_version;

		$salt = '';

		/** @see ET_Core_SupportCenter::maybe_set_site_id() */
		$site_id = get_option( 'et_support_site_id' );

		if ( empty( $site_id ) ) {
			return false;
		}

		// Site ID must be a string
		if ( ! is_string( $site_id ) ) {
			return false;
		}

		$et_license = $this->get_et_license();

		if ( ! $et_license ) {
			return false;
		}

		$send_to_api = array(
			'action'    => 'get_salt',
			'site_id'   => esc_attr( $site_id ),
			'username'  => esc_attr( $et_license['username'] ),
			'api_key'   => esc_attr( $et_license['api_key'] ),
			'site_url'  => esc_url( home_url( '/' ) ),
			'login_url' => 'https://www.elegantthemes.com/members-area/admin/token/'
						   . '?url=' . urlencode( wp_login_url() )
						   . '&token=' . urlencode( $token . '|' . $site_id ),
		);

		$support_user_options = array(
			'timeout'    => 30,
			'body'       => $send_to_api,
			'user-agent' => 'WordPress/' . $wp_version . '; Support Center/' . ET_CORE_VERSION . '; ' . home_url( '/' ),
		);

		$request = wp_remote_post(
			'https://www.elegantthemes.com/api/token.php',
			$support_user_options
		);

		// Early exit if we don't get a good HTTP response from the API server
		if ( 200 !== intval( wp_remote_retrieve_response_code( $request ) ) ) {
			return new WP_Error(
				'et_remote_access',
				esc_html__(
					'Elegant Themes API Error: HTTP error in API response',
					'et-core'
				)
			);
		}

		// Early exit and pass along WP_Error report if the server response is an error
		if ( is_wp_error( $request ) ) {
			return new WP_Error(
				'et_remote_access',
				esc_html__(
					'Elegant Themes API Error: WordPress Error in API response',
					'et-core'
				)
			);
		}

		// Otherwise the response is good - let's load it and continue
		$response = unserialize( wp_remote_retrieve_body( $request ) );

		// If the API returns an error, we will return and log the accompanying message
		$response_is_error          = array_key_exists( 'error', $response );
		$response_has_error_message = array_key_exists( 'message', $response );

		if ( $response_is_error && $response_has_error_message ) {
			return new WP_Error(
				'et_remote_access',
				esc_html__(
					'Elegant Themes API Error: ' . $response['message'],
					'et-core'
				)
			);
		}

		// If we get an "Incorrect Token" response, delete the generated Site ID from database
		$response_is_token_error = array_key_exists( 'incorrect_token', $response );

		if ( $response_is_token_error && ! empty( $response['incorrect_token'] ) ) {
			delete_option( 'et_support_site_id' );

			return new WP_Error(
				'et_remote_access',
				esc_html__(
					'Elegant Themes API Error: Incorrect Token. Please, try again.',
					'et-core'
				)
			);
		}

		// If we get a normal-looking response, but it doesn't contain the salt we need
		if ( empty( $response['salt'] ) ) {
			return new WP_Error(
				'et_remote_access',
				esc_html__(
					'Elegant Themes API Error: The API response was missing required data.',
					'et-core'
				)
			);
		}

		// We have the salt; let's clean it and make sure we can use it
		$salt = sanitize_text_field( $response['salt'] );

		if ( empty( $salt ) ) {
			return new WP_Error(
				'et_remote_access',
				esc_html__(
					'Elegant Themes API Error: The API responded, but the response was empty.',
					'et-core'
				)
			);
		}

		// Generate the password using the token we were initially passed & the salt from the API
		$password = hash( 'sha256', $token . $salt );

		return $password;
	}

	/**
	 * Delete the account if it's expired
	 *
	 * @since 3.20
	 *
	 * @return void
	 */
	public function support_user_maybe_delete_expired_account() {
		if ( empty( $this->support_user_options['date_created'] ) ) {
			return;
		}

		$expiration_date_unix = strtotime( $this->support_user_expiration_time, $this->support_user_options['date_created'] );

		// Delete the user account if the expiration date is in the past
		if ( time() >= $expiration_date_unix ) {
			$this->support_user_delete_account();
		}

		return;
	}

	/**
	 * Delete support account and the plugin options ( token, expiration date )
	 *
	 * @since 3.20
	 *
	 * @return string | WP_Error  Confirmation message on success, WP_Error on failure
	 */
	public function support_user_delete_account() {
		if ( defined( 'DOING_CRON' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/user.php' );
		}

		if ( ! username_exists( $this->support_user_account_name ) ) {
			return new WP_Error( 'get_user_data', esc_html__( 'Support account doesn\'t exist.', 'et-core' ) );
		}

		$support_account_data = get_user_by( 'login', $this->support_user_account_name );

		if ( $support_account_data ) {
			$support_account_id = $support_account_data->ID;

			if (
				( is_multisite() && ! wpmu_delete_user( $support_account_id ) )
				|| ( ! is_multisite() && ! wp_delete_user( $support_account_id ) )
			) {
				return new WP_Error( 'delete_user', esc_html__( 'Support account hasn\'t been removed. Try to regenerate token again.', 'et-core' ) );
			}

			delete_option( $this->support_user_options_name );
		} else {
			return new WP_Error( 'get_user_data', esc_html__( 'Cannot get the support account data. Try to regenerate token again.', 'et-core' ) );
		}

		$this->support_user_remove_roles();

		$this->support_user_remove_site_id();

		$this->support_user_clear_delete_cron();

		// update options variable
		$this->support_user_get_options();

		new WP_Error( 'get_user_data', esc_html__( 'Token has been deleted successfully.', 'et-core' ) );

		return esc_html__( 'Token has been deleted successfully. ', 'et-core' );
	}

	/**
	 * Maybe delete support account and the plugin options when switching themes
	 *
	 * If a theme change is one of:
	 * - [Divi/Extra] > [Divi/Extra] child theme
	 * - [Divi/Extra] child theme > [Divi/Extra] child theme
	 * - [Divi/Extra] child theme > [Divi/Extra]
	 * ...then we won't change the state of the Remote Access toggle.
	 *
	 * @since 3.23
	 *
	 * @return string | WP_Error  Confirmation message on success, WP_Error on failure
	 */
	public function maybe_deactivate_on_theme_switch() {
		// Don't do anything if the user isn't logged in
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Don't do anything if the parent theme's name matches the parent of this Support Center instance
		if ( get_option( 'template' ) === $this->parent_nicename ) {
			return;
		}

		// Leaving Divi/Extra environment; deactivate Support Center
		$this->support_user_delete_account();
		$this->unlist_support_center();
		$this->support_center_capabilities_teardown();
	}

	/**
	 * Is this user the ET Support User?
	 *
	 * @since 3.22
	 *
	 * @param int|null $user_id Pass a User ID to check. We'll get the current user's ID otherwise.
	 *
	 * @return bool Returns whether this user is the ET Support User.
	 */
	function is_support_user( $user_id = null ) {
		$user_id = $user_id ? (int) $user_id : get_current_user_id();
		if ( ! $user_id ) {
			return false;
		}

		$user = get_userdata( $user_id );

		if ( ! is_object( $user ) || ! property_exists( $user, 'roles' ) ) {
			return false;
		}

		// Gather this user's associated role(s).
		$user_roles      = (array) $user->roles;
		$user_is_support = false;

		// First, check the username.
		if ( ! $this->support_user_account_name === $user->user_login ) {
			return $user_is_support;
		}

		// Determine whether this user has the ET Support User role.
		if ( in_array( 'et_support', $user_roles, true ) ) {
			$user_is_support = true;
		}
		if ( in_array( 'et_support_elevated', $user_roles, true ) ) {
			$user_is_support = true;
		}

		return $user_is_support;
	}

	/**
	 * Delete support account and the plugin options ( token, expiration date )
	 *
	 * @since 3.20
	 *
	 * @return void
	 */
	public function unlist_support_center() {
		delete_option( 'et_support_center_installed' );
	}

	/**
	 *
	 */
	public function support_user_remove_site_id() {
		$site_id = get_option( 'et_support_site_id' );

		if ( empty( $site_id ) ) {
			return;
		}

		// Site ID must be a string
		if ( ! is_string( $site_id ) ) {
			return;
		}

		$et_license = $this->get_et_license();

		if ( ! $et_license ) {
			return;
		}

		$send_to_api = array(
			'action'   => 'remove_site_id',
			'site_id'  => esc_attr( $site_id ),
			'username' => esc_attr( $et_license['username'] ),
			'api_key'  => esc_attr( $et_license['api_key'] ),
			'site_url' => esc_url( home_url( '/' ) ),
		);

		$settings = array(
			'timeout' => 30,
			'body'    => $send_to_api,
		);

		$request = wp_remote_post( 'https://www.elegantthemes.com/api/token.php', $settings );
	}

	function support_user_update_via_ajax() {
		// Verify nonce
		et_core_security_check( 'manage_options', 'support_center', 'nonce' );

		// Get POST data
		$support_update = sanitize_text_field( $_POST['support_update'] );

		$response = array();

		// Update option(s)
		if ( 'activate' === $support_update ) {
			$maybe_create_user = $this->support_user_maybe_create_user();
			// Only activate if we have a User ID and Password
			if ( ! is_wp_error( $maybe_create_user ) ) {
				$this->support_user_set_role( 'et_support' );
				$account_settings   = get_option( $this->support_user_options_name );
				$site_id            = get_option( 'et_support_site_id' );
				$response['expiry'] = strtotime(
					date(
						'Y-m-d H:i:s ',
						$this->support_user_options['date_created']
					) . $this->support_user_expiration_time
				);
				$response['token']  = '';
				if ( ! empty( $site_id ) && is_string( $site_id ) ) {
					$account_setting_token = isset( $account_settings['token'] ) ? $account_settings['token'] : '';
					$response['token']     = $account_setting_token . '|' . $site_id;
				}
				$response['message'] = esc_html__(
					'ET Support User role has been activated.',
					'et-core'
				);
			} else {
				et_error( $maybe_create_user->get_error_message() );
				$response['error'] = $maybe_create_user->get_error_message();
			}
		}
		if ( 'elevate' === $support_update ) {
			$this->support_user_set_role( 'et_support_elevated' );
			$response['message'] = esc_html__(
				'ET Support User role has been elevated.',
				'et-core'
			);
		}
		if ( 'deactivate' === $support_update ) {
			$this->support_user_set_role( '' );
			$this->support_user_delete_account();
			$this->support_user_clear_delete_cron();
			$response['message'] = esc_html__(
				'ET Support User role has been deactivated.',
				'et-core'
			);
		}

		// `echo` data to return
		if ( isset( $response ) ) {
			echo json_encode( $response );
		}

		// `die` when we're done
		wp_die();
	}

	/**
	 * SUPPORT CENTER :: SAFE MODE
	 */

	/**
	 * ET Product Allowlist
	 *
	 * @since 3.28
	 *
	 * @param string $product Potential ET product name that we want to confirm is on the list.
	 *
	 * @return string|false   If the product is on our list, we return the "nice name" we have for it. Otherwise, we return FALSE.
	 */
	protected function is_allowlisted_product( $product = '' ) {
		switch ( $product ) {
			case 'divi_builder_plugin':
			case 'divi_theme':
			case 'extra_theme':
			case 'monarch_plugin':
			case 'bloom_plugin':
				return $this->get_parent_nicename( $product );
				break;
			default:
				return false;
		}
	}

	/**
	 * Safe Mode: Set session cookie to temporarily disable Plugins
	 *
	 * @since 3.20
	 *
	 * @return void
	 */
	function safe_mode_update_via_ajax() {
		et_core_security_check( 'manage_options', 'support_center', 'nonce' );

		$response = array();

		// Get POST data
		$support_update = sanitize_text_field( $_POST['support_update'] );

		// Update option(s)
		if ( 'activate' === $support_update ) {
			// Check the ET product that is activating Safe Mode
			$safe_mode_activator = sanitize_key( $_POST['product'] );

			// Confirm that this is a allowlisted product
			$allowlisted_product = $this->is_allowlisted_product( $safe_mode_activator );

			if ( ! $allowlisted_product ) {
				// Send a failure code and exit the function
				header( "HTTP/1.0 403 Forbidden" );
				print 'Bad or malformed ET product name.';
				wp_die();
			}

			$this->toggle_safe_mode( true, $safe_mode_activator );
			$response['message'] = esc_html__( 'ET Safe Mode has been activated.', 'et-core' );
		}
		if ( 'deactivate' === $support_update ) {
			$this->toggle_safe_mode( false );
			$response['message'] = esc_html__( 'ET Safe Mode has been deactivated.', 'et-core' );
		}

		$this->set_safe_mode_cookie();

		// `echo` data to return
		if ( isset( $response ) ) {
			echo json_encode( $response );
		}

		// `die` when we're done
		wp_die();
	}

	/**
	 * Toggle Safe Mode
	 *
	 * @since 3.20
	 *
	 * @param bool $activate TRUE if enabling Safe Mode, FALSE if disabling Safe mode.
	 * @param string $product Name of ET product that is activating Safe Mode (@see ET_Core_SupportCenter::get_parent_nicename()).
	 */
	public function toggle_safe_mode( $activate = true, $product = '' ) {
		$activate            = (bool) $activate;
		$user_id             = get_current_user_id();
		$allowlisted_product = $this->is_allowlisted_product( $product );

		// Only proceed with an activation request if it comes from a allowlisted product
		if ( $activate && ! $allowlisted_product ) {
			return;
		}

		update_user_meta( $user_id, '_et_support_center_safe_mode', $activate ? 'on' : 'off' );
		update_user_meta( $user_id, '_et_support_center_safe_mode_product', $activate ? sanitize_text_field( $allowlisted_product ) : '' );

		$activate ? $this->maybe_add_mu_autoloader() : $this->maybe_remove_mu_autoloader();

		/**
		 * Fires when safe mode is toggled on or off.
		 *
		 * @since 3.25.4
		 *
		 * @param bool $state True if toggled on, false if toggled off.
		 */
		do_action( 'et_support_center_toggle_safe_mode', $activate );
	}

	/**
	 * Set Safe Mode Cookie
	 *
	 * @since 3.20
	 *
	 * @return void
	 */
	function set_safe_mode_cookie() {
		if ( et_core_is_safe_mode_active() ) {
			// This random string ensures old cookies aren't used to view the site in Safe Mode
			$passport = md5( rand() );

			update_option( 'et-support-center-safe-mode-verify', $passport );
			setcookie( 'et-support-center-safe-mode', $passport, time() + DAY_IN_SECONDS, SITECOOKIEPATH, false, is_ssl() );
		} else {
			// Force-expire the cookie
			setcookie( 'et-support-center-safe-mode', '', 1, SITECOOKIEPATH, false, is_ssl() );
		}
	}

	/**
	 * Render modal that intercepts plugin activation/deactivation
	 *
	 * @since 3.20
	 *
	 * @return void
	 */
	public function render_safe_mode_block_restricted() {
		if ( ! et_core_is_safe_mode_active() ) {
			return;
		}

		// Get the name of the ET product that activated Safe Mode
		$safe_mode_activator = get_user_meta( get_current_user_id(), '_et_support_center_safe_mode_product', true );
		$verified_activator  = $this->is_allowlisted_product( $safe_mode_activator );

		?>
		<script type="text/template" id="et-ajax-safe-mode-template">
			<div class="et-core-modal-overlay et-core-form et-core-safe-mode-block-modal">
				<div class="et-core-modal">
					<div class="et-core-modal-header">
						<h3 class="et-core-modal-title">
							<?php print esc_html__( 'Safe Mode', 'et-core' ); ?>
						</h3>
						<a href="#" class="et-core-modal-close" data-et-core-modal="close"></a>
					</div>
					<div id="et-core-safe-mode-block-modal-content">
						<div class="et-core-modal-content">
							<p><?php print esc_html__(
									'Safe Mode is enabled and the current action cannot be performed.',
									'et-core'
								); ?></p>
						</div>
						<a class="et-core-modal-action"
								href="<?php echo admin_url( null, 'admin.php?page=et_support_center#et_card_safe_mode' ); ?>">
							<?php print esc_html__( sprintf( 'Turn Off %1$s Safe Mode', $verified_activator ), 'et-core' ); ?>
						</a>
					</div>
				</div>
			</div>
		</script>
		<?php

	}

	/**
	 * Disable Custom CSS (if Safe Mode is active)
	 *
	 * @since 3.20
	 */
	function maybe_disable_custom_css() {
		// Don't do anything if the user isn't logged in
		if ( ! is_user_logged_in() ) {
			return;
		}

		if ( et_core_is_safe_mode_active() ) {
			// Remove "Additional CSS" from WP Head action hook
			remove_action( 'wp_head', 'wp_custom_css_cb', 101 );
		}
	}

	/**
	 * Add Safe Mode Indicator (if Safe Mode is active)
	 *
	 * @since 3.20
	 */
	function maybe_add_safe_mode_indicator() {
		// Don't do anything if the user isn't logged in
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Don't display when Visual Builder is active
		if ( et_core_is_fb_enabled() ) {
			return;
		}

		if ( et_core_is_safe_mode_active() ) {
			// Get the name of the ET product that activated Safe Mode
			$safe_mode_activator = get_user_meta( get_current_user_id(), '_et_support_center_safe_mode_product', true );
			$verified_activator  = $this->is_allowlisted_product( $safe_mode_activator );

			print sprintf( '<a class="%1$s" href="%2$s">%3$s</a>',
				'et-safe-mode-indicator',
				esc_url( get_admin_url( null, 'admin.php?page=et_support_center#et_card_safe_mode' ) ),
				esc_html__( sprintf( 'Turn Off %1$s Safe Mode', $verified_activator ), 'et-core' )
			);

			print sprintf( '<div id="%1$s"><img src="%2$s" alt="%3$s" id="%3$s"/></div>',
				'et-ajax-saving',
				esc_url( $this->local_path . 'admin/images/ajax-loader.gif' ),
				'loading'
			);
		}
	}

	/**
	 * Prints the admin page for Support Center
	 *
	 * @since 3.20
	 */
	public function add_support_center() {

		$is_current_user_et_support = 0;
		if ( in_array( 'et_support', wp_get_current_user()->roles ) ) {
			$is_current_user_et_support = 1;
		}
		if ( in_array( 'et_support_elevated', wp_get_current_user()->roles ) ) {
			$is_current_user_et_support = 2;
		}

		// Conditionally Display Divi Hosting Card
		$this->maybe_display_divi_hosting_card();
		?>
		<div id="et_support_center" class="wrap et-divi-admin-page--wrapper" data-et-zone="wp-admin" data-et-page="wp-admin-support-center">
			<h1><?php esc_html_e( sprintf( '%1$s Help &amp; Support Center', $this->parent_nicename ), 'et-core' );
				?></h1>

			<div id="epanel">
				<div id="epanel-content">

					<?php

					/**
					 * Run code before any of the Support Center cards have been output
					 *
					 * @since 3.20
					 */
					do_action( 'et_support_center_above_cards' );

					// Build Card :: System Status
					if ( $this->current_user_can( 'et_support_center_system' ) ) {
						$card_title   = esc_html__( 'System Status', 'et-core' );
						$card_content = sprintf( '<div class="et-system-status summary">%1$s</div>'
												 . '<textarea id="et_system_status_plain">%2$s</textarea>'
												 . '<div class="et_card_cta">%3$s %4$s %5$s</div>',
							et_core_intentionally_unescaped( $this->system_diagnostics_generate_report( true, 'div' ), 'html' ),
							et_core_intentionally_unescaped( $this->system_diagnostics_generate_report( true, 'plain' ), 'html' ),
							sprintf( '<a class="full_report_show">%1$s</a>', esc_html__( 'Show Full Report', 'et-core' ) ),
							sprintf( '<a class="full_report_hide">%1$s</a>', esc_html__( 'Hide Full Report', 'et-core' ) ),
							sprintf( '<a class="full_report_copy">%1$s</a>', esc_html__( 'Copy Full Report', 'et-core' ) )
						);

						print $this->add_support_center_card( array(
							'title'              => $card_title,
							'content'            => $card_content,
							'additional_classes' => array(
								'et_system_status',
								'summary',
							),
						) );
					}

					/**
					 * Run code after the 1st Support Center card has been output
					 *
					 * @since 3.20
					 */
					do_action( 'et_support_center_below_position_1' );

					// Build Card :: Remote Access
					if ( $this->current_user_can( 'et_support_center_remote_access' ) && ( 0 === $is_current_user_et_support ) ) {

						$card_title   = esc_html__( 'Elegant Themes Support', 'et-core' );
						$card_content = __( '<p>Enabling <strong>Remote Access</strong> will give the Elegant Themes support team limited access to your WordPress Dashboard. If requested, you can also enable full admin privileges. Remote Access should only be turned on if requested by the Elegant Themes support team. Remote Access is automatically disabled after 4 days.</p>', 'et-core' );

						$support_account = $this->get_et_support_user();

						$is_et_support_user_active = 0;

						$has_et_license = $this->get_et_license();

						if ( ! $has_et_license ) {

							$card_content .= sprintf(
								'<div class="et-support-user"><h4>%1$s</h4><p>%2$s</p></div>',
								esc_html__( 'Remote Access', 'et-core' ),
								__( 'Remote Access cannot be enabled because you do not have a valid API Key or your Elegant Themes subscription has expired. You can find your API Key by <a href="https://www.elegantthemes.com/members-area/api/" target="_blank">logging in</a> to your Elegant Themes account. It should then be added to your <a href="https://www.elegantthemes.com/documentation/divi/update-divi/" target=_blank">Options Panel</a>.', 'et-core' )
							);

						} else {

							if ( is_object( $support_account ) && property_exists( $support_account, 'roles' ) ) {
								if ( in_array( 'et_support', $support_account->roles ) ) {
									$is_et_support_user_active = 1;
								}
								if ( in_array( 'et_support_elevated', $support_account->roles ) ) {
									$is_et_support_user_active = 2;
								}
							}

							$support_user_active_state = ( intval( $is_et_support_user_active ) > 0 ) ? ' et_pb_on_state' : ' et_pb_off_state';

							$expiry = '';
							if ( ! empty( $this->support_user_options['date_created'] ) ) {
								// Calculate the 'Created Date' plus the 'Time To Expire'
								$date_created = date( 'Y-m-d H:i:s ', $this->support_user_options['date_created'] );
								$expiry       = strtotime( $date_created . $this->support_user_expiration_time );
							}

							// Toggle Support User activation
							$card_content .= sprintf( '<div class="et-support-user"><h4>%1$s</h4>'
													  . '<div class="et_support_user_toggle">'
													  . '<div class="%7$s_wrapper"><div class="%7$s %2$s">'
													  . '<span class="%8$s et_pb_on_value">%3$s</span>'
													  . '<span class="et_pb_button_slider"></span>'
													  . '<span class="%8$s et_pb_off_value">%4$s</span>'
													  . '</div></div>'
													  . '<span class="et-support-user-expiry" data-expiry="%5$s">%6$s'
													  . '<span class="support-user-time-to-expiry"></span>'
													  . '</span>'
													  . '<span class="et-remote-access-error"></span>'
													  . '</div>'
													  . '</div>',
								esc_html__( 'Remote Access', 'et-core' ),
								esc_attr( $support_user_active_state ),
								esc_html__( 'Enabled', 'et-core' ),
								esc_html__( 'Disabled', 'et-core' ),
								esc_attr( $expiry ),
								esc_html__( 'Remote Access will be automatically disabled in: ', 'et-core' ),
								'et_pb_yes_no_button',
								'et_pb_value_text'
							);

							// Toggle Support User role elevation (only visible if Support User is active)
							$extra_css                   = ( intval( $is_et_support_user_active ) > 0 ) ? 'style="display:block;"' : '';
							$support_user_elevated_state = ( intval( $is_et_support_user_active ) > 1 ) ? ' et_pb_on_state' : ' et_pb_off_state';

							$card_content .= sprintf( '<div class="et-support-user-elevated" %5$s><h4>%1$s</h4>'
													  . '<div class="et_support_user_elevated_toggle">'
													  . '<div class="%6$s_wrapper"><div class="%6$s %2$s">'
													  . '<span class="%7$s et_pb_on_value">%3$s</span>'
													  . '<span class="et_pb_button_slider"></span>'
													  . '<span class="%7$s et_pb_off_value">%4$s</span>'
													  . '</div></div>'
													  . '</div>'
													  . '</div>',
								esc_html__( 'Activate Full Admin Privileges', 'et-core' ),
								esc_attr( $support_user_elevated_state ),
								esc_html__( 'Enabled', 'et-core' ),
								esc_html__( 'Disabled', 'et-core' ),
								et_core_intentionally_unescaped( $extra_css, 'html' ),
								'et_pb_yes_no_button',
								'et_pb_value_text'
							);
						}

						// Add a "Copy Support Token" CTA if Remote Access is active
						$site_id           = get_option( 'et_support_site_id' );
						$support_token_cta = '';
						if ( intval( $is_et_support_user_active ) > 0 && ! empty( $site_id ) && is_string( $site_id ) ) {
							$account_settings      = get_option( $this->support_user_options_name );
							$account_setting_token = isset( $account_settings['token'] ) ? $account_settings['token'] : '';
							$support_token_cta     = '<a class="copy_support_token" data-token="'
												. esc_attr( $account_setting_token . '|' . $site_id )
												. '">'
												. esc_html__( 'Copy Support Token', 'et-core' )
												. '</a>';
						}

						$vip_support_content = '<div class="et_vip_support">'
							. '<div class="et_vip_support__left">'
								. '<a target="_blank" href="https://www.elegantthemes.com/vip/?utm_source=Divi+VIP&utm_medium=Support+Center&utm_campaign=Native">'
									. '<img src="' . esc_url( ET_CORE_URL ) . 'admin/images/blurb-vip.jpg" alt="Divi VIP Support" />'
								. '</a>'
							. '</div>'
							. '<div class="et_vip_support__right">'
								. '<h2>' . esc_html__( 'Get More With Divi VIP', 'et-core' ) . '</h2>'
								. '<h2>' . esc_html__( 'The Best Support, Even Faster.', 'et-core' ) . '</h2>'
								. '<p>'
									. esc_html__( 'We want to provide exactly the level of support any of our customers need to be successful. With Divi VIP, you get faster support (Under 30 minutes response times around the clock). Keep your clients happy by letting us solve their problems faster.', 'et-core' )
								. '</p>'
								. '<a target="_blank" href="https://www.elegantthemes.com/vip/?utm_source=Divi+VIP&utm_medium=Support+Center&utm_campaign=Native">'
									. esc_html__( 'Get Divi VIP Today!', 'et-core' )
								. '</a>'
							. '</div>'
						. '</div>';

						$card_content .= '<div class="et_card_cta">'
										. '<a target="_blank" href="https://www.elegantthemes.com/members-area/help/">'
										. esc_html__( 'Chat With Support', 'et-core' )
										. '</a>'
										. $support_token_cta
										. $vip_support_content
										. '</div>';

						print $this->add_support_center_card( array(
							'title'              => $card_title,
							'content'            => $card_content,
							'additional_classes' => array(
								'et_remote_access',
								'et-epanel-box',
							),
						) );
					}

					/**
					 * Run code after the 2nd Support Center card has been output
					 *
					 * @since 3.20
					 */
					do_action( 'et_support_center_below_position_2' );

					// Build Card :: Divi Documentation & Help
					if ( $this->current_user_can( 'et_support_center_documentation' ) ) {
						switch ( $this->parent ) {
							case 'extra_theme':
								$documentation_url = 'https://www.elegantthemes.com/documentation/extra/';
								break;
							case 'divi_theme':
								$documentation_url = 'https://www.elegantthemes.com/documentation/divi/';
								break;
							case 'divi_builder_plugin':
								$documentation_url = 'https://www.elegantthemes.com/documentation/divi-builder/';
								break;
							case 'monarch_plugin':
								$documentation_url = 'https://www.elegantthemes.com/documentation/monarch/';
								break;
							case 'bloom_plugin':
								$documentation_url = 'https://www.elegantthemes.com/documentation/bloom/';
								break;
							default:
								$documentation_url = 'https://www.elegantthemes.com/documentation/';
						}

						$card_title   = esc_html__(
							sprintf( '%1$s Documentation &amp; Help', $this->parent_nicename ),
							'et-core'
						);
						$card_content = $this->get_documentation_video_player();
						$card_content .= $this->get_documentation_articles_list();
						$card_content .= '<div class="et_card_cta">'
										 . '<a href="' . $documentation_url . '" class="launch_documentation" target="_blank">'
										 . esc_html__(
											 sprintf( 'View Full %1$s Documentation', $this->parent_nicename ),
											 'et-core'
										 )
										 . '</a>'
										 . '</div>';

						print $this->add_support_center_card( array(
							'title'              => $card_title,
							'content'            => $card_content,
							'additional_classes' => array(
								'et_documentation_help',
								'et-epanel-box',
							),
						) );
					}

					/**
					 * Run code after the 3rd Support Center card has been output
					 *
					 * @since 3.20
					 */
					do_action( 'et_support_center_below_position_3' );

					// Build Card :: Safe Mode
					if ( $this->current_user_can( 'et_support_center_safe_mode' ) ) {

						$card_title       = esc_html__( 'Safe Mode', 'et-core' );
						$card_content     = __( '<p>Enabling <strong>Safe Mode</strong> will temporarily disable features and plugins that may be causing problems with your Elegant Themes product. This includes all Plugins, Child Themes, and Custom Code added to your integration areas. These items are only disabled for your current user session so your visitors will not be disrupted. Enabling Safe Mode makes it easy to figure out what is causing problems on your website by identifying or eliminating third party plugins and code as potential causes.</p>', 'et-core' );
						$error_message    = '';
						$safe_mode_active = ( et_core_is_safe_mode_active() ) ? ' et_pb_on_state' : ' et_pb_off_state';
						$plugins_list     = array();
						$plugins_output   = '';

						$has_mu_plugins_dir        = wp_mkdir_p( WPMU_PLUGIN_DIR ) && wp_is_writable( WPMU_PLUGIN_DIR );
						$can_create_mu_plugins_dir = wp_is_writable( WP_CONTENT_DIR ) && ! wp_mkdir_p( WPMU_PLUGIN_DIR );

						if ( $has_mu_plugins_dir || $can_create_mu_plugins_dir ) {
							// Gather list of plugins that will be temporarily deactivated in Safe Mode
							$all_plugins    = get_plugins();
							$active_plugins = get_option( 'active_plugins' );

							foreach ( $active_plugins as $plugin ) {
								// Verify this 'active' plugin actually exists in the plugins directory
								if ( ! in_array( $plugin, array_keys( $all_plugins ) ) ) {
									continue;
								}

								// If it's not in our allowlist, add it to the list of plugins we'll disable
								if ( ! in_array( $plugin, $this->safe_mode_plugins_allowlist ) ) {
									$plugins_list[] = '<li>' . esc_html( $all_plugins[ $plugin ]['Name'] ) . '</li>';
								}
							}

						} else {
							$error_message = et_get_safe_localization( sprintf( __( '<p class="et-safe-mode-error">Plugins cannot be disabled because your <code>wp-content</code> directory has inconsistent file permissions. <a href="%1$s" target="_blank">Click here</a> for more information.</p>', 'et-core' ), 'https://wordpress.org/support/article/changing-file-permissions/' ) );
						}

						if ( count( $plugins_list ) > 0 ) {
							$plugins_output = sprintf( '<p>%1$s</p><ul>%2$s</ul>',
								esc_html__( 'The following plugins will be temporarily disabled for you only:', 'et-core' ),
								et_core_intentionally_unescaped( implode( ' ', $plugins_list ), 'html' )
							);
						}

						// Toggle Safe Mode activation
						$card_content .= sprintf( '<div id="et_card_safe_mode" class="et-safe-mode" data-et-product="%8$s">'
												  . '<div class="et_safe_mode_toggle">'
												  . '<div class="%5$s_wrapper"><div class="%5$s %1$s">'
												  . '<span class="%6$s et_pb_on_value">%2$s</span>'
												  . '<span class="et_pb_button_slider"></span>'
												  . '<span class="%6$s et_pb_off_value">%3$s</span>'
												  . '</div></div>'
												  . '%4$s'
												  . '%7$s'
												  . '</div>'
												  . '</div>',
							esc_attr( $safe_mode_active ),
							esc_html__( 'Enabled', 'et-core' ),
							esc_html__( 'Disabled', 'et-core' ),
							$plugins_output,
							'et_pb_yes_no_button',
							'et_pb_value_text',
							$error_message,
							esc_attr( $this->parent )
						);

						print $this->add_support_center_card( array(
							'title'              => $card_title,
							'content'            => $card_content,
							'additional_classes' => array(
								'et_safe_mode',
								'et-epanel-box',
							),
						) );
					}

					/**
					 * Run code after the 4th Support Center card has been output
					 *
					 * @since 3.20
					 */
					do_action( 'et_support_center_below_position_4' );

					// Build Card :: Logs
					if ( $this->current_user_can( 'et_support_center_logs' ) ) {
						$debug_log_lines = apply_filters( 'et_debug_log_lines', 200 );
						$wp_debug_log    = $this->get_wp_debug_log( $debug_log_lines );
						$card_title      = esc_html__( 'Logs', 'et-core' );

						$card_content = '<p>If you have <a href="https://codex.wordpress.org/Debugging_in_WordPress" target=_blank" >WP_DEBUG_LOG</a> enabled, WordPress related errors will be archived in a log file. For your convenience, we have aggregated the contents of this log file so that you and the Elegant Themes support team can view it easily. The file cannot be edited here.</p>';

						if ( isset( $wp_debug_log['error'] ) ) {
							$card_content .= '<div class="et_system_status_log_preview">'
											 . '<textarea>' . $wp_debug_log['error'] . '</textarea>'
											 . '</div>';
						} else {
							$card_content .= '<div class="et_system_status_log_preview">'
											 . '<textarea id="et_logs_display">' . $wp_debug_log['entries'] . '</textarea>'
											 . '<textarea id="et_logs_recent">' . $wp_debug_log['entries'] . '</textarea>'
											 . '</div>'
											 . '<div class="et_card_cta">'
											 . '<a href="' . content_url( 'debug.log' ) . '" class="download_debug_log" download>'
											 . esc_html__( 'Download Full Debug Log', 'et-core' )
											 . ' (' . $wp_debug_log['size'] . ')'
											 . '</a>'
											 . '<a class="copy_debug_log">'
											 . esc_html__( 'Copy Recent Log Entries', 'et-core' )
											 . '</a>'
											 . '</div>';
						}

						print $this->add_support_center_card( array(
							'title'              => $card_title,
							'content'            => $card_content,
							'additional_classes' => array(
								'et_system_logs',
								'et-epanel-box',
							),
						) );
					}

					/**
					 * Run code after all of the Support Center cards have been output
					 *
					 * @since 3.20
					 */
					do_action( 'et_support_center_below_cards' );

					?>
				</div>
			</div>
		</div>
		<div id="et-ajax-saving">
			<img src="<?php echo esc_url( $this->local_path . 'admin/images/ajax-loader.gif' ); ?>" alt="loading" id="loading" />
		</div>
		<?php

	}

	/**
	 * SUPPORT CENTER :: DIVI HOSTING CARD
	 */

	/**
	 * Conditionally display Divi Hosting Card in the Support Center
	 *
	 * @since 4.4.7
	 */
	public function maybe_display_divi_hosting_card() {
		// Sanity Check: Exit early if the user does not have permission
		if ( ! $this->current_user_can( 'et_support_center_system' ) ) {
			return;
		}

		// Exit if Admin dismissed the Divi Hosting Card
		if ( get_option( 'et_hosting_card_dismissed', false ) ) {
			return;
		}

		// Show the Divi Hosting Card
		add_action( 'et_support_center_below_position_1', array( $this, 'print_divi_hosting_card' ) );
	}

	/**
	 * Prepare Settings for ET API request
	 * Returns false when ET username/api_key is not found, and ET subscription is not active
	 *
	 * @since 4.4.7
	 * @param string $action
	 *
	 * @return bool|array
	 */
	protected function get_et_api_request_settings( $action ) {
		$et_account  = et_core_get_et_account();
		$et_username = et_()->array_get( $et_account, 'et_username', '' );
		$et_api_key  = et_()->array_get( $et_account, 'et_api_key', '' );

		// Only when ET Username and ET API Key is found
		if ( '' !== $et_username && '' !== $et_api_key ) {
			global $wp_version;

			// Prepare settings for API request
			return array(
				'timeout'    => 30,
				'body'       => array(
					'action'   => $action,
					'username' => $et_username,
					'api_key'  => $et_api_key,
				),
				'user-agent' => 'WordPress/' . $wp_version . '; Support Center/' . ET_CORE_VERSION . '; ' . home_url( '/' ),
			);
		}

		return false;
	}

	/**
	 * Check ET API whether ET User has disabled Divi Hosting Card
	 *
	 * @since 4.4.7
	 *
	 * @return bool
	 */
	protected function maybe_api_has_hosting_card_disabled() {
	    // Get API settings
		$api_settings = $this->get_et_api_request_settings( 'check_hosting_card_status' );

		// Check API only when ET Username, ET API Key is found and Account is active
		if ( is_array( $api_settings ) ) {
			$request               = wp_remote_post( 'https://www.elegantthemes.com/api/api.php', $api_settings );
			$request_response_code = wp_remote_retrieve_response_code( $request );

			// Do not show the Hosting Card when API Request, or, API Response has any error
			if ( is_wp_error( $request ) || 200 !== $request_response_code ) {
				return true;
			}

			$response_body = wp_remote_retrieve_body( $request );
			$response      = (array) json_decode( $response_body );

			// Check whether the User has disabled the card
			if ( et_()->array_get( $response, 'success' ) && et_()->array_get( $response, 'status' ) === 'disabled' ) {
				// Mark it dismissed, so it won't be displayed anymore on this website
				update_option( 'et_hosting_card_dismissed', true );

				// Do not show the Hosting Card
				return true;
			}
		}

		// Show the Hosting Card
		return false;
	}

	/**
	 * Return Data for Divi Hosting Card
	 *
	 * @since 4.4.7
	 *
	 * @return array
	 */
	protected function get_divi_hosting_features() {

		return array(
			'title'           => esc_html__( 'Get Recommended Divi Hosting', 'et-core' ),
			'summary'         => esc_html__( 'Upgrade your hosting to the most reliable, Divi-compatible hosting. Enjoy perfectly configured hosting environments pre-installed with the tools you need to be successful with Divi.', 'et-core' ),
			'url'             => 'https://www.elegantthemes.com/hosting/',
			'learn_more'      => esc_html__( 'Learn About Divi Hosting', 'et-core' ),
			'dismiss_tooltip' => esc_html__( 'Remove This Recommendation On All Of Your Websites And Your Client\'s Websites Forever', 'et-core' ),
			'features'        => array(
				'server'   => array(
					'title'   => esc_html__( 'Divi-Optimized Servers', 'et-core' ),
					'tooltip' => esc_html__( "We worked with our parters to make sure that their hosting solutions meet all of Divi's requirements out of the box. No hosting headaches on Divi Hosting.", 'et-core' ),
				),
				'speed'    => array(
					'title'   => esc_html__( 'Blazing Fast Speed', 'et-core' ),
					'tooltip' => esc_html__( 'Divi Hosting is powered by fast networks, modern hosting infrastructures and the latest server software. Plus you will enjoy automatic caching and a free CDN.', 'et-core' ),
				),
				'security' => array(
					'title'   => esc_html__( 'A Focus On Security', 'et-core' ),
					'tooltip' => esc_html__( 'All of our hosting partners are dedicated to security. That means up-to-date server software and secure hosting practices.', 'et-core' ),
				),
				'backups'  => array(
					'title'   => esc_html__( 'Automatic Backups', 'et-core' ),
					'tooltip' => esc_html__( 'Every website needs backups! Each of our hosting partners provide automatic daily backups. If disaster strikes, these hosting companies have your back.', 'et-core' ),
				),
				'migrate'  => array(
					'title'   => esc_html__( 'Easy Site Migration', 'et-core' ),
					'tooltip' => esc_html__( "Already have a Divi website hosted somewhere else? All of our hosting partners provide migration tools or professional assisted migration. It's easy to switch to Divi Hosting!", 'et-core' ),
				),
				'staging'  => array(
					'title'   => esc_html__( 'Easy Staging Sites', 'et-core' ),
					'tooltip' => esc_html__( 'Automatic staging sites make it easy to develop new designs for your clients without disrupting visitors. Finish your work and push it live all at once.', 'et-core' ),
				),
			),
		);
	}

	/**
	 * Build and display Divi Hosting Card
	 *
	 * @since 4.4.7
	 */
	public function print_divi_hosting_card() {
		// Gather System status data
		$report = $this->system_diagnostics_generate_report( false );
		$result = array();

		// Prepare the report data to check against when to show the Divi Hosting Card
		foreach ( $report as $status ) {
			$result[] = et_()->array_get( $status, 'pass_fail' );
		}

		// Exit if any system status item is not in a warning state (red dot indicator)
		if ( ! in_array( 'fail', array_values( $result ), true ) ) {
			return;
		}

		// Exit if ET User has disabled the Divi Hosting card
		if ( $this->maybe_api_has_hosting_card_disabled() ) {
			return;
		}

		// JS dependency for Tooltips
		wp_enqueue_script( 'popper', $this->local_path . 'admin/js/popper.min.js', array( 'jquery' ), ET_CORE_VERSION );
		wp_enqueue_script( 'tippy', $this->local_path . 'admin/js/tippy.min.js', array( 'jquery', 'popper' ), ET_CORE_VERSION );

		$card     = $this->get_divi_hosting_features();
		$features = '';

		// HTML Template for Features of the Divi Hosting Card
		foreach ( $card['features'] as $name => $feature ) {
			$features .= sprintf(
				'<div class="et_hosting_card--feature" data-tippy-content="%1$s">%2$s <h4>%3$s</h4></div>',
				esc_html( $feature['tooltip'] ),
				sprintf(
					'<object type="image/svg+xml" className="fitvidsignore" data="%1$s" width="32" height="32"></object>',
					esc_url( "{$this->local_path}admin/images/svg/{$name}.svg" )
				),
				esc_html( $feature['title'] )
			);
		}

		// HTML Template for the Divi Hosting Card
		$card_content = sprintf(
			'<p class="et_card_summary">%1$s</p>
			<div class="et_card_content et_hosting_card--features">%2$s</div>
			<div class="et_card_cta et_hosting_card--cta">%3$s</div>',
			esc_html( $card['summary'] ),
			et_core_esc_previously( $features ),
			sprintf(
				'<a class="et_hosting_card--link" target="_blank" href="%1$s" title="%2$s">%3$s</a>',
				esc_url( $card['url'] ),
				esc_attr( $card['learn_more'] ),
				esc_html( $card['learn_more'] )
			)
		);

		// Display the Divi Hosting Card
		print $this->add_support_center_card( array(
			'title'              => $card['title'],
			'content'            => $card_content,
			'additional_classes' => array(
				'et_hosting_card',
			),
			'dismiss_button'     => array(
				'card_key'           => 'et_hosting_card',
				'tooltip'            => $card['dismiss_tooltip'],
				'additional_classes' => array(
					'et_hosting_card--dismiss',
				),
			),
		) );
	}
}
