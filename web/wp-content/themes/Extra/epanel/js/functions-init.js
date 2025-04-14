/* <![CDATA[ */
	var clearpath = ePanelSettings.clearpath;

	jQuery(function($){
		var editors = [];
		var caps    = ePanelSettings.allowedCaps;

		function addEditorInstance(codeEditor, $element, config) {
			if (!$element || $element.length === 0) {
				return;
			}
			var instance = codeEditor.initialize( $element , {
				codemirror: config,
			} );
			if (instance && instance.codemirror) {
				editors.push(instance.codemirror);
			}
		}

		// Add library buttons in code mirror editor.
		function addLibraryButtons($codeMirrorWrap) {
			var libraryBtnsHTML =
				'<ul class="et-code-snippets-library-btns-wrap">' +
					(caps.addLibrary ?
					'<li class="et-code-snippets-btn snippet-add">' +
						'<span class="add"></span>' +
					'</li>' : '') +
					(caps.saveLibrary ?
					'<li class="et-code-snippets-btn snippet-save">' +
						'<span class="save"></span>' +
					'</li>' : '') +
					(caps.portability ?
					'<li class="et-code-snippets-btn snippet-portability">' +
						'<span class="portability"></span>' +
					'</li>' : '') +
				'</ul>'
			;

			$codeMirrorWrap.prepend(libraryBtnsHTML);
		}

		// Extract the context form code mirror textarea.
		function getCodeSnippetsContext(element) {
			var areaForCSS = $(element).parents('.CodeMirror-wrap').siblings('textarea[id*=\'_custom_css\']');

			return areaForCSS.length ? 'code_css' : 'code_html';
		}

		// Get the Code Mirror textarea id.
		function getCodeMirrorId(element) {
			var codeArea = $(element).parents('.CodeMirror').siblings('textarea');

			return codeArea.attr('id');
		}

		// Add library buttons click event listeners.
		function addLibraryButtonsClickEvent() {
			$codeSnippetsBtnsWrap = $('.et-code-snippets-library-btns-wrap');

			if ($codeSnippetsBtnsWrap.length) {
				$codeSnippetsBtnsWrap.find('.add').parent().click(function(e) {
					$(window).trigger('et_epanel_code_snippets_open_add_modal', [getCodeSnippetsContext(e.target), getCodeMirrorId(e.target)]);
				});

				$codeSnippetsBtnsWrap.find('.save').parent().click(function(e) {
					$(window).trigger('et_epanel_code_snippets_open_save_modal', [getCodeSnippetsContext(e.target), getCodeMirrorId(e.target)]);
				});

				$codeSnippetsBtnsWrap.find('.portability').parent().click(function(e) {
					$(window).trigger('et_epanel_code_snippets_open_portability_modal', [getCodeSnippetsContext(e.target), getCodeMirrorId(e.target)]);
				});
			}
		}

		// Use WP 4.9 CodeMirror Editor for Custom CSS
		var codeEditor = window.wp && window.wp.codeEditor;
		if (codeEditor && codeEditor.initialize && codeEditor.defaultSettings && codeEditor.defaultSettings.codemirror) {

			// User ET CodeMirror theme
			var configCSS = $.extend({}, codeEditor.defaultSettings.codemirror, {
				theme: 'et',
			});
			var configHTML = $.extend({}, configCSS, {
				mode: 'htmlmixed',
			});

			if ($('#divi_custom_css').length > 0) {
				// Divi Theme
				addEditorInstance(codeEditor, $('#divi_custom_css'), configCSS);
				addEditorInstance(codeEditor, $('#divi_integration_head'), configHTML);
				addEditorInstance(codeEditor, $('#divi_integration_body'), configHTML);
				addEditorInstance(codeEditor, $('#divi_integration_single_top'), configHTML);
				addEditorInstance(codeEditor, $('#divi_integration_single_bottom'), configHTML);
			} else if ($('#extra_custom_css').length > 0) {
				// Extra Theme
				addEditorInstance(codeEditor, $('#extra_custom_css'), configCSS);
				addEditorInstance(codeEditor, $('#extra_integration_head'), configHTML);
				addEditorInstance(codeEditor, $('#extra_integration_body'), configHTML);
				addEditorInstance(codeEditor, $('#extra_integration_single_top'), configHTML);
				addEditorInstance(codeEditor, $('#extra_integration_single_bottom'), configHTML);
			}

			// Code snippets area.
			var $codeMirrorWrap  = $('#epanel-content').find('.CodeMirror-wrap');
			var isSnippetAllowed = caps.addLibrary || caps.saveLibrary || caps.portability;

			if ($codeMirrorWrap.length && isSnippetAllowed) {
				addLibraryButtons($codeMirrorWrap);
				addLibraryButtonsClickEvent();
			}
		}

		var $palette_inputs = $( '.et_color_palette_main_input' );

		$('#epanel-content,#epanel-content > div').tabs({
			fx: {
				opacity: 'toggle',
				duration:'fast'
			},
			selected: 0,
			activate: function( event, ui ) {
				$epanel = $('#epanel');

				if ( $epanel.hasClass('onload') ) {
					$epanel.removeClass('onload');
				}
			}
		});

		$('.et-box-description').on('click', function() {
			var descheading = $(this).parent('.et-epanel-box').find(".et-box-title h3").html();
			var desctext = $(this).parent('.et-epanel-box').find(".et-box-title .et-box-descr").html();

			$('body').append("<div id='custom-lbox'><div class='et-box-desc'><div class='et-box-desc-top'>"+ ePanelSettings.help_label +"</div><div class='et-box-desc-content'><h3>"+descheading+"</h3>"+desctext+"<div class='et-lightbox-close'></div> </div> <div class='et-box-desc-bottom'></div>	</div></div>");

			et_pb_center_modal( $( '.et-box-desc' ) );

			$('.et-lightbox-close').on('click', function() {
				et_pb_close_modal($('#custom-lbox'));
			});
		});

		$('.et-defaults-button.epanel-reset').on('click', function(e) {
			e.preventDefault();
			$(".reset-popup-overlay, .defaults-hover").addClass('active');

			et_pb_center_modal( $( '.defaults-hover' ) );
		});

		$('.et-defaults-button.epanel-save').on('click', function(e) {
			e.preventDefault();
			var preferences = {
				modalType: 'save',
				sidebarLabel: ePanelSettings.i18n['Theme Option'],
				builtFor: ePanelSettings?.currentTheme ?? 'Divi',
			};

			$(window).trigger('et_theme-options_container_ready', [preferences]);
		});

		$('.et-defaults-button.epanel-add').on('click', function(e) {
			e.preventDefault();
			var preferences = {
				modalType: 'add',
				sidebarLabel: ePanelSettings.i18n['Theme Option'],
				builtFor: ePanelSettings?.currentTheme ?? 'Divi',
			};

			$(window).trigger('et_theme-options_container_ready', [preferences]);
		});

		$('.no').on('click', function() {
			et_pb_close_modal( $( '.reset-popup-overlay' ), 'no_remove' );

			//clean the modal classes when animation complete
			setTimeout( function() {
				$( '.reset-popup-overlay, .defaults-hover' ).removeClass( 'active et_pb_modal_closing' );
			}, 600 );
		});

		// ":not([safari])" is desirable but not necessary selector
		// ":not([safari])" is desirable but not necessary selector
		$('#epanel input:checkbox:not([safari]):not(.yes_no_button)').checkbox();
		$('#epanel input[safari]:checkbox:not(.yes_no_button)').checkbox({cls:'jquery-safari-checkbox'});
		$('#epanel input:radio:not(.yes_no_button)').checkbox();

		// Yes - No button UI
		$('.yes_no_button').each(function() {
			var $checkbox = $(this);
			var value     = $checkbox.is(':checked');
			var state     = value ? 'et_pb_on_state' : 'et_pb_off_state';
			var $template = $($('#epanel-yes-no-button-template').html()).find('.et_pb_yes_no_button').addClass(state);

			$checkbox.hide().after($template);

			if ( 'et_pb_static_css_file' === $checkbox.attr( 'id' ) ) {
				$checkbox
					.parent()
					.addClass( state )
					.next()
					.addClass( 'et_pb_clear_static_css' )
					.on( 'click', function() {
						epanel_clear_static_css( false, true );
					});

				if ( ! value ) {
					$checkbox.parents('.et-epanel-box').next().hide();
				}
			}

			if ( 'divi_dynamic_css' === $checkbox.attr( 'id' ) || 'extra_dynamic_css' === $checkbox.attr( 'id' ) ) {
				if ( ! value ) {
					$checkbox.parents('.et-epanel-box').next().hide();
					$checkbox.parents('.et-epanel-box').next().next().hide();
				}
			}

			if ( 'divi_enable_jquery_body' === $checkbox.attr( 'id' ) || 'extra_enable_jquery_body' === $checkbox.attr( 'id' ) ) {
				if ( ! value ) {
					$checkbox.parents('.et-epanel-box').next().hide();
					$checkbox.parents('.et-epanel-box').next().next().hide();
				}
			}

			if ( 'divi_google_fonts_inline' === $checkbox.attr( 'id' ) || 'extra_google_fonts_inline' === $checkbox.attr( 'id' ) ) {
				if ( ! value ) {
					$checkbox.parents('.et-epanel-box').next().hide();
				}
			}

			if ( 'divi_critical_css' === $checkbox.attr( 'id' ) || 'extra_critical_css' === $checkbox.attr( 'id' ) ) {
				if ( ! value ) {
					$checkbox.parents('.et-epanel-box').next().hide();
				}
			}

		});

		$('.et-box-content').on( 'click', '.et_pb_yes_no_button', function(e){
			e.preventDefault();
			// Fix for nested .et-box-content triggering checkboxes multiple times.
			e.stopPropagation();

			var $click_area = $(this),
				$box_content = $click_area.closest('.et-box-content'),
				$checkbox    = $box_content.find('input[type="checkbox"]'),
				$state       = $box_content.find('.et_pb_yes_no_button');

			if ( $state.parent().next().hasClass( 'et_pb_clear_static_css' ) ) {
				$state = $state.add( $state.parent() );

				if ( $checkbox.is( ':checked' ) ) {
					$box_content.parent().next().hide();
				} else {
					$box_content.parent().next().show();
				}
			}

			if ( 'divi_dynamic_css' === $checkbox.attr( 'id' ) || 'extra_dynamic_css' === $checkbox.attr( 'id' ) ) {
				if ( $checkbox.is( ':checked' ) ) {
					$box_content.parent().next().hide();
					$box_content.parent().next().next().hide();
				} else {
					$box_content.parent().next().show();
					$box_content.parent().next().next().show();
				}
			}

			if ( 'divi_enable_jquery_body' === $checkbox.attr( 'id' ) || 'extra_enable_jquery_body' === $checkbox.attr( 'id' ) ) {
				if ( $checkbox.is( ':checked' ) ) {
					$box_content.parent().next().hide();
					$box_content.parent().next().next().hide();
				} else {
					$box_content.parent().next().show();
					$box_content.parent().next().next().show();
				}
			}

			if ( 'divi_google_fonts_inline' === $checkbox.attr( 'id' ) || 'divi_google_fonts_inline' === $checkbox.attr( 'id' ) ) {
				if ( $checkbox.is( ':checked' ) ) {
					$box_content.parent().next().hide();
				} else {
					$box_content.parent().next().show();
				}
			}

			if ( 'divi_critical_css' === $checkbox.attr( 'id' ) || 'extra_critical_css' === $checkbox.attr( 'id' ) ) {
				if ( $checkbox.is( ':checked' ) ) {
					$box_content.parent().next().hide();
				} else {
					$box_content.parent().next().show();
				}
			}

			$state.toggleClass('et_pb_on_state et_pb_off_state');

			if ( $checkbox.is(':checked' ) ) {
				$checkbox.prop('checked', false);
			} else {
				$checkbox.prop('checked', true);
			}

		});

		var $save_message = $("#epanel-ajax-saving");

		$('#epanel-save-top').on('click', function(e) {
			e.preventDefault();

			$('#epanel-save').trigger('click');
		})

		$('#epanel-save').on('click', function() {
			epanel_save( false, true );
			return false;
		});

		function epanel_save( callback, message ) {

			// If CodeMirror is used
			if (editors.length > 0) {
				$.each(editors, function(i, editor) {
					if (editor.save) {
						// Make sure we store changes into original textarea
						editor.save();
					}
				})
			}

			var options_fromform = $('#main_options_form').formSerialize(),
				add_nonce = '&_ajax_nonce='+ePanelSettings.epanel_nonce;

			options_fromform += add_nonce;

			$.ajax({
				type: "POST",
				url: ajaxurl,
				data: options_fromform,
				beforeSend: function ( xhr ){
					if ( message ) {
						$save_message.removeAttr('class').fadeIn('fast');
					}
				},
				success: function(response){
					if ( message ) {
						$save_message.addClass('success-animation');

						setTimeout(function(){
							$save_message.fadeOut();
						},500);
					}

					if ( 'function' === typeof callback ) {
						callback();
					}
				}
			});
		}

		function epanel_clear_static_css( callback, message ) {
			var data = {
				action: 'et_core_page_resource_clear',
				et_owner: 'all',
				et_post_id: 'all',
				clear_page_resources_nonce: ePanelSettings.et_core_nonces.clear_page_resources_nonce,
			};

			$.ajax( {
				type: "POST",
				url: ajaxurl,
				data: data,
				beforeSend: function ( xhr ) {
					if ( message ) {
						$save_message.removeAttr( 'class' ).fadeIn( 'fast' );
					}
				},
				success: function ( response ) {
					if ( message ) {
						$save_message.addClass( 'success-animation' );

						setTimeout( function () {
							$save_message.fadeOut();
						}, 500 );
					}

					if ( 'function' === typeof callback ) {
						callback();
					}
				}
			} );
		}

		function et_pb_close_modal( $overlay, no_overlay_remove ) {
			var $modal_container = $overlay;

			// add class to apply the closing animation to modal
			$modal_container.addClass( 'et_pb_modal_closing' );

			//remove the modal with overlay when animation complete
			setTimeout( function() {
				if ( 'no_remove' !== no_overlay_remove ) {
					$modal_container.remove();
				}
			}, 600 );
		}

		if ( $palette_inputs.length ) {
			$palette_inputs.each( function() {
				var	$this_input                    = $( this ),
					$palette_wrapper               = $this_input.closest( '.et-box-content' ),
					$colorpalette_colorpickers     = $palette_wrapper.find( '.input-colorpalette-colorpicker' ),
					colorpalette_colorpicker_index = 0,
					saved_palette                  = $this_input.val().split('|');

				$colorpalette_colorpickers.each( function(){
					var $colorpalette_colorpicker      = $(this),
						colorpalette_colorpicker_color = saved_palette[ colorpalette_colorpicker_index ];

					$colorpalette_colorpicker.val( colorpalette_colorpicker_color ).wpColorPicker({
						hide : false,
						default : $(this).data( 'default-color' ),
						width: 313,
						palettes : false,
						change : function( event, ui ) {
							var $input     = $(this);
							var data_index = $input.attr('data-index');
							var $preview   = $palette_wrapper.find('.colorpalette-item-' + data_index + ' .color');
							var color      = ui.color.toString();

							$input.val( color );
							$preview.css({ 'backgroundColor' : color });
							saved_palette[ data_index - 1 ] = color;
							$this_input.val( saved_palette.join( '|' ) );
						}
					});

					$colorpalette_colorpicker.trigger( 'change' );

					colorpalette_colorpicker_index++;
				} );

				$palette_wrapper.on( 'click', '.colorpalette-item', function(e){
					e.preventDefault();

					var $colorpalette_item = $(this),
						data_index         = $colorpalette_item.attr('data-index');

					// Hide other colorpalette colorpicker
					$palette_wrapper.find( '.colorpalette-colorpicker' ).removeClass( 'active' );

					// Display selected colorpalette colorpicker
					$palette_wrapper.find( '.colorpalette-colorpicker[data-index="' + data_index + '"]' ).addClass( 'active' );
				});
			});
		}

		if ( typeof etCore !== 'undefined' && typeof etCore.portability !== 'undefined' ) {
			// Portability integration.
			etCore.portability.save = function( callback ) {
				epanel_save( callback, false );
			}
		}

		function et_pb_center_modal( $modal ) {
			var modal_height = $modal.outerHeight();
			var modal_height_adjustment = (0 - (modal_height / 2)) + 'px';

			$modal.css({
				top : '50%',
				bottom : 'auto',
				marginTop : modal_height_adjustment
			});
		}

		/* eslint-disable prefer-arrow-callback, space-before-blocks */
		$(window).on('et_epanel_code_snippets_open_add_modal', (event, context, codeMirrorId) => {
			// Used for the App and Modal container.
			$('body').first().append('<div id="et-code-snippets-container" class="snippets-modals-portal"></div>');

			var preferences = {
				containerId: 'et-code-snippets-container',
				context,
				codeMirrorId,
				modalType: 'add',
				sidebarLabel: 'code_html' === context ? ePanelSettings.i18n['Code Snippet'] : '',
			};
			var container   = window.document;

			$(window).trigger('et_code_snippets_container_ready', [preferences, container]);
		});
		/* eslint-enable */

		/* eslint-disable prefer-arrow-callback, space-before-blocks */
		$(window).on('et_epanel_code_snippets_open_save_modal', (event, context, codeMirrorId) => {
			// Used for the App and Modal container.
			$('body').first().append('<div id="et-code-snippets-container" class="snippets-modals-portal"></div>');

			var editor = jQuery(`#${codeMirrorId}`).next('.CodeMirror')[0].CodeMirror;
			var content  =  editor.getValue();

			if ('' === content) {
				return;
			}

			var preferences = {
				containerId: 'et-code-snippets-container',
				context,
				codeMirrorId,
				modalType: 'save',
				content: content,
				selectedContent: editor.getSelection()
			};
			var container   = window.document;

			$(window).trigger('et_code_snippets_container_ready', [preferences, container]);
		});
		/* eslint-enable */

		/* eslint-disable prefer-arrow-callback, space-before-blocks */
		$(window).on('et_epanel_code_snippets_open_portability_modal', (event, context, codeMirrorId) => {
			// Used for the App and Modal container.
			$('body').first().append('<div id="et-code-snippets-container" class="snippets-modals-portal"></div>');

			var editor = jQuery(`#${codeMirrorId}`).next('.CodeMirror')[0].CodeMirror;

			var preferences = {
				containerId: 'et-code-snippets-container',
				context,
				codeMirrorId,
				modalType: 'portability',
				content: editor.getValue()
			};
			var container   = window.document;

			$(window).trigger('et_code_snippets_container_ready', [preferences, container]);
		});

		$(document).on('mouseover', '.et-code-snippets-btn.snippet-save', function() {
			var codeMirrorId = getCodeMirrorId(this);
			var editor       = jQuery(`#${codeMirrorId}`).next('.CodeMirror')[0].CodeMirror;
			var content      = editor.getValue();

			if ('' === content) {
				$(this).addClass('et-code-snippets-btn--disabled');
			} else {
				$(this).removeClass('et-code-snippets-btn--disabled');
			}
		});
	});
/* ]]> */
