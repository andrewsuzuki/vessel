<?php namespace Hokeo\Vessel;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Block extends Model {

	protected $table = 'vessel_blocks';

	protected $softDelete = false;

	use DateAccessorTrait;

	// Relationships

	public function user()
	{
		return $this->belongsTo('Hokeo\\Vessel\\User');
	}

	// Scopes

	public function scopeActive($query) {return $query->where('active', true); }
	public function scopeNotActive($query) {return $query->where('Active', false); }

	// Events
	
	public static function boot()
    {
        parent::boot();

        static::deleted(function($page)
        {
        	// delete content
        	
        	// hook?
        });
    }

	// Methods
	
	/**
	 * Validation rules
	 * 
	 * @param  object|null $edit If editing, pass in the updating block model
	 * @return array             Rules for validator
	 */
	public static function rules($edit = null)
	{
		return [
			'title' => 'required',
			'slug' => 'required|alpha_dash|unique:vessel_blocks,slug'.(($edit) ? ','.$edit->id : ''),
			'description' => '',
			'formatter' => 'required|formatter',
		];
	}
}