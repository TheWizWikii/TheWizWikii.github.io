<?php
/**
 * Cross-sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cross-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( $cross_sells ) : ?>

	<div class="cross-sells">
		<?php
		$heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'You may be interested in&hellip;', 'woocommerce' ) );

		if ( $heading ) :
			?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php woocommerce_product_loop_start(); ?>

		<?php foreach ( $cross_sells as $cross_sell ) : ?>

			<li <?php wc_product_class( '', $cross_sell ); ?>>

			<?php woocommerce_template_loop_product_link_open(); ?>

			<?php echo $cross_sell->get_image(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped within get_image() ?>

			<?php echo '<h2 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ) . '">' . esc_html( $cross_sell->get_name() ) . '</h2>'; ?>

			<?php if ( $price_html = $cross_sell->get_price_html() ) : // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Escaped within get_price_html() ?>
				<span class="price"><?php echo $price_html; ?></span>
			<?php endif; ?>

			<?php woocommerce_template_loop_product_link_close(); ?>

			</li>

		<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

	</div>
<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Not indented for readability
endif;

wp_reset_postdata();
