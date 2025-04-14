<?php
/*
  Plugin Name: Advanced Google reCAPTCHA
  Plugin URI: https://getwpcaptcha.com/
  Description: Advanced Google reCAPTCHA will safeguard your WordPress site from spam comments and brute force attacks. With this plugin, you can easily add Google reCAPTCHA to WordPress comment form, login form and other forms.
  Version: 1.22
  Author: WebFactory Ltd
  Author URI: https://www.webfactoryltd.com/
  License: GNU General Public License v3.0
  Text Domain: advanced-google-recaptcha
  Requires at least: 4.0
  Tested up to: 6.5
  Requires PHP: 5.2

  Copyright 2023 - 2024  WebFactory Ltd  (email: support@webfactoryltd.com)
  Copyright 2021 - 2023  WP Concern

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// include only file
if (!defined('ABSPATH')) {
    wp_die(esc_html__('Do not open this file directly.', 'advanced-google-recaptcha'));
}

define('WPCAPTCHA_PLUGIN_FILE', __FILE__);
define('WPCAPTCHA_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPCAPTCHA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPCAPTCHA_OPTIONS_KEY', 'wpcaptcha_options');
define('WPCAPTCHA_META_KEY', 'wpcaptcha_meta');
define('WPCAPTCHA_POINTERS_KEY', 'wpcaptcha_pointers');
define('WPCAPTCHA_NOTICES_KEY', 'wpcaptcha_notices');

require_once WPCAPTCHA_PLUGIN_DIR . 'libs/admin.php';
require_once WPCAPTCHA_PLUGIN_DIR . 'libs/setup.php';
require_once WPCAPTCHA_PLUGIN_DIR . 'libs/utility.php';
require_once WPCAPTCHA_PLUGIN_DIR . 'libs/functions.php';
require_once WPCAPTCHA_PLUGIN_DIR . 'libs/stats.php';
require_once WPCAPTCHA_PLUGIN_DIR . 'libs/ajax.php';

require_once WPCAPTCHA_PLUGIN_DIR . 'interface/tab_login_form.php';
require_once WPCAPTCHA_PLUGIN_DIR . 'interface/tab_activity.php';
require_once WPCAPTCHA_PLUGIN_DIR . 'interface/tab_temp_access.php';
require_once WPCAPTCHA_PLUGIN_DIR . 'interface/tab_firewall.php';
require_once WPCAPTCHA_PLUGIN_DIR . 'interface/tab_captcha.php';
require_once WPCAPTCHA_PLUGIN_DIR . 'interface/tab_geoip.php';
require_once WPCAPTCHA_PLUGIN_DIR . 'interface/tab_design.php';

require_once WPCAPTCHA_PLUGIN_DIR . 'wf-flyout/wf-flyout.php';


// main plugin class
class WPCaptcha
{
    static $version = 0;
    static $type;

    /**
     * Setup Hooks
     *
     * @since 5.0
     *
     * @return null
     */
    static function init()
    {
        // check if minimal required WP version is present
        if (false === WPCaptcha_Setup::check_wp_version(4.6) || false === WPCaptcha_Setup::check_php_version('5.6.20')) {
            return false;
        }

        WPCaptcha_Setup::maybe_upgrade();
        WPCaptcha_Functions::handle_unblock();
        $options = WPCaptcha_Setup::get_options();

        if (is_admin()) {
            new wf_flyout(__FILE__);

            // add WP Captcha menu to admin tools menu group
            add_action('admin_menu', array('WPCaptcha_Admin', 'admin_menu'));

            // aditional links in plugin description and footer
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), array('WPCaptcha_Admin', 'plugin_action_links'));
            add_filter('plugin_row_meta', array('WPCaptcha_Admin', 'plugin_meta_links'), 10, 2);
            add_filter('admin_footer_text', array('WPCaptcha_Admin', 'admin_footer_text'));

            // settings registration
            add_action('admin_init', array('WPCaptcha_Setup', 'register_settings'));
            add_action('admin_notices', array('WPCaptcha_Admin', 'admin_notices'));

            // enqueue admin scripts
            add_action('admin_enqueue_scripts', array('WPCaptcha_Admin', 'admin_enqueue_scripts'));

            // admin actions
            add_action('admin_action_wpcaptcha_install_template', array('WPCaptcha_Functions', 'install_template'));
            add_action('admin_action_wpcaptcha_install_wp301', array('WPCaptcha_Functions', 'install_wp301'));
            
            // AJAX endpoints
            add_action('wp_ajax_wpcaptcha_run_tool', array('WPCaptcha_AJAX', 'ajax_run_tool'));
        } else {
            // Handle login captcha
            if($options['captcha_show_login']){
                add_filter( 'login_form', array('WPCaptcha_Functions', 'captcha_fields'));
                add_action( 'woocommerce_login_form', array('WPCaptcha_Functions', 'captcha_fields'));
                add_action( 'woocommerce_login_form', array('WPCaptcha_Functions', 'login_form_fields'));
                add_action( 'woocommerce_login_form', array('WPCaptcha_Functions', 'login_print_scripts'));
                add_filter( 'edd_login_fields_after', array('WPCaptcha_Functions', 'captcha_fields'));
                add_filter( 'edd_login_fields_after', array('WPCaptcha_Functions', 'login_print_scripts'));
            }

            // Handle registration captcha
            if($options['captcha_show_wp_registration']){
                add_filter( 'registration_errors', array('WPCaptcha_Functions', 'handle_captcha_wp_registration'), 10, 3 );
                add_filter( 'register_form', array('WPCaptcha_Functions', 'captcha_fields'));
            }

            // Handle lost password captcha
            if($options['captcha_show_wp_lost_password']){
                add_filter( 'lostpassword_form', array('WPCaptcha_Functions', 'captcha_fields'));
                add_filter( 'resetpass_form', array('WPCaptcha_Functions', 'captcha_fields'));
                add_action( 'woocommerce_lostpassword_form', array('WPCaptcha_Functions', 'captcha_fields'));
                add_action( 'woocommerce_resetpassword_form', array('WPCaptcha_Functions', 'captcha_fields'));
                add_action( 'woocommerce_lostpassword_form', array('WPCaptcha_Functions', 'login_print_scripts'));
                add_action( 'woocommerce_resetpassword_form', array('WPCaptcha_Functions', 'login_print_scripts'));
                add_action( 'lostpassword_post', array('WPCaptcha_Functions', 'process_lost_password_form'), 10, 1 );
                add_action( 'validate_password_reset', array('WPCaptcha_Functions', 'process_lost_password_form'), 10, 2 );
            }

            // Handle comment form captcha
            if($options['captcha_show_wp_comment']){
                add_filter( 'comment_form_after_fields', array('WPCaptcha_Functions', 'captcha_fields'));
                add_filter( 'comment_form_after_fields', array('WPCaptcha_Functions', 'login_print_scripts'));
                add_filter( 'preprocess_comment', array('WPCaptcha_Functions', 'process_comment_form'), 10, 1 );
            }

            // Handle woocommerce registration
            if($options['captcha_show_woo_registration']){
                add_filter( 'woocommerce_register_form', array('WPCaptcha_Functions', 'captcha_fields'));
                add_filter( 'woocommerce_register_form', array('WPCaptcha_Functions', 'login_print_scripts'));
                add_filter( 'woocommerce_process_registration_errors', array('WPCaptcha_Functions', 'check_woo_register_form_validation' ) );
            }

            // Handle woocommerce checkout
            if($options['captcha_show_woo_checkout']){
                add_action( 'woocommerce_review_order_before_submit', array('WPCaptcha_Functions', 'captcha_fields'));
                add_action( 'woocommerce_review_order_before_submit', array('WPCaptcha_Functions', 'login_print_scripts'));
                add_action( 'woocommerce_checkout_process', array('WPCaptcha_Functions', 'check_woo_checkout_form'));
            }

            // Handle Easy Digital Downloads registration
            if($options['captcha_show_edd_registration']){
                add_filter( 'edd_register_form_fields_before_submit', array('WPCaptcha_Functions', 'captcha_fields'));
                add_filter( 'edd_register_form_fields_before_submit', array('WPCaptcha_Functions', 'login_print_scripts'));
                add_action( 'edd_process_register_form', array('WPCaptcha_Functions', 'check_edd_register_form'));
            }

            // Handle BuddyPress registration
            if($options['captcha_show_bp_registration']){
                add_filter( 'bp_after_signup_profile_fields', array('WPCaptcha_Functions', 'captcha_fields'));
                add_filter( 'bp_after_signup_profile_fields', array('WPCaptcha_Functions', 'login_print_scripts'));
                add_action( 'bp_signup_validate', array('WPCaptcha_Functions', 'process_buddypress_signup_form'));
            }

            add_action('login_enqueue_scripts', array('WPCaptcha_Functions', 'login_enqueue_scripts' ));
            add_action('login_head', array('WPCaptcha_Functions', 'login_head' ), 9999);

            remove_filter('authenticate', 'wp_authenticate_username_password', 9999, 3);
            add_filter('authenticate', array('WPCaptcha_Functions', 'wp_authenticate_username_password'), 9999, 3);

            if($options['login_protection']){
                add_action('login_form', array('WPCaptcha_Functions', 'login_form_fields'));
                add_action('wp_login_failed', array('WPCaptcha_Functions', 'loginFailed' ), 10, 2);
                add_filter('login_errors', array('WPCaptcha_Functions', 'login_error_message' ));
            }
        } // if not admin
    } // init

    /**
     * Get plugin version
     *
     * @since 5.0
     *
     * @return int plugin version
     *
     */
    static function get_plugin_version()
    {
        $plugin_data = get_file_data(__FILE__, array('version' => 'Version'), 'plugin');
        self::$version = $plugin_data['version'];

        return $plugin_data['version'];
    } // get_plugin_version

    /**
     * Set plugin version and texdomain
     *
     * @since 5.0
     *
     * @return null
     */
    static function plugins_loaded()
    {
        self::get_plugin_version();
        load_plugin_textdomain('advanced-google-recaptcha');
    } // plugins_loaded

    static function run()
    {
        self::plugins_loaded();
        WPCaptcha_Setup::load_actions();
    }
} // class WPCaptcha


/**
 * Setup Hooks
 */
register_activation_hook(__FILE__, array('WPCaptcha_Setup', 'activate'));
register_deactivation_hook(__FILE__, array('WPCaptcha_Setup', 'deactivate'));
register_uninstall_hook(__FILE__, array('WPCaptcha_Setup', 'uninstall'));
add_action('plugins_loaded', array('wpcaptcha', 'run'), -9999);
add_action('init', array('wpcaptcha', 'init'), -1);