<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagehistoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('vessel_pagehistories', function($table)
		{
			$table->increments('id');

			$table->integer('page_id')->unsigned();
			$table->integer('edit')->nullable();

			$table->boolean('is_draft');
			
			$table->string('slug');
			$table->string('title');
			$table->text('description');

			$table->integer('user_id');
			
			$table->integer('parent_id')->nullable();
			$table->index('parent_id');

			$table->boolean('nest_url')->default(true);
			$table->boolean('visible')->default(true);

			$table->string('formatter')->nullable();
			$table->string('template')->nullable();

			$table->text('raw')->nullable();
			$table->text('made')->nullable();

			$table->timestamp('created_at');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('vessel_pagehistories');
	}

}
