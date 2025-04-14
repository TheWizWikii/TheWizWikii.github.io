<?php

class ET_Builder_Module_Field_Overflow extends ET_Builder_Module_Field_Base {

	public function get_defaults() {
		return array(
			'prefix'         => '',
			'tab_slug'       => 'custom_css',
			'toggle_slug'    => 'visibility',
			'hover'          => 'tabs',
			'mobile_options' => true,
			'sticky'         => true,
			'default'        => ET_Builder_Module_Helper_Overflow::OVERFLOW_DEFAULT,
		);
	}

	public function get_fields( array $args = array() ) {
		$settings = array_merge( $this->get_defaults(), $args );

		return array_merge(
			$this->get_field( 'x', $settings ),
			$this->get_field( 'y', $settings )
		);
	}

	protected function get_field( $axis, $args ) {
		static $i18n;

		if ( ! $i18n ) {
			$i18n = array(
				'default'     => __( 'Default', 'et_builder' ),
				'visible'     => __( 'Visible', 'et_builder' ),
				'scroll'      => __( 'Scroll', 'et_builder' ),
				'hidden'      => __( 'Hidden', 'et_builder' ),
				'auto'        => __( 'Auto', 'et_builder' ),
				'description' => __( 'Here you can control element overflow on the %s axis. If set to scroll, content that overflows static widths or heights will trigger a browser scrollbar. If set to hidden, content overflow will be clipped.', 'et_builder' ),
				'horizontal'  => __( 'Horizontal Overflow', 'et_builder' ),
				'vertical'    => __( 'Vertical Overflow', 'et_builder' ),
			);
		}

		$overflow         = et_pb_overflow();
		$OVERFLOW_DEFAULT = ET_Builder_Module_Helper_Overflow::OVERFLOW_DEFAULT;
		$OVERFLOW_VISIBLE = ET_Builder_Module_Helper_Overflow::OVERFLOW_VISIBLE;
		$OVERFLOW_SCROLL  = ET_Builder_Module_Helper_Overflow::OVERFLOW_SCROLL;
		$OVERFLOW_HIDDEN  = ET_Builder_Module_Helper_Overflow::OVERFLOW_HIDDEN;
		$OVERFLOW_AUTO    = ET_Builder_Module_Helper_Overflow::OVERFLOW_AUTO;

		switch ( $axis ) {
			case 'x':
				$field = $overflow->get_field_x( $args['prefix'] );
				$label = $i18n['horizontal'];
				break;
			default:
				$field = $overflow->get_field_y( $args['prefix'] );
				$label = $i18n['vertical'];
				break;
		}

		$settings = array(
			'label'          => $label,
			'type'           => 'select',
			'hover'          => $args['hover'],
			'mobile_options' => $args['mobile_options'],
			'sticky'         => $args['sticky'],
			'default'        => $args['default'],
			'tab_slug'       => $args['tab_slug'],
			'toggle_slug'    => $args['toggle_slug'],
			'options'        => array(
				$OVERFLOW_DEFAULT => $i18n['default'],
				$OVERFLOW_VISIBLE => $i18n['visible'],
				$OVERFLOW_SCROLL  => $i18n['scroll'],
				$OVERFLOW_HIDDEN  => $i18n['hidden'],
				$OVERFLOW_AUTO    => $i18n['auto'],
			),
			'description'    => sprintf( $i18n['description'], strtoupper( $axis ) ),
		);

		$options = array( $field => $settings );

		return $options;
	}
}

return new ET_Builder_Module_Field_Overflow();
