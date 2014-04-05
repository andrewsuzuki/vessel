# Vessel Plugin Hooks

This is a list of existing plugin hooks. If the hook is a filter, the callback's available parameters are listed.

## Actions

Actions are called, but no return is expected.

| Hook Name | Description |
| --------- | ----------- |
| back.page-top | Top of back-end page (before title) |
| back.content-top | Top of back-end content (after title) |

## Filters

Filters are passed any number of arguments, and must return the same number of arguments ("filtered" or not) for the next filter in an array.

| Hook Name | Description | Arguments |
| --------- | ----------- | ----------|
| back.menu.main | Vessel back-end menu | $menu (handler object from vespakoen/menu package) |

## Observers

Observers are fired when something is happening to a model.

The hook name format is: model.NAME.EVENT

Where NAME is the lowercase name of the model, and EVENT is creating, created, updating, updated, saving, saved, deleting, deleted, restoring, or restored.

The callback will receive the model.

For example,

```
Plugin::listen('model.user.creating', function($model) {
	// do something with $model
});
```

See the laravel [model events](http://laravel.com/docs/eloquent#model-events) documentation for more information.