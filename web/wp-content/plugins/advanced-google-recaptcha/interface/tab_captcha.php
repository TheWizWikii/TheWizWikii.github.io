<?php
/**
 * WP Captcha
 * https://getwpcaptcha.com/
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

class WPCaptcha_Tab_Captcha extends WPCaptcha
{
    static function display()
    {
        $tabs[] = array('id' => 'tab_captcha', 'class' => 'tab-content', 'label' => __('Captcha', 'advanced-google-recaptcha'), 'callback' => array(__CLASS__, 'tab_captcha'));
        $tabs[] = array('id' => 'tab_captcha_location', 'class' => 'tab-content', 'label' => __('Where To Show', 'advanced-google-recaptcha'), 'callback' => array(__CLASS__, 'tab_captcha_location'));

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

    static function tab_captcha()
    {
        $options = WPCaptcha_Setup::get_options();

        echo '<div class="tab-content">';

        echo '<table class="form-table"><tbody>';

        $captcha = array();
        $captcha[] = array('val' => 'disabled', 'label' => 'Disabled');
        $captcha[] = array('val' => 'builtin', 'label' => 'Built-in Math Captcha');
        $captcha[] = array('val' => 'icons', 'label' => 'Built-in Icon Captcha', 'class' => 'pro-option');
        $captcha[] = array('val' => 'recaptchav2', 'label' => 'Google reCAPTCHA v2');
        $captcha[] = array('val' => 'recaptchav3', 'label' => 'Google reCAPTCHA v3');
        $captcha[] = array('val' => 'hcaptcha', 'label' => 'hCaptcha', 'class' => 'pro-option');
        $captcha[] = array('val' => 'cloudflare', 'label' => 'Cloudflare Turnstile', 'class' => 'pro-option');

        echo '<tr valign="top">
        <th scope="row"><label for="captcha">Captcha</label></th>
        <td><select id="captcha" name="' . esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[captcha]">';
        WPCaptcha_Utility::create_select_options($captcha, $options['captcha']);
        echo '</select>';
        echo '<br /><span>Captcha or "are you human" verification ensures bots can\'t attack your login page and provides additional protection with minimal impact to users.</span>';
        echo '</td></tr>';

        echo '<tr class="captcha_keys_wrapper" style="display:none;" valign="top">
        <th scope="row"><label for="captcha_site_key">Captcha Site Key</label></th>
        <td><input type="text" class="regular-text" id="captcha_site_key" name="' . esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[captcha_site_key]" value="' . esc_html($options['captcha_site_key']) . '" data-old="' . esc_html($options['captcha_site_key']) . '" />';
        echo '</td></tr>';

        echo '<tr class="captcha_keys_wrapper" style="display:none;" valign="top">
        <th scope="row"><label for="captcha_secret_key">Captcha Secret Key</label></th>
        <td><input type="text" class="regular-text" id="captcha_secret_key" name="' . esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[captcha_secret_key]" value="' . esc_html($options['captcha_secret_key']) . '" data-old="' . esc_html($options['captcha_secret_key']) . '" />';
        echo '</td></tr>';

        echo '<tr class="captcha_verify_wrapper" style="display:none;" valign="top">
        <th scope="row"></th>
        <td><button id="verify-captcha" class="button button-primary button-large button-yellow">Verify Captcha <i class="wpcaptcha-icon wpcaptcha-make-group"></i></button>';
        echo '<input type="hidden" class="regular-text" id="captcha_verified" name="' . esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[captcha_verified]" value="0" />';
        echo '<br /><span>Click the Verify Captcha button to verify that the captcha is valid and working otherwise captcha settings will not be saved</span>';
        echo '</td></tr>';

        echo '<tr><td></td><td>';
        WPCaptcha_admin::footer_save_button();
        echo '</td></tr>';

        echo '<tr><td colspan="2">';
            echo '<div class="captcha-box-wrapper ' . ($options['captcha'] == 'disabled'?'captcha-selected':'') . '" data-captcha="disabled">';
                echo '<img src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . '/images/captcha_disabled.png" />';
                echo '<div class="captcha-box-desc">';
                    echo '<h3>Captcha Disabled</h3>';
                    echo '<ul>';
                    echo '<li>No Additional Security</li>';
                    echo '</ul>';
                echo '</div>';
            echo '</div>';

            echo '<div class="captcha-box-wrapper ' . ($options['captcha'] == 'builtin'?'captcha-selected':'') . '" data-captcha="builtin">';
                echo '<img src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . '/images/captcha_builtin.png" />';
                echo '<div class="captcha-box-desc">';
                    echo '<h3>Built-in Math Captcha</h3>';
                    echo '<ul>';
                    echo '<li>Medium Security</li>';
                    echo '<li>No API keys</li>';
                    echo '<li>No 3rd party services</li>';
                    echo '<li>GDPR Compatible</li>';
                    echo '</ul>';
                echo '</div>';
            echo '</div>';

            echo '<div class="captcha-box-wrapper ' . ($options['captcha'] == 'icons'?'captcha-selected':'') . '" data-captcha="icons">';
                echo '<a title="This feature is available in the PRO version. Click for details." href="#" data-feature="recaptchav2" class="open-upsell pro-label">PRO</a>';
                echo '<img src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . '/images/captcha_icons.png" />';
                echo '<div class="captcha-box-desc">';
                    echo '<h3>Built-in Icon Captcha</h3>';
                    echo '<ul>';
                    echo '<li>Medium Security</li>';
                    echo '<li>No API keys</li>';
                    echo '<li>No 3rd party services</li>';
                    echo '<li>GDPR Compatible</li>';
                    echo '</ul>';
                echo '</div>';
            echo '</div>';

            echo '<div class="captcha-box-wrapper ' . ($options['captcha'] == 'recaptchav2'?'captcha-selected':'') . '" data-captcha="recaptchav2">';
                echo '<img src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . '/images/captcha_recaptcha_v2.png" />';
                echo '<div class="captcha-box-desc">';
                    echo '<h3>Google reCaptcha v2</h3>';
                    echo '<ul>';
                    echo '<li>High Security</li>';
                    echo '<li>Requires <a href="https://www.google.com/recaptcha/about/" target="_blank">API Keys</a></li>';
                    echo '<li>Powered by Google</li>';
                    echo '<li>Not GDPR Compatible</li>';
                    echo '</ul>';
                echo '</div>';
            echo '</div>';

            echo '<div class="captcha-box-wrapper ' . ($options['captcha'] == 'recaptchav3'?'captcha-selected':'') . '" data-captcha="recaptchav3">';
                echo '<img src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . '/images/captcha_recaptcha_v3.png" />';
                echo '<div class="captcha-box-desc">';
                    echo '<h3>Google reCaptcha v3</h3>';
                    echo '<ul>';
                    echo '<li>High Security</li>';
                    echo '<li>Requires <a href="https://www.google.com/recaptcha/about/" target="_blank">API Keys</a></li>';
                    echo '<li>Powered by Google</li>';
                    echo '<li>Not GDPR Compatible</li>';
                    echo '</ul>';
                echo '</div>';
            echo '</div>';

            echo '<div class="captcha-box-wrapper ' . ($options['captcha'] == 'hcaptcha'?'captcha-selected':'') . '" data-captcha="hcaptcha">';
                echo '<a title="This feature is available in the PRO version. Click for details." href="#" data-feature="recaptchav2" class="open-upsell pro-label">PRO</a>';
                echo '<img src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . '/images/captcha_hcaptcha.png" />';
                echo '<div class="captcha-box-desc">';
                    echo '<h3>hCaptcha</h3>';
                    echo '<ul>';
                    echo '<li>High Security</li>';
                    echo '<li>Requires <a href="https://www.hcaptcha.com/signup-interstitial" target="_blank">API Keys</a></li>';
                    echo '<li>GDPR Compatible</li>';
                    echo '<li>Best Choice</li>';
                    echo '</ul>';
                echo '</div>';
            echo '</div>';

            echo '<div class="captcha-box-wrapper ' . ($options['captcha'] == 'cloudflare'?'captcha-selected':'') . '" data-captcha="cloudflare">';
                echo '<a title="This feature is available in the PRO version. Click for details." href="#" data-feature="recaptchav2" class="open-upsell pro-label">PRO</a>';
                echo '<img src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . '/images/captcha_cloudflare.png" />';
                echo '<div class="captcha-box-desc">';
                    echo '<h3>Cloudflare Turnstile</h3>';
                    echo '<ul>';
                    echo '<li>High Security</li>';
                    echo '<li>Requires <a href="https://dash.cloudflare.com/sign-up?to=/:account/turnstile" target="_blank">API Keys</a></li>';
                    echo '<li>Not explicitly GDPR Compatible</li>';
                    echo '<li>Powered by Cloudflare</li>';
                    echo '</ul>';
                echo '</div>';
            echo '</div>';


        echo '</td></tr>';

        echo '</tbody></table>';

        echo '</div>';
    } // tab_captcha

    static function tab_captcha_location()
    {
        $options = WPCaptcha_Setup::get_options();

        echo '<div class="tab-content">';

        echo '<table class="form-table"><tbody>';

        echo '<tr valign="top">
        <th scope="row"><label for="captcha_show_login">Login Form</label></th>
        <td>';
        WPCaptcha_Utility::create_toggle_switch('captcha_show_login', array('saved_value' => $options['captcha_show_login'], 'option_key' => esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[captcha_show_login]'));
        echo '<br /><span>Applies to default login, WooCommerce, and Easy Digital Downloads login pages</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label for="captcha_show_wp_registration">Registration Form</label></th>
        <td>';
        WPCaptcha_Utility::create_toggle_switch('captcha_show_wp_registration', array('saved_value' => $options['captcha_show_wp_registration'], 'option_key' => esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[captcha_show_wp_registration]'));
        echo '<br /><span>Show captcha on WordPress user registration form</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label for="captcha_show_wp_lost_password">Lost Password Form</label></th>
        <td>';
        WPCaptcha_Utility::create_toggle_switch('captcha_show_wp_lost_password', array('saved_value' => $options['captcha_show_wp_lost_password'], 'option_key' => esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[captcha_show_wp_lost_password]'));
        echo '<br /><span>Show captcha on WordPress lost password form</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label for="captcha_show_wp_comment">Comment Form</label></th>
        <td>';
        WPCaptcha_Utility::create_toggle_switch('captcha_show_wp_comment', array('saved_value' => $options['captcha_show_wp_comment'], 'option_key' => esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[captcha_show_wp_comment]'));
        echo '<br /><span>Show captcha on WordPress comments form</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label for="captcha_show_woo_registration">WooCommerce Registration Form</label></th>
        <td>';
        WPCaptcha_Utility::create_toggle_switch('captcha_show_woo_registration', array('saved_value' => $options['captcha_show_woo_registration'], 'option_key' => esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[captcha_show_woo_registration]'));
        echo '<br /><span>Show captcha on WooCommerce registration form</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label for="captcha_show_woo_checkout">WooCommerce Checkout Form</label></th>
        <td>';
        WPCaptcha_Utility::create_toggle_switch('captcha_show_woo_checkout', array('saved_value' => $options['captcha_show_woo_checkout'], 'option_key' => esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[captcha_show_woo_checkout]'));
        echo '<br /><span>Show captcha on WooCommerce checkout form</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label for="captcha_show_edd_registration">Easy Digital Downloads Registration Form</label></th>
        <td>';
        WPCaptcha_Utility::create_toggle_switch('captcha_show_edd_registration', array('saved_value' => $options['captcha_show_edd_registration'], 'option_key' => esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[captcha_show_edd_registration]'));
        echo '<br /><span>Show captcha on Easy Digital Downloads registration form</span>';
        echo '</td></tr>';

        echo '<tr valign="top">
        <th scope="row"><label for="captcha_show_bp_registration">BuddyPress Registration Form</label></th>
        <td>';
        WPCaptcha_Utility::create_toggle_switch('captcha_show_bp_registration', array('saved_value' => $options['captcha_show_bp_registration'], 'option_key' => esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[captcha_show_bp_registration]'));
        echo '<br /><span>Show captcha on BuddyPress registration form</span>';
        echo '</td></tr>';

        echo '<tr><td></td><td>';
        WPCaptcha_admin::footer_save_button();
        echo '</td></tr>';

        echo '</tbody></table>';

        echo '</div>';
    } // tab_captcha_location
} // class WPCaptcha_Tab_Captcha
