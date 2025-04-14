<?php

class ET_Builder_Module_Settings_Migration_ShopOrderByDefault extends ET_Builder_Module_Settings_Migration {

	public $version = '3.25.3';

	public function get_modules() {
		return array( 'et_pb_shop' );
	}

	public function get_fields() {
		$fields = array(
			'orderby' => array(
				'affected_fields' => array(
					'orderby' => $this->get_modules(),
				),
			),
		);

		return $fields;
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
		return '' === $current_value ? 'menu_order' : $saved_value;
	}
}

return new ET_Builder_Module_Settings_Migration_ShopOrderByDefault();
