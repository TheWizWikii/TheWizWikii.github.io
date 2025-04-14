<?php

class ET_Builder_Module_Gallery extends ET_Builder_Module {
	function init() {
		$this->name       = esc_html__( 'Gallery', 'et_builder' );
		$this->plural     = esc_html__( 'Galleries', 'et_builder' );
		$this->slug       = 'et_pb_gallery';
		$this->vb_support = 'on';

		$this->settings_modal_toggles = array(
			'general'    => array(
				'toggles' => array(
					'main_content' => esc_html__( 'Images', 'et_builder' ),
					'elements'     => et_builder_i18n( 'Elements' ),
				),
			),
			'advanced'   => array(
				'toggles' => array(
					'layout'  => et_builder_i18n( 'Layout' ),
					'overlay' => et_builder_i18n( 'Overlay' ),
					'image'   => array(
						'title' => et_builder_i18n( 'Image' ),
					),
					'text'    => array(
						'title'    => et_builder_i18n( 'Text' ),
						'priority' => 49,
					),
				),
			),
			'custom_css' => array(
				'toggles' => array(
					'animation' => array(
						'title'    => esc_html__( 'Animation', 'et_builder' ),
						'priority' => 90,
					),
				),
			),
		);

		$this->main_css_element = '%%order_class%%.et_pb_gallery';
		$this->advanced_fields  = array(
			'fonts'          => array(
				'title'      => array(
					'label'        => et_builder_i18n( 'Title' ),
					'css'          => array(
						'main'  => "{$this->main_css_element} .et_pb_gallery_title",
						'hover' => "{$this->main_css_element} .et_pb_gallery_title:hover",
					),
					'header_level' => array(
						'default' => 'h3',
					),
				),
				'caption'    => array(
					'label'           => esc_html__( 'Caption', 'et_builder' ),
					'use_all_caps'    => true,
					'css'             => array(
						'main'  => "{$this->main_css_element} .mfp-title, {$this->main_css_element} .et_pb_gallery_caption",
						'hover' => "{$this->main_css_element} .mfp-title:hover, {$this->main_css_element} .et_pb_gallery_caption:hover",
					),
					'line_height'     => array(
						'range_settings' => array(
							'min'  => '1',
							'max'  => '100',
							'step' => '1',
						),
					),
					'depends_show_if' => 'off',
				),
				'pagination' => array(
					'label'      => esc_html__( 'Pagination', 'et_builder' ),
					'css'        => array(
						'main'       => "{$this->main_css_element} .et_pb_gallery_pagination a",
						'hover'      => "{$this->main_css_element} .et_pb_gallery_pagination a:hover",
						'text_align' => "{$this->main_css_element} .et_pb_gallery_pagination ul",
					),
					'text_align' => array(
						'options' => et_builder_get_text_orientation_options( array( 'justified' ), array() ),
					),
				),
			),
			'borders'        => array(
				'default' => array(
					'css' => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_pb_gallery_item",
							'border_styles' => "{$this->main_css_element} .et_pb_gallery_item",
						),
					),
				),
				'image'   => array(
					'css'             => array(
						'main' => array(
							'border_radii'  => "{$this->main_css_element} .et_pb_gallery_image",
							'border_styles' => "{$this->main_css_element} .et_pb_gallery_image",
						),
					),
					'label_prefix'    => et_builder_i18n( 'Image' ),
					'tab_slug'        => 'advanced',
					'toggle_slug'     => 'image',
					'depends_on'      => array( 'fullwidth' ),
					'depends_show_if' => 'off',
				),
			),
			'box_shadow'     => array(
				'default' => array(
					'show_if' => array(
						'fullwidth' => 'on',
					),
				),
				'image'   => array(
					'label'             => esc_html__( 'Image Box Shadow', 'et_builder' ),
					'option_category'   => 'layout',
					'tab_slug'          => 'advanced',
					'toggle_slug'       => 'image',
					'show_if'           => array(
						'fullwidth' => 'off',
					),
					'css'               => array(
						'main'    => '%%order_class%% .et_pb_gallery_image',
						'overlay' => 'inset',
					),
					'default_on_fronts' => array(
						'color'    => '',
						'position' => '',
					),
				),
			),
			'margin_padding' => array(
				'css' => array(
					'important' => array( 'custom_margin' ), // needed to overwrite last module margin-bottom styling
				),
			),
			'max_width'      => array(
				'css' => array(
					'module_alignment' => '%%order_class%%.et_pb_gallery.et_pb_module',
				),
			),
			'text'           => array(
				'use_background_layout' => true,
				'css'                   => array(
					'main'        => implode(
						', ',
						array(
							"{$this->main_css_element} .et_pb_gallery_title",
							"{$this->main_css_element} .mfp-title",
							"{$this->main_css_element} .et_pb_gallery_caption",
							"{$this->main_css_element} .et_pb_gallery_pagination a",
						)
					),
					'text_shadow' => "{$this->main_css_element}.et_pb_gallery_grid",
				),
				'options'               => array(
					'background_layout' => array(
						'default' => 'light',
						'hover'   => 'tabs',
					),
				),
			),
			'filters'        => array(
				'css'                  => array(
					'main' => '%%order_class%%',
				),
				'child_filters_target' => array(
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'image',
				),
			),
			'image'          => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_gallery_image img',
				),
			),
			'scroll_effects' => array(
				'grid_support' => 'yes',
			),
			'button'         => false,
		);

		$this->custom_css_fields = array(
			'gallery_item'              => array(
				'label'    => esc_html__( 'Gallery Item', 'et_builder' ),
				'selector' => '.et_pb_gallery_item',
			),
			'overlay'                   => array(
				'label'    => et_builder_i18n( 'Overlay' ),
				'selector' => '.et_overlay',
			),
			'overlay_icon'              => array(
				'label'    => esc_html__( 'Overlay Icon', 'et_builder' ),
				'selector' => '.et_overlay:before',
			),
			'gallery_item_title'        => array(
				'label'    => esc_html__( 'Gallery Item Title', 'et_builder' ),
				'selector' => '.et_pb_gallery_title',
			),
			'gallery_item_caption'      => array(
				'label'    => esc_html__( 'Gallery Item Caption', 'et_builder' ),
				'selector' => '.et_pb_gallery_caption',
			),
			'gallery_pagination'        => array(
				'label'    => esc_html__( 'Gallery Pagination', 'et_builder' ),
				'selector' => '.et_pb_gallery_pagination',
			),
			'gallery_pagination_active' => array(
				'label'    => esc_html__( 'Pagination Active Page', 'et_builder' ),
				'selector' => '.et_pb_gallery_pagination a.active',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => 'BRjX-pNHk-s',
				'name' => esc_html__( 'An introduction to the Gallery module', 'et_builder' ),
			),
		);
	}

	function get_fields() {
		$fields = array(
			'gallery_ids'            => array(
				'label'            => esc_html__( 'Images', 'et_builder' ),
				'description'      => esc_html__( 'Choose the images that you would like to appear in the image gallery.', 'et_builder' ),
				'type'             => 'upload-gallery',
				'computed_affects' => array(
					'__gallery',
				),
				'option_category'  => 'basic_option',
				'toggle_slug'      => 'main_content',
			),
			'gallery_orderby'        => array(
				'label'            => esc_html__( 'Image Order', 'et_builder' ),
				'description'      => esc_html__( 'Select an ordering method for the gallery. This controls which gallery items appear first in the list.', 'et_builder' ),
				'type'             => $this->is_loading_bb_data() ? 'hidden' : 'select',
				'options'          => array(
					''     => et_builder_i18n( 'Default' ),
					'rand' => esc_html__( 'Random', 'et_builder' ),
				),
				'default'          => 'off',
				'class'            => array( 'et-pb-gallery-ids-field' ),
				'computed_affects' => array(
					'__gallery',
				),
				'toggle_slug'      => 'main_content',
			),
			'gallery_captions'       => array(
				'type'             => 'hidden',
				'class'            => array( 'et-pb-gallery-captions-field' ),
				'computed_affects' => array(
					'__gallery',
				),
			),
			'fullwidth'              => array(
				'label'            => et_builder_i18n( 'Layout' ),
				'type'             => 'select',
				'option_category'  => 'layout',
				'options'          => array(
					'off' => esc_html__( 'Grid', 'et_builder' ),
					'on'  => esc_html__( 'Slider', 'et_builder' ),
				),
				'default_on_front' => 'off',
				'description'      => esc_html__( 'Toggle between the various gallery layout types.', 'et_builder' ),
				'affects'          => array(
					'zoom_icon_color',
					'caption_font',
					'caption_text_color',
					'caption_line_height',
					'caption_font_size',
					'caption_all_caps',
					'caption_letter_spacing',
					'hover_icon',
					'hover_overlay_color',
					'auto',
					'posts_number',
					'show_title_and_caption',
					'show_pagination',
					'orientation',
					'border_radii_image',
					'border_styles_image',
				),
				'computed_affects' => array(
					'__gallery',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
			),
			'posts_number'           => array(
				'default'         => 4,
				'label'           => esc_html__( 'Image Count', 'et_builder' ),
				'type'            => 'text',
				'option_category' => 'configuration',
				'description'     => esc_html__( 'Define the number of images that should be displayed per page.', 'et_builder' ),
				'depends_show_if' => 'off',
				'toggle_slug'     => 'main_content',
			),
			'orientation'            => array(
				'label'            => esc_html__( 'Thumbnail Orientation', 'et_builder' ),
				'type'             => 'select',
				'options_category' => 'configuration',
				'options'          => array(
					'landscape' => esc_html__( 'Landscape', 'et_builder' ),
					'portrait'  => esc_html__( 'Portrait', 'et_builder' ),
				),
				'default_on_front' => 'landscape',
				'description'      => sprintf(
					'%1$s<br><small><em><strong>%2$s:</strong> %3$s <a href="//wordpress.org/plugins/force-regenerate-thumbnails" target="_blank">%4$s</a>.</em></small>',
					esc_html__( 'Choose the orientation of the gallery thumbnails.', 'et_builder' ),
					esc_html__( 'Note', 'et_builder' ),
					esc_html__( 'If this option appears to have no effect, you might need to', 'et_builder' ),
					esc_html__( 'regenerate your thumbnails', 'et_builder' )
				),
				'depends_show_if'  => 'off',
				'computed_affects' => array(
					'__gallery',
				),
				'tab_slug'         => 'advanced',
				'toggle_slug'      => 'layout',
			),
			'show_title_and_caption' => array(
				'label'            => esc_html__( 'Show Title and Caption', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'description'      => esc_html__( 'Whether or not to show the title and caption for images (if available).', 'et_builder' ),
				'depends_show_if'  => 'off',
				'toggle_slug'      => 'elements',
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'show_pagination'        => array(
				'label'            => esc_html__( 'Show Pagination', 'et_builder' ),
				'type'             => 'yes_no_button',
				'option_category'  => 'configuration',
				'options'          => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default_on_front' => 'on',
				'description'      => esc_html__( 'Enable or disable pagination for this feed.', 'et_builder' ),
				'depends_show_if'  => 'off',
				'toggle_slug'      => 'elements',
				'computed_affects' => array(
					'__gallery',
				),
				'mobile_options'   => true,
				'hover'            => 'tabs',
			),
			'zoom_icon_color'        => array(
				'label'           => esc_html__( 'Overlay Icon Color', 'et_builder' ),
				'description'     => esc_html__( 'Here you can define a custom color for the zoom icon.', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'hover_overlay_color'    => array(
				'label'           => esc_html__( 'Overlay Background Color', 'et_builder' ),
				'description'     => esc_html__( 'Here you can define a custom color for the overlay', 'et_builder' ),
				'type'            => 'color-alpha',
				'custom_color'    => true,
				'depends_show_if' => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'hover_icon'             => array(
				'label'           => esc_html__( 'Overlay Icon', 'et_builder' ),
				'description'     => esc_html__( 'Here you can define a custom icon for the overlay', 'et_builder' ),
				'type'            => 'select_icon',
				'option_category' => 'configuration',
				'class'           => array( 'et-pb-font-icon' ),
				'depends_show_if' => 'off',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'overlay',
				'mobile_options'  => true,
				'sticky'          => true,
			),
			'__gallery'              => array(
				'type'                => 'computed',
				'computed_callback'   => array( 'ET_Builder_Module_Gallery', 'get_gallery' ),
				'computed_depends_on' => array(
					'gallery_ids',
					'gallery_orderby',
					'gallery_captions',
					'fullwidth',
					'orientation',
					'show_pagination',
				),
			),
		);

		return $fields;
	}

	/**
	 * Get attachment data for gallery module
	 *
	 * @param array $args {
	 *     Gallery Options
	 *
	 *     @type array  $gallery_ids     Attachment Ids of images to be included in gallery.
	 *     @type string $gallery_orderby `orderby` arg for query. Optional.
	 *     @type string $fullwidth       on|off to determine grid / slider layout
	 *     @type string $orientation     Orientation of thumbnails (landscape|portrait).
	 * }
	 * @param array $conditional_tags
	 * @param array $current_page
	 *
	 * @return array Attachments data
	 */
	static function get_gallery( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$attachments = array();

		$defaults = array(
			'gallery_ids'      => array(),
			'gallery_orderby'  => '',
			'gallery_captions' => array(),
			'fullwidth'        => 'off',
			'orientation'      => 'landscape',
		);

		$args = wp_parse_args( $args, $defaults );

		$attachments_args = array(
			'include'        => $args['gallery_ids'],
			'post_status'    => 'inherit',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image',
			'order'          => 'ASC',
			'orderby'        => 'post__in',
		);

		// Woo Gallery module shouldn't display placeholder image when no Gallery image is
		// available.
		// @see https://github.com/elegantthemes/submodule-builder/pull/6706#issuecomment-542275647
		if ( isset( $args['attachment_id'] ) ) {
			$attachments_args['attachment_id'] = $args['attachment_id'];
		}

		if ( 'rand' === $args['gallery_orderby'] ) {
			$attachments_args['orderby'] = 'rand';
		}

		if ( 'on' === $args['fullwidth'] ) {
			$width  = 1080;
			$height = 9999;
		} else {
			$width  = 400;
			$height = ( 'landscape' === $args['orientation'] ) ? 284 : 516;
		}

		$width  = (int) apply_filters( 'et_pb_gallery_image_width', $width );
		$height = (int) apply_filters( 'et_pb_gallery_image_height', $height );

		$_attachments = get_posts( $attachments_args );

		foreach ( $_attachments as $key => $val ) {
			$attachments[ $key ]                  = $_attachments[ $key ];
			$attachments[ $key ]->image_alt_text  = get_post_meta( $val->ID, '_wp_attachment_image_alt', true );
			$attachments[ $key ]->image_src_full  = wp_get_attachment_image_src( $val->ID, 'full' );
			$attachments[ $key ]->image_src_thumb = wp_get_attachment_image_src( $val->ID, array( $width, $height ) );
		}

		return $attachments;
	}

	/**
	 * Wrapper for ET_Builder_Module_Gallery::get_gallery() which is intended to be extended by
	 * module which uses gallery module renderer so relevant argument for other module can be added
	 *
	 * @since 3.29
	 * @see ET_Builder_Module_Gallery::get_gallery()
	 * @param array $args {
	 *     Gallery Options
	 *
	 *     @type array  $gallery_ids     Attachment Ids of images to be included in gallery.
	 *     @type string $gallery_orderby `orderby` arg for query. Optional.
	 *     @type string $fullwidth       on|off to determine grid / slider layout
	 *     @type string $orientation     Orientation of thumbnails (landscape|portrait).
	 * }
	 *
	 * @return array
	 */
	public function get_attachments( $args = array() ) {
		return self::get_gallery( $args );
	}

	public function get_pagination_alignment() {
		$text_orientation = isset( $this->props['pagination_text_align'] ) ? $this->props['pagination_text_align'] : '';

		return et_pb_get_alignment( $text_orientation );
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
		$sticky                 = et_pb_sticky_options();
		$multi_view             = et_pb_multi_view_options( $this );
		$gallery_ids            = $this->props['gallery_ids'];
		$fullwidth              = $this->props['fullwidth'];
		$show_title_and_caption = $this->props['show_title_and_caption'];
		$posts_number           = $this->props['posts_number'];
		$show_pagination        = $this->props['show_pagination'];
		$gallery_orderby        = $this->props['gallery_orderby'];
		$auto                   = $this->props['auto'];
		$auto_speed             = $this->props['auto_speed'];
		$orientation            = $this->props['orientation'];
		$pagination_text_align  = $this->get_pagination_alignment();
		$header_level           = $this->props['title_level'];

		$hover_icon        = $this->props['hover_icon'];
		$hover_icon_values = et_pb_responsive_options()->get_property_values( $this->props, 'hover_icon' );
		$hover_icon_tablet = isset( $hover_icon_values['tablet'] ) ? $hover_icon_values['tablet'] : '';
		$hover_icon_phone  = isset( $hover_icon_values['phone'] ) ? $hover_icon_values['phone'] : '';
		$hover_icon_sticky = $sticky->get_value( 'hover_icon', $this->props );

		// validate $orientation, it should be either 'landscape' or 'portrait', default to 'landscape'.
		$orientation = 'portrait' === $orientation ? 'portrait' : 'landscape';

		// Zoom Icon Color.
		$this->generate_styles(
			array(
				'hover'          => false,
				'base_attr_name' => 'zoom_icon_color',
				'selector'       => '%%order_class%% .et_overlay:before',
				'css_property'   => 'color',
				'render_slug'    => $render_slug,
				'important'      => true,
				'type'           => 'color',
			)
		);

		// Hover Overlay Color.
		$this->generate_styles(
			array(
				'hover'          => false,
				'base_attr_name' => 'hover_overlay_color',
				'selector'       => '%%order_class%% .et_overlay',
				'css_property'   => array( 'background-color', 'border-color' ),
				'render_slug'    => $render_slug,
				'type'           => 'color',
			)
		);

		// Get gallery item data
		$attachments = $this->get_attachments(
			array(
				'gallery_ids'     => $gallery_ids,
				'gallery_orderby' => $gallery_orderby,
				'fullwidth'       => $fullwidth,
				'orientation'     => $orientation,
			)
		);

		if ( empty( $attachments ) ) {
			return '';
		}

		$video_background          = $this->video_background();
		$parallax_image_background = $this->get_parallax_image_background();
		$posts_number              = 0 === intval( $posts_number ) ? 4 : intval( $posts_number );

		// Module classnames
		$this->add_classname(
			array(
				$this->get_text_orientation_classname(),
			)
		);

		// Background layout class names.
		$background_layout_class_names = et_pb_background_layout_options()->get_background_layout_class( $this->props );
		$this->add_classname( $background_layout_class_names );

		if ( 'on' === $fullwidth ) {
			$this->add_classname(
				array(
					'et_pb_slider',
					'et_pb_gallery_fullwidth',
				)
			);
		} else {
			$this->add_classname( 'et_pb_gallery_grid' );
		}

		if ( 'on' === $auto && 'on' === $fullwidth ) {
			$this->add_classname(
				array(
					'et_slider_auto',
					"et_slider_speed_{$auto_speed}",
					'clearfix',
				)
			);
		}

		// Background layout data attributes.
		$data_background_layout = et_pb_background_layout_options()->get_background_layout_attrs( $this->props );

		$output = sprintf(
			'<div%1$s class="%2$s"%4$s>%5$s%6$s%7$s%8$s
				<div class="et_pb_gallery_items et_post_gallery clearfix" data-per_page="%3$d">',
			$this->module_id(),
			$this->module_classname( $render_slug ),
			esc_attr( $posts_number ),
			et_core_esc_previously( $data_background_layout ),
			$parallax_image_background,
			$video_background,
			et_core_esc_previously( $this->background_pattern() ), // #7
			et_core_esc_previously( $this->background_mask() ) // #8
		);

		// Images: Add CSS Filters and Mix Blend Mode rules (if set)
		if ( array_key_exists( 'image', $this->advanced_fields ) && array_key_exists( 'css', $this->advanced_fields['image'] ) ) {
			$generate_css_filters_item = $this->generate_css_filters(
				$render_slug,
				'child_',
				self::$data_utils->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' )
			);
		}

		// Overlay output.
		$overlay_output = 'on' === $fullwidth ? '' : ET_Builder_Module_Helper_Overlay::render(
			array(
				'icon'        => $hover_icon,
				'icon_tablet' => $hover_icon_tablet,
				'icon_phone'  => $hover_icon_phone,
				'icon_sticky' => $hover_icon_sticky,
			)
		);

		if ( 'on' !== $fullwidth ) {
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

		$images_count = 0;

		foreach ( $attachments as $id => $attachment ) {
			$image_attrs = array(
				'alt' => $attachment->image_alt_text,
			);

			if ( 'on' !== $fullwidth ) {
				$image_attrs['srcset'] = $attachment->image_src_full[0] . ' 479w, ' . $attachment->image_src_thumb[0] . ' 480w';
				$image_attrs['sizes']  = '(max-width:479px) 479px, 100vw';
			}

			$image_attachment_class = et_pb_media_options()->get_image_attachment_class( $this->props, '', $attachment->ID );

			if ( ! empty( $image_attachment_class ) ) {
				$image_attrs['class'] = esc_attr( $image_attachment_class );
			}

			$image_output = sprintf(
				'<a href="%1$s" title="%2$s">
					%3$s
					%4$s
				</a>',
				esc_url( $attachment->image_src_full[0] ),
				esc_attr( $attachment->post_title ),
				$this->render_image( $attachment->image_src_thumb[0], $image_attrs, false ),
				et_core_esc_previously( $overlay_output )
			);

			$gallery_order = self::_get_index( array( self::INDEX_MODULE_ORDER, $render_slug ) );
			$item_class    = sprintf( ' et_pb_gallery_item_%1$s_%2$s', $gallery_order, $images_count );

			$output .= sprintf(
				'<div class="et_pb_gallery_item%2$s%1$s%3$s%4$s">',
				esc_attr( ' ' . implode( ' ', $background_layout_class_names ) ),
				( 'on' !== $fullwidth ? ' et_pb_grid_item' : '' ),
				$generate_css_filters_item,
				$item_class
			);

			$images_count++;

			$output .= sprintf(
				'<div class="et_pb_gallery_image %1$s">
					%2$s
				</div>',
				esc_attr( $orientation ),
				$image_output
			);

			if ( 'on' !== $fullwidth && $multi_view->has_value( 'show_title_and_caption', 'on' ) ) {
				if ( trim( $attachment->post_title ) ) {
					$output .= $multi_view->render_element(
						array(
							'tag'        => et_pb_process_header_level( $header_level, 'h3' ),
							'content'    => wptexturize( $attachment->post_title ),
							'attrs'      => array(
								'class' => 'et_pb_gallery_title',
							),
							'visibility' => array(
								'show_title_and_caption' => 'on',
							),
						)
					);
				}
				if ( trim( $attachment->post_excerpt ) ) {
					$output .= $multi_view->render_element(
						array(
							'tag'        => 'p',
							'content'    => wptexturize( $attachment->post_excerpt ),
							'attrs'      => array(
								'class' => 'et_pb_gallery_caption',
							),
							'visibility' => array(
								'show_title_and_caption' => 'on',
							),
						)
					);
				}
			}
			$output .= '</div>';
		}

		$output .= '</div>';

		if ( 'on' !== $fullwidth && $multi_view->has_value( 'show_pagination', 'on' ) ) {
			$pagination_classes = array( 'et_pb_gallery_pagination' );
			if ( 'justify' === $pagination_text_align ) {
				$pagination_classes[] = 'et_pb_gallery_pagination_justify';
			}

			$output .= $multi_view->render_element(
				array(
					'tag'        => 'div',
					'attrs'      => array(
						'class' => implode( ' ', $pagination_classes ),
					),
					'visibility' => array(
						'show_pagination' => 'on',
					),
				)
			);
		}

		$output .= '</div>';

		return $output;
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_Gallery();
}
