<?php
/**
 * Migration process to migrate Text Text Alignment into Text Orientation of Text module.
 *
 * @since 3.27.4
 */
class ET_Builder_Module_Settings_Migration_TextAlignment extends ET_Builder_Module_Settings_Migration {

	/**
	 * Migration Version
	 *
	 * @since 3.27.4
	 *
	 * @var string
	 */
	public $version = '3.27.4';

	/**
	 * Get all fields need to be migrated.
	 *
	 * Contains array with:
	 * - key as new field
	 * - value consists affected fields as old field and module location
	 *
	 * @since 3.27.4
	 *
	 * @return array New and old fields need to be migrated.
	 */
	public function get_fields() {
		return array(
			// Text Alignment of Text module.
			'text_orientation' => array(
				'affected_fields' => array(
					'text_text_align' => $this->get_modules(),
				),
			),
		);
	}

	/**
	 * Get all modules affected.
	 *
	 * @since 3.27.4
	 *
	 * @return array
	 */
	public function get_modules() {
		return array( 'et_pb_text' );
	}

	public function migrate(
		$field_name,
		$current_value,
		$module_slug,
		$saved_value,
		$saved_field_name,
		$attrs,
		$content,
		$module_address
	) {
		// Don't migrate empty value.
		return ! empty( $current_value ) ? $current_value : $saved_value;
	}
}

return new ET_Builder_Module_Settings_Migration_TextAlignment();
