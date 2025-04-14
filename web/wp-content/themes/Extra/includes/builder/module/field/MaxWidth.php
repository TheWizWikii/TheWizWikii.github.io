<?php

class ET_Builder_Module_Field_MaxWidth extends ET_Builder_Module_Field_Base {

	/**
	 * Translations.
	 *
	 * @var array
	 */
	protected $i18n = array();

	public function get_defaults() {
		return array(
			'prefix'               => '',
			'use_width'            => true,
			'use_max_width'        => true,
			'use_module_alignment' => true,
		);
	}

	public function get_fields( array $args = array() ) {
		$settings = array_merge( $this->get_defaults(), $args );

		return array_merge(
			$this->width_fields( $settings ),
			$this->max_width_fields( $settings ),
			$this->alignment_fields( $settings )
		);
	}

	private function width_fields( $settings ) {
		if ( ! $settings['use_width'] ) {
			return array();
		}

		$i18n =& $this->i18n;

		if ( ! isset( $i18n['width'] ) ) {
			// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment
			$i18n['width'] = array(
				'label'       => __( 'Width', 'et_builder' ),
				'description' => __( 'By default, elements will extend the full width of their parent element. If you would like to set a custom static width, you can do so using this option.', 'et_builder' ),
			);
			// phpcs:enable
		}

		$alignment  = new ET_Builder_Module_Helper_Alignment( $settings['prefix'] );
		$width      = new ET_Builder_Module_Helper_Width( $settings['prefix'] );
		$field_name = $width->get_field();
		$field      = array_merge(
			array(
				$field_name => array_merge(
					array(
						'label'          => $i18n['width']['label'],
						'description'    => $i18n['width']['description'],
						'default'        => 'auto',
						'default_tablet' => 'auto',
						'allowed_values' => et_builder_get_acceptable_css_string_values( 'width' ),
					),
					$this->get_base_field()
				),
			),
			$this->responsive_fields( $field_name )
		);

		if ( $settings['use_module_alignment'] ) {
			$field[ $field_name ]['responsive_affects'] = array( $alignment->get_field() );
		}

		return $field;
	}

	private function max_width_fields( $settings ) {
		if ( ! $settings['use_max_width'] ) {
			return array();
		}

		$i18n =& $this->i18n;

		if ( ! isset( $i18n['maxwidth'] ) ) {
			// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment
			$i18n['maxwidth'] = array(
				'label'       => __( 'Max Width', 'et_builder' ),
				'description' => __( 'Setting a maximum width will prevent your element from ever surpassing the defined width value. Maximum width can be used in combination with the standard width setting. Maximum width supersedes the normal width value.', 'et_builder' ),
			);
			// phpcs:enable
		}

		$alignment  = new ET_Builder_Module_Helper_Alignment( $settings['prefix'] );
		$max_width  = new ET_Builder_Module_Helper_Max_Width( $settings['prefix'] );
		$field_name = $max_width->get_field();
		$field      = array_merge(
			array(
				$field_name => array_merge(
					array(
						'label'          => $i18n['maxwidth']['label'],
						'description'    => $i18n['maxwidth']['description'],
						'default'        => 'none',
						'default_tablet' => 'none',
						'allowed_values' => et_builder_get_acceptable_css_string_values( 'max-width' ),
					),
					$this->get_base_field()
				),
			),
			$this->responsive_fields( $field_name )
		);

		if ( $settings['use_module_alignment'] ) {
			$field[ $field_name ]['responsive_affects'] = array( $alignment->get_field() );
		}

		return $field;
	}

	private function alignment_fields( $settings ) {
		if ( ! $settings['use_module_alignment'] ) {
			return array();
		}

		$i18n =& $this->i18n;

		if ( ! isset( $i18n['alignment'] ) ) {
			// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment
			$i18n['alignment'] = array(
				'label'       => esc_html__( 'Module Alignment', 'et_builder' ),
				'description' => esc_html__( 'Align the module to the left, right or center.', 'et_builder' ),
			);
			// phpcs:enable
		}

		$width              = new ET_Builder_Module_Helper_Width( $settings['prefix'] );
		$max_width          = new ET_Builder_Module_Helper_Max_Width( $settings['prefix'] );
		$alignment          = new ET_Builder_Module_Helper_Alignment( $settings['prefix'] );
		$field_name         = $alignment->get_field();
		$depends            = array();
		$depends_responsive = array();
		$field              = array(
			'label'           => $i18n['alignment']['label'],
			'description'     => $i18n['alignment']['description'],
			'type'            => 'align',
			'option_category' => 'layout',
			'options'         => et_builder_get_text_orientation_options( array( 'justified' ) ),
			'tab_slug'        => 'advanced',
			'toggle_slug'     => 'width',
			'mobile_options'  => true,
			'show_if_not'     => array(
				'positioning' => array( 'absolute', 'fixed' ),
			),
		);

		if ( $settings['use_width'] ) {
			array_push( $depends, $width->get_field() );
			array_push( $depends_responsive, $width->get_field() );
		}

		if ( $settings['use_max_width'] ) {
			array_push( $depends, $max_width->get_field() );
			array_push( $depends_responsive, $max_width->get_field() );
		}

		if ( $settings['use_width'] || $settings['use_max_width'] ) {
			$field['depends_show_if_not'] = array( '', '100%', 'auto', 'none' );
		}

		if ( ! empty( $depends ) ) {
			$field['depends_on']            = $depends;
			$field['depends_on_responsive'] = $depends_responsive;
		}

		return array( $field_name => $field );
	}

	private function get_base_field() {
		return array(
			'type'             => 'range',
			'hover'            => 'tabs',
			'default_on_child' => true,
			'mobile_options'   => true,
			'sticky'           => true,
			'validate_unit'    => true,
			'default_unit'     => '%',
			'allow_empty'      => true,
			'tab_slug'         => 'advanced',
			'toggle_slug'      => 'width',
			'range_settings'   => array(
				'min'  => 0,
				'max'  => 100,
				'step' => 1,
			),
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

return new ET_Builder_Module_Field_MaxWidth();
