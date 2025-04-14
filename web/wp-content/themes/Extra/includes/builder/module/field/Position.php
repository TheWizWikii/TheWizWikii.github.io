<?php

class ET_Builder_Module_Field_Position extends ET_Builder_Module_Field_Base {

	const TAB_SLUG    = 'custom_css';
	const TOGGLE_SLUG = 'position_fields';

	/**
	 * @var ET_Builder_Element
	 */
	private $module;

	public function get_fields( array $args = array() ) {

		$responsive_options = array();
		$additional_options = array();
		$skip               = array(
			'type'        => 'skip',
			'tab_slug'    => self::TAB_SLUG,
			'toggle_slug' => self::TOGGLE_SLUG,
		);

		static $i18n;

		if ( ! isset( $i18n ) ) {
			// phpcs:disable WordPress.WP.I18n.MissingTranslatorsComment
			$i18n = array(
				'positioning' => array(
					'description' => esc_html__( 'Here you can choose the element\'s position type. Absolutlely positioned elements will float inside their parent elements. Fixed positioned elements will float within the browser viewport. Relatively positioned elements sit statically in their parent container, but can still be offset without disrupting surrounding elements.', 'et_builder' ),
					'options'     => array(
						'relative' => esc_html__( 'Relative', 'et_builder' ),
						'absolute' => esc_html__( 'Absolute', 'et_builder' ),
						'fixed'    => esc_html__( 'Fixed', 'et_builder' ),
					),
				),
				'origin'      => array(
					'label'       => esc_html__( 'Location', 'et_builder' ),
					'description' => esc_html__( 'Here you can adjust the element\'s starting location within its parent container. You can further adjust the element\'s position using the offset controls.', 'et_builder' ),
				),
				'offset'      => array(
					'label'       => esc_html__( 'Offset Origin ', 'et_builder' ),
					'description' => esc_html__( 'Here you can choose from which corner this element is offset from. The vertical and horizontal offset adjustments will be affected based on the element\'s offset origin.', 'et_builder' ),
				),
				'vertical'    => array(
					'label'       => esc_html__( 'Vertical Offset', 'et_builder' ),
					'description' => esc_html__( 'Here you can adjust the element\'s position upwards or downwards from its starting location, which may differ based on its offset origin.', 'et_builder' ),
				),
				'horizontal'  => array(
					'label'       => esc_html__( 'Horizontal Offset', 'et_builder' ),
					'description' => esc_html__( 'Here you can adjust the element\'s position left or right from its starting location, which may differ based on its offset origin.', 'et_builder' ),
				),
				'zindex'      => array(
					'label'       => esc_html__( 'Z Index', 'et_builder' ),
					'description' => esc_html__( 'Here you can control element position on the z axis. Elements with higher z-index values will sit atop elements with lower z-index values.', 'et_builder' ),
				),
			);
			// phpcs:enable
		}

		if ( ! $args['hide_position_fields'] ) {

			$corner_options = array(
				'top_left'     => et_builder_i18n( 'Top Left' ),
				'top_right'    => et_builder_i18n( 'Top Right' ),
				'bottom_left'  => et_builder_i18n( 'Bottom Left' ),
				'bottom_right' => et_builder_i18n( 'Bottom Right' ),
			);

			$center_options = array(
				'center_left'   => et_builder_i18n( 'Center Left' ),
				'center_center' => et_builder_i18n( 'Center Center' ),
				'center_right'  => et_builder_i18n( 'Center Right' ),
				'top_center'    => et_builder_i18n( 'Top Center' ),
				'bottom_center' => et_builder_i18n( 'Bottom Center' ),
			);

			$additional_options['positioning'] = array(
				'label'             => et_builder_i18n( 'Position' ),
				'description'       => $i18n['positioning']['description'],
				'type'              => 'select',
				'options'           => array(
					'none'     => et_builder_i18n( 'Default' ),
					'relative' => $i18n['positioning']['options']['relative'],
					'absolute' => $i18n['positioning']['options']['absolute'],
					'fixed'    => $i18n['positioning']['options']['fixed'],
				),
				'option_category'   => 'layout',
				'default'           => $args['defaults']['positioning'],
				'default_on_child'  => true,
				'tab_slug'          => self::TAB_SLUG,
				'toggle_slug'       => self::TOGGLE_SLUG,
				'mobile_options'    => true,
				'sticky'            => true,
				'hover'             => 'tabs',
				'bb_support'        => false,
				'linked_responsive' => array( 'position_origin_a', 'position_origin_f', 'position_origin_r' ),
			);

			// Position origin/location options
			$origin_option = array(
				'label'            => $i18n['origin']['label'],
				'description'      => $i18n['origin']['description'],
				'type'             => 'position',
				'options'          => $corner_options + $center_options,
				'option_category'  => 'layout',
				'default'          => $args['defaults']['position_origin'],
				'default_on_child' => true,
				'tab_slug'         => self::TAB_SLUG,
				'toggle_slug'      => self::TOGGLE_SLUG,
				'mobile_options'   => true,
				'sticky'           => true,
				'hover'            => 'tabs',
				'bb_support'       => false,
			);

			// For absolute position
			$additional_options['position_origin_a']                      = $origin_option;
			$additional_options['position_origin_a']['linked_responsive'] = array( 'positioning', 'position_origin_f', 'position_origin_r' );

			// For fixed position
			$additional_options['position_origin_f']                      = $origin_option;
			$additional_options['position_origin_f']['linked_responsive'] = array( 'positioning', 'position_origin_a', 'position_origin_r' );

			// For relative position
			$additional_options['position_origin_r']                      = $origin_option;
			$additional_options['position_origin_r']['label']             = $i18n['offset']['label'];
			$additional_options['position_origin_r']['description']       = $i18n['offset']['description'];
			$additional_options['position_origin_r']['options']           = $corner_options;
			$additional_options['position_origin_r']['linked_responsive'] = array( 'positioning', 'position_origin_f', 'position_origin_a' );

			// Offset options
			$offset_option = array(
				'type'             => 'range',
				'range_settings'   => array(
					'min'  => -1000,
					'max'  => 1000,
					'step' => 1,
				),
				'option_category'  => 'layout',
				'default_unit'     => 'px',
				'default_on_child' => true,
				'tab_slug'         => self::TAB_SLUG,
				'toggle_slug'      => self::TOGGLE_SLUG,
				'responsive'       => true,
				'mobile_options'   => true,
				'sticky'           => true,
				'hover'            => 'tabs',
			);

			$additional_options['vertical_offset']                = $offset_option;
			$additional_options['vertical_offset']['default']     = $args['defaults']['vertical_offset'];
			$additional_options['vertical_offset']['label']       = $i18n['vertical']['label'];
			$additional_options['vertical_offset']['description'] = $i18n['vertical']['description'];

			$additional_options['horizontal_offset']                = $offset_option;
			$additional_options['horizontal_offset']['default']     = $args['defaults']['horizontal_offset'];
			$additional_options['horizontal_offset']['label']       = $i18n['horizontal']['label'];
			$additional_options['horizontal_offset']['description'] = $i18n['horizontal']['description'];

			$responsive_options += array(
				'vertical_offset',
				'horizontal_offset',
				'position_origin_a',
				'position_origin_f',
				'position_origin_r',
			);
		}

		if ( ! $args['hide_z_index_fields'] ) {
			$additional_options['z_index'] = array(
				'label'            => $i18n['zindex']['label'],
				'description'      => $i18n['zindex']['description'],
				'type'             => 'range',
				'range_settings'   => array(
					'min'  => -500,
					'max'  => 500,
					'step' => 1,
				),
				'option_category'  => 'layout',
				'default'          => $args['defaults']['z_index'],
				'default_on_child' => true,
				'tab_slug'         => self::TAB_SLUG,
				'toggle_slug'      => self::TOGGLE_SLUG,
				'allowed_values'   => et_builder_get_acceptable_css_string_values( 'z-index' ),
				'unitless'         => true,
				'hover'            => 'tabs',
				'sticky'           => true,
				'responsive'       => true,
				'mobile_options'   => true,
			);

			$responsive_options += array(
				'z_index',
			);
		}

		foreach ( $responsive_options as $option ) {
			$additional_options[ "{$option}_tablet" ]      = $skip;
			$additional_options[ "{$option}_phone" ]       = $skip;
			$additional_options[ "{$option}_last_edited" ] = $skip;
		}

		return $additional_options;
	}

