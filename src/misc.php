<?php

/*
|--------------------------------------------------------------------------
| Misc extensions, functions, helpers, etc
|--------------------------------------------------------------------------
*/

/**
 * Match @v in blade templates (v())
 */
Blade::extend(function($view, $compiler)
{
	$pattern = $compiler->createMatcher('v');

	return preg_replace_callback($pattern, function($replace) {
		if (substr($replace[2], 0, 1) == '(' && substr($replace[2], -1, 1) == ')')
			return $replace[1].'<?php v'.$replace[2].'; ?>';
	}, $view);
});

/**
 * Match @t in blade templates (t())
 */
Blade::extend(function($view, $compiler)
{
	$pattern = $compiler->createMatcher('t');

	return preg_replace_callback($pattern, function($replace) {
		if (substr($replace[2], 0, 1) == '(' && substr($replace[2], -1, 1) == ')')
			return $replace[1].'<?php echo t'.$replace[2].'; ?>';
	}, $view);
});

/**
 * Match @c in blade templates (c())
 */
Blade::extend(function($view, $compiler)
{
	$pattern = $compiler->createMatcher('c');

	return preg_replace_callback($pattern, function($replace) {
		if (substr($replace[2], 0, 1) == '(' && substr($replace[2], -1, 1) == ')')
			return $replace[1].'<?php echo c'.$replace[2].'; ?>';
	}, $view);
});

/**
 * Alias of Facades\Translator::get
 */
if (!function_exists('t'))
{
	function t($key = '')
	{
		if (!$key || !is_string($key)) return false;
		return call_user_func_array(array('Hokeo\\Vessel\\Facades\\Translator', 'get'), func_get_args());
	}
}

/**
 * Alias of Facades\Translator::choice
 */
if (!function_exists('c'))
{
	function c($key = '')
	{
		if (!$key || !is_string($key)) return false;
		return call_user_func_array(array('Hokeo\\Vessel\\Facades\\Translator', 'choice'), func_get_args());
	}
}


/**
 * Alias of \Auth::user()->can()
 */
if (!function_exists('can'))
{
	function can($key)
	{
		return \Auth::user()->can($key);
	}
}

/**
 * Echos vr() (see below)
 */
if (!function_exists('v'))
{
	function v()
	{
		echo call_user_func_array('vr', func_get_args());
	}
}

/**
 * Alias of Facades\Theme::element()
 * 
 * @return string|null
 */
if (!function_exists('vr'))
{
	function vr()
	{
		return call_user_func_array(array('Hokeo\\Vessel\\Facades\\Theme', 'element'), func_get_args());
	}
}

/**
 * Alias of Facades\Plugin::hook()
 */
if (!function_exists('hook'))
{
	function hook()
	{
		return call_user_func_array(array('Hokeo\\Vessel\\Facades\\Plugin', 'hook'), func_get_args());
	}
}

/**
 * Alias of Facades\Plugin::fire()
 * 
 * @return string|null
 */
if (!function_exists('fire'))
{
	function fire()
	{
		return call_user_func_array(array('Hokeo\\Vessel\\Facades\\Plugin', 'fire'), func_get_args());
	}
}