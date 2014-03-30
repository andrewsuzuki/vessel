<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ApiController extends Controller {

	protected $input;

	protected $response;

	public function __construct(Request $input, Response $response)
	{
		$this->input    = $input;
		$this->response = $response;
	}

	public function flashinput()
	{
		$this->input->flash();
		return $this->response->trip(true);
	}

}