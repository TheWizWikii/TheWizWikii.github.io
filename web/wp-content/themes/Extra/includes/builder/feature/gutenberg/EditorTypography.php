<?php
/**
 * Gutenberg editor typography.
 *
 * @package Builder
 * @subpackage Gutenberg
 * @since 4.7.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class use theme's chosen fonts in Gutenberg editor.
 *
 * Class ET_GB_Editor_Typography
 */
class ET_GB_Editor_Typography {

	/**
	 * `ET_GB_Editor_Typography` instance.
	 *
	 * @var ET_GB_Editor_Typography
	 */
	private static $_instance;

	/**
	 * TB's body layout post
	 *
	 * @var WP_Post
	 */
	private $_body_layout_post;

	/**
	 * The `et_pb_post_content` shortcode content extracted from the TB's body layout post content
	 *
	 * @var string
	 */
	private $_post_content_shortcode;

	/**
	 * The `et_pb_post_title shortcode` content extracted from the TB's body layout post content
	 *
	 * @var string
	 */
	private $_post_title_shortcode;

	/**
	 * Constructor.
	 *
	 * ET_GB_Editor_Typography constructor.
	 */
	public function __construct() {

		if ( ! et_core_is_gutenberg_active() ) {
			return;
		}

		$this->register_hooks();
	}

