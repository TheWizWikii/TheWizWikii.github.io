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

	$inner_class   = apply_filters( 'et_builder_inner_content_class', array( 'et_builder_inner_content' ) );
	$inner_classes = implode( ' ', $inner_class );
	?>

	<script type="text/javascript">
		document.documentElement.className = 'js';
	</script>

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php if ( ! is_single() || ! have_posts() ) { ?>
		<article id="post-0">
			<div id="page-container-bfb" class="entry-content">
				<div id="et-boc" class="et-boc">
					<div class="et-l">
						<div class="<?php echo esc_attr( $inner_classes ); ?>">
							<div id="et-fb-app"></div>
						</div>
					</div>
				</div>
			</div>
		</article>
	<?php } ?>
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div id="page-container-bfb" class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
	<div class="bfb-template-footer" style="display: none;">
		<?php wp_footer(); ?>
	</div>
</body>
</html>
