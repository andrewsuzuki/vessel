<?php namespace Hokeo\Vessel;

trait VesselTestTrait {

	public function createApplication()
    {
        $unitTesting = true;
        $testEnvironment = 'testing';
        return require __DIR__.'/../../../../bootstrap/start.php';
    }

    // Helpers

    protected function authenticate(array $fields = array())
    {
    	// $user = User::where('username', $username)->first();
        
        $default = array(
            'username'  => 'andrew',
            'email'     => 'andrew.b.suzuki@gmail.com',
            'first_name'=> 'Andrew',
            'last_name' => 'Suzuki',
            'password'  => \Hash::make('tester'),
            'confirmed' => true,
            'last_login'=> \Carbon\Carbon::now(),
            'created_at'=> \Carbon\Carbon::now(),
            'updated_at'=> \Carbon\Carbon::now(),
            'preferred_formatter' => 'Markdown',
        );

        $fields = array_merge($default, $fields);

        $user = new User;
        foreach ($fields as $key => $value)
        {
            $user[$key] = $value;
        }
        
		$this->be($user);
    }
}