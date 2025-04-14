# Cloud

Divi Cloud Client Application

Cloud Application is the Browser for the Library items which allows to manage items and perform various actions with items. It supports local items and items stored on the Divi Cloud. Both can be loaded into a single list and user can move items between local and cloud libraries using the Cloud App interface.

This is a standalone application and is fully independant from the Divi, includes/builder and core repos.
The only dependancy is the common repo.

It means Cloud Application can be mounted anywhere outside Divi and Visual Builder, for example on WP admin pages and can be extended to work with any type of items. It's not limited to work with Visual Builder layouts, items type is configurable.

To mount the Cloud app you have to create a container with id='et-cloud-app' and trigger `et_cloud_container_ready` event with set of preferences. Cloud Application will be mounted into `et-cloud-app` container. 

Preferences format:
```
{
  context:         string,
  initialTab:      string,
  editableTabs:    array,
  cloudTab:        string,
  predefinedTab:   string,
  globalSupport:   boolean,
  animation:       string,
  isProductTour:   boolean,
  showLoadOptions: boolean,
  permanentFilter: object,
}
```

The page will have to listen for the Cloud App events to handle the actions like loading item from the Cloud, editing, updating, etc. The list of available Cloud App actions:
et_cloud_page_changed,
et_cloud_use_item,
et_cloud_download_progress,
et_cloud_account_status_error,
et_cloud_help,
et_cloud_item_action,
et_cloud_filter_update,
et_cloud_update_item,
et_cloud_item_toggle_location,
et_cloud_token_ready,
et_cloud_token_removed,
et_cloud_app_ready,

Cloud App also have API to send data from the page or trigger some events. See the `cloud/app/providers/bridge.js` for available actions.

All the tests located in `__tests__` directory and can be run form the /cloud repo using `yarn test` command.