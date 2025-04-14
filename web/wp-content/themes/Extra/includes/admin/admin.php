<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function et_add_extra_menu() {
	$core_page = add_menu_page( 'Extra', 'Extra', 'edit_theme_options', 'et_extra_options', 'et_build_epanel' );
	// Add Theme Options menu only if it's enabled for current user
	if ( et_pb_is_allowed( 'theme_options' ) ) {

		if ( isset( $_GET['page'] ) && 'et_extra_options' === $_GET['page'] && isset( $_POST['action'] ) ) {
			if (
				( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'epanel_nonce' ) )
				||
				( 'reset' === $_POST['action'] && isset( $_POST['_wpnonce_reset'] ) && wp_verify_nonce( $_POST['_wpnonce_reset'], 'et-nojs-reset_epanel' ) )
			) {
				epanel_save_data( 'js_disabled' ); //saves data when javascript is disabled
			}
		}

		add_submenu_page( 'et_extra_options', esc_html__( 'Theme Options', 'extra' ), esc_html__( 'Theme Options', 'extra' ), 'manage_options', 'et_extra_options' );
	}

	et_theme_builder_add_admin_page( 'et_extra_options' );

	// Add Theme Customizer menu only if it's enabled for current user
	if ( et_pb_is_allowed( 'theme_customizer' ) ) {
		add_submenu_page( 'et_extra_options', esc_html__( 'Theme Customizer', 'extra' ), esc_html__( 'Theme Customizer', 'extra' ), 'manage_options', 'customize.php?et_customizer_option_set=theme' );
	}
	add_submenu_page( 'et_extra_options', esc_html__( 'Role Editor', 'extra' ), esc_html__( 'Role Editor', 'extra' ), 'manage_options', 'et_extra_role_editor', 'et_pb_display_role_editor' );
	// Add extra Library menu only if it's enabled for current user
	if ( et_pb_is_allowed( 'divi_library' ) ) {
		add_submenu_page( 'et_extra_options', esc_html__( 'Divi Library', 'extra' ), esc_html__( 'Divi Library', 'extra' ), 'manage_options', 'edit.php?post_type=et_pb_layout' );
	}
	// Add layout cagegory build link submenu
	add_submenu_page( 'et_extra_options', esc_html__( 'Category Builder', 'extra' ), esc_html__( 'Category Builder', 'extra' ), 'manage_options', 'edit.php?post_type=layout' );

	// load function to check the permissions of current user
	add_action( "load-{$core_page}", 'et_pb_check_options_access' );
	add_action( "load-{$core_page}", 'et_epanel_hook_scripts' );
	add_action( "admin_print_scripts-{$core_page}", 'et_epanel_admin_js' );
	add_action( "admin_head-{$core_page}", 'et_epanel_css_admin' );
	add_action( "admin_print_scripts-{$core_page}", 'et_epanel_media_upload_scripts' );
	add_action( "admin_head-{$core_page}", 'et_epanel_media_upload_styles' );
}

add_action( 'admin_menu', 'et_add_extra_menu' );

function et_pb_check_options_access() {
	// display wp error screen if theme customizer disabled for current user
	if ( ! et_pb_is_allowed( 'theme_options' ) ) {
		wp_die( esc_html__( "you don't have sufficient permissions to access this page", 'extra' ) );
	}
}

function extra_et_epanel_style_url() {
	return get_template_directory_uri() . '/includes/admin/styles/epanel.css';
}

add_filter( 'et_epanel_style_url', 'extra_et_epanel_style_url' );

function extra_admin_scripts_styles( $hook ) {
	$template_dir = get_template_directory_uri();

	/*
	 * Loads the main admin stylesheet.
	 */
	wp_enqueue_style( 'extra-admin-style', $template_dir . '/includes/admin/styles/admin.css', array(), et_get_theme_version() );
}

add_action( 'admin_enqueue_scripts', 'extra_admin_scripts_styles' );

function extra_update_woocommerce_images() {
	if ( et_get_option( 'extra_update_woocommerce_images' ) ) {
		return;
	}

	$catalog = extra_woocommerce_get_image_size_shop_catalog();
	$single = extra_woocommerce_get_image_size_shop_single();
	$thumbnail = extra_woocommerce_get_image_size_shop_thumbnail();

	update_option( 'shop_catalog_image_size', $catalog );
	update_option( 'shop_single_image_size', $single );
	update_option( 'shop_thumbnail_image_size', $thumbnail );

	et_update_option( 'extra_update_woocommerce_images', true );
}

add_action( 'admin_init', 'extra_update_woocommerce_images' );

class Extra_Post_Format_Meta_Box extends ET_Meta_Box {

	public $post_format;

	public function __construct( $id, $title, $post_format ) {
		parent::__construct( $id, $title, array(
			'post_type' => 'post',
			'context'   => 'advanced',
			'priority'  => 'core',
		) );

		$this->post_format = $post_format;
	}

	public function before_display() {
		printf( '<input type="hidden" class="post-format-options" value="%s" />',
			esc_attr( $this->post_format )
		);
		parent::before_display();
	}

	public function display( $post ) {
		foreach ( $this->fields as $field_key => $field ) {
			$field = $this->fields[ $field_key ];
			echo '<div class="form-field">';

			printf( '<p><strong>%s</strong></p>',
				esc_html( $field['title'] )
			);

			echo '<input type="text" name="' . esc_attr( $field_key ) . '" value="' . esc_attr( $field['value'] ) . '" />';

			if ( !empty( $field['description'] ) ) {
				$description = isset( $field['esc_description_html'] ) && false === $field['esc_description_html'] ? $field['description'] : esc_html( $field['description'] );
				echo '<p class="description">' . $description . '</p>';
			}

			echo '</div><br />';
		}
	}

	public function save( $post_id, $post ) {
		if ( !empty( $_POST['et_post_format'] ) && $this->post_format == $_POST['et_post_format'] ) {
			$this->save_fields( $post_id );
		}
	}

}

class Extra_Page_Template_Meta_Box extends ET_Meta_Box {

	public $page_template;

	public function __construct( $id, $title, $page_template ) {
		parent::__construct( $id, $title, array(
			'post_type' => 'page',
			'context'   => 'normal',
			'priority'  => 'high',
		) );

		$this->page_template = $page_template;
	}

	public function before_display() {
		printf( '<input type="hidden" class="page-template-options" value="%s" />',
			esc_attr( $this->page_template )
		);
		parent::before_display();
	}

	public function display( $post ) {
		foreach ( $this->fields as $field_key => $field ) {
			echo $this->render_field( $field );
		}
	}

	public function save( $post_id, $post ) {
		if ( ( !empty( $_POST['page_template'] ) && $this->page_template == $_POST['page_template'] ) ||
		! empty( $post ) && ! empty( $post->page_template ) && $this->page_template == $post->page_template ) {
			$this->save_fields( $post_id );
		}
	}
}

class Extra_Post_Review_Meta_Box extends ET_Meta_Box {

	public function __construct() {
		parent::__construct( 'post-review-box', esc_html__( 'Review Box Contents', 'extra' ), array(
			'post_type' => 'post',
		) );
	}

	function fields() {
		$this->fields = array(
			'_post_review_box_title'            => array(
				'title'                   => esc_html__( 'Review Box Title', 'extra' ),
				'value_sanitize_function' => 'sanitize_text_field',
			),
			'_post_review_box_summary'          => array(
				'title'                   => esc_html__( 'Summary', 'extra' ),
				'value_sanitize_function' => 'wp_kses_post',
			),
			'_post_review_box_summary_title'    => array(
				'title'                   => esc_html__( 'Summary Title', 'extra' ),
				'value_sanitize_function' => 'sanitize_text_field',
			),
			'_post_review_box_breakdowns'       => array(),
			'_post_review_box_breakdowns_count' => array(
				'value_sanitize_function' => 'absint',
			),
		);
	}

	function display( $post ) {
		wp_enqueue_script( 'jquery-ui-accordion' );

		$title = $this->fields['_post_review_box_title'];

		echo '<div class="form-field">';

		printf( '<p><strong>%s</strong></p>',
			esc_html( $title['title'] )
		);

		$title['value'] = !empty( $title['value'] ) ? $title['value'] : esc_html__( 'Review', 'extra' );
		echo '<input type="text" name="_post_review_box_title" value="' . esc_attr( $title['value'] ) . '" />';

		echo '</div><br />';

		$summary = $this->fields['_post_review_box_summary'];

		echo '<div class="form-field">';

		printf( '<p><strong>%s</strong></p>',
			esc_html( $summary['title'] )
		);

		echo '<textarea name="_post_review_box_summary" >' . esc_textarea( $summary['value'] ) . '</textarea>';

		echo '</div><br />';

		$summary_title = $this->fields['_post_review_box_summary_title'];

		echo '<div class="form-field">';

		printf( '<p><strong>%s</strong></p>',
			esc_html( $summary_title['title'] )
		);

		$summary_title['value'] = !empty( $summary_title['value'] ) ? $summary_title['value'] : esc_html__( 'Summary', 'extra' );
		echo '<input type="text" name="_post_review_box_summary_title" value="' . esc_attr( $summary_title['value'] ) . '" />';

		echo '</div><br />';

		$breakdowns = !empty( $this->fields['_post_review_box_breakdowns']['value'] ) ? $this->fields['_post_review_box_breakdowns']['value'] : array(0);
		$breakdowns_count = !empty( $this->fields['_post_review_box_breakdowns_count']['value'] ) ? $this->fields['_post_review_box_breakdowns_count']['value'] : 1;
		?>
		<input type="hidden" class="breakdowns_count" id="_post_review_box_breakdowns_count" name="_post_review_box_breakdowns_count" value="<?php echo esc_attr( $breakdowns_count ); ?>" />
		<?php
		echo '<div class="breakdowns_container">';
		$index = 0;
		foreach ( $breakdowns as $breakdown ) {
			$title = isset( $breakdown['title'] ) ? $breakdown['title'] : '';
			$rating = isset( $breakdown['rating'] ) ? $breakdown['rating'] : '';
		?>
			<div class="breakdown group" data-breakdown_id="<?php echo esc_attr( $index ); ?>">
				<div class="header"><?php printf( esc_html__( 'Breakdown #%d', 'extra' ), $index ); ?></div>
				<div class="content">
					<div class="delete_breakdown">X</div>
					<p class="field_wrap">
						<label for="_post_review_box_breakdowns[<?php echo esc_attr( $index ); ?>][title]"><?php esc_html_e( 'Title:', 'extra' ); ?></label>
						<input class="widefat" id="_post_review_box_breakdowns[<?php echo esc_attr( $index ); ?>][title]" name="_post_review_box_breakdowns[<?php echo esc_attr( $index ); ?>][title]" type="text" value="<?php echo esc_attr( $title ); ?>" />
					</p>
					<p class="field_wrap">
						<label for="_post_review_box_breakdowns[<?php echo esc_attr( $index ); ?>][rating]"><?php esc_html_e( 'Rating (%):', 'extra' ); ?></label>
						<input class="widefat" id="_post_review_box_breakdowns[<?php echo esc_attr( $index ); ?>][rating]" name="_post_review_box_breakdowns[<?php echo esc_attr( $index ); ?>][rating]" type="text" value="<?php echo esc_attr( $rating ); ?>" />
					</p>
				</div>
			</div>
		<?php $index++; ?>
		<?php
		} ?>
		</div> <!-- /.breakdowns_countainer -->
		<button class="button button-small add_breakdown"><?php esc_html_e( 'Add Breakdown', 'extra' ); ?></button>
		<?php
	}

