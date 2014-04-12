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
			
			// pages_manage   = Manage pages
			// 	pages_view    = View pages
			// 	pages_create  = Create page
			// 	pages_edit    = Edit page (++ whitelist/or/blacklist for specific pages)
			// 	pages_delete  = Delete page
			// blocks_manage  = Manage blocks
			// 	blocks_view   = View blocks
			// 	blocks_create = Create block
			// 	blocks_edit   = Edit block
			// 	blocks_delete = Delete block
			// users_manage   = Manage users and permissions
			// 	users_view    = View users
			// 	users_create  = Create User
			// 	users_edit    = Edit user
			// 	users_delete  = Delete user
			// media_manage   = Upload, delete, rename, etc media
			// 	media_upload  = Upload media (images/files)
			// settings_edit  = Edit site settings
			
			// [
			// 	'values' => ['name' => '', 'display_name' => ''],
			// 	'roles'  => [] // x_y inherited from x_manage
			// ],
			
			[
				'values' => ['name' => 'pages_manage', 'display_name' => 'Manage pages'],
				'roles'  => ['Admin', 'Client']
			],

			[
				'values' => ['name' => 'pages_view', 'display_name' => 'View pages'],
				'roles'  => []
			],

			[
				'values' => ['name' => 'pages_create', 'display_name' => 'Create pages'],
				'roles'  => []
			],

			[
				'values' => ['name' => 'pages_edit', 'display_name' => 'Edit pages'],
				'roles'  => []
			],

			[
				'values' => ['name' => 'pages_delete', 'display_name' => 'Delete pages'],
				'roles'  => []
			],

			[
				'values' => ['name' => 'blocks_manage', 'display_name' => 'Manage blocks'],
				'roles'  => ['Admin']
			],

			[
				'values' => ['name' => 'blocks_view', 'display_name' => 'View blocks'],
				'roles'  => []
			],

			[
				'values' => ['name' => 'blocks_create', 'display_name' => 'Create blocks'],
				'roles'  => []
			],

			[
				'values' => ['name' => 'blocks_edit', 'display_name' => 'Edit blocks'],
				'roles'  => []
			],

			[
				'values' => ['name' => 'blocks_delete', 'display_name' => 'Delete blocks'],
				'roles'  => []
			],

			[
				'values' => ['name' => 'users_manage', 'display_name' => 'Manage users'],
				'roles'  => ['Admin']
			],

			[
				'values' => ['name' => 'users_view', 'display_name' => 'View users'],
				'roles'  => ['Client']
			],

			[
				'values' => ['name' => 'users_create', 'display_name' => 'Create users'],
				'roles'  => []
			],

			[
				'values' => ['name' => 'users_edit', 'display_name' => 'Edit users'],
				'roles'  => []
			],

			[
				'values' => ['name' => 'users_delete', 'display_name' => 'Delete users'],
				'roles'  => []
			],

			[
				'values' => ['name' => 'media_manage', 'display_name' => 'Manage media'],
				'roles'  => ['Admin', 'Client']
			],

			[
				'values' => ['name' => 'media_upload', 'display_name' => 'Upload media'],
				'roles'  => []
			],

			[
				'values' => ['name' => 'settings_edit', 'display_name' => 'Edit site settings'],
				'roles'  => ['Admin']
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
