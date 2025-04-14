<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

global $epanelMainTabs, $themename, $shortname, $options, $wp_registered_sidebars;

$epanelMainTabs = array(
	'general',
	'navigation',
	'layout',
	'ad',
	'seo',
	'integration',
	'updates',
);

$cats_array = get_categories( 'hide_empty=0' );
$pages_array = get_pages( 'hide_empty=0' );
$pages_number = count( $pages_array );

$site_pages = array();
$site_cats = array();
$pages_ids = array();
$share_networks = array();
$share_networks_helper = array();
$cats_ids = array();
$sidebar_options = array();

foreach ($pages_array as $pagg) {
	$site_pages[$pagg->ID] = htmlspecialchars( $pagg->post_title );
	$pages_ids[] = $pagg->ID;
}

foreach ($cats_array as $categs) {
	$site_cats[$categs->cat_ID] = $categs->cat_name;
	$cats_ids[] = $categs->cat_ID;
}

$networks = ET_Social_Share::get_networks();
foreach ( $networks as $network ) {
	$share_networks_helper[$network->slug] = $network->name;
	$share_networks[] = $network->slug;
}

if ( $wp_registered_sidebars && is_array( $wp_registered_sidebars ) ) {
	foreach ( $wp_registered_sidebars as $id => $options ) {
		$sidebar_options[$id] = $options['name'];
	}
}

$shortname 	= esc_html( $shortname );
$pages_ids 	= array_map( 'intval', $pages_ids );
$cats_ids 	= array_map( 'intval', $cats_ids );

$child_theme_active = is_child_theme();

