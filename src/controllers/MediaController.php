<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Validation\Factory;
use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Response;
use Krucas\Notification\Notification;

class MediaController extends Controller
{
	protected $view;

	protected $url;

	protected $input;

	protected $file;

	protected $validator;

	protected $auth;

	protected $config;

	protected $response;

	protected $notification;

	protected $plugin;

	protected $asset;

	protected $upload_path;

	protected $upload_url;

	public function __construct(
		Environment $view,
		UrlGenerator $url,
		Request $input,
		Filesystem $file,
		Factory $validator,
		AuthManager $auth,
		Repository $config,
		Response $response,
		Notification $notification,
		Plugin $plugin,
		Asset $asset)
	{
		$this->view         = $view;
		$this->url          = $url;
		$this->input        = $input;
		$this->file         = $file;
		$this->validator    = $validator;
		$this->auth         = $auth;
		$this->config       = $config;
		$this->response     = $response;
		$this->notification = $notification;
		$this->asset        = $asset;
		$this->plugin       = $plugin;

		$upload_path       = rtrim($this->config->get('vessel::upload_path', 'public/uploads'), '/');
		$this->upload_path = ($upload_path[0] == '/') ? $upload_path : base_path($upload_path);

		$upload_url        = rtrim($this->config->get('vessel::upload_url', 'uploads'), '/');
		$this->upload_url  = ($upload_url[0] == '/') ? $upload_url : url($upload_url);
	}

	/**
	 * Get media page
	 * 
	 * @return response
	 */
	public function getMedia()
	{
		// include blueimp file upload assets
		$this->asset->js(asset('packages/hokeo/vessel/js/jquery.ui.widget.js'), 'jquery-ui-widget');

		$this->asset->js(asset('packages/hokeo/vessel/js/tmpl.min.js'), 'blueimp-tmpl');
		$this->asset->js(asset('packages/hokeo/vessel/js/load-image.min.js'), 'blueimp-load-image');
		$this->asset->js(asset('packages/hokeo/vessel/js/canvas-to-blob.min.js'), 'blueimp-canvas-to-blob');
		$this->asset->js(asset('packages/hokeo/vessel/js/jquery.blueimp-gallery.min.js'), 'jquery-blueimp-gallery');

		$this->asset->js(asset('packages/hokeo/vessel/js/fileupload/jquery.iframe-transport.js'), 'jquery-iframe-transport');
		$this->asset->js(asset('packages/hokeo/vessel/js/fileupload/jquery.fileupload.js'), 'jfu-fileupload');
		$this->asset->js(asset('packages/hokeo/vessel/js/fileupload/jquery.fileupload-process.js'), 'jfu-process');
		$this->asset->js(asset('packages/hokeo/vessel/js/fileupload/jquery.fileupload-image.js'), 'jfu-image');
		$this->asset->js(asset('packages/hokeo/vessel/js/fileupload/jquery.fileupload-audio.js'), 'jfu-audio');
		$this->asset->js(asset('packages/hokeo/vessel/js/fileupload/jquery.fileupload-video.js'), 'jfu-video');
		$this->asset->js(asset('packages/hokeo/vessel/js/fileupload/jquery.fileupload-validate.js'), 'jfu-validate');
		$this->asset->js(asset('packages/hokeo/vessel/js/fileupload/jquery.fileupload-ui.js'), 'jfu-ui');

		$this->asset->js(asset('packages/hokeo/vessel/js/fileupload/init.js'), 'jfu-init');
		
		$this->asset->js(asset('packages/hokeo/vessel/js/jquery.xdr-transport.js'), 'jquery-xdr-transport', '(gte IE 8)&(lt IE 10)');
		
		$this->asset->css(asset('packages/hokeo/vessel/css/fileupload/blueimp-gallery.min.css'), 'blueimp-gallery');
		$this->asset->css(asset('packages/hokeo/vessel/css/fileupload/jquery.fileupload.css'), 'jfu-fileupload');
		$this->asset->css(asset('packages/hokeo/vessel/css/fileupload/jquery.fileupload-ui.css'), 'jfu-ui');

		// include blueimp gallery skeleton html
		$this->plugin->hook('back.scripts-pre', function() {
			return $this->view->make('vessel::partials.blueimp-gallery')->render();
		});

		$this->view->share('title', 'Media');
		return $this->view->make('vessel::media');
	}

