# Vessel

[![Build Status](https://travis-ci.org/hokeo/vessel.svg?branch=master)](https://travis-ci.org/hokeo/vessel)
[![MIT License](http://img.shields.io/badge/license-MIT-green.svg)](http://opensource.org/licenses/MIT)
[![Still Maintained](http://stillmaintained.com/hokeo/vessel.png)](http://stillmaintained.com/hokeo/vessel)

Vessel is a simple, yet powerful file-based CMS (content management system). Written in PHP, built on Laravel.

Currently in the early stages of development. See Roadmap below, or see Installation below to dive in.

### Goals

Vessel aims to be:

* Easy to set up and integrate. It should provide a low barrier to entry for CMS templating. The process for a newcomer to hook an HTML document into content management should be simple, and should not require knowledge of a server side language to get a site up and running. Powerful functionalities from the Laravel Blade templating language are available for use, such as [template extension](http://laravel.com/docs/templates#blade-templating).

* Be straightforward. The backend interface, styled with Twitter Bootstrap, should be easy enough for most users, including clients, to use.

* Be extensible. A number of wordpress-style hooks (actions and filters) should be made available for plugins. Plugins can leverage the power of Composer, and the Laravel framework.

* Be documented extensively, for templaters, plugin developers, and clients.

### Requirements

* Laravel 4
* PHP 5.4+
* MCrypt PHP Extension

### Installation

If you're brave and/or would like to help out, here's how to install a workbench'd Vessel.

1. Create a new [Laravel installation](http://laravel.com/docs/installation). Configure to taste (url + database).
2. Run the following from your base laravel installation path:

```
$ mkdir -p workbench/hokeo/vessel
$ cd workbench/hokeo/vessel
$ git clone git://github.com/hokeo/vessel.git
$ composer update
$ cd ../../../
$ composer dump-autoload
$ php artisan migrate --bench=hokeo/vessel
$ php artisan db:seed --class=Hokeo\\Vessel\\Seeds\\TestVesselSeeder
$ php artisan dump-autoload
```

3. If you want to fiddle with different config variables from your app, run:

```
php artisan config:publish --path=workbench/hokeo/vessel/src/config/config.php hokeo/vessel
```

For testing, run `phpunit` from workbench/hokeo/vessel. Some bootstrapping hackery will ensue, for now.

Feel free to post any issues or comments here on github.

### Roadmap

Todo and recently completed, roughly in order:

* ~~Replace entrust with native role/permission mechanism~~
* ~~Lang + Validation messages~~
* ~~User Registration~~
* ~~Rehash plugin system~~
* Breadcrumbs?
* User Password resets
* Cool formatters (redactor/wysiwyg, image, text, advanced-custom-fields-like, table, custom, etc)
* Lots of hooks
* Installer
* HHVM support
* Docs
* Packagist submission

### License

Vessel is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)