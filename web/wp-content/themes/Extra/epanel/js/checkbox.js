/**
 * jQuery custom checkboxes
 *
 * Copyright (c) 2008 Khavilo Dmitry (http://widowmaker.kiev.ua/checkbox/)
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * @version 1.3.0 Beta 1
 * @author Khavilo Dmitry
 * @mailto wm.morgun@gmail.com
 *
 * Modified to adapt the latest jQuery version (v3 above) included on WordPress 5.6:
 * - (2020-12-15) - jQuery hover method is deprecated.
 * - (2021-02-02) - jQuery :eq() selector is deprecated.
 * - (2021-02-03) - jQuery bind method is deprecated.
 * - (2021-02-04) - jQuery click event shorthand is deprecated.
**/
(function($){var i=function(e){if(!e)var e=window.event;e.cancelBubble=true;if(e.stopPropagation)e.stopPropagation()};$.fn.checkbox=function(f){try{document.execCommand('BackgroundImageCache',false,true)}catch(e){}var g={cls:'jquery-checkbox',empty:clearpath};g=$.extend(g,f||{});var h=function(a){var b=a.checked;var c=a.disabled;var d=$(a);if(a.stateInterval)clearInterval(a.stateInterval);a.stateInterval=setInterval(function(){if(a.disabled!=c)d.trigger((c=!!a.disabled)?'disable':'enable');if(a.checked!=b)d.trigger((b=!!a.checked)?'check':'uncheck')},10);return d};return this.each(function(){var a=this;var b=h(a);if(a.wrapper)a.wrapper.remove();a.wrapper=$('<span class="'+g.cls+'"><span class="mark"><img src="'+g.empty+'" /></span></span>');a.wrapperInner=a.wrapper.children('span').eq(0);a.wrapper.on("mouseenter",function(e){a.wrapperInner.addClass(g.cls+'-hover');i(e)}).on("mouseleave",function(e){a.wrapperInner.removeClass(g.cls+'-hover');i(e)});b.css({position:'absolute',zIndex:-1,visibility:'hidden'}).after(a.wrapper);var c=false;if(b.attr('id')){c=$('label[for='+b.attr('id')+']');if(!c.length)c=false}if(!c){c=b.closest?b.closest('label'):b.parents('label').eq(0);if(!c.length)c=false}if(c){c.on("mouseenter",function(e){a.wrapper.trigger('mouseover',[e])}).on("mouseleave",function(e){a.wrapper.trigger('mouseout',[e])});c.on('click',function(e){b.trigger('click',[e]);i(e);return false})}a.wrapper.on('click',function(e){b.trigger('click',[e]);i(e);return false});b.on('click',function(e){i(e)});b.on('disable',function(){a.wrapperInner.addClass(g.cls+'-disabled')}).on('enable',function(){a.wrapperInner.removeClass(g.cls+'-disabled')});b.on('check',function(){a.wrapper.addClass(g.cls+'-checked')}).on('uncheck',function(){a.wrapper.removeClass(g.cls+'-checked')});$('img',a.wrapper).on('dragstart',function(){return false}).on('mousedown',function(){return false});if(window.getSelection)a.wrapper.css('MozUserSelect','none');if(a.checked)a.wrapper.addClass(g.cls+'-checked');if(a.disabled)a.wrapperInner.addClass(g.cls+'-disabled')})}})(jQuery);