<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Filter to add plugins to the TOC list.
 *
 * @link https://rankmath.com/kb/filters-hooks-api-developer/#add-toc-plugin
 *
 * @since 2.0
 *
 * @param array TOC plugins.
 */
add_filter(
	'rank_math/researches/toc_plugins',
	function( $toc_plugins ) {

		$toc_plugins[ EZ_TOC_BASE_NAME ] = 'Easy Table of Contents';

		return $toc_plugins;
	}
);

/**
 * Filter to remove Connections Business Directory related shortcodes from being processed as eligible TOC items.
 *
 * @since 2.0
 */
add_filter(
	'ez_toc_strip_shortcodes_tagnames',
	function( $tags_to_remove ) {

		$shortcodes = array (	
			'basic-user-avatars',		
			'CARFORM',
			'connections_form',
			'cn_multi_category_search',
			'cn_widget',
			'cn_carousel',
			'connections_categories',
			'connections_link_view',
			'connections_link_edit',
			'connections_login',
			'siteshot',
			'connections',
			'upcoming_list',
			'cn-mapblock',
			'connections_vcard',
			'connections_qtip',
			'cn_thumb',
			'cn_thumbr',
		);

		$tags_to_remove = array_merge( $tags_to_remove, $shortcodes );

		return $tags_to_remove;
	}
);


/**
 * Filter to remove Striking theme related shortcodes from being processed as eligible TOC items.
 *
 * @since 2.0
 */
add_filter(
	'ez_toc_strip_shortcodes_tagnames',
	function( $tags_to_remove ) {

		$shortcodes = array (
			'iconbox',
			'toggle',
		);

		$tags_to_remove = array_merge( $tags_to_remove, $shortcodes );

		return $tags_to_remove;
	}
);

/**
 * Remove the JetPack share buttons node from the post content before extracting headings.
 *
 * @since 2.0
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['jetpack-sharedaddy']   = '.sharedaddy';
		$selectors['jetpack-relatedposts'] = '.jp-relatedposts';

		return $selectors;
	}
);

/**
 * Remove the Elegant Themes Bloom plugin node from the post content before extracting headings.
 *
 * @since 2.0
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['elegant-themes-bloom'] = '.et_bloom_below_post';

		return $selectors;
	}
);

/**
 * Do not allow `the_content` TOC callback to run when editing a page in Visual Composer.
 *
 * @link https://wordpress.org/support/topic/correct-method-to-determine-if-using-frontend-editor/#post-12404679
 *
 * @since 2.0
 */
add_filter(
	'ez_toc_maybe_apply_the_content_filter',
	function( $apply ) {

		if ( function_exists( 'vchelper' ) ) {

			if ( vchelper( 'Frontend' )->isPageEditable() ) {

				$apply = false;
			}
		}

		return $apply;
	}
);

/**
 * Do not allow `the_content` TOC callback to run when editing a page in WPBakery Page Builder.
 *
 * @link https://wordpress.org/support/topic/correct-method-to-determine-if-using-frontend-editor/#post-12404679
 *
 * @since 2.0
 */
add_filter(
	'ez_toc_maybe_apply_the_content_filter',
	function( $apply ) {

		if ( function_exists( 'vc_is_page_editable' ) ) {

			if ( vc_is_page_editable() ) {

				$apply = false;
			}
		}

		return $apply;
	}
);

/**
 * Filter to remove WPBakery Page Builder related shortcodes from being processed as eligible TOC items.
 *
 * @since 2.0
 */
add_filter(
	'ez_toc_strip_shortcodes_tagnames',
	function( $tags_to_remove ) {

		$shortcodes = array (
			'vc_tta_section',
		);

		$tags_to_remove = array_merge( $tags_to_remove, $shortcodes );

		return $tags_to_remove;
	}
);

/**
 * Exclude the TOC shortcodes from being processed in the admin in the Divi Theme by Elegant Themes.
 *
 * @since 2.0
 */
add_action(
	'et_pb_admin_excluded_shortcodes',
	function( $shortcodes ) {

		$shortcodes[] = 'ez-toc';
		$shortcodes[] = apply_filters( 'ez_toc_shortcode', 'toc' );

		return $shortcodes;
	}
);

