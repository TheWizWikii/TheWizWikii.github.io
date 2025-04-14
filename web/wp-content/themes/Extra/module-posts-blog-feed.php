<?php
$data_atts = $this->props_to_html_data_attrs(array(
	'show_featured_image',
	'show_author',
	'show_categories',
	'show_date',
	'show_rating',
	'show_more',
	'show_comments',
	'date_format',
	'posts_per_page',
	'order',
	'orderby',
	'category_id',
	'content_length',
	'blog_feed_module_type',
	'hover_overlay_icon',
	'use_tax_query'
));

if ( 'standard' == $blog_feed_module_type && false === strpos( $category_id, ',' ) ) {
	$color = extra_get_category_color( $category_id );
	$color_style = esc_attr( sprintf( 'border-color:%s;', $color ) );
} else {
	$color_style = '';
}
?>

<?php $id_attr = '' !== $module_id ? sprintf( ' id="%1$s"', esc_attr( $module_id ) ) : ''; ?>
<div <?php echo $id_attr ?> class="posts-blog-feed-module post-module et_pb_extra_module <?php echo esc_attr( $blog_feed_module_type ); ?> <?php echo esc_attr( $module_class ); ?> paginated et_pb_extra_module" style="<?php echo esc_attr( $color_style ); ?>" data-current_page="1" data-et_column_type="<?php echo esc_attr( $_et_column_type ); ?>" <?php echo $data_atts; ?>>
<?php if ( !empty( $feed_title ) ) { ?>
	<div class="module-head">
		<h1 class="feed-title"><?php echo esc_html( $feed_title ); ?></h1>
	</div>
<?php } ?>

<?php if ( $module_posts->have_posts() ) : ?>
<div class="paginated_content">
	<?php require locate_template( 'module-posts-blog-feed-loop.php' ); ?>
</div><!-- /.paginated_content -->

<span class="loader"><?php extra_ajax_loader_img(); ?></span>

<?php if ( $module_posts->max_num_pages > 1 && $show_pagination ) { ?>
	<ul class="pagination">
		<li class="prev arrow"><a class="prev arrow" href="#"></a></li>
	<?php for ( $x = 1; $x <= $module_posts->max_num_pages; $x++ ) { ?>
		<?php if ( $x == $module_posts->max_num_pages ) { ?>
			<li class="ellipsis back"><a class="ellipsis" href="#">...</a></li>
		<?php } ?>

		<?php $last_class = $x == $module_posts->max_num_pages ? ' last' : ''; ?>
		<li class="<?php echo esc_attr( $last_class ); ?>"><a href="#" class="pagination-page pagination-page-<?php echo esc_attr( $x ); ?>" data-page="<?php echo $x; ?>"><?php echo $x; ?></a></li>
		<?php if ( $x == 1 ) { ?>
			<li class="ellipsis front"><a class="ellipsis" href="#">...</a></li>
		<?php } ?>
	<?php } ?>
		<li class="next arrow"><a class="next arrow" href="#"></a></li>
	</ul>
<?php } ?>
<?php else : ?>
	<article class='nopost'>
		<h5><?php esc_html_e( 'Sorry, No Posts Found', 'extra' ); ?></h5>
	</article>
<?php endif; ?>
</div><!-- /.posts-blog-feed-module -->