	function save_fields( $post_id ) {
		foreach ( $this->fields as $field_key => $field ) {

			$value_sanitize_function = !empty( $field['value_sanitize_function'] ) ? $field['value_sanitize_function'] : 'sanitize_text_field';

			if ( isset( $_POST[ $field_key ] ) ) {
				$breakdowns_count = 0;
				$ratings = array();
				$breakdowns = array();
				if ( '_post_review_box_breakdowns' == $field_key ) {
					foreach ( $_POST['_post_review_box_breakdowns'] as $_breakdown ) {
						$breakdown = array();
						$breakdown['title'] = sanitize_text_field( $_breakdown['title'] );
						$breakdown['rating'] = absint( str_ireplace( '%', '', $_breakdown['rating'] ) );
						$breakdown['rating'] = min( 100, $breakdown['rating'] );
						$breakdown['rating'] = max( 0, $breakdown['rating'] );
						$ratings[] = $breakdown['rating'];
						$breakdowns[] = $breakdown;
						$breakdowns_count++;
					}

					$value = $breakdowns;
					$breakdowns_score = array_sum( $ratings ) / count( $ratings );
					update_post_meta( $post_id, '_post_review_box_breakdowns_score', $breakdowns_score );
					update_post_meta( $post_id, '_post_review_box_breakdowns_count', $breakdowns_count );
				} else if ( '_post_review_box_breakdowns_count' == $field_key ) {
					// skip saving whatever was POST'ed as its set manually by counting actual POST'ed breakdowns
					continue;
				} else {
					$value = $value_sanitize_function( $_POST[ $field_key ] );
				}
			}

			if ( isset( $_POST[ $field_key ] ) ) {
				update_post_meta( $post_id, $field_key, $value );
			} else {
				delete_post_meta( $post_id, $field_key );
			}
		}
	}

}
new Extra_Post_Review_Meta_Box;

class Extra_Gallery_Post_Format_Meta_Box extends Extra_Post_Format_Meta_Box {

	public function __construct() {
		parent::__construct( 'gallery-post-format', esc_html__( 'Gallery Format Options', 'extra' ), 'gallery' );
	}

	function fields() {
		$this->fields = array(
			'_gallery_format_attachment_ids' => array(),
			'_gallery_format_autoplay'       => array(
				'title'                   => esc_html__( 'Autoplay Speed', 'extra' ),
				'description'             => esc_html__( 'The speed, in seconds, in which the slider will auto rotate to the next slide. Leave empty to disable autoplay.', 'extra' ),
				'value_sanitize_function' => 'absint',
			),
		);
	}

	function display( $post ) {
		$attachment_ids = $this->fields['_gallery_format_attachment_ids'];
		?>
		<button class="button button-small" id="et_gallery_add_images" data-title="<?php esc_attr_e( 'Add Gallery Images', 'extra' ); ?>" data-title="<?php esc_attr_e( 'Add Images', 'extra' ); ?>"><?php esc_html_e( 'Add Images', 'extra' ); ?></button>
		<ul id="et_gallery_images" class="clearfix">
		<?php

		$attachment_ids = trim( $attachment_ids['value'] );

		if ( !empty( $attachment_ids ) ) {

			$attachment_ids = explode( ',', $attachment_ids );

			foreach ( $attachment_ids as $attachment_id_key => $attachment_id ) {
				$attachment_attributes = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
				if ( empty( $attachment_attributes ) ) {
					unset( $attachment_ids[$attachment_id_key ] );
					continue;
				}

				printf(
					'<li class="gallery_image" data-id="%d">
						<span class="delete">-</span>
						<img src="%s" />
					</li>',
					esc_attr( $attachment_id ),
					esc_attr( $attachment_attributes[0] )
				);
			}
		}

		$attachment_ids = !empty( $attachment_ids ) ? implode( ',', $attachment_ids ) : '';

		?>
		</ul>
		<input type="hidden" id="et_gallery_images_ids" name="_gallery_format_attachment_ids" value="<?php echo esc_attr( $attachment_ids ); ?>" />
		<?php

		$autoplay = $this->fields['_gallery_format_autoplay'];
		echo '<div class="form-field">';

		printf( '<p><strong>%s</strong></p>',
			esc_html( $autoplay['title'] )
		);

		echo '<input type="text" name="_gallery_format_autoplay" value="' . esc_attr( $autoplay['value'] ) . '" />';
		echo '<p class="description">' . esc_html( $autoplay['description'] ) . '</p>';

		echo '</div><br />';
	}

}
new Extra_Gallery_Post_Format_Meta_Box;

class Extra_Audio_Post_Format_Meta_Box extends Extra_Post_Format_Meta_Box {

	public function __construct() {
		parent::__construct( 'audio-post-format', esc_html__( 'Audio Format Options', 'extra' ), 'audio' );
	}

	function before_display() {
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

		parent::before_display();
	}

	function fields() {
		$this->fields = array(
			'_audio_format_file_url'         => array(
				'title' => esc_html__( 'Audio File URL', 'extra' ),
			),
			'_audio_format_background_color' => array(
				'title' => esc_html__( 'Background Color', 'extra' ),
			),
			'_audio_format_title'            => array(
				'title' => esc_html__( 'Audio File Title', 'extra' ),
			),
			'_audio_format_sub_title'        => array(
				'title' => esc_html__( 'Audio File Subtitle', 'extra' ),
			),
		);
	}

	function get_post_default_color() {
		global $post;

		$categories = wp_get_post_categories( $post->ID );

		$color = '';
		if ( !empty( $categories ) ) {
			$first_category_id = $categories[0];
			$color = et_get_childmost_taxonomy_meta( $first_category_id, 'color', true, et_get_option( 'accent_color', '#00A8FF' ) );
		}
		return $color;
	}

	function display( $post ) {
		$url = $this->fields['_audio_format_file_url'];
		echo '<div class="form-field">';

		printf( '<p><strong>%s</strong></p>',
			esc_html( $url['title'] )
		);

		echo '<input type="text" id="_audio_format_file_url" name="_audio_format_file_url" value="' . esc_attr( $url['value'] ) . '" />';
		echo '<button class="button button-small et_media_add_media" type="button" data-media-type="audio" data-url-field="#_audio_format_file_url" data-title="' . esc_attr__( 'Add Audio File', 'extra' ) . '">' . esc_html__( 'Add Audio File', 'extra' ) . '</button>';
		echo '</div><br />';

		$bg_color = $this->fields['_audio_format_background_color'];
		echo '<div class="form-field">';

		printf( '<p><strong>%s</strong></p>',
			esc_html__( $bg_color['title'], 'extra' )
		);

		$color = $this->get_post_default_color();

		$default_attr = ' data-default-color="' . esc_attr( $color ) . '"';

		$value_attr = ' value="' . esc_attr( $bg_color['value'] ) . '"';

		echo '<input type="text" class="color-picker-hex" name="_audio_format_background_color" placeholder="' . esc_attr__( 'Hex Value', 'extra' ) .'" ' . $default_attr . ' ' . $value_attr .' />';

		echo '</div><br />';

		$title = $this->fields['_audio_format_title'];
		echo '<div class="form-field">';

		printf( '<p><strong>%s</strong></p>',
			esc_html( $title['title'] )
		);

		echo '<input type="text" name="_audio_format_title" value="' . esc_attr( $title['value'] ) . '" />';

		echo '</div><br />';

		$sub_title = $this->fields['_audio_format_sub_title'];
		echo '<div class="form-field">';

		printf( '<p><strong>%s</strong></p>',
			esc_html__( $sub_title['title'], 'extra' )
		);

		echo '<input type="text" name="_audio_format_sub_title" value="' . esc_attr( $sub_title['value'] ) . '" />';

		echo '</div><br />';
	}

}
new Extra_Audio_Post_Format_Meta_Box;

class Extra_Quote_Post_Format_Meta_Box extends Extra_Post_Format_Meta_Box {

	public function __construct() {
		parent::__construct( 'quote-post-format', esc_html__( 'Quote Format Options', 'extra' ), 'quote' );
	}

	function fields() {
		$this->fields = array(
			'_quote_format_quote'             => array(
				'title' => esc_html__( 'Quote Text', 'extra' ),
			),
			'_quote_format_quote_attribution' => array(
				'title' => esc_html__( 'Quote Attribution', 'extra' ),
			),
		);
	}

}
new Extra_Quote_Post_Format_Meta_Box;

class Extra_Link_Post_Format_Meta_Box extends Extra_Post_Format_Meta_Box {

	public function __construct() {
		parent::__construct( 'link-post-format', esc_html__( 'Link Format Options', 'extra' ), 'link' );
	}

