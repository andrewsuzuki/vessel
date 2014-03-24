<?php namespace Hokeo\Vessel;

interface EngineInterface {

	public function render($template, $data);
	public function extractMeta($template);
	public function parseMeta($meta);
	public function minify($template);

}