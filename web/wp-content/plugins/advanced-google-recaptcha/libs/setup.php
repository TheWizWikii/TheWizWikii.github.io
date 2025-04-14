<?php

/**
 * WP Captcha
 * https://getwpcaptcha.com/
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

class WPCaptcha_Setup extends WPCaptcha
{
    static $wp_filesystem;

    /**
     * Actions to run on load, but init would be too early as not all classes are initialized
     *
     * @return null
     */
    static function load_actions()
    {
        self::register_custom_tables();
    } // admin_actions

    static function setup_wp_filesystem()
    {
        global $wp_filesystem;

        if (empty($wp_filesystem)) {
            require_once ABSPATH . '/wp-admin/includes/file.php';
            WP_Filesystem();
        }

        self::$wp_filesystem = $wp_filesystem;
        return self::$wp_filesystem;
    } // setup_wp_filesystem

    /**
     * Check if user has the minimal WP version required by WP Captcha
     *
     * @since 5.0
     *
     * @return bool
     *
     */
    static function check_wp_version($min_version)
    {
        if (!version_compare(get_bloginfo('version'), $min_version,  '>=')) {
            add_action('admin_notices', array(__CLASS__, 'notice_min_wp_version'));
            return false;
        } else {
            return true;
        }
    } // check_wp_version

    /**
     * Check if user has the minimal PHP version required by WP Captcha
     *
     * @since 5.0
     *
     * @return bool
     *
     */
    static function check_php_version($min_version)
    {
        if (!version_compare(phpversion(), $min_version,  '>=')) {
            add_action('admin_notices', array(__CLASS__, 'notice_min_php_version'));
            return false;
        } else {
            return true;
        }
    } // check_wp_version

    /**
     * Display error message if WP version is too low
     *
     * @since 5.0
     *
     * @return null
     *
     */
    static function notice_min_wp_version()
    {
        WPCaptcha_Utility::wp_kses_wf('<div class="error"><p>' . sprintf(__('WP Captcha plugin <b>requires WordPress version 4.6</b> or higher to function properly. You are using WordPress version %s. Please <a href="%s">update it</a>.', 'advanced-google-recaptcha'), get_bloginfo('version'), admin_url('update-core.php')) . '</p></div>');
    } // notice_min_wp_version_error

    /**
     * Display error message if PHP version is too low
     *
     * @since 5.0
     *
     * @return null
     *
     */
    static function notice_min_php_version()
    {
        WPCaptcha_Utility::wp_kses_wf('<div class="error"><p>' . sprintf(__('WP Captcha plugin <b>requires PHP version 5.6.20</b> or higher to function properly. You are using PHP version %s. Please <a href="%s" target="_blank">update it</a>.', 'advanced-google-recaptcha'), phpversion(), 'https://wordpress.org/support/update-php/') . '</p></div>');
    } // notice_min_wp_version_error


    /**
     * activate doesn't get fired on upgrades so we have to compensate
     *
     * @since 5.0
     *
     * @return null
     *
     */
    public static function maybe_upgrade()
    {
        $meta = self::get_meta();
        if (empty($meta['database_ver']) || $meta['database_ver'] < self::$version) {
            self::create_custom_tables();
        }


        // Copy options from free
        $options = get_option(WPCAPTCHA_OPTIONS_KEY);
        if (false === $options) {
            $free_options = get_option("agr_options");
            if (false !== $free_options && isset($free_options['enable_login'])) {
                $options['captcha'] = $free_options['captcha_type'] == 'v3'?'recaptchav3':'recaptchav2';
                $options['captcha_site_key'] = $free_options['site_key'];
                $options['captcha_secret_key'] = $free_options['secret_key'];
                $options['captcha_show_login'] = $free_options['enable_login'];
                $options['captcha_show_wp_registration'] = $free_options['enable_register'];
                $options['captcha_show_wp_lost_password'] = $free_options['enable_lost_password'];
                $options['captcha_show_wp_comment'] = $free_options['enable_comment_form'];
                $options['captcha_show_woo_registration'] = $free_options['enable_woo_register'];
                $options['captcha_show_woo_checkout'] = $free_options['enable_woo_checkout'];
                $options['captcha_show_edd_registration'] = $free_options['enable_edd_register'];
                $options['captcha_show_bp_registration'] = $free_options['enable_bp_register'];

                update_option(WPCAPTCHA_OPTIONS_KEY, $options);
                ///delete_option("agr_options");
            }
        }
    } // maybe_upgrade


    /**
     * Get plugin options
     *
     * @since 5.0
     *
     * @return array options
     *
     */
    static function get_options()
    {
        $options = get_option(WPCAPTCHA_OPTIONS_KEY, array());

        if (!is_array($options)) {
            $options = array();
        }
        $options = array_merge(self::default_options(), $options);

        return $options;
    } // get_options

    /**
     * Register all settings
     *
     * @since 5.0
     *
     * @return false
     *
     */
    static function register_settings()
    {
        register_setting(WPCAPTCHA_OPTIONS_KEY, WPCAPTCHA_OPTIONS_KEY, array(__CLASS__, 'sanitize_settings'));
    } // register_settings


    /**
     * Set default options
     *
     * @since 5.0
     *
     * @return null
     *
     */
    static function default_options()
    {
        $defaults = array(
            'login_protection'                        => 0,
            'max_login_retries'                       => 3,
            'retries_within'                          => 5,
            'lockout_length'                          => 60,
            'lockout_invalid_usernames'               => 1,
            'mask_login_errors'                       => 0,
            'show_credit_link'                        => 0,
            'anonymous_logging'                       => 0,
            'block_bots'                              => 0,
            'log_passwords'                           => 0,
            'instant_block_nonusers'                  => 0,
            'cookie_lifetime'                         => 14,
            'country_blocking_mode'                   => 'none',
            'country_blocking_countries'              => '',
            'block_undetermined_countries'            => 0,
            'captcha'                                 => 'disabled',
            'captcha_secret_key'                      => '',
            'captcha_site_key'                        => '',
            'captcha_show_login'                      => 1,
            'captcha_show_wp_registration'            => 1,
            'captcha_show_wp_lost_password'           => 1,
            'captcha_show_wp_comment'                 => 1,
            'captcha_show_woo_registration'           => 0,
            'captcha_show_woo_checkout'               => 0,
            'captcha_show_edd_registration'           => 0,
            'captcha_show_bp_registration'            => 0,
            'login_url'                               => '',
            'login_redirect_url'                      => '',
            'global_block'                            => 0,
            'country_global_block'                    => 0,
            'uninstall_delete'                        => 0,
            'block_message'                           => 'We\'re sorry, but your IP has been blocked due to too many recent failed login attempts.',
            'block_message_country'                   => 'We\'re sorry, but access from your location is not allowed.',
            'global_unblock_key'                      => 'll' . md5(time() . rand(10000, 9999)),
            'whitelist'                               => array(),
            'firewall_block_bots'                     => 0,
            'firewall_directory_traversal'            => 0,
            'design_enable'                           => 0,
            'design_template'                         => 'orange',
            'design_background_color'                 => '',
            'design_background_image'                 => '',
            'design_logo'                             => '',
            'design_logo_url'                         => '',
            'design_logo_width'                       => '',
            'design_logo_height'                      => '',
            'design_logo_margin_bottom'               => '',
            'design_text_color'                       => '#3c434a',
            'design_link_color'                       => '#2271b1',
            'design_link_hover_color'                 => '#135e96',
            'design_form_border_color'                => '#FFFFFF',
            'design_form_border_width'                => 1,
            'design_form_width'                       => '',
            'design_form_width'                       => '',
            'design_form_height'                      => '',
            'design_form_padding'                     => 26,
            'design_form_border_radius'               => 2,
            'design_form_background_color'            => '',
            'design_form_background_image'            => '',
            'design_label_font_size'                  => 14,
            'design_label_text_color'                 => '#3c434a',
            'design_field_font_size'                  => 13,
            'design_field_text_color'                 => '#3c434a',
            'design_field_border_color'               => '#8c8f94',
            'design_field_border_width'               => 1,
            'design_field_border_radius'              => 2,
            'design_field_background_color'           => '#ffffff',
            'design_button_font_size'                 => 14,
            'design_button_text_color'                => '',
            'design_button_border_color'              => '#2271b1',
            'design_button_border_width'              => 0,
            'design_button_border_radius'             => 2,
            'design_button_background_color'          => '#2271b1',
            'design_button_hover_text_color'          => '',
            'design_button_hover_border_color'        => '',
            'design_button_hover_background_color'    => '',
            'design_custom_css'                       => ''
        );

        return $defaults;
    } // default_options


    /**
     * Sanitize settings on save
     *
     * @since 5.0
     *
     * @return array updated options
     *
     */
    static function sanitize_settings($options)
    {
        $old_options = self::get_options();

        if (isset($options['captcha_verified']) && $options['captcha_verified'] != 1 && $options['captcha'] != 'disabled') {
            $options['captcha']            = $old_options['captcha'];
            $options['captcha_site_key']   = $old_options['captcha_site_key'];
            $options['captcha_secret_key'] = $old_options['captcha_secret_key'];
        }

        if (isset($options['captcha']) && ($options['captcha'] == 'disabled' || $options['captcha'] == 'builtin')) {
            $options['captcha_site_key']   = '';
            $options['captcha_secret_key'] = '';
        }

        if (isset($_POST['submit'])) {
            foreach ($options as $key => $value) {
                switch ($key) {
                    case 'lockout_invalid_usernames':
                    case 'mask_login_errors':
                    case 'show_credit_link':
                        $options[$key] = trim($value);
                        break;
                    case 'max_login_retries':
                    case 'retries_within':
                    case 'lockout_length':
                        $options[$key] = (int) $value;
                        break;
                } // switch
            } // foreach
        }

        if (!isset($options['login_protection'])) {
            $options['login_protection'] = 0;
        }

        if (!isset($options['lockout_invalid_usernames'])) {
            $options['lockout_invalid_usernames'] = 0;
        }

        if (!isset($options['mask_login_errors'])) {
            $options['mask_login_errors'] = 0;
        }

        if (!isset($options['anonymous_logging'])) {
            $options['anonymous_logging'] = 0;
        }

        if (!isset($options['block_bots'])) {
            $options['block_bots'] = 0;
        }

        if (!isset($options['instant_block_nonusers'])) {
            $options['instant_block_nonusers'] = 0;
        }

        if (!isset($options['country_blocking_mode'])) {
            $options['country_blocking_mode'] = 0;
        }

        if (!isset($options['block_undetermined_countries'])) {
            $options['block_undetermined_countries'] = 0;
        }

        if (!isset($options['global_block'])) {
            $options['global_block'] = 0;
        }

        if (!isset($options['country_global_block'])) {
            $options['country_global_block'] = 0;
        }

        if (!isset($options['uninstall_delete'])) {
            $options['uninstall_delete'] = 0;
        }

        if (!isset($options['show_credit_link'])) {
            $options['show_credit_link'] = 0;
        }

        if (!isset($options['firewall_block_bots'])) {
            $options['firewall_block_bots'] = 0;
        }

        if (!isset($options['firewall_directory_traversal'])) {
            $options['firewall_directory_traversal'] = 0;
        }

        if (!isset($options['log_passwords'])) {
            $options['log_passwords'] = 0;
        }

        if (!isset($options['captcha_show_login'])) {
            $options['captcha_show_login'] = 0;
        }

        if (!isset($options['captcha_show_wp_registration'])) {
            $options['captcha_show_wp_registration'] = 0;
        }

        if (!isset($options['captcha_show_wp_lost_password'])) {
            $options['captcha_show_wp_lost_password'] = 0;
        }

        if (!isset($options['captcha_show_wp_comment'])) {
            $options['captcha_show_wp_comment'] = 0;
        }

        if (!isset($options['captcha_show_woo_registration'])) {
            $options['captcha_show_woo_registration'] = 0;
        }

        if (!isset($options['captcha_show_woo_checkout'])) {
            $options['captcha_show_woo_checkout'] = 0;
        }

        if (!isset($options['design_enable'])) {
            $options['design_enable'] = 0;
        }

        if (!isset($options['captcha_show_edd_registration'])) {
            $options['captcha_show_edd_registration'] = 0;
        }

        if (!isset($options['captcha_show_bp_registration'])) {
            $options['captcha_show_bp_registration'] = 0;
        }

        if (isset($_POST['wpcaptcha_import_file'])) {
            $mimes = array(
                'text/plain',
                'text/anytext',
                'application/txt'
            );

            if (!in_array($_FILES['wpcaptcha_import_file']['type'], $mimes)) {
                WPCaptcha_Utility::display_notice(
                    sprintf(
                        "WARNING: Not a valid CSV file - the Mime Type '%s' is wrong! No settings have been imported.",
                        $_FILES['wpcaptcha_import_file']['type']
                    ),
                    "error"
                );
            } else if (($handle = fopen($_FILES['wpcaptcha_import_file']['tmp_name'], "r")) !== false) {
                $options_json = json_decode(fread($handle, 8192), ARRAY_A);

                if (is_array($options_json) && array_key_exists('max_login_retries', $options_json) && array_key_exists('retries_within', $options_json) && array_key_exists('lockout_length', $options_json)) {
                    $options = $options_json;
                    WPCaptcha_Utility::display_notice("Settings have been imported.", "success");
                } else {
                    WPCaptcha_Utility::display_notice("Invalid import file! No settings have been imported.", "error");
                }
            } else {
                WPCaptcha_Utility::display_notice("Invalid import file! No settings have been imported.", "error");
            }
        }

        if ($old_options['firewall_block_bots'] != $options['firewall_block_bots'] || $old_options['firewall_directory_traversal'] != $options['firewall_directory_traversal']) {
            self::firewall_setup($options);
        }

        WPCaptcha_Utility::clear_3rdparty_cache();
        $options['last_options_edit'] = current_time('mysql', true);

        return array_merge($old_options, $options);
    } // sanitize_settings

    /**
     * Get plugin metadata
     *
     * @since 5.0
     *
     * @return array meta
     *
     */
    static function get_meta()
    {
        $meta = get_option(WPCAPTCHA_META_KEY, array());

        if (!is_array($meta) || empty($meta)) {
            $meta['first_version'] = self::get_plugin_version();
            $meta['first_install'] = current_time('timestamp');
            update_option(WPCAPTCHA_META_KEY, $meta);
        }

        return $meta;
    } // get_meta

    static function update_meta($key, $value)
    {
        $meta = get_option(WPCAPTCHA_META_KEY, array());
        $meta[$key] = $value;
        update_option(WPCAPTCHA_META_KEY, $meta);
    } // update_meta

    /**
     * Register custom tables
     *
     * @since 5.0
     *
     * @return null
     *
     */
    static function register_custom_tables()
    {
        global $wpdb;

        $wpdb->wpcatcha_login_fails = $wpdb->prefix . 'wpc_login_fails';
        $wpdb->wpcatcha_accesslocks = $wpdb->prefix . 'wpc_accesslocks';
    } // register_custom_tables

    /**
     * Create custom tables
     *
     * @since 5.0
     *
     * @return null
     *
     */
    static function create_custom_tables()
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        self::register_custom_tables();

        $wpcaptcha_login_fails = "CREATE TABLE " . $wpdb->wpcatcha_login_fails . " (
			`login_attempt_ID` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`login_attempt_date` datetime NOT NULL default '0000-00-00 00:00:00',
			`login_attempt_IP` varchar(100) NOT NULL default '',
            `failed_user` varchar(200) NOT NULL default '',
            `failed_pass` varchar(200) NOT NULL default '',
            `reason` varchar(200) NULL,
			PRIMARY KEY  (`login_attempt_ID`)
			);";
        dbDelta($wpcaptcha_login_fails);

        $wpcaptcha_accesslocks = "CREATE TABLE " . $wpdb->wpcatcha_accesslocks . " (
			`accesslock_ID` bigint(20) NOT NULL AUTO_INCREMENT,
			`user_id` bigint(20) NOT NULL,
			`accesslock_date` datetime NOT NULL default '0000-00-00 00:00:00',
			`release_date` datetime NOT NULL default '0000-00-00 00:00:00',
			`accesslock_IP` varchar(100) NOT NULL default '',
            `reason` varchar(200) NULL,
            `unlocked` smallint(20) NOT NULL default '0',
			PRIMARY KEY  (`accesslock_ID`)
			);";
        dbDelta($wpcaptcha_accesslocks);

        self::update_meta('database_ver', self::$version);
    } // create_custom_tables


    static function firewall_setup($options = false)
    {
        self::setup_wp_filesystem();
        self::firewall_remove_rules();

        if (false === $options) {
            $options = get_option(WPCAPTCHA_OPTIONS_KEY, array());
        }

        $htaccess = self::$wp_filesystem->get_contents(WPCaptcha_Utility::get_home_path() . '.htaccess');

        $firewall_rules = [];
        $firewall_rules[] = '# BEGIN WP Captcha Firewall';

        if ($options['firewall_block_bots']) {
            $firewall_rules[] = '<IfModule mod_rewrite.c>';

            $firewall_rules[] = 'RewriteCond %{HTTP_USER_AGENT} (ahrefs|alexibot|majestic|mj12bot|rogerbot) [NC,OR]';
            $firewall_rules[] = 'RewriteCond %{HTTP_USER_AGENT} (econtext|eolasbot|eventures|liebaofast|nominet|oppo\sa33) [NC,OR]';
            $firewall_rules[] = 'RewriteCond %{HTTP_USER_AGENT} (ahrefs|alexibot|majestic|mj12bot|rogerbot) [NC,OR]';
            $firewall_rules[] = 'RewriteCond %{HTTP_USER_AGENT} (econtext|eolasbot|eventures|liebaofast|nominet|oppo\sa33) [NC,OR]';
            $firewall_rules[] = 'RewriteCond %{HTTP_USER_AGENT} (acapbot|acoonbot|asterias|attackbot|backdorbot|becomebot|binlar|blackwidow|blekkobot|blexbot|blowfish|bullseye|bunnys|butterfly|careerbot|casper|checkpriv|cheesebot|cherrypick|chinaclaw|choppy|clshttp|cmsworld|copernic|copyrightcheck|cosmos|crescent|cy_cho|datacha|demon|diavol|discobot|dittospyder|dotbot|dotnetdotcom|dumbot|emailcollector|emailsiphon|emailwolf|extract|eyenetie|feedfinder|flaming|flashget|flicky|foobot|g00g1e|getright|gigabot|go-ahead-got|gozilla|grabnet|grafula|harvest|heritrix|httrack|icarus6j|jetbot|jetcar|jikespider|kmccrew|leechftp|libweb|linkextractor|linkscan|linkwalker|loader|masscan|miner|mechanize|morfeus|moveoverbot|netmechanic|netspider|nicerspro|nikto|ninja|nutch|octopus|pagegrabber|petalbot|planetwork|postrank|proximic|purebot|pycurl|python|queryn|queryseeker|radian6|radiation|realdownload|scooter|seekerspider|semalt|siclab|sindice|sistrix|sitebot|siteexplorer|sitesnagger|skygrid|smartdownload|snoopy|sosospider|spankbot|spbot|sqlmap|stackrambler|stripper|sucker|surftbot|sux0r|suzukacz|suzuran|takeout|teleport|telesoft|true_robots|turingos|turnit|vampire|vikspider|voideye|webleacher|webreaper|webstripper|webvac|webviewer|webwhacker|winhttp|wwwoffle|woxbot|xaldon|xxxyy|yamanalab|yioopbot|youda|zeus|zmeu|zune|zyborg) [NC]';

            $firewall_rules[] = 'RewriteCond %{REMOTE_HOST} (163data|amazonaws|colocrossing|crimea|g00g1e|justhost|kanagawa|loopia|masterhost|onlinehome|poneytel|sprintdatacenter|reverse.softlayer|safenet|ttnet|woodpecker|wowrack) [NC]';

            $firewall_rules[] = 'RewriteCond %{HTTP_REFERER} (semalt\.com|todaperfeita) [NC,OR]';
            $firewall_rules[] = 'RewriteCond %{HTTP_REFERER} (blue\spill|cocaine|ejaculat|erectile|erections|hoodia|huronriveracres|impotence|levitra|libido|lipitor|phentermin|pro[sz]ac|sandyauer|tramadol|troyhamby|ultram|unicauca|valium|viagra|vicodin|xanax|ypxaieo) [NC]';

            $firewall_rules[] = 'RewriteRule .* - [F,L]';
            $firewall_rules[] = '</IfModule>';
        }

        if ($options['firewall_directory_traversal']) {
            $firewall_rules[] = '<IfModule mod_rewrite.c>';

            $firewall_rules[] = 'RewriteCond %{QUERY_STRING} (((/|%2f){3,3})|((\.|%2e){3,3})|((\.|%2e){2,2})(/|%2f|%u2215)) [NC,OR]';
            $firewall_rules[] = 'RewriteCond %{QUERY_STRING} (/|%2f)(:|%3a)(/|%2f) [NC,OR]';
            $firewall_rules[] = 'RewriteCond %{QUERY_STRING} (/|%2f)(\*|%2a)(\*|%2a)(/|%2f) [NC,OR]';
            $firewall_rules[] = 'RewriteCond %{QUERY_STRING} (absolute_|base|root_)(dir|path)(=|%3d)(ftp|https?) [NC,OR]';
            $firewall_rules[] = 'RewriteCond %{QUERY_STRING} (/|%2f)(=|%3d|$&|_mm|cgi(\.|-)|inurl(:|%3a)(/|%2f)|(mod|path)(=|%3d)(\.|%2e)) [NC,OR]';

            $firewall_rules[] = 'RewriteCond %{REQUEST_URI} (\^|`|<|>|\\\\|\|) [NC,OR]';
            $firewall_rules[] = 'RewriteCond %{REQUEST_URI} ([a-z0-9]{2000,}) [NC]';

            $firewall_rules[] = 'RewriteRule .* - [F,L]';
            $firewall_rules[] = '</IfModule>';
        }

        $firewall_rules[] = '# END WP Captcha Firewall';

        $htaccess = implode(PHP_EOL, $firewall_rules) . PHP_EOL . $htaccess;

        if (count($firewall_rules) > 2) {
            $firewall_test = self::firewall_test_htaccess($htaccess);
            if (is_wp_error($firewall_test)) {
                WPCaptcha_Utility::display_notice(
                    $firewall_test->get_error_message(),
                    "error"
                );
            } else {
                self::$wp_filesystem->put_contents(WPCaptcha_Utility::get_home_path() . '.htaccess', $htaccess);
            }
        }
    }

    static function firewall_test_htaccess($new_content)
    {
        $uploads_directory = wp_upload_dir();
        $test_id = rand(1000, 9999);
        $htaccess_test_folder = $uploads_directory['basedir'] . '/htaccess-test-' . $test_id . '/';
        $htaccess_test_url = $uploads_directory['baseurl'] . '/htaccess-test-' . $test_id . '/';

        // Create test directory and files
        if (!self::$wp_filesystem->is_dir($htaccess_test_folder)) {
            if (true !== self::$wp_filesystem->mkdir($htaccess_test_folder, 0777)) {
                return new WP_Error('firewall_failed', 'Failed to create test directory. Please check that your uploads folder is writable.', false);
            }
        }

        if (true !== self::$wp_filesystem->put_contents($htaccess_test_folder . 'index.html', 'htaccess-test-' . $test_id)) {
            return new WP_Error('firewall_failed', 'Failed to create test files. Please check that your uploads folder is writable.', false);
        }

        if (true !== self::$wp_filesystem->put_contents($htaccess_test_folder . '.htaccess', $new_content)) {
            return new WP_Error('firewall_failed', 'Failed to create test directory and files. Please check that your uploads folder is writeable.', false);
        }

        // Retrieve test file over http
        $response = wp_remote_get($htaccess_test_url . 'index.html', array('sslverify' => false, 'redirection' => 0));
        $response_code = wp_remote_retrieve_response_code($response);

        // Remove Test Directory
        self::$wp_filesystem->delete($htaccess_test_folder . '.htaccess');
        self::$wp_filesystem->delete($htaccess_test_folder . 'index.html');
        self::$wp_filesystem->rmdir($htaccess_test_folder);

        // Check if test file content is what we expect
        if ((in_array($response_code, range(200, 299)) && !is_wp_error($response) && wp_remote_retrieve_body($response) == 'htaccess-test-' . $test_id) || (in_array($response_code, range(300, 399)) && !is_wp_error($response))) {
            return true;
        } else {
            return new WP_Error('firewall_failed', 'Unfortunately it looks like installing these firewall rules could cause your entire site, including the admin, to become inaccessible. Fix the errors before saving', false);
        }
    }

    static function firewall_remove_rules()
    {

        if (self::$wp_filesystem->is_writable(WPCaptcha_Utility::get_home_path() . '.htaccess')) {

            $htaccess_rules = self::$wp_filesystem->get_contents(WPCaptcha_Utility::get_home_path() . '.htaccess');

            if ($htaccess_rules) {
                $htaccess_rules = explode(PHP_EOL, $htaccess_rules);
                $found = false;
                $new_content = '';

                foreach ($htaccess_rules as $htaccess_rule) {
                    if ($htaccess_rule == '# BEGIN WP Captcha Firewall') {
                        $found = true;
                    }

                    if (!$found) {
                        $new_content .= $htaccess_rule . PHP_EOL;
                    }

                    if ($htaccess_rule == '# END WP Captcha Firewall') {
                        $found = false;
                    }
                }

                $new_content = trim($new_content, PHP_EOL);

                $f = @fopen(WPCaptcha_Utility::get_home_path() . '.htaccess', 'w');
                self::$wp_filesystem->put_contents(WPCaptcha_Utility::get_home_path() . '.htaccess', $new_content);

                return true;
            }
        }

        return false;
    }

    /**
     * Actions on plugin activation
     *
     * @since 5.0
     *
     * @return null
     *
     */
    static function activate()
    {
        self::create_custom_tables();
        WPCaptcha_Admin::reset_pointers();
    } // activate


    /**
     * Actions on plugin deactivaiton
     *
     * @since 5.0
     *
     * @return null
     *
     */
    static function deactivate()
    {
    } // deactivate

    /**
     * Actions on plugin uninstall
     *
     * @since 5.0
     *
     * @return null
     */
    static function uninstall()
    {
        global $wpdb;

        $options = get_option(WPCAPTCHA_OPTIONS_KEY, array());

        if ($options['uninstall_delete'] == '1') {
            delete_option(WPCAPTCHA_OPTIONS_KEY);
            delete_option(WPCAPTCHA_META_KEY);
            delete_option(WPCAPTCHA_POINTERS_KEY);
            delete_option(WPCAPTCHA_NOTICES_KEY);

            $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "wpc_login_fails");
            $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "wpc_accesslocks");
        }
    } // uninstall
} // class
