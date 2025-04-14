<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * List of default category colors. This color is automatically assigned upon theme activation
 * and category creation if user doesn't assign custom color
 * @return array
 */
function et_default_category_colors() {
	$default_category_colors = array(
		'#7ac8cc',
		'#8e6ecf',
		'#db509f',
		'#e8533b',
		'#f29e1a',
		'#8bd623',
		'#5496d3',
		'#6dd69c',
		'#7464f2',
		'#e4751b',
		'#c1d51c',
		'#54d0e2',
	);

	return apply_filters( 'et_default_category_colors', $default_category_colors );
}

/**
 * Get saved category colors for easy comparison
 * @return array
 */
function et_get_saved_category_colors() {
	$et_taxonomy_meta = get_option( 'et_taxonomy_meta' );

	$saved_category_colors = array();

	if ( ! empty( $et_taxonomy_meta ) ) {
		foreach ( $et_taxonomy_meta as $et_taxonomy_meta_item ) {
			foreach ( $et_taxonomy_meta_item as $et_taxonomy_meta_record ) {
				if ( isset( $et_taxonomy_meta_record['color'] ) ) {
					$saved_category_colors[] = $et_taxonomy_meta_record['color'];
				}
			}
		}
	}

	return apply_filters( 'et_saved_category_colors', $saved_category_colors );
}

/**
 * Automatically assign category color when the theme is activated
 * @return void
 */
function et_assign_category_color_upon_activation() {
	if ( et_get_option( 'has_auto_assign_category_color' ) ) {
		return;
	}

	// Get category
	$categories = get_categories(array(
		'hide_empty' => 0,
	));

	// Available colors
	$colors = et_default_category_colors();

	// Loop categories data
	$color_index = 0;
	foreach ( $categories as $category ) {
		$category_id = $category->term_id;

		// Check for saved color
		$category_color = et_get_taxonomy_meta( $category_id, 'color', true );

		// Skip if the category already has color
		if ( ! empty( $category_color ) ) {
			continue;
		}

		$color = $colors[ $color_index ];

		// Set category color
		et_update_taxonomy_meta( $category_id, 'color', $color );

		// Setup $color_index for next loop iteration
		$color_index++;
		if ( $color_index >= count( $colors ) ) {
			$color_index = 0;
		}
	}

	et_update_option( 'has_auto_assign_category_color', true );
}

add_action( 'after_switch_theme', 'et_assign_category_color_upon_activation' );

/**
 * Get unused/least used category color for default category value instead of accent color
 * to ensure variety of colors used by category
 * @return string
 */
function et_get_default_category_color() {
	// Get categories
	$categories = get_categories();

	// Available colors
	$default_category_colors = et_default_category_colors();
	$saved_category_colors   = et_get_saved_category_colors();
	$colors                  = array_diff( $default_category_colors, $saved_category_colors );

	// Return unused default color if there's any
	if ( ! empty( $colors ) ) {
		return array_shift( $colors );
	}

	// Find the least used category color
	$colors_count = array();

	foreach ( $saved_category_colors as $saved_category_color ) {
		if ( ! in_array( $saved_category_color, $default_category_colors ) ) {
			continue;
		}

		$colors_count[] = $saved_category_color;
	}

	// Get the counts for each used color
	$colors_count = array_count_values( $colors_count );

	// Sort colors count from low to high
	asort( $colors_count );

	// Splice the first array element. Direct array_flip might cause issue on array with equal values
	$unused_colors = array_splice( $colors_count, 0, 1 );

	// Flip colors count value
	$unused_colors = array_flip( $unused_colors );

	// Get the value
	return array_shift( $unused_colors );
}

function extra_add_category_edit_form_color_picker( $term, $taxonomy, $wrapper_tag = 'tr' ) {
	$term_id = isset( $term->term_id ) ? $term->term_id : 0;
	$color = et_get_childmost_taxonomy_meta( $term_id, 'color', true, et_get_default_category_color() );

	$default_attr = ' data-default-color="' . esc_attr( $color ) . '"';
	$value_attr = ' value="' . esc_attr( $color ) . '"';
	?>
	<?php printf( '<%1$s class="form-field">', tag_escape( $wrapper_tag ) ); ?>
		<th scope="row"><label for="description"><?php esc_html_e( 'Color', 'extra' ); ?></label></th>
		<td><input class="color-picker-hex" name="extra_category_color" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'extra' ); ?>"<?php echo $default_attr; ?><?php echo $value_attr; ?> /><br>
		<span class="description"><?php esc_html_e( 'The color used for this category throughout the site.', 'extra' ); ?></span></td>
	<?php printf( '</%1$s>', tag_escape( $wrapper_tag ) ); ?>

	<?php
}

add_action( 'category_edit_form_fields', 'extra_add_category_edit_form_color_picker', 10, 2 );

function extra_add_category_add_form_color_picker( $taxonomy ) {
	extra_add_category_edit_form_color_picker( 0, $taxonomy, 'div' );
}

add_action( 'category_add_form_fields', 'extra_add_category_add_form_color_picker' );

function extra_add_category_edit_form_color_picker_script() {
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'wp-color-picker' );
	?>
	<script type="text/javascript">
	(function($){
		$(document).ready( function(){
			$('.color-picker-hex').wpColorPicker();
		});
	})(jQuery)
	</script>
	<style>
	.form-field .wp-picker-input-wrap .button.wp-picker-default {
		width: auto;
	}
	</style>
	<?php
}

add_action( 'category_add_form', 'extra_add_category_edit_form_color_picker_script' );
add_action( 'category_edit_form', 'extra_add_category_edit_form_color_picker_script' );

function extra_edit_terms_save_color( $term_id ) {
	if ( !empty( $_POST['extra_category_color'] ) ) {
		et_update_taxonomy_meta( $term_id, 'color', sanitize_text_field( $_POST['extra_category_color'] ) );
	}
}

add_action( 'edit_terms', 'extra_edit_terms_save_color', 10, 1 ); // fired when existing category saved
add_action( 'created_category', 'extra_edit_terms_save_color', 10, 1 ); // fired when new category saved
