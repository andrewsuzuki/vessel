<?php

return array(

	/**
	 * Package URI
	 *
	 * @type string
	 */
	'uri' => 'vessel',

	/**
	 * Default date format
	 *
	 * @type string
	 */
	'date_format' => 'M j, Y',

	/**
	 * Path to store media uploads
	 *
	 * If character 0 is not '/' (ergo not absolute), then it will prepend the laravel base path
	 * 
	 * @type string
	 */
	'upload_path' => 'public/uploads',

	/**
	 * URL to access media uploads
	 *
	 * If character 0 is not '/' (ergo from domain root), then it will prepend the laravel base url
	 * 
	 * @type string
	 */
	'upload_url' => 'uploads',

	/**
	 * Thumbnails to make of uploaded images.
	 *
	 * Takes any number of arrays specifying:
	 * KEY (name/subdirectory for thumbnail)
	 * 'resize', 'crop', or 'grab' (intelligent crop+resize)
	 * width (pixels, or on resize mode, null for using ratio with other value)
	 * height (pixels, or on resize mode, null)
	 * additional params (upsizing, x/y position, etc, see below)
	 * 
	 * See the following urls for more information:
	 * http://image.intervention.io/methods/resize
	 * http://image.intervention.io/methods/grab
	 */
	'thumbnails' => array(
		'100x100'  => array('grab', 100, 100),
		'300xauto' => array('resize', 300, null, true),
	),

	/**
	 * Thumbnail to use as main backend thumbnail (should be a small ~100px size)
	 */
	'main_thumbnail' => '100x100',

);