/**
 * Callback the for `et_builder_render_layout` filter.
 *
 * Attaches the ezTOC `the_content` filter callback to the Divi layout content filter so the in page anchors will be
 * added the post content.
 *
 * NOTE: Set priority 12 to run after Divi runs the `do_shortcode` filter on its layout content.
 *
 * @link https://wordpress.org/support/topic/anchor-links-not-being-added-with-divi/
 *
 * @since 2.0
 */
add_filter(
	'et_builder_render_layout',
	array( 'ezTOC', 'the_content' ),
	12,
	1
);

/**
 * Add support for the Uncode Theme.
 *
 * @link http://www.undsgn.com/
 *
 * @since 2.0.11
 */
add_action(

	'after_setup_theme',
	function() {
		if ( function_exists( 'uncode_setup' ) ) {
		add_filter(
				'uncode_single_content_final_output',
				array( 'ezTOC', 'the_content' ),
				10,
				1
			);
		}
	},
	11
);

/**
 * Remove the Starbox plugin node from the post content before extracting headings.
 * @link https://wordpress.org/plugins/starbox/
 * @since 2.0
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['starbox'] = '.abh_box';

		return $selectors;
	}
);

/**
 * Remove the WordPress Related Posts plugin node from the post content before extracting headings.
 * @link
 * @since 2.0
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['wordpress-23-related-posts-plugin'] = '.wp_rp_content';

		return $selectors;
	}
);

/**
 * Remove the Atomic Blocks plugin node from the post content before extracting headings.
 * @link
 * @since 2.0
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['atomic-blocks-cta']         = '.ab-block-cta';
		$selectors['atomic-blocks-testimonial'] = '.ab-block-testimonial';

		return $selectors;
	}
);

/**
 * Remove the Ultimate Addons for VC Composer node from the post content before extracting headings.
 * @link
 * @since 2.0
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['ultimate-addons-for-vc-composer'] = '.ult_tabs';

		return $selectors;
	}
);

/**
 * Remove the WP Product Review node from the post content before extracting headings.
 * @link
 * @since 2.0
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['wp-product-review'] = '.wppr-review-container';

		return $selectors;
	}
);

/**
 * Remove the Contextual Related Posts node from the post content before extracting headings.
 *
 * @link https://wordpress.org/plugins/contextual-related-posts/
 * @since 2.0.9
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {

		$selectors['contextual-related-posts'] = '.crp_related';

		return $selectors;
	}
);

class ezTOC_Elementor {

	/**
	 * Whether the excerpt is being called.
	 *
	 * Used to determine whether the call to `the_content()` came from `get_the_excerpt()`.
	 *
	 * @since 2.0.2
	 *
	 * @var bool Whether the excerpt is being used. Default is false.
	 */
	private $_is_excerpt = false;

	/**
	 * ezTOC_Elementor constructor.
	 *
	 * @since 2.0.2
	 */
	public function __construct() {

		// Hack to avoid enqueue post CSS while it's a `the_excerpt` call.
		add_filter( 'get_the_excerpt', array( $this, 'start_excerpt_flag' ), 1 );
		add_filter( 'get_the_excerpt', array( $this, 'end_excerpt_flag' ), 20 );
		add_filter( 'ez_toc_maybe_apply_the_content_filter', array( $this, 'maybe_apply_the_content_filter' ) );

		add_filter(
			'ez_toc_strip_shortcodes_tagnames',
			function( $tags_to_remove ) {

				$shortcodes = array (
					'elementor-template',
				);

				$tags_to_remove = array_merge( $tags_to_remove, $shortcodes );

				return $tags_to_remove;
			}
		);
	}

	/**
	 * Callback for the `elementor/init` action.
	 *
	 * Add the compatibility filters for Elementor.
	 *
	 * @since 2.0.2
	 */
	public static function start() {

		new self();
	}

	/**
	 * Callback for the `get_the_excerpt` filter.
	 *
	 * Start excerpt flag.
	 *
	 * Flags when `the_excerpt` is called. Used to avoid enqueueing CSS in the excerpt.
	 *
	 * @since 2.0.2
	 *
	 * @param string $excerpt The post excerpt.
	 *
	 * @return string The post excerpt.
	 */
	public function start_excerpt_flag( $excerpt ) {
		$this->_is_excerpt = true;
		return $excerpt;
	}

	/**
	 * Callback for the `get_the_excerpt` filter.
	 *
	 * End excerpt flag.
	 *
	 * Flags when `the_excerpt` call ended.
	 *
	 * @since 2.0.2
	 *
	 * @param string $excerpt The post excerpt.
	 *
	 * @return string The post excerpt.
	 */
	public function end_excerpt_flag( $excerpt ) {
		$this->_is_excerpt = false;
		return $excerpt;
	}

	/**
	 * Callback for the `ez_toc_maybe_apply_the_content_filter` filter.
	 *
	 * If doing Elementor excerpt, which calls `the_content` filter, do not apply the ezTOC `the_content` filter.
	 *
	 * @since 2.0.2
	 *
	 * @param bool $apply
	 *
	 * @return bool mixed
	 */
	public function maybe_apply_the_content_filter( $apply ) {

		if ( $this->_is_excerpt ) {

			$apply = false;
		}

		return $apply;
	}
}
add_action( 'elementor/init', array( 'ezTOC_Elementor', 'start' ) );


