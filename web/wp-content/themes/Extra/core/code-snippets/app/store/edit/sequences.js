// External dependencies.
import {
  props,
  sequence,
  state,
} from 'cerebral';
import {
  set,
  wait,
  when,
} from 'cerebral/factories';

// Internal dependencies.
import {
  MODAL_TYPE_EDIT_ITEM,
  STATE_ERROR,
  STATE_LOADING,
  STATE_SUCCESS,
} from '@code-snippets/lib/constants';
import {
  closeModal,
  openModal,
} from '@code-snippets/store/code-snippets-library/factories';
import { downloadSnippetContent } from '@code-snippets/store/code-snippets-library/sequences';
import {
  saveToCloud,
  saveToLocal,
} from './actions';


const openLocalItemEditor = sequence('Open local item editor', [
  set(state`edit.progress`, 10),
  ({ codeSnippetsLibApi, path, props: { item } }) => codeSnippetsLibApi.getItemContent(item.id, item.type)
    .then(response => path.success({ snippet: response.snippet }))
    .catch(() => path.error()),
  {
    success: [
      set(state`edit.progress`, 90),
      wait(500),
      set(state`edit.progress`, 100),
      wait(200),
      ({ store, props: { snippet } }) => {
        store.set(state`edit.snippet`, snippet);
      },
      set(state`edit.progress`, 0),
    ],
    error: [
      set(state`edit.progress`, 0),
    ],
  },
]);

const openCloudItemEditor = sequence('Open cloud item editor', [
  set(state`edit.snippet`, props`content.data`),
  set(state`edit.content`, props`content`),
]);

const openEditModal = sequence('Open modal to edit code snippet', [
  openModal(MODAL_TYPE_EDIT_ITEM),
  set(state`edit.item`, props`item`),
  set(state`edit.context`, props`context`),
  when(props`item.item_location`, item_location => 'cloud' === item_location),
  {
    true: [
      openCloudItemEditor,
    ],
    false: [
      openLocalItemEditor,
    ],
  },
]);

const closeEditModal = sequence('Close snippet editor modal', [
  closeModal(),
  set(state`edit.saveState`, null),
  set(state`edit.item`, null),
  set(state`edit.snippet`, ''),
]);


const saveEditedItemSuccess = sequence('Edited item Saved', [
  () => ({needImageRefresh: true}),
  downloadSnippetContent,
  set(state`edit.saveState`, STATE_SUCCESS),
  wait(500),
  closeEditModal,
]);

const saveEditedItemError = sequence('Error while saving edited item', [
  set(state`edit.saveState`, STATE_ERROR),
  wait(500),
  closeEditModal,
]);

const saveEditedContent = sequence('Save editted content', [
  set(state`edit.saveState`, STATE_LOADING),
  when(state`edit.item.item_location`, item_location => 'cloud' === item_location),
  {
    true: [
      saveToCloud,
      {
        success: [
          ({ get, props: { content } }) => ({ snippetId: get(state`edit.item.id`), snippetContent: content }),
          ({ get }) => ({needImageRefresh: true, item_location: get(state`edit.item.item_location`)}),
          saveEditedItemSuccess,
        ],
        error: [
          saveEditedItemError,
        ],
      },
    ],
    false: [
      saveToLocal,
      {
        success: [
          ({ get }) => ({ snippetId: get(state`edit.item.id`) }),
          saveEditedItemSuccess,
        ],
        error: [
          saveEditedItemError,
        ],
      },
    ],
  },

]);


export {
  openEditModal,
  closeEditModal,
  saveEditedContent,
};
