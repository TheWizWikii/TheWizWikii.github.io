// External dependencies.
import {
  props,
  sequence,
  state,
} from 'cerebral';
import { set } from 'cerebral/factories';
import { isEmpty } from 'lodash';

// Internal dependencies.
import { getItemTypeByContext, getContextByItemType } from '@common-ui/lib/local-library';
import { updateTokens, doApiRequest } from '@cloud/app/lib/api';
import { handleCloudError } from '@cloud/app/components/app/actions/shared-actions';
import { insertCodeIntoField } from './actions';
import { setCodeSnippetItemsLoaded, setCodeSnippetsLibraryToken } from '../../lib/code-snippets-library';
import { STATE_IDLE, STATE_LOADING, STATE_SUCCESS } from '@code-snippets/lib/constants';


/* eslint-disable import/prefer-default-export */
const setLibraryContext = sequence('Set Code Snippets library context', [
  set(state`context`, props`context`),
  set(state`itemsLoadedAndCached`, false),
]);

const setCloudToken = sequence('Set cloudToken', [
  ({ get }) => {
    setCodeSnippetsLibraryToken(get(props`cloudToken`));
  },
]);

const cacheCloudToken = sequence('Retrieve saved Cloud Access token and save to state', [
  ({ codeSnippetsLibApi, path }) => {
    return codeSnippetsLibApi.getCloudToken()
      .then(cloudTokenData => {
        return path.success({cloudToken: cloudTokenData.accessToken});
      })
      .catch(() => path.error());
  },
  {
    success: [
      setCloudToken
    ],
    error: [],
  },
]);

const loadItems = sequence('Load Code Snippets library items', [
  ({ codeSnippetsLibApi, get, path }) => {
    const context = get(state`context`);
    const type    = getItemTypeByContext(context);

    // Exit if no context provided.
    if ('' === context) {
      return path.error();
    }

    return codeSnippetsLibApi.getItems(type)
      .then(response => path.success({
        items: response,
      }))
      .catch(() => path.error());
  },
  {
    success: [
      set(state`items`, props`items`),

      set(state`itemsLoadedAndCached`, true),

      ({get}) => { setCodeSnippetItemsLoaded(get(state`context`), true); },
    ],
    error: [],
  },
]);

const insertSnippet = sequence('Insert Code Snippet into a field', [
  ({ codeSnippetsLibApi, get, path }) => {
    const id             = get(props`snippetId`);
    const snippetContent = get(props`snippetContent`);
    const context        = get(state`context`);
    const type           = getItemTypeByContext(context);

    if (false !== snippetContent) {
      return path.success({ snippet: snippetContent });
    }

    return codeSnippetsLibApi.getItemContent(id, type)
      .then(response => path.success({ snippet: response.snippet }))
      .catch(() => path.error());
  },
  {
    success: [
      set(state`snippetCode`, props`snippet`),
      set(state`snippetCodeAppend`, props`isAppend`),
      insertCodeIntoField,
      set(state`showLibrary`, false),
      set(state`context`, ''),
      () => { jQuery(window).trigger('et_code_snippets_library_close'); },
    ],
    error: [],
  },
]);

const getExportedItem = sequence('Get the exported Code Snippet content', [
  ({ codeSnippetsLibApi, path, props: { id, itemType } }) => {

    return codeSnippetsLibApi.getItemContent(id, itemType, 'exported')
      .then((response => path.success(response)))
      .catch(() => path.error());
  },
  {
    success: [Promise.resolve(props`response`)],
    error: [],
  },
]);

const toggleLibraryItemLocation = sequence('Remove local item from WPDB', [
  ({ codeSnippetsLibApi, path, props: { id } }) => {

    return codeSnippetsLibApi.removeLocalItem(id)
      .then((response => path.success(response)))
      .catch(() => path.error());
  },
  {
    success: [Promise.resolve(props`response`)],
    error: [],
  },
]);

const updateLocalFilters = sequence('Update Local Filters', [
  ({ codeSnippetsLibApi, path, props: { payload } }) => {

    return codeSnippetsLibApi.updateFilters(payload)
      .then((response => path.success(response)))
      .catch(() => path.error());
  },
  {
    success: [Promise.resolve(props`response`)],
    error: [],
  },
]);

const updateItem = sequence('Update Code Snippets library item', [
  ({ codeSnippetsLibApi, get, path }) => {
    const payload = get(props`payload`);

    return codeSnippetsLibApi.updateItem(payload)
      .then(response => path.success({
        updatedItem: {
          success: true,
          data: response,
        },
      }))
      .catch(() => path.error());
  },
  {
    success: [Promise.resolve(props`updatedItem`)],
    error: []
  },
]);

const setShowLibrary = sequence('Set Code Snippets show library', [
  set(state`showLibrary`, props`toggle`),
]);

const resetSnippetCode = sequence('Reset the saved code value', [
  set(state`snippetCode`, ''),
]);

const setShowSaveModal = sequence('Toggle Code Snippets save modal', [
  set(state`showSave`, props`toggle`),
]);

const openPortablity = sequence('Open Code Snippets portability modal', [
  set(state`showPortability`, true),
]);

const closePortability = sequence('Close Code Snippets portability modal', [
  set(state`showPortability`, false),
  set(state`importError`, false),
]);

