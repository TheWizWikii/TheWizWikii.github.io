<?php
/*
Template Name: Contact
*/

get_header(); ?>
<div id="main-content">
	<div class="container">
		<div id="content-area" class="<?php extra_sidebar_class(); ?> clearfix">
			<div class="et_pb_extra_column_main">
				<?php
				if ( have_posts() ) :
					while ( have_posts() ) : the_post(); ?>
				<div class="et_extra_other_module contact-box">
					<div class="contact-content">
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

						<?php $contact_page_options = extra_get_contact_page_options(); ?>

						<?php if ( !empty( $contact_page_options['map_lat'] ) && !empty( $contact_page_options['map_lng'] ) ) { ?>
						<div class="contact-map" data-zoom="<?php echo esc_attr( $contact_page_options['map_zoom'] ); ?>" data-lat="<?php echo esc_attr( $contact_page_options['map_lat'] ); ?>" data-lng="<?php echo esc_attr( $contact_page_options['map_lng'] ); ?>"></div>
						<?php } ?>

						<form class="contact-form extra-contact-form" method="post">

							<h3><?php echo esc_html( $contact_page_options['title'] ); ?></h3>

							<div class="message"><?php
							$message = extra_contact_form_submit();
							if ( !empty( $message ) ) {
								printf('
									<p class="%s">%s</p>',
									esc_attr( $message['type'] ),
									esc_html( $message['message'] )
								);
							}
							?></div>

							<div class="field first">
								<input id="contact_name" name="contact_name" type="text" placeholder="<?php esc_attr_e( 'Name', 'extra' ); ?> *" data-label="<?php esc_attr_e( 'Name', 'extra' ); ?>" />
							</div>

							<div class="field">
								<input id="contact_email" name="contact_email" type="email" placeholder="<?php esc_attr_e( 'Email', 'extra' ); ?> *" />
							</div>

							<div class="field last">
								<input id="contact_subject" name="contact_subject" type="text" placeholder="<?php esc_attr_e( 'Subject', 'extra' ); ?>" />
							</div>

							<textarea id="contact_message" name="contact_message" placeholder="<?php esc_attr_e( 'Message', 'extra' ); ?>"></textarea>

							<button type="submit" name="et_extra_submit_button" class="submit"><?php esc_html_e( 'Send', 'extra' ); ?><span class="loader"><?php extra_ajax_loader_img(); ?></span></button>

							<input type="hidden" id="action" name="action" value="extra_contact_form_submit" />
							<?php wp_nonce_field( 'extra-contact-form', 'nonce_extra_contact_form' ); ?>

						</form><!-- /.contact-form -->
					</div><!-- /.contact-content -->
				</div><!-- /contact-box -->
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
