<?php
/**
 * Handle dynamic content.
 *
 * @package Builder
 */

/**
 * Gets the dynamic content fields related to Product post type.
 *
 * @since 3.29
 *
 * @return array
 */
function et_builder_get_product_dynamic_content_fields() {
	return array(
		'product_breadcrumb'             => array(
			'label' => esc_html__( 'Product Breadcrumb', 'et_builder' ),
			'type'  => 'text',
		),
		'product_price'                  => array(
			'label' => esc_html__( 'Product Price', 'et_builder' ),
			'type'  => 'text',
		),
		'product_description'            => array(
			'label' => esc_html__( 'Product Description', 'et_builder' ),
			'type'  => 'text',
		),
		'product_short_description'      => array(
			'label' => esc_html__( 'Product Short Description', 'et_builder' ),
			'type'  => 'text',
		),
		'product_reviews_count'          => array(
			'label' => esc_html__( 'Product Reviews Count', 'et_builder' ),
			'type'  => 'text',
		),
		'product_sku'                    => array(
			'label' => esc_html__( 'Product SKU', 'et_builder' ),
			'type'  => 'text',
		),
		'product_reviews'                => array(
			'label'  => esc_html__( 'Product Reviews', 'et_builder' ),
			'type'   => 'text',
			'fields' => array(
				'enable_title' => array(
					'label'   => esc_html__( 'Enable Title', 'et_builder' ),
					'type'    => 'yes_no_button',
					'options' => array(
						'on'  => et_builder_i18n( 'Yes' ),
						'off' => et_builder_i18n( 'No' ),
					),
					'default' => 'on',
				),
			),
		),
		'product_additional_information' => array(
			'label' => esc_html__( 'Product Additional Information', 'et_builder' ),
			'type'  => 'text',
			'fields' => array(
				'enable_title' => array(
					'label'   => esc_html__( 'Enable Title', 'et_builder' ),
					'type'    => 'yes_no_button',
					'options' => array(
						'on'  => et_builder_i18n( 'Yes' ),
						'off' => et_builder_i18n( 'No' ),
					),
					'default' => 'on',
				),
			),
		),
		'product_reviews_tab'            => array(
			'label' => esc_html__( 'Product Reviews', 'et_builder' ),
			'type'  => 'url',
		),
	);
}

/**
 * Get built-in dynamic content fields.
 *
 * @since 3.17.2
 *
 * @param integer $post_id Post Id.
 *
 * @return array[]
 */
