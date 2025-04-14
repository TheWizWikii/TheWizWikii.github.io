// External dependencies.
import {
  state,
} from 'cerebral';
import { noop } from 'lodash';

// Internal dependencies.
import { saveToCloudPure } from '@cloud/app/lib/api';


const saveToCloud = ({ get, props, path }) => {
  const itemId       = get(state`edit.item.id`);
  const context      = get(state`context`);
  let snippetContent = get(state`edit.content`);

  snippetContent = {
    ...snippetContent,
    data: props.content,
  };


  return saveToCloudPure(context, { content: JSON.stringify(snippetContent) }, [], noop, itemId)
    .then(() => path.success())
    .catch(() => path.error());
};

const saveToLocal = ({ codeSnippetsLibApi, path, props: { content }, get }) => {
  const itemId = get(state`edit.item.id`);
  return codeSnippetsLibApi.saveItemContent(itemId, content)
    .then(response => path.success({ snippet: response.snippet }))
    .catch(() => path.error());
};

export {
  saveToCloud,
  saveToLocal,
};
