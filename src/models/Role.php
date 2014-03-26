<?php namespace Hokeo\Vessel;

// use Illuminate\Database\Eloquent\Model;
use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole {

	protected $table = 'vessel_roles';

	protected $softDelete = false;

	use DateAccessorTrait;
	
	public static function rules($edit = null)
	{
		return [
			'title' => 'required',
			'slug' => 'required|alpha_dash|unique:vessel_pages,slug'.(($edit) ? ','.$edit->id : '')
		];
	}
}