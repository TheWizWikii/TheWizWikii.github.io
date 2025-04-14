<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get the current post's TOC list or supplied post's TOC list.
 *
 * @access public
 * @since  2.0
 *
 * @param int|null|WP_Post $post                 An instance of WP_Post or post ID. Defaults to current post.
 * @param bool             $apply_content_filter Whether or not to apply `the_content` filter when processing post for headings.
 *
 * @return string
 */
function get_ez_toc_list( $post = null, $apply_content_filter = true ) {

	if ( ! $post instanceof WP_Post ) {

		$post = get_post( $post );
	}

	if ( $apply_content_filter ) {

		$ezPost = new ezTOC_Post( $post );

	} else {

		$ezPost = new ezTOC_Post( $post, false );
	}

	return $ezPost->getTOCList();
}

/**
 * Display the current post's TOC list or supplied post's TOC list.
 *
 * @access public
 * @since  2.0
 *
 * @param null|WP_Post $post                 An instance of WP_Post
 * @param bool         $apply_content_filter Whether or not to apply `the_content` filter when processing post for headings.
 */
function ez_toc_list( $post = null, $apply_content_filter = true ) {
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason : Already escaped
	echo get_ez_toc_list( $post, $apply_content_filter );
}

/**
 * Get the current post's TOC content block or supplied post's TOC content block.
 *
 * @access public
 * @since  2.0
 *
 * @param int|null|WP_Post $post                 An instance of WP_Post or post ID. Defaults to current post.
 * @param bool             $apply_content_filter Whether or not to apply `the_content` filter when processing post for headings.
 *
 * @return string
 */
function get_ez_toc_block( $post = null, $apply_content_filter = true ) {

	if ( ! $post instanceof WP_Post ) {

		$post = get_post( $post );
	}

	if ( $apply_content_filter ) {

		$ezPost = new ezTOC_Post( $post );

	} else {

		$ezPost = new ezTOC_Post( $post, false );
	}

	return $ezPost->getTOC();
}

/**
 * Display the current post's TOC content or supplied post's TOC content.
 *
 * @access public
 * @since  2.0
 *
 * @param null|WP_Post $post                 An instance of WP_Post
 * @param bool         $apply_content_filter Whether or not to apply `the_content` filter when processing post for headings.
 */
function ez_toc_block( $post = null, $apply_content_filter = true ) {
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason : Already escaped
	echo get_ez_toc_block( $post, $apply_content_filter );
}
// Non amp checker
if ( ! function_exists('ez_toc_is_amp_activated') ){
    
    function ez_toc_is_amp_activated() {
        $result = false;
        if (is_plugin_active('accelerated-mobile-pages/accelerated-moblie-pages.php') || is_plugin_active('amp/amp.php')  ||
                is_plugin_active('better-amp/better-amp.php')  ||
                is_plugin_active('wp-amp/wp-amp.php') ||
                is_plugin_active('amp-wp/amp-wp.php') ||
                is_plugin_active('bunyad-amp/bunyad-amp.php') )
            $result = true;
        
        return $result;
    }
    
}

// Non amp checker
if ( ! function_exists('ez_toc_non_amp') ) {
    
    function ez_toc_non_amp() {

        $non_amp = true;

        if( function_exists('ampforwp_is_amp_endpoint') && ampforwp_is_amp_endpoint() ) {                
            $non_amp = false;                       
        }     
        if( function_exists('is_amp_endpoint') && is_amp_endpoint() ){
            $non_amp = false;           
        }
        if( function_exists('is_better_amp') && is_better_amp() ){       
            $non_amp = false;           
        }
        if( function_exists('is_amp_wp') && is_amp_wp() ){       
            $non_amp = false;           
        }

        return $non_amp;

    }
  
}

/**
 * MBString Extension Admin Notice
 * if not loaded then msg to user
 * @since 2.0.47
 */
if ( function_exists('extension_loaded') && extension_loaded('mbstring') == false ) {
    function ez_toc_admin_notice_for_mbstring_extension() {
        echo '<div class="notice notice-error is-not-dismissible"><p>' . esc_html__( 'PHP MBString Extension is not enabled in your php setup, please enabled to work perfectly', 'easy-table-of-contents' ) . ' <strong>' . esc_html__( 'Easy Table of Contents', 'easy-table-of-contents' ) . '</strong>. ' . esc_html__( 'Check official doc:', 'easy-table-of-contents' ). ' <a href="https://www.php.net/manual/en/mbstring.installation.php" target="_blank">' . esc_html__( 'PHP Manual', 'easy-table-of-contents' ) .'</a></p></div>';
    }
    add_action('admin_notices', 'ez_toc_admin_notice_for_mbstring_extension');
}


