(function() {
       tinymce.PluginManager.add('toc_mce_button', function( editor, url ) {
           editor.addButton('toc_mce_button', {
                       text: 'TOC',
                       icon: false,
                       onclick: function() {
                         // change the shortcode as per your requirement
                          editor.insertContent('[ez-toc]');
                      }
             });
       });
})();