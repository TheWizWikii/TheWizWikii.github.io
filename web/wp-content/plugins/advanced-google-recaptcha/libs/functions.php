<?php

/**
 * WP Captcha
 * https://getwpcaptcha.com/
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

class WPCaptcha_Functions extends WPCaptcha
{
    static $wp_login_php;

    // auto download / install / activate WP 301 Redirects plugin
    static function install_wp301()
    {
        check_ajax_referer('install_wp301');

        if (false === current_user_can('administrator')) {
            wp_die('Sorry, you have to be an admin to run this action.');
        }

        $plugin_slug = 'eps-301-redirects/eps-301-redirects.php';
        $plugin_zip = 'https://downloads.wordpress.org/plugin/eps-301-redirects.latest-stable.zip';

        @include_once ABSPATH . 'wp-admin/includes/plugin.php';
        @include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        @include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        @include_once ABSPATH . 'wp-admin/includes/file.php';
        @include_once ABSPATH . 'wp-admin/includes/misc.php';
        echo '<style>
		body{
			font-family: sans-serif;
			font-size: 14px;
			line-height: 1.5;
			color: #444;
		}
		</style>';

        echo '<div style="margin: 20px; color:#444;">';
        echo 'If things are not done in a minute <a target="_parent" href="' . esc_url(admin_url('plugin-install.php?s=301%20redirects%20webfactory&tab=search&type=term')) . '">install the plugin manually via Plugins page</a><br><br>';
        echo 'Starting ...<br><br>';

        wp_cache_flush();
        $upgrader = new Plugin_Upgrader();
        echo 'Check if WP 301 Redirects is already installed ... <br />';
        if (self::is_plugin_installed($plugin_slug)) {
            echo 'WP 301 Redirects is already installed! <br /><br />Making sure it\'s the latest version.<br />';
            $upgrader->upgrade($plugin_slug);
            $installed = true;
        } else {
            echo 'Installing WP 301 Redirects.<br />';
            $installed = $upgrader->install($plugin_zip);
        }
        wp_cache_flush();

        if (!is_wp_error($installed) && $installed) {
            echo 'Activating WP 301 Redirects.<br />';
            $activate = activate_plugin($plugin_slug);

            if (is_null($activate)) {
                echo 'WP 301 Redirects Activated.<br />';

                echo '<script>setTimeout(function() { top.location = "' . esc_url(admin_url('options-general.php?page=eps_redirects')) . '"; }, 1000);</script>';
                echo '<br>If you are not redirected in a few seconds - <a href="' . esc_url(admin_url('options-general.php?page=eps_redirects')) . '" target="_parent">click here</a>.';
            }
        } else {
            echo 'Could not install WP 301 Redirects. You\'ll have to <a target="_parent" href="' . esc_url(admin_url('plugin-install.php?s=301%20redirects%20webfactory&tab=search&type=term')) . '">download and install manually</a>.';
        }

        echo '</div>';
    } // install_wp301


    /**
     * Check if given plugin is installed
     *
     * @param [string] $slug Plugin slug
     * @return boolean
     */
    static function is_plugin_installed($slug)
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();

        if (!empty($all_plugins[$slug])) {
            return true;
        } else {
            return false;
        }
    } // is_plugin_installed


    static function countFails($username = "")
    {
        global $wpdb;
        $options = WPCaptcha_Setup::get_options();
        $ip = WPCaptcha_Utility::getUserIP();

        $numFails = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(login_attempt_ID) FROM " . $wpdb->wpcatcha_login_fails . " WHERE login_attempt_date + INTERVAL %d MINUTE > %s AND login_attempt_IP = %s",
                array($options['retries_within'], current_time('mysql'), $ip)
            )
        );

        return $numFails;
    }

    static function incrementFails($username = "", $reason = "")
    {
        global $wpdb;
        $options = WPCaptcha_Setup::get_options();
        $ip = WPCaptcha_Utility::getUserIP();

        $username = sanitize_user($username);
        $user = get_user_by('login', $username);

        if ($user || 1 == $options['lockout_invalid_usernames']) {
            if ($user === false) {
                $user_id = -1;
            } else {
                $user_id = $user->ID;
            }

            $wpdb->insert(
                $wpdb->wpcatcha_login_fails,
                array(
                    'user_id' => $user_id,
                    'login_attempt_date' => current_time('mysql'),
                    'login_attempt_IP' => $ip,
                    'failed_user' => $username,
                    'reason' => $reason
                )
            );
        }
    }

    static function lockDown($username = "", $reason = "")
    {
        global $wpdb;
        $options = WPCaptcha_Setup::get_options();
        $ip = WPCaptcha_Utility::getUserIP();

        $username = sanitize_user($username);
        $user = get_user_by('login', $username);
        if ($user || 1 == $options['lockout_invalid_usernames']) {
            if ($user === false) {
                $user_id = -1;
            } else {
                $user_id = $user->ID;
            }

            $wpdb->insert(
                $wpdb->wpcatcha_accesslocks,
                array(
                    'user_id' => $user_id,
                    'accesslock_date' => current_time('mysql'),
                    'release_date' => date('Y-m-d H:i:s', strtotime(current_time('mysql')) + $options['lockout_length'] * 60),
                    'accesslock_IP' => $ip,
                    'reason' => $reason
                )
            );
        }
    }

    static function isLockedDown()
    {
        global $wpdb;
        $ip = WPCaptcha_Utility::getUserIP();

        $stillLocked = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM " . $wpdb->wpcatcha_accesslocks . " WHERE release_date > %s AND accesslock_IP = %s AND unlocked = 0", array(current_time('mysql'), $ip)));

        return $stillLocked;
    }

    static function is_rest_request()
    {
        if (defined('REST_REQUEST') && REST_REQUEST || isset($_GET['rest_route']) && strpos(sanitize_text_field(wp_unslash($_GET['rest_route'])), '/', 0) === 0) {
            return true;
        }

        global $wp_rewrite;
        if (null === $wp_rewrite) {
            $wp_rewrite = new WP_Rewrite();
        }

        $rest_url    = wp_parse_url(trailingslashit(rest_url()));
        $current_url = wp_parse_url(add_query_arg(array()));
        $is_rest = false;
        if(isset($current_url['path'])){
            $is_rest = strpos($current_url['path'], $rest_url['path'], 0) === 0;
        }

        return $is_rest;
    }

    static function wp_authenticate_username_password($user, $username, $password)
    {
        $options = WPCaptcha_Setup::get_options();

        if ($options['login_protection'] && self::isLockedDown()) {
            self::accesslock_screen($options['block_message']);
            return new WP_Error('wpcaptcha_fail_count', __("<strong>ERROR</strong>: We're sorry, but this IP has been blocked due to too many recent failed login attempts.<br /><br />Please try again later.", 'advanced-google-recaptcha'));
        }

        if (!$username) {
            return $user;
        }

        if (self::is_rest_request()) {
            return $user;
        }

        if ($options['captcha_show_login']) {
            $captcha = self::handle_captcha();
            if (is_wp_error($captcha)) {
                if ($options['max_login_retries'] <= self::countFails($username) && self::countFails($username) > 0) {
                    self::lockDown($username, 'Too many captcha fails');
                }
                return $captcha;
            }
        }

        $userdata = get_user_by('login', $username);
        if (false === $userdata) {
            $userdata = get_user_by('email', $username);
        }

        if ($options['login_protection'] && $options['max_login_retries'] <= self::countFails($username)) {
            if ($options['max_login_retries'] <= self::countFails($username) && self::countFails($username) > 0) {
                self::lockDown($username, 'Too many fails');
            }

            if (strlen($username) > 0 && $userdata === false && $options['instant_block_nonusers'] == '1' && self::countFails($username) > 0) {
                self::lockDown($username, 'Invalid Username');
            }

            return new WP_Error('wpcaptcha_fail_count', __("<strong>ERROR</strong>: We're sorry, but this IP has been blocked due to too many recent failed login attempts.<br /><br />Please try again later.", 'advanced-google-recaptcha'));
        }

        if (empty($username) || empty($password)) {
            $error = new WP_Error();

            if (empty($username))
                $error->add('empty_username', __('<strong>ERROR</strong>: The username field is empty.', 'advanced-google-recaptcha'));

            if (empty($password))
                $error->add('empty_password', __('<strong>ERROR</strong>: The password field is empty.', 'advanced-google-recaptcha'));

            return $error;
        }

        if ($userdata === false) {
            return new WP_Error('invalid_username', sprintf(__('<strong>ERROR</strong>: Invalid username. <a href="%s" title="Password Lost and Found">Lost your password</a>?', 'advanced-google-recaptcha'), site_url('wp-login.php?action=lostpassword', 'login')));
        }

        $userdata = apply_filters('wp_authenticate_user', $userdata, $password);

        if (is_wp_error($userdata)) {
            return $userdata;
        }

        if(0 !== intval($userdata->user_status)){
            return new WP_Error('incorrect_password', __('<strong>ERROR</strong>: Inactive account', 'advanced-google-recaptcha'));
        }

        if (!is_string($password) || !is_string($userdata->user_pass) || is_null($userdata->ID) || !wp_check_password($password, $userdata->user_pass, $userdata->ID)) {
            return new WP_Error('incorrect_password', sprintf(__('<strong>ERROR</strong>: Incorrect password. <a href="%s" title="Password Lost and Found">Lost your password</a>?', 'advanced-google-recaptcha'), site_url('wp-login.php?action=lostpassword', 'login')));
        }

        $user =  new WP_User($userdata->ID);
        return $user;
    }

    static function handle_captcha()
    {
        $options = WPCaptcha_Setup::get_options();
        if ($options['captcha'] == 'recaptchav2') {
            if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
                return new WP_Error('wpcaptcha_recaptchav2_not_submitted', __("<strong>ERROR</strong>: reCAPTCHA verification failed.<br /><br />Please try again.", 'advanced-google-recaptcha'));
            } else {
                $secret = $options['captcha_secret_key'];
                $response = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
                if(is_wp_error($response)){
                    return new WP_Error('wpcaptcha_recaptchav3_failed', __("<strong>ERROR</strong>: reCAPTCHA verification request failed<br /><br />" . $response->get_error_message(), 'advanced-google-recaptcha'));
                }
                $response = json_decode($response['body']);
                if ($response->success) {
                    return true;
                } else {
                    return new WP_Error('wpcaptcha_recaptchav2_failed', __("<strong>ERROR</strong>: reCAPTCHA verification failed.<br /><br />Please try again.", 'advanced-google-recaptcha'));
                }
            }
        } else if ($options['captcha'] == 'recaptchav3') {
            if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
                return new WP_Error('wpcaptcha_recaptchav3_not_submitted', __("<strong>ERROR</strong>: reCAPTCHA verification failed.<br /><br />Please try again.", 'advanced-google-recaptcha'));
            } else {
                
                $secret = $options['captcha_secret_key'];
                $response = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $_POST['g-recaptcha-response']);
                if(is_wp_error($response)){
                    return new WP_Error('wpcaptcha_recaptchav3_failed', __("<strong>ERROR</strong>: reCAPTCHA verification request failed<br /><br />" . $response->get_error_message(), 'advanced-google-recaptcha'));
                }
                $response = json_decode($response['body']);
                
                if ($response->success) {
                    return true;
                } else {
                    return new WP_Error('wpcaptcha_recaptchav3_failed', __("<strong>ERROR</strong>: reCAPTCHA verification failed.<br /><br />Please try again.", 'advanced-google-recaptcha'));
                }
            }
        } else if ($options['captcha'] == 'builtin') {
            if (isset($_POST['wpcaptcha_captcha'])) {
                foreach($_POST['wpcaptcha_captcha'] as $captcha_id => $captcha_val){
                    if($captcha_val === $_COOKIE['wpcaptcha_captcha_' . $captcha_id]){
                        return true;
                    } else {
                        return new WP_Error('wpcaptcha_builtin_captcha_failed', __("<strong>ERROR</strong>: captcha verification failed.<br /><br />Please try again.", 'advanced-google-recaptcha'));
                    }
                }
            } else {
                return new WP_Error('wpcaptcha_builtin_captcha_failed', __("<strong>ERROR</strong>: captcha verification failed.<br /><br />Please try again.", 'advanced-google-recaptcha'));
            }
        }

        return true;
    }

    static function handle_captcha_wp_registration($errors, $user_login, $user_email)
    {
        $captcha_check = self::handle_captcha();
        if ($captcha_check !== true) {
            $errors = $captcha_check;
        }

        return $errors;
    }

    static function process_lost_password_form($errors)
    {
        $captcha_check = self::handle_captcha();
        if ($captcha_check !== true) {
            $errors->add('captcha', $captcha_check->get_error_message());
        }
    }

    static function check_woo_register_form_validation($validation_error)
    {
        if (wp_doing_ajax()) {
            return $validation_error;
        }
        
        $captcha_check = self::handle_captcha();

        if ($captcha_check !== true) {
            if (isset($validation_error) && is_wp_error($validation_error)) {
                $validation_error->add('captcha', $captcha_check->get_error_message());
                return $validation_error;
            } else {
                wc_add_notice($captcha_check->get_error_message(), 'error');
                return $validation_error;
            }
        }

        return $validation_error;
    }

    static function check_woo_checkout_form()
    {
        $captcha_check = self::handle_captcha();
        if ($captcha_check !== true) {
            wc_add_notice($captcha_check->get_error_message(), 'error');
        }
    }

    static function check_edd_register_form()
    {
        $captcha_check = self::handle_captcha();
        if ($captcha_check !== true) {
            edd_set_error('captcha', $captcha_check->get_error_message());
        }
    }

    static function process_buddypress_signup_form()
    {
        $captcha_check = self::handle_captcha();
        if ($captcha_check !== true) {
            wp_die(
                '<p><strong>' . esc_html__('ERROR:', 'advanced-google-recaptcha') . '</strong> ' . esc_html(strip_tags($captcha_check->get_error_message())) . '</p>',
                'reCAPTCHA',
                array(
                    'response'  => 403,
                    'back_link' => 1,
                )
            );
        }
    }

    static function process_comment_form($commentdata)
    {
        // No need to check for loggedin user.
        if (absint($commentdata['user_ID']) > 0) {
            return $commentdata;
        }

        $captcha_check = self::handle_captcha();
        if ($captcha_check !== true) {
            wp_die(
                '<p><strong>' . esc_html__('ERROR:', 'advanced-google-recaptcha') . '</strong> ' . esc_html(strip_tags($captcha_check->get_error_message())) . '</p>',
                'reCAPTCHA',
                array(
                    'response'  => 403,
                    'back_link' => 1,
                )
            );
        }

        return $commentdata;
    }

    static function loginFailed($username, $error)
    {
        self::incrementFails($username, $error->get_error_code());
    }

    static function login_error_message($error)
    {
        $options = WPCaptcha_Setup::get_options();

        if ($options['mask_login_errors'] == 1) {
            $error = 'Login Failed';
        }
        return $error;
    }

    static function login_form_fields()
    {
        $options = WPCaptcha_Setup::get_options();
        $showcreditlink = $options['show_credit_link'];

        if ($showcreditlink != "no" && $showcreditlink != 0) {
            echo "<div id='wpcaptcha-protected-by' style='display: block; clear: both; padding-top: 20px; text-align: center;''>";
            esc_html_e('Login form protected by', 'advanced-google-recaptcha');
            echo " <a target='_blank' href='" . esc_url('https://getwpcaptcha.com/') . "'>WP Captcha</a></div>";
            echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                document.querySelector("#loginform").append(document.querySelector("#wpcaptcha-protected-by"));
            });
            </script>';
        }
    }

    static function captcha_fields()
    {
        $options = WPCaptcha_Setup::get_options();
        if ($options['captcha'] == 'recaptchav2') {
            echo '<div class="g-recaptcha" style="transform: scale(0.9); -webkit-transform: scale(0.9); transform-origin: 0 0; -webkit-transform-origin: 0 0;" data-sitekey="' . esc_html($options['captcha_site_key']) . '"></div>';

            echo '<script>
            jQuery("form.woocommerce-checkout").on("submit", function(){
                setTimeout(function(){
                    grecaptcha.reset();
                },100);
            });
            </script>';
        } else if ($options['captcha'] == 'recaptchav3') {
            echo '<input type="hidden" name="g-recaptcha-response" class="agr-recaptcha-response" value="" />';
            echo '<script>
        function wpcaptcha_captcha(){
            grecaptcha.execute("' . esc_html($options['captcha_site_key']) . '", {action: "submit"}).then(function(token) {
                var captchas = document.querySelectorAll(".agr-recaptcha-response");
                captchas.forEach(function(captcha) {
                    captcha.value = token;
                });
            });
        }

        jQuery("form.woocommerce-checkout").on("submit", function(){
            setTimeout(function(){
                wpcaptcha_captcha();
            },100);
        });
        </script>';
        } else if ($options['captcha'] == 'builtin') {
            echo '<p><label for="wpcaptcha_captcha">Are you human? Please solve: ';
            $captcha_id = rand(1000,9999);
            echo '<img class="wpcaptcha-captcha-img" style="vertical-align: text-top;" src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . 'libs/captcha.php?wpcaptcha-generate-image=true&color=' . esc_attr(urlencode('#FFFFFF')) . '&noise=1&id=' . intval($captcha_id) . '" alt="Captcha" />';
            echo '<input class="input" type="text" size="3" name="wpcaptcha_captcha[' . intval($captcha_id) . ']" id="wpcaptcha_captcha" />';
            echo '</label></p><br />';
        }
    }

    static function login_enqueue_scripts()
    {
        $options = WPCaptcha_Setup::get_options();
        if ($options['captcha'] == 'recaptchav2') {
            wp_enqueue_script('wpcaptcha-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), self::$version, true);
        } else if ($options['captcha'] == 'recaptchav3') {
            wp_enqueue_script('wpcaptcha-recaptcha', 'https://www.google.com/recaptcha/api.js?onload=wpcaptcha_captcha&render=' . esc_html($options['captcha_site_key']), array(), self::$version, true);
        }
    }

    static function login_print_scripts()
    {
        $options = WPCaptcha_Setup::get_options();

        if ($options['captcha'] == 'recaptchav2') {
            echo "<script src='https://www.google.com/recaptcha/api.js?ver=" . esc_attr(self::$version) . "' id='wpcaptcha-recaptcha-js'></script>";
        } else if ($options['captcha'] == 'recaptchav3') {
            echo "<script src='https://www.google.com/recaptcha/api.js?onload=wpcaptcha_captcha&render=" . esc_html($options['captcha_site_key']) . "&ver=" . esc_attr(self::$version) . "' id='wpcaptcha-recaptcha-js'></script>";
        }
    }

    static function accesslock_screen($block_message = false)
    {
        $main_color = '#4285f4';
        $secondary_color = '#8eb8ff';

        echo '<style>
            @import url(\'https://fonts.bunny.net/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;1,400;1,500;1,700&display=swap\');

            #wpcaptcha_accesslock_screen_wrapper{
                font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
                width:100%;
                height:100%;
                position:fixed;
                top:0;
                left:0;
                z-index: 999999;
                font-size: 14px;
                color: #333;
                line-height: 1.4;
                background-image: linear-gradient(45deg, ' . esc_attr($main_color) . ' 25%, ' . esc_attr($secondary_color) . ' 25%, ' . esc_attr($secondary_color) . ' 50%, ' . esc_attr($main_color) . ' 50%, ' . esc_attr($main_color) . ' 75%, ' . esc_attr($secondary_color) . ' 75%, ' . esc_attr($secondary_color) . ' 100%);
                background-size: 28.28px 28.28px;
            }

            #wpcaptcha_accesslock_screen_wrapper form{
                max-width: 300px;
                top:50%;
                left:50%;
                margin-top:-200px;
                margin-left:-200px;
                border: none;
                background: #ffffffde;
                box-shadow: 0 1px 3px rgb(0 0 0 / 4%);
                position: fixed;
                text-align:center;
                background: #fffffff2;
                padding: 20px;
                -webkit-box-shadow: 5px 5px 0px 1px rgba(0,0,0,0.22);
                box-shadow: 5px 5px 0px 1px rgba(0,0,0,0.22);
            }

            #wpcaptcha_accesslock_screen_wrapper p{
                padding: 10px;
                line-height:1.5;
            }

            #wpcaptcha_accesslock_screen_wrapper p.error{
                background: #f11c1c;
                color: #FFF;
                font-weight: 500;
            }

            #wpcaptcha_accesslock_screen_wrapper form input[type="text"]{
                padding: 4px 10px;
                border-radius: 2px;
                border: 1px solid #c3c4c7;
                font-size: 16px;
                line-height: 1.33333333;
                margin: 0 6px 16px 0;
                min-height: 40px;
                max-height: none;
                width: 100%;
            }

            #wpcaptcha_accesslock_screen_wrapper form input[type="submit"]{
                padding: 10px 10px;
                border-radius: 2px;
                border: none;
                font-size: 16px;
                background: ' . esc_attr($main_color) . ';
                color: #FFF;
                cursor: pointer;
                width: 100%;
            }

            #wpcaptcha_accesslock_screen_wrapper form input[type="submit"]:hover{
                background: ' . esc_attr($secondary_color) . ';
            }
        </style>

        <script>
        document.title = "' . esc_html(get_bloginfo('name')) . '";
        </script>';
        echo '<div id="wpcaptcha_accesslock_screen_wrapper">';

        echo '<form method="POST">';

        if (isset($_POST['wpcaptcha_recovery_submit']) && wp_verify_nonce($_POST['wpcaptcha_recovery_nonce'], 'wpcaptcha_recovery')) {
            if (!filter_var($_POST['wpcaptcha_recovery_email'], FILTER_VALIDATE_EMAIL)) {
                $display_message = '<p class="error">Invalid email address.</p>';
            } else {
                $user = get_user_by('email', sanitize_text_field($_POST['wpcaptcha_recovery_email']));
                if (user_can($user, 'administrator')) {
                    $unblock_key = md5(time() . rand(10000, 9999));
                    $unblock_attempts = get_transient('wpcaptcha_unlock_count_' . $user->ID);
                    if (!$unblock_attempts) {
                        $unblock_attempts = 0;
                    }

                    $unblock_attempts++;
                    set_transient('wpcaptcha_unlock_count_' . $user->ID, $unblock_attempts, HOUR_IN_SECONDS);

                    if ($unblock_attempts <= 3) {
                        set_transient('wpcaptcha_unlock_' . $unblock_key, $unblock_key, HOUR_IN_SECONDS);

                        $unblock_url = add_query_arg(array('wpcaptcha_unblock' => $unblock_key), wp_login_url());

                        $subject  = 'WP Captcha unblock instructions for ' . site_url();
                        $message  = '<p>The IP ' . WPCaptcha_Utility::getUserIP() . ' has been locked down and someone submitted an unblock request using your email address <strong>' . $_POST['wpcaptcha_recovery_email'] . '</strong></p>';
                        $message .= '<p>If this was you, and you have locked yourself out please click <a target="_blank" href="' . $unblock_url . '">this link</a> which is valid for 1 hour.</p>';
                        $message .= '<p>Please note that for security reasons, this will only unblock the IP of the person opening the link, not the IP of the person who submitted the unblock request. To unblock someone else please do so on the <a href="' . admin_url('options-general.php?page=wpcaptcha#wpcaptcha_activity') . '">WP Captcha Activity Page</p>';

                        add_filter('wp_mail_content_type', function () {
                            return "text/html";
                        });

                        wp_mail($user->user_email, $subject, $message);
                    }
                } else {
                    //If no admin using the submitted email exists, ignore silently
                }

                if (isset($unblock_attempts) && $unblock_attempts > 3) {
                    $display_message = '<p class="error">You have already attempted to unblock yourself recently, please wait 1 hour before trying again.</p>';
                } else {
                    $display_message = '<p>If an administrator having the email address <strong>' . $_POST['wpcaptcha_recovery_email'] . '</strong> exists, an email has been sent with instructions to regain access.</p>';
                }
            }
        }

        echo '<img src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . 'images/wp-captcha-logo.png" alt="WP Captcha" height="60" title="WP Captcha">';

        echo '<br />';
        echo '<br />';
        if ($block_message !== false) {
            echo '<p class="error">' . esc_html($block_message) . '</p>';
        } else {
            echo '<p class="error">We\'re sorry, but your IP has been blocked due to too many recent failed login attempts.</p>';
        }
        if (!empty($display_message)) {
            WPCaptcha_Utility::wp_kses_wf($display_message);
        }
        echo '<p>If you are a user with administrative privilege please enter your email below to receive instructions on how to unblock yourself.</p>';
        echo '<input type="text" name="wpcaptcha_recovery_email" value="" placeholder="" />';
        echo '<input type="submit" name="wpcaptcha_recovery_submit" value="Send unblock email" placeholder="" />';
        wp_nonce_field('wpcaptcha_recovery', 'wpcaptcha_recovery_nonce');


        echo '</form>';
        echo '</div>';

        exit();
    }

    static function handle_unblock()
    {
        global $wpdb;
        $options = WPCaptcha_Setup::get_options();
        if (isset($_GET['wpcaptcha_unblock']) && $options['global_unblock_key'] === $_GET['wpcaptcha_unblock']) {
            $user_ip = WPCaptcha_Utility::getUserIP();
            if (!in_array($user_ip, $options['whitelist'])) {
                $options['whitelist'][] = WPCaptcha_Utility::getUserIP();
            }
            update_option(WPCAPTCHA_OPTIONS_KEY, $options);
        }

        if (isset($_GET['wpcaptcha_unblock']) && strlen($_GET['wpcaptcha_unblock']) == 32) {
            $unblock_key = sanitize_key($_GET['wpcaptcha_unblock']);
            $unblock_transient = get_transient('wpcaptcha_unlock_' . $unblock_key);
            if ($unblock_transient == $unblock_key) {
                $user_ip = WPCaptcha_Utility::getUserIP();
                $wpdb->delete(
                    $wpdb->wpcatcha_accesslocks,
                    array(
                        'accesslock_IP' => $user_ip
                    )
                );

                if (!in_array($user_ip, $options['whitelist'])) {
                    $options['whitelist'][] = WPCaptcha_Utility::getUserIP();
                }

                update_option(WPCAPTCHA_OPTIONS_KEY, $options);
            }
        }
    }

    static function wp_template_loader()
    {
        global $pagenow;
        $pagenow = 'index.php';

        if (!defined('WP_USE_THEMES')) {
            define('WP_USE_THEMES', true);
        }

        wp();

        require_once(ABSPATH . WPINC . '/template-loader.php');
        die();
    }

    public static function pretty_fail_errors($error_code)
    {
        switch ($error_code) {
            case 'wpcaptcha_location_blocked':
                return 'Blocked Location';
                break;
            case 'wpcaptcha_fail_count':
                return 'User exceeded maximum number of fails';
                break;
            case 'wpcaptcha_bot':
                return 'Bot';
                break;
            case 'empty_username':
                return 'Empty Username';
                break;
            case 'empty_password':
                return 'Empty Password';
                break;
            case 'incorrect_password':
                return 'Incorrect Password';
                break;
            case 'invalid_username':
                return 'Invalid Username';
                break;
            case 'wpcaptcha_recaptchav2_not_submitted':
                return 'reCAPTCHA v2 not submitted';
                break;
            case 'wpcaptcha_recaptchav3_not_submitted':
                return 'reCAPTCHA v3 not submitted';
                break;
            case 'wpcaptcha_recaptchav2_failed':
                return 'reCAPTCHA v2 failed verification';
                break;
            case 'wpcaptcha_recaptchav3_not_submitted':
                return 'reCAPTCHA v3 failed verification';
                break;
            case 'wpcaptcha_builtin_captcha_failed':
                return 'Built-in captcha failed verification';
                break;
            case 'wpcaptcha_hcaptcha_failed':
                return 'hCaptcha failed verification';
                break;
            case 'wpcaptcha_icons_captcha_failed':
                return 'Icon captcha failed verification';
            default:
                return 'Unknown';
                break;
        }
    }

    static function login_head()
    {
        $options = WPCaptcha_Setup::get_options();

        if ($options['design_enable']) {
            echo '<style type="text/css">';

            add_filter('login_headerurl', function ($url) {
                $options = WPCaptcha_Setup::get_options();
                if (!empty($options['design_logo_url'])) {
                    return $options['design_logo_url'];
                }
                return $url;
            });


            if (!empty($options['design_logo'])) {
                echo '#login h1 a, .login h1 a {';
                echo 'filter: brightness(0) invert(1);';
                echo '}';
            }

            if (!empty($options['design_background_color'])) {
                echo 'body.login {background-color:' . esc_attr($options['design_background_color']) . '}';
            }

            if (!empty($options['design_background_image'])) {
                echo 'body.login {background-image:url(' . esc_attr($options['design_background_image']) . '); background-size:cover;}';
            }

            echo 'body.login div#login form#loginform {';
            if (!empty($options['design_form_width'])) {
                echo 'width:' . (int)$options['design_form_width'] . 'px;';
            }

            if (!empty($options['design_form_height'])) {
                echo 'height:' . (int)$options['design_form_height'] . 'px;';
            }

            if (!empty($options['design_form_padding'])) {
                echo 'padding:' . (int)$options['design_form_padding'] . 'px;';
            }

            if (!empty($options['design_form_border_radius'])) {
                echo 'border-radius:' . (int)$options['design_form_border_radius'] . 'px;';
            }

            if (!is_null($options['design_form_border_width'])) {
                echo 'border-width:' . (int)$options['design_form_border_width'] . 'px;';
            }

            if (!empty($options['design_form_border_color'])) {
                echo 'border-color:' . esc_attr($options['design_form_border_color']) . ';';
            }

            if (!empty($options['design_form_background_color'])) {
                echo 'background-color:' . esc_attr($options['design_form_background_color']) . ';';
            }

            if (!empty($options['design_form_background_image'])) {
                echo 'background-image:url(' . esc_url($options['design_form_background_image']) . '); background-size:cover;';
            }
            echo '}';

            echo 'body.login div#login form#loginform label {';
            if (!empty($options['design_label_font_size'])) {
                echo 'font-size:' . (int)$options['design_label_font_size'] . 'px;';
            }

            if (!empty($options['design_label_text_color'])) {
                echo 'color:' . esc_attr($options['design_label_text_color']) . ';';
            }
            echo '}';

            echo 'body.login div#login form#loginform input {';
            if (!empty($options['design_field_font_size'])) {
                echo 'font-size:' . (int)$options['design_field_font_size'] . 'px;';
            }

            if (!empty($options['design_field_text_color'])) {
                echo 'color:' . esc_attr($options['design_field_text_color']) . ';';
            }

            if (!empty($options['design_field_border_color'])) {
                echo 'border-color:' . esc_attr($options['design_field_border_color']) . ';';
            }

            if (!is_null($options['design_field_border_width'])) {
                echo 'border-width:' . (int)$options['design_field_border_width'] . 'px;';
            }

            if (!empty($options['design_field_border_radius'])) {
                echo 'border-radius:' . (int)$options['design_field_border_radius'] . 'px;';
            }

            if (!empty($options['design_field_background_color'])) {
                echo 'background-color:' . esc_attr($options['design_field_background_color']) . ';';
            }
            echo '}';

            echo 'body.login div#login form#loginform p.submit input#wp-submit {';
            if (!empty($options['design_button_font_size'])) {
                echo 'font-size:' . (int)$options['design_button_font_size'] . 'px;';
            }

            if (!empty($options['design_button_text_color'])) {
                echo 'color:' . esc_attr($options['design_button_text_color']) . ';';
            }

            if (!empty($options['design_button_border_color'])) {
                echo 'border-color:' . esc_attr($options['design_button_border_color']) . ';';
            }

            if (!is_null($options['design_button_border_width'])) {
                echo 'border-width:' . (int)$options['design_button_border_width'] . 'px;';
            }

            if (!empty($options['design_button_border_radius'])) {
                echo 'border-radius:' . (int)$options['design_button_border_radius'] . 'px;';
            }

            if (!empty($options['design_button_background_color'])) {
                echo 'background-color:' . esc_attr($options['design_button_background_color']) . ';';
            }
            echo '}';

            echo 'body.login div#login form#loginform{';
            if (!empty($options['design_text_color'])) {
                echo 'color:' . esc_attr($options['design_text_color']) . ';';
            }
            echo '}';

            echo 'body.login a, body.login #nav a, body.login #backtoblog a, body.login div#login form#loginform a{';
            if (!empty($options['design_link_color'])) {
                echo 'color:' . esc_attr($options['design_link_color']) . ';';
            }
            echo '}';

            echo 'body.login a:hover, body.login #nav a:hover, body.login #backtoblog a:hover, body.login div#login form#loginform a:hover{';
            if (!empty($options['design_link_hover_color'])) {
                echo 'color:' . esc_attr($options['design_link_hover_color']) . ';';
            }
            echo '}';

            echo 'body.login div#login form#loginform p.submit input#wp-submit:hover {';
            if (!empty($options['design_button_hover_text_color'])) {
                echo 'color:' . esc_attr($options['design_button_hover_text_color']) . ';';
            }

            if (!empty($options['design_button_hover_border_color'])) {
                echo 'border-color:' . esc_attr($options['design_button_hover_border_color']) . ';';
            }

            if (!empty($options['design_button_hover_background_color'])) {
                echo 'background-color:' . esc_attr($options['design_button_hover_background_color']) . ';';
            }
            echo '}';

            echo '.wp-core-ui .button .dashicons, .wp-core-ui .button-secondary .dashicons{';
            if (!empty($options['design_link_color'])) {
                echo 'color:' . esc_attr($options['design_link_color']) . ';';
            }
            echo '}';

            echo '.wp-core-ui .button .dashicons:hover, .wp-core-ui .button-secondary .dashicons:hover{';
            if (!empty($options['design_link_hover_color'])) {
                echo 'color:' . esc_attr($options['design_link_hover_color']) . ';';
            }
            echo '}';


            if (!empty($options['design_custom_css'])) {
                echo esc_html($options['design_custom_css']);
            }

            echo '</style>';
        }
    }

    static function get_templates()
    {
        $templates = array();

        $templates['white'] = array(
            'design_background_color' => '#FFFFFF',
            'design_background_image' => '',
            'design_logo' => 'white-wpcaptcha-icon',
            'design_logo_width' => '100',
            'design_logo_height' => '100',
            'design_logo_margin_bottom' => '30',
            'design_text_color' => '#300000',
            'design_link_color' => '#06a8e8',
            'design_link_hover_color' => '#005b93',
            'design_form_border_color' => '#cbcbcb',
            'design_form_border_width' => '1',
            'design_form_width' => '',
            'design_form_height' => '',
            'design_form_padding' => '20',
            'design_form_border_radius' => '4',
            'design_form_background_color' => '#ffffff',
            'design_form_background_image' => '',
            'design_label_font_size' => '14',
            'design_label_text_color' => '#383838',
            'design_field_font_size' => '14',
            'design_field_text_color' => '#222222',
            'design_field_border_color' => '#d1d1d1',
            'design_field_border_width' => '1',
            'design_field_border_radius' => '2',
            'design_field_background_color' => '#ffffff',
            'design_button_font_size' => '14',
            'design_button_text_color' => '#ffffff',
            'design_button_border_color' => '#000000',
            'design_button_border_width' => '0',
            'design_button_border_radius' => '4',
            'design_button_background_color' => '#595959',
            'design_button_hover_text_color' => '#ffffff',
            'design_button_hover_border_color' => '#ffffff',
            'design_button_hover_background_color' => '#878787',
            'design_custom_css' => ''
        );

        $templates['orange'] = array(
            'design_background_color' => '#ef9b00',
            'design_background_image' => '',
            'design_logo' => 'white-wpcaptcha-icon',
            'design_logo_width' => '100',
            'design_logo_height' => '100',
            'design_logo_margin_bottom' => '30',
            'design_text_color' => '#4c3d00',
            'design_link_color' => '#7c6e13',
            'design_link_hover_color' => '#896709',
            'design_form_border_color' => '#725f00',
            'design_form_border_width' => '0',
            'design_form_width' => '',
            'design_form_height' => '',
            'design_form_padding' => '20',
            'design_form_border_radius' => '4',
            'design_form_background_color' => '#f9e7ac',
            'design_form_background_image' => '',
            'design_label_font_size' => '14',
            'design_label_text_color' => '#634000',
            'design_field_font_size' => '14',
            'design_field_text_color' => '#222222',
            'design_field_border_color' => '#634000',
            'design_field_border_width' => '1',
            'design_field_border_radius' => '2',
            'design_field_background_color' => '#ffffff',
            'design_button_font_size' => '14',
            'design_button_text_color' => '#ffffff',
            'design_button_border_color' => '#634000',
            'design_button_border_width' => '1',
            'design_button_border_radius' => '4',
            'design_button_background_color' => '#634000',
            'design_button_hover_text_color' => '#ffffff',
            'design_button_hover_border_color' => '#8c5f00',
            'design_button_hover_background_color' => '#8c5f00',
            'design_custom_css' => ''
        );

        $templates['red'] = array(
            'design_background_color' => '#ce0000',
            'design_background_image' => '',
            'design_logo' => 'white-wpcaptcha-icon',
            'design_logo_width' => '100',
            'design_logo_height' => '100',
            'design_logo_margin_bottom' => '30',
            'design_text_color' => '#300000',
            'design_link_color' => '#c91e1e',
            'design_link_hover_color' => '#d15959',
            'design_form_border_color' => '#c90000',
            'design_form_border_width' => '2',
            'design_form_width' => '',
            'design_form_height' => '',
            'design_form_padding' => '20',
            'design_form_border_radius' => '4',
            'design_form_background_color' => '#ffffff',
            'design_form_background_image' => '',
            'design_label_font_size' => '14',
            'design_label_text_color' => '#383838',
            'design_field_font_size' => '14',
            'design_field_text_color' => '#222222',
            'design_field_border_color' => '#d1d1d1',
            'design_field_border_width' => '1',
            'design_field_border_radius' => '2',
            'design_field_background_color' => '#ffffff',
            'design_button_font_size' => '14',
            'design_button_text_color' => '#ffffff',
            'design_button_border_color' => '#000000',
            'design_button_border_width' => '0',
            'design_button_border_radius' => '4',
            'design_button_background_color' => '#d30000',
            'design_button_hover_text_color' => '#ffffff',
            'design_button_hover_border_color' => '#ffffff',
            'design_button_hover_background_color' => '#9e0000',
            'design_custom_css' => ''
        );

        $templates['green'] = array(
            'design_background_color' => '#2c6600',
            'design_background_image' => '',
            'design_logo' => 'white-icon.png',
            'design_logo_width' => '100',
            'design_logo_height' => '100',
            'design_logo_margin_bottom' => '30',
            'design_text_color' => '#c6e500',
            'design_link_color' => '#c6e500',
            'design_link_hover_color' => '#acbf00',
            'design_form_border_color' => '#c6e500',
            'design_form_border_width' => '2',
            'design_form_width' => '',
            'design_form_height' => '',
            'design_form_padding' => '20',
            'design_form_border_radius' => '4',
            'design_form_background_color' => '#4b7c01',
            'design_form_background_image' => '',
            'design_label_font_size' => '14',
            'design_label_text_color' => '#ffffff',
            'design_field_font_size' => '14',
            'design_field_text_color' => '#222222',
            'design_field_border_color' => '#87d642',
            'design_field_border_width' => '1',
            'design_field_border_radius' => '2',
            'design_field_background_color' => '#3c7f02',
            'design_button_font_size' => '14',
            'design_button_text_color' => '#ffffff',
            'design_button_border_color' => '#000000',
            'design_button_border_width' => '0',
            'design_button_border_radius' => '4',
            'design_button_background_color' => '#66b500',
            'design_button_hover_text_color' => '#ffffff',
            'design_button_hover_border_color' => '#ffffff',
            'design_button_hover_background_color' => '#a6d800',
            'design_custom_css' => ''
        );

        $templates['blue'] = array(
            'design_background_color' => '#005cb2',
            'design_background_image' => '',
            'design_logo' => 'white-icon.png',
            'design_logo_width' => '100',
            'design_logo_height' => '100',
            'design_logo_margin_bottom' => '30',
            'design_text_color' => '#300000',
            'design_link_color' => '#2ca8ea',
            'design_link_hover_color' => '#005b93',
            'design_form_border_color' => '#008ed1',
            'design_form_border_width' => '2',
            'design_form_width' => '',
            'design_form_height' => '',
            'design_form_padding' => '20',
            'design_form_border_radius' => '4',
            'design_form_background_color' => '#ffffff',
            'design_form_background_image' => '',
            'design_label_font_size' => '14',
            'design_label_text_color' => '#383838',
            'design_field_font_size' => '14',
            'design_field_text_color' => '#222222',
            'design_field_border_color' => '#d1d1d1',
            'design_field_border_width' => '1',
            'design_field_border_radius' => '2',
            'design_field_background_color' => '#ffffff',
            'design_button_font_size' => '14',
            'design_button_text_color' => '#ffffff',
            'design_button_border_color' => '#000000',
            'design_button_border_width' => '0',
            'design_button_border_radius' => '4',
            'design_button_background_color' => '#0084cc',
            'design_button_hover_text_color' => '#ffffff',
            'design_button_hover_border_color' => '#ffffff',
            'design_button_hover_background_color' => '#005796',
            'design_custom_css' => ''
        );

        $templates['gray'] = array(
            'design_background_color' => '#353535',
            'design_background_image' => '',
            'design_logo' => 'white-icon.png',
            'design_logo_width' => '100',
            'design_logo_height' => '100',
            'design_logo_margin_bottom' => '30',
            'design_text_color' => '#300000',
            'design_link_color' => '#06a8e8',
            'design_link_hover_color' => '#005b93',
            'design_form_border_color' => '#474747',
            'design_form_border_width' => '2',
            'design_form_width' => '',
            'design_form_height' => '',
            'design_form_padding' => '20',
            'design_form_border_radius' => '4',
            'design_form_background_color' => '#ffffff',
            'design_form_background_image' => '',
            'design_label_font_size' => '14',
            'design_label_text_color' => '#383838',
            'design_field_font_size' => '14',
            'design_field_text_color' => '#222222',
            'design_field_border_color' => '#d1d1d1',
            'design_field_border_width' => '1',
            'design_field_border_radius' => '2',
            'design_field_background_color' => '#ffffff',
            'design_button_font_size' => '14',
            'design_button_text_color' => '#ffffff',
            'design_button_border_color' => '#000000',
            'design_button_border_width' => '0',
            'design_button_border_radius' => '4',
            'design_button_background_color' => '#595959',
            'design_button_hover_text_color' => '#ffffff',
            'design_button_hover_border_color' => '#ffffff',
            'design_button_hover_background_color' => '#878787',
            'design_custom_css' => ''
        );

        return $templates;
    }

    static function install_template()
    {
        check_admin_referer('wpcaptcha_install_template');
        $options = WPCaptcha_Setup::get_options();

        $template = $_GET['template'];
        $templates = self::get_templates();

        if (array_key_exists($template, $templates)) {
            $options = array_merge($options, $templates[$template]);
            if ($options['design_logo'] == 'white-wpcaptcha-icon') {
                $options['design_logo'] = WPCAPTCHA_PLUGIN_URL . 'images/white-icon.png';
            }

            $options['design_template'] = $template;
            $options['design_enable'] = 1;
            update_option(WPCAPTCHA_OPTIONS_KEY, $options);
            WPCaptcha_Admin::add_notice('template_activated', __('Template activated.', 'advanced-google-recaptcha'), 'success', true);
        } else {
            WPCaptcha_Admin::add_notice('template_not_found', __('Unknown template ID.', 'advanced-google-recaptcha'), 'error', true);
        }

        if (!empty($_GET['redirect'])) {
            wp_safe_redirect($_GET['redirect']);
        }
    }
} // class
