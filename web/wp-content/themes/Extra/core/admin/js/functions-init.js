/* <![CDATA[ */
var clearpath = ePanelishSettings.clearpath;

jQuery(function($) {
	var editors = [];

	function addEditorInstance( codeEditor, $element, config ) {
		if ( !$element || $element.length === 0 ) {
			return;
		}
		var instance = codeEditor.initialize( $element, {
			codemirror: config
		} );
		if ( instance && instance.codemirror ) {
			editors.push( instance.codemirror );
		}
	}

	// Use WP 4.9 CodeMirror Editor for Custom CSS
	var codeEditor = window.wp && window.wp.codeEditor;
	if ( codeEditor && codeEditor.initialize && codeEditor.defaultSettings && codeEditor.defaultSettings.codemirror ) {

		// User ET CodeMirror theme
		var configCSS  = $.extend( {}, codeEditor.defaultSettings.codemirror, {
			theme: 'et'
		} );
		var configHTML = $.extend( {}, configCSS, {
			mode: 'htmlmixed'
		} );

		if ( $( '#divi_custom_css' ).length > 0 ) {
			// Divi Theme
			addEditorInstance( codeEditor, $( '#divi_custom_css' ), configCSS );
			addEditorInstance( codeEditor, $( '#divi_integration_head' ), configHTML );
			addEditorInstance( codeEditor, $( '#divi_integration_body' ), configHTML );
			addEditorInstance( codeEditor, $( '#divi_integration_single_top' ), configHTML );
			addEditorInstance( codeEditor, $( '#divi_integration_single_bottom' ), configHTML );
		} else if ( $( '#extra_custom_css' ).length > 0 ) {
			// Extra Theme
			addEditorInstance( codeEditor, $( '#extra_custom_css' ), configCSS );
			addEditorInstance( codeEditor, $( '#extra_integration_head' ), configHTML );
			addEditorInstance( codeEditor, $( '#extra_integration_body' ), configHTML );
			addEditorInstance( codeEditor, $( '#extra_integration_single_top' ), configHTML );
			addEditorInstance( codeEditor, $( '#extra_integration_single_bottom' ), configHTML );
		}
	}

	var $palette_inputs = $( '.et_color_palette_main_input' );

	$( '#epanel-content,#epanel-content > div' ).tabs( {
		fx:       {
			opacity:  'toggle',
			duration: 'fast'
		},
		selected: 0,
		activate: function( event, ui ) {
			$epanel = $( '#epanel' );

			if ( $epanel.hasClass( 'onload' ) ) {
				$epanel.removeClass( 'onload' );
			}
		}
	} );

	$('.et-box-description').on('click', function(){
		var descheading = $( this ).parent( '.et-epanel-box' ).find( ".et-box-title h3" ).html();
		var desctext    = $( this ).parent( '.et-epanel-box' ).find( ".et-box-title .et-box-descr" ).html();

		$( 'body' ).append( "<div id='custom-lbox'><div class='et-box-desc'><div class='et-box-desc-top'>" + ePanelishSettings.help_label + "</div><div class='et-box-desc-content'><h3>" + descheading + "</h3>" + desctext + "<div class='et-lightbox-close'></div> </div> <div class='et-box-desc-bottom'></div>	</div></div>" );

		et_pb_center_modal( $( '.et-box-desc' ) );

		$('.et-lightbox-close').on('click', function(){
			et_pb_close_modal( $( '#custom-lbox' ) );
		});
	});

	$('.et-defaults-button.epanel-reset').on('click', function(e){
		e.preventDefault();
		$( ".reset-popup-overlay, .defaults-hover" ).addClass( 'active' );

		et_pb_center_modal( $( '.defaults-hover' ) );
	});

	$('.no').on('click', function(){
		et_pb_close_modal( $( '.reset-popup-overlay' ), 'no_remove' );

		//clean the modal classes when animation complete
		setTimeout( function() {
			$( '.reset-popup-overlay, .defaults-hover' ).removeClass( 'active et_pb_modal_closing' );
		}, 600 );
	});

	// ":not([safari])" is desirable but not necessary selector
	// ":not([safari])" is desirable but not necessary selector
	$( '#epanel input:checkbox:not([safari]):not(.yes_no_button)' ).checkbox();
	$( '#epanel input[safari]:checkbox:not(.yes_no_button)' ).checkbox( { cls: 'jquery-safari-checkbox' } );
	$( '#epanel input:radio:not(.yes_no_button)' ).checkbox();

	// Yes - No button UI
	$( '.yes_no_button' ).each( function() {
		var $checkbox = $( this );
		var value     = $checkbox.is( ':checked' );
		var state     = value ? 'et_pb_on_state' : 'et_pb_off_state';
		var $template = $( $( '#epanel-yes-no-button-template' ).html() ).find( '.et_pb_yes_no_button' ).addClass( state );

		$checkbox.hide().after( $template );
	} );

	$( '.et-box-content' ).on( 'click', '.et_pb_yes_no_button', function( e ) {
		e.preventDefault();
		// Fix for nested .et-box-content triggering checkboxes multiple times.
		e.stopPropagation();

		var $click_area  = $( this );
		var $box_content = $click_area.closest( '.et-box-content' );
		var $checkbox    = $box_content.find( 'input[type="checkbox"]' );
		var $state       = $box_content.find( '.et_pb_yes_no_button' );

		if ( $state.parent().next().hasClass( 'et_pb_clear_static_css' ) ) {
			$state = $state.add( $state.parent() );

			if ( $checkbox.is( ':checked' ) ) {
				$box_content.parent().next().hide();
			} else {
				$box_content.parent().next().show();
			}
		}

		$state.toggleClass( 'et_pb_on_state et_pb_off_state' );

		if ( $checkbox.is( ':checked' ) ) {
			$checkbox.prop( 'checked', false );
		} else {
			$checkbox.prop( 'checked', true );
		}

	} );

	var $save_message = $( "#epanel-ajax-saving" );

	$('#epanel-save-top').on('click', function(e) {
		e.preventDefault();

		$( '#epanel-save' ).trigger( 'click' );
	});

	$('#epanel-save').on('click', function() {
		epanel_save( false, true );
		return false;
	});

	function epanel_save( callback, message ) {

		// If CodeMirror is used
		if ( editors.length > 0 ) {
			$.each( editors, function( i, editor ) {
				if ( editor.save ) {
					// Make sure we store changes into original textarea
					editor.save();
				}
			} );
		}

		var options_fromform = $( '#main_options_form' ).formSerialize();
		var add_nonce        = '&_ajax_nonce=' + ePanelishSettings.epanelish_nonce;

		options_fromform += add_nonce;

		$.ajax( {
			type:       "POST",
			url:        ajaxurl,
			data:       options_fromform,
			beforeSend: function( xhr ) {
				if ( message ) {
					$save_message.removeAttr( 'class' ).fadeIn( 'fast' );
				}
			},
			success:    function( response ) {
				if ( message ) {
					$save_message.addClass( 'success-animation' );

					setTimeout( function() {
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
			var $this_input                    = $( this );
			var $palette_wrapper               = $this_input.closest( '.et-box-content' );
			var $colorpalette_colorpickers     = $palette_wrapper.find( '.input-colorpalette-colorpicker' );
			var colorpalette_colorpicker_index = 0;
			var saved_palette                  = $this_input.val().split( '|' );

			$colorpalette_colorpickers.each( function() {
				var $colorpalette_colorpicker      = $( this );
				var colorpalette_colorpicker_color = saved_palette[colorpalette_colorpicker_index];

				$colorpalette_colorpicker.val( colorpalette_colorpicker_color ).wpColorPicker( {
					hide:     false,
					default:  $( this ).data( 'default-color' ),
					width:    313,
					palettes: false,
					change:   function( event, ui ) {
						var $input     = $( this );
						var data_index = $input.attr( 'data-index' );
						var $preview   = $palette_wrapper.find( '.colorpalette-item-' + data_index + ' .color' );
						var color      = ui.color.toString();

						$input.val( color );
						$preview.css( { 'backgroundColor': color } );
						saved_palette[data_index - 1] = color;
						$this_input.val( saved_palette.join( '|' ) );
					}
				} );

				$colorpalette_colorpicker.trigger( 'change' );

				colorpalette_colorpicker_index++;
			} );

			$palette_wrapper.on( 'click', '.colorpalette-item', function( e ) {
				e.preventDefault();

				var $colorpalette_item = $( this );
				var data_index         = $colorpalette_item.attr( 'data-index' );

				// Hide other colorpalette colorpicker
				$palette_wrapper.find( '.colorpalette-colorpicker' ).removeClass( 'active' );

				// Display selected colorpalette colorpicker
				$palette_wrapper.find( '.colorpalette-colorpicker[data-index="' + data_index + '"]' ).addClass( 'active' );
			} );
		} );
	}

	if ( typeof etCore !== 'undefined' && typeof etCore.portability !== 'undefined' ) {
		// Portability integration.
		etCore.portability.save = function( callback ) {
			epanel_save( callback, false );
		};
	}

	function et_pb_center_modal( $modal ) {
		var modal_height            = $modal.outerHeight();
		var modal_height_adjustment = (0 - (modal_height / 2)) + 'px';

		$modal.css( {
			top:       '50%',
			bottom:    'auto',
			marginTop: modal_height_adjustment
		} );
	}

} );
/* ]]> */
