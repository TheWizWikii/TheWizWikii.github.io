<?php
/**
 * Block Templates Compatibility.
 *
 * @package Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Block Templates Compatibility Class.
 *
 * @since 4.9.8
 */
class ET_Builder_Block_Templates {
	/**
	 * Instance of `ET_Builder_Block_Templates`.
	 *
	 * @var ET_Builder_Block_Templates
	 */
	private static $_instance;

	/**
	 * ET_Builder_Block_Templates constructor.
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * Get the class instance.
	 *
	 * @since 4.9.8
	 *
	 * @return ET_Builder_Block_Templates
	 */
	public static function instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Initialize some hooks to support compatibility with block templates.
	 *
	 * @since 4.14.7
	 */
	public function init_hooks() {
		// Bail early if block templates compatibility is not needed.
		if ( ! self::is_block_templates_compat_needed() ) {
			return;
		}

		// Filters block template loeaders.
		add_action( 'wp_loaded', array( 'ET_Builder_Block_Templates', 'filter_template_loaders' ) );

		// WooCommerce compatibility for themes that support FSE.
		add_action( 'template_redirect', array( 'ET_Builder_Block_Templates', 'remove_unsupported_theme_filter' ), 12 );
	}

	/**
	 * Filter specific template loaders to use theme template files if any instead of
	 * 'wp_template' posts.
	 *
	 * @since 4.17.4
	 */
	public static function filter_template_loaders() {
		$template_slugs = self::get_supported_template_slugs();

		foreach ( $template_slugs as $template_slug ) {
			$template_name   = str_replace( '-', '', $template_slug );
			$template_filter = $template_name . '_template';

			add_filter( $template_filter, array( 'ET_Builder_Block_Templates', 'override_block_template' ), 30, 3 );
		}
	}

	/**
	 * Maybe override block templates.
	 *
	 * This action should be executed only when:
	 * - TB Template is active on current page
	 * - Current template is block template canvas
	 *
	 * @since 4.14.7
	 *
	 * @param string $template Current template path.
	 */
	public static function override_block_template( $template = '', $type = '', $templates = array() ) {
		// Bail early if there is no TB templates for current page request.
		if ( empty( et_theme_builder_get_template_layouts() ) ) {
			return $template;
		}

		$override_header = et_theme_builder_overrides_layout( ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE );
		$override_body   = et_theme_builder_overrides_layout( ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE );
		$override_footer = et_theme_builder_overrides_layout( ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE );

		// Bail early if TB doesn't override any layouts.
		if ( ! $override_header && ! $override_body && ! $override_footer ) {
			return $template;
		}

		// Bail early if current template is not `template-canvas.php`.
		if ( 'template-canvas.php' !== basename( $template ) ) {
			return $template;
		}

		// 1. Override the template canvas with PHP template.
		// Use `locate_template` to get the PHP template. If the template doesn't exist, use
		// default builder block template canvas as replacement.
		$canvas_template = ET_BUILDER_DIR . 'templates/block-template-canvas.php';
		$old_template    = $template;
		$new_template    = locate_template( $templates );
		$template        = file_exists( $new_template ) ? $new_template : $canvas_template;

		// 2. Add needed actions and remove default template canvas actions.
		// Remove viewport meta tag.
		if ( function_exists( '_block_template_viewport_meta_tag' ) ) {
			remove_action( 'wp_head', '_block_template_viewport_meta_tag', 0 );
		}

		// Render conditional title tag for `title-tag` support.
		add_action( 'wp_head', '_wp_render_title_tag', 1 );

		// Remove unconditional title tag.
		if ( function_exists( '_block_template_render_title_tag' ) ) {
			remove_action( 'wp_head', '_block_template_render_title_tag', 1 );
		}

		// 3. Enqueue block templates compatibility fixes.
		// Those fixes are related to missing header and footer PHP templates.
		if ( $canvas_template === $template ) {
			// Add opening and closing wrappers for builder block template canvas because
			// there is no specific wrappers found when a page use block template canvas.
			add_action( 'et_theme_builder_template_after_header', array( 'ET_Builder_Block_Templates', 'main_content_opening_wrapper' ) );
			add_action( 'et_theme_builder_template_before_footer', array( 'ET_Builder_Block_Templates', 'main_content_closing_wrapper' ) );

			// Add styles to fix the body layout on additional wrappers added above.
			add_action( 'wp_enqueue_scripts', array( 'ET_Builder_Block_Templates', 'block_template_styles' ) );

			// Disable deperecated warnings on missing files.
			add_action( 'deprecated_file_included', array( 'ET_Builder_Block_Templates', 'disable_deprecated_file_warnings' ) );
		}

		/**
		 * Fires additional actions after builder override block template.
		 *
		 * @since 4.14.7
		 *
		 * @param string $template     New processed block template.
		 * @param string $old_template Original block template.
		 */
		do_action( 'et_after_override_block_template', $template, $old_template );

		return $template;
	}

