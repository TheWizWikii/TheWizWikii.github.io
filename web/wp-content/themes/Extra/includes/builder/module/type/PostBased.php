<?php


abstract class ET_Builder_Module_Type_PostBased extends ET_Builder_Module {

	public $is_post_based = true;

	/**
	 * Loads and returns the contents of the "No Results" template.
	 *
	 * @since 3.0.77
	 *
	 * @param string $heading_tag
	 *
	 * @return string
	 */
	public static function get_no_results_template( $heading_tag = 'h1' ) {
		global $et_no_results_heading_tag;
		$et_no_results_heading_tag = $heading_tag;
		ob_start();

		if ( et_is_builder_plugin_active() ) {
			include ET_BUILDER_PLUGIN_DIR . 'includes/no-results.php';
		} else {
			get_template_part( 'includes/no-results', 'index' );
		}

		return ob_get_clean();
	}

	/**
	 * Filters out invalid term ids from an array.
	 *
	 * @since 3.0.106
	 *
	 * @param integer[] $term_ids
	 * @param string    $taxonomy
	 *
	 * @return integer[]
	 */
	public static function filter_invalid_term_ids( $term_ids, $taxonomy ) {
		$valid_term_ids = array();

		foreach ( $term_ids as $term_id ) {
			$term_id = intval( $term_id );
			$term    = term_exists( $term_id, $taxonomy );
			if ( ! empty( $term ) ) {
				$valid_term_ids[] = $term_id;
			}
		}

		return $valid_term_ids;
	}

	/**
	 * Convert an array or comma-separated list of term ids and special keywords to an array of term ids.
	 *
	 * @since 3.17.2
	 *
	 * @param string|array $terms Comma-separated list of term ids and special keywords.
	 * @param integer      $post_id Optional post id to resolve "current" categories.
	 * @param string       $taxonomy
	 *
	 * @return integer[]
	 */
	protected static function filter_meta_categories( $categories, $post_id = 0, $taxonomy = 'category' ) {
		$raw_term_ids = is_array( $categories ) ? $categories : explode( ',', $categories );

		if ( in_array( 'all', $raw_term_ids, true ) ) {
			// If "All Categories" is selected return an empty array so it works for all terms
			// even ones created after the module was last updated.
			return array();
		}

		$term_ids = array();

		foreach ( $raw_term_ids as $value ) {
			if ( 'current' === $value ) {
				if ( $post_id > 0 ) {
					$post_terms = wp_get_object_terms( $post_id, $taxonomy );

					if ( is_wp_error( $post_terms ) ) {
						continue;
					}

					$term_ids = array_merge( $term_ids, wp_list_pluck( $post_terms, 'term_id' ) );
				} else {
					$is_category = 'category' === $taxonomy && is_category();
					$is_tag      = ! $is_category && 'post_tag' === $taxonomy && is_tag();
					$is_tax      = ! $is_category && ! $is_tag && is_tax( $taxonomy );

					if ( $is_category || $is_tag || $is_tax ) {
						$term_ids[] = get_queried_object()->term_id;
					}
				}

				continue;
			}
			$term_ids[] = (int) $value;
		}

		$term_ids = self::filter_invalid_term_ids( array_unique( array_filter( $term_ids ) ), $taxonomy );

		return $term_ids;
	}

	/**
	 * Handle common filtering of included categories, including handling meta categories.
	 *
	 * @since 4.0
	 *
	 * @param string|array $include_categories Comma-separated list of term ids and special keywords.
	 * @param integer      $post_id
	 * @param string       $taxonomy
	 *
	 * @return array
	 */
	protected static function filter_include_categories( $include_categories, $post_id = 0, $taxonomy = 'category' ) {
		$categories = array();

		if ( ! empty( $include_categories ) ) {
			// wp_doing_ajax() covers VB usage when fetching computed values where we always have a post.
			if ( is_singular() || wp_doing_ajax() ) {
				$post_id    = $post_id > 0 ? $post_id : self::get_current_post_id_reverse();
				$categories = self::filter_meta_categories( $include_categories, $post_id, $taxonomy );
			} else {
				$categories = self::filter_meta_categories( $include_categories, 0, $taxonomy );
			}
		}

		return $categories;
	}

	public static function is_processing_computed_prop() {
		global $et_fb_processing_shortcode_object;

		return $et_fb_processing_shortcode_object || ( wp_doing_ajax() && 'et_pb_process_computed_property' === $_POST['action'] );
	}
}