	function fields() {
		$this->fields = array(
			'_link_format_link_title' => array(
				'title' => esc_html__( 'Link Title', 'extra' ),
			),
			'_link_format_link_url'   => array(
				'title' => esc_html__( 'Link URL', 'extra' ),
			),
		);
	}

}
new Extra_Link_Post_Format_Meta_Box;

class Extra_Video_Post_Format_Meta_Box extends Extra_Post_Format_Meta_Box {

	public function __construct() {
		parent::__construct( 'video-post-format', esc_html__( 'Video Format Options', 'extra' ), 'video' );
	}

	public function fields() {
		$this->fields = array(
			'_video_format_urls' => array(
				'value_sanitize_function' => 'esc_url_raw',
				'value_type'              => 'array',
			),
		);
	}

	public function display( $post ) {
		$urls = explode( ',', $this->fields['_video_format_urls']['value'] );
		$breakdowns_count = !empty( $this->fields['_post_review_box_breakdowns_count']['value'] ) ? $this->fields['_post_review_box_breakdowns_count']['value'] : 1;
		?>
		<p class="description">
		<?php
		printf(
			et_get_safe_localization( __( 'The URL of the video from a <a href="%s" target="_blank">Supported oEmbed Video Provider</a>, or URL of any video. Click "Add Video URL" to add more file types. (.mov, .mp4, .webm, .ogv, etc)', 'extra' ) ),
			esc_url( 'https://developer.wordpress.org/reference/hooks/oembed_providers/' )
		);
		?>
		</p>
		<div class="video_urls_container">
		<?php
		$index = 0;
		foreach ( $urls as $url ) {
		?>
			<div class="video_url">
			<div class="header"><?php esc_html_e( 'Video URL', 'extra' ); ?></div>
				<div class="content">
					<div class="delete_video_url">X</div>
					<div class="form-field">
						<p>
							<?php $url_field_id = '_video_format_url_' . esc_attr( $index ); ?>
							<input type="text" id="<?php echo $url_field_id; ?>" name="_video_format_urls[]" value="<?php echo esc_attr( $url ); ?>" />
							<button class="button button-small et_media_add_media" type="button" data-media-type="video" data-url-field="#<?php echo $url_field_id; ?>" data-title="<?php esc_attr_e( 'Upload A Video File', 'extra' ); ?>"><?php esc_html_e( 'Upload A Video File', 'extra' ); ?></button>
						</p>
					</div>
				</div>
			</div>
			<?php $index++; ?>
		<?php } ?>
		</div>
		<button class="button button-small add_video_url"><?php esc_html_e( 'Add Another Video URL', 'extra' ); ?></button>
	<?php
	}

}
new Extra_Video_Post_Format_Meta_Box;

class Extra_Map_Post_Format_Meta_Box extends Extra_Post_Format_Meta_Box {

	public function __construct() {
		parent::__construct( 'map-post-format', esc_html__( 'Map Format Options', 'extra' ), 'map' );
	}

	function before_display() {
		et_extra_enqueue_google_maps_api();
		parent::before_display();
	}

	function fields() {
		$this->fields = array(
			'_map_format_address' => array(
				'title' => esc_html__( 'Address' ),
			),
			'_map_format_zoom'    => array(),
			'_map_format_lat'     => array(),
			'_map_format_lng'     => array(),
		);
	}

	function display( $post ) {
		$address = $this->fields[ '_map_format_address' ];
		$lat = $this->fields[ '_map_format_lat' ];
		$lng = $this->fields[ '_map_format_lng' ];
		$zoom = $this->fields[ '_map_format_zoom' ];

		// Display Google API Key Notice
		printf(
			'<div class="form-field">
				<p><strong>%1$s</strong></p>
				<p>%2$s</p>
				%3$s
				<a href="%4$s" target="_blank" class="button extra_google_api_key_button">%5$s</a>
			</div>',
			esc_html__( 'Google API Key', 'extra' ),
			et_get_safe_localization( sprintf( __( 'The Map feature uses the Google Maps API and requires a valid Google API Key to function. Before using the map feature, please make sure you have added your API key inside the Extra Theme Options panel. Learn more about how to create your Google API Key <a href="%1$s" target="_blank">here</a>.', 'extra' ), esc_url( 'http://www.elegantthemes.com/gallery/divi/documentation/map/#gmaps-api-key' ) ) ),
			'' !== et_pb_get_google_api_key()
				? sprintf( '<input type="text" id="extra_google_api_key" readonly value="%1$s" />', esc_attr( et_pb_get_google_api_key() ) )
				: '',
			esc_url( admin_url( 'admin.php?page=et_extra_options' ) ),
			'' === et_pb_get_google_api_key() ? esc_html__( 'Add API Key', 'extra' ) : esc_html__( 'Change Your API Key', 'extra' )
		);

		echo '<div class="form-field">';

		printf( '<p><strong>%s</strong></p>',
			esc_html__( $address['title'], 'extra' )
		);

		echo '<input type="text" id="map_format_address" name="_map_format_address" value="' . esc_attr( $address['value'] ) . '" />';
		echo '<button class="button button-small" id="map_format_find" >'. esc_html__( 'Find', 'extra' ) . '</button>';
		echo '<input type="hidden" id="map_format_lat" name="_map_format_lat" value="' . esc_attr( $lat['value'] ) . '" />';
		echo '<input type="hidden" id="map_format_lng" name="_map_format_lng" value="' . esc_attr( $lng['value'] ) . '" />';
		echo '<input type="hidden" id="map_format_zoom" name="_map_format_zoom" value="' . esc_attr( $zoom['value'] ) . '" />';

		echo '</div><br />';

		echo '<div class="form-field">';
			echo '<div class="et-post-format-map" style="height:300px;width:100%;"></div>';
		echo '</div>';
	}

}
new Extra_Map_Post_Format_Meta_Box;

class Extra_Authors_Page_Template_Meta_Box extends Extra_Page_Template_Meta_Box {

	public function __construct() {
		parent::__construct( 'authors-page-template', esc_html__( 'Authors Page Template Options', 'extra' ), 'page-template-authors.php' );
	}

	function fields() {
		$this->fields = array(
			'_authors_page_authors_all' => array(),
			'_authors_page_authors'     => array(
				'value_type'              => 'array',
				'value_sanitize_function' => 'absint',
			),
		);
	}

	function display( $post ) {
		global $wp_version;

		$authors_all = $this->fields['_authors_page_authors_all']['value'];

		$checked_users = explode( ',', $this->fields['_authors_page_authors']['value'] );

		echo '<div class="form-field">';

		printf( '<p><strong>%s</strong></p>',
			esc_html__( 'Authors', 'extra' )
		);

		$query_args = array(
			'order'   => 'ASC',
			'orderby' => 'display_name',
		);

		// Alternative for deprecated `who` property and backward compatibility.
		if ( version_compare( $wp_version, '5.9-beta', '>=' ) ) {
			$query_args['capability'] = array( 'edit_posts' );
		} else {
			$query_args['who'] = 'authors';
		}

		$users = get_users( $query_args );

		echo '<div class="wp-tab-panel"><ul class="categorychecklist checklist">';

		printf( '<li><label><input type="checkbox" name="_authors_page_authors_all" class="check_all" value="1" %2$s> %1$s</label></li>',
			esc_html__( 'All', 'extra' ),
			checked( $authors_all, '1' )
		);

		foreach ( $users as $user ) {
			$user->ID = (int) $user->ID;
			$display = !empty( $user->display_name ) ? $user->display_name : '('. $user->user_login . ')';

			printf( '<li><label><input type="checkbox" name="_authors_page_authors[]" value="%1$s"%3$s> %2$s</label></li>',
				esc_attr( $user->ID ),
				esc_html( $display ),
				in_array( $user->ID, $checked_users ) || !empty( $authors_all ) ? ' checked="checked"' : ''
			);
		}
		echo '</ul></div>';

		echo '</div><br />';
		?>

		<?php
	}

}
new Extra_Authors_Page_Template_Meta_Box;

class Extra_Blog_Feed_Page_Template_Meta_Box extends Extra_Page_Template_Meta_Box {

	public function __construct() {
		parent::__construct( 'blog-feed-page-template', esc_html__( 'Blog Feed Page Template Options', 'extra' ), 'page-template-blog-feed.php' );
	}

	function category_field_renderer( $field ) {
		$name = $field['name'] . '[]';
		$output = '';
		$selected_categories = explode( ',', $field['value'] );
		$cats_array = get_categories( 'hide_empty=0' );
		foreach ( $cats_array as $categs ) {
			$output .= sprintf( '<li><label><input type="checkbox" name="%1$s" value="%2$s" %3$s> %4$s</label></li>',
				esc_attr( $name ),
				esc_attr( $categs->cat_ID ),
				in_array( $categs->cat_ID, $selected_categories ) ? ' checked="checked"' : '',
				esc_html( $categs->cat_name )
			);
		}

		$output = sprintf(
			'<div class="wp-tab-panel"><ul class="categorychecklist checklist">
				<li><label><input type="checkbox" class="check_all"%1$s> %2$s</label></li>
				%3$s
			</ul></div>',
			empty( $field['value'] ) ? ' checked="checked"' : '',
			esc_html__( 'All', 'extra' ),
			$output
		);

		$field['el'] = $output;
		return $this->field_wrap( $field );
	}

