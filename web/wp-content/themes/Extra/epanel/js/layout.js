(function($){
	var initLayout = function() {
		var hash = window.location.hash.replace('#', '');
		var currentTab = $('ul.navigationTabs a')
							.on('click', showTab)
							.filter('a[rel=' + hash + ']');
		if (0 === currentTab.length) {
			currentTab = $('ul.navigationTabs a').first();
		}
		showTab.apply(currentTab.get(0));
		$('#colorpickerHolder').ColorPicker({flat: true});
		$('.colorpopup').ColorPicker({
			onSubmit: function(hsb, hex, rgb, el) {
				$(el).val(hex);
				$(el).ColorPickerHide();
			},
			onBeforeShow: function () {
				$(this).ColorPickerSetColor(this.value);
			}
		})
	};

	var showTab = function(e) {
		var tabIndex = $('ul.navigationTabs a')
							.removeClass('active')
							.index(this);
		$(this)
			.addClass('active')
			.trigger('blur');
		$('div.tab')
			.hide()
				.eq(tabIndex)
				.show();
	};

	EYE.register(initLayout, 'init');
})(jQuery)