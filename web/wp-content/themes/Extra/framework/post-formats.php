<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


if ( !defined( 'ET_POST_FORMAT' ) ) {
	define( 'ET_POST_FORMAT', 'et_post_format' );
}
if ( !defined( 'ET_POST_FORMAT_PREFIX' ) ) {
	define( 'ET_POST_FORMAT_PREFIX', 'et-post-format-' );
}

function et_register_post_format_taxonomy(){
	global $shortname;

	register_taxonomy( ET_POST_FORMAT, 'post', array(
		'public'            => true,
		'hierarchical'      => false,
		'labels'            => array(
			'name'          => esc_html_x( 'Format', $shortname ),
			'singular_name' => esc_html_x( 'Format', $shortname ),
		),
		'query_var'         => true,
		'rewrite'           => false,
		'show_ui'           => false,
		'show_in_nav_menus' => false,
	) );

	add_post_type_support( 'post', 'et-post-formats' );
}

add_action( 'init', 'et_register_post_format_taxonomy', 1 );

function et_get_post_format( $post = null ) {
	if ( ! $post = get_post( $post ) ) {
		return false;
	}

	if ( ! post_type_supports( $post->post_type, 'et-post-formats' ) ) {
		return false;
	}

	$_format = get_the_terms( $post->ID, ET_POST_FORMAT );

	if ( empty( $_format ) ) {
		return false;
	}

	$format = array_shift( $_format );

	$post_format_string = str_replace( ET_POST_FORMAT_PREFIX, '', $format->slug );

	$post_format = in_array( $post_format_string, array_keys( et_get_post_format_strings() ) ) ? $post_format_string : false;

	return apply_filters( 'et_get_post_format', $post_format, $post->ID );
}

function et_has_post_format( $format = array(), $post = null ) {
	$prefixed = array();

	if ( $format ) {
		foreach ( (array) $format as $single ) {
			$prefixed[] = ET_POST_FORMAT_PREFIX . sanitize_key( $single );
		}
	}

	return has_term( $prefixed, ET_POST_FORMAT, $post );
}

function et_set_post_format( $post, $format ) {
	$post = get_post( $post );

	if ( empty( $post ) )
		return new WP_Error( 'invalid_post', esc_html__( 'Invalid post' ) );

	if ( ! empty( $format ) ) {
		$format = sanitize_key( $format );
		if ( 'standard' === $format || ! in_array( $format, et_get_post_format_slugs() ) )
			$format = '';
		else
			$format = ET_POST_FORMAT_PREFIX . $format;
	}

	return wp_set_post_terms( $post->ID, $format, ET_POST_FORMAT );
}

function et_get_post_format_strings() {
	$strings = array(
		'standard' => esc_html__( 'Standard', 'extra' ), // Special case. any value that evals to false will be considered standard
		'aside'    => esc_html__( 'Aside', 'extra' ),
		'chat'     => esc_html__( 'Chat', 'extra' ),
		'gallery'  => esc_html__( 'Gallery', 'extra' ),
		'link'     => esc_html__( 'Link', 'extra' ),
		'image'    => esc_html__( 'Image', 'extra' ),
		'quote'    => esc_html__( 'Quote', 'extra' ),
		'status'   => esc_html__( 'Status', 'extra' ),
		'video'    => esc_html__( 'Video', 'extra' ),
		'audio'    => esc_html__( 'Audio', 'extra' ),
		'map'      => esc_html__( 'Map', 'extra' ),
	);

	$strings = apply_filters( 'et_post_formats_strings', $strings );
	return $strings;
}

function et_get_post_format_slugs() {
	$slugs = array_keys( et_get_post_format_strings() );
	return array_combine( $slugs, $slugs );
}

function et_get_theme_post_format_slugs() {
	$theme_supported_post_formats = get_theme_support( 'et-post-formats' );

	$post_formats = array_intersect( $theme_supported_post_formats[0], array_keys( et_get_post_format_slugs() ) );

	return array_combine( $post_formats, $post_formats );
}

function et_get_post_format_string( $slug ) {
	$strings = et_get_post_format_strings();
	if ( !$slug ) {
		return $strings['standard'];
	} else {
		return ( isset( $strings[$slug] ) ) ? $strings[$slug] : '';
	}
}

function et_post_format_body_class( $body_class ) {
	if ( $post_format = et_get_post_format() ) {
		$body_class[] = 'et-post-format';
		$body_class[] = 'et-post-format-' . $post_format;
	}

	return $body_class;
}

add_filter( 'body_class', 'et_post_format_body_class' );

function _et_post_format_get_term( $term ) {
	if ( isset( $term->slug ) ) {
		$term->name = et_get_post_format_string( str_replace( ET_POST_FORMAT_PREFIX, '', $term->slug ) );
	}
	return $term;
}

