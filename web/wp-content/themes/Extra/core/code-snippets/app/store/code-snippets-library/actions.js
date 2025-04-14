function insertCodeIntoField({ get, props: { snippet, codeMirrorId, isAppend, skipInsert } }) {
  if (skipInsert) {
    return Promise.resolve();
  }

  const codeMirrorInstance = jQuery(`#${codeMirrorId}`).next('.CodeMirror')[0].CodeMirror;

  // Insert code into specified codeMirror field.
  // Append or replace depending on user preferences.
  if (isAppend) {
    snippet = codeMirrorInstance.getValue() ? '\n' + snippet : snippet;
    return Promise.resolve(codeMirrorInstance.replaceRange(snippet, {line: codeMirrorInstance.lastLine()}));
  } else {
    return Promise.resolve(codeMirrorInstance.setValue(snippet));
  }
}

export {
  insertCodeIntoField,
};
