<?php
/**
 * WP Captcha
 * https://getwpcaptcha.com/
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

class WPCaptcha_Tab_Temporary_Access extends WPCaptcha
{
  static function display()
  {
    echo '<div class="tab-content">';

    echo '<div class="notice-box-info">
        Temporary Access links are a convenient way to give temporary access to other people. You can set the lifetime of the link and the maximum number of times it can be used to prevent abuse. <a href="#" class="open-pro-dialog" data-pro-feature="temp-access">Get PRO now</a> to use the Temporary Links feature.
        </div>';

    echo '<img  class="open-upsell open-upsell-block" data-feature="temporary_access" style="width: 100%;" src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . '/images/temporary-access.png" alt="WP Captcha" title="WP Captcha Temporary Access Links" />';
    echo '</div>';
  } // display
} // class WPCaptcha_Tab_2FA