/**
 * Do not allow `the_content` TOC callback to run when on WooCommerce pages.
 *
 * @link https://docs.woocommerce.com/document/conditional-tags/
 * @link https://wordpress.stackexchange.com/a/246525/59053
 *
 * @since 2.0.11
 */
add_filter(
	'ez_toc_maybe_apply_the_content_filter',
	function( $apply ) {

		$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );

		// Just in case an array is not returned.
		if ( ! is_array( $active_plugins ) ) {

			$active_plugins = array();
		}

		$string = implode( '|', $active_plugins );

		if ( class_exists( 'WooCommerce' ) && FALSE !== stripos( $string, 'woocommerce.php' ) ) {

			/** @noinspection PhpUndefinedFunctionInspection */
			if ( is_shop() ||			     
			     is_product_tag() ||
			     is_cart() ||
			     is_checkout() ||
			     is_account_page() ||
			     is_wc_endpoint_url()
			) {

				$apply = false;
			}

		}

		return $apply;
	}
);
/**
 * Beaver Builder Plugin Customization
 * for remove excluding heading contents
 * @since 2.0.34
 */
add_filter(
	'fl_builder_layout_data',
	'ez_toc_flbuilder_layout_data',
	12,
	1
);
function ez_toc_flbuilder_layout_data( $data ) {
	if( has_action( 'the_content' ) ) {
		if(!empty($data)){
			foreach( $data as $nodeKey => $node ){		
				$data[$nodeKey] = $node;
			}
		}				
	}
	return $data;
}

/**
 * Kalium - Medical Theme Compatibility
 * remove duplicate eztoc containers
 * in faq sections
 * @since 2.0.38
 */
