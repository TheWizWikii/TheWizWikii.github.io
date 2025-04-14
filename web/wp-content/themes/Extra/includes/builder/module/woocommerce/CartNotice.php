<?php
/**
 * WooCommerce Modules: ET_Builder_Module_Woocommerce_Cart_Notice class
 *
 * The ET_Builder_Module_Woocommerce_Cart_Notice Class is responsible for rendering the
 * Cart Notice markup using the WooCommerce template.
 *
 * @package Divi\Builder
 *
 * @since   3.29
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class representing WooCommerce Cart Notice component.
 */
final class ET_Builder_Module_Woocommerce_Cart_Notice extends ET_Builder_Module {
	/**
	 * Initialize.
	 *
	 * @since 4.14.0 Fixed PHP Warnings
	 */
	public function init() {
		$this->name        = esc_html__( 'Woo Notice', 'et_builder' );
		$this->plural      = esc_html__( 'Woo Notice', 'et_builder' );
		$this->slug        = 'et_pb_wc_cart_notice';
		$this->vb_support  = 'on';
		$this->folder_name = 'et_pb_woo_modules';

		$this->settings_modal_toggles = array(
			'general'  => array(
				'toggles' => array(
					'main_content' => et_builder_i18n( 'Content' ),
				),
			),
			'advanced' => array(
				'toggles' => array(
					'title'       => array(
						'title'             => esc_html__( 'Title Text', 'et_builder' ),
						'tabbed_subtoggles' => true,
						'bb_icons_support'  => true,
						'sub_toggles'       => array(
							'p' => array(
								'name' => 'P',
								'icon' => 'text-left',
							),
							'a' => array(
								'name' => 'A',
								'icon' => 'text-link',
							),
						),
						'priority'          => 51,
					),
					'error'       => array(
						'title'    => esc_html__( 'Error Text', 'et_builder' ),
						'priority' => 52,
					),
					'body'        => array(
						'title'             => esc_html__( 'Body Text', 'et_builder' ),
						'tabbed_subtoggles' => true,
						'sub_toggles'       => array(
							'p' => array(
								'name' => 'P',
								'icon' => 'text-left',
							),
							'a' => array(
								'name' => 'A',
								'icon' => 'text-link',
							),
						),
						'priority'          => 53,
					),
					'field_label' => array(
						'title'    => esc_html__( 'Field Labels', 'et_builder' ),
						'priority' => 54,
					),
				),
			),
		);

		$this->main_css_element = implode(
			',',
			array(
				'%%order_class%% .woocommerce-message',
				'%%order_class%% .woocommerce-info',
				'%%order_class%% .woocommerce-error',
			)
		);

		$this->advanced_fields = array(
			'fonts'          => array(
				'title'       => array(
					'label'           => et_builder_i18n( 'Title' ),
					'css'             => array(
						'main'      => implode(
							',',
							array(
								'%%order_class%% .woocommerce-message',
								'%%order_class%% .woocommerce-info',
								'%%order_class%% .woocommerce-message a',
								'%%order_class%% .woocommerce-info a',
								'%%order_class%% .woocommerce-error li',
							)
						),
						// CPT style uses `!important` so outputting important is inevitable.
						'important' => 'all',
					),
					'font_size'       => array(
						'default' => '18px',
					),
					'line_height'     => array(
						'default' => '1.7em',
					),
					'hide_text_align' => true,
					'sub_toggle'      => 'p',
					'toggle_slug'     => 'title',
				),
				'title_link'  => array(
					'label'           => et_builder_i18n( 'Link' ),
					'css'             => array(
						'main' => implode(
							',',
							array(
								'%%order_class%% .woocommerce-message a',
								'%%order_class%% .woocommerce-info a',
							)
						),
					),
					'font_size'       => array(
						'default' => '18px',
					),
					'line_height'     => array(
						'default' => '1.7em',
					),
					'toggle_slug'     => 'title',
					'sub_toggle'      => 'a',
					'hide_text_align' => true,
				),
				'error'       => array(
					'label'           => esc_html__( 'Error', 'et_builder' ),
					'css'             => array(
						'main'      => '%%order_class%% .woocommerce-error li',

						// CPT style uses `!important` so outputting important is inevitable.
						'important' => 'all',
					),
					'font_size'       => array(
						'default' => '18px',
					),
					'line_height'     => array(
						'default' => '1.7em',
					),
					'hide_text_align' => true,
					'toggle_slug'     => 'error',
				),
				// Body Text should use body_*. However, Woo v1 was using body_*
				// which is migrated to title_* in Woo v2.
				//
				// This stops us from using body_* since body_* will be retained
				// in the Shortcode for downgrading. Hence, we use content_* for Body Text OG.
				'content'     => array(
					'label'       => et_builder_i18n( 'Body' ),
					'css'         => array(
						'main' => '%%order_class%% .woocommerce-form-login, %%order_class%% .woocommerce-form-coupon',
					),
					'font_size'   => array(
						'default'        => '',
						'allowed_values' => et_builder_get_acceptable_css_string_values( 'width' ),
						'allow_empty'    => true,
					),
					'line_height' => array(
						'default' => '1.7em',
					),
					'toggle_slug' => 'body',
					'sub_toggle'  => 'p',
				),
				'link'        => array(
					'label'       => et_builder_i18n( 'Link' ),
					'css'         => array(
						'main'      => implode(
							',',
							array(
								'%%order_class%% .woocommerce-form-login a',
								'%%order_class%% .woocommerce-form-coupon a',
							)
						),
						// CPT style uses `!important` so outputting important is inevitable.
						'important' => 'all',
					),
					'font_size'   => array(
						'default'        => '',
						'allowed_values' => et_builder_get_acceptable_css_string_values( 'width' ),
						'allow_empty'    => true,
					),
					'line_height' => array(
						'default' => '1.7em',
					),
					'toggle_slug' => 'body',
					'sub_toggle'  => 'a',
				),
				'field_label' => array(
					'label'       => esc_html__( 'Field Label', 'et_builder' ),
					'css'         => array(
						'main' => '%%order_class%% form .form-row label',
					),
					'font_size'   => array(
						'default' => '14px',
					),
					'line_height' => array(
						'default' => '2em',
					),
					'toggle_slug' => 'field_label',
					'sub_toggle'  => 'a',
				),
			),
			'max_width'      => array(
				'css' => array(
					'module_alignment' => '%%order_class%%.et_pb_module',
				),
			),
			'margin_padding' => array(
				'css'            => array(
					'padding'   => implode(
						',',
						array(
							'%%order_class%% .woocommerce-message',
							'%%order_class%% .woocommerce-info',
							'%%order_class%% .woocommerce-error',
						)
					),
					'margin'    => implode(
						',',
						array(
							'%%order_class%% .woocommerce-message',
							'%%order_class%% .woocommerce-info',
							'%%order_class%% .woocommerce-error',
						)
					),
					'important' => 'all',
				),
				'custom_padding' => array(
					'default' => '15px|15px|15px|15px|false|false',
				),
				'custom_margin'  => array(
					'default' => '0em|0em|2em|0em|false|false',
				),
			),
			'button'         => array(
				'button' => array(
					'label'           => et_builder_i18n( 'Button' ),
					'css'             => array(
						'main'      => implode(
							',',
							array(
								'%%order_class%% .wc-forward',
								'%%order_class%% button.button',
								'%%order_class%% .wc-backward',
							)
						),
						'important' => true,
					),
					'use_alignment'   => false,
					'border_width'    => array(
						'default' => '2px',
					),
					'box_shadow'      => array(
						'css' => array(
							'main' => implode(
								',',
								array(
									'%%order_class%% .wc-forward',
									'%%order_class%% button.button',

									// Selector intentionally changed to override default
									// WooCommerce box shadow.
									'%%order_class%% a.button',
								)
							),
						),
					),
					'margin_padding'  => array(
						'css' => array(
							'important' => 'all',
						),
					),
					'toggle_priority' => 60,
				),
			),
			'text'           => array(
				'use_background_layout' => false,
				'css'                   => array(
					'main'        => '%%order_class%% .woocommerce-message, %%order_class%% .woocommerce-info',
					'text_shadow' => '%%order_class%% .woocommerce-message, %%order_class%% .woocommerce-info',
					'important'   => array( 'text-shadow' ),
				),
				'options'               => array(
					'text_orientation' => array(
						'default' => 'left',
					),
				),
			),
			'background'     => array(
				'css' => array(
					// Defined explicitly to solve
					// @see https://github.com/elegantthemes/Divi/issues/17200#issuecomment-542140907
					'main'             => '%%order_class%% .woocommerce-message, %%order_class%% .woocommerce-info, %%order_class%% .woocommerce-error',
					'mask_selector'    => '%%order_class%% > .et_pb_background_mask',
					'pattern_selector' => '%%order_class%% > .et_pb_background_pattern',
					// Important is required to override
					// Appearance ⟶ Customize ⟶ Color schemes styles.
					'important'        => 'all',
				),
			),
			'border'         => array(
				'css' => array(
					'important' => true,
				),
			),
			'form_field'     => array(
				'form_field' => array(
					'label'           => esc_html__( 'Fields', 'et_builder' ),
					'css'             => array(
						'main'                         => '%%order_class%% form .input-text',
						'background_color'             => '.woocommerce %%order_class%% form .form-row .input-text',
						'background_color_hover'       => '.woocommerce %%order_class%% form .input-text:hover',
						'focus_background_color'       => '.woocommerce %%order_class%% form .input-text:focus',
						'focus_background_color_hover' => '%%order_class%% form .input-text:focus:hover',
						'placeholder_focus'            => '%%order_class%% .input-text:focus::-webkit-input-placeholder, %%order_class%% .input-text:focus::-moz-placeholder, %%order_class%% p .input-text:focus:-ms-input-placeholder',
						'padding'                      => '%%order_class%% form .form-row',
						'margin'                       => '%%order_class%% form .form-row',
						'form_text_color'              => '.woocommerce %%order_class%% form .form-row .input-text',
						'form_text_color_hover'        => '.woocommerce %%order_class%% form .form-row .input-text.input-text:hover',
						'focus_text_color'             => '.woocommerce %%order_class%% form .form-row .input-text:focus',
						'focus_text_color_hover'       => '%%order_class%% form .form-row .input-text:focus:hover',
					),
					'box_shadow'      => array(
						'css' => array(
							'main' => '%%order_class%% form .form-row .input-text',
						),
					),
					'border_styles'   => array(
						'form_field'       => array(
							'label_prefix' => esc_html__( 'Fields', 'et_builder' ),
							'css'          => array(
								'main' => array(
									'border_styles' => '%%order_class%% form .form-row input.input-text',
									'border_radii'  => '%%order_class%% form .form-row input.input-text',
								),
							),
							'defaults'     => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '0px',
									'style' => 'solid',
								),
							),
						),
						'form_field_focus' => array(
							'label_prefix' => esc_html__( 'Fields Focus', 'et_builder' ),
							'css'          => array(
								'main' => array(
									'border_styles' => '%%order_class%% form .form-row .input-text:focus',
									'border_radii'  => '%%order_class%% form .form-row input.input-text:focus',
								),
							),
							'defaults'     => array(
								'border_radii'  => 'on|0px|0px|0px|0px',
								'border_styles' => array(
									'width' => '0px',
									'style' => 'solid',
								),
							),
						),
					),
					'font_field'      => array(
						'css'         => array(
							// Required to override default WooCommerce styles.
							'main'      => '%%order_class%% form .form-row input.input-text',
							'important' => array( 'line-height', 'size', 'font' ),
						),
						'font_size'   => array(
							'default' => '14px',
						),
						'line_height' => array(
							'default' => '1.7em',
						),
					),
					'margin_padding'  => array(
						'css' => array(
							'main'    => '%%order_class%% form .form-row',
							'padding' => '%%order_class%% form .form-row input.input-text',
							'margin'  => '%%order_class%% form .form-row',
						),
					),
					'width'           => array(),
					'toggle_priority' => 55,
				),
				'form'       => array(
					'label'                  => esc_html__( 'Form', 'et_builder' ),
					'css'                    => array(
						'main'                   => '%%order_class%% form',
						'background_color'       => '%%order_class%% form',
						'background_color_hover' => '%%order_class%% form:hover',
					),
					'font_field'             => false,
					'margin_padding'         => array(
						'css' => array(
							'main' => implode(
								',',
								array(
									'%%order_class%% form.checkout_coupon',
									'%%order_class%% form.login',
									'%%order_class%% form.register',
								)
							),
						),
					),
					'text_color'             => false,
					'focus_background_color' => false,
					'focus_text_color'       => false,
					'border_styles'          => array(
						'form'       => array(
							'label_prefix' => 'Form',
							'css'          => array(
								'main'      => array(
									'border_styles' => '%%order_class%% form',
									'border_radii'  => '%%order_class%% form',
								),
								'important' => 'all',
							),
							'defaults'     => array(
								'border_radii'  => 'on|5px|5px|5px|5px',
								'border_styles' => array(
									'width' => '1px',
									'style' => 'solid',
									'color' => '#eeeeee',
								),
							),
						),
						'form_focus' => array(
							'css'      => array(
								'main'      => array(
									'border_styles' => '%%order_class%% form input:focus',
									'border_radii'  => '%%order_class%% form input:focus',
								),
								'important' => 'all',
							),
							'defaults' => array(
								'border_radii' => 'on|3px|3px|3px|3px',
							),
						),
					),
					'box_shadow'             => array(
						'css' => array(
							'main' => implode(
								',',
								array(
									'%%order_class%%  form.login',
									'%%order_class%%  form.checkout_coupon',
								)
							),
						),
					),
					'toggle_priority'        => 65,
				),
			),
			// Disable Link OG.
			'link_options'   => false,
			'borders'        => array(
				'default' => array(
					'css'      => array(

						// Redefining selectors is necessary while duplicating the module.
						'main'      => array(
							'border_radii'  => $this->main_css_element,
							'border_styles' => $this->main_css_element,
						),
						'important' => true,
					),
					'defaults' => array(
						'border_styles' => array(
							'width' => '0px',
							'style' => 'solid',
						),
					),
				),
			),
			'box_shadow'     => array(
				'default' => array(
					'css' => array(
						'main'      => implode(
							',',
							array(
								'%%order_class%% .woocommerce-message',
								'%%order_class%% .woocommerce-info',
								'%%order_class%% .woocommerce-error',
							)
						),
						'important' => true,
					),
				),
			),

