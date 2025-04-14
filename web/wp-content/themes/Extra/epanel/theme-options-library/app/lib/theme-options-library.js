// Set global variable to detect new library item creation.
window.themeOptionsLibraryItemsLoaded = {};

export const setThemeOptionsLibraryItemsLoaded = (context, flag) => {
  window.themeOptionsLibraryItemsLoaded = {
    [context] : flag,
  };
};

export const setThemeOptionsLibraryToken = (token) => {
  window.globalCloudToken = token;
};
