<?php
/**
 * Modified block template canvas file to render Visual and Theme Builder layouts.
 *
 * @since 4.9.8
 * @since 4.14.7 Remove block template HTML to only display TB Template.
 *
 * This block template canvas should be used only when TB Template (Header/Footer/Body)
 * is active on current page. Otherwise, we have to use default Block Editor template.
 *
 * @see {ET_Builder_Block_Templates::get_custom_query_template}
 *
 * @package Divi
 */

get_header();

if ( is_singular() && have_posts() ) {
	// If current page is singular, render the content normally.
	// Template type: frontpage, home (page), page, paged, privacypolicy, single, singular.
	while ( have_posts() ) {
		the_post();
		the_content();
	}
} else {
	/**
	 * Fires the main content on block template canvas.
	 *
	 * Use this hook to display custom output for non singular page.
	 *
	 * @since 4.14.7
	 *
	 * Template type: 404, archive, author, category, date, home (non-page), index, search, tag, taxonomy.
	 */
	do_action( 'et_block_template_canvas_main_content' );
}

get_footer();
