<?php

/**
 * WP Captcha
 * https://getwpcaptcha.com/
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

class WPCaptcha_Admin extends WPCaptcha
{

  /**
   * Enqueue Admin Scripts
   *
   * @since 5.0
   *
   * @return null
   */
  static function admin_enqueue_scripts($hook)
  {
    if ('settings_page_wpcaptcha' == $hook) {
      wp_enqueue_style('wpcaptcha-admin', WPCAPTCHA_PLUGIN_URL . 'css/wpcaptcha.css', array(), self::$version);
      wp_enqueue_style('wpcaptcha-dataTables', WPCAPTCHA_PLUGIN_URL . 'css/jquery.dataTables.min.css', array(), self::$version);
      wp_enqueue_style('wpcaptcha-sweetalert', WPCAPTCHA_PLUGIN_URL . 'css/sweetalert2.min.css', array(), self::$version);
      wp_enqueue_style('wpcaptcha-tooltipster', WPCAPTCHA_PLUGIN_URL . 'css/tooltipster.bundle.min.css', array(), self::$version);
      wp_enqueue_style('wp-color-picker');
      wp_enqueue_style('wp-jquery-ui-dialog');

      wp_enqueue_script('jquery-ui-tabs');
      wp_enqueue_script('jquery-ui-core');
      wp_enqueue_script('jquery-ui-position');
      wp_enqueue_script('jquery-effects-core');
      wp_enqueue_script('jquery-effects-blind');
      wp_enqueue_script('jquery-ui-dialog');

      wp_enqueue_script('wpcaptcha-tooltipster', WPCAPTCHA_PLUGIN_URL . 'js/tooltipster.bundle.min.js', array('jquery'), self::$version, true);
      wp_enqueue_script('wpcaptcha-dataTables', WPCAPTCHA_PLUGIN_URL . 'js/jquery.dataTables.min.js', array(), self::$version, true);
      wp_enqueue_script('wpcaptcha-chart', WPCAPTCHA_PLUGIN_URL . 'js/chart.min.js', array(), self::$version, true);
      wp_enqueue_script('wpcaptcha-moment', WPCAPTCHA_PLUGIN_URL . 'js/moment.min.js', array(), self::$version, true);
      wp_enqueue_script('wpcaptcha-sweetalert', WPCAPTCHA_PLUGIN_URL . 'js/sweetalert2.min.js', array(), self::$version, true);

      wp_enqueue_script('wp-color-picker');
      wp_enqueue_media();

      $js_localize = array(
        'undocumented_error' => __('An undocumented error has occurred. Please refresh the page and try again.', 'advanced-google-recaptcha'),
        'documented_error' => __('An error has occurred.', 'advanced-google-recaptcha'),
        'plugin_name' => __('WP Captcha', 'advanced-google-recaptcha'),
        'plugin_url' => WPCAPTCHA_PLUGIN_URL,
        'icon_url' => WPCAPTCHA_PLUGIN_URL . 'images/wp-captcha-loader.gif',
        'settings_url' => admin_url('options-general.php?page=wpcaptcha'),
        'version' => self::$version,
        'site' => get_home_url(),
        'url' => WPCAPTCHA_PLUGIN_URL,
        'cancel_button' => __('Cancel', 'advanced-google-recaptcha'),
        'ok_button' => __('OK', 'advanced-google-recaptcha'),
        'run_tool_nonce' => wp_create_nonce('wpcaptcha_run_tool'),
        'stats_unavailable' => 'Stats will be available once enough data is collected.',
        'stats_locks' => WPCaptcha_Stats::get_stats('locks'),
        'stats_fails' => WPCaptcha_Stats::get_stats('fails'),
        'wp301_install_url' => add_query_arg(array('action' => 'wpcaptcha_install_wp301', '_wpnonce' => wp_create_nonce('install_wp301'), 'rnd' => rand()), admin_url('admin.php')),
      );

      $js_localize['chart_colors'] = array('#4285f4', '#ff5429', '#ff7d5c', '#ffac97');

      wp_enqueue_script('wpcaptcha-admin', WPCAPTCHA_PLUGIN_URL . 'js/wpcaptcha.js', array('jquery'), self::$version, true);
      wp_localize_script('wpcaptcha-admin', 'wpcaptcha_vars', $js_localize);

      // fix for aggressive plugins that include their CSS or JS on all pages
      wp_dequeue_style('uiStyleSheet');
      wp_dequeue_style('wpcufpnAdmin');
      wp_dequeue_style('unifStyleSheet');
      wp_dequeue_style('wpcufpn_codemirror');
      wp_dequeue_style('wpcufpn_codemirrorTheme');
      wp_dequeue_style('collapse-admin-css');
      wp_dequeue_style('jquery-ui-css');
      wp_dequeue_style('tribe-common-admin');
      wp_dequeue_style('file-manager__jquery-ui-css');
      wp_dequeue_style('file-manager__jquery-ui-css-theme');
      wp_dequeue_style('wpmegmaps-jqueryui');
      wp_dequeue_style('wp-botwatch-css');
      wp_dequeue_style('njt-filebird-admin');
      wp_dequeue_style('ihc_jquery-ui.min.css');
      wp_dequeue_style('badgeos-juqery-autocomplete-css');
      wp_dequeue_style('mainwp');
      wp_dequeue_style('mainwp-responsive-layouts');
      wp_dequeue_style('jquery-ui-style');
      wp_dequeue_style('additional_style');
      wp_dequeue_style('wobd-jqueryui-style');
      wp_dequeue_style('wpdp-style3');
      wp_dequeue_style('jquery_smoothness_ui');
      wp_dequeue_style('uap_main_admin_style');
      wp_dequeue_style('uap_font_awesome');
      wp_dequeue_style('uap_jquery-ui.min.css');
      wp_dequeue_style('wqm-select2-style');

      wp_deregister_script('wqm-select2-script');

      WPCaptcha_Utility::dismiss_pointer_ajax();
    }

    $pointers = get_option(WPCAPTCHA_POINTERS_KEY);

    if ('settings_page_wpcaptcha' != $hook) {
      if ($pointers) {
        $pointers['run_tool_nonce'] = wp_create_nonce('wpcaptcha_run_tool');
        wp_enqueue_script('wp-pointer');
        wp_enqueue_style('wp-pointer');
        wp_localize_script('wp-pointer', 'wpcaptcha_pointers', $pointers);
      }

      if ($pointers) {
        wp_enqueue_script('wpcaptcha-pointers', WPCAPTCHA_PLUGIN_URL . 'js/wpcaptcha-pointers.js', array('jquery'), self::$version, true);
      }
    }
  } // admin_enqueue_scripts

  static function admin_notices()
  {
    $notices = get_option(WPCAPTCHA_NOTICES_KEY);

    if (is_array($notices)) {
      foreach ($notices as $id => $notice) {
        WPCaptcha_Utility::wp_kses_wf('<div class="notice-' . $notice['type'] . ' notice is-dismissible"><p>' . $notice['text'] . '<button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></p></div>');
        if ($notice['once'] == true) {
          unset($notices[$id]);
          update_option(WPCAPTCHA_NOTICES_KEY, $notices);
        }
      }
    }
  } // notices

  static function add_notice($id = false, $text = '', $type = 'warning', $show_once = false)
  {
    if ($id) {
      $notices = get_option(WPCAPTCHA_NOTICES_KEY, array());
      $notices[$id] = array('text' => $text, 'type' => $type, 'once' => $show_once);
      update_option(WPCAPTCHA_NOTICES_KEY, $notices);
    }
  }

  /**
   * Admin menu entry
   *
   * @since 5.0
   *
   * @return null
   */
  static function admin_menu()
  {
    add_options_page(
      __('Advanced Google reCAPTCHA', 'advanced-google-recaptcha'),
      __('Advanced Google reCAPTCHA', 'advanced-google-recaptcha'),
      'manage_options',
      'wpcaptcha',
      array(__CLASS__, 'main_page')
    );
  } // admin_menu

  /**
   * Add settings link to plugins page
   *
   * @since 5.0
   *
   * @return null
   */
  static function plugin_action_links($links)
  {
    $settings_link = '<a href="' . admin_url('options-general.php?page=wpcaptcha') . '" title="WP Captcha Settings">' . __('Settings', 'advanced-google-recaptcha') . '</a>';
    $pro_link = '<a href="' . admin_url('options-general.php?page=wpcaptcha#open-pro-dialog') . '" title="Get more protection with WP Captcha PRO"><b>' . __('Get EXTRA protection', 'advanced-google-recaptcha') . '</b></a>';

    array_unshift($links, $settings_link);
    array_unshift($links, $pro_link);

    return $links;
  } // plugin_action_links

  /**
   * Add links to plugin's description in plugins table
   *
   * @since 5.0
   *
   * @return null
   */
  static function plugin_meta_links($links, $file)
  {
    if ($file !== 'advanced-google-recaptcha/advanced-google-recaptcha.php') {
      return $links;
    }

    $support_link = '<a href="https://getwpcaptcha.com/support/" title="' . __('Get help', 'advanced-google-recaptcha') . '">' . __('Support', 'advanced-google-recaptcha') . '</a>';
    $links[] = $support_link;

    return $links;
  } // plugin_meta_links

  /**
   * Admin footer text
   *
   * @since 5.0
   *
   * @return null
   */
  static function admin_footer_text($text)
  {
    if (!self::is_plugin_page()) {
      return $text;
    }

    $text = '<i class="wpcaptcha-footer">WP Captcha v' . self::$version . ' <a href="' . self::generate_web_link('admin_footer') . '" title="Visit WP Captcha page for more info" target="_blank">WebFactory Ltd</a>. Please <a target="_blank" href="https://wordpress.org/support/plugin/advanced-google-recaptcha/reviews/#new-post" title="Rate the plugin">rate the plugin <span>★★★★★</span></a> to help us spread the word. Thank you 🙌 from the WebFactory team!</i>';

    return $text;
  } // admin_footer_text

  /**
   * Helper function for generating UTM tagged links
   *
   * @param string  $placement  Optional. UTM content param.
   * @param string  $page       Optional. Page to link to.
   * @param array   $params     Optional. Extra URL params.
   * @param string  $anchor     Optional. URL anchor part.
   *
   * @return string
   */
  static function generate_web_link($placement = '', $page = '/', $params = array(), $anchor = '')
  {
    $base_url = 'https://getwpcaptcha.com';

    if ('/' != $page) {
      $page = '/' . trim($page, '/') . '/';
    }
    if ($page == '//') {
      $page = '/';
    }

    $parts = array_merge(array('utm_source' => 'advanced-google-recaptcha', 'utm_medium' => 'plugin', 'utm_content' => $placement, 'utm_campaign' => 'wpcaptcha-v' . self::$version), $params);

    if (!empty($anchor)) {
      $anchor = '#' . trim($anchor, '#');
    }

    $out = $base_url . $page . '?' . http_build_query($parts, '', '&amp;') . $anchor;

    return $out;
  } // generate_web_link

  /**
   * Test if we're on plugin's page
   *
   * @since 5.0
   *
   * @return null
   */
  static function is_plugin_page()
  {
    $current_screen = get_current_screen();

    if ($current_screen->id == 'settings_page_wpcaptcha') {
      return true;
    } else {
      return false;
    }
  } // is_plugin_page

  /**
   * Settings Page HTML
   *
   * @since 5.0
   *
   * @return null
   */
  static function main_page()
  {
    if (!current_user_can('manage_options')) {
      wp_die('You do not have sufficient permissions to access this page.');
    }

    $options = WPCaptcha_Setup::get_options();

    // auto remove welcome pointer when options are opened
    $pointers = get_option(WPCAPTCHA_POINTERS_KEY);
    if (isset($pointers['welcome'])) {
      unset($pointers['welcome']);
      update_option(WPCAPTCHA_POINTERS_KEY, $pointers);
    }

    echo '<div class="wrap">';
    echo '<div class="wpcaptcha-header">
                <div class="wp-captcha-logo">
                <img src="' . esc_url(WPCAPTCHA_PLUGIN_URL) . '/images/wp-captcha-logo.png" alt="WP Captcha" height="60" title="WP Captcha">
            </div>';

    echo '<a data-tab="firewall" data-tab2="general" title="Click to open Firewall Settings" class="tooltip change_tab wpcaptcha-header-status wpcaptcha-header-status-' . ($options['firewall_block_bots'] == 1 ? 'enabled' : 'disabled') . '" style="width: 142px;">';
    echo '<span class="dashicons dashicons-yes"></span>';
    echo '<div class="option">Firewall</span></div>';
    if ($options['firewall_block_bots'] == 'disabled') {
      echo '<div class="status">Disabled</div>';
    } else {
      echo '<div class="status">Enabled</div>';
    }
    echo '</a>';

    echo '<a data-tab="login_form" data-tab2="login_basic" title="Click to open Login Protection settings" class="tooltip change_tab wpcaptcha-header-status wpcaptcha-header-status-' . ($options['login_protection'] == 1 ? 'enabled' : 'disabled') . '">';
    echo '<span class="dashicons dashicons-yes"></span>';
    echo '<div class="option">Login Protection</span></div>';
    if ($options['login_protection'] == 'disabled') {
      echo '<div class="status">Disabled</div>';
    } else {
      echo '<div class="status">Enabled</div>';
    }
    echo '</a>';

    echo '<a data-tab="captcha" data-tab2="captcha" title="Click to open Captcha settings" class="tooltip change_tab wpcaptcha-header-status wpcaptcha-header-status-' . ($options['captcha'] == 'disabled' ? 'disabled' : 'enabled') . '" style="width: 142px;">';
    echo '<span class="dashicons dashicons-yes"></span>';
    echo '<div class="option">Captcha</span></div>';
    if ($options['captcha'] == 'disabled') {
      echo '<div class="status">Disabled</div>';
    } else {
      echo '<div class="status">Enabled</div>';
    }
    echo '</a>';

    echo '</div>';

    echo '<h1></h1>';

    echo '<form method="post" action="options.php" enctype="multipart/form-data" id="wpcaptcha_form">';
    settings_fields(WPCAPTCHA_OPTIONS_KEY);

    $tabs = array();

    $tabs[] = array('id' => 'wpcaptcha_captcha', 'icon' => 'wpcaptcha-icon wpcaptcha-make-group', 'class' => '', 'label' => __('Captcha', 'advanced-google-recaptcha'), 'callback' => array('WPCaptcha_Tab_Captcha', 'display'));
    $tabs[] = array('id' => 'wpcaptcha_activity', 'icon' => 'wpcaptcha-icon wpcaptcha-log', 'class' => '', 'label' => __('Activity', 'advanced-google-recaptcha'), 'callback' => array('WPCaptcha_Tab_Activity', 'display'));
    $tabs[] = array('id' => 'wpcaptcha_login_form', 'icon' => 'wpcaptcha-icon wpcaptcha-enter', 'class' => '', 'label' => __('Login Protection', 'advanced-google-recaptcha'), 'callback' => array('WPCaptcha_Tab_Login_Form', 'display'));
    $tabs[] = array('id' => 'wpcaptcha_firewall', 'icon' => 'wpcaptcha-icon wpcaptcha-check', 'class' => '', 'label' => __('Firewall', 'advanced-google-recaptcha'), 'callback' => array('WPCaptcha_Tab_Firewall', 'display'));
    $tabs[] = array('id' => 'wpcaptcha_geoip', 'icon' => 'wpcaptcha-icon wpcaptcha-globe', 'class' => '', 'label' => __('Country Blocking', 'advanced-google-recaptcha'), 'callback' => array('WPCaptcha_Tab_GeoIP', 'display'));
    $tabs[] = array('id' => 'wpcaptcha_design', 'icon' => 'wpcaptcha-icon wpcaptcha-settings', 'class' => '', 'label' => __('Design', 'advanced-google-recaptcha'), 'callback' => array('WPCaptcha_Tab_Design', 'display'));
    $tabs[] = array('id' => 'wpcaptcha_temp_access', 'icon' => 'wpcaptcha-icon wpcaptcha-hour-glass', 'class' => '', 'label' => __('Temp Access', 'advanced-google-recaptcha'), 'callback' => array('WPCaptcha_Tab_Temporary_Access', 'display'));
    $tabs[] = array('id' => 'wpcaptcha_pro', 'class' => 'open-upsell nav-tab-pro', 'icon' => '<span class="dashicons dashicons-star-filled"></span>', 'label' => __('PRO', 'advanced-google-recaptcha'), 'callback' => '');

    $tabs = apply_filters('wpcaptcha_tabs', $tabs);
    echo '<div id="wpcaptcha_tabs_wrapper" class="ui-tabs">';

    echo '<div id="wpcaptcha_tabs" class="ui-tabs" style="display: none;">';
    echo '<ul class="wpcaptcha-main-tab">';
    foreach ($tabs as $tab) {
      echo '<li><a ' . (!empty($tab['callback']) ? 'href="#' . esc_attr($tab['id']) . '"' : '') . 'class="' . esc_attr($tab['class']) . '">';
      if (strpos($tab['icon'], 'dashicon')) {
        WPCaptcha_Utility::wp_kses_wf($tab['icon']);
      } else {
        echo '<span class="icon"><i class="' . esc_attr($tab['icon']) . '"></i></span>';
      }
      echo '<span class="label">' . esc_attr($tab['label']) . '</span></a></li>';
    }
    echo '</ul>';

    foreach ($tabs as $tab) {
      if (is_callable($tab['callback'])) {
        echo '<div style="display: none;" id="' . esc_attr($tab['id']) . '">';
        call_user_func($tab['callback']);
        echo '</div>';
      }
    } // foreach

    echo '</div>';
    echo '</div>';

    echo '<div id="wpcaptcha_tabs_sidebar" style="display:none;">';
    echo '<div class="sidebar-box pro-ad-box">
            <p class="text-center"><a href="#" data-pro-feature="sidebar-box-logo" class="open-pro-dialog">
            <img src="' . esc_url(WPCAPTCHA_PLUGIN_URL . '/images/wp-captcha-logo.png') . '" alt="WP Captcha PRO" title="WP Captcha PRO"></a><br><b>PRO version is here! Grab the launch discount.</b></p>
            <ul class="plain-list">
                <li>7 Types of Captcha + GDPR Compatibility</li>
                <li>Login Page Customization - Visual &amp; URL</li>
                <li>Advanced Login Page Protection</li>
                <li>Email Based Two Factor Authentication (2FA)</li>
                <li>Advanced Firewall + Cloud Blacklists</li>
                <li>Country Blocking (whitelist &amp; blacklist)</li>
                <li>Temporary Access Links</li>
                <li>Recovery URL - You Can Never Get Locked Out</li>
                <li>Licenses &amp; Sites Manager (remote SaaS dashboard)</li>
                <li>White-label Mode</li>
                <li>Complete Codeless Plugin Rebranding</li>
                <li>Email support from plugin developers</li>
            </ul>

            <p class="text-center"><a href="#" class="open-pro-dialog button button-buy" data-pro-feature="sidebar-box">Get PRO Now</a></p>
            </div>';

    if (!defined('EPS_REDIRECT_VERSION') && !defined('WF301_PLUGIN_FILE')) {
      echo '<div class="sidebar-box pro-ad-box box-301">
            <h3 class="textcenter"><b>Problems with redirects?<br>Moving content around or changing posts\' URL?<br>Old URLs giving you problems?<br><br><u>Improve your SEO &amp; manage all redirects in one place!</u></b></h3>

            <p class="text-center"><a href="#" class="install-wp301">
            <img src="' . esc_url(WPCAPTCHA_PLUGIN_URL . '/images/wp-301-logo.png') . '" alt="WP 301 Redirects" title="WP 301 Redirects"></a></p>

            <p class="text-center"><a href="#" class="button button-buy install-wp301">Install and activate the <u>free</u> WP 301 Redirects plugin</a></p>

            <p><a href="https://wordpress.org/plugins/eps-301-redirects/" target="_blank">WP 301 Redirects</a> is a free WP plugin maintained by the same team as this WP Captcha plugin. It has <b>+250,000 users, 5-star rating</b>, and is hosted on the official WP repository.</p>
            </div>';
    }

    echo '<div class="sidebar-box" style="margin-top: 35px;">
            <p>Please <a href="https://wordpress.org/support/plugin/advanced-google-recaptcha/reviews/#new-post" target="_blank">rate the plugin ★★★★★</a> to <b>keep it up-to-date &amp; maintained</b>. It only takes a second to rate. Thank you! 👋</p>
            </div>';
    echo '</div>';
    echo '</form>';

    echo ' <div id="wpcaptcha-pro-dialog" style="display: none;" title="WP Captcha PRO is here!"><span class="ui-helper-hidden-accessible"><input type="text"/></span>

        <div class="center logo"><a href="https://getwpcaptcha.com/?ref=wpcaptcha-free-pricing-table" target="_blank"><img src="' . esc_url(WPCAPTCHA_PLUGIN_URL . '/images/wp-captcha-logo.png') . '" alt="WP Captcha PRO" title="WP Captcha PRO"></a><br>

        <span>Grab the limited PRO <b>Launch Discount</b></span>
        </div>

        <table id="wpcaptcha-pro-table">
        <tr>
        <td class="center">Personal License</td>
        <td class="center">Team License</td>
        <td class="center">Agency License</td>
        </tr>

        <tr class="prices">
        <td class="center"><span><del>$59</del> $49</span> <b>/year</b></td>
        <td class="center"><span><del>$119</del> $99</span> <b>/year</b></td>
        <td class="center"><span><del>$149</del> $119</span> <b>/year</b></td>
        </tr>

        <tr>
        <td><span class="dashicons dashicons-yes"></span><b>1 Site License</b>  ($49 per site)</td>
        <td><span class="dashicons dashicons-yes"></span><b>5 Sites License</b>  ($20 per site)</td>
        <td><span class="dashicons dashicons-yes"></span><b>100 Sites License</b>  ($1.2 per site)</td>
        </tr>

        <tr>
        <td><span class="dashicons dashicons-yes"></span>All Plugin Features</td>
        <td><span class="dashicons dashicons-yes"></span>All Plugin Features</td>
        <td><span class="dashicons dashicons-yes"></span>All Plugin Features</td>
        </tr>

        <tr>
        <td><span class="dashicons dashicons-yes"></span>7 Types of Captcha</td>
        <td><span class="dashicons dashicons-yes"></span>7 Types of Captcha</td>
        <td><span class="dashicons dashicons-yes"></span>7 Types of Captcha</td>
        </tr>

        <tr>
        <td><span class="dashicons dashicons-yes"></span>Advanced Firewall + Cloud Blacklists</td>
        <td><span class="dashicons dashicons-yes"></span>Advanced Firewall + Cloud Blacklists</td>
        <td><span class="dashicons dashicons-yes"></span>Advanced Firewall + Cloud Blacklists</td>
        </tr>

        <tr>
        <td><span class="dashicons dashicons-yes"></span>Login Page Customization</td>
        <td><span class="dashicons dashicons-yes"></span>Login Page Customization</td>
        <td><span class="dashicons dashicons-yes"></span>Login Page Customization</td>
        </tr>

        <tr>
        <td><span class="dashicons dashicons-yes"></span>Email Based 2FA</td>
        <td><span class="dashicons dashicons-yes"></span>Email Based 2FA</td>
        <td><span class="dashicons dashicons-yes"></span>Email Based 2FA</td>
        </tr>

        <tr>
        <td><span class="dashicons dashicons-yes"></span>Temporary Access Links</td>
        <td><span class="dashicons dashicons-yes"></span>Temporary Access Links</td>
        <td><span class="dashicons dashicons-yes"></span>Temporary Access Links</td>
        </tr>

        <tr>
        <td><span class="dashicons dashicons-yes"></span>Country Blocking</td>
        <td><span class="dashicons dashicons-yes"></span>Country Blocking</td>
        <td><span class="dashicons dashicons-yes"></span>Country Blocking</td>
        </tr>

        <tr>
        <td><span class="dashicons dashicons-yes"></span>SaaS Dashboard</td>
        <td><span class="dashicons dashicons-yes"></span>SaaS Dashboard</td>
        <td><span class="dashicons dashicons-yes"></span>SaaS Dashboard</td>
        </tr>

        <tr>
        <td><span class="dashicons dashicons-no"></span>White-label Mode</td>
        <td><span class="dashicons dashicons-yes"></span>White-label Mode</td>
        <td><span class="dashicons dashicons-yes"></span>White-label Mode</td>
        </tr>

        <tr>
        <td><span class="dashicons dashicons-no"></span>Full Plugin Rebranding</td>
        <td><span class="dashicons dashicons-no"></span>Full Plugin Rebranding</td>
        <td><span class="dashicons dashicons-yes"></span>Full Plugin Rebranding</td>
        </tr>

        <tr>
        <td><a class="button button-buy" data-href-org="https://getwpcaptcha.com/buy/?product=personal-yearly-launch&ref=pricing-table" href="https://getwpcaptcha.com/buy/?product=personal-yearly-launch&ref=pricing-table" target="_blank"><del>$59</del> $49 <small>/y</small><br>BUY NOW</a>
        <br>or <a class="button-buy" data-href-org="https://getwpcaptcha.com/buy/?product=personal-ltd-launch&ref=pricing-table" href="https://getwpcaptcha.com/buy/?product=personal-ltd-launch&ref=pricing-table" target="_blank">only <del>$99</del> $79 for a lifetime license</a></td>
        <td><a class="button button-buy" data-href-org="https://getwpcaptcha.com/buy/?product=team-yearly-launch&ref=pricing-table" href="https://getwpcaptcha.com/buy/?product=team-yearly-launch&ref=pricing-table" target="_blank"><del>$119</del> $99 <small>/y</small><br>BUY NOW</a></td>
        <td><a class="button button-buy" data-href-org="https://getwpcaptcha.com/buy/?product=agency-yearly-launch&ref=pricing-table" href="https://getwpcaptcha.com/buy/?product=agency-yearly-launch&ref=pricing-table" target="_blank"><del>$149</del> $119 <small>/y</small><br>BUY NOW</a></td>
        </tr>

        </table>

        <div class="center footer"><b>100% No-Risk Money Back Guarantee!</b> If you don\'t like the plugin over the next 7 days, we will happily refund 100% of your money. No questions asked! Payments are processed by our merchant of records - <a href="https://paddle.com/" target="_blank">Paddle</a>.</div>
      </div>';

    echo '</div>'; // wrap
  } // options_page

  /**
   * Reset pointers
   *
   * @since 5.0
   *
   * @return null
   */
  static function reset_pointers()
  {
    $pointers = array();
    $pointers['welcome'] = array('target' => '#menu-settings', 'edge' => 'left', 'align' => 'right', 'content' => 'Thank you for installing the <b style="font-weight: 800; font-variant: small-caps;">Advanced Google reCAPTCHA</b> plugin! Please open <a href="' . admin_url('options-general.php?page=wpcaptcha') . '">Settings - Advanced Google reCaptcha</a> to set up your captcha and website protection settings.');

    update_option(WPCAPTCHA_POINTERS_KEY, $pointers);
  } // reset_pointers

  /**
   * Settings footer submit button HTML
   *
   * @since 5.0
   *
   * @return null
   */
  static function footer_save_button()
  {
    echo '<p class="submit">';
    echo '<button class="button button-primary button-large">' . esc_html__('Save Changes', 'advanced-google-recaptcha') . ' <i class="wpcaptcha-icon wpcaptcha-checkmark"></i></button>';
    echo '</p>';
  } // footer_save_button
} // class