function et_builder_get_built_in_dynamic_content_fields( $post_id ) {
	$cache_key = 'et_builder_get_built_in_dynamic_content_fields';

	if ( et_core_cache_has( $cache_key ) ) {
		return et_core_cache_get( $cache_key );
	}

	$post_type                = get_post_type( $post_id );
	$post_type                = $post_type ? $post_type : 'post';
	$post_type_object         = get_post_type_object( $post_type );
	$post_type_label          = $post_type_object->labels->singular_name;
	$post_taxonomy_types      = et_builder_get_taxonomy_types( $post_type );
	$tag_taxonomy_post_type   = $post_type;
	$fields                   = array();
	$before_after_field_types = array( 'text', 'any' );

	if ( et_theme_builder_is_layout_post_type( $post_type ) ) {
		$post_type_label        = esc_html__( 'Post', 'et_builder' );
		$tag_taxonomy_post_type = 'post';
		$public_post_types      = array_keys( et_builder_get_public_post_types() );

		foreach ( $public_post_types as $public_post_type ) {
			$post_taxonomy_types = array_merge(
				$post_taxonomy_types,
				et_builder_get_taxonomy_types( $public_post_type )
			);
		}
	}

	$default_category_type = 'post' === $post_type ? 'category' : "{$post_type}_category";

	if ( ! isset( $post_taxonomy_types[ $default_category_type ] ) ) {
		$default_category_type = 'category';

		if ( ! empty( $post_taxonomy_types ) ) {
			// Use the 1st available taxonomy as the default value.
			$default_category_type = array_keys( $post_taxonomy_types );
			$default_category_type = $default_category_type[0];
		}
	}

	$date_format_options = array(
		'default' => et_builder_i18n( 'Default' ),
		'M j, Y'  => esc_html__( 'Aug 6, 1999 (M j, Y)', 'et_builder' ),
		'F d, Y'  => esc_html__( 'August 06, 1999 (F d, Y)', 'et_builder' ),
		'm/d/Y'   => esc_html__( '08/06/1999 (m/d/Y)', 'et_builder' ),
		'm.d.Y'   => esc_html__( '08.06.1999 (m.d.Y)', 'et_builder' ),
		'j M, Y'  => esc_html__( '6 Aug, 1999 (j M, Y)', 'et_builder' ),
		'l, M d'  => esc_html__( 'Tuesday, Aug 06 (l, M d)', 'et_builder' ),
		'custom'  => esc_html__( 'Custom', 'et_builder' ),
	);

	$fields['post_title'] = array(
		// Translators: %1$s: Post type name.
		'label' => esc_html( sprintf( __( '%1$s/Archive Title', 'et_builder' ), $post_type_label ) ),
		'type'  => 'text',
	);

	$fields['post_excerpt'] = array(
		// Translators: %1$s: Post type name.
		'label'  => esc_html( sprintf( __( '%1$s Excerpt', 'et_builder' ), $post_type_label ) ),
		'type'   => 'text',
		'fields' => array(
			'words'           => array(
				'label'   => esc_html__( 'Number of Words', 'et_builder' ),
				'type'    => 'text',
				'default' => '',
			),
			'read_more_label' => array(
				'label'   => esc_html__( 'Read More Text', 'et_builder' ),
				'type'    => 'text',
				'default' => '',
			),
		),
	);

	$fields['post_date'] = array(
		// Translators: %1$s: Post type name.
		'label'  => esc_html( sprintf( __( '%1$s Publish Date', 'et_builder' ), $post_type_label ) ),
		'type'   => 'text',
		'fields' => array(
			'date_format'        => array(
				'label'   => esc_html__( 'Date Format', 'et_builder' ),
				'type'    => 'select',
				'options' => $date_format_options,
				'default' => 'default',
			),
			'custom_date_format' => array(
				'label'   => esc_html__( 'Custom Date Format', 'et_builder' ),
				'type'    => 'text',
				'default' => '',
				'show_if' => array(
					'date_format' => 'custom',
				),
			),
		),
	);

	$fields['post_comment_count'] = array(
		// Translators: %1$s: Post type name.
		'label'  => esc_html( sprintf( __( '%1$s Comment Count', 'et_builder' ), $post_type_label ) ),
		'type'   => 'text',
		'fields' => array(
			'link_to_comments_page' => array(
				'label'   => esc_html__( 'Link to Comments Area', 'et_builder' ),
				'type'    => 'yes_no_button',
				'options' => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default' => 'on',
			),
		),
	);

	if ( ! empty( $post_taxonomy_types ) ) {
		$fields['post_categories'] = array(
			// Translators: %1$s: Post type name.
			'label'  => esc_html( sprintf( __( '%1$s Categories', 'et_builder' ), $post_type_label ) ),
			'type'   => 'text',
			'fields' => array(
				'link_to_term_page' => array(
					'label'   => esc_html__( 'Link to Category Index Pages', 'et_builder' ),
					'type'    => 'yes_no_button',
					'options' => array(
						'on'  => et_builder_i18n( 'Yes' ),
						'off' => et_builder_i18n( 'No' ),
					),
					'default' => 'on',
				),
				'separator'         => array(
					'label'   => esc_html__( 'Categories Separator', 'et_builder' ),
					'type'    => 'text',
					'default' => ' | ',
				),
				'category_type'     => array(
					'label'   => esc_html__( 'Category Type', 'et_builder' ),
					'type'    => 'select',
					'options' => $post_taxonomy_types,
					'default' => $default_category_type,
				),
			),
		);
	}

	// Fill in tag taxonomies.
	if ( isset( $post_taxonomy_types[ "{$tag_taxonomy_post_type}_tag" ] ) ) {
		$fields['post_tags'] = array(
			// Translators: %1$s: Post type name.
			'label'  => esc_html( sprintf( __( '%1$s Tags', 'et_builder' ), $post_type_label ) ),
			'type'   => 'text',
			'fields' => array(
				'link_to_term_page' => array(
					'label'   => esc_html__( 'Link to Tag Index Pages', 'et_builder' ),
					'type'    => 'yes_no_button',
					'options' => array(
						'on'  => et_builder_i18n( 'Yes' ),
						'off' => et_builder_i18n( 'No' ),
					),
					'default' => 'on',
				),
				'separator'         => array(
					'label'   => esc_html__( 'Tags Separator', 'et_builder' ),
					'type'    => 'text',
					'default' => ' | ',
				),
				'category_type'     => array(
					'label'   => esc_html__( 'Category Type', 'et_builder' ),
					'type'    => 'select',
					'options' => $post_taxonomy_types,
					'default' => "{$tag_taxonomy_post_type}_tag",
				),
			),
		);
	}

	$fields['post_link'] = array(
		// Translators: %1$s: Post type name.
		'label'  => esc_html( sprintf( __( '%1$s Link', 'et_builder' ), $post_type_label ) ),
		'type'   => 'text',
		'fields' => array(
			'text'        => array(
				'label'   => esc_html__( 'Link Text', 'et_builder' ),
				'type'    => 'select',
				'options' => array(
					// Translators: %1$s: Post type name.
					'post_title' => esc_html( sprintf( __( '%1$s Title', 'et_builder' ), $post_type_label ) ),
					'custom'     => esc_html__( 'Custom', 'et_builder' ),
				),
				'default' => 'post_title',
			),
			'custom_text' => array(
				'label'   => esc_html__( 'Custom Link Text', 'et_builder' ),
				'type'    => 'text',
				'default' => '',
				'show_if' => array(
					'text' => 'custom',
				),
			),
		),
	);

	$fields['post_author'] = array(
		// Translators: %1$s: Post type name.
		'label'  => esc_html( sprintf( __( '%1$s Author', 'et_builder' ), $post_type_label ) ),
		'type'   => 'text',
		'fields' => array(
			'name_format'      => array(
				'label'   => esc_html__( 'Name Format', 'et_builder' ),
				'type'    => 'select',
				'options' => array(
					'display_name'    => esc_html__( 'Public Display Name', 'et_builder' ),
					'first_last_name' => esc_html__( 'First & Last Name', 'et_builder' ),
					'last_first_name' => esc_html__( 'Last, First Name', 'et_builder' ),
					'first_name'      => esc_html__( 'First Name', 'et_builder' ),
					'last_name'       => esc_html__( 'Last Name', 'et_builder' ),
					'nickname'        => esc_html__( 'Nickname', 'et_builder' ),
					'username'        => esc_html__( 'Username', 'et_builder' ),
				),
				'default' => 'display_name',
			),
			'link'             => array(
				'label'   => esc_html__( 'Link Name', 'et_builder' ),
				'type'    => 'yes_no_button',
				'options' => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default' => 'off',
			),
			'link_destination' => array(
				'label'   => esc_html__( 'Link Destination', 'et_builder' ),
				'type'    => 'select',
				'options' => array(
					'author_archive' => esc_html__( 'Author Archive Page', 'et_builder' ),
					'author_website' => esc_html__( 'Author Website', 'et_builder' ),
				),
				'default' => 'author_archive',
				'show_if' => array(
					'link' => 'on',
				),
			),
		),
	);

	$fields['post_author_bio'] = array(
		'label' => esc_html__( 'Author Bio', 'et_builder' ),
		'type'  => 'text',
	);

	if ( et_builder_tb_enabled() ) {
		$fields['term_description'] = array(
			'label' => esc_html__( 'Category Description', 'et_builder' ),
			'type'  => 'text',
		);
	}

	$fields['site_title'] = array(
		'label' => esc_html__( 'Site Title', 'et_builder' ),
		'type'  => 'text',
	);

	$fields['site_tagline'] = array(
		'label' => esc_html__( 'Site Tagline', 'et_builder' ),
		'type'  => 'text',
	);

	$fields['current_date'] = array(
		'label'  => esc_html__( 'Current Date', 'et_builder' ),
		'type'   => 'text',
		'fields' => array(
			'date_format'        => array(
				'label'   => esc_html__( 'Date Format', 'et_builder' ),
				'type'    => 'select',
				'options' => $date_format_options,
				'default' => 'default',
			),
			'custom_date_format' => array(
				'label'   => esc_html__( 'Custom Date Format', 'et_builder' ),
				'type'    => 'text',
				'default' => '',
				'show_if' => array(
					'date_format' => 'custom',
				),
			),
		),
	);

	$fields['post_link_url'] = array(
		// Translators: %1$s: Post type name.
		'label' => esc_html( sprintf( __( 'Current %1$s Link', 'et_builder' ), $post_type_label ) ),
		'type'  => 'url',
	);

	$fields['post_author_url'] = array(
		'label' => esc_html__( 'Author Page Link', 'et_builder' ),
		'type'  => 'url',
	);

	$fields['home_url'] = array(
		'label' => esc_html__( 'Homepage Link', 'et_builder' ),
		'type'  => 'url',
	);

	// Fill in post type URL options.
	$post_types = et_builder_get_public_post_types();
	foreach ( $post_types as $public_post_type ) {
		$public_post_type_label = $public_post_type->labels->singular_name;
		$key                    = 'post_link_url_' . $public_post_type->name;

		$fields[ $key ] = array(
			// Translators: %1$s: Post type name.
			'label'  => esc_html( sprintf( __( '%1$s Link', 'et_builder' ), $public_post_type_label ) ),
			'type'   => 'url',
			'fields' => array(
				'post_id' => array(
					'label'     => $public_post_type_label,
					'type'      => 'select_post',
					'post_type' => $public_post_type->name,
					'default'   => '',
				),
			),
		);
	}

	$fields['post_featured_image'] = array(
		'label' => esc_html__( 'Featured Image', 'et_builder' ),
		'type'  => 'image',
	);

	$fields['post_author_profile_picture'] = array(
		// Translators: %1$s: Post type name.
		'label' => esc_html__( 'Author Profile Picture', 'et_builder' ),
		'type'  => 'image',
	);

	$fields['site_logo'] = array(
		'label' => esc_html__( 'Site Logo', 'et_builder' ),
		'type'  => 'image',
	);

	$fields['post_meta_key'] = array(
		'label'  => esc_html__( 'Manual Custom Field Name', 'et_builder' ),
		'type'   => 'any',
		'group'  => esc_html__( 'Custom Fields', 'et_builder' ),
		'fields' => array(
			'meta_key' => array(
				'label' => esc_html__( 'Field Name', 'et_builder' ),
				'type'  => 'text',
			),
		),
	);

	if ( current_user_can( 'unfiltered_html' ) ) {
		$fields['post_meta_key']['fields']['enable_html'] = array(
			'label'   => esc_html__( 'Enable raw HTML', 'et_builder' ),
			'type'    => 'yes_no_button',
			'options' => array(
				'on'  => et_builder_i18n( 'Yes' ),
				'off' => et_builder_i18n( 'No' ),
			),
			'default' => 'off',
			'show_on' => 'text',
		);
	}
	/*
	 * Include Product dynamic fields on Product post type.
	 *
	 * This is enforced based on the discussion at
	 *
	 * @see https://github.com/elegantthemes/Divi/issues/15921#issuecomment-512707471
	 */
	if ( et_is_woocommerce_plugin_active() && ( 'product' === $post_type || et_theme_builder_is_layout_post_type( $post_type ) ) ) {
		$fields = array_merge( $fields, et_builder_get_product_dynamic_content_fields() );
	}

	// Fill in boilerplate.
	foreach ( $fields as $key => $field ) {
		$fields[ $key ]['custom'] = false;
		$fields[ $key ]['group']  = et_()->array_get( $fields, "{$key}.group", 'Default' );

		if ( in_array( $field['type'], $before_after_field_types, true ) ) {
			$settings = isset( $field['fields'] ) ? $field['fields'] : array();
			$settings = array_merge(
				array(
					'before' => array(
						'label'   => et_builder_i18n( 'Before' ),
						'type'    => 'text',
						'default' => '',
					),
					'after'  => array(
						'label'   => et_builder_i18n( 'After' ),
						'type'    => 'text',
						'default' => '',
					),
				),
				$settings
			);

			$fields[ $key ]['fields'] = $settings;
		}
	}

	et_core_cache_add( $cache_key, $fields );

	return $fields;
}

