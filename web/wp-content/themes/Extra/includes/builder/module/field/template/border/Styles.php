<?php

require_once ET_BUILDER_DIR . 'module/field/template/Tabbed.php';

class ET_Builder_Module_Field_Template_Border_Styles extends ET_Builder_Module_Field_Template_Tabbed {
	protected function _get_control_class() {
		return 'et-pb-composite-tabbed-border-style';
	}

	protected function _render_tab_preview( $tab ) {
		ob_start();
		?>
		<div class="et-pb-tab-preview-container-column">
			<div class="et-pb-tab-preview-container-preview"></div>
		</div>
		<?php

		return ob_get_clean();
	}
}