	function fields() {
		$this->fields = array(
			'_blog_feed_page_display_style'       => array(
				'title'       => esc_html__( 'Display Style', 'extra' ),
				'type'        => 'select',
				'default'     => strtolower( strval( et_get_option( 'archive_list_style', 'standard' ) ) ),
				'options'     => array(
					'standard' => esc_html__( 'Standard', 'extra' ),
					'masonry'  => esc_html__( 'Masonry', 'extra' ),
				),
				'description' => esc_html__( 'Display method', 'extra' ),
			),
			'_blog_feed_page_categories'          => array(
				'title'                   => esc_html__( 'Categories', 'extra' ),
				'type'                    => 'custom',
				'description'             => esc_html__( 'Choose Categories.', 'extra' ),
				'renderer'                => array(
					$this,
					'category_field_renderer',
				),
				'value_type'              => 'array',
				'value_sanitize_function' => 'absint',
			),
			'_blog_feed_page_posts_per_page'      => array(
				'title'       => esc_html__( 'Posts Per Page', 'extra' ),
				'type'        => 'input',
				'description' => esc_html__( 'The number of posts shown per page.', 'extra' ),
			),
			'_blog_feed_page_orderby'             => array(
				'title'       => esc_html__( 'Sort Method', 'extra' ),
				'type'        => 'select',
				'default'     => 'date',
				'options'     => array(
					'date'          => esc_html__( 'Most Recent', 'extra' ),
					'comment_count' => esc_html__( 'Most Popular', 'extra' ),
					'rating'        => esc_html__( 'Highest Rated', 'extra' ),
				),
				'description' => esc_html__( 'Choose a sort method.', 'extra' ),
			),
			'_blog_feed_page_order'               => array(
				'title'       => esc_html__( 'Sort Order', 'extra' ),
				'type'        => 'select',
				'default'     => 'desc',
				'options'     => array(
					'desc' => esc_html__( 'Descending', 'extra' ),
					'asc'  => esc_html__( 'Ascending', 'extra' ),
				),
				'description' => esc_html__( 'Choose a sort order.', 'extra' ),
			),
			'_blog_feed_page_show_author'         => array(
				'title'       => esc_html__( 'Show Author', 'extra' ),
				'type'        => 'select',
				'options'     => array(
					'1' => esc_html__( 'Show Author', 'extra' ),
					'0' => esc_html__( "Don't Show Author", 'extra' ),
				),
				'description' => esc_html__( "Turn the display of each post's author on or off.", 'extra' ),
			),
			'_blog_feed_page_show_categories'     => array(
				'title'       => esc_html__( 'Show Categories', 'extra' ),
				'type'        => 'select',
				'default'     => '1',
				'options'     => array(
					'1' => esc_html__( 'Show Categories', 'extra' ),
					'0' => esc_html__( "Don't Show Categories", 'extra' ),
				),
				'description' => esc_html__( "Turn the display of each post's categories on or off.", 'extra' ),
			),
			'_blog_feed_page_show_featured_image' => array(
				'title'       => esc_html__( 'Show Featured Image', 'extra' ),
				'type'        => 'select',
				'default'     => '1',
				'options'     => array(
					'1' => esc_html__( 'Show Featured Image', 'extra' ),
					'0' => esc_html__( "Don't Show Featured Image", 'extra' ),
				),
				'description' => esc_html__( "Turn the display of each post's featured image on or off.", 'extra' ),
			),
			'_blog_feed_page_show_ratings'        => array(
				'title'       => esc_html__( 'Show Ratings', 'extra' ),
				'type'        => 'select',
				'default'     => '1',
				'options'     => array(
					'1' => esc_html__( 'Show Ratings', 'extra' ),
					'0' => esc_html__( "Don't Show Ratings", 'extra' ),
				),
				'description' => esc_html__( "Turn the display of each post's rating stars on or off.", 'extra' ),
			),
			'_blog_feed_page_content_length'      => array(
				'title'       => esc_html__( 'Content', 'extra' ),
				'type'        => 'select',
				'options'     => array(
					'excerpt' => esc_html__( 'Show Excerpt', 'extra' ),
					'full'    => esc_html__( "Show Full Content", 'extra' ),
				),
				'description' => esc_html__( "Display the post's exceprt or full content. If full content, then it will truncate to the more tag if used.", 'extra' ),
			),
			'_blog_feed_page_show_date'           => array(
				'title'       => esc_html__( 'Show Date', 'extra' ),
				'type'        => 'select',
				'default'     => '1',
				'options'     => array(
					'1' => esc_html__( 'Show Date', 'extra' ),
					'0' => esc_html__( "Don't Show Date", 'extra' ),
				),
				'description' => esc_html__( "Turn the display of each post's date on or off.", 'extra' ),
			),
			'_blog_feed_page_date_format'         => array(
				'title'       => esc_html__( 'Date Format', 'extra' ),
				'type'        => 'input',
				'default'     => 'M j, Y',
				'description' => esc_html__( 'The format for the date display in PHP date() format', 'extra' ),
			),
			'_blog_feed_page_show_comment_count'  => array(
				'title'       => esc_html__( 'Show Comment Count', 'extra' ),
				'type'        => 'select',
				'default'     => '1',
				'options'     => array(
					'1' => esc_html__( 'Show Comment Count', 'extra' ),
					'0' => esc_html__( "Don't Show Comment Count", 'extra' ),
				),
				'description' => esc_html__( "Turn the display of each post's comment count on or off.", 'extra' ),
			),
		);
	}

}
new Extra_Blog_Feed_Page_Template_Meta_Box;


class Extra_Sitemap_Page_Template_Meta_Box extends Extra_Page_Template_Meta_Box {

	public function __construct() {
		parent::__construct( 'sitemap-page-template', esc_html__( 'Sitemap Page Template Options', 'extra' ), 'page-template-sitemap.php' );
	}

	function pre_register_after() {
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	function authors_field_renderer( $field ) {
		global $wp_version;

		if ( !empty( $field['value'] ) ) {
			$checked_users = explode( ',', $field['value'] );
		} else {
			$checked_users = array();
		}

		$query_args = array(
			'order'   => 'ASC',
			'orderby' => 'display_name',
		);

		// Alternative for deprecated `who` property and backward compatibility.
		if ( version_compare( $wp_version, '5.9-beta', '>=' ) ) {
			$query_args['capability'] = array( 'edit_posts' );
		} else {
			$query_args['who'] = 'authors';
		}

		$users = get_users( $query_args );

		echo '<div class="wp-tab-panel"><ul class="categorychecklist checklist">';

		printf( '<li><label><input type="checkbox" class="check_all" %2$s> %1$s</label></li>',
			esc_html__( 'All', 'extra' ),
			( ! empty( $field['default'] ) && 'all' == $field['default'] ? ' checked="checked"' : '' )
		);

		foreach ( $users as $user ) {
			$user->ID = (int) $user->ID;
			$display = !empty( $user->display_name ) ? $user->display_name : '('. $user->user_login . ')';

			if ( ! empty( $field['default'] ) && 'all' == $field['default'] ? ' checked="checked"' : '' ) {
				$checked = ' checked="checked"';
			} else if ( in_array( $user->ID, $checked_users ) ) {
				$checked = ' checked="checked"';
			} else {
				$checked = '';
			}

			printf( '<li><label><input type="checkbox" name="_sitemap_page_authors_include[]" value="%1$s"%3$s> %2$s</label></li>',
				esc_attr( $user->ID ),
				esc_html( $display ),
				$checked
			);
		}
		echo '</ul></div>';
	}

	function fields() {
		$this->fields = array(
			'_sitemap_page_sections'           => array(
				'value_type'              => 'array',
				'value_sanitize_function' => 'sanitize_text_field',
			),
			'_sitemap_page_pages_exclude'      => array(
				'title'       => esc_html__( 'Exclude', 'extra' ),
				'description' => esc_html__( 'Page IDs, separated by commas.', 'extra' ),
				'class'       => 'medium',
			),
			'_sitemap_page_categories_count'   => array(
				'title'       => esc_html__( 'Show Posts Count', 'extra' ),
				'type'        => 'select',
				'options'     => array(
					'0' => esc_html__( 'No', 'extra' ),
					'1' => esc_html__( 'Yes', 'extra' ),
				),
				'description' => esc_html__( 'Display post count', 'extra' ),
			),
			'_sitemap_page_authors_include'    => array(
				'title'                   => esc_html__( 'Authors', 'extra' ),
				'renderer'                => array(
					$this,
					'authors_field_renderer',
				),
				'description'             => esc_html__( 'Authors to display', 'extra' ),
				'value_type'              => 'array',
				'value_sanitize_function' => 'absint',
				'default'                 => 'all',//??
			),
			'_sitemap_page_archives_limit'     => array(
				'title'       => esc_html__( 'Limit', 'extra' ),
				'description' => esc_html__( 'Amount of archive months to display.', 'extra' ),
				'class'       => 'thin',
				'default'     => 12,
			),
			'_sitemap_page_archives_count'     => array(
				'title'       => esc_html__( 'Show Post Count', 'extra' ),
				'type'        => 'select',
				'options'     => array(
					'0' => esc_html__( 'No', 'extra' ),
					'1' => esc_html__( 'Yes', 'extra' ),
				),
				'description' => esc_html__( 'Whether to show the post count.', 'extra' ),
			),
			'_sitemap_page_recent_posts_limit' => array(
				'title'       => esc_html__( 'Limit', 'extra' ),
				'description' => esc_html__( 'Amount of recent posts to display.', 'extra' ),
				'class'       => 'thin',
				'default'     => 10,
			),

		);
	}

	function display( $post ) {
		$sections = array(
			'pages'        => esc_html__( 'Pages', 'extra' ),
			'categories'   => esc_html__( 'Categories', 'extra' ),
			'tags'         => esc_html__( 'Tags', 'extra' ),
			'recent_posts' => esc_html__( 'Recent Posts', 'extra' ),
			'archives'     => esc_html__( 'Archives', 'extra' ),
			'authors'      => esc_html__( 'Authors', 'extra' ),
		);

		if ( !empty( $this->fields['_sitemap_page_sections']['value'] ) ) {
			$checked_sections = explode( ',', $this->fields['_sitemap_page_sections']['value'] );
		} elseif ( isset( $post->post_status ) && 'auto-draft' === $post->post_status ) {
			$checked_sections = array_keys( $sections );
			$use_defaults = true;
		} else {
			$checked_sections = array();
		}

		echo '<div class="form-field">';

		printf( '<p><strong>%s</strong></p>',
			esc_html__( 'Sitemap Sections Display', 'extra' )
		);

		echo '<p class="description">' . esc_html__( 'Check and sort the sitemap sections you would like to be displayed.', 'extra' ) . '</p>';

		$section_fields = array(
			'pages'        => array(
				'exclude'
			),
			'categories'   => array(
				'count'
			),
			'tags'         => array(),
			'recent_posts' => array(
				'limit'
			),
			'archives'     => array(
				'limit',
				'count',
			),
			'authors'      => array(
				'include'
			),
		);

		$sections_all = $sections;

		$sorted_sections = array();
		foreach ( $checked_sections as $checked_section ) {
			$sorted_sections[$checked_section] = $sections[$checked_section];
			unset( $sections[$checked_section] );
		}

		$sections = $sorted_sections + $sections;

		echo '<div id="sitemap-sections" class="wp-tab-panel"><ul class="checklist">';

		printf( '<li><label><input type="checkbox" name="_sitemap_page_sections_all" class="check_all" value="1" %2$s> %1$s</label></li>',
			esc_html__( 'All', 'extra' ),
			checked( count( $checked_sections ) == count( $sections ), true, false )
		);

		foreach ( $sections as $slug => $name ) {
			printf( '<li class="sortable"><label><input type="checkbox" name="_sitemap_page_sections[]" value="%1$s"%3$s> %2$s</label></li>',
				esc_attr( $slug ),
				esc_html( $name ),
				in_array( $slug, $checked_sections ) ? ' checked="checked"' : ''
			);
		}
		echo '</ul></div>';

		echo '</div><br />';

		foreach ( $sections_all as $section_slug => $section_name ) {
			echo '<div id="sitemap_page_section_' . esc_attr( $section_slug ) . '" class="sitemap_page_section">';

			$section_name = sprintf( '%s Section Options', $section_name );
			echo '<h3 class="sitemap_section_heading">' . esc_html( $section_name ) . '</h3>';

			foreach ( $section_fields[ $section_slug ] as $field ) {
				$field_key = '_sitemap_page_' . $section_slug . '_' . $field;
				$field = $this->fields[ $field_key ];

				if ( empty( $use_defaults ) ) {
					unset( $field['default'] );
				}

				echo $this->render_field( $field );
			}
			echo '</div>';
		}
		?>
		<?php
	}

}
new Extra_Sitemap_Page_Template_Meta_Box;

class Extra_Portfolio_Page_Template_Meta_Box extends Extra_Page_Template_Meta_Box {

