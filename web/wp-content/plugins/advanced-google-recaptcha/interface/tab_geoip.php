<?php
/**
 * WP Captcha
 * https://getwpcaptcha.com/
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

class WPCaptcha_Tab_GeoIP extends WPCaptcha
{
    static function display()
    {
        echo '<div class="tab-content">';

        echo '<div class="notice-box-info">
        The Country Blocking feature allows you to easily block whole countries from either accessing the login form or the whole website. Or if preferred, you can just allow access from certain countries instead.<a href="#" class="open-pro-dialog" data-pro-feature="country-blocking">Get PRO now</a> to use the Country Blocking feature.
        </div>';

        echo '<img src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . '/images/map.png" alt="WP Captcha" title="WP Captcha Country Blocking Map" />';

        echo '<table class="form-table"><tbody>';

        $country_blocking_mode = array();
        $country_blocking_mode[] = array('val' => 'none', 'label' => 'Disable country based blocking');
        $country_blocking_mode[] = array('val' => 'whitelist', 'label' => 'Whitelist mode - allow selected countries, block all others');
        $country_blocking_mode[] = array('val' => 'blacklist', 'label' => 'Blacklist mode - block selected countries, allow all others');

        echo '<tr valign="top">
        <th scope="row"><label for="country_blocking_mode">Blocking Mode</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="country_blocking_mode" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<select id="country_blocking_mode" name="" data-feature="country_blocking_mode">';
        WPCaptcha_Utility::create_select_options($country_blocking_mode, 0);
        echo '</select>';
        echo '<br /><span>Whitelabel mode is best when you want to allow only a few, selected countries to access your site. While the blacklist mode is suited for situations when the majority of countries should be able to access it, and just a few should be blocked.</span>';
        echo '</td></tr>';


        echo '<tr valign="top" class="country-blocking-wrapper" style="display:none">';
        echo '<th scope="row"><label for="country_blocking_countries" class="country-blocking-label">Countries</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="country_blocking_countries" class="open-upsell pro-label">PRO</a></th>';
        echo '<td><input data-feature="country_blocking_countries" type="text" class="open-upsell" id="country_blocking_countries" style="width:500px; max-width:500px !important;" name="" placeholder="Select Countries" />';
        echo '</td></tr>';

        echo '<tr valign="top" class="country-blocking-wrapper" style="display:none">
        <th scope="row"><label for="block_undetermined_countries">Block Undetermined Countries</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="block_undetermined_countries" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="block_undetermined_countries">';
        WPCaptcha_Utility::create_toggle_switch('block_undetermined_countries', array('saved_value' => 0, 'option_key' => ''));
        echo '</div>';
        echo '<br /><span>For some IP addresses it\'s impossible to determine their country (localhost addresses, for instance). Enabling this option will blocks regardless of the Blocking Mode setting.</span>';
        echo '</td></tr>';

        echo '<tr valign="top" class="country-blocking-wrapper" style="display:none">
        <th scope="row"><label for="country_global_block_global">Country Block Type</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="country_global_block" class="open-upsell pro-label">PRO</a></th>
        <td>';
        echo '<div class="open-upsell open-upsell-block" data-feature="country_global_block">';
        echo '<label class="wpcaptcha-radio-option">';
        echo '<span class="radio-container"><input type="radio" name="" id="country_global_block_global" value="1" checked><span class="radio"></span></span> Completely block website access';
        echo '</label>';

        echo '<label class="wpcaptcha-radio-option">';
        echo '<span class="radio-container radio-disabled"><input type="radio" name="" id="country_global_block_login" value="0"><span class="radio"></span></span> Only block access to the login page';
        echo '</label>';
        echo '</div>';
        echo '<span>Completely block website access for blocked countries, or just blocking access to the login page.</span>';
        echo '</td></tr>';


        echo '<tr valign="top" class="country-blocking-wrapper" style="display:none">
        <th scope="row"><label for="block_message_country">Block Message</label><a title="This feature is available in the PRO version. Click for details." href="#" data-feature="block_message_country" class="open-upsell pro-label">PRO</a></th>
        <td><input type="text" data-feature="block_message_country" class="open-upsell regular-text" id="block_message_country" name="" value="" placeholder="We\'re sorry, but access from your location is not allowed." />';
        echo '<br /><span>Message displayed to visitors blocked based on country blocking rules. Default: <i>We\'re sorry, but access from your location is not allowed.</i></span>';
        echo '</td></tr>';

        echo '<tr><td></td><td>';
        echo '<p class="submit"><a class="button button-primary button-large open-upsell" data-feature="country-blocking-save">Save Changes <i class="wpcaptcha-icon wpcaptcha-checkmark"></i></a></p>';
        echo '</td></tr>';

        echo '</tbody></table>';

        echo '</div>';
    } // display
} // class WPCaptcha_Tab_GeoIP
