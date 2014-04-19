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

);