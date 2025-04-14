<?php

class ET_Builder_Module_Settings_Migration_ShopModuleSlugs extends ET_Builder_Module_Settings_Migration {

	public function get_modules() {
		$modules = array(
			'et_pb_shop',
		);

		return $modules;
	}

	public function migrate( $field_name, $current_value, $module_slug, $saved_value, $saved_field_name, $attrs, $content, $module_address ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return $current_value;
		}

		switch ( $field_name ) {
			case 'include_categories':
				$old_categories     = explode( ',', $current_value );
				$new_categories     = array();
				$product_categories = et_builder_get_shop_categories();
				if ( is_array( $product_categories ) && ! empty( $product_categories ) ) {
					foreach ( $product_categories as $category ) {
						if ( is_object( $category ) && is_a( $category, 'WP_Term' ) ) {
							if ( in_array( $category->slug, $old_categories ) ) {
								array_push( $new_categories, $category->term_id );
							}
						}
					}
				}

				return implode( ',', $new_categories );
			case '_builder_version':
				return ET_BUILDER_PRODUCT_VERSION;
			default:
				return $current_value;
		}
	}

	public function get_fields() {
		return array(
			'include_categories' => array(
				'affected_fields' => array(
					'include_categories' => $this->get_modules(),
				),
			),
			'_builder_version'   => array(
				'affected_fields' => array(
					'_builder_version' => $this->get_modules(),
				),
			),
		);
	}

}

return new ET_Builder_Module_Settings_Migration_ShopModuleSlugs();
