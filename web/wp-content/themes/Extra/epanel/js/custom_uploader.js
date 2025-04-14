jQuery(function() {
	var et_file_frame;

	jQuery('.et-upload-image-button').on('click', function(event) {
		var this_el = jQuery( this ),
			use_for = this_el.parents( '.et-epanel-box' ).find( '.et-box-title > h3' ).text(),
			button_text = this_el.data( 'button_text' ),
			window_title = epanel_uploader.media_window_title,
			fileInput = this_el.parent().prev('input.et-upload-field');

			event.preventDefault();

			et_file_frame = wp.media.frames.et_file_frame = wp.media({
				title: window_title,
				library: {
					type: 'image'
				},
				button: {
					text: button_text,
				},
				multiple: false
			});

			et_file_frame.on( 'select', function() {
				var attachment = et_file_frame.state().get( 'selection' ).first().toJSON();
				fileInput.val( attachment.url );
			});

			et_file_frame.open();

		return false;
	});

	jQuery('.et-upload-image-reset').on('click', function() {
		jQuery(this).parent().prev( 'input.et-upload-field' ).val( '' );
	});
});