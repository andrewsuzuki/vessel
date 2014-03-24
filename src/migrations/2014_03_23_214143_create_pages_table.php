<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vessel_pages', function($table)
		{
			$table->increments('id');
			
			$table->string('slug');
			$table->string('title');
			$table->text('description');

			$table->integer('user_id'); // last updated user
			

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
		Schema::drop('vessel_pages');
	}

}
