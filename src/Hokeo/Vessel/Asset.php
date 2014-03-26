<?php namespace Hokeo\Vessel;

class Asset {

	protected $js = array();

	protected $css = array();

	/**
	 * Adds js asset
	 *
	 * See $this->add() for params
	 */
	public function js($source, $name)
	{
		return $this->add('js', $source, $name);
	}

	/**
	 * Adds css asset
	 *
	 * See $this->add() for params
	 */
	public function css($source, $name)
	{
		return $this->add('css', $source, $name);
	}

	/**
	 * Adds js/css asset
	 * 
	 * @param string $type   js/css
	 * @param string $source url/path to asset
	 * @param string $name   name of asset (generic name like 'jquery', NOT 'jquery 1.9.2')
	 */
	protected function add($type, $source, $name)
	{
		$name = strtolower($name);

		if (!isset($this->{$type}[$name]))
		{
			$this->{$type}[$name] = $source;
			return true;
		}

		return false;
	}

	public function make($type)
	{
		if ($type == 'js')
		{
			$assets = $this->js;
			$template = '<script type="text/javascript" src=":source"></script>';
		}
		else
		{
			$assets = $this->css;
			$template = '<link rel="stylesheet" type="text/css" href=":source">';
		}

		$html = "\n\n<!-- Begin assets-".$type." -->\n\n";

		foreach ($assets as $name => $source)
		{
			$html .= str_replace(':source', $source, $template)."\n";
		}

		$html .= "\n<!-- End assets-".$type." -->\n\n";

		return $html;
	}
}