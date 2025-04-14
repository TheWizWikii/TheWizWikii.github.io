<?php
$layouts = et_theme_builder_get_template_layouts();
?>
	<?php
	et_theme_builder_frontend_render_footer(
		$layouts[ ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE ]['id'],
		$layouts[ ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE ]['enabled'],
		$layouts[ ET_THEME_BUILDER_TEMPLATE_POST_TYPE ]
	);
	?>

	<?php if ( et_core_is_fb_enabled() && et_theme_builder_is_layout_post_type( get_post_type() ) ) : ?>
		<?php // Hide the footer when we are editing a TB layout. ?>
		<div class="et-tb-fb-footer" style="display: none;">
			<?php wp_footer(); ?>
		</div>
	<?php else : ?>
		<?php wp_footer(); ?>
	<?php endif; ?>

	<?php if ( ! et_is_builder_plugin_active() && 'on' === et_get_option( 'divi_back_to_top', 'false' ) ) : ?>
		<span class="et_pb_scroll_top et-pb-icon"></span>
	<?php endif; ?>
</body>
</html>
