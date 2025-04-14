<?php

require_once ET_BUILDER_DIR . 'module/field/template/Base.php';

class ET_Builder_Module_Field_Template_Tabbed extends ET_Builder_Module_Field_Template_Base {
	/**
	 * @param $field array Field settings
	 * @param $render_helper ET_Builder_Element
	 *
	 * @return string Control rendered html
	 */
	public function render( $field, $render_helper ) {
		ob_start();
		if ( ! empty( $field['composite_structure'] ) && is_array( $field['composite_structure'] ) ) {
			?>
			<div class="et-pb-composite-tabbed-wrapper">
				<div id="<?php echo esc_attr( $this->_wrap_field_name( $field['name'] ) ); ?>" class="<?php echo esc_attr( $this->_get_control_class() ); ?>" data-attr-suffix="<?php echo esc_attr( $field['attr_suffix'] ); ?>">
					<div class="et-pb-outside-preview-container">
						<?php echo et_core_esc_previously( $this->_render_outside_preview() ); ?>
					</div>
					<ul class="et-pb-settings-tabs">
						<?php foreach ( $field['composite_structure'] as $tab => $structure ) : ?>
							<li class="et-pb-settings-tab">
								<a href="#" class="et-pb-settings-tab-title" data-tab="<?php echo esc_attr( $tab ); ?>">
									<?php
									if ( isset( $structure['label'] ) && ! empty( $structure['label'] ) ) {
										echo esc_html( $structure['label'] );
									}
									// render the icon if there is one defined.
									if ( isset( $structure['icon'] ) && ! empty( $structure['icon'] ) ) {
										echo et_core_esc_previously( $this->_render_icon( esc_html( $structure['icon'] ) ) );
									}
									?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php foreach ( $field['composite_structure'] as $tab => $structure ) : ?>
						<div class="et-pb-settings-tab-content" data-tab="<?php echo esc_attr( $tab ); ?>">
							<div class="et-pb-tab-preview-container" data-tab="<?php echo esc_attr( $tab ); ?>">
								<?php echo et_core_esc_previously( $this->_render_tab_preview( $tab ) ); ?>
							</div>
							<?php if ( ! empty( $structure['controls'] ) && is_array( $structure['controls'] ) ) : ?>
								<?php foreach ( $structure['controls'] as $name => $control ) : ?>
									<?php $control['name'] = $name; ?>
									<?php $control['tab_slug'] = $field['tab_slug']; ?>
									<?php $hidden = 'hidden' === $control['type'] ? ' et_pb_hidden' : ''; ?>
									<div class="et-pb-composite-option et-pb-composite-tabbed-option et-pb-option<?php echo et_core_intentionally_unescaped( $hidden, 'fixed_string' ); ?>" data-control-index="<?php echo esc_attr( $name ); ?>" data-option_name="<?php echo esc_attr( $name ); ?>">
										<?php echo et_core_esc_previously( $render_helper->wrap_settings_option_label( $control ) ); ?>
										<?php echo et_core_esc_previously( $render_helper->wrap_settings_option_field( $control, $name ) ); ?>
									</div>
								<?php endforeach; ?>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<span class="et-pb-reset-setting et-pb-reset-skip et-pb-composite-tabbed-reset-setting"></span>
			<?php
		}

		return ob_get_clean();
	}

	protected function _render_outside_preview() {
		return '';
	}

	protected function _render_tab_preview( $tab ) {
		return '';
	}

	protected function _get_control_class() {
		return 'et-pb-composite-tabbed';
	}
}
