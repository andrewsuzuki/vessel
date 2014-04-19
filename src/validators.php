<?php

/*
|--------------------------------------------------------------------------
| Custom Validators
|--------------------------------------------------------------------------
*/

// Checks if page parent is valid
Validator::extend('pageParent', function($attribute, $value, $parameters)
{
	// check if value is none or if value is valid page id 
	$valid = $value == 'none' || ($page = Hokeo\Vessel\Page::find($value));

	if ($valid && $value != 'none' && isset($parameters[0]))
		$valid = !$page->isSelfOrDescendantOf(Hokeo\Vessel\Page::find($parameters[0]));

	return $valid;
});

// Checks if formatter exists
Validator::extend('formatter', function($attribute, $value, $parameters)
{
	return Hokeo\Vessel\Facades\FormatterManager::registered($value);
});

// Checks if theme template exists
Validator::extend('template', function($attribute, $value, $parameters)
{
	if ($value == 'none') return true;
	
	Hokeo\Vessel\Facades\Theme::load();
	$templates = Hokeo\Vessel\Facades\Theme::getThemeViews();
	return array_key_exists($value, $templates);
});

// Checks if theme exists
Validator::extend('theme', function($attribute, $value, $parameters)
{	
	$themes = Hokeo\Vessel\Facades\Theme::getAvailable();
	return array_key_exists($value, $themes);
});

// Checks if timezone name is valid
Validator::extend('timezone', function($attribute, $value, $parameters)
{
	return in_array($value, \DateTimeZone::listIdentifiers());
});

// Checks if file exists in config upload_path
Validator::extend('uploaded', function($attribute, $value, $parameters)
{
	$upload_path = rtrim(\Config::get('vessel::upload_path', 'public/uploads'), '/');
	$upload_path = ($upload_path[0] == '/') ? $upload_path : base_path($upload_path);

	return \File::exists($upload_path.'/'.$value);
});