<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Reviews class
 *
 * The ET_Builder_Module_Woocommerce_Reviews Class is responsible for rendering the
 * Reviews markup.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */
if ( ! class_exists( 'ET_Builder_Module_Gallery' ) ) {
	require_once ET_BUILDER_DIR_RESOLVED_PATH . '/module/Comments.php';
}

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Reviews component.
 */
class ET_Builder_Module_Woocommerce_Reviews extends ET_Builder_Module_Comments {
	/**
	 * Modify properties defined on base module's (comment) init()
	 *
	 * @since 3.29
	 */
	public function init() {
		parent::init();

		// Define basic module information.
		$this->name        = esc_html__( 'Woo Product Reviews', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Product Reviews', 'et_builder' );
		$this->slug        = 'et_pb_wc_reviews';
		$this->folder_name = 'et_pb_woo_modules';

		// Modify toggle settings.
		$this->settings_modal_toggles['general']['toggles']['main_content'] = array(
			'title'    => et_builder_i18n( 'Content' ),
			'priority' => 1,
		);

		$this->settings_modal_toggles['advanced']['toggles']['rating'] = array(
			'title'    => esc_html__( 'Star Rating', 'et_builder' ),
			'priority' => 60,
		);

		$this->settings_modal_toggles['advanced']['toggles']['image'] = array(
			'title'    => et_builder_i18n( 'Image' ),
			'priority' => 30,
		);

		// Modify advanced field settings.
		$this->advanced_fields['fonts']['header']['label']                            = esc_html__( 'Review Count', 'et_builder' );
		$this->advanced_fields['fonts']['header']['header_level']['default']          = 'h2';
		$this->advanced_fields['fonts']['header']['header_level']['computed_affects'] = array(
			'__reviews',
		);
		$this->advanced_fields['fonts']['header']['font_size']                        = array(
			'default' => '26px',
		);
		$this->advanced_fields['fonts']['header']['line_height']                      = array(
			'default' => '1em',
		);
		$this->advanced_fields['fonts']['title']['font_size']                         = array(
			'default' => '14px',
		);
		$this->advanced_fields['fonts']['title']['line_height']                       = array(
			'default' => '1.7em',
		);
		$this->advanced_fields['fonts']['header']['css']['main']                      = "{$this->main_css_element} h1.woocommerce-Reviews-title, {$this->main_css_element} h2.woocommerce-Reviews-title, {$this->main_css_element} h3.woocommerce-Reviews-title, {$this->main_css_element} h4.woocommerce-Reviews-title, {$this->main_css_element} h5.woocommerce-Reviews-title, {$this->main_css_element} h6.woocommerce-Reviews-title";
		$this->advanced_fields['fonts']['meta']['css']['main']                        = "{$this->main_css_element} #reviews #comments ol.commentlist li .comment-text p.meta, %%order_class%% .comment-form-rating label";
		$this->advanced_fields['fonts']['meta']['hide_text_align']                    = true;
		$this->advanced_fields['fonts']['body']['css']['main']                        = "{$this->main_css_element} .comment-text .description";
		$this->advanced_fields['fonts']['rating']                                     = array(
			'label'            => esc_html__( 'Star Rating', 'et_builder' ),
			'css'              => array(
				'main'                 => '%%order_class%% .star-rating, %%order_class%% .comment-form-rating p.stars a',
				'letter_spacing'       => '%%order_class%% .star-rating, %%order_class%% .comment-form-rating p.stars',
				'letter_spacing_hover' => '%%order_class%% .star-rating:hover, %%order_class%% .comment-form-rating p.stars:hover',
				'color'                => '%%order_class%% .star-rating > span:before, %%order_class%% .comment-form-rating p.stars a',
				'color_hover'          => '%%order_class%% .star-rating:hover > span:before, %%order_class%% .comment-form-rating p.stars:hover a',
				'main'                 => '%%order_class%% .star-rating, %%order_class%% .comment-form-rating p.stars a',
				'text_align'           => '%%order_class%% .comment-form-rating p.stars',
			),
			'font_size'        => array(
				'default' => 14,
			),
			'hide_font'        => true,
			'hide_line_height' => true,
			'hide_text_shadow' => true,
		);
		$this->advanced_fields['borders']['image']['css']['main']['border_radii']     = '%%order_class%%.et_pb_wc_reviews #reviews #comments ol.commentlist li img.avatar';
		$this->advanced_fields['borders']['image']['css']['main']['border_styles']    = '%%order_class%%.et_pb_wc_reviews #reviews #comments ol.commentlist li img.avatar';
		$this->advanced_fields['box_shadow']['image']['css']['main']                  = '%%order_class%%.et_pb_wc_reviews #reviews #comments ol.commentlist li img.avatar';
		$this->advanced_fields['image']['css']['main']                                = '%%order_class%%.et_pb_wc_reviews #reviews #comments ol.commentlist li img.avatar';

		$this->advanced_fields['form_field']['form_field']['font_field']['css']['main'] = "{$this->main_css_element} #commentform textarea, {$this->main_css_element} #commentform input[type='text'], {$this->main_css_element} #commentform input[type='email'], {$this->main_css_element} #commentform input[type='url']";
		$this->advanced_fields['form_field']['form_field']['font_field']['font_size']   = array(
			'default' => '18px',
		);

		// Disable form title heading level because it uses span tag.
		unset( $this->advanced_fields['fonts']['title']['header_level'] );

		$this->custom_css_fields = array(
			'main_header'     => array(
				'label'    => esc_html__( 'Reviews Count', 'et_builder' ),
				'selector' => '.woocommerce-Reviews-title',
			),
			'comment_body'    => array(
				'label'    => esc_html__( 'Review Body', 'et_builder' ),
				'selector' => '.comment_container',
			),
			'comment_meta'    => array(
				'label'    => esc_html__( 'Review Meta', 'et_builder' ),
				'selector' => '#reviews #comments ol.commentlist li .comment-text p.meta',
			),
			'comment_content' => array(
				'label'    => esc_html__( 'Review Rating', 'et_builder' ),
				'selector' => '.comment-form-rating',
			),
			'comment_avatar'  => array(
				'label'    => esc_html__( 'Review Avatar', 'et_builder' ),
				'selector' => '#reviews #comments ol.commentlist li img.avatar',
			),
			'new_title'       => array(
				'label'    => esc_html__( 'New Review Title', 'et_builder' ),
				'selector' => '#reply-title',
			),
			'message_field'   => array(
				'label'    => esc_html__( 'Message Field', 'et_builder' ),
				'selector' => '.comment-form-comment textarea#comment',
			),
			'name_field'      => array(
				'label'    => esc_html__( 'Name Field', 'et_builder' ),
				'selector' => '.comment-form-author input#author',
			),
			'email_field'     => array(
				'label'    => esc_html__( 'Email Field', 'et_builder' ),
				'selector' => '.comment-form-email input#email',
			),
			'submit_button'   => array(
				'label'    => esc_html__( 'Submit Button', 'et_builder' ),
				'selector' => '#submit',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => '7X03vBPYJ1o',
				'name' => esc_html__( 'Divi WooCommerce Modules', 'et_builder' ),
			),
		);

		// Insert classname to module wrapper.
		add_filter(
			'et_builder_wc_reviews_classes',
			array(
				$this,
				'add_wc_reviews_classname',
			),
			10,
			2
		);
	}

	/**
	 * Insert Woo Reviews specific fields and modify fields inherited from base module (comments).
	 *
	 * @return array
	 */
	public function get_fields() {
		$et_accent_color = et_builder_accent_color();

		// Get base module (comment)'s fields.
		$fields = parent::get_fields();

		$fields['product']        = ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
			'product',
			array(
				'computed_affects' => array(
					'__reviews',
				),
				'default'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_product_default(),
			)
		);
		$fields['product_filter'] = ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
			'product_filter',
			array(
				'computed_affects' => array(
					'__reviews',
				),
			)
		);
		$fields['show_rating']    = array(
			'label'            => esc_html__( 'Show Rating', 'et_builder' ),
			'type'             => 'yes_no_button',
			'option_category'  => 'configuration',
			'options'          => array(
				'on'  => esc_html__( 'Yes', 'et_builder' ),
				'off' => esc_html__( 'No', 'et_builder' ),
			),
			'default_on_front' => 'on',
			'toggle_slug'      => 'elements',
			'description'      => esc_html__( 'Turn rating on or off.', 'et_builder' ),
			'mobile_options'   => true,
			'hover'            => 'tabs',
		);
		$fields['__reviews']      = array(
			'type'                => 'computed',
			'computed_callback'   => array(
				'ET_Builder_Module_Woocommerce_Reviews',
				'get_reviews_html',
			),
			'computed_depends_on' => array(
				'product',
				'product_filter',
				'header_level',
			),
		);

