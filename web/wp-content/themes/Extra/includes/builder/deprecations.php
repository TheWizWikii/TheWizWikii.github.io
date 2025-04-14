<?php

/**
 * Deprecated symbols mapped to replacements when applicable, organized by type.
 *
 * @since 3.1
 *
 * @return array[] {
 *     Deprecations
 *
 *     @type string[] $functions {
 *         Deprecated Functions
 *
 *         @type mixed $deprecated_symbol Replacement symbol or default value
 *         ...
 *     }
 *     @type array[]  $classes {
 *         Deprecations By Class
 *
 *         @type array[] $class_name {
 *             Deprecated Symbols
 *
 *             @type string[] $symbol_type {
 *
 *                 @type mixed $deprecated_symbol Replacement symbol or default value
 *                 ...
 *             }
 *             ...
 *         }
 *         ...
 *     }
 * }
 */
return array(
	'functions' => array(),
	'classes'   => array(
		'\ET_Builder_Module_Blurb' => array(
			'method_args' => array(
				'_add_option_toggles'           => array( 'general', array() ),
				'_shortcode_callback'           => array( array(), null, 'et_pb_blurb' ),
				'_shortcode_passthru_callback'  => array( array(), null, 'et_pb_blurb' ),
				'additional_shortcode_callback' => array( array(), null, 'et_pb_blurb' ),
				'shortcode_callback'            => array( array(), null, 'et_pb_blurb' ),
			),
			'methods'     => array(
				'_add_option_toggles'            => '_add_settings_modal_toggles',
				'_get_current_shortcode_address' => 'generate_element_address',
				'_shortcode_callback'            => '_render',
				'_shortcode_passthru_callback'   => 'render_as_builder_data',
				'additional_shortcode_callback'  => 'additional_render',
				'get_shortcode_fields'           => 'get_default_props',
				'pre_shortcode_content'          => 'before_render',
				'shortcode_atts'                 => '',
				'shortcode_atts_to_data_atts'    => 'props_to_html_data_attrs',
				'shortcode_callback'             => 'render',
				'shortcode_output'               => 'output',
			),
			'properties'  => array(
				'advanced_options'      => 'advanced_fields',
				'custom_css_options'    => 'custom_css_fields',
				'options_toggles'       => 'settings_modal_toggles',
				'shortcode_atts'        => 'props',
				'shortcode_content'     => 'content',
				'allowlisted_fields'    => array(),
				'no_shortcode_callback' => 'no_render',
			),
		),
	),
);
