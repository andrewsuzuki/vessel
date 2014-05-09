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
		$this->asset->js('js/jquery.ui.widget.js', 'Hokeo/Vessel', 'jquery-ui-widget');

		$this->asset->js('js/tmpl.min.js', 'Hokeo/Vessel', 'blueimp-tmpl');
		$this->asset->js('js/load-image.min.js', 'Hokeo/Vessel', 'blueimp-load-image');
		$this->asset->js('js/canvas-to-blob.min.js', 'Hokeo/Vessel', 'blueimp-canvas-to-blob');
		$this->asset->js('js/jquery.blueimp-gallery.min.js', 'Hokeo/Vessel', 'jquery-blueimp-gallery');

		$this->asset->js('js/fileupload/jquery.iframe-transport.js', 'Hokeo/Vessel', 'jquery-iframe-transport');
		$this->asset->js('js/fileupload/jquery.fileupload.js', 'Hokeo/Vessel', 'jfu-fileupload');
		$this->asset->js('js/fileupload/jquery.fileupload-process.js', 'Hokeo/Vessel', 'jfu-process');
		$this->asset->js('js/fileupload/jquery.fileupload-image.js', 'Hokeo/Vessel', 'jfu-image');
		$this->asset->js('js/fileupload/jquery.fileupload-audio.js', 'Hokeo/Vessel', 'jfu-audio');
		$this->asset->js('js/fileupload/jquery.fileupload-video.js', 'Hokeo/Vessel', 'jfu-video');
		$this->asset->js('js/fileupload/jquery.fileupload-validate.js', 'Hokeo/Vessel', 'jfu-validate');
		$this->asset->js('js/fileupload/jquery.fileupload-ui.js', 'Hokeo/Vessel', 'jfu-ui');

		$this->asset->js('js/fileupload/init.js', 'Hokeo/Vessel', 'jfu-init');
		
		$this->asset->js('js/jquery.xdr-transport.js', 'Hokeo/Vessel', 'jquery-xdr-transport', '(gte IE 8)&(lt IE 10)');
		
		$this->asset->css('css/fileupload/blueimp-gallery.min.css', 'Hokeo/Vessel', 'blueimp-gallery');
		$this->asset->css('css/fileupload/jquery.fileupload.css', 'Hokeo/Vessel', 'jfu-fileupload');
		$this->asset->css('css/fileupload/jquery.fileupload-ui.css', 'Hokeo/Vessel', 'jfu-ui');

		// include blueimp gallery skeleton html
		hook('back.scripts-pre', function() {
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