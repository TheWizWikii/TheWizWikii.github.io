<?php

function et_fb_shortcode_tags() {
	global $shortcode_tags;

	$shortcode_tag_names = array();
	foreach ( $shortcode_tags as $shortcode_tag_name => $shortcode_tag_cb ) {
		$shortcode_tag_names[] = $shortcode_tag_name;
	}
	return implode( '|', $shortcode_tag_names );
}

/**
 * Prepare Library Categories or Tags List.
 *
 * @param string $taxonomy Name of the taxonomy.
 *
 * @return array Clean Categories/Tags array.
 **/
function et_fb_prepare_library_terms( $taxonomy = 'layout_category' ) {
	$raw_terms_array   = apply_filters( 'et_pb_new_layout_cats_array', get_terms( $taxonomy, array( 'hide_empty' => false ) ) );
	$clean_terms_array = array();

	if ( is_array( $raw_terms_array ) && ! empty( $raw_terms_array ) ) {
		foreach ( $raw_terms_array as $term ) {
			$clean_terms_array[] = array(
				'name'  => html_entity_decode( $term->name ),
				'id'    => $term->term_id,
				'slug'  => $term->slug,
				'count' => $term->count,
			);
		}
	}

	return $clean_terms_array;
}

function et_fb_get_layout_type( $post_id ) {
	return et_fb_get_layout_term_slug( $post_id, 'layout_type' );
}

function et_fb_get_layout_term_slug( $post_id, $term_name ) {
	$post_terms = wp_get_post_terms( $post_id, $term_name );

	if ( empty( $post_terms[0] ) ) {
		return '';
	}

	$slug = $post_terms[0]->slug;

	return $slug;
}

function et_fb_comments_template() {
	return ET_BUILDER_DIR . 'comments_template.php';
}

function et_fb_modify_comments_request( $params ) {
	// modify the request parameters the way it doesn't change the result just to make request with unique parameters
	$params->query_vars['type__not_in'] = 'et_pb_comments_random_type_9999';
}

function et_fb_comments_submit_button( $submit_button ) {
		return sprintf(
			'<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
			esc_attr( 'submit' ),
			esc_attr( 'et_pb_submit' ),
			esc_attr( 'submit et_pb_button' ),
			esc_html_x( 'Submit Comment', 'et_builder' )
		);
}

/**
 * Generate custom comments number for Comments Module preview in Theme Builder.
 *
 * @return string
 */
function et_builder_set_comments_number() {
	return '12';
}

/**
 * Generate Dummy comment for Comments Module preview in Theme Builder.
 *
 * @return WP_Comment[]
 */
function et_builder_add_fake_comments() {
	return array(
		new WP_Comment(
			(object) array(
				'comment_author'   => 'Jane Doe',
				'comment_date'     => '2019-01-01 12:00:00',
				'comment_content'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus pulvinar nulla eu purus pharetra mollis. Nullam fringilla, ligula sit amet placerat rhoncus, arcu dui hendrerit ligula, ac rutrum mi neque quis orci. Morbi at tortor non eros feugiat commodo.',
				'comment_approved' => '1',
			)
		),
	);
}

/**
 * Append all default comment fields such as Author, Email, Website to Comment field for Comments Module preview in Theme Builder.
 *
 * @see comment_form() in /wp-includes/comment-template.php
 *
 * @return string
 */
function et_builder_set_comment_fields( $field ) {
	$req       = get_option( 'require_name_email' );
	$commenter = wp_get_current_commenter();
	$html_req  = $req ? " required='required'" : '';

	$author = sprintf(
		'<p class="comment-form-author"><label for="author">%1$s%2$s</label><input id="author" name="author" type="text" value="%3$s" size="30" maxlength="245"%4$s /></p>',
		esc_html__( 'Name', 'et_builder' ),
		$req ? ' <span class="required">*</span>' : '',
		esc_attr( $commenter['comment_author'] ),
		et_core_intentionally_unescaped( $html_req, 'fixed_string' )
	);

	$email = sprintf(
		'<p class="comment-form-email"><label for="email">%1$s%2$s</label><input id="email" name="email" type="email" value="%3$s" size="30" maxlength="100" aria-describedby="email-notes"%4$s /></p>',
		esc_html__( 'Email', 'et_builder' ),
		$req ? ' <span class="required">*</span>' : '',
		esc_attr( $commenter['comment_author_email'] ),
		et_core_intentionally_unescaped( $html_req, 'fixed_string' )
	);

	$url = sprintf(
		'<p class="comment-form-url"><label for="url">%1$s</label><input id="url" name="url" type="url" value="%2$s" size="30" maxlength="200" /></p>',
		esc_html__( 'Website', 'et_builder' ),
		esc_attr( $commenter['comment_author_url'] )
	);

	return $field . $author . $email . $url;
}

// comments template cannot be generated via AJAX so prepare it beforehand
function et_fb_get_comments_markup() {
	global $post;

	$post_type = isset( $post->post_type ) ? $post->post_type : false;

	// Modify the Comments content for the Comment Module preview in TB.
	if ( et_theme_builder_is_layout_post_type( $post_type ) ) {
		add_filter( 'comments_open', '__return_true' );
		add_filter( 'comment_form_field_comment', 'et_builder_set_comment_fields' );
		add_filter( 'get_comments_number', 'et_builder_set_comments_number' );
		add_filter( 'comments_array', 'et_builder_add_fake_comments' );
	}

	// Modify the comments request to make sure it's unique.
	// Otherwise WP generates SQL error and doesn't allow multiple comments sections on single page
	add_action( 'pre_get_comments', 'et_fb_modify_comments_request', 1 );

	// include custom comments_template to display the comment section with Divi style
	add_filter( 'comments_template', 'et_fb_comments_template' );

	// Modify submit button to be advanced button style ready
	add_filter( 'comment_form_submit_button', 'et_fb_comments_submit_button' );

	// Custom action before calling comments_template.
	do_action( 'et_fb_before_comments_template' );

	ob_start();
	comments_template( '', true );
	$comments_content = ob_get_contents();
	ob_end_clean();

	// Custom action after calling comments_template.
	do_action( 'et_fb_after_comments_template' );

	// remove all the actions and filters to not break the default comments section from theme
	remove_filter( 'comments_template', 'et_fb_comments_template' );
	remove_action( 'pre_get_comments', 'et_fb_modify_comments_request', 1 );

	return $comments_content;
}

// List of shortcode wrappers that requires adjustment in VB. Plugins which uses fullscreen dimension
// tend to apply negative positioning which looks inappropriate on VB's shortcode mechanism
function et_fb_known_shortcode_wrappers() {
	return apply_filters(
		'et_fb_known_shortcode_wrappers',
		array(
			'removeLeft' => array(
				'.fullscreen-container', // revolution slider.
				'.esg-container-fullscreen-forcer', // essential grid.
				'.ls-wp-fullwidth-helper', // layer slider.
			),
		)
	);
}

function et_builder_autosave_interval() {
	return apply_filters( 'et_builder_autosave_interval', et_builder_heartbeat_interval() / 2 );
}

/**
 * Callback function for heartbeat settings.
 *
 * @param array $settings Hearbeat settings.
 *
 * @return array Heartbeat settings.
 **/
function et_fb_heartbeat_settings( $settings ) {
	$settings['suspension'] = 'disable';
	$settings['interval']   = et_builder_heartbeat_interval();
	return $settings;
}
add_filter( 'heartbeat_settings', 'et_fb_heartbeat_settings', 11 );

