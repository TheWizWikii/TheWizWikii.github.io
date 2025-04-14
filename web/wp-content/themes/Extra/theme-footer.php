<?php
/**
 * Template partial used to add content to the page in Theme Builder.
 * Duplicates partial content from footer.php in order to maintain
 * backwards compatibility with child themes.
 *
 * @package Extra
 */

?>

<?php $footer_above_ad = extra_display_ad( 'footer_above', false ); ?>
<?php if ( ! empty( $footer_above_ad ) ) : ?>
	<div class="container">
		<div class="et_pb_extra_row etad footer_above">
			<?php echo et_core_esc_previously( $footer_above_ad, 'html' ); ?>
		</div>
	</div>
<?php endif; ?>

<footer id="footer" class="<?php extra_footer_classes(); ?>">
	<?php get_sidebar( 'footer' ); ?>

	<div id="footer-bottom">
		<div class="container">
			<!-- Footer Info -->
			<p id="footer-info"><?php printf( et_get_safe_localization( __( 'Designed by %1$s | Powered by %2$s', 'extra' ) ), '<a href="http://www.elegantthemes.com" title="Premium WordPress Themes">Elegant Themes</a>', '<a href="http://www.wordpress.org">WordPress</a>' ); ?></p>

			<!-- Footer Navigation -->
			<?php if ( has_nav_menu( 'footer-menu' ) || false !== et_get_option( 'show_footer_social_icons', true ) ) : ?>
				<div id="footer-nav">
					<?php
					if ( has_nav_menu( 'footer-menu' ) ) {
						wp_nav_menu(
							array(
								'theme_location' => 'footer-menu',
								'depth'          => '1',
								'menu_class'     => 'bottom-nav',
								'menu_id'        => 'footer-menu',
								'container'      => '',
								'fallback_cb'    => '',
							)
						);
					}

					$show_footer_social_icons = et_get_option( 'show_footer_social_icons', true );

					if ( false !== $show_footer_social_icons || is_customize_preview() ) {
						?>
						<ul class="et-extra-social-icons" style="<?php extra_visible_display_css( $show_footer_social_icons ); ?>">
							<?php $social_icons = extra_get_social_networks(); ?>
							<?php foreach ( $social_icons as $social_icon => $social_icon_title ) { ?>
								<?php $social_icon = $social_icon; ?>
								<?php $social_icon_url = et_get_option( sprintf( '%s_url', $social_icon ), '' ); ?>
								<?php if ( ! empty( $social_icon_url ) ) { ?>
									<li class="et-extra-social-icon <?php echo esc_attr( $social_icon ); ?>">
										<a href="<?php echo esc_url( $social_icon_url ); ?>" class="et-extra-icon et-extra-icon-background-none et-extra-icon-<?php echo esc_attr( $social_icon ); ?>"></a>
									</li>
								<?php } ?>
							<?php } ?>
						</ul>
						<?php
					}
					?>
				</div> <!-- /#et-footer-nav -->
			<?php endif; ?>
		</div>
	</div>
</footer>
