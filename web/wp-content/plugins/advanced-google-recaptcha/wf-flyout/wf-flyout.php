<?php

/**
 * Universal fly-out menu for WebFactory plugins
 * (c) WebFactory Ltd, 2023
 */


if (false == class_exists('wf_flyout')) {
  class wf_flyout
  {
    var $ver = 1.0;
    var $plugin_file = '';
    var $plugin_slug = '';
    var $config = array();


    function __construct($plugin_file)
    {
      $this->plugin_file = $plugin_file;
      $this->plugin_slug = basename(dirname($plugin_file));
      $this->load_config();

      if (!is_admin()) {
        return;
      } else {
        add_action('admin_init', array($this, 'init'));
      }
    } // __construct


    function load_config()
    {
      $config = array();
      require_once plugin_dir_path($this->plugin_file) . 'wf-flyout/config.php';

      $defaults = array(
        'plugin_screen' => '',
        'icon_border' => '#0000ff',
        'icon_right' => '40px',
        'icon_bottom' => '40px',
        'icon_image' => '',
        'icon_padding' => '2px',
        'icon_size' => '55px',
        'menu_accent_color' => '#ca4a1f',
        'custom_css' => '',
        'menu_items' => array(),
      );

      $config = array_merge($defaults, $config);
      if (!is_array($config['plugin_screen'])) {
        $config['plugin_screen'] = array($config['plugin_screen']);
      }

      $this->config = $config;
    } // load_config


    function is_plugin_screen()
    {
      $screen = get_current_screen();

      if (in_array($screen->id, $this->config['plugin_screen'])) {
        return true;
      } else {
        return false;
      }
    } // is_plugin_screen


    function init()
    {
      add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
      add_action('admin_head', array($this, 'admin_head'));
      add_action('admin_footer', array($this, 'admin_footer'));
    } // init


    function admin_enqueue_scripts()
    {
      if (false === $this->is_plugin_screen()) {
        return;
      }

      wp_enqueue_style('wf_flyout', plugin_dir_url($this->plugin_file) . 'wf-flyout/wf-flyout.css', array(), $this->ver);
      wp_enqueue_script('wf_flyout', plugin_dir_url($this->plugin_file) . 'wf-flyout/wf-flyout.js', array(), $this->ver, true);;
    } // admin_enqueue_scripts


    function admin_head()
    {
      if (false === $this->is_plugin_screen()) {
        return;
      }

      $out = '<style type="text/css">';
      $out .= '#wf-flyout {
        right: ' .  sanitize_text_field($this->config['icon_right']) . ';
        bottom: ' .  sanitize_text_field($this->config['icon_bottom']) . ';
      }';
      $out .= '#wf-flyout #wff-image-wrapper {
        border: ' .  sanitize_text_field($this->config['icon_border']) . ';
      }';
      $out .= '#wf-flyout #wff-button img {
        padding: ' .  sanitize_text_field($this->config['icon_padding']) . ';
        width: ' .  sanitize_text_field($this->config['icon_size']) . ';
        height: ' .  sanitize_text_field($this->config['icon_size']) . ';
      }';
      $out .= '#wf-flyout .wff-menu-item.accent {
        background: ' .  sanitize_text_field($this->config['menu_accent_color']) . ';
      }';
      $out .=  sanitize_text_field($this->config['custom_css']);
      $out .= '</style>';

      WPCaptcha_Utility::wp_kses_wf($out);
    } // admin_head


    function admin_footer()
    {
      if (false === $this->is_plugin_screen()) {
        return;
      }

      $out = '';
      $icons_url = plugin_dir_url($this->plugin_file) . 'wf-flyout/icons/';
      $default_link_item = array('class' => '', 'href' => '#', 'target' => '_blank', 'label' => '', 'icon' => '', 'data' => '');

      $out .= '<div id="wff-overlay"></div>';

      $out .= '<div id="wf-flyout">';

      $out .= '<a href="#" id="wff-button">';
      $out .= '<span class="wff-label">Open Quick Links</span>';
      $out .= '<span id="wff-image-wrapper">';
      $out .= '<img src="' . esc_url($icons_url . $this->config['icon_image']) . '" alt="Open Quick Links" title="Open Quick Links">';
      $out .= '</span>';
      $out .= '</a>';

      $out .= '<div id="wff-menu">';
      $i = 0;
      foreach (array_reverse($this->config['menu_items']) as $item) {
        $i++;
        $item = array_merge($default_link_item, $item);

        if (!empty($item['icon']) && substr($item['icon'], 0, 9) != 'dashicons') {
          $item['class'] .= ' wff-custom-icon';
          $item['class'] = trim($item['class']);
        }

        $out .= '<a ' . $item['data'] . ' href="' . esc_url($item['href']) . '" class="wff-menu-item wff-menu-item-' . $i . ' ' . esc_attr($item['class']) . '" target="_blank">';
        $out .= '<span class="wff-label visible">' . esc_html($item['label']) . '</span>';
        if (substr($item['icon'], 0, 9) == 'dashicons') {
          $out .= '<span class="dashicons ' . sanitize_text_field($item['icon']) . '"></span>';
        } elseif (!empty($item['icon'])) {
          $out .= '<span class="wff-icon"><img src="' . esc_url($icons_url . $item['icon']) . '"></span>';
        }
        $out .= '</a>';
      } // foreach
      $out .= '</div>'; // #wff-menu

      $out .= '</div>'; // #wf-flyout

      WPCaptcha_Utility::wp_kses_wf($out);
    } // admin_footer
  } // wf_flyout
} // if class exists