/**
 * Clear dynamic content fields cache whenever a custom post type is registered.
 *
 * @since 3.26.7
 *
 * @return void
 */
function et_builder_clear_get_built_in_dynamic_content_fields_cache() {
	et_core_cache_delete( 'et_builder_get_built_in_dynamic_content_fields' );
}
add_action( 'registered_post_type', 'et_builder_clear_get_built_in_dynamic_content_fields_cache' );

/**
 * Get all public taxonomies associated with a given post type.
 *
 * @since 3.17.2
 *
 * @param string $post_type Post type.
 *
 * @return array
 */
function et_builder_get_taxonomy_types( $post_type ) {
	$taxonomies = get_object_taxonomies( $post_type, 'object' );
	$list       = array();

	if ( empty( $taxonomies ) ) {
		return $list;
	}

	foreach ( $taxonomies as $taxonomy ) {
		if ( ! empty( $taxonomy ) && $taxonomy->public && $taxonomy->show_ui ) {
			$list[ $taxonomy->name ] = $taxonomy->label;
		}
	}

	return $list;
}

/**
 * Get a user-friendly custom field label for the given meta key.
 *
 * @since 4.4.4
 *
 * @param string $key Post meta key.
 *
 * @return string
 */
function et_builder_get_dynamic_content_custom_field_label( $key ) {
	$label = str_replace( array( '_', '-' ), ' ', $key );
	$label = ucwords( $label );
	$label = trim( $label );
	return $label;
}

/**
 * Get all dynamic content fields in a given string.
 *
 * @since 4.4.4
 *
 * @param string $content Value content.
 *
 * @return array
 */
function et_builder_get_dynamic_contents( $content ) {
	$is_matched = preg_match_all( ET_THEME_BUILDER_DYNAMIC_CONTENT_REGEX, $content, $matches );

	if ( ! $is_matched ) {
		return array();
	}

	return $matches[0];
}

/**
 * Get all meta keys used as dynamic content in the content of a post.
 *
 * @param integer $post_id Post Id.
 *
 * @return array
 */
function et_builder_get_used_dynamic_content_meta_keys( $post_id ) {
	$transient      = 'et_builder_dynamic_content_used_meta_keys_' . $post_id;
	$used_meta_keys = get_transient( $transient );

	if ( false !== $used_meta_keys ) {
		return $used_meta_keys;
	}

	// The most used meta keys will change from time to time so we will also retrieve the used meta keys in the layout
	// content to make sure that the previously selected meta keys always stay in the list even if they are not in the
	// most used meta keys list anymore.
	$layout_post      = get_post( $post_id );
	$used_meta_keys   = array();
	$dynamic_contents = et_builder_get_dynamic_contents( $layout_post->post_content );

	foreach ( $dynamic_contents as $dynamic_content ) {
		$dynamic_content = et_builder_parse_dynamic_content( $dynamic_content );
		$key             = $dynamic_content->get_content();

		if ( et_()->starts_with( $key, 'custom_meta_' ) ) {
			$meta_key         = substr( $key, strlen( 'custom_meta_' ) );
			$used_meta_keys[] = $meta_key;
		}
	}

	set_transient( $transient, $used_meta_keys, 5 * MINUTE_IN_SECONDS );

	return $used_meta_keys;
}

/**
 * Get most used meta keys on public post types.
 *
 * @since 4.4.4
 *
 * @return string[]
 */
function et_builder_get_most_used_post_meta_keys() {
	global $wpdb;

	$most_used_meta_keys = get_transient( 'et_builder_most_used_meta_keys' );
	if ( false !== $most_used_meta_keys ) {
		return $most_used_meta_keys;
	}

	$public_post_types = array_keys( et_builder_get_public_post_types() );
	$post_types        = "'" . implode( "','", esc_sql( $public_post_types ) ) . "'";

	$sql = "SELECT DISTINCT pm.meta_key FROM {$wpdb->postmeta} pm
		INNER JOIN {$wpdb->posts} p ON ( p.ID = pm.post_id AND p.post_type IN ({$post_types}) )
		WHERE pm.meta_key NOT LIKE '\_%'
		GROUP BY pm.meta_key
		ORDER BY COUNT(pm.meta_key) DESC
		LIMIT 50";

	// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- $sql query does not use users/visitor input
	$most_used_meta_keys = $wpdb->get_col( $sql );

	set_transient( 'et_builder_most_used_meta_keys', $most_used_meta_keys, 5 * MINUTE_IN_SECONDS );

	return $most_used_meta_keys;
}

/**
 * Get custom dynamic content fields.
 *
 * @since 3.17.2
 *
 * @param integer $post_id Post Id.
 *
 * @return array[]
 */
function et_builder_get_custom_dynamic_content_fields( $post_id ) {
	$raw_custom_fields = get_post_meta( $post_id );
	$raw_custom_fields = is_array( $raw_custom_fields ) ? $raw_custom_fields : array();
	$custom_fields     = array();

	/**
	 * Filter post meta accepted as custom field options in dynamic content.
	 * Post meta prefixed with `_` is considered hidden from dynamic content options by default
	 * due to its nature as "hidden meta keys". This filter allows third parties to
	 * circumvent this limitation.
	 *
	 * @since 3.17.2
	 *
	 * @param string[] $meta_keys
	 * @param integer $post_id
	 *
	 * @return string[]
	 */
	$display_hidden_meta_keys = apply_filters( 'et_builder_dynamic_content_display_hidden_meta_keys', array(), $post_id );

	// Custom dynamic fields to be displayed on the TB.
	if ( et_theme_builder_is_layout_post_type( get_post_type( $post_id ) ) ) {
		$raw_custom_fields = array_merge(
			$raw_custom_fields,
			array_flip( et_builder_get_most_used_post_meta_keys() ),
			array_flip( et_builder_get_used_dynamic_content_meta_keys( $post_id ) )
		);
	}

	foreach ( $raw_custom_fields as $key => $values ) {
		if ( substr( $key, 0, 1 ) === '_' && ! in_array( $key, $display_hidden_meta_keys, true ) ) {
			// Ignore hidden meta keys.
			continue;
		}

		if ( substr( $key, 0, 3 ) === 'et_' ) {
			// Ignore ET meta keys as they are not suitable for dynamic content use.
			continue;
		}

		$label = et_builder_get_dynamic_content_custom_field_label( $key );

		/**
		 * Filter the display label for a custom field.
		 *
		 * @since 3.17.2
		 *
		 * @param string $label
		 * @param string $meta_key
		 */
		$label = apply_filters( 'et_builder_dynamic_content_custom_field_label', $label, $key );

		$field = array(
			'label'    => $label,
			'type'     => 'any',
			'fields'   => array(
				'before' => array(
					'label'   => et_builder_i18n( 'Before' ),
					'type'    => 'text',
					'default' => '',
					'show_on' => 'text',
				),
				'after'  => array(
					'label'   => et_builder_i18n( 'After' ),
					'type'    => 'text',
					'default' => '',
					'show_on' => 'text',
				),
			),
			'meta_key' => $key,
			'custom'   => true,
			'group'    => __( 'Custom Fields', 'et_builder' ),
		);

		if ( current_user_can( 'unfiltered_html' ) ) {
			$field['fields']['enable_html'] = array(
				'label'   => esc_html__( 'Enable raw HTML', 'et_builder' ),
				'type'    => 'yes_no_button',
				'options' => array(
					'on'  => et_builder_i18n( 'Yes' ),
					'off' => et_builder_i18n( 'No' ),
				),
				'default' => 'off',
				'show_on' => 'text',
			);
		}

		$custom_fields[ "custom_meta_{$key}" ] = $field;
	}

	/**
	 * Filter available custom field options for dynamic content.
	 *
	 * @since 3.17.2
	 *
	 * @param array[] $custom_fields
	 * @param int     $post_id
	 * @param mixed[] $raw_custom_fields
	 *
	 * @return array[]
	 */
	$custom_fields = apply_filters( 'et_builder_custom_dynamic_content_fields', $custom_fields, $post_id, $raw_custom_fields );

	return $custom_fields;
}


