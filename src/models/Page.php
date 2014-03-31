<?php namespace Hokeo\Vessel;

use Illuminate\Support\Facades\URL;
use Baum\Node;

class Page extends Node {

	protected $table = 'vessel_pages';

	protected $softDelete = false;

	use DateAccessorTrait;

	// Relationships

	public function history()
	{
		return $this->hasMany('Hokeo\\Vessel\\Pagehistory');
	}

	public function user()
	{
		return $this->belongsTo('Hokeo\\Vessel\\User');
	}

	// Scopes

	public function scopeVisible($query) {return $query->where('visible', true); }
	public function scopeNotVisible($query) {return $query->where('visible', false); }

	public function scopeMenu($query) {return $query->where('in_menu', true); }
	public function scopeNotMenu($query) {return $query->where('in_menu', false); }

	public function scopeRoot($query) {return $query->where('parent_id', null); }
	public function scopeNotRoot($query) {return $query->where('parent_id', '!=', null); }

	public function scopeBaseTemplate($query) {return $query->where('template', null); }
	public function scopeNotBaseTemplate($query) {return $query->where('template', '!=', null); }

	// Events
	
	public static function boot()
    {
        parent::boot();

        static::deleted(function($page)
        {
        	// delete edits and drafts
        	$page->history()->delete();
        	// delete content
        	Facades\PageHelper::deleteContent($page->id);

        	// hook here
        });
    }

	// Methods
	
	/**
	 * Validation rules
	 * 
	 * @param  object|null $edit If editing, pass in the updating page model
	 * @return array             Rules for validator
	 */
	public static function rules($edit = null)
	{
		return [
			'title' => 'required',
			'slug' => 'required|alpha_dash|unique:vessel_pages,slug'.(($edit) ? ','.$edit->id : ''),
			'description' => '',
			'parent' => 'required|pageParent'.(($edit) ? ':'.$edit->id : ''),
			'formatter' => 'required|formatter',
			'template' => 'required|template',
		];
	}

	/**
	 * Generates url to page
	 *
	 * @param  bool|null $nest true=Return forced nested path|false=Return forced non-nested path|null=Return based on nesting option
	 * @return string
	 */
	public function url($nest = null)
	{
		if (($nest === true) || ($nest === null && $this->nest_url))
			return URL::to(implode('/', $this->getAncestorsAndSelf()->lists('slug')));
		else
			return URL::to($this->slug);
	}
}