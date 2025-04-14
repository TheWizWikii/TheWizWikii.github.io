import edit from '@code-snippets/store/edit/module';
import * as providers from './providers';
import * as sequences from './sequences';
import * as computed from './computed';

export default initialState => (
  {
    state: {
      ...initialState,
      openModal: null,
      itemsLoadedAndCached: false,
      computed: {
        ...computed,
      },
      cloudToken: null,
    },
    providers,
    sequences,
    modules: {
      edit,
    },
  }
);