/**
 * Sanitize dynamic content on save.
 *
 * Check on save post if the user has the unfiltered_html capability,
 * if they do, we can bail, because they can save whatever they want,
 * if they don't, we need to strip the enable_html flag from the dynamic content item,
 * and then re-encode it, and put the new value back in the post content.
 *
 * @since 4.23.2
 *
 * @param array $data    An array of slashed post data.
 *
 * @return array $data Modified post data.
 */
function et_builder_sanitize_dynamic_content_fields( $data ) {
	// get the post content, and unslash it.
	$post_content_being_saved = wp_unslash( $data['post_content'] );

	// get the dynamic content items.
	$dynamic_content_items = et_builder_get_dynamic_contents( $post_content_being_saved );

	// if there are no dynamic content items, we can bail.
	if ( empty( $dynamic_content_items ) ) {
		return $data;
	}

	// if the current user can save unfiltered html, we can bail,
	// because they can save whatever they want.
	if ( current_user_can( 'unfiltered_html' ) ) {
		return $data;
	}

	// loop through the dynamic content items.
	foreach ( $dynamic_content_items as $dynamic_content_item ) {
		// parse the dynamic content item.
		$dynamic_content_item_parsed = et_builder_parse_dynamic_content( $dynamic_content_item );

		// check if enable_html is set.
		if ( $dynamic_content_item_parsed->get_settings( 'enable_html' ) !== '' ) {

			// Set the enable_html flag to off.
			$dynamic_content_item_parsed->set_settings( 'enable_html', 'off' );

			// reserialize the dynamic content item.
			$re_serialized_dynamic_content_item = $dynamic_content_item_parsed->serialize();

			// replace the content in the post content.
			$post_content_being_saved = str_replace( $dynamic_content_item, $re_serialized_dynamic_content_item, $post_content_being_saved );
		}
	}

	// update the post content, and re-slash it.
	$data['post_content'] = wp_slash( $post_content_being_saved );

	return $data;
}

add_filter( 'wp_insert_post_data', 'et_builder_sanitize_dynamic_content_fields', 10, 2 );

/**
 * Get all dynamic content fields.
 *
 * @since 3.17.2
 *
 * @param integer $post_id Post Id.
 * @param string  $context Context e.g `edit`, `display`.
 *
 * @return array[]
 */
function et_builder_get_dynamic_content_fields( $post_id, $context ) {
	global $__et_dynamic_content_fields_index_map;

	$fields        = et_builder_get_built_in_dynamic_content_fields( $post_id );
	$custom_fields = array();

	if ( 'display' === $context || et_pb_is_allowed( 'read_dynamic_content_custom_fields' ) ) {
		$custom_fields = et_builder_get_custom_dynamic_content_fields( $post_id );
	}

	$all = array_merge( $fields, $custom_fields );

	foreach ( $all as $id => $field ) {
		$all[ $id ]['id'] = $id;
	}

	$__et_dynamic_content_fields_index_map = array_flip( array_keys( $all ) );
	uasort( $all, 'et_builder_sort_dynamic_content_fields' );
	$__et_dynamic_content_fields_index_map = array();

	return $all;
}

/**
 * Sort dynamic content fields.
 *
 * @since 4.0
 *
 * @param array $a First field.
 * @param array $b Second field.
 *
 * @return integer
 */
function et_builder_sort_dynamic_content_fields( $a, $b ) {
	global $__et_dynamic_content_fields_index_map;

	$top = array_flip(
		array(
			'Default',
			__( 'Custom Fields', 'et_builder' ),
		)
	);

	$a_group  = et_()->array_get( $a, 'group', 'Default' );
	$a_is_top = isset( $top[ $a_group ] );
	$b_group  = et_()->array_get( $b, 'group', 'Default' );
	$b_is_top = isset( $top[ $b_group ] );

	if ( $a_is_top && ! $b_is_top ) {
		return -1;
	}

	if ( ! $a_is_top && $b_is_top ) {
		return 1;
	}

	if ( $a_is_top && $b_is_top && $a_group !== $b_group ) {
		return $top[ $a_group ] - $top[ $b_group ];
	}

	$a_index = $__et_dynamic_content_fields_index_map[ $a['id'] ];
	$b_index = $__et_dynamic_content_fields_index_map[ $b['id'] ];

	return $a_index - $b_index;
}

/**
 * Get default value for a dynamic content field's setting.
 *
 * @since 3.17.2
 *
 * @param integer $post_id Post Id.
 * @param string  $field Custom field name.
 * @param string  $setting Array of dynamic content settings.
 *
 * @return string
 */
function et_builder_get_dynamic_attribute_field_default( $post_id, $field, $setting ) {
	$_      = ET_Core_Data_Utils::instance();
	$fields = et_builder_get_dynamic_content_fields( $post_id, 'edit' );

	return $_->array_get( $fields, "$field.fields.$setting.default", '' );
}

/**
 * Resolve dynamic content to a simple value.
 *
 * @param string  $name Custom field name.
 * @param array   $settings Array of dynamic content settings.
 * @param integer $post_id Post Id.
 * @param string  $context Context e.g `edit`, `display`.
 * @param array   $overrides An associative array of field_name => value to override field value.
 * @param bool    $is_content Whether dynamic content used in module's main_content field {@see et_builder_ajax_resolve_post_content()}.
 *
 * @return string
 * @since 3.17.2
 */
function et_builder_resolve_dynamic_content( $name, $settings, $post_id, $context, $overrides = array(), $is_content = false ) {
	/**
	 * Generic filter for content resolution based on a given field and post.
	 *
	 * @since 3.17.2
	 *
	 * @param string $content
	 * @param string $name
	 * @param array $settings
	 * @param integer $post_id
	 * @param string $context
	 * @param array $overrides
	 *
	 * @return string
	 */
	$content = apply_filters( 'et_builder_resolve_dynamic_content', '', $name, $settings, $post_id, $context, $overrides );

	/**
	 * Field-specific filter for content resolution based on a given field and post.
	 *
	 * @since 3.17.2
	 *
	 * @param string $content
	 * @param array $settings
	 * @param integer $post_id
	 * @param string $context
	 * @param array $overrides
	 *
	 * @return string
	 */
	$content = apply_filters( "et_builder_resolve_dynamic_content_{$name}", $content, $settings, $post_id, $context, $overrides );

	$content = et_maybe_enable_embed_shortcode( $content, $is_content );

	return $is_content ? do_shortcode( $content ) : $content;
}

/**
 * Wrap a dynamic content value with its before/after settings values.
 *
 * @since 3.17.2
 *
 * @param integer $post_id Post Id.
 * @param string  $name Custom field name.
 * @param string  $value Value content.
 * @param array   $settings Array of dynamic content settings.
 *
 * @return string
 */
function et_builder_wrap_dynamic_content( $post_id, $name, $value, $settings ) {
	$_           = ET_Core_Data_Utils::instance();
	$def         = 'et_builder_get_dynamic_attribute_field_default';
	$before      = $_->array_get( $settings, 'before', $def( $post_id, $name, 'before' ) );
	$after       = $_->array_get( $settings, 'after', $def( $post_id, $name, 'after' ) );
	$tb_post_id  = ET_Builder_Element::get_theme_builder_layout_id();
	$cap_post_id = $tb_post_id ? $tb_post_id : $post_id;
	$user_id     = get_post_field( 'post_author', $cap_post_id );

	if ( ! user_can( $user_id, 'unfiltered_html' ) ) {
		$allowlist = array_merge(
			wp_kses_allowed_html( '' ),
			array(
				'h1'   => array(),
				'h2'   => array(),
				'h3'   => array(),
				'h4'   => array(),
				'h5'   => array(),
				'h6'   => array(),
				'ol'   => array(),
				'ul'   => array(),
				'li'   => array(),
				'span' => array(),
				'p'    => array(),
			)
		);

		$before = wp_kses( $before, $allowlist );
		$after  = wp_kses( $after, $allowlist );
	}

	return $before . $value . $after;
}