if ( in_array( 'js_composer/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && ( 'Kalium - Medical Theme' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) || 'Kalium' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) ) {
    add_shortcode( 'vc_toggle', 'eztoc_vc_toggle_modified' );
    function eztoc_vc_toggle_modified( $atts, $content, $tag ) {
        if ( 'vc_toggle' == $tag ) {

            /**
             * Shortcode attributes
             * @var $atts
             * @var $title
             * @var $el_class
             * @var $style
             * @var $color
             * @var $size
             * @var $open
             * @var $css_animation
             * @var $el_id
             * @var $content - shortcode content
             * @var $css
             * Shortcode class
             * @var WPBakeryShortCode_Vc_Toggle $this_WPBakeryShortCode_Vc_Toggle
             */
            $title = $el_class = $style = $color = $size = $open = $css_animation = $css = $el_id = '';

            $inverted = false;
            $atts = vc_map_get_attributes('vc_toggle', $atts);
            extract($atts);

    // checking is color inverted
            $style = str_replace('_outline', '', $style, $inverted);
            /**
             * @since 4.4
             */
            $elementClass = array(
                'base' => apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'vc_toggle', 'vc_toggle', $atts),
                // TODO: check this code, don't know how to get base class names from params
                'style' => 'vc_toggle_' . $style,
                'color' => ( $color ) ? 'vc_toggle_color_' . $color : '',
                'inverted' => ( $inverted ) ? 'vc_toggle_color_inverted' : '',
                'size' => ( $size ) ? 'vc_toggle_size_' . $size : '',
                'open' => ( 'true' === $open ) ? 'vc_toggle_active' : '',
                'extra' => $atts['css'],
                'css_animation' => '',
                    // TODO: remove getCssAnimation as function in helpers
            );

            $class_to_filter = trim(implode(' ', $elementClass));
            $class_to_filter .= vc_shortcode_custom_css_class($css, ' ');
            $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, 'vc_toggle', $atts);

            $heading_output = apply_filters('wpb_toggle_heading', $atts['title'], array(
                'title' => $title,
                'open' => $open,
                    ));
            $output = '<div ' . ( isset( $el_id ) && ! empty( $el_id ) ? 'id="' . esc_attr( $el_id ) . '"' : '' ) . ' class="' . esc_attr( $css_class ) . '"><div class="vc_toggle_title">' . $heading_output . '<i class="vc_toggle_icon"></i></div><div class="vc_toggle_content">' . wpb_js_remove_wpautop( $content, true ) . '</div></div>';

            return $output;
        }
    }
}

/**
 * Poka Theme Compatibility
 * remove duplicate eztoc containers
 * in header & footer sections
 * @since 2.0.46
 */
if ( 'PokaTheme' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {
    add_action( 'poka_before_main', 'ez_toc_poka_before_main', 4, 0 );
    function ez_toc_poka_before_main() {
        remove_action('poka_before_main', 'poka_before_content', 5, 0);
        remove_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );
        add_action( 'poka_before_main', 'poka_before_content', 5, 0 );
    }
    add_action( 'poka_before_main', 'ez_toc_poka_before_main_after', 6, 0 );
    function ez_toc_poka_before_main_after() {
        add_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );
    }
    add_action( 'poka_after_main', 'ez_toc_poka_after_main', 3, 0 );
    function ez_toc_poka_after_main() {
        remove_action( 'poka_after_main', 'poka_banner_footer', 4, 0 );
        remove_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );
        add_action( 'poka_after_main', 'poka_banner_footer', 4, 0 );
    }
    add_action( 'poka_after_main', 'ez_toc_poka_after_main_after', 5, 0 );
    function ez_toc_poka_after_main_after() {
        add_filter( 'the_content', array( 'ezTOC', 'the_content' ), 100 );
    }
}

if ( 'MAKE9 Divi zh-tw Child' == apply_filters( 'current_theme', get_option( 'current_theme' ) ) ) {
    add_filter('ez_toc_regex_filteration', 'ez_toc_regex_filteration_for_divi_chinese');
    function ez_toc_regex_filteration_for_divi_chinese( $regEx ) {
        $regEx = '/(<h([1-6]{1})(?:(?!\bclass="et_pb_slide_title")[^>])*>)(.*)<\/h\2>/msuU';
    
        return $regEx;
    }
}

