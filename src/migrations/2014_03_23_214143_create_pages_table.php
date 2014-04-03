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
			
			$table->integer('edit')->nullable();

			$table->string('slug');
			$table->string('title');
			$table->text('description');

			$table->integer('user_id'); // last updated user
			
			// start baum
			$table->integer('parent_id')->nullable();
			$table->integer('lft')->nullable();
			$table->integer('rgt')->nullable();
			$table->integer('depth')->nullable();
			$table->index('parent_id');
			$table->index('lft');
			$table->index('rgt');
			// end baum
			
			$table->boolean('nest_url')->default(true);
			$table->boolean('visible')->default(true);
			$table->boolean('in_menu')->default(true);

			$table->string('formatter')->nullable();
			$table->string('template')->nullable();

			$table->text('raw')->nullable();
			$table->text('made')->nullable();

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
