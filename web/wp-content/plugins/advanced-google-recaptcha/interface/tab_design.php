<?php

/**
 * WP Captcha
 * https://getwpcaptcha.com/
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

class WPCaptcha_Tab_Design extends WPCaptcha
{
    static function display()
    {
        echo '<div class="tab-content">';

        $options = WPCaptcha_Setup::get_options();
        $templates = WPCaptcha_Functions::get_templates();

        echo '<table class="form-table"><tbody>';
        echo '<tr valign="top">
        <th scope="row"><label for="block_bots">Enable Customizer</label></th>
        <td>';
        WPCaptcha_Utility::create_toggle_switch('design_enable', array('saved_value' => $options['design_enable'], 'option_key' => esc_attr(WPCAPTCHA_OPTIONS_KEY) . '[design_enable]'));
        echo '<br /><span>You can enable the customizer to use the settings below or leave it turned off to show the default WordPress login page style or customize it using a different plugin or theme settings</span>';
        echo '</td></tr>';
        echo '</tbody>';
        echo '</table>';

        echo '<h3>Templates:</h3>';
        echo '<ul class="design-templates">';
        foreach($templates as $template_id => $template){
            WPCaptcha_Utility::wp_kses_wf('<li><a class="disable_confirm_action ' . ($template_id == $options['design_template']?'design-template-active':'') . '" data-confirm="Are you sure you want to enable this template? This will overwrite all Design settings." href="' . add_query_arg(array('_wpnonce' => wp_create_nonce('wpcaptcha_install_template'), 'template' => $template_id, 'action' => 'wpcaptcha_install_template', 'redirect' => urlencode($_SERVER['REQUEST_URI'])), admin_url('admin.php')) . '"><img src="' . WPCAPTCHA_PLUGIN_URL . '/images/templates/' . $template_id . '.jpg"></a></li>');
        }
        echo '</ul>';

        echo '<tr><td></td><td>';
        WPCaptcha_admin::footer_save_button();
        echo '</td></tr>';

        echo '<br />';

        echo '<div class="notice-box-info">
         The Design options allow you to completely customize the login page appearance.<a href="#" class="open-pro-dialog" data-pro-feature="design">Get PRO now</a> to use the Design feature.
        </div>';

        echo '<img  class="open-upsell open-upsell-block" data-feature="design" style="width: 100%;" src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . '/images/design.png" alt="WP Captcha" title="WP Captcha Design" />';
        echo '</div>';
    } // display

} // class WPCaptcha_Tab_Login_Form
