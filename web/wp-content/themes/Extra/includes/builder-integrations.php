<?php

define( 'ET_BUILDER_THEME', true );

function extra_setup_builder() {
	define( 'ET_BUILDER_DIR', dirname( __FILE__ ) . '/builder/' );
	define( 'ET_BUILDER_URI', get_template_directory_uri() . '/includes/builder' );
	define( 'ET_BUILDER_LAYOUT_POST_TYPE', 'et_pb_layout' );
	define( 'ET_BUILDER_VERSION', et_get_theme_version() );

	load_theme_textdomain( 'et_builder', ET_BUILDER_DIR . 'languages' );
	require ET_BUILDER_DIR . 'framework.php';

	et_pb_register_posttypes();
	extra_setup_project_tag_tax();

	add_action( 'et_builder_ready', 'extra_load_layout_builder_modules' );
}

add_action( 'init', 'extra_setup_builder', 0 );

function extra_setup_project_tag_tax() {
	$labels = array(
		'name'               => esc_html_x( 'Projects', 'project type general name', 'extra' ),
		'singular_name'      => esc_html_x( 'Project', 'project type singular name', 'extra' ),
		'add_new'            => esc_html_x( 'Add New', 'project item', 'extra' ),
		'add_new_item'       => esc_html__( 'Add New Project', 'extra' ),
		'edit_item'          => esc_html__( 'Edit Project', 'extra' ),
		'new_item'           => esc_html__( 'New Project', 'extra' ),
		'all_items'          => esc_html__( 'All Projects', 'extra' ),
		'view_item'          => esc_html__( 'View Project', 'extra' ),
		'search_items'       => esc_html__( 'Search Projects', 'extra' ),
		'not_found'          => esc_html__( 'Nothing found', 'extra' ),
		'not_found_in_trash' => esc_html__( 'Nothing found in Trash', 'extra' ),
		'parent_item_colon'  => '',
	);

	$labels = array(
		'name'                       => esc_html_x( 'Project Skills', 'Project Skill name', 'extra' ),
		'singular_name'              => esc_html_x( 'Project Skill', 'Project skill singular name', 'extra' ),
		'search_items'               => esc_html__( 'Search Project Skills', 'extra' ),
		'all_items'                  => esc_html__( 'All Project Skills', 'extra' ),
		'parent_item'                => esc_html__( 'Parent Skill', 'extra' ),
		'parent_item_colon'          => esc_html__( 'Parent Skill:', 'extra' ),
		'edit_item'                  => esc_html__( 'Edit Skill', 'extra' ),
		'update_item'                => esc_html__( 'Update Skill', 'extra' ),
		'add_new_item'               => esc_html__( 'Add New Project Skill', 'extra' ),
		'new_item_name'              => esc_html__( 'New Skill Name', 'extra' ),
		'menu_name'                  => esc_html__( 'Project Skills', 'extra' ),
		'popular_items'              => esc_html__( 'Popular Skills', 'extra' ),
		'separate_items_with_commas' => esc_html__( 'Separate Skills with Commas', 'extra' ),
		'add_or_remove_items'        => esc_html__( 'Add or Remove Skills', 'extra' ),
		'choose_from_most_used'      => esc_html__( 'Choose From the Most Used Skills', 'extra' ),
		'not_found'                  => esc_html__( 'No Skills Found', 'extra' ),
	);

	register_taxonomy( EXTRA_PROJECT_TAG_TAX, array( EXTRA_PROJECT_POST_TYPE ), array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
	) );
}

function extra_load_layout_builder_modules() {
	require dirname( __FILE__ ) . '/modules.php'; // EXTRA_LAYOUT_POST_TYPE modules
}

function extra_et_pb_load_roles_admin_hook() {
	return 'extra_page_et_extra_role_editor';
}

add_filter( 'et_pb_load_roles_admin_hook', 'extra_et_pb_load_roles_admin_hook' );

function extra_et_divi_role_editor_page() {
	return 'et_extra_role_editor';
}

add_filter( 'et_divi_role_editor_page', 'extra_et_divi_role_editor_page' );

function extra_layout_remove_add_fullwidth_section_button( $button_html ) {
	global $typenow;

	if ( EXTRA_LAYOUT_POST_TYPE == $typenow ) {
		return '';
	}

	// do not display the Fullwidth Section option on library pages when editing the Extra Layout
	if ( ET_BUILDER_LAYOUT_POST_TYPE === $typenow && EXTRA_LAYOUT_POST_TYPE === get_post_meta( get_the_ID(), '_et_pb_built_for_post_type', true ) ) {
		return '';
	}

	return $button_html;
}

add_filter( 'et_builder_add_fullwidth_section_button', 'extra_layout_remove_add_fullwidth_section_button' );

function et_pb_extra_built_for_post_type_display( $post_type ) {
	if ( $post_type == EXTRA_LAYOUT_POST_TYPE ) {
		return esc_html__( 'Extra - Layout', 'extra' );
	}

	return $post_type;
}

add_filter( 'et_pb_layout_built_for_post_type_column', 'et_pb_extra_built_for_post_type_display' );
add_filter( 'et_pb_built_for_post_type_display', 'et_pb_extra_built_for_post_type_display' );