const exportSnippet = sequence('Export code snippet', [
  ({ codeSnippetsLibApi, path, props: { id, cloudContent, directExport } }) => {
    return codeSnippetsLibApi.exportItem(id, cloudContent, directExport)
      .then(() => path.success())
      .catch(() => path.error());
  },
  {
    success: [
      ({ codeSnippetsLibApi, props: { id, fileName } }) => {
        const downloadURI = codeSnippetsLibApi.downloadExportFile(id, fileName);

        window.location.assign(downloadURI);

        window.ETCloudApp.emitSignal({
          signal: 'finishDownload',
          data: {},
        });
      },

      ({ props: { directExport } }) => {
        if (!isEmpty(directExport)) {
          jQuery(window).trigger('et_code_snippets_library_close');
        }
      },

      closePortability,
    ],
    error: []
  },
]);

const importSnippetToCloud = sequence('Import code snippet to cloud', [
  ({ get, store, props: { importFile } }) => updateTokens().then(refreshResponse => {
    const accessToken = refreshResponse['accessToken'];
    const callback    = (newItem) => {
      if (newItem.error) {
        handleCloudError(newItem, get, store, null);
        return;
      }

      store.set(state`showPortability`, false);
      store.set(state`importError`, false),
      store.set(state`importState`, STATE_SUCCESS),

      // Trigger Cloud Items Refresh in the cloud snippet library modal.
      window.ETCloudApp.emitSignal({
        signal: 'refreshCloudItems',
        data: {},
      });
    };

    if (!isEmpty(accessToken)) {
      store.set(state`cloudToken`, accessToken);

      const newCloudItem = {
        title  : importFile.title,
        content: importFile.content,
        status : 'publish',
        meta   : {},
      };

      const resource = getContextByItemType(importFile.type);
      const providedBaseUrl = window.ETCloudApp.getActiveFolderEndpoint();
      doApiRequest({ type: 'post', resource, accessToken, providedBaseUrl }, newCloudItem).then(callback);
    } else {
      set(state`importState`, STATE_IDLE),
      store.set(state`cloudStatus`, { error: 'auth_error' });
    }
  }),
]);

const importSnippetToLocal = sequence('Import code snippet to local', [
  ({ codeSnippetsLibApi, path, props: { importFile } }) => {
    return codeSnippetsLibApi.importItem(importFile)
      .then(() => path.success())
      .catch(() => path.error());
  },
  {
    success: [
      closePortability,
      set(state`importState`, STATE_SUCCESS),
      () => { jQuery(window).trigger('et_cloud_refresh_local_items'); },
    ],
    error: [
      set(state`importState`, STATE_IDLE),
      set(state`importError`, true),
    ]
  },
]);

const decideSnippetImport = sequence('Decide import code snippet', [
  set(state`importState`, STATE_LOADING),

  ({ path, props: { importToCloud } }) => {
    if (importToCloud) {
      return path.cloud();
    } else {
      return path.local();
    }
  },
  {
    cloud: [
      importSnippetToCloud,
    ],
    local: [
      importSnippetToLocal,
    ],
  }
]);

const importSnippet = sequence('Import code snippet', [
  ({ path, props: { importFile } }) => {
    if (importFile) return path.yes();
  },
  {
    yes: [
      decideSnippetImport,
    ],
  }
]);

const downloadSnippetContent = sequence('Download Snippet Content', [
  () => window.ETCloudApp.setCodeSnippetPreviewState({ codeSnippetPreviewState: STATE_LOADING }),
  // eslint-disable-next-line no-shadow
  ({ codeSnippetsLibApi, get, path, props: { snippetId, snippetContent, needImageRefresh, item_location = '' } }) => {
    // When a local item is downloaded, snippetId shall be available.
    if (! snippetContent && 'cloud' !== item_location ) {
      const context = get(state`context`);
      const type    = getItemTypeByContext(context);

      return codeSnippetsLibApi.getItemContent(snippetId, type)
        .then(response => path.success({ snippet: response.snippet, itemId: snippetId }))
        .catch(() => path.error());
    }

    // When a Cloud item is downloaded, snippetContent shall be available.
    const snippet = snippetContent;
    return path.success({ snippet, itemId: snippetId, needImageRefresh });
  },
  {
    success: [
      ({ props: { snippet, itemId, needImageRefresh } }) => {
        window.ETCloudApp.emitSignal({
          signal: 'renderCodeSnippetPreview',
          data: { snippet, itemId, needImageRefresh },
        });
      },
    ],
    error: [

    ],
  },
  () => window.ETCloudApp.setCodeSnippetPreviewState({ codeSnippetPreviewState: '' }),
]);

const closeModal = sequence('Close open modal', [
  set(state`openModal`, null),
]);

export {
  closePortability,
  downloadSnippetContent,
  exportSnippet,
  getExportedItem,
  importSnippet,
  insertSnippet,
  loadItems,
  openPortablity,
  resetSnippetCode,
  setLibraryContext,
  setShowLibrary,
  setShowSaveModal,
  toggleLibraryItemLocation,
  updateItem,
  updateLocalFilters,
  closeModal,
  setCloudToken,
  cacheCloudToken,
};
/* eslint-enable */
