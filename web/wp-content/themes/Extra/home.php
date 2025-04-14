<?php get_header(); ?>

<div id="main-content">
	<div class="container">
		<div id="content-area" class="<?php extra_sidebar_class(); ?> clearfix">
			<div class="et_pb_extra_column_main">
				<?php if ( et_extra_show_home_layout() ) { ?>

					<?php extra_home_layout(); ?>

				<?php } else { ?>
					<?php require locate_template( 'index-content.php' ); ?>
				<?php } ?>
			</div>

			<?php get_sidebar(); ?>

		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer();
