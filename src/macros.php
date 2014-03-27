<?php

/*
|--------------------------------------------------------------------------
| Response, Form, and HTML macros
|--------------------------------------------------------------------------
*/

// Response macros

Response::macro('trip', function($send = array(), $message = null)
{
	if (is_bool($send))
	{
		$send = array('success' => $send);

		if (is_string($message))
		{
			$send['message'] = $message;
		}
	}
	elseif (!is_array($send))
	{
		$send = array('success' => true);
	}
	
	if (!isset($send['success']))
		$send['success'] = true;

    $send['success'] = (bool) $send['success'];

    if (!isset($send['message']) || !is_string($send['message']))
		$send['message'] = '';

	if ($send['success'] === false && $send['message'] == '')
		$send['message'] = 'An unknown error occurred. Please try again.';
	
	if (!isset($send['data']) || !is_array($send['data']))
		$send['data'] = array();

	if (!isset($send['entities']) || !is_array($send['entities']))
		$send['entities'] = array();

	return Response::json($send);
});

// Form macros

Form::macro('selectPageParent', function($name, $thispage = null, array $attributes = array())
{
	$attributes['name'] = $name;
	$attributes['id']   = $name;

	$html  = '<select '.HTML::attributes($attributes).'>';
	$html .= '<option value="none">-- No parent --</option>';

	$selected = Input::get($name);

	if (!$selected && $thispage && ($parent = $thispage->parent()->first())) $selected = $parent->id;

	foreach (Hokeo\Vessel\Page::all() as $page)
	{
	    $html .= '<option value="'.$page->id.'" '.(($thispage && $page->isSelfOrDescendantOf($thispage)) ? 'disabled' : '').' '.(($selected == $page->id) ? 'selected' : '').'>';
	    $html .= str_repeat('&mdash;&nbsp;', $page->getLevel()).$page->title;
	    $html .= '</option>';
	}

	return $html.'</select>';
});
