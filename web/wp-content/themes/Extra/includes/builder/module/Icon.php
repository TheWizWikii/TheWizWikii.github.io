<?php
/**
 * Icon module class.
 *
 * @package Divi
 * @subpackage Builder
 * @since ?
 */

/**
 * Handles setting up everything for icon module.
 *
 * @since ?
 */
class ET_Builder_Module_Icon extends ET_Builder_Module {
	/**
	 * Holds icon selector.
	 *
	 * @var string
	 */
	public $icon_element_selector;

	/**
	 * Holds icon element's classname.
	 *
	 * @var string
	 */
	public $icon_element_classname;

	/**
	 * Initialize the module class.
	 *
	 * @since ?
	 *
	 * @return void
	 */
	public function init() {
		$this->name                   = et_builder_i18n( 'Icon' );
		$this->plural                 = esc_html__( 'Icons', 'et_builder' );
		$this->slug                   = 'et_pb_icon';
		$this->vb_support             = 'on';
		$this->icon_element_selector  = '%%order_class%% .et_pb_icon_wrap .et-pb-icon';
		$this->icon_element_classname = 'et-pb-icon';

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Icon' ),
					'link'         => et_builder_i18n( 'Link' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'icon_settings' => esc_html__( 'Icon', 'et_builder' ),
					'alignment'     => esc_html__( 'Alignment', 'et_builder' ),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation'  => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
					'attributes' => array(
						'title'    => esc_html__( 'Attributes', 'et_builder' ),
						'priority' => 95,
					),
				),
			),
		);

		$this->advanced_fields = array(
			'margin_padding' => array(
				'css' => array(
					'main'      => '%%order_class%%',
					'padding'   => '%%order_class%% .et_pb_icon_wrap',
					'margin'    => '%%order_class%%',
					'important' => 'all',
				),
			),
			'borders'        => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'        => '%%order_class%% .et_pb_icon_wrap',
							'border_styles'       => '%%order_class%% .et_pb_icon_wrap',
							'border_radii_hover'  => '%%order_class%% .et_pb_icon_wrap:hover',
							'border_styles_hover' => '%%order_class%% .et_pb_icon_wrap:hover',
						),
					),
				),
			),
			'background'     => array(
				'css'                           => array(
					'main'  => '%%order_class%% .et_pb_icon_wrap',
					'hover' => '%%order_class%% .et_pb_icon_wrap:hover',
				),
				'use_background_image_parallax' => false,
				'use_background_video'          => false,
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main'    => '%%order_class%% .et_pb_icon_wrap',
						'overlay' => 'inset',
					),
				),
			),
			'transform'      => array(
				'css' => array(
					'main'  => '%%order_class%% .et_pb_icon_wrap',
					'hover' => '%%order_class%% .et_pb_icon_wrap:hover',

				),
			),
			'filters'        => array(
				'css' => array(
					'main'  => '%%order_class%% .et_pb_icon_wrap',
					'hover' => '%%order_class%% .et_pb_icon_wrap:hover',
				),
			),
			'max_width'      => array(
				'use_max_width'        => false,
				'use_width'            => false,
				'use_module_alignment' => false,
			),
			'height'         => array(
				'use_height'     => false,
				'use_max_height' => false,
				'use_min_height' => false,
			),
			'fonts'          => false,
			'text'           => false,
			'button'         => false,
			'link_options'   => false,
		);

		$this->custom_css_fields = array(
			'icon_element' => array(
				'label'    => esc_html__( 'Icon Element', 'et_builder' ),
				'selector' => $this->icon_element_selector,
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'cYwqxoHnjNA',
				'name' => esc_html__( 'An introduction to the Icon module', 'et_builder' ),
			),
		);
	}
	/**
	 * Get's the module fields.
	 *
	 * @since ?
	 *
	 * @return array $fields Module Fields.
	 */
	public function get_fields() {
		$fields = array(
			'font_icon'      => array(
				'label'           => esc_html__( 'Icon', 'et_builder' ),
				'type'            => 'select_icon',
				'option_category' => 'basic_option',
				'default'         => '&#x21;||divi',
				'class'           => array( 'et-pb-font-icon' ),
				'toggle_slug'     => 'main_content',
				'description'     => esc_html__( 'Choose an icon to display with your blurb.', 'et_builder' ),
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'icon_color'     => array(
				'default'        => et_builder_accent_color(),
				'label'          => esc_html__( 'Icon Color', 'et_builder' ),
				'type'           => 'color-alpha',
				'description'    => esc_html__( 'Here you can define a custom color for your icon.', 'et_builder' ),
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'icon_settings',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'icon_width'     => array(
				'label'           => esc_html__( 'Icon Size', 'et_builder' ),
				'default'         => '96px',
				'range_settings'  => array(
					'min'  => '1',
					'max'  => '200',
					'step' => '1',
				),
				'toggle_slug'     => 'icon_settings',
				'description'     => esc_html__( 'Here you can choose icon width.', 'et_builder' ),
				'type'            => 'range',
				'option_category' => 'layout',
				'tab_slug'        => 'advanced',
				'mobile_options'  => true,
				'validate_unit'   => true,
				'allowed_units'   => array( '%', 'em', 'rem', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw' ),
				'responsive'      => true,
				'mobile_options'  => true,
				'sticky'          => true,
				'hover'           => 'tabs',
			),
			'title_text'     => array(
				'label'           => esc_html__( 'Icon Title Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'description'     => esc_html__( 'This defines the HTML Title text.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'url'            => array(
				'label'           => esc_html__( 'Icon Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'off',
				'description'     => esc_html__( 'If you would like your image to be a link, input your destination URL here. No link will be created if this field is left blank.', 'et_builder' ),
				'toggle_slug'     => 'link',
				'dynamic_content' => 'url',
				'affects'         => array(
					'title_text',
				),
			),
			'url_new_window' => array(
				'label'            => esc_html__( 'Icon Link Target', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => esc_html__( 'In The Same Window', 'et_builder' ),
					'on'  => esc_html__( 'In The New Tab', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'depends_show_if'  => 'off',
				'toggle_slug'      => 'link',
				'description'      => esc_html__( 'Here you can choose whether or not your link opens in a new window', 'et_builder' ),
			),
			'align'          => array(
				'label'            => esc_html__( 'Icon Alignment', 'et_builder' ),
				'type'             => 'text_align',
				'option_category'  => 'layout',
				'options'          => et_builder_get_text_orientation_options( array( 'justified' ) ),
				'default_on_front' => 'center',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'alignment',
				'description'      => esc_html__( 'Here you can choose the image alignment.', 'et_builder' ),
				'options_icon'     => 'module_align',
				'mobile_options'   => true,
			),
		);

		return $fields;
	}

	/**
	 * Return an alignment value by device.
	 *
	 * @param string $device Device mode.
	 *
	 * @return string
	 */
	public function get_alignment( $device = 'desktop' ) {
		$is_desktop = 'desktop' === $device;
		$suffix     = ! $is_desktop ? "_{$device}" : '';
		$alignment  = $is_desktop && isset( $this->props['align'] ) ? $this->props['align'] : '';

		if ( ! $is_desktop && et_pb_responsive_options()->is_responsive_enabled( $this->props, 'align' ) ) {
			$alignment = et_pb_responsive_options()->get_any_value( $this->props, "align{$suffix}" );
		}

		return et_pb_get_alignment( $alignment );
	}

	/**
	 * Renders the module output.
	 *
	 * @param  array  $attrs       List of attributes.
	 * @param  string $content     Content being processed.
	 * @param  string $render_slug Slug of module that is used for rendering output.
	 *
	 * @return string
	 */
	public function render( $attrs, $content, $render_slug ) {
		$multi_view       = et_pb_multi_view_options( $this );
		$title_text       = $this->props['title_text'];
		$url              = $this->props['url'];
		$url_new_window   = $this->props['url_new_window'];
		$align            = $this->get_alignment();
		$align_tablet     = $this->get_alignment( 'tablet' );
		$align_phone      = $this->get_alignment( 'phone' );
		$animation_style  = $this->props['animation_style'];
		$box_shadow_style = self::$_->array_get( $this->props, 'box_shadow_style', '' );
		// Responsive Icon Alignment.
		// Set CSS properties and values for the image alignment.
		// 1. Text Align is necessary, just set it from current image alignment value.
		// 2. Margin {Side} is optional. Used to pull the image to right/left side.
		// 3. Margin Left and Right are optional. Used by Center to reset custom margin of point 2.
		$align_values = array(
			'desktop' => array(
				'text-align'      => esc_html( $align ),
				"margin-{$align}" => ! empty( $align ) && 'center' !== $align ? '0' : '',
			),
			'tablet'  => array(
				'text-align'             => esc_html( $align_tablet ),
				'margin-left'            => 'left' !== $align_tablet ? 'auto' : '',
				'margin-right'           => 'left' !== $align_tablet ? 'auto' : '',
				"margin-{$align_tablet}" => ! empty( $align_tablet ) && 'center' !== $align_tablet ? '0' : '',
			),
			'phone'   => array(
				'text-align'            => esc_html( $align_phone ),
				'margin-left'           => 'left' !== $align_phone ? 'auto' : '',
				'margin-right'          => 'left' !== $align_phone ? 'auto' : '',
				"margin-{$align_phone}" => ! empty( $align_phone ) && 'center' !== $align_phone ? '0' : '',
			),
		);

		et_pb_responsive_options()->generate_responsive_css( $align_values, '%%order_class%%', '', $render_slug, '', 'alignment' );

		if ( empty( $title_text ) ) {
			$title_text = et_builder_resolve_dynamic_content( 'post_featured_image_title_text', array(), get_the_ID(), 'display' );
		}

		$icon_hover_selector = str_replace( $this->icon_element_classname, $this->icon_element_classname . ':hover', $this->icon_element_selector );

		// Font Icon Style.
		$this->generate_styles(
			array(
				'utility_arg'    => 'icon_font_family',
				'render_slug'    => $render_slug,
				'base_attr_name' => 'font_icon',
				'important'      => true,
				'selector'       => $this->icon_element_selector,
				'hover_selector' => $icon_hover_selector,
				'processor'      => array(
					'ET_Builder_Module_Helper_Style_Processor',
					'process_extended_icon',
				),
			)
		);

		// Font Icon Color Style.
		$this->generate_styles(
			array(
				'base_attr_name' => 'icon_color',
				'selector'       => $this->icon_element_selector,
				'css_property'   => 'color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
				'hover_selector' => $icon_hover_selector,
			)
		);

		// Font Icon Size Style.
		$this->generate_styles(
			array(
				'base_attr_name' => 'icon_width',
				'selector'       => $this->icon_element_selector,
				'css_property'   => 'font-size',
				'render_slug'    => $render_slug,
				'type'           => 'range',
				'hover_selector' => $icon_hover_selector,
			)
		);

		$output = $multi_view->render_element(
			array(
				'content'        => '{{font_icon}}',
				'attrs'          => array(
					'class' => $this->icon_element_classname,
				),
				'hover_selector' => $this->icon_element_selector,
			)
		);

		$box_shadow_overlay_wrap_class = 'none' !== $box_shadow_style
			? 'has-box-shadow-overlay'
			: '';

		$box_shadow_overlay_element = 'none' !== $box_shadow_style
			? '<div class="box-shadow-overlay"></div>'
			: '';

		$output = sprintf(
			'<span class="et_pb_icon_wrap %1$s">%2$s%4$s%5$s%3$s</span>',
			$box_shadow_overlay_wrap_class,
			$box_shadow_overlay_element,
			$output,
			et_core_esc_previously( $this->background_pattern() ), // #4
			et_core_esc_previously( $this->background_mask() ) // #5
		);

		$title_text = ! empty( $title_text ) ? sprintf( 'title="%1$s"', esc_attr( $title_text ) ) : '';

		if ( '' !== $url ) {
			$output = sprintf(
				'<a href="%1$s"%3$s %4$s>%2$s</a>',
				esc_url( $url ),
				$output,
				( 'on' === $url_new_window ? ' target="_blank"' : '' ),
				$title_text
			);
		}

		// Module classnames.
		if ( ! in_array( $animation_style, array( '', 'none' ), true ) ) {
			$this->add_classname( 'et-waypoint' );
		}

		$output = sprintf(
			'<div%3$s class="%2$s">
				%1$s
			</div>',
			$output,
			$this->module_classname( $render_slug ),
			$this->module_id()
		);

		return $output;
	}

	/**
	 * Filter multi view value.
	 *
	 * @since 3.27.1
	 *
	 * @see ET_Builder_Module_Helper_MultiViewOptions::filter_value
	 *
	 * @param mixed $raw_value Props raw value.
	 * @param array $args {
	 *     Context data.
	 *
	 *     @type string $context      Context param: content, attrs, visibility, classes.
	 *     @type string $name         Module options props name.
	 *     @type string $mode         Current data mode: desktop, hover, tablet, phone.
	 *     @type string $attr_key     Attribute key for attrs context data. Example: src, class, etc.
	 *     @type string $attr_sub_key Attribute sub key that availabe when passing attrs value as array such as styes. Example: padding-top, margin-botton, etc.
	 * }
	 *
	 * @return mixed
	 */
	public function multi_view_filter_value( $raw_value, $args ) {

		$name = isset( $args['name'] ) ? $args['name'] : '';

		if ( $raw_value && 'font_icon' === $name ) {
			return et_pb_get_extended_font_icon_value( $raw_value, true );
		}
		return $raw_value;
	}

	/**
	 * Transition fields for Icon module.
	 *
	 * @since ?
	 *
	 * @return array Fields list in array.
	 */
	public function get_transition_fields_css_props() {
		$fields               = parent::get_transition_fields_css_props();
		$fields['icon_color'] = array( 'color' => $this->icon_element_selector );
		return $fields;
	}
}
new ET_Builder_Module_Icon();
