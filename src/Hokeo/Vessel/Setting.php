<?php namespace Hokeo\Vessel;

use Hokeo\Vessel\Vessel;
use Philf\Setting\Setting as PSetting;

class Setting extends PSetting {

	protected $vessel;

	public function __construct(Vessel $vessel)
	{
		$this->vessel = $vessel;

		parent::__construct($this->vessel->path('/'), 'settings.json', null);
	}
}