function et_failure_notice_get_page_url() {
  return $('#wp-admin-bar-et-disable-visual-builder a').attr('href');
}

$('body').on('click', '.et_builder_increase_memory', function() {
	const $this_button = $(this);

	$.ajax({
		type: "POST",
		dataType: 'json',
		url: et_fb_options.ajaxurl,
		data: {
			action : 'et_pb_increase_memory_limit',
			et_admin_load_nonce : et_fb_options.et_admin_load_nonce
		},
		success: function(data) {
			if (!_.isUndefined(data.success)) {
				$this_button.addClass('et_builder_modal_action_button_success').text(et_fb_options.memory_limit_increased);
			} else {
				$this_button.addClass('et_builder_modal_action_button_fail').prop('disabled', true).text(et_fb_options.memory_limit_not_increased);
			}
		}
	});

	return false;
} );

$('body').on('click', '.et-builder-timeout .et-core-modal-action', function() {
  location.reload();
  return false;
} );

// disable Visual Builder on Close button
$('body').on('click', '.et-builder-timeout .et-core-modal-close, .et-builder-timeout', function() {
  location.assign(et_failure_notice_get_page_url());
  return false;
} );


// disable Visual Builder on Close button
$('body').on('click', '.et-theme-builder-no-post-content .et-core-modal-close, .et-theme-builder-no-post-content', function(e) {
  if (!$(e.target).is('.et-core-modal-action')) {
    e.preventDefault();
    location.assign(et_failure_notice_get_page_url());
  }
});