	public function __construct() {
		parent::__construct( 'portfolio-page-template', esc_html__( 'Portfolio Page Template Options', 'extra' ), 'page-template-portfolio.php' );
	}

	function fields() {
		$this->fields = array(
			'_portfolio_project_categories' => array(
				'value_type'              => 'array',
				'value_sanitize_function' => 'absint',
			),
			'_portfolio_hide_title'         => array(
				'value_type' => 'checkbox',
			),
			'_portfolio_hide_categories'    => array(
				'value_type' => 'checkbox',
			),
		);
	}

	function display( $post ) {
		echo '<div class="form-field">';

		printf( '<p><strong>%s</strong></p>',
			esc_html__( 'Project Categories', 'extra' )
		);

		echo '<div class="wp-tab-panel"><ul class="categorychecklist checklist">';

		$checked_categories = explode( ',', $this->fields['_portfolio_project_categories']['value'] );

		$args = array();

		$categories = get_terms( EXTRA_PROJECT_CATEGORY_TAX, $args );

		foreach ( $categories as $category ) {
			printf( '<li><label><input type="checkbox" name="_portfolio_project_categories[]" value="%1$s"%3$s> %2$s</label></li>',
				esc_attr( $category->term_id ),
				esc_html( $category->name ),
				in_array( $category->term_id, $checked_categories ) ? ' checked="checked"' : ''
			);
		}
		echo '</ul></div>';

		echo '</div><br />';

		?>
		<div class="form-field">
			<label>
				<input type="checkbox" name="_portfolio_hide_title" value="1" <?php checked( $this->fields['_portfolio_hide_title']['value'], '1' ); ?> />
				<?php echo esc_html__( 'Hide Project Titles', 'extra' ); ?>
			</label>
		</div>
		<br />
		<?php

		?>
		<div class="form-field">
			<label>
				<input type="checkbox" name="_portfolio_hide_categories" value="1" <?php checked( $this->fields['_portfolio_hide_categories']['value'], '1' ); ?> />
				<?php echo esc_html__( 'Hide Project Categories', 'extra' ); ?>
			</label>
		</div>
		<br />
		<?php
	}

}
new Extra_Portfolio_Page_Template_Meta_Box;

class Extra_Contact_Page_Template_Meta_Box extends Extra_Page_Template_Meta_Box {

	public function __construct() {
		parent::__construct( 'contact-page-template', esc_html__( 'Contact Page Template Options', 'extra' ), 'page-template-contact.php' );
	}

	function pre_register_after() {
		et_extra_enqueue_google_maps_api();
	}

	function fields() {
		$this->fields = array(
			'_contact_form_title'           => array(),
			'_contact_form_email'           => array(
				'value_sanitize_function' => 'sanitize_email',
			),
			'_contact_form_map_zoom'        => array(),
			'_contact_form_map_address'     => array(),
			'_contact_form_map_address_lat' => array(),
			'_contact_form_map_address_lng' => array(),
		);
	}

	function display( $post ) {
		?>
		<div class="form-field">
			<label>
				<p><?php esc_html_e( 'Contact Form Title', 'extra' ); ?></p>
				<input type="text" name="_contact_form_title" value="<?php echo esc_attr( $this->fields['_contact_form_title']['value'] ); ?>" />
			</label>
		</div>

		<div class="form-field">
			<label>
				<p><?php esc_html_e( 'Send Email to', 'extra' ); ?></p>
				<input type="text" name="_contact_form_email" value="<?php echo esc_attr( $this->fields['_contact_form_email']['value'] ); ?>" />
			</label>
		</div>

		<div class="form-field">
			<p><strong><?php esc_html_e( 'Contact Form Map', 'extra' ); ?></strong></p>
		</div>

		<?php
			// Display Google API Key Notice
			printf(
				'<div class="form-field">
					<p>%1$s</p>
					<p>%2$s</p>
					%3$s
					<a href="%4$s" target="_blank" class="button extra_google_api_key_button">%5$s</a>
				</div>',
				esc_html__( 'Google API Key', 'extra' ),
				et_get_safe_localization( sprintf( __( 'The Map feature uses the Google Maps API and requires a valid Google API Key to function. Before using the map feature, please make sure you have added your API key inside the Extra Theme Options panel. Learn more about how to create your Google API Key <a href="%1$s" target="_blank">here</a>.', 'extra' ), esc_url( 'http://www.elegantthemes.com/gallery/divi/documentation/map/#gmaps-api-key' ) ) ),
				'' !== et_pb_get_google_api_key()
					? sprintf( '<input type="text" id="extra_google_api_key" readonly value="%1$s" />', esc_attr( et_pb_get_google_api_key() ) )
					: '',
				esc_url( admin_url( 'admin.php?page=et_extra_options' ) ),
				'' === et_pb_get_google_api_key() ? esc_html__( 'Add API Key', 'extra' ) : esc_html__( 'Change Your API Key', 'extra' )
			);
		?>

		<div class="form-field">
			<p><?php esc_html_e( 'Map Pin Address', 'extra' ); ?></p>
			<input type="text" name="_contact_form_map_address" id="contact_form_map_address" value="<?php echo esc_attr( $this->fields['_contact_form_map_address']['value'] ); ?>">
			<button class="button button-small" id="contact_form_map_address_find" ><?php esc_html_e( 'Find', 'extra' ); ?></button>
			<p class="description"><?php esc_html_e( 'Type an address and click find to place a marker at that address.', 'extra' );?></p>
			<input type="hidden" name="_contact_form_map_address_lat" id="contact_form_map_address_lat" value="<?php echo esc_attr( $this->fields['_contact_form_map_address_lat']['value'] ); ?>">
			<input type="hidden" name="_contact_form_map_address_lng" id="contact_form_map_address_lng" value="<?php echo esc_attr( $this->fields['_contact_form_map_address_lng']['value'] ); ?>">
			<input type="hidden" name="_contact_form_map_zoom" id="contact_form_map_zoom" value="<?php echo esc_attr( $this->fields['_contact_form_map_zoom']['value'] ); ?>">
			<div class="contact-map" style="height:300px;width:100%;"></div>
			<p class="description"><?php esc_html_e( 'Drag the marker and the address will be updated automatically. Change the zoom and the zoom level will be saved as well.', 'extra' );?></p>
		</div>
		<?php
	}

}
new Extra_Contact_Page_Template_Meta_Box;

class Extra_Layout_Home_Selector_Meta_Box extends ET_Meta_Box {

	public function __construct(){
		parent::__construct( 'layout-home', esc_html__( 'Layout Usage', 'extra' ), array(
			'post_type' => EXTRA_LAYOUT_POST_TYPE,
			'context'   => 'side',
		) );
	}

	function fields() {
		$this->fields = array(
			EXTRA_HOME_LAYOUT_META_KEY    => array(
				'title' => esc_html__( 'Use this layout as the home layout?', 'extra' ),
			),
			EXTRA_DEFAULT_LAYOUT_META_KEY => array(
				'title'       => esc_html__( 'Use this layout as the default layout?', 'extra' ),
				'description' => esc_html__( 'Use as default for any category that does not have a specific layout set.', 'extra' ),
			),
		);
	}

	function display( $post ) {
		foreach ( $this->fields as $key => $field ) {
			?>
			<div class="form-field">
				<label>
					<input type="checkbox" name="<?php echo esc_attr( $key ); ?>" value="1" <?php checked( $field['value'], '1' ); ?> />
					<?php echo esc_html( $field['title'] ); ?>
				</label>
				<?php if ( !empty( $field['description'] ) ) { ?>
					<p class="description"><?php echo esc_html( $field['description'] ); ?></p>
				<?php } ?>
			</div>
			<br />
			<?php
		}
	}

	public function save( $post_id, $post ) {
		if ( isset( $_POST[ EXTRA_HOME_LAYOUT_META_KEY ] ) ) {
			update_option( 'show_on_front', 'layout' );
		} elseif ( get_post_meta( $post_id, EXTRA_HOME_LAYOUT_META_KEY, true ) ) {
			$post_type_fallback = get_option( 'page_on_front' ) ? 'page' : 'posts';

			update_option( 'show_on_front', $post_type_fallback );
		}

		foreach ( $this->fields as $meta_key => $field ) {
			if ( isset( $_POST[ $meta_key ] ) ) {
				$args = array(
					'meta_key'       => $meta_key,
					'meta_value'     => 1,
					'posts_per_page' => 1,
				);

				$layouts = extra_get_layouts( $args );
				if ( !empty( $layouts->post ) ) {
					delete_post_meta( $layouts->post->ID, $meta_key );
				}

				update_post_meta( $post_id, $meta_key, 1 );
			} else {
				delete_post_meta( $post_id, $meta_key );
			}
		}
	}

}
new Extra_Layout_Home_Selector_Meta_Box;

class Extra_Project_Details_Meta_Box extends ET_Meta_Box {

