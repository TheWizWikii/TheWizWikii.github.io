<?php

class ET_Builder_Module_Settings_Migration_BorderOptions extends ET_Builder_Module_Settings_Migration {

	public $version  = '3.0.87';
	public $defaults = array(
		'all_modules'         => array(
			'width' => '0px',
			'color' => '#ffffff',
			'style' => 'solid',
		),
		'post_based'          => array(
			'width' => '1px',
		),
		'toggles_and_tabs'    => array(
			'width' => '1px',
			'color' => '#d9d9d9',
			'style' => 'solid',
		),
		'et_pb_contact_form'  => array(
			'width' => '1px',
		),
		'et_pb_contact_field' => array(
			'width' => '14px',
			'color' => '#bbb',
		),
		'et_pb_blog'          => array(
			'width' => '1px',
			'color' => '#d8d8d8',
			'style' => 'solid',
		),
		'et_pb_signup'        => array(
			'width' => '1px',
		),
	);

	public function __construct() {
		parent::__construct();

		self::$_bb_excluded_name_changes[] = 'use_border_color';
		self::$_bb_excluded_name_changes[] = 'use_focus_border_color';
	}

	public function _get_border_style_default( $module_slug, $field_name ) {
		$field_name       = str_replace( 'border_', '', $field_name );
		$default_key      = 'all_modules';
		$toggles_and_tabs = array( 'et_pb_toggle', 'et_pb_accordion', 'et_pb_accordion_item', 'et_pb_tabs' );
		$post_based       = array( 'et_pb_portfolio', 'et_pb_filterable_portfolio', 'et_pb_gallery' );

		if ( in_array( $module_slug, $toggles_and_tabs ) ) {
			$default_key = 'toggles_and_tabs';
		} elseif ( in_array( $module_slug, $post_based ) ) {
			$default_key = 'post_based';
		} elseif ( isset( $this->defaults[ $module_slug ][ $field_name ] ) ) {
			$default_key = $module_slug;
		}

		if ( isset( $this->defaults[ $default_key ][ $field_name ] ) ) {
			$default = $this->defaults[ $default_key ][ $field_name ];
		} else {
			$default = $this->defaults['all_modules'][ $field_name ];
		}

		return $default;
	}

	public function get_modules( $group = '' ) {
		$modules = array();

		if ( in_array( $group, array( '', 'border' ) ) ) {
			$modules[] = 'et_pb_accordion';
			$modules[] = 'et_pb_audio';
			$modules[] = 'et_pb_counters';
			$modules[] = 'et_pb_blog';
			$modules[] = 'et_pb_blurb';
			$modules[] = 'et_pb_cta';
			$modules[] = 'et_pb_comments';
			$modules[] = 'et_pb_contact_form';
			$modules[] = 'et_pb_contact_field';
			$modules[] = 'et_pb_signup';
			$modules[] = 'et_pb_image';
			$modules[] = 'et_pb_login';
			$modules[] = 'et_pb_number_counter';
			$modules[] = 'et_pb_team_member';
			$modules[] = 'et_pb_post_nav';
			$modules[] = 'et_pb_post_title';
			$modules[] = 'et_pb_pricing_tables';
			$modules[] = 'et_pb_tabs';
			$modules[] = 'et_pb_testimonial';
			$modules[] = 'et_pb_text';
			$modules[] = 'et_pb_toggle';
			$modules[] = 'et_pb_fullwidth_image';
			$modules[] = 'et_pb_fullwidth_post_title';
			$modules[] = 'et_pb_filterable_portfolio';
			$modules[] = 'et_pb_gallery';
			$modules[] = 'et_pb_portfolio';
		}

		if ( in_array( $group, array( '', 'border_fullwidth' ) ) ) {
			$modules[] = 'et_pb_blog';
		}

		if ( in_array( $group, array( '', 'image_border' ) ) ) {
			$modules[] = 'et_pb_fullwidth_portfolio';
		}

		if ( in_array( $group, array( '', 'border_radius' ) ) ) {
			$modules[] = 'et_pb_counters';
		}

		if ( in_array( $group, array( '', 'input_border_radius' ) ) ) {
			$modules[] = 'et_pb_contact_form';
			$modules[] = 'et_pb_contact_field';
			$modules[] = 'et_pb_comments';
		}

		if ( in_array( $group, array( '', 'portrait_border_radius' ) ) ) {
			$modules[] = 'et_pb_testimonial';
		}

		if ( in_array( $group, array( '', 'use_focus_border_color' ) ) ) {
			$modules[] = 'et_pb_login';
			$modules[] = 'et_pb_signup';
		}

		if ( in_array( $group, array( '', 'link_shape' ) ) ) {
			$modules[] = 'et_pb_social_media_follow';
		}

		return array_unique( $modules );
	}

