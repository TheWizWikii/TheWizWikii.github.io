<?php
if ( post_password_required() ) : ?>

<p class="no-comments"><?php esc_html_e( 'This post is password protected. Enter the password to view comments.', 'extra' ); ?></p>
<?php
	return;
endif;
?>
<!-- You can start editing here. -->

<section id="comment-wrap">
<?php if ( have_comments() && ! empty( $comments_by_type['comment'] ) ) : ?>
	<h2 id="comments" class="page_title"><?php comments_number( esc_html__( '0 Comments', 'extra' ), esc_html__( '1 Comment', 'extra' ), '% ' . esc_html__( 'Comments', 'extra' ) ); ?></h2>
<?php endif; ?>
	<?php if ( have_comments() ) : ?>
		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="comment_navigation_top clearfix">
				<div class="nav-previous"><?php previous_comments_link( et_get_safe_localization( __( '<span class="meta-nav">&larr;</span> Older Comments', 'extra' ) ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( et_get_safe_localization( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'extra' ) ) ); ?></div>
			</div> <!-- .navigation -->
		<?php endif; // check for comment navigation ?>

		<?php if ( ! empty( $comments_by_type['comment'] ) ) : ?>
			<ol class="commentlist clearfix">
				<?php wp_list_comments( array( 'type' => 'comment', 'callback' => 'et_custom_comments_display' ) ); ?>
			</ol>
		<?php endif; ?>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // Are there comments to navigate through? ?>
			<div class="comment_navigation_bottom clearfix">
				<div class="nav-previous"><?php previous_comments_link( et_get_safe_localization( __( '<span class="meta-nav">&larr;</span> Older Comments', 'extra' ) ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( et_get_safe_localization( __( 'Newer Comments <span class="meta-nav">&rarr;</span>', 'extra' ) ) ); ?></div>
			</div> <!-- .navigation -->
		<?php endif; // check for comment navigation ?>

		<?php if ( ! empty( $comments_by_type['pings'] ) ) : ?>
			<div id="trackbacks">
				<h3 id="trackbacks-title"><?php esc_html_e( 'Trackbacks/Pingbacks', 'extra' ); ?></h3>
				<ol class="pinglist">
					<?php wp_list_comments( 'type=pings&callback=et_list_pings' ); ?>
				</ol>
			</div>
		<?php endif; ?>
	<?php else : // this is displayed if there are no comments so far ?>
		<div id="comments" class="nocomments">
			<?php if ( 'open' == $post->comment_status ) : ?> <!-- If comments are open, but there are no comments. -->

			<?php else : // comments are closed ?> <!-- If comments are closed. -->

			<?php endif; ?>
		</div>
	<?php endif; ?>
	<?php if ( 'open' == $post->comment_status ) : ?>
		<?php comment_form( array('label_submit' => esc_attr__( 'Post Comment', 'extra' ), 'comment_field' => '<p class="comment-form-comment"><label for="comment">' . esc_html_x( 'COMMENT', 'noun', 'extra' ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea></p>', 'title_reply' => '<span>' . esc_attr__( 'Leave a reply', 'extra' ) . '</span>', 'title_reply_to' => esc_attr__( 'Leave a Reply to %s', 'extra' ) ) ); ?>
	<?php else: ?>

	<?php endif; ?>
</section>