	public function __construct(){
		parent::__construct( 'project-details', esc_html__( 'Project Details', 'extra' ), array(
			'post_type' => EXTRA_PROJECT_POST_TYPE,
		) );
	}

	function fields() {
		$this->fields = array(
			EXTRA_PROJECT_DETAILS_TITLE_META_KEY                  => array(
				'title'   => esc_html__( 'Project Details Title', 'extra' ),
				'default' => esc_html__( 'Project Details:', 'extra' ),
			),
			EXTRA_PROJECT_DETAILS_PROJECT_URL_META_KEY            => array(
				'title' => esc_html__( 'Project URL', 'extra' ),
			),
			EXTRA_PROJECT_DETAILS_PROJECT_URL_TEXT_META_KEY       => array(
				'title'   => esc_html__( 'Project URL Text', 'extra' ),
				'default' => esc_html__( 'View The Project', 'extra' ),
			),
			EXTRA_PROJECT_DETAILS_PROJECT_URL_NEW_WINDOW_META_KEY => array(
				'title' => esc_html__( 'Open Project URL in new window', 'extra' ),
				'type'  => 'checkbox',
			),
			EXTRA_PROJECT_DETAILS_LOCATION_META_KEY               => array(
				'title'   => esc_html__( 'Project Details Location', 'extra' ),
				'type'    => 'select',
				'options' => array(
					'sidebar'    => esc_html__( 'Sidebar, Project Details box below content', 'extra' ),
					'single_col' => esc_html__( 'No sidebar, single column view with Project Details box below content', 'extra' ),
					'split'      => esc_html__( 'Project Details box as sidebar', 'extra' ),
				),
			),
			EXTRA_PROJECT_DETAILS_SHORT_DESC_META_KEY             => array(
				'title' => esc_html__( 'Short description', 'extra' ),
				'type'  => 'textarea',
			),
		);
	}

	function display( $post ) {
		?>
		<div class="form-wrap et-admin-metabox">
			<div class="form-field">
				<label>
					<?php echo $this->fields[EXTRA_PROJECT_DETAILS_TITLE_META_KEY]['title']; ?><br />
					<input type="text" class="regular-text" name="<?php echo EXTRA_PROJECT_DETAILS_TITLE_META_KEY; ?>" value="<?php echo esc_attr( $this->fields[EXTRA_PROJECT_DETAILS_TITLE_META_KEY]['value'] ); ?>" />
				</label>
			</div>
			<div class="form-field">
				<label>
					<?php echo $this->fields[EXTRA_PROJECT_DETAILS_PROJECT_URL_META_KEY]['title']; ?><br />
					<input type="text" class="regular-text" name="<?php echo EXTRA_PROJECT_DETAILS_PROJECT_URL_META_KEY; ?>" value="<?php echo esc_attr( $this->fields[EXTRA_PROJECT_DETAILS_PROJECT_URL_META_KEY]['value'] ); ?>" />
				</label>
				<label>
					<?php echo $this->fields[EXTRA_PROJECT_DETAILS_PROJECT_URL_NEW_WINDOW_META_KEY]['title']; ?>
					<input type="checkbox" name="<?php echo esc_attr( EXTRA_PROJECT_DETAILS_PROJECT_URL_NEW_WINDOW_META_KEY ); ?>" value="1" <?php checked( $this->fields[EXTRA_PROJECT_DETAILS_PROJECT_URL_NEW_WINDOW_META_KEY]['value'], '1' ); ?> />
				</label>
			</div>
			<div class="form-field">
				<label>
					<?php echo $this->fields[EXTRA_PROJECT_DETAILS_PROJECT_URL_TEXT_META_KEY]['title']; ?><br />
					<input type="text" class="regular-text" name="<?php echo EXTRA_PROJECT_DETAILS_PROJECT_URL_TEXT_META_KEY; ?>" value="<?php echo esc_attr( $this->fields[EXTRA_PROJECT_DETAILS_PROJECT_URL_TEXT_META_KEY]['value'] ); ?>" />
				</label>
			</div>
			<div class="form-field">
				<label>
					<?php echo $this->fields[EXTRA_PROJECT_DETAILS_LOCATION_META_KEY]['title']; ?>
					<br />
					<select id="<?php echo EXTRA_PROJECT_DETAILS_LOCATION_META_KEY; ?>" class="extra-admin-input" name="<?php echo EXTRA_PROJECT_DETAILS_LOCATION_META_KEY; ?>">
						<?php $location = $this->fields[EXTRA_PROJECT_DETAILS_LOCATION_META_KEY]['value']; ?>
						<?php foreach ( $this->fields[EXTRA_PROJECT_DETAILS_LOCATION_META_KEY]['options'] as $option_value => $option_title ) { ?>
							<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $option_value, $location ); ?> > <?php echo esc_html( $option_title ); ?></option>
						<?php } ?>
					</select>
				</label>
			</div>
			<div class="form-field">
				<label>
					<?php echo $this->fields[EXTRA_PROJECT_DETAILS_SHORT_DESC_META_KEY]['title']; ?>
					<br />
					<textarea class="large-text code" name="<?php echo EXTRA_PROJECT_DETAILS_SHORT_DESC_META_KEY; ?>"><?php echo esc_textarea( $this->fields[EXTRA_PROJECT_DETAILS_SHORT_DESC_META_KEY]['value'] ); ?></textarea>
				</label>
			</div>
		</div>
		<?php
	}

}
new Extra_Project_Details_Meta_Box;

class Extra_Project_Gallery_Meta_Box extends ET_Meta_Box {

	public function __construct() {
		parent::__construct( 'project-gallery', esc_html__( 'Project Gallery', 'extra' ), array(
			'post_type' => EXTRA_PROJECT_POST_TYPE,
		) );
	}

	function fields() {
		$this->fields = array(
			'_gallery_attachment_ids' => array(),
			'_gallery_autoplay'       => array(
				'title'                   => esc_html__( 'Autoplay Speed', 'extra' ),
				'description'             => esc_html__( 'The speed, in seconds, in which the slider will auto rotate to the next slide. Leave empty to disable autoplay.', 'extra' ),
				'value_sanitize_function' => 'absint',
			),
		);
	}

	function display( $post ) {
		$attachment_ids = $this->fields['_gallery_attachment_ids'];
		?>
		<button class="button button-small" id="et_gallery_add_images" data-title="<?php esc_attr_e( 'Add Gallery Images', 'extra' ); ?>" data-title="<?php esc_attr_e( 'Add Images', 'extra' ); ?>"><?php esc_html_e( 'Add Images', 'extra' ); ?></button>
		<ul id="et_gallery_images" class="clearfix">
		<?php

		$attachment_ids = trim( $attachment_ids['value'] );

		if ( !empty( $attachment_ids ) ) {

			$attachment_ids = explode( ',', $attachment_ids );

			foreach ( $attachment_ids as $attachment_id_key => $attachment_id ) {
				$attachment_attributes = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
				if ( empty( $attachment_attributes ) ) {
					unset( $attachment_ids[$attachment_id_key ] );
					continue;
				}

				printf(
					'<li class="gallery_image" data-id="%d">
						<span class="delete">-</span>
						<img src="%s" />
					</li>',
					esc_attr( $attachment_id ),
					esc_attr( $attachment_attributes[0] )
				);
			}
		}

		$attachment_ids = !empty( $attachment_ids ) ? implode( ',', $attachment_ids ) : '';

		?>
		</ul>
		<input type="hidden" id="et_gallery_images_ids" name="_gallery_attachment_ids" value="<?php echo esc_attr( $attachment_ids ); ?>" />
		<?php

		$autoplay = $this->fields['_gallery_autoplay'];
		echo '<div class="form-field">';

		printf( '<p><strong>%s</strong></p>',
			esc_html( $autoplay['title'] )
		);

		echo '<input type="text" name="_gallery_autoplay" value="' . esc_attr( $autoplay['value'] ) . '" />';
		echo '<p class="description">' . esc_html( $autoplay['description'] ) . '</p>';

		echo '</div><br />';
	}

}
new Extra_Project_Gallery_Meta_Box;

class Extra_Page_Post_Settings_Meta_Box extends ET_Meta_Box {

	public function __construct() {
		$default_post_types = array(
			'post',
			'page',
			EXTRA_LAYOUT_POST_TYPE,
			EXTRA_PROJECT_POST_TYPE,
			'product',
		);
		$all_post_types_with_builder = et_builder_get_builder_post_types();
		$all_supported_post_types = array_merge( $default_post_types, $all_post_types_with_builder );

		parent::__construct( 'extra-page-post-settings', esc_html__( 'Extra Settings', 'extra' ), array(
			'post_type' => $all_supported_post_types,
			'context'   => 'side',
		) );
	}