function extra_et_builder_module_post_types( $post_types, $slug, $this_post_types = array() ) {
	$allowed_layout_modules = apply_filters( 'extra_allowed_layout_modules', array(
		'et_pb_section',
		'et_pb_row',
		'et_pb_row_inner',
		'et_pb_column',
		'et_pb_image',
		'et_pb_text',
		'et_pb_code',
	) );

	// This module has been intentionally set to be/include EXTRA_LAYOUT_POST_TYPE, so it won't be altered
	if ( in_array( EXTRA_LAYOUT_POST_TYPE, $this_post_types ) ) {
		return $post_types;
	}

	// unset EXTRA_LAYOUT_POST_TYPE from disallowed default modules
	if ( !in_array( $slug, $allowed_layout_modules ) ) {
		$_post_types = array_flip( $post_types );

		unset( $_post_types[ EXTRA_LAYOUT_POST_TYPE ] );

		$post_types = array_flip( $_post_types );
	}

	return $post_types;
}

add_filter( 'et_builder_module_post_types', 'extra_et_builder_module_post_types', 10, 3 );

define( 'ET_EXTRA_PREDEFINED_LAYOUTS_VERSION', 1 );

function et_pb_extra_update_predefined_layouts() {
	// don't do anything if layouts have been updated
	if ( 'on' === get_theme_mod( 'et_pb_extra_predefined_layouts_version_' . ET_EXTRA_PREDEFINED_LAYOUTS_VERSION ) ) {
		return;
	}

	// set the show on front option to layout when Extra theme activated
	update_option( 'show_on_front', 'layout' );

	// Extra Builder predefined layouts
	// delete default layouts
	et_pb_extra_delete_predefined_layouts( EXTRA_LAYOUT_POST_TYPE );

	// add predefined layouts
	et_pb_extra_add_predefined_layouts();

	// add default layouts
	et_pb_extra_add_default_layouts();

	set_theme_mod( 'et_pb_extra_predefined_layouts_version_' . ET_EXTRA_PREDEFINED_LAYOUTS_VERSION, 'on' );
}

add_action( 'admin_init', 'et_pb_extra_update_predefined_layouts' );

function et_extra_install_premade_layouts_upon_activation() {
	et_extra_stash_extra_layouts( false );
	// need to remove this flag to force re-adding them, as they could have been deleted by legacy divi
	remove_theme_mod( 'et_pb_extra_predefined_layouts_version_' . ET_EXTRA_PREDEFINED_LAYOUTS_VERSION );
}

add_action( 'after_switch_theme', 'et_extra_install_premade_layouts_upon_activation' );

function et_extra_stash_extra_layouts_upon_deactivation() {
	et_extra_stash_extra_layouts( true );
}

add_action( 'switch_theme', 'et_extra_stash_extra_layouts_upon_deactivation' );

function et_extra_stash_extra_layouts( $stash = true ) {
	global $wpdb;

	$args = array(
		'posts_per_page' => -1,
		'fields'         => 'ids',
		'meta_query'     => array(
			array(
				'key'     => '_et_pb_built_for_post_type',
				'value'   => EXTRA_LAYOUT_POST_TYPE,
				'compare' => 'IN',
			),
		),
	);

	$stashed_post_type = ET_BUILDER_LAYOUT_POST_TYPE . '_stashed';
	if ( $stash ) {
		$from_post_type = ET_BUILDER_LAYOUT_POST_TYPE;
		$to_post_type = $stashed_post_type;
	} else {
		$from_post_type = $stashed_post_type;
		$to_post_type = ET_BUILDER_LAYOUT_POST_TYPE;
	}

	$args['post_type'] = $from_post_type;

	$query = new WP_Query( $args );

	if ( empty( $query->posts ) ) {
		return;
	}

	$post_ids = array_map( 'absint', $query->posts );
	$post_ids = implode( ', ', $post_ids );

	$sql = $wpdb->prepare(
		"
			UPDATE $wpdb->posts
			SET post_type = %s
			WHERE ID IN( " . $post_ids . " )
		",
		$to_post_type
	);

	$wpdb->query( $sql );

	if ( $stash ) {
		set_theme_mod( 'et_extra_stashed_extra_layouts', 'on' );
	} else {
		remove_theme_mod( 'et_extra_stashed_extra_layouts' );
	}
}

if ( ! function_exists( 'et_pb_extra_add_predefined_layouts' ) ) :

	function et_pb_extra_add_predefined_layouts() {
		$et_builder_layouts = et_pb_extra_get_predefined_layouts();

		$meta = array(
			'_et_pb_predefined_layout'   => 'on',
			'_et_pb_built_for_post_type' => EXTRA_LAYOUT_POST_TYPE,
		);

		if ( isset( $et_builder_layouts ) && is_array( $et_builder_layouts ) ) {
			foreach ( $et_builder_layouts as $et_builder_layout ) {
				et_pb_create_layout( $et_builder_layout['name'], $et_builder_layout['content'], $meta );
			}
		}

		set_theme_mod( 'et_pb_extra_predefined_layouts_added', 'on' );
	}

endif;

