<?php
/**
 * Resolve placeholder content for built-in dynamic content fields for Theme Builder layouts.
 *
 * @since 4.0
 *
 * @param string  $content   Content.
 * @param string  $name      Name.
 * @param array   $settings  Settings.
 * @param integer $post_id   Post ID.
 * @param string  $context   Context.
 * @param array   $overrides Overrides.
 *
 * @return string
 */
function et_theme_builder_filter_resolve_default_dynamic_content( $content, $name, $settings, $post_id, $context, $overrides ) {
	$post_type = get_post_type( $post_id );

	if ( ! et_theme_builder_is_layout_post_type( $post_type ) && ! is_et_theme_builder_template_preview() ) {
		return $content;
	}

	$placeholders = array(
		'post_title'          => __( 'Your Dynamic Post Title Will Display Here', 'et_builder' ),
		'post_excerpt'        => __( 'Your dynamic post excerpt will display here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus auctor urna eleifend diam eleifend sollicitudin a fringilla turpis. Curabitur lectus enim.', 'et_builder' ),
		'post_date'           => time(),
		'post_comment_count'  => 12,
		'post_categories'     => array(
			__( 'Category 1', 'et_builder' ),
			__( 'Category 2', 'et_builder' ),
			__( 'Category 3', 'et_builder' ),
		),
		'post_tags'           => array(
			__( 'Tag 1', 'et_builder' ),
			__( 'Tag 2', 'et_builder' ),
			__( 'Tag 3', 'et_builder' ),
		),
		'post_author'         => array(
			'display_name'    => __( 'John Doe', 'et_builder' ),
			'first_last_name' => __( 'John Doe', 'et_builder' ),
			'last_first_name' => __( 'Doe, John', 'et_builder' ),
			'first_name'      => __( 'John', 'et_builder' ),
			'last_name'       => __( 'Doe', 'et_builder' ),
			'nickname'        => __( 'John', 'et_builder' ),
			'username'        => __( 'johndoe', 'et_builder' ),
		),
		'post_author_bio'     => __( 'Your dynamic author bio will display here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus auctor urna eleifend diam eleifend sollicitudin a fringilla turpis. Curabitur lectus enim.', 'et_builder' ),
		'post_featured_image' => ET_BUILDER_PLACEHOLDER_LANDSCAPE_IMAGE_DATA,
		'term_description'    => __( 'Your dynamic category description will display here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus auctor urna eleifend diam eleifend sollicitudin a fringilla turpis. Curabitur lectus enim.', 'et_builder' ),
		'site_logo'           => 'https://www.elegantthemes.com/img/divi.png',
	);

	$_       = et_();
	$def     = 'et_builder_get_dynamic_attribute_field_default';
	$wrapped = false;

	switch ( $name ) {
		case 'post_title':
			$content = esc_html( $placeholders[ $name ] );
			break;

		case 'post_excerpt':
			$words     = (int) $_->array_get( $settings, 'words', $def( $post_id, $name, 'words' ) );
			$read_more = $_->array_get( $settings, 'read_more_label', $def( $post_id, $name, 'read_more_label' ) );
			$content   = esc_html( $placeholders[ $name ] );

			if ( $words > 0 ) {
				$content = wp_trim_words( $content, $words );
			}

			if ( ! empty( $read_more ) ) {
				$content .= sprintf(
					' <a href="%1$s">%2$s</a>',
					'#',
					esc_html( $read_more )
				);
			}
			break;

		case 'post_date':
			$format        = $_->array_get( $settings, 'date_format', $def( $post_id, $name, 'date_format' ) );
			$custom_format = $_->array_get( $settings, 'custom_date_format', $def( $post_id, $name, 'custom_date_format' ) );

			if ( 'default' === $format ) {
				$format = strval( get_option( 'date_format' ) );
			}

			if ( 'custom' === $format ) {
				$format = $custom_format;
			}

			$content = esc_html( date( $format, $placeholders[ $name ] ) );
			break;

		case 'post_comment_count':
			$link    = $_->array_get( $settings, 'link_to_comments_page', $def( $post_id, $name, 'link_to_comments_page' ) );
			$link    = 'on' === $link;
			$content = esc_html( $placeholders[ $name ] );

			if ( $link ) {
				$content = sprintf(
					'<a href="%1$s">%2$s</a>',
					'#',
					et_core_esc_previously( et_builder_wrap_dynamic_content( $post_id, $name, $content, $settings ) )
				);
				$wrapped = true;
			}
			break;

		case 'post_categories': // Intentional fallthrough.
		case 'post_tags':
			$link      = $_->array_get( $settings, 'link_to_term_page', $def( $post_id, $name, 'link_to_category_page' ) );
			$link      = 'on' === $link;
			$url       = '#';
			$separator = $_->array_get( $settings, 'separator', $def( $post_id, $name, 'separator' ) );
			$separator = ! empty( $separator ) ? $separator : $def( $post_id, $name, 'separator' );
			$content   = $placeholders[ $name ];

			foreach ( $content as $index => $item ) {
				$content[ $index ] = esc_html( $item );

				if ( $link ) {
					$content[ $index ] = sprintf(
						'<a href="%1$s" target="%2$s">%3$s</a>',
						esc_url( $url ),
						esc_attr( '_blank' ),
						et_core_esc_previously( $content[ $index ] )
					);
				}
			}

			$content = implode( esc_html( $separator ), $content );
			break;

		case 'post_link':
			$text        = $_->array_get( $settings, 'text', $def( $post_id, $name, 'text' ) );
			$custom_text = $_->array_get( $settings, 'custom_text', $def( $post_id, $name, 'custom_text' ) );
			$label       = 'custom' === $text ? $custom_text : $placeholders['post_title'];
			$content     = sprintf(
				'<a href="%1$s">%2$s</a>',
				'#',
				esc_html( $label )
			);
			break;

		case 'post_author':
			$name_format = $_->array_get( $settings, 'name_format', $def( $post_id, $name, 'name_format' ) );
			$link        = $_->array_get( $settings, 'link', $def( $post_id, $name, 'link' ) );
			$link        = 'on' === $link;
			$label       = isset( $placeholders[ $name ][ $name_format ] ) ? $placeholders[ $name ][ $name_format ] : '';
			$url         = '#';

			$content = esc_html( $label );

			if ( $link && ! empty( $url ) ) {
				$content = sprintf(
					'<a href="%1$s" target="%2$s">%3$s</a>',
					esc_url( $url ),
					esc_attr( '_blank' ),
					et_core_esc_previously( $content )
				);
			}
			break;

		case 'post_author_bio':
			$content = esc_html( $placeholders[ $name ] );
			break;

		case 'term_description':
			$content = esc_html( $placeholders[ $name ] );
			break;

		case 'post_link_url':
			$content = '#';
			break;

		case 'post_author_url':
			$content = '#';
			break;

		case 'post_featured_image':
			$content = et_core_intentionally_unescaped( $placeholders[ $name ], 'fixed_string' );
			break;

		case 'site_logo':
			if ( empty( $content ) ) {
				$content = esc_url( $placeholders[ $name ] );
			} else {
				$wrapped = true;
			}
			break;

		default:
			// Avoid unhandled cases being wrapped twice by the default resolve and this one.
			$wrapped = true;
			break;
	}

	if ( $_->starts_with( $name, 'custom_meta_' ) ) {
		$meta_key   = substr( $name, strlen( 'custom_meta_' ) );
		$meta_value = get_post_meta( $post_id, $meta_key, true );
		if ( empty( $meta_value ) ) {
			$content = et_builder_get_dynamic_content_custom_field_label( $meta_key );
		} else {
			$wrapped = true;
		}
	}

	if ( ! $wrapped ) {
		$content = et_builder_wrap_dynamic_content( $post_id, $name, $content, $settings );
		$wrapped = true;
	}

	return $content;
}
add_filter( 'et_builder_resolve_dynamic_content', 'et_theme_builder_filter_resolve_default_dynamic_content', 11, 6 );
