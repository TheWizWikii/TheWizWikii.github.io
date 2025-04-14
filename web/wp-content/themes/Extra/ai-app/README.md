## DIVI AI - Frontend APP
-----
-----
:smiley:

> How to trigger the AI app:

On a click event, we can hook the below statement:

```jQuery(window).trigger('et_ai_container_ready', [preferences, 'et-ai-app']);```


Param 1: Object **--->** preferences
```
const preferences = {
    context: 'section',
    date: new Date(),
};
```
 This is just an example, you can pass any configuration you need.


Param 2: String **--->** 'container ID'
_We just have to pass a string as an ID, and the AI app will automatically create the element in the DOM_

> How the AI app starts:

AI app listens for the event and runs the _<APP />_ component.
```
$(window).on('et_ai_container_ready', (event, preferences, container) => {
  ...
  ...
    <Provider store={store}>
      <App />
    </Provider>
  ...
});
```
We can pass the preferences like this, if necessary: (Index.tsx)
```
<Provider store={store}>
  <App preferences={preferences} />
</Provider>
```

> How to start the test server:

Run `yarn` in the root to install packages
Run `yarn json-server` to start the JSON server

There is a db.json file included in the root directory for data.

Enjoy!
