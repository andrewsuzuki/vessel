<?php namespace Hokeo\Vessel;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Routing\UrlGenerator;

class Asset {

	protected $file;

	protected $url;

	protected $base_path;

	protected $url_prefix;

	protected $js;

	protected $css;

	protected $other;

	protected $js_common;

	protected $css_common;

	/**
	 * Construct Asset class
	 * 
	 * @return void
	 */
	public function __construct(Filesystem $file, UrlGenerator $url, $base_path = null)
	{
		$this->file = $file;
		$this->url  = $url;

		if (!$base_path) $base_path = base_path().'/public/assets';
		$this->base_path  = $base_path;
		$this->url_prefix = 'assets';

		$this->js         = array();
		$this->css        = array();
		$this->other      = array();
		$this->js_common  = array();
		$this->css_common = array();
	}

	/**
	 * Adds js asset
	 *
	 * See $this->add() for params
	 */
	public function js($filename, $namespace, $common_name = null, $conditional = '')
	{
		return $this->add($filename, $namespace, 'js', $common_name, $conditional);
	}

	/**
	 * Adds css asset
	 *
	 * See $this->add() for params
	 */
	public function css($filename, $namespace, $common_name = null, $conditional = '')
	{
		return $this->add($filename, $namespace, 'css', $common_name, $conditional);
	}

	/**
	 * Adds non js/css asset
	 *
	 * See $this->add() for params
	 */
	public function other($filename, $namespace)
	{
		return $this->add($filename, $namespace, 'other');
	}

	/**
	 * Queues asset for this request
	 * 
	 * @param string $type        'css'/'js'/'other'
	 * @param string $filename    Filename (published under given namespace)
	 * @param string $namespace   Namespace of asset
	 * @param string $common_name (Optional) common name of js/css asset, for example 'jquery' or 'angularjs',
	 *                            will not add asset if an asset was already added with this name.
	 *                            See cdnjs.com for common library names
	 * @param string $conditional (Optional) will surround js/css asset with HTML if statement: <!--[if $if]><![endif]-->
	 * @return bool               Success
	 */
	public function add($filename, $namespace, $type = null, $common_name = null, $conditional = '')
	{
		if (!$type || !isset($this->{$type})) $type = 'other'; // make type 'other' if not js or css

		if (!$this->commonAssetAdded($type, $common_name))
		{
			$namespace = $this->encodeNamespace($namespace);

			$this->{$type}[] = array(
				'filename'    => $filename,
				'namespace'   => $namespace,
				'common_name' => $common_name,
				'conditional' => $conditional
			);

			// if common, mark as added
			if (($type == 'js' || $type == 'css') && is_string($common_name) && strlen($common_name))
				$this->{$type.'_common'}[$common_name] = true;

			return true;
		}

		return false;
	}

	/**
	 * Get all added js or css
	 *
	 * @param  string     $type 'js'/'css'/'other'
	 * @return array
	 */
	public function getAdded($type)
	{
		switch ($type)
		{
			case 'js':
				return $this->js;
			case 'css':
				return $this->css;
			default:
				return $this->other;
		}
	}

	/**
	 * Check if a common vendor asset ('jquery', 'angularjs', etc) was already added
	 * See cdnjs.com for common library names
	 * 
	 * @param  string $type 'js'/'css'/'other'
	 * @param  string $name Name of asset
	 * @return bool
	 */
	public function commonAssetAdded($type, $name)
	{
		if ($type !== 'js' && $type !== 'css') return false;
		if (!is_string($name) || !strlen($name)) return false;
		return isset($this->{$type.'_common'}[$name]);
	}

	/**
	 * Returns all added js/css in <script>/<link> format
	 * 
	 * @param  string $type 'js'/'css'
	 * @return string       script/link tags (possibly with conditionals)
	 */
	public function make($type)
	{
		if ($type == 'js')
		{
			$alloftype = $this->js;
			$template = '<script type="text/javascript" src=":source"></script>';
		}
		elseif ($type == 'css')
		{
			$alloftype = $this->css;
			$template = '<link rel="stylesheet" type="text/css" href=":source">';
		}
		else
		{
			return '';
		}

		$html = "\n\n<!-- Begin assets-".$type." -->\n\n";

		foreach ($alloftype as $asset)
		{
			$assethtml = str_replace(':source', $this->getUrlFromNamespace($asset['namespace'], $asset['filename'], false), $template);

			if (is_string($asset['conditional']) && strlen($asset['conditional']))
				$assethtml = '<!--[if '.$asset['conditional'].']>'.$assethtml.'<![endif]-->';

			$html .= $assethtml."\n";
		}

		$html .= "\n<!-- End assets-".$type." -->\n\n";

		return $html;
	}


