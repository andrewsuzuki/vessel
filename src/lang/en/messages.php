<?php

return array(

	'general' => array(
		'success'          => 'Success.',
		'error'            => 'An error occurred.',
		'save-success'     => ':name was saved succesfully.',
		'save-success-p'   => ':name were saved succesfully.',
		'save-error'       => ':name was not saved due to an error.',
		'save-error-p'     => ':name were not saved due to an error.',
		'delete-success'   => ':name was deleted successfully.',
		'delete-success-p' => ':name were deleted successfully.',
		'delete-error'     => ':name was not deleted due to an error.',
		'delete-error-p'   => ':name were not deleted due to an error.',
	),

	'auth' => array(
		'login-success'  => 'You have been logged in as <strong>:name</strong>.',
		'login-error'    => 'Your credentials were incorrect.',
		'logout-success' => 'You have been logged out successfully.',
	),

	'pages' => array(
		'edit' => array(
			'concurrency-warning' => 'This page has been updated elsewhere since you started editing. Click save again to force this edit.',
		),
	),

	'blocks' => array(
		'edit' => array(
			'concurrency-warning' => 'This block has been updated elsewhere since you started editing. Click save again to force this edit.',
		),
	),

	'menus' => array(
		'delete' => array(
			'error' => 'You cannot delete the main menu.',
			'items-error' => 'There was an error saving the menu\'s items.',
		),
	),

	'media' => array(
		'upload' => array(
			'not-uploaded-error' => 'File was not uploaded.',
			'name-invalid-error' => 'File name was invalid.',
		),
	),

	'users' => array(
		'save-success'   => 'User was saved successfully.',
		'delete-success' => 'User was deleted successfully.',
		'delete-error'   => 'You cannot delete your own user account.',
	),

	'roles' => array(
		'delete' => array(
			'error' => 'You cannot delete native roles.',
		),
	),

);