async function importThemeOptions({ themeOptionsLibApi, props: { item } }) {
  const exportedContent   = item;
  exportedContent.context = 'epanel';

  const file = new File([JSON.stringify(exportedContent)], 'theme_option.json', { type: 'application/json' });
  await themeOptionsLibApi.importContent(file);
  window.location = window.location.href.replace(/reset\=true\&|\&reset\=true/, '');
};

export default {
  importThemeOptions,
};
