<?php
/*
Template Name: Authors
*/

get_header(); ?>
<div id="main-content" class="authors-page">
	<div class="container">
		<div id="content-area" class="<?php extra_sidebar_class(); ?> clearfix">
			<div class="et_pb_extra_column_main">
				<div class="et_pb_extra_row">
					<div class="et_pb_extra_column et_pb_extra_column_4_4">
					<?php
					if ( have_posts() ) :
						while ( have_posts() ) : the_post(); ?>
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'module' ); ?>>
							<div class="post-wrap">
								<h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
								<div class="post-content">
									<?php the_content(); ?>
									<?php
									if ( ! extra_is_builder_built() ) {
										wp_link_pages( array(
											'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'extra' ),
											'after'  => '</div>',
										) );
									}
									?>
								</div>
							</div><!-- /.post-wrap -->
						</article>
					</div> <!-- /.et_pb_extra_column_1_1 -->
				</div><!-- /.et_pb_extra_row -->

				<?php $authors_page_vars = extra_get_authors_page_options(); ?>

				<div class="authors" data-columns>
					<?php foreach ( $authors_page_vars['authors'] as $author ) { ?>
						<?php $user_color = !empty( $author->user_color ) ? $author->user_color : extra_global_accent_color(); ?>
							<div class="author" style="border-top-color:<?php echo esc_attr( $user_color ); ?>">
								<style type="text/css">
									.et-extra-icon-author-<?php echo esc_attr( $author->ID ); ?>-color-hover:hover::before {
										color: <?php echo esc_attr( $user_color ); ?> !important;
									}
								</style>
								<div class="author-content">
									<a href="<?php echo esc_url( get_author_posts_url( $author->ID ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'Posts By: %s', 'extra' ), $author->display_name ) ); ?>" rel="author">
										<?php echo get_avatar( $author->ID, 150, 'mystery', esc_attr( $author->display_name ) ); ?>
									</a>
									<h2 style="color:<?php echo esc_attr( $user_color ); ?>"><?php echo esc_html( $author->display_name ); ?></h2>
									<p class="description"><?php echo esc_html( $author->description ); ?></p>
									<?php $count = count_user_posts( $author->ID ); ?>
									<a href="<?php echo esc_url( get_author_posts_url( $author->ID ) ); ?>" class="button post-count" title="<?php printf( esc_attr__( 'Posts By: %s', 'extra' ), $author->user_nicename ); ?>" rel="author"><?php esc_html_e( sprintf( _n( '%d Post', '%d Posts', $count ), $count ) ); ?></a>
								</div> <!-- /.author-content -->

								<?php
								$social_links = '';
								foreach ( extra_get_social_networks() as $network => $title ) {
									$network = esc_attr( $network );
									if ( !empty( $author->$network ) ) {
										$url = extra_format_url( $author->$network );
										$social_links .= sprintf( '<a href="%s" target="_blank"><i class="et-extra-icon et-extra-icon-%s et-extra-icon-author-%s-color-hover"></i></a>',
											esc_url( $url ),
											esc_attr( $network ),
											esc_attr( $author->ID )
										);
									}
								}
								if ( !empty( $social_links ) ) {
									echo '<div class="author-footer">' . $social_links . '</div>';
								}
								?>
							</div> <!-- /.author -->
					<?php } ?>
				</div>

					<?php
						endwhile;
					else :
					?>
					<h2><?php esc_html_e( 'Post not found', 'extra' ); ?></h2>
					<?php
					endif;
					?>

				<?php
				if ( comments_open() || get_comments_number() ) {
					comments_template( '', true );
				}
				?>
			</div><!-- /.et_pb_extra_column.et_pb_extra_column_main -->

			<?php get_sidebar(); ?>

		</div> <!-- #content-area -->
	</div> <!-- .container -->
</div> <!-- #main-content -->

<?php get_footer();
