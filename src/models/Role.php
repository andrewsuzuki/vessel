<?php namespace Hokeo\Vessel;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole {

	protected $table = 'roles';

	protected $softDelete = false;

	use DateAccessorTrait;
	
	public static function rules($edit = null)
	{
		return [
			'name' => 'required|unique:roles,name'.(($edit) ? ','.$edit->name : ''),
		];
	}
}