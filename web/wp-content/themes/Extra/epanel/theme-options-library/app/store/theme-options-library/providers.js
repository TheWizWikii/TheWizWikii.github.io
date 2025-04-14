// Internal dependencies.
import { noop } from 'lodash';

// Internal dependencies.
import { post } from '../../lib/request';
import config from '../../lib/config';
import { saveToCloudPure } from '@cloud/app/lib/api';

export default {
  /**
   * Gets the Theme Options library items.
   *
   * @param {string} context Context (a.k.a Item type).
   *
   * @returns {Array} Resolved value from promise. Array of objects.
   */
  getItems(context) {
    /* eslint-disable key-spacing */
    return post({
      action: 'et_theme_options_library_get_items',
      context,
      nonce: config.nonces.et_theme_options_library_get_items,
    });
    /* eslint-enable key-spacing */
  },

  getItemsContent(item) {
    return post({
      action: 'et_theme_options_library_get_item_content',
      et_theme_option_id: item,
      nonce: config.nonces.et_theme_options_library_get_item_content,
    });
  },

  importContent(file) {
    const formData = new FormData();
    formData.append('action', 'et_core_portability_import');
    formData.append('file', file, 'theme-options.json');
    formData.append('context', 'epanel');
    formData.append('nonce', config.nonces.et_core_portability_import);
    return post(formData, {
      contentType: false,
      processData: false,
    });
  },

  exportItem(id, cloudContent) {
    return post({
      action: 'et_theme_options_library_export_item',
      nonce: config.nonces.et_theme_options_library_export_item,
      id,
      cloudContent,
    });
  },

  downloadExportFile(id, fileName) {
    const args = {
      action: 'et_theme_options_library_export_item_download',
      nonce: config.nonces.et_theme_options_library_export_item,
      fileName,
      id,
    };

    return `${config.api}?${jQuery.param(args)}`;
  },

  /**
   * Update Local Tags/Categories and return updated list.
   *
   * @param {object} payload Payload.
   * @returns {Array}        Response.
   */
  updateFilters(payload) {
    return post({
      action: 'et_theme_options_library_update_terms',
      nonce: config.nonces.et_theme_options_library_update_terms,
      payload,
    });
  },

  /**
   * Update the theme options library item.
   *
   * @param {object} payload Updated item details.
   * @returns {Array}        Resolved value from promise. Array of objects.
   */
  updateItem(payload) {
    return post({
      action : 'et_theme_options_library_update_item',
      nonce  : config.nonces.et_theme_options_library_update_item,
      payload,
    });
  },

  /*
   * Gets the Theme Options library item content.
   *
   * @returns {Array} Resolved value from promise. Array of objects.
   */
  getItemContent(id) {
    return post({
      action: 'et_theme_options_library_get_item_content',
      nonce: config.nonces.et_theme_options_library_get_item_content,
      et_theme_option_id: id,
    });
  },

  /**
   * Retrieve Cloud Token.
   *
   * @returns {Array} Response with cloud token.
   */
  getCloudToken() {
    return post({
      action: 'et_theme_options_library_get_token',
      nonce : config.nonces.et_theme_options_library_get_token,
    });
  },

  /**
   * Remove local item.
   *
   * @param {int} id 
   * @returns {Array} Response with cloud token.
   */
  export() {
    return post({
      action: 'et_core_portability_export',
      nonce: config.nonces.et_core_portability_export,
      context: 'epanel_temp',
      content: false,
      selection: false,
      timestamp: 0,
      page: 1,
    });
  },

  saveTempOptions() {
    let opsForm = jQuery('#main_options_form').formSerialize();
    const nonce = `&_ajax_nonce=${config.nonces.et_core_save_theme_options}`;
    opsForm    += `${nonce}&action=save_epanel_temp`;
    return post(opsForm);
  },

  download(timestamp) {
    let downloadURL = config.epanel_save_url;
    const query     = {
      timestamp,
      name: '',
    };

    Object.entries(query).forEach(([key, value]) => {
      if (value) {
        downloadURL = `${downloadURL}&${key}=${value}`;
      }
    });

    return fetch(downloadURL);
  },

  saveThemeOptionsToLocal(item, content) {
    const {
      item_name,
      selected_cats,
      selected_tags,
      new_category_name,
      new_tag_name,
    } = item;

    return post({
      action: 'et_library_save_item',
      et_library_save_item_nonce: config.nonces.et_library_save_item,
      post_type: config.post_types.et_theme_options,
      item_name,
      selected_cats,
      selected_tags,
      new_category_name,
      new_tag_name,
      content,
    });
  },

  saveThemeOptionsToCloud(item, content) {
    const {
      new_category_name,
      new_tag_name,
      selected_tags,
      selected_cats,
      item_name,
      providedBaseUrl,
    } = item;

    const newCategories = new_category_name.split(',').map(newCategory => newCategory.trim());
    const newTags       = new_tag_name.split(',').map(newTag => newTag.trim());
    const termsData     = {
      tags: [...selected_tags, ...newTags],
      categories: [...selected_cats, ...newCategories],
    };

    const newCloudItem = {
      title: item_name,
      content,
      status: 'publish',
    };

    return saveToCloudPure('theme-options', newCloudItem, termsData, noop, 0, providedBaseUrl);
  },

  deleteTempOptions() {
    return post({
      action: 'et_theme_options_delete_temp_options',
      et_theme_options_delete_temp_options_nonce: config.nonces.et_theme_options_delete_temp_options,
    });
  },

  removeLocalItem(id) {
    /* eslint-disable key-spacing */
    return post({
      action                 : 'et_theme_options_toggle_cloud_status',
      nonce                  : config.nonces.et_theme_options_library_toggle_item_location,
      et_theme_option_id     : id,
    });
    /* eslint-enable */
  },
};
