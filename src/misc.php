<?php

/*
|--------------------------------------------------------------------------
| Misc extensions, functions, helpers, etc
|--------------------------------------------------------------------------
*/

/**
 * Match @v in blade templates
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
 * Alias or Facades\Theme::element()
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
 * Alias or Facades\Plugin::hook()
 */
if (!function_exists('hook'))
{
	function hook()
	{
		return call_user_func_array(array('Hokeo\\Vessel\\Facades\\Plugin', 'hook'), func_get_args());
	}
}

/**
 * Alias or Facades\Plugin::fire()
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