	/**
	 * Get uploaded media
	 * 
	 * @return response json response
	 */
	public function getHandle()
	{
		$files = array();

		foreach ($this->file->files($this->upload_path) as $file) // get files in config upload path
		{
			if ($file[0] != '.')
			{
				// if file doesn't start with '.', add to array
	
				$basename = basename($file);

				$files[] = array(
					'name'         => $basename,
					'size'         => $this->file->size($file),
					'url'          => $this->upload_url.'/'.$basename,
					'thumbnailUrl' => $this->upload_url.'/'.$basename, // tmp
					'deleteUrl'    => $this->url->route('vessel.media.handle', array('filename' => $basename)), // tmp
					'deleteType'   => 'DELETE',
				);
			}
		}

		return $this->response->json(array('files' => $files));
	}

	/**
	 * Handle media upload
	 * 
	 * @return response json response
	 */
	public function postHandle()
	{
		// file validation rules
		$rules = array(
			'files.0' => 'required|image|max:3000',
		);

		$validation = $this->validator->make($this->input->all(), $rules); // validate
		
		$file          = $this->input->file('files.0'); // get file object
		$size          = $file->getSize(); // get file size
		$original_name = $file->getClientOriginalName(); // get file name (uploaded as)

		if ($validation->passes())
		{
			$file_parts = explode('.', $original_name); // explode basename by .

			$original_basename = $file_parts[0]; // get basename without extension(s)
			$basename = $original_basename;

			if ($basename) // make sure this filename doesn't start with .
			{
				array_shift($file_parts);
				$extension = implode('.', $file_parts); // make extension(s) string

				// append -i (file-1, file-2, etc) if it already exists (until it doesn't)
				$i = 1;
				while ($this->file->exists($this->upload_path.'/'.$basename.'.'.$extension))
				{
					$basename = $original_basename.'-'.$i;
					$i++;
				}

				// final filename to save as
				$filename = $basename.'.'.$extension;

				// save file from tmp to config upload path
				if ($file->move($this->upload_path, $filename))
				{
					// return response with upload data
					return $this->response->json(array('files' => array(
						array(
							'name'         => $filename,
							'size'         => $size,
							'url'          => $this->upload_url.'/'.$filename,
							'thumbnailUrl' => $this->upload_url.'/'.$filename, // tmp
							'deleteUrl'    => $this->url->route('vessel.media.handle', array('filename' => $filename)), // tmp
							'deleteType'   => 'DELETE',
						),
					)));
				}
				else
				{
					$error = 'An unknown error occurred.';
				}
			}
			else
			{
				$error = 'Filename must not start with a period.';
			}
		}
		else
		{
			$filename = $original_name;
			$error = $validation->errors()->first(); // get validation errors
		}

		// return error json
		return $this->response->json(array('files' => array(
			array(
				'name'  => $filename,
				'size'  => $size,
				'error' => $error,
			),
		)));
	}

	/**
	 * Handle media delete
	 * 
	 * @return response json response
	 */
	public function deleteHandle()
	{
		// file delete validation rules
		$rules = array(
			'filename' => 'required|uploaded',
		);

		$validation = $this->validator->make($this->input->all(), $rules); // validate
		
		$filename = $this->input->get('filename', '[unknown]'); // get filename

		// if filename is specified + exists, delete it
		if ($validation->passes())
			$success = $this->file->delete($this->upload_path.'/'.$filename);

		// return json response
		return $this->response->json(array('files' => array(
			array(
				$filename => $success
			)
		)));
	}
}