<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class ET_GB_Utils_Conversion
 *
 * Handling Gutenberg serialized content conversion into builder shortcode layout
 */
class ET_GB_Utils_Conversion {
	// Populate all layout block which is placed inside other block. Layout block contains
	// section which has to be the first level element once converted into VB content
	private $deep_layout_blocks = array();

	// Layout list. Layout block got its own section. Others are concatenated into text module
	private $layout_list = array();

	// Temporary variable to hold non layout block into one
	private $text_module_content = '';

	// Serialized layout
	private $shortcode_layout = '';

	/**
	 * Check if given block is layout block
	 *
	 * @since 4.1.0
	 *
	 * @todo being set as static so it is easier to be used outside this class. If being used quite
	 *       frequently, probably consider wrap this into function. Not needed at the moment tho
	 *
	 * @param array $block Parsed block.
	 *
	 * @return bool
	 */
	public static function is_layout_block( $block = array() ) {
		$block_name = et_()->array_get( $block, 'blockName', '' );

		return 'divi/layout' === $block_name;
	}

	/**
	 * Check if given block is reusable block
	 *
	 * @since 4.1.0
	 *
	 * @todo being set as static so it is easier to be used outside this class. If being used quite
	 *       frequently, probably consider wrap this into function. Not needed at the moment tho
	 *
	 * @param array $block Parsed block.
	 *
	 * @return bool
	 */
	public static function is_reusable_block( $block = array() ) {
		$block_name = et_()->array_get( $block, 'blockName', '' );

		return 'core/block' === $block_name && et_()->array_get( $block, 'attrs.ref' ) > 0;
	}

	/**
	 * Get reusable block's parsed content. NOTE: WordPress has built in `render_block_core_block()`
	 * but it renders the block and its content instead of parse its content.
	 *
	 * @since 4.1.0
	 *
	 * @see render_block_core_block()
	 *
	 * @todo being set as static so it is easier to be used outside this class. If being used quite
	 *       frequently, probably consider wrap this into function. Not needed at the moment tho
	 *
	 * @param array $block Parsed block.
	 *
	 * @return array
	 */
	public static function get_reusable_block_content( $block ) {
		$block_id   = et_()->array_get( $block, 'attrs.ref' );
		$block_data = get_post( $block_id );

		if ( ! $block_data || 'wp_block' !== $block_data->post_type || 'publish' !== $block_data->post_status ) {
			return array();
		}

		return parse_blocks( $block_data->post_content );
	}

	/**
	 * Parse reusable block by getting its content and append it as innerBlocks
	 *
	 * @since 4.1.0
	 *
	 * @param array Parsed block.
	 *
	 * @return array Modified parsed block.
	 */
	public static function parse_reusable_block( $block ) {
		$reusable_block_data  = self::get_reusable_block_content( $block );
		$block['innerBlocks'] = array_merge( $block['innerBlocks'], $reusable_block_data );

		// Unset reusable block's ref attribute so reusable block content is no longer fetched
		unset( $block['attrs']['ref'] );

		// Change block into group so its content is being rendered
		$block['blockName'] = 'core/group';

		// Recreate innerContent which is used by block parser to render innerBlock.
		// See: `render_block()`'s `$block['innerContent'] as $chunk` loop
		$block['innerContent'] = array_merge(
			array( '<div class="wp-block-group"><div class="wp-block-group__inner-container">' ),
			array_fill( 0, count( $block['innerBlocks'] ), null ),
			array( '</div></div>' )
		);

		return $block;
	}

	/**
	 * Pull layout block that is located deep inside inner blocks. Layout block contains section;
	 * in builder, section has to be on the first level of document
	 *
	 * @since 4.1.0
	 *
	 * @param array $block Parsed block.
	 */
	private function pull_layout_block( $block ) {
		// Pull and populate layout block. Layout block contains section(s) so it should be rendered
		// on first level layout, below Gutenberg content inside text module
		if ( self::is_layout_block( $block ) ) {
			// Pull layout block and populate list of layout block located on inner blocks
			$this->deep_layout_blocks[] = $block;

			// Remove innerContent and innerHTML value because inner block can't be simply removed
			// due to nested block rendering relies on `$block['innerContent']` making cross reference
			// on `$block['innerBlocks']` and removing them causes error (see: `render_block()`'s
			// `$block['innerContent'] as $chunk` loop). Thus, set deep layout block's content empty
			// so it doesn't get rendered
			$block['innerHTML']    = '';
			$block['innerContent'] = array();

			return $block;
		}

		// Reusable block's content is not saved inside block; Thus Get reusable block's content,
		// append it as innerBlock, and pull layout block if exist.
		if ( self::is_reusable_block( $block ) ) {
			$block = self::parse_reusable_block( $block );
		}

		// Recursively loop over block then pull Layout Block
		if ( ! empty( $block['innerBlocks'] ) ) {
			$block['innerBlocks'] = array_map(
				array( $this, 'pull_layout_block' ),
				$block['innerBlocks']
			);
		}

		return $block;
	}

