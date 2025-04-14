<?php
if ( !is_active_sidebar( 'sidebar-footer-1' ) && !is_active_sidebar( 'sidebar-footer-2' ) && !is_active_sidebar( 'sidebar-footer-3' ) && !is_active_sidebar( 'sidebar-footer-4' ) ) {
	return;
}
?>
<div class="container">
	<div class="et_pb_extra_row container-width-change-notify">
		<?php
		$footer_sidebars           = array(
			'sidebar-footer-1',
			'sidebar-footer-2',
			'sidebar-footer-3',
			'sidebar-footer-4',
		);
		$footer_column_index       = 0;
		$footer_columns_visibility = extra_footer_columns_visibility();

		foreach ($footer_sidebars as $footer_sidebar ) {
			$footer_column_index++;
			$footer_columns_visibility_index = $footer_column_index - 1;

			if ( is_active_sidebar( $footer_sidebar ) && ( is_customize_preview() || ! is_customize_preview() && $footer_columns_visibility[$footer_columns_visibility_index] ) ) {
				?>
				<div class="et_pb_extra_column <?php echo ( 0 === $footer_column_index % 2 ) ? 'even' : 'odd'; ?> <?php esc_attr_e( sprintf( 'column-%s', $footer_column_index ) ); ?>">
					<?php dynamic_sidebar( $footer_sidebar ); ?>
				</div>
				<?php
			}
		}
		?>
	</div>
</div>
