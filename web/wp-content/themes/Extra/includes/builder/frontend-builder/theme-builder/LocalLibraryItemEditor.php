<?php
/**
 * File containing Local Library Item Editor class.
 *
 * @package Builder
 * @subpackage ThemeBuilder
 * @since 4.18.0
 */

/**
 * Local Library Item Editor class.
 */
class ET_Theme_Builder_Local_Library_Item_Editor {
	/**
	 * Hold the class instance.
	 *
	 * @var ET_Theme_Builder_Local_Library_Item_Editor[]
	 */
	private static $_instances;

	/**
	 * Interim Theme Builder Id.
	 *
	 * @var int
	 */
	protected static $_theme_builder_id;

	/**
	 * Library Item.
	 *
	 * @var ET_Theme_Builder_Local_Library_Item
	 */
	public $item;

	/**
	 * Class contructor.
	 *
	 * @param int $item_id Item Id.
	 */
	public function __construct( $item_id ) {
		if ( ! self::$_theme_builder_id ) {
			self::$_theme_builder_id = et_theme_builder_insert_library_theme_builder();
		}

		$this->item = new ET_Theme_Builder_Local_Library_Item( $item_id );
	}

	/**
	 * Get the singleton instance.
	 *
	 * @param int $item_id Item Id.
	 *
	 * @return ET_Theme_Builder_Local_Library_Item_Editor
	 */
	public static function instance( $item_id ) {
		if ( ! isset( self::$_instances[ $item_id ] ) ) {
			self::$_instances[ $item_id ] = new ET_Theme_Builder_Local_Library_Item_Editor( $item_id );
		}

		return self::$_instances[ $item_id ];
	}

	/**
	 * Gets the interim Theme Builder Id for the current request.
	 *
	 * @return int
	 */
	public static function get_interim_theme_builder_id() {
		return self::$_theme_builder_id;
	}

	/**
	 * Init Library Template Item.
	 */
	public function init_library_template_item_editor() {
		if ( ! isset( self::$_theme_builder_id ) ) {
			return false;
		}

		// Insert template.
		$template_id = et_theme_builder_create_template_from_library_template( $this->item->item_post );

		if ( ! $template_id ) {
			return false;
		}

		add_post_meta( self::$_theme_builder_id, '_et_template', $template_id );
	}

	/**
	 * Init Library Template Item.
	 */
	public function init_library_set_item_editor() {
		if ( ! isset( self::$_theme_builder_id ) ) {
			return false;
		}

		$template_ids             = get_post_meta( $this->item->item_post->ID, '_et_template_id', false );
		$default_template_item_id = (int) get_post_meta( $this->item->item_post->ID, '_et_default_template_id', true );
		$global_layouts           = array();

		foreach ( $template_ids as $maybe_template_id ) {
			$template_item_id = absint( $maybe_template_id );
			$template_item    = new ET_Theme_Builder_Local_Library_Item( $template_item_id );

			if ( ! isset( $template_item->item_post ) || ! is_a( $template_item->item_post, 'WP_Post' ) ) {
				continue;
			}

			// Insert template.
			$template_id = et_theme_builder_create_template_from_library_template( $template_item->item_post, $global_layouts );

			if ( ! $template_id ) {
				continue;
			}

			if ( $template_item_id === $default_template_item_id ) {
				$global_layouts = array(
					'body'   => (int) get_post_meta( $template_id, '_et_body_layout_id', true ),
					'header' => (int) get_post_meta( $template_id, '_et_header_layout_id', true ),
					'footer' => (int) get_post_meta( $template_id, '_et_footer_layout_id', true ),
				);
			}

			add_post_meta( self::$_theme_builder_id, '_et_template', $template_id );
		}
	}

	/**
	 * Init Library Item.
	 */
	public function init_library_item_editor() {
		if ( ! is_a( $this->item, 'ET_Theme_Builder_Local_Library_Item' ) ) {
			return false;
		}

		$item_type = $this->item->get_item_type();

		if ( ET_THEME_BUILDER_ITEM_TEMPLATE === $item_type ) {
			$this->init_library_template_item_editor();
		} elseif ( ET_THEME_BUILDER_ITEM_SET === $item_type ) {
			$this->init_library_set_item_editor();
		}
	}

	/**
	 * Gets the item title field formatted to be displayed in Theme Builder.
	 *
	 * @return string
	 */
	public function get_library_item_editor_item_title() {
		if ( ET_THEME_BUILDER_ITEM_SET === $this->item->get_item_type() ) {
			return sprintf(
				'%1$s: %2$s',
				esc_html_x( 'Edit Set', 'Edit Set using Theme Builder', 'et_builder' ),
				$this->item->get_item_title()
			);
		} elseif ( ET_THEME_BUILDER_ITEM_TEMPLATE === $this->item->get_item_type() ) {
			return sprintf(
				'%1$s: %2$s',
				esc_html_x( 'Edit Template', 'Edit Template using Theme Builder', 'et_builder' ),
				$this->item->get_item_title()
			);
		}
	}
}