$options = array(

	array(
		"name" => "wrap-general",
		"type" => "contenttab-wrapstart",
	),

	array( "type" => "subnavtab-start"),

	array(
		'name' => 'general-1',
		'type' => 'subnav-tab',
		'desc' => esc_html__( 'General', 'Extra' ),
	),
	array(
		'name' => 'general-2',
		'type' => 'subnav-tab',
		'desc' => esc_html__( 'Performance', 'Extra' ),
	),
	array( "type" => "subnavtab-end"),

	array(
		"name" => "general-1",
		"type" => "subcontent-start",
	),

	array(
		"name" => esc_html__( "Logo", $themename ),
		"id"   => $shortname . "_logo",
		"type" => "upload",
		"std"  => "",
		"desc" => esc_html__( "If you would like to use your own custom logo image click the Upload Image button.", $themename ),
	),

	array(
		"name" => esc_html__( "Favicon", $themename ),
		"id"   => $shortname . "_favicon",
		"type" => "upload",
		"std"  => "",
		"desc" => esc_html__( "If you would like to use your own custom favicon image click the Upload Image button.", $themename ),
	),

	array( 'type' => 'clearfix' ),

	array(
		"name" => esc_html__( "Global Accent Color", $themename ),
		"type" => "textcolorpopup",
		"id"   => "accent_color",
		"std"  => extra_global_accent_color(),
		"desc" => esc_html__( "Here you can change Global Accent Color that is used on all pages of your site.", $themename ),
	),

	array(
		"name" => esc_html__( "Fixed Navigation Bar", $themename ),
		"id"   => $shortname . "_fixed_nav",
		"type" => "checkbox",
		"std"  => "on",
		"desc" => esc_html__( "By default the navigation bar stays on top of the screen at all times. We suggest to disable this option, if you need to use a logo taller than the default one.", $themename ),
	),

	array( "type" => "clearfix" ),

	array( "type" => "clearfix" ),

	array(
		"name"           => esc_html__( "Sidebar Location", $themename ),
		"id"             => "sidebar_location",
		"type"           => "select",
		"options"        => array(
			'right' => esc_html__( 'Right', $themename ),
			'left'  => esc_html__( 'Left', $themename ),
			'none'  => esc_html__( 'No Sidebar', $themename ),
		),
		"std"            => 'right',
		"desc"           => esc_html__( "Here you can choose the default location of the Sidebar.", $themename ),
		'et_save_values' => true,
	),
	array(
		"name"           => esc_html__( "WooCommerce Sidebar Location", $themename ),
		"id"             => "woocommerce_sidebar_location",
		"type"           => "select",
		"options"        => array(
			'right' => esc_html__( 'Right', $themename ),
			'left'  => esc_html__( 'Left', $themename ),
			'none'  => esc_html__( 'No Sidebar', $themename ),
		),
		"std"            => 'right',
		"desc"           => esc_html__( "Here you can choose the default location of the Sidebar for WooCommerce pages.", $themename ),
		'et_save_values' => true,
	),
	array(
		"name"           => esc_html__( "Sidebar/Widget Area", $themename ),
		"id"             => "sidebar",
		"type"           => "select",
		"options"        => $sidebar_options,
		"desc"           => esc_html__( "Here you can choose the default Sidebar/Widget Area.", $themename ),
		'et_save_values' => true,
	),
	array(
		"name"           => esc_html__( "WooCommerce Sidebar/Widget Area", $themename ),
		"id"             => "woocommerce_sidebar",
		"type"           => "select",
		"options"        => $sidebar_options,
		"desc"           => esc_html__( "Here you can choose the default Sidebar/Widget Area for WooCommerce pages.", $themename ),
		'et_save_values' => true,
	),
	array(
		"name"           => esc_html__( "Social Network Follower Count Refresh", $themename ),
		"id"             => "social_followers_transient_expiration",
		"type"           => "select",
		"options"        => array(
			( HOUR_IN_SECONDS )      => esc_html__( 'Every Hour', $themename ),
			( HOUR_IN_SECONDS * 3 )  => esc_html__( 'Every 3 Hours', $themename ),
			( HOUR_IN_SECONDS * 12 ) => esc_html__( 'Every 12 Hours', $themename ),
			( HOUR_IN_SECONDS * 24 ) => esc_html__( 'Every 1 Day', $themename ),
			( DAY_IN_SECONDS * 3 )   => esc_html__( 'Every 3 Days', $themename ),
			( DAY_IN_SECONDS * 7 )   => esc_html__( 'Every 7 Days', $themename ),
		),
		"std"            => ( HOUR_IN_SECONDS * 3 ),
		"desc"           => esc_html__( "Here you can choose the refresh period used for the social network follower counts widget.", $themename ),
		'et_save_values' => true,
	),

	array(
		"name"              => esc_html__( "Google API Key", $themename ),
		"id"                => "et_google_api_settings_api_key",
		"std"               => "",
		"type"              => "text",
		"validation_type"   => "nohtml",
		'is_global'         => true,
		'main_setting_name' => 'et_google_api_settings',
		'sub_setting_name'  => 'api_key',
		"desc"              => et_get_safe_localization( sprintf( __( 'The Maps module uses the Google Maps API and requires a valid Google API Key to function. Before using the map module, please make sure you have added your API key here. Learn more about how to create your Google API Key <a target="_blank" href="%1$s">here</a>.', $themename ), 'http://www.elegantthemes.com/gallery/divi/documentation/map/' ) ),
	),

	array(
		"name"              => esc_html__( "Enqueue Google Maps Script", $themename ),
		"id"                => "et_enqueue_google_maps_script",
		"main_setting_name" => "et_google_api_settings",
		"sub_setting_name"  => 'enqueue_google_maps_script',
		'is_global'         => true,
		"type"              => "checkbox",
		"std"               => "on",
		"desc"              => esc_html__( "Disable this option to remove Google Maps API script from your page. Note: Modules which relies to Google Maps API script such as Maps and Fullwidth Maps are still loaded, but will not work unless you manually add Google Maps API script.", $themename ),
	),

	array(
		"name"              => esc_html__( "Use Google Fonts", $themename ),
		"id"                => "et_use_google_fonts",
		"main_setting_name" => "et_google_api_settings",
		"sub_setting_name"  => 'use_google_fonts',
		'is_global'         => true,
		"type"              => "checkbox",
		"std"               => "on",
		"desc"              => esc_html__( "Disable this option to remove the Google Fonts from your Divi Builder Pages.", $themename ),
	),
	array(
		'name' => esc_html__( 'Google Fonts subsets', 'Extra' ),
		'id'   => $shortname . '_gf_enable_all_character_sets',
		'type' => 'checkbox',
		'std'  => 'false',
		'desc' => esc_html__( 'This will enable Google Fonts for Non-English languages.', 'Extra' ),
	),
	array( 'type' => 'clearfix' ),

	array(
		"name" => esc_html__( "Show Facebook Icon", $themename ),
		"id"   => "show_facebook_icon",
		"type" => "checkbox",
		"std"  => "on",
		"desc" => esc_html__( "Here you can choose to display the Facebook Icon on your homepage.", $themename ),
	),

	array(
		"name" => esc_html__( "Show Twitter Icon", $themename ),
		"id"   => "show_twitter_icon",
		"type" => "checkbox2",
		"std"  => "on",
		"desc" => esc_html__( "Here you can choose to display the Twitter Icon.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name" => esc_html__( "Show Google+ Icon", $themename ),
		"id"   => "show_googleplus_icon",
		"type" => "checkbox",
		"std"  => "on",
		"desc" => esc_html__( "Here you can choose to display the Google+ Icon on your homepage.", $themename ),
	),

	array(
		"name" => esc_html__( "Show Pinterest Icon", $themename ),
		"id"   => "show_pinterest_icon",
		"type" => "checkbox2",
		"std"  => "on",
		"desc" => esc_html__( "Here you can choose to display the Pinterest Icon on your homepage.", $themename ),
	),

	array( "type" => "clearfix"),


	array(
		"name" => esc_html__( "Show Tumblr Icon", $themename ),
		"id"   => "show_tumblr_icon",
		"type" => "checkbox",
		"std"  => "on",
		"desc" => esc_html__( "Here you can choose to display the Tumblr Icon on your homepage.", $themename ),
	),

	array(
		"name" => esc_html__( "Show Stumbleupon Icon", $themename ),
		"id"   => "show_stumbleupon_icon",
		"type" => "checkbox2",
		"std"  => "on",
		"desc" => esc_html__( "Here you can choose to display the Stumbleupon Icon on your homepage.", $themename ),
	),

	array( "type" => "clearfix"),


	array(
		"name" => esc_html__( "Show Instagram Icon", $themename ),
		"id"   => "show_instagram_icon",
		"type" => "checkbox",
		"std"  => "on",
		"desc" => esc_html__( "Here you can choose to display the Instagram Icon on your homepage.", $themename ),
	),

	array(
		"name" => esc_html__( "Show Youtube Icon", $themename ),
		"id"   => "show_youtube_icon",
		"type" => "checkbox2",
		"std"  => "on",
		"desc" => esc_html__( "Here you can choose to display the Youtube Icon on your homepage.", $themename ),
	),

	array( "type" => "clearfix"),


	array(
		"name" => esc_html__( "Show Dribbble Icon", $themename ),
		"id"   => "show_dribbble_icon",
		"type" => "checkbox",
		"std"  => "on",
		"desc" => esc_html__( "Here you can choose to display the Dribbble Icon on your homepage.", $themename ),
	),

	array(
		"name" => esc_html__( "Show RSS Icon", $themename ),
		"id"   => "show_rss_icon",
		"type" => "checkbox2",
		"std"  => "on",
		"desc" => esc_html__( "Here you can choose to display the RSS Icon.", $themename ),
	),

	array( "type" => "clearfix"),


			// start urls
	array(
		"name"            => esc_html__( "Facebook Profile Url", $themename ),
		"id"              => "facebook_url",
		"std"             => "",
		"type"            => "text",
		"validation_type" => "url",
		"desc"            => esc_html__( "Enter the URL of your Facebook Profile.", $themename ),
	),

	array(
		"name"            => esc_html__( "Twitter Profile Url", $themename ),
		"id"              => "twitter_url",
		"std"             => "",
		"type"            => "text",
		"validation_type" => "url",
		"desc"            => esc_html__( "Enter the URL of your Twitter Profile.", $themename ),
	),

	array(
		"name"            => esc_html__( "Google+ Profile Url", $themename ),
		"id"              => "googleplus_url",
		"std"             => "",
		"type"            => "text",
		"validation_type" => "url",
		"desc"            => esc_html__( "Enter the URL of your Google+ Profile.", $themename ),
	),

	array(
		"name"            => esc_html__( "Pinterest Profile Url", $themename ),
		"id"              => "pinterest_url",
		"std"             => "",
		"type"            => "text",
		"validation_type" => "url",
		"desc"            => esc_html__( "Enter the URL of your Pinterest Profile.", $themename ),
	),

	array(
		"name"            => esc_html__( "Tumblr Profile Url", $themename ),
		"id"              => "tumblr_url",
		"std"             => "",
		"type"            => "text",
		"validation_type" => "url",
		"desc"            => esc_html__( "Enter the URL of your Tumblr Profile.", $themename ),
	),

	array(
		"name"            => esc_html__( "Stumbleupon Profile Url", $themename ),
		"id"              => "stumbleupon_url",
		"std"             => "",
		"type"            => "text",
		"validation_type" => "url",
		"desc"            => esc_html__( "Enter the URL of your Stumbleupon Profile.", $themename ),
	),

	array(
		"name"            => esc_html__( "Instagram Profile Url", $themename ),
		"id"              => "instagram_url",
		"std"             => "",
		"type"            => "text",
		"validation_type" => "url",
		"desc"            => esc_html__( "Enter the URL of your Instagram Profile.", $themename ),
	),

	array(
		"name"            => esc_html__( "Youtube Profile Url", $themename ),
		"id"              => "youtube_url",
		"std"             => "",
		"type"            => "text",
		"validation_type" => "url",
		"desc"            => esc_html__( "Enter the URL of your Youtube Profile.", $themename ),
	),

	array(
		"name"            => esc_html__( "Dribbble Profile Url", $themename ),
		"id"              => "dribbble_url",
		"std"             => "",
		"type"            => "text",
		"validation_type" => "url",
		"desc"            => esc_html__( "Enter the URL of your Dribbble Profile.", $themename ),
	),

	array(
		"name"            => esc_html__( "RSS Icon Url", $themename ),
		"id"              => "rss_url",
		"std"             => "",
		"type"            => "text",
		"validation_type" => "url",
		"desc"            => esc_html__( "Enter the URL of your RSS feed.", $themename ),
	),

	array(
		"name"            => esc_html__( "Number of Posts displayed on Archive pages", $themename ),
		"id"              => $shortname . "_archivenum_posts",
		"std"             => "5",
		"type"            => "text",
		"desc"            => esc_html__( "Here you can designate how many recent articles are displayed on the Archive pages. This option works independently from the Settings > Reading options in wp-admin.", $themename ),
		"validation_type" => "number",
	),

	array(
		"name"            => esc_html__( "Number of Products displayed on WooCommerce archive pages", $themename ),
		"id"              => $shortname . "_woocommerce_archive_num_posts",
		"std"             => "9",
		"type"            => "text",
		"validation_type" => "number",
		"desc"            => esc_html__( "Here you can designate how many WooCommerce products are displayed on the archive page. This option works independently from the Settings > Reading options in wp-admin.", $themename),
	),

	array(
		"name"            => esc_html__( "Number of Posts displayed on Category page", $themename ),
		"id"              => $shortname . "_catnum_posts",
		"std"             => "5",
		"type"            => "text",
		"validation_type" => "number",
		"desc"            => esc_html__( "Here you can designate how many recent articles are displayed on the Category page. This option works independently from the Settings > Reading options in wp-admin.", $themename ),
	),

	array(
		"name"            => esc_html__( "Number of Posts displayed on Search pages", $themename ),
		"id"              => $shortname . "_searchnum_posts",
		"std"             => "5",
		"type"            => "text",
		"desc"            => esc_html__( "Here you can designate how many recent articles are displayed on the Search results pages. This option works independently from the Settings > Reading options in wp-admin.", $themename ),
		"validation_type" => "number",
	),

	array(
		"name"            => esc_html__( "Number of Posts displayed on Tag pages", $themename ),
		"id"              => $shortname . "_tagnum_posts",
		"std"             => "5",
		"type"            => "text",
		"desc"            => esc_html__( "Here you can designate how many recent articles are displayed on the Tag pages. This option works independently from the Settings > Reading options in wp-admin.", $themename ),
		"validation_type" => "number",
	),

	array(
		"name"            => esc_html__( "Date format", $themename ),
		"id"              => $shortname . "_date_format",
		"std"             => "M j, Y",
		"type"            => "text",
		"desc"            => et_get_safe_localization( __( "This option allows you to change how your dates are displayed. For more information please refer to the WordPress codex here:<a href='http://codex.wordpress.org/Formatting_Date_and_Time' target='_blank'>Formatting Date and Time</a>", $themename ) ),
		'validation_type' => 'date_format',
	),

	array( "type" => "clearfix"),

	array(
		"name" => esc_html__( "Use excerpts when defined", $themename ),
		"id"   => $shortname . "_use_excerpt",
		"type" => "checkbox",
		"std"  => "false",
		"desc" => esc_html__( "This will enable the use of excerpts in posts or pages.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name" => esc_html__( "Back To Top Button", $themename ),
		"id"   => $shortname . "_back_to_top",
		"type" => "checkbox",
		"std"  => "false",
		"desc" => esc_html__( "Enable this option to display Back To Top Button while scrolling", $themename ),
	),

	array(
		"name" => esc_html__( "Smooth Scrolling", $themename ),
		"id"   => $shortname . "_smooth_scroll",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "Enable this option to get the smooth scrolling effect with mouse wheel", $themename ),
	),

	array( 'type' => 'clearfix' ),

	array(
		'name'            => esc_html__( 'Custom CSS', 'Extra' ),
		'id'              => $shortname . '_custom_css',
		'type'            => 'textarea',
		'std'             => '',
		'desc'            => esc_html__( 'Here you can add custom css to override or extend default styles.', 'Extra' ),
		'validation_type' => 'nohtml',
	),
	array(
		'name' => 'general-1',
		'type' => 'subcontent-end',
	),
	array(
		'name' => 'general-2',
		'type' => 'subcontent-start',
	),
	array(
		'name' => esc_html__( 'Dynamic Module Framework', 'Extra' ),
		'id'   => 'extra_dynamic_module_framework',
		'type' => 'checkbox',
		'std'  => 'on',
		'desc' => esc_html__( 'Enable this to allow the Divi Framework to only load the modules that are used on the page, and process the logic for the features in use.', 'Extra' ),
	),
	array(
		'name' => esc_html__( 'Dynamic CSS', 'Extra' ),
		'id'   => 'extra_dynamic_css',
		'type' => 'checkbox',
		'std'  => 'on',
		'desc' => esc_html__( 'Dynamic CSS greatly reduces CSS file size by dynamically generating only the assets necessary for the features and modules you use. This eliminates all file bloat and greatly improves load times.', 'Extra' ),
	),
	array(
		'name' => esc_html__( 'Dynamic Icons', 'Extra' ),
		'id'   => $child_theme_active ? 'extra_dynamic_icons_child_theme' : 'extra_dynamic_icons',
		'type' => 'checkbox',
		'std'  => $child_theme_active ? 'off' : 'on',
		'desc' => esc_html__( 'The Divi icon font is broken up into various subsets. These subsets are loaded only when needed based on the modules and features used on each page. If you need access to the entire icon font on all pages (for example, if you are using our icon font in your child theme), then you can disable this option and load the entire icon font library on all pages.', 'Extra' ),
	),
	array(
		'name' => esc_html__( 'Load Dynamic Stylesheet In-line', 'Extra' ),
		'id'   => 'extra_inline_stylesheet',
		'type' => 'checkbox',
		'std'  => 'off',
		'desc' => esc_html__( 'This option dequeues the Extra style.css file and prints the contents in-line. This removes a render blocking request and improves the PageSpeed scores of individual pages. However, it also prevents the style.css file from being cached. This option isn\'t recommended unless you are optimizing for a website with a single panding page.', 'Extra' ),
	),
	array(
		'name' => esc_html__( 'Critical CSS', 'Extra' ),
		'id'   => 'extra_critical_css',
		'type' => 'checkbox',
		'std'  => 'on',
		'desc' => esc_html__( 'Critical CSS greatly improves website load times and Google PageSpeed scores by deferring non-critical styles and eliminating render-blocking CSS requests.', 'Extra' ),
	),
	array(
		'name'    => esc_html__( 'Critical Threshold Height', 'Extra' ),
		'id'      => 'extra_critical_threshold_height',
		'options' => array( esc_html__( 'Low', 'Extra' ), esc_html__( 'Medium', 'Extra' ), esc_html__( 'High', 'Extra' ) ),
		'type'    => 'select',
		'std'     => 'Medium',
		'desc'    => esc_html__( 'When Critical CSS is enabled, Extra determines an "above the fold threshold" and defers all styles for elements below the fold. However, this threshold is just a estimate and can vary on different devices. Increasing threshold height will deffer fewer styles, resulting in slightly slower load times but less of a chance for Cumulative Layout Shifts to occur. If you are experiencing CLS issues you can increase the threshold height.', 'Extra' ),
	),
	array(
		'name' => esc_html__( 'Dynamic JavaScript Libraries', 'Extra' ),
		'id'   => 'extra_dynamic_js_libraries',
		'type' => 'checkbox',
		'std'  => 'on',
		'desc' => esc_html__( 'Only load external JavaScript libraries when they are needed by a specific Divi modules on the page. This removes unused JavaScript from the main scripts bundle and improves load times.', 'Extra' ),
	),
	array(
		'name' => esc_html__( 'Disable WordPress Emojis', 'Extra' ),
		'id'   => 'extra_disable_emojis',
		'type' => 'checkbox',
		'std'  => 'on',
		'desc' => esc_html__( 'WordPress loads it\'s own WordPress emojis. Modern browsers support native emojis, making WordPress\'s emojis unnecessary in most cases. Removing WordPress emojis removes unneeded assets and improves performance.', 'Extra' ),
	),
	array(
		'name' => esc_html__( 'Defer Gutenberg Block CSS', 'Extra' ),
		'id'   => 'extra_defer_block_css',
		'type' => 'checkbox',
		'std'  => 'on',
		'desc' => esc_html__( 'If a page is built with the Divi Builder, the Gutenberg block CSS file is moved from the header to the footer. Since the file is most likely not used, we can load it later. This removes a render blocking request and improving performance.', 'Extra' ),
	),
	array(
		'name' => esc_html__( 'Improve Google Fonts Loading', 'Extra' ),
		'id'   => 'extra_google_fonts_inline',
		'type' => 'checkbox',
		'std'  => 'off',
		'desc' => esc_html__( 'Enable cache Google Fonts and load them inline. This reduces render-blocking requests and improves page load times.', 'Extra' ),
	),
	array(
		'name' => esc_html__( 'Limit Google Fonts Support For Legacy Browsers', 'Extra' ),
		'id'   => 'extra_limit_google_fonts_support_for_legacy_browsers',
		'type' => 'checkbox',
		'std'  => 'on',
		'desc' => esc_html__( 'Enabling this option will lower the size of Google Fonts and improve load times, however it will limit Google Fonts support in some very old browsers. You can turn this off to increase support for older browsers at a slight cost to performance.', 'Extra' ),
	),
	array(
		'name' => esc_html__( 'Defer jQuery And jQuery Migrate', 'Extra' ),
		'id'   => 'extra_enable_jquery_body',
		'type' => 'checkbox',
		'std'  => 'on',
		'desc' => esc_html__( 'When possible, jQuery and jQuery Migrate will be moved to the body to speed up load times. If a third party plugin registers jQuery as a dependency, it will be moved back to the head.', 'Extra' ),
	),
	array(
		'name' => esc_html__( 'Enqueue jQuery Compatibility Script', 'Extra' ),
		'id'   => 'extra_enable_jquery_compatibility',
		'type' => 'checkbox',
		'std'  => 'on',
		'desc' => esc_html__( 'Some third party scripts may be incorrectly enqueued without declaring jQuery as dependency. If jQuery is deferred, these scripts could break. If you are experiencing console errors after enabling the "Defer jQuery And jQuery Migrate" option, you can enable this option, which will load an additional compatibility script that will attempt to solve the issue. You can turn this feature off if everything is working fine without it.', 'Extra' ),
	),
	array(
		'name' => esc_html__( 'Defer Additional Third Party Scripts', 'Extra' ),
		'id'   => 'extra_enable_jquery_body_super',
		'type' => 'checkbox',
		'std'  => 'off',
		'desc' => esc_html__( 'Warning: This can cause JavaScript errors in some cases and should be used with care. When enabled, scripts registered by plugins and themes will be deferred to improve performance and jQuery will always be loaded in the body.', 'Extra' ),
	),
	array(
		'name' => 'general-2',
		'type' => 'subcontent-end',
	),
	array(
		'name' => 'wrap-general',
		'type' => 'contenttab-wrapend',
	),

			//-------------------------------------------------------------------------------------//

	array(
		"name" => "wrap-navigation",
		"type" => "contenttab-wrapstart",
	),

	array( "type" => "subnavtab-start"),

	array(
		"name" => "navigation-1",
		"type" => "subnav-tab",
		"desc" => esc_html__( "Pages", $themename ),
	),

	array(
		"name" => "navigation-2",
		"type" => "subnav-tab",
		"desc" => esc_html__( "Categories", $themename ),
	),

	array(
		"name" => "navigation-3",
		"type" => "subnav-tab",
		"desc" => esc_html__( "General Settings", $themename ),
	),

	array( "type" => "subnavtab-end"),

	array(
		"name" => "navigation-1",
		"type" => "subcontent-start",
	),

	array(
		"name"    => esc_html__( "Exclude pages from the navigation bar", $themename ),
		"id"      => $shortname . "_menupages",
		"type"    => "checkboxes",
		"std"     => "",
		"desc"    => esc_html__( "Here you can choose to remove certain pages from the navigation menu. All pages marked with an X will not appear in your navigation bar.", $themename ),
		"usefor"  => "pages",
		"options" => $pages_ids,
	),

	array(
		"name" => esc_html__( "Show dropdown menus", $themename ),
		"id"   => $shortname . "_enable_dropdowns",
		"type" => "checkbox",
		"std"  => "on",
		"desc" => esc_html__( "If you would like to remove the dropdown menus from the pages navigation bar disable this feature.", $themename ),
	),

	array(
		"name" => esc_html__( "Display Home link", $themename ),
		"id"   => $shortname . "_home_link",
		"type" => "checkbox2",
		"std"  => "on",
		"desc" => esc_html__( "By default the theme creates a Home link in the default created menu, that when clicked leads back to your blog's homepage. If however, you are using a static homepage and have already created a page called Home to use, this will result in a duplicate link. In this case you should disable this feature to remove the link.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name"    => esc_html__( "Sort Pages Links", $themename ),
		"id"      => $shortname . "_sort_pages",
		"type"    => "select",
		"std"     => "post_title",
		"desc"    => esc_html__( "Here you can choose to sort your pages links.", $themename ),
		"options" => array(
			"post_title",
			"menu_order",
			"post_date",
			"post_modified",
			"ID",
			"post_author",
			"post_name",
		),
	),

	array(
		"name"    => esc_html__( "Order Pages Links by Ascending/Descending", $themename ),
		"id"      => $shortname . "_order_page",
		"type"    => "select",
		"std"     => "asc",
		"desc"    => esc_html__( "Here you can choose to reverse the order that your pages links are displayed. You can choose between ascending and descending.", $themename ),
		"options" => array(
			"asc",
			"desc",
		),
	),

	array(
		"name"            => esc_html__( "Number of dropdown tiers shown", $themename ),
		"id"              => $shortname . "_tiers_shown_pages",
		"type"            => "text",
		"std"             => "3",
		"desc"            => esc_html__( "This option allows you to control how many tiers your pages dropdown menu has in the default theme created main menu. Increasing the number allows for additional menu items to be shown. This setting is not applicable If you are using a custom menu and not the default theme created main menu.", $themename ),
		"validation_type" => "number",
	),

	array( "type" => "clearfix"),


	array(
		"name" => "navigation-1",
		"type" => "subcontent-end",
	),

	array(
		"name" => "navigation-2",
		"type" => "subcontent-start",
	),

	array(
		"name"    => esc_html__( "Exclude categories from the navigation bar", $themename ),
		"id"      => $shortname . "_menucats",
		"type"    => "checkboxes",
		"std"     => "",
		"desc"    => esc_html__( "Here you can choose to remove certain categories from the navigation menu. All categories marked with an X will not appear in your navigation bar.", $themename ),
		"usefor"  => "categories",
		"options" => $cats_ids,
	),

	array(
		"name" => esc_html__( "Show dropdown menus", $themename ),
		"id"   => $shortname . "_enable_dropdowns_categories",
		"type" => "checkbox",
		"std"  => "on",
		"desc" => esc_html__( "If you would like to remove the dropdown menus from the categories navigation bar disable this feature.", $themename ),
	),

	array(
		"name" => esc_html__( "Hide empty categories", $themename ),
		"id"   => $shortname . "_categories_empty",
		"type" => "checkbox",
		"std"  => "on",
		"desc" => esc_html__( "If you would like categories to be displayed in your navigationbar that don't have any posts in them then disable this option. By default empty categories are hidden", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name"            => esc_html__( "Number of dropdown tiers shown", $themename ),
		"id"              => $shortname . "_tiers_shown_categories",
		"type"            => "text",
		"std"             => "3",
		"desc"            => esc_html__( "This options allows you to control how many teirs your pages dropdown menu has. Increasing the number allows for additional menu items to be shown.", $themename ),
		"validation_type" => "number",
	),

	array( "type" => "clearfix"),

	array(
		"name"    => esc_html__( "Sort Categories Links by Name/ID/Slug/Count/Term Group", $themename ),
		"id"      => $shortname . "_sort_cat",
		"type"    => "select",
		"std"     => "name",
		"desc"    => esc_html__( "By default pages are sorted by name. However if you would rather have them sorted by ID you can adjust this setting.", $themename ),
		"options" => array(
			"name",
			"ID",
			"slug",
			"count",
			"term_group",
		),
	),

	array(
		"name"    => esc_html__( "Order Category Links by Ascending/Descending", $themename ),
		"id"      => $shortname . "_order_cat",
		"type"    => "select",
		"std"     => "asc",
		"desc"    => esc_html__( "Here you can choose to reverse the order that your categories links are displayed. You can choose between ascending and descending.", $themename ),
		"options" => array(
			"asc",
			"desc",
		),
	),

	array(
		"name" => "navigation-2",
		"type" => "subcontent-end",
	),

	array(
		"name" => "navigation-3",
		"type" => "subcontent-start",
	),

	array(
		"name" => esc_html__( "Disable top tier dropdown menu links", $themename ),
		"id"   => $shortname . "_disable_toptier",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "In some cases users will want to create parent categories or links as placeholders to hold a list of child links or categories. In this case it is not desirable to have the parent links lead anywhere, but instead merely serve an organizational function. Enabling this options will remove the links from all parent pages/categories so that they don't lead anywhere when clicked.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name" => "navigation-3",
		"type" => "subcontent-end",
	),

	array(
		"name" => "wrap-navigation",
		"type" => "contenttab-wrapend",
	),

			//-------------------------------------------------------------------------------------//

	array(
		"name" => "wrap-layout",
		"type" => "contenttab-wrapstart",
	),

	array( "type" => "subnavtab-start"),

	array(
		"name" => "layout-1",
		"type" => "subnav-tab",
		"desc" => esc_html__( "Single Post Layout", $themename ),
	),

	array(
		"name" => "layout-2",
		"type" => "subnav-tab",
		"desc" => esc_html__( "Single Page Layout", $themename ),
	),

	array(
		"name" => "layout-3",
		"type" => "subnav-tab",
		"desc" => esc_html__( "General Settings", $themename ),
	),

	array( "type" => "subnavtab-end"),

	array(
		"name" => "layout-1",
		"type" => "subcontent-start",
	),

	array(
		"name"    => esc_html__( "Choose which items to display in the postinfo section", $themename ),
		"id"      => $shortname . "_postinfo2",
		"type"    => "different_checkboxes",
		"std"     => array(
			"author",
			"date",
			"categories",
			"comments",
			"rating_stars",
		),
		"desc"    => esc_html__( "Here you can choose which items appear in the postinfo section on single post pages. This is the area, usually below the post title, which displays basic information about your post. The highlighted itmes shown below will appear.", $themename ),
		"options" => array(
			"author",
			"date",
			"categories",
			"comments",
			"rating_stars",
		),
	),

	array(
		"name" => esc_html__( "Show comments on posts", $themename ),
		"id"   => $shortname . "_show_postcomments",
		"type" => "checkbox",
		"std"  => "on",
		"desc" => esc_html__( "You can disable this option if you want to remove the comments and comment form from single post pages.", $themename ),
	),

	array(
		'name' => esc_html__( 'Show author box', $themename ),
		'id'   => "{$shortname}_show_author_box",
		'type' => 'checkbox',
		'std'  => 'on',
		'desc' => esc_html__( 'You can disable this option if you want to remove the author box from single post pages.', $themename ),
	),

	array(
		'name' => esc_html__( 'Show related posts', $themename ),
		'id'   => "{$shortname}_show_related_posts",
		'type' => 'checkbox',
		'std'  => 'on',
		'desc' => esc_html__( 'You can disable this option if you want to remove the related posts from single post pages.', $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name"                    => esc_html__( "Sharing Icons to Display", $themename ),
		"id"                      => $shortname . "_post_share_icons",
		"type"                    => "checkboxes",
		"value_sanitize_function" => "sanitize_text_field",
		"std"                     => array(),
		"desc"                    => esc_html__( "Here you can choose which sharing icons/options to display. All options marked with an X will not appear.", $themename ),
		"usefor"                  => "custom",
		"helper"                  => $share_networks_helper,
		"options"                 => $share_networks,
	),

	array( "type" => "clearfix"),

	array(
		"name" => "layout-1",
		"type" => "subcontent-end",
	),

	array(
		"name" => "layout-2",
		"type" => "subcontent-start",
	),

	array(
		"name" => esc_html__( "Show comments on pages", $themename ),
		"id"   => $shortname . "_show_pagescomments",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "By default comments are not placed on pages, however, if you would like to allow people to comment on your pages simply enable this option.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name" => "layout-2",
		"type" => "subcontent-end",
	),

	array(
		"name" => "layout-3",
		"type" => "subcontent-start",
	),

	array(
		"name"    => esc_html__( "Post info section", $themename ),
		"id"      => $shortname . "_postinfo1",
		"type"    => "different_checkboxes",
		"std"     => array(
			"author",
			"date",
			"categories",
			"rating_stars",
		),
		"desc"    => esc_html__( "Here you can choose which items appear in the postinfo section on archive pages. This is the area, usually below the post title, which displays basic information about your post. The highlighted itmes shown below will appear.", $themename ),
		"options" => array(
			"author",
			"date",
			"categories",
			"comments",
			"rating_stars",
		),
	),

	array( "type" => "clearfix"),

	array(
		"name"    => esc_html__( "Standard or Masonry Style for Archive Pages", $themename ),
		"id"      => "archive_list_style",
		"type"    => "select",
		"std"     => "standard",
		"desc"    => esc_html__( "Choose whether standard or masonry display style for archive pages for tags, author posts, search results.", $themename ),
		"options" => array(
			"standard" => esc_html__( 'Standard', $themename ),
			"masonry"  => esc_html__( 'Masonry', $themename ),
		),
	),

	array( "type" => "clearfix"),

	array(
		"name" => "layout-3",
		"type" => "subcontent-end",
	),

	array(
		"name" => "wrap-layout",
		"type" => "contenttab-wrapend",
	),

				//-------------------------------------------------------------------------------------//
	array(
		"name" => "wrap-seo",
		"type" => "contenttab-wrapstart",
	),

	array( "type" => "subnavtab-start"),

	array(
		"name" => "seo-1",
		"type" => "subnav-tab",
		"desc" => esc_html__( "Homepage SEO", $themename ),
	),

	array(
		"name" => "seo-2",
		"type" => "subnav-tab",
		"desc" => esc_html__( "Single Post Page SEO", $themename ),
	),

	array(
		"name" => "seo-3",
		"type" => "subnav-tab",
		"desc" => esc_html__( "Index Page SEO", $themename ),
	),

	array( "type" => "subnavtab-end"),

	array(
		"name" => "seo-1",
		"type" => "subcontent-start",
	),

	array(
		"name" => esc_html__( "Enable custom title", $themename ),
		"id"   => $shortname . "_seo_home_title",
		"type" => "checkbox",
		"std"  => "false",
		"desc" => esc_html__( "By default the theme uses a combination of your blog name and your blog description, as defined when you created your blog, to create your homepage titles. However if you want to create a custom title then simply enable this option and fill in the custom title field below.", $themename ),
	),

	array(
		"name" => esc_html__( "Enable meta description", $themename ),
		"id"   => $shortname . "_seo_home_description",
		"type" => "checkbox",
		"std"  => "false",
		"desc" => esc_html__( "By default the theme uses your blog description, as defined when you created your blog, to fill in the meta description field. If you would like to use a different description then enable this option and fill in the custom description field below.", $themename ),
	),

	array(
		"name" => esc_html__( "Enable meta keywords", $themename ),
		"id"   => $shortname . "_seo_home_keywords",
		"type" => "checkbox",
		"std"  => "false",
		"desc" => esc_html__( "By default the theme does not add keywords to your header. Most search engines don't use keywords to rank your site anymore, but some people define them anyway just in case. If you want to add meta keywords to your header then enable this option and fill in the custom keywords field below.", $themename ),
	),

	array(
		"name" => esc_html__( "Enable canonical URL's", $themename ),
		"id"   => $shortname . "_seo_home_canonical",
		"type" => "checkbox",
		"std"  => "false",
		"desc" => esc_html__( "Canonicalization helps to prevent the indexing of duplicate content by search engines, and as a result, may help avoid duplicate content penalties and pagerank degradation. Some pages may have different URLs all leading to the same place. For example domain.com, domain.com/index.html, and www.domain.com are all different URLs leading to your homepage. From a search engine's perspective these duplicate URLs, which also occur often due to custom permalinks, may be treaded individually instead of as a single destination. Defining a canonical URL tells the search engine which URL you would like to use officially. The theme bases its canonical URLs off your permalinks and the domain name defined in the settings tab of wp-admin.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name"            => esc_html__( "Homepage custom title (if enabled)", $themename ),
		"id"              => $shortname . "_seo_home_titletext",
		"type"            => "text",
		"std"             => "",
		"desc"            => esc_html__( "If you have enabled custom titles you can add your custom title here. Whatever you type here will be placed between the < title >< /title > tags in header.php", $themename ),
		"validation_type" => "nohtml",
	),

	array(
		"name"            => esc_html__( "Homepage meta description (if enabled)", $themename ),
		"id"              => $shortname . "_seo_home_descriptiontext",
		"type"            => "textarea",
		"std"             => "",
		"desc"            => esc_html__( "If you have enabled meta descriptions you can add your custom description here.", $themename ),
		"validation_type" => "nohtml",
	),

	array(
		"name"            => esc_html__( "Homepage meta keywords (if enabled)", $themename ),
		"id"              => $shortname . "_seo_home_keywordstext",
		"type"            => "text",
		"std"             => "",
		"desc"            => esc_html__( "If you have enabled meta keywords you can add your custom keywords here. Keywords should be separated by comas. For example: wordpress,themes,templates,elegant", $themename ),
		"validation_type" => "nohtml",
	),

	array(
		"name"    => esc_html__( "If custom titles are disabled, choose autogeneration method", $themename ),
		"id"      => $shortname . "_seo_home_type",
		"type"    => "select",
		"std"     => "BlogName | Blog description",
		"options" => array(
			"BlogName | Blog description",
			"Blog description | BlogName",
			"BlogName only",
		),
		"desc"    => esc_html__( "If you are not using cutsom post titles you can still have control over how your titles are generated. Here you can choose which order you would like your post title and blog name to be displayed, or you can remove the blog name from the title completely.", $themename ),
	),

	array(
		"name"            => esc_html__( "Define a character to separate BlogName and Post title", $themename ),
		"id"              => $shortname . "_seo_home_separate",
		"type"            => "text",
		"std"             => " | ",
		"desc"            => esc_html__( "Here you can change which character separates your blog title and post name when using autogenerated post titles. Common values are | or -", $themename ),
		"validation_type" => "nohtml",
	),

	array(
		"name" => "seo-1",
		"type" => "subcontent-end",
	),

	array(
		"name" => "seo-2",
		"type" => "subcontent-start",
	),

	array(
		"name" => esc_html__( "Enable custom titles", $themename ),
		"id"   => $shortname . "_seo_single_title",
		"type" => "checkbox",
		"std"  => "false",
		"desc" => esc_html__( "By default the theme creates post titles based on the title of your post and your blog name. If you would like to make your meta title different than your actual post title you can define a custom title for each post using custom fields. This option must be enabled for custom titles to work, and you must choose a custom field name for your title below.", $themename ),
	),

	array(
		"name" => esc_html__( "Enable custom description", $themename ),
		"id"   => $shortname . "_seo_single_description",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "If you would like to add a meta description to your post you can do so using custom fields. This option must be enabled for descriptions to be displayed on post pages. You can add your meta description using custom fields based off the custom field name you define below.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name" => esc_html__( "Enable custom keywords", $themename ),
		"id"   => $shortname . "_seo_single_keywords",
		"type" => "checkbox",
		"std"  => "false",
		"desc" => esc_html__( "If you would like to add meta keywords to your post you can do so using custom fields. This option must be enabled for keywords to be displayed on post pages. You can add your meta keywords using custom fields based off the custom field name you define below.", $themename ),
	),

	array(
		"name" => esc_html__( "Enable canonical URL's", $themename ),
		"id"   => $shortname . "_seo_single_canonical",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "Canonicalization helps to prevent the indexing of duplicate content by search engines, and as a result, may help avoid duplicate content penalties and pagerank degradation. Some pages may have different URL's all leading to the same place. For example domain.com, domain.com/index.html, and www.domain.com are all different URLs leading to your homepage. From a search engine's perspective these duplicate URLs, which also occur often due to custom permalinks, may be treaded individually instead of as a single destination. Defining a canonical URL tells the search engine which URL you would like to use officially. The theme bases its canonical URLs off your permalinks and the domain name defined in the settings tab of wp-admin.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name"            => esc_html__( "Custom field Name to be used for title", $themename ),
		"id"              => $shortname . "_seo_single_field_title",
		"type"            => "text",
		"std"             => "seo_title",
		"desc"            => esc_html__( "When you define your title using custom fields you should use this value for the custom field Name. The Value of your custom field should be the custom title you would like to use.", $themename ),
		"validation_type" => "nohtml",
	),

	array(
		"name"            => esc_html__( "Custom field Name to be used for description", $themename ),
		"id"              => $shortname . "_seo_single_field_description",
		"type"            => "text",
		"std"             => "seo_description",
		"desc"            => esc_html__( "When you define your meta description using custom fields you should use this value for the custom field Name. The Value of your custom field should be the custom description you would like to use.", $themename ),
		"validation_type" => "nohtml",
	),

	array(
		"name"            => esc_html__( "Custom field Name to be used for keywords", $themename ),
		"id"              => $shortname . "_seo_single_field_keywords",
		"type"            => "text",
		"std"             => "seo_keywords",
		"desc"            => esc_html__( "When you define your keywords using custom fields you should use this value for the custom field Name. The Value of your custom field should be the meta keywords you would like to use, separated by comas.", $themename ),
		"validation_type" => "nohtml",
	),

	array(
		"name"    => esc_html__( "If custom titles are disabled, choose autogeneration method", $themename ),
		"id"      => $shortname . "_seo_single_type",
		"type"    => "select",
		"std"     => "Post title | BlogName",
		"options" => array(
			"Post title | BlogName",
			"BlogName | Post title",
			"Post title only",
		),
		"desc"    => esc_html__( "If you are not using cutsom post titles you can still have control over hw your titles are generated. Here you can choose which order you would like your post title and blog name to be displayed, or you can remove the blog name from the title completely.", $themename ),
	),

	array(
		"name"            => esc_html__( "Define a character to separate BlogName and Post title", $themename ),
		"id"              => $shortname . "_seo_single_separate",
		"type"            => "text",
		"std"             => " | ",
		"desc"            => esc_html__( "Here you can change which character separates your blog title and post name when using autogenerated post titles. Common values are | or -", $themename ),
		"validation_type" => "nohtml",
	),

	array(
		"name" => "seo-2",
		"type" => "subcontent-end",
	),

	array(
		"name" => "seo-3",
		"type" => "subcontent-start",
	),

	array(
		"name" => esc_html__( "Enable canonical URL's", $themename ),
		"id"   => $shortname . "_seo_index_canonical",
		"type" => "checkbox",
		"std"  => "false",
		"desc" => esc_html__( "Canonicalization helps to prevent the indexing of duplicate content by search engines, and as a result, may help avoid duplicate content penalties and pagerank degradation. Some pages may have different URL's all leading to the same place. For example domain.com, domain.com/index.html, and www.domain.com are all different URLs leading to your homepage. From a search engine's perspective these duplicate URLs, which also occur often due to custom permalinks, may be treaded individually instead of as a single destination. Defining a canonical URL tells the search engine which URL you would like to use officially. The theme bases its canonical URLs off your permalinks and the domain name defined in the settings tab of wp-admin.", $themename ),
	),

	array(
		"name" => esc_html__( "Enable meta descriptions", $themename ),
		"id"   => $shortname . "_seo_index_description",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "Check this box if you want to display meta descriptions on category/archive pages. The description is based off the category description you choose when creating/edit your category in wp-admin.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name"    => esc_html__( "Choose title autogeneration method", $themename ),
		"id"      => $shortname . "_seo_index_type",
		"type"    => "select",
		"std"     => "Category name | BlogName",
		"options" => array(
			"Category name | BlogName",
			"BlogName | Category name",
			"Category name only",
		),
		"desc"    => esc_html__( "Here you can choose how your titles on index pages are generated. You can change which order your blog name and index title are displayed, or you can remove the blog name from the title completely.", $themename ),
	),

	array(
		"name"            => esc_html__( "Define a character to separate BlogName and Post title", $themename ),
		"id"              => $shortname . "_seo_index_separate",
		"type"            => "text",
		"std"             => " | ",
		"desc"            => esc_html__( "Here you can change which character separates your blog title and index page name when using autogenerated post titles. Common values are | or -", $themename ),
		"validation_type" => "nohtml",
	),

	array( "type" => "clearfix"),

	array(
		"name" => "seo-3",
		"type" => "subcontent-end",
	),

	array(
		"name" => "wrap-seo",
		"type" => "contenttab-wrapend",
	),

				//-------------------------------------------------------------------------------------//

	array(
		"name" => "wrap-integration",
		"type" => "contenttab-wrapstart",
	),

	array( "type" => "subnavtab-start"),

	array(
		"name" => "integration-1",
		"type" => "subnav-tab",
		"desc" => esc_html__( "Code Integration", $themename ),
	),

	array( "type" => "subnavtab-end"),

	array(
		"name" => "integration-1",
		"type" => "subcontent-start",
	),

	array(
		"name" => esc_html__( "Enable header code", $themename ),
		"id"   => $shortname . "_integrate_header_enable",
		"type" => "checkbox",
		"std"  => "on",
		"desc" => esc_html__( "Disabling this option will remove the header code below from your blog. This allows you to remove the code while saving it for later use.", $themename ),
	),

	array(
		"name" => esc_html__( "Enable body code", $themename ),
		"id"   => $shortname . "_integrate_body_enable",
		"type" => "checkbox2",
		"std"  => "on",
		"desc" => esc_html__( "Disabling this option will remove the body code below from your blog. This allows you to remove the code while saving it for later use.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name" => esc_html__( "Enable single top code", $themename ),
		"id"   => $shortname . "_integrate_singletop_enable",
		"type" => "checkbox",
		"std"  => "on",
		"desc" => esc_html__( "Disabling this option will remove the single top code below from your blog. This allows you to remove the code while saving it for later use.", $themename ),
	),

	array(
		"name" => esc_html__( "Enable single bottom code", $themename ),
		"id"   => $shortname . "_integrate_singlebottom_enable",
		"type" => "checkbox2",
		"std"  => "on",
		"desc" => esc_html__( "Disabling this option will remove the single bottom code below from your blog. This allows you to remove the code while saving it for later use.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name" => esc_html__( "Add code to the < head > of your blog", $themename ),
		"id"   => $shortname . "_integration_head",
		"type" => "textarea",
		"std"  => "",
		"desc" => esc_html__( "Any code you place here will appear in the head section of every page of your blog. This is useful when you need to add javascript or css to all pages.", $themename ),
	),

	array(
		"name" => esc_html__( "Add code to the < body > (good for tracking codes such as google analytics)", $themename ),
		"id"   => $shortname . "_integration_body",
		"type" => "textarea",
		"std"  => "",
		"desc" => esc_html__( "Any code you place here will appear in body section of all pages of your blog. This is usefull if you need to input a tracking pixel for a state counter such as Google Analytics.", $themename ),
	),

	array(
		"name" => esc_html__( "Add code to the top of your posts", $themename ),
		"id"   => $shortname . "_integration_single_top",
		"type" => "textarea",
		"std"  => "",
		"desc" => esc_html__( "Any code you place here will be placed at the top of all single posts. This is useful if you are looking to integrating things such as social bookmarking links.", $themename ),
	),

	array(
		"name" => esc_html__( "Add code to the bottom of your posts, before the comments", $themename ),
		"id"   => $shortname . "_integration_single_bottom",
		"type" => "textarea",
		"std"  => "",
		"desc" => esc_html__( "Any code you place here will be placed at the top of all single posts. This is useful if you are looking to integrating things such as social bookmarking links.", $themename ),
	),

	array(
		"name" => "integration-1",
		"type" => "subcontent-end",
	),

	array(
		"name" => "wrap-integration",
		"type" => "contenttab-wrapend",
	),

	array(
		"name" => "wrap-updates",
		"type" => "contenttab-wrapstart",
	),

	array(
		"type" => "subnavtab-start",
	),

	array(
		"name" => "updates-1",
		"type" => "subnav-tab",
		"desc" => esc_html__( "General", $themename ),
	),

	array(
		"type" => "subnavtab-end",
	),

	array(
		"name" => "updates-1",
		"type" => "subcontent-start",
	),

	array(
		'name'              => esc_html__( 'Username', $themename ),
		'id'                => 'et_automatic_updates_options_username',
		'std'               => '',
		'type'              => 'password',
		'validation_type'   => 'nohtml',
		'desc'              => et_get_safe_localization( __( 'Before you can receive product updates, you must first authenticate your Elegant Themes subscription. To do this, you need to enter both your Elegant Themes Username and your Elegant Themes API Key. Your username is the same username you use when logging in to <a href="http://elegantthemes.com/" target="_blank">ElegantThemes.com</a>', $themename ) ),
		'is_global'         => true,
		'main_setting_name' => 'et_automatic_updates_options',
		'sub_setting_name'  => 'username',
	),

	array(
		'name'            => esc_html__( 'API Key', $themename ),
		'id'              => 'et_automatic_updates_options_api_key',
		'std'             => '',
		'type'            => 'password',
		'validation_type' => 'nohtml',
		'desc'            => et_get_safe_localization( __( 'Before you can receive product updates, you must first authenticate your Elegant Themes subscription. To do this, you need to enter both your Elegant Themes Username and your Elegant Themes API Key. To locate your API Key, <a href="https://www.elegantthemes.com/members-area/" target="_blank">log in</a> to your Elegant Themes account and navigate to the <strong>Account > API Key</strong> page.', $themename ) ),
		'is_global'         => true,
		'main_setting_name' => 'et_automatic_updates_options',
		'sub_setting_name'  => 'api_key',
	),

	et_extra_version_rollback()->get_epanel_option(),

	array(
		"name" => "updates-1",
		"type" => "subcontent-end",
	),

	array(
		"name" => "wrap-updates",
		"type" => "contenttab-wrapend",
	),

				//-------------------------------------------------------------------------------------//

	array(
		"name" => "wrap-advertisements",
		"type" => "contenttab-wrapstart",
	),

	array( "type" => "subnavtab-start"),

	array(
		"name" => "advertisements-header",
		"type" => "subnav-tab",
		"desc" => esc_html__( "Header", $themename ),
	),

	array(
		"name" => "advertisements-header-below",
		"type" => "subnav-tab",
		"desc" => esc_html__( "Below Header", $themename ),
	),

	array(
		"name" => "advertisements-footer-above",
		"type" => "subnav-tab",
		"desc" => esc_html__( "Footer", $themename ),
	),

	array(
		"name" => "advertisements-post-above",
		"type" => "subnav-tab",
		"desc" => esc_html__( "Above Post", $themename ),
	),

	array(
		"name" => "advertisements-post-below",
		"type" => "subnav-tab",
		"desc" => esc_html__( "Below Post", $themename ),
	),

	array( "type" => "subnavtab-end"),

	array(
		"name" => "advertisements-header",
		"type" => "subcontent-start",
	),

	array(
		"name" => esc_html__( "Enable Header Ad", $themename ),
		"id"   => "header_ad_enable",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "Enabling this option will display an ad in the header. If enabled you must fill in the banner image and destination url below.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name"            => esc_html__( "Input advertisement image", $themename ),
		"id"              => "header_ad_image",
		"type"            => "text",
		"std"             => "",
		"desc"            => esc_html__( "Here you can provide image url", $themename ),
		"validation_type" => "url",
	),

	array(
		"name"            => esc_html__( "Input advertisement destination url", $themename ),
		"id"              => "header_ad_url",
		"type"            => "text",
		"std"             => "",
		"desc"            => esc_html__( "Here you can provide destination url", $themename ),
		"validation_type" => "url",
	),

	array(
		"name" => esc_html__( "Input adsense code", $themename ),
		"id"   => "header_ad_adsense",
		"type" => "textarea",
		"std"  => "",
		"desc" => esc_html__( "Place your adsense code here.", $themename ),
	),

	array(
		"name" => esc_html__( "Enable Responsive Adsense Ad", $themename ),
		"id"   => "header_responsive_adsense_ad_enable",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "Adsense's responsive ad units require the ads' container to have a fixed width. Enabling this option applies a fixed width to this ad's container.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name" => "advertisements-header",
		"type" => "subcontent-end",
	),

	array(
		"name" => "advertisements-header-below",
		"type" => "subcontent-start",
	),

	array(
		"name" => esc_html__( "Enable Below Header - Ad", $themename ),
		"id"   => "header_below_ad_enable",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "Enabling this option will display an ad below the header. If enabled you must fill in the banner image and destination url below.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name"            => esc_html__( "Input advertisement image", $themename ),
		"id"              => "header_below_ad_image",
		"type"            => "text",
		"std"             => "",
		"desc"            => esc_html__( "Here you can provide image url", $themename ),
		"validation_type" => "url",
	),

	array(
		"name"            => esc_html__( "Input advertisement destination url", $themename ),
		"id"              => "header_below_ad_url",
		"type"            => "text",
		"std"             => "",
		"desc"            => esc_html__( "Here you can provide destination url", $themename ),
		"validation_type" => "url",
	),

	array(
		"name" => esc_html__( "Input adsense code", $themename ),
		"id"   => "header_below_ad_adsense",
		"type" => "textarea",
		"std"  => "",
		"desc" => esc_html__( "Place your adsense code here.", $themename ),
	),

	array(
		"name" => esc_html__( "Enable Responsive Adsense Ad", $themename ),
		"id"   => "header_below_responsive_adsense_ad_enable",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "Adsense's responsive ad units require the ads' container to have a fixed width. Enabling this option applies a fixed width to this ad's container.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name" => "advertisements-header-below",
		"type" => "subcontent-end",
	),

	array(
		"name" => "advertisements-footer-above",
		"type" => "subcontent-start",
	),

	array(
		"name" => esc_html__( "Enable Above Footer - Ad", $themename ),
		"id"   => "footer_above_ad_enable",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "Enabling this option will display an ad above the footer. If enabled you must fill in the banner image and destination url below.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name"            => esc_html__( "Input advertisement image", $themename ),
		"id"              => "footer_above_ad_image",
		"type"            => "text",
		"std"             => "",
		"desc"            => esc_html__( "Here you can provide image url", $themename ),
		"validation_type" => "url",
	),

	array(
		"name"            => esc_html__( "Input advertisement destination url", $themename ),
		"id"              => "footer_above_ad_url",
		"type"            => "text",
		"std"             => "",
		"desc"            => esc_html__( "Here you can provide destination url", $themename ),
		"validation_type" => "url",
	),

	array(
		"name" => esc_html__( "Input adsense code", $themename ),
		"id"   => "footer_above_ad_adsense",
		"type" => "textarea",
		"std"  => "",
		"desc" => esc_html__( "Place your adsense code here.", $themename ),
	),

	array(
		"name" => esc_html__( "Enable Responsive Adsense Ad", $themename ),
		"id"   => "footer_above_responsive_adsense_ad_enable",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "Adsense's responsive ad units require the ads' container to have a fixed width. Enabling this option applies a fixed width to this ad's container.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name" => "advertisements-footer-above",
		"type" => "subcontent-end",
	),

	array(
		"name" => "advertisements-post-below",
		"type" => "subcontent-start",
	),

	array(
		"name" => esc_html__( "Enable Single Post - Below Post - Ad", $themename ),
		"id"   => "post_below_ad_enable",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "Enabling this option will display an ad on the bottom of your post pages below the single post content. If enabled you must fill in the banner image and destination url below.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name"            => esc_html__( "Input advertisement image", $themename ),
		"id"              => "post_below_ad_image",
		"type"            => "text",
		"std"             => "",
		"desc"            => esc_html__( "Here you can provide image url", $themename ),
		"validation_type" => "url",
	),

	array(
		"name"            => esc_html__( "Input advertisement destination url", $themename ),
		"id"              => "post_below_ad_url",
		"type"            => "text",
		"std"             => "",
		"desc"            => esc_html__( "Here you can provide destination url", $themename ),
		"validation_type" => "url",
	),

	array(
		"name" => esc_html__( "Input adsense code", $themename ),
		"id"   => "post_below_ad_adsense",
		"type" => "textarea",
		"std"  => "",
		"desc" => esc_html__( "Place your adsense code here.", $themename ),
	),

	array(
		"name" => esc_html__( "Enable Responsive Adsense Ad", $themename ),
		"id"   => "post_below_responsive_adsense_ad_enable",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "Adsense's responsive ad units require the ads' container to have a fixed width. Enabling this option applies a fixed width to this ad's container.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name" => "advertisements-post-below",
		"type" => "subcontent-end",
	),

	array(
		"name" => "advertisements-post-above",
		"type" => "subcontent-start",
	),

	array(
		"name" => esc_html__( "Enable Single Post - Above Post - Ad", $themename ),
		"id"   => "post_above_ad_enable",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "Enabling this option will display an ad on the top of your post pages above the single post content. If enabled you must fill in the banner image and destination url below.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name"            => esc_html__( "Input advertisement image", $themename ),
		"id"              => "post_above_ad_image",
		"type"            => "text",
		"std"             => "",
		"desc"            => esc_html__( "Here you can provide image url", $themename ),
		"validation_type" => "url",
	),

	array(
		"name"            => esc_html__( "Input advertisement destination url", $themename ),
		"id"              => "post_above_ad_url",
		"type"            => "text",
		"std"             => "",
		"desc"            => esc_html__( "Here you can provide destination url", $themename ),
		"validation_type" => "url",
	),

	array(
		"name" => esc_html__( "Input adsense code", $themename ),
		"id"   => "post_above_ad_adsense",
		"type" => "textarea",
		"std"  => "",
		"desc" => esc_html__( "Place your adsense code here.", $themename ),
	),

	array(
		"name" => esc_html__( "Enable Responsive Adsense Ad", $themename ),
		"id"   => "post_above_responsive_adsense_ad_enable",
		"type" => "checkbox2",
		"std"  => "false",
		"desc" => esc_html__( "Adsense's responsive ad units require the ads' container to have a fixed width. Enabling this option applies a fixed width to this ad's container.", $themename ),
	),

	array( "type" => "clearfix"),

	array(
		"name" => "advertisements-post-above",
		"type" => "subcontent-end",
	),

	array(
		"name" => "wrap-advertisements",
		"type" => "contenttab-wrapend",
	),

				//-------------------------------------------------------------------------------------//

);
