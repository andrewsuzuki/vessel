<?php

/*
|--------------------------------------------------------------------------
| Custom Validators
|--------------------------------------------------------------------------
*/

// Checks if page parent is valid
Validator::extend('pageParent', function($attribute, $value, $parameters)
{	
	if (isset($parameters[1]) && $parameters[1] == 'true') // check if this is the home page
	{
		$valid = $value == 'none';
	}
	else
	{
		// make sure it's a page id
		$valid = $value == 'none' || ($page = Hokeo\Vessel\Page::find($value));

		// make sure page id is not self or descendant of this page
		if ($valid && $value != 'none' && isset($parameters[0]))
			$valid = !$page->isSelfOrDescendantOf(Hokeo\Vessel\Page::find($parameters[0]));
	}

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

// Checks if checkbox was checked (equal to '1', 'on', 'true', 'yes')
Validator::extend('checked', function($attribute, $value, $parameters)
{
	return in_array(strtolower($value), array('1', 'on', 'true', 'yes'));
});

// Checks if page id exists, is public, and is root
Validator::extend('home_page_id', function($attribute, $value, $parameters)
{
	$page = Hokeo\Vessel\Page::find($value);
	return $page && $page->visible && $page->isRoot();
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