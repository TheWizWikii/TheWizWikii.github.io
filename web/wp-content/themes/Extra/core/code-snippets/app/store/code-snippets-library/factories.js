import { state } from 'cerebral';


const openModal = type => ({ store }) => store.set(state`openModal`, type);

const closeModal = () => ({ store }) => store.set(state`openModal`, null);

export {
  openModal,
  closeModal,
};
