<?php
/*
Template Name: Timeline
*/

get_header(); ?>
<div id="main-content">
	<div class="container">
		<div id="content-area" class="<?php extra_sidebar_class(); ?> clearfix">
			<div class="et_pb_extra_column_main">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) : the_post(); ?>
				<div class="et_pb_extra_column et_pb_extra_column_4_4">
					<div class="timeline-container">
						<div id="timeline-sticky-header" class="et_extra_other_module timeline-module">
							<div class="module-head">
								<h1 class="module-title-month"></h1>
								<span class="module-filter module-title-year"></span>
							</div>
						</div>
						<div id="timeline" class="timeline mobile">
							<?php $timeline_posts = extra_get_timeline_posts_onload(); ?>
							<?php require locate_template( 'timeline-posts-content.php' ); ?>
							<div class="loader">
								<?php extra_ajax_loader_img(); ?>
							</div>
						</div>
						<div class="timeline-nav">
							<ul id="timeline-menu" class="timeline-menu">
								<?php
								$month_groups = extra_get_timeline_menu_month_groups();
								$current_year = '';

								foreach ( $month_groups as $month_group ) {

									list( $month, $year ) = explode( '-', $month_group );

									if ( $year != $current_year ) {
										printf( '<li class="year year-%1$d"><a href="#%1$d" data-year="%1$d">%1$d</a></li>', esc_attr( $year ) );
										$current_year = $year;
									}

									printf( '<li class="month year-%1$d"><a href="#%2$s_%1$d" data-month="%2$s" data-year="%1$d">%3$s</a></li>', esc_attr( $year ), esc_attr( strtolower( $month ) ), esc_html( $month ) );

									?>
									<?php
								}
								?>
							</ul>
						</div>
					</div><!-- /.timeline-container -->
				</div><!-- /.et_pb_extra_column_main -->

			<?php
				endwhile;
			else :
			?>
				<h2><?php esc_html_e( 'Post not found', 'extra' ); ?></h2>
			<?php
			endif;
			wp_reset_query();
			?>
			</div><!-- /.et_pb_extra_column_main -->

			<?php get_sidebar(); ?>

		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer();
