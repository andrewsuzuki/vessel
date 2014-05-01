<?php namespace Hokeo\Vessel;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model {

	protected $table = 'vessel_permissions';

	protected $softDelete = false;

	use DateAccessorTrait;

	// Relations
	
	/**
	 * Roles Many to Many
	 * 
	 * @return object
	 */
	public function roles()
	{
		return $this->belongsToMany('Hokeo\\Vessel\\Role', 'vessel_permission_role');
	}
	
	// Events
	
	public static function boot()
	{
		parent::boot();

		static::deleting(function($permission) {
			$permission->roles()->sync(array()); // unsync roles
		});
	}

	// Methods

	/**
	 * Return native vessel permission names
	 *
	 * TODO: this
	 * 
	 * @return array
	 */
	public function getNative()
	{
		return array();
	}
}