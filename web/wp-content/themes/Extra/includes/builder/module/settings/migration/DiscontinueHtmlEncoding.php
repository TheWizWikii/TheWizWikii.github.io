<?php
/**
 * Migration for the Code Module and Signup Module that had values html encoded.
 *
 * @since 3.15.1
 */
class ET_Builder_Module_Settings_Migration_DiscontinueHtmlEncoding extends ET_Builder_Module_Settings_Migration {
	public function get_modules( $group = '' ) {
		$modules = array();

		if ( in_array( $group, array( '', 'signup' ) ) ) {
			$modules[] = 'et_pb_signup';
		}

		if ( in_array( $group, array( '', 'code' ) ) ) {
			$modules[] = 'et_pb_code';
			$modules[] = 'et_pb_fullwidth_code';
		}

		return $modules;
	}

	public function get_fields() {
		return array(
			'raw_content'    => array(
				'affected_fields' => array(
					'raw_content' => $this->get_modules( 'code' ),
				),
			),
			'description'    => array(
				'affected_fields' => array(
					'description' => $this->get_modules( 'signup' ),
				),
			),
			'footer_content' => array(
				'affected_fields' => array(
					'footer_content' => $this->get_modules( 'signup' ),
				),
			),
		);
	}

	private function decode_code_module_raw_content( $content ) {
		// convert previously escaped/encoded content back to normal
		$content = et_builder_replace_code_content_entities( $content );
		$content = ET_Builder_Element::convert_smart_quotes_and_amp( $content );

		// TODO, not sure about this, but single quotes were encoded so this seemed to be needed
		$content = str_replace( '&#39;', "'", $content );

		$author_id = get_post_field( 'post_author', get_the_ID() ) || get_current_user_id();

		if ( ! user_can( $author_id, 'unfiltered_html' ) ) {
			$content = $this->_post_content_capability_check( $content );
		}

		$content = html_entity_decode( $content, ENT_QUOTES );

		// Only apply this aspect of the migration on FE, its not needed to run in VB or BB,
		// thats the purpose ! is_admin() check is serving in this case.
		if ( ! is_admin() ) {
			// convert <br /> tags into placeholder so wpautop will leave them alone.
			$content = preg_replace( '|<br[\s]?[\/]?>|', '<!–- [et_pb_br_holder] -–>', $content );

			// convert <p> tag to <pee> tag, so wpautop will leave them alone,
			// *and* so that we can clearly spot the <p> tags that wpautop adds
			// so we can quickly remove them.
			$content = preg_replace( '|<p |', '<pee ', $content );
			$content = preg_replace( '|<p>|', '<pee>', $content );
			$content = preg_replace( '|<\/p>|', '</pee>', $content );
		}

		return $content;
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {

		// code module migration for BB
		if ( in_array( $module_slug, $this->get_modules( 'code' ) ) && 'raw_content' === $saved_field_name ) {
			return $this->decode_code_module_raw_content( $content );
		}

		$signup_fields = array( 'description', 'footer_content' );

		if ( in_array( $module_slug, $this->get_modules( 'signup' ) ) && in_array( $saved_field_name, $signup_fields ) ) {
			return html_entity_decode( $saved_value, ENT_COMPAT, 'UTF-8' );
		}

		return $saved_value;
	}

	public function get_content_migration_modules( $group = '' ) {
		$modules = array();

		if ( in_array( $group, array( '', 'code' ) ) ) {
			$modules[] = 'et_pb_code';
			$modules[] = 'et_pb_fullwidth_code';
		}

		return $modules;
	}

	public function migrate_content( $module_slug, $attrs, $content ) {
		if ( in_array( $module_slug, $this->get_content_migration_modules( 'code' ) ) ) {
			return $this->decode_code_module_raw_content( $content );
		}

		return $content;
	}

	private function _post_content_capability_check( $content ) {
		$content = preg_replace_callback( '/\[et_pb_code.*?\](.*)\[\/et_pb_code\]/mis', array( $this, '_sanitize_code_module_content_regex' ), $content );
		$content = preg_replace_callback( '/\[et_pb_fullwidth_code.*?\](.*)\[\/et_pb_fullwidth_code\]/mis', array( $this, '_sanitize_code_module_content_regex' ), $content );

		return $content;
	}

	private function _sanitize_code_module_content_regex( $matches ) {
		$sanitized_content   = wp_kses_post( $matches[1] );
		$sanitized_shortcode = str_replace( $matches[1], $sanitized_content, $matches[0] );

		return $sanitized_shortcode;
	}

}

return new ET_Builder_Module_Settings_Migration_DiscontinueHtmlEncoding();
