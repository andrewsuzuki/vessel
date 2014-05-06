<?php

/*
|--------------------------------------------------------------------------
| Helper functions
|--------------------------------------------------------------------------
*/

/**
 * Alias of Facades\Translator::get
 */
if (!function_exists('t'))
{
	function t($key = '')
	{
		if (!$key || !is_string($key)) return false;
		if (defined('V_TEST_NOW')) return $key;
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
		if (defined('V_TEST_NOW')) return $key;
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
		return \Auth::check() && \Auth::user()->can($key);
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