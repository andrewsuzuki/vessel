<?php namespace Hokeo\Vessel\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestUsersSeeder extends Seeder {

	public function run()
	{
		DB::table('vessel_users')->delete();
		
		DB::table('vessel_users')->insert(
			array(
				array(
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
					),
				));
	}

}
