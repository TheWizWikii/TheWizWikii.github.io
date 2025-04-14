<?php
/**
 * ET_GB_Block_Post_Excerpt class file.
 *
 * @class   ET_GB_Block_Post_Excerpt
 * @package Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class to handle Core - Post Excerpt block integration.
 */
class ET_GB_Block_Post_Excerpt {
	/**
	 * Class instance.
	 *
	 * @var ET_GB_Block_Post_Excerpt
	 */
	private static $_instance;

	/**
	 * Class constructor.
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
	 * @since 4.14.5
	 *
	 * @return ET_GB_Block_Post_Excerpt Class instance.
	 */
	public static function instance() {
		if ( null === self::$_instance ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Register hooks
	 *
	 * @since 4.14.5
	 */
	public function register_hooks() {
		add_filter( 'render_block_core/post-excerpt', array( $this, 'render_block' ), 10, 2 );
		add_filter( 'get_the_excerpt', array( $this, 'get_the_post_excerpt' ) );
	}

	/**
	 * Filter rendered Core - Post Excerpt block on FE.
	 *
	 * @since 4.14.5
	 *
	 * @param string $block_content Saved & serialized block data.
	 * @param array  $parsed_block  Block info.
	 *
	 * @return string Modified block post excerpt.
	 */
	public function render_block( $block_content, $parsed_block ) {
		$attributes = ! empty( $parsed_block['attrs'] ) ? $parsed_block['attrs'] : array();

		return $this->get_rendered_post_excerpt( $block_content, true, $attributes );
	}

	/**
	 * Filter post excerpt of REST API request.
	 *
	 * Only filter post excerpt rendered from REST API request. This API request is being
	 * used by Block Editor.
	 *
	 * @since 4.14.5
	 *
	 * @param string $post_excerpt Current post excerpt rendered.
	 *
	 * @return string Modified post excerpt.
	 */
	public function get_the_post_excerpt( $post_excerpt ) {
		// Bail early if current request is not REST API request.
		if ( ! defined( 'REST_REQUEST' ) || ! REST_REQUEST ) {
			return $post_excerpt;
		}

		return $this->get_rendered_post_excerpt( $post_excerpt );
	}

	/**
	 * Get rendered post excerpt built with builder. Always return rendered $block_excerpt
	 * because it's already wrapped with Post Excerpt block wrapper.
	 *
	 * @since 4.14.5
	 *
	 * @param string  $block_excerpt Current rendered post excerpt.
	 * @param boolean $is_wrapped    Whether the post excerpt is wrapped or not.
	 * @param array   $attributes    Block attributes values.
	 *
	 * @return string Old or new rendered post excerpt.
	 */
	public function get_rendered_post_excerpt( $block_excerpt, $is_wrapped = false, $attributes = array() ) {
		// Bail early if no global post. Need to get the post here due to some issues with
		// 3rd party plugins regarding missing 2nd arg on the `get_the_excerpt` filter.
		$post_id = ! empty( $attributes['postId'] ) ? (int) $attributes['postId'] : 0;
		$post    = $post_id ? get_post( $post_id ) : get_post();
		if ( empty( $post ) ) {
			return $block_excerpt;
		}

		if ( ! empty( $post->post_excerpt ) ) {
			return $block_excerpt;
		}

		// Bail early if Builder framework is not loaded. There are some cases where 3rd
		// party plugins run scan without visiting theme functions file.
		if ( ! function_exists( 'et_builder_load_framework' ) ) {
			return $block_excerpt;
		}

		if ( ! et_pb_is_pagebuilder_used( $post->ID ) ) {
			return $block_excerpt;
		}

		static $et_rendered_post_excerpt = array();

		// Bail early if current post is already processed.
		if ( isset( $et_rendered_post_excerpt[ $post->ID ] ) ) {
			return $et_rendered_post_excerpt[ $post->ID ];
		}

		// 1. Ensure all the ET shortcode are registered.
		if ( ! did_action( 'et_builder_ready' ) ) {
			// When the `get_the_excerpt` filter is called by Query Loop block on the FE,
			// the `ET_Builder_Element` class is loaded properly but no ET shortcode is
			// registered yet. In this case, we can call `et_builder_init_global_settings`
			// & `et_builder_add_main_elements` methods directly. However, this class is not
			// loaded on the Block Editor, so we have to load all related files manually
			// before we can call those methods to register the shortcode.
			if ( ! class_exists( 'ET_Builder_Element' ) ) {
				require_once ET_BUILDER_DIR . 'class-et-builder-value.php';
				require_once ET_BUILDER_DIR . 'class-et-builder-element.php';
				require_once ET_BUILDER_DIR . 'ab-testing.php';
			}

			et_builder_init_global_settings();
			et_builder_add_main_elements();
			et_builder_settings_init();
		}

		// 2. Generate Builder post excerpt.
		// WordPress post excerpt length comes from `excerpt_length` filter. And, it's
		// words based length, not characters based length.
		$excerpt_length   = apply_filters( 'excerpt_length', 55 );
		$new_post_excerpt = et_core_intentionally_unescaped( wpautop( et_delete_post_first_video( truncate_post( $excerpt_length, false, $post, true, true ) ) ), 'html' );

		// 3. Ensure to return the block wrapper if the $block_excerpt is already wrapped.
		if ( $is_wrapped && ! empty( $new_post_excerpt ) ) {
			$new_post_excerpt = wp_strip_all_tags( $new_post_excerpt );

			// If generated block excerpt is not empty, we just need to replace the excerpt
			// text with the new one. Otherwise, we have to rebuilt the block excerpt.
			if ( ! empty( $block_excerpt ) ) {
				$wrapper          = '/(<p class="wp-block-post-excerpt__excerpt">)(.*?)(<a|<\/p>)/';
				$new_post_excerpt = preg_replace( $wrapper, "$1{$new_post_excerpt}$3", $block_excerpt );
			} else {
				// 3.a. More Text.
				$more_text = ! empty( $attributes['moreText'] ) ? '<a class="wp-block-post-excerpt__more-link" href="' . esc_url( get_the_permalink( $post->ID ) ) . '">' . esc_html( $attributes['moreText'] ) . '</a>' : '';

				// 3.b. Text Align Class.
				$classes       = ! empty( $attributes['textAlign'] ) ? 'has-text-align-' . esc_attr( $attributes['textAlign'] ) : '';
				$wrapper_attrs = get_block_wrapper_attributes( array( 'class' => $classes ) );

				// 3.c. Post Excerpt Content.
				$content               = '<p class="wp-block-post-excerpt__excerpt">' . $new_post_excerpt;
				$show_more_on_new_line = et_()->array_get( $attributes, 'showMoreOnNewLine', true );
				if ( $show_more_on_new_line && ! empty( $more_text ) ) {
					$content .= '</p><p class="wp-block-post-excerpt__more-text">' . $more_text . '</p>';
				} else {
					$content .= $more_text . '</p>';
				}

				$new_post_excerpt = sprintf( '<div %1$s>%2$s</div>', $wrapper_attrs, $content );
			}
		}

		$et_rendered_post_excerpt[ $post->ID ] = $new_post_excerpt;

		return $new_post_excerpt;
	}
}

// Initialize ET_GB_Block_Post_Excerpt.
ET_GB_Block_Post_Excerpt::instance();
