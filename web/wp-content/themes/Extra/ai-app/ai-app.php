<?php

if ( ! defined( 'ET_AI_SERVER_URL' ) ) {
	define( 'ET_AI_SERVER_URL', 'https://ai.elegantthemes.com/api/v1' );
}

if ( ! defined( 'ET_AI_PLACEHOLDER_LANDSCAPE_IMAGE_DATA' ) ) {
	define( 'ET_AI_PLACEHOLDER_LANDSCAPE_IMAGE_DATA', 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTA4MCIgaGVpZ2h0PSI1NDAiIHZpZXdCb3g9IjAgMCAxMDgwIDU0MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICAgIDxnIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPHBhdGggZmlsbD0iI0VCRUJFQiIgZD0iTTAgMGgxMDgwdjU0MEgweiIvPgogICAgICAgIDxwYXRoIGQ9Ik00NDUuNjQ5IDU0MGgtOTguOTk1TDE0NC42NDkgMzM3Ljk5NSAwIDQ4Mi42NDR2LTk4Ljk5NWwxMTYuMzY1LTExNi4zNjVjMTUuNjItMTUuNjIgNDAuOTQ3LTE1LjYyIDU2LjU2OCAwTDQ0NS42NSA1NDB6IiBmaWxsLW9wYWNpdHk9Ii4xIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KICAgICAgICA8Y2lyY2xlIGZpbGwtb3BhY2l0eT0iLjA1IiBmaWxsPSIjMDAwIiBjeD0iMzMxIiBjeT0iMTQ4IiByPSI3MCIvPgogICAgICAgIDxwYXRoIGQ9Ik0xMDgwIDM3OXYxMTMuMTM3TDcyOC4xNjIgMTQwLjMgMzI4LjQ2MiA1NDBIMjE1LjMyNEw2OTkuODc4IDU1LjQ0NmMxNS42Mi0xNS42MiA0MC45NDgtMTUuNjIgNTYuNTY4IDBMMTA4MCAzNzl6IiBmaWxsLW9wYWNpdHk9Ii4yIiBmaWxsPSIjMDAwIiBmaWxsLXJ1bGU9Im5vbnplcm8iLz4KICAgIDwvZz4KPC9zdmc+Cg==' );
}


class ET_AI_App {
	/**
	 * @var ET_AI_App
	 */
	private static $_instance;

	/**
	 * Get the class instance.
	 *
	 * @since ??
	 *
	 * @return ET_AI_App
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();

			self::init_hooks();
		}

		return self::$_instance;
	}

	/**
	 * Update ET Account.
	 *
	 * @since ??
	 *
	 * @return void
	 */
	public static function et_builder_update_et_account_local() {
		// Username and API saved shall be reflected in Theme Options.
		// Hence, using the same cap used in Theme Options.
		et_core_security_check( 'edit_theme_options', 'et_builder_update_et_account', 'wp_nonce' );

		$username = isset( $_POST['et_username'] ) ? sanitize_text_field( $_POST['et_username'] ) : '';
		$api_key  = isset( $_POST['et_api_key'] ) ? sanitize_text_field( $_POST['et_api_key'] ) : '';

		$result = update_site_option(
			'et_automatic_updates_options',
			[
				'username' => $username,
				'api_key'  => $api_key,
			]
		);

		if ( $result ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Update AJAX calls list.
	 *
	 * @since ??
	 *
	 * @return Array
	 */
	public static function update_ajax_calls_list() {
		return [
			'action' => array(
				'et_builder_update_et_account_local',
				'et_ai_upload_image',
				'et_ai_layout_save_defaults',
			),
		];
	}

	/**
	 * AJAX Callback: Upload thumbnail and assign it to specified post.
	 *
	 * @since 4.17.0
	 *
	 * @global $_FILES['imageFile'] File to upload.
	 * @global $_POST['postId'] Post id to set thumbnail for.
	 *
	 * @return void
	 */
	public static function et_ai_upload_image() {
		et_core_security_check( 'edit_posts', 'et_ai_upload_image', 'wp_nonce' );

		// Get image URL from POST data
		$image_url_raw = isset( $_POST['imageURL'] ) ? esc_url_raw( $_POST['imageURL'] ) : '';

		// Check if image URL is valid
		if ( $image_url_raw && '' !== $image_url_raw ) {
			// Download image and add it to Media Library
			$upload = media_sideload_image( $image_url_raw, get_the_id(), null, 'id' );

			// Check for errors while downloading image
			if ( is_wp_error( $upload ) ) {
				wp_send_json_error( [ 'message' => $upload->get_error_message() ] );
			}

			// Get attachment ID and image URL
			$attachment_id = is_wp_error( $upload ) ? 0 : $upload;
			$image_url     = get_attached_file( $attachment_id );

			// Convert image to JPG and compress with quality of 80
			$image_editor = wp_get_image_editor( $image_url );
			if ( ! is_wp_error( $image_editor ) ) {
				$image_editor->set_quality( 80 );
				$saved = $image_editor->save( null, 'image/jpeg' );

				if ( ! is_wp_error( $saved ) ) {
					wp_delete_attachment( $attachment_id, true );
					$attachment_id = wp_insert_attachment(
						[
							'post_mime_type' => 'image/jpeg',
							'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $saved['path'] ) ),
							'post_content'   => '',
							'post_status'    => 'inherit',
						],
						$saved['path']
					);
					wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $saved['path'] ) );
				}
			}

			// Send success response with attachment ID and URL
			wp_send_json_success(
				[
					'localImageID'  => $attachment_id,
					'localImageURL' => wp_get_attachment_url( $attachment_id ),
				]
			);
		}
	}

	/**
	 * AJAX Callback: Delete images.
	 *
	 * @since ??
	 *
	 * @return void
	 */
	public static function et_ai_delete_images() {
		et_core_security_check( 'manage_options', 'et_ai_delete_images', 'wp_nonce' );

		$image_ids = isset( $_POST['image_ids'] ) ? array_map( 'intval', $_POST['image_ids'] ) : [];

		if ( ! empty( $image_ids ) ) {
			foreach ( $image_ids as $image_id ) {
				wp_delete_attachment( $image_id, true );
			}
		}
	}

	/**
	 * Initialize hooks.
	 *
	 * @since ??
	 *
	 * @return void
	 */
	public static function init_hooks() {
		add_filter( 'et_builder_load_requests', [ 'ET_AI_App', 'update_ajax_calls_list' ] );

		add_action( 'wp_ajax_et_builder_update_et_account_local', [ 'ET_AI_App', 'et_builder_update_et_account_local' ] );
		add_action( 'wp_ajax_et_ai_upload_image', [ 'ET_AI_App', 'et_ai_upload_image' ] );
		add_action( 'wp_ajax_et_ai_layout_save_defaults', [ 'ET_AI_App', 'et_ai_layout_save_defaults' ] );
		add_action( 'wp_ajax_et_ai_delete_images', [ 'ET_AI_App', 'et_ai_delete_images' ] );
	}

	/**
	 * AJAX Callback: Save layout prompt fonts and colors settings.
	 *
	 * @since ??
	 *
	 * @return void
	 */
	public static function et_ai_layout_save_defaults() {
		et_core_security_check( 'manage_options', 'et_ai_layout_save_defaults', 'wp_nonce' );

		$options = [
			'heading_font'       => 'et_ai_layout_heading_font',
			'body_font'          => 'et_ai_layout_body_font',
			'heading_font_color' => 'et_ai_layout_heading_font_color',
			'body_font_color'    => 'et_ai_layout_body_font_color',
			'primary_color'      => 'et_ai_layout_primary_color',
			'secondary_color'    => 'et_ai_layout_secondary_color',
			'site_description'   => 'et_ai_layout_site_description',
		];

		foreach ( $options as $post_key => $option_name ) {
			if ( isset( $_POST[ $post_key ] ) ) {
				et_update_option( $option_name, sanitize_text_field( $_POST[ $post_key ] ) );
			}
		}

		wp_send_json_success();
	}

	/**
	 * Gets the available languages.
	 *
	 * @return array Available languages.
	 */
	public static function get_available_languages() {
		$translations        = get_site_transient( 'available_translations' );
		$available_languages = [];

		if ( ! $translations ) {
			/** Load WordPress Translation Install API */
			require_once ABSPATH . 'wp-admin/includes/translation-install.php';

			$translations = wp_get_available_translations();
		}

		foreach ( $translations as $translation => $translation_data ) {
			if ( ! isset( $translation_data['english_name'] ) ) {
				continue;
			}

			$english_name = $translation_data['english_name'];

			$available_languages[ $english_name ] = $english_name;
		}

		return $available_languages;
	}

	/**
	 * Gets the available fonts.
	 *
	 * @return array Available fonts.
	 */
	public static function get_available_fonts() {
		$user_fonts   = et_builder_get_custom_fonts();
		$ai_app_fonts = et_builder_get_fonts(
			array(
				'prepend_standard_fonts' => false,
			)
		);

		// combine google fonts with custom user fonts.
		$ai_app_fonts = array_merge( $user_fonts, $ai_app_fonts );

		$et_domain_fonts = array(
			'ru_RU' => 'cyrillic',
			'uk'    => 'cyrillic',
			'bg_BG' => 'cyrillic',
			'vi'    => 'vietnamese',
			'el'    => 'greek',
			'ar'    => 'arabic',
			'he_IL' => 'hebrew',
			'th'    => 'thai',
			'si_lk' => 'sinhala',
			'bn_bd' => 'bengali',
			'ta_lk' => 'tamil',
			'te'    => 'telegu',
			'km'    => 'khmer',
			'kn'    => 'kannada',
			'ml_in' => 'malayalam',
			'ja'    => 'japanese',
			'ko_KR' => 'korean',
			'ml_IN' => 'malayalam',
			'zh_CN' => 'chinese-simplified',
		);

		$font_choices          = array();
		$site_domain           = get_locale();
		$removed_fonts_mapping = et_builder_old_fonts_mapping();

		foreach ( $ai_app_fonts as $font_name => $font_properties ) {
			$use_parent_font = false;

			if ( isset( $removed_fonts_mapping[ $font_name ] ) ) {
				$parent_font                      = $removed_fonts_mapping[ $font_name ]['parent_font'];
				$font_properties['character_set'] = $ai_app_fonts[ $parent_font ]['character_set'];
				$use_parent_font                  = true;
			}

			if (
				'' !== $site_domain &&
				isset( $et_domain_fonts[ $site_domain ] ) &&
				isset( $font_properties['character_set'] ) &&
				false === strpos( $font_properties['character_set'], $et_domain_fonts[ $site_domain ] )
			) {
				continue;
			}

			$font_choices[ $font_name ] = $font_name;
		}

		return $font_choices;
	}

	/**
	 * Gets the language name in English.
	 *
	 * @return string Language name in English. Otherwise the locale.
	 */
	public static function get_language_english_name() {
		$current_locale = get_locale();
		$translations   = get_site_transient( 'available_translations' );

		if ( ! $translations ) {
			/** Load WordPress Translation Install API */
			require_once ABSPATH . 'wp-admin/includes/translation-install.php';

			$translations = wp_get_available_translations();
		}

		// Output the language name.
		return isset( $translations[ $current_locale ]['english_name'] )
			? $translations[ $current_locale ]['english_name']
			: $current_locale;
	}

	/**
	 * ET_AI_App helpers.
	 *
	 * @since ??
	 */
	public static function get_ai_app_helpers() {
		if ( ! defined( 'ET_AI_PLUGIN_DIR' ) ) {
			define( 'ET_AI_PLUGIN_DIR', get_template_directory() . '/ai-app' );
		}

		$ai_default_heading_font    = et_get_option( 'et_ai_layout_heading_font', '' );
		$ai_default_body_font       = et_get_option( 'et_ai_layout_body_font', '' );
		$heading_font_color         = et_get_option( 'et_ai_layout_heading_font_color', '' );
		$body_font_color            = et_get_option( 'et_ai_layout_body_font_color', '' );
		$primary_color              = et_get_option( 'et_ai_layout_primary_color', '' );
		$secondary_color            = et_get_option( 'et_ai_layout_secondary_color', '' );
		$default_site_description   = wp_unslash( et_get_option( 'et_ai_layout_site_description' ) );
		$website_body_font_color    = et_get_option( 'font_color', '' );
		$website_heading_font_color = et_get_option( 'header_color', '' );
		$website_body_font          = et_get_option( 'body_font', '' );
		$website_heading_font       = et_get_option( 'heading_font', '' );

		$attributes = array(
			'i18n'                       => [
				'userPrompt'    => require ET_AI_PLUGIN_DIR . '/i18n/user-prompt.php',
				'authorization' => require ET_AI_PLUGIN_DIR . '/i18n/authorization.php',
				'aiCode'        => require ET_AI_PLUGIN_DIR . '/i18n/ai-code.php',
				'aiImageEditor' => require ET_AI_PLUGIN_DIR . '/i18n/ai-image-editor.php',
			],
			'ajaxurl'                    => is_ssl() ? admin_url( 'admin-ajax.php' ) : admin_url( 'admin-ajax.php', 'http' ),
			'nonces'                     => [
				'et_builder_update_et_account' => wp_create_nonce( 'et_builder_update_et_account' ),
				'et_ai_upload_image'           => wp_create_nonce( 'et_ai_upload_image' ),
				'et_ai_layout_save_defaults'   => wp_create_nonce( 'et_ai_layout_save_defaults' ),
				'et_ai_delete_images'          => wp_create_nonce( 'et_ai_delete_images' ),
			],
			'site_name'                  => '',
			'site_description'           => '',
			'page_name'                  => get_the_title(),
			'post_type'                  => get_post_type(),
			'site_language'              => self::get_language_english_name(),
			'available_languages'        => self::get_available_languages(),
			'available_fonts'            => self::get_available_fonts(),
			'images_uri'                 => ET_AI_PLUGIN_URI . '/images',
			'ai_server_url'              => ET_AI_SERVER_URL,
			'ai_default_heading_font'    => $ai_default_heading_font,
			'ai_default_body_font'       => $ai_default_body_font,
			'heading_font_color'         => $heading_font_color,
			'body_font_color'            => $body_font_color,
			'website_body_font_color'    => $website_body_font_color,
			'website_heading_font_color' => $website_heading_font_color,
			'website_body_font'          => $website_body_font,
			'website_heading_font'       => $website_heading_font,
			'primary_color'              => $primary_color,
			'secondary_color'            => $secondary_color,
			'default_site_desc'          => $default_site_description,
			'woocommerce_status'         => class_exists( 'WooCommerce' ) ? 'active' : 'inactive',
			'placeholder_image'          => ET_AI_PLACEHOLDER_LANDSCAPE_IMAGE_DATA,
			'product_version'            => ET_BUILDER_PRODUCT_VERSION,
		);

		$is_onboarding = is_admin() && isset( $_GET['page'] ) && 'et_onboarding' === $_GET['page'];

		if ( 'page' === get_post_type() || $is_onboarding ) {
			if ( is_multisite() ) {
				$sample_tagline = sprintf( __( 'Just another %s site' ), get_network()->site_name );
			} else {
				$sample_tagline = __( 'Just another WordPress site' );
			}
			if ( get_bloginfo( 'description' ) !== $sample_tagline ) {
				$attributes['site_description'] = get_bloginfo( 'description' );
			}
			$attributes['site_name'] = get_bloginfo( 'name' );
		}

		return $attributes;
	}

	/**
	 * Load the Cloud App scripts.
	 *
	 * @since ??
	 *
	 * @return void
	 */
	public static function load_js( $enqueue_prod_scripts = true, $skip_react_loading = false ) {
		if ( defined( 'ET_BUILDER_PLUGIN_ACTIVE' ) ) {
			if ( ! defined( 'ET_AI_PLUGIN_URI' ) ) {
				define( 'ET_AI_PLUGIN_URI', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
			}

			if ( ! defined( 'ET_AI_PLUGIN_DIR' ) ) {
				define( 'ET_AI_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			}
		} else {
			if ( ! defined( 'ET_AI_PLUGIN_URI' ) ) {
				define( 'ET_AI_PLUGIN_URI', get_template_directory_uri() . '/ai-app' );
			}

			if ( ! defined( 'ET_AI_PLUGIN_DIR' ) ) {
				define( 'ET_AI_PLUGIN_DIR', get_template_directory() . '/ai-app' );
			}
		}

		$CORE_VERSION = defined( 'ET_CORE_VERSION' ) ? ET_CORE_VERSION : '';
		$ET_DEBUG     = defined( 'ET_DEBUG' ) && ET_DEBUG;
		$DEBUG        = $ET_DEBUG;

		$home_url       = wp_parse_url( get_site_url() );
		$build_dir_uri  = ET_AI_PLUGIN_URI . '/build';
		$common_scripts = ET_COMMON_URL . '/scripts';
		$cache_buster   = $DEBUG ? mt_rand() / mt_getrandmax() : $CORE_VERSION;
		$asset_path     = ET_AI_PLUGIN_DIR . '/build/et-ai-app.bundle.js';

		if ( file_exists( $asset_path ) ) {
			wp_enqueue_style( 'et-ai-styles', "{$build_dir_uri}/et-ai-app.bundle.css", [], (string) $cache_buster );
		}

		wp_enqueue_script( 'es6-promise', "{$common_scripts}/es6-promise.auto.min.js", [], '4.2.2', true );

		$BUNDLE_DEPS = [
			'jquery',
			'react',
			'react-dom',
			'es6-promise',
		];

		if ( $DEBUG || $enqueue_prod_scripts || file_exists( $asset_path ) ) {
			$BUNDLE_URI = ! file_exists( $asset_path ) ? "{$home_url['scheme']}://{$home_url['host']}:31498/et-ai-app.bundle.js" : "{$build_dir_uri}/et-ai-app.bundle.js";

			// Skip the React loading if we already have React ( Gutenberg editor for example ) to avoid conflicts.
			if ( ! $skip_react_loading ) {
				if ( function_exists( 'et_fb_enqueue_react' ) ) {
					et_fb_enqueue_react();
				}
			}

			wp_enqueue_script( 'et-ai-app', $BUNDLE_URI, $BUNDLE_DEPS, (string) $cache_buster, true );
			wp_localize_script( 'et-ai-app', 'et_ai_data', self::get_ai_app_helpers() );
		}
	}
}

ET_AI_App::instance();
