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

		$seeds = scandir(__DIR__);

		$blacklist = array('TestVesselSeeder');

		foreach($seeds as $seed)
		{
			if (substr($seed, 0, 4) == 'Test' && substr($seed, -10) == 'Seeder.php' && !in_array(($base = basename($seed, '.php')), $blacklist))
			{
				$this->call('Hokeo\\Vessel\\Seeds\\'.$base);
			}
		}
	}

}