<?php namespace Hokeo\Vessel;

class BlockHelper {
	
	/**
	 * Makes block content
	 * 
	 * @param  array  $input      Array of input (usually including 'content')
	 * @param  string $formatter  Name of formatter
	 * @return string|boolean     Formatted content, or false if formatter dne
	 */
	public function makeContent($input, $formatter)
	{
		if ($this->formatter->exists($formatter) && $this->formatter->isForBlocks($formatter))
		{
			$this->formatter->set($formatter);
			$formatter = $this->formatter->formatter();
			$content = (isset($input['content'])) ? $input['content'] : null;
			return $formatter->render($content, $input);
		}

		return false;
	}
}