	// Processing functions

	/**
	 * @param object $module Current module to be processed
	 */
	public function set_module( $module ) {
		$this->module = $module;
	}

	/**
	 * Interpreter of ET_Builder_Element::get_media_query
	 *
	 * @param string $view
	 *
	 * @return array
	 */
	public function get_media_query( $view ) {
		$media_query = array();
		if ( 'tablet' === $view ) {
			$media_query = array(
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_980' ),
			);
		} elseif ( 'phone' === $view ) {
			$media_query = array(
				'media_query' => ET_Builder_Element::get_media_query( 'max_width_767' ),
			);
		}

		return $media_query;
	}

	/**
	 * @param array  $attrs
	 * @param string $name
	 * @param string $desktopDefault
	 * @param string $view
	 *
	 * @return mixed
	 */
	private function get_default( $attrs, $name, $desktopDefault = '', $view = 'desktop' ) {
		$utils      = ET_Core_Data_Utils::instance();
		$responsive = ET_Builder_Module_Helper_ResponsiveOptions::instance();
		$suffix     = in_array( $view, array( 'tablet', 'phone' ) ) ? "_$view" : '';
		if ( 'hover' === $view ) {
			return $utils->array_get( $attrs, $name, $desktopDefault );
		}

		return $responsive->get_default_value( $attrs, "$name$suffix", $desktopDefault );
	}

