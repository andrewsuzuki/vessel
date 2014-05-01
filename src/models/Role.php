<?php namespace Hokeo\Vessel;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole {

	protected $table = 'roles';

	protected $softDelete = false;

	use DateAccessorTrait;
	
	// Events
	
	public function beforeDelete($forced = false)
	{
		if (in_array($this->name, $this->getNative())) return false;
		parent::beforeDelete($forced);
	}

	// Rules

	public static function rules($edit = null)
	{
		$base = array(
			'name'             => 'required|unique:roles,name'.(($edit) ? ','.$edit->id : ''),
			'role_permissions' => 'required|array|permissions',
		);

		return $base;
	}

	// Methods

	/**
	 * Return native vessel role names
	 * 
	 * @return array
	 */
	public function getNative()
	{
		return array('Admin', 'User');
	}
}