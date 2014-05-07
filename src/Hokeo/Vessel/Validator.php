<?php namespace Hokeo\Vessel;

use Illuminate\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Translation\TranslatorInterface;

class Validator extends \Illuminate\Validation\Validator {

	protected $config;

	protected $filesystem;

	protected $fm;

	protected $mm;

	protected $pm;

	protected $theme;

	protected $page; // model

	protected $role; // model

	protected $permission; // model

	public function __construct(
		Repository $config,
		Filesystem $filesystem,
		FormatterManager $fm,
		MenuManager $mm,
		PluginManager $pm,
		Theme $theme,
		Page $page,
		Role $role,
		Permission $permission,
		TranslatorInterface $translator,
		array $data,
		array $rules,
		array $messages)
	{
		$this->config     = $config;
		$this->filesystem = $filesystem;
		$this->fm         = $fm;
		$this->mm         = $mm;
		$this->pm         = $pm;
		$this->theme      = $theme;
		$this->page       = $page;
		$this->role       = $role;
		$this->permission = $permission;

		parent::__construct($translator, $data, $rules, $messages);
	}

	/**
	 * Get the validation message for an attribute and rule under the vessel lang namespace.
	 *
	 * @param  string  $attribute
	 * @param  string  $rule
	 * @return string
	 */
	protected function getMessage($attribute, $rule)
	{
		$lowerRule = snake_case($rule);

		$inlineMessage = $this->getInlineMessage($attribute, $lowerRule);
		if (!is_null($inlineMessage)) return $inlineMessage;

		// append type if it's a size rule
		if (in_array($rule, $this->sizeRules)) $lowerRule = $lowerRule.'.'.$this->getAttributeType($attribute);

		// check vessel namespace, then app lang
		$check = array(
			'vessel::validation.custom.'.$attribute.'.'.$lowerRule,
			'vessel::validation'.'.'.$lowerRule,
			'validation.custom.'.$attribute.'.'.$lowerRule,
			'validation'.'.'.$lowerRule,
		);

		foreach ($check as $key)
		{
			$message = $this->translator->trans($key);
			if ($message !== $key) return $message;
		}

		return $this->getInlineMessage(
			$attribute, $lowerRule, $this->fallbackMessages
		) ?: $key;
	}

	// Checks if page parent is valid
	public function validatePageParent($attribute, $value, $parameters)
	{
		if (isset($parameters[1]) && $parameters[1] == 'true') // check if this is the home page
		{
			$valid = $value == 'none';
		}
		else
		{
			// make sure it's a page id
			$valid = $value == 'none' || ($page = $this->page->find($value));

			// make sure page id is not self or descendant of this page
			if ($valid && $value != 'none' && isset($parameters[0]))
				$valid = !$page->isSelfOrDescendantOf($this->page->find($parameters[0]));
		}

		return $valid;
	}

	// Checks if formatter exists
	public function validateFormatter($attribute, $value, $parameters)
	{
		return $this->fm->registered($value);
	}

	// Checks if theme template exists
	public function validateTemplate($attribute, $value, $parameters)
	{
		if ($value == 'none') return true;
		
		// TODO: validate template withload having to load theme
		$this->theme->load();
		$templates = $this->theme->getThemeSubs();
		return array_key_exists($value, $templates);
	}

	// Checks if page id exists, is public, and is root
	public function validateHomePageId($attribute, $value, $parameters)
	{
		$page = $this->page->find($value);
		return $page && $page->visible && $page->isRoot();
	}

	// Checks if all plugins in array are available
	public function validatePlugins($attribute, $value, $parameters)
	{	
		if (!is_array($value) || empty($value)) return true;

		$available = $this->pm->getAvailable();
		foreach ($value as $dir)
			if (!in_array($dir, $available)) return false;
		return true;
	}

	// Checks if theme exists
	public function validateTheme($attribute, $value, $parameters)
	{	
		$themes = $this->theme->getAvailable();
		return array_key_exists($value, $themes);
	}

	// Checks if timezone name is valid
	public function validateTimezone($attribute, $value, $parameters)
	{
		return in_array($value, \DateTimeZone::listIdentifiers());
	}

	// Checks if string a registered menu mapper name
	public function validateMenuMapper($attribute, $value, $parameters)
	{
		$mappers = $this->mm->getRegisteredMappers();
		return isset($mappers[$value]);
	}

	// Checks if id is a role
	public function validateRole($attribute, $value, $parameters)
	{
		return ($this->role->find($value)) ? true : false;
	}

	// Checks if array of ids are all existing roles
	public function validateRoles($attribute, $value, $parameters)
	{
		if (!is_array($value) || empty($value)) return false; // false for non-array or empty array
		foreach ($value as $role)
			if (!$this->role->find($role)) return false; // false if one of the roles doesn't exist
		return true;
	}

	// Checks if array of ids are all existing permissions
	public function validatePermissions($attribute, $value, $parameters)
	{
		if (!is_array($value)) return false; // false for non-array
		foreach ($value as $permission)
			if (!$this->permission->find($permission)) return false; // false if one of the permissions doesn't exist
		return true;
	}

	// Checks for valid username
	public function validateUsername($attribute, $value, $parameters)
	{
		return ctype_alnum(str_replace(array('-', '_'), '', $value)); // check if value is only alphanumeric/hyphen/underscore
	}

	// Checks if checkbox was checked (equal to '1', 'on', 'true', 'yes')
	public function validateChecked($attribute, $value, $parameters)
	{
		return in_array(strtolower($value), array('1', 'on', 'true', 'yes'));
	}

	// Checks if file exists in config upload_path
	public function validateUploaded($attribute, $value, $parameters)
	{
		$upload_path = rtrim($this->config->get('vessel::upload_path', 'public/uploads'), '/');
		$upload_path = ($upload_path[0] == '/') ? $upload_path : base_path($upload_path);

		return $this->file->exists($upload_path.'/'.$value);
	}

	// Checks if string is valid json, and is an array
	public function validateJsonStringArray($attribute, $value, $parameters)
	{
		return is_array(json_decode($value, true));
	}
}
