<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ApiController extends Controller {

	protected $input;

	public function __construct(Request $input)
	{
		$this->input = $input;
		// Can't use an injected Response for now (doesn't have non-static __call magic)
	}

	public function flashinput()
	{
		$this->input->flash();
		return Response::trip(true);
	}
}