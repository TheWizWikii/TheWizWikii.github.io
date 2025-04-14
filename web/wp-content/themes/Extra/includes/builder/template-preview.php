<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

et_core_security_check( 'edit_posts', 'et_pb_preview_nonce', '', '_GET' );

$container_style = isset( $_POST['is_fb_preview'] ) || isset( $_GET['item_id'] ) ? 'max-width: none; padding: 0;' : '';
$post_id         = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;

if ( ! current_user_can( 'edit_post', $post_id ) ) {
	$post_id = 0;
}

$post = get_post( $post_id );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>" />

		<?php
		/**
		 * Fires in the head, before {@see wp_head()} is called. This action can be used to
		 * insert elements into the beginning of the head before any styles are scripts.
		 *
		 * @since 1.0
		 */
		do_action( 'et_head_meta' );

		$template_directory_uri = get_template_directory_uri();
		?>

		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

		<script type="text/javascript">
			document.documentElement.className = 'js';
		</script>

		<?php wp_head(); ?>

		<style type="text/css">
			html {margin-top: 0 !important;}
			.entry-content {margin-top: -1.5em !important;}
		</style>
	</head>
	<body <?php body_class(); ?>>
		<div id="page-container">
			<div id="main-content">
				<div class="container" style="<?php echo esc_attr( $container_style ); ?>">
					<div id="<?php echo esc_attr( apply_filters( 'et_pb_preview_wrap_id', 'content' ) ); ?>">
					<div class="<?php echo esc_attr( apply_filters( 'et_pb_preview_wrap_class', 'entry-content post-content entry content' ) ); ?>">

					<?php
					if ( isset( $_GET['item_id'] ) ) {
						$item_id           = (int) $_GET['item_id'];
						$rendered_template = et_theme_builder_render_library_template_preview( $item_id );
						echo et_core_intentionally_unescaped( $rendered_template, 'html' );
					} elseif ( isset( $_POST['shortcode'] ) ) {
						if ( $post ) {
							// Setup postdata so post-dependent data like dynamic content
							// can be resolved.
							setup_postdata( $post );
						}

						// process content for builder plugin
						if ( et_is_builder_plugin_active() ) {
							$content = do_shortcode( wp_unslash( $_POST['shortcode'] ) );
							$content = str_replace( ']]>', ']]&gt;', $content );

							$content = et_builder_get_builder_content_opening_wrapper() . et_builder_get_layout_opening_wrapper() . $content . et_builder_get_layout_closing_wrapper() . et_builder_get_builder_content_closing_wrapper();
						} else {
							$content = apply_filters( 'the_content', wp_unslash( $_POST['shortcode'] ) );
							$content = str_replace( ']]>', ']]&gt;', $content );
						}

						if ( $post ) {
							wp_reset_postdata();
						}

						echo et_core_intentionally_unescaped( $content, 'html' );
					} else {
						printf( '<p class="et-pb-preview-loading"><span>%1$s</span></p>', esc_html__( 'Loading preview...', 'et_builder' ) );
					}
					?>

					</div> <!-- .entry-content.post-content.entry -->
					</div> <!-- #content -->
					<?php echo et_builder_disabled_link_modal(); ?>
				</div><!-- .container -->
			</div><!-- #main-content -->
		</div> <!-- #page-container -->
		<?php wp_footer(); ?>
	</body>
</html>
