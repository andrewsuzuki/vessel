<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Illuminate\Routing\Redirector;
use Krucas\Notification\Notification;

class BlockController extends Controller
{
	protected $view;

	protected $input;

	protected $auth;

	protected $redirect;

	protected $notification;

	protected $blockhelper;

	protected $fm;

	protected $theme;

	protected $block; // model

	public function __construct(
		Environment $view,
		Request $input,
		AuthManager $auth,
		Redirector $redirect,
		Notification $notification,
		BlockHelper $blockhelper,
		FormatterManager $fm,
		Theme $theme,
		Block $block)
	{
		$this->view         = $view;
		$this->input        = $input;
		$this->auth         = $auth;
		$this->redirect     = $redirect;
		$this->notification = $notification;
		$this->blockhelper  = $blockhelper;
		$this->fm           = $fm;
		$this->theme        = $theme;
		$this->block        = $block;
	}

	public function getBlocks()
	{
		$blocks = $this->block->with('user')->get();
		$this->view->share('title', 'Blocks');
		return $this->view->make('vessel::blocks')->with(compact('blocks'));
	}

	public function getBlockNew()
	{
		$mode = 'new';
		$block = $this->block->newInstance();

		$this->view->share('title', 'New Block');

		$formatter = $this->fm->tryEach(
			$this->input->get('formatter'),
			$this->input->old('formatter')
			);

		$interface = $formatter->fmInterface('', '');

		$formatter->fmSetup();

		$formatters_select_array = $this->fm->filterForSelect('block');
		$formatter_current = get_class($formatter);

		return $this->view->make('vessel::block')->with(compact('block', 'mode', 'interface', 'formatters_select_array', 'formatter_current'));
	}

	public function postBlockNew()
	{
		$block = $this->block->newInstance();
		return $this->blockhelper->saveblock($block, 'new');
	}

	public function getBlockEdit($id)
	{
		$mode = 'edit';
		$block = $this->block->find($id); // find block
		if (!$block) throw new \VesselBackNotFoundException; // throw error if not found

		$this->view->share('title', 'Edit Block '.$block->title);

		$formatter = $this->fm->tryEach(
			$this->input->get('formatter'),
			$this->input->old('formatter'),
			$block->formatter
			);

		$interface = $formatter->fmInterface($block->raw, $block->made);

		$formatter->fmSetup();

		$formatters_select_array = $this->fm->filterForSelect('block');
		$formatter_current = get_class($formatter);

		return $this->view->make('vessel::block')->with(compact('block', 'mode', 'interface', 'formatters_select_array', 'formatter_current'));
	}

	public function postBlockEdit($id)
	{
		$block = $this->block->findOrFail($id);
		$is_draft = $this->input->has('save_as_draft');
		return $this->blockhelper->saveBlock($block, 'edit', $is_draft);
	}

	public function getBlockDelete($id)
	{
		$block = $this->block->find($id);

		if ($block)
		{
			$block->delete();
			$this->notification->success(t('messages.general.delete-success', array('name' => 'Block')));
			return $this->redirect->route('vessel.blocks');
		}

		throw new \VesselBackNotFoundException;
	}
}