if ( ! function_exists( 'et_pb_extra_add_default_layouts' ) ) :

	function et_pb_extra_add_default_layouts() {
		$et_builder_layouts = et_pb_extra_get_default_layouts();

		$is_home_layout_exists = false;
		$is_index_layout_exists = false;

		$layout_args = array(
			'posts_per_page' => -1,
			'nopaging'       => true,
			'post_status'    => 'publish',
			'meta_key'       => '_et_pb_predefined_default_layout',
			'meta_value'     => 'on',
		);

		$layout_args['meta_query'] = array(
			array(
				'key'     => '_et_pb_predefined_default_type',
				'value'   => 'home',
				'compare' => 'IN',
			),
		);

		// get the predefiend default home layouts
		$home_layouts_query = extra_get_layouts( $layout_args );

		$layout_args['meta_query'] = array(
			array(
				'key'     => '_et_pb_predefined_default_type',
				'value'   => 'index',
				'compare' => 'IN',
			),
		);

		// get the predefiend default layouts
		$default_layouts_query = extra_get_layouts( $layout_args );

		if ( $home_layouts_query->posts ) {
			$is_home_layout_exists = true;
		}

		if ( $default_layouts_query->posts ) {
			$is_index_layout_exists = true;
		}

		// do not proceed if both layouts already exist
		if ( $is_index_layout_exists && $is_home_layout_exists ) {
			return;
		}

		if ( isset( $et_builder_layouts ) && is_array( $et_builder_layouts ) ) {
			foreach ( $et_builder_layouts as $et_builder_layout ) {
				// do nothing if current layout already exist
				if ( ( isset( $et_builder_layout['default_home'] ) && $et_builder_layout['default_home'] && $is_home_layout_exists ) || ( isset( $et_builder_layout['default_index'] ) && $et_builder_layout['default_index'] && $is_index_layout_exists ) ) {
					continue;
				}

				$meta = array(
					'_et_pb_predefined_default_layout' => 'on',
					'_et_pb_predefined_default_type'   => $et_builder_layout['default_type'],
				);

				// add meta for default home and index page layouts
				if ( 'home' === $et_builder_layout['default_type'] && false === extra_get_home_layout_id() ) {
					$meta['_extra_layout_home'] = 1;
				} else if ( 'index' === $et_builder_layout['default_type'] && false === extra_get_default_layout_id() ) {
					$meta['_extra_layout_default'] = 1;
				}

				et_pb_create_extra_layout( $et_builder_layout['name'], $et_builder_layout['content'], $meta );
			}
		}
	}

endif;

if ( ! function_exists( 'et_pb_create_extra_layout' ) ) :

	function et_pb_create_extra_layout( $name, $content, $meta = array(), $tax_input = array() ) {
		$layout = array(
			'post_title'   => sanitize_text_field( $name ),
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => 'layout',
		);

		$layout_id = wp_insert_post( $layout );

		if ( !empty( $meta ) ) {
			foreach ( $meta as $meta_key => $meta_value ) {
				add_post_meta( $layout_id, $meta_key, sanitize_text_field( $meta_value ) );
			}
		}

		if ( ! empty( $tax_input ) ) {
			foreach ( $tax_input as $taxonomy => $terms ) {
				wp_set_post_terms( $layout_id, $terms, $taxonomy );
			}
		}

		return $layout_id;
	}

endif;

function et_pb_extra_delete_predefined_layouts( $built_for_post_type = '' ) {
	$args = array(
		'posts_per_page' => -1,
		'post_type'      => ET_BUILDER_LAYOUT_POST_TYPE,
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => '_et_pb_predefined_layout',
				'value'   => 'on',
				'compare' => 'EXISTS',
			),
		),
	);

	if ( !empty( $built_for_post_type ) ) {
		$args['meta_query'][] = array(
			'key'     => '_et_pb_built_for_post_type',
			'value'   => $built_for_post_type,
			'compare' => 'IN',
		);
	} else {
		$args['meta_query'][] = array(
			'key'     => '_et_pb_built_for_post_type',
			'compare' => 'NOT EXISTS',
		);
	}

	$predefined_layouts = get_posts( $args );

	if ( $predefined_layouts ) {
		foreach ( $predefined_layouts as $predefined_layout ) {
			if ( isset( $predefined_layout->ID ) ) {
				wp_delete_post( $predefined_layout->ID, true );
			}
		}
	}
}

