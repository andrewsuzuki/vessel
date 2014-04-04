<?php namespace Hokeo\Vessel;

use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Auth\AuthManager;
use Illuminate\Validation\Factory;
use Krucas\Notification\Notification;

class BlockHelper {

	protected $input;

	protected $redirect;

	protected $auth;

	protected $validator;

	protected $notification;

	protected $fm;

	protected $block;

	public function __construct(
		Request $input,
		Redirector $redirect,
		AuthManager $auth,
		Factory $validator,
		Notification $notification,
		FormatterManager $fm,
		Block $block)
	{
		$this->input        = $input;
		$this->redirect     = $redirect;
		$this->auth         = $auth;
		$this->validator    = $validator;
		$this->notification = $notification;
		$this->fm           = $fm;
		$this->block        = $block;
	}

	/**
	 * Renders block for display
	 *
	 * @param  string      Block slug
	 * @return string|void Block content if block exists and is active, formatter exists, or void if not
	 */
	public function display($slug)
	{
		$block = $this->block->where('slug', $slug)->first();

		if ($block && $block->active)
		{
			try
			{
				$formatter = $this->fm->get($block->formatter);
			}
			catch (\Exception $e)
			{
				// do nothing
				return;
			}

			return $formatter->fmUse($block->raw, $block->made);
		}
	}
	
	/**
	 * Saves block given input
	 * 
	 * @param  object  $block  Block object (can be new)
	 * @param  string  $mode   edit|new
	 * @return object          Redirect response
	 */
	public function saveBlock($block, $mode = 'edit')
	{
		if ($mode == 'edit')
		{
			// if saving an edit, give a warning if it's been edited elsewhere since
			if ($this->input->get('updated_at') != (string) $block->updated_at && !$this->input->get('force_edit'))
			{
				$this->notification->warning('This block has been updated elsewhere since you started editing. Click save again to force this edit.');
				return $this->redirect->back()->with('force_edit', 'true')->withInput();
			}

			$rules = $this->block->rules($block); // validation rules for editing
		}
		else
		{
			$rules = $this->block->rules(); // validation rules for new
		}

		// validate input

		$validator = $this->validator->make($this->input->all(), $rules);

		if ($validator->fails())
		{
			// redirect back with error and input
			$this->notification->error($validator->messages()->first());
			return $this->redirect->back()->withInput();
		}

		// process content
		
		// get formatter
		try
		{
			$formatter = $this->fm->get($this->input->get('formatter'), 'block');
		}
		catch (\Exception $e)
		{
			// redirect back with error and input
			$this->notification->error($e->getMessage());
			return $this->redirect->back()->withInput();
		}

		// process
		$processed = $formatter->fmProcess();

		// verify processing returns array with two elements
		if (!is_array($processed) || count($processed) !== 2)
		{
			// error with processing, redirect back
			return $this->redirect->back()->withInput();
		}

		// set fields
		$block->title       = $this->input->get('title');
		$block->slug        = $this->input->get('slug');
		$block->description = $this->input->get('description');
		$block->formatter   = $this->input->get('formatter');
		$block->active      = (bool) $this->input->get('active');

		$block->raw         = $processed[0];
		$block->made        = $processed[1];

		// associate saver (user)
		$block->user()->associate($this->auth->user());

		$block->save();

		// determine success message
		if ($mode == 'edit')
			$this->notification->success('Block was edited successfully.');
		else
			$this->notification->success('Block was created successfully.');

		// redirect to edit page
		return $this->redirect->route('vessel.blocks.edit', array('id' => $block->id));
	}
}