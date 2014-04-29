<?php namespace Hokeo\Vessel\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestUsersSeeder extends Seeder {

	public function run()
	{
		DB::table('vessel_users')->delete();

		$users = array(
			[
				'values' => [
					'username'  => 'andrew',
					'email'     => 'andrew.b.suzuki@gmail.com',
					'first_name'=> 'Andrew',
					'last_name' => 'Suzuki',
					'password'  => \Hash::make('tester'),
					'confirmed' => true,
					'confirmation' => \Str::random(),
					'last_login'=> \Carbon\Carbon::now()->subHour(),
					'created_at'=> \Carbon\Carbon::parse('5 days ago'),
					'updated_at'=> \Carbon\Carbon::parse('1 day ago'),
				],
				'role' => 'Admin',
			],
			[
				'values' => [
					'username'  => 'john',
					'email'     => 'test@test.com',
					'first_name'=> 'John',
					'last_name' => 'Brown',
					'password'  => \Hash::make('tester'),
					'confirmed' => true,
					'confirmation' => \Str::random(),
					'last_login'=> \Carbon\Carbon::now()->subHours(5),
					'created_at'=> \Carbon\Carbon::parse('3 days ago'),
					'updated_at'=> \Carbon\Carbon::parse('2 days ago'),
				],
				'role' => 'Client'
			],
			[
				'values' => [
					'username'  => 'walter',
					'email'     => 'walter@greymatter.com',
					'first_name'=> 'Walter',
					'last_name' => 'White',
					'password'  => \Hash::make('heisenberg'),
					'confirmed' => true,
					'confirmation' => \Str::random(),
					'last_login'=> \Carbon\Carbon::now()->subMonths(4),
					'created_at'=> \Carbon\Carbon::now()->subMonths(6),
					'updated_at'=> \Carbon\Carbon::now()->subMonths(5),
				],
				'role' => 'User'
			]

		);

		foreach ($users as $user)
		{
			$created_user = \Hokeo\Vessel\User::create($user['values']);

			if ($created_user)
			{
				if ($user['role'] && $role = \Hokeo\Vessel\Role::where('name', $user['role'])->first())
				{
					$created_user->attachRole($role);
				}
			}
		}
	}

}