	/**
	 * Set main content opening wrapper.
	 *
	 * Provide the opening wrapper tags only to ensure TB layout works smoothly. The same
	 * wrapper is being used on Divi theme.
	 *
	 * @since 4.14.7
	 */
	public static function main_content_opening_wrapper() {
		// Bail early if DBP is inactive because the issue doesn't happen on Divi/Extra.
		if ( ! et_is_builder_plugin_active() ) {
			return;
		}

		// By default, content class is `builder-content`. This class has no style at all
		// because it's controlled by the builder itself. This class can be useful as an
		// indicator and selector for the content built with builder.
		$content_class = 'builder-content';

		// When current page is singular page, check builder and divi/layout block usage.
		if ( is_singular() ) {
			$post_id              = get_the_ID();
			$is_page_builder_used = et_pb_is_pagebuilder_used( $post_id );

			// The `block-content wp-site-blocks` classes will added on current page when:
			// - Builder is not used.
			// - Builder is used but it's coming from Divi Layout block.
			// The `block-content` class has style to reset content width. The `wp-site-blocks`
			// class is needed to mimic default block content styles.
			if ( ! $is_page_builder_used || ( $is_page_builder_used && has_block( 'divi/layout', $post_id ) ) ) {
				$content_class = 'block-content wp-site-blocks';
			}
		}
		?>
			<div id="et-main-area">
				<div id="main-content" class="<?php echo esc_attr( $content_class ); ?>">
		<?php
	}

	/**
	 * Set main content closing wrapper.
	 *
	 * Provide the closing wrapper tag only to ensure TB layout works smoothly. The same
	 * wrapper is being used on Divi theme.
	 *
	 * @since 4.14.7
	 */
	public static function main_content_closing_wrapper() {
		// Bail early if DBP is inactive because the issue doesn't happen on Divi/Extra.
		if ( ! et_is_builder_plugin_active() ) {
			return;
		}

		?>
				</div><!-- #main-content -->
			</div><!-- #et-main-area -->
		<?php
	}

	/**
	 * Enqueue block templates compatibility styles.
	 *
	 * @since 4.14.7
	 */
	public static function block_template_styles() {
		// Bail early if DBP is inactive because the issue doesn't happen on Divi/Extra.
		if ( ! et_is_builder_plugin_active() ) {
			return;
		}

		wp_enqueue_style( 'et-block-templates-styles', ET_BUILDER_URI . '/styles/block_templates.css', array(), ET_BUILDER_PRODUCT_VERSION );
	}

	/**
	 * Disable deprecated files warnings.
	 *
	 * Since themes that support block template may don't have some files, the template
	 * may fall into backward compatibility for those files and trigger warnings. Hence,
	 * we need to disable them temporarily. The list of files:
	 * - header
	 * - footer
	 * - comments
	 *
	 * @since 4.14.7
	 *
	 * @param string $file File info.
	 */
	public static function disable_deprecated_file_warnings( $file ) {
		// Bail early if DBP is inactive because the issue doesn't happen on Divi/Extra.
		if ( ! et_is_builder_plugin_active() ) {
			return;
		}

		if ( strpos( $file, 'header.php' ) || strpos( $file, 'footer.php' ) || strpos( $file, 'comments.php' ) ) {
			add_filter( 'deprecated_file_trigger_error', '__return_false' );
		} else {
			add_filter( 'deprecated_file_trigger_error', '__return_true' );
		}
	}

