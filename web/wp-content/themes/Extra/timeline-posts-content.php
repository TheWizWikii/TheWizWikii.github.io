<?php
global $wp_locale;

$current_month = '';
$current_year = '';
$current_month_number = '';
?>
<?php
if ( $timeline_posts->have_posts() ) :
	while ( $timeline_posts->have_posts() ) : $timeline_posts->the_post(); ?>
	<?php
	$post_month = strtolower( get_the_time( 'F' ) );
	$post_year = strtolower( get_the_time( 'Y' ) );
	$post_month_number = get_the_time( 'm' );
	if ( $post_month != $current_month || $post_year != $current_year ) {

	?>
	<?php if ( !empty( $current_month ) ) { //need to close up existing month grouping ?>
		</ul><!-- /.posts-list -->
	</div><!-- /.timeline-module -->
	<?php } ?>
	<?php
	$current_month = $post_month;
	$current_month_number = $post_month_number;
	$current_year = $post_year;

	// start new month grouping
	printf(
		'<div class="timeline-module et_extra_other_module year-%1$d" data-year="%1$d" data-month="%2$s" id="%2$s_%1$d" style="border-top-color:%4$s">
		<div class="module-head">
			<h1 class="module-title-month">%3$s</h1>
			<span class="module-filter module-title-year">%1$d</span>
		</div>
		<ul class="posts-list">',
		esc_attr( $current_year ),
		esc_attr( $current_month ),
		esc_html( $wp_locale->get_month( $current_month_number ) ),
		esc_attr( extra_global_accent_color() )
	);

	} // end conditional group header logic

	// begin post creation
	?>
	<li>
		<article class="post">
			<?php
			$thumb = et_extra_get_post_thumb(array(
				'size'    => 'extra-image-square-small',
				'a_class' => array( 'post-thumbnail' ),
			));

			if ( $thumb ) {
				echo $thumb;
			}
			?>
			<div class="post-content">
				<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<div class="post-meta">
					<?php extra_the_post_date();?> | <?php extra_the_post_categories(); ?> | <?php extra_the_post_rating_stars(); ?>
				</div>
			</div>
		</article>
	</li>
<?php
	endwhile;
	wp_reset_postdata();

	// close last group
	?>
	</ul><!-- /.posts-list -->
</div><!-- /.timeline-module -->
<?php
endif;