if ( in_array( 'lasso/affiliate-plugin.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    add_filter('ez_toc_regex_filteration', 'ez_toc_regex_filteration_for_lasso_products');
    function ez_toc_regex_filteration_for_lasso_products( $regEx ) {
        $regEx = '/(<(?:h|H){1}([1-6]{1})[^>]*>)(.*)<\/(?:h|H){1}\2>/msuU';

        return $regEx;
    }
}

/**
 * Grow, Social Pro by Mediavine plugin compatibility
 * Anchors were not being generated for special char like inverted comma.
 * @since 2.0.52
 */
add_filter('ez_toc_extract_headings_content', 'ez_toc_social_pro_by_mediavine_com',10,1);

function ez_toc_social_pro_by_mediavine_com($content){
	
	if(class_exists( '\Mediavine\Grow\Shortcodes' ) && ezTOC_Option::get('mediavine-create') == 1){

		$settings = Mediavine\Grow\Settings::get_setting( 'dpsp_pinterest_share_images_setting', [] );		
		if ( !empty( $settings['share_image_page_builder_compatibility'] ) || ! empty( $settings['share_image_lazy_load_compatibility'] )  ) {
			$content = mb_convert_encoding( html_entity_decode($content), 'HTML-ENTITIES', 'UTF-8' );	
		}
						
	}
		
	return $content;
}

/**
 * Parse Gutenberg reusable block
 * @since 2.0.53
 */
add_filter('ez_toc_modify_process_page_content', 'ez_toc_parse_gutenberg_reusable_block',10,1);

function ez_toc_parse_gutenberg_reusable_block($content){
	
	if(function_exists('do_blocks')){
		if(has_block('easytoc/toc')){
			$content =  str_replace( '<!-- wp:easytoc/toc /-->', 'eztoctempblock', $content );		
			$content = do_blocks($content);
			$content =  str_replace( 'eztoctempblock', '<!-- wp:easytoc/toc /-->', $content );				
		}else{
			$content = do_blocks($content);
		}			
	}
	return $content;
}

/**
 * Create by Mediavine plugin compatibility
 * shortcode were not being parse for custom post type mv_create added by this plugin inside post content
 * @since 2.0.52
 */
add_filter('ez_toc_modify_process_page_content', 'ez_toc_parse_mv_create_shortcode',10,1);

function ez_toc_parse_mv_create_shortcode($content){
	
	if ( in_array( 'mediavine-create/mediavine-create.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && ezTOC_Option::get('mediavine-create') == 1) {
		if ( has_shortcode( $content, 'mv_create' )) {
			$content = do_shortcode($content);		
		}		
	}			
	return $content;
}
/**
 * Remove the Create by Mediavine node from the post content before extracting headings.
 *
 * @link https://wordpress.org/plugins/mediavine-create/
 * @since 2.0.8
 * Modifyed in 2.0.52
 */
add_filter(
	'ez_toc_exclude_by_selector',
	function( $selectors ) {
		if(ezTOC_Option::get('mediavine-create') != 1){
			$selectors['mediavine-create'] = '.mv-create-card';
		}	
		return $selectors;
	}
);


/**
 * UpSolution Core compatibility
 * @link https://help.us-themes.com/impreza/us-core/
 * @since 2.0.54
 */

 add_filter('ez_toc_sidebar_has_toc_filter', 'ez_toc_sidebar_has_toc_status_us_core', 10,1);

 function ez_toc_sidebar_has_toc_status_us_core($status){
 
	if(function_exists('us_get_page_area_id')){
		$content_template_id = us_get_page_area_id( 'content' );
		$content_template    = get_post( (int) $content_template_id );
		if(isset($content_template->post_content)){
			if ( has_shortcode( $content_template->post_content, 'toc' ) || has_shortcode( $content_template->post_content, 'ez-toc' ) ) {
				$status = true;				
			}
		}
	}

	 return $status;
}

/**
 * Custom Field Suite plugin sidebar compatibility
 *
 * @link https://wordpress.org/plugins/custom-field-suite/
 * @since 2.0.52
 *
 */

add_filter('ez_toc_sidebar_has_toc_filter', 'ez_toc_sidebar_has_toc_status_cfs', 10,1);

function ez_toc_sidebar_has_toc_status_cfs($status){

	global $post;
	if(function_exists('CFS')){
		$fields = CFS()->get(false, $post->ID);
		if(isset($fields['use_ez_toc']) &&  $fields['use_ez_toc'] == true){
			$status = true;
		}

	}
	
	return $status;
}

/**
 * Block Editor Template
 *
 * @link https://developer.wordpress.org/block-editor/
 * @since 2.0.54
 *
 */

 if(function_exists('wp_is_block_theme') && wp_is_block_theme()){
	add_filter('ez_toc_sidebar_has_toc_filter', 'ez_toc_guttenberg_has_toc', 10,1);
 }

 function ez_toc_guttenberg_has_toc($status){

	$block_post_template = get_block_template(get_stylesheet() . '//' .'single');
	$block_page_template = get_block_template(get_stylesheet() . '//' .'page');
	if(is_single() && is_object($block_post_template) && (has_shortcode($block_post_template->content,'toc') || has_shortcode($block_post_template->content,'ez-toc')))
	{
		$status=true;
	}
	if(is_page() && is_object($block_post_template) && (has_shortcode($block_page_template->content,'toc') || has_shortcode($block_page_template->content,'ez-toc')))
	{
		$status=true;
	}
	return $status;
 }

 if(function_exists('init_goodlayers_core_system') && ezTOC_Option::get('goodlayers-core') == 1){

	eztoc_enable_output_buffer_filter();
	add_filter('eztoc_wordpress_final_output', 'ez_toc_gdlr_core_the_content', 10, 1);

function ez_toc_gdlr_core_the_content($content){
	if(is_admin()){
		return $content;
	}
	$post     = ezTOC::get( get_the_ID() );
	if($post){
		$find    = $post->getHeadings();	
		$replace = $post->getHeadingsWithAnchors();
		$toc     = $post->getTOC();
		if ( !is_array($content ) && !empty( $find ) && !empty( $replace ) && !empty( $content ) ){
			$content =  str_replace( $find, $replace, $content );
		}

		if ( strpos('id="ez-toc-container"',$content) === false ){
			$content =  str_replace( '<div class="traveltour-single-article-content">', '<div class="traveltour-single-article-content">'.$toc, $content );
		}
	} 
	
	return $content;
}

// function to get combined content of goodlayers builder
function ezTOC_gdlr_core()
{
   $postID =  get_the_ID(); 
   $gdlr_core_builder  = get_post_meta( $postID ,'gdlr-core-page-builder' , false );
   $gdlr_core_builder = isset($gdlr_core_builder[0])?$gdlr_core_builder[0]:$gdlr_core_builder;
   $content="";
   if(!empty($gdlr_core_builder) && is_array($gdlr_core_builder))
   {
     foreach($gdlr_core_builder as $element)
     {
		
		if(isset($element['items']))
		{
			
			foreach($element['items'] as $inner_element){
				if($inner_element['template'] === 'element'){
					$content= $content . ez_toc_gdlr_core_fetch_content($inner_element);
			    }else if(isset($inner_element['items'])){
					foreach($inner_element['items'] as $level2_element){
						if($level2_element['template'] === 'element'){
							$content= $content . ez_toc_gdlr_core_fetch_content($level2_element);
						}
					}
				}
			}
	   }

	   if(isset($element['template']) && $element['template'] === 'element'){
		$content= $content . ez_toc_gdlr_core_fetch_content($element);
   }
}
}
   return $content;
} 

function ez_toc_gdlr_core_fetch_content($element){
	if(isset($element['type']) && isset($element['value'])){
		$type = $element['type'];
		$element_class = 'gdlr_core_pb_element_'.$type;
			if(class_exists($element_class)){
				return $element_class::get_content($element['value']);
			}
	}
	return '';
}
// Adding Goodlayers Content  to create combined toc
add_filter( 'ez_toc_modify_process_page_content', 'ez_toc_gdlr_core_process_page_content', 10, 1 );
function ez_toc_gdlr_core_process_page_content( $content )
{

    if (function_exists( 'ezTOC_gdlr_core' ) )
    {
        $eztoc_gdlr_core_content = ezTOC_gdlr_core();
        $content = $content . $eztoc_gdlr_core_content;
    }
    return $content;
}

}

if(function_exists('rest_get_url_prefix') && ezTOC_Option::get('disable_in_restapi') == 1){
	add_filter( 'ez_toc_modify_process_page_content', 'ez_toc_check_for_wp_json_request', 999, 1 );
	function ez_toc_check_for_wp_json_request($content){
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return $content;
		}
		$rest_prefix         = trailingslashit( rest_get_url_prefix() );
		if(strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) !== false){
			return '';
		}
		return $content;
	}
}

