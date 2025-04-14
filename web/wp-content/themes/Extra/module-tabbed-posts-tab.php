<?php if ( empty( $module_posts ) ) return; ?>
<div class="tab-content tab-content-<?php echo esc_attr( $tab_id ); ?> <?php esc_attr_e( $module_class ); ?>">
	<?php require locate_template( 'module-posts-content.php' ); ?>
</div>
