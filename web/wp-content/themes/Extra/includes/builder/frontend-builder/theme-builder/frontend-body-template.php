<?php
$layouts = et_theme_builder_get_template_layouts();
?>
<?php get_header(); ?>

<?php
et_theme_builder_frontend_render_body(
	$layouts[ ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE ]['id'],
	$layouts[ ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE ]['enabled'],
	$layouts[ ET_THEME_BUILDER_TEMPLATE_POST_TYPE ]
);
?>

<?php
get_footer();
