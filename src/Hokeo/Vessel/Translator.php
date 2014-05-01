<?php namespace Hokeo\Vessel;

use Illuminate\Translation\Translator as ITranslator;
use Illuminate\Translation\LoaderInterface;

class Translator extends ITranslator {

	protected $vessel_lang_namespace = 'vessel';

	/**
	 * Create a new translator instance.
	 *
	 * @param  \Illuminate\Translation\LoaderInterface  $loader
	 * @param  string  $locale
	 * @return void
	 */
	public function __construct(LoaderInterface $loader, $locale)
	{
		$this->loader = $loader;
		$this->locale = $locale;
	}

	/**
	 * Determine if a translation exists.
	 *
	 * @param  string  $key
	 * @param  string  $locale
	 * @return bool
	 */
	public function has($key, $locale = null)
	{
		return parent::has($this->toVesselKey($key), $locale);
	}

	/**
	 * Parse a key into namespace, group, and item.
	 *
	 * @param  string  $key
	 * @return array
	 */
	public function parseKey($key)
	{
		return parent::parseKey($this->toVesselKey($key));
	}

	/**
	 * Prepend the vessel namespace to a non-namespaced key
	 *
	 * @param  string $key
	 * @return string
	 */
	public function toVesselKey($key)
	{
		return (strpos($key, '::') === false) ? $this->vessel_lang_namespace.'::'.$key : $key;
	}
}