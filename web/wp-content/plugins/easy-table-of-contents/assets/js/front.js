jQuery( function( $ ) {

	/**
	 * @typedef ezTOC
	 * @type {Object} ezTOC
	 * @property {string} affixSelector
	 * @property {string} scroll_offset
	 * @property {string} smooth_scroll
	 * @property {string} visibility_hide_by_default
	 */

	if ( typeof ezTOC != 'undefined' ) {

		/**
		 * Init EZ TOC.
		 */
		function ezTOCInit() {

		var affix = $( '.ez-toc-widget-container.ez-toc-affix' );

		if ( 0 !== affix.length ) {

			/**
			 * The smooth scroll offset needs to be taken into account when defining the offset_top property.
			 * @link https://github.com/shazahm1/Easy-Table-of-Contents/issues/19
			 *
			 * @type {number}
			 */
			var affixOffset = 30;

			// check offset setting
			if ( typeof ezTOC.scroll_offset != 'undefined' ) {

				affixOffset = parseInt( ezTOC.scroll_offset );
			}

			$( ezTOC.affixSelector ).stick_in_parent( {
				inner_scrolling: false,
				offset_top:      affixOffset
			} )
		}

		$.fn.shrinkTOCWidth = function() {

			$( this ).css( {
				width:   'auto',
				display: 'table'
			});

			if ( /MSIE 7\./.test( navigator.userAgent ) )
				$( this ).css( 'width', '' );
		};


		if ( typeof ezTOC.visibility_hide_by_default != 'undefined' ) {

			// Get all toggles that have not been loaded.
			var toggles = $( '.ez-toc-toggle:not(.ez-toc-loaded),.ez-toc-widget-sticky-toggle:not(.ez-toc-loaded)' ); 

			var invert = ezTOC.visibility_hide_by_default;

                        $.each(toggles, function(i, obj) {
                            
                            var toggle = $(this);
                            $(toggle).addClass('ez-toc-loaded'); // Attach loaded class.
                            var toc = $( toggle ).parents('#ez-toc-container,#ez-toc-widget-container,#ez-toc-widget-sticky-container').find( 'ul.ez-toc-list,ul.ez-toc-widget-sticky-list' );
                            if($(toc).hasClass('eztoc-toggle-hide-by-default')){
                                invert = 1;
                            }                                
                            if (typeof Cookies !== "undefined") {
                                if ( Cookies ) {

                                        Cookies.get( 'ezTOC_hidetoc-' + i ) == 1 ? $(toggle).data( 'visible', false ) : $(toggle).data( 'visible', true );
                                        Cookies.remove('ezTOC_hidetoc-' + i)

                                } else {

                                        $(toggle).data( 'visible', true );
                                        Cookies.remove('ezTOC_hidetoc-' + i);
                                }
                            }

                            if ( invert ) {

                                    $(toggle).data( 'visible', false )
                            }

                            if ( ! $(toggle).data( 'visible' ) ) {

                                    toc.hide();
                            }

                            $(toggle).on( 'click', function( event ) {

                                    event.preventDefault();

                                    const main = document.querySelector("#ez-toc-container");
                                    if(main){
                                            main.classList.toggle("toc_close");
                                    }
                                    else
                                    {
                                            const side = document.querySelector(".ez-toc-widget-container,.ez-toc-widget-sticky-container");
                                            side.classList.toggle("toc_close");					
                                    }

                                    if ( $( this ).data( 'visible' ) ) {

                                            $( this ).data( 'visible', false );
                                            if (typeof Cookies !== "undefined") {
                                                if ( Cookies ) {

                                                        if ( invert )
                                                                Cookies.set( 'ezTOC_hidetoc-' + i, null, { path: '/' } );
                                                        else
                                                                Cookies.set( 'ezTOC_hidetoc-' + i, '1', { expires: 30, path: '/' } );
                                                }
                                            }

                                            toc.hide( 'fast' );

                                    } else {

                                            $( this ).data( 'visible', true );
                                            if (typeof Cookies !== "undefined") {
                                                if ( Cookies ) {

                                                        if ( invert )
                                                                Cookies.set( 'ezTOC_hidetoc-' + i, '1', { expires: 30, path: '/' } );
                                                        else
                                                                Cookies.set( 'ezTOC_hidetoc-' + i, null, { path: '/' } );
                                                }
                                            }

                                            toc.show( 'fast' );

                                    }

                            } );
                        
                        });
		}


        // ======================================
        // Set active heading in ez-toc-widget list
        // ======================================

        var headings = $( 'span.ez-toc-section' ).toArray();
        var headingToListElementLinkMap = getHeadingToListElementLinkMap( headings );
        var listElementLinks = $.map( headingToListElementLinkMap, function ( value, key ) {
            return value
        } );
        var scrollOffset = getScrollOffset();

        activateSetActiveEzTocListElement();

        function setActiveEzTocListElement() {
            var activeHeading = getActiveHeading( scrollOffset, headings );
            if ( activeHeading ) {
                var activeListElementLink = headingToListElementLinkMap[ activeHeading.id ];
                removeStyleFromNonActiveListElement( activeListElementLink, listElementLinks );
                setStyleForActiveListElementElement( activeListElementLink );
            }
        }

        function activateSetActiveEzTocListElement() {
            if ( headings.length > 0 && $('.ez-toc-widget-container').length) {
                $( window ).on( 'load resize scroll', setActiveEzTocListElement );
            }
        }

        function deactivateSetActiveEzTocListElement() {
            $( window ).off( 'load resize scroll', setActiveEzTocListElement );
        }

        function getEzTocListElementLinkByHeading( heading ) {
            return $( '.ez-toc-widget-container .ez-toc-list a[href="#' + $( heading ).attr( 'id' ) + '"]' );
        }

        function getHeadingToListElementLinkMap( headings ) {
            return headings.reduce( function ( map, heading ) {
                map[ heading.id ] = getEzTocListElementLinkByHeading( heading );
                return map;
            }, {} );
        }

        function getScrollOffset() {
            var scrollOffset = 5; // so if smooth offset is off, the correct title is set as active
            if ( typeof ezTOC.smooth_scroll != 'undefined' && parseInt( ezTOC.smooth_scroll ) === 1 ) {
                scrollOffset = ( typeof ezTOC.scroll_offset != 'undefined' ) ? parseInt( ezTOC.scroll_offset ) : 30;
            }

            var adminbar = $( '#wpadminbar' );

            if ( adminbar.length ) {
                scrollOffset += adminbar.height();
            }
            return scrollOffset;
        }

        function getActiveHeading( topOffset, headings ) {
            var scrollTop = $( window ).scrollTop();
            var relevantOffset = scrollTop + topOffset + 1;
            var activeHeading = headings[ 0 ];
            var closestHeadingAboveOffset = relevantOffset - $( activeHeading ).offset().top;
            headings.forEach( function ( section ) {
                var topOffset = relevantOffset - $( section ).offset().top;
                if ( topOffset > 0 && topOffset < closestHeadingAboveOffset ) {
                    closestHeadingAboveOffset = topOffset;
                    activeHeading = section;
                }
            } );
            return activeHeading;
        }

        function removeStyleFromNonActiveListElement( activeListElementLink, listElementLinks ) {
            listElementLinks.forEach( function ( listElementLink ) {
                if ( activeListElementLink !== listElementLink && listElementLink.parent().hasClass( 'active' ) ) {
                    listElementLink.parent().removeClass( 'active' );
                }
            } );
        }

        function correctActiveListElementBackgroundColorHeight( activeListElement ) {
            var listElementHeight = getListElementHeightWithoutUlChildren( activeListElement );
            addListElementBackgroundColorHeightStyleToHead( listElementHeight );
        }

        function getListElementHeightWithoutUlChildren( listElement ) {
            var $listElement = $( listElement );
            var content = $listElement.html();
            // Adding list item with class '.active' to get the real height.
            // When adding a class to an existing element and using jQuery(..).height() directly afterwards,
            // the height is the 'old' height. The height might change due to text-wraps when setting the text-weight bold for example
            // When adding a new item, the height is calculated correctly.
            // But only when it might be visible (so display:none; is not possible...)
            // But because it get's directly removed afterwards it never will be rendered by the browser
            // (at least in my tests in FF, Chrome, IE11 and Edge)
            $listElement.parent().append( '<li id="ez-toc-height-test" class="active">' + content + '</li>' );
            var listItem = $( '#ez-toc-height-test' );
            var height = listItem.height();
	        listItem.remove();
            return height - ($listElement.children( 'ul' ).first().height() || 0);
        }

        function addListElementBackgroundColorHeightStyleToHead( listElementHeight ) {
            // Remove existing
            //$( '#ez-toc-active-height' ).remove();
            // jQuery(..).css(..) doesn't work, because ::before is a pseudo element and not part of the DOM
            // Workaround is to add it to head
           // $( '<style id="ez-toc-active-height">.ez-toc-widget-container ul.ez-toc-list li.active {height:' + listElementHeight + 'px;' + '} </style>' ).appendTo( 'head' );
		   $( '.ez-toc-widget-container ul.ez-toc-list li.active' ).css( 'height',listElementHeight + 'px' );
        }

        function setStyleForActiveListElementElement( activeListElementLink ) {
            var activeListElement = activeListElementLink.parent();
            if ( !activeListElement.hasClass( 'active' ) ) {
                activeListElement.addClass( 'active' );
            }
            correctActiveListElementBackgroundColorHeight( activeListElement );
        }
    }
    if($( '#ez-toc-container').length){
        if(!$( '#ez-toc-container .ez-toc-toggle label span').html()){
            $( '#ez-toc-container .ez-toc-toggle label').html(ezTOC.fallbackIcon);
        }
    }
        
    	/**
		 * Attach global init handler to ezTOC window object.
		 */
		ezTOC.init = function(){
			ezTOCInit();
		}
		// Start EZ TOC on page load.
		ezTOCInit();

        if ( typeof ezTOC.ajax_toggle != 'undefined' && parseInt( ezTOC.ajax_toggle ) === 1 ) {
            $( document ).ajaxComplete(function() {
                ezTOCInit();
            });
        }
        
	}
    $(document).on('click', '#ez-toc-open-sub-hd', function(e) {
        $(this).attr("id","ez-toc-open-sub-hd-active");
        e.preventDefault();
    });
    $(document).on('click', '#ez-toc-open-sub-hd-active', function(e) {
        $(this).attr("id","ez-toc-open-sub-hd");
        e.preventDefault();
    });    

    $("#ez-toc-more-links-enabler").click(function () { 
        $(".ez-toc-more-link").show();
        $("#ez-toc-more-links-enabler").hide();
        $("#ez-toc-more-links-disabler").attr("style","display:inline-block");
    });
    $("#ez-toc-more-links-disabler").click(function () { 
        $(".ez-toc-more-link").hide();
        $("#ez-toc-more-links-enabler").show();
        $("#ez-toc-more-links-disabler").hide();
    });

    if ( parseInt( ezTOC.chamomile_theme_is_on ) === 1 ) {

        $('#ez-toc-container').find('.hamburger').remove();
        
    }

} );