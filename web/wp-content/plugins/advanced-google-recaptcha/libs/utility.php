<?php

/**
 * WP Captcha
 * https://getwpcaptcha.com/
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

use WFMaxMind\Db\Reader;

class WPCaptcha_Utility extends WPCaptcha
{
    /**
     * Display settings notice
     *
     * @param $redirect
     * @return bool
     */
    static function display_notice($message, $type = 'error', $code = 'advanced-google-recaptcha')
    {
        global $wp_settings_errors;

        $wp_settings_errors[] = array(
            'setting' => WPCAPTCHA_OPTIONS_KEY,
            'code'    => $code,
            'message' => $message,
            'type'    => $type
        );
        set_transient('settings_errors', $wp_settings_errors);
    } // display_notice

    /**
     * Empty cache in various 3rd party plugins
     *
     * @since 5.0
     *
     * @return null
     *
     */
    static function clear_3rdparty_cache()
    {
        if (function_exists('w3tc_pgcache_flush')) {
            w3tc_pgcache_flush();
        }
        if (function_exists('wp_cache_clean_cache')) {
            global $file_prefix;
            wp_cache_clean_cache($file_prefix);
        }
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
        if (class_exists('Endurance_Page_Cache')) {
            $epc = new Endurance_Page_Cache;
            $epc->purge_all();
        }
        if (method_exists('SG_CachePress_Supercacher', 'purge_cache')) {
            SG_CachePress_Supercacher::purge_cache(true);
        }

        if (class_exists('SiteGround_Optimizer\Supercacher\Supercacher')) {
            SiteGround_Optimizer\Supercacher\Supercacher::purge_cache();
        }
    } // empty_3rdparty_cache


    /**
     * Dismiss pointer
     *
     * @since 5.0
     *
     * @return null
     *
     */
    static function dismiss_pointer_ajax()
    {
        delete_option(WPCAPTCHA_POINTERS_KEY);
    }

    /**
     * checkbox helper function
     *
     * @since 5.0
     *
     * @return string checked HTML
     *
     */
    static function checked($value, $current, $echo = false)
    {
        $out = '';

        if (!is_array($current)) {
            $current = (array) $current;
        }

        if (in_array($value, $current)) {
            $out = ' checked="checked" ';
        }

        if ($echo) {
            WPCaptcha_Utility::wp_kses_wf($out);
        } else {
            return $out;
        }
    } // checked

    /**
     * Create toggle switch
     *
     * @since 5.0
     *
     * @return string Switch HTML
     *
     */
    static function create_toggle_switch($name, $options = array(), $output = true, $class = '')
    {
        $default_options = array('value' => '1', 'saved_value' => '', 'option_key' => $name);
        $options = array_merge($default_options, $options);

        $out = "\n";
        $out .= '<div class="toggle-wrapper">';
        $out .= '<input class="' . $class . '" type="checkbox" id="' . $name . '" ' . self::checked($options['value'], $options['saved_value']) . ' type="checkbox" value="' . $options['value'] . '" name="' . $options['option_key'] . '">';
        $out .= '<label for="' . $name . '" class="toggle"><span class="toggle_handler"></span></label>';
        $out .= '</div>';

        if ($output) {
            WPCaptcha_Utility::wp_kses_wf($out);
        } else {
            return $out;
        }
    } // create_toggle_switch

    /**
     * Get user IP
     *
     * @since 5.0
     *
     * @return string userip
     *
     */
    static function getUserIP($force_clear = false)
    {
        $options = WPCaptcha_Setup::get_options();
        $ip = '';

        if (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        if ($options['anonymous_logging'] == '1' && !$force_clear) {
            $ip = md5($ip);
        }

        return $ip;
    } // getUserIP

    /**
     * Create select options for select
     *
     * @since 5.0
     *
     * @param array $options options
     * @param string $selected selected value
     * @param bool $output echo, if false return html as string
     * @return string html with options
     */
    static function create_select_options($options, $selected = null, $output = true)
    {
        $out = "\n";

        foreach ($options as $tmp) {
            if ((is_array($selected) && in_array($tmp['val'], $selected)) || $selected == $tmp['val']) {
                $out .= "<option selected=\"selected\" value=\"{$tmp['val']}\" " . (isset($tmp['class']) ? "class=\"{$tmp['class']}\"" : "") . ">{$tmp['label']}&nbsp;</option>\n";
            } else {
                $out .= "<option value=\"{$tmp['val']}\" " . (isset($tmp['class']) ? "class=\"{$tmp['class']}\"" : "") . ">{$tmp['label']}&nbsp;</option>\n";
            }
        }

        if ($output) {
            WPCaptcha_Utility::wp_kses_wf($out);
        } else {
            return $out;
        }
    } //  create_select_options


    static function create_radio_group($name, $options, $selected = null, $output = true)
    {
        $out = "\n";

        foreach ($options as $tmp) {
            if ($selected == $tmp['val']) {
                $out .= "<label for=\"{$name}_{$tmp['val']}\" class=\"radio_wrapper\"><input id=\"{$name}_{$tmp['val']}\" name=\"{$name}\" type=\"radio\" checked=\"checked\" value=\"{$tmp['val']}\">{$tmp['label']}&nbsp;</option></label>\n";
            } else {
                $out .= "<label for=\"{$name}_{$tmp['val']}\" class=\"radio_wrapper\"><input id=\"{$name}_{$tmp['val']}\" name=\"{$name}\" type=\"radio\" value=\"{$tmp['val']}\">{$tmp['label']}&nbsp;</option></label>\n";
            }
        }

        if ($output) {
            WPCaptcha_Utility::wp_kses_wf($out);
        } else {
            return $out;
        }
    }

    /**
     * Parse user agent to add device icon and clean text
     *
     * @since 5.0
     *
     * @param string $user_agent
     * @return string $user_agent
     */
    static function parse_user_agent($user_agent = false)
    {
        if (!$user_agent) {
            $user_agent = array();
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $user_agent[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
        }

        $user_agent = new WhichBrowser\Parser($user_agent);

        $user_agent_string = '';
        if ($user_agent->isType('mobile')) {
            $user_agent_string .= '<i class="tooltip fas fa-mobile-alt" title="Phone"></i>';
        } else if ($user_agent->isType('tablet')) {
            $user_agent_string .= '<i class="tooltip fas fa-tablet-alt" title="Table"></i>';
        } else if ($user_agent->isType('desktop')) {
            $user_agent_string .= '<i class="tooltip fas fa-desktop" title="Desktop"></i>';
        } else {
            $user_agent_string .= '<i class="tooltip fas fa-robot" title="Bot"></i>';
        }

        if (isset($user_agent->browser) && isset($user_agent->browser->version)) {
            $browser_version = explode('.', $user_agent->browser->version->toString());
        } else {
            $browser_version = array('unknown');
        }

        if ($user_agent->os) {
            $os = $user_agent->os->toString();
        } else {
            $os = 'unknown';
        }

        if (isset($user_agent->browser) && isset($user_agent->browser->name)) {
            $browser_name = $user_agent->browser->name;
        } else {
            $browser_name = 'unknown';
        }

        $user_agent_string .= ' ' . $browser_name . ' ' . $browser_version[0] . ' on ' . $os;


        return $user_agent_string;
    } // parse_user_agent

    static function get_home_path()
    {

        if (!function_exists('get_home_path')) {

            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        return get_home_path();
    }

    static function wp_kses_wf($html)
    {
        add_filter('safe_style_css', function ($styles) {
            $styles_wf = array(
                'text-align',
                'margin',
                'color',
                'float',
                'border',
                'background',
                'background-color',
                'border-bottom',
                'border-bottom-color',
                'border-bottom-style',
                'border-bottom-width',
                'border-collapse',
                'border-color',
                'border-left',
                'border-left-color',
                'border-left-style',
                'border-left-width',
                'border-right',
                'border-right-color',
                'border-right-style',
                'border-right-width',
                'border-spacing',
                'border-style',
                'border-top',
                'border-top-color',
                'border-top-style',
                'border-top-width',
                'border-width',
                'caption-side',
                'clear',
                'cursor',
                'direction',
                'font',
                'font-family',
                'font-size',
                'font-style',
                'font-variant',
                'font-weight',
                'height',
                'letter-spacing',
                'line-height',
                'margin-bottom',
                'margin-left',
                'margin-right',
                'margin-top',
                'overflow',
                'padding',
                'padding-bottom',
                'padding-left',
                'padding-right',
                'padding-top',
                'text-decoration',
                'text-indent',
                'vertical-align',
                'width',
                'display',
            );

            foreach ($styles_wf as $style_wf) {
                $styles[] = $style_wf;
            }
            return $styles;
        });

        $allowed_tags = wp_kses_allowed_html('post');
        $allowed_tags['input'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'data-*' => true,
            'size' => true,
            'disabled' => true
        );

        $allowed_tags['textarea'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'data-*' => true,
            'cols' => true,
            'rows' => true,
            'disabled' => true,
            'autocomplete' => true
        );

        $allowed_tags['select'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'data-*' => true,
            'multiple' => true,
            'disabled' => true
        );

        $allowed_tags['option'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'selected' => true,
            'data-*' => true
        );
        $allowed_tags['optgroup'] = array(
            'type' => true,
            'style' => true,
            'class' => true,
            'id' => true,
            'checked' => true,
            'disabled' => true,
            'name' => true,
            'size' => true,
            'placeholder' => true,
            'value' => true,
            'selected' => true,
            'data-*' => true,
            'label' => true
        );

        $allowed_tags['a'] = array(
            'href' => true,
            'data-*' => true,
            'class' => true,
            'style' => true,
            'id' => true,
            'target' => true,
            'data-*' => true,
            'role' => true,
            'aria-controls' => true,
            'aria-selected' => true,
            'disabled' => true
        );

        $allowed_tags['div'] = array(
            'style' => true,
            'class' => true,
            'id' => true,
            'data-*' => true,
            'role' => true,
            'aria-labelledby' => true,
            'value' => true,
            'aria-modal' => true,
            'tabindex' => true
        );

        $allowed_tags['li'] = array(
            'style' => true,
            'class' => true,
            'id' => true,
            'data-*' => true,
            'role' => true,
            'aria-labelledby' => true,
            'value' => true,
            'aria-modal' => true,
            'tabindex' => true
        );

        $allowed_tags['span'] = array(
            'style' => true,
            'class' => true,
            'id' => true,
            'data-*' => true,
            'aria-hidden' => true
        );

        $allowed_tags['style'] = array(
            'class' => true,
            'id' => true,
            'type' => true,
            'style' => true
        );

        $allowed_tags['fieldset'] = array(
            'class' => true,
            'id' => true,
            'type' => true,
            'style' => true
        );

        $allowed_tags['link'] = array(
            'class' => true,
            'id' => true,
            'type' => true,
            'rel' => true,
            'href' => true,
            'media' => true,
            'style' => true
        );

        $allowed_tags['form'] = array(
            'style' => true,
            'class' => true,
            'id' => true,
            'method' => true,
            'action' => true,
            'data-*' => true,
            'style' => true
        );

        $allowed_tags['script'] = array(
            'class' => true,
            'id' => true,
            'type' => true,
            'src' => true,
            'style' => true
        );

        $allowed_tags['table'] = array(
            'class' => true,
            'id' => true,
            'type' => true,
            'cellpadding' => true,
            'cellspacing' => true,
            'border' => true,
            'style' => true
        );

        $allowed_tags['canvas'] = array(
            'class' => true,
            'id' => true,
            'style' => true
        );

        echo wp_kses($html, $allowed_tags);

        add_filter('safe_style_css', function ($styles) {
            $styles_wf = array(
                'text-align',
                'margin',
                'color',
                'float',
                'border',
                'background',
                'background-color',
                'border-bottom',
                'border-bottom-color',
                'border-bottom-style',
                'border-bottom-width',
                'border-collapse',
                'border-color',
                'border-left',
                'border-left-color',
                'border-left-style',
                'border-left-width',
                'border-right',
                'border-right-color',
                'border-right-style',
                'border-right-width',
                'border-spacing',
                'border-style',
                'border-top',
                'border-top-color',
                'border-top-style',
                'border-top-width',
                'border-width',
                'caption-side',
                'clear',
                'cursor',
                'direction',
                'font',
                'font-family',
                'font-size',
                'font-style',
                'font-variant',
                'font-weight',
                'height',
                'letter-spacing',
                'line-height',
                'margin-bottom',
                'margin-left',
                'margin-right',
                'margin-top',
                'overflow',
                'padding',
                'padding-bottom',
                'padding-left',
                'padding-right',
                'padding-top',
                'text-decoration',
                'text-indent',
                'vertical-align',
                'width'
            );

            foreach ($styles_wf as $style_wf) {
                if (($key = array_search($style_wf, $styles)) !== false) {
                    unset($styles[$key]);
                }
            }
            return $styles;
        });
    }
} // class
