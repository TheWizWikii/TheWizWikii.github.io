<?php
/**
 * Core builder functions.
 *
 * @package Builder
 * @since 1.0
 */

if ( ! defined( 'ET_BUILDER_PRODUCT_VERSION' ) ) {
	// Note, this will be updated automatically during grunt release task.
	define( 'ET_BUILDER_PRODUCT_VERSION', '4.26.0' );
}

if ( ! defined( 'ET_BUILDER_VERSION' ) ) {
	define( 'ET_BUILDER_VERSION', 0.7 );
}

if ( ! defined( 'ET_BUILDER_FORCE_CACHE_PURGE' ) ) {
	define( 'ET_BUILDER_FORCE_CACHE_PURGE', false );
}

if ( ! defined( 'ET_BUILDER_DIR_RESOLVED_PATH' ) ) {
	define( 'ET_BUILDER_DIR_RESOLVED_PATH', dirname( __FILE__ ) );
}

// When set to true, the builder will use the new loading method.
if ( ! defined( 'ET_BUILDER_CACHE_ASSETS' ) ) {
	define( 'ET_BUILDER_CACHE_ASSETS', ! isset( $_REQUEST['nocache'] ) ); // phpcs:ignore WordPress.Security.NonceVerification --  CSRF ok.
}

if ( ! defined( 'ET_BUILDER_CACHE_MODULES' ) ) {
	define( 'ET_BUILDER_CACHE_MODULES', apply_filters( 'et_builder_cache_modules', true ) );
}

if ( ! defined( 'ET_BUILDER_JSON_ENCODE_OPTIONS' ) ) {
	define( 'ET_BUILDER_JSON_ENCODE_OPTIONS', 0 );
}

if ( ! defined( 'ET_BUILDER_KEEP_OLDEST_CACHED_ASSETS' ) ) {
	define( 'ET_BUILDER_KEEP_OLDEST_CACHED_ASSETS', false );
}

if ( ! defined( 'ET_BUILDER_PURGE_OLD_CACHED_ASSETS' ) ) {
	define( 'ET_BUILDER_PURGE_OLD_CACHED_ASSETS', true );
}

if ( defined( 'ET_BUILDER_DEFINITION_SORT' ) && ET_BUILDER_DEFINITION_SORT ) {

	/**
	 * You don't want to know and this isn't the function you're looking for.
	 * Still reading ? Aight, this is only used to debug definitions.
	 *
	 * @param array $definitions Definitions.
	 */
	function et_builder_definition_sort( &$definitions ) {
		if ( ! is_array( $definitions ) ) {
			return;
		}

		$fields = array_keys( $definitions );
		$order  = array(
			'label',
			'description',
			'option_category',
			'type',
			'data_type',
			'upload_button_text',
		);

		foreach ( $fields as $field ) {
			$definition =& $definitions[ $field ];

			if ( is_array( $definition ) ) {
				foreach ( $order as $key ) {
					if ( isset( $definition[ $key ] ) ) {
						$value = $definition[ $key ];
						unset( $definition[ $key ] );
						$definition[ $key ] = $value;
					}
				}
				et_builder_definition_sort( $definition );
			}
		}
	}
}

$et_fonts_queue = array();

/**
 * Exclude predefined layouts from import.
 *
 * @param array $posts The imported posts.
 *
 * @return array
 */
function et_remove_predefined_layouts_from_import( $posts ) {
	$processed_posts = $posts;

	if ( isset( $posts ) && is_array( $posts ) ) {
		$processed_posts = array();

		foreach ( $posts as $post ) {
			if ( isset( $post['postmeta'] ) && is_array( $post['postmeta'] ) ) {
				foreach ( $post['postmeta'] as $meta ) {
					if ( '_et_pb_predefined_layout' === $meta['key'] && 'on' === $meta['value'] ) {
						continue 2;
					}
				}
			}

			$processed_posts[] = $post;
		}
	}

	return $processed_posts;
}
add_filter( 'wp_import_posts', 'et_remove_predefined_layouts_from_import', 5 );


/**
 * Output all module fields JSON dump.
 * For dev use only.
 *
 * @return void
 */
// phpcs:disable Squiz.PHP.CommentedOutCode -- This is debug function.
// function et_dev_output_all_fields() {
// die( json_encode( ET_Builder_Element::get_all_fields('page') ) );
// }
// add_action('wp', 'et_dev_output_all_fields', 100);.
// phpcs:enable
/**
 * Set the layout_type taxonomy to "layout" for layouts imported from old version of Divi.
 *
 * @param array $posts Imported posts.
 *
 * @return array
 */
function et_update_old_layouts_taxonomy( $posts ) {
	$processed_posts = $posts;

	if ( isset( $posts ) && is_array( $posts ) ) {
		$processed_posts = array();

		foreach ( $posts as $post ) {
			$update_built_for_post_type = false;

			if ( 'et_pb_layout' === $post['post_type'] ) {
				if ( ! isset( $post['terms'] ) ) {
					$post['terms'][] = array(
						'name'   => 'layout',
						'slug'   => 'layout',
						'domain' => 'layout_type',
					);
					$post['terms'][] = array(
						'name'   => 'not_global',
						'slug'   => 'not_global',
						'domain' => 'scope',
					);
				}

				$update_built_for_post_type = true;

				// check whether _et_pb_built_for_post_type custom field exists.
				if ( ! empty( $post['postmeta'] ) ) {
					foreach ( $post['postmeta'] as $index => $value ) {
						if ( '_et_pb_built_for_post_type' === $value['key'] ) {
							$update_built_for_post_type = false;
						}
					}
				}
			}

			// set _et_pb_built_for_post_type value to 'page' if not exists.
			if ( $update_built_for_post_type ) {
				$post['postmeta'][] = array(
					'key'   => '_et_pb_built_for_post_type',
					'value' => 'page',
				);
			}

			$processed_posts[] = $post;
		}
	}

	return $processed_posts;
}
add_filter( 'wp_import_posts', 'et_update_old_layouts_taxonomy', 10 );

if ( ! function_exists( 'et_pb_add_layout_filters' ) ) :
	/**
	 * Add custom filters for posts in the Divi Library.
	 */
	function et_pb_add_layout_filters() {
		// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		if ( isset( $_GET['post_type'] ) && 'et_pb_layout' === $_GET['post_type'] ) {
			$layout_categories   = get_terms( 'layout_category' );
			$filter_category     = array();
			$filter_category[''] = esc_html__( 'All Categories', 'et_builder' );

			if ( is_array( $layout_categories ) && ! empty( $layout_categories ) ) {
				foreach ( $layout_categories as $category ) {
					$filter_category[ $category->slug ] = $category->name;
				}
			}

			$layout_packs    = get_terms( 'layout_pack' );
			$filter_pack     = array();
			$filter_pack[''] = esc_html_x( 'All Packs', 'Layout Packs', 'et_builder' );

			if ( is_array( $layout_packs ) ) {
				foreach ( $layout_packs as $pack ) {
					$filter_pack[ $pack->slug ] = $pack->name;
				}
			}

			$filter_layout_type = array(
				''        => esc_html__( 'All Types', 'et_builder' ),
				'module'  => esc_html__( 'Modules', 'et_builder' ),
				'row'     => esc_html__( 'Rows', 'et_builder' ),
				'section' => esc_html__( 'Sections', 'et_builder' ),
				'layout'  => esc_html__( 'Layouts', 'et_builder' ),
			);

			$filter_scope = array(
				''           => esc_html__( 'All Scopes', 'et_builder' ),
				'global'     => esc_html__( 'Global', 'et_builder' ),
				'not_global' => esc_html__( 'Not Global', 'et_builder' ),
			);
			?>

		<select name="layout_type">
			<?php
			$selected = isset( $_GET['layout_type'] ) ? sanitize_text_field( $_GET['layout_type'] ) : '';
			foreach ( $filter_layout_type as $value => $label ) {
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $value ),
					selected( $value, $selected ),
					esc_html( $label )
				);
			}
			?>
		</select>

		<select name="scope">
			<?php
			$selected = isset( $_GET['scope'] ) ? sanitize_text_field( $_GET['scope'] ) : '';
			foreach ( $filter_scope as $value => $label ) {
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $value ),
					selected( $value, $selected ),
					esc_html( $label )
				);
			}
			?>
		</select>

		<select name="layout_category">
			<?php
			$selected = isset( $_GET['layout_category'] ) ? sanitize_text_field( $_GET['layout_category'] ) : '';
			foreach ( $filter_category as $value => $label ) {
				printf(
					'<option value="%1$s"%2$s>%3$s</option>',
					esc_attr( $value ),
					selected( $value, $selected ),
					esc_html( $label )
				);
			}
			?>
		</select>

		<select name="layout_pack">
				<?php
				$selected = isset( $_GET['layout_pack'] ) ? sanitize_text_field( $_GET['layout_pack'] ) : '';
				foreach ( $filter_pack as $value => $label ) {
					printf(
						'<option value="%1$s"%2$s>%3$s</option>',
						esc_attr( $value ),
						selected( $value, $selected ),
						esc_html( $label )
					);
				}
				?>
		</select>
			<?php
			// phpcs:enable
		}
	}
endif;
add_action( 'restrict_manage_posts', 'et_pb_add_layout_filters' );

if ( ! function_exists( 'et_pb_load_export_section' ) ) :
	/**
	 * Add "Export Divi Layouts" button to the Divi Library page.
	 */
	function et_pb_load_export_section() {
		$current_screen = get_current_screen();

		if ( 'edit-et_pb_layout' === $current_screen->id ) {
			// display wp error screen if library is disabled for current user.
			if ( ! et_pb_is_allowed( 'divi_library' ) || ! et_pb_is_allowed( 'add_library' ) || ! et_pb_is_allowed( 'save_library' ) ) {
				wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'et_builder' ) );
			}

			add_action( 'all_admin_notices', 'et_pb_export_layouts_interface' );
		}
	}
endif;
add_action( 'load-edit.php', 'et_pb_load_export_section' );

if ( ! function_exists( 'et_pb_edit_library_categories' ) ) :
	/**
	 * Enqueue script on Library Categories editing screen.
	 */
	function et_pb_edit_library_categories() {
		$current_screen = get_current_screen();

		if ( 'edit-layout_category' === $current_screen->id || 'edit-layout_pack' === $current_screen->id ) {
			// display wp error screen if library is disabled for current user.
			if ( ! et_pb_is_allowed( 'divi_library' ) || ! et_pb_is_allowed( 'add_library' ) || ! et_pb_is_allowed( 'save_library' ) ) {
				wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'et_builder' ) );
			}

			wp_enqueue_script( 'builder-library-category', ET_BUILDER_URI . '/scripts/library_category.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
		}
	}
endif;
add_action( 'load-edit-tags.php', 'et_pb_edit_library_categories' );

/**
 * Check whether the library editor page should be displayed or not.
 */
function et_pb_check_library_permissions() {
	$current_screen = get_current_screen();

	if ( 'et_pb_layout' === $current_screen->id && ( ! et_pb_is_allowed( 'divi_library' ) || ! et_pb_is_allowed( 'save_library' ) ) ) {
		// display wp error screen if library is disabled for current user.
		wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'et_builder' ) );
	}
}
add_action( 'load-post.php', 'et_pb_check_library_permissions' );

if ( ! function_exists( 'exclude_premade_layouts_library' ) ) :
	/**
	 * Exclude premade layouts from the list of all templates in the library.
	 *
	 * @param WP_Query $query Query.
	 */
	function exclude_premade_layouts_library( $query ) {
		// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		global $pagenow;
		$current_post_type = get_query_var( 'post_type' );

		if ( is_admin() && 'edit.php' === $pagenow && $current_post_type && 'et_pb_layout' === $current_post_type ) {
			$meta_query = array(
				array(
					'key'     => '_et_pb_predefined_layout',
					'value'   => 'on',
					'compare' => 'NOT EXISTS',
				),
			);

			$used_built_for_post_types = ET_Builder_Library::built_for_post_types( 'all' );
			if ( isset( $_GET['built_for'] ) && count( $used_built_for_post_types ) > 1 ) {
				$built_for_post_type = sanitize_text_field( $_GET['built_for'] );
				// get array of all standard post types if built_for is one of them.
				$built_for_post_type_processed = in_array( $built_for_post_type, ET_Builder_Library::built_for_post_types(), true ) ? ET_Builder_Library::built_for_post_types() : $built_for_post_type;

				if ( in_array( $built_for_post_type, $used_built_for_post_types, true ) ) {
					$meta_query[] = array(
						'key'     => '_et_pb_built_for_post_type',
						'value'   => $built_for_post_type_processed,
						'compare' => 'IN',
					);
				}
			}

			$query->set( 'meta_query', $meta_query );
			//phpcs:enable
		}

		return $query;
	}
endif;
add_action( 'pre_get_posts', 'exclude_premade_layouts_library' );

if ( ! function_exists( 'exclude_premade_layouts_library_count' ) ) :
	/**
	 * Post count for "mine" in post table relies to fixed value set by WP_Posts_List_Table->user_posts_count
	 * Thus, exclude_premade_layouts_library() action doesn't automatically exclude premade layout and
	 * it has to be late filtered via this exclude_premade_layouts_library_count().
	 *
	 * @see WP_Posts_List_Table->user_posts_count to see how mine post value is retrieved.
	 *
	 * @param array $views All views in post list table.
	 * @return array
	 */
	function exclude_premade_layouts_library_count( $views ) {
		if ( isset( $views['mine'] ) ) {
			$current_user_id = get_current_user_id();

			if ( isset( $_GET['author'] ) && ( $_GET['author'] === $current_user_id ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
				$class = 'current';

				// Reuse current $wp_query global.
				global $wp_query;

				$mine_posts_count = $wp_query->found_posts;
			} else {
				$class = '';

				// Use WP_Query instead of plain MySQL SELECT because the custom field filtering uses
				// GROUP BY which needs FOUND_ROWS() and this has been automatically handled by WP_Query.
				$query = new WP_Query(
					array(
						'post_type'  => 'et_pb_layout',
						'author'     => $current_user_id,
						'meta_query' => array(
							'key'     => '_et_pb_predefined_layout',
							'value'   => 'on',
							'compare' => 'NOT EXISTS',
						),
					)
				);

				$mine_posts_count = $query->found_posts;
			}

			$url = add_query_arg(
				array(
					'post_type' => 'et_pb_layout',
					'author'    => $current_user_id,
				),
				'edit.php'
			);

			$views['mine'] = sprintf(
				'<a href="%1$s" class="%2$s">%3$s <span class="count">(%4$s)</span></a>',
				esc_url( $url ),
				esc_attr( $class ),
				esc_html__( 'Mine', 'et_builder' ),
				esc_html( intval( $mine_posts_count ) )
			);
		}

		return $views;
	}
endif;
add_filter( 'views_edit-et_pb_layout', 'exclude_premade_layouts_library_count' );


if ( ! function_exists( 'et_pb_get_standard_post_types' ) ) :
	/**
	 * Returns the standard '_et_pb_built_for_post_type' post types.
	 *
	 * @deprecated {@see ET_Builder_Post_Type_Layout::get_built_for_post_types()}
	 *
	 * @since 3.1  Deprecated.
	 * @since 1.8
	 *
	 * @return string[]
	 */
	function et_pb_get_standard_post_types() {
		return ET_Builder_Library::built_for_post_types();
	}
endif;

if ( ! function_exists( 'et_pb_get_used_built_for_post_types' ) ) :
	/**
	 * Returns all current '_et_pb_built_for_post_type' post types.
	 *
	 * @deprecated {@see ET_Builder_Post_Type_Layout::get_built_for_post_types()}
	 *
	 * @since 3.1  Deprecated.
	 * @since 1.8
	 *
	 * @return string[]
	 */
	function et_pb_get_used_built_for_post_types() {
		return ET_Builder_Library::built_for_post_types( 'all' );
	}
endif;

if ( ! function_exists( 'et_pb_get_font_icon_symbols' ) ) :
	/**
	 * Return fon icon symbols.
	 */
	function et_pb_get_font_icon_symbols() {
		$symbols = array( '&amp;#x21;', '&amp;#x22;', '&amp;#x23;', '&amp;#x24;', '&amp;#x25;', '&amp;#x26;', '&amp;#x27;', '&amp;#x28;', '&amp;#x29;', '&amp;#x2a;', '&amp;#x2b;', '&amp;#x2c;', '&amp;#x2d;', '&amp;#x2e;', '&amp;#x2f;', '&amp;#x30;', '&amp;#x31;', '&amp;#x32;', '&amp;#x33;', '&amp;#x34;', '&amp;#x35;', '&amp;#x36;', '&amp;#x37;', '&amp;#x38;', '&amp;#x39;', '&amp;#x3a;', '&amp;#x3b;', '&amp;#x3c;', '&amp;#x3d;', '&amp;#x3e;', '&amp;#x3f;', '&amp;#x40;', '&amp;#x41;', '&amp;#x42;', '&amp;#x43;', '&amp;#x44;', '&amp;#x45;', '&amp;#x46;', '&amp;#x47;', '&amp;#x48;', '&amp;#x49;', '&amp;#x4a;', '&amp;#x4b;', '&amp;#x4c;', '&amp;#x4d;', '&amp;#x4e;', '&amp;#x4f;', '&amp;#x50;', '&amp;#x51;', '&amp;#x52;', '&amp;#x53;', '&amp;#x54;', '&amp;#x55;', '&amp;#x56;', '&amp;#x57;', '&amp;#x58;', '&amp;#x59;', '&amp;#x5a;', '&amp;#x5b;', '&amp;#x5c;', '&amp;#x5d;', '&amp;#x5e;', '&amp;#x5f;', '&amp;#x60;', '&amp;#x61;', '&amp;#x62;', '&amp;#x63;', '&amp;#x64;', '&amp;#x65;', '&amp;#x66;', '&amp;#x67;', '&amp;#x68;', '&amp;#x69;', '&amp;#x6a;', '&amp;#x6b;', '&amp;#x6c;', '&amp;#x6d;', '&amp;#x6e;', '&amp;#x6f;', '&amp;#x70;', '&amp;#x71;', '&amp;#x72;', '&amp;#x73;', '&amp;#x74;', '&amp;#x75;', '&amp;#x76;', '&amp;#x77;', '&amp;#x78;', '&amp;#x79;', '&amp;#x7a;', '&amp;#x7b;', '&amp;#x7c;', '&amp;#x7d;', '&amp;#x7e;', '&amp;#xe000;', '&amp;#xe001;', '&amp;#xe002;', '&amp;#xe003;', '&amp;#xe004;', '&amp;#xe005;', '&amp;#xe006;', '&amp;#xe007;', '&amp;#xe009;', '&amp;#xe00a;', '&amp;#xe00b;', '&amp;#xe00c;', '&amp;#xe00d;', '&amp;#xe00e;', '&amp;#xe00f;', '&amp;#xe010;', '&amp;#xe011;', '&amp;#xe012;', '&amp;#xe013;', '&amp;#xe014;', '&amp;#xe015;', '&amp;#xe016;', '&amp;#xe017;', '&amp;#xe018;', '&amp;#xe019;', '&amp;#xe01a;', '&amp;#xe01b;', '&amp;#xe01c;', '&amp;#xe01d;', '&amp;#xe01e;', '&amp;#xe01f;', '&amp;#xe020;', '&amp;#xe021;', '&amp;#xe022;', '&amp;#xe023;', '&amp;#xe024;', '&amp;#xe025;', '&amp;#xe026;', '&amp;#xe027;', '&amp;#xe028;', '&amp;#xe029;', '&amp;#xe02a;', '&amp;#xe02b;', '&amp;#xe02c;', '&amp;#xe02d;', '&amp;#xe02e;', '&amp;#xe02f;', '&amp;#xe030;', '&amp;#xe103;', '&amp;#xe0ee;', '&amp;#xe0ef;', '&amp;#xe0e8;', '&amp;#xe0ea;', '&amp;#xe101;', '&amp;#xe107;', '&amp;#xe108;', '&amp;#xe102;', '&amp;#xe106;', '&amp;#xe0eb;', '&amp;#xe010;', '&amp;#xe105;', '&amp;#xe0ed;', '&amp;#xe100;', '&amp;#xe104;', '&amp;#xe0e9;', '&amp;#xe109;', '&amp;#xe0ec;', '&amp;#xe0fe;', '&amp;#xe0f6;', '&amp;#xe0fb;', '&amp;#xe0e2;', '&amp;#xe0e3;', '&amp;#xe0f5;', '&amp;#xe0e1;', '&amp;#xe0ff;', '&amp;#xe031;', '&amp;#xe032;', '&amp;#xe033;', '&amp;#xe034;', '&amp;#xe035;', '&amp;#xe036;', '&amp;#xe037;', '&amp;#xe038;', '&amp;#xe039;', '&amp;#xe03a;', '&amp;#xe03b;', '&amp;#xe03c;', '&amp;#xe03d;', '&amp;#xe03e;', '&amp;#xe03f;', '&amp;#xe040;', '&amp;#xe041;', '&amp;#xe042;', '&amp;#xe043;', '&amp;#xe044;', '&amp;#xe045;', '&amp;#xe046;', '&amp;#xe047;', '&amp;#xe048;', '&amp;#xe049;', '&amp;#xe04a;', '&amp;#xe04b;', '&amp;#xe04c;', '&amp;#xe04d;', '&amp;#xe04e;', '&amp;#xe04f;', '&amp;#xe050;', '&amp;#xe051;', '&amp;#xe052;', '&amp;#xe053;', '&amp;#xe054;', '&amp;#xe055;', '&amp;#xe056;', '&amp;#xe057;', '&amp;#xe058;', '&amp;#xe059;', '&amp;#xe05a;', '&amp;#xe05b;', '&amp;#xe05c;', '&amp;#xe05d;', '&amp;#xe05e;', '&amp;#xe05f;', '&amp;#xe060;', '&amp;#xe061;', '&amp;#xe062;', '&amp;#xe063;', '&amp;#xe064;', '&amp;#xe065;', '&amp;#xe066;', '&amp;#xe067;', '&amp;#xe068;', '&amp;#xe069;', '&amp;#xe06a;', '&amp;#xe06b;', '&amp;#xe06c;', '&amp;#xe06d;', '&amp;#xe06e;', '&amp;#xe06f;', '&amp;#xe070;', '&amp;#xe071;', '&amp;#xe072;', '&amp;#xe073;', '&amp;#xe074;', '&amp;#xe075;', '&amp;#xe076;', '&amp;#xe077;', '&amp;#xe078;', '&amp;#xe079;', '&amp;#xe07a;', '&amp;#xe07b;', '&amp;#xe07c;', '&amp;#xe07d;', '&amp;#xe07e;', '&amp;#xe07f;', '&amp;#xe080;', '&amp;#xe081;', '&amp;#xe082;', '&amp;#xe083;', '&amp;#xe084;', '&amp;#xe085;', '&amp;#xe086;', '&amp;#xe087;', '&amp;#xe088;', '&amp;#xe089;', '&amp;#xe08a;', '&amp;#xe08b;', '&amp;#xe08c;', '&amp;#xe08d;', '&amp;#xe08e;', '&amp;#xe08f;', '&amp;#xe090;', '&amp;#xe091;', '&amp;#xe092;', '&amp;#xe0f8;', '&amp;#xe0fa;', '&amp;#xe0e7;', '&amp;#xe0fd;', '&amp;#xe0e4;', '&amp;#xe0e5;', '&amp;#xe0f7;', '&amp;#xe0e0;', '&amp;#xe0fc;', '&amp;#xe0f9;', '&amp;#xe0dd;', '&amp;#xe0f1;', '&amp;#xe0dc;', '&amp;#xe0f3;', '&amp;#xe0d8;', '&amp;#xe0db;', '&amp;#xe0f0;', '&amp;#xe0df;', '&amp;#xe0f2;', '&amp;#xe0f4;', '&amp;#xe0d9;', '&amp;#xe0da;', '&amp;#xe0de;', '&amp;#xe0e6;', '&amp;#xe093;', '&amp;#xe094;', '&amp;#xe095;', '&amp;#xe096;', '&amp;#xe097;', '&amp;#xe098;', '&amp;#xe099;', '&amp;#xe09a;', '&amp;#xe09b;', '&amp;#xe09c;', '&amp;#xe09d;', '&amp;#xe09e;', '&amp;#xe09f;', '&amp;#xe0a0;', '&amp;#xe0a1;', '&amp;#xe0a2;', '&amp;#xe0a3;', '&amp;#xe0a4;', '&amp;#xe0a5;', '&amp;#xe0a6;', '&amp;#xe0a7;', '&amp;#xe0a8;', '&amp;#xe0a9;', '&amp;#xe0aa;', '&amp;#xe0ab;', '&amp;#xe0ac;', '&amp;#xe0ad;', '&amp;#xe0ae;', '&amp;#xe0af;', '&amp;#xe0b0;', '&amp;#xe0b1;', '&amp;#xe0b2;', '&amp;#xe0b3;', '&amp;#xe0b4;', '&amp;#xe0b5;', '&amp;#xe0b6;', '&amp;#xe0b7;', '&amp;#xe0b8;', '&amp;#xe0b9;', '&amp;#xe0ba;', '&amp;#xe0bb;', '&amp;#xe0bc;', '&amp;#xe0bd;', '&amp;#xe0be;', '&amp;#xe0bf;', '&amp;#xe0c0;', '&amp;#xe0c1;', '&amp;#xe0c2;', '&amp;#xe0c3;', '&amp;#xe0c4;', '&amp;#xe0c5;', '&amp;#xe0c6;', '&amp;#xe0c7;', '&amp;#xe0c8;', '&amp;#xe0c9;', '&amp;#xe0ca;', '&amp;#xe0cb;', '&amp;#xe0cc;', '&amp;#xe0cd;', '&amp;#xe0ce;', '&amp;#xe0cf;', '&amp;#xe0d0;', '&amp;#xe0d1;', '&amp;#xe0d2;', '&amp;#xe0d3;', '&amp;#xe0d4;', '&amp;#xe0d5;', '&amp;#xe0d6;', '&amp;#xe0d7;', '&amp;#xe600;', '&amp;#xe601;', '&amp;#xe602;', '&amp;#xe603;', '&amp;#xe604;', '&amp;#xe605;', '&amp;#xe606;', '&amp;#xe607;', '&amp;#xe608;', '&amp;#xe609;', '&amp;#xe60a;', '&amp;#xe60b;', '&amp;#xe60c;', '&amp;#xe60d;', '&amp;#xe60e;', '&amp;#xe60f;', '&amp;#xe610;', '&amp;#xe611;', '&amp;#xe612;', '&amp;#xe008;' );

		$symbols = apply_filters( 'et_pb_font_icon_symbols', $symbols );

		return $symbols;
	}
endif;

if ( ! function_exists( 'et_pb_get_font_icon_list' ) ) :
	/**
	 * Font icon list.
	 */
	function et_pb_get_font_icon_list() {
		$output = is_customize_preview() ? et_pb_get_font_icon_list_items() : '<%= window.et_builder.font_icon_list_template() %>';

		$output = sprintf( '<ul class="et_font_icon">%1$s</ul>', et_core_esc_previously( $output ) );

		return $output;
	}
endif;

if ( ! function_exists( 'et_pb_get_svg_icons_list' ) ) :
	/**
	 * Return svg icons list.
	 */
	function et_pb_get_svg_icons_list() {
		$all_icons = array(
			'add'                 =>
				'<g>
				<path d="M18 13h-3v-3a1 1 0 0 0-2 0v3h-3a1 1 0 0 0 0 2h3v3a1 1 0 0 0 2 0v-3h3a1 1 0 0 0 0-2z" fillRule="evenodd"></path>
			</g>',
			'delete'              =>
				'<g>
				<path d="M19 9h-3V8a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v1H9a1 1 0 1 0 0 2h10a1 1 0 0 0 .004-2H19zM9 20c.021.543.457.979 1 1h8c.55-.004.996-.45 1-1v-7H9v7zm2.02-4.985h2v4h-2v-4zm4 0h2v4h-2v-4z" fillRule="evenodd"></path>
			</g>',
			'setting'             =>
				'<g>
				<path d="M20.426 13.088l-1.383-.362a.874.874 0 0 1-.589-.514l-.043-.107a.871.871 0 0 1 .053-.779l.721-1.234a.766.766 0 0 0-.116-.917 6.682 6.682 0 0 0-.252-.253.768.768 0 0 0-.917-.116l-1.234.722a.877.877 0 0 1-.779.053l-.107-.044a.87.87 0 0 1-.513-.587l-.362-1.383a.767.767 0 0 0-.73-.567h-.358a.768.768 0 0 0-.73.567l-.362 1.383a.878.878 0 0 1-.513.589l-.107.044a.875.875 0 0 1-.778-.054l-1.234-.722a.769.769 0 0 0-.918.117c-.086.082-.17.166-.253.253a.766.766 0 0 0-.115.916l.721 1.234a.87.87 0 0 1 .053.779l-.043.106a.874.874 0 0 1-.589.514l-1.382.362a.766.766 0 0 0-.567.731v.357a.766.766 0 0 0 .567.731l1.383.362c.266.07.483.26.588.513l.043.107a.87.87 0 0 1-.053.779l-.721 1.233a.767.767 0 0 0 .115.917c.083.087.167.171.253.253a.77.77 0 0 0 .918.116l1.234-.721a.87.87 0 0 1 .779-.054l.107.044a.878.878 0 0 1 .513.589l.362 1.383a.77.77 0 0 0 .731.567h.356a.766.766 0 0 0 .73-.567l.362-1.383a.878.878 0 0 1 .515-.589l.107-.044a.875.875 0 0 1 .778.054l1.234.721c.297.17.672.123.917-.117.087-.082.171-.166.253-.253a.766.766 0 0 0 .116-.917l-.721-1.234a.874.874 0 0 1-.054-.779l.044-.107a.88.88 0 0 1 .589-.513l1.383-.362a.77.77 0 0 0 .567-.731v-.357a.772.772 0 0 0-.569-.724v-.005zm-6.43 3.9a2.986 2.986 0 1 1 2.985-2.986 3 3 0 0 1-2.985 2.987v-.001z" fillRule="evenodd"></path>
			</g>',
			'background-color'    =>
				'<g>
				<path d="M19.4 14.6c0 0-1.5 3.1-1.5 4.4 0 0.9 0.7 1.6 1.5 1.6 0.8 0 1.5-0.7 1.5-1.6C20.9 17.6 19.4 14.6 19.4 14.6zM19.3 12.8l-4.8-4.8c-0.2-0.2-0.4-0.3-0.6-0.3 -0.3 0-0.5 0.1-0.7 0.3l-1.6 1.6L9.8 7.8c-0.4-0.4-1-0.4-1.4 0C8 8.1 8 8.8 8.4 9.1l1.8 1.8 -2.8 2.8c-0.4 0.4-0.4 1-0.1 1.4l4.6 4.6c0.2 0.2 0.4 0.3 0.6 0.3 0.3 0 0.5-0.1 0.7-0.3l6.1-6.1C19.5 13.4 19.5 13.1 19.3 12.8zM15.6 14.6c-1.7 1.7-4.5 1.7-6.2 0l2.1-2.1 1 1c0.4 0.4 1 0.4 1.4 0 0.4-0.4 0.4-1 0-1.4l-1-1 0.9-0.9 3.1 3.1L15.6 14.6z" fillRule="evenodd"></path>
			</g>',
			'background-image'    =>
				'<g>
				<path d="M22.9 7.5c-0.1-0.3-0.5-0.6-0.8-0.6H5.9c-0.4 0-0.7 0.2-0.8 0.6C5.1 7.6 5 7.7 5 7.9v12.2c0 0.1 0 0.2 0.1 0.4 0.1 0.3 0.5 0.5 0.8 0.6h16.2c0.4 0 0.7-0.2 0.8-0.6 0-0.1 0.1-0.2 0.1-0.4V7.9C23 7.7 23 7.6 22.9 7.5zM21 18.9H7v-10h14V18.9z" fillRule="evenodd"></path>
				<circle cx="10.5" cy="12.4" r="1.5"></circle>
				<polygon points="15 16.9 13 13.9 11 16.9 "></polygon>
				<polygon points="17 10.9 15 16.9 19 16.9 "></polygon>
			</g>',
			'background-gradient' =>
				'<g>
				<path d="M22.9 7.5c-0.1-0.3-0.5-0.6-0.8-0.6H5.9c-0.4 0-0.7 0.2-0.8 0.6C5.1 7.6 5 7.7 5 7.9v12.2c0 0.1 0 0.2 0.1 0.4 0.1 0.3 0.5 0.5 0.8 0.6h16.2c0.4 0 0.7-0.2 0.8-0.6 0-0.1 0.1-0.2 0.1-0.4V7.9C23 7.7 23 7.6 22.9 7.5zM21 18.9L7 8.9h14V18.9z" fillRule="evenodd"></path>
			</g>',
			'background-video'    =>
				'<g>
				<path d="M22.9 7.5c-0.1-0.3-0.5-0.6-0.8-0.6H5.9c-0.4 0-0.7 0.2-0.8 0.6C5.1 7.6 5 7.7 5 7.9v12.2c0 0.1 0 0.2 0.1 0.4 0.1 0.3 0.5 0.5 0.8 0.6h16.2c0.4 0 0.7-0.2 0.8-0.6 0-0.1 0.1-0.2 0.1-0.4V7.9C23 7.7 23 7.6 22.9 7.5zM21 18.9H7v-10h14V18.9z" fillRule="evenodd"></path>
				<polygon points="13 10.9 13 16.9 17 13.9 "></polygon>
			</g>',
			'swap'                =>
				'<g>
				<path d="M19 12h-3V9c0-0.5-0.5-1-1-1H8C7.5 8 7 8.5 7 9v7c0 0.5 0.5 1 1 1h3v3c0 0.5 0.5 1 1 1h7c0.5 0 1-0.5 1-1v-7C20 12.5 19.5 12 19 12zM18 19h-5v-2h2c0.5 0 1-0.5 1-1v-2h2V19z" fillRule="evenodd"></path>
			</g>',
			'none'                =>
				'<g>
				<path d="M14 24c5.5 0 10-4.5 10-10S19.5 4 14 4 4 8.5 4 14s4.5 10 10 10zm0-17.5c4.1 0 7.5 3.4 7.5 7.5 0 1.5-.5 2.9-1.2 4.1L9.9 7.7c1.2-.7 2.6-1.2 4.1-1.2zM7.7 9.9l10.4 10.4c-1.2.8-2.6 1.2-4.1 1.2-4.1 0-7.5-3.4-7.5-7.5 0-1.5.5-2.9 1.2-4.1z"></path>
			</g>',
			'animation-none'      =>
				'<g>
				<path d="M14 24c5.5 0 10-4.5 10-10S19.5 4 14 4 4 8.5 4 14s4.5 10 10 10zm0-17.5c4.1 0 7.5 3.4 7.5 7.5 0 1.5-.5 2.9-1.2 4.1L9.9 7.7c1.2-.7 2.6-1.2 4.1-1.2zM7.7 9.9l10.4 10.4c-1.2.8-2.6 1.2-4.1 1.2-4.1 0-7.5-3.4-7.5-7.5 0-1.5.5-2.9 1.2-4.1z"></path>
			</g>',
			'animation-fade'      =>
				'<g>
				<circle cx="8.5" cy="19.5" r="1.5"></circle>
				<circle cx="8.5" cy="14.5" r="1.5"></circle>
				<circle cx="5" cy="12" r="1"></circle>
				<circle cx="5" cy="17" r="1"></circle>
				<circle cx="8.5" cy="9.5" r="1.5"></circle>
				<path d="M15.7 4c-.4 0-.8.1-1.2.3-.6.3-.5.7-1.5.7-1.1 0-2 .9-2 2s.9 2 2 2c.3 0 .5.2.5.5s-.2.5-.5.5c-1.1 0-2 .9-2 2s.9 2 2 2c.3 0 .5.2.5.5s-.2.5-.5.5c-1.1 0-2 .9-2 2s.9 2 2 2c.3 0 .5.2.5.5s-.2.5-.5.5c-1.1 0-2 .9-2 2s.9 2 2 2c1 0 .9.4 1.4.7.4.2.8.3 1.2.3 4.3-.4 8.3-5.3 8.3-10.5s-4-10-8.2-10.5z"></path>
			</g>',
			'animation-slide'     =>
				'<g>
				<path d="M22 4h-5c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h5c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM10 14c0 .6.4 1 1 1h.6L10 16.6c-.4.4-.4 1 0 1.4.4.4 1 .4 1.4 0l3.3-3.3c.2-.2.3-.5.3-.7s-.1-.5-.3-.7L11.4 10c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l1.6 1.6H11c-.6 0-1 .4-1 1z"></path>
				<circle cx="7" cy="14" r="1.5"></circle>
				<circle cx="3" cy="14" r="1"></circle>
			</g>',
			'animation-bounce'    =>
				'<g>
				<circle cx="21.5" cy="8.5" r="3.5"></circle>
				<circle cx="16" cy="12" r="1.7"></circle>
				<circle cx="13" cy="15" r="1.2"></circle>
				<circle cx="11" cy="18" r="1"></circle>
				<circle cx="9" cy="22" r="1"></circle>
				<circle cx="7" cy="19" r="1"></circle>
				<circle cx="4" cy="17" r="1"></circle>
			</g>',
			'animation-zoom'      =>
				'<g>
				<path d="M23.7 4.3c-.1-.1-.2-.2-.3-.2-.1-.1-.3-.1-.4-.1h-5c-.6 0-1 .4-1 1s.4 1 1 1h2.6l-3.1 3.1c-.2-.1-.3-.1-.5-.1h-6c-.2 0-.3 0-.5.1L7.4 6H10c.6 0 1-.4 1-1s-.4-1-1-1H5c-.1 0-.3 0-.4.1-.2.1-.4.3-.5.5-.1.1-.1.3-.1.4v5c0 .6.4 1 1 1s1-.4 1-1V7.4l3.1 3.1c-.1.2-.1.3-.1.5v6c0 .2 0 .3.1.5L6 20.6V18c0-.6-.4-1-1-1s-1 .4-1 1v5c0 .1 0 .3.1.4.1.2.3.4.5.5.1.1.3.1.4.1h5c.6 0 1-.4 1-1s-.4-1-1-1H7.4l3.1-3.1c.2 0 .3.1.5.1h6c.2 0 .3 0 .5-.1l3.1 3.1H18c-.6 0-1 .4-1 1s.4 1 1 1h5c.1 0 .3 0 .4-.1.2-.1.4-.3.5-.5.1-.1.1-.3.1-.4v-5c0-.6-.4-1-1-1s-1 .4-1 1v2.6l-3.1-3.1c0-.2.1-.3.1-.5v-6c0-.2 0-.3-.1-.5L22 7.4V10c0 .6.4 1 1 1s1-.4 1-1V5c0-.1 0-.3-.1-.4 0-.1-.1-.2-.2-.3z"></path>
			</g>',
			'animation-flip'      =>
				'<g>
				<path d="M22 2.4l-7 2.9V7h-2v-.8L7.6 8.7c-.4.2-.6.5-.6.9v8.7c0 .4.2.7.6.9l5.4 2.5V21h2v1.7l7 2.9c.5.2 1-.2 1-.7V3.1c0-.5-.5-.9-1-.7zM15 19h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V9h2v2zM13 2h2v2.5h-2zM13 23.5h2V26h-2z"></path>
			</g>',
			'animation-fold'      =>
				'<g>
				<path d="M24 7h-4V3.4c0-.8-.6-1.4-1.3-1.4-.2 0-.5.1-.7.2l-6.5 3.9c-.9.6-1.5 1.6-1.5 2.6V23c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2zm-6 10.5c0 .2-.1.4-.3.5L12 21.5V8.7c0-.4.2-.7.5-.9L18 4.5v13zM6 7h2v2H6zM6 23h2v2H6zM2.6 7.1c-.1 0-.1.1-.2.1v.1l-.1.1-.1.1c-.1.1-.2.3-.2.5v1h2V7H3c-.1 0-.2 0-.4.1zM2 23v1c0 .4.3.8.7.9.1.1.2.1.3.1h1v-2H2zM2 11h2v2H2zM2 19h2v2H2zM2 15h2v2H2z"></path>
			</g>',
			'animation-roll'      =>
				'<g>
				<path d="M18.8 5c-5.3-2.7-11.8.2-14 5.6-1.1 2.8-1 6 .2 8.8.4 1 3.9 6.5 5 3.6.5-1.2-1.3-2.2-1.9-3-.8-1.2-1.4-2.5-1.6-3.9-.4-2.7.5-5.5 2.4-7.4 4-4 11.6-2.5 12.6 3.4.4 2.7-.9 5.5-3.4 6.6-2.6 1.1-6 0-6.8-2.8-.7-2.4 1.2-5.7 4-4.8 1.1.3 2 1.5 1.5 2.7-.3.7-1.7 1.2-1.6.1 0-.3.2-.4.2-.8-.1-.4-.5-.6-.9-.6-1.1.1-1.6 1.6-1.3 2.5.3 1.2 1.5 1.9 2.7 1.9 2.9 0 4.2-3.4 3.1-5.7-1.2-2.6-4.6-3.4-7-2.2-2.6 1.3-3.8 4.4-3.1 7.2 1.6 5.9 9.3 6.8 13.1 2.5 3.8-4.2 1.9-11.1-3.2-13.7z"></path>
			</g>',
			'border-link'         =>
				'<g>
 				<path d="M14.71 17.71a3 3 0 0 1-2.12-.88l-.71-.71a1 1 0 0 1 1.41-1.41l.71.71a1 1 0 0 0 1.41 0l5-4.95a1 1 0 0 0 0-1.41l-1.46-1.42a1 1 0 0 0-1.41 0L16.1 9.07a1 1 0 0 1-1.41-1.41l1.43-1.43a3.07 3.07 0 0 1 4.24 0l1.41 1.41a3 3 0 0 1 0 4.24l-5 4.95a3 3 0 0 1-2.06.88z"></path>
 				<path d="M9.76 22.66a3 3 0 0 1-2.12-.88l-1.42-1.42a3 3 0 0 1 0-4.24l5-4.95a3.07 3.07 0 0 1 4.24 0l.71.71a1 1 0 0 1-1.41 1.41l-.76-.7a1 1 0 0 0-1.41 0l-5 4.95a1 1 0 0 0 0 1.41L9 20.36a1 1 0 0 0 1.41 0L11.82 19a1 1 0 0 1 1.41 1.41l-1.36 1.36a3 3 0 0 1-2.11.89z"></path>
 			</g>',
			'border-all'          =>
				'<g>
 				<path d="M22 5H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1zm-2 15H8V8h12z"></path>
 			</g>',
			'border-top'          =>
				'<g>
 				<path d="M17 21h2v2h-2zM5 9h2v2H5zM21 17h2v2h-2zM21 9h2v2h-2zM21 13h2v2h-2zM21 23h1a1 1 0 0 0 1-1v-1h-2zM5 17h2v2H5zM5 13h2v2H5zM13 21h2v2h-2zM9 21h2v2H9zM5 21v1a1 1 0 0 0 1 1h1v-2zM22 5H6a1 1 0 0 0-1 1v2h18V6a1 1 0 0 0-1-1z"></path>
 			</g>',
			'border-right'        =>
				'<g>
 				<path d="M13 5h2v2h-2zM5 9h2v2H5zM9 5h2v2H9zM7 5H6a1 1 0 0 0-1 1v1h2zM5 13h2v2H5zM13 21h2v2h-2zM5 17h2v2H5zM9 21h2v2H9zM17 5h2v2h-2zM5 21v1a1 1 0 0 0 1 1h1v-2zM22 5h-2v18h2a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1zM17 21h2v2h-2z"></path>
 			</g>',
			'border-bottom'       =>
				'<g>
 				<path d="M9 5h2v2H9zM7 20H5v2a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1v-2H7zM17 5h2v2h-2zM5 13h2v2H5zM5 9h2v2H5zM13 5h2v2h-2zM5 17h2v2H5zM21 9h2v2h-2zM21 17h2v2h-2zM22 5h-1v2h2V6a1 1 0 0 0-1-1zM21 13h2v2h-2zM7 5H6a1 1 0 0 0-1 1v1h2z"></path>
 			</g>',
			'border-left'         =>
				'<g>
 				<path d="M22 5h-1v2h2V6a1 1 0 0 0-1-1zM9 21h2v2H9zM21 17h2v2h-2zM13 21h2v2h-2zM21 13h2v2h-2zM9 5h2v2H9zM17 21h2v2h-2zM17 5h2v2h-2zM21 9h2v2h-2zM8 7V5H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h2V7zM21 23h1a1 1 0 0 0 1-1v-1h-2zM13 5h2v2h-2z"></path>
 			</g>',
			'lock'                =>
				'<g>
				<path d="M20 12C19.9 11.7 19.3 11 19 11L18 11C18 8.1 16.2 6 14 6 11.8 6 10 8.1 10 11L9 11C8.6 11 8.1 11.6 8 12L8 13 8 19 8 20C8.1 20.3 8.7 20.9 9 21L19 21C19.4 21 19.9 20.4 20 20L20 19 20 14 20 12 20 12ZM14 8C15.1 8 16 9.4 16 11.1L12 11.1C12 9.4 12.9 8 14 8L14 8ZM18 19L10 19 10 13 18 13 18 19 18 19Z" fillRule="evenodd"></path>
				<path d="M14 18C14.6 18 15 17.6 15 17L15 15C15 14.4 14.6 14 14 14 13.4 14 13 14.4 13 15L13 15 13 17C13 17.6 13.4 18 14 18L14 18Z" fillRule="evenodd"></path>
			</g>',
		);
		return $all_icons;
	}
endif;

if ( ! function_exists( 'et_pb_get_font_icon_list_items' ) ) :
	/**
	 * Return Font icon list items.
	 */
	function et_pb_get_font_icon_list_items() {
		$output = '';

		$symbols = et_pb_get_font_icon_symbols();

		foreach ( $symbols as $symbol ) {
			$output .= sprintf( '<li data-icon=\'%1$s\'></li>', esc_attr( $symbol ) );
		}

		return $output;
	}
endif;

if ( ! function_exists( 'et_pb_font_icon_list' ) ) :
	/**
	 * Display font icon list.
	 */
	function et_pb_font_icon_list() {
		echo et_core_esc_previously( et_pb_get_font_icon_list() );
	}
endif;

if ( ! function_exists( 'et_pb_get_font_down_icon_symbols' ) ) :
	/**
	 * Return icon to display for the scroll down button.
	 */
	function et_pb_get_font_down_icon_symbols() {
		$symbols = array( '&amp;#x22;', '&amp;#x33;', '&amp;#x37;', '&amp;#x3b;', '&amp;#x3f;', '&amp;#x43;', '&amp;#x47;', '&amp;#xe03a;', '&amp;#xe044;', '&amp;#xe048;', '&amp;#xe04c;' );

		return $symbols;
	}
endif;

if ( ! function_exists( 'et_pb_get_font_down_icon_list' ) ) :
	/**
	 * Return font down icon list.
	 */
	function et_pb_get_font_down_icon_list() {
		$output = is_customize_preview() ? et_pb_get_font_down_icon_list_items() : '<%= window.et_builder.font_down_icon_list_template() %>';

		$output = sprintf( '<ul class="et_font_icon">%1$s</ul>', et_core_esc_previously( $output ) );

		return $output;
	}
endif;

if ( ! function_exists( 'et_pb_get_font_down_icon_list_items' ) ) :
	/**
	 * Return font down icon list items.
	 */
	function et_pb_get_font_down_icon_list_items() {
		$output = '';

		$symbols = et_pb_get_font_down_icon_symbols();

		foreach ( $symbols as $symbol ) {
			$output .= sprintf( '<li data-icon="%1$s"></li>', esc_attr( $symbol ) );
		}

		return $output;
	}
endif;

if ( ! function_exists( 'et_pb_font_down_icon_list' ) ) :
	/**
	 * Return icon to display for the scroll down button.
	 */
	function et_pb_font_down_icon_list() {
		echo et_core_esc_previously( et_pb_get_font_down_icon_list() );
	}
endif;


if ( ! function_exists( 'et_pb_process_font_icon' ) ) :
	/**
	 * Processes font icon value for use on front-end
	 *
	 * @param string $font_icon        Font Icon ( exact value or in %%index_number%% format ).
	 * @param string $symbols_function Optional. Name of the function that gets an array of font icon values.
	 *                                 et_pb_get_font_icon_symbols function is used by default.
	 * @return string $font_icon       Font Icon value
	 */
	function et_pb_process_font_icon( $font_icon, $symbols_function = 'default' ) {

		// Do it if $font_icon is an extended icon.
		if ( et_pb_maybe_extended_icon( $font_icon ) ) {
			return et_pb_get_extended_font_icon_value( $font_icon );
		}

		// the exact font icon value is saved.
		if ( 1 !== preg_match( '/^%%/', trim( $font_icon ) ) ) {
			return $font_icon;
		}

		// the font icon value is saved in the following format: %%index_number%%.
		$icon_index   = (int) str_replace( '%', '', $font_icon );
		$icon_symbols = 'default' === $symbols_function ? et_pb_get_font_icon_symbols() : call_user_func( $symbols_function );
		$font_icon    = isset( $icon_symbols[ $icon_index ] ) ? $icon_symbols[ $icon_index ] : '';

		return $font_icon;
	}
endif;

if ( ! function_exists( 'et_builder_accent_color' ) ) :
	/**
	 * Return an accent color.
	 *
	 * @param string $default_color Default color.
	 */
	function et_builder_accent_color( $default_color = '#7EBEC5' ) {
		// Accent color option exists in Divi theme only. Use default color in plugin.
		$accent_color = ! et_is_builder_plugin_active() ? et_get_option( 'accent_color', $default_color ) : $default_color;

		return apply_filters( 'et_builder_accent_color', $accent_color );
	}
endif;

if ( ! function_exists( 'et_pb_process_header_level' ) ) :
	/**
	 * Process header level.
	 *
	 * @param string $new_level Header level.
	 * @param string $default Default header level.
	 *
	 * @return string
	 */
	function et_pb_process_header_level( $new_level, $default ) {

		$valid_header_levels = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );

		// return the new header level if exists in the list of valid header levels.
		if ( in_array( $new_level, $valid_header_levels, true ) ) {
			return $new_level;
		}

		// return default if defined. Fallback to h2 otherwise.
		return isset( $default ) ? $default : 'h2';
	}
endif;

if ( ! function_exists( 'et_pb_get_alignment' ) ) {
	/**
	 * Return an alignment value from alignment key.
	 *
	 * @param string $key Alignment key.
	 *
	 * @return string
	 */
	function et_pb_get_alignment( $key ) {
		if ( is_rtl() && 'left' === $key ) {
			$key = 'right';
		}

		switch ( $key ) {
			case 'force_left':
				return 'left';
			case 'justified':
				return 'justify';
			default:
				return $key;
		}
	}
}

if ( ! function_exists( 'et_builder_get_text_orientation_options' ) ) :
	/**
	 * Return text orientation options to use in dropdown.
	 *
	 * @param array $exclude_options Options to be excluded.
	 * @param array $include_options Options to be included.
	 */
	function et_builder_get_text_orientation_options( $exclude_options = array(), $include_options = array() ) {
		$text_orientation_options = array(
			'left'      => et_builder_i18n( 'Left' ),
			'center'    => et_builder_i18n( 'Center' ),
			'right'     => et_builder_i18n( 'Right' ),
			'justified' => et_builder_i18n( 'Justified' ),
		);

		if ( is_rtl() ) {
			$text_orientation_options = array(
				'right'      => et_builder_i18n( 'Right' ),
				'center'     => et_builder_i18n( 'Center' ),
				'force_left' => et_builder_i18n( 'Left' ),
			);
		}

		// Exclude some options if needed.
		if ( ! empty( $exclude_options ) ) {
			foreach ( $exclude_options as $exclude ) {
				unset( $text_orientation_options[ $exclude ] );
			}
		}

		// Include some options if needed.
		if ( ! empty( $include_options ) ) {
			$text_orientation_options = wp_parse_args( $include_options, $text_orientation_options );
		}

		return apply_filters( 'et_builder_text_orientation_options', $text_orientation_options );
	}
endif;

if ( ! function_exists( 'et_builder_get_gallery_settings' ) ) :
	/**
	 * Return gallery button.
	 */
	function et_builder_get_gallery_settings() {
		$output = sprintf(
			'<input type="button" class="button button-upload et-pb-gallery-button" value="%1$s" />',
			esc_attr__( 'Update Gallery', 'et_builder' )
		);

		return $output;
	}
endif;

if ( ! function_exists( 'et_builder_get_nav_menus_options' ) ) :
	/**
	 * Return navigation menus options.
	 */
	function et_builder_get_nav_menus_options() {
		$nav_menus_options = array( 'none' => esc_html__( 'Select a menu', 'et_builder' ) );

		$nav_menus = wp_get_nav_menus( array( 'orderby' => 'name' ) );
		foreach ( (array) $nav_menus as $_nav_menu ) {
			$nav_menus_options[ $_nav_menu->term_id ] = $_nav_menu->name;
		}

		return apply_filters( 'et_builder_nav_menus_options', $nav_menus_options );
	}
endif;

if ( ! function_exists( 'et_builder_generate_center_map_setting' ) ) :
	/**
	 * Return center map div.
	 */
	function et_builder_generate_center_map_setting() {
		return '<div id="et_pb_map_center_map" class="et-pb-map et_pb_map_center_map"></div>';
	}
endif;

if ( ! function_exists( 'et_builder_generate_pin_zoom_level_input' ) ) :
	/**
	 * Return map's pin zoom level input element.
	 */
	function et_builder_generate_pin_zoom_level_input() {
		return '<input class="et_pb_zoom_level" type="hidden" value="18" />';
	}
endif;

/**
 * Define conditional tags needed for component's backend parser. This is used for FB's public facing update
 * mechanism to pass conditional tag to admin-ajax.php for component which relies to backend parsing. Backend
 * uses this conditional tags' key as well for sanitization
 *
 * @return array
 */
function et_fb_conditional_tag_params() {
	global $post;

	$post_type = isset( $post->post_type ) ? $post->post_type : false;

	$conditional_tags = array(
		'is_limited_mode'             => et_builder_is_limited_mode(),
		'is_bfb'                      => et_builder_bfb_enabled(),
		'is_bfb_activated'            => et_builder_bfb_activated(),
		'is_tb'                       => et_builder_tb_enabled(),
		'is_front_page'               => is_front_page(),
		'is_home_page'                => is_home() || is_front_page(),
		'is_search'                   => is_search(),
		'is_single'                   => is_single(),
		'is_singular'                 => is_singular(),
		'is_singular_project'         => is_singular( 'project' ),
		'is_rtl'                      => is_rtl(),
		'is_no_rtl'                   => 'on' === et_get_option( 'divi_disable_translations', 'off' ),
		'et_is_builder_plugin_active' => et_is_builder_plugin_active(),
		'is_user_logged_in'           => is_user_logged_in(),
		'et_is_ab_testing_active'     => et_is_ab_testing_active() ? 'yes' : 'no',
		'is_wrapped_styles'           => et_builder_has_limitation( 'use_wrapped_styles' ),
		'is_gutenberg'                => et_core_is_gutenberg_active(),
		'is_custom_post_type'         => et_builder_is_post_type_custom( $post_type ),
		'is_layout_post_type'         => et_theme_builder_is_layout_post_type( $post_type ),
		'is_rich_editor'              => 'true' === apply_filters( 'user_can_richedit', get_user_option( 'rich_editing' ) ) ? 'yes' : 'no',

		// Pass falsey as empty string so it remains falsey when conditionalTags is fetched and
		// passed string as AJAX payload (on AJAX string, false bool becomes 'false' string).
		'is_layout_block'             => ET_GB_Block_Layout::is_layout_block_preview() ? true : '',
	);

	return apply_filters( 'et_fb_conditional_tag_params', $conditional_tags );
}

if ( ! function_exists( 'et_builder_page_creation_options' ) ) :
	/**
	 * Get Page Creation flow options
	 *
	 * @since 4.2
	 *
	 * @return array
	 */
	function et_builder_page_creation_options() {
		return array(
			'build_from_scratch'    => array(
				'className'       => 'accent-blue',
				'imgSrc'          => 'scratch.png',
				'imgSrcHover'     => 'scratch.gif',
				'titleText'       => esc_html__( 'Build From Scratch', 'et_builder' ),
				'descriptionText' => esc_html__( 'Build your page from the ground up. Don’t worry, you can access our premade layouts at any time.', 'et_builder' ),
				'buttonText'      => esc_html__( 'Start Building', 'et_builder' ),
				'permission'      => array( 'add_module' ),
				'setting'         => array(
					'value_index' => 1,
				),
			),
			'use_existing_content'  => array(
				'className'       => 'accent-blue',
				'imgSrc'          => 'existing.png',
				'imgSrcHover'     => 'existing.gif',
				'titleText'       => esc_html__( 'Use Existing Content', 'et_builder' ),
				'descriptionText' => esc_html__( 'Use the Divi Builder while retaining your existing page content.', 'et_builder' ),
				'buttonText'      => esc_html__( 'Start Building', 'et_builder' ),
				'permission'      => array( 'edit_module' ),
				'setting'         => false,
			),
			'choose_premade_layout' => array(
				'className'       => 'accent-purple',
				'imgSrc'          => 'premade.png',
				'imgSrcHover'     => 'premade.gif',
				'titleText'       => esc_html__( 'Choose a premade Layout', 'et_builder' ),
				'descriptionText' => esc_html__( 'Choose from hundreds of world-class premade layouts or start from any of your existing saved layouts.', 'et_builder' ),
				'buttonText'      => esc_html__( 'Browse Layouts', 'et_builder' ),
				'permission'      => array( 'load_layout', 'divi_library' ),
				'setting'         => array(
					'label'       => esc_html__( 'Load Premade Layout', 'et_builder' ),
					'value_index' => 2,
				),
			),
			'build_with_ai'         => array(
				'className'       => 'accent-dark-blue',
				'imgSrc'          => 'layout-insert-build-with-ai.svg',
				'imgSrcHover'     => 'layout-insert-build-with-ai.svg',
				'titleText'       => esc_html__( 'Build With AI', 'et_builder' ),
				'bannerText'      => esc_html__( 'Brand New', 'et_builder' ),
				'descriptionText' => esc_html__( 'Simply describe your page content, sit back, relax, and let Divi AI build your page with the click of a button.', 'et_builder' ),
				'buttonText'      => esc_html__( 'Generate Layout', 'et_builder' ),
				'permission'      => array( 'divi_ai' ),
				'setting'         => array(
					'value_index' => 3,
				),
			),
		);
	}
endif;

if ( ! function_exists( 'et_builder_page_creation_settings' ) ) :
	/**
	 * Get Page Creation flow setting options
	 *
	 * @since 4.2
	 *
	 * @param bool $value_as_index Flag to set the options value as numeric index.
	 *
	 * @return array
	 */
	function et_builder_page_creation_settings( $value_as_index = false ) {
		$default_label = esc_html__( 'Give Me A Choice', 'et_builder' );

		if ( $value_as_index ) {
			$settings = array(
				$default_label,
			);
		} else {
			$settings = array(
				'default' => $default_label,
			);
		}

		foreach ( et_builder_page_creation_options() as $key => $option ) {
			if ( ! et_()->array_get( $option, 'setting' ) ) {
				continue;
			}

			if ( isset( $option['permission'] ) ) {
				$capabilities = is_array( $option['permission'] ) ? $option['permission'] : explode( ',', $option['permission'] );
				$allowed      = array_filter( $capabilities, 'et_pb_is_allowed' );

				if ( ! $allowed || count( $capabilities ) !== count( $allowed ) ) {
					continue;
				}
			}

			$value = $value_as_index ? $option['setting']['value_index'] : $key;
			$label = et_()->array_get( $option, 'setting.label', $option['titleText'] );

			$settings[ $value ] = $label;
		}

		return $settings;
	}
endif;

/**
 * Return an app preferences.
 *
 * @return mixed|void
 */
function et_fb_app_preferences_settings() {
	$app_preferences = array(
		'settings_bar_location'               => array(
			'type'    => 'string',
			'default' => 'bottom',
			'options' => array(
				'top-left',
				'top',
				'top-right',
				'right',
				'bottom-right',
				'bottom',
				'bottom-left',
				'left',
			),
		),
		'builder_animation'                   => array(
			'type'    => 'bool',
			'default' => true,
		),
		'builder_display_modal_settings'      => array(
			'type'    => 'bool',
			'default' => false,
		),
		'builder_enable_dummy_content'        => array(
			'type'    => 'bool',
			'default' => true,
		),
		'builder_enable_visual_theme_builder' => array(
			'type'    => 'bool',
			'default' => true,
		),
		'event_mode'                          => array(
			'type'    => 'string',
			'default' => 'hover',
			'options' => array(
				'hover' => esc_html__( 'Hover Mode', 'et_builder' ),
				'click' => esc_html__( 'Click Mode', 'et_builder' ),
				'grid'  => esc_html__( 'Grid Mode', 'et_builder' ),
			),
		),
		'view_mode'                           => array(
			'type'    => 'string',
			'default' => et_builder_bfb_enabled() ? 'wireframe' : 'desktop',
			'options' => array(
				'desktop'   => esc_html__( 'Desktop View', 'et_builder' ),
				'tablet'    => esc_html__( 'Tablet View', 'et_builder' ),
				'phone'     => esc_html__( 'Phone View', 'et_builder' ),
				'wireframe' => esc_html__( 'Wireframe View', 'et_builder' ),
			),
		),
		'hide_disabled_modules'               => array(
			'type'    => 'bool',
			'default' => false,
		),
		'history_intervals'                   => array(
			'type'    => 'int',
			'default' => 1,
			'options' => array(
				'1'  => esc_html__( 'After Every Action', 'et_builder' ),
				'10' => esc_html__( 'After Every 10th Action', 'et_builder' ),
				'20' => esc_html__( 'After Every 20th Action', 'et_builder' ),
				'30' => esc_html__( 'After Every 30th Action', 'et_builder' ),
				'40' => esc_html__( 'After Every 40th Action', 'et_builder' ),
			),
		),
		'page_creation_flow'                  => array(
			'type'    => 'string',
			'default' => 'default',
			'options' => et_builder_page_creation_settings(),
		),
		'quick_actions_always_start_with'     => array(
			'type'    => 'string',
			'default' => 'nothing',
		),
		'quick_actions_show_recent_queries'   => array(
			'type'    => 'string',
			'default' => 'off',
		),
		'quick_actions_recent_queries'        => array(
			'type'       => 'string',
			'default'    => '',
			'max_length' => 100,
		),
		'quick_actions_recent_category'       => array(
			'type'       => 'string',
			'default'    => '',
			'max_length' => 100,
		),
		'modal_preference'                    => array(
			'type'    => 'string',
			'default' => 'default',
			'options' => array(
				'default'    => esc_html__( 'Last Used Position', 'et_builder' ),
				'minimum'    => esc_html__( 'Floating Minimum Size', 'et_builder' ),
				'fullscreen' => esc_html__( 'Fullscreen', 'et_builder' ),
				'left'       => esc_html__( 'Fixed Left Sidebar', 'et_builder' ),
				'right'      => esc_html__( 'Fixed Right Sidebar', 'et_builder' ),
				'bottom'     => esc_html__( 'Fixed Bottom Panel', 'et_builder' ),
				// TODO, disabled until further notice (Issue #3930 & #5859)
				// 'top'     => esc_html__( 'Fixed Top Panel', 'et_builder' ),.
			),
		),
		'modal_snap_location'                 => array(
			'type'    => 'string',
			'default' => '',
		),
		'modal_snap'                          => array(
			'type'    => 'bool',
			'default' => false,
		),
		'modal_fullscreen'                    => array(
			'type'    => 'bool',
			'default' => false,
		),
		'modal_dimension_width'               => array(
			'type'    => 'int',
			'default' => 400,
		),
		'modal_dimension_height'              => array(
			'type'    => 'int',
			'default' => 400,
		),
		'modal_position_x'                    => array(
			'type'    => 'int',
			'default' => 30,
		),
		'modal_position_y'                    => array(
			'type'    => 'int',
			'default' => 50,
		),
		'toolbar_click'                       => array(
			'type'    => 'bool',
			'default' => false,
		),
		'toolbar_desktop'                     => array(
			'type'    => 'bool',
			'default' => true,
		),
		'toolbar_grid'                        => array(
			'type'    => 'bool',
			'default' => false,
		),
		'toolbar_hover'                       => array(
			'type'    => 'bool',
			'default' => false,
		),
		'toolbar_phone'                       => array(
			'type'    => 'bool',
			'default' => true,
		),
		'toolbar_tablet'                      => array(
			'type'    => 'bool',
			'default' => true,
		),
		'toolbar_wireframe'                   => array(
			'type'    => 'bool',
			'default' => true,
		),
		'toolbar_zoom'                        => array(
			'type'    => 'bool',
			'default' => true,
		),
		'lv_modal_dimension_height'           => array(
			'type'    => 'int',
			'default' => 0,
		),
		'lv_modal_dimension_width'            => array(
			'type'    => 'int',
			'default' => 0,
		),
		'lv_modal_position_x'                 => array(
			'type'    => 'int',
			'default' => 0,
		),
		'lv_modal_position_y'                 => array(
			'type'    => 'int',
			'default' => 0,
		),
		// Re: "width/height": responsive dimensions presume portrait orientation.
		'responsive_tablet_width'             => array(
			'type'    => 'int',
			'default' => 768,
		),
		'responsive_tablet_height'            => array(
			'type'    => 'int',
			'default' => 0,
		),
		'responsive_phone_width'              => array(
			'type'    => 'int',
			'default' => 400,
		),
		'responsive_phone_height'             => array(
			'type'    => 'int',
			'default' => 0,
		),
		'responsive_minimum_width'            => array(
			'type'    => 'int',
			'default' => 320,
		),
		'responsive_maximum_width'            => array(
			'type'    => 'int',
			'default' => 980,
		),
	);

	return apply_filters( 'et_fb_app_preferences_defaults', $app_preferences );
}

/**
 * Return the preferences list which should not be synced between Visual Builder and Backend Visual Builder.
 *
 * @return mixed|void
 */
function et_fb_unsynced_preferences() {
	/**
	 * Filters the preferences list which should not be synced between Visual Builder and Backend Visual Builder.
	 *
	 * @since 3.18
	 *
	 * @param array
	 */
	return apply_filters( 'et_fb_app_preferences_unsynced', array( 'view_mode', 'toolbar_click', 'toolbar_desktop', 'toolbar_grid', 'toolbar_hover', 'toolbar_phone', 'toolbar_tablet', 'toolbar_wireframe', 'toolbar_zoom', 'modal_preference' ) );
}

/**
 * Return app preferences.
 *
 * @return mixed|void
 */
function et_fb_app_preferences() {
	$app_preferences = et_fb_app_preferences_settings();
	if ( et_is_builder_plugin_active() ) {
		// Since Divi Builder Plugin is always 'limited', need to use a different
		// condition to prefix the options when BFB is used.
		$limited_prefix = et_builder_bfb_enabled() ? 'limited_' : '';
	} else {
		$limited_prefix = et_builder_is_limited_mode() ? 'limited_' : '';
	}

	foreach ( $app_preferences as $preference_key => $preference ) {
		$option_name = 'et_fb_pref_' . $preference_key;

		// Some preferences should not be synced between VB and Limited VB.
		if ( in_array( $preference_key, et_fb_unsynced_preferences(), true ) ) {
			$option_name = 'et_fb_pref_' . $limited_prefix . $preference_key;
		}

		$option_value = et_get_option( $option_name, $preference['default'], '', true );

		// If options available, verify returned value against valid options. Return default if fails.
		if ( isset( $preference['options'] ) ) {
			$options       = $preference['options'];
			$valid_options = isset( $options[0] ) ? $options : array_keys( $options );
			// phpcs:ignore WordPress.PHP.StrictInArray -- $valid_options array has strings and numbers values.
			if ( ! in_array( (string) $option_value, $valid_options ) ) {
				$option_value = $preference['default'];
			}
		}

		// Exceptional preference. Snap left is not supported in Limited mode, so replace it with default.
		if ( '' !== $limited_prefix && 'modal_snap_location' === $preference_key && 'left' === $option_value ) {
			$option_value = $preference['default'];
		}

		$app_preferences[ $preference_key ]['value'] = $option_value;
	}

	return apply_filters( 'et_fb_app_preferences', $app_preferences );
}

/**
 * Woocommerce Components for visual builder
 *
 * @since 4.0.1
 *
 * @return array
 */
function et_fb_current_page_woocommerce_components() {
	$is_product_cpt        = 'product' === get_post_type();
	$is_tb                 = et_builder_tb_enabled();
	$cpt_has_wc_components = $is_product_cpt || $is_tb;
	$has_wc_components     = et_is_woocommerce_plugin_active() && $cpt_has_wc_components;

	if ( $has_wc_components && $is_tb ) {
		// Set upsells ID for upsell module in TB.
		ET_Theme_Builder_Woocommerce_Product_Variable_Placeholder::set_tb_upsells_ids();

		// Force set product's class to ET_Theme_Builder_Woocommerce_Product_Variable_Placeholder in TB.
		add_filter( 'woocommerce_product_class', 'et_theme_builder_wc_product_class' );

		// Set product categories and tags in TB.
		add_filter( 'get_the_terms', 'et_theme_builder_wc_terms', 10, 3 );

		// Use Divi's image placeholder in TB.
		add_filter( 'woocommerce_single_product_image_thumbnail_html', 'et_builder_wc_placeholder_img' );
	}

	$woocommerce_components = ! $has_wc_components ? array() : array(
		'et_pb_wc_add_to_cart'      => ET_Builder_Module_Woocommerce_Add_To_Cart::get_add_to_cart(),
		'et_pb_wc_additional_info'  => ET_Builder_Module_Woocommerce_Additional_Info::get_additional_info(),
		'et_pb_wc_breadcrumb'       => ET_Builder_Module_Woocommerce_Breadcrumb::get_breadcrumb(),
		'et_pb_wc_cart_notice'      => ET_Builder_Module_Woocommerce_Cart_Notice::get_cart_notice(),
		'et_pb_wc_description'      => ET_Builder_Module_Woocommerce_Description::get_description(),
		'et_pb_wc_images'           => ET_Builder_Module_Woocommerce_Images::get_images(),
		'et_pb_wc_meta'             => ET_Builder_Module_Woocommerce_Meta::get_meta(),
		'et_pb_wc_price'            => ET_Builder_Module_Woocommerce_Price::get_price(),
		'et_pb_wc_rating'           => ET_Builder_Module_Woocommerce_Rating::get_rating(),
		'et_pb_wc_reviews'          => ET_Builder_Module_Woocommerce_Reviews::get_reviews_html(),
		'et_pb_wc_stock'            => ET_Builder_Module_Woocommerce_Stock::get_stock(),
		'et_pb_wc_tabs'             => ET_Builder_Module_Woocommerce_Tabs::get_tabs(),
		'et_pb_wc_title'            => ET_Builder_Module_Woocommerce_Title::get_title(),
		'et_pb_wc_related_products' => ET_Builder_Module_Woocommerce_Related_Products::get_related_products(),
		'et_pb_wc_upsells'          => ET_Builder_Module_Woocommerce_Upsells::get_upsells(),
	);

	return $woocommerce_components;
}

/**
 * Before & after components for builder.
 *
 * This method should not be used for anything other than to determine whether a module
 * has before & after components on builder load.
 *
 * @since 4.14.5
 *
 * @return array Components (HTML).
 */
function et_fb_current_page_before_after_components() {
	$modules_components = array();

	// Bail early if current request comes from any Ajax request.
	if ( wp_doing_ajax() ) {
		return $modules_components;
	}

	// Bail early if Module Shortcode Manager class doesn't exist.
	if ( ! class_exists( 'ET_Builder_Module_Shortcode_Manager' ) ) {
		return $modules_components;
	}

	/**
	 * Filters modules list.
	 *
	 * The modules list comes from Shortcode Manager only contains built-in modules
	 * intentionally. 3rd-party modules need to include their module slug and class name
	 * via `et_fb_fetch_before_after_modules_map` filter.
	 *
	 * @param array Modules list.
	 */
	$modules_map = apply_filters( 'et_fb_fetch_before_after_modules_map', ET_Builder_Module_Shortcode_Manager::get_modules_map() );

	// Bail early if components map is empty.
	if ( empty( $modules_map ) ) {
		return $modules_components;
	}

	foreach ( $modules_map as $module_slug => $module_data ) {
		$module_class = et_()->array_get( $module_data, 'classname' );

		// Skip if module class name is not found.
		if ( empty( $module_class ) || ! class_exists( $module_class ) ) {
			continue;
		}

		$module_components = $module_class::get_component_before_after_module( $module_slug, array() );

		// Skip if there is no before & after components.
		$has_components = et_()->array_get( $module_components, 'has_components' );
		if ( true !== $has_components ) {
			continue;
		}

		$modules_components[ $module_slug ] = $module_components;
	}

	return $modules_components;
}

/**
 * Array of WooCommerce Tabs.
 *
 * @since 4.4.2 Fixed fatal error @link https://github.com/elegantthemes/Divi/issues/19404
 * @since 4.4.2 Added Custom Tabs support.
 *
 * @used-by et_fb_current_page_params()
 *
 * @return array
 */
function et_fb_woocommerce_tabs() {
	global $product, $post;

	$old_product = $product;
	$old_post    = $post;
	$is_product  = isset( $product ) && is_a( $product, 'WC_Product' );

	if ( ! $is_product && et_is_woocommerce_plugin_active() ) {
		$product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( 'latest' );

		if ( $product ) {
			$post = get_post( $product->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride -- Overriding global post is safe as original $post has been restored at the end.
		} else {
			$product = $old_product;
			return ET_Builder_Module_Helper_Woocommerce_Modules::get_default_tab_options();
		}
	}

	// On non-product post types, the filter will cause fatal error
	// unless we have global $product set.
	$tabs    = apply_filters( 'woocommerce_product_tabs', array() );
	$options = array();

	foreach ( $tabs as $name => $tab ) {
		$options[ $name ] = array(
			'value' => $name,
			'label' => $tab['title'],
		);
	}

	// Reset global $product.
	$product = $old_product;
	$post    = $old_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride -- Restoring original global $post data.

	return $options;
}

/**
 * Get the category taxonomy associated with a given post type.
 *
 * @since 4.0.6
 *
 * @param string $post_type Post type.
 *
 * @return string|bool
 */
function et_builder_get_category_taxonomy( $post_type ) {
	static $cache = array();

	// Address common cases.
	switch ( $post_type ) {
		case 'page':
			return false;
		case 'post':
			return 'category';
		case 'project':
			return 'project_category';
		case 'product':
			return 'product_cat';
	}

	if ( isset( $cache[ $post_type ] ) ) {
		// Use cached value.
		return $cache[ $post_type ];
	}

	// Unknown post_type, guess the taxonomy.
	$taxonomies = get_object_taxonomies( $post_type, 'names' );

	foreach ( array( 'category', 'cat' ) as $pattern ) {
		$matches = preg_grep( '/' . $pattern . '$/', $taxonomies );
		if ( ! empty( $matches ) ) {
			$cache[ $post_type ] = reset( $matches );
			return $cache[ $post_type ];
		}
	}

	// Tough luck.
	$cache[ $post_type ] = false;
	return $cache[ $post_type ];
}

/**
 * Retrieve a post's category terms as a list with specified format.
 *
 * @since 4.0.6
 *
 * @param string $separator Optional. Separate items using this.
 *
 * @return string|false|WP_Error A list of terms on success, false if there are no terms, WP_Error on failure.
 */
function et_builder_get_the_term_list( $separator = '' ) {
	$id       = get_the_ID();
	$taxonomy = et_builder_get_category_taxonomy( get_post_type( $id ) );

	return $taxonomy ? get_the_term_list( $id, $taxonomy, $before = '', $separator ) : false;
}

/**
 * Define current-page related data that are needed by frontend builder. Backend parser also uses this
 * to sanitize updated value for computed data
 *
 * @return array
 */
function et_fb_current_page_params() {
	global $post, $authordata, $paged;

	// Get current page url.

	$current_url = ( isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ) ? ( is_ssl() ? 'https://' : 'http://' ) . sanitize_text_field( $_SERVER['HTTP_HOST'] ) . sanitize_text_field( $_SERVER['REQUEST_URI'] ) : '';

	// Fallback for preview.
	if ( empty( $authordata ) && isset( $post->post_author ) ) {
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- A fallback to set global $authordata.
		$authordata = get_userdata( $post->post_author );
	}

	// Get comment count.
	$comment_count = isset( $post->ID ) ? get_comments_number( $post->ID ) : 0;

	// WordPress' _n() only supports singular n plural, thus we do comment count to text manually.
	if ( 0 === $comment_count ) {
		$comment_count_text = __( 'No Comments', 'et_builder' );
	} elseif ( 1 === $comment_count ) {
		$comment_count_text = __( '1 Comment', 'et_builder' );
	} else {
		// translators: comments count.
		$comment_count_text = sprintf( __( '%d Comments', 'et_builder' ), $comment_count );
	}

	// Get current page paginated data.
	$et_paged = is_front_page() ? get_query_var( 'page' ) : get_query_var( 'paged' );

	// Get thumbnail size.
	$thumbnail_size = isset( $post->ID ) && 'post' === get_post_type( $post->ID ) && 'et_full_width_page' === get_post_meta( $post->ID, '_et_pb_page_layout', true ) ? 'et-pb-post-main-image-fullwidth-large' : 'large';

	$post_id     = isset( $post->ID ) ? $post->ID : (int) et_()->array_get( $_POST, 'current_page.id' ); // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
	$exclude_woo = wp_doing_ajax() || ! et_is_woocommerce_plugin_active() || 'latest' === ET_Builder_Module_Helper_Woocommerce_Modules::get_product_default();

	$default_categories = array( get_term_by( 'name', 'Uncategorized', 'category' ) );
	$categories         = et_pb_get_post_categories( $post_id, $default_categories );

	// phpcs:ignore
	$block_id = et_()->array_get( $_GET, 'blockId', '' );

	$current_page = array(
		'url'                   => esc_url( $current_url ),
		'permalink'             => esc_url( remove_query_arg( 'et_fb', $current_url ) ),
		'backendBuilderUrl'     => esc_url( sprintf( admin_url( '/post.php?post=%d&action=edit' ), get_the_ID() ) ),
		'id'                    => isset( $post->ID ) ? $post->ID : false,
		'title'                 => esc_html( get_the_title() ),
		'thumbnailUrl'          => isset( $post->ID ) ? esc_url( get_the_post_thumbnail_url( $post->ID, $thumbnail_size ) ) : '',
		'thumbnailId'           => isset( $post->ID ) ? get_post_thumbnail_id( $post->ID ) : '',
		'authorName'            => esc_html( get_the_author() ),
		'authorUrl'             => isset( $authordata->ID ) && isset( $authordata->user_nicename ) ? esc_html( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ) : false,
		// translators: post author name.
		'authorUrlTitle'        => sprintf( esc_html__( 'Posts by %s', 'et_builder' ), get_the_author() ),
		'date'                  => intval( get_the_time( 'U' ) ),
		'categories'            => $categories,
		'commentsPopup'         => esc_html( $comment_count_text ),
		'commentsCount'         => esc_html( $comment_count ),
		'comments_popup_tb'     => esc_html__( '12 Comments', 'et_builder' ),
		'paged'                 => is_front_page() ? $et_paged : $paged,
		'post_modified'         => isset( $post->ID ) ? esc_attr( $post->post_modified ) : '',
		'lang'                  => get_locale(),
		'blockId'               => ET_GB_Block_Layout::is_layout_block_preview() ? sanitize_title( et_()->array_get( $_GET, 'blockId', '' ) ) : '', // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		'langCode'              => get_locale(),
		'page_layout'           => $post_id ? get_post_meta( $post_id, '_et_pb_page_layout', true ) : '',
		'woocommerceComponents' => $exclude_woo ? array() : et_fb_current_page_woocommerce_components(),
		'woocommerceTabs'       => et_builder_tb_enabled() && et_is_woocommerce_plugin_active() ?
			ET_Builder_Module_Helper_Woocommerce_Modules::get_default_tab_options() : et_fb_woocommerce_tabs(),
		'woocommerce'           => array(
			'inactive_module_notice' => esc_html__(
				'WooCommerce must be active for this module to appear',
				'et_builder'
			),
		),
		'beforeAfterComponents' => et_fb_current_page_before_after_components(),
	);

	return apply_filters( 'et_fb_current_page_params', $current_page );
}

/**
 * Ajax Callback :: Process computed property.
 */
function et_pb_process_computed_property() {
	if ( ! isset( $_POST['et_pb_process_computed_property_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_pb_process_computed_property_nonce'] ), 'et_pb_process_computed_property_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	if ( ! isset( $_POST['depends_on'], $_POST['conditional_tags'], $_POST['current_page'] ) ) {
		// Shouldn't even be a possibility, but...
		// Since computing `__page` can exit here too, we need to json_encode the reponse.
		// This is needed in case jQuery migrate is disabled (eg via plugin) otherwise the AJAX success callback
		// won't be executed (because json is malformed).
		die( wp_json_encode( null ) );
	}

	$utils = ET_Core_Data_Utils::instance();

	// phpcs:disable ET.Sniffs.ValidatedSanitizedInput -- Sanitization of following arrays is done at the time of accessing an array values.
	$depends_on       = isset( $_POST['depends_on'] ) ? $_POST['depends_on'] : array();
	$conditional_tags = isset( $_POST['conditional_tags'] ) ? $_POST['conditional_tags'] : array();
	$current_page     = isset( $_POST['current_page'] ) ? $_POST['current_page'] : array();
	// phpcs:enable

	// allowlist keys.
	$conditional_tags = array_intersect_key( $conditional_tags, et_fb_conditional_tag_params() );
	$current_page     = array_intersect_key( $current_page, et_fb_current_page_params() );

	// sanitize values.
	$conditional_tags = $utils->sanitize_text_fields( $conditional_tags );
	$current_page     = $utils->sanitize_text_fields( $current_page );

	$module_slug  = isset( $_POST['module_type'] ) ? sanitize_text_field( $_POST['module_type'] ) : '';
	$request_type = isset( $_POST['request_type'] ) ? sanitize_text_field( $_POST['request_type'] ) : '';

	if ( in_array( $request_type, array( '404', 'archive', 'home' ), true ) ) {
		// On non-singular page, we do not have $current_page id, so we will check if user has theme_builder capability.
		if ( ! et_pb_is_allowed( 'theme_builder' ) ) {
			die( -1 );
		}
	} else {
		// For other pages, we will check if user can edit specific post.
		if ( ! current_user_can( 'edit_post', $current_page['id'] ) ) {
			die( -1 );
		}
	}

	// Check if there is page id.
	if ( empty( $current_page['id'] ) && '404' !== $request_type ) {
		die( -1 );
	}

	// $_POST['depends_on'] is a single dimensional assoc array created by jQuery.ajax data param, sanitize each key and value, they will both be strings
	foreach ( $depends_on as $key => $value ) {

		if ( et_()->includes( $value, '%' ) ) {
			// `sanitize_text_fields` removes octets `%[a-f0-9]{2}` and would zap icon values / `%date`
			// so we prefix octets with `_` to protected them and remove the prefix after sanitization.
			$prepared_value  = preg_replace( '/%([a-f0-9]{2})/', '%_$1', $value );
			$sanitized_value = preg_replace( '/%_([a-f0-9]{2})/', '%$1', sanitize_text_field( $prepared_value ) );
		} else {
			$sanitized_value = sanitize_text_field( $value );
		}

		$depends_on[ sanitize_text_field( $key ) ] = $sanitized_value;

	}

	// Since VB performance, it is introduced single ajax request for several property
	// in that case, computed_property posted data can be as an array
	// hence we get the raw post data value, then sanitize it afterward either as array or string.
	// @phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- Will be sanitized conditionally as string or array afterward.
	$computed_property = isset( $_POST['computed_property'] ) ? $_POST['computed_property'] : '';
	$computed_property = is_array( $computed_property ) ? array_map( 'sanitize_text_field', $computed_property ) : sanitize_text_field( $computed_property );

	// get all fields for module.
	$fields = ET_Builder_Element::get_module_fields( $request_type, $module_slug );

	// make sure only valid fields are being passed through.
	$depends_on = array_intersect_key( $depends_on, $fields );

	if ( is_array( $computed_property ) ) {
		$results = array();

		foreach ( $computed_property as $property ) {
			if ( ! isset( $fields[ $property ], $fields[ $property ]['computed_callback'] ) ) {
				continue;
			}

			$callback = $fields[ $property ]['computed_callback'];

			if ( is_callable( $callback ) ) {
				// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found -- The callback is hard-coded in module fields configuration.
				$results[ $property ] = call_user_func( $callback, $depends_on, $conditional_tags, $current_page );
			}
		}

		if ( empty( $results ) ) {
			die( -1 );
		}

		die( wp_json_encode( $results ) );
	}

	// computed property field.
	$field = $fields[ $computed_property ];

	$callback = $field['computed_callback'];

	if ( is_callable( $callback ) ) {
		// @phpcs:ignore Generic.PHP.ForbiddenFunctions.Found -- The callback is hard-coded in module fields configuration.
		die( wp_json_encode( call_user_func( $callback, $depends_on, $conditional_tags, $current_page ) ) );
	} else {
		die( -1 );
	}
}
add_action( 'wp_ajax_et_pb_process_computed_property', 'et_pb_process_computed_property' );

/**
 * Fetch before or after components.
 *
 * @since 4.14.5
 *
 * @return string Components outputs.
 */
function et_fb_fetch_before_after_components() {
	// Bail early if the nonce is incorrect or current user can't edit posts.
	$nonce = ! empty( $_POST['et_fb_fetch_before_after_components_nonce'] ) ? sanitize_text_field( $_POST['et_fb_fetch_before_after_components_nonce'] ) : '';

	if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'et_fb_fetch_before_after_components_nonce' ) ) {
		wp_send_json_error();
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error();
	}

	// Bail early if Module Shortcode Manager class doesn't exist.
	if ( ! class_exists( 'ET_Builder_Module_Shortcode_Manager' ) ) {
		wp_send_json_error();
	}

	/**
	 * Filters modules list.
	 *
	 * The modules list comes from Shortcode Manager only contains built-in modules
	 * intentionally. 3rd-party modules need to include their module slug and class name
	 * via `et_fb_fetch_before_after_modules_map` filter.
	 *
	 * @param array Modules list.
	 */
	$modules_map = apply_filters( 'et_fb_fetch_before_after_modules_map', ET_Builder_Module_Shortcode_Manager::get_modules_map() );

	// Bail early if components map is empty.
	if ( empty( $modules_map ) ) {
		return $modules_components;
	}

	$module_type  = ! empty( $_POST['module_type'] ) ? sanitize_text_field( $_POST['module_type'] ) : '';
	$module_class = et_()->array_get( $modules_map, array( $module_type, 'classname' ) );

	// Bail early if module class name is not found.
	if ( empty( $module_class ) || ! class_exists( $module_class ) ) {
		wp_send_json_error();
	}

	// phpcs:disable ET.Sniffs.ValidatedSanitizedInput -- Sanitization of following arrays is done on Sanitize values section below.
	$conditional_tags = isset( $_POST['conditional_tags'] ) ? $_POST['conditional_tags'] : array();
	$current_page     = isset( $_POST['current_page'] ) ? $_POST['current_page'] : array();
	$module_attrs     = isset( $_POST['module_attrs'] ) ? $_POST['module_attrs'] : array();
	// phpcs:enable

	// Allow list keys.
	$conditional_tags = array_intersect_key( $conditional_tags, et_fb_conditional_tag_params() );
	$current_page     = array_intersect_key( $current_page, et_fb_current_page_params() );

	// Sanitize values.
	$conditional_tags = et_()->sanitize_text_fields( $conditional_tags );
	$current_page     = et_()->sanitize_text_fields( $current_page );
	$module_attrs     = et_()->sanitize_text_fields( $module_attrs );
	$render_mode      = isset( $_POST['render_mode'] ) ? sanitize_text_field( $_POST['render_mode'] ) : '';
	$post_type        = isset( $_POST['post_type'] ) ? sanitize_text_field( $_POST['post_type'] ) : '';
	$action           = isset( $_POST['action'] ) ? sanitize_text_field( $_POST['action'] ) : '';

	// Bail early if current user can't edit this post.
	if ( empty( $current_page['id'] ) || ! current_user_can( 'edit_post', $current_page['id'] ) ) {
		wp_send_json_error();
	}

	// Setup module data.
	$module_data = array(
		'conditional_tags' => $conditional_tags,
		'current_page'     => $current_page,
		'module_attrs'     => $module_attrs,
		'render_mode'      => $render_mode,
		'post_type'        => $post_type,
		'action'           => $action,
	);

	/**
	 * Fires before processing before & after components.
	 *
	 * @since 4.14.5
	 *
	 * @param string $module_type Module slug.
	 * @param array  $module_data Module data passed from the request.
	 */
	do_action( 'et_fb_pre_fetch_before_after_components', $module_type, $module_data );

	$output = $module_class::get_component_before_after_module( $module_type, $module_data );

	wp_send_json_success( $output );
}
add_action( 'wp_ajax_et_fb_fetch_before_after_components', 'et_fb_fetch_before_after_components' );

/**
 * Process shortcode json.
 *
 * @since 4.11.4 Added $inject_responsive_hover param.
 *
 * @param array  $object Shortcodes object.
 * @param array  $options Options.
 * @param string $library_item_type Library item type.
 * @param bool   $escape_content_slashes Whether escape content slashes.
 * @param bool   $inject_responsive_hover Flag to inject missing responsive and hover mode attributes.
 *
 * @return string
 */
function et_fb_process_to_shortcode( $object, $options = array(), $library_item_type = '', $escape_content_slashes = true, $inject_responsive_hover = false ) {
	$output  = '';
	$_object = array();

	$default_options = array(
		'force_valid_slugs'    => false,
		'post_type'            => false,
		'apply_global_presets' => false,
	);

	$options = wp_parse_args( $options, $default_options );

	$global_presets_manager = ET_Builder_Global_Presets_Settings::instance();

	// do not proceed if $object is empty.
	if ( empty( $object ) ) {
		return '';
	}

	$font_icon_fields = ! empty( $options['post_type'] ) ? ET_Builder_Element::get_font_icon_fields( $options['post_type'] ) : false;
	$structure_types  = ET_Builder_Element::get_structure_module_slugs();

	if ( in_array( $library_item_type, array( 'module', 'row' ), true ) ) {
		$excluded_elements = array();

		switch ( $library_item_type ) {
			case 'module':
				$excluded_elements = array( 'et_pb_section', 'et_pb_row', 'et_pb_column' );
				break;
			case 'row':
				$excluded_elements = array( 'et_pb_section' );
				break;
		}

		foreach ( $object as $item ) {
			// do not proceed if $item is empty.
			if ( empty( $item ) ) {
				continue;
			}

			while ( in_array( $item['type'], $excluded_elements, true ) ) {
				$item = $item['content'][0];
			}

			$_object[] = $item;
		}
	} else {
		$_object = $object;
	}

	if ( $options['force_valid_slugs'] ) {
		// we need to supply a reasonable default post type to get a simple list of slugs,
		// otherwise the function will return an array of arrays of slugs for every possible post_type.
		$slug_post_type = ! empty( $options['post_type'] ) ? $options['post_type'] : 'page';
		$valid_slugs    = ET_Builder_Element::get_module_slugs_by_post_type( $slug_post_type );
	}

	foreach ( $_object as $item ) {
		// do not proceed if $item is empty.
		if ( empty( $item ) ) {
			continue;
		}
		$attributes = '';
		$content    = '';
		$type       = sanitize_text_field( $item['type'] );
		$type       = esc_attr( $type );

		// if option enabled, reject invalid slugs.
		if ( $options['force_valid_slugs'] ) {
			if ( ! in_array( $type, $valid_slugs, true ) ) {
				continue;
			}
		}

		if ( ! empty( $item['raw_child_content'] ) ) {
			$content = stripslashes( $item['raw_child_content'] );
		}

		if ( $options['apply_global_presets'] ) {
			$module_type           = $global_presets_manager->maybe_convert_module_type( $type, $item['attrs'] );
			$module_global_presets = $global_presets_manager->get_module_presets_settings( $module_type, $item['attrs'] );
			$item['attrs']         = array_merge( $module_global_presets, $item['attrs'] );
		}

		// Inject responsive/hover attribute value to inherit from desktop
		// when the responsive/hover setting is "on" but the responsive/hover attribute is not exist.
		// This can happen when responsive/hover mode values reset in the builder.
		if ( $inject_responsive_hover ) {
			foreach ( $item['attrs'] as $attribute => $value ) {
				// Inject responsive mode attribute value.
				if ( '_last_edited' === substr( $attribute, -12 ) && 0 === strpos( $value, 'on' ) ) {
					$attr_key_base   = str_replace( '_last_edited', '', $attribute );
					$attr_key_tablet = $attr_key_base . '_tablet';
					$attr_key_phone  = $attr_key_base . '_phone';
					$attr_is_content = 'content' === $attr_key_base && isset( $item['content'] ) && is_string( $item['content'] );

					// Inject tablet mode attribute value.
					if ( ! isset( $item['attrs'][ $attr_key_tablet ] ) ) {
						if ( $attr_is_content ) {
							$item['attrs'][ $attr_key_tablet ] = $item['content'];
						} elseif ( isset( $item['attrs'][ $attr_key_base ] ) ) {
							$item['attrs'][ $attr_key_tablet ] = $item['attrs'][ $attr_key_base ];
						}
					}

					// Inject phone mode attribute value.
					if ( ! isset( $item['attrs'][ $attr_key_phone ] ) ) {
						if ( isset( $item['attrs'][ $attr_key_tablet ] ) ) {
							$item['attrs'][ $attr_key_phone ] = $item['attrs'][ $attr_key_tablet ];
						} else {
							if ( $attr_is_content ) {
								$item['attrs'][ $attr_key_phone ] = $content;
							} elseif ( isset( $values[ $attr_key_base ] ) ) {
								$item['attrs'][ $attr_key_phone ] = $values[ $attr_key_base ];
							}
						}
					}
				}

				// Inject hover mode attribute value.
				if ( '__hover_enabled' === substr( $attribute, -15 ) && 0 === strpos( $value, 'on' ) ) {
					$attr_key_base  = str_replace( '__hover_enabled', '', $attribute );
					$attr_key_hover = $attr_key_base . '__hover';

					if ( ! isset( $item['attrs'][ $attr_key_hover ] ) ) {
						$attr_is_content = 'content' === $attr_key_base && isset( $item['content'] ) && is_string( $item['content'] );

						if ( $attr_is_content ) {
							$item['attrs'][ $attr_key_hover ] = $item['content'];
						} elseif ( isset( $item['attrs'][ $attr_key_base ] ) ) {
							$item['attrs'][ $attr_key_hover ] = $item['attrs'][ $attr_key_base ];
						}
					}
				}
			}
		}

		foreach ( $item['attrs'] as $attribute => $value ) {
			// ignore computed fields.
			if ( '__' === substr( $attribute, 0, 2 ) ) {
				continue;
			}

			// Ignore post_content_module_attrs. They are needed only during editing.
			if ( 'post_content_module_attrs' === $attribute ) {
				continue;
			}

			// Sanitize attribute.
			$attribute = sanitize_text_field( $attribute );

			// Sanitize input properly.
			if ( isset( $font_icon_fields[ $item['type'] ][ $attribute ] ) ) {
				$value = esc_attr( $value );
			}

			// handle content.
			if ( in_array( $attribute, array( 'content', 'raw_content' ), true ) ) {
				// do not override the content if item has raw_child_content.
				if ( empty( $item['raw_child_content'] ) ) {
					$content = $value;

					$content = trim( $content );

					if ( ! empty( $content ) && 'content' === $attribute ) {
						$content = "\n\n" . $content . "\n\n";
					}
				}
			} else {
				// Since WordPress version 5.1, any links in the content that
				// has "target" attribute will be automatically added
				// rel="noreferrer noopener" attribute. This attribute added
				// after the shortcode processed in et_fb_process_to_shortcode
				// function. This become an issue for the builder while parsing the shortcode attributes
				// because the double quote that wrapping the "rel" attribute value is not encoded.
				// So we need to manipulate "target" attribute here before storing the content by renaming
				// is as "data-et-target-link". Later in "et_pb_fix_shortcodes" function
				// we will turn it back as "target".
				$value = str_replace( ' target=', ' data-et-target-link=', $value );

				$is_include_attr = false;

				if ( '' === $value
					&& et_pb_hover_options()->get_field_base_name( $attribute ) !== $attribute
					&& et_pb_hover_options()->is_enabled( et_pb_hover_options()->get_field_base_name( $attribute ), $item['attrs'] ) ) {
					$is_include_attr = true;
				}

				if ( '' === $value
					&& et_pb_responsive_options()->get_field_base_name( $attribute ) !== $attribute
					&& et_pb_responsive_options()->is_enabled( et_pb_responsive_options()->get_field_base_name( $attribute ), $item['attrs'] ) ) {
					$is_include_attr = true;
				}

				if ( '' !== $value ) {
					$is_include_attr = true;
				}

				if ( $is_include_attr ) {
					// TODO, should we check for and handle default here? probably done in FB alredy...

					// Make sure double quotes are encoded, before adding values to shortcode.
					$value = str_ireplace( '"', '%22', $value );

					// Make sure single backslash is encoded, before adding values to Shortcode.
					if ( 'breadcrumb_separator' === $attribute ) {
						$value = str_ireplace( '\\', '%5c', $value );
					}

					// Encode backslash for custom CSS-related and json attributes.
					$json_attributes = array( 'checkbox_options', 'radio_options', 'select_options', 'conditional_logic_rules' );

					if ( 0 === strpos( $attribute, 'custom_css_' ) || in_array( $attribute, $json_attributes, true ) ) {
						$value = str_ireplace( '\\', '%92', $value );

					} elseif ( et_builder_parse_dynamic_content( $value )->is_dynamic() ) {
						$value = str_replace( '\\', '%92', $value );
					}

					// Encode backslash for custom date format attributes.
					$modules_and_attr_with_custom_date = array(
						'et_pb_blog'                 => 'meta_date',
						'et_pb_fullwidth_post_title' => 'date_format',
						'et_pb_post_title'           => 'date_format',
					);

					if ( ! empty( $modules_and_attr_with_custom_date[ $type ] ) && $modules_and_attr_with_custom_date[ $type ] === $attribute ) {
						$value = str_replace( '\\', '%92', $value );
					}

					$attributes .= ' ' . esc_attr( $attribute ) . '="' . et_core_esc_previously( $value ) . '"';
				}
			}
		}

		$attributes = str_replace( array( '[', ']' ), array( '%91', '%93' ), $attributes );

		// prefix sections with a fb_built attr flag.
		if ( 'et_pb_section' === $type ) {
			$attributes = ' fb_built="1"' . $attributes;
		}

		// build shortcode
		// start the opening tag.
		$output .= '[' . $type . $attributes;

		// close the opening tag, depending on self closing.
		if ( empty( $content ) && ! isset( $item['content'] ) && ! in_array( $type, $structure_types, true ) ) {
			$open_tag_only = true;
			$output       .= ' /]';
		} else {
			$open_tag_only = false;
			$output       .= ']';
		}

		// if applicable, add inner content and close tag.
		if ( ! $open_tag_only ) {
			if ( 'et_pb_section' === $type && isset( $item['attrs'] ) && isset( $item['attrs']['fullwidth'] ) && 'on' !== $item['attrs']['fullwidth'] && isset( $item['attrs']['specialty'] ) && 'on' !== $item['attrs']['specialty'] && ( ! isset( $item['content'] ) || ! is_array( $item['content'] ) ) ) {
				// insert empty row if saving empty Regular section to make it work correctly in BB.
				$output .= '[et_pb_row admin_label="Row"][/et_pb_row]';
			} elseif ( isset( $item['content'] ) && is_array( $item['content'] ) ) {
				$output .= et_fb_process_to_shortcode( $item['content'], $options, '', $escape_content_slashes, $inject_responsive_hover );
			} else {
				if ( ! empty( $content ) ) {
					if ( et_is_builder_plugin_active() && in_array( $type, ET_Builder_Element::get_has_content_modules(), true ) ) {
						// Wrap content in autop to avoid tagless content on FE due to content is edited on html editor and only
						// have one-line without newline wrap which prevent `the_content`'s wpautop filter to properly wrap it.
						$content = wpautop( $content );
					}

					$output .= $content;
				} else {
					if ( isset( $item['content'] ) ) {
						$_content = $item['content'];

						if ( $escape_content_slashes ) {
							$_content = str_replace( '\\', '\\\\', $_content );
						}

						if ( et_is_builder_plugin_active() && in_array( $type, ET_Builder_Element::get_has_content_modules(), true ) ) {
							// Wrap content in autop to avoid tagless content on FE due to content is edited on html editor and only
							// have one-line without newline wrap which prevent `the_content`'s wpautop filter to properly wrap it.
							$_content = wpautop( $_content );
						}

						$output .= $_content;
					} else {
						$output .= '';
					}
				}
			}

			// add the closing tag.
			$output .= '[/' . $type . ']';
		}
	}

	return $output;
}

/**
 * Ajax Callback :: Render shortcode output.
 */
function et_fb_ajax_render_shortcode() {
	if ( ! isset( $_POST['et_pb_render_shortcode_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_pb_render_shortcode_nonce'] ), 'et_pb_render_shortcode_nonce' ) ) {
		wp_send_json_error();
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error();
	}

	$utils = ET_Core_Data_Utils::instance();

	global $et_pb_predefined_module_index;

	$et_pb_predefined_module_index = isset( $_POST['et_fb_module_index'] ) && 'default' !== $_POST['et_fb_module_index'] ? sanitize_text_field( $_POST['et_fb_module_index'] ) : false;

	$options = isset( $_POST['options'] ) ? $utils->sanitize_text_fields( $_POST['options'] ) : array(); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- sanitize_text_fields sanitize the options.

	// enforce valid module slugs only
	// shortcode slugs need to be allowlisted so as to prevent malicious shortcodes from being generated and run through do_shortcode().
	$options['force_valid_slugs'] = true;

	// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['object'] will not be stored in db.
	$object = isset( $_POST['object'] ) ? $_POST['object'] : array();

	// convert shortcode array to shortcode string.
	$shortcode = et_fb_process_to_shortcode( $object, $options );

	// take shortcode string and ensure it's properly sanitized for the purposes of this function.
	$shortcode = et_pb_enforce_builder_shortcode( $shortcode );

	$output = do_shortcode( $shortcode );

	$styles = ET_Builder_Element::get_style();

	if ( ! empty( $styles ) ) {
		$output .= sprintf(
			'<style type="text/css" class="et-builder-advanced-style">
				%1$s
			</style>',
			$styles
		);
	}

	wp_send_json_success( $output );
}
add_action( 'wp_ajax_et_fb_ajax_render_shortcode', 'et_fb_ajax_render_shortcode' );

/**
 * Determine current user can save the post.
 *
 * @param int    $post_id Post id.
 * @param string $status Post status.
 *
 * @return bool
 */
function et_fb_current_user_can_save( $post_id, $status = '' ) {
	if ( 'page' === get_post_type( $post_id ) ) {
		if ( ! current_user_can( 'edit_pages' ) ) {
			return false;
		}

		if ( ! current_user_can( 'publish_pages' ) && 'publish' === $status ) {
			return false;
		}

		if ( ! current_user_can( 'edit_published_pages' ) && 'publish' === get_post_status( $post_id ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_others_pages' ) && ! current_user_can( 'edit_page', $post_id ) ) {
			return false;
		}
	} else {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		if ( ! current_user_can( 'publish_posts' ) && 'publish' === $status ) {
			return false;
		}

		if ( ! current_user_can( 'edit_published_posts' ) && 'publish' === get_post_status( $post_id ) ) {
			return false;
		}

		if ( ! current_user_can( 'edit_others_posts' ) && ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}
	}

	// If this is a theme builder layout post type, check divi roles for that capability.
	if ( in_array( get_post_type( $post_id ), et_theme_builder_get_layout_post_types(), true ) && ! et_pb_is_allowed( 'theme_builder' ) ) {
		return false;
	}

	return true;
}

/**
 * Ajax Callback :: Drop backup/autosave depending on exit type.
 */
function et_fb_ajax_drop_autosave() {
	if ( ! isset( $_POST['et_fb_drop_autosave_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_fb_drop_autosave_nonce'] ), 'et_fb_drop_autosave_nonce' ) ) {
		wp_send_json_error();
	}

	$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;

	if ( ! et_fb_current_user_can_save( $post_id ) ) {
		wp_send_json_error();
	}

	$post_author = get_current_user_id();
	$autosave    = wp_get_post_autosave( $post_id, $post_author );

	$autosave_deleted = false;

	// delete builder settings autosave.
	delete_post_meta( $post_id, "_et_builder_settings_autosave_{$post_author}" );

	if ( ! empty( $autosave ) ) {
		wp_delete_post_revision( $autosave->ID );
		$autosave = wp_get_post_autosave( $post_id, $post_author );
		if ( empty( $autosave ) ) {
			$autosave_deleted = true;
		}
	} else {
		$autosave_deleted = true;
	}

	if ( $autosave_deleted ) {
		wp_send_json_success();
	} else {
		wp_send_json_error();
	}
}
add_action( 'wp_ajax_et_fb_ajax_drop_autosave', 'et_fb_ajax_drop_autosave' );

/**
 * Ajax Callback :: Save layout.
 */
function et_fb_ajax_save() {

	if ( ! isset( $_POST['et_fb_save_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_fb_save_nonce'] ), 'et_fb_save_nonce' ) ) {
		wp_send_json_error();
	}

	$utils       = ET_Core_Data_Utils::instance();
	$post_id     = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : 0;
	$options     = isset( $_POST['options'] ) ? $_POST['options'] : array(); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['options'] is an array, it's value sanitization is done  at the time of accessing value.
	$layout_type = isset( $_POST['layout_type'] ) ? sanitize_text_field( $_POST['layout_type'] ) : '';

	$is_theme_builder_layout = in_array( get_post_type( $post_id ), et_theme_builder_get_layout_post_types(), true );

	// For post content check if user can save post.
	if ( ! et_fb_current_user_can_save( $post_id, $utils->array_get_sanitized( $options, 'status' ) ) ) {
		wp_send_json_error();
	}

	$update = false;

	if ( ! isset( $_POST['skip_post_update'] ) ) {
		$is_layout_block_preview = sanitize_text_field( $utils->array_get( $_POST, 'options.conditional_tags.is_layout_block', '' ) );
		$block_id                = sanitize_title( $utils->array_get( $_POST, 'options.current_page.blockId', '' ) );
		$shortcode_data          = isset( $_POST['modules'] ) ? json_decode( stripslashes( $_POST['modules'] ), true ) : array(); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- modules string will be sanitized at the time of saving in the db.

		// Cast as bool if falsey; blockId is retrieved from ajax request, and
		// already return empty string (falsey) if no value found. Nevertheless let's be more safe.
		if ( ! $block_id ) {
			$block_id = false;
		}

		// Cast as bool if falsey; is_layout_block_preview is retrieved from ajax request, and
		// already return empty string (falsey) if no value found. Nevertheless let's be more safe.
		if ( ! $is_layout_block_preview ) {
			$is_layout_block_preview = false;
		}
		$built_for_type = get_post_meta( $post_id, '_et_pb_built_for_post_type', true );
		if ( ! $built_for_type && ! $is_layout_block_preview ) {
			update_post_meta( $post_id, '_et_pb_built_for_post_type', 'page' );
		}

		// If Default Editor is used for Post Content, and Post Content is not edited,
		// handleAjaxSave will pass return_to_default_editor,
		// and in that case we need reactivate the default editor for the post.
		// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput.InputNotSanitized -- input is inside isset() function so its value is not used
		if ( isset( $_POST['return_to_default_editor'] ) && rest_sanitize_boolean( $_POST['return_to_default_editor'] ) ) {
			update_post_meta( $post_id, '_et_pb_use_builder', 'off' );
			update_post_meta( $post_id, '_et_pb_show_page_creation', 'on' );

			// Get old content and if we should return to the Default Editor.
			$post_content = get_post_meta( $post_id, '_et_pb_old_content', true );
		} else {
			if ( ! $is_layout_block_preview ) {
				update_post_meta( $post_id, '_et_pb_use_builder', 'on' );
			}
			$post_content = et_fb_process_to_shortcode( $shortcode_data, $options, $layout_type, true, true );
		}

		// Store a copy of the sanitized post content in case wpkses alters it since that
		// would cause our check at the end of this function to fail.
		$sanitized_content = sanitize_post_field( 'post_content', $post_content, $post_id, 'db' );

		// Exit early for layout block update; builder should not actually save post content in this scenario
		// Update post meta and let it is being used to update layoutContent on editor.
		if ( $is_layout_block_preview && $block_id ) {
			$layout_preview_meta_key = "_et_block_layout_preview_{$block_id}";
			$saved_layout            = get_post_meta( $post_id, $layout_preview_meta_key, true );

			// If saved layout is identical to the the layout sent via AJAX, return send json success;
			// this is needed because update_post_meta() returns false if the saved layout is identical
			// to the the one given as param.
			if ( ! empty( $saved_layout ) && $saved_layout === $post_content ) {
				wp_send_json_success(
					array(
						'save_verification' => true,
					)
				);

				wp_die();
			}

			$update = update_post_meta( $post_id, $layout_preview_meta_key, $post_content );

			if ( $update ) {
				wp_send_json_success(
					array(
						'save_verification' => true,
					)
				);
			} else {
				wp_send_json_error();
			}

			wp_die();
		}

		$update = wp_update_post(
			array(
				'ID'           => $post_id,
				'post_content' => $post_content,
				'post_status'  => $utils->array_get_sanitized( $options, 'status' ),
			)
		);
	}

	// update Global modules with selective sync.
	if ( 'module' === $layout_type && isset( $_POST['unsyncedGlobalSettings'] ) && 'none' !== $_POST['unsyncedGlobalSettings'] ) {
	    // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['unsyncedGlobalSettings'] will be sanitized before storing in db.
		$unsynced_options = stripslashes( $_POST['unsyncedGlobalSettings'] );
		update_post_meta( $post_id, '_et_pb_excluded_global_options', sanitize_text_field( $unsynced_options ) );
	}

	// check if there is an autosave that is newer.
	$post_author = get_current_user_id();
	// Store one autosave per author. If there is already an autosave, overwrite it.
	$autosave = wp_get_post_autosave( $post_id, $post_author );

	if ( ! empty( $autosave ) ) {
		wp_delete_post_revision( $autosave->ID );
	}

	if ( isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ) {
		et_builder_update_settings( $_POST['settings'], $post_id ); // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['settings'] is an array, it's value sanitization is done inside function at the time of accessing value.
	}

	if ( isset( $_POST['preferences'] ) && is_array( $_POST['preferences'] ) && ! $is_theme_builder_layout ) {
		$app_preferences = et_fb_app_preferences_settings();

		// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['et_builder_mode'] value used in the comparision.
		$limited_prefix = ! empty( $_POST['et_builder_mode'] ) && 'limited' === $_POST['et_builder_mode'] ? 'limited_' : '';

		foreach ( $app_preferences as $preference_key => $preference_data ) {

			// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $preference_value will be sanitized before saving in db.
			$preference_value = isset( $_POST['preferences'][ $preference_key ] ) && isset( $_POST['preferences'][ $preference_key ]['value'] ) ? $_POST['preferences'][ $preference_key ]['value'] : $preference_data['default'];

			// sanitize based on type.
			switch ( $preference_data['type'] ) {
				case 'int':
					$preference_value = absint( $preference_value );
					break;
				case 'bool':
					$preference_value = 'true' === $preference_value ? 'true' : 'false';
					break;
				default:
					$preference_value = sanitize_text_field( $preference_value );
					break;
			}

			$preference_value_max_length = et_()->array_get( $preference_data, 'max_length', 0 );

			if ( $preference_value && is_numeric( $preference_value_max_length ) && $preference_value_max_length > 0 ) {
				$preference_value = substr( $preference_value, 0, $preference_value_max_length );
			}

			$option_name = 'et_fb_pref_' . $preference_key;

			if ( in_array( $preference_key, et_fb_unsynced_preferences(), true ) ) {
				$option_name = 'et_fb_pref_' . $limited_prefix . $preference_key;
			}

			et_update_option( $option_name, $preference_value );
		}
	}

	// Clear AB Testing stats & transient data.
	if ( isset( $_POST['ab_testing'] ) && isset( $_POST['ab_testing']['is_clear_stats'] ) && 'true' === $_POST['ab_testing']['is_clear_stats'] && et_pb_is_allowed( 'ab_testing' ) ) {
		et_pb_ab_remove_stats( $post_id );
		et_pb_ab_clear_cache_handler( $post_id );
	}

	do_action( 'et_save_post', $post_id );

	if ( $update ) {
		if ( ! empty( $_POST['et_builder_version'] ) ) {
			update_post_meta( $post_id, '_et_builder_version', sanitize_text_field( $_POST['et_builder_version'] ) );
		}

		// Get saved post, verify its content against the one that is being sent.
		$saved_post           = get_post( $update );
		$saved_post_content   = $saved_post->post_content;
		$builder_post_content = stripslashes( $sanitized_content );

		// Get rendered post content only if it's needed.
		$return_rendered_content = sanitize_text_field( $utils->array_get( $_POST, 'options.return_rendered_content', 'false' ) );
		$rendered_post_content   = 'true' === $return_rendered_content ? do_shortcode( $saved_post_content ) : '';

		// If `post_content` column on wp_posts table doesn't use `utf8mb4` charset, the saved post
		// content's emoji will be encoded which means the check of saved post_content vs
		// builder's post_content will be false; Thus check the charset of `post_content` column
		// first then encode the builder's post_content if needed
		// @see https://make.wordpress.org/core/2015/04/02/omg-emoji-%f0%9f%98%8e/
		// @see https://make.wordpress.org/core/2015/04/02/the-utf8mb4-upgrade/.
		global $wpdb;

		if ( 'utf8' === $wpdb->get_col_charset( $wpdb->posts, 'post_content' ) ) {
			$builder_post_content = wp_encode_emoji( $builder_post_content );
		}

		$saved_verification = $saved_post_content === $builder_post_content;

		if ( $saved_verification ) {
			// Strip non-printable characters to ensure preg_match_all operation work properly.
			$post_content_cleaned = preg_replace( '/[\x00-\x1F\x7F]/u', '', $saved_post->post_content );

			preg_match_all( '/\[et_pb_section(.*?)?\]\[et_pb_row(.*?)?\]\[et_pb_column(.*?)?\](.+?)\[\/et_pb_column\]\[\/et_pb_row\]\[\/et_pb_section\]/m', $post_content_cleaned, $matches );
			if ( isset( $matches[4] ) && ! empty( $matches[4] ) ) {
				// Set page creation flow to off.
				update_post_meta( $post_id, '_et_pb_show_page_creation', 'off' );
			} else {
				delete_post_meta( $post_id, '_et_pb_show_page_creation' );
			}
		}

		/**
		 * Hook triggered when the Post is updated.
		 *
		 * @param int $post_id Post ID.
		 *
		 * @since 3.29
		 */
		do_action( 'et_update_post', $post_id );

		wp_send_json_success(
			array(
				'status'            => get_post_status( $update ),
				'save_verification' => apply_filters( 'et_fb_ajax_save_verification_result', $saved_verification ),
				'rendered_content'  => $rendered_post_content,
			)
		);
	} elseif ( isset( $_POST['skip_post_update'] ) ) {
		wp_send_json_success();
	} else {
		wp_send_json_error();
	}
}
add_action( 'wp_ajax_et_fb_ajax_save', 'et_fb_ajax_save' );

/**
 * Ajax Callback :: Convert fb object into shortcode.
 */
function et_fb_get_shortcode_from_fb_object() {
	if ( ! et_core_security_check( 'edit_posts', 'et_fb_convert_to_shortcode_nonce', 'et_fb_convert_to_shortcode_nonce', '_POST', false ) ) {
		wp_send_json_error();
	}

	// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['modules'] will not be stored in db.
	$shortcode_data = isset( $_POST['modules'] ) ? json_decode( stripslashes( $_POST['modules'] ), true ) : array();
	$layout_type    = isset( $_POST['layout_type'] ) ? sanitize_text_field( $_POST['layout_type'] ) : '';

	$post_content = et_fb_process_to_shortcode( $shortcode_data, array(), $layout_type );

	// Get rendered post content only if it's needed.
	$utils                   = ET_Core_Data_Utils::instance();
	$return_rendered_content = sanitize_text_field( $utils->array_get( $_POST, 'options.return_rendered_content', 'false' ) );
	$rendered_post_content   = 'true' === $return_rendered_content ? do_shortcode( $post_content ) : '';

	wp_send_json_success(
		array(
			'processed_content' => $post_content,
			'rendered_content'  => $rendered_post_content,
		)
	);
}

add_action( 'wp_ajax_et_fb_get_shortcode_from_fb_object', 'et_fb_get_shortcode_from_fb_object' );

/**
 * Ajax Callback :: Convert shortcode into HTML.
 */
function et_fb_get_html_from_shortcode() {
	if ( ! et_core_security_check( 'edit_posts', 'et_fb_shortcode_to_html_nonce', 'et_fb_shortcode_to_html_nonce', '_POST', false ) ) {
		wp_send_json_error();
	}

	// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- $_POST['modules'] will not be stored in db.
	$post_content = isset( $_POST['content'] ) ? stripslashes( $_POST['content'] ) : '';

	// Get rendered post content by shortcode.
	$rendered_post_content = do_shortcode( $post_content );

	wp_send_json_success(
		array(
			'rendered_content' => $rendered_post_content,
		)
	);
}

add_action( 'wp_ajax_et_fb_get_html_from_shortcode', 'et_fb_get_html_from_shortcode' );

/**
 * Ajax Callback :: Save library modules.
 */
function et_fb_save_layout() {
	if ( ! isset( $_POST['et_fb_save_library_modules_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_fb_save_library_modules_nonce'] ), 'et_fb_save_library_modules_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	if ( empty( $_POST['et_layout_name'] ) ) {
		die( -1 );
	}

	$post_type = sanitize_text_field( et_()->array_get( $_POST, 'et_post_type', 'page' ) );

	if ( et_theme_builder_is_layout_post_type( $post_type ) ) {
		// Treat TB layouts as normal posts when storing layouts from the library.
		$post_type = 'page';
	}

	$args = array(
		'layout_type'          => isset( $_POST['et_layout_type'] ) ? sanitize_text_field( $_POST['et_layout_type'] ) : 'layout',
		'layout_selected_cats' => isset( $_POST['et_layout_cats'] ) ? sanitize_text_field( $_POST['et_layout_cats'] ) : '',
		'layout_selected_tags' => isset( $_POST['et_layout_tags'] ) ? sanitize_text_field( $_POST['et_layout_tags'] ) : '',
		'built_for_post_type'  => $post_type,
		'layout_new_cat'       => isset( $_POST['et_layout_new_cat'] ) ? sanitize_text_field( $_POST['et_layout_new_cat'] ) : '',
		'layout_new_tag'       => isset( $_POST['et_layout_new_tag'] ) ? sanitize_text_field( $_POST['et_layout_new_tag'] ) : '',
		'columns_layout'       => isset( $_POST['et_columns_layout'] ) ? sanitize_text_field( $_POST['et_columns_layout'] ) : '0',
		'module_type'          => isset( $_POST['et_module_type'] ) ? sanitize_text_field( $_POST['et_module_type'] ) : 'et_pb_unknown',
		'layout_scope'         => isset( $_POST['et_layout_scope'] ) ? sanitize_text_field( $_POST['et_layout_scope'] ) : 'not_global',
		'module_width'         => isset( $_POST['et_module_width'] ) ? sanitize_text_field( $_POST['et_module_width'] ) : 'regular',
		'layout_content'       => isset( $_POST['et_layout_content'] ) ? et_fb_process_to_shortcode( json_decode( stripslashes( $_POST['et_layout_content'] ), true ) ) : '', // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- The `$_POST['et_layout_content']` will be sanitized before saving into db.
		'layout_name'          => isset( $_POST['et_layout_name'] ) ? sanitize_text_field( $_POST['et_layout_name'] ) : '',
	);

	$new_layout_meta = et_pb_submit_layout( $args );
	$updated_terms   = array();

	foreach ( [ 'layout_category', 'layout_tag' ] as $taxonomy ) {
		$raw_terms_array   = apply_filters( 'et_pb_new_layout_cats_array', get_terms( $taxonomy, array( 'hide_empty' => false ) ) );
		$clean_terms_array = array();

		if ( is_array( $raw_terms_array ) && ! empty( $raw_terms_array ) ) {
			foreach ( $raw_terms_array as $term ) {
				$clean_terms_array[] = array(
					'name' => html_entity_decode( $term->name ),
					'id'   => $term->term_id,
					'slug' => $term->slug,
				);
			}
		}

		$updated_terms[ $taxonomy ] = $clean_terms_array;
	}

	$data = array(
		'layout_data'   => json_decode( $new_layout_meta, true ),
		'updated_terms' => $updated_terms,
	);

	die( wp_json_encode( et_core_esc_previously( $data ) ) );
}
add_action( 'wp_ajax_et_fb_save_layout', 'et_fb_save_layout' );

/**
 * Ajax Callback :: Process shortcode to exported layout object.
 */
function et_fb_get_cloud_item_content() {
	if ( ! isset( $_POST['et_fb_save_cloud_item_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_fb_save_cloud_item_nonce'] ), 'et_fb_save_cloud_item_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$layout_type        = isset( $_POST['et_layout_type'] ) ? sanitize_text_field( $_POST['et_layout_type'] ) : '';
	$layout_content     = isset( $_POST['et_layout_content'] ) ? et_fb_process_to_shortcode( json_decode( stripslashes( $_POST['et_layout_content'] ), true ), array(), $layout_type ) : ''; // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- The `$_POST['et_layout_content']` will be sanitized before saving into db.
	$exported_shortcode = get_exported_content( $layout_content );

	die( wp_json_encode( array( 'shortcode' => $exported_shortcode ) ) );
}
add_action( 'wp_ajax_et_fb_get_cloud_item_content', 'et_fb_get_cloud_item_content' );

/**
 * Prepare shortcode for exporting.
 *
 * @since 4.17.0
 *
 * @param string $shortcode Shortcode to process.
 *
 * @return array
 */
function get_exported_content( $shortcode ) {
	// Set faux $_POST value that is required by portability.
	$_POST['post']    = '1';
	$_POST['content'] = $shortcode;

	// Remove page value if it is equal to `false`, avoiding paginated images not accidentally triggered.
	if ( isset( $_POST['page'] ) && false === $_POST['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		unset( $_POST['page'] ); // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
	}

	$portability = et_core_portability_load( 'et_builder' );

	// Export the content.
	return $portability->export( true, true );
}

/**
 * Ajax Callback :: Process shortcode.
 */
function et_fb_prepare_shortcode() {
	if ( ! isset( $_POST['et_fb_prepare_shortcode_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['et_fb_prepare_shortcode_nonce'] ), 'et_fb_prepare_shortcode_nonce' ) ) {
		wp_send_json_error();
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	// phpcs:disable ET.Sniffs.ValidatedSanitizedInput -- The `$_POST['et_page_content']` and `$_POST['apply_global_presets']` will not be stored on db.
	$content              = isset( $_POST['et_page_content'] ) ? json_decode( stripslashes( $_POST['et_page_content'] ), true ) : '';
	$apply_global_presets = isset( $_POST['apply_global_presets'] ) ? wp_validate_boolean( $_POST['apply_global_presets'] ) : false;
	// phpcs:enable

	$options = array(
		'apply_global_presets' => $apply_global_presets,
	);

	$result = $content ? et_fb_process_to_shortcode( $content, $options, '', false ) : '';

	die( wp_json_encode( array( 'shortcode' => $result ) ) );
}
add_action( 'wp_ajax_et_fb_prepare_shortcode', 'et_fb_prepare_shortcode' );

/**
 * Ajax Callback :: Save library module.
 */
function et_fb_update_layout() {
	if ( ! isset( $_POST['et_fb_save_library_modules_nonce'] ) || ! wp_verify_nonce( $_POST['et_fb_save_library_modules_nonce'], 'et_fb_save_library_modules_nonce' ) ) { // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- The nonce value is used only for comparision in the `wp_verify_nonce`.
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	$post_id = isset( $_POST['et_template_post_id'] ) ? absint( $_POST['et_template_post_id'] ) : '';

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		die( -1 );
	}

	// phpcs:disable ET.Sniffs.ValidatedSanitizedInput -- Following $_POST values will be sanitized before storing in db.
	$post_content            = isset( $_POST['et_layout_content'] ) ? json_decode( stripslashes( $_POST['et_layout_content'] ), true ) : array();
	$new_content             = isset( $_POST['et_layout_content'] ) ? et_fb_process_to_shortcode( $post_content ) : '';
	$excluded_global_options = isset( $_POST['et_excluded_global_options'] ) ? stripslashes( $_POST['et_excluded_global_options'] ) : array();
	$is_saving_global_module = isset( $_POST['et_saving_global_module'] ) ? sanitize_text_field( $_POST['et_saving_global_module'] ) : '';
	// phpcs:enable

	if ( '' !== $post_id ) {
		$update = array(
			'ID'           => $post_id,
			'post_content' => $new_content,
		);

		$result = wp_update_post( $update );

		if ( ! $result || is_wp_error( $result ) ) {
			wp_send_json_error();
		}

		ET_Core_PageResource::remove_static_resources( 'all', 'all' );

		// update list of unsynced options for global module.
		if ( 'true' === $is_saving_global_module ) {
			update_post_meta( $post_id, '_et_pb_excluded_global_options', sanitize_text_field( $excluded_global_options ) );
		}
	}

	die();
}
add_action( 'wp_ajax_et_fb_update_layout', 'et_fb_update_layout' );

/**
 * Ajax Callback :: Return requested attachments from media library.
 */
function et_fb_fetch_attachments() {
	et_core_security_check( 'edit_posts', 'et_fb_fetch_attachments', 'et_fb_fetch_attachments' );

	$ids = ET_Core_Data_Utils::instance()->array_get( $_POST, 'ids' );

	if ( empty( $ids ) ) {
		wp_send_json( null );
	} else {
		$attachments = get_posts(
			array(
				'posts_per_page' => - 1,
				'include'        => $ids,
				'post_type'      => 'attachment',
			)
		);

		foreach ( $attachments as $index => $attachment ) {
			$metadata = array();

			foreach ( get_intermediate_image_sizes() as $size ) {
				$metadata[ $size ] = wp_get_attachment_image_src( $attachment->ID, $size );
			}

			$attachments[ $index ] = array_merge(
				get_object_vars( $attachment ),
				array(
					'metadata' => $metadata,
				)
			);
		}

		wp_send_json( $attachments );
	}

	die();
}

add_action( 'wp_ajax_et_fb_fetch_attachments', 'et_fb_fetch_attachments' );

if ( ! function_exists( 'et_fb_disable_product_tour' ) ) :
	/**
	 * Saving User Specific Tour status.
	 */
	function et_fb_disable_product_tour() {
		do_action( 'et_fb_disable_product_tour' );

		if ( ! et_core_security_check_passed( 'edit_posts' ) ) {
			ET_Core_Logger::debug( 'Unable to disable product tour. Security check failed!' );
			return;
		}

		$user_id                          = (int) get_current_user_id();
		$product_tour_status              = et_get_option( 'product_tour_status', [] );
		$all_product_settings             = is_array( $product_tour_status ) ? $product_tour_status : [];
		$all_product_settings[ $user_id ] = 'off';

		et_update_option( 'product_tour_status', $all_product_settings );
	}
endif;

if ( ! function_exists( 'et_builder_include_categories_option' ) ) :
	/**
	 * Generate output string for `include_categories` option used in backbone template.
	 *
	 * @param array  $args Arguments to get project categories.
	 * @param string $default_category @todo Add parameter doc.
	 * @return string
	 */
	function et_builder_include_categories_option( $args = array(), $default_category = '' ) {
		$custom_items = array();

		if ( ! empty( $args['custom_items'] ) ) {
			$custom_items = $args['custom_items'];
			unset( $args['custom_items'] );
		}

		$defaults = array(
			'use_terms'  => true,
			'term_name'  => 'project_category',
			'field_name' => 'et_pb_include_categories',
		);

		$defaults = apply_filters( 'et_builder_include_categories_defaults', $defaults );

		$args               = wp_parse_args( $args, $defaults );
		$args['field_name'] = esc_attr( $args['field_name'] );

		$term_args = apply_filters( 'et_builder_include_categories_option_args', array( 'hide_empty' => false ) );

		$output = "\t<% var {$args['field_name']}_temp = typeof data !== 'undefined' && typeof data.{$args['field_name']} !== 'undefined' ? data.{$args['field_name']}.split( ',' ) : ['" . esc_html( $default_category ) . "']; {$args['field_name']}_temp = typeof data === 'undefined' && typeof {$args['field_name']} !== 'undefined' ? {$args['field_name']}.split( ',' ) : {$args['field_name']}_temp; %>\n";

		if ( $args['use_terms'] ) {
			$cats_array = get_terms( $args['term_name'], $term_args );
		} else {
			$cats_array = get_categories( apply_filters( 'et_builder_get_categories_args', 'hide_empty=0' ) );
		}

		$cats_array = array_merge( $custom_items, $cats_array );

		if ( empty( $cats_array ) ) {
			$taxonomy_type = $args['use_terms'] ? $args['term_name'] : 'category';
			$taxonomy      = get_taxonomy( $taxonomy_type );
			$labels        = get_taxonomy_labels( $taxonomy );
			$output        = sprintf( '<p>%1$s</p>', esc_html( $labels->not_found ) );
		}

		foreach ( $cats_array as $category ) {
			$contains = sprintf(
				"<%%= _.contains( {$args['field_name']}_temp, '%1\$s' ) ? checked='checked' : '' %%>",
				is_array( $category ) ? esc_html( $category['term_id'] ) : esc_html( $category->term_id )
			);

			$output .= sprintf(
				'%4$s<label><input type="checkbox" name="%5$s" value="%1$s"%3$s> %2$s</label><br/>',
				is_array( $category ) ? esc_html( $category['term_id'] ) : esc_html( $category->term_id ),
				is_array( $category ) ? esc_html( $category['name'] ) : esc_html( $category->name ),
				$contains,
				"\n\t\t\t\t\t",
				$args['field_name']
			);
		}

		$output = "<div id='{$args['field_name']}'>" . $output . '</div>';

		return apply_filters( 'et_builder_include_categories_option_html', $output );
	}
endif;

if ( ! function_exists( 'et_builder_include_categories_shop_option' ) ) :
	/**
	 * Generate output string for `include_shop_categories` option used in backbone template.
	 *
	 * @param array $args arguments to get shop categories.
	 * @return string
	 */
	function et_builder_include_categories_shop_option( $args = array() ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return '';
		}

		$output = "\t<% var et_pb_include_categories_shop_temp = typeof data !== 'undefined' && typeof data.et_pb_include_categories !== 'undefined' ? data.et_pb_include_categories.split( ',' ) : []; et_pb_include_categories_shop_temp = typeof data === 'undefined' && typeof et_pb_include_categories !== 'undefined' ? et_pb_include_categories.split( ',' ) : et_pb_include_categories_shop_temp; %>\n";

		$product_categories = et_builder_get_shop_categories( $args );

		$output .= '<div id="et_pb_include_categories">';

		if ( is_array( $product_categories ) && ! empty( $product_categories ) ) {
			foreach ( $product_categories as $category ) {
				if ( is_object( $category ) && is_a( $category, 'WP_Term' ) ) {
					$contains = sprintf(
						'<%%= _.contains( et_pb_include_categories_shop_temp, "%1$s" ) ? checked="checked" : "" %%>',
						esc_html( $category->term_id )
					);

					$output .= sprintf(
						'%4$s<label><input type="checkbox" name="et_pb_include_categories" value="%1$s"%3$s> %2$s</label><br/>',
						esc_attr( $category->term_id ),
						esc_html( $category->name ),
						$contains,
						"\n\t\t\t\t\t"
					);
				}
			}
		}

		$output .= '</div>';

		return apply_filters( 'et_builder_include_categories_option_html', $output );
	}
endif;

if ( ! function_exists( 'et_divi_get_projects' ) ) :
	/**
	 * Return projects.
	 *
	 * @param array $args WP_Query arguments.
	 */
	function et_divi_get_projects( $args = array() ) {
		$default_args = array(
			'post_type' => 'project',
		);
		$args         = wp_parse_args( $args, $default_args );
		return new WP_Query( $args );
	}
endif;

if ( ! function_exists( 'et_pb_extract_items' ) ) :
	/**
	 * Return pricing table items html.
	 *
	 * @param string $content Content.
	 */
	function et_pb_extract_items( $content ) {
		$output          = '';
		$first_character = '';

		$lines = array_filter( explode( "\n", str_replace( array( '<p>', '</p>', '<br />' ), "\n", $content ) ) );
		foreach ( $lines as $line ) {
			$line = trim( $line );
			if ( '&#8211;' === substr( $line, 0, 7 ) ) {
				$line = '-' . substr( $line, 7 );
			}
			if ( '' === $line ) {
				continue;
			}
			$first_character = $line[0];
			if ( in_array( $first_character, array( '-', '+' ), true ) ) {
				$line = trim( substr( $line, 1 ) );
			}
			$output .= sprintf(
				'[et_pb_pricing_item available="%2$s"]%1$s[/et_pb_pricing_item]',
				$line,
				( '-' === $first_character ? 'off' : 'on' )
			);
		}
		return do_shortcode( $output );
	}
endif;

/**
 * Get all acceptable string value for given CSS property
 *
 * @since 4.15.0 Added background-size to allow only 'auto' and ''.
 *
 * @param string $property property name.
 * @return array of acceptable CSS string values of given property name.
 */
function et_builder_get_acceptable_css_string_values( $property = 'all' ) {
	$css_string_values = array(
		'width'           => array(
			'auto',
			'inherit',
			'initial',
			'unset',
			'',
		),
		'max-width'       => array(
			'none',
			'inherit',
			'initial',
			'unset',
		),
		'margin'          => array(
			'auto',
			'inherit',
			'initial',
			'unset',
		),
		'padding'         => array(
			'inherit',
			'initial',
			'unset',
		),
		'height'          => array(
			'auto',
			'inherit',
			'initial',
			'unset',
			'',
		),
		'min-height'      => array(
			'auto',
			'inherit',
			'initial',
			'unset',
		),
		'max-height'      => array(
			'none',
			'inherit',
			'initial',
			'unset',
		),
		'z-index'         => array(
			'auto',
		),
		'line-height'     => array(
			'',
		),
		'font-size'       => array(
			'%',
			'em',
			'rem',
			'px',
			'cm',
			'mm',
			'in',
			'pt',
			'pc',
			'ex',
			'vh',
			'vw',
		),
		'background-size' => array(
			'auto',
			'',
		),
	);

	$acceptable_strings = apply_filters( 'et_builder_acceptable_css_string_values', $css_string_values );

	if ( 'all' === $property ) {
		return $acceptable_strings;
	}

	return isset( $acceptable_strings[ $property ] ) ? $acceptable_strings[ $property ] : array();
}

if ( ! function_exists( 'et_builder_process_range_value' ) ) :
	/**
	 * Process range setting field value.
	 *
	 * @param string $range Range value.
	 * @param string $option_type CSS property.
	 */
	function et_builder_process_range_value( $range, $option_type = '' ) {
		$range       = trim( $range );
		$range_digit = '';

		if ( 'none' !== $range ) {
			$range_digit = floatval( $range );
		}

		$range_string = str_replace( et_()->to_css_decimal( $range_digit ), '', (string) $range );

		if ( '' !== $option_type && in_array( $range, et_builder_get_acceptable_css_string_values( $option_type ), true ) ) {
			$result = $range;
		} else {
			if ( '' === $range_string ) {
				$range_string = 'line_height' === $option_type && 3 >= $range_digit ? 'em' : 'px';
			}

			$result = et_()->to_css_decimal( $range_digit ) . $range_string;
		}

		return apply_filters( 'et_builder_processed_range_value', $result, $range, $range_string );
	}
endif;

if ( ! function_exists( 'et_builder_get_border_styles' ) ) :
	/**
	 * Return border styles options list.
	 */
	function et_builder_get_border_styles() {
		$styles = array(
			'solid'  => esc_html__( 'Solid', 'et_builder' ),
			'dashed' => esc_html__( 'Dashed', 'et_builder' ),
			'dotted' => esc_html__( 'Dotted', 'et_builder' ),
			'double' => esc_html__( 'Double', 'et_builder' ),
			'groove' => esc_html__( 'Groove', 'et_builder' ),
			'ridge'  => esc_html__( 'Ridge', 'et_builder' ),
			'inset'  => esc_html__( 'Inset', 'et_builder' ),
			'outset' => esc_html__( 'Outset', 'et_builder' ),
			'none'   => et_builder_i18n( 'None' ),
		);

		return apply_filters( 'et_builder_border_styles', $styles );
	}
endif;

if ( ! function_exists( 'et_builder_font_options' ) ) :
	/**
	 * Return an array of font options.
	 */
	function et_builder_font_options() {
		$options = array();

		$default_options = array(
			'default' => array(
				'name' => et_builder_i18n( 'Default' ),
			),
		);
		$fonts           = array_merge( $default_options, et_builder_get_fonts() );

		foreach ( $fonts as $font_name => $font_settings ) {
			$options[ $font_name ] = 'default' !== $font_name ? $font_name : $font_settings['name'];
		}

		return $options;
	}
endif;

if ( ! function_exists( 'et_builder_get_google_font_items' ) ) :
	/**
	 * Return google font list items.
	 */
	function et_builder_get_google_font_items() {
		$output       = '';
		$font_options = et_builder_font_options();

		foreach ( $font_options as $key => $value ) {
			$output .= sprintf(
				'<li class="select-option-item select-option-item-%3$s" data-value="%1$s">%2$s</li>',
				esc_attr( $key ),
				esc_html( $value ),
				str_replace( ' ', '_', esc_attr( $key ) )
			);
		}

		return $output;
	}
endif;

if ( ! function_exists( 'et_builder_set_element_font' ) ) :
	/**
	 * Return element font style.
	 *
	 * @param string $font Font style value.
	 * @param bool   $use_important Whether use !important.
	 * @param bool   $default Default font style value. e.x global font value.
	 */
	function et_builder_set_element_font( $font, $use_important = false, $default = false ) {
		static $cache = array();

		$style = '';

		if ( '' === $font ) {
			return $style;
		}

		$font_values         = explode( '|', $font );
		$default             = ! $default ? '||||||||' : $default;
		$font_values_default = explode( '|', $default );

		if ( ! empty( $font_values ) ) {
			// backward compatibility with old version of theme.
			if ( isset( $font_values[1] ) ) {
				$font_values[1] = 'on' === $font_values[1] ? '700' : $font_values[1];
			}

			$font_values          = array_map( 'trim', $font_values );
			$font_name            = $font_values[0];
			$font_weight          = isset( $font_values[1] ) && '' !== $font_values[1] ? $font_values[1] : '';
			$is_font_italic       = isset( $font_values[2] ) && 'on' === $font_values[2] ? true : false;
			$is_font_uppercase    = isset( $font_values[3] ) && 'on' === $font_values[3] ? true : false;
			$is_font_underline    = isset( $font_values[4] ) && 'on' === $font_values[4] ? true : false;
			$is_font_small_caps   = isset( $font_values[5] ) && 'on' === $font_values[5] ? true : false;
			$is_font_line_through = isset( $font_values[6] ) && 'on' === $font_values[6] ? true : false;
			$font_line_color      = isset( $font_values[7] ) ? $font_values[7] : '';
			$font_line_style      = isset( $font_values[8] ) ? $font_values[8] : '';

			$font_name_default            = $font_values_default[0];
			$font_weight_default          = isset( $font_values_default[1] ) && '' !== $font_values_default[1] ? $font_values_default[1] : '';
			$is_font_italic_default       = isset( $font_values_default[2] ) && 'on' === $font_values_default[2] ? true : false;
			$is_font_uppercase_default    = isset( $font_values_default[3] ) && 'on' === $font_values_default[3] ? true : false;
			$is_font_underline_default    = isset( $font_values_default[4] ) && 'on' === $font_values_default[4] ? true : false;
			$is_font_small_caps_default   = isset( $font_values_default[5] ) && 'on' === $font_values_default[5] ? true : false;
			$is_font_line_through_default = isset( $font_values_default[6] ) && 'on' === $font_values_default[6] ? true : false;
			$font_line_color_default      = isset( $font_values_default[7] ) ? $font_values_default[7] : '';
			$font_line_style_default      = isset( $font_values_default[8] ) ? $font_values_default[8] : '';

			if ( '' !== $font_name && $font_name_default !== $font_name && 'Default' !== $font_name ) {
				if ( empty( $cache[ $font_name ] ) ) {
					et_builder_enqueue_font( $font_name );
					$font_family         = et_builder_get_font_family( $font_name );
					$cache[ $font_name ] = $font_family;
				} else {
					$font_family = $cache[ $font_name ];
				}

				if ( $use_important ) {
					$font_family = rtrim( $font_family, ';' ) . ' !important;';
				}

				$style .= "$font_family ";
			}

			// Parse global font weight value.
			$is_global_font_weigth = in_array( $font_weight, array( '--et_global_heading_font_weight', '--et_global_body_font_weight' ), true );
			$font_weight           = $is_global_font_weigth ? '--et_global_heading_font_weight' === $font_weight ? et_get_option( 'heading_font_weight', '' ) : et_get_option( 'body_font_weight', '' ) : $font_weight;

			$style .= et_builder_set_element_font_style( 'font-weight', ( '' !== $font_weight_default && ( '' === $font_weight || $font_weight_default === $font_weight ) ), ( '' !== $font_weight ), 'normal', $font_weight, $use_important );

			$style .= et_builder_set_element_font_style( 'font-style', $is_font_italic_default, $is_font_italic, 'normal', 'italic', $use_important );

			$style .= et_builder_set_element_font_style( 'text-transform', $is_font_uppercase_default, $is_font_uppercase, 'none', 'uppercase', $use_important );

			$style .= et_builder_set_element_font_style( 'text-decoration', $is_font_underline_default, $is_font_underline, 'none', 'underline', $use_important );

			$style .= et_builder_set_element_font_style( 'font-variant', $is_font_small_caps_default, $is_font_small_caps, 'none', 'small-caps', $use_important );

			$style .= et_builder_set_element_font_style( 'text-decoration', $is_font_line_through_default, $is_font_line_through, 'none', 'line-through', $use_important );

			$style .= et_builder_set_element_font_style( 'text-decoration-style', ( '' !== $font_line_style_default ), ( '' !== $font_line_style ), 'solid', $font_line_style, $use_important );

			$style .= et_builder_set_element_font_style( '-webkit-text-decoration-color', ( '' !== $font_line_color_default ), ( '' !== $font_line_color ), '', $font_line_color, $use_important );
			$style .= et_builder_set_element_font_style( 'text-decoration-color', ( '' !== $font_line_color_default ), ( '' !== $font_line_color ), '', $font_line_color, $use_important );

			$style = rtrim( $style );
		}

		return $style;
	}
endif;

if ( ! function_exists( 'et_builder_set_element_font_style' ) ) :
	/**
	 * Set element font style.
	 *
	 * @param string $property CSS property.
	 * @param bool   $default @todo Add parameter doc.
	 * @param bool   $value @todo Add parameter doc.
	 * @param string $property_default @todo Add parameter doc.
	 * @param string $property_value Property value.
	 * @param string $use_important Whether use !important specificity.
	 */
	function et_builder_set_element_font_style( $property, $default, $value, $property_default, $property_value, $use_important ) {
		$style = '';

		if ( $value && ! $default ) {
			$style = sprintf(
				'%1$s: %2$s%3$s; ',
				esc_html( $property ),
				$property_value,
				( $use_important ? ' !important' : '' )
			);
		} elseif ( ! $value && $default ) {
			$style = sprintf(
				'%1$s: %2$s%3$s; ',
				esc_html( $property ),
				$property_default,
				( $use_important ? ' !important' : '' )
			);
		}

		return $style;
	}
endif;

if ( ! function_exists( 'et_builder_set_reset_font_style' ) ) :
	/**
	 * Set reset CSS style declaration to normalize the existing font styles value from another font
	 * options group.
	 *
	 * @since 3.23
	 *
	 * @param  string  $current_value  Current font option value.
	 * @param  string  $compared_value Compared or parent font option value.
	 * @param  boolean $use_important  Imporant status.
	 * @return string                  Generated reset font styles.
	 */
	function et_builder_set_reset_font_style( $current_value, $compared_value, $use_important = false ) {
		// Being save, ensure current and compared values are valid string.
		if ( ! is_string( $current_value ) || ! is_string( $compared_value ) ) {
			return '';
		}

		$current_pieces  = explode( '|', $current_value );
		$compared_pieces = explode( '|', $compared_value );
		if ( empty( $current_pieces ) || empty( $compared_pieces ) ) {
			return '';
		}

		// Current value font style status.
		$is_current_italic       = isset( $current_pieces[2] ) && 'on' === $current_pieces[2];
		$is_current_uppercase    = isset( $current_pieces[3] ) && 'on' === $current_pieces[3];
		$is_current_underline    = isset( $current_pieces[4] ) && 'on' === $current_pieces[4];
		$is_current_small_caps   = isset( $current_pieces[5] ) && 'on' === $current_pieces[5];
		$is_current_line_through = isset( $current_pieces[6] ) && 'on' === $current_pieces[6];

		// Compated value font style status.
		$is_compared_italic       = isset( $compared_pieces[2] ) && 'on' === $compared_pieces[2];
		$is_compared_uppercase    = isset( $compared_pieces[3] ) && 'on' === $compared_pieces[3];
		$is_compared_underline    = isset( $compared_pieces[4] ) && 'on' === $compared_pieces[4];
		$is_compared_small_caps   = isset( $compared_pieces[5] ) && 'on' === $compared_pieces[5];
		$is_compared_line_through = isset( $compared_pieces[6] ) && 'on' === $compared_pieces[6];

		$style     = '';
		$important = $use_important ? ' !important' : '';

		// Reset italic.
		if ( ! $is_current_italic && $is_compared_italic ) {
			$style .= "font-style: normal{$important};";
		}

		// Reset uppercase.
		if ( ! $is_current_uppercase && $is_compared_uppercase ) {
			$style .= "text-transform: none{$important};";
		}

		// Reset small caps.
		if ( ! $is_current_small_caps && $is_compared_small_caps ) {
			$style .= "font-variant: none{$important};";
		}

		// Reset underline.
		if ( ! $is_current_underline && $is_compared_underline ) {
			$underline_value = $is_current_line_through || $is_compared_line_through ? 'line-through' : 'none';
			$style          .= "text-decoration: {$underline_value}{$important};";
		}

		// Reset line through.
		if ( ! $is_current_line_through && $is_compared_line_through ) {
			$line_through_value = $is_current_underline || $is_compared_underline ? 'underline' : 'none';
			$style             .= "text-decoration: {$line_through_value}{$important};";
		}

		return $style;
	}
endif;

if ( ! function_exists( 'et_builder_get_element_style_css' ) ) :
	/**
	 * Return element css style.
	 *
	 * @param string $value Property value.
	 * @param string $property Css property.
	 * @param bool   $use_important Whether add !important specificity.
	 */
	function et_builder_get_element_style_css( $value, $property = 'margin', $use_important = false ) {
		$style = '';

		$values = explode( '|', $value );

		if ( ! empty( $values ) ) {
			$element_style = '';
			$values        = array_map( 'trim', $values );
			$positions     = array(
				'top',
				'right',
				'bottom',
				'left',
			);

			foreach ( $positions as $i => $position ) {
				if ( ! isset( $values[ $i ] ) || '' === $values[ $i ] ) {
					continue;
				}

				$element_style .= sprintf(
					'%3$s-%1$s: %2$s%4$s; ',
					esc_attr( $position ),
					esc_attr( et_builder_process_range_value( $values[ $i ], $property ) ),
					esc_attr( $property ),
					( $use_important ? ' !important' : '' )
				);
			}

			$style .= rtrim( $element_style );
		}

		return $style;
	}
endif;

if ( ! function_exists( 'et_builder_enqueue_font' ) ) :
	/**
	 * Enqueue fonts.
	 *
	 * @param string $font_name font name.
	 */
	function et_builder_enqueue_font( $font_name ) {
		global $et_fonts_queue, $et_user_fonts_queue;

		$fonts                 = et_builder_get_fonts();
		$websafe_fonts         = et_builder_get_websafe_fonts();
		$user_fonts            = et_builder_get_custom_fonts();
		$removed_fonts_mapping = et_builder_old_fonts_mapping();

		if ( array_key_exists( $font_name, $user_fonts ) ) {
			$et_user_fonts_queue[ $font_name ] = $user_fonts[ $font_name ];
			return;
		}

		// Skip enqueueing if font name is not found. Possibly happen if support for particular font need to be dropped.
		if ( ! array_key_exists( $font_name, $fonts ) && ! isset( $removed_fonts_mapping[ $font_name ] ) ) {
			return;
		}

		// Skip enqueueing for websafe fonts.
		if ( array_key_exists( $font_name, $websafe_fonts ) ) {
			return;
		}

		if ( isset( $removed_fonts_mapping[ $font_name ] ) ) {
			$font_name = $removed_fonts_mapping[ $font_name ]['parent_font'];
		}
		$font_character_set = $fonts[ $font_name ]['character_set'];

		global $shortname;

		// Force enabled subsets for existing sites once.
		if ( ! et_get_option( "{$shortname}_skip_font_subset_force", false ) ) {
			et_update_option( "{$shortname}_gf_enable_all_character_sets", 'on' );
			et_update_option( "{$shortname}_skip_font_subset_force", true );
		}

		// By default, only latin and latin-ext subsets are loaded, all available subsets can be enabled in ePanel.
		if ( 'false' === et_get_option( "{$shortname}_gf_enable_all_character_sets", 'false' ) ) {
			$latin_ext = '';

			if ( false !== strpos( $fonts[ $font_name ]['character_set'], 'latin-ext' ) ) {
				$latin_ext = ',latin-ext';
			}

			$font_character_set = "latin{$latin_ext}";
		}

		$font_name_slug = sprintf(
			'et-gf-%1$s',
			strtolower( str_replace( ' ', '-', $font_name ) )
		);

		$queued_font = array(
			'font'   => sprintf(
				'%s:%s',
				str_replace( ' ', '+', $font_name ),
				apply_filters( 'et_builder_set_styles', $fonts[ $font_name ]['styles'], $font_name )
			),
			'subset' => apply_filters( 'et_builder_set_character_set', $font_character_set, $font_name ),
		);

		// Enqueue google fonts.
		$et_fonts_queue[ $font_name_slug ] = $queued_font;
	}
endif;

if ( ! function_exists( 'et_builder_enqueue_user_fonts' ) ) :
	/**
	 * Load user fonts.
	 *
	 * @param array $et_user_fonts User fonts.
	 *
	 * @return The @font-face CSS at-rule.
	 */
	function et_builder_enqueue_user_fonts( $et_user_fonts ) {
		$output = '';
		// load user fonts.
		if ( ! empty( $et_user_fonts ) ) {
			foreach ( $et_user_fonts as $font_name => $font_data ) {
				if ( is_array( $font_data['font_url'] ) && ! empty( $font_data['font_url'] ) ) {
					// generate the @font-face src from the uploaded font files
					// all the font formats have to be added in certain order to provide the best browser support.
					$uploaded_files = array(
						'eot'   => array(
							'url'    => isset( $font_data['font_url']['eot'] ) ? $font_data['font_url']['eot'] : false,
							'format' => 'embedded-opentype',
						),
						'woff2' => array(
							'url'    => isset( $font_data['font_url']['woff2'] ) ? $font_data['font_url']['woff2'] : false,
							'format' => 'woff2',
						),
						'woff'  => array(
							'url'    => isset( $font_data['font_url']['woff'] ) ? $font_data['font_url']['woff'] : false,
							'format' => 'woff',
						),
						'ttf'   => array(
							'url'    => isset( $font_data['font_url']['ttf'] ) ? $font_data['font_url']['ttf'] : false,
							'format' => 'truetype',
						),
						'otf'   => array(
							'url'    => isset( $font_data['font_url']['otf'] ) ? $font_data['font_url']['otf'] : false,
							'format' => 'opentype',
						),
					);

					$font_src = '';

					foreach ( $uploaded_files as $ext => $file_data ) {
						if ( ! $file_data['url'] ) {
							continue;
						}

						$font_src .= '' === $font_src ? 'src: ' : ', ';

						$font_src .= sprintf(
							'url("%1$s%2$s") format("%3$s")',
							esc_url( $file_data['url'] ),
							'eot' === $ext ? '?#iefix' : '',
							esc_attr( $file_data['format'] )
						);
					}

					$output .= sprintf(
						'@font-face { font-family: "%1$s"; font-display: swap; %2$s %3$s; }',
						esc_attr( $font_name ),
						isset( $font_data['font_url']['eot'] ) ? sprintf( 'src: url(%1$s);', esc_url( $font_data['font_url']['eot'] ) ) : '',
						// Make sure to properly escape each individual piece of $font_src above.
						et_core_esc_previously( $font_src )
					);
				} else {
					$output .= sprintf( '@font-face { font-family: "%1$s"; font-display: swap; src: url(%2$s);}', esc_attr( $font_name ), esc_url( $font_data['font_url'] ) );
				}
			}
		}

		return $output;
	}
endif;

if ( ! function_exists( 'et_font_subset_force_check' ) ) :
	/**
	 * Font subset force check on theme activation.
	 */
	function et_font_subset_force_check() {
		global $shortname;

		if ( empty( $shortname ) || ! in_array( $shortname, array( 'divi', 'extra' ), true ) ) {
			return;
		}

		if ( ! et_get_option( "{$shortname}_skip_font_subset_force", false ) ) {
			et_update_option( "{$shortname}_skip_font_subset_force", true );
		}
	}
endif;
add_action( 'after_switch_theme', 'et_font_subset_force_check' );

/**
 * Preconnect to Google Fonts to allow async dns lookup.
 */
function et_builder_preconnect_google_fonts() {
	$feature_manager = ET_Builder_Google_Fonts_Feature::instance();
	$output_inline   = $feature_manager->is_option_enabled( 'google_fonts_inline' );

	if ( $output_inline && et_core_use_google_fonts() ) {
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />';
	}
}
add_action( 'wp_enqueue_scripts', 'et_builder_preconnect_google_fonts', 9 );

/**
 * Enqueue queued Google Fonts into WordPress' wp_enqueue_style as one request
 *
 * @return void
 */
function et_builder_print_font() {
	global $et_fonts_queue, $et_fonts_cache;

	// Bail if no queued google font found.
	if ( empty( $et_fonts_queue ) ) {
		return;
	}

	$fonts          = wp_list_pluck( $et_fonts_queue, 'font' );
	$subsets        = wp_list_pluck( $et_fonts_queue, 'subset' );
	$unique_subsets = array_unique( explode( ',', implode( ',', $subsets ) ) );

	// Get the google fonts for the current page that are stored as an option.
	$post_fonts_data = array();

	$post_id = is_singular() ? get_the_ID() : false;

	if ( false !== $post_id ) {
		$post_fonts_data = get_post_meta( $post_id, 'et_enqueued_post_fonts', true );
	}

	if ( ! is_array( $post_fonts_data ) ) {
		$post_fonts_data = array();
	}

	if ( empty( $post_fonts_data ) || ! is_array( $post_fonts_data['family'] ) || ! is_array( $post_fonts_data['subset'] ) ) {
		$post_fonts_data = array(
			'family' => array(),
			'subset' => array(),
		);
	}

	$google_fonts_feature_cache_key = ET_Builder_Google_Fonts_Feature::_get_cache_index();

	// We only need the difference in the fonts since the subsets might be needed
	// in cases where a new font is added to the page and it is not yet present
	// in the option cache.
	$cached_fonts          = $post_fonts_data['family'];
	$cached_subsets        = $post_fonts_data['subset'];
	$unique_cached_subsets = array_unique( explode( ',', implode( ',', $cached_subsets ) ) );
	$cached_cache_key      = ! empty( $post_fonts_data['cache_key'] ) ? $post_fonts_data['cache_key'] : $google_fonts_feature_cache_key;

	// compare if things have changed since the post cache.
	$fonts_diff     = array_diff( $fonts, $cached_fonts );
	$subsets_diff   = array_diff( $unique_subsets, $unique_cached_subsets );
	$cache_key_diff = $cached_cache_key !== $google_fonts_feature_cache_key;

	if ( ! $fonts_diff && ! $subsets_diff ) {
		// The `$fonts` variable stores all the fonts used on the page (cache does not matter)
		// while the `$cached_fonts` one only stores the fonts that were lastly saved into
		// the post meta. When we run `array_diff` we would only get a result if there
		// are new fonts present on the page that are not yet cached. However if some
		// of the cached fonts are no longer in use this will not be caught by the
		// `array_diff`. To fix this if the item count in `$fonts` is different
		// than the one in `$cached_fonts` we update the post meta with the
		// data from the `$fonts` variable to force unused fonts removal.
		if ( count( $fonts ) !== count( $cached_fonts ) || ! empty( $cache_key_diff ) ) {
			// Update the option for the current page with the new data.
			$post_fonts_data = array(
				'family' => et_core_sanitized_previously( $fonts ),
				'subset' => et_core_sanitized_previously( $unique_subsets ),
			);

			// Do not update post meta here, save the value to global variable and update it at `shutdown` hook.
			// Prevents object cache error on GoDaddy + Woocommerce websites.
			$et_fonts_cache = et_core_sanitized_previously( $post_fonts_data );
		}

		return;
	}

	if ( et_core_use_google_fonts() ) {
		// Append combined subset at the end of the URL as different query string.
		$google_fonts_url_args = array(
			'family'  => implode( '|', $fonts ),
			'subset'  => implode( ',', $unique_subsets ),
			'display' => 'swap',
		);

		$feature_manager  = ET_Builder_Google_Fonts_Feature::instance();
		$google_fonts_url = $feature_manager->get_google_fonts_url( $google_fonts_url_args );
		$output_inline    = $feature_manager->is_option_enabled( 'google_fonts_inline' );

		if ( $output_inline ) {
			$contents = $feature_manager->get(
				'google-fonts',
				function() use ( $feature_manager, $google_fonts_url ) {
					return $feature_manager->fetch( $google_fonts_url );
				},
				sanitize_text_field( et_core_esc_previously( $google_fonts_url ) )
			);

			// if something went wrong fetching the contents.
			if ( false === $contents ) {
				// phpcs:ignore WordPress.WP.EnqueuedResourceParameters --  Google fonts api does not have versions
				wp_enqueue_style( 'et-builder-googlefonts', et_core_esc_previously( $google_fonts_url ), array(), null );
			} else {
				echo '<style id="et-builder-googlefonts-inline">' . $contents . '</style>';
			}
		} else {
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters --  Google fonts api does not have versions
			wp_enqueue_style( 'et-builder-googlefonts', et_core_esc_previously( $google_fonts_url ), array(), null );
		}
	}

	// Create a merge of the existing fonts and subsets in the option and the newly added ones.
	$updated_fonts   = array_merge( $fonts, $post_fonts_data['family'] );
	$updated_subsets = array_merge( $unique_subsets, $post_fonts_data['subset'] );

	// Update the option for the current page with the new data.
	$post_fonts_data = array(
		'family'    => array_unique( $updated_fonts ),
		'subset'    => array_unique( $updated_subsets ),
		'cache_key' => $google_fonts_feature_cache_key,
	);

	// Do not update post meta here, save the value to global variable and update it at `shutdown` hook.
	// Prevents object cache error on GoDaddy + Woocommerce websites.
	$et_fonts_cache = et_core_sanitized_previously( $post_fonts_data );
}
add_action( 'wp_footer', 'et_builder_print_font' );

/**
 * Update Fonts Cache in post meta
 * Run this function on shutdown hook to prevents object cache error on GoDaddy + Woocommerce websites
 *
 * @return void
 */
function et_builder_update_fonts_cache() {
	global $et_fonts_cache;

	if ( ! isset( $et_fonts_cache ) || empty( $et_fonts_cache ) ) {
		return;
	}

	$post_id = is_singular() ? get_the_ID() : false;

	if ( ! $post_id ) {
		return;
	}

	update_post_meta( $post_id, 'et_enqueued_post_fonts', et_core_sanitized_previously( $et_fonts_cache ) );
}
add_action( 'shutdown', 'et_builder_update_fonts_cache' );

/**
 * Enqueue queued Google Fonts into WordPress' wp_enqueue_style as one request (cached version)
 *
 * @return void
 */
function et_builder_preprint_font() {
	// Return if this is not a post or a page.
	if ( ! is_singular() || ! et_core_use_google_fonts() ) {
		return;
	}

	$post_id = get_the_ID();

	$post_fonts_data = get_post_meta( $post_id, 'et_enqueued_post_fonts', true );

	// Bail early if the post fonts data is not an array.
	if ( ! is_array( $post_fonts_data ) ) {
		return;
	}

	$fonts_family = isset( $post_fonts_data['family'] ) ? $post_fonts_data['family'] : '';
	$fonts_subset = isset( $post_fonts_data['subset'] ) ? $post_fonts_data['subset'] : '';

	// We expect both 'family' and 'subset' to contain an array so bail early if one of them does not contain an array.
	if ( ! is_array( $fonts_family ) || ! is_array( $fonts_subset ) ) {
		return;
	}

	$unique_subsets = array_filter( $fonts_subset );

	$google_fonts_url_args = array(
		'family'  => implode( '|', $fonts_family ),
		'subset'  => implode( ',', $unique_subsets ),
		'display' => 'swap',
	);

	$feature_manager  = ET_Builder_Google_Fonts_Feature::instance();
	$google_fonts_url = $feature_manager->get_google_fonts_url( $google_fonts_url_args );
	$output_inline    = $feature_manager->is_option_enabled( 'google_fonts_inline' );

	if ( $output_inline ) {
		$contents = $feature_manager->get(
			'google-fonts',
			function() use ( $feature_manager, $google_fonts_url ) {
				return $feature_manager->fetch( $google_fonts_url );
			},
			sanitize_text_field( et_core_esc_previously( $google_fonts_url ) )
		);

		// if something went wrong fetching the contents.
		if ( false === $contents ) {
			// phpcs:ignore WordPress.WP.EnqueuedResourceParameters --  Google fonts api does not have versions
			wp_enqueue_style( 'et-builder-googlefonts-cached', et_core_esc_previously( $google_fonts_url ), array(), null );
		} else {
			echo '<style id="et-builder-googlefonts-cached-inline">' . $contents . '</style>';
		}
	} else {
		// phpcs:ignore WordPress.WP.EnqueuedResourceParameters -- Google fonts api does not have versions.
		wp_enqueue_style( 'et-builder-googlefonts-cached', et_core_esc_previously( $google_fonts_url ), array(), null );
	}
}
add_action( 'wp_enqueue_scripts', 'et_builder_preprint_font' );

if ( ! function_exists( 'et_pb_get_page_custom_css' ) ) :
	/**
	 * Return page custom style.
	 *
	 * @param int $post_id post id.
	 */
	function et_pb_get_page_custom_css( $post_id = 0 ) {
		$post_id          = $post_id ? $post_id : get_the_ID();
		$post_type        = get_post_type( $post_id );
		$overflow         = et_pb_overflow();
		$page_id          = apply_filters( 'et_pb_page_id_custom_css', $post_id );
		$exclude_defaults = true;
		$page_settings    = ET_Builder_Settings::get_values( 'page', $page_id, $exclude_defaults );
		$selector_prefix  = '.et-l--post';

		switch ( $post_type ) {
			case ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE:
				$selector_prefix = '.et-l--header';
				break;

			case ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE:
				$selector_prefix = '.et-l--body';
				break;

			case ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE:
				$selector_prefix = '.et-l--footer';
				break;
		}

		$wrap_post_id = $page_id;

		if ( et_theme_builder_is_layout_post_type( $post_type ) ) {
			$main_post_id = ET_Post_Stack::get_main_post_id();

			if ( $main_post_id ) {
				$wrap_post_id = $main_post_id;
			}
		}

		$wrap_selector = et_pb_is_pagebuilder_used( $wrap_post_id ) && ( et_is_builder_plugin_active() || et_builder_post_is_of_custom_post_type( $wrap_post_id ) );

		if ( $wrap_selector ) {
			$selector_prefix = ' ' . ET_BUILDER_CSS_PREFIX . $selector_prefix;
		}

		$output = get_post_meta( $page_id, '_et_pb_custom_css', true );

		if ( isset( $page_settings['et_pb_light_text_color'] ) ) {
			$output .= sprintf(
				'%2$s .et_pb_bg_layout_dark { color: %1$s !important; }',
				esc_html( $page_settings['et_pb_light_text_color'] ),
				esc_html( $selector_prefix )
			);
		}

		if ( isset( $page_settings['et_pb_dark_text_color'] ) ) {
			$output .= sprintf(
				'%2$s .et_pb_bg_layout_light { color: %1$s !important; }',
				esc_html( $page_settings['et_pb_dark_text_color'] ),
				esc_html( $selector_prefix )
			);
		}

		if ( isset( $page_settings['et_pb_content_area_background_color'] ) ) {
			$content_area_bg_selector = et_is_builder_plugin_active() ? $selector_prefix : ' .page.et_pb_pagebuilder_layout #main-content';
			$output                  .= sprintf(
				'%1$s { background-color: %2$s; }',
				esc_html( $content_area_bg_selector ),
				esc_html( $page_settings['et_pb_content_area_background_color'] )
			);
		}

		if ( isset( $page_settings['et_pb_section_background_color'] ) ) {
			$output .= sprintf(
				'%2$s > .et_builder_inner_content > .et_pb_section { background-color: %1$s; }',
				esc_html( $page_settings['et_pb_section_background_color'] ),
				esc_html( $selector_prefix )
			);
		}

		$overflow_x = $overflow->get_value_x( $page_settings, '', 'et_pb_' );
		$overflow_y = $overflow->get_value_y( $page_settings, '', 'et_pb_' );

		if ( ! empty( $overflow_x ) ) {
			$output .= sprintf(
				'%2$s .et_builder_inner_content { overflow-x: %1$s; }',
				esc_html( $overflow_x ),
				esc_html( $selector_prefix )
			);
		}

		if ( ! empty( $overflow_y ) ) {
			$output .= sprintf(
				'%2$s .et_builder_inner_content { overflow-y: %1$s; }',
				esc_html( $overflow_y ),
				esc_html( $selector_prefix )
			);
		}

		if ( isset( $page_settings['et_pb_page_z_index'] ) && '' !== $page_settings['et_pb_page_z_index'] ) {
			$output .= sprintf(
				'%2$s .et_builder_inner_content { z-index: %1$s; }',
				esc_html( $page_settings['et_pb_page_z_index'] ),
				esc_html( '.et-db #et-boc .et-l' . $selector_prefix )
			);
		}

		return apply_filters( 'et_pb_page_custom_css', $output );
	}
endif;

if ( ! function_exists( 'et_pb_video_oembed_data_parse' ) ) :
	/**
	 * Remove scheme from video url.
	 *
	 * @param string $return The returned oEmbed HTML.
	 * @param object $data   A data object result from an oEmbed provider.
	 * @param string $url    The URL of the content to be embedded.
	 */
	function et_pb_video_oembed_data_parse( $return, $data, $url ) {
		if ( isset( $data->thumbnail_url ) ) {
			return esc_url( str_replace( array( 'https://', 'http://' ), '//', $data->thumbnail_url ), array( 'http' ) );
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'et_pb_check_oembed_provider' ) ) :
	/**
	 * Returns the corresponding oEmbed provider's URL.
	 *
	 * @param string $url oembed url.
	 *
	 * @reurn string
	 */
	function et_pb_check_oembed_provider( $url ) {
		if ( version_compare( $GLOBALS['wp_version'], '5.3', '<' ) ) {
			require_once ABSPATH . WPINC . '/class-oembed.php';
		} else {
			require_once ABSPATH . WPINC . '/class-wp-oembed.php';
		}

		$oembed = _wp_oembed_get_object();

		return $oembed->get_provider( esc_url( $url ), array( 'discover' => false ) );
	}
endif;

if ( ! function_exists( 'et_builder_get_oembed' ) ) :
	/**
	 * Get cached embedded item on page load.
	 *
	 * Use the item source as the key, so some modules with the same item can share it.
	 *
	 * @since 4.5.2
	 *
	 * @param  string  $url      Item URL.
	 * @param  string  $group    Item group to set different cache for the same key.
	 * @param  boolean $is_cache Whether to use WordPress Object Cache or not.
	 * @return string
	 */
	function et_builder_get_oembed( $url, $group = 'video', $is_cache = true ) {
		$item_src = esc_url( $url );

		// Temporarily save embedded item on page load only. Use the item source as the
		// key, so some modules with the same item can share it.
		$item_embed = $is_cache ? wp_cache_get( $item_src, $group ) : false;

		if ( ! $item_embed ) {
			$item_embed = wp_oembed_get( $item_src );

			if ( $is_cache ) {
				wp_cache_set( $item_src, $item_embed, $group );
			}
		}

		return apply_filters( 'et_builder_get_oembed', $item_embed, $url, $group, $is_cache );
	}
endif;

if ( ! function_exists( 'et_pb_set_video_oembed_thumbnail_resolution' ) ) :
	/**
	 * Replace YouTube video thumbnails to high resolution if the high resolution image exists.
	 *
	 * @param string $image_src thumbnail image src.
	 * @param string $resolution thumbnail image resolutions.
	 *
	 * @return string
	 */
	function et_pb_set_video_oembed_thumbnail_resolution( $image_src, $resolution = 'default' ) {
		// Replace YouTube video thumbnails to high resolution if the high resolution image exists.
		if ( 'high' === $resolution && false !== strpos( $image_src, 'hqdefault.jpg' ) ) {
			$high_res_image_src  = str_replace( 'hqdefault.jpg', 'maxresdefault.jpg', $image_src );
			$protocol            = is_ssl() ? 'https://' : 'http://';
			$processed_image_url = esc_url( str_replace( '//', $protocol, $high_res_image_src ), array( 'http', 'https' ) );
			$response            = wp_remote_get( $processed_image_url, array( 'timeout' => 30 ) );

			// Youtube doesn't guarantee that high res image exists for any video, so we need to check whether it exists and fallback to default image in case of error.
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
				return $image_src;
			}

			return $high_res_image_src;
		}

		return $image_src;
	}
endif;

/**
 * Return all registered sidebars.
 *
 * @return mixed|void
 */
function et_builder_get_widget_areas_list() {
	global $wp_registered_sidebars;

	$widget_areas = array();

	foreach ( $wp_registered_sidebars as $sidebar_key => $sidebar ) {
		$widget_areas[ $sidebar_key ] = array(
			'name' => $sidebar['name'],
		);
	}

	return apply_filters( 'et_builder_get_widget_areas_list', $widget_areas );
}

if ( ! function_exists( 'et_builder_get_widget_areas' ) ) :
	/**
	 * Return widget areas dropdown html.
	 */
	function et_builder_get_widget_areas() {
		$wp_registered_sidebars = et_builder_get_widget_areas_list();
		$et_pb_widgets          = get_theme_mod( 'et_pb_widgets' );

		$output = '<select name="et_pb_area" id="et_pb_area">';

		foreach ( $wp_registered_sidebars as $id => $options ) {
			$selected = sprintf(
				'<%%= typeof( et_pb_area ) !== "undefined" && "%1$s" === et_pb_area ?  " selected=\'selected\'" : "" %%>',
				esc_html( $id )
			);

			$output .= sprintf(
				'<option value="%1$s"%2$s>%3$s</option>',
				esc_attr( $id ),
				$selected,
				esc_html( $options['name'] )
			);
		}

		$output .= '</select>';

		return $output;
	}
endif;

if ( ! function_exists( 'et_pb_export_layouts_interface' ) ) :
	/**
	 * Display 'Manage Categories' button at top in Layout wp admin edit screen.
	 */
	function et_pb_export_layouts_interface() {
		if ( ! current_user_can( 'export' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to export the content of this site.', 'et_builder' ) );
		}

		?>
		<a href="<?php echo et_core_esc_wp( admin_url( 'edit-tags.php?taxonomy=layout_category' ) ); ?>" id="et_load_category_page"><?php esc_html_e( 'Manage Categories', 'et_builder' ); ?></a>
		<a href="<?php echo et_core_esc_wp( admin_url( 'edit-tags.php?taxonomy=layout_tag' ) ); ?>" id="et_load_category_page"><?php esc_html_e( 'Manage Tags', 'et_builder' ); ?></a>

		<?php
			echo et_core_esc_previously( et_builder_portability_link( 'et_builder_layouts', array( 'class' => 'et-pb-portability-button' ) ) );
	}
endif;

add_action( 'export_wp', 'et_pb_edit_export_query' );
/**
 * Add filter for the export query.
 */
function et_pb_edit_export_query() {
	add_filter( 'query', 'et_pb_edit_export_query_filter' );
}

/**
 * Export query.
 *
 * @param WP_Query $query object.
 *
 * @return string|void
 */
function et_pb_edit_export_query_filter( $query ) {
	// Apply filter only once.
	remove_filter( 'query', 'et_pb_edit_export_query_filter' );

	et_core_nonce_verified_previously();

	// ensure user can export.
	if ( ! current_user_can( 'export' ) ) {
		return $query;
	}

	global $wpdb;

	$content = ! empty( $_GET['content'] ) ? sanitize_text_field( $_GET['content'] ) : '';

	if ( ET_BUILDER_LAYOUT_POST_TYPE !== $content ) {
		return $query;
	}

	$sql            = '';
	$i              = 0;
	$possible_types = array(
		'layout',
		'section',
		'row',
		'module',
		'fullwidth_section',
		'specialty_section',
		'fullwidth_module',
	);

	foreach ( $possible_types as $template_type ) {
		$selected_type = 'et_pb_template_' . $template_type;

		if ( isset( $_GET[ $selected_type ] ) ) {
			if ( 0 === $i ) {
				$sql = " AND ( `{$wpdb->term_relationships}`.term_taxonomy_id = %d";
			} else {
				$sql .= " OR `{$wpdb->term_relationships}`.term_taxonomy_id = %d";
			}

			$sql_args[] = (int) $_GET[ $selected_type ];

			$i++;
		}
	}

	if ( '' !== $sql ) {
		$sql  .= ' )';
		$sql   = sprintf(
			'SELECT ID FROM %4$s
			 INNER JOIN %3$s ON ( %4$s.ID = %3$s.object_id )
			 WHERE %4$s.post_type = "%1$s"
			 AND %4$s.post_status != "auto-draft"
			 %2$s',
			ET_BUILDER_LAYOUT_POST_TYPE,
			$sql,
			$wpdb->term_relationships,
			$wpdb->posts
		);
		$query = $wpdb->prepare( $sql, $sql_args ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Value of the $sql is safely prepared above.
	}

	return $query;
}

/**
 * Initialize builder metabox in BFB.
 */
function et_pb_setup_theme() {
	add_action( 'add_meta_boxes', 'et_pb_add_custom_box', 10, 2 );
	add_action( 'add_meta_boxes', 'et_builder_prioritize_meta_box', 999999 );
	add_filter( 'hidden_meta_boxes', 'et_pb_hidden_meta_boxes' );
}
add_action( 'init', 'et_pb_setup_theme', 11 );

/**
 * Override metaboxes order to ensure Divi Builder metabox has top position.
 *
 * @since 3.17.3
 *
 * @param string $value Custom value.
 *
 * @return string
 */
function et_builder_override_meta_boxes_order( $value ) {
	static $custom = false;

	// Store the value on the first call;.
	$custom = false === $custom ? $value : $custom;

	return $custom;
}

/**
 * Forcefully prioritize the Divi Builder metabox to be at the top.
 * User drag&drop metabox order customizations are still supported.
 * Required since not all plugins properly register their metaboxes in the add_meta_boxes hook.
 *
 * @since 3.17.2
 *
 * @return void
 */
function et_builder_prioritize_meta_box() {
	global $wp_meta_boxes;

	$screen = get_current_screen();

	// Only prioritize Divi Builder metabox if current post type has Divi Builder enabled.
	if ( ! in_array( $screen->post_type, et_builder_get_enabled_builder_post_types(), true ) ) {
		return;
	}

	// Get custom order.
	$page        = $screen->id;
	$option_name = "meta-box-order_$page";
	$custom      = get_user_option( $option_name );

	foreach ( $wp_meta_boxes as $page => $contexts ) {
		foreach ( $contexts as $context => $priorities ) {
			foreach ( $priorities as $priority => $boxes ) {
				if ( ! isset( $boxes[ ET_BUILDER_LAYOUT_POST_TYPE ] ) ) {
					continue;
				}

				$divi = $boxes[ ET_BUILDER_LAYOUT_POST_TYPE ];

				unset( $boxes[ ET_BUILDER_LAYOUT_POST_TYPE ] );

				// phpcs:ignore WordPress.WP.GlobalVariablesOverride -- Push "The Divi Builder" metabox at top by updating `$wp_meta_boxes`.
				$wp_meta_boxes[ $page ][ $context ][ $priority ] = array_merge( array( ET_BUILDER_LAYOUT_POST_TYPE => $divi ), $boxes );

				// If our mbox is the first one in custom ordering.
				if ( is_array( $custom )
					&& isset( $custom[ $context ] )
					&& 0 === strpos( $custom[ $context ], ET_BUILDER_LAYOUT_POST_TYPE ) ) {
					// Find all metaboxes that are not included in custom order.
					$sorted = explode( ',', $custom[ $context ] );
					$add    = array_diff( array_keys( $boxes ), $sorted );

					if ( $add ) {
						// Add them after Divi.
						$custom[ $context ] = implode( ',', array_merge( array( ET_BUILDER_LAYOUT_POST_TYPE ), $add, array_slice( $sorted, 1 ) ) );

						// Store the custom value.
						et_builder_override_meta_boxes_order( $custom );
						// and override `get_user_option` so WP will use it.
						add_filter( "get_user_option_{$option_name}", 'et_builder_override_meta_boxes_order' );
					}
				}
			}
		}
	}
}

/**
 * The page builders require the WP Heartbeat script in order to function. We ensure the heartbeat
 * is loaded with the page builders by scheduling this callback to run right before scripts
 * are output to the footer. {@see 'admin_enqueue_scripts', 'wp_footer'}
 */
function et_builder_maybe_ensure_heartbeat_script() {
	// Don't perform any actions on 'wp_footer' if VB is not active.
	if ( 'wp_footer' === current_filter() && empty( $_GET['et_fb'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		return;
	}

	// We have to check both 'registered' AND 'enqueued' to cover cases where heartbeat has been
	// de-registered because 'enqueued' will return `true` for a de-registered script at this stage.
	$heartbeat_okay = wp_script_is( 'heartbeat', 'registered' ) && wp_script_is( 'heartbeat', 'enqueued' );
	$autosave_okay  = wp_script_is( 'autosave', 'registered' ) && wp_script_is( 'autosave', 'enqueued' );

	if ( '1' === et_()->array_get( $_GET, 'et_bfb', '0' ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		// Do not enqueue WP autosave in the BFB iframe because it doesn't include #content / #excerpt / #title
		// and will result in empty (browser) backups (note: still included in top window).
		$autosave_okay = true;

		wp_dequeue_script( 'autosave' );
	}

	if ( $heartbeat_okay && $autosave_okay ) {
		return;
	}

	$suffix = SCRIPT_DEBUG ? '' : '.min';

	// phpcs:disable WordPress.WP.EnqueuedResourceParameters -- Version numbers are not set to load latest scripts from the WP Core.
	if ( ! $heartbeat_okay ) {
		$heartbeat_src = "/wp-includes/js/heartbeat{$suffix}.js";
		// wp-hooks was introduced in WP 5.0.
		$deps = wp_script_is( 'wp-hooks', 'registered' ) ? array( 'jquery', 'wp-hooks' ) : array( 'jquery' );
		wp_enqueue_script( 'heartbeat', $heartbeat_src, $deps, false, true );
		wp_localize_script( 'heartbeat', 'heartbeatSettings', apply_filters( 'heartbeat_settings', array() ) );
	}

	if ( ! $autosave_okay ) {
		$autosave_src = "/wp-includes/js/autosave{$suffix}.js";
		wp_enqueue_script( 'autosave', $autosave_src, array( 'heartbeat' ), false, true );
	}
	// phpcs:enable
}
add_action( 'admin_print_scripts-post-new.php', 'et_builder_maybe_ensure_heartbeat_script', 9 );
add_action( 'admin_print_scripts-post.php', 'et_builder_maybe_ensure_heartbeat_script', 9 );
add_action( 'wp_footer', 'et_builder_maybe_ensure_heartbeat_script', 19 );

/**
 * Set builder post type.
 *
 * @param string $post_type post type.
 */
function et_builder_set_post_type( $post_type = '' ) {
	global $et_builder_post_type, $post;

	$et_builder_post_type = ! empty( $post_type ) ? $post_type : $post->post_type;
}

/**
 * Saves Metabox settings.
 *
 * @since 3.29.2 Included check to verify if constant exists before use.
 *           Throws error otherwise from PHP7.2.x
 *
 * @param int     $post_id post id.
 * @param WP_Post $post object.
 *
 * @return int
 */
function et_pb_metabox_settings_save_details( $post_id, $post ) {
	global $pagenow;

	if ( 'post.php' !== $pagenow ) {
		return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// do not update builder post meta when Preview is loading.
	if ( isset( $_POST['wp-preview'] ) && 'dopreview' === $_POST['wp-preview'] ) {
		return $post_id;
	}

	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return $post_id;
	}

	if ( ! isset( $_POST['et_pb_settings_nonce'] ) || ! wp_verify_nonce( $_POST['et_pb_settings_nonce'], basename( __FILE__ ) ) ) { // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- The nonce value is used only for comparision in the `wp_verify_nonce`.
		return $post_id;
	}

	if ( isset( $_POST['et_pb_use_builder'] ) ) {
		$et_pb_use_builder_input = sanitize_text_field( $_POST['et_pb_use_builder'] );

		update_post_meta( $post_id, '_et_pb_use_builder', $et_pb_use_builder_input );

		if ( ! empty( $_POST['et_builder_version'] ) ) {
			update_post_meta( $post_id, '_et_builder_version', sanitize_text_field( $_POST['et_builder_version'] ) );
		}

		$et_pb_show_page_creation_input = isset( $_POST['et_pb_show_page_creation'] ) ? sanitize_text_field( $_POST['et_pb_show_page_creation'] ) : false;

		if ( 'on' === $et_pb_show_page_creation_input ) {
			// Set page creation flow to on.
			update_post_meta( $post_id, '_et_pb_show_page_creation', 'on' );
		} elseif ( 'off' === $et_pb_show_page_creation_input ) {
			// Delete page creation flow.
			delete_post_meta( $post_id, '_et_pb_show_page_creation' );
		} elseif ( false === $et_pb_show_page_creation_input && 'on' === $et_pb_use_builder_input ) {
			$et_pb_show_page_creation_meta = get_post_meta( $post_id, '_et_pb_show_page_creation', true );

			// Strip non-printable characters.
			$post_content_cleaned = preg_replace( '/[\x00-\x1F\x7F]/u', '', $post->post_content );

			preg_match_all( '/\[et_pb_section(.*?)?\]\[et_pb_row(.*?)?\]\[et_pb_column(.*?)?\](.+?)\[\/et_pb_column\]\[\/et_pb_row\]\[\/et_pb_section\]/m', $post_content_cleaned, $matches );
			if ( isset( $matches[4] ) && ! empty( $matches[4] ) ) {
				if ( 'on' === $et_pb_show_page_creation_meta ) {
					// Set page creation flow to on.
					update_post_meta( $post_id, '_et_pb_show_page_creation', 'off' );
				}
			} else {
				delete_post_meta( $post_id, '_et_pb_show_page_creation' );
			}
		}

		if ( 'on' !== $et_pb_use_builder_input ) {
			if ( defined( 'ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY' ) ) {
				delete_post_meta( $post_id, ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY );
			}
		}
	} else {
		delete_post_meta( $post_id, '_et_pb_use_builder' );
		delete_post_meta( $post_id, '_et_builder_version' );
		if ( defined( 'ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY' ) ) {
			delete_post_meta( $post_id, ET_BUILDER_WC_PRODUCT_PAGE_CONTENT_STATUS_META_KEY );
		}
	}

	/**
	 * The et_save_post hook.
	 *
	 * @hooked et_builder_set_product_page_layout_meta - 10
	 *
	 * @param int $post_id
	 */
	do_action( 'et_save_post', $post_id );

	// Do not process Page Settings if BFB is enabled. Were saving it via ajax.
	if ( et_builder_bfb_enabled() ) {
		// But we still need to save old content.
		if ( isset( $_POST['et_pb_old_content'] ) ) {
			update_post_meta( $post_id, '_et_pb_old_content', $_POST['et_pb_old_content'] );
			// WooCommerce Modules needs the following hook.

			/**
			 * Fires after the `_et_pb_old_content` post meta is updated.
			 *
			 * In case you want to over-ride `_et_pb_old_content` content, this is the hook you should use.
			 *
			 * @see et_builder_wc_long_description_metabox_save()
			 *
			 * @since 3.29
			 *
			 * @param int $post_id Post ID.
			 * $param WP_Post $post The Post.
			 * $param array $_POST  Request variables. This could be used for Nonce verification, etc.
			 */
			do_action( 'et_pb_old_content_updated', $post_id, $post, $_POST );
		} else {
			delete_post_meta( $post_id, '_et_pb_old_content' );
		}
		return $post_id;
	}

	// Only run AB Testing-related update sequence if AB Testing is allowed.
	if ( et_pb_is_allowed( 'ab_testing' ) ) {
		// Delete AB Testing settings' autosave.
		delete_post_meta( $post_id, '_et_pb_use_ab_testing_draft' );
		delete_post_meta( $post_id, '_et_pb_ab_subjects_draft' );

		if ( isset( $_POST['et_pb_use_ab_testing'] ) && in_array( $_POST['et_pb_use_ab_testing'], array( 'on', 'off' ), true ) ) {
			update_post_meta( $post_id, '_et_pb_use_ab_testing', sanitize_text_field( $_POST['et_pb_use_ab_testing'] ) );

			if ( 'on' === $_POST['et_pb_use_ab_testing'] ) {
				if ( ! get_post_meta( $post_id, '_et_pb_ab_testing_id', true ) ) {
					update_post_meta( $post_id, '_et_pb_ab_testing_id', wp_rand() );
				}
			} else {
				delete_post_meta( $post_id, '_et_pb_ab_testing_id' );
				delete_post_meta( $post_id, 'et_pb_subjects_cache' );
				et_pb_ab_remove_stats( $post_id );
			}
		} else {
			delete_post_meta( $post_id, '_et_pb_use_ab_testing' );
			delete_post_meta( $post_id, '_et_pb_ab_testing_id' );
		}

		if ( isset( $_POST['et_pb_ab_subjects'] ) && '' !== $_POST['et_pb_ab_subjects'] ) {
			update_post_meta( $post_id, '_et_pb_ab_subjects', et_prevent_duplicate_item( sanitize_text_field( $_POST['et_pb_ab_subjects'] ), ',' ) );
		} else {
			delete_post_meta( $post_id, '_et_pb_ab_subjects' );
		}

		if ( isset( $_POST['et_pb_ab_goal_module'] ) && '' !== $_POST['et_pb_ab_goal_module'] ) {
			update_post_meta( $post_id, '_et_pb_ab_goal_module', sanitize_text_field( $_POST['et_pb_ab_goal_module'] ) );
		} else {
			delete_post_meta( $post_id, '_et_pb_ab_goal_module' );
		}

		if ( isset( $_POST['et_pb_ab_stats_refresh_interval'] ) && '' !== $_POST['et_pb_ab_stats_refresh_interval'] ) {
			update_post_meta( $post_id, '_et_pb_ab_stats_refresh_interval', sanitize_text_field( $_POST['et_pb_ab_stats_refresh_interval'] ) );
		} else {
			delete_post_meta( $post_id, '_et_pb_ab_stats_refresh_interval' );
		}
	}

	if ( isset( $_POST['et_pb_old_content'] ) ) {
		update_post_meta( $post_id, '_et_pb_old_content', $_POST['et_pb_old_content'] );

		/**
		 * Fires after the `_et_pb_old_content` post meta is updated.
		 *
		 * In case you want to over-ride `_et_pb_old_content` content, this is the hook you should use.
		 *
		 * @see et_builder_wc_long_description_metabox_save()
		 *
		 * @since 3.29
		 *
		 * @param int $post_id Post ID.
		 * $param WP_Post $post The Post.
		 * $param array $_POST  Request variables. This could be used for Nonce verification, etc.
		 */
		do_action( 'et_pb_old_content_updated', $post_id, $post, $_POST );
	} else {
		delete_post_meta( $post_id, '_et_pb_old_content' );
	}

	et_builder_update_settings( null, $post_id );

	if ( isset( $_POST['et_pb_unsynced_global_attrs'] ) ) {
		$unsynced_options_array = stripslashes( sanitize_text_field( $_POST['et_pb_unsynced_global_attrs'] ) );
		update_post_meta( $post_id, '_et_pb_excluded_global_options', $unsynced_options_array );
	}

	return $post_id;
}
add_action( 'save_post', 'et_pb_metabox_settings_save_details', 10, 2 );

/**
 * Set et-saved-post-* cookie and delete et-saving-post-* cookie after post save.
 *
 * @param int     $post_id Post id.
 * @param WP_Post $post Object.
 *
 * @return mixed
 */
function et_pb_set_et_saved_cookie( $post_id, $post ) {
	global $pagenow;

	if ( 'post.php' !== $pagenow ) {
		return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	$post_type = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
		return $post_id;
	}

	if ( ! isset( $_POST['et_pb_settings_nonce'] ) || ! wp_verify_nonce( $_POST['et_pb_settings_nonce'], basename( __FILE__ ) ) ) { // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- The nonce value is used only for comparision in the `wp_verify_nonce`.
		return $post_id;
	}

	// delete.
	setcookie( 'et-saving-post-' . $post_id . '-bb', 'bb', time() - DAY_IN_SECONDS, SITECOOKIEPATH, false, is_ssl() );
	// set.
	setcookie( 'et-saved-post-' . $post_id . '-bb', 'bb', time() + MINUTE_IN_SECONDS * 5, SITECOOKIEPATH, false, is_ssl() );
}

add_action( 'save_post', 'et_pb_set_et_saved_cookie', 10, 2 );

/**
 * Handling title-less & content-less switching from backend builder to normal editor
 *
 * @param int   $maybe_empty whether the wp_insert_post content is empty or not.
 * @param array $postarr all $_POST data that is being passed to wp_insert_post().
 * @return int  whether wp_insert_post content should be considered empty or not
 */
function et_pb_ensure_builder_activation_switching( $maybe_empty, $postarr ) {
	// Consider wp_insert_post() content is not empty if incoming et_pb_use_builder is `off` while currently saved _et_pb_use_builder value is `on`.
	if ( isset( $postarr['et_pb_use_builder'] ) && 'off' === $postarr['et_pb_use_builder'] && isset( $postarr['post_ID'] ) && et_pb_is_pagebuilder_used( $postarr['post_ID'] ) ) {
		return false;
	}

	return $maybe_empty;
}
add_filter( 'wp_insert_post_empty_content', 'et_pb_ensure_builder_activation_switching', 10, 2 );

/**
 * Display buttons before main editor in BFB.
 *
 * @param WP_Post $post object.
 */
function et_pb_before_main_editor( $post ) {
	if ( ! et_builder_enabled_for_post( $post->ID ) ) {
		return;
	}

	$_et_builder_use_builder   = get_post_meta( $post->ID, '_et_pb_use_builder', true );
	$is_builder_used           = 'on' === $_et_builder_use_builder;
	$last_builder_version_used = get_post_meta( $post->ID, '_et_builder_version', true ); // Examples: 'BB|Divi|3.0.30' 'VB|Divi|3.0.30'.

	$_et_builder_use_ab_testing            = et_builder_bfb_enabled() ? false : get_post_meta( $post->ID, '_et_pb_use_ab_testing', true );
	$_et_builder_ab_stats_refresh_interval = et_builder_bfb_enabled() ? false : et_pb_ab_get_refresh_interval( $post->ID );
	$_et_builder_ab_subjects               = et_builder_bfb_enabled() ? false : get_post_meta( $post->ID, '_et_pb_ab_subjects', true );
	$_et_builder_ab_goal_module            = et_builder_bfb_enabled() ? false : et_pb_ab_get_goal_module( $post->ID );

	$builder_always_enabled = apply_filters( 'et_builder_always_enabled', false, $post->post_type, $post );
	if ( 'et_pb_layout' === $post->post_type ) {
		// No matter what, in Divi Library we always want the builder.
		$builder_always_enabled = true;
	}
	if ( $builder_always_enabled ) {
		$is_builder_used         = true;
		$_et_builder_use_builder = 'on';
	}

	// TODO, need to change the output of these buttons if BFB.

	// Add button only if current user is allowed to use it otherwise display placeholder with all required data.
	if ( et_pb_is_allowed( 'divi_builder_control' ) ) {
		$buttons = sprintf(
			'<a href="#" id="et_pb_toggle_builder" data-builder="%2$s" data-editor="%3$s" class="button button-primary button-large%4$s%5$s">%1$s</a>',
			( $is_builder_used ? esc_html__( 'Return To Standard Editor', 'et_builder' ) : esc_html__( 'Use The Divi Builder', 'et_builder' ) ),
			esc_html__( 'Use The Divi Builder', 'et_builder' ),
			esc_html__( 'Return To Standard Editor', 'et_builder' ),
			( $is_builder_used ? ' et_pb_builder_is_used' : '' ),
			( $builder_always_enabled ? ' et_pb_hidden' : '' )
		);

		// add in the visual builder button only on appropriate post types
		// also, don't add the button on page if it set as static posts page.
		if ( et_builder_fb_enabled_for_post( $post->ID ) && et_pb_is_allowed( 'use_visual_builder' ) && ! et_is_extra_library_layout( $post->ID ) && get_option( 'page_for_posts' ) !== $post->ID ) {
			$buttons .= sprintf(
				'<a href="%1$s" id="et_pb_fb_cta" class="button button-primary button-large%3$s%4$s">%2$s</a>',
				esc_url( et_fb_get_vb_url() ),
				esc_html__( 'Build On The Front End', 'et_builder' ),
				( $builder_always_enabled ? ' et-first-child' : '' ),
				( et_pb_is_pagebuilder_used( $post->ID ) ? ' et_pb_ready' : '' )
			);
		}

		printf(
			'<div class="et_pb_toggle_builder_wrapper%1$s"%4$s>%2$s</div><div id="et_pb_main_editor_wrap"%3$s>',
			( $is_builder_used ? ' et_pb_builder_is_used' : '' ),
			et_core_esc_previously( $buttons ),
			( $is_builder_used ? ' class="et_pb_post_body_hidden"' : '' ),
			( et_builder_bfb_enabled() ? ' style="opacity: 0;"' : '' )
		);
	} else {
		printf(
			'<div class="et_pb_toggle_builder_wrapper%2$s"%3$s></div><div id="et_pb_main_editor_wrap"%1$s>',
			( $is_builder_used ? ' class="et_pb_post_body_hidden"' : '' ),
			( $is_builder_used ? ' et_pb_builder_is_used' : '' ),
			( et_builder_bfb_enabled() ? ' style="opacity: 0;"' : '' )
		);
	}

	if ( ! et_builder_bfb_enabled() ) {
		$module_fields_dependencies = wp_json_encode( ET_Builder_Element::get_field_dependencies( $post->post_type ) );

		echo et_core_esc_previously(
			"
			<script>
				window.et_pb_module_field_dependencies = JSON.parse( '{$module_fields_dependencies}' );
			</script>"
		);
	}

	?>
	<p class="et_pb_page_settings" style="display: none;">
		<?php wp_nonce_field( basename( __FILE__ ), 'et_pb_settings_nonce' ); ?>
		<input type="hidden" id="et_pb_last_post_modified" name="et_pb_last_post_modified" value="<?php echo esc_attr( $post->post_modified ); ?>" />
		<input type="hidden" id="et_pb_use_builder" name="et_pb_use_builder" value="<?php echo esc_attr( $_et_builder_use_builder ); ?>" />
		<input type="hidden" id="et_builder_version" name="et_builder_version" value="<?php echo esc_attr( $last_builder_version_used ); ?>" />
		<input type="hidden" autocomplete="off" id="et_pb_use_ab_testing" name="et_pb_use_ab_testing" value="<?php echo esc_attr( $_et_builder_use_ab_testing ); ?>">
		<input type="hidden" autocomplete="off" id="_et_pb_ab_stats_refresh_interval" name="et_pb_ab_stats_refresh_interval" value="<?php echo esc_attr( $_et_builder_ab_stats_refresh_interval ); ?>">
		<input type="hidden" autocomplete="off" id="et_pb_ab_subjects" name="et_pb_ab_subjects" value="<?php echo esc_attr( $_et_builder_ab_subjects ); ?>">
		<input type="hidden" autocomplete="off" id="et_pb_ab_goal_module" name="et_pb_ab_goal_module" value="<?php echo esc_attr( $_et_builder_ab_goal_module ); ?>">
		<?php et_pb_builder_settings_hidden_inputs( $post->ID ); ?>
		<?php et_pb_builder_global_library_inputs( $post->ID ); ?>

		<textarea id="et_pb_old_content" name="et_pb_old_content"><?php echo esc_attr( get_post_meta( $post->ID, '_et_pb_old_content', true ) ); ?></textarea>
	</p>
	<?php
}

/**
 * Add #et_pb_main_editor_wrap closing div.
 *
 * @param WP_Post $post object.
 */
function et_pb_after_main_editor( $post ) {
	if ( ! et_builder_enabled_for_post( $post->ID ) ) {
		return;
	}
	echo '</div> <!-- #et_pb_main_editor_wrap -->';
}

/**
 * Setup Divi Builder in BFB.
 */
function et_pb_setup_main_editor() {
	if ( ! et_core_is_gutenberg_enabled() ) {
		add_action( 'edit_form_after_title', 'et_pb_before_main_editor' );
		add_action( 'edit_form_after_editor', 'et_pb_after_main_editor' );
	}
}
add_action( 'add_meta_boxes', 'et_pb_setup_main_editor', 11 );

/**
 *  Load scripts and styles in admin.
 *
 * @param string $hook The current admin page.
 */
function et_pb_admin_scripts_styles( $hook ) {
	global $typenow, $pagenow;

	// load css file for the Divi menu.
	wp_enqueue_style( 'library-menu-styles', ET_BUILDER_URI . '/styles/library_menu.css', array(), ET_BUILDER_VERSION );

	if ( 'widgets.php' === $hook ) {
		wp_enqueue_script( 'et_pb_widgets_js', ET_BUILDER_URI . '/scripts/ext/widgets.js', array( 'jquery' ), ET_BUILDER_VERSION, true );

		$et_pb_options_admin = array(
			'ajaxurl'             => admin_url( 'admin-ajax.php' ),
			'et_admin_load_nonce' => wp_create_nonce( 'et_admin_load_nonce' ),
			'widget_info'         => sprintf(
				'<div id="et_pb_widget_area_create"><p>%1$s.</p><p>%2$s.</p><p><label>%3$s <input id="et_pb_new_widget_area_name" value="" /></label><button class="button button-primary et_pb_create_widget_area">%4$s</button></p><p class="et_pb_widget_area_result"></p></div>',
				esc_html__( 'Here you can create new widget areas for use in the Sidebar module', 'et_builder' ),
				esc_html__( 'Note: Naming your widget area "sidebar 1", "sidebar 2", "sidebar 3", "sidebar 4" or "sidebar 5" will cause conflicts with this theme', 'et_builder' ),
				esc_html__( 'Widget Name', 'et_builder' ),
				esc_html__( 'Create', 'et_builder' )
			),
			'delete_string'       => esc_html__( 'Delete', 'et_builder' ),
		);

		wp_localize_script( 'et_pb_widgets_js', 'et_pb_options', apply_filters( 'et_pb_options_admin', $et_pb_options_admin ) );

		wp_enqueue_style( 'et_pb_widgets_css', ET_BUILDER_URI . '/styles/widgets.css', array(), ET_BUILDER_VERSION );

		return;
	}

	// Do not enqueue BB assets if GB is active on this page.
	if ( et_core_is_gutenberg_enabled() ) {
		return;
	}

	if ( ! in_array( $hook, array( 'post-new.php', 'post.php' ), true ) ) {
		return;
	}

	/*
	 * Load the builder javascript and css files for custom post types
	 * custom post types can be added using et_builder_post_types filter
	*/

	$post_types           = et_builder_get_builder_post_types();
	$on_enabled_post_type = isset( $typenow ) && in_array( $typenow, $post_types, true );
	$on_enabled_post      = isset( $pagenow ) && 'post.php' === $pagenow && isset( $_GET['post'] ) && et_builder_enabled_for_post( intval( $_GET['post'] ) ); // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.

	if ( $on_enabled_post_type || $on_enabled_post ) {
		wp_enqueue_style( 'et_bb_bfb_common', ET_BUILDER_URI . '/styles/bb_bfb_common.css', array(), ET_BUILDER_VERSION );

		// Boot one builders assets or the other.
		if ( et_builder_bfb_enabled() ) {
			et_bfb_enqueue_scripts();

			// do not load BFB if builder is disabled on page.
			if ( ! et_pb_is_pagebuilder_used( get_the_ID() ) ) {
				return;
			}

			// BFB loads builder modal outside the iframe using react portal. external scripts
			// that is used on modal needs to be enqueued.
			et_builder_enqueue_assets_main();

			et_builder_enqueue_open_sans();

			$secondary_css_bundles = glob( ET_BUILDER_DIR . 'frontend-builder/build/bundle.*.css' );

			if ( $secondary_css_bundles ) {
				$bundles = array( 'et-frontend-builder' );

				foreach ( $secondary_css_bundles as $css_bundle ) {
					$slug  = basename( $css_bundle, '.css' );
					$parts = explode( '.', $slug, -1 );

					// Drop "bundle" from array.
					array_shift( $parts );

					$slug = implode( '-', $parts );

					et_fb_enqueue_bundle( "et-fb-{$slug}", basename( $css_bundle ), $bundles, null );

					$bundles[] = $slug;
				}
			}

			// Hooks for theme/plugin specific styling which complements visual builder.
			do_action( 'et_bfb_boot' );
		} else {
			et_pb_add_builder_page_js_css();
		}
	}
}
add_action( 'admin_enqueue_scripts', 'et_pb_admin_scripts_styles', 10, 1 );

/**
 * Disable emoji detection script on edit page which has Backend Builder on it.
 * WordPress automatically replaces emoji with plain image for backward compatibility
 * on older browsers. This causes issue when emoji is used on header or other input
 * text field because (when the modal is saved, shortcode is generated, and emoji
 * is being replaced with plain image) it creates incorrect attribute markup
 * such as `title="I <img class="emoji" src="../heart.png" /> WP"` and causes
 * the whole input text value to be disappeared
 *
 * @return void
 */
function et_pb_remove_emoji_detection_script() {
	// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
	global $pagenow;

	$disable_emoji_detection = false;

	// Disable emoji detection script on editing page which has Backend Builder
	// global $post isn't available at admin_init, so retrieve $post data manually.
	if ( 'post.php' === $pagenow && isset( $_GET['post'] ) ) {
		$post_id = (int) $_GET['post'];
		$post    = get_post( $post_id );

		if ( is_a( $post, 'WP_POST' ) && et_builder_enabled_for_post( $post->ID ) ) {
			$disable_emoji_detection = true;
		}
	}

	// Disable emoji detection script on post new page which has Backend Builder.
	$has_post_type_query = isset( $_GET['post_type'] );
	if ( 'post-new.php' === $pagenow && ( ! $has_post_type_query || ( $has_post_type_query && in_array( $_GET['post_type'], et_builder_get_builder_post_types(), true ) ) ) ) {
		$disable_emoji_detection = true;
	}

	if ( $disable_emoji_detection ) {
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	}
	// phpcs:enable
}
add_action( 'admin_init', 'et_pb_remove_emoji_detection_script' );

/**
 * Disable emoji detection script on visual builder
 * WordPress automatically replaces emoji with plain image for backward compatibility
 * on older browsers. This causes issue when emoji is used on header or other input
 * text field because the staticize emoji creates HTML markup which appears to be
 * invalid on input[type="text"] field such as `title="I <img class="emoji"
 * src="../heart.png" /> WP"` and causes the input text value to be escaped and
 * disappeared
 *
 * @return void
 */
function et_fb_remove_emoji_detection_script() {
	global $post;

	// Disable emoji detection script on visual builder. React's auto escaping will
	// remove all staticized emoji when being opened on modal's input field.
	if ( isset( $post->ID ) && et_fb_is_enabled( $post->ID ) ) {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	}
}
add_action( 'wp', 'et_fb_remove_emoji_detection_script' );

/**
 * If the builder is used for the page, get rid of random p tags.
 *
 * @param string $content content.
 *
 * @return string|string[]|null
 */
function et_pb_fix_builder_shortcodes( $content ) {
	if ( is_admin() ) {
		// ET_Builder_Element is not loaded in the administration and some plugins call
		// the_content there (e.g. WP File Manager).
		return $content;
	}

	$is_theme_builder = ET_Builder_Element::is_theme_builder_layout();
	$is_singular      = is_singular() && 'on' === get_post_meta( get_the_ID(), '_et_pb_use_builder', true );

	// if the builder is used for the page, get rid of random p tags.
	if ( $is_theme_builder || $is_singular ) {
		$content = et_pb_fix_shortcodes( $content );
	}

	return $content;
}
add_filter( 'the_content', 'et_pb_fix_builder_shortcodes' );
add_filter( 'et_builder_render_layout', 'et_pb_fix_builder_shortcodes' );

/**
 * Prepare code module for wpautop.
 *
 * @param string $content content.
 *
 * @return string|string[]|null
 */
function et_pb_the_content_prep_code_module_for_wpautop( $content ) {
	if ( 'on' === get_post_meta( get_the_ID(), '_et_pb_use_builder', true ) ) {
		$content = et_pb_prep_code_module_for_wpautop( $content );
	}
	return $content;
}
add_filter( 'the_content', 'et_pb_the_content_prep_code_module_for_wpautop', 0 );
add_filter( 'et_builder_render_layout', 'et_pb_the_content_prep_code_module_for_wpautop', 0 );

if ( ! function_exists( 'et_pb_generate_new_layout_modal' ) ) {
	/**
	 * Generate the html for "Add new template" Modal in Library.
	 *
	 * @return mixed|void
	 */
	function et_pb_generate_new_layout_modal() {
		$template_type_option_output = '';
		$layout_cat_option_output    = '';
		$layout_cats_list            = '';
		$layout_tags_list            = '';
		$template_type_options_list  = '';

		$new_layout_template_types = array(
			'module'            => esc_html__( 'Module', 'et_builder' ),
			'fullwidth_module'  => esc_html__( 'Fullwidth Module', 'et_builder' ),
			'row'               => esc_html__( 'Row', 'et_builder' ),
			'section'           => esc_html__( 'Section', 'et_builder' ),
			'fullwidth_section' => esc_html__( 'Fullwidth Section', 'et_builder' ),
			'specialty_section' => esc_html__( 'Specialty Section', 'et_builder' ),
			'layout'            => et_builder_i18n( 'Layout' ),
		);

		$template_type_options = apply_filters( 'et_pb_new_layout_template_types', $new_layout_template_types );

		// construct output for the template type option.
		if ( ! empty( $template_type_options ) ) {
			foreach ( $template_type_options as $option_id => $option_name ) {
				$template_type_options_list .= sprintf(
					'<option value="%1$s">%2$s</option>',
					esc_attr( $option_id ),
					esc_html( $option_name )
				);
			}

			$template_type_option_output = sprintf(
				'<br><label>%1$s:</label>
				<select id="new_template_type">
					%2$s
				</select>',
				esc_html__( 'Layout Type', 'et_builder' ),
				$template_type_options_list
			);
		}

		$template_global_option_output = apply_filters(
			'et_pb_new_layout_global_option',
			sprintf(
				'<label>%1$s<input type="checkbox" value="global" id="et_pb_template_global"></label>',
				esc_html__( 'Save as Global', 'et_builder' )
			)
		);

		$layout_categories = apply_filters( 'et_pb_new_layout_cats_array', get_terms( 'layout_category', array( 'hide_empty' => false ) ) );
		if ( is_array( $layout_categories ) && ! empty( $layout_categories ) ) {
			foreach ( $layout_categories as $category ) {
				$layout_cats_list .= sprintf(
					'<label>%1$s<input type="checkbox" value="%2$s"/></label>',
					esc_html( $category->name ),
					esc_attr( $category->term_id )
				);
			}
		}

		$layout_tags = apply_filters( 'et_pb_new_layout_tags_array', get_terms( 'layout_tag', array( 'hide_empty' => false ) ) );
		if ( is_array( $layout_tags ) && ! empty( $layout_tags ) ) {
			foreach ( $layout_tags as $tag ) {
				$layout_tags_list .= sprintf(
					'<label>%1$s<input type="checkbox" value="%2$s"/></label>',
					esc_html( $tag->name ),
					esc_attr( $tag->term_id )
				);
			}
		}

		// Construct output for the layout Tag option.
		$layout_cat_option_output = sprintf(
			'<br><label>%1$s</label>
			<div class="layout_cats_container">
				%3$s
			</div>
			<input type="text" value="" id="et_pb_new_cat_name" class="regular-text" placeholder="%2$s">',
			esc_html__( 'Add To Categories', 'et_builder' ),
			esc_html__( 'Create new Category', 'et_builder' ),
			$layout_cats_list
		);

		// Construct output for the layout Tag option.
		$layout_tag_option_output = sprintf(
			'<br><label>%1$s</label>
			<div class="layout_cats_container">
				%3$s
			</div>
			<input type="text" value="" id="et_pb_new_tag_name" class="regular-text" placeholder="%2$s">',
			esc_html__( 'Add To Tags', 'et_builder' ),
			esc_html__( 'Create new Tag', 'et_builder' ),
			$layout_tags_list
		);

		$output = sprintf(
			'<div class="et_pb_modal_overlay et_modal_on_top et_pb_new_template_modal">
				<div class="et_pb_prompt_modal">
					<h2>%1$s</h2>
					<div class="et_pb_prompt_modal_inside">
						<label>%2$s:</label>
							<input type="text" value="" id="et_pb_new_template_name" class="regular-text">
							%6$s
							%3$s
							%4$s
							%5$s
							%8$s
							%7$s
							<input id="et_builder_layout_built_for_post_type" type="hidden" value="page">
					</div>
					<a href="#"" class="et_pb_prompt_dont_proceed et-pb-modal-close"></a>
					<div class="et_pb_prompt_buttons">
						<br>
						<span class="spinner"></span>
						<input type="submit" class="et_pb_create_template button-primary et_pb_prompt_proceed">
					</div>
				</div>
			</div>',
			esc_html__( 'Add New Layout', 'et_builder' ),
			esc_html__( 'Layout Name', 'et_builder' ),
			$template_type_option_output,
			$template_global_option_output,
			$layout_cat_option_output, // #5
			apply_filters( 'et_pb_new_layout_before_options', '' ),
			apply_filters( 'et_pb_new_layout_after_options', '' ),
			$layout_tag_option_output
		);

		return apply_filters( 'et_pb_new_layout_modal_output', $output );
	}
}

if ( ! function_exists( 'et_pb_get_layout_type' ) ) :
	/**
	 * Get layout type of given post ID.
	 *
	 * @param int $post_id post id.
	 *
	 * @return string|bool
	 */
	function et_pb_get_layout_type( $post_id ) {
		// Get taxonomies.
		$layout_type_data = wp_get_post_terms( $post_id, 'layout_type' );

		if ( empty( $layout_type_data ) ) {
			return false;
		}

		// Pluck name out of taxonomies.
		$layout_type_array = wp_list_pluck( $layout_type_data, 'name' );

		// Logically, a layout only have one layout type.
		$layout_type = implode( '|', $layout_type_array );

		return $layout_type;
	}
endif;

if ( ! function_exists( 'et_pb_is_wp_old_version' ) ) :
	/**
	 * Determine current wp version is less than 4.5.
	 */
	function et_pb_is_wp_old_version() {
		global $wp_version;

		$wp_major_version = substr( $wp_version, 0, 3 );

		if ( version_compare( $wp_major_version, '4.5', '<' ) ) {
			return true;
		}

		return false;
	}
endif;

if ( ! function_exists( 'et_builder_theme_or_plugin_updated_cb' ) ) :
	/**
	 * Delete cached definitions/helpers after theme or plugin update.
	 */
	function et_builder_theme_or_plugin_updated_cb() {
		// Delete cached definitions / helpers.
		et_fb_delete_builder_assets();
		et_update_option( 'et_pb_clear_templates_cache', true );
	}
	add_action( 'after_switch_theme', 'et_builder_theme_or_plugin_updated_cb' );
	add_action( 'activated_plugin', 'et_builder_theme_or_plugin_updated_cb', 10, 0 );
	add_action( 'deactivated_plugin', 'et_builder_theme_or_plugin_updated_cb', 10, 0 );
	add_action( 'upgrader_process_complete', 'et_builder_theme_or_plugin_updated_cb', 10, 0 );
	add_action( 'et_support_center_toggle_safe_mode', 'et_builder_theme_or_plugin_updated_cb', 10, 0 );
endif;

/**
 * Enqueue scripts that are required by BFB and Layout Block. These scripts are abstracted into
 * separated file so Layout Block can enqueue the same sets of scripts without re-register and
 * re-enqueue them
 *
 * @since 4.1.0
 */
function et_bfb_enqueue_scripts_dependencies() {
	global $wp_version, $post;

	$wp_major_version = substr( $wp_version, 0, 3 );

	if ( et_pb_is_pagebuilder_used( get_the_ID() ) ) {
		wp_enqueue_editor();
	}

	if ( version_compare( $wp_major_version, '4.5', '<' ) ) {
		$jquery_ui = 'et_pb_admin_date_js';
		wp_register_script( $jquery_ui, ET_BUILDER_URI . '/scripts/ext/jquery-ui-1.10.4.custom.min.js', array( 'jquery' ), ET_BUILDER_PRODUCT_VERSION, true );
	} else {
		$jquery_ui = 'jquery-ui-datepicker';
	}

	// Load timepicker script on admin page in case of BFB to make it work with modals loaded on WP admin DOM.
	wp_enqueue_script( 'et_bfb_admin_date_addon_js', ET_BUILDER_URI . '/scripts/ext/jquery-ui-timepicker-addon.js', array( $jquery_ui ), ET_BUILDER_PRODUCT_VERSION, true );

	// Load google maps script on admin page in case of BFB to make it work with modals loaded on WP admin DOM.
	if ( et_pb_enqueue_google_maps_script() ) {
		$bfb_google_maps_api_url_args = array(
			'key'      => et_pb_get_google_api_key(),
			'callback' => 'initMap',
		);
		$bfb_google_maps_api_url      = add_query_arg( $bfb_google_maps_api_url_args, is_ssl() ? 'https://maps.googleapis.com/maps/api/js' : 'http://maps.googleapis.com/maps/api/js' );
		wp_enqueue_script( 'et_bfb_google_maps_api', esc_url( $bfb_google_maps_api_url ), array(), '3', true );
	}

	wp_enqueue_script( 'et_pb_media_library', ET_BUILDER_URI . '/scripts/ext/media-library.js', array( 'media-editor' ), ET_BUILDER_PRODUCT_VERSION, true );

	if ( ! wp_script_is( 'wp-hooks', 'registered' ) ) {
		// Use bundled wp-hooks script when WP < 5.0.
		wp_enqueue_script( 'wp-hooks', ET_BUILDER_URI . '/frontend-builder/assets/backports/hooks.js', array(), ET_BUILDER_PRODUCT_VERSION, false );
	}
}

if ( ! function_exists( 'et_bfb_enqueue_scripts' ) ) :
	/**
	 * Register BFB scripts.
	 */
	function et_bfb_enqueue_scripts() {
		global $post;

		// Enqueue scripts required by BFB.
		et_bfb_enqueue_scripts_dependencies();

		wp_enqueue_script( 'et_bfb_admin_js', ET_BUILDER_URI . '/scripts/bfb_admin_script.js', array( 'jquery', 'et_pb_media_library' ), ET_BUILDER_PRODUCT_VERSION, true );
		$bfb_options = array(
			'ajaxurl'                     => admin_url( 'admin-ajax.php' ),
			'et_enable_bfb_nonce'         => wp_create_nonce( 'et_enable_bfb_nonce' ),
			'default_initial_column_type' => apply_filters( 'et_builder_default_initial_column_type', '4_4' ),
			'default_initial_text_module' => apply_filters( 'et_builder_default_initial_text_module', 'et_pb_text' ),
			'skip_default_content_adding' => apply_filters( 'et_builder_skip_content_activation', false, $post ) ? 'skip' : '',
		);

		wp_localize_script( 'et_bfb_admin_js', 'et_bfb_options', apply_filters( 'et_bfb_options', $bfb_options ) );

		// Add filter to register tinyMCE buttons that is missing from BFB.
		add_filter( 'mce_external_plugins', 'et_bfb_filter_mce_plugin' );
	}
endif;

/**
 * BFB use built-in WordPress tinyMCE initialization while visual builder uses standalone tinyMCE
 * initialization which leads to several buttons in VB not available in BFB. This function register
 * them as plugins
 *
 * @since 4.0.9
 *
 * @param array $plugins tinyMCE plugin list.
 *
 * @return array
 */
function et_bfb_filter_mce_plugin( $plugins ) {
	// NOTE: `ET_FB_ASSETS_URI` constant isn't available yet at this point, so use `ET_BUILDER_URI`.
	$plugins['table'] = ET_BUILDER_URI . '/frontend-builder/assets/vendors/plugins/table/plugin.min.js';

	return $plugins;
}

/**
 * Tinymce to load in html mode for BB.
 *
 * @param array  $settings  Array of editor arguments.
 * @param string $editor_id Unique editor identifier, e.g. 'content'. Accepts 'classic-block'
 *                          when called from block editor's Classic block.
 *
 * @return mixed
 */
function et_pb_wp_editor_settings( $settings, $editor_id ) {
	if ( 'content' === $editor_id ) {
		$settings['default_editor'] = 'html';
	}

	return $settings;
}

if ( ! function_exists( 'et_pb_add_builder_page_js_css' ) ) :
	/**
	 * Load builder js and css.
	 */
	function et_pb_add_builder_page_js_css() {
		global $typenow, $post, $wp_version;

		// Get WP major version.
		$wp_major_version = substr( $wp_version, 0, 3 );

		// Avoid serving any data from object cache.
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}

		// fix tinymce to load in html mode for BB.
		if ( et_pb_is_pagebuilder_used() ) {
			add_filter( 'wp_editor_settings', 'et_pb_wp_editor_settings', 10, 2 );
		}

		// BEGIN Process shortcodes (for module settings migrations and Yoast SEO compatibility)
		// Get list of shortcodes that causes issue if being triggered in admin.
		$conflicting_shortcodes = et_pb_admin_excluded_shortcodes();

		if ( ! empty( $conflicting_shortcodes ) ) {
			foreach ( $conflicting_shortcodes as $shortcode ) {
				remove_shortcode( $shortcode );
			}
		}

		// save the original content of $post variable.
		$post_original = $post;
		// get the content for yoast.
		$post_content_processed = do_shortcode( $post->post_content );
		// set the $post to the original content to make sure it wasn't changed by do_shortcode().
		$post = $post_original; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- were restoring it to what it was beforea few lines above.
		// END Process shortcodes.

		$is_global_template      = '';
		$post_id                 = '';
		$post_type               = $typenow;
		$selective_sync_status   = '';
		$global_module_type      = '';
		$excluded_global_options = array();

		$utils           = ET_Core_Data_Utils::instance();
		$updates_options = get_site_option( 'et_automatic_updates_options', array() );
		$et_account      = array(
			'et_username' => $utils->array_get( $updates_options, 'username', '' ),
			'et_api_key'  => $utils->array_get( $updates_options, 'api_key', '' ),
			'status'      => get_site_option( 'et_account_status', 'not_active' ),
		);

		// we need some post data when editing saved templates.
		if ( 'et_pb_layout' === $typenow ) {
			$template_scope     = wp_get_object_terms( get_the_ID(), 'scope' );
			$template_type      = wp_get_object_terms( get_the_ID(), 'layout_type' );
			$is_global_template = ! empty( $template_scope[0] ) ? $template_scope[0]->slug : 'regular';
			$global_module_type = ! empty( $template_type[0] ) ? $template_type[0]->slug : '';
			$post_id            = get_the_ID();

			// Check whether it's a Global item's page and display wp error if Global items disabled for current user.
			if ( ! et_pb_is_allowed( 'edit_global_library' ) && 'global' === $is_global_template ) {
				wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'et_builder' ) );
			}

			if ( 'global' === $is_global_template ) {
				$excluded_global_options = get_post_meta( $post_id, '_et_pb_excluded_global_options' );
				$selective_sync_status   = empty( $excluded_global_options ) ? '' : 'updated';
			}

			$built_for_post_type = get_post_meta( get_the_ID(), '_et_pb_built_for_post_type', true );
			$built_for_post_type = '' !== $built_for_post_type ? $built_for_post_type : 'page';
			$post_type           = apply_filters( 'et_pb_built_for_post_type', $built_for_post_type, get_the_ID() );
		}

		// we need this data to create the filter when adding saved modules.
		$layout_categories    = get_terms( 'layout_category' );
		$layout_cat_data      = array();
		$layout_cat_data_json = '';

		if ( is_array( $layout_categories ) && ! empty( $layout_categories ) ) {
			foreach ( $layout_categories as $category ) {
				$layout_cat_data[] = array(
					'slug' => $category->slug,
					'name' => $category->name,
				);
			}
		}
		if ( ! empty( $layout_cat_data ) ) {
			$layout_cat_data_json = wp_json_encode( $layout_cat_data );
		}

		// Set fixed protocol for preview URL to prevent cross origin issue.
		$preview_scheme = is_ssl() ? 'https' : 'http';

		$preview_url = esc_url( home_url( '/' ) );

		if ( 'https' === $preview_scheme && ! strpos( $preview_url, 'https://' ) ) {
			$preview_url = str_replace( 'http://', 'https://', $preview_url );
		}

		// force update cache if et_pb_clear_templates_cache option is set to on.
		$force_cache_value  = et_get_option( 'et_pb_clear_templates_cache', '', '', true );
		$force_cache_update = '' !== $force_cache_value ? $force_cache_value : ET_BUILDER_FORCE_CACHE_PURGE;

		/**
		 * Whether or not the backend builder should clear its Backbone template cache.
		 *
		 * @param bool $force_cache_update
		 */
		$force_cache_update = apply_filters( 'et_pb_clear_template_cache', $force_cache_update );

		// delete et_pb_clear_templates_cache option it's not needed anymore.
		et_delete_option( 'et_pb_clear_templates_cache' );

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'underscore' );
		wp_enqueue_script( 'backbone' );

		if ( et_pb_enqueue_google_maps_script() ) {
			$google_maps_api_url_args = array(
				'v'   => 3,
				'key' => et_pb_get_google_api_key(),
			);

			$google_maps_api_url = add_query_arg( $google_maps_api_url_args, is_ssl() ? 'https://maps.googleapis.com/maps/api/js' : 'http://maps.googleapis.com/maps/api/js' );

			wp_enqueue_script( 'google-maps-api', esc_url_raw( $google_maps_api_url ), array(), '3', true );
		}

		wp_enqueue_script( 'wp-color-picker' );
		wp_enqueue_style( 'wp-color-picker' );

		if ( version_compare( $wp_major_version, '4.9', '>=' ) ) {
			wp_enqueue_script( 'wp-color-picker-alpha', ET_BUILDER_URI . '/scripts/ext/wp-color-picker-alpha.min.js', array( 'jquery', 'wp-color-picker' ), ET_BUILDER_VERSION, true );
			$color_picker_strings = array(
				'legacy_pick'    => esc_html__( 'Select', 'et_builder' ),
				'legacy_current' => esc_html__( 'Current Color', 'et_builder' ),
			);
			wp_localize_script( 'wp-color-picker-alpha', 'et_pb_color_picker_strings', apply_filters( 'et_pb_color_picker_strings_builder', $color_picker_strings ) );
		} else {
			wp_enqueue_script( 'wp-color-picker-alpha', ET_BUILDER_URI . '/scripts/ext/wp-color-picker-alpha-48.min.js', array( 'jquery', 'wp-color-picker' ), ET_BUILDER_VERSION, true );
		}

		wp_register_script( 'chart', ET_BUILDER_URI . '/scripts/ext/chart.min.js', array(), ET_BUILDER_VERSION, true );
		wp_register_script( 'jquery-tablesorter', ET_BUILDER_URI . '/scripts/ext/jquery.tablesorter.min.js', array( 'jquery' ), ET_BUILDER_VERSION, true );

		// load 1.10.4 versions of jQuery-ui scripts if WP version is less than 4.5, load 1.11.4 version otherwise.
		if ( et_pb_is_wp_old_version() ) {
			$jquery_ui = 'et_pb_admin_date_js';
			wp_enqueue_script( $jquery_ui, ET_BUILDER_URI . '/scripts/ext/jquery-ui-1.10.4.custom.min.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
		} else {
			$jquery_ui = 'jquery-ui-datepicker';
		}

		wp_enqueue_script( 'et_pb_admin_date_addon_js', ET_BUILDER_URI . '/scripts/ext/jquery-ui-timepicker-addon.js', array( $jquery_ui ), ET_BUILDER_VERSION, true );

		wp_enqueue_script( 'validation', ET_BUILDER_URI . '/scripts/ext/jquery.validate.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
		wp_enqueue_script( 'minicolors', ET_BUILDER_URI . '/scripts/ext/jquery.minicolors.js', array( 'jquery' ), ET_BUILDER_VERSION, true );

		wp_enqueue_script( 'et_pb_cache_notice_js', ET_BUILDER_URI . '/scripts/cache_notice.js', array( 'jquery', 'et_pb_admin_js' ), ET_BUILDER_VERSION, true );

		$pb_notice_options = array(
			'product_version' => ET_BUILDER_PRODUCT_VERSION,
		);
		wp_localize_script( 'et_pb_cache_notice_js', 'et_pb_notice_options', apply_filters( 'et_pb_notice_options_builder', $pb_notice_options ) );

		wp_enqueue_script( 'lz_string', ET_BUILDER_URI . '/scripts/ext/lz-string.min.js', array(), ET_BUILDER_VERSION, true );

		// phpcs:disable WordPress.WP.EnqueuedResourceParameters -- The script version number are specified in the src. No need to set $ver explicitly.
		wp_enqueue_script( 'es6-promise', '//cdn.jsdelivr.net/npm/es6-promise@4/dist/es6-promise.auto.min.js', array(), null, true );
		wp_enqueue_script( 'postmate', '//cdn.jsdelivr.net/npm/postmate@1.1.9/build/postmate.min.js', array( 'es6-promise' ), null, true );
		// phpcs:enable

		wp_enqueue_script( 'et_pb_media_library', ET_BUILDER_URI . '/scripts/ext/media-library.js', array( 'media-editor' ), ET_BUILDER_PRODUCT_VERSION, true );
		wp_enqueue_script( 'et_pb_admin_js', ET_BUILDER_URI . '/scripts/builder.js', array( 'jquery', 'jquery-ui-core', 'underscore', 'backbone', 'chart', 'jquery-tablesorter', 'et_pb_media_library', 'lz_string', 'es6-promise' ), ET_BUILDER_VERSION, true );

		$saved_gutter_width = get_post_meta( get_the_ID(), '_et_pb_gutter_width', true );

		$pb_options         = array(
			'debug'                                        => defined( 'ET_DEBUG' ) && ET_DEBUG,
			'wp_default_editor'                            => wp_default_editor(),
			'et_account'                                   => $et_account,
			'ajaxurl'                                      => admin_url( 'admin-ajax.php' ),
			'home_url'                                     => home_url(),
			'cookie_path'                                  => SITECOOKIEPATH,
			'preview_url'                                  => add_query_arg( 'et_pb_preview', 'true', $preview_url ),
			'et_admin_load_nonce'                          => wp_create_nonce( 'et_admin_load_nonce' ),
			'images_uri'                                   => ET_BUILDER_URI . '/images',
			'postId'                                       => $post->ID,
			'post_type'                                    => $post_type,
			'is_third_party_post_type'                     => et_builder_is_post_type_custom( $post_type ) ? 'yes' : 'no',
			'et_builder_module_parent_shortcodes'          => ET_Builder_Element::get_parent_slugs_regex( $post_type ),
			'et_builder_module_child_shortcodes'           => ET_Builder_Element::get_child_slugs_regex( $post_type ),
			'et_builder_module_raw_content_shortcodes'     => ET_Builder_Element::get_raw_content_slugs( $post_type ),
			'et_builder_modules'                           => ET_Builder_Element::get_modules_js_array( $post_type ),
			'et_builder_modules_count'                     => ET_Builder_Element::get_modules_count( $post_type ),
			'et_builder_modules_with_children'             => ET_Builder_Element::get_slugs_with_children( $post_type ),
			'et_builder_modules_featured_image_background' => ET_Builder_Element::get_featured_image_background_modules( $post_type ),
			'et_builder_templates_amount'                  => ET_BUILDER_AJAX_TEMPLATES_AMOUNT,
			'et_builder_edit_global_library'               => et_pb_is_allowed( 'edit_global_library' ),
			'default_initial_column_type'                  => apply_filters( 'et_builder_default_initial_column_type', '4_4' ),
			'default_initial_text_module'                  => apply_filters( 'et_builder_default_initial_text_module', 'et_pb_text' ),
			'section_only_row_dragged_away'                => esc_html__( 'The section should have at least one row.', 'et_builder' ),
			'fullwidth_module_dragged_away'                => esc_html__( 'Fullwidth module can\'t be used outside of the Fullwidth Section.', 'et_builder' ),
			'stop_dropping_3_col_row'                      => esc_html__( "This number of columns can't be used on this row.", 'et_builder' ),
			'preview_image'                                => esc_html__( 'Preview', 'et_builder' ),
			'empty_admin_label'                            => esc_html__( 'Module', 'et_builder' ),
			'video_module_image_error'                     => esc_html__( 'Still images cannot be generated from this video service and/or this video format', 'et_builder' ),
			'geocode_error'                                => esc_html__( 'Geocode was not successful for the following reason', 'et_builder' ),
			'geocode_error_2'                              => esc_html__( 'Geocoder failed due to', 'et_builder' ),
			'no_results'                                   => esc_html__( 'No results found', 'et_builder' ),
			'all_tab_options_hidden'                       => esc_html__( 'No available options for this configuration.', 'et_builder' ),
			'update_global_module'                         => esc_html__( 'You\'re about to update global module. This change will be applied to all pages where you use this module. Press OK if you want to update this module', 'et_builder' ),
			'global_row_alert'                             => esc_html__( 'You cannot add global rows into global sections', 'et_builder' ),
			'global_module_alert'                          => esc_html__( 'You cannot add global modules into global sections or rows', 'et_builder' ),
			'all_cat_text'                                 => esc_html__( 'All Categories', 'et_builder' ),
			'font_name_error'                              => esc_html__( 'Name Cannot be Empty', 'et_builder' ),
			'font_file_error'                              => esc_html__( 'Please Select Font File', 'et_builder' ),
			'font_weight_error'                            => esc_html__( 'Please Select Font Weight', 'et_builder' ),
			'is_global_template'                           => $is_global_template,
			'selective_sync_status'                        => $selective_sync_status,
			'global_module_type'                           => $global_module_type,
			'excluded_global_options'                      => isset( $excluded_global_options[0] ) ? json_decode( $excluded_global_options[0] ) : array(),
			'template_post_id'                             => $post_id,
			'layout_categories'                            => $layout_cat_data_json,
			'map_pin_address_error'                        => esc_html__( 'Map Pin Address cannot be empty', 'et_builder' ),
			'map_pin_address_invalid'                      => esc_html__( 'Invalid Pin and address data. Please try again.', 'et_builder' ),
			'locked_section_permission_alert'              => esc_html__( 'You do not have permission to unlock this section.', 'et_builder' ),
			'locked_row_permission_alert'                  => esc_html__( 'You do not have permission to unlock this row.', 'et_builder' ),
			'locked_module_permission_alert'               => esc_html__( 'You do not have permission to unlock this module.', 'et_builder' ),
			'locked_item_permission_alert'                 => esc_html__( 'You do not have permission to perform this task.', 'et_builder' ),
			'localstorage_unavailability_alert'            => esc_html__( 'Unable to perform copy/paste process due to inavailability of localStorage feature in your browser. Please use latest modern browser (Chrome, Firefox, or Safari) to perform copy/paste process', 'et_builder' ),
			'invalid_color'                                => esc_html__( 'Invalid Color', 'et_builder' ),
			'et_pb_preview_nonce'                          => wp_create_nonce( 'et_pb_preview_nonce' ),
			'is_divi_library'                              => 'et_pb_layout' === $typenow ? 1 : 0,
			'layout_type'                                  => 'et_pb_layout' === $typenow ? et_pb_get_layout_type( get_the_ID() ) : 0,
			'is_plugin_used'                               => et_is_builder_plugin_active(),
			'yoast_content'                                => et_is_yoast_seo_plugin_active() ? $post_content_processed : '',
			'ab_db_status'                                 => true === et_pb_db_status_up_to_date() ? 'exists' : 'not_exists',
			'ab_testing_builder_nonce'                     => wp_create_nonce( 'ab_testing_builder_nonce' ),
			'page_color_palette'                           => get_post_meta( get_the_ID(), '_et_pb_color_palette', true ),
			'default_color_palette'                        => implode( '|', et_pb_get_default_color_palette() ),
			'page_section_bg_color'                        => get_post_meta( get_the_ID(), '_et_pb_section_background_color', true ),
			'page_gutter_width'                            => '' !== $saved_gutter_width ? $saved_gutter_width : et_get_option( 'gutter_width', '3' ),
			'product_version'                              => ET_BUILDER_PRODUCT_VERSION,
			'active_plugins'                               => et_builder_get_active_plugins(),
			'force_cache_purge'                            => $force_cache_update ? 'true' : 'false',
			'memory_limit_increased'                       => esc_html__( 'Your memory limit has been increased', 'et_builder' ),
			'memory_limit_not_increased'                   => esc_html__( "Your memory limit can't be changed automatically", 'et_builder' ),
			'google_api_key'                               => et_pb_get_google_api_key(),
			'options_page_url'                             => et_pb_get_options_page_link(),
			'et_pb_google_maps_script_notice'              => et_pb_enqueue_google_maps_script(),
			'select_text'                                  => esc_html__( 'Select', 'et_builder' ),
			'et_fb_autosave_nonce'                         => wp_create_nonce( 'et_fb_autosave_nonce' ),
			'et_builder_email_fetch_lists_nonce'           => wp_create_nonce( 'et_builder_email_fetch_lists_nonce' ),
			'et_builder_email_add_account_nonce'           => wp_create_nonce( 'et_builder_email_add_account_nonce' ),
			'et_builder_email_remove_account_nonce'        => wp_create_nonce( 'et_builder_email_remove_account_nonce' ),
			'et_pb_module_settings_migrations'             => ET_Builder_Module_Settings_Migration::$migrated,
			'acceptable_css_string_values'                 => et_builder_get_acceptable_css_string_values( 'all' ),
			'upload_font_nonce'                            => wp_create_nonce( 'et_fb_upload_font_nonce' ),
			'user_fonts'                                   => et_builder_get_custom_fonts(),
			'google_fonts'                                 => et_builder_get_google_fonts(),
			'supported_font_weights'                       => et_builder_get_font_weight_list(),
			'supported_font_formats'                       => et_pb_get_supported_font_formats(),
			'all_svg_icons'                                => et_pb_get_svg_icons_list(),
			'library_get_layouts_data_nonce'               => wp_create_nonce( 'et_builder_library_get_layouts_data' ),
			'library_get_layout_nonce'                     => wp_create_nonce( 'et_builder_library_get_layout' ),
			'library_update_account_nonce'                 => wp_create_nonce( 'et_builder_library_update_account' ),
			'library_custom_tabs'                          => ET_Builder_Library::builder_library_modal_custom_tabs( $post_type ),
		);
		$pb_options_builder = array_merge( $pb_options, et_pb_history_localization() );

		wp_localize_script( 'et_pb_admin_js', 'et_pb_options', apply_filters( 'et_pb_options_builder', $pb_options_builder ) );

		$ab_settings = et_builder_ab_labels();

		$pb_ab_js_options = array(
			'test_id'                                     => $post->ID,
			'has_report'                                  => et_pb_ab_has_report( $post->ID ),
			'has_permission'                              => et_pb_is_allowed( 'ab_testing' ),
			'refresh_interval_duration'                   => et_pb_ab_get_refresh_interval_duration( $post->ID ),
			'refresh_interval_durations'                  => et_pb_ab_refresh_interval_durations(),
			'analysis_formula'                            => et_pb_ab_get_analysis_formulas(),
			'have_conversions'                            => et_pb_ab_get_modules_have_conversions(),
			'sales_title'                                 => esc_html__( 'Sales', 'et_builder' ),
			'force_cache_purge'                           => $force_cache_update,
			'total_title'                                 => esc_html__( 'Total', 'et_builder' ),

			// Saved data.
			'subjects_rank'                               => ( 'on' === get_post_meta( $post->ID, '_et_pb_use_builder', true ) ) ? et_pb_ab_get_saved_subjects_ranks( $post->ID ) : false,

			// Rank color.
			'subjects_rank_color'                         => et_pb_ab_get_subject_rank_colors(),

			// Configuration.
			'has_no_permission'                           => array(
				'title' => esc_html__( 'Unauthorized Action', 'et_builder' ),
				'desc'  => esc_html__( 'You do not have permission to edit the module, row or section in this split test.', 'et_builder' ),
			),

			// AB Testing.
			'select_ab_testing_subject'                   => $ab_settings['select_subject'],
			'select_ab_testing_goal'                      => $ab_settings['select_goal'],
			'configure_ab_testing_alternative'            => $ab_settings['configure_alternative'],
			'select_ab_testing_winner_first'              => $ab_settings['select_winner_first'],
			'select_ab_testing_subject_first'             => $ab_settings['select_subject_first'],
			'select_ab_testing_goal_first'                => $ab_settings['select_goal_first'],
			'cannot_select_subject_parent_as_goal'        => $ab_settings['cannot_select_subject_parent_as_goal'],
			'cannot_select_global_children_as_subject'    => $ab_settings['cannot_select_global_children_as_subject'],
			'cannot_select_global_children_as_goal'       => $ab_settings['cannot_select_global_children_as_goal'],

			// Save to Library.
			'cannot_save_app_layout_has_ab_testing'       => $ab_settings['cannot_save_app_layout_has_ab_testing'],
			'cannot_save_section_layout_has_ab_testing'   => $ab_settings['cannot_save_section_layout_has_ab_testing'],
			'cannot_save_row_layout_has_ab_testing'       => $ab_settings['cannot_save_row_layout_has_ab_testing'],
			'cannot_save_row_inner_layout_has_ab_testing' => $ab_settings['cannot_save_row_inner_layout_has_ab_testing'],
			'cannot_save_module_layout_has_ab_testing'    => $ab_settings['cannot_save_module_layout_has_ab_testing'],

			// Load / Clear Layout.
			'cannot_load_layout_has_ab_testing'           => $ab_settings['cannot_load_layout_has_ab_testing'],
			'cannot_clear_layout_has_ab_testing'          => $ab_settings['cannot_clear_layout_has_ab_testing'],

			// Cannot Import / Export Layout (Portability).
			'cannot_import_export_layout_has_ab_testing'  => $ab_settings['cannot_import_export_layout_has_ab_testing'],

			// Moving Goal / Subject.
			'cannot_move_module_goal_out_from_subject'    => $ab_settings['cannot_move_module_goal_out_from_subject'],
			'cannot_move_row_goal_out_from_subject'       => $ab_settings['cannot_move_row_goal_out_from_subject'],
			'cannot_move_goal_into_subject'               => $ab_settings['cannot_move_goal_into_subject'],
			'cannot_move_subject_into_goal'               => $ab_settings['cannot_move_subject_into_goal'],

			// Cloning + Has Goal.
			'cannot_clone_section_has_goal'               => $ab_settings['cannot_clone_section_has_goal'],
			'cannot_clone_row_has_goal'                   => $ab_settings['cannot_clone_row_has_goal'],

			// Removing + Has Goal.
			'cannot_remove_section_has_goal'              => $ab_settings['cannot_remove_section_has_goal'],
			'cannot_remove_row_has_goal'                  => $ab_settings['cannot_remove_row_has_goal'],

			// Removing + Has Unremovable Subjects.
			'cannot_remove_section_has_unremovable_subject' => $ab_settings['cannot_remove_section_has_unremovable_subject'],
			'cannot_remove_row_has_unremovable_subject'   => $ab_settings['cannot_remove_row_has_unremovable_subject'],

			// View stats summary table heading.
			'view_stats_thead_titles'                     => $ab_settings['view_stats_thead_titles'],
		);
		wp_localize_script( 'et_pb_admin_js', 'et_pb_ab_js_options', apply_filters( 'et_pb_ab_js_options', $pb_ab_js_options ) );

		$pb_help_options = array(
			'shortcuts' => et_builder_get_shortcuts( 'bb' ),
		);
		wp_localize_script( 'et_pb_admin_js', 'et_pb_help_options', apply_filters( 'et_pb_help_options', $pb_help_options ) );

		et_core_load_main_fonts();

		wp_enqueue_style( 'et_pb_admin_css', ET_BUILDER_URI . '/styles/style.css', array(), ET_BUILDER_VERSION );
		wp_enqueue_style( 'et_pb_admin_date_css', ET_BUILDER_URI . '/styles/jquery-ui-1.12.1.custom.css', array(), ET_BUILDER_VERSION );

		wp_add_inline_style( 'et_pb_admin_css', et_pb_ab_get_subject_rank_colors_style() );

		ET_Cloud_App::load_js();
	}
endif;

/**
 * Set et-editor-available-post-* cookie
 */
function et_pb_set_editor_available_cookie() {
	$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : false; // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.

	$headers_sent = headers_sent();

	if ( et_builder_should_load_framework() && is_admin() && ! $headers_sent && ! empty( $post_id ) ) {
		setcookie( 'et-editor-available-post-' . $post_id . '-bb', 'bb', time() + ( MINUTE_IN_SECONDS * 30 ), SITECOOKIEPATH, false, is_ssl() );
	}
}
add_action( 'admin_init', 'et_pb_set_editor_available_cookie' );

/**
 * List of history meta.
 *
 * @return array History meta.
 */
function et_pb_history_localization() {
	return array(
		'verb'     => array(
			'did'                     => esc_html__( 'Did', 'et_builder' ),
			'added'                   => esc_html__( 'Added', 'et_builder' ),
			'edited'                  => esc_html__( 'Edited', 'et_builder' ),
			'removed'                 => esc_html__( 'Removed', 'et_builder' ),
			'moved'                   => esc_html__( 'Moved', 'et_builder' ),
			'expanded'                => esc_html__( 'Expanded', 'et_builder' ),
			'collapsed'               => esc_html__( 'Collapsed', 'et_builder' ),
			'locked'                  => esc_html__( 'Locked', 'et_builder' ),
			'unlocked'                => esc_html__( 'Unlocked', 'et_builder' ),
			'cloned'                  => esc_html__( 'Cloned', 'et_builder' ),
			'cleared'                 => esc_html__( 'Cleared', 'et_builder' ),
			'enabled'                 => esc_html__( 'Enabled', 'et_builder' ),
			'disabled'                => esc_html__( 'Disabled', 'et_builder' ),
			'copied'                  => esc_html__( 'Copied', 'et_builder' ),
			'reset'                   => esc_html__( 'Reset', 'et_builder' ),
			'cut'                     => esc_html__( 'Cut', 'et_builder' ),
			'pasted'                  => esc_html__( 'Pasted', 'et_builder' ),
			'pasted_styles'           => esc_html__( 'Pasted Styles', 'et_builder' ),
			'renamed'                 => esc_html__( 'Renamed', 'et_builder' ),
			'loaded'                  => esc_html__( 'Loaded', 'et_builder' ),
			'turnon'                  => esc_html__( 'Turned On', 'et_builder' ),
			'turnoff'                 => esc_html__( 'Turned Off', 'et_builder' ),
			'globalon'                => esc_html__( 'Made Global', 'et_builder' ),
			'globaloff'               => esc_html__( 'Disabled Global', 'et_builder' ),
			'configured'              => esc_html__( 'Configured', 'et_builder' ),
			'find_replace'            => esc_html__( 'Find & Replace', 'et_builder' ),
			'extend_styles'           => esc_html__( 'Extend Styles', 'et_builder' ),
			'imported'                => esc_html__( 'Imported From Layout', 'et_builder' ),
			'presetCreated'           => esc_html__( 'Preset Created For', 'et_builder' ),
			'presetNameChanged'       => esc_html__( 'Preset Name Changed For', 'et_builder' ),
			'presetDeleted'           => esc_html__( 'Preset Deleted For', 'et_builder' ),
			'presetAssignedAsDefault' => esc_html__( 'Preset Assigned As Default For', 'et_builder' ),
		),
		'noun'     => array(
			'section'           => esc_html__( 'Section', 'et_builder' ),
			'saved_section'     => esc_html__( 'Saved Section', 'et_builder' ),
			'fullwidth_section' => esc_html__( 'Fullwidth Section', 'et_builder' ),
			'specialty_section' => esc_html__( 'Specialty Section', 'et_builder' ),
			'column'            => esc_html__( 'Column', 'et_builder' ),
			'row'               => esc_html__( 'Row', 'et_builder' ),
			'saved_row'         => esc_html__( 'Saved Row', 'et_builder' ),
			'module'            => esc_html__( 'Module', 'et_builder' ),
			'saved_module'      => esc_html__( 'Saved Module', 'et_builder' ),
			'page'              => esc_html__( 'Page', 'et_builder' ),
			'layout'            => et_builder_i18n( 'Layout' ),
			'abtesting'         => esc_html__( 'Split Testing', 'et_builder' ),
			'settings'          => esc_html__( 'Settings', 'et_builder' ),
		),
		'addition' => array(
			'phone'   => esc_html__( 'on Phone', 'et_builder' ),
			'tablet'  => esc_html__( 'on Tablet', 'et_builder' ),
			'desktop' => esc_html__( 'on Desktop', 'et_builder' ),
		),
	);
}

/**
 * Page Settings Metabox code is included in builder.js which won't be loaded unless BB is.
 * In such cases (eg BFB or GB are enabled) we provide the mbox js logic in a separate file.
 *
 * @return void
 */
function et_pb_metabox_scripts() {
	// Only act if `builder.js` isn't enqueued.
	if ( ! wp_script_is( 'et_pb_admin_js' ) ) {
		global $typenow;
		wp_enqueue_script( 'et_page_settings_metabox_js', ET_BUILDER_URI . '/scripts/page-settings-metabox.js', array( 'jquery' ), ET_BUILDER_PRODUCT_VERSION, true );
		$pb_options = array(
			'post_type'                => $typenow,
			'is_third_party_post_type' => et_builder_is_post_type_custom( $typenow ) ? 'yes' : 'no',
		);
		wp_localize_script( 'et_page_settings_metabox_js', 'et_pb_options', $pb_options );
	}
}

/**
 * Prevents the Builder mbox from being hidden.
 *
 * @param string[] $hidden all hidden metaboxes.
 *
 * @return mixed
 */
function et_pb_hidden_meta_boxes( $hidden ) {
	$found = array_search( 'et_pb_layout', $hidden, true );
	if ( false !== $found ) {
		unset( $hidden[ $found ] );
	}
	return $hidden;
}

/**
 * Add "The Divi Builder" BB metabox.
 *
 * @param string  $post_type post type.
 * @param WP_Post $post post object.
 */
function et_pb_add_custom_box( $post_type, $post ) {
	add_action( 'admin_enqueue_scripts', 'et_pb_metabox_scripts', 99 );
	// Do not add BB metabox if GB is active on this page.
	if ( et_core_is_gutenberg_enabled() ) {
		return;
	}

	// Do not add BB metabox if builder is not activate on this page.
	if ( et_builder_bfb_enabled() && ! et_pb_is_pagebuilder_used( $post->ID ) ) {
		return;
	}

	$post_types = et_builder_get_builder_post_types();
	$add        = in_array( $post_type, $post_types, true );

	if ( ! $add && ! empty( $post ) && et_builder_enabled_for_post( $post->ID ) ) {
		$add = true;
	}

	if ( $add ) {
		add_meta_box( ET_BUILDER_LAYOUT_POST_TYPE, esc_html__( 'The Divi Builder', 'et_builder' ), 'et_pb_pagebuilder_meta_box', $post_type, 'normal', 'high' );
	}
}

if ( ! function_exists( 'et_pb_get_the_author_posts_link' ) ) :
	/**
	 * Return a post author link markup.
	 */
	function et_pb_get_the_author_posts_link() {
		global $authordata, $post;

		// Fallback for preview.
		if ( empty( $authordata ) && isset( $post->post_author ) ) {
			$authordata = get_userdata( $post->post_author ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- If $authordata is not set then set it.
		}

		// If $authordata is empty, don't continue.
		if ( empty( $authordata ) ) {
			return;
		}

		$link = sprintf(
			'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
			esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
			// translators: post author name.
			esc_attr( sprintf( __( 'Posts by %s', 'et_builder' ), get_the_author() ) ),
			get_the_author()
		);
		return apply_filters( 'the_author_posts_link', $link );
	}
endif;

if ( ! function_exists( 'et_pb_get_comments_popup_link' ) ) :
	/**
	 * Return comments link.
	 *
	 * @param bool|string $zero text to display when 0 comments.
	 * @param bool|string $one text to display when 1 comment.
	 * @param bool|string $more text to display for more than 1 comments.
	 */
	function et_pb_get_comments_popup_link( $zero = false, $one = false, $more = false ) {
		$id     = get_the_ID();
		$number = get_comments_number( $id );

		if ( 0 === $number && ! comments_open() && ! pings_open() ) {
			return;
		}

		if ( $number > 1 ) {
			// translators: more comments text.
			$output = str_replace( '%', number_format_i18n( $number ), ( false === $more ) ? __( '% Comments', 'et_builder' ) : $more );
		} elseif ( 0 === $number ) {
			$output = ( false === $zero ) ? __( 'No Comments', 'et_builder' ) : $zero;
		} else { // must be one.
			$output = ( false === $one ) ? __( '1 Comment', 'et_builder' ) : $one;
		}

		do_action( 'et_builder_before_comments_number' );

		$link = '<span class="comments-number"><a href="' . esc_url( get_permalink() . '#respond' ) . '">' . apply_filters( 'comments_number', esc_html( $output ), esc_html( $number ) ) . '</a></span>';

		do_action( 'et_builder_after_comments_number' );

		return $link;
	}
endif;

if ( ! function_exists( 'et_pb_postinfo_meta' ) ) :
	/**
	 * Return post meta.
	 *
	 * @param string[] $postinfo post info e.g date, author, categories.
	 * @param string   $date_format date format.
	 * @param string   $comment_zero text to display for 0 comments.
	 * @param string   $comment_one text to display for 1 comments.
	 * @param string   $comment_more text to display for more comments.
	 */
	function et_pb_postinfo_meta( $postinfo, $date_format, $comment_zero, $comment_one, $comment_more ) {
		$postinfo_meta = array();

		if ( in_array( 'author', $postinfo, true ) ) {
			$postinfo_meta[] = ' ' . esc_html__( 'by', 'et_builder' ) . ' <span class="author vcard">' . et_pb_get_the_author_posts_link() . '</span>';
		}

		if ( in_array( 'date', $postinfo, true ) ) {
			$postinfo_meta[] = '<span class="published">' . esc_html( get_the_time( $date_format ) ) . '</span>';
		}

		if ( in_array( 'categories', $postinfo, true ) ) {
			$categories_list = get_the_category_list( ', ' );

			// do not output anything if no categories retrieved.
			if ( '' !== $categories_list ) {
				$postinfo_meta[] = $categories_list;
			}
		}

		if ( in_array( 'comments', $postinfo, true ) ) {
			$postinfo_meta[] = et_pb_get_comments_popup_link( $comment_zero, $comment_one, $comment_more );
		}

		return implode( ' | ', array_filter( $postinfo_meta ) );
	}
endif;


if ( ! function_exists( 'et_pb_fix_shortcodes' ) ) {
	/**
	 * Fix shortcodes? @todo Add function doc.
	 *
	 * @param string $content post content.
	 * @param bool   $is_raw_content whether content is row.
	 *
	 * @return string|string[]|null
	 */
	function et_pb_fix_shortcodes( $content, $is_raw_content = false ) {
		// Turn back the "data-et-target-link" attribute as "target" attribte
		// that has been made before saving the content in "et_fb_process_to_shortcode" function.
		if ( false !== strpos( $content, 'data-et-target-link=' ) ) {
			$content = str_replace( ' data-et-target-link=', ' target=', $content );
		}

		if ( $is_raw_content ) {
			$content = et_builder_replace_code_content_entities( $content );
			$content = ET_Builder_Element::convert_smart_quotes_and_amp( $content );
		}

		$slugs = ET_Builder_Element::get_module_slugs_by_post_type();

		// The current patterns take care to replace only the shortcodes that extends `ET_Builder_Element` class
		// In order to avoid cases like this: `[3:45]<br>`
		// The pattern looks like this `(\[\/?(et_pb_section|et_pb_column|et_pb_row)[^\]]*\])`.
		$shortcode_pattern = sprintf( '(\[\/?(%s)[^\]]*\])', implode( '|', $slugs ) );
		$opening_pattern   = '(<br\s*\/?>|<p>|\n)+';
		$closing_pattern   = '(<br\s*\/?>|<\/p>|\n)+';
		$space_pattern     = '[\s*|\n]*';

		// Replace `]</p>`, `]<br>` `]\n` with `]`
		// Make sure to remove any closing `</p>` tags or line breaks or new lines after shortcode tag.
		$pattern_1 = sprintf( '/%1$s%2$s%3$s/', $shortcode_pattern, $space_pattern, $closing_pattern );

		// Replace `<p>[`, `<br>[` `\n[` with `[`
		// Make sure to remove any opening `<p>` tags or line breaks or new lines before shortcode tag.
		$pattern_2 = sprintf( '/%1$s%2$s%3$s/', $opening_pattern, $space_pattern, $shortcode_pattern );

		$content = preg_replace( $pattern_1, '$1', $content );
		$content = preg_replace( $pattern_2, '$2', $content );

		return $content;
	}
}

if ( ! function_exists( 'et_pb_load_global_module' ) ) {
	/**
	 * Return gloval module content.
	 *
	 * @param integer $global_id layout id.
	 * @param string  $row_type row type.
	 * @param string  $prev_bg Previous background color.
	 * @param string  $next_bg next background color.
	 *
	 * @return string|string[]|null
	 */
	function et_pb_load_global_module( $global_id, $row_type = '', $prev_bg = '', $next_bg = '' ) {
		$global_shortcode = '';

		if ( '' !== $global_id ) {
			$query = new WP_Query(
				array(
					'p'         => (int) $global_id,
					'post_type' => array(
						ET_BUILDER_LAYOUT_POST_TYPE,
						ET_THEME_BUILDER_HEADER_LAYOUT_POST_TYPE,
						ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE,
						ET_THEME_BUILDER_FOOTER_LAYOUT_POST_TYPE,
					),
				)
			);

			if ( ! empty( $query->post ) ) {
				// Call the_post() to properly configure post data. Make sure to call the_post() and
				// wp_reset_postdata() only if the posts result exist to avoid unexpected issues.
				$query->the_post();

				wp_reset_postdata();

				$global_shortcode = $query->post->post_content;

				if ( '' !== $row_type && 'et_pb_row_inner' === $row_type ) {
					$global_shortcode = str_replace( 'et_pb_row', 'et_pb_row_inner', $global_shortcode );
					$global_shortcode = str_replace( 'et_pb_column', 'et_pb_column_inner', $global_shortcode );
				}
			}
		}

		// Set provided prev_background_color.
		if ( ! empty( $prev_bg ) ) {
			$global_shortcode = preg_replace( '/prev_background_color="(.*?)"/', 'prev_background_color="' . $prev_bg . '"', $global_shortcode, 1 );
		}

		// Set provided next_background_color.
		if ( ! empty( $next_bg ) ) {
			$global_shortcode = preg_replace( '/next_background_color="(.*?)"/', 'next_background_color="' . $next_bg . '"', $global_shortcode, 1 );
		}

		return $global_shortcode;
	}
}

if ( ! function_exists( 'et_pb_extract_shortcode_content' ) ) {
	/**
	 * Return the shortcode content.
	 *
	 * @param string $content content.
	 * @param string $shortcode_name shortcode name.
	 *
	 * @return bool|false|string
	 */
	function et_pb_extract_shortcode_content( $content, $shortcode_name ) {

		$start = strpos( $content, ']' ) + 1;
		$end   = strrpos( $content, '[/' . $shortcode_name );

		if ( false !== $end ) {
			$content = substr( $content, $start, $end - $start );
		} else {
			$content = (bool) false;
		}

		return $content;
	}
}

if ( ! function_exists( 'et_pb_remove_shortcode_content' ) ) {
	/**
	 * Remove the content part of the shortcode.
	 *
	 * @param string $content content.
	 * @param string $shortcode_name shortcode name.
	 *
	 * @return string|string[]
	 */
	function et_pb_remove_shortcode_content( $content, $shortcode_name ) {
		$shortcode_content = et_pb_extract_shortcode_content( $content, $shortcode_name );

		if ( $shortcode_content ) {
			// Anchor to the ][ brackets around the content so content that appears in
			// attributes does not get removed as well.
			return str_replace( ']' . $shortcode_content . '[', '][', $content );
		}

		return $content;
	}
}

if ( ! function_exists( 'et_pb_get_global_module_content' ) ) {
	/**
	 * Return global module content.
	 *
	 * @param string $content content.
	 * @param string $shortcode_name shortcode slug.
	 * @param bool   $for_inner_row whether we getting module content for inner row.
	 *
	 * @return bool|false|string|string[]|null
	 */
	function et_pb_get_global_module_content( $content, $shortcode_name, $for_inner_row = false ) {
		/**
		 * Filter list of modules where we don't need to apply autop to the global module content.
		 *
		 * @param array Module slugs list.
		 */
		$custom_autop_ignored_modules  = apply_filters( 'et_builder_global_modules_ignore_autop', array() );
		$custom_autop_ignored_modules  = is_array( $custom_autop_ignored_modules ) ? $custom_autop_ignored_modules : array();
		$default_autop_ignored_modules = array_merge( array( 'et_pb_code', 'et_pb_fullwidth_code' ), $custom_autop_ignored_modules );

		// Do not apply autop to code modules.
		if ( in_array( $shortcode_name, $default_autop_ignored_modules, true ) ) {
			return et_pb_extract_shortcode_content( $content, $shortcode_name );
		}

		$original_code_modules = array();
		$shortcode_content     = et_pb_extract_shortcode_content( $content, $shortcode_name );

		// Getting content for Global row when it's turned to inner row in specialty section
		// Need to make sure it wrapped in et_pb_column_inner, not et_pb_column.
		if ( $for_inner_row && false === strpos( $shortcode_content, '[et_pb_column_inner' ) ) {
			$shortcode_content = str_replace( 'et_pb_column', 'et_pb_column_inner', $shortcode_content );
		}

		// Get all the code and fullwidth code modules from content.
		preg_match_all( '/(\[et_pb(_fullwidth_code|_code).+?\[\/et_pb(_fullwidth_code|_code)\])/s', $shortcode_content, $original_code_modules );

		$global_content = et_pb_fix_shortcodes( wpautop( $shortcode_content ) );

		// Replace content modified by wpautop for code and fullwidth code modules with original content.
		if ( ! empty( $original_code_modules ) ) {
			global $et_pb_global_code_replacements;

			$et_pb_global_code_replacements = $original_code_modules[0];
			$global_content                 = preg_replace_callback( '/(\[et_pb(_fullwidth_code|_code).+?\[\/et_pb(_fullwidth_code|_code)\])/s', 'et_builder_get_global_code_replacement', $global_content );
		}

		return $global_content;
	}
}

if ( ! function_exists( 'et_builder_get_global_code_replacement' ) ) {
	/**
	 * Retrieve the global code original instance to replace the modified in global code shortcode.
	 *
	 * @param array $matches found matches.
	 *
	 * @return mixed
	 */
	function et_builder_get_global_code_replacement( $matches ) {
		global $et_pb_global_code_replacements;

		return array_shift( $et_pb_global_code_replacements );
	}
}


if ( ! function_exists( 'et_builder_activate_bfb_auto_draft' ) ) {
	/**
	 * Force activate post_id which has auto-draft status
	 */
	function et_builder_activate_bfb_auto_draft() {
		et_core_security_check( 'edit_posts', 'et_enable_bfb_nonce' );

		$post_id = ! empty( $_POST['et_post_id'] ) ? absint( $_POST['et_post_id'] ) : 0;

		if ( 0 === $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
			die();
		}

		// et_builder_activate_bfb_auto_draft() is executed when post title and content empty which means post_status is still lik. ely
		// to be "auto-draft". "auto-draft" status returns 404 page; thus post status needs to be updated to "draft".
		wp_update_post(
			array(
				'ID'          => $post_id,
				'post_status' => 'draft',
			)
		);

		update_post_meta( $post_id, '_et_pb_use_builder', 'on' );
		die();
	}
}
add_action( 'wp_ajax_et_builder_activate_bfb_auto_draft', 'et_builder_activate_bfb_auto_draft' );

if ( ! function_exists( 'et_builder_ajax_toggle_bfb' ) ) {
	/**
	 * Ajax Callback :: Switch To The New Divi Builder.
	 */
	function et_builder_ajax_toggle_bfb() {
		et_core_security_check( 'manage_options', 'et_builder_toggle_bfb', 'nonce', '_GET' );
		$enable   = isset( $_GET['enable'] ) && '1' === $_GET['enable'];
		$redirect = isset( $_GET['redirect'] ) ? esc_url_raw( $_GET['redirect'] ) : '';
		if ( empty( $redirect ) && isset( $_SERVER['HTTP_REFERER'] ) ) {
			$redirect = esc_url_raw( $_SERVER['HTTP_REFERER'] );
		}

		if ( empty( $redirect ) ) {
			$redirect = esc_url_raw( admin_url( '/' ) );
		}
		et_builder_toggle_bfb( $enable );

		set_transient( 'et_builder_show_bfb_welcome_modal', true, 0 );

		wp_safe_redirect( $redirect );
		exit;
	}
}
add_action( 'wp_ajax_et_builder_toggle_bfb', 'et_builder_ajax_toggle_bfb' );

/**
 * Return font weight select input element html.
 *
 * @return string
 */
function et_generate_font_weight_select_output() {
	$all_weights = et_builder_get_font_weight_list();
	$output      = '';

	foreach ( $all_weights as $number => $name ) {
		$output .= sprintf(
			'<label><input type="checkbox" name="et_font_weight[]" value="%1$s" />%2$s %3$s</label>',
			esc_attr( $number ),
			esc_html( $name ),
			esc_html( $number )
		);
	}

	return $output;
}

/**
 * Return regular and specialty layouts.
 *
 * @return mixed|void
 */
function et_builder_get_columns() {
	$columns = array(
		'specialty' => array(
			'1_2,1_2'     => array(
				'position' => '1,0',
				'columns'  => '3',
			),
			'1_2,1_2'     => array(
				'position' => '0,1',
				'columns'  => '3',
			),
			'1_4,3_4'     => array(
				'position' => '0,1',
				'columns'  => '3',
			),
			'3_4,1_4'     => array(
				'position' => '1,0',
				'columns'  => '3',
			),
			'1_4,1_4,1_2' => array(
				'position' => '0,0,1',
				'columns'  => '3',
			),
			'1_2,1_4,1_4' => array(
				'position' => '1,0,0',
				'columns'  => '3',
			),
			'1_4,1_2,1_4' => array(
				'position' => '0,1,0',
				'columns'  => '3',
			),
			'1_3,2_3'     => array(
				'position' => '0,1',
				'columns'  => '4',
			),
			'2_3,1_3'     => array(
				'position' => '1,0',
				'columns'  => '4',
			),
		),
		'regular'   => array(
			'4_4',
			'1_2,1_2',
			'1_3,1_3,1_3',
			'1_4,1_4,1_4,1_4',
			'1_5,1_5,1_5,1_5,1_5',
			'1_6,1_6,1_6,1_6,1_6,1_6',
			'2_5,3_5',
			'3_5,2_5',
			'1_3,2_3',
			'2_3,1_3',
			'1_4,3_4',
			'3_4,1_4',
			'1_4,1_2,1_4',
			'1_5,3_5,1_5',
			'1_4,1_4,1_2',
			'1_2,1_4,1_4',
			'1_5,1_5,3_5',
			'3_5,1_5,1_5',
			'1_6,1_6,1_6,1_2',
			'1_2,1_6,1_6,1_6',
		),
	);

	return apply_filters( 'et_builder_get_columns', $columns );
}

/**
 * Return columns layout.
 *
 * @return mixed|void
 */
function et_builder_get_columns_layout() {
	$layout_columns =
		'<% if ( typeof et_pb_specialty !== \'undefined\' && et_pb_specialty === \'on\' ) { %>
			<li data-layout="1_2,1_2" data-specialty="1,0" data-specialty_columns="3">
				<div class="et_pb_layout_column et_pb_column_layout_1_2 et_pb_variations et_pb_3_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
					</div>
				</div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2 et_pb_specialty_column"></div>
			</li>

			<li data-layout="1_2,1_2" data-specialty="0,1" data-specialty_columns="3">
				<div class="et_pb_layout_column et_pb_column_layout_1_2 et_pb_specialty_column"></div>

				<div class="et_pb_layout_column et_pb_column_layout_1_2 et_pb_variations et_pb_3_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
					</div>
				</div>
			</li>

			<li data-layout="1_4,3_4" data-specialty="0,1" data-specialty_columns="3">
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
				<div class="et_pb_layout_column et_pb_column_layout_3_4 et_pb_variations et_pb_3_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
					</div>
				</div>
			</li>

			<li data-layout="3_4,1_4" data-specialty="1,0" data-specialty_columns="3">
				<div class="et_pb_layout_column et_pb_column_layout_3_4 et_pb_variations et_pb_3_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
					</div>
				</div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
			</li>

			<li data-layout="1_4,1_2,1_4" data-specialty="0,1,0" data-specialty_columns="3">
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2 et_pb_variations et_pb_3_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
					</div>
				</div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
			</li>

			<li data-layout="1_2,1_4,1_4" data-specialty="1,0,0" data-specialty_columns="3">
				<div class="et_pb_layout_column et_pb_column_layout_1_2 et_pb_variations et_pb_3_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
					</div>
				</div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
			</li>

			<li data-layout="1_4,1_4,1_2" data-specialty="0,0,1" data-specialty_columns="3">
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4 et_pb_specialty_column"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2 et_pb_variations et_pb_3_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
						<div class="et_pb_variation et_pb_variation_1_3"></div>
					</div>
				</div>
			</li>

			<li data-layout="1_3,2_3" data-specialty="0,1" data-specialty_columns="4">
				<div class="et_pb_layout_column et_pb_column_layout_1_3 et_pb_specialty_column"></div>
				<div class="et_pb_layout_column et_pb_column_layout_2_3 et_pb_variations et_pb_3_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_4"></div>
						<div class="et_pb_variation et_pb_variation_1_4"></div>
						<div class="et_pb_variation et_pb_variation_1_4"></div>
						<div class="et_pb_variation et_pb_variation_1_4"></div>
					</div>
				</div>
			</li>

			<li data-layout="2_3,1_3" data-specialty="1,0" data-specialty_columns="4">
				<div class="et_pb_layout_column et_pb_column_layout_2_3 et_pb_variations et_pb_3_variations">
					<div class="et_pb_variation et_pb_variation_full"></div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_2"></div>
						<div class="et_pb_variation et_pb_variation_1_2"></div>
					</div>
					<div class="et_pb_variation_row">
						<div class="et_pb_variation et_pb_variation_1_4"></div>
						<div class="et_pb_variation et_pb_variation_1_4"></div>
						<div class="et_pb_variation et_pb_variation_1_4"></div>
						<div class="et_pb_variation et_pb_variation_1_4"></div>
					</div>
				</div>
				<div class="et_pb_layout_column et_pb_column_layout_1_3 et_pb_specialty_column"></div>
			</li>
		<% } else if ( typeof view !== \'undefined\' && typeof view.model.attributes.specialty_columns !== \'undefined\' ) { %>
			<li data-layout="4_4">
				<div class="et_pb_layout_column et_pb_column_layout_fullwidth"></div>
			</li>
			<li data-layout="1_2,1_2">
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
			</li>
			<% if ( view.model.attributes.layout === "2_3" ) { %>
				<li data-layout="1_4,1_4,1_4,1_4">
					<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
					<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
					<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
					<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				</li>
			<% } else { %>
			    <li data-layout="1_3,1_3,1_3">
					<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
					<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
					<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
				</li>
			<% } %>
		<% } else { %>
			<li data-layout="4_4">
				<div class="et_pb_layout_column et_pb_column_layout_fullwidth"></div>
			</li>
			<li data-layout="1_2,1_2">
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
			</li>
			<li data-layout="1_3,1_3,1_3">
				<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
			</li>
			<li data-layout="1_4,1_4,1_4,1_4">
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
			</li>
			<li data-layout="1_5,1_5,1_5,1_5,1_5">
				<div class="et_pb_layout_column et_pb_column_layout_1_5"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_5"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_5"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_5"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_5"></div>
			</li>
			<li data-layout="1_6,1_6,1_6,1_6,1_6,1_6">
				<div class="et_pb_layout_column et_pb_column_layout_1_6"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_6"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_6"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_6"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_6"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_6"></div>
			</li>
			<li data-layout="2_5,3_5">
				<div class="et_pb_layout_column et_pb_column_layout_2_5"></div>
				<div class="et_pb_layout_column et_pb_column_layout_3_5"></div>
			</li>
			<li data-layout="3_5,2_5">
				<div class="et_pb_layout_column et_pb_column_layout_3_5"></div>
				<div class="et_pb_layout_column et_pb_column_layout_2_5"></div>
			</li>
			<li data-layout="1_3,2_3">
				<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
				<div class="et_pb_layout_column et_pb_column_layout_2_3"></div>
			</li>
			<li data-layout="2_3,1_3">
				<div class="et_pb_layout_column et_pb_column_layout_2_3"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_3"></div>
			</li>
			<li data-layout="1_4,3_4">
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_3_4"></div>
			</li>
			<li data-layout="3_4,1_4">
				<div class="et_pb_layout_column et_pb_column_layout_3_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
			</li>
			<li data-layout="1_4,1_2,1_4">
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
			</li>
			<li data-layout="1_5,3_5,1_5">
				<div class="et_pb_layout_column et_pb_column_layout_1_5"></div>
				<div class="et_pb_layout_column et_pb_column_layout_3_5"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_5"></div>
			</li>
			<li data-layout="1_4,1_4,1_2">
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
			</li>
			<li data-layout="1_2,1_4,1_4">
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_4"></div>
			</li>
			<li data-layout="1_5,1_5,3_5">
				<div class="et_pb_layout_column et_pb_column_layout_1_5"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_5"></div>
				<div class="et_pb_layout_column et_pb_column_layout_3_5"></div>
			</li>
			<li data-layout="3_5,1_5,1_5">
				<div class="et_pb_layout_column et_pb_column_layout_3_5"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_5"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_5"></div>
			</li>
			<li data-layout="1_6,1_6,1_6,1_2">
				<div class="et_pb_layout_column et_pb_column_layout_1_6"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_6"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_6"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
			</li>
			<li data-layout="1_2,1_6,1_6,1_6">
				<div class="et_pb_layout_column et_pb_column_layout_1_2"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_6"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_6"></div>
				<div class="et_pb_layout_column et_pb_column_layout_1_6"></div>
			</li>
	<%
		}
	%>';

	return apply_filters( 'et_builder_layout_columns', $layout_columns );
}

/**
 * Display meta box in admin screen.
 */
function et_pb_pagebuilder_meta_box() {
	global $typenow, $post;

	do_action( 'et_pb_before_page_builder' );

	if ( et_builder_bfb_enabled() ) {
		$new_page_url = false;
		$is_new_page  = false;
		$edit_page_id = get_the_ID();
		$no_rtl_class = is_rtl() && 'on' === et_get_option( 'divi_disable_translations', 'off' ) ? 'et-fb-no-rtl' : '';

		// Polylang creates copy of page and BFB should be loaded on page which is not saved yet and cannot be loaded on FE
		// Therefore load the homepage and replace the content for BFB to make it load with content from other post.
		if ( 'add' === get_current_screen()->action || (int) get_option( 'page_for_posts' ) === $edit_page_id ) {
			$new_page_url = get_home_url();
			$is_new_page  = true;
		}

		$bfb_url = et_core_intentionally_unescaped( et_fb_get_bfb_url( $new_page_url, $is_new_page, $edit_page_id ), 'fixed_string' );
		// If Admin is SSL but FE is not, we need to fix VB url or it won't work
		// because trying to load insecure resource.
		$bfb_url = set_url_scheme( $bfb_url, is_ssl() ? 'https' : 'http' );

		// phpcs:disable WordPress.Security.EscapeOutput -- XSS safe.
		echo "
			<div class='et_divi_builder et-bfb-page-preloading et_divi_builder_bottom_margin'>
				<script>
					var iframe = document.body.appendChild(document.createElement('iframe'));

					iframe.id        = 'et-bfb-app-frame';

					document.body.classList.add('et-db');
					document.body.classList.add('et-bfb');

					if ('' !== '{$no_rtl_class}') {
						document.body.classList.add('{$no_rtl_class}');
					}

					document.addEventListener('DOMContentLoaded', function() {
						var outer = jQuery('<div>', {id: 'et-boc', class: 'et-boc'});
						var inner = jQuery('<div>', {class: 'et-l'});
						var app   = jQuery('<div>', {id: 'et-fb-app'});

						inner.append(app);
						outer.append(inner);
						jQuery('#wpwrap').wrap(outer);

						jQuery('#et-bfb-app-frame').appendTo('#et_pb_layout .et_divi_builder');
					    iframe.src = '{$bfb_url}';

						// Add first-visible classname to first visible metabox on #normal-sortables
						jQuery('#et_pb_layout')
							.parent()
							.children(':visible')
							.first()
							.addClass('first-visible');
					});
				</script>
			</div>
		";
		// phpcs:enable

		return;
	}

	$new_builder_url_args = array(
		'action' => 'et_builder_toggle_bfb',
		'enable' => '1',
		'nonce'  => wp_create_nonce( 'et_builder_toggle_bfb' ),
	);

	$new_builder_url = add_query_arg( $new_builder_url_args, admin_url( 'admin-ajax.php' ) );

	// Disable BFB notification on Extra category builder. BFB support for Extra category builder will be added post inital launch
	// This option available for admins only.
	if ( apply_filters( 'et_pb_display_bfb_notification_under_bb', true ) && current_user_can( 'manage_options' ) && et_pb_is_allowed( 'use_visual_builder' ) && et_pb_is_allowed( 'divi_builder_control' ) ) {
		echo '<div class="et-bfb-optin-cta">';
			echo '<p class="et-bfb-optin-cta__message et-bfb-optin-cta__message--warning">';
				echo esc_html__( 'A New And Improved Divi Builder Experience Is Available!', 'et_builder' );
				echo '<a href="' . esc_url( $new_builder_url ) . '" class="et-bfb-optin-cta__button">';
					echo esc_html__( 'Switch To The New Divi Builder', 'et_builder' );
				echo '</a>';
			echo '</p>';
		echo '</div>';
	}

	echo '<div id="et_pb_hidden_editor">';
	echo '<div id="et_pb_content_editor">';
		$content_editor_settings = array(
			'media_buttons' => true,
			'tinymce'       => array(
				'wp_autoresize_on' => true,
			),
		);
		wp_editor( '', 'et_pb_content', $content_editor_settings );
		echo '</div>';
		echo '<div id="et_pb_description_editor">';
		$description_editor_settings = array(
			'media_buttons' => true,
			'tinymce'       => array(
				'wp_autoresize_on' => true,
			),
		);
		wp_editor( '', 'et_pb_description', $description_editor_settings );
		echo '</div>';
		echo '<div id="et_pb_footer_content_editor">';
		$footer_content_editor_settings = array(
			'media_buttons' => true,
			'tinymce'       => array(
				'wp_autoresize_on' => true,
			),
		);
		wp_editor( '', 'et_pb_footer_content', $footer_content_editor_settings );
		echo '</div>';
		echo '</div>';

		printf(
			'<div id="et_pb_main_container" class="post-type-%1$s%2$s"></div>',
			esc_attr( $typenow ),
			! et_pb_is_allowed( 'move_module' ) ? ' et-pb-disable-sort' : ''
		);
	$rename_module_menu  = et_pb_is_allowed( 'edit_module' ) && ( et_pb_is_allowed( 'general_settings' ) || et_pb_is_allowed( 'advanced_settings' ) || et_pb_is_allowed( 'custom_css_settings' ) ) ? sprintf(
		'<%% if ( this.hasOption( "rename" ) ) { %%>
			<li><a class="et-pb-right-click-rename" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Rename', 'et_builder' )
	) : '';
	$copy_module_menu    = et_pb_is_allowed( 'add_module' ) ? sprintf(
		'<%% if ( this.hasOption( "copy" ) ) { %%>
			<li><a class="et-pb-right-click-copy" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Copy', 'et_builder' )
	) : '';
	$paste_after_menu    = et_pb_is_allowed( 'add_module' ) ? sprintf(
		'<%% if ( this.hasOption( "paste-after" ) ) { %%>
			<li><a class="et-pb-right-click-paste-after" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Paste After', 'et_builder' )
	) : '';
	$paste_menu_item     = et_pb_is_allowed( 'add_module' ) ? sprintf(
		'<%% if ( this.hasOption( "paste-column" ) ) { %%>
			<li><a class="et-pb-right-click-paste-column" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Paste', 'et_builder' )
	) : '';
	$paste_app_menu_item = et_pb_is_allowed( 'add_module' ) ? sprintf(
		'<%% if ( this.hasOption( "paste-app" ) ) { %%>
			<li><a class="et-pb-right-click-paste-app" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Paste', 'et_builder' )
	) : '';
	$save_to_lib_menu    = et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'save_library' ) ? sprintf(
		'<%% if ( this.hasOption( "save-to-library") ) { %%>
			<li><a class="et-pb-right-click-save-to-library" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Save to Library', 'et_builder' )
	) : '';
	$lock_unlock_menu    = et_pb_is_allowed( 'lock_module' ) ? sprintf(
		'<%% if ( this.hasOption( "lock" ) ) { %%>
			<li><a class="et-pb-right-click-lock" href="#"><span class="unlock">%1$s</span><span class="lock">%2$s</span></a></li>
		<%% } %%>',
		esc_html__( 'Unlock', 'et_builder' ),
		esc_html__( 'Lock', 'et_builder' )
	) : '';
	$enable_disable_menu = et_pb_is_allowed( 'disable_module' ) ? sprintf(
		'<%% if ( this.hasOption( "disable" ) ) { %%>
			<li><a class="et-pb-right-click-disable" href="#"><span class="enable">%1$s</span><span class="disable">%2$s</span></a>
				<span class="et_pb_disable_on_options"><span class="et_pb_disable_on_option et_pb_disable_on_phone"></span><span class="et_pb_disable_on_option et_pb_disable_on_tablet"></span><span class="et_pb_disable_on_option et_pb_disable_on_desktop"></span></span>
			</li>
		<%% } %%>',
		esc_html__( 'Enable', 'et_builder' ),
		esc_html__( 'Disable', 'et_builder' )
	) : '';

	// Hide AB Testing menu if current post is Divi Library.
	$is_divi_library       = 'et_pb_layout' === $post->post_type;
	$start_ab_testing_menu = et_pb_is_allowed( 'ab_testing' ) && ! $is_divi_library ? sprintf(
		'<%% if ( this.hasOption( "start-ab-testing") ) { %%>
			<li><a class="et-pb-right-click-start-ab-testing" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Split Test', 'et_builder' )
	) : '';
	$end_ab_testing_menu   = et_pb_is_allowed( 'ab_testing' ) && ! $is_divi_library ? sprintf(
		'<%% if ( this.hasOption( "end-ab-testing") ) { %%>
			<li><a class="et-pb-right-click-end-ab-testing" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'End Split Test', 'et_builder' )
	) : '';
	$disable_global_menu   = et_pb_is_allowed( 'edit_module' ) && et_pb_is_allowed( 'edit_global_library' ) ? sprintf(
		'<%% if ( this.hasOption( "disable-global") ) { %%>
			<li><a class="et-pb-right-click-disable-global" href="#">%1$s</a></li>
		<%% } %%>',
		esc_html__( 'Disable Global', 'et_builder' )
	) : '';
	// Right click options Template.
	printf(
		'<script type="text/template" id="et-builder-right-click-controls-template">
		<ul class="options">
			<%% if ( "module" !== this.options.model.attributes.type || _.contains( %13$s, this.options.model.attributes.module_type ) ) { %%>
				%1$s

				%15$s

				%16$s

				%17$s

				%8$s

				<%% if ( this.hasOption( "undo" ) ) { %%>
				<li><a class="et-pb-right-click-undo" href="#">%9$s</a></li>
				<%% } %%>

				<%% if ( this.hasOption( "redo" ) ) { %%>
				<li><a class="et-pb-right-click-redo" href="#">%10$s</a></li>
				<%% } %%>

				%2$s

				%3$s

				<%% if ( this.hasOption( "collapse" ) ) { %%>
				<li><a class="et-pb-right-click-collapse" href="#"><span class="expand">%4$s</span><span class="collapse">%5$s</span></a></li>
				<%% } %%>

				%6$s

				%7$s

				%12$s

				%11$s

			<%% } %%>

			<%% if ( this.hasOption( "preview" ) ) { %%>
			<li><a class="et-pb-right-click-preview" href="#">%14$s</a></li>
			<%% } %%>
		</ul>
		</script>',
		et_core_esc_previously( $rename_module_menu ),
		et_core_esc_previously( $enable_disable_menu ),
		et_core_esc_previously( $lock_unlock_menu ),
		et_builder_i18n( 'Expand' ),
		esc_html__( 'Collapse', 'et_builder' ), // #5
		et_core_esc_previously( $copy_module_menu ),
		et_core_esc_previously( $paste_after_menu ),
		et_core_esc_previously( $save_to_lib_menu ),
		esc_html__( 'Undo', 'et_builder' ),
		esc_html__( 'Redo', 'et_builder' ), // #10
		et_core_esc_previously( $paste_menu_item ),
		et_core_esc_previously( $paste_app_menu_item ),
		et_core_esc_previously( et_pb_allowed_modules_list() ),
		esc_html__( 'Preview', 'et_builder' ),
		et_core_esc_previously( $start_ab_testing_menu ), // #15
		et_core_esc_previously( $end_ab_testing_menu ),
		et_core_esc_previously( $disable_global_menu )
	);

	// "Rename Module Admin Label" Modal Window Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-rename_admin_label">
			<div class="et_pb_prompt_modal">
				<a href="#" class="et_pb_prompt_dont_proceed et-pb-modal-close">
					<span>%1$s</span>
				</a>
				<div class="et_pb_prompt_buttons">
					<br/>
					<input type="submit" class="et_pb_prompt_proceed" value="%2$s" />
				</div>
			</div>
		</script>',
		et_builder_i18n( 'Cancel' ),
		esc_attr__( 'Save', 'et_builder' )
	);

	// "Rename Module Admin Label" Modal Content Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-rename_admin_label-text">
			<h3>%1$s</h3>
			<p>%2$s</p>

			<input type="text" value="" id="et_pb_new_admin_label" class="regular-text" />
		</script>',
		esc_html__( 'Rename', 'et_builder' ),
		esc_html__( 'Enter a new name for this module', 'et_builder' )
	);

	// Builder's Main Buttons.
	$save_to_lib_button = et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'save_library' ) ? sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-save" title="%1$s">
			<span>%2$s</span>
		</a>',
		esc_attr__( 'Save to Library', 'et_builder' ),
		esc_html__( 'Save to Library', 'et_builder' )
	) : '';

	$load_from_lib_button = et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'load_layout' ) && et_pb_is_allowed( 'add_library' ) && et_pb_is_allowed( 'add_module' ) ? sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-load" title="%1$s">
			<span>%2$s</span>
		</a>',
		esc_attr__( 'Load From Library', 'et_builder' ),
		esc_html__( 'Load Layout', 'et_builder' )
	) : '';

	$clear_layout_button = et_pb_is_allowed( 'add_module' ) ? sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-clear" title="%1$s">
			<span>%2$s</span>
		</a>',
		esc_attr__( 'Clear Layout', 'et_builder' ),
		esc_html__( 'Clear Layout', 'et_builder' )
	) : '';

	// Builder's History Buttons.
	$history_button = sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-history" title="%1$s">
			<span class="icon"></span><span class="label">%2$s</span>
		</a>',
		esc_attr__( 'See History', 'et_builder' ),
		esc_html__( 'See History', 'et_builder' )
	);

	$redo_button = sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-redo" title="%1$s">
			<span class="icon"></span><span class="label">%2$s</span>
		</a>',
		esc_attr__( 'Redo', 'et_builder' ),
		esc_html__( 'Redo', 'et_builder' )
	);

	$undo_button = sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-undo" title="%1$s">
			<span class="icon"></span><span class="label">%2$s</span>
		</a>',
		esc_attr__( 'Undo', 'et_builder' ),
		esc_html__( 'Undo', 'et_builder' )
	);

	// App View Stats Button.
	$view_ab_stats_button = sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-view-ab-stats" title="%1$s">
			<span class="icon"></span><span class="label">%2$s</span>
		</a>',
		esc_attr__( 'View Stats', 'et_builder' ),
		esc_html__( 'View Stats', 'et_builder' )
	);

	// App Settings Button.
	$settings_button = sprintf(
		'<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-settings" title="%1$s">
			<span class="icon"></span><span class="label">%2$s</span>
		</a>',
		esc_attr__( 'Settings', 'et_builder' ),
		esc_html__( 'Settings', 'et_builder' )
	);

	// App Template.
	printf(
		'<script type="text/template" id="et-builder-app-template">
			<div id="et_pb_layout_controls">
				%1$s
				%2$s
				%3$s
				%4$s
				%5$s
				%6$s
				%7$s
				%8$s
			</div>
			<div id="et-pb-histories-visualizer-overlay"></div>
			<ol id="et-pb-histories-visualizer"></ol>
		</script>',
		et_core_esc_previously( $save_to_lib_button ),
		et_core_esc_previously( $load_from_lib_button ),
		et_core_esc_previously( $clear_layout_button ),
		et_core_esc_previously( $history_button ),
		et_core_esc_previously( $redo_button ),
		et_core_esc_previously( $undo_button ),
		et_core_esc_previously( $view_ab_stats_button ),
		et_core_esc_previously( $settings_button )
	);

	// App Settings Buttons Template.
	$builder_button_ab_testing_conditional = '( typeof et_pb_ab_goal === "undefined" || et_pb_ab_goal === "off" || typeof et_pb_ab_subject !== "undefined" )';

	$is_ab_active = isset( $post->ID ) && 'on' === get_post_meta( $post->ID, '_et_pb_use_ab_testing', true );

	$view_stats_active_class = $is_ab_active ? 'active' : '';

	$view_stats_button = et_pb_is_allowed( 'ab_testing' ) ? sprintf(
		'<a href="#" class="et-pb-app-view-ab-stats-button %1$s" title="%2$s">
			<span class="icon">
				<object type="image/svg+xml" data="%3$s/images/stats.svg"></object>
			</span>
			<span class="label">%2$s</span>
		</a>',
		esc_attr( $view_stats_active_class ),
		esc_attr__( 'View Split Testing Stats', 'et_builder' ),
		esc_url( ET_BUILDER_URI )
	) : '';

	$portability_class = 'et-pb-app-portability-button';

	if ( $is_ab_active ) {
		$portability_class .= ' et-core-disabled';
	}

	$page_settings_button = et_pb_is_allowed( 'page_options' ) ? sprintf(
		'<a href="#" class="et-pb-app-settings-button" title="%1$s">
			<span class="icon">
				<object type="image/svg+xml" data="%3$s/images/menu.svg"></object>
			</span>
			<span class="label">%2$s</span>
		</a>',
		esc_attr__( 'Settings', 'et_builder' ),
		esc_html__( 'Settings', 'et_builder' ),
		esc_url( ET_BUILDER_URI )
	) : '';

	printf(
		'<script type="text/template" id="et-builder-app-settings-button-template">
			%1$s
			%2$s
			%3$s
		</script>',
		et_core_esc_previously( $page_settings_button ),
		et_core_esc_previously( et_builder_portability_link( 'et_builder', array( 'class' => $portability_class ) ) ),
		et_core_esc_previously( $view_stats_button )
	);

	// do not display settings on global sections if not allowed for current user.
	$global_settings_logic = ! et_pb_is_allowed( 'edit_global_library' ) ? ' && typeof et_pb_global_module === "undefined"' : '';

	$section_settings_button = sprintf(
		'<%% if ( ( typeof et_pb_template_type === \'undefined\' || \'section\' === et_pb_template_type || \'\' === et_pb_template_type )%3$s ) { %%>
			<a href="#" class="et-pb-settings et-pb-settings-section" title="%1$s"><span>%2$s</span></a>
		<%% } %%>',
		esc_attr__( 'Settings', 'et_builder' ),
		esc_html__( 'Settings', 'et_builder' ),
		et_core_esc_previously( $global_settings_logic )
	);

	$section_clone_button  = sprintf(
		'%3$s
			<a href="#" class="et-pb-clone et-pb-clone-section" title="%1$s"><span>%2$s</span></a>
		%4$s',
		esc_attr__( 'Clone Section', 'et_builder' ),
		esc_html__( 'Clone Section', 'et_builder' ),
		'<% if ( ' . et_core_esc_previously( $builder_button_ab_testing_conditional ) . ' ) { %>',
		'<% } %>'
	);
	$section_remove_button = sprintf(
		'%3$s
			<a href="#" class="et-pb-remove et-pb-remove-section" title="%1$s"><span>%2$s</span></a>
		%4$s',
		esc_attr__( 'Delete Section', 'et_builder' ),
		esc_html__( 'Delete Section', 'et_builder' ),
		'<% if ( ' . et_core_esc_previously( $builder_button_ab_testing_conditional ) . ' ) { %>',
		'<% } %>'
	);
	$section_unlock_button = sprintf(
		'<a href="#" class="et-pb-unlock" title="%1$s"><span>%2$s</span></a>',
		esc_attr__( 'Unlock Section', 'et_builder' ),
		esc_html__( 'Unlock Section', 'et_builder' )
	);
	// Section Template.
	$settings_controls = sprintf(
		'<div class="et-pb-controls">
			%1$s

			<%% if ( typeof et_pb_template_type === \'undefined\' || ( \'section\' !== et_pb_template_type && \'row\' !== et_pb_template_type && \'module\' !== et_pb_template_type ) ) { %%>
				%2$s
				%3$s
			<%% } %%>

			<a href="#" class="et-pb-expand" title="%4$s"><span>%5$s</span></a>
			%6$s
		</div>',
		et_pb_is_allowed( 'edit_module' ) && ( et_pb_is_allowed( 'general_settings' ) || et_pb_is_allowed( 'advanced_settings' ) || et_pb_is_allowed( 'custom_css_settings' ) ) ? et_core_esc_previously( $section_settings_button ) : '',
		et_pb_is_allowed( 'add_module' ) ? et_core_esc_previously( $section_clone_button ) : '',
		et_pb_is_allowed( 'add_module' ) ? et_core_esc_previously( $section_remove_button ) : '',
		esc_attr__( 'Expand Section', 'et_builder' ),
		esc_html__( 'Expand Section', 'et_builder' ),
		et_pb_is_allowed( 'lock_module' ) ? et_core_esc_previously( $section_unlock_button ) : ''
	);
	$settings_controls = apply_filters( 'et_builder_section_settings_controls', $settings_controls );

	$add_from_lib_section = et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'add_library' ) ? sprintf(
		'<span class="et-pb-section-add-saved">%1$s</span>',
		esc_html__( 'Add From Library', 'et_builder' )
	) : '';

	$add_standard_section_button = sprintf(
		'<span class="et-pb-section-add-main">%1$s</span>',
		esc_html__( 'Standard Section', 'et_builder' )
	);
	$add_standard_section_button = apply_filters( 'et_builder_add_main_section_button', $add_standard_section_button );

	$add_fullwidth_section_button = sprintf(
		'<span class="et-pb-section-add-fullwidth">%1$s</span>',
		esc_html__( 'Fullwidth Section', 'et_builder' )
	);
	$add_fullwidth_section_button = apply_filters( 'et_builder_add_fullwidth_section_button', $add_fullwidth_section_button );

	$add_specialty_section_button = sprintf(
		'<span class="et-pb-section-add-specialty">%1$s</span>',
		esc_html__( 'Specialty Section', 'et_builder' )
	);
	$add_specialty_section_button = apply_filters( 'et_builder_add_specialty_section_button', $add_specialty_section_button );

	$settings_add_controls = sprintf(
		'<%% if ( typeof et_pb_template_type === \'undefined\' || ( \'section\' !== et_pb_template_type && \'row\' !== et_pb_template_type && \'module\' !== et_pb_template_type ) ) { %%>
			<a href="#" class="et-pb-section-add">
				%1$s
				%2$s
				%3$s
				%4$s
			</a>
		<%% } %%>',
		et_core_esc_previously( $add_standard_section_button ),
		et_core_esc_previously( $add_fullwidth_section_button ),
		et_core_esc_previously( $add_specialty_section_button ),
		et_core_esc_previously( $add_from_lib_section )
	);
	$settings_add_controls = et_pb_is_allowed( 'add_module' ) ? apply_filters( 'et_builder_section_add_controls', $settings_add_controls ) : '';

	$insert_first_row_button = et_pb_is_allowed( 'add_module' ) ? sprintf(
		'<a href="#" class="et-pb-insert-row">
			<span>%1$s</span>
		</a>',
		esc_html__( 'Insert Row(s)', 'et_builder' )
	) : '';

	$disable_sort_logic = ! et_pb_is_allowed( 'move_module' ) ? ' et-pb-disable-sort' : '';

	$disable_global_sort_logic = ! et_pb_is_allowed( 'edit_global_library' )
		? '<%= typeof et_pb_global_module !== \'undefined\' ? \' et-pb-disable-sort\' : \'\' %>'
		: '';

	printf(
		'<script type="text/template" id="et-builder-section-template">
			<div class="et-pb-right-click-trigger-overlay"></div>
			%1$s
			<div class="et-pb-section-content et-pb-data-cid%3$s%4$s<%%= typeof et_pb_template_type !== \'undefined\' && \'module\' === et_pb_template_type ? \' et_pb_hide_insert\' : \'\' %%>" data-cid="<%%= cid %%>" data-skip="<%%= typeof( et_pb_skip_module ) === \'undefined\' ? \'false\' : \'true\' %%>">
				%5$s
			</div>
			%2$s
			<div class="et-pb-locked-overlay et-pb-locked-overlay-section"></div>
			<span class="et-pb-section-title"><%%= admin_label.replace( /%%22/g, "&quot;" ).replace( /%%91/g, "&#91;" ).replace( /%%93/g, "&#93;" ) %%></span>
		</script>',
		et_core_esc_previously( $settings_controls ),
		et_core_esc_previously( $settings_add_controls ),
		et_core_intentionally_unescaped( $disable_sort_logic, 'fixed_string' ),
		et_core_intentionally_unescaped( $disable_global_sort_logic, 'fixed_string' ),
		et_core_esc_previously( $insert_first_row_button )
	);

	$row_settings_button         = sprintf(
		'<%% if ( ( typeof et_pb_template_type === \'undefined\' || et_pb_template_type !== \'module\' )%3$s ) { %%>
			<a href="#" class="et-pb-settings et-pb-settings-row" title="%1$s"><span>%2$s</span></a>
		<%% } %%>',
		esc_attr__( 'Settings', 'et_builder' ),
		esc_html__( 'Settings', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? ' && ( typeof et_pb_global_module === "undefined" || "" === et_pb_global_module ) && ( typeof et_pb_global_parent === "undefined" || "" === et_pb_global_parent )' : '' // do not display settings button on global rows if not allowed for current user.
	);
	$row_clone_button            = sprintf(
		'%3$s
			<a href="#" class="et-pb-clone et-pb-clone-row" title="%1$s"><span>%2$s</span></a>
		%4$s',
		esc_attr__( 'Clone Row', 'et_builder' ),
		esc_html__( 'Clone Row', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? '<% if ( ( typeof et_pb_global_parent === "undefined" || "" === et_pb_global_parent ) && ' . $builder_button_ab_testing_conditional . ' ) { %>' : '<% if ( ' . $builder_button_ab_testing_conditional . ' ) { %>', // do not display clone button on rows within global sections if not allowed for current user.
		'<% } %>'
	);
	$row_remove_button           = sprintf(
		'%3$s
			<a href="#" class="et-pb-remove et-pb-remove-row" title="%1$s"><span>%2$s</span></a>
		%4$s',
		esc_attr__( 'Delete Row', 'et_builder' ),
		esc_html__( 'Delete Row', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? '<% if ( ( typeof et_pb_global_parent === "undefined" || "" === et_pb_global_parent  ) && ' . $builder_button_ab_testing_conditional . ') { %>' : '<% if ( ' . $builder_button_ab_testing_conditional . ' ) { %>', // do not display clone button on rows within global sections if not allowed for current user.
		'<% } %>'
	);
	$row_change_structure_button = sprintf(
		'%3$s
			<a href="#" class="et-pb-change-structure" title="%1$s"><span>%2$s</span></a>
		%4$s',
		esc_attr__( 'Change Structure', 'et_builder' ),
		esc_html__( 'Change Structure', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? '<% if ( ( typeof et_pb_global_module === "undefined" || "" === et_pb_global_module ) && ( typeof et_pb_global_parent === "undefined" || "" === et_pb_global_parent ) ) { %>' : '', // do not display change structure button on global rows if not allowed for current user.
		! et_pb_is_allowed( 'edit_global_library' ) ? '<% } %>' : ''
	);
	$row_unlock_button           = sprintf(
		'<a href="#" class="et-pb-unlock" title="%1$s"><span>%2$s</span></a>',
		esc_attr__( 'Unlock Row', 'et_builder' ),
		esc_html__( 'Unlock Row', 'et_builder' )
	);
	// Row Template.
	$settings = sprintf(
		'<div class="et-pb-controls">
			%1$s
		<%% if ( typeof et_pb_template_type === \'undefined\' || \'section\' === et_pb_template_type ) { %%>
			%2$s
		<%% }

		if ( typeof et_pb_template_type === \'undefined\' || et_pb_template_type !== \'module\' ) { %%>
			%4$s
		<%% }

		if ( typeof et_pb_template_type === \'undefined\' || \'section\' === et_pb_template_type ) { %%>
			%3$s
		<%% } %%>

		<a href="#" class="et-pb-expand" title="%5$s"><span>%6$s</span></a>
		%7$s
		</div>',
		et_pb_is_allowed( 'edit_module' ) && ( et_pb_is_allowed( 'general_settings' ) || et_pb_is_allowed( 'advanced_settings' ) || et_pb_is_allowed( 'custom_css_settings' ) ) ? $row_settings_button : '',
		et_pb_is_allowed( 'add_module' ) ? $row_clone_button : '',
		et_pb_is_allowed( 'add_module' ) ? $row_remove_button : '',
		et_pb_is_allowed( 'edit_module' ) && ( et_pb_is_allowed( 'general_settings' ) || et_pb_is_allowed( 'advanced_settings' ) || et_pb_is_allowed( 'custom_css_settings' ) ) ? $row_change_structure_button : '',
		esc_attr__( 'Expand Row', 'et_builder' ),
		esc_html__( 'Expand Row', 'et_builder' ),
		et_pb_is_allowed( 'lock_module' ) ? $row_unlock_button : ''
	);
	$settings = apply_filters( 'et_builder_row_settings_controls', $settings );

	$row_class = sprintf(
		'class="et-pb-row-content et-pb-data-cid%1$s%2$s <%%= typeof et_pb_template_type !== \'undefined\' && \'module\' === et_pb_template_type ? \' et_pb_hide_insert\' : \'\' %%>"',
		! et_pb_is_allowed( 'move_module' ) ? ' et-pb-disable-sort' : '',
		! et_pb_is_allowed( 'edit_global_library' )
			? sprintf( '<%%= typeof et_pb_global_parent !== \'undefined\' || typeof et_pb_global_module !== \'undefined\' ? \' et-pb-disable-sort\' : \'\' %%>' )
			: ''
	);

	$data_skip = 'data-skip="<%= typeof( et_pb_skip_module ) === \'undefined\' ? \'false\' : \'true\' %>"';

	$add_row_button = et_pb_is_allowed( 'add_module' ) ? sprintf(
		'<%% if ( ( typeof et_pb_template_type === \'undefined\' || \'section\' === et_pb_template_type )%2$s ) { %%>
			<a href="#" class="et-pb-row-add">
				<span>%1$s</span>
			</a>
		<%% } %%>',
		esc_html__( 'Add Row', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? ' && typeof et_pb_global_parent === "undefined"' : '' // do not display add row buton on global sections if not allowed for current user.
	) : '';

	$insert_column_button = et_pb_is_allowed( 'add_module' ) ? sprintf(
		'<a href="#" class="et-pb-insert-column">
			<span>%1$s</span>
		</a>',
		esc_html__( 'Insert Column(s)', 'et_builder' )
	) : '';

	printf(
		'<script type="text/template" id="et-builder-row-template">
			<div class="et-pb-right-click-trigger-overlay"></div>
			%1$s
			<div data-cid="<%%= cid %%>" %2$s %3$s>
				<div class="et-pb-row-container"></div>
				%4$s
			</div>
			%5$s
			<div class="et-pb-locked-overlay et-pb-locked-overlay-row"></div>
			<span class="et-pb-row-title"><%%= admin_label.replace( /%%22/g, "&quot;" ).replace( /%%91/g, "&#91;" ).replace( /%%93/g, "&#93;" ) %%></span>
		</script>',
		et_core_esc_previously( $settings ),
		et_core_intentionally_unescaped( $row_class, 'fixed_string' ),
		et_core_intentionally_unescaped( $data_skip, 'fixed_string' ),
		et_core_esc_previously( $insert_column_button ),
		et_core_esc_previously( $add_row_button )
	);

	// Module Block Template.
	$clone_button    = et_pb_is_allowed( 'add_module' ) ? sprintf(
		'<%% if ( ( typeof et_pb_template_type === \'undefined\' || et_pb_template_type !== \'module\' )%3$s && _.contains(%4$s, module_type) && ' . $builder_button_ab_testing_conditional . ' ) { %%>
			<a href="#" class="et-pb-clone et-pb-clone-module" title="%1$s">
				<span>%2$s</span>
			</a>
		<%% } %%>',
		esc_attr__( 'Clone Module', 'et_builder' ),
		esc_html__( 'Clone Module', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? ' &&  ( typeof et_pb_global_parent === "undefined" || "" === et_pb_global_parent )' : '',
		et_pb_allowed_modules_list()
	) : '';
	$remove_button   = et_pb_is_allowed( 'add_module' ) ? sprintf(
		'<%% if ( ( typeof et_pb_template_type === \'undefined\' || et_pb_template_type !== \'module\' )%3$s && (_.contains(%4$s, module_type) || "removed" === component_status) && ' . $builder_button_ab_testing_conditional . ' ) { %%>
			<a href="#" class="et-pb-remove et-pb-remove-module" title="%1$s">
				<span>%2$s</span>
			</a>
		<%% } %%>',
		esc_attr__( 'Remove Module', 'et_builder' ),
		esc_html__( 'Remove Module', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? ' &&  ( typeof et_pb_global_parent === "undefined" || "" === et_pb_global_parent )' : '',
		et_pb_allowed_modules_list()
	) : '';
	$unlock_button   = et_pb_is_allowed( 'lock_module' ) ? sprintf(
		'<%% if ( typeof et_pb_template_type === \'undefined\' || et_pb_template_type !== \'module\' ) { %%>
			<a href="#" class="et-pb-unlock" title="%1$s">
				<span>%2$s</span>
			</a>
		<%% } %%>',
		esc_html__( 'Unlock Module', 'et_builder' ),
		esc_attr__( 'Unlock Module', 'et_builder' )
	) : '';
	$settings_button = et_pb_is_allowed( 'edit_module' ) && ( et_pb_is_allowed( 'general_settings' ) || et_pb_is_allowed( 'advanced_settings' ) || et_pb_is_allowed( 'custom_css_settings' ) ) ? sprintf(
		'<%% if (%3$s _.contains( %4$s, module_type ) ) { %%>
			<a href="#" class="et-pb-settings" title="%1$s">
				<span>%2$s</span>
			</a>
		<%% } %%>',
		esc_attr__( 'Module Settings', 'et_builder' ),
		esc_html__( 'Module Settings', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? ' ( typeof et_pb_global_parent === "undefined" || "" === et_pb_global_parent ) && ( typeof et_pb_global_module === "undefined" || "" === et_pb_global_module ) &&' : '',
		et_pb_allowed_modules_list()
	) : '';

	printf(
		'<script type="text/template" id="et-builder-block-module-template">
			%1$s
			%2$s
			%3$s
			%4$s
			<span class="et-pb-module-title"><%%= admin_label.replace( /%%22/g, "&quot;" ).replace( /%%91/g, "&#91;" ).replace( /%%93/g, "&#93;" ) %%></span>
		</script>',
		et_core_esc_previously( $settings_button ),
		et_core_esc_previously( $clone_button ),
		et_core_esc_previously( $remove_button ),
		et_core_esc_previously( $unlock_button )
	);

	// Modal Template.

	$can_edit_or_has_modal_view_tab = et_pb_is_allowed( 'edit_module' ) && ( et_pb_is_allowed( 'general_settings' ) || et_pb_is_allowed( 'advanced_settings' ) || et_pb_is_allowed( 'custom_css_settings' ) );

	$save_exit_button = $can_edit_or_has_modal_view_tab ? sprintf(
		'<a href="#" class="et-pb-modal-save button button-primary">
			<span>%1$s</span>
		</a>',
		esc_html__( 'Save & Exit', 'et_builder' )
	) : '';

	$save_template_button = et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'save_library' ) ? sprintf(
		'<%% if ( typeof et_pb_template_type === \'undefined\' || \'\' === et_pb_template_type ) { %%>
			<a href="#" class="et-pb-modal-save-template button">
				<span>%1$s</span>
			</a>
		<%% } %%>',
		esc_html__( 'Save & Add To Library', 'et_builder' )
	) : '';

	$preview_template_button = sprintf(
		'<a href="#" class="et-pb-modal-preview-template button">
			<span class="icon"></span>
			<span class="label">%1$s</span>
		</a>',
		esc_html__( 'Preview', 'et_builder' )
	);

	$single_button_class = ! et_pb_is_allowed( 'divi_library' ) || ! et_pb_is_allowed( 'save_library' ) ? ' et_pb_single_button' : '';

	$no_editing_class = $can_edit_or_has_modal_view_tab ? '' : ' et_pb_no_editing';

	printf(
		'<script type="text/template" id="et-builder-modal-template">
			<div class="et-pb-modal-container%6$s">

				<a href="#" class="et-pb-modal-close">
					<span>%1$s</span>
				</a>

			<%% if ( ! ( typeof open_view !== \'undefined\' && open_view === \'column_specialty_settings\' ) && typeof type !== \'undefined\' && ( type === \'module\' || type === \'section\' || type === \'row_inner\' || ( type === \'row\' && typeof open_view === \'undefined\' ) ) ) { %%>
				<div class="et-pb-modal-bottom-container%4$s">
					%2$s
					%5$s
					%3$s
				</div>
			<%% } %%>

			</div>
		</script>',
		et_builder_i18n( 'Cancel' ),
		et_core_esc_previously( $save_template_button ),
		et_core_esc_previously( $save_exit_button ),
		et_core_intentionally_unescaped( $single_button_class, 'fixed_string' ),
		et_core_esc_previously( $preview_template_button ),
		et_core_intentionally_unescaped( $no_editing_class, 'fixed_string' )
	);

	// Column Settings Template.
	$columns_number =
		'<% if ( view.model.attributes.specialty_columns === 3 ) { %>
			3
		<% } else { %>
			2
		<% } %>';

	$data_specialty_columns = sprintf(
		'<%% if ( typeof view !== \'undefined\' && typeof view.model.attributes.specialty_columns !== \'undefined\' ) { %%>
			data-specialty_columns="%1$s"
		<%% } %%>',
		$columns_number
	);

	$saved_row_tab = et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'add_library' ) ? sprintf(
		'<li class="et-pb-saved-module" data-open_tab="et-pb-saved-modules-tab">
			<a href="#">%1$s</a>
		</li>',
		esc_html__( 'Add From Library', 'et_builder' )
	) : '';

	$saved_row_container = et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'add_library' )
		? '<% if ( ( typeof change_structure === \'undefined\' || \'true\' !== change_structure ) && ( typeof et_pb_specialty === \'undefined\' || et_pb_specialty !== \'on\' ) ) { %>
			<div class="et-pb-main-settings et-pb-main-settings-full et-pb-saved-modules-tab"></div>
		<% } %>'
		: '';

	printf(
		'<script type="text/template" id="et-builder-column-settings-template">

			<h3 class="et-pb-settings-heading" data-current_row="<%%= cid %%>">%1$s</h3>

		<%% if ( ( typeof change_structure === \'undefined\' || \'true\' !== change_structure ) && ( typeof et_pb_specialty === \'undefined\' || et_pb_specialty !== \'on\' ) ) { %%>
			<ul class="et-pb-options-tabs-links et-pb-saved-modules-switcher" %2$s>
				<li class="et-pb-saved-module et-pb-options-tabs-links-active" data-open_tab="et-pb-new-modules-tab" data-content_loaded="true">
					<a href="#">%3$s</a>
				</li>
				%4$s
			</ul>
		<%% } %%>

			<div class="et-pb-main-settings et-pb-main-settings-full et-pb-new-modules-tab active-container">
				<ul class="et-pb-column-layouts">
					%5$s
				</ul>
			</div>

			%6$s

		</script>',
		esc_html__( 'Insert Columns', 'et_builder' ),
		et_core_intentionally_unescaped( $data_specialty_columns, 'fixed_string' ),
		esc_html__( 'New Row', 'et_builder' ),
		et_core_esc_previously( $saved_row_tab ),
		et_core_intentionally_unescaped( et_builder_get_columns_layout(), 'fixed_string' ),
		et_core_intentionally_unescaped( $saved_row_container, 'fixed_string' )
	);

	// "Add Module" Template
	$fullwidth_class =
		'<% if ( typeof module.fullwidth_only !== \'undefined\' && module.fullwidth_only === \'on\' ) { %> et_pb_fullwidth_only_module<% } %>';

	$saved_modules_tab = et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'add_library' ) ? sprintf(
		'<li class="et-pb-saved-module" data-open_tab="et-pb-saved-modules-tab">
			<a href="#">%1$s</a>
		</li>',
		esc_html__( 'Add From Library', 'et_builder' )
	) : '';

	$saved_modules_container = et_pb_is_allowed( 'divi_library' ) && et_pb_is_allowed( 'add_library' )
	? '<div class="et-pb-main-settings et-pb-main-settings-full et-pb-saved-modules-tab"></div>'
	: '';

	printf(
		'<script type="text/template" id="et-builder-modules-template">
			<h3 class="et-pb-settings-heading">%1$s</h3>

			<ul class="et-pb-options-tabs-links et-pb-saved-modules-switcher">
				<li class="et-pb-new-module et-pb-options-tabs-links-active" data-open_tab="et-pb-all-modules-tab">
					<a href="#">%2$s</a>
				</li>

				%3$s
			</ul>

			<div class="et-pb-main-settings et-pb-main-settings-full et-pb-all-modules-tab active-container">
				<ul class="et-pb-all-modules">
				<%% _.each(modules, function(module) { %%>
					<%% if ( "et_pb_row" !== module.label && "et_pb_section" !== module.label && "et_pb_column" !== module.label && "et_pb_row_inner" !== module.label && _.contains(%6$s, module.label ) ) { %%>
						<li class="<%%= module.label %%>%4$s">
							<span class="et_module_title"><%%= module.title %%></span>
						</li>
					<%% } %%>
				<%% }); %%>
				</ul>
			</div>

			%5$s
		</script>',
		esc_html__( 'Insert Module', 'et_builder' ),
		esc_html__( 'New Module', 'et_builder' ),
		et_core_esc_previously( $saved_modules_tab ),
		et_core_intentionally_unescaped( $fullwidth_class, 'fixed_string' ),
		et_core_intentionally_unescaped( $saved_modules_container, 'fixed_string' ),
		et_core_esc_previously( et_pb_allowed_modules_list() )
	);

	// Load Layout Template.
	printf(
		'<script type="text/template" id="et-builder-load_layout-template">
			<h3 class="et-pb-settings-heading">%1$s</h3>

		<%% if ( typeof display_switcher !== \'undefined\' && display_switcher === \'on\' ) { %%>
			<ul class="et-pb-options-tabs-links et-pb-saved-modules-switcher">
				<li class="et-pb-new-module et-pb-options-tabs-links-active" data-open_tab="et-pb-all-modules-tab">
					<a href="#">%2$s</a>
				</li>
				<li class="et-pb-saved-module" data-open_tab="et-pb-saved-modules-tab">
					<a href="#">%3$s</a>
				</li>
				<%% if (!_.isEmpty(et_pb_options.library_custom_tabs)) { %%>
					<%% _.each(et_pb_options.library_custom_tabs, function(tab_name, tab_id) { %%>
						<li class="et-pb-saved-module" data-open_tab="et-pb-<%%= tab_id %%>-tab" data-custom_tab_id="<%%= tab_id %%>">
							<a href="#"><%%= tab_name %%></a>
						</li>
					<%% }) %%>
				<%% } %%>
			</ul>
		<%% } %%>

		<%% if ( typeof display_switcher !== \'undefined\' && display_switcher === \'on\' ) { %%>
			<div class="et-pb-main-settings et-pb-main-settings-full et-pb-all-modules-tab active-container"><div id="et-cloud-app" class="et-fb-library-container"></div></div>
			<div class="et-pb-main-settings et-pb-main-settings-full et-pb-saved-modules-tab" style="display: none;"></div>
			<%% if (!_.isEmpty(et_pb_options.library_custom_tabs)) { %%>
				<%% _.each(et_pb_options.library_custom_tabs, function(tab_name, tab_id) { %%>
					<div class="et-pb-main-settings et-pb-main-settings-full et-pb-<%%= tab_id %%>-tab" style="display: none;"></div>
				<%% }) %%>
			<%% } %%>
		<%% } else { %%>
			<div class="et-pb-main-settings et-pb-main-settings-full et-pb-saved-modules-tab active-container"></div>
		<%% } %%>
		</script>',
		esc_html__( 'Load Layout', 'et_builder' ),
		esc_html__( 'Premade Layouts', 'et_builder' ),
		esc_html__( 'Your Saved Layouts', 'et_builder' )
	);

	// Library Account Status Error.
	$root_directory = defined( 'ET_BUILDER_PLUGIN_ACTIVE' ) ? ET_BUILDER_PLUGIN_DIR : get_template_directory();
	$library_i18n   = require $root_directory . '/cloud/i18n/library.php';

	printf(
		'
		<script type="text/template" id="et-builder-library-account-status-error-template">
			<div class="et-pb-library-account-status-error">
				<%% if ( expired ) { %%>
					<h2>%1$s</h2>
					<p>%2$s</p>
				<%% } else { %%>
					<h2>%3$s</h2>
					<p>%4$s</p>
					<div class="et-pb-option et-pb-option--text">
						<label for="et_username">%5$s</label>
						<div class="et-pb-option-container et-pb-option-container--text">
							<input id="et_username" type="text" class="regular-text" value="" />
							<p class="description">%6$s</p>
						</div>
					</div>
					<div class="et-pb-option et-pb-option--text">
						<label for="et_api_key">%7$s</label>
						<div class="et-pb-option-container et-pb-option-container--text">
							<input id="et_api_key" type="text" class="regular-text" value="" />
							<p class="description">%8$s</p>
						</div>
					</div>
					<div class="et-pb-option-container">
						<a href="#" class="button">%9$s</a>
					</div>
				<%% } %%>
			</div>
		</script>',
		et_core_esc_previously( $library_i18n['Uh Oh!'] ),
		et_core_esc_previously( $library_i18n['$expiredAccount'] ),
		et_core_esc_previously( $library_i18n['Authentication Required'] ),
		et_core_esc_previously( $library_i18n['$noAccount'] ),
		et_core_esc_previously( $library_i18n['Username'] ),
		et_core_esc_previously( $library_i18n['$usernameHelp'] ),
		et_core_esc_previously( $library_i18n['API Key'] ),
		et_core_esc_previously( $library_i18n['$apiKeyHelp'] ),
		et_core_esc_previously( $library_i18n['Submit'] )
	);

	// Library Back Button.
	echo '
		<script type="text/template" id="et-builder-library-back-button-template">
			<div class="et-pb-library-back-button" aria-role="button" aria-label="Back To Layouts List">
				<svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet"shapeRendering="geometricPrecision">
					<g>
						<path d="M14.988 10.963h-3v-2.52a.393.393 0 0 0-.63-.361l-5.2 4.5a.491.491 0 0 0 0 .72l5.2 4.5a.393.393 0 0 0 .63-.36v-2.52h2.99a2.992 2.992 0 0 1 2.99 2.972v1.287a.7.7 0 0 0 .7.694h2.59a.7.7 0 0 0 .7-.694v-1.3a6.948 6.948 0 0 0-6.97-6.918z" fillRule="evenodd" />
					</g>
				</svg>
			</div>
		</script>
	';

	$insert_module_button = et_pb_is_allowed( 'add_module' ) ? sprintf(
		'%2$s
		<a href="#" class="et-pb-insert-module<%%= typeof et_pb_template_type === \'undefined\' || \'module\' !== et_pb_template_type ? \'\' : \' et_pb_hidden_button\' %%>">
			<span>%1$s</span>
		</a>
		%3$s',
		esc_html__( 'Insert Module(s)', 'et_builder' ),
		! et_pb_is_allowed( 'edit_global_library' ) ? '<% if ( typeof et_pb_global_parent === "undefined" ) { %>' : '',
		! et_pb_is_allowed( 'edit_global_library' ) ? '<% } %>' : ''
	) : '';

	// Column Template.
	printf(
		'<script type="text/template" id="et-builder-column-template">
			%1$s
		</script>',
		et_core_esc_previously( $insert_module_button )
	);

	// Insert Row(s).
	$insert_row_button = et_pb_is_allowed( 'add_module' ) ? sprintf(
		'<a href="#" class="et-pb-insert-row">
			<span>%1$s</span>
		</a>',
		esc_html__( 'Insert Row(s)', 'et_builder' )
	) : '';

	// Insert Row Template.
	printf(
		'<script type="text/template" id="et-builder-specialty-column-template">
			%1$s
		</script>',
		et_core_esc_previously( $insert_row_button )
	);

	// Advanced Settings Buttons Module.
	printf(
		'<script type="text/template" id="et-builder-advanced-setting">
			<%% if ( \'et_pb_column\' !== module_type && \'et_pb_column_inner\' !== module_type ) { %%>
				<a href="#" class="et-pb-advanced-setting-remove">
					<span>%1$s</span>
				</a>
			<%% } %%>

			<a href="#" class="et-pb-advanced-setting-options">
				<span>%2$s</span>
			</a>

			<%% if ( \'et_pb_column\' !== module_type && \'et_pb_column_inner\' !== module_type ) { %%>
				<a href="#" class="et-pb-clone et-pb-advanced-setting-clone">
					<span>%3$s</span>
				</a>
			<%% } %%>
		</script>',
		esc_html__( 'Delete', 'et_builder' ),
		esc_html__( 'Settings', 'et_builder' ),
		esc_html__( 'Clone Module', 'et_builder' )
	);

	// Advanced Settings Modal Buttons Template.
	printf(
		'<script type="text/template" id="et-builder-advanced-setting-edit">
			<div class="et-pb-modal-container">
				<a href="#" class="et-pb-modal-close">
					<span>%1$s</span>
				</a>

				<div class="et-pb-modal-bottom-container">
					<a href="#" class="et-pb-modal-save">
						<span>%2$s</span>
					</a>
				</div>
			</div>
		</script>',
		et_builder_i18n( 'Cancel' ),
		esc_html__( 'Save', 'et_builder' )
	);

	// "Deactivate Builder" Modal Message Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-deactivate_builder-text">
			<h3>%1$s</h3>
			<p>%2$s</p>
			<p>%3$s</p>
		</script>',
		esc_html__( 'Disable Builder', 'et_builder' ),
		esc_html__( 'All content created in the Divi Builder will be lost. Previous content will be restored.', 'et_builder' ),
		esc_html__( 'Do you wish to proceed?', 'et_builder' )
	);

	// "Clear Layout" Modal Window Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-clear_layout-text">
			<h3>%1$s</h3>
			<p>%2$s</p>
			<p>%3$s</p>
		</script>',
		esc_html__( 'Clear Layout', 'et_builder' ),
		esc_html__( 'All of your current page content will be lost.', 'et_builder' ),
		esc_html__( 'Do you wish to proceed?', 'et_builder' )
	);

	// "Reset Advanced Settings" Modal Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-reset_advanced_settings-text">
			<p>%1$s</p>
			<p>%2$s</p>
		</script>',
		esc_html__( 'All advanced module settings in will be lost.', 'et_builder' ),
		esc_html__( 'Do you wish to proceed?', 'et_builder' )
	);

	// "Save Layout" Modal Window Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-save_layout">
			<div class="et_pb_prompt_modal">
				<a href="#" class="et_pb_prompt_dont_proceed et-pb-modal-close">
					<span>%1$s</span>
				</a>
				<div class="et_pb_prompt_buttons">
					<br/>
					<input type="submit" class="et_pb_prompt_proceed" value="%2$s" />
				</div>
			</div>
		</script>',
		et_builder_i18n( 'Cancel' ),
		esc_html__( 'Save', 'et_builder' )
	);

	// "Save Layout" Modal Content Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-save_layout-text">
			<h3>%1$s</h3>
			<p>%2$s</p>

			<label>%3$s</label>
			<input type="text" value="" id="et_pb_new_layout_name" class="regular-text" />
		</script>',
		esc_html__( 'Save To Library', 'et_builder' ),
		esc_html__( 'Save your current page to the Divi Library for later use.', 'et_builder' ),
		esc_html__( 'Layout Name', 'et_builder' )
	);

	// "Delete Font" Modal Text
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-delete_font-text">
			<h3>%1$s</h3>
			<p>%2$s</p>
		</script>',
		esc_html__( 'Delete Font', 'et_builder' ),
		sprintf( '%1$s %2$s?', esc_html__( 'Are you sure want to delete', 'et_builder' ), '<%= font_name %>' )
	);

	// "Upload Font" Modal Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-upload_font">
			<div class="et_pb_prompt_modal et-pb-font-uploader">
				<a href="#" class="et_pb_prompt_dont_proceed et-pb-modal-close">
					<span>%1$s</span>
				</a>
				<div class="et_pb_prompt_buttons">
					<br/>
					<input type="submit" class="et_pb_prompt_proceed" value="%2$s" />
				</div>
			</div>
		</script>',
		et_builder_i18n( 'Cancel' ),
		esc_html__( 'Upload', 'et_builder' )
	);

	// "Upload Font" Modal Text
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-upload_font-text">
			<h3>%1$s</h3>
			<div class="et-font-uploader-content">
				<div class="et-font-uploader-error"></div>

				<h4>%2$s</h4>
				<input type="text" value="" id="et-font-uploader-name" class="regular-text" />

				<form class="et-core-portability-import-form">
					<span class="et-core-portability-import-placeholder">%3$s</span>
					<button class="et-core-button">%4$s</button>
					<input type="file" accept=".ttf, .otf, .eot, .woff2, .woff" multiple>
					<div class="et-core-clearfix"></div>
					<p class="et-font-uploader-hint">%7$s: ttf, otf</p>
					<div class="et-font-uploader-selected-fonts et-font-uploader-hidden-field"><h4>%8$s</h4></div>
					<h4>%5$s</h4>
					<p class="et-font-uploader-hint">%9$s</p>
					<label><input type="checkbox" name="et-font-uploader-all-weight" class="et-font-uploader-all-weights" checked>%6$s</label>
					<div class="et-font-uploader-weight-values et-font-uploader-hidden-section">
						%10$s
					</div>
				</form>
			</div>
		</script>',
		esc_html__( 'Upload Font', 'et_builder' ),
		esc_html__( 'Font Name', 'et_builder' ),
		esc_html__( 'Drag Files Here', 'et_builder' ),
		esc_html__( 'Choose Font Files', 'et_builder' ),
		esc_html__( 'Supported Font Weights', 'et_builder' ),
		esc_html__( 'All', 'et_builder' ),
		esc_html__( 'Supported File Formats', 'et_builder' ),
		esc_html__( 'Selected Font Files', 'et_builder' ),
		esc_html__( 'Choose the font weights supported by your font. Select "All" if you don\'t know this information or if your font includes all weights.', 'et_builder' ),
		et_core_esc_previously( et_generate_font_weight_select_output() )
	);

	// "Save Template" Modal Window Layout
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-save_template">
			<div class="et_pb_prompt_modal et_pb_prompt_modal_save_library">
				<div class="et_pb_prompt_buttons">
					<br/>
					<input type="submit" class="et_pb_prompt_proceed" value="%1$s" />
				</div>
			</div>
		</script>',
		esc_attr__( 'Save And Add To Library', 'et_builder' )
	);

	// "Save Template" Content Layout
	$layout_categories = get_terms( 'layout_category', array( 'hide_empty' => false ) );
	$categories_output = sprintf(
		'<div class="et-pb-option"><label>%1$s</label>',
		esc_html__( 'Add To Categories', 'et_builder' )
	);

	if ( is_array( $layout_categories ) && ! empty( $layout_categories ) ) {
		$categories_output .= '<div class="et-pb-option-container layout_cats_container">';
		foreach ( $layout_categories as $category ) {
			$categories_output .= sprintf(
				'<label>%1$s<input type="checkbox" value="%2$s"/></label>',
				esc_html( $category->name ),
				esc_attr( $category->term_id )
			);
		}
		$categories_output .= '</div></div>';
	}

	$categories_output .= sprintf(
		'
		<div class="et-pb-option">
			<label>%1$s:</label>
			<div class="et-pb-option-container">
				<input type="text" value="" id="et_pb_new_cat_name" class="regular-text" />
			</div>
		</div>',
		esc_html__( 'Create New Category', 'et_builder' )
	);

	$general_checkbox  = sprintf(
		'<label>
			%1$s <input type="checkbox" value="general" id="et_pb_template_general" checked />
		</label>',
		esc_html__( 'Include General settings', 'et_builder' )
	);
	$advanced_checkbox = sprintf(
		'<label>
			%1$s <input type="checkbox" value="advanced" id="et_pb_template_advanced" checked />
		</label>',
		esc_html__( 'Include Advanced Design settings', 'et_builder' )
	);
	$css_checkbox      = sprintf(
		'<label>
			%1$s <input type="checkbox" value="css" id="et_pb_template_css" checked />
		</label>',
		esc_html__( 'Include Custom CSS', 'et_builder' )
	);

	printf(
		'<script type="text/template" id="et-builder-prompt-modal-save_template-text">
			<div class="et-pb-main-settings">
				<p>%1$s</p>

				<div class="et-pb-option">
					<label>%2$s:</label>

					<div class="et-pb-option-container">
						<input type="text" value="" id="et_pb_new_template_name" class="regular-text" />
					</div>
				</div>

			<%% if ( \'global\' !== is_global && \'global\' !== is_global_child ) { %%>
				<div class="et-pb-option">
					<label>%3$s</label>

					<div class="et-pb-option-container">
						<label>
							%4$s <input type="checkbox" value="" id="et_pb_template_global" />
						</label>
					</div>
				</div>
			<%% } %%>

				%5$s
			</div>
		</script>',
		esc_html__( 'Here you can save the current item and add it to your Divi Library for later use as well.', 'et_builder' ),
		esc_html__( 'Template Name', 'et_builder' ),
		esc_html__( 'Save as Global:', 'et_builder' ),
		esc_html__( 'Make this a global item', 'et_builder' ),
		et_core_esc_previously( $categories_output )
	);

	// Prompt Modal Window Template.
	printf(
		'<script type="text/template" id="et-builder-prompt-modal">
			<div class="et_pb_prompt_modal">
				<a href="#" class="et_pb_prompt_dont_proceed et-pb-modal-close">
					<span>%1$s<span>
				</a>

				<div class="et_pb_prompt_buttons">
					<a href="#" class="et_pb_prompt_proceed">%2$s</a>
				</div>
			</div>
		</script>',
		et_builder_i18n( 'No' ),
		et_builder_i18n( 'Yes' )
	);

	// "Open Settings" Modal Window Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-open_settings">
			<div class="et_pb_prompt_modal">
				<a href="#" class="et_pb_prompt_dont_proceed et-pb-modal-close">
					<span>%1$s</span>
				</a>
				<div class="et_pb_prompt_buttons">
					<br/>
					<input type="submit" class="et_pb_prompt_proceed" value="%2$s" />
				</div>
			</div>
		</script>',
		et_builder_i18n( 'Cancel' ),
		esc_html__( 'Save', 'et_builder' )
	);

	$utils  = ET_Core_Data_Utils::instance();
	$fields = array();
	// Filter out fields not supposed to show in BB.
	foreach ( ET_Builder_Settings::get_fields() as $key => $field ) {
		if ( true === $utils->array_get( $field, 'show_in_bb', true ) ) {
			$fields[ $key ] = $field;
		}
	}

	// "Open Settings" Modal Content Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-open_settings-text">
			<h3>%1$s</h3>
			<div class="et_pb_prompt_fields">
			%2$s
			</div><!-- .et_pb_prompt_fields -->
		</script>',
		esc_html__( 'Divi Builder Settings', 'et_builder' ),
		et_core_esc_previously( et_pb_get_builder_settings_fields( $fields ) )
	);

	// AB Testing.
	$ab_testing = et_builder_ab_labels();

	// "Turn off AB Testing" Modal Window Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-turn_off_ab_testing">
			<div class="et_pb_prompt_modal">
				<a href="#" class="et_pb_prompt_dont_proceed et-pb-modal-close">
					<span>%1$s</span>
				</a>
				<div class="et_pb_prompt_buttons">
					<br/>
					<input type="submit" class="et_pb_prompt_proceed" value="%2$s" />
				</div>
			</div>
		</script>',
		et_builder_i18n( 'Cancel' ),
		et_builder_i18n( 'Yes' )
	);

	// "Turn off AB Testing" Modal Content Template
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-turn_off_ab_testing-text">
			<h3>%1$s</h3>
			<p>%2$s</p>
			<p>%3$s</p>
		</script>',
		esc_html__( 'End Split Test?', 'et_builder' ),
		esc_html__( 'Upon ending your split test, you will be asked to select which subject variation you would like to keep. Remaining subjects will be removed.', 'et_builder' ),
		esc_html__( 'Note: this process cannot be undone.', 'et_builder' )
	);

	// AB Testing Alert :: Modal Window Template.
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-ab_testing_alert">
			<div class="et_pb_prompt_modal">
				<div class="et_pb_prompt_buttons">
					<br/>
					<input type="submit" class="et_pb_prompt_proceed" value="%1$s" />
				</div>
			</div>
		</script>',
		esc_html__( 'Ok', 'et_builder' )
	);

	// AB Testing Alert :: Modal Content Template.
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-ab_testing_alert-text">
			<%% if ( ! _.isUndefined( et_pb_ab_js_options[id] ) ) { %%>
				<h3><%%= et_pb_ab_js_options[id].title %%></h3>
				<p><%%= et_pb_ab_js_options[id].desc %%></p>
			<%% } else { %%>
				<h3>%1$s</h3>
				<p>%2$s</p>
			<%% } %%>
		</script>',
		esc_html__( 'An Error Occurred', 'et_builder' ),
		esc_html__( 'For some reason, you cannot perform this task.', 'et_builder' )
	);

	// AB Testing Alert Yes/No :: Modal Window Template.
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-ab_testing_alert_yes_no">
			<div class="et_pb_prompt_modal">
				<div class="et_pb_prompt_buttons">
					<br/>
					<button class="et_pb_prompt_proceed_alternative et_pb_prompt_cancel">%1$s</button>
					<input type="submit" class="et_pb_prompt_proceed has_alternative has_cancel_alternative" value="%2$s" />
				</div>
			</div>
		</script>',
		et_builder_i18n( 'Cancel' ),
		esc_html__( 'Proceed', 'et_builder' )
	);

	// AB Testing Alert Yes/No :: Modal Content Template.
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-ab_testing_alert_yes_no-text">
			<%% if ( ! _.isUndefined( et_pb_ab_js_options[id] ) ) { %%>
				<h3><%%= et_pb_ab_js_options[id].title %%></h3>
				<p><%%= et_pb_ab_js_options[id].desc %%></p>
			<%% } else { %%>
				<h3>%1$s</h3>
				<p>%2$s</p>
			<%% } %%>
		</script>',
		esc_html__( 'An Error Occurred', 'et_builder' ),
		esc_html__( 'For some reason, you cannot perform this task.', 'et_builder' )
	);

	/**
	 * Split Testing :: Set global item winner status
	 */
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-set_global_subject_winner">
			<div class="et_pb_prompt_modal">
				<div class="et_pb_prompt_buttons">
					<br/>
					<button class="et_pb_prompt_proceed_alternative">%1$s</button>
					<input type="submit" class="et_pb_prompt_proceed has_alternative" value="%2$s" />
				</div>
			</div>
		</script>',
		et_core_esc_previously( $ab_testing['set_global_winner_status']['cancel'] ),
		et_core_esc_previously( $ab_testing['set_global_winner_status']['proceed'] )
	);

	// AB Testing :: Set global item winner status template.
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-set_global_subject_winner-text">
			<h3>%1$s</h3>
			<p>%2$s</p>
			<ol>
				<li>%3$s</li>
				<li>%4$s</li>
			</ol>
		</script>',
		et_core_esc_previously( $ab_testing['set_global_winner_status']['title'] ),
		et_core_esc_previously( $ab_testing['set_global_winner_status']['desc'] ),
		et_core_esc_previously( $ab_testing['set_global_winner_status']['option_1'] ),
		et_core_esc_previously( $ab_testing['set_global_winner_status']['option_2'] )
	);

	/**
	 * AB Testing :: View Stats Template
	 */
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-view_ab_stats">
			<div class="et_pb_prompt_modal et_pb_ab_view_stats">
				<a href="#" class="et_pb_prompt_dont_proceed et-pb-modal-close">
					<span>%1$s</span>
				</a>
			</div>
		</script>',
		et_builder_i18n( 'Cancel' )
	);

	$view_stats_tabs = '';

	foreach ( et_pb_ab_get_analysis_types() as $analysis ) {
		$view_stats_tabs .= sprintf(
			'<div class="view-stats-tab tab-%1$s" data-analysis="%1$s">
				<ul class="et-pb-ab-view-stats-time-filter">
					<li><a href="#" data-duration="day">%2$s</a></li>
					<li><a href="#" data-duration="week">%3$s</a></li>
					<li><a href="#" data-duration="month">%4$s</a></li>
					<li><a href="#" data-duration="all">%5$s</a></li>
				</ul><!-- .et-pb-ab-view-stats-time-filter -->

				<ul class="et-pb-ab-view-stats-subjects-filter">
				</ul><!-- .et-pb-ab-view-stats-subjects-filter -->

				<div class="view-stats-main-stats">
					<canvas id="ab-testing-stats-%1$s" class="ab-testing-stats" width="913" height="330"></canvas>
				</div>

				<h2 class="sub-heading">%6$s</h2>
				<div class="view-stats-table-wrapper">
					<table id="view-stats-table-%1$s" class="view-stats-table">
						<thead></thead>
						<tbody></tbody>
						<tfoot></tfoot>
					</table>
				</div><!-- .view-stats-table-wrapper -->
				<div class="view-stats-pie-wrapper">
					<canvas id="ab-testing-stats-pie-%1$s" class="ab-testing-stats-pie" width="200" height="200"></canvas>
					<ul class="ab-testing-stats-pie-legends">
					</ul><!-- .ab-testing-stats-pie-legends -->
				</div><!-- .view-stats-pie-wrapper -->
				<div class="no-stats">
					<span class="icon">
						<object type="image/svg+xml" data="%7$s/images/stats-no-data.svg"></object>
					</span>
					<h2>%8$s</h2>
					<p>%9$s</p>
				</div><!-- .no-stats -->
			</div>',
			esc_attr( $analysis ),
			esc_html__( 'Last 24 Hours', 'et_builder' ),
			esc_html__( 'Last 7 Days', 'et_builder' ),
			esc_html__( 'Last Month', 'et_builder' ),
			esc_html__( 'All Time', 'et_builder' ),
			esc_html__( 'Summary &amp; Data', 'et_builder' ),
			esc_url( ET_BUILDER_URI ),
			esc_html__( 'Statistics are still being collected for this time frame', 'et_builder' ),
			esc_html__( 'Stats will be displayed upon sufficient data collection', 'et_builder' )
		);
	}

	// AB Testing :: View Stats content.
	printf(
		'<script type="text/template" id="et-builder-prompt-modal-view_ab_stats-text">
			<h3>%1$s</h3>
			<ul class="et-pb-options-tabs-links">
				<li class="et_pb_options_tab_ab_stat_conversion et-pb-options-tabs-links-active" data-analysis="conversions">
					<a href="#">%6$s</a>
				</li>
				<li class="et_pb_options_tab_ab_stat_clicks" data-analysis="clicks">
					<a href="#">%2$s</a>
				</li>
				<li class="et_pb_options_tab_ab_stat_reads" data-analysis="reads">
					<a href="#">%3$s</a>
				</li>
				<li class="et_pb_options_tab_ab_stat_bounces" data-analysis="bounces">
					<a href="#">%4$s</a>
				</li>
				<li class="et_pb_options_tab_ab_stat_engagements" data-analysis="engagements">
					<a href="#">%5$s</a>
				</li>
				<li class="et_pb_options_tab_ab_stat_shortcode_conversions" data-analysis="shortcode_conversions">
					<a href="#">%13$s</a>
				</li>
				<li class="et_pb_ab_refresh_button">
					<a href="#" class="et-pb-ab-refresh-stats" title="%11$s">
						<span class="icon"></span><span class="label">%12$s</span>
					</a>
				</li>
			</ul><!-- .et-pb-options-tabs-links -->
			<div class="et-pb-ab-view-stats-content has-data">
				%7$s
			</div>
			<div class="et-pb-ab-view-stats-content no-data">
				<span class="icon">
					<object type="image/svg+xml" data="%8$s/images/stats-no-data.svg"></object>
				</span>
				<h2>%9$s</h2>
				<p>%10$s</p>
			</div>
			<div class="et_pb_prompt_buttons">
				<input type="submit" class="et_pb_prompt_proceed" value="%14$s">
			</div>
		</script>',
		esc_html__( 'Split Testing Statistics', 'et_builder' ),
		esc_html__( 'Clicks', 'et_builder' ),
		esc_html__( 'Reads', 'et_builder' ),
		esc_html__( 'Bounces', 'et_builder' ),
		esc_html__( 'Goal Engagement', 'et_builder' ), // 5
		esc_html__( 'Conversions', 'et_builder' ),
		et_core_esc_previously( $view_stats_tabs ),
		esc_url( ET_BUILDER_URI ),
		esc_html__( 'Statistics are being collected', 'et_builder' ),
		esc_html__( 'Stats will be displayed upon sufficient data collection', 'et_builder' ), // 10
		esc_attr__( 'Refresh Stats', 'et_builder' ),
		esc_html__( 'Refresh Stats', 'et_builder' ),
		esc_html__( 'Shortcode Conversions', 'et_builder' ),
		esc_attr__( 'End Split Test &amp; Pick Winner', 'et_builder' )
	);

	// "Add Specialty Section" Button Template
	printf(
		'<script type="text/template" id="et-builder-add-specialty-section-button">
			<a href="#" class="et-pb-section-add-specialty et-pb-add-specialty-template" data-is_template="true">%1$s</a>
		</script>',
		esc_html__( 'Add Specialty Section', 'et_builder' )
	);

	// Saved Entry Template.
	echo '<script type="text/template" id="et-builder-saved-entry">
			<a class="et_pb_saved_entry_item"><%= title %></a>
		</script>';

	// Font Family Select Template.
	$font_marker = et_pb_is_allowed( 'custom_fonts_management' ) ? '<span class="et-pb-user-font-marker"></span>' : '';

	$upload_button = et_pb_is_allowed( 'custom_fonts_management' ) ? sprintf( '<input type="button" class="button button-upload et-pb-font-upload-button" value="%1$s">', esc_html__( 'Upload', 'et_builder' ) ) : '';

	printf(
		'<script type="text/template" id="et-builder-google-fonts-options-items">
			<li class="et-pb-option-subgroup et-pb-option-subgroup-uploaded">
				<p class="et-pb-subgroup-title">%1$s</p>
				<ul class="et-pb-option-subgroup-container">
					<%% _.each(this.et_builder_template_options.user_fonts, function(font_data, font_name) { %%>
						<li class="select-option-item select-option-item-custom-font select-option-item-<%%= font_name.replace( / /g, "_" ) %%>" data-value="<%%= font_name %%>"><%%= font_name %%>%2$s</li>
					<%% }); %%>
				</ul>
				%3$s
			</li>
			%4$s
		</script>',
		esc_html__( 'Uploaded', 'et_builder' ),
		et_core_intentionally_unescaped( $font_marker, 'fixed_string' ),
		et_core_esc_previously( $upload_button ),
		et_core_esc_previously( et_builder_get_google_font_items() )
	);

	// Font Icons Template.
	printf(
		'<script type="text/template" id="et-builder-font-icon-list-items">
			%1$s
		</script>',
		et_core_esc_previously( et_pb_get_font_icon_list_items() )
	);

	// Histories Visualizer Item Template.
	printf(
		'<script type="text/template" id="et-builder-histories-visualizer-item-template">
			<li id="et-pb-history-<%%= this.options.get( "timestamp" ) %%>" class="<%%= this.options.get( "current_active_history" ) ? "active" : "undo"  %%>" data-timestamp="<%%= this.options.get( "timestamp" )  %%>">
				<span class="datetime"><%%= this.options.get( "datetime" )  %%></span>
				<span class="verb"> <%%= this.getVerb()  %%></span>
				<span class="noun"> <%%= this.getNoun()  %%></span>
				<%% if ( typeof this.getAddition === "function" && "" !== this.getAddition() ) { %%>
					<span class="addition"> <%%= this.getAddition() %%></span>
				<%% } %%>
			</li>
		</script>'
	);

	// Font Down Icons Template.
	printf(
		'<script type="text/template" id="et-builder-font-down-icon-list-items">
			%1$s
		</script>',
		et_core_esc_previously( et_pb_get_font_down_icon_list_items() )
	);

	printf(
		'<script type="text/template" id="et-builder-preview-icons-template">
			<ul class="et-pb-preview-screensize-switcher">
				<li><a href="#" class="et-pb-preview-mobile" data-width="375"><span class="label">%1$s</span></a></li>
				<li><a href="#" class="et-pb-preview-tablet" data-width="768"><span class="label">%2$s</span></a></li>
				<li><a href="#" class="et-pb-preview-desktop active"><span class="label">%3$s</span></a></li>
			</ul>
		</script>',
		esc_html__( 'Mobile', 'et_builder' ),
		et_builder_i18n( 'Tablet' ),
		et_builder_i18n( 'Desktop' )
	);

	printf(
		'<script type="text/template" id="et-builder-options-tabs-links-template">
			<ul class="et-pb-options-tabs-links">
				<%% _.each(this.et_builder_template_options.tabs.options, function(tab, index) { %%>
					<li class="et_pb_options_tab_<%%= tab.slug %%><%%= \'1\' === index ? \' et-pb-options-tabs-links-active\' : \'\' %%>">
						<a href="#"><%%= tab.label %%></a>
					</li>
				<%% }); %%>
			</ul>
		</script>'
	);

	printf(
		'<script type="text/template" id="et-builder-mobile-options-tabs-template">
			<div class="et_pb_mobile_settings_tabs et_pb_tabs_mobile">
				<a href="#" class="et_pb_mobile_settings_tab et_pb_mobile_settings_active_tab" data-settings_tab="desktop">
					%1$s
				</a>
				<a href="#" class="et_pb_mobile_settings_tab" data-settings_tab="tablet">
					%2$s
				</a>
				<a href="#" class="et_pb_mobile_settings_tab" data-settings_tab="phone">
					%3$s
				</a>
			</div>
		</script>',
		et_builder_i18n( 'Desktop' ),
		et_builder_i18n( 'Tablet' ),
		esc_html__( 'Smartphone', 'et_builder' )
	);

	printf(
		'<script type="text/template" id="et-builder-hover-options-tabs-template">
			<div class="et_pb_mobile_settings_tabs et_pb_tabs_hover et_pb_tabs_hover_only">
				<a href="#" class="et_pb_mobile_settings_tab et_pb_mobile_settings_active_tab et_pb_hover" data-settings_tab="default">
					%1$s
				</a>
				<a href="#" class="et_pb_mobile_settings_tab et_pb_hover" data-settings_tab="hover">
					%2$s
				</a>
			</div>
		</script>',
		et_builder_i18n( 'Default' ),
		esc_html__( 'Hover', 'et_builder' )
	);

	printf(
		'<script type="text/template" id="et-builder-mobile-hover-options-tabs-template">
			<div class="et_pb_mobile_settings_tabs et_pb_tabs_mobile et_pb_tabs_hover">
				<a href="#" class="et_pb_mobile_settings_tab et_pb_mobile_settings_active_tab" data-settings_tab="desktop">
					%1$s
				</a>
				<a href="#" class="et_pb_mobile_settings_tab et_pb_hover" data-settings_tab="hover">
					%2$s
				</a>
				<a href="#" class="et_pb_mobile_settings_tab et_pb_mobile" data-settings_tab="tablet">
					%3$s
				</a>
				<a href="#" class="et_pb_mobile_settings_tab et_pb_mobile" data-settings_tab="phone">
					%4$s
				</a>
			</div>
		</script>',
		et_builder_i18n( 'Desktop' ),
		esc_html__( 'Hover', 'et_builder' ),
		et_builder_i18n( 'Tablet' ),
		esc_html__( 'Smartphone', 'et_builder' )
	);

	printf(
		'<script type="text/template" id="et-builder-hover-icon-template"><span class="et-pb-hover-settings-toggle" data-id="placeholder">
			 <svg width="18" height="18" viewBox="-2 -3 16 16">
			     <path d="M8.69 9.43l2.22-.84a.5.5 0 0 0 .19-.8L5.22 1.28A.7.7 0 0 0 4 1.75v8.73a.5.5 0 0 0 .68.47l2.14-.81 1 2.42a1 1 0 1 0 1.86-.75z"></path>
			 </svg>
			 <input type="hidden" id="placeholder"/>
			 </span></script>'
	);

	printf(
		'<script type="text/template" id="et-builder-padding-option-template">
			<label>
				<%%= this.et_builder_template_options.padding.options.label %%>
				<input type="text" class="et_custom_margin et_custom_margin_<%%= this.et_builder_template_options.padding.options.side %%><%%= this.et_builder_template_options.padding.options.class %%><%%= \'need_mobile\' === this.et_builder_template_options.padding.options.need_mobile ? \' et_pb_setting_mobile et_pb_setting_mobile_desktop et_pb_setting_mobile_active\' : \'\' %%>"<%%= \'need_mobile\' === this.et_builder_template_options.padding.options.need_mobile ? \' data-device="desktop"\' : \'\' %%> />
				<%% if ( \'need_mobile\' === this.et_builder_template_options.padding.options.need_mobile ) { %%>
					<input type="text" class="et_custom_margin et_pb_setting_mobile et_pb_setting_mobile_tablet et_custom_margin_<%%= this.et_builder_template_options.padding.options.side %%><%%= this.et_builder_template_options.padding.options.class %%>" data-device="tablet" />
					<input type="text" class="et_custom_margin et_pb_setting_mobile et_pb_setting_mobile_phone et_custom_margin_<%%= this.et_builder_template_options.padding.options.side %%><%%= this.et_builder_template_options.padding.options.class %%>" data-device="phone" />
				<%% } %%>
			</label>
		</script>'
	);

	printf(
		'<script type="text/template" id="et-builder-yes_no_button-option-template">
			<div class="et_pb_yes_no_button et_pb_off_state">
				<span class="et_pb_value_text et_pb_on_value"><%%= this.et_builder_template_options.yes_no_button.options.on %%></span>
				<span class="et_pb_button_slider"></span>
				<span class="et_pb_value_text et_pb_off_value"><%%= this.et_builder_template_options.yes_no_button.options.off %%></span>
			</div>
		</script>'
	);

	print(
		'<script type="text/template" id="et-builder-animation_buttons-option-template">
			<div class="et_pb_animation_buttons">
				<% _.each(this.et_builder_template_options.animation_buttons.options, function(option_title, option_name) { %>
					<div class="et_animation_button">
						<a href="#">
							<span class="et_animation_button_title" data-value="<%= option_name %>"><%= option_title %></span>
							<span class="et_animation_button_icon">
								<div class="et-pb-icon">
									<svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><%= this.et_builder_template_options.options_icons[ "animation-" + option_name ] %></svg>
								</div>
							</span>
						</a>
					</div>
				<% }); %>
			</div>
		</script>'
	);

	print(
		'<script type="text/template" id="et-builder-background_tabs_nav-option-template">
			<ul class="et_pb_background-tab-navs">
				<% _.each(this.et_builder_template_options.background_tabs_nav.options, function(tab_nav_name, index) { %>
					<li><a href="#" class="et_pb_background-tab-nav et_pb_background-tab-nav--<%= tab_nav_name %>" data-tab="<%= tab_nav_name %>" title="<%= tab_nav_name %>"><div class="et-pb-icon"><svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><%= this.et_builder_template_options.options_icons[ "background-" + tab_nav_name ] %></svg></div></a></li>
				<% }); %>
			</ul>
		</script>'
	);

	print(
		'<script type="text/template" id="et-builder-background_gradient_buttons-option-template">
			<div class="et-pb-option-preview et-pb-option-preview--empty">
				<button class="et-pb-option-preview-button et-pb-option-preview-button--add">
					<div class="et-pb-icon">
						<svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><%= this.et_builder_template_options.options_icons[ "add" ] %></svg>
					</div>
				</button>
				<button class="et-pb-option-preview-button et-pb-option-preview-button--swap">
					<div class="et-pb-icon">
						<svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><%= this.et_builder_template_options.options_icons[ "swap" ] %></svg>
					</div>
				</button>
				<button class="et-pb-option-preview-button et-pb-option-preview-button--delete">
					<div class="et-pb-icon">
						<svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><%= this.et_builder_template_options.options_icons[ "delete" ] %></svg>
					</div>
				</button>
			</div>
		</script>'
	);

	print(
		'<script type="text/template" id="et-builder-option_preview_buttons-option-template">
			<div class="et-pb-option-preview et-pb-option-preview--empty">
				<button class="et-pb-option-preview-button et-pb-option-preview-button--add">
					<div class="et-pb-icon">
						<svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><%= this.et_builder_template_options.options_icons[ "add" ] %></svg>
					</div>
				</button>
				<button class="et-pb-option-preview-button et-pb-option-preview-button--edit">
					<div class="et-pb-icon">
						<svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><%= this.et_builder_template_options.options_icons[ "setting" ] %></svg>
					</div>
				</button>
				<button class="et-pb-option-preview-button et-pb-option-preview-button--delete">
					<div class="et-pb-icon">
						<svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><%= this.et_builder_template_options.options_icons[ "delete" ] %></svg>
					</div>
				</button>
			</div>
		</script>'
	);

	printf(
		'<script type="text/template" id="et-builder-multiple_buttons-option-template">
			<div class="et_pb_multiple_buttons">
				<%% _.each(this.et_builder_template_options.multiple_buttons.options, function(button_options, button_id) { %%>
					<div class="et_builder_<%%= button_id %%>_button et_builder_multiple_buttons_button mce-widget mce-btn" data-value="<%%= button_id %%>">
						<button type="button">
							<%%= button_options.title %%>
						</button>
					</div>
				<%% }); %%>
			</div>
		</script>'
	);

	printf(
		'<script type="text/template" id="et-builder-font-weight-items">
			<div class="et_pb_font_weight_container">
				<label for="et_builder_font_weight">%1$s:</label>
				<select class="et_builder_font_weight">
					<%% _.each(this.et_builder_template_options.font_weights, function(font_weight_name, font_weight) { %%>
						<option value="<%%= font_weight %%>"><%%= font_weight_name %%></option>
					<%% }); %%>
				<select>
			</div>
		</script>',
		esc_html__( 'Font Weight', 'et_builder' )
	);

	printf(
		'<script type="text/template" id="et-builder-font_buttons-option-template">
			<div class="et_pb_font_style_container">
				<label for="et_builder_font_style">%1$s:</label>
				<%% _.each(this.et_builder_template_options.font_buttons.options, function(font_button) { %%>
					<div class="et_builder_<%%= font_button %%>_font et_builder_font_style mce-widget mce-btn" data-button_name="<%%= font_button %%>">
						<button type="button">
							<i class="mce-ico mce-i-<%%= font_button %%>"></i>
						</button>
					</div>
				<%% }); %%>
			</div>
		</script>',
		esc_html__( 'Font Style', 'et_builder' )
	);

	printf(
		'<script type="text/template" id="et-builder-font_line_styles-option-template">
			<div class="et_pb_font_line_settings">
				<div class="et_pb_font_line_color">
					<label for="et_builder_font_style" data-underline_label="%5$s %7$s" data-strikethrough_label="%6$s %7$s">%1$s:</label>

					<span class="et-pb-custom-color-button et-pb-choose-custom-color-button"><span>%4$s</span></span>
					<div class="et-pb-custom-color-container et_pb_hidden">
						<input class="et-pb-color-picker-hex et-pb-color-picker-hex-alpha" type="text" data-alpha="true" placeholder="%2$s" data-selected-value="" value="" />
						<input class="et-pb-custom-color-picker et-pb-font-line-color-value" type="hidden" value="" />
					</div>
				</div>
				<div class="et_pb_font_line_style">
					<label for="et_builder_font_style" data-underline_label="%5$s %8$s" data-strikethrough_label="%6$s %8$s">%3$s:</label>
					<select class="et_pb_font_line_style_select">
						<option value="solid">solid</option>
						<option value="double">double</option>
						<option value="dotted">dotted</option>
						<option value="dashed">dashed</option>
						<option value="wavy">wavy</option>
					</select>
				</div>
			</div>
		</script>',
		esc_html__( 'Line Color', 'et_builder' ),
		esc_attr__( 'Hex Value', 'et_builder' ),
		esc_attr__( 'Line Style', 'et_builder' ),
		esc_attr__( 'Choose Custom Color', 'et_builder' ),
		esc_attr__( 'Underline', 'et_builder' ),
		esc_attr__( 'Strikethrough', 'et_builder' ),
		esc_attr__( 'Color', 'et_builder' ),
		esc_attr__( 'Style', 'et_builder' )
	);

	printf(
		'<script type="text/template" id="et-builder-text-align-buttons-option-template">
			<%% _.each(this.et_builder_template_options.text_align_buttons.options, function(text_align_button) { %%>
				<%%
					var text_align_button_classname = text_align_button === "justified" ? "justify" : text_align_button;
					text_align_button_classname = text_align_button === "force_left" ? "left" : text_align_button_classname;
					var text_align_button_type = this.et_builder_template_options.text_align_buttons.type;
				%%>
				<div class="et_builder_<%%= text_align_button %%>_text_align et_builder_text_align mce-widget mce-btn" data-value="<%%= text_align_button %%>">
					<button type="button">
						<i class="mce-ico align-<%%= text_align_button_type %%> mce-i-align<%%= text_align_button_classname %%>"></i>
					</button>
				</div>
			<%% }); %%>
		</script>'
	);

	printf(
		'<script type="text/template" id="et-builder-select-option-template">
			<%% _.each(this.et_builder_template_options.select.options.list, function(option_label, option_value) {
				var data = "";
				var option_label_updated = option_label;

				if ( _.isObject( option_label ) ) {
					if ( ! _.isUndefined( option_label["data"] ) ) {
						var data_key_name = _.keys( option_label["data"] );

						data = " data-" + _.escape( data_key_name[0] ) + "=\'" + _.escape( option_label["data"][ data_key_name[0] ] ) + "\'";
					}
					var option_label_updated = option_label["value"];
				}

				var select_name = this.et_builder_template_options.select.options.select_name.replace( "data.", "" );
				var select_value = this.et_builder_template_options.select.data[ select_name ];
				var select_value_escaped = _.escape( select_value );
				var option_value_escaped = _.escape( option_value );
				var default_value = this.et_builder_template_options.select.options.default;
				var selected_attr = ! _.isUndefined( select_value ) && "" !== select_value && option_value_escaped === select_value_escaped || ( _.isUndefined( select_value ) && default_value !== "" && option_value_escaped === default_value ) ? \' selected="selected"\' : "";
				%%>
				<option <%%= data %%> value="<%%= option_value_escaped %%>" <%%= selected_attr %%>><%%= _.escape( option_label_updated ) %%></option>
			<%% }); %%>
		</script>'
	);

	printf(
		'<script type="text/template" id="et-builder-failure-notice-template">
			%1$s
		</script>',
		et_core_esc_previously( et_builder_get_failure_notification_modal() )
	);

	printf(
		'<script type="text/template" id="et-builder-cache-notice-template">
			%1$s
		</script>',
		et_core_esc_previously( et_builder_get_cache_notification_modal() )
	);

	printf(
		'<script type="text/template" id="et-builder-page-creation-template">
			%1$s
		</script>',
		et_core_esc_previously( et_builder_page_creation_modal() )
	);

	// Help Template.
	printf(
		'<script type="text/template" id="et-builder-help-template">
			<h3 class="et-pb-settings-heading">%1$s</h3>

			<ul class="et-pb-options-tabs-links et-pb-help-switcher">
				<li class="et-pb-new-module et-pb-options-tabs-links-active" data-open_tab="et-pb-shortcuts-tab">
					<a href="#">%2$s</a>
				</li>
			</ul>

			<div class="et-pb-main-settings et-pb-main-settings-full et-pb-shortcuts-tab active-container"></div>
		</script>',
		esc_html__( 'Divi Builder Helper', 'et_builder' ),
		esc_html__( 'Shortcuts', 'et_builder' )
	);

	do_action( 'et_pb_after_page_builder' );
}

/**
 * Returns builder settings markup
 *
 * @param array $options builder settings' configuration.
 * @return string builder settings' markup
 */
function et_pb_get_builder_settings_fields( $options ) {
	$outputs  = '';
	$defaults = et_pb_get_builder_settings_configuration_default();

	foreach ( $options as $option ) {
		$option           = wp_parse_args( $option, $defaults );
		$type             = $option['type'];
		$field_list_class = $type;
		$affecting        = ! empty( $option['affects'] ) ? implode( '|', $option['affects'] ) : '';

		if ( $option['depends_show_if'] ) {
			$field_list_class .= ' et-pb-display-conditionally';
		}

		if ( isset( $option['class'] ) ) {
			$field_list_class .= ' ' . $option['class'];
		}

		$outputs .= sprintf(
			'<div class="et_pb_prompt_field_list et-pb-option-container %1$s" data-id="%2$s" data-type="%3$s" data-autoload="%4$s" data-affects="%5$s" data-visibility-dependency="%6$s">',
			esc_attr( $field_list_class ),
			esc_attr( $option['id'] ),
			esc_attr( $type ),
			esc_attr( $option['autoload'] ),
			esc_attr( $affecting ),
			esc_attr( $option['depends_show_if'] )
		);

		switch ( $option['type'] ) {
			case 'yes_no_button':
				$outputs .= sprintf(
					'<label>%2$s</label>
						<div class="et_pb_prompt_field">
							<div class="et_pb_yes_no_button_wrapper ">
								<div class="et_pb_yes_no_button et_pb_off_state">
									<span class="et_pb_value_text et_pb_on_value">%3$s</span>
									<span class="et_pb_button_slider"></span>
									<span class="et_pb_value_text et_pb_off_value">%4$s</span>
								</div>

								<select name="%1$s" id="%1$s" class="et-pb-main-setting regular-text">
									<option value="off">%5$s</option>
									<option value="on">%6$s</option>
								</select>
							</div><span class="et-pb-reset-setting"></span>
						</div>',
					esc_attr( $option['id'] ),
					esc_html( $option['label'] ),
					isset( $option['values'] ) ? esc_html( $option['values']['yes'] ) : et_builder_i18n( 'Yes' ),
					isset( $option['values'] ) ? esc_html( $option['values']['no'] ) : et_builder_i18n( 'No' ),
					et_builder_i18n( 'Off' ),
					et_builder_i18n( 'On' )
				);
				break;

			case 'codemirror':
			case 'textarea':
				$outputs .= sprintf(
					'<label for="%1$s">%2$s</label>
						<div class="et_pb_prompt_field">
							<textarea id="%1$s" name="%1$s"%3$s></textarea>
						</div>',
					esc_attr( $option['id'] ),
					esc_html( $option['label'] ),
					isset( $option['readonly'] ) && 'readonly' === $option['readonly'] ? ' readonly' : ''
				);
				break;

			case 'colorpalette':
				$outputs .= sprintf( '<label>%1$s</label><div class="et_pb_prompt_field">', esc_html( $option['label'] ) );

				$outputs .= '<div class="et_pb_colorpalette_overview">';

				for ( $colorpalette_index = 1; $colorpalette_index < 9; $colorpalette_index++ ) {
					$outputs .= sprintf( '<span class="colorpalette-item colorpalette-item-%1$s" data-index="%1$s"></span>', esc_attr( $colorpalette_index ) );
				}

				$outputs .= '</div>';

				for ( $colorpicker_index = 1; $colorpicker_index < 9; $colorpicker_index++ ) {
					$outputs .= sprintf(
						'<div class="colorpalette-colorpicker" data-index="%2$s">
							<input id="%1$s-%2$s" name="%1$s-%2$s" data-index="%2$s" type="text" class="input-colorpalette-colorpicker" data-alpha="true" />
						</div>',
						esc_attr( $option['id'] ),
						esc_attr( $colorpicker_index )
					);
				}

				$outputs .= '</div>';

				break;

			case 'color-alpha':
				$outputs .= sprintf(
					'<label for="%1$s">%2$s</label>
						<div class="et_pb_prompt_field">
							<input id="%1$s" name="%1$s" type="text" class="input-colorpicker" data-alpha="true" data-default-color="%3$s" />
						</div>',
					esc_attr( $option['id'] ),
					esc_html( $option['label'] ),
					esc_attr( $option['default'] )
				);
				break;

			case 'range':
				$outputs .= sprintf(
					'<label for="%1$s">%2$s</label>
						<div class="et_pb_prompt_field">
							<input id="%1$s" name="%1$s" type="range" class="range" step="%3$s" min="%4$s" max="%5$s" />
						</div>',
					esc_attr( $option['id'] ),
					esc_html( $option['label'] ),
					esc_attr( $option['range_settings']['step'] ),
					esc_attr( $option['range_settings']['min'] ),
					esc_attr( $option['range_settings']['max'] )
				);
				break;

			case 'select':
				$options = '';

				foreach ( $option['options'] as $value => $text ) {
					$options .= sprintf(
						'<option value="%1$s">%2$s</option>',
						esc_attr( $value ),
						esc_html( $text )
					);
				}

				$outputs .= sprintf(
					'<label for="%1$s">%2$s</label>
						<div class="et_pb_prompt_field">
							<select id="%1$s" name="%1$s">
								%3$s
							</select>
						</div>',
					esc_attr( $option['id'] ),
					esc_html( $option['label'] ),
					et_core_esc_previously( $options )
				);
				break;
		}

		$outputs .= sprintf( '</div><!-- .et_pb_prompt_field_list.et-pb-option-container.%1$s -->', esc_attr( $option['type'] ) );
	}

	return $outputs;
}

/**
 * Prints hidden inputs for passing settings data to database
 *
 * @param integer $post_id post id.
 *
 * @return void|bool
 */
function et_pb_builder_settings_hidden_inputs( $post_id ) {
	if ( ! class_exists( 'ET_Builder_Settings' ) ) {
		return false;
	}

	$settings = ET_Builder_Settings::get_fields();
	$defaults = et_pb_get_builder_settings_configuration_default();

	if ( empty( $settings ) ) {
		return;
	}

	if ( empty( $settings ) ) {
		return;
	}

	foreach ( $settings as $setting ) {
		$setting = wp_parse_args( $setting, $defaults );

		if ( ! $setting['autoload'] ) {
			continue;
		}

		$id       = '_' . $setting['id'];
		$meta_key = isset( $setting['meta_key'] ) ? $setting['meta_key'] : $id;
		$value    = get_post_meta( $post_id, $meta_key, true );

		if ( ( ! $value || '' === $value ) && $setting['default'] ) {
			$value = $setting['default'];
		}

		printf(
			'<input type="hidden" id="%1$s" name="%1$s" value="%2$s" />',
			esc_attr( $id ),
			esc_attr( $value )
		);
	}
}

/**
 * Prints hidden inputs for passing global modules data to database.
 *
 * @param integer $post_id post id.
 *
 * @return void
 */
function et_pb_builder_global_library_inputs( $post_id ) {
	global $typenow;

	if ( 'et_pb_layout' !== $typenow ) {
		return;
	}

	$template_scope     = wp_get_object_terms( get_the_ID(), 'scope' );
	$template_type      = wp_get_object_terms( get_the_ID(), 'layout_type' );
	$is_global_template = ! empty( $template_scope[0] ) ? $template_scope[0]->slug : 'regular';
	$template_type_slug = ! empty( $template_type[0] ) ? $template_type[0]->slug : '';

	if ( 'global' !== $is_global_template || 'module' !== $template_type_slug ) {
		return;
	}

	$excluded_global_options = get_post_meta( $post_id, '_et_pb_excluded_global_options' );

	printf(
		'<input type="hidden" id="et_pb_unsynced_global_attrs" name="et_pb_unsynced_global_attrs" value="%1$s" />',
		isset( $excluded_global_options[0] ) ? esc_attr( $excluded_global_options[0] ) : wp_json_encode( array() )
	);
}

/**
 * Returns array of default builder settings configuration item
 *
 * @return array
 */
function et_pb_get_builder_settings_configuration_default() {
	return array(
		'id'              => '',
		'type'            => '',
		'label'           => '',
		'min'             => '',
		'max'             => '',
		'step'            => '',
		'autoload'        => true,
		'default'         => false,
		'affects'         => array(),
		'depends_show_if' => false,
	);
}

/**
 * Update a builder setting.
 *
 * @param array  $settings The new option value.
 * @param string $post_id The post id or 'global' for global settings.
 */
function et_builder_update_settings( $settings, $post_id = 'global' ) {
	// Allow the use of uppercase in $is_bb variable as BB is common abbreviation.

	et_core_nonce_verified_previously();

	$is_global = 'global' === $post_id;
	$is_bb     = null === $settings;
	$settings  = $is_bb ? $_POST : $settings;
	$fields    = $is_global ? ET_Builder_Settings::get_fields( 'builder' ) : ET_Builder_Settings::get_fields();
	$utils     = ET_Core_Data_Utils::instance();
	$update    = array();

	foreach ( (array) $settings as $setting_key => $setting_value ) {
		$raw_setting_value = $setting_value;
		$setting_key       = $is_bb ? substr( $setting_key, 1 ) : $setting_key;

		// Verify setting key.
		if ( ! isset( $fields[ $setting_key ] ) || ! isset( $fields[ $setting_key ]['type'] ) ) {
			continue;
		}

		// Auto-formatting subjects' value format.
		if ( 'et_pb_ab_subjects' === $setting_key && is_array( $setting_value ) ) {
			$setting_value = implode( ',', $setting_value );
		}

		// TODO Possibly move sanitization.php to builder dir
		// Sanitize value.
		switch ( $fields[ $setting_key ]['type'] ) {
			case 'colorpalette':
				$palette_colors = explode( '|', $setting_value );
				$setting_value  = implode( '|', array_map( 'et_sanitize_alpha_color', $palette_colors ) );
				break;

			case 'range':
				// Avoid setting absolute value for range if option is z_index.
				if ( 'et_pb_page_z_index' === $setting_key ) {
					break;
				}
				$setting_value = absint( $setting_value );
				$range_min     = isset( $fields[ $setting_key ]['range_settings'] ) && isset( $fields[ $setting_key ]['range_settings']['min'] ) ?
					absint( $fields[ $setting_key ]['range_settings']['min'] ) : -1;
				$range_max     = isset( $fields[ $setting_key ]['range_settings'] ) && isset( $fields[ $setting_key ]['range_settings']['max'] ) ?
					absint( $fields[ $setting_key ]['range_settings']['max'] ) : -1;

				if ( $setting_value < $range_min || $range_max < $setting_value ) {
					continue 2;
				}

				break;

			case 'color-alpha':
				$setting_value = et_sanitize_alpha_color( $setting_value );
				break;

			case 'codemirror':
			case 'textarea':
				// Allow HTML content on Excerpt field.
				if ( 'et_pb_post_settings_excerpt' === $setting_key ) {
					$setting_value = wp_kses_post( $setting_value );
				} else {
					$setting_value = sanitize_textarea_field( $setting_value );
				}
				break;

			case 'categories':
				$setting_value = array_map( 'intval', explode( ',', $setting_value ) );
				break;

			default:
				$setting_value = sanitize_text_field( $setting_value );
				break;
		}

		// check whether or not the defined value === default value.
		$is_default = isset( $fields[ $setting_key ]['default'] ) && $setting_value === $fields[ $setting_key ]['default'];

		// Auto-formatting AB Testing status' meta key.
		if ( 'et_pb_enable_ab_testing' === $setting_key ) {
			$setting_key = 'et_pb_use_ab_testing';
		}

		/**
		 * Fires before updating a builder setting in the database.
		 *
		 * @param string     $setting_key   The option name/id.
		 * @param string     $setting_value The new option value.
		 * @param string|int $post_id       The post id or 'global' for global settings.
		 */
		do_action( 'et_builder_settings_update_option', $setting_key, $setting_value, $post_id );

		// If `post_field` is defined, we need to update the post.
		$post_field = $utils->array_get( $fields, "{$setting_key}.post_field", false );
		if ( false !== $post_field ) {
			// Only allowed in VB.
			if ( ! ( $is_global || $is_bb ) ) {
				// Save the post field so we can do a single update.
				// Use the raw value and rely on wp_update_post to sanitize it in order to allow certain HTML tags.
				$update[ $post_field ] = $raw_setting_value;
			}
			continue;
		}

		// If `taxonomy_name` is defined, we need to update the post terms.
		$taxonomy_name = $utils->array_get( $fields, "{$setting_key}.taxonomy_name", false );
		if ( false !== $taxonomy_name ) {
			// Only allowed in VB.
			if ( ! ( $is_global || $is_bb ) ) {
				$post_type = $utils->array_get( $fields, "{$setting_key}.post_type", false );
				if ( get_post_type( $post_id ) === $post_type ) {
					// Only update if the post type matches.
					wp_set_object_terms( $post_id, $setting_value, $taxonomy_name );
				}
			}
			continue;
		}

		// Save the setting in a post meta.
		$meta_key  = $utils->array_get( $fields, $setting_key . '.meta_key', false ) ? $fields[ $setting_key ]['meta_key'] : "_{$setting_key}";
		$save_post = $utils->array_get( $fields, $setting_key . '.save_post', true );
		if ( $is_bb && false === $save_post ) {
			// This meta key must be ignored during classic-editor / BB save action or it will
			// overwrite values in the WP edit page.
			continue;
		}
		// remove if value is default.
		if ( $is_default ) {
			$is_global ? et_delete_option( $setting_key ) : delete_post_meta( $post_id, $meta_key );
		} else {
			// Update.
			$is_global ? et_update_option( $setting_key, $setting_value ) : update_post_meta( $post_id, $meta_key, $setting_value );
		}

		// Removing autosave.
		delete_post_meta( $post_id, "{$meta_key}_draft" );
	}

	// Removing builder settings autosave.
	$current_user_id = get_current_user_id();

	delete_post_meta( $post_id, "_et_builder_settings_autosave_{$current_user_id}" );

	if ( count( $update ) > 0 ) {
		// This MUST NOT be executed while saving data in the BB or it will generate
		// an update loop that will end the universe as we know it.
		if ( ! ( $is_bb || wp_is_post_revision( $post_id ) ) ) {
			$update['ID'] = $post_id;
			wp_update_post( $update );
		}
	}
}

/**
 * Returns array of default color pallete.
 *
 * @param integer $post_id post id.
 *
 * @return array default color palette
 */
function et_pb_get_default_color_palette( $post_id = 0 ) {
	$default_palette = array(
		'#000000',
		'#FFFFFF',
		'#E02B20',
		'#E09900',
		'#EDF000',
		'#7CDA24',
		'#0C71C3',
		'#8300E9',
	);

	$saved_global_palette = et_get_option( 'divi_color_palette', false );

	$palette = $saved_global_palette && '' !== str_replace( '|', '', $saved_global_palette ) ? explode( '|', $saved_global_palette ) : $default_palette;

	return apply_filters( 'et_pb_get_default_color_palette', $palette, $post_id );
}

/**
 * Modify builder editor's TinyMCE configuration
 *
 * @param array  $mce_init An array with TinyMCE config.
 * @param string $editor_id  Unique editor identifier.
 *
 * @return array
 */
function et_pb_content_mce_config( $mce_init, $editor_id ) {
	if ( 'et_pb_content' === $editor_id && isset( $mce_init['toolbar1'] ) ) {
		// Get toolbar as array.
		$toolbar1 = explode( ',', $mce_init['toolbar1'] );

		// Look for read more (wp_more)'s array' key.
		$wp_more_key = array_search( 'wp_more', $toolbar1, true );

		if ( $wp_more_key ) {
			unset( $toolbar1[ $wp_more_key ] );
		}

		// Update toolbar1 configuration.
		$mce_init['toolbar1'] = implode( ',', $toolbar1 );
	}

	return $mce_init;
}
add_filter( 'tiny_mce_before_init', 'et_pb_content_mce_config', 10, 2 );

/**
 * Get post format with filterable output
 *
 * @todo once WordPress provide filter for get_post_format() output, this function can be retired
 * @see get_post_format()
 *
 * @return mixed string|bool string of post format or false for default
 */
function et_pb_post_format() {
	return apply_filters( 'et_pb_post_format', get_post_format(), get_the_ID() );
}

/**
 * Return post format into false when using pagebuilder.
 *
 * @param string  $post_format post format.
 * @param integer $post_id post id.
 *
 * @return mixed string|bool string of post format or false for default
 */
function et_pb_post_format_in_pagebuilder( $post_format, $post_id ) {

	if ( et_pb_is_pagebuilder_used( $post_id ) ) {
		return false;
	}

	return $post_format;
}
add_filter( 'et_pb_post_format', 'et_pb_post_format_in_pagebuilder', 10, 2 );

if ( ! function_exists( 'et_get_first_audio_block' ) ) :
	/**
	 * Return the first audio block from the post content.
	 */
	function et_get_first_audio_block() {
		$content = get_the_content();

		// It is assumed that audio module figures will not contain other figures.
		preg_match( '/<figure\s+[^>]*?class=([\'"])[^\'"]*?wp-block-audio[^\'"]*?\1[^>]*?>.*?<\/figure>/', $content, $matches );

		if ( empty( $matches ) ) {
			return '';
		}

		return $matches[0];
	}
endif;

if ( ! function_exists( 'et_pb_get_audio_player' ) ) :
	/**
	 * Return audio player.
	 */
	function et_pb_get_audio_player() {
		global $_et_pbgap_audio_to_remove;
		$shortcode_audio = '';

		$regex = get_shortcode_regex( array( 'audio' ) );
		preg_match_all( "/{$regex}/s", get_the_content(), $matches );

		foreach ( $matches[2] as $key => $shortcode_match ) {
			// Remove audio shortcode if its contains first attached audio file URL
			// first attached audio file is automatically appended on post's format content.
			if ( 'audio' === $shortcode_match ) {
				$_et_pbgap_audio_to_remove = $matches[0][0];
				$shortcode_audio           = do_shortcode( $_et_pbgap_audio_to_remove );
				break;
			}
		}

		if ( '' === $shortcode_audio ) {
			$_et_pbgap_audio_to_remove = et_get_first_audio_block();
			$shortcode_audio           = $_et_pbgap_audio_to_remove;
		}

		if ( '' === $shortcode_audio ) {
			return false;
		}

		$output = sprintf(
			'<div class="et_audio_container">
			    %1$s
		    </div>',
			$shortcode_audio
		);

		add_filter( 'the_content', 'et_delete_post_audio' );

		return $output;
	}
endif;

if ( ! function_exists( 'et_divi_post_format_content' ) ) :
	/**
	 * Displays post audio, quote and link post formats content
	 */
	function et_divi_post_format_content() {
		$post_format = et_pb_post_format();

		$text_color_class = et_divi_get_post_text_color();

		$inline_style = et_divi_get_post_bg_inline_style();

		global $post;

		if ( post_password_required( $post ) ) {
			return;
		}

		switch ( $post_format ) {
			case 'audio':
				printf(
					'<div class="et_audio_content%4$s"%5$s>
                        <h2><a href="%3$s">%1$s</a></h2>
                        %2$s
				    </div>',
					esc_html( get_the_title() ),
					et_core_intentionally_unescaped( et_pb_get_audio_player(), 'html' ),
					esc_url( get_permalink() ),
					esc_attr( $text_color_class ),
					et_core_esc_previously( $inline_style )
				);

				break;
			case 'quote':
				printf(
					'<div class="et_quote_content%4$s"%5$s>
                        %1$s
                        <a href="%2$s" class="et_quote_main_link">%3$s</a>
				    </div>',
					et_core_intentionally_unescaped( et_get_blockquote_in_content(), 'html' ),
					esc_url( get_permalink() ),
					esc_html__( 'Read more', 'et_builder' ),
					esc_attr( $text_color_class ),
					et_core_esc_previously( $inline_style )
				);

				break;
			case 'link':
				printf(
					'<div class="et_link_content%5$s"%6$s>
                        <h2><a href="%2$s">%1$s</a></h2>
                        <a href="%3$s" class="et_link_main_url">%4$s</a>
				    </div>',
					esc_html( get_the_title() ),
					esc_url( get_permalink() ),
					esc_url( et_get_link_url() ),
					esc_html( et_get_link_url() ),
					esc_attr( $text_color_class ),
					et_core_esc_previously( $inline_style )
				);

				break;
		}
	}
endif;


if ( ! function_exists( 'et_get_blockquote_in_content' ) ) :
	/**
	 * Extract and return the first blockquote from content.
	 */
	function et_get_blockquote_in_content() {
		global $more;
		$more_default = $more;
		$more         = 1; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Disable `$more` flag to get the blockquote that may exist after <!--more--> tag.

		remove_filter( 'the_content', 'et_remove_blockquote_from_content' );

		$content = apply_filters( 'the_content', get_the_content() );

		add_filter( 'the_content', 'et_remove_blockquote_from_content' );

		$more = $more_default; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Restore default `$more` flag.

		if ( preg_match( '/<blockquote(.+?)<\/blockquote>/is', $content, $matches ) ) {
			return $matches[0];
		} else {
			return false;
		}
	}
endif;

if ( ! function_exists( 'et_get_link_url' ) ) :
	/**
	 * Return link from the post content.
	 */
	function et_get_link_url() {
		$link_url = get_post_meta( get_the_ID(), '_format_link_url', true );
		if ( '' !== $link_url ) {
			return $link_url;
		}

		$content = get_the_content();
		$has_url = get_url_in_content( $content );

		return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
	}
endif;

if ( ! function_exists( 'et_get_first_video' ) ) :
	/**
	 * Fix the issue with thumbnail video player, not working, when video url is added to content without shortcode
	 */
	function et_get_first_video() {
		$first_url    = '';
		$first_video  = '';
		$video_width  = (int) apply_filters( 'et_blog_video_width', 1080 );
		$video_height = (int) apply_filters( 'et_blog_video_height', 630 );

		$i       = 0;
		$content = get_the_content();

		preg_match_all( '|^\s*https?://[^\s"]+\s*$|im', $content, $urls );

		foreach ( $urls[0] as $url ) {
			$i++;

			if ( 1 === $i ) {
				$first_url = trim( $url );
			}

			$oembed = wp_oembed_get( esc_url( $url ) );

			if ( ! $oembed ) {
				continue;
			}

			$first_video = $oembed;
			$first_video = preg_replace( '/<embed /', '<embed wmode="transparent" ', $first_video );
			$first_video = preg_replace( '/<\/object>/', '<param name="wmode" value="transparent" /></object>', $first_video );

			// If the url comes from a GB embed block.
			if ( preg_match( '|wp-block-embed.+?' . preg_quote( $url, null ) . '|s', $content ) ) {
				// We need to remove some useless markup later.
				add_filter( 'the_content', 'et_delete_post_video' );
			}
			break;
		}

		if ( '' === $first_video ) {
			// Gutenberg compatibility.
			if ( ! has_shortcode( $content, 'video' ) && empty( $first_url ) ) {
				preg_match( '/<!-- wp:video[^\]]+?class="wp-block-video"><video[^\]]+?src="([^\]]+?)"[^\]]+?<!-- \/wp:video -->/', $content, $gb_video );
				$first_url = isset( $gb_video[1] ) ? $gb_video[1] : false;
			}

			if ( ! has_shortcode( $content, 'video' ) && ! empty( $first_url ) ) {
				$video_shortcode = sprintf( '[video src="%1$s" /]', esc_attr( $first_url ) );

				if ( ! empty( $gb_video ) ) {
					$content = str_replace( $gb_video[0], $video_shortcode, $content );
				} else {
					$content = str_replace( $first_url, $video_shortcode, $content );
				}
			}

			if ( has_shortcode( $content, 'video' ) ) {
				$regex = get_shortcode_regex();
				preg_match( "/{$regex}/s", $content, $match );

				$first_video = preg_replace( '/width="[0-9]*"/', "width=\"{$video_width}\"", $match[0] );
				$first_video = preg_replace( '/height="[0-9]*"/', "height=\"{$video_height}\"", $first_video );

				add_filter( 'the_content', 'et_delete_post_video' );

				$first_video = do_shortcode( et_pb_fix_shortcodes( $first_video ) );
			}
		}

		return ( '' !== $first_video ) ? $first_video : false;
	}
endif;

if ( ! function_exists( 'et_delete_post_video' ) ) :
	/**
	 * Removes the first video shortcode from content on single pages since it is displayed
	 * at the top of the page. This will also remove the video shortcode url from archive pages content
	 *
	 * @param string $content post content.
	 */
	function et_delete_post_video( $content ) {
		if ( has_post_format( 'video' ) ) :
			if ( has_shortcode( $content, 'video' ) ) {
				$regex = get_shortcode_regex();
				preg_match_all( "/{$regex}/s", $content, $matches );

				// $matches[2] holds an array of shortcodes names in the post
				foreach ( $matches[2] as $key => $shortcode_match ) {
					if ( 'video' === $shortcode_match ) {
						$content = str_replace( $matches[0][ $key ], '', $content );
						if ( is_single() && is_main_query() ) {
							break;
						}
					}
				}
			} else {
				// Gutenberg compatibility.
				preg_match( '/<figure class="wp-block-video"[^\]]+?<video[^\]]+?src="([^\]]+?)"[^\]]+?<\/figure>/', $content, $gb_video );

				if ( ! empty( $gb_video[0] ) ) {
					$content = str_replace( $gb_video[0], '', $content );
				} else {
					// Remove GB embed caption for the first video.
					$content = preg_replace( '|<figure class="wp-block-embed.+?><div.+?>\s*?</div>.+?</figure>|', '', $content, 1 );
				}
			}
		endif;

		return $content;
	}
endif;

if ( ! function_exists( 'et_delete_post_audio' ) ) :
	/**
	 * Removes the audio shortcode of the first attached (NOT embedded) audio from content on single pages since
	 * it is displayed at the top of the page. This will also remove the audio shortcode url from archive pages content
	 *
	 * @see https://www.elegantthemes.com/documentation/divi/post-formats/
	 *
	 * @param string $content post content.
	 */
	function et_delete_post_audio( $content ) {
		global $_et_pbgap_audio_to_remove;

		if ( has_post_format( 'audio' ) && $_et_pbgap_audio_to_remove ) {
			$content = str_replace( $_et_pbgap_audio_to_remove, '', $content );
		}

		return $content;
	}
endif;

if ( ! function_exists( 'et_delete_post_first_video' ) ) :
	/**
	 * Delete the first video url from the post content.
	 *
	 * @param string $content post content.
	 */
	function et_delete_post_first_video( $content ) {

		if ( 'video' !== et_pb_post_format() ) {
			return $content;
		}

		$first_video = et_get_first_video();
		if ( false !== $first_video ) {
			preg_match_all( '|^\s*https?:\/\/[^\s"]+\s*|im', $content, $urls );

			if ( ! empty( $urls[0] ) ) {
				$content = str_replace( $urls[0], '', $content );
			}
		}

		return $content;
	}
endif;

/**
 * Fix JetPack post excerpt shortcode issue.
 *
 * @param array $results related posts.
 *
 * @return mixed
 */
function et_jetpack_post_excerpt( $results ) {
	foreach ( $results as $key => $post ) {
		if ( isset( $post['excerpt'] ) ) {
			// Remove ET shortcodes from JetPack excerpt.
			$results[ $key ]['excerpt'] = preg_replace( '#\[et_pb(.*)\]#', '', $post['excerpt'] );
		}
	}
	return $results;
}
add_filter( 'jetpack_relatedposts_returned_results', 'et_jetpack_post_excerpt' );

/**
 * Adds a Divi gallery type when the Jetpack plugin is enabled.
 *
 * @param array $types gallery types.
 */
function et_jetpack_gallery_type( $types ) {
	$types['divi'] = 'Divi';
	return $types;
}
add_filter( 'jetpack_gallery_types', 'et_jetpack_gallery_type' );

if ( ! function_exists( 'et_get_gallery_attachments' ) ) :
	/**
	 * Fetch the gallery attachments
	 *
	 * @param array $attr gallery shortcode attributes.
	 */
	function et_get_gallery_attachments( $attr ) {
		// We're trusting author input, so let's at least make sure it looks like a valid orderby statement.
		if ( isset( $attr['orderby'] ) ) {
			$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
			if ( ! $attr['orderby'] ) {
				unset( $attr['orderby'] );
			}
		}
		$html5 = current_theme_supports( 'html5', 'gallery' );
		$pairs = array(
			'order'      => 'ASC',
			'orderby'    => 'menu_order ID',
			'id'         => get_the_ID() ? get_the_ID() : 0,
			'itemtag'    => $html5 ? 'figure' : 'dl',
			'icontag'    => $html5 ? 'div' : 'dt',
			'captiontag' => $html5 ? 'figcaption' : 'dd',
			'columns'    => 3,
			'size'       => 'thumbnail',
			'include'    => '',
			'exclude'    => '',
			'link'       => '',
		);
		$atts  = shortcode_atts( $pairs, $attr, 'gallery' );

		$id = intval( $atts['id'] );
		if ( 'RAND' === $atts['order'] ) {
			$atts['orderby'] = 'none';
		}
		if ( ! empty( $atts['include'] ) ) {
			$_attachments = get_posts(
				array(
					'include'        => $atts['include'],
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $atts['order'],
					'orderby'        => $atts['orderby'],
				)
			);

			$attachments = array();
			foreach ( $_attachments as $key => $val ) {
				$attachments[ $val->ID ] = $_attachments[ $key ];
			}
		} elseif ( ! empty( $atts['exclude'] ) ) {
			$attachments = get_children(
				array(
					'post_parent'    => $id,
					'exclude'        => $atts['exclude'],
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $atts['order'],
					'orderby'        => $atts['orderby'],
				)
			);
		} else {
			$attachments = get_children(
				array(
					'post_parent'    => $id,
					'post_status'    => 'inherit',
					'post_type'      => 'attachment',
					'post_mime_type' => 'image',
					'order'          => $atts['order'],
					'orderby'        => $atts['orderby'],
				)
			);
		}

		return $attachments;
	}
endif;

/**
 * Generate the HTML for custom gallery layouts.
 *
 * @param string $val output.
 * @param string $attr gallery settings.
 *
 * @return string
 */
function et_gallery_layout( $val, $attr ) {
	// check to see if the gallery output is already rewritten.
	if ( ! empty( $val ) ) {
		return $val;
	}

	// Do not filter gallery items in plugin to not break custom styles which may be applied by 3rd party theme.
	if ( et_is_builder_plugin_active() ) {
		return $val;
	}

	if ( ! apply_filters( 'et_gallery_layout_enable', false ) ) {
		return $val;
	}

	$output = '';

	if ( ! is_singular() && ! et_pb_is_pagebuilder_used( get_the_ID() ) && ! is_et_pb_preview() ) {
		$attachments    = et_get_gallery_attachments( $attr );
		$gallery_output = '';
		foreach ( $attachments as $attachment ) {
			$attachment_image = wp_get_attachment_url( $attachment->ID, 'et-pb-post-main-image-fullwidth' );
			$gallery_output  .= sprintf(
				'<div class="et_pb_slide" style="background: url(%1$s);"></div>',
				esc_attr( $attachment_image )
			);
		}
		$output = sprintf(
			'<div class="et_pb_slider et_pb_slider_fullwidth_off et_pb_gallery_post_type">
				<div class="et_pb_slides">
					%1$s
				</div>
			</div>',
			$gallery_output
		);

	} else {
		if ( ! isset( $attr['type'] ) || ! in_array( $attr['type'], array( 'rectangular', 'square', 'circle', 'rectangle' ), true ) ) {
			$attachments    = et_get_gallery_attachments( $attr );
			$gallery_output = '';
			foreach ( $attachments as $attachment ) {
				$gallery_output .= sprintf(
					'<li class="et_gallery_item et_pb_gallery_image">
						<a href="%1$s" title="%3$s">
							<span class="et_portfolio_image">
								%2$s
								<span class="et_overlay"></span>
							</span>
						</a>
						%4$s
					</li>',
					esc_url( wp_get_attachment_url( $attachment->ID, 'full' ) ),
					wp_get_attachment_image( $attachment->ID, 'et-pb-portfolio-image' ),
					esc_attr( $attachment->post_title ),
					! empty( $attachment->post_excerpt )
						? sprintf( '<p class="et_pb_gallery_caption">%1$s</p>', esc_html( $attachment->post_excerpt ) )
						: ''
				);
			}
			$output = sprintf(
				'<ul class="et_post_gallery clearfix">
					%1$s
				</ul>',
				$gallery_output
			);
		}
	}
	return $output;
}
add_filter( 'post_gallery', 'et_gallery_layout', 1000, 2 );

if ( ! function_exists( 'et_pb_gallery_images' ) ) :
	/**
	 * Display image gallery.
	 *
	 * @param string $force_gallery_layout Optional. Slider gallery layout.
	 */
	function et_pb_gallery_images( $force_gallery_layout = '' ) {
		if ( 'slider' === $force_gallery_layout ) {
			// Get the post content.
			$post = get_post( get_the_ID() );
			if ( ! $post ) {
				return '';
			}
			// We want to include GB galleries in results so need to convert them to shortcodes
			// because `get_post_gallery` won't find them otherwise.
			$post->post_content = apply_filters( 'et_gb_galleries_to_shortcodes', $post->post_content );
			$attachments        = get_post_gallery( $post, false );
			$gallery_output     = '';
			$output             = '';
			$images_array       = ! empty( $attachments['ids'] ) ? explode( ',', $attachments['ids'] ) : array();

			if ( empty( $images_array ) ) {
				return $output;
			}

			foreach ( $images_array as $attachment ) {
				$image_src       = wp_get_attachment_url( $attachment, 'et-pb-post-main-image-fullwidth' );
				$gallery_output .= sprintf(
					'<div class="et_pb_slide" style="background: url(%1$s);"></div>',
					esc_url( $image_src )
				);
			}
			printf(
				'<div class="et_pb_slider et_pb_slider_fullwidth_off et_pb_gallery_post_type">
                    <div class="et_pb_slides">
                        %1$s
                    </div>
			    </div>',
				et_core_esc_previously( $gallery_output )
			);
		} else {
			add_filter( 'et_gallery_layout_enable', 'et_gallery_layout_turn_on' );
			printf(
				do_shortcode( '%1$s' ),
				et_core_intentionally_unescaped( get_post_gallery(), 'html' )
			);
			remove_filter( 'et_gallery_layout_enable', 'et_gallery_layout_turn_on' );
		}
	}
endif;

/**
 * Used to always use divi gallery on et_pb_gallery_images
 */
function et_gallery_layout_turn_on() {
	return true;
}

/**
 * Remove Elegant Builder plugin filter, that activates visual mode on each page load in WP-Admin
 */
function et_pb_remove_lb_plugin_force_editor_mode() {
	remove_filter( 'wp_default_editor', 'et_force_tmce_editor' );
}
add_action( 'admin_init', 'et_pb_remove_lb_plugin_force_editor_mode' );

/**
 *
 * Generates array of all Role options
 */
function et_pb_all_role_options() {
	// get all the roles that can edit theme options.
	$applicability_roles = et_core_get_roles_by_capabilities( [ 'edit_theme_options' ] );

	// Get all the roles that can edit theme options and edit others posts.
	$tb_applicability_roles = et_core_get_roles_by_capabilities( [ 'edit_theme_options', 'edit_others_posts' ], true );

	// get all the modules and build array of capabilities for them.
	$all_modules_array  = ET_Builder_Element::get_modules_array();
	$custom_user_tabs   = ET_Builder_Element::get_tabs();
	$options_categories = ET_Builder_Element::get_options_categories();
	$module_capabilies  = array();
	$tabs_array         = array(
		'general_settings'    => array(
			'name' => esc_html__( 'Content Settings', 'et_builder' ),
		),
		'advanced_settings'   => array(
			'name' => esc_html__( 'Design Settings', 'et_builder' ),
		),
		'custom_css_settings' => array(
			'name' => esc_html__( 'Advanced Settings', 'et_builder' ),
		),
	);

	// add all custom user tabs into list.
	if ( ! empty( $custom_user_tabs ) ) {
		foreach ( $custom_user_tabs as $module => $tabs_data ) {
			if ( ! empty( $tabs_data ) ) {
				foreach ( $tabs_data as $tab_slug => $tab_data ) {
					$tabs_array[ $tab_slug ] = array(
						'name' => $tab_data['name'],
					);
				}
			}
		}
	}

	foreach ( $all_modules_array as $module => $module_details ) {
		if ( ! in_array( $module_details['label'], array( 'et_pb_section', 'et_pb_row', 'et_pb_row_inner', 'et_pb_column' ), true ) ) {
			$module_capabilies[ $module_details['label'] ] = array(
				'name' => sanitize_text_field( $module_details['title'] ),
			);
		}
	}

	// we need to display some options only when theme activated.
	$theme_only_options = ! et_is_builder_plugin_active()
		? array(
			'theme_customizer' => array(
				'name'          => esc_html__( 'Theme Customizer', 'et_builder' ),
				'applicability' => $applicability_roles,
			),
			'page_options'     => array(
				'name' => esc_html__( 'Page Options', 'et_builder' ),
			),
		)
		: array();

	$all_role_options = array(
		'general_capabilities'        => array(
			'section_title' => '',
			'options'       => array(
				'theme_options'  => array(
					'name'          => et_is_builder_plugin_active() ? esc_html__( 'Plugin Options', 'et_builder' ) : esc_html__( 'Theme Options', 'et_builder' ),
					'applicability' => $applicability_roles,
				),
				// Added capabilities, so we can control menu role wise effectively.
				'divi_library'   => array(
					'name'          => esc_html__( 'Divi Library', 'et_builder' ),
					'applicability' => $applicability_roles,
					'capabilities'  => 'export',
				),
				'theme_builder'  => array(
					'name'          => esc_html__( 'Theme Builder', 'et_builder' ),
					'applicability' => $tb_applicability_roles,
				),
				'support_center' => array(
					'name'          => esc_html__( 'Support Center', 'et_builder' ),
					'applicability' => $applicability_roles,
				),
				'divi_ai'        => array(
					'name'          => esc_html__( 'Divi AI', 'et_builder' ),
					'applicability' => array( 'administrator', 'editor' ),
				),
				'ab_testing'     => array(
					'name' => esc_html__( 'Split Testing', 'et_builder' ),
				),
			),
		),
		'builder_capabilities'        => array(
			'section_title' => esc_html__( 'Builder Interface', 'et_builder' ),
			'options'       => array(
				'add_module'                         => array(
					'name' => esc_html__( 'Add/Delete Item', 'et_builder' ),
				),
				'edit_module'                        => array(
					'name' => esc_html__( 'Edit Item', 'et_builder' ),
				),
				'move_module'                        => array(
					'name' => esc_html__( 'Move Item', 'et_builder' ),
				),
				'disable_module'                     => array(
					'name' => esc_html__( 'Disable Item', 'et_builder' ),
				),
				'lock_module'                        => array(
					'name' => esc_html__( 'Lock Item', 'et_builder' ),
				),
				'divi_builder_control'               => array(
					'name' => esc_html__( 'Toggle Divi Builder', 'et_builder' ),
				),
				'load_layout'                        => array(
					'name' => esc_html__( 'Load Layout', 'et_builder' ),
				),
				'use_visual_builder'                 => array(
					'name' => esc_html__( 'Use Visual Builder', 'et_builder' ),
				),
				'custom_fonts_management'            => array(
					'name' => esc_html__( 'Upload/Remove Fonts', 'et_builder' ),
				),
				'read_dynamic_content_custom_fields' => array(
					'name' => esc_html__( 'Dynamic Content Custom Fields', 'et_builder' ),
				),
			),
		),
		'library_capabilities'        => array(
			'section_title' => esc_html__( 'Library Settings', 'et_builder' ),
			'options'       => array(
				'save_library'        => array(
					'name' => esc_html__( 'Save To Library', 'et_builder' ),
				),
				'add_library'         => array(
					'name' => esc_html__( 'Add From Library', 'et_builder' ),
				),
				'edit_global_library' => array(
					'name' => esc_html__( 'Edit Global Items', 'et_builder' ),
				),
			),
		),
		'module_tabs'                 => array(
			'section_title' => esc_html__( 'Settings Tabs', 'et_builder' ),
			'options'       => $tabs_array,
		),
		'general_module_capabilities' => array(
			'section_title' => esc_html__( 'Settings Types', 'et_builder' ),
			'options'       => $options_categories,
		),
		'module_capabilies'           => array(
			'section_title' => esc_html__( 'Module Use', 'et_builder' ),
			'options'       => $module_capabilies,
		),
	);

	$all_role_options = apply_filters( 'add_et_builder_role_options', $all_role_options );

	$all_role_options['general_capabilities']['options'] = array_merge( $all_role_options['general_capabilities']['options'], $theme_only_options );

	// Set portability capabilities.
	$registered_portabilities = et_core_cache_get_group( 'et_core_portability' );

	if ( ! empty( $registered_portabilities ) ) {
		$all_role_options['general_capabilities']['options']['portability'] = array(
			'name' => esc_html__( 'Portability', 'et_builder' ),
		);
		$all_role_options['portability']                                    = array(
			'section_title' => esc_html__( 'Portability', 'et_builder' ),
			'options'       => array(),
		);

		// Dynamically create an option foreach portability.
		foreach ( $registered_portabilities as $portability_context => $portability_instance ) {
			$portability_options = array(
				'name' => esc_html( $portability_instance->name ),
			);

			if ( isset( $portability_instance->applicability ) ) {
				$portability_options['applicability'] = $portability_instance->applicability;
			}

			$all_role_options['portability']['options'][ "{$portability_context}_portability" ] = $portability_options;
		}
	}

	return $all_role_options;
}

/**
 *
 * Prints the admin page for Role Editor
 */
function et_pb_display_role_editor() {
	$all_role_options    = et_pb_all_role_options();
	$option_tabs         = '';
	$menu_tabs           = '';
	$builder_roles_array = et_pb_get_all_roles_list();

	foreach ( $builder_roles_array as $role => $role_title ) {
		$option_tabs .= et_pb_generate_roles_tab( $all_role_options, $role );

		$menu_tabs .= sprintf(
			'<a href="#" class="et-pb-layout-buttons%4$s" data-open_tab="et_pb_role-%3$s_options" title="%1$s">
				<span>%2$s</span>
			</a>',
			esc_attr( $role_title ),
			esc_html( $role_title ),
			esc_attr( $role ),
			'administrator' === $role ? ' et_pb_roles_active_menu' : ''
		);
	}

	printf(
		'<div class="et_pb_roles_main_container">
			<a href="#" id="et_pb_save_roles" class="button button-primary button-large">%3$s</a>
			<h3 class="et_pb_roles_title"><span>%2$s</span></h3>
			<div id="et_pb_main_container" class="post-type-page">
				<div id="et_pb_layout_controls">
					%1$s
					<a href="#" class="et-pb-layout-buttons et-pb-layout-buttons-reset" title="Reset all settings">
						<span class="icon"></span><span class="label">Reset</span>
					</a>
					%4$s
				</div>
			</div>
			<div class="et_pb_roles_container_all">
				%5$s
			</div>
		</div>',
		et_core_esc_previously( $menu_tabs ),
		esc_html__( 'Divi Role Editor', 'et_builder' ),
		esc_html__( 'Save Divi Roles', 'et_builder' ),
		et_core_esc_previously( et_builder_portability_link( 'et_pb_roles', array( 'class' => 'et-pb-layout-buttons et-pb-portability-button' ) ) ),
		et_core_esc_previously( $option_tabs )
	);
}

/**
 * Generates the options tab for specified role.
 *
 * @param array  $all_role_options array of all Role options.
 * @param string $role role.
 *
 * @return string
 */
function et_pb_generate_roles_tab( $all_role_options, $role ) {
	$form_sections = '';

	// generate all sections of the form for current role.
	if ( ! empty( $all_role_options ) ) {
		foreach ( $all_role_options as $capability_id => $capability_options ) {
			if ( isset( $capability_options['applicability'] ) && ! in_array( $role, $capability_options['applicability'], true ) ) {
				continue;
			}

			$form_sections .= sprintf(
				'<div class="et_pb_roles_section_container">
					%1$s
					<div class="et_pb_roles_options_internal">
						%2$s
					</div>
				</div>',
				! empty( $capability_options['section_title'] )
					? sprintf( '<h4 class="et_pb_roles_divider">%1$s <span class="et_pb_toggle_all"></span></h4>', esc_html( $capability_options['section_title'] ) )
					: '',
				et_pb_generate_capabilities_output( $capability_options['options'], $role )
			);
		}
	}

	$output = sprintf(
		'<div class="et_pb_roles_options_container et_pb_role-%2$s_options%3$s">
			<p class="et_pb_roles_notice">%1$s</p>
			<form id="et_pb_%2$s_role" data-role_id="%2$s">
				%4$s
			</form>
		</div>',
		esc_html__( 'Using the Divi Role Editor, you can limit the types of actions that can be taken by WordPress users of different roles. This is a great way to limit the functionality available to your customers or guest authors to ensure that they only have the necessary options available to them.', 'et_builder' ),
		esc_attr( $role ),
		'administrator' === $role ? ' active-container' : '',
		$form_sections // #4
	);

	return $output;
}

/**
 * Generates the enable/disable buttons list based on provided capabilities array and role.
 *
 * @param array  $cap_array capabilities.
 * @param string $role user role.
 *
 * @return string
 */
function et_pb_generate_capabilities_output( $cap_array, $role ) {
	$output = '';

	if ( ! empty( $cap_array ) ) {
		$user_has_all_capabilities = true;
		$role_obj                  = get_role( $role );

		foreach ( $cap_array as $capability => $capability_details ) {
			if ( empty( $capability_details['applicability'] ) || ( ! empty( $capability_details['applicability'] ) && in_array( $role, $capability_details['applicability'], true ) ) ) {

				// $capability_details['capabilities'] is an array of capabilities that are required to see this option.
				if ( isset( $capability_details['capabilities'] ) ) {
					if ( is_string( $capability_details['capabilities'] ) && ! $role_obj->has_cap( $capability_details['capabilities'] ) ) {
						$user_has_all_capabilities = false;
					} elseif ( is_array( $capability_details['capabilities'] ) ) {
						foreach ( $capability_details['capabilities'] as $capability ) {
							if ( ! $role_obj->has_cap( $capability ) ) {
								$user_has_all_capabilities = false;
								break;
							}
						}
					}

					if ( ! $user_has_all_capabilities ) {
						continue;
					}
				}

				$output .= sprintf(
					'<div class="et_pb_capability_option">
						<span class="et_pb_capability_title">%4$s</span>
						<div class="et_pb_yes_no_button_wrapper">
							<div class="et_pb_yes_no_button et_pb_on_state">
								<span class="et_pb_value_text et_pb_on_value">%1$s</span>
								<span class="et_pb_button_slider"></span>
								<span class="et_pb_value_text et_pb_off_value">%2$s</span>
							</div>
							<select name="%3$s" id="%3$s" class="et-pb-main-setting regular-text">
								<option value="on" %5$s>Yes</option>
								<option value="off" %6$s>No</option>
							</select>
						</div>
					</div>',
					esc_html__( 'Enabled', 'et_builder' ),
					esc_html__( 'Disabled', 'et_builder' ),
					esc_attr( $capability ),
					esc_html( $capability_details['name'] ),
					selected( true, et_pb_is_allowed( $capability, $role ), false ),
					selected( false, et_pb_is_allowed( $capability, $role ), false )
				);
			}
		}
	}

	return $output;
}

/**
 * Loads scripts and styles for Role Editor Admin page
 *
 * @param string $hook hook name.
 */
function et_pb_load_roles_admin( $hook ) {
	// load scripts only on role editor page.

	if ( apply_filters( 'et_pb_load_roles_admin_hook', 'divi_page_et_divi_role_editor' ) !== $hook ) {
		return;
	}

	et_core_load_main_fonts();
	wp_enqueue_style( 'builder-roles-editor-styles', ET_BUILDER_URI . '/styles/roles_style.css', array( 'et-core-admin' ), ET_BUILDER_VERSION );
	wp_enqueue_script( 'builder-roles-editor-scripts', ET_BUILDER_URI . '/scripts/roles_admin.js', array( 'jquery' ), ET_BUILDER_VERSION, true );
	$pb_roles_options = array(
		'ajaxurl'        => admin_url( 'admin-ajax.php' ),
		'et_roles_nonce' => wp_create_nonce( 'et_roles_nonce' ),
		'modal_title'    => esc_html__( 'Reset Roles', 'et_builder' ),
		'modal_message'  => esc_html__( 'All of your current role settings will be set to defaults. Do you wish to proceed?', 'et_builder' ),
		'modal_yes'      => et_builder_i18n( 'Yes' ),
		'modal_no'       => et_builder_i18n( 'No' ),
	);
	wp_localize_script( 'builder-roles-editor-scripts', 'et_pb_roles_options', $pb_roles_options );
}
add_action( 'admin_enqueue_scripts', 'et_pb_load_roles_admin' );

/**
 * Generates the array of allowed modules in jQuery Array format.
 *
 * @param string $role the user role.
 *
 * @return string
 */
function et_pb_allowed_modules_list( $role = '' ) {
	global $typenow;
	// always return empty array if user doesn't have the edit_posts capability.
	if ( ! current_user_can( 'edit_posts' ) ) {
		return '[]';
	}

	$saved_capabilities = et_pb_get_role_settings();
	$role               = '' === $role ? et_pb_get_current_user_role() : $role;

	$all_modules_array = ET_Builder_Element::get_modules_array( $typenow );

	$saved_modules_capabilities = isset( $saved_capabilities[ $role ] ) ? $saved_capabilities[ $role ] : array();

	$alowed_modules = '[';
	foreach ( $all_modules_array as $module => $module_details ) {
		if ( ! in_array( $module_details['label'], array( 'et_pb_section', 'et_pb_row', 'et_pb_row_inner', 'et_pb_column' ), true ) ) {
			// Add module into the list if it's not saved or if it's saved not with "off" state.
			if ( ! isset( $saved_modules_capabilities[ $module_details['label'] ] ) || ( isset( $saved_modules_capabilities[ $module_details['label'] ] ) && 'off' !== $saved_modules_capabilities[ $module_details['label'] ] ) ) {
				$alowed_modules .= "'" . esc_attr( $module_details['label'] ) . "',";
			}
		}
	}

	$alowed_modules .= ']';

	return $alowed_modules;
}

if ( ! function_exists( 'et_divi_get_post_text_color' ) ) {
	/**
	 * Return text color css class.
	 *
	 * @return mixed|string
	 */
	function et_divi_get_post_text_color() {
		$text_color_class = '';

		$post_format = et_pb_post_format();

		if ( in_array( $post_format, array( 'audio', 'link', 'quote' ), true ) ) {
			$text_color       = get_post_meta( get_the_ID(), '_et_post_bg_layout', true );
			$text_color_class = $text_color ? $text_color : 'light';
			$text_color_class = ' et_pb_text_color_' . $text_color_class;
		}

		return $text_color_class;
	}
}

if ( ! function_exists( 'et_divi_get_post_bg_inline_style' ) ) {
	/**
	 * Return css style attribute that ho;d background color inline style.
	 *
	 * @return string
	 */
	function et_divi_get_post_bg_inline_style() {
		$inline_style = '';

		$post_id = get_the_ID();

		$post_use_bg_color = get_post_meta( $post_id, '_et_post_use_bg_color', true ) ? true : false;

		$bg_color      = get_post_meta( $post_id, '_et_post_bg_color', true );
		$post_bg_color = $bg_color && '' !== $bg_color ? $bg_color : '#ffffff';

		if ( $post_use_bg_color ) {
			$inline_style = sprintf( ' style="background-color: %1$s;"', esc_html( $post_bg_color ) );
		}

		return $inline_style;
	}
}

/**
 * Remove the blockquote from post content.
 *
 * @param string $content post content.
 *
 * @return string|string[]|null
 */
function et_remove_blockquote_from_content( $content ) {
	if ( 'quote' !== et_pb_post_format() ) {
		return $content;
	}

	if ( et_theme_builder_overrides_layout( ET_THEME_BUILDER_BODY_LAYOUT_POST_TYPE ) ) {
		// Do not remove quotes when TB has taken over.
		return $content;
	}

	$content = preg_replace( '/<blockquote(.+?)<\/blockquote>/is', '', $content, 1 );

	return $content;
}
add_filter( 'the_content', 'et_remove_blockquote_from_content' );

/**
 * Register rewrite rule and tag for preview page
 *
 * @return void
 */
function et_pb_register_preview_endpoint() {
	add_rewrite_tag( '%et_pb_preview%', 'true' );
}
add_action( 'init', 'et_pb_register_preview_endpoint', 11 );

/**
 * Flush rewrite rules to fix the issue "preg_match" issue with 2.5
 *
 * @return void
 */
function et_pb_maybe_flush_rewrite_rules() {
	et_builder_maybe_flush_rewrite_rules( '2_5_flush_rewrite_rules' );
}
add_action( 'init', 'et_pb_maybe_flush_rewrite_rules', 9 );

/**
 * Register template for preview page.
 *
 * @param string $template The path of the template to include.
 *
 * @return string path to template file
 */
function et_pb_register_preview_page( $template ) {
	global $wp_query;

	if ( 'true' === $wp_query->get( 'et_pb_preview' ) && isset( $_GET['et_pb_preview_nonce'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		show_admin_bar( false );

		return ET_BUILDER_DIR . 'template-preview.php';
	}

	return $template;
}
add_action( 'template_include', 'et_pb_register_preview_page' );

/**
 * Disable all the dynamic assets for preview page so all the styles can be rendered correctly.
 *
 * @return void
 */
function et_pb_preview_page_disable_dynamic_assets() {
	global $wp_query;

	if ( 'true' === $wp_query->get( 'et_pb_preview' ) && isset( $_GET['et_pb_preview_nonce'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
		// Instruct Shortcode Manager to register/load all modules/shortcodes.
		add_filter( 'et_builder_should_load_all_module_data', '__return_true' );

		// Disable Feature: Dynamic Assets.
		add_filter( 'et_disable_js_on_demand', '__return_true' );
		add_filter( 'et_use_dynamic_css', '__return_false' );
		add_filter( 'et_should_generate_dynamic_assets', '__return_false' );

		// Disable Feature: Critical CSS.
		add_filter( 'et_builder_critical_css_enabled', '__return_false' );

		// Disable Cache in Feature Manager.
		add_filter( 'et_builder_post_feature_cache_enabled', '__return_false' );
	}
}

add_action( 'wp', 'et_pb_preview_page_disable_dynamic_assets' );

if ( ! function_exists( 'et_builder_replace_code_content_entities' ) ) :
	/**
	 * The do_shortcode() replaces square brackers with html entities,
	 * convert them back to make sure js code works ok
	 *
	 * @param string $content post content.
	 */
	function et_builder_replace_code_content_entities( $content ) {
		$content = str_replace( '&#091;', '[', $content );
		$content = str_replace( '&#093;', ']', $content );
		$content = str_replace( '&#215;', 'x', $content );

		return $content;
	}
endif;

if ( ! function_exists( 'et_builder_convert_line_breaks' ) ) :
	/**
	 * We use placeholders to preserve the line-breaks, convert them back to "\n".
	 *
	 * @param string $content content.
	 * @param string $line_breaks_format line break format e.g \n or <br>.
	 */
	function et_builder_convert_line_breaks( $content, $line_breaks_format = "\n" ) {

		// before we swap out the placeholders,
		// remove all the <p> tags and \n that wpautop added!
		$content = preg_replace( '/\n/smi', '', $content );
		$content = preg_replace( '/<p>/smi', '', $content );
		$content = preg_replace( '/<\/p>/smi', '', $content );

		$content = str_replace( array( '<!– [et_pb_line_break_holder] –>', '<!-- [et_pb_line_break_holder] -->', '||et_pb_line_break_holder||' ), $line_breaks_format, $content );
		$content = str_replace( '<!–- [et_pb_br_holder] -–>', '<br />', $content );

		// convert the <pee tags back to <p
		// see et_pb_prep_code_module_for_wpautop().
		$content = str_replace( '<pee', '<p', $content );
		$content = str_replace( '</pee>', '</p> ', $content );

		return $content;
	}
endif;

/**
 * Adjust the number of all layouts displayed on library page to exclude predefined layouts.
 *
 * @param object $counts An object containing the current post_type's post
 *                       counts by status.
 *
 * @return mixed
 */
function et_pb_fix_count_library_items( $counts ) {
	// do nothing if get_current_screen function doesn't exists at this point to avoid php errors in some plugins.
	if ( ! function_exists( 'get_current_screen' ) ) {
		return $counts;
	}

	$current_screen = get_current_screen();

	if ( isset( $current_screen->id ) && 'edit-et_pb_layout' === $current_screen->id && isset( $counts->publish ) ) {
		// perform query to get all the not predefined layouts.
		$query = new WP_Query(
			array(
				'meta_query'     => array(
					array(
						'key'     => '_et_pb_predefined_layout',
						'value'   => 'on',
						'compare' => 'NOT EXISTS',
					),
				),
				'post_type'      => ET_BUILDER_LAYOUT_POST_TYPE,
				'posts_per_page' => '-1',
			)
		);

		// set the $counts->publish = amount of non predefined layouts.
		$counts->publish = isset( $query->post_count ) ? (int) $query->post_count : 0;
	}

	return $counts;
}
add_filter( 'wp_count_posts', 'et_pb_fix_count_library_items' );

/**
 * Mobile settings tabs: Desktop, Table, Smartphone.
 *
 * @return string
 */
function et_pb_generate_mobile_settings_tabs() {
	$mobile_settings_tabs = '<%= window.et_builder.mobile_tabs_output() %>';

	return $mobile_settings_tabs;
}

/**
 * Generates the css code for responsive options.
 *
 * Uses array of values for each device as input parameter and css_selector with property to
 * apply the css
 *
 * @deprecated See ET_Builder_Module_Helper_ResponsiveOptions::instance()->generate_responsive_css().
 *
 * @since 3.23 Deprecated.
 *
 * @param  array  $values_array   All device values.
 * @param  mixed  $css_selector   CSS selector.
 * @param  string $css_property   CSS property.
 * @param  string $function_name  Module slug.
 * @param  string $additional_css Additional CSS.
 */
function et_pb_generate_responsive_css( $values_array, $css_selector, $css_property, $function_name, $additional_css = '' ) {
	if ( ! empty( $values_array ) ) {
		foreach ( $values_array as $device => $current_value ) {
			if ( '' === $current_value ) {
				continue;
			}
			$declaration = '';
			// value can be provided as a string or array in following format - array( 'property_1' => 'value_1', 'property_2' => 'property_2', ... , 'property_n' => 'value_n' ).
			if ( is_array( $current_value ) && ! empty( $current_value ) ) {
				foreach ( $current_value as $this_property => $this_value ) {
					if ( '' === $this_value ) {
						continue;
					}
					$declaration .= sprintf(
						'%1$s: %2$s%3$s',
						$this_property,
						esc_html( et_builder_process_range_value( $this_value, $this_property ) ),
						'' !== $additional_css ? $additional_css : ';'
					);
				}
			} else {
				$declaration = sprintf(
					'%1$s: %2$s%3$s',
					$css_property,
					esc_html( et_builder_process_range_value( $current_value, $css_property ) ),
					'' !== $additional_css ? $additional_css : ';'
				);
			}
			if ( '' === $declaration ) {
				continue;
			}
			$style = array(
				'selector'    => $css_selector,
				'declaration' => $declaration,
			);
			if ( 'desktop_only' === $device ) {
				$style['media_query'] = ET_Builder_Element::get_media_query( 'min_width_981' );
			} elseif ( 'desktop' !== $device ) {
				$current_media_query  = 'tablet' === $device ? 'max_width_980' : 'max_width_767';
				$style['media_query'] = ET_Builder_Element::get_media_query( $current_media_query );
			}
			ET_Builder_Element::set_style( $function_name, $style );
		}
	}
}

/**
 * Search module: search the posts.
 *
 * @param mixed $query search WP_Query object.
 */
function et_pb_custom_search( $query = false ) {
	if ( is_admin() || ! is_a( $query, 'WP_Query' ) || ! $query->is_search ) {
		return;
	}

	$utils = ET_Core_Data_Utils::instance();

	// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.
	if ( isset( $_GET['et_pb_searchform_submit'] ) ) {
		$post_types = array();
		if ( ! isset( $_GET['et_pb_include_posts'] ) && ! isset( $_GET['et_pb_include_pages'] ) ) {
			$post_types = array( 'post' );
		}
		if ( isset( $_GET['et_pb_include_pages'] ) ) {
			$post_types = array( 'page' );
		}
		if ( isset( $_GET['et_pb_include_posts'] ) ) {
			$post_types[] = 'post';
		}

		// $postTypes is allowlisted values only
		$query->set( 'post_type', $post_types );

		if ( ! empty( $_GET['et_pb_search_cat'] ) ) {
			// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- Search categories sanitization has been done below on each cat.
			$categories_array = explode( ',', $_GET['et_pb_search_cat'] );
			$categories_array = $utils->sanitize_text_fields( $categories_array );
			$query->set( 'category__not_in', $categories_array );
		}

		if ( isset( $_GET['et-posts-count'] ) ) {
			$query->set( 'posts_per_page', (int) $_GET['et-posts-count'] );
		}
	}
	// phpcs:enable
}
add_action( 'pre_get_posts', 'et_pb_custom_search' );

if ( ! function_exists( 'et_custom_comments_display' ) ) :
	/**
	 * Custom callback function to control the look of the comment.
	 *
	 * @param WP_Comment $comment comment object.
	 * @param WP_Comment $args args.
	 * @param WP_Comment $depth comment depth.
	 */
	function et_custom_comments_display( $comment, $args, $depth ) {
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- Set the current comment.
		$GLOBALS['comment'] = $comment;

		$default_avatar = get_option( 'avatar_default' ) ? get_option( 'avatar_default' ) : 'mystery';
		?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment-body clearfix">
			<div class="comment_avatar">
					<?php echo get_avatar( $comment, $size = '80', esc_attr( $default_avatar ), esc_attr( get_comment_author() ) ); ?>
			</div>

			<div class="comment_postinfo">
					<?php printf( '<span class="fn">%s</span>', get_comment_author_link() ); ?>
				<span class="comment_date">
					<?php
					/* translators: 1: date, 2: time */
					printf( esc_html__( 'on %1$s at %2$s', 'et_builder' ), esc_html( get_comment_date() ), esc_html( get_comment_time() ) );
					?>
				</span>
					<?php edit_comment_link( esc_html__( '(Edit)', 'et_builder' ), ' ' ); ?>
				<?php
				$comment_reply_link_args = array(
					'reply_text' => esc_html__( 'Reply', 'et_builder' ),
					'depth'      => (int) $depth,
					'max_depth'  => (int) $args['max_depth'],
				);
				$et_comment_reply_link   = get_comment_reply_link( array_merge( $args, $comment_reply_link_args ) );
				?>
			</div>

			<div class="comment_area">
				<?php if ( '0' === $comment->comment_approved ) : ?>
					<em class="moderation"><?php esc_html_e( 'Your comment is awaiting moderation.', 'et_builder' ); ?></em>
					<br />
				<?php endif; ?>

				<div class="comment-content clearfix">
				<?php
					comment_text();
				if ( $et_comment_reply_link ) {
					echo '<span class="reply-container">' . et_core_esc_previously( $et_comment_reply_link ) . '</span>';
				}
				?>
				</div>
			</div>
		</article>
		<?php
	}
endif;

/**
 * Exclude library related taxonomies from Yoast SEO Sitemap.
 *
 * @param array  $value excluded taxonomies.
 * @param string $taxonomy taxonomy name.
 *
 * @return bool
 */
function et_wpseo_sitemap_exclude_taxonomy( $value, $taxonomy ) {
	$excluded = array( 'scope', 'module_width', 'layout_type', 'layout_category', 'layout', 'layout_pack' );

	if ( in_array( $taxonomy, $excluded, true ) ) {
		return true;
	}

	return false;
}
add_filter( 'wpseo_sitemap_exclude_taxonomy', 'et_wpseo_sitemap_exclude_taxonomy', 10, 2 );

if ( ! function_exists( 'et_is_wp_job_manager_plugin_active' ) ) :
	/**
	 * Is WP Job Manager plugin active?
	 *
	 * @return bool  True - if the plugin is active
	 */
	function et_is_wp_job_manager_plugin_active() {
		return class_exists( 'WP_Job_Manager' );
	}
endif;

if ( ! function_exists( 'et_is_gutenberg_active' ) ) :
	/**
	 * Is Gutenberg active?
	 *
	 * @deprecated See {@see et_core_is_gutenberg_active()}
	 *
	 * @since 3.19.2 Renamed and moved to core.
	 * @since 3.18
	 *
	 * @return bool  True - if the plugin is active
	 */
	function et_is_gutenberg_active() {
		return et_core_is_gutenberg_active();
	}
endif;

if ( ! function_exists( 'et_is_gutenberg_enabled' ) ) :
	/**
	 * Is Gutenberg active and enabled for the current post
	 * WP 5.0 WARNING - don't use before global post has been set
	 *
	 * @deprecated See {@see et_core_is_gutenberg_enabled()}
	 *
	 * @since 3.19.2 Renamed and moved to core.
	 * @since 3.18
	 *
	 * @return bool  True - if the plugin is active and enabled.
	 */
	function et_is_gutenberg_enabled() {
		return et_core_is_gutenberg_enabled();
	}
endif;

/**
 * Modify comment count for preview screen. Somehow WordPress' get_comments_number() doesn't get correct $post_id
 * param and doesn't have proper fallback to global $post if $post_id variable isn't found. This causes incorrect
 * comment count in preview screen
 *
 * @see get_comments_number()
 * @see get_comments_number_text()
 * @see comments_number()
 *
 * @param integer $count comment count.
 * @param integer $post_id post id.
 * @return string
 */
function et_pb_preview_comment_count( $count, $post_id ) {
	if ( is_et_pb_preview() ) {
		global $post;
		$count = isset( $post->comment_count ) ? $post->comment_count : $count;
	}

	return $count;
}
add_filter( 'get_comments_number', 'et_pb_preview_comment_count', 10, 2 );

/**
 * List of shortcodes that triggers error if being used in admin
 *
 * @return array shortcode tag
 */
function et_pb_admin_excluded_shortcodes() {
	$shortcodes = array();

	// Triggers issue if Sensei and YOAST SEO are activated.
	if ( et_is_yoast_seo_plugin_active() && function_exists( 'Sensei' ) ) {
		$shortcodes[] = 'usercourses';
	}

	// WPL real estate prints unwanted on-page JS that caused an issue on BB.
	if ( class_exists( 'wpl_extensions' ) ) {
		$shortcodes[] = 'WPL';
	}

	// [submit_job_form] shortcode prints wp_editor this creating problems post edit page render
	if ( et_is_wp_job_manager_plugin_active() ) {
		$shortcodes[] = 'submit_job_form';
	}

	// [shop_messages] shortcode causes a fatal error when rendered too soon
	if ( et_is_woocommerce_plugin_active() ) {
		$shortcodes[] = 'shop_messages';
	}

	return apply_filters( 'et_pb_admin_excluded_shortcodes', $shortcodes );
}

/**
 * Get GMT offset string that can be used for parsing date into correct timestamp
 *
 * @return string
 */
function et_pb_get_gmt_offset_string() {
	$gmt_offset        = get_option( 'gmt_offset' );
	$gmt_divider       = '-' === substr( $gmt_offset, 0, 1 ) ? '-' : '+';
	$gmt_offset_hour   = str_pad( abs( intval( $gmt_offset ) ), 2, '0', STR_PAD_LEFT );
	$gmt_offset_minute = str_pad( ( ( abs( $gmt_offset ) * 100 ) % 100 ) * ( 60 / 100 ), 2, '0', STR_PAD_LEFT );
	$gmt_offset_string = "GMT{$gmt_divider}{$gmt_offset_hour}{$gmt_offset_minute}";

	return $gmt_offset_string;
}

/**
 * Get post's category label and permalink to be used on frontend
 *
 * @param int       $post_id post id.
 * @param WP_Term[] $default term objects.
 *
 * @return array categories
 */
function et_pb_get_post_categories( $post_id, $default = array() ) {
	$categories      = get_the_category( $post_id );
	$post_categories = array();

	if ( $default && ! $categories ) {
		$categories = array_values( $default );

		foreach ( array_keys( $categories ) as $key ) {
			_make_cat_compat( $categories[ $key ] );
		}
	}

	// Filter out any falsy values that may appear due from $default.
	$categories = array_filter( $categories );

	if ( ! empty( $categories ) ) {
		foreach ( $categories as $category ) {
			$post_categories[ $category->cat_ID ] = array(
				'id'        => $category->cat_ID,
				'label'     => $category->cat_name,
				'permalink' => get_category_link( $category->cat_ID ),
			);
		}
	}

	return $post_categories;
}

/**
 * Generates "Use Visual Builder" button url
 *
 * @return string
 */
function et_fb_get_page_url() {
	global $wp;

	$post_id         = get_the_ID();
	$is_divi_library = 'et_pb_layout' === get_post_type( $post_id );

	if ( $is_divi_library ) {
		return get_edit_post_link( $post_id );
	} elseif ( is_singular() ) {
		return get_permalink( $post_id );
	}

	return add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
}

/**
 * Add "Use Visual Builder" link to WP-Admin bar
 *
 * @return void
 */
function et_fb_add_admin_bar_link() {
	$is_not_builder_enabled_single   = ! is_singular() || ! et_builder_fb_enabled_for_post( get_the_ID() );
	$is_not_in_wc_shop               = ! et_builder_used_in_wc_shop();
	$not_allowed_fb_access           = ! et_pb_is_allowed( 'use_visual_builder' );
	$app_preferences                 = et_fb_app_preferences_settings();
	$default_visual_theme_builder    = et_()->array_get( $app_preferences, 'enable_visual_theme_builder.default' );
	$is_visual_theme_builder_enabled = et_get_option( 'et_fb_pref_builder_enable_visual_theme_builder', $default_visual_theme_builder );
	$is_not_theme_builder_used       = ! et_fb_is_theme_builder_used_on_page() || ! et_pb_is_allowed( 'theme_builder' ) || ! filter_var( $is_visual_theme_builder_enabled, FILTER_VALIDATE_BOOLEAN );
	$is_enabled_for_post_type        = ! is_singular() || et_builder_enabled_for_post_type( get_post_type( get_the_ID() ) );

	// Return if builder is not allowed for the  user.
	if ( $not_allowed_fb_access ) {
		return;
	}

	// Return if builder is not enabled for the post type.
	if ( ! $is_enabled_for_post_type && $is_not_builder_enabled_single ) {
		return;
	}

	// Return for non-singular pages if they are not WC shop page and Theme Builder is not used.
	if ( $is_not_builder_enabled_single && $is_not_in_wc_shop && $is_not_theme_builder_used ) {
		return;
	}

	global $wp_admin_bar, $wp_the_query;

	$post_id = get_the_ID();

	// WooCommerce Shop Page replaces main query, thus it has to be normalized.
	if ( et_builder_used_in_wc_shop() && method_exists( $wp_the_query, 'get_queried_object' ) && isset( $wp_the_query->get_queried_object()->ID ) ) {
		$post_id = $wp_the_query->get_queried_object()->ID;
	}

	$page_url = et_fb_get_page_url();

	// Don't add the link, if Frontend Builder has been loaded already.
	if ( et_fb_is_enabled() || et_fb_is_enabled_on_any_template() ) {
		$wp_admin_bar->add_menu(
			array(
				'id'    => 'et-disable-visual-builder',
				'title' => esc_html__( 'Exit Visual Builder', 'et_builder' ),
				'href'  => esc_url( $page_url ),
			)
		);

		return;
	}

	$current_object = $wp_the_query->get_queried_object();

	$user_is_allowed_to_edit = isset( $current_object->ID ) ? current_user_can( 'edit_post', $current_object->ID ) : ( et_fb_is_theme_builder_used_on_page() && et_pb_is_allowed( 'theme_builder' ) );

	if ( is_admin() || ! $user_is_allowed_to_edit || ! et_pb_is_allowed( 'divi_builder_control' ) ) {
		return;
	}

	$use_visual_builder_url = et_pb_is_pagebuilder_used( $post_id ) || ( et_fb_is_theme_builder_used_on_page() && ! is_singular() ) ?
		et_fb_get_builder_url( $page_url ) :
		add_query_arg(
			array(
				'et_fb_activation_nonce' => wp_create_nonce( 'et_fb_activation_nonce_' . $post_id ),
			),
			$page_url
		);

	$wp_admin_bar->add_menu(
		array(
			'id'    => 'et-use-visual-builder',
			'title' => esc_html__( 'Enable Visual Builder', 'et_builder' ),
			'href'  => esc_url( $use_visual_builder_url ),
		)
	);
}
add_action( 'admin_bar_menu', 'et_fb_add_admin_bar_link', 999 );

/**
 * Retrieve and process saved Layouts.
 * It different than the function which retrieves saved Sections, Rows and Modules from library because layouts require different processing
 */
function et_fb_get_saved_layouts() {
	if ( ! isset( $_POST['et_fb_retrieve_library_modules_nonce'] ) || ! wp_verify_nonce( $_POST['et_fb_retrieve_library_modules_nonce'], 'et_fb_retrieve_library_modules_nonce' ) ) { // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- The nonce value is used only for comparision in the `wp_verify_nonce`.
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	// Reduce number of results per page if we're hosted on wpengine to avoid 500 error due to memory allocation.
	// This is caused by one of their custom mu-plugins doing additional stuff but we have no control over there.
	$page_size    = function_exists( 'is_wpe' ) || function_exists( 'is_wpe_snapshot' ) ? 25 : 50;
	$post_type    = ! empty( $_POST['et_post_type'] ) ? sanitize_text_field( $_POST['et_post_type'] ) : 'post';
	$layouts_type = ! empty( $_POST['et_load_layouts_type'] ) ? sanitize_text_field( $_POST['et_load_layouts_type'] ) : 'all';
	$start_from   = ! empty( $_POST['et_templates_start_page'] ) ? sanitize_text_field( $_POST['et_templates_start_page'] ) : 0;

	$post_type = apply_filters( 'et_pb_show_all_layouts_built_for_post_type', $post_type, $layouts_type );

	$all_layouts_data           = et_pb_retrieve_templates( 'layout', '', 'false', '0', $post_type, $layouts_type, array( $start_from, $page_size ) );
	$all_layouts_data_processed = $all_layouts_data;
	$next_page                  = 'none';

	if ( 0 !== $start_from && empty( $all_layouts_data ) ) {
		$all_layouts_data_processed = array();
	} else {
		if ( empty( $all_layouts_data ) ) {
			$all_layouts_data_processed = array( 'error' => esc_html__( 'You have not saved any items to your Divi Library yet. Once an item has been saved to your library, it will appear here for easy use.', 'et_builder' ) );
		} else {
			foreach ( $all_layouts_data as $index => $data ) {
				$all_layouts_data_processed[ $index ]['shortcode'] = et_fb_process_shortcode( $data['shortcode'] );
			}
			$next_page = $start_from + $page_size;
		}
	}

	$json_templates = wp_json_encode(
		array(
			'templates_data' => $all_layouts_data_processed,
			'next_page'      => $next_page,
		)
	);

	die( et_core_intentionally_unescaped( $json_templates, 'html' ) );
}

add_action( 'wp_ajax_et_fb_get_saved_layouts', 'et_fb_get_saved_layouts' );

/**
 * Ajax Callback: Process imported content.
 */
function et_fb_process_imported_content() {
	if ( ! isset( $_POST['et_fb_process_imported_data_nonce'] ) || ! wp_verify_nonce( $_POST['et_fb_process_imported_data_nonce'], 'et_fb_process_imported_data_nonce' ) ) { // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- The nonce value is used only for comparision in the `wp_verify_nonce`.
		die( -1 );
	}

	if ( ! current_user_can( 'edit_posts' ) ) {
		die( -1 );
	}

	// phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- Value in `$_POST['et_raw_shortcode']` is processed by `et_fb_process_shortcode` and being returned in ajax response.
	$processed_shortcode = isset( $_POST['et_raw_shortcode'] ) ? et_fb_process_shortcode( stripslashes( $_POST['et_raw_shortcode'] ) ) : '';

	die( wp_json_encode( $processed_shortcode ) );
}
add_action( 'wp_ajax_et_fb_process_imported_content', 'et_fb_process_imported_content' );

/**
 * Builder initial content.
 *
 * @param string  $content post content.
 * @param integer $post_id post id.
 *
 * @return string
 */
function et_fb_maybe_get_bfb_initial_content( $content, $post_id ) {
	$from_post = isset( $_GET['from_post'] ) ? sanitize_text_field( $_GET['from_post'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification -- This function does not change any state, and is therefore not susceptible to CSRF.

	if ( ! empty( $from_post ) && 'empty' !== $from_post ) {
		$copy_content_from = get_post( $from_post );
		$existing_content  = $copy_content_from->post_content;

		if ( '' !== $existing_content && has_shortcode( $existing_content, 'et_pb_section' ) ) {
			return $existing_content;
		}
	}

	// process the content only for BFB.
	if ( ! et_builder_bfb_enabled() ) {
		return $content;
	}

	// If content already has a section, it means builder is active and activation has to be
	// skipped to avoid nested and unwanted builder structure.
	if ( has_shortcode( $content, 'et_pb_section' ) ) {
		return $content;
	}

	// Save old content.
	$saved_old_content = get_post_meta( $post_id, '_et_pb_old_content', true );
	$save_old_content  = false;
	$post              = get_post( $post_id );

	if ( '' !== $content ) {
		$save_old_content = update_post_meta( $post_id, '_et_pb_old_content', $content );
	}

	/**
	 * Filters the flag that sets default Content during Builder activation.
	 *
	 * @since 3.29
	 *
	 * @used-by et_builder_wc_init()
	 */
	if ( apply_filters( 'et_builder_skip_content_activation', false, $post ) ) {
		return $content;
	}

	if ( true !== $save_old_content && $saved_old_content !== $content && '' !== $content ) {
		return $content;
	}

	$text_module = '' !== $content ? '[et_pb_text admin_label="Text"]' . $content . '[/et_pb_text]' : '';

	// Re-format content.
	$updated_content =
		'[et_pb_section admin_label="section"]
			[et_pb_row admin_label="row"]
				[et_pb_column type="4_4"]' . $text_module . '[/et_pb_column]
			[/et_pb_row]
		[/et_pb_section]';

	return $updated_content;
}

/**
 * Called via async AJAX call after the builder rendered. It will regenerate both helper/definitions files.
 * If their content changed, the builder will trigger a page reload to use the updated cached files.
 */
function et_fb_update_builder_assets() {
	if ( ! isset( $_POST['et_fb_helper_nonce'] ) || ! wp_verify_nonce( $_POST['et_fb_helper_nonce'], 'et_fb_update_helper_assets_nonce' ) ) { // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- The nonce value is used only for comparision in the `wp_verify_nonce`.
		die( -1 );
	}

	$post_id = ! empty( $_POST['et_post_id'] ) ? sanitize_text_field( $_POST['et_post_id'] ) : '';

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		die( -1 );
	}

	// Set current post as global $post.
	$post = get_post( $post_id ); // phpcs:ignore WordPress.Variables.GlobalVariables.OverrideProhibited

	$post_type = ! empty( $_POST['et_post_type'] ) ? sanitize_text_field( $_POST['et_post_type'] ) : 'post';

	// Update helpers cached js file.
	$helpers = et_fb_get_dynamic_asset( 'helpers', $post_type, true );
	// Update definitions cached js file.
	$definitions = et_fb_get_dynamic_asset( 'definitions', $post_type, true );

	// When either definitions or helpers needs an update, also clear modules cache.
	if ( $definitions['updated'] || $helpers['updated'] ) {
		$modules_cache = ET_Builder_Element::get_cache_filename( $post_type );

		if ( file_exists( $modules_cache ) ) {
			@unlink( $modules_cache ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged -- `unlink` may fail with the permissions denied error.
		}
	}

	die(
		wp_json_encode(
			array(
				'helpers'     => $helpers,
				'definitions' => $definitions,
			)
		)
	);
}
add_action( 'wp_ajax_et_fb_update_builder_assets', 'et_fb_update_builder_assets' );

/**
 * Returns builder definitions.
 *
 * @param string $post_type post type.
 *
 * @return array
 */
function et_fb_get_builder_definitions( $post_type ) {

	// force render builder data when retrieving builder definition to ensure definitions retrieved via ajax call
	// equal to definitions retrieved on wp_footer when no dynamic asset cache found.
	add_filter( 'et_builder_module_force_render', '__return_true' );

	$fields_data                                = array();
	$fields_data['custom_css']                  = ET_Builder_Element::get_custom_css_fields( $post_type );
	$fields_data['advanced_fields']             = ET_Builder_Element::get_advanced_fields( $post_type );
	$fields_data['general_fields']              = ET_Builder_Element::get_general_fields( $post_type );
	$fields_data['childModuleTitles']           = ET_Builder_Element::get_child_module_titles( $post_type );
	$fields_data['optionsToggles']              = ET_Builder_Element::get_toggles( $post_type );
	$fields_data['customTabs']                  = ET_Builder_Element::get_tabs( $post_type );
	$fields_data['customTabsFields']            = ET_Builder_Element::get_settings_modal_tabs_fields( $post_type );
	$fields_data['customLayoutsTabs']           = ET_Builder_Library::builder_library_modal_custom_tabs( $post_type );
	$fields_data['moduleItemsConfig']           = ET_Builder_Element::get_module_items_configs( $post_type );
	$fields_data['moduleTransitions']           = ET_Builder_Element::get_modules_transitions( $post_type );
	$fields_data['contact_form_input_defaults'] = et_fb_process_shortcode(
		sprintf(
			'[et_pb_contact_field field_title="%1$s" field_type="input" field_id="Name" required_mark="on" fullwidth_field="off" /][et_pb_contact_field field_title="%2$s" field_type="email" field_id="Email" required_mark="on" fullwidth_field="off" /][et_pb_contact_field field_title="%3$s" field_type="text" field_id="Message" required_mark="on" fullwidth_field="on" /]',
			esc_attr__( 'Name', 'et_builder' ),
			esc_attr__( 'Email Address', 'et_builder' ),
			esc_attr__( 'Message', 'et_builder' )
		)
	);

	// Remove duplicates from field definitions.
	$map           = array();
	$unique_fields = array();
	$unique_count  = 0;

	foreach ( array( 'custom_css', 'general_fields', 'advanced_fields' ) as $source ) {
		$definitions  = &$fields_data[ $source ];
		$module_names = array_keys( $definitions );

		foreach ( $module_names as $module_name ) {
			$module        = &$definitions[ $module_name ];
			$setting_names = array_keys( $module );

			foreach ( $setting_names as $setting_name ) {
				$setting = &$module[ $setting_name ];

				if ( 'advanced_defaults' === $setting_name ) {
					// advanced_defaults are just duplicated data, we can rebuilt them later.
					$setting = false;
					continue;
				}

				$key = wp_json_encode( $setting );

				if ( ! isset( $map[ $key ] ) ) {
					// Found a duplicate here.
					$unique_fields[] = $setting;
					$map[ $key ]     = $unique_count++;
				}

				$setting = $map[ $key ];
			}
		}
	}

	// Remove force builder data render.
	remove_filter( 'et_builder_module_force_render', '__return_true' );

	// No longer needed.
	unset( $map );

	// Include the unique fields in the AJAX payload.
	$fields_data['unique_fields'] = $unique_fields;

	return $fields_data;
}

/**
 * Returns builder shortcode object.
 *
 * @param string  $post_type the post type.
 * @param integer $post_id the post id.
 * @param string  $layout_type layout type.
 *
 * @return array
 */
function et_fb_get_builder_shortcode_object( $post_type, $post_id, $layout_type ) {

	// We need to store the current post when this function is executed in a wp-admin page
	// to prevent post based modules included in the shortcode from altering the loop.
	global $post;
	$backup = $post;

	$fields_data = array();
	add_filter( 'et_builder_module_force_render', '__return_true' );

	$post_data               = get_post( $post_id );
	$post_data_post_modified = is_object( $post_data ) ? gmdate( 'U', strtotime( $post_data->post_modified ) ) : 0;
	$post_content            = is_object( $post_data ) ? $post_data->post_content : '';
	$is_theme_builder        = et_builder_tb_enabled();
	$is_backend_builder      = et_builder_bfb_enabled();
	$theme_builder_layouts   = et_theme_builder_get_template_layouts();

	// Unset main template from Theme Builder layouts to avoid PHP Notices.
	if ( isset( $theme_builder_layouts['et_template'] ) ) {
		unset( $theme_builder_layouts['et_template'] );
	}

	// Get the content for all theme builder posts.
	foreach ( $theme_builder_layouts as $key => $theme_builder_layout ) {
		if ( 0 !== $theme_builder_layout['id'] && $theme_builder_layout['enabled'] && $theme_builder_layout['override'] ) {
			$post_data                                     = get_post( $theme_builder_layout['id'] );
			$theme_builder_layouts[ $key ]['post_content'] = $post_data->post_content;
		}
	}

	// if autosave exists here, return it with the real content, autosave.js and getServerSavedPostData() will look for it.
	$current_user_id = get_current_user_id();
	// Store one autosave per author. If there is already an autosave, overwrite it.
	$autosave = wp_get_post_autosave( $post_id, $current_user_id );

	if ( ! empty( $autosave ) ) {
		$autosave_post_modified = gmdate( 'U', strtotime( $autosave->post_modified ) );

		if ( $autosave_post_modified > $post_data_post_modified ) {
			$fields_data['autosave_shortcode_object'] = et_fb_process_shortcode( $autosave->post_content );
			$fields_data['has_newer_autosave']        = true;
		} else {
			$fields_data['has_newer_autosave'] = false;
		}
		// Delete the autosave, becuase we will present the option to use the autosave to the user, and they will use it or not
		// we need to delete the db copy now.
		wp_delete_post_revision( $autosave->ID );
	}

	switch ( $layout_type ) {
		case 'module':
			$use_fullwidth_section = false !== strpos( $post_content, '[et_pb_fullwidth_' ) ? true : false;
			// Remove module placeholders.
			$post_content = false !== strpos( $post_content, 'et_pb_fullwidth_module_placeholder' ) || false !== strpos( $post_content, 'et_pb_module_placeholder' ) ? '' : $post_content;

			if ( ! $use_fullwidth_section ) {
				$post_content = sprintf( '[et_pb_row][et_pb_column type="4_4"]%1$s[/et_pb_column][/et_pb_row]', $post_content );
			}

			$post_content = sprintf(
				'[et_pb_section%2$s]%1$s[/et_pb_section]',
				$post_content,
				$use_fullwidth_section ? ' fullwidth="on"' : ''
			);

			break;
		case 'row':
			$post_content = '[et_pb_section]' . $post_content . '[/et_pb_section]';
			break;
	}

	$post_content = et_fb_maybe_get_bfb_initial_content( $post_content, $post_id );

	/**
	 * Filters the raw post content when the Builder is loaded.
	 *
	 * @since 3.29
	 *
	 * @param string $post_content The raw/unprocessed post content.
	 * @param int $post_id Post ID.
	 */
	$post_content = apply_filters( 'et_fb_load_raw_post_content', $post_content, $post_id );

	$fields_data['shortcode_object'] = array();

	// Process main post content fields data.
	$post_fields_data = et_fb_process_shortcode( $post_content );

	$fields_data['shortcode_object'] = array();

	// In Visual Builder get All Theme Builder Areas.
	if ( et_pb_is_allowed( 'theme_builder' ) && ! $is_theme_builder && ! $is_backend_builder && ! empty( $theme_builder_layouts ) ) {
		// Process Theme Builder Header area fields data.
		if ( isset( $theme_builder_layouts['et_header_layout']['post_content'] ) ) {
			$theme_builder_header    = apply_filters( 'et_fb_load_raw_post_content', $theme_builder_layouts['et_header_layout']['post_content'], $theme_builder_layouts['et_header_layout']['id'] );
			$tb_header_fields_data   = et_fb_process_shortcode( $theme_builder_header, '', '', '', '', 'et_header_layout' );
			$processed_fields_data[] = $tb_header_fields_data;
		}

		// Process Theme Builder Body area.
		if ( isset( $theme_builder_layouts['et_body_layout']['post_content'] ) ) {
			$theme_builder_body                  = apply_filters( 'et_fb_load_raw_post_content', $theme_builder_layouts['et_body_layout']['post_content'], $theme_builder_layouts['et_body_layout']['id'] );
			$tb_body_fields_data                 = et_fb_process_shortcode( $theme_builder_body, '', '', '', '', 'et_body_layout' );
			$theme_builder_post_content_selector = et_fb_generate_post_content_module_selector( $tb_body_fields_data, 'theme_builder_content' );

			if ( $theme_builder_post_content_selector && ! is_home() && ! is_archive() && ! is_404() ) {
				// If Theme Builder Body contains Post Content Module, replace it with real post content.
				$post_content_fields_data = et_fb_process_shortcode( $post_content, '', '', '', '', 'post_content' );
				$processed_fields_data[]  = et_fb_generate_tb_body_area_with_post_content( $tb_body_fields_data, $theme_builder_post_content_selector, $post_content_fields_data );
			} else {
				// if not, just add Theme Builder Body area content.
				$processed_fields_data[] = $tb_body_fields_data;

				// Add Post content too, so it can be loaded when post content module is added.
				if ( ! is_home() && ! is_archive() && ! is_404() ) {
					$initial_post_content_fields_data = et_fb_process_shortcode( $post_content, '', '', '', '', 'initial_post_content' );
					$processed_fields_data[]          = $initial_post_content_fields_data;
				}
			}
		} elseif ( ! is_home() && et_pb_is_pagebuilder_used( $post_id ) ) {
			// If there is no Theme Builder Body, load post content.
			$processed_fields_data[] = $post_fields_data;
		}

		// Process Theme Builder Header area fields data.
		if ( isset( $theme_builder_layouts['et_footer_layout']['post_content'] ) ) {
			$theme_builder_footer    = apply_filters( 'et_fb_load_raw_post_content', $theme_builder_layouts['et_footer_layout']['post_content'], $theme_builder_layouts['et_footer_layout']['id'] );
			$tb_footer_fields_data   = et_fb_process_shortcode( $theme_builder_footer, '', '', '', '', 'et_footer_layout' );
			$processed_fields_data[] = $tb_footer_fields_data;
		}

		// Build the shortcode_object from Theme Builder Areas.
		foreach ( $processed_fields_data as $processed_field_data ) {
			if ( is_array( $processed_field_data ) ) {
				$fields_data['shortcode_object'] = array_merge( $fields_data['shortcode_object'], $processed_field_data );
			}
		}
	} else {
		// In Theme Builder and Backend Builder show only main post.
		$fields_data['shortcode_object'] = $post_fields_data;
	}

	remove_filter( 'et_builder_module_force_render', '__return_true' );

	// Restore post.
	$post = $backup; // phpcs:ignore WordPress.WP.GlobalVariablesOverride -- This is legit way of setting global $post.
	setup_postdata( $post );

	return $fields_data;
}

/**
 * This function searches the multidimensional array of post content fields for post content module
 * and generates array of keys that are used to select post content module.
 *
 * @param array  $array Array of post fields data.
 * @param string $element_type Type of an element that is analyzes at the moment.
 *
 * @return array
 */
function et_fb_generate_post_content_module_selector( array $array, $element_type ) {
	global $current_section;
	global $current_row;
	global $current_row_inner;
	global $current_column;
	global $current_column_inner;
	global $current_module;
	global $post_content_module_selector;

	if ( 'theme_builder_content' === $element_type ) {
		// Loop through Theme Builder Area sections.
		foreach ( $array as $key => $value ) {
			if ( isset( $array[ $key ]['content'] ) && is_array( $array[ $key ]['content'] ) ) {
				$current_section = $key;
				et_fb_generate_post_content_module_selector( $array[ $key ]['content'], $array[ $key ]['type'] );
			}
		}
	} elseif ( 'et_pb_section' === $element_type ) {
		// Loop through rows.
		foreach ( $array as $key => $value ) {
			if ( 'et_pb_fullwidth_post_content' === $array[ $key ]['type'] ) {
				et_fb_generate_post_content_module_selector( $array, 'et_pb_column' );
			} elseif ( isset( $array[ $key ]['content'] ) && is_array( $array[ $key ]['content'] ) ) {
				$current_row = $key;
				et_fb_generate_post_content_module_selector( $array[ $key ]['content'], $array[ $key ]['type'] );
			}
		}
	} elseif ( 'et_pb_row' === $element_type ) {
		// Loop through columns.
		foreach ( $array as $key => $value ) {
			if ( isset( $array[ $key ]['content'] ) && is_array( $array[ $key ]['content'] ) ) {
				$current_column = $key;
				et_fb_generate_post_content_module_selector( $array[ $key ]['content'], $array[ $key ]['type'] );
			}
		}
	} elseif ( 'et_pb_row_inner' === $element_type ) {
		// Loop through columns.
		foreach ( $array as $key => $value ) {
			if ( isset( $array[ $key ]['content'] ) && is_array( $array[ $key ]['content'] ) ) {
				$current_column_inner = $key;
				et_fb_generate_post_content_module_selector( $array[ $key ]['content'], $array[ $key ]['type'] );
			}
		}
	} elseif ( 'et_pb_column' === $element_type || 'et_pb_column_inner' === $element_type ) {
		// Loop through modules.
		foreach ( $array as $key => $value ) {
			if ( 'et_pb_row_inner' === $array[ $key ]['type'] ) {
				foreach ( $array as $key => $value ) {
					if ( isset( $array[ $key ]['content'] ) && is_array( $array[ $key ]['content'] ) ) {
						$current_row_inner = $key;
						et_fb_generate_post_content_module_selector( $array[ $key ]['content'], $array[ $key ]['type'] );
					}
				}
			} elseif ( 'et_pb_post_content' === $array[ $key ]['type'] ) {
				// If Post Content Module is Found build the selector from current Section, Row, and Column.
				$current_module  = $key;
				$is_column_inner = 'et_pb_column_inner' === $array[ $key ]['parent_slug'];

				$post_content_module_selector = $is_column_inner ? array(
					'section'      => $current_section,
					'row_inner'    => $current_row_inner,
					'column'       => $current_row,
					'column_inner' => $current_column_inner,
					'module'       => $current_module,
				) : array(
					'section' => $current_section,
					'row'     => $current_row,
					'column'  => $current_column,
					'module'  => $current_module,
				);
			} elseif ( 'et_pb_fullwidth_post_content' === $array[ $key ]['type'] ) {
				// If Post Content Module is FullWidth create selector with section and module id.
				$current_module               = $key;
				$post_content_module_selector = array(
					'section' => $current_section,
					'module'  => $current_module,
				);
			}
		}
	}

	return $post_content_module_selector;
}

/**
 * This function is used to generate the Theme Builder Body area that has Post
 * Content module inside. It replaces Post Content module with sections of a
 * post that is being currently edited.
 *
 * @param array $theme_builder_body_fields Theme Builder Body area fields data.
 * @param array $selector Array of keys for selecting Post Content Module.
 * @param array $post_content_fields Post Content fields data that should
 *     replace the Post Content Module.
 *
 * @return array
 */
function et_fb_generate_tb_body_area_with_post_content( $theme_builder_body_fields, $selector, $post_content_fields ) {
	if ( ! isset( $selector['row'] ) && ! isset( $selector['column'] ) ) {
		$original_post_content_module = $theme_builder_body_fields[ $selector['section'] ]['content'][ $selector['module'] ];

		$theme_builder_body_fields[ $selector['section'] ]['attrs']['post_content_module_attrs']        = $original_post_content_module['attrs'];
		$theme_builder_body_fields[ $selector['section'] ]['content'][ $selector['module'] ]['content'] = $post_content_fields;
	} elseif ( null === $selector['column'] ) {
		$original_post_content_module = $theme_builder_body_fields[ $selector['section'] ]['content'][ $selector['row'] ]['content'][ $selector['module'] ];

		$theme_builder_body_fields[ $selector['section'] ]['content'][ $selector['row'] ]['attrs']['post_content_module_attrs']        = $original_post_content_module['attrs'];
		$theme_builder_body_fields[ $selector['section'] ]['content'][ $selector['row'] ]['content'][ $selector['module'] ]['content'] = $post_content_fields;
	} elseif ( isset( $selector['row_inner'] ) ) {
		$original_post_content_module = $theme_builder_body_fields[ $selector['section'] ]['content'][ $selector['column'] ]['content'][ $selector['row_inner'] ]['content'][ $selector['column_inner'] ]['content'][ $selector['module'] ];

		$theme_builder_body_fields[ $selector['section'] ]['content'][ $selector['column'] ]['content'][ $selector['row_inner'] ]['content'][ $selector['column_inner'] ]['attrs']['post_content_module_attrs']        = $original_post_content_module['attrs'];
		$theme_builder_body_fields[ $selector['section'] ]['content'][ $selector['column'] ]['content'][ $selector['row_inner'] ]['content'][ $selector['column_inner'] ]['content'][ $selector['module'] ]['content'] = $post_content_fields;
	} else {
		$original_post_content_module = $theme_builder_body_fields[ $selector['section'] ]['content'][ $selector['row'] ]['content'][ $selector['column'] ]['content'][ $selector['module'] ];

		$theme_builder_body_fields[ $selector['section'] ]['content'][ $selector['row'] ]['content'][ $selector['column'] ]['attrs']['post_content_module_attrs']        = $original_post_content_module['attrs'];
		$theme_builder_body_fields[ $selector['section'] ]['content'][ $selector['row'] ]['content'][ $selector['column'] ]['content'][ $selector['module'] ]['content'] = $post_content_fields;
	}

	return $theme_builder_body_fields;
}

/**
 * Ajax Callback: Retrieve builder data on frontend app load.
 */
function et_fb_retrieve_builder_data() {
	if ( ! isset( $_POST['et_fb_helper_nonce'] ) || ! wp_verify_nonce( $_POST['et_fb_helper_nonce'], 'et_fb_load_helper_assets_nonce' ) ) { // phpcs:ignore ET.Sniffs.ValidatedSanitizedInput -- The nonce value is used only for comparision in the `wp_verify_nonce`.
		die( -1 );
	}

	$post_id = ! empty( $_POST['et_post_id'] ) ? sanitize_text_field( $_POST['et_post_id'] ) : '';

	if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( 'edit_post', $post_id ) ) {
		die( -1 );
	}

	$post_type = ! empty( $_POST['et_post_type'] ) ? sanitize_text_field( $_POST['et_post_type'] ) : 'post';

	$layout_type = ! empty( $_POST['et_layout_type'] ) ? sanitize_text_field( $_POST['et_layout_type'] ) : '';

	$fields_data = array_merge(
		et_fb_get_builder_definitions( $post_type ),
		et_fb_get_builder_shortcode_object( $post_type, $post_id, $layout_type )
	);

	// Enable zlib compression.
	et_builder_enable_zlib_compression();

	die( wp_json_encode( $fields_data ) );
}
add_action( 'wp_ajax_et_fb_retrieve_builder_data', 'et_fb_retrieve_builder_data' );

/**
 * Replaces site_url in a json string with its protocol-less version.
 *
 * @param string $json The json string that contain site url.
 *
 * @return string
 */
function et_fb_remove_site_url_protocol( $json ) {
	$no_proto = str_replace( '/', '\/', preg_replace( '#^\w+:#', '', get_site_url() ) );
	$from     = array(
		"https:$no_proto" => $no_proto,
		"http:$no_proto"  => $no_proto,
	);
	return strtr( $json, $from );
}

/**
 * Used to update the content of the cached definitions js file.
 *
 * @param string $content content? @todo Add param description.
 * @param string $post_type Post type? @todo Add param description.
 *
 * @return string
 */
function et_fb_get_asset_definitions( $content, $post_type ) {
	$definitions = et_fb_get_builder_definitions( $post_type );
	return sprintf(
		'window.ETBuilderBackend=jQuery.extend(true,%s,window.ETBuilderBackend)',
		et_fb_remove_site_url_protocol( wp_json_encode( $definitions, ET_BUILDER_JSON_ENCODE_OPTIONS ) )
	);
}
add_filter( 'et_fb_get_asset_definitions', 'et_fb_get_asset_definitions', 10, 2 );

/**
 * Return Divi options setting page link.
 *
 * @return mixed|string|void
 */
function et_pb_get_options_page_link() {
	// Builder plugin has different path to options page.
	if ( et_is_builder_plugin_active() ) {
		return admin_url( 'admin.php?page=et_divi_options#tab_et_dashboard_tab_content_api_main' );
	}

	return apply_filters( 'et_pb_theme_options_link', admin_url( 'admin.php?page=et_divi_options' ) );
}

/**
 * Localization: Product tour text.
 *
 * @param integer $post_id The post id to determine the Save/Publish button text from post status.
 *
 * @return array
 */
function et_fb_get_product_tour_text( $post_id ) {
	$post_status = get_post_status( $post_id );

	$product_tour_text = array(
		'start'                 => array(
			'title'          => esc_html__( 'Welcome To The Divi Builder', 'et_builder' ),
			'description'    => sprintf(
				// translators: %10$s: Tour video overlay, %1$s: "Section" - label,  %2$s: Add icon markup, %3$s: "Row" - label, %4$s: Add icon markup, %5$s: "Modules" - label, %6$s: Add icon markup, %7$s: Settings gear icon markup, %9$s: Documentation link markup.
				__( '%10$sBuilding beautiful pages is a breeze using the Visual Builder. To get started, add a new %1$s to your page by pressing the %2$s button. Next, add a %3$s of columns inside your section by pressing the %4$s button. Finally, start adding some content %5$s inside your columns by pressing the %6$s button. You can customize the design and content of any element on the page by pressing the %7$s button. If you ever need help, visit our %9$s page for a full list of tutorials.', 'et_builder' ),
				sprintf( '<span class="et_fb_tour_text et_fb_tour_text_blue">%1$s</span>', esc_html__( 'Section' ) ),
				'<span class="et_fb_tour_icon et_fb_tour_icon_blue"><svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><g><path d="M18 13h-3v-3a1 1 0 0 0-2 0v3h-3a1 1 0 0 0 0 2h3v3a1 1 0 0 0 2 0v-3h3a1 1 0 0 0 0-2z" fillRule="evenodd" /></g></svg></span>',
				sprintf( '<span class="et_fb_tour_text et_fb_tour_text_green">%1$s</span>', esc_html__( 'Row' ) ),
				'<span class="et_fb_tour_icon et_fb_tour_icon_green"><svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><g><path d="M18 13h-3v-3a1 1 0 0 0-2 0v3h-3a1 1 0 0 0 0 2h3v3a1 1 0 0 0 2 0v-3h3a1 1 0 0 0 0-2z" fillRule="evenodd" /></g></svg></span>',
				sprintf( '<span class="et_fb_tour_text et_fb_tour_text_black">%1$s</span>', esc_html__( 'Modules' ) ),
				'<span class="et_fb_tour_icon"><svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><g><path d="M18 13h-3v-3a1 1 0 0 0-2 0v3h-3a1 1 0 0 0 0 2h3v3a1 1 0 0 0 2 0v-3h3a1 1 0 0 0 0-2z" fillRule="evenodd" /></g></svg></span>',
				'<span class="et_fb_tour_icon"><svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><g><path d="M20.426 13.088l-1.383-.362a.874.874 0 0 1-.589-.514l-.043-.107a.871.871 0 0 1 .053-.779l.721-1.234a.766.766 0 0 0-.116-.917 6.682 6.682 0 0 0-.252-.253.768.768 0 0 0-.917-.116l-1.234.722a.877.877 0 0 1-.779.053l-.107-.044a.87.87 0 0 1-.513-.587l-.362-1.383a.767.767 0 0 0-.73-.567h-.358a.768.768 0 0 0-.73.567l-.362 1.383a.878.878 0 0 1-.513.589l-.107.044a.875.875 0 0 1-.778-.054l-1.234-.722a.769.769 0 0 0-.918.117c-.086.082-.17.166-.253.253a.766.766 0 0 0-.115.916l.721 1.234a.87.87 0 0 1 .053.779l-.043.106a.874.874 0 0 1-.589.514l-1.382.362a.766.766 0 0 0-.567.731v.357a.766.766 0 0 0 .567.731l1.383.362c.266.07.483.26.588.513l.043.107a.87.87 0 0 1-.053.779l-.721 1.233a.767.767 0 0 0 .115.917c.083.087.167.171.253.253a.77.77 0 0 0 .918.116l1.234-.721a.87.87 0 0 1 .779-.054l.107.044a.878.878 0 0 1 .513.589l.362 1.383a.77.77 0 0 0 .731.567h.356a.766.766 0 0 0 .73-.567l.362-1.383a.878.878 0 0 1 .515-.589l.107-.044a.875.875 0 0 1 .778.054l1.234.721c.297.17.672.123.917-.117.087-.082.171-.166.253-.253a.766.766 0 0 0 .116-.917l-.721-1.234a.874.874 0 0 1-.054-.779l.044-.107a.88.88 0 0 1 .589-.513l1.383-.362a.77.77 0 0 0 .567-.731v-.357a.772.772 0 0 0-.569-.724v-.005zm-6.43 3.9a2.986 2.986 0 1 1 2.985-2.986 3 3 0 0 1-2.985 2.987v-.001z" fillRule="evenodd" /></g></svg></span>',
				'<span class="et_fb_tour_text et_fb_tour_text_black">?</span>',
				sprintf( '<a target="_blank" href="https://www.elegantthemes.com/documentation/divi/" class="et_fb_tour_text et_fb_tour_text_black">%1$s</a>', esc_html__( 'Documentation' ) ),
				sprintf(
					'<div class="et-fb-tour-video-overlay" data-video="https://www.youtube.com/embed/JXZIGZqr9OE?rel=0&autoplay=1">
							<img src="%1$s"/>
							<div class="et-fb-play-overlay"></div>
						</div>',
					esc_url( ET_BUILDER_URI . '/frontend-builder/assets/img/product-tour-intro.jpg' )
				)
			),
			'endButtonText'  => esc_html__( 'Start Building', 'et_builder' ),
			'skipButtonText' => esc_html__( 'Take the Tour', 'et_builder' ),
		),
		'loadLayout'            => array(
			'title'       => esc_html__( 'Load A New Layout', 'et_builder' ),
			'description' => esc_html__( 'Loading pre-made layouts is a great way to jump-start your new page. The Divi Builder comes with dozens of layouts to choose from, and you can find lots of great free layouts online too. You can save your favorite layouts to the Divi Library and load them on new pages or share them with the community. Click the highlighted button to open the layouts menu and select a pre-made layout.', 'et_builder' ),
		),
		'selectLayoutPack'      => array(
			'title'       => esc_html__( 'Choose A Layout Pack', 'et_builder' ),
			'description' => esc_html__( 'Here you can see a list of pre-made layout packs that ship with the Divi Builder. You can also access layouts that you have saved to your Divi Library. Choose the “Divi Builder Demo” layout pack to see the layouts it includes.', 'et_builder' ),
		),
		'loadLayoutItem'        => array(
			'title'       => esc_html__( 'Choose A Layout To Start With', 'et_builder' ),
			'description' => esc_html__( 'Now you can see more details about the layout pack as well as a list of the layouts it includes. Click “Use Layout” to apply the layout to your page.', 'et_builder' ),
		),
		'addSection'            => array(
			'title'       => esc_html__( 'Add A New Section', 'et_builder' ),
			'description' => sprintf(
				// translators: %1$s: "Sections" - label, %2$s: "Rows" - label.
				__( 'Now that your pre-made layout has been loaded, we can start adding new content to the page. The Divi Builder organizes content using %1$s, %2$s and Modules. Sections are the largest organizational element. Click the highlighted button to add a new section to the page.', 'et_builder' ),
				sprintf( '<span class="et_fb_tour_text_blue">%1$s</span>', esc_html__( 'Sections' ) ),
				sprintf( '<span class="et_fb_tour_text_green">%1$s</span>', esc_html__( 'Rows' ) )
			),
		),
		'selectSectionType'     => array(
			'title'       => esc_html__( 'Choose A Section Type', 'et_builder' ),
			'description' => sprintf(
				// translators: %1$s: "Regular" - label text, %2$s: "Specialty" - label tex, %3$s: "Fullwidth" - label tex.
				__( 'The Divi Builder has three different section types. %1$s sections conform to the standard width of your page layout. %2$s Sections can be used to create advanced sidebar layouts. %3$s sections extend the full width of your page and can be used with fullwidth modules. Click the “Regular” section button to add a new section to your page.', 'et_builder' ),
				sprintf( '<span class="et_fb_tour_text_blue">%1$s</span>', esc_html__( 'Regular' ) ),
				sprintf( '<span class="et_fb_tour_text_red">%1$s</span>', esc_html__( 'Specialty' ) ),
				sprintf( '<span class="et_fb_tour_text_purple">%1$s</span>', esc_html__( 'Fullwidth' ) )
			),
		),
		'selectRow'             => array(
			'title'       => esc_html__( 'Add A New Row Of Columns', 'et_builder' ),
			'description' => sprintf(
				// translators: %1$s: "Rows" - label.
				__( 'Every section contains one or more %1$s of columns. You can choose between various column layouts for each row you add to your page. Click the highlighted three-column layout to add a new row to your section.', 'et_builder' ),
				sprintf( '<span class="et_fb_tour_text_green">%1$s</span>', esc_html__( 'Rows' ) )
			),
		),
		'selectModule'          => array(
			'title'       => esc_html__( 'Add A Module To The Column', 'et_builder' ),
			'description' => esc_html__( 'Within each column you can add one or more Modules. A module is basic content element. The Divi Builder comes with over 40 different content elements to choose from, such as Images, Videos, Text, and Buttons. Click the highlighted Blurb button to add a new Blurb module to the first column in your row.', 'et_builder' ),
		),
		'configureModule'       => array(
			'title'       => esc_html__( 'Adjust Your Module Settings', 'et_builder' ),
			'description' => esc_html__( 'Each Module comes with various settings. These settings are separated into three tabs: Content, Design and Advanced. Inside the content tab you can modify the module content elements, such as text and images. If you need more control over the appearance of your module, head over to the Design tab. For more advanced modifications, such as custom CSS and HTML attributes, explore the Advanced tab. Try adjusting the Title of your blurb by clicking into the highlighted field.', 'et_builder' ),
		),
		'saveModule'            => array(
			'title'       => esc_html__( 'Accept Or Discard Your Changes', 'et_builder' ),
			'description' => esc_html__( 'Whenever you make changes in the Divi Builder, these changes can be Undone, Redone, Discarded or Accepted. Now that you have adjusted your module’s title, you can click the red discard button to cancel these changes, or your can click the green button to accept them.', 'et_builder' ),
		),
		'duplicateModule'       => array(
			'title'       => esc_html__( 'Hover To Access Action Buttons', 'et_builder' ),
			'description' => esc_html__( 'Whenever you hover over a Section, Row or Module in the Divi Builder, action buttons will appear. These buttons can be used to move, modify, duplicate or delete your content. Click the highlighted “duplicate” icon to duplicate the blurb module that you just added to the page.', 'et_builder' ),
		),
		'moveModule'            => array(
			'title'       => __( 'Drag & Drop Content', 'et_builder' ),
			'description' => esc_html__( 'Every item on the page can be dragged and dropped to new locations. Using your mouse, click the highlighted move icon and hold down the mouse button. While holding down the mouse button, move your cursor over to the empty column and then release your mouse button to drop the module into the new column.', 'et_builder' ),
		),
		'rightClickCopy'        => array(
			'title'       => esc_html__( 'Access Right Click Options', 'et_builder' ),
			'description' => esc_html__( 'In addition to hover actions, additional options can be accessed by Right Clicking or Cmd + Clicking on any module, row or section. Using the right click menu shown, click the highlighted “Copy Module” button to copy the blurb module that you just moved.', 'et_builder' ),
		),
		'rightClickPaste'       => array(
			'title'       => esc_html__( 'Paste Your Copied Module', 'et_builder' ),
			'description' => esc_html__( 'Now that you have copied a module using the Right Click menu, you can Right Click in a new location to paste that module. Using the right click options shown, click the “Paste Module” button to paste the module you just copied into the empty column.', 'et_builder' ),
		),
		'rowOptions'            => array(
			'title'       => esc_html__( 'Access Your Row Options', 'et_builder' ),
			'description' => esc_html__( 'Every Row and Section has its own set of options that can be used to adjust the item’s appearance. You can adjust its width, padding, background and more. To access a row’s settings, hover over the row and click the highlighted options button.', 'et_builder' ),
		),
		'editRow'               => array(
			'title'       => esc_html__( 'Adjust Your Row Setting', 'et_builder' ),
			'description' => esc_html__( 'Just like Modules, Rows come with a lot of settings that are separated into the Content, Design and Advanced tabs. Click the highlighted button to add a new background color to your row.', 'et_builder' ),
		),
		'saveRow'               => array(
			'title'       => esc_html__( 'Accept Your Changes', 'et_builder' ),
			'description' => esc_html__( 'Click the highlighted green check mark button to accept your changes. ', 'et_builder' ),
		),
		'pageSettings'          => array(
			'title'       => esc_html__( 'Open Your Page Settings', 'et_builder' ),
			'description' => esc_html__( 'While using the Divi Builder, you can access your page settings by toggling the page settings bar at the bottom of your screen. Click the highlighted button to reveal your page settings.', 'et_builder' ),
		),
		'tabletPreview'         => array(
			'title'       => esc_html__( 'Preview Your Page On Mobile', 'et_builder' ),
			'description' => esc_html__( 'While editing your page, it’s easy to see what your design will look like on mobile devices. You can also make adjustments to your module, row and section settings for each mobile breakpoint. Click the highlighted “Tablet” icon to enter Tablet preview mode. ', 'et_builder' ),
		),
		'desktopPreview'        => array(
			'title'       => esc_html__( 'Switch Back To Desktop Mode', 'et_builder' ),
			'description' => esc_html__( 'You can switch back and forth between each preview mode freely while editing your page. Now that we have previewed our page on Tablet, let’s switch back to Desktop preview mode by clicking the highlighted button.', 'et_builder' ),
		),
		'openHistory'           => array(
			'title'       => esc_html__( 'Access Your Editing History', 'et_builder' ),
			'description' => esc_html__( 'Every change you make while editing your page is saved in your editing history. You can navigate backwards and forwards through time to any point during your current editing session, as well as undo and redo recent changes. Click the highlighted History button to access your editing history. ', 'et_builder' ),
		),
		'editHistory'           => array(
			'title'       => esc_html__( 'Undo, Redo And Restore', 'et_builder' ),
			'description' => esc_html__( 'Here you can undo, redo or restore a saved history state. If you change your mind about recent changes, simply click back in time and start building again. You can also undo and redo recent changes. Click the undo and redo buttons and then accept your changes by clicking the green check mark.', 'et_builder' ),
		),
		'savePage'              => array(
			'title'       => esc_html__( 'Save Your Page', 'et_builder' ),
			'description' => sprintf(
				// translators: %1$s: "Save" or "Publish" - label.
				esc_html__( 'When you are all done, you can save your changes by clicking the %1$s button inside of your page settings bar. You can also press Ctrl + S at any time to save your changes. Click the highlighted Save button to save your changes. Don’t worry, the page you were working on before starting this tour will not be lost!', 'et_builder' ),
				in_array( $post_status, array( 'private', 'publish' ), true ) ? esc_html__( 'Save', 'et_builder' ) : esc_html__( 'Publish', 'et_builder' )
			),
		),
		'finish'                => array(
			'title'         => esc_html__( 'You’re Ready To Go!', 'et_builder' ),
			'description'   => sprintf(
				// translators: %10$s: Tour video overlay, %1$s: "Section" - label,  %2$s: Add icon markup, %3$s: "Row" - label, %4$s: Add icon markup, %5$s: "Modules" - label, %6$s: Add icon markup, %7$s: Settings gear icon markup, %9$s: Documentation link markup.
				__( '%10$sBuilding beautiful pages is a breeze using the Visual Builder. To get started, add a new %1$s to your page by pressing the %2$s button. Next, add a %3$s of columns inside your section by pressing the %4$s button. Finally, start adding some content %5$s inside your columns by pressing the %6$s button. You can customize the design and content of any element on the page by pressing the %7$s button. If you ever need help, visit our %9$s page for a full list of tutorials.', 'et_builder' ),
				sprintf( '<span class="et_fb_tour_text et_fb_tour_text_blue">%1$s</span>', esc_html__( 'Section' ) ),
				'<span class="et_fb_tour_icon et_fb_tour_icon_blue"><svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><g><path d="M18 13h-3v-3a1 1 0 0 0-2 0v3h-3a1 1 0 0 0 0 2h3v3a1 1 0 0 0 2 0v-3h3a1 1 0 0 0 0-2z" fillRule="evenodd" /></g></svg></span>',
				sprintf( '<span class="et_fb_tour_text et_fb_tour_text_green">%1$s</span>', esc_html__( 'Row' ) ),
				'<span class="et_fb_tour_icon et_fb_tour_icon_green"><svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><g><path d="M18 13h-3v-3a1 1 0 0 0-2 0v3h-3a1 1 0 0 0 0 2h3v3a1 1 0 0 0 2 0v-3h3a1 1 0 0 0 0-2z" fillRule="evenodd" /></g></svg></span>',
				sprintf( '<span class="et_fb_tour_text et_fb_tour_text_black">%1$s</span>', esc_html__( 'Modules' ) ),
				'<span class="et_fb_tour_icon"><svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><g><path d="M18 13h-3v-3a1 1 0 0 0-2 0v3h-3a1 1 0 0 0 0 2h3v3a1 1 0 0 0 2 0v-3h3a1 1 0 0 0 0-2z" fillRule="evenodd" /></g></svg></span>',
				'<span class="et_fb_tour_icon"><svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><g><path d="M20.426 13.088l-1.383-.362a.874.874 0 0 1-.589-.514l-.043-.107a.871.871 0 0 1 .053-.779l.721-1.234a.766.766 0 0 0-.116-.917 6.682 6.682 0 0 0-.252-.253.768.768 0 0 0-.917-.116l-1.234.722a.877.877 0 0 1-.779.053l-.107-.044a.87.87 0 0 1-.513-.587l-.362-1.383a.767.767 0 0 0-.73-.567h-.358a.768.768 0 0 0-.73.567l-.362 1.383a.878.878 0 0 1-.513.589l-.107.044a.875.875 0 0 1-.778-.054l-1.234-.722a.769.769 0 0 0-.918.117c-.086.082-.17.166-.253.253a.766.766 0 0 0-.115.916l.721 1.234a.87.87 0 0 1 .053.779l-.043.106a.874.874 0 0 1-.589.514l-1.382.362a.766.766 0 0 0-.567.731v.357a.766.766 0 0 0 .567.731l1.383.362c.266.07.483.26.588.513l.043.107a.87.87 0 0 1-.053.779l-.721 1.233a.767.767 0 0 0 .115.917c.083.087.167.171.253.253a.77.77 0 0 0 .918.116l1.234-.721a.87.87 0 0 1 .779-.054l.107.044a.878.878 0 0 1 .513.589l.362 1.383a.77.77 0 0 0 .731.567h.356a.766.766 0 0 0 .73-.567l.362-1.383a.878.878 0 0 1 .515-.589l.107-.044a.875.875 0 0 1 .778.054l1.234.721c.297.17.672.123.917-.117.087-.082.171-.166.253-.253a.766.766 0 0 0 .116-.917l-.721-1.234a.874.874 0 0 1-.054-.779l.044-.107a.88.88 0 0 1 .589-.513l1.383-.362a.77.77 0 0 0 .567-.731v-.357a.772.772 0 0 0-.569-.724v-.005zm-6.43 3.9a2.986 2.986 0 1 1 2.985-2.986 3 3 0 0 1-2.985 2.987v-.001z" fillRule="evenodd" /></g></svg></span>',
				'<span class="et_fb_tour_text et_fb_tour_text_black">?</span>',
				sprintf( '<a target="_blank" href="https://www.elegantthemes.com/documentation/divi/" class="et_fb_tour_text et_fb_tour_text_black">%1$s</a>', esc_html__( 'Documentation' ) ),
				sprintf(
					'<div class="et-fb-tour-video-overlay" data-video="https://www.youtube.com/embed/JXZIGZqr9OE?rel=0&autoplay=1">
							<img src="%1$s"/>
							<div class="et-fb-play-overlay"></div>
						</div>',
					esc_url( ET_BUILDER_URI . '/frontend-builder/assets/img/product-tour-intro.jpg' )
				)
			),
			'endButtonText' => esc_html__( 'Start Building', 'et_builder' ),
		),
		'endButtonTextDefault'  => esc_html__( 'End the Tour', 'et_builder' ),
		'skipButtonTextDefault' => esc_html__( 'Skip This Step', 'et_builder' ),
	);

	return $product_tour_text;
}

/**
 * Process builder shortcode into object.
 *
 * The standard do_shortcode filter should be removed, and
 * this function hooked instead.
 *
 * This function is very similar to `do_shortcode`,
 * with the main differences being:
 *  - Its main design is to allow recursive array to be built out of wp shortcode
 *  - Allows shortcode callback to return an array rather than a string
 *  - It tracks the inner `index` / `_i` of each child shortcode to the passed content, which is used in the address creation as well
 *  - It uses and passes `$address` & `$parent_address`, which are used by FB app.
 *
 * @param string $content post content.
 * @param string $parent_address parent shortcode address.
 * @param string $global_parent ?? @todo Add param doc.
 * @param string $global_parent_type ?? @todo Add param doc.
 * @param string $parent_type ?? @todo Add param doc.
 *
 * @return mixed
 */
function et_fb_process_shortcode( $content, $parent_address = '', $global_parent = '', $global_parent_type = '', $parent_type = '', $theme_builder_area = '' ) {
	global $shortcode_tags, $fb_processing_counter;

	if ( false === strpos( $content, '[' ) ) {
		return $content;
	}

	// Count started processes.
	$fb_processing_counter = isset( $fb_processing_counter ) ? $fb_processing_counter + 1 : 1;

	// Find all registered tag names in $content.
	preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches );
	// Only need unique tag names.
	$unique_matches = array_unique( $matches[1] );

	$tagnames = array_intersect( array_keys( $shortcode_tags ), $unique_matches );
	$pattern  = get_shortcode_regex( $unique_matches );
	$content  = preg_match_all( "/$pattern/", $content, $matches, PREG_SET_ORDER );

	$_matches = array();
	$_index   = 0;
	foreach ( $matches as $match ) {
		$tag = $match[2];

		// reset global parent data to calculate it correctly for next modules.
		if ( $global_parent_type === $tag && '' !== $global_parent ) {
			$global_parent      = '';
			$global_parent_type = '';
		}

		$attr = shortcode_parse_atts( $match[3] );

		if ( ! is_array( $attr ) ) {
			$attr = array();
		}

		$index   = $_index++;
		$address = isset( $parent_address ) && '' !== $parent_address ? (string) $parent_address . '.' . (string) $index : (string) $index;

		// set global parent and global parent tag if current module is global and can be a parent.
		$possible_global_parents = array( 'et_pb_section', 'et_pb_row', 'et_pb_row_inner' );
		if ( '' === $global_parent && in_array( $tag, $possible_global_parents, true ) ) {
			$global_parent      = isset( $attr['global_module'] ) ? $attr['global_module'] : '';
			$global_parent_type = $tag;
		}

		// As responsive content attributes value might be has been encoded before saving to database,
		// so we need to decode it before passing back to builder.
		if ( $attr ) {
			$decoded_content_fields = array(
				'content__hover',
				'content_tablet',
				'content_phone',
				'raw_content__hover',
				'raw_content_tablet',
				'raw_content_phone',
			);

			foreach ( $decoded_content_fields as $decoded_content_field ) {
				if ( array_key_exists( $decoded_content_field, $attr ) ) {
					$attr[ $decoded_content_field ] = str_replace( array( '%22', '%92', '%91', '%93' ), array( '"', '\\', '&#91;', '&#93;' ), $attr[ $decoded_content_field ] );
				}
			}
		}

		$attr['_i']       = $index;
		$attr['_address'] = $address;

		// Builder shortcode which exist on page but not registered in WP i.e. 3rd party shortcode when 3rd party module disabled
		// Add dummy object to render it in Divi Builder.
		if ( ! in_array( $tag, $tagnames, true ) ) {
			$_matches[] = array(
				'_i'             => $index,
				'_order'         => $index,
				'address'        => $address,
				'vb_support'     => 'off',
				'component_path' => 'et-fb-removed-component',
				'type'           => $tag,
				'attrs'          => $attr,
			);

			continue;
		}

		// Flag that the shortcode object is being built.
		$GLOBALS['et_fb_processing_shortcode_object'] = true;

		if ( isset( $match[5] ) ) {
			// phpcs:ignore Generic.PHP.ForbiddenFunctions.Found -- `call_user_func` calls the registered shortcode callback.
			$output = call_user_func( $shortcode_tags[ $tag ], $attr, $match[5], $tag, $parent_address, $global_parent, $global_parent_type, $parent_type, $theme_builder_area );
		} else {
			// self-closing tag.
			// phpcs:ignore Generic.PHP.ForbiddenFunctions.Found -- `call_user_func` calls the registered shortcode callback.
			$output = call_user_func( $shortcode_tags[ $tag ], $attr, null, $tag );
		}

		$_matches[] = et_fb_add_additional_attrs( $attr, $output );
	}

	// Count finished processes.
	$fb_processing_counter = $fb_processing_counter - 1; // phpcs:ignore Squiz.Operators.IncrementDecrementUsage -- This is more readable.

	// Make sure ALL the processes finished to avoid wrong disabling of `et_fb_processing_shortcode_object` when several concurrent instances of `et_fb_process_shortcode` running.
	if ( 0 === $fb_processing_counter ) {
		// Turn off the flag since the shortcode object is done being built.
		et_fb_reset_shortcode_object_processing();
	}

	return $_matches;
}

/**
 * Allowlist any additional attributes.
 *
 * @param array $processed_attrs Shortcode's processed attributes.
 * @param array $output Shortcode output.
 *
 * @return mixed
 */
function et_fb_add_additional_attrs( $processed_attrs, $output ) {
	if ( empty( $output['attrs'] ) ) {
		return $output;
	}

	// A list of all the attributes that are already returned after the shortcode is processed.
	$safe_attrs    = array_keys( $output['attrs'] );
	$allowed_attrs = array();

	foreach ( $processed_attrs as $attr => $value ) {
		if ( ! preg_match( '~_hover(_enabled)?$~', $attr ) ) {
			continue;
		}

		// if color value includes `gcid-`, check for associated Global Color value.
		if ( empty( $value ) || false === strpos( $value, 'gcid-' ) ) {
			continue;
		}

		$global_color_info = et_builder_get_all_global_colors( true );

		// If there are no matching Global Colors, return null.
		if ( ! is_array( $global_color_info ) ) {
			continue;
		}

		foreach ( $global_color_info as $gcid => $details ) {
			if ( false !== strpos( $value, $gcid ) ) {
				// Match substring (needed for attrs like gradient stops).
				$value = str_replace( $gcid, $details['color'], $value );
			}
		}

		// Finally, escape the output.
		if ( ! empty( $global_color_info['color'] ) ) {
			$value = esc_attr( $value );
		}

		$allowed_attrs[ $attr ] = $value;
	}

	// Extra conversion for the case with the `font_icon__hover` option.
	if ( ! empty( $allowed_attrs['font_icon__hover'] ) && et_pb_maybe_old_divi_font_icon( $allowed_attrs['font_icon__hover'] ) ) {
		$allowed_attrs['font_icon__hover'] = et_pb_build_extended_font_icon_value( $allowed_attrs['font_icon__hover'], null, null, true );
	}

	if ( $allowed_attrs ) {
		$output['attrs'] = array_merge( $output['attrs'], $allowed_attrs );
	}

	return $output;
}

/**
 * Parse builder shortcode into an array.
 *
 * @param string $content Builder built post content.
 *
 * @return array Array representation of the builder shortcode.
 */
function et_pb_parse_shortcode_to_array( $content ) {
	global $shortcode_tags;

	if ( false === strpos( $content, '[' ) ) {
		return $content;
	}

	// Find all registered tag names in $content.
	preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches );
	$tagnames = array_intersect( array_keys( $shortcode_tags ), $matches[1] );

	$pattern = get_shortcode_regex( $matches[1] );

	$content = preg_match_all( "/$pattern/", $content, $matches, PREG_SET_ORDER );

	$shortcode_data = array();
	foreach ( $matches as $match ) {
		$tag  = $match[2];
		$attr = shortcode_parse_atts( $match[3] );

		if ( ! is_array( $attr ) ) {
			$attr = array();
		}

		$_shortcode_data = array(
			'type'  => $tag,
			'attrs' => $attr,
		);

		if ( ! empty( $match[5] ) ) {
			$_shortcode_data['content'] = et_pb_parse_shortcode_to_array( $match[5] );
		}

		$shortcode_data[] = $_shortcode_data;
	}

	return $shortcode_data;
}

/**
 * Parse post content that includes builder shortcode to an array,
 * then run it back through "some sanity check" and "some sanitization"
 * and then form into a shortcode again.
 *
 * @see et_fb_process_to_shortcode() for exact sanitizations performed.
 *
 * @param string $content Builder built post content.
 * @param bool   $force_valid_builder_slugs Whether to force the shortcode to allow valid builder shortcode slugs only.
 *
 * @return string Sanitized builder built post content.
 */
function et_pb_sanitize_shortcode( $content, $force_valid_builder_slugs = false ) {
	global $shortcode_tags;

	if ( false === strpos( $content, '[' ) ) {
		return $content;
	}

	$content_array = et_pb_parse_shortcode_to_array( $content );

	$options     = array(
		'force_valid_slugs' => $force_valid_builder_slugs,
	);
	$new_content = et_fb_process_to_shortcode( $content_array, $options );

	return $new_content;
}

/**
 * Use shortcode tag which renders the content to correctly display its properties.
 *
 * @param string $tag shortcode tag.
 *
 * @return string
 */
function et_fb_prepare_tag( $tag ) {
	// List of aliases.
	$aliases = apply_filters(
		'et_fb_prepare_tag_aliases',
		array(
			'et_pb_accordion_item' => 'et_pb_toggle',
		)
	);

	return isset( $aliases[ $tag ] ) ? $aliases[ $tag ] : $tag;
}

if ( ! function_exists( 'et_strip_shortcodes' ) ) :
	/**
	 * Strip builder shortcodes only, leaving default WordPress shortcodes intact.
	 *
	 * @param string $content the content.
	 * @param string $truncate_post_based_shortcodes_only Optional. Whether trunct only post based shortcodes.
	 */
	function et_strip_shortcodes( $content, $truncate_post_based_shortcodes_only = false ) {
		global $shortcode_tags;

		$content = trim( $content );

		$strip_content_shortcodes = array(
			'et_pb_code',
			'et_pb_fullwidth_code',
			'et_pb_social_media_follow_network',
		);

		// list of post-based shortcodes.
		if ( $truncate_post_based_shortcodes_only ) {
			$strip_content_shortcodes = array(
				'et_pb_post_slider',
				'et_pb_fullwidth_post_slider',
				'et_pb_blog',
				'et_pb_comments',
			);
		}

		foreach ( $strip_content_shortcodes as $shortcode_name ) {
			$regex = sprintf(
				'(\[%1$s[^\]]*\][^\[]*\[\/%1$s\]|\[%1$s[^\]]*\])',
				esc_html( $shortcode_name )
			);

			$content = preg_replace( $regex, '', $content );
		}

		// do not proceed if we need to truncate post-based shortcodes only.
		if ( $truncate_post_based_shortcodes_only ) {
			return $content;
		}

		$shortcode_tag_names = array();
		foreach ( $shortcode_tags as $shortcode_tag_name => $shortcode_tag_cb ) {
			if ( 0 !== strpos( $shortcode_tag_name, 'et_pb_' ) ) {
				continue;
			}

			$shortcode_tag_names[] = $shortcode_tag_name;
		}

		$et_shortcodes = implode( '|', $shortcode_tag_names );

		$regex_opening_shortcodes = sprintf( '(\[(%1$s)[^\]]+\])', esc_html( $et_shortcodes ) );
		$regex_closing_shortcodes = sprintf( '(\[\/(%1$s)\])', esc_html( $et_shortcodes ) );

		$content = preg_replace( $regex_opening_shortcodes, '', $content );
		$content = preg_replace( $regex_closing_shortcodes, '', $content );

		return $content;
	}
endif;

/**
 * Reset shortcode object processing.
 */
function et_fb_reset_shortcode_object_processing() {
	$GLOBALS['et_fb_processing_shortcode_object'] = false;
}

add_action( 'et_fb_enqueue_assets', 'et_fb_backend_helpers' );

if ( ! function_exists( 'et_builder_maybe_flush_rewrite_rules' ) ) :
	/**
	 * Flush rewrite rules if theme option saved value and passed $value are not same.
	 *
	 * @param string $setting_name The theme option.
	 * @param string $value The value to be compared.
	 */
	function et_builder_maybe_flush_rewrite_rules( $setting_name, $value = 'done' ) {
		$string_value = (string) $value;
		$saved_value  = et_get_option( $setting_name );

		if ( $saved_value && $saved_value === $string_value ) {
			return;
		}

		flush_rewrite_rules();

		et_update_option( $setting_name, $string_value );
	}
endif;

/**
 * Flush rewrite rules to fix the issue Layouts, not being visible on front-end and visual builder,
 * if pretty permalinks were enabled
 *
 * @return void
 */
function et_pb_maybe_flush_rewrite_rules_library() {
	// Run flush rewrite only when et_pb_layout post type registered.
	if ( post_type_exists( 'et_pb_layout' ) ) {
		et_builder_maybe_flush_rewrite_rules( 'et_flush_rewrite_rules_library', ET_BUILDER_PRODUCT_VERSION );
	}
}
add_action( 'init', 'et_pb_maybe_flush_rewrite_rules_library', 9 );

/**
 * Remove et_builder_maybe_flush_rewrite_rules flag if flush_rewrite_rules() is called while
 * `et_pb_layout` post type hasn't been registered
 *
 * @since 3.19.18
 *
 * @param string|array $old_value old option value.
 * @param string|array $value new option value.
 * @param string       $option option name.
 */
function et_pb_maybe_remove_flush_rewrite_rules_library_flag( $old_value, $value, $option ) {
	// rewrite rules for CPT that are rebuilt by flush_rewrite_rules() are based on
	// get_post_types( array( '_builtin' => false ) ) value; Hence if flush_rewrite_rules() is
	// executed while `et_pb_layout` CPT hasn't been registered (usually by third party plugin)
	// et_pb_maybe_flush_rewrite_rules_library() flag has to be removed to trigger flush_rewrite_rules()
	// via et_pb_maybe_flush_rewrite_rules_library() which contains `et_pb_layout` rewrite rules
	// because et_pb_maybe_flush_rewrite_rules_library() checks for `et_pb_layout` first.
	if ( '' === $value && ! post_type_exists( 'et_pb_layout' ) ) {
		et_update_option( 'et_flush_rewrite_rules_library', '' );
	}
}
add_action( 'update_option_rewrite_rules', 'et_pb_maybe_remove_flush_rewrite_rules_library_flag', 10, 3 );

if ( ! function_exists( 'et_builder_get_shortcuts' ) ) :
	/**
	 * Get list of shortcut available on BB and FB
	 *
	 * @param string $on (fb|bb) shortcut mode.
	 * @return array shortcut list
	 */
	function et_builder_get_shortcuts( $on = 'fb' ) {

		$shortcuts = array(
			'page'   => array(
				'page_title'              => array(
					'title' => esc_html__( 'Page Shortcuts', 'et_builder' ),
					'on'    => array(
						'fb',
						'bb',
					),
				),
				'undo'                    => array(
					'kbd'  => array( 'super', 'z' ),
					'desc' => esc_html__( 'Undo', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'redo'                    => array(
					'kbd'  => array( 'super', 'y' ),
					'desc' => esc_html__( 'Redo', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'save'                    => array(
					'kbd'  => array( 'super', 's' ),
					'desc' => esc_html__( 'Save Page', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'save_as_draft'           => array(
					'kbd'  => array( 'super', 'shift', 's' ),
					'desc' => esc_html__( 'Save Page As Draft', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'exit'                    => array(
					'kbd'  => array( 'super', 'e' ),
					'desc' => esc_html__( 'Exit Visual Builder', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'exit_to_backend_builder' => array(
					'kbd'  => array( 'super', 'shift', 'e' ),
					'desc' => esc_html__( 'Exit To Backend Builder', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'toggle_settings_bar'     => array(
					'kbd'  => array( 't' ),
					'desc' => esc_html__( 'Toggle Settings Bar', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'open_page_settings'      => array(
					'kbd'  => array( 'o' ),
					'desc' => esc_html__( 'Open Page Settings', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'open_history'            => array(
					'kbd'  => array( 'h' ),
					'desc' => esc_html__( 'Open History Window', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'open_portability'        => array(
					'kbd'  => array( 'p' ),
					'desc' => esc_html__( 'Open Portability Window', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'zoom_in'                 => array(
					'kbd'  => array( 'super', '+' ),
					'desc' => esc_html__( 'Responsive Zoom In', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'zoom_out'                => array(
					'kbd'  => array( 'super', '-' ),
					'desc' => esc_html__( 'Responsive Zoom Out', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'wireframe'               => array(
					'kbd'  => array( 'shift', 'w' ),
					'desc' => esc_html__( 'Wireframe Mode', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'click_mode'              => array(
					'kbd'  => array( 'super', 'shift', 'c' ),
					'desc' => esc_html__( 'Click Mode', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'grid_mode'               => array(
					'kbd'  => array( 'super', 'shift', 'g' ),
					'desc' => esc_html__( 'Grid Mode', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'hover_mode'              => array(
					'kbd'  => array( 'super', 'shift', 'h' ),
					'desc' => esc_html__( 'Hover Mode', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'help'                    => array(
					'kbd'  => array( '?' ),
					'desc' => esc_html__( 'List All Shortcuts', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
			),
			'inline' => array(
				'inline_title' => array(
					'title' => esc_html__( 'Inline Editor Shortcuts', 'et_builder' ),
					'on'    => array(
						'fb',
					),
				),
				'escape'       => array(
					'kbd'  => array( 'esc' ),
					'desc' => esc_html__( 'Exit Inline Editor', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
			),
			'module' => array(
				'module_title'                 => array(
					'title' => esc_html__( 'Module Shortcuts', 'et_builder' ),
					'on'    => array(
						'fb',
						'bb',
					),
				),
				'module_copy'                  => array(
					'kbd'  => array( 'super', 'c' ),
					'desc' => esc_html__( 'Copy Module', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'module_cut'                   => array(
					'kbd'  => array( 'super', 'x' ),
					'desc' => esc_html__( 'Cut Module', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'module_paste'                 => array(
					'kbd'  => array( 'super', 'v' ),
					'desc' => esc_html__( 'Paste Module', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'module_copy_styles'           => array(
					'kbd'  => array( 'super', 'alt', 'c' ),
					'desc' => esc_html__( 'Copy Module Styles', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'module_paste_styles'          => array(
					'kbd'  => array( 'super', 'alt', 'v' ),
					'desc' => esc_html__( 'Paste Module Styles', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'module_reset_styles'          => array(
					'kbd'  => array( 'super', 'alt', 'r' ),
					'desc' => esc_html__( 'Reset Module Styles', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'module_lock'                  => array(
					'kbd'  => array( 'super', 'shift', 'l' ),
					'desc' => esc_html__( 'Lock Module', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'module_disable'               => array(
					'kbd'  => array( 'super', 'shift', 'd' ),
					'desc' => esc_html__( 'Disable Module', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'drag_auto_copy'               => array(
					'kbd'  => array( 'alt', 'module move' ),
					'desc' => esc_html__( 'Move and copy module into dropped location', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'column_change_structure'      => array(
					'kbd'  => array( 'c', array( '1', '2', '3', '4', '5', '...' ) ),
					'desc' => esc_html__( 'Change Column Structure', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'row_make_fullwidth'           => array(
					'kbd'  => array( 'r', 'f' ),
					'desc' => esc_html__( 'Make Row Fullwidth', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'row_edit_gutter'              => array(
					'kbd'  => array( 'g', array( '1', '2', '3', '4' ) ),
					'desc' => esc_html__( 'Change Gutter Width', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'add_new_row'                  => array(
					'kbd'  => array( 'r', array( '1', '2', '3', '4', '5', '...' ) ),
					'desc' => esc_html__( 'Add New Row', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'add_new_section'              => array(
					'kbd'  => array( 's', array( '1', '2', '3' ) ),
					'desc' => esc_html__( 'Add New Section', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'resize_padding_auto_opposite' => array(
					'kbd'  => array( 'shift', 'Drag Padding' ),
					'desc' => esc_html__( 'Restrict padding to 10px increments', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'resize_padding_limited'       => array(
					'kbd'  => array( 'alt', 'Drag Padding' ),
					'desc' => esc_html__( 'Padding limited to opposing value', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'resize_padding_10'            => array(
					'kbd'  => array( 'shift', 'alt', 'Drag Padding' ),
					'desc' => esc_html__( 'Mirror padding on both sides', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'increase_padding_row'         => array(
					'kbd'  => array( 'r', array( 'left', 'right', 'up', 'down' ) ),
					'desc' => esc_html__( 'Increase Row Padding', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'decrease_padding_row'         => array(
					'kbd'  => array( 'r', 'alt', array( 'left', 'right', 'up', 'down' ) ),
					'desc' => esc_html__( 'Decrease Row Padding', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'increase_padding_section'     => array(
					'kbd'  => array( 's', array( 'left', 'right', 'up', 'down' ) ),
					'desc' => esc_html__( 'Increase Section Padding', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'decrease_padding_section'     => array(
					'kbd'  => array( 's', 'alt', array( 'left', 'right', 'up', 'down' ) ),
					'desc' => esc_html__( 'Decrease Section Padding', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'increase_padding_module'      => array(
					'kbd'  => array( 'm', array( 'left', 'right', 'up', 'down' ) ),
					'desc' => esc_html__( 'Increase Module Padding', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'decrease_padding_module'      => array(
					'kbd'  => array( 'm', 'alt', array( 'left', 'right', 'up', 'down' ) ),
					'desc' => esc_html__( 'Decrease Module Padding', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'increase_padding_row_10'      => array(
					'kbd'  => array( 'r', 'shift', array( 'left', 'right', 'up', 'down' ) ),
					'desc' => esc_html__( 'Increase Row Padding By 10px', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'decrease_padding_row_10'      => array(
					'kbd'  => array( 'r', 'alt', 'shift', array( 'left', 'right', 'up', 'down' ) ),
					'desc' => esc_html__( 'Decrease Row Padding By 10px', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'increase_padding_section_10'  => array(
					'kbd'  => array( 's', 'shift', array( 'left', 'right', 'up', 'down' ) ),
					'desc' => esc_html__( 'Increase Section Padding By 10px', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'decrease_padding_section_10'  => array(
					'kbd'  => array( 's', 'alt', 'shift', array( 'left', 'right', 'up', 'down' ) ),
					'desc' => esc_html__( 'Decrease Section Padding By 10px', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'increase_padding_module_10'   => array(
					'kbd'  => array( 'm', 'shift', array( 'left', 'right', 'up', 'down' ) ),
					'desc' => esc_html__( 'Increase Module Padding By 10px', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'decrease_padding_module_10'   => array(
					'kbd'  => array( 'm', 'alt', 'shift', array( 'left', 'right', 'up', 'down' ) ),
					'desc' => esc_html__( 'Decrease Module Padding By 10px', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
			),
			'modal'  => array(
				'modal_title'   => array(
					'title' => esc_html__( 'Modal Shortcuts', 'et_builder' ),
					'on'    => array(
						'fb',
						'bb',
					),
				),
				'escape'        => array(
					'kbd'  => array( 'esc' ),
					'desc' => esc_html__( 'Close Modal', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'save_changes'  => array(
					'kbd'  => array( 'enter' ),
					'desc' => esc_html__( 'Save Changes', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'undo'          => array(
					'kbd'  => array( 'super', 'z' ),
					'desc' => esc_html__( 'Undo', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'redo'          => array(
					'kbd'  => array( 'super', 'shift', 'z' ),
					'desc' => esc_html__( 'Redo', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'switch_tabs'   => array(
					'kbd'  => array( 'shift', 'tab' ),
					'desc' => esc_html__( 'Switch Tabs', 'et_builder' ),
					'on'   => array(
						'fb',
						'bb',
					),
				),
				'toggle_expand' => array(
					'kbd'  => array( 'super', 'enter' ),
					'desc' => esc_html__( 'Expand Modal Fullscreen', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'toggle_snap'   => array(
					'kbd'  => array( 'super', array( 'left', 'right' ) ),
					'desc' => esc_html__( 'Snap Modal Left / Right', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'quick_actions' => array(
					'kbd'  => array( 'shift', 'space' ),
					'desc' => esc_html__( 'Quick Actions', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
				'layers_view'   => array(
					'kbd'  => array( 'super', 'l' ),
					'desc' => esc_html__( 'Layers View', 'et_builder' ),
					'on'   => array(
						'fb',
					),
				),
			),
		);
		$shortcuts = apply_filters( 'et_builder_get_shortcuts', $shortcuts );

		// Filter shortcuts.
		$filtered_shortcuts = array();

		foreach ( $shortcuts as $group_key => $group ) {
			foreach ( $group as $shortcut_key => $shortcut ) {
				if ( in_array( $on, $shortcut['on'], true ) ) {
					$filtered_shortcuts[ $group_key ][ $shortcut_key ] = $shortcut;
				}
			}
		}

		return $filtered_shortcuts;
	}
endif;

if ( ! function_exists( 'et_pb_get_responsive_status' ) ) :
	/**
	 * Parsed *_last_edited value and determine wheter the passed string means it has responsive value or not
	 * *_last_edited holds two values (responsive status and last opened tabs) in the following format: status|last_opened_tab
	 *
	 * @param string $last_edited last_edited data.
	 * @return bool
	 */
	function et_pb_get_responsive_status( $last_edited ) {
		$parsed_last_edited = is_string( $last_edited ) ? explode( '|', $last_edited ) : array( 'off', 'desktop' );

		return isset( $parsed_last_edited[0] ) ? 'on' === $parsed_last_edited[0] : false;
	}
endif;

if ( ! function_exists( 'et_pb_get_value_unit' ) ) :
	/**
	 * Get unit of given value
	 *
	 * @param string $value string with unit.
	 * @param string $default_unit default unit.
	 *
	 * @return string unit name
	 */
	function et_pb_get_value_unit( $value, $default_unit = 'px' ) {
		$value                   = isset( $value ) ? $value : '';
		$valid_one_char_units    = array( '%', 'x' );
		$valid_two_chars_units   = array( 'em', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw', 'ms' );
		$valid_three_chars_units = array( 'deg', 'rem' );
		$important               = '!important';
		$important_length        = strlen( $important );
		$value_length            = strlen( $value );

		if ( '' === $value || is_numeric( $value ) ) {
			return $default_unit;
		}

		if ( substr( $value, ( 0 - $important_length ), $important_length ) === $important ) {
			$value_length = $value_length - $important_length;
			$value        = trim( substr( $value, 0, $value_length ) );
		}

		if ( in_array( substr( $value, -3, 3 ), $valid_three_chars_units, true ) ) {
			return substr( $value, -3, 3 );
		}

		if ( in_array( substr( $value, -2, 2 ), $valid_two_chars_units, true ) ) {
			return substr( $value, -2, 2 );
		}

		if ( in_array( substr( $value, -1, 1 ), $valid_one_char_units, true ) ) {
			return substr( $value, -1, 1 );
		}

		return $default_unit;
	}
endif;

if ( ! function_exists( 'et_sanitize_input_unit' ) ) :
	/**
	 * Sanitized value and its unit
	 *
	 * @param mixed       $value Input value.
	 * @param string      $auto_important Whether add !important specificity. Default false.
	 * @param string|bool $default_unit The default unit.
	 *
	 * @return string sanitized input and its unit
	 */
	function et_sanitize_input_unit( $value = '', $auto_important = false, $default_unit = false ) {
		$value                   = (string) $value;
		$valid_one_char_units    = array( '%', 'x' );
		$valid_two_chars_units   = array( 'em', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ex', 'vh', 'vw', 'ms' );
		$valid_three_chars_units = array( 'deg', 'rem' );
		$important               = '!important';
		$important_length        = strlen( $important );
		$has_important           = false;
		$value_length            = strlen( $value );
		$unit_value;

		// Check for important.
		if ( substr( $value, ( 0 - $important_length ), $important_length ) === $important ) {
			$has_important = true;
			$value_length  = $value_length - $important_length;
			$value         = trim( substr( $value, 0, $value_length ) );
		}

		if ( in_array( substr( $value, -3, 3 ), $valid_three_chars_units, true ) ) {
			$unit_value = floatval( $value ) . substr( $value, -3, 3 );

			// Re-add !important tag.
			if ( $has_important && ! $auto_important ) {
				$unit_value = $unit_value . ' ' . $important;
			}

			return $unit_value;
		}

		if ( in_array( substr( $value, -2, 2 ), $valid_two_chars_units, true ) ) {
			$unit_value = floatval( $value ) . substr( $value, -2, 2 );

			// Re-add !important tag.
			if ( $has_important && ! $auto_important ) {
				$unit_value = $unit_value . ' ' . $important;
			}

			return $unit_value;
		}

		if ( in_array( substr( $value, -1, 1 ), $valid_one_char_units, true ) ) {
			$unit_value = floatval( $value ) . substr( $value, -1, 1 );

			// Re-add !important tag.
			if ( $has_important && ! $auto_important ) {
				$unit_value = $unit_value . ' ' . $important;
			}

			return $unit_value;
		}

		$result = floatval( $value );

		if ( 'no_default_unit' === $default_unit ) {
			return $result;
		}

		if ( $default_unit ) {
			return $result . $default_unit;
		}

		if ( ! $default_unit ) {
			$result .= 'px';
		}

		// Return and automatically append px (default value).
		return $result;
	}
endif;

if ( ! function_exists( 'et_builder_get_taxonomies' ) ) :
	/**
	 * Get taxonomies for modules
	 *
	 * @param array $args Optional. Uses 'use_terms' argument to retrieve the terms in a given taxonomy.
	 * Uses 'term_name' to specify Taxonomy to retrieve terms.
	 *
	 * @return array Array of WP taxonomies splitted into the taxonomy types
	 */
	function et_builder_get_shop_categories( $args = array() ) {
		$defaults = apply_filters(
			'et_builder_include_categories_shop_defaults',
			array(
				'use_terms' => true,
				'term_name' => 'product_cat',
			)
		);

		$term_args          = apply_filters( 'et_builder_include_categories_shop_args', array( 'hide_empty' => false ) );
		$args               = wp_parse_args( $args, $defaults );
		$product_categories = $args['use_terms'] ? get_terms( $args['term_name'], $term_args ) : get_categories( apply_filters( 'et_builder_get_categories_shop_args', 'hide_empty=0' ) );

		return $product_categories;
	}
endif;

if ( ! function_exists( 'et_pb_get_spacing' ) ) :
	/**
	 * Return spacing value.
	 *
	 * @param string $spacing  spacing string.
	 * @param string $corner spacing corner.
	 * @param string $default default value.
	 */
	function et_pb_get_spacing( $spacing, $corner, $default = '0px' ) {
		$corners       = array( 'top', 'right', 'bottom', 'left' );
		$corner_index  = array_search( $corner, $corners, true );
		$spacing_array = explode( '|', $spacing );

		return isset( $spacing_array[ $corner_index ] ) && '' !== $spacing_array[ $corner_index ] ? $spacing_array[ $corner_index ] : $default;
	}
endif;

if ( ! function_exists( 'et_fb_enqueue_bundle' ) ) :
	/**
	 * Enqueue a bundle
	 *
	 * @param string $id Name of the stylesheet.
	 * @param string $resource Resource file name.
	 * @param array  $deps Resource dependencies.
	 * @param mixed  $ver Resource version number.
	 */
	function et_fb_enqueue_bundle( $id, $resource, $deps, $ver = false ) {
		$debug  = defined( 'ET_DEBUG' ) && ET_DEBUG;
		$ver    = false === $ver ? ET_BUILDER_VERSION : $ver;
		$build  = 'frontend-builder/build';
		$bundle = sprintf( '%s/%s/%s', ET_BUILDER_URI, $build, $resource );
		$type   = pathinfo( $resource, PATHINFO_EXTENSION );

		switch ( $type ) {
			case 'css':
				if ( file_exists( sprintf( '%s%s/%s', ET_BUILDER_DIR, $build, $resource ) ) || ! $debug ) {
					wp_enqueue_style( $id, $bundle, $deps, $ver );
				} elseif ( $debug ) {
					// Style is already embedded in the bundle but we still need to enqueue its deps.
					foreach ( $deps as $dep ) {
						wp_enqueue_style( $dep );
					}
				}
				break;
			case 'js':
				if ( file_exists( sprintf( '%s%s/%s', ET_BUILDER_DIR, $build, $resource ) ) || ! $debug ) {
					// If the file exists on disk, enqueue it.
					wp_enqueue_script( $id, $bundle, $deps, $ver, true );
				} else {
					// Otherwise load `hot` from webpack-dev-server.
					$site_url       = wp_parse_url( get_site_url() );
					$hot_bundle_url = "{$site_url['scheme']}://{$site_url['host']}:31495/$resource";

					wp_enqueue_script( $id, $hot_bundle_url, $deps, $ver, true );
				}
				wp_add_inline_script( $id, 'window.et_gb = (window.top && window.top.Cypress && window.parent === window.top && window) || (window.top && window.top.Cypress && window.parent !== window.top && window.parent) || window.top || window;', 'before' );
				break;
		}
	}
endif;

if ( ! function_exists( 'et_builder_get_active_plugins' ) ) :
	/**
	 * Get list of all active plugins (single, network active, and mu)
	 *
	 * @return array active plugins
	 */
	function et_builder_get_active_plugins() {
		$active_plugins = get_option( 'active_plugins' );

		// Returned format must be array.
		if ( ! is_array( $active_plugins ) ) {
			$active_plugins = array();
		}

		// Get mu-plugins (must-use)
		// mu-plugins data is returned in array( "plugin/name.php" => array( 'data' => 'value' ) ) format.
		$mu_plugins = get_mu_plugins();
		if ( is_array( $mu_plugins ) ) {
			$active_plugins = array_merge( $active_plugins, array_keys( $mu_plugins ) );
		}

		// Get network active plugins
		// Network active plugin data is returned in array( "plugin/name.php" => active_timestamp_int format.
		if ( is_multisite() ) {
			$network_active_plugins = get_site_option( 'active_sitewide_plugins' );

			if ( is_array( $network_active_plugins ) ) {
				$active_plugins = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
			}
		}

		return apply_filters( 'et_builder_get_active_plugins', $active_plugins );
	}
endif;

if ( ! function_exists( 'et_has_hover_enabled' ) ) :
	/**
	 * Determine whether hover is enable or not.
	 *
	 * @param array $props element's props.
	 */
	function et_has_hover_enabled( $props ) {
		$et_has_hover_enabled = false;
		$prop_names           = array_keys( $props );
		$suffix               = et_pb_hover_options()->get_enabled_suffix();
		foreach ( $prop_names as $prop_name ) {
			if ( preg_match( "~{$suffix}$~", $prop_name ) && 'on' === $props[ $prop_name ] ) {
				$et_has_hover_enabled = true;
				break;
			}
		}
		return $et_has_hover_enabled;
	}
endif;

if ( ! function_exists( 'et_builder_is_hover_enabled' ) ) :
	/**
	 * Check if the setting has enabled hover options
	 *
	 * @param string $setting setting name.
	 * @param array  $props element's props.
	 */
	function et_builder_is_hover_enabled( $setting, $props ) {
		return et_pb_hover_options()->is_enabled( $setting, $props );
	}
endif;

if ( ! function_exists( 'et_builder_add_prefix' ) ) {
	/**
	 * Prefixes a string key with a prefix string using the provided delimiter
	 * In case the prefix is empty, original key is returned
	 *
	 * @param string $prefix  prefix string.
	 * @param string $key string key.
	 * @param string $delimiter  delimiter.
	 *
	 * @return string
	 */
	function et_builder_add_prefix( $prefix, $key, $delimiter = '_' ) {
		return '' === $prefix ? $key : $prefix . $delimiter . $key;
	}
}

if ( ! function_exists( 'et_builder_has_value' ) ) {
	/**
	 * Check if value is not an empty value
	 * Empty values are considered:
	 *  - null
	 *  - ''
	 *  - false
	 *
	 * @param string $value value to check.
	 *
	 * @return bool
	 */
	function et_builder_has_value( $value ) {
		return null !== $value && '' !== $value && false !== $value;
	}
}

if ( ! function_exists( 'et_builder_get_or' ) ) {
	/**
	 * Returns the value in case it is not empty
	 * Otherwise, return the default value
	 *
	 * @param string $value the builder value.
	 * @param string $default default value.
	 *
	 * @return string
	 */
	function et_builder_get_or( $value, $default = '' ) {
		return et_builder_has_value( $value ) ? $value : $default;
	}
}

if ( ! function_exists( 'et_builder_module_prop' ) ) {
	/**
	 * Returns props value by provided key, if the value is empty, returns the default value
	 *
	 * @param string $prop provided key.
	 * @param array  $props all props.
	 * @param mixed  $default default value.
	 *
	 * @return mixed|null
	 */
	function et_builder_module_prop( $prop, $props, $default ) {
		return et_builder_get_or( et_()->array_get( $props, $prop ), $default );
	}
}

if ( ! function_exists( 'et_pb_get_column_svg' ) ) {
	/**
	 * Returns svg which represents the requried columns type
	 *
	 * @param string $type Column layout type.
	 *
	 * @return string svg code.
	 */
	function et_pb_get_column_svg( $type ) {
		$svg = '';

		switch ( $type ) {
			case '4_4':
				$svg = '<rect width="100%" height="20" y="5" rx="5" ry="5" />';
				break;
			case '1_2,1_2':
				$svg = '<rect width="48.5%" height="20" y="5" rx="5" ry="5" />
						<rect width="48.5%" height="20" y="5" rx="5" ry="5" x="51.5%" />';
				break;
			case '1_3,1_3,1_3':
				$svg = '<rect width="31.3%" height="20" y="5" rx="5" ry="5" />
						<rect width="31.3%" height="20" y="5" rx="5" ry="5" x="34.3%" />
						<rect width="31.3%" height="20" y="5" rx="5" ry="5" x="68.6%" />';
				break;
			case '1_4,1_4,1_4,1_4':
				$svg = '<rect width="22.75%" height="20" y="5" rx="5" ry="5" />
						<rect width="22.75%" height="20" y="5" rx="5" ry="5" x="25.75%" />
						<rect width="22.75%" height="20" y="5" rx="5" ry="5" x="51.5%" />
						<rect width="22.75%" height="20" y="5" rx="5" ry="5" x="77.25%" />';
				break;
			case '1_5,1_5,1_5,1_5,1_5':
				$svg = '<rect width="17.6%" height="20" y="5" rx="5" ry="5" />
						<rect width="17.6%" height="20" y="5" rx="5" ry="5" x="20.6%" />
						<rect width="17.6%" height="20" y="5" rx="5" ry="5" x="41.2%" />
						<rect width="17.6%" height="20" y="5" rx="5" ry="5" x="61.8%" />
						<rect width="17.6%" height="20" y="5" rx="5" ry="5" x="82.4%" />';
				break;
			case '1_6,1_6,1_6,1_6,1_6,1_6':
				$svg = '<rect width="14.16%" height="20" y="5" rx="5" ry="5" />
						<rect width="14.16%" height="20" y="5" rx="5" ry="5" x="17.16%" />
						<rect width="14.16%" height="20" y="5" rx="5" ry="5" x="34.32%" />
						<rect width="14.16%" height="20" y="5" rx="5" ry="5" x="51.48%" />
						<rect width="14.16%" height="20" y="5" rx="5" ry="5" x="68.64%" />
						<rect width="14.16%" height="20" y="5" rx="5" ry="5" x="85.8%" />';
				break;
			case '2_5,3_5':
				$svg = '<rect width="38.5%" height="20" y="5" rx="5" ry="5" />
						<rect width="58.5%" height="20" y="5" rx="5" ry="5" x="41.5%" />';
				break;
			case '3_5,2_5':
				$svg = '<rect width="58.5%" height="20" y="5" rx="5" ry="5" />
						<rect width="38.5%" height="20" y="5" rx="5" ry="5" x="61.5%" />';
				break;
			case '1_3,2_3':
				$svg = '<rect width="31.5%" height="20" y="5" rx="5" ry="5" />
						<rect width="65.5%" height="20" y="5" rx="5" ry="5" x="34.5%" />';
				break;
			case '2_3,1_3':
				$svg = '<rect width="65.5%" height="20" y="5" rx="5" ry="5" />
						<rect width="31.5%" height="20" y="5" rx="5" ry="5" x="68.5%" />';
				break;
			case '1_4,3_4':
				$svg = '<rect width="23.5%" height="20" y="5" rx="5" ry="5" />
						<rect width="73.5%" height="20" y="5" rx="5" ry="5" x="26.5%" />';
				break;
			case '3_4,1_4':
				$svg = '<rect width="73.5%" height="20" y="5" rx="5" ry="5" />
						<rect width="23.5%" height="20" y="5" rx="5" ry="5" x="76.5%" />';
				break;
			case '1_4,1_2,1_4':
				$svg = '<rect width="23.5%" height="20" y="5" rx="5" ry="5" />
						<rect width="47%" height="20" y="5" rx="5" ry="5" x="26.5%" />
						<rect width="23.5%" height="20" y="5" rx="5" ry="5" x="76.5%" />';
				break;
			case '1_5,3_5,1_5':
				$svg = '<rect width="18.5%" height="20" y="5" rx="5" ry="5" />
						<rect width="57%" height="20" y="5" rx="5" ry="5" x="21.5%" />
						<rect width="18.5%" height="20" y="5" rx="5" ry="5" x="81.5%" />';
				break;
			case '1_4,1_4,1_2':
				$svg = '<rect width="23.5%" height="20" y="5" rx="5" ry="5" />
						<rect width="23.5%" height="20" y="5" rx="5" ry="5" x="26.5%" />
						<rect width="47%" height="20" y="5" rx="5" ry="5" x="53%" />';
				break;
			case '1_2,1_4,1_4':
				$svg = '<rect width="47%" height="20" y="5" rx="5" ry="5" />
						<rect width="23.5%" height="20" y="5" rx="5" ry="5" x="50%" />
						<rect width="23.5%" height="20" y="5" rx="5" ry="5" x="76.5%" />';
				break;
			case '1_5,1_5,3_5':
				$svg = '<rect width="18.5%" height="20" y="5" rx="5" ry="5" />
						<rect width="18.5%" height="20" y="5" rx="5" ry="5" x="21.5%" />
						<rect width="57%" height="20" y="5" rx="5" ry="5" x="43%" />';
				break;
			case '3_5,1_5,1_5':
				$svg = '<rect width="57%" height="20" y="5" rx="5" ry="5" />
						<rect width="18.5%" height="20" y="5" rx="5" ry="5" x="60%" />
						<rect width="18.5%" height="20" y="5" rx="5" ry="5" x="81.5%" />';
				break;
			case '1_6,1_6,1_6,1_2':
				$svg = '<rect width="14.6%" height="20" y="5" rx="5" ry="5" />
						<rect width="14.6%" height="20" y="5" rx="5" ry="5" x="18.1%" />
						<rect width="14.6%" height="20" y="5" rx="5" ry="5" x="36.2%" />
						<rect width="45.7%" height="20" y="5" rx="5" ry="5" x="54.3%" />';
				break;
			case '1_2,1_6,1_6,1_6':
				$svg = '<rect width="47%" height="20" y="5" rx="5" ry="5" />
						<rect width="14.6%" height="20" y="5" rx="5" ry="5" x="50%" />
						<rect width="14.6%" height="20" y="5" rx="5" ry="5" x="67.6%" />
						<rect width="14.6%" height="20" y="5" rx="5" ry="5" x="85.2%" />';
				break;
		}

		return $svg;
	}
}

/**
 * Get image metadata responsive sizes
 *
 * @since 3.27.3
 *
 * @param string $image_src     The 'src' of the image.
 * @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
 * @param array  $size          Array of width and height values in pixels (in that order).
 *
 * @return array|bool
 */
function et_builder_responsive_image_metadata( $image_src, $image_meta = null, $size = null ) {
	$cache = ET_Core_Cache_File::get( 'image_responsive_metadata' );

	// Normalize image URL.
	$normalized_url = et_attachment_normalize_url( $image_src );

	if ( isset( $cache[ $normalized_url ] ) ) {
		if ( et_core_is_uploads_dir_url( $normalized_url ) ) {
			return $cache[ $normalized_url ];
		}

		unset( $cache[ $normalized_url ] );
		ET_Core_Cache_File::set( 'image_responsive_metadata', $cache );
	}

	$responsive_sizes = array();

	$image_id = is_numeric( $image_src ) ? intval( $image_src ) : et_get_attachment_id_by_url( $image_src );

	if ( ! $image_id ) {
		return array();
	}

	if ( is_null( $image_meta ) ) {
		$image_meta = wp_get_attachment_metadata( $image_id );
	}

	if ( ! $image_meta || empty( $image_meta['sizes'] ) ) {
		return array();
	}

	if ( is_null( $size ) ) {
		$size = et_get_attachment_size_by_url( $image_src );
	}

	if ( 'full' === $size && isset( $image_meta['width'] ) && isset( $image_meta['height'] ) ) {
		$size = array(
			absint( $image_meta['width'] ),
			absint( $image_meta['height'] ),
		);
	} elseif ( is_string( $size ) && ! empty( $image_meta['sizes'][ $size ] ) ) {
		$size = array(
			absint( $image_meta['sizes'][ $size ]['width'] ),
			absint( $image_meta['sizes'][ $size ]['height'] ),
		);
	}

	if ( ! $size || ! is_array( $size ) ) {
		return array();
	}

	foreach ( $image_meta['sizes'] as $size_key => $size_data ) {
		if ( strpos( $size_key, 'et-pb-image--responsive--' ) !== 0 ) {
			continue;
		}

		if ( is_array( $size ) && $size[0] < $size_data['width'] ) {
			$responsive_sizes[ $size_data['width'] ] = false;
		} else {
			$responsive_sizes[ $size_data['width'] ] = $size_data;
		}
	}

	if ( $responsive_sizes ) {
		ksort( $responsive_sizes );

		// Cache the responsive sizes data.
		if ( et_core_is_uploads_dir_url( $normalized_url ) ) {
			$cache[ $normalized_url ] = $responsive_sizes;
			ET_Core_Cache_File::set( 'image_responsive_metadata', $cache );
		}
	}

	return $responsive_sizes;
}

if ( ! function_exists( 'et_filter_wp_calculate_image_srcset' ) ) :
	/**
	 * Filters an image's 'srcset' sources.
	 *
	 * @since 3.27
	 *
	 * @param array  $sources {
	 *     One or more arrays of source data to include in the 'srcset'.
	 *
	 *     @type array $width {
	 *         @type string $url        The URL of an image source.
	 *         @type string $descriptor The descriptor type used in the image candidate string,
	 *                                  either 'w' or 'x'.
	 *         @type int    $value      The source width if paired with a 'w' descriptor, or a
	 *                                  pixel density value if paired with an 'x' descriptor.
	 *     }
	 * }
	 * @param array  $size_array    Array of width and height values in pixels (in that order).
	 * @param string $image_src     The 'src' of the image.
	 * @param array  $image_meta    The image meta data as returned by 'wp_get_attachment_metadata()'.
	 *
	 * @return array
	 */
	function et_filter_wp_calculate_image_srcset( $sources, $size_array, $image_src, $image_meta ) {
		// Do not filter when in wp-admin area.
		if ( is_admin() ) {
			return $sources;
		}

		$responsive_sources = array();

		if ( ! et_is_responsive_images_enabled() ) {
			return $responsive_sources;
		}

		if ( is_string( $size_array ) ) {
			$size_array = et_get_attachment_size_by_url( $image_src );
		}

		if ( is_string( $size_array ) && $image_meta ) {
			if ( 'full' === $size_array ) {
				$size_array = array(
					absint( $image_meta['width'] ),
					absint( $image_meta['height'] ),
				);
			} elseif ( ! empty( $image_meta['sizes'][ $size_array ] ) ) {
				$size_array = array(
					absint( $image_meta['sizes'][ $size_array ]['width'] ),
					absint( $image_meta['sizes'][ $size_array ]['height'] ),
				);
			}
		}

		if ( ! is_array( $size_array ) ) {
			return $responsive_sources;
		}

		$responsive_metadata = et_builder_responsive_image_metadata( $image_src, $image_meta, $size_array );

		if ( $responsive_metadata ) {
			foreach ( $responsive_metadata as $max_width => $size_data ) {
				if ( ! $size_data ) {
					continue;
				}

				// In some SVG images, the value of `$max_width` is 0, in those cases we can set `$max_width` from `$size_array`.
				if ( ! $max_width ) {
					$max_width = $size_array[0];
				}

				$responsive_sources[ $max_width ] = array(
					'url'        => str_replace( basename( $image_src ), $size_data['file'], $image_src ),
					'descriptor' => 'w',
					'value'      => $max_width,
				);
			}

			if ( $responsive_sources && $size_array[0] > $max_width ) {
				$responsive_sources[ $size_array[0] ] = array(
					'url'        => $image_src,
					'descriptor' => 'w',
					'value'      => $size_array[0],
				);
			}

			if ( $responsive_sources ) {
				krsort( $responsive_sources );
			}
		} else {
			$responsive_sources = $sources;
		}

		return $responsive_sources;
	}
endif;
add_filter( 'wp_calculate_image_srcset', 'et_filter_wp_calculate_image_srcset', 10, 4 );

if ( ! function_exists( 'et_filter_wp_calculate_image_sizes' ) ) :
	/**
	 * Filters the output of 'wp_calculate_image_sizes()'.
	 *
	 * @since 3.27.3
	 *
	 * @param string       $sizes         A source size value for use in a 'sizes' attribute.
	 * @param array|string $size          Requested size. Image size or array of width and height values
	 *                                    in pixels (in that order).
	 * @param string|null  $image_src     The URL to the image file or null.
	 * @param array|null   $image_meta    The image meta data as returned by wp_get_attachment_metadata() or null.
	 *
	 * @return string|bool A valid source size value for use in a 'sizes' attribute or false.
	 */
	function et_filter_wp_calculate_image_sizes( $sizes, $size, $image_src, $image_meta ) {
		// Do not filter when in wp-admin area.
		if ( is_admin() ) {
			return $sizes;
		}

		$responsive_sizes = '';

		if ( ! et_is_responsive_images_enabled() ) {
			return $responsive_sizes;
		}

		if ( is_string( $size ) ) {
			$size = et_get_attachment_size_by_url( $image_src );
		}

		if ( is_string( $size ) && $image_meta ) {
			if ( 'full' === $size ) {
				$size = array(
					absint( $image_meta['width'] ),
					absint( $image_meta['height'] ),
				);
			} elseif ( ! empty( $image_meta['sizes'][ $size ] ) ) {
				$size = array(
					absint( $image_meta['sizes'][ $size ]['width'] ),
					absint( $image_meta['sizes'][ $size ]['height'] ),
				);
			}
		}

		if ( ! is_array( $size ) ) {
			return $responsive_sizes;
		}

		$responsive_metadata = et_builder_responsive_image_metadata( $image_src, $image_meta, $size );

		if ( $responsive_metadata ) {
			$max_width  = 0;
			$prev_width = 0;
			$sizes_temp = array();

			foreach ( $responsive_metadata as $max_width => $size_data ) {
				if ( ! $size_data ) {
					continue;
				}

				if ( $prev_width ) {
					$sizes_temp[ $max_width ] = sprintf( '(min-width: %2$dpx) and (max-width: %1$dpx) %1$dpx', $max_width, ( $prev_width + 1 ) );
				} else {
					$sizes_temp[ $max_width ] = sprintf( '(min-width: %2$dpx) and (max-width: %1$dpx) %1$dpx', $max_width, $prev_width );
				}

				$prev_width = $max_width;
			}

			if ( $sizes_temp && $size[0] > $prev_width ) {
				$sizes_temp[ $size[0] ] = sprintf( '(min-width: %2$dpx) %1$dpx', $size[0], ( $prev_width + 1 ) );
			}

			if ( $sizes_temp ) {
				$sizes_temp[] = '100vw';
			}

			$responsive_sizes = implode( ', ', $sizes_temp );
		} else {
			$responsive_sizes = $sizes;
		}

		return $responsive_sizes;
	}
endif;
add_filter( 'wp_calculate_image_sizes', 'et_filter_wp_calculate_image_sizes', 10, 4 );

/**
 * Register and localize assets early enough to avoid conflicts
 * with third party plugins that use the same assets.
 *
 * @since 4.0.9
 */
function et_builder_register_assets() {
	global $wp_version;

	$root             = ET_BUILDER_URI;
	$wp_major_version = substr( $wp_version, 0, 3 );

	// phpcs:disable WordPress.WP.EnqueuedResourceParameters -- These scripts are inside WordPress core. In order to always load latest script, version number is set false.
	wp_register_script( 'iris', admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
	wp_register_script( 'wp-color-picker', admin_url( 'js/color-picker.min.js' ), array( 'iris' ), false, 1 );
	// phpcs:enable

	$wp_color_picker_l10n = array(
		'clear'         => esc_html__( 'Clear', 'et_builder' ),
		'defaultString' => et_builder_i18n( 'Default' ),
		'pick'          => esc_html__( 'Select Color', 'et_builder' ),
	);

	if ( version_compare( $wp_major_version, '4.9', '>=' ) ) {
		wp_register_script( 'wp-color-picker-alpha', "{$root}/scripts/ext/wp-color-picker-alpha.min.js", array( 'jquery', 'wp-color-picker' ), ET_BUILDER_VERSION, true );
	} else {
		wp_register_script( 'wp-color-picker-alpha', "{$root}/scripts/ext/wp-color-picker-alpha-48.min.js", array( 'jquery', 'wp-color-picker' ), ET_BUILDER_VERSION, true );
		$wp_color_picker_l10n['current'] = esc_html__( 'Current Color', 'et_builder' );
	}

	wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $wp_color_picker_l10n );
}
add_action( 'init', 'et_builder_register_assets', 11 );

if ( ! function_exists( 'et_set_parallax_bg_wrap_border_radius' ) ) :
	/**
	 * Set border radius to parallax background wrapper.
	 *
	 * @since 4.4.8
	 *
	 * @param array  $props Module settings.
	 * @param string $module Module slug.
	 * @param string $order_class Module main css element.
	 *
	 * @return void
	 */
	function et_set_parallax_bg_wrap_border_radius( $props, $module, $order_class ) {
		$border_radius_values        = et_pb_responsive_options()->get_property_values( $props, 'border_radii' );
		$border_radius_hover_enabled = et_builder_module_prop( 'border_radii__hover_enabled', $props, '' );
		$border_radius_hover_values  = et_builder_module_prop( 'border_radii__hover', $props, '' );

		foreach ( et_pb_responsive_options()->get_modes() as $device ) {
			if ( 'on||||' === $border_radius_values[ $device ] ) {
				$border_radius_values[ $device ] = '';
				continue;
			}

			$border_radius_values[ $device ] = et_format_parallax_bg_wrap_radius_values( $border_radius_values[ $device ] );
		}

		et_pb_responsive_options()->generate_responsive_css(
			$border_radius_values,
			$order_class . ' .et_parallax_bg_wrap',
			'border-radius',
			$module,
			'',
			'border'
		);

		if ( 'on|hover' === $border_radius_hover_enabled ) {
			$radius_hover_values = et_format_parallax_bg_wrap_radius_values( $border_radius_hover_values );
		} else {
			$radius_hover_values = $border_radius_values['desktop'];
		}

		if ( $radius_hover_values ) {
			$el_style = array(
				'selector'    => $order_class . ':hover .et_parallax_bg_wrap',
				'declaration' => esc_html(
					sprintf(
						'border-radius: %1$s;',
						$radius_hover_values
					)
				),
			);
			ET_Builder_Element::set_style( $module, $el_style );
		}
	}
endif;

if ( ! function_exists( 'et_format_parallax_bg_wrap_radius_values' ) ) :
	/**
	 * Get formatted border radius of parallax background wrapper
	 *
	 * @since 4.4.8
	 *
	 * @param string $border_radius_values border radius values.
	 *
	 * @return string
	 */
	function et_format_parallax_bg_wrap_radius_values( $border_radius_values ) {
		$radius_values = array();
		$radius_array  = explode( '|', $border_radius_values );
		$radius_count  = count( $radius_array );

		for ( $i = 1; $i < $radius_count; $i++ ) {
			$radius_values[] = $radius_array[ $i ] ? $radius_array[ $i ] : 0;
		}

		return trim( implode( ' ', $radius_values ) );
	}
endif;

if ( ! function_exists( 'et_builder_generate_css' ) ) {
	/**
	 * Generate CSS.
	 *
	 * @param array $args  Styles arg.
	 *
	 * @return string|void
	 */
	function et_builder_generate_css( $args ) {

		$defaults = array(
			'prefix' => '',
			'suffix' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		/*
		 * Bail early if we have no $selector elements or properties and $value.
		 */
		if ( ! $args['value'] || ! $args['selector'] ) {
			return;
		}

		return sprintf( '%s { %s: %s; }', $args['selector'], $args['style'], $args['prefix'] . $args['value'] . $args['suffix'] );
	}
}

if ( ! function_exists( 'et_builder_generate_css_style' ) ) {
	/**
	 * Generate CSS property.
	 *
	 * @param array $args Styles arg.
	 *
	 * @return string|void
	 */
	function et_builder_generate_css_style( $args ) {

		$defaults = array(
			'prefix' => '',
			'suffix' => '',
		);

		$args = wp_parse_args( $args, $defaults );

		/*
		 * Bail early if we have no style and $value.
		 */
		if ( ! $args['value'] || ! $args['style'] ) {
			return;
		}

		return sprintf( '%s: %s;', $args['style'], $args['prefix'] . $args['value'] . $args['suffix'] );
	}
}

if ( ! function_exists( 'et_builder_default_colors_ajax_update_handler' ) ) :
	/**
	 * Default colors AJAX update handler.
	 *
	 * @since 4.9.0
	 */
	function et_builder_default_colors_ajax_update_handler() {
		// Get nonce from $_POST.
		$nonce = isset( $_POST['et_builder_default_colors_nonce'] ) ? sanitize_text_field( $_POST['et_builder_default_colors_nonce'] ) : '';

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'et_builder_default_colors_update' ) ) {
			wp_send_json_error();
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error();
		}

		// Get default_colors from $_POST.
		$default_colors = isset( $_POST['default_colors'] ) ? sanitize_text_field( wp_unslash( $_POST['default_colors'] ) ) : '';

		et_update_option( 'divi_color_palette', str_replace( ',', '|', $default_colors ) );

		wp_send_json_success();
	}
endif;

add_action( 'wp_ajax_et_builder_default_colors_update', 'et_builder_default_colors_ajax_update_handler' );

if ( ! function_exists( 'et_builder_global_colors_ajax_save_handler' ) ) :
	/**
	 * Global colors AJAX save handler.
	 *
	 * @since 4.9.0
	 */
	function et_builder_global_colors_ajax_save_handler() {
		// Get nonce from $_POST.
		$nonce = isset( $_POST['et_builder_global_colors_save_nonce'] ) ? sanitize_text_field( $_POST['et_builder_global_colors_save_nonce'] ) : '';

		if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, 'et_builder_global_colors_save' ) ) {
			wp_send_json_error();
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error();
		}

		// Get colors from $_POST.
		$post_colors   = filter_input( INPUT_POST, 'global_colors', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$global_colors = array();

		if ( is_array( $post_colors ) ) {
			foreach ( $post_colors as $data_id => $data ) {
				// Drop bad data.
				if ( 'undefined' === $data_id || empty( $data ) ) {
					continue;
				}

				// Sanitize data_id (e.g: gcid-3330f0vf7 ).
				$global_id = sanitize_text_field( $data_id );

				foreach ( $data as $type => $value ) {
					// Sanitize both type (e.g: color, active) and value (color value, yes/no).
					$global_colors[ $global_id ][ sanitize_text_field( $type ) ] = sanitize_text_field( $value );
				}
			}
		}

		if ( empty( $global_colors ) ) {
			wp_send_json_error();
		}

		/**
		 * Fires after global colors are processed.
		 *
		 * @since 4.25.0
		 */
		do_action( 'et_global_colors_saved', $global_colors );

		// Do not save customizer colors into Global Colors setting.
		$excluded_keys = [
			'gcid-primary-color',
			'gcid-secondary-color',
			'gcid-heading-color',
			'gcid-body-color',
		];

		foreach ( $excluded_keys as $excluded_key ) {
			unset( $global_colors[ $excluded_key ] );
		}

		// Global Color data has been sanitized above.
		et_update_option( 'et_global_colors', $global_colors );

		ET_Core_PageResource::remove_static_resources( 'all', 'all' );

		wp_send_json_success();
	}
endif;

add_action( 'wp_ajax_et_builder_global_colors_save', 'et_builder_global_colors_ajax_save_handler' );

if ( ! function_exists( 'et_builder_global_colors_ajax_get_handler' ) ) :
	/**
	 * Global colors AJAX get handler.
	 *
	 * @since 4.19.2
	 */
	function et_builder_global_colors_ajax_get_handler() {
		// Get nonce from $_GET.
		et_core_security_check( 'edit_posts', 'et_builder_global_colors_get', 'et_builder_global_colors_get_nonce', '_GET' );
		wp_send_json_success( [ 'global_colors' => et_builder_get_all_global_colors( true ) ] );
	}
endif;

add_action( 'wp_ajax_et_builder_global_colors_get', 'et_builder_global_colors_ajax_get_handler' );

/**
 * Get a global color info by id.
 *
 * @since 4.9.0
 *
 * @param string $color_id Id of global color.
 *
 * @return array
 */
function et_builder_get_global_color_info( $color_id ) {
	$colors = et_builder_get_all_global_colors( true );

	if ( empty( $colors ) || ! array_key_exists( $color_id, $colors ) ) {
		return null;
	}

	// if replaced value exists, return color info with that replaced id.
	if ( isset( $colors[ $color_id ]['replaced_with'] ) ) {
		$replaced_id = $colors[ $color_id ]['replaced_with'];
		return $colors[ $replaced_id ];
	}

	return $colors[ $color_id ];
}

/**
 * Check if given value is a Global Color Id.
 *
 * @since 4.21.1
 *
 * @param string $attr_value Color value.
 * @return bool
 */
function et_builder_is_global_color( $attr_value ) {
	return 0 === strpos( $attr_value, 'gcid-' );
}

/**
 * Get Global Color by Color Id.
 *
 * @since 4.21.1
 *
 * @param string $color_id Color ID.
 * @return string
 */
function et_builder_get_global_color( $color_id ) {
	$color_info = et_builder_get_global_color_info( $color_id );

	return isset( $color_info['color'] ) ? $color_info['color'] : $color_id;
}

/**
 * Checks if overflow CSS property should be set or not.
 *
 * @since 4.17.4
 *
 * @param bool|string        $overflow_enabled If overflow is enabled (true) or disabled (false) or -x or -y.
 * @param string             $function_name    Module slug.
 * @param ET_Builder_Element $module           Module object.
 *
 * @return bool|string
 */
function et_process_border_radii_options_overflow( $overflow_enabled, $function_name, $module ) {
	if ( in_array( $function_name, [ 'et_pb_section', 'et_pb_row' ], true ) &&
		ET_Builder_Element::module_contains( $module->_original_content, [ 'et_pb_menu', 'et_pb_fullwidth_menu' ] ) ) {
		$overflow_enabled = false;
	}
	return $overflow_enabled;
}
add_filter( 'et_builder_process_advanced_borders_options_radii_overflow_enabled', 'et_process_border_radii_options_overflow', 10, 3 );

/**
 * Adds `fitvidsignore` class to vimeo videos parent tags which have unusual aspect ratios.
 *
 * WordPress adds extra `div` tag as a parent on vimeo videos with unusual aspect ratios so
 * videos would have proper aspect ratio responsively. That causes issues with jQuery `fitvids()`.
 * Ref: https://github.com/elegantthemes/Divi/issues/16116
 *
 * @since 4.17.5
 */
add_filter(
	'oembed_dataparse',
	function( $html, $data ) {
		if ( ! class_exists( 'DOMDocument' ) ) {
			return $html;
		}

		if ( 'Vimeo' !== $data->provider_name ) {
			return $html;
		}

		$doc                 = new DOMDocument();
		$doc_load_html_state = $doc->loadHTML( $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );

		if ( false === $doc_load_html_state ) {
			return $html;
		}

		$extra_div_nodes = $doc->getElementsByTagName( 'div' );

		if ( 0 === $extra_div_nodes->length ) {
			return $html;
		}

		$extra_div_node = $extra_div_nodes[0];
		$extra_div_node->setAttribute( 'class', 'fitvidsignore' );

		$output = $doc->saveHTML();
		$output = false === $output ? $html : $output;

		return $output;
	},
	10,
	2
);

if ( ! function_exists( 'et_pb_get_youtube_url_regex' ) ) :
	/**
	 * Regex to match a YouTube URL from any known/common YouTube URL format.
	 *
	 * Expected YouTube URL Formats.
	 * - https://www.youtube.com/watch?v=XXXX.
	 * - https://www.youtube.com/embed/XXXX.
	 * - https://youtu.be/XXXX.
	 *
	 * To check regex, see: https://regex101.com/r/4FbeMZ/1.
	 *
	 * @since 4.18.1
	 *
	 * @return string YouTube video URL regex.
	 */
	function et_pb_get_youtube_url_regex() {
		return '/^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/i';
	}
endif;

if ( ! function_exists( 'et_pb_normalize_youtube_url' ) ) :
	/**
	 * Normalize a YouTube URL from any known/common YouTube URL format.
	 *
	 * Convert YouTube URL into normalized form: https://www.youtube.com/watch?v=XXXX.
	 * For https://www.youtube.com/watch?v=XXXX to check regex is https://regex101.com/r/B2qLJy/1.
	 * For https://www.youtube.com/embed/XXXX to check regex is https://regex101.com/r/oZ3iNP/1.
	 * For https://youtu.be/XXXX to check regex is https://regex101.com/r/5nqmhF/1.
	 *
	 * @param string $url youtube video url.
	 *
	 * @since 4.18.1
	 *
	 * @return string Normalized YouTube URL.
	 */
	function et_pb_normalize_youtube_url( $url ) {
		preg_match( et_pb_get_youtube_url_regex(), esc_url( $url ), $youtube_embed_video );

		return 'https://www.youtube.com/watch?v=' . $youtube_embed_video[1];
	}
endif;

if ( ! function_exists( 'et_pb_validate_youtube_url' ) ) :
	/**
	 * Validate a YouTube URL from any known/common YouTube URL format.
	 *
	 * For https://www.youtube.com/watch?v=XXXX to check regex is https://regex101.com/r/B2qLJy/1.
	 * For https://www.youtube.com/embed/XXXX to check regex is https://regex101.com/r/oZ3iNP/1.
	 * For https://youtu.be/XXXX to check regex is https://regex101.com/r/5nqmhF/1.
	 *
	 * @param string $url youtube video url.
	 *
	 * @since 4.18.1
	 *
	 * @return bool Whether provided URL is a valid YouTube URL or not.
	 */
	function et_pb_validate_youtube_url( $url ) {
		preg_match( et_pb_get_youtube_url_regex(), $url, $youtube_embed_video );

		return is_array( $youtube_embed_video ) && ! empty( $youtube_embed_video );
	}
endif;

if ( ! function_exists( 'et_update_customizer_colors' ) ) :
	/**
	 * Update customizer colors.
	 *
	 * @param array $global_colors Global colors.
	 *
	 * @since 4.25.0
	 *
	 * @return void
	 */
	function et_update_customizer_colors( $global_colors ) {
		$primary_color   = isset( $global_colors['gcid-primary-color']['color'] )
			? $global_colors['gcid-primary-color']['color']
			: '';
		$secondary_color = isset( $global_colors['gcid-secondary-color']['color'] )
			? $global_colors['gcid-secondary-color']['color']
			: '';
		$heading_color   = isset( $global_colors['gcid-header-color']['color'] )
			? $global_colors['gcid-header-color']['color']
			: '';
		$body_color      = isset( $global_colors['gcid-font-color']['color'] )
			? $global_colors['gcid-font-color']['color']
			: '';

		if ( ! empty( $primary_color ) ) {
			et_update_option( 'accent_color', $primary_color );
		}

		if ( ! empty( $secondary_color ) ) {
			et_update_option( 'secondary_accent_color', $secondary_color );
		}

		if ( ! empty( $heading_color ) ) {
			et_update_option( 'header_color', $heading_color );
		}

		if ( ! empty( $body_color ) ) {
			et_update_option( 'font_color', $body_color );
		}
	}
endif;

add_action( 'et_global_colors_saved', 'et_update_customizer_colors' );

/**
 * Ajax Callback :: Update cusomizer fonts.
 */
function et_update_customizer_fonts() {
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'et_pb_save_customizer_fonts_nonce' ) ) {
		die( -1 );
	}

	if ( ! current_user_can( 'edit_theme_options' ) ) {
		die( -1 );
	}

	$new_heading_font = isset( $_POST['et_pb_heading_font'] ) ? sanitize_text_field( $_POST['et_pb_heading_font'] ) : '';
	$new_body_font   = isset( $_POST['et_pb_body_font'] ) ? sanitize_text_field( $_POST['et_pb_body_font'] ) : '';

	if ( $new_heading_font ) {
		et_update_option( 'heading_font', $new_heading_font );
	}

	if ( $new_body_font ) {
		et_update_option( 'body_font', $new_body_font );
	}
}

add_action( 'wp_ajax_et_update_customizer_fonts', 'et_update_customizer_fonts' );