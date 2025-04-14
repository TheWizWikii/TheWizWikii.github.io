<?php
/**
 * WP Captcha
 * https://getwpcaptcha.com/
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

class WPCaptcha_Tab_Login_Form extends WPCaptcha
{
    static function display()
    {
        $tabs[] = array('id' => 'tab_login_basic', 'class' => 'tab-content', 'label' => __('Basic', 'advanced-google-recaptcha'), 'callback' => array(__CLASS__, 'tab_basic'));
        $tabs[] = array('id' => 'tab_login_advanced', 'class' => 'tab-content', 'label' => __('Advanced', 'advanced-google-recaptcha'), 'callback' => array(__CLASS__, 'tab_advanced'));
        $tabs[] = array('id' => 'tab_login_tools', 'class' => 'tab-content', 'label' => __('Tools', 'advanced-google-recaptcha'), 'callback' => array(__CLASS__, 'tab_tools'));

        echo '<div id="tabs_log" class="ui-tabs wpcaptcha-tabs-2nd-level">';
        echo '<ul>';
        foreach ($tabs as $tab) {
            echo '<li><a href="#' . esc_attr($tab['id']) . '">' . esc_html($tab['label']) . '</a></li>';
        }
        echo '</ul>';

        foreach ($tabs as $tab) {
            if (is_callable($tab['callback'])) {
                echo '<div style="display: none;" id="' . esc_attr($tab['id']) . '" class="' . esc_attr($tab['class']) . '">';
                call_user_func($tab['callback']);
                echo '</div>';
            }
        } // foreach

        echo '</div>'; // second level of tabs


    } // display

    static function tab_basic()
    {
        $options = WPCaptcha_Setup::get_options();

        echo '<table class="form-table"><tbody>';

        echo '<tr valign="top">
        <th scope="row"><label for="login_protection">Login Protection</label></th>
        <td>';
        WPCaptcha_Utility::create_toggle_switch('login_protection', array('saved_value' => $options['login_protection'], 'option_key' => esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[login_protection]'));
        echo '<br><span>Enable Login Protection</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label for="max_login_retries">Max Login Retries</label></th>
        <td><input type="number" class="regular-text" id="max_login_retries" name="' . esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[max_login_retries]" value="' . (int)$options['max_login_retries'] . '" />';
        echo '<span>Number of failed login attempts within the "Retry Time Period Restriction" (defined below) needed to trigger a Access Lock.</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label for="retries_within">Retry Time Period Restriction</label></th>
        <td><input type="number" class="regular-text" id="retries_within" name="' . esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[retries_within]" value="' . (int)$options['retries_within'] . '" /> minutes';
        echo '<span>Amount of time in which failed login attempts are allowed before an access lock occurs.</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label for="lockout_length">Access Lock Length</label></th>
        <td><input type="number" class="regular-text" id="lockout_length" name="' . esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[lockout_length]" value="' . (int)$options['lockout_length'] . '" /> minutes';
        echo '<span>Amount of time a particular IP will be blocked once an access lock has been triggered.</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="lockout_invalid_usernames" for="lockout_invalid_usernames">Log Failed Attempts With Non-existant Usernames</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="lockout_invalid_usernames" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="lockout_invalid_usernames">';
        WPCaptcha_Utility::create_toggle_switch('lockout_invalid_usernames', array('saved_value' => 0, 'option_key' => ''));
        echo '</div>';
        echo '<span>Log failed log in attempts with non-existant usernames the same way failed attempts with bad passwords are logged.</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="mask_login_errors" for="mask_login_errors">Mask Login Errors</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="mask_login_errors" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="mask_login_errors">';
        WPCaptcha_Utility::create_toggle_switch('mask_login_errors', array('saved_value' => 0, 'option_key' => ''));
        echo '</div>';
        echo '<span>Hide log in error details (such as invalid username, invalid password, invalid captcha value) to minimize data available to attackers.</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="global_block" for="global_block">Block Type</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="global_block" class="open-upsell pro-label">PRO</a></th>
        <td>';
            echo '<div class="open-upsell open-upsell-block" data-feature="global_block">';
            echo '<label class="wpcaptcha-radio-option">';
            echo '<span class="radio-container"><input type="radio" id="global_block_global" value="1"><span class="radio"></span></span> Completely block website access';
            echo '</label>';

            echo '<label class="wpcaptcha-radio-option">';
            echo '<span class="radio-container"><input type="radio"  id="global_block_login" value="0" checked><span class="radio"></span></span> Only block access to the login page';
            echo '</label>';
            echo '</div>';
        echo '<span>Completely block website access for blocked IPs, or just blocking access to the login page.</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="block_message" for="block_message">Block Message</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="block_message" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="block_message">';
        echo '<input type="text" class="regular-text" id="block_message" value="" />';
        echo '</div>';
        echo '<span>Message displayed to visitors blocked due to too many failed login attempts. Default: <i>We\'re sorry, but your IP has been blocked due to too many recent failed login attempts.</i></span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" for="whitelist" for="whitelist">Whitelisted IPs</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="whitelist" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="whitelist">';
        echo '<textarea class="regular-text" id="whitelist" rows="6"></textarea>';
        echo '</div>';
        echo '<span>List of IP addresses that will never be blocked. Enter one IP per line.<br>Your current IP is: <code>' . esc_html($_SERVER['REMOTE_ADDR']) . '</code></span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label for="show_credit_link">Show Credit Link</label></th>
        <td>';
        WPCaptcha_Utility::create_toggle_switch('show_credit_link', array('saved_value' => $options['show_credit_link'], 'option_key' => esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[show_credit_link]'));
        echo '<br><span>Show a small "form protected by" link below the login form to help others learn about WP Captcha and protect their sites.</span>';
        echo '</td></tr>';

        echo '<tr><td></td><td>';
        WPCaptcha_admin::footer_save_button();
        echo '</td></tr>';

        echo '</tbody></table>';
    }

    static function tab_advanced()
    {
        $options = WPCaptcha_Setup::get_options();

        echo '<table class="form-table"><tbody>';

        if(is_multisite()){
            echo '<div class="notice-box-info" style="border-color:#ff9f00;">WP Captcha does not support changing the Login URL for multisite installs</div>';
        }

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" data-feature="login_url" for="login_url">Login URL</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="login_url" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="login_url">';
        echo '<code>' . esc_url(home_url('/')) . '</code><input type="text" class="regular-text" style="width:160px;" id="login_url" value="" /><code>/</code>';
        echo '</div>';
        echo '<span>Protect your website by changing the login page URL and prevent access to the default <code>wp-login.php</code> page and the <code>wp-admin</code> path that represent the main target of most attacks. Leave empty to use default login URL.</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" data-feature="login_redirect_url" for="login_redirect_url">Redirect URL</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="login_redirect_url" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="login_redirect_url">';
        echo '<code>' . esc_url(home_url('/')) . '</code><input type="text" class="regular-text" style="width:160px;"  id="login_redirect_url" value="" placeholder="404" /><code>/</code>';
        echo '</div>';
        echo '<span>URL where attempts to access <code>wp-login.php</code> or <code>wp-admin</code> should be redirected to. If a custom login URL is set, this defaults to <code>' . esc_url(home_url('/404/')) . '</code> unless you set it to something else.</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" data-feature="password_check" for="password_check">Password Check</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="password_check" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="wpcaptcha_run_tests">';
        echo '<button class="button button-primary button-large" style="margin-bottom:6px;">Test user passwords <i class="wpcaptcha-icon wpcaptcha-lock"></i></button>';
        echo '</div>';
        echo '<span>Check if any user has a weak password that is vulnerable to common brute-force dictionary attacks.</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" data-feature="anonymous_logging" for="anonymous_logging">Anonymous Activity Logging</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="anonymous_logging" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="anonymous_logging">';
        WPCaptcha_Utility::create_toggle_switch('anonymous_logging', array('saved_value' => 0, 'option_key' => ''));
        echo '</div>';
        echo '<span>Logging anonymously means IP addresses of your visitors are stored as hashed values. The user\'s country and user agent are still logged, but without the IP these are not considered personal data according to GDPR.</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" data-feature="log_passwords" for="log_passwords">Log Passwords</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="log_passwords" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="log_passwords">';
        WPCaptcha_Utility::create_toggle_switch('log_passwords', array('saved_value' => 0, 'option_key' => ''));
        echo '</div>';
        echo '<span>Enablign this option will log the passwords used in failed login attempts. This is not recommended on websites with multiple users as the passwords are logged as plain text and can be viewed by all users that have access to the WP Captcha logs or the database.</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" data-feature="block_bots" for="block_bots">Block Bots</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="block_bots" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="block_bots">';
        WPCaptcha_Utility::create_toggle_switch('block_bots', array('saved_value' => 0, 'option_key' => ''));
        echo '</div>';
        echo '<span>Block bots from accessing the login page and attempting to log in.</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" data-feature="instant_block_nonusers" for="instant_block_nonusers">Block Login Attempts With Non-existing Usernames</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="instant_block_nonusers" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="instant_block_nonusers">';
        WPCaptcha_Utility::create_toggle_switch('instant_block_nonusers', array('saved_value' => 0, 'option_key' => ''));
        echo '</div>';
        echo '<span>Immediately block IP if there is a failed login attempt with a non-existing username</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" data-feature="honeypot" for="honeypot">Add Honeypot for Bots</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="honeypot" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="honeypot">';
        WPCaptcha_Utility::create_toggle_switch('honeypot', array('saved_value' => 0, 'option_key' => ''));
        echo '</div>';
        echo '<span>Add a special, hidden "honeypot" field to the login form to catch and prevent bots from attempting to log in.<br>This does not affect the way humans log in, nor does it add an extra step.</span>';
        echo '</td></tr>';

        $cookie_lifetime = array();
        $cookie_lifetime[] = array('val' => '14', 'label' => '14 days (default)');
        $cookie_lifetime[] = array('val' => '30', 'label' => '30 days');
        $cookie_lifetime[] = array('val' => '90', 'label' => '3 months');
        $cookie_lifetime[] = array('val' => '180', 'label' => '6 months');
        $cookie_lifetime[] = array('val' => '365', 'label' => '1 year');

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" data-feature="cookie_lifetime" for="cookie_lifetime">Cookie Lifetime</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="cookie_lifetime" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="cookie_lifetime">';
        echo '<select id="cookie_lifetime">';
        WPCaptcha_Utility::create_select_options($cookie_lifetime, $options['cookie_lifetime']);
        echo '</select>';
        echo '</div>';
        echo '<span>Cookie lifetime if "Remember Me" option is checked on login form.</span>';
        echo '</td></tr>';

        echo '<tr><td></td><td>';
        WPCaptcha_admin::footer_save_button();
        echo '</td></tr>';

        echo '</tbody></table>';
    }

    static function tab_tools()
    {
        $options = WPCaptcha_Setup::get_options();

        echo '<table class="form-table"><tbody>';

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" data-feature="test_email" for="password_check">Email Test</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="test_email" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="test_email">';
        echo '<button class="button button-primary button-large" style="margin-bottom:6px;">Send test email</button>';
        echo '</div>';
        echo '<span>Send an email to test that you can receive emails from your website.</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label class="open-upsell open-upsell-block" data-feature="honeypot" for="recovery_url">Recovery URL</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="recovery_url" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="recovery_url">';
        echo '<button class="button button-primary button-large" style="margin-bottom:6px;">View Recovery URL</button>';
        echo '</div>';
        echo '<span>In case you lock yourself out and need to whitelist your IP address, please save the recovery URL somewhere safe.<br>Do NOT share the recovery URL.</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th><label class="open-upsell open-upsell-block" data-feature="import_file">Import Settings</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="import_file" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="import_file">';
        echo '<input accept="txt" type="file" name="wpcaptcha_import_file" value="">
        <button name="wpcaptcha_import_file" id="submit" class="button button-primary button-large" value="">Upload</button>';
        echo '</div>';
        echo '</td>
        </tr>';

        echo '<tr valign="top">
        <th><label class="open-upsell open-upsell-block" data-feature="export">Export Settings</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="export" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="export">';
        echo '<a class="button button-primary button-large" style="padding-top: 3px;" href="' . esc_url(add_query_arg(array('action' => 'wpcaptcha_export_settings'), admin_url('admin.php'))) . '">Download Export File</a>';
        echo '</div>';
        echo '</td>
        </tr>';

        echo '</tbody></table>';
    }
} // class WPCaptcha_Tab_Login_Form
