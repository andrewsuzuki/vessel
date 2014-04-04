<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBlocksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vessel_blocks', function($table)
		{
			$table->increments('id');
			
			$table->string('slug');
			$table->string('title');
			$table->text('description');

			$table->integer('user_id'); // last updated user
			
			$table->boolean('active')->default(true);

			$table->string('formatter')->nullable();

			$table->text('raw');
			$table->text('made');

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
		Schema::drop('vessel_blocks');
	}

}
