<?php

/**
 * Class ET_Builder_Module_Field_Template_Base
 * Base class for field renderers in BB
 */
abstract class ET_Builder_Module_Field_Template_Base {

	/**
	 * @param $field
	 * @param $render_helper
	 *
	 * @return template
	 */
	abstract public function render( $field, $render_helper );

	protected function _render_icon( $icon_name ) {
		return '<div class="et-pb-icon">
					<svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><%= et_builder_template_options.options_icons[ "' . esc_attr( $icon_name ) . '" ] %></svg>
				</div>';
	}

	protected function _wrap_field_name( $name ) {
		// Don't add 'et_pb_' prefix to the "Admin Label" field
		if ( 'admin_label' === $name ) {
			return $name;
		}

		return 'et_pb_' . $name;
	}
}
