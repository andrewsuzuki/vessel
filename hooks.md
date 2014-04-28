# Vessel Plugin Hooks

This is a list of existing plugin hooks. If the hook is a filter, the callback's available parameters are listed.

## Actions

Actions are called, but no return is expected.

| Hook Name | Description |
| --------- | ----------- |
| back.page-top | Top of back-end page (before title) |
| back.page-bottom | Bottom of back-end page |
| back.content-top | Top of back-end content (after title) |
| back.content-bottom | Bottom of back-end content (immediately before page-bottom) |
| back.content-bottom | Bottom of back-end content (immediately before page-bottom) |
| back.scripts-pre | Before all back-end footer javascripts |
| back.scripts-assets-pre | Before all back-end footer javascripts loaded with Asset class (after basic jquery, bootstrap, etc) |
| back.scripts-assets-post | After all back-end footer javascripts loaded with Asset class |
| back.scripts-post | After all back-end footer javascripts |

## Filters

Filters are passed any number of arguments, and must return the same number of arguments ("filtered" or not) for the next filter in an array.

| Hook Name | Description | Arguments |
| --------- | ----------- | ----------|
| back.menu.main | Vessel back-end menu | $menu (handler object from vespakoen/menu package) |
| back.menumanager.getmenuablepages | Pages to list in <select> as pages available for menu | $pages (collection) |

## Observers

Observers are fired when something is happening to a model.

The hook name format is: model.name.event

Where **name** is the lowercase name of the model, and **event** is creating, created, updating, updated, saving, saved, deleting, deleted, restoring, or restored.

The callback will receive the model.

For example,

```
Plugin::listen('model.user.creating', function($model) {
	// do something with $model
});
```

See the laravel [model events](http://laravel.com/docs/eloquent#model-events) documentation for more information.