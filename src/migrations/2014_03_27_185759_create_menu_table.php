<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vessel_menu', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('content');
			$table->integer('order');
			$table->integer('parent_id')->nullable();
			$table->boolean('is_seperator')->default(false);
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vessel_menu');
	}

}
