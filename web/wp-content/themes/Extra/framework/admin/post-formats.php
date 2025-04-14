<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Et_Post_Format_Meta_Box extends ET_Meta_Box {

	public $shortname;

	public function __construct() {
		global $shortname;

		$this->shortname = $shortname;

		parent::__construct( 'et-post-format', esc_html__( 'Format', $this->shortname ), array(
			'post_type' => 'post',
			'context'   => 'side',
			'priority'  => 'default',
		) );
	}

	function fields() {
		$this->fields = array(
			'et_post_format' => array(),
		);
	}

	function display( $post ) {
		$post_format = et_get_post_format( $post->ID );

		if ( !$post_format ) {
			$post_format = get_option( 'et_default_post_format' );
		}

		if ( !$post_format || 'standard' === $post_format ) {
			$post_format = '0';
		}
		?>
		<div id="post-formats-select">
		<input type="radio" name="et_post_format" class="post-format" id="et-post-format-0" value="0" <?php checked( $post_format, '0' ); ?> /> <label for="et-post-format-0" class="post-format-icon post-format-standard"><?php echo esc_html( et_get_post_format_string( 'standard' ) ); ?></label>
		<?php foreach ( et_get_theme_post_format_slugs() as $format ) : ?>
		<br /><input type="radio" name="et_post_format" class="post-format" id="et-post-format-<?php echo esc_attr( $format ); ?>" value="<?php echo esc_attr( $format ); ?>" <?php checked( $post_format, $format ); ?> /> <label for="et-post-format-<?php echo esc_attr( $format ); ?>" class="post-format-icon post-format-<?php echo esc_attr( $format ); ?>"><?php echo esc_html( et_get_post_format_string( $format ) ); ?></label>
		<?php endforeach; ?><br />
	</div>
	<?php
	}

	public function save( $post_id, $post ) {
		if ( isset( $_POST['et_post_format'] ) ) {
			$post_format = sanitize_text_field( $_POST['et_post_format'] );
			et_set_post_format( $post_id, $post_format );
		}
	}

}
new Et_Post_Format_Meta_Box;