		/*
		 * Modify base module (comment) fields; These fields can't be hidden because Woo Reviews
		 * uses base module's `render()` method which it expects
		 * `$this->props['show_reply']` to exist
		 */
		$fields['show_reply']['type'] = 'hidden';

		return $fields;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['rating_letter_spacing'] = array(
			'font-size'      => '%%order_class%% .star-rating, %%order_class%% .comment-form-rating p.stars',
			'width'          => '%%order_class%% .star-rating',
			'letter-spacing' => '%%order_class%% .star-rating, %%order_class%% .comment-form-rating p.stars',
		);
		$fields['rating_font_size']      = array(
			'font-size' => '%%order_class%% .star-rating, %%order_class%% .comment-form-rating p.stars a',
		);

		return $fields;
	}

	/**
	 * Get reviews HTML.
	 *
	 * @param array $args             Arguments from Computed Prop AJAX call.
	 * @param array $conditional_tags Conditional Tags.
	 * @param array $current_page     Current page args.
	 *
	 * @return string
	 */
	public static function get_reviews_html( $args = array(), $conditional_tags = array(), $current_page = array() ) {
		$maybe_product_id = 'current';

		$defaults = array(
			'header_level' => 'h2',
		);

		$args = wp_parse_args( $args, $defaults );

		// Get correct product ID when current request is computed callback request.
		if ( ET_Builder_Element::get_current_post_id() && ! et_builder_tb_enabled() ) {
			$maybe_product_id = ET_Builder_Element::get_current_post_id();
		}

		if ( array_key_exists( 'id', $current_page ) ) {
			$maybe_product_id = $current_page['id'];
		}

		if ( array_key_exists( 'product', $args ) && ! empty( $args['product'] ) ) {
			$maybe_product_id = $args['product'];
		}

		$is_tb = et_builder_tb_enabled();

		if ( $is_tb || is_et_pb_preview() ) {
			global $product;

			et_theme_builder_wc_set_global_objects();
		} else {
			$product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $maybe_product_id );
		}

		if ( ! ( $product instanceof WC_Product ) ) {
			return '';
		}

		$reviews_markup = self::get_reviews_markup( $product, $args['header_level'], true );

		if ( $is_tb || is_et_pb_preview() ) {
			et_theme_builder_wc_reset_global_objects();
		}

		return $reviews_markup;
	}

	/**
	 * Gets the Reviews markup.
	 *
	 * This includes the Reviews and the Review comment form.
	 *
	 * @since 3.29
	 *
	 * @param WC_Product $product      WooCommerce Product.
	 * @param string     $header_level Heading level.
	 * @param bool       $is_ajax      Should be set to TRUE when used in AJAX call for proper
	 *                                 results.
	 *
	 * @return string
	 */
	public static function get_reviews_markup( $product, $header_level, $is_ajax = false ) {
		if ( ! ( $product instanceof WC_Product ) ) {
			return '';
		}

		if ( ! comments_open( $product->get_id() ) ) {
			return '';
		}

		$reviews_title = ET_Builder_Module_Helper_Woocommerce_Modules::get_reviews_title( $product );
		// Product could be changed using the Product filter in the Settings modal.
		// Hence supplying the Product ID to fetch data based on the selected Product.
		$reviews         = get_comments(
			array(
				'post_id' => $product->get_id(),
				'status'  => 'approve',
			)
		);
		$total_pages     = get_comment_pages_count( $reviews );
		$reviews_content = wp_list_comments(
			array(
				'callback' => 'woocommerce_comments',
				'echo'     => false,
			),
			$reviews
		);

		// Supply the `$total_pages` var. Otherwise $pagination would always be empty.
		if ( $is_ajax ) {
			$page = get_query_var( 'cpage' );
			if ( ! $page ) {
				$page = 1;
			}
			$args = array(
				'base'         => add_query_arg( 'cpage', '%#%' ),
				'format'       => '',
				'total'        => $total_pages,
				'current'      => $page,
				'echo'         => false,
				'add_fragment' => '#comments',
				'type'         => 'list',
			);
			global $wp_rewrite;
			if ( $wp_rewrite->using_permalinks() ) {
				$args['base'] = user_trailingslashit( trailingslashit( get_permalink() ) . $wp_rewrite->comments_pagination_base . '-%#%', 'commentpaged' );
			}

			$pagination = paginate_links( $args );
		} else {
			$pagination = paginate_comments_links(
				array(
					'echo'  => false,
					'type'  => 'list',
					'total' => $total_pages,
				)
			);
		}

		// Pass $product, $reviews to unify the flow of data.
		$reviews_comment_form = ET_Builder_Module_Helper_Woocommerce_Modules::get_reviews_comment_form( $product, $reviews );

		return sprintf(
			'
			<div id="reviews" class="woocommerce-Reviews">
				<div id="comments">
					<%3$s class="woocommerce-Reviews-title">
						%1$s
					</%3$s>
					<ol class="commentlist">
						%2$s
					</ol>
					<nav class="woocommerce-pagination">
						%4$s
					</nav>
				</div>
				<div id="review_form_wrapper">
					%5$s
				</div>
				<div class="clear"></div>
			</div>
			',
			/* 1$s */
			$reviews_title,
			/* 2$s */
			$reviews_content,
			/* 3$s */
			$header_level,
			/* 4$s */
			$pagination,
			/* 5$s */
			$reviews_comment_form
		);
	}

	/**
	 * Add classname into module wrapper.
	 *
	 * @param array $classname    List of class names.
	 * @param int   $render_count Count of times the module is rendered.
	 */
	public function add_wc_reviews_classname( $classname, $render_count ) {
		/*
		 * Woo Reviews can't add `et_pb_wc_reviews` via `$this->add_classname()` method because
		 * comments module specifically remove slug classname at the end of its rendering process
		 * {@link https://github.com/elegantthemes/submodule-builder/pull/2910/files#diff-832b621946ab4f4dee33ddbf442d0225R348}
		 */
		$classname[] = $this->slug;

		return $classname;
	}

	/**
	 * Remove action and filter hook performed before comment contents rendering by extending
	 * the method because Woo Reviews doesn't need it
	 *
	 * @since 3.29
	 */
	public function before_comments_content() {
		/* intentionally empty*/
	}

	/**
	 * Render review (comments) content
	 *
	 * @since 3.29
	 */
	public function get_comments_content() {
		$header_level     = $this->props['header_level'];
		$product          = $this->props['product'];
		$verified_product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $product );

		return self::get_reviews_markup(
			$verified_product,
			et_pb_process_header_level( $header_level, 'h2' )
		);
	}

	/**
	 * Remove action and filter hook performed before comment contents rendering by extending
	 * the method because Woo Reviews doesn't need it.
	 *
	 * @since 3.29
	 */
	public function after_comments_content() {
		/* intentionally empty*/
	}

	/**
	 * {@inheritdoc}
	 */
	public function render( $attrs, $content, $render_slug ) {
		// Image - CSS Filters.
		if ( et_()->array_get( $this->advanced_fields, 'image.css', false ) ) {
			$classes = $this->generate_css_filters( $this->slug, 'child_', et_()->array_get( $this->advanced_fields['image']['css'], 'main', '%%order_class%%' ) );
			$this->add_classname( $classes );
		}

		ET_Builder_Module_Helper_Woocommerce_Modules::add_star_rating_style(
			$render_slug,
			$this->props,
			'%%order_class%% .star-rating',
			'%%order_class%% .star-rating:hover'
		);

		// Fixes right text alignment of review form star rating. By default, WC adds text-indent -999em to
		// hide the original rating number. However, it causes an issue if the alignment is set to right
		// position. We should push the rating value to the right side and hide the overflow.
		$rating_alignments       = array();
		$rating_alignment_values = et_pb_responsive_options()->get_property_values( $this->props, 'rating_text_align' );
		foreach ( $rating_alignment_values as $mode => $value ) {
			// Should be added only when the alignment is right.
			if ( 'right' !== $value ) {
				continue;
			}

			$rating_alignments[ $mode ] = 'overflow: hidden; text-indent: 999em;';
		}

		// Generate style for desktop, tablet, and phone.
		et_pb_responsive_options()->declare_responsive_css(
			$rating_alignments,
			'%%order_class%% p.stars a',
			$render_slug
		);

		return parent::render( $attrs, $content, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Reviews();