// This function is used to add dynamic helpers whose content changes frequently
// because depending on the current post or options that can be edited by the user.
function et_fb_get_dynamic_backend_helpers() {
	global $post;

	$layout_type      = '';
	$layout_scope     = '';
	$layout_location  = '';
	$layout_built_for = '';
	$remote_item_id   = '';

	// Override $post data if current visual builder is rendering layout block; This is needed
	// because block editor might be used in CPT that has no frontend such as reusable block's
	// `wp_block` CPT so layout block preview needs to be rendered using latest / other post
	// frontend. To correctly render and update the layout, adjust post ID and other data accordingly
	$is_layout_block_preview = ET_GB_Block_Layout::is_layout_block_preview();

	if ( $is_layout_block_preview && isset( $_GET['et_post_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- No need to use nonce.
		$et_post_id = (int) $_GET['et_post_id'];

		$post = get_post( $et_post_id );
	}

	$current_user         = wp_get_current_user();
	$post_type            = isset( $post->post_type ) ? $post->post_type : false;
	$post_id              = isset( $post->ID ) ? $post->ID : false;
	$post_status          = isset( $post->post_status ) ? $post->post_status : false;
	$post_title           = isset( $post->post_title ) ? esc_attr( $post->post_title ) : false;
	$post_thumbnail_alt   = has_post_thumbnail() ? get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) : false;
	$post_thumbnail_title = has_post_thumbnail() && is_object( get_post_thumbnail_id() ) && is_object( get_post( get_post_thumbnail_id() ) ) && ! is_home()
		? get_post( get_post_thumbnail_id()->post_title )
		: false;

	$request_type  = $post_type;
	$user_cloud_id = 0;

	// Set request_type on 404 pages.
	if ( is_404() ) {
		$request_type = '404';
	}

	// Set request_type on Archive pages.
	if ( is_archive() ) {
		$request_type = 'archive';
	}

	// Set request_type on the homepage.
	if ( is_home() ) {
		$request_type = 'home';
	}

	if ( 'et_pb_layout' === $post_type ) {
		$layout_type      = et_fb_get_layout_type( $post_id );
		$layout_scope     = et_fb_get_layout_term_slug( $post_id, 'scope' );
		$layout_location  = 'local';
		$layout_built_for = get_post_meta( $post_id, '_et_pb_built_for_post_type', 'page' );

		// Only set the remote_item_id if temp post still exists.
		if ( ! empty( $_GET['cloudItem'] ) && get_post_status( $post_id ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
			$remote_item_id  = (int) sanitize_text_field( $_GET['cloudItem'] ); // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
			$layout_location = 'cloud';

			$user_cloud_id = isset( $_GET['userCloudId'] ) ? sanitize_text_field( $_GET['userCloudId'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		}
	}

	$host        = isset( $_SERVER['HTTP_HOST'] ) ? esc_url( $_SERVER['HTTP_HOST'] ) : '';
	$request_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url( $_SERVER['REQUEST_URI'] ) : '';
	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $host . $request_uri;

	// disable product tour on the app launch, so it won't be started next time.
	if ( et_builder_is_product_tour_enabled() ) {
		et_fb_disable_product_tour();
	}

	$theme_builder_layouts = et_theme_builder_get_template_layouts();

	// In some cases when page created using Polylang
	// it may have predefined content, so inital content is not empty.
	$has_predefined_content = isset( $_GET['from_post'] ) && 'empty' !== $_GET['from_post'] ? 'yes' : 'no';

	// Validate the Theme Builder body layout and its post content module, if any.
	$has_tb_layouts           = ! empty( $theme_builder_layouts );
	$is_tb_layout             = et_theme_builder_is_layout_post_type( $post_type );
	$tb_body_layout           = et_()->array_get( $theme_builder_layouts, ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE, array() );
	$tb_body_has_post_content = $tb_body_layout && et_theme_builder_layout_has_post_content( $tb_body_layout );
	$is_bfb                   = ! empty( $_GET['et_fb'] ) && ! empty( $_GET['et_bfb'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- value is not used
	$has_valid_body_layout    = ! $has_tb_layouts || $is_tb_layout || $tb_body_has_post_content || $is_bfb;

	// If page is not singular and uses theme builder, set $post_status to 'publish'
	// to get the 'Save' button instead of 'Draft' and 'Publish'.
	if ( ! is_singular() && et_fb_is_theme_builder_used_on_page() && et_pb_is_allowed( 'theme_builder' ) ) {
		$post_status = 'publish';
	}

	$all_subjects_raw = get_post_meta( $post_id, '_et_pb_ab_subjects', true );

	$home_url = wp_parse_url( get_site_url() );

	$library_capability   = et_core_portability_cap( 'et_builder_layouts' );
	$local_import_support = current_user_can( $library_capability );

	$helpers = array(
		'site_url'                     => get_site_url(),
		'site_domain'                  => isset( $home_url['host'] ) ? untrailingslashit( $home_url['host'] ) : '/',
		'locale'                       => get_user_locale(),
		'domainToken'                  => get_option( 'et_server_domain_token', '' ),
		'debug'                        => defined( 'ET_DEBUG' ) && ET_DEBUG,
		'postId'                       => $post_id,
		'postTitle'                    => $post_title,
		'postStatus'                   => $post_status,
		'postType'                     => $post_type,
		'postMeta'                     => $post,
		'postThumbnailAlt'             => $post_thumbnail_alt,
		'postThumbnailTitle'           => $post_thumbnail_title,
		'requestType'                  => $request_type,
		'isCustomPostType'             => et_builder_is_post_type_custom( $post_type ) ? 'yes' : 'no',
		'layoutType'                   => $layout_type,
		'layoutScope'                  => $layout_scope,
		'layoutLocation'               => $layout_location,
		'layoutBuiltFor'               => $layout_built_for,
		'hasPredefinedContent'         => $has_predefined_content,
		'remoteItemId'                 => $remote_item_id,
		'userCloudId'                  => $user_cloud_id,
		'publishCapability'            => ( is_page() && ! current_user_can( 'publish_pages' ) ) || ( ! is_page() && ! current_user_can( 'publish_posts' ) ) ? 'no_publish' : 'publish',
		'ajaxUrl'                      => is_ssl() ? admin_url( 'admin-ajax.php' ) : admin_url( 'admin-ajax.php', 'http' ),
		'et_account'                   => et_core_get_et_account(),
		'productTourStatus'            => et_builder_is_product_tour_enabled() ? 'on' : 'off',
		'localLibraryImportSupport'    => $local_import_support ? 'yes' : 'no',
		'gutterWidth'                  => (string) et_get_option( 'gutter_width', '3' ),
		'sectionPadding'               => et_get_option( 'section_padding', 4 ),
		'cookie_path'                  => SITECOOKIEPATH,
		'etBuilderAccentColor'         => et_builder_accent_color(),
		'gmt_offset_string'            => et_pb_get_gmt_offset_string(),
		'currentUserDisplayName'       => $current_user->display_name,
		'currentRole'                  => et_pb_get_current_user_role(),
		'currentUserCapabilities'      => array(
			'manageOptions'    => current_user_can( 'manage_options' ),
			'manageCategories' => current_user_can( 'manage_categories' ),
		),
		'exportUrl'                    => et_fb_get_portability_export_url(),
		'nonces'                       => et_fb_get_nonces(),
		'currentPage'                  => et_fb_current_page_params(),
		'currentTheme'                 => et_core_get_theme_info( 'Name' ),
		'appPreferences'               => et_fb_app_preferences(),
		'pageSettingsFields'           => ET_Builder_Settings::get_fields(),
		'pageSettingsValues'           => ET_Builder_Settings::get_settings_values(),
		'abTestingSubjects'            => false !== $all_subjects_raw ? explode( ',', $all_subjects_raw ) : array(),
		'productTourText'              => et_fb_get_product_tour_text( $post_id ),
		'show_page_creation'           => $is_layout_block_preview ? '' : get_post_meta( $post_id, '_et_pb_show_page_creation', true ),
		'mediaButtons'                 => et_builder_get_media_buttons(),
		'shortcode_tags'               => et_fb_shortcode_tags(),
		'customizer'                   => array(
			'tablet'       => array(
				'sectionHeight' => et_get_option( 'tablet_section_height' ),
			),
			'phone'        => array(
				'sectionHeight' => et_get_option( 'phone_section_height' ),
			),
			'fonts'        => array(
				'heading' => et_get_option( 'heading_font', '' ),
				'body'    => et_get_option( 'body_font', '' ),
			),
			'font_weights' => array(
				'heading' => et_get_option( 'heading_font_weight', '500' ),
				'body'    => et_get_option( 'body_font_weight', '500' ),
			),
		),
		'abTesting'                    => is_object( $post ) ? et_builder_ab_options( $post->ID ) : false,
		'conditionalTags'              => et_fb_conditional_tag_params(),
		'commentsModuleMarkup'         => et_fb_get_comments_markup(),
		'failureNotification'          => et_builder_get_failure_notification_modal(),
		'noBrowserSupportNotification' => et_builder_get_no_browser_notification_modal(),
		/**
		 * Filters taxonomies array.
		 *
		 * @param array Array of all registered taxonomies.
		 */
		'getTaxonomies'                => apply_filters( 'et_fb_taxonomies', et_fb_get_taxonomy_terms() ),

		/**
		 * Filters taxonomy labels.
		 *
		 * @param array Array of labels for all registered taxonomies.
		 */
		'getTaxonomyLabels'            => apply_filters( 'et_fb_taxonomy_labels', et_fb_get_taxonomy_labels() ),
		'urls'                         => array(
			'loginFormUrl'        => esc_url( site_url( 'wp-login.php', 'login_post' ) ),
			'forgotPasswordUrl'   => esc_url( wp_lostpassword_url() ),
			'logoutUrl'           => esc_url( wp_logout_url() ),
			'logoutUrlRedirect'   => esc_url( wp_logout_url( $current_url ) ),
			'themeOptionsUrl'     => esc_url( et_pb_get_options_page_link() ),
			'builderPreviewStyle' => ET_BUILDER_URI . '/styles/preview.css',
			'themeCustomizerUrl'  => et_pb_is_allowed( 'theme_customizer' ) ? add_query_arg(
				array(
					'et_customizer_option_set' => 'theme',
					'url'                      => rawurlencode( $current_url ),
				),
				admin_url( 'customize.php' )
			) : false,
			'roleEditorUrl'       => current_user_can( 'manage_options' ) ? add_query_arg( array( 'page' => 'et_divi_role_editor' ), admin_url( 'admin.php' ) ) : false,
			'manageLibraryUrl'    => current_user_can( 'manage_options' ) ? add_query_arg( array( 'post_type' => 'et_pb_layout' ), admin_url( 'edit.php' ) ) : false,
			'ajaxUrl'             => is_ssl() ? admin_url( 'admin-ajax.php' ) : admin_url( 'admin-ajax.php', 'http' ),
		),
		'defaults'                     => array(
			'et_pb_countdown_timer' => array(
				'date_time' => gmdate( 'Y-m-d H:i', current_time( 'timestamp' ) + ( 30 * 86400 ) ), // next 30 days from current day
			),
		),
		'themeBuilder'                 => array(
			'isLayout'           => et_theme_builder_is_layout_post_type( $post_type ),
			'layoutPostTypes'    => et_theme_builder_get_layout_post_types(),
			'bodyLayoutPostType' => ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE,
			'postContentModules' => et_theme_builder_get_post_content_modules(),
			'hasValidBodyLayout' => $has_valid_body_layout,
			'themeBuilderAreas'  => et_theme_builder_get_template_layouts(),
		),
		'i18n'                         => array(
			'modules' => array(
				'login'       => array(
					'loginAs' => sprintf( esc_html__( 'Login as %s', 'et_builder' ), $current_user->display_name ),
				),
				'postContent' => array(
					'placeholder' => et_theme_builder_get_post_content_placeholder(),
				),
			),
			'modals'  => array(
				'pageSettings' => array(
					'title'   => ET_Builder_Settings::get_title(),
					'toggles' => ET_Builder_Settings::get_toggles(),
				),
			),
			'themeBuilder' => array(
				'editHeader'      => esc_html__( 'Edit Header Template', 'et_builder' ),
				'editFooter'      => esc_html__( 'Edit Footer Template', 'et_builder' ),
				'editBody'        => esc_html__( 'Edit Body Template', 'et_builder' ),
				'editPostContent' => esc_html__( 'Edit Post Content', 'et_builder' ),
			),
		),
		'globalPresets'                => ET_Builder_Element::get_global_presets(),
		'module_cache_filename_id'     => ET_Builder_Element::get_cache_filename_id( $post_type ),
		'registeredPostTypeOptions'    => et_get_registered_post_type_options(),
		'codeSnippets'                 => [
			'config' => [
				'api'    => admin_url( 'admin-ajax.php' ),
				'nonces' => [
					'et_code_snippets_library_get_items' => wp_create_nonce( 'et_code_snippets_library_get_items' ),
				],
			],
		],
		'aiLayout'                     => [
			'headingFont'      => et_get_option( 'et_ai_layout_heading_font', '' ),
			'bodyFont'         => et_get_option( 'et_ai_layout_body_font', '' ),
			'primaryColor'     => et_get_option( 'et_ai_layout_primary_color', '' ),
			'secondaryColor'   => et_get_option( 'et_ai_layout_secondary_color', '' ),
			'headingFontColor' => et_get_option( 'et_ai_layout_heading_font_color', '' ),
			'bodyFontColor'    => et_get_option( 'et_ai_layout_body_font_color', '' ),
		],
	);

	// `class_exists` check avoids https://github.com/elegantthemes/Divi/issues/23662 error.
	if ( class_exists( 'ET_Builder_Module_Helper_Woocommerce_Modules' ) ) {
		$helpers['wooCommerce'] = array(
			'themeBuilderCheckoutTemplatePageId' => ET_Builder_Module_Helper_Woocommerce_Modules::get_tb_template_id_by_current_page_id( $post_id ),
		);
	}

	$helpers['css'] = array(
		'wrapperPrefix'   => ET_BUILDER_CSS_WRAPPER_PREFIX,
		'containerPrefix' => ET_BUILDER_CSS_CONTAINER_PREFIX,
		'layoutPrefix'    => ET_BUILDER_CSS_LAYOUT_PREFIX,
		'prefix'          => ET_BUILDER_CSS_PREFIX,
	);

	$custom_defaults_unmigrated = et_get_option( ET_Builder_Global_Presets_Settings::CUSTOM_DEFAULTS_UNMIGRATED_OPTION, false );

	if ( $custom_defaults_unmigrated ) {
		$helpers['customDefaultsUnmigrated'] = ET_Builder_Global_Presets_Settings::migrate_custom_defaults_to_global_presets( $custom_defaults_unmigrated );
	}

	$helpers['dynamicContentFields'] = et_builder_get_dynamic_content_fields( $post_id, 'edit' );

	$helpers['aiTextFieldEmptyOptions']        = et_builder_get_ai_text_field_empty_options();
	$helpers['aiImageFieldOptions']            = et_builder_get_ai_text_field_empty_options( true );
	$helpers['aiTextFieldOptions']             = et_builder_get_ai_text_field_options();
	$helpers['aiTextOptions']                  = et_builder_get_ai_text_options();
	$helpers['aiCodeOptions']                  = et_builder_get_ai_code_options();
	$helpers['aiImageOptions']                 = et_builder_get_ai_image_options();
	$helpers['aiTextFieldSelectedTextOptions'] = et_builder_get_ai_selected_text_field_options();

	return $helpers;
}

/**
 * This function is used to add static helpers whose content changes rarely.
 * eg: google fonts, module defaults and so on.
 *
 * @param string $post_type Post type.
 * @return array
 */
function et_fb_get_static_backend_helpers( $post_type ) {
	$custom_user_fonts = et_builder_get_custom_fonts();
	$use_google_fonts  = et_core_use_google_fonts();
	$websafe_fonts     = et_builder_get_websafe_fonts();
	$google_fonts      = $websafe_fonts;
	$sticky            = et_pb_sticky_options();

	if ( $use_google_fonts ) {
		$google_fonts = array_merge( $websafe_fonts, et_builder_get_google_fonts() );
		ksort( $google_fonts );
	}

	$google_fonts = array_merge( array( 'Default' => array() ), $google_fonts );

	/**
	 * Filters modules list.
	 *
	 * @param array $modules_array.
	 */
	$fb_modules_array = apply_filters( 'et_fb_modules_array', ET_Builder_Element::get_modules_array( $post_type, true ) );

	/**
	 * Filters modules list which affect "Add New Row" button position.
	 *
	 * @param array $modules_list.
	 */
	$modules_row_overlapping_add_new = apply_filters(
		'et_fb_modules_row_overlapping_add_new',
		array(
			'et_pb_counters',
			'et_pb_post_nav',
			'et_pb_search',
			'et_pb_social_media_follow',
		)
	);

	$modules_defaults = array(
		'title'    => _x( 'Your Title Goes Here', 'Modules dummy content', 'et_builder' ),
		'subtitle' => _x( 'Subtitle goes Here', 'et_builder' ),
		'body'     => _x(
			'<p>Your content goes here. Edit or remove this text inline or in the module Content settings. You can also style every aspect of this content in the module Design settings and even apply custom CSS to this text in the module Advanced settings.</p>', // phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings -- Need to have p tag.
			'et_builder'
		),
		'number'   => 50,
		'button'   => _x( 'Click Here', 'Modules dummy content', 'et_builder' ),
		'image'    => array(
			'landscape' => ET_BUILDER_PLACEHOLDER_LANDSCAPE_IMAGE_DATA,
			'portrait'  => ET_BUILDER_PLACEHOLDER_PORTRAIT_IMAGE_DATA,
		),
		'video'    => 'https://www.youtube.com/watch?v=FkQuawiGWUw',
	);

	/**
	 * App preferences
	 */
	$app_preferences = et_fb_app_preferences_settings();

	/**
	 * ETBuilderBackend
	 *
	 * @var array $helpers
	 */
	// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned -- Invalid warning.
	// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow -- Invalid warning.
	$helpers = array(
		'blog_id'                          => get_current_blog_id(),
		'autosaveInterval'                 => et_builder_autosave_interval(),
		'shortcodeObject'                  => array(),
		'autosaveShortcodeObject'          => array(),
		'tinymcePlugins'                   => apply_filters(
			'et_fb_tinymce_plugins',
			array(
				'autolink',
				'link',
				'image',
				'lists',
				'print',
				'preview',
				'autoresize',
				'textcolor',
				'table',
				'paste',
				'fullscreen',
				'charmap',
				'emoticons',
				'wpview',
			)
		),
		'tinymceSkinUrl'                   => ET_FB_ASSETS_URI . '/vendors/tinymce-skin',
		'tinymceCSSFiles'                  => esc_url( includes_url( 'js/tinymce' ) . '/skins/wordpress/wp-content.css' ),
		'images_uri'                       => ET_BUILDER_URI . '/images',
		'optionTemplate'                   => array(
			'fieldNamePrefix' => et_pb_option_template()->template_prefix,
			'templates'       => et_pb_option_template()->templates(),
			'data'            => et_pb_option_template()->all(),
		),
		'componentDefinitions'             => array(
			'generalFields'     => array(),
			'advancedFields'    => array(),
			'customCssFields'   => array(),
			'fieldsDefaults'    => array(),
			'defaults'          => array(),
			'optionsToggles'    => array(),
			'customTabs'        => array(),
			'customLayoutsTabs' => array(),
		),
		'pageCreationOptions'              => et_builder_page_creation_options(),
		'findReplace'                      => array(
			'groups'     => array(
				'locations' => esc_html__( 'Locations', 'et_builder' ),
				'elements'  => et_builder_i18n( 'Elements' ),
			),
			'within'     => array(
				'locations' => array(
					'this_section' => esc_html__( 'This Section', 'et_builder' ),
					'this_row'     => esc_html__( 'This Row', 'et_builder' ),
					'this_column'  => esc_html__( 'This Column', 'et_builder' ),
				),
				'elements'  => array(
					// Translators: %s: element name.
					'this_module'   => esc_html__( 'This %s\'s Settings', 'et_builder' ),
					'all_modules'   => esc_html__( 'All Modules', 'et_builder' ),
					'all_rows'      => esc_html__( 'All Rows', 'et_builder' ),
					// Translators: %s: similar settings.
					'all_same_type' => esc_html__( 'All %s Settings', 'et_builder' ),
				),
			),
			'throughout' => array(
				'this_section' => esc_html__( 'This Section', 'et_builder' ),
				'this_row'     => esc_html__( 'This Row', 'et_builder' ),
				'this_column'  => esc_html__( 'This Column', 'et_builder' ),
			),
			'themeBuilderOptions' => array(
				'this_page'        => esc_html__( 'Header, Footer & Page', 'et_builder' ),
				'et_header_layout' => esc_html__( 'Header Template', 'et_builder' ),
				'et_body_layout'   => esc_html__( 'Body Template', 'et_builder' ),
				'et_footer_layout' => esc_html__( 'Footer Template', 'et_builder' ),
				'post_content'     => esc_html__( 'This Page', 'et_builder' ),
			),
			'all'        => array(
				'on' => esc_html__( 'Replace all found values within every option type, not limited to %s', 'et_builder' ),
			),
		),
		'dividers'                         => ET_Builder_Module_Fields_Factory::get( 'Divider' )->dividers,
		'moduleParentShortcodes'           => ET_Builder_Element::get_parent_slugs_regex( $post_type ),
		'moduleChildShortcodes'            => ET_Builder_Element::get_child_slugs_regex( $post_type ),
		'moduleChildSlugs'                 => ET_Builder_Element::get_child_slugs( $post_type ),
		'moduleRawContentShortcodes'       => ET_Builder_Element::get_raw_content_slugs( $post_type ),
		'modules'                          => $fb_modules_array,
		'modulesCount'                     => count( $fb_modules_array ),
		'modulesWithChildren'              => ET_Builder_Element::get_slugs_with_children( $post_type ),
		'modulesShowOnCancelDropClassname' => apply_filters(
			'et_fb_modules_show_on_cancel_drop_classname',
			array(
				'et_pb_gallery',
				'et_pb_filterable_portfolio',
			)
		),
		'modulesFeaturedImageBackground'   => ET_Builder_Element::get_featured_image_background_modules( $post_type ),
		'modulesRowOverlappingAddNew'      => $modules_row_overlapping_add_new,
		'structureModules'                 => array(
			array(
				'slug'       => 'et_pb_section',
				'name'       => esc_html__( 'Section', 'et_builder' ),
				'namePlural' => esc_html__( 'Sections', 'et_builder' ),
			),
			array(
				'slug'       => 'et_pb_row',
				'name'       => esc_html__( 'Row', 'et_builder' ),
				'namePlural' => esc_html__( 'Rows', 'et_builder' ),
			),
			array(
				'slug'       => 'et_pb_row_inner',
				'name'       => esc_html__( 'Row', 'et_builder' ),
				'namePlural' => esc_html__( 'Rows', 'et_builder' ),
			),
			array(
				'slug'       => 'et_pb_column',
				'name'       => esc_html__( 'Column', 'et_builder' ),
				'namePlural' => esc_html__( 'Columns', 'et_builder' ),
			),
			array(
				'slug'       => 'et_pb_column_inner',
				'name'       => esc_html__( 'Column', 'et_builder' ),
				'namePlural' => esc_html__( 'Columns', 'et_builder' ),
			),
		),
		'et_builder_css_media_queries'     => ET_Builder_Element::get_media_quries( 'for_js' ),
		'builderOptions'                   => et_builder_options(),
		'builderVersion'                   => ET_BUILDER_PRODUCT_VERSION,
		'noBuilderSupportNotification'     => et_builder_get_no_builder_notification_modal(),
		'exitNotification'                 => et_builder_get_exit_notification_modal(),
		'browserAutosaveNotification'      => et_builder_get_browser_autosave_notification_modal(),
		'serverAutosaveNotification'       => et_builder_get_server_autosave_notification_modal(),
		'coreModalTemplate'                => et_builder_get_core_modal_template(),
		'coreModalButtonsTemplate'         => et_builder_get_core_modal_buttons_template(),
		'unsavedNotification'              => et_builder_get_unsaved_notification_texts(),
		'globalPresetsSaveFailure'         => et_builder_get_global_presets_save_failure_texts(),
		'globalPresetsSaveForbidden'       => et_builder_get_global_presets_save_forbidden_texts(),
		'globalPresetsLoadFailure'         => et_builder_get_global_presets_load_failure_texts(),
		// Translators: %s: layout title.
		'backupLabel'                      => __( 'Backup of %s', 'et_builder' ),

		'googleAPIKey'                     => et_pb_is_allowed( 'theme_options' ) ? get_option( 'et_google_api_settings' ) : '',
		'useGoogleFonts'                   => $use_google_fonts,
		'googleFontsList'                  => array_keys( $google_fonts ),
		'googleFonts'                      => $google_fonts,
		'websafeFonts'                     => $websafe_fonts,
		'customFonts'                      => $custom_user_fonts,
		'removedFonts'                     => et_builder_old_fonts_mapping(),
		'allFontWeights'                   => et_builder_get_font_weight_list(),
		'allFontFormats'                   => et_pb_get_supported_font_formats(),
		'fontIcons'                        => et_pb_get_font_icon_symbols(),
		'fontIconsDown'                    => et_pb_get_font_down_icon_symbols(),
		'fontIconsExtended'                => et_pb_get_decoded_extended_font_icon_symbols(),
		'socialNetFaIcons'                 => et_pb_get_social_net_fa_icons(),
		'widgetAreas'                      => et_builder_get_widget_areas_list(),
		'et_builder_fonts_data'            => et_builder_get_fonts(),
		'roleSettings'                     => et_pb_get_role_settings(),
		'optionsCategoriesPermissions'     => array_keys( ET_Builder_Element::get_options_categories() ),
		'classNames'                       => array(
			'hide_on_mobile_class' => 'et-hide-mobile',
		),
		'columnLayouts'                => et_builder_get_columns(),
		'searchFilterItems'            => array(
			'show_only' => array(
				'styles_modified'   => esc_html__( 'Modified Styles', 'et_builder' ),
				'styles_responsive' => esc_html__( 'Responsive Styles/Content', 'et_builder' ),
				'styles_hover'      => esc_html__( 'Hover Styles/Content', 'et_builder' ),
				'styles_sticky'     => esc_html__( 'Sticky Styles', 'et_builder' ),
				'active_content'    => esc_html__( 'Active Content', 'et_builder' ),
			),
		),
		'searchFilterIconItems'            => array(
			'show_only' => array(
				'solid' => esc_html__( 'Solid Icons', 'et_builder' ),
				'line'  => esc_html__( 'Line Icons', 'et_builder' ),
				'divi'  => esc_html__( 'Divi Icons', 'et_builder' ),
				'fa'    => esc_html__( 'Font Awesome', 'et_builder' ),
			),
		),
		'backgroundPatternOptions'         => et_pb_background_pattern_options()->settings(),
		'backgroundMaskOptions'            => et_pb_background_mask_options()->settings(),
		'backgroundTabs'                   => array(
			'color',
			'gradient',
			'image',
			'video',
			'pattern',
			'mask',
		),
		'defaults'                     => array(
			'et_pb_accordion_item'              => array(
				'title'   => $modules_defaults['title'],
				'content' => $modules_defaults['body'],
			),
			'et_pb_audio'                       => array(
				'title'       => $modules_defaults['title'],
				'artist_name' => _x( 'Artist Name', 'Modules dummy content', 'et_builder' ),
			),
			'et_pb_counter'                     => array(
				'content' => $modules_defaults['title'],
				'percent' => $modules_defaults['number'],
			),
			'et_pb_blurb'                       => array(
				'title'   => $modules_defaults['title'],
				'content' => $modules_defaults['body'],
				'image'   => $modules_defaults['image']['landscape'],
			),
			'et_pb_button'                      => array(
				'button_text' => $modules_defaults['button'],
			),
			'et_pb_cta'                         => array(
				'title'       => $modules_defaults['title'],
				'content'     => $modules_defaults['body'],
				'button_text' => $modules_defaults['button'],
			),
			'et_pb_circle_counter'              => array(
				'title'  => $modules_defaults['title'],
				'number' => $modules_defaults['number'],
			),
			'et_pb_signup'                      => array(
				'title'       => $modules_defaults['title'],
				'description' => $modules_defaults['body'],
			),
			'et_pb_image'                       => array(
				'src' => $modules_defaults['image']['landscape'],
			),
			'et_pb_fullwidth_image'             => array(
				'src' => $modules_defaults['image']['landscape'],
			),
			'et_pb_login'                       => array(
				'title'   => $modules_defaults['title'],
				'content' => $modules_defaults['body'],
			),
			'et_pb_number_counter'              => array(
				'title'  => $modules_defaults['title'],
				'number' => $modules_defaults['number'],
			),
			'et_pb_team_member'                 => array(
				'name'      => _x( 'Name Goes Here', 'Modules dummy content', 'et_builder' ),
				'content'   => $modules_defaults['body'],
				'image_url' => $modules_defaults['image']['portrait'],
				'position'  => _x( 'Position', 'Modules dummy content', 'et_builder' ),
			),
			'et_pb_pricing_table'               => array(
				'title'    => _x( 'Table Title', 'Modules dummy content', 'et_builder' ),
				'subtitle' => $modules_defaults['subtitle'],
				'currency' => _x( '$', 'Modules dummy content', 'et_builder' ),
				'content'  => _x(
					"+ This feature is included\n+ This feature is included\n+ This feature is included\n+ This feature is included\n- This feature is not included\n- This feature is not included",
					'Modules dummy content',
					'et_builder'
				),
				'sum'      => $modules_defaults['number'],
			),
			'et_pb_slide'                       => array(
				'heading'     => $modules_defaults['title'],
				'content'     => $modules_defaults['body'],
				'button_text' => $modules_defaults['button'],
			),
			'et_pb_tab'                         => array(
				'title'   => _x( 'Tab Title', 'Modules dummy content', 'et_builder' ),
				'content' => $modules_defaults['body'],
			),
			'et_pb_testimonial'                 => array(
				'author'       => _x( 'Name Goes Here', 'Modules dummy content', 'et_builder' ),
				'portrait_url' => $modules_defaults['image']['portrait'],
				'content'      => $modules_defaults['body'],
			),
			'et_pb_text'                        => array(
				'content' => $modules_defaults['body'],
			),
			'et_pb_toggle'                      => array(
				'title'   => $modules_defaults['title'],
				'content' => $modules_defaults['body'],
			),
			'et_pb_countdown_timer'             => array(
				'title' => $modules_defaults['title'],
			),
			'et_pb_video'                       => array(
				'src' => $modules_defaults['video'],
			),
			'et_pb_video_slider_item'           => array(
				'src'                => $modules_defaults['video'],
				'__is_oembed'        => ET_Builder_Module_Video_Slider_Item::is_oembed( array( 'src' => $modules_defaults['video'] ) ),
				'__get_oembed'       => ET_Builder_Module_Video_Slider_Item::get_oembed( array( 'src' => $modules_defaults['video'] ) ),
				'__oembed_thumbnail' => ET_Builder_Module_Video_Slider_Item::get_oembed_thumbnail( array( 'src' => $modules_defaults['video'] ) ),
			),
			'et_pb_fullwidth_header'            => array(
				'title'           => $modules_defaults['title'],
				'content'         => $modules_defaults['body'],
				'button_one_text' => $modules_defaults['button'],
			),
			'et_pb_social_media_follow_network' => array(
				'social_network'   => 'facebook',
				'content'          => 'facebook',
				'background_color' => '#3b5998',
			),
			'contactFormInputs'                 => array(),
			'backgroundOptions'                 => array(
				'repeat'          => ET_Global_Settings::get_value( 'all_background_gradient_repeat' ),
				'type'            => ET_Global_Settings::get_value( 'all_background_gradient_type' ),
				'direction'       => ET_Global_Settings::get_value( 'all_background_gradient_direction' ),
				'radialDirection' => ET_Global_Settings::get_value( 'all_background_gradient_direction_radial' ),
				'stops'           => ET_Global_Settings::get_value( 'all_background_gradient_stops' ),
				'unit'            => ET_Global_Settings::get_value( 'all_background_gradient_unit' ),
				'overlaysImage'   => ET_Global_Settings::get_value( 'all_background_gradient_overlays_image' ),
				'colorStart'      => ET_Global_Settings::get_value( 'all_background_gradient_start' ),
				'startPosition'   => ET_Global_Settings::get_value( 'all_background_gradient_start_position' ),
				'colorEnd'        => ET_Global_Settings::get_value( 'all_background_gradient_end' ),
				'endPosition'     => ET_Global_Settings::get_value( 'all_background_gradient_end_position' ),
			),
			'filterOptions'                     => array(
				'hue_rotate'     => ET_Global_Settings::get_value( 'all_filter_hue_rotate' ),
				'saturate'       => ET_Global_Settings::get_value( 'all_filter_saturate' ),
				'brightness'     => ET_Global_Settings::get_value( 'all_filter_brightness' ),
				'contrast'       => ET_Global_Settings::get_value( 'all_filter_contrast' ),
				'invert'         => ET_Global_Settings::get_value( 'all_filter_invert' ),
				'sepia'          => ET_Global_Settings::get_value( 'all_filter_sepia' ),
				'opacity'        => ET_Global_Settings::get_value( 'all_filter_opacity' ),
				'blur'           => ET_Global_Settings::get_value( 'all_filter_blur' ),
				'mix_blend_mode' => ET_Global_Settings::get_value( 'all_mix_blend_mode' ),
			),
			'et_pb_heading'            => array(
				'title' => $modules_defaults['title'],
			),
		),
		'saveModuleLibraryCategories'      => et_fb_prepare_library_terms(),
		'saveModuleLibraryTags'            => et_fb_prepare_library_terms( 'layout_tag' ),
		'emailNameFieldOnlyProviders'  => array_keys( ET_Builder_Module_Signup::providers()->names_by_slug( 'all', 'name_field_only' ) ),
		'emailPredefinedCustomFields'  => ET_Core_API_Email_Providers::instance()->custom_fields_data(),
		'emailCustomFieldProviders'    => array_keys( ET_Builder_Module_Signup::providers()->names_by_slug( 'all', 'custom_fields' ) ),
		'columnSettingFields'          => array(
			'general'  => array(
				'bg_img_%s'                              => ET_Builder_Element::background_field_template(
					'image',
					array(
						'label'              => esc_html__( 'Column %s Background Image', 'et_builder' ),
						'type'               => 'upload',
						'option_category'    => 'basic_option',
						'upload_button_text' => et_builder_i18n( 'Upload an image' ),
						'choose_text'        => esc_attr__( 'Choose a Background Image', 'et_builder' ),
						'update_text'        => esc_attr__( 'Set As Background', 'et_builder' ),
						'description'        => esc_html__( 'If defined, this image will be used as the background for this module. To remove a background image, simply delete the URL from the settings field.', 'et_builder' ),
						'tab_slug'           => 'general',
						'toggle_slug'        => 'background',
						'sub_toggle'         => 'column_%s',
					)
				),
				'background_color_%s'                    => ET_Builder_Element::background_field_template(
					'color',
					array(
						'label'           => esc_html__( 'Column %s Background Color', 'et_builder' ),
						'type'            => 'color-alpha',
						'custom_color'    => true,
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
						'hover'           => 'tabs',
						'sticky'          => true,
						'option_category' => 'configuration',
					)
				),
				'parallax_%s'                            => ET_Builder_Element::background_field_template(
					'parallax',
					array(
						'label'           => esc_html__( 'Column %s Parallax Effect', 'et_builder' ),
						'type'            => 'yes_no_button',
						'option_category' => 'configuration',
						'options'         => array(
							'on'  => et_builder_i18n( 'Yes' ),
							'off' => et_builder_i18n( 'No' ),
						),
						'default'         => 'off',
						'description'     => esc_html__( 'Here you can choose whether or not use parallax effect for the featured image', 'et_builder' ),
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				'parallax_method_%s'                     => ET_Builder_Element::background_field_template(
					'parallax_method',
					array(
						'label'           => esc_html__( 'Column %s Parallax Method', 'et_builder' ),
						'type'            => 'select',
						'option_category' => 'configuration',
						'options'         => array(
							'off' => esc_html__( 'CSS', 'et_builder' ),
							'on'  => esc_html__( 'True Parallax', 'et_builder' ),
						),
						'default'         => 'on',
						'show_if'         => array(
							'parallax_%s' => 'on',
						),
						'description'     => esc_html__( 'Here you can choose which parallax method to use for the featured image', 'et_builder' ),
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				'background_size_%s'                     => ET_Builder_Element::background_field_template(
					'size',
					array(
						'label'           => esc_html__( 'Column %s Background Image Size', 'et_builder' ),
						'type'            => 'select',
						'option_category' => 'layout',
						'options'         => array(
							'cover'   => et_builder_i18n( 'Cover' ),
							'contain' => et_builder_i18n( 'Fit' ),
							'initial' => et_builder_i18n( 'Actual Size' ),
							'stretch' => et_builder_i18n( 'Stretch to Fill' ),
							'custom'  => et_builder_i18n( 'Custom Size' ),
						),
						'default'         => 'cover',
						'show_if'         => array(
							'parallax_%s' => 'off',
						),
						'sub_toggle'      => 'column_%s',
					)
				),
				'background_image_width_%s'              => ET_Builder_Element::background_field_template(
					'image_width',
					array(
						'allow_empty'      => true,
						'allowed_units'    => et_pb_get_background_field_allowed_units(),
						'allowed_values'   => et_builder_get_acceptable_css_string_values( 'background-size' ),
						'default'          => 'auto',
						'default_unit'     => '%',
						'default_on_child' => true,
						'fixed_range'      => true,
						'hover'            => 'tabs',
						'label'            => esc_html__( 'Column %s Background Image Width', 'et_builder' ),
						'mobile_options'   => true,
						'option_category'  => 'layout',
						'range_settings'   => array(
							'min'       => 0,
							'min_limit' => 0,
							'max'       => 100,
							'step'      => 1,
						),
						'show_if'          => array(
							'background_size_%s' => 'custom',
						),
						'show_if_not'      => array(
							'parallax_%s' => 'on',
						),
						'sticky'           => true,
						'sub_toggle'       => 'column_%s',
						'type'             => 'range',
						'validate_unit'    => true,
					)
				),
				'background_image_height_%s'             => ET_Builder_Element::background_field_template(
					'image_height',
					array(
						'allow_empty'     => true,
						'allowed_units'   => et_pb_get_background_field_allowed_units(),
						'allowed_values'  => et_builder_get_acceptable_css_string_values( 'background-size' ),
						'default'         => 'auto',
						'default_unit'    => '%',
						'field_template'  => 'image_height',
						'fixed_range'     => true,
						'hover'           => 'tabs',
						'label'           => esc_html__( 'Column %s Background Image Height', 'et_builder' ),
						'mobile_options'  => true,
						'option_category' => 'layout',
						'range_settings'  => array(
							'min'       => 0,
							'min_limit' => 0,
							'max'       => 100,
							'step'      => 1,
						),
						'show_if'         => array(
							'background_size_%s' => 'custom',
						),
						'show_if_not'     => array(
							'parallax_%s' => 'on',
						),
						'sticky'          => true,
						'sub_toggle'      => 'column_%s',
						'type'            => 'range',
						'validate_unit'   => true,
					)
				),
				'background_position_%s'                 => ET_Builder_Element::background_field_template(
					'position',
					array(
						'label'           => esc_html__( 'Column %s Background Image Position', 'et_builder' ),
						'type'            => 'select',
						'option_category' => 'layout',
						'options'         => et_pb_get_background_position_options(),
						'default'         => 'center',
						'show_if_not'     => array(
							'parallax_%s' => 'on',
						),
						'sub_toggle'      => 'column_%s',
					)
				),
				'background_horizontal_offset_%s'        => ET_Builder_Element::background_field_template(
					'horizontal_offset',
					array(
						'allowed_units'   => et_pb_get_background_field_allowed_units(),
						'default'         => '0',
						'default_unit'    => '%',
						'field_template'  => 'horizontal_offset',
						'fixed_range'     => true,
						'hover'           => 'tabs',
						'label'           => esc_html__( 'Column %s Background Image Horizontal Offset', 'et_builder' ),
						'mobile_options'  => true,
						'option_category' => 'layout',
						'range_settings'  => array(
							'min'  => - 100,
							'max'  => 100,
							'step' => 1,
						),
						'sticky'          => true,
						'show_if'         => array(
							'background_position_%s' => array(
								'top_left',
								'top_right',
								'center_left',
								'center_right',
								'bottom_left',
								'bottom_right',
							),
						),
						'show_if_not'     => array(
							'parallax_%s' => 'on',
						),
						'sub_toggle'      => 'column_%s',
						'type'            => 'range',
						'validate_unit'   => true,
					)
				),
				'background_vertical_offset_%s'          => ET_Builder_Element::background_field_template(
					'vertical_offset',
					array(
						'allowed_units'   => et_pb_get_background_field_allowed_units(),
						'default'         => '0',
						'default_unit'    => '%',
						'field_template'  => 'vertical_offset',
						'fixed_range'     => true,
						'hover'           => 'tabs',
						'label'           => esc_html__( 'Column %s Background Image Vertical Offset', 'et_builder' ),
						'mobile_options'  => true,
						'option_category' => 'layout',
						'range_settings'  => array(
							'min'  => - 100,
							'max'  => 100,
							'step' => 1,
						),
						'show_if'         => array(
							'background_position_%s' => array(
								'top_left',
								'top_center',
								'top_right',
								'bottom_left',
								'bottom_center',
								'bottom_right',
							),
						),
						'show_if_not'     => array(
							'parallax_%s' => 'on',
						),
						'sticky'          => true,
						'sub_toggle'      => 'column_%s',
						'type'            => 'range',
						'validate_unit'   => true,
					)
				),
				'background_repeat_%s'                   => ET_Builder_Element::background_field_template(
					'repeat',
					array(
						'label'           => esc_html__( 'Column %s Background Image Repeat', 'et_builder' ),
						'type'            => 'select',
						'option_category' => 'layout',
						'options'         => et_pb_get_background_repeat_options(),
						'default'         => 'no-repeat',
						'show_if'         => array(
							'background_size_%s' => array(
								'cover',
								'contain',
								'initial',
								'custom',
							),
						),
						'show_if_not'     => array(
							'parallax_%s' => 'on',
						),
						'sub_toggle'      => 'column_%s',
					)
				),
				'background_blend_%s'                    => ET_Builder_Element::background_field_template(
					'blend',
					array(
						'label'           => esc_html__( 'Column %s Background Image Blend', 'et_builder' ),
						'type'            => 'select',
						'option_category' => 'layout',
						'options'         => et_pb_get_background_blend_mode_options(),
						'default'         => 'normal',
						'sub_toggle'      => 'column_%s',
					)
				),
				'use_background_color_gradient_%s'       => ET_Builder_Element::background_field_template(
					'use_color_gradient',
					array(
						'label'           => esc_html__( 'Column %s Use Background Color Gradient', 'et_builder' ),
						'type'            => 'yes_no_button',
						'option_category' => 'configuration',
						'options'         => array(
							'off' => et_builder_i18n( 'No' ),
							'on'  => et_builder_i18n( 'Yes' ),
						),
						'default'         => 'off',
						'description'     => '',
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				'background_color_gradient_stops_%s'     => ET_Builder_Element::background_field_template(
					'color_gradient_stops',
					array(
						'label'           => esc_html__( 'Column %s Gradient Stops', 'et_builder' ),
						'type'            => 'gradient-stops',
						'option_category' => 'configuration',
						'description'     => esc_html__( 'Add two or more color stops to your gradient background. Each stop can be dragged to any position on the gradient bar. From each color stop to the next the color is interpolated into a smooth gradient.', 'et_builder' ),
						'default'         => ET_Global_Settings::get_value( 'all_background_gradient_stops' ),
						'show_if'      => array(
							'use_background_color_gradient_%s' => 'on',
						),
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				'background_color_gradient_type_%s'      => ET_Builder_Element::background_field_template(
					'color_gradient_type',
					array(
						'label'           => esc_html__( 'Column %s Gradient Type', 'et_builder' ),
						'type'            => 'select',
						'option_category' => 'configuration',
						'options'         => array(
							'linear'     => et_builder_i18n( 'Linear' ),
							'circular'   => et_builder_i18n( 'Circular' ),
							'elliptical' => et_builder_i18n( 'Elliptical' ),
							'conic'      => et_builder_i18n( 'Conical' ),
						),
						'default'         => ET_Global_Settings::get_value( 'all_background_gradient_type' ),
						'description'     => '',
						'show_if'         => array(
							'use_background_color_gradient_%s' => 'on',
						),
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				'background_color_gradient_direction_%s' => ET_Builder_Element::background_field_template(
					'color_gradient_direction',
					array(
						'label'           => esc_html__( 'Column %s Gradient Direction', 'et_builder' ),
						'type'            => 'range',
						'option_category' => 'configuration',
						'range_settings'  => array(
							'min'  => 1,
							'max'  => 360,
							'step' => 1,
						),
						'default'         => ET_Global_Settings::get_value( 'all_background_gradient_direction' ),
						'validate_unit'   => true,
						'fixed_unit'      => 'deg',
						'fixed_range'     => true,
						'show_if'         => array(
							'background_color_gradient_type_%s' => array(
								'linear',
								'conic',
							),
						),
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				'background_color_gradient_direction_radial_%s' => ET_Builder_Element::background_field_template(
					'color_gradient_direction_radial',
					array(
						'label'           => esc_html__( 'Column %s Gradient Position', 'et_builder' ),
						'type'            => 'select',
						'option_category' => 'configuration',
						'options'         => array(
							'center'       => et_builder_i18n( 'Center' ),
							'top left'     => et_builder_i18n( 'Top Left' ),
							'top'          => et_builder_i18n( 'Top' ),
							'top right'    => et_builder_i18n( 'Top Right' ),
							'right'        => et_builder_i18n( 'Right' ),
							'bottom right' => et_builder_i18n( 'Bottom Right' ),
							'bottom'       => et_builder_i18n( 'Bottom' ),
							'bottom left'  => et_builder_i18n( 'Bottom Left' ),
							'left'         => et_builder_i18n( 'Left' ),
						),
						'default'         => '',
						'description'     => '',
						'show_if'         => array(
							'background_color_gradient_type_%s' => array(
								'radial',
								'circular',
								'elliptical',
								'conic',
							),
						),
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				'background_color_gradient_repeat_%s' => ET_Builder_Element::background_field_template(
					'color_gradient_repeat',
					array(
						'label'           => esc_html__( 'Column %s Repeat Gradient', 'et_builder' ),
						'type'            => 'yes_no_button',
						'option_category' => 'configuration',
						'options'         => array(
							'off'     => et_builder_i18n( 'No' ),
							'on'      => et_builder_i18n( 'Yes' ),
							'default' => intval( ET_Global_Settings::get_value( 'all_background_gradient_repeat' ) ),
						),
						'description'     => '',
						'show_if'         => array(
							'use_background_color_gradient_%s' => 'on',
						),
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				'background_color_gradient_unit_%s'      => ET_Builder_Element::background_field_template(
					'color_gradient_unit',
					array(
						'label'           => esc_html__( 'Column %s Gradient Unit', 'et_builder' ),
						'type'            => 'select',
						'option_category' => 'configuration',
						'options'          => array(
							'%'    => et_builder_i18n( 'Percent' ),
							'px'   => et_builder_i18n( 'Pixels' ),
							'em'   => et_builder_i18n( 'Font Size (em)' ),
							'rem'  => et_builder_i18n( 'Root-level Font Size (rem)' ),
							'ex'   => et_builder_i18n( 'X-Height (ex)' ),
							'ch'   => et_builder_i18n( 'Zero-width (ch)' ),
							'pc'   => et_builder_i18n( 'Picas (pc)' ),
							'pt'   => et_builder_i18n( 'Points (pt)' ),
							'cm'   => et_builder_i18n( 'Centimeters (cm)' ),
							'mm'   => et_builder_i18n( 'Millimeters (mm)' ),
							'in'   => et_builder_i18n( 'Inches (in)' ),
							'vh'   => et_builder_i18n( 'Viewport Height (vh)' ),
							'vw'   => et_builder_i18n( 'Viewport Width (vw)' ),
							'vmin' => et_builder_i18n( 'Viewport Minimum (vmin)' ),
							'vmax' => et_builder_i18n( 'Viewport Maximum (vmax)' ),
						),
						'default'         => ET_Global_Settings::get_value( 'all_background_gradient_unit' ),
						'description'     => '',
						'show_if'          => array(
							// Do not render this control for conic gradients.
							'background_color_gradient_type_%s' => array(
								'linear',
								'radial',
								'circular',
								'elliptical',
							),
						),
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				'background_color_gradient_overlays_image_%s' => ET_Builder_Element::background_field_template(
					'color_gradient_overlays_image',
					array(
						'label'           => esc_html__( 'Column %s Place Gradient Above Background Image', 'et_builder' ),
						'type'            => 'yes_no_button',
						'option_category' => 'configuration',
						'options'         => array(
							'off'     => et_builder_i18n( 'No' ),
							'on'      => et_builder_i18n( 'Yes' ),
							'default' => intval( ET_Global_Settings::get_value( 'all_background_gradient_overlays_image' ) ),
						),
						'description'     => '',
						'show_if'         => array(
							'use_background_color_gradient_%s' => 'on',
						),
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),

				// Deprecated.
				'background_color_gradient_start_%s'     => ET_Builder_Element::background_field_template(
					'color_gradient_start',
					array(
						'label'           => esc_html__( 'Column %s Gradient Start', 'et_builder' ),
						'type'            => 'color-alpha',
						'option_category' => 'configuration',
						'description'     => '',
						'default'         => ET_Global_Settings::get_value( 'all_background_gradient_start' ),
						'show_if'         => array(
							'use_background_color_gradient_%s' => 'on',
						),
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				// Deprecated.
				'background_color_gradient_end_%s'       => ET_Builder_Element::background_field_template(
					'color_gradient_end',
					array(
						'label'           => esc_html__( 'Column %s Gradient End', 'et_builder' ),
						'type'            => 'color-alpha',
						'option_category' => 'configuration',
						'description'     => '',
						'default'         => ET_Global_Settings::get_value( 'all_background_gradient_end' ),
						'show_if'         => array(
							'use_background_color_gradient_%s' => 'on',
						),
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				// Deprecated.
				'background_color_gradient_start_position_%s' => ET_Builder_Element::background_field_template(
					'color_gradient_start_position',
					array(
						'label'           => esc_html__( 'Column %s Start Position', 'et_builder' ),
						'type'            => 'range',
						'option_category' => 'configuration',
						'range_settings'  => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						),
						'default'         => ET_Global_Settings::get_value( 'all_background_gradient_start_position' ),
						'validate_unit'   => true,
						'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pc', 'ex', 'vh', 'vw' ),
						'default_unit'    => '%',
						'fixed_range'     => true,
						'show_if'         => array(
							'use_background_color_gradient_%s' => 'on',
						),
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				// Deprecated.
				'background_color_gradient_end_position_%s' => ET_Builder_Element::background_field_template(
					'color_gradient_end_position',
					array(
						'label'           => esc_html__( 'Column %s End Position', 'et_builder' ),
						'type'            => 'range',
						'option_category' => 'configuration',
						'range_settings'  => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						),
						'default'         => ET_Global_Settings::get_value( 'all_background_gradient_end_position' ),
						'validate_unit'   => true,
						'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pc', 'ex', 'vh', 'vw' ),
						'default_unit'    => '%',
						'fixed_range'     => true,
						'show_if'         => array(
							'use_background_color_gradient_%s' => 'on',
						),
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),

				'background_video_mp4_%s'                => ET_Builder_Element::background_field_template(
					'video_mp4',
					array(
						'label'              => esc_html__( 'Column %s Background Video MP4', 'et_builder' ),
						'type'               => 'upload',
						'option_category'    => 'basic_option',
						'data_type'          => 'video',
						'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
						'choose_text'        => esc_attr__( 'Choose a Background Video MP4 File', 'et_builder' ),
						'update_text'        => esc_attr__( 'Set As Background Video', 'et_builder' ),
						'tab_slug'           => 'general',
						'toggle_slug'        => 'background',
						'sub_toggle'         => 'column_%s',
					)
				),
				'background_video_webm_%s'               => ET_Builder_Element::background_field_template(
					'video_webm',
					array(
						'label'              => esc_html__( 'Column %s Background Video Webm', 'et_builder' ),
						'type'               => 'upload',
						'option_category'    => 'basic_option',
						'data_type'          => 'video',
						'upload_button_text' => esc_attr__( 'Upload a video', 'et_builder' ),
						'choose_text'        => esc_attr__( 'Choose a Background Video WEBM File', 'et_builder' ),
						'update_text'        => esc_attr__( 'Set As Background Video', 'et_builder' ),
						'tab_slug'           => 'general',
						'toggle_slug'        => 'background',
						'sub_toggle'         => 'column_%s',
					)
				),
				'background_video_width_%s'              => ET_Builder_Element::background_field_template(
					'video_width',
					array(
						'label'           => esc_html__( 'Column %s Background Video Width', 'et_builder' ),
						'type'            => 'text',
						'option_category' => 'basic_option',
						'tab_slug'        => 'general',
						'sub_toggle'      => 'column_%s',
					)
				),
				'background_video_height_%s'             => ET_Builder_Element::background_field_template(
					'video_height',
					array(
						'label'           => esc_html__( 'Column %s Background Video Height', 'et_builder' ),
						'type'            => 'text',
						'option_category' => 'basic_option',
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				'allow_player_pause_%s'                  => ET_Builder_Element::background_field_template(
					'allow_player_pause',
					array(
						'label'           => esc_html__( 'Column %s Pause Video When Another Video Plays', 'et_builder' ),
						'type'            => 'yes_no_button',
						'option_category' => 'configuration',
						'options'         => array(
							'off' => et_builder_i18n( 'No' ),
							'on'  => et_builder_i18n( 'Yes' ),
						),
						'default'         => 'off',
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				'background_video_pause_outside_viewport_%s' => ET_Builder_Element::background_field_template(
					'video_pause_outside_viewport',
					array(
						'label'           => esc_html__( 'Column %s Pause Video While Not In View', 'et_builder' ),
						'type'            => 'yes_no_button',
						'option_category' => 'configuration',
						'options'         => array(
							'off' => et_builder_i18n( 'No' ),
							'on'  => et_builder_i18n( 'Yes' ),
						),
						'default'         => 'on',
						'tab_slug'        => 'general',
						'toggle_slug'     => 'background',
						'sub_toggle'      => 'column_%s',
					)
				),
				'__video_background_%s'                  => ET_Builder_Element::background_field_template(
					'video_computed',
					array(
						'type'                => 'computed',
						'computed_callback'   => array( 'ET_Builder_Column', 'get_column_video_background' ),
						'computed_depends_on' => array(
							'background_video_mp4_%s',
							'background_video_webm_%s',
							'background_video_width_%s',
							'background_video_height_%s',
						),
						'option_category'     => 'basic_option',
					)
				),
			),
			'advanced' => array(
				'padding_%s' => array(
					'label'           => esc_html__( 'Column %s Padding', 'et_builder' ),
					'type'            => 'custom_padding',
					'mobile_options'  => true,
					'option_category' => 'layout',
					'description'     => esc_html__( 'Adjust padding to specific values, or leave blank to use the default padding.', 'et_builder' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'margin_padding',
					'sub_toggle'      => 'column_%s',
					'hover'           => 'tabs',
					'sticky'          => true,
					'allowed_units'   => array(
						'%',
						'em',
						'rem',
						'px',
						'cm',
						'mm',
						'in',
						'pt',
						'pc',
						'ex',
						'vh',
						'vw',
					),
				),
			),
			'css'      => array(
				'module_id_%s'         => array(
					'label'           => esc_html__( 'Column %s CSS ID', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'classes',
					'sub_toggle'      => 'column_%s',
					'option_class'    => 'et_pb_custom_css_regular',
				),
				'module_class_%s'      => array(
					'label'           => esc_html__( 'Column %s CSS Class', 'et_builder' ),
					'type'            => 'text',
					'option_category' => 'configuration',
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'classes',
					'sub_toggle'      => 'column_%s',
					'option_class'    => 'et_pb_custom_css_regular',
				),
				'custom_css_before_%s' => array(
					'label'                    => esc_html__( 'Column %s before', 'et_builder' ),
					'no_space_before_selector' => true,
					'selector'                 => ':before',
					'tab_slug'                 => 'custom_css',
					'toggle_slug'              => 'custom_css',
					'sub_toggle'               => 'column_%s',
					'hover'                    => 'tabs',
					'sticky'                   => true,
					'option_category'          => 'layout',
				),
				'custom_css_main_%s'   => array(
					'label'           => esc_html__( 'Column %s Main Element', 'et_builder' ),
					'tab_slug'        => 'custom_css',
					'toggle_slug'     => 'custom_css',
					'sub_toggle'      => 'column_%s',
					'hover'           => 'tabs',
					'sticky'          => true,
					'option_category' => 'layout',
				),
				'custom_css_after_%s'  => array(
					'label'                    => esc_html__( 'Column %s After', 'et_builder' ),
					'no_space_before_selector' => true,
					'selector'                 => ':after',
					'tab_slug'                 => 'custom_css',
					'toggle_slug'              => 'custom_css',
					'sub_toggle'               => 'column_%s',
					'hover'                    => 'tabs',
					'sticky'                   => true,
					'option_category'          => 'layout',
				),

			),
		),
		'knownShortcodeWrappers'       => et_fb_known_shortcode_wrappers(),
		'acceptableCSSStringValues'    => et_builder_get_acceptable_css_string_values( 'all' ),
		'customModuleCredits'          => ET_Builder_Element::get_custom_modules_credits( $post_type ),
		'ignoreAdminBarClickIds'       => apply_filters( 'et_fb_ignore_adminbar_click_ids', array() ),
		'stickyElements'               => array(
			'incompatibleFields'   => $sticky->get_incompatible_fields(),
			'validStickyPositions' => $sticky->get_valid_sticky_positions(),
		),
	);
	// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
	// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow

	// Include Pattern/Mask fields for Specialty Section Column Background Settings.
	$column_general_fields = $helpers['columnSettingFields']['general'];
	$column_pattern_fields = et_pb_get_pattern_fields( 'background', true );
	$column_mask_fields    = et_pb_get_mask_fields( 'background', true );

	$helpers['columnSettingFields']['general'] = array_merge( $column_general_fields, $column_pattern_fields, $column_mask_fields );

	// Cleanup.
	$column_general_fields = null;
	$column_pattern_fields = null;
	$column_mask_fields    = null;

	if ( function_exists( 'WC' ) ) {
		$helpers['wooCommerce'] = array();

		if ( function_exists( 'et_builder_wc_get_checkout_notice' ) ) {
			$checkout_modules_notice = array(
				'requiredCheckoutModulesNotice' => array(
					'isBillingOnly'       => 'billing_only' === get_option( 'woocommerce_ship_to_destination' ),
					'isBillingOnlyNotice' => et_builder_wc_get_checkout_notice( 'billing_only' ),
					'notice'              => et_builder_wc_get_checkout_notice( 'shipping' ),
				),
			);

			$helpers['wooCommerce'] = array_merge( $helpers['wooCommerce'], $checkout_modules_notice );
		}

		if ( function_exists( 'et_builder_wc_get_non_checkout_page_notice' ) ) {
			$checkout_page_notice = array(
				'nonCheckoutPageNotice' => et_builder_wc_get_non_checkout_page_notice(),
			);

			$helpers['wooCommerce'] = array_merge( $helpers['wooCommerce'], $checkout_page_notice );
		}

		$checkout_page_id = array(
			'checkoutPageId' => function_exists( 'wc_get_page_id' )
				? wc_get_page_id( 'checkout' )
				: 0,
		);

		$helpers['wooCommerce'] = array_merge( $helpers['wooCommerce'], $checkout_page_id );
	}

	$modules_i10n    = ET_Builder_Element::get_modules_i10n( $post_type );
	$additional_i10n = array(
		'audio'               => array(
			'meta' => _x( 'by <strong>%1$s</strong>', 'Audio Module meta information', 'et_builder' ),
		),
		'background'          => array(
			'label'       => __( 'Background', 'et_builder' ),
			'description' => esc_html__( 'Adjust the background style of this element by customizing the background color, gradient, image, video, pattern and mask.', 'et_builder' ),
		),
		'column'              => array(
			'backgroundColor' => esc_html__( 'Column %s Background', 'et_builder' ),
		),
		'contactForm'         => array(
			'thankYou' => esc_html__( 'Thanks for contacting us', 'et_builder' ),
			'submit'   => esc_attr__( 'Submit', 'et_builder' ),
		),
		'contactFormItem'     => array(
			'noOptions'     => esc_html__( 'No options added.', 'et_builder' ),
			'selectDefault' => esc_html__( '-- Please Select --', 'et_builder' ),
		),
		'countdownTimer'      => array(
			'dayFull'     => esc_html__( 'Day(s)', 'et_builder' ),
			'dayShort'    => esc_html__( 'Day', 'et_builder' ),
			'hourFull'    => esc_html__( 'Hour(s)', 'et_builder' ),
			'hourShort'   => esc_html__( 'Hrs', 'et_builder' ),
			'minuteFull'  => esc_html__( 'Minute(s)', 'et_builder' ),
			'minuteShort' => esc_html__( 'Min', 'et_builder' ),
			'secondFull'  => esc_html__( 'Second(s)', 'et_builder' ),
			'secondShort' => esc_html__( 'Sec', 'et_builder' ),
		),
		'customCss'           => array(
			'label' => et_builder_i18n( 'Custom CSS' ),
		),
		'signup'              => array(
			'firstName' => esc_attr__( 'First Name', 'et_builder' ),
			'lastName'  => esc_attr__( 'Last Name', 'et_builder' ),
			'name'      => esc_attr__( 'Name', 'et_builder' ),
			'email'     => esc_attr__( 'Email', 'et_builder' ),
		),
		'filterablePortfolio' => array(
			'all' => esc_html__( 'All', 'et_builder' ),
		),
		'login'               => array(
			'login'          => esc_html__( 'Login', 'et_builder' ),
			'logout'         => esc_html__( 'Log out', 'et_builder' ),
			'forgotPassword' => esc_html__( 'Forgot your password?', 'et_builder' ),
			'username'       => esc_html__( 'Username', 'et_builder' ),
			'password'       => esc_html__( 'Password', 'et_builder' ),
			'note_autofill'  => esc_attr__( 'Note: this field is used to disable browser autofill during the form editing in VB', 'et_builder' ),
		),
		'postTitle'           => array(
			'by' => esc_html__( 'by ', 'et_builder' ),
		),
		'search'              => array(
			'submitButtonText' => esc_html__( 'Search', 'et_builder' ),
			'searchfor'        => esc_html__( 'Search for:', 'et_builder' ),
		),
		'fullwidthPostSlider' => array(
			'by' => esc_html__( 'by ', 'et_builder' ),
		),
		'socialFollow'        => array(
			'follow' => esc_html__( 'Follow', 'et_builder' ),
		),
		'items'               => array(
			'newItemDefaultText' => esc_html__( 'New Item', 'et_builder' ),
		),
	);

	// Prepare VB help videos list.
	$help_videos = array_merge(
		array(
			'et_pb_default'         => array(
				array(
					'id'   => 'T-Oe01_J62c',
					'name' => esc_html__( 'An introduction to the Divi Builder', 'et_builder' ),
				),
				array(
					'id'   => '9eqXcrLcnoc',
					'name' => esc_html__( 'Jump-starting your page with pre-made layouts', 'et_builder' ),
				),
				array(
					'id'   => 'exLLvnS5pR8',
					'name' => esc_html__( 'Saving and loading layouts from the Divi Library', 'et_builder' ),
				),
				array(
					'id'   => '3kmJ_mMVB1w',
					'name' => esc_html__( 'Getting creative with Sections', 'et_builder' ),
				),
				array(
					'id'   => 'R9ds7bEaHE8',
					'name' => esc_html__( 'Organizing your content with Rows', 'et_builder' ),
				),
				array(
					'id'   => '1iqjhnHVA9Y',
					'name' => esc_html__( 'Using Design settings to customize your page', 'et_builder' ),
				),
				array(
					'id'   => 'MVWpwKJR8eE',
					'name' => esc_html__( 'Using the builders Right Click controls', 'et_builder' ),
				),
				array(
					'id'   => 'PBmijAL4twA',
					'name' => esc_html__( 'Importing and exporting Divi Builder layouts', 'et_builder' ),
				),
				array(
					'id'   => 'pklyz3vcjEs',
					'name' => esc_html__( 'Become a power use with keyboard shortcuts', 'et_builder' ),
				),
			),
			'et_pb_add_section'     => array(
				array(
					'id'   => '3kmJ_mMVB1w',
					'name' => esc_html__( 'An introduction to Sections', 'et_builder' ),
				),
				array(
					'id'   => '1iqjhnHVA9Y',
					'name' => esc_html__( 'Design Settings and Advanced Section Settings', 'et_builder' ),
				),
				array(
					'id'   => 'boNZZ0MYU0E',
					'name' => esc_html__( 'Saving and loading from the library', 'et_builder' ),
				),
			),
			'et_pb_add_row'         => array(
				array(
					'id'   => 'R9ds7bEaHE8',
					'name' => esc_html__( 'An introduction to Rows', 'et_builder' ),
				),
				array(
					'id'   => '1iqjhnHVA9Y',
					'name' => esc_html__( 'Design Settings and Advanced Row Settings', 'et_builder' ),
				),
				array(
					'id'   => 'boNZZ0MYU0E',
					'name' => esc_html__( 'Saving and loading from the library', 'et_builder' ),
				),
			),
			'et_pb_add_module'      => array(
				array(
					'id'   => 'FkQuawiGWUw',
					'name' => esc_html__( 'An introduction to Modules', 'et_builder' ),
				),
				array(
					'id'   => '1iqjhnHVA9Y',
					'name' => esc_html__( 'Design Settings and Advanced Module Settings', 'et_builder' ),
				),
				array(
					'id'   => 'boNZZ0MYU0E',
					'name' => esc_html__( 'Saving and loading from the library', 'et_builder' ),
				),
			),
			'et_pb_default_layouts' => array(
				array(
					'id'   => '9eqXcrLcnoc',
					'name' => esc_html__( 'Using pre-made layouts', 'et_builder' ),
				),
				array(
					'id'   => 'boNZZ0MYU0E',
					'name' => esc_html__( ' Saving and loading from the library', 'et_builder' ),
				),
				array(
					'id'   => 'pR8b4i4E2e4',
					'name' => esc_html__( ' Using Divi Cloud', 'et_builder' ),
				),
			),
			'et_pb_portability'     => array(
				array(
					'id'   => 'PBmijAL4twA',
					'name' => esc_html__( 'Importing and exporting layouts', 'et_builder' ),
				),
			),
			'et_pb_history'         => array(
				array(
					'id'   => 'FkQuawiGWUw',
					'name' => esc_html__( 'Managing your editing history', 'et_builder' ),
				),
			),
			'et_pb_save_to_library' => array(
				array(
					'id'   => 'boNZZ0MYU0E',
					'name' => esc_html__( 'Saving and loading from the library', 'et_builder' ),
				),
				array(
					'id'   => 'pR8b4i4E2e4',
					'name' => esc_html__( 'Using Divi Cloud', 'et_builder' ),
				),
				array(
					'id'   => 'TQnPBXzTSGY',
					'name' => esc_html__( 'Global modules, rows and sections', 'et_builder' ),
				),
				array(
					'id'   => 'tarDcDjE86w',
					'name' => esc_html__( 'Using Selective Sync', 'et_builder' ),
				),
				array(
					'id'   => 'PBmijAL4twA',
					'name' => esc_html__( ' Importing and exporting items from the library', 'et_builder' ),
				),
			),
			'et_pb_page_settings'   => array(
				array(
					'id'   => 'FkQuawiGWUw',
					'name' => esc_html__( 'An introduction to Page Settings', 'et_builder' ),
				),
			),
			'et_pb_global_presets'  => array(
				array(
					'id'   => esc_html__( '3VqtCV5Obx4', 'et_builder' ),
					'name' => esc_html__( 'Using Divi Presets', 'et_builder' ),
				),
			),
		),
		ET_Builder_Element::get_help_videos()
	);

	// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned -- Invalid warning.
	// Internationalization.
	$helpers['i18n'] = array(
		'modules'                   => array_merge( $modules_i10n, $additional_i10n ),
		'saveButtonText'            => esc_attr__( 'Save', 'et_builder' ),
		'saveDraftButtonText'       => esc_attr__( 'Save Draft', 'et_builder' ),
		'saveLayoutBlockButtonText' => esc_attr__( 'Save & Exit', 'et_builder' ),
		'saveCloudItemText'         => esc_attr__( 'Save Cloud Item', 'et_builder' ),
		'publishButtonText'         => ( is_page() && ! current_user_can( 'publish_pages' ) ) || ( ! is_page() && ! current_user_can( 'publish_posts' ) ) ? esc_attr__( 'Submit', 'et_builder' ) : esc_attr__( 'Publish', 'et_builder' ),
		'controls'                  => array(
			'tinymce'          => array(
				'visual' => esc_html__( 'Visual', 'et_builder' ),
				'text'   => et_builder_i18n( 'Text' ),
			),
			'moduleItem'       => array(
				'addNew' => esc_html__( 'Add New Item', 'et_builder' ),
			),
			'upload'           => array(
				'buttonText' => esc_html__( 'Upload', 'et_builder' ),
				'addImage'   => esc_html__( 'Add Image', 'et_builder' ),
				'addVideo'   => esc_html__( 'Add Video', 'et_builder' ),
			),
			'insertMedia'      => array(
				'buttonText'     => esc_html__( 'Add Media', 'et_builder' ),
				'modalTitleText' => esc_html__( 'Insert Media', 'et_builder' ),
			),
			'inputMargin'      => array(
				'top'    => et_builder_i18n( 'Top' ),
				'right'  => et_builder_i18n( 'Right' ),
				'bottom' => et_builder_i18n( 'Bottom' ),
				'left'   => et_builder_i18n( 'Left' ),
			),
			'colorpicker'      => array(
				'clear'          => esc_html__( 'Clear', 'et_builder' ),
				'select'         => esc_html__( 'Select', 'et_builder' ),
				'selectColor'    => esc_html__( 'Select Color', 'et_builder' ),
				'noColor'        => esc_html__( 'Transparent', 'et_builder' ),
				'addColor'       => esc_html__( 'Add Color', 'et_builder' ),
				'addGlobalColor' => esc_html__( 'Add Global Color', 'et_builder' ),
			),
			'colorManager'     => array(
				'saved'  => esc_html__( 'Saved', 'et_builder' ),
				'global' => esc_html__( 'Global', 'et_builder' ),
				'recent' => esc_html__( 'Recent', 'et_builder' ),
			),
			'customizerColorLabels' => array(
				'gcid-primary-color'   => esc_html__( 'Primary Color', 'et_builder' ),
				'gcid-secondary-color' => esc_html__( 'Secondary Color', 'et_builder' ),
				'gcid-heading-color'   => esc_html__( 'Heading Text Color', 'et_builder' ),
				'gcid-body-color'      => esc_html__( 'Body Text Color', 'et_builder' ),
			),
			'customizerCannotDelete' => esc_html__( 'cannot be deleted', 'et_builder' ),
			'uploadGallery'    => array(
				'uploadButtonText' => esc_html__( 'Update Gallery', 'et_builder' ),
				'addImages'        => esc_html__( 'Add Gallery Images', 'et_builder' ),
			),
			'centerMap'        => array(
				'updateMapButtonText'  => esc_html__( 'Find', 'et_builder' ),
				'geoCodeError'         => esc_html__( 'Geocode was not successful for the following reason', 'et_builder' ),
				'geoCodeError_2'       => esc_html__( 'Geocoder failed due to', 'et_builder' ),
				'noResults'            => esc_html__( 'No results found', 'et_builder' ),
				'mapPinAddressInvalid' => esc_html__( 'Invalid Pin and address data. Please try again.', 'et_builder' ),
			),
			'tabs'             => array(
				'general'        => et_builder_i18n( 'Content' ),
				'design'         => et_builder_i18n( 'Design' ),
				'advanced'       => et_builder_i18n( 'Design' ),
				'css'            => et_builder_i18n( 'Advanced' ),
				'desktop'        => et_builder_i18n( 'Desktop' ),
				'tablet'         => et_builder_i18n( 'Tablet' ),
				'phone'          => et_builder_i18n( 'Phone' ),
				'hover'          => esc_html__( 'Hover', 'et_builder' ),
				'moduleElements' => esc_html__( 'Module Elements', 'et_builder' ),
				'freeFormCSS'    => esc_html__( 'Free-Form CSS', 'et_builder' ),
			),
			'additionalButton' => array(
				'changeApiKey'              => esc_html__( 'Change API Key', 'et_builder' ),
				'generateImageUrlFromVideo' => esc_html__( 'Generate From Video', 'et_builder' ),
			),
			'conditionalLogic' => array(
				'checked'        => esc_html__( 'checked', 'et_builder' ),
				'unchecked'      => esc_html__( 'not checked', 'et_builder' ),
				'is'             => esc_html__( 'equals', 'et_builder' ),
				'isNot'          => esc_html__( 'does not equal', 'et_builder' ),
				'isGreater'      => esc_html__( 'is greater than', 'et_builder' ),
				'isLess'         => esc_html__( 'is less than', 'et_builder' ),
				'contains'       => esc_html__( 'contains', 'et_builder' ),
				'doesNotContain' => esc_html__( 'does not contain', 'et_builder' ),
				'isEmpty'        => esc_html__( 'is empty', 'et_builder' ),
				'isNotEmpty'     => esc_html__( 'is not empty', 'et_builder' ),
			),
			'selectAnimation'  => array(
				'none'   => et_builder_i18n( 'None' ),
				'fade'   => et_builder_i18n( 'Fade' ),
				'slide'  => et_builder_i18n( 'Slide' ),
				'bounce' => esc_html__( 'Bounce', 'et_builder' ),
				'zoom'   => esc_html__( 'Zoom', 'et_builder' ),
				'flip'   => et_builder_i18n( 'Flip' ),
				'fold'   => esc_html__( 'Fold', 'et_builder' ),
				'roll'   => esc_html__( 'Roll', 'et_builder' ),
			),
			'cssText'          => esc_html__( 'CSS', 'et_builder' ),
			'cssSelector'      => esc_html__( 'CSS added here will target the following class:', 'et_builder' ),
			'cssSelectorFree'  => esc_html__( 'CSS added here can target this element and any sub element using the selector keyword.', 'et_builder' ),
			'hoverOptions'     => array(
				'default' => et_builder_i18n( 'Default' ),
				'hover'   => esc_html__( 'Hover', 'et_builder' ),
				'tablet'  => et_builder_i18n( 'Tablet' ),
				'phone'   => et_builder_i18n( 'Phone' ),
			),
			'background'       => array(
				'addColor'       => esc_html__( 'Add Background Color', 'et_builder' ),
				'addGradient'    => esc_html__( 'Add Background Gradient', 'et_builder' ),
				'addImage'       => esc_html__( 'Add Background Image', 'et_builder' ),
				'addVideo'       => esc_html__( 'Add Background Video', 'et_builder' ),
				'addPattern'     => esc_html__( 'Add Background Pattern', 'et_builder' ),
				'addMask'        => esc_html__( 'Add Background Mask', 'et_builder' ),
				'color'          => esc_html__( 'Background Color', 'et_builder' ),
				'gradient'       => esc_html__( 'Background Gradient', 'et_builder' ),
				'gradientColors' => esc_html__( 'Background Gradient Colors', 'et_builder' ),
				'image'          => esc_html__( 'Background Image', 'et_builder' ),
				'pattern'        => esc_html__( 'Background Pattern', 'et_builder' ),
				'mask'           => esc_html__( 'Background Mask', 'et_builder' ),
				'video'          => esc_html__( 'Background Video', 'et_builder' ),
			),
			'responsiveTabs'   => array(
				'desktop' => et_builder_i18n( 'Desktop' ),
				'tablet'  => et_builder_i18n( 'Tablet' ),
				'phone'   => et_builder_i18n( 'Phone' ),
			),
			'element'          => esc_html__( 'Element', 'et_builder' ),
		),
		'rightClickMenuItems'       => array(
			'undo'                      => esc_html__( 'Undo', 'et_builder' ),
			'redo'                      => esc_html__( 'Redo', 'et_builder' ),
			'lock'                      => esc_html__( 'Lock', 'et_builder' ),
			'lock_items'                => esc_html__( 'Lock', 'et_builder' ),
			'unlock'                    => esc_html__( 'Unlock', 'et_builder' ),
			'copy'                      => esc_html__( 'Copy', 'et_builder' ),
			'copy_items'                => esc_html__( 'Copy Elements', 'et_builder' ),
			'paste'                     => esc_html__( 'Paste', 'et_builder' ),
			'paste_items'               => esc_html__( 'Paste Elements', 'et_builder' ),
			'reset'                     => esc_html__( 'Reset', 'et_builder' ),
			'reset_styles'              => esc_html__( 'Reset Styles', 'et_builder' ),
			// Translators: %1$s: module name.
			'resetModuleStyles'         => esc_html__( 'Reset %1$s Styles', 'et_builder' ),
			// Translators: %1$s: module name.
			'resetDefaultModuleStyles'  => esc_html__( 'Reset %1$s Styles Defaults', 'et-builder' ),
			// Translators: %1$s: reset title (module name, toggle group, or option name).
			'resetDefaultStyles'        => esc_html__( 'Reset %1$s Defaults', 'et_builder' ),
			'styles'                    => esc_html__( 'Styles', 'et_builder' ),
			'copyStyle'                 => esc_html__( 'Copy Style', 'et_builder' ),
			'pasteStyle'                => esc_html__( 'Paste Style', 'et_builder' ),
			'disable'                   => esc_html__( 'Disable', 'et_builder' ),
			'disable_items'             => esc_html__( 'Disable', 'et_builder' ),
			'enable'                    => esc_html__( 'Enable', 'et_builder' ),
			'save'                      => esc_html__( 'Save To Library', 'et_builder' ),
			'saveCloud'                 => esc_html__( 'Save To Divi Cloud', 'et_builder' ),
			'startABTesting'            => esc_html__( 'Split Test', 'et_builder' ),
			'endABTesting'              => esc_html__( 'End Split Test', 'et_builder' ),
			'moduleType'                => array(
				'module'   => esc_html__( 'Module', 'et_builder' ),
				'column'   => esc_html__( 'Column', 'et_builder' ),
				'row'      => esc_html__( 'Row', 'et_builder' ),
				'section'  => esc_html__( 'Section', 'et_builder' ),
				'modules'  => esc_html__( 'Modules', 'et_builder' ),
				'columns'  => esc_html__( 'Columns', 'et_builder' ),
				'rows'     => esc_html__( 'Rows', 'et_builder' ),
				'sections' => esc_html__( 'Sections', 'et_builder' ),
			),
			'disableGlobal'             => esc_html__( 'Disable Global', 'et_builder' ),
			'collapse'                  => esc_html__( 'Collapse', 'et_builder' ),
			'expand'                    => et_builder_i18n( 'Expand' ),
			'stylesModified'            => esc_html__( 'View Modified Styles', 'et_builder' ),
			'toggle'                    => esc_html__( 'Toggle', 'et_builder' ),
			'tab'                       => esc_html__( 'Tab', 'et_builder' ),
			'option'                    => esc_html__( 'Option', 'et_builder' ),
			'options'                   => esc_html__( 'Options', 'et_builder' ),
			'optionsGroup'              => esc_html__( 'Options Group', 'et_builder' ),
			'optionsGroups'             => esc_html__( 'Options Groups', 'et_builder' ),
			'item'                      => esc_html__( 'Item', 'et_builder' ),
			'go_to_option'              => esc_html__( 'Go To Option', 'et_builder' ),
			'find_replace'              => esc_html__( 'Find & Replace', 'et_builder' ),
			'extend_styles'             => array(
				'module'        => esc_html__( 'Extend %s Styles', 'et_builder' ),
				'child_item'    => esc_html__( 'Extend Item Styles', 'et_builder' ),
				'options_group' => esc_html__( 'Extend %s Styles', 'et_builder' ),
				'option'        => esc_html__( 'Extend %s', 'et_builder' ),
			),
			'makeDefault'               => esc_html__( 'Make Default', 'et_builder' ),
			'makeStyleDefault'          => esc_html__( 'Make Style Default', 'et_builder' ),
			'makeStylesDefault'         => esc_html__( 'Make Styles Default', 'et_builder' ),
			'modifyDefaultValue'        => esc_html__( 'Modify Default Value', 'et_builder' ),
			'modifyDefaultValues'       => esc_html__( 'Modify Default Values', 'et_builder' ),
			'detachFromGlobal'          => esc_html__( 'Detach From Global', 'et_builder' ),
			'convertToGlobal'           => esc_html__( 'Convert To Global', 'et_builder' ),
			'makeGlobalColor'           => esc_html__( 'Make Global Color', 'et_builder' ),
			'editSavedColor'            => esc_html__( 'Edit Saved Color', 'et_builder' ),
			'editGlobalColor'           => esc_html__( 'Edit Global Color', 'et_builder' ),
			'deleteGlobalColor'         => esc_html__( 'Delete Global Color', 'et_builder' ),
			'replaceGlobalColor'        => esc_html__( 'Replace Global Color', 'et_builder' ),
			'applyToCurrentPreset'      => esc_html__( 'Apply To Active Preset', 'et_builder' ),
			'applyStyleToCurrentPreset' => esc_html__( 'Apply Style To Active Preset', 'et_builder' ),
			'applyStylesToActivePreset' => esc_html__( 'Apply Styles To Active Preset', 'et_builder' ),
			'editPresetStyle'           => esc_html__( 'Edit Preset Style', 'et_builder' ),
			'goToLayer'                 => esc_html__( 'Go To Layer', 'et_builder' ),
			'gradientStops'             => array(
				'findReplace' => esc_html__( 'Find & Replace Color', 'et_builder' ),
				'remove'      => esc_html__( 'Remove Gradient Stop', 'et_builder' ),
			),
			'generate_content_with_ai'  => esc_html__( 'Generate Content With AI', 'et_builder' ),
		),
		'tooltips'                  => array(
			'insertModule'         => esc_html__( 'Insert Module', 'et_builder' ),
			'insertColumn'         => esc_html__( 'Insert Columns', 'et_builder' ),
			'insertSection'        => esc_html__( 'Insert Section', 'et_builder' ),
			'insertRow'            => esc_html__( 'Insert Row', 'et_builder' ),
			'newModule'            => esc_html__( 'New Module', 'et_builder' ),
			'newRow'               => esc_html__( 'New Row', 'et_builder' ),
			'newSection'           => esc_html__( 'New Section', 'et_builder' ),
			'addFromLibrary'       => esc_html__( 'Add From Library', 'et_builder' ),
			'addToLayoutLibrary'   => esc_html__( 'Add To Layout Library', 'et_builder' ),
			'addToModuleLibrary'   => esc_html__( 'Add To Module Library', 'et_builder' ),
			'addToRowLibrary'      => esc_html__( 'Add To Row Library', 'et_builder' ),
			'addToSectionLibrary'  => esc_html__( 'Add To Section Library', 'et_builder' ),
			'loading'              => esc_html__( 'loading...', 'et_builder' ),
			'regular'              => esc_html__( 'Regular', 'et_builder' ),
			'fullwidth'            => esc_html__( 'Fullwidth', 'et_builder' ),
			'selectIcon'           => esc_html__( 'Select Icon', 'et_builder' ),
			'specialty'            => esc_html__( 'Specialty', 'et_builder' ),
			'changeRow'            => esc_html__( 'Choose Layout', 'et_builder' ),
			'clearLayout'          => esc_html__( 'Clear Layout', 'et_builder' ),
			'clearLayoutText'      => esc_html__( 'All of your current page content will be lost. Do you wish to proceed?', 'et_builder' ),
			'yes'                  => et_builder_i18n( 'Yes' ),
			'loadLayout'           => esc_html__( 'Load From Library', 'et_builder' ),
			'layoutDetails'        => esc_html__( 'Layout Details', 'et_builder' ),
			'Enable Divi Cloud'    => esc_html__( 'Enable Divi Cloud', 'et_builder' ),
			'layoutName'           => esc_html__( 'Layout Name', 'et_builder' ),
			'replaceLayout'        => esc_html__( 'Replace Existing Content', 'et_builder' ),
			'search'               => esc_html__( 'Search', 'et_builder' ) . '...',
			'portability'          => esc_html__( 'Import & Export Page Content', 'et_builder' ),
			'export'               => esc_html__( 'Export', 'et_builder' ),
			'import'               => esc_html__( 'Import', 'et_builder' ),
			'exportText'           => esc_html__( 'Exporting your Divi Builder Layout will create a JSON file that can be imported into a different website.', 'et_builder' ),
			'exportName'           => esc_html__( 'Export File Name', 'et_builder' ),
			'exportButton'         => esc_html__( 'Export Divi Builder Layout', 'et_builder' ),
			'importText'           => esc_html__( 'Importing a previously-exported Divi Builder Layout file will overwrite all content currently on this page.', 'et_builder' ),
			'importField'          => esc_html__( 'Select File To Import', 'et_builder' ),
			'importBackUp'         => esc_html__( 'Download Backup Before Importing', 'et_builder' ),
			'importButton'         => esc_html__( 'Import Divi Builder Layout', 'et_builder' ),
			'noFile'               => esc_html__( 'No File Selected', 'et_builder' ),
			'chooseFile'           => esc_html__( 'Choose File', 'et_builder' ),
			'chooseFiles'           => esc_html__( 'Choose Files', 'et_builder' ),
			'portabilityOptions'   => esc_html__( 'Options', 'et_builder' ),
			'includeGlobalPresets' => esc_html__( 'Include Presets', 'et_builder' ),
			'applyGlobalPresets'   => esc_html__( 'Apply To Exported Layout', 'et_builder' ),
			'importContextFail'    => esc_html__( 'This file should not be imported in this context.', 'et_builder' ),
			'bulkImportContextFail'    => esc_html__( 'These files should not be imported in this context.', 'et_builder' ),
			'closeWindow'              => esc_html__( 'Close Window', 'et_builder' ),
			'no'                       => esc_html__( 'No', 'et_builder' ),
			'yes'                      => esc_html__( 'Yes', 'et_builder' ),
			'closelibraryConfirmation' => esc_html__( 'Are you sure you want to cancel current request(s) and close the window?', 'et_builder' ),
			'globalPresets'        => array(
				'title'            => esc_html__( 'Are You Sure?', 'et_builder' ),
				'text'             => array(
					// Translators: %1$s: preset name.
					'madeChanges'   => esc_html__( 'You\'ve made changes to the %1$s preset settings.', 'et_builder' ),
					'wishToProceed' => array(
						// Translators: %1$s: preset name.
						'saveDefaults'              => esc_html__( 'This will affect all %1$s across your entire site. Do you wish to proceed?', 'et_builder' ),
						// Translators: %1$s = module name; %2$s = preset name.
						'applyStylesToActivePreset' => esc_html__( 'This will affect all <strong>%1$s</strong> using the <strong>%2$s</strong> across your entire site. Do you wish to proceed?', 'et_builder' ),
						// Translators: %1$s = preset name; %2$s = module name.
						'deletePreset'              => esc_html__( 'This will delete and unassign this <strong>%1$s</strong> preset across your entire site, and assign the <strong>Default %2$s Preset</strong> in all instances. Do you wish to proceed?', 'et_builder' ),
						// Translators: %1$s = preset name; %2$s = module name (plural); %3$s = module name.
						'assignPresetToDefault'     => esc_html__( 'This will assign <strong>%1$s</strong> as the <strong>Default %3$s Preset</strong> across your entire site. All %2$s that use the <strong>Default %3$s Preset</strong> will be affected. Do you wish to proceed?', 'et_builder' ),
					),
					'applyOnImport' => esc_html__( 'You are about to import the layout\'s presets.', 'et_builder' ),
				),
				'module'           => esc_html__( 'Module', 'et_builder' ),
				'modules'          => esc_html__( 'Modules', 'et_builder' ),
				'migrationWarning' => esc_html__( 'The first time presets are saved, settings will be migrated from the retired Module Customizer. This may result in slight text size changes in 1/3 and 1/4 columns and some slight padding changes in some modules.' ),
				'presets'          => array(
					'createNewPreset' => esc_html__( 'Create New Preset From Current Styles', 'et_builder' ),
					'editPresets'     => esc_html__( 'Manage Presets', 'et_builder' ),
					'addNewPreset'    => esc_html__( 'Add New Preset', 'et_builder' ),
				),
			),
			'applyGlobalColor'     => esc_html__( 'You\'ve made changes to this global color. This will affect all instances of this global color across your entire site. Do you wish to proceed?', 'et_builder' ),
			'deleteGlobalColor'    => esc_html__( 'You\'re deleting a global color, which will no longer be available across your website, however, instances of this global color will not be affected. Do you wish to proceed?', 'et_builder' ),
			'finishSavedEdit'      => esc_html__( 'Finish Editing Saved Colors', 'et_builder' ),
			'finishGlobalEdit'     => esc_html__( 'Finish Editing Global Colors', 'et_builder' ),
			'portabilityTabs'      => array(
				'import' => array(
					'replaceLayout'        => esc_html__( 'Replace Existing Content', 'et_builder' ),
					'importBackUp'         => esc_html__( 'Download Backup Before Importing', 'et_builder' ),
					'addToLibrary'         => esc_html__( 'Add To Library', 'et_builder' ),
					'includeGlobalPresets' => esc_html__( 'Import Presets', 'et_builder' ),
					'imported'             => esc_html__( 'imported', 'et_builder' ),
					'ImportToCloud'        => esc_html__( 'Import To Cloud', 'et_builder' ),
					'selectedFiles'        => esc_html__( 'Selected Files:', 'et_builder' ),
					'drageFiles'           => esc_html__( 'Drag Files Here', 'et_builder' ),
					'selectFiles'          => esc_html__( 'Select Files To Import', 'et_builder' ),
				),
				'export' => array(
					'applyGlobalPresets' => esc_html__( 'Apply Presets To Exported Layout' ),
				),
			),
			'favoritesAdd'         => esc_html__( 'Add To Favorites', 'et_builder' ),
			'favoritesRemove'      => esc_html__( 'Remove From Favorites', 'et_builder' ),
		),
		'prompts'                   => array(
			'importWithLabel'        => esc_html__( 'Import Design Presets?', 'et_builder' ),
			'importWithContent'      => esc_html__( 'This layout contains global design presets. Check the box below to import these styles as presets, or leave it unchecked to bring them in as static styles.', 'et_builder' ),
			'import'                 => esc_html__( 'Import', 'et_builder' ),
			'importPresets'          => esc_html__( 'Import Presets', 'et_builder' ),
			'close'                  => esc_html__( 'Close', 'et_builder' ),
			'convertRowtoSection'    => esc_html__( 'Convert Row To Section', 'et_builder' ),
			'convertModuletoRow'     => esc_html__( 'Convert Module To Row', 'et_builder' ),
			'convertModuletoSection' => esc_html__( 'Convert Module To Section', 'et_builder' ),
			'convertToRowMsg'        => esc_html__( 'Conversion completed. Item moved to the Rows library.', 'et_builder' ),
			'convertToSectionMsg'    => esc_html__( 'Conversion completed. Item moved to the Sections library.', 'et_builder' ),
		),
		'saveModuleLibraryAttrs'    => array(
			'cancel'                 => et_builder_i18n( 'Cancel' ),
			'general'                => esc_html__( 'Include General Settings', 'et_builder' ),
			'advanced'               => esc_html__( 'Include Advanced Design Settings', 'et_builder' ),
			'css'                    => esc_html__( 'Include Custom CSS', 'et_builder' ),
			'selectCategoriesText'   => esc_html__( 'Select category(ies) for new template or type a new name ( optional )', 'et_builder' ),
			'template_name'          => esc_html__( 'Layout Name', 'et_builder' ),
			'sectionLabel'           => esc_html__( 'Section Name', 'et_builder' ),
			'rowLabel'               => esc_html__( 'Row Name', 'et_builder' ),
			'moduleLabel'            => esc_html__( 'Module Name', 'et_builder' ),
			'selectiveError'         => esc_html__( 'Please select at least 1 tab to save', 'et_builder' ),
			'globalTitle'            => esc_html__( 'Save as Global', 'et_builder' ),
			'cloudTitle'             => esc_html__( 'Save To Divi Cloud', 'et_builder' ),
			'cloudSavingTitle'       => esc_html__( 'Saving To Divi Cloud', 'et_builder' ),
			'globalText'             => esc_html__( 'Make this a global item', 'et_builder' ),
			'createCatText'          => esc_html__( 'Create New Category/Categories', 'et_builder' ),
			'createTagText'          => esc_html__( 'Create New Tag(s)', 'et_builder' ),
			'addToCatText'           => esc_html__( 'Add To Categories', 'et_builder' ),
			'addToTagText'           => esc_html__( 'Add To Tags', 'et_builder' ),
			'descriptionText'        => esc_html__( 'Here you can add the current item to your Divi Library for later use.', 'et_builder' ),
			'descriptionTextLayout'  => esc_html__( 'Save your current page to the Divi Library for later use.', 'et_builder' ),
			'descriptionSectionText' => esc_html__( 'Save your current section to the Divi Library for later use.', 'et_builder' ),
			'descriptionRowText'     => esc_html__( 'Save your current row to the Divi Library for later use.', 'et_builder' ),
			'descriptionModuleText'  => esc_html__( 'Save your current module to the Divi Library for later use.', 'et_builder' ),
			'saveText'               => esc_html__( 'Save To Library', 'et_builder' ),
			'saveToCloudText'        => esc_html__( 'Save To Divi Cloud', 'et_builder' ),
			'allCategoriesText'      => esc_html__( 'All Categories', 'et_builder' ),
			'saveIndividual'         => esc_html__( 'Save Individual Items', 'et_builder' ),
			'saveSectionsIndividual' => esc_html__( 'Also Save All %s Sections as Individual Items', 'et_builder' ),
			'saveRowsIndividual'     => esc_html__( 'Also Save All %s Rows as Individual Items', 'et_builder' ),
			'saveModulesIndividual'  => esc_html__( 'Also Save All %s Modules as Individual Items', 'et_builder' ),
		),
		'alertModal'                => array(
			'buttonCancelLabel'  => et_builder_i18n( 'Cancel' ),
			'buttonProceedLabel' => esc_html__( 'Proceed', 'et_builder' ),
		),
		'modals'                    => array(
			'defaultTitle'          => esc_html__( 'Modal Title', 'et_builder' ),
			'tabItemTitles'         => array(
				'general' => esc_html__( 'General', 'et_builder' ),
				'design'  => et_builder_i18n( 'Design' ),
				'css'     => esc_html__( 'CSS', 'et_builder' ),
			),
			'moduleSettings'        => array(
				// Translators: %s: module name.
				'title'     => esc_html__( '%s Settings', 'et_builder' ),
				'noSupport' => esc_html__( 'This option is not previewable in the Visual Builder. It will only take effect once you exit the Visual Builder', 'et_builder' ),
			),
			'pageSettings'          => array(
				'tabs' => ET_Builder_Settings::get_tabs(),
			),
			'searchOptions'         => esc_html__( 'Search Options', 'et_builder' ),
			'searchIcons'           => esc_html__( 'Search Icons', 'et_builder' ),
			'filter'                => esc_html__( 'Filter', 'et_builder' ),
			'show_only'             => esc_html__( 'Show Only', 'et_builder' ),
			'filterNotice'          => esc_html__( 'No options exist for this search query. <span>Click here</span> to clear your search filters.', 'et_builder' ),
			'filterNoticeClickable' => esc_html__( 'Click here', 'et_builder' ),
			'exploreDiviModules'    => esc_html__( 'Explore More Divi Modules', 'et_builder' ),
			'extend_styles'         => array(
				'title'   => esc_html__( 'Extend Styles', 'et_builder' ),
				'button'  => esc_html__( 'Extend', 'et_builder' ),
				'options' => array(
					'to'         => array(
						'containers' => array(),
						'modules'    => array(
							'module' => esc_html__( 'All Modules', 'et_builder' ),
						),
					),
					'throughout' => array(
						'page'             => esc_html__( 'Header, Footer & Page', 'et_builder' ),
						'et_header_layout' => esc_html__( 'Header Template', 'et_builder' ),
						'et_body_layout'   => esc_html__( 'Body Template', 'et_builder' ),
						'et_footer_layout' => esc_html__( 'Footer Template', 'et_builder' ),
						'post_content'     => esc_html__( 'This Page', 'et_builder' ),
						'section'          => esc_html__( 'This Section', 'et_builder' ),
						'row'              => esc_html__( 'This Row', 'et_builder' ),
						'column'           => esc_html__( 'This Column', 'et_builder' ),
					),
				),
				'groups'  => array(
					'to' => array(
						'containers' => esc_html__( 'Containers', 'et_builder' ),
						'modules'    => esc_html__( 'Modules', 'et_builder' ),
					),
				),
				'labels'  => array(
					'to'                   => esc_html__( 'To', 'et_builder' ),
					'throughout'           => esc_html__( 'Throughout', 'et_builder' ),
					// translators: %s is Plural Module Name.
					'all'                  => esc_html__( 'All %s', 'et_builder' ),
					// translators: %1$s is Module Name, %2$s is Plural Module Name.
					'extend_module'        => esc_html__( 'Extend This %1$s\'s Styles To All %2$s', 'et_builder' ),
					// translators: %s is Options Group Name.
					'extend_options_group' => esc_html__( 'Extend This %s\'s Styles', 'et_builder' ),
					// translators: %s is option Field Name.
					'extend_option'        => esc_html__( 'Extend This %s', 'et_builder' ),
				),
			),
			'globalPresets'         => array(
				'title'    => esc_html__( '%s Presets', 'et_builder' ),
				'defaults' => esc_html__( 'Defaults', 'et_builder' ),
				'presets'  => array(
					'preset'                => esc_html__( 'Preset', 'et_builder' ),
					'moduleNamePreset'      => esc_html__( '%s Preset', 'et_builder' ),
					'default'               => esc_html__( 'Default', 'et_builder' ),
					'defaultPreset'         => esc_html__( 'Default Preset', 'et_builder' ),
					'basedOn'               => esc_html__( 'Based On', 'et_builder' ),
					'presetSettings'        => esc_html__( 'Preset Settings', 'et_builder' ),
					'presetName'            => esc_html__( 'Preset Name', 'et_builder' ),
					'assignPresetToDefault' => array(
						'title'   => esc_html__( 'Assign Preset To Default', 'et_builder' ),
						'options' => array(
							'on'  => esc_html__( 'Yes', 'et_builder' ),
							'off' => esc_html__( 'No', 'et_builder' ),
						),
					),
				),
			),
			'layersView'            => array(
				'column'                => array(
					'settings'  => esc_html__( 'Column Settings', 'et_builder' ),
					'duplicate' => esc_html__( 'Duplicate Column', 'et_builder' ),
					'delete'    => esc_html__( 'Delete Column', 'et_builder' ),
				),
				'title'                 => esc_html__( 'Layers', 'et_builder' ),
				'toggleAll'             => esc_html__( 'Open/Close All', 'et_builder' ),
				'toggleCollapse'        => esc_html__( 'Collapse', 'et_builder' ),
				'toggleExpand'          => et_builder_i18n( 'Expand' ),
				'searchLayers'          => esc_html__( 'Search Layers', 'et_builder' ),
				'searchFilterItems'     => array(
					'show_only' => array(
						'section' => esc_html__( 'Sections', 'et_builder' ),
						'row'     => esc_html__( 'Rows', 'et_builder' ),
						'column'  => esc_html__( 'Columns', 'et_builder' ),
						'module'  => esc_html__( 'Modules', 'et_builder' ),
						'global'  => esc_html__( 'Global Elements', 'et_builder' ),
					),
				),
				'filterNotice'          => esc_html__( 'No layers exist for this search query. Click here to clear your search filters.', 'et_builder' ),
				'filterNoticeClickable' => esc_html__( 'Click here', 'et_builder' ),
			),
			'moduleElements'        => array(
				'part1' => esc_html__( 'Target predefined selectors within this element without the need to write out selectors name. i.e.', 'et_builder' ),
				'part2' => esc_html__( 'instead of', 'et_builder' ),
			),
			'freeFormCSS'           => array(
				'part1' => esc_html__( 'Write free-form css using the keyword', 'et_builder' ),
				'part2' => esc_html__( 'to target this module i.e.', 'et_builder' ),
			),
			'insertLayout'          => [
				'title'             => esc_html__( 'Insert Layout', 'et_builder' ),
				'Premade Layout'    => esc_html__( 'Premade Layout', 'et_builder' ),
				'Saved Layout'      => esc_html__( 'Saved Layout', 'et_builder' ),
				'Build With AI'     => esc_html__( 'Build With AI', 'et_builder' ),
				'Brand New'         => esc_html__( 'Brand New', 'et_builder' ),
				'premadeLayoutDesc' => esc_html__( 'Select from thousands of premade layouts designed for every type of site', 'et_builder' ),
				'savedLayoutDesc'   => esc_html__( 'Start from a layout in your Divi Library or clone an existing page', 'et_builder' ),
				'buildWithAIDesc'   => esc_html__( 'Simply describe your page, sit back, relax and let Divi AI build it for you', 'et_builder' ),
			],
		),
		'selectControl'             => array(
			'typeToSearch' => esc_html__( 'Start Typing', 'et_builder' ),
			'subgroups'    => array(
				'recent'              => esc_html__( 'Recent', 'et_builder' ),
				'uploaded'            => esc_html__( 'Custom Fonts', 'et_builder' ),
				'global'              => esc_html__( 'Global Fonts', 'et_builder' ),
				'global_font_weights' => esc_html__( 'Global Font Weights', 'et_builder' ),
			),
			'noResults'    => esc_html__( 'No results found', 'et_builder' ),
			'noTitle'      => esc_html__( '(no title)', 'et_builder' ),
			// Translators: Used for pagination: %1$s = current page; %2$s = total pages.
			'pagination'   => esc_html__( '%1$s of %2$s', 'et_builder' ),
		),
		'history'                   => array(
			'modal'    => array(
				'title' => esc_html__( 'Editing History', 'et_builder' ),
				'tabs'  => array(
					'states'              => esc_html__( 'History States', 'et_builder' ),
					'globalHistoryStates' => esc_html__( 'Global History States', 'et_builder' ),
				),
			),
			'meta'     => et_pb_history_localization(),
			'elements' => et_builder_i18n( 'Elements' ),
		),
		'findReplace'               => array(
			'modal' => array(
				'title'       => esc_html__( 'Find & Replace', 'et_builder' ),
				'tooltip'     => esc_html__( 'Replace', 'et_builder' ),
				'find'        => array(
					'label'       => esc_html__( 'Find This %s', 'et_builder' ),
					'description' => esc_html__( 'This is the option value that will be replaced throughout your page. Where this option exists, within the defined scope, it will be replaced by the new value configured below.', 'et_builder' ),
				),
				'within'      => array(
					'label'       => esc_html__( 'Within', 'et_builder' ),
					'description' => esc_html__( 'The value will only be replaced within the confines of the area selected here. You can replace the value across your entire page, or you can replace the value only within specific parts of your page or within specific modules.', 'et_builder' ),
				),
				'throughout'  => array(
					'label'       => esc_html__( 'Throughout', 'et_builder' ),
					'description' => esc_html__( 'The value will only be replaced inside of modules that exist within the area selected here. You can replace the value across your entire page, or you can replace the value only within specific parts of your page.', 'et_builder' ),
				),
				'replaceWith' => array(
					'label'       => esc_html__( 'Replace With', 'et_builder' ),
					'description' => esc_html__( 'When the value above is found within your desired area, it will be replaced with the value that you choose here.', 'et_builder' ),
				),
				'replaceAll'  => array(
					'label'       => esc_html__( 'Replace All', 'et_builder' ),
					'description' => esc_html__( 'By default, values will only be replaced when found within the exact option type selected. If you enable this checkbox, the search will be extended to all options and values will be replaced everywhere. For example, a color will be replaced in all colors options: Text Colors, Background Colors, Border Colors, etc.', 'et_builder' ),
				),
				'error'       => array(
					'field_type_not_match'    => esc_html__( 'Field type is not match', 'et_builder' ),
					'field_name_not_match'    => esc_html__( 'Field name is not match', 'et_builder' ),
					'replace_value_not_valid' => esc_html__( 'Replace value is not valid', 'et_builder' ),
					'replace_value_not_match' => esc_html__( 'Replace value is not match', 'et_builder' ),
				),
			),
		),
		'replaceGlobalColor'        => array(
			'modal' => array(
				'title'       => esc_html__( 'Replace Global Color', 'et_builder' ),
				'description' => esc_html__( 'This global color will be deleted, and all instances across your site will be replaced with another global color of your choice.', 'et_builder' ),
				'tooltip'     => esc_html__( 'Replace', 'et_builder' ),
				'find'        => array(
					'label'       => esc_html__( 'Replace', 'et_builder' ),
					'description' => esc_html__( 'This is the option value that will be replaced throughout your page. Where this option exists, within the defined scope, it will be replaced by the new value configured below.', 'et_builder' ),
				),
				'replaceWith' => array(
					'label'       => esc_html__( 'With', 'et_builder' ),
					'description' => esc_html__( 'When the value above is found within your desired area, it will be replaced with the value that you choose here.', 'et_builder' ),
				),
				'error'       => array(
					'field_type_not_match'    => esc_html__( 'Field type is not match', 'et_builder' ),
					'field_name_not_match'    => esc_html__( 'Field name is not match', 'et_builder' ),
					'replace_value_not_valid' => esc_html__( 'Replace value is not valid', 'et_builder' ),
					'replace_value_not_match' => esc_html__( 'Replace value is not match', 'et_builder' ),
				),
			),
		),
		'help'                      => array(
			'modal'     => array(
				'title' => esc_html__( 'Divi Builder Helper', 'et_builder' ),
				'tabs'  => array(
					'gettingStarted' => esc_html__( 'Video Tutorials', 'et_builder' ),
					'shortcut'       => esc_html__( 'Keyboard Shortcuts', 'et_builder' ),
				),
			),
			'shortcuts' => et_builder_get_shortcuts( 'fb' ),
			'button'    => esc_html__( 'Help', 'et_builder' ),
		),
		'abTesting'                 => array_merge(
			et_builder_ab_labels(),
			array(
				'reportTitle'          => esc_html__( 'Split Testing Statistics', 'et_builder' ),
				'reportTabNavs'        => array(
					'clicks'                => esc_html__( 'Clicks', 'et_builder' ),
					'reads'                 => esc_html__( 'Reads', 'et_builder' ),
					'bounces'               => esc_html__( 'Bounces', 'et_builder' ),
					'engagements'           => esc_html__( 'Goal Engagement', 'et_builder' ),
					'conversions'           => esc_html__( 'Conversions', 'et_builder' ),
					'shortcode_conversions' => esc_html__( 'Shortcode Conversions', 'et_builder' ),
				),
				'reportFilterTime'     => array(
					'day'   => esc_html__( 'Last 24 Hours', 'et_builder' ),
					'week'  => esc_html__( 'Last 7 Days', 'et_builder' ),
					'month' => esc_html__( 'Last Month', 'et_builder' ),
					'all'   => esc_html__( 'All Time', 'et_builder' ),
				),
				'reportTotal'          => esc_html__( 'Total', 'et_builder' ),
				'reportSummaryTitle'   => esc_html__( 'Summary & Data', 'et_builder' ),
				'reportRefreshTooltip' => esc_html__( 'Refresh Split Test Data', 'et_builder' ),
				'reportEndTestButton'  => esc_html__( 'End Split Test & Pick Winner', 'et_builder' ),
			)
		),
		'fonts'                     => array(
			'fontWeight'        => esc_html__( 'Font Weight', 'et_builder' ),
			'fontStyle'         => esc_html__( 'Font Style', 'et_builder' ),
			'delete'            => esc_html__( 'Delete', 'et_builder' ),
			'deleteConfirm'     => esc_html__( 'Are You Sure Want To Delete', 'et_builder' ),
			'confirmAction'     => esc_html__( 'Are You Sure?', 'et_builder' ),
			'cancel'            => et_builder_i18n( 'Cancel' ),
			'upload'            => esc_html__( 'Upload', 'et_builder' ),
			'edit_global'       => esc_html__( 'Edit Global Fonts', 'et_builder' ),
			'font'              => esc_html__( 'Font', 'et_builder' ),
			'chooseFile'        => esc_html__( 'Choose Font Files', 'et_builder' ),
			'supportedFiles'    => esc_html__( 'Supported File Formats', 'et_builder' ),
			'fileError'         => esc_html__( 'Unsupported File Format', 'et_builder' ),
			'noFile'            => esc_html__( 'Drag Files Here', 'et_builder' ),
			'fontName'          => esc_html__( 'Name Your Font', 'et_builder' ),
			'fontNameLabel'     => esc_html__( 'Font Name', 'et_builder' ),
			'selectedFiles'     => esc_html__( 'Selected Font Files', 'et_builder' ),
			'weightsSupport'    => esc_html__( 'Supported Font Weights', 'et_builder' ),
			'weightsHelp'       => esc_html__( 'Choose the font weights supported by your font. Select "All" if you don\'t know this information or if your font includes all weights.', 'et_builder' ),
			'noFilesError'      => esc_html__( 'Please Select At Least One File', 'et_builder' ),
			'searchFonts'       => esc_html__( 'Search Fonts', 'et_builder' ),
			'underline'         => esc_html__( 'Underline', 'et_builder' ),
			'strikethrough'     => esc_html__( 'Strikethrough', 'et_builder' ),
			'color'             => et_builder_i18n( 'Color' ),
			'style'             => esc_html__( 'Style', 'et_builder' ),
			'all'               => esc_html__( 'All', 'et_builder' ),
			'Headings'          => esc_html__( 'Headings', 'et_builder' ),
			'Body'              => esc_html__( 'Body', 'et_builder' ),
			'Save'              => esc_html__( 'Save', 'et_builder' ),
			'Edit Global Fonts' => esc_html__( 'Edit Global Fonts', 'et_builder' ),
			'Headings Font'     => esc_html__( 'Headings Font', 'et_builder' ),
			'Body Font'         => esc_html__( 'Body Font', 'et_builder' ),
		),

		// Drag and Droploader
		'droploader'                => array(
			'title'              => esc_html__( 'Drop Files Here To Upload', 'et_builder' ),
			'description'        => esc_html__( 'Drop %s files here to automatically generate website content', 'et_builder' ),
			'allowed_extensions' => esc_html__( 'Only the following file formats are allowed: %s', 'et_builder' ),
			'errors'             => array(
				'uploadFailed'          => array(
					'title'             => esc_html__( 'Upload Failed', 'et_builder' ),
					'buttonCancelLabel' => esc_html__( 'Close', 'et_builder' ),
				),
				'file_name_empty'       => esc_html__( 'Uploaded file name is empty', 'et_builder' ),
				'file_size_empty'       => esc_html__( 'Uploaded file size is empty: %s', 'et_builder' ),
				'file_type_empty'       => esc_html__( 'Uploaded file type is empty: %s', 'et_builder' ),
				'file_extension_empty'  => esc_html__( 'Uploaded file extension is empty: %s', 'et_builder' ),
				'file_type_not_allowed' => esc_html__( 'Uploaded file type is not allowed: %s', 'et_builder' ),
				'file_type_unknown'     => esc_html__( 'Uploaded file type is unknown', 'et_builder' ),
				'file_content_invalid'  => esc_html__( 'Uploaded file content is invalid: %s', 'et_builder' ),
				'file_untrusted'        => esc_html__( 'File is untrusted: %s', 'et_builder' ),
				'action_not_allowed'    => esc_html__( 'You are not allowed to perform this action', 'et_builder' ),
			),
			'fileTypes'          => array(
				'names' => array(
					'audio' => esc_html__( 'Audio', 'et_builder' ),
					'html'  => esc_html__( 'HTML', 'et_builder' ),
					'css'   => esc_html__( 'CSS', 'et_builder' ),
					'font'  => esc_html__( 'Font', 'et_builder' ),
					'image' => et_builder_i18n( 'Image' ),
					'json'  => esc_html__( 'JSON', 'et_builder' ),
					'text'  => et_builder_i18n( 'Text' ),
					'video' => esc_html__( 'Video', 'et_builder' ),
				),
			),
		),

		'app'                       => array(
			'modal' => array(
				'title'                       => esc_html__( 'Builder Settings', 'et_builder' ),
				'labels'                      => array(
					'toolbar'              => esc_html__( 'Customize Builder Settings Toolbar', 'et_builder' ),
					'interaction_mode'     => esc_html__( 'Builder Default Interaction Mode', 'et_builder' ),
					'history'              => esc_html__( 'History State Interval', 'et_builder' ),
					'modal_position'       => esc_html__( 'Settings Modal Default Position', 'et_builder' ),
					'animation'            => esc_html__( 'Builder Interface Animations', 'et_builder' ),
					'disabled_modules'     => esc_html__( 'Show Disabled Modules At 50% Opacity', 'et_builder' ),
					'group_settings'       => esc_html__( 'Group Settings Into Closed Toggles', 'et_builder' ),
					'dummy_content'        => esc_html__( 'Add Placeholder Content To New Modules', 'et_builder' ),
					'view_mode'            => esc_html__( 'Builder Default View Mode', 'et_builder' ),
					'page_creation_flow'   => esc_html__( 'Page Creation Flow', 'et_builder' ),
					'visual_theme_builder' => esc_html__( 'Theme Builder Template Editing', 'et_builder' ),
				),
				'view_mode_select'            => array(
					'desktop'   => $app_preferences['view_mode']['options']['desktop'],
					'tablet'    => $app_preferences['view_mode']['options']['tablet'],
					'phone'     => $app_preferences['view_mode']['options']['phone'],
					'wireframe' => $app_preferences['view_mode']['options']['wireframe'],
				),
				'interaction_mode_select'     => array(
					'0' => $app_preferences['event_mode']['options']['hover'],
					'1' => $app_preferences['event_mode']['options']['click'],
					'2' => $app_preferences['event_mode']['options']['grid'],
				),
				'history_intervals_select'    => array(
					'0' => $app_preferences['history_intervals']['options']['1'],
					'1' => $app_preferences['history_intervals']['options']['10'],
					'2' => $app_preferences['history_intervals']['options']['20'],
					'3' => $app_preferences['history_intervals']['options']['30'],
					'4' => $app_preferences['history_intervals']['options']['40'],
				),
				'modal_default_select'        => array(
					'0' => $app_preferences['modal_preference']['options']['default'],
					'1' => $app_preferences['modal_preference']['options']['minimum'],
					'2' => $app_preferences['modal_preference']['options']['fullscreen'],
					'3' => $app_preferences['modal_preference']['options']['left'],
					'4' => $app_preferences['modal_preference']['options']['right'],
					'5' => $app_preferences['modal_preference']['options']['bottom'],
					// TODO, disabled until further notice (Issue #3930 & #5859)
					// '6' => $app_preferences['modal_preference']['options']['top'],
				),
				'builder_animation_toggle'    => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'hide_disabled_module_toggle' => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'display_modal_settings'      => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'enable_dummy_content'        => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'enable_visual_theme_builder' => array(
					'on'  => et_builder_i18n( 'On' ),
					'off' => et_builder_i18n( 'Off' ),
				),
				'page_creation_flow_select'   => et_builder_page_creation_settings( true ),
			),
		),
		'video'                     => array(
			'active'  => esc_html__( 'Video Overlay is Currently Active.', 'et_builder' ),
			'offline' => esc_html__( 'Unable To Establish Internet Connection.', 'et_builder' ),
		),

		/**
		 * Filters the builder's video tutorials.
		 * Can be used for add video tutorials for custom 3rd-party modules.
		 *
		 * @param string[] $help_videos {
		 *     Videos to module relations array
		 *
		 *     @type string[] $module_slug {
		 *          Module slugs array
		 *
		 *          @type string[] $sort_order {
		 *              Video sort order
		 *
		 *              @type string $id Youtube video ID
		 *              @type string $name Localized video title
		 *          }
		 *     }
		 * }
		 */
		'videos'                    => apply_filters( 'et_fb_help_videos', $help_videos ),

		'sortable'                  => array(
			'has_no_ab_permission'                     => esc_html__( 'You do not have permission to edit the module, row or section in this split test.', 'et_builder' ),
			'cannot_move_goal_into_subject'            => esc_html__( 'A split testing goal cannot be moved inside of a split testing subject. To perform this action you must first end your split test.', 'et_builder' ),
			'cannot_move_subject_into_goal'            => esc_html__( 'A split testing subject cannot be moved inside of a split testing goal. To perform this action you must first end your split test.', 'et_builder' ),
			'cannot_move_row_goal_out_from_subject'    => esc_html__( 'Once set, a goal that has been placed inside a split testing subject cannot be moved outside the split testing subject. You can end your split test and start a new one if you would like to make this change.', 'et_builder' ),
			'section_only_row_dragged_away'            => esc_html__( 'The section should have at least one row.', 'et_builder' ),
			'global_module_alert'                      => esc_html__( 'You cannot add global modules into global sections or rows', 'et_builder' ),
			'cannot_move_module_goal_out_from_subject' => esc_html__( 'Once set, a goal that has been placed inside a split testing subject cannot be moved outside the split testing subject. You can end your split test and start a new one if you would like to make this change.', 'et_builder' ),
			'stop_dropping_3_col_row'                  => esc_html__( 'This number of columns cannot be used on this row.', 'et_builder' ),
			'onlyColumnDraggedAway'                    => esc_html__( 'The row must have at least one column.', 'et_builder' ),
		),
		'tooltip'                   => array(
			'pageSettingsBar' => array(
				'responsive' => array(
					'wireframe' => esc_html__( 'Wireframe View', 'et_builder' ),
					'zoom'      => esc_html__( 'Zoom Out', 'et_builder' ),
					'desktop'   => esc_html__( 'Desktop View', 'et_builder' ),
					'tablet'    => esc_html__( 'Tablet View', 'et_builder' ),
					'phone'     => esc_html__( 'Phone View', 'et_builder' ),
				),
				'eventMode'  => array(
					'click' => esc_html__( 'Click Mode', 'et_builder' ),
					'hover' => esc_html__( 'Hover Mode', 'et_builder' ),
					'grid'  => esc_html__( 'Grid Mode', 'et_builder' ),
				),
				'main'       => array(
					'loadLibrary'       => esc_html__( 'Load From Library', 'et_builder' ),
					'saveToLibrary'     => esc_html__( 'Save To Library', 'et_builder' ),
					'clearLayout'       => esc_html__( 'Clear Layout', 'et_builder' ),
					'pageSettingsModal' => esc_html__( 'Page Settings', 'et_builder' ),
					'history'           => esc_html__( 'Editing History', 'et_builder' ),
					'portability'       => esc_html__( 'Portability', 'et_builder' ),
					'open'              => esc_html__( 'Expand Settings', 'et_builder' ),
					'close'             => esc_html__( 'Collapse Settings', 'et_builder' ),
					'insertLayout'      => esc_html__( 'Insert Layout', 'et_builder' ),
				),
				'save'       => array(
					'saveDraft' => esc_html__( 'Save as Draft', 'et_builder' ),
					'save'      => esc_html__( 'Save', 'et_builder' ),
					'publish'   => esc_html__( 'Publish', 'et_builder' ),
				),
			),
			'modal'           => array(
				'expandModal'    => esc_html__( 'Expand Modal', 'et_builder' ),
				'contractModal'  => esc_html__( 'Contract Modal', 'et_builder' ),
				'resize'         => esc_html__( 'Resize Modal', 'et_builder' ),
				'snapModal'      => esc_html__( 'Snap To Left', 'et_builder' ),
				'snapModalRight' => esc_html__( 'Snap To Right', 'et_builder' ),
				'separateModal'  => esc_html__( 'Separate Modal', 'et_builder' ),
				'redo'           => esc_html__( 'Redo', 'et_builder' ),
				'undo'           => esc_html__( 'Undo', 'et_builder' ),
				'cancel'         => esc_html__( 'Discard All Changes', 'et_builder' ),
				'save'           => esc_html__( 'Save Changes', 'et_builder' ),
				'close'          => esc_html__( 'Close', 'et_builder' ),
				'rightMenu'      => esc_html__( 'Other Settings', 'et_builder' ),
				'globalPresets'  => array(
					'edit'                    => esc_html__( 'Manage %s Presets', 'et_builder' ),
					'exit'                    => esc_html__( 'Back To Module Settings', 'et_builder' ),
					'selectPreset'            => esc_html__( 'Select A Preset', 'et_builder' ),
					'activatePreset'          => esc_html__( 'Activate Preset', 'et_builder' ),
					'disablePreset'           => esc_html__( 'Disable Preset', 'et_builder' ),
					'presetSettings'          => esc_html__( 'Preset Settings', 'et_builder' ),
					'duplicatePreset'         => esc_html__( 'Duplicate Preset', 'et_builder' ),
					'deletePreset'            => esc_html__( 'Delete Preset', 'et_builder' ),
					'assignAsDefault'         => esc_html__( 'Assign Preset as Default', 'et_builder' ),
					'editPresetStyles'        => esc_html__( 'Edit Preset Styles', 'et_builder' ),
					'updateWithCurrentStyles' => esc_html__( 'Update Preset With Current Styles', 'et_builder' ),
				),
			),
			'inlineEditor'    => array(
				'back'             => esc_html__( 'Go Back', 'et_builder' ),
				'increaseFontSize' => esc_html__( 'Decrease Font Size', 'et_builder' ),
				'decreaseFontSize' => esc_html__( 'Increase Font Size', 'et_builder' ),
				'bold'             => esc_html__( 'Bold Text', 'et_builder' ),
				'italic'           => esc_html__( 'Italic Text', 'et_builder' ),
				'underline'        => esc_html__( 'Underline Text', 'et_builder' ),
				'link'             => esc_html__( 'Insert Link', 'et_builder' ),
				'quote'            => esc_html__( 'Insert Quote', 'et_builder' ),
				'alignment'        => esc_html__( 'Text Alignment', 'et_builder' ),
				'centerText'       => esc_html__( 'Center Text', 'et_builder' ),
				'rightText'        => esc_html__( 'Right Text', 'et_builder' ),
				'leftText'         => esc_html__( 'Left Text', 'et_builder' ),
				'justifyText'      => esc_html__( 'Justify Text', 'et_builder' ),
				'list'             => esc_html__( 'List Settings', 'et_builder' ),
				'indent'           => esc_html__( 'Indent List', 'et_builder' ),
				'undent'           => esc_html__( 'Undent List', 'et_builder' ),
				'orderedList'      => esc_html__( 'Insert Ordered List', 'et_builder' ),
				'unOrderedList'    => esc_html__( 'Insert Unordered List', 'et_builder' ),
				'text'             => esc_html__( 'Text Settings', 'et_builder' ),
				'textColor'        => esc_html__( 'Text Color', 'et_builder' ),
				'heading'          => array(
					'one'   => esc_html__( 'Insert Heading One', 'et_builder' ),
					'two'   => esc_html__( 'Insert Heading Two', 'et_builder' ),
					'three' => esc_html__( 'Insert Heading Three', 'et_builder' ),
					'four'  => esc_html__( 'Insert Heading Four', 'et_builder' ),
				),
			),
			'section'         => array(
				'tab'       => array(
					'move'         => esc_html__( 'Move Section', 'et_builder' ),
					'settings'     => esc_html__( 'Section Settings', 'et_builder' ),
					'duplicate'    => esc_html__( 'Duplicate Section', 'et_builder' ),
					'addToLibrary' => esc_html__( 'Save Section To Library', 'et_builder' ),
					'delete'       => esc_html__( 'Delete Section', 'et_builder' ),
					'exit'         => esc_html__( 'Exit Section', 'et_builder' ),
					'rightMenu'    => esc_html__( 'Other Section Settings', 'et_builder' ),
				),
				'addButton' => esc_html__( 'Add New Section', 'et_builder' ),
			),
			'row'             => array(
				'tab'             => array(
					'move'         => esc_html__( 'Move Row', 'et_builder' ),
					'settings'     => esc_html__( 'Row Settings', 'et_builder' ),
					'duplicate'    => esc_html__( 'Duplicate Row', 'et_builder' ),
					'addToLibrary' => esc_html__( 'Save Row To Library', 'et_builder' ),
					'delete'       => esc_html__( 'Delete Row', 'et_builder' ),
					'exit'         => esc_html__( 'Exit Row', 'et_builder' ),
					'update'       => esc_html__( 'Change Column Structure', 'et_builder' ),
					'rightMenu'    => esc_html__( 'Other Row Settings', 'et_builder' ),
				),
				'addButton'       => esc_html__( 'Add New Row', 'et_builder' ),
				'addColumnButton' => esc_html__( 'Add New Column', 'et_builder' ),
				'chooseColumn'    => esc_html__( 'Choose Column Structure', 'et_builder' ),
			),
			'module'          => array(
				'tab'       => array(
					'move'         => esc_html__( 'Move Module', 'et_builder' ),
					'settings'     => esc_html__( 'Module Settings', 'et_builder' ),
					'duplicate'    => esc_html__( 'Duplicate Module', 'et_builder' ),
					'addToLibrary' => esc_html__( 'Save Module To Library', 'et_builder' ),
					'delete'       => esc_html__( 'Delete Module', 'et_builder' ),
					'exit'         => esc_html__( 'Exit Module', 'et_builder' ),
					'rightMenu'    => esc_html__( 'Other Module Settings', 'et_builder' ),
				),
				'addButton' => esc_html__( 'Add New Module', 'et_builder' ),
			),
		),
		'unsavedConfirmation'       => esc_html__( 'Unsaved changes will be lost if you leave the Divi Builder at this time.', 'et_builder' ),
		'libraryLoadError'          => esc_html__( 'Error loading Library items from server. Please refresh the page and try again.', 'et_builder' ),
		'productTourText'           => array(),
		'BFBText'                   => array(
			'disableBFB' => array(
				'yes'      => et_builder_i18n( 'Yes' ),
				'title'    => esc_html__( 'Disable Builder', 'et_builder' ),
				'mainText' => esc_html__( 'All content created in the Divi Builder will be lost. Previous content will be restored. Do you wish to proceed?', 'et_builder' ),
			),
		),
		'errorBoundaries'           => array(
			'title'            => esc_html__( 'Oops! An Error Has Occurred', 'et_builder' ),
			'message'          => esc_html__( 'This content could not be displayed. Please report this error to our team so that we can fix it and then save and reload the builder.', 'et_builder' ),
			'messageNonAdmin'  => esc_html__( 'This content could not be displayed. Click the button below to save and reload the builder', 'et_builder' ),
			'buttonReport'     => esc_html__( 'Report This Error', 'et_builder' ),
			'buttonSaveReload' => esc_html__( 'Save and Reload', 'et_builder' ),
			'modal'            => array(
				'title'                   => esc_html__( 'Report An Error', 'et_builder' ),
				'noAccountMessage'        => esc_html__( 'Elegant Themes username and API key have not been configured on this site. Error reporting requires username and API key to work.', 'et_builder' ),
				'noAccountGuide'          => esc_html__( 'Click the button below, then go to Updates tab.', 'et_builder' ),
				'noAccountButtonLabel'    => esc_html__( 'Configure username and API key', 'et_builder' ),
				'consentTitle'            => esc_html__( 'Data Transfer Agreement', 'et_builder' ),
				'consentDescription'      => esc_html__( 'The following information will be sent to our team when you submit an error report. This includes the complete content of this page, a detailed error report, and basic information about your website such as which plugins you have installed, which software versions you are using and more. The full list of data transfered in this report will include the following:', 'et_builder' ),
				'consentNotes'            => esc_html__( 'Error Descripion', 'et_builder' ),
				'consentNotesDescription' => esc_html__( 'Describe what exactly you did before this error message appears on the builder. This is optional but really helpful for us to fix this issue. So the more accurate your description is, the easier for us to fix it.', 'et_builder' ),
				'consentLabel'            => esc_html__( 'I agree to transfer this data to Elegant Themes.', 'et_builder' ),
				'buttonLabel'             => esc_html__( 'Send Error Report', 'et_builder' ),
				'successMessage'          => esc_html__( 'Thank you for reporting this issue. Your report has been successfully sent.', 'et_builder' ),
				'successAutoclose'        => esc_html__( 'This message will be automatically closed in 3 seconds.', 'et_builder' ),
				'debugInfo'               => ET_Builder_Error_Report::get_debug_info(),
				'errorMessage'            => esc_html__( 'An error occurred, please try again.', 'et_builder' ),
			),
		),
		/**
		 * @todo update vbSupport['modalSupportNotices']['off'] and vbSupport['modalSupportNotices']['partial'] once the documentation page is ready
		 */
		'vbSupport'                 => array(
			'modalSupportNotices'  => array(
				'off'     => sprintf(
					esc_html__( 'This third party module is not fully compatible with the latest version of the Divi Builder. You can still edit the module, but a preview will not be rendered in the builder. You can contact the developer of the module to encourage them to update it. <a href="%1$s" target="_blank">Click here</a> for more info.', 'et_builder' ),
					'https://www.elegantthemes.com/documentation/developers/divi-module/compatibility-levels/'
				),
				'partial' => sprintf(
					esc_html__( 'This third party module is not fully compatible with the latest version of the Divi Builder. You can still edit the module, but it will take longer to update on the page. You can contact the developer of the module to encourage them to update it. <a href="%1$s" target="_blank">Click here</a> for more info.', 'et_builder' ),
					'https://www.elegantthemes.com/documentation/developers/divi-module/compatibility-levels/'
				),
			),
			'unsupportedFieldType' => esc_html__( 'The above custom field is not fully supported and has been rendered as a standard input.' ),
		),

		'dynamicContent'            => array(
			'invalidField'      => esc_html__( 'Invalid field or insufficient permissions.', 'et_builder' ),
			'manualCustomField' => esc_html__( 'Manual Custom Field Name', 'et_builder' ),
			'tooltips'          => array(
				'enable'   => esc_html__( 'Use Dynamic Content', 'et_builder' ),
				'disable'  => esc_html__( 'Remove Dynamic Content', 'et_builder' ),
				'settings' => esc_html__( 'Edit Dynamic Content', 'et_builder' ),
				'reset'    => esc_html__( 'Reset Dynamic Content', 'et_builder' ),
			),
		),

		'responsiveViews'           => array(
			'button'         => array(
				'make_default_view'  => esc_html__( 'Make Default %s View', 'et_builder' ),
				'reset_default_view' => esc_html__( 'Reset Default %s View', 'et_builder' ),
			),
			'preset_desktop' => esc_html__( 'Desktop View', 'et_builder' ),
			'preset_custom'  => esc_html__( 'Custom View', 'et_builder' ),
		),

		'ai'                        => array(
			'tooltips' => array(
				'divi_ai_options' => esc_html__( 'Divi AI Options', 'et_builder' ),
			),
			'title'          => esc_html__( 'Title', 'et_builder' ),
			'excerpt'        => esc_html__( 'Excerpt', 'et_builder' ),
			'layout_with_ai' => esc_html__( 'Layout With AI', 'et_builder' ),
			'layout'         => array(
				'notification' => array(
					'How Does It Look?'       => esc_html__( 'How Does It Look?', 'et_builder' ),
					'Would you like to save?' => esc_html__( 'Would you like to save these fonts and colors for future Divi AI layouts?', 'et_builder' ),
					'No Thanks'               => esc_html__( 'No Thanks', 'et_builder' ),
					'Yes Please!'             => esc_html__( 'Yes Please!', 'et_builder' ),
				),
			),
			'image'          => esc_html__( 'Featured Image', 'et_builder' ),
		),
	);
	// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned

	$helpers['i18n'] = array_merge(
		$helpers['i18n'],
		require ET_BUILDER_DIR . 'frontend-builder/i18n.php'
	);

	return $helpers;
}

// Used to update the content of the cached helper js file.
function et_fb_get_asset_helpers( $content, $post_type ) {
	$helpers = et_fb_get_static_backend_helpers( $post_type );
	return sprintf(
		'window.ETBuilderBackend=jQuery.extend(true,%s,window.ETBuilderBackendDynamic)',
		et_fb_remove_site_url_protocol( wp_json_encode( $helpers, ET_BUILDER_JSON_ENCODE_OPTIONS ) )
	);
}
add_filter( 'et_fb_get_asset_helpers', 'et_fb_get_asset_helpers', 10, 2 );

function et_fb_backend_helpers() {
	global $post;
	$post_type = isset( $post->post_type ) ? $post->post_type : 'post';

	if ( wp_script_is( 'et-dynamic-asset-helpers', 'enqueued' ) ) {
		// New loading method
		$custom_post_id  = isset( $_GET['custom_page_id'] ) ? $_GET['custom_page_id'] : false;
		$current_post_id = isset( $post->ID ) ? $post->ID : false;
		$post_id         = $custom_post_id ? $custom_post_id : $current_post_id;
		$layout_type     = '';

		if ( 'et_pb_layout' === $post_type ) {
			$layout_type = et_fb_get_layout_type( $post_id );
		}

		// Include in the page the dynamic helpers and the shortcode_object
		$helpers = array_merge(
			et_fb_get_dynamic_backend_helpers(),
			et_fb_get_builder_shortcode_object( $post_type, $post_id, $layout_type )
		);

		$helpers['cachedAssets'] = true;

		/**
		 * Filters backend data passed to the Visual Builder.
		 *
		 * @since 3.28
		 *
		 * @param array $helpers
		 */
		$helpers = apply_filters( 'et_fb_backend_helpers', $helpers );
		// Pass dynamic helpers via localization.
		wp_localize_script( 'et-dynamic-asset-helpers', 'ETBuilderBackendDynamic', $helpers );
	} else {
		// Old loading method
		// Include in the page all helpers
		$helpers = array_merge_recursive(
			et_fb_get_static_backend_helpers( $post_type ),
			et_fb_get_dynamic_backend_helpers()
		);

		$helpers['cachedAssets'] = false;
		// Pass all helpers via localization.
		wp_localize_script( 'et-frontend-builder', 'ETBuilderBackend', $helpers );
	}
}

if ( ! function_exists( 'et_fb_fix_plugin_conflicts' ) ) :
	/**
	 * Disabled Autoptimize plugin on Front-end Builder page.
	 *
	 * @return void
	 **/
	function et_fb_fix_plugin_conflicts() {
		// Disable Autoptimize plugin.
		remove_action( 'init', 'autoptimize_start_buffering', -1 );
		remove_action( 'template_redirect', 'autoptimize_start_buffering', 2 );

		// Disable WP Super Cache when loading Divi Builder.
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
	}
endif;

if ( ! function_exists( 'et_fb_get_taxonomy_terms' ) ) :
	/**
	 * Retrieves all WP taxonomies for Visual Builder
	 *
	 * @return array
	 */
	function et_fb_get_taxonomy_terms() {
		$result = array();

		$taxonomies = get_taxonomies();
		foreach ( $taxonomies as $taxonomy => $name ) {
			$terms = get_terms( $name, array( 'hide_empty' => false ) ); // phpcs:ignore Generic.CodeAnalysis.ForLoopWithTestFunctionCall.NotAllowed -- Need to get the terms for each taxonomy.
			if ( $terms ) {
				$terms_count = count( $terms );
				for ( $i = 0; $i < $terms_count; $i++ ) {
					// `count` gets updated frequently and it causes static cached helpers update.
					// Since we don't use it anywhere, we can exclude the value to avoid the issue.
					unset( $terms[ $i ]->count );
				}
				$result[ $name ] = $terms;
			}
		}

		return $result;
	}
endif;

if ( ! function_exists( 'et_fb_get_taxonomy_labels' ) ) :
	/**
	 * Retrieves all WP taxonomies labels for Visual Builder
	 *
	 * @return array
	 */
	function et_fb_get_taxonomy_labels() {
		$result = array();

		foreach ( get_taxonomies() as $tax => $name ) {
			$taxonomy = get_taxonomy( $name );
			if ( $taxonomy ) {
				$result[ $name ] = get_taxonomy_labels( $taxonomy );
			}
		}

		return $result;
	}
endif;

if ( ! function_exists( 'et_builder_get_media_buttons' ) ) :
	/**
	 * Retrieves media buttons html for rich text usage.
	 *
	 * @since 3.18
	 *
	 * @return string
	 */
	function et_builder_get_media_buttons() {
		ob_start();
		remove_action( 'media_buttons', 'media_buttons' );
		echo '<span class="et-fb-tinymce-media-buttons__spacer et-fb-tinymce-media-buttons__spacer--leading">' . esc_html__( 'Add Media', 'et_builder' ) . '</span>';
		do_action( 'media_buttons' );
		$legacy_filter = apply_filters( 'media_buttons_context', '' );
		if ( $legacy_filter ) {
			// #WP22559. Close <a> if a plugin started by closing <a> to open their own <a> tag.
			if ( 0 === stripos( trim( $legacy_filter ), '</a>' ) ) {
				$legacy_filter .= '</a>';
			}
			echo esc_html( $legacy_filter );
		}
		echo '<span class="et-fb-tinymce-media-buttons__spacer et-fb-tinymce-media-buttons__spacer--trailing"><span>' . esc_html__( 'Visual', 'et_builder' ) . '</span><span>' . et_builder_i18n( 'Text' ) . '</span></span>';
		add_action( 'media_buttons', 'media_buttons' );
		return ob_get_clean();
	}
endif;
