/**
 * WP Captcha
 * Backend GUI pointers
 * (c) WebFactory Ltd, 2022 - 2023, www.webfactoryltd.com
 */

jQuery(document).ready(function($){
  if (typeof wpcaptcha_pointers  == 'undefined') {
    return;
  }

  $.each(wpcaptcha_pointers, function(index, pointer) {
    if (index.charAt(0) == '_') {
      return true;
    }
    $(pointer.target).pointer({
        content: '<h3>WP Captcha</h3><p>' + pointer.content + '</p>',
        pointerWidth: 380,
        position: {
            edge: pointer.edge,
            align: pointer.align
        },
        close: function() {
                $.get(ajaxurl, {
                    action: "wpcaptcha_run_tool",
                    tool: "wpcaptcha_dismiss_pointer",
                    notice_name: index,
                    _ajax_nonce: wpcaptcha_pointers.run_tool_nonce
                });
        }
      }).pointer('open');
  });
});
