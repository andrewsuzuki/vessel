<?php namespace Hokeo\Vessel\Seeds;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestPermissionsSeeder extends Seeder {

	public function run()
	{
		// clear tables
		
		DB::table('roles')->delete();
		DB::table('permissions')->delete();
		DB::table('permission_role')->delete();
		DB::table('assigned_roles')->delete();

		// create roles
		
		$roles = array(
			// [
			// 	'name' => '',
			// ],
			
			[
				'name' => 'Admin',
			],
			[
				'name' => 'Client',
			],
			[
				'name' => 'User',
			],

		);

		$roles_created = array();

		foreach ($roles as $role)
		{
			$roles_created[$role['name']] = ['role' => \Hokeo\Vessel\Role::create(['name' => $role['name']]), 'permissions_to_sync' => array()];
		}

		// create permissions and sync with roles

		$permissions = array(
			// [
			// 	'values' => [
			// 		'name' => '',
			// 		'display_name' => '',
			// 	],
			// 	'roles' => [
			// 		'',
			// 	]
			// ],
			
			[
				'values' => [
					'name' => 'create_pages',
					'display_name' => 'Create Pages',
				],
				'roles' => [
					'Admin',
					'Client',
				]
			],

			[
				'values' => [
					'name' => 'manage_users',
					'display_name' => 'Manage Users',
				],
				'roles' => [
					'Admin',
				]
			],
		);

		$permissions_created = array();

		foreach ($permissions as $permission)
		{
			if (!isset($permission['values']) || !isset($permission['roles'])) break;

			$permissions_created[$permission['values']['name']] = \Hokeo\Vessel\Permission::create($permission['values']);

			foreach ($permission['roles'] as $role)
			{
				if (isset($roles_created[$role]))
				{
					$roles_created[$role]['permissions_to_sync'][] = $permissions_created[$permission['values']['name']]->id;
				}
			}
		}
		
		foreach ($roles_created as $role)
		{
			$role['role']->perms()->sync($role['permissions_to_sync']);
		}
	}

}
