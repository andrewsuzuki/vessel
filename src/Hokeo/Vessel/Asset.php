<?php namespace Hokeo\Vessel;

class Asset {

	protected $js;

	protected $css;

	/**
	 * Construct Asset class
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$this->js = array();
		$this->css = array();
	}

	/**
	 * Adds js asset
	 *
	 * See $this->add() for params
	 */
	public function js($source, $name, $if = '')
	{
		return $this->add('js', $source, $name, $if);
	}

	/**
	 * Adds css asset
	 *
	 * See $this->add() for params
	 */
	public function css($source, $name, $if = '')
	{
		return $this->add('css', $source, $name, $if);
	}

	/**
	 * Adds js/css asset
	 * 
	 * @param string $type   'js'/'css'
	 * @param string $source url/path to asset
	 * @param string $name   name of asset (generic name like 'jquery', NOT 'jquery 1.9.2')
	 * @param string $if     (Optional) will surround asset with HTML if statement: <!--[if $if]><![endif]-->
	 */
	public function add($type, $source, $name, $if = '')
	{
		$name = strtolower($name);

		if (isset($this->{$type}) && !isset($this->{$type}[$name]))
		{
			$this->{$type}[$name] = array(
				'source' => $source,
				'if' => $if
			);
			return true;
		}

		return false;
	}

	/**
	 * Get all added js or css
	 *
	 * @param  string     $type 'js'/'css'
	 * @return array|void
	 */
	public function get($type)
	{
		switch ($type)
		{
			case 'js':
				return $this->js;
			case 'css':
				return $this->css;
		}
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

		foreach ($assets as $name => $info)
		{
			$asset = str_replace(':source', $info['source'], $template);

			if (is_string($info['if']) && strlen($info['if']))
				$asset = '<!--[if '.$info['if'].']>'.$asset.'<![endif]-->';

			$html .= $asset."\n";
		}

		$html .= "\n<!-- End assets-".$type." -->\n\n";

		return $html;
	}
}