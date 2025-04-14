<?php // phpcs:disable Squiz.Commenting.FileComment.Missing -- Not used in other templates.
if ( et_theme_builder_overrides_layout( ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE ) || et_theme_builder_overrides_layout( ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE ) ) {
	// Skip rendering anything as this partial is being buffered anyway.
	// In addition, avoids get_sidebar() issues since that uses
	// locate_template() with require_once.
	return;
}

$footer_above_ad = extra_display_ad( 'footer_above', false );

if ( ! empty( $footer_above_ad ) ) { ?>
<div class="container">
	<div class="et_pb_extra_row etad footer_above">
		<?php echo et_core_esc_previously( $footer_above_ad ); ?>
	</div>
</div>
<?php } ?>

	<footer id="footer" class="<?php extra_footer_classes(); ?>">
		<?php get_sidebar( 'footer' ); ?>
		<div id="footer-bottom">
			<div class="container">

				<!-- Footer Info -->
				<p id="footer-info"><?php printf( et_get_safe_localization( __( 'Designed by %1$s | Powered by %2$s', 'extra' ) ), '<a href="http://www.elegantthemes.com" title="Premium WordPress Themes">Elegant Themes</a>', '<a href="http://www.wordpress.org">WordPress</a>' ); ?></p>

				<!-- Footer Navigation -->
				<?php if ( has_nav_menu( 'footer-menu' ) || false !== et_get_option( 'show_footer_social_icons', true ) ) { ?>
				<div id="footer-nav">
					<?php
					if ( has_nav_menu( 'footer-menu' ) ) {
						wp_nav_menu( array(
							'theme_location' => 'footer-menu',
							'depth'          => '1',
							'menu_class'     => 'bottom-nav',
							'menu_id'        => 'footer-menu',
							'container'      => '',
							'fallback_cb'    => '',
						) );
					}

					$show_footer_social_icons = et_get_option( 'show_footer_social_icons', true );

					if ( false !== $show_footer_social_icons || is_customize_preview() ) {
					?>
						<ul class="et-extra-social-icons" style="<?php extra_visible_display_css( $show_footer_social_icons ); ?>">
						<?php $social_icons = extra_get_social_networks(); ?>
						<?php foreach ( $social_icons as $social_icon => $social_icon_title ) { ?>
							<?php $social_icon = esc_attr( $social_icon ); ?>
							<?php $social_icon_url = et_get_option( sprintf( '%s_url', $social_icon ), '' ); ?>
							<?php if ( ! empty( $social_icon_url ) ) { ?>
							<li class="et-extra-social-icon <?php echo $social_icon; ?>">
								<a href="<?php echo esc_url( $social_icon_url ); ?>" class="et-extra-icon et-extra-icon-background-none et-extra-icon-<?php echo $social_icon; ?>"></a>
							</li>
							<?php } ?>
						<?php } ?>
						</ul>
					<?php
					}
					?>
				</div> <!-- /#et-footer-nav -->
				<?php } ?>

			</div>
		</div>
	</footer>
	</div> <!-- #page-container -->

	<?php if ( 'on' == et_get_option( 'extra_back_to_top' ) ) { ?>
		<span title="<?php esc_attr_e( 'Back To Top', 'extra' ); ?>" id="back_to_top"></span>
	<?php } ?>

	<?php wp_footer(); ?>
</body>
</html>
