/*!
 * jQuery hash persistance - v1.0 - 09/25/2014
 * Element options/state storage in location hash
 * Copyright (c) Elegant Themes
 */

//	Usage Example:
//
//	// To save the current state of an element (example)
//	function save_the_element_state() {
//		var element_current_state = [];
//
//		// First hash parameter is the element's id
//		element_current_state.push( $the_element.attr('id') ); //the_elements_id
//
//		// 1 or more pieces of data pertaining to the state of the element to be stored
//		element_current_state.push( $the_element.data('something') ); //something_cool
//		element_current_state.push( $the_element.find('.something_else').attr('id') ); //something_else_cool
//		element_current_state.push( etc_etc ); //more_coolness
//
//		// Join the array together, into properly formatted string
//		element_current_state = element_current_state.join( et_hash_module_param_seperator );
//		// element_current_state = 'the_elements_id|something_cool|something_else_cool|more_coolness';
//
//		// send the hash to et_set_hash which will append/update the location hash
//		et_set_hash( element_current_state );
//
//		// Example resulting location:
//		// http://website.com/the_page#the_elements_id|something_cool|something_else_cool|more_coolness||someotherelement|other_param_a|other_param_b
//
//	}
//
//	// Register handler for et_hashchange event
//	$the_element.on('et_hashchange', function( event ){
//		// event.params is a numerically keyed array of the parameters sent to
//		// et_set_hash() last, with 0 being the first parameter.
//		var params = event.params,
//			something = params[0],
//			something_else = params[1],
//			etc_etc = params[2],
//			$the_element = $( '#' + event.target.id );
//
//		// do whatever is needed to apply the parameters to bring your element back to
//		// the state according to the stored parameters
//	});

var et_hash_module_seperator = '||',
	et_hash_module_param_seperator = '|',
	et_set_hash,
	et_setting_hash = false;

(function ($) {

	function et_process_hashchange(hash) {
		var module_params,
			element;

		if ((hash.indexOf(et_hash_module_seperator, 0)) !== -1) {
			modules = hash.split(et_hash_module_seperator);
			for (var i = 0; i < modules.length; i++) {
				module_params = modules[i].split(et_hash_module_param_seperator);
				element = module_params[0];
				module_params.shift();

				if ($('#' + element).length) {
					$('#' + element).trigger({
						type: "et_hashchange",
						params: module_params
					});
				}
			}
		} else {
			module_params = hash.split(et_hash_module_param_seperator);
			element = module_params[0];
			module_params.shift();

			if ($('#' + element).length) {
				$('#' + element).trigger({
					type: "et_hashchange",
					params: module_params
				});
			}
		}
	}

	et_set_hash = function (module_state_hash) {
		var hash,
			element,
			in_hash;

		et_setting_hash = true;

		module_id = module_state_hash.split(et_hash_module_param_seperator)[0];
		if (!$('#' + module_id).length) {
			return;
		}

		if (window.location.hash) {
			hash = window.location.hash.substring(1); //Puts hash in variable, and removes the # character
			new_hash = [];

			if ((hash.indexOf(et_hash_module_seperator, 0)) !== -1) {
				modules = hash.split(et_hash_module_seperator);
				in_hash = false;

				for (var i = 0; i < modules.length; i++) {
					element = modules[i].split(et_hash_module_param_seperator)[0];
					if (element === module_id) {
						new_hash.push(module_state_hash);
						in_hash = true;
					} else {
						new_hash.push(modules[i]);
					}
				}
				if (!in_hash) {
					new_hash.push(module_state_hash);
				}
			} else {
				module_params = hash.split(et_hash_module_param_seperator);
				element = module_params[0];

				if (element !== module_id) {
					new_hash.push(hash);
				}
				new_hash.push(module_state_hash);
			}

			hash = new_hash.join(et_hash_module_seperator);
		} else {
			hash = module_state_hash;
		}

		var yScroll = document.body.scrollTop;
		window.location.hash = hash;
		document.body.scrollTop = yScroll;
		et_setting_hash = false;
	};


	$(window).on('load', function () {
		if (window.HashChangeEvent) {
			$(window).on('hashchange', function() {
				var hash = window.location.hash.substring(1);
				if (!et_setting_hash && hash.length) {
					et_process_hashchange(hash);
				}
			});
			$(window).trigger('hashchange');
		}
	});

})(jQuery);
