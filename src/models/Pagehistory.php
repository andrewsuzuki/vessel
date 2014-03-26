<?php namespace Hokeo\Vessel;

use Baum\Node;

class Pagehistory extends Node {

	protected $table = 'vessel_pagehistories';

	protected $softDelete = false;

	use DateAccessorTrait;

	public function page()
	{
		return $this->belongsTo('Hokeo\Vessel\Page', 'page_id');
	}

	public function user()
	{
		return $this->belongsTo('Hokeo\Vessel\User');
	}
}