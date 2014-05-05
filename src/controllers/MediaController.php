<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Http\Request;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Auth\AuthManager;
use Illuminate\Config\Repository;
use Illuminate\Support\Facades\Response;

class MediaController extends Controller
{
	protected $view;

	protected $url;

	protected $input;

	protected $file;

	protected $auth;

	protected $config;

	protected $response;

	protected $plugin;

	protected $asset;

	protected $mediahelper;

	public function __construct(
		Environment $view,
		UrlGenerator $url,
		Request $input,
		Filesystem $file,
		AuthManager $auth,
		Repository $config,
		Response $response,
		Plugin $plugin,
		Asset $asset,
		MediaHelper $mediahelper)
	{
		$this->view         = $view;
		$this->url          = $url;
		$this->input        = $input;
		$this->file         = $file;
		$this->auth         = $auth;
		$this->config       = $config;
		$this->response     = $response;
		$this->asset        = $asset;
		$this->plugin       = $plugin;
		$this->mediahelper  = $mediahelper;
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
	 * Get all uploaded media
	 * 
	 * @return response json response
	 */
	public function getHandle()
	{
		return $this->response->json(array('files' => $this->mediahelper->all()));
	}

	/**
	 * Handle media upload
	 * 
	 * @return response json response
	 */
	public function postHandle()
	{
		$filename = '';
		$size     = 0;

		try
		{
			if (!$this->input->hasFile('files.0')) throw new \Exception(t('messages.media.not-uploaded-error')); // check file input
			$file     = $this->input->file('files.0'); // get file object
			$filename = $this->mediahelper->upload($file->getRealPath(), basename($file->getClientOriginalName())); // save + make thumbs if image
			$file     = $this->mediahelper->path($filename); // get new file path
			$return   = array(
				'name'         => $filename,
				'size'         => $this->file->size($file),
				'url'          => $this->mediahelper->url($filename),
				'deleteUrl'    => $this->mediahelper->urlDelete($filename),
				'deleteType'   => 'DELETE',
			);

			// add thumbnail url to return if image
			if ($this->mediahelper->isImage($file)) // if file is an image...
			{
				if ($name = $this->mediahelper->getMainThumbName()) // get main thumbnail name
					$return['thumbnailUrl'] = $this->mediahelper->url($name.'/'.$filename); // set thumbnail url
			}
		}
		catch (\Exception $e)
		{
			// return exception
			$return = array(
				'name'  => $filename,
				'size'  => $size,
				'error' => $e->getMessage(),
			);
		}

		return $this->response->json(array('files' => array($return))); // return json response
	}

	/**
	 * Handle media delete
	 * 
	 * @return response json response
	 */
	public function deleteHandle()
	{		
		$filename = $this->input->get('filename'); // get filename

		$success = $filename && $this->mediahelper->delete($filename); // try to delete file

		// return json response with success boolean
		return $this->response->json(array('files' => array(
			array(
				$filename => $success
			)
		)));
	}
}