	/**
	 * Publish an asset or directory to the public directory under a namespace (subdirectory)
	 *
	 * @param  string $path      Full path to asset or directory
	 * @param  mixed  $namespace Namespace (vendor/package is preferred, e.g. 'hokeo/vessel' or 'suzuki/project'), or null for global namespace
	 * @param  bool   $force     Whether to overwrite existing assets in namespace with same filename
	 * @return bool              If asset was published (copied) (will return false for existing assets)
	 */
	public function publish($path, $namespace, $force = false)
	{
		// check if asset is already published
		if (!$force && $this->isPublished($path, $namespace)) return false;

		// determine if file or directory
		if ($this->file->isFile($path))
			$is_dir = false;
		elseif ($this->file->isDirectory($path))
			$is_dir = true;
		else
			return false;

		$this->createNamespace($namespace); // create namespace directory if it doesn't exist

		$to = $this->getDirFromNamespace($namespace).DIRECTORY_SEPARATOR.basename($path); // make basename from path

		return ($is_dir) ? $this->file->copyDirectory($path, $to) : $this->file->copy($path, $to); // publish asset / dir
	}

	/**
	 * Remove a published asset from the public directory
	 * 
	 * @return bool
	 */
	public function unpublish($path, $namespace)
	{
		$dir = $this->getDirFromNamespace($namespace).DIRECTORY_SEPARATOR.basename($path);

		if ($this->file->isFile($dir))
			return $this->file->delete($dir); // delete single file asset
		elseif ($this->file->isDirectory($dir))
			return $this->file->deleteDirectory($dir); // or delete sub directory
		else
			return false;
	}

	/**
	 * Check if asset or directory was published in given namespace
	 * 
	 * @return bool
	 */
	public function isPublished($path, $namespace)
	{
		return $this->file->exists($this->getDirFromNamespace($namespace).DIRECTORY_SEPARATOR.basename($path));
	}

	/**
	 * Create an asset namespace
	 *
	 * @param  string $namespace Namespace name
	 * @return bool
	 */
	public function createNamespace($namespace)
	{
		$dir = $this->getDirFromNamespace($namespace); // get asset directory from given namespace
		if ($this->file->isDirectory($dir)) return true;
		return $this->file->makeDirectory($dir, 511, true, false); // make namespace directory if it doesn't exist
	}

	/**
	 * Check if asset namespace directory exists
	 * 
	 * @return bool
	 */
	public function namespaceExists($namespace)
	{
		return $this->file->isDirectory($this->getDirFromNamespace($namespace));
	}

	/**
	 * Delete an entire published asset namespace
	 * 
	 * @return bool
	 */
	public function deleteNamespace($namespace)
	{
		return $this->file->deleteDirectory($this->getDirFromNamespace($namespace));
	}

	/**
	 * Get full path to asset subdirectory, given namespace
	 *
	 * @param  string $namespace Asset namespace
	 * @param  bool   $encode    Whether to encode namespace
	 * @return string
	 */
	public function getDirFromNamespace($namespace, $encode = true)
	{
		return $this->base_path.DIRECTORY_SEPARATOR.(($encode) ? $this->encodeNamespace($namespace) : $namespace);
	}

	/**
	 * Get full url to asset subdirectory, given namespace
	 *
	 * @param  string      $namespace Asset namespace
	 * @param  string|null $filename  Filename to append if desired
	 * @param  bool        $encode    Whether to encode namespace
	 * @return string
	 */
	public function getUrlFromNamespace($namespace, $filename = null, $encode = true)
	{
		return $this->url->to('/'.$this->url_prefix.'/'.(($encode) ? $this->encodeNamespace($namespace) : $namespace)
			.((is_string($filename) && strlen($filename)) ? '/'.$filename : ''));
	}

	/**
	 * Format plugin namespace as asset subdirectory
	 *
	 * @param  string $namespace Asset namespace
	 * @return string
	 */
	public function encodeNamespace($namespace)
	{
		return md5($namespace);
	}
}