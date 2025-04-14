/**
 * wp-color-picker-alpha
 *
 * Version 1.0
 * Copyright (c) 2017 Elegant Themes.
 * Licensed under the GPLv2 license.
 *
 * Overwrite Automattic Iris for enabled Alpha Channel in wpColorPicker
 * Only run in input and is defined data alpha in true
 * Add custom colorpicker UI
 *
 * This is modified version made by Elegant Themes based on the work covered by
 * the following copyright:
 *
 * wp-color-picker-alpha Version: 1.1
 * https://github.com/23r9i0/wp-color-picker-alpha
 * Copyright (c) 2015 Sergio P.A. (23r9i0).
 * Licensed under the GPLv2 license.
 */
( function( $ ) {
	// Variable for some backgrounds
	var image = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAIAAAHnlligAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAHJJREFUeNpi+P///4EDBxiAGMgCCCAGFB5AADGCRBgYDh48CCRZIJS9vT2QBAggFBkmBiSAogxFBiCAoHogAKIKAlBUYTELAiAmEtABEECk20G6BOmuIl0CIMBQ/IEMkO0myiSSraaaBhZcbkUOs0HuBwDplz5uFJ3Z4gAAAABJRU5ErkJggg==';
	// html stuff for wpColorPicker copy of the original color-picker.js
	var	_before = '<button type="button" class="button wp-color-result" aria-expanded="false"><span class="wp-color-result-text"></span></button>',
		_after = '<div class="wp-picker-holder" />',
		_wrap = '<div class="wp-picker-container" />',
		_button = '<input type="button" class="button button-small button-clear hidden" />',
		_wrappingLabel = '<label></label>',
		_wrappingLabelText = '<span class="screen-reader-text"></span>',
		_close_button = '<button type="button" class="button button-confirm" />',
		_close_button_icon = '<div style="fill: #3EF400; width: 25px; height: 25px; margin-top: -1px;"><svg viewBox="0 0 28 28" preserveAspectRatio="xMidYMid meet" shapeRendering="geometricPrecision"><g><path d="M19.203 9.21a.677.677 0 0 0-.98 0l-5.71 5.9-2.85-2.95a.675.675 0 0 0-.98 0l-1.48 1.523a.737.737 0 0 0 0 1.015l4.82 4.979a.677.677 0 0 0 .98 0l7.68-7.927a.737.737 0 0 0 0-1.015l-1.48-1.525z" fillRule="evenodd" /></g></svg></div>';

  // Color picker label translation. By default, set label manually without translation.
  var _defaultString    = 'Default';
  var _defaultAriaLabel = 'Select default color';
  var _clearString      = 'Clear';
  var _clearAriaLabel   = 'Clear color';
  var _colorValue       = 'Color value';
  var _selectColor      = 'Select Color';

  if ('undefined' !== typeof wp && 'undefined' !== typeof wp.i18n && 'undefined' !== typeof wp.i18n.__) {
    // Directly use wp.i18n if it exists. wp.i18n is added on 5.0.
    var __ = wp.i18n.__;
    _defaultString    = __( 'Default' );
    _defaultAriaLabel = __( 'Select default color' );
    _clearString      = __( 'Clear' );
    _clearAriaLabel   = __( 'Clear color' );
    _colorValue       = __( 'Color value' );
    _selectColor      = __( 'Select Color' );
  } else if ('undefined' !== typeof wpColorPickerL10n && 'undefined' !== typeof wpColorPickerL10n.current) {
    // Or use wpColorPickerL10n if it's still supported. wpColorPickerL10n is deprecated
    // since 5.5.
    _defaultString    = wpColorPickerL10n.defaultString;
    _defaultAriaLabel = wpColorPickerL10n.defaultAriaLabel;
    _clearString      = wpColorPickerL10n.clear,
    _clearAriaLabel   = wpColorPickerL10n.clearAriaLabel;
    _colorValue       = wpColorPickerL10n.defaultLabel;
    _selectColor      = wpColorPickerL10n.pick;
  }

	/**
	 * Overwrite Color
	 * for enable support rbga
	 */
	Color.fn.toString = function() {
		if ( this._alpha < 1 )
			return this.toCSS( 'rgba', this._alpha ).replace( /\s+/g, '' );

		var hex = parseInt( this._color, 10 ).toString( 16 );

		if ( this.error )
			return '';

		if ( hex.length < 6 ) {
			for ( var i = 6 - hex.length - 1; i >= 0; i-- ) {
				hex = '0' + hex;
			}
		}

		return '#' + hex;
	};

	/**
	 * Overwrite wpColorPicker
	 */
	$.widget( 'wp.wpColorPicker', $.wp.wpColorPicker, {

		_create: function() {
			// Return early if Iris support is missing.
			if ( ! $.support.iris ) {
				return;
			}

			var self = this,
				el = self.element;

			// Override default options with options bound to the element.
			$.extend( self.options, el.data() );

			// Create a color picker which only allows adjustments to the hue.
			if ( self.options.type === 'hue' ) {
				return self._createHueOnly();
			}

			// Bind the close event.
			self.close = self.close.bind(self);

			self.initialValue = el.val();

			// Add a CSS class to the input field.
			el.addClass( 'wp-color-picker' );

			/*
			 * Check if there's already a wrapping label, e.g. in the Customizer.
			 * If there's no label, add a default one to match the Customizer template.
			 */
			if ( ! el.parent( 'label' ).length ) {
				// Wrap the input field in the default label.
				el.wrap( _wrappingLabel );
				// Insert the default label text.
				self.wrappingLabelText = $( _wrappingLabelText )
					.insertBefore( el )
					.text( _colorValue );
			}

			/*
			 * At this point, either it's the standalone version or the Customizer
			 * one, we have a wrapping label to use as hook in the DOM, let's store it.
			 */
			self.wrappingLabel = el.parent();

			// Wrap the label in the main wrapper.
			self.wrappingLabel.wrap( _wrap );
			// Store a reference to the main wrapper.
			self.wrap = self.wrappingLabel.parent();
			// Set up the toggle button and insert it before the wrapping label.
			self.toggler = $( _before )
				.insertBefore( self.wrappingLabel )
				.css( { backgroundColor: self.initialValue } )
				.attr( 'title', _selectColor )
				.addClass( 'et-wp-color-result-updated');

			// some strings were changed in recent colorpicker update, but we still need to use legacy strings in some places
			if ( typeof et_pb_color_picker_strings !== 'undefined' ) {
				self.toggler.attr( 'data-legacy_title', et_pb_color_picker_strings.legacy_pick ).attr( 'data-current', et_pb_color_picker_strings.legacy_current );
			}

			// Set the toggle button span element text.
			self.toggler.find( '.wp-color-result-text' ).text( _selectColor );
			// Set up the Iris container and insert it after the wrapping label.
			self.pickerContainer = $( _after ).insertAfter( self.wrappingLabel );
			// Store a reference to the Clear/Default button.
			self.button = $( _button );
			self.close_button = $( _close_button ).html( _close_button_icon );

			if ( self.options.diviColorpicker ) {
				el.after( self.close_button );
			}

			// Set up the Clear/Default button.
			if ( self.options.defaultColor ) {
				self.button
					.addClass( 'wp-picker-default' )
					.val( _defaultString )
					.attr( 'aria-label', _defaultAriaLabel );
			} else {
				self.button
					.addClass( 'wp-picker-clear' )
					.val( _clearString )
					.attr( 'aria-label', _clearAriaLabel );
			}

			// Wrap the wrapping label in its wrapper and append the Clear/Default button.
			self.wrappingLabel
				.wrap( '<span class="wp-picker-input-wrap hidden" />' )
				.after( self.button );

			/*
			 * The input wrapper now contains the label+input+Clear/Default button.
			 * Store a reference to the input wrapper: we'll use this to toggle
			 * the controls visibility.
			 */
			self.inputWrapper = el.closest( '.wp-picker-input-wrap' );

			/*
			 * CSS for support < 4.9
			 */
			self.toggler.css({
				'height': '24px',
				'margin': '0 6px 6px 0',
				'padding': '0 0 0 30px',
				'font-size': '11px'
			});


			el.iris( {
				target: self.pickerContainer,
				hide: self.options.hide,
				width: self.options.width,
				height: self.options.height,
				mode: self.options.mode,
				palettes: self.options.palettes,
				diviColorpicker: self.options.diviColorpicker,
				change: function( event, ui ) {
					if ( self.options.alpha ) {
						self.toggler.css( {
							'background-image' : 'url(' + image + ')',
							'position' : 'relative'
						} );
						if ( self.toggler.find('span.color-alpha').length == 0 ) {
							self.toggler.append('<span class="color-alpha" />');
						}
						self.toggler.find( 'span.color-alpha' ).css( {
							'width'                     : '100%',
							'height'                    : '100%',
							'position'                  : 'absolute',
							'top'                       : '0px',
							'left'                      : '0px',
							'border-top-left-radius'    : '3px',
							'border-bottom-left-radius' : '3px',
							'background'                : ui.color.toString()
						} );
					} else {
						self.toggler.css( { backgroundColor: ui.color.toString() } );
					}

					// check for a custom cb
					if ( 'function' === typeof self.options.change ) {
						self.options.change.call( this, event, ui );
					}
				}
			} );

			el.val( self.initialValue );
			self._addListeners();

			// Force the color picker to always be closed on initial load.
			if ( ! self.options.hide ) {
				self.toggler.trigger('click');
			}
		},

		_addListeners: function() {
			var self = this;

			// Prevent any clicks inside this widget from leaking to the top and closing it.
			self.wrap.on( 'click.wpcolorpicker', function( event ) {
				event.stopPropagation();
				return false;
			});

			self.toggler.on('click', function() {
				if ( self.toggler.hasClass( 'wp-picker-open' ) ) {
					self.close();
				} else {
					self.open();
				}
			});

			self.element.on( 'change', function( event ) {
				// Empty or Error = clear
				if ( $( this ).val() === '' || self.element.hasClass( 'iris-error' ) ) {
					if ( self.options.alpha ) {
						self.toggler.find( 'span.color-alpha' ).css( 'backgroundColor', '' );
					} else {
						self.toggler.css( 'backgroundColor', '' );
					}

					// fire clear callback if we have one
					if ( 'function' === typeof self.options.clear )
						self.options.clear.call( this, event );
				}
			} );

			self.button.on( 'click', function( event ) {
				if ( $( this ).hasClass( 'wp-picker-clear' ) ) {
					self.element.val( '' );
					if ( self.options.alpha ) {
						self.toggler.find( 'span.color-alpha' ).css( 'backgroundColor', '' );
					} else {
						self.toggler.css( 'backgroundColor', '' );
					}

					if ( 'function' === typeof self.options.clear )
						self.options.clear.call( this, event );

				} else if ( $( this ).hasClass( 'wp-picker-default' ) ) {
					self.element.val( self.options.defaultColor ).trigger('change');
				}
			});

			self.close_button.on('click', function(event) {
				event.preventDefault();
				self.close();
			});
		},

		close: function() {
			this._super();
			var self = this;

			if ('function' === typeof self.options.onClose) {
				self.options.onClose.call(this);
			}
		},
	});

	/**
	 * Overwrite iris
	 */
	$.widget( 'a8c.iris', $.a8c.iris, {
		_create: function() {
			this._super();

			// Global option for check is mode rbga is enabled
			this.options.alpha = this.element.data( 'alpha' ) || false;

			// Is not input disabled
			if ( ! this.element.is( ':input' ) ) {
				this.options.alpha = false;
			}

			if ( typeof this.options.alpha !== 'undefined' && this.options.alpha ) {
				var self = this,
					el = self.element,
					_html = '<div class="iris-strip iris-slider iris-alpha-slider"><div class="iris-slider-offset iris-slider-offset-alpha"></div></div>',
					aContainer = $( _html ).appendTo( self.picker.find( '.iris-picker-inner' ) ),
					aSlider = aContainer.find( '.iris-slider-offset-alpha' ),
					controls = {
						aContainer: aContainer,
						aSlider: aSlider
					};

				// Set default width for input reset
				self.options.defaultWidth = el.width();

				// Update width for input
				if ( self._color._alpha < 1 || self._color.toString().indexOf('rgb') != 1 ) {
					el.width( parseInt( self.options.defaultWidth+100 ) );
				}

				// Push new controls
				$.each( controls, function( k, v ){
					self.controls[k] = v;
				});

				// Change size strip and add margin for sliders
				self.controls.square.css({'margin-right': '0px'});
				var emptyWidth = ( self.picker.width() - self.controls.square.width() - 20 ),
					stripsMargin = emptyWidth/6,
					stripsWidth = (emptyWidth/2) - stripsMargin;

				$.each( [ 'aContainer', 'strip' ], function( k, v ) {
					self.controls[v].width( stripsWidth ).css({ 'margin-left': stripsMargin + 'px' });
				});

				// Add new slider
				self._initControls();

				// For updated widget
				self._change();
			}
		},
		_initControls: function() {
			this._super();

			if ( this.options.alpha ) {
				var self = this,
					controls = self.controls;

				controls.aSlider.slider({
					orientation: 'vertical',
					min: 0,
					max: 100,
					step: 1,
					value: parseInt( self._color._alpha*100 ),
					slide: function( event, ui ) {
						// Update alpha value
						self._color._alpha = parseFloat( ui.value/100 );
						self._change.apply( self, arguments );
					}
				});
			}
		},
		_change: function() {
			this._super();
			var self = this,
				el = self.element;

			if ( this.options.alpha ) {
				var	controls = self.controls,
					alpha = parseInt( self._color._alpha*100 ),
					color = self._color.toRgb(),
					gradient = [
						'rgb(' + color.r + ',' + color.g + ',' + color.b + ') 0%',
						'rgba(' + color.r + ',' + color.g + ',' + color.b + ', 0) 100%'
					],
					defaultWidth = self.options.defaultWidth,
					target = self.picker.closest('.wp-picker-container').find( '.wp-color-result' );

				// Generate background slider alpha, only for CSS3 old browser fuck!! :)
				controls.aContainer.css({ 'background': 'linear-gradient(to bottom, ' + gradient.join( ', ' ) + '), url(' + image + ')' });

				if ( target.hasClass('wp-picker-open') ) {
					// Update alpha value
					controls.aSlider.slider( 'value', alpha );

					/**
					 * Disabled change opacity in default slider Saturation ( only is alpha enabled )
					 * and change input width for view all value
					 */
					if ( self._color._alpha < 1 ) {
						var style = controls.strip.attr( 'style' ).replace( /rgba\(([0-9]+,)(\s+)?([0-9]+,)(\s+)?([0-9]+)(,(\s+)?[0-9\.]+)\)/g, 'rgb($1$3$5)' );

						controls.strip.attr( 'style', style );

						el.width( parseInt( defaultWidth+100 ) );
					} else {
						el.width( defaultWidth );
					}
				}
			}

			var reset = el.data('reset-alpha') || false;
			if ( reset ) {
				self.picker.find( '.iris-palette-container' ).on( 'click.palette', '.iris-palette', function() {
					self._color._alpha = 1;
					self.active = 'external';
					self._change();
				});
			}
		},
		_addInputListeners: function( input ) {
			var self = this,
				debounceTimeout = 700, // originally set to 100, but some user perceive it as "jumps to random colors at third digit"
				callback = function( event ){
					var color = new Color( input.val() ),
						val = input.val();

					input.removeClass( 'iris-error' );
					// we gave a bad color
					if ( color.error ) {
						// don't error on an empty input
						if ( val !== '' ) {
							input.addClass( 'iris-error' );
						}
					} else {
						if ( color.toString() !== self._color.toString() ) {
							// let's not do this on keyup for hex shortcodes
							if ( ! ( event.type === 'keyup' && val.match( /^[0-9a-fA-F]{3}$/ ) ) ) {
								self._setOption( 'color', color.toString() );
							}
						}
					}
				};

			input.on( 'change', callback ).on( 'keyup', self._debounce( callback, debounceTimeout ) );

			// If we initialized hidden, show on first focus. The rest is up to you.
			if ( self.options.hide ) {
				input.one( 'focus', function() {
					self.show();
				});
			}
		},
		_dimensions: function( reset ) {
			// whatever size
			var self = this,
				opts = self.options,
				controls = self.controls,
				square = controls.square,
				strip = self.picker.find( '.iris-strip' ),
				squareWidth = '77.5%',
				stripWidth = '12%',
				totalPadding = 20,
				innerWidth = opts.border ? opts.width - totalPadding : opts.width,
				controlsHeight,
				paletteCount = Array.isArray( opts.palettes ) ? opts.palettes.length : self._palettes.length,
				paletteMargin, paletteWidth, paletteContainerWidth;

			if ( reset ) {
				square.css( 'width', '' );
				strip.css( 'width', '' );
				self.picker.css( {width: '', height: ''} );
			}

			squareWidth = innerWidth * ( parseFloat( squareWidth ) / 100 );
			stripWidth = innerWidth * ( parseFloat( stripWidth ) / 100 );
			controlsHeight = opts.border ? squareWidth + totalPadding : squareWidth;

			if (opts.diviColorpicker ) {
				square.width( opts.width ).height( opts.height );
				controlsHeight = opts.height;
			} else {
				square.width( squareWidth ).height( squareWidth );
			}

			strip.height( squareWidth ).width( stripWidth );
			self.picker.css({
				width: 'number' === typeof opts.width ? opts.width + 'px' : opts.width,
				height: 'number' === typeof controlsHeight ? controlsHeight + 'px' : controlsHeight,
			});

			if ( ! opts.palettes ) {
				return self.picker.css( 'paddingBottom', '' );
			}

			// single margin at 2%
			paletteMargin = squareWidth * 2 / 100;
			paletteContainerWidth = squareWidth - ( ( paletteCount - 1 ) * paletteMargin );
			paletteWidth = paletteContainerWidth / paletteCount;
			self.picker.find('.iris-palette').each( function( i ) {
				var margin = i === 0 ? 0 : paletteMargin;
				$( this ).css({
					width: paletteWidth + 'px',
					height: paletteWidth + 'px',
					marginLeft: margin + 'px',
				});
			});
			self.picker.css( 'paddingBottom', paletteWidth + paletteMargin + 'px' );
			strip.height( paletteWidth + paletteMargin + squareWidth );
		}
	} );
}( jQuery ) );

// Auto Call plugin is class is color-picker.
jQuery(function($) {
  $('.color-picker').wpColorPicker();
});
