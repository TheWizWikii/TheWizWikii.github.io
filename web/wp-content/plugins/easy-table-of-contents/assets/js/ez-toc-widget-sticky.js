jQuery(function ($) {
    // ======================================
    // Set active heading in ez-toc-widget list
    // ======================================

    var ezTOCWidgetStickyheadings = $('span.ez-toc-section').toArray();
    var ezTOCWidgetStickyheadingToListElementLinkMap = ezTOCWidgetStickygetHeadingToListElementLinkMap(ezTOCWidgetStickyheadings);
    var ezTOCWidgetStickylistElementLinks = $.map(ezTOCWidgetStickyheadingToListElementLinkMap, function (value, key) {
        return value
    });
    var ezTOCWidgetStickyscrollOffset = ezTOCWidgetStickygetScrollOffset();

    ezTOCWidgetStickyactivateSetActiveEzTocListElement();

    function ezTOCWidgetStickysetActiveEzTocListElement() {
        var ezTOCWidgetStickyactiveHeading = ezTOCWidgetStickygetActiveHeading(ezTOCWidgetStickyscrollOffset, ezTOCWidgetStickyheadings);
        if (ezTOCWidgetStickyactiveHeading) {
            var ezTOCWidgetStickyactiveListElementLink = ezTOCWidgetStickyheadingToListElementLinkMap[ ezTOCWidgetStickyactiveHeading.id ];
            ezTOCWidgetStickyremoveStyleFromNonActiveListElement(ezTOCWidgetStickyactiveListElementLink, ezTOCWidgetStickylistElementLinks);
            ezTOCWidgetStickysetStyleForActiveListElementElement(ezTOCWidgetStickyactiveListElementLink);
        }
    }

    function ezTOCWidgetStickyactivateSetActiveEzTocListElement() {
        if (ezTOCWidgetStickyheadings.length > 0 && $('.ez-toc-widget-sticky-container').length) {
            $(window).on('load resize scroll', ezTOCWidgetStickysetActiveEzTocListElement);
        }
    }

    function ezTOCWidgetStickydeactivateSetActiveEzTocListElement() {
        $(window).off('load resize scroll', ezTOCWidgetStickysetActiveEzTocListElement);
    }

    function ezTOCWidgetStickygetEzTocListElementLinkByHeading(ezTOCWidgetStickyheading) {
        return $('.ez-toc-widget-sticky-container .ez-toc-widget-sticky-list a[href="#' + $(ezTOCWidgetStickyheading).attr('id') + '"]');
    }

    function ezTOCWidgetStickygetHeadingToListElementLinkMap(ezTOCWidgetStickyheadings) {
        return ezTOCWidgetStickyheadings.reduce(function (ezTOCWidgetStickymap, ezTOCWidgetStickyheading) {
            ezTOCWidgetStickymap[ ezTOCWidgetStickyheading.id ] = ezTOCWidgetStickygetEzTocListElementLinkByHeading(ezTOCWidgetStickyheading);
            return ezTOCWidgetStickymap;
        }, {});
    }

    function ezTOCWidgetStickygetScrollOffset() {
        var ezTOCWidgetStickyscrollOffset = 30; // so if smooth offset is off, the correct title is set as active
        if (typeof ezTOC != 'undefined' && typeof ezTOC.smooth_scroll != 'undefined' && parseInt(ezTOC.smooth_scroll) === 1) {
            ezTOCWidgetStickyscrollOffset = (typeof ezTOC.scroll_offset != 'undefined') ? parseInt(ezTOC.scroll_offset) : 30;
        }

        var adminbar = $('#wpadminbar');

        if (adminbar.length) {
            ezTOCWidgetStickyscrollOffset += adminbar.height();
        }
        return ezTOCWidgetStickyscrollOffset;
    }

    function ezTOCWidgetStickygetActiveHeading(ezTOCWidgetStickytopOffset, ezTOCWidgetStickyheadings) {
        var ezTOCWidgetStickyscrollTop = $(window).scrollTop();
        var ezTOCWidgetStickyrelevantOffset = ezTOCWidgetStickyscrollTop + ezTOCWidgetStickytopOffset + 1;
        var ezTOCWidgetStickyactiveHeading = ezTOCWidgetStickyheadings[ 0 ];
        var ezTOCWidgetStickyclosestHeadingAboveOffset = ezTOCWidgetStickyrelevantOffset - $(ezTOCWidgetStickyactiveHeading).offset().top;
        ezTOCWidgetStickyheadings.forEach(function (ezTOCWidgetStickysection) {
            var ezTOCWidgetStickytopOffset = ezTOCWidgetStickyrelevantOffset - $(ezTOCWidgetStickysection).offset().top;
            if (ezTOCWidgetStickytopOffset > 0 && ezTOCWidgetStickytopOffset < ezTOCWidgetStickyclosestHeadingAboveOffset) {
                ezTOCWidgetStickyclosestHeadingAboveOffset = ezTOCWidgetStickytopOffset;
                ezTOCWidgetStickyactiveHeading = ezTOCWidgetStickysection;
            }
        });
        return ezTOCWidgetStickyactiveHeading;
    }

    function ezTOCWidgetStickyremoveStyleFromNonActiveListElement(ezTOCWidgetStickyactiveListElementLink, ezTOCWidgetStickylistElementLinks) {
        ezTOCWidgetStickylistElementLinks.forEach(function (ezTOCWidgetStickylistElementLink) {
            if (ezTOCWidgetStickyactiveListElementLink !== ezTOCWidgetStickylistElementLink && ezTOCWidgetStickylistElementLink.parent().hasClass('active')) {
                ezTOCWidgetStickylistElementLink.parent().removeClass('active');
            }
        });
    }

    function ezTOCWidgetStickycorrectActiveListElementBackgroundColorHeight(ezTOCWidgetStickyactiveListElement) {
        var ezTOCWidgetStickylistElementHeight = ezTOCWidgetStickygetListElementHeightWithoutUlChildren(ezTOCWidgetStickyactiveListElement);
        ezTOCWidgetStickyaddListElementBackgroundColorHeightStyleToHead(ezTOCWidgetStickylistElementHeight);
    }

    function ezTOCWidgetStickygetListElementHeightWithoutUlChildren(ezTOCWidgetStickylistElement) {
        var $ezTOCWidgetStickylistElement = $(ezTOCWidgetStickylistElement);
        var ezTOCWidgetStickycontent = $ezTOCWidgetStickylistElement.html();
        // Adding list item with class '.active' to get the real height.
        // When adding a class to an existing element and using jQuery(..).height() directly afterwards,
        // the height is the 'old' height. The height might change due to text-wraps when setting the text-weight bold for example
        // When adding a new item, the height is calculated correctly.
        // But only when it might be visible (so display:none; is not possible...)
        // But because it get's directly removed afterwards it never will be rendered by the browser
        // (at least in my tests in FF, Chrome, IE11 and Edge)
        $ezTOCWidgetStickylistElement.parent().append('<li id="ez-toc-widget-sticky-height-test" class="active">' + ezTOCWidgetStickycontent + '</li>');
        var ezTOCWidgetStickylistItem = $('#ez-toc-widget-sticky-height-test');
        var ezTOCWidgetStickyheight = ezTOCWidgetStickylistItem.height();
        ezTOCWidgetStickylistItem.remove();
        return ezTOCWidgetStickyheight - $ezTOCWidgetStickylistElement.children('ul').first().height();
    }

    function ezTOCWidgetStickyaddListElementBackgroundColorHeightStyleToHead(ezTOCWidgetStickylistElementHeight) {
        // Remove existing
        $('#ez-toc-widget-sticky-active-height').remove();
        // jQuery(..).css(..) doesn't work, because ::before is a pseudo element and not part of the DOM
        // Workaround is to add it to head
        $('<style id="ez-toc-widget-sticky-active-height">' +
                '.ez-toc-widget-sticky-container ul.ez-toc-widget-sticky-list li.active {' +
                // 'line-heigh:' + listElementHeight + 'px; ' +
                'height:' + ezTOCWidgetStickylistElementHeight + 'px;' +
                '} </style>')
                .appendTo('head');
    }

    function ezTOCWidgetStickysetStyleForActiveListElementElement(ezTOCWidgetStickyactiveListElementLink) {
        var ezTOCWidgetStickyactiveListElement = ezTOCWidgetStickyactiveListElementLink.parent();
        if (!ezTOCWidgetStickyactiveListElement.hasClass('active')) {
            ezTOCWidgetStickyactiveListElement.addClass('active');
        }
        ezTOCWidgetStickycorrectActiveListElementBackgroundColorHeight(ezTOCWidgetStickyactiveListElement);
    }

    /**
     * EzTOC Widget Dynamic Scrolling JS
     * 
     * @since 2.0.41
     */

    setTimeout(function () {
        jQuery(window).on('load resize scroll', ezTOCWidgetStickysetScrollActiveEzTocListElement);
    }, 100);

    var ezTocActiveList = '';
    function ezTOCWidgetStickysetScrollActiveEzTocListElement(e) {
        e.preventDefault();

        if (jQuery(document).width() > 980 && jQuery(window).scrollTop() >= ezTocWidgetSticky.scroll_fixed_position && (jQuery('.post,.post-content').length == 0 || jQuery('.post,.post-content').length > 0 && jQuery(window).scrollTop() <= jQuery('.post,.post-content').height())) {
            jQuery('.ez-toc-widget-sticky').css({
                'position': 'fixed',
                'width': (ezTocWidgetSticky.sidebar_width != 'auto') ? ezTocWidgetSticky.sidebar_width + '' + ezTocWidgetSticky.sidebar_width_size_unit : $('#ez-toc-widget-sticky-container').parents('.widget-area').width(),
                'top': (ezTocWidgetSticky.fixed_top_position != '30') ? ezTocWidgetSticky.fixed_top_position + '' + ezTocWidgetSticky.fixed_top_position_size_unit : '30px',
                'z-index': '9999999',
                'background-color': jQuery(document).find('body').css("background-color"),
            });
            jQuery('.ez-toc-widget-sticky nav').css({
                'overflow-y': (ezTocWidgetSticky.navigation_scroll_bar == 'on') ? 'auto' : 'hidden',
                'max-height': (ezTocWidgetSticky.scroll_max_height != 'auto') ? ezTocWidgetSticky.scroll_max_height + '' + ezTocWidgetSticky.scroll_max_height_size_unit : 'calc(100vh - 111px)'
            });
        } else {
            jQuery('.ez-toc-widget-sticky,.ez-toc-widget-sticky nav').attr('style', false);
        }
        var ezTocHrefActive = jQuery("#ez-toc-widget-sticky-container li.active a").attr('href');
        var ezTocLastChild = "#ez-toc-widget-sticky-container nav>ul>li:last-child a";
        let ezTocOffsetTopDynamic = Math.round(jQuery("#ez-toc-widget-sticky-container .ez-toc-link[href='" + ezTocHrefActive + "']").position().top);
        var ezTocLastChildTop = Math.round(jQuery(ezTocLastChild).position().top);
        if (ezTocHrefActive != ezTocActiveList) {
            jQuery('.ez-toc-widget-sticky nav').scrollTop(Math.round(jQuery('.ez-toc-widget-sticky nav').scrollTop() + ezTocOffsetTopDynamic) - 50);
        }
        ezTocActiveList = ezTocHrefActive;
    }
});