if ( ! function_exists( 'et_pb_extra_get_predefined_layouts' ) ) :

	function et_pb_extra_get_predefined_layouts() {
		$layouts = array();

		$layouts[] = array(
			'name'    => esc_html__( 'Homepage Basic', 'extra' ),
			'content' => <<<EOT
[et_pb_section admin_label="section"][et_pb_row admin_label="row"][et_pb_column type="4_4"][et_pb_featured_posts_slider admin_label="Featured Posts Slider" category_id="0" display_featured_posts_only="on" orderby="comment_count" order="desc" enable_autoplay="1" show_thumbnails="1" show_author="1" show_categories="1" show_comments="1" show_rating="1" show_date="1" /][et_pb_posts_blog_feed_masonry admin_label="Blog Feed Masonry" show_pagination="1" show_author="1" show_categories="1" show_featured_image="1" content_length="excerpt" show_date="1" category_id="0" heading_style="category" orderby="date" order="desc" show_comments="1" show_rating="1" /][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
		);

		$layouts[] = array(
			'name'    => esc_html__( 'Standard Blog Homepage', 'extra' ),
			'content' => <<<EOT
[et_pb_section admin_label="section"][et_pb_row admin_label="row"][et_pb_column type="4_4"][et_pb_featured_posts_slider admin_label="Featured Posts Slider" display_featured_posts_only="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" enable_autoplay="off" orderby="date" order="desc" remove_drop_shadow="off"] [/et_pb_featured_posts_slider][/et_pb_column][/et_pb_row][et_pb_row admin_label="Row"][et_pb_column type="1_2"][et_pb_posts admin_label="Posts" display_featured_posts_only="off" show_thumbnails="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" heading_style="category" orderby="comment_count" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off"] [/et_pb_posts][/et_pb_column][et_pb_column type="1_2"][et_pb_posts admin_label="Posts" display_featured_posts_only="off" show_thumbnails="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" heading_style="category" orderby="rating" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off"] [/et_pb_posts][/et_pb_column][/et_pb_row][et_pb_row admin_label="Row"][et_pb_column type="4_4"][et_pb_posts_blog_feed_standard admin_label="Blog Feed Standard" show_pagination="on" show_author="on" show_categories="on" show_featured_image="on" show_more="on" show_date="on" display_featured_posts_only="off" show_comments="on" show_rating="on" content_length="excerpt" heading_style="category" orderby="date" order="desc" custom_read_more="off" read_more_text_size="20" read_more_border_width="2" read_more_border_radius="3" read_more_letter_spacing="0" read_more_use_icon="default" read_more_icon_placement="right" read_more_on_hover="on" read_more_border_radius_hover="3" read_more_letter_spacing_hover="0" remove_drop_shadow="off" posts_per_page="4"] [/et_pb_posts_blog_feed_standard][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
		);

		$layouts[] = array(
			'name'    => esc_html__( 'Masonry Blog Homepage', 'extra' ),
			'content' => <<<EOT
[et_pb_section admin_label="section"][et_pb_row admin_label="row"][et_pb_column type="4_4"][et_pb_featured_posts_slider admin_label="Featured Posts Slider" display_featured_posts_only="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" enable_autoplay="off" orderby="date" order="desc" remove_drop_shadow="off"] [/et_pb_featured_posts_slider][/et_pb_column][/et_pb_row][et_pb_row admin_label="Row"][et_pb_column type="1_3"][et_pb_posts admin_label="Posts" display_featured_posts_only="off" show_thumbnails="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" heading_style="category" orderby="comment_count" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off"] [/et_pb_posts][/et_pb_column][et_pb_column type="1_3"][et_pb_posts admin_label="Posts" display_featured_posts_only="off" show_thumbnails="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" heading_style="category" orderby="rating" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off"] [/et_pb_posts][/et_pb_column][et_pb_column type="1_3"][et_pb_posts admin_label="Posts" display_featured_posts_only="off" show_thumbnails="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" heading_style="category" orderby="date" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off"] [/et_pb_posts][/et_pb_column][/et_pb_row][et_pb_row admin_label="Row"][et_pb_column type="4_4"][et_pb_posts_blog_feed_masonry admin_label="Blog Feed Masonry" show_pagination="on" show_author="on" show_categories="on" show_featured_image="on" show_more="on" show_date="on" display_featured_posts_only="off" show_comments="on" show_rating="on" content_length="excerpt" heading_style="category" orderby="date" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" custom_read_more="off" read_more_text_size="20" read_more_border_width="2" read_more_border_radius="3" read_more_letter_spacing="0" read_more_use_icon="default" read_more_icon_placement="right" read_more_on_hover="on" read_more_border_radius_hover="3" read_more_letter_spacing_hover="0" remove_drop_shadow="off" posts_per_page="4"] [/et_pb_posts_blog_feed_masonry][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
		);

		$layouts[] = array(
			'name'    => esc_html__( 'Magazine Homepage', 'extra' ),
			'content' => <<<EOT
[et_pb_section admin_label="section"][et_pb_row admin_label="row"][et_pb_column type="4_4"][et_pb_featured_posts_slider admin_label="Featured Posts Slider" category_id="0" display_featured_posts_only="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" enable_autoplay="off" orderby="date" order="desc" remove_drop_shadow="off"] [/et_pb_featured_posts_slider][/et_pb_column][/et_pb_row][et_pb_row admin_label="Row"][et_pb_column type="1_2"][et_pb_posts admin_label="Posts" category_id="0" posts_per_page="4" display_featured_posts_only="off" show_thumbnails="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" heading_style="category" orderby="comment_count" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off" /][/et_pb_column][et_pb_column type="1_2"][et_pb_posts admin_label="Posts" category_id="0" posts_per_page="4" display_featured_posts_only="off" show_thumbnails="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" heading_style="category" orderby="date" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off" /][/et_pb_column][/et_pb_row][et_pb_row admin_label="Row"][et_pb_column type="4_4"][et_pb_posts_carousel admin_label="Posts Carousel" display_featured_posts_only="off" show_date="on" enable_autoplay="off" heading_style="category" orderby="date" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off" /][/et_pb_column][/et_pb_row][et_pb_row admin_label="Row"][et_pb_column type="1_3"][et_pb_posts admin_label="Posts" category_id="0" posts_per_page="4" display_featured_posts_only="off" show_thumbnails="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" heading_style="category" orderby="comment_count" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off"] [/et_pb_posts][/et_pb_column][et_pb_column type="1_3"][et_pb_posts admin_label="Posts" category_id="0" posts_per_page="4" display_featured_posts_only="off" show_thumbnails="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" heading_style="category" orderby="rating" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off"] [/et_pb_posts][/et_pb_column][et_pb_column type="1_3"][et_pb_posts admin_label="Posts" category_id="0" posts_per_page="4" display_featured_posts_only="off" show_thumbnails="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" heading_style="category" orderby="date" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off"] [/et_pb_posts][/et_pb_column][/et_pb_row][et_pb_row admin_label="Row"][et_pb_column type="4_4"][et_pb_tabbed_posts admin_label="Tabbed Posts" show_thumbnails="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off"] [et_pb_tabbed_posts_tab category_id="0" category_name="All" display_featured_posts_only="off" /][et_pb_tabbed_posts_tab category_id="0" category_name="All" display_featured_posts_only="off" /][et_pb_tabbed_posts_tab category_id="0" category_name="All" display_featured_posts_only="off" /][et_pb_tabbed_posts_tab category_id="0" category_name="All" display_featured_posts_only="off" /] [/et_pb_tabbed_posts][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
		);

		$layouts[] = array(
			'name'    => esc_html__( 'Standard Blog Category', 'extra' ),
			'content' => <<<EOT
[et_pb_section admin_label="section"][et_pb_row admin_label="Row"][et_pb_column type="4_4"][et_pb_posts_blog_feed_standard admin_label="Blog Feed Standard" show_pagination="on" show_author="on" show_categories="on" show_featured_image="on" show_more="on" show_date="on" display_featured_posts_only="off" show_comments="on" show_rating="on" content_length="excerpt" heading_style="category" orderby="date" order="desc" custom_read_more="off" read_more_text_size="20" read_more_border_width="2" read_more_border_radius="3" read_more_letter_spacing="0" read_more_use_icon="default" read_more_icon_placement="right" read_more_on_hover="on" read_more_border_radius_hover="3" read_more_letter_spacing_hover="0" remove_drop_shadow="off" posts_per_page="8"] [/et_pb_posts_blog_feed_standard][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
		);

		$layouts[] = array(
			'name'    => esc_html__( 'Masonry Blog Category', 'extra' ),
			'content' => <<<EOT
[et_pb_section admin_label="section"][et_pb_row admin_label="Row"][et_pb_column type="4_4"][et_pb_posts_blog_feed_masonry admin_label="Blog Feed Masonry" show_pagination="on" show_author="on" show_categories="on" show_featured_image="on" show_more="on" show_date="on" display_featured_posts_only="off" show_comments="on" show_rating="on" content_length="excerpt" heading_style="category" orderby="date" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" custom_read_more="off" read_more_text_size="20" read_more_border_width="2" read_more_border_radius="3" read_more_letter_spacing="0" read_more_use_icon="default" read_more_icon_placement="right" read_more_on_hover="on" read_more_border_radius_hover="3" read_more_letter_spacing_hover="0" remove_drop_shadow="off" posts_per_page="10"] [/et_pb_posts_blog_feed_masonry][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
		);

		$layouts[] = array(
			'name'    => esc_html__( 'Standard Blog Category With Featured Posts', 'extra' ),
			'content' => <<<EOT
[et_pb_section admin_label="section"][et_pb_row admin_label="row"][et_pb_column type="4_4"][et_pb_featured_posts_slider admin_label="Featured Posts Slider" display_featured_posts_only="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" enable_autoplay="off" orderby="date" order="desc" remove_drop_shadow="off"] [/et_pb_featured_posts_slider][/et_pb_column][/et_pb_row][et_pb_row admin_label="Row"][et_pb_column type="4_4"][et_pb_posts_blog_feed_standard admin_label="Blog Feed Standard" show_pagination="on" show_author="on" show_categories="on" show_featured_image="on" show_more="on" show_date="on" display_featured_posts_only="off" show_comments="on" show_rating="on" content_length="excerpt" heading_style="category" orderby="date" order="desc" custom_read_more="off" read_more_text_size="20" read_more_border_width="2" read_more_border_radius="3" read_more_letter_spacing="0" read_more_use_icon="default" read_more_icon_placement="right" read_more_on_hover="on" read_more_border_radius_hover="3" read_more_letter_spacing_hover="0" remove_drop_shadow="off" posts_per_page="6"] [/et_pb_posts_blog_feed_standard][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
		);

		$layouts[] = array(
			'name'    => esc_html__( 'Masonry Blog Category With Featured Posts', 'extra' ),
			'content' => <<<EOT
[et_pb_section admin_label="section"][et_pb_row admin_label="row"][et_pb_column type="4_4"][et_pb_featured_posts_slider admin_label="Featured Posts Slider" display_featured_posts_only="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" enable_autoplay="off" orderby="date" order="desc" remove_drop_shadow="off"] [/et_pb_featured_posts_slider][/et_pb_column][/et_pb_row][et_pb_row admin_label="Row"][et_pb_column type="4_4"][et_pb_posts_blog_feed_masonry admin_label="Blog Feed Masonry" show_pagination="on" show_author="on" show_categories="on" show_featured_image="on" show_more="on" show_date="on" display_featured_posts_only="off" show_comments="on" show_rating="on" content_length="excerpt" heading_style="category" orderby="date" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" custom_read_more="off" read_more_text_size="20" read_more_border_width="2" read_more_border_radius="3" read_more_letter_spacing="0" read_more_use_icon="default" read_more_icon_placement="right" read_more_on_hover="on" read_more_border_radius_hover="3" read_more_letter_spacing_hover="0" remove_drop_shadow="off" posts_per_page="6"] [/et_pb_posts_blog_feed_masonry][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
		);

		return $layouts;
	}

endif;

if ( ! function_exists( 'et_pb_extra_get_default_layouts' ) ) :

	function et_pb_extra_get_default_layouts() {
		// home layout should be first, otherwise it'll not be set as defult homepage and Index layout will be used
		$layouts[] = array(
			'name'         => esc_html__( 'Homepage', 'extra' ),
			'default_type' => 'home',
			'content'      => <<<EOT
[et_pb_section admin_label="section"][et_pb_row admin_label="row"][et_pb_column type="4_4"][et_pb_featured_posts_slider admin_label="Featured Posts Slider" category_id="0" display_featured_posts_only="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" enable_autoplay="off" orderby="date" order="desc" remove_drop_shadow="off" /][/et_pb_column][/et_pb_row][et_pb_row admin_label="Row"][et_pb_column type="1_3"][et_pb_posts admin_label="Posts" category_id="0" posts_per_page="4" display_featured_posts_only="off" show_thumbnails="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" heading_style="category" orderby="comment_count" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off" /][/et_pb_column][et_pb_column type="1_3"][et_pb_posts admin_label="Posts" category_id="0" posts_per_page="4" display_featured_posts_only="off" show_thumbnails="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" heading_style="category" orderby="rating" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off" /][/et_pb_column][et_pb_column type="1_3"][et_pb_posts admin_label="Posts" category_id="0" posts_per_page="4" display_featured_posts_only="off" show_thumbnails="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" heading_style="category" orderby="date" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" remove_drop_shadow="off" /][/et_pb_column][/et_pb_row][et_pb_row admin_label="Row"][et_pb_column type="4_4"][et_pb_posts_blog_feed_masonry admin_label="Blog Feed Masonry" category_id="0" posts_per_page="6" show_pagination="on" show_author="on" show_categories="on" show_featured_image="on" show_more="on" show_date="on" display_featured_posts_only="off" show_comments="on" show_rating="on" content_length="excerpt" heading_style="category" orderby="date" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" custom_read_more="off" read_more_text_size="20" read_more_border_width="2" read_more_border_radius="3" read_more_letter_spacing="0" read_more_use_icon="default" read_more_icon_placement="right" read_more_on_hover="on" read_more_border_radius_hover="3" read_more_letter_spacing_hover="0" remove_drop_shadow="off" /][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
		);

		$layouts[] = array(
			'name'         => esc_html__( 'Default Category', 'extra' ),
			'default_type' => 'index',
			'content'      => <<<EOT
[et_pb_section admin_label="section"][et_pb_row admin_label="row"][et_pb_column type="4_4"][et_pb_featured_posts_slider admin_label="Featured Posts Slider" category_id="-1" display_featured_posts_only="on" show_author="on" show_categories="on" show_comments="on" show_rating="on" show_date="on" enable_autoplay="off" orderby="date" order="desc" remove_drop_shadow="off" /][et_pb_posts_blog_feed_masonry admin_label="Blog Feed Masonry" category_id="-1" posts_per_page="12" show_pagination="on" show_author="on" show_categories="on" show_featured_image="on" show_more="on" show_date="on" display_featured_posts_only="off" show_comments="on" show_rating="on" content_length="excerpt" heading_style="category" orderby="date" order="desc" use_border_color="off" border_color="#ffffff" border_style="solid" custom_read_more="off" read_more_text_size="20" read_more_border_width="2" read_more_border_radius="3" read_more_letter_spacing="0" read_more_use_icon="default" read_more_icon_placement="right" read_more_on_hover="on" read_more_border_radius_hover="3" read_more_letter_spacing_hover="0" remove_drop_shadow="off" /][/et_pb_column][/et_pb_row][/et_pb_section]
EOT
		);

		return $layouts;
	}

endif;

function et_pb_extra_options_builder_params( $params ) {
	global $post;

	if ( isset( $params['preview_url'] ) && isset( $post->post_type ) && EXTRA_LAYOUT_POST_TYPE === $post->post_type ) {
		$params['preview_url'] = add_query_arg( 'is_extra_layout', '1', $params['preview_url'] );
	}

	return $params;
}

add_filter( 'et_pb_options_builder', 'et_pb_extra_options_builder_params' );

function et_pb_extra_admin_scripts_styles() {
	global $typenow;

	// Layout only adjustment
	if ( isset( $typenow ) && EXTRA_LAYOUT_POST_TYPE === $typenow ) {
		$builder_adjustment = "
			@media only screen and (min-width: 1035px) {
				.et_pb_modal_settings_container,
				.et-pb-preview-screensize-switcher, .et-pb-options-tabs-links,
				.et-pb-item-previewing .et-pb-preview-tab {
					width: 1025px
				}

				.et_pb_modal_settings_container {
					margin-left: -512.5px;
				}

				.et_modal_on_top .et_pb_prompt_modal_save_library {
					width: 1025px;
					margin-left: -513px;
				}
			}
		";

		wp_add_inline_style( 'et_pb_admin_css', $builder_adjustment );
	}
}

add_action( 'admin_enqueue_scripts', 'et_pb_extra_admin_scripts_styles', 15 );

function extra_builder_set_default_values( $defaults ) {
	// Font style value
	$bold_uppercase_value = '|on||on|';

	$uppercase_value = '|||on|';

	$bold_value = '|on|||';

	// Common Extra module element styling
	$header = array(
		'font'           => $bold_uppercase_value,
		'font_size'      => '16px',
		'text_color'     => '',
		'letter_spacing' => '1.2px',
		'line_height'    => '1em',
	);

	$subheader = array(
		'font'           => '',
		'font_size'      => '14px',
		'text_color'     => '',
		'letter_spacing' => '0px',
		'line_height'    => '1em',
	);

	$title = array(
		'font'           => $bold_uppercase_value,
		'font_size'      => '16px',
		'text_color'     => '',
		'letter_spacing' => '0.5px',
		'line_height'    => '1.3em',
	);

	$meta = array(
		'font'           => '',
		'font_size'      => '12px',
		'text_color'     => '',
		'letter_spacing' => '0px',
		'line_height'    => '1em',
	);

	$body = array(
		'font'           => '',
		'font_size'      => '14px',
		'text_color'     => '',
		'letter_spacing' => '0px',
		'line_height'    => '1.8em',
	);

	$list_title = array(
		'font'           => $bold_value,
		'font_size'      => '14px',
		'text_color'     => '',
		'letter_spacing' => '0px',
		'line_height'    => '1.3em',
	);

	$border = array(
		'radius' => '3px',
	);

	// Overriding divi builder styling
	$builder_toggle_font = array(
		'toggle' => array(
			'font' => $bold_uppercase_value,
		),
	);

	$builder_title_font = array(
		'title' => array(
			'font' => $bold_uppercase_value,
		),
	);

	$builder_header_font = array(
		'header' => array(
			'font' => $bold_uppercase_value,
		),
	);

	$builder_header_font_uppercase_only = array(
		'header' => array(
			'font' => $uppercase_value,
		),
	);

	$builder_caption_font = array(
		'caption' => array(
			'font' => $bold_uppercase_value,
		),
	);

	$extra_module_defaults = array(
		'et_pb_posts'                    => array(
			'header'     => $header,
			'subheader'  => $subheader,
			'main_title' => $title,
			'main_meta'  => $meta,
			'main_body'  => $body,
			'list_title' => $list_title,
			'list_meta'  => $meta,
			'border'     => $border,
		),
		'et_pb_tabbed_posts'             => array(
			'tab'        => $header,
			'main_title' => $title,
			'main_meta'  => $meta,
			'main_body'  => $body,
			'list_title' => $list_title,
			'list_meta'  => $meta,
			'border'     => $border,
		),
		'et_pb_posts_carousel'           => array(
			'header'    => $header,
			'subheader' => $subheader,
			'title'     => array(
				'font'           => $bold_value,
				'font_size'      => '14px',
				'text_color'     => '',
				'letter_spacing' => '0.5px',
				'line_height'    => '1.3em',
			),
			'meta'      => $meta,
			'border'    => $border,
		),
		'et_pb_featured_posts_slider'    => array(
			'title'  => array(
				'font'           => $bold_value,
				'font_size'      => '20px',
				'text_color'     => '',
				'letter_spacing' => '0.5px',
				'line_height'    => '1.3em',
			),
			'meta'   => $meta,
			'border' => $border,
		),
		'et_pb_posts_blog_feed_standard' => array(
			'header' => $header,
			'title'  => $title,
			'meta'   => $meta,
			'body'   => $body,
			'border' => $border,
		),
		'et_pb_posts_blog_feed_masonry'  => array(
			'title'  => $title,
			'meta'   => $meta,
			'body'   => $body,
			'border' => $border,
		),
		'et_pb_ads'                      => array(
			'header' => $header,
			'border' => $border,
		),

		'et_pb_accordion'                => $builder_toggle_font,
		'et_pb_audio'                    => $builder_title_font,
		'et_pb_blog'                     => $builder_header_font,
		'et_pb_blurb'                    => $builder_header_font,
		'et_pb_cta'                      => $builder_header_font,
		'et_pb_circle_counter'           => $builder_title_font,
		'et_pb_contact_form'             => $builder_title_font,
		'et_pb_countdown_timer'          => $builder_header_font_uppercase_only,
		'et_pb_signup'                   => $builder_header_font,
		'et_pb_filterable_portfolio'     => $builder_title_font,
		'et_pb_gallery'                  => $builder_caption_font,
		'et_pb_login'                    => $builder_header_font,
		'et_pb_number_counter'           => $builder_title_font,
		'et_pb_team_member'              => $builder_header_font,
		'et_pb_portfolio'                => $builder_title_font,
		'et_pb_post_title'               => $builder_title_font,
		'et_pb_pricing_tables'           => $builder_header_font,
		'et_pb_shop'                     => $builder_title_font,
		'et_pb_sidebar'                  => $builder_header_font,
		'et_pb_slider'                   => $builder_header_font_uppercase_only,
		'et_pb_slide'                    => $builder_header_font_uppercase_only,
		'et_pb_toggle'                   => $builder_title_font,
		'et_pb_fullwidth_header'         => $builder_title_font,
		'et_pb_fullwidth_post_title'     => $builder_title_font,
		'et_pb_fullwidth_slider'         => $builder_header_font_uppercase_only,

	);

	// Format default values
	foreach ( $extra_module_defaults as $module_name => $module ) {
		foreach ( $module as $element => $properties ) {
			foreach ( $properties as $property => $property_value ) {
				$defaults["{$module_name}-{$element}_{$property}"]['default'] = $property_value;
			}
		}
	}

	return $defaults;
}

add_filter( 'et_set_default_values', 'extra_builder_set_default_values' );

// load Extra scripts for library page
foreach ( array( 'edit', 'post' ) as $hook ) {
	add_action( "admin_head-{$hook}.php", 'et_extra_library_custom_scripts' );
}

function et_extra_library_custom_scripts() {
	global $typenow;

	if ( 'et_pb_layout' === $typenow ) {
		$new_layout_modal = et_pb_generate_new_layout_modal();

		wp_enqueue_script( 'extra-library-scripts', get_template_directory_uri() . '/includes/admin/scripts/library_scripts_extra.js', array( 'jquery' ) );
	}
}

function et_pb_generate_extra_checkbox() {
	$output = sprintf( '<label>%1$s<input type="checkbox" value="extra_layout" id="et_pb_extra_layout" /></label>', esc_html__( 'Extra - Layout', 'extra' ) );
	return $output;
}

// Add Extra checkbox into the Library New Layout modal
add_filter( 'et_pb_new_layout_before_options', 'et_pb_generate_extra_checkbox' );

/**
 * Prefix main elements selectors on Extra layout
 *
 * @param string  CSS selector
 * @param string  function name
 * @return string modified CSS selector
 */
function extra_layout_selector_prefixer( $selector, $function_name ) {
	// List of module slugs that need to be prefixed
	$prefixed_modules = apply_filters( 'extra_layout_prefixed_selectors', array(
		'et_pb_section',
		'et_pb_row',
		'et_pb_row_inner',
		'et_pb_column',
	));

	// Prefixing selectors in Extra layout
	if ( extra_layout_used() || ( is_et_pb_preview() && isset( $_GET['is_extra_layout'] ) ) && in_array( $function_name, $prefixed_modules ) ) {
		if ( 'default' === ET_Builder_Element::get_theme_builder_layout_type() ) {
			if ( 'body ' === substr( $selector, 0, 5 ) ) {
				$selector = str_replace( 'body ', 'body.et_extra_layout .et_pb_extra_column_main ', $selector );
			} else {
				$selector = ".et_extra_layout .et_pb_extra_column_main {$selector}";
			}
		}
	}

	return $selector;
}
add_filter( 'et_pb_set_style_selector', 'extra_layout_selector_prefixer', 10, 2 );

/**
 * Switch the translation of Visual Builder interface to current user's language
 * @return void
 */
if ( ! function_exists( 'et_divi_builder_setup_thumbnails' ) ) :
function et_fb_set_builder_locale( $locale ) {
	// apply translations inside VB only
	if ( empty( $_GET['et_fb'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		return $locale;
	}

	$user = get_user_locale();

	if ( $user === $locale ) {
		return $locale;
	}

	if ( ! function_exists( 'switch_to_locale' ) ) {
		return $locale;
	}

	switch_to_locale( $user );

	return $user;
}
endif;
add_filter( 'theme_locale', 'et_fb_set_builder_locale' );

/**
 * Added custom post class
 * @param array $classes array of post classes
 * @param array $class   array of additional post classes
 * @param int   $post_id post ID
 * @return array modified array of post classes
 */
function et_pb_extra_post_class( $classes, $class, $post_id ) {
	global $post;

	// Added specific class name if curent post uses comment module. Use global $post->post_content
	// instead of get_the_content() to retrieve the post's unparsed shortcode content
	if ( is_singular() && has_shortcode( $post->post_content, 'et_pb_comments' ) ) {
		$classes[] = 'et_pb_no_comments_section';
	}

	return $classes;
}
add_filter( 'post_class', 'et_pb_extra_post_class', 10, 3 );

/**
 * Check whether current page is Extra theme (or child theme)'s category builder page
 *
 * @since ??
 *
 * @return bool
 */
function extra_is_category_builder_edit_screen() {
	global $pagenow;

	$utils          = ET_Core_Data_Utils::instance();
	$is_edit_screen = in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
	$is_cpt_layout  = $is_edit_screen && ( 'layout' === get_post_type() || 'layout' === $utils->array_get( $_GET, 'post_type' ) );

	return is_admin() && $is_edit_screen && $is_cpt_layout;
}

/**
 * Hook to the proper filter(s) to disable BFB and its notification on category builder
 *
 * @todo possibly remove this once BFB support for Extra category builder has been added
 *
 * @since ??
 *
 * @return bool
 */
function extra_disable_bfb_on_category_builder( $value ) {
	return $value ? ! extra_is_category_builder_edit_screen() : $value;
}

// Should have been hooked after et_builder_filter_bfb_enabled callback
add_filter( 'et_builder_bfb_enabled', 'extra_disable_bfb_on_category_builder', 20 );

// Should have been hooked after et_builder_filter_show_bfb_optin_modal callback
add_filter( 'et_builder_show_bfb_optin_modal', 'extra_disable_bfb_on_category_builder', 20 );

add_filter( 'et_pb_display_bfb_notification_under_bb', 'extra_disable_bfb_on_category_builder' );

function extra_maybe_adjust_row_advanced_options_config( $advanced_options ) {
	$selector = implode( ', ', array(
		'%%order_class%%',
		'.page %%order_class%%.et_pb_row',
		'.single %%order_class%%.et_pb_row',
	) );

	et_()->array_set( $advanced_options, 'max_width.css.width', $selector );
	et_()->array_set( $advanced_options, 'max_width.options.width.default', '90%' );

	return $advanced_options;
}
add_filter( 'et_pb_row_advanced_fields', 'extra_maybe_adjust_row_advanced_options_config' );

/**
 * Modifies the initial section background color to be transparent.
 *
 * This section is the first section in the default WooCommerce Modules layout.
 *
 * @see et_builder_wc_get_initial_content()
 *
 * @return string
 */
function extra_modify_initial_wc_section_bg() {
	return 'rgba(255, 255, 255, 0)';
}
add_filter( 'et_builder_wc_initial_top_section_bg', 'extra_modify_initial_wc_section_bg' );
