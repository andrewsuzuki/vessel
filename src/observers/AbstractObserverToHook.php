<?php namespace Hokeo\Vessel\Observer;

abstract class AbstractObserverToHook {

	protected $name = '';

	public function getName($event)
	{
		return 'model.'.$this->name.'.'.$event;
	}

	public function fire($event, $model)
	{
		\Hokeo\Vessel\Facades\Plugin::fire($this->getName($event), array($model), true);
	}

	public function creating($model)
	{
		$this->fire('creating', $model);
	}

	public function created($model)
	{
		$this->fire('created', $model);
	}

	public function updating($model)
	{
		$this->fire('updating', $model);
	}

	public function updated($model)
	{
		$this->fire('updated', $model);
	}

	public function saving($model)
	{
		$this->fire('saving', $model);
	}

	public function saved($model)
	{
		$this->fire('saved', $model);
	}

	public function deleting($model)
	{
		$this->fire('deleting', $model);
	}

	public function deleted($model)
	{
		$this->fire('deleted', $model);
	}

	public function restoring($model)
	{
		$this->fire('restoring', $model);
	}

	public function restored($model)
	{
		$this->fire('restored', $model);
	}
}