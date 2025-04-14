( function($) {

	$(function() {
		// WP 5.8 above - Widget Block Editor.
		var is_widgets_block_editor = $('#widgets-editor').length > 0;
		var $et_pb_sidebars         = $('div[id^=et_pb_widget_area_]');

		// 1.a Appends custom widget area create form panel.
		var et_pb_sidebars_append_widget_create_area = function() {
			var widget_editor_area = is_widgets_block_editor ? '.block-editor-writing-flow > div' : '.widget-liquid-right';

			$(widget_editor_area).append(et_pb_options.widget_info);

			var $widget_create_area = $('#et_pb_widget_area_create');
			var $widget_name_input  = $widget_create_area.find('#et_pb_new_widget_area_name');

			$widget_create_area.find('.et_pb_create_widget_area').on('click', function(event) {
				var $this_el = $(this);

				event.preventDefault();

				if ('' === $widget_name_input.val()) return;

				$.ajax({
					type: "POST",
					url: et_pb_options.ajaxurl,
					data:
					{
						action: 'et_pb_add_widget_area',
						et_admin_load_nonce: et_pb_options.et_admin_load_nonce,
						et_widget_area_name: $widget_name_input.val()
					},
					success: function(data) {
						$this_el.closest('#et_pb_widget_area_create').find('.et_pb_widget_area_result').hide().html(data).slideToggle();
					}
				});
			});
		}

		// 1.b. Appends custom widget area remove button and handles remove action.
		var et_pb_sidebars_append_delete_button = function() {
			// 1.b.1. Append custom widget area remove button.
			var widget_area_id = is_widgets_block_editor ? $(this).data('widget-area-id') : $(this).attr('id');
			var widget_wrapper = is_widgets_block_editor ? '.block-editor-block-list__block' : '.widgets-holder-wrap';
			var widget_title   = is_widgets_block_editor ? '.components-panel__body-toggle' : '.sidebar-name h2, .sidebar-name h3';

			$(this).closest(widget_wrapper).find(widget_title).before('<a href="#" class="et_pb_widget_area_remove" data-et-widget-area-id="' + widget_area_id + '">' + et_pb_options.delete_string + '</a>');

			// 1.b.2. Handles remove widget area action.
			$('.et_pb_widget_area_remove').on('click', function(event) {
				var $this_el = $(this);

				event.preventDefault();

				$.ajax( {
					type: "POST",
					url: et_pb_options.ajaxurl,
					data:
					{
						action : 'et_pb_remove_widget_area',
						et_admin_load_nonce : et_pb_options.et_admin_load_nonce,
						et_widget_area_name : $this_el.data('et-widget-area-id'),
					},
					success: function( data ){
						$('a[data-et-widget-area-id="' + data + '"]').closest(widget_wrapper).remove();
					}
				} );

				return false;
			});
		};

		// 2. Observe to append custom widget area create form and remove buttons.
		if (is_widgets_block_editor) {
			var widget_block_editor_mutation = _.debounce(function(mutations, observer) {
				var is_widget_area_create_added = $('#et_pb_widget_area_create').length > 0;
				var is_widget_area_remove_added = $('.et_pb_widget_area_remove').length > 0;

				if (! is_widget_area_create_added) {
					et_pb_sidebars_append_widget_create_area();
				}

				if (! is_widget_area_remove_added) {
					$('div[data-widget-area-id^=et_pb_widget_area_]').each(et_pb_sidebars_append_delete_button);
				}

				// Disconnect once we know the custom widget area create form and remove buttons
				// are added to the Widget Block Editor.
				if (is_widget_area_create_added && is_widget_area_remove_added) {
					observer.disconnect();
				}
			}, 1000);

			// Watch for Widgets to load the Block Editor and Widget Area blocks. There is no
			// event to know when they will be loaded, hence we use mutation observer.
			var widget_block_editor_observer = new MutationObserver(widget_block_editor_mutation);

			// WP 6.1 and below - Widget Editor mutating node.
			var widget_block_editor_node = document.querySelector('.block-editor-block-list__layout') || document.querySelector('#widgets-editor');

			widget_block_editor_observer.observe(widget_block_editor_node, {childList: true});
		} else {
			et_pb_sidebars_append_widget_create_area();
			$et_pb_sidebars.each(et_pb_sidebars_append_delete_button);
		}
	} );

} )(jQuery);