	/**
	 * Get class instance.
	 *
	 * @return object class instance.
	 */
	public static function instance() {

		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Register hooks
	 */
	public function register_hooks() {
		add_action( 'admin_footer', array( $this, 'enqueue_block_typography_styles' ) );
		add_filter( 'block_editor_settings_all', array( $this, 'block_editor_settings_all' ), 10, 2 );
	}

	/**
	 * Filter editor styles pass to the GB editor.
	 *
	 * @param array                   $editor_settings editor settings.
	 * @param WP_Block_Editor_Context $block_editor_context The current block editor context.
	 *
	 * @return mixed
	 */
	public function block_editor_settings_all( $editor_settings, $block_editor_context ) {

		$styles  = $this->get_body_styles();
		$styles .= $this->get_title_styles();

		$post = $block_editor_context->post;

		// If no post is found, return $error_settings early.
		if ( empty( $post ) ) {
			return $editor_settings;
		}

		if ( $post ) {
			$tb_layouts = et_theme_builder_get_template_layouts( ET_Theme_Builder_Request::from_post( $post->ID ) );

			if ( isset( $tb_layouts[ ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE ] ) ) {
				$body_layout             = $tb_layouts[ ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE ];
				$body_layout_id          = et_()->array_get( $body_layout, 'id' );
				$this->_body_layout_post = get_post( $body_layout_id );

				$this->_initialize_shortcode( '_post_content_shortcode', et_theme_builder_get_post_content_modules() );
				$this->_initialize_shortcode( '_post_title_shortcode', array( 'et_pb_post_title' ) );
				$styles .= $this->get_tb_styles();
			}
		}

		$editor_settings['styles'][] = array(
			'css'            => $styles,
			'__unstableType' => 'theme',
		);

		return $editor_settings;
	}

	/**
	 * Set the et_pb_post_content and et_pb_post_title shortcode from the body layout post content.
	 *
	 * @param string $prop {@see self::$_post_content_shortcode} or {@see self::$_post_title_shortcode} property.
	 * @param array  $tagnames Shortcode tagnames.
	 */
	private function _initialize_shortcode( $prop, $tagnames ) {
		$regex = get_shortcode_regex( $tagnames );

		if ( preg_match_all( "/$regex/", $this->_body_layout_post->post_content, $matches ) ) {
			$post_title_shortcodes = et_()->array_get( $matches, '0' );

			// Take the style from the first Post Title module that has the title enabled.
			foreach ( $post_title_shortcodes as $post_title_shortcode ) {
				if ( false === strpos( $post_title_shortcode, 'title="off"' ) ) {
					$this->{$prop} = $post_title_shortcode;

					return;
				}
			}
		} elseif ( preg_match_all( "/$regex/", $this->_body_layout_post->post_content, $matches, PREG_SET_ORDER ) ) {
			$this->{$prop} = et_()->array_get(
				$matches,
				'0.0'
			);
		}
	}

	/**
	 * Print GB typography style.
	 */
	public function enqueue_block_typography_styles() {

		if ( ! ( method_exists( get_current_screen(), 'is_block_editor' ) && get_current_screen()->is_block_editor() ) ) {
			return;
		}

		et_builder_print_font();
	}

	/**
	 * Print the post content style.
	 */
	public function get_body_styles() {

		$body_styles = '';

		$body_font = esc_html( et_get_option( 'body_font' ) );

		if ( ! empty( $body_font ) && 'none' !== $body_font ) {
			et_builder_enqueue_font( $body_font );
			$font_family = et_builder_get_font_family( $body_font );

			$body_styles .= et_builder_generate_css_style(
				array(
					'style' => 'font-family',
					'value' => str_replace( 'font-family: ', '', $font_family ),
				)
			);
		}

		$body_font_height = esc_html( et_get_option( 'body_font_height' ) );

		if ( ! empty( $body_font_height ) ) {
			$body_styles .= et_builder_generate_css_style(
				array(
					'style' => 'line-height',
					'value' => $body_font_height,
				)
			);
		}

		$body_font_size = esc_html( et_get_option( 'body_font_size' ) );

		if ( ! empty( $body_font_size ) ) {
			$body_styles .= et_builder_generate_css_style(
				array(
					'style'  => 'font-size',
					'value'  => $body_font_size,
					'suffix' => 'px',
				)
			);
		}

		if ( ! empty( $body_styles ) ) {
			$body_styles = sprintf( 'body { %1$s }', $body_styles );
		}

		return $body_styles;
	}

	/**
	 * Print post title styles.
	 */
	public function get_title_styles() {

		$title_styles = '';

		$heading_font = esc_html( et_get_option( 'heading_font' ) );

		// Fallback to the body font.
		if ( empty( $heading_font ) || 'none' === $heading_font ) {
			$heading_font = esc_html( et_get_option( 'body_font' ) );
		}

		if ( ! empty( $heading_font ) && 'none' !== $heading_font ) {
			et_builder_enqueue_font( $heading_font );
			$font_family = et_builder_get_font_family( $heading_font );

			$title_styles .= et_builder_generate_css_style(
				array(
					'style' => 'font-family',
					'value' => str_replace( 'font-family: ', '', $font_family ),
				)
			);
		}

		$body_header_spacing = esc_html( et_get_option( 'body_header_spacing' ) );

		if ( ! empty( $body_header_spacing ) ) {
			$title_styles .= et_builder_generate_css_style(
				array(
					'style'  => 'letter-spacing',
					'value'  => $body_header_spacing,
					'suffix' => 'px',
				)
			);
		}

		$body_header_height = esc_html( et_get_option( 'body_header_height' ) );

		if ( ! empty( $body_header_height ) && '1' !== $body_header_height ) {
			$title_styles .= et_builder_generate_css_style(
				array(
					'style' => 'line-height',
					'value' => $body_header_height,
				)
			);
		}

		$body_header_style = esc_html( et_get_option( 'body_header_style' ) );

		if ( ! empty( $body_header_style ) ) {
			// Convert string into array.
			$styles_array = explode( '|', $body_header_style );

			$font_properties_value_map = array(
				'font-weight'     => 'bold',
				'font-style'      => 'italic',
				'text-transform'  => 'uppercase',
				'text-decoration' => 'underline',
			);

			foreach ( $font_properties_value_map as $css_property => $value ) {
				if ( in_array( $value, $styles_array, true ) ) {
					$title_styles .= et_builder_generate_css_style(
						array(
							'style' => $css_property,
							'value' => $value,
						)
					);
				}
			}
		}

		if ( ! empty( $title_styles ) ) {
			$title_styles = sprintf( 'h1,h2,h3,h4,h5,h6,.editor-post-title__block .editor-post-title__input { %1$s }', $title_styles );
		}

		$title_styles .= $this->get_heading_levels_font_size_style();

		return $title_styles;
	}

	/**
	 * Print TB's style.
	 */
	public function get_tb_styles() {

		if ( empty( $this->_post_content_shortcode ) && empty( $this->_post_title_shortcode ) ) {
			return;
		}

		if ( ! class_exists( 'ET_Builder_Element' ) ) {
			require_once ET_BUILDER_DIR . 'class-et-builder-value.php';
			require_once ET_BUILDER_DIR . 'class-et-builder-element.php';
			require_once ET_BUILDER_DIR . 'ab-testing.php';
			et_builder_init_global_settings();
			et_builder_add_main_elements();
			et_builder_settings_init();
			ET_Builder_Element::set_media_queries();
		}

		// To generate the styles from the shortcode, this do_shortcode will intialize et_pb_post_content and et_pb_post_title modules classes.
		ob_start();
		do_shortcode( $this->_post_title_shortcode . $this->_post_content_shortcode );
		ob_end_clean();

		// Get style generated by modules.
		$tb_style = ET_Builder_Element::get_style();

		// Remove `color` property from theme builder style.
		$tb_style = preg_replace( '/(?<=[{;\s])color:.*?;/s', '', $tb_style );

		$have_post_content_style = preg_match( '/\.et_pb_post_content_0\s*{\s*(.*?)\s*}/s', $tb_style, $matches );
		if ( $have_post_content_style && isset( $matches[1] ) ) {
			$et_pb_post_content_styles = explode( ';', $matches[1] );
			$typography_properties     = array(
				'font-family',
				'font-size',
				'font-weight',
				'font-style',
				'text-align',
				'text-shadow',
				'letter-spacing',
				'line-height',
				'text-transform',
				'text-decoration',
				'text-decoration-style',
			);

			$post_content_style = '';

			foreach ( $et_pb_post_content_styles as $et_pb_post_content_style ) {
				$style        = explode( ':', $et_pb_post_content_style ); // explode CSS property and value.
				$css_property = trim( $style[0] );
				if ( in_array( $css_property, $typography_properties, true ) ) {
					$post_content_style .= $css_property . ':' . $style[1] . ';';
				}
			}

			$tb_style = 'body {' . $post_content_style . '}' . $tb_style;
		}

		foreach ( array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) as $heading_selector ) {
			$tb_style = str_replace( ".et_pb_post_content_0 $heading_selector ", $heading_selector, $tb_style );
		}

		foreach ( array( 'a', 'ul', 'ol', 'ul li', 'ol li', 'blockquote' ) as $selector ) {
			$search = array(
				".et_pb_post_content_0 $selector ",
				".et_pb_post_content_0.et_pb_post_content $selector",
			);

			$tb_style = str_replace( $search, $selector, $tb_style );
		}

		// Replace the post title style selectors with editor's post title selector.
		$tb_style = str_replace( array( '.et_pb_post_title_0 .entry-title', '.et_pb_post_title_0 .et_pb_title_container h1.entry-title, .et_pb_post_title_0 .et_pb_title_container h2.entry-title, .et_pb_post_title_0 .et_pb_title_container h3.entry-title, .et_pb_post_title_0 .et_pb_title_container h4.entry-title, .et_pb_post_title_0 .et_pb_title_container h5.entry-title, .et_pb_post_title_0 .et_pb_title_container h6.entry-title' ), '.wp-block.editor-post-title__block .editor-post-title__input', $tb_style );

		// Enqueue fonts.
		$fonts_regex = '/font-family:\s+[\'"]([a-zA-Z0-9\s]+)[\'"]/';
		$has_fonts   = preg_match_all( $fonts_regex, $tb_style, $matches, PREG_SET_ORDER );
		if ( false !== $has_fonts && isset( $match[1] ) ) {
			foreach ( $matches as $match ) {
				et_builder_enqueue_font( $match[1] );
			}
		}

		return $tb_style;
	}

	/**
	 * Generate the heading levels font size from the Header Size customizer setting and return style.
	 *
	 * @return string
	 */
	public function get_heading_levels_font_size_style() {

		$body_header_size = esc_html( et_get_option( 'body_header_size' ) );

		$title_styles = '';

		if ( empty( $body_header_size ) ) {
			return $title_styles;
		}

		$font_sizes = array(
			'h1,.editor-post-title__block .editor-post-title__input' => $body_header_size,
			'h2' => $body_header_size * .86,
			'h3' => $body_header_size * .73,
			'h4' => $body_header_size * .60,
			'h5' => $body_header_size * .53,
			'h6' => $body_header_size * .47,
		);

		foreach ( $font_sizes as $selector => $font_size ) {
			$title_styles .= ',' . et_builder_generate_css(
				array(
					'style'    => 'font-size',
					'value'    => intval( $font_size ),
					'suffix'   => 'px',
					'selector' => $selector,
				)
			);
		}

		return $title_styles;
	}

}

// Initialize ET_GB_Editor_Typography.
ET_GB_Editor_Typography::instance();