			'transform'      => array(
				'css' => array(
					'main' => '%%order_class%% .et_pb_module_inner',
				),
			),
		);

		$this->custom_css_fields = array(
			'title'       => array(
				'label'    => et_builder_i18n( 'Title' ),
				'selector' => implode(
					',',
					array(
						'%%order_class%% .woocommerce-message',
						'%%order_class%% .woocommerce-info',
						'%%order_class%% .woocommerce-message a',
						'%%order_class%% .woocommerce-info a',
					)
				),
			),
			'title_link'  => array(
				'label'    => esc_html__( 'Title Link', 'et_builder' ),
				'selector' => implode(
					',',
					array(
						'%%order_class%% .woocommerce-message a',
						'%%order_class%% .woocommerce-info a',
					)
				),
			),
			'body'        => array(
				'label'    => et_builder_i18n( 'Body' ),
				'selector' => '%%order_class%% .woocommerce-form-login, %%order_class%% .woocommerce-form-coupon',
			),
			'link'        => array(
				'label'    => et_builder_i18n( 'Link' ),
				'selector' => implode(
					',',
					array(
						'%%order_class%% .woocommerce-form-login a',
						'%%order_class%% .woocommerce-form-coupon a',
					)
				),
			),
			'field_label' => array(
				'label'    => esc_html__( 'Field Label', 'et_builder' ),
				'selector' => '%%order_class%% .woocommerce-message',
			),
			'button'      => array(
				'label'    => esc_html__( 'Button', 'et_builder' ),
				'selector' => '%%order_class%% .wc-forward',
			),
			'fields'      => array(
				'label'    => esc_html__( 'Fields', 'et_builder' ),
				'selector' => '%%order_class%% form .input-text',
			),
			'login_form'  => array(
				'label'    => esc_html__( 'Login Form', 'et_builder' ),
				'selector' => '.woocommerce-form-login',
			),
			'coupon_form' => array(
				'label'    => esc_html__( 'Coupon Form', 'et_builder' ),
				'selector' => '.woocommerce-form-coupon',
			),
		);

		$this->help_videos = array(
			array(
				'id'   => '7X03vBPYJ1o',
				'name' => esc_html__( 'Divi WooCommerce Modules', 'et_builder' ),
			),
		);

		/*
		 * Disable default cart notice if needed. Priority need to be set at 100 to
		 * that the callback is being called after modules are being loaded.
		 *
		 * See: et_builder_load_framework()
		 */
		add_action(
			'wp',
			array(
				'ET_Builder_Module_Woocommerce_Cart_Notice',
				'disable_default_notice',
			),
			100
		);

		// Clear notices array which was modified during render.
		add_action( 'wp_footer', array( 'ET_Builder_Module_Woocommerce_Cart_Notice', 'clear_notices' ) );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @since 4.14.0 Dynamic defaults for Page Type & Placeholder color introduced.
	 */
	public function get_fields() {
		if ( ! method_exists( 'ET_Builder_Module_Helper_Woocommerce_Modules', 'get_field' ) ) {
			return array();
		}

		$fields = array(
			'required_field_indicator_color' => array(
				'label'           => esc_html__(
					'Required Field Indicator Color',
					'et_builder'
				),
				'description'     => esc_html__(
					'Pick a color to be used for the required field indicator.',
					'et_builder'
				),
				'type'            => 'color-alpha',
				'option_category' => 'button',
				'custom_color'    => true,
				'default'         => '',
				'tab_slug'        => 'advanced',
				'toggle_slug'     => 'field_label',
				'hover'           => 'tabs',
				'mobile_options'  => true,
				'priority'        => '5',
			),
			'page_type'                      => array(
				'label'            => esc_html__( 'Page Type', 'et_builder' ),
				'description'      => esc_html__( 'Here you can select the Page type.', 'et_builder' ),
				'type'             => 'select',
				'option_category'  => 'configuration',
				'options'          => array(
					'product'  => esc_html__( 'Product Page', 'et_builder' ),
					'cart'     => esc_html__( 'Cart Page', 'et_builder' ),
					'checkout' => esc_html__( 'Checkout Page', 'et_builder' ),
				),
				'toggle_slug'      => 'main_content',
				'default'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_page_type_default(),
				'computed_affects' => array(
					'__cart_notice',
				),
			),
			'product'                        => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'product',
				array(
					'default'          => ET_Builder_Module_Helper_Woocommerce_Modules::get_product_default(),
					'computed_affects' => array(
						'__cart_notice',
					),
					'show_if'          => array(
						'page_type' => 'product',
					),
				)
			),
			'__cart_notice'                  => array(
				'type'                => 'computed',
				'computed_callback'   => array(
					'ET_Builder_Module_Woocommerce_Cart_Notice',
					'get_cart_notice',
				),
				'computed_depends_on' => array(
					'product',
					'page_type',
				),
			),
			'fields_width'                   => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'fields_width',
				array(
					'tab_slug'    => 'advanced',
					'toggle_slug' => 'form_field',
				)
			),
			'placeholder_color'              => ET_Builder_Module_Helper_Woocommerce_Modules::get_field(
				'placeholder_color'
			),
		);

		return $fields;
	}

	/**
	 * Swaps login form template.
	 *
	 * By default WooCommerce displays these only when logged-out.
	 * However these templates must be shown in VB when logged-in. Hence we use these templates.
	 *
	 * @param string $template      Template.
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments.
	 * @param string $template_path Template path.
	 * @param string $default_path  Default path.
	 *
	 * @return string
	 */
	public static function swap_template( $template, $template_name, $args, $template_path, $default_path ) {
		$is_template_override = in_array(
			$template_name,
			array(
				'checkout/form-login.php',
				'global/form-login.php',
			),
			true
		);

		if ( $is_template_override ) {
			return trailingslashit( ET_BUILDER_DIR ) . 'feature/woocommerce/templates/' . $template_name;
		}

		return $template;
	}

	/**
	 * Swaps login form template.
	 *
	 * Aligning `Remember me` checkbox vertically requires change in HTML markup.
	 *
	 * @param string $template      Template.
	 * @param string $template_name Template name.
	 * @param array  $args          Arguments.
	 * @param string $template_path Template path.
	 * @param string $default_path  Default path.
	 *
	 * @return string
	 */
	public static function swap_template_frontend( $template, $template_name, $args, $template_path, $default_path ) {
		$is_template_override = in_array(
			$template_name,
			array(
				'global/form-login.php',
			),
			true
		);

		$template_name_parts = explode( '.', $template_name );

		if ( $is_template_override && 2 === count( $template_name_parts ) ) {
			$template_name_parts[0] = $template_name_parts[0] . '-fe';
			$template_name          = implode( '.', $template_name_parts );

			return trailingslashit( ET_BUILDER_DIR ) . 'feature/woocommerce/templates/' . $template_name;
		}

		return $template;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_transition_fields_css_props() {
		$fields = parent::get_transition_fields_css_props();

		$fields['required_field_indicator_color'] = array(
			'color' => '%%order_class%% form .form-row .required',
		);
		$fields['placeholder_color']              = array(
			'color' => array(
				'%%order_class%% form .form-row input.input-text::placeholder',
				'%%order_class%% form .form-row input.input-text::-webkit-input-placeholder',
				'%%order_class%% form .form-row input.input-text::-moz-placeholder',
				'%%order_class%% form .form-row input.input-text:-ms-input-placeholder',
			),
		);

		return $fields;
	}

	/**
	 * Output Coupon error message for Divi user to design.
	 *
	 * This output is intentional in VB. However, WooCommerce will handle display on the FE.
	 *
	 * @since 4.14.0
	 */
	public static function output_coupon_error_message() {
		$msg = __( 'Coupon "DIVI" does not exist!', 'et_builder' );
		wc_print_notice( $msg, 'error' );
	}

	/**
	 * Handle hooks.
	 *
	 * @param array $conditional_tags Conditional tags from AJAX callback.
	 */
	public static function maybe_handle_hooks( $conditional_tags ) {
		$is_tb              = et_()->array_get( $conditional_tags, 'is_tb', false );
		$is_use_placeholder = $is_tb || is_et_pb_preview();
		$class              = 'ET_Builder_Module_Woocommerce_Cart_Notice';

		/*
		 * Aligning `Remember me` checkbox vertically requires change in HTML markup.
		 */
		add_filter(
			'wc_get_template',
			[
				$class,
				'swap_template_frontend',
			],
			10,
			5
		);

		if ( et_fb_is_computed_callback_ajax() || $is_use_placeholder ) {
			add_action(
				'woocommerce_cart_is_empty',
				[
					$class,
					'output_coupon_error_message',
				]
			);

			/*
			 * Show Login form in VB.
			 *
			 * The swapped login form will display irrespective of the user logged-in status.
			 *
			 * Previously swapped template (FE) will only display the form when
			 * a user is not logged-in. Hence we use a different template in VB.
			 */
			add_filter(
				'wc_get_template',
				[
					$class,
					'swap_template',
				],
				10,
				5
			);
		}
	}

	/**
	 * Reset hooks.
	 *
	 * @param array $conditional_tags Conditional tags from AJAX callback.
	 */
	public static function maybe_reset_hooks( $conditional_tags ) {
		$is_tb              = et_()->array_get( $conditional_tags, 'is_tb', false );
		$is_use_placeholder = $is_tb || is_et_pb_preview();
		$class              = 'ET_Builder_Module_Woocommerce_Cart_Notice';

		remove_filter(
			'wc_get_template',
			[
				$class,
				'swap_template_frontend',
			],
			10,
			5
		);

		if ( et_fb_is_computed_callback_ajax() || $is_use_placeholder ) {
			remove_filter(
				'wc_get_template',
				[
					$class,
					'swap_template',
				]
			);

			remove_action(
				'woocommerce_cart_is_empty',
				[
					$class,
					'output_coupon_error_message',
				]
			);
		}
	}

	/**
	 * Disable default WooCommerce notice if current page's main query post content contains
	 * Cart Notice module to prevent duplicate cart notices being rendered AND to make Cart Notice
	 * module can render the notices correctly (notices are cleared once they are rendered)
	 *
	 * @since 3.29
	 */
	public static function disable_default_notice() {
		global $post;

		$remove_default_notices = false;
		$tb_layouts             = et_theme_builder_get_template_layouts();
		$tb_layout_types        = et_theme_builder_get_layout_post_types();

		// Check if a TB layout outputs the notices.
		foreach ( $tb_layout_types as $post_type ) {
			$id      = et_()->array_get( $tb_layouts, array( $post_type, 'id' ), 0 );
			$enabled = et_()->array_get( $tb_layouts, array( $post_type, 'enabled' ), 0 );

			if ( ! $id || ! $enabled ) {
				continue;
			}

			$content = get_post_field( 'post_content', $id );

			if ( has_shortcode( $content, 'et_pb_wc_cart_notice' ) ) {
				$remove_default_notices = true;
				break;
			}
		}

		// Check if the product itself outputs the notices.
		if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'et_pb_wc_cart_notice' ) ) {
			$remove_default_notices = true;
		}

		if ( $remove_default_notices ) {
			remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 );
		}
	}

	/**
	 * We update Woo Notices array during modules render and need to cleat it
	 * after Woo Product is fully rendered to avoid duplicated notifications on
	 * subsequent page loads.
	 */
	public static function clear_notices() {
		if ( ! empty( WC()->session ) ) {
			WC()->session->set( 'wc_notices', null );
		}
	}

	/**
	 * Gets the Cart message based on the Page type.
	 *
	 * @since 4.14.0
	 *
	 * @param array $args Args.
	 *
	 * @return string
	 */
	public static function get_cart_message( $args ) {
		$default_product_id = ET_Builder_Module_Helper_Woocommerce_Modules::get_product_default_value();

		$page_type  = et_()->array_get( $args, 'page_type', 'product' );
		$product_id = et_()->array_get( $args, 'product', $default_product_id );

		if ( 'cart' === $page_type ) {
			$message = wp_kses_post( apply_filters( 'wc_empty_cart_message', __( 'Your cart is currently empty.', 'woocommerce' ) ) );
		} elseif ( 'checkout' === $page_type ) {
			$message = apply_filters( 'woocommerce_checkout_login_message', esc_html__( 'Returning customer?', 'woocommerce' ) ) . ' <a href="#" class="showlogin">' . esc_html__( 'Click here to login', 'woocommerce' ) . '</a>';
		} else {
			// Since the default Page type is `Product`, the conditional `if` is ignored.
			$product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $product_id );

			if ( ! empty( $product ) && function_exists( 'wc_add_to_cart_message' ) ) {
				$message = wc_add_to_cart_message( $product->get_id(), false, true );
			} else {
				// A fallback.
				$message = sprintf(
					'&ldquo;%s&rdquo; %s',
					esc_html__( 'Product Name' ),
					esc_html__( 'has been added to cart.' )
				);
			}
		}

		return $message;
	}

	/**
	 * Gets the Cart notice markup.
	 *
	 * @param array $args Additional arguments.
	 *
	 * @return string
	 */
	public static function get_cart_notice( $args = array(), $conditional_tags = array() ) {
		$message   = self::get_cart_message( $args );
		$page_type = et_()->array_get( $args, 'page_type', 'product' );

		$is_tb      = et_()->array_get( $conditional_tags, 'is_tb', false );
		$is_builder = et_fb_is_computed_callback_ajax() || $is_tb || is_et_pb_preview();

		$args = wp_parse_args(
			array(
				'wc_cart_message' => $message,
				'page_type'       => $page_type,
				'is_builder'      => $is_builder,
			),
			$args
		);

		self::maybe_handle_hooks( $conditional_tags );

		if ( $is_builder || et_core_is_fb_enabled() ) {
			if ( 'checkout' === $page_type ) {
				$markup = et_builder_wc_render_module_template(
					'woocommerce_checkout_login_form',
					$args
				);
			} elseif ( 'cart' === $page_type ) {
				$markup = et_builder_wc_render_module_template( 'wc_cart_empty_template' );
			} else {
				$markup = et_builder_wc_render_module_template( 'wc_print_notice', $args );
			}
		} else {
			if ( 'checkout' === $page_type ) {
				$notices_markup = et_builder_wc_render_module_template(
					'woocommerce_output_all_notices'
				);

				$form_markup = et_builder_wc_render_module_template(
					'woocommerce_checkout_login_form',
					$args
				);

				$markup = sprintf( '%s%s', $notices_markup, $form_markup );
			} elseif ( 'cart' === $page_type && ( is_null( WC()->cart ) || WC()->cart->is_empty() ) ) {
				$markup = et_builder_wc_render_module_template( 'wc_cart_empty_template' );
			} else {
				$markup = et_builder_wc_render_module_template( 'woocommerce_output_all_notices', $args );

				return $markup;
			}
		}

		self::maybe_reset_hooks( $conditional_tags );

		return $markup;
	}

	/**
	 * Gets the Button classname.
	 *
	 * @used-by ET_Builder_Module_Helper_Woocommerce_Modules::add_custom_button_icons()
	 *
	 * @return string
	 */
	public function get_button_classname() {
		return 'button';
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
		$this->add_classname( 'woocommerce' );
		$page_type = et_()->array_get( $this->props, 'page_type', 'product' );

		/*
		 * In front end, do not print cart notice module if there is no notices exist.
		 *
		 * There is no custom style rendered below (to make sure that styles are correctly cached
		 * nevertheless), thus it is fine to exit early;
		 */
		if ( ! empty( WC()->session )
			&& empty( WC()->session->get( 'wc_notices', array() ) )
			&& ! in_array( $page_type, array( 'cart', 'checkout' ), true )
			&& ! is_et_pb_preview()
		) {
			return '';
		}

		$fields_width = et_()->array_get( $this->props, 'fields_width', false );
		if ( false !== $fields_width ) {
			$this->add_classname( "et_pb_fields_layout_{$fields_width}" );
		}

		ET_Builder_Module_Helper_Woocommerce_Modules::process_background_layout_data( $render_slug, $this );
		ET_Builder_Module_Helper_Woocommerce_Modules::process_custom_button_icons( $render_slug, $this );

		// Placeholder Color.
		$placeholder_selectors = array(
			'%%order_class%% form .form-row input.input-text::placeholder',
			'%%order_class%% form .form-row input.input-text::-webkit-input-placeholder',
			'%%order_class%% form .form-row input.input-text::-moz-placeholder',
			'%%order_class%% form .form-row input.input-text:-ms-input-placeholder',
		);

		$this->generate_styles(
			array(
				'base_attr_name'                  => 'placeholder_color',
				'selector'                        => join( ', ', $placeholder_selectors ),
				'hover_pseudo_selector_location'  => 'suffix',
				'sticky_pseudo_selector_location' => 'prefix',
				'css_property'                    => 'color',
				'important'                       => false,
				'render_slug'                     => $render_slug,
				'type'                            => 'color',
			)
		);

		$this->add_classname( $this->get_text_orientation_classname() );

		// Handle Required Field Indicator Color responsive and hover fields.
		$required_field_indicator_color_values = et_pb_responsive_options()->get_property_values( $this->props, 'required_field_indicator_color' );
		$required_field_indicator_color_hover  = $this->get_hover_value( 'required_field_indicator_color' );
		$required_field_indicator_selector     = '%%order_class%% form .form-row .required';

		et_pb_responsive_options()->generate_responsive_css(
			$required_field_indicator_color_values,
			$required_field_indicator_selector,
			'color',
			$render_slug,
			'',
			'color'
		);

		if ( et_builder_is_hover_enabled( 'required_field_indicator_color', $this->props ) ) {
			ET_Builder_Element::set_style(
				$render_slug,
				array(
					'selector'    => '%%order_class%% form .form-row:hover .required',
					'declaration' => sprintf(
						'color: %1$s;',
						esc_html( $required_field_indicator_color_hover )
					),
				)
			);
		}

		if ( in_array( $page_type, [ 'checkout', 'cart' ], true )
			&& isset( WC()->cart )
			&& ! is_null( WC()->cart && method_exists( WC()->cart, 'check_cart_items' ) ) ) {
			$return = WC()->cart->check_cart_items();

			if ( 'checkout' === $page_type && wc_notice_count( 'error' ) > 0 ) {
				$this->add_classname( 'et_pb_hide_module' );
			}
		}

		if ( 'Extra' === et_core_get_theme_info( 'Name' ) ) {
			// Handle Padding left because of the Icons in Extra theme.
			$padding_values        = et_pb_responsive_options()->get_property_values( $this->props, 'custom_padding' );
			$padding_left_selector = '%%order_class%% .woocommerce-info, %%order_class%% .woocommerce-error';

			$padding_left_values = array();

			foreach ( $padding_values as $device => $padding_value ) {

				if ( empty( $padding_value ) ) {
					$padding_left_values[ $device ] = '';
				} else {
					$psv = explode( '|', $padding_value );

					if ( isset( $psv[3] ) ) {
						$padding_left_values[ $device ] = sprintf( 'calc( %s + 34px ) !important', $psv[3] );
					}
				}
			}

			et_pb_responsive_options()->generate_responsive_css(
				$padding_left_values,
				$padding_left_selector,
				'padding-left',
				$render_slug,
				'',
				'padding'
			);
		}

		$output = self::get_cart_notice( $this->props );

		return $this->_render_module_wrapper( $output, $render_slug );
	}
}

new ET_Builder_Module_Woocommerce_Cart_Notice();
