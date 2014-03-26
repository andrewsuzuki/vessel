<?php

class VesselSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$seeds = scandir(__DIR__);

		$blacklist = array('VesselSeeder');

		print "Seeding Vessel...\n";

		foreach($seeds as $seed)
		{
			if (substr($seed, -10) == 'Seeder.php' && !in_array(($base = basename($seed, '.php')), $blacklist))
			{
				$this->call($base);
			}
		}

		print "Vessel Seeded.\n";
	}

}