	/**
	 * Convert serialized block into shortcode layout
	 *
	 * @since 4.1.0
	 *
	 * @param string $serialized_block
	 *
	 * @return string
	 */
	public function block_to_shortcode( $serialized_block = '' ) {
		// Wrapper div needs to be trimmed
		$layout_open_tag     = '<div class="wp-block-divi-layout">';
		$layout_open_length  = strlen( $layout_open_tag );
		$layout_close_tag    = '</div>';
		$layout_close_length = strlen( $layout_close_tag );

		// Parsed blocks
		$blocks = parse_blocks( $serialized_block );

		// Loop blocks
		foreach ( $blocks as $block ) {
			if ( self::is_layout_block( $block ) ) {
				// Append currently populated non-Layout Block into one before layout block is appended
				if ( ! empty( $this->text_module_content ) ) {
					$this->layout_list[] = $this->text_module_content;

					// Reset text module content so next non-layout block is placed below current layout block
					$this->text_module_content = '';
				}

				$this->layout_list[] = $block;
			} else {
				// Reusable block's content is not saved inside block; Thus Get reusable block's
				// content, append it as innerBlock, and pull layout block if exist.
				if ( self::is_reusable_block( $block ) ) {
					$block = self::parse_reusable_block( $block );
				}

				// Pull any Layout Block inside nested block if there's any
				if ( ! empty( $block['innerBlocks'] ) ) {
					$block['innerBlocks'] = array_map(
						array( $this, 'pull_layout_block' ),
						$block['innerBlocks']
					);
				}

				// Populate block into temporary text module content buffer
				$this->text_module_content .= render_block( $block );
			}
		}

		// Populate remaining non-layout block into layout list
		if ( ! empty( $this->text_module_content ) ) {
			$this->layout_list[] = $this->text_module_content;

			// Reset
			$this->text_module_content = '';
		}

		// Loop over populated content and render it into shortcode layout
		foreach ( array_merge( $this->layout_list, $this->deep_layout_blocks ) as $item ) {
			if ( self::is_layout_block( $item ) ) {
				$shortcode_layout = trim( et_()->array_get( $item, 'innerHTML', '' ) );

				// Remove layout content opening <div>
				if ( $layout_open_tag === substr( $shortcode_layout, 0, $layout_open_length ) ) {
					$shortcode_layout = substr( $shortcode_layout, $layout_open_length );
				}

				// Remove layout content closing </div>
				if ( $layout_close_tag === substr( $shortcode_layout, ( 0 - $layout_close_length ) ) ) {
					$shortcode_layout = substr( $shortcode_layout, 0, ( 0 - $layout_close_length ) );
				}

				$this->shortcode_layout .= $shortcode_layout;
			} else {
				$text_module             = '[et_pb_text]' . $item . '[/et_pb_text]';
				$column                  = '[et_pb_column type="4_4"]' . $text_module . '[/et_pb_column]';
				$row                     = '[et_pb_row admin_label="row"]' . $column . '[/et_pb_row]';
				$this->shortcode_layout .= '[et_pb_section admin_label="section"]' . $row . '[/et_pb_section]';
			}
		}

		return $this->shortcode_layout;
	}
}

/**
 * Convert gutenberg block layout into shortcode.
 * NOTE: There is JS version for activation via Gutenberg. See: `convertBlockToShortcode()`
 *
 * @since 4.1.0
 *
 * @param string $post_content Post content / serialized block.
 *
 * @return string Shortcode layout.
 */
function et_builder_convert_block_to_shortcode( $post_content ) {
	$conversion = new ET_GB_Utils_Conversion();

	return $conversion->block_to_shortcode( $post_content );
}