/**
 * Resolve built-in dynamic content fields.
 *
 * @param string  $content Value content.
 * @param string  $name Custom field name.
 * @param array   $settings Array of dynamic content settings.
 * @param integer $post_id Post Id.
 * @param string  $context Context e.g `edit`, `display`.
 * @param array   $overrides An associative array of field_name => value to override field value.
 *
 * @return string
 * @since 3.17.2
 */
function et_builder_filter_resolve_default_dynamic_content( $content, $name, $settings, $post_id, $context, $overrides ) {
	global $shortname, $wp_query;

	$_       = ET_Core_Data_Utils::instance();
	$def     = 'et_builder_get_dynamic_attribute_field_default';
	$post    = ( is_int( $post_id ) && 0 !== $post_id ) ? get_post( $post_id ) : false;
	$author  = null;
	$wrapped = false;
	$is_woo  = false;

	if ( $post ) {
		$author = get_userdata( $post->post_author );
	} elseif ( is_author() ) {
		$author = get_queried_object();
	}

	switch ( $name ) {
		case 'product_title': // Intentional fallthrough.
		case 'post_title':
			if ( isset( $overrides[ $name ] ) ) {
				$content = $overrides[ $name ];
			} else {
				$content = et_builder_get_current_title( $post_id );
			}

			$content = et_core_intentionally_unescaped( $content, 'cap_based_sanitized' );
			break;

		case 'post_excerpt':
			if ( ! $post ) {
				break;
			}

			$words     = (int) $_->array_get( $settings, 'words', $def( $post_id, $name, 'words' ) );
			$read_more = $_->array_get( $settings, 'read_more_label', $def( $post_id, $name, 'read_more_label' ) );
			$content   = isset( $overrides[ $name ] ) ? $overrides[ $name ] : get_the_excerpt( $post_id );

			if ( $words > 0 ) {
				$content = wp_trim_words( $content, $words );
			}

			if ( ! empty( $read_more ) ) {
				$content .= sprintf(
					' <a href="%1$s">%2$s</a>',
					esc_url( get_permalink( $post_id ) ),
					esc_html( $read_more )
				);
			}
			break;

		case 'post_date':
			if ( ! $post ) {
				break;
			}

			$format        = $_->array_get( $settings, 'date_format', $def( $post_id, $name, 'date_format' ) );
			$custom_format = $_->array_get( $settings, 'custom_date_format', $def( $post_id, $name, 'custom_date_format' ) );

			if ( 'default' === $format ) {
				$format = '';
			}

			if ( 'custom' === $format ) {
				$format = $custom_format;
			}

			$content = esc_html( get_the_date( $format, $post_id ) );
			break;

		case 'post_comment_count':
			if ( ! $post ) {
				break;
			}

			$link    = $_->array_get( $settings, 'link_to_comments_page', $def( $post_id, $name, 'link_to_comments_page' ) );
			$link    = 'on' === $link;
			$content = esc_html( get_comments_number( $post_id ) );

			if ( $link ) {
				$content = sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url( get_comments_link( $post_id ) ),
					et_core_esc_previously( et_builder_wrap_dynamic_content( $post_id, $name, $content, $settings ) )
				);
				$wrapped = true;
			}
			break;

		case 'post_categories': // Intentional fallthrough.
		case 'post_tags':
			if ( ! $post ) {
				break;
			}

			$overrides_map   = array(
				'category' => 'post_categories',
				'post_tag' => 'post_tags',
			);
			$post_taxonomies = et_builder_get_taxonomy_types( get_post_type( $post_id ) );
			$taxonomy        = $_->array_get( $settings, 'category_type', '' );

			if ( in_array( $taxonomy, array( 'et_header_layout_category', 'et_body_layout_category', 'et_footer_layout_category' ), true ) ) {
				// TB layouts were storing an invalid taxonomy in <= 4.0.3 so we have to correct it:.
				$taxonomy = $def( $post_id, $name, 'category_type' );
			}

			if ( ! isset( $post_taxonomies[ $taxonomy ] ) ) {
				break;
			}

			$link      = $_->array_get( $settings, 'link_to_term_page', $def( $post_id, $name, 'link_to_category_page' ) );
			$link      = 'on' === $link;
			$separator = $_->array_get( $settings, 'separator', $def( $post_id, $name, 'separator' ) );
			$separator = ! empty( $separator ) ? $separator : $def( $post_id, $name, 'separator' );
			$ids_key   = isset( $overrides_map[ $taxonomy ] ) ? $overrides_map[ $taxonomy ] : '';
			$ids       = isset( $overrides[ $ids_key ] ) ? array_filter( array_map( 'intval', explode( ',', $overrides[ $ids_key ] ) ) ) : array();
			$terms     = ! empty( $ids ) ? get_terms(
				array(
					'taxonomy' => $taxonomy,
					'include'  => $ids,
				)
			) : get_the_terms( $post_id, $taxonomy );
			if ( is_array( $terms ) ) {
				$content = et_builder_list_terms( $terms, $link, $separator );
			} else {
				$content = '';
			}
			break;

		case 'post_link':
			if ( ! $post ) {
				break;
			}

			$text        = $_->array_get( $settings, 'text', $def( $post_id, $name, 'text' ) );
			$custom_text = $_->array_get( $settings, 'custom_text', $def( $post_id, $name, 'custom_text' ) );
			$label       = 'custom' === $text ? $custom_text : get_the_title( $post_id );
			$content     = sprintf(
				'<a href="%1$s">%2$s</a>',
				esc_url( get_permalink( $post_id ) ),
				esc_html( $label )
			);
			break;

		case 'post_author':
			$name_format      = $_->array_get( $settings, 'name_format', $def( $post_id, $name, 'name_format' ) );
			$link             = $_->array_get( $settings, 'link', $def( $post_id, $name, 'link' ) );
			$link             = 'on' === $link;
			$link_destination = $_->array_get( $settings, 'link_destination', $def( $post_id, $name, 'link_destination' ) );
			$link_target      = 'author_archive' === $link_destination ? '_self' : '_blank';
			$label            = '';
			$url              = '';

			if ( ! $author ) {
				$content = '';
				break;
			}

			switch ( $name_format ) {
				case 'display_name':
					$label = $author->display_name;
					break;
				case 'first_last_name':
					$label = $author->first_name . ' ' . $author->last_name;
					break;
				case 'last_first_name':
					$label = $author->last_name . ', ' . $author->first_name;
					break;
				case 'first_name':
					$label = $author->first_name;
					break;
				case 'last_name':
					$label = $author->last_name;
					break;
				case 'nickname':
					$label = $author->nickname;
					break;
				case 'username':
					$label = $author->user_login;
					break;
			}

			switch ( $link_destination ) {
				case 'author_archive':
					$url = get_author_posts_url( $author->ID );
					break;
				case 'author_website':
					$url = $author->user_url;
					break;
			}

			$content = esc_html( $label );

			if ( $link && ! empty( $url ) ) {
				$content = sprintf(
					'<a href="%1$s" target="%2$s">%3$s</a>',
					esc_url( $url ),
					esc_attr( $link_target ),
					et_core_esc_previously( $content )
				);
			}
			break;

		case 'post_author_bio':
			if ( ! $author ) {
				break;
			}

			$content = et_core_intentionally_unescaped( $author->description, 'cap_based_sanitized' );
			break;

		case 'term_description':
			$content = et_core_intentionally_unescaped( term_description(), 'cap_based_sanitized' );
			break;

		case 'site_title':
			$content = esc_html( get_bloginfo( 'name' ) );
			break;

		case 'site_tagline':
			$content = esc_html( get_bloginfo( 'description' ) );
			break;

		case 'current_date':
			$format        = $_->array_get( $settings, 'date_format', $def( $post_id, $name, 'date_format' ) );
			$custom_format = $_->array_get( $settings, 'custom_date_format', $def( $post_id, $name, 'custom_date_format' ) );

			if ( 'default' === $format ) {
				$format = strval( get_option( 'date_format' ) );
			}

			if ( 'custom' === $format ) {
				$format = $custom_format;
			}

			$content = esc_html( date_i18n( $format ) );
			break;

		case 'post_link_url':
			if ( ! $post ) {
				break;
			}

			$content = esc_url( get_permalink( $post_id ) );
			break;

		case 'post_author_url':
			if ( ! $author ) {
				break;
			}

			$content = esc_url( get_author_posts_url( $author->ID ) );
			break;

		case 'home_url':
			$content = esc_url( home_url( '/' ) );
			break;

		case 'any_post_link_url':
			$selected_post_id = $_->array_get( $settings, 'post_id', $def( $post_id, $name, 'post_id' ) );
			$content          = esc_url( get_permalink( $selected_post_id ) );
			break;

		case 'product_reviews_tab':
			$content = '#product_reviews_tab';
			break;

		case 'post_featured_image':
			$is_blog_query = isset( $wp_query->et_pb_blog_query ) && $wp_query->et_pb_blog_query;

			if ( isset( $overrides[ $name ] ) ) {
				$id      = (int) $overrides[ $name ];
				$content = wp_get_attachment_image_url( $id, 'full' );
				break;
			}

			if ( ! $is_blog_query && ( is_category() || is_tag() || is_tax() ) ) {
				$term_id       = (int) get_queried_object_id();
				$attachment_id = (int) get_term_meta( $term_id, 'thumbnail_id', true );
				$url           = wp_get_attachment_image_url( $attachment_id, 'full' );
				$content       = $url ? esc_url( $url ) : '';
				break;
			}

			if ( $post ) {
				$url     = get_the_post_thumbnail_url( $post_id, 'full' );
				$content = $url ? esc_url( $url ) : '';
				break;
			}

			break;

		case 'post_featured_image_alt_text':
			$is_blog_query = isset( $wp_query->et_pb_blog_query ) && $wp_query->et_pb_blog_query;

			if ( isset( $overrides[ $name ] ) ) {
				$id      = (int) $overrides[ $name ];
				$img_alt = $id ? get_post_meta( $id, '_wp_attachment_image_alt', true ) : '';
				$content = $img_alt ? esc_attr( $img_alt ) : '';
				break;
			}

			if ( ! $is_blog_query && ( is_category() || is_tag() || is_tax() ) ) {
				$term_id       = (int) get_queried_object_id();
				$attachment_id = (int) get_term_meta( $term_id, 'thumbnail_id', true );
				$img_alt       = $attachment_id ? get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) : '';
				$content       = $img_alt ? esc_attr( $img_alt ) : '';
				break;
			}

			if ( $post ) {
				$img_alt = get_post_thumbnail_id() ? get_post_meta( get_post_thumbnail_id(), '_wp_attachment_image_alt', true ) : '';
				$content = $img_alt ? esc_attr( $img_alt ) : '';
				break;
			}

			break;

		case 'post_featured_image_title_text':
			$is_blog_query = isset( $wp_query->et_pb_blog_query ) && $wp_query->et_pb_blog_query;

			if ( isset( $overrides[ $name ] ) ) {
				$id        = (int) $overrides[ $name ];
				$img_title = $id ? get_the_title( $id ) : '';
				$content   = $img_title ? esc_attr( $img_title ) : '';
				break;
			}

			if ( ! $is_blog_query && ( is_category() || is_tag() || is_tax() ) ) {
				$term_id       = (int) get_queried_object_id();
				$attachment_id = (int) get_term_meta( $term_id, 'thumbnail_id', true );
				$img_title     = $attachment_id ? get_the_title( $attachment_id ) : '';
				$content       = $img_title ? esc_attr( $img_title ) : '';
				break;
			}

			if ( $post ) {
				$img_title = get_post_thumbnail_id() ? get_the_title( get_post_thumbnail_id() ) : '';
				$content   = $img_title ? esc_attr( $img_title ) : '';
				break;
			}

			break;

		case 'post_author_profile_picture':
			if ( ! $author ) {
				break;
			}

			$content = get_avatar_url( $author->ID );
			break;

		case 'site_logo':
			$logo    = et_get_option( $shortname . '_logo' );
			$content = '';

			if ( ! empty( $logo ) ) {
				$content = esc_url( $logo );
			}

			break;

		case 'product_breadcrumb':
			if ( ! $post ) {
				break;
			}

			$dynamic_product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $post_id );

			if ( $dynamic_product ) {
				$is_woo  = true;
				$content = ET_Builder_Module_Woocommerce_Breadcrumb::get_breadcrumb(
					array(
						'product' => $dynamic_product->get_id(),
					)
				);
			} else {
				$content = '';
			}
			break;

		case 'product_price':
			if ( ! $post ) {
				break;
			}

			$dynamic_product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $post_id );

			if ( $dynamic_product ) {
				$is_woo  = true;
				$content = ET_Builder_Module_Woocommerce_Price::get_price(
					array(
						'product' => $dynamic_product->get_id(),
					)
				);
			} else {
				$content = '';
			}
			break;

		case 'product_description':
			if ( ! $post ) {
				break;
			}

			$dynamic_product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $post_id );

			if ( $dynamic_product ) {
				$is_woo  = true;
				$content = ET_Builder_Module_Woocommerce_Description::get_description(
					array(
						'product'          => $dynamic_product->get_id(),
						'description_type' => 'description',
					)
				);
			} else {
				$content = '';
			}
			break;

		case 'product_short_description':
			if ( ! $post ) {
				break;
			}

			$dynamic_product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $post_id );

			if ( $dynamic_product ) {
				$is_woo  = true;
				$content = ET_Builder_Module_Woocommerce_Description::get_description(
					array(
						'product'          => $dynamic_product->get_id(),
						'description_type' => 'short_description',
					)
				);
			} else {
				$content = '';
			}
			break;

		case 'product_reviews_count':
			if ( ! $post ) {
				break;
			}

			$dynamic_product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $post_id );

			if ( $dynamic_product ) {
				$is_woo  = true;
				$content = $dynamic_product->get_review_count();
			} else {
				$content = 0;
			}
			break;

		case 'product_sku':
			if ( ! $post ) {
				break;
			}

			$dynamic_product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $post_id );

			if ( $dynamic_product ) {
				$is_woo  = true;
				$content = $dynamic_product->get_sku();
			} else {
				$content = '';
			}
			break;

		case 'product_reviews':
			if ( ! $post ) {
				break;
			}

			$dynamic_product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $post_id );

			if ( ! $dynamic_product ) {
				$content = '';
				break;
			}

			// Return early if comments are closed.
			if ( ! comments_open( $dynamic_product->get_id() ) ) {
				$content = '';
				break;
			}

			$is_woo = true;

			// Product description refers to Product short description.
			// Product short description is nothing but post excerpt.
			$args        = array( 'post_id' => $dynamic_product->get_id() );
			$comments    = get_comments( $args );
			$total_pages = get_comment_pages_count( $comments );
			$content     = wp_list_comments(
				array(
					'callback' => 'woocommerce_comments',
					'echo'     => false,
				),
				$comments
			);

			// Pass $dynamic_product, $reviews to unify the flow of data.
			$reviews_title        = ET_Builder_Module_Helper_Woocommerce_Modules::get_reviews_title( $dynamic_product );
			$reviews_comment_form = ET_Builder_Module_Helper_Woocommerce_Modules::get_reviews_comment_form( $dynamic_product, $comments );
			$no_reviews_text      = sprintf(
				'<p class="woocommerce-noreviews">%s</p>',
				esc_html__( 'There are no reviews yet.', 'et_builder' )
			);

			$no_reviews    = is_array( $comments ) && count( $comments ) > 0 ? '' : $no_reviews_text;
			$is_show_title = 'on' === $_->array_get( $settings, 'enable_title', 'on' );

			if ( wp_doing_ajax() ) {
				$page = get_query_var( 'cpage' );
				if ( ! $page ) {
					$page = 1;
				}
				$args = array(
					'base'         => add_query_arg( 'cpage', '%#%' ),
					'format'       => '',
					'total'        => $total_pages,
					'current'      => $page,
					'echo'         => false,
					'add_fragment' => '#comments',
					'type'         => 'list',
				);
				global $wp_rewrite;
				if ( $wp_rewrite->using_permalinks() ) {
					$args['base'] = user_trailingslashit( trailingslashit( get_permalink() ) . $wp_rewrite->comments_pagination_base . '-%#%', 'commentpaged' );
				}

				$pagination = paginate_links( $args );
			} else {
				$pagination = paginate_comments_links(
					array(
						'echo'  => false,
						'type'  => 'list',
						'total' => $total_pages,
					)
				);
			}

			$title = $is_show_title
				? sprintf( '<h2 class="woocommerce-Reviews-title">%s</h2>', et_core_esc_previously( $reviews_title ) )
				: '';

			$content = sprintf(
				'
						<div id="reviews" class="woocommerce-Reviews">
							%1$s
							<div id="comments">
								<ol class="commentlist">
								%2$s
								</ol>
								<nav class="woocommerce-pagination">
									%5$s
								</nav>
								%4$s
							</div>
							<div id="review_form_wrapper">
								%3$s
							</div>
						</div>
						',
				/* 1$s */ et_core_esc_previously( $title ),
				/* 2$s */ et_core_esc_previously( $content ),
				/* 3$s */ et_core_esc_previously( $reviews_comment_form ),
				/* 4$s */ et_core_esc_previously( $no_reviews ),
				/* 5$s */ et_core_esc_previously( $pagination )
			);
			$wrapped = true;
			break;

		case 'product_additional_information':
			if ( ! $post ) {
				break;
			}

			$dynamic_product = ET_Builder_Module_Helper_Woocommerce_Modules::get_product( $post_id );
			$show_title      = $_->array_get( $settings, 'enable_title', 'on' );

			if ( $dynamic_product ) {
				$is_woo  = true;
				$content = ET_Builder_Module_Woocommerce_Additional_Info::get_additional_info(
					array(
						'product'    => $dynamic_product->get_id(),
						'show_title' => $show_title,
					)
				);
			} else {
				$content = '';
			}
			break;

		case 'post_meta_key':
			$meta_key = $_->array_get( $settings, 'meta_key' );
			$content  = get_post_meta( $post_id, $meta_key, true );
			$is_fe    = 'fe' === et_builder_get_current_builder_type() && ! is_et_theme_builder_template_preview() ? true : false;

			if ( ( $is_fe && empty( $content ) ) || empty( $meta_key ) ) {
				$content = '';
				break;
			}

			if ( empty( $content ) ) {
				$content = et_builder_get_dynamic_content_custom_field_label( $meta_key );
			} else {
				$enable_html = $_->array_get( $settings, 'enable_html' );

				if ( 'on' !== $enable_html ) {
					$content = esc_html( $content );
				}
			}
			break;
	}

	// Handle in post type URL options.
	$post_types = et_builder_get_public_post_types();
	foreach ( $post_types as $public_post_type ) {
		$key = 'post_link_url_' . $public_post_type->name;

		if ( $key !== $name ) {
			continue;
		}

		$selected_post_id = $_->array_get( $settings, 'post_id', $def( $post_id, $name, 'post_id' ) );
		$content          = esc_url( get_permalink( $selected_post_id ) );
		break;
	}

	// Wrap non plain text woo data to add custom selector for styling inheritance.
	// It works by checking is the content has HTML tag.
	if ( $is_woo && $content && preg_match( '/<\s?[^\>]*\/?\s?>/i', $content ) ) {
		$content = sprintf( '<div class="woocommerce et-dynamic-content-woo et-dynamic-content-woo--%2$s">%1$s</div>', $content, $name );
	}

	if ( ! $wrapped ) {
		$content = et_builder_wrap_dynamic_content( $post_id, $name, $content, $settings );
		$wrapped = true;
	}

	return $content;
}
add_filter( 'et_builder_resolve_dynamic_content', 'et_builder_filter_resolve_default_dynamic_content', 10, 6 );

