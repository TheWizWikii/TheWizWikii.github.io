<?php
/**
 * Field definition for sticky elements feature.
 *
 * @package     Divi
 * @sub-package Builder
 * @since 4.6.0
 */

/**
 * Sticky field class.
 *
 * @since 4.6.0
 */
class ET_Builder_Module_Field_Sticky extends ET_Builder_Module_Field_Base {
	/**
	 * Default sticky element field attributes (prefix, tab, and toggle).
	 *
	 * @var array Default sticky element field attributes.
	 */
	protected $defaults = array(
		'prefix'      => 'sticky',
		'tab_slug'    => 'custom_css',
		'toggle_slug' => 'scroll_effects',
	);

	/**
	 * Retrieves default settings for sticky fields.
	 *
	 * @since 4.6.0
	 *
	 * @return array $settings Default settings.
	 */
	public function get_defaults() {
		return $this->defaults;
	}

	/**
	 * Retrieves default value of specific field settings
	 *
	 * @since 4.6.0
	 *
	 * @param string $name    Default name.
	 * @param string $default Default's default value.
	 *
	 * @return mixed
	 */
	public function get_default( $name = '', $default = '' ) {
		return et_()->array_get( $this->defaults, $name, $default );
	}

	/**
	 * Retrieves fields for sticky settings.
	 *
	 * @since 4.6.0
	 *
	 * @param  array $args   Associative array for settings.
	 *
	 * @return array $fields Option settings.
	 */
	public function get_fields( array $args = array() ) {
		static $i18n;

		// Cache translations.
		if ( ! isset( $i18n ) ) {
			$i18n = array(
				'position'           => array(
					'label'       => esc_html__( 'Sticky Position', 'et_builder' ),
					'description' => esc_html__( 'Choose to have this element remain a fixed distance from the top or bottom edge of the browser window as the user scrolls', 'et_builder' ),
					'options'     => array(
						'do_not_sticky'       => esc_html__( 'Do Not Stick', 'et_builder' ),
						'sticky_to_top'       => esc_html__( 'Stick to Top', 'et_builder' ),
						'stick_to_bottom'     => esc_html__( 'Stick to Bottom', 'et_builder' ),
						'stick_to_top_bottom' => esc_html__( 'Stick to Top and Bottom', 'et_builder' ),
					),
				),
				'offset_top'         => array(
					'label'       => esc_html__( 'Sticky Top Offset', 'et_builder' ),
					'description' => esc_html__( 'Define the vertical offset distance from the top edge of the browser window', 'et_builder' ),
				),
				'offset_bottom'      => array(
					'label'       => esc_html__( 'Sticky Bottom Offset', 'et_builder' ),
					'description' => esc_html__( 'Define the vertical offset distance from the bottom edge of the browser window', 'et_builder' ),
				),
				'limit_top'          => array(
					'label'       => esc_html__( 'Top Sticky Limit', 'et_builder' ),
					'description' => esc_html__( 'If defined, this element will stick to the top of this container, overriding its stickiness edge of the browser', 'et_builder' ),
				),
				'limit_bottom'       => array(
					'label'       => esc_html__( 'Bottom Sticky Limit', 'et_builder' ),
					'description' => esc_html__( 'If defined, this element will stick to the bottom of this container, overriding its stickiness to the edge of the browser', 'et_builder' ),
				),
				'offset_surrounding' => array(
					'label'       => esc_html__( 'Offset From Surrounding Sticky Elements', 'et_builder' ),
					'description' => esc_html__( 'Apply “Sticky Offset” to the nearest Sticky Element above or below this element', 'et_builder' ),
				),
				'transition'         => array(
					'label'       => esc_html__( 'Transition Default and Sticky Styles', 'et_builder' ),
					'description' => esc_html__( 'Enabled animated transitions between default and sticky styles when the element becomes “stuck”', 'et_builder' ),
				),
			);
		}

		$settings = array_merge( $this->get_defaults(), $args );
		$prefix   = $settings['prefix'];

		return array(
			"{$prefix}_position"           => array(
				'label'           => $i18n['position']['label'],
				'description'     => $i18n['position']['description'],
				'tab_slug'        => $settings['tab_slug'],
				'toggle_slug'     => $settings['toggle_slug'],
				'type'            => 'select',
				'options'         => array(
					'none'       => $i18n['position']['options']['do_not_sticky'],
					'top'        => $i18n['position']['options']['sticky_to_top'],
					'bottom'     => $i18n['position']['options']['stick_to_bottom'],
					'top_bottom' => $i18n['position']['options']['stick_to_top_bottom'],
				),
				'default'         => 'none',
				'option_category' => 'layout',
				'responsive'      => true,
				'mobile_options'  => true,
				'bb_support'      => false,
			),
			"{$prefix}_offset_top"         => array(
				'label'          => $i18n['offset_top']['label'],
				'description'    => $i18n['offset_top']['description'],
				'tab_slug'       => $settings['tab_slug'],
				'toggle_slug'    => $settings['toggle_slug'],
				'type'           => 'range',
				'range_settings' => array(
					'min'  => 0,
					'max'  => 400,
					'step' => 1,
				),
				'default'        => '0px',
				'show_if_not'    => array(
					"{$prefix}_position" => array( 'none', 'bottom' ),
				),
				'responsive'     => true,
				'mobile_options' => true,
				'bb_support'     => false,
			),
			"{$prefix}_offset_bottom"      => array(
				'label'          => $i18n['offset_bottom']['label'],
				'description'    => $i18n['offset_bottom']['description'],
				'tab_slug'       => $settings['tab_slug'],
				'toggle_slug'    => $settings['toggle_slug'],
				'type'           => 'range',
				'range_settings' => array(
					'min'  => 0,
					'max'  => 400,
					'step' => 1,
				),
				'default'        => '0px',
				'show_if_not'    => array(
					"{$prefix}_position" => array( 'none', 'top' ),
				),
				'responsive'     => true,
				'mobile_options' => true,
				'bb_support'     => false,
			),
			"{$prefix}_limit_top"          => array(
				'label'               => $i18n['limit_top']['label'],
				'description'         => $i18n['limit_top']['description'],
				'tab_slug'            => $settings['tab_slug'],
				'toggle_slug'         => $settings['toggle_slug'],
				'type'                => 'select',
				'options'             => $this->get_limit_options( $settings['module_slug'] ),
				'default'             => 'none',
				'depends_show_if_not' => array( 'top', 'none' ),
				'depends_on'          => array(
					"{$prefix}_position",
				),
				'responsive'          => true,
				'mobile_options'      => true,
				'bb_support'          => false,
			),
			"{$prefix}_limit_bottom"       => array(
				'label'               => $i18n['limit_bottom']['label'],
				'description'         => $i18n['limit_bottom']['description'],
				'tab_slug'            => $settings['tab_slug'],
				'toggle_slug'         => $settings['toggle_slug'],
				'type'                => 'select',
				'options'             => $this->get_limit_options( $settings['module_slug'] ),
				'default'             => 'none',
				'depends_show_if_not' => array( 'bottom', 'none' ),
				'depends_on'          => array(
					"{$prefix}_position",
				),
				'responsive'          => true,
				'mobile_options'      => true,
				'bb_support'          => false,
			),
			"{$prefix}_offset_surrounding" => array(
				'label'          => $i18n['offset_surrounding']['label'],
				'description'    => $i18n['offset_surrounding']['description'],
				'tab_slug'       => $settings['tab_slug'],
				'toggle_slug'    => $settings['toggle_slug'],
				'type'           => 'yes_no_button',
				'options'        => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default'        => 'on',
				'show_if_not'    => array(
					"{$prefix}_position" => 'none',
				),
				'responsive'     => true,
				'mobile_options' => true,
				'bb_support'     => false,
			),
			"{$prefix}_transition"         => array(
				'label'          => $i18n['transition']['label'],
				'description'    => $i18n['transition']['description'],
				'tab_slug'       => $settings['tab_slug'],
				'toggle_slug'    => $settings['toggle_slug'],
				'type'           => 'yes_no_button',
				'options'        => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default'        => 'on',
				'show_if_not'    => array(
					"{$prefix}_position" => 'none',
				),
				'responsive'     => true,
				'mobile_options' => true,
				'bb_support'     => false,
			),
		);
	}

