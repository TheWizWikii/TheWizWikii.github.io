<?php
/**
 * Migration for modules fields prior to UI Improvement Release. Some defaults are changes for better UI, these
 * migrations makes affected modules made prior UI Improvement to keep its current UI output
 *
 * @since 3.2
 */
class ET_Builder_Module_Settings_Migration_UIImprovement extends ET_Builder_Module_Settings_Migration {
	public $version = '3.2';

	public function get_modules( $group = '' ) {
		$modules = array();

		if ( in_array( $group, array( '', 'testimonial', 'background_layout', 'version' ) ) ) {
			$modules[] = 'et_pb_testimonial';
		}

		if ( in_array( $group, array( '', 'slide', 'background_color' ) ) ) {
			$modules[] = 'et_pb_slide';
		}

		if ( in_array( $group, array( '', 'post_slider', 'background_color' ) ) ) {
			$modules[] = 'et_pb_post_slider';
			$modules[] = 'et_pb_fullwidth_post_slider';
		}

		if ( in_array( $group, array( '', 'fullwidth_header', 'background_layout', 'background_color', 'version' ) ) ) {
			$modules[] = 'et_pb_fullwidth_header';
		}

		if ( in_array( $group, array( '', 'divider', 'version' ) ) ) {
			$modules[] = 'et_pb_divider';
		}

		return $modules;
	}

	public function get_fields() {
		return array(
			'background_layout' => array(
				'affected_fields' => array(
					'background_layout' => $this->get_modules( 'background_layout' ),
				),
			),
			'background_color'  => array(
				'affected_fields' => array(
					'background_color' => $this->get_modules( 'background_color' ),
				),
			),
			'color'             => array(
				'affected_fields' => array(
					'color' => $this->get_modules( 'divider' ),
				),
			),
			'show_divider'      => array(
				'affected_fields' => array(
					'show_divider' => $this->get_modules( 'divider' ),
				),
			),
			'_builder_version'  => array(
				'affected_fields' => array(
					'_builder_version' => $this->get_modules( 'version' ),
				),
			),
		);
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		$is_current_value_empty = '' === $current_value;

		if ( $is_current_value_empty ) {
			// Background Layout
			if ( 'background_layout' === $field_name ) {
				// Testimonial
				if ( 'et_pb_testimonial' === $module_slug ) {
					$current_value = 'dark';
				}

				if ( 'et_pb_fullwidth_header' === $module_slug ) {
					$current_value = 'light';
				}
			}

			// Background Color
			if ( 'background_color' === $field_name ) {
				if ( in_array( $module_slug, $this->get_modules( 'slide' ) ) ) {
					$current_value = '#ffffff';

					if ( et_builder_accent_color() === $saved_value ) {
						$current_value = '#ffffff';
					} else {
						$current_value = $saved_value;
					}
				}

				if ( in_array( $module_slug, $this->get_modules( 'post_slider' ) ) ) {
					if ( isset( $attrs['background_layout'] ) && 'light' === $attrs['background_layout'] ) {
						$current_value = '#f5f5f5';
					} else {
						$current_value = '#2ea3f2';
					}
				}

				// Migrated value cannot be empty string because it'll be filled by default on visual builder.
				// Thus for empty value, use rgba-based transparent white color
				if ( in_array( $module_slug, $this->get_modules( 'fullwidth_header' ) ) ) {
					$current_value = 'rgba(255, 255, 255, 0)';
				}
			}

			// Divider
			if ( in_array( $module_slug, $this->get_modules( 'divider' ) ) ) {
				if ( 'color' === $field_name ) {
					$current_value = '#ffffff';
				}

				// Divider visibility was automatically set to visible when module customizer for divider visibility is turned on in theme
				// Thus the migration only took effect on theme when module customizer for divider visibility isn't turn on
				$is_theme_module_customizer_unmodified = ! et_is_builder_plugin_active() && true === et_get_option( 'et_pb_divider-show_divider', false );

				if ( 'show_divider' === $field_name && ! $is_theme_module_customizer_unmodified ) {
					$current_value = 'off';
				}
			}
		}

		// Bump _builder_version if migrate() is called for visual builder data retrieval. This needs to be done
		// due to shortcode trimming. Migration originally added with migrating attribute value from a field_name to
		// different field_name in mind. When data is migrated to the same field_name and the value gets trimmed
		// due to it being identical to default value, what ends up happening is unexpected layout change when
		// layout is saved in VB because the builder version remains but the data changed and gets trimmed
		// because it is migrated to default value and it gets migrated again on the next round (due to builder
		// version remains to its original value)
		if ( $field_name === '_builder_version' && is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$current_value = $this->version;
		}

		return $current_value;
	}
}

return new ET_Builder_Module_Settings_Migration_UIImprovement();