	/**
	 * @param array  $attrs
	 * @param string $name
	 * @param string $default_value
	 * @param string $view
	 * @param bool   $force_return
	 *
	 * @return mixed
	 */
	private function get_value( $attrs, $name, $default_value = '', $view = 'desktop', $force_return = false ) {
		// Sticky style.
		if ( 'sticky' === $view ) {
			$sticky = et_pb_sticky_options();

			return $sticky->get_value( $name, $attrs, $default_value );
		}

		$utils         = ET_Core_Data_Utils::instance();
		$responsive    = ET_Builder_Module_Helper_ResponsiveOptions::instance();
		$hover         = et_pb_hover_options();
		$is_hover      = 'hover' === $view;
		$field_device  = $is_hover ? 'desktop' : $view;
		$field_name    = $is_hover ? $hover->get_hover_field( $name ) : $name;
		$field_default = $is_hover ? $utils->array_get( $attrs, $name, $default_value ) : $default_value;

		return $responsive->get_any_value( $attrs, $field_name, $field_default, $force_return, $field_device );
	}

	/**
	 * Get exposed module settings for assisting layout block preview rendering
	 *
	 * @since 4.3.2
	 *
	 * @param string $function_name
	 *
	 * @return null|array
	 */
	public function get_layout_block_settings( $function_name ) {
		$position_fields     = et_()->array_get( $this->module->advanced_fields, self::TOGGLE_SLUG, array() );
		$has_position_fields = is_array( $position_fields );

		// Bail if current module has no position fields
		if ( ! $has_position_fields ) {
			return;
		}

		$props      = $this->module->props;
		$responsive = et_pb_responsive_options();
		$hover      = et_pb_hover_options();

		// Position values
		$position = $responsive->is_responsive_enabled( $props, 'positioning' ) ?
			$responsive->get_property_values( $props, 'positioning', '', true ) :
			array(
				'desktop' => $responsive->get_desktop_value( 'positioning', $props ),
			);

		if ( $hover->is_enabled( 'positioning', $props ) ) {
			$position['hover'] = $hover->get_value( 'positioning', $props );
		}

		// Bail if current module is not fixed positioning on any breakpoint/mode
		if ( ! in_array( 'fixed', $position ) ) {
			return;
		}

		// Position fixed origin values
		$position_fixed_origin = $responsive->is_responsive_enabled( $props, 'position_origin_f' ) ?
			$responsive->get_property_values( $props, 'position_origin_f', '', true ) :
			array(
				'desktop' => $responsive->get_desktop_value( 'position_origin_f', $props ),
			);

		if ( $hover->is_enabled( 'position_origin_f', $props ) ) {
			$position_fixed_origin['hover'] = $hover->get_value( 'position_origin_f', $props );
		}

		// Vertical offset
		$vertical_offset = $responsive->is_responsive_enabled( $props, 'vertical_offset' ) ?
			$responsive->get_property_values( $props, 'vertical_offset', '', true ) :
			array(
				'desktop' => $responsive->get_desktop_value( 'vertical_offset', $props ),
			);

		if ( $hover->is_enabled( 'vertical_offset', $props ) ) {
			$vertical_offset['hover'] = $hover->get_value( 'vertical_offset', $props );

			// Offset rendering relies on origin position. Thus if position origin has
			// no hover value, set desktop as hover value to trigger adjustment rendering
			if ( ! isset( $position_fixed_origin['hover'] ) ) {
				$position_fixed_origin['hover'] = $position_fixed_origin['desktop'];
			}
		}

		// Horizontal offset
		$horizontal_offset = $responsive->is_responsive_enabled( $props, 'horizontal_offset' ) ?
			$responsive->get_property_values( $props, 'horizontal_offset', '', true ) :
			array(
				'desktop' => $responsive->get_desktop_value( 'horizontal_offset', $props ),
			);

		if ( $hover->is_enabled( 'horizontal_offset', $props ) ) {
			$horizontal_offset['hover'] = $hover->get_value( 'horizontal_offset', $props );

			// Offset rendering relies on origin position. Thus if position origin has
			// no hover value, set desktop as hover value to trigger adjustment rendering
			if ( ! isset( $position_fixed_origin['hover'] ) ) {
				$position_fixed_origin['hover'] = $position_fixed_origin['desktop'];
			}
		}

		// Return current module's position and position origin settings
		return array(
			'position'              => $position,
			'position_fixed_origin' => $position_fixed_origin,
			'vertical_offset'       => $vertical_offset,
			'horizontal_offset'     => $horizontal_offset,
		);
	}

