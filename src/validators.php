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
	return Hokeo\Vessel\FormatterFacade::exists($value);
});