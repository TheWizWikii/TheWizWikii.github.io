<?php
$layouts = et_theme_builder_get_template_layouts();
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<?php echo $tb_theme_head; ?>

	<?php do_action( 'et_theme_builder_template_head' ); ?>

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<?php

	wp_body_open();

	et_theme_builder_frontend_render_header(
		$layouts[ ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE ]['id'],
		$layouts[ ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE ]['enabled'],
		$layouts[ ET_THEME_BUILDER_TEMPLATE_POST_TYPE ]
	);
	?>
