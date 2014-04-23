<?php namespace Hokeo\Vessel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Menuitem extends Model {

	protected $table = 'vessel_menuitems';

	protected $softDelete = false;

	use DateAccessorTrait;

	public function menu()
	{
		return $this->belongsTo('Hokeo\\Vessel\\Menu');
	}

	public function page()
	{
		return $this->belongsTo('Hokeo\\Vessel\\Page');
	}

	// Methods
	
	/**
	 * Validation rules
	 * 
	 * @param  object|null $edit If editing, pass in the updating menuitem model
	 * @return array             Rules for validator
	 */
	public static function rules($edit = null)
	{
		return [
			'name' => 'required',
		];
	}
}