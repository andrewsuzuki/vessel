<?php namespace Hokeo\Vessel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Menu extends Model {

	protected $table = 'vessel_menus';

	protected $softDelete = false;

	use DateAccessorTrait;

	// Relationships

	public function user()
	{
		return $this->belongsTo('Hokeo\\Vessel\\User');
	}

	public function menuitems()
	{
		return $this->hasMany('Hokeo\\Vessel\\Menuitem');
	}

	// Events

	public static function boot()
	{
		parent::boot();

		// don't allow delete, or update if changing slug, of main menu (last resort)
		
		$prevent_main = function($menu, $deleting)
		{
			$original = $menu->getOriginal();
			if ($original['slug'] == 'main' && ($menu->slug != 'main' || $deleting)) return false;
		};

		static::updating(function($menu) use ($prevent_main) {
			if (!$prevent_main($menu, false)) return false;
		});

		static::deleting(function($menu) use ($prevent_main) {
			if (!$prevent_main($menu, true)) return false;
			$menu->menuitems()->delete(); // delete associated menuitems
		});
	}

	// Methods
	
	/**
	 * Validation rules
	 * 
	 * @param  object|null $edit If editing, pass in the updating menu model
	 * @return array             Rules for validator
	 */
	public static function rules($edit = null)
	{
		return [
			'title'       => 'required',
			'slug'        => 'required|alpha_dash|unique:vessel_menus,slug'.(($edit) ? ','.$edit->id : ''),
			'description' => '',
			'menuitems'   => 'json_string_array'
		];
	}
}