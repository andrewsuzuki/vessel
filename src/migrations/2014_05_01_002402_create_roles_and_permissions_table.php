<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRolesAndPermissionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vessel_roles', function($table)
		{
			$table->increments('id')->unsigned();
			$table->string('name')->unique();
			$table->timestamps();
		});

		Schema::create('vessel_role_user', function($table)
		{
			$table->increments('id')->unsigned();
			$table->integer('user_id')->unsigned();
			$table->integer('role_id')->unsigned();
		});

		Schema::create('vessel_permissions', function($table)
		{
			$table->increments('id')->unsigned();
			$table->string('name');
			$table->string('display_name');
			$table->timestamps();
		});

		Schema::create('vessel_permission_role', function($table)
		{
			$table->increments('id')->unsigned();
			$table->integer('permission_id')->unsigned();
			$table->integer('role_id')->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vessel_role_user');
		Schema::drop('vessel_permission_role');
		Schema::drop('vessel_roles');
		Schema::drop('vessel_permissions');
	}

}
