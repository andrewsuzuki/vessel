<?php namespace Hokeo\Vessel;

interface FormatterInterface
{
	public function getName();
	public function useAssets();
	public function getEditorHtml($content = null);
	public function isCompiled();
	public function render($string);
}