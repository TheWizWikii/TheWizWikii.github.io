<?php
/**
 * Migration: Woo v2 modules
 *
 * Contains Woo Notice and Woo Add to cart modules migrations.
 *
 * @since 4.14.0
 * @package Divi
 */

defined( 'ABSPATH' ) || exit;

/**
 * Text OG Migration Class.
 *
 * Migrates Text OG fields to Title OG in Woo Cart Notice.
 * Migrates Text OG fields to Field Labels OG in Woo Add to Cart.
 */
class ET_Builder_Module_Settings_Migration_WooTextOG extends ET_Builder_Module_Settings_Migration {

	/**
	 * Gets the modules that needs migration.
	 *
	 * @used-by ET_Builder_Module_Settings_Migration::handle_field_name_migrations()
	 *
	 * @return string[]
	 */
	public function get_modules() {
		return array(
			'et_pb_wc_cart_notice',
			'et_pb_wc_add_to_cart',
		);
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing -- Paresh's PR contains function comment.
	public function get_fields() {
		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned -- Invalid warning.
		// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow -- Invalid warning.
		return array(
			// Migration 01: Cart Notice.
			// Text Font, weight & style.
			'title_font'                                               => array(
				'affected_fields' => array(
					'body_font' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_font_last_edited'                                   => array(
				'affected_fields' => array(
					'body_font_last_edited' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_font_tablet'                                        => array(
				'affected_fields' => array(
					'body_font_tablet' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_font_phone'                                         => array(
				'affected_fields' => array(
					'body_font_phone' => array( 'et_pb_wc_cart_notice' ),
				),
			),

			// Text Color.
			'title_text_color'                                         => array(
				'affected_fields' => array(
					'body_text_color' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_color_last_edited'                             => array(
				'affected_fields' => array(
					'body_text_color_last_edited' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_color_tablet'                                  => array(
				'affected_fields' => array(
					'body_text_color_tablet' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_color_phone'                                   => array(
				'affected_fields' => array(
					'body_text_color_phone' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_color__hover_enabled'                          => array(
				'affected_fields' => array(
					'body_text_color__hover_enabled' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_color__hover'                                  => array(
				'affected_fields' => array(
					'body_text_color__hover' => array( 'et_pb_wc_cart_notice' ),
				),
			),

			// Text Size.
			'title_font_size'                                          => array(
				'affected_fields' => array(
					'body_font_size' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_font_size_last_edited'                              => array(
				'affected_fields' => array(
					'body_font_size_last_edited' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_font_size_tablet'                                   => array(
				'affected_fields' => array(
					'body_font_size_tablet' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_font_size_phone'                                    => array(
				'affected_fields' => array(
					'body_font_size_phone' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_font_size__hover_enabled'                           => array(
				'affected_fields' => array(
					'body_font_size__hover_enabled' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_font_size__hover'                                   => array(
				'affected_fields' => array(
					'body_font_size__hover' => array( 'et_pb_wc_cart_notice' ),
				),
			),

			// Text Letter spacing.
			'title_letter_spacing'                                     => array(
				'affected_fields' => array(
					'body_letter_spacing' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_letter_spacing_last_edited'                         => array(
				'affected_fields' => array(
					'body_letter_spacing_last_edited' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_letter_spacing_tablet'                              => array(
				'affected_fields' => array(
					'body_letter_spacing_tablet' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_letter_spacing_phone'                               => array(
				'affected_fields' => array(
					'body_letter_spacing_phone' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_letter_spacing__hover_enabled'                      => array(
				'affected_fields' => array(
					'body_letter_spacing__hover_enabled' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_letter_spacing__hover'                              => array(
				'affected_fields' => array(
					'body_letter_spacing__hover' => array( 'et_pb_wc_cart_notice' ),
				),
			),

			// Text Line height.
			'title_line_height'                                        => array(
				'affected_fields' => array(
					'body_line_height' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_line_height_last_edited'                            => array(
				'affected_fields' => array(
					'body_line_height_last_edited' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_line_height_tablet'                                 => array(
				'affected_fields' => array(
					'body_line_height_tablet' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_line_height_phone'                                  => array(
				'affected_fields' => array(
					'body_line_height_phone' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_line_height__hover_enabled'                         => array(
				'affected_fields' => array(
					'body_line_height__hover_enabled' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_line_height__hover'                                 => array(
				'affected_fields' => array(
					'body_line_height__hover' => array( 'et_pb_wc_cart_notice' ),
				),
			),

			// Text horizontal shadow.
			'title_text_shadow_style'                                  => array(
				'affected_fields' => array(
					'body_text_shadow_style' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_horizontal_length'                      => array(
				'affected_fields' => array(
					'body_text_shadow_horizontal_length' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_horizontal_length_last_edited'          => array(
				'affected_fields' => array(
					'body_text_shadow_horizontal_length_last_edited' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_horizontal_length_tablet'               => array(
				'affected_fields' => array(
					'body_text_shadow_horizontal_length_tablet' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_horizontal_length_phone'                => array(
				'affected_fields' => array(
					'body_text_shadow_horizontal_length_phone' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_horizontal_length__hover_enabled'       => array(
				'affected_fields' => array(
					'body_text_shadow_horizontal_length__hover_enabled' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_horizontal_length__hover'               => array(
				'affected_fields' => array(
					'body_text_shadow_horizontal_length__hover' => array( 'et_pb_wc_cart_notice' ),
				),
			),

			// Text vertical shadow.
			'title_text_shadow_vertical_length'                        => array(
				'affected_fields' => array(
					'body_text_shadow_vertical_length' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_vertical_length_last_edited'            => array(
				'affected_fields' => array(
					'body_text_shadow_vertical_length_last_edited' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_vertical_length_tablet'                 => array(
				'affected_fields' => array(
					'body_text_shadow_vertical_length_tablet' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_vertical_length_phone'                  => array(
				'affected_fields' => array(
					'body_text_shadow_vertical_length_phone' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_vertical_length__hover_enabled'         => array(
				'affected_fields' => array(
					'body_text_shadow_vertical_length__hover_enabled' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_vertical_length__hover'                 => array(
				'affected_fields' => array(
					'body_text_shadow_vertical_length__hover' => array( 'et_pb_wc_cart_notice' ),
				),
			),

			// Text Blur strength.
			'title_text_shadow_blur_strength'                          => array(
				'affected_fields' => array(
					'body_text_shadow_blur_strength' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_blur_strength_last_edited'              => array(
				'affected_fields' => array(
					'body_text_shadow_blur_strength_last_edited' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_blur_strength_tablet'                   => array(
				'affected_fields' => array(
					'body_text_shadow_blur_strength_tablet' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_body_text_shadow_blur_strength_phone'               => array(
				'affected_fields' => array(
					'body_text_shadow_blur_strength_phone' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_blur_strength__hover_enabled'           => array(
				'affected_fields' => array(
					'body_text_shadow_blur_strength__hover_enabled' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_blur_strength__hover'                   => array(
				'affected_fields' => array(
					'body_text_shadow_blur_strength__hover' => array( 'et_pb_wc_cart_notice' ),
				),
			),

			// Text Shadow color.
			'title_text_shadow_color'                                  => array(
				'affected_fields' => array(
					'body_text_shadow_color' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_color_last_edited'                      => array(
				'affected_fields' => array(
					'body_text_shadow_color_last_edited' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_color_tablet'                           => array(
				'affected_fields' => array(
					'body_text_shadow_color_tablet' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_color_phone'                            => array(
				'affected_fields' => array(
					'body_text_shadow_color_phone' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_color__hover_enabled'                   => array(
				'affected_fields' => array(
					'body_text_shadow_color__hover_enabled' => array( 'et_pb_wc_cart_notice' ),
				),
			),
			'title_text_shadow_color__hover'                           => array(
				'affected_fields' => array(
					'body_text_shadow_color__hover' => array( 'et_pb_wc_cart_notice' ),
				),
			),

			// Migration 01: Add to Cart.
			// Text Font, weight & style.
			'field_label_font'                                         => array(
				'affected_fields' => array(
					'body_font' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_font_last_edited'                             => array(
				'affected_fields' => array(
					'body_font_last_edited' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_font_tablet'                                  => array(
				'affected_fields' => array(
					'body_font_tablet' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_font_phone'                                   => array(
				'affected_fields' => array(
					'body_font_phone' => array( 'et_pb_wc_add_to_cart' ),
				),
			),

			// Text Color.
			'field_label_text_color'                                   => array(
				'affected_fields' => array(
					'body_text_color' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_color_last_edited'                       => array(
				'affected_fields' => array(
					'body_text_color_last_edited' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_color_tablet'                            => array(
				'affected_fields' => array(
					'body_text_color_tablet' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_color_phone'                             => array(
				'affected_fields' => array(
					'body_text_color_phone' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_color__hover_enabled'                    => array(
				'affected_fields' => array(
					'body_text_color__hover_enabled' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_color__hover'                            => array(
				'affected_fields' => array(
					'body_text_color__hover' => array( 'et_pb_wc_add_to_cart' ),
				),
			),

			// Text Size.
			'field_label_font_size'                                    => array(
				'affected_fields' => array(
					'body_font_size' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_font_size_last_edited'                        => array(
				'affected_fields' => array(
					'body_font_size_last_edited' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_font_size_tablet'                             => array(
				'affected_fields' => array(
					'body_font_size_tablet' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_font_size_phone'                              => array(
				'affected_fields' => array(
					'body_font_size_phone' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_font_size__hover_enabled'                     => array(
				'affected_fields' => array(
					'body_font_size__hover_enabled' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_font_size__hover'                             => array(
				'affected_fields' => array(
					'body_font_size__hover' => array( 'et_pb_wc_add_to_cart' ),
				),
			),

			// Text Letter spacing.
			'field_label_letter_spacing'                               => array(
				'affected_fields' => array(
					'body_letter_spacing' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_letter_spacing_last_edited'                   => array(
				'affected_fields' => array(
					'body_letter_spacing_last_edited' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_letter_spacing_tablet'                        => array(
				'affected_fields' => array(
					'body_letter_spacing_tablet' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_letter_spacing_phone'                         => array(
				'affected_fields' => array(
					'body_letter_spacing_phone' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_letter_spacing__hover_enabled'                => array(
				'affected_fields' => array(
					'body_letter_spacing__hover_enabled' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_letter_spacing__hover'                        => array(
				'affected_fields' => array(
					'body_letter_spacing__hover' => array( 'et_pb_wc_add_to_cart' ),
				),
			),

			// Text Line height.
			'field_label_line_height'                                  => array(
				'affected_fields' => array(
					'body_line_height' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_line_height_last_edited'                      => array(
				'affected_fields' => array(
					'body_line_height_last_edited' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_line_height_tablet'                           => array(
				'affected_fields' => array(
					'body_line_height_tablet' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_line_height_phone'                            => array(
				'affected_fields' => array(
					'body_line_height_phone' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_line_height__hover_enabled'                   => array(
				'affected_fields' => array(
					'body_line_height__hover_enabled' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_line_height__hover'                           => array(
				'affected_fields' => array(
					'body_line_height__hover' => array( 'et_pb_wc_add_to_cart' ),
				),
			),

			// Text horizontal shadow.
			'field_label_text_shadow_style'                            => array(
				'affected_fields' => array(
					'body_text_shadow_style' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_horizontal_length'                => array(
				'affected_fields' => array(
					'body_text_shadow_horizontal_length' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_horizontal_length_last_edited'    => array(
				'affected_fields' => array(
					'body_text_shadow_horizontal_length_last_edited' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_horizontal_length_tablet'         => array(
				'affected_fields' => array(
					'body_text_shadow_horizontal_length_tablet' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_horizontal_length_phone'          => array(
				'affected_fields' => array(
					'body_text_shadow_horizontal_length_phone' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_horizontal_length__hover_enabled' => array(
				'affected_fields' => array(
					'body_text_shadow_horizontal_length__hover_enabled' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_horizontal_length__hover'         => array(
				'affected_fields' => array(
					'body_text_shadow_horizontal_length__hover' => array( 'et_pb_wc_add_to_cart' ),
				),
			),

			// Text vertical shadow.
			'field_label_text_shadow_vertical_length'                  => array(
				'affected_fields' => array(
					'body_text_shadow_vertical_length' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_vertical_length_last_edited'      => array(
				'affected_fields' => array(
					'body_text_shadow_vertical_length_last_edited' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_vertical_length_tablet'           => array(
				'affected_fields' => array(
					'body_text_shadow_vertical_length_tablet' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_vertical_length_phone'            => array(
				'affected_fields' => array(
					'body_text_shadow_vertical_length_phone' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_vertical_length__hover_enabled'   => array(
				'affected_fields' => array(
					'body_text_shadow_vertical_length__hover_enabled' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_vertical_length__hover'           => array(
				'affected_fields' => array(
					'body_text_shadow_vertical_length__hover' => array( 'et_pb_wc_add_to_cart' ),
				),
			),

			// Text Blur strength.
			'field_label_text_shadow_blur_strength'                    => array(
				'affected_fields' => array(
					'body_text_shadow_blur_strength' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_blur_strength_last_edited'        => array(
				'affected_fields' => array(
					'body_text_shadow_blur_strength_last_edited' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_blur_strength_tablet'             => array(
				'affected_fields' => array(
					'body_text_shadow_blur_strength_tablet' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_body_text_shadow_blur_strength_phone'         => array(
				'affected_fields' => array(
					'body_text_shadow_blur_strength_phone' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_blur_strength__hover_enabled'     => array(
				'affected_fields' => array(
					'body_text_shadow_blur_strength__hover_enabled' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_blur_strength__hover'             => array(
				'affected_fields' => array(
					'body_text_shadow_blur_strength__hover' => array( 'et_pb_wc_add_to_cart' ),
				),
			),

			// Text Shadow color.
			'field_label_text_shadow_color'                            => array(
				'affected_fields' => array(
					'body_text_shadow_color' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_color_last_edited'                => array(
				'affected_fields' => array(
					'body_text_shadow_color_last_edited' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_color_tablet'                     => array(
				'affected_fields' => array(
					'body_text_shadow_color_tablet' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_color_phone'                      => array(
				'affected_fields' => array(
					'body_text_shadow_color_phone' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_color__hover_enabled'             => array(
				'affected_fields' => array(
					'body_text_shadow_color__hover_enabled' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
			'field_label_text_shadow_color__hover'                     => array(
				'affected_fields' => array(
					'body_text_shadow_color__hover' => array( 'et_pb_wc_add_to_cart' ),
				),
			),
		);
		// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned -- Invalid warning.
		// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow -- Invalid warning.
	}

	// phpcs:ignore Squiz.Commenting.FunctionComment.Missing -- Paresh's PR contains function comment.
	public function migrate(
		$field_name,
		$current_value,
		$module_slug,
		$saved_value,
		$saved_field_name,
		$attrs,
		$content,
		$module_address
	) {
		// Don't migrate empty value.
		if ( ! empty( $current_value ) ) {
			return $current_value;
		}

		return $saved_value;
	}
}

return new ET_Builder_Module_Settings_Migration_WooTextOG();
