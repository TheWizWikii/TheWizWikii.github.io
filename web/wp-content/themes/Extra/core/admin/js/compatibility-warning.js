/**
 * Compatibility Warning Scripts.
 *
 * The scripts below is used for overriding or modifying default WP template to show
 * warning about WP and PHP versions compatibility with user's environment.
 *
 * @see {ET_Core_Compatibility_Warning()}
 */
 (function($) {
  'use strict';

  // Bail early if there is no compatibility warning data.
  if (!window.et_compatibility_warning) {
    return;
  }

  var data = et_compatibility_warning;

  // A. Update Core.
  if (data.update_core_data) {
    if (data.update_core_data.plugins) {
      // Plugins - Override updates table body.
      var pluginsTableBody = window.wp.template('et-update-core-plugins-table-body');
      $('#update-plugins-table .plugins').html(pluginsTableBody(data.update_core_data));
    }

    if (data.update_core_data.themes) {
      // Themes - Override updates table body.
      var themesTableBody = window.wp.template('et-update-core-themes-table-body');
      $('#update-themes-table .plugins').html(themesTableBody(data.update_core_data));
    }
  }

  // B. Manage Themes.
  if (data.manage_themes_data) {
    // Themes List & Details - Remove default templates, so we can replace them later.
    $('#tmpl-theme').remove();
    $('#tmpl-theme-single').remove();
  }

  // C. Theme Customizer.
  if (data.customizer_data) {
    // Active Theme - Disable publish button.
    if (true !== data.customizer_data.compatible_wp || true !== data.customizer_data.compatible_php) {
      var $save            = $('#customize-controls #save');
      var $publishSettings = $('#customize-controls #publish-settings');

      if ($publishSettings.length > 0) {
        $save.remove();
        $publishSettings
          .removeAttr('id')
          .attr('class', 'button button-primary disabled')
          .text(data.customizer_data.disabled_text);
      } else {
        $save
          .removeAttr('id')
          .attr('class', 'button button-primary disabled')
          .attr('value', data.customizer_data.disabled_text)
          .text(data.customizer_data.disabled_text);
      }
    }

    // Themes List & Details - Remove default templates, so we can replace them later.
    $('#tmpl-customize-control-theme-content').remove();
  }
})(jQuery);
