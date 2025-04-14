<?php

class ET_Builder_Module_Tabs_Item extends ET_Builder_Module {
	function init() {
		$this->name                        = esc_html__( 'Tab', 'et_builder' );
		$this->plural                      = esc_html__( 'Tabs', 'et_builder' );
		$this->slug                        = 'et_pb_tab';
		$this->vb_support                  = 'on';
		$this->type                        = 'child';
		$this->child_title_var             = 'title';
		$this->advanced_setting_title_text = esc_html__( 'New Tab', 'et_builder' );
		$this->settings_text               = esc_html__( 'Tab Settings', 'et_builder' );
		$this->main_css_element            = '%%order_class%%';

		$this->settings_modal_toggles = array(
			'general' => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Text' ),
				),
			),
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'body' => array(
					'label'          => et_builder_i18n( 'Body' ),
					'css'            => array(
						'main'         => ".et_pb_tabs .et_pb_all_tabs {$this->main_css_element}.et_pb_tab",
						'line_height'  => ".et_pb_tabs {$this->main_css_element}.et_pb_tab p",
						'limited_main' => ".et_pb_tabs .et_pb_all_tabs {$this->main_css_element}.et_pb_tab, .et_pb_tabs .et_pb_all_tabs {$this->main_css_element}.et_pb_tab p",
					),
					'line_height'    => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'block_elements' => array(
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
					),
				),
				'tab'  => array(
					'label'           => esc_html__( 'Tab', 'et_builder' ),
					'css'             => array(
						'main'      => ".et_pb_tabs .et_pb_tabs_controls li{$this->main_css_element}, .et_pb_tabs .et_pb_tabs_controls li{$this->main_css_element} a",
						'color'     => ".et_pb_tabs .et_pb_tabs_controls li{$this->main_css_element} a",
						'important' => 'all',
					),
					'line_height'     => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'hide_text_align' => true,
				),
			),
			'background'     => array(
				'css'      => array(
					'main' => ".et_pb_tabs {$this->main_css_element}.et_pb_tab",
				),
				'settings' => array(
					'color' => 'alpha',
				),
			),
			'borders'        => array(
				'default' => false,
			),
			'margin_padding' => array(
				'use_margin' => false,
				'css'        => array(
					'padding' => '.et_pb_tabs .et_pb_tab%%order_class%%',
				),
			),
			'box_shadow'     => array(
				'default' => false,
			),
			'text'           => false,
			'max_width'      => false,
			'height'         => false,
			'button'         => false,
			'scroll_effects' => false,
			'sticky'         => false,
		);

		$this->custom_css_fields = array(
			'main_element' => array(
				'label'    => et_builder_i18n( 'Main Element' ),
				'selector' => ".et_pb_tabs div{$this->main_css_element}.et_pb_tab",
			),
		);
	}

	function get_fields() {
		$fields = array(
			'title'   => array(
				'label'           => et_builder_i18n( 'Title' ),
				'type'            => 'text',
				'description'     => esc_html__( 'The title will be used within the tab button for this tab.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'option_category' => 'basic_option',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
			'content' => array(
				'label'           => et_builder_i18n( 'Body' ),
				'type'            => 'tiny_mce',
				'description'     => esc_html__( 'Here you can define the content that will be placed within the current tab.', 'et_builder' ),
				'toggle_slug'     => 'main_content',
				'dynamic_content' => 'text',
				'option_category' => 'basic_option',
				'mobile_options'  => true,
				'hover'           => 'tabs',
			),
		);
		return $fields;
	}

	/**
	 * Set the `product` prop on TabItem.
	 *
	 * `product` prop is only available w/ the Parents' Tab module and not w/ TabsItem module.
	 *
	 * The global $et_pb_wc_tabs variable is set
	 */
	function maybe_inherit_values() {
		// Inheriting Tabs attribute.
		global $et_pb_wc_tabs;

		if ( isset( $et_pb_wc_tabs ) && ! empty( $et_pb_wc_tabs['product'] ) ) {
			$this->props['product'] = $et_pb_wc_tabs['product'];
		}

	}

	/**
	 * Return the Product ID when set. Otherwise return parent::get_the_ID()
	 *
	 * $this->props['product'] is set using
	 *
	 * @see ET_Builder_Module_Tabs_Item->maybe_inherit_values()
	 *
	 * @return bool|int
	 */
	function get_the_ID() {
		if ( ! isset( $this->props['product'] ) ) {
			return parent::get_the_ID();
		}

		$product = wc_get_product( absint( $this->props['product'] ) );
		if ( $product instanceof WC_Product ) {
			return $product->get_id();
		}
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
		global $et_pb_tab_titles;
		global $et_pb_tab_classes;

		$multi_view = et_pb_multi_view_options( $this );

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		$i = 0;

		$multi_view->set_default_value( 'title', esc_html__( 'Tab', 'et_builder' ) );

		$et_pb_tab_titles[]  = $multi_view->get_values( 'title' );
		$et_pb_tab_classes[] = ET_Builder_Element::get_module_order_class( $render_slug );

		// Module classnames
		$this->add_classname(
			array(
				'clearfix',
				$this->get_text_orientation_classname(),
			)
		);

		if ( 1 === count( $et_pb_tab_titles ) ) {
			$this->add_classname( 'et_pb_active_content' );
		}

		// Remove automatically added classnames
		$this->remove_classname(
			array(
				'et_pb_module',
			)
		);

		$content = $multi_view->render_element(
			array(
				'tag'     => 'div',
				'content' => '{{content}}',
				'attrs'   => array(
					'class' => 'et_pb_tab_content',
				),
			)
		);

		$output = sprintf(
			'<div class="%2$s">
				%4$s
				%3$s
				%5$s
				%6$s
				%1$s
			</div>',
			$content,
			$this->module_classname( $render_slug ),
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $this->background_pattern() ), // #5
			et_core_esc_previously( $this->background_mask() ) // #6
		);

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Tabs_Item();
}
