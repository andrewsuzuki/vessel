<?php

/*
|--------------------------------------------------------------------------
| Misc extensions, etc
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
