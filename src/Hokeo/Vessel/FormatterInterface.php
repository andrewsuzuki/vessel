<?php namespace Hokeo\Vessel;

interface FormatterInterface
{
	public function name();
	public function forTypes();

	public function setupInterface();
	public function getInterface($raw, $made);

	public function submit();
	public function make($raw, $made);
}