<?php

class ConfigureTestVesselBuild {

	protected $base;

	public function __construct($base)
	{
		$this->base = $base;
	}

	protected function fail($message)
	{
		die('ConfigureTestVesselBuild failed: '.$message);
	}

	protected function getReplacePut($file, array $fr = array())
	{
		$file = $this->base.DIRECTORY_SEPARATOR.$file;

		$get = @file_get_contents($file);

		if ($get)
		{
			$replaced = $get;

			foreach ($fr as $find => $replace)
			{
				$replaced = str_replace($find, $replace, $replaced);
			}

			if (@file_put_contents($file, $replaced) !== false)
			{
				return true;
			}

			$this->fail('Get-replace-put of '.$file.' failed (PUT)');
		}

		$this->fail('Get-replace-put of '.$file.' failed (GET)');
	}

	public function run()
	{
		
	}

	// public function configApp()
	// {
		// $this->getReplacePut('app/config/app.php', array("'url' => 'http://localhost'" => "'url' => 'http://localhost'"));
	// }
}

$configureTestVesselBuild = new ConfigureTestVesselBuild(__DIR__.'/../../../..');
$configureTestVesselBuild->run();