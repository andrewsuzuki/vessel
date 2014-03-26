<?php

/*
|--------------------------------------------------------------------------
| View Composers and Creators
|--------------------------------------------------------------------------
*/

View::creator('vessel::layout', function($view)
{
	\Hokeo\Vessel\FormatterFacade::useAssets();
});