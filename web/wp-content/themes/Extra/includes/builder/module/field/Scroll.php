<?php

class ET_Builder_Module_Field_Scroll extends ET_Builder_Module_Field_Base {

	/**
	 * Translations.
	 *
	 * @var array
	 */
	protected $i18n = array();

	public function get_defaults() {
		$i18n =& $this->i18n;

		if ( ! isset( $i18n['defaults'] ) ) {
			// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment
			$i18n['defaults'] = array(
				'label'       => esc_html__( 'Scroll Transform Effects', 'et_builder' ),
				'description' => esc_html__( 'Using Scrolling Effects, you can transform elements on your page as you scroll. The animation\'s transition is based on the user\'s scrolling behavior. Once the element enters the browser viewport (top), the animation begins, and it once it leaves the viewport (bottom), the animation ends.', 'et_builder' ),
			);
			// phpcs:enable
		}

		return array(
			'prefix'      => '',
			'label'       => $i18n['defaults']['label'],
			'description' => $i18n['defaults']['description'],
			'tab_slug'    => 'custom_css',
			'toggle_slug' => 'scroll_effects',
			'options'     => array(),
		);
	}

	public function get_fields( array $args = array() ) {
		$settings           = array_merge( $this->get_defaults(), $args );
		$prefix             = $settings['prefix'];
		$grid_support       = $settings['grid_support'];
		$name               = et_builder_add_prefix( $prefix, 'scroll_effects' );
		$grid_motion_toggle = array();

		$i18n =& $this->i18n;

		if ( ! isset( $i18n['grid'] ) ) {
			// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment
			$i18n['grid']    = array(
				'label'       => esc_html__( 'Apply Motion Effects To Child Elements', 'et_builder' ),
				'description' => esc_html__( 'This applies motion effects to individual elements within the module rather than the module as a whole. For example, to each image within a Gallery, rather than the Gallery container.', 'et_builder' ),
			);
			$i18n['trigger'] = array(
				'label'       => esc_html__( 'Motion Effect Trigger', 'et_builder' ),
				'description' => esc_html__( 'Here you can choose when motion effects are triggered: When the top of the element enters into view, when the middle of the element enters into view, or when the bottom of the element enters into view.', 'et_builder' ),
				'options'     => array(
					'middle' => esc_html__( 'Middle of Element', 'et_builder' ),
					'top'    => esc_html__( 'Top of Element', 'et_builder' ),
					'bottom' => esc_html__( 'Bottom of Element', 'et_builder' ),
				),
			);
			// phpcs:enable
		}

		if ( 'yes' === $grid_support ) {
			$grid_motion_toggle = array(
				'enable_grid_motion' => array(
					'label'           => $i18n['grid']['label'],
					'description'     => $i18n['grid']['description'],
					'type'            => 'yes_no_button',
					'option_category' => 'configuration',
					'options'         => array(
						'off' => et_builder_i18n( 'No' ),
						'on'  => et_builder_i18n( 'Yes' ),
					),
					'default'         => 'off',
					'tab_slug'        => $settings['tab_slug'],
					'toggle_slug'     => $settings['toggle_slug'],
					'bb_support'      => false,
				),
			);
		}

		$motion_triggers = array(
			'motion_trigger_start' => array(
				'label'           => $i18n['trigger']['label'],
				'description'     => $i18n['trigger']['description'],
				'options'         => array(
					'middle' => $i18n['trigger']['options']['middle'],
					'top'    => $i18n['trigger']['options']['top'],
					'bottom' => $i18n['trigger']['options']['bottom'],
				),
				'type'            => 'select',
				'option_category' => 'configuration',
				'default'         => 'middle',
				'tab_slug'        => $settings['tab_slug'],
				'toggle_slug'     => $settings['toggle_slug'],
				'bb_support'      => false,
			),
		);

		return array_merge(
			$grid_motion_toggle,
			array(
				$name => array(
					'label'               => $settings['label'],
					'description'         => $settings['description'],
					'tab_slug'            => $settings['tab_slug'],
					'toggle_slug'         => $settings['toggle_slug'],
					'attr_suffix'         => '',
					'type'                => 'composite',
					'option_category'     => 'layout',
					'composite_type'      => 'default',
					'composite_structure' => $this->get_options( $settings['options'], $settings['tab_slug'], $settings['toggle_slug'], $prefix ),
					'bb_support'          => false,
				),
			),
			$motion_triggers
		);
	}

	private function get_options( array $options_settings, $tab, $toggle, $prefix = '' ) {
		$options = array();

		$i18n =& $this->i18n;

		if ( ! isset( $i18n['options'] ) ) {
			// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment
			$i18n['options'] = array(
				'enable'           => __( 'Enable %s', 'et_builder' ),
				'set'              => __( 'Set %s', 'et_builder' ),
				'startTitle'       => esc_html__( 'Viewport Bottom', 'et_builder' ),
				'endTitle'         => esc_html__( 'Viewport Top', 'et_builder' ),
				'startValueTitle'  => esc_html__( 'Starting', 'et_builder' ),
				'middleValueTitle' => esc_html__( 'Mid', 'et_builder' ),
				'endValueTitle'    => esc_html__( 'Ending', 'et_builder' ),
			);
			// phpcs:enable
		}

		foreach ( $options_settings as $name => $settings ) {
			$option_name = et_builder_add_prefix( $prefix, $name );
			$icon        = et_()->array_get( $settings, 'icon', '' );
			$label       = et_()->array_get( $settings, 'label', '' );
			$description = et_()->array_get( $settings, 'description' );
			$default     = et_()->array_get( $settings, 'default' );
			$resolver    = et_()->array_get( $settings, 'resolver' );

			$options[ $option_name ] = array(
				'icon'     => $icon,
				'tooltip'  => $label,
				'controls' => array(
					"{$option_name}_enable" => array(
						'label'            => et_esc_html_once( $i18n['options']['enable'], $label ),
						'description'      => et_esc_html_once( $description ),
						'type'             => 'yes_no_button',
						'option_category'  => 'configuration',
						'options'          => array(
							'off' => et_builder_i18n( 'No' ),
							'on'  => et_builder_i18n( 'Yes' ),
						),
						'default'          => 'off',
						'tab_slug'         => $tab,
						'toggle_slug'      => $toggle,
						'main_tab_setting' => 'on',
					),
					$option_name            => array(
						'label'          => et_esc_html_once( $i18n['options']['set'], $label ),
						'description'    => et_esc_html_once( $description ),
						'type'           => 'motion',
						'default'        => $default,
						'tab_slug'       => $tab,
						'toggle_slug'    => $toggle,
						'mobile_options' => true,
						'show_if'        => array(
							"{$option_name}_enable" => 'on',
						),
						'resolver'       => $resolver,
						'i10n'           => array(
							'startTitle'       => $i18n['options']['startTitle'],
							'endTitle'         => $i18n['options']['endTitle'],
							'startValueTitle'  => et_()->array_get( $settings, 'startValueTitle', $i18n['options']['startValueTitle'] ),
							'middleValueTitle' => et_()->array_get( $settings, 'middleValueTitle', $i18n['options']['middleValueTitle'] ),
							'endValueTitle'    => et_()->array_get( $settings, 'endValueTitle', $i18n['options']['endValueTitle'] ),
						),
					),
				),
			);
		}

		return $options;
	}
}

return new ET_Builder_Module_Field_Scroll();