/**
 * EzPrintR method
 * to print_r content with pre tags
 * @since 2.0.34
 * @param $content
 * @return void
*/
function EzPrintR($content){
	echo "<pre>";
    print_r($content);
    echo "</pre>";
}

/**
 * EzDumper method
 * to var_dump content with pre tags
 * @since 2.0.34
 * @param $content
 * @return void
*/
function EzDumper($content){
	echo "<pre>";
    var_dump($content);
    echo "</pre>";
}

/**
 * Since version 2.0.52
 * Export all settings to json file
 */
add_action( 'wp_ajax_ez_toc_export_all_settings', 'ez_toc_export_all_settings'); 
function ez_toc_export_all_settings()
{
    if ( !current_user_can( 'manage_options' ) ) {
        die('-1');
    }
    if(!isset($_GET['_wpnonce'])){
        die('-1');
    }
    if( !wp_verify_nonce(  $_GET['_wpnonce'] , '_wpnonce' ) ){
        die('-1');
    }

    $export_settings_data = get_option('ez-toc-settings');
    if(!empty($export_settings_data)){
        header('Content-type: application/json');
        header('Content-disposition: attachment; filename=ez_toc_settings_backup.json');
        echo wp_json_encode($export_settings_data);   
    }                             
    wp_die();
}

/**
 * Adding page/post title in TOC list
 * @since 2.0.56
 */
add_action( 'init', function() {
    if(ezTOC_Option::get('show_title_in_toc') == 1 && !is_admin())
    {
        ob_start();
    }
} );
add_action('shutdown', function() {
    if(ezTOC_Option::get('show_title_in_toc') == 1 && !is_admin()){
        $final = '';
        $levels = ob_get_level();
    
        for ($i = 0; $i < $levels; $i++) {
            $final .= ob_get_clean();
        }
        //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason : This if final output buffer
        echo apply_filters('eztoc_wordpress_final_output', $final);
    }
 
}, 10);

add_filter('eztoc_wordpress_final_output', function($content){
    if(!is_singular('post') && !is_page()) { return $content;}
    if(ezTOC_Option::get('show_title_in_toc') == 1 && !is_admin()){ 
        return preg_replace_callback(
            '/<body.*?>(.*?)<\/body>/is',
            function ($matches) {
                $body_content = $matches[1];
                return preg_replace_callback(
                    '/<h1(.*?)>(.*?)<\/h1>/i',
                    function ($h1_matches) {
                        $title = $h1_matches[2];
                        $added_link = '<h1'.$h1_matches[1].'><span class="ez-toc-section" id="'.esc_attr(ezTOCGenerateHeadingIDFromTitle($title)).'" ez-toc-data-id="#'.esc_attr(ezTOCGenerateHeadingIDFromTitle($title)).'"></span>';
                        $added_link .= esc_attr($title);
                        $added_link .= '<span class="ez-toc-section-end"></span></h1>';
                        return $added_link;
                    },
                    $body_content
                );
            },
            $content
        );
    }
}, 10, 1);

    
    add_filter( 'ez_toc_modify_process_page_content', 'ez_toc_page_content_include_page_title', 10, 1 );
    function ez_toc_page_content_include_page_title( $content ) {
        if(ezTOC_Option::get('show_title_in_toc') == 1 && !is_admin()){ 
            $title = get_the_title();
            $added_page_title= '<h1 class="entry-title">'.wp_kses_post($title).'</h1>';
            $content = $added_page_title.$content;
        }
        return $content;
    }
     function ezTOCGenerateHeadingIDFromTitle( $heading ) {
        $return = false;
        if ( $heading ) {
            $heading = apply_filters( 'ez_toc_url_anchor_target_before', $heading );
            $return = html_entity_decode( $heading, ENT_QUOTES, get_option( 'blog_charset' ) );
            $return = trim( wp_strip_all_tags( $return ) );
            $return = remove_accents( $return );
            $return = str_replace( array( "\r", "\n", "\n\r", "\r\n" ), ' ', $return );
            $return = htmlentities2( $return );
            $return = str_replace( array( '&amp;', '&nbsp;'), ' ', $return );
            $return = str_replace( array( '&shy;' ),'', $return );					// removed silent hypen 
            $return = html_entity_decode( $return, ENT_QUOTES, get_option( 'blog_charset' ) );
            $return = preg_replace( '/[\x00-\x1F\x7F]*/u', '', $return );
            $return = str_replace(
                array( '*', '\'', '(', ')', ';', '@', '&', '=', '+', '$', ',', '/', '?', '#', '[', ']' ),
                '',
                $return
            );
            $return = str_replace(
                array( '%', '{', '}', '|', '\\', '^', '~', '[', ']', '`' ),
                '',
                $return
            );
            $return = str_replace(
                array( '$', '.', '+', '!', '*', '\'', '(', ')', ',', '’' ),
                '',
                $return
            );
            $return = str_replace(
                array( '-', '-', 'â€“', 'â€”' ),
                '-',
                $return
            );
            $return = str_replace(
                array( 'â€˜', 'â€™', 'â€œ', 'â€' ),
                '',
                $return
            );
            $return = str_replace( array( ':' ), '_', $return );
            $return = preg_replace( '/\s+/', '_', $return );
            $return = preg_replace( '/-+/', '-', $return );
            $return = preg_replace( '/_+/', '_', $return );
            $return = rtrim( $return, '-_' );
            $return = preg_replace_callback(
                "{[^0-9a-z_.!~*'();,/?:@&=+$#-]}i",
                function( $m ) {
    
                    return sprintf( '%%%02X', ord( $m[0] ) );
                },
                $return
            );
            if ( ezTOC_Option::get( 'lowercase' ) ) {
    
                $return = strtolower( $return );
            }
            if ( !$return || true == ezTOC_Option::get( 'all_fragment_prefix' ) ) {
    
                $return = ( ezTOC_Option::get( 'fragment_prefix' ) ) ? ezTOC_Option::get( 'fragment_prefix' ) : '_';
            }
            if ( ezTOC_Option::get( 'hyphenate' ) ) {
    
                $return = str_replace( '_', '-', $return );
                $return = preg_replace( '/-+/', '-', $return );
            }
        }
        return apply_filters( 'ez_toc_url_anchor_target', $return, $heading );
    }
   //Device Eligibility
  //@since 2.0.60
