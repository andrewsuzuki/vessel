# Vessel

[![Build Status](https://travis-ci.org/hokeo/vessel.svg?branch=master)](https://travis-ci.org/hokeo/vessel)
[![MIT License](http://img.shields.io/badge/license-MIT-red.svg)](http://opensource.org/licenses/MIT)
[![Still Maintained](http://stillmaintained.com/hokeo/vessel.png)](http://stillmaintained.com/hokeo/vessel)

Vessel is a simple, yet powerful file-based CMS (content management system). Written in PHP, built on Laravel.

Currently in the early stages of development. Some things work, others do not. See Installation below to try it out.

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
$ php artisan asset:publish --bench=hokeo/vessel
$ php artisan dump-autoload
```

3. If you want to fiddle with different config variables from your app, run:

```
php artisan config:publish --path=workbench/hokeo/vessel/src/config/config.php hokeo/vessel
```

For testing, just run `phpunit` from workbench/hokeo/vessel. Some bootstrapping hackery will ensue, for now.

Feel free to post any issues or comments here on github.

### Roadmap

Todo, roughly in order:

* ~~JS auto-slugs~~
* ~~Plugins system~~
* ~~Theme setting~~
* ~~Page sub-templates~~
* ~~Page edit history~~
* ~~Page drafts~~
* ~~Set up unit tests~~
* ~~Set Up Permissions~~
* Menu builder
* Menu api
* ~~Block manager~~
* User Settings
* User Password resets
* User Registration
* User Management
* Cool formatters (redactor/wysiwyg, image, text, advanced-custom-fields-like, table, custom, etc)
* Media management
* Docs
* Packagist

### License

Vessel is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)