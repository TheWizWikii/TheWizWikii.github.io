<?php

require_once ET_BUILDER_DIR . 'module/field/template/Base.php';

class ET_Builder_Module_Field_Template_Border_Radius extends ET_Builder_Module_Field_Template_Base {
	public function render( $field, $render_helper ) {
		ob_start();
		?>
		<div class="et-pb-border-radius-wrap">
			<div class="et-pb-border-radius-wrap-column">
				<div class="et-pb-border-radius-top-left">
					<input class="et-pb-border-radius-option-input"
							type="text"
							value=""
							data-corner="top-left"
					/>
				</div>
				<div class="et-pb-border-radius-bottom-left">
					<input class="et-pb-border-radius-option-input"
							type="text"
							value=""
							data-corner="bottom-left"
					/>
				</div>
			</div>
			<div class="et-pb-border-radius-wrap-column">
				<div class="et-pb-border-radius-preview">
					<div class="et-pb-border-radius-wrap-link-button">
						<a href="#" class="">
							<?php echo et_core_esc_previously( $this->_render_icon( 'border-link' ) ); ?>
						</a>
					</div>
				</div>
			</div>
			<div class="et-pb-border-radius-wrap-column">
				<div class="et-pb-border-radius-top-right">
					<input class="et-pb-border-radius-option-input"
							type="text"
							value=""
							data-corner="top-right"
					/>
				</div>
				<div class="et-pb-border-radius-bottom-right">
					<input class="et-pb-border-radius-option-input"
							type="text"
							value=""
							data-corner="bottom-right"
					/>
				</div>
			</div>
		</div>
		<span class="et-pb-reset-setting"></span>
		<?php
			$name = $render_helper->get_field_name( $field );

			// Avoid underscore's _.template() being treated as PHP tags when PHP server's asp_tags is enabled
			$value_open_tag  = '<%-';
			$value_close_tag = '%>';
		?>
		<?php $attr_name = $render_helper->get_type() === 'child' ? 'data.' . esc_attr( $name ) : esc_attr( $name ); ?>
		<?php $default = isset( $field['default'] ) ? $field['default'] : 'on||||'; ?>
		<input id="<?php echo esc_attr( $name ); ?>"
			type="hidden"
			name="<?php echo esc_attr( $name ); ?>"
			data-default="<?php echo esc_attr( $default ); ?>"
			value="<?php echo et_core_intentionally_unescaped( $value_open_tag, 'fixed_string' ); ?> typeof( <?php echo esc_attr( $attr_name ); ?> ) !== 'undefined' ?  <?php echo esc_attr( $attr_name ) . '.replace(/%91/g, "[").replace(/%93/g, "]").replace(/%22/g, "\"")'; ?> : '' <?php echo et_core_intentionally_unescaped( $value_close_tag, 'fixed_string' ); ?>"
			class="et-pb-main-setting"
		/>
		<?php
		return ob_get_clean();
	}

}
