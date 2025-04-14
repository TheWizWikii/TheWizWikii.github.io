<?php
/**
 * ET_Builder_Module_Settings_Migration_ContactFormUniqueID class file.
 *
 * @class   ET_Builder_Module_Settings_Migration_ContactFormUniqueID
 * @package Builder/Module/Settings/Migration
 */

/**
 * Migration process to fill in missing unique_id attribute on Contact Form module.
 *
 * @since 4.13.1
 */
class ET_Builder_Module_Settings_Migration_ContactFormUniqueID extends ET_Builder_Module_Settings_Migration {

	/**
	 * Migration Version
	 *
	 * @since 4.13.1
	 *
	 * @var string
	 */
	public $version = '4.13.1';

	/**
	 * Get the field that need to be migrated.
	 *
	 * Contains array with:
	 * - key as new field
	 * - value consists affected fields as old field and module location
	 *
	 * @since 4.13.1
	 *
	 * @return array New and old fields need to be migrated.
	 */
	public function get_fields() {
		return array(
			// Unique ID of Contact Form module.
			'_unique_id' => array(
				'affected_fields' => array(
					'_unique_id' => $this->get_modules(),
				),
			),
		);
	}

	/**
	 * Get all modules affected.
	 *
	 * @since 4.13.1
	 *
	 * @return array
	 */
	public function get_modules() {
		return array( 'et_pb_contact_form' );
	}

	/**
	 * Run migrate process.
	 *
	 * @since 4.13.1
	 *
	 * @param string $field_name       Field name.
	 * @param string $current_value    Current value.
	 * @param string $module_slug      Module slug.
	 * @param string $saved_value      Saved value.
	 * @param string $saved_field_name Saved field name.
	 * @param string $attrs            Module attributes values.
	 * @param string $content          Module content.
	 * @param string $module_address   Module address.
	 *
	 * @return string New value.
	 */
	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		// Setup unique ID for Contact Form module. Only do this when the current page is
		// builder and not on the FE because it mays create incorrect unique ID.
		if ( '_unique_id' === $field_name && function_exists( 'et_fb_is_enabled' ) && et_fb_is_enabled() ) {
			return ET_Core_Data_Utils::uuid_v4();
		}

		return $current_value;
	}
}

return new ET_Builder_Module_Settings_Migration_ContactFormUniqueID();