	/**
	 * Get limit position options based on Element Type.
	 *
	 * @since 4.6.0
	 *
	 * @param string $module_slug Module Slug.
	 *
	 * @return array $options     Limit options.
	 */
	private function get_limit_options( $module_slug ) {
		static $i18n;

		// Cache translations.
		if ( ! isset( $i18n ) ) {
			$i18n = array(
				'none'    => esc_html__( 'None', 'et_builder' ),
				'body'    => esc_html__( 'Body Area', 'et_builder' ),
				'section' => esc_html__( 'Section', 'et_builder' ),
				'row'     => esc_html__( 'Row', 'et_builder' ),
				'column'  => esc_html__( 'Column', 'et_builder' ),
			);
		}

		$options = array(
			'none'    => $i18n['none'],
			'body'    => $i18n['body'],
			'section' => $i18n['section'],
			'row'     => $i18n['row'],
			'column'  => $i18n['column'],
		);

		if ( 'et_pb_column' === $module_slug ) {
			unset( $options['column'] );
		}

		if ( 'et_pb_row' === $module_slug ) {
			unset( $options['column'] );
			unset( $options['row'] );
		}

		if ( 'et_pb_section' === $module_slug ) {
			unset( $options['column'] );
			unset( $options['row'] );
			unset( $options['section'] );
		}

		return $options;
	}
}

return new ET_Builder_Module_Field_Sticky();