/**
 * Add iFrame to allowed wp_kses_post tags.
 *
 * @param array  $tags Allowed tags, attributes, and entities.
 * @param string $context Context to judge allowed tags by. Allowed values are 'post'.
 *
 * @return array
 */
function et_builder_wp_kses_post_tags( $tags, $context ) {
	if ( 'post' === $context && current_user_can( 'unfiltered_html' ) ) {
		$tags['iframe'] = array(
			'title'           => true,
			'width'           => true,
			'height'          => true,
			'src'             => true,
			'allow'           => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
		);
	}

	return $tags;
}

add_filter( 'wp_kses_allowed_html', 'et_builder_wp_kses_post_tags', 10, 2 );

/**
 * Resolve custom field dynamic content fields.
 *
 * @param string  $content Value content.
 * @param string  $name Custom field name.
 * @param array   $settings Array of dynamic content settings.
 * @param integer $post_id Post Id.
 * @param string  $context Context e.g `edit`, `display`.
 * @param array   $overrides  An associative array of field_name => value to override field value.
 *
 * @return string
 * @since 3.17.2
 */
function et_builder_filter_resolve_custom_field_dynamic_content( $content, $name, $settings, $post_id, $context, $overrides ) {
	$post   = get_post( $post_id );
	$fields = et_builder_get_dynamic_content_fields( $post_id, $context );

	if ( empty( $fields[ $name ]['meta_key'] ) ) {
		return $content;
	}

	if ( 'edit' === $context && ! et_pb_is_allowed( 'read_dynamic_content_custom_fields' ) ) {
		if ( 'text' === $fields[ $name ]['type'] ) {
			return esc_html__( 'You don\'t have sufficient permissions to access this content.', 'et_builder' );
		}
		return '';
	}

	$_           = ET_Core_Data_Utils::instance();
	$def         = 'et_builder_get_dynamic_attribute_field_default';
	$enable_html = $_->array_get( $settings, 'enable_html', $def( $post_id, $name, 'enable_html' ) );

	if ( $post ) {
		$content = get_post_meta( $post_id, $fields[ $name ]['meta_key'], true );
	}

	/**
	 * Provide a hook for third party compatibility purposes of formatting meta values.
	 *
	 * @since 3.17.2
	 *
	 * @param string $meta_value
	 * @param string $meta_key
	 * @param integer $post_id
	 */
	$content = apply_filters( 'et_builder_dynamic_content_meta_value', $content, $fields[ $name ]['meta_key'], $post_id );

	// Sanitize HTML contents.
	$content = wp_kses_post( $content );

	if ( 'on' !== $enable_html ) {
		$content = esc_html( $content );
	}

	$content = et_builder_wrap_dynamic_content( $post_id, $name, $content, $settings );

	return $content;
}
add_filter( 'et_builder_resolve_dynamic_content', 'et_builder_filter_resolve_custom_field_dynamic_content', 10, 6 );


