<?php
/**
 * Migrate Social Media Follow Network Twitter to X
 *
 * @package Divi/Builder/Migration
 */

/**
 * Divi ET_Builder_Migration_SocialMediaFollowNetworkTwitterToX class
 */
class ET_Builder_Migration_SocialMediaFollowNetworkTwitterToX extends ET_Builder_Module_Settings_Migration {

	/**
	 * From which version we are migrating
	 *
	 * @var string
	 */
	public $version = '4.22.2';

	/**
	 * Get modules to migrate
	 *
	 * @return string[]
	 */
	public function get_modules() {
		return array( 'et_pb_social_media_follow_network' );
	}

	/**
	 * Get module fields to migrate
	 *
	 * @return array[]
	 */
	public function get_fields() {
		// Return fields to migrate social icon background color.
		return array(
			'background_color' => array(
				'affected_fields' => array(
					'background_color' => $this->get_modules(),
				),
			),
		);
	}


	/**
	 * Modify the saved value for the field
	 *
	 * @param string $field_name Field Name.
	 * @param string $current_value Current Value.
	 * @param string $module_slug Module Slug.
	 * @param string $saved_value Saved Value.
	 * @param string $saved_field_name Saved Field name.
	 * @param array  $attrs Module Attributes.
	 * @param string $content Content.
	 * @param string $module_address Module Address.
	 * @return string
	 */
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
		if ( isset( $attrs['social_network'] ) && 'twitter' === $attrs['social_network'] && 'background_color' === $saved_field_name && '#00aced' === $saved_value ) {
			return '#000000';
		}

		return $saved_value;
	}

}

return new ET_Builder_Migration_SocialMediaFollowNetworkTwitterToX();
