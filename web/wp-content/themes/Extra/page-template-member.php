<?php
/*
Template Name: Member Login
*/

get_header(); ?>
<div id="main-content">
	<div class="container">
		<div id="content-area" class="<?php extra_sidebar_class(); ?> clearfix">
			<div class="et_pb_extra_column_main">
					<?php
					if ( have_posts() ) :
						while ( have_posts() ) : the_post(); ?>
					<div class="et_extra_other_module member-box">
						<div class="member-content">
							<h2><?php the_title(); ?></h2>

							<?php the_content(); ?>
							<?php
							if ( ! extra_is_builder_built() ) {
								wp_link_pages( array(
									'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'extra' ),
									'after'  => '</div>',
								) );
							}
							?>

							<?php if ( !is_user_logged_in() ) { ?>
							<form name="loginform" id="loginform" action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">
								<input class="input" type="text" name="log" placeholder="<?php esc_attr_e( 'USERNAME', 'extra' ); ?>">
								<input class="input" type="password" name="pwd" placeholder="<?php esc_attr_e( 'PASSWORD', 'extra' ); ?>">
								<button type="submit" name="et_extra_submit_button"  class="button"><?php esc_html_e( 'Login', 'extra' ); ?></button>
							</form>
							<?php } else { ?>
							<h3><?php esc_html_e( 'You are already logged in.', 'extra' );?></h3>
							<a class="button" href="<?php echo wp_logout_url(); ?>" title="<?php esc_attr_e( 'Logout', 'extra' ); ?>"><?php esc_html_e( 'Logout', 'extra' ); ?></a>
							<?php } ?>
						</div><!-- /.member-content -->
					</div><!-- /.member-box -->
					<?php
						endwhile;
					else :
					?>
					<h2><?php esc_html_e( 'Post not found', 'extra' ); ?></h2>
					<?php
					endif;
					wp_reset_query();
					?>
				</div>
				<!-- /.et_pb_extra_column_main -->

			<?php get_sidebar(); ?>

		</div> <!-- /#content-area -->
	</div> <!-- /.container -->
</div> <!-- /#main-content -->

<?php get_footer();
