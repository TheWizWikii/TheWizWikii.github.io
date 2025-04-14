<?php

/**
 * Parser of additional composite type attributes
 */
class ET_Builder_Module_Field_Attribute_Composite_Parser {
	/**
	 * @param string $type type of composite attribute
	 * @param array  $structure attribute structure, depends on type
	 *
	 * @return array Additional attributes for merging with rest of module attributes
	 */
	public static function parse( $type, $structure ) {
		switch ( $type ) {
			case 'tabbed':
			default:
				require_once ET_BUILDER_DIR . 'module/field/attribute/composite/type/Tabbed.php';
				return ET_Builder_Module_Field_Attribute_Composite_Type_Tabbed::parse( $structure );
		}
	}
}
