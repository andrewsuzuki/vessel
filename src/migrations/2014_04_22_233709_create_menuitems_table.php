<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuitemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vessel_menuitems', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->integer('menu_id')->unsigned()->nullable();
			$table->integer('page_id')->unsigned()->nullable();
			$table->string('link_if')->nullable();

			// start baum
			$table->integer('parent_id')->nullable();
			$table->integer('lft')->nullable();
			$table->integer('rgt')->nullable();
			$table->integer('depth')->nullable();
			$table->index('parent_id');
			$table->index('lft');
			$table->index('rgt');
			// end baum
			
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
		Schema::drop('vessel_menuitems');
	}

}
