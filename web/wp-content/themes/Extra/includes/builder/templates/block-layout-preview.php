<!DOCTYPE html>
<html <?php language_attributes(); ?> style="margin-top: 0 !important;">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>"/>
	<?php
	show_admin_bar( false );

	/**
	 * Fires in the head, before {@see wp_head()} is called. This action can be used to
	 * insert elements into the beginning of the head before any styles or scripts.
	 *
	 * @since 1.0
	 */
	do_action( 'et_head_meta' );
	?>

	<script type="text/javascript">
		document.documentElement.className = 'js';
	</script>

	<style>
		.et-cloud-item-editor.et-bp-settings-hide-settings-modal .et-fb-modal {
			z-index: -1 !important;
		}
	</style>

	<?php wp_head(); ?>
</head>
<?php
	/**
	 * body's overflow:hidden is necessary to avoid unwanted scrollbar during layout preview loading
	 * Nothing else (inline style added by js, on-page css) is faster enough to prevent it.
	 */
?>
<body <?php body_class(); ?> style="overflow: hidden;">
<div id="page-container">
	<?php
	// Get TB template for current layout block preview page (basically FE of current page);
	// Use TB if there is applicable template or default layout if it doesn't exist
	$tb_template = ET_GB_Block_Layout::get_preview_tb_template();

	if ( $tb_template['has_layout'] ) {
		$layout_id      = $tb_template['layout_id'];
		$layout_enabled = $tb_template['layout_enabled'];
		$template_id    = $tb_template['template_id'];

		et_theme_builder_frontend_render_body( $layout_id, $layout_enabled, $template_id );
	} else {
		?>
			<div id="main-content">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
					<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="entry-content">
							<?php the_content(); ?>
						</div>
					</article>
				<?php endwhile; ?>
			</div><!-- #main-content -->
			<?php
	}
	?>
</div><!-- #page-container -->
<div id="block-layout-preview-footer">
	<?php wp_footer(); ?>
</div><!-- #block-layout-preview-footer -->
</body>
</html>
