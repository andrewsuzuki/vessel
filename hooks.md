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
