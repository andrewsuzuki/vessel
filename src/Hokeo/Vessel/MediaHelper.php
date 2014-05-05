<?php namespace Hokeo\Vessel;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Config\Repository;
use Intervention\Image\Image;

class MediaHelper {

	protected $url;

	protected $file;

	protected $config;

	protected $image;

	protected $plugin;

	protected $upload_path;

	protected $upload_url;

	protected $image_mimes;

	/**
	 * MediaHelper constructor
	 * 
	 * @return void
	 */
	public function __construct(
		UrlGenerator $url,
		Filesystem $file,
		Repository $config,
		Image $image,
		Plugin $plugin)
	{
		$this->url    = $url;
		$this->file   = $file;
		$this->config = $config;
		$this->image  = $image;
		$this->plugin = $plugin;

		// get upload path from config
		$upload_path       = rtrim($this->config->get('vessel::upload_path', 'public/uploads'), '/');
		$this->upload_path = ($upload_path[0] == '/') ? $upload_path : base_path($upload_path);

		// get upload url from config
		$upload_url        = rtrim($this->config->get('vessel::upload_url', 'uploads'), '/');
		$this->upload_url  = ($upload_url[0] == '/') ? $upload_url : url($upload_url);

		// set allowed image mime types
		$this->image_mimes = array('image/gif', 'image/jpeg', 'image/png');
	}

	/**
	 * Get all uploaded media
	 * 
	 * @return array Array of files (structured for jquery file upload)
	 */
	public function all()
	{
		$files = array();

		foreach ($this->file->files($this->upload_path) as $file) // get files in config upload path
		{
			if ($file[0] != '.')
			{
				// if file doesn't start with '.', add to array
	
				$basename = basename($file);

				$arr = array(
					'name'         => $basename,
					'size'         => $this->file->size($file),
					'url'          => $this->url($basename),
					'deleteUrl'    => $this->urlDelete($basename),
					'deleteType'   => 'DELETE',
				);

				// add thumbnail url to return if image
				if ($this->isImage($file)) // if file is an image...
				{
					if ($name = $this->getMainThumbName()) // get main thumbnail name
						$arr['thumbnailUrl'] = $this->url($name.'/'.$basename); // set thumbnail url
				}

				$files[] = $arr;
			}
		}

		return $files;
	}

	/**
	 * Upload a file (from tmp)
	 * 
	 * @return string Filename of uploaded file
	 */
	public function upload($tmp, $filename)
	{
		if (!$this->file->exists($tmp)) throw new \Exception(t('messages.media.not-uploaded-error'));
		// TODO: check file size is less than max
		if (!$filename || !is_string($filename)) throw new \InvalidArgumentException(t('messages.media.name-invalid-error'));

		$file_parts = explode('.', $filename); // explode basename by .

		$original_basename = $file_parts[0]; // get basename without extension(s)
		$basename = $original_basename;

		if (!$basename) throw new \InvalidArgumentException(t('messages.media.name-invalid-error')); // make sure this filename doesn't start with .
		
		array_shift($file_parts); // remove first element (basename without extension(s))
		$extension = implode('.', $file_parts); // make extension(s) into string

		// append -i (file-1, file-2, etc) if it already exists (until it doesn't)
		$i = 1;
		while ($this->file->exists($this->path($basename.'.'.$extension)))
		{
			$basename = $original_basename.'-'.$i;
			$i++;
		}

		$final_filename = $basename.'.'.$extension; // make final filename to save as

		if (!$this->file->move($tmp, $this->path($final_filename))) throw new \Exception;
		// save file from tmp to config upload path

		$this->makeImageThumbs($final_filename); // make thumbnail(s) if it's an image

		return $final_filename; // return final filename
	}

	/**
	 * Delete uploaded file
	 *
	 * @param  string $filename Name of file
	 * @return bool             Success
	 */
	public function delete($filename)
	{
		$file = $this->path($filename); // get full path to file

		if ($this->file->exists($file))
		{
			$is_image = $this->isImage($file);

			if ($this->file->delete($file)) // delete file
			{
				if ($is_image) $this->deleteImageThumbs($filename); // delete thumbnail(s) if they exist

				return true;
			}
		}

		return false;
	}

