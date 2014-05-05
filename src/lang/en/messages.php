<?php

return array(
	'general' => array(
		'success'             => 'Success.',
		'error'               => 'An error occurred.',
		'save-success'        => ':name was saved succesfully.',
		'save-success-p'      => ':name were saved succesfully.',
		'save-error'          => ':name was not saved due to an error.',
		'save-error-p'        => ':name were not saved due to an error.',
		'delete-success'      => ':name was deleted successfully.',
		'delete-success-p'    => ':name were deleted successfully.',
		'delete-error'        => ':name was not deleted due to an error.',
		'delete-error-p'      => ':name were not deleted due to an error.',
		'concurrency-warning' => 'This has been updated elsewhere since you started editing. Try again to force this edit.',
	),

	'auth' => array(
		'login-success'  => 'You have been logged in as <strong>:name</strong>.',
		'login-error'    => 'Your credentials were incorrect.',
		'logout-success' => 'You have been logged out successfully.',
	),

	'pages' => array(
		'save-success'   => 'Page :title was saved successfully.',
		'delete-success' => 'Page :title was deleted successfully.',
		'drafts' => array(
			'save-success'   => 'Draft :title was saved successfully.',
			'delete-success' => 'Draft was deleted successfully.',
		),
		'edits' => array(
			'delete-success' => 'Edit was deleted successfully.',
		),
	),

	'blocks' => array(
		'save-success'        => 'Block :title was saved succesfully.',
		'delete-success'      => 'Block :title was deleted succesfully.',
	),

	'menus' => array(
		'save-success'       => 'Menu :title was saved successfully.',
		'delete-success'     => 'Menu :title was deleted successfully.',
		'delete-error'       => 'You cannot delete the main menu.',
		'mapper-dne-error'   => 'Specified menu mapper does not exist.',
		'delete-items-error' => 'There was an error saving the menu\'s items.',
	),

	'media' => array(
		'not-uploaded-error' => 'File was not uploaded.',
		'name-invalid-error' => 'File name was invalid.',
	),

	'users' => array(
		'save-success'   => 'User was saved successfully.',
		'delete-success' => 'User was deleted successfully.',
		'delete-error'   => 'You cannot delete your own user account.',
	),

	'roles' => array(
		'save-success'   => 'Role :name was saved successfully.',
		'delete-success' => 'Role :name was deleted successfully.',
		'delete-error'   => 'You cannot delete native roles.',
	),

	'settings' => array(
		'save-success' => 'Settings were saved successfully.',
	),

	'user_settings' => array(
		'save-success' => 'Your user settings were saved successfully.',
	),

	'formatters' => array(
		'does-not-exist-error'   => 'Formatter is not registered or does not exist.',
		'not-correct-type-error' => 'Formatter is not of the correct type.',
	),

	'plugins' => array(
		'hook-not-valid-error' => 'Hook ":name" was not valid.',
	),

	'misc' => array(
		'dir-must-be-string-error' => 'Directory path must be string.',
	),
);