/**
 * Resolve a dynamic group post content field for use during editing.
 *
 * @since 3.17.2
 *
 * @param string  $field Custom field name.
 * @param array   $settings Array of dynamic content settings.
 * @param integer $post_id Post Id.
 * @param array   $overrides An associative array of field_name => value to override field value.
 * @param boolean $is_content Whether dynamic content used in module's main_content field {@see et_builder_ajax_resolve_post_content()}.
 *
 * @return string
 */
function et_builder_filter_resolve_dynamic_post_content_field( $field, $settings, $post_id, $overrides = array(), $is_content = false ) {
	return et_builder_resolve_dynamic_content( $field, $settings, $post_id, 'edit', $overrides, $is_content );
}
add_action( 'et_builder_resolve_dynamic_post_content_field', 'et_builder_filter_resolve_dynamic_post_content_field', 10, 5 );

/**
 * Clean potential dynamic content from filter artifacts.
 *
 * @since 3.20.2
 *
 * @param string $value Content.
 *
 * @return string
 */
function et_builder_clean_dynamic_content( $value ) {
	// Strip wrapping <p></p> tag as it appears in shortcode content in certain cases (e.g. BB preview).
	$value = preg_replace( '/^<p>(.*)<\/p>$/i', '$1', trim( $value ) );
	return $value;
}

