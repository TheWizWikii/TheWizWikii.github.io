<?php // phpcs:ignore WordPress.Files.FileName -- We don't follow WP filename format.
if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly.
	exit;
}

/**
 * ET_Builder_Plugin_Compat_Yith_Dynamic_Pricing_Discounts class file.
 *
 * @class   ET_Builder_Plugin_Compat_Yith_Dynamic_Pricing_Discounts
 * @package Divi
 * @subpackage Builder
 * @since 4.18.1
 * @link https://yithemes.com/themes/plugins/yith-woocommerce-dynamic-pricing-and-discounts/
 */
class ET_Builder_Plugin_Compat_Yith_Dynamic_Pricing_Discounts extends ET_Builder_Plugin_Compat_Base {
	/**
	 * Constructor.
	 *
	 * @since 4.18.1
	 */
	public function __construct() {
		$this->plugin_id = 'yith-woocommerce-dynamic-pricing-and-discounts-premium/init.php';
		$this->init_hooks();
	}

	/**
	 * Hook methods to WordPress.
	 *
	 * @since 4.18.1
	 *
	 * @return void
	 */
	public function init_hooks() {
		// Bail if there's no version found.
		if ( ! $this->get_plugin_version() ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', [ $this, 'pricing_styles' ], 99 );
		add_filter( 'et_builder_module_et_pb_wc_price_outer_wrapper_attrs', [ $this, 'add_summary_class' ], 99, 2 );
	}

	/**
	 * Includes the Pricing module stylesheet.
	 *
	 * This stylesheet should only be included when YITH discounts plugin is enabled.
	 *
	 * @return void
	 */
	public function pricing_styles() {
		wp_enqueue_style(
			'et-builder-yith-pricing-discounts',
			ET_BUILDER_URI . '/plugin-compat/styles/yith-woocommerce-dynamic-pricing-and-discounts-premium.css',
			[],
			ET_BUILDER_VERSION
		);
	}

	/**
	 * Adds the class required for YITH plugin to be compatible.
	 *
	 * @param array  $attrs  Module props.
	 * @param string $module Module slug.
	 *
	 * @return array $attrs
	 */
	public function add_summary_class( $attrs, $module ) {
		$_               = et_();
		$classes         = explode( ' ', $_->array_get( $attrs, 'class', '' ) );
		$updated_classes = array_merge( $classes, [ 'summary' ] );
		$attrs['class']  = implode( ' ', $updated_classes );

		return $attrs;
	}
}

new ET_Builder_Plugin_Compat_Yith_Dynamic_Pricing_Discounts();
