<?php


class ET_Builder_Module_Settings_Migration_Animation extends ET_Builder_Module_Settings_Migration {

	public $version = '3.0.72';

	public function get_fields() {
		return array(
			'animation_style'           => array(
				'affected_fields' => array(
					'animation' => $this->get_modules( 'image' ),
				),
				'map'             => array(
					'off'     => 'none',
					'fade_in' => 'fade',
				),
			),
			'animation_duration'        => array(
				'affected_fields' => array(
					'animation' => $this->get_modules( 'image' ),
				),
			),
			'animation_intensity_slide' => array(
				'affected_fields' => array(
					'animation' => $this->get_modules( 'image' ),
				),
			),
			'animation_direction'       => array(
				'affected_fields' => array(
					'animation' => $this->get_modules( 'image' ),
				),
				'map'             => array(
					'off'     => '',
					'fade_in' => 'center',
					''        => 'left',
				),
			),
		);
	}

	public function get_modules( $group = '' ) {
		$modules = array();

		if ( in_array( $group, array( '', 'image' ) ) ) {
			$modules[] = 'et_pb_image';
			$modules[] = 'et_pb_fullwidth_image';
		}

		return $modules;
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		// Image & Fullwidth Image modules migration setting
		if ( in_array( $module_slug, $this->get_modules( 'image' ) ) ) {

			// Migrate 'animation' attribute value to new animation UI attributes.
			// The following migration setting is basically default value for image module's legacy animation setting
			if ( 'animation' === $saved_field_name ) {

				// Image module specific override
				if ( 'et_pb_image' === $module_slug ) {
					// Overwrite default animation if global setting is modified via customizer
					$global_animation_direction = ET_Global_Settings::get_value( 'et_pb_image-animation' );
					$default_animation          = $global_animation_direction && '' !== $global_animation_direction ? $global_animation_direction : 'left';

					if ( '' === $current_value ) {
						$current_value = $default_animation;
					}
				}

				// Animation is set to off on legacy animation setting
				$is_animation_off = 'off' === $current_value;

				// Set animation duration to 500ms
				if ( 'animation_duration' === $field_name && ! $is_animation_off ) {
					return '500ms';
				}

				// Set animation intensity to 10%
				if ( 'animation_intensity_slide' === $field_name && ! $is_animation_off ) {
					return '10%';
				}

				// Map animation style to provided configuration, otherwise it is 'slide' style
				if ( 'animation_style' === $field_name ) {
					return isset( $this->fields['animation_style']['map'][ $current_value ] ) ? $this->fields['animation_style']['map'][ $current_value ] : 'slide';
				}

				// Map animation direction to provided configuration, otherwise keep current value
				if ( 'animation_direction' === $field_name ) {
					return isset( $this->fields['animation_direction']['map'][ $current_value ] ) ? $this->fields['animation_direction']['map'][ $current_value ] : $current_value;
				}
			}
		}

		return $saved_value;
	}
}

return new ET_Builder_Module_Settings_Migration_Animation();