	/**
	 * @param string $function_name
	 *
	 * @since 4.6.0 Add sticky style support.
	 */
	public function process( $function_name ) {
		$utils           = ET_Core_Data_Utils::instance();
		$hover           = et_pb_hover_options();
		$sticky          = et_pb_sticky_options();
		$responsive      = ET_Builder_Module_Helper_ResponsiveOptions::instance();
		$position_config = $utils->array_get( $this->module->advanced_fields, self::TOGGLE_SLUG, array() );
		$z_index_config  = $utils->array_get( $this->module->advanced_fields, 'z_index', array() );

		$props = $this->module->props;

		$this->module->set_position_locations( array() );

		if ( ! is_array( $z_index_config ) && ! is_array( $position_config ) ) {
			return;
		}

		$has_z_index  = false;
		$has_position = false;

		// z_index processing
		if ( is_array( $z_index_config ) ) {
			$z_index_selector = $utils->array_get( $z_index_config, 'css.main', '%%order_class%%' );
			$z_index_default  = $utils->array_get( $z_index_config, 'default', '' );
			$z_important      = $utils->array_get( $z_index_config, 'important', false ) !== false ? ' !important' : '';
			$views            = array( 'desktop' );

			if ( $hover->is_enabled( 'z_index', $props ) ) {
				array_push( $views, 'hover' );
			}

			if ( $responsive->is_responsive_enabled( $props, 'z_index' ) ) {
				array_push( $views, 'tablet', 'phone' );
			}

			// If the module is sticky or inside a sticky module, we need to add z-index for sticky state
			// with an `!important` flag to override sticky's default inline z-index: 10000 value when module enters sticky state.
			if ( $sticky->is_sticky_module( $props ) || $sticky->is_inside_sticky_module() ) {
				array_push( $views, 'sticky' );
			}

			foreach ( $views as $type ) {
				$value = $this->get_value( $props, 'z_index', $z_index_default, $type, false );

				if ( 'sticky' === $type ) {
					$desktop_value = $this->get_value( $props, 'z_index', $z_index_default, 'desktop', false );
					$value         = $sticky->get_value( 'z_index', $props, $desktop_value );
					$z_important   = ' !important';
				}

				if ( '' !== $value ) {
					$type_selector = $z_index_selector;

					if ( 'hover' === $type ) {
						$type_selector = $hover->add_hover_to_selectors( $z_index_selector );
					}

					if ( 'sticky' === $type ) {
						$type_selector = $sticky->add_sticky_to_selectors( $z_index_selector, $sticky->is_sticky_module( $props ) );
					}

					$el_style = array(
						'selector'    => $type_selector,
						'declaration' => "z-index: $value$z_important;",
						'priority'    => $this->module->get_style_priority(),
					) + $this->get_media_query( $type );
					ET_Builder_Element::set_style( $function_name, $el_style );
					$has_z_index = true;
				}
			}
		}

		if ( is_array( $position_config ) ) {
			$position_selector    = $utils->array_get( $position_config, 'css.main', '%%order_class%%' );
			$position_default     = $utils->array_get( $position_config, 'default', 'none' );
			$position_important   = $utils->array_get( $position_config, 'important', false ) !== false ? ' !important' : '';
			$desktop_origin_value = 'top_left';

			$views = array( 'desktop' );

			if ( $hover->is_enabled( 'positioning', $props ) ) {
				array_push( $views, 'hover' );
			}

			if ( $responsive->is_responsive_enabled( $props, 'positioning' ) ) {
				array_push( $views, 'tablet', 'phone' );
			}

			if ( $sticky->is_inside_sticky_module() && $sticky->is_enabled( 'positioning', $props ) ) {
				array_push( $views, 'sticky' );
			}

			$position_origins = array();
			foreach ( $views as $type ) {
				$value          = $this->get_value( $props, 'positioning', $position_default, $type, true );
				$default_value  = $this->get_default( $props, 'positioning', $position_default, $type );
				$important      = in_array( $value, array( 'fixed', 'absolute' ) ) || ( 'desktop' != $type ) ? ' !important' : $position_important;
				$position_value = $value;
				$is_parallax_on = 'on' === $this->get_value( $props, 'parallax' ) ? true : false;
				$is_divider_set = isset( $props['top_divider_style'] ) || isset( $props['bottom_divider_style'] );

				// When parallax or divider is enabled on the element and the position value
				// is set to none skip because it should always be relative.
				if ( 'none' === $value && ( $is_parallax_on || $is_divider_set ) ) {
					continue;
				}

				if ( 'none' === $value ) {
					// none is interpreted as static in FE.
					$position_value            = 'static';
					$important                 = ' !important';
					$position_origins[ $type ] = 'none';
				} else {
					$suffix                    = sprintf( '_%s', substr( $value, 0, 1 ) );
					$position_origins[ $type ] = $this->get_value( $props, "position_origin$suffix", 'top_left', $type, true );
				}
				if ( $default_value === $value ) {
					$position_origins[ $type ] .= '_is_default';
				}
				if ( strpos( $position_origins[ $type ], '_is_default' ) === false ) {
					$type_selector = 'hover' === $type ? "{$position_selector}:hover" : $position_selector;

					$el_style = array(
						'selector'    => $type_selector,
						'declaration' => "position: $position_value$important;",
						'priority'    => $this->module->get_style_priority(),
					) + $this->get_media_query( $type );
					ET_Builder_Element::set_style( $function_name, $el_style );

					$has_position = true;
				}
			}

			$resp_status = array(
				'horizontal' => $responsive->get_responsive_status( $utils->array_get( $props, 'horizontal_offset_last_edited', false ) ),
				'vertical'   => $responsive->get_responsive_status( $utils->array_get( $props, 'vertical_offset_last_edited', false ) ),
			);

			$hover_status = array(
				'horizontal' => $hover->is_enabled( 'horizontal_offset', $props ),
				'vertical'   => $hover->is_enabled( 'vertical_offset', $props ),
			);

			$sticky_status = array(
				'horizontal' => $sticky->is_inside_sticky_module() ? $sticky->is_enabled( 'horizontal_offset', $props ) : false,
				'vertical'   => $sticky->is_inside_sticky_module() ? $sticky->is_enabled( 'vertical_offset', $props ) : false,
			);

			if ( $resp_status['horizontal'] || $resp_status['vertical'] ) {
				if ( ! isset( $position_origins['tablet'] ) ) {
					$position_origins['tablet'] = $position_origins['desktop'];
				}
				if ( ! isset( $position_origins['phone'] ) ) {
					$position_origins['phone'] = $position_origins['desktop'];
				}
			}

			if ( ( $hover_status['horizontal'] || $hover_status['vertical'] ) && ! isset( $position_origins['hover'] ) ) {
				$position_origins['hover'] = $position_origins['desktop'];
			}

			if ( ( $sticky_status['horizontal'] || $sticky_status['vertical'] ) && ! isset( $position_origins['sticky'] ) ) {
				$position_origins['sticky'] = $position_origins['desktop'];
			}

			$this->module->set_position_locations( $position_origins );

			foreach ( $position_origins as $type => $origin ) {
				switch ( $type ) {
					case 'hover':
						$type_selector = $hover->add_hover_to_selectors( $position_selector );
						break;

					case 'sticky':
						$type_selector = $sticky->add_sticky_to_selectors( $position_selector, false );
						break;

					default:
						$type_selector = $position_selector;
						break;
				}

				$active_origin       = $origin;
				$is_default_position = false;
				$default_strpos      = strpos( $origin, '_is_default' );
				if ( $default_strpos !== false ) {
					$is_default_position = true;
					$active_origin       = substr( $origin, 0, $default_strpos );
				}
				if ( 'none' === $active_origin ) {
					if ( ! $is_default_position ) {
						$el_style = array(
							'selector'    => $type_selector,
							'declaration' => 'top:0px; right:auto; bottom:auto; left:0px;',
							'priority'    => $this->module->get_style_priority(),
						) + $this->get_media_query( $type );
						ET_Builder_Element::set_style( $function_name, $el_style );
					}
					continue;
				}

				$offsets = array( 'vertical', 'horizontal' );
				foreach ( $offsets as $offsetSlug ) {
					// phpcs:disable ET.Sniffs.ValidVariableName.InterpolatedVariableNotSnakeCase -- Existing codebase.
					$field_slug    = "{$offsetSlug}_offset";
					$is_hover      = 'hover' === $type && $hover_status[ $offsetSlug ];
					$is_sticky     = 'sticky' === $type;
					$is_responsive = in_array( $type, array( 'tablet', 'phone' ) ) && $resp_status[ $offsetSlug ];
					$offset_view   = $is_hover || $is_sticky || $is_responsive ? $type : 'desktop';
					$value         = esc_attr( $this->get_value( $props, $field_slug, '0px', $offset_view, true ) );

					if (
						in_array( $offset_view, array( 'desktop', 'sticky' ), true )
						&& $is_default_position
						&& 'top_left' === $active_origin
						&& '0px' === $value
					) {
						continue;
					}

					$origin_array     = explode( '_', $active_origin );
					$property         = 'left';
					$inverse_property = 'right';
					if ( 'horizontal' === $offsetSlug ) {
						if ( 'center' === $origin_array[1] ) {
							$value = '50%';
						} elseif ( 'right' === $origin_array[1] ) {
							$property         = 'right';
							$inverse_property = 'left';
						}
					} else {
						$property         = 'top';
						$inverse_property = 'bottom';
						if ( 'center' === $origin_array[0] ) {
							$value = '50%';
						} elseif ( 'bottom' === $origin_array[0] ) {
							$property         = 'bottom';
							$inverse_property = 'top';
						}
					}
					// phpcs:enable ET.Sniffs.ValidVariableName.InterpolatedVariableNotSnakeCase -- Existing codebase.

					// add the adminbar height offset to avoid overflow of fixed elements.
					$active_position = $this->get_value( $props, 'positioning', $position_default, $type, true );
					$has_negative_position = strpos( $props['custom_css_main_element'], 'top: -' ) || strpos( $props['custom_css_main_element'], 'top:-' );
					if ( 'top' === $property ) {
						$admin_bar_declaration = "$property: $value";
						if ( 'fixed' === $active_position ) {
							$admin_bar_height      = 'phone' === $type ? '46px' : '32px';
							$admin_bar_declaration = "$property: calc($value + $admin_bar_height);";
						}
						if ( ! $has_negative_position && ( 'desktop' !== $type || 'fixed' === $active_position ) ) {
							$el_style = array(
								'selector'    => "body.logged-in.admin-bar $type_selector",
								'declaration' => $admin_bar_declaration,
								'priority'    => $this->module->get_style_priority(),
							) + $this->get_media_query( $type );
							ET_Builder_Element::set_style( $function_name, $el_style );
						}
					}
					if ( 'top' === $inverse_property && ( 'desktop' !== $type || 'fixed' === $active_position ) ) {
						$el_style = array(
							'selector'    => "body.logged-in.admin-bar $type_selector",
							'declaration' => "$inverse_property: auto",
							'priority'    => $this->module->get_style_priority(),
						) + $this->get_media_query( $type );
						ET_Builder_Element::set_style( $function_name, $el_style );
					}

					$el_style = array(
						'selector'    => $type_selector,
						'declaration' => "$property: $value;",
						'priority'    => $this->module->get_style_priority(),
					) + $this->get_media_query( $type );
					ET_Builder_Element::set_style( $function_name, $el_style );

					$el_style = array(
						'selector'    => $type_selector,
						'declaration' => "$inverse_property: auto;",
						'priority'    => $this->module->get_style_priority(),
					) + $this->get_media_query( $type );
					ET_Builder_Element::set_style( $function_name, $el_style );
				}
			}
		}

		if ( $has_z_index && ( ! is_array( $position_config ) || ! $has_position ) ) {
			// Backwards compatibility. Before this feature if z-index was set, position got defaulted as relative
			$el_style = array(
				'selector'    => '%%order_class%%',
				'declaration' => 'position: relative;',
				'priority'    => $this->module->get_style_priority(),
			);
			ET_Builder_Element::set_style( $function_name, $el_style );
		}
	}

	/**
	 * Determine if Position Options are used.
	 *
	 * @since 4.10.0
	 *
	 * @param array $attrs Module attributes/props.
	 *
	 * @return bool
	 */
	public function is_used( $attrs ) {
		foreach ( $attrs as $attr => $value ) {
			if ( ! $value ) {
				continue;
			}

			$is_attr = false !== strpos( $attr, 'z_index' )
				|| false !== strpos( $attr, 'positioning' )
				|| false !== strpos( $attr, 'position_origin' )
				|| 'vertical_offset' === $attr
				|| 'horizontal_offset' === $attr;

			// Ignore default value.
			if ( 'positioning' === $attr && 'relative' === $value ) {
				continue;
			}

			// Ignore default value.
			if ( ( 'position_origin_a' === $attr || 'position_origin_f' === $attr || 'position_origin_r' === $attr ) && 'top_left' === $value ) {
				continue;
			}

			// Ignore default value.
			if ( 'z_index' === $attr && 'auto' === $value ) {
				continue;
			}

			if ( $is_attr ) {
				return true;
			}
		}

		return false;
	}
}

return new ET_Builder_Module_Field_Position();
