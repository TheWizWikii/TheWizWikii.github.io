<?php
/**
 * Plugin Name: Easy Table of Contents
 * Plugin URI: https://tocwp.com/
 * Description: Adds a user friendly and fully automatic way to create and display a table of contents generated from the page content.
 * Version: 2.0.68.1
 * Author: Magazine3
 * Author URI: https://tocwp.com/
 * Text Domain: easy-table-of-contents
 * Domain Path: /languages
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright 2022  Magazine3  ( email : team@magazine3.in )
 *
 * Easy Table of Contents is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy Table of Contents; if not, see <http://www.gnu.org/licenses/>.
 *
 * @package  Easy Table of Contents
 * @category Plugin
 * @author   Magazine3
 * @version  2.0.68.1
 */

use Easy_Plugins\Table_Of_Contents\Debug;
use function Easy_Plugins\Table_Of_Contents\Cord\insertElementByPTag;
use function Easy_Plugins\Table_Of_Contents\Cord\insertElementByImgTag;
use function Easy_Plugins\Table_Of_Contents\Cord\mb_find_replace;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ezTOC' ) ) {

	/**
	 * Class ezTOC
	 */
	final class ezTOC {

		/**
		 * Current version.
		 *
		 * @since 1.0
		 * @var string
		 */
		const VERSION = '2.0.68.1';

		/**
		 * Stores the instance of this class.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @var ezTOC
		 */
		private static $instance;

		/**
		 * @since 2.0
		 * @var array
		 */
		private static $store = array();

		/**
		 * A dummy constructor to prevent the class from being loaded more than once.
		 *
		 * @access public
		 * @since  1.0
		 */
		public function __construct() { /* Do nothing here */ }

		/**
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @return ezTOC
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

				self::$instance = new self;

				self::defineConstants();
				self::includes();
				self::hooks();

				self::loadTextdomain();
			}

			return self::$instance;
		}

		/**
		 * Define the plugin constants.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		private static function defineConstants() {

			define( 'EZ_TOC_DIR_NAME', plugin_basename( dirname( __FILE__ ) ) );
			define( 'EZ_TOC_BASE_NAME', plugin_basename( __FILE__ ) );
			define( 'EZ_TOC_PATH', dirname( __FILE__ ) );
			define( 'EZ_TOC_URL', plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Includes the plugin dependency files.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		private static function includes() {

			require_once( EZ_TOC_PATH . '/includes/class-eztoc-option.php' );
			require_once(EZ_TOC_PATH. "/includes/public-helper-function.php" );

			if ( is_admin() ) {

				// This must be included after `class.options.php` because it depends on it methods.
				require_once( EZ_TOC_PATH . '/includes/class-eztoc-admin.php' );
				require_once(EZ_TOC_PATH. "/includes/helper-function.php" );
				require_once( EZ_TOC_PATH . '/includes/class-eztoc-pointers.php' );
			}

			require_once( EZ_TOC_PATH . '/includes/class-eztoc-post.php' );
            require_once( EZ_TOC_PATH . '/includes/class-eztoc-widget.php' );
			require_once( EZ_TOC_PATH . '/includes/class-eztoc-widgetsticky.php' );
			require_once( EZ_TOC_PATH . '/includes/class-debug.php' );
			require_once( EZ_TOC_PATH . '/includes/inc.functions.php' );
			require_once( EZ_TOC_PATH . '/includes/inc.cord-functions.php' );

			require_once( EZ_TOC_PATH . '/includes/inc.plugin-compatibility.php' );
		}

		/**
		 * Add the core action filter hook.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		private static function hooks() {

			add_action('admin_head', array( __CLASS__, 'addEditorButton' ));
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueueScripts' ) );
			add_action( 'wp_head', array( __CLASS__, 'ez_toc_inline_styles' ) );
			add_action( 'wp_head', array( __CLASS__, 'ez_toc_schema_sitenav_creator' ) );			

			if ( in_array( 'divi-machine/divi-machine.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'Fortunato Pro' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {
				add_option( 'ez-toc-post-content-core-level', false );
			}
						
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueueScriptsforExcludeCSS' ) );
			
			if( !self::checkBeaverBuilderPluginActive() ) {
				add_filter( 'the_content', array( __CLASS__, 'the_content' ), 100 );
				/*
				* Fix for toc not showing / links not working for StoreHub theme custom post types
				* https://github.com/ahmedkaludi/Easy-Table-of-Contents/issues/760
				*/
				add_filter('ilj_get_the_content',array( __CLASS__, 'the_content_storehub' ), 100 ); 
				
				if( defined('EASY_TOC_AMP_VERSION') ){
					add_filter( 'ampforwp_modify_the_content', array( __CLASS__, 'the_content' ) );
				}
				add_filter( 'term_description',  array( __CLASS__, 'toc_term_content_filter' ), 99,2);
				add_filter( 'woocommerce_taxonomy_archive_description_raw',  array( __CLASS__, 'toc_category_content_filter_woocommerce' ), 99,2);
				add_shortcode( 'ez-toc', array( __CLASS__, 'shortcode' ) );                                    
				add_shortcode( apply_filters( 'ez_toc_shortcode', 'toc' ), array( __CLASS__, 'shortcode' ) );
				add_shortcode( 'ez-toc-widget-sticky', array( __CLASS__, 'ez_toc_widget_sticky_shortcode' ) );
				add_action('wp_footer', array(__CLASS__, 'stickyToggleContent'));

			}
		}
	
		/**
		 * is_sidebar_hastoc function
		 * @since 2.0.51
		 * @static
		 * @return bool
		 */
		public static function is_sidebar_hastoc() {

			$status = false;

			$generate_toc_link_ids = ezTOC_Option::get('generate_toc_link_ids');
			if($generate_toc_link_ids){
				return true;
			}

			$widget_blocks = get_option( 'widget_block' );
			foreach( (array) $widget_blocks as $widget_block ) {
				if ( ! empty( $widget_block['content'] ) && ( has_shortcode( $widget_block['content'] , 'toc' ) || has_shortcode( $widget_block['content'] , 'ez-toc' ) || has_shortcode( $widget_block['content'] , 'ez-toc-widget-sticky' ) ) ) {					
					$status = true;
					break;
				}
			}
			if(!$status){
				$widget_texts = get_option( 'widget_text' );
				foreach( (array) $widget_texts as $widget_text ) {
					if ( ! empty( $widget_text['text'] ) && ( has_shortcode( $widget_text['text'] , 'toc' ) || has_shortcode( $widget_text['text'] , 'ez-toc' ) || has_shortcode( $widget_text['text'] , 'ez-toc-widget-sticky' ) ) ) {					
						$status = true;						
						break;
					}
				}

			}
			if(!$status){
				$widget_cust_htmls = get_option( 'widget_custom_html' );				
				foreach( (array) $widget_cust_htmls as $widget_cust_html ) {
					if ( ! empty( $widget_cust_html['content'] ) && ( has_shortcode( $widget_cust_html['content'] , 'toc' ) || has_shortcode( $widget_cust_html['content'] , 'ez-toc' ) || has_shortcode( $widget_cust_html['content'] , 'ez-toc-widget-sticky' ) ) ) {					
						$status = true;						
						break;
					}
				}

			}
			
			return apply_filters('ez_toc_sidebar_has_toc_filter', $status);
		}
                
        /**
	 * enqueueScriptsforExcludeCSS Method
	 * for adding toggle css on loading as CSS
	 * @access public
	 * @since  2.0.40
         * @static
	 */
        public static function enqueueScriptsforExcludeCSS()
        {
			if ( ezTOC_Option::get( 'exclude_css' ) && 'css' == ezTOC_Option::get( 'toc_loading' ) ) {
                                
				$cssChecked = '#ez-toc-container input[type="checkbox"]:checked + nav, #ez-toc-widget-container input[type="checkbox"]:checked + nav {opacity: 0;max-height: 0;border: none;display: none;}';
				wp_register_style( 'ez-toc-exclude-toggle-css', '', array(), ezTOC::VERSION );
				wp_enqueue_style( 'ez-toc-exclude-toggle-css', '', array(), ezTOC::VERSION );
				wp_add_inline_style( 'ez-toc-exclude-toggle-css', $cssChecked );
			}
        }
        
		/**
         * checkBeaverBuilderPluginActive Method
         * @since 2.0.34
		 * @return bool
		 */
		private static function checkBeaverBuilderPluginActive() {
			//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason : Nonce verification is not required here.
			if( has_action( 'the_content' ) && isset($_REQUEST['fl_builder'])) {
				return true;
			}
			return false;
		}
		/**
		 * Load the plugin translation.
		 *
		 * Credit: Adapted from Ninja Forms / Easy Digital Downloads.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @uses   apply_filters()
		 * @uses   get_locale()
		 * @uses   load_textdomain()
		 * @uses   load_plugin_textdomain()
		 *
		 * @return void
		 */
		public static function loadTextdomain() {

			// Plugin textdomain. This should match the one set in the plugin header.
			$domain = 'easy-table-of-contents';

			// Set filter for plugin's languages directory
			$languagesDirectory = apply_filters( "ez_{$domain}_languages_directory", EZ_TOC_DIR_NAME . '/languages/' );

			// Traditional WordPress plugin locale filter
			$locale   = apply_filters( 'plugin_locale', get_locale(), $domain );
			$fileName = sprintf( '%1$s-%2$s.mo', $domain, $locale );

			// Setup paths to current locale file
			$local  = $languagesDirectory . $fileName;
			$global = WP_LANG_DIR . "/{$domain}/" . $fileName;

			if ( file_exists( $global ) ) {

				// Look in global `../wp-content/languages/{$domain}/` folder.
				load_textdomain( $domain, $global );

			} elseif ( file_exists( $local ) ) {

				// Look in local `../wp-content/plugins/{plugin-directory}/languages/` folder.
				load_textdomain( $domain, $local );

			} else {

				// Load the default language files
				load_plugin_textdomain( $domain, false, $languagesDirectory );
			}
		}

		public static function ez_toc_inline_styles() {

			if ( ezTOC_Option::get( 'inline_css' ) ) {

				if ( self::is_enqueue_scripts_eligible() && function_exists('eztoc_read_file_contents')) {
					
					$screen_css = eztoc_read_file_contents( EZ_TOC_PATH . '/assets/css/screen.min.css' );				
					$screen_css .= self::InlineCountingCSS( ezTOC_Option::get( 'heading-text-direction', 'ltr' ) );
					$screen_css .= self::InlineCountingCSS( ezTOC_Option::get( 'heading-text-direction', 'ltr' ),'ez-toc-widget-direction','ez-toc-widget-container', 'counter', 'ez-toc-widget-container' );
					$screen_css .= self::inlineCSS();
					echo '<style id="ez-toc-inline-css">'.esc_html($screen_css).'</style>';

				}
				
			}
		}

		public static function ez_toc_schema_sitenav_creator(){
			global $eztoc_disable_the_content;
			if(ezTOC_Option::get( 'schema_sitenav_checkbox' ) == true){
				$eztoc_disable_the_content = true;
				$post = ezTOC::get( get_the_ID() );
				if($post){
					$items = $post->getTocTitleId();
					if(!empty($items)){
						$output_array = array();
						foreach($items as $item){
							$output_array[] = array(
								"@context" => "https://schema.org",
								"@type"    => "SiteNavigationElement",
								'@id'      => '#ez-toc',
								"name"     => wp_strip_all_tags($item['title']),
								"url"      => get_permalink() ."#". $item['id'],
							);
						}
						if(!empty($output_array)){
							$schema_opt = array();	
							$schema_opt['@context'] = "https://schema.org"; 
							$schema_opt['@graph']   = $output_array; 
							echo '<script type="application/ld+json" class="ez-toc-schema-markup-output">'.wp_json_encode( $schema_opt ).'</script>';
						}
						
					}
				}							
			}			
		}
		
		/**
		 * Call back for the `wp_enqueue_scripts` action.
		 *
		 * Register and enqueue CSS and javascript files for frontend.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public static function enqueueScripts() {

				$eztoc_post_id = get_the_ID();								
				// If SCRIPT_DEBUG is set and TRUE load the non-minified JS files, otherwise, load the minified files.
				$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';				

				if ( in_array( 'js_composer_salient/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {					
					$postMetaContent = get_post_meta( $eztoc_post_id, '_nectar_portfolio_extra_content',true );
					if( !empty( $postMetaContent ) ){
						update_option( 'ez-toc-post-meta-content', array( $eztoc_post_id => do_shortcode( $postMetaContent ) ) );
					}
				}
									
				// Register stylesheet which can be called later using wp_enqueue_style() 
				wp_register_style( 'ez-toc', EZ_TOC_URL . "assets/css/screen{$min}.css",array( ), ezTOC::VERSION );
				wp_register_style( 'ez-toc-sticky', EZ_TOC_URL . "assets/css/ez-toc-sticky{$min}.css", array(), self::VERSION );

				// Register scripts which can be called later using wp_enqueue_script() 																																
				$in_footer = true;
				if ( ezTOC_Option::get( 'load_js_in' ) == 'header' ) {
					$in_footer = false;
				}
				wp_register_script( 'ez-toc-sticky', EZ_TOC_URL . "assets/js/ez-toc-sticky{$min}.js", array( 'jquery'), ezTOC::VERSION . '-' . filemtime( EZ_TOC_PATH . "/assets/js/ez-toc-sticky{$min}.js" ), $in_footer );				
				wp_register_script( 'ez-toc-js-cookie', EZ_TOC_URL . "vendor/js-cookie/js.cookie{$min}.js", array(), '2.2.1', $in_footer );
				wp_register_script( 'ez-toc-jquery-sticky-kit', EZ_TOC_URL . "vendor/sticky-kit/jquery.sticky-kit{$min}.js", array( 'jquery' ), '1.9.2', $in_footer );                        			
				wp_register_script( 'ez-toc-js', EZ_TOC_URL . "assets/js/front{$min}.js", array( 'jquery', 'ez-toc-js-cookie', 'ez-toc-jquery-sticky-kit' ), ezTOC::VERSION . '-' . filemtime( EZ_TOC_PATH . "/assets/js/front{$min}.js" ), $in_footer );
				wp_register_script( 'ez-toc-scroll-scriptjs', apply_filters('ez_toc_smscroll_jsfile_filter',EZ_TOC_URL . "assets/js/smooth_scroll{$min}.js"), array( 'jquery' ), ezTOC::VERSION, $in_footer );
				self::localize_scripts();
																													
				if ( self::is_enqueue_scripts_eligible() ) {
					self::enqueue_registered_script();	
					self::enqueue_registered_style();	
					self::inlineMainCountingCSS();
					if ( in_array( 'js_composer/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
						self::inlineWPBakeryJS();
					}												
				}											
				
				if ( ezTOC_Option::get( 'sticky-toggle' ) ) {
					wp_enqueue_script( 'ez-toc-sticky');					
				}
				if ( ezTOC_Option::get( 'sticky-toggle' ) ) {
					wp_enqueue_style( 'ez-toc-sticky' );
					self::inlineStickyToggleCSS();				                				
				}

				/**
				 * Foodie Pro Theme Compatibility
				 * for working sticky toggle
				 * in right way
				 * @since 2.0.39
				 */
				if ( 'Foodie Pro' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {

					wp_register_style( 'ez-toc-foodie-pro', EZ_TOC_URL . "assets/css/foodie-pro{$min}.css",array(), ezTOC::VERSION );
					wp_enqueue_style( 'ez-toc-foodie-pro' );

				}

				/**
				 * Thrive Theme Builder Compatibility
				 * add inline custom CSS to remove double line
				 * on links of our Easy TOC container
				 * @since 2.0.38
				 */
				if ( 'Thrive Theme Builder' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {

					wp_register_style( 'ez-toc-thrive-theme-builder', EZ_TOC_URL . "assets/css/thrive-theme-builder{$min}.css",array(), ezTOC::VERSION );
					wp_enqueue_style( 'ez-toc-thrive-theme-builder' );

				}
				
		}
		
		/**
         * localize_scripts Method
         * Localize scripts
         *
         * @since  2.0.52
         * @static
         * @uses wp_localize_script()
         * @return void
         *
         */
		public static function localize_scripts(){
				global $ez_toc_shortcode_attr;				
			    $eztoc_post_id = get_the_ID();
				$js_vars = array();

				if ( ezTOC_Option::get( 'smooth_scroll' ) ) {
					$js_vars['smooth_scroll'] = true;
				}else{
					$js_vars['smooth_scroll'] = false;
				}

				if ( ezTOC_Option::get( 'show_heading_text' ) && ezTOC_Option::get( 'visibility' ) ) {

					$width = ezTOC_Option::get( 'width' ) !== 'custom' ? ezTOC_Option::get( 'width' ) : (wp_is_mobile() ? 'auto' : ezTOC_Option::get( 'width_custom' ) . ezTOC_Option::get( 'width_custom_units' ));
					$js_vars['visibility_hide_by_default'] = ezTOC_Option::get( 'visibility_hide_by_default' ) ? true : false;
                                
					if( true == get_post_meta( $eztoc_post_id, '_ez-toc-visibility_hide_by_default', true ) ){
						$js_vars['visibility_hide_by_default'] = true;
						$js_vars['width'] = esc_js( $width );
					}                
				}else{

					if(ezTOC_Option::get( 'visibility' )){
						$js_vars['visibility_hide_by_default'] = ezTOC_Option::get( 'visibility_hide_by_default' ) ? true : false;
						if( true == get_post_meta( $eztoc_post_id, '_ez-toc-visibility_hide_by_default', true ) ){
							$js_vars['visibility_hide_by_default'] = true;
						}
												
					}
				}

				$offset = wp_is_mobile() ? ezTOC_Option::get( 'mobile_smooth_scroll_offset', 0 ) : ezTOC_Option::get( 'smooth_scroll_offset', 30 );
				$js_vars['scroll_offset'] = esc_js( $offset );

				if ( ezTOC_Option::get( 'widget_affix_selector' ) ) {
					$js_vars['affixSelector'] = ezTOC_Option::get( 'widget_affix_selector' );
				}

				if (ezTOC_Option::get( 'toc_loading' ) != 'css') {
					$icon = ezTOC::getTOCToggleIcon();
					if( function_exists( 'ez_toc_pro_activation_link' ) ) {
							$icon = apply_filters('ez_toc_modify_icon',$icon);
					}
					$js_vars['fallbackIcon'] = $icon;
				}

				if(ezTOC_Option::get( 'collapsable_sub_hd' )){
					$js_vars['collapseSubHd'] = true;
				}

				if(ezTOC_Option::get( 'ajax_load_more' )){
					$js_vars['ajax_toggle'] = true;
				}

				if(isset($ez_toc_shortcode_attr['initial_view']) && $ez_toc_shortcode_attr['initial_view'] == 'show'){
					$js_vars['visibility_hide_by_default'] = false;
				}

				/** 
				 * If Chamomile theme is active then remove hamburger div from content
				 * @since 2.0.53
				 * */
				if ( 'Chamomile' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {
					$js_vars['chamomile_theme_is_on'] = true;
				}else{
					$js_vars['chamomile_theme_is_on'] = false;
				}
				
				if ( 0 < count( $js_vars ) ) {
					wp_localize_script( 'ez-toc-js', 'ezTOC', $js_vars );
					// smooth scroll js localization
					$js_scroll = array();
					$js_scroll['scroll_offset'] = esc_js( $offset );					
					$js_scroll['add_request_uri'] = ezTOC_Option::get( 'add_request_uri' ) ? true : false;
					
					if(ezTOC_Option::get( 'smooth_scroll' ) && ezTOC_Option::get( 'avoid_anch_jump' )){
						$js_scroll['JumpJsLinks'] = true;
					}
					wp_localize_script( 'ez-toc-scroll-scriptjs', 'eztoc_smooth_local', $js_scroll );						
				}
				//localize sticky js

				if ( ezTOC_Option::get( 'sticky-toggle' ) ) {
					$js_sticky = array();
					$js_sticky['close_on_link_click'] = false;
					if( (( 1 == ezTOC_Option::get('sticky-toggle-close-on-mobile', 0) || '1' == ezTOC_Option::get('sticky-toggle-close-on-mobile', 0) || true == ezTOC_Option::get('sticky-toggle-close-on-mobile', 0) ) && wp_is_mobile()) ||  ( 1 == ezTOC_Option::get('sticky-toggle-close-on-desktop', 0) || '1' == ezTOC_Option::get('sticky-toggle-close-on-desktop', 0) || true == ezTOC_Option::get('sticky-toggle-close-on-desktop', 0) ) ) {
						$js_sticky['close_on_link_click'] = true;
					}
					wp_localize_script( 'ez-toc-sticky', 'eztoc_sticky_local', $js_sticky );
				}

		}

		/**
         * enqueue_registered_style_and_script Method
         * Enqueue styles and scripts later after registered
         *
         * @since  2.0.52
         * @static
         * @uses wp_enqueue_style() & wp_enqueue_script()
         * @return void
         *
         */
		public static function enqueue_registered_style(){
			
			if(!ezTOC_Option::get( 'exclude_css' )){
				if ( ! ezTOC_Option::get( 'inline_css' ) ) {
					wp_enqueue_style( 'ez-toc' );
					$css = self::inlineCSS();
					wp_add_inline_style( 'ez-toc', $css );
				}
			}
												
		}
		/**
         * enqueue_registered_style_and_script Method
         * Enqueue styles and scripts later after registered
         *
         * @since  2.0.52
         * @static
         * @uses wp_enqueue_style() & wp_enqueue_script()
         * @return void
         *
         */
		public static function enqueue_registered_script(){

			if (ezTOC_Option::get( 'toc_loading' ) == 'js') {
					if ( ezTOC_Option::get( 'smooth_scroll' ) ) {
						wp_enqueue_script( 'ez-toc-scroll-scriptjs' );
					}					
					wp_enqueue_script( 'ez-toc-js' );
			}

		}
                        
        /**
         * inlineWPBakeryJS Method
         * Javascript code for WP Bakery Plugin issue for mobile screen
         *
         * @since  2.0.35
         * @static
         * @uses \wp_add_inline_script()
         * @return void
         *
         * ez-toc-list ez-toc-link
         * ez-toc-section
         */
        private static function inlineWPBakeryJS()
        {
			$sticky_js = '';
        
			if (wp_is_mobile()) {
				$sticky_js = "
					let ezTocStickyContainer = document.querySelector('#ez-toc-sticky-container');
					if (document.querySelectorAll('#ez-toc-sticky-container').length > 0) {
						let ezTocStickyContainerUL = ezTocStickyContainer.querySelectorAll('.ez-toc-link');
						for (let i = 0; i < ezTocStickyContainerUL.length; i++) {
							let anchorHREF = ezTocStickyContainerUL[i].getAttribute('href');
							ezTocStickyContainerUL[i].setAttribute('href', anchorHREF + '-' + uniqID);
						}
					}
				";
			}
	
			$inline_wp_bakery_js = "
				let mobileContainer = document.querySelector('#mobile.vc_row-fluid');
				if (document.querySelectorAll('#mobile.vc_row-fluid').length > 0) {
					let ezTocContainerUL = mobileContainer.querySelectorAll('.ez-toc-link');
					let uniqID = 'xs-sm-' + Math.random().toString(16).slice(2);
					for (let i = 0; i < ezTocContainerUL.length; i++) {
						let anchorHREF = ezTocContainerUL[i].getAttribute('href');
						let section = mobileContainer.querySelector('span.ez-toc-section' + anchorHREF);
						if (section) {
							section.setAttribute('id', anchorHREF.replace('#', '') + '-' + uniqID);
						}
						ezTocContainerUL[i].setAttribute('href', anchorHREF + '-' + uniqID);
					}
					{$sticky_js}
				}
			";
	
			wp_add_inline_script('ez-toc-js', $inline_wp_bakery_js);
        }

		/**
		 * Prints out inline CSS after the core CSS file to allow overriding core styles via options.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public static function inlineCSS() {

			$css = '';

			if('Chamomile' == apply_filters( 'current_theme', get_option( 'current_theme' ) )){
				$css .= '@media screen and (max-width: 1000px) {
				          #ez-toc-container nav{
				            display: block;        
				          }    
				        }';
			}

			if ( ! ezTOC_Option::get( 'exclude_css' ) ) {

				$css .= 'div#ez-toc-container .ez-toc-title {font-size: ' . esc_attr( ezTOC_Option::get( 'title_font_size', 120 ) ) . esc_attr( ezTOC_Option::get( 'title_font_size_units', '%' ) ) . ';}';
				$css .= 'div#ez-toc-container .ez-toc-title {font-weight: ' . esc_attr( ezTOC_Option::get( 'title_font_weight', 500 ) ) . ';}';
				$css .= 'div#ez-toc-container ul li {font-size: ' . esc_attr(ezTOC_Option::get( 'font_size', 95 )) . esc_attr(ezTOC_Option::get( 'font_size_units', '%' )) . ';}';
				$css .= 'div#ez-toc-container ul li {font-weight: ' . esc_attr( ezTOC_Option::get( 'font_weight', 500 ) ) . ';}';
				$css .= 'div#ez-toc-container nav ul ul li {font-size: ' . esc_attr( ezTOC_Option::get( 'child_font_size', 90 ) . esc_attr(ezTOC_Option::get( 'child_font_size_units', '%' ) )) . ';}';

				if ( ezTOC_Option::get( 'theme' ) === 'custom' || ezTOC_Option::get( 'width' ) != 'auto' ) {

					$css .= 'div#ez-toc-container {';

					if ( ezTOC_Option::get( 'theme' ) === 'custom' ) {

						$css .= 'background: ' . esc_attr( ezTOC_Option::get( 'custom_background_colour','#f9f9f9' ) ) . ';border: '.esc_attr( ezTOC_Option::get( 'custom_border_size' ,1) ).'px solid ' . esc_attr( ezTOC_Option::get( 'custom_border_colour' ,'#aaa') ) . ';';
					}

					if ( 'auto' !== ezTOC_Option::get( 'width' ) ) {

						$css .= 'width: ';

						if ( 'custom' !== ezTOC_Option::get( 'width' ) ) {

							$css .= ezTOC_Option::get( 'width' );

						} else {

							$css .= wp_is_mobile() ? 'auto' : ezTOC_Option::get( 'width_custom' ) . ezTOC_Option::get( 'width_custom_units' );
						}

						$css .= ';';
					}

					$css .= '}';
				}

				if ( 'custom' === ezTOC_Option::get( 'theme' ) ) {

					$css .= 'div#ez-toc-container p.ez-toc-title , #ez-toc-container .ez_toc_custom_title_icon , #ez-toc-container .ez_toc_custom_toc_icon {color: ' . esc_attr( ezTOC_Option::get( 'custom_title_colour' ) ) . ';}';
					$css .= 'div#ez-toc-container ul.ez-toc-list a {color: ' . esc_attr( ezTOC_Option::get( 'custom_link_colour' ) ) . ';}';
					$css .= 'div#ez-toc-container ul.ez-toc-list a:hover {color: ' . esc_attr( ezTOC_Option::get( 'custom_link_hover_colour' ) ) . ';}';
					$css .= 'div#ez-toc-container ul.ez-toc-list a:visited {color: ' . esc_attr( ezTOC_Option::get( 'custom_link_visited_colour' ) ) . ';}';
					
				}

				if(ezTOC_Option::get( 'headings-padding' )){
					$css .= self::inlineHeadingsPaddingCSS();	
				}
                                
			}

			return apply_filters('ez_toc_pro_inline_css',$css);
			
		}

        /**
         * inlineMainCountingCSS Method
         * for adding inlineCounting CSS
         * in wp_head in last
         * @since 2.0.37
         * @return void
        */
        public static function inlineMainCountingCSS() {
            $css = '';
            /**
             * RTL Direction
             * @since 2.0.33
            */
            $css .= self::InlineCountingCSS( ezTOC_Option::get( 'heading-text-direction', 'ltr' ) );
            $css .= self::InlineCountingCSS( ezTOC_Option::get( 'heading-text-direction', 'ltr' ),'ez-toc-widget-direction','ez-toc-widget-container', 'counter', 'ez-toc-widget-container' );

            if( ezTOC_Option::get( 'sticky-toggle' ) ) {
                $cssSticky = self::InlineCountingCSS( ezTOC_Option::get( 'heading-text-direction', 'ltr' ), 'ez-toc-sticky-toggle-direction', 'ez-toc-sticky-toggle-counter', 'counter', 'ez-toc-sticky-container' );
                wp_add_inline_style( 'ez-toc-sticky', $cssSticky );
            }
            /* End rtl direction */

            if ( ! ezTOC_Option::get( 'exclude_css' ) ) {
                  wp_add_inline_style( 'ez-toc', $css );
            }
        }

        /**
         * InlineCountingCSS Method
         * @since 2.0.33
         * @scope private
         * @static
         * @param string $direction
         * @param string $directionClass
         * @param string $class
         * @param string $counter
         * @param string $containerId
         * @return string
        */
		public static function InlineCountingCSS( $direction = 'ltr', $directionClass = 'ez-toc-container-direction', $class = 'ez-toc-counter',  $counter = 'counter', $containerId = 'ez-toc-container' ) {
			$list_type = ezTOC_Option::get( $counter, 'decimal' );
			if( $list_type != 'none' ) {
				$inlineCSS = '';
				$counterListAll = array_merge( ezTOC_Option::getCounterListDecimal(), ezTOC_Option::getCounterList_i18n() );
				$listTypesForCounting = array_keys( $counterListAll );
				$inlineCSS .= ".$directionClass {direction: $direction;}";
		
				$listAnchorPosition = 'before';
				$marginCSS = 'margin-right: .2em;';
				$floatPosition = 'float: left;';
				if( $direction == 'rtl' ) {
					$class .= '-rtl';
					$marginCSS = 'margin-left: .2em;';
					$floatPosition = 'float: right;';
				}
		
				$importantItem = '';
				if ( 'Edition Child' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {
					$importantItem = ' !important';
				}
		
				if( in_array( $list_type, $listTypesForCounting ) ) {
					if( $direction == 'rtl' ) {
						$length = 6;
						$counterRTLCSS = self::rtlCounterResetCSS( $length, $class );
						$counterRTLCSS .= self::rtlCounterIncrementCSS( $length, $class );
						$counterRTLCSS .= self::rtlCounterContentCSS( $length, $list_type, $class );
						$inlineCSS .= $counterRTLCSS;
					}
					if( $direction == 'ltr' ) {
						$counterPositionCSS = "";
						if( 'outside' == ezTOC_Option::get( 'counter-position' ) )
							$counterPositionCSS = "min-width: 22px;width: auto;";
		
						$inlineCSS .= ".$class ul{counter-reset: item $importantItem;}";
						$inlineCSS .= ".$class nav ul li a::$listAnchorPosition {content: counters(item, '.', $list_type) '. ';display: inline-block;counter-increment: item;flex-grow: 0;flex-shrink: 0;$marginCSS $floatPosition $counterPositionCSS}";
					}
				} else {
					$content = "  ";
					if( $list_type == 'numeric' || $list_type == 'cjk-earthly-branch' )
						$content = ". ";
		
					$counterPositionCSS = "";
					if( 'outside' == ezTOC_Option::get( 'counter-position' ) ) {
						$counterPositionCSS = "min-width: 15px;width: auto;";
						if( 'square' == $list_type )
							$counterPositionCSS = "min-width: 20px;width: auto;";
						if( 'cjk-earthly-branch' == $list_type  )
							$counterPositionCSS = "min-width: 25px;width: auto;";
					}   
					$counterContent = "counter(item, $list_type) '$content'";
					if( $list_type == '- ' )
						$counterContent = 'counter(item, none) "- "';
		
					$inlineCSS .= ".$class ul {direction: $direction;counter-reset: item $importantItem;}";
					$inlineCSS .= ".$class nav ul li a::$listAnchorPosition {content: $counterContent;$marginCSS counter-increment: item;flex-grow: 0;flex-shrink: 0;$floatPosition $counterPositionCSS}";
				}
				return $inlineCSS;
			}
		}		

        /**
         * rtlCounterResetCSS Method
         * @since 2.0.33
         * @scope private
         * @static
         * @param int $length
         * @param string $class
         * @return string
        */
        private static function rtlCounterResetCSS( $length = 6, $class = 'ez-toc-counter-rtl' )
        {
            if ($length < 6) {
                $length = 6;
            }
            $counterResetCSS = "";
            for ($i = 1; $i <= $length; $i++) {
                $ul = [];
                for ($j = 1; $j <= $i; $j++) {
                    $ul[$j] = "ul";
                }
                $ul = implode(" ", $ul);
                $items = [];
                for ($j = $i; $j <= $length; $j++) {
                    $items[$j] = "item-level$j";
                }
                $items = implode(", ", $items);
                $counterResetCSS .= ".$class $ul {direction: rtl;counter-reset: $items;}";
            }
            return $counterResetCSS;
        }

        /**
         * rtlCounterIncrementCSS Method
         * @since 2.0.33
         * @scope private
         * @static
         * @param int $length
         * @param string $class
         * @return string
        */
        private static function rtlCounterIncrementCSS( $length = 6, $class = 'ez-toc-counter-rtl' )
        {
            if ($length < 6) {
                $length = 6;
            }
            $counterIncrementCSS = "";
            for ($i = 1; $i <= $length; $i++) {
                $ul = [];
                for ($j = 1; $j <= $i; $j++) {
                    $ul[$j] = "ul";
                }
                $ul = implode(" ", $ul);
                $item = "item-level$i";
                $counterIncrementCSS .= ".$class $ul li {counter-increment: $item;}";
            }
            return $counterIncrementCSS;
        }

        /**
         * rtlCounterContentCSS Method
         * @since 2.0.33
         * @scope private
         * @static
         * @param int $length
         * @param string $list_type
         * @param string $class
         * @return string
        */
        private static function rtlCounterContentCSS( $length = 6, $list_type = 'decimal', $class = 'ez-toc-counter-rtl' )
        {
			$counterPositionCSS = "";
			if( 'outside' == ezTOC_Option::get( 'counter-position' ) )
				$counterPositionCSS = "min-width: 22px;width: auto;";
            if ($length < 6) {
                $length = 6;
            }
            $counterContentCSS = "";
            for ($i = 1; $i <= $length; $i++) {
                $ul = [];
                for ($j = 1; $j <= $i; $j++) {
                    $ul[$j] = "ul";
                }
                $ul = implode(" ", $ul);
                $items = [];

                $cnt = $i;
                for ($j = 1; $j <= $i; $j++) {
                    $items[$cnt] = "counter(item-level$cnt, $list_type)";
                    $cnt--;
                }
                $items = implode(' "." ', $items);
                $counterContentCSS .= ".$class nav $ul li a::before {content: $items '. ';float: right;margin-left: 0.2rem;flex-grow: 0;flex-shrink: 0; $counterPositionCSS }";
            }
            return $counterContentCSS;
        }

		/**
         * inlineHeadingsPaddingCSS Method
         *
         * @since  2.0.48
         * @static
         */
		private static function inlineHeadingsPaddingCSS() 
		{
			$padding_top = ezTOC_Option::get( 'headings-padding-top' );
			$padding_bottom = ezTOC_Option::get( 'headings-padding-bottom' );
			$padding_left = ezTOC_Option::get( 'headings-padding-left' );
			$padding_right = ezTOC_Option::get( 'headings-padding-right' );
		
			$padding_top = ! empty( $padding_top ) && $padding_top !== '0' ? $padding_top . ezTOC_Option::get( 'headings-padding-top_units' ) : '0';
			$padding_bottom = ! empty( $padding_bottom ) && $padding_bottom !== '0' ? $padding_bottom . ezTOC_Option::get( 'headings-padding-bottom_units' ) : '0';
			$padding_left = ! empty( $padding_left ) && $padding_left !== '0' ? $padding_left . ezTOC_Option::get( 'headings-padding-left_units' ) : '0';
			$padding_right = ! empty( $padding_right ) && $padding_right !== '0' ? $padding_right . ezTOC_Option::get( 'headings-padding-right_units' ) : '0';
		
			return sprintf(
				'ul.ez-toc-list a.ez-toc-link { padding: %s %s %s %s; }',
				esc_attr( $padding_top ),
				esc_attr( $padding_right ),
				esc_attr( $padding_bottom ),
				esc_attr( $padding_left )
			);
		}
		

        /**
         * inlineStickyToggleCSS Method
         * Prints out inline Sticky Toggle CSS after the core CSS file to allow overriding core styles via options.
         *
         * @since  2.0.32
         * @static
         */
        private static function inlineStickyToggleCSS()
        {
            $custom_width = 'width: auto;';
            if (ezTOC_Option::get('sticky-toggle-width') == 'custom' && !empty(ezTOC_Option::get(
                    'sticky-toggle-width-custom'
                ))) {
                $custom_width = 'width: ' . ezTOC_Option::get('sticky-toggle-width-custom') . ezTOC_Option::get( 'sticky-toggle-width-custom_units' ) . ';' . PHP_EOL;
            }
            $custom_height = 'height: 100vh;';
            if (ezTOC_Option::get('sticky-toggle-height') == 'custom' && !empty(ezTOC_Option::get(
                    'sticky-toggle-height-custom'
                ))) {
                $custom_height = 'height: ' . ezTOC_Option::get('sticky-toggle-height-custom') . ezTOC_Option::get( 'sticky-toggle-height-custom_units' ). ';' . PHP_EOL;
            }
            
            $topMarginStickyContainer = '65px';
            if ( ezTOC_Option::get( 'show_heading_text' ) ) {
                $toc_title = ezTOC_Option::get( 'heading_text' );
                if( strlen($toc_title) > 20 ) {
                    $topMarginStickyContainer = '70px';
                }
                if( strlen($toc_title) > 40 ) {
                    $topMarginStickyContainer = '80px';
                }
                if( strlen($toc_title) > 60 ) {
                    $topMarginStickyContainer = '90px';
                }
            }
			$stickyToggleAlignTop="8%";
			$stickyToggleAlignChk = ezTOC_Option::get( 'sticky-toggle-alignment' ); 
			if ( !empty($stickyToggleAlignChk) ) {
				
				if($stickyToggleAlignChk  == 'middle'){
					$stickyToggleAlignTop = '45%';
				}
				if($stickyToggleAlignChk  == 'bottom'){
					$stickyToggleAlignTop = '75%';
				}

			}

			$stickyBgColor="#fff";
			$stickyHeadTxtColor="#111";
			$stickyHeadBgColor="#fff";
			$stickyAddlCss="";
			$stickyHeadTxtWeight =600;
			$stickyHeadTxtSize =18;
		
			$stickyAddlCss = apply_filters('ez_toc_sticky_pro_css', $stickyAddlCss );
		
            $inlineStickyToggleCSS = ".ez-toc-sticky-fixed{position: fixed;top: 0;left: 0;z-index: 999999;width: auto;max-width: 100%;} .ez-toc-sticky-fixed .ez-toc-sidebar {position: relative;top: auto;{$custom_width};box-shadow: 1px 1px 10px 3px rgb(0 0 0 / 20%);box-sizing: border-box;padding: 20px 30px;background: {$stickyBgColor};margin-left: 0 !important; {$custom_height} overflow-y: auto;overflow-x: hidden;} .ez-toc-sticky-fixed .ez-toc-sidebar #ez-toc-sticky-container { padding: 0px;border: none;margin-bottom: 0;margin-top: {$topMarginStickyContainer};} #ez-toc-sticky-container a { color: #000;} .ez-toc-sticky-fixed .ez-toc-sidebar .ez-toc-sticky-title-container {border-bottom-color: #EEEEEE;background-color: {$stickyHeadBgColor};padding:15px;border-bottom: 1px solid #e5e5e5;width: 100%;position: absolute;height: auto;top: 0;left: 0;z-index: 99999999;} .ez-toc-sticky-fixed .ez-toc-sidebar .ez-toc-sticky-title-container .ez-toc-sticky-title {font-weight: {$stickyHeadTxtWeight};font-size: {$stickyHeadTxtSize}px;color: {$stickyHeadTxtColor};} .ez-toc-sticky-fixed .ez-toc-close-icon {-webkit-appearance: none;padding: 0;cursor: pointer;background: 0 0;border: 0;float: right;font-size: 30px;font-weight: 600;line-height: 1;position: relative;color: {$stickyHeadTxtColor};top: -2px;text-decoration: none;} .ez-toc-open-icon {position: fixed;left: 0px;top:{$stickyToggleAlignTop};text-decoration: none;font-weight: bold;padding: 5px 10px 15px 10px;box-shadow: 1px -5px 10px 5px rgb(0 0 0 / 10%);background-color: {$stickyHeadBgColor};color:{$stickyHeadTxtColor};display: inline-grid;line-height: 1.4;border-radius: 0px 10px 10px 0px;z-index: 999999;} .ez-toc-sticky-fixed.hide {-webkit-transition: opacity 0.3s linear, left 0.3s cubic-bezier(0.4, 0, 1, 1);-ms-transition: opacity 0.3s linear, left 0.3s cubic-bezier(0.4, 0, 1, 1);-o-transition: opacity 0.3s linear, left 0.3s cubic-bezier(0.4, 0, 1, 1);transition: opacity 0.3s linear, left 0.3s cubic-bezier(0.4, 0, 1, 1);left: -100%;} .ez-toc-sticky-fixed.show {-webkit-transition: left 0.3s linear, left 0.3s easy-out;-moz-transition: left 0.3s linear;-o-transition: left 0.3s linear;transition: left 0.3s linear;left: 0;} .ez-toc-open-icon span.arrow { font-size: 18px; } .ez-toc-open-icon span.text {font-size: 13px;writing-mode: vertical-rl;text-orientation: mixed;} @media screen  and (max-device-width: 640px) {.ez-toc-sticky-fixed .ez-toc-sidebar {min-width: auto;} .ez-toc-sticky-fixed .ez-toc-sidebar.show { padding-top: 35px; } .ez-toc-sticky-fixed .ez-toc-sidebar #ez-toc-sticky-container { min-width: 100%; } }{$stickyAddlCss}";

			if( 'right' == ezTOC_Option::get( 'sticky-toggle-position', 'left') ) {
				$inlineStickyToggleCSS = ".ez-toc-sticky-fixed { position: fixed;top: 0;right: 0;z-index: 999999;width: auto;max-width: 100%;} .ez-toc-sticky-fixed .ez-toc-sidebar { position: relative;top: auto;width: auto !important;height: 100%;box-shadow: 1px 1px 10px 3px rgb(0 0 0 / 20%);box-sizing: border-box;padding: 20px 30px;background: {$stickyBgColor};margin-left: 0 !important;height: auto;overflow-y: auto;overflow-x: hidden; {$custom_height} } .ez-toc-sticky-fixed .ez-toc-sidebar #ez-toc-sticky-container { {$custom_width};padding: 0px;border: none;margin-bottom: 0;margin-top: {$topMarginStickyContainer};} #ez-toc-sticky-container a { color: #000; } .ez-toc-sticky-fixed .ez-toc-sidebar .ez-toc-sticky-title-container {border-bottom-color: #EEEEEE;background-color: {$stickyHeadBgColor};padding:15px;border-bottom: 1px solid #e5e5e5;width: 100%;position: absolute;height: auto;top: 0;left: 0;z-index: 99999999;} .ez-toc-sticky-fixed .ez-toc-sidebar .ez-toc-sticky-title-container .ez-toc-sticky-title { font-weight: {$stickyHeadTxtWeight}; font-size: {$stickyHeadTxtSize}px; color: {$stickyHeadTxtColor}; } .ez-toc-sticky-fixed .ez-toc-close-icon{-webkit-appearance:none;padding:0;cursor:pointer;background:0 0;border:0;float:right;font-size:30px;font-weight:600;line-height:1;position:relative;color:{$stickyHeadTxtColor};top:-2px;text-decoration:none}.ez-toc-open-icon{position:fixed;right:0;top:{$stickyToggleAlignTop};text-decoration:none;font-weight:700;padding:5px 10px 15px;box-shadow:1px -5px 10px 5px rgb(0 0 0 / 10%);background-color:{$stickyHeadBgColor};color:{$stickyHeadTxtColor};display:inline-grid;line-height:1.4;border-radius:10px 0 0 10px;z-index:999999}.ez-toc-sticky-fixed.hide{-webkit-transition:opacity .3s linear,right .3s cubic-bezier(.4, 0, 1, 1);-ms-transition:opacity .3s linear,right .3s cubic-bezier(.4, 0, 1, 1);-o-transition:opacity .3s linear,right .3s cubic-bezier(.4, 0, 1, 1);transition:opacity .3s linear,right .3s cubic-bezier(.4, 0, 1, 1);right:-100%}.ez-toc-sticky-fixed.show{-moz-transition:right .3s linear;-o-transition:right .3s linear;transition:right .3s linear;right:0}.ez-toc-open-icon span.arrow{font-size:18px}.ez-toc-open-icon span.text{font-size:13px;writing-mode:vertical-lr;text-orientation:mixed;-webkit-transform:rotate(180deg);-moz-transform:rotate(180deg);-ms-transform:rotate(180deg);-o-transform:rotate(180deg);transform:rotate(180deg)}@media screen and (max-device-width:640px){.ez-toc-sticky-fixed .ez-toc-sidebar{min-width:auto}.ez-toc-sticky-fixed .ez-toc-sidebar.show{padding-top:35px}.ez-toc-sticky-fixed .ez-toc-sidebar #ez-toc-sticky-container{min-width:100%}}{$stickyAddlCss}";
			}
			wp_add_inline_style( 'ez-toc-sticky', $inlineStickyToggleCSS );
		}
				
		public static function is_enqueue_scripts_eligible( ) {

			$isEligible = self::is_eligible( get_post() );

			if($isEligible){
				if(!ez_toc_auto_device_target_status()){
					$isEligible = false;
				}
			}

			if(!$isEligible){
				if( self::is_sidebar_hastoc() || is_active_widget( false, false, 'ezw_tco' ) || is_active_widget( false, false, 'ez_toc_widget_sticky' ) || get_post_meta( get_the_ID(), '_nectar_portfolio_extra_content',true )){
					$isEligible = true;
				}
			}

			return $isEligible;
		}
		/**
		 * Returns true if the table of contents is eligible to be printed, false otherwise.
		 *
		 * NOTE: Must bve use only within the loop.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param WP_Post $post
		 *
		 * @return bool
		 */
		public static function is_eligible( $post ) {
			
			if ( empty( $post ) || ! $post instanceof WP_Post ) {

				Debug::log( 'not_instance_of_post', 'Not an instance if `WP_Post`.', $post );
				return false;
			}
                        
			/**
			 * Easy TOC Run On Amp Pages Check
			 * @since 2.0.46
			 */
			if ( ( ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) !== false && 0 == ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) || '0' == ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) || false == ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) ) && !ez_toc_non_amp() ) {
				Debug::log( 'non_amp', 'Is frontpage, TOC is not enabled.', false );
				return false;                            
			}

			/**
			 * New Restriction
			 * @since 2.0.59
			 */
			if( ezTOC_Option::get( 'restrict_url_text' ) && ezTOC_Option::get( 'restrict_url_text' ) != '' ){
				$all_urls = nl2br(ezTOC_Option::get( 'restrict_url_text' ));
				$all_urls = str_replace('<br />', '', $all_urls);
				$urls_arr = explode(PHP_EOL, $all_urls);
				if(is_array($urls_arr)){
					foreach ($urls_arr as $url_arr) {
						if ( isset($_SERVER['REQUEST_URI']) && false !== strpos( $_SERVER['REQUEST_URI'], trim($url_arr) ) ) {
							Debug::log( 'is_restricted_path', 'In restricted path, post not eligible.', ezTOC_Option::get( 'restrict_path' ) );
							return false;
						}
					}
				}
			}
						
			if ( has_shortcode( $post->post_content, apply_filters( 'ez_toc_shortcode', 'toc' ) ) || has_shortcode( $post->post_content, 'ez-toc' ) ) {
				Debug::log( 'has_ez_toc_shortcode', 'Has instance of shortcode.', true );
				return true;
			}
                        
			if ( is_front_page() && ! ezTOC_Option::get( 'include_homepage' ) ) {
				Debug::log( 'is_front_page', 'Is frontpage, TOC is not enabled.', false );
				return false;
			}

			$type = get_post_type( $post->ID );

			Debug::log( 'current_post_type', 'Post type is.', $type );

			$enabled = in_array( $type, ezTOC_Option::get( 'enabled_post_types', array() ), true );
			$insert  = in_array( $type, ezTOC_Option::get( 'auto_insert_post_types', array() ), true );

			Debug::log( 'is_supported_post_type', 'Is supported post type?', $enabled );
			Debug::log( 'is_auto_insert_post_type', 'Is auto insert for post types?', $insert );

			if ( $insert || $enabled ) {

				if ( ezTOC_Option::get( 'restrict_path' ) ) {

					/**
					 * @link https://wordpress.org/support/topic/restrict-path-logic-does-not-work-correctly?
					 */
					if ( isset($_SERVER['REQUEST_URI']) && false !== strpos( ezTOC_Option::get( 'restrict_path' ), $_SERVER['REQUEST_URI'] ) ) {

						Debug::log( 'is_restricted_path', 'In restricted path, post not eligible.', ezTOC_Option::get( 'restrict_path' ) );
						return false;

					} else {

						Debug::log( 'is_not_restricted_path', 'Not in restricted path, post is eligible.', ezTOC_Option::get( 'restrict_path' ) );
						return true;
					}

				} else {

					if ( $insert && 1 === (int) get_post_meta( $post->ID, '_ez-toc-disabled', true ) ) {

						Debug::log( 'is_auto_insert_disable_post_meta', 'Auto insert enabled and disable TOC is enabled in post meta.', false );
						return false;

					} elseif ( $insert && 0 === (int) get_post_meta( $post->ID, '_ez-toc-disabled', true ) ) {

						Debug::log( 'is_auto_insert_enabled_post_meta', 'Auto insert enabled and disable TOC is not enabled in post meta.', true );
						return true;

					} elseif ( $enabled && 1 === (int) get_post_meta( $post->ID, '_ez-toc-insert', true ) ) {

						Debug::log( 'is_supported_post_type_disable_insert_post_meta', 'Supported post type and insert TOC is enabled in post meta.', true );
						return true;

					} elseif ( $enabled && $insert ) {

						Debug::log( 'supported_post_type_and_auto_insert', 'Supported post type and auto insert TOC is enabled.', true );
						return true;
					}

					Debug::log( 'not_auto_insert_or_not_supported_post_type', 'Not supported post type or insert TOC is disabled.', false );
					return false;
				}

			} else {

				Debug::log( 'not_auto_insert_and_not_supported post_type', 'Not supported post type and do not auto insert TOC.', false );
				return false;
			}
		}

		/**
		 * Get TOC from store and if not in store process post and add it to the store.
		 *
		 * @since 2.0
		 *
		 * @param int $id
		 *
		 * @return ezTOC_Post|null
		 */
		public static function get( $id ) {

			$post = null;

			if ( isset( self::$store[ $id ] ) && self::$store[ $id ] instanceof ezTOC_Post && !in_array( 'js_composer_salient/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

				$post = self::$store[ $id ];
			} else {
				
				$post_id = ! empty( $id ) ? $id : get_the_ID();
				$post = ezTOC_Post::get( $post_id );

				if ( $post instanceof ezTOC_Post ) {

					self::$store[ $id ] = $post;
				}
			}

			return $post;
		}

        /**
         * Callback for the registered shortcode `[ez-toc-widget-sticky]`
         *
         * NOTE: Shortcode is run before the callback @see ezTOC::the_content() for the `the_content` filter
         *
         * @access private
         * @since  2.0.41
         *
         * @param array|string $atts    Shortcode attributes array or empty string.
         * @param string       $content The enclosed content (if the shortcode is used in its enclosing form)
         * @param string       $tag     Shortcode name.
         *
         * @return string
         */
        public static function ez_toc_widget_sticky_shortcode( $atts, $content, $tag ) {             global $wp_widget_factory;

            if ( 'ez-toc-widget-sticky' == $tag ) {
    
                extract( shortcode_atts( array(
                    'highlight_color' => '#ededed',
                    'title' => 'Table of Contents',
                    'advanced_options' => '',
                    'scroll_fixed_position' => 30,
                    'sidebar_width' => 'auto',
                    'sidebar_width_size_unit' => 'none',
                    'fixed_top_position' => 30,
                    'fixed_top_position_size_unit' => 'px',
                    'navigation_scroll_bar' => 'on',
                    'scroll_max_height' => 'auto',
                    'scroll_max_height_size_unit' => 'none',
                    'ez_toc_widget_sticky_before_widget_container' => '',
                    'ez_toc_widget_sticky_before_widget' => '',
                    'ez_toc_widget_sticky_before' => '',
                    'ez_toc_widget_sticky_after' => '',
                    'ez_toc_widget_sticky_after_widget' => '',
                    'ez_toc_widget_sticky_after_widget_container' => '',
                ), $atts ) );

                $widget_name = esc_html( 'ezTOC_WidgetSticky' );
                
                $instance = array(
                    'title' => ( ! empty ( $title ) ) ? $title : '',
					'sidebar_sticky_title' => ( ! empty ( $title ) ) ? $title : '',
                    'highlight_color' => ( ! empty ( $highlight_color ) ) ? $highlight_color : '#ededed',
                    'advanced_options' => ( ! empty ( $advanced_options ) ) ? $advanced_options : '',
                    'scroll_fixed_position' => ( ! empty ( $scroll_fixed_position ) ) ? ( int ) $scroll_fixed_position : 30,
                    'sidebar_width' => ( ! empty ( $sidebar_width ) ) ? ( 'auto' == $sidebar_width ) ? $sidebar_width : ( int ) wp_strip_all_tags ( $sidebar_width ) : 'auto',
                    'sidebar_width_size_unit' => ( ! empty ( $sidebar_width_size_unit ) ) ? $sidebar_width_size_unit : 'none',
                    'fixed_top_position' => ( ! empty ( $fixed_top_position ) ) ? ( 'auto' == $fixed_top_position ) ? $fixed_top_position : ( int ) wp_strip_all_tags ( $fixed_top_position ) : 30,
                    'fixed_top_position_size_unit' => ( ! empty ( $fixed_top_position_size_unit ) ) ? $fixed_top_position_size_unit : 'px',
                    'navigation_scroll_bar' => ( ! empty ( $navigation_scroll_bar ) ) ? $navigation_scroll_bar : 'on',
                    'scroll_max_height' => ( ! empty ( $scroll_max_height ) ) ? ( 'auto' == $scroll_max_height ) ? $scroll_max_height : ( int ) wp_strip_all_tags ( $scroll_max_height ) : 'auto',
                    'scroll_max_height_size_unit' => ( ! empty ( $scroll_max_height_size_unit ) ) ? $scroll_max_height_size_unit : 'none',
                );
                
                if ( !is_a( $wp_widget_factory->widgets[ $widget_name ], 'WP_Widget' ) ):
                    $wp_class = 'WP_Widget_' . ucwords(strtolower($class));

                    if (!is_a($wp_widget_factory->widgets[$wp_class], 'WP_Widget')):
						/* translators: %s: Widget class name */
                        return '<p>'.sprintf(esc_html__("%s: Widget class not found. Make sure this widget exists and the class name is correct","easy-table-of-contents"),'<strong>'.$class.'</strong>').'</p>';
                    else:
                        $class = $wp_class;
                    endif;
                endif;

                $id = uniqid( time() );
                ob_start();
                the_widget( $widget_name, $instance, array(
                    'widget_id' => 'ez-toc-widget-sticky-' . $id,
                    'ez_toc_widget_sticky_before_widget_container' => $ez_toc_widget_sticky_before_widget_container,
                    'ez_toc_widget_sticky_before_widget' => $ez_toc_widget_sticky_before_widget,
                    'ez_toc_widget_sticky_before' => $ez_toc_widget_sticky_before,
                    'ez_toc_widget_sticky_after' => $ez_toc_widget_sticky_after,
                    'ez_toc_widget_sticky_after_widget' => $ez_toc_widget_sticky_after_widget,
                    'ez_toc_widget_sticky_after_widget_container' => $ez_toc_widget_sticky_after_widget_container,
                    ) 
                );
                $output = ob_get_contents();
                ob_end_clean();
                return $output;
            }
        }
                
		/**
		 * Callback for the registered shortcode `[ez-toc]`
		 *
		 * NOTE: Shortcode is run before the callback @see ezTOC::the_content() for the `the_content` filter
		 *
		 * @access private
		 * @since  1.3
		 *
		 * @param array|string $atts    Shortcode attributes array or empty string.
		 * @param string       $content The enclosed content (if the shortcode is used in its enclosing form)
		 * @param string       $tag     Shortcode name.
		 *
		 * @return string
		 */
		public static function shortcode( $atts, $content, $tag ) {
				global $ez_toc_shortcode_attr;
				$ez_toc_shortcode_attr = $atts;
				$html = '';
				
				if(!ez_toc_shortcode_enable_support_status($atts)){
					return $html;
				}
				if( ( ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) !== false && 0 == ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) || '0' == ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) || false == ezTOC_Option::get( 'toc-run-on-amp-pages', 1 ) ) && !ez_toc_non_amp() ){
					return $html;
				}
				//Enqueue css and styles if that has not been added by wp_enqueue_scripts			
				self::enqueue_registered_script();	
				self::enqueue_registered_style();	
				self::inlineMainCountingCSS();		
				$pid = (function_exists('get_queried_object_id') && class_exists('Storyhub'))?get_queried_object_id():get_the_ID();		

				$post_id = isset( $atts['post_id'] ) ? (int) $atts['post_id'] : $pid;																					
																				
				$post = self::get( $post_id );

				if ( ! $post instanceof ezTOC_Post ) {

						Debug::log( 'not_instance_of_post', 'Not an instance if `WP_Post`.', $pid );

						return Debug::log()->appendTo( $content );
				}
									
				$options =  array();
				if (isset($atts["header_label"])) {
					$options['header_label'] = $atts["header_label"];
				}
				if (isset($atts["display_header_label"]) && $atts["display_header_label"] == "no") {
					$options['no_label'] = true;
				}
				if (isset($atts["toggle_view"]) && $atts["toggle_view"] == "no") {
					$options['no_toggle'] = true;
				}
				if (isset($atts["initial_view"]) && $atts["initial_view"] == 'hide') {
					$options['visibility_hide_by_default'] = true;
				}
				if (isset($atts["initial_view"]) && $atts["initial_view"] == 'show') {
					$options['visibility_hide_by_default'] = false;
				}
				if (isset($atts["display_counter"]) && $atts["display_counter"] == "no") {
					$options['no_counter'] = true;
				}
				if (isset($atts["view_more"]) && $atts["view_more"] > 0) {
					$options['view_more'] = $atts["view_more"];
				}
				$html = count($options) > 0 ? $post->getTOC($options) : $post->getTOC();			
				
				return apply_filters( 'eztoc_shortcode_final_toc_html', $html );
		}

		/**
		 * Whether or not apply `the_content` filter.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		private static function maybeApplyTheContentFilter() {

			$apply = true;

			global $wp_current_filter;

			// Do not execute if root current filter is one of those in the array.
			if (isset($wp_current_filter[0]) && in_array( $wp_current_filter[0], array( 'get_the_excerpt', 'init', 'wp_head' ), true ) ) {

				$apply = false;
			}

			// bail if feed, search or archive
			if ( is_feed() || is_search() || is_archive() ) {
				
				if( (true == ezTOC_Option::get( 'include_category', false) && is_category()) || (true == ezTOC_Option::get( 'include_tag', false) && is_tag()) || (true == ezTOC_Option::get( 'include_product_category', false) &&  (function_exists('is_product_category') && is_product_category()) ) || (true == ezTOC_Option::get( 'include_custom_tax', false) && is_tax())) {
					
					$apply = true;
				} else {
					$apply = false;
				}
			}
			                        
			if( function_exists('get_current_screen') ) {
				$my_current_screen = get_current_screen();
				if ( isset( $my_current_screen->id )  ) {

					if( $my_current_screen->id == 'edit-post' ) {          
						$apply = false;
					}
				}

				if(is_object($my_current_screen) && method_exists( $my_current_screen, 'is_block_editor' ) && $my_current_screen->is_block_editor()){
					$apply = false;
				}
			}

			if ( ! empty( array_intersect( $wp_current_filter, array( 'get_the_excerpt', 'init', 'wp_head' ) ) ) ) {
				$apply = false;
			}

			if ((function_exists('et_core_is_model_view') && et_core_is_model_view()) || (function_exists('et_builder_is_enabled') && et_builder_is_enabled())) {
				// Divi frontend & backend builder
				$apply = false;
			} 
			/**
			 * Whether or not to apply `the_content` filter callback.
			 *
			 * @see ezTOC::the_content()
			 *
			 * @since 2.0
			 *
			 * @param bool $apply
			 */			
			return apply_filters( 'ez_toc_maybe_apply_the_content_filter', $apply );
		}

		/**
		 * Callback for the `the_content` filter.
		 *
		 * This will add the inline table of contents page anchors to the post content. It will also insert the
		 * table of contents inline with the post content as defined by the user defined preference.
		 *
		 * @since 1.0
		 *
		 * @param string $content
		 *
		 * @return string
		 */
		public static function the_content( $content ) {
				                    
				if( function_exists( 'post_password_required' ) ) {
					if( post_password_required() ) return Debug::log()->appendTo( $content );
				}
			
				$maybeApplyFilter = self::maybeApplyTheContentFilter();													
				$content = apply_filters('eztoc_modify_the_content',$content);
				
				if ( in_array( 'divi-machine/divi-machine.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'Fortunato Pro' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {
					update_option( 'ez-toc-post-content-core-level', $content );
			}
			Debug::log( 'the_content_filter', 'The `the_content` filter applied.', $maybeApplyFilter );

			if ( ! $maybeApplyFilter ) {

				return Debug::log()->appendTo( $content );
			}
			// Fix for getting current page id when sub-queries are used on the page
			$ez_toc_current_post_id = function_exists('get_queried_object_id')?get_queried_object_id():get_the_ID();

			// Bail if post not eligible and widget is not active.
			if(apply_filters( 'current_theme', get_option( 'current_theme' ) ) == 'MicrojobEngine Child'){
				$isEligible = self::is_eligible( get_post($ez_toc_current_post_id) );
			}else{
				$isEligible = self::is_eligible( get_post() );
			}
			
			
			//More button
			$options =  array();
			if (ezTOC_Option::get( 'ctrl_headings' ) == true) {
				$options['view_more'] = ezTOC_Option::get( 'limit_headings_num' );
			}
			$post_ctrl_headings = get_post_meta( $ez_toc_current_post_id, '_ez-toc-p_ctrl_heading', true );
			$post_ctrl_headings_limit = get_post_meta( $ez_toc_current_post_id, '_ez-toc-p_limit_headings_num', true );

			if($post_ctrl_headings == true && $post_ctrl_headings_limit > 0){
				$options['view_more'] = get_post_meta( $ez_toc_current_post_id, '_ez-toc-p_limit_headings_num', true );
			}

			$isEligible = apply_filters('eztoc_do_shortcode',$isEligible);

			if($isEligible){
				if(!ez_toc_auto_device_target_status()){
					$isEligible = false;
				}
			}

			Debug::log( 'post_eligible', 'Post eligible.', $isEligible );
			$return_only_an = false; 
			if(!$isEligible && (self::is_sidebar_hastoc() || is_active_widget( false, false, 'ezw_tco' ) || is_active_widget( false, false, 'ez_toc_widget_sticky' ) || ezTOC_Option::get('sticky-toggle') )){
				$isEligible = true;
				$return_only_an = true;
			}
			
			if ( ! $isEligible ) {
				return Debug::log()->appendTo( $content );
			}
			
			if(apply_filters( 'current_theme', get_option( 'current_theme' ) ) == 'MicrojobEngine Child'){
				$post = self::get( $ez_toc_current_post_id );
			}else{
				$post = self::get( get_the_ID());
			}
			
			
			if ( ! $post instanceof ezTOC_Post ) {

				Debug::log( 'not_instance_of_post', 'Not an instance if `WP_Post`.', get_the_ID() );

				return Debug::log()->appendTo( $content );
			}
			 //Bail if no headings found.
			 if ( ! $post->hasTOCItems() && ezTOC_Option::get( 'no_heading_text' ) != 1) {

			 	return Debug::log()->appendTo( $content );
			 }
			         
			$find    = $post->getHeadings();
			$replace = $post->getHeadingsWithAnchors();
			$toc 	 = count($options) > 0 ? $post->getTOC($options) : $post->getTOC();
			$headings = implode( PHP_EOL, $find );
			$anchors  = implode( PHP_EOL, $replace );

			$headingRows = count( $find ) + 1;
			$anchorRows  = count( $replace ) + 1;

			$style = "background-image: linear-gradient(#F1F1F1 50%, #F9F9F9 50%); background-size: 100% 4em; border: 1px solid #CCC; font-family: monospace; font-size: 1em; line-height: 2em; margin: 0 auto; overflow: auto; padding: 0 8px 4px; white-space: nowrap; width: 100%;";

			Debug::log(
				'found_post_headings',
				'Found headings:',
				"<textarea id='ez-toc-debug-headings-found' rows='{$headingRows}' style='{$style}' wrap='soft'>{$headings}</textarea>"
			);

			Debug::log(
				'replace_post_headings',
				'Replace found headings with:',
				"<textarea id='ez-toc-debug-headings-replace' rows='{$anchorRows}' style='{$style}' wrap='soft'>{$anchors}</textarea>"
			);
			

			if ( $return_only_an ) {
				Debug::log( 'side_bar_has shortcode', 'Shortcode found, add links to content.', true );
				return mb_find_replace( $find, $replace, $content );
			}
			// If shortcode used or post not eligible, return content with anchored headings.
			if ( strpos( $content, 'ez-toc-container' ) || ! $isEligible ) {

				Debug::log( 'shortcode_found', 'Shortcode found, add links to content.', true );

				return mb_find_replace( $find, $replace, $content );
			}
			
			$position  = get_post_meta( get_the_ID(), '_ez-toc-position-specific', true );
			if (empty($position)) {
				$position = ezTOC_Option::get( 'position' );
			}

			Debug::log( 'toc_insert_position', 'Insert TOC at position', $position );

			switch ( $position ) {

				case 'top':
					$content = $toc . mb_find_replace( $find, $replace, $content );
					break;

				case 'bottom':
					$content = mb_find_replace( $find, $replace, $content ) . $toc;
					break;

				case 'after':
					$replace[0] = $replace[0] . $toc;
					$content    = mb_find_replace( $find, $replace, $content );
					break;
				case 'afterpara':
					$exc_blkqt  = get_post_meta( get_the_ID(), '_ez-toc-s_blockqoute_checkbox', true );
					if ($exc_blkqt) {
						$exc_blkqt = ezTOC_Option::get( 'blockqoute_checkbox' );
					}
					//blockqoute
					$blockquotes = array();
					if($exc_blkqt == true){
						preg_match_all("/<blockquote(.*?)>(.*?)<\/blockquote>/s", $content, $blockquotes);
						if(!empty($blockquotes)){
					    	$content = ez_toc_para_blockquote_replace($blockquotes, $content, 1);
					   	}
					}
					$content = insertElementByPTag( mb_find_replace( $find, $replace, $content ), $toc );
					//add blockqoute back
					if($exc_blkqt == true && !empty($blockquotes)){
					    $content = ez_toc_para_blockquote_replace($blockquotes, $content, 2);
				    }
					break;
				case 'aftercustompara':
					$exc_blkqt  = get_post_meta( get_the_ID(), '_ez-toc-s_blockqoute_checkbox', true );
					if ($exc_blkqt) {
						$exc_blkqt = ezTOC_Option::get( 'blockqoute_checkbox' );
					}
					//blockqoute
					$blockquotes = array();
					if($exc_blkqt == true){
						preg_match_all("/<blockquote(.*?)>(.*?)<\/blockquote>/s", $content, $blockquotes);
						if(!empty($blockquotes)){
					    	$content = ez_toc_para_blockquote_replace($blockquotes, $content, 1);
					   	}
					}
					$paragraph_index  = get_post_meta( get_the_ID(), '_ez-toc-s_custom_para_number', true );
					if (empty($paragraph_index)) {
						$paragraph_index = ezTOC_Option::get( 'custom_para_number' );
					}
					if($paragraph_index == 1){
						$content = insertElementByPTag( mb_find_replace( $find, $replace, $content ), $toc );
					}else if($paragraph_index > 1){
						$closing_p = '</p>';
						$paragraphs = explode( $closing_p, $content );
						if(!empty($paragraphs) && is_array($paragraphs) && $paragraph_index <= count($paragraphs)){
							$paragraph_id = $paragraph_index;
							foreach ($paragraphs as $index => $paragraph) {
								if ( trim( $paragraph ) ) {
									$paragraphs[$index] .= $closing_p;
								}
								$pos = strpos($paragraph, '<p');
								if ( $paragraph_id == $index + 1 && $pos !== false ) {
									$paragraphs[$index] .= $toc;
								}
							}
							$content = implode( '', $paragraphs );
							$content = mb_find_replace( $find, $replace, $content );
						}else{
							$content = insertElementByPTag( mb_find_replace( $find, $replace, $content ), $toc );	
						}
					}else{
						$content = insertElementByPTag( mb_find_replace( $find, $replace, $content ), $toc );	
					}
					//add blockqoute back
					if($exc_blkqt == true && !empty($blockquotes)){
					    $content = ez_toc_para_blockquote_replace($blockquotes, $content, 2);
				    }
					break;	
				case 'aftercustomimg':
					$img_index  = get_post_meta( get_the_ID(), '_ez-toc-s_custom_img_number', true );
					if (empty($img_index)) {
						$img_index = ezTOC_Option::get( 'custom_img_number' );
					}
					if($img_index == 1){
						$content = insertElementByImgTag( mb_find_replace( $find, $replace, $content ), $toc );
					}else if($img_index > 1){
						$closing_img = '</figure>';
						$imgs = explode( $closing_img, $content );
						if(!empty($imgs) && is_array($imgs) && $img_index <= count($imgs)){
							$img_id = $img_index;
							foreach ($imgs as $index => $img) {
								if ( trim( $img ) ) {
									$imgs[$index] .= $closing_img;
								}
								$pos = strpos($img, '<figure');
								if ( $img_id == $index + 1 && $pos !== false ) {
									$imgs[$index] .= $toc;
								}
							}
							$content = implode( '', $imgs );
							$content = mb_find_replace( $find, $replace, $content );
						}else{
							$content = insertElementByImgTag( mb_find_replace( $find, $replace, $content ), $toc );	
						}
					}else{
						$content = insertElementByImgTag( mb_find_replace( $find, $replace, $content ), $toc );	
					}
					break;	
				case 'before':
				default:
					$content    = mb_find_replace( $find, $replace, $content );

					/**
					 * @link https://wordpress.org/support/topic/php-notice-undefined-offset-8/
					 */
					if ( ! array_key_exists( 0, $replace ) ) {
						break;
					}

					$pattern = '`<h[1-6]{1}[^>]*' . preg_quote( $replace[0], '`' ) . '`msuU';
					$result  = preg_match( $pattern, $content, $matches );

					/*
					 * Try to place TOC before the first heading found in eligible heading, failing that,
					 * insert TOC at top of content.
					 */
					if ( 1 === $result ) {

						Debug::log( 'toc_insert_position_found', 'Insert TOC before first eligible heading.', $result );

						$start   = strpos( $content, $matches[0] );
						$content = substr_replace( $content, $toc, $start, 0 );

					} else {

						Debug::log( 'toc_insert_position_not_found', 'Insert TOC before first eligible heading not found.', $result );

					}
			}
            
			return Debug::log()->appendTo( $content );
		}

		/**
		 * stickyToggleContent Method
		 * Call back for the `wp_footer` action.
		 *
		 * @since  2.0.32
		 * @static
		 */
		public static function stickyToggleContent() {

			if(ezTOC_Option::get('sticky-toggle')){
			  
			  if(ez_toc_stikcy_enable_support_status()){

				if( function_exists( 'post_password_required' ) ) {
					if(post_password_required() ) {
						return false;
					}
			    }

				$toggleClass = "hide";
				$linkZindex  = "";

				$post = self::get( get_the_ID() );

				if ( null !== $post) {

					$stickyToggleTOC = $post->getStickyToggleTOC();

					if(!empty($stickyToggleTOC)){
						$openButtonText = esc_html__( 'Index', 'easy-table-of-contents' );
						if( !empty( ezTOC_Option::get( 'sticky-toggle-open-button-text' ) ) ) {
							$openButtonText = ezTOC_Option::get( 'sticky-toggle-open-button-text' );
						}
						if( !empty( ezTOC_Option::get( 'sticky-toggle-open' ) ) ) {
							$isTOCOpen = ezTOC_Option::get( 'sticky-toggle-open' );
							if($isTOCOpen){
								$toggleClass="show";
								$linkZindex="style='z-index:-1;'";
							}
						}
						

					$arrowSide = ( 'right' == ezTOC_Option::get( 'sticky-toggle-position', 'left') )?"&#8592;":"&#8594;"; 
					
					$themeClass = 'ez-toc-sticky-'.ezTOC_Option::get( 'sticky_theme', 'grey' );
										
					?>
					<div class="ez-toc-sticky">
						<div class="ez-toc-sticky-fixed <?php echo esc_attr($toggleClass); ?> <?php echo esc_attr($themeClass); ?>">
							<div class='ez-toc-sidebar'><?php echo $stickyToggleTOC; //phpcs:ignore  ?></div>
						</div>
						<a class='ez-toc-open-icon' href='#' onclick='ezTOC_showBar(event)' <?php echo esc_attr($linkZindex); ?>>
							<span class="arrow"><?php echo esc_html($arrowSide); ?></span>
							<span class="text"><?php echo esc_html($openButtonText); ?></span>
						</a>
					</div>
					<?php
					}
				}
			  }
			}			
		}

		/**
		 * Call back for the `wp_head` action.
		 *
		 * Add add button for shortcode in wysisyg editor .
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public static function addEditorButton() {
			
            if ( !current_user_can( 'edit_posts' ) &&  !current_user_can( 'edit_pages' ) ) {
                       return;
               }
			   
		
           if ( 'true' == get_user_option( 'rich_editing' ) ) {
               add_filter( 'mce_external_plugins', array( __CLASS__, 'toc_add_tinymce_plugin'));
               add_filter( 'mce_buttons', array( __CLASS__, 'toc_register_mce_button' ));
               }
			
		}
		
		/**
		 * Call back for the `mce_external_plugins` action.
		 *
		 * Register new button in the editor.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */		
		
		public static function toc_register_mce_button( $buttons ) {
            
				array_push( $buttons, 'toc_mce_button' );
				return $buttons;
		}
			
		/**
		 * Call back for the `mce_buttons` action.
		 *
		 * Add  js to insert the shortcode on the click event.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public static function toc_add_tinymce_plugin( $plugin_array ) {
			
				$plugin_array['toc_mce_button'] = EZ_TOC_URL .'assets/js/toc-mce-button.js';
				return $plugin_array;
		}

		/**
         * getTOCToggleIcon Method
         * @access public
   		 * @since  2.0.35
   		 * @static
		 * @return string
		 */
		public static function getTOCToggleIcon( $type = '' )
		{
			$iconColor = '#000000';
			if( ezTOC_Option::get( 'custom_title_colour' ) )
			{
				$iconColor = ezTOC_Option::get( 'custom_title_colour' );
			}
			$spanClass = '';
			if( ezTOC_Option::get( 'toc_loading' ) == 'css' && $type != 'widget-with-visibility_on_header_text' ) 
			{
				$spanClass = 'ez-toc-cssicon';
			}

			return '<span class="' . esc_attr($spanClass) . '"><span class="eztoc-hide" style="display:none;">Toggle</span><span class="ez-toc-icon-toggle-span"><svg style="fill: ' . esc_attr($iconColor) . ';color:' . esc_attr($iconColor) . '" xmlns="http://www.w3.org/2000/svg" class="list-377408" width="20px" height="20px" viewBox="0 0 24 24" fill="none"><path d="M6 6H4v2h2V6zm14 0H8v2h12V6zM4 11h2v2H4v-2zm16 0H8v2h12v-2zM4 16h2v2H4v-2zm16 0H8v2h12v-2z" fill="currentColor"></path></svg><svg style="fill: ' . esc_attr($iconColor) . ';color:' . esc_attr($iconColor) . '" class="arrow-unsorted-368013" xmlns="http://www.w3.org/2000/svg" width="10px" height="10px" viewBox="0 0 24 24" version="1.2" baseProfile="tiny"><path d="M18.2 9.3l-6.2-6.3-6.2 6.3c-.2.2-.3.4-.3.7s.1.5.3.7c.2.2.4.3.7.3h11c.3 0 .5-.1.7-.3.2-.2.3-.5.3-.7s-.1-.5-.3-.7zM5.8 14.7l6.2 6.3 6.2-6.3c.2-.2.3-.5.3-.7s-.1-.5-.3-.7c-.2-.2-.4-.3-.7-.3h-11c-.3 0-.5.1-.7.3-.2.2-.3.5-.3.7s.1.5.3.7z"/></svg></span></span>';
		}

		 /**
         * the_category_content_filter Method
         * @access public
   		 * @since  2.0.46
   		 * @static
		 * @return string
		 */
		public static function toc_term_content_filter( $description , $term_id ) {
                    if( (is_category() && true == ezTOC_Option::get( 'include_category', false)) || (is_tax() && true == ezTOC_Option::get( 'include_custom_tax', false)) || (is_tag() && true == ezTOC_Option::get( 'include_tag', false)) ) {
						if(!is_admin() && !empty($description)){							
							return self::the_content($description);
						}
                    }
                    return $description;
		}

		public static function toc_category_content_filter_woocommerce( $description , $term ) {
					if( true == ezTOC_Option::get( 'include_product_category', false) ) {
						if(!is_admin() && !empty($description)){
							return self::the_content($description);
						}
					}
					return $description;
		}

		/**
		 * the_content_storehub Method
		 * Call back for the `the_content` filter.
		 *
		 * This will add the inline table of contents page anchors to the post content. It will also insert the
		 * table of contents inline with the post content as defined by the user defined preference.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 * @param string $content
		 * @return string
		 */
		public static function the_content_storehub ( $content ) {
				                    
			if( function_exists( 'post_password_required' ) ) {
				if( post_password_required() ) return Debug::log()->appendTo( $content );
			}
		
			$maybeApplyFilter = self::maybeApplyTheContentFilter();													
			$content = apply_filters('eztoc_modify_the_content',$content);
			
		Debug::log( 'the_content_filter', 'The `the_content` filter applied.', $maybeApplyFilter );
		
		if ( ! $maybeApplyFilter ) {
		
			return Debug::log()->appendTo( $content );
		}
		
		$isEligible = self::is_eligible( get_post() );
	
		$isEligible = apply_filters('eztoc_do_shortcode',$isEligible);
		
		if($isEligible){
			if(!ez_toc_auto_device_target_status()){
				$isEligible = false;
			}
		}
		
		Debug::log( 'post_eligible', 'Post eligible.', $isEligible );
		if(!$isEligible && (self::is_sidebar_hastoc() || is_active_widget( false, false, 'ezw_tco' ) || is_active_widget( false, false, 'ez_toc_widget_sticky' ) || ezTOC_Option::get('sticky-toggle') )){
			$isEligible = true;
		}
		
		if ( ! $isEligible ) {
			return Debug::log()->appendTo( $content );
		}
		
		$post = self::get( get_the_ID());
		
		if ( ! $post instanceof ezTOC_Post ) {
		
			Debug::log( 'not_instance_of_post', 'Not an instance if `WP_Post`.', get_the_ID() );
		
			return Debug::log()->appendTo( $content );
		}
		 //Bail if no headings found.
		 if ( ! $post->hasTOCItems() && ezTOC_Option::get( 'no_heading_text' ) != 1) {
		
			 return Debug::log()->appendTo( $content );
		 }
				 
		$find    = $post->getHeadings();
		$replace = $post->getHeadingsWithAnchors();

		return mb_find_replace( $find, $replace, $content );
		
		}


	}

	/**
	 * The main function responsible for returning the Easy Table of Contents instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing to declare the global.
	 *
	 * Example: <?php $instance = ezTOC(); ?>
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @return ezTOC
	 */
	function ezTOC() {

		return ezTOC::instance();
	}

	// Start Easy Table of Contents.
	add_action( 'plugins_loaded', 'ezTOC' );
}

register_activation_hook(__FILE__, 'ez_toc_activate');
function ez_toc_activate() {
    add_option('ez_toc_do_activation_redirect', true);
}