	function fields() {
		global $wp_registered_sidebars, $post;

		$is_builder_active = et_pb_is_pagebuilder_used();

		if ( 'product' == $post->post_type ) {
			$global_sidebar_location = strval( et_get_option( 'woocommerce_sidebar_location', extra_global_sidebar_location() ) );
		} else {
			$global_sidebar_location = extra_global_sidebar_location();
		}

		$this->fields = array(
			'_extra_sidebar_location' => array(
				'title'      => esc_html__( 'Sidebar location', 'extra' ),
				'type'       => 'select',
				'options'    => array(
					''      => sprintf( esc_html__( 'Default (%s)', 'extra' ), ucwords( $global_sidebar_location ) ),
					'right' => esc_html__( 'Right', 'extra' ),
					'left'  => esc_html__( 'Left', 'extra' ),
					'none'  => esc_html__( 'No Sidebar', 'extra' ),
				),
				'attributes' => array(
					'data-location' => extra_global_sidebar_location(),
				),
			),
		);

		$this->fields['_extra_sidebar'] = array(
			'title'   => esc_html__( 'Choose Sidebar/Widget Area', 'extra' ),
			'type'    => 'select',
			'options' => array(),
		);

		if ( 'product' == $post->post_type ) {
			$global_sidebar = et_get_option( 'woocommerce_sidebar', extra_global_sidebar() );
		} else {
			$global_sidebar = extra_global_sidebar();
		}

		if ( !empty( $wp_registered_sidebars[ $global_sidebar ] ) ) {
			$sidebar = $wp_registered_sidebars[ $global_sidebar ];
			$this->fields['_extra_sidebar']['options'][''] = sprintf( esc_html__( 'Default (%s)', 'extra' ), $sidebar['name'] );
		} else {
			$this->fields['_extra_sidebar']['options'][''] = esc_html__( 'Default', 'extra' );
		}

		if ( $wp_registered_sidebars && is_array( $wp_registered_sidebars ) ) {
			foreach ( $wp_registered_sidebars as $id => $options ) {
				$this->fields['_extra_sidebar']['options'][$id] = $options['name'];
			}
		}

		if ( 'post' == $post->post_type ) {
			$this->fields['_extra_featured_post'] = array(
				'title' => esc_html__( 'Featured Post', 'extra' ),
			);

			// Display Author Box field depending on ePanel value.
			if ( 'on' == et_get_option( 'extra_show_author_box', 'on' ) ) {
				$this->fields['_extra_hide_author_box'] = array(
					'title' => esc_html__( 'Hide Author Box', 'extra' ),
				);
			} else {
				$this->fields['_extra_show_author_box'] = array(
					'title' => esc_html__( 'Show Author Box', 'extra' ),
				);
			}

			// Display Related Posts field depending on ePanel value.
			if ( 'on' == et_get_option( 'extra_show_related_posts', 'on' ) ) {
				$this->fields['_extra_hide_related_posts'] = array(
					'title' => esc_html__( 'Hide Related Posts', 'extra' ),
				);
			} else {
				$this->fields['_extra_show_related_posts'] = array(
					'title' => esc_html__( 'Show Related Posts', 'extra' ),
				);
			}

		}

		if ( extra_check_feature_availability_in_post_type( $post->post_type, 'hide_title_meta_in_single' ) ) {
			$this->fields['_post_extra_title_meta_hide_single'] = array(
				'title'                   => esc_html__( 'Hide Title &amp; Meta on Post', 'extra' ),
				'value_sanitize_function' => 'wp_validate_boolean',
				'type'                    => 'checkbox',
			);
		}

		if ( extra_check_feature_availability_in_post_type( $post->post_type, 'hide_featured_image_in_single' ) ) {
			$this->fields['_post_extra_featured_image_hide_single'] = array(
				'title'                   => esc_html__( 'Hide Featured Image on Post', 'extra' ),
				'value_sanitize_function' => 'wp_validate_boolean',
				'type'                    => 'checkbox',
			);
		}

		if ( et_get_post_meta_setting( 'all', 'rating_stars' ) && in_array( $post->post_type, extra_get_rating_post_types() ) ) {
			$this->fields['_post_extra_rating_hide'] = array(
				'title'                   => esc_html__( 'Hide Post Rating', 'extra' ),
				'value_sanitize_function' => 'wp_validate_boolean',
				'type'                    => 'checkbox',
			);
		}
	}

	function display( $post ) {
		?>
		<div class="form-field extra-sidebar-control-field sidebar-location">
			<label>
				<?php echo $this->fields['_extra_sidebar_location']['title']; ?>
				<br />
				<?php
				$attributes = '';
				foreach ( $this->fields['_extra_sidebar_location']['attributes'] as $attribute_key => $attribute_value ) {
					$attributes .= ' ' . esc_attr( $attribute_key ) . '="' . esc_attr( $attribute_value ) . '"';
				}
				?>
				<select class="extra-admin-input" name="_extra_sidebar_location" <?php echo $attributes; ?>>
					<?php $location = $this->fields['_extra_sidebar_location']['value']; ?>
					<?php foreach ( $this->fields['_extra_sidebar_location']['options'] as $option_value => $option_title ) { ?>
						<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $option_value, $location ); ?> > <?php echo esc_html( $option_title ); ?></option>
					<?php } ?>
				</select>
			</label>
		</div>
		<br />
		<div class="form-field extra-sidebar-control-field sidebar-area">
			<label>
				<?php echo $this->fields['_extra_sidebar']['title']; ?>
				<br />
				<select class="extra-admin-input" name="_extra_sidebar">
					<?php $sidebar = $this->fields['_extra_sidebar']['value']; ?>
					<?php foreach ( $this->fields['_extra_sidebar']['options'] as $option_value => $option_title ) { ?>
						<option value="<?php echo esc_attr( $option_value ); ?>" <?php selected( $option_value, $sidebar ); ?> > <?php echo esc_html( $option_title ); ?></option>
					<?php } ?>
				</select>
			</label>
		</div>
		<br />
		<?php

		if ( 'post' == $post->post_type ) {
		?>
		<div class="form-field">
			<label>
				<input type="checkbox" name="_extra_featured_post" value="1" <?php checked( $this->fields['_extra_featured_post']['value'], '1' ); ?> />
				<?php echo esc_html( $this->fields['_extra_featured_post']['title'] ); ?>
			</label>
		</div>
		<br />
		<?php if ( 'on' == et_get_option( 'extra_show_author_box', 'on' ) ) { ?>
			<div class="form-field">
				<label>
					<input type="checkbox" name="_extra_hide_author_box" value="1" <?php checked( $this->fields['_extra_hide_author_box']['value'], '1' ); ?> />
					<?php echo esc_html( $this->fields['_extra_hide_author_box']['title'] ); ?>
				</label>
			</div>
		<?php } else { ?>
			<div class="form-field">
				<label>
					<input type="checkbox" name="_extra_show_author_box" value="1" <?php checked( $this->fields['_extra_show_author_box']['value'], '1' ); ?> />
					<?php echo esc_html( $this->fields['_extra_show_author_box']['title'] ); ?>
				</label>
			</div>
		<?php } ?>
		<br />
		<?php if ( 'on' == et_get_option( 'extra_show_related_posts', 'on' ) ) { ?>
			<div class="form-field">
				<label>
					<input type="checkbox" name="_extra_hide_related_posts" value="1" <?php checked( $this->fields['_extra_hide_related_posts']['value'], '1' ); ?> />
					<?php echo esc_html( $this->fields['_extra_hide_related_posts']['title'] ); ?>
				</label>
			</div>
		<?php } else { ?>
			<div class="form-field">
				<label>
					<input type="checkbox" name="_extra_show_related_posts" value="1" <?php checked( $this->fields['_extra_show_related_posts']['value'], '1' ); ?> />
					<?php echo esc_html( $this->fields['_extra_show_related_posts']['title'] ); ?>
				</label>
			</div>
		<?php } ?>
		<br />
		<?php }

		if ( extra_check_feature_availability_in_post_type( $post->post_type, 'hide_title_meta_in_single' ) ) {
			$hide_title_meta_single = $this->fields['_post_extra_title_meta_hide_single'];
		?>
		<div class="form-field">
			<label for="<?php echo esc_attr( $hide_title_meta_single['id'] ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $hide_title_meta_single['name'] ); ?>" id="<?php echo esc_attr( $hide_title_meta_single['id'] ); ?>" value="1" <?php echo checked( $hide_title_meta_single['value'], '1', false ); ?> />
				<?php echo esc_html( $hide_title_meta_single['title'] ); ?>
			</label>
		</div>
		<br>
		<?php
		}

		if ( extra_check_feature_availability_in_post_type( $post->post_type, 'hide_featured_image_in_single' ) ) {
			$hide_featured_image_single = $this->fields['_post_extra_featured_image_hide_single'];
		?>
		<div class="form-field">
			<label for="<?php echo esc_attr( $hide_featured_image_single['id'] ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $hide_featured_image_single['name'] ); ?>" id="<?php echo esc_attr( $hide_featured_image_single['id'] ); ?>" value="1" <?php echo checked( $hide_featured_image_single['value'], '1', false ); ?> />
				<?php echo esc_html( $hide_featured_image_single['title'] ); ?>
			</label>
		</div>
		<br>
		<?php
		}

		if ( et_get_post_meta_setting( 'all', 'rating_stars' ) && in_array( $post->post_type, extra_get_rating_post_types() ) ) {
			$force_display_rating = $this->fields['_post_extra_rating_hide'];
		?>
		<div class="form-field">
			<label for="<?php echo esc_attr( $force_display_rating['id'] ); ?>">
				<input type="checkbox" name="<?php echo esc_attr( $force_display_rating['name'] ); ?>" id="<?php echo esc_attr( $force_display_rating['id'] ); ?>" value="1" <?php echo checked( $force_display_rating['value'], '1', false ); ?> />
				<?php echo esc_html( $force_display_rating['title'] ); ?>
			</label>
		</div>
		<?php
		}
	}

}
new Extra_Page_Post_Settings_Meta_Box;

function extra_init_walker_nav_menu_edit() {
	class Extra_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit {

		function end_el( &$output, $item, $depth = 0, $args = array() ) {
			ob_start();

			$item_id = esc_attr( $item->ID );
			if ( $item->menu_item_parent == 0 ) {
			?>
			<p id="field-mega-menu-<?php echo esc_attr( $item_id ); ?>" class="field-mega-menu description description-wide unmoved" style="display:none;" >
				<label for="edit-menu-item-mega-menu-<?php echo $item_id; ?>">
					<?php esc_html_e( 'Extra Mega Menu', 'extra' ); ?><br />
					<select name="menu-item-mega-menu[<?php echo $item_id; ?>]" id="edit-menu-item-mega-menu-<?php echo $item_id; ?>">
						<option value=""><?php esc_html_e( 'No Mega Menu', 'extra' ); ?></option>
						<option value="mega-list" <?php selected( $item->mega_menu, 'mega-list', true ); ?> ><?php esc_html_e( 'Mega Menu List', 'extra' ); ?></option>
						<?php if ( $item->type == 'taxonomy' && $item->object == 'category' ) { ?>
						<option value="mega-featured-3" <?php selected( $item->mega_menu, 'mega-featured-3', true ); ?> ><?php esc_html_e( 'Mega Menu 3 Featured', 'extra' ); ?></option>
						<option value="mega-featured-2" <?php selected( $item->mega_menu, 'mega-featured-2', true ); ?> ><?php esc_html_e( 'Mega Menu 2 Featured and Recent List', 'extra' ); ?></option>
						<?php } ?>
					</select>
				</label>
			</p>
			<?php
			}
			$output .= ob_get_clean();
			$output .= "</li>\n";
		}

	}
}

