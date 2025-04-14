<?php
/**
 * Handle AI Button feature.
 *
 * @package Builder
 * @since 4.22.0
 */

/**
 * Gets AI field options to be listed.
 *
 * These options are shown when the field is empty.
 *
 * @param boolean $is_image If button is used on image field.
 *
 * @return Array
 */
function et_builder_get_ai_text_field_empty_options( $is_image = false ) {
	$options_list = [
		[
			'label' => esc_html__( 'Generate Automatically with AI', 'et_builder' ),
			'slug'  => 'generate_automatically_with_ai',
		],
	];

	if ( $is_image ) {
		$options_list   = [];
		$options_list[] = [
			'label' => esc_html__( 'Generate with AI', 'et_builder' ),
			'slug'  => 'generate_with_ai',
		];
		$options_list[] = [
			'label' => esc_html__( 'Generate & Replace', 'et_builder' ),
			'slug'  => 'generate_and_replace',
		];
		$options_list[] = [
			'label' => esc_html__( 'Reimagine', 'et_builder' ),
			'slug'  => 'reimagine',
		];
		$options_list[] = [
			'label' => esc_html__( 'Change Style', 'et_builder' ),
			'slug'  => 'change_style',
		];
		$options_list[] = [
			'label' => esc_html__( 'Change Aspect Ratio', 'et_builder' ),
			'slug'  => 'change_aspect_ratio',
		];
	} else {
		$options_list[] = [
			'label' => esc_html__( 'Prompt & Write with Divi AI', 'et_builder' ),
			'slug'  => 'prompt_write_with_divi_ai',
		];
	}

	return $options_list;
}

/**
 * Gets AI field options to be listed.
 *
 * These options are shown when the field is NOT empty.
 *
 * @return Array
 */
function et_builder_get_ai_text_field_options() {
	return [
		[
			'label' => esc_html__( 'Regenerate Automatically', 'et_builder' ),
			'slug'  => 'regenerate_automatically',
			'group' => esc_html__( 'Modify With AI', 'et_builder' ),
		],
		[
			'label' => esc_html__( 'Prompt & Write with Divi AI', 'et_builder' ),
			'slug'  => 'new_prompt_with_divi_ai',
			'group' => esc_html__( 'Modify With AI', 'et_builder' ),
		],
		[
			'label' => esc_html__( 'Refine with AI', 'et_builder' ),
			'slug'  => 'refine_with_ai',
			'group' => esc_html__( 'Modify With AI', 'et_builder' ),
		],
		[
			'label' => esc_html__( 'Rewrite Automatically', 'et_builder' ),
			'slug'  => 'rewrite_automatically',
			'group' => esc_html__( 'Modify With AI', 'et_builder' ),
		],
		[
			'label' => esc_html__( 'Lengthen Text', 'et_builder' ),
			'slug'  => 'lengthen_text',
			'group' => esc_html__( 'Modify With AI', 'et_builder' ),
		],
		[
			'label' => esc_html__( 'Shorten Text', 'et_builder' ),
			'slug'  => 'shorten_text',
			'group' => esc_html__( 'Modify With AI', 'et_builder' ),
		],
		[
			'label' => esc_html__( 'Simplify Language', 'et_builder' ),
			'slug'  => 'simplify_language',
			'group' => esc_html__( 'Modify With AI', 'et_builder' ),
		],
		[
			'label' => esc_html__( 'Change Tone', 'et_builder' ),
			'slug'  => 'change_tone',
			'group' => esc_html__( 'Modify With AI', 'et_builder' ),
		],
	];
}

/**
 * Gets AI text options to be listed.
 *
 * These options are shown when the field is NOT empty.
 *
 * @return Array
 */
function et_builder_get_ai_text_options() {
	return [
		'write_with_ai'       => esc_html__( 'Write With AI', 'et_builder' ),
		'improve_with_ai'     => esc_html__( 'Improve With AI', 'et_builder' ),
		'write_automatically' => esc_html__( 'Write Automatically', 'et_builder' ),
		'write_and_replace'   => esc_html__( 'Write & Replace', 'et_builder' ),
	];
}

/**
 * Get AI code options to be listed.
 *
 * These options are shown when the code field is NOT empty.
 *
 * @return Array
 */
function et_builder_get_ai_code_options() {
	return [
		'code_with_ai'          => esc_html__( 'Code With AI', 'et_builder' ),
		'improve_code_with_ai'  => esc_html__( 'Improve With AI', 'et_builder' ),

		// Later, qucik options will be retrieved from AI server, these are just placeholders for now.
		'make_it_better'        => esc_html__( 'Make It Better', 'et_builder' ),
		'format'                => esc_html__( 'Format', 'et_builder' ),
		'improve_compatibility' => esc_html__( 'Improve Compatibility', 'et_builder' ),
		'optimize'              => esc_html__( 'Optimize', 'et_builder' ),
		'convert_color_values'  => esc_html__( 'Convert Color Values', 'et_builder' ),
		'prefix_classes'        => esc_html__( 'Prefix Classes', 'et_builder' ),
	];
}

/**
 * Gets AI image options to be listed.
 *
 * These options are shown when the field is NOT empty.
 *
 * @return Array
 */
function et_builder_get_ai_image_options() {
	return [
		'generate_with_ai'       => esc_html__( 'Generate With AI', 'et_builder' ),
		'generate_automatically' => esc_html__( 'Generate Automatically', 'et_builder' ),
		'generate_and_replace'   => esc_html__( 'Generate & Replace', 'et_builder' ),
		'reimagine'              => esc_html__( 'Reimagine', 'et_builder' ),
		'change_style'           => esc_html__( 'Change Style', 'et_builder' ),
		'modify'                 => esc_html__( 'Modify', 'et_builder' ),
		'extend'                 => esc_html__( 'Extend', 'et_builder' ),
		'upscale'                => esc_html__( 'Upscale', 'et_builder' ),
		'enhance'                => esc_html__( 'Enhance', 'et_builder' ),
		'expand_and_fill'        => esc_html__( 'Expand And Fill', 'et_builder' ),
		'edit_image_with_ai'     => esc_html__( 'Edit Image With AI', 'et_builder' ),
	];
}


/**
 * Gets AI field options to be listed.
 *
 * These options are shown when the text field has selected text.
 */
function et_builder_get_ai_selected_text_field_options() {
	return [
		[
			'label' => esc_html__( 'Prompt & Write Selection with Divi AI', 'et_builder' ),
			'slug'  => 'new_prompt_with_divi_ai',
			'group' => esc_html__( 'Modify With AI', 'et_builder' ),
		],
		[
			'label' => esc_html__( 'Rewrite Selection Automatically', 'et_builder' ),
			'slug'  => 'rewrite_automatically',
			'group' => esc_html__( 'Modify With AI', 'et_builder' ),
		],
	];
}
