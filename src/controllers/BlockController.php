<?php namespace Hokeo\Vessel;

use Illuminate\Routing\Controller;
use Illuminate\View\Environment;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Krucas\Notification\Notification;

class BlockController extends Controller
{
	protected $view;

	protected $input;

	protected $redirect;

	protected $blockhelper;

	protected $formatter;

	protected $theme;

	protected $notification;

	protected $block; // model

	public function __construct(
		Environment $view,
		Request $input,
		Redirector $redirect,
		BlockHelper $blockhelper,
		Formatter $formatter,
		Theme $theme,
		Notification $notification,
		Block $block)
	{
		$this->view         = $view;
		$this->input        = $input;
		$this->redirect     = $redirect;
		$this->blockhelper  = $blockhelper;
		$this->formatter    = $formatter;
		$this->theme        = $theme;
		$this->notification = $notification;
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

		$this->blockhelper->setBlockFormatter($block);
		$editor = $this->formatter->formatter()->getEditorHtml();

		$this->theme->load();
		$sub_templates = $this->theme->getThemeViewsSelect();

		return $this->view->make('vessel::block')->with(compact('block', 'mode', 'editor', 'sub_templates'));
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
		if (!$block) throw new \VesselNotFoundException; // throw error if not found

		$this->view->share('title', 'Edit '.$block->title); // set view title
		$this->blockhelper->setblockFormatter($block); // set formatter according to block setting (editor)

		$content = $this->blockhelper->getContent($block->id, true);

		$editor  = $this->formatter->formatter()->getEditorHtml($content); // get editor html

		$this->theme->load();
		$sub_templates = $this->theme->getThemeViewsSelect();

		return $this->view->make('vessel::block')->with(compact('block', 'edits', 'drafts', 'mode', 'editor', 'sub_templates'));
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
			$this->notification->success('Block was deleted successfully.');
			return $this->redirect->route('vessel.blocks');
		}

		throw new \VesselNotFoundException;
	}
}