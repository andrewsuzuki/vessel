<?php namespace Hokeo\Vessel;

trait VesselTestTrait {

	public function createApplication()
    {
        $unitTesting = true;
        $testEnvironment = 'testing';
        return require __DIR__.'/../../../../bootstrap/start.php';
    }

    // Helpers

    protected function authenticate($username = 'andrew')
    {
    	$user = User::where('username', $username)->first();

		$this->be($user);
    }
}