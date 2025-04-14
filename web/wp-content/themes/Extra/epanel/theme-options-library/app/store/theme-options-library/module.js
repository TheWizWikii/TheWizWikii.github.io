import { state } from 'cerebral';
import * as sequences from './sequences';
import themeOptionsLibApi from './providers';
import { PORTABILITY_STATE_DEFAULT } from '../../lib/constants';


export default initialState => (
  {
    state: {
      ...initialState,
      showSaveModal: get => 'save' === get(state`modalType`),
      showLibraryModal: get => 'add' === get(state`modalType`),
      itemsLoadedAndCached: false,
      portability: {
        state: PORTABILITY_STATE_DEFAULT,
        export: {},
      },
    },
    providers: {
      themeOptionsLibApi,
    },
    sequences,
  }
);
