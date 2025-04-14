<?php
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * Compatibility for the Imagify plugin.
 *
 * @since 4.4.6
 *
 * @link https://wordpress.org/plugins/imagify/
 */
class ET_Builder_Plugin_Compat_Imagify extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 4.4.6
	 */
	public function __construct() {
		$this->plugin_id = 'imagify/imagify.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 4.4.6
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'overrides_main_style' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'overrides_customizer_styles' ) );
	}

	/**
	 * Overrides main style if needed. Please modify it accordingly in the future.
	 *
	 * @since 4.4.6
	 */
	public function overrides_main_style() {
		if ( ! function_exists( 'get_imagify_option' ) ) {
			return;
		}

		$style = '';

		// Logo - Custom style should be applied only when WebP + picture tag is enabled.
		$is_webp         = get_imagify_option( 'display_webp' );
		$is_webp_picture = 'picture' === get_imagify_option( 'display_webp_method' );
		if ( $is_webp && $is_webp_picture ) {
			$logo_height = esc_attr( et_get_option( 'logo_height', '54' ) );
			$style      .= "
				picture#logo {
					display: inherit;
				}
				picture#logo source, picture#logo img {
					width: auto;
					max-height: {$logo_height}%;
					vertical-align: middle;
				}
				@media (min-width: 981px) {
					.et_vertical_nav #main-header picture#logo source,
					.et_vertical_nav #main-header picture#logo img {
						margin-bottom: 28px;
					}
				}
			";
		}

		if ( ! empty( $style ) ) {
			wp_add_inline_style( 'divi-style', $style );
		}
	}

	/**
	 * Overrides customizer style if needed. Please modify it accordingly in the future.
	 *
	 * @since 4.4.6
	 */
	function overrides_customizer_styles() {
		if ( ! function_exists( 'get_imagify_option' ) ) {
			return;
		}

		$scripts = '';

		// Logo - Custom style should be applied only when WebP + picture tag is enabled.
		$is_webp         = get_imagify_option( 'display_webp' );
		$is_webp_picture = 'picture' === get_imagify_option( 'display_webp_method' );
		if ( $is_webp && $is_webp_picture ) {
			$scripts .= "
				(function($, api){
					var logo_image = '';

					function fix_webp_logo_height() {
						if ('' === logo_image) {
							var context = frames['customize-' + api.previewer.channel()].document;
							logo_image  = $('picture#logo img, picture#logo source', context);
						}

						var logo_height = api.value('et_divi[logo_height]')();
						logo_height     = 'undefined' === typeof logo_height ? 54 : parseInt(logo_height);
						logo_image.css('max-height', logo_height + '%');
					}

					api('et_divi[logo_height]', function(value) {
						value.bind(function(to) {
							fix_webp_logo_height();
						});
					});
				})(jQuery, wp.customize);
			";
		}

		if ( ! empty( $scripts ) ) {
			wp_add_inline_script( 'divi-customizer-controls-js', $scripts );
		}
	}
}

new ET_Builder_Plugin_Compat_Imagify();
