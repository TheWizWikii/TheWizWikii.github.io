<?php

class ET_Builder_Module_Fullwidth_Image extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Fullwidth Image', 'et_builder' );
		$this->plural     = esc_html__( 'Fullwidth Images', 'et_builder' );
		$this->slug       = 'et_pb_fullwidth_image';
		$this->vb_support = 'on';
		$this->fullwidth  = true;
		$this->defaults   = array(
			'align' => 'left',
		);

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Image' ),
					'link'         => et_builder_i18n( 'Link' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'overlay' => et_builder_i18n( 'Overlay' ),
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
			'margin_padding'  => array(
				'css' => array(
					'important' => 'all',
				),
			),
			'box_shadow'      => array(
				'default' => array(
					'css' => array(
						'overlay' => 'inset',
					),
				),
			),
			'fonts'           => false,
			'text'            => false,
			'button'          => false,
			'link_options'    => false,
			'position_fields' => array(
				'default' => 'relative',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'cYwqxoHnjNA',
				'name' => esc_html__( 'An introduction to the Fullwidth Image module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'src'                 => array(
				'label'              => et_builder_i18n( 'Image' ),
				'type'               => 'upload',
				'option_category'    => 'basic_option',
				'upload_button_text' => et_builder_i18n( 'Upload an image' ),
				'choose_text'        => esc_attr__( 'Choose an Image', 'et_builder' ),
				'update_text'        => esc_attr__( 'Set As Image', 'et_builder' ),
				'affects'            => array(
					'alt',
					'title_text',
				),
				'description'        => esc_html__( 'Upload your desired image, or type in the URL to the image you would like to display.', 'et_builder' ),
				'toggle_slug'        => 'main_content',
				'dynamic_content'    => 'image',
				'mobile_options'     => true,
				'hover'              => 'tabs',
			),
			'alt'                 => array(
				'label'           => esc_html__( 'Image Alternative Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'depends_on'      => array(
					'src',
				),
				'description'     => esc_html__( 'This defines the HTML ALT text. A short description of your image can be placed here.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'title_text'          => array(
				'label'           => esc_html__( 'Image Title Text', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'on',
				'depends_on'      => array(
					'src',
				),
				'description'     => esc_html__( 'This defines the HTML Title text.', 'et_builder' ),
				'tab_slug'        => 'custom_css',
				'toggle_slug'     => 'attributes',
				'dynamic_content' => 'text',
			),
			'show_in_lightbox'    => array(
				'label'            => esc_html__( 'Open In Lightbox', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'off' => et_builder_i18n( 'No' ),
					'on'  => et_builder_i18n( 'Yes' ),
				),
				'default_on_front' => 'off',
				'affects'          => array(
					'url',
					'url_new_window',
					'use_overlay',
				),
				'toggle_slug'      => 'link',
				'description'      => esc_html__( 'Here you can choose whether or not the image should open in Lightbox. Note: if you select to open the image in Lightbox, url options below will be ignored.', 'et_builder' ),
			),
			'url'                 => array(
				'label'           => esc_html__( 'Image Link URL', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'basic_option',
				'depends_show_if' => 'off',
				'affects'         => array(
					'use_overlay',
				),
				'description'     => esc_html__( 'If you would like your image to be a link, input your destination URL here. No link will be created if this field is left blank.', 'et_builder' ),
				'toggle_slug'     => 'link',
				'dynamic_content' => 'url',
			),
			'url_new_window'      => array(
				'label'            => esc_html__( 'Image Link Target', 'et_builder' ),
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
			'use_overlay'         => array(
				'label'            => esc_html__( 'Image Overlay', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'layout',
				'options'          => array(
					'off' => et_builder_i18n( 'Off' ),
					'on'  => et_builder_i18n( 'On' ),
				),
				'default_on_front' => 'off',
				'affects'          => array(
					'overlay_icon_color',
					'hover_overlay_color',
					'hover_icon',
				),
				'depends_show_if'  => 'on',
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'overlay',
				'description'      => esc_html__( 'If enabled, an overlay color and icon will be displayed when a visitors hovers over the image', 'et_builder' ),
			),
			'overlay_icon_color'  => array(
				'label'           => esc_html__( 'Overlay Icon Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'Here you can define a custom color for the overlay icon', 'et_builder' ),
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'hover_overlay_color' => array(
				'label'           => esc_html__( 'Hover Overlay Color', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'Here you can define a custom color for the overlay', 'et_builder' ),
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'hover_icon'          => array(
				'label'           => esc_html__( 'Hover Icon Picker', 'et_builder' ),
				'type'            => 'select_icon',
				'option_category' => 'configuration',
				'class'           => array( 'et-pb-font-icon' ),
				'depends_show_if' => 'on',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'description'     => esc_html__( 'Here you can define a custom icon for the overlay', 'et_builder' ),
				'mobile_options'  => true,
				'sticky'          => true,
			),
		);

		return $fields;
	}

	public function get_transition_fields_css_props() {
		$fields  = parent::get_transition_fields_css_props();
		$filters = $this->get_transition_filters_fields_css_props( 'child_filters' );

		// Note: overlay_icon_color and hover_overlay color requires transition but `.et_overlay`'s
		// static class already set transition to all so no dynamic transition needs to be added.

		return array_merge( $fields, $filters );
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
		$multi_view        = et_pb_multi_view_options( $this );
		$sticky            = et_pb_sticky_options();
		$src               = $this->props['src'];
		$alt               = $this->props['alt'];
		$title_text        = $this->props['title_text'];
		$url               = $this->props['url'];
		$url_new_window    = $this->props['url_new_window'];
		$show_in_lightbox  = $this->props['show_in_lightbox'];
		$hover_icon        = $this->props['hover_icon'];
		$hover_icon_tablet = $this->props['hover_icon_tablet'];
		$hover_icon_phone  = $this->props['hover_icon_phone'];
		$hover_icon_sticky = $sticky->get_value( 'hover_icon', $this->props );
		$use_overlay       = $this->props['use_overlay'];
		$animation_style   = $this->props['animation_style'];

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();

		// Load up Dynamic Content (if needed) to capture Featured Image objects.
		// In this way we can process `alt` and `title` attributes defined in
		// the WP Media Library when they haven't been specified by the user in
		// Module Settings.
		if ( empty( $alt ) || empty( $title_text ) ) {
			$raw_src   = et_()->array_get( $this->attrs_unprocessed, 'src' );
			$src_value = et_builder_parse_dynamic_content( $raw_src );

			if ( $src_value->is_dynamic() && $src_value->get_content() === 'post_featured_image' ) {
				// If there is no user-specified ALT attribute text, check the WP
				// Media Library entry for text that may have been added there.
				if ( empty( $alt ) ) {
					$alt = et_builder_resolve_dynamic_content( 'post_featured_image_alt_text', array(), get_the_ID(), 'display' );
				}

				// If there is no user-specified TITLE attribute text, check the WP
				// Media Library entry for text that may have been added there.
				if ( empty( $title_text ) ) {
					$title_text = et_builder_resolve_dynamic_content( 'post_featured_image_title_text', array(), get_the_ID(), 'display' );
				}
			}
		}

		// overlay can be applied only if image has link or if lightbox enabled
		$is_overlay_applied = 'on' === $use_overlay && ( 'on' === $show_in_lightbox || ( 'off' === $show_in_lightbox && '' !== $url ) ) ? 'on' : 'off';
		$is_sticky_module   = $sticky->is_sticky_module( $this->props );

		if ( 'on' === $is_overlay_applied ) {
			$this->generate_styles(
				array(
					'hover'          => false,
					'base_attr_name' => 'overlay_icon_color',
					'selector'       => '%%order_class%% .et_overlay:before',
					'css_property'   => 'color',
					'render_slug'    => $render_slug,
					'important'      => true,
					'type'           => 'color',
				)
			);

			$this->generate_styles(
				array(
					'hover'          => false,
					'base_attr_name' => 'hover_overlay_color',
					'selector'       => '%%order_class%% .et_overlay',
					'css_property'   => 'background-color',
					'render_slug'    => $render_slug,
					'type'           => 'color',
				)
			);

			$overlay_output = ET_Builder_Module_Helper_Overlay::render(
				array(
					'icon'        => $hover_icon,
					'icon_tablet' => $hover_icon_tablet,
					'icon_phone'  => $hover_icon_phone,
					'icon_sticky' => $hover_icon_sticky,
				)
			);

			// Overlay Icon Styles.
			$this->generate_styles(
				array(
					'hover'          => false,
					'utility_arg'    => 'icon_font_family',
					'render_slug'    => $render_slug,
					'base_attr_name' => 'hover_icon',
					'important'      => true,
					'selector'       => '%%order_class%% .et_overlay:before',
					'processor'      => array(
						'ET_Builder_Module_Helper_Style_Processor',
						'process_extended_icon',
					),
				)
			);
		}

		$image_attrs = array(
			'src'   => '{{src}}',
			'alt'   => esc_attr( $alt ),
			'title' => esc_attr( $title_text ),
		);

		$image_attachment_class = et_pb_media_options()->get_image_attachment_class( $this->props, 'src' );

		if ( ! empty( $image_attachment_class ) ) {
			$image_attrs['class'] = esc_attr( $image_attachment_class );
		}

		$image_html = $multi_view->render_element(
			array(
				'tag'      => 'img',
				'attrs'    => $image_attrs,
				'required' => 'src',
			)
		);

		$output = sprintf(
			'%1$s
			%2$s',
			$image_html,
			'on' === $is_overlay_applied ? $overlay_output : ''
		);

		if ( 'on' === $show_in_lightbox ) {
			$output = sprintf(
				'<a href="%1$s" class="et_pb_lightbox_image" title="%3$s">%2$s</a>',
				esc_attr( $src ),
				$output,
				esc_attr( $title_text )
			);
		} elseif ( '' !== $url ) {
			$output = sprintf(
				'<a href="%1$s"%3$s>%2$s</a>',
				esc_url( $url ),
				$output,
				( 'on' === $url_new_window ? ' target="_blank"' : '' )
			);
		}

		// Module classnames
		if ( ! in_array( $animation_style, array( '', 'none' ) ) ) {
			$this->add_classname( 'et-waypoint' );
		}

		if ( 'on' === $is_overlay_applied ) {
			$this->add_classname( 'et_pb_has_overlay' );
		}

		$output = sprintf(
			'<div%3$s class="%2$s">
				%5$s
				%4$s
				%6$s
				%7$s
				%1$s
			</div>',
			$output,
			$this->module_classname( $render_slug ),
			$this->module_id(),
			$video_background,
			$parallax_image_background,
			et_core_esc_previously( $this->background_pattern() ), // #6
			et_core_esc_previously( $this->background_mask() ) // #7
		);

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Fullwidth_Image();
}
