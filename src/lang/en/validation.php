<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Native Validation Language Lines
	|--------------------------------------------------------------------------
	*/

	'accepted'         => 'The :attribute must be accepted.',
	'active_url'       => 'The :attribute is not a valid URL.',
	'after'            => 'The :attribute must be a date after :date.',
	'alpha'            => 'The :attribute may only contain letters.',
	'alpha_dash'       => 'The :attribute may only contain letters, numbers, and dashes.',
	'alpha_num'        => 'The :attribute may only contain letters and numbers.',
	'array'            => 'The :attribute must be an array.',
	'before'           => 'The :attribute must be a date before :date.',
	'between'          => array(
		'numeric' => 'The :attribute must be between :min and :max.',
		'file'    => 'The :attribute must be between :min and :max kilobytes.',
		'string'  => 'The :attribute must be between :min and :max characters.',
		'array'   => 'The :attribute must have between :min and :max items.',
	),
	'confirmed'        => 'The :attribute confirmation does not match.',
	'date'             => 'The :attribute is not a valid date.',
	'date_format'      => 'The :attribute does not match the format :format.',
	'different'        => 'The :attribute and :other must be different.',
	'digits'           => 'The :attribute must be :digits digits.',
	'digits_between'   => 'The :attribute must be between :min and :max digits.',
	'email'            => 'The :attribute format is invalid.',
	'exists'           => 'The selected :attribute is invalid.',
	'image'            => 'The :attribute must be an image.',
	'in'               => 'The selected :attribute is invalid.',
	'integer'          => 'The :attribute must be an integer.',
	'ip'               => 'The :attribute must be a valid IP address.',
	'max'              => array(
		'numeric' => 'The :attribute may not be greater than :max.',
		'file'    => 'The :attribute may not be greater than :max kilobytes.',
		'string'  => 'The :attribute may not be greater than :max characters.',
		'array'   => 'The :attribute may not have more than :max items.',
	),
	'mimes'            => 'The :attribute must be a file of type: :values.',
	'min'              => array(
		'numeric' => 'The :attribute must be at least :min.',
		'file'    => 'The :attribute must be at least :min kilobytes.',
		'string'  => 'The :attribute must be at least :min characters.',
		'array'   => 'The :attribute must have at least :min items.',
	),
	'not_in'           => 'The selected :attribute is invalid.',
	'numeric'          => 'The :attribute must be a number.',
	'regex'            => 'The :attribute format is invalid.',
	'required'         => 'The :attribute field is required.',
	'required_if'      => 'The :attribute field is required when :other is :value.',
	'required_with'    => 'The :attribute field is required when :values is present.',
	'required_without' => 'The :attribute field is required when :values is not present.',
	'same'             => 'The :attribute and :other must match.',
	'size'             => array(
		'numeric' => 'The :attribute must be :size.',
		'file'    => 'The :attribute must be :size kilobytes.',
		'string'  => 'The :attribute must be :size characters.',
		'array'   => 'The :attribute must contain :size items.',
	),
	'unique'           => 'The :attribute has already been taken.',
	'url'              => 'The :attribute format is invalid.',

	/*
	|--------------------------------------------------------------------------
	| Vessel Validation Language Lines
	|--------------------------------------------------------------------------
	*/

	'page_parent'       => 'The specified page parent was not valid.',
	'formatter'         => 'The specified formatter was not valid.',
	'template'          => 'The specified template was not valid.',
	'home_page_id'      => 'The specified homepage was not valid.',
	'theme'             => 'The specified theme was not valid.',
	'timezone'          => 'The specified timezone was not valid.',
	'menu_mapper'       => 'The specified menu mapper was not valid.',
	'role'              => 'The specified role was not valid.',
	'roles'             => 'The specified user roles were not valid.',
	'permissions'       => 'The specified role permissions were not valid.',
	'username'          => 'The username must contain alphanumeric characters, underscores, and hyphens.',
	'checked'           => 'The :attribute checkbox must be checked.',
	'uploaded'          => 'The file was not uploaded.',
	'json_string_array' => 'The json data array was not valid.',

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	*/

	'custom' => array(),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	*/

	'attributes' => array(
		// 'email' => 'e-mail address',
	),

);