function ez_toc_auto_device_target_status(){
        $status = true;      
        if(ezTOC_Option::get( 'device_target' ) == 'mobile'){
            if(function_exists('wp_is_mobile') && wp_is_mobile()){                
                $status = true;      
            }else{                
                $status = false;      
            }
        }
        if(ezTOC_Option::get( 'device_target' ) == 'desktop'){
            if(function_exists('wp_is_mobile') && wp_is_mobile()){                
                $status = false;      			
            }else{                
                $status = true;      
            }
        }
        return $status;
}
/**
 * Check for the enable support of sticky toc/toggle
 * @since 2.0.60
 */
function ez_toc_stikcy_enable_support_status(){

    $status = false;

    $stickyPostTypes = apply_filters('ez_toc_sticky_post_types', ezTOC_Option::get('sticky-post-types'));

    if(!empty($stickyPostTypes)){
        if(is_singular() && !is_front_page()){
            $postType = get_post_type();
            if(in_array($postType,$stickyPostTypes)){
                $status = true;
            }
        }										
    }

    if(ezTOC_Option::get('sticky_include_homepage')){
        if ( is_front_page() || is_home() ) {
            $status = true;
        }
    }

    if(ezTOC_Option::get('sticky_include_category')){
        if ( is_category() ) {
            $status = true;
        }
    }

    if(ezTOC_Option::get('sticky_include_tag')){
        if ( is_tag() ) {
            $status = true;
        }
    }
    
    if(ezTOC_Option::get('sticky_include_product_category')){
        if ( is_tax( 'product_cat' ) ) {
            $status = true;
        }
    }

    if(ezTOC_Option::get('sticky_include_custom_tax')){
        if ( is_tax() ) {
            $status = true;
        }
    }

    //Device Eligibility
    //@since 2.0.60
    if(ezTOC_Option::get( 'sticky_device_target' ) == 'mobile'){
        if(function_exists('wp_is_mobile') && wp_is_mobile()){
            $status = true;
        }else{
            $status = false;
        }
    }

    if(ezTOC_Option::get( 'sticky_device_target' ) == 'desktop'){
        if(function_exists('wp_is_mobile') && wp_is_mobile()){
            $status = false;
        }else{
            $status = true;
        }
    }

    if( ezTOC_Option::get( 'sticky_restrict_url_text' ) && ezTOC_Option::get( 'sticky_restrict_url_text' ) != '' ){
        $all_urls = nl2br(ezTOC_Option::get( 'sticky_restrict_url_text' ));
        $all_urls = str_replace('<br />', '', $all_urls);
        $urls_arr = explode(PHP_EOL, $all_urls);
        if(is_array($urls_arr)){
            foreach ($urls_arr as $url_arr) {
                if ( isset($_SERVER['REQUEST_URI']) && false !== strpos( $_SERVER['REQUEST_URI'], trim($url_arr) ) ) {
                    $status = false;
                    break;
                }
            }
        }
    }
    
    return apply_filters('ez_toc_sticky_enable_support', $status);

}


/**
 * Helps exclude blockquote
 * @since 2.0.58
 */
