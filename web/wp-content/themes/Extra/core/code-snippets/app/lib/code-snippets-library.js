// Set global variable to detect new library item creation.
window.CodeSnippetItemsLoaded = {};

export const setCodeSnippetItemsLoaded = (context, flag) => {
  window.CodeSnippetItemsLoaded = {
    ...CodeSnippetItemsLoaded,
    [context] : flag,
  };
};

/* eslint-disable import/prefer-default-export */
export const getItemTypeByContext = context => {
  let type;

  switch (context) {
    case 'code_html':
      type = 'et_code_snippet_html_js';
      break;
    case 'code_css_no_selector':
      type = 'et_code_snippet_css_no_selector';
      break;
    default:
      type = 'et_code_snippet_css';
      break;
  }

  return type;
};

export const setCodeSnippetsLibraryToken = (token) => {
  window.globalCloudToken = token;
};
