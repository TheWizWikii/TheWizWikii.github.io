<?php
/**
 * ET_Theme_Builder_Local_Library_Item class
 *
 * @package Builder
 * @subpackage ThemeBuilder
 * @since 4.18.0
 */

/**
 * Class used to implement local library in the theme builder.
 * */
class ET_Theme_Builder_Local_Library_Item {

	/**
	 * Data util.
	 *
	 * @var ET_Core_Data_Utils
	 */
	protected static $_;

	/**
	 * The library item post.
	 *
	 * @var WP_Post|null
	 */
	public $item_post = null;

	/**
	 *  The library item type. i.e template and preset.
	 *
	 * @deprecated Use get_item_type() instead. This should be private variable, eventually.
	 *
	 * @var string
	 */
	public $item_type;

	/**
	 * The contructor.
	 *
	 * @since 4.18.0
	 *
	 * @param integer|null $item_id Iem post id.
	 */
	public function __construct( $item_id = null ) {
		if ( $item_id ) {
			$this->init_item( $item_id );
		}

		self::$_ = ET_Core_Data_Utils::instance();
	}

	/**
	 * Returns the Library Item type.
	 *
	 * @return string
	 */
	public function get_item_type() {
		return $this->item_type;
	}

	/**
	 * Init item.
	 *
	 * @param integer $item_id Item post id.
	 */
	public function init_item( $item_id ) {
		// Initalize item post.
		$item_post = et_theme_builder_get_library_item_post( $item_id );

		if ( is_wp_error( $item_post ) ) {
			return new WP_Error( 'et_library_item_not_exists', __( 'Library Item does not exist.', 'et_builder' ) );
		}

		$this->item_post = $item_post;

		$item_type = et_theme_builder_get_library_item_type( $item_post );

		if ( is_wp_error( $item_post ) ) {
			return $item_type;
		}

		$this->item_type = $item_type;
	}

	/**
	 * Use the local library item.
	 *
	 * @param array $args Item details.
	 */
	public function use_library_item( $args = [] ) {
		if ( ! $this->item_post || ! $this->item_type ) {
			return new WP_Error(
				'no_library_item_found',
				esc_html__( 'No library item found', 'et_builder' ),
				array(
					'status' => 400,
				)
			);
		}

		switch ( $this->item_type ) {
			case ET_THEME_BUILDER_ITEM_TEMPLATE:
				return $this->use_template();
			case ET_THEME_BUILDER_ITEM_SET:
				return $this->use_preset( $args );
			// `default` case is already handled in the above `if` statement.
		}
	}

	// phpcs:disable Squiz.Commenting.FunctionComment.ParamCommentFullStop -- Respecting punctuation.
	/**
	 * Use local library template.
	 *
	 * @param array $global_layouts Optional. Array containing the necessary params.
	 *    $params = [
	 *      'header' => (int|string) Header Layout ID. `use_global` string when TB global layout (relink option) is to be used.
	 *      'body'   => (int|string) Body Layout ID. `use_global` string when TB global layout (relink option) is to be used.
	 *      'footer' => (int|string) Footer Layout ID. `use_global` string when TB global layout (relink option) is to be used.
	 *    ]
	 */
	public function use_template( $global_layouts = [] ) {
		$template_data = array(
			'layouts'  => et_theme_builder_create_layouts_from_library_template( $this->item_post, $global_layouts ),
			'settings' => et_theme_builder_get_template_settings( $this->item_post->ID, true ),
		);

		return $template_data;
	}
	// phpcs:enable

	/**
	 * Populate Data to implement Use Preset functionality in TB.
	 *
	 * @param array $args Additional arguments.
	 *
	 * @return array
	 */
	public function use_preset( $args = [] ) {
		$preset = [];
		$_      = et_();

		$override_default_website_template  = $_->array_get( $args, 'override_default_website_template' );
		$incoming_layout_duplicate_decision = $_->array_get( $args, 'incoming_layout_duplicate_decision' );
		$global_layouts                     = [];
		$default_template_id                = 0;

		$maybe_default_template_id = get_post_meta( $this->item_post->ID, '_et_default_template_id', true );
		$default_template_id       = is_string( $maybe_default_template_id ) ? absint( $maybe_default_template_id ) : 0;

		if ( 'duplicate' === $incoming_layout_duplicate_decision || $override_default_website_template ) {

			if ( $default_template_id > 0 ) {
				// $context is raw, because get_post()->post_content by default uses `raw`.
				$default_template = get_post( $default_template_id );
				$global_layouts   = et_theme_builder_create_layouts_from_library_template( $default_template );

				$preset[ $default_template_id ] = [
					'layouts'  => $global_layouts,
					'settings' => et_theme_builder_get_template_settings( $default_template_id, true ),
				];
			}

			if ( ! $override_default_website_template ) {
				// Layouts should be created for each templates when `Import as static layouts` is clicked.
				$global_layouts = [];
			}
		}

		$template_ids = get_post_meta( $this->item_post->ID, '_et_template_id', false );

		foreach ( $template_ids as $maybe_template_id ) {
			$template_id = absint( $maybe_template_id );

			if ( $default_template_id === $template_id && 'relink' !== $incoming_layout_duplicate_decision ) {
				continue;
			}

			if ( $default_template_id === $template_id && 'relink' === $incoming_layout_duplicate_decision ) {
				foreach ( [ 'header', 'body', 'footer' ] as $layout_type ) {
					if ( '1' === get_post_meta( $template_id, "_et_{$layout_type}_layout_global", true ) ) {
						$global_layouts[ $layout_type ] = 'use_global';
					}
				}
			}

			$library_item           = new self( $template_id );
			$preset[ $template_id ] = $library_item->use_template( $global_layouts );
		}

		return $preset;
	}