if(!function_exists('ez_toc_para_blockquote_replace')){
function ez_toc_para_blockquote_replace($blockquotes, $content, $step){
    $bId = 0;
    if($step == 1){    
        foreach($blockquotes[0] as $blockquote){
            $replace = '#eztocbq' . $bId . '#';
            $content = str_replace( trim($blockquote), $replace, $content );
            $bId++;
        }
    }elseif($step == 2){    
        foreach($blockquotes[0] as $blockquote){
            $search = '#eztocbq' . $bId . '#'; 
            $content = str_replace( $search, trim($blockquote), $content );
            $bId++;
        }
    }
    return $content;
}
}

/**
 * Helps allow line breaks
 * @since 2.0.59
 */
add_filter('ez_toc_title_allowable_tags', 'ez_toc_link_allow_br_tag');
function ez_toc_link_allow_br_tag($tags){
    if(ezTOC_Option::get( 'prsrv_line_brk' )){
        $tags = '<br>';
    }
    return $tags;
}

/**
 * Check the status of shortcode enable support which is defined in shortcode attributes
 * @since 2.0.59
 */
function ez_toc_shortcode_enable_support_status($atts){
    
    $status = true;

    if(isset($atts['post_types'])){
        $exp_post_types = explode(',', $atts['post_types']);
        if(!empty($exp_post_types)){
            $exp_post_types = array_map("trim",$exp_post_types);
            if(is_singular()){
                $curr_post_type = get_post_type();
                if(in_array($curr_post_type, $exp_post_types )){
                    $status = true;
                }else{
                    $status = false;
                }
            }else{
                $status = false;
            }       
        }
    }

    if(isset($atts['post_in'])){
        $exp_post_ids = explode(',', $atts['post_in']);
        if(!empty($exp_post_ids)){
            $exp_post_ids = array_map("trim",$exp_post_ids);
            if(is_singular()){
                $ID = get_the_ID();
                if(in_array($ID, $exp_post_ids )){
                    $status = true;
                }else{
                    $status = false;
                }
            }else{
                $status = false;
            }       
        }
    }

    if(isset($atts['post_not_in'])){
        $exp_post_ids = explode(',', $atts['post_not_in']);
        if(!empty($exp_post_ids)){
            $exp_post_ids = array_map("trim",$exp_post_ids);
            if(is_singular()){
                $ID = get_the_ID();
                if(!in_array($ID, $exp_post_ids )){
                    $status = true;
                }else{
                    $status = false;
                }
            }else{
                $status = false;
            }       
        }
    }
            
        
    if(isset($atts['device_target']) && $atts['device_target'] != ''){
        $status = false;
        $my_device = $atts['device_target'];
        if(function_exists('wp_is_mobile') && wp_is_mobile()){
            if($my_device == 'mobile'){
                $status = true;
            }
        }else{
            if($my_device == 'desktop'){
                $status = true;
            }
        }
    }
    
    return $status;    
}

/**
 * Display No heading found text when there are no heading present in the content 
 * @since 2.0.66
 */
add_filter('eztoc_shortcode_final_toc_html','eztoc_shortcode_html_no_heading_text');
add_filter('eztoc_autoinsert_final_toc_html','eztoc_shortcode_html_no_heading_text');

function eztoc_shortcode_html_no_heading_text($html){

    if(empty($html)){
        if( ezTOC_Option::get( 'no_heading_text' ) == 1 && ezTOC_Option::get( 'no_heading_text_value' )){
			$no_heading_text_value = !empty(ezTOC_Option::get( 'no_heading_text_value' ))?esc_html(ezTOC_Option::get( 'no_heading_text_value' )):'No heading found';
			$html = '<div class="eztoc_no_heading_found">' . $no_heading_text_value . '</div>';
		}
    }
    return $html;
}

/**
 * Added [no-ez-toc] to disbale TOC on specific page/post
 * @since 2.0.56
 */
add_shortcode( 'no-ez-toc', 'ez_toc_noeztoc_callback' );
function ez_toc_noeztoc_callback( $atts, $content = "" ) {
	add_filter(
		'ez_toc_maybe_apply_the_content_filter',	function( $apply ) {
			return false;
		}
		,999
	);
	//  condition when  `the_content` filter is not used by the theme
	add_filter(
		'ez_toc_modify_process_page_content',	function( $apply ) {
			return '';
		}
		,999
	);
	return $content;
}

add_action( 'admin_init' , 'ez_toc_redirect' );
function ez_toc_redirect( ) {
    if ( get_option( 'ez_toc_do_activation_redirect' , false ) ) {
        delete_option( 'ez_toc_do_activation_redirect' );
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Reason: Nonce not required here
        if( !isset( $_GET['activate-multi'] ) )
        {
            wp_safe_redirect( "options-general.php?page=table-of-contents#welcome" );
        }
    }
}