<?php namespace Hokeo\Vessel;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
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
			'parent' => 'required|pageParent'.(($edit) ? ':'.$edit->id : ''),
			'formatter' => 'required|formatter',
		];
	}

	public function url()
	{
		if ($this->nest_url)
			return URL::to(implode('/', $this->getAncestorsAndSelf()->lists('slug')));
		else
			return URL::to($this->slug);
	}
}