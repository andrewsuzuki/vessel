<?php namespace Hokeo\Vessel;

interface SettingInterface {

	public function path($path); // set path
	public function filename($filename); // set filename
	public function get($searchKey, $fallback); // get
	public function set($key, $value); // set
	public function forget($deleteKey);
	public function has($searchKey);
	public function load($path, $filename);
	public function save($path, $filename);
	public function clear();
	public function setArray(array $data);
}