function extra_admin_scripts( $hook ) {
	global $typenow;

	$template_dir = get_template_directory_uri();
	$theme_version = SCRIPT_DEBUG ? time() : et_get_theme_version();
	$protocol = is_ssl() ? 'https' : 'http';

	if ( 'nav-menus.php' == $hook ) {
		wp_enqueue_script( 'extra_admin_nav_menu_js', $template_dir . '/includes/admin/scripts/nav-menu.js', array( 'jquery' ), $theme_version, true );
	}

	if ( 'widgets.php' == $hook ) {
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_script( 'extra_widgets_js', $template_dir . '/includes/admin/scripts/widgets.js', array( 'jquery' ), $theme_version, true );
		wp_enqueue_style( 'extra_widgets_css', $template_dir . '/includes/admin/styles/widgets.css', array(), $theme_version );
		wp_localize_script( 'extra_widgets_js', 'EXTRA', array(
			'label_img_url'   => esc_html__( 'Image Url:', 'extra' ),
			'label_img_alt_text' => esc_html__( 'Image Alt Text:', 'extra' ),
			'label_link_url'  => esc_html__( 'Link Url:', 'extra' ),
			'label_ad_html'   => esc_html__( 'Ad HTML:', 'extra' ),
			'label_new_line'  => esc_html__( 'Start on New Line', 'extra' ),
			'label_or'        => esc_html__( 'OR', 'extra' ),
			'label_ad_number' => esc_html__( 'Ad #', 'extra' ),
			'authorize_nonce' => wp_create_nonce( 'authorize_nonce' ),
			'ajaxurl'         => admin_url( 'admin-ajax.php', $protocol ),
		) );
	}

	if ( 'options-reading.php' == $hook ) {

		$layouts_query = extra_get_layouts(array(
			'posts_per_page' => -1,
			'nopaging'       => true,
			'post_status'    => 'publish',
		));

		if ( $layouts_query->posts ) {
			$layouts = array();

			$home_layout_id = extra_get_home_layout_id();

			foreach ( $layouts_query->posts as $post ) {
				$layouts[] = array(
					"id"   => $post->ID,
					"name" => $post->post_title,
				);
			}

			wp_enqueue_script( 'extra_admin_options_reading_js', $template_dir . '/includes/admin/scripts/options-reading.js', array( 'jquery' ), $theme_version, true );
			wp_localize_script( 'extra_admin_options_reading_js', 'EXTRA', array(
				'layouts'                 => json_encode( $layouts ),
				'current_home_layout_id'  => $home_layout_id,
				'show_on_front'           => get_option( 'show_on_front' ),
				'extra_theme_layout_link' => sprintf( et_get_safe_localization(  __( 'An <a href="%s">Extra Theme Layout</a> (select below)', 'extra' ) ), 'edit.php?post_type=' . EXTRA_LAYOUT_POST_TYPE ),
			) );
		}

		wp_reset_postdata();
	}

	if ( in_array( $hook, array( 'post.php', 'post-new.php' ) ) ) {
		// load every post and post edit page, i.e. posts, pages, CPTs

		wp_enqueue_script( 'extra_admin_gallery_images_js', $template_dir . '/includes/admin/scripts/gallery-images.js', array( 'jquery', 'jquery-ui-sortable' ), $theme_version, true );

		if ( 'page' == $typenow ) {
			wp_enqueue_script( 'extra_admin_page_template_js', $template_dir . '/includes/admin/scripts/page-template.js', array( 'jquery', 'jquery-effects-highlight' ), $theme_version, true );
		}

		if ( 'post' == $typenow || EXTRA_PROJECT_POST_TYPE == $typenow ) {
			wp_enqueue_script( 'extra_admin_posts_js', $template_dir . '/includes/admin/scripts/posts.js', array( 'jquery' ), $theme_version, true );
			wp_localize_script( 'extra_admin_posts_js', 'EXTRA', array(
				'label_breakdown_title'  => esc_html__( 'Title:', 'extra' ),
				'label_breakdown_rating' => esc_html__( 'Rating (%):', 'extra' ),
				'label_breakdown_number' => esc_html__( 'Breakdown #', 'extra' ),
			) );
		}

		if ( 'post' == $typenow ) {
			wp_enqueue_script( 'extra_admin_post_format_js', $template_dir . '/includes/admin/scripts/post-format.js', array( 'jquery', 'jquery-effects-highlight' ), $theme_version, true );
		}

		if ( EXTRA_LAYOUT_POST_TYPE == $typenow ) {
			wp_enqueue_script( 'extra_admin_post_layout_js', $template_dir . '/includes/admin/scripts/post-layout.js', array( 'jquery' ), $theme_version, true );
			wp_localize_script( 'extra_admin_post_layout_js', 'EXTRA', array(
				'category_description' => esc_html__( 'Use this layout on the categories selected above.', 'extra' ),
			) );
		}

		// load *every* wp-admin post.php and post-new.php page
		wp_enqueue_script( 'extra_admin_js', $template_dir . '/includes/admin/scripts/admin-posts.js', array( 'jquery' ), $theme_version, true );
	}
}

add_action( 'admin_enqueue_scripts', 'extra_admin_scripts', 10, 1 );

function extra_reading_options_show_on_front_layout( $value ) {
	if ( empty( $_POST['option_page'] ) || 'reading' != $_POST['option_page'] ) {
		return $value;
	}

	check_admin_referer( 'reading-options' );

	$args = array(
		'meta_key'       => EXTRA_HOME_LAYOUT_META_KEY,
		'meta_value'     => 1,
		'posts_per_page' => 1,
	);

	$layouts = extra_get_layouts( $args );
	wp_reset_postdata();

	if ( !empty( $layouts->post ) ) {
		delete_post_meta( $layouts->post->ID, EXTRA_HOME_LAYOUT_META_KEY );
	}

	if ( $value == 'layout' && !empty( $_POST['home_layout'] ) ) {
		update_post_meta( absint( $_POST['home_layout'] ), EXTRA_HOME_LAYOUT_META_KEY, 1 );
	}

	return $value;
}

add_filter( 'pre_update_option_show_on_front', 'extra_reading_options_show_on_front_layout' );

function extra_wp_edit_nav_menu_walker( $menu_id ) {
	extra_init_walker_nav_menu_edit();
	return 'Extra_Walker_Nav_Menu_Edit';
}

add_filter( 'wp_edit_nav_menu_walker', 'extra_wp_edit_nav_menu_walker' );

function extra_update_nav_menu( $menu_id ) {
	$args = func_get_args();
	// Need to count args because this same hook
	// name is called twice in the same $_POST via:
	// wp_nav_menu_update_menu_items() and wp_update_nav_menu_object()
	// When called by .._object, there are 2 args sent to the hook
	if ( count( $args ) > 1 ) {
		if ( !empty( $_POST['menu-item-mega-menu'] ) ) {
			foreach ( $_POST['menu-item-mega-menu'] as $menu_item_id => $mega_menu_setting ) {
				if ( !empty( $mega_menu_setting ) ) {
					update_post_meta( $menu_item_id, '_menu_item_mega_menu', sanitize_key( $mega_menu_setting ) );
				} else {
					delete_post_meta( $menu_item_id, '_menu_item_mega_menu' );
				}
			}
		}
	}
}

add_action( 'wp_update_nav_menu', 'extra_update_nav_menu', 10, 2 );

function extra_add_user_profile_color_option( $profileuser ) {
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'wp-color-picker' );

?>
<table class="form-table" id="user_color">
	<tbody>
		<tr>
			<th>
				<label><?php esc_html_e( 'Color', 'extra' ); ?></label>
			</th>
			<td>
				<?php $color = isset( $profileuser->user_color ) ? $profileuser->user_color : ''; ?>
				<input class="color-picker-hex" name="user_color" type="text" maxlength="7" placeholder="<?php esc_attr_e( 'Hex Value', 'extra' ); ?>" data-default-color="<?php echo esc_attr( $color ); ?>" value="<?php echo esc_attr( $color ); ?>" /><br />
				<span class="description"><?php esc_html_e( 'The color used for this user throughout the site.', 'extra' ); ?></span>
			</td>
		</tr>
	</tbody>
</table>
<script type="text/javascript">
(function($){
	$(document).ready( function(){
		$('#user_color').insertBefore( $('#description').closest('.form-table') );
		$('.color-picker-hex').wpColorPicker();
	});
})(jQuery)
</script>
<?php
}

add_action( 'show_user_profile', 'extra_add_user_profile_color_option' );
add_action( 'edit_user_profile', 'extra_add_user_profile_color_option' );

function extra_save_user_profile_color_option( $user_id ) {
	if ( !empty( $_POST['user_color'] ) ) {
		update_user_meta( $user_id, 'user_color', sanitize_text_field( $_POST['user_color'] ) );
	} else {
		delete_user_meta( $user_id, 'user_color' );
	}
}

add_action( 'personal_options_update', 'extra_save_user_profile_color_option' );
add_action( 'edit_user_profile_update', 'extra_save_user_profile_color_option' );

function extra_set_post_default_meta( $post_id, $post ) {
	if ( 'post' == $post->post_type ) {
		$default_meta = array(
			'_extra_rating_average' => 0,
		);

		foreach ($default_meta as $meta_key => $default_value) {
			$already_set = get_post_meta( $post_id, $meta_key, true );
			if ( empty( $already_set ) ) {
				update_post_meta( $post_id, $meta_key, $default_value );
			}
		}
	}
}

add_action( 'save_post', 'extra_set_post_default_meta', 10, 2 );