/**
 * Parse a JSON-encoded string into an ET_Builder_Value instance or null on failure.
 *
 * @since 3.20.2
 *
 * @param string $json JSON-encoded string.
 *
 * @return ET_Builder_Value|null
 */
function et_builder_parse_dynamic_content_json( $json ) {
	// phpcs:disable WordPress.Security.NonceVerification -- This function does not change any stats, hence CSRF ok.
	$post_types         = array_keys( et_builder_get_public_post_types() );
	$dynamic_content    = json_decode( $json, true );
	$is_dynamic_content = is_array( $dynamic_content ) && isset( $dynamic_content['dynamic'] ) && (bool) $dynamic_content['dynamic'];
	$has_content        = is_array( $dynamic_content ) && isset( $dynamic_content['content'] ) && is_string( $dynamic_content['content'] );
	$has_settings       = is_array( $dynamic_content ) && isset( $dynamic_content['settings'] ) && is_array( $dynamic_content['settings'] );
	$has_category_type  = is_array( $dynamic_content ) && isset( $dynamic_content['settings'] ) && isset( $dynamic_content['settings']['category_type'] );

	// When adding a section from library get_post_type() will not work, and post type has to be fetched from $_POST.
	$is_added_from_library = isset( $_POST['et_post_type'] );

	if ( ! $is_dynamic_content || ! $has_content || ! $has_settings ) {
		return null;
	}

	// Replaces layout_category with proper category_type depending on the post type on which the layout is added.
	if ( $has_category_type && 'post_categories' === $dynamic_content['content'] && ! 0 === substr_compare( $dynamic_content['settings']['category_type'], '_tag', - 4 ) ) {
		if ( $is_added_from_library ) {
			$correct_post_type = sanitize_text_field( $_POST['et_post_type'] );
			$correct_post_type = in_array( $correct_post_type, $post_types, true ) ? $correct_post_type : 'post';
		} else {
			$correct_post_type = get_post_type();
			$correct_post_type = in_array( $correct_post_type, $post_types, true ) ? $correct_post_type : 'post';
		}

		if ( 'post' === $correct_post_type ) {
			$dynamic_content['settings']['category_type'] = 'category';
		} else {
			$dynamic_content['settings']['category_type'] = $correct_post_type . '_category';
		}
	}

	return new ET_Builder_Value(
		(bool) $dynamic_content['dynamic'],
		sanitize_text_field( $dynamic_content['content'] ),
		array_map( 'wp_kses_post', $dynamic_content['settings'] )
	);
	// phpcs:enable
}

/**
 * Convert a value to an ET_Builder_Value representation.
 *
 * @since 3.17.2
 *
 * @param string $content Value content.
 *
 * @return ET_Builder_Value
 */
function et_builder_parse_dynamic_content( $content ) {
	$json            = et_builder_clean_dynamic_content( $content );
	$json            = preg_replace( '/^@ET-DC@(.*?)@$/', '$1', $json );
	$dynamic_content = et_builder_parse_dynamic_content_json( $json );

	if ( null === $dynamic_content ) {
		$json            = base64_decode( $json ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions -- `base64_decode` is used to unserialize dynamic content.
		$dynamic_content = et_builder_parse_dynamic_content_json( $json );
	}

	if ( null === $dynamic_content ) {
		return new ET_Builder_Value( false, wp_kses_post( $content ), array() );
	}

	return $dynamic_content;
}

/**
 * Serialize dynamic content.
 *
 * @since 3.20.2
 *
 * @param bool    $dynamic Whether the value is static or dynamic.
 * @param string  $content Value content. Represents the dynamic content type when dynamic.
 * @param mixed[] $settings Array of dynamic content settings.
 *
 * @return string
 */
function et_builder_serialize_dynamic_content( $dynamic, $content, $settings ) {
	// JSON_UNESCAPED_SLASHES is only supported from 5.4.
	$options = defined( 'JSON_UNESCAPED_SLASHES' ) ? JSON_UNESCAPED_SLASHES : 0;
	$result  = wp_json_encode(
		array(
			'dynamic'  => $dynamic,
			'content'  => $content,
			// Force object type for keyed arrays as empty arrays will be encoded to
			// javascript arrays instead of empty objects.
			'settings' => (object) $settings,
		),
		$options
	);

	// Use fallback if needed.
	$result = 0 === $options ? str_replace( '\/', '/', $result ) : $result;

	return '@ET-DC@' . base64_encode( $result ) . '@'; // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions -- `base64_encode` is used to serialize dynamic content.
}

/**
 * Strip dynamic content.
 *
 * @since 4.0.9
 *
 * @param string $content Post Content.
 *
 * @return string
 */
function et_builder_strip_dynamic_content( $content ) {
	return preg_replace( '/@ET-DC@(.*?)@/', '', $content );
}

/**
 * Reencode legacy dynamic content in post excerpts.
 *
 * @since 3.20.2
 *
 * @param string  $post_excerpt Post Excerpt.
 * @param integer $post_id Post Id.
 *
 * @return string
 */
function et_builder_reencode_legacy_dynamic_content_in_excerpt( $post_excerpt, $post_id ) {
	$json = '/
		\{              # { character
			(?:         # non-capturing group
				[^{}]   # anything that is not a { or }
				|       # OR
				(?R)    # recurse the entire pattern
			)*          # previous group zero or more times
		\}              # } character
	/x';

	return preg_replace_callback( $json, 'et_builder_reencode_legacy_dynamic_content_in_excerpt_callback', $post_excerpt );
}
add_filter( 'et_truncate_post', 'et_builder_reencode_legacy_dynamic_content_in_excerpt', 10, 2 );

/**
 * Callback to reencode legacy dynamic content for preg_replace_callback.
 *
 * @since 3.20.2
 *
 * @param array $matches PCRE match.
 *
 * @return string
 */
function et_builder_reencode_legacy_dynamic_content_in_excerpt_callback( $matches ) {
	$value = et_builder_parse_dynamic_content_json( $matches[0] );
	return null === $value ? $matches[0] : $value->serialize();
}

/**
 * Resolve dynamic content in post excerpts instead of showing raw JSON.
 *
 * @since 3.17.2
 *
 * @param string  $post_excerpt Post excerpt.
 * @param integer $post_id Post Id.
 *
 * @return string
 */
function et_builder_resolve_dynamic_content_in_excerpt( $post_excerpt, $post_id ) {
	// Use an obscure acronym named global variable instead of an anonymous function as we are
	// targeting PHP 5.2.
	global $_et_brdcie_post_id;

	$_et_brdcie_post_id = $post_id;
	$post_excerpt       = preg_replace_callback( '/@ET-DC@.*?@/', 'et_builder_resolve_dynamic_content_in_excerpt_callback', $post_excerpt );
	$_et_brdcie_post_id = 0;

	return $post_excerpt;
}
add_filter( 'et_truncate_post', 'et_builder_resolve_dynamic_content_in_excerpt', 10, 2 );

/**
 * Callback to resolve dynamic content for preg_replace_callback.
 *
 * @since 3.17.2
 *
 * @param array $matches PCRE match.
 *
 * @return string
 */
function et_builder_resolve_dynamic_content_in_excerpt_callback( $matches ) {
	global $_et_brdcie_post_id;
	return et_builder_parse_dynamic_content( $matches[0] )->resolve( $_et_brdcie_post_id );
}