add_filter( 'get_post_format', '_et_post_format_get_term' );

function _et_post_format_get_terms( $terms, $taxonomies, $args ) {
	if ( in_array( ET_POST_FORMAT, (array) $taxonomies ) ) {
		if ( isset( $args['fields'] ) && 'names' == $args['fields'] ) {
			foreach ( $terms as $order => $name ) {
				$terms[$order] = et_get_post_format_string( str_replace( ET_POST_FORMAT_PREFIX, '', $name ) );
			}
		} else {
			foreach ( (array) $terms as $order => $term ) {
				if ( isset( $term->taxonomy ) && ET_POST_FORMAT == $term->taxonomy ) {
					$terms[$order]->name = et_get_post_format_string( str_replace( ET_POST_FORMAT_PREFIX, '', $term->slug ) );
				}
			}
		}
	}
	return $terms;
}

add_filter( 'get_terms', '_et_post_format_get_terms', 10, 3 );

function _et_post_format_wp_get_object_terms( $terms ) {
	foreach ( (array) $terms as $order => $term ) {
		if ( isset( $term->taxonomy ) && ET_POST_FORMAT == $term->taxonomy ) {
			$terms[$order]->name = et_get_post_format_string( str_replace( ET_POST_FORMAT_PREFIX, '', $term->slug ) );
		}
	}
	return $terms;
}

add_filter( 'wp_get_object_terms', '_et_post_format_wp_get_object_terms' );

function et_has_format_content( $post_id = 0 ) {
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	if ( 'post' === get_post_type( $post_id ) ) {
		switch ( et_get_post_format( $post_id ) ) {
			case 'video':
				$meta_key = '_video_format_urls';
				break;
			case 'audio':
				$meta_key = '_audio_format_file_url';
				break;
			case 'quote':
				$meta_key = '_quote_format_quote';
				break;
			case 'gallery':
				$meta_key = '_gallery_format_attachment_ids';
				break;
			case 'link':
				$meta_key = '_link_format_link_url';
				break;
			case 'map':
				$meta_key = '_map_format_lat';
				break;
			default:
				$meta_key = '';
				break;
		}

		if ( !empty( $meta_key ) ) {
			$has_format_content_setting = get_post_meta( $post_id, $meta_key, true );
		} else {
			$has_format_content_setting = has_post_thumbnail();
		}

		$has_format_content = $has_format_content_setting ? true : false;
	} else {
		$has_format_content = false;
	}

	return apply_filters( 'et_has_format_content', $has_format_content, $post_id );
}

function et_has_format_content_class( $classes, $class, $post_id ) {
	$has_format_content = et_has_format_content( $post_id );

	if ( $has_format_content ) {
		$classes[] = 'et-has-post-format-content';
	} elseif ( 'post' === get_post_type( $post_id ) ) {
		$classes[] = 'et-doesnt-have-format-content';
	}

	return $classes;
}

add_filter( 'post_class', 'et_has_format_content_class', 10, 3 );

function et_set_post_format_default_class( $classes, $class, $post_id ) {
	if ( 'post' === get_post_type( $post_id ) && ! has_term( array(), ET_POST_FORMAT, $post_id ) ) {
		$classes[] = 'et_post_format-et-post-format-standard';
	}

	return $classes;
}

add_filter( 'post_class', 'et_set_post_format_default_class', 10, 3 );

/**
 * Register section, field, and settings for Extra's default post format
 * @return void
 */
function et_register_writing_admin_settings() {
	add_settings_section( 'et_writing_settings', false, false, 'writing' );

	add_settings_field( 'et_default_post_format', esc_html__( 'Default Post Format', 'extra' ), 'et_default_post_format_render', 'writing', 'et_writing_settings' );

	register_setting( 'writing', 'et_default_post_format', 'et_default_post_format_sanitize' );
}
add_action( 'admin_init', 'et_register_writing_admin_settings' );

/**
 * Render UI for Extra post format. Hide default post format options using
 * javascript since there is no hook for modifying it
 * @return void
 */
function et_default_post_format_render() {
	$post_formats = et_get_post_format_strings();
	?>
	<select name="et_default_post_format" id="et_default_post_format">
		<?php foreach ( $post_formats as $format_slug => $format_name ): ?>
		<option<?php selected( get_option( 'et_default_post_format' ), $format_slug ); ?> value="<?php echo esc_attr( $format_slug ); ?>"><?php echo esc_html( $format_name ); ?></option>
		<?php endforeach; ?>
	</select>
	<script type="text/javascript">
		jQuery('#default_post_format').parents('tr').remove();
	</script>
	<?php
}

/**
 * Sanitize et_default_post_format before being saved
 * @return bool|string
 */
function et_default_post_format_sanitize( $value ) {
	return in_array( $value, array_keys( et_get_post_format_strings() ) ) ? $value : false;
}
