<?php

// Form and HTML macros

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
