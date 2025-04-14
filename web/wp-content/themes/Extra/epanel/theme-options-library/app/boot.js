// External Dependencies
import React from 'react';
import { render, unmountComponentAtNode } from 'react-dom';
import $ from 'jquery';
import App from 'cerebral';
// import Devtools from 'cerebral/devtools';
import { Container } from '@cerebral/react';


// Internal Dependencies
import store from './store/index';
import ThemeOptionsApp from './components/App';

const initialState = {
  content:              '',
  context:              'theme-options',
  items:                [],
  showLibrary:          false,
  showPortability:      false,
  showSave:             false,
};

const unMountCommonLibraryApp = () => {
  const container = document.getElementById('et-theme-options-container');
  if (container) {
    unmountComponentAtNode(container);
    container.remove();
  }
}

// Note: Hyphen is used to stay consistent w/ the Cloud context.
$(window).on(`et_theme-options_container_ready`, (event, preferences) => {
  let devtools = null;

  /*if (process.env.NODE_ENV === 'development') {
    devtools = Devtools({
      host:                 '127.0.0.1:22722',
      reconnect:            false,
      bigComponentsWarning: 15,
    });
  }*/

  const modalType = preferences?.modalType || '';

  const state = {
    ...initialState,
    modalType
  };

  state.sidebarLabel = preferences?.sidebarLabel || '';
  state.builtFor     = preferences?.builtFor ?? 'Divi';

  const app = App(store(state), {
    devtools,
    returnSequencePromise: true,
  });

  const {
    containerId = 'et-theme-options-container',
    containerClass = 'et-theme-options-container'
  } = preferences;

  $(document.body).first().append(`<div id=${containerId} class=${containerClass}></div>`);

  render(
    <Container app={app}>
      <ThemeOptionsApp />
    </Container>,
    document.getElementById(containerId)
  );
});


$(window).on('et_theme-options_container_close', () => {
  unMountCommonLibraryApp();
});
