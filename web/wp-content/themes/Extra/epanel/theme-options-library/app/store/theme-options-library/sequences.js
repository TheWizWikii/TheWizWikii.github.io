// External dependencies.
import { props, sequence, state } from 'cerebral';
import { set, when } from 'cerebral/factories';
import { get as lodashGet } from 'lodash';

// Internal dependencies.
import { setThemeOptionsLibraryItemsLoaded, setThemeOptionsLibraryToken } from '../../lib/theme-options-library';
import actions from './actions';
import {
  PORTABILITY_STATE_EXPORT_THEME_OPTIONS,
  PORTABILITY_STATE_IMPORT_THEME_OPTIONS,
} from '../../lib/constants';


const closePortability = sequence('Close Theme Options portability modal', [
  set(state`showPortability`, false),
  set(state`importError`, false),
]);

const closeThemeOptionApp = sequence('Close theme options library app', [
  set(state`modalType`, null),
]);

const loadItems = sequence('Load theme options library items', [
  /* eslint-disable arrow-body-style, arrow-parens */
  ({ get, themeOptionsLibApi, path }) => {
    const context = get(state`context`);

    return themeOptionsLibApi
      .getItems(context)
      .then(response => path.success({
        items: response,
      }))
      .catch(() => path.error());
  },
  {
    success: [
      set(state`items`, props`items`),
      set(state`itemsLoadedAndCached`, true),

      ({ get }) => {
        setThemeOptionsLibraryItemsLoaded(get(state`context`), true);
      },
    ],
    error: [],
  },
  /* eslint-enable */
]);

const updateLocalFilters = sequence('Update Local Filters', [
  ({ themeOptionsLibApi, path, props: { payload } }) => themeOptionsLibApi
    .updateFilters(payload)
    .then((response => path.success(response)))
    .catch(() => path.error()),
  {
    success: [Promise.resolve(props`response`)],
    error: [],
  },
]);

const getExportedItem = sequence('Get the exported theme option content', [
  ({ themeOptionsLibApi, path, props: { id } }) => themeOptionsLibApi
    .getItemContent(id)
    .then(response => path.success(response))
    .catch(() => path.error()),
  {
    success: [Promise.resolve(props`response`)],
    error: [],
  },
]);

const updateItem = sequence('Update theme options library item', [
  ({ themeOptionsLibApi, get, path }) => {
    const payload = get(props`payload`);
    return themeOptionsLibApi.updateItem(payload)
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
    error: [],
  },
]);

const setCloudToken = sequence('Set cloudToken', [
  ({ get }) => {
    setThemeOptionsLibraryToken(get(props`cloudToken`));
  },
]);

const cacheCloudToken = sequence('Retrieve saved Cloud Access token and save to state', [
  ({ themeOptionsLibApi, path }) => {

    return themeOptionsLibApi.getCloudToken()
      .then(cloudTokenData => {
        return path.success({cloudToken: cloudTokenData.accessToken});
      })
      .catch(() => path.error());
  },
  {
    success: [
      setCloudToken,
    ],
    error: [],
  },
]);

const setLibraryContext = sequence('Set Theme Options library context', [
  set(state`context`, props`context`),
]);

const useThemeOptions = sequence('Insert theme options into a field', [
  when(props`item`, item => isNaN(parseInt(item))),
  {
    true: [
      ({ props: contextProps }) => ({ item: JSON.parse(contextProps.item) }),
      actions.importThemeOptions,
    ],
    false: [
      async ({ props: { item }, themeOptionsLibApi }) => {
        const data         = await themeOptionsLibApi.getItemsContent(item);
        const { exported } = data;
        return { item: exported };
      },
      actions.importThemeOptions,
    ],
  },
]);

const openPortablity = sequence('Open theme options library modal', [
  ({ store, props: { data } }) => {
    const portabilityState = lodashGet(data, 'action');

    if (PORTABILITY_STATE_EXPORT_THEME_OPTIONS === portabilityState) {
      const itemLocation = lodashGet(data, 'item.item_location');
      const exportItemId = lodashGet(data, 'item.id');
      store.set(state`portability.export.id`, exportItemId);

      if ('cloud' === itemLocation) {
        const exportItemContent = lodashGet(data, 'content');
        store.set(state`portability.export.content`, exportItemContent);
        store.set(state`portability.export.item_location`, itemLocation);
      }
    }

    if ([PORTABILITY_STATE_IMPORT_THEME_OPTIONS, PORTABILITY_STATE_EXPORT_THEME_OPTIONS].includes(portabilityState)) {
      store.set(state`portability.state`, portabilityState);
    } else {
      store.set(state`portability.state`, PORTABILITY_STATE_IMPORT_THEME_OPTIONS);
    }
  },

  set(state`showPortability`, true),
]);

const setShowLibrary = sequence('Set theme options library', [
  set(state`showLibrary`, props`toggle`),
]);

const exportThemeOptions = sequence('Export theme option', [
  ({ themeOptionsLibApi, path, props: { id, cloudContent } }) => themeOptionsLibApi.exportItem(id, cloudContent)
    .then(() => path.success())
    .catch(() => path.error()),
  {
    success: [
      ({ themeOptionsLibApi, props: { id, fileName } }) => {
        const downloadURI = themeOptionsLibApi.downloadExportFile(id, fileName);

        window.location.assign(downloadURI);

        window.ETCloudApp.emitSignal({
          signal: 'finishDownload',
          data: {},
        });
      },
      closePortability,
    ],
    error: [],
  },
]);

const saveThemeOptions = sequence('Save theme options', [
  async ({ themeOptionsLibApi }) => {
    await themeOptionsLibApi.saveTempOptions();
    const exportRestData = await themeOptionsLibApi.export();
    return { timestamp: exportRestData.timestamp };
  },
  async ({ themeOptionsLibApi, props: contextProps }) => {
    const response        = await themeOptionsLibApi.download(contextProps.timestamp);
    const exportedContent = await response.json();
    return { content: JSON.stringify(exportedContent) };
  },
  when(props`item.cloud`, cloud => 'on' === cloud),
  {
    true: [
      ({ themeOptionsLibApi, props: contextProps }) => {
        const { item, content } = contextProps;
        return themeOptionsLibApi.saveThemeOptionsToCloud(item, content);
      },
    ],
    false: [
      ({ themeOptionsLibApi, props: contextProps }) => {
        const { item, content } = contextProps;
        return themeOptionsLibApi.saveThemeOptionsToLocal(item, content);
      },
    ],
  },
  ({ themeOptionsLibApi }) => themeOptionsLibApi.deleteTempOptions(),
]);

const toggleLibraryItemLocation = sequence('Remove local item from WPDB', [
  /* eslint-disable-next-line arrow-body-style */
  ({ themeOptionsLibApi, path, props: { id } }) => {
    return themeOptionsLibApi.removeLocalItem(id)
      .then((response => path.success(response)))
      .catch(() => path.error());
  },
  {
    success: [Promise.resolve(props`response`)],
    error: [],
  },
  /* eslint-enable arrow-body-style */
]);

export {
  closePortability,
  closeThemeOptionApp,
  exportThemeOptions,
  cacheCloudToken,
  getExportedItem,
  loadItems,
  openPortablity,
  setCloudToken,
  setLibraryContext,
  setShowLibrary,
  toggleLibraryItemLocation,
  updateItem,
  updateLocalFilters,
  useThemeOptions,
  saveThemeOptions,
};