/**
 * Molongui Authorship plugin compatibility
 * @link https://wordpress.org/plugins/molongui-authorship/
 * @since 2.0.56
 */
if( function_exists( 'molongui_authorship_load_plugin_textdomain' ) && ezTOC_Option::get('molongui-authorship') == 1 ){
add_filter( 'ez_toc_modify_process_page_content', 'ez_toc_content_molongui_authorship');
	function ez_toc_content_molongui_authorship($content){
		if(!empty($content))
		{
			if(strpos($content, "m-a-box-container") !== false){

					libxml_use_internal_errors(true);
					$dom = new DOMDocument();
					$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
					$xpath = new DOMXPath($dom);
					if($xpath){
						$divs = $xpath->query('//div[@class="m-a-box-container"]');
							if($divs){
								foreach ($divs as $div) {
									$div->parentNode->removeChild($div);
								}
							}					
						// Save the modified HTML content
						$modifiedHtml = $dom->saveHTML();
						// Return the modified HTML content
						return $modifiedHtml;
					}

			}
			
		}
		return $content;		
	}
}

/* 
* Compatibility for Walker News Template
* @since 2.0.56
*/
if(function_exists('wp_get_theme')){
    $theme_data = wp_get_theme();
	if(!empty($theme_data) && $theme_data->get_template()== 'walker-news-template')
    {
		add_filter( 'ez_toc_sidebar_has_toc_filter', 'ez_toc_walker_news_template_fix');

		function ez_toc_walker_news_template_fix($status){
			
				$content = get_the_content();
				if(function_exists('do_blocks')){
					$content = do_blocks($content);
				}
				if(has_shortcode($content,'toc') || has_shortcode($content,'ez-toc')){
					return true;
				}

				return 	$status;
			}	
		
	}
	
}

