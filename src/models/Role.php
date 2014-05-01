<?php namespace Hokeo\Vessel;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

	protected $table = 'vessel_roles';

	protected $softDelete = false;

	use DateAccessorTrait;

	// Relations
	
	/**
	 * Users Many to Many
	 * 
	 * @return object
	 */
	public function users()
	{
		return $this->belongsToMany('Hokeo\\Vessel\\User', 'vessel_role_user');
	}
	
	/**
	 * Permissions Many to Many
	 * 
	 * @return object
	 */
	public function permissions()
	{
		return $this->belongsToMany('Hokeo\\Vessel\\Permission', 'vessel_permission_role');
	}
	
	// Events
	
	public static function boot()
	{
		parent::boot();

		static::deleting(function($role) {
			if (in_array($role->name, $role->getNative())) return false;
			$role->permissions()->sync(array()); // unsync permissions
			$role->users()->sync(array()); // unsync users
			return true;
		});
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