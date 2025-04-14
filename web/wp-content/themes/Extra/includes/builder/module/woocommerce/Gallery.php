<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Description class
 *
 * The ET_Builder_Module_Woocommerce_Description Class is responsible for rendering the
 * Description markup using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ET_Builder_Module_Gallery' ) ) {
	require_once ET_BUILDER_DIR_RESOLVED_PATH . '/module/Gallery.php';
}

/**
 * Class representing WooCommerce Gallery component.
 */
class ET_Builder_Module_Woocommerce_Gallery extends ET_Builder_Module_Gallery {
	/**
	 * Modify properties defined on base module's (gallery) init()
	 *
	 * @since 3.29
	 */
	public function init() {
		parent::init();

		$this->name             = esc_html__( 'Woo Product Gallery', 'et_builder' );
		$this->plural           = esc_html__( 'Woo Product Gallery', 'et_builder' );
		$this->slug             = 'et_pb_wc_gallery';
		$this->folder_name      = 'et_pb_woo_modules';
		$this->main_css_element = '%%order_class%%';

		// Intentionally removing inherited options group.
		unset( $this->settings_modal_toggles['general']['toggles']['main_content'] );

		// Rename Elements Option group to Content.
		$this->settings_modal_toggles['general']['toggles']['elements'] = et_builder_i18n( 'Content' );

		// Intentionally removing inherited advanced options group.
		$this->advanced_fields['link_options'] = false;

		$this->advanced_fields['fonts']['title']['font_size']        = array(
			'default' => '18px',
		);
		$this->advanced_fields['fonts']['title']['line_height']      = array(
			'default' => '1em',
		);
		$this->advanced_fields['fonts']['pagination']['font_size']   = array(
			'default' => '16px',
		);
		$this->advanced_fields['fonts']['pagination']['line_height'] = array(
			'default' => '16px',
		);
		$this->advanced_fields['fonts']['caption']['font_size']      = array(
			'default' => '14px',
		);
		$this->advanced_fields['fonts']['caption']['line_height']    = array(
			'default' => '1em',
		);
		$this->advanced_fields['position_fields']                    = array(
			'default' => 'relative',
		);

		$this->custom_css_fields = array(
			'gallery_item'              => array(
				'label'    => esc_html__( 'Gallery Item', 'et_builder' ),
				'selector' => '.et_pb_gallery_item',
			),
			'gallery_pagination'        => array(
				'label'    => esc_html__( 'Gallery Pagination', 'et_builder' ),
				'selector' => '.et-pb-controllers a',
			),
			'gallery_pagination_active' => array(
				'label'    => esc_html__( 'Pagination Active Page', 'et_builder' ),
				'selector' => '.et-pb-controllers a.et-pb-active-control',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => '7X03vBPYJ1o',
				'name' => esc_html__( 'Divi WooCommerce Modules', 'et_builder' ),
			),
		);

		// Insert classname to module wrapper.
		add_filter(
			'et_builder_wc_gallery_classes',
			array(
				$this,
				'add_wc_gallery_classname',
			),
			10,
			2
		);
	}

