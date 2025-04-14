<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class ET_Meta_Box {
	/**
	 * Metabox ID.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * Metabox title.
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Metabox context.
	 *
	 * @var string
	 */
	private $context;

	/**
	 * Metabox priority.
	 *
	 * @var string
	 */
	private $priority;

	/**
	 * Post types.
	 *
	 * @var array
	 */
	private $post_types;

	/**
	 * Post data.
	 *
	 * @var array
	 */
	public $post_data = array();

	/**
	 * Metabox fields.
	 *
	 * @var array
	 */
	public $fields = array();

	/**
	 * Metabox post ID.
	 *
	 * @var array
	 */
	private $post_id;

	/**
	 * Metabox meta.
	 *
	 * @var array
	 */
	private $meta = null;

	/**
	 * Nonce.
	 *
	 * @var string
	 */
	private $nonce = 'et_metabox_settings_nonce';

	function __construct( $id, $title, $args = array() ) {
		$this->id = $id;
		$this->title = $title;

		$args = wp_parse_args( $args, array(
			'post_type' => 'post',
			'context'   => 'advanced',
			'priority'  => 'default',
		) );

		if ( is_string( $args['post_type'] ) ) {
			$args['post_type'] = array( $args['post_type'] );
		}

		$this->post_types = $args['post_type'];

		$this->context = $args['context'];
		$this->priority = $args['priority'];

		add_action( 'load-post.php', array( $this, 'pre_register' ) );
		add_action( 'load-post-new.php', array( $this, 'pre_register' ) );
	}

	function pre_register() {
		if ( ! in_array( get_current_screen()->post_type, $this->post_types ) ) {
			return;
		}

		if ( isset( $_GET['post'] ) ) {
			$this->post_id = intval( $_GET['post'] );
			$this->post_data = $this->get_meta();
		}

		add_action( 'add_meta_boxes', array( $this, 'register' ) );
		add_action( 'save_post', array( $this, '_save_post' ), 10, 2 );

		$this->pre_register_after();
	}

	function pre_register_after() { }

	function register() {
		add_meta_box( $this->id, $this->title, array( $this, '_display' ), null, $this->context, $this->priority );
	}

	function before_display() {
		wp_nonce_field( basename( __FILE__ ), $this->nonce );
		foreach ( $this->fields as $key => $field ) {
			$value = '';

			if ( isset( $this->meta[$key] ) ) {
				$value = $this->meta[$key];
			}

			if ( '' === $value && isset( $field['default'] ) ) {
				$value = $field['default'];
			}

			if ( empty( $field['name'] ) ) {
				$this->fields[$key]['name'] = $key;
			}

			if ( empty( $field['id'] ) ) {
				$this->fields[$key]['id'] = $this->fields[$key]['name'];
			}

			$this->fields[$key]['value'] = $value;
		}
	}

	function fields() { }

	function _display( $post ) {
		$this->fields();
		$this->before_display();
		$this->fetch_meta();
		$this->display( $post );
	}

	function render_field( $field ) {
		if ( !empty( $field['renderer'] ) ) {
			return call_user_func( $field['renderer'], $field );
		}

		$field['id'] = !empty( $field['id'] ) ? $field['id'] : $field['name'];

		$classes = array('regular-text');
		if ( !empty( $field['class'] ) ) {
			if ( is_string( $field['class'] ) ) {
				$field['class'] = array( $field['class'] );
			}

			$classes = array_merge( $classes, $field['class'] );
		}
		$field['class'] = implode( ' ', $classes );

		$attributes = '';
		if ( !empty( $field['attributes'] ) ) {
			if ( is_array( $field['attributes'] )  ) {
				foreach ( $field['attributes'] as $attribute_key => $attribute_value ) {
					$attributes .= ' ' . esc_attr( $attribute_key ) . '="' . esc_attr( $attribute_value ) . '"';
				}
			} else {
				$attributes = ' '. $field['attributes'];
			}
		}

		// TODO IMPLEMENT FORM VALIDATION LIKE PB MODULES
		// LIKE THIS:
		// foreach ( $this->get_validation_attr_rules() as $rule ) {
		// 	if ( !empty( $field[ $rule ] ) ) {
		// 		$this->validation_in_use = true;
		// 		$attributes .= ' data-rule-' . esc_attr( $rule ). '="' . esc_attr( $field[ $rule ] ) . '"';
		// 	}
		// }

		$default = isset( $field['default'] ) ? $field['default'] : '';

		$field['value'] = isset( $field['value'] ) ? $field['value'] : $default;

		$field['type'] = !empty( $field['type'] ) ? $field['type'] : 'input';

		switch( $field['type'] ) {
			case 'select':
				$field['el'] = $this->render_select( $field['name'], $field['options'], $field['value'], $field['id'], $field['class'], $attributes );
				break;
			case 'upload':
				$upload_button_text = !empty( $field['upload_button_text'] ) ? $field['upload_button_text'] : '';
				$field['el'] = $this->render_upload( $field['name'], $field['value'], $field['id'], $field['class'], $upload_button_text );
				break;
			case 'checkbox':
				$field['el'] = '<input type="checkbox" name="' . $field['name'] . '" id="' . $field['id'] . '" value="1" class="' . $field['class'] . '" ' . checked( $field['value'], '1', false ) . ' />';
				break;
			case 'text':
			case 'input':
			default:
				$field['el'] = '<input type="text" name="' . $field['name'] . '" id="' . $field['id'] .'" class="' . $field['class'] . '" value="' . $field['value'] . '" />';
				break;

		}

		return $this->field_wrap( $field );
	}

	function field_wrap( $field ) {
		return sprintf(
			'<div class="form-field">
				<p><strong><label for="%s">%s</label></strong></p>
				%s
				%s
			</div><br />',
			esc_attr( $field['id'] ),
			esc_html( $field['title'] ),
			$field['el'],
			( !empty( $field['description'] ) ? '<p class="description">' . $field['description'] . '</p>' : '')
		);
	}

	function render_upload( $name, $value = '', $id = '', $class = '', $upload_button_text = '' ) {
		$output = sprintf('
			<input %sclass="uploadfield%s" type="text" size="90" name="%s" value="%s" />
			<div class="upload_buttons">
				<input class="upload_image_reset" type="button" value="%s" />
				<input class="upload_image_button" type="button" value="%s" />
			</div>',
			( !empty( $id ) ? sprintf( 'id="%s" ', esc_attr( $id ) ) : ''),
			( !empty( $class ) ? sprintf( ' %s', esc_attr( $class ) ) : ''),
			esc_attr( $name ),
			esc_attr( $value ),
			esc_html__( 'Reset', $this->themename ),
			( !empty( $upload_button_text ) ? esc_attr( $upload_button_text ) : esc_attr__( 'Upload', $this->themename ) )
		);

		return $output;
	}

	function render_select( $name, $options, $value = '', $id = '', $class = '', $attributes = '' ) {
		$options_output = '';
		foreach ( $options as $option_value => $option_label ) {
			$selected_attr = $value == $option_value ? ' selected="selected"' : '';
			$options_output .= sprintf(
				'<option value="%s"%s>%s</option>',
				esc_attr( $option_value ),
				$selected_attr,
				esc_html( $option_label )
			);
		}

		$output = sprintf(
			'<select name="%s"%s%s%s>%s</select>',
			esc_attr( $name ),
			( !empty( $id ) ? sprintf( ' id="%s"', esc_attr( $id ) ) : '' ),
			( !empty( $class ) ? sprintf( ' class="%s"', esc_attr( $class ) ) : '' ),
			( !empty( $attributes ) ? $attributes : '' ),
			$options_output
		);
		return $output;
	}

	function display( $post ) { }

	function _save_post( $post_id, $post ) {
		global $pagenow;

		if ( 'post.php' != $pagenow ) return $post_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$post_type = get_post_type_object( $post->post_type );
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return $post_id;
		}

		if ( ! in_array( $post->post_type, $this->post_types ) ) {
			return;
		}

		if ( ! isset( $_POST[$this->nonce] ) || ! wp_verify_nonce( $_POST[$this->nonce], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		$this->fields();
		$this->save( $post_id, $post );
	}

	function save_fields( $post_id ) {
		foreach ( $this->fields as $field_key => $field ) {

			$value_sanitize_function = !empty( $field['value_sanitize_function'] ) ? $field['value_sanitize_function'] : 'sanitize_text_field';

			if ( isset( $_POST[ $field_key ] ) ) {
				if ( !empty( $field['value_type'] ) && 'array' == $field['value_type'] ) {
					$value = implode( ',', array_map( $value_sanitize_function, $_POST[ $field_key ] ) );
				} else if ( !empty( $field['value_type'] ) && 'checkbox' == $field['value_type'] ) {
					$value = 1;
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

	function delete_fields( $post_id ) {
		foreach ( $this->fields as $field_key => $field ) {
			delete_post_meta( $post_id, $field_key );
		}
	}

	function save( $post_id, $post ) {
		$this->save_fields( $post_id );
	}

	private function fetch_meta() {
		if ( !$this->post_id ) {
			return;
		}

		$meta = get_post_custom( $this->post_id );
		foreach ( $meta as $key => $values ) {
			$meta[ $key ] = maybe_unserialize( $meta[ $key ][0] );
		}

		$this->meta = $meta;
	}

	function get_fresh_meta() {
		$this->fetch_meta();

		return $this->meta();
	}

	function get_meta() {
		if ( is_null( $this->meta ) ) {
			$this->fetch_meta();
		}

		return $this->meta;
	}

}
