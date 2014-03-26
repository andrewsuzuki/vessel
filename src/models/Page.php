<?php namespace Hokeo\Vessel;

// use Illuminate\Database\Eloquent\Model;
use Baum\Node;

class Page extends Node {

	protected $table = 'vessel_pages';

	protected $softDelete = false;

	use DateAccessorTrait;

	public function history()
	{
		return $this->hasMany('Hokeo\Vessel\Pagehistory', 'vessel_pagehistories');
	}

	public function user()
	{
		return $this->belongsTo('Hokeo\Vessel\User');
	}
	
	public static function rules($edit = null)
	{
		return [
			'title' => 'required',
			'slug' => 'required|alpha_dash|unique:vessel_pages,slug'.(($edit) ? ','.$edit->id : ''),
			'description' => '',
			'parent' => 'pageParent'.(($edit) ? ':'.$edit->id : '')
		];
	}
}