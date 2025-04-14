<?php
// Prevent file from being loaded directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

function extra_setup_eventon(){
	add_action( 'eventon_before_main_content', 'et_extra_output_content_wrapper', 11 );
	add_action( 'eventon_after_main_content', 'et_extra_output_content_wrapper_end', 9 );
}

add_action( 'after_setup_theme', 'extra_setup_eventon' );


function et_extra_output_content_wrapper() {
	echo '
		<div id="main-content">
			<div class="container">
				<div id="content-area" class="clearfix">
					<div class="et_pb_extra_column_main">';
}

function et_extra_output_content_wrapper_end() {
	echo '
					</div> <!--.et_pb_extra_column_main -->';

	get_sidebar();

	echo '
				</div> <!-- #content-area -->
			</div> <!-- .container -->
		</div> <!-- #main-content -->';
}
