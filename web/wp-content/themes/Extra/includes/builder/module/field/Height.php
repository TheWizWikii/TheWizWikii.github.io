<?php

class ET_Builder_Module_Field_Height extends ET_Builder_Module_Field_Base {

	/**
	 * Translations.
	 *
	 * @var array
	 */
	protected $i18n = array();

	public function get_defaults() {
		return array(
			'prefix'         => '',
			'use_min_height' => true,
			'use_height'     => true,
			'use_max_height' => true,
		);
	}

	public function get_fields( array $args = array() ) {
		$settings = array_merge( $this->get_defaults(), $args );

		return array_merge(
			$this->get_min_height( $settings ),
			$this->get_height( $settings ),
			$this->get_max_height( $settings )
		);
	}

	private function get_min_height( $settings ) {
		if ( ! $settings['use_min_height'] ) {
			return array();
		}

		$helper = et_pb_min_height_options( $settings['prefix'] );

		$i18n =& $this->i18n;

		if ( ! isset( $i18n['minheight'] ) ) {
			// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment
			$i18n['minheight'] = array(
				'label'       => __( 'Min Height', 'et_builder' ),
				'description' => __( 'When a minimum height is set, the element will always have a height of at least the amount defined. This supersedes smaller static height values. Unlike height, minimum height does not result in content overflow and will allow the height of your element to remain dynamic.', 'et_builder' ),
			);
			// phpcs:enable
		}

		return array_merge(
			array(
				$helper->get_field() => array_merge(
					array(
						'label'          => $i18n['minheight']['label'],
						'description'    => $i18n['minheight']['description'],
						'default'        => 'auto',
						'default_tablet' => 'auto',
						'default_phone'  => 'auto',
						'allowed_values' => et_builder_get_acceptable_css_string_values( 'min-height' ),
						'range_settings' => array(
							'min'  => 100,
							'max'  => 1000,
							'step' => 1,
						),
					),
					$this->get_base_field()
				),
			),
			$this->responsive_fields( $helper->get_field() )
		);
	}

	private function get_height( $settings ) {
		if ( ! $settings['use_height'] ) {
			return array();
		}

		$helper = et_pb_height_options( $settings['prefix'] );

		$i18n =& $this->i18n;

		if ( ! isset( $i18n['height'] ) ) {
			// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment
			$i18n['height'] = array(
				'label'       => __( 'Height', 'et_builder' ),
				'description' => __( 'This sets a static height value for your element. Once set, the height of the element will no longer be determined by its inner content. Content that exceeds the static height of the element will overflow the element wrapper.', 'et_builder' ),
			);
			// phpcs:enable
		}

		return array_merge(
			array(
				$helper->get_field() => array_merge(
					array(
						'label'          => $i18n['height']['label'],
						'description'    => $i18n['height']['description'],
						'default'        => 'auto',
						'default_tablet' => 'auto',
						'default_phone'  => 'auto',
						'allowed_values' => et_builder_get_acceptable_css_string_values( 'height' ),
						'range_settings' => array(
							'min'  => 100,
							'max'  => 1000,
							'step' => 1,
						),
					),
					$this->get_base_field()
				),
			),
			$this->responsive_fields( $helper->get_field() )
		);
	}

	private function get_max_height( $settings ) {
		if ( ! $settings['use_max_height'] ) {
			return array();
		}

		$helper = et_pb_max_height_options( $settings['prefix'] );

		$i18n =& $this->i18n;

		if ( ! isset( $i18n['maxheight'] ) ) {
			// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment
			$i18n['maxheight'] = array(
				'label'       => __( 'Max Height', 'et_builder' ),
				'description' => __( 'Setting a maximum height will prevent your element from ever surpassing the defined height value. As your module content increases and surpasses the maximum height, it will overflow the element wrapper.', 'et_builder' ),
			);
			// phpcs:enable
		}

		return array_merge(
			array(
				$helper->get_field() => array_merge(
					array(
						'label'          => $i18n['maxheight']['label'],
						'description'    => $i18n['maxheight']['description'],
						'default'        => 'none',
						'default_tablet' => 'none',
						'default_phone'  => 'none',
						'allowed_values' => et_builder_get_acceptable_css_string_values( 'max-height' ),
						'range_settings' => array(
							'min'  => 100,
							'max'  => 1000,
							'step' => 1,
						),
					),
					$this->get_base_field()
				),
			),
			$this->responsive_fields( $helper->get_field() )
		);
	}

	private function get_base_field() {
		return array(
			'type'             => 'range',
			'hover'            => 'tabs',
			'default_on_child' => true,
			'mobile_options'   => true,
			'sticky'           => true,
			'validate_unit'    => true,
			'unitless'         => false,
			'default_unit'     => 'px',
			'allow_empty'      => true,
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'width',
		);
	}

	private function responsive_fields( $field ) {
		return array(
			"{$field}_tablet"      => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			"{$field}_phone"       => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
			"{$field}_last_edited" => array(
				'type'        => 'skip',
				'tab_slug'    => 'advanced',
				'toggle_slug' => 'width',
			),
		);
	}
}

return new ET_Builder_Module_Field_Height();