	/**
	 * Make thumbnails for an image
	 *
	 * @param  string  $filename Path to image file (must be png/jpg/gif)
	 * @return boolean           Success
	 */
	public function makeImageThumbs($filename)
	{
		$file = $this->path($filename); // get full path to file

		if (!$this->isImage($file)) return false; // verify file exists and is an image

		$thumbs = $this->config->get('vessel::thumbnails', array()); // get thumbnail sizes to make

		if (!is_array($thumbs)) return false; // verify thumbs config is an array

		$available_methods = array('resize', 'crop', 'grab'); // available methods for intervention image resizing

		foreach ($thumbs as $name => $thumb) // loop thumbs to make
		{
			if ($name && is_string($name) && is_array($thumb)) // verify thumb name and array
			{
				if (!$this->file->isDirectory($this->upload_path.'/'.$name)) // verify thumbnail subdirectory
					if (!$this->file->makeDirectory($this->upload_path.'/'.$name)) continue; // make directory if it doesn't exist

				if (!isset($thumb[0]) || !in_array($thumb[0], $available_methods)) continue; // verify thumb resize method
				$method = $thumb[0];

				// make thumbnail
				array_shift($thumb); // get params for intervention method (remove first element - the method name)
				$made = $this->image->make($file); // load image
				call_user_func_array(array($made, $method), $thumb); // call intervention resize method
				$made->save($this->upload_path.'/'.$name.'/'.$filename); // save thumb
			}
		}

		return true;
	}

	/**
	 * Delete all thumbnails of an image
	 * 
	 * @return integer Number of thumbnails deleted
	 */
	public function deleteImageThumbs($filename)
	{
		$n = 0;

		foreach ($this->file->directories($this->upload_path) as $dir) // loop thumb directories
		{
			if ($this->file->exists($dir.'/'.$filename))
				if ($this->file->delete($dir.'/'.$filename)) $n++; // delete thumb and increment counter
		}

		return $n;
	}

	/**
	 * Regenerate all image thumbnails
	 * 
	 * @return void
	 */
	public function regenerateImageThumbs()
	{
		// delete all existing thumbnails
		foreach ($this->file->directories($this->upload_path) as $dir)
			$this->file->deleteDirectory($dir);

		// regenerate each original image
		foreach ($this->file->files($this->upload_path) as $file)
			$this->makeImageThumbs(basename($file));
	}

	/**
	 * Get main thumbnail name to use
	 * 
	 * @return string|bool Name of thumbnail (ex. '100x100' or 'small'), or false
	 */
	public function getMainThumbName()
	{
		$name = $this->config->get('vessel::main_thumbnail'); // try to get main thumbnail value

		if (!$name)
		{
			$nails = $this->config->get('vessel::thumbnails'); // or revert to first thumbnail
			if (is_array($nails) && !empty($nails)) $name = key($nails); // verify first thumb
		}

		return ($name) ? $name : false; // return thumbnail name, or false
	}

	/**
	 * Get path to uploaded file
	 * 
	 * @return string Absolute path
	 */
	public function path($filename)
	{
		return $this->upload_path.'/'.$filename;
	}

	/**
	 * Get url to uploaded file
	 * 
	 * @return string Full url
	 */
	public function url($filename)
	{
		return $this->upload_url.'/'.$filename;
	}

	/**
	 * Get url to delete file (with DELETE method)
	 * 
	 * @return string Full url
	 */
	public function urlDelete($filename)
	{
		return $this->url->route('vessel.media.handle', array('filename' => $filename));
	}

	/**
	 * Determine if file exists and is an accepted image type (png/jpeg/gif)
	 * 
	 * @return boolean
	 */
	public function isImage($file)
	{
		return $this->file->exists($file) && in_array($this->getImageMime($file), $this->image_mimes);
	}

	/**
	 * Get mime type of an image file
	 *
	 * @param  string      $file Path to image
	 * @return string|bool       Mime type, or boolean false if it couldn't get mime type (probably not an image)
	 */
	public function getImageMime($file)
	{
		return image_type_to_mime_type(exif_imagetype($file));
	}
}