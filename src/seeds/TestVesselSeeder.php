<?php namespace Hokeo\Vessel\Seeds;

use Illuminate\Database\Seeder;

class TestVesselSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		\Eloquent::unguard();

		$seeds = array(
			'Permissions',
			'Users',
			'Pages',
		);

		foreach ($seeds as $seed)
		{
			$this->callTestSeeder($seed);
		}

		// $seeds = scandir(__DIR__);

		// $blacklist = array('TestVesselSeeder');

		// foreach($seeds as $seed)
		// {
		// 	if (substr($seed, 0, 4) == 'Test' && substr($seed, -10) == 'Seeder.php' && !in_array(($base = basename($seed, '.php')), $blacklist))
		// 	{
		// 		$this->call('Hokeo\\Vessel\\Seeds\\'.$base);
		// 	}
		// }
	}

	/**
	 * Calls test seeder under Hokeo\Vessel\Seeds
	 * 
	 * @param string $name Name of seeder; file/class must be of the form 'Test'.$name.'Seeder'
	 */
	protected function callTestSeeder($name)
	{
		if ($name !== 'Vessel')
		{
			$this->call('Hokeo\\Vessel\\Seeds\\Test'.$name.'Seeder');
		}
	}

}