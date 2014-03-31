<?php namespace Hokeo\Vessel;

use Illuminate\Database\Eloquent\Model;

class Pagehistory extends Model {

	protected $table = 'vessel_pagehistories';

	protected $softDelete = false;

	public $timestamps = false;

	use DateAccessorTrait;

	public function page()
	{
		return $this->belongsTo('Hokeo\Vessel\Page', 'page_id');
	}

	public function user()
	{
		return $this->belongsTo('Hokeo\Vessel\User');
	}

	// Scopes

	public function scopeDraft($query) {return $query->where('is_draft', true); }
	public function scopeNotDraft($query) {return $query->where('is_draft', false); }
}