	/**
	 * Remove unsupported theme filters for WooCommerce.
	 *
	 * When current theme supports FSE, WooCommerce will mark it as unsupported theme and
	 * overrides some filters and few of them are related to builder. Hence, we need to
	 * remove those filters to ensure Divi Builder works normally.
	 *
	 * @since 4.14.7
	 */
	public static function remove_unsupported_theme_filter() {
		// Bail early if WooCommerce is not active or current theme is not block theme.
		if ( ! et_is_woocommerce_plugin_active() || ! et_builder_is_block_theme() ) {
			return;
		}

		// Single Product.
		if ( is_product() ) {
			global $post;

			$post_id = $post ? $post->ID : 0;

			// Only remove those filters when current product uses builder.
			if ( et_pb_is_pagebuilder_used( $post_id ) ) {
				remove_filter( 'the_content', array( 'WC_Template_Loader', 'unsupported_theme_product_content_filter' ), 10 );
				remove_filter( 'woocommerce_product_tabs', array( 'WC_Template_Loader', 'unsupported_theme_remove_review_tab' ), 10 );
			}
		}
	}

	/**
	 * Determine whether block templates compatibility support is needed or not.
	 *
	 * Support block templates compatibility only if:
	 * - Current WordPress or Gutenberg supports block templates
	 * - Current theme supports block templates
	 *
	 * @since 4.17.4
	 *
	 * @return boolean Compatibility status.
	 */
	public static function is_block_templates_compat_needed() {
		// Bail early if `locate_block_template` function doesn't exists (WP 5.8 above).
		if ( ! function_exists( 'locate_block_template' ) ) {
			return false;
		}

		// Whether current theme supports block templates or block theme.
		$is_theme_supports_block_templates = current_theme_supports( 'block-templates' );
		$is_block_theme                    = et_builder_is_block_theme();
		$is_block_templates_compat_needed  = $is_theme_supports_block_templates || $is_block_theme;

		/**
		 * Filters the result of the block templates compatibility check.
		 *
		 * @since 4.17.4
		 *
		 * @param boolean $is_block_templates_compat_needed Compatibility status.
		 */
		return (bool) apply_filters( 'et_builder_is_block_templates_compat_needed', $is_block_templates_compat_needed );
	}

	/**
	 * Get supported template slugs.
	 *
	 * Those template slugs are available on TB.
	 *
	 * @since 4.17.4
	 *
	 * @return string[] List of supported template slugs.
	 */
	public static function get_supported_template_slugs() {
		/**
		 * List of possible hook names:
		 *  - `404_template`
		 *  - `archive_template`
		 *  - `author_template`
		 *  - `category_template`
		 *  - `date_template`
		 *  - `frontpage_template`
		 *  - `home_template`
		 *  - `index_template`
		 *  - `page_template`
		 *  - `privacypolicy_template`
		 *  - `search_template`
		 *  - `single_template`
		 *  - `singular_template`
		 *  - `tag_template`
		 *  - `taxonomy_template`
		 *
		 * Don't include `attachment`, `embed`, `paged` because they aren't modified on TB.
		 */
		$default_template_slugs = array(
			'index',
			'home',
			'front-page',
			'singular',
			'single',
			'page',
			'archive',
			'author',
			'category',
			'taxonomy',
			'date',
			'tag',
			'search',
			'privacy-policy',
			'404',
		);
		$template_slugs         = $default_template_slugs;

		// Use `get_default_block_template_types` result if it exists.
		if ( function_exists( 'get_default_block_template_types' ) ) {
			$template_types = (array) get_default_block_template_types();

			if ( isset( $template_types['attachment'] ) ) {
				unset( $template_types['attachment'] );
			}

			$template_slugs = ! empty( $template_types ) ? array_keys( $template_types ) : $default_template_types;
		}

		return $template_slugs;
	}
}

ET_Builder_Block_Templates::instance();
