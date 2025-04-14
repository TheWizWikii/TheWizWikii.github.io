/* eslint-disable import/prefer-default-export */
import { post } from '../../lib/request';
import config from '../../lib/config';


export const codeSnippetsLibApi = {
  /**
   * Gets the Code Snippet library items.
   *
   * @param {string} type One of `et_code_snippet` types.
   * @returns {Array} Resolved value from promise. Array of objects.
   */
  getItems(type) {
  /* eslint-disable key-spacing */
    return post({
      action              : 'et_code_snippets_library_get_items',
      nonce               : config.nonces.et_code_snippets_library_get_items,
      et_code_snippet_type: type,
    });
    /* eslint-enable */
  },

  getItemContent(id, snippetType, contentFormat = 'raw') {
    /* eslint-disable key-spacing */
    return post({
      action                 : 'et_code_snippets_library_get_item_content',
      nonce                  : config.nonces.et_code_snippets_library_get_item_content,
      et_code_snippet_id     : id,
      et_code_snippet_type   : snippetType,
      et_code_snippet_format : contentFormat,
    });
    /* eslint-enable */
  },

  saveItemContent(id, snippetContent) {
    /* eslint-disable key-spacing */
    return post({
      action                 : 'et_code_snippets_library_save_item_content',
      nonce                  : config.nonces.et_code_snippets_library_save_item_content,
      et_code_snippet_id     : id,
      et_code_snippet_content: snippetContent,
    });
    /* eslint-enable */
  },

  removeLocalItem(id) {
    /* eslint-disable key-spacing */
    return post({
      action                 : 'et_code_snippets_toggle_cloud_status',
      nonce                  : config.nonces.et_code_snippets_library_toggle_item_location,
      et_code_snippet_id     : id,
    });
    /* eslint-enable */
  },

  /**
   * Update the Code Snippet library item.
   *
   * @param {object} payload Updated item details.
   * @returns {Array}        Resolved value from promise. Array of objects.
   */
  updateItem(payload) {
    /* eslint-disable key-spacing */
    return post({
      action : 'et_code_snippets_library_update_item',
      nonce  : config.nonces.et_code_snippets_library_update_item,
      payload,
    });
    /* eslint-enable */
  },

  /**
   * Export Code Snippet library item.
   *
   * @param {int} id             Item ID.
   * @param {array} cloudContent Cloud content.
   * @param {obj} directExport   Snippet type and Content.
   * @returns {Array}            Resolved value from promise. Array of objects.
   */
   exportItem(id, cloudContent, directExport) {
    /* eslint-disable key-spacing */
    return post({
      action : 'et_code_snippets_library_export_item',
      nonce  : config.nonces.et_code_snippets_library_export_item,
      id,
      cloudContent,
      directExport,
    });
    /* eslint-enable */
  },

  /**
   * Download Code Snippet library item.
   *
   * @param {int} id             Item ID.
   * @param {string} fileName    Item name.
   * @returns {string}           URI string.
   */
   downloadExportFile(id, fileName) {
    /* eslint-disable key-spacing */
    const args = {
      action  : 'et_code_snippets_library_export_item_download',
      nonce   : config.nonces.et_code_snippets_library_export_item,
      fileName,
      id,
    };

    return `${config.api}?${jQuery.param(args)}`;
    /* eslint-enable */
  },

  /**
   * Import Code Snippet library item.
   *
   * @param {Blob} file File.
   * @returns {Array}   Response.
   */
   importItem(fileData) {
    const fileContent = JSON.parse(fileData.content);

    return post({
      action     : 'et_code_snippets_library_import_item',
      nonce      : config.nonces.et_code_snippets_library_import_item,
      fileContent: JSON.stringify(fileContent.data),
      fileData,
    });
  },

  /**
   * Update Local Tags/Categories and return updated list.
   *
   * @param {Blob} file File.
   * @returns {Array}   Response.
   */
  updateFilters(payload) {
    return post({
      action  : 'et_code_snippets_library_update_terms',
      nonce   : config.nonces.et_code_snippets_library_update_terms,
      payload,
    });
  },

  /**
   * Retrieve Cloud Token.
   *
   * @returns {Array}   Response with cloud token.
   */
  getCloudToken() {
    return post({
      action : 'et_code_snippets_library_get_token',
      nonce  : config.nonces.et_code_snippets_library_get_token,
    });
  }
};
/* eslint-enable */
