<?php

class ET_Builder_Module_PostContent extends ET_Builder_Module_Type_PostContent {

	public $slug = 'et_pb_post_content';

	public function init() {
		$this->name        = esc_html__( 'Post Content', 'et_builder' );
		$this->plural      = esc_html__( 'Post Content', 'et_builder' );
		$this->vb_support  = 'on';
		$this->help_videos = array();

		// Use specific selector to target only content inside the app when in VB
		$this->main_css_element = '%%order_class%%';
	}
}

if ( et_builder_should_load_all_module_data() ) {
	new ET_Builder_Module_PostContent();
}