	public function get_fields() {
		return array(
			'border_color_all'              => array(
				'affected_fields' => array(
					'use_border_color' => $this->get_modules( 'border' ),
					'border_color'     => $this->get_modules( 'border' ),
				),
			),
			'border_width_all'              => array(
				'affected_fields' => array(
					'border_width' => $this->get_modules( 'border' ),
				),
			),
			'border_style_all'              => array(
				'affected_fields' => array(
					'border_style' => $this->get_modules( 'border' ),
				),
			),

			'border_color_all_fullwidth'    => array(
				'affected_fields' => array(
					'use_border_color' => $this->get_modules( 'border_fullwidth' ),
					'border_color'     => $this->get_modules( 'border_fullwidth' ),
				),
			),
			'border_width_all_fullwidth'    => array(
				'affected_fields' => array(
					'border_width' => $this->get_modules( 'border_fullwidth' ),
				),
			),
			'border_style_all_fullwidth'    => array(
				'affected_fields' => array(
					'border_style' => $this->get_modules( 'border_fullwidth' ),
				),
			),
			// migrate module image borders
			'border_color_all_image'        => array(
				'affected_fields' => array(
					'use_border_color' => $this->get_modules( 'image_border' ),
					'border_color'     => $this->get_modules( 'image_border' ),
				),
			),
			'border_width_all_image'        => array(
				'affected_fields' => array(
					'border_width' => $this->get_modules( 'image_border' ),
				),
			),
			'border_style_all_image'        => array(
				'affected_fields' => array(
					'border_style' => $this->get_modules( 'image_border' ),
				),
			),

			// migrate focus border color
			'border_color_all_fields_focus' => array(
				'affected_fields' => array(
					'use_focus_border_color' => $this->get_modules( 'use_focus_border_color' ),
					'use_border_color'       => $this->get_modules( 'use_focus_border_color' ),
					'focus_border_color'     => $this->get_modules( 'use_focus_border_color' ),
				),
			),
			'border_width_all_fields_focus' => array(
				'affected_fields' => array(
					'use_focus_border_color' => $this->get_modules( 'use_focus_border_color' ),
				),
			),
			'border_style_all_fields_focus' => array(
				'affected_fields' => array(
					'use_focus_border_color' => $this->get_modules( 'use_focus_border_color' ),
				),
			),

			'border_radii'                  => array(
				'affected_fields' => array(
					'border_radius'       => $this->get_modules( 'border_radius' ),
					'input_border_radius' => $this->get_modules( 'input_border_radius' ),
					'link_shape'          => $this->get_modules( 'link_shape' ),
				),
			),

			'border_radii_portrait'         => array(
				'affected_fields' => array(
					'portrait_border_radius' => $this->get_modules( 'portrait_border_radius' ),
				),
			),
		);
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		// Border Radius setting migration setting
		if ( in_array( $module_slug, $this->get_modules( 'border_radius' ) ) ) {
			if ( 'border_radius' === $saved_field_name ) {
				return $this->migrate_border_radius( $current_value );
			}
		}

		if ( in_array( $module_slug, $this->get_modules( 'input_border_radius' ) ) ) {
			if ( 'input_border_radius' === $saved_field_name ) {
				return $this->migrate_border_radius( $current_value );
			}
		}

		if ( in_array( $module_slug, $this->get_modules( 'portrait_border_radius' ) ) ) {
			if ( 'portrait_border_radius' === $saved_field_name ) {
				if ( ! empty( $current_value ) ) {
					return $this->migrate_border_radius( $current_value );
				}
			}
		}

		if ( in_array( $module_slug, $this->get_modules( 'link_shape' ) ) ) {
			if ( ( 'link_shape' === $saved_field_name ) ) {
				if ( $current_value === 'circle' ) {
					return 'on|100%|100%|100%|100%';
				}
			}
		}

		$focus_fields = array( 'border_width_all_fields_focus', 'border_style_all_fields_focus', 'border_color_all_fields_focus' );

		if ( in_array( $field_name, $focus_fields ) && in_array( $module_slug, $this->get_modules( 'use_focus_border_color' ) ) ) {
			if ( 'use_focus_border_color' !== $saved_field_name ) {
				return $saved_value;
			}

			if ( 'on' === $attrs['use_focus_border_color'] ) {
				switch ( $field_name ) {
					case 'border_width_all_fields_focus':
						$current_value = '1px';
						break;
					case 'border_style_all_fields_focus':
						$current_value = 'solid';
						break;
					case 'border_color_all_fields_focus':
						$color = '#ffffff';
						if ( isset( $attrs['focus_border_color'] ) ) {
							$color = $attrs['focus_border_color'];
						}
						$current_value = $color;
						break;
					default:
						$current_value = '';
				}

				return $current_value;
			}
		}

		if ( in_array( $saved_field_name, array( 'border_width', 'border_style', 'border_color' ) ) ) {
			if ( isset( $attrs['use_border_color'] ) && 'on' === $attrs['use_border_color'] ) {
				if ( '' === $current_value || 'default' === $current_value ) {
					$current_value = $this->_get_border_style_default( $module_slug, $saved_field_name );
				}

				return $current_value;
			} else {
				return '';
			}
		}

		return $saved_value;
	}

	private function migrate_border_radius( $radius_value ) {
		$value       = is_numeric( $radius_value ) ? $radius_value . 'px' : $radius_value;
		$value_array = array_fill( 0, 4, $value );

		return 'on|' . implode( '|', $value_array );
	}
}

return new ET_Builder_Module_Settings_Migration_BorderOptions();
