<?php
/*
Template Name: Portfolio
*/

get_header(); ?>
<div id="main-content">
	<div class="container">
		<div id="content-area" class="<?php extra_sidebar_class(); ?> clearfix">
	<?php
	if ( have_posts() ) :
		while ( have_posts() ) : the_post(); ?>
			<div class="et_pb_extra_column_main">
				<?php $portfolio_options = extra_get_portfolio_options(); ?>
				<div class="et_pb_extra_row container-width-change-notify">
					<div class="et_pb_extra_column et_pb_extra_column_4_4">
						<?php if ( ! $portfolio_options['hide_title'] ) { ?>
							<h1 class="page-title"><?php the_title(); ?></h1>
						<?php } ?>
					</div>
				</div>
				<div class="et_filterable_portfolio">
					<ul id="portfolio_filter" class="filterable_portfolio_filter" >
						<li class="filter-toggle">Filter Projects</li>
						<li><a href="#all" title="<?php esc_attr_e( 'All', 'extra' ); ?>"><?php esc_html_e( 'All', 'extra' ); ?></a></li>
						<?php foreach ( $portfolio_options['project_categories'] as $category ) { ?>
							<?php
							printf( '<li><a href="#%1$s" title="%2$s" rel="%1$s">%3$s</a></li>',
								esc_attr( $category->slug ),
								esc_attr( $category->name ),
								esc_html( $category->name )
							);
							?>
						<?php } ?>
					</ul>
					<div id="portfolio_list" class="filterable_portfolio_list">
					<?php $projects = extra_get_portfolio_projects(); ?>

					<?php
					if ( $projects->have_posts() ) :
						while ( $projects->have_posts() ) : $projects->the_post(); ?>

						<?php $category_classes = extra_get_portfolio_project_category_classes(); ?>
						<a href="<?php the_permalink(); ?>" id="project-<?php the_ID(); ?>" <?php post_class( $category_classes ); ?>>
							<span class="project-content">
								<?php
								$thumb = et_extra_get_post_thumb( array(
									'size'         => 'extra-image-square-medium',
									'link_wrapped' => false,
									'img_after'    => '<span class="et_pb_extra_overlay"></span>',
								) );
								?>
								<?php if ( $thumb ) { ?>
								<span class="thumbnail featured-image">
								<?php echo $thumb; ?>
								</span>
								<?php } ?>

							<?php if ( ! ( $portfolio_options['hide_title'] && $portfolio_options['hide_categories'] ) ) { ?>
								<span class="content">
									<?php if ( ! $portfolio_options['hide_title'] ) { ?>
									<h3 class="entry-title"><?php the_title(); ?></h3>
									<?php } ?>
									<?php if ( ! $portfolio_options['hide_categories'] ) { ?>
									<p><?php extra_the_project_categories(); ?></p>
									<?php } ?>
								</span>
							<?php } ?>
							</span><!-- #project-{ID} -->
						</a>
					<?php
						endwhile;
					endif;
					wp_reset_postdata();
					?>
					</div>
				</div>
			</div><!-- /.et_pb_extra_column.et_pb_extra_column_main -->

			<?php get_sidebar(); ?>

	<?php
		endwhile;
	else :
	?>
		<h2><?php esc_html_e( 'Post not found', 'extra' ); ?></h2>
	<?php
	endif;
	?>
		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer();