/**
 * Mediavine trellis compatibility
 * @since 2.0.57
 */
add_filter('eztoc_modify_the_content','eztoc_mediavine_trellis_content_improver');
function eztoc_mediavine_trellis_content_improver($content){
	if(class_exists('Mediavine\Trellis\Custom_Content') && ezTOC_Option::get('mediavine-create') == 1 ){
		$content = mb_convert_encoding( html_entity_decode($content), 'HTML-ENTITIES', 'UTF-8' );
	}
	return $content;
}

//Perfmatters Compatibility
add_filter('ez_toc_pro_inline_css','ez_toc_perfmatters_touch_css');
function ez_toc_perfmatters_touch_css($css){
	if('css' == ezTOC_Option::get( 'toc_loading' ) && class_exists('Perfmatters\Config') && !empty(Perfmatters\Config::$options['assets']['delay_js']) && !empty(Perfmatters\Config::$options['assets']['fastclick'])) {
	 	$css .= 'label > * { pointer-events:none; }';
	}
  	return $css;
}
/**
 * Woodmart + WPbakery Gallery compatibility
 * @since 2.0.58
 */
function ez_toc_woodmart_gallery_fix(){
	if(function_exists('woodmart_load_classes') && class_exists('Vc_Manager')){
		
		if(!wp_style_is('el-section-title')){
			wp_register_style( 'el-section-title', WOODMART_THEME_DIR.'/css/parts/el-section-title.min.css', array() ,ezTOC::VERSION );
			wp_enqueue_style( 'el-section-title' );
		}
		
		if(!wp_style_is('wd-section-title-style-simple-and-brd')){
		wp_register_style( 'wd-section-title-style-simple-and-brd', WOODMART_THEME_DIR.'/css/parts/el-section-title-style-simple-and-brd.min.css', array() ,ezTOC::VERSION);
		wp_enqueue_style( 'wd-section-title-style-simple-and-brd' );
		}
		
		if(!wp_style_is('wd-owl-carousel')){
			wp_register_style( 'wd-owl-carousel', WOODMART_THEME_DIR.'/css/parts/lib-owl-carousel.min.css', array() ,ezTOC::VERSION);
			wp_enqueue_style( 'wd-owl-carousel' );
		}
		
		if(!wp_style_is('wd-image-gallery')){
			wp_register_style( 'wd-image-gallery', WOODMART_THEME_DIR.'/css/parts/el-gallery.min.css', array() ,ezTOC::VERSION);
			wp_enqueue_style( 'wd-image-gallery' );
		}

		if(!wp_style_is('wd-accordion')){
			wp_register_style( 'wd-accordion', WOODMART_THEME_DIR.'/css/parts/el-accordion.min.css', array() ,ezTOC::VERSION);
			wp_enqueue_style( 'wd-accordion' );
		}
		
		if(!wp_style_is('wd-tabs')){
			wp_register_style( 'wd-tabs', WOODMART_THEME_DIR.'/css/parts/el-tabs.min.css', array() ,ezTOC::VERSION);
			wp_enqueue_style( 'wd-tabs' );
		}

		if(!wp_style_is('wd-team-member')){
			wp_register_style( 'wd-team-member', WOODMART_THEME_DIR.'/css/parts/el-team-member.min.css', array() ,ezTOC::VERSION);
			wp_enqueue_style( 'wd-team-member' );
		}
			
	}	
}
add_action('wp_enqueue_scripts', 'ez_toc_woodmart_gallery_fix');