	/**
	 * Insert Woo Galleries specific fields and modify fields inherited from base module (gallery)
	 *
	 * @return array
	 */
	public function get_fields() {
		/*
		 * Woo Galleries fields that need to be prepended before fields inherited from gallery
		 * module.
		 */
		$product_default   = ET_Builder_Module_Helper_Woocommerce_Modules::get_product_default();
		$wc_gallery_fields = array(
			'product'        => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product',
				array(
					'default'          => $product_default,
					'toggle_slug'      => 'elements',
					'computed_affects' => array(
						'__gallery',
					),
				)
			),
			'product_filter' => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product_filter',
				array(
					'toggle_slug'      => 'elements',
					'computed_affects' => array(
						'__gallery',
					),
				)
			),
		);

		// Base module (gallery) fields.
		$fields = parent::get_fields();

		// Set the default Layout as Slider.
		if ( array_key_exists( 'fullwidth', $fields ) ) {
			$fields['fullwidth']['default_on_front'] = 'on';
		}

		// Prepending WC images field to fields inherited from gallery module (base module).
		$fields = array_merge( $wc_gallery_fields, $fields );

		// Hide gallery upload image field because module images are set from "Product" field.
		$fields['gallery_ids']['type'] = 'hidden';

		/*
		 * Modify `__gallery`'s `computed_callback` attribute so Woo Gallery can insert additional
		 * arguments to computed callback result.
		 */
		$fields['__gallery'] = array(
			'type'                => 'computed',
			'computed_callback'   => array(
				'ET_Builder_Module_Woocommerce_Gallery',
				'get_wc_gallery',
			),
			'computed_depends_on' => array(
				// Field is hidden because its control is take over by `product` field.
				'gallery_ids',

				/*
				 * Fields exist but not being rendered because their options group is hidden
				 * based on the spec.
				 */
				'gallery_orderby',
				'gallery_captions',

				// Exising and visible fields.
				'fullwidth',
				'orientation',
				'show_pagination',
				'product',
				'product_filter',
			),
		);

		return $fields;
	}

	/**
	 * Gets Placeholder ID as Gallery IDs when in TB mode.
	 *
	 * @see   https://github.com/elegantthemes/Divi/issues/18768
	 *
	 * @since 4.10.8
	 *
	 * @param array $conditional_tags Conditional Tags.
	 *
	 * @return array Array containing placeholder Id when in TB mode. Empty array otherwise.
	 */
	public static function get_gallery_ids( $conditional_tags ) {
		if ( ! is_array( $conditional_tags ) ) {
			return array();
		}

		$is_tb = et_()->array_get( $conditional_tags, 'is_tb', false );

		if ( ! $is_tb || ! function_exists( 'wc_placeholder_img_src' ) ) {
			return array();
		}

		$placeholder_src = wc_placeholder_img_src( 'full' );
		$placeholder_id  = attachment_url_to_postid( $placeholder_src );

		if ( 0 === absint( $placeholder_id ) ) {
			return array();
		}

		return array( $placeholder_id );
	}

	/**
	 * Computed callback's callback method which adjusted arguments passed to original computed
	 * callback's callback so the result is suitable for Woo Gallery module
	 *
	 * @since 4.10.8 Load Placeholder Image when in TB mode.
	 * @since 3.29
	 *
	 * @param array $args             Arguments from Computed Prop AJAX call.
	 * @param array $conditional_tags Conditional Tags.
	 * @param array $current_page     Current page args.
	 *
	 * @return array
	 */
	public static function get_wc_gallery( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		if ( 'current' === $args['product'] && 'true' === et_()->array_get( $conditional_tags, 'is_tb', false ) || is_et_pb_preview() ) {
			et_theme_builder_wc_set_global_objects( $conditional_tags );

			global $product;
		} else {
			// Generate valid `gallery_ids` value based `product` attribute.
			$product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $args['product'] );
		}

		$attachment_ids = array();

		if ( $product ) {
			$featured_image_id = intval( $product->get_image_id() );
			$attachment_ids    = $product->get_gallery_image_ids();
		}

		// Load placeholder Image when in TB.
		if ( is_array( $attachment_ids ) && empty( $attachment_ids ) ) {
			$attachment_ids = self::get_gallery_ids( $conditional_tags );
		}

		// Modify `gallery_ids` value.
		$args['gallery_ids'] = $attachment_ids;

		// Don't display Placeholder when no Gallery image is available.
		// @see https://github.com/elegantthemes/submodule-builder/pull/6706#issuecomment-542275647
		if ( 0 === count( $attachment_ids ) ) {
			$args['attachment_id'] = -1;
		}

		return ET_Builder_Module_Gallery::get_gallery( $args, $conditional_tags, $current_page );
	}

	/**
	 * Modify module wrapper's classname
	 *
	 * @since 3.29
	 *
	 * @param array $classname    List of class names.
	 * @param int   $render_count Count of times the module is rendered.
	 *
	 * @return array
	 */
	public function add_wc_gallery_classname( $classname, $render_count ) {
		// For gallery to be properly rendered, it needs `et_pb_gallery` classname.
		$classname[] = 'et_pb_gallery';

		return $classname;
	}

	/**
	 * Use ET_Builder_Module_Woocommerce_Gallery::get_wc_gallery() instead of base module's
	 * ET_Builder_Module_Gallery::get_gallery() method for defining attachment value in
	 * frontend's `render()` and visual builder's computed callback result
	 *
	 * @since 3.29
	 *
	 * @param array $args Additional arguments.
	 *
	 * @return array
	 */
	public function get_attachments( $args = array() ) {
		$args['product'] = $this->props['product'];

		return self::get_wc_gallery( $args );
	}
}

new ET_Builder_Module_Woocommerce_Gallery();
