( function( $ ) {

	"use strict";

	// Extend etCorePortability since it is declared by localization.
	window.etCore.portability = $.extend( etCorePortability, {

		cancelled: false,

		boot: function( $instance ) {
			var $this = this;
			var $customizeHeader = $( '#customize-header-actions' );
			var $customizePortability = $( '.et-core-customize-controls-close' );

			// Moved portability button into customizer header
			if ( $customizeHeader.length && $customizePortability.length ) {
				$customizeHeader.append( $customizePortability );
			}

			$( '[data-et-core-portability]' ).each( function() {
				$this.listen( $( this ) );
			} );

			$('[data-et-core-portability-export-to-cloud]').each(function() {
				$this.listen( $( this ) );
			});

			// Release unecessary cache.
			etCorePortability = null;
		},

		listen: function( $el ) {
			var $this = this;

			$el.find('[data-et-core-portability-export]').on('click', function(e){
				e.preventDefault();

				if ( ! $this.actionsDisabled() ) {
					$this.disableActions();
					$this.export();
				}
			});

			$el.find('[data-et-core-portability-export-to-cloud]').on('click', function(e) {
				e.preventDefault();

				if ( ! $this.actionsDisabled() ) {
					$this.disableActions();
					$this.exportToCloud();
				}
			});


			$el.find( '.et-core-portability-export-form input[type="text"]' ).on( 'keydown', function( e ) {
				if ( 13 === e.keyCode ) {
					e.preventDefault();
					$el.find('[data-et-core-portability-export]').trigger('click');
				}
			} );

			// Portability populate import.
			$el.find( '.et-core-portability-import-form input[type="file"]' ).on( 'change', function( e ) {
				$this.populateImport( $( this ).get( 0 ).files[0] );
			} );

			$el.find('.et-core-portability-import').on('click', function(e){
				e.preventDefault();

				if ( ! $this.actionsDisabled() ) {
					$this.disableActions();
					$this.import();
				}
			});

			// Trigger file window.
			$el.find('.et-core-portability-import-form button').on('click', function(e){
				e.preventDefault();
				$this.instance( 'input[type="file"]' ).trigger( 'click' );
			});

			// Cancel request.
			$el.find('[data-et-core-portability-cancel]').on('click', function(e){
				e.preventDefault();
				$this.cancel();
			});
		},

		validateImportFile: function( file, noOutput ) {
			if ( undefined !== file && 'undefined' != typeof file.name  && 'undefined' != typeof file.type && 'json' == file.name.split( '.' ).slice( -1 )[0] ) {

				return true;
			}

			if ( ! noOutput ) {
				etCore.modalContent( '<p>' + this.text.invalideFile + '</p>', false, 3000, '#et-core-portability-import' );
			}

			this.enableActions();

			return false;
		},

		populateImport: function( file ) {
			if ( ! this.validateImportFile( file ) ) {
				return;
			}

			$( '.et-core-portability-import-placeholder' ).text( file.name );
		},

		import: async function(noBackup) {
			var $this = this;
			var file = $this.instance('input[type="file"]').get(0).files[0];
			file     = await $this.formatBuilderLayoutFile(file);

			if (undefined === window.FormData) {
				etCore.modalContent('<p>' + this.text.browserSupport + '</p>', false, 3000, '#et-core-portability-import');

				$this.enableActions();

				return;
			}

			if (!$this.validateImportFile(file)) {
				return;
			}

			$this.addProgressBar( $this.text.importing );

			// Export Backup if set.
			if ( $this.instance( '[name="et-core-portability-import-backup"]' ).is( ':checked' ) && ! noBackup ) {
				$this.export( true );

				$( $this ).on( 'exported', function() {
					$this.import( true );
				} );

				return;
			}

			var includeGlobalPresets = $this.instance('[name="et-core-portability-import-include-global-presets"]').is(':checked');

			$this.ajaxAction( {
				action: 'et_core_portability_import',
				file: file,
				include_global_presets: includeGlobalPresets,
				nonce: $this.nonces.import
			}, function( response ) {
				etCore.modalContent( '<div class="et-core-loader et-core-loader-success"></div>', false, 3000, '#et-core-portability-import' );
				$this.toggleCancel();

				$( document ).delay( 3000 ).queue( function() {
					etCore.modalContent( '<div class="et-core-loader"></div>', false, false, '#et-core-portability-import' );

					$( this ).dequeue().delay( 2000 ).queue( function() {
						// Save post content for individual content.
						if ( 'undefined' !== typeof response.data.postContent ) {
							var save = $( '#save-action #save-post' );

							if ( save.length === 0 ) {
								save = $( '#publishing-action input[type="submit"]' );
							}

							if ( 'undefined' !== typeof window.tinyMCE && window.tinyMCE.get( 'content' ) && ! window.tinyMCE.get( 'content' ).isHidden() ) {
								var editor = window.tinyMCE.get( 'content' );

								editor.setContent(response.data.postContent.trim(), { format: 'html' });
							} else {
								$('#content').val(response.data.postContent.trim());
							}

							save.trigger( 'click' );

							window.onbeforeunload = function() {
								$( 'body' ).fadeOut( 500 );
							}
						} else {
							$( 'body' ).fadeOut( 500, function() {
								// Remove confirmation popup before relocation.
								$( window ).off( 'beforeunload' );

								window.location = window.location.href.replace(/reset\=true\&|\&reset\=true/,'');
							} )
						}
					} );
				} );
			}, true );
		},

		renderNoItemsToExportError: function($this = this) {
			etCore.modalContent( '<div class="et-core-loader et-core-loader-fail"></div><h3>' + $this.text.noItemsToExport + '</h3>', false, true, '#' + $this.instance( '.ui-tabs-panel:visible' ).attr( 'id' ) );
	
			$this.enableActions();
		},

		export: function( backup, returnJSON = false ) {
			var $this = this,
				progressBarMessages = backup ? $this.text.backuping : $this.text.exporting;

			$this.save( function() {
				var posts = {},
					content = false;

				// Include selected posts.
				if ( $this.instance( '[name="et-core-portability-posts"]' ).is( ':checked' ) ) {
					$( '#posts-filter [name="post[]"]:checked:enabled' ).each( function() {
						posts[this.id] = this.value;
					} );

					// do not proceed and display error message if no Items selected
					if ( $.isEmptyObject( posts ) ) {
						etCore.modalContent( '<div class="et-core-loader et-core-loader-fail"></div><h3>' + $this.text.noItemsSelected + '</h3>', false, true, '#' + $this.instance( '.ui-tabs-panel:visible' ).attr( 'id' ) );

						$this.enableActions();

						return;
					}
				}

				$this.addProgressBar( progressBarMessages );

				// Get post layout.
				if ( 'undefined' !== typeof window.tinyMCE && window.tinyMCE.get( 'content' ) && ! window.tinyMCE.get( 'content' ).isHidden() ) {
					content = window.tinyMCE.get( 'content' ).getContent();
				} else if ( $( 'textarea#content' ).length > 0 ) {
					content = $( 'textarea#content' ).val();
				}

				if ( false !== content ) {
					content = content.replace( /^([^\[]*){1}/, '' );
					content = content.replace( /([^\]]*)$/, '' );
				}

				var applyGlobalPresets = $this.instance( '[name="et-core-portability-apply-presets"]' ).is( ':checked' );

				if ( returnJSON ) {
					if (0 === $( '#posts-filter [name="post[]"]' ).length) {
						$this.renderNoItemsToExportError();

						// Stop the export process.
						return;
					}

					$this.ajaxAction( {
						action: 'et_core_portability_export',
						content: content,
						selection: $.isEmptyObject( posts ) ? false : JSON.stringify( posts ),
						apply_global_presets: applyGlobalPresets,
						nonce: $this.nonces.export,
						return: true,
					}, function( response ) {
						$this.toggleCancel();
						window.et_export_layout_response = response.data;

						if ( 0 === window.et_export_layout_response.data.length ) {
							$this.renderNoItemsToExportError();

							// Stop the export process.
							return;
						}

						$('#et-cloud-app--layouts').empty();

						var preferences = {
							containerId: 'et-cloud-app--layouts',
							context: 'et_pb_layouts',
							codeMirrorId: '#',
							modalType: 'headless',
							content: '',
							selectedContent: '',
						};
						var container   = window.document;

						$this.removeProgressBar();

						$(window).trigger('et_code_snippets_container_ready', [preferences, container]);
					} );
				} else {
					$this.ajaxAction( {
						action: 'et_core_portability_export',
						content: content,
						selection: $.isEmptyObject( posts ) ? false : JSON.stringify( posts ),
						apply_global_presets: applyGlobalPresets,
						nonce: $this.nonces.export
					}, function( response ) {
						var time        = ' ' + new Date().toJSON().replace( 'T', ' ' ).replace( ':', 'h' ).substring( 0, 16 );
						var downloadURL = $this.instance( '[data-et-core-portability-export]' ).data( 'et-core-portability-export' );
						var query       = {
								'timestamp': response.data.timestamp,
								'name': encodeURIComponent( $this.instance( '.et-core-portability-export-form input' ).val() + ( backup ? time : '' ) ),
							};

						$.each( query, function( key, value ) {
							if ( value ) {
								downloadURL = downloadURL + '&' + key + '=' + value;
							}
						} );

						// Remove confirmation popup before relocation.
						$( window ).off( 'beforeunload' );

						window.location.assign( encodeURI( downloadURL ) );

						if ( ! backup ) {
							etCore.modalContent( '<div class="et-core-loader et-core-loader-success"></div>', false, 3000, '#et-core-portability-export' );
							$this.toggleCancel();
						}

						$( $this ).trigger( 'exported' );
					} );
				}
			} );
		},

		exportToCloud: function() {
			var $this = this;

			$this.export( false, true );
		},

    exportFB: function(exportUrl, postId, content, fileName, importFile, page, timestamp, progress = 0, estimation = 1, layoutId = 0) {
      var $this     = this;
      var context   = layoutId !== 0 ? 'et_builder_layouts' : 'et_builder';
      var selection = layoutId !== 0 ? JSON.stringify({'id': layoutId}) : false;

      // Trigger event which updates VB-UI's progress bar
      window.et_fb_export_progress   = progress;
      window.et_fb_export_estimation = estimation;

      var exportEvent = document.createEvent('Event');
      exportEvent.initEvent('et_fb_layout_export_in_progress', true, true);
      window.dispatchEvent(exportEvent);

      page = typeof page === 'undefined' ? 1 : page;

      $.ajax({
        type: 'POST',
        url: etCore.ajaxurl,
        dataType: 'json',
        data: {
          action: 'et_core_portability_export',
          content: content.shortcode,
          global_presets: content.global_presets,
          global_colors: content.global_colors,
          timestamp: timestamp !== undefined ? timestamp : 0,
          nonce: $this.nonces.export,
          post: postId,
          context: context,
          selection: selection,
          page: page,
        },
        success: function(response) {
          var errorEvent = document.createEvent('Event');

          errorEvent.initEvent('et_fb_layout_export_error', true, true);

          // The error is unknown but most of the time it would be cased by the server max size being exceeded.
          if ('string' === typeof response && '0' === response) {
            window.et_fb_export_layout_message = $this.text.maxSizeExceeded;
            window.dispatchEvent(errorEvent);

            return;
          }
          // Memory size set on server is exhausted.
          else if ('string' === typeof response && response.toLowerCase().indexOf('memory size') >= 0) {
            window.et_fb_export_layout_message = $this.text.memoryExhausted;
            window.dispatchEvent(errorEvent);
            return;
          }
          // Paginate.
          else if ('undefined' !== typeof response.page) {
            if ($this.cancelled) {
              return;
            }

            // Update progress bar
            var updatedProgress   = Math.ceil((response.page * 100) / response.total_pages);
            var updatedEstimation = Math.ceil(((response.total_pages - response.page) * 6) / 60);

            // If progress param isn't empty, updated progress should continue from it
            // because before exportFB(), shortcode should've been prepared via another
            // ajax request first
            if (0 < progress) {
              const remainingProgress = (100 - updatedProgress) / 100;
              updatedProgress = (updatedProgress * remainingProgress) + progress;
            }

            // Update global variables
            window.et_fb_export_progress   = updatedProgress;
            window.et_fb_export_estimation = updatedEstimation;

            // Dispatch event to trigger UI update
            window.dispatchEvent(exportEvent);

            return $this.exportFB(
              exportUrl,
              postId,
              content,
              fileName,
              importFile,
              (page + 1),
              response.timestamp,
              updatedProgress,
              updatedEstimation,
              layoutId
            );
          } else if ('undefined' !== typeof response.data && 'undefined' !== typeof response.data.message) {
            window.et_fb_export_layout_message = $this.text[response.data.message];
            window.dispatchEvent(errorEvent);
            return;
          } else if (false === response.success) {
            window.dispatchEvent(errorEvent);
            return;
          }

          var time = ' ' + new Date().toJSON().replace('T', ' ').replace(':', 'h').substring(0, 16),
            downloadURL = exportUrl,
            query = {
              'timestamp': response.data.timestamp,
              'name': '' !== fileName ? fileName : encodeURIComponent(time),
            };

          $.each(query, function(key, value) {
            if (value) {
              downloadURL = downloadURL + '&' + key + '=' + value;
            }
          });

          // Remove confirmation popup before relocation.
          $(window).off('beforeunload');

          // Update progress bar's global variables
          window.et_fb_export_progress = 100;
          window.et_fb_export_estimation = 0;

          // Dispatch event to trigger UI update
          window.dispatchEvent(exportEvent);
          window.location.assign(encodeURI(downloadURL));

          // perform import if needed
          if (typeof importFile !== 'undefined') {
            $this.importFB(importFile, postId);
          } else {
            var event = document.createEvent('Event');

            event.initEvent('et_fb_layout_export_finished', true, true);

            // trigger event to communicate with FB
            window.dispatchEvent(event);
          }
        }
      });
    },

		importFB: function(file, postId, options) {
			var $this      = this;
			var errorEvent = document.createEvent( 'Event' );

			window.et_fb_import_progress = 0;
			window.et_fb_import_estimation = 1;

			errorEvent.initEvent( 'et_fb_layout_import_error', true, true );

			if ( undefined === window.FormData ) {
				window.et_fb_import_layout_message = this.text.browserSupport;
				window.dispatchEvent( errorEvent );
				return;
			}

			if ( ! $this.validateImportFile( file, true ) ) {
				window.et_fb_import_layout_message = this.text.invalideFile;
				window.dispatchEvent( errorEvent );
				return;
			}

			if ('undefined' === typeof options) {
				options = {};
			}

			options = $.extend({
				replace: false,
				context: 'et_builder',
				returnJson: false,
				useTempPresets: false,
				includeGlobalPresets: false,
				onboarding: false,
			}, options);

			var fileSize = Math.ceil( ( file.size / ( 1024 * 1024 ) ).toFixed( 2 ) ),
				formData = new FormData(),
				requestData = {
					action: 'et_core_portability_import',
					include_global_presets: options.includeGlobalPresets,
					et_cloud_return_json: options.returnJson,
					et_cloud_use_temp_presets: options.useTempPresets,
					file: file,
					content: false,
					timestamp: 0,
					nonce: $this.nonces.import,
					post: postId,
					replace: options.replace ? '1' : '0',
					onboarding: options.onboarding ? '1' : '0',
					context: options.context
				};

			/**
			 * Max size set on server is exceeded.
			 *
			 * 0 indicating "unlimited" according to php specs
			 * https://www.php.net/manual/en/ini.core.php#ini.post-max-size
			 **/
			if (
				( 0 > $this.postMaxSize && fileSize >= $this.postMaxSize )
				|| ( 0 > $this.uploadMaxSize && fileSize >= $this.uploadMaxSize )
			) {
				window.et_fb_import_layout_message = this.text.maxSizeExceeded;
				window.dispatchEvent( errorEvent );
				return;
			}

			$.each(requestData, function(name, value) {
				if ('file' === name) {
				  // Explicitly set the file name.
				  // Otherwise it'll be set to 'Blob' in case of Blob type, but we need actual filename here.
				  formData.append('file', value, value.name);
				} else {
				  formData.append(name, value);
				}
			});

			var importFBAjax = function( importData ) {
				return $.ajax( {
					type: 'POST',
					url: etCore.ajaxurl,
					processData: false,
					contentType: false,
					data: formData,
					success: function( response ) {
						var event = document.createEvent( 'Event' );

						event.initEvent( 'et_fb_layout_import_in_progress', true, true );

						// Handle known error
						if ( ! response.success && 'undefined' !== typeof response.data && 'undefined' !== typeof response.data.message && 'undefined' !== typeof $this.text[ response.data.message ] ) {
							window.et_fb_import_layout_message = $this.text[ response.data.message ];
							window.dispatchEvent( errorEvent );
						}
						// The error is unknown but most of the time it would be cased by the server max size being exceeded.
						else if ( 'string' === typeof response && ('0' === response || '' === response) ) {
							window.et_fb_import_layout_message = $this.text.maxSizeExceeded;
							window.dispatchEvent( errorEvent );

							return;
						}
						// Memory size set on server is exhausted.
						else if ( 'string' === typeof response && response.toLowerCase().indexOf( 'memory size' ) >= 0 ) {
							window.et_fb_import_layout_message = $this.text.memoryExhausted;
							window.dispatchEvent( errorEvent );

							return;
						}
						// Pagination
						else if ( 'undefined' !== typeof response.page && 'undefined' !== typeof response.total_pages ) {
							// Update progress bar
							var progress = Math.ceil( ( response.page * 100 ) / response.total_pages );
							var estimation = Math.ceil( ( ( response.total_pages - response.page ) * 6 ) / 60 );

							window.et_fb_import_progress = progress;
							window.et_fb_import_estimation = estimation;

							// Import data
							var nextImportData = importData;
							nextImportData.append( 'page', ( parseInt(response.page) + 1 ) );
							nextImportData.append( 'timestamp', response.timestamp );
							nextImportData.append( 'file', null );

							importFBAjax( nextImportData );

							// trigger event to communicate with FB
							window.dispatchEvent( event );
						} else {
							// Update progress bar
							window.et_fb_import_progress = 100;
							window.et_fb_import_estimation = 0;

							// trigger event to communicate with FB
							window.dispatchEvent( event );

							// Allow some time for animations to animate
							setTimeout( function() {
								var event = document.createEvent( 'Event' );

								event.initEvent( 'et_fb_layout_import_finished', true, true );

								// save the data into global variable for later use in FB
								window.et_fb_import_layout_response = response;

								// trigger event to communicate with FB (again)
								window.dispatchEvent( event );
							}, 1300 );
						}
					}
				} );
			};

			return importFBAjax(formData)
		},

		bulkImportFB: async function(files, postId, options) {
			var $this        = this;
			var errorEvent   = document.createEvent( 'Event' );
			var importCount  = 0, totalFiles = files.length;
			var importRequests = [];
			var totalPromises = 0;

			var watchImportPromises = function() {
				totalPromises += 1;
				var pendingPromisesLength = importRequests.length;
				Promise.allSettled(importRequests).then(function(responses) {
					if (totalPromises > pendingPromisesLength) {
						return;
					}

					var success = responses.some(function(response) {
						return 'fulfilled' === response.status;
					});
	
					if (success) {
						var successResponse = [];
						
						for (var i = 0; i < responses.length; i++) {
							if ('fulfilled' === responses[i].status && responses[i].value.success) {
								successResponse.push(responses[i].value);
							}	
						}
	
						// Allow some time for animations to animate
						setTimeout(function() {
							var event = document.createEvent( 'Event' );
							event.initEvent( 'et_fb_layout_import_finished', true, true );
							// save the data into global variable for later use in FB
							window.et_fb_import_layout_response = successResponse;
	
							// trigger event to communicate with FB (again)
							window.dispatchEvent( event );
						}, 1300);
					} else {
						// Undo import porgress,
						var event = document.createEvent( 'Event' );
						event.initEvent( 'et_fb_layout_import_in_progress', true, true );
						window.et_fb_import_progress = 99;
						window.dispatchEvent( event );
	
						var importErrorMessages = responses.map(function(value) {
							return value.reason;
						});
	
						window.et_fb_import_layout_message = importErrorMessages;
						window.dispatchEvent( errorEvent );
					}
				});
			};

			var importFBAjax = function(importData) {
				var promise = new Promise(function(resolve, reject) {
					var jqXHR = $.ajax( {
						type: 'POST',
						url: etCore.ajaxurl,
						processData: false,
						contentType: false,
						data: importData,
					});

					jqXHR.done(function(response) {
						var event = document.createEvent( 'Event' );
						event.initEvent( 'et_fb_layout_import_in_progress', true, true );
						var importFile = importData.get('file');

						// Handle known error
						if ( ! response.success && 'undefined' !== typeof response.data && 'undefined' !== typeof response.data.message && 'undefined' !== typeof $this.text[ response.data.message ] ) {
							reject({file: importFile, error: $this.text[ response.data.message ]});
							importCount++;
						}
						// The error is unknown but most of the time it would be cased by the server max size being exceeded.
						else if ( 'string' === typeof response && ('0' === response || '' === response) ) {
							reject({file: importFile.name, error: $this.text.maxSizeExceeded});
							importCount++;
						}
						// Memory size set on server is exhausted.
						else if ( 'string' === typeof response && response.toLowerCase().indexOf( 'memory size' ) >= 0 ) {
							resolve({importFile, error: $this.text.memoryExhausted});
							importCount++;

						}
						// Pagination
						else if ( 'undefined' !== typeof response.page && 'undefined' !== typeof response.total_pages ) {
							// Import data
							var nextImportData = importData;
							nextImportData.append( 'page', ( parseInt(response.page) + 1 ) );
							nextImportData.append( 'timestamp', response.timestamp );
							nextImportData.append( 'file', null );
							importFBAjax(nextImportData);
							return resolve(response);
						} else {
							resolve(response);
							importCount++;
						}

						// Update progress bar
						window.et_fb_import_progress = (importCount/totalFiles) * 100;
						window.et_fb_import_estimation = 0;
						// trigger event to communicate with FB
						window.dispatchEvent( event );
					}).fail(function(error) {
						reject(error);
					});
				});
				importRequests.push(promise);
				watchImportPromises();
			};

			window.et_fb_import_progress = 0;
			window.et_fb_import_estimation = 1;

			errorEvent.initEvent( 'et_fb_layout_import_error', true, true );

			if ( undefined === window.FormData ) {
				window.et_fb_import_layout_message = this.text.browserSupport;
				window.dispatchEvent( errorEvent );
				return;
			}

			if ('undefined' === typeof options) {
				options = {};
			}

			options = $.extend({
				replace: false,
				context: 'et_builder_layouts',
				returnJson: false,
				useTempPresets: false,
				includeGlobalPresets: false,
			}, options);

			for (var i = 0; i < files.length; i++) {
				var file = await $this.formatBuilderLayoutFile(files[i]),
				fileSize = Math.ceil( ( file.size / ( 1024 * 1024 ) ).toFixed( 2 ) ),
				formData = new FormData(),
				requestData = {
					action: 'et_core_portability_import',
					include_global_presets: options.includeGlobalPresets,
					et_cloud_return_json: options.returnJson,
					et_cloud_use_temp_presets: options.useTempPresets,
					file: file,
					content: false,
					timestamp: 0,
					nonce: $this.nonces.import,
					post: postId,
					replace: options.replace ? '1' : '0',
					context: options.context
				};

				if (
					( 0 > $this.postMaxSize && fileSize >= $this.postMaxSize )
					|| ( 0 > $this.uploadMaxSize && fileSize >= $this.uploadMaxSize )
				) {
					continue;
				}


				$.each(requestData, function(name, value) {
					if ('file' === name) {
					// Explicitly set the file name.
					// Otherwise it'll be set to 'Blob' in case of Blob type, but we need actual filename here.
						formData.append('file', value, value.name);
					} else {
						formData.append(name, value);
					}
				});

				importFBAjax(formData);
			}

		},

		importPresetsAsDefault: function( presets, presetPrefix = '' , globalColors = null, images = null ) {
			const $this = this;

			return new Promise((resolve) => {
				$.ajax({
					type    : 'POST',
					url     : etCore.ajaxurl,
					dataType: 'json',
					data    : {
						action      : 'et_core_portability_import_default_presets',
						nonce       : $this.nonces.presets,
						presetPrefix: presetPrefix,
						presets     : JSON.stringify(presets),
						globalColors: globalColors ? JSON.stringify(globalColors) : null,
						images      : images ? JSON.stringify(images) : null,
					},
					complete: () => {
						resolve(true);
					},
				});
			});
		},

		importCustomizerSettings: function( settingsFile ) {
			const $this = this;

			return new Promise((resolve) => {
				$this.ajaxAction( {
					action : 'et_core_portability_import',
					context: 'et_divi_mods',
					file   : settingsFile,
					nonce  : $this.nonces.import,
				}, function() {}, true );

				return resolve(true);
			});
		},

		ajaxAction: function( data, callback, fileSupport ) {
			var $this = this;

			// Reset cancelled.
			this.cancelled = false;

			data = $.extend( {
				nonce: $this.nonce,
				file: null,
				content: false,
				timestamp: 0,
				post: $( '#post_ID' ).val(),
				context: $this.instance().data( 'et-core-portability' ),
				page: 1,
			}, data );

			var	ajax = {
				type: 'POST',
				url: etCore.ajaxurl,
				data: data,
				success: function( response ) {
					// The error is unknown but most of the time it would be caused by the server max size being exceeded.
					if ( 'string' === typeof response && '0' === response ) {
						etCore.modalContent( '<p>' + $this.text.maxSizeExceeded + '</p>', false, true, '#' + $this.instance( '.ui-tabs-panel:visible' ).attr( 'id' ) );

						$this.enableActions();

						return;
					}
					// Memory size set on server is exhausted.
					else if ( 'string' === typeof response && response.toLowerCase().indexOf( 'memory size' ) >= 0 ) {
						etCore.modalContent( '<p>' + $this.text.memoryExhausted + '</p>', false, true, '#' + $this.instance( '.ui-tabs-panel:visible' ).attr( 'id' ) );

						$this.enableActions();

						return;
					}
					// Paginate.
					else if ( 'undefined' !== typeof response.page ) {
						var progress = Math.ceil( ( response.page * 100 ) / response.total_pages );

						if ( $this.cancelled ) {
							return;
						}

						$this.toggleCancel( true );

						$this.ajaxAction( $.extend( data, {
							page: parseInt( response.page ) + 1,
							timestamp: response.timestamp,
							file: null,
						} ), callback, false );

						$this.instance( '.et-core-progress-bar' )
							.width( progress + '%' )
							.text( progress + '%' );

						$this.instance( '.et-core-progress-subtext span' ).text( Math.ceil( ( ( response.total_pages - response.page ) * 6 ) / 60 ) );

						return;
					} else if ( 'undefined' !== typeof response.data && 'undefined' !== typeof response.data.message ) {
						etCore.modalContent( '<p>' + $this.text[response.data.message] + '</p>', false, 3000, '#' + $this.instance( '.ui-tabs-panel:visible' ).attr( 'id' ) );

						$this.enableActions();

						return;
					}

					// Timestamp when AJAX response is received
					var ajax_returned_timestamp = new Date().getTime();

					// Animate Progresss Bar
					var animateCoreProgressBar = function( DOMHighResTimeStamp ) {
						// Check has been performed for 3s and progress bar DOM still can't be found, consider it fail to avoid infinite loop
						var current_timestamp = new Date().getTime();
						if ((current_timestamp - ajax_returned_timestamp) > 3000) {
							$this.enableActions();
							etCore.modalContent( '<div class="et-core-loader et-core-loader-fail"></div>', false, 3000, '#' + $this.instance( '.ui-tabs-panel:visible' ).attr( 'id' ) );
							return;
						}

						// Check if core progress DOM exists
						if ($this.instance( '.et-core-progress' ).length ) {
							$this.instance( '.et-core-progress' )
								.removeClass( 'et-core-progress-striped' )
								.find( '.et-core-progress-bar' ).width( '100%' )
								.text( '100%' )
								.delay( 1000 )
								.queue( function() {

									$this.enableActions();

									if ( 'undefined' === typeof response.data || ( 'undefined' !== typeof response.data && ! response.data.timestamp ) ) {
										etCore.modalContent( '<div class="et-core-loader et-core-loader-fail"></div>', false, 3000, '#' + $this.instance( '.ui-tabs-panel:visible' ).attr( 'id' ) );
										return;
									}

									$( this ).dequeue();

									callback( response );
								} );
						} else {
							// Recheck on the next animation frame
							window.requestAnimationFrame(animateCoreProgressBar);
						}
					}
					animateCoreProgressBar();
				}
			};

			if ( fileSupport ) {
				var fileSize = Math.ceil( ( data.file.size / ( 1024 * 1024 ) ).toFixed( 2 ) ),
					formData = new FormData();

				/**
				 * Max size set on server is exceeded.
				 *
				 * 0 indicating "unlimited" according to php specs
				 * https://www.php.net/manual/en/ini.core.php#ini.post-max-size
				 **/
				if (
					( 0 > $this.postMaxSize && fileSize >= $this.postMaxSize )
					|| ( 0 > $this.uploadMaxSize && fileSize >= $this.uploadMaxSize )
				) {
					etCore.modalContent( '<p>' + $this.text.maxSizeExceeded + '</p>', false, true, '#' + $this.instance( '.ui-tabs-panel:visible' ).attr( 'id' ) );

					$this.enableActions();

					return;
				}

				$.each( ajax.data, function( name, value ) {
					formData.append( name, value);
				} );

				ajax = $.extend( ajax, {
					data: formData,
					processData: false,
					contentType : false,
				} );
			}

			$.ajax( ajax );
		},

		// This function should be overwritten for options portability type to make sure data are saved before exporting.
		save: function( callback ) {
			if ( 'undefined' !== typeof wp && 'undefined' !== typeof wp.customize ) {
				var saveCallback = function() {
					callback();
					wp.customize.unbind( 'saved', saveCallback );
				}

				$('#save').trigger('click');

				wp.customize.bind( 'saved', saveCallback );
			} else {
				// Add a slight delay for animation purposes.
				setTimeout( function() {
					callback();
				}, 1000 )
			}
		},

		addProgressBar: function( message ) {
			etCore.modalContent( '<div class="et-core-progress et-core-progress-striped et-core-active"><div class="et-core-progress-bar" style="width: 10%;">1%</div><span class="et-core-progress-subtext">' + message + '</span></div>', false, false, '#' + this.instance( '.ui-tabs-panel:visible' ).attr( 'id' ) );
		},

		removeProgressBar: function() {
			const $overlay = $('.et-core-modal-overlay.et-core-active');
			$overlay.find('.et-core-modal-temp-content').remove();
			$overlay.find('[name="et-core-portability-posts"]').prop('checked', false);
			$overlay.find('.et-core-modal-content').removeAttr('style');
		},

		actionsDisabled: function() {
			if ( this.instance( '.et-core-modal-action' ).hasClass( 'et-core-disabled' ) ) {
				return true;
			}

			return false;
		},

		disableActions: function() {
			this.instance( '.et-core-modal-action' ).addClass( 'et-core-disabled' );
		},

		enableActions: function() {
			this.instance( '.et-core-modal-action' ).removeClass( 'et-core-disabled' );
		},

		toggleCancel: function( cancel ) {
			var $target = this.instance( '.ui-tabs-panel:visible [data-et-core-portability-cancel]' );

			var $duringAction = this.instance('.et-core-action-buttons-container__during_action');

			if ( cancel && ! $target.is( ':visible' ) ) {
				$target.show().animate( { opacity: 1 }, 600, 'swing', function() {
					$duringAction.show();
				} );
			} else if ( ! cancel && $target.is( ':visible' ) ) {
				$target.animate( { opacity: 0 }, 600, function() {
					$target.hide();
					$duringAction.hide();
				} );
			}
		},

		cancel: function( cancel ) {
			this.cancelled = true;

			// Remove all temp files. Set a delay as temp files might still be in the process of being added.
			setTimeout( function() {
				$.ajax( {
					type: 'POST',
					url: etCore.ajaxurl,
					data: {
						nonce: this.nonces.cancel,
						context: this.instance().data( 'et-core-portability' ),
						action: 'et_core_portability_cancel',
					}
				} );
			}.bind( this ), 3000 );
			etCore.modalContent( '<div class="et-core-loader et-core-loader-success"></div>', false, 3000, '#' + this.instance( '.ui-tabs-panel:visible' ).attr( 'id' ) );
			this.toggleCancel();
			this.enableActions();
		},

		instance: function( element ) {
			return $( '.et-core-active[data-et-core-portability]' + ( element ? ' ' + element : '' ) );
		},

		formatBuilderLayoutFile: function(file) {
			const reader = new FileReader();

			return new Promise((resolve, reject) => {
				reader.onloadend = (e) => {
					var content = '';
					try {
						content  = JSON.parse(e.target.result);
					} catch (e) {
						const importFile = new File([JSON.stringify({})], file.name, { type: 'application/json' });
						return resolve(importFile);
					}

					if('et_builder' === content.context) {
						const name        = file.name.replace('.json', '');
						const postId      = Object.keys(content.data)[0];
						const postContent = content.data[postId];

						const convertedFile = {
						...content,
						context: 'et_builder_layouts',
						data: {
							[postId]: {
							ID: parseInt(postId, 10),
							post_title: name,
							post_name: name,
							post_content: postContent,
							post_excerpt: '',
							post_status: 'publish',
							comment_status: 'closed',
							ping_status: 'closed',
							post_type: 'et_pb_layout',
							post_meta: {
								_et_pb_built_for_post_type: ['page']
							},
							terms: {
								1: {
								name: 'layout',
								slug: 'layout',
								taxonomy: 'layout_type',
								},
							},
							},
						}
						}

						const importFile = new File([JSON.stringify(convertedFile)], file.name, { type: 'application/json' });
						resolve(importFile);
					} else {
						resolve(file);
					}
				}
	
				reader.onerror = () => {
					reader.abort();
					reject();
				};
				
				reader.readAsText(file);
			});
		  }

	} );

	$(function() {
		window.etCore.portability.boot();
	});

	$(window).on('et_export_to_cloud_cancel', function() {
		etCore.modalContent( '<div class="et-core-loader et-core-loader-fail"></div>', false, 3000, '#et-core-portability-export' );

		window.etCore.portability.toggleCancel();
	});

	if ('undefined' !== typeof pagenow && 'edit-et_pb_layout' === pagenow) {
		$(window).on('et_code_snippets_library_close', function() {
			var preferences = {
				containerId: 'et-cloud-app--layouts',
				context: 'et_pb_layouts',
				codeMirrorId: '#',
				modalType: '',
				content: '',
				selectedContent: '',
			};
			var container   = window.document;

			$(window).trigger('et_code_snippets_container_ready', [preferences, container]);

			etCore.modalContent( '', false, 0, '#et-core-portability-export' );
			$('.et-core-modal-close:visible').click();
		});
	}
})( jQuery );