/**
 * Ad inserter plugin compatibility
 * url : https://wordpress.org/plugins/ad-inserter/
 * When toc shortcode being added inside ad inserter block, it runs infinite loop. Below code is the solution for it.
 * @since 2.0.62
 * return boolean
 */
add_filter('ez_toc_apply_filter_status_manually', 'ez_toc_adinserter_block_has_toc_shortcode',10,1);

function ez_toc_adinserter_block_has_toc_shortcode($status){
	
	if ( in_array( 'ad-inserter/ad-inserter.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		global $block_object;		
		for ($block = 0; $block <= 96; $block ++) {
			if(isset($block_object [$block])){
				$obj = $block_object [$block];				
				if(is_object($obj) && is_array($obj->wp_options)){
					if(has_shortcode( $obj->wp_options['code'] , 'ez-toc' ) || has_shortcode( $obj->wp_options['code'] , 'toc' )){
						$status = false;
						break;			
					}
				}
			}
			
		}

	}
	
	return $status;
}

/**
 * Current Year, Symbols and IP Shortcode compatibility
 * shortcode were not being parse for heading title in elementor
 * plugin url : https://wordpress.org/plugins/current-year-shortcode/
 * @since 2.0.62
 */
add_filter('ez_toc_table_heading_title_anchor', 'ez_toc_parse_curreny_year_shortcode',10,1);
add_filter('ez_toc_content_heading_title', 'ez_toc_parse_curreny_year_shortcode',10,1);
add_filter('ez_toc_content_heading_title_anchor', 'ez_toc_parse_curreny_year_shortcode',10,1);

function ez_toc_parse_curreny_year_shortcode($content){
	
	if ( in_array( 'current-year-shortcode/year-kgm.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )) {
			$content = do_shortcode($content);			
	}			
	return $content;
}

/** Pressbook theme Compatiblity
 * @since 2.0.63
 * @param bool $status The current status of applying the TOC filter.
 * @return bool The updated status of applying the TOC filter.
 */
add_filter('ez_toc_apply_filter_status_manually', 'ez_toc_press_books_theme_compatibility',10,1);
function ez_toc_press_books_theme_compatibility($status){
  if(function_exists('wp_get_theme')){
    $active_theme = wp_get_theme();
    if(!empty($active_theme) && $active_theme->get( 'Name' ) == 'McLuhan'){
      $status = false;
    }
  }
  return $status;
}


/**
 * Divi Builder Compatibility
 * @since 2.0.64
 */
add_filter(
	'ez_toc_strip_shortcodes_with_inner_content',
	function( $tags_to_remove ) {
		$shortcodes = array (
			'et_pb_text'
		);
		$tags_to_remove = array_merge( $tags_to_remove, $shortcodes );
		return $tags_to_remove;
	}

);