	/**
	 * Returns TRUE when the given Preset ID contains a global layout.
	 *
	 * @return bool
	 */
	public function has_global_layouts() {
		if ( ET_THEME_BUILDER_ITEM_SET !== $this->item_type ) {
			return false;
		}

		$has_global_layouts = '1' === get_post_meta( $this->item_post->ID, '_et_has_global_layouts', true );

		return $has_global_layouts;
	}

	/**
	 * Returns TRUE when the given Preset ID contains a default template.
	 *
	 * @return bool
	 */
	public function has_default_template() {
		if ( ET_THEME_BUILDER_ITEM_SET !== $this->item_type ) {
			return false;
		}

		$has_default_template = '1' === get_post_meta( $this->item_post->ID, '_et_has_default_template', true );

		return $has_default_template;
	}

	/**
	 * Gets the default template ID when the $item_type is preset.
	 *
	 * @return bool
	 */
	public function get_default_template_id() {
		if ( ! $this->has_default_template() ) {
			return 0;
		}

		$template_ids        = get_post_meta( $this->item_post->ID, '_et_template_id', false );
		$default_template_id = 0;

		foreach ( $template_ids as $template_id ) {
			$is_default = (bool) get_post_meta( $template_id, '_et_default', true );

			if ( $is_default ) {
				$default_template_id = $template_id;
			} else {
				continue;
			}

			return $default_template_id;
		}
	}

	/**
	 * Get the theme builder id.
	 *
	 * @since 4.18.0
	 *
	 * @return int The theme builder id.
	 */
	public function get_theme_builder_id() {
		return $this->theme_builder_id;
	}

	/**
	 * Gets the item field.
	 *
	 * @param string $field_name Database field name.
	 * @param string $context    Refer get_post_field() for context.
	 * @param string $default    Default value to return when actual value does not exist.
	 * @return string
	 */
	public function get_item_field( $field_name = 'post_title', $context = 'display', $default = '' ) {
		if ( is_a( $this->item_post, 'WP_Post' ) ) {
			return get_post_field( $field_name, $this->item_post->ID, $context );
		} else {
			return $default;
		}
	}

	/**
	 * Gets the item title field.
	 *
	 * @param string $context Refer get_post_field() for context.
	 * @param string $default Default value to return when actual value does not exist.
	 * @return string
	 */
	public function get_item_title( $context = 'display', $default = '' ) {
		return $this->get_item_field( 'post_title', $context );
	}

	/**
	 * Gets the item title field formatted to be displayed in Theme Builder.
	 *
	 * @return int|WP_Error Valid Post ID on success. 0 or WP_Error on failure.
	 */
	public function duplicate_template_item() {
		$template_meta_keys = array(
			'_et_autogenerated_title',
			'_et_enabled',
			'_et_header_layout_enabled',
			'_et_body_layout_enabled',
			'_et_footer_layout_enabled',
			'_et_template_title',
			'_et_use_on',
			'_et_exclude_from',
			'_et_header_layout_global',
			'_et_body_layout_global',
			'_et_footer_layout_global',
			'_et_set_template',
			'_et_default',
		);

		foreach ( $template_meta_keys as $key ) {
			$post_meta_value = get_post_meta( $this->item_post->ID, $key, true );

			// `empty()` must NOT be used because meta value may contain '0' and the post meta will be skipped during duplication.
			if ( isset( $post_meta_value ) && '' !== $post_meta_value ) {
				$template_meta_input[ $key ] = $post_meta_value;
			}
		}

		$new_item = array(
			'post_title'   => $this->item_post->post_title,
			'post_content' => wp_slash( $this->item_post->post_content ),
			'post_status'  => 'publish',
			'post_type'    => $this->item_post->post_type,
			'meta_input'   => $template_meta_input,
			'tax_input'    => array(
				'et_tb_item_type' => ET_THEME_BUILDER_ITEM_TEMPLATE,
			),
		);

		return wp_insert_post( $new_item );
	}

	/**
	 * Duplicates the Library Item.
	 *
	 * @return int
	 */
	public function duplicate_item() {
		$item_duplication_function = "duplicate_{$this->get_item_type()}_item";
		$duplicated_item_id        = $this->$item_duplication_function();

		return $duplicated_item_id;
	}
}
