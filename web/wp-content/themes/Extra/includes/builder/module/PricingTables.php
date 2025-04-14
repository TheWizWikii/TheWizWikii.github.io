<?php

class ET_Builder_Module_Pricing_Tables extends ET_Builder_Module {
	/**
	 * Holds additional shortcode.
	 *
	 * @var string
	 */
	public $additional_shortcode;

	function init() {
		$this->name             = esc_html__( 'Pricing Tables', 'et_builder' );
		$this->plural           = esc_html__( 'Pricing Tables', 'et_builder' );
		$this->slug             = 'et_pb_pricing_tables';
		$this->vb_support       = 'on';
		$this->main_css_element = '%%order_class%%';
		$this->child_slug       = 'et_pb_pricing_table';
		$this->child_item_text  = esc_html__( 'Pricing Table', 'et_builder' );

		$this->additional_shortcode = 'et_pb_pricing_item';
		$this->custom_css_fields    = array(
			'pricing_heading'       => array(
				'label'    => esc_html__( 'Pricing Heading', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading',
			),
			'pricing_title'         => array(
				'label'    => esc_html__( 'Pricing Title', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading h2',
			),
			'pricing_subtitle'      => array(
				'label'    => esc_html__( 'Pricing Subtitle', 'et_builder' ),
				'selector' => '.et_pb_pricing_heading .et_pb_best_value',
			),
			'pricing_top'           => array(
				'label'    => esc_html__( 'Pricing Top', 'et_builder' ),
				'selector' => '.et_pb_pricing_content_top',
			),
			'price'                 => array(
				'label'    => esc_html__( 'Price', 'et_builder' ),
				'selector' => '.et_pb_et_price',
			),
			'currency'              => array(
				'label'    => esc_html__( 'Currency', 'et_builder' ),
				'selector' => '.et_pb_dollar_sign',
			),
			'frequency'             => array(
				'label'    => esc_html__( 'Frequency', 'et_builder' ),
				'selector' => '.et_pb_frequency',
			),
			'pricing_content'       => array(
				'label'    => esc_html__( 'Pricing Content', 'et_builder' ),
				'selector' => '.et_pb_pricing_content',
			),
			'pricing_item'          => array(
				'label'    => esc_html__( 'Pricing Item', 'et_builder' ),
				'selector' => 'ul.et_pb_pricing li',
			),
			'pricing_item_excluded' => array(
				'label'    => esc_html__( 'Excluded Item', 'et_builder' ),
				'selector' => 'ul.et_pb_pricing li.et_pb_not_available',
			),
			'pricing_button'        => array(
				'label'    => esc_html__( 'Pricing Button', 'et_builder' ),
				'selector' => '.et_pb_pricing_table_button',
			),
			'featured_table'        => array(
				'label'    => esc_html__( 'Featured Table', 'et_builder' ),
				'selector' => '.et_pb_featured_table',
			),
		);

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'elements' => et_builder_i18n( 'Elements' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'layout' => et_builder_i18n( 'Layout' ),
					'bullet' => esc_html__( 'Bullet', 'et_builder' ),
				),
			),
		);

		$this->advanced_fields = array(
			'borders'         => array(
				'default' => array(
					// @TODO
					'additional_elements' => array(
						array(
							"{$this->main_css_element} .et_pb_pricing_content_top" => array( 'bottom' ),
						),
					),
					'css'                 => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_pb_pricing_table",
							'border_styles' => "{$this->main_css_element} .et_pb_pricing_table",
						),
					),
					'defaults'            => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '1px',
							'color' => '#bebebe',
							'style' => 'solid',
						),
					),
				),
				'price'   => array(
					'css'             => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_pb_pricing_content_top",
							'border_styles' => "{$this->main_css_element} .et_pb_pricing_content_top",
						),
					),
					'option_category' => 'border',
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'price',
					'defaults'        => array(
						'border_radii'  => 'on||||',
						'border_styles' => array(
							'width' => '0px',
							'color' => '#bebebe',
							'style' => 'solid',
						),
						'composite'     => array(
							'border_bottom' => array(
								'border_width_bottom' => '1px',
							),
						),
					),
				),
			),
			'fonts'           => array(
				'header'             => array(
					'label'            => et_builder_i18n( 'Title' ),
					'css'              => array(
						'main'      => "{$this->main_css_element} .et_pb_pricing_heading h2, {$this->main_css_element} .et_pb_pricing_heading h1.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h3.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h4.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h5.et_pb_pricing_title, {$this->main_css_element} .et_pb_pricing_heading h6.et_pb_pricing_title",
						'important' => 'all',
					),
					'letter_spacing'   => array(
						'default' => '0px',
					),
					'header_level'     => array(
						'default' => 'h2',
					),
					'options_priority' => array(
						'header_text_color' => 9,
					),
				),
				'body'               => array(
					'label'            => et_builder_i18n( 'Body' ),
					'css'              => array(
						'main'         => "{$this->main_css_element} .et_pb_pricing li",
						'limited_main' => "{$this->main_css_element} .et_pb_pricing li, {$this->main_css_element} .et_pb_pricing li span, {$this->main_css_element} .et_pb_pricing li a",
					),
					'line_height'      => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'font_size'        => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'letter_spacing'   => array(
						'default' => '0px',
					),
					'block_elements'   => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
					),
					'options_priority' => array(
						'body_text_color' => 9,
					),
				),
				'subheader'          => array(
					'label'            => esc_html__( 'Subtitle', 'et_builder' ),
					'css'              => array(
						'main' => "{$this->main_css_element} .et_pb_best_value",
					),
					'letter_spacing'   => array(
						'default' => '0px',
					),
					'line_height'      => array(
						'default' => '1em',
					),
					'options_priority' => array(
						'subheader_text_color' => 9,
					),
				),
				'price'              => array(
					'label'            => esc_html__( 'Price', 'et_builder' ),
					'css'              => array(
						'main'       => "{$this->main_css_element} .et_pb_sum",
						'text_align' => "{$this->main_css_element} .et_pb_pricing_content_top",
					),
					'line_height'      => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'options_priority' => array(
						'price_text_color' => 8,
					),
				),
				'currency_frequency' => array(
					'label'            => esc_html__( 'Currency &amp; Frequency', 'et_builder' ),
					'css'              => array(
						'main' => "{$this->main_css_element} .et_pb_dollar_sign, {$this->main_css_element} .et_pb_frequency",
					),
					'hide_text_align'  => true,
					'options_priority' => array(
						'currency_frequency_text_color' => 9,
					),
				),
				'excluded'           => array(
					'label'            => esc_html__( 'Excluded Item', 'et_builder' ),
					'css'              => array(
						'main' => "{$this->main_css_element} .et_pb_pricing li.et_pb_not_available, {$this->main_css_element} .et_pb_pricing li.et_pb_not_available span, {$this->main_css_element} .et_pb_pricing li.et_pb_not_available a",
					),
					'line_height'      => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'font_size'        => array(
						'default' => absint( et_get_option( 'body_font_size', '14' ) ) . 'px',
					),
					'options_priority' => array(
						'excluded_text_color' => 9,
					),
				),
			),
			'background'      => array(
				'css'      => array(
					'main' => "{$this->main_css_element} .et_pb_pricing_table",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'button'          => array(
				'button' => array(
					'label'          => et_builder_i18n( 'Button' ),
					'css'            => array(
						'main'         => "{$this->main_css_element} .et_pb_pricing_table_button.et_pb_button",
						'limited_main' => "{$this->main_css_element} .et_pb_pricing_table_button.et_pb_button",
						'alignment'    => "{$this->main_css_element} .et_pb_button_wrapper",
					),
					'use_alignment'  => true,
					'box_shadow'     => array(
						'css' => array(
							'main' => '%%order_class%% .et_pb_button',
						),
					),
					'margin_padding' => array(
						'css' => array(
							'important' => 'all',
						),
					),
				),
			),
			'margin_padding'  => array(
				'css' => array(
					'important'      => 'all', // needed to overwrite last module margin-bottom styling
					'main'           => '%%order_class%% .et_pb_pricing_heading, %%order_class%% .et_pb_pricing_content_top, %%order_class%% .et_pb_pricing_content',
					'padding-right'  => '%%order_class%% .et_pb_button_wrapper',
					'padding-bottom' => '%%order_class%% .et_pb_pricing_table',
					'padding-left'   => '%%order_class%% .et_pb_button_wrapper',
				),
			),
			'text'            => array(
				'css' => array(
					'text_orientation' => '%%order_class%% .et_pb_pricing_table, %%order_class%% .et_pb_pricing_content',
					'text_shadow'      => '%%order_class%% .et_pb_pricing_heading, %%order_class%% .et_pb_pricing_content_top, %%order_class%% .et_pb_pricing_content',
				),
			),
			'position_fields' => array(
				'default' => 'relative',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'BVzu4WnjgYI',
				'name' => esc_html__( 'An introduction to the Pricing Tables module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'featured_table_background_color'              => array(
				'label'          => esc_html__( 'Featured Background Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a unique color to be used for the background of featured pricing tables. This helps featured tables stand out from the rest.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'layout',
				'priority'       => 23,
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'featured_table_header_background_color'       => array(
				'label'          => esc_html__( 'Featured Header Background Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a unique color to use for the background behind pricing table titles in featured pricing tables. Unique colors can help featured items stand out from the rest.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'header',
				'priority'       => 21,
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'featured_table_header_text_color'             => array(
				'label'          => esc_html__( 'Featured Title Text Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a unique color to use for title text in featured pricing tables. Unique colors can help featured items stand out from the rest.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'header',
				'priority'       => 20,
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'header_background_color'                      => array(
				'label'          => esc_html__( 'Table Header Background Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the background behind pricing table titles.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'header',
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'featured_table_subheader_text_color'          => array(
				'label'          => esc_html__( 'Featured Subtitle Text Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a unique color to use for subtitles in featured pricing tables. Unique colors can help featured items stand out from the rest.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'subheader',
				'priority'       => 20,
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'featured_table_text_color'                    => array(
				'label'          => esc_html__( 'Featured Body Text Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a unique color to use for body text in featured pricing tables. Unique colors can help featured items stand out from the rest.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'body',
				'sub_toggle'     => 'p',
				'priority'       => 8,
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'show_bullet'                                  => array(
				'label'            => esc_html__( 'Show Bullets', 'et_builder' ),
				'description'      => esc_html__( "Disabling bullets will remove the bullet points that appear next to each list item within the pricing table's feature area.", 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'toggle_slug'      => 'elements',
				'affects'          => array(
					'bullet_color',
				),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'featured_table_bullet_color'                  => array(
				'label'          => esc_html__( 'Featured Bullet Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a unique color to use for the bullets that appear next to each list items within featured tabes. Unique colors can help featured items stand out from the rest.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'bullet',
				'priority'       => 22,
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'bullet_color'                                 => array(
				'label'           => esc_html__( 'Bullet Color', 'et_builder' ),
				'description'     => esc_html__( "Pick a color to use for the bullets that appear next to each list item within the pricing table's feature area.", 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'bullet',
				'depends_show_if' => 'on',
				'hover'           => 'tabs',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'show_featured_drop_shadow'                    => array(
				'label'            => esc_html__( 'Show Featured Drop Shadow', 'et_builder' ),
				'description'      => esc_html__( 'Featured pricing tables have a drop shadow that helps them stand out from the rest. This shadow can be disabled if you wish.', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
				'priority'         => 24,
				'mobile_options'   => true,
			),
			'featured_table_excluded_text_color'           => array(
				'label'          => esc_html__( 'Featured Excluded Item Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a unique color to use for excluded list items within featured pricing tables. Unique colors can help featured items stand out from the rest.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'excluded',
				'priority'       => 20,
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'featured_table_price_background_color'        => array(
				'label'          => esc_html__( 'Featured Pricing Area Background Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a unique color to use for the background area that appears behind the pricing text in featured tables. Unique colors can help featured items stand out from the rest.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'price',
				'priority'       => 18,
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'featured_table_price_color'                   => array(
				'label'          => esc_html__( 'Featured Price Text Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a unique color to use for price text within featured pricing tables. Unique colors can help featured items stand out from the rest.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'price',
				'priority'       => 19,
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'featured_table_currency_frequency_text_color' => array(
				'label'          => esc_html__( 'Featured Currency &amp; Frequency Text Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a unique color to use for currency and frequency text within featured pricing tables. Unique colors can help featured items stand out from the rest.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'currency_frequency',
				'priority'       => 20,
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
			'price_background_color'                       => array(
				'label'          => esc_html__( 'Pricing Area Background Color', 'et_builder' ),
				'description'    => esc_html__( 'Pick a color to use for the background area that appears behind the pricing text.', 'et_builder' ),
				'type'           => 'color-alpha',
				'custom_color'   => true,
				'tab_slug'       => 'advanced',
				'toggle_slug'    => 'price',
				'priority'       => 21,
				'hover'          => 'tabs',
				'mobile_options' => true,
				'sticky'         => true,
			),
		);
		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['bullet_color']                = array( 'border-color' => '%%order_class%% .et_pb_pricing li span:before' );
		$fields['featured_table_bullet_color'] = array( 'border-color' => '%%order_class%% .et_pb_featured_table .et_pb_pricing li span:before' );

		$fields['featured_table_header_background_color'] = array( 'background-color' => '%%order_class%% .et_pb_featured_table .et_pb_pricing_heading' );
		$fields['featured_table_header_text_color']       = array( 'color' => '%%order_class%% .et_pb_featured_table .et_pb_pricing_heading h2, %%order_class%% .et_pb_featured_table .et_pb_pricing_heading .et_pb_pricing_title' );
		$fields['header_background_color']                = array( 'background-color' => '%%order_class%% .et_pb_pricing_heading' );
		$fields['featured_table_text_color']              = array( 'color' => '%%order_class%% .et_pb_featured_table .et_pb_pricing_content li, %%order_class%% .et_pb_featured_table .et_pb_pricing_content li span, %%order_class%% .et_pb_featured_table .et_pb_pricing_content li a' );
		$fields['featured_table_subheader_text_color']    = array( 'color' => '%%order_class%% .et_pb_featured_table .et_pb_best_value' );
		$fields['featured_table_price_color']             = array( 'color' => '%%order_class%% .et_pb_featured_table .et_pb_sum' );

		$fields['featured_table_currency_frequency_text_color'] = array( 'color' => '%%order_class%% .et_pb_featured_table .et_pb_dollar_sign, %%order_class%% .et_pb_featured_table .et_pb_frequency' );
		$fields['featured_table_excluded_text_color']           = array( 'color' => '%%order_class%% .et_pb_featured_table .et_pb_pricing li.et_pb_not_available, %%order_class%% .et_pb_featured_table .et_pb_pricing li.et_pb_not_available span, %%order_class%% .et_pb_featured_table .et_pb_pricing li.et_pb_not_available a' );
		$fields['featured_table_price_background_color']        = array( 'background-color' => '%%order_class%% .et_pb_featured_table .et_pb_pricing_content_top' );
		$fields['price_background_color']                       = array( 'background-color' => '%%order_class%% .et_pb_pricing_content_top' );

		return $fields;
	}

	function before_render() {
		global $et_pb_pricing_tables_num,
			$et_pb_pricing_tables_icon,
			$et_pb_pricing_tables_icon_tablet,
			$et_pb_pricing_tables_icon_phone,
			$et_pb_pricing_tab,
			$et_pb_pricing_tables_button_rel,
			$et_pb_pricing_tables_header_level,
			$et_pb_pricing_tables_sticky,
			$et_pb_pricing_tables_sticky_transition;

		$button_custom = $this->props['custom_button'];

		$custom_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'button_icon' );
		$custom_icon        = isset( $custom_icon_values['desktop'] ) ? $custom_icon_values['desktop'] : '';
		$custom_icon_tablet = isset( $custom_icon_values['tablet'] ) ? $custom_icon_values['tablet'] : '';
		$custom_icon_phone  = isset( $custom_icon_values['phone'] ) ? $custom_icon_values['phone'] : '';

		$et_pb_pricing_tables_num = 0;

		$et_pb_pricing_tables_icon        = 'on' === $button_custom ? $custom_icon : '';
		$et_pb_pricing_tables_icon_tablet = 'on' === $button_custom ? $custom_icon_tablet : '';
		$et_pb_pricing_tables_icon_phone  = 'on' === $button_custom ? $custom_icon_phone : '';

		$et_pb_pricing_tables_button_rel   = $this->props['button_rel'];
		$et_pb_pricing_tables_header_level = 'h2' === $this->props['header_level'] ? '' : $this->props['header_level'];

		// Pass down sticky module status for correct selector suffix placement.
		$et_pb_pricing_tables_sticky = et_pb_sticky_options()->is_sticky_module( $this->props );

		// Module item has no sticky options hence this needs to be inherited to setup transition.
		$et_pb_pricing_tables_sticky_transition = et_()->array_get( $this->props, 'sticky_transition', 'on' );
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
		$multi_view     = et_pb_multi_view_options( $this );
		$featured_table = $this->get_featured_table( $content );

		$show_featured_drop_shadow        = $this->props['show_featured_drop_shadow'];
		$show_featured_drop_shadow_values = et_pb_responsive_options()->get_property_values( $this->props, 'show_featured_drop_shadow' );
		$show_featured_drop_shadow_tablet = isset( $show_featured_drop_shadow_values['tablet'] ) ? $show_featured_drop_shadow_values['tablet'] : '';
		$show_featured_drop_shadow_phone  = isset( $show_featured_drop_shadow_values['phone'] ) ? $show_featured_drop_shadow_values['phone'] : '';
		$body_text_align_values           = et_pb_responsive_options()->get_property_values( $this->props, 'body_text_align' );

		global $et_pb_pricing_tables_num, $et_pb_pricing_tables_icon, $et_pb_pricing_tables_icon_tablet, $et_pb_pricing_tables_icon_phone;

		// Show Featured Drop Shadow.
		$disabled_shadow        = 'none';
		$reset_shadow           = '0 0 12px rgba(0,0,0,0.1)';
		$featured_shadow        = '';
		$featured_shadow_tablet = '';
		$featured_shadow_phone  = '';

		if ( 'on' !== $show_featured_drop_shadow ) {
			$featured_shadow = 'none';
		}

		if ( '' !== $show_featured_drop_shadow_tablet ) {
			if ( 'on' !== $show_featured_drop_shadow_tablet ) {
				$featured_shadow_tablet = $disabled_shadow;
			} elseif ( 'on' === $show_featured_drop_shadow_tablet && 'on' !== $featured_shadow ) {
				$featured_shadow_tablet = $reset_shadow;
			}

			if ( $featured_shadow_tablet === $featured_shadow ) {
				$featured_shadow_tablet = '';
			}
		}

		if ( '' !== $show_featured_drop_shadow_phone ) {
			if ( 'on' !== $show_featured_drop_shadow_phone ) {
				$featured_shadow_phone = $disabled_shadow;
			} elseif ( 'on' === $show_featured_drop_shadow_phone && 'on' !== $featured_shadow_tablet ) {
				$featured_shadow_phone = $reset_shadow;
			}

			if ( $featured_shadow_phone === $featured_shadow_tablet ) {
				$featured_shadow_phone = '';
			}
		}

		$featured_shadow_values = array(
			'desktop' => esc_html( $featured_shadow ),
			'tablet'  => esc_html( $featured_shadow_tablet ),
			'phone'   => esc_html( $featured_shadow_phone ),
		);

		et_pb_responsive_options()->generate_responsive_css( $featured_shadow_values, '%%order_class%% .et_pb_featured_table', array( '-moz-box-shadow', '-webkit-box-shadow', 'box-shadow' ), $render_slug, '', 'shadow' );

		// Featured Table Background Color.
		$this->generate_styles(
			array(
				'base_attr_name'                  => 'featured_table_background_color',
				'selector'                        => '%%order_class%% .et_pb_featured_table',
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'background-color',
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		// Header Background Color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'header_background_color',
				'selector'       => '%%order_class%% .et_pb_pricing_heading',
				'hover_selector' => '%%order_class%% .et_pb_pricing_table:hover .et_pb_pricing_heading',
				'css_property'   => 'background-color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		// Featured Table Header Background Color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'featured_table_header_background_color',
				'selector'       => '%%order_class%% .et_pb_featured_table .et_pb_pricing_heading',
				'hover_selector' => '%%order_class%% .et_pb_featured_table:hover .et_pb_pricing_heading',
				'css_property'   => 'background-color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
				'important'      => true,
			)
		);

		// Featured Table Title Text Color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'featured_table_header_text_color',
				'selector'       => '%%order_class%% .et_pb_featured_table .et_pb_pricing_heading h2, %%order_class%% .et_pb_featured_table .et_pb_pricing_heading .et_pb_pricing_title',
				'hover_selector' => '%%order_class%% .et_pb_featured_table:hover .et_pb_pricing_heading h2, %%order_class%% .et_pb_featured_table:hover .et_pb_pricing_heading .et_pb_pricing_title',
				'css_property'   => 'color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
				'important'      => true,
			)
		);

		// Featured Table Sutitle Text Color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'featured_table_subheader_text_color',
				'selector'       => '%%order_class%% .et_pb_featured_table .et_pb_best_value',
				'hover_selector' => '%%order_class%% .et_pb_featured_table:hover .et_pb_best_value',
				'css_property'   => 'color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
				'important'      => true,
			)
		);

		// Featured Table Price Text Color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'featured_table_price_color',
				'selector'       => '%%order_class%% .et_pb_featured_table .et_pb_sum',
				'hover_selector' => '%%order_class%% .et_pb_featured_table:hover .et_pb_sum',
				'css_property'   => 'color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
				'important'      => true,
			)
		);

		// Featured Table Body Text Color.
		$featured_table_text_color_selector       = et_builder_has_limitation( 'use_additional_limiting_styles' ) ? '%%order_class%% .et_pb_featured_table .et_pb_pricing_content li, %%order_class%% .et_pb_featured_table .et_pb_pricing_content li span, %%order_class%% .et_pb_featured_table .et_pb_pricing_content li a' : '%%order_class%% .et_pb_featured_table .et_pb_pricing_content li';
		$featured_table_text_color_selector_hover = et_is_builder_plugin_active() ? '%%order_class%% .et_pb_featured_table:hover .et_pb_pricing_content li, %%order_class%% .et_pb_featured_table:hover .et_pb_pricing_content li span, %%order_class%% .et_pb_featured_table:hover .et_pb_pricing_content li a' : '%%order_class%% .et_pb_featured_table:hover .et_pb_pricing_content li';

		$this->generate_styles(
			array(
				'base_attr_name' => 'featured_table_text_color',
				'selector'       => $featured_table_text_color_selector,
				'hover_selector' => $featured_table_text_color_selector_hover,
				'css_property'   => 'color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
				'important'      => true,
			)
		);

		// Bullet Color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'bullet_color',
				'selector'       => '%%order_class%% .et_pb_pricing li span:before',
				'hover_selector' => '%%order_class%% .et_pb_pricing:hover li span:before',
				'css_property'   => 'border-color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		// Featured Table Bullet Color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'featured_table_bullet_color',
				'selector'       => '%%order_class%% .et_pb_featured_table .et_pb_pricing li span:before',
				'hover_selector' => '%%order_class%% .et_pb_featured_table:hover .et_pb_pricing li span:before',
				'css_property'   => 'border-color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		// Featured Table Currency Frequency Text Color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'featured_table_currency_frequency_text_color',
				'selector'       => '%%order_class%% .et_pb_featured_table .et_pb_dollar_sign, %%order_class%% .et_pb_featured_table .et_pb_frequency',
				'hover_selector' => '%%order_class%% .et_pb_featured_table:hover .et_pb_dollar_sign, %%order_class%% .et_pb_featured_table:hover .et_pb_frequency',
				'css_property'   => 'color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
				'important'      => true,
			)
		);

		// Featured Table Excluded Item Text Color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'featured_table_excluded_text_color',
				'selector'       => '%%order_class%% .et_pb_featured_table .et_pb_pricing li.et_pb_not_available, %%order_class%% .et_pb_featured_table .et_pb_pricing li.et_pb_not_available span, %%order_class%% .et_pb_featured_table .et_pb_pricing li.et_pb_not_available a',
				'hover_selector' => '%%order_class%% .et_pb_featured_table:hover .et_pb_pricing li.et_pb_not_available, %%order_class%% .et_pb_featured_table:hover .et_pb_pricing li.et_pb_not_available span, %%order_class%% .et_pb_featured_table:hover .et_pb_pricing li.et_pb_not_available a',
				'css_property'   => 'color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
				'important'      => true,
			)
		);

		// Featured Table Price Background Color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'featured_table_price_background_color',
				'selector'       => '%%order_class%% .et_pb_featured_table .et_pb_pricing_content_top',
				'hover_selector' => '%%order_class%% .et_pb_featured_table:hover .et_pb_pricing_content_top',
				'css_property'   => 'background-color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		// Price Background Color.
		$this->generate_styles(
			array(
				'base_attr_name' => 'price_background_color',
				'selector'       => '%%order_class%% .et_pb_pricing_content_top',
				'hover_selector' => '%%order_class%%:hover .et_pb_pricing_content_top',
				'css_property'   => 'background-color',
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		// Custom Padding Left On Center.
		if ( ! empty( $body_text_align_values ) ) {
			foreach ( $body_text_align_values as $body_text_align_device => $body_text_align_value ) {
				if ( 'center' !== $body_text_align_value ) {
					continue;
				}

				$padding_left_style = array(
					'selector'    => '%%order_class%% .et_pb_pricing li',
					'declaration' => esc_html( 'padding-left: 0;' ),
				);

				if ( 'desktop' !== $body_text_align_device ) {
					$current_media_query               = 'tablet' === $body_text_align_device ? 'max_width_980' : 'max_width_767';
					$padding_left_style['media_query'] = ET_Builder_Element::get_media_query( $current_media_query );
				}

				ET_Builder_Element::set_style( $render_slug, $padding_left_style );
			}
		}

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();
		$content                   = $this->content;

		// Module classnames
		$this->add_classname(
			array(
				'et_pb_pricing',
				'clearfix',
				"et_pb_pricing_{$et_pb_pricing_tables_num}",
				$featured_table,
			)
		);

		if ( 'off' === $multi_view->get_value( 'show_bullet' ) ) {
			$this->add_classname( 'et_pb_pricing_no_bullet' );
		}

		// Remove automatically added classnames
		$this->remove_classname(
			array(
				$render_slug,
			)
		);

		$multi_view_data_attr = $multi_view->render_attrs(
			array(
				'classes' => array(
					'et_pb_pricing_no_bullet' => array(
						'show_bullet' => 'off',
					),
				),
			)
		);

		$output = sprintf(
			'<div%3$s class="%2$s"%6$s>
				%5$s
				%4$s
				%7$s
				%8$s
				<div class="et_pb_pricing_table_wrap">
					%1$s
				</div>
			</div>',
			$content,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			$video_background,
			$parallax_image_background,
			$multi_view_data_attr,
			et_core_esc_previously( $this->background_pattern() ), // #7
			et_core_esc_previously( $this->background_mask() ) // #8
		);

		$output .= $this->keep_box_shadow_compatibility( $attrs, $content, $render_slug );

		return $output;
	}

	/**
	 * Additional module output.
	 *
	 * @param  array  $atts          List of attributes.
	 * @param  string $content       Content being processed.
	 * @param  string $function_name Slug of module that is used for rendering output.
	 *
	 * @return string
	 */
	public function additional_render( $atts, $content, $function_name ) {
		$attributes = shortcode_atts(
			array(
				'available' => 'on',
			),
			$atts
		);

		$output = sprintf(
			'<li%2$s><span>%1$s</span></li>',
			$content,
			( 'on' !== $attributes['available'] ? ' class="et_pb_not_available"' : '' )
		);
		return $output;
	}

	private function get_featured_table( $content ) {
		// Extract `et_pb_pricing_table` shortcode attributes
		preg_match_all( '/\[et_pb_pricing_table(\s+[^\]]*)\]/', $content, $matches );

		if ( ! isset( $matches[1] ) || 0 === count( $matches[1] ) ) {
			return '';
		}

		$list = array();

		foreach ( $matches[1] as $match ) {
			// Check if the shortcode has the `feature` attribute on
			// TODO: Find a better way to do that
			$list[] = (bool) preg_match( '/[\s]featured=[\'|"]on[\'|"]/', $match );
		}

		// We need to know only the first 4 tables status,
		// because in a row are maximum 4 tables
		$count = count( $list ) > 4 ? 4 : count( $list );

		for ( $i = 0; $i < $count; $i ++ ) {
			if ( true === $list[ $i ] ) {
				switch ( $i ) {
					case 0:
						return '';
					case 1:
						return 'et_pb_second_featured';
					case 2:
						return 'et_pb_third_featured';
					case 3:
						return 'et_pb_fourth_featured';
				}
			}
		}

		return 'et_pb_no_featured_in_first_row';
	}

	private function keep_box_shadow_compatibility( $atts, $content, $function_name ) {
		/**
		 * @var ET_Builder_Module_Field_BoxShadow $box_shadow
		 */
		$box_shadow = ET_Builder_Module_Fields_Factory::get( 'BoxShadow' );
		$utils      = ET_Core_Data_Utils::instance();

		if (
			! is_admin()
			&&
			version_compare( $utils->array_get( $atts, '_builder_version', '3.0.93' ), '3.0.97', 'lt' )
			&&
			$box_shadow->is_inset( $box_shadow->get_value( $atts ) )
		) {
			$class          = '.' . self::get_module_order_class( $function_name );
			$overlay_shadow = $box_shadow->get_style( $class, $atts );

			return sprintf(
				'<style type="text/css">%1$s %2$s %3$s</style>',
				'.et_pb_pricing > .box-shadow-overlay { z-index: 11; }',
				sprintf( '%1$s { box-shadow: none; }', esc_attr( $class ) ),
				sprintf( '%1$s { %2$s }', esc_attr( $overlay_shadow['selector'] ), esc_attr( $overlay_shadow['declaration'] ) )
			);
		}

		return '';
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Pricing_Tables();
}
