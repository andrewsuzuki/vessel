<?php namespace Hokeo\Vessel;

interface FormatterInterface
{
	public function useAssets();
	public function getEditorHtml();
	public function render($string);
}