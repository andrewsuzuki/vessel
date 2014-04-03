<?php namespace Hokeo\Vessel;

interface FormatterInterface
{
	public function fmName();
	public function fmFor();

	public function fmInterface($raw, $made);
	public function fmProcess();

	public function fmSetup();
	public function fmUse($raw, $made);
}