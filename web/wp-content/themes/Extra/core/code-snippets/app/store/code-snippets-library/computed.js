// External dependencies.
import { state } from 'cerebral';

// Internal dependencies.
import { MODAL_TYPE_EDIT_ITEM } from '@code-snippets/lib/constants';


const isEditItemModalOpen = get => {
  const openModal = get(state`openModal`);
  return openModal === MODAL_TYPE_EDIT_ITEM;
};

export {
  isEditItemModalOpen,
};
